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

// Usar solo la fecha inicial para el reporte diario
$fecha_inicio = date('d/m/Y', strtotime($fi));
$fecha_dia = date("d/m/Y");
$hora_impresion = getdate();
$h = sprintf("%02d", $hora_impresion['hours']);
$m = sprintf("%02d", $hora_impresion['minutes']);
$s = sprintf("%02d", $hora_impresion['seconds']);

$tiendas = explode(',', $tienda);
sort($tiendas);
?>

<style>
    body { 
        font-family: Arial, sans-serif; 
        font-size: 11px; 
        background: #FFFFFF;
        margin: 0;
        padding: 20px;
    }
    .reporte-container { 
        max-width: 400px;
        margin: 0 auto;
    }
    .header {
        text-align: center;
        margin-bottom: 15px;
    }
    .logo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #000;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        font-size: 24px;
    }
    .company-name {
        font-weight: bold;
        font-size: 13px;
        margin: 3px 0;
    }
    .report-title {
        font-weight: bold;
        font-size: 12px;
        margin: 3px 0;
    }
    .store-info {
        font-weight: bold;
        font-size: 11px;
        margin: 2px 0;
    }
    .divider {
        border-top: 1px solid #000;
        margin: 10px 0;
    }
    .section-header {
        text-align: center;
        font-weight: bold;
        font-size: 11px;
        margin: 15px 0 5px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-bottom: 10px;
    }
    .header-row {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }
    .header-row td {
        font-weight: bold;
        padding: 2px;
        text-align: center;
    }
    .data-row td {
        padding: 1px 2px;
        border: none;
    }
    .total-row {
        border-top: 1px solid #000;
        font-weight: bold;
    }
    .total-row td {
        padding: 2px;
    }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .user-info {
        font-size: 9px;
        margin-top: 15px;
    }
    @media print {
        body { margin: 0; padding: 10px; }
        .toolbar { display: none; }
    }
</style>

