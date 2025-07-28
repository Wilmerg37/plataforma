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
        $factura = 0,
        $pare_roy = 0,
        $pares_otro = 0,
        $tota_pares = 0,
        $accesorios = 0,
        $venta = 0,
        $meta = 0,
        $hora = 0
      );

      $query = "select	   
					   A.COD_VENDEDOR CODIGO, 
                       A.VENDEDOR NOMBRE,
                       PUESTO,
                       NVL(META,0)META,
                       ROUND(SUM(A.venta_SIN_IVA),2) VENTA,
                       NVL(ROUND(SUM(A.venta_SIN_IVA) - (META),2),0) DIFERENCIA,
					   SUM(A.TRANSACCIONES) FACTURAS,
					   NVL(SUM(A.PAR_ROY),0) ROY,
					   NVL(SUM(A.PAR_OTROS),0) OTROS,
					   NVL(SUM(A.PAR_ROY),0) + NVL(SUM(A.PAR_OTROS),0)  PARES,
					   NVL(SUM(A.PAR_ACCE),0) ACCESORIOS,
					
					   ROUND(DECODE(SUM(A.CANTIDAD),0,SUM(A.VENTA_SIN_IVA),(SUM(A.VENTA_SIN_IVA) / SUM(A.CANTIDAD))),2) PPP,
					   ROUND(DECODE(SUM(A.TRANSACCIONES),0,SUM(A.CANTIDAD),(SUM(A.CANTIDAD) / SUM(A.TRANSACCIONES))),2)UPT, 
					   ROUND(DECODE(SUM(A.TRANSACCIONES),0,SUM(A.VENTA_SIN_IVA),(SUM(A.VENTA_SIN_IVA) / SUM(A.TRANSACCIONES))),2) QPT,
					     NVL(ROUND(SUM(A.venta_SIN_IVA /case when HORA = 0 then 1 else A.HORA END),2),0) VH,
                       CONTRATACION,
                        HORA, A.TIENDA
					   FROM (
					   select  t1.store_code TIENDA, trunc(t1.created_datetime) FECHA, t1.employee1_login_name COD_VENDEDOR, 
					   E.PUESTO, A.META,A.HORA,
					   t1.employee1_full_name VENDEDOR,
					   E.FECHA_INGRESO CONTRATACION,
					   case when t1.receipt_type=0 then 1 when t1.receipt_type=1 then -1 end TRANSACCIONES, 
					   
					   sum(case when t1.receipt_type=0 and t2.vend_code='001' then (t2.qty)
								when t1.receipt_type=1 and t2.vend_code='001' then (t2.qty)*-1 end) as par_roy, 
					   
					   sum(case when t1.receipt_type=0 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)
								when t1.receipt_type=1 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)*-1 end) as par_otros, 
					   
					   sum(case when t1.receipt_type=0 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)
								when t1.receipt_type=1 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)*-1 end) par_acce,
								
					   sum(case when t1.receipt_type=0    then (T2.qty) 
								when t1.receipt_type=1    then (T2.qty)*-1 end ) as cantidad,           
					   
					   sum(case when t1.receipt_type=0 and SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty*T2.PRICE)
								when t1.receipt_type=1 and SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty*T2.PRICE)*-1 end)venta_CON_IVA_ACC,
					   
						sum(case when t1.receipt_type=0 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.price)/1.12*(T2.qty)) 
								 when t1.receipt_type=1 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.price)/1.12*(T2.qty))*-1 end ) as venta_sin_iva_ACC,    
					   
					   sum(case when t1.receipt_type=0 then (t2.qty*t2.cost) when t1.receipt_type=1 then (t2.qty*t2.cost)*-1 else 0 end) as costo, 
					   
					   sum(case WHEN t1.receipt_type=0 AND SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.COST)*(T2.qty))
								when t1.receipt_type=1 AND SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.COST)*(T2.qty))*-1 end ) as COSTO_sin_iva_ACC ,
							 
					   NVL(sum(case when t1.receipt_type=0 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))
									when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))*-1 end ),0)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0)) as venta_con_iva, 
							 
					   NVL(sum(case when t1.receipt_type=0 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12 
									        when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12*-1 
                          end ),0)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0))/1.12  as venta_sin_iva   					   
							 
					   from rps.document t1 
                       inner join rps.document_item t2 on (t1.sid = t2.doc_sid)
					   INNER JOIN ROY_META_SEM_X_VENDEDOR A ON  TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = A.SEMANA AND TO_CHAR(T1.CREATED_DATETIME,'IYYY') = A.ANIO AND T1.STORE_NO = A.TIENDA AND t1.employee1_login_name = A.CODIGO_EMPLEADO AND T1.SBS_NO = A.SBS
					   INNER join ROY_VENDEDORES_FRIED E on (E.CODIGO_VENDEDOR = t1.employee1_login_name)
					   where 1=1
					   and t1.status=4 
                    and t1.employee1_full_name not in ('SYSADMIN')
						and t1.receipt_type<>2
                          AND T1.sbs_no = $sbs
                          AND t1.STORE_NO = $tienda
                       -- and EXTRACT(YEAR FROM t1.CREATED_dATETIME)|| TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = '26'
                        and t1.CREATED_DATETIME between to_date('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
						
						group by t1.store_code,  t1.employee1_login_name, t1.employee1_full_name, trunc(t1.created_datetime), T1.DOC_NO, t1.receipt_type, t1.disc_amt, E.PUESTO, A.META, A.HORA, E.FECHA_INGRESO
						,NVL( t2.lty_piece_of_tbr_disc_amt,0)	)A 
							GROUP BY A.TIENDA, A.COD_VENDEDOR, A.VENDEDOR, PUESTO, META, CONTRATACION, HORA, A.TIENDA

							ORDER BY DECODE(PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),CONTRATACION ASC";
      $resultado = consultaOracle(3, $query);
      $cnt = 1;

  ?>
       <br><small class="h4 text-primary font-weight-bold text-center"><?php echo "| Dia:" . date('d-m') . " | Meta del Dia: Q " . number_format(MTDS($tienda,  $fi, date('Y', strtotime($ff)), $sbs)[0], 2) . " |" ?></small></br></h3>
      
      <table style="font-size:14px;" class="table table-hover table-sm tbavxv">
        <thead class="bg-primary">
          <td>No</td>
          <td>Antiguedad</td>
          <td>Código</td>
          <td>Asesora</td>
          <td>Puesto</td>
          <td>hora</td>
          <td>Meta Sem.</td>
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
              <td><?php echo $sim[0] . " " . number_format($avxv[11], 2) ?></td>
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

            if ($avxv[2] === 'VACACIONISTA') {
              $avxv[3] = 0;
            }

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
          <tr class="table-active align-middle font-weight-bold">
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
                        <!--SE AGREGA PARA CALCULO DE PORCENTAJE DE META CONTRA VENTA-->
                <br>
              

                          <thead  class="bg-primary">
                                                                                            
                                          <tr>
                                              <th bgcolor="#FFFFFF" align="center" colspan="20"><font color="Black"><h2> RESUMEN  VENTAS  VRS  METAS</h2></font></th>
                                          </tr>    
                                          <tr>     
                                          <td>TIENDA</td>
                                          <td>META PARES</td>
                                          <td>VENTA PARES</td>
                                          <td>DIFERENCIA</td>
                                          <td>PORCENTAJE META</td>
                                          <td>Estado</td>     
                                          <td> | </td>
                                          <td>META SIN IVA</td>
                                          <td>VENTA SIN IVA</td>
                                          <td>DIFERENCIA</td>
                                          <td>PORCENTAJE META</td>
                                          <td>Estado</td>                                  
                                            
                                          </tr>

                                         
                          </thead>

                          <tbody align="center" style="background-color: #ccc; font-weight:bold;">
		                    	<?php
				 $query ="
									SELECT TIENDA, ROUND(SUM(META_S_IVA),2) META_S_IVA, ROUND(SUM(META_C_IVA),2)META_C_IVA,SUM(META_PRS)META_PRS 
									FROM ROY_META_DIARIA_TDS
									WHERE FECHA  between to_date('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
									AND TIENDA in($tienda)
									GROUP BY TIENDA
									ORDER BY  TIENDA
								 ";
                 $resultado1 = consultaOracle(3, $query);
                 $cnt = 1;

                 
                 foreach ($resultado1 as $avxv1) {
                 ?>
                   <tr>
                     
                     <td><b><?php echo $avxv1[0] ?><b></td>
                     <td><b><?php echo $avxv1[3] ?><b></td>
                     <td><?php echo $total[3] ?></td>
                     <td style="<?php echo v_vrs_m(DifVentaMeta($total[3], $avxv1[3])) ?>"><?php echo iva($iva, DifVentaMeta($total[3],$avxv1[3]), $sbs) ?></td>
                     <td><?php echo Porcentaje($total[3], $avxv1[3]) . " %" ?></th>
                     <td><span class="<?php echo status(Porcentaje($total[3], $avxv1[3])) ?>" style="<?php echo color(Porcentaje($total[3], $avxv1[3])) ?>"></span></td>
                     <td> | </td>
                     <td><?php echo iva($iva, $avxv1[1], $sbs) ?></td>
                    <td><?php echo iva($iva, $total[5], $sbs) ?></td>
                    <td style="<?php echo v_vrs_m(DifVentaMeta($total[5], $avxv1[1])) ?>"><?php echo iva($iva, DifVentaMeta($total[5],$avxv1[1]), $sbs) ?></td>
                    <td><?php echo Porcentaje($total[5], $avxv1[1]) . " %" ?></th>
                    <td>
                      <span class="<?php echo status(Porcentaje($total[5], $avxv1[1])) ?>" style="<?php echo color(Porcentaje($total[5], $avxv1[1])) ?>"></span>
                    </td>
                    
                   </tr>
					
					
								 
										 
								 <?php
				 
								 
									 
								 }//fin del for
								 
								 
								 ?>
			                    </tbody>                  
                      
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
  $('.tbavxv').DataTable({
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