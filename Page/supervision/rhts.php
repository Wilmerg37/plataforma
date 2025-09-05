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
$filtro = ($vacacionista == '1') ? '' : " AND EMP.EMPL_NAME < '5000'";

$semanas = rangoWY($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);
$resumen_extras_global = [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Horarios</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jsPDF y html2canvas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

         <link rel="stylesheet" href="../css/estilohorarios.css">
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h5>Generando reporte...</h5>
            <p class="mb-0 text-muted">Por favor espere</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header-section">
                <h1><i class="fas fa-chart-line"></i> Reporte de Horarios</h1>
                <p class="subtitle">Sistema de Gestión de Horarios y Control de Personal</p>
            </div>

            <!-- Leyenda de etiquetas -->
            <div class="legend-container">
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(158, 35, 240), rgb(138, 25, 220)); color: white;">GTO Presencial</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(87, 244, 250), rgb(67, 224, 230));">GTO Virtual</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(55, 118, 255), rgb(35, 98, 235)); color: white;">TV Presencial</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(82, 247, 90), rgb(62, 227, 70));">TV Virtual</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(252, 239, 62), rgb(232, 219, 42));">Reunión GTS</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(255, 124, 36), rgb(235, 104, 16)); color: white;">Reunión ASS</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(141, 69, 1), rgb(121, 49, 1)); color: white;">Inducción ROY</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(255, 104, 235), rgb(235, 84, 215));">Cumpleaños</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(148, 148, 148), rgb(128, 128, 128)); color: white;">Vacaciones</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(117, 71, 97), rgb(97, 51, 77)); color: white;">Cobertura</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(68, 119, 66), rgb(48, 99, 46)); color: white;">Suspensión LABORAL</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(64, 68, 151), rgb(44, 48, 131)); color: white;">Suspensión IGSS</div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(209, 133, 203), rgb(189, 113, 183));">Lactancia</div>
            </div>

            <?php
            foreach ($tiendas as $tienda) {
                foreach ($semanas as $semana) {
                    // Tu query PHP original aquí
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
                              ST.UDF1_STRING COD_SUP, ST.UDF2_STRING NOM_SUP , MV.META_S_IVA META,
                               HR.HORA_TOT_S , HR.HORA_EXTRA_S, HR.HORA_ALM_S, HR.HORA_LEY_S,  HR.ETIQUETA

                            FROM ROY_HORARIO_TDS HR
                            INNER JOIN ROY_VENDEDORES_FRIED V 
                                ON  HR.CODIGO_EMPL = V.CODIGO_VENDEDOR

                            INNER JOIN RPS.STORE ST 
                                ON V.TIENDA = ST.STORE_NO

                            INNER JOIN RPS.SUBSIDIARY SB 
                                ON V.SBS = SB.SBS_NO AND ST.SBS_SID = SB.SID

                            INNER JOIN ROY_META_DIARIA_TDS MV
                            ON HR.TIENDA = MV.TIENDA  AND TO_DATE(HR.FECHA, 'YYYY-MM-DD') = MV.FECHA   

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

                            WHERE EXTRACT(YEAR FROM TO_DATE(HR.FECHA, 'YYYY-MM-DD'))|| TO_CHAR(trunc(TO_DATE(HR.FECHA, 'YYYY-MM-DD'),'d'),'IW')+1 ='$semana'
                              AND HR.TIENDA = $tienda
                              AND V.SBS = $sbs

                            ORDER BY 
                                TO_DATE(HR.FECHA, 'YYYY-MM-DD'), 
                                DECODE(v.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4)";

                    $resultado = consultaOracle(3, $query);

                    $datos = [];
                    $fechas_unicas = [];
                    $metas_por_fecha = [];
                    $horas_totales = [];
                    $horas_extras = [];

                    foreach ($resultado as $rdst) {
                        $tienda_ = $rdst[0];
                        $codigo = $rdst[1];
                        $nombre = $rdst[2];
                        $puesto = $rdst[3];
                        $fecha = $rdst[5]; // DD/MM/YYYY
                        $entrada = $rdst[6];
                        $salida = $rdst[8];
                        $meta = $rdst[12];
                        $horas_totales[$codigo] = $rdst[13]; // HORA_TOT_S
                        $horas_extras[$codigo] = $rdst[14]; // HORA_EXTRA_S

                        // Evita sumar duplicado por empleado+semana
                        if (!isset($horas_extras_acumuladas)) $horas_extras_acumuladas = [];

                        $unique_key = $tienda . '|' . $semana . '|' . $codigo;

                        if (!isset($horas_extras_acumuladas[$unique_key])) {
                            $horas_extra = (float)$rdst[14];

                            if (!isset($resumen_extras_global[$tienda][$codigo])) {
                                $resumen_extras_global[$tienda][$codigo] = [
                                    'nombre' => $nombre,
                                    'horas' => 0
                                ];
                            }

                            $resumen_extras_global[$tienda][$codigo]['horas'] += $horas_extra;
                            $horas_extras_acumuladas[$unique_key] = true;
                        }

                        $horas_alm[$codigo] = $rdst[15]; // HORA_ALM_S
                        $horas_ley[$codigo] = $rdst[16]; // HORA_LEY_S
                        $clave = "$tienda_|$codigo|$nombre|$puesto";

                        if (!isset($datos[$clave])) {
                            $datos[$clave] = [];
                        }

                        $datos[$clave][$fecha] = [
                            'horario' => ($entrada === 'DESCANSO' || $salida === 'DESCANSO') ? 'DESCANSO' : "$entrada - $salida",
                            'etiqueta' => $rdst[17] // posición 17 es HR.ETIQUETA
                        ];

                        if (!in_array($fecha, $fechas_unicas)) {
                            $fechas_unicas[] = $fecha;
                        }

                        $metas_por_fecha[$fecha] = $meta;
                    }

                    usort($fechas_unicas, function($a, $b) {
                        $dateA = DateTime::createFromFormat('d/m/Y', $a);
                        $dateB = DateTime::createFromFormat('d/m/Y', $b);
                        return $dateA <=> $dateB;
                    });

                    // Días en español
                    $dias = [
                        'Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes',
                        'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves',
                        'Friday' => 'Viernes', 'Saturday' => 'Sábado'
                    ];

                    $meta_tienda = MTS($tienda, substr($semana, -2), substr($semana, 0, 4), $sbs)[0];
            ?>

            <!-- Stats Cards -->
            <div class="row stats-row g-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary-color), #3b82f6);">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-value" style="color: var(--primary-color);"><?php echo $tienda; ?></div>
                        <div class="stat-label">Tienda</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--info-color), #0891b2);">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div class="stat-value" style="color: var(--info-color);"><?php echo substr($semana, -2); ?></div>
                        <div class="stat-label">Semana <?php echo substr($semana, 0, 4); ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--success-color), #059669);">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div class="stat-value" style="color: var(--success-color);">Q <?php echo number_format($meta_tienda, 0); ?></div>
                        <div class="stat-label">Meta Tienda</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, var(--warning-color), #d97706);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value" style="color: var(--warning-color);"><?php echo count($datos); ?></div>
                        <div class="stat-label">Empleados</div>
                    </div>
                </div>
            </div>

            <!-- Tabla de horarios -->
            <div class="table-container" id="tabla-<?php echo $tienda; ?>-<?php echo substr($semana, -2); ?>">
                <div class="table-header">
                    <h4 class="table-title">
                        <i class="fas fa-table"></i> 
                        Horarios Tienda <?php echo $tienda; ?> - Semana <?php echo substr($semana, -2); ?>/<?php echo substr($semana, 0, 4); ?>
                    </h4>
                    <div class="export-buttons">
                        <button class="btn-export" onclick="exportarExcel('<?php echo $tienda; ?>', '<?php echo substr($semana, -2); ?>')">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                        <button class="btn-export" onclick="exportarPDF('<?php echo $tienda; ?>', '<?php echo substr($semana, -2); ?>')">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern tbavxv" id="table-<?php echo $tienda; ?>-<?php echo substr($semana, -2); ?>">
                        <thead>
                            <!-- Fila 1: Metas -->
                            <tr>
                                <th rowspan="4">Tienda</th>
                                <th rowspan="4">Código</th>
                                <th rowspan="4">Nombre</th>
                                <th rowspan="4">Puesto</th>                    

                                <?php foreach ($fechas_unicas as $fecha): ?>
                                <th class="celda-meta">Q <?php echo isset($metas_por_fecha[$fecha]) ? number_format($metas_por_fecha[$fecha], 2) : '-'; ?></th>
                                <?php endforeach; ?>

                                <th colspan="4" rowspan="3" class="borde-izquierdo-total">Total Horas</th>
                            </tr>

                            <!-- Fila 2: Fechas -->
                            <tr>
                                <?php foreach ($fechas_unicas as $fecha): ?>
                                <th class="celda-fecha"><?php echo DateTime::createFromFormat('d/m/Y', $fecha)->format('d/m'); ?></th>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Fila 3: Días -->
                            <tr>
                                <?php foreach ($fechas_unicas as $fecha): ?>
                                <th class="celda-fecha">
                                    <?php
                                    $fechaObj = DateTime::createFromFormat('d/m/Y', $fecha);
                                    echo $dias[$fechaObj->format('l')];
                                    ?>
                                </th>
                                <?php endforeach; ?>
                            </tr>

                            <!-- Fila 4: IN - OUT -->
                            <tr>
                                <?php foreach ($fechas_unicas as $fecha): ?>
                                    <th class="celda-inout">IN - OUT -- HR</th>
                                <?php endforeach; ?>
                                <th>Sem.</th>
                                <th>Ley</th>                    
                                <th>Alm.</th>
                                <th>Ext.</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $totales_s = $totales_ext = $totales_alm = $totales_ley = 0;
                            $descripciones_etiqueta = [
                                1 => 'GTO-Presencial',
                                2 => 'GTO-Virtual',
                                3 => 'TV Presencial',
                                4 => 'TV Virtual',
                                5 => 'Reunión GTS',
                                6 => 'Reunión ASS',
                                7 => 'Inducción ROY',
                                8 => 'Cumpleaños',
                                9 => 'Vacaciones',
                                10 => 'Cobertura',                          
                                11 => 'Suspensión LABORAL',
                                12 => 'Suspensión IGSS',
                                13 => 'Lactancia',
                            ];

                            foreach ($datos as $empleado => $horarios):
                                list($tienda_e, $codigo, $nombre, $puesto) = explode('|', $empleado);
                                $hs = isset($horas_totales[$codigo]) ? (float)$horas_totales[$codigo] : 0;
                                $he = isset($horas_extras[$codigo]) ? (float)$horas_extras[$codigo] : 0;
                                $ha = isset($horas_alm[$codigo]) ? (float)$horas_alm[$codigo] : 0;
                                $hl = isset($horas_ley[$codigo]) ? (float)$horas_ley[$codigo] : 0;

                                $totales_s += $hs;
                                $totales_ext += $he;
                                $totales_alm += $ha;
                                $totales_ley += $hl;
                            ?>
                            <tr>
                                <td><?php echo $tienda_e; ?></td>
                                <td><strong><?php echo $codigo; ?></strong></td>
                                <td><?php echo $nombre; ?></td>
                                <td><span class="badge bg-primary"><?php echo $puesto; ?></span></td>

                                <?php foreach ($fechas_unicas as $fecha): ?>
                                        <?php
                                        $celda = $horarios[$fecha] ?? ['horario' => '', 'etiqueta' => null];
                                        $horario = $celda['horario'];
                                        $etiqueta = (int)($celda['etiqueta'] ?? 0);
                                        $es_descanso = ($horario === 'DESCANSO');

                                        // Estilo base
                                        $clase = $es_descanso ? 'descanso' : '';

                                        // Estilos por etiqueta
                                        switch ($etiqueta) {
                                            case 1: $clase .= ' etiqueta-1'; break;
                                            case 2: $clase .= ' etiqueta-2'; break;
                                            case 3: $clase .= ' etiqueta-3'; break;
                                            case 4: $clase .= ' etiqueta-4'; break;
                                            case 5: $clase .= ' etiqueta-5'; break;
                                            case 6: $clase .= ' etiqueta-6'; break;
                                            case 7: $clase .= ' etiqueta-7'; break;
                                            case 8: $clase .= ' etiqueta-8'; break;
                                            case 9: $clase .= ' etiqueta-9'; break;
                                            case 10: $clase .= ' etiqueta-10'; break;
                                            case 11: $clase .= ' etiqueta-11'; break;
                                            case 12: $clase .= ' etiqueta-12'; break;
                                            case 13: $clase .= ' etiqueta-13'; break;
                                        }

                                        $tooltip = isset($descripciones_etiqueta[$etiqueta]) ? $descripciones_etiqueta[$etiqueta] : '';
                                        ?>
                                        <td 
                                            class="<?php echo trim($clase); ?> tooltip-custom" 
                                            data-tooltip="<?php echo $tooltip; ?>"
                                        >
                                            <?php
                                            if (!$es_descanso && strpos($horario, ' - ') !== false) {
                                                list($hora_in, $hora_out) = explode(' - ', $horario);
                                                $hora_in_ts = strtotime($hora_in);
                                                $hora_out_ts = strtotime($hora_out);

                                                if ($hora_out_ts < $hora_in_ts) {
                                                    $hora_out_ts += 24 * 3600;
                                                }

                                                $diferencia = $hora_out_ts - $hora_in_ts;
                                                $horas_diff = floor($diferencia / 3600);
                                                $minutos_diff = floor(($diferencia % 3600) / 60);

                                                echo "<div class='fw-bold'>$hora_in - $hora_out</div>";
                                                echo "<small>{$horas_diff}h</small>";
                                            } else {
                                                echo '<div class="fw-bold">DESCANSO</div>';
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>

                                <td><strong><?php echo number_format($hs, 0); ?></strong></td>
                                <td><?php echo number_format($hl, 0); ?></td>
                                <td><?php echo number_format($ha, 0); ?></td>                    
                                <td><strong><?php echo number_format($he, 0); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>

                            <!-- Totales por tienda -->
                            <tr class="total-row">
                                <td colspan="<?php echo 4 + count($fechas_unicas); ?>"><strong>Total general tienda:</strong></td>
                                <td><strong><?php echo number_format($totales_s, 0); ?></strong></td>
                                <td><strong><?php echo number_format($totales_ley, 0); ?></strong></td>
                                <td><strong><?php echo number_format($totales_alm, 0); ?></strong></td>                   
                                <td><strong><?php echo number_format($totales_ext, 0); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
                }
            }

            // Extrae los números de semana para mostrarlos en el título
            $semanas_texto = implode(', ', array_map(function($s) {
                return substr($s, -2); // Toma solo los últimos dos dígitos (número de semana)
            }, $semanas));
            ?>

           <!-- Resumen de horas extras -->
