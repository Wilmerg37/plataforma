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
$semanas = rangoWY($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);
?>

<!-- Enlace al archivo CSS profesional -->
<link rel="stylesheet" href="../css/estilords.css">

<div class="professional-tienda-container">
  <div class="container-fluid">
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
                          HORA
             FROM (
             select  t1.store_code, trunc(t1.created_datetime) FECHA, t1.employee1_login_name COD_VENDEDOR,
            A.META,A.HORA,
             t1.employee1_full_name VENDEDOR,
                         E.FECHA_INGRESO CONTRATACION,
             E.PUESTO,
             case when t1.receipt_type=0 then 1 when t1.receipt_type=1 then -1 end TRANSACCIONES, 
             
             sum(case when t1.receipt_type=0 and t2.vend_code='001' then (t2.qty)
                  when t1.receipt_type=1 and t2.vend_code='001' then (t2.qty)*-1 end) as par_roy, 
             
             sum(case when t1.receipt_type=0 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)
                  when t1.receipt_type=1 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)*-1 end) as par_otros, 
             
             sum(case when t1.receipt_type=0 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)
                  when t1.receipt_type=1 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)*-1 end) par_acce,
                  
             sum(case when t1.receipt_type=0  and SUBSTR(T2.DCS_CODE,1,3)not in ('SER','PRE','PRO')   then (T2.qty) 
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
             inner JOIN ROY_META_SEM_X_VENDEDOR A ON  TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = A.SEMANA AND TO_CHAR(T1.CREATED_DATETIME,'IYYY') = A.ANIO AND T1.STORE_NO = A.TIENDA AND t1.employee1_login_name = A.CODIGO_EMPLEADO AND T1.SBS_NO = A.SBS
             inner join ROY_VENDEDORES_FRIED E on (E.CODIGO_VENDEDOR = t1.employee1_login_name)
                         
             where 1=1
             and t1.status=4 
                      and t1.employee1_full_name not in ('SYSADMIN')
            and t1.receipt_type<>2
                            AND T1.sbs_no = $sbs
                            AND t1.STORE_NO in($tienda)
                            and EXTRACT(YEAR FROM t1.CREATED_dATETIME)|| TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = '$semana'
                       --   and t1.CREATED_DATETIME between to_date('2024-05-26 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('2024-06-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            
            group by t1.store_code,  t1.employee1_login_name, t1.employee1_full_name, trunc(t1.created_datetime), T1.DOC_NO, t1.receipt_type, t1.disc_amt,  A.META, A.HORA,E.PUESTO,E.FECHA_INGRESO
               ,NVL( t2.lty_piece_of_tbr_disc_amt,0) 
                   )A 
              GROUP BY A.STORE_CODE, A.COD_VENDEDOR, A.VENDEDOR, META,  HORA, PUESTO, CONTRATACION
                               ORDER BY DECODE(PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),CONTRATACION ASC";
        $resultado = consultaOracle(3, $query);
        $cnt = 1;

    ?>
    
    <div class="tienda-report-card">
      <div class="tienda-header-gradient">
        <div class="tienda-header-content">
          <h3 class="tienda-title">
            <i class="fas fa-store tienda-icon-header"></i>
            Tienda no: <?php echo $tienda; ?>
          </h3>
          <div class="tienda-subtitle">
            <i class="fas fa-calendar-week"></i> Año: <?php echo substr($semana, 0, 4) ?>
            <i class="fas fa-chart-line" style="margin-left: 15px;"></i> Semana: <?php echo substr($semana, -2) ?>
            <i class="fas fa-target" style="margin-left: 15px;"></i> Meta tienda: Q <?php echo number_format(MTS($tienda, substr($semana, -2), substr($semana, 0, 4), $sbs)[0], 2) ?>
          </div>
        </div>
      </div>
      
      <div class="tienda-table-responsive">
        <table class="table professional-tienda-table tbavxv">
          <thead>
            <td><i class="fas fa-hashtag tienda-table-icon"></i>No</td>
            <td><i class="fas fa-history tienda-table-icon"></i>Antigüedad</td>
            <td><i class="fas fa-id-badge tienda-table-icon"></i>Código</td>
            <td><i class="fas fa-user tienda-table-icon"></i>Asesora</td>
            <td><i class="fas fa-briefcase tienda-table-icon"></i>Puesto</td>
            <td><i class="fas fa-clock tienda-table-icon"></i>Hora</td>
            <td><i class="fas fa-bullseye tienda-table-icon"></i>Meta</td>
            <td><i class="fas fa-dollar-sign tienda-table-icon"></i>Venta</td>
            <td><i class="fas fa-balance-scale tienda-table-icon"></i>Diferencia</td>
            <td><i class="fas fa-receipt tienda-table-icon"></i>Facturas</td>
            <td><i class="fas fa-crown tienda-table-icon"></i>Pares Roy</td>
            <td><i class="fas fa-shoe-prints tienda-table-icon"></i>Pares Otros</td>
            <td><i class="fas fa-layer-group tienda-table-icon"></i>Pares</td>
            <td><i class="fas fa-shopping-bag tienda-table-icon"></i>Accesorios</td>
            <td><i class="fas fa-calculator tienda-table-icon"></i>PPP</td>
            <td><i class="fas fa-chart-bar tienda-table-icon"></i>UPT</td>
            <td><i class="fas fa-coins tienda-table-icon"></i>QPT</td>
            <td><i class="fas fa-tachometer-alt tienda-table-icon"></i>VH</td>
            <td><i class="fas fa-percentage tienda-table-icon"></i>%</td>
            <td><i class="fas fa-traffic-light tienda-table-icon"></i>Estado</td>
          </thead>

          <tbody>
            <?php
            foreach ($resultado as $avxv) {
              // Determinar clase de puesto para el badge
              $positionClass = '';
              $positionText = substr($avxv[2], 0, 3);
              if (strpos($avxv[2], 'JEFE DE TIENDA') !== false) {
                $positionClass = 'position-jefe';
                $positionText = 'JEF';
              } elseif (strpos($avxv[2], 'SUB JEFE') !== false) {
                $positionClass = 'position-sub';
                $positionText = 'SUB';
              } elseif (strpos($avxv[2], 'ASESOR') !== false) {
                $positionClass = 'position-asesor';
                $positionText = 'ASE';
              }
            ?>
              <tr>
                <td><span class="tienda-metric-badge"><?php echo $cnt++ ?></span></td>
                <td>
                  <i class="fas fa-calendar-alt" style="color: #7f8c8d; margin-right: 5px;"></i>
                  <?php echo Antiguedad($avxv[15])[0] . " días" ?>
                </td>
                <td><span class="tienda-code-badge"><?php echo $avxv[0] ?></span></td>
                <td>
                  <i class="fas fa-user-circle" style="color: #3498db; margin-right: 5px;"></i>
                  <?php echo ucwords(strtolower($avxv[1])) ?>
                </td>
                <td><span class="tienda-position-badge <?php echo $positionClass ?>"><?php echo $positionText ?></span></td>
                <td>
                 
                  <?php echo $avxv[16] ?>
                </td>
                <td class="currency-value"><?php echo iva($iva, $avxv[3], $sbs) ?></td>
                <td class="currency-value"><?php echo iva($iva, $avxv[4], $sbs) ?></td>
                <td style="<?php echo v_vrs_m($avxv[5]) ?>" class="currency-value"><?php echo iva($iva, $avxv[5], $sbs) ?></td>
                <td class="count-value">
                
                  <?php echo $avxv[6] ?>
                </td>
                <td class="count-value"><?php echo $avxv[7] ?></td>
                <td class="count-value"><?php echo $avxv[8] ?></td>
                <td class="count-value highlight-metric"><?php echo $avxv[9] ?></td>
                <td class="count-value"><?php echo $avxv[10] ?></td>
                <td class="currency-value"><?php echo $sim[0] . " " . number_format($avxv[4]/$avxv[9], 2) ?></td> 
                <td class="count-value"><?php echo $avxv[12] ?></td>
                <td class="currency-value"><?php echo $sim[0] . " " . number_format($avxv[13], 2) ?></td>
                <td class="currency-value"><?php echo $sim[0] . " " . number_format($avxv[14], 2) ?></td>
                <td class="percentage-value highlight-metric"><?php echo Porcentaje($avxv[4], $avxv[3]) . " %" ?></td>
                <td>
                  <span class="tienda-status-indicator <?php echo status(Porcentaje($avxv[4], $avxv[3])) ?>" style="<?php echo color2(Porcentaje($avxv[4], $avxv[3]), Antiguedad($avxv[15])[1]) ?>">
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
            <tr class="tienda-total-row">
              <td></td>
              <td></td>
              <td></td>
              <td>
                <i class="fas fa-chart-pie" style="margin-right: 8px;"></i>
                <strong>TOTAL</strong>
              </td>
              <td></td>
              <td class="data-value">
                <?php echo $total[7] ?>
              </td>
              <td class="data-value"><?php echo iva($iva, $total[6], $sbs) ?></td>
              <td class="data-value"><?php echo iva($iva, $total[5], $sbs) ?></td>
              <td style="<?php echo v_vrs_m(DifVentaMeta($total[5], $total[6])) ?>" class="data-value"><?php echo iva($iva, DifVentaMeta($total[5], $total[6]), $sbs) ?></td>
              <td class="data-value"><?php echo $total[0] ?></td>
              <td class="data-value"><?php echo $total[1] ?></td>
              <td class="data-value"><?php echo $total[2] ?></td>
              <td class="data-value"><?php echo $total[3] ?></td>
              <td class="data-value"><?php echo $total[4] ?></td>
              <td class="data-value "><?php echo $sim[0] . " " . ppp($total[5], $total[3]) ?></td>
              <td class="data-value"><?php echo upt($total[0], $total[3], $total[4]) ?></td>
              <td class="data-value "><?php echo $sim[0] . " " . qpt($total[5], $total[0]) ?></td>
              <td class="data-value "><?php echo $sim[0] . " " . vh($total[5],$total[7])?></td>
              <td class="data-value percentage-value"><strong><?php echo Porcentaje($total[5], $total[6]) . " %" ?></strong></td>
              <td>
                <span class="tienda-status-indicator <?php echo status(Porcentaje($total[5], $total[6])) ?>" style="<?php echo color(Porcentaje($total[5], $total[6])) ?>"></span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <hr class="tienda-divider">
    <?php
      }
    }
    ?>
  </div>
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