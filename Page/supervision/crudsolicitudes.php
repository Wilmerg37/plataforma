<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Headers críticos para JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');



// Agregar al inicio del archivo para soporte UTF-8
ini_set('default_charset', 'utf-8');
putenv('NLS_LANG=SPANISH_SPAIN.AL32UTF8');

// SOLO PARA DEPURAR — luego desactivar en producción
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../../Funsiones/global.php';
include_once '../../Funsiones/conexion.php';
$conn = Oracle();
if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
    exit;
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {

        // MEJORADO: GET SOLICITUDES PARA SUPERVISORES
case 'get_solicitudes':
        try {
            if (ob_get_level()) {
                ob_clean();
            }
            
            // ← DEBUG TEMPORAL - VER QUÉ HAY EN LA SESIÓN
            /*$debug = [
                'session_id' => session_id(),
                'session_status' => session_status(),
                'session_exists' => isset($_SESSION),
                'session_data' => $_SESSION ?? 'NO SESSION',
                'user_exists' => isset($_SESSION['user']),
                'user_data' => $_SESSION['user'] ?? 'NO USER',
                'user_6_exists' => isset($_SESSION['user'][6]),
                'user_6_value' => $_SESSION['user'][6] ?? 'NO USER[6]'
            ];
            
            header('Content-Type: application/json');
            echo json_encode(['debug' => $debug]);
            exit;*/ // ← QUITAR DESPUÉS DE VER EL DEBUG
            // ← CAMBIAR DE [6] A [12]
        $usuario_logueado = $_SESSION['user'][12] ?? null;
        
        if (!$usuario_logueado) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado en sesión']);
            exit;
        }

        $query = "SELECT * FROM(         
                        SELECT
                            s.ID_SOLICITUD,
                            s.NUM_TIENDA,
                            s.PUESTO_SOLICITADO,
                            s.ESTADO_SOLICITUD,
                            s.ESTADO_APROBACION,  -- ← AGREGAR ESTA LÍNEA
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
                            (SELECT COUNT(*) FROM ROY_CHAT_HISTORICO ch WHERE ch.ID_HISTORICO = h.ID_HISTORICO) AS TOTAL_MENSAJES,
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
                        ORDER BY s.FECHA_SOLICITUD DESC 
                    ) A
                    INNER JOIN (
                        SELECT store_no, udf1_string, udf2_string 
                        FROM RPS.STORE 
                        WHERE sbs_sid = '680861302000159257' 
                    ) sp ON a.solicitado_por = sp.udf2_string 
                        AND A.NUM_TIENDA = sp.store_no
                    WHERE sp.udf1_string = :usuario_logueado";

          $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':usuario_logueado', $usuario_logueado);
            
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
                    'ESTADO_APROBACION' => $row['ESTADO_APROBACION'] ?: 'Por Aprobar', // ← AGREGAR ESTA LÍNEA
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

            header('Content-Type: application/json');
            echo json_encode($solicitudes);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;



        // BUSCAR EMPLEADO POR CÓDIGO CON VALIDACIÓN DE SUPERVISOR
        case 'search_employee':
            if (!isset($_GET['codigo'])) {
                echo json_encode(['error' => 'Código requerido'], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            $codigo = $_GET['codigo'];
            error_log("Buscando empleado con código: " . $codigo);
            
            // Buscar en tabla de supervisores de RPS
            $query = "SELECT udf1_string AS CODIGO, udf2_string AS NOMBRE
                        FROM RPS.STORE
                        WHERE udf1_string = :codigo
                        GROUP BY udf1_string, udf2_string";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':codigo', $codigo);
            oci_execute($stmt);

            $employee = null;
            if ($row = oci_fetch_assoc($stmt)) {
                error_log("Empleado encontrado: " . $row['NOMBRE']);
                
                // Obtener tiendas a cargo
                $queryStores = "SELECT STORE_NO FROM RPS.STORE WHERE udf1_string = :codigo ORDER BY STORE_NO";
                $stmtStores = oci_parse($conn, $queryStores);
                oci_bind_by_name($stmtStores, ':codigo', $codigo);
                oci_execute($stmtStores);
                
                $tiendas = [];
                while ($storeRow = oci_fetch_assoc($stmtStores)) {
                    $tiendas[] = $storeRow['STORE_NO'];
                }
                
                // VALIDACIÓN: Verificar que es supervisor (tiene tiendas)
                if (count($tiendas) > 0) {
                    $employee = [
                        'codigo' => $row['CODIGO'],
                        'nombre' => $row['NOMBRE'],
                        'puesto' => 'Supervisor Regional',
                        'tiendas' => $tiendas,
                        'es_supervisor' => true
                    ];
                    error_log("Supervisor válido con " . count($tiendas) . " tiendas");
                } else {
                    error_log("Empleado sin tiendas - NO ES SUPERVISOR");
                    echo json_encode([
                        'error' => 'ACCESO DENEGADO',
                        'message' => 'El código ingresado no corresponde a un supervisor autorizado.',
                        'codigo_ingresado' => $codigo,
                        'nombre_empleado' => $row['NOMBRE'],
                        'es_supervisor' => false
                    ], JSON_UNESCAPED_UNICODE);
                    oci_free_statement($stmtStores);
                    oci_free_statement($stmt);
                    oci_close($conn);
                    break;
                }
                
                oci_free_statement($stmtStores);
            } else {
                error_log("Código no encontrado");
                echo json_encode([
                    'error' => 'EMPLEADO NO ENCONTRADO',
                    'message' => 'El código ingresado no existe en el sistema.',
                    'codigo_ingresado' => $codigo
                ], JSON_UNESCAPED_UNICODE);
                oci_free_statement($stmt);
                oci_close($conn);
                break;
            }

            oci_free_statement($stmt);
            
            if ($employee) {
                echo json_encode($employee, JSON_UNESCAPED_UNICODE);
            }
            oci_close($conn);
            break;

        // OBTENER LISTA DE SUPERVISORES VÁLIDOS
        case 'get_valid_supervisors':
            $query = "SELECT udf1_string AS CODIGO, udf2_string AS NOMBRE
                        FROM RPS.STORE
                        WHERE udf1_string IS NOT NULL
                        GROUP BY udf1_string, udf2_string
                        HAVING COUNT(STORE_NO) > 0
                        ORDER BY udf2_string";
            $stmt = oci_parse($conn, $query);
            oci_execute($stmt);

            $supervisors = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $supervisors[] = [
                    'codigo' => $row['CODIGO'],
                    'nombre' => $row['NOMBRE']
                ];
            }

            oci_free_statement($stmt);
            oci_close($conn);
            
            echo json_encode($supervisors, JSON_UNESCAPED_UNICODE);
            break;

        // CREAR SOLICITUD
      case 'create_advanced_solicitud':
    $empleado_codigo = $_POST['empleado_codigo'] ?? '';
    $empleado_nombre = $_POST['empleado_nombre'] ?? '';
    $tienda_no = $_POST['tienda_no'] ?? '';
    $puesto_solicitado = $_POST['puesto_solicitado'] ?? '';
    $razon_vacante = $_POST['razon_vacante'] ?? '';
    $dirigido_a = $_POST['dirigido_a'] ?? '';

    if (empty($empleado_codigo) || empty($tienda_no) || empty($puesto_solicitado) || empty($razon_vacante) || empty($dirigido_a)) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos'], JSON_UNESCAPED_UNICODE);
        break;
    }

    // INSERTAR EN TU TABLA ROY_SOLICITUD_PERSONAL
    $query = "INSERT INTO ROY_SOLICITUD_PERSONAL (
                NUM_TIENDA,
                PUESTO_SOLICITADO,
                ESTADO_SOLICITUD,
                FECHA_SOLICITUD,
                FECHA_MODIFICACION,
                SOLICITADO_POR,
                RAZON,
                DIRIGIDO_A
              ) VALUES (
                :tienda_no,
                :puesto_solicitado,
                'Pendiente',
                SYSDATE,
                SYSDATE,
                :empleado_nombre,
                :razon_vacante,
                :dirigido_a
              )";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':tienda_no', $tienda_no);
    oci_bind_by_name($stmt, ':puesto_solicitado', $puesto_solicitado);
    oci_bind_by_name($stmt, ':empleado_nombre', $empleado_nombre);
    oci_bind_by_name($stmt, ':razon_vacante', $razon_vacante);
    oci_bind_by_name($stmt, ':dirigido_a', $dirigido_a);

    if (oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
        if (oci_commit($conn)) {
            echo json_encode([
                'success' => true, 
                'message' => 'Solicitud creada exitosamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            oci_rollback($conn);
            $e = oci_error($conn);
            echo json_encode(['success' => false, 'error' => 'Error en commit: ' . $e['message']], JSON_UNESCAPED_UNICODE);
        }
    } else {
        oci_rollback($conn);
        $e = oci_error($stmt);
        echo json_encode(['success' => false, 'error' => 'Error en insert: ' . $e['message']], JSON_UNESCAPED_UNICODE);
    }

    oci_free_statement($stmt);
    oci_close($conn);
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

    // ENVIAR SELECCIÓN DE CVS A RRHH
    case 'enviar_seleccion_cvs':
    if (empty($_POST['id_solicitud']) || empty($_POST['archivos']) || !isset($_POST['supervisor'])) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
        exit;
    }

    $id_solicitud = $_POST['id_solicitud'];
    $archivos = $_POST['archivos'];
    $supervisor = $_POST['supervisor'];
    $comentario = $_POST['comentario'] ?? '';
    $total = count(explode(',', $archivos));

    // Obtener el ID_HISTORICO más reciente de estado "Cvs Enviados"
    $queryHistId = "SELECT MAX(ID_HISTORICO) AS ID_HISTORICO
                    FROM ROY_HISTORICO_SOLICITUD
                    WHERE ID_SOLICITUD = :id_solicitud
                      AND LOWER(ESTADO_NUEVO) LIKE '%cvs%'";
    $stmtHistId = oci_parse($conn, $queryHistId);
    oci_bind_by_name($stmtHistId, ':id_solicitud', $id_solicitud);
    oci_execute($stmtHistId);
    $rowHistId = oci_fetch_assoc($stmtHistId);
    $id_historico_cv_envio = $rowHistId['ID_HISTORICO'] ?? null;
    oci_free_statement($stmtHistId);

    if (!$id_historico_cv_envio) {
        echo json_encode(['success' => false, 'error' => 'No se encontró el histórico para CVS Enviados.']);
        exit;
    }

    // Desactivar selecciones anteriores para la misma solicitud
    $desactivar = oci_parse($conn, "UPDATE ROY_SELECCION_CVS SET ES_ACTIVA = 'N' WHERE ID_SOLICITUD = :id_solicitud");
    oci_bind_by_name($desactivar, ':id_solicitud', $id_solicitud);
    oci_execute($desactivar);
    oci_free_statement($desactivar);

    // Insertar nueva selección
    $stmt = oci_parse($conn, "
        INSERT INTO ROY_SELECCION_CVS (
            ID_SELECCION, ID_SOLICITUD, SUPERVISOR,
            ARCHIVOS_SELECCIONADOS, COMENTARIO_SUPERVISOR, TOTAL_ARCHIVOS,
            FECHA_SELECCION, ESTADO_SELECCION, ES_ACTIVA, ID_HISTORICO_CV_ENVIO
        ) VALUES (
            ROY_SELECCION_CVS_SEQ.NEXTVAL, :id_solicitud, :supervisor,
            EMPTY_CLOB(), EMPTY_CLOB(), :total,
            SYSDATE, 'ENVIADO', 'Y', :id_historico_cv_envio
        ) RETURNING ARCHIVOS_SELECCIONADOS, COMENTARIO_SUPERVISOR INTO :archivos_clob, :comentario_clob
    ");

    $archivos_clob = oci_new_descriptor($conn, OCI_D_LOB);
    $comentario_clob = oci_new_descriptor($conn, OCI_D_LOB);
    oci_bind_by_name($stmt, ':id_solicitud', $id_solicitud);
    oci_bind_by_name($stmt, ':supervisor', $supervisor);
    oci_bind_by_name($stmt, ':total', $total);
    oci_bind_by_name($stmt, ':id_historico_cv_envio', $id_historico_cv_envio);
    oci_bind_by_name($stmt, ':archivos_clob', $archivos_clob, -1, OCI_B_CLOB);
    oci_bind_by_name($stmt, ':comentario_clob', $comentario_clob, -1, OCI_B_CLOB);

    if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => 'Error al insertar la selección.']);
        exit;
    }

    $archivos_clob->save($archivos);
    $comentario_clob->save($comentario);
    oci_commit($conn);
    oci_free_statement($stmt);
    echo json_encode(['success' => true]);
    break;

