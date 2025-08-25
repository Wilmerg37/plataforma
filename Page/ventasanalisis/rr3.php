<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/ventasanalisis/queryRpro.php";
require_once "../../vendor/autoload.php"; // Para DomPDF

use Dompdf\Dompdf;
use Dompdf\Options;

$tienda = isset($_POST['tienda']) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];

$sim = impuestoSimbolo($sbs);
$sim = is_array($sim) ? (isset($sim[0]) ? $sim[0] : '') : $sim;

$iva = isset($_POST['iva']) ? $_POST['iva'] : '';
$vacacionista = isset($_POST['vacacionista']) ? $_POST['vacacionista'] : '';
$filtro = ($vacacionista == '1') ? '' : " AND EMP.EMPL_NAME < '5000'";

$semanas = rangoWe($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);

// Función para obtener datos de una tienda específica
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
                    'stock' => 0
                ];
            }

            if ($tipo === 'VENTA') {
                $agrupado[$clave]['venta'] += $pares;
                if ($precio > 0 && $agrupado[$clave]['precio'] == 0) {
                    $agrupado[$clave]['precio'] = $precio;
                }
            } elseif ($tipo === 'STOCK') {
                $agrupado[$clave]['stock'] += $pares;
                // Priorizar precio del stock - siempre sobrescribir
                if ($precio > 0) {
                    $agrupado[$clave]['precio'] = $precio;
                }
            }
        }
    }

    return $agrupado;
}

// Obtener datos de todas las tiendas
$datos_todas_tiendas = [];
foreach ($tiendas as $tienda_num) {
    $datos_todas_tiendas[$tienda_num] = obtenerDatosTienda($tienda_num, $sbs, $fi, $ff);
}

// Debug: Verificar estructura de datos
// Elimina esto después de corregir el error
/*
foreach ($datos_todas_tiendas as $tienda => $datos) {
    foreach ($datos as $clave => $producto) {
        if (is_array($producto['precio'])) {
            echo "ERROR: Precio es array en tienda $tienda, producto $clave: ";
            print_r($producto['precio']);
            echo "<br>";
        }
    }
}
*/

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
            // Si ya existe, mantener el precio más alto (del stock)
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

