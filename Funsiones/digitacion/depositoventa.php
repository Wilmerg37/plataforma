<?php
require_once "../consulta.php";
require_once "../global.php";

header('Content-Type: application/json; charset=utf-8');

$fi = isset($_POST['fechas']) ? date('Y-m-d', strtotime(substr($_POST['fechas'], 0, -13))) : '';
$ff = isset($_POST['fechas']) ? date('Y-m-d', strtotime(substr($_POST['fechas'], -10))) : '';

$queryOracle = "SELECT
    TO_CHAR(T1.CREATED_DATETIME,'YYYY-MM-DD') FECHA,
    T1.STORE_NO TIENDA,
    1 SBS,
    NVL(sum(case when t1.receipt_type=0 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))
                  when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))*-1 end ),0)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0)) VENTA
FROM RPS.DOCUMENT T1
INNER JOIN RPS.DOCUMENT_ITEM T2 on (T1.sid = T2.doc_sid)
WHERE T1.CREATED_DATETIME BETWEEN TO_DATE(:fi || ' 00:00:00','YYYY-MM-DD HH24:MI:SS') AND TO_DATE(:ff || ' 23:59:59','YYYY-MM-DD HH24:MI:SS')
GROUP BY TO_CHAR(T1.CREATED_DATETIME,'YYYY-MM-DD'), T1.STORE_NO
ORDER BY T1.STORE_NO";

$paramsOracle = [':fi' => $fi, ':ff' => $ff];
$ventas = consultaOracle(3, $queryOracle, $paramsOracle);

// Conectar y consultar SQL Server para depósitos
$querySQL = "SELECT 
    C.Fecha_Corte FECHA,
    C.No_Tienda TIENDA,
    1 SBS,
    C.Monto DEPOSITO
FROM Tb_Corte C
JOIN Tb_Bancos_corte B ON B.Id_Banco = C.Tb_Bancos_corte_Id_Banco
JOIN Tb_Formapago P ON P.Id_Pago = C.Tb_Formapago_Id_Pago    
WHERE C.FECHA_CORTE BETWEEN ? AND ?  
AND P.Descripcion = 'EFECTIVO'
ORDER BY C.No_Tienda, C.Fecha_CreacioN";

$paramsSQL = [$fi, $ff];
$depositos = consultaSQLServer(3, $querySQL, $paramsSQL);

// Ahora hacemos el LEFT JOIN en PHP para unir ventas y depósitos por FECHA y TIENDA
// Convertimos depósitos en un array asociativo para fácil acceso

$depMap = [];
foreach ($depositos as $d) {
    // $d: [FECHA, TIENDA, SBS, DEPOSITO]
    $key = $d[0] . '|' . $d[1];
    $depMap[$key] = $d[3];
}

$finalResult = [];
foreach ($ventas as $v) {
    // $v: [FECHA, TIENDA, SBS, VENTA]
    $key = $v[0] . '|' . $v[1];
    $deposito = isset($depMap[$key]) ? $depMap[$key] : 0;
    $venta = $v[3];
    $diferencia = $deposito - $venta;

    $finalResult[] = [
        'SUBSIDIARIA' => str_pad('1', 3, '0', STR_PAD_LEFT), // fijo '001' porque SBS=1
        'FECHA' => $v[0],
        'TIENDA' => 'T-' . str_pad($v[1], 3, '0', STR_PAD_LEFT),
        'VENTA' => (float)$venta,
        'DEPOSITO' => (float)$deposito,
        'DIFERENCIA' => (float)$diferencia
    ];
}

// Enviar JSON con clave 'data' para DataTables
echo json_encode(['data' => $finalResult], JSON_UNESCAPED_UNICODE);
exit;
