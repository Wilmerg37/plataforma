<?php
require_once "../consulta.php";

$tienda = $_SESSION['user'][6];
$sbs = $_SESSION['user'][7];
$filtro = "";

// Aplicar filtro por tienda si existe
if (!is_null($tienda)) {
    $filtro = "WHERE tienda = '$tienda'";
}

$query = "
    SELECT 
        TO_CHAR(fecha, 'DD/MM/YYYY') AS \"fecha\",
        tienda AS \"tienda\",
        estilo AS \"estilo\",
        grupo AS \"grupo\",
        color AS \"color\",
        talla AS \"talla\",
        desc2 AS \"desc2\",
        razon AS \"razon\",
        comentario AS \"comentario\"
    FROM roy_backorder
    $filtro
    ORDER BY fecha DESC
    FETCH FIRST 20 ROWS ONLY
";

$resultado = consultaOracle(5,$query);

echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
