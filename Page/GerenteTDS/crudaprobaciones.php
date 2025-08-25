<?php
// Debug logging mejorado
error_log("=== NUEVA PETICIÓN GERENTES ===");
error_log("GET: " . print_r($_GET, true));
error_log("POST: " . print_r($_POST, true));

header('Content-Type: application/json');

// Para debugging - activar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);
//=====================================================================
// FUNCIONES DE UTILIDAD
//=====================================================================

// ===================================================================
// FUNCIONES AUXILIARES
// ===================================================================

function enviarJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function manejarError($mensaje, $errorOracle = null) {
    $errorCompleto = $mensaje;
    if ($errorOracle) {
        $errorCompleto .= ': ' . (is_array($errorOracle) ? $errorOracle['message'] : $errorOracle);
    }
    error_log($errorCompleto);
    enviarJSON(['success' => false, 'error' => $errorCompleto]);
}

function registrarHistorial($conn, $accion, $datos = []) {
    try {
        error_log("Registrando historial: $accion - " . json_encode($datos));
        // Aquí puedes implementar el registro de historial si es necesario
    } catch (Exception $e) {
        error_log("Error registrando historial: " . $e->getMessage());
    }
}

// FUNCIÓN PARA CALCULAR TIEMPO TRANSCURRIDO
function ordenarDatosPorFecha($datos) {
    usort($datos, function($a, $b) {
        $fechaA = DateTime::createFromFormat('d-m-Y H:i:s', $a['FECHA_CAMBIO']);
        if (!$fechaA) {
            $fechaA = DateTime::createFromFormat('Y-m-d H:i:s', $a['FECHA_CAMBIO']);
        }
        
        $fechaB = DateTime::createFromFormat('d-m-Y H:i:s', $b['FECHA_CAMBIO']);
        if (!$fechaB) {
            $fechaB = DateTime::createFromFormat('Y-m-d H:i:s', $b['FECHA_CAMBIO']);
        }
        
        return $fechaA <=> $fechaB; // Orden ascendente (más antiguo primero)
    });
    
    return $datos;
}

// ✅ FUNCIÓN PARA CALCULAR TIEMPO ENTRE CAMBIOS CONSECUTIVOS (FORMATO COMPLETO)
function calcularTiempoConsecutivoCompleto($fechaAnterior, $fechaActual) {
    if (empty($fechaAnterior)) {
        return '--- PRIMER REGISTRO ---'; // Primera fila muestra esto
    }
    
    try {
        // Convertir fechas
        $fechaAnt = DateTime::createFromFormat('d-m-Y H:i:s', $fechaAnterior);
        if (!$fechaAnt) {
            $fechaAnt = DateTime::createFromFormat('Y-m-d H:i:s', $fechaAnterior);
        }
        
        $fechaAct = DateTime::createFromFormat('d-m-Y H:i:s', $fechaActual);
        if (!$fechaAct) {
            $fechaAct = DateTime::createFromFormat('Y-m-d H:i:s', $fechaActual);
        }
        
        if (!$fechaAnt || !$fechaAct) {
            return 'Error de fecha';
        }
        
        $diferencia = $fechaAct->diff($fechaAnt);
        
        // Calcular tiempo transcurrido en formato completo
        $totalDias = $diferencia->days;
        $horas = $diferencia->h;
        $minutos = $diferencia->i;
        $segundos = $diferencia->s;
        
        // Formato completo para debug
        $partes = [];
        
        if ($totalDias > 0) {
            $partes[] = $totalDias . ' día' . ($totalDias > 1 ? 's' : '');
        }
        if ($horas > 0) {
            $partes[] = $horas . ' hora' . ($horas > 1 ? 's' : '');
        }
        if ($minutos > 0) {
            $partes[] = $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
        }
        if ($segundos > 0 || empty($partes)) {
            $partes[] = $segundos . ' segundo' . ($segundos > 1 ? 's' : '');
        }
        
        return implode(', ', $partes) . ' transcurrido' . (count($partes) > 1 ? 's' : '');
        
    } catch (Exception $e) {
        return 'Error de cálculo: ' . $e->getMessage();
    }
}

