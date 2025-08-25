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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Actualización de Horarios - Profesional</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --dark-color: #1e293b;
            --light-bg: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin: 0 auto;
            padding: 30px;
            max-width: 100%;
            overflow-x: auto;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .header-section > * {
            position: relative;
            z-index: 1;
        }

        .header-section h1 {
            margin: 0 0 10px 0;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header-section .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        .legend-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin: 25px 0;
            padding: 25px;
            background: var(--light-bg);
            border-radius: var(--border-radius);
            border: 2px dashed var(--secondary-color);
        }

        .legend-box {
            padding: 12px 16px;
            border-radius: 8px;
            color: #000;
            font-weight: 600;
            text-align: center;
            font-size: 0.85rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .legend-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .store-info {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
            text-align: center;
        }

        .store-info h3 {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
        }

        .table-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 0;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-header {
            background: linear-gradient(135deg, var(--dark-color), #334155);
            color: white;
            padding: 20px 25px;
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-title {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .table-modern {
            margin: 0;
            font-size: 0.9rem;
            border: none;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 15px 8px;
            border: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 12px 8px;
            vertical-align: middle;
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
            text-align: center;
        }

        .table-modern tbody tr:hover {
            background-color: rgba(37, 99, 235, 0.05);
            transform: scale(1.001);
            transition: all 0.2s ease;
        }

        .alerta-hora {
            background: linear-gradient(135deg, #fca5a5, #ef4444) !important;
            color: white !important;
            font-weight: 600 !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .descanso {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            color: white !important;
            font-style: italic;
            font-weight: 600 !important;
        }

        /* Etiquetas con gradientes */
        .etiqueta-1 { background: linear-gradient(135deg, rgb(158, 35, 240), rgb(138, 25, 220)) !important; color: white !important; }
        .etiqueta-2 { background: linear-gradient(135deg, rgb(87, 244, 250), rgb(67, 224, 230)) !important; color: black !important; }
        .etiqueta-3 { background: linear-gradient(135deg, rgb(55, 118, 255), rgb(35, 98, 235)) !important; color: white !important; }
        .etiqueta-4 { background: linear-gradient(135deg, rgb(82, 247, 90), rgb(62, 227, 70)) !important; color: black !important; }
        .etiqueta-5 { background: linear-gradient(135deg, rgb(252, 239, 62), rgb(232, 219, 42)) !important; color: black !important; }
        .etiqueta-6 { background: linear-gradient(135deg, rgb(255, 124, 36), rgb(235, 104, 16)) !important; color: white !important; }
        .etiqueta-7 { background: linear-gradient(135deg, rgb(141, 69, 1), rgb(121, 49, 1)) !important; color: white !important; }
        .etiqueta-8 { background: linear-gradient(135deg, rgb(255, 104, 235), rgb(235, 84, 215)) !important; color: black !important; }
        .etiqueta-9 { background: linear-gradient(135deg, rgb(148, 148, 148), rgb(128, 128, 128)) !important; color: white !important; }
        .etiqueta-10 { background: linear-gradient(135deg, rgb(117, 71, 97), rgb(97, 51, 77)) !important; color: white !important; }
        .etiqueta-11 { background: linear-gradient(135deg, rgb(68, 119, 66), rgb(48, 99, 46)) !important; color: white !important; }
        .etiqueta-12 { background: linear-gradient(135deg, rgb(64, 68, 151), rgb(44, 48, 131)) !important; color: white !important; }
        .etiqueta-13 { background: linear-gradient(135deg, rgb(209, 133, 203), rgb(189, 113, 183)) !important; color: black !important; }

        .btn-modern {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-update {
            background: linear-gradient(135deg, var(--info-color), #0891b2);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-manager {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .badge-sub-manager {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            color: white;
        }

        .badge-advisor {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .modal-modern .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--card-shadow);
        }

        .modal-modern .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #3b82f6);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .form-control-modern {
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 12px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control-modern:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-label-modern {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-present {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-late {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .status-absent {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: var(--card-shadow);
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .text-truncate-custom {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
            }
            
            .header-section {
                padding: 20px;
            }
            
            .header-section h1 {
                font-size: 1.8rem;
            }
            
            .legend-container {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            }
            
            .table-modern {
                font-size: 0.75rem;
            }

            .table-modern thead th {
                padding: 10px 4px;
                font-size: 0.65rem;
            }

            .table-modern tbody td {
                padding: 8px 4px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <h5>Procesando...</h5>
            <p class="mb-0 text-muted">Por favor espere</p>
        </div>
    </div>

    <div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
            <div class="header-section">
                <h1><i class="fas fa-clock"></i> Sistema de Actualización de Horarios</h1>
                <p class="subtitle">Gestión profesional de horarios y justificaciones de personal</p>
            </div>

            <!-- Leyenda de etiquetas -->
            <div class="legend-container">
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(158, 35, 240), rgb(138, 25, 220)); color: white;">
                    <i class="fas fa-user-tie"></i> GTO Presencial
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(87, 244, 250), rgb(67, 224, 230));">
                    <i class="fas fa-video"></i> GTO Virtual
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(55, 118, 255), rgb(35, 98, 235)); color: white;">
                    <i class="fas fa-tv"></i> TV Presencial
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(82, 247, 90), rgb(62, 227, 70));">
                    <i class="fas fa-desktop"></i> TV Virtual
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(252, 239, 62), rgb(232, 219, 42));">
                    <i class="fas fa-users"></i> Reunión GTS
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(255, 124, 36), rgb(235, 104, 16)); color: white;">
                    <i class="fas fa-handshake"></i> Reunión ASS
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(141, 69, 1), rgb(121, 49, 1)); color: white;">
                    <i class="fas fa-graduation-cap"></i> Inducción ROY
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(255, 104, 235), rgb(235, 84, 215));">
                    <i class="fas fa-birthday-cake"></i> Cumpleaños
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(148, 148, 148), rgb(128, 128, 128)); color: white;">
                    <i class="fas fa-umbrella-beach"></i> Vacaciones
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(117, 71, 97), rgb(97, 51, 77)); color: white;">
                    <i class="fas fa-shield-alt"></i> Cobertura
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(68, 119, 66), rgb(48, 99, 46)); color: white;">
                    <i class="fas fa-ban"></i> Suspensión LABORAL
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(64, 68, 151), rgb(44, 48, 131)); color: white;">
                    <i class="fas fa-hospital"></i> Suspensión IGSS
                </div>
                <div class="legend-box" style="background: linear-gradient(135deg, rgb(209, 133, 203), rgb(189, 113, 183));">
                    <i class="fas fa-baby"></i> Lactancia
                </div>
            </div>

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

                // Tu query original aquí
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
                             ON  HR.CODIGO_EMPL = V.CODIGO_VENDEDOR

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

                        -- Filtros
                        WHERE TO_DATE(HR.FECHA, 'YYYY-MM-DD') 
          BETWEEN TO_DATE('$fi', 'YYYY-MM-DD') 
              AND TO_DATE('$ff', 'YYYY-MM-DD')

                          AND HR.TIENDA = $tienda
                          AND V.SBS = $sbs

                        ORDER BY 
                        DECODE(V.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),
                        
                            TO_DATE(HR.FECHA, 'YYYY-MM-DD')";

                $resultado = consultaOracle(3, $query);
                $cnt = 1;
            ?>

            <!-- Store Info -->
            <div class="store-info">
                <h3><i class="fas fa-store"></i> Tienda: <?php echo $tienda ?></h3>
            </div>

            <!-- Tabla de horarios -->
            <div class="table-container">
                <div class="table-header">
                    <h4 class="table-title">
                        <i class="fas fa-table"></i> 
                        Gestión de Horarios - Tienda <?php echo $tienda ?>
                    </h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern tbrdst" id="table-<?php echo $tienda ?>">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> No</th>
                                <th><i class="fas fa-id-card"></i> ID Registro</th>
                                <th><i class="fas fa-store"></i> Tienda</th>
                                <th><i class="fas fa-barcode"></i> Código</th>
                                <th><i class="fas fa-user"></i> Nombre</th>
                                <th><i class="fas fa-briefcase"></i> Puesto</th>
                                <th><i class="fas fa-calendar-day"></i> Día</th>
                                <th><i class="fas fa-calendar"></i> Fecha</th>
                                <th><i class="fas fa-clock"></i> Hora Ingreso</th>
                                <th><i class="fas fa-clock"></i> Hora Salida</th>
                                <th><i class="fas fa-comment-alt"></i> Razón</th>
                                <th><i class="fas fa-calendar-check"></i> Fecha Actualización</th>
                                <th><i class="fas fa-cogs"></i> Acción</th>
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

                                // Determinar clase de etiqueta
                                $etiquetaClase = !empty($rdst[12]) ? 'etiqueta-' . intval($rdst[12]) : '';
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo $cnt ?></span></td>
                                <td><strong class="text-primary"><?php echo $rdst[14] ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-info me-2"></i>
                                        <?php echo $rdst[0] ?>
                                    </div>
                                </td>
                                <td><strong class="text-primary"><?php echo $rdst[1] ?></strong></td>
                                <td>
                                    <div class="text-truncate-custom" title="<?php echo $rdst[2] ?>">
                                        <i class="fas fa-user-circle text-secondary me-2"></i>
                                        <?php echo $rdst[2] ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $puesto = SUBSTR($rdst[3], 0, 3);
                                    $badge_class = '';
                                    switch($puesto) {
                                        case 'JEF': $badge_class = 'badge-manager'; break;
                                        case 'SUB': $badge_class = 'badge-sub-manager'; break;
                                        case 'ASE': $badge_class = 'badge-advisor'; break;
                                        default: $badge_class = 'badge-advisor'; break;
                                    }
                                    ?>
                                    <span class="badge-custom <?php echo $badge_class; ?>"><?php echo $puesto ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-day text-warning me-2"></i>
                                        <strong><?php echo $rdst[4] ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-info me-2"></i>
                                        <?php echo $rdst[5] ?>
                                    </div>
                                </td>
                                <td class="<?php echo $etiquetaClase; ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        <strong><?php echo $rdst[6] ?></strong>
                                    </div>
                                </td>
                                <td class="<?php echo $etiquetaClase; ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        <strong><?php echo $rdst[8] ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate-custom" title="<?php echo $rdst[13] ?>">
                                        <?php if (!empty($rdst[13])): ?>
                                            <i class="fas fa-comment-dots text-success me-2"></i>
                                            <?php echo $rdst[13] ?>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="fas fa-minus me-2"></i>Sin justificación
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($rdst[17])): ?>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-success me-2"></i>
                                            <small><?php echo $rdst[17] ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">
                                            <i class="fas fa-minus"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-modern btn-update btn-sm justificar-btn" 
                                                data-id="<?php echo $rdst[14]; ?>" 
                                                data-nombre="<?php echo $rdst[2]; ?>" 
                                                data-codigo="<?php echo $rdst[1]; ?>"
                                                data-fecha="<?php echo htmlspecialchars($rdst[5]); ?>"
                                                data-dia="<?php echo $rdst[4]; ?>"
                                                data-hora-in="<?php echo $rdst[6]; ?>"
                                                data-hora-out="<?php echo $rdst[8]; ?>"
                                                data-justificacion="<?php echo htmlspecialchars($rdst[13]); ?>"
                                                title="Actualizar registro">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-modern btn-delete btn-sm eliminar-btn" 
                                                data-id="<?php echo $rdst[14]; ?>"
                                                title="Eliminar registro">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
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

            <hr class="my-5">
            
            <?php
            }
            ?>
        </div>
    </div>

    <!-- Modal Justificación -->
    <div class="modal fade modal-modern" id="justificarModal" tabindex="-1" role="dialog" aria-labelledby="justificarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="formJustificacion">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-clock me-2"></i> Actualización de Horario
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body p-4">           
                        <input type="hidden" name="id_registro" id="id_registro">
                        <input type="hidden" name="etiqueta" id="etiqueta">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="fas fa-user"></i> Empleado
                                </label>
                                <input type="text" class="form-control form-control-modern" id="nombre_empleado" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="fas fa-barcode"></i> Código
                                </label>
                                <input type="text" class="form-control form-control-modern" id="codigo_empleado" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="fas fa-calendar"></i> Fecha
                                </label>
                                <input type="text" class="form-control form-control-modern" id="fecha" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="fas fa-calendar-day"></i> Día
                                </label>
                                <input type="text" class="form-control form-control-modern" id="dia" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="fas fa-sign-in-alt"></i> Hora Ingreso
                                </label>
                                <input type="text" class="form-control form-control-modern" id="hora_in" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-modern">
                                    <i class="fas fa-sign-out-alt"></i> Hora Salida
                                </label>
                                <input type="text" class="form-control form-control-modern" id="hora_out" disabled>
                            </div>

                            <div class="col-12">
                                <label class="form-label-modern">
                                    <i class="fas fa-tags"></i> Seleccionar motivo
                                </label>
                                <select class="form-control form-control-modern" id="motivo_select">
                                    <option value="">-- Seleccione un motivo --</option>
                                    <option value="GTO PRESENCIAL">🏢 GTO PRESENCIAL</option>
                                    <option value="GTO VIRTUAL">💻 GTO VIRTUAL</option>
                                    <option value="TV PRESENCIAL">📺 TV PRESENCIAL</option>
                                    <option value="TV VIRTUAL">🖥️ TV VIRTUAL</option>
                                    <option value="REUNION GTS">👥 REUNION GTS</option>
                                    <option value="REUNION ASS">🤝 REUNION ASS</option>
                                    <option value="INDUCCION ROY">🎓 INDUCCION ROY</option>
                                    <option value="CUMPLEANOS">🎂 CUMPLEAÑOS</option>
                                    <option value="VACACIONES">🏖️ VACACIONES</option>
                                    <option value="COBERTURA">🛡️ COBERTURA</option>
                                    <option value="SUSPENSION LABORAL">🚫 SUSPENSION LABORAL</option>
                                    <option value="SUSPENSION IGSS">🏥 SUSPENSION IGSS</option>
                                    <option value="LACTANCIA">👶 LACTANCIA</option>
                                    <option value="CITA IGSS">🏥 CITA IGSS</option>
                                    <option value="OTROS">📝 OTROS</option>
                                </select>
                            </div>

                            <!-- NUEVO: Fechas de SUSPENSION -->
                            <div id="fechasSuspension" style="display: none;" class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-modern">
                                            <i class="fas fa-calendar-plus"></i> Fecha Inicio
                                        </label>
                                        <input type="date" class="form-control form-control-modern" name="fecha_inicio" id="fecha_inicio">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-modern">
                                            <i class="fas fa-calendar-minus"></i> Fecha Fin
                                        </label>
                                        <input type="date" class="form-control form-control-modern" name="fecha_fin" id="fecha_fin">
                                    </div>
                                </div>
                            </div>

                            <!-- NUEVO: Horas para justificaciones específicas -->
                            <div id="horasGTO" style="display: none;" class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-modern">
                                            <i class="fas fa-clock"></i> Hora Ingreso
                                        </label>
                                        <input type="time" class="form-control form-control-modern" name="gto_hora_ingreso" id="gto_hora_ingreso">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-modern">
                                            <i class="fas fa-clock"></i> Hora Salida
                                        </label>
                                        <input type="time" class="form-control form-control-modern" name="gto_hora_salida" id="gto_hora_salida">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-modern">
                                    <i class="fas fa-comment-alt"></i> Razón
                                </label>
                                <textarea class="form-control form-control-modern" name="justificacion" id="justificacion" rows="3" placeholder="Describa la razón de la actualización..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-modern">
                            <i class="fas fa-save me-2"></i> Actualizar
                        </button>
                        <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cerrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <script>
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        $(document).ready(function () {
            // Inicializar DataTables
            $('.tbrdst').each(function() {
                $(this).DataTable({
                    "searching": true,
                    "paging": true,
                    "pageLength": 25,
                    "ordering": true,
                    "info": true,
                    "responsive": true,
                    "autoWidth": false,
                    "scrollX": true,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                    },
                    "columnDefs": [
                        { "orderable": false, "targets": [0, 12] }, // No ordenar columna No y Acción
                        { "searchable": false, "targets": [0, 12] }
                    ]
                });
            });

            // Abrir modal con datos
            $(document).off('click', '.justificar-btn').on('click', '.justificar-btn', function () {
                $('#id_registro').val($(this).data('id'));
                $('#nombre_empleado').val($(this).data('nombre'));
                $('#codigo_empleado').val($(this).data('codigo'));
                $('#fecha').val($(this).data('fecha'));
                $('#dia').val($(this).data('dia'));
                $('#hora_in').val($(this).data('hora-in'));
                $('#hora_out').val($(this).data('hora-out'));
                $('#justificacion').val($(this).data('justificacion'));
                
                // Limpiar motivo y ocultar secciones al abrir modal
                $('#motivo_select').val('');
                $('#fechasSuspension').hide();
                $('#horasGTO').hide();
                
                var modal = new bootstrap.Modal(document.getElementById('justificarModal'));
                modal.show();
            });

            // Guardar justificación
            $('#formJustificacion').submit(function (e) {
                e.preventDefault();
                showLoading();
                
                $.ajax({
                    url: '/roy/Page/supervision/update_horarios_tds.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        hideLoading();
                        Swal.fire({
                            icon: 'success',
                            title: '¡Actualización exitosa!',
                            text: 'Los datos se han actualizado correctamente',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            var modal = bootstrap.Modal.getInstance(document.getElementById('justificarModal'));
                            modal.hide();
                            location.reload();
                        });
                    },
                    error: function () {
                        hideLoading();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un error al guardar la actualización',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            });

            // Cambio de motivo - Lógica mejorada
            $('#motivo_select').on('change', function () {
                var selected = $(this).val();

                // Mostrar/ocultar fechas para suspensiones y vacaciones
                if (['SUSPENSION LABORAL', 'VACACIONES', 'SUSPENSION IGSS'].includes(selected)) {
                    $('#fechasSuspension').show();
                } else {
                    $('#fechasSuspension').hide();
                    $('#fecha_inicio').val('');
                    $('#fecha_fin').val('');
                }

                // Mostrar/ocultar horas para diferentes tipos de justificaciones
                if (['CITA IGSS', 'GTO PRESENCIAL', 'GTO VIRTUAL', 'TV PRESENCIAL', 'TV VIRTUAL', 'REUNION GTS', 'REUNION ASS', 'COBERTURA', 'LACTANCIA', 'OTROS'].includes(selected)) {
                    $('#horasGTO').show();
                } else {
                    $('#horasGTO').hide();
                    $('#gto_hora_ingreso').val('');
                    $('#gto_hora_salida').val('');
                }

                // Lógica principal para el campo razón
                if (selected === 'OTROS') {
                    // Para OTROS: campo vacío y editable
                    $('#justificacion').val('').prop('readonly', false).focus();
                } else if (selected === '') {
                    // Para opción vacía: campo vacío y editable
                    $('#justificacion').val('').prop('readonly', false);
                } else {
                    // Para cualquier otro motivo: llenar automáticamente y hacer readonly
                    $('#justificacion').val(selected).prop('readonly', true);
                }

                // Asignar etiquetas
                var etiquetas = {
                    "GTO PRESENCIAL": 1,
                    "GTO VIRTUAL": 2,
                    "TV PRESENCIAL": 3,
                    "TV VIRTUAL": 4,
                    "REUNION GTS": 5,
                    "REUNION ASS": 6,
                    "INDUCCION ROY": 7,
                    "CUMPLEANOS": 8,
                    "VACACIONES": 9,
                    "COBERTURA": 10,
                    "SUSPENSION LABORAL": 11,
                    "SUSPENSION IGSS": 12,
                    "LACTANCIA": 13,
                    "CITA IGSS": 14,
                    "OTROS": 15
                };

                $('#etiqueta').val(etiquetas[selected] || '');
            });

            // Eliminar registro
            $(document).off('click', '.eliminar-btn').on('click', '.eliminar-btn', function () {
                var id = $(this).data('id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción eliminará el registro permanentemente',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showLoading();
                        
                        $.ajax({
                            url: '/roy/Page/supervision/update_horarios_tds.php',
                            type: 'POST',
                            data: { 
                                id_registro: id, 
                                modo: 'eliminar' 
                            },
                            success: function (response) {
                                hideLoading();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'El registro se ha eliminado correctamente',
                                    confirmButtonColor: '#10b981'
                                }).then(() => {
                                    // Eliminar la fila de la tabla
                                    var table = $('.tbrdst').DataTable();
                                    table.row(row).remove().draw();
                                });
                            },
                            error: function () {
                                hideLoading();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un error al eliminar el registro',
                                    confirmButtonColor: '#ef4444'
                                });
                            }
                        });
                    }
                });
            });

            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>