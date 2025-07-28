<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$tienda = (isset($_POST['tienda'])) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];

$iva = (isset($_POST['iva'])) ? $_POST['iva'] : '';
$vacacionista = (isset($_POST['vacacionista'])) ? $_POST['vacacionista'] : '';
$filtro = '';

if ($vacacionista == '1') {
    $filtro = '';
} else {
    $filtro = " AND A.TIPO <> 'VACACIONISTA'";
}

$tiendas = explode(',', $tienda);
sort($tiendas);

if (isset($_POST['download_excel'])) {
    // Creamos el objeto Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Definir las cabeceras
    $sheet->setCellValue('A1', 'Código');
    $sheet->setCellValue('B1', 'Asesora');
    $sheet->setCellValue('C1', 'Puesto');
    $sheet->setCellValue('D1', 'Pago Acumulado');

    // Contador de fila para la tabla
    $row = 2;

    // Recorremos las tiendas seleccionadas
    foreach ($tiendas as $tie) {
        // Consulta filtrada por cada tienda
        $query = "
            -- Tu consulta SQL aquí --
        ";

        $consulta = consultaOracle(3, $query);

        // Recorremos los datos de los vendedores
        foreach ($consulta as $rtt) {
            $codigo = $rtt[1];
            $nombre = ucwords(strtolower($rtt[2]));
            $puesto = substr($rtt[3], 0, 3);
            $bonoAcumulado = 0;

            // Aquí calculamos el bono acumulado
            // Para cada semana y vendedor, calculamos el pago y lo acumulamos
            foreach ($semanas as $sem) {
                $semana = (int)$sem;
                if (isset($rtt['semanas'][$semana])) {
                    $estatus = $rtt['semanas'][$semana];
                    // Aquí calculas el pago semanal para cada semana
                    $pagoSemana = calcularPagoPorSemana($estatus, $puesto, $rtt['semanasConsecutivas']);
                    $bonoAcumulado += $pagoSemana;
                }
            }

            // Insertamos los datos en las celdas
            $sheet->setCellValue("A$row", $codigo);
            $sheet->setCellValue("B$row", $nombre);
            $sheet->setCellValue("C$row", $puesto);
            $sheet->setCellValue("D$row", "Q" . number_format($bonoAcumulado, 2));

            // Aumentamos el contador de filas
            $row++;
        }
    }

    // Especificamos que el archivo sea descargado en formato Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'Reporte_Tienda_' . date('Y-m-d') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    
    // Guardamos el archivo directamente en el flujo de salida para la descarga
    $writer->save('php://output');
    exit;
}
?>

