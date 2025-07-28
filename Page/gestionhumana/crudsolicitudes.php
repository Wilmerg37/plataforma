<?php
include_once '../../Funsiones/conexion.php';

$conn = Oracle();
if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    die("Error de conexión a la base de datos.");
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {

        case 'get_solicitudes':
            $query = "SELECT 
                        s.NUM_TIENDA,
                        s.PUESTO_SOLICITADO,
                        s.ESTADO_SOLICITUD,
                        TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD,
                        s.SOLICITADO_POR
                      FROM ROY_SOLICITUD_PERSONAL s
                      ORDER BY s.FECHA_SOLICITUD DESC";

            $stmt = oci_parse($conn, $query);
            oci_execute($stmt);

            $solicitudes = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $solicitudes[] = [
                    'NUM_TIENDA' => $row['NUM_TIENDA'],
                    'PUESTO_SOLICITADO' => $row['PUESTO_SOLICITADO'],
                    'ESTADO_SOLICITUD' => $row['ESTADO_SOLICITUD'],
                    'FECHA_SOLICITUD' => $row['FECHA_SOLICITUD'],
                    'SOLICITADO_POR' => $row['SOLICITADO_POR']
                ];
            }

            oci_free_statement($stmt);
            oci_close($conn);

            header('Content-Type: application/json');
            echo json_encode($solicitudes);
            break;

        case 'add_solicitud':
            $tienda_no = $_POST['tienda_no'];
            $puesto = $_POST['puesto'];
            $solicitado_por = $_POST['solicitado_por'];

            $query = "INSERT INTO ROY_SOLICITUD_PERSONAL (
                        NUM_TIENDA,
                        PUESTO_SOLICITADO,
                        ESTADO_SOLICITUD,
                        FECHA_SOLICITUD,
                        SOLICITADO_POR
                      ) VALUES (
                        :tienda_no,
                        :puesto,
                        'Pendiente',
                        SYSDATE,
                        :solicitado_por
                      )";

            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':tienda_no', $tienda_no);
            oci_bind_by_name($stmt, ':puesto', $puesto);
            oci_bind_by_name($stmt, ':solicitado_por', $solicitado_por);

            if (oci_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                $e = oci_error($stmt);
                echo json_encode(['success' => false, 'error' => $e['message']]);
            }

            oci_free_statement($stmt);
            oci_close($conn);
            break;

        case 'toggle_solicitud_status':
            $tienda_no = $_POST['tienda_no'];
            $fecha_solicitud = $_POST['fecha_solicitud']; // Debe venir como 'DD-MM-YYYY'
            $nuevo_estado = $_POST['nuevo_estado']; // e.g. 'Aprobada', 'Cancelada'

            $query = "UPDATE ROY_SOLICITUD_PERSONAL
                      SET ESTADO_SOLICITUD = :nuevo_estado
                      WHERE NUM_TIENDA = :tienda_no
                        AND TRUNC(FECHA_SOLICITUD) = TO_DATE(:fecha_solicitud, 'DD-MM-YYYY')";

            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':nuevo_estado', $nuevo_estado);
            oci_bind_by_name($stmt, ':tienda_no', $tienda_no);
            oci_bind_by_name($stmt, ':fecha_solicitud', $fecha_solicitud);

            if (oci_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => oci_error($stmt)]);
            }

            oci_free_statement($stmt);
            oci_close($conn);
            break;

            case 'update_solicitud':
    $tienda_no = $_POST['tienda_no'];
    $puesto = $_POST['puesto'];
    $solicitado_por = $_POST['solicitado_por'];
    $fecha_original = $_POST['fecha_original']; // Para encontrar la solicitud original

    $query = "UPDATE ROY_SOLICITUD_PERSONAL
              SET NUM_TIENDA = :tienda_no,
                  PUESTO_SOLICITADO = :puesto,
                  SOLICITADO_POR = :solicitado_por,
                  FECHA_MODIFICACION = SYSDATE
              WHERE FECHA_SOLICITUD = TO_DATE(:fecha_original, 'DD-MM-YYYY HH24:MI:SS')";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':tienda_no', $tienda_no);
    oci_bind_by_name($stmt, ':puesto', $puesto);
    oci_bind_by_name($stmt, ':solicitado_por', $solicitado_por);
    oci_bind_by_name($stmt, ':fecha_original', $fecha_original);

    if (oci_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        $e = oci_error($stmt);
        echo json_encode(['success' => false, 'error' => $e['message']]);
    }

    oci_free_statement($stmt);
    oci_close($conn);
    break;


        default:
            http_response_code(400);
            echo json_encode(['error' => 'Acción no válida.']);
            break;
    }
}
?>
