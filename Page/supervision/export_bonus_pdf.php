<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

// Incluir DomPDF
require_once '../vendor/autoload.php'; // Ajusta la ruta según tu instalación
use Dompdf\Dompdf;
use Dompdf\Options;

// Obtener datos del POST
$tienda = $_POST['tienda'] ?? '';
$fi = $_POST['fi'] ?? '';
$ff = $_POST['ff'] ?? '';
$sbs = $_POST['sbs'] ?? '';
$iva = $_POST['iva'] ?? '';
$vacacionista = $_POST['vacacionista'] ?? '';
$filtro = $_POST['filtro'] ?? '';

// Configurar DomPDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

// Procesar datos para el PDF
$tiendas = explode(',', $tienda);
sort($tiendas);

// Generar HTML para el PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            color: #333;
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
        }
        
        .header p {
            color: #666;
            font-size: 11px;
            margin: 5px 0 0 0;
        }
        
        .table-section {
            margin-bottom: 30px;
        }
        
        .store-title {
            color: #333;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 4px 3px;
            text-align: center;
            font-size: 8px;
            vertical-align: top;
        }
        
        th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        .week-header {
            background-color: #667eea;
            color: white;
            font-weight: bold;
        }
        
        .summary-row {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            font-weight: bold;
        }
        
        .week-cell {
            min-width: 80px;
            padding: 3px 2px;
        }
        
        .week-info {
            display: block;
            line-height: 1.2;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 3px;
        }
        
        .status-excellent { background-color: #10b981; }
        .status-good { background-color: #22c55e; }
        .status-warning { background-color: #f59e0b; }
        .status-danger { background-color: #ef4444; }
        
        .percentage-display {
            font-weight: bold;
            font-size: 9px;
            color: #1f2937;
        }
        
        .meta-info, .venta-info {
            font-size: 7px;
            color: #666;
            margin: 1px 0;
        }
        
        .payment-info {
            font-weight: bold;
            color: #0369a1;
            font-size: 8px;
            background: #e0f2fe;
            padding: 1px 2px;
            border-radius: 2px;
            margin-top: 2px;
        }
        
        .accumulated-payment {
            background-color: #10b981;
            color: white;
            font-weight: bold;
            font-size: 9px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>';

foreach ($tiendas as $tie) {
    // Query original para cada tienda
    $query = "
     SELECT E.TIENDA, E.CODIGO_VENDEDOR, E.NOMBRE, E.PUESTO, E.FECHA_INGRESO CONTRATACION, A.SEMANA, 
           CASE WHEN A.TIPO = 'VACACIONISTA' THEN 0 ELSE ROUND(A.META, 2) END META,
            ROUND(NVL(sum(CASE 
                            WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
                            WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
                          END), 0), 2)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0)) VENTA,
            ROUND(NVL(sum(CASE 
                            WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
                            WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
                          END)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0)), 0) / A.META * 100, 0) PORCENTAJE
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
            AND t1.STORE_NO = $tie
            $filtro
            AND t1.CREATED_DATETIME BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                         AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
      GROUP BY E.TIENDA, E.CODIGO_VENDEDOR, E.NOMBRE, E.PUESTO, E.FECHA_INGRESO, A.SEMANA, A.META, A.TIPO
      ORDER BY DECODE(E.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4), A.SEMANA,E.FECHA_INGRESO";

    $consulta = consultaOracle(3, $query);
    $semanas = rangoWe($fi, $ff);
    
    // Procesar datos de vendedores (lógica original)
    $datosVendedores = [];
    
    foreach ($consulta as $rtt) {
        $clave = $rtt[1];
        
        if (!isset($datosVendedores[$clave])) {
            $datosVendedores[$clave] = [
                'antiguedad' => Antiguedad($rtt[4])[0] . " - días",
                'codigo' => $rtt[1],
                'nombre' => ucwords(strtolower($rtt[2])),
                'puesto' => substr($rtt[3], 0, 3),
                'semanas' => [],
                'ultimoPago' => 0,
                'semanasConsecutivas' => 0
            ];
        }

        $semana = (int)$rtt[5];
        $estatus = $rtt[8];
        $meta = $rtt[6];
        $venta = $rtt[7];

        $datosVendedores[$clave]['semanas'][$semana] = [
            'estatus' => $estatus,
            'meta' => $meta,
            'venta' => $venta
        ];
    }

    // Calcular promedios por semana (lógica original)
    $promediosPorSemana = [];
    $semanasConsecutivasTienda = 0;

    foreach ($semanas as $yw) {
        $semana = (int)$yw;
        $sumPorcentaje = 0;
        $count = 0;

        foreach ($datosVendedores as $vendedor) {
            if (isset($vendedor['semanas'][$semana])) {
                $sumPorcentaje += $vendedor['semanas'][$semana]['estatus'];
                $count++;
            }
        }

        $promedio = ($count > 0) ? round($sumPorcentaje / $count, 2) : 0;
        $promediosPorSemana[$semana] = $promedio;
    }

    // Generar HTML para esta tienda
    $html .= '
    <div class="table-section">
        <div class="header">
            <h1>REPORTE DE BONOS - TIENDA ' . $tie . '</h1>
            <p>Período: ' . date('d/m/Y', strtotime($fi)) . ' al ' . date('d/m/Y', strtotime($ff)) . '</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Antiguedad</th>
                    <th>Código</th>
                    <th>Asesora</th>
                    <th>Puesto</th>';
    
    foreach ($semanas as $sem) {
        $html .= '<th class="week-header">Semana</th>';
    }
    
    $html .= '<th>Pago Acumulado</th>
                </tr>
                <tr>
                    <th colspan="4"></th>';
    
    foreach ($semanas as $sem) {
        $html .= '<th class="week-header">' . substr($sem, -4) . '</th>';
    }
    
    $html .= '<th></th>
                </tr>
            </thead>
            <tbody>';

    $totalBonoTienda = 0;

    foreach ($datosVendedores as $vendedor) {
        $bonoAcumulado = 0;
        
        $html .= '<tr>
            <td>' . $vendedor['antiguedad'] . '</td>
            <td><strong>' . $vendedor['codigo'] . '</strong></td>
            <td>' . $vendedor['nombre'] . '</td>
            <td>' . $vendedor['puesto'] . '</td>';

        foreach ($semanas as $yw) {
            $semana = (int)$yw;
            $pagoSemana = 0;
            $estatus = 0;
            $meta = 0;
            $venta = 0;

            if (isset($vendedor['semanas'][$semana])) {
                $datosSemana = $vendedor['semanas'][$semana];
                $estatus = $datosSemana['estatus'];
                $meta = $datosSemana['meta'];
                $venta = $datosSemana['venta'];

                // Cálculos de pago (lógica original)
                if ($vendedor['puesto'] == 'JEF') {
                    $pagoSemana = calcularPagoPorSemana($estatus, $vendedor['puesto'], $vendedor['semanasConsecutivas']);
                    $promedioSemana = $promediosPorSemana[$semana] ?? 0;
                    $bonoTienda = calcularBonoPorTienda($promedioSemana, $semanasConsecutivasTienda, $estatus);
                    $pagoSemana += $bonoTienda;
                }

                if ($vendedor['puesto'] == 'SUB' || $vendedor['puesto'] == 'ASE') {
                    $pagoSemana = calcularPagoPorSemanaVend($estatus, $vendedor['puesto'], $vendedor['semanasConsecutivas']);
                }

                $bonoAcumulado += $pagoSemana;
            }

            // Determinar clase de estatus para el punto de color
            $statusClass = 'status-danger';
            if ($estatus >= 100) $statusClass = 'status-excellent';
            elseif ($estatus >= 80) $statusClass = 'status-good';
            elseif ($estatus >= 60) $statusClass = 'status-warning';

            $html .= '<td class="week-cell">
                <div class="week-info">
                    <div>
                        <span class="status-dot ' . $statusClass . '"></span>
                        <span class="percentage-display">' . $estatus . '%</span>
                    </div>
                    <div class="meta-info">Meta: Q' . number_format($meta, 2) . '</div>
                    <div class="venta-info">Venta: Q' . number_format($venta, 2) . '</div>
                    <div class="payment-info">Pago: ' . (($pagoSemana > 0) ? "Q" . number_format($pagoSemana, 2) : "Q0.00") . '</div>
                </div>
            </td>';
        }

        $html .= '<td class="accumulated-payment">Q' . number_format($bonoAcumulado, 2) . '</td>
        </tr>';
        
        $totalBonoTienda += $bonoAcumulado;
    }

    // Fila de resumen
    $html .= '<tr class="summary-row">
        <td colspan="4"><strong>Total por Tienda:</strong></td>';
    
    foreach ($semanas as $yw) {
        $semana = (int)$yw;
        $prom = $promediosPorSemana[$semana] ?? 0;
        
        $statusClass = 'status-danger';
        if ($prom >= 100) $statusClass = 'status-excellent';
        elseif ($prom >= 80) $statusClass = 'status-good';
        elseif ($prom >= 60) $statusClass = 'status-warning';
        
        $html .= '<td class="week-cell">
            <span class="status-dot ' . $statusClass . '"></span>
            <span class="percentage-display">' . number_format($prom, 0) . '%</span>
        </td>';
    }
    
    $html .= '<td class="accumulated-payment">Q' . number_format($totalBonoTienda, 2) . '</td>
    </tr>';

    $html .= '</tbody>
        </table>
    </div>';
}

$html .= '
    <div class="footer">
        <p>Reporte generado el ' . date('d/m/Y H:i:s') . ' | Sistema de Bonos</p>
    </div>
</body>
</html>';

// Cargar HTML en DomPDF
$dompdf->loadHtml($html);

// Configurar el papel y orientación
$dompdf->setPaper('A4', 'landscape');

// Renderizar el PDF
$dompdf->render();

// Nombre del archivo
$filename = "Reporte_Bonos_Tienda_" . str_replace(',', '_', $tienda) . "_" . date('Y-m-d') . ".pdf";

// Enviar el PDF al navegador
$dompdf->stream($filename, array("Attachment" => false));
?>