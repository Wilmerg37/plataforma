<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

// Incluir DomPDF
require_once '../vendor/autoload.php'; // Ajusta la ruta según tu instalación
use Dompdf\Dompdf;
use Dompdf\Options;

// Obtener datos del POST
$tienda = $_POST['tienda'] ?? '';
$fi = $_POST['fi'] ?? '';
$ff = $_POST['ff'] ?? '';
$sbs = $_POST['sbs'] ?? '';
$iva = $_POST['iva'] ?? '';
$vacacionista = $_POST['vacacionista'] ?? '';

// Configurar DomPDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);

$dompdf = new Dompdf($options);

// Regenerar los datos para el PDF
$filtro = '';
if ($vacacionista == '1') {
    $filtro = '';
} else {
    $filtro = " AND EMP.EMPL_NAME < '5000'";
}

$sim = impuestoSimbolo($sbs);
$total = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

// Query original para obtener los datos
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

// Generar HTML para el PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            color: #333;
            font-size: 18px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            font-size: 12px;
            margin: 5px 0 0 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: center;
            font-size: 9px;
        }
        
        th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #10b981 !important;
            color: white !important;
            font-weight: bold;
        }
        
        .separator-col {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        .status-success { background-color: #10b981; }
        .status-warning { background-color: #f59e0b; }
        .status-danger { background-color: #ef4444; }
        
        .negative { color: #ef4444; }
        .positive { color: #10b981; }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE SUPERVISIÓN</h1>
        <p>Supervisor: ' . $tienda . ' | Fecha: ' . date('d-m-Y') . ' | Meta del Día: Q ' . number_format(MTDS($tienda, $fi, date('Y', strtotime($ff)), $sbs)[0], 2) . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tienda</th>
                <th>Nombre Tienda</th>
                <th>Día</th>
                <th>Fecha</th>
                <th>Meta Prs</th>
                <th>Venta Prs</th>
                <th>Dif. Prs</th>
                <th>UPT</th>
                <th>% Prs</th>
                <th>St.</th>
                <th>|</th>
                <th>Meta Día</th>
                <th>Venta Día</th>
                <th>Diferencia</th>
                <th>QPT</th>
                <th>%</th>
                <th>Est.</th>
            </tr>
        </thead>
        <tbody>';

$cnt = 1;
$mt_prs = $vta_prs = $dif_prs = $venta = $meta = 0;

foreach ($resultado as $rdst) {
    $rowStyle = ($rdst[3] == 0) ? 'style="background-color: #fee2e2; color: #991b1b;"' : '';
    
    $html .= '<tr ' . $rowStyle . '>
        <td>' . $cnt++ . '</td>
        <td><strong>' . $rdst[0] . '</strong></td>
        <td>' . $rdst[1] . '</td>
        <td>' . $rdst[10] . '</td>
        <td>' . $rdst[11] . '</td>
        <td>' . $rdst[2] . '</td>
        <td><strong>' . $rdst[3] . '</strong></td>
        <td class="' . ($rdst[4] < 0 ? 'negative' : 'positive') . '">' . $rdst[4] . '</td>
        <td>' . $rdst[12] . '</td>
        <td>' . Porcentaje($rdst[3], $rdst[2]) . '%</td>
        <td>●</td>
        <td class="separator-col">|</td>
        <td>' . iva($iva, $rdst[6], $sbs) . '</td>
        <td>' . iva($iva, $rdst[5], $sbs) . '</td>
        <td class="' . ($rdst[7] < 0 ? 'negative' : 'positive') . '">' . iva($iva, $rdst[7], $sbs) . '</td>
        <td>' . $sim[0] . ' ' . number_format($rdst[13], 2) . '</td>
        <td>' . Porcentaje($rdst[5], $rdst[6]) . '%</td>
        <td>●</td>
    </tr>';

    if ($rdst[3] !== 'VACACIONISTA') {
        $mt_prs += $rdst[2];
        $vta_prs += $rdst[3];
        $dif_prs += $rdst[4];
        $venta += $rdst[5];
        $meta += $rdst[6];
    }
}

// Fila de totales
$html .= '<tr class="total-row">
    <td></td>
    <td colspan="4"><strong>TOTAL</strong></td>
    <td>' . $mt_prs . '</td>
    <td>' . $vta_prs . '</td>
    <td>' . $dif_prs . '</td>
    <td></td>
    <td>' . Porcentaje($vta_prs, $mt_prs) . '%</td>
    <td>●</td>
    <td class="separator-col">|</td>
    <td>' . iva($iva, $meta, $sbs) . '</td>
    <td>' . iva($iva, $venta, $sbs) . '</td>
    <td>' . iva($iva, DifVentaMeta($venta, $meta), $sbs) . '</td>
    <td></td>
    <td>' . Porcentaje($venta, $meta) . '%</td>
    <td>●</td>
</tr>';

$html .= '</tbody>
    </table>
    
    <div class="footer">
        <p>Reporte generado el ' . date('d/m/Y H:i:s') . ' | Sistema de Supervisión</p>
    </div>
</body>
</html>';

// Cargar HTML en DomPDF
$dompdf->loadHtml($html);

// Configurar el papel y orientación
$dompdf->setPaper('A4', 'landscape');

// Renderizar el PDF
$dompdf->render();

// Nombre del archivo
$filename = "Reporte_Supervision_" . $tienda . "_" . date('Y-m-d') . ".pdf";

// Enviar el PDF al navegador
$dompdf->stream($filename, array("Attachment" => false));
?>