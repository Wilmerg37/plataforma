<?php
include_once '../Funsiones/conexion.php';  // Incluye el archivo de conexión con Oracle


$conn = Oracle();  //  función Oracle para obtener la conexión OCI8

if (!$conn) {
    error_log("Error de conexión a la base de datos.");
    die("Error de conexión a la base de datos.");
}

// Manejo de selección dinámica de SUPERVISOR y TIENDA
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Obtener supervisores
    if ($action === 'get_supervisors') {
        $query = "SELECT udf1_string AS SUPERVISOR_ID, MAX(udf2_string) AS SUPERVISOR_NAME
                    FROM RPS.STORE
                    WHERE udf1_string IS NOT NULL AND  SBS_SID = '680861302000159257'
                    GROUP BY udf1_string
                    ORDER BY udf1_string";
        $stmt = oci_parse($conn, $query);
        oci_execute($stmt);

        $supervisors = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $supervisors[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        header('Content-Type: application/json');
        echo json_encode($supervisors);
        exit;
    }

    // Obtener tiendas basadas en el supervisor seleccionado
    if ($action === 'get_stores' && isset($_GET['supervisor_id'])) {
        $supervisor_id = $_GET['supervisor_id'];
        $query = "SELECT STORE_NO FROM RPS.STORE WHERE udf1_string = :supervisor_id ORDER BY STORE_NO";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':supervisor_id', $supervisor_id);
        oci_execute($stmt);

        $stores = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $stores[] = $row;
        }

        oci_free_statement($stmt);
        oci_close($conn);

        header('Content-Type: application/json');
        echo json_encode($stores);
        exit;
    }

    // Obtener empleados basados en la tienda seleccionada
    if ($action === 'get_employees' && isset($_GET['store_no'])) {
        $store_no = $_GET['store_no'];
        error_log("Obteniendo empleados para la tienda: $store_no");

        $query = "SELECT CODIGO_VENDEDOR EMPL_NAME, NOMBRE FULL_NAME , PUESTO FROM ROY_VENDEDORES_FRIED
                  WHERE TIENDA = :store_no AND ACTIVO = 1
                   ORDER BY DECODE(PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4), fecha_ingreso ";
          $stmt = oci_parse($conn, $query);
          oci_bind_by_name($stmt, ':store_no', $store_no);
      
          // Verificar si la consulta se preparó correctamente
          if (!$stmt) {
              $e = oci_error($conn);
              error_log("Error en la consulta de empleados: " . $e['message']);
              die("Error en la consulta de empleados.");
          }
      
          $result = oci_execute($stmt);
      
          // Verificar si la consulta se ejecutó correctamente
          if (!$result) {
              $e = oci_error($stmt);
              error_log("Error al ejecutar la consulta de empleados: " . $e['message']);
              die("Error al ejecutar la consulta de empleados: " . $e['message']);
          }
      
          $employees = [];
          while ($row = oci_fetch_assoc($stmt)) {
              $employees[] = $row;
          }
      
          error_log("Empleados recuperados: " . print_r($employees, true));  // Mostrar los empleados obtenidos
      
          // Verificar si se obtuvieron empleados
          if (empty($employees)) {
              error_log("No se encontraron empleados para la tienda: $store_no");
          }
      
          oci_free_statement($stmt);
          oci_close($conn);
      
          header('Content-Type: application/json');
          echo json_encode($employees);
          exit;
      }

      if ($action === 'get_horas_e' && isset($_GET['store_no'])) {
    $store_no = $_GET['store_no'];
    error_log("Obteniendo HORAS_E para la tienda: $store_no");

    $query = "SELECT MIN(H_EXTRAS) AS HORAS_E FROM ROY_HORARIOS_CC_TIENDAS WHERE TIENDA = :store_no";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':store_no', $store_no);

    if (!$stmt) {
        $e = oci_error($conn);
        error_log("Error preparando la consulta de HORAS_E: " . $e['message']);
        die("Error preparando la consulta de HORAS_E.");
    }

    $result = oci_execute($stmt);

    if (!$result) {
        $e = oci_error($stmt);
        error_log("Error al ejecutar la consulta de HORAS_E: " . $e['message']);
        die("Error al ejecutar la consulta de HORAS_E: " . $e['message']);
    }

    $horas_e = null;
    if ($row = oci_fetch_assoc($stmt)) {
        $horas_e = $row;
    }

    error_log("HORAS_E recuperadas: " . print_r($horas_e, true));

    oci_free_statement($stmt);
    oci_close($conn);

    header('Content-Type: application/json');
    echo json_encode($horas_e);
    exit;
}

}