// ✅ FUNCIÓN PARA GENERAR ESTADÍSTICAS DE TIEMPO MEJORADAS
function generarEstadisticasTiempoMejoradas($datos) {
    if (empty($datos)) {
        return [
            'fecha_mas_antiguo' => 'N/A',
            'fecha_mas_reciente' => 'N/A', 
            'tiempo_total' => 'N/A',
            'promedio_real' => 'N/A'
        ];
    }
    
    // IMPORTANTE: Ordenar datos por fecha ascendente
    $datosOrdenados = ordenarDatosPorFecha($datos);
    
    // Obtener fechas ordenadas
    $fechas = [];
    $tiempoTotalSegundos = 0;
    
    foreach ($datosOrdenados as $registro) {
        try {
            $fecha = DateTime::createFromFormat('d-m-Y H:i:s', $registro['FECHA_CAMBIO']);
            if (!$fecha) {
                $fecha = DateTime::createFromFormat('Y-m-d H:i:s', $registro['FECHA_CAMBIO']);
            }
            if ($fecha) {
                $fechas[] = $fecha;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    if (count($fechas) < 2) {
        $fechaMasAntigua = $fechas[0] ?? null;
        $fechaMasReciente = $fechas[0] ?? null;
        
        return [
            'fecha_mas_antiguo' => $fechaMasAntigua ? $fechaMasAntigua->format('d-m-Y H:i:s') : 'N/A',
            'fecha_mas_reciente' => $fechaMasReciente ? $fechaMasReciente->format('d-m-Y H:i:s') : 'N/A',
            'tiempo_total' => 'Solo un registro',
            'promedio_real' => 'N/A'
        ];
    }
    
    $fechaMasAntigua = $fechas[0];
    $fechaMasReciente = end($fechas);
    
    // Calcular tiempo total entre todos los cambios consecutivos
    for ($i = 1; $i < count($fechas); $i++) {
        $diferencia = $fechas[$i]->diff($fechas[$i-1]);
        $segundos = ($diferencia->days * 86400) + ($diferencia->h * 3600) + ($diferencia->i * 60) + $diferencia->s;
        $tiempoTotalSegundos += $segundos;
    }
    
    // Calcular promedio real
    $promedioSegundos = $tiempoTotalSegundos / (count($fechas) - 1);
    
    // Convertir tiempo total a formato legible
    function convertirSegundosATexto($segundosTotal) {
        $diasTotal = floor($segundosTotal / 86400);
        $horasTotal = floor(($segundosTotal % 86400) / 3600);
        $minutosTotal = floor(($segundosTotal % 3600) / 60);
        $segTotal = floor($segundosTotal % 60);
        
        $partes = [];
        if ($diasTotal > 0) $partes[] = $diasTotal . ' día' . ($diasTotal > 1 ? 's' : '');
        if ($horasTotal > 0) $partes[] = $horasTotal . ' hora' . ($horasTotal > 1 ? 's' : '');
        if ($minutosTotal > 0) $partes[] = $minutosTotal . ' minuto' . ($minutosTotal > 1 ? 's' : '');
        if ($segTotal > 0 || empty($partes)) $partes[] = $segTotal . ' segundo' . ($segTotal > 1 ? 's' : '');
        
        return implode(', ', $partes);
    }
    
    $tiempoTotalTexto = $tiempoTotalSegundos > 0 ? convertirSegundosATexto($tiempoTotalSegundos) : 'N/A';
    $promedioTexto = $promedioSegundos > 0 ? convertirSegundosATexto($promedioSegundos) : 'N/A';
    
    return [
        'fecha_mas_antiguo' => $fechaMasAntigua->format('d-m-Y H:i:s'),
        'fecha_mas_reciente' => $fechaMasReciente->format('d-m-Y H:i:s'),
        'tiempo_total' => $tiempoTotalTexto,
        'promedio_real' => $promedioTexto
    ];
}

// ✅ FUNCIÓN EXCEL COMPLETA CORREGIDA
function generarExcelHistorial($datos) {
    if (!isset($datos['datos']) || empty($datos['datos'])) {
        echo "<script>alert('No hay datos para exportar'); window.close();</script>";
        exit;
    }
    
    // IMPORTANTE: Ordenar datos por fecha ascendente antes de calcular
    $datosOrdenados = ordenarDatosPorFecha($datos['datos']);
    $datos['datos'] = $datosOrdenados;
    
    // Calcular estadísticas de tiempo mejoradas
    $estadisticasTiempo = generarEstadisticasTiempoMejoradas($datos['datos']);
    
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="historial_solicitudes_' . date('Y-m-d_H-i-s') . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 0;
                padding: 15px;
            }
            .header-container {
                text-align: center;
                padding: 15px 0;
                border-bottom: 2px solid #333;
                margin-bottom: 15px;
            }
            .header-content {
                display: inline-block;
                vertical-align: middle;
            }
            .logo-text { 
                display: inline-block;
                vertical-align: middle;
                font-size: 24px;
                font-weight: bold;
                color: #000;
                margin-right: 25px;
            }
            .title { 
                display: inline-block;
                vertical-align: middle;
                font-size: 18px; 
                font-weight: bold; 
                color: #333;
            }
            .filters {
                text-align: center;
                color: #666;
                font-size: 11px;
                margin: 10px 0 15px 0;
            }
            .info-row {
                font-size: 10px;
                margin-bottom: 15px;
                text-align: center;
            }
            .orden-nota {
                background-color: #fff3cd;
                border: 2px solid #ffc107;
                padding: 10px;
                margin: 15px 0;
                border-radius: 5px;
                text-align: center;
                font-weight: bold;
                color: #856404;
            }
            table { 
                border-collapse: collapse; 
                width: 100%; 
                font-size: 12px;
            }
            th { 
                background-color: #4A90E2;
                color: white; 
                font-weight: bold; 
                padding: 10px 8px;
                border: 2px solid #2c5aa0;
                text-align: center;
                font-size: 12px;
            }
            td { 
                padding: 8px 8px;
                border: 1px solid #4A90E2;
                text-align: left;
                font-size: 12px;
                background-color: #ffffff;
            }
            .fila-par {
                background-color: #f0f8ff;
            }
            .fila-impar {
                background-color: #fafffe;
            }
            .col-azul {
                border-left: 3px solid #4A90E2;
            }
            .col-verde {
                border-left: 3px solid #4ECDC4;
            }
            .tiempo-transcurrido { 
                background-color: #fff3cd; 
                font-weight: bold; 
                color: #856404; 
                text-align: center;
                font-size: 11px;
            }
            .primer-registro {
                background-color: #d4edda;
                color: #155724;
                text-align: center;
                font-weight: bold;
            }
            .estadisticas-tiempo {
                background-color: #e8f5e8;
                border: 2px solid #4CAF50;
                padding: 15px;
                margin: 20px auto;
                border-radius: 5px;
                width: 60%;
            }
        </style>
    </head>
    <body>';
    
    // HEADER
    echo '<div class="header-container">
        <div class="header-content">
            <span class="logo-text">ROY</span>
            <span class="title">' . htmlspecialchars($datos['titulo_reporte']) . '</span>
        </div>
    </div>';
    
    echo '<div class="filters">' . htmlspecialchars($datos['subtitulo']) . '</div>';
    
    echo '<div class="info-row">
        <strong>Período:</strong> ' . htmlspecialchars($datos['periodo']) . ' | 
        <strong>Total de registros:</strong> ' . $datos['registros'] . ' | 
        <strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '
    </div>';

    // RESUMEN SI ES REPORTE GENERAL
    if ($datos['tipo_reporte'] === 'general' && isset($datos['resumen'])) {
        echo '<table style="margin-bottom: 15px; width: 60%; margin-left: auto; margin-right: auto;">
            <tr><th colspan="2">RESUMEN ESTADÍSTICO</th></tr>
            <tr class="fila-par"><td style="padding: 8px;"><strong>Total de cambios</strong></td><td style="text-align: center;">' . $datos['resumen']['total_cambios'] . '</td></tr>
            <tr class="fila-impar"><td style="padding: 8px;"><strong>Solicitudes afectadas</strong></td><td style="text-align: center;">' . $datos['resumen']['solicitudes_afectadas'] . '</td></tr>
            <tr class="fila-par"><td style="padding: 8px;"><strong>Tiendas afectadas</strong></td><td style="text-align: center;">' . $datos['resumen']['tiendas_afectadas'] . '</td></tr>
            <tr class="fila-impar"><td style="padding: 8px;"><strong>Supervisores afectados</strong></td><td style="text-align: center;">' . $datos['resumen']['supervisores_afectados'] . '</td></tr>
        </table>';
    }
    
    // TABLA PRINCIPAL CON TIEMPO CONSECUTIVO
    echo '<table>
        <thead>
            <tr>';
    
    foreach ($datos['headers_excel'] as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    echo '<th style="background-color: #FF6B6B;"><i class="fas fa-clock"></i> Tiempo Transcurrido</th>';
    
    echo '</tr>
        </thead>
        <tbody>';
    
    foreach ($datos['datos'] as $rowIndex => $registro) {
        $filaClass = ($rowIndex % 2 == 0) ? 'fila-par' : 'fila-impar';
        
        // Calcular tiempo desde el cambio anterior (ahora que están ordenados correctamente)
        $fechaAnterior = ($rowIndex > 0) ? $datos['datos'][$rowIndex - 1]['FECHA_CAMBIO'] : '';
        $tiempoTranscurrido = calcularTiempoConsecutivoCompleto($fechaAnterior, $registro['FECHA_CAMBIO']);
        
        // Determinar si es el primer registro
        $esPrimerRegistro = ($rowIndex === 0);
        $tiempoClass = $esPrimerRegistro ? 'primer-registro' : 'tiempo-transcurrido';
        
        echo '<tr class="' . $filaClass . '">
            <td class="col-azul">' . htmlspecialchars($registro['ID_SOLICITUD']) . '</td>
            <td class="col-verde">' . htmlspecialchars($registro['NUM_TIENDA']) . '</td>
            <td class="col-azul">' . htmlspecialchars($registro['PUESTO_SOLICITADO']) . '</td>
            <td class="col-verde">' . htmlspecialchars($registro['SOLICITADO_POR']) . '</td>
            <td class="col-verde">' . htmlspecialchars($registro['ESTADO_ANTERIOR']) . '</td>
            <td class="col-azul">' . htmlspecialchars($registro['ESTADO_NUEVO']) . '</td>
            <td class="col-verde">' . htmlspecialchars($registro['APROBACION_ANTERIOR']) . '</td>
            <td class="col-azul">' . htmlspecialchars($registro['APROBACION_NUEVA']) . '</td>
            <td class="col-verde">' . htmlspecialchars($registro['COMENTARIO_NUEVO']) . '</td>
            <td class="col-azul">' . htmlspecialchars($registro['FECHA_CAMBIO']) . '</td>
            <td class="' . $tiempoClass . '">' . htmlspecialchars($tiempoTranscurrido) . '</td>
        </tr>';
    }
    
    echo '</tbody>
    </table>';
    
    // ✅ AGREGAR FILA DE TIEMPO TOTAL TRANSCURRIDO
    echo '<table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td colspan="10" style="text-align: right; font-weight: bold; background-color: #e8f4f8; padding: 10px; border: 2px solid #4A90E2;">
                TIEMPO TOTAL TRANSCURRIDO:
            </td>
            <td style="text-align: center; font-weight: bold; background-color: #fff3cd; color: #856404; padding: 10px; border: 2px solid #FF6B6B;">
                ' . htmlspecialchars($estadisticasTiempo['tiempo_total']) . '
            </td>
        </tr>
    </table>';
    
    // ESTADÍSTICAS DE TIEMPO CENTRADAS - MOVIDO MÁS AL CENTRO
    echo '<br><br>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="11" style="text-align: center; font-size: 16px; font-weight: bold; color: #2E7D32; padding: 15px; background-color: #f8f9fa; border: 1px solid #ddd;">
                ANÁLISIS DE TIEMPO TRANSCURRIDO
            </td>
        </tr>
    </table>
    
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: #e8f5e8; font-weight: bold; font-size: 12px;">Cambio más antiguo:</td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: white; text-align: center; font-size: 12px;">' . htmlspecialchars($estadisticasTiempo['fecha_mas_antiguo']) . '</td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
        </tr>
        <tr>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: #e8f5e8; font-weight: bold; font-size: 12px;">Cambio más reciente:</td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: white; text-align: center; font-size: 12px;">' . htmlspecialchars($estadisticasTiempo['fecha_mas_reciente']) . '</td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
        </tr>
        <tr>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: #e8f5e8; font-weight: bold; font-size: 12px;">Tiempo total entre cambios:</td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: white; text-align: center; font-size: 12px;">' . htmlspecialchars($estadisticasTiempo['tiempo_total']) . '</td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
        </tr>
        <tr>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: #e8f5e8; font-weight: bold; font-size: 12px;">Tiempo promedio entre cambios:</td>
            <td style="width: 16.67%; padding: 8px; border: 2px solid #4CAF50; background-color: white; text-align: center; font-size: 12px;">' . htmlspecialchars($estadisticasTiempo['promedio_real']) . '</td>
            <td style="width: 8.33%;"></td>
            <td style="width: 8.33%;"></td>
        </tr>
    </table>';
    
    echo '</body>
    </html>';
    
    exit;
}

// ✅ FUNCIÓN PDF COMPLETA CORREGIDA (similar implementación)
function generarPDFHistorial($datos) {
    if (!isset($datos['datos']) || empty($datos['datos'])) {
        echo "<script>alert('No hay datos para exportar'); window.close();</script>";
        exit;
    }
    
    // IMPORTANTE: Ordenar datos por fecha ascendente antes de calcular
    $datosOrdenados = ordenarDatosPorFecha($datos['datos']);
    $datos['datos'] = $datosOrdenados;
    
    // Calcular estadísticas de tiempo mejoradas
    $estadisticasTiempo = generarEstadisticasTiempoMejoradas($datos['datos']);
    
    header('Content-Type: text/html; charset=UTF-8');
    
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>' . $datos['titulo_reporte'] . '</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                font-size: 12px;
                margin: 20px;
            }
            .header {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
                padding-bottom: 15px;
            }
            .header img {
                height: 50px;
                margin-right: 15px;
                vertical-align: middle;
            }
            .logo-text {
                font-size: 24px;
                font-weight: bold;
                color: #000;
                margin-right: 20px;
            }
            .titulo {
                font-size: 18px;
                font-weight: bold;
                color: #333;
            }
            .subtitulo {
                font-size: 14px;
                color: #666;
                margin: 10px 0;
                text-align: center;
            }
            .info {
                background-color: #f5f5f5;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
            }
            .orden-nota {
                background-color: #fff3cd;
                border: 2px solid #ffc107;
                padding: 10px;
                margin: 15px 0;
                border-radius: 5px;
                text-align: center;
                font-weight: bold;
                color: #856404;
            }
            .resumen {
                display: flex;
                justify-content: space-around;
                margin-bottom: 20px;
                background-color: #e8f4f8;
                padding: 15px;
                border-radius: 5px;
            }
            .resumen-item {
                text-align: center;
            }
            .resumen-numero {
                font-size: 24px;
                font-weight: bold;
                color: #2c5aa0;
            }
            .resumen-texto {
                font-size: 12px;
                color: #666;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                font-size: 10px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 6px;
                text-align: left;
            }
            th {
                background-color: #4a90e2;
                color: white;
                font-weight: bold;
                text-align: center;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .tiempo-transcurrido {
                background-color: #fff3cd;
                font-weight: bold;
                color: #856404;
                text-align: center;
            }
            .primer-registro {
                background-color: #d4edda;
                color: #155724;
                text-align: center;
                font-weight: bold;
            }
            .estadisticas-tiempo {
                background-color: #e8f5e8;
                border: 2px solid #4CAF50;
                padding: 15px;
                margin: 20px auto;
                border-radius: 5px;
                width: 60%;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 10px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 10px;
            }
            @media print {
                body { margin: 0; }
                .no-print { display: none; }
            }
        </style>
        <script>
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 1000);
            };
        </script>
    </head>
    <body>
        <div class="header">
            <img src="logo3.png" alt="Logo ROY" style="height: 50px; margin-right: 15px; vertical-align: middle;">
            <div>
                <div class="titulo">' . htmlspecialchars($datos['titulo_reporte']) . '</div>
            </div>
        </div>
        
        <div class="subtitulo">' . htmlspecialchars($datos['subtitulo']) . '</div>
        
        <div class="info">
            <strong>Período:</strong> ' . htmlspecialchars($datos['periodo']) . '<br>
            <strong>Total de registros:</strong> ' . $datos['registros'] . '<br>
            <strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '
        </div>
        
        <div class="orden-nota">
            <i class="fas fa-info-circle"></i> 
            <strong>NOTA:</strong> Los datos han sido ordenados cronológicamente (más antiguo primero) para calcular correctamente el tiempo transcurrido entre cambios consecutivos.
        </div>';
    
    // Mostrar resumen si es reporte general
    if ($datos['tipo_reporte'] === 'general' && isset($datos['resumen'])) {
        echo '<div class="resumen">
            <div class="resumen-item">
                <div class="resumen-numero">' . $datos['resumen']['total_cambios'] . '</div>
                <div class="resumen-texto">Total de Cambios</div>
            </div>
            <div class="resumen-item">
                <div class="resumen-numero">' . $datos['resumen']['solicitudes_afectadas'] . '</div>
                <div class="resumen-texto">Solicitudes Afectadas</div>
            </div>
            <div class="resumen-item">
                <div class="resumen-numero">' . $datos['resumen']['tiendas_afectadas'] . '</div>
                <div class="resumen-texto">Tiendas Afectadas</div>
            </div>
            <div class="resumen-item">
                <div class="resumen-numero">' . $datos['resumen']['supervisores_afectados'] . '</div>
                <div class="resumen-texto">Supervisores Afectados</div>
            </div>
        </div>';
    }
    
    // Tabla de datos con tiempo consecutivo
    echo '<table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tienda</th>
                <th>Puesto</th>
                <th>Supervisor</th>
                <th>Estado Anterior</th>
                <th>Estado Nuevo</th>
                <th>Aprob. Anterior</th>
                <th>Aprob. Nueva</th>
                <th>Comentario</th>
                <th>Fecha</th>
                <th><i class="fas fa-clock"></i> Tiempo Transcurrido</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($datos['datos'] as $rowIndex => $registro) {
        // Calcular tiempo desde el cambio anterior (ahora que están ordenados correctamente)
        $fechaAnterior = ($rowIndex > 0) ? $datos['datos'][$rowIndex - 1]['FECHA_CAMBIO'] : '';
        $tiempoTranscurrido = calcularTiempoConsecutivoCompleto($fechaAnterior, $registro['FECHA_CAMBIO']);
        
        // Determinar si es el primer registro
        $esPrimerRegistro = ($rowIndex === 0);
        $tiempoClass = $esPrimerRegistro ? 'primer-registro' : 'tiempo-transcurrido';
        
        echo '<tr>
            <td>' . htmlspecialchars($registro['ID_SOLICITUD']) . '</td>
            <td>' . htmlspecialchars($registro['NUM_TIENDA']) . '</td>
            <td>' . htmlspecialchars($registro['PUESTO_SOLICITADO']) . '</td>
            <td>' . htmlspecialchars($registro['SOLICITADO_POR']) . '</td>
            <td>' . htmlspecialchars($registro['ESTADO_ANTERIOR']) . '</td>
            <td>' . htmlspecialchars($registro['ESTADO_NUEVO']) . '</td>
            <td>' . htmlspecialchars($registro['APROBACION_ANTERIOR']) . '</td>
            <td>' . htmlspecialchars($registro['APROBACION_NUEVA']) . '</td>
            <td>' . htmlspecialchars($registro['COMENTARIO_NUEVO']) . '</td>
            <td>' . htmlspecialchars($registro['FECHA_CAMBIO']) . '</td>
            <td class="' . $tiempoClass . '">' . htmlspecialchars($tiempoTranscurrido) . '</td>
        </tr>';
    }
    
    echo '</tbody>
    </table>';
    
    // Estadísticas de tiempo centradas
    echo '<div style="text-align: center; margin: 30px 0;">
        <h3 style="color: #2E7D32;"><i class="fas fa-chart-bar"></i> ANÁLISIS DE TIEMPO TRANSCURRIDO</h3>
    </div>
    
    <div class="estadisticas-tiempo">
        <table style="width: 100%; background-color: white; margin: 0;">
            <tr>
                <td style="padding: 10px;"><strong><i class="fas fa-calendar-alt"></i> Cambio más antiguo:</strong></td>
                <td style="padding: 10px;">' . htmlspecialchars($estadisticasTiempo['fecha_mas_antiguo']) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px;"><strong><i class="fas fa-calendar-check"></i> Cambio más reciente:</strong></td>
                <td style="padding: 10px;">' . htmlspecialchars($estadisticasTiempo['fecha_mas_reciente']) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px;"><strong><i class="fas fa-calculator"></i> Tiempo total entre cambios:</strong></td>
                <td style="padding: 10px;">' . htmlspecialchars($estadisticasTiempo['tiempo_total']) . '</td>
            </tr>
            <tr>
                <td style="padding: 10px;"><strong><i class="fas fa-clock"></i> Tiempo promedio entre cambios:</strong></td>
                <td style="padding: 10px;">' . htmlspecialchars($estadisticasTiempo['promedio_real']) . '</td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Reporte generado por Sistema ROY - ' . date('d/m/Y H:i:s') . '</p>
        <p class="no-print">
            <button onclick="window.print()"><i class="fas fa-print"></i> Imprimir / Guardar como PDF</button>
            <button onclick="window.close()"><i class="fas fa-times"></i> Cerrar</button>
        </p>
    </div>
    
    </body>
    </html>';
    
    exit;
}

function generarReporteFiltrado($conn, $fecha_inicial, $fecha_final, $filtro_tienda, $filtro_supervisor, $filtro_puesto, $incluir_aprobaciones, $incluir_estados, $usuario_logueado) {
    
    //  OBTENER NOMBRE DEL SUPERVISOR SI SE FILTRA POR CÓDIGO
    $nombre_supervisor_filtrado = '';
    $codigo_supervisor_real = '';
    if (!empty($filtro_supervisor)) {
        $queryNombreSup = "SELECT udf1_string as codigo, udf2_string as nombre 
                          FROM RPS.STORE 
                          WHERE udf1_string = :filtro_supervisor 
                          AND sbs_sid = '680861302000159257' 
                          AND ROWNUM = 1";
        
        $stmtNombreSup = oci_parse($conn, $queryNombreSup);
        oci_bind_by_name($stmtNombreSup, ':filtro_supervisor', $filtro_supervisor);
        oci_execute($stmtNombreSup);
        
        if ($rowSup = oci_fetch_assoc($stmtNombreSup)) {
            $nombre_supervisor_filtrado = trim($rowSup['NOMBRE']);
            $codigo_supervisor_real = trim($rowSup['CODIGO']);
            error_log("✅ Supervisor para reporte - Código: $codigo_supervisor_real, Nombre: $nombre_supervisor_filtrado");
        } else {
            return ['error' => 'No se encontró supervisor con código: ' . $filtro_supervisor];
        }
        oci_free_statement($stmtNombreSup);
    }
    
    //CONSTRUIR QUERY PARA REPORTE FILTRADO
    $whereConditions = [];
    $joinConditions = [];
    
    $whereConditions[] = "h.FECHA_CAMBIO BETWEEN TO_DATE(:fecha_inicial, 'YYYY-MM-DD') AND TO_DATE(:fecha_final, 'YYYY-MM-DD') + 1";
    
    // Filtro de usuario (gerentes)
    if (in_array($usuario_logueado, ['5333', '5210'])) {
        $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
        $nombre_gerente = $gerente_nombres[$usuario_logueado];
        $joinConditions[] = "LEFT JOIN RPS.STORE rps_gerente ON rps_gerente.udf2_string = s.SOLICITADO_POR AND rps_gerente.sbs_sid = '680861302000159257'";
        $whereConditions[] = "UPPER(TRIM(rps_gerente.udf4_string)) = UPPER(TRIM(:nombre_gerente))";
    }
    
    // Filtros específicos
    if (!empty($filtro_tienda)) {
        $whereConditions[] = "s.NUM_TIENDA = :filtro_tienda";
    }
    
    if (!empty($nombre_supervisor_filtrado)) {
        $whereConditions[] = "s.SOLICITADO_POR = :nombre_supervisor_filtrado";
    }
    
    if (!empty($filtro_puesto)) {
        $whereConditions[] = "s.PUESTO_SOLICITADO = :filtro_puesto";
    }
    
    $joinClause = implode(' ', $joinConditions);
    $whereClause = implode(' AND ', $whereConditions);
    
    $query = "SELECT 
                h.ID_HISTORICO,
                h.ID_SOLICITUD,
                s.NUM_TIENDA,
                s.PUESTO_SOLICITADO,
                s.SOLICITADO_POR,
                h.ESTADO_ANTERIOR,
                h.ESTADO_NUEVO,
                h.APROBACION_ANTERIOR,
                h.APROBACION_NUEVA,
                h.COMENTARIO_NUEVO,
                TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                rps_info.udf1_string as CODIGO_SUPERVISOR
              FROM ROY_HISTORICO_SOLICITUD h
              INNER JOIN ROY_SOLICITUD_PERSONAL s ON h.ID_SOLICITUD = s.ID_SOLICITUD
              LEFT JOIN RPS.STORE rps_info ON rps_info.udf2_string = s.SOLICITADO_POR AND rps_info.sbs_sid = '680861302000159257'
              $joinClause
              WHERE $whereClause
              ORDER BY h.FECHA_CAMBIO DESC";
    
    $stmt = oci_parse($conn, $query);
    
    // Bind parámetros
    oci_bind_by_name($stmt, ':fecha_inicial', $fecha_inicial);
    oci_bind_by_name($stmt, ':fecha_final', $fecha_final);
    
    if (!empty($filtro_tienda)) {
        oci_bind_by_name($stmt, ':filtro_tienda', $filtro_tienda);
    }
    
    if (!empty($nombre_supervisor_filtrado)) {
        oci_bind_by_name($stmt, ':nombre_supervisor_filtrado', $nombre_supervisor_filtrado);
    }
    
    if (!empty($filtro_puesto)) {
        oci_bind_by_name($stmt, ':filtro_puesto', $filtro_puesto);
    }
    
    if (in_array($usuario_logueado, ['5333', '5210'])) {
        $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
        $nombre_gerente = $gerente_nombres[$usuario_logueado];
        oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
    }
    
    if (!oci_execute($stmt)) {
        $error = oci_error($stmt);
        oci_free_statement($stmt);
        return ['error' => 'Error en consulta filtrada: ' . $error['message']];
    }
    
    $historial = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $incluir_registro = false;
        
        if ($incluir_aprobaciones && $incluir_estados) {
            $incluir_registro = true;
        } elseif ($incluir_aprobaciones && !$incluir_estados) {
            $incluir_registro = ($row['APROBACION_ANTERIOR'] !== $row['APROBACION_NUEVA']);
        } elseif (!$incluir_aprobaciones && $incluir_estados) {
            $incluir_registro = ($row['ESTADO_ANTERIOR'] !== $row['ESTADO_NUEVO']);
        }
        
        if ($incluir_registro) {
            $historial[] = $row;
        }
    }
    
    oci_free_statement($stmt);
    
    // GENERAR METADATA DEL REPORTE FILTRADO
    $filtros_aplicados = [];
    if (!empty($filtro_tienda)) $filtros_aplicados[] = "Tienda: $filtro_tienda";
    if (!empty($filtro_supervisor)) $filtros_aplicados[] = "Supervisor: $nombre_supervisor_filtrado ($codigo_supervisor_real)";
    if (!empty($filtro_puesto)) $filtros_aplicados[] = "Puesto: $filtro_puesto";
    
    return [
        'success' => true,
        'tipo_reporte' => 'filtrado',
        'registros' => count($historial),
        'filtros_aplicados' => $filtros_aplicados,
        'periodo' => "$fecha_inicial - $fecha_final",
        'fecha_inicial' => $fecha_inicial,
        'fecha_final' => $fecha_final,
        'datos' => $historial,
        'incluye_aprobaciones' => $incluir_aprobaciones,
        'incluye_estados' => $incluir_estados,
        'titulo_reporte' => 'Historial Filtrado de Solicitudes',
        'subtitulo' => 'Filtros: ' . implode(' | ', $filtros_aplicados),
        'headers_excel' => [
            'ID_SOLICITUD' => 'ID Solicitud',
            'NUM_TIENDA' => 'Tienda',
            'PUESTO_SOLICITADO' => 'Puesto',
            'SOLICITADO_POR' => 'Supervisor',
            'ESTADO_ANTERIOR' => 'Estado Anterior',
            'ESTADO_NUEVO' => 'Estado Nuevo',
            'APROBACION_ANTERIOR' => 'Aprobación Anterior',
            'APROBACION_NUEVA' => 'Aprobación Nueva',
            'COMENTARIO_NUEVO' => 'Comentario',
            'FECHA_CAMBIO' => 'Fecha Cambio'
        ]
    ];
}

