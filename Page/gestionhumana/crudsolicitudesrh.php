<?php
// ===== CONFIGURACIÓN ÚNICA Y LIMPIA =====
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '50M');
ini_set('max_file_uploads', 30);
ini_set('max_execution_time', 400);
ini_set('max_input_time', 400);
ini_set('memory_limit', '256M');

// Solo para depuración - DESACTIVAR EN PRODUCCIÓN
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Header JSON único
header('Content-Type: application/json; charset=utf-8');

// Debug logging
error_log("=== NUEVA PETICIÓN ===");
error_log("GET: " . print_r($_GET, true));
error_log("POST: " . print_r($_POST, true));
error_log("FILES: " . print_r($_FILES, true));

include_once '../../Funsiones/conexion.php';

$conn = Oracle();
if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

// ===== FUNCIONES AUXILIARES =====
function detectarErrorArchivo($error_code) {
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

function enviarJSON($data) {
    echo json_encode($data);
    exit;
}


function formatearTamaño($bytes) {
if ($bytes == 0) return '0 B';
                                
$k = 1024;
$sizes = ['B', 'KB', 'MB', 'GB'];
$i = floor(log($bytes) / log($k));
return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

// ==================================================================================
// FUNCIÓN AUXILIAR: CALCULAR TIEMPO TRANSCURRIDO
// ==================================================================================
function calcularTiempo($fechaInicio, $fechaFin = null) {
    try {
        $fin = $fechaFin ? DateTime::createFromFormat('d-m-Y H:i:s', $fechaFin) : new DateTime();
        $inicio = DateTime::createFromFormat('d-m-Y H:i:s', $fechaInicio);
        
        if (!$inicio) {
            $inicio = DateTime::createFromFormat('Y-m-d H:i:s', $fechaInicio);
        }
        
        if (!$inicio) return 'Fecha inválida';
        
        $diff = $fin->diff($inicio);
        
        $dias = $diff->days;
        $horas = $diff->h;
        $minutos = $diff->i;
        
        $partes = [];
        if ($dias > 0) $partes[] = $dias . ' día' . ($dias > 1 ? 's' : '');
        if ($horas > 0) $partes[] = $horas . ' hora' . ($horas > 1 ? 's' : '');
        if ($minutos > 0) $partes[] = $minutos . ' minuto' . ($minutos > 1 ? 's' : '');
        
        return !empty($partes) ? implode(', ', $partes) : '0 minutos';
        
    } catch (Exception $e) {
        return 'Error';
    }
}


// ===== DETERMINAR ACCIÓN =====
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if (!$action) {
    enviarJSON(['success' => false, 'error' => 'Acción no especificada']);
}

// ===== SWITCH PRINCIPAL =====
//=========================================================================================
// INICIALIZACION
//==========================================================================================
switch ($action) {

//=========================================================================================
// INICIO ACCIONES
//==========================================================================================   
                            case 'listar_solicitudes_rh':
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
                                
                                -- COMENTARIO COMBINADO: del historial general O del último cambio de candidatos
                                COALESCE(
                                    s.COMENTARIO_SOLICITUD,
                                    (SELECT h.COMENTARIO_NUEVO 
                                    FROM ROY_HISTORICO_SOLICITUD h 
                                    WHERE h.ID_SOLICITUD = s.ID_SOLICITUD 
                                    AND h.COMENTARIO_NUEVO IS NOT NULL
                                    ORDER BY h.FECHA_CAMBIO DESC 
                                    FETCH FIRST 1 ROWS ONLY)
                                ) AS COMENTARIO_SOLICITUD,

                                -- ID_HISTORICO del último comentario (general o candidatos)
                                (SELECT h.ID_HISTORICO
                                FROM ROY_HISTORICO_SOLICITUD h
                                WHERE h.ID_SOLICITUD = s.ID_SOLICITUD
                                AND (h.COMENTARIO_NUEVO IS NOT NULL OR EXISTS (
                                    SELECT 1 FROM ROY_CHAT_HISTORICO c WHERE c.ID_HISTORICO = h.ID_HISTORICO
                                ))
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
                                ) AS TIENE_OBSERVACIONES_DIA_PRUEBA,

                                --  AGREGAR ESTE CAMPO QUE FALTABA
                                (
                                    SELECT COUNT(*)
                                    FROM ROY_CANDIDATOS_SOLICITUD cs
                                    WHERE cs.ID_SOLICITUD = s.ID_SOLICITUD
                                    AND cs.ACTIVO = 'Y'
                                ) AS TOTAL_CANDIDATOS

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
                            'DIRIGIDO_RH' => $row['DIRIGIDO_RH'],
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
                            'NO_LEIDOS' => intval($row['NO_LEIDOS']),
                            'TOTAL_CANDIDATOS' => intval($row['TOTAL_CANDIDATOS'])
                        ];
                            }

                            oci_free_statement($stmt);
                            oci_close($conn);

                            error_log(" Solicitudes APROBADAS obtenidas para RRHH: " . count($solicitudes));
                            echo json_encode($solicitudes);
                            break;
//=========================================================================================
// CAMBIO DE ESTADO
//==========================================================================================

                        //CAMBIAR ESTADO - CORREGIDO PARA ACEPTAR PDFs
case 'toggle_solicitud_status':
    error_log("========== INICIO toggle_solicitud_status ==========");
    
    if (empty($_POST['id_solicitud']) || empty($_POST['nuevo_estado'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios']);
        break;
    }

    $id = trim($_POST['id_solicitud']);
    $nuevo_estado = trim($_POST['nuevo_estado']);
    $comentario_nuevo = trim($_POST['comentario'] ?? '');

    if (empty($comentario_nuevo)) {
        echo json_encode(['success' => false, 'error' => 'El comentario es obligatorio']);
        break;
    }

    // Normalizar estado
    $estadosPermitidos = ['Pendiente', 'Vacante Activa', 'Candidatos en Seleccion'];
    $nuevo_estado = ucwords(strtolower($nuevo_estado));
    
    if ($nuevo_estado === 'Candidatos En Seleccion') {
        $nuevo_estado = 'Candidatos en Seleccion';
    }

    if (!in_array($nuevo_estado, $estadosPermitidos, true)) {
        echo json_encode(['success' => false, 'error' => "Estado no válido: $nuevo_estado"]);
        break;
    }

    try {
        // 1. Obtener estado anterior
        $sql = "SELECT ESTADO_SOLICITUD FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id', $id);
        
        if (!oci_execute($stmt)) {
            throw new Exception("Error al consultar estado anterior");
        }
        
        $row = oci_fetch_assoc($stmt);
        if (!$row) {
            throw new Exception("Solicitud no encontrada con ID: $id");
        }
        
        $estado_anterior = $row['ESTADO_SOLICITUD'];
        oci_free_statement($stmt);

        // 2. UPDATE
        $sql_update = "UPDATE ROY_SOLICITUD_PERSONAL 
                      SET ESTADO_SOLICITUD = :estado,
                          COMENTARIO_SOLICITUD = :comentario,
                          FECHA_MODIFICACION = SYSDATE
                      WHERE ID_SOLICITUD = :id";
        
        $stmt_update = oci_parse($conn, $sql_update);
        oci_bind_by_name($stmt_update, ':estado', $nuevo_estado);
        oci_bind_by_name($stmt_update, ':comentario', $comentario_nuevo);
        oci_bind_by_name($stmt_update, ':id', $id);
        
        if (!oci_execute($stmt_update, OCI_NO_AUTO_COMMIT)) {
            $error = oci_error($stmt_update);
            throw new Exception("Error en UPDATE: " . $error['message']);
        }
        
        oci_free_statement($stmt_update);

        // 3. Insertar en ROY_HISTORICO_SOLICITUD
        $sql_hist = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                    (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, COMENTARIO_NUEVO, FECHA_CAMBIO, TIPO_EVENTO)
                    VALUES (:id, :ant, :nuevo, :com, SYSDATE, 'CAMBIO_ESTADO_SOLICITUD')";
        
        $stmt_hist = oci_parse($conn, $sql_hist);
        oci_bind_by_name($stmt_hist, ':id', $id);
        oci_bind_by_name($stmt_hist, ':ant', $estado_anterior);
        oci_bind_by_name($stmt_hist, ':nuevo', $nuevo_estado);
        oci_bind_by_name($stmt_hist, ':com', $comentario_nuevo);
        
        if (!oci_execute($stmt_hist, OCI_NO_AUTO_COMMIT)) {
            throw new Exception("Error insertando historial");
        }
        
        $id_historico = null;
        $sql_get_hist = "SELECT ID_HISTORICO FROM ROY_HISTORICO_SOLICITUD 
                        WHERE ID_SOLICITUD = :id 
                        ORDER BY FECHA_CAMBIO DESC 
                        FETCH FIRST 1 ROWS ONLY";
        
        $stmt_get_hist = oci_parse($conn, $sql_get_hist);
        oci_bind_by_name($stmt_get_hist, ':id', $id);
        oci_execute($stmt_get_hist);
        $hist_row = oci_fetch_assoc($stmt_get_hist);
        if ($hist_row) {
            $id_historico = $hist_row['ID_HISTORICO'];
        }
        oci_free_statement($stmt_get_hist);
        oci_free_statement($stmt_hist);

        // 4. ✅ AGREGAR COMENTARIO AL CHAT (ROY_CHAT_HISTORICO)
        if ($id_historico) {
            $sql_chat = "INSERT INTO ROY_CHAT_HISTORICO 
                        (ID_MENSAJE, ID_HISTORICO, ROL, MENSAJE, FECHA, REMITENTE, ES_LEIDO)
                        VALUES (SEQ_CHAT_MENSAJE.NEXTVAL, :id_hist, 'RRHH', EMPTY_CLOB(), SYSDATE, 'RRHH_SISTEMA', 'N')
                        RETURNING MENSAJE INTO :mensaje_clob";
            
            $stmt_chat = oci_parse($conn, $sql_chat);
            oci_bind_by_name($stmt_chat, ':id_hist', $id_historico);
            
            $clob = oci_new_descriptor($conn, OCI_D_LOB);
            oci_bind_by_name($stmt_chat, ':mensaje_clob', $clob, -1, OCI_B_CLOB);
            
            if (oci_execute($stmt_chat, OCI_NO_AUTO_COMMIT)) {
                // Guardar el comentario en el CLOB
                $mensaje_chat = "Estado cambiado a: $nuevo_estado\n\nComentario: $comentario_nuevo";
                $clob->save($mensaje_chat);
                $clob->free();
            }
            
            oci_free_statement($stmt_chat);
            error_log("✅ Comentario guardado en chat");
        }

        // 5. COMMIT
        if (!oci_commit($conn)) {
            $error = oci_error($conn);
            throw new Exception("COMMIT FALLÓ: " . $error['message']);
        }
        
        error_log("✅ COMMIT EXITOSO");

        echo json_encode([
            'success' => true,
            'mensaje' => 'Estado actualizado correctamente',
            'estado_anterior' => $estado_anterior,
            'estado_nuevo' => $nuevo_estado,
            'chat_guardado' => ($id_historico ? true : false)
        ]);

    } catch (Exception $e) {
        error_log("❌ ERROR: " . $e->getMessage());
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;
//=========================================================================================
// FILTROS
//==========================================================================================

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
    
//=========================================================================================
// LECTURA Y SUBIDA DE ARCHVIVOS DE CANDIDATOS
//==========================================================================================
    //LECTURA DE ARCHIVOS DE LOS CANDIDATOS
case 'get_archivos_candidato':
    header('Content-Type: application/json; charset=utf-8');
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        $estado_filtro = $_GET['estado_filtro'] ?? null;
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato requerido');
        }
        
        $whereClause = "a.ID_CANDIDATO = :id_candidato";
        if ($estado_filtro) {
            $whereClause .= " AND a.ESTADO_RELACIONADO = :estado_filtro";
        }
        
        $query = "SELECT 
                    a.ID_ARCHIVO,
                    a.NOMBRE_ARCHIVO,
                    a.TIPO_ARCHIVO,
                    a.ESTADO_RELACIONADO,
                    a.SUBIDO_POR_ROL,
                    TO_CHAR(a.FECHA_SUBIDA, 'DD-MM-YYYY HH24:MI:SS') as FECHA_SUBIDA,
                    c.NOMBRE_CANDIDATO,
                    c.APELLIDOS_CANDIDATO
                FROM ROY_ARCHIVOS_SOLICITUD a
                JOIN ROY_CANDIDATOS_SOLICITUD c ON a.ID_CANDIDATO = c.ID_CANDIDATO
                WHERE " . $whereClause . "
                ORDER BY a.FECHA_SUBIDA DESC";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
        
        if ($estado_filtro) {
            oci_bind_by_name($stmt, ':estado_filtro', $estado_filtro);
        }
        
        oci_execute($stmt);
        
        $archivos = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $nombreSolo = basename($row['NOMBRE_ARCHIVO']);
            $rutaCompleta = __DIR__ . "/archivos_candidatos/" . $nombreSolo;
            $archivoExiste = file_exists($rutaCompleta);
            $tamaño = $archivoExiste ? filesize($rutaCompleta) : 0;
            
            $archivos[] = [
                'ID_ARCHIVO' => $row['ID_ARCHIVO'],
                'NOMBRE_ARCHIVO' => $row['NOMBRE_ARCHIVO'],
                'NOMBRE_SOLO' => $nombreSolo,
                'TIPO_ARCHIVO' => $row['TIPO_ARCHIVO'],
                'ESTADO_RELACIONADO' => $row['ESTADO_RELACIONADO'],
                'SUBIDO_POR_ROL' => $row['SUBIDO_POR_ROL'],
                'FECHA_SUBIDA' => $row['FECHA_SUBIDA'],
                'NOMBRE_CANDIDATO' => $row['NOMBRE_CANDIDATO'],
                'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                'TAMAÑO_BYTES' => $tamaño,
                'TAMAÑO_FORMATTED' => formatearTamaño($tamaño), // Ya tienes esta función
                'EXISTE' => $archivoExiste,
                // NUEVAS URLS PARA VER Y DESCARGAR
                'URL_VER' => $archivoExiste ? './gestionhumana/crudsolicitudesrh.php?action=ver_archivo&archivo=' . urlencode($nombreSolo) : null,
                'URL_DESCARGA' => $archivoExiste ? './gestionhumana/crudsolicitudesrh.php?action=descargar_archivo&archivo=' . urlencode($nombreSolo) : null
            ];
        }
        
        oci_free_statement($stmt);
        
        echo json_encode([
            'success' => true,
            'archivos' => $archivos,
            'total' => count($archivos)
        ]);
        exit;
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
    break;

        //SUBIDA DE ARCHIVOS DE LOS CANDIDATOS
    case 'subir_archivo_candidato':
        try {
            error_log("=== INICIO SUBIDA ARCHIVO ===");
            
            // 1. VALIDAR DATOS OBLIGATORIOS
            if (!isset($_FILES['archivo'])) {
                enviarJSON(['success' => false, 'error' => 'No se recibió el archivo']);
            }
            
            if (!isset($_POST['id_candidato']) || !isset($_POST['id_solicitud'])) {
                enviarJSON(['success' => false, 'error' => 'Faltan datos obligatorios (id_candidato, id_solicitud)']);
            }

            $id_candidato = intval($_POST['id_candidato']);
            $id_solicitud = intval($_POST['id_solicitud']);
            $archivo = $_FILES['archivo'];
            $tipo_archivo = $_POST['tipo_archivo'] ?? 'CV Enviado';

            error_log("ID Candidato: $id_candidato, ID Solicitud: $id_solicitud");
            error_log("Archivo recibido: " . print_r($archivo, true));

            // 2. VALIDAR ERRORES DE SUBIDA
            $errorArchivo = detectarErrorArchivo($archivo['error']);
            if ($errorArchivo) {
                enviarJSON(['success' => false, 'error' => $errorArchivo]);
            }

            // 3. VALIDAR TAMAÑO Y TIPO
            if ($archivo['size'] > 10 * 1024 * 1024) { // 10MB
                enviarJSON(['success' => false, 'error' => 'Archivo muy grande (máximo 10MB)']);
            }

            $extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
            
            if (!in_array($extension, $extensionesPermitidas)) {
                enviarJSON(['success' => false, 'error' => 'Tipo de archivo no permitido. Solo: ' . implode(', ', $extensionesPermitidas)]);
            }

            // 4. CREAR DIRECTORIO SI NO EXISTE
            $rutaDestino = __DIR__ . "/archivos_candidatos/";
            if (!is_dir($rutaDestino)) {
                if (!mkdir($rutaDestino, 0777, true)) {
                    enviarJSON(['success' => false, 'error' => 'No se pudo crear el directorio de destino']);
                }
            }

            // 5. GENERAR NOMBRE ÚNICO
            // Generar nombre con nueva nomenclatura: ESTADO_SOLICITUD_CANDIDATO_FECHA.extension
            $fechaActual = date('Y-m-d');
            $nombreArchivo = $tipo_archivo . "_{$id_solicitud}_{$id_candidato}_{$fechaActual}." . $extension;
            $rutaFinal = $rutaDestino . $nombreArchivo;

            error_log("Ruta final: $rutaFinal");

            // 6. MOVER ARCHIVO
            if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
                $error = error_get_last();
                error_log("Error moviendo archivo: " . print_r($error, true));
                enviarJSON(['success' => false, 'error' => 'Error al guardar el archivo en el servidor']);
            }

            // 7. INSERTAR EN BASE DE DATOS
            $query = "INSERT INTO ROY_ARCHIVOS_SOLICITUD 
                        (ID_SOLICITUD, NOMBRE_ARCHIVO, FECHA_SUBIDA, ID_CANDIDATO, ESTADO_RELACIONADO, SUBIDO_POR_ROL, TIPO_ARCHIVO) 
                    VALUES 
                        (:id_solicitud, :nombre, SYSDATE, :id_candidato, :estado, :rol, :tipo)";

            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':id_solicitud', $id_solicitud);
            oci_bind_by_name($stmt, ':nombre', $nombreArchivo);
            oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
            oci_bind_by_name($stmt, ':estado', $tipo_archivo);
            
            $rol_usuario = 'RRHH'; // Puedes obtener esto de la sesión
            oci_bind_by_name($stmt, ':rol', $rol_usuario);
            oci_bind_by_name($stmt, ':tipo', $tipo_archivo);
            
            if (!oci_execute($stmt)) {
                $error = oci_error($stmt);
                error_log("Error BD: " . print_r($error, true));
                
                // Eliminar archivo físico si falló la BD
                if (file_exists($rutaFinal)) {
                    unlink($rutaFinal);
                }
                
                oci_rollback($conn);
                enviarJSON(['success' => false, 'error' => 'Error guardando en base de datos: ' . $error['message']]);
            }

            oci_free_statement($stmt);
            oci_commit($conn);

            error_log("=== ARCHIVO SUBIDO EXITOSAMENTE ===");
            
            enviarJSON([
                'success' => true, 
                'mensaje' => 'Archivo subido correctamente',
                'archivo' => $nombreArchivo,
                'tamaño' => $archivo['size']
            ]);

        } catch (Exception $e) {
            error_log("Excepción en subida: " . $e->getMessage());
            if ($conn) oci_rollback($conn);
            enviarJSON(['success' => false, 'error' => 'Error interno: ' . $e->getMessage()]);
        }
        break;