// VALIDAR si ya hay horarios asignados para la tienda, semana y año
if (isset($_GET['action']) && $_GET['action'] === 'check_schedule') {
    $store_no = $_GET['store_no'];
    $fecha = $_GET['fecha']; // Fecha base (inicio de semana)

    // Calcular semana y año basados en la fecha +1 semana
    $fecha_base = date('Y-m-d', strtotime($fecha . ' +1 week'));
    $semana = date('W', strtotime($fecha_base));
    $anio = date('Y', strtotime($fecha_base));

    $query = "SELECT COUNT(*) AS TOTAL FROM ROY_HORARIO_TDS 
              WHERE TIENDA = :tienda AND SEMANA = :semana AND ANIO = :anio";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':tienda', $store_no);
    oci_bind_by_name($stmt, ':semana', $semana);
    oci_bind_by_name($stmt, ':anio', $anio);
    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    $total = $row['TOTAL'];

    oci_free_statement($stmt);
    oci_close($conn);

    header('Content-Type: application/json');
    echo json_encode(['total' => $total]);
    exit;
}


// Verificar si se ha enviado el formulario para insertar horas de empleados
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $store_no = $_POST['store_no'];  // Número de tienda
    $fecha = $_POST['fecha'];        // Fecha seleccionada
    $empleados = $_POST['employees']; // Array de empleados con horas

    // Preparar el INSERT para cada empleado
    $query = "INSERT INTO ROY_HORARIO_TDS (TIENDA, CODIGO_EMPL, NOMBRE_EMPL, FECHA, DIA, SEMANA, HORA_IN, HORA_OUT, HORA_TOT_S, HORA_LEY_S, HORA_ALM_S, HORA_EXTRA_S, ETIQUETA)
              VALUES (:tienda, :codigo_empl, :nombre_empl, :fecha, :dia, :semana, :hora_in, :hora_out, :hora_tot_s, :hora_ley_s, :hora_alm_s, :hora_extra_s, :etiqueta)";