//GUARDA LAS SELECCIONES DE LOS ARCHIVOS A LAS BASE DE DATOS
case 'guardar_seleccion_cvs':
    header('Content-Type: application/json');

    $idSolicitud = $_POST['id_solicitud'] ?? null;
    $archivos = isset($_POST['archivos_seleccionados']) ? json_decode($_POST['archivos_seleccionados'], true) : null;
    $comentario = $_POST['comentario'] ?? '';
    $totalArchivos = $_POST['total_archivos'] ?? 0;

    if (empty($idSolicitud) || empty($archivos) || !is_array($archivos)) {
        echo json_encode([
            'success' => false,
            'error' => 'Datos incompletos: solicitud o archivos faltantes'
        ]);
        exit;
    }

    // Obtener nombre del supervisor
    $querySup = "SELECT SOLICITADO_POR FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :idSolicitud";
    $stmtSup = oci_parse($conn, $querySup);
    oci_bind_by_name($stmtSup, ':idSolicitud', $idSolicitud);
    oci_execute($stmtSup);
    $rowSup = oci_fetch_assoc($stmtSup);

    if (!$rowSup || empty($rowSup['SOLICITADO_POR'])) {
        echo json_encode([
            "success" => false,
            "error" => "No se encontró el supervisor de la solicitud"
        ]);
        exit;
    }

    $supervisor = $rowSup['SOLICITADO_POR'];
    $archivosSeleccionados = implode(', ', $archivos);

    // 1. Marcar selecciones anteriores como inactivas
    $updateQuery = "UPDATE ROY_SELECCION_CVS
                    SET ES_ACTIVA = 'N'
                    WHERE ID_SOLICITUD = :idSolicitud AND ES_ACTIVA = 'Y'";
    $stmtUpdate = oci_parse($conn, $updateQuery);
    oci_bind_by_name($stmtUpdate, ':idSolicitud', $idSolicitud);
    oci_execute($stmtUpdate);

    // 2. Obtener el ID_HISTORICO más reciente con estado "CVS Enviados"
    $queryHistorico = "SELECT ID_HISTORICO 
                   FROM ROY_HISTORICO_SOLICITUD
                   WHERE ID_SOLICITUD = :idSolicitud 
                     AND LOWER(ESTADO_NUEVO) LIKE '%cvs%'
                   ORDER BY FECHA_CAMBIO DESC FETCH FIRST 1 ROWS ONLY";
    $stmtHist = oci_parse($conn, $queryHistorico);
    oci_bind_by_name($stmtHist, ':idSolicitud', $idSolicitud);
    oci_execute($stmtHist);
    $rowHist = oci_fetch_assoc($stmtHist);
    $idHistorico = $rowHist['ID_HISTORICO'] ?? null;

    // 3. Insertar nueva selección (sin estado explícito, usará 'ENVIADO' por defecto)
    $queryInsert = "INSERT INTO ROY_SELECCION_CVS (
                    ID_SOLICITUD,
                    ID_HISTORICO_CV_ENVIO,  -- CORRECTO AQUÍ
                    SUPERVISOR,
                    ARCHIVOS_SELECCIONADOS,
                    COMENTARIO_SUPERVISOR,
                    TOTAL_ARCHIVOS,
                    FECHA_SELECCION,
                    ES_ACTIVA
                ) VALUES (
                    :idSolicitud,
                    :idHistorico,
                    :supervisor,
                    :archivos,
                    :comentario,
                    :totalArchivos,
                    SYSDATE,
                    'Y'
                )";

    $stmtInsert = oci_parse($conn, $queryInsert);
    oci_bind_by_name($stmtInsert, ':idSolicitud', $idSolicitud);
    oci_bind_by_name($stmtInsert, ':idHistorico', $idHistorico);
    oci_bind_by_name($stmtInsert, ':supervisor', $supervisor);
    oci_bind_by_name($stmtInsert, ':archivos', $archivosSeleccionados);
    oci_bind_by_name($stmtInsert, ':comentario', $comentario);
    oci_bind_by_name($stmtInsert, ':totalArchivos', $totalArchivos);

    if (oci_execute($stmtInsert)) {
        echo json_encode([
            "success" => true,
            "mensaje" => "Selección guardada correctamente"
        ]);
        exit;
    } else {
        $e = oci_error($stmtInsert);
        echo json_encode([
            "success" => false,
            "error" => "Error al guardar: " . $e['message']
        ]);
        exit;
    }


    //selecciona el boton para que se vea solamente el resumen
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

       // EDITAR SOLICITUD Y GUARDAR HISTORIAL DE CAMBIOS
