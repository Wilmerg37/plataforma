<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";


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
  $filtro = " AND A.TIPO <> 'VACACIONISTA'";
}
$semanas = rangoWY($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);

?>
<div class="container-fluid shadow rounded py-3 px-4">
  <?php
  foreach ($tiendas as $tienda) {

    foreach ($semanas as $semana) {
      $total = array(
        $factura = 0,
        $pare_roy = 0,
        $pares_otro = 0,
        $tota_pares = 0,
        $accesorios = 0,
        $venta = 0,
        $meta = 0,
        $hora = 0
      );

      $query = "SELECT   
    V.CODIGO_EMPLEADO AS CODIGO,
    E.NOMBRE AS NOMBRE,
    E.PUESTO,
    NVL(V.META, 0) AS META,
    ROUND(NVL(SUM(A.venta_SIN_IVA), 0), 2) AS VENTA,
    NVL(ROUND(NVL(SUM(A.venta_SIN_IVA), 0) - NVL(V.META, 0), 2), 0) AS DIFERENCIA,
    NVL(SUM(A.TRANSACCIONES), 0) AS FACTURAS,
    NVL(SUM(A.PAR_ROY), 0) AS ROY,
    NVL(SUM(A.PAR_OTROS), 0) AS OTROS,
    NVL(SUM(A.PAR_ROY), 0) + NVL(SUM(A.PAR_OTROS), 0) AS PARES,
    NVL(SUM(A.PAR_ACCE), 0) AS ACCESORIOS,
    ROUND(DECODE(NVL(SUM(A.CANTIDAD), 0), 0, 0, (NVL(SUM(A.VENTA_SIN_IVA), 0) / SUM(A.CANTIDAD))), 2) AS PPP,
    ROUND(DECODE(NVL(SUM(A.TRANSACCIONES), 0), 0, 0, (NVL(SUM(A.CANTIDAD), 0) / SUM(A.TRANSACCIONES))), 2) AS UPT,
    ROUND(DECODE(NVL(SUM(A.TRANSACCIONES), 0), 0, 0, (NVL(SUM(A.VENTA_SIN_IVA), 0) / SUM(A.TRANSACCIONES))), 2) AS QPT,
    NVL(ROUND(NVL(SUM(A.venta_SIN_IVA), 0) / CASE WHEN NVL(V.HORA, 0) = 0 THEN 1 ELSE V.HORA END, 2), 0) AS VH,
    E.FECHA_INGRESO AS CONTRATACION,
    NVL(V.HORA, 0) AS HORA
FROM 
    -- Tabla base: todos los vendedores con sus metas
    ROY_META_SEM_X_VENDEDOR V
    INNER JOIN ROY_VENDEDORES_FRIED E ON (E.CODIGO_VENDEDOR = V.CODIGO_EMPLEADO)
    
    -- LEFT JOIN con las ventas para incluir vendedores sin ventas
    LEFT JOIN (
        SELECT  
            t1.store_code, 
            trunc(t1.created_datetime) FECHA, 
            t1.employee1_login_name COD_VENDEDOR,
            CASE WHEN t1.receipt_type = 0 THEN 1 WHEN t1.receipt_type = 1 THEN -1 END TRANSACCIONES, 
            
            SUM(CASE WHEN t1.receipt_type = 0 AND t2.vend_code = '001' THEN (t2.qty)
                     WHEN t1.receipt_type = 1 AND t2.vend_code = '001' THEN (t2.qty) * -1 END) AS par_roy, 
            
            SUM(CASE WHEN t1.receipt_type = 0 AND t2.vend_code <> '001' AND SUBSTR(T2.DCS_CODE, 1, 3) NOT IN ('ACC', 'SER', 'PRE', 'PRO') THEN (t2.qty)
                     WHEN t1.receipt_type = 1 AND t2.vend_code <> '001' AND SUBSTR(T2.DCS_CODE, 1, 3) NOT IN ('ACC', 'SER', 'PRE', 'PRO') THEN (t2.qty) * -1 END) AS par_otros, 
            
            SUM(CASE WHEN t1.receipt_type = 0 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN (t2.qty)
                     WHEN t1.receipt_type = 1 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN (t2.qty) * -1 END) par_acce,
                     
            SUM(CASE WHEN t1.receipt_type = 0 AND SUBSTR(T2.DCS_CODE, 1, 3) NOT IN ('SER', 'PRE', 'PRO') THEN (T2.qty) 
                     WHEN t1.receipt_type = 1 AND SUBSTR(T2.DCS_CODE, 1, 3) NOT IN ('SER', 'PRE', 'PRO') THEN (T2.qty) * -1 END) AS cantidad,           
            
            SUM(CASE WHEN t1.receipt_type = 0 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN (t2.qty * T2.PRICE)
                     WHEN t1.receipt_type = 1 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN (t2.qty * T2.PRICE) * -1 END) venta_CON_IVA_ACC,
            
            SUM(CASE WHEN t1.receipt_type = 0 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN ((T2.price) / 1.12 * (T2.qty)) 
                     WHEN t1.receipt_type = 1 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN ((T2.price) / 1.12 * (T2.qty)) * -1 END) AS venta_sin_iva_ACC,    
            
            SUM(CASE WHEN t1.receipt_type = 0 THEN (t2.qty * t2.cost) 
                     WHEN t1.receipt_type = 1 THEN (t2.qty * t2.cost) * -1 ELSE 0 END) AS costo, 
            
            SUM(CASE WHEN t1.receipt_type = 0 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN ((T2.COST) * (T2.qty))
                     WHEN t1.receipt_type = 1 AND SUBSTR(T2.DCS_CODE, 1, 3) = 'ACC' THEN ((T2.COST) * (T2.qty)) * -1 END) AS COSTO_sin_iva_ACC,
                     
            NVL(SUM(CASE WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty))
                         WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) * -1 END), 0) - SUM(NVL(t2.lty_piece_of_tbr_disc_amt, 0)) AS venta_con_iva, 
                         
            NVL(SUM(CASE WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 
                         WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 END), 0) - SUM(NVL(t2.lty_piece_of_tbr_disc_amt, 0)) / 1.12 AS venta_sin_iva                      
                         
        FROM rps.document t1 
        INNER JOIN rps.document_item t2 ON (t1.sid = t2.doc_sid)
        
        WHERE 1 = 1
            AND t1.status = 4 
            AND t1.employee1_full_name NOT IN ('SYSADMIN')
            AND t1.receipt_type <> 2
            AND T1.sbs_no = $sbs
            AND t1.STORE_NO IN ($tienda)
            AND EXTRACT(YEAR FROM t1.CREATED_dATETIME) || TO_CHAR(trunc(T1.CREATED_DATETIME, 'd'), 'IW') + 1 = '$semana'
            
        GROUP BY t1.store_code, t1.employee1_login_name, trunc(t1.created_datetime), T1.DOC_NO, t1.receipt_type, t1.disc_amt
        
    ) A ON (A.COD_VENDEDOR = V.CODIGO_EMPLEADO)
    
