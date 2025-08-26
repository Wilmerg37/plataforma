<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Asignación de Horarios</title>
    
    <!-- Bootstrap 5 para un diseño más moderno -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SheetJS para exportar a Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin: 20px auto;
            padding: 30px;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .header-section h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            border: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-custom {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }

        .btn-success-custom {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }

        .legend-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 20px 0;
            padding: 20px;
            background: var(--light-bg);
            border-radius: 15px;
            border: 2px dashed var(--secondary-color);
        }

        .legend-box {
            padding: 10px 15px;
            border-radius: 8px;
            color: #000;
            font-weight: 600;
            text-align: center;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .legend-box:hover {
            transform: scale(1.05);
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--card-shadow);
            border-left: 4px solid var(--success-color);
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success-color);
            margin-bottom: 5px;
        }

        .stats-label {
            color: var(--secondary-color);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            overflow-x: auto;
        }

        .table-modern {
            margin: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .table-modern thead th {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 15px 8px;
            border: none;
            text-align: center;
        }

        .table-modern tbody td {
            padding: 12px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
        }

        .table-modern tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.001);
            transition: all 0.2s ease;
        }

        .time-input {
            width: 100%;
            max-width: 80px;
            font-size: 0.8rem;
            padding: 6px 8px;
            margin: 2px 0;
            border: 1px solid #d1d5db;
            border-radius: 6px;
        }

        .etiqueta-select {
            width: 100%;
            font-size: 0.75rem;
            padding: 4px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin-top: 4px;
        }

        .horas-trabajadas {
            font-weight: 600;
            color: var(--primary-color);
            background: #eff6ff;
            padding: 4px 8px;
            border-radius: 6px;
            text-align: center;
            margin-top: 4px;
            font-size: 0.8rem;
        }

        .meta-row {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0) !important;
        }

        .total-row {
            background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
            font-weight: 600;
        }

        .floating-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 1000;
        }

        .floating-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        .floating-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.4);
        }

        .bg-orange {
            background: linear-gradient(135deg, #f97316, #ea580c) !important;
            color: white;
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
            }
            
            .header-section h1 {
                font-size: 1.8rem;
            }
            
            .legend-container {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
            
            .floating-buttons {
                bottom: 20px;
                right: 20px;
            }
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header-section">
                <h1><i class="fas fa-calendar-alt"></i> Sistema de Asignación de Horarios</h1>
                <p class="mb-0 mt-2 opacity-90">Gestión profesional de horarios y turnos</p>
            </div>

            <form id="form-horarios" method="POST">
                <!-- Formulario de selección -->
                <div class="form-card">
                    <h4 class="text-primary mb-4"><i class="fas fa-cogs"></i> Configuración Inicial</h4>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label" for="employee_code">
                                <i class="fas fa-user-tie"></i> Supervisor:
                            </label>
                            <select id="employee_code" name="employee_code" class="form-select" required>
                                <option value="" disabled selected>Seleccione un supervisor</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="store_no">
                                <i class="fas fa-store"></i> Tienda:
                            </label>
                            <select id="store_no" name="store_no" class="form-select" required>
                                <option value="" disabled selected>Seleccione una tienda</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="start-date">
                                <i class="fas fa-calendar-plus"></i> Fecha de Inicio (Domingo):
                            </label>
                            <input type="date" id="start-date" name="fecha" class="form-control" required>
                        </div>
                    </div>
                </div>

                <!-- Leyenda de colores -->
                <div class="form-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-palette"></i> Leyenda de Etiquetas</h5>
                    <div class="legend-container">
                        <div class="legend-box" style="background-color:rgb(158, 35, 240);">GTO Presencial</div>
                        <div class="legend-box" style="background-color:rgb(87, 244, 250);">GTO Virtual</div>
                        <div class="legend-box" style="background-color:rgb(55, 118, 255);">TV Presencial</div>
                        <div class="legend-box" style="background-color:rgb(82, 247, 90);">TV Virtual</div>
                        <div class="legend-box" style="background-color:rgb(252, 239, 62);">Reunión GTS</div>
                        <div class="legend-box" style="background-color:rgb(255, 124, 36);">Reunión ASS</div>
                        <div class="legend-box" style="background-color:rgb(141, 69, 1);">Inducción ROY</div>
                        <div class="legend-box" style="background-color:rgb(255, 104, 235);">Cumpleaños</div>
                        <div class="legend-box" style="background-color:rgb(148, 148, 148);">Vacaciones</div>
                        <div class="legend-box" style="background-color:rgb(117, 71, 97);">Cobertura</div>
                        <div class="legend-box" style="background-color:rgb(68, 119, 66);">Suspensión LABORAL</div>
                        <div class="legend-box" style="background-color:rgb(64, 68, 151);">Suspensión IGSS</div>
                        <div class="legend-box" style="background-color:rgb(209, 133, 203);">Lactancia</div>
                    </div>
                </div>

                <!-- Información de la semana y meta -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-value" id="numero_semana">0</div>
                            <div class="stats-label">Semana del Año</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-value" id="meta-semanal-total">Q. 0.00</div>
                            <div class="stats-label">Meta Semanal</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-value" id="valor-horas-e">--</div>
                            <div class="stats-label">Horas Extra Mínimas</div>
                        </div>
                    </div>
                </div>

                <!-- Campos ocultos -->
                <input type="hidden" id="semana" name="semana">
                <input type="hidden" name="fechas[dia_domingo]" id="fecha-dia-domingo">
                <input type="hidden" name="fechas[dia_lunes]" id="fecha-dia-lunes">
                <input type="hidden" name="fechas[dia_martes]" id="fecha-dia-martes">
                <input type="hidden" name="fechas[dia_miercoles]" id="fecha-dia-miercoles">
                <input type="hidden" name="fechas[dia_jueves]" id="fecha-dia-jueves">
                <input type="hidden" name="fechas[dia_viernes]" id="fecha-dia-viernes">
                <input type="hidden" name="fechas[dia_sabado]" id="fecha-dia-sabado">

                <!-- Tabla de empleados -->
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="text-primary mb-0"><i class="fas fa-users"></i> Empleados Asignados</h4>
                        <button type="button" class="btn btn-success btn-custom btn-success-custom" onclick="exportarAExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                    </div>
                    
                    <table id="empleadosTable" class="table table-modern">
                        <thead>
                            <!-- Fila de metas -->
                            <tr class="meta-row">
                                <td colspan="3" class="text-center fw-bold">Meta por día</td>
                                <td id="meta-domingo" class="text-center">-</td>
                                <td id="meta-lunes" class="text-center">-</td>
                                <td id="meta-martes" class="text-center">-</td>
                                <td id="meta-miercoles" class="text-center">-</td>
                                <td id="meta-jueves" class="text-center">-</td>
                                <td id="meta-viernes" class="text-center">-</td>
                                <td id="meta-sabado" class="text-center">-</td>
                                <td colspan="4" style="background-color:rgb(247, 139, 120); color: #000; text-align: center;">
                                    <strong>Horas extra para esta tienda:</strong> <span id="valor-horas-e-table">--</span>
                                </td>
                                <td colspan="2" style="background-color: #d2b48c; color: #000; text-align: center;">Meta</td>
                            </tr>

                            <tr>
                                <th>CÓDIGO</th>
                                <th>ASESORA</th>
                                <th>PUESTO</th>
                                <th class="bg-orange">DOMINGO <br><span id="fecha-domingo"></span></th>
                                <th class="bg-orange">LUNES <br> <span id="fecha-lunes"></span></th>
                                <th class="bg-orange">MARTES <br> <span id="fecha-martes"></span></th>
                                <th class="bg-orange">MIÉRCOLES <br> <span id="fecha-miercoles"></span></th>
                                <th class="bg-orange">JUEVES <br> <span id="fecha-jueves"></span></th>
                                <th class="bg-orange">VIERNES <br> <span id="fecha-viernes"></span></th>
                                <th class="bg-orange">SÁBADO <br> <span id="fecha-sabado"></span></th>
                                <th>SEM.</th>
                                <th>TOTAL<br>LEY</th>
                                <th>HORAS<br>ALM.</th>
                                <th>EXTR.</th>
                                <th style="background-color: #d2b48c; color: #000;">%</th>
                                <th style="background-color: #d2b48c; color: #000;">Q.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contenido dinámico -->
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3"><strong>Totales por día:</strong></td>
                                <td><strong id="total-domingo">0</strong></td>
                                <td><strong id="total-lunes">0</strong></td>
                                <td><strong id="total-martes">0</strong></td>
                                <td><strong id="total-miercoles">0</strong></td>
                                <td><strong id="total-jueves">0</strong></td>
                                <td><strong id="total-viernes">0</strong></td>
                                <td><strong id="total-sabado">0</strong></td>
                                <td colspan="6"></td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="10"><strong>Total General:</strong></td>
                                <td><strong id="total-horas-trabajadas">0</strong></td>
                                <td><strong id="total-ley"></strong></td>
                                <td><strong id="total-horas-almuerzo">0</strong></td>
                                <td><strong id="total-horas-extras">0</strong></td>
                                <td><strong>100%</strong></td>
                                <td><strong id="total-meta-individual">0</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Botón de guardar -->
                <div class="text-center mt-4">
                    <button id="btn-guardar" type="submit" class="btn btn-primary btn-custom btn-primary-custom">
                        <i class="fas fa-save"></i> Guardar Horarios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Botones flotantes -->
    <div class="floating-buttons">
        <button type="button" class="floating-btn" style="background: linear-gradient(135deg, #10b981, #059669);" onclick="calcularHorasTrabajadas()" title="Recalcular">
            <i class="fas fa-calculator"></i>
        </button>
        <button type="button" class="floating-btn" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);" onclick="limpiarFormulario()" title="Limpiar">
            <i class="fas fa-broom"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function limpiarFormulario() {
            Swal.fire({
                title: '¿Limpiar formulario?',
                text: 'Se perderán todos los datos ingresados',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#ef4444'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }

        function exportarAExcel() {
            try {
                showLoading();
                
                // Obtener datos básicos
                const supervisor = $('#employee_code option:selected').text();
                const tienda = $('#store_no').val();
                const fecha = $('#start-date').val();
                const semana = $('#numero_semana').text();
                
                // Crear workbook
                const wb = XLSX.utils.book_new();
                
                // Datos del encabezado
                const headerData = [
                    ['SISTEMA DE ASIGNACIÓN DE HORARIOS'],
                    [''],
                    ['Supervisor:', supervisor],
                    ['Tienda:', tienda],
                    ['Fecha de inicio:', fecha],
                    ['Semana del año:', semana],
                    ['Meta semanal:', $('#meta-semanal-total').text()],
                    ['Horas extra mínimas:', $('#valor-horas-e').text()],
                    ['']
                ];

                // Datos de metas por día
                const metasData = [
                    ['METAS POR DÍA'],
                    ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                    [
                        $('#meta-domingo').text(),
                        $('#meta-lunes').text(),
                        $('#meta-martes').text(),
                        $('#meta-miercoles').text(),
                        $('#meta-jueves').text(),
                        $('#meta-viernes').text(),
                        $('#meta-sabado').text()
                    ],
                    ['']
                ];

                // Encabezados de la tabla principal
                const tableHeaders = [
                    'CÓDIGO', 'ASESORA', 'PUESTO',
                    'DOMINGO', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO',
                    'SEM.', 'TOTAL LEY', 'HORAS ALM.', 'EXTR.', '%', 'Q.'
                ];

                // Datos de empleados
                const employeeData = [];
                employeeData.push(['HORARIOS DE EMPLEADOS']);
                employeeData.push(tableHeaders);

                $('#empleadosTable tbody tr').each(function() {
                    const row = [];
                    
                    // Datos básicos del empleado
                    row.push($(this).find('td:eq(0)').text().trim());
                    row.push($(this).find('td:eq(1)').text().trim());
                    row.push($(this).find('td:eq(2)').text().trim());
                    
                    // Horarios por día (combinar entrada, salida y etiqueta)
                    for (let i = 3; i <= 9; i++) {
                        const cell = $(this).find(`td:eq(${i})`);
                        const horaIn = cell.find('input[name*="hora_in"]').val() || '';
                        const horaOut = cell.find('input[name*="hora_out"]').val() || '';
                        const etiqueta = cell.find('select option:selected').text() || 'Sin etiqueta';
                        const horasTrabajadas = cell.find('.horas-trabajadas').text() || '0';
                        
                        row.push(`${horaIn}-${horaOut} (${etiqueta}) [${horasTrabajadas}h]`);
                    }
                    
                    // Totales
                    row.push($(this).find('.total-horas').val() || '0');
                    row.push($(this).find('input[name*="hora_ley_s"]').val() || '44');
                    row.push($(this).find('input[name*="hora_alm_s"]').val() || '5');
                    row.push($(this).find('.hora-extra').val() || '0');
                    row.push($(this).find('.meta-porcentaje').text() || '-');
                    row.push($(this).find('.meta-individual').text() || '-');
                    
                    employeeData.push(row);
                });

                // Totales
                employeeData.push(['']);
                employeeData.push([
                    'TOTALES POR DÍA', '', '',
                    $('#total-domingo').text(),
                    $('#total-lunes').text(),
                    $('#total-martes').text(),
                    $('#total-miercoles').text(),
                    $('#total-jueves').text(),
                    $('#total-viernes').text(),
                    $('#total-sabado').text(),
                    '', '', '', '', '', ''
                ]);
                
                employeeData.push([
                    'TOTAL GENERAL', '', '', '', '', '', '', '', '', '',
                    $('#total-horas-trabajadas').text(),
                    $('#total-ley').text(),
                    $('#total-horas-almuerzo').text(),
                    $('#total-horas-extras').text(),
                    '100%',
                    $('#total-meta-individual').text()
                ]);

                // Combinar todos los datos
                const allData = [...headerData, ...metasData, ...employeeData];

                // Crear hoja de cálculo
                const ws = XLSX.utils.aoa_to_sheet(allData);

                // Aplicar estilos (ancho de columnas)
                const colWidths = [
                    {wch: 15}, {wch: 25}, {wch: 15}, {wch: 20}, {wch: 20}, {wch: 20}, 
                    {wch: 20}, {wch: 20}, {wch: 20}, {wch: 20}, {wch: 10}, {wch: 12}, 
                    {wch: 12}, {wch: 10}, {wch: 8}, {wch: 15}
                ];
                ws['!cols'] = colWidths;

                // Agregar la hoja al workbook
                XLSX.utils.book_append_sheet(wb, ws, 'Horarios');

                // Generar nombre del archivo
                const nombreArchivo = `Horarios_Tienda${tienda}_Semana${semana}_${new Date().toISOString().split('T')[0]}.xlsx`;

                // Descargar
                XLSX.writeFile(wb, nombreArchivo);

                hideLoading();
                
                Swal.fire({
                    icon: 'success',
                    title: '¡Exportación exitosa!',
                    text: `El archivo ${nombreArchivo} se ha descargado correctamente`,
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

        $(document).ready(function() {
            // Cargar supervisores al cargar la página
            showLoading();
            $.ajax({
                url: 'insert_hours.php?action=get_supervisors',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var supervisorSelect = $('#employee_code');
                    supervisorSelect.empty();
                    supervisorSelect.append('<option value="" disabled selected>Seleccione un supervisor</option>');
                    data.forEach(function(supervisor) {
                        supervisorSelect.append('<option value="' + supervisor.SUPERVISOR_ID + '">' + supervisor.SUPERVISOR_ID + ' - ' + supervisor.SUPERVISOR_NAME + '</option>');
                    });
                    hideLoading();
                },
                error: function() {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los supervisores',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });

            // Cargar tiendas al seleccionar un supervisor
            $('#employee_code').change(function() {
                var supervisorId = $(this).val();
                showLoading();
                $.ajax({
                    url: 'insert_hours.php?action=get_stores',
                    type: 'GET',
                    data: { supervisor_id: supervisorId },
                    dataType: 'json',
                    success: function(data) {
                        var storeSelect = $('#store_no');
                        storeSelect.empty();
                        storeSelect.append('<option value="" disabled selected>Seleccione una tienda</option>');
                        data.forEach(function(store) {
                            storeSelect.append('<option value="' + store.STORE_NO + '"> ' + store.STORE_NO + ' </option>');
                        });
                        hideLoading();
                    },
                    error: function() {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar las tiendas',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });

            // Validar cuando se selecciona tienda o fecha
            $('#store_no, #start-date').change(function () {
                const tienda = $('#store_no').val();
                const fecha = $('#start-date').val();

                $('#btn-guardar').prop('disabled', false);

                if (tienda && fecha) {
                    validarHorariosAsignados(tienda, fecha);
                }
            });

            // Cargar empleados al seleccionar una tienda
            $('#store_no').change(function() {
                var storeNo = $(this).val();
                if (!storeNo) return;
                
                showLoading();
                $.ajax({
                    url: 'insert_hours.php?action=get_employees',
                    type: 'GET',
                    data: { store_no: storeNo },
                    dataType: 'json',
                    success: function(data) {
                        var employeeTable = $('#empleadosTable tbody');
                        employeeTable.empty();

                        if (data.length === 0) {
                            employeeTable.append('<tr><td colspan="16" class="text-center">No se encontraron empleados para esta tienda.</td></tr>');
                            hideLoading();
                            return;
                        }

                        // Obtener las HORAS_E
                        $.ajax({
                            url: 'insert_hours.php',
                            type: 'GET',
                            data: {
                                action: 'get_horas_e',
                                store_no: storeNo
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data && data.HORAS_E !== null) {
                                    $('#valor-horas-e').text(data.HORAS_E);
                                    $('#valor-horas-e-table').text(data.HORAS_E);
                                } else {
                                    $('#valor-horas-e').text('--');
                                    $('#valor-horas-e-table').text('--');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error al obtener HORAS_E:', error);
                                $('#valor-horas-e').text('--');
                                $('#valor-horas-e-table').text('--');
                            }
                        });

                        data.forEach(function(employee, index) {
                            var dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

                            var employeeRow = `
                                <tr>
                                    <td>${employee.EMPL_NAME}<input type="hidden" name="employees[${index}][codigo_emp]" value="${employee.EMPL_NAME}"></td>
                                    <td style="max-width: 200px; word-wrap: break-word;">${employee.FULL_NAME}<input type="hidden" name="employees[${index}][nombre_emp]" value="${employee.FULL_NAME}"></td>
                                    <td>${employee.PUESTO}<input type="hidden" name="employees[${index}][puesto]" value="${employee.PUESTO}"></td>`;

                            // Generar celdas para cada día
                            dias.forEach(function(dia) {
                                employeeRow += generarCeldaDia(index, dia);
                            });

                            // Agregar el resto de columnas
                            employeeRow += `
                                <td><input type="text" class="form-control total-horas time-input" name="employees[${index}][hora_tot_s]" placeholder="Total" readonly></td>
                                <td><input type="text" class="form-control time-input" name="employees[${index}][hora_ley_s]" value="44" placeholder="44"></td>
                                <td><input type="text" class="form-control time-input" name="employees[${index}][hora_alm_s]" value="5" placeholder="5"></td>
                                <td><input type="text" class="form-control hora-extra time-input" name="employees[${index}][hora_extra_s]" placeholder="Extra" readonly></td>
                                <td class="meta-porcentaje text-center"></td>
                                <td class="meta-individual text-center"></td>
                            </tr>`;

                            employeeTable.append(employeeRow);
                        });

                        // Aplicar estilos a los selects
                        $('.etiqueta-select').each(function() {
                            cambiarColorSelect(this);
                        });

                        calcularHorasTrabajadas();
                        actualizarMetaPorAsesor();
                        hideLoading();
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        console.error('Error al cargar empleados:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron cargar los empleados',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });

            // Envío del formulario
            $('#form-horarios').submit(function(event) {
                event.preventDefault();
                
                showLoading();
                var formData = $(this).serialize();

                $.ajax({
                    url: 'insert_hours.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        hideLoading();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Horarios guardados correctamente',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        console.error('Error al insertar horas:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudieron guardar los horarios',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });

            // Detectar cambios en las horas y recalcular
            $(document).on('change', '.hora-input', function() {
                let value = $(this).val().trim();
                
                // Auto-formato de horas
                if (/^\d{1,2}$/.test(value) && parseInt(value) >= 0 && parseInt(value) <= 23) {
                    $(this).val(value.padStart(2, '0') + ":00");
                }
                
                calcularHorasTrabajadas();
            });

            // Detectar cambios en horas de almuerzo y ley
            $(document).on('change', 'input[name*="[hora_alm_s]"], input[name*="[hora_ley_s]"]', function() {
                calcularHorasTrabajadas();
            });

            // Cambiar color del select según etiqueta
            $(document).on('change', '.etiqueta-select', function() {
                cambiarColorSelect(this);
            });

            // Actualizar fechas y semana
            $('#start-date').on('change', function() {
                actualizarFechasYSemana();
                obtenerMetasPorTiendaYFecha();
                actualizarMetaPorAsesor();
            });

            // Actualizar cuando cambie la tienda
            $('#store_no').on('change', function () {
                if ($('#start-date').val()) {
                    actualizarFechasYSemana();
                    obtenerMetasPorTiendaYFecha();
                    actualizarMetaPorAsesor();
                }
            });
        });

        function generarCeldaDia(index, dia) {
            return `
                <td style="min-width: 120px;">
                    <input type="text" class="form-control hora-input time-input" name="employees[${index}][${dia}][hora_in]" placeholder="HH:MM" value="09:00">
                    <input type="text" class="form-control hora-input time-input" name="employees[${index}][${dia}][hora_out]" placeholder="HH:MM" value="20:00">
                    <select name="employees[${index}][${dia}][etiqueta]" class="form-control etiqueta-select" onchange="cambiarColorSelect(this)">
                        <option value="0">Sin etiqueta</option>
                        <option value="1" style="background-color:rgb(158, 35, 240); color:white;">GTO Presencial</option>
                        <option value="2" style="background-color:rgb(87, 244, 250);">GTO Virtual</option>
                        <option value="3" style="background-color:rgb(55, 118, 255); color:white;">TV Presencial</option>
                        <option value="4" style="background-color:rgb(82, 247, 90);">TV Virtual</option>
                        <option value="5" style="background-color:rgb(252, 239, 62);">Reunión GTS</option>
                        <option value="6" style="background-color:rgb(255, 124, 36);">Reunión ASS</option>
                        <option value="7" style="background-color:rgb(141, 69, 1); color:white;">Inducción ROY</option>
                        <option value="8" style="background-color:rgb(255, 104, 235);">Cumpleaños</option>
                        <option value="9" style="background-color:rgb(148, 148, 148); color:white;">Vacaciones</option>
                        <option value="10" style="background-color:rgb(117, 71, 97); color:white;">Cobertura</option>
                        <option value="11" style="background-color:rgb(68, 119, 66); color:white;">Suspensión LABORAL</option>
                        <option value="12" style="background-color:rgb(64, 68, 151); color:white;">Suspensión IGSS</option>
                        <option value="13" style="background-color:rgb(209, 133, 203);">Lactancia</option>
                    </select>
                    <div class="horas-trabajadas" data-dia="${dia}">0</div>
                </td>`;
        }

        function cambiarColorSelect(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const backgroundColor = selectedOption.style.backgroundColor;
            const color = selectedOption.style.color || '#000';
            
            if (backgroundColor) {
                selectElement.style.backgroundColor = backgroundColor;
                selectElement.style.color = color;
                selectElement.style.fontWeight = 'bold';
            } else {
                selectElement.style.backgroundColor = '';
                selectElement.style.color = '';
                selectElement.style.fontWeight = '';
            }
        }

        function actualizarFechasYSemana() {
            var fechaInput = $('#start-date').val();
            if (!fechaInput) return;
            
            var partes = fechaInput.split('-');
            var fechaSeleccionada = new Date(partes[0], partes[1] - 1, partes[2]);

            var numeroSemana = getWeekNumber(fechaSeleccionada);
            $('#semana').val(numeroSemana);
            $('#numero_semana').text(numeroSemana);

            var diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

            for (var i = 0; i < diasSemana.length; i++) {
                var fechaDia = new Date(fechaSeleccionada);
                fechaDia.setDate(fechaSeleccionada.getDate() + i);

                var dia = ('0' + fechaDia.getDate()).slice(-2);
                var mes = ('0' + (fechaDia.getMonth() + 1)).slice(-2);
                var anio = fechaDia.getFullYear();

                var fechaFormateada = `${dia}-${mes}-${anio}`;
                var fechaISO = `${anio}-${mes}-${dia}`;

                $('#fecha-' + diasSemana[i]).text(fechaFormateada);
                $('#fecha-dia-' + diasSemana[i]).val(fechaISO);
            }
        }

        function obtenerMetasPorTiendaYFecha() {
            var storeNo = $('#store_no').val();
            var fechaInput = $('#start-date').val();

            if (!storeNo || !fechaInput) {
                return;
            }

            $.ajax({
                url: 'insert_hours.php',
                method: 'GET',
                data: {
                    action: 'get_goals',
                    tienda: storeNo,
                    fecha: fechaInput
                },
                success: function(response) {
                    var dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
                    let sumaMetas = 0;

                    dias.forEach(function(dia) {
                        if (response.hasOwnProperty(dia)) {
                            const valor = parseFloat(response[dia]);
                            if (!isNaN(valor)) {
                                sumaMetas += valor;
                                $('#meta-' + dia).text('Q. ' + valor.toLocaleString('es-GT', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }));
                            } else {
                                $('#meta-' + dia).text('-');
                            }
                        } else {
                            $('#meta-' + dia).text('-');
                        }
                    });

                    $('#meta-semanal-total').text('Q. ' + sumaMetas.toLocaleString('es-GT', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    actualizarMetaPorAsesor();
                },
                error: function(xhr, status, error) {
                    console.error('Error al obtener las metas:', error);
                }
            });
        }

        function actualizarMetaPorAsesor() {
            var totalMetaSemanal = 0;
            var metaTexto = $('#meta-semanal-total').text().replace('Q. ', '').replaceAll(',', '').trim();

            if (metaTexto !== "") {
                totalMetaSemanal = parseFloat(metaTexto);
            }

            var filasAsesores = $('#empleadosTable tbody tr');
            var cantidadAsesores = filasAsesores.length;

            if (cantidadAsesores === 0 || isNaN(totalMetaSemanal)) {
                $('#total-meta-individual').text('Q. 0.00');
                filasAsesores.each(function () {
                    $(this).find('.meta-porcentaje').text('-');
                    $(this).find('.meta-individual').text('-');
                });
                return;
            }

            var metaIndividual = totalMetaSemanal / cantidadAsesores;
            var porcentaje = (100 / cantidadAsesores).toFixed(2);

            filasAsesores.each(function () {
                $(this).find('.meta-porcentaje').text(`${porcentaje}%`);
                $(this).find('.meta-individual').text('Q. ' + metaIndividual.toLocaleString('es-GT', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            });

            $('#total-meta-individual').text('Q. ' + totalMetaSemanal.toLocaleString('es-GT', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }

        function getWeekNumber(date) {
            var firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            var pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        function convertirHoraAMinutos(hora) {
            if (!hora || hora === "") return 0;
            var partes = hora.split(':');
            return parseInt(partes[0]) * 60 + parseInt(partes[1]);
        }

        function convertirMinutosAHoras(minutos) {
            if (isNaN(minutos) || minutos < 0) return "00:00";
            var horas = Math.floor(minutos / 60);
            var mins = minutos % 60;
            return horas.toString().padStart(2, '0') + ':' + mins.toString().padStart(2, '0');
        }

        function validarHorariosAsignados(tienda, fecha) {
            fetch(`insert_hours.php?action=check_schedule&store_no=${tienda}&fecha=${fecha}`)
                .then(response => response.json())
                .then(data => {
                    if (data.total > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: '¡Atención!',
                            html: '<span style="color: red; font-weight: bold;">ESTA TIENDA YA TIENE HORARIOS ASIGNADOS PARA ESTA SEMANA.</span>',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#f59e0b'
                        });
                        document.getElementById('btn-guardar').disabled = true;
                    } else {
                        document.getElementById('btn-guardar').disabled = false;
                    }
                })
                .catch(error => {
                    console.error("Error al verificar horarios:", error);
                    document.getElementById('btn-guardar').disabled = false;
                });
        }

        function calcularHorasTrabajadas() {
            var totalPorDia = {
                domingo: 0,
                lunes: 0,
                martes: 0,
                miercoles: 0,
                jueves: 0,
                viernes: 0,
                sabado: 0
            };

            var totalHorasTrabajadas = 0;
            var totalHorasExtras = 0;
            var totalHorasAlmuerzo = 0;

            $('#empleadosTable tbody tr').each(function() {
                var totalEmpleado = 0;
                var dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

                dias.forEach(function(dia) {
                    var horaIn = $(this).find(`input[name*="[${dia}][hora_in]"]`).val();
                    var horaOut = $(this).find(`input[name*="[${dia}][hora_out]"]`).val();
                    
                    if (horaIn && horaOut) {
                        var minutosTrabajados = convertirHoraAMinutos(horaOut) - convertirHoraAMinutos(horaIn);
                        if (minutosTrabajados < 0) minutosTrabajados += 24 * 60;
                        
                        var horasTrabajadas = minutosTrabajados / 60;
                        totalEmpleado += horasTrabajadas;
                        totalPorDia[dia] += horasTrabajadas;

                        $(this).find(`div[data-dia="${dia}"]`).text(horasTrabajadas.toFixed(0) + 'h');
                    } else {
                        $(this).find(`div[data-dia="${dia}"]`).text("0h");
                    }
                }, this);

                var horasAlmuerzo = parseFloat($(this).find('input[name*="[hora_alm_s]"]').val()) || 0;
                var horasLey = parseFloat($(this).find('input[name*="[hora_ley_s]"]').val()) || 44;

                var horasNetasTrabajadas = totalEmpleado - horasAlmuerzo;
                // CORRECCIÓN: Permitir valores negativos
                var horasExtras = horasNetasTrabajadas - horasLey;

                $(this).find('.total-horas').val(horasNetasTrabajadas.toFixed(0));
                $(this).find('.hora-extra').val(horasExtras.toFixed(0));
                
                totalHorasTrabajadas += horasNetasTrabajadas;
                totalHorasExtras += horasExtras;
                totalHorasAlmuerzo += horasAlmuerzo;
            });

            // Actualizar totales por día
            for (var dia in totalPorDia) {
                $(`#total-${dia}`).text(totalPorDia[dia].toFixed(0));
            }

            // Actualizar totales generales
            $('#total-horas-trabajadas').text(totalHorasTrabajadas.toFixed(0));
            $('#total-horas-almuerzo').text(totalHorasAlmuerzo.toFixed(0));
            $('#total-horas-extras').text(totalHorasExtras.toFixed(0));
        }
    </script>
</body>
</html>