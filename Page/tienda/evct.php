<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/tienda/queryRpro.php";



$tienda = (isset($_POST['tienda'])) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];
$sim = impuestoSimbolo($sbs);

$iva = (isset($_POST['iva'])) ? $_POST['iva'] : '';
$vacacionista = (isset($_POST['vacacionista'])) ? $_POST['vacacionista'] : '';
$filtro = '';

if ($vacacionista == '1') {
  $filtro = '';
} else {
  $filtro = " AND EMP.EMPL_NAME < '5000'";
}
$semanas = rangoWe($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);
?>
<div class="container-fluid shadow rounded py-3 px-4">
  <?php
  foreach ($tiendas as $tienda) {

       $total = array(
        $existencia = 0,
        $valor_existencia = 0,
        $venta = 0,
        $valor_venta = 0,
        $compras = 0,
        $valor_compras = 0
       
      );

      $query = "
                          SELECT 
                        COALESCE(Q1.vend_code, Q2.vend_code, Q3.vend_code) AS COD,
                        Q1.vend_name PROVEEDOR,
                        
                                                    -- Inventario
                            NVL( Q1.CANT,0) AS EXISTENCIAS,
                            NVL( Q1.COSTO,0) AS COSTO_EXISTENCIAS,
                              
                              -- Ventas
                            NVL( Q2.CANTIDAD,0) AS cant_ventas,
                            NVL( Q2.VENTA_SIN_IVA,0) VENTA_SIN_IVA,
                              
                              -- Compras
                            NVL( Q3.CANTIDAD,0) AS cant_compras,
                            NVL( Q3.COSTO,0) AS costo_compras

                    FROM
                    (
                        -- Primera consulta: inventario
                        SELECT 
                            t.vend_code,
                            t.vend_name,
                            SUM(t.qty) AS CANT,
                            SUM(t.costo) AS COSTO
                        FROM (
                            SELECT 
                                st.store_code,
                                st.store_name, 
                                vd.vend_code,
                                vd.vend_name,
                                i.SID,
                                i.description1,
                                i.attribute,
                                i.item_size,
                                SUM(iq.qty) AS qty,
                                ROUND(SUM(iq.qty * ip.price), 2) AS precio,
                                ROUND(SUM(i.cost), 2) AS costo
                            FROM rps.invn_sbs_item i
                            INNER JOIN rps.invn_sbs_item_qty iq ON i.sbs_sid = iq.sbs_sid AND i.sid = iq.invn_sbs_item_sid
                            INNER JOIN rps.store st ON iq.store_sid = st.sid
                            INNER JOIN rps.vendor vd ON i.vend_sid = vd.sid AND i.sbs_sid = vd.sbs_sid
                            INNER JOIN rps.dcs d ON i.sbs_sid = d.sbs_sid AND i.dcs_sid = d.sid
                            INNER JOIN rps.subsidiary s ON i.sbs_sid = s.sid AND vd.sbs_sid = s.sid
                            LEFT JOIN (
                                SELECT s.sid AS sbs_sid, invn_sbs_item_sid, ip.price 
                                FROM rps.subsidiary s
                                JOIN rps.invn_sbs_price ip ON s.sid = ip.sbs_sid AND s.active_price_lvl_sid = ip.price_lvl_sid
                            ) ip ON iq.sbs_sid = ip.sbs_sid AND iq.invn_sbs_item_sid = ip.invn_sbs_item_sid
                            WHERE 
                                s.sbs_no = $sbs
                                AND st.store_no IN ($tienda)
                                AND i.created_datetime <= TO_DATE('$ff', 'YYYY-MM-DD')
                                AND iq.qty <> 0
                            GROUP BY st.store_code, st.store_name, i.SID, i.description1, i.attribute, i.item_size, vd.vend_code, vd.vend_name
                        ) t
                        GROUP BY t.vend_code, t.vend_name
                    ) Q1

                    FULL OUTER JOIN (
                        -- Segunda consulta: ventas
                        SELECT 
                            V.vend_code, 
                            SUM(V.cantidad) AS cantidad,
                            ROUND(SUM(V.venta_sin_iva), 2) AS venta_sin_iva
                        FROM (
                            SELECT 
                                vd.vend_code, 
                                SUM(CASE WHEN z.receipt_type = 0 THEN b.qty WHEN z.receipt_type = 1 THEN b.qty * -1 END) AS cantidad,
                                SUM(CASE 
                                    WHEN z.receipt_type = 0 THEN ((b.price - (b.price * NVL(z.disc_perc, 0) / 100)) * b.qty) / 1.12 
                                    WHEN z.receipt_type = 1 THEN ((b.price - (b.price * NVL(z.disc_perc, 0) / 100)) * b.qty) / 1.12 * -1 
                                END) AS venta_sin_iva
                            FROM rps.document z
                            JOIN rps.document_item b ON z.sid = b.doc_sid
                            JOIN rps.invn_sbs_item i ON i.sid = b.invn_sbs_item_sid
                            JOIN rps.store st ON z.store_no = st.store_no
                            JOIN rps.vendor vd ON b.vend_code = vd.vend_code AND i.sbs_sid = vd.sbs_sid
                            JOIN rps.dcs d ON i.sbs_sid = d.sbs_sid AND i.dcs_sid = d.sid
                            WHERE 
                                z.sbs_no = $sbs
                                AND z.store_no IN ($tienda)
                                AND z.created_datetime BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                                            AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
                            GROUP BY vd.vend_code
                        ) V
                        GROUP BY V.vend_code
                    ) Q2 ON Q1.vend_code = Q2.vend_code

                    FULL OUTER JOIN (
                        -- Tercera consulta: compras
                        SELECT 
                            C.vend_code,
                            SUM(C.qty) AS cantidad,
                            SUM(C.cost) AS costo
                        FROM (
                            SELECT 
                                v.vend_code,
                                (CASE WHEN a.vou_type = 1 THEN b.qty * -1 ELSE b.qty END) AS qty,
                                ROUND(b.cost, 2) AS cost
                            FROM rps.voucher a
                            JOIN rps.vou_item b ON a.sid = b.vou_sid
                            JOIN rps.vou_comment vc ON a.sid = vc.vou_sid
                            JOIN rps.subsidiary s ON a.sbs_sid = s.sid
                            JOIN rps.store st ON st.sid = a.store_sid
                            LEFT JOIN rps.vendor_invoice vi ON vi.sid = a.vendor_invoice_sid
                            JOIN rps.invn_sbs_item i ON i.sid = b.item_sid
                            JOIN rps.vendor v ON v.sid = i.vend_sid AND a.sbs_sid = v.sbs_sid
                            JOIN rps.dcs d ON d.sid = i.dcs_sid
                            WHERE 
                                a.vou_type IN (0)
                                AND a.vou_class = 0
                                AND a.slip_flag = 0
                                AND vc.comments LIKE '%COMPRA%'
                                AND a.held = 0
                                AND s.sbs_no = $sbs
                                AND st.store_no IN ($tienda)
                                AND a.created_datetime BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                                          AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
                        ) C
                        GROUP BY C.vend_code
                    ) Q3 ON COALESCE(Q1.vend_code, Q2.vend_code) = Q3.vend_code
                    ORDER BY 1";
      $resultado = consultaOracle(3, $query);
      $cnt = 1;

  ?>
                     <tr><td colspan="8"><h3 align="center"><b><p >Existencias - Ventas - Compras</p></b></h3></td></tr>
                     <tr><td colspan="8"><h3 align="center"><b><p ><?php echo 'DEL: '.date('d-m-Y', strtotime($fi)) .' AL: '.date('d-m-Y', strtotime($ff)) ;  ?> </p></b></h3> </td></tr>                 
                     <tr><td colspan='27'><h3 align="center"><b><p >TIENDA: <?php echo utf8_encode($tienda); ?></p></b></h3></td></tr>



      <table style="font-size:14px;" class="table table-hover table-sm tbevct">
        <thead class="bg-primary">
          <td >CODIGO PROVEEDOR</td>
          <td >NOMBRE PROVEEDOR</td>
          <td style="text-align: right;">EXISTENCIAS</td>
          <td style="text-align: right;">VALOR</td>
          <td style="text-align: right;">VENTAS</td>
          <td style="text-align: right;">VALOR</td>
          <td style="text-align: right;">COMPRAS</td>
          <td style="text-align: right;">VALOR</td>
          
        </thead>

        <tbody class="align-middle font-size" style="width:100%">
          <?php
          foreach ($resultado as $evct) {
          ?>
            <tr>              
            <td ><b><?php echo $evct[0] ?></b></td>
            <td ><b><?php echo $evct[1] ?></b></td>
            <td style="text-align: right;"><b><?php echo $evct[2] ?></b></td>
            <td style="text-align: right;"><b>Q. <?php echo number_format($evct[3], 2) ?></b></td>
            <td style="text-align: right;"><b><?php echo $evct[4] ?></b></td>
            <td style="text-align: right;"><b>Q. <?php echo number_format($evct[5], 2) ?></b></td>
            <td style="text-align: right;"><b><?php echo $evct[6] ?></b></td>
            <td style="text-align: right;"><b>Q. <?php echo number_format($evct[7], 2) ?></b></td>

            </tr>
          <?php

            if ($evct[2] === 'VACACIONISTA') {
              $evct[3] = 0;
            }

            $total = array(
              $existencia += $evct[2],
              $valor_existencia += $evct[3],
              $venta += $evct[4],
              $valor_venta += $evct[5],  
              $compras += $evct[6],
              $valor_compras += $evct[7]
             
            );
          }
          ?>
          <tr class="table-active align-middle font-weight-bold">
            
            <td></td>
            <td align="center"> GRAN TOTAL</td>            
            <td style="text-align: right;"><?php echo $total[0] ?></td>
            <td style="text-align: right;"><b>Q. <?php echo number_format($total[1], 2) ?></b></td>
            <td style="text-align: right;"><?php echo $total[2] ?></td>
            <td style="text-align: right;"><b>Q. <?php echo number_format($total[3], 2) ?></b></td>
            <td style="text-align: right;"><?php echo $total[4] ?></td>
            <td style="text-align: right;"><b>Q. <?php echo number_format($total[5], 2) ?></b></td>
          </tr>
                        <!--SE AGREGA PARA CALCULO DE PORCENTAJE DE META CONTRA VENTA-->
               
								 
										 
								              
                      
        </tbody>
           
        <tfoot >
               
        </tfoot>
      </table>

      <hr>
  <?php
    }
  
  ?>
</div>

<script>
  $('.tbevct').DataTable({
    "searching": false,
    "paging": false,
    "ordering": false,
    "info": false,
    "responsive": true,
    "autoWidth": false
  });

  $('.tooltip').tooltip();

  var url = "../Js/tienda/tienda.js";
  $.getScript(url);

</script>