<?php foreach ($resumen_extras_global as $tienda => $empleados): ?>
<div class="summary-card">
    <h4 class="summary-title">
        <i class="fas fa-clock"></i>
        Resumen de Horas Extras – Semanas <?php echo $semanas_texto; ?> – Tienda <?php echo $tienda; ?>
    </h4>

    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th class="text-center">Horas Extras</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_extras_resumen = 0;
                foreach ($empleados as $codigo => $info):
                    $total_extras_resumen += $info['horas'];
                ?>
                <tr>
                    <td><strong><?php echo $codigo; ?></strong></td>
                    <td><?php echo $info['nombre']; ?></td>
                    <td class="text-center">
                        <span class="badge 
                            <?php 
                                if ($info['horas'] > 0) echo 'bg-warning text-dark'; 
                                elseif ($info['horas'] < 0) echo 'bg-danger'; 
                                else echo 'bg-secondary'; 
                            ?> fs-6">
                            <?php echo number_format($info['horas'], 0); ?>h
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="2"><strong>Total Horas Extras Tienda:</strong></td>
                    <td class="text-center">
                        <span class="badge bg-success fs-6">
                            <strong><?php echo number_format($total_extras_resumen, 0); ?>h</strong>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>


        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // Inicializar DataTables
        $(document).ready(function() {
            $('.tbavxv').each(function() {
                $(this).DataTable({
                    "searching": false,
                    "paging": false,
                    "ordering": false,
                    "info": false,
                    "responsive": true,
                    "autoWidth": false,
                    "scrollX": true
                });
            });
        });

        // Función para exportar a Excel
        function exportarExcel(tienda, semana) {
            try {
                showLoading();
                
                const wb = XLSX.utils.book_new();
                const tableId = `table-${tienda}-${semana}`;
                const table = document.getElementById(tableId);
                
                if (!table) {
                    throw new Error('Tabla no encontrada');
                }

                // Crear datos para Excel
                const ws_data = [];
                
                // Encabezado del reporte
                ws_data.push(['REPORTE DE HORARIOS']);
                ws_data.push([`Tienda: ${tienda}`]);
                ws_data.push([`Semana: ${semana}`]);
                ws_data.push([`Fecha de generación: ${new Date().toLocaleDateString()}`]);
                ws_data.push(['']);

                // Extraer datos de la tabla
                const rows = table.querySelectorAll('tr');
                
                rows.forEach((row, index) => {
                    const cells = row.querySelectorAll('th, td');
                    const rowData = [];
                    
                    cells.forEach(cell => {
                        // Limpiar el texto de la celda
                        let cellText = cell.innerText || cell.textContent || '';
                        cellText = cellText.replace(/\s+/g, ' ').trim();
                        rowData.push(cellText);
                    });
                    
                    if (rowData.length > 0) {
                        ws_data.push(rowData);
                    }
                });

                // Crear hoja de cálculo
                const ws = XLSX.utils.aoa_to_sheet(ws_data);
                
                // Configurar anchos de columna
                const colWidths = Array(ws_data[0] ? ws_data[0].length : 10).fill().map(() => ({wch: 15}));
                ws['!cols'] = colWidths;

                XLSX.utils.book_append_sheet(wb, ws, `Horarios_T${tienda}_S${semana}`);

                const filename = `Horarios_Tienda_${tienda}_Semana_${semana}_${new Date().toISOString().split('T')[0]}.xlsx`;
                XLSX.writeFile(wb, filename);

                hideLoading();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Exportación exitosa!',
                    text: `Archivo ${filename} descargado correctamente`,
                    confirmButtonColor: '#10b981'
                });

            } catch (error) {
                hideLoading();
                console.error('Error al exportar:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al exportar',
                    text: 'Hubo un problema al generar el archivo Excel',
                    confirmButtonColor: '#ef4444'
                });
            }
        }

        // Función para exportar a PDF
        function exportarPDF(tienda, semana) {
            try {
                showLoading();
                
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('l', 'mm', 'a4'); // Landscape orientation
                
                const tableContainer = document.getElementById(`tabla-${tienda}-${semana}`);
                
                if (!tableContainer) {
                    throw new Error('Container de tabla no encontrado');
                }

                // Configurar PDF
                pdf.setFontSize(16);
                pdf.text(`Reporte de Horarios - Tienda ${tienda} - Semana ${semana}`, 20, 20);
                
                pdf.setFontSize(10);
                pdf.text(`Fecha de generación: ${new Date().toLocaleDateString()}`, 20, 30);

                // Usar html2canvas para capturar la tabla
                html2canvas(tableContainer, {
                    scale: 1,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                }).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const imgWidth = 250; // Ancho en mm
                    const imgHeight = (canvas.height * imgWidth) / canvas.width;
                    
                    // Verificar si necesita múltiples páginas
                    let position = 40;
                    const pageHeight = 180; // Altura disponible por página
                    
                    if (imgHeight <= pageHeight) {
                        pdf.addImage(imgData, 'PNG', 20, position, imgWidth, imgHeight);
                    } else {
                        // Dividir en múltiples páginas si es necesario
                        let remainingHeight = imgHeight;
                        let currentY = 0;
                        
                        while (remainingHeight > 0) {
                            const currentHeight = Math.min(pageHeight, remainingHeight);
                            const cropCanvas = document.createElement('canvas');
                            const cropContext = cropCanvas.getContext('2d');
                            
                            cropCanvas.width = canvas.width;
                            cropCanvas.height = (currentHeight * canvas.height) / imgHeight;
                            
                            cropContext.drawImage(
                                canvas,
                                0, currentY * canvas.height / imgHeight,
                                canvas.width, cropCanvas.height,
                                0, 0,
                                canvas.width, cropCanvas.height
                            );
                            
                            const cropImgData = cropCanvas.toDataURL('image/png');
                            pdf.addImage(cropImgData, 'PNG', 20, position, imgWidth, currentHeight);
                            
                            remainingHeight -= currentHeight;
                            currentY += currentHeight;
                            
                            if (remainingHeight > 0) {
                                pdf.addPage();
                                position = 20;
                            }
                        }
                    }

                    const filename = `Horarios_Tienda_${tienda}_Semana_${semana}_${new Date().toISOString().split('T')[0]}.pdf`;
                    pdf.save(filename);

                    hideLoading();
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡PDF generado!',
                        text: `Archivo ${filename} descargado correctamente`,
                        confirmButtonColor: '#10b981'
                    });
                    
                }).catch(error => {
                    throw error;
                });

            } catch (error) {
                hideLoading();
                console.error('Error al generar PDF:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error al generar PDF',
                    text: 'Hubo un problema al crear el archivo PDF',
                    confirmButtonColor: '#ef4444'
                });
            }
        }

        // Inicializar tooltips
        $(document).ready(function() {
            $('[data-tooltip]').hover(
                function() {
                    // Mouse enter
                },
                function() {
                    // Mouse leave
                }
            );
        });
    </script>
</body>
</html>