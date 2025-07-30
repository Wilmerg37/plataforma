<?php
// Debug logging mejorado
error_log("=== NUEVA PETICIÓN GERENTES ===");
error_log("GET: " . print_r($_GET, true));
error_log("POST: " . print_r($_POST, true));

header('Content-Type: application/json');

// Para debugging - activar errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once '../../Funsiones/conexion.php';

$conn = Oracle();
if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos']);
    exit;
}

error_log("Conexión a BD establecida");

if (isset($_GET['action'])) {
    error_log("Procesando action: " . $_GET['action']);
    
    switch ($_GET['action']) {

        // SOLICITUDES PARA GERENTES
case 'get_solicitudes_gerentes':
    try {
        $filtro_estado = $_GET['estado_aprobacion'] ?? '';
        $filtro_gerente = $_GET['dirigido_a'] ?? '';
        
        error_log("🔍 Filtros aplicados - Estado: '$filtro_estado', Gerente: '$filtro_gerente'");
        
        // ✅ CONSTRUIR CONSULTA CON FILTROS DINÁMICOS
        $whereConditions = [];
        $whereConditions[] = "1=1"; // Condición base
        
        // Filtro por estado de aprobación
        if (!empty($filtro_estado)) {
            $whereConditions[] = "s.ESTADO_APROBACION = :estado_aprobacion";
        }
        
        // Filtro por gerente (dirigido_a)
        if (!empty($filtro_gerente)) {
            $whereConditions[] = "s.DIRIGIDO_A = :dirigido_a";
        }
        
        $query = "SELECT 
                    s.ID_SOLICITUD,
                    s.NUM_TIENDA,
                    s.PUESTO_SOLICITADO,
                    s.ESTADO_SOLICITUD,
                    s.ESTADO_APROBACION,
                    s.DIRIGIDO_A,
                    s.DIRIGIDO_RH,
                    TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD,
                    CASE 
                        WHEN s.FECHA_MODIFICACION != s.FECHA_SOLICITUD 
                        THEN TO_CHAR(s.FECHA_MODIFICACION, 'DD-MM-YYYY HH24:MI:SS')
                        ELSE NULL
                    END AS FECHA_MODIFICACION,
                    s.SOLICITADO_POR,
                    s.RAZON
                  FROM ROY_SOLICITUD_PERSONAL s
                  WHERE " . implode(' AND ', $whereConditions) . "
                  ORDER BY s.FECHA_SOLICITUD DESC";
        
        $stmt = oci_parse($conn, $query);
        
        // ✅ BIND PARÁMETROS SEGÚN FILTROS APLICADOS
        if (!empty($filtro_estado)) {
            oci_bind_by_name($stmt, ':estado_aprobacion', $filtro_estado);
        }
        if (!empty($filtro_gerente)) {
            oci_bind_by_name($stmt, ':dirigido_a', $filtro_gerente);
        }
        
        if (!oci_execute($stmt)) {
            $error = oci_error($stmt);
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
                'DIRIGIDO_A' => $row['DIRIGIDO_A'],
                'DIRIGIDO_RH' => $row['DIRIGIDO_RH'],
                'FECHA_SOLICITUD' => $row['FECHA_SOLICITUD'],
                'FECHA_MODIFICACION' => $row['FECHA_MODIFICACION'],
                'SOLICITADO_POR' => $row['SOLICITADO_POR'],
                'RAZON' => $row['RAZON']
            ];
        }
        
        oci_free_statement($stmt);
        oci_close($conn);
        
        error_log("✅ Solicitudes para gerentes: " . count($solicitudes));
        echo json_encode($solicitudes);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    break;

// ✅ NUEVO CASE: APROBAR/RECHAZAR Y ASIGNAR ASESORA RRHH
// ✅ NUEVO CASE: APROBAR/RECHAZAR Y ASIGNAR ASESORA RRHH
case 'procesar_aprobacion_gerente':
    error_log("Procesando cambio de aprobación...");
    error_log("POST data: " . print_r($_POST, true));
    
    if (empty($_POST['id_solicitud']) || empty($_POST['nueva_aprobacion'])) {
        error_log("Faltan datos obligatorios");
        echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios: ID solicitud y nueva aprobación']);
        break;
    }

    $id = $_POST['id_solicitud'];
    $nueva_aprobacion = $_POST['nueva_aprobacion'];
    $comentario = $_POST['comentario'] ?? 'Cambio de estado de aprobación';
    $dirigido_rh = $_POST['dirigido_rh'] ?? null;  // ← YA TIENES ESTO

    error_log("Datos: ID=$id, Nueva Aprobación=$nueva_aprobacion, Dirigido RH=$dirigido_rh");

    try {
        // Obtener aprobación anterior
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

        error_log("Aprobación anterior: $aprobacion_anterior");

        // ✅ ACTUALIZAR SOLICITUD CON LÓGICA CONDICIONAL
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
        
        if (!oci_execute($stmtUpd)) {
            $error = oci_error($stmtUpd);
            throw new Exception("Error actualizando solicitud: " . $error['message']);
        }
        oci_free_statement($stmtUpd);

        error_log("Solicitud actualizada");

        // Insertar en historial (resto del código igual)
        $queryHistorial = "INSERT INTO ROY_HISTORICO_SOLICITUD 
            (ID_SOLICITUD, APROBACION_ANTERIOR, APROBACION_NUEVA, COMENTARIO_NUEVO, FECHA_CAMBIO)
            VALUES (:id_solicitud, :aprobacion_anterior, :aprobacion_nueva, :comentario, SYSDATE)";
        $stmtHist = oci_parse($conn, $queryHistorial);
        oci_bind_by_name($stmtHist, ':id_solicitud', $id);
        oci_bind_by_name($stmtHist, ':aprobacion_anterior', $aprobacion_anterior);
        oci_bind_by_name($stmtHist, ':aprobacion_nueva', $nueva_aprobacion);
        oci_bind_by_name($stmtHist, ':comentario', $comentario);
        
        if (!oci_execute($stmtHist)) {
            $error = oci_error($stmtHist);
            throw new Exception("Error insertando historial: " . $error['message']);
        }
        oci_free_statement($stmtHist);

        error_log("Historial insertado");

        $mensaje = 'Estado de aprobación actualizado correctamente de "' . $aprobacion_anterior . '" a "' . $nueva_aprobacion . '"';
        if ($dirigido_rh) {
            $mensaje .= ' y asignado a: ' . $dirigido_rh;
        }

        echo json_encode([
            'success' => true,
            'mensaje' => $mensaje
        ]);

    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    oci_close($conn);
    break;

// ✅ NUEVO CASE: OBTENER LISTAS PARA DROPDOWNS
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

        default:
            error_log("Action no reconocida: " . $_GET['action']);
            echo json_encode(['success' => false, 'error' => 'Acción no reconocida']);
            break;
    }
} else {
    error_log("No se proporcionó action");
    echo json_encode(['success' => false, 'error' => 'No se proporcionó acción']);
}
?>