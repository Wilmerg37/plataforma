<?php
session_start();
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

date_default_timezone_set('America/Guatemala');

$codigoUsuario = $_SESSION['user'][6];

// Función para obtener tiendas del supervisor
function getTiendasSupervisor($codigoSupervisor) {
    $query = "SELECT STORE_NO FROM RPS.STORE WHERE udf1_string = '$codigoSupervisor' ORDER BY STORE_NO";
    $resultado = consultaOracle(5, $query);
    
    $tiendas = array();
    if ($resultado) {
        foreach ($resultado as $row) {
            $tiendas[] = $row['STORE_NO'];
        }
    }
    return $tiendas;
}

// Función para verificar si es tienda individual
function obtenerCodigoTiendaUsuario($codigoUsuario) {
    if (is_numeric($codigoUsuario)) {
        $query = "SELECT STORE_NO FROM RPS.STORE WHERE STORE_NO = '$codigoUsuario' OR STORE_NO = 'ROYT" . str_pad($codigoUsuario, 2, '0', STR_PAD_LEFT) . "' OR STORE_NO = 'ROYH" . str_pad($codigoUsuario, 2, '0', STR_PAD_LEFT) . "'";
        $resultado = consultaOracle(5, $query);
        
        if ($resultado && count($resultado) > 0) {
            return $resultado[0]['STORE_NO'];
        }
    }
    return null;
}

// Determinar filtro de tiendas
$filtroTiendas = "";
$filtroTiendasRPS = "";
$listaTiendasArray = array();

$codigoTiendaUsuario = obtenerCodigoTiendaUsuario($codigoUsuario);

if ($codigoTiendaUsuario) {
    // Es una tienda individual
    $filtroTiendas = "AND INV.STORE_NO = '$codigoTiendaUsuario'";
    $filtroTiendasRPS = "AND t1.STORE_NO = '$codigoTiendaUsuario'";
    $listaTiendasArray = array($codigoTiendaUsuario);
} else {
    // Es supervisor, obtener sus tiendas
    $tiendasSupervisor = getTiendasSupervisor($codigoUsuario);
    if (!empty($tiendasSupervisor)) {
        $listaTiendas = "'" . implode("','", $tiendasSupervisor) . "'";
        $filtroTiendas = "AND INV.STORE_NO IN ($listaTiendas)";
        $filtroTiendasRPS = "AND t1.STORE_NO IN ($listaTiendas)";
        $listaTiendasArray = $tiendasSupervisor;
    }
}

// Fechas automáticas
$anioActual = date('Y');
$semanaActual = date('W');
$yw = $anioActual . str_pad($semanaActual, 2, '0', STR_PAD_LEFT);
$fechaHoy = date('Y-m-d');

// Obtener tráfico (usando la función existente)
function obtenerTrafico() {
    global $codigoUsuario, $listaTiendasArray;
    
    $url = "https://www.smssoftware.net/tms/manTrafExp?fromDate=" . date("m/d/Y") . "&toDate=" . date("m/d/Y") . "&interval=1440&hours=0&reqType=tdd&apiKey=C3XS754LDZYPJJTEDZ7MFN19BNQC3QWB&locationId=ROY000";
    $fileContents = file_get_contents($url);
    $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
    $fileContents = trim(str_replace('"', "'", $fileContents));
    $simpleXml = simplexml_load_string($fileContents);
    $trafficoData = json_decode(json_encode($simpleXml, JSON_UNESCAPED_UNICODE), true);
    
    $totalTrafico = 0;
    
    if (isset($trafficoData['data'])) {
        foreach ($trafficoData['data'] as $item) {
            $storeId = $item['@attributes']['storeId'];
            $numeroTienda = '';
            
            // Convertir código de tienda
            if (strpos($storeId, 'ROYT') === 0) {
                $numeroTienda = (int) substr($storeId, 4);
            } elseif (strpos($storeId, 'ROYH') === 0) {
                $numeroTienda = $storeId;
            }
            
            // Verificar si la tienda está en la lista del usuario
            if (in_array($numeroTienda, $listaTiendasArray) || in_array($storeId, $listaTiendasArray)) {
                $totalTrafico += (int) $item['@attributes']['trafficValue'];
            }
        }
    }
    
    return $totalTrafico;
}

