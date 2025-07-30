<?php
require_once "../../Funsiones/global.php";

if (session_status() === PHP_SESSION_NONE) session_start();

// Recibir datos POST
$tienda     = isset($_POST['tiendaBackorder']) ? trim($_POST['tiendaBackorder']) : null;
$fecha      = isset($_POST['fechaBackorder']) ? $_POST['fechaBackorder'] : null;
$estilo     = isset($_POST['estilo']) ? trim($_POST['estilo']) : null;
$grupo      = isset($_POST['grupo']) ? trim($_POST['grupo']) : null;
$color      = isset($_POST['color']) ? trim($_POST['color']) : null;
$talla      = isset($_POST['talla']) ? trim($_POST['talla']) : null;
$desc2      = isset($_POST['desc2']) ? trim($_POST['desc2']) : null;
$razon      = isset($_POST['razon']) ? trim($_POST['razon']) : null;
$comentario = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';

// Validar campos obligatorios
if (!$tienda || !$fecha || !$estilo || !$grupo || !$color || !$talla || !$desc2 || !$razon || !$comentario) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios']);
    exit;
}

// Consulta SQL
$query = "
    INSERT INTO roy_backorder (tienda, fecha, estilo, grupo, color, talla, desc2, razon,comentario)
    VALUES (:tienda, TO_DATE(:fecha, 'YYYY-MM-DD'), :estilo, :grupo, :color, :talla, :desc2, :razon, :comentario)
";

// Parámetros
$params = [
    ':tienda' => $tienda,
    ':fecha'  => $fecha,
    ':estilo' => $estilo,
    ':grupo'  => $grupo,
    ':color'  => $color,
    ':talla'  => $talla,
    ':desc2'  => $desc2,
    ':razon'  => $razon,
    ':comentario'  => $comentario
];

// Ejecutar
$result = consultaOracle(4, $query, $params);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Backorder registrado correctamente']);
} else {
    $error = oci_error();
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al registrar el backorder',
        'oracle_error' => $error['message']
    ]);
}