<div class="container-fluid shadow rounded py-3 px-4">
    

    <?php
    // Recorremos cada tienda seleccionada
    foreach ($tiendas as $tie) {
        // Consulta filtrada por cada tienda
        $query = "
         SELECT E.TIENDA, E.CODIGO_VENDEDOR, E.NOMBRE, E.PUESTO, E.FECHA_INGRESO CONTRATACION, A.SEMANA, 
               CASE WHEN A.TIPO = 'VACACIONISTA' THEN 0 ELSE ROUND(A.META, 2) END META,
                ROUND(NVL(sum(CASE 
                                WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
                                WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
                              END), 0), 2)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0))- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0))/1.12  VENTA,
                ROUND(NVL(sum(CASE 
                                WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
                                WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
                              END)- SUM(NVL( t2.lty_piece_of_tbr_disc_amt,0)), 0) / A.META * 100, 2) PORCENTAJE
          FROM rps.document t1
          INNER JOIN rps.document_item t2 ON (t1.sid = t2.doc_sid)
          INNER JOIN ROY_META_SEM_X_VENDEDOR A ON TO_CHAR(trunc(T1.CREATED_DATETIME, 'd'), 'IW') + 1 = A.SEMANA 
                                                  AND TO_CHAR(T1.CREATED_DATETIME, 'IYYY') = A.ANIO 
                                                  AND T1.STORE_NO = A.TIENDA 
                                                  AND t1.employee1_login_name = A.CODIGO_EMPLEADO 
                                                  AND T1.SBS_NO = A.SBS
          INNER JOIN ROY_VENDEDORES_FRIED E ON (E.CODIGO_VENDEDOR = t1.employee1_login_name)
          WHERE 1 = 1
                AND t1.status = 4
                AND t1.receipt_type <> 2
                AND T1.sbs_no = $sbs
                AND t1.STORE_NO = $tie  -- Filtro para cada tienda
                $filtro
                AND t1.CREATED_DATETIME BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                             AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
          GROUP BY E.TIENDA, E.CODIGO_VENDEDOR, E.NOMBRE, E.PUESTO, E.FECHA_INGRESO, A.SEMANA, A.META, A.TIPO
          ORDER BY DECODE(E.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4), A.SEMANA,E.FECHA_INGRESO";

        $consulta = consultaOracle(3, $query);
    ?>
        <h3 class="text-center font-weight-bold text-primary">Tienda no: <?php echo $tie; ?><br><small class="h4 text-primary text-center"><?php echo "( " . date('d/m/Y', strtotime($fi)) . " --al-- " . date('d/m/Y', strtotime($ff)) . " )" ?></small></br></h3>
           
        <table style="font-size:14px;" class="table table-hover table-sm tbrtt">
            <thead class="bg-primary">
            <tr>
                <td>Antiguedad</td>
                <td>Código</td>
                <td>Asesora</td>
                <td>Puesto</td>
                <?php 
                    // Aquí mostramos las semanas solo una vez
                    $semanas = rangoWe($fi, $ff); // Obtener las semanas
                    foreach ($semanas as $sem) { 
                ?>
                <td>Semana</td> <!-- Aquí se coloca el texto "Semana" -->
                <?php } ?> 
                <td>Pago Acumulado</td>
            </tr>
            <tr>
                <td colspan="4"></td> <!-- Espacio para alinear con la fila de datos -->
                <?php 
                    foreach ($semanas as $sem) { 
                ?>
                <td><?php echo substr($sem, -4); ?></td> <!-- Aquí se coloca el valor de la semana -->
                <?php } ?> 
                <td></td> <!-- Aquí podrías colocar cualquier valor relacionado con el pago acumulado -->
            </tr>

            </thead>

            <?php
                // Inicializamos el array para agrupar los datos de los vendedores
                $datosVendedores = [];
            ?>

            <tbody class="align-middle font-size" style="width:100%">
                <?php
                    // Recorremos el resultado de la consulta para agrupar los datos de cada vendedor
                    foreach ($consulta as $rtt) {
                        $clave = $rtt[1]; // Código del vendedor

                        // Si el vendedor aún no está en el array de datos, lo agregamos
                        if (!isset($datosVendedores[$clave])) {
                            $datosVendedores[$clave] = [
                                'antiguedad' => Antiguedad($rtt[4])[0] . " - días", // Antigüedad
                                'codigo' => $rtt[1], // Código del vendedor
                                'nombre' => ucwords(strtolower($rtt[2])), // Nombre
                                'puesto' => substr($rtt[3], 0, 3), // Puesto
                                'semanas' => [], // Array de semanas y estatus
                                'ultimoPago' => 0, // Almacenamos el último pago para reiniciar si es necesario
                                'semanasConsecutivas' => 0 // Contador de semanas consecutivas que cumplen el criterio
                            ];
                        }

                        // Asociamos la semana y el estatus con el vendedor
                        $semana = (int)$rtt[5]; // Semana actual (convertir a entero)
                        $estatus = $rtt[8]; // Porcentaje de venta
                        $meta = $rtt[6];            // Meta
                        $venta = $rtt[7];           // Venta


                        // Guardamos el estatus para esta semana y vendedor
                        $datosVendedores[$clave]['semanas'][$semana] = [
    'estatus' => $estatus,
    'meta' => $meta,
    'venta' => $venta
];

                    }

                    //.........nuevo 
$promediosPorSemana = [];
$semanasConsecutivasTienda = 0; // Para rastrear semanas consecutivas de buen desempeño

