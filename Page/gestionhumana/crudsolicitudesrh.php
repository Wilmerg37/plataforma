<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE); // Solo errores críticos
ini_set('display_errors', 0); // No mostrar warnings en output
// Debug logging
error_log("=== NUEVA PETICIÓN ===");
error_log("GET: " . print_r($_GET, true));
error_log("POST: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));

// ===== CONFIGURACIÓN CRÍTICA PARA ARCHIVOS GRANDES =====
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('max_file_uploads', 30);
ini_set('max_execution_time', 400);
ini_set('max_input_time', 400);
ini_set('memory_limit', '256M');

// Debug y errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("=== CONFIGURACIÓN PHP APLICADA ===");
error_log("upload_max_filesize: " . ini_get('upload_max_filesize'));
error_log("post_max_size: " . ini_get('post_max_size'));
error_log("max_file_uploads: " . ini_get('max_file_uploads'));


header('Content-Type: application/json');

// Configuración para desactivar errores en producción
ini_set('display_errors', 0);
error_reporting(0);

// SOLO PARA DEPURAR — luego desactivar en producción
ini_set('display_errors', 1);
error_reporting(E_ALL);



include_once '../../Funsiones/conexion.php';

$conn = Oracle();
if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    die("Error de conexión a la base de datos.");
}


//======================================================================================
// FUNCIONES 
//======================================================================================
            // Función para detectar tipo de error
            function detectarErrorArchivo($error_code, $file_size = 0) {
                switch ($error_code) {
                    case UPLOAD_ERR_OK:
                        return null;
                    case UPLOAD_ERR_INI_SIZE:
                        return "Archivo muy grande (excede upload_max_filesize del servidor)";
                    case UPLOAD_ERR_FORM_SIZE:
                        return "Archivo muy grande (excede el límite del formulario)";
                    case UPLOAD_ERR_PARTIAL:
                        return "El archivo se subió parcialmente (conexión interrumpida)";
                    case UPLOAD_ERR_NO_FILE:
                        return "No se seleccionó ningún archivo";
                    case UPLOAD_ERR_NO_TMP_DIR:
                        return "Error del servidor: falta directorio temporal";
                    case UPLOAD_ERR_CANT_WRITE:
                        return "Error del servidor: no se puede escribir el archivo";
                    case UPLOAD_ERR_EXTENSION:
                        return "Extensión de archivo bloqueada por el servidor";
                    default:
                        return "Error desconocido en la subida ($error_code)";
                }
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
                
                $query = "SELECT DISTINCT
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
                        ORDER BY FECHA_CAMBIO DESC";
                
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
                                    COUNT(DISTINCT h.ID_HISTORICO) as TOTAL_CAMBIOS,
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
                $queryDetalle = "SELECT DISTINCT
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
                                ORDER BY FECHA_CAMBIO DESC";
                
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
           
                function generarReporteFiltradoSinUsuario($conn, $fecha_inicial, $fecha_final, $filtro_tienda, $filtro_supervisor, $filtro_puesto, $incluir_aprobaciones, $incluir_estados) {
                    
                    // OBTENER NOMBRE DEL SUPERVISOR SI SE FILTRA POR CÓDIGO
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
                        } else {
                            return ['error' => 'No se encontró supervisor con código: ' . $filtro_supervisor];
                        }
                        oci_free_statement($stmtNombreSup);
                    }
                    
                    // CONSTRUIR QUERY SIN RESTRICCIONES DE USUARIO
                    $whereConditions = [];
                    
                    $whereConditions[] = "h.FECHA_CAMBIO BETWEEN TO_DATE(:fecha_inicial, 'YYYY-MM-DD') AND TO_DATE(:fecha_final, 'YYYY-MM-DD') + 1";
                    
                    if (!empty($filtro_tienda)) {
                        $whereConditions[] = "s.NUM_TIENDA = :filtro_tienda";
                    }
                    
                    if (!empty($nombre_supervisor_filtrado)) {
                        $whereConditions[] = "s.SOLICITADO_POR = :nombre_supervisor_filtrado";
                    }
                    
                    if (!empty($filtro_puesto)) {
                        $whereConditions[] = "s.PUESTO_SOLICITADO = :filtro_puesto";
                    }
                    
                    $whereClause = implode(' AND ', $whereConditions);
                    
                    $query = "SELECT DISTINCT
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
                            WHERE $whereClause
                            ORDER BY FECHA_CAMBIO DESC";
                    
                    $stmt = oci_parse($conn, $query);
                    
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

                function generarReporteGeneralSinUsuario($conn, $fecha_inicial, $fecha_final, $incluir_aprobaciones, $incluir_estados) {
                    
                    $whereConditions = [];
                    $whereConditions[] = "h.FECHA_CAMBIO BETWEEN TO_DATE(:fecha_inicial, 'YYYY-MM-DD') AND TO_DATE(:fecha_final, 'YYYY-MM-DD') + 1";
                    $whereClause = implode(' AND ', $whereConditions);
                    
                    // QUERY PARA RESUMEN GENERAL
                    $queryResumen = "SELECT 
                                        COUNT(*) as TOTAL_CAMBIOS,
                                        COUNT(DISTINCT s.ID_SOLICITUD) as SOLICITUDES_AFECTADAS,
                                        COUNT(DISTINCT s.NUM_TIENDA) as TIENDAS_AFECTADAS,
                                        COUNT(DISTINCT s.SOLICITADO_POR) as SUPERVISORES_AFECTADOS
                                    FROM ROY_HISTORICO_SOLICITUD h
                                    INNER JOIN ROY_SOLICITUD_PERSONAL s ON h.ID_SOLICITUD = s.ID_SOLICITUD
                                    WHERE $whereClause";
                    
                    $stmtResumen = oci_parse($conn, $queryResumen);
                    oci_bind_by_name($stmtResumen, ':fecha_inicial', $fecha_inicial);
                    oci_bind_by_name($stmtResumen, ':fecha_final', $fecha_final);
                    
                    oci_execute($stmtResumen);
                    $resumen = oci_fetch_assoc($stmtResumen);
                    oci_free_statement($stmtResumen);
                    
                    // QUERY PARA DATOS DETALLADOS
                    $queryDetalle = "SELECT DISTINCT
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
                                    WHERE $whereClause
                                    ORDER BY FECHA_CAMBIO DESC";
                    
                    $stmtDetalle = oci_parse($conn, $queryDetalle);
                    oci_bind_by_name($stmtDetalle, ':fecha_inicial', $fecha_inicial);
                    oci_bind_by_name($stmtDetalle, ':fecha_final', $fecha_final);
                    
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

                //FUNCIÓN AUXILIAR PARA SUBIR ARCHIVOS DE AVAL
                            function subirArchivoAval($archivo, $idSolicitud, $tipo) {
                                $uploadDir = '../uploads/avales/';
                                
                                // Crear directorio si no existe
                                if (!is_dir($uploadDir)) {
                                    mkdir($uploadDir, 0755, true);
                                }
                                
                                // Validar tipo de archivo
                                $tiposPermitidos = ['pdf'];
                                $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                                
                                if (!in_array($extension, $tiposPermitidos)) {
                                    throw new Exception("Tipo de archivo no permitido. Solo se permiten archivos PDF.");
                                }
                                
                                // Validar tamaño (max 10MB)
                                $maxSize = 10 * 1024 * 1024; // 10MB
                                if ($archivo['size'] > $maxSize) {
                                    throw new Exception("El archivo es demasiado grande. Máximo 10MB permitido.");
                                }
                                
                                // Generar nombre único
                                $timestamp = date('Y-m-d_H-i-s');
                                $nombreArchivo = $tipo . '_SOL_' . $idSolicitud . '_' . $timestamp . '.' . $extension;
                                $rutaCompleta = $uploadDir . $nombreArchivo;
                                $rutaRelativa = 'uploads/avales/' . $nombreArchivo;
                                
                                if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                                    throw new Exception('Error subiendo archivo: ' . $archivo['name']);
                                }
                                
                                return [
                                    'nombre_archivo' => $nombreArchivo,
                                    'nombre_original' => $archivo['name'],
                                    'ruta_completa' => $rutaCompleta,
                                    'ruta_relativa' => $rutaRelativa,
                                    'tipo' => $tipo,
                                    'tamaño' => $archivo['size'],
                                    'extension' => $extension
                                ];
                            }


