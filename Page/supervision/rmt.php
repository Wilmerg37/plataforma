<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

// Autoload para las exportaciones (solo si existe)
if (file_exists('../../vendor/autoload.php')) {
    require_once '../../vendor/autoload.php';
}

// Declaraciones use al inicio del archivo
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Manejar exportaciones
if (isset($_POST['action']) && $_POST['action'] === 'export_pdf') {
    if (!class_exists('Dompdf\Dompdf')) {
        die('Error: DomPDF no está instalado. Ejecute: composer require dompdf/dompdf');
    }
    
    $export_data = json_decode($_POST['export_data'], true);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $tiendas = $_POST['tiendas'] ?? '';
    
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    
    $dompdf = new Dompdf($options);
    
    $html = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Control de Horarios</title>
        <style>
            @page { margin: 15mm; size: A4 landscape; }
            body { font-family: Arial, sans-serif; font-size: 8px; margin: 0; padding: 0; }
            .header { text-align: center; background: #2c3e50; color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
            .header h1 { margin: 0; font-size: 18px; font-weight: bold; }
            .header p { margin: 5px 0 0 0; font-size: 12px; }
            .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 7px; }
            .table th { background: #2c3e50; color: white; padding: 6px 3px; text-align: center; font-weight: bold; border: 1px solid #ddd; font-size: 6px; }
            .table td { padding: 4px 3px; text-align: center; border: 1px solid #ddd; vertical-align: middle; }
            .table tbody tr:nth-child(even) { background-color: #f8f9fa; }
            .alerta-hora { background: #e74c3c !important; color: white !important; font-weight: bold; }
            .descanso { background: #95a5a6 !important; color: white !important; font-style: italic; }
            .store-title { background: #3498db; color: white; padding: 8px; text-align: center; font-weight: bold; margin: 15px 0 5px 0; border-radius: 5px; }
            .etiqueta-1 { background: rgb(158, 35, 240) !important; color: white !important; }
            .etiqueta-2 { background: rgb(87, 244, 250) !important; color: black !important; }
            .etiqueta-3 { background: rgb(55, 118, 255) !important; color: white !important; }
            .etiqueta-4 { background: rgb(82, 247, 90) !important; color: black !important; }
            .etiqueta-5 { background: rgb(252, 239, 62) !important; color: black !important; }
            .etiqueta-6 { background: rgb(255, 124, 36) !important; color: white !important; }
            .etiqueta-7 { background: rgb(141, 69, 1) !important; color: white !important; }
            .etiqueta-8 { background: rgb(255, 104, 235) !important; color: white !important; }
            .etiqueta-9 { background: rgb(148, 148, 148) !important; color: white !important; }
            .etiqueta-10 { background: rgb(117, 71, 97) !important; color: white !important; }
            .etiqueta-11 { background: rgb(68, 119, 66) !important; color: white !important; }
            .etiqueta-12 { background: rgb(64, 68, 151) !important; color: white !important; }
            .etiqueta-13 { background: rgb(209, 133, 203) !important; color: white !important; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>REPORTE DE CONTROL DE HORARIOS</h1>
            <p>Período del ' . date('d/m/Y', strtotime($fecha_inicio)) . ' al ' . date('d/m/Y', strtotime($fecha_fin)) . '</p>
            <p>Tiendas: ' . $tiendas . '</p>
            <p>Generado el: ' . date('d/m/Y H:i:s') . '</p>
        </div>';
    
    // Agrupar datos por tienda
    $datos_por_tienda = [];
    foreach ($export_data as $row) {
        $tienda = $row['tienda'];
        if (!isset($datos_por_tienda[$tienda])) {
            $datos_por_tienda[$tienda] = [];
        }
        $datos_por_tienda[$tienda][] = $row;
    }
    
    $primera_tienda = true;
    foreach ($datos_por_tienda as $tienda => $datos) {
        if (!$primera_tienda) {
            $html .= '<div style="page-break-before: always;"></div>';
        }
        
        $html .= '
        <div class="store-title">TIENDA: ' . $tienda . '</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 6%;">ID Reg</th>
                    <th style="width: 4%;">Tienda</th>
                    <th style="width: 5%;">Código</th>
                    <th style="width: 12%;">Nombre</th>
                    <th style="width: 8%;">Puesto</th>
                    <th style="width: 6%;">Día</th>
                    <th style="width: 7%;">Fecha</th>
                    <th style="width: 6%;">H.Ing</th>
                    <th style="width: 6%;">M.Ent</th>
                    <th style="width: 6%;">H.Sal</th>
                    <th style="width: 6%;">M.Sal</th>
                    <th style="width: 10%;">Justificación</th>
                    <th style="width: 7%;">F.Inicio</th>
                    <th style="width: 7%;">F.Final</th>
                    <th style="width: 5%;">H.Ini</th>
                    <th style="width: 5%;">H.Fin</th>
                    <th style="width: 8%;">F.Just</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($datos as $row) {
            $claseEntrada = '';
            $claseSalida = '';
            
            if ($row['marco_entrada'] != '00:00' && $row['hora_ingreso'] != 'DESCANSO') {
                $hora_ingreso = strtotime($row['hora_ingreso']);
                $marco_entrada = strtotime($row['marco_entrada']);
                if ($marco_entrada > $hora_ingreso) {
                    $claseEntrada = 'alerta-hora';
                }
            }
            
            if ($row['marco_salida'] != '00:00' && $row['hora_salida'] != 'DESCANSO') {
                $hora_salida = strtotime($row['hora_salida']);
                $marco_salida = strtotime($row['marco_salida']);
                if ($marco_salida < $hora_salida) {
                    $claseSalida = 'alerta-hora';
                }
            }
            
            if ($row['hora_ingreso'] == 'DESCANSO') {
                $claseEntrada = 'descanso';
            }
            if ($row['hora_salida'] == 'DESCANSO') {
                $claseSalida = 'descanso';
            }
            
            $html .= '
            <tr>
                <td>' . $row['no'] . '</td>
                <td>' . $row['id_registro'] . '</td>
                <td>' . $row['tienda'] . '</td>
                <td>' . $row['codigo'] . '</td>
                <td style="text-align: left; padding-left: 2px;">' . $row['nombre'] . '</td>
                <td>' . $row['puesto'] . '</td>
                <td>' . $row['dia'] . '</td>
                <td>' . $row['fecha'] . '</td>
                <td class="' . $claseEntrada . '">' . $row['hora_ingreso'] . '</td>
                <td class="' . $claseEntrada . '">' . $row['marco_entrada'] . '</td>
                <td class="' . $claseSalida . '">' . $row['hora_salida'] . '</td>
                <td class="' . $claseSalida . '">' . $row['marco_salida'] . '</td>
                <td style="text-align: left; padding-left: 2px;">' . ($row['justificacion'] ?: '-') . '</td>
                <td>' . ($row['fecha_inicio'] ?: '-') . '</td>
                <td>' . ($row['fecha_final'] ?: '-') . '</td>
                <td>' . ($row['hora_inicio'] ?: '-') . '</td>
                <td>' . ($row['hora_final'] ?: '-') . '</td>
                <td>' . ($row['fecha_just'] ?: '-') . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
        $primera_tienda = false;
    }
    
    $html .= '
        <div style="position: fixed; bottom: 10mm; left: 0; right: 0; text-align: center; font-size: 8px; color: #666;">
            <p>Reporte generado automáticamente - ' . date('d/m/Y H:i:s') . '</p>
        </div>
    </body>
    </html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    $filename = 'Reporte_Horarios_' . date('Y-m-d_H-i-s') . '.pdf';
    $dompdf->stream($filename, array('Attachment' => true));
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'export_excel') {
    if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        die('Error: PhpSpreadsheet no está instalado. Ejecute: composer require phpoffice/phpspreadsheet');
    }
    
    $export_data = json_decode($_POST['export_data'], true);
    $fecha_inicio = $_POST['fecha_inicio'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $tiendas = $_POST['tiendas'] ?? '';
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Configurar cabecera principal
    $sheet->setCellValue('A1', 'REPORTE DE CONTROL DE HORARIOS');
    $sheet->mergeCells('A1:R1');
    $sheet->getStyle('A1')->applyFromArray([
        'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
    ]);
    
    $sheet->setCellValue('A2', 'Período: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' al ' . date('d/m/Y', strtotime($fecha_fin)));
    $sheet->setCellValue('A3', 'Tiendas: ' . $tiendas);
    $sheet->setCellValue('A4', 'Generado: ' . date('d/m/Y H:i:s'));
    
    $sheet->mergeCells('A2:R2');
    $sheet->mergeCells('A3:R3');
    $sheet->mergeCells('A4:R4');
    
    $sheet->getStyle('A2:A4')->applyFromArray([
        'font' => ['bold' => true, 'size' => 11],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECF0F1']]
    ]);
    
    // Encabezados de la tabla
    $row = 6;
    $headers = [
        'A' => 'No', 'B' => 'ID Registro', 'C' => 'Tienda', 'D' => 'Código', 'E' => 'Nombre Empleado',
        'F' => 'Puesto', 'G' => 'Día', 'H' => 'Fecha', 'I' => 'Hora Ingreso', 'J' => 'Marco Entrada',
        'K' => 'Hora Salida', 'L' => 'Marco Salida', 'M' => 'Justificación', 'N' => 'Fecha Inicio',
        'O' => 'Fecha Final', 'P' => 'Hora Inicio', 'Q' => 'Hora Final', 'R' => 'Fecha Justificación'
    ];
    
    foreach ($headers as $col => $header) {
        $sheet->setCellValue($col . $row, $header);
    }
    
    $sheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C3E50']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
    ]);
    
    // Llenar datos
    $dataRow = $row + 1;
    foreach ($export_data as $index => $data) {
        $sheet->setCellValue('A' . $dataRow, $data['no']);
        $sheet->setCellValue('B' . $dataRow, $data['id_registro']);
        $sheet->setCellValue('C' . $dataRow, $data['tienda']);
        $sheet->setCellValue('D' . $dataRow, $data['codigo']);
        $sheet->setCellValue('E' . $dataRow, $data['nombre']);
        $sheet->setCellValue('F' . $dataRow, $data['puesto']);
        $sheet->setCellValue('G' . $dataRow, $data['dia']);
        $sheet->setCellValue('H' . $dataRow, $data['fecha']);
        $sheet->setCellValue('I' . $dataRow, $data['hora_ingreso']);
        $sheet->setCellValue('J' . $dataRow, $data['marco_entrada']);
        $sheet->setCellValue('K' . $dataRow, $data['hora_salida']);
        $sheet->setCellValue('L' . $dataRow, $data['marco_salida']);
        $sheet->setCellValue('M' . $dataRow, $data['justificacion'] ?: '');
        $sheet->setCellValue('N' . $dataRow, $data['fecha_inicio'] ?: '');
        $sheet->setCellValue('O' . $dataRow, $data['fecha_final'] ?: '');
        $sheet->setCellValue('P' . $dataRow, $data['hora_inicio'] ?: '');
        $sheet->setCellValue('Q' . $dataRow, $data['hora_final'] ?: '');
        $sheet->setCellValue('R' . $dataRow, $data['fecha_just'] ?: '');
        
        // Aplicar estilos condicionales
        $rowStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
        ];
        
        if ($index % 2 == 0) {
            $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']];
        }
        
        $sheet->getStyle('A' . $dataRow . ':R' . $dataRow)->applyFromArray($rowStyle);
        $dataRow++;
    }
    
    // Ajustar ancho de columnas
    $columnWidths = [
        'A' => 6, 'B' => 12, 'C' => 8, 'D' => 10, 'E' => 25, 'F' => 15, 'G' => 12, 'H' => 12,
        'I' => 12, 'J' => 12, 'K' => 12, 'L' => 12, 'M' => 20, 'N' => 12, 'O' => 12, 'P' => 12, 'Q' => 12, 'R' => 15
    ];
    
    foreach ($columnWidths as $col => $width) {
        $sheet->getColumnDimension($col)->setWidth($width);
    }
    
    $sheet->getRowDimension(1)->setRowHeight(30);
    $sheet->getRowDimension($row)->setRowHeight(25);
    $sheet->freezePane('A' . ($row + 1));
    $sheet->setAutoFilter('A' . $row . ':R' . ($dataRow - 1));
    
    $writer = new Xlsx($spreadsheet);
    
    $filename = 'Reporte_Horarios_' . date('Y-m-d_H-i-s') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

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

// Variables para exportación
$export_data = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Control de Horarios</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../css/estilosmarcaje.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="main-container">
    <!-- Header Section -->
    <div class="header-section">
        <h1 class="header-title">
            <i class="fas fa-clock me-3"></i>
            Control de Horarios
        </h1>
        <p class="header-subtitle">
            Reporte del <?php echo date('d/m/Y', strtotime($fi)); ?> al <?php echo date('d/m/Y', strtotime($ff)); ?>
        </p>
    </div>

    <!-- Controls Section -->
    <div class="controls-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="search-container">
                    <input type="text" id="globalSearch" class="search-input" placeholder="Buscar en toda la tabla...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="export-buttons justify-content-end d-flex">
                    <button class="export-btn btn-excel" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-2"></i>Excel
                    </button>
                    <button class="export-btn btn-pdf" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf me-2"></i>PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend-container">
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(158, 35, 240), rgb(138, 25, 220)); color: white;">GTO Presencial</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(87, 244, 250), rgb(67, 224, 230)); color: black;">GTO Virtual</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(55, 118, 255), rgb(35, 98, 235)); color: white;">TV Presencial</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(82, 247, 90), rgb(62, 227, 70)); color: black;">TV Virtual</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(252, 239, 62), rgb(232, 219, 42)); color: black;">Reunión GTS</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(255, 124, 36), rgb(235, 104, 16)); color: white;">Reunión ASS</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(141, 69, 1), rgb(121, 49, 1)); color: white;">Inducción ROY</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(255, 104, 235), rgb(235, 84, 215)); color: white;">Cumpleaños</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(148, 148, 148), rgb(128, 128, 128)); color: white;">Vacaciones</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(117, 71, 97), rgb(97, 51, 77)); color: white;">Cobertura</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(68, 119, 66), rgb(48, 99, 46)); color: white;">Suspensión LABORAL</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(64, 68, 151), rgb(44, 48, 131)); color: white;">Suspensión IGSS</div>
        <div class="legend-box" style="background: linear-gradient(135deg, rgb(209, 133, 203), rgb(189, 113, 183)); color: white;">Lactancia</div>
    </div>

    <!-- Content Section -->
    <div class="px-4 pb-4">
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
                $mt_prs = 0,
                $vta_prs = 0,
                $dif_prs = 0
            );

            $query = "SELECT 
                        HR.TIENDA, 
                        HR.CODIGO_EMPL, 
                        HR.NOMBRE_EMPL, 
                        V.PUESTO,
                        UPPER(HR.DIA) AS DIA,
                        TO_CHAR(TO_DATE(HR.FECHA, 'YYYY-MM-DD'), 'DD/MM/YYYY') AS FECHA,
                        
                        CASE 
                            WHEN HR.HORA_IN = '00:00' THEN 'DESCANSO' 
                            ELSE HR.HORA_IN 
                        END AS HORA_IN,
                      NVL( TO_CHAR(RG.ENTRADA, 'HH24:MI'),'00:00') AS ENTRADA,
                      CASE 
                            WHEN HR.HORA_OUT = '00:00' THEN 'DESCANSO' 
                            ELSE HR.HORA_OUT 
                        END AS HORA_OUT,
                      NVL( TO_CHAR(RG.SALIDA, 'HH24:MI'),'00:00') AS SALIDA,
                          ST.UDF1_STRING COD_SUP, ST.UDF2_STRING NOM_SUP , HR.ETIQUETA , HR.JUSTIFICACION, hr.id_registro ,HR.FECHA_INICIO, HR.FECHA_FIN, 
                          TO_CHAR(FECHA_JUSTIFICACION, 'DD/MM/YYYY HH24:MI:SS'), HR.HORA_JUS_IN, HR.HORA_JUS_OUT

                    FROM ROY_HORARIO_TDS HR
                    INNER JOIN ROY_VENDEDORES_FRIED V 
                        ON HR.TIENDA = V.TIENDA AND HR.CODIGO_EMPL = V.CODIGO_VENDEDOR

                    INNER JOIN RPS.STORE ST 
                        ON V.TIENDA = ST.STORE_NO

                    INNER JOIN RPS.SUBSIDIARY SB 
                        ON V.SBS = SB.SBS_NO AND ST.SBS_SID = SB.SID

                     LEFT JOIN (
                        SELECT 
                            TIENDA, 
                            CODIGO_EMPLEADO, 
                            TRUNC(FECHA) AS FECHA,
                            MIN(FECHA) AS ENTRADA,
                            MAX(FECHA) AS SALIDA
                        FROM ROY_HLL_REGISTRO_HUELLA
                        GROUP BY TIENDA, CODIGO_EMPLEADO, TRUNC(FECHA)
                    ) RG 
                        ON HR.TIENDA = RG.TIENDA 
                        AND HR.CODIGO_EMPL = RG.CODIGO_EMPLEADO 
                        AND TRUNC(TO_DATE(HR.FECHA, 'YYYY-MM-DD')) = RG.FECHA

                    WHERE TO_DATE(HR.FECHA, 'YYYY-MM-DD') 
                        BETWEEN TO_DATE('$fi', 'YYYY-MM-DD') 
                        AND TO_DATE('$ff', 'YYYY-MM-DD')
                        AND HR.TIENDA = $tienda
                        AND V.SBS = $sbs

                    ORDER BY 
                        HR.CODIGO_EMPL, 
                        TO_DATE(HR.FECHA, 'YYYY-MM-DD')";
            
            $resultado = consultaOracle(3, $query);
            $cnt = 1;
        ?>

        <div class="store-section">
            <div class="store-header">
                <h3 class="store-title">
                    <i class="fas fa-store me-3"></i>
                    Tienda: <?php echo $tienda; ?>
                </h3>
            </div>

            <div class="table-container">
                <table class="table table-striped table-hover professional-table" id="table_<?php echo $tienda; ?>">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Registro</th>
                            <th>Tienda</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Puesto</th>
                            <th>Día</th>
                            <th>Fecha</th>
                            <th>Hora Ingreso</th>
                            <th>Marco Entrada</th>
                            <th>Hora Salida</th>
                            <th>Marco Salida</th>
                            <th>Justificación</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Final</th>
                            <th>Hora Inicio</th>
                            <th>Hora Final</th>
                            <th>Fecha Just.</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($resultado as $rdst) {
                            $cnt++;

                            // Validación de HORA INGRESO y MARCO ENTRADA
                            $hora_ingreso = ($rdst[6] != 'DESCANSO') ? strtotime($rdst[6]) : false;
                            $marco_entrada = ($rdst[7] != '00:00') ? strtotime($rdst[7]) : false;

                            $claseEntrada = '';
                            if (!$marco_entrada || !$hora_ingreso) {
                                $claseEntrada = 'alerta-hora';
                            } elseif ($marco_entrada > $hora_ingreso) {
                                $claseEntrada = 'alerta-hora';
                            }

                            // Validación de HORA SALIDA y MARCO SALIDA
                            $hora_salida = ($rdst[8] != 'DESCANSO') ? strtotime($rdst[8]) : false;
                            $marco_salida = ($rdst[9] != '00:00') ? strtotime($rdst[9]) : false;

                            $claseSalida = '';
                            if (!$marco_salida || !$hora_salida) {
                                $claseSalida = 'alerta-hora';
                            } elseif ($marco_salida < $hora_salida) {
                                $claseSalida = 'alerta-hora';
                            }

                            // Preparar datos para exportación
                            $export_data[] = [
                                'no' => $cnt,
                                'id_registro' => $rdst[14],
                                'tienda' => $rdst[0],
                                'codigo' => $rdst[1],
                                'nombre' => $rdst[2],
                                'puesto' => $rdst[3],
                                'dia' => $rdst[4],
                                'fecha' => $rdst[5],
                                'hora_ingreso' => $rdst[6],
                                'marco_entrada' => $rdst[7],
                                'hora_salida' => $rdst[8],
                                'marco_salida' => $rdst[9],
                                'justificacion' => $rdst[13],
                                'fecha_inicio' => $rdst[15],
                                'fecha_final' => $rdst[16],
                                'hora_inicio' => $rdst[18],
                                'hora_final' => $rdst[19],
                                'fecha_just' => $rdst[17]
                            ];
                        ?>
                        <tr>
                            <td><?php echo $cnt; ?></td>
                            <td><?php echo $rdst[14]; ?></td>
                            <td><?php echo $rdst[0]; ?></td>
                            <td><?php echo $rdst[1]; ?></td>
                            <td><?php echo $rdst[2]; ?></td>
                            <td><?php echo $rdst[3]; ?></td>
                            <td><?php echo $rdst[4]; ?></td>
                            <td><?php echo $rdst[5]; ?></td>
                            
                            <?php
                            $etiquetaClase = !empty($rdst[18]) ? 'etiqueta-' . intval($rdst[12]) : '';
                            ?>
                            <td class="<?php echo $etiquetaClase; ?>"><?php echo $rdst[6]; ?></td>
                            <td class="<?php echo $claseEntrada . ' ' . $etiquetaClase; ?>"><?php echo $rdst[7]; ?></td>
                            <td class="<?php echo $etiquetaClase; ?>"><?php echo $rdst[8]; ?></td>
                            <td class="<?php echo $claseSalida . ' ' . $etiquetaClase; ?>"><?php echo $rdst[9]; ?></td>
                            
                            <td><?php echo $rdst[13]; ?></td>
                            <td><?php echo $rdst[15]; ?></td>
                            <td><?php echo $rdst[16]; ?></td>
                            <td><?php echo $rdst[18]; ?></td>
                            <td><?php echo $rdst[19]; ?></td>
                            <td><?php echo $rdst[17]; ?></td>
                            
                            <td>
                                <button class="action-btn btn-justify justificar-btn" 
                                        data-id="<?php echo $rdst[14]; ?>" 
                                        data-nombre="<?php echo $rdst[2]; ?>" 
                                        data-codigo="<?php echo $rdst[1]; ?>"
                                        data-fecha="<?php echo htmlspecialchars($rdst[5]); ?>"
                                        data-dia="<?php echo $rdst[4]; ?>"
                                        data-hora-in="<?php echo $rdst[6]; ?>"
                                        data-hora-out="<?php echo $rdst[8]; ?>"
                                        data-justificacion="<?php echo htmlspecialchars($rdst[13]); ?>">
                                    <i class="fas fa-edit me-1"></i>Justificar
                                </button>
                            </td>
                        </tr>
                        <?php
                        if ($rdst[3] === 'VACACIONISTA') {
                            $rdst[6] = 0;
                        }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        }
        ?>
    </div>
