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
        $hora = 0,
        $mt_prs = 0 ,
        $vta_prs = 0 ,
        $dif_prs = 0
      );

      $query = "
      SELECT  MT.TIENDA,	MT.NOMBRETIENDA,
					   MT.META_PRS,
                         NVL(SUM(A.PAR_ROY),0) + NVL(SUM(A.PAR_OTROS),0)  VENTA_PRS,
                       
                            NVL(SUM(A.PAR_ROY),0) + NVL(SUM(A.PAR_OTROS),0) - MT.META_PRS DIF_PRS,
					  NVL( ROUND(SUM(A.venta_SIN_IVA),2),0) VENTA_SIN_IVA,
                       MT.META_S_IVA,
						NVL(ROUND(SUM(A.venta_SIN_IVA),2)- (MT.META_S_IVA),0)DIF, 
                        MT.COD_SUP, MT.NOM_SUP, A.DIA, A.FECHA,
                         ROUND(DECODE(SUM(A.TRANSACCIONES),0,SUM(A.CANTIDAD),(SUM(A.CANTIDAD) / SUM(A.TRANSACCIONES))),2)UPT, 
					   ROUND(DECODE(SUM(A.TRANSACCIONES),0,SUM(A.VENTA_SIN_IVA),(SUM(A.VENTA_SIN_IVA) / SUM(A.TRANSACCIONES))),2) QPT
										   FROM 
							 
				
                              
							(SELECT M.TIENDA,M.FECHA,M.META_S_IVA,M.META_C_IVA,M.SEMANA,M.META_PRS,M.ANIO,M.COD_SUPER, ST.STORE_NAME NOMBRETIENDA , ST.UDF1_STRING COD_SUP,ST.UDF2_STRING NOM_SUP FROM ROY_META_DIARIA_TDS M
                                   INNER JOIN RPS.STORE ST ON M.TIENDA = ST.STORE_NO
			   WHERE FECHA  between to_date('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
			   AND ST.SBS_SID = '680861302000159257'
                )MT
               LEFT JOIN 
               (
										   select S.UDF1_STRING COD_SUP,S.UDF2_STRING NOM_SUP, t1.store_NO, trunc(t1.created_datetime) FECHA, t1.employee1_login_name COD_VENDEDOR, 
										   TO_CHAR(T1.CREATED_DATETIME, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') DIA,
										   t1.employee1_full_name VENDEDOR,
										     s.store_name NOMBRETIENDA,
										 --  MAX((SELECT STORE_NAME FROM RPS.STORE  WHERE STORE_NO = s.store_no  AND SBS_SID = '680861302000159257' AND ADDRESS1 IS NOT NULL )) NOMBRETIENDA,
										   case when t1.receipt_type=0 then 1 when t1.receipt_type=1 then -1 end TRANSACCIONES, 
										   
										   sum(case when t1.receipt_type=0 and t2.vend_code='001' then (t2.qty)
													when t1.receipt_type=1 and t2.vend_code='001' then (t2.qty)*-1 end) as par_roy, 
										   
										   sum(case when t1.receipt_type=0 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)
													when t1.receipt_type=1 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)*-1 end) as par_otros, 
										   
										   sum(case when t1.receipt_type=0 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)
													when t1.receipt_type=1 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)*-1 end) par_acce,
													
										sum(case when t1.receipt_type=0  and SUBSTR(T2.DCS_CODE,1,3)not in ('SER','PRE','PRO')   then (T2.qty) 
								when t1.receipt_type=1  and SUBSTR(T2.DCS_CODE,1,3)not in ('SER','PRE','PRO')  then (T2.qty)*-1 end )as cantidad,           
										   
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
														when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12*-1 end ),0)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0))/1.12 as venta_sin_iva     
										   
												 
										   from rps.document t1 
                                           inner join rps.document_item t2 on (t1.sid = t2.doc_sid)					   
										   INNER join rps.STORE S on (S.sid=t1.STORE_SID)
										   where 1=1
										   and t1.status=4 
											and t1.receipt_type<>2
											and T1.sbs_no=1 
                                           				
						and t1.CREATED_DATETIME between to_date('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
						
						group by S.UDF1_STRING ,S.UDF2_STRING,   s.store_name,t1.store_NO,  t1.employee1_login_name, t1.employee1_full_name, trunc(t1.created_datetime), TO_CHAR(T1.CREATED_DATETIME, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH'),
             T1.DOC_NO, t1.receipt_type, t1.disc_amt,NVL( t2.lty_piece_of_tbr_disc_amt,0)
							)A 

               
			   ON MT.TIENDA = A.STORE_NO  AND MT.FECHA = A.FECHA  
               WHERE MT.COD_SUP = $tienda
			   GROUP BY MT.TIENDA, MT.META_S_IVA,MT.NOMBRETIENDA,MT.COD_SUP, MT.NOM_SUP, MT.META_PRS, A.DIA, A.FECHA
			   ORDER BY cast(MT.TIENDA as int), A.FECHA ";
      $resultado = consultaOracle(3, $query);      
      $cnt=1;
   
  ?>
      <h3 class="text-center font-weight-bold text-primary">supervisor: <?php echo $tienda ?>
    
   
      <br><small class="h6 text-primary font-weight-bold text-center"><?php echo "| Dia:" . date('d-m') . " | Meta del Dia: Q " . number_format(MTDS($tienda,  $fi, date('Y', strtotime($ff)), $sbs)[0], 2) . " |" ?></small></br></h3>
    

      <table  style="font-size:14px;" class="table table-hover table-sm tbrdst">
        <thead class="bg-primary">
          <td>No</td>
          <td>Tienda</td>
          <td>Nombre de tienda</td>          
          <td>Dia</td>
          <td>Fecha</td>
          <td>Meta Prs</td>
          <td>Venta Prs</td>
          <td>Dif. Prs</td>
          <td>UPT</td>
          <td>Porc. Prs</td>
          <td>Estatus</td>
          <th>|</th>
          <td>Meta del dia</td>
          <td>Venta del dia</td>          
          <td>Diferencia</td>
          <td>QPT</td>
          <td>%</td>
          <td>Estado</td>
        </thead>
        
        <tbody class="align-middle font-size" style="width:100%">
          <?php
          foreach ($resultado as $rdst) {
          ?>
            <tr style="background-color: <?php echo ($rdst[3] == 0) ? '#ff4d4d' : '#eaeded'; ?>; font-weight:bold;">

              <td><?php echo $cnt++ ?></td>            
              <td><b><?php echo $rdst[0] ?><b></td>
              <td><?php echo $rdst[1] ?></td>
              <td><?php echo $rdst[10] ?></td>
              <td><?php echo $rdst[11] ?></td>
              <td colspan = 1><?php echo $rdst[2] ?></td>
              <td colspan = 1><b><?php echo $rdst[3] ?><b></td>
              <td style="<?php echo v_vrs_m($rdst[4]) ?>"><?php echo $rdst[4] ?></td>
              <td><?php echo $rdst[12] ?></td>
              <td><?php echo Porcentaje($rdst[3], $rdst[2]) . " %" ?></td>
              <td>
                <span class="<?php echo status(Porcentaje($rdst[3], $rdst[2])) ?>" style="<?php echo color(Porcentaje($rdst[3], $rdst[2])) ?>">
                </span>
              </td>
							<td><?php echo" | "?></td>
              <td style="<?php echo v_vrs_m($rdst[6]) ?>"><?php echo iva($iva, $rdst[6], $sbs) ?></td>
              <td style="<?php echo v_vrs_m($rdst[5]) ?>"><?php echo iva($iva, $rdst[5], $sbs) ?></td>              
              <td style="<?php echo v_vrs_m($rdst[7]) ?>"><?php echo iva($iva, $rdst[7], $sbs) ?></td>
              <td><?php echo $sim[0] . " " . number_format($rdst[13], 2) ?></td>
              <td><?php echo Porcentaje($rdst[5], $rdst[6]) . " %" ?></td>
              <td>
                <span class="<?php echo status(Porcentaje($rdst[5], $rdst[6])) ?>" style="<?php echo color(Porcentaje($rdst[5], $rdst[6])) ?>">
                </span>
              </td>
              
            </tr>
          <?php

            if ($rdst[3] === 'VACACIONISTA') {
              $rdst[6] = 0;
            }

            $total = array(
             
              $mt_prs += $rdst[2],
              $vta_prs += $rdst[3],
              $dif_prs += $rdst[4],
              $venta += $rdst[5],        
              $meta += $rdst[6]
             
             
            );
                 }
               
            
          ?>
          <tr class="align-middle font-weight-bold" style="background-color: #48c9b0; color:rgb(0, 0, 0);">
            <td></td>         
            
            <td colspan = 4 align="center">TOTAL</td>
            <td><?php echo $total[0] ?></td>
            <td><?php echo $total[1] ?></td>
            <td style="<?php echo v_vrs_m($total[2]) ?>"><?php echo $total[2]?></td>
            <td><?php  ?></td>
            <td><?php echo Porcentaje($total[1], $total[0]) . " %" ?></td>
            <td>
              <span class="<?php echo status(Porcentaje($total[1], $total[0])) ?>" style="<?php echo color(Porcentaje($total[1], $total[0])) ?>"></span>
            </td>
            <td>|</td>                            
            <td><?php echo iva($iva, $total[4], $sbs) ?></td>
            <td><?php echo iva($iva, $total[3], $sbs) ?></td>
            <td style="<?php echo v_vrs_m(DifVentaMeta($total[3], $total[4])) ?>"><?php echo iva($iva, DifVentaMeta($total[3], $total[4]), $sbs) ?></td>
            <td><?php  ?></td>
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