//  FUNCIÓN PARA REPORTE GENERAL
function generarReporteGeneral($conn, $fecha_inicial, $fecha_final, $incluir_aprobaciones, $incluir_estados, $usuario_logueado) {
    
    $whereConditions = [];
    $joinConditions = [];
    
    $whereConditions[] = "h.FECHA_CAMBIO BETWEEN TO_DATE(:fecha_inicial, 'YYYY-MM-DD') AND TO_DATE(:fecha_final, 'YYYY-MM-DD') + 1";
    
    // Filtro de usuario (gerentes)
    if (in_array($usuario_logueado, ['5333', '5210'])) {
        $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
        $nombre_gerente = $gerente_nombres[$usuario_logueado];
        $joinConditions[] = "LEFT JOIN RPS.STORE rps_gerente ON rps_gerente.udf2_string = s.SOLICITADO_POR AND rps_gerente.sbs_sid = '680861302000159257'";
        $whereConditions[] = "UPPER(TRIM(rps_gerente.udf4_string)) = UPPER(TRIM(:nombre_gerente))";
    }
    
    $joinClause = implode(' ', $joinConditions);
    $whereClause = implode(' AND ', $whereConditions);
    
    // QUERY PARA RESUMEN GENERAL
    $queryResumen = "SELECT 
                        COUNT(*) as TOTAL_CAMBIOS,
                        COUNT(DISTINCT s.ID_SOLICITUD) as SOLICITUDES_AFECTADAS,
                        COUNT(DISTINCT s.NUM_TIENDA) as TIENDAS_AFECTADAS,
                        COUNT(DISTINCT s.SOLICITADO_POR) as SUPERVISORES_AFECTADOS
                     FROM ROY_HISTORICO_SOLICITUD h
                     INNER JOIN ROY_SOLICITUD_PERSONAL s ON h.ID_SOLICITUD = s.ID_SOLICITUD
                     $joinClause
                     WHERE $whereClause";
    
    $stmtResumen = oci_parse($conn, $queryResumen);
    oci_bind_by_name($stmtResumen, ':fecha_inicial', $fecha_inicial);
    oci_bind_by_name($stmtResumen, ':fecha_final', $fecha_final);
    
    if (in_array($usuario_logueado, ['5333', '5210'])) {
        $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
        $nombre_gerente = $gerente_nombres[$usuario_logueado];
        oci_bind_by_name($stmtResumen, ':nombre_gerente', $nombre_gerente);
    }
    
    oci_execute($stmtResumen);
    $resumen = oci_fetch_assoc($stmtResumen);
    oci_free_statement($stmtResumen);
    
    // QUERY PARA DATOS DETALLADOS
    $queryDetalle = "SELECT 
                        h.ID_HISTORICO,
                        h.ID_SOLICITUD,
                        s.NUM_TIENDA,
                        s.PUESTO_SOLICITADO,
                        s.SOLICITADO_POR,
                        h.ESTADO_ANTERIOR,
                        h.ESTADO_NUEVO,
                        h.APROBACION_ANTERIOR,
                        h.APROBACION_NUEVA,
                        h.COMENTARIO_NUEVO,
                        TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                        rps_info.udf1_string as CODIGO_SUPERVISOR
                     FROM ROY_HISTORICO_SOLICITUD h
                     INNER JOIN ROY_SOLICITUD_PERSONAL s ON h.ID_SOLICITUD = s.ID_SOLICITUD
                     LEFT JOIN RPS.STORE rps_info ON rps_info.udf2_string = s.SOLICITADO_POR AND rps_info.sbs_sid = '680861302000159257'
                     $joinClause
                     WHERE $whereClause
                     ORDER BY h.FECHA_CAMBIO DESC";
    
    $stmtDetalle = oci_parse($conn, $queryDetalle);
    oci_bind_by_name($stmtDetalle, ':fecha_inicial', $fecha_inicial);
    oci_bind_by_name($stmtDetalle, ':fecha_final', $fecha_final);
    
    if (in_array($usuario_logueado, ['5333', '5210'])) {
        $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
        $nombre_gerente = $gerente_nombres[$usuario_logueado];
        oci_bind_by_name($stmtDetalle, ':nombre_gerente', $nombre_gerente);
    }
    
    if (!oci_execute($stmtDetalle)) {
        $error = oci_error($stmtDetalle);
        oci_free_statement($stmtDetalle);
        return ['error' => 'Error en consulta general: ' . $error['message']];
    }
    
    $historial = [];
    while ($row = oci_fetch_assoc($stmtDetalle)) {
        $incluir_registro = false;
        
        if ($incluir_aprobaciones && $incluir_estados) {
            $incluir_registro = true;
        } elseif ($incluir_aprobaciones && !$incluir_estados) {
            $incluir_registro = ($row['APROBACION_ANTERIOR'] !== $row['APROBACION_NUEVA']);
        } elseif (!$incluir_aprobaciones && $incluir_estados) {
            $incluir_registro = ($row['ESTADO_ANTERIOR'] !== $row['ESTADO_NUEVO']);
        }
        
        if ($incluir_registro) {
            $historial[] = $row;
        }
    }
    
    oci_free_statement($stmtDetalle);
    
    return [
        'success' => true,
        'tipo_reporte' => 'general',
        'registros' => count($historial),
        'resumen' => [
            'total_cambios' => $resumen['TOTAL_CAMBIOS'],
            'solicitudes_afectadas' => $resumen['SOLICITUDES_AFECTADAS'],
            'tiendas_afectadas' => $resumen['TIENDAS_AFECTADAS'],
            'supervisores_afectados' => $resumen['SUPERVISORES_AFECTADOS']
        ],
        'periodo' => "$fecha_inicial - $fecha_final",
        'fecha_inicial' => $fecha_inicial,
        'fecha_final' => $fecha_final,
        'datos' => $historial,
        'incluye_aprobaciones' => $incluir_aprobaciones,
        'incluye_estados' => $incluir_estados,
        'titulo_reporte' => 'Historial General de Solicitudes',
        'subtitulo' => 'Período: ' . date('d/m/Y', strtotime($fecha_inicial)) . ' - ' . date('d/m/Y', strtotime($fecha_final)),
        'headers_excel' => [
            'ID_SOLICITUD' => 'ID Solicitud',
            'NUM_TIENDA' => 'Tienda',
            'PUESTO_SOLICITADO' => 'Puesto',
            'SOLICITADO_POR' => 'Supervisor',
            'ESTADO_ANTERIOR' => 'Estado Anterior',
            'ESTADO_NUEVO' => 'Estado Nuevo',
            'APROBACION_ANTERIOR' => 'Aprobación Anterior',
            'APROBACION_NUEVA' => 'Aprobación Nueva',
            'COMENTARIO_NUEVO' => 'Comentario',
            'FECHA_CAMBIO' => 'Fecha Cambio'
        ],
        'estadisticas_adicionales' => [
            'cambios_por_tipo' => getCambiosPorTipo($historial),
            'actividad_por_tienda' => getActividadPorTienda($historial),
            'cambios_por_dia' => getCambiosPorDia($historial)
        ]
    ];
}

//  FUNCIONES AUXILIARES PARA ESTADÍSTICAS
function getCambiosPorTipo($historial) {
    $tipos = [
        'solo_estado' => 0,
        'solo_aprobacion' => 0,
        'ambos' => 0
    ];
    
    foreach ($historial as $registro) {
        $cambio_estado = $registro['ESTADO_ANTERIOR'] !== $registro['ESTADO_NUEVO'];
        $cambio_aprobacion = $registro['APROBACION_ANTERIOR'] !== $registro['APROBACION_NUEVA'];
        
        if ($cambio_estado && $cambio_aprobacion) {
            $tipos['ambos']++;
        } elseif ($cambio_estado) {
            $tipos['solo_estado']++;
        } elseif ($cambio_aprobacion) {
            $tipos['solo_aprobacion']++;
        }
    }
    
    return $tipos;
}

function getActividadPorTienda($historial) {
    $tiendas = [];
    
    foreach ($historial as $registro) {
        $tienda = $registro['NUM_TIENDA'];
        if (!isset($tiendas[$tienda])) {
            $tiendas[$tienda] = [
                'tienda' => $tienda,
                'supervisor' => $registro['SOLICITADO_POR'],
                'cambios' => 0,
                'solicitudes' => []
            ];
        }
        $tiendas[$tienda]['cambios']++;
        $tiendas[$tienda]['solicitudes'][] = $registro['ID_SOLICITUD'];
    }
    
    // Contar solicitudes únicas por tienda
    foreach ($tiendas as &$tienda) {
        $tienda['solicitudes_unicas'] = count(array_unique($tienda['solicitudes']));
        unset($tienda['solicitudes']); // No necesitamos el array completo en la respuesta
    }
    
    // Ordenar por mayor actividad
    usort($tiendas, function($a, $b) {
        return $b['cambios'] - $a['cambios'];
    });
    
    return array_slice($tiendas, 0, 10); // Top 10 tiendas más activas
}

function getCambiosPorDia($historial) {
    $dias = [];
    
    foreach ($historial as $registro) {
        $fecha = substr($registro['FECHA_CAMBIO'], 0, 10); // DD-MM-YYYY
        if (!isset($dias[$fecha])) {
            $dias[$fecha] = 0;
        }
        $dias[$fecha]++;
    }
    
    // Ordenar por fecha
    ksort($dias);
    
    return $dias;
}

//=======================================================================
//INICIALIZACION DE SISTEMA 
//=======================================================================
session_start();

if (!isset($_SESSION['user'][12])) {
    enviarJSON(['success' => false, 'error' => 'No autenticado']);
}

if (!isset($_GET['action'])) {
    enviarJSON(['success' => false, 'error' => 'No action specified']);
}

require_once '../../Funsiones/global.php';
include_once '../../Funsiones/conexion.php';

$conn = Oracle();
if (!$conn) {
    enviarJSON(['success' => false, 'error' => 'Sin conexión Oracle']);
}

$usuario_logueado = $_SESSION['user'][12];

//  USUARIOS AUTORIZADOS: GERENTES + INFORMÁTICA
$gerentes_validos = ['5333', '5210'];
$informatica_usuarios = ['5407', '5202']; // ← AGREGAR TUS CÓDIGOS DE INFORMÁTICA AQUÍ
$usuarios_autorizados = array_merge($gerentes_validos, $informatica_usuarios);

if (!in_array($usuario_logueado, $usuarios_autorizados)) {
    enviarJSON(['success' => false, 'error' => 'Acceso denegado']);
}

$action = $_GET['action'];

