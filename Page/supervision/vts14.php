<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

$tienda = (isset($_POST['tienda'])) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];
$sim = impuestoSimbolo($sbs);

$iva = (isset($_POST['iva'])) ? $_POST['iva'] : '';
$vacacionista = (isset($_POST['vacacionista'])) ? $_POST['vacacionista'] : '';
$filtro = '';

if ($vacacionista == '1') {
  $filtro = '';
} else {
  $filtro = " AND EMP.EMPL_NAME < '5000'";
}
$semanas = rangoWe($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Supervisión</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- DomPDF CSS -->
         <link rel="stylesheet" href="../css/estilo14hrs.css?v=<?php echo time(); ?>">
    <style type="text/css" media="print">
        body { font-size: 12px; }
        .no-print { display: none; }
    </style>

   
</head>
<body>

<div class="main-container">
    <?php
    foreach ($tiendas as $tienda) {
        $total = array(
            $factura = 0,
            $pare_roy = 0,
            $pares_otro = 0,
            $tota_pares = 0,
            $accesorios = 0,
            $venta = 0,
            $meta = 0,
            $hora = 0,
            $mt_prs = 0 ,
            $vta_prs = 0 ,
            $dif_prs = 0
        );

        // Query original mantenido exactamente igual
        $query = "
        SELECT  MT.TIENDA,	MT.NOMBRETIENDA,
                       MT.META_PRS,
                             NVL(SUM(A.PAR_ROY),0) + NVL(SUM(A.PAR_OTROS),0)  VENTA_PRS,
                           
                                NVL(SUM(A.PAR_ROY),0) + NVL(SUM(A.PAR_OTROS),0) - MT.META_PRS DIF_PRS,
                      NVL( ROUND(SUM(A.venta_SIN_IVA),2),0) VENTA_SIN_IVA,
                           MT.META_S_IVA,
                            NVL(ROUND(SUM(A.venta_SIN_IVA),2)- (MT.META_S_IVA),0)DIF, 
                            MT.COD_SUP, MT.NOM_SUP, A.DIA, A.FECHA,
                             ROUND(DECODE(SUM(A.TRANSACCIONES),0,SUM(A.CANTIDAD),(SUM(A.CANTIDAD) / SUM(A.TRANSACCIONES))),2)UPT, 
                           ROUND(DECODE(SUM(A.TRANSACCIONES),0,SUM(A.VENTA_SIN_IVA),(SUM(A.VENTA_SIN_IVA) / SUM(A.TRANSACCIONES))),2) QPT
                                           FROM 
                 
        
                                  
                            (SELECT M.TIENDA,M.FECHA,M.META_S_IVA,M.META_C_IVA,M.SEMANA,M.META_PRS,M.ANIO,M.COD_SUPER, ST.STORE_NAME NOMBRETIENDA , ST.UDF1_STRING COD_SUP,ST.UDF2_STRING NOM_SUP FROM ROY_META_DIARIA_TDS M
                                       INNER JOIN RPS.STORE ST ON M.TIENDA = ST.STORE_NO
               WHERE FECHA  between to_date('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
               AND ST.SBS_SID = '680861302000159257'
                    )MT
                   LEFT JOIN 
                   (
                                           select S.UDF1_STRING COD_SUP,S.UDF2_STRING NOM_SUP, t1.store_NO, trunc(t1.created_datetime) FECHA, t1.employee1_login_name COD_VENDEDOR, 
                                           TO_CHAR(T1.CREATED_DATETIME, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') DIA,
                                           t1.employee1_full_name VENDEDOR,
                                             s.store_name NOMBRETIENDA,
                                         --  MAX((SELECT STORE_NAME FROM RPS.STORE  WHERE STORE_NO = s.store_no  AND SBS_SID = '680861302000159257' AND ADDRESS1 IS NOT NULL )) NOMBRETIENDA,
                                           case when t1.receipt_type=0 then 1 when t1.receipt_type=1 then -1 end TRANSACCIONES, 
                                           
                                           sum(case when t1.receipt_type=0 and t2.vend_code='001' then (t2.qty)
                                                    when t1.receipt_type=1 and t2.vend_code='001' then (t2.qty)*-1 end) as par_roy, 
                                           
                                           sum(case when t1.receipt_type=0 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)
                                                    when t1.receipt_type=1 and t2.vend_code <> 001 and SUBSTR(T2.DCS_CODE,1,3)not in ('ACC','SER','PRE','PRO')  then (t2.qty)*-1 end) as par_otros, 
                                           
                                           sum(case when t1.receipt_type=0 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)
                                                    when t1.receipt_type=1 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty)*-1 end) par_acce,
                                                    
                                        sum(case when t1.receipt_type=0  and SUBSTR(T2.DCS_CODE,1,3)not in ('SER','PRE','PRO')   then (T2.qty) 
                        when t1.receipt_type=1  and SUBSTR(T2.DCS_CODE,1,3)not in ('SER','PRE','PRO')  then (T2.qty)*-1 end )as cantidad,           
                                           
                                           sum(case when t1.receipt_type=0 and SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty*T2.PRICE)
                                                    when t1.receipt_type=1 and SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then (t2.qty*T2.PRICE)*-1 end)venta_CON_IVA_ACC,
                                           
                                            sum(case when t1.receipt_type=0 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.price)/1.12*(T2.qty)) 
                                                     when t1.receipt_type=1 and   SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.price)/1.12*(T2.qty))*-1 end ) as venta_sin_iva_ACC,    
                                           
                                           sum(case when t1.receipt_type=0 then (t2.qty*t2.cost) when t1.receipt_type=1 then (t2.qty*t2.cost)*-1 else 0 end) as costo, 
                                           
                                           sum(case WHEN t1.receipt_type=0 AND SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.COST)*(T2.qty))
                                                    when t1.receipt_type=1 AND SUBSTR(T2.DCS_CODE,1,3)= 'ACC' then ((T2.COST)*(T2.qty))*-1 end ) as COSTO_sin_iva_ACC ,
                                                 
                                           NVL(sum(case when t1.receipt_type=0 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))
                                                        when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))*-1 end ),0)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0)) as venta_con_iva, 
                                                 
                                           NVL(sum(case when t1.receipt_type=0 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12 
                                                        when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12*-1 end ),0)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0))/1.12 as venta_sin_iva     
                                           
                                                 
                                           from rps.document t1 
                                               inner join rps.document_item t2 on (t1.sid = t2.doc_sid)					   
                                           INNER join rps.STORE S on (S.sid=t1.STORE_SID)
                                           where 1=1
                                           and t1.status=4 
                                            and t1.receipt_type<>2
                                            and T1.sbs_no=1 
                                                           				
                            and t1.CREATED_DATETIME between to_date('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
                            
                            group by S.UDF1_STRING ,S.UDF2_STRING,   s.store_name,t1.store_NO,  t1.employee1_login_name, t1.employee1_full_name, trunc(t1.created_datetime), TO_CHAR(T1.CREATED_DATETIME, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH'),
                 T1.DOC_NO, t1.receipt_type, t1.disc_amt,NVL( t2.lty_piece_of_tbr_disc_amt,0)
                                )A 

                   
               ON MT.TIENDA = A.STORE_NO  AND MT.FECHA = A.FECHA  
                   WHERE MT.COD_SUP = $tienda
               GROUP BY MT.TIENDA, MT.META_S_IVA,MT.NOMBRETIENDA,MT.COD_SUP, MT.NOM_SUP, MT.META_PRS, A.DIA, A.FECHA
               ORDER BY cast(MT.TIENDA as int), A.FECHA ";
        
        $resultado = consultaOracle(3, $query);      
        $cnt = 1;
    ?>

    <!-- Header del reporte -->
    <div class="report-header">
        <h1 class="supervisor-title">
            <i class="fas fa-user-tie"></i>
            Supervisor: <?php echo $tienda ?>
        </h1>
        <p class="supervisor-subtitle">
            <i class="fas fa-calendar-day"></i>
            Día: <?php echo date('d-m') ?> | Meta del Día: Q <?php echo number_format(MTDS($tienda, $fi, date('Y', strtotime($ff)), $sbs)[0], 2) ?>
        </p>
    </div>

    <!-- Botones de exportación -->
    <div class="export-section no-print">
        <button class="export-btn excel" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i>
            Exportar a Excel
        </button>
        <button class="export-btn pdf" onclick="exportToPDF()">
            <i class="fas fa-file-pdf"></i>
            Exportar a PDF
        </button>
    </div>

    <!-- Tabla de datos -->
    <div class="table-container">
        <div class="table-header">
            <h4>
                <i class="fas fa-table"></i>
                Reporte Detallado de Ventas y Metas
            </h4>
        </div>

        <div class="table-responsive">
            <table class="table table-modern tbrdst" id="reportTable">
                <thead>
                    <tr>
                        <td><i class="fas fa-hashtag"></i> No</td>
                        <td><i class="fas fa-store"></i> Tienda</td>
                        <td><i class="fas fa-building"></i> Nombre de tienda</td>          
                        <td><i class="fas fa-calendar-day"></i> Día</td>
                        <td><i class="fas fa-calendar-alt"></i> Fecha</td>
                        <td><i class="fas fa-bullseye"></i> Meta Prs</td>
                        <td><i class="fas fa-chart-line"></i> Venta Prs</td>
                        <td><i class="fas fa-balance-scale"></i> Dif. Prs</td>
                        <td><i class="fas fa-shopping-cart"></i> UPT</td>
                        <td><i class="fas fa-percentage"></i> Porc. Prs</td>
                        <td><i class="fas fa-traffic-light"></i> Estatus</td>
                        <td class="separator-col">|</td>
                        <td><i class="fas fa-target"></i> Meta del día</td>
                        <td><i class="fas fa-cash-register"></i> Venta del día</td>          
                        <td><i class="fas fa-calculator"></i> Diferencia</td>
                        <td><i class="fas fa-coins"></i> QPT</td>
                        <td><i class="fas fa-chart-pie"></i> %</td>
                        <td><i class="fas fa-flag"></i> Estado</td>
                    </tr>
                </thead>
                
                <tbody>
                    <?php
                    foreach ($resultado as $rdst) {
                        $rowColor = ($rdst[3] == 0) ? 'style="background-color: #fee2e2; color: #991b1b;"' : '';
                    ?>
                        <tr <?php echo $rowColor ?>>
                            <td><strong><?php echo $cnt++ ?></strong></td>            
                            <td><strong><?php echo $rdst[0] ?></strong></td>
                            <td><?php echo $rdst[1] ?></td>
                            <td><?php echo $rdst[10] ?></td>
                            <td><?php echo $rdst[11] ?></td>
                            <td><?php echo $rdst[2] ?></td>
                            <td><strong><?php echo $rdst[3] ?></strong></td>
                            <td style="<?php echo v_vrs_m($rdst[4]) ?>"><?php echo $rdst[4] ?></td>
                            <td><?php echo $rdst[12] ?></td>
                            <td><?php echo Porcentaje($rdst[3], $rdst[2]) . " %" ?></td>
                            <td>
                                <span class="status-indicator <?php echo status(Porcentaje($rdst[3], $rdst[2])) ?>" 
                                      style="<?php echo color(Porcentaje($rdst[3], $rdst[2])) ?>">
                                </span>
                            </td>
                            <td class="separator-col">|</td>
                            <td style="<?php echo v_vrs_m($rdst[6]) ?>"><?php echo iva($iva, $rdst[6], $sbs) ?></td>
                            <td style="<?php echo v_vrs_m($rdst[5]) ?>"><?php echo iva($iva, $rdst[5], $sbs) ?></td>              
                            <td style="<?php echo v_vrs_m($rdst[7]) ?>"><?php echo iva($iva, $rdst[7], $sbs) ?></td>
                            <td><?php echo $sim[0] . " " . number_format($rdst[13], 2) ?></td>
                            <td><?php echo Porcentaje($rdst[5], $rdst[6]) . " %" ?></td>
                            <td>
                                <span class="status-indicator <?php echo status(Porcentaje($rdst[5], $rdst[6])) ?>" 
                                      style="<?php echo color(Porcentaje($rdst[5], $rdst[6])) ?>">
                                </span>
                            </td>
                        </tr>
                    <?php
                        if ($rdst[3] === 'VACACIONISTA') {
                            $rdst[6] = 0;
                        }

                        $total = array(
                            $mt_prs += $rdst[2],
                            $vta_prs += $rdst[3],
                            $dif_prs += $rdst[4],
                            $venta += $rdst[5],        
                            $meta += $rdst[6]
                        );
                    }
                    ?>
                    <tr class="total-row">
                        <td></td>         
                        <td colspan="4" align="center">
                            <strong><i class="fas fa-calculator"></i> TOTAL</strong>
                        </td>
                        <td><?php echo $total[0] ?></td>
                        <td><?php echo $total[1] ?></td>
                        <td style="<?php echo v_vrs_m($total[2]) ?>"><?php echo $total[2] ?></td>
                        <td></td>
                        <td><?php echo Porcentaje($total[1], $total[0]) . " %" ?></td>
                        <td>
                            <span class="status-indicator <?php echo status(Porcentaje($total[1], $total[0])) ?>" 
                                  style="<?php echo color(Porcentaje($total[1], $total[0])) ?>">
                            </span>
                        </td>
                        <td class="separator-col">|</td>                            
                        <td><?php echo iva($iva, $total[4], $sbs) ?></td>
                        <td><?php echo iva($iva, $total[3], $sbs) ?></td>
                        <td style="<?php echo v_vrs_m(DifVentaMeta($total[3], $total[4])) ?>">
                            <?php echo iva($iva, DifVentaMeta($total[3], $total[4]), $sbs) ?>
                        </td>
                        <td></td>
                        <td><?php echo Porcentaje($total[3], $total[4]) . " %" ?></td>
                        <td>
                            <span class="status-indicator <?php echo status(Porcentaje($total[3], $total[4])) ?>" 
                                  style="<?php echo color(Porcentaje($total[3], $total[4])) ?>">
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php } ?>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    // Inicializar DataTables manteniendo la configuración original
    $('.tbrdst').DataTable({
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "responsive": true,
        "autoWidth": false,
        "scrollX": true
    });

    $('.tooltip').tooltip();

    // Función para exportar a Excel
    function exportToExcel() {
        try {
            const wb = XLSX.utils.book_new();
            const table = document.getElementById('reportTable');
            
            if (!table) {
                alert('Tabla no encontrada');
                return;
            }

            // Crear datos para Excel
            const ws_data = [];
            
            // Encabezado del reporte
            ws_data.push(['REPORTE DE SUPERVISIÓN']);
            ws_data.push(['Supervisor: <?php echo $tienda; ?>']);
            ws_data.push(['Fecha: <?php echo date("d-m-Y"); ?>']);
            ws_data.push(['Meta del día: Q <?php echo number_format(MTDS($tienda, $fi, date("Y", strtotime($ff)), $sbs)[0], 2); ?>']);
            ws_data.push(['']);

            // Extraer datos de la tabla
            const rows = table.querySelectorAll('tr');
            
            rows.forEach((row, index) => {
                const cells = row.querySelectorAll('th, td');
                const rowData = [];
                
                cells.forEach(cell => {
                    // Limpiar el texto de la celda
                    let cellText = cell.innerText || cell.textContent || '';
                    cellText = cellText.replace(/\s+/g, ' ').trim();
                    // Remover iconos Font Awesome
                    cellText = cellText.replace(/[\uF000-\uF8FF]|[\u2000-\u3300]|[\uE000-\uF8FF]/g, '').trim();
                    rowData.push(cellText);
                });
                
                if (rowData.length > 0) {
                    ws_data.push(rowData);
                }
            });

            // Crear hoja de cálculo
            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            
            // Configurar anchos de columna
            const colWidths = Array(18).fill().map(() => ({wch: 15}));
            ws['!cols'] = colWidths;

            XLSX.utils.book_append_sheet(wb, ws, 'Reporte_Supervision');

            const filename = `Reporte_Supervision_${<?php echo $tienda; ?>}_${new Date().toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, filename);

            // Mostrar mensaje de éxito
            showNotification('Archivo Excel exportado correctamente', 'success');

        } catch (error) {
            console.error('Error al exportar:', error);
            showNotification('Error al exportar a Excel', 'error');
        }
    }

    // Función para exportar a PDF usando DomPDF (backend)
    function exportToPDF() {
        try {
            // Crear formulario temporal para enviar datos a PHP
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'export_pdf.php'; // Archivo PHP que manejará DomPDF
            form.target = '_blank';

            // Agregar datos necesarios
            const inputs = [
                {name: 'tienda', value: '<?php echo $tienda; ?>'},
                {name: 'fi', value: '<?php echo $fi; ?>'},
                {name: 'ff', value: '<?php echo $ff; ?>'},
                {name: 'sbs', value: '<?php echo $sbs; ?>'},
                {name: 'iva', value: '<?php echo $iva; ?>'},
                {name: 'vacacionista', value: '<?php echo $vacacionista; ?>'},
                {name: 'html_content', value: document.querySelector('.main-container').outerHTML}
            ];

            inputs.forEach(input => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.value = input.value;
                form.appendChild(hiddenInput);
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);

            showNotification('Generando PDF...', 'info');

        } catch (error) {
            console.error('Error al exportar PDF:', error);
            showNotification('Error al exportar a PDF', 'error');
        }
    }

    // Función para mostrar notificaciones
    function showNotification(message, type) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        `;
        notification.innerHTML = `
            <strong>${type === 'success' ? 'Éxito' : type === 'error' ? 'Error' : 'Información'}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remover después de 3 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Efectos visuales adicionales
    $(document).ready(function() {
        // Animación de entrada
        $('.main-container').hide().fadeIn(500);
        
        // Efecto hover en filas de la tabla
        $('.table-modern tbody tr').hover(
            function() {
                $(this).find('td').css('background-color', '#f8fafc');
            },
            function() {
                $(this).find('td').css('background-color', '');
            }
        );
    });

    // Cargar script adicional manteniendo funcionalidad original
    var url = "../Js/supervision/supervisor.js";
    $.getScript(url).fail(function() {
        console.warn('No se pudo cargar el script adicional:', url);
    });
</script>

</body>
</html>