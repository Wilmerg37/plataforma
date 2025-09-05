<?php
include_once '../../Funsiones/conexion.php';

// Función para enviar respuesta JSON
function sendJsonResponse($success, $message = '', $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Función para validar datos de entrada
function validateInput($data, $required_fields) {
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = "El campo {$field} es obligatorio";
        }
    }
    
    return $errors;
}

// Función para verificar si existe un vendedor
function vendedorExists($conn, $codigo_vendedor) {
    $query = "SELECT COUNT(*) as count FROM ROY_VENDEDORES_FRIED WHERE codigo_vendedor = :codigo_vendedor";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':codigo_vendedor', $codigo_vendedor);
    oci_execute($stmt);
    
    $row = oci_fetch_assoc($stmt);
    $exists = $row['COUNT'] > 0;
    
    oci_free_statement($stmt);
    return $exists;
}

$conn = Oracle();
if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    sendJsonResponse(false, "Error de conexión a la base de datos.");
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_employees':
            try {
                $query = "SELECT s.store_no AS tienda_no, 
                                 vf.codigo_vendedor, 
                                 vf.nombre, 
                                 vf.puesto, 
                                 vf.activo, 
                                 TO_char(vf.fecha_ingreso, 'DD-MM-YYYY') AS fecha_ingreso
                          FROM ROY_VENDEDORES_FRIED vf
                          INNER JOIN RPS.STORE s ON vf.tienda = s.store_no
                          INNER JOIN RPS.subsidiary sb ON s.sbs_sid = sb.sid AND vf.sbs = sb.sbs_no
                          ORDER BY s.store_no, 
                                   DECODE(VF.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),
                                   vf.fecha_ingreso ASC";
                
                $stmt = oci_parse($conn, $query);
                if (!oci_execute($stmt)) {
                    $error = oci_error($stmt);
                    throw new Exception("Error en consulta: " . $error['message']);
                }

                $employees = [];
                while ($row = oci_fetch_assoc($stmt)) {
                    $employees[] = [
                        'TIENDA_NO' => $row['TIENDA_NO'],
                        'CODIGO_VENDEDOR' => $row['CODIGO_VENDEDOR'],
                        'NOMBRE' => $row['NOMBRE'],
                        'PUESTO' => $row['PUESTO'],
                        'ACTIVO' => $row['ACTIVO'] == 1 ? 'Sí' : 'No',
                        'FECHA_INGRESO' => $row['FECHA_INGRESO']
                    ];
                }

                oci_free_statement($stmt);
                oci_close($conn);

                header('Content-Type: application/json');
                echo json_encode($employees);
                
            } catch (Exception $e) {
                error_log("Error get_employees: " . $e->getMessage());
                sendJsonResponse(false, "Error al obtener los empleados: " . $e->getMessage());
            }
            break;

        case 'add_employee':
            try {
                // Validar campos obligatorios
                $required_fields = ['tienda_no', 'codigo_vendedor', 'nombre', 'puesto', 'fecha_ingreso'];
                $validation_errors = validateInput($_POST, $required_fields);
                
                if (!empty($validation_errors)) {
                    sendJsonResponse(false, implode(', ', $validation_errors));
                }

                $tienda_no = trim($_POST['tienda_no']);
                $codigo_vendedor = trim($_POST['codigo_vendedor']);
                $nombre = trim($_POST['nombre']);
                $puesto = trim($_POST['puesto']);
                $fecha_ingreso = trim($_POST['fecha_ingreso']);

                // Validaciones adicionales
                if (!is_numeric($tienda_no)) {
                    sendJsonResponse(false, "El número de tienda debe ser numérico");
                }

                if (!is_numeric($codigo_vendedor)) {
                    sendJsonResponse(false, "El código de vendedor debe ser numérico");
                }

                if (strlen($nombre) < 3) {
                    sendJsonResponse(false, "El nombre debe tener al menos 3 caracteres");
                }

                // Verificar que el código de vendedor no exista
                if (vendedorExists($conn, $codigo_vendedor)) {
                    sendJsonResponse(false, "Ya existe un vendedor con el código {$codigo_vendedor}. Por favor, utilice un código diferente.");
                }

                // Validar formato de fecha (DD-MM-YYYY)
                $date_parts = explode('-', $fecha_ingreso);
                if (count($date_parts) !== 3 || !checkdate($date_parts[1], $date_parts[0], $date_parts[2])) {
                    sendJsonResponse(false, "Formato de fecha inválido. Use DD-MM-YYYY");
                }

                // Validar que la tienda exista
                $check_store_query = "SELECT COUNT(*) as count FROM RPS.STORE WHERE store_no = :tienda_no";
                $check_store_stmt = oci_parse($conn, $check_store_query);
                oci_bind_by_name($check_store_stmt, ':tienda_no', $tienda_no);
                oci_execute($check_store_stmt);
                $store_row = oci_fetch_assoc($check_store_stmt);
                
                if ($store_row['COUNT'] == 0) {
                    oci_free_statement($check_store_stmt);
                    sendJsonResponse(false, "La tienda número {$tienda_no} no existe en el sistema");
                }
                oci_free_statement($check_store_stmt);

                // Insertar nuevo vendedor
                $query = "INSERT INTO ROY_VENDEDORES_FRIED (SBS, tienda, codigo_vendedor, nombre, puesto, activo, fecha_ingreso)
                          VALUES ('1', :tienda_no, :codigo_vendedor, :nombre, :puesto, '1', TO_DATE(:fecha_ingreso, 'DD-MM-YYYY'))";
                
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':tienda_no', $tienda_no);
                oci_bind_by_name($stmt, ':codigo_vendedor', $codigo_vendedor);
                oci_bind_by_name($stmt, ':nombre', $nombre);
                oci_bind_by_name($stmt, ':puesto', $puesto);
                oci_bind_by_name($stmt, ':fecha_ingreso', $fecha_ingreso);

                if (oci_execute($stmt)) {
                    oci_free_statement($stmt);
                    oci_close($conn);
                    sendJsonResponse(true, "Vendedor creado exitosamente");
                } else {
                    $error = oci_error($stmt);
                    oci_free_statement($stmt);
                    throw new Exception("Error al crear vendedor: " . $error['message']);
                }
                
            } catch (Exception $e) {
                error_log("Error add_employee: " . $e->getMessage());
                sendJsonResponse(false, "Error al crear el empleado: " . $e->getMessage());
            }
            break;

        case 'update_employee':
            try {
                // Validar campos obligatorios
                $required_fields = ['tienda_no', 'codigo_vendedor', 'nombre', 'puesto', 'activo', 'fecha_ingreso'];
                $validation_errors = validateInput($_POST, $required_fields);
                
                if (!empty($validation_errors)) {
                    sendJsonResponse(false, implode(', ', $validation_errors));
                }

                $tienda_no = trim($_POST['tienda_no']);
                $codigo_vendedor = trim($_POST['codigo_vendedor']);
                $nombre = trim($_POST['nombre']);
                $puesto = trim($_POST['puesto']);
                $activo = $_POST['activo'];
                $fecha_ingreso = trim($_POST['fecha_ingreso']);

                // Validaciones adicionales
                if (!is_numeric($tienda_no)) {
                    sendJsonResponse(false, "El número de tienda debe ser numérico");
                }

                if (!is_numeric($codigo_vendedor)) {
                    sendJsonResponse(false, "El código de vendedor debe ser numérico");
                }

                if (strlen($nombre) < 3) {
                    sendJsonResponse(false, "El nombre debe tener al menos 3 caracteres");
                }

                // Validar formato de fecha
                $date_parts = explode('-', $fecha_ingreso);
                if (count($date_parts) !== 3 || !checkdate($date_parts[1], $date_parts[0], $date_parts[2])) {
                    sendJsonResponse(false, "Formato de fecha inválido. Use DD-MM-YYYY");
                }

                // Verificar que el vendedor exista
                if (!vendedorExists($conn, $codigo_vendedor)) {
                    sendJsonResponse(false, "El vendedor con código {$codigo_vendedor} no existe");
                }

                $query = "UPDATE ROY_VENDEDORES_FRIED
                          SET tienda = :tienda_no, 
                              nombre = :nombre,
                              puesto = :puesto, 
                              activo = :activo, 
                              fecha_ingreso = TO_DATE(:fecha_ingreso, 'DD-MM-YYYY') 
                          WHERE codigo_vendedor = :codigo_vendedor";

                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':tienda_no', $tienda_no);
                oci_bind_by_name($stmt, ':codigo_vendedor', $codigo_vendedor);
                oci_bind_by_name($stmt, ':nombre', $nombre);
                oci_bind_by_name($stmt, ':puesto', $puesto);
                oci_bind_by_name($stmt, ':activo', $activo);
                oci_bind_by_name($stmt, ':fecha_ingreso', $fecha_ingreso);

                if (oci_execute($stmt)) {
                    oci_free_statement($stmt);
                    oci_close($conn);
                    sendJsonResponse(true, "Vendedor actualizado exitosamente");
                } else {
                    $error = oci_error($stmt);
                    oci_free_statement($stmt);
                    throw new Exception("Error al actualizar vendedor: " . $error['message']);
                }
                
            } catch (Exception $e) {
                error_log("Error update_employee: " . $e->getMessage());
                sendJsonResponse(false, "Error al actualizar el empleado: " . $e->getMessage());
            }
            break;

        case 'toggle_employee_status':
            try {
                if (!isset($_POST['codigo_vendedor']) || !isset($_POST['activo'])) {
                    sendJsonResponse(false, "Parámetros faltantes");
                }

                $codigo_vendedor = trim($_POST['codigo_vendedor']);
                $activo = $_POST['activo'];

                if (!is_numeric($codigo_vendedor)) {
                    sendJsonResponse(false, "Código de vendedor inválido");
                }

                // Verificar que el vendedor exista
                if (!vendedorExists($conn, $codigo_vendedor)) {
                    sendJsonResponse(false, "El vendedor no existe");
                }

                $query = "UPDATE ROY_VENDEDORES_FRIED 
                          SET activo = :activo 
                          WHERE codigo_vendedor = :codigo_vendedor";

                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':activo', $activo);
                oci_bind_by_name($stmt, ':codigo_vendedor', $codigo_vendedor);

                if (oci_execute($stmt)) {
                    oci_free_statement($stmt);
                    oci_close($conn);
                    echo 'true';
                } else {
                    oci_free_statement($stmt);
                    echo 'false';
                }
                
            } catch (Exception $e) {
                error_log("Error toggle_employee_status: " . $e->getMessage());
                echo 'false';
            }
            break;

        case 'delete_employee':
            try {
                if (!isset($_POST['codigo_vendedor'])) {
                    sendJsonResponse(false, "Código de vendedor requerido");
                }

                $codigo_vendedor = trim($_POST['codigo_vendedor']);

                if (!is_numeric($codigo_vendedor)) {
                    sendJsonResponse(false, "Código de vendedor inválido");
                }

                // Verificar que el vendedor exista
                if (!vendedorExists($conn, $codigo_vendedor)) {
                    sendJsonResponse(false, "El vendedor no existe");
                }

                // Verificar si el vendedor tiene registros relacionados (opcional)
                // Puedes agregar aquí validaciones adicionales si hay tablas relacionadas

                $query = "DELETE FROM ROY_VENDEDORES_FRIED WHERE codigo_vendedor = :codigo_vendedor";
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ':codigo_vendedor', $codigo_vendedor);

                if (oci_execute($stmt)) {
                    oci_free_statement($stmt);
                    oci_close($conn);
                    sendJsonResponse(true, "Vendedor eliminado exitosamente");
                } else {
                    $error = oci_error($stmt);
                    oci_free_statement($stmt);
                    throw new Exception("Error al eliminar vendedor: " . $error['message']);
                }
                
            } catch (Exception $e) {
                error_log("Error delete_employee: " . $e->getMessage());
                sendJsonResponse(false, "Error al eliminar el empleado: " . $e->getMessage());
            }
            break;

        default:
            http_response_code(400);
            sendJsonResponse(false, "Acción no válida");
            break;
    }
} else {
    http_response_code(400);
    sendJsonResponse(false, "No se especificó ninguna acción");
}
?>