try {
    switch ($action) {
        
        // ===================================================================
        // OBTENER SUPERVISORES ASIGNADOS (MANTENEMOS EL CÓDIGO ORIGINAL)
        // ===================================================================
        case 'get_supervisores_asignados':
            $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
            
            // Si es informática, mostrar todos los supervisores
            if (in_array($usuario_logueado, ['5407', '5202'])) {
                $query = "SELECT CODIGO_SUPERVISOR, NOMBRE_SUPERVISOR, EMAIL_SUPERVISOR, NOMBRE_GERENTE, EMAIL_GERENTE
                          FROM (
                              SELECT udf1_string AS CODIGO_SUPERVISOR, udf2_string AS NOMBRE_SUPERVISOR,
                                     udf3_string AS EMAIL_SUPERVISOR, udf4_string AS NOMBRE_GERENTE,
                                     udf5_string AS EMAIL_GERENTE,
                                     ROW_NUMBER() OVER (PARTITION BY udf1_string ORDER BY STORE_NO) AS rn
                              FROM RPS.STORE
                              WHERE udf1_string IS NOT NULL AND udf2_string IS NOT NULL
                              AND sbs_sid = '680861302000159257'
                          ) WHERE rn = 1 ORDER BY NOMBRE_SUPERVISOR";
                          
                $stmt = oci_parse($conn, $query);
            } else {
                $nombre_gerente = $gerente_nombres[$usuario_logueado];
                
                $query = "SELECT CODIGO_SUPERVISOR, NOMBRE_SUPERVISOR, EMAIL_SUPERVISOR, NOMBRE_GERENTE, EMAIL_GERENTE
                          FROM (
                              SELECT udf1_string AS CODIGO_SUPERVISOR, udf2_string AS NOMBRE_SUPERVISOR,
                                     udf3_string AS EMAIL_SUPERVISOR, udf4_string AS NOMBRE_GERENTE,
                                     udf5_string AS EMAIL_GERENTE,
                                     ROW_NUMBER() OVER (PARTITION BY udf1_string ORDER BY STORE_NO) AS rn
                              FROM RPS.STORE
                              WHERE UPPER(TRIM(udf4_string)) = UPPER(TRIM(:nombre_gerente))
                              AND udf1_string IS NOT NULL AND udf2_string IS NOT NULL
                              AND sbs_sid = '680861302000159257'
                          ) WHERE rn = 1 ORDER BY NOMBRE_SUPERVISOR";

                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
            }
            
            if (!$stmt) {
                manejarError('Error preparando consulta supervisores');
            }
            
            if (!oci_execute($stmt)) {
                manejarError('Error ejecutando consulta supervisores', oci_error($stmt));
            }

            $supervisores = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $rowLimpia = [];
                foreach ($row as $key => $value) {
                    $rowLimpia[trim($key)] = is_string($value) ? trim($value) : $value;
                }
                
                // Obtener tiendas del supervisor
                $queryTiendas = "SELECT STORE_NO FROM RPS.STORE 
                                WHERE udf1_string = :codigo_supervisor 
                                AND sbs_sid = '680861302000159257' AND STORE_NO IS NOT NULL
                                ORDER BY STORE_NO";
                
                $stmtTiendas = oci_parse($conn, $queryTiendas);
                if ($stmtTiendas) {
                    oci_bind_by_name($stmtTiendas, ':codigo_supervisor', $rowLimpia['CODIGO_SUPERVISOR']);
                    if (oci_execute($stmtTiendas)) {
                        $tiendas = [];
                        while ($tiendaRow = oci_fetch_assoc($stmtTiendas)) {
                            $tiendas[] = trim($tiendaRow['STORE_NO']);
                        }
                        oci_free_statement($stmtTiendas);
                    } else {
                        $tiendas = [];
                    }
                } else {
                    $tiendas = [];
                }

                $supervisores[] = [
                    'codigo' => $rowLimpia['CODIGO_SUPERVISOR'] ?? '',
                    'nombre' => $rowLimpia['NOMBRE_SUPERVISOR'] ?? '',
                    'email' => $rowLimpia['EMAIL_SUPERVISOR'] ?? '',
                    'gerente' => $rowLimpia['NOMBRE_GERENTE'] ?? '',
                    'email_gerente' => $rowLimpia['EMAIL_GERENTE'] ?? '',
                    'tiendas_asignadas' => $tiendas,
                    'total_tiendas' => count($tiendas)
                ];
            }
            oci_free_statement($stmt);
            
            registrarHistorial($conn, 'GET_SUPERVISORES', ['count' => count($supervisores)]);
            enviarJSON(['success' => true, 'supervisores' => $supervisores]);
            break;

        // ===================================================================
        // OBTENER TIENDAS DE SUPERVISOR (CÓDIGO ORIGINAL)
        // ===================================================================
        case 'get_tiendas_supervisor':
            $codigo_supervisor = trim($_GET['codigo_supervisor'] ?? '');
            if (empty($codigo_supervisor)) {
                manejarError('Código supervisor requerido');
            }

            $query = "SELECT STORE_NO AS NUM_TIENDA, STORE_NAME AS NOMBRE_TIENDA
                     FROM RPS.STORE WHERE udf1_string = :codigo_supervisor
                     AND sbs_sid = '680861302000159257' AND STORE_NO IS NOT NULL
                     ORDER BY STORE_NO";

            $stmt = oci_parse($conn, $query);
            if (!$stmt) {
                manejarError('Error preparando consulta tiendas');
            }
            
            oci_bind_by_name($stmt, ':codigo_supervisor', $codigo_supervisor);
            
            if (!oci_execute($stmt)) {
                manejarError('Error ejecutando consulta tiendas', oci_error($stmt));
            }

            $tiendas = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $tiendas[] = [
                    'numero' => trim($row['NUM_TIENDA']),
                    'nombre' => trim($row['NOMBRE_TIENDA'] ?? ('Tienda ' . $row['NUM_TIENDA']))
                ];
            }
            oci_free_statement($stmt);
            
            enviarJSON(['success' => true, 'tiendas' => $tiendas]);
            break;

        // ===================================================================
        // ✅ OBTENER SOLICITUDES CON FILTRO POR SUPERVISORES ASIGNADOS
        // ===================================================================
        case 'get_solicitudes_gerentes':
            try {
                if (ob_get_level()) ob_clean();

                // 🔍 DEBUG - VERIFICAR USUARIO LOGUEADO
                error_log("🔍 DEBUG - Usuario logueado: " . $usuario_logueado);
                error_log("🔍 DEBUG - Session completa: " . print_r($_SESSION, true));
                
                $filtro_estado = $_GET['estado_aprobacion'] ?? '';
                $filtro_gerente = $_GET['dirigido_a'] ?? '';
                
                error_log("🔍 Usuario: $usuario_logueado, Filtros - Estado: '$filtro_estado', Gerente: '$filtro_gerente'");

                // ✅ DEFINIR USUARIOS AUTORIZADOS Y SUS ROLES
                $gerentes_validos = ['5333', '5210'];
                $informatica_usuarios = ['5407', '5202']; // ← AGREGAR TUS CÓDIGOS DE INFORMÁTICA AQUÍ
                $usuarios_autorizados = array_merge($gerentes_validos, $informatica_usuarios);

                if (!in_array($usuario_logueado, $usuarios_autorizados)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
                    exit;
                }

                // ✅ DETERMINAR TIPO DE USUARIO
                $es_informatica = in_array($usuario_logueado, $informatica_usuarios);
                $es_gerente = in_array($usuario_logueado, $gerentes_validos);

                error_log("🔍 DEBUG - Es informática: " . ($es_informatica ? 'SÍ' : 'NO'));
                error_log("🔍 DEBUG - Es gerente: " . ($es_gerente ? 'SÍ' : 'NO'));

                // ✅ BASE QUERY (IGUAL QUE SUPERVISORES)
                $baseQuery = "
                    SELECT
                        s.ID_SOLICITUD,
                        s.NUM_TIENDA,
                        s.PUESTO_SOLICITADO,
                        s.ESTADO_SOLICITUD,
                        s.ESTADO_APROBACION,
                        s.DIRIGIDO_RH,
                        TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD,
                        CASE 
                            WHEN s.FECHA_MODIFICACION != s.FECHA_SOLICITUD 
                            THEN TO_CHAR(s.FECHA_MODIFICACION, 'DD-MM-YYYY HH24:MI:SS')
                            ELSE NULL
                        END AS FECHA_MODIFICACION,
                        s.SOLICITADO_POR,
                        s.RAZON,
                        s.DIRIGIDO_A,
                        CASE
                            WHEN EXISTS (
                                SELECT 1 
                                FROM ROY_ARCHIVOS_SOLICITUD a
                                JOIN ROY_HISTORICO_SOLICITUD h ON a.ID_HISTORICO = h.ID_HISTORICO
                                WHERE a.ID_SOLICITUD = s.ID_SOLICITUD
                                AND LOWER(h.ESTADO_NUEVO) LIKE '%cvs%'
                                AND h.ID_HISTORICO = (
                                    SELECT MAX(ID_HISTORICO)
                                    FROM ROY_HISTORICO_SOLICITUD
                                    WHERE ID_SOLICITUD = s.ID_SOLICITUD
                                    AND LOWER(ESTADO_NUEVO) LIKE '%cvs%'
                                )
                            ) THEN 1 ELSE 0
                        END AS TIENE_ARCHIVOS,
                        CASE 
                            WHEN s.ESTADO_SOLICITUD = 'Con CVs Disponibles' THEN 1
                            ELSE 0
                        END AS CVS_DISPONIBLES,
                        (
                            SELECT CASE
                                WHEN COUNT(*) > 0 THEN 1 ELSE 0
                            END
                            FROM ROY_SELECCION_CVS sc
                            JOIN (
                                SELECT MAX(ID_HISTORICO) AS ID_HISTORICO
                                FROM ROY_HISTORICO_SOLICITUD
                                WHERE ID_SOLICITUD = s.ID_SOLICITUD
                                AND LOWER(ESTADO_NUEVO) LIKE '%cvs%'
                            ) h_cvs ON sc.ID_HISTORICO_CV_ENVIO = h_cvs.ID_HISTORICO
                            WHERE sc.ID_SOLICITUD = s.ID_SOLICITUD
                            AND sc.ES_ACTIVA = 'Y'
                        ) AS TIENE_SELECCION,
                        h.ID_HISTORICO,
                        h.COMENTARIO_NUEVO,
                        h.COMENTARIO_ANTERIOR,
                        (
                            SELECT COUNT(*) 
                            FROM ROY_CHAT_HISTORICO ch 
                            WHERE ch.ID_HISTORICO = h.ID_HISTORICO
                        ) AS TOTAL_MENSAJES,
                        (
                            SELECT COUNT(*)
                            FROM ROY_CHAT_HISTORICO ch
                            WHERE ch.ID_HISTORICO = h.ID_HISTORICO
                            AND UPPER(ch.ES_LEIDO) = 'N'
                            AND UPPER(ch.ROL) = 'RRHH'
                        ) AS NO_LEIDOS
                    FROM ROY_SOLICITUD_PERSONAL s
                    LEFT JOIN (
                        SELECT ID_HISTORICO, ID_SOLICITUD, COMENTARIO_NUEVO, COMENTARIO_ANTERIOR
                        FROM (
                            SELECT h.*, ROW_NUMBER() OVER (PARTITION BY ID_SOLICITUD ORDER BY FECHA_CAMBIO DESC) AS rn
                            FROM ROY_HISTORICO_SOLICITUD h
                        )
                        WHERE rn = 1
                    ) h ON s.ID_SOLICITUD = h.ID_SOLICITUD
                ";

                if ($es_informatica) {
                    // ✅ INFORMÁTICA VE TODAS LAS SOLICITUDES
                    error_log("🔍 DEBUG - Consulta para INFORMÁTICA: ver todas las solicitudes");
                    $query = "$baseQuery ORDER BY s.FECHA_SOLICITUD DESC";
                    $stmt = oci_parse($conn, $query);
                    
                } else if ($es_gerente) {
                    // ✅ GERENTES VEN SOLO SOLICITUDES DE SUS SUPERVISORES ASIGNADOS
                    $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
                    $nombre_gerente = $gerente_nombres[$usuario_logueado];
                    
                    error_log("🔍 DEBUG - Consulta para GERENTE: $nombre_gerente");
                    
                    // QUERY SIMILAR AL DE SUPERVISORES PERO FILTRADO POR GERENTE
                    $query = "SELECT * FROM ($baseQuery) A
                              INNER JOIN (
                                SELECT store_no, udf1_string, udf2_string, udf4_string 
                                FROM RPS.STORE 
                                WHERE sbs_sid = '680861302000159257' 
                                AND UPPER(TRIM(udf4_string)) = UPPER(TRIM(:nombre_gerente))
                              ) sp ON A.SOLICITADO_POR = sp.udf2_string AND A.NUM_TIENDA = sp.store_no
                              ORDER BY FECHA_SOLICITUD DESC";
                    
                    $stmt = oci_parse($conn, $query);
                    oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
                    
                    error_log("🔍 DEBUG - Query para gerente: " . $query);
                    error_log("🔍 DEBUG - Nombre gerente: " . $nombre_gerente);
                }

                // ✅ AGREGAR FILTROS ADICIONALES SI EXISTEN
                if (!empty($filtro_estado) || !empty($filtro_gerente)) {
                    $whereConditions = [];
                    
                    if (!empty($filtro_estado)) {
                        $whereConditions[] = "ESTADO_APROBACION = :estado_aprobacion";
                    }
                    
                    if (!empty($filtro_gerente)) {
                        $whereConditions[] = "DIRIGIDO_A = :dirigido_a";
                    }
                    
                    if (!empty($whereConditions)) {
                        // Modificar query para agregar WHERE adicional
                        $query = "SELECT * FROM ($query) WHERE " . implode(' AND ', $whereConditions);
                        $stmt = oci_parse($conn, $query);
                        
                        // Re-bind parámetros del gerente si es necesario
                        if ($es_gerente) {
                            oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
                        }
                        
                        // Bind filtros adicionales
                        if (!empty($filtro_estado)) {
                            oci_bind_by_name($stmt, ':estado_aprobacion', $filtro_estado);
                        }
                        if (!empty($filtro_gerente)) {
                            oci_bind_by_name($stmt, ':dirigido_a', $filtro_gerente);
                        }
                    }
                }

                if (!oci_execute($stmt)) {
                    $error = oci_error($stmt);
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => $error['message']]);
                    exit;
                }

                $solicitudes = [];
                while ($row = oci_fetch_assoc($stmt)) {
                    $solicitudes[] = [
                        'ID_SOLICITUD' => $row['ID_SOLICITUD'],
                        'NUM_TIENDA' => $row['NUM_TIENDA'],
                        'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
                        'ESTADO_SOLICITUD' => $row['ESTADO_SOLICITUD'],
                        'ESTADO_APROBACION' => $row['ESTADO_APROBACION'] ?: 'Por Aprobar',
                        'DIRIGIDO_RH' => $row['DIRIGIDO_RH'],
                        'FECHA_SOLICITUD' => $row['FECHA_SOLICITUD'],
                        'FECHA_MODIFICACION' => $row['FECHA_MODIFICACION'],
                        'SOLICITADO_POR' => $row['SOLICITADO_POR'],
                        'RAZON' => $row['RAZON'],
                        'DIRIGIDO_A' => $row['DIRIGIDO_A'],
                        'TIENE_ARCHIVOS' => $row['TIENE_ARCHIVOS'],
                        'CVS_DISPONIBLES' => $row['CVS_DISPONIBLES'],
                        'ID_HISTORICO' => $row['ID_HISTORICO'],
                        'COMENTARIO_NUEVO' => $row['COMENTARIO_NUEVO'],
                        'TIENE_SELECCION' => $row['TIENE_SELECCION'],
                        'NO_LEIDOS' => $row['NO_LEIDOS']
                    ];
                }

                oci_free_statement($stmt);
                oci_close($conn);

                error_log("✅ Solicitudes para usuario $usuario_logueado: " . count($solicitudes));
                
                header('Content-Type: application/json');
                echo json_encode($solicitudes);
                
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        // ===================================================================
        // RESTO DE CASES DEL SEGUNDO CÓDIGO (MANTENEMOS TODOS)
        // ===================================================================
        
                    case 'procesar_aprobacion_gerente':
                        error_log("Procesando cambio de aprobacion con comentarios obligatorios...");
                        error_log("POST data: " . print_r($_POST, true));
                        
                        // 🆕 VALIDACIONES MEJORADAS
                        if (empty($_POST['id_solicitud']) || empty($_POST['nueva_aprobacion'])) {
                            error_log("Faltan datos obligatorios");
                            echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios: ID solicitud y nueva aprobación']);
                            break;
                        }

                        $id = $_POST['id_solicitud'];
                        $nueva_aprobacion = $_POST['nueva_aprobacion'];
                        $comentario = $_POST['comentario'] ?? '';
                        $dirigido_rh = $_POST['dirigido_rh'] ?? null;
                        $tipo_comentario = $_POST['tipo_comentario'] ?? 'general';
                        
                        // 🆕 VALIDACIÓN DE COMENTARIOS OBLIGATORIOS
                        if ($nueva_aprobacion === 'Aprobado') {
                            if (empty($dirigido_rh)) {
                                echo json_encode(['success' => false, 'error' => 'Para aprobar una solicitud debe seleccionar una persona de RRHH']);
                                break;
                            }
                            if (empty($comentario) || strlen(trim($comentario)) < 10) {
                                echo json_encode(['success' => false, 'error' => 'Para aprobar una solicitud debe proporcionar un comentario explicativo de al menos 10 caracteres']);
                                break;
                            }
                        } elseif ($nueva_aprobacion === 'No Aprobado') {
                            if (empty($comentario) || strlen(trim($comentario)) < 10) {
                                echo json_encode(['success' => false, 'error' => 'Para rechazar una solicitud debe proporcionar un motivo de al menos 10 caracteres']);
                                break;
                            }
                        }
                        
                        // 🆕 OBTENER INFORMACIÓN DEL GERENTE CORRECTAMENTE
                        $codigo_gerente = $_SESSION['user'][12] ?? null;
                        
                        // ✅ MAPEO DE CÓDIGOS A NOMBRES DE GERENTES
                        $gerente_nombres = [
                            '5333' => 'Christian Quan', 
                            '5210' => 'Giovanni Cardoza'
                        ];
                        
                        // ✅ OBTENER NOMBRE DEL GERENTE
                        if ($codigo_gerente && isset($gerente_nombres[$codigo_gerente])) {
                            $nombre_gerente = $gerente_nombres[$codigo_gerente];
                            error_log("✅ Gerente identificado: $nombre_gerente (código: $codigo_gerente)");
                        } else {
                            // Fallback para otros usuarios
                            $nombre_gerente = $_SESSION['user'][2] ?? 'Sistema';
                            error_log("⚠️ Gerente no identificado, usando fallback: $nombre_gerente");
                        }

                        error_log("Datos: ID=$id, Nueva Aprobación=$nueva_aprobacion, Dirigido RH=$dirigido_rh, Gerente=$nombre_gerente, Tipo Comentario=$tipo_comentario");

                        try {
                            // ✅ INICIAR TRANSACCIÓN
                            oci_execute(oci_parse($conn, "SAVEPOINT inicio_aprobacion"), OCI_NO_AUTO_COMMIT);
                            
                            // Obtener aprobación anterior para el historial tradicional
                            $queryAnterior = "SELECT ESTADO_APROBACION FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
                            $stmtAnt = oci_parse($conn, $queryAnterior);
                            oci_bind_by_name($stmtAnt, ':id', $id);
                            
                            if (!oci_execute($stmtAnt)) {
                                $error = oci_error($stmtAnt);
                                throw new Exception("Error obteniendo estado anterior: " . $error['message']);
                            }
                            
                            $aprobacion_anterior = 'Por Aprobar';
                            if ($row = oci_fetch_assoc($stmtAnt)) {
                                $aprobacion_anterior = $row['ESTADO_APROBACION'] ?: 'Por Aprobar';
                            }
                            oci_free_statement($stmtAnt);

                            // ✅ ACTUALIZAR SOLICITUD PRINCIPAL
                            if ($nueva_aprobacion == 'Aprobado' && $dirigido_rh) {
                                $queryUpdate = "UPDATE ROY_SOLICITUD_PERSONAL SET 
                                                ESTADO_APROBACION = :aprobacion,
                                                DIRIGIDO_RH = :dirigido_rh,
                                                FECHA_MODIFICACION = SYSDATE 
                                                WHERE ID_SOLICITUD = :id";
                                $stmtUpd = oci_parse($conn, $queryUpdate);
                                oci_bind_by_name($stmtUpd, ':aprobacion', $nueva_aprobacion);
                                oci_bind_by_name($stmtUpd, ':dirigido_rh', $dirigido_rh);
                                oci_bind_by_name($stmtUpd, ':id', $id);
                            } else {
                                $queryUpdate = "UPDATE ROY_SOLICITUD_PERSONAL SET 
                                                ESTADO_APROBACION = :aprobacion, 
                                                FECHA_MODIFICACION = SYSDATE 
                                                WHERE ID_SOLICITUD = :id";
                                $stmtUpd = oci_parse($conn, $queryUpdate);
                                oci_bind_by_name($stmtUpd, ':aprobacion', $nueva_aprobacion);
                                oci_bind_by_name($stmtUpd, ':id', $id);
                            }
                            
                            if (!oci_execute($stmtUpd, OCI_NO_AUTO_COMMIT)) {
                                $error = oci_error($stmtUpd);
                                throw new Exception("Error actualizando solicitud: " . $error['message']);
                            }
                            oci_free_statement($stmtUpd);

                            // 🆕 INSERTAR EN TABLA SIMPLIFICADA DE APROBACIONES
                            $decision = '';
                            switch($nueva_aprobacion) {
                                case 'Aprobado': $decision = 'APROBADO'; break;
                                case 'No Aprobado': $decision = 'NO_APROBADO'; break;
                                case 'Por Aprobar': $decision = 'PENDIENTE'; break;
                                default: $decision = 'PENDIENTE'; break;
                            }
                            
                            // 🆕 CONSTRUIR COMENTARIO ESTRUCTURADO
                            $comentario_estructurado = '';
                            if ($nueva_aprobacion === 'Aprobado') {
                                $comentario_estructurado = "APROBACIÓN GERENCIAL\n";
                                $comentario_estructurado .= "Procesado por: $nombre_gerente\n";
                                $comentario_estructurado .= "Asignado a RRHH: $dirigido_rh\n";
                                $comentario_estructurado .= "Comentario de aprobación: $comentario\n";
                                $comentario_estructurado .= "Fecha de procesamiento: " . date('Y-m-d H:i:s');
                            } elseif ($nueva_aprobacion === 'No Aprobado') {
                                $comentario_estructurado = "RECHAZO GERENCIAL\n";
                                $comentario_estructurado .= "Procesado por: $nombre_gerente\n";
                                $comentario_estructurado .= "Motivo del rechazo: $comentario\n";
                                $comentario_estructurado .= "Fecha de procesamiento: " . date('Y-m-d H:i:s');
                            } else {
                                $comentario_estructurado = "CAMBIO DE ESTADO\n";
                                $comentario_estructurado .= "Procesado por: $nombre_gerente\n";
                                $comentario_estructurado .= "Nuevo estado: $nueva_aprobacion\n";
                                if (!empty($comentario)) {
                                    $comentario_estructurado .= "Comentario: $comentario\n";
                                }
                                $comentario_estructurado .= "Fecha de procesamiento: " . date('Y-m-d H:i:s');
                            }
                            
                            // 🆕 VERIFICAR SI YA EXISTE UNA ENTRADA PARA ESTA SOLICITUD
                            $queryExiste = "SELECT COUNT(*) as CUENTA FROM ROY_APROBACIONES_GERENCIA WHERE ID_SOLICITUD = :id_solicitud";
                            $stmtExiste = oci_parse($conn, $queryExiste);
                            oci_bind_by_name($stmtExiste, ':id_solicitud', $id);
                            
                            if (!oci_execute($stmtExiste)) {
                                $error = oci_error($stmtExiste);
                                throw new Exception("Error verificando existencia: " . $error['message']);
                            }
                            
                            $existe = false;
                            if ($row = oci_fetch_assoc($stmtExiste)) {
                                $existe = ($row['CUENTA'] > 0);
                            }
                            oci_free_statement($stmtExiste);
                            
                            if ($existe) {
                                // 🆕 ACTUALIZAR REGISTRO EXISTENTE
                                $queryAprobacion = "UPDATE ROY_APROBACIONES_GERENCIA SET 
                                                    DECISION = :decision,
                                                    COMENTARIO_GERENTE = EMPTY_CLOB(),
                                                    GERENTE = :gerente,
                                                    CODIGO_GERENTE = :codigo_gerente,
                                                    FECHA_DECISION = SYSDATE
                                                    WHERE ID_SOLICITUD = :id_solicitud 
                                                    RETURNING COMENTARIO_GERENTE INTO :comentario_clob";
                                
                                $stmtAprobacion = oci_parse($conn, $queryAprobacion);
                                oci_bind_by_name($stmtAprobacion, ':id_solicitud', $id);
                                oci_bind_by_name($stmtAprobacion, ':decision', $decision);
                                oci_bind_by_name($stmtAprobacion, ':gerente', $nombre_gerente);
                                oci_bind_by_name($stmtAprobacion, ':codigo_gerente', $codigo_gerente);
                            } else {
                                // 🆕 INSERTAR NUEVO REGISTRO
                                $queryAprobacion = "INSERT INTO ROY_APROBACIONES_GERENCIA (
                                    ID_SOLICITUD, 
                                    DECISION,
                                    COMENTARIO_GERENTE,
                                    GERENTE,
                                    CODIGO_GERENTE,
                                    FECHA_DECISION
                                ) VALUES (
                                    :id_solicitud,
                                    :decision,
                                    EMPTY_CLOB(),
                                    :gerente,
                                    :codigo_gerente,
                                    SYSDATE
                                ) RETURNING COMENTARIO_GERENTE INTO :comentario_clob";
                                
                                $stmtAprobacion = oci_parse($conn, $queryAprobacion);
                                oci_bind_by_name($stmtAprobacion, ':id_solicitud', $id);
                                oci_bind_by_name($stmtAprobacion, ':decision', $decision);
                                oci_bind_by_name($stmtAprobacion, ':gerente', $nombre_gerente);
                                oci_bind_by_name($stmtAprobacion, ':codigo_gerente', $codigo_gerente);
                            }
                            
                            // 🆕 CREAR DESCRIPTOR CLOB PARA EL COMENTARIO
                            $comentario_clob = oci_new_descriptor($conn, OCI_D_LOB);
                            oci_bind_by_name($stmtAprobacion, ':comentario_clob', $comentario_clob, -1, OCI_B_CLOB);
                            
                            if (!oci_execute($stmtAprobacion, OCI_NO_AUTO_COMMIT)) {
                                $error = oci_error($stmtAprobacion);
                                throw new Exception("Error procesando aprobación: " . $error['message']);
                            }
                            
                            // 🆕 GUARDAR COMENTARIO ESTRUCTURADO EN CLOB
                            if (!$comentario_clob->save($comentario_estructurado)) {
                                throw new Exception("Error guardando comentario del gerente");
                            }
                            
                            // ✅ MANTENER HISTORIAL TRADICIONAL PARA COMPATIBILIDAD
                            $comentario_historial = $comentario; // Solo el comentario simple para el historial
                            $queryHistorial = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                                (ID_SOLICITUD, APROBACION_ANTERIOR, APROBACION_NUEVA, COMENTARIO_NUEVO, FECHA_CAMBIO)
                                VALUES (:id_solicitud, :aprobacion_anterior, :aprobacion_nueva, :comentario, SYSDATE)";
                            $stmtHist = oci_parse($conn, $queryHistorial);
                            oci_bind_by_name($stmtHist, ':id_solicitud', $id);
                            oci_bind_by_name($stmtHist, ':aprobacion_anterior', $aprobacion_anterior);
                            oci_bind_by_name($stmtHist, ':aprobacion_nueva', $nueva_aprobacion);
                            oci_bind_by_name($stmtHist, ':comentario', $comentario_historial);
                            
                            if (!oci_execute($stmtHist, OCI_NO_AUTO_COMMIT)) {
                                $error = oci_error($stmtHist);
                                throw new Exception("Error insertando historial: " . $error['message']);
                            }
                            oci_free_statement($stmtHist);
                            
                            // ✅ CONFIRMAR TRANSACCIÓN
                            if (!oci_commit($conn)) {
                                throw new Exception("Error en commit de la transacción");
                            }
                            
                            // 🆕 LIBERAR RECURSOS
                            $comentario_clob->free();
                            oci_free_statement($stmtAprobacion);

                            error_log("✅ Aprobación registrada exitosamente con comentario obligatorio");

                            // 🆕 MENSAJE DE RESPUESTA MEJORADO
                            $mensaje = "Estado de aprobación actualizado correctamente de \"$aprobacion_anterior\" a \"$nueva_aprobacion\"";
                            
                            if ($nueva_aprobacion === 'Aprobado') {
                                $mensaje .= " y asignado a: $dirigido_rh";
                                $mensaje .= ". El comentario de aprobación ha sido registrado correctamente.";
                            } elseif ($nueva_aprobacion === 'No Aprobado') {
                                $mensaje .= ". El motivo del rechazo ha sido registrado y será visible para el supervisor.";
                            }

                            echo json_encode([
                                'success' => true,
                                'mensaje' => $mensaje,
                                'decision_registrada' => $decision,
                                'comentario_guardado' => !empty($comentario),
                                'tipo_comentario' => $tipo_comentario,
                                'tabla_utilizada' => 'ROY_APROBACIONES_GERENCIA (Con comentarios obligatorios)',
                                'datos' => [
                                    'gerente' => $nombre_gerente,
                                    'dirigido_rh' => $dirigido_rh,
                                    'tiene_comentario' => !empty($comentario)
                                ]
                            ]);

                        } catch (Exception $e) {
                            error_log("❌ Exception: " . $e->getMessage());
                            oci_rollback($conn);
                            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                        }
                        
                        oci_close($conn);
                        break;
        // ✅ OBTENER LISTAS PARA DROPDOWNS
        case 'get_listas_gerentes':
            try {
                $data = [
                    'gerentes' => ['Christian Quan', 'Giovanni Cardoza'],
                    'asesoras_rrhh' => ['Cristy Garcia', 'Keisha Davila', 'Emma de Cea'],
                    'estados' => [
                        ['value' => '', 'label' => 'Todos los Estados'],
                        ['value' => 'Por Aprobar', 'label' => 'Por Aprobar'],
                        ['value' => 'Aprobado', 'label' => 'Aprobado'],
                        ['value' => 'No Aprobado', 'label' => 'No Aprobado']
                    ]
                ];
                
                echo json_encode($data);
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;


case 'obtener_resumen_aprobacion_gerente':
    $id_solicitud = $_GET['id_solicitud'] ?? $_POST['id_solicitud'];
    
    try {
        // Usar los nombres correctos de las columnas según tu tabla
        $query = "SELECT 
                    s.ID_SOLICITUD,
                    s.NUM_TIENDA,
                    s.PUESTO_SOLICITADO,
                    s.SOLICITADO_POR,
                    s.ESTADO_APROBACION,
                    s.DIRIGIDO_RH,
                    s.FECHA_SOLICITUD,
                    ag.COMENTARIO_GERENTE,
                    ag.GERENTE,
                    ag.CODIGO_GERENTE,
                    TO_CHAR(ag.FECHA_DECISION, 'DD/MM/YYYY HH24:MI:SS') as FECHA_DECISION_FORMATO
                  FROM ROY_SOLICITUD_PERSONAL s
                  LEFT JOIN ROY_APROBACIONES_GERENCIA ag ON s.ID_SOLICITUD = ag.ID_SOLICITUD
                  WHERE s.ID_SOLICITUD = :id_solicitud";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_solicitud', $id_solicitud);
        
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception("Error en consulta: " . $error['message']);
        }
        
        if ($row = oci_fetch_assoc($stmt)) {
            // Leer comentario CLOB
            $comentario_completo = '';
            if ($row['COMENTARIO_GERENTE']) {
                $comentario_completo = $row['COMENTARIO_GERENTE']->read($row['COMENTARIO_GERENTE']->size());
                $row['COMENTARIO_GERENTE']->free();
                
            // 🆕 OBTENER NOMBRE COMPLETO DEL GERENTE
            $nombre_gerente = 'No disponible';
            if (!empty($row['GERENTE'])) {
                $nombre_gerente = $row['GERENTE'];
            } elseif (!empty($row['CODIGO_GERENTE'])) {
                // Mapeo de códigos a nombres si el nombre no viene en GERENTE
                $gerente_nombres = [
                    '5333' => 'Christian Quan', 
                    '5210' => 'Giovanni Cardoza'
                ];
                $nombre_gerente = $gerente_nombres[$row['CODIGO_GERENTE']] ?? 'Gerente código ' . $row['CODIGO_GERENTE'];
            }
            }
            
            // Extraer solo el comentario limpio
            $comentario_limpio = 'Sin comentario adicional';
            if ($comentario_completo) {
                // Debug para ver qué contiene
                error_log("COMENTARIO COMPLETO DEBUG: " . $comentario_completo);
                
                // 🆕 MÉTODO MÁS DIRECTO: buscar y extraer solo después de los dos puntos
                if (strpos($comentario_completo, 'Comentario de aprobacion:') !== false) {
                    $comentario_limpio = substr($comentario_completo, strpos($comentario_completo, 'Comentario de aprobacion:') + strlen('Comentario de aprobacion:'));
                    $comentario_limpio = trim($comentario_limpio);
                    // Quitar todo lo que viene después incluyendo saltos de línea
                    $comentario_limpio = explode("\n", $comentario_limpio)[0];
                    $comentario_limpio = trim($comentario_limpio);
                } elseif (strpos($comentario_completo, 'Motivo del rechazo:') !== false) {
                    $comentario_limpio = substr($comentario_completo, strpos($comentario_completo, 'Motivo del rechazo:') + strlen('Motivo del rechazo:'));
                    $comentario_limpio = trim($comentario_limpio);
                    $comentario_limpio = explode("\n", $comentario_limpio)[0];
                    $comentario_limpio = trim($comentario_limpio);
                } else {
                    // Si no encuentra el patrón, tomar la línea más útil
                    $lineas = explode("\n", $comentario_completo);
                    foreach ($lineas as $linea) {
                        $linea = trim($linea);
                        if (!empty($linea) && 
                            stripos($linea, 'GERENCIAL') === false && 
                            stripos($linea, 'Procesado por') === false && 
                            stripos($linea, 'Asignado a RRHH') === false && 
                            stripos($linea, 'Fecha de procesamiento') === false &&
                            !preg_match('/^\d{4}-\d{2}-\d{2}/', $linea) &&
                            strlen($linea) > 3) {
                            
                            // Si la línea contiene dos puntos, tomar solo lo que está después
                            if (strpos($linea, ':') !== false) {
                                $partes = explode(':', $linea);
                                $comentario_limpio = trim(end($partes));
                            } else {
                                $comentario_limpio = $linea;
                            }
                            break;
                        }
                    }
                }
                
                // 🆕 ÚLTIMA LIMPIEZA: quitar caracteres extraños y fechas
                $comentario_limpio = str_replace(['?', '??'], '', $comentario_limpio);
                $comentario_limpio = preg_replace('/\s*Fecha de procesamiento:.*$/', '', $comentario_limpio);
                $comentario_limpio = trim($comentario_limpio);
                
                // Si después de todo sigue vacío, poner mensaje por defecto
                if (empty($comentario_limpio) || strlen($comentario_limpio) < 3) {
                    $comentario_limpio = 'Aprobacion procesada';
                }
                
                error_log("COMENTARIO LIMPIO EXTRAIDO: " . $comentario_limpio);
            }
            
            // Formatear fecha de solicitud
            $fecha_solicitud_formato = '';
            if ($row['FECHA_SOLICITUD']) {
                if (is_object($row['FECHA_SOLICITUD'])) {
                    $fecha_solicitud_formato = $row['FECHA_SOLICITUD']->format('d/m/Y');
                } else {
                    $fecha_obj = DateTime::createFromFormat('d/M/y', $row['FECHA_SOLICITUD']);
                    if ($fecha_obj) {
                        $fecha_solicitud_formato = $fecha_obj->format('d/m/Y');
                    } else {
                        $fecha_solicitud_formato = $row['FECHA_SOLICITUD'];
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'solicitud' => [
                    'id' => $row['ID_SOLICITUD'],
                    'tienda' => $row['NUM_TIENDA'],
                    'puesto_solicitado' => $row['PUESTO_SOLICITADO'],
                    'supervisor' => $row['SOLICITADO_POR'],
                    'estado_aprobacion' => $row['ESTADO_APROBACION'],
                    'dirigido_rh' => $row['DIRIGIDO_RH'],
                    'fecha_solicitud' => $fecha_solicitud_formato
                ],
                'resumen_aprobacion' => [
                    'procesado_por' => $nombre_gerente,
                    'asignado_a' => $row['DIRIGIDO_RH'],
                    'comentario_aprobacion' => $comentario_limpio,
                    'fecha_procesamiento' => $row['FECHA_DECISION_FORMATO']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Solicitud no encontrada']);
        }
        
        oci_free_statement($stmt);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    oci_close($conn);
    break;
        // ===================================================================
        // RESTO DE CASES DEL SEGUNDO CÓDIGO (TODOS LOS DEMÁS CASES)
        // ===================================================================
        
        // HISTORIAL INDIVIDUAL
        case 'get_historial_individual':
            if (!isset($_GET['id'])) {
                echo json_encode([]);
                break;
            }

            $id = $_GET['id'];
            error_log("Obteniendo historial individual para ID: $id");

            $query = "SELECT
                        h.ID_HISTORICO,
                        sp.NUM_TIENDA,
                        h.ESTADO_ANTERIOR,
                        h.ESTADO_NUEVO,
                        h.APROBACION_ANTERIOR,
                        h.APROBACION_NUEVA,
                        h.COMENTARIO_ANTERIOR,
                        h.COMENTARIO_NUEVO,
                        TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO
                      FROM ROY_HISTORICO_SOLICITUD h
                      JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD
                      WHERE h.ID_SOLICITUD = :id
                      ORDER BY h.FECHA_CAMBIO DESC";

            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':id', $id);
            oci_execute($stmt);

            $historial = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $historial[] = $row;
            }

            oci_free_statement($stmt);
            oci_close($conn);

            error_log("Historial individual obtenido: " . count($historial) . " registros");
            echo json_encode($historial);
            break;
            
            // OBTENER TIENDAS PARA FILTRO
            case 'get_tiendas_filtro':
                try {
                    // ✅ LIMPIAR OUTPUT BUFFER
                    if (ob_get_level()) ob_clean();
                    
                    // ✅ QUERY SIMPLIFICADO SIN JOIN PROBLEMÁTICO
                    $query = "SELECT DISTINCT NUM_TIENDA
                            FROM ROY_SOLICITUD_PERSONAL
                            WHERE NUM_TIENDA IS NOT NULL
                            ORDER BY NUM_TIENDA";
                    
                    $stmt = oci_parse($conn, $query);
                    
                    if (!oci_execute($stmt)) {
                        $error = oci_error($stmt);
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Error en consulta: ' . $error['message']]);
                        exit;
                    }
                    
                    $tiendas = [];
                    while ($row = oci_fetch_assoc($stmt)) {
                        $numeroTienda = trim($row['NUM_TIENDA']);
                        $tiendas[] = [
                            'numero' => $numeroTienda,
                            'nombre' => 'Tienda ' . $numeroTienda  // Nombre genérico
                        ];
                    }
                    
                    oci_free_statement($stmt);
                    header('Content-Type: application/json');
                    echo json_encode($tiendas);
                    exit;
                    
                } catch (Exception $e) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }
                break;

            
            // OBTENER SUPERVISORES PARA FILTRO - CORREGIDO PARA INFORMÁTICA
            case 'get_supervisores_filtro':
                    try {
                        // ✅ LIMPIAR OUTPUT BUFFER
                        if (ob_get_level()) ob_clean();
                        
                        if (in_array($usuario_logueado, ['5407', '5202'])) {
                            // INFORMÁTICA VE TODOS
                            $query = "SELECT DISTINCT udf1_string AS CODIGO_SUPERVISOR, 
                                            udf2_string AS NOMBRE_SUPERVISOR
                                    FROM RPS.STORE
                                    WHERE udf1_string IS NOT NULL 
                                    AND udf2_string IS NOT NULL
                                    AND sbs_sid = '680861302000159257'
                                    ORDER BY udf2_string";
                            
                            $stmt = oci_parse($conn, $query);
                            
                        } elseif (in_array($usuario_logueado, ['5333', '5210'])) {
                            // GERENTES VEN SOLO SUS SUPERVISORES
                            $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
                            $nombre_gerente = $gerente_nombres[$usuario_logueado];
                            
                            $query = "SELECT DISTINCT udf1_string AS CODIGO_SUPERVISOR, 
                                            udf2_string AS NOMBRE_SUPERVISOR
                                    FROM RPS.STORE
                                    WHERE UPPER(TRIM(udf4_string)) = UPPER(TRIM(:nombre_gerente))
                                    AND udf1_string IS NOT NULL 
                                    AND udf2_string IS NOT NULL
                                    AND sbs_sid = '680861302000159257'
                                    ORDER BY udf2_string";
                            
                            $stmt = oci_parse($conn, $query);
                            oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
                        } else {
                            header('Content-Type: application/json');
                            echo json_encode(['error' => 'Usuario no autorizado']);
                            exit;
                        }
                        
                        if (!oci_execute($stmt)) {
                            $error = oci_error($stmt);
                            header('Content-Type: application/json');
                            echo json_encode(['error' => 'Error en consulta: ' . $error['message']]);
                            exit;
                        }
                        
                        $supervisores = [];
                        while ($row = oci_fetch_assoc($stmt)) {
                            $supervisores[] = [
                                'codigo' => trim($row['CODIGO_SUPERVISOR']),
                                'nombre' => trim($row['NOMBRE_SUPERVISOR'])
                            ];
                        }
                        
                        oci_free_statement($stmt);
                        header('Content-Type: application/json');
                        echo json_encode($supervisores);
                        exit;
                        
                    } catch (Exception $e) {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => $e->getMessage()]);
                        exit;
                    }
                    break;

            
            // OBTENER PUESTOS PARA FILTRO
            case 'get_puestos_filtro':
                        try {
                            // ✅ LIMPIAR OUTPUT BUFFER
                            if (ob_get_level()) ob_clean();
                            
                            if (in_array($usuario_logueado, ['5407', '5202'])) {
                                // INFORMÁTICA VE TODOS
                                $query = "SELECT DISTINCT PUESTO_SOLICITADO
                                        FROM ROY_SOLICITUD_PERSONAL
                                        WHERE PUESTO_SOLICITADO IS NOT NULL
                                        ORDER BY PUESTO_SOLICITADO";
                                
                                $stmt = oci_parse($conn, $query);
                                
                            } elseif (in_array($usuario_logueado, ['5333', '5210'])) {
                                // GERENTES VEN SOLO PUESTOS DE SUS SUPERVISORES
                                $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
                                $nombre_gerente = $gerente_nombres[$usuario_logueado];
                                
                                $query = "SELECT DISTINCT s.PUESTO_SOLICITADO
                                        FROM ROY_SOLICITUD_PERSONAL s
                                        WHERE s.PUESTO_SOLICITADO IS NOT NULL
                                        AND s.SOLICITADO_POR IN (
                                            SELECT udf2_string 
                                            FROM RPS.STORE 
                                            WHERE UPPER(TRIM(udf4_string)) = UPPER(TRIM(:nombre_gerente))
                                            AND udf2_string IS NOT NULL
                                            AND sbs_sid = '680861302000159257'
                                        )
                                        ORDER BY s.PUESTO_SOLICITADO";
                                
                                $stmt = oci_parse($conn, $query);
                                oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
                            } else {
                                header('Content-Type: application/json');
                                echo json_encode(['error' => 'Usuario no autorizado']);
                                exit;
                            }
                            
                            if (!oci_execute($stmt)) {
                                $error = oci_error($stmt);
                                header('Content-Type: application/json');
                                echo json_encode(['error' => 'Error en consulta: ' . $error['message']]);
                                exit;
                            }
                            
                            $puestos = [];
                            while ($row = oci_fetch_assoc($stmt)) {
                                $puesto = trim($row['PUESTO_SOLICITADO']);
                                if (!empty($puesto)) {
                                    $puestos[] = $puesto;
                                }
                            }
                            
                            oci_free_statement($stmt);
                            header('Content-Type: application/json');
                            echo json_encode($puestos);
                            exit;
                            
                        } catch (Exception $e) {
                            header('Content-Type: application/json');
                            echo json_encode(['error' => $e->getMessage()]);
                            exit;
                        }
                        break;

            // HISTORIAL FILTRADO GENERAL