WHERE 1 = 1
    AND TO_CHAR(trunc(SYSDATE, 'd'), 'IW') + 1 = V.SEMANA 
    AND TO_CHAR(SYSDATE, 'IYYY') = V.ANIO 
    AND 3 = V.TIENDA  -- Ajusta este valor según tu tienda
    AND V.SBS = 1
    -- Agrega aquí otros filtros necesarios para la tabla de metas
    
GROUP BY V.CODIGO_EMPLEADO, E.NOMBRE, V.META, V.HORA, E.PUESTO, E.FECHA_INGRESO
ORDER BY DECODE(E.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4), E.FECHA_INGRESO ASC";
      $resultado = consultaOracle(3, $query);
      $cnt = 1;

  ?>
      <h3 class="text-center font-weight-bold text-primary">Tienda no: <?php echo $tienda; ?><br><small class="h4 text-primary font-weight-bold text-center"><?php echo "| Año: " . substr($semana, 0, 4) . " | Semana: " . substr($semana, -2) . " | Meta tienda: Q " . number_format(MT($tienda, substr($semana, -2), substr($semana, 0, 4), $sbs)[0], 2) . " |" ?></small></br></h3>
      
      <table style="font-size:14px;" class="table table-hover table-sm tbavxv">
        <thead class="bg-primary">
          <td>No</td>
          <td>Antiguedad</td>
          <td>Código</td>
          <td>Asesora</td>
          <td>Puesto</td>
          <td>hora</td>
          <td>Meta</td>
          <td>Venta</td>
          <td>Diferencia</td>
          <td>Facturas</td>
          <td>Pares roy</td>
          <td>Pares otros</td>
          <td>Pares</td>
          <td>Accesorios</td>
          <td>PPP</td>
          <td>UPT</td>
          <td>QPT</td>
          <td>VH</td>
          <td>%</td>
          <td>Estado</td>
        </thead>

        <tbody class="align-middle font-size" style="width:100%">
          <?php
          foreach ($resultado as $avxv) {
          ?>
            <tr>
              <td><?php echo $cnt++ ?></td>
              <td><?php echo Antiguedad($avxv[15])[0] . " - días" ?></td>
              <td><b><?php echo $avxv[0] ?><b></td>
              <td><?php echo ucwords(strtolower($avxv[1])) ?></td>
              <td><?php echo substr($avxv[2], 0, 3) ?></td>
              <td><?php echo $avxv[16] ?></td>
              <td><?php echo iva($iva, $avxv[3], $sbs) ?></td>
              <td><?php echo iva($iva, $avxv[4], $sbs) ?></td>
              <td style="<?php echo v_vrs_m($avxv[5]) ?>"><?php echo iva($iva, $avxv[5], $sbs) ?></td>
              <td><?php echo $avxv[6] ?></td>
              <td><?php echo $avxv[7] ?></td>
              <td><?php echo $avxv[8] ?></td>
              <td><?php echo $avxv[9] ?></td>
              <td><?php echo $avxv[10] ?></td>
              <td>
                <?php 
                  $divisor = $avxv[9] != 0 ? $avxv[9] : 1; // Si es 0, usa 1 en su lugar
                  echo $sim[0] . " " . number_format($avxv[4] / $divisor, 2);
                ?>
              </td>                        
              <td><?php echo $avxv[12] ?></td>
              <td><?php echo $sim[0] . " " . number_format($avxv[13], 2) ?></td>
              <td><?php echo $sim[0] . " " . number_format($avxv[14], 2) ?></td>
              <td><?php echo Porcentaje($avxv[4], $avxv[3]) . " %" ?></td>
              <td>
                <span class="<?php echo status(Porcentaje($avxv[4], $avxv[3])) ?>" style="<?php echo color2(Porcentaje($avxv[4], $avxv[3]), Antiguedad($avxv[15])[1]) ?>">
                </span>
              </td>
            </tr>
          <?php

          if ($avxv[2] === 'VACACIONISTA') {     $avxv[3] = 0;    }

            $total = array(
              $factura += $avxv[6],
              $pare_roy += $avxv[7],
              $pares_otro += $avxv[8],
              $tota_pares += $avxv[9],
              $accesorios += $avxv[10],
              $venta += $avxv[4],
              $meta += $avxv[3],
              $hora += $avxv[16]
            );
          }
          ?>
          <tr class="align-middle font-weight-bold" style="background-color: #48c9b0; color:rgb(0, 0, 0);">
            <td></td>
            <td></td>
            <td></td>
            <td align="center">TOTAL</td>
            <td></td>
            <td><?php echo $total[7] ?></td>
            <td><?php echo iva($iva, $total[6], $sbs) ?></td>
            <td><?php echo iva($iva, $total[5], $sbs) ?></td>
            <td style="<?php echo v_vrs_m(DifVentaMeta($total[5], $total[6])) ?>"><?php echo iva($iva, DifVentaMeta($total[5], $total[6]), $sbs) ?></td>
            <td><?php echo $total[0] ?></td>
            <td><?php echo $total[1] ?></td>
            <td><?php echo $total[2] ?></td>
            <td><?php echo $total[3] ?></td>
            <td><?php echo $total[4] ?></td>
            <td><?php echo $sim[0] . " " . ppp($total[5], $total[3]) ?></td>
            <td><?php echo upt($total[0], $total[3], $total[4]) ?></td>
            <td><?php echo $sim[0] . " " . qpt($total[5], $total[0]) ?></td>
            <td><?php echo $sim[0] . " " . vh($total[5],$total[7])?></td>
            <td><?php echo Porcentaje($total[5], $total[6]) . " %" ?></th>
            <td>
              <span class="<?php echo status(Porcentaje($total[5], $total[6])) ?>" style="<?php echo color(Porcentaje($total[5], $total[6])) ?>"></span>
            </td>
          </tr>
        </tbody>

        <tfoot>

        </tfoot>
      </table>

      <hr>
  <?php
    }
  }
  ?>
</div>

<script>
  $('.tbavxv').DataTable({
    "searching": false,
    "paging": false,
    "ordering": false,
    "info": false,
    "responsive": true,
    "autoWidth": false
  });

  $('.tooltip').tooltip();

  var url = "../Js/supervision/supervisor.js";
  $.getScript(url);

</script>