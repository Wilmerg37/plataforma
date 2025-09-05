<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$tienda = (isset($_POST['tienda'])) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];

$iva = (isset($_POST['iva'])) ? $_POST['iva'] : '';
$vacacionista = (isset($_POST['vacacionista'])) ? $_POST['vacacionista'] : '';
$filtro = '';

if ($vacacionista == '1') {
    $filtro = '';
} else {
    $filtro = " AND A.TIPO <> 'VACACIONISTA'";
}

$tiendas = explode(',', $tienda);
sort($tiendas);

// Manejo de exportación a Excel (lógica original)
if (isset($_POST['download_excel'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    $sheet->setCellValue('A1', 'Código');
    $sheet->setCellValue('B1', 'Asesora');
    $sheet->setCellValue('C1', 'Puesto');
    $sheet->setCellValue('D1', 'Pago Acumulado');

    $row = 2;

    foreach ($tiendas as $tie) {
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
        
        // Lógica de procesamiento original para Excel...
        foreach ($consulta as $rtt) {
            $codigo = $rtt[1];
            $nombre = ucwords(strtolower($rtt[2]));
            $puesto = substr($rtt[3], 0, 3);
            $bonoAcumulado = 0;

            $sheet->setCellValue("A$row", $codigo);
            $sheet->setCellValue("B$row", $nombre);
            $sheet->setCellValue("C$row", $puesto);
            $sheet->setCellValue("D$row", "Q" . number_format($bonoAcumulado, 2));
            $row++;
        }
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'Reporte_Tienda_' . date('Y-m-d') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Bonos</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

         <link rel="stylesheet" href="../css/estilobonoestr.css">
   
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h5>Procesando reporte...</h5>
            <p class="mb-0 text-muted">Por favor espere</p>
        </div>
    </div>

    <div class="main-container">
        <?php
        // Recorremos cada tienda seleccionada (lógica original)
        foreach ($tiendas as $tie) {
            // Consulta filtrada por cada tienda (query original)
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
        ?>
            <!-- Header del reporte -->
            <div class="report-header">
                <h1 class="store-title">
                    <i class="fas fa-store"></i>
                    Tienda no: <?php echo $tie; ?>
                </h1>
                <p class="date-subtitle">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo "( " . date('d/m/Y', strtotime($fi)) . " --al-- " . date('d/m/Y', strtotime($ff)) . " )" ?>
                </p>
            </div>

            <!-- Botones de exportación -->
            <div class="export-section">
                <form method="post" style="display: inline;">
                    <input type="hidden" name="download_excel" value="1">
                    <input type="hidden" name="tienda" value="<?php echo $tienda; ?>">
                    <input type="hidden" name="fecha" value="<?php echo $_POST['fecha'] ?? ''; ?>">
                    <input type="hidden" name="sbs" value="<?php echo $sbs; ?>">
                    <input type="hidden" name="iva" value="<?php echo $iva; ?>">
                    <input type="hidden" name="vacacionista" value="<?php echo $vacacionista; ?>">
                    <button type="submit" class="export-btn excel">
                        <i class="fas fa-file-excel"></i>
                        Exportar a Excel
                    </button>
                </form>
                <button class="export-btn pdf" onclick="exportToPDF('<?php echo $tie; ?>')">
                    <i class="fas fa-file-pdf"></i>
                    Exportar a PDF
                </button>
            </div>

            <!-- Tabla de bonos -->
            <div class="table-container">
                <div class="table-header">
                    <h4>
                        <i class="fas fa-coins"></i>
                        Reporte Detallado de Bonos por Vendedor
                    </h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern tbrtt" id="bonusTable_<?php echo $tie; ?>">
                        <thead>
                            <tr>
                                <td><i class="fas fa-clock"></i> Antiguedad</td>
                                <td><i class="fas fa-id-badge"></i> Código</td>
                                <td><i class="fas fa-user"></i> Asesora</td>
                                <td><i class="fas fa-briefcase"></i> Puesto</td>
                                <?php 
                                    $semanas = rangoWe($fi, $ff);
                                    foreach ($semanas as $sem) { 
                                ?>
                                <td class="week-cell"><i class="fas fa-calendar-week"></i> Semana</td>
                                <?php } ?> 
                                <td><i class="fas fa-wallet"></i> Pago Acumulado</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <?php 
                                    foreach ($semanas as $sem) { 
                                ?>
                                <td class="week-cell"><strong><?php echo substr($sem, -4); ?></strong></td>
                                <?php } ?> 
                                <td></td>
                            </tr>
                        </thead>

                        <?php
                            // Lógica original para procesar datos de vendedores
                            $datosVendedores = [];
                        ?>

                        <tbody>
                            <?php
                                // Procesamiento original de datos
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

                                // Cálculo de promedios (lógica original)
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

                                $totalBonoTienda = 0;

                                // Mostrar datos de vendedores
                                foreach ($datosVendedores as $vendedor) {
                                    $bonoAcumulado = 0;
                            ?>
                            <tr>
                                <td><?php echo $vendedor['antiguedad']; ?></td>
                                <td><strong><?php echo $vendedor['codigo']; ?></strong></td>
                                <td><?php echo $vendedor['nombre']; ?></td>
                                <td><span class="badge bg-primary"><?php echo $vendedor['puesto']; ?></span></td>

                                <?php
                                // Procesamiento de semanas (lógica original)
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

                                        // Cálculos de pago originales
                                        if ($vendedor['puesto'] == 'JEF') {
                                            $pagoSemana = calcularPagoPorSemana($estatus, $vendedor['puesto'], $vendedor['semanasConsecutivas']);
                                            $promedioSemana = $promediosPorSemana[$semana] ?? 0;
                                            $bonoTienda = calcularBonoPorTienda($promedioSemana, $semanasConsecutivasTienda, $estatus, $pagoSemana);
                                            $pagoSemana += $bonoTienda;
                                        }

                                        if ($vendedor['puesto'] == 'SUB' || $vendedor['puesto'] == 'ASE') {
                                            $pagoSemana = calcularPagoPorSemanaVend($estatus, $vendedor['puesto'], $vendedor['semanasConsecutivas']);
                                        }

                                        $bonoAcumulado += $pagoSemana;
                                    }
                                ?>

                                <td class="week-cell">
                                    <div class="week-info">
                                        <div>
                                            <span class="status-indicator <?php echo status($estatus); ?>" 
                                                  style="<?php echo color($estatus); ?>"></span>
                                            <span class="percentage-display"><?php echo $estatus . " %"; ?></span>
                                        </div>
                                        <div class="meta-info">Meta: Q<?php echo number_format($meta, 2); ?></div>
                                        <div class="venta-info">Venta: Q<?php echo number_format($venta, 2); ?></div>
                                        <div class="payment-info">
                                            Pago: <?php echo ($pagoSemana > 0) ? "Q" . number_format($pagoSemana, 2) : "Q0.00"; ?>
                                        </div>
                                    </div>
                                </td>
                                <?php } ?>

                                <td class="accumulated-payment">
                                    <strong>Q<?php echo number_format($bonoAcumulado, 2); ?></strong>
                                </td>
                            </tr>
                            <?php
                                $totalBonoTienda += $bonoAcumulado;
                                }
                            ?>
                            
                            <!-- Fila de resumen por tienda -->
                            <tr class="summary-row">
                                <td colspan="4" class="text-center">
                                    <strong><i class="fas fa-calculator"></i> Total por Tienda:</strong>
                                </td>
                                <?php foreach ($semanas as $yw): 
                                    $semana = (int)$yw;
                                    $prom = $promediosPorSemana[$semana] ?? 0;
                                ?>
                                <td class="week-cell">
                                    <div class="week-info">
                                        <span class="status-indicator <?php echo status($prom); ?>" 
                                              style="<?php echo color($prom); ?>"></span>
                                        <span class="percentage-display"><?php echo number_format($prom, 0) . " %"; ?></span>
                                    </div>
                                </td>
                                <?php endforeach; ?>
                                <td class="accumulated-payment">
                                    <strong>Q<?php echo number_format($totalBonoTienda, 2); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
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

        // Inicializar DataTables manteniendo configuración original
        $('.tbrtt').DataTable({
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": false,
            "responsive": true,
            "autoWidth": false,
            "scrollX": true
        });

        // Función para exportar a PDF usando DomPDF
        function exportToPDF(tienda) {
            try {
                showLoading();
                
                // Crear formulario temporal para enviar datos a PHP
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'export_bonus_pdf.php';
                form.target = '_blank';

                // Agregar datos necesarios
                const inputs = [
                    {name: 'tienda', value: tienda},
                    {name: 'fi', value: '<?php echo $fi; ?>'},
                    {name: 'ff', value: '<?php echo $ff; ?>'},
                    {name: 'sbs', value: '<?php echo $sbs; ?>'},
                    {name: 'iva', value: '<?php echo $iva; ?>'},
                    {name: 'vacacionista', value: '<?php echo $vacacionista; ?>'},
                    {name: 'filtro', value: '<?php echo $filtro; ?>'},
                    {name: 'html_content', value: document.querySelector('.main-container').outerHTML}
                ];

                inputs.forEach(input => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = input.name;
                    hiddenInput.value = input.value;
                    form.appendChild(hiddenInput);
                });

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);

                hideLoading();
                
                Swal.fire({
                    icon: 'info',
                    title: 'Generando PDF...',
                    text: 'El archivo se abrirá en una nueva pestaña',
                    timer: 2000,
                    showConfirmButton: false
                });

            } catch (error) {
                hideLoading();
                console.error('Error al exportar PDF:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al generar el PDF'
                });
            }
        }

        // Efectos visuales adicionales
        $(document).ready(function() {
            // Animación de entrada
            $('.main-container').hide().fadeIn(500);
            
            // Efecto hover mejorado en filas de la tabla
            $('.table-modern tbody tr').hover(
                function() {
                    if (!$(this).hasClass('summary-row')) {
                        $(this).find('td').css('background-color', '#f8fafc');
                    }
                },
                function() {
                    if (!$(this).hasClass('summary-row')) {
                        $(this).find('td').css('background-color', '');
                    }
                }
            );

            // Efecto en botones de exportación
            $('.export-btn').hover(
                function() {
                    $(this).find('i').addClass('fa-spin');
                },
                function() {
                    $(this).find('i').removeClass('fa-spin');
                }
            );

            hideLoading();
        });
    </script>

</body>
</html>