<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

// Autoload para Dompdf (ajusta la ruta según tu instalación)
require_once "../../vendor/autoload.php";

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

// Calcular el rango de 12 semanas para el segundo reporte
$fecha_fin_segunda = $ff;
$fecha_inicio_segunda = date('Y-m-d', strtotime($fecha_fin_segunda . ' -11 weeks'));

$semanas = rangoWY($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);

// Función para generar PDF
function generarPDF($contenidoHTML, $nombreArchivo = 'reporte_ventas.pdf') {
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml($contenidoHTML);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($nombreArchivo, array("Attachment" => false));
}

// Verificar si se solicita PDF
if (isset($_POST['export_pdf']) && $_POST['export_pdf'] == '1') {
    ob_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .sales-container { font-family: Arial, sans-serif; }
        .section-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .table-responsive { overflow-x: auto; }
        .status-indicator { font-size: 1.5em; display: inline-block; }
        .store-group { border: 2px solid #dee2e6; border-radius: 10px; margin-bottom: 30px; padding: 20px; }
        .export-buttons { position: fixed; top: 20px; right: 20px; z-index: 1000; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<div class="container-fluid sales-container">
    
    <!-- Botones de exportación -->
    <div class="export-buttons no-print">
        <form method="post" style="display: inline;">
            <?php
            foreach ($_POST as $key => $value) {
                if ($key != 'export_pdf') {
                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                }
            }
            ?>
            <input type="hidden" name="export_pdf" value="1">
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </form>
    </div>

    <!-- Encabezado del reporte -->
    <div class="section-header text-center">
        <h1><i class="fas fa-chart-bar"></i> REPORTE INTEGRAL DE VENTAS</h1>
        <p class="mb-0">Período: <?php echo date('d/m/Y', strtotime($fi)) . " al " . date('d/m/Y', strtotime($ff)); ?></p>
    </div>

    <?php foreach ($tiendas as $tienda_actual): ?>
    
    <div class="store-group">
        
        <!-- SECCIÓN 1: REPORTE DETALLADO POR SEMANA -->
        <div class="mb-5">
            <h2 class="text-center font-weight-bold text-info mb-4">
                <i class="fas fa-chart-line"></i> REPORTE DETALLADO - TIENDA <?php echo $tienda_actual; ?>
            </h2>
            
            <?php foreach ($semanas as $semana): ?>
                <?php
                $total = array(
                    'facturas' => 0,
                    'pare_roy' => 0,
                    'pares_otro' => 0,
                    'total_pares' => 0,
                    'accesorios' => 0,
                    'venta' => 0,
                    'meta' => 0,
                    'hora' => 0
                );

                $query = "select	   
                           A.COD_VENDEDOR CODIGO, 
                           A.VENDEDOR NOMBRE,
                           A.PUESTO,
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
                          A.META,
                A.HORA ,
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
                           inner JOIN ROY_META_SEM_X_VENDEDOR A ON  TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = A.SEMANA 
                 AND TO_CHAR(T1.CREATED_DATETIME,'IYYY') = A.ANIO 
                 AND T1.STORE_NO = A.TIENDA
                  AND t1.employee1_login_name = A.CODIGO_EMPLEADO 
                  AND T1.SBS_NO = A.SBS
                           inner join ROY_VENDEDORES_FRIED E on (E.CODIGO_VENDEDOR = t1.employee1_login_name)
                           
                           where 1=1
                           and t1.status=4 
                        and t1.employee1_full_name not in ('SYSADMIN')
                            and t1.receipt_type<>2
                              AND T1.sbs_no = $sbs
                              AND t1.STORE_NO = $tienda_actual
                              and EXTRACT(YEAR FROM t1.CREATED_dATETIME)|| TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = '$semana'
                              $filtro
                            
                            group by t1.store_code,  t1.employee1_login_name, t1.employee1_full_name, trunc(t1.created_datetime), T1.DOC_NO, t1.receipt_type, t1.disc_amt,  A.META, A.HORA,E.PUESTO,E.FECHA_INGRESO
                                  
                     )A 
                                GROUP BY A.STORE_CODE, A.COD_VENDEDOR, A.VENDEDOR, META,  HORA, PUESTO, CONTRATACION
                                 ORDER BY DECODE(PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),CONTRATACION ASC";
                
                $resultado = consultaOracle(3, $query);
                $cnt = 1;
                ?>
                
                <h4 class="text-center font-weight-bold text-primary">
                    <?php echo "Año: " . substr($semana, 0, 4) . " | Semana: " . substr($semana, -2) . " | Meta tienda: " . $sim[0] . " " . number_format(MT($tienda_actual, substr($semana, -2), substr($semana, 0, 4), $sbs)[0], 2); ?>
                </h4>
                
                <div class="table-responsive">
                    <table class="table table-hover table-sm table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Antigüedad</th>
                                <th>Código</th>
                                <th>Asesora</th>
                                <th>Puesto</th>
                                <th>Hora</th>
                                <th>Meta</th>
                                <th>Venta</th>
                                <th>Diferencia</th>
                                <th>Facturas</th>
                                <th>Pares Roy</th>
                                <th>Pares Otros</th>
                                <th>Pares</th>
                                <th>Accesorios</th>
                                <th>PPP</th>
                                <th>UPT</th>
                                <th>QPT</th>
                                <th>VH</th>
                                <th>%</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $avxv): ?>
                            <tr>
                                <td><?php echo $cnt++; ?></td>
                                <td><?php echo Antiguedad($avxv[15])[0] . " días"; ?></td>
                                <td><strong><?php echo $avxv[0]; ?></strong></td>
                                <td><?php echo ucwords(strtolower($avxv[1])); ?></td>
                                <td><?php echo substr($avxv[2], 0, 3); ?></td>
                                <td><?php echo $avxv[16]; ?></td>
                                <td><?php echo iva($iva, $avxv[3], $sbs); ?></td>
                                <td><?php echo iva($iva, $avxv[4], $sbs); ?></td>
                                <td style="<?php echo v_vrs_m($avxv[5]); ?>"><?php echo iva($iva, $avxv[5], $sbs); ?></td>
                                <td><?php echo $avxv[6]; ?></td>
                                <td><?php echo $avxv[7]; ?></td>
                                <td><?php echo $avxv[8]; ?></td>
                                <td><?php echo $avxv[9]; ?></td>
                                <td><?php echo $avxv[10]; ?></td>
                                <td><?php 
                                    $divisor = $avxv[9] != 0 ? $avxv[9] : 1;
                                    echo $sim[0] . " " . number_format($avxv[4] / $divisor, 2);
                                ?></td>
                                <td><?php echo $avxv[12]; ?></td>
                                <td><?php echo $sim[0] . " " . number_format($avxv[13], 2); ?></td>
                                <td><?php echo $sim[0] . " " . number_format($avxv[14], 2); ?></td>
                                <td><?php echo Porcentaje($avxv[4], $avxv[3]) . "%"; ?></td>
                                <td>
                                    <span class="status-indicator <?php echo status(Porcentaje($avxv[4], $avxv[3])); ?>" 
                                          style="<?php echo color2(Porcentaje($avxv[4], $avxv[3]), Antiguedad($avxv[15])[1]); ?>">
                                        ●
                                    </span>
                                </td>
                            </tr>
                            <?php
                            if ($avxv[2] === 'VACACIONISTA') { $avxv[3] = 0; }
                            
                            $total['facturas'] += $avxv[6];
                            $total['pare_roy'] += $avxv[7];
                            $total['pares_otro'] += $avxv[8];
                            $total['total_pares'] += $avxv[9];
                            $total['accesorios'] += $avxv[10];
                            $total['venta'] += $avxv[4];
                            $total['meta'] += $avxv[3];
                            $total['hora'] += $avxv[16];
                            ?>
                            <?php endforeach; ?>
                            
                            <tr class="font-weight-bold" style="background-color: #48c9b0; color: #000;">
                                <td colspan="4" class="text-center"><strong>TOTAL</strong></td>
                                <td></td>
                                <td><?php echo $total['hora']; ?></td>
                                <td><?php echo iva($iva, $total['meta'], $sbs); ?></td>
                                <td><?php echo iva($iva, $total['venta'], $sbs); ?></td>
                                <td style="<?php echo v_vrs_m(DifVentaMeta($total['venta'], $total['meta'])); ?>">
                                    <?php echo iva($iva, DifVentaMeta($total['venta'], $total['meta']), $sbs); ?>
                                </td>
                                <td><?php echo $total['facturas']; ?></td>
                                <td><?php echo $total['pare_roy']; ?></td>
                                <td><?php echo $total['pares_otro']; ?></td>
                                <td><?php echo $total['total_pares']; ?></td>
                                <td><?php echo $total['accesorios']; ?></td>
                                <td><?php echo $sim[0] . " " . ppp($total['venta'], $total['total_pares']); ?></td>
                                <td><?php echo upt($total['facturas'], $total['total_pares'], $total['accesorios']); ?></td>
                                <td><?php echo $sim[0] . " " . qpt($total['venta'], $total['facturas']); ?></td>
                                <td><?php echo $sim[0] . " " . vh($total['venta'], $total['hora']); ?></td>
                                <td><?php echo Porcentaje($total['venta'], $total['meta']) . "%"; ?></td>
                                <td>
                                    <span class="status-indicator <?php echo status(Porcentaje($total['venta'], $total['meta'])); ?>" 
                                          style="<?php echo color(Porcentaje($total['venta'], $total['meta'])); ?>">
                                        ●
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>

        <!-- SECCIÓN 2: REPORTE DE TENDENCIAS (12 SEMANAS) -->
        <div class="mb-5">
            <h2 class="text-center font-weight-bold text-warning mb-4">
                <i class="fas fa-chart-area"></i> REPORTE DE TENDENCIAS - TIENDA <?php echo $tienda_actual; ?>
            </h2>
            <div class="text-center mb-4">
                <small class="h5 text-info">
                    <i class="fas fa-calendar-alt"></i> 
                    Período: <?php echo date('d/m/Y', strtotime($fecha_inicio_segunda)) . " al " . date('d/m/Y', strtotime($fecha_fin_segunda)); ?>
                </small>
            </div>

            <?php
            $promediosPorTienda = array();
            
            $query = "
               SELECT E.TIENDA, E.CODIGO_VENDEDOR, E.NOMBRE, E.PUESTO, NVL(E.FECHA_INGRESO, TO_DATE(sysdate, 'DD-MM-YYYY')) AS CONTRATACION, A.SEMANA, 
                     CASE WHEN A.TIPO = 'VACACIONISTA' THEN 0 ELSE ROUND(A.META, 2) END META,
                      ROUND(NVL(sum(CASE 
                                      WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
                                      WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
                                    END), 0), 2) VENTA,
                                          
                                  ROUND(
            NVL(
              sum(CASE 
                    WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
                    WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
                  END), 0
            ) 
            / DECODE(A.META, 0, 1, A.META) * 100, 2
          ) PORCENTAJE
           
                FROM rps.document t1
                INNER JOIN rps.document_item t2 ON (t1.sid = t2.doc_sid)
                INNER JOIN ROY_META_SEM_X_VENDEDOR A ON TO_CHAR(trunc(T1.CREATED_DATETIME, 'd'), 'IW') + 1 = A.SEMANA 
                                                        AND TO_CHAR(T1.CREATED_DATETIME, 'IYYY') = A.ANIO 
                                                        AND T1.STORE_NO = A.TIENDA 
                                                        AND t1.employee1_login_name = A.CODIGO_EMPLEADO 
                                                        AND T1.SBS_NO = A.SBS
                INNER JOIN ROY_VENDEDORES_FRIED E ON (E.CODIGO_VENDEDOR = t1.employee1_login_name)
                WHERE 1 = 1
                      AND t1.status = 4
                      AND t1.receipt_type <> 2
                      AND T1.sbs_no = $sbs
                      AND t1.STORE_NO = $tienda_actual
                      $filtro
                      AND t1.CREATED_DATETIME BETWEEN TO_DATE('$fecha_inicio_segunda 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                               AND TO_DATE('$fecha_fin_segunda 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
                GROUP BY E.TIENDA, E.CODIGO_VENDEDOR, E.NOMBRE, E.PUESTO, E.FECHA_INGRESO, A.SEMANA, A.META, A.TIPO
                ORDER BY DECODE(E.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4), A.SEMANA,E.FECHA_INGRESO";

            $consulta = consultaOracle(3, $query);
            $semanas_tendencia = rangoWe($fecha_inicio_segunda, $fecha_fin_segunda);
            $datosVendedores = array();
            ?>

            <div class="table-responsive">
                <table class="table table-hover table-sm table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Antigüedad</th>
                            <th>Código</th>
                            <th>Asesora</th>
                            <th>Puesto</th>
                            <?php foreach ($semanas_tendencia as $sem): ?>
                            <th><?php echo substr($sem, -4); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Procesar datos de vendedores
                        foreach ($consulta as $rtt) {
                            $clave = $rtt[1];
                            
                            if (!isset($datosVendedores[$clave])) {
                                $datosVendedores[$clave] = array(
                                    'antiguedad' => Antiguedad($rtt[4])[0] . " días",
                                    'codigo' => $rtt[1],
                                    'nombre' => ucwords(strtolower($rtt[2])),
                                    'puesto' => substr($rtt[3], 0, 3),
                                    'semanas' => array()
                                );
                            }
                            
                            $semana = (int)$rtt[5];
                            $estatus = $rtt[8];
                            
                            $datosVendedores[$clave]['semanas'][$semana] = $estatus;
                            
                            if (!isset($promediosPorTienda[$semana])) {
                                $promediosPorTienda[$semana] = array('ventas' => 0, 'metas' => 0);
                            }
                            
                            $ventas = $rtt[7];
                            $meta = $rtt[6];
                            
                            $promediosPorTienda[$semana]['ventas'] += $ventas;
                            $promediosPorTienda[$semana]['metas'] += $meta;
                        }
                        
                        foreach ($datosVendedores as $vendedor):
                        ?>
                        <tr>
                            <td><?php echo $vendedor['antiguedad']; ?></td>
                            <td><strong><?php echo $vendedor['codigo']; ?></strong></td>
                            <td><?php echo $vendedor['nombre']; ?></td>
                            <td><?php echo $vendedor['puesto']; ?></td>
                            <?php foreach ($semanas_tendencia as $yw): ?>
                                <?php $semana = (int)$yw; ?>
                                <td>
                                    <?php if (isset($vendedor['semanas'][$semana])): ?>
                                        <?php $estatus = $vendedor['semanas'][$semana]; ?>
                                        <span class="status-indicator <?php echo status($estatus); ?>" 
                                              style="<?php echo color($estatus); ?>">●</span>
                                        <?php echo $estatus . "%"; ?>
                                    <?php else: ?>
                                        <span class="fas fa-circle" style="color:#ffffff; font-size: 1em;">○</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr class="font-weight-bold" style="background-color: #48c9b0; color: #000;">
                            <td colspan="4"><strong>Promedio Tienda <?php echo $tienda_actual; ?></strong></td>
                            <?php foreach ($semanas_tendencia as $sem): ?>
                                <?php 
                                $semana = (int)$sem;
                                if (isset($promediosPorTienda[$semana])) {
                                    $ventas = $promediosPorTienda[$semana]['ventas'];
                                    $metas = $promediosPorTienda[$semana]['metas'];
                                    
                                    if ($metas > 0) {
                                        $promedio = round(($ventas / $metas) * 100, 0);
                                    } else {
                                        $promedio = 0;
                                    }
                                } else {
                                    $promedio = 0;
                                }
                                
                                $statusClass = status($promedio);
                                $colorStyle = color($promedio);
                                ?>
                                <td>
                                    <span class="status-indicator <?php echo $statusClass; ?>" 
                                          style="<?php echo $colorStyle; ?>">●</span>
                                    <?php echo $promedio . "%"; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php endforeach; ?>
    
</div>

<?php
// Si se solicitó PDF, generar y enviar
if (isset($_POST['export_pdf']) && $_POST['export_pdf'] == '1') {
    $contenidoHTML = ob_get_contents();
    ob_end_clean();
    
    // Agregar estilos adicionales para PDF
    $estilosPDF = '
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .container-fluid { padding: 0; margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 4px; text-align: center; }
        .table th { background-color: #007bff; color: white; font-weight: bold; }
        .section-header { background-color: #6c5ce7; color: white; padding: 10px; text-align: center; margin-bottom: 15px; }
        .store-group { border: 1px solid #ddd; margin-bottom: 20px; padding: 10px; }
        .status-indicator { font-size: 12px; }
        .no-print { display: none !important; }
        h1 { font-size: 16px; margin: 5px 0; }
        h2 { font-size: 14px; margin: 5px 0; }
        h3 { font-size: 12px; margin: 5px 0; }
        h4 { font-size: 11px; margin: 5px 0; }
    </style>';
    
    $contenidoCompleto = '<!DOCTYPE html><html><head><meta charset="UTF-8">' . $estilosPDF . '</head><body>' . $contenidoHTML . '</body></html>';
    
    // Usar Dompdf
    $dompdf = new Dompdf\Dompdf();
    $options = $dompdf->getOptions();
    $options->set(array('isRemoteEnabled' => true));
    $dompdf->setOptions($options);
    
    $dompdf->loadHtml($contenidoCompleto);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $nombreArchivo = 'reporte_ventas_' . date('Y-m-d_H-i-s') . '.pdf';
    $dompdf->stream($nombreArchivo, array("Attachment" => false));
    exit;
}
?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Configuración de DataTables para todas las tablas
    $('.table').each(function() {
        if (!$(this).hasClass('dataTable')) {
            $(this).DataTable({
                "searching": false,
                "paging": false,
                "ordering": false,
                "info": false,
                "responsive": true,
                "autoWidth": false,
                "scrollX": true,
                "language": {
                    "emptyTable": "No hay datos disponibles",
                    "zeroRecords": "No se encontraron registros"
                }
            });
        }
    });

    // Función para scroll suave entre secciones
    window.scrollToSection = function(sectionClass) {
        $('html, body').animate({
            scrollTop: $(sectionClass).offset().top - 20
        }, 800);
    };

    // Agregar botones de navegación
    if ($('.navigation-buttons').length === 0) {
        var navButtons = `
        <div class="navigation-buttons text-center mb-3 no-print" style="position: sticky; top: 0; z-index: 100; background: white; padding: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <button class="btn btn-info btn-sm mr-2" onclick="scrollToTop()">
                <i class="fas fa-arrow-up"></i> Inicio
            </button>
            <button class="btn btn-secondary btn-sm mr-2" onclick="toggleSections('detailed')">
                <i class="fas fa-chart-line"></i> Ver Solo Detallado
            </button>
            <button class="btn btn-warning btn-sm mr-2" onclick="toggleSections('trends')">
                <i class="fas fa-chart-area"></i> Ver Solo Tendencias
            </button>
            <button class="btn btn-success btn-sm" onclick="toggleSections('all')">
                <i class="fas fa-eye"></i> Ver Todo
            </button>
        </div>`;
        $('.sales-container').prepend(navButtons);
    }

    // Función para alternar vistas
    window.toggleSections = function(type) {
        switch(type) {
            case 'detailed':
                $('.store-group > div:first-child').show();
                $('.store-group > div:last-child').hide();
                break;
            case 'trends':
                $('.store-group > div:first-child').hide();
                $('.store-group > div:last-child').show();
                break;
            case 'all':
                $('.store-group > div').show();
                break;
        }
    };

    // Función para ir al inicio
    window.scrollToTop = function() {
        $('html, body').animate({scrollTop: 0}, 800);
    };

    // Mejorar la experiencia visual
    $('.status-indicator').hover(
        function() {
            $(this).css('transform', 'scale(1.2)');
        },
        function() {
            $(this).css('transform', 'scale(1)');
        }
    );

    // Tooltip para indicadores de estado
    $('.status-indicator').each(function() {
        var percentage = $(this).parent().text().trim();
        $(this).attr('title', 'Rendimiento: ' + percentage);
    });

    // Cargar script supervisor.js si existe
    var supervisorScript = "../Js/supervision/supervisor.js";
    $.getScript(supervisorScript).fail(function() {
        console.log("Script supervisor.js no encontrado, continuando sin él");
    });

    // Mensaje de confirmación para exportar PDF
    $('button[type="submit"]').click(function(e) {
        var form = $(this).closest('form');
        if (form.find('input[name="export_pdf"]').val() == '1') {
            if (!confirm('¿Está seguro de que desea exportar el reporte completo a PDF? Este proceso puede tardar varios minutos.')) {
                e.preventDefault();
            }
        }
    });

    // Indicador de carga para exportación PDF
    $('form').submit(function() {
        if ($(this).find('input[name="export_pdf"]').val() == '1') {
            $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Generando PDF...');
            $(this).find('button[type="submit"]').prop('disabled', true);
        }
    });

    // Funciones adicionales para mejorar UX
    
    // Resaltar filas al pasar el mouse
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );

    // Agregar indicadores de carga para tablas grandes
    $('.table-responsive').each(function() {
        var table = $(this).find('table');
        if (table.find('tbody tr').length > 50) {
            $(this).prepend('<div class="alert alert-info alert-dismissible fade show" role="alert"><i class="fas fa-info-circle"></i> Esta tabla contiene muchos registros. Use Ctrl+F para buscar datos específicos.<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>');
        }
    });

    // Función para imprimir sección específica
    window.printSection = function(sectionClass) {
        var printContent = $(sectionClass).html();
        var originalContent = $('body').html();
        
        $('body').html(`
            <div class="print-container">
                <style>
                    @media print {
                        .no-print { display: none !important; }
                        body { font-size: 12px; }
                        .table { font-size: 10px; }
                        .table th, .table td { padding: 2px !important; }
                    }
                </style>
                ${printContent}
            </div>
        `);
        
        window.print();
        $('body').html(originalContent);
        location.reload(); // Recargar para restaurar funcionalidad
    };

    // Función para exportar tabla específica a Excel
    window.exportTableToExcel = function(tableClass, filename) {
        var tables = $(tableClass);
        if (tables.length > 0) {
            var html = '<table>';
            tables.each(function() {
                html += $(this)[0].outerHTML;
            });
            html += '</table>';
            
            var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
            var downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            downloadLink.href = url;
            downloadLink.download = filename + '.xls';
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    };

    // Agregar controles adicionales de exportación si se desean
    var exportControls = `
    <div class="export-controls no-print mt-3 text-center">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-info btn-sm" onclick="printSection('.store-group')">
                <i class="fas fa-print"></i> Imprimir Todo
            </button>
            <button type="button" class="btn btn-outline-success btn-sm" onclick="exportTableToExcel('.table', 'reporte_ventas_excel')">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
        </div>
    </div>`;
    
    $('.container-fluid').append(exportControls);

    // Función para alternar modo compacto
    window.toggleCompactMode = function() {
        $('.table').toggleClass('table-sm');
        $('.container-fluid').toggleClass('compact-mode');
        
        if ($('.container-fluid').hasClass('compact-mode')) {
            $('.container-fluid').css('font-size', '11px');
        } else {
            $('.container-fluid').css('font-size', '');
        }
    };

    // Agregar botón de modo compacto
    $('.navigation-buttons').append(`
        <button class="btn btn-outline-dark btn-sm ml-2" onclick="toggleCompactMode()">
            <i class="fas fa-compress-arrows-alt"></i> Modo Compacto
        </button>
    `);

    console.log("Reporte de Ventas cargado exitosamente");
});

// Función para manejar errores de carga
window.onerror = function(msg, url, lineNo, columnNo, error) {
    console.log('Error: ' + msg + '\nURL: ' + url + '\nLínea: ' + lineNo + '\nColumna: ' + columnNo + '\nError: ' + JSON.stringify(error));
    return false;
};
</script>

</body>
</html>