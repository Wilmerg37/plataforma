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
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13px;
        background-color: #f5f7fa;
        color: #333;
    }

    .reporte-container {
        padding: 30px;
        background-color: #fff;
        border-radius: 8px;
        margin: 0 auto;
        max-width: 1200px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    h2, h3, h4 {
        text-align: center;
        margin: 8px 0;
    }

    h2 {
        font-size: 22px;
        color: #1a73e8;
    }

    h3 {
        font-size: 18px;
        color: #444;
    }

    h4 {
        font-size: 15px;
        color: #666;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        background-color: #fff;
        border: 1px solid #ddd;
    }

    th {
        background-color: #1a73e8;
        color: #fff;
        padding: 8px;
        text-transform: uppercase;
        font-size: 12px;
    }

    td {
        padding: 6px;
        text-align: center;
        border: 1px solid #eee;
        font-size: 13px;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .estilo {
        background-color: #e3eaf4;
        padding: 12px 15px;
        font-weight: bold;
        font-size: 17px;
        margin: 25px 0 10px;
        border-left: 5px solid #1a73e8;
    }

    .estatus {
        font-style: italic;
        font-weight: bold;
        font-size: 15px;
        margin: 15px 0;
        padding-left: 10px;
        color: #555;
    }

    hr {
        border: none;
        border-top: 1px solid #ccc;
        margin: 30px 0;
    }

    p {
        text-align: center;
        color: #999;
        font-style: italic;
    }

    .low-stock {
    background-color: #fdecea;
    color: #d93025;
    font-weight: bold;
}
</style>


<div class="reporte-container">
<?php
foreach ($tiendas as $tienda) {
    echo "<h2>LOS 25 MÁS VENDIDOS</h2>";
    echo "<h3>POR ESTILO Y PROVEEDOR</h3>";
    echo "<h4>TIENDA: " . htmlspecialchars($tienda) . "</h4>";
    echo "<h4>DEL: " . date('d/m/Y', strtotime($fi)) . " &nbsp;&nbsp; AL: " . date('d/m/Y', strtotime($ff)) . "</h4>";

    $query = "
 
WITH ventas AS (
    SELECT 
        d.store_code, 
        v.vend_name, 
        SUBSTR(i.description2, 1, 12) AS estilo,
        i.item_size AS siz,
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
        AND d.store_no = {$tienda}
        AND d.invc_post_date BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                 AND TO_DATE('{$ff} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
    GROUP BY d.store_code, v.vend_name, SUBSTR(i.description2, 1, 12), i.item_size
),
stock AS (
    SELECT 
        s.store_code,
        v.vend_name,
        SUBSTR(i.description2, 1, 12) AS estilo,
        i.item_size AS siz,
        SUM(q.qty) AS exist
    FROM rps.invn_sbs_item i
    JOIN rps.invn_sbs_item_qty q ON i.sid = q.invn_sbs_item_sid AND i.sbs_sid = q.sbs_sid
    JOIN rps.store s ON q.store_sid = s.sid
    JOIN rps.subsidiary sub ON q.sbs_sid = sub.sid
    LEFT JOIN rps.vendor v ON i.vend_sid = v.sid AND i.sbs_sid = v.sbs_sid
    WHERE sub.sbs_no = {$sbs}
      AND s.store_no = {$tienda}
    GROUP BY s.store_code, v.vend_name, SUBSTR(i.description2, 1, 12), i.item_size
),
minimos AS (
    SELECT 
        s.store_code,
        v.vend_name,
        SUBSTR(i.description2, 1, 12) AS estilo,
        i.item_size AS siz,
        MIN(q.min_qty) AS mini
    FROM rps.invn_sbs_item i
    JOIN rps.invn_minmax m ON m.invn_item_sid = i.sid AND m.sbs_sid = i.sbs_sid
    JOIN rps.invn_minmax_qty q ON m.sid = q.invn_minmax_sid
    JOIN rps.store s ON q.store_sid = s.sid
    JOIN rps.subsidiary sub ON m.sbs_sid = sub.sid
    LEFT JOIN rps.vendor v ON i.vend_sid = v.sid AND i.sbs_sid = v.sbs_sid
    WHERE sub.sbs_no = {$sbs}
      AND s.store_no = {$tienda}
    GROUP BY s.store_code, v.vend_name, SUBSTR(i.description2, 1, 12), i.item_size
),
top_estilos AS (
    SELECT estilo
    FROM ventas
    GROUP BY estilo
    ORDER BY SUM(qty_sold) DESC
    FETCH FIRST 25 ROWS ONLY
)
SELECT 
    COALESCE(v.estilo, s.estilo, m.estilo) AS estilo,
    COALESCE(v.siz, s.siz, m.siz) AS siz,
    COALESCE(v.vend_name, s.vend_name, m.vend_name) AS vend_name,
    COALESCE(v.estado, '-') AS estado,
    NVL(v.qty_sold, 0) AS qty_sold,
    NVL(s.exist, 0) AS exist,
    NVL(m.mini, 0) AS mini
FROM ventas v
FULL OUTER JOIN stock s 
    ON v.store_code = s.store_code 
    AND v.estilo = s.estilo 
    AND v.siz = s.siz
FULL OUTER JOIN minimos m 
    ON COALESCE(v.store_code, s.store_code) = m.store_code 
    AND COALESCE(v.estilo, s.estilo) = m.estilo 
    AND COALESCE(v.siz, s.siz) = m.siz
WHERE COALESCE(v.estilo, s.estilo, m.estilo) IN (
    SELECT estilo FROM top_estilos
)
ORDER BY estilo, siz
";

    $resultado = consultaOracle(5, $query);

    if (!empty($resultado)) {
        $agrupado = [];
        foreach ($resultado as $fila) {
            $clave = $fila['ESTILO'];
            $talla = $fila['SIZ'];

            if (!isset($agrupado[$clave])) {
                $agrupado[$clave] = [
                    'proveedor' => $fila['VEND_NAME'],
                    'estado' => $fila['ESTADO'],
                    'tallas' => [],
                    'total_venta' => 0
                ];
            }

            $agrupado[$clave]['tallas'][$talla] = [
                'venta' => $fila['QTY_SOLD'],
                'exist' => $fila['EXIST'],
                'min' => $fila['MINI']
            ];

            $agrupado[$clave]['total_venta'] += $fila['QTY_SOLD'];
        }

        uasort($agrupado, function($a, $b
) {
return $b['total_venta'] <=> $a['total_venta'];
});

    foreach ($agrupado as $clave => $data) {
        echo "<div class='estilo' style='background-color:#D3D3D3; font-weight:bold; font-size:20px;'>"
          . " Estilo-Grupo-Color: "  . htmlspecialchars($clave) . " - Total Vendido: " . number_format($data['total_venta']) 
           
            . "</div>";

        $tallas_completas = array_keys($data['tallas']);
        sort($tallas_completas, SORT_NUMERIC);

        echo "<table>";
        echo "<tr><th>Proveedor</th><th></th>";
        foreach ($tallas_completas as $t) {
            echo "<th>" . htmlspecialchars($t) . "</th>";
        }
        echo "<th>Total</th></tr>";

        // Venta
        $total_venta = 0;
        echo "<tr>";
        echo "<td rowspan='3' style='font-weight:normal;'>" . htmlspecialchars($data['proveedor']) . "</td>";
        echo "<td>venta</td>";
        foreach ($tallas_completas as $t) {
            $val = $data['tallas'][$t]['venta'];
            $total_venta += $val;
            echo "<td>" . number_format($val) . "</td>";
        }
        echo "<td><strong>" . number_format($total_venta) . "</strong></td>";
        echo "</tr>";

        // Existencia
       // Existencia
$total_exist = 0;
echo "<tr><td>Existencia</td>";
foreach ($tallas_completas as $t) {
    $val = $data['tallas'][$t]['exist'];
    $min = $data['tallas'][$t]['min'];
    $total_exist += $val;

    // Aquí aplicamos estilo si el stock es menor al mínimo
    $clase = ($val < $min) ? "class='low-stock'" : "";
    echo "<td $clase>" . number_format($val) . "</td>";
}
echo "<td><strong>" . number_format($total_exist) . "</strong></td>";
echo "</tr>";


        // Mínimo
        $total_min = 0;
        echo "<tr><td>mínimo</td>";
        foreach ($tallas_completas as $t) {
            $val = $data['tallas'][$t]['min'];
            $total_min += $val;
            echo "<td>" . number_format($val) . "</td>";
        }
        echo "<td><strong>" . number_format($total_min) . "</strong></td>";
        echo "</tr>";

        echo "</table>";
        echo "<div class='estatus' style='font-weight:bold; font-size:17px;'>Estatus: " . htmlspecialchars($data['estado']) . "</div>";
        echo "<hr style='border: 1px solid #ccc; margin: 10px 0;'>";
    }
    } else {
        echo "<p>No hay resultados para esta tienda.</p>";
    }

    echo "<hr>";
}
?>
</div>