// 1. Obtener ventas del día
$queryVentas = "SELECT 
                  ROUND(NVL(SUM(CASE WHEN INV.INVC_TYPE = 0 THEN ((I_I.PRICE/1.12) * I_I.QTY)
                                    WHEN INV.INVC_TYPE = 2 THEN ((I_I.PRICE/1.12) * I_I.QTY)* -1 END), 0), 2) AS VENTA_DIA,
                  COUNT(DISTINCT INV.INVC_SID) AS TRANSACCIONES_DIA
                FROM INVOICE INV
                  INNER JOIN INVC_ITEM I_I ON INV.INVC_SID = I_I.INVC_SID
                WHERE INV.SBS_NO = 1
                AND INV.STORE_NO NOT IN(000,100,104,109)
                $filtroTiendas
                AND TRUNC(INV.CREATED_DATE) = TRUNC(SYSDATE)";

$resultadoVentas = consultaOracle(5, $queryVentas);
$ventasDia = $resultadoVentas ? $resultadoVentas[0]['VENTA_DIA'] : 0;
$transaccionesDia = $resultadoVentas ? $resultadoVentas[0]['TRANSACCIONES_DIA'] : 0;

// 2. Obtener metas de la semana
$queryMetas = "SELECT 
                 NVL(SUM(V.META), 0) AS META_SEMANA
               FROM ROY_META_SEM_X_VENDEDOR V
                 INNER JOIN ROY_VENDEDORES_FRIED E ON (E.CODIGO_VENDEDOR = V.CODIGO_EMPLEADO)
               WHERE TO_CHAR(trunc(SYSDATE, 'd'), 'IW') + 1 = V.SEMANA 
               AND TO_CHAR(SYSDATE, 'IYYY') = V.ANIO 
               AND V.SBS = 1";

// Si es tienda específica, agregar filtro
if ($codigoTiendaUsuario) {
    $queryMetas .= " AND V.TIENDA = '$codigoTiendaUsuario'";
} else if (!empty($listaTiendasArray)) {
    $listaTiendasMetas = "'" . implode("','", $listaTiendasArray) . "'";
    $queryMetas .= " AND V.TIENDA IN ($listaTiendasMetas)";
}

$resultadoMetas = consultaOracle(5, $queryMetas);
$metaSemana = $resultadoMetas ? $resultadoMetas[0]['META_SEMANA'] : 0;

// 3. Obtener ventas de la semana para comparar con meta
$queryVentasSemana = "SELECT 
                        ROUND(NVL(SUM(CASE WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 
                                          WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 END), 0), 2) AS VENTA_SEMANA
                      FROM rps.document t1 
                      INNER JOIN rps.document_item t2 ON (t1.sid = t2.doc_sid)
                      WHERE t1.status = 4 
                      AND t1.employee1_full_name NOT IN ('SYSADMIN')
                      AND t1.receipt_type <> 2
                      AND t1.sbs_no = 1
                      $filtroTiendasRPS
                      AND EXTRACT(YEAR FROM t1.created_datetime) || TO_CHAR(trunc(t1.created_datetime, 'd'), 'IW') + 1 = '$yw'";

$resultadoVentasSemana = consultaOracle(5, $queryVentasSemana);
$ventasSemana = $resultadoVentasSemana ? $resultadoVentasSemana[0]['VENTA_SEMANA'] : 0;

// 4. Obtener tráfico
$trafico = obtenerTrafico();

// 5. Calcular conversión (ventas del día / tráfico del día * 100)
$conversion = ($trafico > 0) ? round(($transaccionesDia / $trafico) * 100, 2) : 0;

// 6. Calcular porcentaje de cumplimiento de meta
$cumplimientoMeta = ($metaSemana > 0) ? round(($ventasSemana / $metaSemana) * 100, 2) : 0;

// Preparar respuesta
$respuesta = array(
    'ventas_dia' => number_format($ventasDia, 2),
    'ventas_semana' => number_format($ventasSemana, 2),
    'meta_semana' => number_format($metaSemana, 2),
    'cumplimiento_meta' => $cumplimientoMeta,
    'trafico_dia' => $trafico,
    'transacciones_dia' => $transaccionesDia,
    'conversion' => $conversion,
    'usuario' => $codigoUsuario,
    'tiendas_filtradas' => $listaTiendasArray,
    'fecha_consulta' => date('Y-m-d H:i:s'),
    'semana_actual' => $semanaActual
);

header('Content-Type: application/json');
echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);