</div>

<!-- Modal Justificación Mejorado -->
<div class="modal fade" id="justificarModal" tabindex="-1" aria-labelledby="justificarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formJustificacion">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Justificación de Horario
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">           
                    <input type="hidden" name="id_registro" id="id_registro">
                    <input type="hidden" name="etiqueta" id="etiqueta">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Empleado
                                </label>
                                <input type="text" class="form-control" id="nombre_empleado" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-id-card me-2"></i>Código
                                </label>
                                <input type="text" class="form-control" id="codigo_empleado" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Fecha
                                </label>
                                <input type="text" class="form-control" id="fecha" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-sun me-2"></i>Día
                                </label>
                                <input type="text" class="form-control" id="dia" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock me-2"></i>Hora Ingreso
                                </label>
                                <input type="text" class="form-control" id="hora_in" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock me-2"></i>Hora Salida
                                </label>
                                <input type="text" class="form-control" id="hora_out" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-list me-2"></i>Seleccionar motivo
                        </label>
                        <select class="form-select" id="motivo_select">
                            <option value="">-- Seleccione un motivo --</option>
                            <option value="HOME OFFICE">HOME OFFICE</option>
                            <option value="GTO PRESENCIAL">GTO PRESENCIAL</option>
                            <option value="GTO VIRTUAL">GTO VIRTUAL</option>
                            <option value="TV PRESENCIAL">TV PRESENCIAL</option>
                            <option value="TV VIRTUAL">TV VIRTUAL</option>
                            <option value="REUNION GTS">REUNION GTS</option>
                            <option value="REUNION ASS">REUNION ASS</option>
                            <option value="INDUCCION ROY">INDUCCION ROY</option>
                            <option value="CUMPLEANOS">CUMPLEAÑOS</option>
                            <option value="VACACIONES">VACACIONES</option>
                            <option value="COBERTURA">COBERTURA</option>
                            <option value="SUSPENSION LABORAL">SUSPENSION LABORAL</option>
                            <option value="SUSPENSION IGSS">SUSPENSION IGSS</option>
                            <option value="LACTANCIA">LACTANCIA</option>
                            <option value="OTROS">OTROS</option>
                        </select>
                    </div>

                    <!-- Fechas de SUSPENSION -->
                    <div id="fechasSuspension" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-plus me-2"></i>Fecha Inicio
                                    </label>
                                    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar-minus me-2"></i>Fecha Fin
                                    </label>
                                    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Horas para GTO PRESENCIAL -->
                    <div id="horasGTO" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-clock me-2"></i>Hora Ingreso
                                    </label>
                                    <input type="time" class="form-control" name="gto_hora_ingreso" id="gto_hora_ingreso">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-clock me-2"></i>Hora Salida
                                    </label>
                                    <input type="time" class="form-control" name="gto_hora_salida" id="gto_hora_salida">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-comment me-2"></i>Justificación
                        </label>
                        <textarea class="form-control" name="justificacion" id="justificacion" rows="3" placeholder="Escriba la justificación aquí..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(document).ready(function() {
    // Configurar DataTables para cada tabla
    <?php foreach ($tiendas as $tienda): ?>
    $('#table_<?php echo $tienda; ?>').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 50,
        "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Horarios_Tienda_<?php echo $tienda; ?>_<?php echo date("Y-m-d"); ?>'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Horarios_Tienda_<?php echo $tienda; ?>_<?php echo date("Y-m-d"); ?>',
                orientation: 'landscape',
                pageSize: 'A4'
            }
        ],
        "columnDefs": [
            { "className": "text-center", "targets": [0, 1, 2, 3, 6, 7, 8, 9, 10, 11, 18] }
        ]
    });
    <?php endforeach; ?>

    // Buscador global mejorado
    $('#globalSearch').on('keyup', function() {
        var searchTerm = this.value;
        $('.professional-table').each(function() {
            $(this).DataTable().search(searchTerm).draw();
        });
        
        // Mostrar/ocultar botón de limpiar
        if (searchTerm.length > 0) {
            if (!$('#clearSearch').length) {
                $('.search-container').append('<button type="button" id="clearSearch" class="btn btn-outline-secondary btn-sm ms-2"><i class="fas fa-times"></i></button>');
            }
            $('#clearSearch').show();
        } else {
            $('#clearSearch').hide();
        }
    });

    // Limpiar búsqueda
    $(document).on('click', '#clearSearch', function() {
        $('#globalSearch').val('');
        $('.professional-table').each(function() {
            $(this).DataTable().search('').draw();
        });
        $(this).hide();
    });

    // Modal de justificación
    $(document).on('click', '.justificar-btn', function() {
        $('#id_registro').val($(this).data('id'));
        $('#nombre_empleado').val($(this).data('nombre'));
        $('#codigo_empleado').val($(this).data('codigo'));
        $('#fecha').val($(this).data('fecha'));
        $('#dia').val($(this).data('dia'));
        $('#hora_in').val($(this).data('hora-in'));
        $('#hora_out').val($(this).data('hora-out'));
        $('#justificacion').val($(this).data('justificacion'));

        // Resetear campos dinámicos
        $('#motivo_select').val('');
        $('#fechasSuspension').hide();
        $('#horasGTO').hide();
        $('#etiqueta').val('');

        $('#justificarModal').modal('show');
    });

    // Guardar justificación
    $('#formJustificacion').submit(function(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!$('#motivo_select').val()) {
            showAlert('warning', 'Debe seleccionar un motivo');
            return;
        }
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...').prop('disabled', true);

        $.ajax({
            url: '/roy/Page/supervision/guardar_justificacion.php',
            type: 'POST',
            data: $(this).serialize(),
            timeout: 10000,
            success: function(response) {
                showAlert('success', 'Justificación guardada correctamente');
                $('#justificarModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr, status, error) {
                let message = 'Error al guardar la justificación';
                if (status === 'timeout') {
                    message = 'La operación tardó demasiado tiempo. Inténtelo nuevamente.';
                }
                showAlert('error', message);
                console.error('Error:', error);
            },
            complete: function() {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Manejo del select de motivos
    $('#motivo_select').on('change', function() {
        var selected = $(this).val();
        const suspensionMotivos = ['SUSPENSION IGSS', 'SUSPENSION LABORAL', 'VACACIONES', 'CUMPLEANOS'];
        const horariosMotivos = ['GTO PRESENCIAL', 'GTO VIRTUAL', 'TV PRESENCIAL', 'TV VIRTUAL', 'REUNION GTS', 'REUNION ASS', 'INDUCCION ROY', 'LACTANCIA', 'COBERTURA'];

        // Mostrar/ocultar secciones con animación
        if (suspensionMotivos.includes(selected)) {
            $('#fechasSuspension').slideDown(300);
        } else {
            $('#fechasSuspension').slideUp(300);
            $('#fecha_inicio, #fecha_fin').val('');
        }

        if (horariosMotivos.includes(selected)) {
            $('#horasGTO').slideDown(300);
        } else {
            $('#horasGTO').slideUp(300);
            $('#gto_hora_ingreso, #gto_hora_salida').val('');
        }

        // Configurar justificación
        if (selected === 'OTROS' || selected === '') {
            $('#justificacion').val('').prop('readonly', false).attr('placeholder', 'Escriba la justificación aquí...');
        } else {
            $('#justificacion').val(selected).prop('readonly', true).attr('placeholder', '');
        }

        // Configurar etiqueta
        const etiquetas = {
            "GTO PRESENCIAL": 1, "GTO VIRTUAL": 2, "TV PRESENCIAL": 3, "TV VIRTUAL": 4,
            "REUNION GTS": 5, "REUNION ASS": 6, "INDUCCION ROY": 7, "CUMPLEANOS": 8,
            "VACACIONES": 9, "COBERTURA": 10, "SUSPENSION LABORAL": 11, "SUSPENSION IGSS": 12,
            "LACTANCIA": 13
        };

        $('#etiqueta').val(etiquetas[selected] || '');
    });

    // Validación de fechas
    $('#fecha_inicio, #fecha_fin').on('change', function() {
        const fechaInicio = $('#fecha_inicio').val();
        const fechaFin = $('#fecha_fin').val();
        
        if (fechaInicio && fechaFin && new Date(fechaInicio) > new Date(fechaFin)) {
            showAlert('warning', 'La fecha de inicio no puede ser mayor a la fecha fin');
            $('#fecha_fin').val('');
        }
    });

    // Validación de horas
    $('#gto_hora_ingreso, #gto_hora_salida').on('change', function() {
        const horaIngreso = $('#gto_hora_ingreso').val();
        const horaSalida = $('#gto_hora_salida').val();
        
        if (horaIngreso && horaSalida && horaIngreso >= horaSalida) {
            showAlert('warning', 'La hora de ingreso debe ser menor a la hora de salida');
            $('#gto_hora_salida').val('');
        }
    });
});

// Función para mostrar alertas mejoradas
function showAlert(type, message, duration = 5000) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger', 
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const icon = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-exclamation-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
    }[type] || 'fas fa-info-circle';
    
    const alertId = 'alert-' + Date.now();
    const alertHtml = `
        <div id="${alertId}" class="alert ${alertClass} alert-dismissible fade show shadow-lg position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 350px;" role="alert">
            <div class="d-flex align-items-center">
                <i class="${icon} me-2"></i>
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `;
    
    $('body').append(alertHtml);
    
    // Auto-remover
    setTimeout(() => {
        $('#' + alertId).fadeOut(300, function() {
            $(this).remove();
        });
    }, duration);
}

// Funciones de exportación integradas
function exportToExcel() {
    showAlert('info', 'Preparando exportación a Excel...');
    
    const form = $('<form>', {
        method: 'POST',
        action: window.location.href
    });
    
    form.append($('<input>', {type: 'hidden', name: 'action', value: 'export_excel'}));
    form.append($('<input>', {type: 'hidden', name: 'export_data', value: JSON.stringify(<?php echo json_encode($export_data); ?>)}));
    form.append($('<input>', {type: 'hidden', name: 'fecha_inicio', value: '<?php echo $fi; ?>'}));
    form.append($('<input>', {type: 'hidden', name: 'fecha_fin', value: '<?php echo $ff; ?>'}));
    form.append($('<input>', {type: 'hidden', name: 'tiendas', value: '<?php echo implode(",", $tiendas); ?>'}));
    
    $('body').append(form);
    form.submit();
    form.remove();
}

function exportToPDF() {
    showAlert('info', 'Generando archivo PDF...');
    
    const form = $('<form>', {
        method: 'POST',
        action: window.location.href
    });
    
    form.append($('<input>', {type: 'hidden', name: 'action', value: 'export_pdf'}));
    form.append($('<input>', {type: 'hidden', name: 'export_data', value: JSON.stringify(<?php echo json_encode($export_data); ?>)}));
    form.append($('<input>', {type: 'hidden', name: 'fecha_inicio', value: '<?php echo $fi; ?>'}));
    form.append($('<input>', {type: 'hidden', name: 'fecha_fin', value: '<?php echo $ff; ?>'}));
    form.append($('<input>', {type: 'hidden', name: 'tiendas', value: '<?php echo implode(",", $tiendas); ?>'}));
    
    $('body').append(form);
    form.submit();
    form.remove();
}

// Cargar script externo si existe
var url = "../Js/supervision/supervisor.js";
$.getScript(url).fail(function() {
    console.log('Script externo no encontrado, usando funcionalidad integrada');
});
</script>

</body>
</html>