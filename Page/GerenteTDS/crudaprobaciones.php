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

//=======================================================================
//INICIALIZACION DE SISTEMA 
//=======================================================================
//====================================================================================================================
// CASES METODO POST LEER/GUARDA DATOS 
//====================================================================================================================    
session_start();

if (!isset($_SESSION['user'][12])) {
    enviarJSON(['success' => false, 'error' => 'No autenticado']);
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;

if (!$action) {
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

//$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        
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
        // OBTENER SOLICITUDES CON FILTRO POR SUPERVISORES ASIGNADOS
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
                        'NO_LEIDOS' => $row['NO_LEIDOS'],
                        'TOTAL_CANDIDATOS' => intval($row['TOTAL_CANDIDATOS'])
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

 
// ====================================================================================
// NUEVOS CASES PARA EL NUEVO FUNCIONAMIENTO DE SOLICITUD PERSONAL -GERENTE-
// ====================================================================================

// ===================================================================================
// PERMISOS DE SUBIDA SEGUN VISTA - GERENTES SOLO LECTURA 
// ===================================================================================
case 'get_permisos_subida_candidato_gerente':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        $rol_usuario = $_GET['rol_usuario'] ?? 'GERENTE';
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato requerido');
        }
        
       // MODIFICACIÓN: Incluir MOTIVO_DESCARTE en la consulta
$queryCandidato = "SELECT 
                    c.ESTADO_CANDIDATO, 
                    c.MOTIVO_DESCARTE,
                    c.ACTIVO,
                    s.PUESTO_SOLICITADO, 
                    s.DIRIGIDO_RH,
                    s.NUM_TIENDA,
                    s.SOLICITADO_POR as SUPERVISOR,
                    TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY') as FECHA_SOLICITUD
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
        
        // ✅ CONSULTA PARA OBTENER QUIÉN DESCARTÓ AL CANDIDATO
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

        // Obtener nombre del gerente del historial
        $queryGerente = "SELECT h.OBSERVACIONES FROM ROY_CANDIDATOS_HIST_EST h WHERE h.ID_CANDIDATO = :id AND h.ESTADO_NUEVO = 'Aprobacion de Aval Enviado' AND ROWNUM = 1 ORDER BY h.FECHA_CAMBIO DESC";
        $stmtGerente = oci_parse($conn, $queryGerente);
        oci_bind_by_name($stmtGerente, ':id', $id_candidato);
        oci_execute($stmtGerente);
        $infoGerente = oci_fetch_assoc($stmtGerente);
        $nombreGerente = 'GERENTE DE OPERACIONES';
        if ($infoGerente && preg_match('/Decisión del gerente ([^:]+):/', $infoGerente['OBSERVACIONES'], $matches)) {
            $nombreGerente = trim($matches[1]);
        }
        oci_free_statement($stmtGerente);
        
        echo json_encode([
            'success' => true,
            'rol_usuario' => 'GERENTE',
            'estado_candidato' => $estadoActualCandidato,
            'puesto_solicitado' => $dataCandidato['PUESTO_SOLICITADO'],
            'motivo_descarte' => $motivo_descarte,
            'info_descarte' => $infoDescarte, // ✅ INFORMACIÓN DE QUIÉN DESCARTÓ
            'carpetas' => $carpetasPermitidas,
            'es_jefe' => true,
            'asesora_rh' => $nombreAsesoraRH,
            'posicion_actual' => $posicionActual,
            'num_tienda' => $dataCandidato['NUM_TIENDA'] ?? 'No disponible',
            'supervisor' => $dataCandidato['SOLICITADO_POR'] ?? 'No disponible',
            'fecha_solicitud' => $dataCandidato['FECHA_SOLICITUD'] ?? 'No disponible',
            'nombre_gerente' => $nombreGerente,
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;
    
//================================================================
// NUEVO CASE: DESCARTAR CANDIDATO POR SUPERVISOR 
//================================================================
case 'descartar_candidato_gerente':
    try {
        $id_candidato = $_POST['id_candidato'] ?? null;
        $motivo_descarte = $_POST['motivo_descarte'] ?? null;

        if (!$id_candidato || !$motivo_descarte) {
            echo json_encode(['success' => false, 'error' => 'ID de candidato y motivo de descarte son requeridos']);
            exit;
        }

        $id_candidato = intval($id_candidato);

        // ✅ DEBUG: Ver qué contiene la sesión
        error_log("🔍 DEBUG - Contenido completo de la sesión: " . print_r($_SESSION, true));
        
        // ✅ OBTENER CÓDIGO DEL GERENTE
        $codigo_gerente = $_SESSION['user'][12] ?? null;
        error_log("🔍 DEBUG - Código gerente obtenido: " . $codigo_gerente);
        
        // ✅ MAPEO DE CÓDIGOS A NOMBRES DE GERENTES
        $gerente_nombres = [
            '5333' => 'Christian Quan', 
            '5210' => 'Giovanni Cardoza'
        ];
        
        // ✅ VERIFICAR SI EL CÓDIGO EXISTE EN EL MAPEO
        if ($codigo_gerente && isset($gerente_nombres[$codigo_gerente])) {
            $nombre_gerente = $gerente_nombres[$codigo_gerente];
            error_log("✅ Gerente identificado correctamente: $nombre_gerente (código: $codigo_gerente)");
        } else {
            error_log("⚠️ Código de gerente no encontrado en mapeo: $codigo_gerente");
            
            // ✅ INTENTAR DIFERENTES POSICIONES DE LA SESIÓN
            $posibles_nombres = [
                $_SESSION['user'][1] ?? null,  // Posición 1
                $_SESSION['user'][2] ?? null,  // Posición 2
                $_SESSION['user'][3] ?? null,  // Posición 3
                $_SESSION['nombre_usuario'] ?? null,
                $_SESSION['usuario_nombre'] ?? null,
                $_SESSION['nombre'] ?? null
            ];
            
            error_log("🔍 Posibles nombres en sesión: " . print_r($posibles_nombres, true));
            
            // Buscar el primer nombre válido
            $nombre_gerente = 'Gerente de Operaciones'; // Valor por defecto
            foreach ($posibles_nombres as $posible_nombre) {
                if (!empty($posible_nombre) && $posible_nombre !== '1' && strlen(trim($posible_nombre)) > 2) {
                    $nombre_gerente = trim($posible_nombre);
                    error_log("✅ Nombre encontrado en sesión: $nombre_gerente");
                    break;
                }
            }
            
            // ✅ ÚLTIMA OPCIÓN: CONSULTAR BASE DE DATOS
            if ($nombre_gerente === 'Gerente de Operaciones' && $codigo_gerente) {
                error_log("🔍 Intentando buscar nombre en base de datos para código: $codigo_gerente");
                
                // Consultar en la tabla de usuarios o empleados
                $queryNombreGerente = "SELECT NOMBRE_EMPLEADO, APELLIDO_EMPLEADO 
                                       FROM ROY_EMPLEADOS 
                                       WHERE CODIGO_EMPLEADO = :codigo_gerente
                                       AND ROWNUM = 1";
                
                $stmtNombre = oci_parse($conn, $queryNombreGerente);
                if ($stmtNombre) {
                    oci_bind_by_name($stmtNombre, ':codigo_gerente', $codigo_gerente);
                    if (oci_execute($stmtNombre)) {
                        $dataNombre = oci_fetch_assoc($stmtNombre);
                        if ($dataNombre) {
                            $nombre_gerente = trim($dataNombre['NOMBRE_EMPLEADO'] . ' ' . $dataNombre['APELLIDO_EMPLEADO']);
                            error_log("✅ Nombre obtenido de BD: $nombre_gerente");
                        }
                    }
                    oci_free_statement($stmtNombre);
                }
            }
        }

        error_log("🎯 Nombre final del gerente: $nombre_gerente");

        // Resto del código permanece igual...
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

        // ✅ CREAR OBSERVACIÓN CON NOMBRE CORRECTO
        $observacionDescarte = "CANDIDATO DESCARTADO POR GERENTE: {$nombre_gerente} - Motivo: " . $motivo_descarte;
        
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
        /*$queryHistGeneral = "INSERT INTO ROY_HISTORICO_SOLICITUD 
                            (ID_SOLICITUD, ESTADO_ANTERIOR, ESTADO_NUEVO, FECHA_CAMBIO, TIPO_EVENTO) 
                            VALUES (:id_solicitud, :estado_anterior, 'Descartado', SYSDATE, 'CANDIDATO_DESCARTADO_GERENTE')";
        
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
            'message' => 'Candidato descartado correctamente por gerencia',
            'candidato' => $nombreCompleto,
            'descartado_por' => $nombre_gerente
        ]);

    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ================================
// TAMBIÉN PARA DEBUG: Agregar este case temporal
// ================================

case 'debug_session':
    echo json_encode([
        'session_completa' => $_SESSION,
        'user_array' => $_SESSION['user'] ?? 'No existe',
        'codigo_gerente' => $_SESSION['user'][12] ?? 'No existe posición 12'
    ]);
    break;
    
    // MOTIVO DEL DESCARTE 
    case 'get_motivo_descarte_gerente':
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

// ===================================================================================
// OBTENER CANDIDATOS POR SOLICITUD PARA GERENTES
// ===================================================================================   
case 'get_candidatos_por_solicitud_gerente':

        // ✅ FORZAR LIMPIEZA DE BUFFER Y HEADER
    if (ob_get_level()) ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    
    error_log("=== INICIO CASE get_candidatos_por_solicitud_gerente ===");

    // ✅ FORZAR SALIDA INMEDIATA (sin buffer)
    if (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    
    error_log("=== INICIO: get_candidatos_por_solicitud_gerente ===");
    
    try {
        $id_solicitud = $_GET['id_solicitud'] ?? null;
        error_log("ID Solicitud: " . $id_solicitud);
        
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
        
        error_log("Solicitud encontrada - REACTIVADA: " . ($solicitud['REACTIVADA'] ?? 'NULL'));
        
        $es_reactivada = ($solicitud['REACTIVADA'] === 'Y');
        $es_plaza_cubierta = (strtolower(trim($solicitud['ESTADO_SOLICITUD'])) === 'plaza cubierta');

        // ============================================================================
        // CASO 1: SOLICITUD EN PROCESO DE REACTIVACIÓN (REACTIVADA = 'Y')
        // ============================================================================
        if ($es_reactivada) {
            error_log("✅ Solicitud está REACTIVADA = Y");
            
            // Obtener última reactivación
            $query_estado_react = "SELECT * FROM (
                                      SELECT 
                                          NUM_REACTIVACION,
                                          ESTADO_REACT
                                      FROM ROY_HIST_REACTIVACIONES 
                                      WHERE ID_SOLICITUD = :id_solicitud 
                                      ORDER BY NUM_REACTIVACION DESC
                                   ) WHERE ROWNUM = 1";
            
            $stmt_react = oci_parse($conn, $query_estado_react);
            oci_bind_by_name($stmt_react, ':id_solicitud', $id_solicitud);
            
            if (!oci_execute($stmt_react)) {
                $err = oci_error($stmt_react);
                error_log("❌ Error query reactivación: " . print_r($err, true));
                oci_free_statement($stmt_react);
                
                // Si falla, devolver esperando_rh = true
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
            
            $reactivacion = oci_fetch_assoc($stmt_react);
            oci_free_statement($stmt_react);
            
            error_log("📋 Reactivación: " . print_r($reactivacion, true));
            
            // Si NO hay reactivación O está pendiente
            if (!$reactivacion || strtolower(trim($reactivacion['ESTADO_REACT'])) === 'pendiente') {
                error_log("⚠️ Estado PENDIENTE - enviando esperando_rh = true");
                
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
            
            error_log("✅ Estado CONFIRMADO - continuando");
            // Si está confirmada, continuar al flujo normal (no hacer exit aquí)
        } else {
            error_log("ℹ️ Solicitud NO está reactivada");
        }

        // ============================================================================
        // CASO 2 Y 3: SOLICITUD NORMAL O CON REACTIVACIÓN CONFIRMADA
        // ============================================================================
        
        error_log("🔍 Buscando candidatos...");
        
        $whereClause = "WHERE c.ID_SOLICITUD = :id_solicitud";
        
        if ($es_plaza_cubierta) {
            // CASO 1: Plaza cubierta - Solo mostrar contratado activo
            $whereClause .= " AND c.ESTADO_CANDIDATO = 'Contratado' AND c.ACTIVO = 'Y'";
        } else {
            // CASO 2: Verificar si hay candidatos reactivados post-contratación
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
                // ✅ HAY REACTIVADOS: Obtener la fecha de la ÚLTIMA reactivación
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
                    // ✅ MOSTRAR SOLO candidatos reactivados en la ÚLTIMA reactivación
                    $whereClause .= " AND c.REACTIVADO_POST_CONTRATACION = 'Y'
                                     AND TRUNC(c.FECHA_REACTIVACION_CANDIDATO) >= TRUNC(TO_DATE(:ultima_fecha, 'DD/MM/YY HH24:MI:SS'))";
                } else {
                    // No hay fecha de reactivación, mostrar todos los reactivados
                    $whereClause .= " AND c.REACTIVADO_POST_CONTRATACION = 'Y'";
                }
            } else {
                // NO HAY REACTIVADOS: Aplicar filtros normales
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
        
        // ✅ Si hay fecha de última reactivación, hacer bind
        if (isset($ultima_fecha_reactivacion) && $ultima_fecha_reactivacion) {
            oci_bind_by_name($stmt, ':ultima_fecha', $ultima_fecha_reactivacion);
        }
        
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

        error_log("✅ Candidatos encontrados: " . count($candidatos));

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
        
        error_log("=== FIN: get_candidatos_por_solicitud_gerente ===");
        
    } catch (Exception $e) {
        error_log("❌ ERROR: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;  // ✅ IMPORTANTE: exit al final del case
    break;
    
//===================================================================================
// OBTENER RESUMEN DE APROBACION DE PROCESAMIENTO GERENCIAL 
//===================================================================================

case 'get_resultado_procesamiento_gerencial':
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


// ===================================================================================
// OBTENER ARCHIVOS DE CANDIDATO PARA GERENTES
// =================================================================================== 

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


// ===================================================================================
// VER Y DESCARGAR ARCHIVO PARA GERENTES
// =================================================================================== 
// VER ARCHIVO PARA GERENTES
case 'ver_archivo':
    $nombre_archivo = $_GET['archivo'] ?? '';
    if (empty($nombre_archivo)) {
        http_response_code(404);
        echo "Archivo no especificado";
        exit;
    }
    
    // Ruta ajustada para gerentes - apunta a la carpeta de gestionhumana
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

// DESCARGAR ARCHIVO PARA GERENTES
case 'descargar_archivo':
    $nombre_archivo = $_GET['archivo'] ?? '';
    if (empty($nombre_archivo)) {
        http_response_code(404);
        echo "Archivo no especificado";
        exit;
    }
    
    // Ruta ajustada para gerentes - apunta a la carpeta de gestionhumana  
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

// ===================================================================================
// OBTENER TOTAL DE CANDIDATOS PARA GERENTES
// =================================================================================== 

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
                
case 'get_solicitud_by_candidato':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        if (!$id_candidato) {
            throw new Exception('ID de candidato no proporcionado');
        }
        
        $query = "SELECT c.ID_SOLICITUD FROM ROY_CANDIDATOS_SOLICITUD c WHERE c.ID_CANDIDATO = :id_candidato AND c.ACTIVO = 'Y'";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_candidato', $id_candidato);
        oci_execute($stmt);
        $resultado = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        
        if (!$resultado) {
            throw new Exception('No se encontró el candidato especificado');
        }
        
        echo json_encode(['success' => true, 'id_solicitud' => $resultado['ID_SOLICITUD']]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;


// ===================================================================================
// PROCESO DE "APROBACION DE AVAL" 
// ===================================================================================

// ===================================================================================
// NUEVO CASE: OBTENER INFORMACIÓN COMPLETA DEL AVAL PROCESADO
// ===================================================================================
case 'get_info_aval_completa':
    try {
        $id_candidato = $_GET['id_candidato'] ?? null;
        
        if (!$id_candidato) {
            throw new Exception('ID de candidato requerido');
        }
        
        error_log("=== DEBUG INICIO - ID Candidato: $id_candidato ===");
        
        // CONSULTA PRINCIPAL
        $query = "SELECT 
                    c.ID_CANDIDATO,
                    c.NOMBRE_CANDIDATO,
                    c.APELLIDOS_CANDIDATO,
                    c.DOCUMENTO_CANDIDATO,
                    c.ESTADO_CANDIDATO,
                    c.APROBACION,
                    c.MOTIVO_DECISION,
                    TO_CHAR(c.FECHA_DECISION, 'DD-MON-YYYY HH24:MI:SS') as FECHA_DECISION_FORMATEADA,
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
        
        error_log("DEBUG - Candidato encontrado: " . $candidato['NOMBRE_CANDIDATO'] . " " . $candidato['APELLIDOS_CANDIDATO']);
        
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
        error_log("DEBUG - Nombre inicial del gerente: $nombreGerente");
        
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
            
            error_log("DEBUG - Total registros historial: " . count($debug_registros));
            error_log("DEBUG - Registros completos: " . print_r($debug_registros, true));
        } else {
            $error = oci_error($stmtDebugHistorial);
            error_log("DEBUG - Error en consulta historial: " . print_r($error, true));
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
                error_log("DEBUG - Info gerente encontrada - Usuario: " . ($infoGerente['USUARIO_CAMBIO'] ?? 'NULL'));
                
                // PASO 1: Intentar extraer nombre de las observaciones (MEJORADO)
                if (!empty($infoGerente['OBSERVACIONES'])) {
                    $observaciones = $infoGerente['OBSERVACIONES'];
                    
                    if (is_object($observaciones)) {
                        $observaciones = $observaciones->load();
                    }
                    
                    error_log("DEBUG - Observaciones a procesar: " . $observaciones);
                    
                    // Patrones mejorados para extraer el nombre del gerente
                    $patronesGerente = [
                        '/Decisión del gerente\s+([^:]+):\s*/',
                        '/Decisi[oó]n del gerente\s+([^:]+):\s*/',  // Con acentos
                        '/Decisi\?\?n del gerente\s+([^:]+):\s*/',  // Con caracteres mal codificados
                        '/GERENTE:\s*([^-\n\r]+)/i',
                        '/Gerente\s*([^:]+):\s*/',
                        '/Procesado por[:\s]*([^-\n\r]+)/i'
                    ];
                    
                    foreach ($patronesGerente as $index => $patron) {
                        error_log("DEBUG - Probando patrón $index: $patron");
                        if (preg_match($patron, $observaciones, $matches)) {
                            error_log("DEBUG - Patrón $index COINCIDIÓ: " . print_r($matches, true));
                            $nombreExtraido = trim($matches[1]);
                            if (!empty($nombreExtraido) && strlen($nombreExtraido) > 2) {
                                $nombreGerente = $nombreExtraido;
                                error_log("DEBUG - NOMBRE EXTRAÍDO DE OBSERVACIONES: $nombreGerente");
                                break;
                            }
                        }
                    }
                }
                
                // PASO 2: Si se extrajo un nombre, validarlo contra RPS.STORE y lista de códigos
                if ($nombreGerente !== 'GERENTE DE OPERACIONES') {
                    error_log("DEBUG - Validando nombre extraído: $nombreGerente");
                    
                    // Lista de gerentes conocidos
                    $gerente_nombres = [
                        '5333' => 'Christian Quan', 
                        '5210' => 'Giovanni Cardoza'
                    ];
                    
                    // Verificar si el nombre extraído coincide con alguno de la lista
                    $codigoEncontrado = null;
                    foreach ($gerente_nombres as $codigo => $nombre) {
                        if (stripos($nombre, $nombreGerente) !== false || stripos($nombreGerente, $nombre) !== false) {
                            $codigoEncontrado = $codigo;
                            $nombreGerente = $nombre; // Usar el nombre completo de la lista
                            error_log("DEBUG - Nombre validado con lista: $nombreGerente (código: $codigo)");
                            break;
                        }
                    }
                    
                    // Si no se encontró en la lista, buscar en RPS.STORE
                    if (!$codigoEncontrado) {
                        error_log("DEBUG - Nombre no encontrado en lista, buscando en RPS.STORE: $nombreGerente");
                        
                        // Buscar en RPS.STORE por nombre del gerente
                        $queryRpsStore = "SELECT store_no, udf1_string, udf2_string, udf4_string 
                                         FROM RPS.STORE 
                                         WHERE sbs_sid = '680861302000159257' 
                                         AND UPPER(TRIM(udf4_string)) LIKE UPPER(:nombre_gerente)
                                         AND ROWNUM = 1";
                        
                        $stmtRpsStore = oci_parse($conn, $queryRpsStore);
                        $nombreBusqueda = '%' . $nombreGerente . '%';
                        oci_bind_by_name($stmtRpsStore, ':nombre_gerente', $nombreBusqueda);
                        
                        if (oci_execute($stmtRpsStore)) {
                            $dataRpsStore = oci_fetch_assoc($stmtRpsStore);
                            error_log("DEBUG - Resultado RPS.STORE: " . print_r($dataRpsStore, true));
                            
                            if ($dataRpsStore) {
                                $nombreGerenteRps = trim($dataRpsStore['UDF4_STRING']);
                                
                                // Verificar si este nombre de RPS coincide con algún código conocido
                                foreach ($gerente_nombres as $codigo => $nombre) {
                                    if (stripos($nombre, $nombreGerenteRps) !== false || stripos($nombreGerenteRps, $nombre) !== false) {
                                        $nombreGerente = $nombre;
                                        error_log("DEBUG - Nombre validado con RPS.STORE y lista: $nombreGerente (código: $codigo)");
                                        break;
                                    }
                                }
                                
                                // Si no coincide con ningún código conocido, usar el de RPS.STORE
                                if (!isset($codigo)) {
                                    $nombreGerente = $nombreGerenteRps;
                                    error_log("DEBUG - Usando nombre de RPS.STORE: $nombreGerente");
                                }
                            } else {
                                error_log("DEBUG - No se encontró el gerente en RPS.STORE");
                            }
                        } else {
                            $error = oci_error($stmtRpsStore);
                            error_log("DEBUG - Error en consulta RPS.STORE: " . print_r($error, true));
                        }
                        oci_free_statement($stmtRpsStore);
                    }
                } else {
                    error_log("DEBUG - No se extrajo nombre de observaciones, usando valor por defecto");
                }
            } else {
                error_log("DEBUG - NO se encontró información del gerente en el historial");
            }
        } else {
            $error = oci_error($stmtGerente);
            error_log("DEBUG - Error ejecutando consulta del historial: " . print_r($error, true));
        }
        oci_free_statement($stmtGerente);
        
        error_log("DEBUG - NOMBRE GERENTE FINAL: '$nombreGerente'");
        
        // CREAR ARRAY CON TODOS LOS DATOS
        $candidato_procesado = [
            'ID_CANDIDATO' => $candidato['ID_CANDIDATO'],
            'NOMBRE_CANDIDATO' => $candidato['NOMBRE_CANDIDATO'],
            'APELLIDOS_CANDIDATO' => $candidato['APELLIDOS_CANDIDATO'],
            'DOCUMENTO_CANDIDATO' => $candidato['DOCUMENTO_CANDIDATO'],
            'ESTADO_CANDIDATO' => $candidato['ESTADO_CANDIDATO'],
            'APROBACION' => $candidato['APROBACION'],
            'MOTIVO_DECISION' => $motivo_decision,
            'FECHA_DECISION' => $candidato['FECHA_DECISION_FORMATEADA'],
            'PUESTO_SOLICITADO' => $candidato['PUESTO_SOLICITADO'],
            'NUM_TIENDA' => $candidato['NUM_TIENDA'],
            'SUPERVISOR' => $candidato['SUPERVISOR'],
            'ARCHIVOS_CV' => $candidato['ARCHIVOS_CV'],
            'ARCHIVOS_PSICOMETRICA' => $candidato['ARCHIVOS_PSICOMETRICA'],
            'ARCHIVOS_ENTREVISTA_RH' => $candidato['ARCHIVOS_ENTREVISTA_RH'],
            'ARCHIVOS_ENTREVISTA_TECNICA' => $candidato['ARCHIVOS_ENTREVISTA_TECNICA'],
            'ARCHIVOS_DIA_PRUEBA' => $candidato['ARCHIVOS_DIA_PRUEBA'],
            'ARCHIVOS_POLIGRAFO' => $candidato['ARCHIVOS_POLIGRAFO'],
            'NOMBRE_GERENTE' => $nombreGerente
        ];
        
        error_log("DEBUG - Candidato procesado creado con NOMBRE_GERENTE: " . $candidato_procesado['NOMBRE_GERENTE']);
        
        echo json_encode([
            'success' => true,
            'candidato' => $candidato_procesado,
            'NOMBRE_GERENTE' => $nombreGerente,
            'debug_info' => $debug_registros
        ]);
        
        error_log("=== DEBUG FIN ===");
        
    } catch (Exception $e) {
        error_log("DEBUG - ERROR: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ==================================================================================
// CASE: PROCESO DE SOLICITUDES (HISTORIAL GENERAL E INDIVIDUAL)
// ==================================================================================
case 'get_proceso_solicitudes_gerentes':
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

    //FILTROS PARA EL HISTORIAL DE LAS SOLICITUDES CON SUS CANDIDATOS 
    //=====================================================================================
    // CASES DE FILTROS PARA EL HISTORIAL GENERAL E INDIVIDUAL DE SOLICITUDES
    //=====================================================================================

                           // OBTENER TIENDAS PARA FILTRO
                            case 'get_tiendas_filtro_gerente':
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
                            case 'get_supervisores_filtro_gerente':
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
                    case 'get_puestos_filtro_gerente':
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
    
        //RESUMEN DE APROBACION POR GERENTE                        
        case 'obtener_resumen_aprobacion_gerente':
            // Limpiar cualquier output buffer antes de enviar JSON
            if (ob_get_level()) {
                ob_clean();
            }
            
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
                    }
                    
                    // OBTENER NOMBRE COMPLETO DEL GERENTE
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
                    
                    // Extraer solo el comentario limpio
                    $comentario_limpio = 'Sin comentario adicional';
                    if ($comentario_completo) {
                        // Debug para ver qué contiene
                        error_log("COMENTARIO COMPLETO DEBUG: " . $comentario_completo);
                        
                        // MÉTODO MÁS DIRECTO: buscar y extraer solo después de los dos puntos
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

//=========================================================================================
// VER RESULTADO DE APROBACION DE GERENTE APROBACION
//==========================================================================================

case 'obtener_resumen_grnts':
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


//=======================================================================================================
//FIN DE CASES DE GERENTES 
//=======================================================================================================

        // ===================================================================
        // DEFAULT CASE (SOLO UNO)
        // ===================================================================
        default:
            error_log("Action no reconocida: " . $_GET['action']);
            echo json_encode(['success' => false, 'error' => 'Acción no reconocida']);
            break;
    }

} else if (isset($_POST['action'])) {

//====================================================================================================================
// CASES METODO POST ENVIA/GUARDA DATOS 
//====================================================================================================================    
    switch ($_POST['action']) {

            case 'subir_archivo_candidato_gerente':
    try {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        $id_candidato = $_POST['id_candidato'] ?? null;
        $id_solicitud = $_POST['id_solicitud'] ?? null;
        $estado_relacionado = $_POST['estado_relacionado'] ?? null;
        
        if (!$id_candidato || !$id_solicitud || !$estado_relacionado) {
            throw new Exception('Parámetros incompletos');
        }
        
        $archivo = $_FILES['archivo'];
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])) {
            throw new Exception('Tipo de archivo no permitido');
        }
        
        if (!in_array($estado_relacionado, ['Entrevista Tecnica', 'Dia de Prueba'])) {
            throw new Exception('Sin permisos para este estado');
        }
        
        $rutaDestino = '../gestionhumana/archivos_candidatos/';
        if (!is_dir($rutaDestino)) {
            mkdir($rutaDestino, 0755, true);
        }
        
        $nombreArchivo = $estado_relacionado . "_{$id_solicitud}_{$id_candidato}_" . date('Y-m-d') . '.' . $extension;
        
        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino . $nombreArchivo)) {
            throw new Exception('Error al guardar el archivo');
        }
        
        $queryInsert = "INSERT INTO ROY_ARCHIVOS_SOLICITUD 
                       (ID_SOLICITUD, NOMBRE_ARCHIVO, FECHA_SUBIDA, ID_HISTORICO, 
                        TIPO_ARCHIVO, ID_CANDIDATO, ESTADO_RELACIONADO, SUBIDO_POR_ROL) 
                       VALUES (:id_solicitud, :nombre, SYSDATE, NULL, 
                               :tipo_archivo, :id_candidato, :estado, 'GERENTE')";
        
        $stmtInsert = oci_parse($conn, $queryInsert);
        oci_bind_by_name($stmtInsert, ':id_solicitud', $id_solicitud);
        oci_bind_by_name($stmtInsert, ':nombre', $nombreArchivo);
        oci_bind_by_name($stmtInsert, ':tipo_archivo', $estado_relacionado);
        oci_bind_by_name($stmtInsert, ':id_candidato', $id_candidato);
        oci_bind_by_name($stmtInsert, ':estado', $estado_relacionado);
        
        if (!oci_execute($stmtInsert)) {
            $error = oci_error($stmtInsert);
            throw new Exception('Error en base de datos: ' . $error['message']);
        }
        
        oci_free_statement($stmtInsert);
        oci_commit($conn);
        
        echo json_encode(['success' => true, 'message' => 'Archivo subido exitosamente']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
    break;   

    
// ===================================================================================
// NUEVO CASE: OBTENER CANDIDATOS CON ESTADO "APROBACION DE AVAL" 
// ===================================================================================
case 'get_candidatos_aval_gerente':
    try {
        $id_solicitud = $_GET['id_solicitud'] ?? null;
        
        if (!$id_solicitud) {
            throw new Exception('ID de solicitud requerido');
        }
        
        // ✅ CONSULTA MODIFICADA - INCLUIR TANTO CANDIDATOS NUEVOS COMO REPROCESABLES
        $query = "SELECT 
                    c.ID_CANDIDATO,
                    c.NOMBRE_CANDIDATO,
                    c.APELLIDOS_CANDIDATO,
                    c.DOCUMENTO_CANDIDATO,
                    c.ESTADO_CANDIDATO,
                    c.ACTIVO,
                    c.APROBACION,
                    c.MOTIVO_DECISION,
                    TO_CHAR(c.FECHA_DECISION, 'DD-MON-YYYY HH24:MI') as FECHA_DECISION,
                    s.PUESTO_SOLICITADO,
                    s.SOLICITADO_POR,
                    (SELECT COUNT(*) FROM ROY_ARCHIVOS_SOLICITUD a 
                    WHERE a.ID_CANDIDATO = c.ID_CANDIDATO) as TOTAL_ARCHIVOS
                FROM ROY_CANDIDATOS_SOLICITUD c
                JOIN ROY_SOLICITUD_PERSONAL s ON c.ID_SOLICITUD = s.ID_SOLICITUD
                WHERE c.ID_SOLICITUD = :id_solicitud
                AND c.ESTADO_CANDIDATO = 'Aprobacion de Aval'
                AND c.ACTIVO = 'Y'
                ORDER BY c.ID_CANDIDATO DESC";
        
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':id_solicitud', $id_solicitud);
        
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
            throw new Exception('Error ejecutando consulta: ' . $error['message']);
        }
        
        $candidatos = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $motivo_decision = '';
            if ($row['MOTIVO_DECISION']) {
                if (is_object($row['MOTIVO_DECISION'])) {
                    $motivo_decision = $row['MOTIVO_DECISION']->load();
                } else {
                    $motivo_decision = $row['MOTIVO_DECISION'];
                }
            }
            
            // ✅ VERIFICAR SI TIENE DECISIONES PREVIAS EN EL HISTORIAL
            $queryHistorialPrevio = "SELECT COUNT(*) as TOTAL_DECISIONES
                                    FROM ROY_CANDIDATOS_HIST_EST 
                                    WHERE ID_CANDIDATO = :id_candidato 
                                    AND ESTADO_NUEVO = 'Aprobacion de Aval Enviado'";
            
            $stmtHistorial = oci_parse($conn, $queryHistorialPrevio);
            oci_bind_by_name($stmtHistorial, ':id_candidato', $row['ID_CANDIDATO']);
            oci_execute($stmtHistorial);
            $historialData = oci_fetch_assoc($stmtHistorial);
            $tieneDecisionesPrevias = ($historialData['TOTAL_DECISIONES'] > 0);
            oci_free_statement($stmtHistorial);
            
            $candidatos[] = [
                'ID_CANDIDATO' => $row['ID_CANDIDATO'],
                'NOMBRE_CANDIDATO' => $row['NOMBRE_CANDIDATO'],
                'APELLIDOS_CANDIDATO' => $row['APELLIDOS_CANDIDATO'],
                'DOCUMENTO_CANDIDATO' => $row['DOCUMENTO_CANDIDATO'],
                'ESTADO_CANDIDATO' => $row['ESTADO_CANDIDATO'],
                'ACTIVO' => $row['ACTIVO'],
                'APROBACION' => $row['APROBACION'],
                'MOTIVO_DECISION' => $motivo_decision,
                'FECHA_DECISION' => $row['FECHA_DECISION'],
                'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
                'SOLICITADO_POR' => $row['SOLICITADO_POR'],
                'TOTAL_ARCHIVOS' => $row['TOTAL_ARCHIVOS'],
                'ya_procesado' => false, // ✅ SIEMPRE FALSE PARA CANDIDATOS EN "Aprobacion de Aval"
                'tiene_decisiones_previas' => $tieneDecisionesPrevias,
                'decision_texto' => $tieneDecisionesPrevias ? 'REPROCESABLE' : 'PENDIENTE'
            ];
        }
        oci_free_statement($stmt);
        
        echo json_encode([
            'success' => true,
            'candidatos' => $candidatos,
            'total_candidatos' => count($candidatos)
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ===================================================================================
// NUEVO CASE: PROCESAR DECISION DE AVAL POR GERENTE
// ===================================================================================  
case 'procesar_aval_gerente':
    try {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        
        $id_candidato = $_POST['id_candidato'] ?? null;
        $decision = $_POST['decision'] ?? null;
        $motivo_decision = $_POST['motivo_decision'] ?? '';
        
        if (!$id_candidato || !$decision) {
            throw new Exception('ID de candidato y decisión son requeridos');
        }
        
        if (!in_array($decision, ['APROBADO', 'RECHAZADO'])) {
            throw new Exception('Decisión inválida. Debe ser APROBADO o RECHAZADO');
        }
        
               $id_candidato = intval($id_candidato);
        
        //  NUEVA VALIDACIÓN: VERIFICAR SI YA EXISTE UN CANDIDATO APROBADO Y SOLAMENTE DEJAR UNO CON ESTADO DE APROBACION
        if ($decision === 'APROBADO') {
            // Obtener el ID_SOLICITUD del candidato actual
            $queryGetSolicitud = "SELECT ID_SOLICITUD 
                                  FROM ROY_CANDIDATOS_SOLICITUD 
                                  WHERE ID_CANDIDATO = :id_candidato 
                                  AND ACTIVO = 'Y'";
            
            $stmtGetSolicitud = oci_parse($conn, $queryGetSolicitud);
            oci_bind_by_name($stmtGetSolicitud, ':id_candidato', $id_candidato);
            oci_execute($stmtGetSolicitud);
            $dataSolicitud = oci_fetch_assoc($stmtGetSolicitud);
            oci_free_statement($stmtGetSolicitud);
            
            if (!$dataSolicitud) {
                throw new Exception('No se pudo obtener la solicitud del candidato');
            }
            
            $id_solicitud = $dataSolicitud['ID_SOLICITUD'];
            
            // 1. Contar cuántas veces se ha reactivado esta solicitud
            $queryCountReactivaciones = "SELECT NVL(COUNT(*), 0) as TOTAL_REACTIVACIONES 
                                        FROM ROY_HIST_REACTIVACIONES 
                                        WHERE ID_SOLICITUD = :id_solicitud";

            $stmtCountReact = oci_parse($conn, $queryCountReactivaciones);
            oci_bind_by_name($stmtCountReact, ':id_solicitud', $id_solicitud);
            oci_execute($stmtCountReact);
            $dataReact = oci_fetch_assoc($stmtCountReact);
            $total_reactivaciones = $dataReact['TOTAL_REACTIVACIONES'];
            oci_free_statement($stmtCountReact);

            // 2. Contar cuántos candidatos ya han sido aprobados (APROBACION = 'Y')
            $queryCountAprobados = "SELECT COUNT(*) as TOTAL_APROBADOS 
                                    FROM ROY_CANDIDATOS_SOLICITUD 
                                    WHERE ID_SOLICITUD = :id_solicitud 
                                    AND APROBACION = 'Y'
                                    AND ACTIVO = 'Y'";

            $stmtCountAprob = oci_parse($conn, $queryCountAprobados);
            oci_bind_by_name($stmtCountAprob, ':id_solicitud', $id_solicitud);
            oci_execute($stmtCountAprob);
            $dataAprob = oci_fetch_assoc($stmtCountAprob);
            $total_aprobados = $dataAprob['TOTAL_APROBADOS'];
            oci_free_statement($stmtCountAprob);

            // 3. Calcular aprobaciones permitidas
            $aprobaciones_permitidas = $total_reactivaciones + 1;

            // 4. Validar si se puede aprobar
            if ($total_aprobados >= $aprobaciones_permitidas) {
                throw new Exception("No se puede aprobar más candidatos. Esta solicitud ha sido reactivada {$total_reactivaciones} vez/veces, por lo que solo se permiten {$aprobaciones_permitidas} aprobación/es. Ya se han aprobado {$total_aprobados} candidato(s).");
            }
        }
        //FIN NUEVA VALIDACION PARA APROBACION DE UN SOLO CANDIDATO
        
        // Obtener información del gerente logueado
        $usuario_logueado = $_SESSION['user'][12] ?? null;
        if (!$usuario_logueado) {
            throw new Exception('Usuario gerente no encontrado en sesión');
        }
        
        // Obtener nombre del gerente
        $gerente_nombres = ['5333' => 'Christian Quan', '5210' => 'Giovanni Cardoza'];
        $nombre_gerente = $gerente_nombres[$usuario_logueado] ?? 'Gerente de Operaciones';
        
        // ✅ VERIFICACIÓN MODIFICADA - PERMITIR REPROCESAR CANDIDATOS EN ESTADO "Aprobacion de Aval"
        $queryVerificar = "SELECT ID_CANDIDATO, ESTADO_CANDIDATO, APROBACION 
                           FROM ROY_CANDIDATOS_SOLICITUD 
                           WHERE ID_CANDIDATO = :id_candidato 
                           AND ESTADO_CANDIDATO = 'Aprobacion de Aval'
                           AND ACTIVO = 'Y'";
        
        $stmtVerificar = oci_parse($conn, $queryVerificar);
        oci_bind_by_name($stmtVerificar, ':id_candidato', $id_candidato);
        oci_execute($stmtVerificar);
        $candidatoData = oci_fetch_assoc($stmtVerificar);
        oci_free_statement($stmtVerificar);
        
        if (!$candidatoData) {
            throw new Exception('Candidato no encontrado o no está en estado "Aprobacion de Aval" para procesar');
        }
        
        // ✅ ELIMINAR VALIDACIÓN DE "YA PROCESADO" - PERMITIR REPROCESAR
        // Esto permite que un candidato que volvió al estado "Aprobacion de Aval" pueda ser procesado nuevamente
        
        // Actualizar campos de decisión del gerente
        $aprobacion_valor = ($decision === 'APROBADO') ? 'Y' : 'N';
        
        $queryUpdate = "UPDATE ROY_CANDIDATOS_SOLICITUD 
                        SET APROBACION = :aprobacion,
                            MOTIVO_DECISION = :motivo_decision,
                            FECHA_DECISION = SYSDATE,
                            ESTADO_CANDIDATO = 'Aprobacion de Aval Enviado'
                        WHERE ID_CANDIDATO = :id_candidato";
        
        $stmtUpdate = oci_parse($conn, $queryUpdate);
        oci_bind_by_name($stmtUpdate, ':aprobacion', $aprobacion_valor);
        oci_bind_by_name($stmtUpdate, ':motivo_decision', $motivo_decision);
        oci_bind_by_name($stmtUpdate, ':id_candidato', $id_candidato);
        
        if (!oci_execute($stmtUpdate)) {
            $error = oci_error($stmtUpdate);
            throw new Exception('Error actualizando decisión: ' . $error['message']);
        }
        
        // ✅ INSERTAR NUEVO REGISTRO EN HISTORIAL (PERMITIR MÚLTIPLES DECISIONES)
        $observaciones = "Decisión del gerente $nombre_gerente: $decision - $motivo_decision";
        
        $queryHistorial = "INSERT INTO ROY_CANDIDATOS_HIST_EST 
                          (ID_CANDIDATO, ESTADO_ANTERIOR, ESTADO_NUEVO, FECHA_CAMBIO, USUARIO_CAMBIO, OBSERVACIONES)
                          VALUES (:id_candidato, 'Aprobacion de Aval', 'Aprobacion de Aval Enviado', SYSDATE, :usuario, :observaciones)";
        
        $stmtHistorial = oci_parse($conn, $queryHistorial);
        oci_bind_by_name($stmtHistorial, ':id_candidato', $id_candidato);
        oci_bind_by_name($stmtHistorial, ':usuario', $usuario_logueado);
        oci_bind_by_name($stmtHistorial, ':observaciones', $observaciones);
        
        if (!oci_execute($stmtHistorial)) {
            $error = oci_error($stmtHistorial);
            throw new Exception('Error insertando historial: ' . $error['message']);
        }
        
        oci_free_statement($stmtHistorial);
        oci_free_statement($stmtUpdate);
        
        oci_commit($conn);
        
        echo json_encode([
            'success' => true,
            'message' => 'Decisión de aval procesada correctamente',
            'decision' => $decision,
            'nombre_gerente' => $nombre_gerente
        ]);
        
    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

            // ===================================================================
        // RESTO DE CASES DEL SEGUNDO CÓDIGO (MANTENEMOS TODOS)
        // ===================================================================
        
                    case 'procesar_aprobacion_gerente':

                        while (ob_get_level()) {
                                ob_end_clean();
                        }
                        ob_start();

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

                            // ✅ LIMPIAR CUALQUIER OUTPUT PREVIO ANTES DEL JSON
                            ob_clean();

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

// ===================================================================================
// REACTIVAR SOLICITUD (SOLO GERENTE)
// ===================================================================================
case 'reactivar_solicitud':
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
        
        // ✅ OBTENER NOMBRE DEL GERENTE
        $codigo_gerente = $_SESSION['user'][12] ?? null;
        $gerente_nombres = [
            '5333' => 'Christian Quan', 
            '5210' => 'Giovanni Cardoza'
        ];
        
        $nombre_gerente = $gerente_nombres[$codigo_gerente] ?? 'Gerente no identificado';
        
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
            :nombre_gerente,
            'Gerente',
            :motivo_reactivacion,
            :id_candidato_anterior,
            :nombre_candidato_anterior,
            'Pendiente'
        ) RETURNING ID_REACTIVACION INTO :id_reactivacion_nueva";
        
        $stmt_historial = oci_parse($conn, $query_historial);
        $id_reactivacion_nueva = null;
        
        oci_bind_by_name($stmt_historial, ':id_solicitud', $id_solicitud);
        oci_bind_by_name($stmt_historial, ':num_reactivacion', $num_reactivacion);
        oci_bind_by_name($stmt_historial, ':nombre_gerente', $nombre_gerente);
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
            'nombre_gerente' => $nombre_gerente
        ]);
        
    } catch (Exception $e) {
        oci_rollback($conn);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'No action specified']);
}
?>