foreach ($semanas as $yw) {
    $semana = (int)$yw;
    $sumPorcentaje = 0;
    $count = 0;

  foreach ($datosVendedores as $vendedor) {
    if (isset($vendedor['semanas'][$semana])) {
        $sumPorcentaje += $vendedor['semanas'][$semana]['estatus'];
        $count++;
    }
}


    $promedio = ($count > 0) ? round($sumPorcentaje / $count, 2) : 0;
    $promediosPorSemana[$semana] = $promedio;
}


$totalBonoTienda = 0;
                    // Ahora recorremos el array de vendedores para mostrar la tabla
                    foreach ($datosVendedores as $vendedor) {
                        $bonoAcumulado = 0;  // Inicializamos el bono acumulado en cero
                ?>
                <tr>
                    <td><?php echo $vendedor['antiguedad']; ?></td>
                    <td><b><?php echo $vendedor['codigo']; ?></b></td>
                    <td><?php echo $vendedor['nombre']; ?></td>
                    <td><?php echo $vendedor['puesto']; ?></td>

                    <?php
                    // Recorremos las semanas disponibles en el rango
foreach ($semanas as $yw) {
    $semana = (int)$yw;
    $pagoSemana = 0;
    $estatus = 0;
    $meta = 0;
    $venta = 0;

    // Verificamos si hay datos para esta semana
    if (isset($vendedor['semanas'][$semana])) {
        $datosSemana = $vendedor['semanas'][$semana];

        $estatus = $datosSemana['estatus'];
        $meta = $datosSemana['meta'];
        $venta = $datosSemana['venta'];

        // Calcular el pago según el puesto
        if ($vendedor['puesto'] == 'JEF') {
            $pagoSemana = calcularPagoPorSemana($estatus, $vendedor['puesto'], $vendedor['semanasConsecutivas']);

            $promedioSemana = $promediosPorSemana[$semana] ?? 0;
            $bonoTienda = calcularBonoPorTienda($promedioSemana, $semanasConsecutivasTienda, $estatus);
            $pagoSemana += $bonoTienda;
        }

        if ($vendedor['puesto'] == 'SUB' || $vendedor['puesto'] == 'ASE') {
            $pagoSemana = calcularPagoPorSemanaVend($estatus, $vendedor['puesto'], $vendedor['semanasConsecutivas']);
        }

        $bonoAcumulado += $pagoSemana;
    }
    ?>

    <td>
        <span class="<?php echo status($estatus); ?>" style="<?php echo color($estatus); ?>"></span>
        <span style="font-weight: bold; color: black; font-size: 1.2em;"><?php echo $estatus . " %"; ?></span><br>
        <div>Meta: Q<?php echo number_format($meta, 2); ?></div>
        <div>Venta: Q<?php echo number_format($venta, 2); ?></div>
       <div><span style="font-weight: bold; color: blue;">Pago: <?php echo ($pagoSemana > 0) ? "Q" . number_format($pagoSemana, 2) : "Q0.00"; ?></span></div>

    </td>
<?php } ?>


                    <!-- Mostrar el bono acumulado -->
                    <td><strong>Q<?php echo number_format($bonoAcumulado, 2); ?></strong></td>
                </tr>
                <?php
                  $totalBonoTienda += $bonoAcumulado;
                    }
                ?>
                
                                        <!-- Fila de resumen por tienda -->
                        <tr style="background:#e6f2ff; font-weight:bold;">
                            <td colspan="4" class="text-center">Total por Tienda:</td>
                            <?php foreach ($semanas as $yw): 
                                $semana = (int)$yw;
                                $prom = $promediosPorSemana[$semana] ?? 0;
                            ?>
                            <td>
                                <span class="<?php echo status($prom); ?>" style="<?php echo color($prom); ?>"></span>
                                <span style="font-weight: bold; color: black; font-size: 1.2em;"><?php echo number_format($prom, 2) . " %"; ?></span><br>
                            </td>
                            <?php endforeach; ?>
                            <td>Q<?php echo number_format($totalBonoTienda, 2); ?></td>
                        </tr>
            </tbody>
      
             

        </table>
        
    <?php } ?>
</div>

<script>
  $('.tbrtt').DataTable({
    "searching": false,
    "paging": false,
    "ordering": false,
    "info": false,
    "responsive": true,
    "autoWidth": false
  });
</script>