foreach ($empleados as $empleado) {
    $codigo_empl = isset($empleado['codigo_emp']) && is_numeric(trim($empleado['codigo_emp'])) ? trim($empleado['codigo_emp']) : null;

    $nombre_empl = $empleado['nombre_emp'];



        // Verificar que 'codigo_emp' tiene un valor válido y no es null
        if (is_null($codigo_empl)) {
            error_log("Código empleado no definido o inválido para el empleado: $nombre_empl");
            continue;  // Si el código del empleado no existe, pasar al siguiente
        }
    
        // Confirmar en el log el valor correcto antes de la inserción
        error_log("Insertando datos para el empleado: $nombre_empl (Código: $codigo_empl)");
    
    // Definir una lista de días para acceder a las horas de cada día
    $dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    foreach ($dias as $dia) {
        // Validar si existen los índices para el día actual
        if (isset($empleado[$dia]['hora_in']) && isset($empleado[$dia]['hora_out'])) {
            $hora_in = $empleado[$dia]['hora_in'];
            $hora_out = $empleado[$dia]['hora_out'];
            $etiqueta = isset($empleado[$dia]['etiqueta']) && is_numeric($empleado[$dia]['etiqueta']) ? intval($empleado[$dia]['etiqueta']) : 0;

        } else {
            // Si faltan las horas para algún día, continuar con el siguiente día
            error_log("Horas no definidas para $dia en el empleado $nombre_empl ($codigo_empl)");
            continue;
        }

        // Calcular el día de la semana y la semana
        $dia_semana = ucfirst($dia);  // Capitalizar el nombre del día
      //  $semana = getWeekNumber($fecha)  
        $semana = date('W', strtotime (date('Y-m-d', strtotime($fecha . ' +1 week'))));  // Número de la semana basado en la fecha
        $fecha_del_dia = $_POST['fechas']['dia_' . $dia];  //  Fecha real de cada día

        

        // Asignar y validar el valor para las horas extras y otras métricas
        $hora_tot = isset($empleado['hora_tot_s']) && is_numeric($empleado['hora_tot_s']) ? floatval($empleado['hora_tot_s']) : 0;
        $hora_ley = isset($empleado['hora_ley_s']) && is_numeric($empleado['hora_ley_s']) ? floatval($empleado['hora_ley_s']) : 0;
        $hora_alm = isset($empleado['hora_alm_s']) && is_numeric($empleado['hora_alm_s']) ? floatval($empleado['hora_alm_s']) : 0;
        $hora_extra = isset($empleado['hora_extra_s']) && is_numeric($empleado['hora_extra_s']) ? floatval($empleado['hora_extra_s']) : 0;

                        // Verificar si ya existen datos para este empleado, tienda y fecha
                        $checkQuery = "SELECT COUNT(*) AS TOTAL FROM ROY_HORARIO_TDS 
                                    WHERE TIENDA = :tienda AND CODIGO_EMPL = :codigo_empl AND FECHA = TO_DATE(:fecha, 'YYYY-MM-DD')";
                        $checkStmt = oci_parse($conn, $checkQuery);
                        oci_bind_by_name($checkStmt, ':tienda', $store_no);
                        oci_bind_by_name($checkStmt, ':codigo_empl', $codigo_empl);
                        oci_bind_by_name($checkStmt, ':fecha', $fecha_del_dia);

                        oci_execute($checkStmt);
                        $row = oci_fetch_assoc($checkStmt);
                        $total = $row['TOTAL'];

                        oci_free_statement($checkStmt);

                        if ($total > 0) {
                            error_log("Ya existen datos para $nombre_empl ($codigo_empl) en la fecha $fecha_del_dia");
                            echo "Ya existen datos para el empleado $nombre_empl en la tienda $store_no para el día $fecha_del_dia.\n";
                            continue; // No insertar si ya hay datos
                        }




        // Preparar la consulta SQL
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ':tienda', $store_no, SQLT_INT);  // Asegurarse de que tienda sea numérico
        oci_bind_by_name($stmt, ':codigo_empl', $codigo_empl, -1, SQLT_CHR);  // Utilizar SQLT_CHR para cadena completa
        oci_bind_by_name($stmt, ':nombre_empl', $nombre_empl);
        oci_bind_by_name($stmt, ':fecha', $fecha_del_dia);
        oci_bind_by_name($stmt, ':dia', $dia_semana);
        oci_bind_by_name($stmt, ':semana', $semana, SQLT_INT);  // Asegurarse de que semana sea numérico
        oci_bind_by_name($stmt, ':hora_in', $hora_in);
        oci_bind_by_name($stmt, ':hora_out', $hora_out);
        oci_bind_by_name($stmt, ':hora_tot_s', $hora_tot, SQLT_FLT);  // Convertir a flotante
        oci_bind_by_name($stmt, ':hora_ley_s', $hora_ley, SQLT_FLT);  // Convertir a flotante
        oci_bind_by_name($stmt, ':hora_alm_s', $hora_alm, SQLT_FLT);  // Convertir a flotante
        oci_bind_by_name($stmt, ':hora_extra_s', $hora_extra, SQLT_FLT);  // Convertir a flotante
        oci_bind_by_name($stmt, ':etiqueta', $etiqueta, SQLT_INT);


        $result = oci_execute($stmt);

        if (!$result) {
            $e = oci_error($stmt);
            error_log("Error al insertar: " . htmlentities($e['message']));
            echo "Error al insertar datos del empleado $nombre_empl ($codigo_empl): " . htmlentities($e['message']);
        }
    }
}

    oci_free_statement($stmt);
    oci_close($conn);
    echo "Horas de empleados insertadas correctamente.";
    exit;
}

//
//TRAE LA META DIARIA POR TIENDA Y FECHA
if (isset($_GET['action']) && $_GET['action'] == 'get_goals') {
    $storeNo = $_GET['tienda'];
    $startDate = $_GET['fecha']; // formato: YYYY-MM-DD

    $start = new DateTime($startDate);
    $metas = [];

    for ($i = 0; $i < 7; $i++) {
        $date = $start->format('Y-m-d'); // Oracle espera formato DD/MM/YYYY

        $query = "SELECT ROUND(SUM(META_S_IVA),2) AS meta 
                  FROM ROY_META_DIARIA_TDS 
                  WHERE TIENDA = :tienda AND FECHA = TO_DATE(:fecha, 'YYYY-MM-DD')";
        $stmt = oci_parse($conn, $query);

        oci_bind_by_name($stmt, ':tienda', $storeNo);
        oci_bind_by_name($stmt, ':fecha', $date);

        oci_execute($stmt);
        $goalAmount = 0;

        if ($row = oci_fetch_assoc($stmt)) {
            $goalAmount = $row['META'] ?? 0;
        }

        $day = strtolower($start->format('l')); // 'monday', etc.
      $dias = ['sunday'=>'domingo','monday'=>'lunes','tuesday'=>'martes','wednesday'=>'miercoles','thursday'=>'jueves','friday'=>'viernes','saturday'=>'sabado'];
        $metas[$dias[$day]] = $goalAmount;

        $start->modify('+1 day');
        oci_free_statement($stmt);
    }

    oci_close($conn);
    header('Content-Type: application/json');
    echo json_encode($metas);
    exit;
}


?>
