<?php
require_once "../../Funsiones/consulta.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Solo se permite POST');
}

// Si la petición es para eliminar el registro
if (isset($_POST['modo']) && $_POST['modo'] === 'eliminar') {
  $id = $_POST['id_registro'] ?? null;

  if (!$id) {
    http_response_code(400);
    exit('Falta ID para eliminar');
  }

  $query = "DELETE FROM ROY_HORARIO_TDS WHERE ID_REGISTRO = :id";
  $params = [':id' => $id];

  $resultado = consultaOracle(4, $query, $params);

  echo $resultado ? "OK" : "Error al eliminar en base de datos";
  exit;
}

// Datos para actualización
$id = $_POST['id_registro'] ?? null;
$justificacion = $_POST['justificacion'] ?? null;
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;
$hora_ingreso = $_POST['gto_hora_ingreso'] ?? null;
$hora_salida = $_POST['gto_hora_salida'] ?? null;
$etiqueta = $_POST['etiqueta'] ?? null;

if (!$id || $justificacion === null) {
  http_response_code(400);
  exit('Faltan datos');
}

// 🔥 CAMBIO: Validar que si es "OTROS" debe tener texto en justificación
if ($etiqueta == '15' && (empty($justificacion) || trim($justificacion) === '')) {
  http_response_code(400);
  exit('Debe proporcionar una razón cuando selecciona OTROS.');
}

// Actualización según tipo de justificación
$requiere_fechas = ['SUSPENSION LABORAL', 'VACACIONES','SUSPENSION IGSS'];

if (in_array($justificacion, $requiere_fechas)) {
  if (empty($fecha_inicio) || empty($fecha_fin)) {
    http_response_code(400);
    exit('Debe proporcionar fecha de inicio y fin para esta justificación.');
  }

  $query = "UPDATE ROY_HORARIO_TDS
            SET JUSTIFICACION = :justificacion,
                FECHA_INICIO = TO_DATE(:fecha_inicio, 'YYYY-MM-DD'),
                FECHA_FIN = TO_DATE(:fecha_fin, 'YYYY-MM-DD'),
                FECHA_JUSTIFICACION = SYSDATE,
                ETIQUETA = :etiqueta
            WHERE ID_REGISTRO = :id";

  $params = [
    ':justificacion' => $justificacion,
    ':fecha_inicio' => $fecha_inicio,
    ':fecha_fin' => $fecha_fin,
    ':etiqueta' => $etiqueta,
    ':id' => $id
  ];

} elseif (in_array($justificacion, ['CITA IGSS', 'GTO PRESENCIAL', 'GTO VIRTUAL', 'TV PRESENCIAL', 'TV VIRTUAL','REUNION GTS', 'REUNION ASS', 'COBERTURA','LACTANCIA']) || $etiqueta == '15') {
  // 🔥 CAMBIO: Agregué || $etiqueta == '15' para incluir OTROS en validación de horas
  
  if (empty($hora_ingreso) || empty($hora_salida)) {
    http_response_code(400);
    exit('Debe proporcionar hora de ingreso y salida para esta justificación.');
  }

  $query = "UPDATE ROY_HORARIO_TDS
            SET JUSTIFICACION = :justificacion,
                HORA_IN = :hora_ingreso,
                HORA_OUT = :hora_salida,
                FECHA_INICIO = NULL,
                FECHA_FIN = NULL,
                FECHA_JUSTIFICACION = SYSDATE,
                ETIQUETA = :etiqueta
            WHERE ID_REGISTRO = :id";

  $params = [
    ':justificacion' => $justificacion,
    ':hora_ingreso' => $hora_ingreso,
    ':hora_salida' => $hora_salida,
    ':etiqueta' => $etiqueta,
    ':id' => $id
  ];

} else {
  // Para justificaciones que no requieren fechas ni horas
  $query = "UPDATE ROY_HORARIO_TDS
            SET JUSTIFICACION = :justificacion,
                FECHA_INICIO = NULL,
                FECHA_FIN = NULL,
                HORA_IN = NULL,
                HORA_OUT = NULL,
                FECHA_JUSTIFICACION = SYSDATE,
                ETIQUETA = :etiqueta
            WHERE ID_REGISTRO = :id";

  $params = [
    ':justificacion' => $justificacion,
    ':etiqueta' => $etiqueta,
    ':id' => $id
  ];
}

$resultado = consultaOracle(4, $query, $params);

// 🔥 CAMBIO: Mejoré la condición para el cálculo de horas
if ($resultado && $justificacion !== null && !in_array($justificacion, $requiere_fechas)) {
  // Obtener datos de la semana y empleado
  $querySemana = "SELECT SEMANA, CODIGO_EMPL FROM ROY_HORARIO_TDS WHERE ID_REGISTRO = :id";
  $datos = consultaOracle(5, $querySemana, [':id' => $id]);

  error_log(print_r($datos, true));

  if (!empty($datos)) {
    $semana = $datos[0]['SEMANA'];
    $codigoEmpl = $datos[0]['CODIGO_EMPL'];

    // Sumar las horas de la semana
    $queryHoras = "
      SELECT 
        SUM((TO_DATE(HORA_OUT, 'HH24:MI') - TO_DATE(HORA_IN, 'HH24:MI')) * 24) AS TOTAL_HORAS,
        MAX(HORA_ALM_S) AS HORA_ALM, 
        MAX(HORA_LEY_S) AS HORA_LEY
      FROM ROY_HORARIO_TDS
      WHERE SEMANA = :semana AND CODIGO_EMPL = :codigo_empl
       AND HORA_IN IS NOT NULL 
        AND HORA_OUT IS NOT NULL
        AND HORA_IN <> '00:00'
        AND HORA_OUT <> '00:00'
    ";

    $resHoras = consultaOracle(5, $queryHoras, [
      ':semana' => $semana,
      ':codigo_empl' => $codigoEmpl
    ]);

    if (!empty($resHoras)) {
      $totalHoras = floatval($resHoras[0]['TOTAL_HORAS']);
      $horaAlmuerzo = floatval($resHoras[0]['HORA_ALM']);
      $horaley = floatval($resHoras[0]['HORA_LEY']);

      $horaTotS = $totalHoras - $horaAlmuerzo;   //YA  RESTO HORAS ALMUERZO
      //$horaExtraS = max(0, $horaTotS - $horaley - $horaAlmuerzo); QUITO FUNSION PARA EXTRAS
        $horaExtraS =  $horaTotS - $horaley ; 

      // Actualizar todos los registros de esa semana y empleado
      $updateTotales = "
        UPDATE ROY_HORARIO_TDS
        SET HORA_TOT_S = :hora_tot,
            HORA_EXTRA_S = :hora_extra
        WHERE SEMANA = :semana AND CODIGO_EMPL = :codigo_empl
      ";

      $paramsTotales = [
        ':hora_tot' => $horaTotS,
        ':hora_extra' => $horaExtraS,
        ':semana' => $semana,
        ':codigo_empl' => $codigoEmpl
      ];

      consultaOracle(4, $updateTotales, $paramsTotales);
    }
  }
}

// 🟢 Final
if ($resultado) {
  echo "OK";
} else {
  http_response_code(500);
  echo "Error al guardar en base de datos";
}
?>