<div class="reporte-container">
    <?php
    foreach ($tiendas as $tienda_actual) {
        // Obtener nombre de la tienda
        $query_tienda = "SELECT store_name FROM rps.store WHERE store_no = {$tienda_actual}";
        $resultado_tienda = consultaOracle(5, $query_tienda);
        $nom_tienda = !empty($resultado_tienda) ? $resultado_tienda[0]['STORE_NAME'] : 'TIENDA NO. ' . $tienda_actual;

        // Obtener subsidiaria
        $query_sub = "SELECT sbs_name FROM rps.subsidiary WHERE sbs_no = {$sbs}";
        $resultado_sub = consultaOracle(5, $query_sub);
        $subsidiaria = !empty($resultado_sub) ? $resultado_sub[0]['SBS_NAME'] : 'Subsidiaria ' . $sbs;
    ?>

    <!-- HEADER -->
    <div class="header">
        <div class="logo">ROY</div>
        <div class="company-name">INTERNACIONAL DE CALZADO, S.A.</div>
        <div class="report-title">DIARIO DE VENTAS</div>
        <div class="store-info"><?php echo strtoupper($nom_tienda); ?></div>
        <div class="store-info">Fecha de Corte <?php echo $fecha_inicio; ?></div>
        <div class="store-info">Fecha y Hora de Impresion <?php echo $fecha_dia.' '.$h.':'.$m.':'.$s; ?></div>
    </div>

    <div class="divider"></div>

    <!-- DOCUMENTOS/FACTURAS -->
    <table>
        <tr class="header-row">
            <td style="width: 50%;">DOC.</td>
            <td style="width: 25%;">FECHA</td>
            <td style="width: 25%;">MONTO</td>
        </tr>
        <?php
        $query_facturas = "
            SELECT 
                D.CUST_FIELD||'-'|| LPAD(D.DOC_NO,8,'0') doc, 
                TRUNC(d.invc_post_date) fecha,
                ROUND(SUM(CASE 
                    WHEN D.receipt_type=0 THEN ((DI.price-(DI.price*NVL(D.disc_perc,0)/100))*(DI.qty))
                    WHEN D.receipt_type=1 THEN ((DI.price-(DI.price*NVL(D.disc_perc,0)/100))*(DI.qty))*-1 
                END),2) - SUM(NVL( di.lty_piece_of_tbr_disc_amt,0))  monto_fac
            FROM rps.document d 
            INNER JOIN rps.document_item di ON (d.sid=di.doc_sid)
            INNER JOIN rps.invn_sbs_item i ON (i.sid=di.invn_sbs_item_sid AND i.sbs_sid=d.subsidiary_sid)
            WHERE d.status=4
                AND d.receipt_type<>2
                AND d.sbs_no={$sbs} 
                AND d.store_no={$tienda_actual}
                AND d.invc_post_date BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            GROUP BY D.CUST_FIELD||'-'|| LPAD(D.DOC_NO,8,'0'), TRUNC(d.invc_post_date)
            ORDER BY TRUNC(d.invc_post_date), D.CUST_FIELD||'-'|| LPAD(D.DOC_NO,8,'0')
        ";
        
        $resultado_facturas = consultaOracle(5, $query_facturas);
        $total_facturas = 0;
        
        if (!empty($resultado_facturas)) {
            foreach ($resultado_facturas as $factura) {
                $total_facturas += $factura['MONTO_FAC'];
        ?>
                <tr class="data-row">
                    <td class="text-left"><?php echo $factura['DOC']; ?></td>
                    <td class="text-center"><?php echo date("d/m/Y", strtotime($factura['FECHA'])); ?></td>
                    <td class="text-right"><?php echo $sim . " " . number_format($factura['MONTO_FAC'], 2); ?></td>
                </tr>
        <?php
            }
        }
        ?>
        <tr class="total-row">
            <td colspan="2" class="text-left">TOTAL FACTURAS</td>
            <td class="text-right"><?php echo $sim . " " . number_format($total_facturas, 2); ?></td>
        </tr>
    </table>

    <!-- RESUMEN DE MOVIMIENTOS -->
    <div class="section-header">RESUMEN DE MOVIMIENTOS</div>
    <table>
        <tr class="header-row">
            <td style="width: 70%;">DESCRIPCION</td>
            <td style="width: 30%;">UNIDADES</td>
        </tr>
        <?php
        $query_movimientos = "
            SELECT 'COMPRAS_ROY' Descripcion, NVL(SUM(DECODE(v.vou_type, 1, vi.qty * -1, vi.qty)),0) unidades 
            FROM rps.voucher v 
            INNER JOIN rps.vou_item vi ON (v.sid=vi.vou_sid)
            INNER JOIN rps.store s ON (v.sbs_sid=s.sbs_sid AND v.store_sid=s.sid)
            WHERE v.CREATEDBY_SID=680861302000166260
                AND v.vou_class=0
                AND s.store_no={$tienda_actual}
                AND v.created_datetime BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            UNION ALL
            SELECT 'RECIBOS' Descripcion, NVL(SUM(DECODE(v.vou_type, 1, vi.qty * -1, vi.qty)),0) unidades 
            FROM rps.voucher v 
            INNER JOIN rps.vou_item vi ON (v.sid=vi.vou_sid)
            INNER JOIN rps.store s ON (v.sbs_sid=s.sbs_sid AND v.store_sid=s.sid)
            WHERE v.CREATEDBY_SID<>680861302000166260
                AND v.vou_class=0
                AND s.store_no={$tienda_actual}
                AND v.created_datetime BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            UNION ALL
            SELECT 'FACTURA' Descripcion,
                SUM(CASE 
                    WHEN d.receipt_type=0 THEN (di.qty)
                    WHEN d.receipt_type=1 THEN (di.qty)*-1 
                END) *-1 unidades
            FROM rps.document d 
            INNER JOIN rps.document_item di ON (d.sid=di.doc_sid)
            WHERE d.status=4
                AND d.receipt_type<>2
                AND d.store_no={$tienda_actual}
                AND d.invc_post_date BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            UNION ALL
            SELECT 'TRANS_SALIDA' Descripcion, 
                NVL(SUM(CASE 
                    WHEN NVL(s.reversed_flag,0)<>2 THEN si.qty * -1 
                    ELSE si.qty 
                END),0)*-1 unidades 
            FROM rps.slip s 
            INNER JOIN rps.slip_item si ON (s.sid=si.slip_sid)
            INNER JOIN rps.store st ON (s.out_sbs_sid=st.sbs_sid AND s.out_store_sid=st.sid)
            WHERE st.store_no={$tienda_actual}
                AND s.created_datetime BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
        ";
        
        $resultado_movimientos = consultaOracle(5, $query_movimientos);
        $total_uds = 0;
        
        if (!empty($resultado_movimientos)) {
            foreach ($resultado_movimientos as $movimiento) {
                $total_uds += $movimiento['UNIDADES'];
        ?>
                <tr class="data-row">
                    <td class="text-left"><?php echo $movimiento['DESCRIPCION']; ?></td>
                    <td class="text-right"><?php echo number_format($movimiento['UNIDADES']); ?></td>
                </tr>
        <?php
            }
        }
        ?>
        <tr class="total-row">
            <td class="text-left">TOTAL</td>
            <td class="text-right"><?php echo number_format($total_uds); ?></td>
        </tr>
    </table>

    <!-- RESUMEN FORMAS DE PAGO -->
    <div class="section-header">RESUMEN FORMAS DE PAGO</div>
    <table>
        <tr class="header-row">
            <td style="width: 70%;">DESCRIPCION</td>
            <td style="width: 30%;">MONTO</td>
        </tr>
        <?php
        $query_formas_pago = "
            SELECT 
                CASE 
                    WHEN t.tender_type=2 THEN TO_CHAR(tc.card_type_name) 
                    WHEN t.tender_type=0 THEN 'Efectivo' 
                    WHEN t.tender_type=1 THEN 'Check'
                    WHEN t.tender_type=3 THEN 'COD'
                    WHEN t.tender_type=4 THEN 'Charge'
                    WHEN t.tender_type=5 THEN 'Store Credit'
                    WHEN t.tender_type=6 THEN 'Split'
                    WHEN t.tender_type=7 THEN 'Deposit'
                    WHEN t.tender_type=8 THEN 'Payments'
                    WHEN t.tender_type=9 THEN 'Gift Certificate'
                    WHEN t.tender_type=10 THEN 'Gift Card'
                    WHEN t.tender_type=11 THEN 'VISANET'
                    WHEN t.tender_type=12 THEN 'Foreign Currency'
                    WHEN t.tender_type=13 THEN 'Traveler Check'
                    WHEN t.tender_type=14 THEN 'Check in F/CD'
                    WHEN t.tender_type=15 THEN 'Central Gift Card'
                    WHEN t.tender_type=16 THEN 'Central Gift Certificate'
                    WHEN t.tender_type=17 THEN 'Central Credit'
                    ELSE 'Otro' 
                END descripcion,
                SUM(t.amount) monto_P
            FROM rps.document d 
            INNER JOIN rps.tender t ON (d.sid=t.doc_sid)
            LEFT JOIN rps.tender_credit_card tc ON (t.sid=tc.tender_sid)
            WHERE d.status=4
                AND d.receipt_type<>2
                AND d.sbs_no={$sbs}
                AND d.store_no={$tienda_actual}
                AND d.invc_post_date BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            GROUP BY t.tender_type, tc.card_type_name
            ORDER BY t.tender_type
        ";
        
        $resultado_formas_pago = consultaOracle(5, $query_formas_pago);
        $total_monto = 0;
        
        if (!empty($resultado_formas_pago)) {
            foreach ($resultado_formas_pago as $forma_pago) {
                $total_monto += $forma_pago['MONTO_P'];
        ?>
                <tr class="data-row">
                    <td class="text-left"><?php echo $forma_pago['DESCRIPCION']; ?></td>
                    <td class="text-right"><?php echo $sim . " " . number_format($forma_pago['MONTO_P'], 2); ?></td>
                </tr>
        <?php
            }
        }
        ?>
        <tr class="total-row">
            <td class="text-left">GRAN TOTAL</td>
            <td class="text-right"><?php echo $sim . " " . number_format($total_monto, 2); ?></td>
        </tr>
    </table>

    <!-- DETALLE DE PUNTOS CANJEADOS -->
    <div class="section-header">DETALLE DE PUNTOS CANJEADOS</div>
    <table>
        <tr class="header-row">
            <td style="width: 40%;">DESCRIPCION</td>
            <td style="width: 30%;">PUNTOS</td>
            <td style="width: 30%;">MONTO</td>
        </tr>
        <?php
        $query_puntos = "
            SELECT 'Puntos Lealtad' descripcion, 
                SUM(NVL(LTY_SALE_USED_POINTS_P,0)) PUNTOS, 
                SUM(lty_redeem_amt) monto_PT 
            FROM RPS.DOCUMENT
            WHERE CREATED_DATETIME BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
                AND sbs_no = {$sbs}
                AND STORE_NO = {$tienda_actual}
                AND (LTY_SALE_USED_POINTS_P > 0 OR lty_redeem_amt > 0)
        ";
        
        $resultado_puntos = consultaOracle(5, $query_puntos);
        $total_monto_puntos = 0;
        
        if (!empty($resultado_puntos) && $resultado_puntos[0]['PUNTOS'] > 0) {
            foreach ($resultado_puntos as $punto) {
                $total_monto_puntos += $punto['MONTO_PT'];
        ?>
                <tr class="data-row">
                    <td class="text-left"><?php echo $punto['DESCRIPCION']; ?></td>
                    <td class="text-center"><?php echo number_format($punto['PUNTOS'], 2); ?></td>
                    <td class="text-right"><?php echo $sim . " " . number_format($punto['MONTO_PT'], 2); ?></td>
                </tr>
        <?php
            }
        } else {
        ?>
            <tr class="data-row">
                <td class="text-left">Puntos Lealtad</td>
                <td class="text-center">0.00</td>
                <td class="text-right"><?php echo $sim . " 0.00"; ?></td>
            </tr>
        <?php
        }
        ?>
        <tr class="total-row">
            <td colspan="2" class="text-left">GRAN TOTAL</td>
            <td class="text-right"><?php echo $sim . " " . number_format($total_monto_puntos, 2); ?></td>
        </tr>
    </table>

    <!-- RESUMEN DE VENTAS POR VENDEDOR -->
    <div class="section-header">RESUMEN DE VENTAS POR VENDEDOR</div>
    <table>
        <tr class="header-row">
            <td style="width: 15%;">COD</td>
            <td style="width: 55%;">NOMBRE</td>
            <td style="width: 30%;">MONTO C/IVA</td>
        </tr>
        <?php
        $query_vendedores = "
            SELECT d.employee1_login_name cod, 
                d.employee1_full_name nombre,
                ROUND(SUM(CASE 
                    WHEN D.receipt_type=0 THEN ((DI.price-(DI.price*NVL(D.disc_perc,0)/100))*(DI.qty))
                    WHEN D.receipt_type=1 THEN ((DI.price-(DI.price*NVL(D.disc_perc,0)/100))*(DI.qty))*-1 
                END),2) - NVL(MAX(D.LTY_REDEEM_AMT),0) monto_V
            FROM rps.document d 
            INNER JOIN rps.document_item di ON (d.sid=di.doc_sid)
            WHERE d.status=4
                AND d.receipt_type<>2
                AND d.sbs_no={$sbs}
                AND d.store_no={$tienda_actual}
                AND d.invc_post_date BETWEEN TO_DATE('{$fi} 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                AND TO_DATE('{$fi} 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            GROUP BY d.employee1_login_name, d.employee1_full_name
            ORDER BY d.employee1_login_name
        ";
        
        $resultado_vendedores = consultaOracle(5, $query_vendedores);
        $total_facturas_v = 0;
        
        if (!empty($resultado_vendedores)) {
            foreach ($resultado_vendedores as $vendedor) {
                $total_facturas_v += $vendedor['MONTO_V'];
        ?>
                <tr class="data-row">
                    <td class="text-center"><?php echo $vendedor['COD']; ?></td>
                    <td class="text-left"><?php echo $vendedor['NOMBRE']; ?></td>
                    <td class="text-right"><?php echo $sim . " " . number_format($vendedor['MONTO_V'], 2); ?></td>
                </tr>
        <?php
            }
        }
        ?>
        <tr class="total-row">
            <td colspan="2" class="text-left">GRAN TOTAL</td>
            <td class="text-right"><?php echo $sim . " " . number_format($total_facturas_v, 2); ?></td>
        </tr>
    </table>

    <div class="user-info">
        USUARIO: <?php echo isset($_SESSION['user'][0]) ? $_SESSION['user'][0] : 'wgarcia'; ?>
    </div>

    <?php
        if (count($tiendas) > 1) {
            echo "<div style='page-break-after: always; margin-top: 30px;'></div>";
        }
    }
    ?>
</div>

<script>
function exportarExcel() {
    alert('Función de exportación a Excel pendiente de implementar');
}

// Auto print si se requiere
if (window.location.search.includes('print=1')) {
    window.print();
}
</script>