case 'get_historial_filtrado':
    try {
        if (ob_get_level()) ob_clean();
        
        $fecha_inicial = $_GET['fecha_inicial'] ?? '';
        $fecha_final = $_GET['fecha_final'] ?? '';
        $incluir_aprobaciones = $_GET['incluir_aprobaciones'] ?? 1;
        $incluir_estados = $_GET['incluir_estados'] ?? 1;
        
        $filtro_tienda = $_GET['filtro_tienda'] ?? '';
        $filtro_supervisor = $_GET['filtro_supervisor'] ?? '';
        $filtro_puesto = $_GET['filtro_puesto'] ?? '';
        
        error_log("🔍 FILTROS RECIBIDOS - Tienda: '$filtro_tienda', Código Supervisor: '$filtro_supervisor', Puesto: '$filtro_puesto'");
        
        if (empty($fecha_inicial) || empty($fecha_final)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Fechas requeridas']);
            exit;
        }
        
        // ✅ OBTENER NOMBRE DEL SUPERVISOR SI SE FILTRA POR CÓDIGO
        $nombre_supervisor_filtrado = '';
        if (!empty($filtro_supervisor)) {
            $queryNombreSup = "SELECT udf2_string as nombre 
                              FROM RPS.STORE 
                              WHERE udf1_string = :filtro_supervisor 
                              AND sbs_sid = '680861302000159257' 
                              AND ROWNUM = 1";
            
            $stmtNombreSup = oci_parse($conn, $queryNombreSup);
            oci_bind_by_name($stmtNombreSup, ':filtro_supervisor', $filtro_supervisor);
            oci_execute($stmtNombreSup);
            
            if ($rowSup = oci_fetch_assoc($stmtNombreSup)) {
                $nombre_supervisor_filtrado = trim($rowSup['NOMBRE']);
                error_log("✅ Supervisor encontrado - Código: $filtro_supervisor, Nombre: $nombre_supervisor_filtrado");
            } else {
                error_log("❌ No se encontró supervisor con código: $filtro_supervisor");
                // Si no existe el supervisor, devolver array vacío
                header('Content-Type: application/json');
                echo json_encode([]);
                exit;
            }
            oci_free_statement($stmtNombreSup);
        }
        
        // ✅ CONSTRUIR QUERY PRINCIPAL CON JOINS CORRECTOS
        $whereConditions = [];
        $joinConditions = [];
        
        // Fecha siempre requerida
        $whereConditions[] = "h.FECHA_CAMBIO BETWEEN TO_DATE(:fecha_inicial, 'YYYY-MM-DD') AND TO_DATE(:fecha_final, 'YYYY-MM-DD') + 1";
        
        // ✅ FILTRO DE USUARIO (GERENTES)
        if (in_array($usuario_logueado, ['5333', '5210'])) {
            $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
            $nombre_gerente = $gerente_nombres[$usuario_logueado];
            
            $joinConditions[] = "LEFT JOIN RPS.STORE rps_gerente ON rps_gerente.udf2_string = s.SOLICITADO_POR AND rps_gerente.sbs_sid = '680861302000159257'";
            $whereConditions[] = "UPPER(TRIM(rps_gerente.udf4_string)) = UPPER(TRIM(:nombre_gerente))";
        }
        
        // ✅ FILTRO POR TIENDA
        if (!empty($filtro_tienda)) {
            $whereConditions[] = "s.NUM_TIENDA = :filtro_tienda";
        }
        
        // ✅ FILTRO POR SUPERVISOR (usando nombre obtenido)
        if (!empty($filtro_supervisor) && !empty($nombre_supervisor_filtrado)) {
            $whereConditions[] = "s.SOLICITADO_POR = :nombre_supervisor_filtrado";
        }
        
        // ✅ FILTRO POR PUESTO
        if (!empty($filtro_puesto)) {
            $whereConditions[] = "s.PUESTO_SOLICITADO = :filtro_puesto";
        }
        
        // ✅ CONSTRUIR QUERY COMPLETO
        $joinClause = implode(' ', $joinConditions);
        $whereClause = implode(' AND ', $whereConditions);
        
        $query = "SELECT 
                    h.ID_HISTORICO,
                    h.ID_SOLICITUD,
                    s.NUM_TIENDA,
                    s.PUESTO_SOLICITADO,
                    s.SOLICITADO_POR,
                    h.ESTADO_ANTERIOR,
                    h.ESTADO_NUEVO,
                    h.APROBACION_ANTERIOR,
                    h.APROBACION_NUEVA,
                    h.COMENTARIO_NUEVO,
                    TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                    rps_info.udf1_string as CODIGO_SUPERVISOR
                  FROM ROY_HISTORICO_SOLICITUD h
                  INNER JOIN ROY_SOLICITUD_PERSONAL s ON h.ID_SOLICITUD = s.ID_SOLICITUD
                  LEFT JOIN RPS.STORE rps_info ON rps_info.udf2_string = s.SOLICITADO_POR AND rps_info.sbs_sid = '680861302000159257'
                  $joinClause
                  WHERE $whereClause
                  ORDER BY h.FECHA_CAMBIO DESC";
        
        error_log("🔍 QUERY FINAL CONSTRUIDO:");
        error_log($query);
        
        // ✅ PREPARAR Y EJECUTAR
        $stmt = oci_parse($conn, $query);
        if (!$stmt) {
            $error = oci_error($conn);
            error_log("❌ Error preparando query: " . print_r($error, true));
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error preparando consulta']);
            exit;
        }
        
        // ✅ BIND PARÁMETROS
        oci_bind_by_name($stmt, ':fecha_inicial', $fecha_inicial);
        oci_bind_by_name($stmt, ':fecha_final', $fecha_final);
        error_log("✅ Parámetros de fecha vinculados");
        
        if (!empty($filtro_tienda)) {
            oci_bind_by_name($stmt, ':filtro_tienda', $filtro_tienda);
            error_log("✅ Parámetro tienda vinculado: $filtro_tienda");
        }
        
        if (!empty($nombre_supervisor_filtrado)) {
            oci_bind_by_name($stmt, ':nombre_supervisor_filtrado', $nombre_supervisor_filtrado);
            error_log("✅ Parámetro supervisor vinculado: $nombre_supervisor_filtrado");
        }
        
        if (!empty($filtro_puesto)) {
            oci_bind_by_name($stmt, ':filtro_puesto', $filtro_puesto);
            error_log("✅ Parámetro puesto vinculado: $filtro_puesto");
        }
        
        if (in_array($usuario_logueado, ['5333', '5210'])) {
            $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
            $nombre_gerente = $gerente_nombres[$usuario_logueado];
            oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
            error_log("✅ Parámetro gerente vinculado: $nombre_gerente");
        }
        
        // ✅ EJECUTAR QUERY
        error_log("🚀 Ejecutando query...");
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            error_log("❌ Error ejecutando query: " . print_r($error, true));
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error en consulta: ' . $error['message']]);
            exit;
        }
        
        error_log("✅ Query ejecutado exitosamente, procesando resultados...");
        
        // ✅ PROCESAR RESULTADOS
        $historial = [];
        $registrosEncontrados = 0;
        
        while ($row = oci_fetch_assoc($stmt)) {
            $registrosEncontrados++;
            
            if ($registrosEncontrados <= 5) { // Log de primeros 5 registros
                error_log("📝 Registro $registrosEncontrados: Tienda={$row['NUM_TIENDA']}, Supervisor={$row['SOLICITADO_POR']}, Código={$row['CODIGO_SUPERVISOR']}, Puesto={$row['PUESTO_SOLICITADO']}");
            }
            
            // ✅ APLICAR FILTROS DE INCLUSIÓN
            $incluir_registro = false;
            
            if ($incluir_aprobaciones && $incluir_estados) {
                $incluir_registro = true;
            } elseif ($incluir_aprobaciones && !$incluir_estados) {
                $incluir_registro = ($row['APROBACION_ANTERIOR'] !== $row['APROBACION_NUEVA']);
            } elseif (!$incluir_aprobaciones && $incluir_estados) {
                $incluir_registro = ($row['ESTADO_ANTERIOR'] !== $row['ESTADO_NUEVO']);
            }
            
            if ($incluir_registro) {
                $historial[] = [
                    'ID_HISTORICO' => $row['ID_HISTORICO'],
                    'ID_SOLICITUD' => $row['ID_SOLICITUD'],
                    'NUM_TIENDA' => $row['NUM_TIENDA'],
                    'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
                    'SOLICITADO_POR' => $row['SOLICITADO_POR'],
                    'CODIGO_SUPERVISOR' => $row['CODIGO_SUPERVISOR'],
                    'ESTADO_ANTERIOR' => $row['ESTADO_ANTERIOR'],
                    'ESTADO_NUEVO' => $row['ESTADO_NUEVO'],
                    'APROBACION_ANTERIOR' => $row['APROBACION_ANTERIOR'],
                    'APROBACION_NUEVA' => $row['APROBACION_NUEVA'],
                    'COMENTARIO_NUEVO' => $row['COMENTARIO_NUEVO'],
                    'FECHA_CAMBIO' => $row['FECHA_CAMBIO']
                ];
            }
        }
        
        oci_free_statement($stmt);
        oci_close($conn);
        
        error_log("✅ RESULTADO FINAL: $registrosEncontrados registros encontrados, " . count($historial) . " incluidos en respuesta");
        
        // ✅ RESPUESTA
        header('Content-Type: application/json');
        echo json_encode($historial);
        exit;
        
    } catch (Exception $e) {
        error_log("❌ EXCEPCIÓN CAPTURADA: " . $e->getMessage());
        error_log("❌ Stack trace: " . $e->getTraceAsString());
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
        exit;
    }
    break;

        // VER ARCHIVOS
        case 'get_archivos':
            error_log("=== OBTENIENDO ARCHIVOS DEL ÚLTIMO CAMBIO DE CVS ===");

            if (!isset($_GET['id']) || empty($_GET['id'])) {
                error_log("ID de solicitud no proporcionado");
                echo json_encode([
                    'error' => 'ID de solicitud requerido',
                    'archivos' => []
                ]);
                break;
            }

            $id = $_GET['id'];
            error_log("Buscando archivos para solicitud ID: " . $id);

            try {
                // Buscar el último ID_HISTORICO que tenga CVS en estado nuevo
                $queryHist = "SELECT MAX(ID_HISTORICO) AS ID_HISTORICO 
                              FROM ROY_HISTORICO_SOLICITUD 
                              WHERE ID_SOLICITUD = :id 
                              AND LOWER(ESTADO_NUEVO) LIKE '%cvs%'";

                $stmtHist = oci_parse($conn, $queryHist);
                oci_bind_by_name($stmtHist, ':id', $id);
                oci_execute($stmtHist);

                $idHistorico = null;
                if ($row = oci_fetch_assoc($stmtHist)) {
                    $idHistorico = $row['ID_HISTORICO'];
                }
                oci_free_statement($stmtHist);

                if (!$idHistorico) {
                    echo json_encode([
                        'success' => true,
                        'archivos' => [],
                        'mensaje' => 'No hay archivos recientes para estados CVS.',
                        'solicitud_id' => $id
                    ]);
                    break;
                }

                // Obtener archivos vinculados al ID_HISTORICO
                $query = "SELECT 
                            NOMBRE_ARCHIVO, 
                            TO_CHAR(FECHA_SUBIDA, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_SUBIDA,
                            ID_ARCHIVO
                          FROM ROY_ARCHIVOS_SOLICITUD 
                          WHERE ID_SOLICITUD = :id 
                          AND ID_HISTORICO = :id_hist
                          ORDER BY FECHA_SUBIDA DESC";

                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':id', $id);
                oci_bind_by_name($stmt, ':id_hist', $idHistorico);
                oci_execute($stmt);

                $archivos = [];
                while ($row = oci_fetch_assoc($stmt)) {
                    $nombreArchivo = $row['NOMBRE_ARCHIVO'];
                    $fechaSubida = $row['FECHA_SUBIDA'];
                    $idArchivo = $row['ID_ARCHIVO'] ?? uniqid();

                    $rutaCompleta = '../../' . $nombreArchivo;
                    $archivoExiste = file_exists($rutaCompleta);

                    $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
                    $nombreSolo = basename($nombreArchivo);
                    $tamaño = $archivoExiste ? filesize($rutaCompleta) : 0;
                    $tamañoMB = $tamaño > 0 ? round($tamaño / 1024 / 1024, 2) : 0;

                    $archivos[] = [
                        'ID_ARCHIVO' => $idArchivo,
                        'NOMBRE_ARCHIVO' => $nombreArchivo,
                        'NOMBRE_SOLO' => $nombreSolo,
                        'FECHA_SUBIDA' => $fechaSubida,
                        'EXTENSION' => $extension,
                        'TAMAÑO_BYTES' => $tamaño,
                        'TAMAÑO_MB' => $tamañoMB,
                        'EXISTE' => $archivoExiste,
                        'RUTA_RELATIVA' => $nombreArchivo
                    ];
                }

                oci_free_statement($stmt);

                echo json_encode([
                    'success' => true,
                    'archivos' => $archivos,
                    'id_historico' => $idHistorico,
                    'solicitud_id' => $id
                ]);

            } catch (Exception $e) {
                error_log("Excepción en get_archivos: " . $e->getMessage());
                echo json_encode([
                    'error' => 'Error interno del servidor',
                    'archivos' => []
                ]);
            }

            oci_close($conn);
            break;


        // VER RESUMEN CVS
        case 'ver_resumen_cvs':
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            $action = $_POST['action'] ?? $data['action'] ?? null;
            $idSolicitud = $_POST['id_solicitud'] ?? $data['id_solicitud'] ?? null;

            if (empty($action) || empty($idSolicitud)) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Parámetros faltantes'
                ]);
                exit;
            }

            // Obtener el último ID_HISTORICO con estado tipo CVS
            $queryHistorico = "SELECT MAX(ID_HISTORICO) AS ID_HISTORICO
                               FROM ROY_HISTORICO_SOLICITUD
                               WHERE ID_SOLICITUD = :id
                                 AND LOWER(ESTADO_NUEVO) LIKE '%cvs%'";

            $stmtHistorico = oci_parse($conn, $queryHistorico);
            oci_bind_by_name($stmtHistorico, ':id', $idSolicitud);
            oci_execute($stmtHistorico);
            $rowHistorico = oci_fetch_assoc($stmtHistorico);
            $idHistoricoCV = $rowHistorico['ID_HISTORICO'] ?? null;
            oci_free_statement($stmtHistorico);

            if (!$idHistoricoCV) {
                echo json_encode([
                    'success' => false,
                    'error' => 'No se encontró historial relacionado con "CVS Enviados"'
                ]);
                exit;
            }

            // Obtener la selección activa para ese ID_HISTORICO_CV_ENVIO
            $query = "SELECT ARCHIVOS_SELECCIONADOS 
                      FROM ROY_SELECCION_CVS 
                      WHERE ID_SOLICITUD = :id
                        AND ID_HISTORICO_CV_ENVIO = :idh
                        AND ES_ACTIVA = 'Y'
                      FETCH FIRST 1 ROWS ONLY";

            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':id', $idSolicitud);
            oci_bind_by_name($stmt, ':idh', $idHistoricoCV);

            if (!oci_execute($stmt)) {
                $error = oci_error($stmt);
                echo json_encode([
                    'success' => false,
                    'error' => 'Error en la consulta SQL',
                    'sql_error' => $error['message']
                ]);
                exit;
            }

            $archivos = [];
            $row = oci_fetch_assoc($stmt);
            if ($row && !empty($row['ARCHIVOS_SELECCIONADOS'])) {
                $clob = $row['ARCHIVOS_SELECCIONADOS'];
                $contenido = is_object($clob) && method_exists($clob, 'load') ? $clob->load() : '';

                if (!empty($contenido)) {
                    $rutasArchivos = explode(',', $contenido);
                    foreach ($rutasArchivos as $ruta) {
                        $ruta = trim($ruta);
                        if (!empty($ruta)) {
                            $nombre = basename($ruta);
                            $tipo = strtoupper(pathinfo($nombre, PATHINFO_EXTENSION));
                            $archivos[] = [
                                'NOMBRE_ARCHIVO' => $nombre,
                                'TIPO' => $tipo,
                                'RUTA' => $ruta
                            ];
                        }
                    }
                }
            }

            oci_free_statement($stmt);

            echo json_encode([
                'success' => true,
                'archivos' => $archivos,
                'total' => count($archivos)
            ]);
            exit;


        // VER PRUEBAS ADJUNTAS
        case 'ver_pruebas_adjuntas':
            try {
                if (empty($_POST['id_solicitud']) || empty($_POST['tipo'])) {
                    throw new Exception("Faltan parámetros requeridos.");
                }

                $idSolicitud = $_POST['id_solicitud'];
                $tipoArchivo = strtoupper(trim($_POST['tipo'])); // PSICOMETRICA o POLIGRAFO

                $query = "SELECT ID_ARCHIVO, NOMBRE_ARCHIVO, FECHA_SUBIDA
                  FROM ROY_ARCHIVOS_SOLICITUD
                  WHERE ID_SOLICITUD = :id_solicitud
                    AND UPPER(TIPO_ARCHIVO) = :tipo
                  ORDER BY FECHA_SUBIDA DESC
                  FETCH FIRST 1 ROWS ONLY";

                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ":id_solicitud", $idSolicitud);
                oci_bind_by_name($stmt, ":tipo", $tipoArchivo);
                oci_execute($stmt);

                $archivos = [];
                while ($row = oci_fetch_assoc($stmt)) {
                    $archivos[] = $row;
                }

                echo json_encode(['success' => true, 'archivos' => $archivos]);

            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al obtener archivos adjuntos.',
                    'error' => $e->getMessage()
                ]);
            }
            break;

            //====================================================================
            // GENERACION DE REPORTES PDF Y EXCEL
            //====================================================================
            case 'generar_reporte_historial':
                try {
                    if (ob_get_level()) ob_clean();
                    
                    // ✅ DETECTAR FORMATO SOLICITADO
                    $formato = $_GET['formato'] ?? 'json';
                    
                    // ✅ OBTENER PARÁMETROS (desde GET o POST dependiendo del formato)
                    if ($formato === 'json') {
                        // Para vista previa, usar POST
                        $fecha_inicial = $_POST['fecha_inicial'] ?? '';
                        $fecha_final = $_POST['fecha_final'] ?? '';
                        $incluir_aprobaciones = $_POST['incluir_aprobaciones'] ?? 1;
                        $incluir_estados = $_POST['incluir_estados'] ?? 1;
                        $filtro_tienda = $_POST['filtro_tienda'] ?? '';
                        $filtro_supervisor = $_POST['filtro_supervisor'] ?? '';
                        $filtro_puesto = $_POST['filtro_puesto'] ?? '';
                    } else {
                        // Para exportación, usar GET
                        $fecha_inicial = $_GET['fecha_inicial'] ?? '';
                        $fecha_final = $_GET['fecha_final'] ?? '';
                        $incluir_aprobaciones = $_GET['incluir_aprobaciones'] ?? 1;
                        $incluir_estados = $_GET['incluir_estados'] ?? 1;
                        $filtro_tienda = $_GET['filtro_tienda'] ?? '';
                        $filtro_supervisor = $_GET['filtro_supervisor'] ?? '';
                        $filtro_puesto = $_GET['filtro_puesto'] ?? '';
                    }
                    
                    error_log("🎯 GENERAR REPORTE FORMATO: $formato - Tienda: '$filtro_tienda', Supervisor: '$filtro_supervisor', Puesto: '$filtro_puesto'");
                    
                    if (empty($fecha_inicial) || empty($fecha_final)) {
                        if ($formato === 'json') {
                            header('Content-Type: application/json');
                            echo json_encode(['error' => 'Fechas requeridas para generar el reporte']);
                        } else {
                            echo "<script>alert('Fechas requeridas'); window.close();</script>";
                        }
                        exit;
                    }
                    
                    // ✅ VERIFICAR SI HAY FILTROS ADICIONALES
                    $hay_filtros_adicionales = !empty($filtro_tienda) || !empty($filtro_supervisor) || !empty($filtro_puesto);
                    
                    if ($hay_filtros_adicionales) {
                        error_log("📊 Generando REPORTE FILTRADO en formato: $formato");
                        $resultado = generarReporteFiltrado($conn, $fecha_inicial, $fecha_final, $filtro_tienda, $filtro_supervisor, $filtro_puesto, $incluir_aprobaciones, $incluir_estados, $usuario_logueado);
                    } else {
                        error_log("📊 Generando REPORTE GENERAL en formato: $formato");
                        $resultado = generarReporteGeneral($conn, $fecha_inicial, $fecha_final, $incluir_aprobaciones, $incluir_estados, $usuario_logueado);
                    }
                    
                    // ✅ VERIFICAR SI HAY ERROR EN LA GENERACIÓN DE DATOS
                    if (isset($resultado['error'])) {
                        if ($formato === 'json') {
                            header('Content-Type: application/json');
                            echo json_encode($resultado);
                        } else {
                            echo "<script>alert('Error: {$resultado['error']}'); window.close();</script>";
                        }
                        exit;
                    }
                    
                    // ✅ GENERAR SEGÚN EL FORMATO SOLICITADO
                    switch($formato) {
                        case 'excel':
                            error_log("📊 Generando archivo Excel...");
                            generarExcelHistorial($resultado);
                            break;
                            
                        case 'pdf':
                            error_log("📊 Generando archivo PDF...");
                            generarPDFHistorial($resultado);
                            break;
                            
                        default:
                            // Devolver JSON para vista previa en el modal
                            error_log("📊 Devolviendo JSON para vista previa");
                            header('Content-Type: application/json');
                            echo json_encode($resultado);
                            break;
                    }
                    
                    exit;
                    
                } catch (Exception $e) {
                    error_log("❌ Error generando reporte: " . $e->getMessage());
                    
                    if (isset($formato) && $formato !== 'json') {
                        echo "<script>alert('Error generando reporte: {$e->getMessage()}'); window.close();</script>";
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => 'Error generando reporte: ' . $e->getMessage()]);
                    }
                    exit;
                }
                break;


                // OBTENER INFORMACIÓN COMPLETA DEL AVAL
                case 'obtener_info_aval':
                    if (ob_get_level()) ob_end_clean();
                    header('Content-Type: application/json; charset=utf-8');
                    
                    try {
                        $id_solicitud = $_GET['id_solicitud'] ?? '';
                        if (empty($id_solicitud)) {
                            echo json_encode(['success' => false, 'error' => 'ID requerido']);
                            exit;
                        }
                        
                        // ✅ 1. INFORMACIÓN DE LA SOLICITUD
                        $querySolicitud = "SELECT 
                                            NUM_TIENDA,
                                            PUESTO_SOLICITADO,
                                            SOLICITADO_POR,
                                            RAZON,
                                            TO_CHAR(FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD
                                        FROM ROY_SOLICITUD_PERSONAL
                                        WHERE ID_SOLICITUD = :id";
                        
                        $stmtSolicitud = oci_parse($conn, $querySolicitud);
                        oci_bind_by_name($stmtSolicitud, ':id', $id_solicitud);
                        oci_execute($stmtSolicitud);
                        $solicitud = oci_fetch_assoc($stmtSolicitud);
                        oci_free_statement($stmtSolicitud);
                        
                        // ✅ 2. INFORMACIÓN DEL CANDIDATO (última observación enviada)
                        $queryCandidato = "SELECT 
                                            CANDIDATO_NOMBRE,
                                            CANDIDATO_DOCUMENTO,
                                            PUESTO_EVALUADO,
                                            TO_CHAR(FECHA_DIA_PRUEBA, 'DD/MM/YYYY') as FECHA_DIA_PRUEBA,
                                            HORA_INICIO,
                                            HORA_FIN,
                                            OBSERVACIONES_DET,
                                            DESEMPENO_GENERAL,  
                                            PUNTUALIDAD,
                                            ACTITUD,
                                            CONOCIMIENTOS,
                                            RECOMENDACION_SUP,
                                            TO_CHAR(FECHA_CREACION, 'DD/MM/YYYY HH24:MI') as FECHA_EVALUACION
                                        FROM ROY_OBSERVACIONES_DIA_PRUEBA
                                        WHERE ID_SOLICITUD = :id 
                                        AND ESTADO = 'ENVIADO'
                                        ORDER BY FECHA_CREACION DESC
                                        FETCH FIRST 1 ROWS ONLY";
                        
                        $stmtCandidato = oci_parse($conn, $queryCandidato);
                        oci_bind_by_name($stmtCandidato, ':id', $id_solicitud);
                        oci_execute($stmtCandidato);
                        $candidato = oci_fetch_assoc($stmtCandidato);
                        oci_free_statement($stmtCandidato);
                        
                        // ✅ 3. INFORMACIÓN DEL AVAL MÁS RECIENTE (ROY_AVALES_GERENCIA)
                        $queryAval = "SELECT 
                                        ID_AVAL,
                                        ENVIADO_POR,
                                        TO_CHAR(FECHA_ENVIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_ENVIO,
                                        COMENTARIO_RRHH,
                                        ARCHIVO_REPORTE_PDF,
                                        ARCHIVO_CV_CANDIDATO,
                                        ESTADO_AVAL
                                    FROM ROY_AVALES_GERENCIA
                                    WHERE ID_SOLICITUD = :id
                                    ORDER BY FECHA_ENVIO DESC
                                    FETCH FIRST 1 ROWS ONLY";
                        
                        $stmtAval = oci_parse($conn, $queryAval);
                        oci_bind_by_name($stmtAval, ':id', $id_solicitud);
                        oci_execute($stmtAval);
                        $aval = oci_fetch_assoc($stmtAval);
                        oci_free_statement($stmtAval);
                        
                        // ✅ 4. PROCESAR CLOBS
                        if ($candidato && isset($candidato['OBSERVACIONES_DET']) && is_object($candidato['OBSERVACIONES_DET'])) {
                            $candidato['OBSERVACIONES_DET'] = $candidato['OBSERVACIONES_DET']->load();
                        }
                        
                        if ($aval && isset($aval['COMENTARIO_RRHH']) && is_object($aval['COMENTARIO_RRHH'])) {
                            $aval['COMENTARIO_RRHH'] = $aval['COMENTARIO_RRHH']->load();
                        }
                        
                        // ✅ 5. PROCESAR DOCUMENTOS DE ROY_AVALES_GERENCIA
                        $documentos = [];
                        if ($aval && !empty($aval['ARCHIVO_REPORTE_PDF'])) {
                            $documentos[] = [
                                'nombre' => basename($aval['ARCHIVO_REPORTE_PDF']),
                                'ruta' => '/roy/Page/' . ltrim($aval['ARCHIVO_REPORTE_PDF'], '/'),
                                'tipo' => 'Reporte del Día de Prueba'
                            ];
                        }
                        if ($aval && !empty($aval['ARCHIVO_CV_CANDIDATO'])) {
                            $documentos[] = [
                                'nombre' => basename($aval['ARCHIVO_CV_CANDIDATO']),
                                'ruta' => '/roy/Page/' . ltrim($aval['ARCHIVO_CV_CANDIDATO'], '/'),
                                'tipo' => 'Curriculum Vitae'
                            ];
                        }
                        
                        // ✅ 6. RESPUESTA FINAL
                        $respuesta = [
                            'success' => true,
                            'data' => [
                                'solicitud' => [
                                    'tienda' => $solicitud['NUM_TIENDA'] ?? 'N/A',
                                    'puesto' => $solicitud['PUESTO_SOLICITADO'] ?? 'N/A',
                                    'supervisor' => $solicitud['SOLICITADO_POR'] ?? 'N/A',
                                    'razon' => $solicitud['RAZON'] ?? 'N/A',
                                    'fecha_solicitud' => $solicitud['FECHA_SOLICITUD'] ?? 'N/A'
                                ],
                                'candidato' => [
                                    'CANDIDATO_NOMBRE' => $candidato['CANDIDATO_NOMBRE'] ?? 'No especificado',
                                    'CANDIDATO_DOCUMENTO' => $candidato['CANDIDATO_DOCUMENTO'] ?? 'N/A',
                                    'PUESTO_EVALUADO' => $candidato['PUESTO_EVALUADO'] ?? 'N/A',
                                    'FECHA_DIA_PRUEBA' => $candidato['FECHA_DIA_PRUEBA'] ?? 'N/A',
                                    'HORARIO' => ($candidato['HORA_INICIO'] ?? '') . ' - ' . ($candidato['HORA_FIN'] ?? ''),
                                    'OBSERVACIONES_DET' => $candidato['OBSERVACIONES_DET'] ?? 'Sin observaciones',
                                    'DESEMPENO_GENERAL' => $candidato['DESEMPENO_GENERAL'] ?? 'N/A',
                                    'PUNTUALIDAD' => $candidato['PUNTUALIDAD'] ?? 'N/A',
                                    'ACTITUD' => $candidato['ACTITUD'] ?? 'N/A',
                                    'CONOCIMIENTOS' => $candidato['CONOCIMIENTOS'] ?? 'N/A',
                                    'RECOMENDACION_SUP' => $candidato['RECOMENDACION_SUP'] ?? 'N/A',
                                    'FECHA_EVALUACION' => $candidato['FECHA_EVALUACION'] ?? 'N/A'
                                ],
                                'aval' => [
                                    'enviado_por' => $aval['ENVIADO_POR'] ?? 'N/A',
                                    'fecha_envio' => $aval['FECHA_ENVIO'] ?? 'N/A',
                                    'comentario_rh' => $aval['COMENTARIO_RRHH'] ?? 'Sin comentarios',
                                    'estado_aval' => $aval['ESTADO_AVAL'] ?? 'PENDIENTE'
                                ],
                                'documentos' => $documentos
                            ]
                        ];
                        
                        echo json_encode($respuesta);
                        exit;
                        
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
                        exit;
                    }
                    break;




                //  PROCESAR DECISIÓN DEL AVAL
                        case 'procesar_decision_aval':
                        try {
                            error_log("INICIO procesar_decision_aval");
                            
                            $id_solicitud      = $_POST['id_solicitud'] ?? '';
                            $decision          = strtoupper(trim($_POST['decision'] ?? ''));
                            $comentarioGerente = trim($_POST['comentario_gerente'] ?? '');

                            error_log(" Datos recibidos - ID: $id_solicitud, Decisión: $decision");

                            if (empty($id_solicitud) || empty($decision) || empty($comentarioGerente)) {
                                enviarJSON(['success' => false, 'error' => 'Datos incompletos']);
                            }
                            if (!in_array($decision, ['APROBADO','RECHAZADO'])) {
                                enviarJSON(['success' => false, 'error' => 'Decisión inválida']);
                            }

                            // ✅ INFO DE GERENTE
                            $codigo_gerente = $_SESSION['user'][12] ?? null;
                            $map = ['5333'=>'Christian Quan','5210'=>'Giovanni Cardoza'];
                            $nombre_gerente = $map[$codigo_gerente] ?? 'Sistema';
                            
                            error_log("Gerente - Código: $codigo_gerente, Nombre: $nombre_gerente");

                            // ✅ BUSCAR ID_AVAL ACTIVO
                            $qUlt = "SELECT ID_AVAL FROM ROY_AVALES_GERENCIA
                                    WHERE ID_SOLICITUD = :id AND ACTIVO = 'Y'
                                    ORDER BY FECHA_ENVIO DESC
                                    FETCH FIRST 1 ROWS ONLY";
                            $stUlt = oci_parse($conn, $qUlt);
                            oci_bind_by_name($stUlt, ':id', $id_solicitud);
                            
                            if (!oci_execute($stUlt)) {
                                $error = oci_error($stUlt);
                                error_log("Error buscando aval: " . $error['message']);
                                enviarJSON(['success'=>false,'error'=>'Error buscando aval: ' . $error['message']]);
                            }
                            
                            $rUlt = oci_fetch_assoc($stUlt);
                            oci_free_statement($stUlt);
                            
                            if (!$rUlt) {
                                error_log("No se encontró aval activo para solicitud $id_solicitud");
                                enviarJSON(['success'=>false,'error'=>'No se encontró aval activo para esta solicitud']);
                            }
                            
                            $idAval = (int)$rUlt['ID_AVAL'];
                            error_log("ID_AVAL encontrado: $idAval");

                            // INICIAR TRANSACCIÓN MANUAL
                            $savepoint = oci_parse($conn, "SAVEPOINT decision_aval");
                            oci_execute($savepoint, OCI_NO_AUTO_COMMIT);
                            oci_free_statement($savepoint);

                            // 1) ACTUALIZAR ROY_AVALES_GERENCIA
                            $estadoAvalFinal = ($decision === 'APROBADO') ? 'APROBADO' : 'RECHAZADO';
                            
                            $qUpd = "UPDATE ROY_AVALES_GERENCIA
                                    SET FECHA_RESPUESTA = SYSDATE,
                                        GERENTE_CODIGO = :gc,
                                        GERENTE_NOMBRE = :gn,
                                        COMENTARIO_GERENTE = :cmt,
                                        DECISION_GERENTE = :dec,
                                        ESTADO_AVAL = :estado_aval
                                    WHERE ID_AVAL = :id_aval";
                            
                            $stUpd = oci_parse($conn, $qUpd);
                            oci_bind_by_name($stUpd, ':gc', $codigo_gerente);
                            oci_bind_by_name($stUpd, ':gn', $nombre_gerente);
                            oci_bind_by_name($stUpd, ':cmt', $comentarioGerente);
                            oci_bind_by_name($stUpd, ':dec', $decision);
                            oci_bind_by_name($stUpd, ':estado_aval', $estadoAvalFinal);
                            oci_bind_by_name($stUpd, ':id_aval', $idAval);
                            
                            if (!oci_execute($stUpd, OCI_NO_AUTO_COMMIT)) {
                                $error = oci_error($stUpd);
                                error_log("❌ Error actualizando aval: " . $error['message']);
                                oci_rollback($conn);
                                enviarJSON(['success' => false, 'error' => 'Error actualizando aval: ' . $error['message']]);
                            }
                            
                            $filasAfectadas = oci_num_rows($stUpd);
                            error_log("✅ Aval actualizado - Filas afectadas: $filasAfectadas");
                            oci_free_statement($stUpd);

                            // 2) OBTENER ESTADO ANTERIOR
                            $qEA = "SELECT ESTADO_NUEVO FROM ROY_HISTORICO_SOLICITUD
                                    WHERE ID_SOLICITUD = :id
                                    ORDER BY FECHA_CAMBIO DESC
                                    FETCH FIRST 1 ROWS ONLY";
                            $stEA = oci_parse($conn, $qEA);
                            oci_bind_by_name($stEA, ':id', $id_solicitud);
                            oci_execute($stEA);
                            
                            $estadoAnterior = 'Pendiente Aval Gerencia';
                            if ($rowEA = oci_fetch_assoc($stEA)) {
                                $estadoAnterior = $rowEA['ESTADO_NUEVO'] ?: $estadoAnterior;
                            }
                            oci_free_statement($stEA);
                            
                            error_log("📊 Estado anterior: $estadoAnterior");

                            // 3) INSERTAR EN HISTORIAL
                        $estadoNuevo = 'Aval Enviado';
                        $comentarioHistorial = "DECISION GERENCIAL: $decision - $comentarioGerente";
                        $tipoEvento = 'AVAL_ENVIADO';
                        $avalEnviado = 'Y';

                        // SIN ID_HISTORICO - ES IDENTITY COLUMN (AUTO-GENERADO)
                        $qHist = "INSERT INTO ROY_HISTORICO_SOLICITUD
                                    (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, 
                                    FECHA_CAMBIO, COMENTARIO_NUEVO, TIPO_EVENTO, AVAL_ENVIADO)
                                VALUES
                                    (:id, :eant, :envo, SYSDATE, :comentario, :tipo, :aval)";

                        $stHist = oci_parse($conn, $qHist);
                        oci_bind_by_name($stHist, ':id', $id_solicitud);
                        oci_bind_by_name($stHist, ':eant', $estadoAnterior);
                        oci_bind_by_name($stHist, ':envo', $estadoNuevo);
                        oci_bind_by_name($stHist, ':comentario', $comentarioHistorial);
                        oci_bind_by_name($stHist, ':tipo', $tipoEvento);
                        oci_bind_by_name($stHist, ':aval', $avalEnviado);

                        if (!oci_execute($stHist, OCI_NO_AUTO_COMMIT)) {
                            $error = oci_error($stHist);
                            error_log(" Error insertando historial: " . $error['message']);
                            oci_rollback($conn);
                            enviarJSON(['success' => false, 'error' => 'Error insertando historial: ' . $error['message']]);
                        }

                        error_log(" Historial insertado correctamente con IDENTITY COLUMN");
                        oci_free_statement($stHist);

                            //  4) ACTUALIZAR SOLICITUD
                            $qSol = "UPDATE ROY_SOLICITUD_PERSONAL 
                                    SET ESTADO_SOLICITUD = :estado_nuevo, FECHA_MODIFICACION = SYSDATE
                                    WHERE ID_SOLICITUD = :id";
                            $stSol = oci_parse($conn, $qSol);
                            oci_bind_by_name($stSol, ':estado_nuevo', $estadoNuevo);
                            oci_bind_by_name($stSol, ':id', $id_solicitud);
                            
                            if (!oci_execute($stSol, OCI_NO_AUTO_COMMIT)) {
                                $error = oci_error($stSol);
                                error_log(" Error actualizando solicitud: " . $error['message']);
                                oci_rollback($conn);
                                enviarJSON(['success' => false, 'error' => 'Error actualizando solicitud: ' . $error['message']]);
                            }
                            
                            error_log("Solicitud actualizada");
                            oci_free_statement($stSol);

                            //  COMMIT
                            if (!oci_commit($conn)) {
                                $error = oci_error($conn);
                                error_log("Error en commit: " . $error['message']);
                                oci_rollback($conn);
                                enviarJSON(['success' => false, 'error' => 'Error en commit: ' . $error['message']]);
                            }

                            error_log(" TRANSACCIÓN COMPLETADA EXITOSAMENTE");

                            enviarJSON([
                                'success' => true,
                                'mensaje' => "Decisión registrada: $decision",
                                'decision' => $decision,
                                'id_aval_actualizado' => $idAval
                            ]);

                        } catch (Throwable $e) {
                            error_log("EXCEPCIÓN: " . $e->getMessage());
                            oci_rollback($conn);
                            enviarJSON(['success' => false, 'error' => $e->getMessage()]);
                        }
                        break;

                        // CASE PARA OBTENER RESULTADO DEL AVAL
                            case 'obtener_resultado_aval':
                                    try {
                                        if (ob_get_level()) ob_clean();
                                        header('Content-Type: application/json; charset=utf-8');
                                        
                                        $id_solicitud = $_GET['id_solicitud'] ?? '';
                                        if (empty($id_solicitud)) {
                                            echo json_encode(['success' => false, 'error' => 'ID requerido']);
                                            exit;
                                        }
                                        
                                        // ✅ OBTENER INFORMACIÓN DE LA SOLICITUD
                                        $querySolicitud = "SELECT 
                                                            NUM_TIENDA,
                                                            PUESTO_SOLICITADO,
                                                            SOLICITADO_POR,
                                                            RAZON,
                                                            TO_CHAR(FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD
                                                        FROM ROY_SOLICITUD_PERSONAL
                                                        WHERE ID_SOLICITUD = :id";
                                        
                                        $stmtSolicitud = oci_parse($conn, $querySolicitud);
                                        oci_bind_by_name($stmtSolicitud, ':id', $id_solicitud);
                                        oci_execute($stmtSolicitud);
                                        $solicitud = oci_fetch_assoc($stmtSolicitud);
                                        oci_free_statement($stmtSolicitud);
                                        
                                        // ✅ OBTENER RESULTADO DEL AVAL (ÚLTIMO PROCESADO)
                                        $queryAval = "SELECT 
                                                        a.DECISION_GERENTE,
                                                        a.ESTADO_AVAL,
                                                        a.GERENTE_NOMBRE,
                                                        TO_CHAR(a.FECHA_RESPUESTA, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_DECISION,
                                                        a.COMENTARIO_GERENTE
                                                    FROM ROY_AVALES_GERENCIA a
                                                    WHERE a.ID_SOLICITUD = :id 
                                                    AND a.DECISION_GERENTE IS NOT NULL
                                                    ORDER BY a.FECHA_RESPUESTA DESC
                                                    FETCH FIRST 1 ROWS ONLY";
                                        
                                        $stmtAval = oci_parse($conn, $queryAval);
                                        oci_bind_by_name($stmtAval, ':id', $id_solicitud);
                                        oci_execute($stmtAval);
                                        $aval = oci_fetch_assoc($stmtAval);
                                        oci_free_statement($stmtAval);
                                        
                                        if (!$aval) {
                                            echo json_encode(['success' => false, 'error' => 'No se encontró decisión del aval']);
                                            exit;
                                        }
                                        
                                        // ✅ PROCESAR CLOB SI ES NECESARIO
                                        if (isset($aval['COMENTARIO_GERENTE']) && is_object($aval['COMENTARIO_GERENTE'])) {
                                            $aval['COMENTARIO_GERENTE'] = $aval['COMENTARIO_GERENTE']->load();
                                        }
                                        
                                        // ✅ RESPUESTA
                                        $respuesta = [
                                            'success' => true,
                                            'data' => [
                                                'solicitud' => [
                                                    'id' => $id_solicitud,
                                                    'tienda' => $solicitud['NUM_TIENDA'] ?? 'N/A',
                                                    'puesto' => $solicitud['PUESTO_SOLICITADO'] ?? 'N/A',
                                                    'supervisor' => $solicitud['SOLICITADO_POR'] ?? 'N/A',
                                                    'razon' => $solicitud['RAZON'] ?? 'N/A',
                                                    'fecha_solicitud' => $solicitud['FECHA_SOLICITUD'] ?? 'N/A'
                                                ],
                                                'aval' => [
                                                    'decision' => $aval['DECISION_GERENTE'] ?? 'N/A',
                                                    'estado' => $aval['ESTADO_AVAL'] ?? 'N/A',
                                                    'gerente' => $aval['GERENTE_NOMBRE'] ?? 'N/A',
                                                    'fecha_decision' => $aval['FECHA_DECISION'] ?? 'N/A',
                                                    'comentario' => $aval['COMENTARIO_GERENTE'] ?? 'Sin comentarios'
                                                ]
                                            ]
                                        ];
                                        
                                        echo json_encode($respuesta);
                                        exit;
                                        
                                    } catch (Exception $e) {
                                        echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
                                        exit;
                                    }
                                    break;

                

        // ===================================================================
        // DEFAULT CASE (SOLO UNO)
        // ===================================================================
        default:
            error_log("Action no reconocida: " . $_GET['action']);
            echo json_encode(['success' => false, 'error' => 'Acción no reconocida']);
            break;
    }

} catch (Exception $e) {
    error_log("Error general: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
} finally {
    if (isset($conn) && $conn) {
        oci_close($conn);
    }
}


?>