case 'update_solicitud':
    $id_solicitud = $_POST['id_solicitud'];
    $nueva_tienda = $_POST['tienda_no'];
    $nuevo_puesto = $_POST['puesto'];
    $nueva_razon = $_POST['razon'];
    $nuevo_dirigido_a = $_POST['dirigido_a']; // ← AGREGAR ESTA LÍNEA

    // Validación básica
    if (empty($id_solicitud) || empty($nueva_tienda) || empty($nuevo_puesto) || empty($nueva_razon) || empty($nuevo_dirigido_a)) { // ← AGREGAR || empty($nuevo_dirigido_a)
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit;
    }

    // Obtener valores actuales
    $stmt = oci_parse($conn, "SELECT NUM_TIENDA, PUESTO_SOLICITADO, RAZON, DIRIGIDO_A FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id"); // ← AGREGAR , DIRIGIDO_A
    oci_bind_by_name($stmt, ':id', $id_solicitud);
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        echo json_encode(['success' => false, 'error' => 'Error al obtener solicitud: ' . $e['message']]);
        exit;
    }

    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Solicitud no encontrada']);
        exit;
    }

    $tienda_anterior = $row['NUM_TIENDA'];
    $puesto_anterior = $row['PUESTO_SOLICITADO'];
    $razon_anterior = $row['RAZON'];
    $dirigido_a_anterior = $row['DIRIGIDO_A']; // ← AGREGAR ESTA LÍNEA

    // Actualizar la solicitud
    $stmt_update = oci_parse($conn, "
        UPDATE ROY_SOLICITUD_PERSONAL 
        SET NUM_TIENDA = :tienda, PUESTO_SOLICITADO = :puesto, RAZON = :razon, DIRIGIDO_A = :dirigido_a, FECHA_MODIFICACION = SYSDATE
        WHERE ID_SOLICITUD = :id
    "); // ← AGREGAR , DIRIGIDO_A = :dirigido_a
    oci_bind_by_name($stmt_update, ':tienda', $nueva_tienda);
    oci_bind_by_name($stmt_update, ':puesto', $nuevo_puesto);
    oci_bind_by_name($stmt_update, ':razon', $nueva_razon);
    oci_bind_by_name($stmt_update, ':dirigido_a', $nuevo_dirigido_a); // ← AGREGAR ESTA LÍNEA
    oci_bind_by_name($stmt_update, ':id', $id_solicitud);

    if (!oci_execute($stmt_update)) {
        $e = oci_error($stmt_update);
        echo json_encode(['success' => false, 'error' => 'Error al actualizar solicitud: ' . $e['message']]);
        exit;
    }
    oci_free_statement($stmt_update);

    // Insertar historial solo si hay cambios
    $stmt_hist = oci_parse($conn, "
        INSERT INTO ROY_HISTORICO_EDICION (
            ID_EDICION, ID_SOLICITUD, CAMPO_MODIFICADO, VALOR_ANTERIOR, VALOR_NUEVO, FECHA_CAMBIO
        ) VALUES (
            ROY_HISTORICO_EDICION_SEQ.NEXTVAL, :id_solicitud, :campo, :valor_ant, :valor_nuevo, SYSDATE
        )
    ");

    $campos = [
        'NUM_TIENDA' => [$tienda_anterior, $nueva_tienda],
        'PUESTO_SOLICITADO' => [$puesto_anterior, $nuevo_puesto],
        'RAZON' => [$razon_anterior, $nueva_razon],
        'DIRIGIDO_A' => [$dirigido_a_anterior, $nuevo_dirigido_a] // ← AGREGAR ESTA LÍNEA
    ];

    foreach ($campos as $campo => [$valor_ant, $valor_nuevo]) {
        if ($valor_ant != $valor_nuevo) {
            oci_bind_by_name($stmt_hist, ':id_solicitud', $id_solicitud);
            oci_bind_by_name($stmt_hist, ':campo', $campo);
            oci_bind_by_name($stmt_hist, ':valor_ant', $valor_ant);
            oci_bind_by_name($stmt_hist, ':valor_nuevo', $valor_nuevo);
            oci_execute($stmt_hist);
        }
    }

    oci_free_statement($stmt_hist);
    oci_close($conn);

    echo json_encode(['success' => true, 'message' => 'Solicitud actualizada y cambios registrados']);
    break;


        // OBTENER TIENDAS DEL SUPERVISOR - NUEVO CASE AGREGADO
        case 'get_supervisor_stores':
            try {
                $solicitado_por = $_GET['solicitado_por'] ?? '';
                
                if (empty($solicitado_por)) {
                    echo json_encode([
                        'error' => 'Nombre del supervisor requerido'
                    ]);
                    exit;
                }
                
                // Buscar supervisor por nombre en RPS.STORE
                $query1 = "SELECT udf1_string AS CODIGO, udf2_string AS NOMBRE
                          FROM RPS.STORE
                          WHERE UPPER(udf2_string) = UPPER(:solicitado_por)
                          GROUP BY udf1_string, udf2_string";
                
                $stmt1 = oci_parse($conn, $query1);
                
                if (!$stmt1) {
                    $e = oci_error($conn);
                    throw new Exception('Error preparando consulta empleados: ' . $e['message']);
                }
                
                oci_bind_by_name($stmt1, ':solicitado_por', $solicitado_por);
                
                if (!oci_execute($stmt1)) {
                    $e = oci_error($stmt1);
                    throw new Exception('Error ejecutando consulta empleados: ' . $e['message']);
                }
                
                if ($supervisor = oci_fetch_assoc($stmt1)) {
                    $supervisor_codigo = $supervisor['CODIGO'];
                    $supervisor_nombre = $supervisor['NOMBRE'];
                    
                    // Obtener tiendas del supervisor
                    $query2 = "SELECT STORE_NO FROM RPS.STORE 
                              WHERE udf1_string = :supervisor_codigo 
                              ORDER BY STORE_NO";
                    
                    $stmt2 = oci_parse($conn, $query2);
                    
                    if (!$stmt2) {
                        $e = oci_error($conn);
                        throw new Exception('Error preparando consulta tiendas: ' . $e['message']);
                    }
                    
                    oci_bind_by_name($stmt2, ':supervisor_codigo', $supervisor_codigo);
                    
                    if (!oci_execute($stmt2)) {
                        $e = oci_error($stmt2);
                        throw new Exception('Error ejecutando consulta tiendas: ' . $e['message']);
                    }
                    
                    $tiendas = [];
                    while ($row = oci_fetch_assoc($stmt2)) {
                        $tiendas[] = $row['STORE_NO'];
                    }
                    
                    echo json_encode([
                        'nombre' => $supervisor_nombre,
                        'tiendas' => $tiendas
                    ]);
                    
                    oci_free_statement($stmt2);
                } else {
                    echo json_encode([
                        'error' => 'Supervisor no encontrado o no tiene permisos'
                    ]);
                }
                
                oci_free_statement($stmt1);
                
            } catch (Exception $e) {
                echo json_encode([
                    'error' => 'Error del servidor: ' . $e->getMessage()
                ]);
            }
            oci_close($conn);
            break;

        //CASE HISTORIAL DE MODIFICACIONES
case 'get_historial_edicion':
    if (!isset($_GET['id'])) {
        echo json_encode([]);
        break;
    }

    $id = $_GET['id'];

    $query = "SELECT 
                HE.CAMPO_MODIFICADO,
                HE.VALOR_ANTERIOR,
                HE.VALOR_NUEVO,
                TO_CHAR(HE.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO
              FROM ROY_HISTORICO_EDICION HE
              WHERE HE.ID_SOLICITUD = :id
              ORDER BY HE.FECHA_CAMBIO DESC";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);

    $historial = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $historial[] = $row;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    echo json_encode($historial);
    break;

   //CHAT EMERGENTE
// OBTENER COMENTARIOS DEL CHAT (UNIFICADO) - VERSIÓN SUPERVISORES CORREGIDA
case 'get_comentarios_chat':
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


// GUARDAR RESPUESTA DEL SUPERVISOR (MEJORADO)
case 'guardar_respuesta_chat':
    $id_historico = $_POST['id_historico'] ?? null;
    $mensaje = $_POST['mensaje'] ?? null;
    $rol = $_POST['rol'] ?? 'SUPERVISOR';
    $remitente = $_POST['remitente'] ?? 'SUPERVISOR_SISTEMA';

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
                    REMITENTE,
                    ES_LEIDO
                  ) VALUES (
                    SEQ_CHAT_MENSAJE.NEXTVAL,
                    :id_historico, 
                    :rol, 
                    EMPTY_CLOB(),
                    SYSDATE,
                    :remitente,
                    'N'
                  ) RETURNING MENSAJE INTO :mensaje_clob";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_historico', $id_historico);
        oci_bind_by_name($stmt, ':rol', $rol);
        oci_bind_by_name($stmt, ':remitente', $remitente);

        $clob = oci_new_descriptor($conn, OCI_D_LOB);
        oci_bind_by_name($stmt, ':mensaje_clob', $clob, -1, OCI_B_CLOB);

        if (oci_execute($stmt, OCI_DEFAULT)) {
            if ($clob->save($mensaje)) {
                oci_commit($conn);
                
        // ← BLOQUE AGREGADO AQUÍ ↓
        // MARCAR MENSAJES DE RRHH COMO LEÍDOS CUANDO EL SUPERVISOR ABRE EL CHAT
        $queryMarcarLeido = "UPDATE ROY_CHAT_HISTORICO 
                           SET ES_LEIDO = 'Y'
                           WHERE ID_HISTORICO = :idHistorico 
                           AND UPPER(ROL) = 'RRHH'
                           AND UPPER(ES_LEIDO) = 'N'";

        $stmtMarcarLeido = oci_parse($conn, $queryMarcarLeido);
        oci_bind_by_name($stmtMarcarLeido, ':idHistorico', $idhistorico);

        if (oci_execute($stmtMarcarLeido)) {
            oci_commit($conn);
        }
        oci_free_statement($stmtMarcarLeido);
        // ← HASTA AQUÍ ↑

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


    default:
        echo json_encode([
            'success' => false, 
            'error' => 'Acción no válida: ' . $action,
            'action_recibida' => $action
        ]);
        oci_close($conn);
        exit;

case 'marcar_mensajes_leidos_supervisor':
    $idHistorico = $_POST['id_historico'] ?? 0;
    
    // DEBUG: Log de entrada
    error_log("🔍 SUPERVISOR - ID_HISTORICO recibido: " . $idHistorico);
    
    if (!$idHistorico) {
        echo json_encode(['success' => false, 'error' => 'ID histórico requerido']);
        exit;
    }
    
    try {
        // DEBUG: Verificar qué mensajes hay antes del update
        $queryCheck = "SELECT ID_MENSAJE, ROL, ES_LEIDO FROM ROY_CHAT_HISTORICO WHERE ID_HISTORICO = :idHistorico";
        $stmtCheck = oci_parse($conn, $queryCheck);
        oci_bind_by_name($stmtCheck, ':idHistorico', $idHistorico);
        oci_execute($stmtCheck);
        
        $mensajes = [];
        while ($row = oci_fetch_assoc($stmtCheck)) {
            $mensajes[] = $row;
        }
        error_log("📋 Mensajes antes del update: " . json_encode($mensajes));
        oci_free_statement($stmtCheck);
        
        // Marcar mensajes de RRHH como leídos por el SUPERVISOR
        $query = "UPDATE ROY_CHAT_HISTORICO 
                  SET ES_LEIDO = 'Y' 
                  WHERE ID_HISTORICO = :idHistorico 
                  AND UPPER(ROL) = 'RRHH'
                  AND UPPER(ES_LEIDO) = 'N'";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':idHistorico', $idHistorico);
        
        if (oci_execute($stmt)) {
            $rowsAffected = oci_num_rows($stmt);
            error_log("✅ Filas actualizadas: " . $rowsAffected);
            oci_commit($conn);
            echo json_encode(['success' => true, 'updated' => $rowsAffected]);
        } else {
            $e = oci_error($stmt);
            error_log("❌ Error en UPDATE: " . json_encode($e));
            echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
        }
        
        oci_free_statement($stmt);
        
    } catch (Exception $e) {
        error_log("❌ Excepción: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

        //CASE PARA VER ARCHIVOS DE PRUEBAS PSICOMETRICAS Y POLIGRAFO
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



    }
} else {
    echo json_encode(['success' => false, 'error' => 'No se especificó ninguna acción']);
    oci_close($conn);
}

?>




