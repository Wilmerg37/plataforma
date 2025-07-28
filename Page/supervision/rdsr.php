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

      $query = "Select
        TIENDA, 
        SUM(HORA)HORA  ,
       sum(META) META, 
       SUM(VENTA) VENTA,
       SUM(VENTA)-SUM(META) DIFERENCIA,
       SUM(FACTURAS)FACTURAS,
       SUM(PARES)PARES,
       SUM(ACCESORIOS) ACCESORIOS,
        ROUND(DECODE(SUM(PARES),0,SUM(VENTA),(SUM(VENTA) / SUM(PARES))),2) PPP,
        ROUND(DECODE(SUM(FACTURAS),0,SUM(PARES),(SUM(PARES) / SUM(FACTURAS))),2)UPT, 
        ROUND(DECODE(SUM(FACTURAS),0,SUM(VENTA),(SUM(VENTA) / SUM(FACTURAS))),2) QPT,
          NVL(ROUND(SUM(VENTA) /SUM(HORA),2),0) VH              
FROM(

select	               A.STORE_CODE TIENDA,
					   A.COD_VENDEDOR CODIGO, 
                       A.VENDEDOR NOMBRE,
                       PUESTO,
                       CASE WHEN A.PUESTO = 'VACACIONISTA' then '0' ELSE NVL(META,0) END META,
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
                        HORA
					   FROM (
					   select  t1.store_code, trunc(t1.created_datetime) FECHA, t1.employee1_login_name COD_VENDEDOR, 
					   A.TIPO PUESTO, A.META,A.HORA,
					   t1.employee1_full_name VENDEDOR,
					   E.FECHA_INGRESO CONTRATACION,
					   case when t1.receipt_type=0 then 1 when t1.receipt_type=1 then -1 end TRANSACCIONES, 
					   
					   sum(case when t1.receipt_type=0 and t2.vend_code='001' then (t2.qty)
								when t1.receipt_type=1 and t2.vend_code='001' then (t2.qty)*-1 end) as par_roy, 
					   
					   sum(case when t1.receipt_type=0 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)
								when t1.receipt_type=1 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)*-1 end) as par_otros, 
					   
					   sum(case when t1.receipt_type=0 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)
								when t1.receipt_type=1 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)*-1 end) par_acce,
								
					   sum(case when t1.receipt_type=0  and SUBSTR(T2.DCS_CODE,1,3)not in ('SER','PRE','PRO')  then (T2.qty) 
								when t1.receipt_type=1  and SUBSTR(T2.DCS_CODE,1,3)not in ('SER','PRE','PRO')  then (T2.qty)*-1 end ) as cantidad,           
					   
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
									when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12*-1 end ),0)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0))/1.12  as venta_sin_iva   					   
							 
					   from rps.document t1 
                       inner join rps.document_item t2 on (t1.sid = t2.doc_sid)
                       inner join rps.store st on t1.store_no = st.store_no 
                        INNER JOIN  rps.subsidiary s on st.sbs_sid=s.sid AND t1.SBS_NO = S.SBS_NO 
					   INNER JOIN ROY_META_SEM_X_VENDEDOR A ON  TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = A.SEMANA 
             AND TO_CHAR(T1.CREATED_DATETIME,'IYYY') = A.ANIO 
             AND T1.STORE_NO = A.TIENDA AND t1.employee1_login_name = A.CODIGO_EMPLEADO AND T1.SBS_NO = A.SBS
					   INNER join ROY_VENDEDORES_FRIED E on (E.CODIGO_VENDEDOR = t1.employee1_login_name)
					   where 1=1
					   and t1.status=4 
						and t1.receipt_type<>2
                        --  AND E.PUESTO <> 'VACACIONISTA'
                          AND T1.sbs_no = $sbs
                          AND st.UDF1_STRING = ( $tienda)
                          and EXTRACT(YEAR FROM t1.CREATED_dATETIME)|| TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = '$semana'
                        --   AND A.ANIO||A.SEMANA = '$semana'
                          $filtro
                     --   and t1.CREATED_DATETIME between to_date('2024-05-26 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('2024-06-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
						
						group by t1.store_code,  t1.employee1_login_name, t1.employee1_full_name, trunc(t1.created_datetime), T1.DOC_NO, t1.receipt_type, t1.disc_amt, A.TIPO, A.META, A.HORA, E.FECHA_INGRESO
							)A 
							GROUP BY A.STORE_CODE, A.COD_VENDEDOR, A.VENDEDOR, PUESTO, META, CONTRATACION, HORA
							order by 1,2,3)TA
                            GROUP BY TIENDA
                            order by TIENDA";
      $resultado = consultaOracle(3, $query);
      
      $cnt=1;
   
  ?>
      <h2 class="text-center font-weight-bold text-primary">supervisor: <?php echo $tienda; ?>
      <br><small class="h4 text-primary font-weight-bold text-center"><?php echo "| Año: " . substr($semana, 0, 4) . " | Semana: " . substr($semana, -2) . " | Meta Semana: Q " . number_format(MTSS($tienda, substr($semana, -2), substr($semana, 0, 4), $sbs)[0], 2) . " |" ?></small></br></h2>
        
      <table style="font-size:14px;" class="table table-hover table-sm tbrdst">
        <thead class="bg-primary">
          <td>No</td>
          <td>Tienda</td>
          <td>Hora</td>
          <td>Meta</td>
          <td>Venta</td>
          <td>Diferencia</td>
          <td>Facturas</td>
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
          foreach ($resultado as $rdst) {
          ?>
            <tr>
              <td><?php echo $cnt++ ?></td>
            
              <td><b><?php echo $rdst[0] ?><b></td>
              <td><?php echo ucwords(strtolower($rdst[1])) ?></td>
              <td style="<?php echo v_vrs_m($rdst[2]) ?>"><?php echo iva($iva, $rdst[2], $sbs) ?></td>
              <td style="<?php echo v_vrs_m($rdst[3]) ?>"><?php echo iva($iva, $rdst[3], $sbs) ?></td>
              <td style="<?php echo v_vrs_m($rdst[4]) ?>"><?php echo iva($iva, $rdst[4], $sbs) ?></td>
              
              <td><?php echo $rdst[5] ?></td>
              <td><?php echo $rdst[6] ?></td>
              <td><?php echo $rdst[7] ?></td>
              <td><?php echo $sim[0] . " " . number_format($rdst[3] / $rdst[6] , 2) ?></td>
              <td><?php echo $rdst[9] ?></td>
              <td><?php echo $sim[0] . " " . number_format($rdst[10], 2) ?></td>
              <td><?php echo $sim[0] . " " . number_format($rdst[11], 2) ?></td>
              <td><?php echo Porcentaje($rdst[3], $rdst[2]) . " %" ?></td>
              <td>
                <span class="<?php echo status(Porcentaje($rdst[3], $rdst[2])) ?>" style="<?php echo color(Porcentaje($rdst[3], $rdst[2])) ?>">
                </span>
              </td>
            </tr>
          <?php

if ($rdst[2] === 'VACACIONISTA') {
  $rdst[3] = 0;
}



            $total = array(
              $hora += $rdst[1],  
              $tota_pares += $rdst[6],
              $accesorios += $rdst[7],
              $venta += $rdst[3],
              $meta += $rdst[2],
              $factura += $rdst[5]
             
            );
                 }
               
            
          ?>
          <tr class="align-middle font-weight-bold" style="background-color: #48c9b0; color:rgb(0, 0, 0);">
            <td></td>
          
            
            <td align="center">TOTAL</td>
            
            <td><?php echo $total[0] ?></td>
            <td><?php echo iva($iva, $total[4], $sbs) ?></td>
            <td><?php echo iva($iva, $total[3], $sbs) ?></td>
            <td style="<?php echo v_vrs_m(DifVentaMeta($total[3], $total[4])) ?>"><?php echo iva($iva, DifVentaMeta($total[3], $total[4]), $sbs) ?></td>
            <td><?php echo $total[5] ?></td>
            <td><?php echo $total[1] ?></td>
            <td><?php echo $total[2] ?></td>
            <td><?php echo $sim[0] . " " . ppp($total[3], $total[1]) ?></td>
            <td><?php echo upt($total[5], $total[1], $total[2]) ?></td>
            <td><?php echo $sim[0] . " " . qpt($total[3], $total[5]) ?></td>
            <td><?php echo $sim[0] . " " . vh($total[3],$total[0])?></td>
            <td><?php echo Porcentaje($total[3], $total[4]) . " %" ?></th>
            <td>
              <span class="<?php echo status(Porcentaje($total[3], $total[4])) ?>" style="<?php echo color(Porcentaje($total[3], $total[4])) ?>"></span>
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
  $('.tbrdst').DataTable({
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