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

if (isset($_GET['action'])) {
    switch ($_GET['action']) {

        //HISTORIAL GENERAL
case 'get_historial':
    $query = "SELECT 
                h.ID_HISTORICO,
                sp.ID_SOLICITUD,
                sp.NUM_TIENDA,
                h.ESTADO_ANTERIOR,
                h.ESTADO_NUEVO,
                h.COMENTARIO_ANTERIOR,
                h.COMENTARIO_NUEVO,
                TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO
              FROM ROY_HISTORICO_SOLICITUD h
              JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD
              ORDER BY h.FECHA_CAMBIO DESC";

    $stmt = oci_parse($conn, $query);
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





// AGREGAR ESTE NUEVO CASE
case 'get_historial_filtrado':
    error_log("📋 Obteniendo historial filtrado para RRHH...");
    
    // Validar parámetros
    if (!isset($_GET['fecha_inicial']) || !isset($_GET['fecha_final'])) {
        echo json_encode(['success' => false, 'error' => 'Faltan parámetros de fecha']);
        break;
    }

    $fechaInicial = $_GET['fecha_inicial'];
    $fechaFinal = $_GET['fecha_final'];
    $incluirAprobaciones = isset($_GET['incluir_aprobaciones']) ? (int)$_GET['incluir_aprobaciones'] : 1;
    $incluirEstados = isset($_GET['incluir_estados']) ? (int)$_GET['incluir_estados'] : 1;

    error_log("📅 Filtros: $fechaInicial - $fechaFinal, Aprobaciones: $incluirAprobaciones, Estados: $incluirEstados");

    // Construir condiciones WHERE dinámicamente
    $condicionesExtra = [];
    
    if (!$incluirAprobaciones && !$incluirEstados) {
        echo json_encode(['success' => false, 'error' => 'Debe incluir al menos un tipo de cambio']);
        break;
    }

    $query = "SELECT 
                h.ID_HISTORICO,
                sp.ID_SOLICITUD,
                sp.NUM_TIENDA,
                h.ESTADO_ANTERIOR,
                h.ESTADO_NUEVO,
                h.APROBACION_ANTERIOR,  -- ← AGREGAR CAMPOS DE APROBACIÓN
                h.APROBACION_NUEVA,     -- ← AGREGAR CAMPOS DE APROBACIÓN
                h.COMENTARIO_ANTERIOR,
                h.COMENTARIO_NUEVO,
                TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                sp.PUESTO_SOLICITADO,
                sp.SOLICITADO_POR
              FROM ROY_HISTORICO_SOLICITUD h
              JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD
              WHERE sp.ESTADO_APROBACION = 'Aprobado'  -- Solo solicitudes aprobadas (filtro RRHH)
                AND TO_DATE(:fecha_inicial, 'YYYY-MM-DD') <= h.FECHA_CAMBIO
                AND h.FECHA_CAMBIO <= TO_DATE(:fecha_final, 'YYYY-MM-DD') + INTERVAL '1' DAY - INTERVAL '1' SECOND";

    // Agregar filtros opcionales
    if (!$incluirAprobaciones) {
        $query .= " AND (h.APROBACION_ANTERIOR IS NULL AND h.APROBACION_NUEVA IS NULL)";
    }
    
    if (!$incluirEstados) {
        $query .= " AND (h.ESTADO_ANTERIOR IS NULL AND h.ESTADO_NUEVO IS NULL)";
    }

    $query .= " ORDER BY h.FECHA_CAMBIO DESC";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':fecha_inicial', $fechaInicial);
    oci_bind_by_name($stmt, ':fecha_final', $fechaFinal);
    
    if (!oci_execute($stmt)) {
        $error = oci_error($stmt);
        error_log("❌ Error ejecutando consulta historial filtrado: " . print_r($error, true));
        echo json_encode(['success' => false, 'error' => 'Error en consulta: ' . $error['message']]);
        oci_close($conn);
        break;
    }

    $historial = [];
    while ($row = oci_fetch_assoc($stmt)) {
        // Mantener la misma lógica de archivos que tienes
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

        $historial[] = [
            'ID_HISTORICO' => $row['ID_HISTORICO'],
            'ID_SOLICITUD' => $row['ID_SOLICITUD'],
            'NUM_TIENDA' => $row['NUM_TIENDA'],
            'ESTADO_ANTERIOR' => $row['ESTADO_ANTERIOR'],
            'ESTADO_NUEVO' => $row['ESTADO_NUEVO'],
            'APROBACION_ANTERIOR' => $row['APROBACION_ANTERIOR'] ?: 'Por Aprobar',
            'APROBACION_NUEVA' => $row['APROBACION_NUEVA'] ?: 'Por Aprobar',
            'COMENTARIO_ANTERIOR' => $row['COMENTARIO_ANTERIOR'],
            'COMENTARIO_NUEVO' => $row['COMENTARIO_NUEVO'],
            'FECHA_CAMBIO' => $row['FECHA_CAMBIO'],
            'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
            'SOLICITADO_POR' => $row['SOLICITADO_POR'],
            'ARCHIVOS' => $row['ARCHIVOS']  // ← MANTENER TU LÓGICA DE ARCHIVOS
        ];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    error_log("✅ Historial filtrado obtenido: " . count($historial) . " registros");
    echo json_encode($historial);
    break;


    // AGREGAR ESTE CASE TAMBIÉN
case 'exportar_historial':
    error_log("📄 Exportando historial...");
    
    if (!isset($_GET['formato']) || !isset($_GET['fecha_inicial']) || !isset($_GET['fecha_final'])) {
        die('Faltan parámetros obligatorios');
    }

    $formato = $_GET['formato'];
    $fechaInicial = $_GET['fecha_inicial'];
    $fechaFinal = $_GET['fecha_final'];
    $incluirAprobaciones = isset($_GET['incluir_aprobaciones']) ? (int)$_GET['incluir_aprobaciones'] : 1;
    $incluirEstados = isset($_GET['incluir_estados']) ? (int)$_GET['incluir_estados'] : 1;

    if ($formato === 'excel') {
        // ✅ TU LÓGICA DE EXCEL ANTERIOR (mantenerla igual)
        
    } else if ($formato === 'pdf') {
        // ✅ REDIRIGIR AL ARCHIVO PDF MODIFICADO
        $params = http_build_query([
            'fecha_inicial' => $fechaInicial,
            'fecha_final' => $fechaFinal,
            'incluir_aprobaciones' => $incluirAprobaciones,
            'incluir_estados' => $incluirEstados
        ]);
        
        header("Location: reporte_historial_pdf.php?$params");
        exit;
    }
    
    break;


                // SOLICITUDES
case 'get_solicitudes':
    error_log("📋 Obteniendo solicitudes APROBADAS para RRHH...");
    
    $query = "SELECT
        s.ID_SOLICITUD,
        s.NUM_TIENDA,
        s.PUESTO_SOLICITADO,
        s.ESTADO_SOLICITUD,
        s.ESTADO_APROBACION,  -- ← AGREGAR ESTA LÍNEA
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

        -- Ya tenías TIENE_ARCHIVOS:
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

        -- TIENE_SELECCION: si hay selección activa para el último 'CVs Enviados'
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
        ) AS TIENE_SELECCION

    FROM ROY_SOLICITUD_PERSONAL s
    WHERE s.ESTADO_APROBACION = 'Aprobado'  -- ← AGREGAR ESTE FILTRO
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
            'ESTADO_APROBACION' => $row['ESTADO_APROBACION'] ?: 'Por Aprobar', // ← AGREGAR ESTA LÍNEA
            'FECHA_SOLICITUD' => $row['FECHA_SOLICITUD'],
            'FECHA_MODIFICACION' => $row['FECHA_MODIFICACION'],
            'SOLICITADO_POR' => $row['SOLICITADO_POR'],
            'RAZON' => $row['RAZON'],
            'DIRIGIDO_A' => $row['DIRIGIDO_A'],
            'COMENTARIO_SOLICITUD' => $row['COMENTARIO_SOLICITUD'],
            'ID_HISTORICO' => $row['ID_HISTORICO'],
            'TIENE_ARCHIVOS' => $row['TIENE_ARCHIVOS'],
            'TIENE_SELECCION' => $row['TIENE_SELECCION'],
            'NO_LEIDOS' => intval($row['NO_LEIDOS'])
        ];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    error_log("✅ Solicitudes APROBADAS obtenidas para RRHH: " . count($solicitudes));
    echo json_encode($solicitudes);
    break;






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


