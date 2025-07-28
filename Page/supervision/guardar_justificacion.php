<?php
require_once "../../Funsiones/consulta.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Solo se permite POST');
}

$id = $_POST['id_registro'] ?? null;
$justificacion = $_POST['justificacion'] ?? null;
$fecha_inicio = $_POST['fecha_inicio'] ?? null;
$fecha_fin = $_POST['fecha_fin'] ?? null;

// Nuevos campos
$hora_ingreso = $_POST['gto_hora_ingreso'] ?? null;
$hora_salida  = $_POST['gto_hora_salida'] ?? null;
$etiqueta     = $_POST['etiqueta'] ?? null; // nuevo campo para guardar etiqueta

if (!$id || $justificacion === null) {
  http_response_code(400);
  exit('Faltan datos');
}

$requiere_fechas = ['SUSPENSION IGSS', 'SUSPENSION LABORAL', 'VACACIONES'];

// Caso 1: Justificaciones que requieren fechas
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

// Caso 2: GTO PRESENCIAL, VIRTUAL, REUNION GTS, REUNION ASS, LACTANCIA (requieren horas y etiqueta)
} elseif (in_array($justificacion, ['GTO PRESENCIAL', 'GTO VIRTUAL', 'TV PRESENCIAL', 'TV VIRTUAL','REUNION GTS', 'COBERTURA','REUNION ASS', 'LACTANCIA'])) {
  if (empty($hora_ingreso) || empty($hora_salida)) {
    http_response_code(400);
    exit('Debe proporcionar hora de ingreso y salida para esta justificación.');
  }

  $query = "UPDATE ROY_HORARIO_TDS 
            SET JUSTIFICACION = :justificacion,
                HORA_JUS_IN = :hora_ingreso,
                HORA_JUS_OUT = :hora_salida,
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

// Caso 3: Otros
} else {
  $query = "UPDATE ROY_HORARIO_TDS 
            SET JUSTIFICACION = :justificacion,
                FECHA_INICIO = NULL,
                FECHA_FIN = NULL,
                HORA_JUS_IN = NULL,
                HORA_JUS_OUT = NULL,
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

if ($resultado) {
  echo "OK";
} else {
  http_response_code(500);
  echo "Error al guardar en base de datos";
}