//=========================================================================================
// PERMISOS DE SUBIDA DE ARCHIVOS
//==========================================================================================
        //PERMISOS DE SUBIDA SEGUN ROL Y ESTADO
 case 'get_permisos_subida_candidato_rh':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        $rol_usuario = $_GET['rol_usuario'] ?? 'RRHH';
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato requerido');
        }
        
        //  INCLUIR MOTIVO_DESCARTE Y ACTIVO EN LA CONSULTA
        $queryCandidato = "SELECT 
                            c.ESTADO_CANDIDATO, 
                            c.MOTIVO_DESCARTE,
                            c.ACTIVO,
                            c.DOCUMENTO_CANDIDATO,
                            c.FECHA_REGISTRO,
                            s.PUESTO_SOLICITADO, 
                            s.SOLICITADO_POR, 
                            s.DIRIGIDO_A
                          FROM ROY_CANDIDATOS_SOLICITUD c
                          JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                          WHERE c.ID_CANDIDATO = :id";
        
        $stmt = oci_parse($conn, $queryCandidato);
        oci_bind_by_name($stmt, ':id', $id_candidato);
        oci_execute($stmt);
        $dataCandidato = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        if (!$dataCandidato) {
            throw new Exception('Candidato no encontrado');
        }

        //  PROCESAR MOTIVO_DESCARTE SI ES CLOB
        $motivo_descarte = '';
        if ($dataCandidato['MOTIVO_DESCARTE']) {
            if (is_object($dataCandidato['MOTIVO_DESCARTE'])) {
                $motivo_descarte = $dataCandidato['MOTIVO_DESCARTE']->load();
            } else {
                $motivo_descarte = $dataCandidato['MOTIVO_DESCARTE'];
            }
        }

        //  CONSULTA PARA OBTENER QUIÉN DESCARTÓ AL CANDIDATO
        $queryQuienDescarto = "SELECT 
                                h.OBSERVACIONES,
                                h.FECHA_CAMBIO,
                                CASE 
                                    WHEN h.OBSERVACIONES LIKE '%GERENTE:%' THEN 
                                        TRIM(REGEXP_SUBSTR(h.OBSERVACIONES, 'GERENTE: ([^-]+)', 1, 1, NULL, 1))
                                    WHEN h.OBSERVACIONES LIKE '%SUPERVISOR:%' THEN 
                                        TRIM(REGEXP_SUBSTR(h.OBSERVACIONES, 'SUPERVISOR: ([^-]+)', 1, 1, NULL, 1))
                                    WHEN h.OBSERVACIONES LIKE '%RRHH:%' THEN 
                                        TRIM(REGEXP_SUBSTR(h.OBSERVACIONES, 'RRHH: ([^-]+)', 1, 1, NULL, 1))
                                    WHEN h.OBSERVACIONES LIKE '%GERENTE%' THEN 'Gerente (sin nombre)'
                                    WHEN h.OBSERVACIONES LIKE '%SUPERVISOR%' THEN 'Supervisor (sin nombre)'
                                    WHEN h.OBSERVACIONES LIKE '%RRHH%' THEN 'RH (sin nombre)'
                                    ELSE 'Usuario no identificado'
                                END as NOMBRE_QUIEN_DESCARTO,
                                CASE 
                                    WHEN h.OBSERVACIONES LIKE '%GERENTE%' THEN 'GERENTE'
                                    WHEN h.OBSERVACIONES LIKE '%SUPERVISOR%' THEN 'SUPERVISOR'
                                    WHEN h.OBSERVACIONES LIKE '%RRHH%' THEN 'RRHH'
                                    ELSE 'DESCONOCIDO'
                                END as TIPO_USUARIO_DESCARTO
                            FROM ROY_CANDIDATOS_HIST_EST h 
                            WHERE h.ID_CANDIDATO = :id_candidato 
                            AND h.ESTADO_NUEVO = 'Descartado' 
                            AND h.ACTIVO = 'Y'
                            ORDER BY h.FECHA_CAMBIO DESC 
                            FETCH FIRST 1 ROWS ONLY";

        $stmtDescarte = oci_parse($conn, $queryQuienDescarto);
        oci_bind_by_name($stmtDescarte, ':id_candidato', $id_candidato);
        oci_execute($stmtDescarte);
        $infoDescarte = oci_fetch_assoc($stmtDescarte);
        oci_free_statement($stmtDescarte);

        // Verificar si está descartado
        $esDescartado = ($dataCandidato['ACTIVO'] === 'N' || $dataCandidato['ESTADO_CANDIDATO'] === 'Descartado');

        //  DEFINIR CARPETAS DISPONIBLES PARA RH
        $carpetasDisponibles = [
            ['NOMBRE_ESTADO' => 'CV Enviado'],
            ['NOMBRE_ESTADO' => 'Psicometrica'],
            ['NOMBRE_ESTADO' => 'Entrevista RH'],
            ['NOMBRE_ESTADO' => 'Entrevista Tecnica'],
            ['NOMBRE_ESTADO' => 'Dia de Prueba'],
            ['NOMBRE_ESTADO' => 'Poligrafo'],
            ['NOMBRE_ESTADO' => 'Expediente RH']
        ];
        
        // Filtrar según permisos del rol
        $carpetasPermitidas = [];
        
        foreach ($carpetasDisponibles as $carpeta) {
            $nombreEstado = $carpeta['NOMBRE_ESTADO'];
            
            //  VERIFICAR SI YA SUBIÓ ARCHIVOS
            $queryArchivos = "SELECT COUNT(*) as TIENE_ARCHIVOS
                             FROM ROY_ARCHIVOS_SOLICITUD 
                             WHERE ID_CANDIDATO = :id_candidato 
                             AND ESTADO_RELACIONADO = :estado";
            
            $stmtArch = oci_parse($conn, $queryArchivos);
            oci_bind_by_name($stmtArch, ':id_candidato', $id_candidato);
            oci_bind_by_name($stmtArch, ':estado', $nombreEstado);
            oci_execute($stmtArch);
            $rowArch = oci_fetch_assoc($stmtArch);
            $yaSubioArchivos = ($rowArch['TIENE_ARCHIVOS'] > 0);
            oci_free_statement($stmtArch);
            
            $puedeSubir = false;
            $motivoBloqueo = null;
            
            // Si está descartado, no puede subir nada
            if ($esDescartado) {
                $puedeSubir = false;
                $motivoBloqueo = 'Candidato descartado';
            } else {
                //  PERMISOS PARA RH
                switch ($rol_usuario) {
                    case 'RRHH':
                        // RH puede subir archivos en todos los estados
                        $estadosRRHH = ['CV Enviado', 'Psicometrica', 'Entrevista RH', 'Poligrafo', 'Expediente RH'];
                        
                        if (in_array($nombreEstado, $estadosRRHH)) {
                            $puedeSubir = !$yaSubioArchivos;
                            $motivoBloqueo = $yaSubioArchivos ? 'Ya se subieron archivos para este estado' : null;
                        } else {
                            // Para estados que no puede subir RH (Entrevista Técnica, Día de Prueba)
                            $puedeSubir = false;
                            if (!$yaSubioArchivos) {
                                $motivoBloqueo = "Este estado es manejado por supervisión";
                            } else {
                                $motivoBloqueo = 'Solo visualización disponible';
                            }
                        }
                        break;
                        
                    default:
                        $puedeSubir = false;
                        $motivoBloqueo = 'Sin permisos para este estado';
                }
            }
            
            $carpetasPermitidas[] = [
                'nombre_estado' => $nombreEstado,
                'puede_subir' => $puedeSubir,
                'puede_ver' => true, // RH SIEMPRE PUEDE VER
                'ya_tiene_archivos' => $yaSubioArchivos,
                'motivo_bloqueo' => $motivoBloqueo
            ];
        }
        
        //  RESPUESTA COMPLETA PARA RH
        echo json_encode([
            'success' => true,
            'rol_usuario' => 'RRHH',
            'estado_candidato' => $dataCandidato['ESTADO_CANDIDATO'],
            'puesto_solicitado' => $dataCandidato['PUESTO_SOLICITADO'],
            'motivo_descarte' => $motivo_descarte,
            'info_descarte' => $infoDescarte ?: [], //  INFORMACIÓN DE QUIÉN DESCARTÓ
            'carpetas' => $carpetasPermitidas,
            'candidato' => [
                'ACTIVO' => $dataCandidato['ACTIVO'],
                'DOCUMENTO_CANDIDATO' => $dataCandidato['DOCUMENTO_CANDIDATO'] ?? 'No registrado',
                'FECHA_REGISTRO' => $dataCandidato['FECHA_REGISTRO'] ?? 'No disponible'
            ],
            'es_descartado' => $esDescartado,
            'solicitado_por' => $dataCandidato['SOLICITADO_POR'],
            'dirigido_a' => $dataCandidato['DIRIGIDO_A']
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ================================
// 2. AGREGAR CASE PARA OBTENER CANDIDATOS CON FILTROS
// ================================
case 'get_candidatos_solicitud_rh':
    try {
        $id_solicitud = $_GET['id_solicitud'] ?? null;
        $filtro_estado = $_GET['filtro_estado'] ?? 'todos';
        
        if (!$id_solicitud) {
            throw new Exception('ID de solicitud requerido');
        }

        // 1. OBTENER INFO BÁSICA DE LA SOLICITUD
        $query_sol = "SELECT REACTIVADA, ESTADO_SOLICITUD FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
        $stmt_sol = oci_parse($conn, $query_sol);
        oci_bind_by_name($stmt_sol, ':id', $id_solicitud);
        oci_execute($stmt_sol);
        $info_sol = oci_fetch_assoc($stmt_sol);
        oci_free_statement($stmt_sol);

        $es_reactivada = ($info_sol['REACTIVADA'] === 'Y');
        $es_plaza_cubierta = (strtolower(trim($info_sol['ESTADO_SOLICITUD'])) === 'plaza cubierta');

       // 2. CONSTRUIR WHERE CON LÓGICA CORRECTA
        $whereClause = "WHERE c.ID_SOLICITUD = :id_solicitud";
        
        if ($es_plaza_cubierta) {
            // CASO 1: Plaza cubierta - Solo mostrar contratado activo
            $whereClause .= " AND c.ESTADO_CANDIDATO = 'Contratado' AND c.ACTIVO = 'Y'";
        } elseif ($es_reactivada) {
            // ✅ CASO 2: Solicitud reactivada - Verificar estado de reactivación
            $query_estado_react = "SELECT ESTADO_REACT 
                                  FROM ROY_HIST_REACTIVACIONES 
                                  WHERE ID_SOLICITUD = :id_solicitud 
                                  ORDER BY NUM_REACTIVACION DESC 
                                  FETCH FIRST 1 ROW ONLY";
            
            $stmt_react = oci_parse($conn, $query_estado_react);
            oci_bind_by_name($stmt_react, ':id_solicitud', $id_solicitud);
            oci_execute($stmt_react);
            $react_info = oci_fetch_assoc($stmt_react);
            oci_free_statement($stmt_react);
            
            $estado_react = strtolower(trim($react_info['ESTADO_REACT'] ?? 'pendiente'));
            
            if ($estado_react === 'pendiente') {
                // PENDIENTE: Mostrar todos los candidatos disponibles para reactivar (descartados + no contratados)
                $whereClause .= " AND c.ESTADO_CANDIDATO != 'Contratado'";
            } else {
                // CONFIRMADA: Mostrar SOLO los candidatos que RH ya reactivó
                // Obtener fecha de la última reactivación
                $query_ultima_fecha = "SELECT MAX(FECHA_REACTIVACION) as ULTIMA_FECHA
                                      FROM ROY_HIST_REACTIVACIONES 
                                      WHERE ID_SOLICITUD = :id_solicitud";
                $stmt_fecha = oci_parse($conn, $query_ultima_fecha);
                oci_bind_by_name($stmt_fecha, ':id_solicitud', $id_solicitud);
                oci_execute($stmt_fecha);
                $fecha_result = oci_fetch_assoc($stmt_fecha);
                $ultima_fecha_reactivacion = $fecha_result['ULTIMA_FECHA'];
                oci_free_statement($stmt_fecha);
                
                if ($ultima_fecha_reactivacion) {
                    // Mostrar SOLO candidatos reactivados en la ÚLTIMA reactivación
                    $whereClause .= " AND c.REACTIVADO_POST_CONTRATACION = 'Y'
                                     AND TRUNC(c.FECHA_REACTIVACION_CANDIDATO) >= TRUNC(TO_DATE(:ultima_fecha, 'DD/MM/YY HH24:MI:SS'))
                                     AND c.ESTADO_CANDIDATO != 'Contratado'";
                } else {
                    // No hay fecha, mostrar todos los reactivados
                    $whereClause .= " AND c.REACTIVADO_POST_CONTRATACION = 'Y'
                                     AND c.ESTADO_CANDIDATO != 'Contratado'";
                }
            }
        } else {
            // CASO 3: Proceso normal - Verificar si hay candidatos reactivados
            $query_check_reactivados = "SELECT COUNT(*) as TOTAL 
                                        FROM ROY_CANDIDATOS_SOLICITUD 
                                        WHERE ID_SOLICITUD = :id_solicitud 
                                        AND REACTIVADO_POST_CONTRATACION = 'Y'";
            $stmt_check = oci_parse($conn, $query_check_reactivados);
            oci_bind_by_name($stmt_check, ':id_solicitud', $id_solicitud);
            oci_execute($stmt_check);
            $result_check = oci_fetch_assoc($stmt_check);
            $hay_reactivados = ($result_check['TOTAL'] > 0);
            oci_free_statement($stmt_check);
            
            if ($hay_reactivados) {
                // Hay reactivados - Obtener fecha de la última reactivación
                $query_ultima_fecha = "SELECT MAX(FECHA_REACTIVACION) as ULTIMA_FECHA
                                      FROM ROY_HIST_REACTIVACIONES 
                                      WHERE ID_SOLICITUD = :id_solicitud";
                $stmt_fecha = oci_parse($conn, $query_ultima_fecha);
                oci_bind_by_name($stmt_fecha, ':id_solicitud', $id_solicitud);
                oci_execute($stmt_fecha);
                $fecha_result = oci_fetch_assoc($stmt_fecha);
                $ultima_fecha_reactivacion = $fecha_result['ULTIMA_FECHA'];
                oci_free_statement($stmt_fecha);
                
                if ($ultima_fecha_reactivacion) {
                    // Mostrar SOLO candidatos de la última reactivación
                    $whereClause .= " AND c.REACTIVADO_POST_CONTRATACION = 'Y'
                                     AND TRUNC(c.FECHA_REACTIVACION_CANDIDATO) >= TRUNC(TO_DATE(:ultima_fecha, 'DD/MM/YY HH24:MI:SS'))";
                } else {
                    $whereClause .= " AND c.REACTIVADO_POST_CONTRATACION = 'Y'";
                }
            } else {
                // No hay reactivados - Aplicar filtros normales
                if ($filtro_estado === 'activos') {
                    $whereClause .= " AND (c.ACTIVO = 'Y' AND c.ESTADO_CANDIDATO != 'Descartado')";
                } elseif ($filtro_estado === 'descartados') {
                    $whereClause .= " AND (c.ACTIVO = 'N' OR c.ESTADO_CANDIDATO = 'Descartado')";
                }
            }
        }
        $query = "SELECT 
                    c.ID_CANDIDATO,
                    c.NOMBRE_CANDIDATO,
                    c.APELLIDOS_CANDIDATO,
                    c.DOCUMENTO_CANDIDATO,
                    c.ESTADO_CANDIDATO,
                    c.ACTIVO,
                    c.APROBACION,
                    c.MOTIVO_DESCARTE,
                    c.FECHA_REGISTRO,
                    c.FECHA_MODIFICACION,
                    c.REACTIVADO_POST_CONTRATACION,  -- ← AGREGAR ESTA LÍNEA
                    s.PUESTO_SOLICITADO,
                    s.SOLICITADO_POR,
                    s.MOTIVO_REACTIVACION,
                    s.DIRIGIDO_A,
                    
                    (SELECT COUNT(*) 
                    FROM ROY_ARCHIVOS_SOLICITUD a 
                    WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS TOTAL_ARCHIVOS,
                    
                    (SELECT h.OBSERVACIONES 
                    FROM ROY_CANDIDATOS_HIST_EST h 
                    WHERE h.ID_CANDIDATO = c.ID_CANDIDATO 
                    AND h.ESTADO_NUEVO = 'Descartado' 
                    AND h.ACTIVO = 'Y'
                    ORDER BY h.FECHA_CAMBIO DESC 
                    FETCH FIRST 1 ROWS ONLY) as OBSERVACIONES_DESCARTE,
                    
                    (SELECT h.FECHA_CAMBIO 
                    FROM ROY_CANDIDATOS_HIST_EST h 
                    WHERE h.ID_CANDIDATO = c.ID_CANDIDATO 
                    AND h.ESTADO_NUEVO = 'Descartado' 
                    AND h.ACTIVO = 'Y'
                    ORDER BY h.FECHA_CAMBIO DESC 
                    FETCH FIRST 1 ROWS ONLY) as FECHA_DESCARTE
                    
                FROM ROY_CANDIDATOS_SOLICITUD c
                JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                $whereClause
                ORDER BY c.FECHA_REGISTRO DESC";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_solicitud', $id_solicitud);
        
        // ✅ Si hay fecha de última reactivación, hacer bind
        if (isset($ultima_fecha_reactivacion) && $ultima_fecha_reactivacion) {
            oci_bind_by_name($stmt, ':ultima_fecha', $ultima_fecha_reactivacion);
        }
        
        oci_execute($stmt);

        $candidatos = [];
        $motivo_reactivacion = '';
        $nombre_gerente = 'Gerente';
        
        while ($row = oci_fetch_assoc($stmt)) {
            $motivo_descarte = '';
            if ($row['MOTIVO_DESCARTE']) {
                if (is_object($row['MOTIVO_DESCARTE'])) {
                    $motivo_descarte = $row['MOTIVO_DESCARTE']->load();
                } else {
                    $motivo_descarte = $row['MOTIVO_DESCARTE'];
                }
            }

            // Capturar info de la solicitud del primer registro
            if (empty($motivo_reactivacion) && $row['MOTIVO_REACTIVACION']) {
                $motivo_reactivacion = is_object($row['MOTIVO_REACTIVACION']) ? 
                    $row['MOTIVO_REACTIVACION']->load() : $row['MOTIVO_REACTIVACION'];
            }

            $quien_descarto = 'No identificado';
            $tipo_usuario = 'DESCONOCIDO';
            
            if ($row['OBSERVACIONES_DESCARTE']) {
                $obs = $row['OBSERVACIONES_DESCARTE'];
                if (strpos($obs, 'GERENTE:') !== false) {
                    $quien_descarto = trim(preg_replace('/.*GERENTE:\s*([^-]+)\s*-.*/i', '$1', $obs));
                    $tipo_usuario = 'GERENTE';
                } elseif (strpos($obs, 'SUPERVISOR:') !== false) {
                    $quien_descarto = trim(preg_replace('/.*SUPERVISOR:\s*([^-]+)\s*-.*/i', '$1', $obs));
                    $tipo_usuario = 'SUPERVISOR';
                }
            }

            $candidatos[] = [
                'ID_CANDIDATO' => $row['ID_CANDIDATO'],
                'NOMBRE_COMPLETO' => trim($row['NOMBRE_CANDIDATO'] . ' ' . $row['APELLIDOS_CANDIDATO']),
                'NOMBRE_CANDIDATO' => $row['NOMBRE_CANDIDATO'],
                'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                'DOCUMENTO_CANDIDATO' => $row['DOCUMENTO_CANDIDATO'],
                'ESTADO_CANDIDATO' => $row['ESTADO_CANDIDATO'],
                'ACTIVO' => $row['ACTIVO'],
                'APROBACION' => $row['APROBACION'],
                'MOTIVO_DESCARTE' => $motivo_descarte,
                'FECHA_REGISTRO' => $row['FECHA_REGISTRO'],
                'FECHA_MODIFICACION' => $row['FECHA_MODIFICACION'],
                'REACTIVADO_POST_CONTRATACION' => $row['REACTIVADO_POST_CONTRATACION'] ?? 'N',  // ← AGREGAR
                'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
                'SUPERVISOR' => $row['SOLICITADO_POR'],
                'FECHA_DESCARTE' => $row['FECHA_DESCARTE'],
                'QUIEN_DESCARTO' => $quien_descarto,
                'TIPO_USUARIO_DESCARTO' => $tipo_usuario,
                'TOTAL_ARCHIVOS' => intval($row['TOTAL_ARCHIVOS'] ?? 0),
                'ES_DESCARTADO' => ($row['ACTIVO'] === 'N' || $row['ESTADO_CANDIDATO'] === 'Descartado')
            ];
        }
        oci_free_statement($stmt);

        // OBTENER INFO COMPLETA DE LA SOLICITUD
        $querySolicitudCompleta = "SELECT NUM_TIENDA, PUESTO_SOLICITADO, SOLICITADO_POR, ESTADO_SOLICITUD 
                                FROM ROY_SOLICITUD_PERSONAL 
                                WHERE ID_SOLICITUD = :id";
        $stmtSolCompleta = oci_parse($conn, $querySolicitudCompleta);
        oci_bind_by_name($stmtSolCompleta, ':id', $id_solicitud);
        oci_execute($stmtSolCompleta);
        $infoSolicitudCompleta = oci_fetch_assoc($stmtSolCompleta);
        oci_free_statement($stmtSolCompleta);

        $response = [
            'success' => true,
            'candidatos' => $candidatos,
            'total' => count($candidatos),
            'filtro_aplicado' => $filtro_estado,
            // SIEMPRE INCLUIR INFO DE SOLICITUD
            'solicitud' => [
                'id' => $id_solicitud,
                'tienda' => $infoSolicitudCompleta['NUM_TIENDA'] ?? 'No especificada',
                'puesto' => $infoSolicitudCompleta['PUESTO_SOLICITADO'] ?? 'No especificado',
                'supervisor' => $infoSolicitudCompleta['SOLICITADO_POR'] ?? 'No asignado',
                'estado' => $infoSolicitudCompleta['ESTADO_SOLICITUD'] ?? '',
                'reactivada' => $es_reactivada ? 'Y' : 'N',
                'motivo_reactivacion' => $motivo_reactivacion,
                'nombre_gerente' => $nombre_gerente
            ]
        ];

        echo json_encode($response);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;
//=========================================================================================
// CHAT EMERGENTE
//==========================================================================================

    //CHAT ENTRE RH Y SUPERVISION, JUNTO CON EL HISTORICO Y RESPUESTAS 
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

//=========================================================================================
// REGISTRO DE CANDIDATOS 
//==========================================================================================

                     //registrar candidatos iniciales 
case 'registrar_candidatos_iniciales':
    error_reporting(E_ERROR | E_PARSE); 
    try {
        error_log("=== INICIO REGISTRO CANDIDATOS SIN CV ===");
        
        $id_solicitud = $_POST['id_solicitud'] ?? null;
        $nuevo_estado = $_POST['nuevo_estado'] ?? null;
        $comentario = $_POST['comentario'] ?? '';
        $candidatos_json = $_POST['candidatos'] ?? null;

        // DECODIFICAR JSON
        if ($candidatos_json) {
            $candidatos = json_decode($candidatos_json, true);
        } else {
            $candidatos = null;
        }

        // VALIDACIÓN MEJORADA
        if (!$id_solicitud || !$candidatos || !is_array($candidatos)) {
            // Debug para ver qué está llegando
            error_log("DEBUG - id_solicitud: " . var_export($id_solicitud, true));
            error_log("DEBUG - candidatos_json: " . var_export($candidatos_json, true));
            error_log("DEBUG - candidatos decodificado: " . var_export($candidatos, true));
            throw new Exception('Datos incompletos para registrar candidatos');
        }
        
        // VALIDACIÓN COMENTARIO OBLIGATORIO
        if (empty(trim($comentario))) {
            throw new Exception('El comentario es obligatorio para registrar candidatos');
        }
        
        $id_solicitud = intval($id_solicitud);
        
        $candidatos_registrados = [];
        $errores = [];
        
        // OBTENER PUESTO DE LA SOLICITUD
        $queryPuesto = "SELECT PUESTO_SOLICITADO FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
        $stmtPuesto = oci_parse($conn, $queryPuesto);
        oci_bind_by_name($stmtPuesto, ':id', $id_solicitud);
        oci_execute($stmtPuesto);
        $rowPuesto = oci_fetch_assoc($stmtPuesto);
        $puesto_solicitado = isset($rowPuesto['PUESTO_SOLICITADO']) ? $rowPuesto['PUESTO_SOLICITADO'] : '';
        oci_free_statement($stmtPuesto);
        
        foreach ($candidatos as $index => $candidato) {
            try {
                if (empty($candidato['nombre']) || empty($candidato['apellidos'])) {
                    throw new Exception("Candidato " . ($index + 1) . ": Nombre y apellidos son obligatorios");
                }
                
                $nombre_candidato = $candidato['nombre'];
                $apellidos_candidato = $candidato['apellidos'];
                $documento_candidato = isset($candidato['documento']) ? $candidato['documento'] : '';
                
                // INSERTAR CANDIDATO SIN ARCHIVOS
                $queryInsert = "INSERT INTO ROY_CANDIDATOS_SOLICITUD 
                    (ID_SOLICITUD, NOMBRE_CANDIDATO, APELLIDOS_CANDIDATO, 
                     DOCUMENTO_CANDIDATO, PUESTO_SOLICITADO, ESTADO_CANDIDATO) 
                    VALUES (:id_solicitud, :nombre, :apellidos, :documento, :puesto, 'CV Enviado')
                    RETURNING ID_CANDIDATO INTO :id_candidato";
                
                $stmtInsert = oci_parse($conn, $queryInsert);
                $id_candidato = null;
                
                oci_bind_by_name($stmtInsert, ':id_solicitud', $id_solicitud);
                oci_bind_by_name($stmtInsert, ':nombre', $nombre_candidato);
                oci_bind_by_name($stmtInsert, ':apellidos', $apellidos_candidato);
                oci_bind_by_name($stmtInsert, ':documento', $documento_candidato);
                oci_bind_by_name($stmtInsert, ':puesto', $puesto_solicitado);
                oci_bind_by_name($stmtInsert, ':id_candidato', $id_candidato, -1, SQLT_INT);
                
                if (!oci_execute($stmtInsert)) {
                    $error = oci_error($stmtInsert);
                    throw new Exception("Error insertando candidato: " . $error['message']);
                }
                
                $candidatos_registrados[] = [
                    'id_candidato' => $id_candidato,
                    'nombre_completo' => $nombre_candidato . ' ' . $apellidos_candidato,
                    'cv_subido' => false // SIEMPRE FALSE AHORA
                ];
                
                oci_free_statement($stmtInsert);
                
            } catch (Exception $e) {
                $errores[] = "Candidato " . ($index + 1) . ": " . $e->getMessage();
            }
        }
        
        // SOLO CREAR HISTORIAL SI SE REGISTRARON CANDIDATOS
        if (count($candidatos_registrados) > 0) {
            // OBTENER ESTADO ANTERIOR
            $queryEstadoAnterior = "SELECT ESTADO_SOLICITUD FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
            $stmtEstadoAnterior = oci_parse($conn, $queryEstadoAnterior);
            oci_bind_by_name($stmtEstadoAnterior, ':id', $id_solicitud);
            oci_execute($stmtEstadoAnterior);
            $rowEstadoAnterior = oci_fetch_assoc($stmtEstadoAnterior);
            $estado_anterior = isset($rowEstadoAnterior['ESTADO_SOLICITUD']) ? $rowEstadoAnterior['ESTADO_SOLICITUD'] : 'Desconocido';
            oci_free_statement($stmtEstadoAnterior);

            oci_free_statement($stmtEstadoAnterior);
            
            // ACTUALIZAR EL ESTADO DE LA SOLICITUD
            $queryUpdateSolicitud = "UPDATE ROY_SOLICITUD_PERSONAL 
                                     SET ESTADO_SOLICITUD = :nuevo_estado,
                                         FECHA_MODIFICACION = SYSDATE
                                     WHERE ID_SOLICITUD = :id_solicitud";
            
            $stmtUpdateSolicitud = oci_parse($conn, $queryUpdateSolicitud);
            oci_bind_by_name($stmtUpdateSolicitud, ':nuevo_estado', $nuevo_estado);
            oci_bind_by_name($stmtUpdateSolicitud, ':id_solicitud', $id_solicitud);
            
            if (!oci_execute($stmtUpdateSolicitud)) {
                $error = oci_error($stmtUpdateSolicitud);
                throw new Exception("Error al actualizar estado de solicitud: " . $error['message']);
            }
            oci_free_statement($stmtUpdateSolicitud);
            
            // CREAR COMENTARIO PARA HISTORIAL
            $comentario_historial = "CANDIDATOS REGISTRADOS:\n" . $comentario;
            
            $queryHistorial = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, COMENTARIO_NUEVO, FECHA_CAMBIO, TIPO_EVENTO)
                VALUES (:id_solicitud, :estado_anterior, :estado_nuevo, :comentario, SYSDATE, 'CANDIDATOS_REGISTRADOS')";
                
            $stmtHistorial = oci_parse($conn, $queryHistorial);
            oci_bind_by_name($stmtHistorial, ':id_solicitud', $id_solicitud);
            oci_bind_by_name($stmtHistorial, ':estado_anterior', $estado_anterior);
            oci_bind_by_name($stmtHistorial, ':estado_nuevo', $nuevo_estado);
            oci_bind_by_name($stmtHistorial, ':comentario', $comentario_historial);
            oci_execute($stmtHistorial);

            // OBTENER EL ID_HISTORICO que se acaba de crear
            $queryGetId = "SELECT MAX(ID_HISTORICO) AS ID_HISTORICO 
                        FROM ROY_HISTORICO_SOLICITUD 
                        WHERE ID_SOLICITUD = :id_solicitud 
                        AND TIPO_EVENTO = 'CANDIDATOS_REGISTRADOS'";

            $stmtGetId = oci_parse($conn, $queryGetId);
            oci_bind_by_name($stmtGetId, ':id_solicitud', $id_solicitud);
            oci_execute($stmtGetId);
            $resultId = oci_fetch_assoc($stmtGetId);
            $id_historico_creado = $resultId['ID_HISTORICO'];
            oci_free_statement($stmtGetId);

            // INSERTAR EN ROY_CHAT_HISTORICO para que aparezca en el chat
            if ($id_historico_creado) {
                $queryChat = "INSERT INTO ROY_CHAT_HISTORICO (
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
                                EMPTY_CLOB(),
                                SYSDATE,
                                'RRHH_SISTEMA',
                                'N'
                            ) RETURNING MENSAJE INTO :mensaje_clob";

                $stmtChat = oci_parse($conn, $queryChat);
                oci_bind_by_name($stmtChat, ':id_historico', $id_historico_creado);
                
                $clob = oci_new_descriptor($conn, OCI_D_LOB);
                oci_bind_by_name($stmtChat, ':mensaje_clob', $clob, -1, OCI_B_CLOB);

                if (oci_execute($stmtChat, OCI_DEFAULT)) {
                    $clob->save($comentario); // EL COMENTARIO ORIGINAL DEL USUARIO
                }
                
                $clob->free();
                oci_free_statement($stmtChat);
            }

            oci_free_statement($stmtHistorial);
        }
        
        oci_commit($conn);
        
        // RESPUESTA MODIFICADA - NO CERRAR MODAL
        echo json_encode([
            'success' => true,
            'candidatos_registrados' => count($candidatos_registrados),
            'candidatos_agregados' => count($ok),
            'archivos_subidos' => 0,
            'candidatos' => $ok,
            'errores' => $errs,
            'historial_creado' => true,
            'mensaje' => 'Candidatos registrados exitosamente. Los CVs se pueden subir posteriormente mediante el sistema de carpetas.',
            'mantener_modal_abierto' => true,
            'candidatos_listos' => true,
            'comentario_original' => $comentario,
            'nuevo_estado' => $nuevo_estado
        ]);
        
    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;


    
// ========================================================================================
//  AGREGAR CANDIDATOS ADICIONALES
// ========================================================================================


//=========================================================================================
// AGREGAR CANDIDATOS A SOLICITUD EXISTENTE
//=========================================================================================
//=========================================================================================
// AGREGAR CANDIDATOS A SOLICITUD EXISTENTE (CORREGIDO)
//=========================================================================================
case 'agregar_candidatos_solicitud':
    try {
        $id_solicitud = $_POST['id_solicitud'] ?? null;
        $candidatos_json = $_POST['candidatos'] ?? null;
        
        if (!$id_solicitud) {
            throw new Exception('ID de solicitud no proporcionado');
        }
        
        if (!$candidatos_json) {
            throw new Exception('Datos de candidatos no proporcionados');
        }
        
        // Decodificar JSON de candidatos
        $candidatos = json_decode($candidatos_json, true);
        if (!$candidatos || !is_array($candidatos)) {
            throw new Exception('Formato de candidatos inválido');
        }
        
        // Validar que haya candidatos
        if (count($candidatos) === 0) {
            throw new Exception('No hay candidatos para agregar');
        }
        
        $candidatos_agregados = 0;
        $id_solicitud = intval($id_solicitud);
        
        // Obtener el próximo ID disponible
        $sql_max_id = "SELECT NVL(MAX(ID_CANDIDATO), 0) + 1 AS NEXT_ID FROM ROY_CANDIDATOS_SOLICITUD";
        $stmt_max_id = oci_parse($conn, $sql_max_id);
        oci_execute($stmt_max_id);
        $row = oci_fetch_assoc($stmt_max_id);
        $next_id = $row['NEXT_ID'];
        oci_free_statement($stmt_max_id);
        
        foreach ($candidatos as $candidato) {
            $nombre = trim($candidato['nombre'] ?? '');
            $apellidos = trim($candidato['apellidos'] ?? '');
            $documento = trim($candidato['documento'] ?? '');
            
            if (empty($nombre) || empty($apellidos)) {
                continue; // Saltar candidatos incompletos
            }
            
            // Insertar candidato usando la tabla real ROY_CANDIDATOS_SOLICITUD
            $sql_insert = "INSERT INTO ROY_CANDIDATOS_SOLICITUD (
                ID_CANDIDATO, 
                ID_SOLICITUD, 
                NOMBRE_CANDIDATO, 
                APELLIDOS_CANDIDATO, 
                DOCUMENTO_CANDIDATO,
                ESTADO_CANDIDATO,
                FECHA_REGISTRO,
                ACTIVO
            ) VALUES (
                :id_candidato,
                :id_solicitud,
                :nombre,
                :apellidos,
                :documento,
                'CV Enviado',
                SYSDATE,
                'Y'
            )";
            
            $stmt_insert = oci_parse($conn, $sql_insert);
            oci_bind_by_name($stmt_insert, ':id_candidato', $next_id);
            oci_bind_by_name($stmt_insert, ':id_solicitud', $id_solicitud);
            oci_bind_by_name($stmt_insert, ':nombre', $nombre);
            oci_bind_by_name($stmt_insert, ':apellidos', $apellidos);
            oci_bind_by_name($stmt_insert, ':documento', $documento);
            
            if (oci_execute($stmt_insert)) {
                $candidatos_agregados++;
                $next_id++; // Incrementar ID para el siguiente candidato
            }
            oci_free_statement($stmt_insert);
        }
        
        if ($candidatos_agregados > 0) {
            echo json_encode([
                'success' => true,
                'candidatos_agregados' => $candidatos_agregados,
                'message' => "Se agregaron $candidatos_agregados candidato(s) exitosamente"
            ]);
        } else {
            throw new Exception('No se pudo agregar ningún candidato');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    break;



//=========================================================================================
// OBTENER CANDIDATOS POR SOLICITUD
//==========================================================================================
                            //candidatos por solicitud
case 'get_candidatos_por_solicitud':
try {
    $id_solicitud = $_GET['id_solicitud'] ?? null;
    $filtro_estado = $_GET['filtro_estado'] ?? 'todos';
    
    // VALIDACIÓN
    if (empty($id_solicitud)) {
        throw new Exception('ID de solicitud no proporcionado');
    }
    
    $id_solicitud = trim($id_solicitud);
    
    if (!is_numeric($id_solicitud)) {
        throw new Exception('ID de solicitud debe ser numérico. Recibido: ' . $id_solicitud);
    }
    
    $id_solicitud = intval($id_solicitud);
    
    if ($id_solicitud <= 0) {
        throw new Exception('ID de solicitud debe ser mayor a 0');
    }
    
    error_log("ID Solicitud validado correctamente: " . $id_solicitud);
    error_log("Filtro aplicado: " . $filtro_estado);
    
    // PASO 1: Obtener información de la solicitud
    $sql_solicitud = "SELECT 
                        NUM_TIENDA,
                        PUESTO_SOLICITADO,
                        SOLICITADO_POR,
                        ESTADO_SOLICITUD,
                        REACTIVADA,
                        MOTIVO_REACTIVACION,
                        DIRIGIDO_RH,
                        TO_CHAR(FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD
                    FROM ROY_SOLICITUD_PERSONAL 
                    WHERE ID_SOLICITUD = :id_solicitud";
    
    $st_solicitud = oci_parse($conn, $sql_solicitud);
    oci_bind_by_name($st_solicitud, ':id_solicitud', $id_solicitud);
    oci_execute($st_solicitud);
    $info_solicitud = oci_fetch_assoc($st_solicitud);
    oci_free_statement($st_solicitud);

    // PASO 2: Obtener candidatos según filtro
    $candidatos_activos = [];
    $candidatos_inactivos = [];

    // DETECTAR SI ES PLAZA CUBIERTA O REACTIVADA
    $es_plaza_cubierta = strtolower(trim($info_solicitud['ESTADO_SOLICITUD'])) === 'plaza cubierta';
    $es_reactivada = isset($info_solicitud['REACTIVADA']) && $info_solicitud['REACTIVADA'] === 'Y';

    // Condición para filtrar candidatos
    $condicion_plaza_cubierta = "";

    if ($es_plaza_cubierta && !$es_reactivada) {
        // Plaza cubierta SIN reactivación: solo mostrar contratado
        $condicion_plaza_cubierta = " AND c.ESTADO_CANDIDATO = 'Contratado'";
    } elseif ($es_reactivada) {
        // Solicitud reactivada: mostrar TODOS excepto el contratado
        $condicion_plaza_cubierta = " AND c.ESTADO_CANDIDATO != 'Contratado'";
    }

    // CARGAR CANDIDATOS ACTIVOS
    if ($filtro_estado === 'todos' || $filtro_estado === 'activos') {
        $sql_activos = "SELECT 
                            c.ID_CANDIDATO,
                            c.NOMBRE_CANDIDATO,
                            c.APELLIDOS_CANDIDATO,
                            c.DOCUMENTO_CANDIDATO,
                            c.ESTADO_CANDIDATO,
                            c.APROBACION,
                            c.REACTIVADO_POST_CONTRATACION,
                            'Y' AS ACTIVO,
                            NULL AS MOTIVO_DESCARTE,
                            TO_CHAR(c.FECHA_REGISTRO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_REGISTRO,
                            (SELECT COUNT(*) 
                            FROM ROY_ARCHIVOS_SOLICITUD a 
                            WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS TOTAL_ARCHIVOS,
                            -- NUEVO: Obtener información detallada de archivos (usando ESTADO_RELACIONADO)
                            (SELECT LISTAGG(a.NOMBRE_ARCHIVO || '|' || 
                                    NVL(a.ESTADO_RELACIONADO, c.ESTADO_CANDIDATO) || '|' || 
                                    TO_CHAR(a.FECHA_SUBIDA, 'DD-MM-YYYY HH24:MI:SS') || '|' || 
                                    NVL(a.TIPO_ARCHIVO, 'application/octet-stream'), ';;') 
                                    WITHIN GROUP (ORDER BY a.FECHA_SUBIDA DESC)
                            FROM ROY_ARCHIVOS_SOLICITUD a 
                            WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS INFO_ARCHIVOS,
                            -- NUEVO: Obtener el último estado ANTES del descarte
                            (SELECT h.ESTADO_ANTERIOR 
                            FROM ROY_CANDIDATOS_HIST_EST h 
                            WHERE h.ID_CANDIDATO = c.ID_CANDIDATO 
                            AND h.ESTADO_NUEVO = 'Descartado'
                            ORDER BY h.FECHA_CAMBIO DESC 
                            FETCH FIRST 1 ROW ONLY) AS ULTIMO_ESTADO_ALCANZADO
                        FROM ROY_CANDIDATOS_SOLICITUD c
                        WHERE c.ID_SOLICITUD = :id_solicitud
                        AND (c.ACTIVO = 'Y' OR c.ACTIVO IS NULL)
                        AND c.ESTADO_CANDIDATO != 'Descartado'
                        $condicion_plaza_cubierta
                        ORDER BY 
                        CASE 
                            WHEN c.ESTADO_CANDIDATO = 'Aprobacion de Aval' THEN 1
                            WHEN c.ESTADO_CANDIDATO = 'Aprobacion de Aval Enviado' AND c.APROBACION IS NULL THEN 2
                            WHEN c.ESTADO_CANDIDATO = 'Aprobacion de Aval Enviado' THEN 3
                            ELSE 4
                        END,
                        c.FECHA_MODIFICACION DESC NULLS LAST,
                        c.ID_CANDIDATO DESC";

        $st_activos = oci_parse($conn, $sql_activos);
        oci_bind_by_name($st_activos, ':id_solicitud', $id_solicitud);
        oci_execute($st_activos);

        while ($row = oci_fetch_assoc($st_activos)) {
            // Procesar información de archivos
            $archivos = [];
            $ultimoEstadoAlcanzado = $row['ULTIMO_ESTADO_ALCANZADO'];
            
            if (!empty($row['INFO_ARCHIVOS'])) {
                $archivosArray = explode(';;', $row['INFO_ARCHIVOS']);
                foreach ($archivosArray as $archivoInfo) {
                    $partes = explode('|', $archivoInfo);
                    if (count($partes) >= 4) {
                        $archivos[] = [
                            'nombre' => $partes[0],
                            'estado' => $partes[1],
                            'fecha' => $partes[2],
                            'tipo' => $partes[3]
                        ];
                    }
                }
            }
            
            $candidatos_activos[] = [
                'ID_CANDIDATO'        => $row['ID_CANDIDATO'],
                'NOMBRE_COMPLETO'     => trim($row['NOMBRE_CANDIDATO'] . ' ' . $row['APELLIDOS_CANDIDATO']),
                'NOMBRE_CANDIDATO'    => $row['NOMBRE_CANDIDATO'],
                'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                'DOCUMENTO_CANDIDATO' => $row['DOCUMENTO_CANDIDATO'],
                'PUESTO_SOLICITADO'   => $info_solicitud['PUESTO_SOLICITADO'],
                'ESTADO_CANDIDATO'    => $row['ESTADO_CANDIDATO'],
                'APROBACION'          => $row['APROBACION'],
                'REACTIVADO_POST_CONTRATACION' => $row['REACTIVADO_POST_CONTRATACION'],
                'ACTIVO'              => 'Y',
                'MOTIVO_DESCARTE'     => null,
                'FECHA_REGISTRO'      => $row['FECHA_REGISTRO'],
                'TOTAL_ARCHIVOS'      => intval($row['TOTAL_ARCHIVOS']),
                'ES_DESCARTADO'       => false,
                // NUEVOS CAMPOS:
                'ARCHIVOS'            => $archivos,
                'ULTIMO_ESTADO_ALCANZADO' => $ultimoEstadoAlcanzado ?: $row['ESTADO_CANDIDATO']
            ];
        }
        oci_free_statement($st_activos);
    }

    // CARGAR CANDIDATOS INACTIVOS (solo si es reactivada o filtro descartados)
    if (($es_reactivada || $filtro_estado === 'descartados' || $filtro_estado === 'todos') && !$es_plaza_cubierta) {
        $sql_inactivos = "SELECT 
                            c.ID_CANDIDATO,
                            c.NOMBRE_CANDIDATO,
                            c.APELLIDOS_CANDIDATO,
                            c.DOCUMENTO_CANDIDATO,
                            c.ESTADO_CANDIDATO,
                            c.APROBACION,
                            c.MOTIVO_DESCARTE,
                            c.REACTIVADO_POST_CONTRATACION,
                            TO_CHAR(c.FECHA_REGISTRO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_REGISTRO,
                            (SELECT COUNT(*) 
                            FROM ROY_ARCHIVOS_SOLICITUD a 
                            WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS TOTAL_ARCHIVOS,
                            -- MISMO FORMATO PARA ARCHIVOS
                            (SELECT LISTAGG(a.NOMBRE_ARCHIVO || '|' || 
                                    NVL(a.ESTADO_RELACIONADO, c.ESTADO_CANDIDATO) || '|' || 
                                    TO_CHAR(a.FECHA_SUBIDA, 'DD-MM-YYYY HH24:MI:SS') || '|' || 
                                    NVL(a.TIPO_ARCHIVO, 'application/octet-stream'), ';;') 
                                    WITHIN GROUP (ORDER BY a.FECHA_SUBIDA DESC)
                            FROM ROY_ARCHIVOS_SOLICITUD a 
                            WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS INFO_ARCHIVOS,
                            (SELECT h.ESTADO_ANTERIOR 
                            FROM ROY_CANDIDATOS_HIST_EST h 
                            WHERE h.ID_CANDIDATO = c.ID_CANDIDATO 
                            AND h.ESTADO_NUEVO = 'Descartado'
                            ORDER BY h.FECHA_CAMBIO DESC 
                            FETCH FIRST 1 ROW ONLY) AS ULTIMO_ESTADO_ALCANZADO
                        FROM ROY_CANDIDATOS_SOLICITUD c
                        WHERE c.ID_SOLICITUD = :id_solicitud
                        AND c.ACTIVO = 'N'
                        AND c.ESTADO_CANDIDATO != 'Contratado'
                        ORDER BY c.FECHA_REGISTRO DESC";

        $st_inactivos = oci_parse($conn, $sql_inactivos);
        oci_bind_by_name($st_inactivos, ':id_solicitud', $id_solicitud);
        
        if (oci_execute($st_inactivos)) {
            while ($row = oci_fetch_assoc($st_inactivos)) {
                $motivo_descarte = '';
                if ($row['MOTIVO_DESCARTE']) {
                    if (is_object($row['MOTIVO_DESCARTE'])) {
                        $motivo_descarte = $row['MOTIVO_DESCARTE']->load();
                    } else {
                        $motivo_descarte = $row['MOTIVO_DESCARTE'];
                    }
                }
                
                // 🔥 FALTA: Procesar información de archivos para inactivos
                $archivos = [];
                $ultimoEstadoAlcanzado = $row['ULTIMO_ESTADO_ALCANZADO'];
                
                if (!empty($row['INFO_ARCHIVOS'])) {
                    $archivosArray = explode(';;', $row['INFO_ARCHIVOS']);
                    foreach ($archivosArray as $archivoInfo) {
                        $partes = explode('|', $archivoInfo);
                        if (count($partes) >= 4) {
                            $archivos[] = [
                                'nombre' => $partes[0],
                                'estado' => $partes[1],
                                'fecha' => $partes[2],
                                'tipo' => $partes[3]
                            ];
                        }
                    }
                }
                
                $candidatos_inactivos[] = [
                    'ID_CANDIDATO'        => $row['ID_CANDIDATO'],
                    'NOMBRE_COMPLETO'     => trim($row['NOMBRE_CANDIDATO'] . ' ' . $row['APELLIDOS_CANDIDATO']),
                    'NOMBRE_CANDIDATO'    => $row['NOMBRE_CANDIDATO'],
                    'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                    'DOCUMENTO_CANDIDATO' => $row['DOCUMENTO_CANDIDATO'],
                    'PUESTO_SOLICITADO'   => $info_solicitud['PUESTO_SOLICITADO'],
                    'ESTADO_CANDIDATO'    => 'Descartado',
                    'APROBACION'          => $row['APROBACION'],
                    'REACTIVADO_POST_CONTRATACION' => $row['REACTIVADO_POST_CONTRATACION'],
                    'ACTIVO'              => 'N',
                    'MOTIVO_DESCARTE'     => $motivo_descarte,
                    'FECHA_REGISTRO'      => $row['FECHA_REGISTRO'],
                    'TOTAL_ARCHIVOS'      => intval($row['TOTAL_ARCHIVOS']),
                    'ES_DESCARTADO'       => true,
                    // 🔥 AGREGAR: Campos de archivos para inactivos
                    'ARCHIVOS'            => $archivos,
                    'ULTIMO_ESTADO_ALCANZADO' => $ultimoEstadoAlcanzado ?: $row['ESTADO_CANDIDATO']
                ];
            }
        }
        oci_free_statement($st_inactivos);
    }

    // BUSCAR CANDIDATOS CON ESTADO_CANDIDATO = 'Descartado' AUNQUE TENGAN ACTIVO = 'Y'
    if (($es_reactivada || $filtro_estado === 'todos' || $filtro_estado === 'descartados') && !$es_plaza_cubierta) {
    $sql_descartados_por_estado = "SELECT 
                                    c.ID_CANDIDATO,
                                    c.NOMBRE_CANDIDATO,
                                    c.APELLIDOS_CANDIDATO,
                                    c.DOCUMENTO_CANDIDATO,
                                    c.ESTADO_CANDIDATO,
                                    c.APROBACION,
                                    c.MOTIVO_DESCARTE,
                                    c.REACTIVADO_POST_CONTRATACION,
                                    TO_CHAR(c.FECHA_REGISTRO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_REGISTRO,
                                    (SELECT COUNT(*) 
                                    FROM ROY_ARCHIVOS_SOLICITUD a 
                                    WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS TOTAL_ARCHIVOS,
                                    (SELECT LISTAGG(a.NOMBRE_ARCHIVO || '|' || 
                                            NVL(a.ESTADO_RELACIONADO, c.ESTADO_CANDIDATO) || '|' || 
                                            TO_CHAR(a.FECHA_SUBIDA, 'DD-MM-YYYY HH24:MI:SS') || '|' || 
                                            NVL(a.TIPO_ARCHIVO, 'application/octet-stream'), ';;') 
                                            WITHIN GROUP (ORDER BY a.FECHA_SUBIDA DESC)
                                    FROM ROY_ARCHIVOS_SOLICITUD a 
                                    WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS INFO_ARCHIVOS,
                                    (SELECT h.ESTADO_ANTERIOR 
                                    FROM ROY_CANDIDATOS_HIST_EST h 
                                    WHERE h.ID_CANDIDATO = c.ID_CANDIDATO 
                                    AND h.ESTADO_NUEVO = 'Descartado'
                                    ORDER BY h.FECHA_CAMBIO DESC 
                                    FETCH FIRST 1 ROW ONLY) AS ULTIMO_ESTADO_ALCANZADO
                                FROM ROY_CANDIDATOS_SOLICITUD c
                                WHERE c.ID_SOLICITUD = :id_solicitud
                                AND c.ESTADO_CANDIDATO = 'Descartado'
                                AND (c.ACTIVO = 'Y' OR c.ACTIVO IS NULL)
                                AND c.ESTADO_CANDIDATO != 'Contratado'
                                ORDER BY c.FECHA_REGISTRO DESC";

        $st_descartados = oci_parse($conn, $sql_descartados_por_estado);
        oci_bind_by_name($st_descartados, ':id_solicitud', $id_solicitud);
        
        if (oci_execute($st_descartados)) {
            while ($row = oci_fetch_assoc($st_descartados)) {
                $motivo_descarte = '';
                if ($row['MOTIVO_DESCARTE']) {
                    if (is_object($row['MOTIVO_DESCARTE'])) {
                        $motivo_descarte = $row['MOTIVO_DESCARTE']->load();
                    } else {
                        $motivo_descarte = $row['MOTIVO_DESCARTE'];
                    }
                }
                
                // 🔥 FALTA: Procesar información de archivos para descartados por estado
                $archivos = [];
                $ultimoEstadoAlcanzado = $row['ULTIMO_ESTADO_ALCANZADO'];
                
                if (!empty($row['INFO_ARCHIVOS'])) {
                    $archivosArray = explode(';;', $row['INFO_ARCHIVOS']);
                    foreach ($archivosArray as $archivoInfo) {
                        $partes = explode('|', $archivoInfo);
                        if (count($partes) >= 4) {
                            $archivos[] = [
                                'nombre' => $partes[0],
                                'estado' => $partes[1],
                                'fecha' => $partes[2],
                                'tipo' => $partes[3]
                            ];
                        }
                    }
                }
                
                $candidatos_inactivos[] = [
                    'ID_CANDIDATO'        => $row['ID_CANDIDATO'],
                    'NOMBRE_COMPLETO'     => trim($row['NOMBRE_CANDIDATO'] . ' ' . $row['APELLIDOS_CANDIDATO']),
                    'NOMBRE_CANDIDATO'    => $row['NOMBRE_CANDIDATO'],
                    'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                    'DOCUMENTO_CANDIDATO' => $row['DOCUMENTO_CANDIDATO'],
                    'PUESTO_SOLICITADO'   => $info_solicitud['PUESTO_SOLICITADO'],
                    'ESTADO_CANDIDATO'    => 'Descartado',
                    'APROBACION'          => $row['APROBACION'],
                    'REACTIVADO_POST_CONTRATACION' => $row['REACTIVADO_POST_CONTRATACION'],
                    'ACTIVO'              => $row['ACTIVO'] ?: 'Y',
                    'MOTIVO_DESCARTE'     => $motivo_descarte,
                    'FECHA_REGISTRO'      => $row['FECHA_REGISTRO'],
                    'TOTAL_ARCHIVOS'      => intval($row['TOTAL_ARCHIVOS']),
                    'ES_DESCARTADO'       => true,
                    // 🔥 AGREGAR: Campos de archivos para descartados por estado
                    'ARCHIVOS'            => $archivos,
                    'ULTIMO_ESTADO_ALCANZADO' => $ultimoEstadoAlcanzado ?: $row['ESTADO_CANDIDATO']
                ];
            }
        }
        oci_free_statement($st_descartados);
    }

    $todos_candidatos = array_merge($candidatos_activos, $candidatos_inactivos);

    // Procesar MOTIVO_REACTIVACION si es CLOB
    $motivo_reactivacion = '';
    if (isset($info_solicitud['MOTIVO_REACTIVACION']) && $info_solicitud['MOTIVO_REACTIVACION']) {
        if (is_object($info_solicitud['MOTIVO_REACTIVACION'])) {
            $motivo_reactivacion = $info_solicitud['MOTIVO_REACTIVACION']->load();
        } else {
            $motivo_reactivacion = $info_solicitud['MOTIVO_REACTIVACION'];
        }
    }

    // RESPUESTA FINAL
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'               => true,
        'solicitud'             => [
            'id' => $id_solicitud,
            'tienda' => $info_solicitud['NUM_TIENDA'],
            'puesto' => $info_solicitud['PUESTO_SOLICITADO'],
            'supervisor' => $info_solicitud['SOLICITADO_POR'],
            'estado' => $info_solicitud['ESTADO_SOLICITUD'],
            'fecha_solicitud' => $info_solicitud['FECHA_SOLICITUD'],
            'reactivada' => $es_reactivada ? 'Y' : 'N',
            'motivo_reactivacion' => $motivo_reactivacion,
            'dirigido_rh' => $info_solicitud['DIRIGIDO_RH'] ?? null
        ],
        'candidatos'            => $todos_candidatos,
        'candidatos_activos'    => $candidatos_activos,
        'candidatos_inactivos'  => $candidatos_inactivos,
        'total'                 => count($todos_candidatos),
        'total_activos'         => count($candidatos_activos),
        'total_inactivos'       => count($candidatos_inactivos),
        'filtro_aplicado'       => $filtro_estado,
        'conteos'               => [
            'total' => count($todos_candidatos),
            'activos' => count($candidatos_activos),
            'descartados' => count($candidatos_inactivos)
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
break;

//=========================================================================================
// VER RESULTADO DE APROBACION DE GERENTE 
//==========================================================================================

case 'obtener_resumen_rrhh':
    $id_solicitud = $_GET['id_solicitud'] ?? $_POST['id_solicitud'];
    
    try {
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
            
            // EXTRAER COMENTARIO LIMPIO - CORREGIDO SIN ERRORES DE REFERENCIA
            $comentario_limpio = 'Sin comentario adicional';
            if ($comentario_completo) {
                error_log("COMENTARIO COMPLETO DEBUG: " . $comentario_completo);
                
                // MÉTODO DIRECTO
                if (strpos($comentario_completo, 'Comentario de aprobacion:') !== false) {
                    $comentario_limpio = substr($comentario_completo, strpos($comentario_completo, 'Comentario de aprobacion:') + strlen('Comentario de aprobacion:'));
                    $comentario_limpio = trim($comentario_limpio);
                    $lineas = explode("\n", $comentario_limpio);
                    $comentario_limpio = trim($lineas[0]);
                } elseif (strpos($comentario_completo, 'Motivo del rechazo:') !== false) {
                    $comentario_limpio = substr($comentario_completo, strpos($comentario_completo, 'Motivo del rechazo:') + strlen('Motivo del rechazo:'));
                    $comentario_limpio = trim($comentario_limpio);
                    $lineas = explode("\n", $comentario_limpio);
                    $comentario_limpio = trim($lineas[0]);
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
                            
                            // CORREGIDO: SIN ERROR DE REFERENCIA
                            if (strpos($linea, ':') !== false) {
                                $partes = explode(':', $linea);
                                $comentario_limpio = trim($partes[count($partes) - 1]); // USAR ÍNDICE
                            } else {
                                $comentario_limpio = $linea;
                            }
                            break;
                        }
                    }
                }
                
                // ÚLTIMA LIMPIEZA
                $comentario_limpio = str_replace(['?', '??'], '', $comentario_limpio);
                $comentario_limpio = preg_replace('/\s*Fecha de procesamiento:.*$/', '', $comentario_limpio);
                $comentario_limpio = trim($comentario_limpio);
                
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

//=========================================================================================
// CAMBIAR ESTADO POR CANDIDATO
//==========================================================================================

                            //cambiar estado por candidato
                        case 'cambiar_estado_candidato':
                            try {
                                $id_candidato = intval($_POST['id_candidato'] ?? 0);
                                $nuevo_estado = $_POST['nuevo_estado'] ?? '';
                                $observaciones = $_POST['observaciones'] ?? '';
                                $es_jefe = intval($_POST['es_jefe'] ?? 0);
                                
                                if (!$id_candidato || !$nuevo_estado) {
                                    enviarJSON(['success' => false, 'error' => 'Datos incompletos']);
                                }
                                
                                // *** NUEVA VALIDACIÓN DE PROGRESIÓN ***
                                $flujoEstados = ['CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 'Dia de Prueba'];
                                if ($es_jefe) {
                                    $flujoEstados[] = 'Poligrafo';
                                }
                                
                                $estadosEspeciales = ['Aprobacion de Aval', 'Contratado'];
                                
                                // Validar que se puedan subir archivos al nuevo estado
                                if (in_array($nuevo_estado, $estadosEspeciales)) {
                                    // Validar que tiene archivos en TODOS los estados anteriores
                                    foreach ($flujoEstados as $estadoRequerido) {
                                        $stmtVal = oci_parse($conn, "SELECT COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_CANDIDATO = :id AND ESTADO_RELACIONADO = :estado");
                                        oci_bind_by_name($stmtVal, ':id', $id_candidato);
                                        oci_bind_by_name($stmtVal, ':estado', $estadoRequerido);
                                        oci_execute($stmtVal);
                                        $res = oci_fetch_assoc($stmtVal);
                                        oci_free_statement($stmtVal);
                                        
                                        if ($res['TOTAL'] == 0) {
                                            enviarJSON([
                                                'success' => false,
                                                'error' => "No se puede avanzar a '$nuevo_estado'. Falta documentación en el estado: $estadoRequerido"
                                            ]);
                                        }
                                    }
                                } else {
                                    // Para estados normales, validar solo el anterior (si no es el primero)
                                    $posicion = array_search($nuevo_estado, $flujoEstados);
                                    if ($posicion !== false && $posicion > 0) {
                                        $estadoAnterior = $flujoEstados[$posicion - 1];
                                        $stmtVal = oci_parse($conn, "SELECT COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_CANDIDATO = :id AND ESTADO_RELACIONADO = :estado");
                                        oci_bind_by_name($stmtVal, ':id', $id_candidato);
                                        oci_bind_by_name($stmtVal, ':estado', $estadoAnterior);
                                        oci_execute($stmtVal);
                                        $res = oci_fetch_assoc($stmtVal);
                                        oci_free_statement($stmtVal);
                                        
                                        if ($res['TOTAL'] == 0) {
                                            enviarJSON([
                                                'success' => false,
                                                'error' => "Debe completar el estado '$estadoAnterior' antes de avanzar a '$nuevo_estado'"
                                            ]);
                                        }
                                    }
                                }
                                // Obtener información del candidato y solicitud
                                $queryInfo = "SELECT c.ID_SOLICITUD, c.ESTADO_CANDIDATO, c.NOMBRE_CANDIDATO, c.APELLIDOS_CANDIDATO,
                                                    s.COMENTARIO_SOLICITUD
                                            FROM ROY_CANDIDATOS_SOLICITUD c
                                            JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                                            WHERE c.ID_CANDIDATO = :id";
                                $stmtInfo = oci_parse($conn, $queryInfo);
                                oci_bind_by_name($stmtInfo, ':id', $id_candidato);
                                oci_execute($stmtInfo);
                                $infoCandidate = oci_fetch_assoc($stmtInfo);
                                oci_free_statement($stmtInfo);
                                
                                if (!$infoCandidate) {
                                    throw new Exception('Candidato no encontrado');
                                }
                                
                                $estadoAnterior = $infoCandidate['ESTADO_CANDIDATO'];
                                $nombreCompleto = trim($infoCandidate['NOMBRE_CANDIDATO'] . ' ' . $infoCandidate['APELLIDOS_CANDIDATO']);
                                $id_solicitud = intval($infoCandidate['ID_SOLICITUD']);
                                $comentario_general_existente = $infoCandidate['COMENTARIO_SOLICITUD'];
                                
                                // ACTUALIZAR ESTADO DEL CANDIDATO
                                $queryUpdate = "UPDATE ROY_CANDIDATOS_SOLICITUD 
                                            SET ESTADO_CANDIDATO = :nuevo_estado, FECHA_MODIFICACION = SYSDATE
                                            WHERE ID_CANDIDATO = :id_candidato";

                                $stmtUpdate = oci_parse($conn, $queryUpdate);
                                oci_bind_by_name($stmtUpdate, ':nuevo_estado', $nuevo_estado);
                                oci_bind_by_name($stmtUpdate, ':id_candidato', $id_candidato);
                                oci_execute($stmtUpdate);
                                oci_free_statement($stmtUpdate);

                                // ✅ GUARDAR EN HISTORIAL DEL CANDIDATO (ROY_CANDIDATOS_HIST_EST)
                                $observacionCambio = !empty(trim($observaciones)) 
                                    ? "Cambio de estado por RH: {$observaciones}" 
                                    : "Cambio de estado realizado";

                                $queryHistCandidato = "INSERT INTO ROY_CANDIDATOS_HIST_EST 
                                    (ID_CANDIDATO, ESTADO_ANTERIOR, ESTADO_NUEVO, OBSERVACIONES, FECHA_CAMBIO, ACTIVO)
                                    VALUES (:id_candidato, :estado_anterior, :estado_nuevo, :observaciones, SYSDATE, 'Y')";

                                $stmtHistCandidato = oci_parse($conn, $queryHistCandidato);
                                oci_bind_by_name($stmtHistCandidato, ':id_candidato', $id_candidato);
                                oci_bind_by_name($stmtHistCandidato, ':estado_anterior', $estadoAnterior);
                                oci_bind_by_name($stmtHistCandidato, ':estado_nuevo', $nuevo_estado);
                                oci_bind_by_name($stmtHistCandidato, ':observaciones', $observacionCambio);
                                oci_execute($stmtHistCandidato);
                                oci_free_statement($stmtHistCandidato);

                                // EL COMENTARIO GENERAL DE LA SOLICITUD NO SE TOCA

                                oci_commit($conn);

                                echo json_encode([
                                    'success' => true,
                                    'message' => 'Estado del candidato actualizado correctamente',
                                    'comentario_general_preservado' => true
                                ]);
                                
                            } catch (Exception $e) {
                                oci_rollback($conn);
                                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                            }
                            break;
//=========================================================================================
// DESCARTAR CANDIDATO 
//==========================================================================================
case 'descartar_candidato_rh':
    try {
        $id_candidato = $_POST['id_candidato'] ?? null;
        $motivo_descarte = $_POST['motivo_descarte'] ?? null;

        if (!$id_candidato || !$motivo_descarte) {
            echo json_encode(['success' => false, 'error' => 'ID de candidato y motivo de descarte son requeridos']);
            exit;
        }

        $id_candidato = intval($id_candidato);

        // OBTENER DATOS DEL CANDIDATO Y EL NOMBRE DE RH ASIGNADO
        $queryDatos = "SELECT 
                            c.ID_SOLICITUD, 
                            c.ESTADO_CANDIDATO, 
                            c.NOMBRE_CANDIDATO, 
                            c.APELLIDOS_CANDIDATO,
                            s.DIRIGIDO_RH,
                            s.DIRIGIDO_A
                       FROM ROY_CANDIDATOS_SOLICITUD c
                       JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                       WHERE c.ID_CANDIDATO = :id";
        
        $stmtDatos = oci_parse($conn, $queryDatos);
        oci_bind_by_name($stmtDatos, ':id', $id_candidato);
        oci_execute($stmtDatos);
        $dataDatos = oci_fetch_assoc($stmtDatos);
        oci_free_statement($stmtDatos);

        if (!$dataDatos) {
            echo json_encode(['success' => false, 'error' => 'Candidato no encontrado']);
            exit;
        }

        $estadoAnterior = $dataDatos['ESTADO_CANDIDATO'];
        $nombreCompleto = trim($dataDatos['NOMBRE_CANDIDATO'] . ' ' . $dataDatos['APELLIDOS_CANDIDATO']);
        $idSolicitud = intval($dataDatos['ID_SOLICITUD']);
        
        // OBTENER NOMBRE DE RH - PRIORIZAR DIRIGIDO_RH, LUEGO DIRIGIDO_A
        $nombre_rh = null;
        if (!empty($dataDatos['DIRIGIDO_RH'])) {
            $nombre_rh = trim($dataDatos['DIRIGIDO_RH']);
            error_log(" Nombre RH obtenido de DIRIGIDO_RH: $nombre_rh");
        } elseif (!empty($dataDatos['DIRIGIDO_A'])) {
            $nombre_rh = trim($dataDatos['DIRIGIDO_A']);
            error_log(" Nombre RH obtenido de DIRIGIDO_A: $nombre_rh");
        } else {
            $nombre_rh = 'Recursos Humanos';
            error_log(" No se encontró nombre RH, usando genérico");
        }

        // Validar que esté en Entrevista RH
        $estadosPermitidos = [
            'entrevista rh',
            'entrevista tecnica',
            'dia de prueba',
            'poligrafo',
            'expediente completo',
            'expediente rh',
            'confirmacion de contratacion'
        ];

        // Normalizar el estado a minúsculas para comparar
        $estadoNormalizado = strtolower(trim($estadoAnterior));

        // Buscar coincidencia parcial (en caso de acentos o espacios)
        $estadoPermitido = false;
        foreach ($estadosPermitidos as $estado) {
            if (stripos($estadoNormalizado, str_replace(' ', '', $estado)) !== false || 
                $estadoNormalizado === $estado) {
                $estadoPermitido = true;
                break;
            }
        }

        if (!$estadoPermitido) {
            echo json_encode([
                'success' => false, 
                'error' => "Solo se puede descartar candidatos desde 'Entrevista RH' en adelante (excepto Aprobación de Aval). Estado actual: $estadoAnterior"
            ]);
            exit;
        }

        // CREAR OBSERVACIÓN CON NOMBRE DE RH (igual que supervisión)
        $observacionDescarte = "CANDIDATO DESCARTADO POR RRHH: {$nombre_rh} - Motivo: " . $motivo_descarte;
        
        $queryHistDescarte = "INSERT INTO ROY_CANDIDATOS_HIST_EST 
                             (ID_CANDIDATO, ESTADO_ANTERIOR, ESTADO_NUEVO, OBSERVACIONES, FECHA_CAMBIO, ACTIVO)
                             VALUES (:id_candidato, :estado_anterior, 'Descartado', :observaciones, SYSDATE, 'Y')";
        
        $stmtHistDescarte = oci_parse($conn, $queryHistDescarte);
        oci_bind_by_name($stmtHistDescarte, ':id_candidato', $id_candidato);
        oci_bind_by_name($stmtHistDescarte, ':estado_anterior', $estadoAnterior);
        oci_bind_by_name($stmtHistDescarte, ':observaciones', $observacionDescarte);
        oci_execute($stmtHistDescarte);
        oci_free_statement($stmtHistDescarte);

        // Insertar en historial general (igual que supervisión)
      /*  $comentarioGeneral = "Candidato {$nombreCompleto} descartado por RRHH: {$nombre_rh} - Motivo: {$motivo_descarte}";
        
        $queryHistGeneral = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                            (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, FECHA_CAMBIO, TIPO_EVENTO) 
                            VALUES (:id_solicitud, :estado_anterior, 'Descartado', SYSDATE, 'CANDIDATO_DESCARTADO_RH')";
        
        $stmtHistGeneral = oci_parse($conn, $queryHistGeneral);
        oci_bind_by_name($stmtHistGeneral, ':id_solicitud', $idSolicitud);
        oci_bind_by_name($stmtHistGeneral, ':estado_anterior', $estadoAnterior);
        oci_execute($stmtHistGeneral);
        oci_free_statement($stmtHistGeneral);*/

        // Actualizar candidato a descartado (igual que supervisión)
        $queryUpdate = "UPDATE ROY_CANDIDATOS_SOLICITUD
                       SET ESTADO_CANDIDATO = 'Descartado',
                           ACTIVO = 'N',
                           MOTIVO_DESCARTE = :motivo,
                           FECHA_MODIFICACION = SYSDATE
                       WHERE ID_CANDIDATO = :id";
        $stmtUpdate = oci_parse($conn, $queryUpdate);
        oci_bind_by_name($stmtUpdate, ':motivo', $motivo_descarte);
        oci_bind_by_name($stmtUpdate, ':id', $id_candidato);
        oci_execute($stmtUpdate);
        oci_free_statement($stmtUpdate);

        oci_commit($conn);

        echo json_encode([
            'success' => true,
            'message' => 'Candidato descartado correctamente por Recursos Humanos',
            'candidato' => $nombreCompleto,
            'descartado_por' => $nombre_rh // NOMBRE OBTENIDO DE LA SOLICITUD
        ]);

    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;
//=========================================================================================
// CREAR CANDIDATOS ADICIONALES
//==========================================================================================
                            //cargar candidatos adicionales 
                        case 'cargar_candidatos_adicionales':
                            try {
                                $id_solicitud = $_POST['id_solicitud'] ?? null;
                                $candidatos = $_POST['candidatos'] ?? null;

                                if (empty($id_solicitud) || !is_numeric($id_solicitud)) {
                                    throw new Exception('ID de solicitud inválido o no proporcionado');
                                }
                                
                                $id_solicitud = (int)$id_solicitud;
                                if ($id_solicitud <= 0) {
                                    throw new Exception('ID de solicitud debe ser mayor a 0');
                                }
                                
                                if (!$candidatos || !is_array($candidatos) || count($candidatos) === 0) {
                                    throw new Exception('Datos de candidatos son requeridos');
                                }

                                // Verificar solicitud y obtener puesto
                                $sqlPuesto = "SELECT PUESTO_SOLICITADO FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
                                $stP = oci_parse($conn, $sqlPuesto);
                                oci_bind_by_name($stP, ':id', $id_solicitud, -1, SQLT_INT);
                                if (!oci_execute($stP)) { 
                                    $e = oci_error($stP); 
                                    throw new Exception('Error verificando solicitud: '.$e['message']); 
                                }
                                $rowP = oci_fetch_assoc($stP);
                                oci_free_statement($stP);
                                if (!$rowP) throw new Exception('La solicitud con ID '.$id_solicitud.' no existe');
                                $puesto_solicitado = $rowP['PUESTO_SOLICITADO'] ?? '';

                                //  INSERTAR SIN ARCHIVOS CV
                                $sqlInsert = "INSERT INTO ROY_CANDIDATOS_SOLICITUD
                                    (ID_SOLICITUD, NOMBRE_CANDIDATO, APELLIDOS_CANDIDATO,
                                    DOCUMENTO_CANDIDATO, PUESTO_SOLICITADO, ESTADO_CANDIDATO)
                                VALUES
                                    (:id_solicitud, :nombre, :apellidos,
                                    :documento, :puesto, :estado)
                                RETURNING ID_CANDIDATO INTO :id_candidato";
                                $stI = oci_parse($conn, $sqlInsert);

                                $b_id_solicitud = $id_solicitud;
                                $b_nombre = $b_apellidos = $b_documento = $b_puesto = $b_estado = null;
                                $b_id_candidato = null;

                                oci_bind_by_name($stI, ':id_solicitud', $b_id_solicitud, -1, SQLT_INT);
                                oci_bind_by_name($stI, ':nombre',    $b_nombre, 200);
                                oci_bind_by_name($stI, ':apellidos', $b_apellidos, 200);
                                oci_bind_by_name($stI, ':documento', $b_documento, 200);
                                $b_puesto = $puesto_solicitado;
                                oci_bind_by_name($stI, ':puesto',    $b_puesto, 200);
                                $b_estado = 'CV Enviado'; // ESTADO INICIAL SIN CV
                                oci_bind_by_name($stI, ':estado',    $b_estado, 50);
                                oci_bind_by_name($stI, ':id_candidato', $b_id_candidato, -1, SQLT_INT);

                                $ok = [];
                                $errs = [];

                                foreach ($candidatos as $i => $c) {
                                    try {
                                        $b_nombre    = trim($c['nombre']    ?? '');
                                        $b_apellidos = trim($c['apellidos'] ?? '');
                                        $b_documento = isset($c['documento']) ? trim((string)$c['documento']) : null;

                                        if ($b_nombre === '' || $b_apellidos === '') {
                                            throw new Exception('Nombre y apellidos son obligatorios');
                                        }

                                        $b_id_candidato = null;
                                        if (!oci_execute($stI, OCI_NO_AUTO_COMMIT)) {
                                            $eI = oci_error($stI);
                                            throw new Exception('Error insertando candidato: '.$eI['message']);
                                        }

                                        $ok[] = [
                                            'id_candidato'   => $b_id_candidato,
                                            'nombre_completo'=> $b_nombre.' '.$b_apellidos,
                                            'cv_subido' => false //  SIEMPRE FALSE
                                        ];
                                    } catch (Exception $ex) {
                                        $errs[] = 'Candidato '.($i+1).': '.$ex->getMessage();
                                    }
                                }

                                if (count($ok) === 0 && count($errs) > 0) {
                                    oci_rollback($conn);
                                } else {
                                    oci_commit($conn);
                                }

                                if ($stI) oci_free_statement($stI);

                                echo json_encode([
                                    'success' => true,
                                    'candidatos_agregados' => count($ok),
                                    'candidatos' => $ok,
                                    'errores' => $errs,
                                    'mensaje' => 'Candidatos agregados exitosamente. Los CVs se pueden subir posteriormente.'
                                ]);
                            } catch (Exception $e) {
                                @oci_rollback($conn);
                                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                            }
                            break;
//=========================================================================================
// ENVIAR EXPEDIENTE PARA APROBACION DE AVAL
//==========================================================================================
                            //documentos para aval
                            case 'validar_documentos_para_aval':
                            try {
                                $id_candidato = $_GET['id_candidato'] ?? null;
                                
                                if (!$id_candidato) {
                                    throw new Exception('ID de candidato requerido');
                                }
                                
                                // Verificar que todos los estados por los que pasó tengan archivos
                                $query = "SELECT COUNT(*) as TOTAL_ARCHIVOS
                                        FROM ROY_ARCHIVOS_SOLICITUD 
                                        WHERE ID_CANDIDATO = :id_candidato";
                                
                                $stmt = oci_parse($conn, $query);
                                oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
                                oci_execute($stmt);
                                $resultado = oci_fetch_assoc($stmt);
                                oci_free_statement($stmt);
                                
                                $puede_pasar = ($resultado['TOTAL_ARCHIVOS'] >= 3); // Mínimo 3 archivos
                                
                                echo json_encode([
                                    'success' => true,
                                    'puede_pasar_a_aval' => $puede_pasar,
                                    'documentos_faltantes' => $puede_pasar ? [] : ['Faltan documentos mínimos']
                                ]);
                                
                            } catch (Exception $e) {
                                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                            }
                            break;
//=========================================================================================
// OBTENER CARPETAS PROGRESIVAS CONFORME A ESTADO DE CANDIDATO
//==========================================================================================
                            case 'get_carpetas_progresivas_rh':
                                header('Content-Type: application/json; charset=utf-8');
                                try {
                                    $id_candidato = $_GET['id_candidato'] ?? null;
                                    
                                    if (!$id_candidato) {
                                        throw new Exception('ID de candidato requerido');
                                    }
                                    
                                    // *** OBTENER EL PUESTO SOLICITADO DESDE LA BASE DE DATOS ***
                                    $queryPuesto = "SELECT s.PUESTO_SOLICITADO 
                                                FROM ROY_CANDIDATOS_SOLICITUD c 
                                                JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD 
                                                WHERE c.ID_CANDIDATO = :id";
                                    $stmtPuesto = oci_parse($conn, $queryPuesto);
                                    oci_bind_by_name($stmtPuesto, ':id', $id_candidato);
                                    oci_execute($stmtPuesto);
                                    $rowPuesto = oci_fetch_assoc($stmtPuesto);
                                    $puestoSolicitado = $rowPuesto ? $rowPuesto['PUESTO_SOLICITADO'] : '';
                                    oci_free_statement($stmtPuesto);
                                    
                                    // *** CORRECCIÓN: Solo "JEFE DE TIENDA" exacto requiere Polígrafo ***
                                    $esJefeTienda = strtoupper(trim($puestoSolicitado)) === 'JEFE DE TIENDA';
                                    
                                    // Obtener estado actual del candidato
                                    $queryEstado = "SELECT ESTADO_CANDIDATO FROM ROY_CANDIDATOS_SOLICITUD WHERE ID_CANDIDATO = :id";
                                    $stmtEstado = oci_parse($conn, $queryEstado);
                                    oci_bind_by_name($stmtEstado, ':id', $id_candidato);
                                    oci_execute($stmtEstado);
                                    $rowEstado = oci_fetch_assoc($stmtEstado);
                                    $estadoActual = $rowEstado ? $rowEstado['ESTADO_CANDIDATO'] : 'CV Enviado';
                                    oci_free_statement($stmtEstado);
                                    
                                    // Obtener archivos existentes
                                    $queryArchivos = "SELECT ESTADO_RELACIONADO, COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_CANDIDATO = :id GROUP BY ESTADO_RELACIONADO";
                                    $stmtArchivos = oci_parse($conn, $queryArchivos);
                                    oci_bind_by_name($stmtArchivos, ':id', $id_candidato);
                                    oci_execute($stmtArchivos);
                                    
                                    $archivosExistentes = [];
                                    while ($row = oci_fetch_assoc($stmtArchivos)) {
                                        $archivosExistentes[$row['ESTADO_RELACIONADO']] = $row['TOTAL'];
                                    }
                                    oci_free_statement($stmtArchivos);
                                    
                                    // Estados en orden con sus números de progresión
                                    $estadosBase = [
                                        'CV Enviado' => 1,
                                        'Psicometrica' => 2, 
                                        'Entrevista RH' => 3,
                                        'Entrevista Tecnica' => 4,
                                        'Dia de Prueba' => 5
                                    ];
                                    
                                    if ($esJefeTienda) {
                                        $estadosBase['Poligrafo'] = 6;
                                    }
                                    
                                    // Obtener el orden del estado actual
                                    $ordenEstadoActual = isset($estadosBase[$estadoActual]) ? $estadosBase[$estadoActual] : 1;
                                    
                                    $carpetas = [];
                                    
                                    // *** LÓGICA PROGRESIVA: Disponible hasta el estado actual ***
                                    foreach ($estadosBase as $nombreEstado => $orden) {
                                        $tieneArchivos = isset($archivosExistentes[$nombreEstado]) && $archivosExistentes[$nombreEstado] > 0;
                                        
                                        // *** DISPONIBLE SI: orden <= ordenEstadoActual ***
                                        $disponible = $orden <= $ordenEstadoActual;
                                        
                                        $motivo_bloqueo = null;
                                        if (!$disponible) {
                                            $motivo_bloqueo = "Debe avanzar el estado del candidato primero";
                                        } else {
                                            //  AGREGAR LÓGICA PARA MOSTRAR MENSAJES ESPECÍFICOS CUANDO ESTÁ DISPONIBLE PERO SIN ARCHIVOS
                                            if (!$tieneArchivos && $disponible) {
                                                // Obtener nombres del supervisor y gerente
                                                $querySupervisor = "SELECT s.SOLICITADO_POR 
                                                                FROM ROY_CANDIDATOS_SOLICITUD c
                                                                JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                                                                WHERE c.ID_CANDIDATO = :id";
                                                
                                                $stmtSup = oci_parse($conn, $querySupervisor);
                                                oci_bind_by_name($stmtSup, ':id', $id_candidato);
                                                oci_execute($stmtSup);
                                                $rowSup = oci_fetch_assoc($stmtSup);
                                                $nombreSupervisor = $rowSup ? trim($rowSup['SOLICITADO_POR']) : 'el supervisor';
                                                oci_free_statement($stmtSup);
                                                
// Buscar el gerente del supervisor
$nombreGerente = 'Gerente de Operaciones';
if ($nombreSupervisor !== 'el supervisor') {
    $queryGerente = "SELECT DISTINCT udf4_string as NOMBRE_GERENTE 
                    FROM RPS.STORE 
                    WHERE sbs_sid = '680861302000159257' 
                    AND UPPER(TRIM(udf2_string)) = UPPER(TRIM(:supervisor))
                    AND udf4_string IS NOT NULL
                    FETCH FIRST 1 ROWS ONLY";
    
    $stmtGerente = oci_parse($conn, $queryGerente);
    oci_bind_by_name($stmtGerente, ':supervisor', $nombreSupervisor);
    
    if (oci_execute($stmtGerente)) {
        $rowGerente = oci_fetch_assoc($stmtGerente);
        if ($rowGerente && !empty($rowGerente['NOMBRE_GERENTE'])) {
            $nombreGerente = trim($rowGerente['NOMBRE_GERENTE']);
        }
    }
    oci_free_statement($stmtGerente);
}

// ✅ CONVERTIR CÓDIGO A NOMBRE SI ES NECESARIO
$mapeoGerentes = [
    '5210' => 'GIOVANNI CARDOZA',
    '5333' => 'CHRISTIAN QUAN'
];

// Si el nombre es un código, reemplazarlo
if (isset($mapeoGerentes[$nombreGerente])) {
    $nombreGerente = $mapeoGerentes[$nombreGerente];
    error_log("✅ Código de gerente convertido a nombre: $nombreGerente");
}

error_log("🎯 Nombre gerente FINAL: $nombreGerente");
                                                
                                                //  GENERAR MENSAJES ESPECÍFICOS PARA ENTREVISTA TÉCNICA Y DÍA DE PRUEBA
                                    if ($nombreEstado === 'Entrevista Tecnica') {
                                        if ($estadoActual === 'Entrevista Tecnica' || $estadoActual === 'Dia de Prueba' || $estadoActual === 'Poligrafo' || $estadoActual === 'Expediente RH') {
                                            $motivo_bloqueo = "El supervisor {$nombreSupervisor} o el gerente {$nombreGerente} aún no ha subido los archivos correspondientes";
                                        }
                                    } elseif ($nombreEstado === 'Dia de Prueba') {
                                        if ($estadoActual === 'Dia de Prueba' || $estadoActual === 'Poligrafo' || $estadoActual === 'Expediente RH') {
                                            $motivo_bloqueo = "El supervisor {$nombreSupervisor} o el gerente {$nombreGerente} aún no ha subido los archivos correspondientes";
                                        }
                                    }
                                            }
                                        }
                                        
                                        $carpetas[] = [
                                            'nombre' => $nombreEstado,
                                            'completado' => $tieneArchivos,
                                            'disponible' => $disponible,
                                            'archivos' => $tieneArchivos ? $archivosExistentes[$nombreEstado] : 0,
                                            'motivo_bloqueo' => $motivo_bloqueo,
                                            'es_actual' => $nombreEstado === $estadoActual,
                                            'orden' => $orden // Para debug
                                        ];
                                    }
                                    
                                    // Calcular progreso
                                    $completados = count(array_filter($carpetas, function($c) { 
                                        return $c['completado']; 
                                    }));
                                    $porcentajeProgreso = count($carpetas) > 0 ? 
                                        round(($completados / count($carpetas)) * 100) : 0;
                                    
                                    echo json_encode([
                                        'success' => true,
                                        'estadoActual' => $estadoActual,
                                        'carpetas' => $carpetas,
                                        'porcentajeProgreso' => $porcentajeProgreso,
                                        'totalCarpetas' => count($carpetas),
                                        'carpetasCompletadas' => $completados,
                                        'puestoSolicitado' => $puestoSolicitado, // *** AGREGAR PARA DEBUG ***
                                        'esJefeTienda' => $esJefeTienda, // *** AGREGAR PARA DEBUG ***
                                        'notaPoligrafo' => $esJefeTienda ? 'El Polígrafo aplica para JEFE DE TIENDA' : 'El Polígrafo solo aplica para JEFE DE TIENDA'
                                    ]);
                                    
                                } catch (Exception $e) {
                                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                                }
                                break;
//=========================================================================================
// CAMBIO DE ESTADO CONFORME A CARPETAS PROGRESIVAS
//========================================================================================== 
                                case 'cambiar_estado_progresivo':
                                try {
                                    $id_candidato = $_POST['id_candidato'] ?? null;
                                    $nuevo_estado = $_POST['nuevo_estado'] ?? null;
                                    $comentario = $_POST['comentario'] ?? '';
                                    
                                    if (!$id_candidato || !$nuevo_estado) {
                                        throw new Exception('ID de candidato y nuevo estado son requeridos');
                                    }
                                    
                                    // *** NUEVA LÓGICA: SOLO VALIDAR PARA "APROBACION DE AVAL" ***
                                    if ($nuevo_estado === 'APROBACION DE AVAL' || $nuevo_estado === 'Aprobado para Aval') {
                                        // Definir estados que deben tener archivos OBLIGATORIOS según el puesto
                                        $flujoEstados = ['CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 'Dia de Prueba'];
                                        
                                        // *** CORRECCIÓN: Solo "JEFE DE TIENDA" específicamente requiere Polígrafo ***
                                        $queryPuesto = "SELECT s.PUESTO_SOLICITADO FROM ROY_CANDIDATOS_SOLICITUD c JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD WHERE c.ID_CANDIDATO = :id";
                                        $stmtPuesto = oci_parse($conn, $queryPuesto);
                                        oci_bind_by_name($stmtPuesto, ':id', $id_candidato);
                                        oci_execute($stmtPuesto);
                                        $rowPuesto = oci_fetch_assoc($stmtPuesto);
                                        
                                        // *** CAMBIO: Solo "JEFE DE TIENDA" exacto requiere Polígrafo ***
                                        $esJefeTienda = $rowPuesto && strtoupper(trim($rowPuesto['PUESTO_SOLICITADO'])) === 'JEFE DE TIENDA';
                                        oci_free_statement($stmtPuesto);
                                        
                                        if ($esJefeTienda) {
                                            $flujoEstados[] = 'Poligrafo';
                                        }
                                        
                                        // VALIDAR que TODOS los estados requeridos tengan archivos
                                        $estadosSinArchivos = [];
                                        foreach ($flujoEstados as $estadoRequerido) {
                                            $queryValidar = "SELECT COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_CANDIDATO = :id AND ESTADO_RELACIONADO = :estado";
                                            $stmtValidar = oci_parse($conn, $queryValidar);
                                            oci_bind_by_name($stmtValidar, ':id', $id_candidato);
                                            oci_bind_by_name($stmtValidar, ':estado', $estadoRequerido);
                                            oci_execute($stmtValidar);
                                            $resultado = oci_fetch_assoc($stmtValidar);
                                            oci_free_statement($stmtValidar);
                                            
                                            if ($resultado['TOTAL'] == 0) {
                                                $estadosSinArchivos[] = $estadoRequerido;
                                            }
                                        }
                                        
                                        // Si faltan archivos, no permitir el cambio
                                        if (!empty($estadosSinArchivos)) {
                                            throw new Exception("No se puede pasar a 'Aprobado para Aval'. Faltan documentos en los siguientes estados: " . implode(', ', $estadosSinArchivos));
                                        }
                                    }
                                    
                                    // Validar que el estado es válido en el flujo
                                    $estadosValidos = [
                                        'CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 
                                        'Dia de Prueba', 'Poligrafo', 'Aprobado para Aval'
                                    ];
                                    
                                   /* if (!in_array($nuevo_estado, $estadosValidos)) {
                                        throw new Exception('Estado no válido en el flujo progresivo');
                                    }*/

                                        // Solo validar que no esté vacío
                                        if (empty(trim($nuevo_estado))) {
                                            throw new Exception('Estado no puede estar vacío');
                                        }
                                    
                                    // Obtener estado anterior
                                    $queryAnterior = "SELECT ESTADO_CANDIDATO FROM ROY_CANDIDATOS_SOLICITUD WHERE ID_CANDIDATO = :id";
                                    $stmtAnterior = oci_parse($conn, $queryAnterior);
                                    oci_bind_by_name($stmtAnterior, ':id', $id_candidato);
                                    oci_execute($stmtAnterior);
                                    $rowAnterior = oci_fetch_assoc($stmtAnterior);
                                    $estadoAnterior = $rowAnterior ? $rowAnterior['ESTADO_CANDIDATO'] : 'Inicial';
                                    oci_free_statement($stmtAnterior);
                                    
                                    // *** CORRECCIÓN: Usar FECHA_MODIFICACION en lugar de FECHA_ACTUALIZACION ***
                                    $queryUpdate = "UPDATE ROY_CANDIDATOS_SOLICITUD SET ESTADO_CANDIDATO = :estado, FECHA_MODIFICACION = SYSDATE WHERE ID_CANDIDATO = :id";
                                    $stmtUpdate = oci_parse($conn, $queryUpdate);
                                    oci_bind_by_name($stmtUpdate, ':estado', $nuevo_estado);
                                    oci_bind_by_name($stmtUpdate, ':id', $id_candidato);
                                    
                                    if (!oci_execute($stmtUpdate)) {
                                        throw new Exception('Error al actualizar el estado del candidato');
                                    }
                                    oci_free_statement($stmtUpdate);
                                    
                                    // Registrar en historial ROY_CANDIDATOS_HIST_EST
                                    $queryHistorial = "INSERT INTO ROY_CANDIDATOS_HIST_EST 
                                                    (ID_HISTORIAL, ID_CANDIDATO, ESTADO_ANTERIOR, ESTADO_NUEVO, FECHA_CAMBIO, OBSERVACIONES, ACTIVO) 
                                                    VALUES (roy_candidatos_hist_est_seq.NEXTVAL, :id, :anterior, :nuevo, SYSDATE, :obs, 'Y')";

                                    $stmtHistorial = oci_parse($conn, $queryHistorial);
                                    oci_bind_by_name($stmtHistorial, ':id', $id_candidato);
                                    oci_bind_by_name($stmtHistorial, ':anterior', $estadoAnterior);
                                    oci_bind_by_name($stmtHistorial, ':nuevo', $nuevo_estado);
                                    oci_bind_by_name($stmtHistorial, ':obs', $comentario);

                                    if (!oci_execute($stmtHistorial)) {
                                        $error = oci_error($stmtHistorial);
                                        error_log('Error al registrar historial: ' . print_r($error, true));
                                    }
                                    oci_free_statement($stmtHistorial);
                                    
                                    // Confirmar transacción
                                    oci_commit($conn);
                                    
                                    echo json_encode([
                                        'success' => true,
                                        'message' => 'Estado actualizado correctamente',
                                        'estado_anterior' => $estadoAnterior,
                                        'estado_nuevo' => $nuevo_estado
                                    ]);
                                    
                                } catch (Exception $e) {
                                    oci_rollback($conn);
                                    echo json_encode([
                                        'success' => false, 
                                        'error' => $e->getMessage()
                                    ]);
                                }
                                break;
//=========================================================================================
// ESTADOS MAESTROS
//==========================================================================================
                            //obtener estados maestros para combo
                        case 'get_estados_master':
                            try {
                                // Consultar desde la base de datos con el orden correcto
                                $query = "SELECT NOMBRE_ESTADO, ORDEN_PROCESO, SOLO_JEFES, PERMITE_ARCHIVOS
                                        FROM ROY_ESTADOS_CANDIDATO 
                                        WHERE ACTIVO = 'Y'
                                        AND NOMBRE_ESTADO != 'Expediente RH'  -- Excluir explícitamente
                                        ORDER BY ORDEN_PROCESO";
                                
                                $stmt = oci_parse($conn, $query);
                                
                                if (oci_execute($stmt)) {
                                    $estados = [];
                                    while ($row = oci_fetch_assoc($stmt)) {
                                        $estados[] = [
                                            'NOMBRE_ESTADO' => $row['NOMBRE_ESTADO'],
                                            'ORDEN_PROCESO' => $row['ORDEN_PROCESO'],
                                            'SOLO_JEFES' => $row['SOLO_JEFES'],
                                            'PERMITE_ARCHIVOS' => $row['PERMITE_ARCHIVOS']
                                        ];
                                    }
                                    oci_free_statement($stmt);
                                    
                                    echo json_encode([
                                        'success' => true,
                                        'estados' => $estados
                                    ]);
                                } else {
                                    // Fallback con el orden correcto
                                    $estadosDefault = [
                                        ['NOMBRE_ESTADO' => 'CV Enviado', 'ORDEN_PROCESO' => 1, 'SOLO_JEFES' => 'N'],
                                        ['NOMBRE_ESTADO' => 'Psicometrica', 'ORDEN_PROCESO' => 2, 'SOLO_JEFES' => 'N'],
                                        ['NOMBRE_ESTADO' => 'Entrevista RH', 'ORDEN_PROCESO' => 3, 'SOLO_JEFES' => 'N'],
                                        ['NOMBRE_ESTADO' => 'Entrevista Tecnica', 'ORDEN_PROCESO' => 4, 'SOLO_JEFES' => 'N'],
                                        ['NOMBRE_ESTADO' => 'Dia de Prueba', 'ORDEN_PROCESO' => 5, 'SOLO_JEFES' => 'N'],
                                        ['NOMBRE_ESTADO' => 'Poligrafo', 'ORDEN_PROCESO' => 6, 'SOLO_JEFES' => 'Y']
                                    ];
                                    
                                    echo json_encode([
                                        'success' => true,
                                        'estados' => $estadosDefault
                                    ]);
                                }
                                
                            } catch (Exception $e) {
                                echo json_encode([
                                    'success' => false,
                                    'error' => $e->getMessage()
                                ]);
                            }
                            break;
//=========================================================================================
// CALCULO DE TOTALIDAD DE CANDIDATOS
//==========================================================================================
                            case 'get_total_candidatos':
                            $id_solicitud = $_GET['id_solicitud'] ?? null;
                            if (!$id_solicitud) {
                                echo json_encode(['success' => false, 'error' => 'ID requerido']);
                                break;
                            }
                            
                            $query = "SELECT COUNT(*) as TOTAL FROM ROY_CANDIDATOS_SOLICITUD 
                                    WHERE ID_SOLICITUD = :id AND ACTIVO = 'Y'";
                            $stmt = oci_parse($conn, $query);
                            oci_bind_by_name($stmt, ':id', $id_solicitud);
                            oci_execute($stmt);
                            $row = oci_fetch_assoc($stmt);
                            oci_free_statement($stmt);
                            
                            echo json_encode([
                                'success' => true,
                                'total' => $row['TOTAL'] ?? 0
                            ]);
                            break;
//=========================================================================================
// HISTORIAL DE CANDIDATO POR SOLICITUD
//==========================================================================================
                        case 'get_historial_candidato':
                            try {
                                $id_candidato = $_GET['id_candidato'] ?? null;
                                
                                if (!$id_candidato) {
                                    throw new Exception('ID de candidato requerido');
                                }
                                
                                $id_candidato = intval($id_candidato);
                                
                                // Obtener información del candidato
                                $queryCandidato = "SELECT c.NOMBRE_CANDIDATO, c.APELLIDOS_CANDIDATO, c.ESTADO_CANDIDATO,
                                                        c.DOCUMENTO_CANDIDATO, s.NUM_TIENDA, s.PUESTO_SOLICITADO
                                                FROM ROY_CANDIDATOS_SOLICITUD c
                                                JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                                                WHERE c.ID_CANDIDATO = :id_candidato";
                                
                                $stmtCandidato = oci_parse($conn, $queryCandidato);
                                oci_bind_by_name($stmtCandidato, ':id_candidato', $id_candidato, -1, SQLT_INT);
                                oci_execute($stmtCandidato);
                                $infoCandidato = oci_fetch_assoc($stmtCandidato);
                                oci_free_statement($stmtCandidato);
                                
                                if (!$infoCandidato) {
                                    throw new Exception('Candidato no encontrado');
                                }
                                
                                // Obtener historial del candidato
                                $query = "SELECT 
                                            h.ID_HISTORIAL,
                                            h.ESTADO_ANTERIOR,
                                            h.ESTADO_NUEVO,
                                            TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                                            h.OBSERVACIONES
                                        FROM roy_candidatos_hist_est h
                                        WHERE h.ID_CANDIDATO = :id_candidato
                                        ORDER BY h.FECHA_CAMBIO ASC";
                                
                                $stmt = oci_parse($conn, $query);
                                oci_bind_by_name($stmt, ':id_candidato', $id_candidato, -1, SQLT_INT);
                                oci_execute($stmt);
                                
                                $historial = [];
                                while ($row = oci_fetch_assoc($stmt)) {
                                    $historial[] = [
                                        'ID_HISTORIAL' => $row['ID_HISTORIAL'],
                                        'ESTADO_ANTERIOR' => $row['ESTADO_ANTERIOR'] ?: 'Inicial',
                                        'ESTADO_NUEVO' => $row['ESTADO_NUEVO'],
                                        'FECHA_CAMBIO' => $row['FECHA_CAMBIO'],
                                        'OBSERVACIONES' => $row['OBSERVACIONES']
                                    ];
                                }
                                oci_free_statement($stmt);
                                
                                echo json_encode([
                                    'success' => true,
                                    'candidato' => [
                                        'id' => $id_candidato,
                                        'nombre' => trim($infoCandidato['NOMBRE_CANDIDATO'] . ' ' . $infoCandidato['APELLIDOS_CANDIDATO']),
                                        'documento' => $infoCandidato['DOCUMENTO_CANDIDATO'],
                                        'estado_actual' => $infoCandidato['ESTADO_CANDIDATO'],
                                        'tienda' => $infoCandidato['NUM_TIENDA'],
                                        'puesto' => $infoCandidato['PUESTO_SOLICITADO']
                                    ],
                                    'historial' => $historial,
                                    'total' => count($historial)
                                ]);
                                
                            } catch (Exception $e) {
                                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                            }
                            break;
            //=========================================================================================
            //PROGRESION DE ESTADOS POR CANDIDATO
            //=========================================================================================
                case 'validar_progresion_estado':
                    try {
                        $id_candidato = intval($_GET['id_candidato'] ?? 0);
                        $estado_destino = $_GET['estado_destino'] ?? '';
                        $es_jefe = intval($_GET['es_jefe'] ?? 0);
                        
                        if (!$id_candidato || !$estado_destino) {
                            enviarJSON(['success' => false, 'error' => 'Parámetros faltantes']);
                        }
                        
                        // Definir flujo de estados según tu estructura existente
                        $flujoEstados = ['CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 'Dia de Prueba'];
                        if ($es_jefe) {
                            $flujoEstados[] = 'Poligrafo';
                        }
                        
                        // Estados especiales que requieren validación completa
                        $estadosEspeciales = ['Aprobacion de Aval', 'Contratado'];
                        
                        $puedeAvanzar = true;
                        $estadosFaltantes = [];
                        $mensaje = '';
                        
                        if (in_array($estado_destino, $estadosEspeciales)) {
                            // Para estados especiales: validar TODOS los estados anteriores
                            foreach ($flujoEstados as $estadoRequerido) {
                                $queryValidar = "SELECT COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD 
                                            WHERE ID_CANDIDATO = :id_candidato AND ESTADO_RELACIONADO = :estado";
                                $stmtValidar = oci_parse($conn, $queryValidar);
                                oci_bind_by_name($stmtValidar, ':id_candidato', $id_candidato);
                                oci_bind_by_name($stmtValidar, ':estado', $estadoRequerido);
                                oci_execute($stmtValidar);
                                $resultado = oci_fetch_assoc($stmtValidar);
                                oci_free_statement($stmtValidar);
                                
                                if ($resultado['TOTAL'] == 0) {
                                    $puedeAvanzar = false;
                                    $estadosFaltantes[] = $estadoRequerido;
                                }
                            }
                            
                            if (!$puedeAvanzar) {
                                $mensaje = "Para avanzar a '$estado_destino' debe completar todos los estados: " . implode(', ', $estadosFaltantes);
                            }
                        } else {
                            // Para estados normales: validar solo el anterior
                            $posicionDestino = array_search($estado_destino, $flujoEstados);
                            
                            if ($posicionDestino !== false && $posicionDestino > 0) {
                                $estadoAnterior = $flujoEstados[$posicionDestino - 1];
                                
                                $queryValidar = "SELECT COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD 
                                            WHERE ID_CANDIDATO = :id_candidato AND ESTADO_RELACIONADO = :estado";
                                $stmtValidar = oci_parse($conn, $queryValidar);
                                oci_bind_by_name($stmtValidar, ':id_candidato', $id_candidato);
                                oci_bind_by_name($stmtValidar, ':estado', $estadoAnterior);
                                oci_execute($stmtValidar);
                                $resultado = oci_fetch_assoc($stmtValidar);
                                oci_free_statement($stmtValidar);
                                
                                if ($resultado['TOTAL'] == 0) {
                                    $puedeAvanzar = false;
                                    $mensaje = "Debe completar el estado '$estadoAnterior' antes de avanzar a '$estado_destino'";
                                }
                            }
                        }
                        
                        enviarJSON([
                            'success' => true,
                            'puede_avanzar' => $puedeAvanzar,
                            'mensaje' => $mensaje,
                            'estados_faltantes' => $estadosFaltantes
                        ]);
                        
                    } catch (Exception $e) {
                        enviarJSON(['success' => false, 'error' => $e->getMessage()]);
                    }
                    break;

                    case 'validar_antes_cambio_estado':
                        try {
                            $id_candidato = intval($_GET['id_candidato'] ?? 0);
                            $nuevo_estado = $_GET['nuevo_estado'] ?? '';
                            $es_jefe = intval($_GET['es_jefe'] ?? 0);
                            
                            if (!$id_candidato || !$nuevo_estado) {
                                echo json_encode(['success' => false, 'error' => 'Parámetros faltantes']);
                                exit;
                            }
                            
                            // Estados en orden de tu flujo
                            $flujoEstados = ['CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 'Dia de Prueba'];
                            if ($es_jefe) {
                                $flujoEstados[] = 'Poligrafo';
                            }
                            
                            $estadosEspeciales = ['Aprobado para Aval', 'Contratado'];
                            $puedeAvanzar = true;
                            $mensaje = '';
                            
                            if (in_array($nuevo_estado, $estadosEspeciales)) {
                                // Para estados especiales: necesita TODOS los documentos
                                foreach ($flujoEstados as $estadoRequerido) {
                                    $queryValidar = "SELECT COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_CANDIDATO = :id AND ESTADO_RELACIONADO = :estado";
                                    $stmtValidar = oci_parse($conn, $queryValidar);
                                    oci_bind_by_name($stmtValidar, ':id', $id_candidato);
                                    oci_bind_by_name($stmtValidar, ':estado', $estadoRequerido);
                                    oci_execute($stmtValidar);
                                    $resultado = oci_fetch_assoc($stmtValidar);
                                    oci_free_statement($stmtValidar);
                                    
                                    if ($resultado['TOTAL'] == 0) {
                                        $puedeAvanzar = false;
                                        $mensaje = "Falta documentación en: $estadoRequerido";
                                        break;
                                    }
                                }
                            } else {
                                // Para estados normales: validar solo el anterior
                                $posicion = array_search($nuevo_estado, $flujoEstados);
                                if ($posicion !== false && $posicion > 0) {
                                    $estadoAnterior = $flujoEstados[$posicion - 1];
                                    $queryValidar = "SELECT COUNT(*) as TOTAL FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_CANDIDATO = :id AND ESTADO_RELACIONADO = :estado";
                                    $stmtValidar = oci_parse($conn, $queryValidar);
                                    oci_bind_by_name($stmtValidar, ':id', $id_candidato);
                                    oci_bind_by_name($stmtValidar, ':estado', $estadoAnterior);
                                    oci_execute($stmtValidar);
                                    $resultado = oci_fetch_assoc($stmtValidar);
                                    oci_free_statement($stmtValidar);
                                    
                                    if ($resultado['TOTAL'] == 0) {
                                        $puedeAvanzar = false;
                                        $mensaje = "Debe completar: $estadoAnterior";
                                    }
                                }
                            }
                            
                            echo json_encode([
                                'success' => true,
                                'puede_avanzar' => $puedeAvanzar,
                                'mensaje' => $mensaje
                            ]);
                            exit;
                            
                        } catch (Exception $e) {
                            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                            exit;
                        }
                        break;
//=========================================================================================
// DESCARGAR ARCHIVO Y VER ARCHIVO
//=========================================================================================

                    case 'ver_archivo':
                        $nombre_archivo = $_GET['archivo'] ?? '';
                        if (empty($nombre_archivo)) {
                            http_response_code(404);
                            echo "Archivo no especificado";
                            exit;
                        }
                        
                        $rutaArchivo = __DIR__ . "/archivos_candidatos/" . $nombre_archivo;
                        if (!file_exists($rutaArchivo)) {
                            http_response_code(404);
                            echo "Archivo no encontrado";
                            exit;
                        }
                        
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $rutaArchivo);
                        finfo_close($finfo);
                        
                        while (ob_get_level()) { ob_end_clean(); }
                        header('Content-Type: ' . $mimeType);
                        header('Content-Disposition: inline; filename="' . basename($rutaArchivo) . '"');
                        readfile($rutaArchivo);
                        exit;

                    case 'descargar_archivo':
                        $nombre_archivo = $_GET['archivo'] ?? '';
                        if (empty($nombre_archivo)) {
                            http_response_code(404);
                            echo "Archivo no especificado";
                            exit;
                        }
                        
                        $rutaArchivo = __DIR__ . "/archivos_candidatos/" . $nombre_archivo;
                        if (!file_exists($rutaArchivo)) {
                            http_response_code(404);
                            echo "Archivo no encontrado";
                            exit;
                        }
                        
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($finfo, $rutaArchivo);
                        finfo_close($finfo);
                        
                        while (ob_get_level()) { ob_end_clean(); }
                        header('Content-Type: ' . $mimeType);
                        header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
                        readfile($rutaArchivo);
                        exit;


//=========================================================================================
// OBTENER ARCHIVOS DE CANDIDATO
//=========================================================================================                               
                        case 'obtener_archivos_candidato':
                            try {
                                $id_candidato = intval($_GET['id_candidato'] ?? 0);
                                
                                if (!$id_candidato) {
                                    throw new Exception('ID de candidato requerido');
                                }
                                
                                $query = "SELECT a.ID_ARCHIVO, a.NOMBRE_ARCHIVO, a.ESTADO_RELACIONADO, a.TIPO_ARCHIVO,
                                                a.SUBIDO_POR_ROL, TO_CHAR(a.FECHA_SUBIDA, 'DD/MM/YYYY HH24:MI') as FECHA_SUBIDA,
                                                c.NOMBRE_CANDIDATO, c.APELLIDOS_CANDIDATO
                                        FROM ROY_ARCHIVOS_SOLICITUD a
                                        JOIN ROY_CANDIDATOS_SOLICITUD c ON a.ID_CANDIDATO = c.ID_CANDIDATO
                                        WHERE a.ID_CANDIDATO = :id_candidato
                                        ORDER BY a.FECHA_SUBIDA DESC";
                                
                                $stmt = oci_parse($conn, $query);
                                oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
                                oci_execute($stmt);
                                
                                $archivos = [];
                                while ($row = oci_fetch_assoc($stmt)) {
                                    // Verificar si el archivo físico existe usando tu estructura
                                    $rutaCompleta = __DIR__ . "/archivos_candidatos/" . $row['NOMBRE_ARCHIVO'];
                                    $archivoExiste = file_exists($rutaCompleta);
                                    $tamaño = $archivoExiste ? filesize($rutaCompleta) : 0;
                                    
                                    $archivos[] = [
                                        'ID_ARCHIVO' => $row['ID_ARCHIVO'],
                                        'NOMBRE_ARCHIVO' => $row['NOMBRE_ARCHIVO'],
                                        'TIPO_ARCHIVO' => $row['TIPO_ARCHIVO'],
                                        'ESTADO_RELACIONADO' => $row['ESTADO_RELACIONADO'],
                                        'SUBIDO_POR_ROL' => $row['SUBIDO_POR_ROL'],
                                        'FECHA_SUBIDA' => $row['FECHA_SUBIDA'],
                                        'NOMBRE_CANDIDATO' => $row['NOMBRE_CANDIDATO'],
                                        'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                                        'TAMAÑO_BYTES' => $tamaño,
                                        'TAMAÑO_FORMATTED' => $archivoExiste ? formatearTamaño($tamaño) : 'N/A',
                                        'EXISTE' => $archivoExiste,
                                        'URL_DESCARGA' => $archivoExiste ? './gestionhumana/crudsolicitudesrh.php?action=descargar_archivo&archivo=' . urlencode($row['NOMBRE_ARCHIVO']) : null,
                                        'URL_VER' => $archivoExiste ? './gestionhumana/crudsolicitudesrh.php?action=ver_archivo&archivo=' . urlencode($row['NOMBRE_ARCHIVO']) : null
                                    ];
                                }
                                
                                oci_free_statement($stmt);
                                
                                enviarJSON([
                                    'success' => true,
                                    'archivos' => $archivos,
                                    'total' => count($archivos)
                                ]);
                                
                            } catch (Exception $e) {
                                enviarJSON(['success' => false, 'error' => $e->getMessage()]);
                            }
                            break;

//=========================================================================================
// ELIMINAR ARCHIVO DE CANDIDATO
//==========================================================================================

                            case 'eliminar_archivo_individual':
                                try {
                                    $id_archivo = intval($_POST['id_archivo'] ?? 0);
                                    $nombre_archivo = $_POST['nombre_archivo'] ?? '';
                                    
                                    if (!$id_archivo || !$nombre_archivo) {
                                        enviarJSON(['success' => false, 'error' => 'Datos incompletos - ID archivo y nombre requeridos']);
                                    }
                                    
                                    // Eliminar archivo físico
                                    $rutaArchivo = __DIR__ . "/archivos_candidatos/" . $nombre_archivo;
                                    if (file_exists($rutaArchivo)) {
                                        unlink($rutaArchivo);
                                    }
                                    
                                    // Eliminar registro de BD
                                    $queryEliminar = "DELETE FROM ROY_ARCHIVOS_SOLICITUD WHERE ID_ARCHIVO = :id_archivo";
                                    $stmtEliminar = oci_parse($conn, $queryEliminar);
                                    oci_bind_by_name($stmtEliminar, ':id_archivo', $id_archivo);
                                    
                                    if (oci_execute($stmtEliminar)) {
                                        oci_commit($conn);
                                        enviarJSON([
                                            'success' => true, 
                                            'mensaje' => 'Archivo eliminado correctamente'
                                        ]);
                                    } else {
                                        $error = oci_error($stmtEliminar);
                                        enviarJSON(['success' => false, 'error' => 'Error en BD: ' . $error['message']]);
                                    }
                                    
                                    oci_free_statement($stmtEliminar);
                                    
                                } catch (Exception $e) {
                                    enviarJSON(['success' => false, 'error' => $e->getMessage()]);
                                }
                                break;
//=========================================================================================
// RESULTADO DE APROBACION DE AVAL RH 
//==========================================================================================

// ===================================================================================
// OBTENER INFORMACIÓN COMPLETA DEL AVAL PROCESADO - RH
// ===================================================================================
case 'get_info_aval_completa_rh':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato requerido');
        }
        
        error_log("=== DEBUG RH - ID Candidato: $id_candidato ===");
        
        // CONSULTA PRINCIPAL
        $query = "SELECT 
                    c.ID_CANDIDATO,
                    c.NOMBRE_CANDIDATO,
                    c.APELLIDOS_CANDIDATO,
                    c.DOCUMENTO_CANDIDATO,
                    c.ESTADO_CANDIDATO,
                    c.APROBACION,
                    c.MOTIVO_DECISION,
                    TO_CHAR(c.FECHA_DECISION, 'DD-MM-YYYY HH24:MI:SS') as FECHA_DECISION_FORMATEADA,
                    s.PUESTO_SOLICITADO,
                    s.NUM_TIENDA,
                    s.SOLICITADO_POR as SUPERVISOR,
                    (SELECT COUNT(*) FROM ROY_ARCHIVOS_SOLICITUD a 
                     WHERE a.ID_CANDIDATO = c.ID_CANDIDATO 
                     AND a.ESTADO_RELACIONADO = 'CV Enviado') as ARCHIVOS_CV,
                    (SELECT COUNT(*) FROM ROY_ARCHIVOS_SOLICITUD a 
                     WHERE a.ID_CANDIDATO = c.ID_CANDIDATO 
                     AND a.ESTADO_RELACIONADO = 'Psicometrica') as ARCHIVOS_PSICOMETRICA,
                    (SELECT COUNT(*) FROM ROY_ARCHIVOS_SOLICITUD a 
                     WHERE a.ID_CANDIDATO = c.ID_CANDIDATO 
                     AND a.ESTADO_RELACIONADO = 'Entrevista RH') as ARCHIVOS_ENTREVISTA_RH,
                    (SELECT COUNT(*) FROM ROY_ARCHIVOS_SOLICITUD a 
                     WHERE a.ID_CANDIDATO = c.ID_CANDIDATO 
                     AND a.ESTADO_RELACIONADO = 'Entrevista Tecnica') as ARCHIVOS_ENTREVISTA_TECNICA,
                    (SELECT COUNT(*) FROM ROY_ARCHIVOS_SOLICITUD a 
                     WHERE a.ID_CANDIDATO = c.ID_CANDIDATO 
                     AND a.ESTADO_RELACIONADO = 'Dia de Prueba') as ARCHIVOS_DIA_PRUEBA,
                    (SELECT COUNT(*) FROM ROY_ARCHIVOS_SOLICITUD a 
                     WHERE a.ID_CANDIDATO = c.ID_CANDIDATO 
                     AND a.ESTADO_RELACIONADO = 'Poligrafo') as ARCHIVOS_POLIGRAFO
                FROM ROY_CANDIDATOS_SOLICITUD c
                JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                WHERE c.ID_CANDIDATO = :id_candidato
                AND c.ESTADO_CANDIDATO = 'Aprobacion de Aval Enviado'";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
        
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception('Error ejecutando consulta: ' . $error['message']);
        }
        
        $candidato = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        if (!$candidato) {
            throw new Exception('Candidato no encontrado o no está en estado "Aprobacion de Aval Enviado"');
        }
        
        error_log("DEBUG RH - Candidato encontrado: " . $candidato['NOMBRE_CANDIDATO'] . " " . $candidato['APELLIDOS_CANDIDATO']);
        
        // Procesar MOTIVO_DECISION como CLOB
        $motivo_decision = '';
        if ($candidato['MOTIVO_DECISION']) {
            if (is_object($candidato['MOTIVO_DECISION'])) {
                $motivo_decision = $candidato['MOTIVO_DECISION']->load();
            } else {
                $motivo_decision = $candidato['MOTIVO_DECISION'];
            }
        }

        // INICIALIZAR NOMBRE DEL GERENTE
        $nombreGerente = 'GERENTE DE OPERACIONES';
        error_log("DEBUG RH - Nombre inicial del gerente: $nombreGerente");
        
        // CONSULTA DEBUG HISTORIAL
        $queryDebugHistorial = "SELECT 
                                h.ID_HISTORIAL,
                                h.OBSERVACIONES, 
                                h.USUARIO_CAMBIO,
                                TO_CHAR(h.FECHA_CAMBIO, 'DD-MON-YYYY HH24:MI:SS') as FECHA_CAMBIO
                               FROM ROY_CANDIDATOS_HIST_EST h 
                               WHERE h.ID_CANDIDATO = :id_candidato 
                               AND h.ESTADO_NUEVO = 'Aprobacion de Aval Enviado' 
                               ORDER BY h.FECHA_CAMBIO DESC";

        $stmtDebugHistorial = oci_parse($conn, $queryDebugHistorial);
        oci_bind_by_name($stmtDebugHistorial, ':id_candidato', $id_candidato);
        
        $debug_registros = [];
        if (oci_execute($stmtDebugHistorial)) {
            while ($row = oci_fetch_assoc($stmtDebugHistorial)) {
                $observaciones = $row['OBSERVACIONES'];
                if (is_object($observaciones)) {
                    $observaciones = $observaciones->load();
                }
                
                $debug_registros[] = [
                    'id_historial' => $row['ID_HISTORIAL'],
                    'observaciones' => $observaciones,
                    'usuario_cambio' => $row['USUARIO_CAMBIO'],
                    'fecha_cambio' => $row['FECHA_CAMBIO']
                ];
            }
            
            error_log("DEBUG RH - Total registros historial: " . count($debug_registros));
            error_log("DEBUG RH - Registros completos: " . print_r($debug_registros, true));
        } else {
            $error = oci_error($stmtDebugHistorial);
            error_log("DEBUG RH - Error en consulta historial: " . print_r($error, true));
        }
        oci_free_statement($stmtDebugHistorial);
        
        // BUSCAR NOMBRE DEL GERENTE EN EL PRIMER REGISTRO
        $queryGerente = "SELECT h.OBSERVACIONES, h.USUARIO_CAMBIO
                        FROM ROY_CANDIDATOS_HIST_EST h 
                        WHERE h.ID_CANDIDATO = :id_candidato 
                        AND h.ESTADO_NUEVO = 'Aprobacion de Aval Enviado' 
                        ORDER BY h.FECHA_CAMBIO DESC
                        FETCH FIRST 1 ROWS ONLY";

        $stmtGerente = oci_parse($conn, $queryGerente);
        oci_bind_by_name($stmtGerente, ':id_candidato', $id_candidato);
        
        if (oci_execute($stmtGerente)) {
            $infoGerente = oci_fetch_assoc($stmtGerente);
            
            if ($infoGerente) {
                error_log("DEBUG RH - Info gerente encontrada - Usuario: " . ($infoGerente['USUARIO_CAMBIO'] ?? 'NULL'));
                
                // PASO 1: Intentar extraer nombre de las observaciones
                if (!empty($infoGerente['OBSERVACIONES'])) {
                    $observaciones = $infoGerente['OBSERVACIONES'];
                    
                    if (is_object($observaciones)) {
                        $observaciones = $observaciones->load();
                    }
                    
                    error_log("DEBUG RH - Observaciones a procesar: " . $observaciones);
                    
                    // Patrones mejorados para extraer el nombre del gerente
                    $patronesGerente = [
                        '/Decisión del gerente\s+([^:]+):\s*/',
                        '/Decisi[oó]n del gerente\s+([^:]+):\s*/',
                        '/Decisi\?\?n del gerente\s+([^:]+):\s*/',
                        '/GERENTE:\s*([^-\n\r]+)/i',
                        '/Gerente\s*([^:]+):\s*/',
                        '/Procesado por[:\s]*([^-\n\r]+)/i'
                    ];
                    
                    foreach ($patronesGerente as $index => $patron) {
                        error_log("DEBUG RH - Probando patrón $index: $patron");
                        if (preg_match($patron, $observaciones, $matches)) {
                            error_log("DEBUG RH - Patrón $index COINCIDIÓ: " . print_r($matches, true));
                            $nombreExtraido = trim($matches[1]);
                            if (!empty($nombreExtraido) && strlen($nombreExtraido) > 2) {
                                $nombreGerente = $nombreExtraido;
                                error_log("DEBUG RH - NOMBRE EXTRAÍDO: $nombreGerente");
                                break;
                            }
                        }
                    }
                }
                
                // PASO 2: Si se extrajo un nombre, validarlo
                if ($nombreGerente !== 'GERENTE DE OPERACIONES') {
                    error_log("DEBUG RH - Validando nombre extraído: $nombreGerente");
                    
                    $gerente_nombres = [
                        '5333' => 'Christian Quan', 
                        '5210' => 'Giovanni Cardoza'
                    ];
                    
                    foreach ($gerente_nombres as $codigo => $nombre) {
                        if (stripos($nombre, $nombreGerente) !== false || stripos($nombreGerente, $nombre) !== false) {
                            $nombreGerente = $nombre;
                            error_log("DEBUG RH - Nombre validado: $nombreGerente");
                            break;
                        }
                    }
                }
            } else {
                error_log("DEBUG RH - NO se encontró información del gerente");
            }
        } else {
            $error = oci_error($stmtGerente);
            error_log("DEBUG RH - Error en consulta: " . print_r($error, true));
        }
        oci_free_statement($stmtGerente);
        
        error_log("DEBUG RH - NOMBRE GERENTE FINAL: '$nombreGerente'");
        
        // CREAR ARRAY CON TODOS LOS DATOS
        $candidatoFormateado = [
            'ID_CANDIDATO' => $candidato['ID_CANDIDATO'],
            'NOMBRE_CANDIDATO' => $candidato['NOMBRE_CANDIDATO'],
            'APELLIDOS_CANDIDATO' => $candidato['APELLIDOS_CANDIDATO'],
            'DOCUMENTO_CANDIDATO' => $candidato['DOCUMENTO_CANDIDATO'],
            'ESTADO_CANDIDATO' => $candidato['ESTADO_CANDIDATO'],
            'APROBACION' => $candidato['APROBACION'],
            'MOTIVO_DECISION' => $motivo_decision,
            'FECHA_DECISION_FORMATEADA' => $candidato['FECHA_DECISION_FORMATEADA'],
            'PUESTO_SOLICITADO' => $candidato['PUESTO_SOLICITADO'],
            'NUM_TIENDA' => $candidato['NUM_TIENDA'],
            'SUPERVISOR' => $candidato['SUPERVISOR'],
            'ARCHIVOS_CV' => intval($candidato['ARCHIVOS_CV']),
            'ARCHIVOS_PSICOMETRICA' => intval($candidato['ARCHIVOS_PSICOMETRICA']),
            'ARCHIVOS_ENTREVISTA_RH' => intval($candidato['ARCHIVOS_ENTREVISTA_RH']),
            'ARCHIVOS_ENTREVISTA_TECNICA' => intval($candidato['ARCHIVOS_ENTREVISTA_TECNICA']),
            'ARCHIVOS_DIA_PRUEBA' => intval($candidato['ARCHIVOS_DIA_PRUEBA']),
            'ARCHIVOS_POLIGRAFO' => intval($candidato['ARCHIVOS_POLIGRAFO']),
            'NOMBRE_GERENTE' => $nombreGerente
        ];
        
        error_log("DEBUG RH - Array formateado con NOMBRE_GERENTE: " . $candidatoFormateado['NOMBRE_GERENTE']);
        
        echo json_encode([
            'success' => true,
            'candidato' => $candidatoFormateado
        ]);
        
        error_log("=== DEBUG RH FIN ===");
        
    } catch (Exception $e) {
        error_log("ERROR RH: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ===================================================================================
// NUEVO CASE: MARCAR CANDIDATO COMO CONTRATADO
// ===================================================================================
case 'marcar_candidato_contratado':
    try {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        $id_candidato = $_POST['id_candidato'] ?? null;
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato es requerido');
        }
        
        $id_candidato = intval($id_candidato);
        
        // Obtener información del candidato y la solicitud
        $queryInfo = "SELECT c.ID_SOLICITUD, 
                             c.NOMBRE_CANDIDATO, 
                             c.APELLIDOS_CANDIDATO,
                             c.ESTADO_CANDIDATO,
                             c.APROBACION,
                             s.ESTADO_SOLICITUD  -- ✅ AGREGADO: Obtener estado actual de la solicitud
                      FROM ROY_CANDIDATOS_SOLICITUD c
                      JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                      WHERE c.ID_CANDIDATO = :id_candidato 
                      AND c.ACTIVO = 'Y'";
        
        $stmtInfo = oci_parse($conn, $queryInfo);
        oci_bind_by_name($stmtInfo, ':id_candidato', $id_candidato);
        oci_execute($stmtInfo);
        $dataInfo = oci_fetch_assoc($stmtInfo);
        oci_free_statement($stmtInfo);
        
        if (!$dataInfo) {
            throw new Exception('Candidato no encontrado o inactivo');
        }
        
        // Validar que el candidato esté aprobado
        if ($dataInfo['APROBACION'] !== 'Y') {
            throw new Exception('Solo se pueden contratar candidatos que hayan sido aprobados por el gerente');
        }
        
        $id_solicitud = $dataInfo['ID_SOLICITUD'];
        $nombre_completo = trim($dataInfo['NOMBRE_CANDIDATO'] . ' ' . $dataInfo['APELLIDOS_CANDIDATO']);
        $estado_anterior_candidato = $dataInfo['ESTADO_CANDIDATO'];
        $estado_anterior_solicitud = $dataInfo['ESTADO_SOLICITUD']; // ✅ Estado anterior de la solicitud
        
        // Obtener nombre de la asesora de RH de la sesión
        $nombre_rh = $_SESSION['user'][3] ?? 'Recursos Humanos';
        
        // Actualizar estado del candidato a "Contratado"
        $queryUpdateCandidato = "UPDATE ROY_CANDIDATOS_SOLICITUD 
                                 SET ESTADO_CANDIDATO = 'Contratado',
                                     FECHA_MODIFICACION = SYSDATE
                                 WHERE ID_CANDIDATO = :id_candidato";
        
        $stmtUpdateCandidato = oci_parse($conn, $queryUpdateCandidato);
        oci_bind_by_name($stmtUpdateCandidato, ':id_candidato', $id_candidato);
        
        if (!oci_execute($stmtUpdateCandidato)) {
            $error = oci_error($stmtUpdateCandidato);
            throw new Exception('Error al actualizar candidato: ' . $error['message']);
        }
        oci_free_statement($stmtUpdateCandidato);
        
        // Registrar en el historial de candidatos
        $observacion_historial = "Candidato CONTRATADO por RRHH: $nombre_rh - El candidato $nombre_completo fue contratado exitosamente";

        $id_candidato_int = intval($id_candidato);

        $queryHistorial = "INSERT INTO ROY_CANDIDATOS_HIST_EST 
                        (ID_CANDIDATO, ESTADO_ANTERIOR, ESTADO_NUEVO, FECHA_CAMBIO, OBSERVACIONES)
                        VALUES ($id_candidato_int, '$estado_anterior_candidato', 'Contratado', SYSDATE, '$observacion_historial')";

        $stmtHistorial = oci_parse($conn, $queryHistorial);

        if (!oci_execute($stmtHistorial)) {
            $error = oci_error($stmtHistorial);
            throw new Exception('Error al registrar historial: ' . $error['message']);
        }
        oci_free_statement($stmtHistorial);
                
        // Actualizar el estado de la solicitud a "Plaza Cubierta"
        $queryUpdateSolicitud = "UPDATE ROY_SOLICITUD_PERSONAL 
                                 SET ESTADO_SOLICITUD = 'Plaza Cubierta',
                                     FECHA_MODIFICACION = SYSDATE
                                 WHERE ID_SOLICITUD = :id_solicitud";
        
        $stmtUpdateSolicitud = oci_parse($conn, $queryUpdateSolicitud);
        oci_bind_by_name($stmtUpdateSolicitud, ':id_solicitud', $id_solicitud);
        
        if (!oci_execute($stmtUpdateSolicitud)) {
            $error = oci_error($stmtUpdateSolicitud);
            throw new Exception('Error al actualizar solicitud: ' . $error['message']);
        }
        oci_free_statement($stmtUpdateSolicitud);
        
        // ✅ INSERTAR EN HISTORIAL GENERAL DE LA SOLICITUD (CORREGIDO)
        $queryHistGeneral = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                            (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, FECHA_CAMBIO, TIPO_EVENTO) 
                            VALUES (:id_solicitud, :estado_anterior, 'Plaza Cubierta', SYSDATE, 'CANDIDATO_CONTRATADO_RH')";
        
        $stmtHistGeneral = oci_parse($conn, $queryHistGeneral);
        oci_bind_by_name($stmtHistGeneral, ':id_solicitud', $id_solicitud); // ✅ Variable correcta
        oci_bind_by_name($stmtHistGeneral, ':estado_anterior', $estado_anterior_solicitud); // ✅ Variable correcta
        
        if (!oci_execute($stmtHistGeneral)) {
            $error = oci_error($stmtHistGeneral);
            throw new Exception('Error al registrar historial de solicitud: ' . $error['message']);
        }
        oci_free_statement($stmtHistGeneral);
        
        oci_commit($conn);
        
        echo json_encode([
            'success' => true,
            'message' => "Candidato $nombre_completo marcado como CONTRATADO exitosamente",
            'candidato' => $nombre_completo
        ]);
        
    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ===================================================================================
// REACTIVAR CANDIDATOS AL REACTIVAR SOLICITUD - RH
// ===================================================================================

case 'confirmar_reactivacion_rh':
    $idSolicitud = $_POST['id_solicitud'] ?? null;
    $candidatosIds = json_decode($_POST['candidatos_ids'] ?? '[]', true);
    $usuarioRH = $_SESSION['nombre_completo'] ?? $_SESSION['usuario'] ?? 'RH';

    if (!$idSolicitud || empty($candidatosIds)) {
        echo json_encode([
            'success' => false,
            'error' => 'Datos incompletos para confirmar reactivación'
        ]);
        exit;
    }

    try {
        $totalReactivados = 0;
        $candidatosReactivadosInfo = [];

        foreach ($candidatosIds as $idCandidato) {
            // 1. Obtener estado actual del candidato E HISTORIAL
            $sqlEstado = "SELECT 
                c.ESTADO_CANDIDATO,
                c.ACTIVO,
                c.NOMBRE_CANDIDATO,
                c.APELLIDOS_CANDIDATO,
                (SELECT h.ESTADO_ANTERIOR 
                 FROM ROY_CANDIDATOS_HIST_EST h 
                 WHERE h.ID_CANDIDATO = c.ID_CANDIDATO 
                 AND h.ESTADO_NUEVO = 'Descartado'
                 ORDER BY h.FECHA_CAMBIO DESC 
                 FETCH FIRST 1 ROW ONLY) AS ULTIMO_ESTADO_ANTES_DESCARTE
            FROM ROY_CANDIDATOS_SOLICITUD c
            WHERE c.ID_CANDIDATO = :id_candidato";
            
            $stmtEstado = oci_parse($conn, $sqlEstado);
            oci_bind_by_name($stmtEstado, ':id_candidato', $idCandidato);
            oci_execute($stmtEstado);
            $candidato = oci_fetch_assoc($stmtEstado);
            oci_free_statement($stmtEstado);

            if (!$candidato) {
                continue;
            }

            $estadoActual = $candidato['ESTADO_CANDIDATO'];
            $activoActual = $candidato['ACTIVO'];
            $ultimoEstadoAntes = $candidato['ULTIMO_ESTADO_ANTES_DESCARTE'];
            $nombreCompleto = trim($candidato['NOMBRE_CANDIDATO'] . ' ' . $candidato['APELLIDOS_CANDIDATO']);

            // 2. Determinar nuevo estado según lógica de reactivación
            $nuevoEstado = $estadoActual;
            $nuevoActivo = 'Y';

            if ($activoActual === 'N' || $estadoActual === 'Descartado') {
                // Era descartado → Restaurar al último estado donde estuvo
                $nuevoEstado = $ultimoEstadoAntes ?: 'CV Enviado';
            } elseif (stripos($estadoActual, 'No Aprobado') !== false) {
                // Rechazado de aval → Volver a Aprobación de Aval para re-evaluación
                $nuevoEstado = 'Aprobacion de Aval';
            } elseif (stripos($estadoActual, 'Aprobacion de Aval Enviado') !== false) {
                // Enviado para aval → Volver a Aprobación de Aval para NUEVA evaluación
                $nuevoEstado = 'Aprobacion de Aval';
            }
            // Si estaba en otro estado activo → NO CAMBIAR

            // 3. Actualizar candidato
            $sqlUpdate = "UPDATE ROY_CANDIDATOS_SOLICITUD SET
                ACTIVO = :nuevo_activo,
                ESTADO_CANDIDATO = :nuevo_estado,
                REACTIVADO_POST_CONTRATACION = 'Y',
                FECHA_REACTIVACION_CANDIDATO = SYSDATE,
                FECHA_MODIFICACION = SYSDATE,
                APROBACION = :aprobacion_null,
                MOTIVO_DECISION = :motivo_null
            WHERE ID_CANDIDATO = :id_candidato";
            
            $stmtUpdate = oci_parse($conn, $sqlUpdate);
            
            // Limpiar aprobación solo si vuelve a "Aprobacion de Aval"
            $aprobacionNull = ($nuevoEstado === 'Aprobacion de Aval') ? null : null;
            $motivoNull = ($nuevoEstado === 'Aprobacion de Aval') ? null : null;
            
            oci_bind_by_name($stmtUpdate, ':nuevo_activo', $nuevoActivo);
            oci_bind_by_name($stmtUpdate, ':nuevo_estado', $nuevoEstado);
            oci_bind_by_name($stmtUpdate, ':aprobacion_null', $aprobacionNull);
            oci_bind_by_name($stmtUpdate, ':motivo_null', $motivoNull);
            oci_bind_by_name($stmtUpdate, ':id_candidato', $idCandidato);
            
            if (oci_execute($stmtUpdate, OCI_NO_AUTO_COMMIT)) {
                oci_free_statement($stmtUpdate);
                
                // 4. Registrar en historial solo si hubo cambio de estado
                if ($estadoActual !== $nuevoEstado || $activoActual !== $nuevoActivo) {
                    $sqlHistorial = "INSERT INTO ROY_CANDIDATOS_HIST_EST (
                        ID_HISTORIAL, ID_CANDIDATO, 
                        ESTADO_ANTERIOR, ESTADO_NUEVO, 
                        FECHA_CAMBIO, OBSERVACIONES, ACTIVO
                    ) VALUES (
                        roy_candidatos_hist_est_seq.NEXTVAL, :id_candidato,
                        :estado_anterior, :estado_nuevo,
                        SYSDATE, :observaciones, 'Y'
                    )";
                    
                    $stmtHist = oci_parse($conn, $sqlHistorial);
                    $observaciones = "RECURSOS HUMANOS: $usuarioRH - Candidato reactivado tras reactivación de solicitud. Estado restaurado a: $nuevoEstado";
                    
                    oci_bind_by_name($stmtHist, ':id_candidato', $idCandidato);
                    oci_bind_by_name($stmtHist, ':estado_anterior', $estadoActual);
                    oci_bind_by_name($stmtHist, ':estado_nuevo', $nuevoEstado);
                    oci_bind_by_name($stmtHist, ':observaciones', $observaciones);
                    oci_execute($stmtHist, OCI_NO_AUTO_COMMIT);
                    oci_free_statement($stmtHist);
                }

                $totalReactivados++;
                $candidatosReactivadosInfo[] = [
                    'id' => $idCandidato,
                    'nombre' => $nombreCompleto,
                    'estado_anterior' => $estadoActual,
                    'estado_nuevo' => $nuevoEstado
                ];
            }
        }

        // ✅ FIX #4: Validar que al menos se reactivó un candidato
        if ($totalReactivados === 0) {
            oci_rollback($conn);
            echo json_encode([
                'success' => false,
                'error' => 'No se pudo reactivar ningún candidato'
            ]);
            exit;
        }

        // 5. ✅ FIX #1: Actualizar historial de reactivaciones (CORREGIDO)
        // Primero obtener el ID de la reactivación pendiente
        $sqlGetIdReact = "SELECT ID_REACTIVACION 
                         FROM ROY_HIST_REACTIVACIONES 
                         WHERE ID_SOLICITUD = :id_solicitud 
                         AND ESTADO_REACT = 'Pendiente'
                         ORDER BY FECHA_REACTIVACION DESC 
                         FETCH FIRST 1 ROWS ONLY";
        
        $stmtGetId = oci_parse($conn, $sqlGetIdReact);
        oci_bind_by_name($stmtGetId, ':id_solicitud', $idSolicitud);
        oci_execute($stmtGetId);
        $reactRow = oci_fetch_assoc($stmtGetId);
        $idReactivacion = $reactRow['ID_REACTIVACION'] ?? null;
        oci_free_statement($stmtGetId);

        if ($idReactivacion) {
            $sqlUpdateReact = "UPDATE ROY_HIST_REACTIVACIONES SET
                ESTADO_REACT = 'Confirmada',
                FECHA_CONF_RH = SYSDATE,
                TOTAL_CAND_REACT = :total
            WHERE ID_REACTIVACION = :id_reactivacion";
            
            $stmtUpdateReact = oci_parse($conn, $sqlUpdateReact);
            oci_bind_by_name($stmtUpdateReact, ':total', $totalReactivados);
            oci_bind_by_name($stmtUpdateReact, ':id_reactivacion', $idReactivacion);
            oci_execute($stmtUpdateReact, OCI_NO_AUTO_COMMIT);
            oci_free_statement($stmtUpdateReact);
        }

        // 6. Actualizar solicitud - SIEMPRE volver a "Candidatos en Seleccion"
        $sqlUpdateSol = "UPDATE ROY_SOLICITUD_PERSONAL SET
            REACTIVADA = 'N',
            ESTADO_SOLICITUD = 'Candidatos en Seleccion',
            FECHA_MODIFICACION = SYSDATE
        WHERE ID_SOLICITUD = :id_solicitud";
        
        $stmtUpdateSol = oci_parse($conn, $sqlUpdateSol);
        oci_bind_by_name($stmtUpdateSol, ':id_solicitud', $idSolicitud);
        oci_execute($stmtUpdateSol, OCI_NO_AUTO_COMMIT);
        oci_free_statement($stmtUpdateSol);

        // Confirmar transacción
        oci_commit($conn);

        echo json_encode([
            'success' => true,
            'message' => 'Candidatos reactivados correctamente',
            'total_reactivados' => $totalReactivados,
            'candidatos' => $candidatosReactivadosInfo,
            'reactivacion_finalizada' => true
        ]);

    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode([
            'success' => false,
            'error' => 'Error al confirmar reactivación: ' . $e->getMessage()
        ]);
    }
    break;


    //=========================================================================================
    // GENERAR HISTORIAL GENERAL E INDIVIDUAL 
    //=========================================================================================
case 'get_proceso_solicitudes':
    try {
        if (ob_get_level()) ob_clean();
        
        // Parámetros recibidos
        $filtro_tienda = $_GET['filtro_tienda'] ?? '';
        $filtro_supervisor = $_GET['filtro_supervisor'] ?? '';
        $filtro_puesto = $_GET['filtro_puesto'] ?? '';
        
        $usuario_logueado = $_SESSION['user'][12] ?? '';
        
        // Determinar si es individual o general
        $es_individual = !empty($filtro_tienda) || !empty($filtro_supervisor) || !empty($filtro_puesto);
        
        // Construir condiciones WHERE
        $whereConditions = ["1=1"];
        
        // Filtro de gerente (seguridad)
        if (in_array($usuario_logueado, ['5333', '5210'])) {
            $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
            $nombre_gerente = $gerente_nombres[$usuario_logueado];
            $whereConditions[] = "EXISTS (
                SELECT 1 FROM RPS.STORE rps 
                WHERE rps.udf2_string = s.SOLICITADO_POR 
                AND UPPER(TRIM(rps.udf4_string)) = UPPER(TRIM('$nombre_gerente'))
                AND rps.sbs_sid = '680861302000159257'
            )";
        }
        
        // Aplicar filtros adicionales
        if (!empty($filtro_tienda)) {
            $whereConditions[] = "s.NUM_TIENDA = '$filtro_tienda'";
        }
        
        if (!empty($filtro_supervisor)) {
            // Buscar el nombre del supervisor por su código
            $query_sup = "SELECT DISTINCT udf2_string 
                        FROM RPS.STORE 
                        WHERE udf1_string = :codigo_supervisor 
                        AND sbs_sid = '680861302000159257'
                        FETCH FIRST 1 ROWS ONLY";
            
            $stmt_sup = oci_parse($conn, $query_sup);
            oci_bind_by_name($stmt_sup, ':codigo_supervisor', $filtro_supervisor);
            oci_execute($stmt_sup);
            $row_sup = oci_fetch_assoc($stmt_sup);
            
            if ($row_sup) {
                $nombre_supervisor = trim($row_sup['UDF2_STRING']);
                $whereConditions[] = "s.SOLICITADO_POR = '$nombre_supervisor'";
            }
            
            oci_free_statement($stmt_sup);
        }
        
        if (!empty($filtro_puesto)) {
            $whereConditions[] = "UPPER(s.PUESTO_SOLICITADO) LIKE UPPER('%$filtro_puesto%')";
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Query principal
        $query = "
        SELECT
            s.ID_SOLICITUD,
            s.NUM_TIENDA,
            s.PUESTO_SOLICITADO,
            s.SOLICITADO_POR,
            s.ESTADO_SOLICITUD,
            TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_SOLICITUD,
            
            NVL(s.REACTIVADA, 'N') as REACTIVADA,
            s.MOTIVO_REACTIVACION,
            TO_CHAR(s.FECHA_REACTIVACION, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_REACTIVACION,
            
            (SELECT hr.USUARIO_REACTIVO
            FROM ROY_HIST_REACTIVACIONES hr
            WHERE hr.ID_SOLICITUD = s.ID_SOLICITUD
            AND hr.ESTADO_REACT = 'Confirmada'
            ORDER BY hr.FECHA_REACTIVACION DESC
            FETCH FIRST 1 ROWS ONLY) AS USUARIO_REACTIVACION,
            
            (SELECT TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS')
             FROM ROY_HISTORICO_SOLICITUD h
             WHERE h.ID_SOLICITUD = s.ID_SOLICITUD
             AND h.ESTADO_NUEVO = 'Plaza Cubierta'
             ORDER BY h.FECHA_CAMBIO DESC
             FETCH FIRST 1 ROWS ONLY) AS FECHA_PLAZA_CUBIERTA,
            
            (SELECT COUNT(*)
             FROM ROY_CANDIDATOS_SOLICITUD c
             WHERE c.ID_SOLICITUD = s.ID_SOLICITUD
             AND c.ACTIVO = 'Y') AS TOTAL_CANDIDATOS,
            
            (SELECT COUNT(*)
             FROM ROY_HIST_REACTIVACIONES hr
             WHERE hr.ID_SOLICITUD = s.ID_SOLICITUD
             AND hr.ESTADO_REACT = 'Confirmada') AS NUM_REACTIVACIONES
            
        FROM ROY_SOLICITUD_PERSONAL s
        WHERE $whereClause
        ORDER BY s.FECHA_SOLICITUD DESC
        ";
        
        $stmt = oci_parse($conn, $query);
        
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception('Error en query: ' . $error['message']);
        }
        
        $solicitudes = [];
        
        while ($row = oci_fetch_assoc($stmt)) {
            $id_solicitud = $row['ID_SOLICITUD'];
            
            // Calcular tiempos
            $fechaFin = $row['FECHA_PLAZA_CUBIERTA'] ?: null;
            $tiempoTotal = calcularTiempo($row['FECHA_SOLICITUD'], $fechaFin);
            
            $tiempoReactivacion = '';
            if ($row['REACTIVADA'] === 'Y' && $row['FECHA_REACTIVACION']) {
                $tiempoReactivacion = calcularTiempo($row['FECHA_REACTIVACION'], $fechaFin);
            }
            
            // Obtener estado anterior
            $query_estado = "
            SELECT ESTADO_ANTERIOR
            FROM ROY_HISTORICO_SOLICITUD
            WHERE ID_SOLICITUD = :id
            ORDER BY FECHA_CAMBIO DESC
            FETCH FIRST 1 ROWS ONLY
            ";
            
            $stmt_estado = oci_parse($conn, $query_estado);
            oci_bind_by_name($stmt_estado, ':id', $id_solicitud);
            oci_execute($stmt_estado);
            $estado_data = oci_fetch_assoc($stmt_estado);
            $estado_anterior = $estado_data ? $estado_data['ESTADO_ANTERIOR'] : 'Estado Inicial';
            oci_free_statement($stmt_estado);
            
            // Calcular tiempo en estado anterior
            $tiempoEstadoAnterior = calcularTiempo($row['FECHA_SOLICITUD'], $row['FECHA_PLAZA_CUBIERTA']);
            
            // ✅ OBTENER TODAS LAS REACTIVACIONES DE LA SOLICITUD
            $query_reactivaciones = "
            SELECT 
                hr.ID_REACTIVACION,
                hr.NUM_REACTIVACION,
                TO_CHAR(hr.FECHA_REACTIVACION, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_REACTIVACION,
                hr.MOTIVO_REACT,
                hr.NOMBRE_CAND_ANT,
                hr.USUARIO_REACTIVO
            FROM ROY_HIST_REACTIVACIONES hr
            WHERE hr.ID_SOLICITUD = :id_solicitud
            AND hr.ESTADO_REACT = 'Confirmada'
            ORDER BY hr.NUM_REACTIVACION ASC
            ";
            
            $stmt_react = oci_parse($conn, $query_reactivaciones);
            oci_bind_by_name($stmt_react, ':id_solicitud', $id_solicitud);
            oci_execute($stmt_react);
            
            $reactivaciones = [];
            while ($react = oci_fetch_assoc($stmt_react)) {
                // Procesar CLOB para motivo
                $motivo_react = '';
                if (isset($react['MOTIVO_REACT']) && $react['MOTIVO_REACT']) {
                    if (is_object($react['MOTIVO_REACT'])) {
                        $motivo_react = $react['MOTIVO_REACT']->load();
                    } else {
                        $motivo_react = $react['MOTIVO_REACT'];
                    }
                }
                
                $reactivaciones[] = [
                    'ID_REACTIVACION' => $react['ID_REACTIVACION'],
                    'NUM_REACTIVACION' => $react['NUM_REACTIVACION'],
                    'FECHA_REACTIVACION' => $react['FECHA_REACTIVACION'],
                    'MOTIVO_REACT' => $motivo_react,
                    'NOMBRE_CAND_ANT' => $react['NOMBRE_CAND_ANT'],
                    'USUARIO_REACTIVO' => $react['USUARIO_REACTIVO']
                ];
            }
            oci_free_statement($stmt_react);
            
            // ✅ OBTENER CANDIDATOS AGRUPADOS POR REACTIVACIÓN
            $query_candidatos = "
            SELECT 
                c.ID_CANDIDATO,
                c.NOMBRE_CANDIDATO,
                c.APELLIDOS_CANDIDATO,
                c.ESTADO_CANDIDATO,
                c.REACTIVADO_POST_CONTRATACION,
                TO_CHAR(c.FECHA_REGISTRO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_REGISTRO,
                TO_CHAR(c.FECHA_REACTIVACION_CANDIDATO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_REACTIVACION_CANDIDATO,
                c.MOTIVO_DESCARTE,
                (SELECT h.ESTADO_ANTERIOR
                 FROM ROY_CANDIDATOS_HIST_EST h
                 WHERE h.ID_CANDIDATO = c.ID_CANDIDATO
                 ORDER BY h.FECHA_CAMBIO DESC
                 FETCH FIRST 1 ROWS ONLY) AS ESTADO_ANTERIOR
            FROM ROY_CANDIDATOS_SOLICITUD c
            WHERE c.ID_SOLICITUD = :id_solicitud
            AND c.ACTIVO = 'Y'
            ORDER BY c.FECHA_REGISTRO ASC
            ";
            
            $stmt_cand = oci_parse($conn, $query_candidatos);
            oci_bind_by_name($stmt_cand, ':id_solicitud', $id_solicitud);
            oci_execute($stmt_cand);
            
            // ✅ AGRUPAR CANDIDATOS: PROCESO ORIGINAL vs CADA REACTIVACIÓN
            $candidatos_proceso_original = [];
            $candidatos_por_reactivacion = [];
            
            while ($cand = oci_fetch_assoc($stmt_cand)) {
                $tiempoCandidato = calcularTiempo($cand['FECHA_REGISTRO']);
                
                // Procesar CLOB para motivo descarte
                $motivo_descarte = '';
                if (isset($cand['MOTIVO_DESCARTE']) && $cand['MOTIVO_DESCARTE']) {
                    if (is_object($cand['MOTIVO_DESCARTE'])) {
                        $motivo_descarte = $cand['MOTIVO_DESCARTE']->load();
                    } else {
                        $motivo_descarte = $cand['MOTIVO_DESCARTE'];
                    }
                }
                
                $candidato_data = [
                    'ID_CANDIDATO' => $cand['ID_CANDIDATO'],
                    'NOMBRE_COMPLETO' => trim($cand['NOMBRE_CANDIDATO'] . ' ' . $cand['APELLIDOS_CANDIDATO']),
                    'ESTADO_ANTERIOR' => $cand['ESTADO_ANTERIOR'] ?: 'Inicial',
                    'ESTADO_ACTUAL' => $cand['ESTADO_CANDIDATO'],
                    'MOTIVO_DESCARTE' => $motivo_descarte,
                    'FECHA_REGISTRO' => $cand['FECHA_REGISTRO'],
                    'TIEMPO_EN_PROCESO' => $tiempoCandidato,
                    'ES_REACTIVADO' => $cand['REACTIVADO_POST_CONTRATACION'] === 'Y' ? 'Y' : 'N'
                ];
                
                // ✅ CLASIFICAR: ¿Proceso original o reactivación?
                if ($cand['REACTIVADO_POST_CONTRATACION'] !== 'Y' || !$cand['FECHA_REACTIVACION_CANDIDATO']) {
                    // PROCESO ORIGINAL
                    $candidatos_proceso_original[] = $candidato_data;
                } else {
                    // REACTIVADO - Determinar a qué reactivación pertenece
                    $fecha_react_cand = $cand['FECHA_REACTIVACION_CANDIDATO'];
                    
                    $reactivacion_asignada = null;
                    foreach ($reactivaciones as $idx => $react) {
                        $fecha_react_solicitud = $react['FECHA_REACTIVACION'];
                        
                        // Si es la última reactivación, o si la fecha del candidato está antes de la siguiente reactivación
                        $es_ultima = ($idx === count($reactivaciones) - 1);
                        
                        if ($es_ultima) {
                            // Última reactivación - tomar todos los posteriores
                            if ($fecha_react_cand >= $fecha_react_solicitud) {
                                $reactivacion_asignada = $react['NUM_REACTIVACION'];
                                break;
                            }
                        } else {
                            // Comparar con la siguiente reactivación
                            $siguiente_react = $reactivaciones[$idx + 1];
                            $fecha_siguiente = $siguiente_react['FECHA_REACTIVACION'];
                            
                            if ($fecha_react_cand >= $fecha_react_solicitud && $fecha_react_cand < $fecha_siguiente) {
                                $reactivacion_asignada = $react['NUM_REACTIVACION'];
                                break;
                            }
                        }
                    }
                    
                    if ($reactivacion_asignada !== null) {
                        if (!isset($candidatos_por_reactivacion[$reactivacion_asignada])) {
                            $candidatos_por_reactivacion[$reactivacion_asignada] = [];
                        }
                        $candidatos_por_reactivacion[$reactivacion_asignada][] = $candidato_data;
                    }
                }
            }
            oci_free_statement($stmt_cand);
            
            // ✅ CONSTRUIR ARRAY FINAL CON ESTRUCTURA JERÁRQUICA
            $candidatos = [
                'proceso_original' => $candidatos_proceso_original,
                'reactivaciones' => []
            ];
            
            foreach ($reactivaciones as $react) {
                $num_react = $react['NUM_REACTIVACION'];
                $candidatos['reactivaciones'][] = [
                    'info_reactivacion' => $react,
                    'candidatos' => $candidatos_por_reactivacion[$num_react] ?? []
                ];
            }
            
            // Procesar CLOB para motivo de reactivación
            $motivo_reactivacion = '';
            if (isset($row['MOTIVO_REACTIVACION']) && $row['MOTIVO_REACTIVACION']) {
                if (is_object($row['MOTIVO_REACTIVACION'])) {
                    $motivo_reactivacion = $row['MOTIVO_REACTIVACION']->load();
                } else {
                    $motivo_reactivacion = $row['MOTIVO_REACTIVACION'];
                }
            }
            
            // ✅ CALCULAR TOTALES DE CANDIDATOS
            $total_candidatos_original = count($candidatos['proceso_original']);
            $total_candidatos_reactivados = 0;
            foreach ($candidatos['reactivaciones'] as $react_data) {
                $total_candidatos_reactivados += count($react_data['candidatos']);
            }
            $total_candidatos_global = $total_candidatos_original + $total_candidatos_reactivados;
            
            $solicitudes[] = [
                'ID_SOLICITUD' => $id_solicitud,
                'NUM_TIENDA' => $row['NUM_TIENDA'],
                'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
                'SOLICITADO_POR' => $row['SOLICITADO_POR'],
                'ESTADO_ANTERIOR' => $estado_anterior,
                'ESTADO_ACTUAL' => $row['ESTADO_SOLICITUD'],
                'FECHA_SOLICITUD' => $row['FECHA_SOLICITUD'],
                
                'REACTIVADA' => $row['REACTIVADA'],
                'MOTIVO_REACTIVACION' => $motivo_reactivacion,
                'FECHA_REACTIVACION' => $row['FECHA_REACTIVACION'],
                'USUARIO_REACTIVACION' => $row['USUARIO_REACTIVACION'],
                'NUM_REACTIVACIONES' => isset($row['NUM_REACTIVACIONES']) ? intval($row['NUM_REACTIVACIONES']) : 0,
                'FECHA_PLAZA_CUBIERTA' => $row['FECHA_PLAZA_CUBIERTA'],
                
                'TIEMPO_TOTAL' => $tiempoTotal,
                'TIEMPO_REACTIVACION' => $tiempoReactivacion,
                'TIEMPO_ESTADO_ANTERIOR' => $tiempoEstadoAnterior,
                
                // ✅ ESTRUCTURA MEJORADA: CANDIDATOS AGRUPADOS
                'CANDIDATOS' => $candidatos,
                'TOTAL_CANDIDATOS' => $total_candidatos_global,
                'TOTAL_CANDIDATOS_ORIGINAL' => $total_candidatos_original,
                'TOTAL_CANDIDATOS_REACTIVADOS' => $total_candidatos_reactivados
            ];
        }
        
        oci_free_statement($stmt);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'tipo' => $es_individual ? 'individual' : 'general',
            'total_registros' => count($solicitudes),
            'filtros' => [
                'tienda' => $filtro_tienda,
                'supervisor' => $filtro_supervisor,
                'puesto' => $filtro_puesto
            ],
            'datos' => $solicitudes
        ]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    break;
//=========================================================================================
// FINALIZACION
//==========================================================================================
    default:
        enviarJSON(['success' => false, 'error' => 'Acción no reconocida: ' . $action]);
        break;
}

// Cerrar conexión si existe
if ($conn) {
    oci_close($conn);
}
?>