//VER CVS SELECCIONADOS POR SUPERVISORES
case 'ver_resumen_cvs':
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $action = $_POST['action'] ?? $data['action'] ?? null;
    $idSolicitud = $_POST['id_solicitud'] ?? $data['id_solicitud'] ?? null;

    if (empty($action)) {
        echo json_encode(['success' => false, 'error' => 'Acción no especificada']);
        exit;
    }

    if (empty($idSolicitud)) {
        echo json_encode(['success' => false, 'error' => 'ID de solicitud no proporcionado']);
        exit;
    }

    $query = "SELECT 
                sel.ARCHIVOS_SELECCIONADOS,
                sol.SOLICITADO_POR,
                TO_CHAR(sol.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD
              FROM ROY_SELECCION_CVS sel
              JOIN ROY_SOLICITUD_PERSONAL sol ON sol.ID_SOLICITUD = sel.ID_SOLICITUD
              WHERE sel.ID_SOLICITUD = :id
              ORDER BY sel.FECHA_SELECCION DESC
              FETCH FIRST 1 ROWS ONLY";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':id', $idSolicitud);

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
    $supervisor = '';
    $fechaSolicitud = '';

    $row = oci_fetch_assoc($stmt);
    if ($row) {
        $supervisor = $row['SOLICITADO_POR'];
        $fechaSolicitud = $row['FECHA_SOLICITUD'];

        if (!empty($row['ARCHIVOS_SELECCIONADOS'])) {
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
    }

    oci_free_statement($stmt);

    echo json_encode([
        'success' => true,
        'archivos' => $archivos,
        'supervisor' => $supervisor,
        'fecha' => $fechaSolicitud,
        'total' => count($archivos)
    ]);
    exit;

    }
}
?>