foreach ($datos_todas_tiendas as $datos) {
    foreach ($datos as $producto) {
        $total_ventas += (int)$producto['venta'];
        $total_stock += (int)$producto['stock'];
        if ($producto['stock'] < $producto['venta'] && $producto['venta'] > 0) {
            $productos_bajo_stock++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Análisis de Ventas vs Stock</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/estilosventasanalisis.css?v=<?php echo time(); ?>">
</head>
<script src="../js/advanced_inventory.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<body>
    <div class="report-container fade-in">
        <!-- Header del reporte -->
        <div class="report-header">
            <div class="report-header-content">
                <h1 class="report-title">
                    <i class="fas fa-chart-line"></i>
                    Reporte de Inventario
                </h1>
                <p class="report-subtitle">Análisis de ventas vs stock por tienda</p>
            </div>
        </div>

        <!-- Información del reporte -->
        <div class="report-info">
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-store info-icon"></i>
                    <div>
                        <div class="info-label">Tiendas</div>
                        <div class="info-value"><?php echo implode(', ', array_map(function($t) { return "Tienda $t"; }, $tiendas)); ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar-alt info-icon"></i>
                    <div>
                        <div class="info-label">Período</div>
                        <div class="info-value"><?php echo "$fi - $ff"; ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-globe info-icon"></i>
                    <div>
                        <div class="info-label">País</div>
                        <div class="info-value"><?php echo $pais; ?></div>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock info-icon"></i>
                    <div>
                        <div class="info-label">Generado</div>
                        <div class="info-value"><?php echo date('d/m/Y H:i'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controles -->
        <div class="controls-section">
            <div class="controls-left">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Buscar producto..." id="searchInput">
                </div>
            </div>
            <div class="controls-right">
                <button class="btn btn-success" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i>
                    Exportar Excel
                </button>
               <button class="btn btn-info" onclick="exportToPDF()">
    <i class="fas fa-file-pdf"></i> Exportar PDF
</button>
                <button class="btn btn-outline" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    Imprimir
                </button>
            </div>
        </div>

        <!-- Tabla de datos -->
        <div class="table-container">
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th rowspan="2"><i class="fas fa-tag"></i> Género</th>
                        <th rowspan="2"><i class="fas fa-list"></i> Categoría</th>
                        <th rowspan="2"><i class="fas fa-shoe-prints"></i> Estilo</th>
                        <th rowspan="2"><i class="fas fa-palette"></i> Color</th>
                        <th rowspan="2"><i class="fas fa-dollar-sign"></i> Precio</th>
                        <?php foreach ($tiendas as $tienda_num): ?>
                            <th colspan="2" class="store-column"><i class="fas fa-store"></i> TIENDA<?php echo $tienda_num; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($tiendas as $tienda_num): ?>
                            <th class="venta-column"><i class="fas fa-shopping-cart"></i> VENTA</th>
                            <th class="stock-column"><i class="fas fa-boxes"></i> STOCK</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos_unicos as $clave => $producto): ?>
                        <tr>
                            <td><?php 
                                $genero = is_array($producto['genero']) ? $producto['genero'][0] : $producto['genero'];
                                echo htmlspecialchars($genero ?? ''); 
                            ?></td>
                            <td><?php 
                                $categoria = is_array($producto['categoria']) ? $producto['categoria'][0] : $producto['categoria'];
                                echo htmlspecialchars($categoria ?? ''); 
                            ?></td>
                            <td><?php 
                                $estilo = is_array($producto['estilo']) ? $producto['estilo'][0] : $producto['estilo'];
                                echo htmlspecialchars($estilo ?? ''); 
                            ?></td>
                            <td><?php 
                                $color = is_array($producto['color']) ? $producto['color'][0] : $producto['color'];
                                echo htmlspecialchars($color ?? ''); 
                            ?></td>
                           <td><?php echo $sim . number_format((float)$producto['precio'], 2); ?></td>


                            
                            <?php foreach ($tiendas as $tienda_num): ?>
                                <?php
                                $venta = 0;
                                $stock = 0;
                                
                                if (isset($datos_todas_tiendas[$tienda_num][$clave])) {
                                    $dato_tienda = $datos_todas_tiendas[$tienda_num][$clave];
                                    $venta = is_numeric($dato_tienda['venta']) ? (int)$dato_tienda['venta'] : 0;
                                    $stock = is_numeric($dato_tienda['stock']) ? (int)$dato_tienda['stock'] : 0;
                                }
                                
                                $clase_stock = 'stock-column';
                                if ($stock < $venta && $venta > 0) {
                                    $clase_stock .= ' low-stock';
                                } elseif ($stock > ($venta * 2) && $venta > 0) {
                                    $clase_stock .= ' high-stock';
                                }
                                ?>
                                <td class="venta-column"><?php echo number_format($venta); ?></td>
                                <td class="<?php echo $clase_stock; ?>"><?php echo number_format($stock); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Resumen -->
        <div class="summary-section">
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-header">
                        <div class="summary-icon sales">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h3 class="summary-title">Total Ventas</h3>
                        </div>
                    </div>
                    <div class="summary-value"><?php echo number_format($total_ventas); ?></div>
                    <p class="summary-subtitle">Pares vendidos en el período</p>
                </div>

                <div class="summary-card">
                    <div class="summary-header">
                        <div class="summary-icon stock">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div>
                            <h3 class="summary-title">Stock Total</h3>
                        </div>
                    </div>
                    <div class="summary-value"><?php echo number_format($total_stock); ?></div>
                    <p class="summary-subtitle">Pares disponibles</p>
                </div>

                <div class="summary-card">
                    <div class="summary-header">
                        <div class="summary-icon alerts">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h3 class="summary-title">Alertas</h3>
                        </div>
                    </div>
                    <div class="summary-value"><?php echo $productos_bajo_stock; ?></div>
                    <p class="summary-subtitle">Productos con bajo stock</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función de búsqueda
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('dataTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }
                
                rows[i].style.display = found ? '' : 'none';
            }
        });

        // Función para exportar a Excel
        function exportToExcel() {
            const table = document.getElementById('dataTable');
            const wb = XLSX.utils.table_to_book(table, {sheet: "Reporte Inventario"});
            const fecha = new Date().toISOString().slice(0,10);
            XLSX.writeFile(wb, `reporte_inventario_${fecha}.xlsx`);
        }

        // Función para exportar a PDF (usando el print nativo)
        function exportToPDF() {
            window.print();
        }

        // Animaciones al cargar
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.summary-card, .data-table tbody tr');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('fade-in');
                }, index * 50);
            });
        });
    </script>

    <!-- SheetJS para exportar a Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</body>
</html>