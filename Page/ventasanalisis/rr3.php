<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/tienda/queryRpro.php";

$tienda = isset($_POST['tienda']) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];

$sim = impuestoSimbolo($sbs);
$iva = isset($_POST['iva']) ? $_POST['iva'] : '';
$vacacionista = isset($_POST['vacacionista']) ? $_POST['vacacionista'] : '';
$filtro = ($vacacionista == '1') ? '' : " AND EMP.EMPL_NAME < '5000'";

$semanas = rangoWe($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);

// Filtro por estilo - MANTENER EN LA MISMA PÁGINA
$filtro_estilo = isset($_POST['filtro_estilo']) ? trim($_POST['filtro_estilo']) : '';

// Manejar limpiar filtro
if (isset($_POST['limpiar_filtro']) && $_POST['limpiar_filtro'] == '1') {
    $filtro_estilo = '';
}

// Manejar exportación a Excel
if (isset($_POST['exportar_excel']) && $_POST['exportar_excel'] == '1') {
    $exportar = true;
} else {
    $exportar = false;
}
?>

<link rel="stylesheet" href="../css/estilorr4.css">


<div class="reporte-container">
    <div class="header-section">
        <h1>REPORTE DE VENTAS POR TIENDA</h1>
        <div class="subtitle">Análisis Consolidado por Estilo y Proveedor</div>
        <div class="subtitle">Periodo: <?php echo date('d/m/Y', strtotime($fi)) . " - " . date('d/m/Y', strtotime($ff)); ?></div>
    </div>

    <div class="filtros-section">
        <div class="filtro-container">
            <form method="POST" action="" style="display: contents;">
                <?php
                // Mantener TODOS los valores POST existentes
                foreach ($_POST as $key => $value) {
                    if ($key !== 'filtro_estilo' && $key !== 'exportar_excel') {
                        if (is_array($value)) {
                            foreach ($value as $v) {
                                echo "<input type='hidden' name='{$key}[]' value='" . htmlspecialchars($v) . "'>";
                            }
                        } else {
                            echo "<input type='hidden' name='{$key}' value='" . htmlspecialchars($value) . "'>";
                        }
                    }
                }
                ?>
                
                <div class="filtro-grupo">
                    <label for="filtro_estilo">🔍 Filtrar por Estilo:</label>
                    <input type="text" 
                           id="filtro_estilo" 
                           name="filtro_estilo" 
                           class="filtro-input" 
                           placeholder="Ingrese código de estilo (Ej: 007081-05-09)"
                           value="<?php echo htmlspecialchars($filtro_estilo); ?>">
                </div>
                
                <div class="filtro-grupo">
                    <button type="submit" class="btn-filtrar">🔍 Aplicar Filtro</button>
                    <button type="submit" name="exportar_excel" value="1" class="btn-excel">📊 Exportar a Excel</button>
                    <?php if (!empty($filtro_estilo)): ?>
                        <button type="submit" name="limpiar_filtro" value="1" class="btn-limpiar">🗑️ Limpiar Filtro</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="content-section">
        <?php
        // Obtener datos de todas las tiendas
        $datos_consolidados = [];
        $todas_tiendas = [];

        foreach ($tiendas as $tienda_actual) {
            $todas_tiendas[] = $tienda_actual;
            
            $query = "
WITH ventas AS (
    SELECT 
        d.store_code, 
        v.vend_name, 
        SUBSTR(i.description2, 1, 12) AS estilo,
        CAST(MAX(u.udf12_string) AS VARCHAR2(100)) AS estado,
        SUM(CASE 
            WHEN d.receipt_type = 0 THEN di.qty
            WHEN d.receipt_type = 1 THEN -di.qty
            ELSE 0
        END) AS qty_sold
    FROM rps.document d
    INNER JOIN rps.document_item di ON d.sid = di.doc_sid
    INNER JOIN rps.invn_sbs_item i ON i.sid = di.invn_sbs_item_sid AND i.sbs_sid = d.subsidiary_sid
    LEFT JOIN rps.invn_sbs_extend u ON i.sid = u.invn_sbs_item_sid
    LEFT JOIN rps.vendor v ON i.vend_sid = v.sid AND i.sbs_sid = v.sbs_sid
    WHERE d.status = 4
        AND d.receipt_type <> 2
        AND v.vend_code <> 106
        AND d.sbs_no = {$sbs}
        AND d.store_no = {$tienda_actual}
        AND d.invc_post_date BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                 AND TO_DATE('{$ff} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
    GROUP BY d.store_code, v.vend_name, SUBSTR(i.description2, 1, 12)
),
stock AS (
    SELECT 
        s.store_code,
        v.vend_name,
        SUBSTR(i.description2, 1, 12) AS estilo,
        SUM(q.qty) AS exist
    FROM rps.invn_sbs_item i
    JOIN rps.invn_sbs_item_qty q ON i.sid = q.invn_sbs_item_sid AND i.sbs_sid = q.sbs_sid
    JOIN rps.store s ON q.store_sid = s.sid
    JOIN rps.subsidiary sub ON q.sbs_sid = sub.sid
    LEFT JOIN rps.vendor v ON i.vend_sid = v.sid AND i.sbs_sid = v.sbs_sid
    WHERE sub.sbs_no = {$sbs}
      AND s.store_no = {$tienda_actual}
    GROUP BY s.store_code, v.vend_name, SUBSTR(i.description2, 1, 12)
),
minimos AS (
    SELECT 
        s.store_code,
        v.vend_name,
        SUBSTR(i.description2, 1, 12) AS estilo,
        SUM(q.min_qty) AS mini
    FROM rps.invn_sbs_item i
    JOIN rps.invn_minmax m ON m.invn_item_sid = i.sid AND m.sbs_sid = i.sbs_sid
    JOIN rps.invn_minmax_qty q ON m.sid = q.invn_minmax_sid
    JOIN rps.store s ON q.store_sid = s.sid
    JOIN rps.subsidiary sub ON m.sbs_sid = sub.sid
    LEFT JOIN rps.vendor v ON i.vend_sid = v.sid AND i.sbs_sid = v.sbs_sid
    WHERE sub.sbs_no = {$sbs}
      AND s.store_no = {$tienda_actual}
    GROUP BY s.store_code, v.vend_name, SUBSTR(i.description2, 1, 12)
)
SELECT 
    COALESCE(v.estilo, s.estilo, m.estilo) AS estilo,
    COALESCE(v.vend_name, s.vend_name, m.vend_name) AS vend_name,
    COALESCE(v.estado, '-') AS estado,
    NVL(v.qty_sold, 0) AS qty_sold,
    NVL(s.exist, 0) AS exist,
    NVL(m.mini, 0) AS mini,
    {$tienda_actual} AS tienda
FROM ventas v
FULL OUTER JOIN stock s 
    ON v.store_code = s.store_code 
    AND v.estilo = s.estilo
FULL OUTER JOIN minimos m 
    ON COALESCE(v.store_code, s.store_code) = m.store_code 
    AND COALESCE(v.estilo, s.estilo) = m.estilo
WHERE COALESCE(v.estilo, s.estilo, m.estilo) IS NOT NULL";

            // Agregar filtro por estilo si está definido
            if (!empty($filtro_estilo)) {
                $query .= " AND UPPER(COALESCE(v.estilo, s.estilo, m.estilo)) LIKE UPPER('%{$filtro_estilo}%')";
            }

            $query .= " ORDER BY estilo";

            $resultado = consultaOracle(5, $query);

            foreach ($resultado as $fila) {
                $key = $fila['ESTILO'] . '|' . $fila['VEND_NAME'];
                
                if (!isset($datos_consolidados[$key])) {
                    $datos_consolidados[$key] = [
                        'estilo' => $fila['ESTILO'],
                        'proveedor' => $fila['VEND_NAME'],
                        'estado' => $fila['ESTADO'],
                        'tiendas' => []
                    ];
                }
                
                $datos_consolidados[$key]['tiendas'][$tienda_actual] = [
                    'venta' => $fila['QTY_SOLD'],
                    'existencia' => $fila['EXIST'],
                    'minimo' => $fila['MINI']
                ];
            }
        }

        // Manejar exportación a Excel
        if ($exportar && !empty($datos_consolidados)) {
            // Generar contenido para Excel
            $excel_content = generarExcel($datos_consolidados, $todas_tiendas, $fi, $ff, $filtro_estilo);
            
            // Headers para descarga
            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="reporte_ventas_' . date('Y-m-d_H-i-s') . '.xls"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            header('Expires: 0');
            
            echo $excel_content;
            exit();
        }

        if (!empty($datos_consolidados)) {
            echo "<div class='resumen-info'>";
            echo "<h3>📊 Resumen del Reporte</h3>";
            echo "<p><strong>Estilos encontrados:</strong> " . count($datos_consolidados) . "</p>";
            echo "<p><strong>Tiendas analizadas:</strong> " . implode(', ', $todas_tiendas) . "</p>";
            if (!empty($filtro_estilo)) {
                echo "<p><strong>Filtro aplicado:</strong> \"" . htmlspecialchars($filtro_estilo) . "\"</p>";
            }
            echo "</div>";

            echo "<table class='tabla-principal'>";
            
            // Header
            echo "<tr class='tabla-header'>";
            echo "<th>Proveedor</th>";
            echo "<th>Estilo-Grupo-Color</th>";
            echo "<th>Tipo</th>";
            foreach ($todas_tiendas as $t) {
                echo "<th>Tienda $t<br><small>Total</small></th>";
            }
            echo "</tr>";

            foreach ($datos_consolidados as $data) {
                // Fila de Ventas
                echo "<tr class='fila-venta'>";
                echo "<td rowspan='4' class='celda-proveedor'>" . htmlspecialchars($data['proveedor']) . "</td>";
                echo "<td rowspan='4' class='celda-estilo'>" . htmlspecialchars($data['estilo']) . "</td>";
                echo "<td class='celda-tipo'>Venta</td>";
                
                foreach ($todas_tiendas as $t) {
                    $valor = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['venta'] : 0;
                    $clase = ($valor > 0) ? 'valor-venta' : '';
                    echo "<td class='celda-tienda $clase'>" . number_format($valor) . "</td>";
                }
                echo "</tr>";

                // Fila de Existencias
                echo "<tr class='fila-existencia'>";
                echo "<td class='celda-tipo'>Existencia</td>";
                
                foreach ($todas_tiendas as $t) {
                    $valor = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['existencia'] : 0;
                    $minimo = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['minimo'] : 0;
                    $clase = ($valor < $minimo && $minimo > 0) ? 'low-stock' : 'valor-existencia';
                    echo "<td class='celda-tienda $clase'>" . number_format($valor) . "</td>";
                }
                echo "</tr>";

                // Fila de Mínimos
                echo "<tr class='fila-minimo'>";
                echo "<td class='celda-tipo'>Mínimo</td>";
                
                foreach ($todas_tiendas as $t) {
                    $valor = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['minimo'] : 0;
                    echo "<td class='celda-tienda valor-minimo'>" . number_format($valor) . "</td>";
                }
                echo "</tr>";

                // Fila de Estatus
                echo "<tr class='estatus-row'>";
                echo "<td class='celda-tipo'>Estatus</td>";
                $colspan = count($todas_tiendas);
                echo "<td colspan='$colspan' class='estatus-cell'>";
                echo "<span class='estatus-valor'>" . htmlspecialchars($data['estado']) . "</span>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<div class='no-resultados'>";
            echo "<h3>❌ No se encontraron resultados</h3>";
            if (!empty($filtro_estilo)) {
                echo "<p>No hay datos que coincidan con el filtro: \"" . htmlspecialchars($filtro_estilo) . "\"</p>";
            } else {
                echo "<p>No hay datos disponibles para el período seleccionado.</p>";
            }
            echo "</div>";
        }

        // Función para generar Excel
        function generarExcel($datos, $tiendas, $fecha_inicio, $fecha_fin, $filtro = '') {
            // Establecer codificación UTF-8
            $html = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <style>
                    table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
                    th, td { border: 1px solid #000; padding: 8px; text-align: center; }
                    .header { background-color: #4472C4; color: white; font-weight: bold; font-size: 12px; }
                    .proveedor { background-color: #F2F2F2; text-align: left; font-weight: bold; vertical-align: middle; }
                    .estilo { background-color: #F2F2F2; text-align: left; font-weight: bold; vertical-align: middle; }
                    .tipo { background-color: #E7E6E6; font-weight: bold; text-align: center; }
                    .venta { background-color: #E2EFDA; }
                    .existencia { background-color: #FFF2CC; }
                    .minimo { background-color: #F4CCCC; }
                    .low-stock { background-color: #FF6B6B; color: white; font-weight: bold; }
                    .estatus { background-color: #D9D9D9; font-style: italic; text-align: center; }
                </style>
            </head>
            <body>
                <h2 style="text-align: center; color: #1976d2;">REPORTE DE VENTAS POR TIENDA</h2>
                <h3 style="text-align: center; color: #666;">Análisis Consolidado por Estilo y Proveedor</h3>
                <p style="text-align: center;"><strong>Período:</strong> ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</p>';
                
            if (!empty($filtro)) {
                $html .= '<p style="text-align: center;"><strong>Filtro aplicado:</strong> ' . htmlspecialchars($filtro) . '</p>';
            }
            
            $html .= '<p style="text-align: center;"><strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . '</p>
                <br>
                <table>
                    <tr class="header">
                        <th>Proveedor</th>
                        <th>Estilo-Grupo-Color</th>
                        <th>Tipo</th>';
            
            foreach ($tiendas as $t) {
                $html .= '<th>Tienda ' . $t . '</th>';
            }
            
            $html .= '</tr>';
            
            foreach ($datos as $data) {
                // Fila de Ventas
                $html .= '<tr class="venta">
                    <td rowspan="4" class="proveedor">' . htmlspecialchars($data['proveedor']) . '</td>
                    <td rowspan="4" class="estilo">' . htmlspecialchars($data['estilo']) . '</td>
                    <td class="tipo">Venta</td>';
                
                foreach ($tiendas as $t) {
                    $valor = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['venta'] : 0;
                    $html .= '<td>' . number_format($valor) . '</td>';
                }
                $html .= '</tr>';
                
                // Fila de Existencias
                $html .= '<tr class="existencia">
                    <td class="tipo">Existencia</td>';
                foreach ($tiendas as $t) {
                    $valor = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['existencia'] : 0;
                    $minimo = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['minimo'] : 0;
                    $clase = ($valor < $minimo && $minimo > 0) ? 'low-stock' : '';
                    $html .= '<td class="' . $clase . '">' . number_format($valor) . '</td>';
                }
                $html .= '</tr>';
                
                // Fila de Mínimos
                $html .= '<tr class="minimo">
                    <td class="tipo">Mínimo</td>';
                foreach ($tiendas as $t) {
                    $valor = isset($data['tiendas'][$t]) ? $data['tiendas'][$t]['minimo'] : 0;
                    $html .= '<td>' . number_format($valor) . '</td>';
                }
                $html .= '</tr>';
                
                // Fila de Estatus
                $html .= '<tr class="estatus">
                    <td class="tipo">Estatus</td>
                    <td colspan="' . count($tiendas) . '">' . htmlspecialchars($data['estado']) . '</td>
                </tr>';
            }
            
            $html .= '</table>
                <br>
                <p style="font-size: 10px; color: #666; text-align: center;">
                    Reporte generado automáticamente el ' . date('d/m/Y H:i:s') . '
                </p>
            </body></html>';
            
            return $html;
        }
        ?>
    </div>
</div>