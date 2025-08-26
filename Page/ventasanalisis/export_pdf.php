<?php
// export_pdf.php - Archivo separado para manejar la exportación a PDF
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/tienda/queryRpro.php";
require_once "../../vendor/autoload.php"; // Para DomPDF

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();

// Recibir parámetros
$tienda = isset($_GET['tienda']) ? $_GET['tienda'] : '';
$fi = isset($_GET['fi']) ? $_GET['fi'] : '';
$ff = isset($_GET['ff']) ? $_GET['ff'] : '';
$sbs = isset($_GET['sbs']) ? $_GET['sbs'] : '';
$pais = isset($_SESSION['user'][7]) ? $_SESSION['user'][7] : 'N/A';

$sim = impuestoSimbolo($sbs);
$sim = is_array($sim) ? (isset($sim[0]) ? $sim[0] : '') : $sim;

$tiendas = explode(',', $tienda);
sort($tiendas);

// Reutilizar las funciones del archivo principal
function calcularRotacionInventario($venta, $stock_promedio, $periodo_dias = 365) {
    if ($stock_promedio == 0) return 0;
    $factor_anual = 365 / $periodo_dias;
    $rotacion = ($venta / $stock_promedio) * $factor_anual;
    return round($rotacion, 2);
}

function obtenerStockHistorico($tienda_num, $sbs, $fi, $ff, $estilo, $color) {
    $query = "
    SELECT AVG(stock_qty) as stock_promedio
    FROM (
        SELECT TRUNC(created_date) as fecha, SUM(qty) as stock_qty
        FROM (
            select S.SBS_NO, ST.STORE_NO, 
                   SUBSTR(i.description2,1,6) ESTILO,   
                   SUBSTR(i.description2,11,2) COLOR,
                   sum(iq.qty) Qty,
                   i.created_datetime as created_date
            from rps.invn_sbs_item i 
            inner join rps.invn_sbs_item_qty iq on i.sbs_sid=iq.sbs_sid and i.sid=iq.invn_sbs_item_sid
            inner join rps.store st on iq.store_sid=st.sid
            inner join rps.subsidiary s on i.sbs_sid=s.sid
            Where S.sbs_no = {$sbs}
            and ST.store_no = {$tienda_num}
            and SUBSTR(i.description2,1,6) = '{$estilo}'
            and SUBSTR(i.description2,11,2) = '{$color}'
            AND i.created_Datetime between to_date('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                       AND to_date('{$ff} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            group by S.SBS_NO, ST.STORE_NO, SUBSTR(i.description2,1,6), SUBSTR(i.description2,11,2), 
                     i.created_datetime
        )
        GROUP BY TRUNC(created_date)
    )";
    
    $resultado = consultaOracle(5, $query);
    return !empty($resultado) && isset($resultado[0]['STOCK_PROMEDIO']) ? 
           (float)$resultado[0]['STOCK_PROMEDIO'] : 0;
}

function obtenerDatosTienda($tienda_num, $sbs, $fi, $ff) {
    $query = "
    SELECT * FROM (
        SELECT a.sbs_no SUBSIDIARIA, A.STORE_NO TIENDA, A.C_NAME GENERO, S_NAME CATEGORIA, 
               ESTILO, COLOR, PRECIO, 'VENTA' TIPO, sum(cantidad) PARES 
        FROM (
            select t1.sbs_no, t1.store_NO, trunc(t1.created_datetime) FECHA, 
                   e.empl_name COD_VENDEDOR, e.full_name VENDEDOR,
                   substr(B.description2,1,6) ESTILO, substr(B.description2,8,2) GRUPO, 
                   substr(B.description2,11,2) COLOR,
                   DCS.D_NAME, DCS.C_NAME, DCS.S_NAME,
                   sum(case when t1.receipt_type=0 then (T2.qty) 
                           when t1.receipt_type=1 then (T2.qty)*-1 end) as cantidad,        					   
                   sum(case when t1.receipt_type=0 then (t2.qty*t2.cost) 
                           when t1.receipt_type=1 then (t2.qty*t2.cost)*-1 else 0 end) as costo, 			   			   
                   NVL(sum(case when t1.receipt_type=0 then ((t2.price-(t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))
                               when t1.receipt_type=1 then ((t2.price-(t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))*-1 end),0) as venta_con_iva, 
                   NVL(sum(case when t1.receipt_type=0 then ((t2.price-(t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12 
                               when t1.receipt_type=1 then ((t2.price-(t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12*-1 end),0) as venta_sin_iva,
                   t2.price as PRECIO     
            from rps.document t1 
            inner join rps.document_item t2 on (t1.sid = t2.doc_sid)					   
            left join rps.employee e on (e.sid=t2.employee1_sid)                        
            INNER JOIN RPS.INVN_SBS_ITEM B ON t2.INVN_sBS_ITEM_SID = B.SID
            INNER JOIN RPS.DCS DCS ON B.sbs_sid = DCS.sbs_sid AND B.dcs_sid = DCS.sid
            where 1=1
            and t1.status=4 
            and t1.receipt_type<>2
            and T1.sbs_no = {$sbs}
            and T1.store_no = {$tienda_num}					
            and t1.CREATED_DATETIME between to_date('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                        AND to_date('{$ff} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            group by t1.sbs_no, t1.store_NO, e.full_name, e.empl_name, trunc(t1.created_datetime), 
                     T1.DOC_NO, t1.receipt_type, t1.disc_amt, substr(B.description2,1,6), 
                     substr(B.description2,8,2), substr(B.description2,11,2), 
                     DCS.D_NAME, DCS.C_NAME, DCS.S_NAME, t2.price
        ) A 
        GROUP BY A.SBS_NO, A.STORE_NO, ESTILO, COLOR, C_NAME, S_NAME, PRECIO
                            
    UNION ALL       
                
        SELECT SBS_NO SUBSIDIARIA, STORE_NO TIENDA, C_NAME GENERO, S_NAME CATEGORIA, ESTILO, COLOR, PRICE PRECIO, 
               'STOCK' TIPO, SUM(QTY) PARES 
        FROM (
            select S.SBS_NO, ST.STORE_NO, 
                   SUBSTR(i.description2,1,6) ESTILO,   
                   SUBSTR(i.description2,8,2) GRUPO,  
                   SUBSTR(i.description2,11,2) COLOR,  
                   SUBSTR(i.description2,14,3) TALLA,
                   D.D_NAME, D.C_NAME, D.S_NAME,
                   sum(iq.qty) Qty, IP.PRICE
            from rps.invn_sbs_item i 
            inner join rps.invn_sbs_item_qty iq on i.sbs_sid=iq.sbs_sid and i.sid=iq.invn_sbs_item_sid
            inner join rps.store st on iq.store_sid=st.sid
            INNER JOIN RPS.VENDOR VD ON I.vend_SID = VD.SID
            inner join rps.dcs d on i.sbs_sid=d.sbs_sid and i.dcs_sid=d.sid
            inner join rps.subsidiary s on i.sbs_sid=s.sid
            left join (
                select s.sid sbs_sid, invn_sbs_item_sid, ip.price 
                from rps.subsidiary s, rps.invn_sbs_price ip 
                where s.sid=ip.sbs_sid and s.active_price_lvl_sid=ip.price_lvl_sid
            ) ip on iq.sbs_sid=ip.sbs_sid and iq.invn_sbs_item_sid=ip.invn_sbs_item_sid
            Where S.sbs_no = {$sbs}
            and ST.store_no = {$tienda_num}
            AND i.created_Datetime <= (TO_DATE('{$ff}', 'YYYY-MM-DD'))
            and (iq.qty) <> 0
            group by ST.STORE_NO, S.SBS_NO, D.D_NAME, D.C_NAME, D.S_NAME,
                     SUBSTR(i.description2,1,6), SUBSTR(i.description2,8,2),  
                     SUBSTR(i.description2,11,2), SUBSTR(i.description2,14,3), 
                     ip.price, I.COST
        )
        GROUP BY SBS_NO, STORE_NO, C_NAME, ESTILO, COLOR, C_NAME, S_NAME, PRICE                                                                         
    ) SM
    ORDER BY SM.SUBSIDIARIA, SM.TIENDA, SM.ESTILO, SM.COLOR";

    $resultado = consultaOracle(5, $query);
    
    // Reorganizar resultados agrupando por estilo y color
    $agrupado = [];
    if (!empty($resultado)) {
        foreach ($resultado as $fila) {
            $estilo = is_array($fila['ESTILO']) ? $fila['ESTILO'][0] : $fila['ESTILO'];
            $color = is_array($fila['COLOR']) ? $fila['COLOR'][0] : $fila['COLOR'];
            $tipo = is_array($fila['TIPO']) ? $fila['TIPO'][0] : $fila['TIPO'];
            $pares = is_numeric($fila['PARES']) ? (int)$fila['PARES'] : 0;
            $precio = isset($fila['PRECIO']) && is_numeric($fila['PRECIO']) ? (float)$fila['PRECIO'] : 0;
            $genero = isset($fila['GENERO']) ? (is_array($fila['GENERO']) ? $fila['GENERO'][0] : $fila['GENERO']) : '';
            $categoria = isset($fila['CATEGORIA']) ? (is_array($fila['CATEGORIA']) ? $fila['CATEGORIA'][0] : $fila['CATEGORIA']) : '';

            $clave = $estilo . '-' . $color;

            if (!isset($agrupado[$clave])) {
                $agrupado[$clave] = [
                    'estilo' => $estilo,
                    'color' => $color,
                    'genero' => $genero,
                    'categoria' => $categoria,
                    'precio' => 0,
                    'venta' => 0,
                    'stock' => 0,
                    'rotacion' => 0
                ];
            }

            if ($tipo === 'VENTA') {
                $agrupado[$clave]['venta'] += $pares;
                if ($precio > 0 && $agrupado[$clave]['precio'] == 0) {
                    $agrupado[$clave]['precio'] = $precio;
                }
            } elseif ($tipo === 'STOCK') {
                $agrupado[$clave]['stock'] += $pares;
                if ($precio > 0) {
                    $agrupado[$clave]['precio'] = $precio;
                }
            }
        }
    }

    // Calcular rotación para cada producto
    $dias_periodo = (strtotime($ff) - strtotime($fi)) / (60 * 60 * 24) + 1;
    foreach ($agrupado as $clave => &$producto) {
        $stock_promedio = obtenerStockHistorico($tienda_num, $sbs, $fi, $ff, $producto['estilo'], $producto['color']);
        if ($stock_promedio == 0) $stock_promedio = $producto['stock']; // Fallback al stock actual
        $producto['rotacion'] = calcularRotacionInventario($producto['venta'], $stock_promedio, $dias_periodo);
    }

    return $agrupado;
}

// Obtener datos de todas las tiendas
$datos_todas_tiendas = [];
foreach ($tiendas as $tienda_num) {
    $datos_todas_tiendas[$tienda_num] = obtenerDatosTienda($tienda_num, $sbs, $fi, $ff);
}

// Combinar todos los productos únicos
$productos_unicos = [];
foreach ($datos_todas_tiendas as $tienda_num => $datos) {
    foreach ($datos as $clave => $producto) {
        if (!isset($productos_unicos[$clave])) {
            $productos_unicos[$clave] = [
                'estilo' => is_array($producto['estilo']) ? $producto['estilo'][0] : $producto['estilo'],
                'color' => is_array($producto['color']) ? $producto['color'][0] : $producto['color'],
                'genero' => is_array($producto['genero']) ? $producto['genero'][0] : $producto['genero'],
                'categoria' => is_array($producto['categoria']) ? $producto['categoria'][0] : $producto['categoria'],
                'precio' => (is_array($producto['precio']) 
                    ? (isset($producto['precio'][0]) && is_numeric($producto['precio'][0]) 
                        ? (float)$producto['precio'][0] 
                        : 0)
                    : (is_numeric($producto['precio']) 
                        ? (float)$producto['precio'] 
                        : 0))
            ];
        } else {
            $precio_actual = is_numeric($producto['precio']) ? (float)$producto['precio'] : 0;
            $precio_existente = is_numeric($productos_unicos[$clave]['precio']) ? (float)$productos_unicos[$clave]['precio'] : 0;
            
            if ($precio_actual > $precio_existente) {
                $productos_unicos[$clave]['precio'] = $precio_actual;
            }
        }
    }
}

// Calcular totales para resumen
$total_ventas = 0;
$total_stock = 0;
$productos_bajo_stock = 0;
$rotacion_promedio = 0;
$contador_rotacion = 0;

foreach ($datos_todas_tiendas as $datos) {
    foreach ($datos as $producto) {
        $total_ventas += (int)$producto['venta'];
        $total_stock += (int)$producto['stock'];
        if ($producto['stock'] < $producto['venta'] && $producto['venta'] > 0) {
            $productos_bajo_stock++;
        }
        if ($producto['rotacion'] > 0) {
            $rotacion_promedio += $producto['rotacion'];
            $contador_rotacion++;
        }
    }
}

$rotacion_promedio = $contador_rotacion > 0 ? $rotacion_promedio / $contador_rotacion : 0;

// Configurar DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Generar HTML optimizado para PDF
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 15mm; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 9px; 
            margin: 0; 
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 18px; 
            color: #2563eb;
            font-weight: bold;
        }
        .header p { 
            margin: 5px 0 0 0; 
            font-size: 12px; 
            color: #666;
        }
        .info-section { 
            margin-bottom: 15px; 
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        .info-grid { 
            display: table; 
            width: 100%; 
        }
        .info-item { 
            display: table-cell; 
            width: 25%; 
            padding: 3px 5px; 
            font-size: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .summary-grid { 
            display: table; 
            width: 100%; 
            margin: 10px 0; 
        }
        .summary-item { 
            display: table-cell; 
            width: 25%; 
            text-align: center; 
            padding: 8px; 
            border: 1px solid #ddd; 
            background: #f8f9fa;
        }
        .summary-value { 
            font-size: 14px; 
            font-weight: bold; 
            color: #2563eb; 
            margin-bottom: 3px;
        }
        .summary-label {
            font-size: 8px;
            color: #666;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 3px 2px; 
            text-align: left; 
            font-size: 7px; 
        }
        th { 
            background-color: #2563eb; 
            color: white;
            font-weight: bold; 
            text-align: center;
        }
        .store-column { 
            background-color: #e3f2fd; 
            text-align: center; 
            font-weight: bold;
        }
        .venta-column { 
            background-color: #e8f5e8; 
            text-align: right; 
        }
        .stock-column { 
            background-color: #fff3e0; 
            text-align: right; 
        }
        .rotacion-column { 
            background-color: #f3e5f5; 
            text-align: right; 
            font-weight: bold;
        }
        .low-stock { 
            background-color: #ffebee !important; 
            color: #c62828 !important; 
            font-weight: bold;
        }
        .high-stock { 
            background-color: #e8f5e8 !important; 
            color: #2e7d32 !important; 
        }
        .rotacion-high { color: #2e7d32; font-weight: bold; }
        .rotacion-medium { color: #f57c00; font-weight: bold; }
        .rotacion-low { color: #c62828; font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
        .legend {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 8px;
        }
        .legend-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #2563eb;
        }
        .legend-grid {
            display: table;
            width: 100%;
        }
        .legend-item {
            display: table-cell;
            width: 33.33%;
            padding: 2px;
        }
        .color-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 5px;
            border-radius: 2px;
        }
        .footer {
            position: fixed;
            bottom: 10mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔄 Reporte de Inventario con Rotación</h1>
        <p>Análisis completo de ventas vs stock con índices de rotación</p>
    </div>
    
    <div class="info-section">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">🏪 Tiendas:</div>
                <div>' . implode(', ', array_map(function($t) { return "T$t"; }, $tiendas)) . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">📅 Período:</div>
                <div>' . $fi . ' - ' . $ff . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">🌍 País:</div>
                <div>' . $pais . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">⏰ Generado:</div>
                <div>' . date('d/m/Y H:i') . '</div>
            </div>
        </div>
    </div>
    
    <div class="summary-grid">
        <div class="summary-item">
            <div class="summary-value">' . number_format($total_ventas) . '</div>
            <div class="summary-label">Total Ventas (pares)</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">' . number_format($total_stock) . '</div>
            <div class="summary-label">Stock Total (pares)</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">' . $productos_bajo_stock . '</div>
            <div class="summary-label">Alertas Bajo Stock</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">' . number_format($rotacion_promedio, 1) . '</div>
            <div class="summary-label">Rotación Promedio</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th rowspan="2">Género</th>
                <th rowspan="2">Categoría</th>
                <th rowspan="2">Estilo</th>
                <th rowspan="2">Color</th>
                <th rowspan="2">Precio</th>';

foreach ($tiendas as $tienda_num) {
    $html .= '<th colspan="3" class="store-column">TIENDA ' . $tienda_num . '</th>';
}

$html .= '</tr><tr>';

foreach ($tiendas as $tienda_num) {
    $html .= '<th class="venta-column">VENTA</th>
              <th class="stock-column">STOCK</th>
              <th class="rotacion-column">ROT.</th>';
}

$html .= '</tr></thead><tbody>';

foreach ($productos_unicos as $clave => $producto) {
    $html .= '<tr>
        <td>' . htmlspecialchars($producto['genero'] ?? '') . '</td>
        <td>' . htmlspecialchars($producto['categoria'] ?? '') . '</td>
        <td><strong>' . htmlspecialchars($producto['estilo'] ?? '') . '</strong></td>
        <td>' . htmlspecialchars($producto['color'] ?? '') . '</td>
        <td class="right">' . $sim . number_format((float)$producto['precio'], 2) . '</td>';
    
    foreach ($tiendas as $tienda_num) {
        $venta = 0;
        $stock = 0;
        $rotacion = 0;
        
        if (isset($datos_todas_tiendas[$tienda_num][$clave])) {
            $dato_tienda = $datos_todas_tiendas[$tienda_num][$clave];
            $venta = is_numeric($dato_tienda['venta']) ? (int)$dato_tienda['venta'] : 0;
            $stock = is_numeric($dato_tienda['stock']) ? (int)$dato_tienda['stock'] : 0;
            $rotacion = is_numeric($dato_tienda['rotacion']) ? (float)$dato_tienda['rotacion'] : 0;
        }
        
        $clase_stock = '';
        if ($stock < $venta && $venta > 0) {
            $clase_stock = 'low-stock';
        } elseif ($stock > ($venta * 2) && $venta > 0) {
            $clase_stock = 'high-stock';
        }
        
        $clase_rotacion = '';
        if ($rotacion >= 4) {
            $clase_rotacion = 'rotacion-high';
        } elseif ($rotacion >= 2) {
            $clase_rotacion = 'rotacion-medium';
        } elseif ($rotacion > 0) {
            $clase_rotacion = 'rotacion-low';
        }
        
        $html .= '<td class="venta-column">' . number_format($venta) . '</td>
                  <td class="stock-column ' . $clase_stock . '">' . number_format($stock) . '</td>
                  <td class="rotacion-column ' . $clase_rotacion . '">' . number_format($rotacion, 1) . '</td>';
    }
    
    $html .= '</tr>';
}

$html .= '</tbody></table>

<div class="legend">
    <div class="legend-title">📊 Interpretación de Indicadores</div>
    <div class="legend-grid">
        <div class="legend-item">
            <span class="color-indicator" style="background: #2e7d32;"></span>
            <strong>Rotación ≥ 4:</strong> Alta rotación (excelente)
        </div>
        <div class="legend-item">
            <span class="color-indicator" style="background: #f57c00;"></span>
            <strong>Rotación 2-4:</strong> Rotación moderada (buena)
        </div>
        <div class="legend-item">
            <span class="color-indicator" style="background: #c62828;"></span>
            <strong>Rotación < 2:</strong> Baja rotación (revisar)
        </div>
    </div>
    <br>
    <div class="legend-grid">
        <div class="legend-item">
            <span class="color-indicator" style="background: #ffebee; border: 1px solid #c62828;"></span>
            <strong>Bajo Stock:</strong> Stock menor que ventas
        </div>
        <div class="legend-item">
            <span class="color-indicator" style="background: #e8f5e8; border: 1px solid #2e7d32;"></span>
            <strong>Alto Stock:</strong> Stock mayor al doble de ventas
        </div>
        <div class="legend-item">
            <span class="color-indicator" style="background: #f8f9fa; border: 1px solid #ddd;"></span>
            <strong>Stock Normal:</strong> Balance adecuado
        </div>
    </div>
    <br>
    <div style="text-align: center; font-size: 7px; color: #666;">
        <strong>Nota:</strong> La rotación de inventario indica cuántas veces se vende el stock promedio en un año. 
        Valores altos indican productos de alta demanda, valores bajos pueden requerir estrategias de promoción.
    </div>
</div>

<div class="footer">
    Reporte generado automáticamente el ' . date('d/m/Y H:i:s') . ' | Sistema de Gestión de Inventario
</div>

</body>
</html>';

// Configurar y generar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Enviar PDF al navegador
$filename = 'reporte_inventario_rotacion_' . date('Y-m-d_H-i-s') . '.pdf';
$dompdf->stream($filename, array('Attachment' => true));
?>