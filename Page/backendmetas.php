<?php
include_once '../Funsiones/conexion.php';

$conn = Oracle();

if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    die("Error de conexión a la base de datos.");
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_supervisors':
            $query = "SELECT DISTINCT udf1_string AS SUPERVISOR_ID, udf2_string AS SUPERVISOR_NAME FROM RPS.STORE WHERE udf1_string IS NOT NULL AND SBS_SID = '680861302000159257' ORDER BY udf1_string";
            $stmt = oci_parse($conn, $query);
            oci_execute($stmt);
            $supervisors = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $supervisors[] = $row;
            }
            oci_free_statement($stmt);
            echo json_encode($supervisors);
            break;

        case 'get_stores':
            $supervisor_id = $_GET['supervisor_id'];
            $query = "SELECT STORE_NO, STORE_NAME FROM RPS.STORE WHERE udf1_string = :supervisor_id ORDER BY STORE_NO";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':supervisor_id', $supervisor_id);
            oci_execute($stmt);
            $stores = [];
            while ($row = oci_fetch_assoc($stmt)) {
                $stores[] = $row;
            }
            oci_free_statement($stmt);
            echo json_encode($stores);
            break;

        case 'get_employees':
                $store_no = $_GET['store_no'];
                $semana = $_GET['semana'];
                $anio = $_GET['anio'];
            
                // Verificar si la meta ya existe en ROY_META_SEM_X_VENDEDOR
                $checkMetaQuery = "SELECT COUNT(*) AS META_EXISTS 
                                   FROM ROY_META_SEM_X_VENDEDOR 
                                   WHERE TIENDA = :store_no 
                                     AND SEMANA = :semana 
                                     AND ANIO = :anio";
                $stmtCheck = oci_parse($conn, $checkMetaQuery);
                oci_bind_by_name($stmtCheck, ':store_no', $store_no);
                oci_bind_by_name($stmtCheck, ':semana', $semana);
                oci_bind_by_name($stmtCheck, ':anio', $anio);
                oci_execute($stmtCheck);
                $metaExists = oci_fetch_assoc($stmtCheck)['META_EXISTS'] > 0;
                oci_free_statement($stmtCheck);
            
                if ($metaExists) {
                    // Si la meta existe, ejecutar el query original para obtener los empleados y metas
                    $query = "SELECT CD.TIENDA AS TIENDA, 
                                    VF.CODIGO_VENDEDOR AS EMPL_NAME, 
                                    VF.NOMBRE AS FULL_NAME, 
                                    VF.PUESTO AS TIPO_PUESTO, 
                                    CD.META,
                                    NVL(CD.HORA, 44) AS HORA
                                FROM ROY_VENDEDORES_FRIED VF
                                INNER JOIN RPS.STORE S ON VF.TIENDA = S.STORE_NO
                                INNER JOIN RPS.SUBSIDIARY SB ON S.SBS_SID = SB.SID AND VF.SBS = SB.SBS_NO
                                LEFT JOIN ROY_META_SEM_X_VENDEDOR CD ON CD.CODIGO_EMPLEADO = VF.CODIGO_VENDEDOR 
                                    AND CD.SBS = SB.SBS_NO 
                                    AND CD.TIENDA = S.STORE_NO 
                                    AND CD.SEMANA = :semana
                                    AND CD.ANIO = :anio
                                WHERE 
                                    VF.ACTIVO = 1
                                    AND S.STORE_NO = :store_no
                                ORDER BY DECODE(VF.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),
                                    VF.FECHA_INGRESO ASC";
            
                    $stmt = oci_parse($conn, $query);
                    oci_bind_by_name($stmt, ':store_no', $store_no);
                    oci_bind_by_name($stmt, ':semana', $semana);
                    oci_bind_by_name($stmt, ':anio', $anio);
                    oci_execute($stmt);
            
                    $employees = [];
                    while ($row = oci_fetch_assoc($stmt)) {
                        $employees[] = [
                            'EMPL_NAME' => $row['EMPL_NAME'],
                            'FULL_NAME' => $row['FULL_NAME'],
                            'TIPO_PUESTO' => $row['TIPO_PUESTO'],
                            'HORA' => $row['HORA'],
                            'META' => $row['META'] ?? '0'
                        ];
                    }
                    oci_free_statement($stmt);
                    
                    header('Content-Type: application/json');
                    echo json_encode($employees);
            
                } else {
                    // Si la meta no existe, ejecutar el query para obtener empleados y calcular la meta
                    $query = "SELECT VF.CODIGO_VENDEDOR AS EMPL_NAME, 
                                      VF.NOMBRE AS FULL_NAME, 
                                      VF.PUESTO AS TIPO_PUESTO, 
                                      MT.META AS META_SEMANAL,
                                      CD.HORA AS HORA,
                                   
                                    CASE WHEN VF.PUESTO = 'VACACIONISTA' THEN 0 ELSE    MT.META / (
                                          SELECT COUNT(*) 
                                          FROM (
                                              SELECT VF.CODIGO_VENDEDOR 
                                              FROM ROY_VENDEDORES_FRIED VF
                                              INNER JOIN RPS.STORE S ON VF.TIENDA = S.STORE_NO
                                              INNER JOIN ROY_META_SEM_TDS MT ON S.STORE_NO = MT.TIENDA
                                              INNER JOIN RPS.SUBSIDIARY SB ON S.SBS_SID = SB.SID AND VF.SBS = SB.SBS_NO
                                              WHERE S.STORE_NO = :STORE_NO
                                                AND MT.SEMANA = :SEMANA
                                                AND MT.ANIO = :ANIO
                                                AND VF.PUESTO <>'VACACIONISTA'
                                                AND VF.ACTIVO = 1

                                              GROUP BY VF.CODIGO_VENDEDOR
                                          )
                                      ) END AS META 
                                      
                                      
                                FROM ROY_VENDEDORES_FRIED VF
                                INNER JOIN RPS.STORE S ON VF.TIENDA = S.STORE_NO
                                INNER JOIN ROY_META_SEM_TDS MT ON S.STORE_NO = MT.TIENDA
                                INNER JOIN RPS.SUBSIDIARY SB ON S.SBS_SID = SB.SID AND VF.SBS = SB.SBS_NO
                                LEFT JOIN ROY_META_SEM_X_VENDEDOR CD ON CD.CODIGO_EMPLEADO = VF.CODIGO_VENDEDOR 
                                    AND SB.SBS_NO = CD.SBS 
                                    AND S.STORE_NO = CD.TIENDA 
                                    AND MT.SEMANA = CD.SEMANA
                                WHERE S.STORE_NO = :STORE_NO
                                  AND MT.SEMANA = :SEMANA
                                  AND MT.ANIO = :ANIO
                                   AND VF.ACTIVO = 1

                                GROUP BY VF.CODIGO_VENDEDOR, VF.NOMBRE, VF.PUESTO, MT.META, CD.HORA, VF.FECHA_INGRESO
                                ORDER BY DECODE(VF.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),
                                         VF.FECHA_INGRESO ASC";
            
                    $stmt = oci_parse($conn, $query);
                    oci_bind_by_name($stmt, ':store_no', $store_no);
                    oci_bind_by_name($stmt, ':semana', $semana);
                    oci_bind_by_name($stmt, ':anio', $anio);
                    oci_execute($stmt);
            
                    $employees = [];
                    while ($row = oci_fetch_assoc($stmt)) {
                        $employees[] = [
                            'EMPL_NAME' => $row['EMPL_NAME'],
                            'FULL_NAME' => $row['FULL_NAME'],
                            'TIPO_PUESTO' => $row['TIPO_PUESTO'],
                            'HORA' => $row['HORA'],
                            'META' => $row['META'] ?? '0'
                        ];
                    }
                    oci_free_statement($stmt);
                    
                    header('Content-Type: application/json');
                    echo json_encode($employees);
                }
                break;
            
            

                case 'tile-metas':
                    if (isset($_GET['t'], $_GET['s'], $_GET['a'])) {
                        $storeNo = $_GET['t'];
                        $weekNumber = $_GET['s'];
                        $year = $_GET['a'];
                
                        $query = "SELECT SUM(META) AS TOTAL_META FROM ROY_META_SEM_TDS WHERE TIENDA = :storeNo AND SEMANA = :weekNumber AND ANIO = :year";
                        $stmt = oci_parse($conn, $query);
                        oci_bind_by_name($stmt, ':storeNo', $storeNo);
                        oci_bind_by_name($stmt, ':weekNumber', $weekNumber);
                        oci_bind_by_name($stmt, ':year', $year);
                
                        $result = oci_execute($stmt);
                        if ($result) {
                            $row = oci_fetch_assoc($stmt);
                            if ($row) {
                                $meta = $row['TOTAL_META'];
                                echo json_encode(['meta' => $meta]);
                            } else {
                                echo json_encode(['error' => 'No se encontraron datos de meta']);
                            }
                        } else {
                            $e = oci_error($stmt);
                            echo json_encode(['error' => 'Error en la consulta: ' . $e['message']]);
                        }
                    } else {
                        echo json_encode(['error' => 'Faltan parámetros necesarios']);
                    }
                    break;

         case 'update_meta':
                    $employee_name = $_POST['employee_name'];
                    $meta = $_POST['meta'];
                    $store_no = $_POST['store_no'];
                    $semana = $_POST['semana'];
                    $anio = $_POST['anio'];
                    $tipo = $_POST['tipo'];
                    $hora = $_POST['hora'];

                    $query = "DECLARE
                                        v_count NUMBER;
                                    BEGIN
                                        -- Verificar si ya existe el registro
                                        SELECT COUNT(*) INTO v_count 
                                        FROM ROY_META_SEM_X_VENDEDOR
                                        WHERE CODIGO_EMPLEADO = :employee_name 
                                        AND TIENDA = :store_no 
                                        AND SEMANA = :semana 
                                        AND ANIO = :anio;

                                        -- Eliminar si existe (v_count >= 1)
                                        IF v_count >= 1 THEN
                                            DELETE FROM ROY_META_SEM_X_VENDEDOR
                                            WHERE CODIGO_EMPLEADO = :employee_name 
                                            AND TIENDA = :store_no 
                                            AND SEMANA = :semana 
                                            AND ANIO = :anio;
                                        END IF;

                                        -- Insertar el nuevo registro
                                        INSERT INTO ROY_META_SEM_X_VENDEDOR 
                                            (TIENDA, CODIGO_EMPLEADO, META, SEMANA, TIPO, ANIO, HORA, SBS)
                                        VALUES 
                                            (:store_no, :employee_name, :meta, :semana, :tipo, :anio, :hora, '1');

                                        COMMIT;
                                    END;";

                    $stmt = oci_parse($conn, $query);
                    oci_bind_by_name($stmt, ':meta', $meta);
                    oci_bind_by_name($stmt, ':employee_name', $employee_name);
                    oci_bind_by_name($stmt, ':store_no', $store_no);
                    oci_bind_by_name($stmt, ':semana', $semana);
                    oci_bind_by_name($stmt, ':anio', $anio);
                    oci_bind_by_name($stmt, ':tipo', $tipo);
                    oci_bind_by_name($stmt, ':hora', $hora);

                    oci_execute($stmt);
                    if ($error = oci_error($stmt)) {
                        echo json_encode(['error' => 'Failed to update meta: ' . $error['message']]);
                        oci_rollback($conn);
                    } else {
                        oci_commit($conn);
                        echo json_encode(['success' => 'Meta updated successfully']);
                    }

                    break;

                    case 'save_all_metas':
                        $data = json_decode(file_get_contents("php://input"), true);
                        if (!$data || !isset($data['store_no'], $data['semana'], $data['anio'], $data['metas'])) {
                            echo json_encode(['error' => 'Datos incompletos o mal formados.']);
                            exit;
                        }
                    
                        $store_no = $data['store_no'];
                        $semana = $data['semana'];
                        $anio = $data['anio'];
                        $metas = $data['metas'];
                        $tipo = $data['tipo'];
                        $errors = false;
                    
                        foreach ($metas as $meta) {
                            if (!isset($meta['employee_name'], $meta['meta'], $meta['hours'], $meta['tipo'])) {
                                echo json_encode(['error' => 'Datos incompletos en las metas individuales.']);
                                exit;
                            }
                    
                            $employeeCode = $meta['employee_name'];
                            $meta_value = $meta['meta'];
                            $hours = $meta['hours'];
                            $tipo = $meta['tipo'];
                            $sbs = '1';
                            // Primero elimina los registros existentes
                            $deleteQuery = "DELETE FROM ROY_META_SEM_X_VENDEDOR WHERE CODIGO_EMPLEADO = :employeeCode AND TIENDA = :store_no AND SEMANA = :semana AND ANIO = :anio";
                            $stmtDelete = oci_parse($conn, $deleteQuery);
                            oci_bind_by_name($stmtDelete, ':employeeCode', $employeeCode);
                            oci_bind_by_name($stmtDelete, ':store_no', $store_no);
                            oci_bind_by_name($stmtDelete, ':semana', $semana);
                            oci_bind_by_name($stmtDelete, ':anio', $anio);
                            if (!oci_execute($stmtDelete)) {
                                $errors = true;
                                break;
                            }
                    
                            // Luego inserta el nuevo registro con las horas
                            $insertQuery = "INSERT INTO ROY_META_SEM_X_VENDEDOR (TIENDA, CODIGO_EMPLEADO, META, SEMANA, TIPO, ANIO, HORA, SBS) VALUES (:store_no, :employeeCode, :meta, :semana, :tipo, :anio, :hours, :sbs)";
                            $stmtInsert = oci_parse($conn, $insertQuery);
                            oci_bind_by_name($stmtInsert, ':store_no', $store_no);
                            oci_bind_by_name($stmtInsert, ':employeeCode', $employeeCode);
                            oci_bind_by_name($stmtInsert, ':meta', $meta_value);
                            oci_bind_by_name($stmtInsert, ':hours', $hours);
                            oci_bind_by_name($stmtInsert, ':tipo', $tipo);

                            oci_bind_by_name($stmtInsert, ':semana', $semana);
                            oci_bind_by_name($stmtInsert, ':anio', $anio);
                            oci_bind_by_name($stmtInsert, ':tipo', $tipo);
                            oci_bind_by_name($stmtInsert, ':sbs', $sbs);
                            if (!oci_execute($stmtInsert)) {
                                $errors = true;
                                break;
                            }
                        }
                    
                        if ($errors) {
                            oci_rollback($conn); // Revertir cambios en caso de error
                            echo json_encode(['error' => 'Metas guardadas.']);
                        } else {
                            oci_commit($conn); // Confirmar todos los cambios si todo fue exitoso
                            echo json_encode(['success' => true]);
                        }
                        break;
                    
                    }
              }
                
       



oci_close($conn);
?>

