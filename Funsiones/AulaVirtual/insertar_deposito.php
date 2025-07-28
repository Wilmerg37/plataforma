<?php
require_once "../../Funsiones/global.php";

// Recibir datos POST
$no_transaccion = isset($_POST['noDeposito']) ? trim($_POST['noDeposito']) : null;
$monto = isset($_POST['montoDeposito']) ? floatval(str_replace(',', '', $_POST['montoDeposito'])) : null;
$fecha_corte = isset($_POST['fechaDeposito']) ? $_POST['fechaDeposito'] : null;
$observacion = isset($_POST['comentario']) ? trim($_POST['comentario']) : '';
$banco_id = isset($_POST['bancoDeposito']) ? intval($_POST['bancoDeposito']) : null;
$pago_id = isset($_POST['tipoDeposito']) ? intval($_POST['tipoDeposito']) : null;
$no_tienda = isset($_POST['tiendaDeposito']) ? intval($_POST['tiendaDeposito']) : null;  // Asumiendo que tienes este campo en el formulario

// Validar datos
if (!$no_transaccion || !$monto || !$fecha_corte || !$banco_id || !$pago_id || !$no_tienda) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios']);
    exit;
}

// Consulta con parámetros (placeholders)
$query = "INSERT INTO Tb_Corte (NO_TRANSACCION, MONTO, FECHA_CORTE, OBSERVACION, TB_BANCOS_CORTE_ID_BANCO, TB_FORMAPAGO_ID_PAGO, NO_TIENDA)
          VALUES (?, ?, ?, ?, ?, ?, ?)";

// Parámetros para sqlsrv (deben ser referencias)
$params = [
    &$no_transaccion,
    &$monto,
    &$fecha_corte,
    &$observacion,
    &$banco_id,
    &$pago_id,
    &$no_tienda
];

// Ejecutar
$result = consultaSQLServer(2, $query, $params);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Depósito registrado correctamente']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al insertar depósito']);
}