//=========================================================================================
// INICIALIZACION
//==========================================================================================
$action = $_GET['action'] ?? $_POST['action'] ?? '';
if (isset($_GET['action'])) {
    switch ($_GET['action']) {

                    // 🧪 CASE DE PRUEBA - AGREGAR ESTE PRIMERO
                    case 'test_conexion':
                        echo json_encode([
                            'success' => true,
                            'message' => 'Conexión funcionando',
                            'timestamp' => date('Y-m-d H:i:s'),
                            'get_data' => $_GET,
                            'post_data' => $_POST
                        ]);
                        exit;


                            // OBTENER TIENDAS PARA FILTRO
                            case 'get_tiendas_filtro':
                                try {
                                    if (ob_get_level()) ob_clean();
                                    
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
                                            'nombre' => 'Tienda ' . $numeroTienda
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
                                    if (ob_get_level()) ob_clean();
                                    
                                    $query = "SELECT DISTINCT udf1_string AS CODIGO_SUPERVISOR, 
                                                    udf2_string AS NOMBRE_SUPERVISOR
                                            FROM RPS.STORE
                                            WHERE udf1_string IS NOT NULL 
                                            AND udf2_string IS NOT NULL
                                            AND sbs_sid = '680861302000159257'
                                            ORDER BY udf2_string";
                                    
                                    $stmt = oci_parse($conn, $query);
                                    
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
                                if (ob_get_level()) ob_clean();
                                
                                $query = "SELECT DISTINCT PUESTO_SOLICITADO
                                        FROM ROY_SOLICITUD_PERSONAL
                                        WHERE PUESTO_SOLICITADO IS NOT NULL
                                        ORDER BY PUESTO_SOLICITADO";
                                
                                $stmt = oci_parse($conn, $query);
                                
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

                        // HISTORIAL GENERAL FILTRADO GENERAL
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
                                
                                $query = "SELECT DISTINCT
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
                                        ORDER BY FECHA_CAMBIO DESC";
                                
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
                       $resultado = generarReporteFiltradoSinUsuario($conn, $fecha_inicial, $fecha_final, $filtro_tienda, $filtro_supervisor, $filtro_puesto, $incluir_aprobaciones, $incluir_estados);
                    } else {
                        error_log("📊 Generando REPORTE GENERAL en formato: $formato");
                        $resultado = generarReporteGeneralSinUsuario($conn, $fecha_inicial, $fecha_final, $incluir_aprobaciones, $incluir_estados);
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


                        case 'get_solicitudes':
                            error_log("Obteniendo solicitudes APROBADAS para RRHH...");
                            
                                $query = "SELECT
                                    s.ID_SOLICITUD,
                                    s.NUM_TIENDA,
                                    s.PUESTO_SOLICITADO,
                                    s.ESTADO_SOLICITUD,
                                    s.ESTADO_APROBACION,
                                    s.DIRIGIDO_RH,
                                    TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD,
                                    TO_CHAR(s.FECHA_MODIFICACION, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_MODIFICACION,
                                    s.SOLICITADO_POR,
                                    s.RAZON,
                                    s.DIRIGIDO_A,
                                    s.COMENTARIO_SOLICITUD,

                                    -- ID_HISTORICO último con mensajes
                                    (
                                        SELECT h.ID_HISTORICO
                                        FROM ROY_HISTORICO_SOLICITUD h
                                        WHERE h.ID_SOLICITUD = s.ID_SOLICITUD
                                        AND EXISTS (
                                            SELECT 1 FROM ROY_CHAT_HISTORICO c WHERE c.ID_HISTORICO = h.ID_HISTORICO
                                        )
                                        ORDER BY h.FECHA_CAMBIO DESC
                                        FETCH FIRST 1 ROWS ONLY
                                    ) AS ID_HISTORICO,

                                    -- Conteo de mensajes NO LEÍDOS del SUPERVISOR
                                    (
                                        SELECT COUNT(*)
                                        FROM ROY_CHAT_HISTORICO ch
                                        WHERE ch.ID_HISTORICO = (
                                            SELECT h.ID_HISTORICO
                                            FROM ROY_HISTORICO_SOLICITUD h
                                            WHERE h.ID_SOLICITUD = s.ID_SOLICITUD
                                            AND EXISTS (
                                                SELECT 1 FROM ROY_CHAT_HISTORICO c WHERE c.ID_HISTORICO = h.ID_HISTORICO
                                            )
                                            ORDER BY h.FECHA_CAMBIO DESC
                                            FETCH FIRST 1 ROWS ONLY
                                        )
                                        AND UPPER(ch.ES_LEIDO) = 'N'
                                        AND UPPER(ch.ROL) = 'SUPERVISOR'
                                    ) AS NO_LEIDOS,

                                    -- TIENE_ARCHIVOS
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

                                    -- TIENE_SELECCION
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

                                    -- CORREGIDO: TIENE_OBSERVACIONES_DIA_PRUEBA
                                    (
                                        SELECT CASE
                                            WHEN MAX(obs.ID_OBSERVACION) IS NOT NULL THEN 1 ELSE 0
                                        END
                                        FROM ROY_OBSERVACIONES_DIA_PRUEBA obs
                                        WHERE obs.ID_SOLICITUD = s.ID_SOLICITUD
                                        AND obs.ESTADO = 'ENVIADO'
                                    ) AS TIENE_OBSERVACIONES_DIA_PRUEBA

                                FROM ROY_SOLICITUD_PERSONAL s
                                WHERE s.ESTADO_APROBACION = 'Aprobado'
                                ORDER BY s.FECHA_SOLICITUD DESC";

                            $stmt = oci_parse($conn, $query);
                            
                            if (!oci_execute($stmt)) {
                                $error = oci_error($stmt);
                                error_log("❌ Error ejecutando consulta RRHH: " . print_r($error, true));
                                echo json_encode(['success' => false, 'error' => 'Error en consulta: ' . $error['message']]);
                                oci_close($conn);
                                break;
                            }

                            $solicitudes = [];
                            while ($row = oci_fetch_assoc($stmt)) {
                                $solicitudes[] = [
                                    'ID_SOLICITUD' => $row['ID_SOLICITUD'],
                                    'NUM_TIENDA' => $row['NUM_TIENDA'],
                                    'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
                                    'ESTADO_SOLICITUD' => $row['ESTADO_SOLICITUD'],
                                    'ESTADO_APROBACION' => $row['ESTADO_APROBACION'] ?: 'Por Aprobar',
                                    'DIRIGIDO_RH' => $row['DIRIGIDO_RH'],  // ✅ AGREGAR ESTA LÍNEA
                                    'FECHA_SOLICITUD' => $row['FECHA_SOLICITUD'],
                                    'FECHA_MODIFICACION' => $row['FECHA_MODIFICACION'],
                                    'SOLICITADO_POR' => $row['SOLICITADO_POR'],
                                    'RAZON' => $row['RAZON'],
                                    'DIRIGIDO_A' => $row['DIRIGIDO_A'],
                                    'COMENTARIO_SOLICITUD' => $row['COMENTARIO_SOLICITUD'],
                                    'ID_HISTORICO' => $row['ID_HISTORICO'],
                                    'TIENE_ARCHIVOS' => $row['TIENE_ARCHIVOS'],
                                    'TIENE_SELECCION' => $row['TIENE_SELECCION'],
                                    'TIENE_OBSERVACIONES_DIA_PRUEBA' => $row['TIENE_OBSERVACIONES_DIA_PRUEBA'],
                                    'NO_LEIDOS' => intval($row['NO_LEIDOS'])
                                ];
                            }

                            oci_free_statement($stmt);
                            oci_close($conn);

                            error_log("✅ Solicitudes APROBADAS obtenidas para RRHH: " . count($solicitudes));
                            echo json_encode($solicitudes);
                            break;

                //VER CVS SELECCIONADOS POR SUPERVISORES
                case 'ver_resumen_cvs':
                    error_log("🔍 CASE ver_resumen_cvs ejecutado");
                    
                    // ✅ OBTENER ID_SOLICITUD DE GET (ya no necesitas validar action)
                    $idSolicitud = $_GET['id_solicitud'] ?? $_POST['id_solicitud'] ?? null;
                    error_log("📋 ID Solicitud recibido: " . $idSolicitud);
                    
                    if (empty($idSolicitud)) {
                        echo json_encode(['success' => false, 'error' => 'ID de solicitud no proporcionado']);
                        exit;
                    }

                    try {
                        $query = "SELECT 
                                    sel.ARCHIVOS_SELECCIONADOS,
                                    sol.SOLICITADO_POR,
                                    TO_CHAR(sol.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD
                                FROM ROY_SELECCION_CVS sel
                                JOIN ROY_SOLICITUD_PERSONAL sol ON sol.ID_SOLICITUD = sel.ID_SOLICITUD
                                WHERE sel.ID_SOLICITUD = :id
                                AND sel.ES_ACTIVA = 'Y'
                                ORDER BY sel.FECHA_SELECCION DESC
                                FETCH FIRST 1 ROWS ONLY";

                        $stmt = oci_parse($conn, $query);
                        oci_bind_by_name($stmt, ':id', $idSolicitud);

                        if (!oci_execute($stmt)) {
                            $error = oci_error($stmt);
                            echo json_encode([
                                'success' => false,
                                'error' => 'Error en la consulta SQL: ' . $error['message']
                            ]);
                            exit;
                        }

                        $archivos = [];
                        $supervisor = '';
                        $fechaSolicitud = '';

                        $row = oci_fetch_assoc($stmt);
                        if ($row) {
                            $supervisor = $row['SOLICITADO_POR'];
                            $fechaSolicitud = $row['FECHA_SOLICITUD'];

                            if (!empty($row['ARCHIVOS_SELECCIONADOS'])) {
                                $clob = $row['ARCHIVOS_SELECCIONADOS'];
                                $contenido = is_object($clob) && method_exists($clob, 'load') ? $clob->load() : $clob;

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
                        }

                        oci_free_statement($stmt);

                        echo json_encode([
                            'success' => true,
                            'archivos' => $archivos,
                            'supervisor' => $supervisor,
                            'fecha' => $fechaSolicitud,
                            'total' => count($archivos)
                        ]);
                        
                    } catch (Exception $e) {
                        error_log("❌ Error en ver_resumen_cvs: " . $e->getMessage());
                        echo json_encode([
                            'success' => false,
                            'error' => 'Error interno: ' . $e->getMessage()
                        ]);
                    }
                    exit;
                            //CAMBIAR ESTADO - CORREGIDO PARA ACEPTAR PDFs
                case 'toggle_solicitud_status':
                    if (empty($_POST['id_solicitud']) || empty($_POST['nuevo_estado']) || !isset($_POST['comentario'])) {
                        echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios.']);
                        oci_close($conn);
                        break;
                    }

                    $id = $_POST['id_solicitud'];
                    $nuevo_estado = $_POST['nuevo_estado'];
                    $comentario_nuevo = $_POST['comentario'];
                    $tipoArchivo = $_POST['tipo_archivo'] ?? null; // ← NUEVO

                    // Obtener estado anterior
                    $queryAnterior = "SELECT ESTADO_SOLICITUD FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
                    $stmtAnt = oci_parse($conn, $queryAnterior);
                    oci_bind_by_name($stmtAnt, ':id', $id);
                    oci_execute($stmtAnt);
                    $estado_anterior = ($row = oci_fetch_assoc($stmtAnt)) ? $row['ESTADO_SOLICITUD'] : '';
                    oci_free_statement($stmtAnt);

                    // Obtener último comentario anterior
                    $comentario_anterior = '';
                    $queryComentario = "SELECT COMENTARIO_NUEVO FROM ROY_HISTORICO_SOLICITUD 
                                        WHERE ID_SOLICITUD = :id 
                                        AND ID_HISTORICO = (SELECT MAX(ID_HISTORICO) FROM ROY_HISTORICO_SOLICITUD WHERE ID_SOLICITUD = :id)";
                    $stmtCom = oci_parse($conn, $queryComentario);
                    oci_bind_by_name($stmtCom, ':id', $id);
                    oci_execute($stmtCom);
                    if ($row = oci_fetch_assoc($stmtCom)) {
                        $comentario_anterior = $row['COMENTARIO_NUEVO'];
                    }
                    oci_free_statement($stmtCom);

                    // Actualizar solicitud
                    $queryUpdate = "UPDATE ROY_SOLICITUD_PERSONAL SET 
                                    ESTADO_SOLICITUD = :estado, 
                                    COMENTARIO_SOLICITUD = :comentario,
                                    FECHA_MODIFICACION = SYSDATE 
                                    WHERE ID_SOLICITUD = :id";
                    $stmtUpd = oci_parse($conn, $queryUpdate);
                    oci_bind_by_name($stmtUpd, ':estado', $nuevo_estado);
                    oci_bind_by_name($stmtUpd, ':comentario', $comentario_nuevo);
                    oci_bind_by_name($stmtUpd, ':id', $id);
                    oci_execute($stmtUpd);
                    oci_free_statement($stmtUpd);

                    // Obtener ID_HISTORICO desde la secuencia
                    $stmtSeq = oci_parse($conn, "SELECT SEQ_HISTORICO_SOLICITUD.NEXTVAL AS ID FROM DUAL");
                    oci_execute($stmtSeq);
                    $rowSeq = oci_fetch_assoc($stmtSeq);
                    $idHistorico = $rowSeq['ID'];
                    oci_free_statement($stmtSeq);

                    // Insertar en historial con ID fijo
                    $queryHistorial = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                    (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, COMENTARIO_ANTERIOR, COMENTARIO_NUEVO, FECHA_CAMBIO)
                    VALUES (:id_solicitud, :estado_anterior, :estado_nuevo, :comentario_anterior, :comentario_nuevo, SYSDATE)
                    RETURNING ID_HISTORICO INTO :id_historico";
                    $stmtHist = oci_parse($conn, $queryHistorial);
                    oci_bind_by_name($stmtHist, ':id_solicitud', $id);
                    oci_bind_by_name($stmtHist, ':estado_anterior', $estado_anterior);
                    oci_bind_by_name($stmtHist, ':estado_nuevo', $nuevo_estado);
                    oci_bind_by_name($stmtHist, ':comentario_anterior', $comentario_anterior);
                    oci_bind_by_name($stmtHist, ':comentario_nuevo', $comentario_nuevo);
                    oci_bind_by_name($stmtHist, ':id_historico', $idHistorico, -1, SQLT_INT);
                    oci_execute($stmtHist);
                    oci_free_statement($stmtHist);

                    // Insertar en chat si aplica
                    if (!empty($comentario_nuevo) && $idHistorico !== null) {
                        
                        $sqlChat = "INSERT INTO ROY_CHAT_HISTORICO (
                                    ID_MENSAJE,       
                                    ID_HISTORICO, 
                                    ROL, 
                                    MENSAJE,          
                                    FECHA,
                                    REMITENTE,
                                    ES_LEIDO
                                ) VALUES (
                                    SEQ_CHAT_MENSAJE.NEXTVAL,  
                                    :id_historico, 
                                    'RRHH', 
                                    :mensaje, 
                                    SYSDATE,
                                    :remitente,
                                    'N'
                                )";

                        $queryRemitente = "SELECT DIRIGIDO_A FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
                        $stmtRem = oci_parse($conn, $queryRemitente);
                        oci_bind_by_name($stmtRem, ':id', $id);
                        oci_execute($stmtRem);
                        $rowRem = oci_fetch_assoc($stmtRem);
                        $remitente = $rowRem['DIRIGIDO_A'] ?? 'RRHH';
                        oci_free_statement($stmtRem);

                        $stmtChat = oci_parse($conn, $sqlChat);
                        oci_bind_by_name($stmtChat, ':id_historico', $idHistorico);
                        oci_bind_by_name($stmtChat, ':mensaje', $comentario_nuevo);
                        oci_bind_by_name($stmtChat, ':remitente', $remitente);
                        oci_execute($stmtChat);
                        oci_free_statement($stmtChat);
                    }

                    // Subir archivos
                    $archivosSubidos = [];
                    $archivos_field = $_FILES['archivos'] ?? $_FILES['archivos[]'] ?? null;

                    if ($archivos_field && isset($archivos_field['name'])) {
                        $rutaBase = '../gestionhumana/archivos_aprobados/';
                        if (!is_dir($rutaBase)) mkdir($rutaBase, 0777, true);

                        $nombres = is_array($archivos_field['name']) ? $archivos_field['name'] : [$archivos_field['name']];
                        $tmp_names = is_array($archivos_field['tmp_name']) ? $archivos_field['tmp_name'] : [$archivos_field['tmp_name']];
                        $errors = is_array($archivos_field['error']) ? $archivos_field['error'] : [$archivos_field['error']];

                        for ($i = 0; $i < count($nombres); $i++) {
                            if ($errors[$i] === UPLOAD_ERR_OK && !empty($nombres[$i])) {
                                $nombreOriginal = basename($nombres[$i]);
                                $tmp = $tmp_names[$i];
                                $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
                                $permitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

                                if (!in_array($extension, $permitidos) || !file_exists($tmp)) continue;

                                $fileSize = filesize($tmp);
                                if ($fileSize === 0 || $fileSize > 50 * 1024 * 1024) continue;

                                $nombreFinal = 'solicitud_' . $id . '_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
                                $rutaFinal = $rutaBase . $nombreFinal;
                                $rutaRelativa = 'gestionhumana/archivos_aprobados/' . $nombreFinal;

                                if (move_uploaded_file($tmp, $rutaFinal) && file_exists($rutaFinal)) {
                                    $stmtArchivo = oci_parse($conn, "INSERT INTO ROY_ARCHIVOS_SOLICITUD (
                                        ID_SOLICITUD,
                                        ID_HISTORICO,
                                        NOMBRE_ARCHIVO,
                                        FECHA_SUBIDA,
                                        TIPO_ARCHIVO
                                    ) VALUES (
                                        :id_solicitud,
                                        :id_historico,
                                        :nombre_archivo,
                                        SYSDATE,
                                        :tipo_archivo
                                    )");
                                    oci_bind_by_name($stmtArchivo, ':id_solicitud', $id);
                                    oci_bind_by_name($stmtArchivo, ':id_historico', $idHistorico);
                                    oci_bind_by_name($stmtArchivo, ':nombre_archivo', $rutaRelativa);
                                    oci_bind_by_name($stmtArchivo, ':tipo_archivo', $tipoArchivo);
                                    if (!oci_execute($stmtArchivo)) {
                                        $e = oci_error($stmtArchivo);
                                        error_log("❌ Error al insertar archivo: " . $e['message']);
                                    } else {
                                        $archivosSubidos[] = $nombreOriginal;
                                    }

                                    oci_free_statement($stmtArchivo);
                                }
                            }
                        }
                    }

                    echo json_encode([
                        'success' => true,
                        'mensaje' => !empty($archivosSubidos)
                            ? 'Estado actualizado y ' . count($archivosSubidos) . ' archivo(s) subido(s) correctamente.'
                            : 'Estado actualizado correctamente.'
                    ]);
                    oci_close($conn);
                    break;




                // VER ARCHIVOS
                case 'get_archivos':
                    error_log("=== OBTENIENDO ARCHIVOS POR TIPO ===");

                    if (!isset($_GET['id']) || empty($_GET['id'])) {
                        error_log("ID de solicitud no proporcionado");
                        echo json_encode([
                            'error' => 'ID de solicitud requerido',
                            'archivos' => []
                        ]);
                        break;
                    }

                    $id = $_GET['id'];
                    $tipoArchivo = strtoupper($_GET['tipo'] ?? 'CVS'); // Default a CVS si no viene tipo
                    error_log("Buscando archivos para solicitud ID: $id y tipo: $tipoArchivo");

                    try {
                        // Buscar el último ID_HISTORICO del tipo solicitado (CVS, PSICOMETRICA, POLIGRAFO)
                        $queryHist = "SELECT MAX(ID_HISTORICO) AS ID_HISTORICO 
                                    FROM ROY_HISTORICO_SOLICITUD 
                                    WHERE ID_SOLICITUD = :id 
                                    AND LOWER(ESTADO_NUEVO) LIKE :tipo_estado";

                        $stmtHist = oci_parse($conn, $queryHist);
                        $tipoLike = '%' . strtolower($tipoArchivo) . '%';
                        oci_bind_by_name($stmtHist, ':id', $id);
                        oci_bind_by_name($stmtHist, ':tipo_estado', $tipoLike);
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
                                'mensaje' => "No hay archivos recientes para el tipo: $tipoArchivo.",
                                'solicitud_id' => $id
                            ]);
                            break;
                        }

                        // Obtener archivos vinculados al ID_HISTORICO Y tipo
                        $query = "SELECT 
                                    NOMBRE_ARCHIVO, 
                                    TO_CHAR(FECHA_SUBIDA, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_SUBIDA,
                                    ID_ARCHIVO
                                FROM ROY_ARCHIVOS_SOLICITUD 
                                WHERE ID_SOLICITUD = :id 
                                AND ID_HISTORICO = :id_hist
                                AND UPPER(TIPO_ARCHIVO) = :tipo_arch
                                ORDER BY FECHA_SUBIDA DESC";

                        $stmt = oci_parse($conn, $query);
                        oci_bind_by_name($stmt, ':id', $id);
                        oci_bind_by_name($stmt, ':id_hist', $idHistorico);
                        oci_bind_by_name($stmt, ':tipo_arch', $tipoArchivo);
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
                            'solicitud_id' => $id,
                            'tipo_archivo' => $tipoArchivo
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


                                        // CASE HISTORIAL INDIVIDUAL MODIFICADO
                case 'get_historial_individual':
                    if (!isset($_GET['id'])) {
                        echo json_encode([]);
                        break;
                    }

                    $id = $_GET['id'];

                    $query = "SELECT
                                h.ID_HISTORICO,
                                sp.NUM_TIENDA,
                                h.ESTADO_ANTERIOR,
                                h.ESTADO_NUEVO,
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
                        $row['ARCHIVOS'] = [];

                        // Buscar archivos relacionados a este ID_HISTORICO
                        $query_archivos = "SELECT NOMBRE_ARCHIVO FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_HISTORICO = :id_historico";
                        $stmt_arch = oci_parse($conn, $query_archivos);
                        oci_bind_by_name($stmt_arch, ':id_historico', $row['ID_HISTORICO']);
                        oci_execute($stmt_arch);

                        while ($arch = oci_fetch_assoc($stmt_arch)) {
                            $row['ARCHIVOS'][] = $arch;
                        }

                        oci_free_statement($stmt_arch);
                        $historial[] = $row;
                    }

                    oci_free_statement($stmt);
                    oci_close($conn);

                    header('Content-Type: application/json');
                    echo json_encode($historial);
                    break;

                    //CASE FUNCIONALIDAD DE CHAT EMERGENTE "VER COMENTARIO"
                // OBTENER COMENTARIOS DEL CHAT (UNIFICADO) - VERSIÓN RRHH CORREGIDA
                case 'get_comentarios_chat_rh':
                    $idHistorico = $_POST['id_historico'] ?? $_GET['id_historico'] ?? 0;

                    if (!$idHistorico) {
                        echo json_encode(['success' => false, 'error' => 'ID histórico requerido']);
                        exit;
                    }

                    try {
                        $mensajes = [];

                        // OBTENER SOLO MENSAJES DEL CHAT DE ROY_CHAT_HISTORICO
                        $queryChat = "SELECT 
                                        ID_MENSAJE as id,
                                        ID_HISTORICO as id_historico,
                                        ROL as rol,
                                        TO_CHAR(MENSAJE) as mensaje,
                                        TO_CHAR(FECHA, 'DD-MM-YYYY HH24:MI:SS') AS fecha
                                    FROM ROY_CHAT_HISTORICO
                                    WHERE ID_HISTORICO = :idHistorico
                                    ORDER BY FECHA ASC";

                        $stmtChat = oci_parse($conn, $queryChat);
                        oci_bind_by_name($stmtChat, ':idHistorico', $idHistorico);

                        if (oci_execute($stmtChat)) {
                            while ($rowChat = oci_fetch_assoc($stmtChat)) {
                                $mensajes[] = [
                                    'id' => $rowChat['ID'],
                                    'id_historico' => $rowChat['ID_HISTORICO'],
                                    'rol' => $rowChat['ROL'],
                                    'mensaje' => $rowChat['MENSAJE'],
                                    'fecha' => $rowChat['FECHA'],
                                    'es_comentario_inicial' => false
                                ];
                            }
                        }
                        oci_free_statement($stmtChat);

                        echo json_encode(['success' => true, 'mensajes' => $mensajes]);
                        
                    } catch (Exception $e) {
                        error_log("Excepción en get_comentarios_chat: " . $e->getMessage());
                        echo json_encode(['success' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
                    }
                    break;


                //case para guardar la respuesta
                case 'guardar_respuesta_chat_rh':
                    $id_historico = $_POST['id_historico'] ?? null;
                    $mensaje = $_POST['mensaje'] ?? null;
                    $rol = $_POST['rol'] ?? 'RRHH';
                    $remitente = $_POST['remitente'] ?? 'RRHH_SISTEMA'; 

                    if (empty($id_historico) || empty($mensaje)) {
                        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
                        exit;
                    }

                    try {
                        $query = "INSERT INTO ROY_CHAT_HISTORICO (
                                    ID_MENSAJE,
                                    ID_HISTORICO,
                                    ROL,
                                    MENSAJE,
                                    FECHA,
                                    REMITENTE,         -- ← LINEA AGREGADA
                                    ES_LEIDO           -- ← LINEA AGREGADA
                                ) VALUES (
                                    SEQ_CHAT_MENSAJE.NEXTVAL,
                                    :id_historico, 
                                    :rol, 
                                    EMPTY_CLOB(),
                                    SYSDATE,
                                    :remitente,        -- ← CONCEPTO AGREGADO
                                    'N'                -- ← CONCEPTO AGREGADO
                                ) RETURNING MENSAJE INTO :mensaje_clob";

                        $stmt = oci_parse($conn, $query);
                        oci_bind_by_name($stmt, ':id_historico', $id_historico);
                        oci_bind_by_name($stmt, ':rol', $rol);
                        oci_bind_by_name($stmt, ':remitente', $remitente); // NUEVO
                        
                        $clob = oci_new_descriptor($conn, OCI_D_LOB);
                        oci_bind_by_name($stmt, ':mensaje_clob', $clob, -1, OCI_B_CLOB);

                        if (oci_execute($stmt, OCI_DEFAULT)) {
                            if ($clob->save($mensaje)) {
                                oci_commit($conn);
                                echo json_encode(['success' => true, 'message' => 'Respuesta guardada correctamente']);
                            } else {
                                oci_rollback($conn);
                                echo json_encode(['success' => false, 'error' => 'Error al guardar contenido del mensaje']);
                            }
                        } else {
                            $e = oci_error($stmt);
                            oci_rollback($conn);
                            echo json_encode(['success' => false, 'error' => 'Error en base de datos: ' . $e['message']]);
                        }

                        $clob->free();
                        oci_free_statement($stmt);
                        
                    } catch (Exception $e) {
                        oci_rollback($conn);
                        error_log("Error en guardar_respuesta_chat: " . $e->getMessage());
                        echo json_encode(['success' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
                    }
                    break;

                    case 'marcar_mensajes_leidos_rh':
                    $idHistorico = $_POST['id_historico'] ?? 0;
                    
                    if (!$idHistorico) {
                        echo json_encode(['success' => false, 'error' => 'ID histórico requerido']);
                        exit;
                    }
                    
                    try {
                        // Marcar mensajes del SUPERVISOR como leídos por RRHH
                        $query = "UPDATE ROY_CHAT_HISTORICO 
                                SET ES_LEIDO = 'Y' 
                                WHERE ID_HISTORICO = :idHistorico 
                                AND UPPER(ROL) = 'SUPERVISOR'
                                AND UPPER(ES_LEIDO) = 'N'";
                        
                        $stmt = oci_parse($conn, $query);
                        oci_bind_by_name($stmt, ':idHistorico', $idHistorico);
                        
                        if (oci_execute($stmt)) {
                            oci_commit($conn);
                            echo json_encode(['success' => true]);
                        } else {
                            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
                        }
                        
                        oci_free_statement($stmt);
                        
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                    }
                    break;


                // 🆕 AGREGAR ESTE CASE DESPUÉS DE ver_resumen_cvs
            case 'get_observaciones_completas_rrhh':
                try {
                    $id_solicitud = $_GET['id_solicitud'] ?? $_POST['id_solicitud'] ?? null;
                    
                    if (!$id_solicitud) {
                        throw new Exception('ID de solicitud no proporcionado');
                    }
                    
                    // ✅ OBTENER INFORMACIÓN BÁSICA DE LA SOLICITUD
                    $querySolicitud = "SELECT 
                                        ID_SOLICITUD,
                                        NUM_TIENDA,
                                        PUESTO_SOLICITADO,
                                        ESTADO_SOLICITUD,
                                        SOLICITADO_POR,
                                        TO_CHAR(FECHA_SOLICITUD, 'DD/MM/YYYY') as FECHA_SOLICITUD
                                    FROM ROY_SOLICITUD_PERSONAL 
                                    WHERE ID_SOLICITUD = :id_solicitud";
                    
                    $stmtSolicitud = oci_parse($conn, $querySolicitud);
                    oci_bind_by_name($stmtSolicitud, ':id_solicitud', $id_solicitud);
                    oci_execute($stmtSolicitud);
                    
                    $solicitud = oci_fetch_assoc($stmtSolicitud);
                    oci_free_statement($stmtSolicitud);
                    
                    if (!$solicitud) {
                        throw new Exception('No se encontró la solicitud especificada');
                    }
                    
                    // ✅ OBTENER TODOS LOS CICLOS DE "DÍA DE PRUEBA" 
                    $queryCiclos = "SELECT 
                                    ID_HISTORICO,
                                    TO_CHAR(FECHA_CAMBIO, 'DD/MM/YYYY HH24:MI') as FECHA_CICLO,
                                    COMENTARIO_NUEVO
                                FROM ROY_HISTORICO_SOLICITUD 
                                WHERE ID_SOLICITUD = :id_solicitud 
                                AND (LOWER(ESTADO_NUEVO) LIKE '%día de prueba%' 
                                    OR LOWER(ESTADO_NUEVO) LIKE '%dia de prueba%')
                                ORDER BY FECHA_CAMBIO DESC";
                    
                    $stmtCiclos = oci_parse($conn, $queryCiclos);
                    oci_bind_by_name($stmtCiclos, ':id_solicitud', $id_solicitud);
                    oci_execute($stmtCiclos);
                    
                    $ciclos = [];
                    while ($rowCiclo = oci_fetch_assoc($stmtCiclos)) {
                        $ciclos[] = $rowCiclo;
                    }
                    oci_free_statement($stmtCiclos);
                    
                    // ✅ OBTENER TODAS LAS OBSERVACIONES CON SUS CICLOS
                    $queryObservaciones = "SELECT 
                                                o.ID_OBSERVACION,
                                                o.ID_SOLICITUD,
                                                o.SUPERVISOR_CODIGO,
                                                o.SUPERVISOR_NOMBRE,
                                                o.CANDIDATO_NOMBRE,
                                                o.CANDIDATO_DOCUMENTO,
                                                TO_CHAR(o.FECHA_DIA_PRUEBA, 'DD/MM/YYYY') as FECHA_DIA_PRUEBA,
                                                o.HORA_INICIO,
                                                o.HORA_FIN,
                                                o.PUESTO_EVALUADO,
                                                o.OBSERVACIONES_DET,
                                                o.DESEMPENO_GENERAL,  
                                                o.PUNTUALIDAD,
                                                o.ACTITUD,
                                                o.CONOCIMIENTOS,
                                                o.RECOMENDACION_SUP,
                                                TO_CHAR(o.FECHA_CREACION, 'DD/MM/YYYY HH24:MI') as FECHA_CREACION,
                                                o.ESTADO,
                                                o.ID_HIST_ASOCIADO,
                                                h.FECHA_CAMBIO as FECHA_CICLO_COMPLETA
                                            FROM ROY_OBSERVACIONES_DIA_PRUEBA o
                                            LEFT JOIN ROY_HISTORICO_SOLICITUD h ON o.ID_HIST_ASOCIADO = h.ID_HISTORICO
                                            WHERE o.ID_SOLICITUD = :id_solicitud 
                                            AND o.ESTADO = 'ENVIADO'
                                            AND o.ID_OBSERVACION = (
                                                SELECT MAX(o2.ID_OBSERVACION)
                                                FROM ROY_OBSERVACIONES_DIA_PRUEBA o2
                                                WHERE o2.ID_SOLICITUD = :id_solicitud
                                                AND o2.ESTADO = 'ENVIADO'
                                            )
                                            ORDER BY o.FECHA_CREACION DESC";
                    
                    $stmtObservaciones = oci_parse($conn, $queryObservaciones);
                    oci_bind_by_name($stmtObservaciones, ':id_solicitud', $id_solicitud);
                    oci_execute($stmtObservaciones);
                    
                    $observaciones = [];
                    $totalObservaciones = 0;
                    $recomendados = 0;
                    $noRecomendados = 0;
                    
                    while ($rowObs = oci_fetch_assoc($stmtObservaciones)) {
                        // 🔄 PROCESAR CLOB SI ES NECESARIO
                        if (isset($rowObs['OBSERVACIONES_DET']) && is_object($rowObs['OBSERVACIONES_DET'])) {
                            $rowObs['OBSERVACIONES_DET'] = $rowObs['OBSERVACIONES_DET']->load();
                        }
                        
                        // 📊 ESTADÍSTICAS
                        $totalObservaciones++;
                        if ($rowObs['RECOMENDACION_SUP'] === 'RECOMENDADO') {
                            $recomendados++;
                        } else {
                            $noRecomendados++;
                        }
                        
                        $observaciones[] = $rowObs;
                    }
                    oci_free_statement($stmtObservaciones);
                    
                    // 📊 CALCULAR ESTADÍSTICAS GENERALES
                    $estadisticas = [
                        'total_observaciones' => $totalObservaciones,
                        'total_ciclos' => count($ciclos),
                        'recomendados' => $recomendados,
                        'no_recomendados' => $noRecomendados,
                        'porcentaje_recomendado' => $totalObservaciones > 0 ? round(($recomendados / $totalObservaciones) * 100, 1) : 0
                    ];
                    
                    // ✅ RESPUESTA COMPLETA
                    echo json_encode([
                        'success' => true,
                        'solicitud' => $solicitud,
                        'ciclos' => $ciclos,
                        'observaciones' => $observaciones,
                        'estadisticas' => $estadisticas,
                        'message' => 'Información completa obtenida correctamente'
                    ]);
                    
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'error' => $e->getMessage(),
                        'debug_info' => [
                            'id_solicitud' => $id_solicitud ?? 'No proporcionado',
                            'timestamp' => date('Y-m-d H:i:s')
                        ]
                    ]);
                }
                break; 
                // CASE COMPLETO PARA ENVÍO A AVAL GERENCIA
                        case 'enviar_aval_gerencia':
                            try {
                                $id_solicitud = $_POST['id_solicitud'] ?? null;
                                $comentario_rrhh = $_POST['comentario'] ?? '';
                                $enviado_por = $_POST['enviado_por'] ?? 'Sistema RRHH';
                                
                                if (!$id_solicitud) {
                                    throw new Exception('ID de solicitud requerido');
                                }
                                
                                // ✅ VERIFICAR ARCHIVOS REQUERIDOS
                                if (!isset($_FILES['pdf_reporte']) || $_FILES['pdf_reporte']['error'] !== 0) {
                                    throw new Exception('El archivo PDF del reporte de observaciones es requerido');
                                }
                                
                                if (!isset($_FILES['cv_candidato']) || $_FILES['cv_candidato']['error'] !== 0) {
                                    throw new Exception('El archivo CV del candidato es requerido');
                                }
                                
                                // ✅ VERIFICAR QUE LA SOLICITUD EXISTE Y TIENE OBSERVACIONES (SIN BLOQUEAR MÚLTIPLES AVALES)
                                $queryVerificar = "SELECT 
                                                    s.ID_SOLICITUD,
                                                    s.ESTADO_SOLICITUD,
                                                    s.NUM_TIENDA,
                                                    s.PUESTO_SOLICITADO,
                                                    s.SOLICITADO_POR,
                                                    (SELECT COUNT(*) FROM ROY_HISTORICO_SOLICITUD h 
                                                    WHERE h.ID_SOLICITUD = s.ID_SOLICITUD 
                                                    AND h.OBSERVACIONES_ENVIADAS = 'Y' 
                                                    AND h.TIPO_EVENTO = 'OBSERVACIONES_ENVIADAS') as TIENE_OBSERVACIONES
                                                FROM ROY_SOLICITUD_PERSONAL s 
                                                WHERE s.ID_SOLICITUD = :id_solicitud";
                                
                                $stmtVerificar = oci_parse($conn, $queryVerificar);
                                oci_bind_by_name($stmtVerificar, ':id_solicitud', $id_solicitud);
                                oci_execute($stmtVerificar);
                                
                                $solicitudData = oci_fetch_assoc($stmtVerificar);
                                if (!$solicitudData) {
                                    throw new Exception('La solicitud especificada no existe');
                                }
                                
                                if ($solicitudData['TIENE_OBSERVACIONES'] == 0) {
                                    throw new Exception('Esta solicitud no tiene observaciones del supervisor. No se puede enviar a aval gerencia.');
                                }
                                
                                // ✅ NUEVA LÓGICA: Permitir múltiples avales pero marcar los anteriores como inactivos
                                $queryDesactivarAnteriores = "UPDATE ROY_AVALES_GERENCIA 
                                                            SET ACTIVO = 'N',
                                                                FECHA_MODIFICACION = SYSDATE
                                                            WHERE ID_SOLICITUD = :id_solicitud 
                                                            AND ESTADO_AVAL = 'PENDIENTE'
                                                            AND ACTIVO = 'Y'";
                                
                                $stmtDesactivar = oci_parse($conn, $queryDesactivarAnteriores);
                                oci_bind_by_name($stmtDesactivar, ':id_solicitud', $id_solicitud);
                                oci_execute($stmtDesactivar);
                                oci_free_statement($stmtDesactivar);
                                
                                $estadoAnterior = $solicitudData['ESTADO_SOLICITUD'];
                                $estadoNuevo = "Pendiente Aval Gerencia";
                                
                                oci_free_statement($stmtVerificar);
                                
                                // ✅ OBTENER INFORMACIÓN DEL CANDIDATO DE LAS OBSERVACIONES
                                $queryCandidato = "SELECT 
                                                    o.CANDIDATO_NOMBRE,
                                                    o.SUPERVISOR_NOMBRE
                                                FROM ROY_OBSERVACIONES_DIA_PRUEBA o
                                                WHERE o.ID_SOLICITUD = :id_solicitud
                                                ORDER BY o.FECHA_CREACION DESC
                                                FETCH FIRST 1 ROWS ONLY";
                                
                                $stmtCandidato = oci_parse($conn, $queryCandidato);
                                oci_bind_by_name($stmtCandidato, ':id_solicitud', $id_solicitud);
                                oci_execute($stmtCandidato);
                                
                                $candidatoData = oci_fetch_assoc($stmtCandidato);
                                $candidato_nombre = $candidatoData['CANDIDATO_NOMBRE'] ?? 'No especificado';
                                $supervisor_nombre = $candidatoData['SUPERVISOR_NOMBRE'] ?? $solicitudData['SOLICITADO_POR'];
                                
                                oci_free_statement($stmtCandidato);
                                
                                // ✅ SUBIR Y VALIDAR ARCHIVOS
                                $archivoPDF = subirArchivoAval($_FILES['pdf_reporte'], $id_solicitud, 'REPORTE_PDF');
                                $archivoCV = subirArchivoAval($_FILES['cv_candidato'], $id_solicitud, 'CV_CANDIDATO');
                                
                                // ✅ INSERTAR EN TABLA DE AVALES
                                $queryAval = "INSERT INTO ROY_AVALES_GERENCIA (
                                    ID_SOLICITUD,
                                    FECHA_ENVIO,
                                    ENVIADO_POR,
                                    COMENTARIO_RRHH,
                                    ARCHIVO_REPORTE_PDF,
                                    ARCHIVO_CV_CANDIDATO,
                                    CANDIDATO_NOMBRE,
                                    PUESTO_SOLICITADO,
                                    NUM_TIENDA,
                                    SUPERVISOR_NOMBRE,
                                    ESTADO_AVAL
                                ) VALUES (
                                    :id_solicitud,
                                    SYSDATE,
                                    :enviado_por,
                                    :comentario_rrhh,
                                    :archivo_pdf,
                                    :archivo_cv,
                                    :candidato_nombre,
                                    :puesto_solicitado,
                                    :num_tienda,
                                    :supervisor_nombre,
                                    'PENDIENTE'
                                )";
                                
                                $stmtAval = oci_parse($conn, $queryAval);
                                oci_bind_by_name($stmtAval, ':id_solicitud', $id_solicitud);
                                oci_bind_by_name($stmtAval, ':enviado_por', $enviado_por);
                                oci_bind_by_name($stmtAval, ':comentario_rrhh', $comentario_rrhh);
                                oci_bind_by_name($stmtAval, ':archivo_pdf', $archivoPDF['ruta_relativa']);
                                oci_bind_by_name($stmtAval, ':archivo_cv', $archivoCV['ruta_relativa']);
                                oci_bind_by_name($stmtAval, ':candidato_nombre', $candidato_nombre);
                                oci_bind_by_name($stmtAval, ':puesto_solicitado', $solicitudData['PUESTO_SOLICITADO']);
                                oci_bind_by_name($stmtAval, ':num_tienda', $solicitudData['NUM_TIENDA']);
                                oci_bind_by_name($stmtAval, ':supervisor_nombre', $supervisor_nombre);
                                
                                if (!oci_execute($stmtAval)) {
                                    $error = oci_error($stmtAval);
                                    throw new Exception('Error insertando aval: ' . $error['message']);
                                }
                                
                                oci_free_statement($stmtAval);
                                
                                // ✅ REGISTRAR EN HISTORIAL (VERSIÓN SIMPLE)
                                $comentarioHistorial = "Aval enviado para aprobación del gerente";
                                
                                $queryHistorial = "INSERT INTO ROY_HISTORICO_SOLICITUD (
                                    ID_SOLICITUD,
                                    ESTADO_ANTERIOR,
                                    ESTADO_NUEVO,
                                    COMENTARIO_NUEVO,
                                    FECHA_CAMBIO,
                                    AVAL_ENVIADO,
                                    TIPO_EVENTO
                                ) VALUES (
                                    :id_solicitud,
                                    :estado_anterior,
                                    :estado_nuevo,
                                    :comentario,
                                    SYSDATE,
                                    'Y',
                                    'AVAL ENVIADO A GERENCIA'
                                )";
                                
                                $stmtHistorial = oci_parse($conn, $queryHistorial);
                                oci_bind_by_name($stmtHistorial, ':id_solicitud', $id_solicitud);
                                oci_bind_by_name($stmtHistorial, ':estado_anterior', $estadoAnterior);
                                oci_bind_by_name($stmtHistorial, ':estado_nuevo', $estadoNuevo);
                                oci_bind_by_name($stmtHistorial, ':comentario', $comentarioHistorial);
                                
                                if (!oci_execute($stmtHistorial)) {
                                    $error = oci_error($stmtHistorial);
                                    throw new Exception('Error registrando en historial: ' . $error['message']);
                                }
                                
                                // 🔧 CORRECCIÓN: Obtener ID del historial recién insertado de forma más confiable
                                $queryGetId = "SELECT 
                                                (SELECT MAX(ID_HISTORICO) FROM ROY_HISTORICO_SOLICITUD 
                                                WHERE ID_SOLICITUD = :id_solicitud 
                                                AND AVAL_ENVIADO = 'Y' 
                                                AND TIPO_EVENTO = 'AVAL ENVIADO A GERENCIA') AS ID_HISTORICO
                                            FROM DUAL";
                                
                                $stmtGetId = oci_parse($conn, $queryGetId);
                                oci_bind_by_name($stmtGetId, ':id_solicitud', $id_solicitud);
                                oci_execute($stmtGetId);
                                $resultId = oci_fetch_assoc($stmtGetId);
                                $idHistorico = $resultId['ID_HISTORICO'] ?? null;
                                
                                oci_free_statement($stmtHistorial);
                                oci_free_statement($stmtGetId);
                                
                                // ✅ ACTUALIZAR REFERENCIA EN TABLA DE AVALES
                                if ($idHistorico) {
                                    $queryUpdateAval = "UPDATE ROY_AVALES_GERENCIA 
                                                    SET ID_HISTORICO_ENVIO = :id_historico 
                                                    WHERE ID_SOLICITUD = :id_solicitud 
                                                    AND ESTADO_AVAL = 'PENDIENTE'
                                                    AND ACTIVO = 'Y'";
                                    
                                    $stmtUpdateAval = oci_parse($conn, $queryUpdateAval);
                                    oci_bind_by_name($stmtUpdateAval, ':id_historico', $idHistorico);
                                    oci_bind_by_name($stmtUpdateAval, ':id_solicitud', $id_solicitud);
                                    oci_execute($stmtUpdateAval);
                                    oci_free_statement($stmtUpdateAval);
                                }
                                
                                // ✅ ACTUALIZAR ESTADO DE LA SOLICITUD
                                $queryUpdate = "UPDATE ROY_SOLICITUD_PERSONAL 
                                            SET ESTADO_SOLICITUD = :estado_nuevo,
                                                FECHA_MODIFICACION = SYSDATE
                                            WHERE ID_SOLICITUD = :id_solicitud";
                                
                                $stmtUpdate = oci_parse($conn, $queryUpdate);
                                oci_bind_by_name($stmtUpdate, ':estado_nuevo', $estadoNuevo);
                                oci_bind_by_name($stmtUpdate, ':id_solicitud', $id_solicitud);
                                
                                if (!oci_execute($stmtUpdate)) {
                                    $error = oci_error($stmtUpdate);
                                    throw new Exception('Error actualizando estado: ' . $error['message']);
                                }
                                
                                oci_free_statement($stmtUpdate);
                                
                                // Confirmar transacción
                                oci_commit($conn);
                                
                                // ✅ RESPUESTA EXITOSA
                                echo json_encode([
                                    'success' => true,
                                    'message' => 'Solicitud enviada a aval gerencia correctamente',
                                    'data' => [
                                        'id_solicitud' => $id_solicitud,
                                        'estado_anterior' => $estadoAnterior,
                                        'estado_nuevo' => $estadoNuevo,
                                        'candidato' => $candidato_nombre,
                                        'archivos' => [
                                            'pdf_reporte' => $archivoPDF['nombre_archivo'],
                                            'cv_candidato' => $archivoCV['nombre_archivo']
                                        ],
                                        'fecha_envio' => date('Y-m-d H:i:s'),
                                        'enviado_por' => $enviado_por
                                    ]
                                ]);
                                
                            } catch (Exception $e) {
                                oci_rollback($conn);
                                error_log('❌ Error enviando aval gerencia: ' . $e->getMessage());
                                
                                echo json_encode([
                                    'success' => false,
                                    'error' => $e->getMessage()
                                ]);
                            }
                            break;

                            // CASE PARA OBTENER RESULTADO DEL AVAL
                            case 'obtener_resultado_aval_recursos':
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


                                    case 'obtener_resumen_procesar_gerente':
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

case 'obtener_resumen_rrhh':
    // Limpiar cualquier output buffer antes de enviar JSON
    if (ob_get_level()) {
        ob_clean();
    }
    
    $id_solicitud = $_GET['id_solicitud'] ?? $_POST['id_solicitud'];
    
    try {
        // Consulta sin validación de sesión para RRHH
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
            }
            
            // OBTENER NOMBRE COMPLETO DEL GERENTE
            $nombre_gerente = 'No disponible';
            if (!empty($row['GERENTE'])) {
                $nombre_gerente = $row['GERENTE'];
            } elseif (!empty($row['CODIGO_GERENTE'])) {
                $gerente_nombres = [
                    '5333' => 'Christian Quan', 
                    '5210' => 'Giovanni Cardoza'
                ];
                $nombre_gerente = $gerente_nombres[$row['CODIGO_GERENTE']] ?? 'Gerente código ' . $row['CODIGO_GERENTE'];
            }
            
            // Extraer solo el comentario limpio - USAR LA MISMA LÓGICA QUE FUNCIONA EN GERENTES
            $comentario_limpio = 'Sin comentario adicional';
            if ($comentario_completo) {
                // Debug para ver qué contiene
                error_log("COMENTARIO COMPLETO DEBUG: " . $comentario_completo);
                
                // MÉTODO DIRECTO: buscar y extraer solo después de los dos puntos
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
                
                // ÚLTIMA LIMPIEZA: quitar caracteres extraños y fechas
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
            
            ob_clean(); // Limpiar cualquier output previo
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
            exit; // Evitar que se ejecute código adicional
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'error' => 'Solicitud no encontrada']);
            exit;
        }
        
        oci_free_statement($stmt);
        
    } catch (Exception $e) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
    
    oci_close($conn);
    break;


    }
}
?>
