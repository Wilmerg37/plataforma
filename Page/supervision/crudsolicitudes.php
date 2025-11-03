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

$action = $_GET['action'] ?? $_POST['action'] ?? null;

if (isset($_GET['action'])) {
    switch ($_GET['action']) {

        // MEJORADO: GET SOLICITUDES PARA SUPERVISORES
// 🚨 SOLUCIÓN DEFINITIVA - REEMPLAZA EL CASE 'get_solicitudes':

case 'get_solicitudes':
    try {
        if (ob_get_level()) ob_clean();

        $usuario_logueado = $_SESSION['user'][12] ?? null;

        if (!$usuario_logueado) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado en sesión']);
            exit;
        }

        // Lista de códigos de usuarios
        $codigos_supervisores = ['5378','5379','6250','6006','5385','5287','5400','5226','5139','5142'];
        $codigos_gerentes = ['5333', '5210']; // Christian Quan y Giovanni Cardoza
        
        $es_supervisor = in_array($usuario_logueado, $codigos_supervisores);
        $es_gerente = in_array($usuario_logueado, $codigos_gerentes);

        // Mapeo de gerentes a nombres
        $gerente_nombres = [
            '5333' => 'Christian Quan', 
            '5210' => 'Giovanni Cardoza'
        ];

        // Query base común
        $baseQuery = "
                    SELECT
                        s.ID_SOLICITUD,
                        s.NUM_TIENDA,
                        s.PUESTO_SOLICITADO,
                        s.ESTADO_SOLICITUD,
                        s.COMENTARIO_SOLICITUD,
                        s.ESTADO_APROBACION,
                        s.DIRIGIDO_RH,
                        TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOL,
                        CASE 
                            WHEN s.FECHA_MODIFICACION != s.FECHA_SOLICITUD 
                            THEN TO_CHAR(s.FECHA_MODIFICACION, 'DD-MM-YYYY HH24:MI:SS')
                            ELSE NULL
                        END AS FECHA_MOD,
                        s.SOLICITADO_POR,
                        s.RAZON,
                        s.DIRIGIDO_A,
                        (CASE 
                            WHEN s.REACTIVADA = 'Y' THEN 
                                (SELECT COUNT(*) FROM ROY_CANDIDATOS_SOLICITUD c 
                                WHERE c.ID_SOLICITUD = s.ID_SOLICITUD 
                                AND c.REACTIVADO_POST_CONTRATACION = 'Y' 
                                AND c.ACTIVO = 'Y')
                            ELSE 
                                (SELECT COUNT(*) FROM ROY_CANDIDATOS_SOLICITUD c 
                                WHERE c.ID_SOLICITUD = s.ID_SOLICITUD AND c.ACTIVO = 'Y')
                        END) as TOTAL_CANDIDATOS,
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
                END AS CVS_DISP,
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
                ) AS TIENE_SEL,
                (
                    SELECT CASE
                        WHEN COUNT(*) > 0 THEN 1 ELSE 0
                    END
                    FROM ROY_OBSERVACIONES_DIA_PRUEBA obs
                    WHERE obs.ID_SOLICITUD = s.ID_SOLICITUD
                    AND obs.ID_HIST_ASOCIADO = (
                        SELECT MAX(h2.ID_HISTORICO)
                        FROM ROY_HISTORICO_SOLICITUD h2
                        WHERE h2.ID_SOLICITUD = s.ID_SOLICITUD
                        AND (LOWER(h2.ESTADO_NUEVO) LIKE '%día de prueba%' 
                             OR LOWER(h2.ESTADO_NUEVO) LIKE '%dia de prueba%')
                    )
                ) AS TIENE_OBS,
                h.ID_HISTORICO,
                h.COMENTARIO_NUEVO,
                h.COMENTARIO_ANTERIOR,
                (
                    SELECT COUNT(*) 
                    FROM ROY_CHAT_HISTORICO ch 
                    WHERE ch.ID_HISTORICO = h.ID_HISTORICO
                ) AS TOTAL_MSG,
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
                    WHERE h.COMENTARIO_NUEVO IS NOT NULL 
                    OR EXISTS (SELECT 1 FROM ROY_CHAT_HISTORICO c WHERE c.ID_HISTORICO = h.ID_HISTORICO)
                )
                WHERE rn = 1
            ) h ON s.ID_SOLICITUD = h.ID_SOLICITUD
        ";

        if ($es_supervisor) {
            // Query para supervisores (solo sus propias solicitudes)
            $query = "SELECT * FROM ($baseQuery) A
                      INNER JOIN (
                        SELECT store_no, udf1_string, udf2_string 
                        FROM RPS.STORE 
                        WHERE sbs_sid = '680861302000159257' 
                      ) sp ON A.SOLICITADO_POR = sp.udf2_string AND A.NUM_TIENDA = sp.store_no
                      WHERE sp.udf1_string = :usuario_logueado
                      ORDER BY FECHA_SOL DESC";
                      
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':usuario_logueado', $usuario_logueado);
            
        } elseif ($es_gerente) {
            // Query para gerentes (solo supervisores de su región)
            $nombre_gerente = $gerente_nombres[$usuario_logueado];
            
            $query = "SELECT * FROM ($baseQuery) A
                      INNER JOIN (
                        SELECT store_no, udf1_string, udf2_string, udf4_string
                        FROM RPS.STORE 
                        WHERE sbs_sid = '680861302000159257' 
                      ) sp ON A.SOLICITADO_POR = sp.udf2_string AND A.NUM_TIENDA = sp.store_no
                      WHERE UPPER(TRIM(sp.udf4_string)) = UPPER(TRIM(:nombre_gerente))
                      ORDER BY FECHA_SOL DESC";
                      
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':nombre_gerente', $nombre_gerente);
            
        } else {
            // Query general para otros usuarios (como RH)
            $query = "$baseQuery ORDER BY s.FECHA_SOLICITUD DESC";
            $stmt = oci_parse($conn, $query);
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
                'COMENTARIO_SOLICITUD' => $row['COMENTARIO_SOLICITUD'],
                'ESTADO_APROBACION' => $row['ESTADO_APROBACION'] ?: 'Por Aprobar',
                'DIRIGIDO_RH' => $row['DIRIGIDO_RH'],
                'FECHA_SOLICITUD' => $row['FECHA_SOL'],
                'FECHA_MODIFICACION' => $row['FECHA_MOD'],
                'SOLICITADO_POR' => $row['SOLICITADO_POR'],
                'RAZON' => $row['RAZON'],
                'DIRIGIDO_A' => $row['DIRIGIDO_A'],
                'TIENE_ARCHIVOS' => $row['TIENE_ARCHIVOS'],
                'CVS_DISPONIBLES' => $row['CVS_DISP'],
                'ID_HISTORICO' => $row['ID_HISTORICO'],
                'COMENTARIO_NUEVO' => $row['COMENTARIO_NUEVO'],
                'TIENE_SELECCION' => $row['TIENE_SEL'],
                'TIENE_OBSERVACIONES_DIA_PRUEBA' => $row['TIENE_OBS'],
                'NO_LEIDOS' => $row['NO_LEIDOS'],
                'TOTAL_CANDIDATOS' => intval($row['TOTAL_CANDIDATOS']) 
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


    //ver comentario de aprobacion
case 'get_resultado_aprobacion':
    try {
        $id_solicitud = $_POST['id_solicitud'] ?? null;
        
        if (!$id_solicitud) {
            throw new Exception('ID de solicitud no proporcionado');
        }
        
        // 📋 CONSULTA ORACLE CON NOMBRES CORRECTOS DE COLUMNAS
        $sql = "SELECT 
                    s.ID_SOLICITUD,
                    s.NUM_TIENDA,
                    s.PUESTO_SOLICITADO,
                    TO_CHAR(s.FECHA_SOLICITUD, 'DD/MM/YYYY') as FECHA_SOLICITUD,
                    s.RAZON,
                    s.ESTADO_APROBACION,
                    s.SOLICITADO_POR,
                    s.DIRIGIDO_A,
                    TO_CHAR(s.FECHA_MODIFICACION, 'DD/MM/YYYY HH24:MI') as FECHA_MODIFICACION,
                    
                    -- Información de aprobación/rechazo con nombres correctos
                    a.COMENTARIO_GERENTE as COMENTARIO_RECHAZO,
                    a.GERENTE as GERENTE_DECISION,
                    a.CODIGO_GERENTE,
                    TO_CHAR(a.FECHA_DECISION, 'DD/MM/YYYY HH24:MI') as FECHA_RECHAZO
                    
                FROM ROY_SOLICITUD_PERSONAL s
                LEFT JOIN ROY_APROBACIONES_GERENCIA a ON s.ID_SOLICITUD = a.ID_SOLICITUD
                WHERE s.ID_SOLICITUD = :id_solicitud";
        
        $stmt = oci_parse($conn, $sql);
        if (!$stmt) {
            $error = oci_error($conn);
            throw new Exception('Error preparando consulta: ' . $error['message']);
        }
        
        oci_bind_by_name($stmt, ':id_solicitud', $id_solicitud);
        
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception('Error ejecutando consulta: ' . $error['message']);
        }
        
        $datos = oci_fetch_assoc($stmt);
        
        if (!$datos) {
            throw new Exception('No se encontró la solicitud especificada');
        }
        
        // 🔄 PROCESAR CLOB SI ES NECESARIO
        if (isset($datos['COMENTARIO_RECHAZO']) && is_object($datos['COMENTARIO_RECHAZO'])) {
            $datos['COMENTARIO_RECHAZO'] = $datos['COMENTARIO_RECHAZO']->load();
        }
        
        // ✅ RESPUESTA EXITOSA
        echo json_encode([
            'success' => true,
            'data' => $datos,
            'message' => 'Información obtenida correctamente'
        ]);
        
        oci_free_statement($stmt);
        
    } catch (Exception $e) {
        // ❌ ERROR EN LA CONSULTA
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

                                 // CASE PARA OBTENER RESULTADO DEL AVAL
case 'get_info_aval_completa_supervisor':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato requerido');
        }
        
        error_log("=== DEBUG SUPERVISOR - ID Candidato: $id_candidato ===");
        
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
        
        error_log("DEBUG SUPERVISOR - Candidato encontrado: " . $candidato['NOMBRE_CANDIDATO'] . " " . $candidato['APELLIDOS_CANDIDATO']);
        
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
        $nombreGerente = 'Gerente de Operaciones';
        error_log("DEBUG SUPERVISOR - Nombre inicial del gerente: $nombreGerente");
        
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
            
            error_log("DEBUG SUPERVISOR - Total registros historial: " . count($debug_registros));
            error_log("DEBUG SUPERVISOR - Registros completos: " . print_r($debug_registros, true));
        } else {
            $error = oci_error($stmtDebugHistorial);
            error_log("DEBUG SUPERVISOR - Error en consulta historial: " . print_r($error, true));
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
                error_log("DEBUG SUPERVISOR - Info gerente encontrada - Usuario: " . ($infoGerente['USUARIO_CAMBIO'] ?? 'NULL'));
                
                // PASO 1: Intentar extraer nombre de las observaciones
                if (!empty($infoGerente['OBSERVACIONES'])) {
                    $observaciones = $infoGerente['OBSERVACIONES'];
                    
                    if (is_object($observaciones)) {
                        $observaciones = $observaciones->load();
                    }
                    
                    error_log("DEBUG SUPERVISOR - Observaciones a procesar: " . $observaciones);
                    
                    // Patrones mejorados para extraer el nombre del gerente
                    $patronesGerente = [
                        // Patrón específico para "Decisión del gerente [NOMBRE]:"
                        '/Decisi[oó\?]n del gerente\s+([A-Z][a-z]+\s+[A-Z][a-z]+)\s*:/iu',
                        
                        // Patrón alternativo sin acentos
                        '/Decisi.{1,3}n del gerente\s+([A-Z][a-z]+\s+[A-Z][a-z]+)\s*:/iu',
                        
                        // Capturar nombre antes de ": APROBADO" o ": NO APROBADO"
                        '/gerente\s+([A-Z][a-z]+\s+[A-Z][a-z]+)\s*:\s*(?:APROBADO|NO APROBADO)/iu',
                        
                        // Capturar dos palabras capitalizadas después de "gerente"
                        '/gerente\s+([A-Z][a-zA-Z]+\s+[A-Z][a-zA-Z]+)/u',
                        
                        // Backup: capturar cualquier texto entre "gerente" y ":"
                        '/gerente\s+(.+?):/iu',
                    ];
                    
                    foreach ($patronesGerente as $index => $patron) {
                        error_log("DEBUG SUPERVISOR - Probando patrón $index: $patron");
                        if (preg_match($patron, $observaciones, $matches)) {
                            error_log("DEBUG SUPERVISOR - Patrón $index COINCIDIÓ: " . print_r($matches, true));
                            $nombreExtraido = trim($matches[1]);
                            if (!empty($nombreExtraido) && strlen($nombreExtraido) > 2) {
                                $nombreGerente = $nombreExtraido;
                                error_log("DEBUG SUPERVISOR - NOMBRE EXTRAÍDO: $nombreGerente");
                                break;
                            }
                        }
                    }
                }
                
                // PASO 2: Si se extrajo un nombre, validarlo
                if ($nombreGerente !== 'Gerente de Operaciones') {
                    error_log("DEBUG SUPERVISOR - Validando nombre extraído: $nombreGerente");
                    
                    $gerente_nombres = [
                        '5333' => 'Christian Quan', 
                        '5210' => 'Giovanni Cardoza'
                    ];
                    
                    foreach ($gerente_nombres as $codigo => $nombre) {
                        if (stripos($nombre, $nombreGerente) !== false || stripos($nombreGerente, $nombre) !== false) {
                            $nombreGerente = $nombre;
                            error_log("DEBUG SUPERVISOR - Nombre validado: $nombreGerente");
                            break;
                        }
                    }
                }
            } else {
                error_log("DEBUG SUPERVISOR - NO se encontró información del gerente");
            }
        } else {
            $error = oci_error($stmtGerente);
            error_log("DEBUG SUPERVISOR - Error en consulta: " . print_r($error, true));
        }
        oci_free_statement($stmtGerente);
        
        error_log("DEBUG SUPERVISOR - NOMBRE GERENTE FINAL: '$nombreGerente'");
        
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
        
        error_log("DEBUG SUPERVISOR - Array formateado con NOMBRE_GERENTE: " . $candidatoFormateado['NOMBRE_GERENTE']);
        
        echo json_encode([
            'success' => true,
            'candidato' => $candidatoFormateado
        ]);
        
        error_log("=== DEBUG SUPERVISOR FIN ===");
        
    } catch (Exception $e) {
        error_log("ERROR SUPERVISOR: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;
    //================================================================
    // CASE PARA VER A LOS CANDIDATOS ASOCIADOS A LA SOLICITUD 
    //================================================================
    //PERMISOS DE SUBIDA SEGUN VISTA - SUPERVISORES SOLO LECTURA
case 'get_permisos_subida_candidato_supervisor':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        $rol_usuario = $_GET['rol_usuario'] ?? 'SUPERVISOR';
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato requerido');
        }
        
        // MODIFICACIÓN: Incluir MOTIVO_DESCARTE en la consulta
        $queryCandidato = "SELECT 
                            c.ESTADO_CANDIDATO, 
                            c.MOTIVO_DESCARTE,
                            c.ACTIVO,
                            s.PUESTO_SOLICITADO, 
                            s.DIRIGIDO_RH
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
        
        //CONSULTA PARA OBTENER QUIÉN DESCARTÓ AL CANDIDATO
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
                                            WHEN h.OBSERVACIONES LIKE '%RECURSOS HUMANOS:%' THEN 
                                                TRIM(REGEXP_SUBSTR(h.OBSERVACIONES, 'RECURSOS HUMANOS: ([^-]+)', 1, 1, NULL, 1))
                                            WHEN h.OBSERVACIONES LIKE '%GERENTE%' THEN 'Gerente (sin nombre)'
                                            WHEN h.OBSERVACIONES LIKE '%SUPERVISOR%' THEN 'Supervisor (sin nombre)'
                                            WHEN h.OBSERVACIONES LIKE '%RRHH%' THEN 'RRHH (sin nombre)'
                                            WHEN h.OBSERVACIONES LIKE '%RECURSOS HUMANOS%' THEN 'Recursos Humanos (sin nombre)'
                                            ELSE 'Usuario no identificado'
                                        END as NOMBRE_QUIEN_DESCARTO,
                                        CASE 
                                            WHEN h.OBSERVACIONES LIKE '%GERENTE%' THEN 'GERENTE'
                                            WHEN h.OBSERVACIONES LIKE '%SUPERVISOR%' THEN 'SUPERVISOR'
                                            WHEN h.OBSERVACIONES LIKE '%RRHH%' THEN 'RRHH'
                                            WHEN h.OBSERVACIONES LIKE '%RECURSOS HUMANOS%' THEN 'RRHH'
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



        // Procesar MOTIVO_DESCARTE si es un objeto CLOB
        $motivo_descarte = '';
        if ($dataCandidato['MOTIVO_DESCARTE']) {
            if (is_object($dataCandidato['MOTIVO_DESCARTE'])) {
                $motivo_descarte = $dataCandidato['MOTIVO_DESCARTE']->load();
            } else {
                $motivo_descarte = $dataCandidato['MOTIVO_DESCARTE'];
            }
        }
        
        // OBTENER EL ESTADO ACTUAL DEL CANDIDATO
        $estadoActualCandidato = trim($dataCandidato['ESTADO_CANDIDATO']);
        $nombreAsesoraRH = trim($dataCandidato['DIRIGIDO_RH']) ?: 'la asesora de Recursos Humanos';
        $esDescartado = $estadoActualCandidato === 'Descartado' || $dataCandidato['ACTIVO'] === 'N';
        
        // Verificar si es jefe de tienda
        $es_jefe = stripos($dataCandidato['PUESTO_SOLICITADO'], 'JEFE') !== false;
        
        // 🎯 DEFINIR FLUJO PROGRESIVO DE ESTADOS
        $flujoEstados = [
            ['nombre' => 'CV Enviado', 'orden' => 1],
            ['nombre' => 'Psicometrica', 'orden' => 2],
            ['nombre' => 'Entrevista RH', 'orden' => 3],
            ['nombre' => 'Entrevista Tecnica', 'orden' => 4],
            ['nombre' => 'Dia de Prueba', 'orden' => 5],
            ['nombre' => 'Poligrafo', 'orden' => 6]  // Solo si es jefe
        ];
        
        // 🎯 DETERMINAR POSICIÓN ACTUAL DEL CANDIDATO
        $posicionActual = 0;
        foreach ($flujoEstados as $estado) {
            // Comparación flexible para el estado actual
            $esEstadoActual = (
                $estadoActualCandidato === $estado['nombre'] ||
                // Para Entrevista Técnica
                ($estado['nombre'] === 'Entrevista Tecnica' && (
                    stripos($estadoActualCandidato, 'Entrevista Tecnica') !== false ||
                    stripos($estadoActualCandidato, 'Entrevista Técnica') !== false
                )) ||
                // Para Día de Prueba
                ($estado['nombre'] === 'Dia de Prueba' && (
                    stripos($estadoActualCandidato, 'Dia de Prueba') !== false ||
                    stripos($estadoActualCandidato, 'Día de Prueba') !== false
                ))
            );
            
            if ($esEstadoActual) {
                $posicionActual = $estado['orden'];
                break;
            }
        }
        
        // Si no encontramos coincidencia exacta, asumir que está en CV Enviado
        if ($posicionActual === 0) {
            $posicionActual = 1;
        }
        
        // 🎯 CONFIGURAR PERMISOS POR CARPETA
        $estadosSubidaSupervisores = ['Entrevista Tecnica', 'Dia de Prueba'];
        $carpetasPermitidas = [];
        
        foreach ($flujoEstados as $estado) {
            $nombreEstado = $estado['nombre'];
            $posicionCarpeta = $estado['orden'];
            
            // Saltar Polígrafo si no es jefe
            if ($nombreEstado === 'Poligrafo' && !$es_jefe) {
                continue;
            }
            
            // Verificar si ya hay archivos subidos para este estado
            $queryArchivos = "SELECT COUNT(*) as CANTIDAD FROM ROY_ARCHIVOS_SOLICITUD 
                             WHERE ID_CANDIDATO = :id AND ESTADO_RELACIONADO = :estado";
            $stmtArchivos = oci_parse($conn, $queryArchivos);
            oci_bind_by_name($stmtArchivos, ':id', $id_candidato);
            oci_bind_by_name($stmtArchivos, ':estado', $nombreEstado);
            oci_execute($stmtArchivos);
            $rowArchivos = oci_fetch_assoc($stmtArchivos);
            $yaSubioArchivos = intval($rowArchivos['CANTIDAD']) > 0;
            oci_free_statement($stmtArchivos);
            
            $puedeSubir = false;
            $motivoBloqueo = null;
            
            // Si el candidato está descartado, no puede subir nada
            if ($esDescartado) {
                $puedeSubir = false;
                $motivoBloqueo = 'Candidato descartado';
            } else {
                // Lógica normal para candidatos activos
                $candidatoAlcanzoEsteEstado = ($posicionActual >= $posicionCarpeta);
                $esCarpetaPermitidaParaSupervisores = in_array($nombreEstado, $estadosSubidaSupervisores);
                
                if ($candidatoAlcanzoEsteEstado && $esCarpetaPermitidaParaSupervisores) {
                    // ✅ Puede subir si no hay archivos ya subidos
                    $puedeSubir = !$yaSubioArchivos;
                    $motivoBloqueo = $yaSubioArchivos ? 'Ya se subieron archivos para este estado' : null;
                } elseif (!$candidatoAlcanzoEsteEstado) {
                    // ❌ Candidato no ha llegado a este estado
                    $puedeSubir = false;
                    $motivoBloqueo = "El candidato aún no ha llegado a este estado";
                } elseif (!$esCarpetaPermitidaParaSupervisores) {
                    // ❌ Estado no permitido para supervisores
                    $puedeSubir = false;
                    if (!$yaSubioArchivos) {
                        $motivoBloqueo = "La asesora de RH {$nombreAsesoraRH} aún no ha subido los archivos correspondientes";
                    } else {
                        $motivoBloqueo = 'Solo RH puede subir archivos en este estado';
                    }
                } else {
                    // ❌ Caso por defecto
                    $puedeSubir = false;
                    $motivoBloqueo = 'Sin permisos para este estado';
                }
            }
            
            $carpetasPermitidas[] = [
                'nombre_estado' => $nombreEstado,
                'puede_subir' => $puedeSubir,
                'puede_ver' => true,    // SUPERVISORES SIEMPRE PUEDEN VER
                'ya_tiene_archivos' => $yaSubioArchivos,
                'motivo_bloqueo' => $motivoBloqueo,
                'estado_actual_candidato' => $estadoActualCandidato,
                'asesora_rh' => $nombreAsesoraRH,
                // 🔍 DEBUG INFO
                'debug_info' => [
                    'posicion_actual' => $posicionActual,
                    'posicion_carpeta' => $posicionCarpeta,
                    'candidato_alcanzo' => $candidatoAlcanzoEsteEstado ?? false,
                    'es_permitida_supervisor' => $esCarpetaPermitidaParaSupervisores ?? false
                ]
            ];
        }
        
        echo json_encode([
            'success' => true,
            'rol_usuario' => 'SUPERVISOR',
            'estado_candidato' => $estadoActualCandidato,
            'puesto_solicitado' => $dataCandidato['PUESTO_SOLICITADO'],
            'motivo_descarte' => $motivo_descarte,
            'info_descarte' => $infoDescarte, // ✅ INFORMACIÓN DE QUIÉN DESCARTÓ
            'carpetas' => $carpetasPermitidas,
            'es_jefe' => $es_jefe,
            'asesora_rh' => $nombreAsesoraRH,
            'posicion_actual' => $posicionActual
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;


//================================================================
// NUEVO CASE: DESCARTAR CANDIDATO POR SUPERVISOR 
//================================================================
case 'descartar_candidato_supervisor':
    try {
        $id_candidato = $_POST['id_candidato'] ?? null;
        $motivo_descarte = $_POST['motivo_descarte'] ?? null;

        if (!$id_candidato || !$motivo_descarte) {
            echo json_encode(['success' => false, 'error' => 'ID de candidato y motivo de descarte son requeridos']);
            exit;
        }

        $id_candidato = intval($id_candidato);

        // ✅ OBTENER CÓDIGO DEL SUPERVISOR LOGUEADO (IGUAL QUE EN get_solicitudes)
        $usuario_logueado = $_SESSION['user'][12] ?? null;
        
        if (!$usuario_logueado) {
            echo json_encode(['success' => false, 'error' => 'Usuario no encontrado en sesión']);
            exit;
        }

        // ✅ OBTENER NOMBRE DEL SUPERVISOR DESDE LA TABLA RPS.STORE
        $queryNombreSupervisor = "SELECT udf2_string as NOMBRE_SUPERVISOR
                                  FROM RPS.STORE 
                                  WHERE udf1_string = :codigo_supervisor 
                                  AND sbs_sid = '680861302000159257' 
                                  AND ROWNUM = 1";
        
        $stmtNombre = oci_parse($conn, $queryNombreSupervisor);
        oci_bind_by_name($stmtNombre, ':codigo_supervisor', $usuario_logueado);
        oci_execute($stmtNombre);
        $dataNombre = oci_fetch_assoc($stmtNombre);
        oci_free_statement($stmtNombre);
        
        // ✅ DETERMINAR NOMBRE DEL SUPERVISOR
        if ($dataNombre && !empty($dataNombre['NOMBRE_SUPERVISOR'])) {
            $nombre_supervisor = trim($dataNombre['NOMBRE_SUPERVISOR']);
            error_log("✅ Supervisor identificado: $nombre_supervisor (código: $usuario_logueado)");
        } else {
            // Fallback: usar códigos conocidos o obtener desde solicitud
            $supervisor_nombres_conocidos = [
                '5378' => 'SUPERVISOR_1',
                '5379' => 'SUPERVISOR_2', 
                '6250' => 'SUPERVISOR_3',
                '6006' => 'SUPERVISOR_4',
                '5385' => 'SUPERVISOR_5',
                '5287' => 'SUPERVISOR_6',
                '5400' => 'SUPERVISOR_7',
                '5226' => 'SUPERVISOR_8',
                '5139' => 'SUPERVISOR_9',
                '5142' => 'SUPERVISOR_10'
            ];
            
            if (isset($supervisor_nombres_conocidos[$usuario_logueado])) {
                $nombre_supervisor = $supervisor_nombres_conocidos[$usuario_logueado];
            } else {
                // Último recurso: obtener desde la solicitud del candidato
                $querySupSolicitud = "SELECT s.SOLICITADO_POR 
                                      FROM ROY_CANDIDATOS_SOLICITUD c
                                      JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                                      WHERE c.ID_CANDIDATO = :id_candidato";
                $stmtSupSol = oci_parse($conn, $querySupSolicitud);
                oci_bind_by_name($stmtSupSol, ':id_candidato', $id_candidato);
                oci_execute($stmtSupSol);
                $dataSupSol = oci_fetch_assoc($stmtSupSol);
                oci_free_statement($stmtSupSol);
                
                $nombre_supervisor = $dataSupSol['SOLICITADO_POR'] ?? 'Supervisor';
            }
            
            error_log("⚠️ Supervisor no encontrado en RPS.STORE, usando fallback: $nombre_supervisor");
        }

        // Obtener datos del candidato
        $queryEstadoActual = "SELECT c.ID_SOLICITUD, c.ESTADO_CANDIDATO, c.NOMBRE_CANDIDATO, c.APELLIDOS_CANDIDATO 
                             FROM ROY_CANDIDATOS_SOLICITUD c 
                             WHERE c.ID_CANDIDATO = :id";
        $stmtEstado = oci_parse($conn, $queryEstadoActual);
        oci_bind_by_name($stmtEstado, ':id', $id_candidato);
        oci_execute($stmtEstado);
        $dataEstado = oci_fetch_assoc($stmtEstado);
        oci_free_statement($stmtEstado);

        if (!$dataEstado) {
            echo json_encode(['success' => false, 'error' => 'Candidato no encontrado']);
            exit;
        }

        $estadoAnterior = $dataEstado['ESTADO_CANDIDATO'];
        $nombreCompleto = trim($dataEstado['NOMBRE_CANDIDATO'] . ' ' . $dataEstado['APELLIDOS_CANDIDATO']);
        $idSolicitud = intval($dataEstado['ID_SOLICITUD']);

        // CREAR OBSERVACIÓN CON NOMBRE CORRECTO DEL SUPERVISOR
        $observacionDescarte = "CANDIDATO DESCARTADO POR SUPERVISOR: {$nombre_supervisor} - Motivo: " . $motivo_descarte;
        
        $queryHistDescarte = "INSERT INTO ROY_CANDIDATOS_HIST_EST 
                             (ID_CANDIDATO, ESTADO_ANTERIOR, ESTADO_NUEVO, OBSERVACIONES, FECHA_CAMBIO, ACTIVO)
                             VALUES (:id_candidato, :estado_anterior, 'Descartado', :observaciones, SYSDATE, 'Y')";
        
        $stmtHistDescarte = oci_parse($conn, $queryHistDescarte);
        oci_bind_by_name($stmtHistDescarte, ':id_candidato', $id_candidato);
        oci_bind_by_name($stmtHistDescarte, ':estado_anterior', $estadoAnterior);
        oci_bind_by_name($stmtHistDescarte, ':observaciones', $observacionDescarte);
        oci_execute($stmtHistDescarte);
        oci_free_statement($stmtHistDescarte);

        // Insertar en historial general
        /*$comentarioGeneral = "Candidato {$nombreCompleto} descartado por SUPERVISOR: {$nombre_supervisor} - Motivo: {$motivo_descarte}";
        
        $queryHistGeneral = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                            (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, FECHA_CAMBIO, TIPO_EVENTO) 
                            VALUES (:id_solicitud, :estado_anterior, 'Descartado', SYSDATE, 'CANDIDATO_DESCARTADO_SUPERVISOR')";
        
        $stmtHistGeneral = oci_parse($conn, $queryHistGeneral);
        oci_bind_by_name($stmtHistGeneral, ':id_solicitud', $idSolicitud);
        oci_bind_by_name($stmtHistGeneral, ':estado_anterior', $estadoAnterior);
        oci_execute($stmtHistGeneral);
        oci_free_statement($stmtHistGeneral);*/

        // Actualizar candidato a descartado
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
            'message' => 'Candidato descartado correctamente por supervisión',
            'candidato' => $nombreCompleto,
            'descartado_por' => $nombre_supervisor // ✅ INFORMACIÓN ADICIONAL
        ]);

    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

    // MOTIVO DEL DESCARTE 
    case 'get_motivo_descarte':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        
        if (!$id_candidato) {
            echo json_encode(['success' => false, 'error' => 'ID de candidato requerido']);
            exit;
        }
        
        $query = "SELECT MOTIVO_DESCARTE FROM ROY_CANDIDATOS_SOLICITUD WHERE ID_CANDIDATO = :id";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id', $id_candidato);
        oci_execute($stmt);
        $result = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        echo json_encode([
            'success' => true,
            'motivo_descarte' => $result['MOTIVO_DESCARTE'] ?? 'No especificado'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ================================================================
// 🆕 NUEVO CASE: OBTENER ID_SOLICITUD POR ID_CANDIDATO
// ================================================================
case 'get_solicitud_by_candidato':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato no proporcionado');
        }
        
        // Consulta para obtener el ID_SOLICITUD del candidato
        $query = "SELECT 
                    c.ID_SOLICITUD,
                    c.ID_CANDIDATO,
                    c.NOMBRE_CANDIDATO,
                    s.NUM_TIENDA,
                    s.PUESTO_SOLICITADO
                  FROM ROY_CANDIDATOS_SOLICITUD c
                  INNER JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                  WHERE c.ID_CANDIDATO = :id_candidato 
                  AND c.ACTIVO = 'Y'";
        
        $stmt = oci_parse($conn, $query);
        if (!$stmt) {
            $error = oci_error($conn);
            throw new Exception('Error preparando consulta: ' . $error['message']);
        }
        
        oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
        
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception('Error ejecutando consulta: ' . $error['message']);
        }
        
        $resultado = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        if (!$resultado) {
            throw new Exception('No se encontró el candidato especificado');
        }
        
        echo json_encode([
            'success' => true,
            'id_solicitud' => $resultado['ID_SOLICITUD'],
            'id_candidato' => $resultado['ID_CANDIDATO'],
            'nombre_candidato' => $resultado['NOMBRE_CANDIDATO'],
            'num_tienda' => $resultado['NUM_TIENDA'],
            'puesto_solicitado' => $resultado['PUESTO_SOLICITADO']
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    break;

    //================================================================
    // CASEA PARA VER LOS ARCHIVOS DEL ESTADO DE LOS CANDIDATOS
    //================================================================
case 'get_candidatos_por_solicitud_supervisor':
    try {
        $id_solicitud = $_GET['id_solicitud'] ?? null;
        
        if (!$id_solicitud) {
            throw new Exception('ID de solicitud requerido');
        }
        
        // 1. OBTENER INFO DE LA SOLICITUD
        $query_solicitud = "SELECT 
            ESTADO_SOLICITUD,
            REACTIVADA,
            MOTIVO_REACTIVACION,
            PUESTO_SOLICITADO,
            NUM_TIENDA,
            SOLICITADO_POR
        FROM ROY_SOLICITUD_PERSONAL 
        WHERE ID_SOLICITUD = :id";
        
        $stmt_solicitud = oci_parse($conn, $query_solicitud);
        oci_bind_by_name($stmt_solicitud, ':id', $id_solicitud);
        oci_execute($stmt_solicitud);
        $solicitud = oci_fetch_assoc($stmt_solicitud);
        oci_free_statement($stmt_solicitud);
        
        if (!$solicitud) {
            throw new Exception('Solicitud no encontrada');
        }
        
        $es_reactivada = ($solicitud['REACTIVADA'] === 'Y');
        $es_plaza_cubierta = (strtolower(trim($solicitud['ESTADO_SOLICITUD'])) === 'plaza cubierta');

        // ============================================================================
        // CASO 1: SOLICITUD EN PROCESO DE REACTIVACIÓN (REACTIVADA = 'Y')
        // ============================================================================
        if ($es_reactivada) {
            // NO mostrar candidatos, solo mensaje de espera
            echo json_encode([
                'success' => true,
                'candidatos' => [],
                'total_candidatos' => 0,
                'esperando_rh' => true,
                'solicitud' => [
                    'reactivada' => 'Y',
                    'motivo_reactivacion' => $solicitud['MOTIVO_REACTIVACION'],
                    'num_tienda' => $solicitud['NUM_TIENDA'],
                    'puesto_solicitado' => $solicitud['PUESTO_SOLICITADO'],
                    'supervisor' => $solicitud['SOLICITADO_POR']
                ]
            ]);
            exit;
        }

        // ============================================================================
        // CASO 2 Y 3: SOLICITUD NORMAL O CON REACTIVACIÓN CONFIRMADA
        // ============================================================================
        
        $whereClause = "WHERE c.ID_SOLICITUD = :id_solicitud";
        
        if ($es_plaza_cubierta) {
            // Plaza cubierta: Solo mostrar contratado activo
            $whereClause .= " AND c.ESTADO_CANDIDATO = 'Contratado' AND c.ACTIVO = 'Y'";
        } else {
            // Verificar si hay candidatos reactivados post-contratación
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
                // HAY REACTIVADOS: Mostrar SOLO los candidatos reactivados (ocultar contratado)
                $whereClause .= " AND c.REACTIVADO_POST_CONTRATACION = 'Y'";
            }
        }

        // QUERY PRINCIPAL - EXACTO AL DE GERENTES
        $query = "SELECT 
                    c.ID_CANDIDATO,
                    c.NOMBRE_CANDIDATO,
                    c.APELLIDOS_CANDIDATO,
                    c.ESTADO_CANDIDATO,
                    c.ACTIVO,
                    c.MOTIVO_DESCARTE,
                    c.FECHA_REGISTRO,
                    c.REACTIVADO_POST_CONTRATACION,
                    
                    (SELECT COUNT(*) 
                    FROM ROY_ARCHIVOS_SOLICITUD a 
                    WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) AS TOTAL_ARCHIVOS
                    
                FROM ROY_CANDIDATOS_SOLICITUD c
                $whereClause
                ORDER BY c.FECHA_REGISTRO DESC";

        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_solicitud', $id_solicitud);
        oci_execute($stmt);

        $candidatos = [];
        
        while ($row = oci_fetch_assoc($stmt)) {
            $motivo_descarte = '';
            if ($row['MOTIVO_DESCARTE']) {
                if (is_object($row['MOTIVO_DESCARTE'])) {
                    $motivo_descarte = $row['MOTIVO_DESCARTE']->load();
                } else {
                    $motivo_descarte = $row['MOTIVO_DESCARTE'];
                }
            }

            $candidatos[] = [
                'ID_CANDIDATO' => $row['ID_CANDIDATO'],
                'NOMBRE_CANDIDATO' => $row['NOMBRE_CANDIDATO'],
                'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                'ESTADO_CANDIDATO' => $row['ESTADO_CANDIDATO'],
                'ACTIVO' => $row['ACTIVO'],
                'MOTIVO_DESCARTE' => $motivo_descarte,
                'FECHA_REGISTRO' => $row['FECHA_REGISTRO'],
                'REACTIVADO_POST_CONTRATACION' => $row['REACTIVADO_POST_CONTRATACION'] ?? 'N',
                'TOTAL_ARCHIVOS' => intval($row['TOTAL_ARCHIVOS'] ?? 0),
                'ULTIMO_ESTADO_ALCANZADO' => $row['ESTADO_CANDIDATO']
            ];
        }
        oci_free_statement($stmt);

        echo json_encode([
            'success' => true,
            'candidatos' => $candidatos,
            'total_candidatos' => count($candidatos),
            'esperando_rh' => false,
            'solicitud' => [
                'reactivada' => 'N',
                'num_tienda' => $solicitud['NUM_TIENDA'],
                'puesto_solicitado' => $solicitud['PUESTO_SOLICITADO'],
                'supervisor' => $solicitud['SOLICITADO_POR']
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;



// OBTENER ARCHIVOS DE CANDIDATO PARA SUPERVISORES
case 'get_archivos_candidato':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        $estado_relacionado = $_GET['estado_relacionado'] ?? null;
        
        if (!$id_candidato || !$estado_relacionado) {
            throw new Exception('ID de candidato y estado relacionado son requeridos');
        }
        
            $query = "SELECT 
                        a.ID_ARCHIVO,
                        a.NOMBRE_ARCHIVO,
                        a.FECHA_SUBIDA,
                        a.SUBIDO_POR_ROL
                    FROM ROY_ARCHIVOS_SOLICITUD a
                  WHERE a.ID_CANDIDATO = :id_candidato 
                  AND a.ESTADO_RELACIONADO = :estado_relacionado
                  ORDER BY a.FECHA_SUBIDA DESC";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
        oci_bind_by_name($stmt, ':estado_relacionado', $estado_relacionado);
        oci_execute($stmt);
        
        $archivos = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $archivos[] = $row;
        }
        oci_free_statement($stmt);
        
        echo json_encode([
            'success' => true,
            'archivos' => $archivos
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;
//================================================================
// DESCARGAR Y VER ARCHIVO DEL CANDIDATO
//================================================================
case 'ver_archivo':
    $nombre_archivo = $_GET['archivo'] ?? '';
    if (empty($nombre_archivo)) {
        http_response_code(404);
        echo "Archivo no especificado";
        exit;
    }
    
    // Ruta ajustada para supervisores - apunta a la carpeta de gestionhumana
    $rutaArchivo = __DIR__ . "/../gestionhumana/archivos_candidatos/" . $nombre_archivo;
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
    break;

case 'descargar_archivo':
    $nombre_archivo = $_GET['archivo'] ?? '';
    if (empty($nombre_archivo)) {
        http_response_code(404);
        echo "Archivo no especificado";
        exit;
    }
    
    // Ruta ajustada para supervisores - apunta a la carpeta de gestionhumana  
    $rutaArchivo = __DIR__ . "/../gestionhumana/archivos_candidatos/" . $nombre_archivo;
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
    break;

    //================================================================
    // TOTAL CANDIDATOS POR SOLICITUD 
    //================================================================
    // OBTENER TOTAL DE CANDIDATOS - COPIADO DE RH
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
//================================================================
//CASE PARA REACTIVACION DEL CANDIDATO 
//================================================================
case 'reactivar_solicitud_supervisor':
    try {
        $id_solicitud = $_POST['id_solicitud'] ?? null;
        $motivo_reactivacion = $_POST['motivo_reactivacion'] ?? null;
        
        if (!$id_solicitud || !$motivo_reactivacion) {
            throw new Exception('ID de solicitud y motivo son requeridos');
        }
        
        if (strlen(trim($motivo_reactivacion)) < 10) {
            throw new Exception('El motivo debe tener al menos 10 caracteres');
        }
        
        // Verificar que la solicitud esté en "Plaza Cubierta"
        $query_verificar = "SELECT ESTADO_SOLICITUD FROM ROY_SOLICITUD_PERSONAL WHERE ID_SOLICITUD = :id";
        $stmt_verificar = oci_parse($conn, $query_verificar);
        oci_bind_by_name($stmt_verificar, ':id', $id_solicitud);
        oci_execute($stmt_verificar);
        $solicitud = oci_fetch_assoc($stmt_verificar);
        oci_free_statement($stmt_verificar);
        
        if (!$solicitud) {
            throw new Exception('Solicitud no encontrada');
        }
        
        if (strtolower(trim($solicitud['ESTADO_SOLICITUD'])) !== 'plaza cubierta') {
            throw new Exception('Solo se pueden reactivar solicitudes en estado "Plaza Cubierta"');
        }
        
        // ✅ OBTENER NOMBRE DEL SUPERVISOR
        $usuario_logueado = $_SESSION['user'][12] ?? null;
        
        if (!$usuario_logueado) {
            throw new Exception('Usuario supervisor no encontrado en sesión');
        }

        // Obtener nombre del supervisor desde RPS.STORE
        $queryNombreSupervisor = "SELECT udf2_string as NOMBRE_SUPERVISOR
                                  FROM RPS.STORE 
                                  WHERE udf1_string = :codigo_supervisor 
                                  AND sbs_sid = '680861302000159257' 
                                  AND ROWNUM = 1";
        
        $stmtNombre = oci_parse($conn, $queryNombreSupervisor);
        oci_bind_by_name($stmtNombre, ':codigo_supervisor', $usuario_logueado);
        oci_execute($stmtNombre);
        $dataNombre = oci_fetch_assoc($stmtNombre);
        oci_free_statement($stmtNombre);
        
        $nombre_supervisor = ($dataNombre && !empty($dataNombre['NOMBRE_SUPERVISOR'])) 
            ? trim($dataNombre['NOMBRE_SUPERVISOR']) 
            : 'Supervisor';
        
        // ✅ OBTENER ID DEL CANDIDATO CONTRATADO ANTES DE REACTIVAR
        $query_contratado = "SELECT ID_CANDIDATO, NOMBRE_CANDIDATO, APELLIDOS_CANDIDATO 
                             FROM ROY_CANDIDATOS_SOLICITUD 
                             WHERE ID_SOLICITUD = :id_solicitud 
                             AND ESTADO_CANDIDATO = 'Contratado'
                             AND ROWNUM = 1";
        $stmt_contratado = oci_parse($conn, $query_contratado);
        oci_bind_by_name($stmt_contratado, ':id_solicitud', $id_solicitud);
        oci_execute($stmt_contratado);
        $candidato_contratado = oci_fetch_assoc($stmt_contratado);
        oci_free_statement($stmt_contratado);
        
        $id_candidato_anterior = $candidato_contratado ? $candidato_contratado['ID_CANDIDATO'] : null;
        $nombre_candidato_anterior = $candidato_contratado ? 
            trim($candidato_contratado['NOMBRE_CANDIDATO'] . ' ' . $candidato_contratado['APELLIDOS_CANDIDATO']) : 
            'Sin candidato contratado';
        
        // ✅ CALCULAR NÚMERO DE REACTIVACIÓN
        $query_count = "SELECT NVL(MAX(NUM_REACTIVACION), 0) + 1 as SIGUIENTE 
                        FROM ROY_HIST_REACTIVACIONES 
                        WHERE ID_SOLICITUD = :id_solicitud";
        $stmt_count = oci_parse($conn, $query_count);
        oci_bind_by_name($stmt_count, ':id_solicitud', $id_solicitud);
        oci_execute($stmt_count);
        $data_count = oci_fetch_assoc($stmt_count);
        $num_reactivacion = $data_count['SIGUIENTE'];
        oci_free_statement($stmt_count);
        
        // ✅ INSERTAR EN HISTORIAL DE REACTIVACIONES
        $query_historial = "INSERT INTO ROY_HIST_REACTIVACIONES (
            ID_REACTIVACION,
            ID_SOLICITUD,
            NUM_REACTIVACION,
            FECHA_REACTIVACION,
            USUARIO_REACTIVO,
            TIPO_USUARIO,
            MOTIVO_REACT,
            ID_CAND_CONTRAT_ANT,
            NOMBRE_CAND_ANT,
            ESTADO_REACT
        ) VALUES (
            SEQ_REACTIVACIONES.NEXTVAL,
            :id_solicitud,
            :num_reactivacion,
            SYSDATE,
            :nombre_supervisor,
            'Supervisor',
            :motivo_reactivacion,
            :id_candidato_anterior,
            :nombre_candidato_anterior,
            'Pendiente'
        ) RETURNING ID_REACTIVACION INTO :id_reactivacion_nueva";
        
        $stmt_historial = oci_parse($conn, $query_historial);
        $id_reactivacion_nueva = null;
        
        oci_bind_by_name($stmt_historial, ':id_solicitud', $id_solicitud);
        oci_bind_by_name($stmt_historial, ':num_reactivacion', $num_reactivacion);
        oci_bind_by_name($stmt_historial, ':nombre_supervisor', $nombre_supervisor);
        oci_bind_by_name($stmt_historial, ':motivo_reactivacion', $motivo_reactivacion);
        oci_bind_by_name($stmt_historial, ':id_candidato_anterior', $id_candidato_anterior);
        oci_bind_by_name($stmt_historial, ':nombre_candidato_anterior', $nombre_candidato_anterior);
        oci_bind_by_name($stmt_historial, ':id_reactivacion_nueva', $id_reactivacion_nueva, -1, OCI_B_INT);
        
        if (!oci_execute($stmt_historial, OCI_NO_AUTO_COMMIT)) {
            $error = oci_error($stmt_historial);
            throw new Exception('Error al crear historial de reactivación: ' . $error['message']);
        }
        oci_free_statement($stmt_historial);
        
        // ✅ ACTUALIZAR LA SOLICITUD
        $query_reactivar = "UPDATE ROY_SOLICITUD_PERSONAL 
                           SET ESTADO_SOLICITUD = 'Candidatos en Seleccion',
                               REACTIVADA = 'Y',
                               MOTIVO_REACTIVACION = :motivo,
                               FECHA_REACTIVACION = SYSDATE,
                               FECHA_MODIFICACION = SYSDATE,
                               ID_REACT_ACTUAL = :id_reactivacion
                           WHERE ID_SOLICITUD = :id_solicitud";
        
        $stmt_reactivar = oci_parse($conn, $query_reactivar);
        oci_bind_by_name($stmt_reactivar, ':motivo', $motivo_reactivacion);
        oci_bind_by_name($stmt_reactivar, ':id_solicitud', $id_solicitud);
        oci_bind_by_name($stmt_reactivar, ':id_reactivacion', $id_reactivacion_nueva);
        
        if (!oci_execute($stmt_reactivar, OCI_NO_AUTO_COMMIT)) {
            $error = oci_error($stmt_reactivar);
            throw new Exception('Error al reactivar solicitud: ' . $error['message']);
        }
        
        oci_free_statement($stmt_reactivar);
        oci_commit($conn);
        
        echo json_encode([
            'success' => true,
            'message' => 'Solicitud reactivada correctamente',
            'numero_reactivacion' => $num_reactivacion,
            'nombre_supervisor' => $nombre_supervisor
        ]);
        
    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

//================================================================
// FIN CASES PARA SUPERVISORES
//================================================================

    }
} else {
    switch ($action) {

case 'subir_archivo_candidato_supervisor':
    try {
        // Limpiar cualquier output previo
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        error_log("=== INICIO SUBIDA ARCHIVO SUPERVISOR ===");
        
        // 1. ✅ OBTENER PARÁMETROS REQUERIDOS
        $id_candidato = $_POST['id_candidato'] ?? null;
        $id_solicitud = $_POST['id_solicitud'] ?? null;
        $estado_relacionado = $_POST['estado_relacionado'] ?? null;
        
        error_log("Parámetros: ID_CANDIDATO=$id_candidato, ID_SOLICITUD=$id_solicitud, ESTADO=$estado_relacionado");
        
        // 2. ✅ VALIDACIONES DE PARÁMETROS
        if (!$id_candidato) {
            throw new Exception('ID de candidato no proporcionado');
        }
        
        if (!$id_solicitud) {
            throw new Exception('ID de solicitud no proporcionado');
        }
        
        if (!$estado_relacionado) {
            throw new Exception('Estado relacionado no proporcionado');
        }
        
        // 3. ✅ VALIDAR ARCHIVO SUBIDO
        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al recibir el archivo. Código: ' . ($_FILES['archivo']['error'] ?? 'N/A'));
        }
        
        $archivo = $_FILES['archivo'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        
        if (!in_array($extension, $extensionesPermitidas)) {
            throw new Exception('Tipo de archivo no permitido. Solo: ' . implode(', ', $extensionesPermitidas));
        }
        
        // 4. ✅ VALIDAR PERMISOS PARA SUPERVISOR
        $estadosPermitidos = ['Entrevista Tecnica', 'Dia de Prueba'];
        if (!in_array($estado_relacionado, $estadosPermitidos)) {
            throw new Exception('Sin permisos para este estado: ' . $estado_relacionado);
        }
        
        // 5. ✅ VERIFICAR QUE NO EXISTAN ARCHIVOS PREVIOS
        $queryExiste = "SELECT COUNT(*) as EXISTE FROM ROY_ARCHIVOS_SOLICITUD 
                       WHERE ID_CANDIDATO = :id_candidato AND ESTADO_RELACIONADO = :estado";
        $stmtExiste = oci_parse($conn, $queryExiste);
        oci_bind_by_name($stmtExiste, ':id_candidato', $id_candidato);
        oci_bind_by_name($stmtExiste, ':estado', $estado_relacionado);
        
        if (!oci_execute($stmtExiste)) {
            $error = oci_error($stmtExiste);
            throw new Exception('Error verificando archivos: ' . $error['message']);
        }
        
        $rowExiste = oci_fetch_assoc($stmtExiste);
        oci_free_statement($stmtExiste);
        
        if ($rowExiste['EXISTE'] > 0) {
            throw new Exception('Ya se subieron archivos para este estado');
        }
        
        // 6. ✅ OBTENER ID_HISTORICO DEL CANDIDATO PARA ESTE ESTADO (OPCIONAL)
        $id_historico = null; // Por defecto NULL
        
        try {
            $queryHistorico = "SELECT ID_HISTORICO 
                              FROM ROY_HISTORICO_SOLICITUD 
                              WHERE ID_SOLICITUD = :id_solicitud 
                              AND TIPO_EVENTO = :estado_relacionado 
                              ORDER BY FECHA_CAMBIO DESC 
                              FETCH FIRST 1 ROWS ONLY";
            
            $stmtHistorico = oci_parse($conn, $queryHistorico);
            oci_bind_by_name($stmtHistorico, ':id_solicitud', $id_solicitud);
            oci_bind_by_name($stmtHistorico, ':estado_relacionado', $estado_relacionado);
            
            if (oci_execute($stmtHistorico)) {
                $rowHistorico = oci_fetch_assoc($stmtHistorico);
                $id_historico = $rowHistorico['ID_HISTORICO'] ?? null;
                oci_free_statement($stmtHistorico);
                
                error_log("ID_HISTORICO " . ($id_historico ? "encontrado: $id_historico" : "no encontrado, usando NULL") . " para estado: $estado_relacionado");
            } else {
                error_log("No se pudo consultar ID_HISTORICO, usando NULL");
            }
        } catch (Exception $e) {
            error_log("Error consultando ID_HISTORICO: " . $e->getMessage() . ", usando NULL");
            $id_historico = null;
        }
        
        // 7. ✅ CREAR DIRECTORIO Y MOVER ARCHIVO
        $rutaDestino = '../gestionhumana/archivos_candidatos/';
        if (!is_dir($rutaDestino)) {
            mkdir($rutaDestino, 0755, true);
        }
        
        $nombreArchivo = $estado_relacionado . "_{$id_solicitud}_{$id_candidato}_" . date('Y-m-d') . '.' . $extension;
        $rutaFinal = $rutaDestino . $nombreArchivo;
        
        if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            throw new Exception('Error al guardar el archivo en el servidor');
        }
        
        // 8. ✅ INSERTAR EN BASE DE DATOS (ESTRUCTURA REAL DE LA TABLA)
        $queryInsert = "INSERT INTO ROY_ARCHIVOS_SOLICITUD 
                       (ID_SOLICITUD, NOMBRE_ARCHIVO, FECHA_SUBIDA, ID_HISTORICO, 
                        TIPO_ARCHIVO, ID_CANDIDATO, ESTADO_RELACIONADO, SUBIDO_POR_ROL) 
                       VALUES (:id_solicitud, :nombre, SYSDATE, :id_historico, 
                               :tipo_archivo, :id_candidato, :estado, 'SUPERVISOR')";
        
        $stmtInsert = oci_parse($conn, $queryInsert);
        
        if (!$stmtInsert) {
            $error = oci_error($conn);
            throw new Exception('Error preparando consulta: ' . $error['message']);
        }
        
        // ✅ BIND DE PARÁMETROS CON ESTRUCTURA REAL INCLUYENDO ID_HISTORICO
        oci_bind_by_name($stmtInsert, ':id_solicitud', $id_solicitud);
        oci_bind_by_name($stmtInsert, ':nombre', $nombreArchivo);
        oci_bind_by_name($stmtInsert, ':id_historico', $id_historico); // ✅ AHORA SÍ INCLUIMOS ID_HISTORICO
        oci_bind_by_name($stmtInsert, ':tipo_archivo', $estado_relacionado); // TIPO_ARCHIVO = estado
        oci_bind_by_name($stmtInsert, ':id_candidato', $id_candidato);
        oci_bind_by_name($stmtInsert, ':estado', $estado_relacionado);
        
        if (!oci_execute($stmtInsert)) {
            $error = oci_error($stmtInsert);
            error_log("❌ Error en INSERT: " . print_r($error, true));
            throw new Exception('Error guardando en base de datos: ' . $error['message']);
        }
        
        oci_free_statement($stmtInsert);
        oci_commit($conn);
        
        error_log("✅ Archivo subido exitosamente: $nombreArchivo" . ($id_historico ? " con ID_HISTORICO: $id_historico" : " con ID_HISTORICO: NULL"));
        
        // 9. ✅ RESPUESTA EXITOSA
        echo json_encode([
            'success' => true,
            'message' => 'Archivo subido exitosamente',
            'nombre_archivo' => $nombreArchivo,
            'estado' => $estado_relacionado,
            'candidato_id' => $id_candidato,
            'solicitud_id' => $id_solicitud,
            'id_historico' => $id_historico
        ]);
        
    } catch (Exception $e) {
        error_log("❌ Error en subida: " . $e->getMessage());
        
        // Asegurar que solo se envíe un JSON
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit; // ✅ IMPORTANTE: Salir para evitar output adicional
    break;

        }
    echo json_encode(['success' => false, 'error' => 'No se especificó ninguna acción']);
    oci_close($conn);
}

?>




