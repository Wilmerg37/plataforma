<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

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

// Inicializamos el array para acumular las ventas y metas por tienda y semana
$promediosPorTienda = [];

?>
<div class="container-fluid shadow rounded py-3 px-4">
  <?php
  // Recorremos cada tienda seleccionada
  foreach ($tiendas as $tie) {

    // Consulta filtrada por cada tienda
    $query = "
     SELECT E.TIENDA, E.CODIGO_VENDEDOR, E.NOMBRE, E.PUESTO, NVL(E.FECHA_INGRESO, TO_DATE(sysdate, 'DD-MM-YYYY')) AS CONTRATACION, A.SEMANA, 
           CASE WHEN A.TIPO = 'VACACIONISTA' THEN 0 ELSE ROUND(A.META, 2) END META,
            ROUND(NVL(sum(CASE 
                            WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
                            WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
                          END), 0), 2) VENTA,
                                
                        ROUND(
  NVL(
    sum(CASE 
          WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12
          WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc, 0) / 100)) * (t2.qty)) / 1.12 * -1 
        END), 0
  ) 
  / DECODE(A.META, 0, 1, A.META) * 100, 2
) PORCENTAJE
 
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
          <td><?php echo substr($sem, -4); ?></td>
          <?php } ?> 
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
                'semanas' => [] // Array de semanas y estatus
              ];
            }

            // Asociamos la semana y el estatus con el vendedor
            $semana = (int)$rtt[5]; // Semana actual (convertir a entero)
            $estatus = $rtt[8]; // Porcentaje de venta

            // Guardamos el estatus para esta semana y vendedor
            $datosVendedores[$clave]['semanas'][$semana] = $estatus;

            // Acumulamos las ventas y metas por tienda y semana para calcular el promedio
            if (!isset($promediosPorTienda[$tie])) {
                $promediosPorTienda[$tie] = [];
            }

            if (!isset($promediosPorTienda[$tie][$semana])) {
                $promediosPorTienda[$tie][$semana] = ['ventas' => 0, 'metas' => 0];
            }

            // Sumamos las ventas y las metas
            $ventas = $rtt[7];  // Ventas
            $meta = $rtt[6];    // Meta

            $promediosPorTienda[$tie][$semana]['ventas'] += $ventas;
            $promediosPorTienda[$tie][$semana]['metas'] += $meta;
          }

          // Ahora recorremos el array de vendedores para mostrar la tabla
          foreach ($datosVendedores as $vendedor) {
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

            // Verificamos si hay datos para esta semana
            if (isset($vendedor['semanas'][$semana])) {
              $estatus = $vendedor['semanas'][$semana];
              ?>
              <td>
                <span class="<?php echo status($estatus) ?>" style="<?php echo color($estatus) ?>"></span>
                <?php echo $estatus . " %"; ?>
              </td>
              <?php
            } else {
              echo '<td><span class="fas fa-circle" style="color:#ffffff; font-size: 2em;"></span></td>';
            }
          }
          ?>
        </tr>
        <?php
        }
        ?>
<tr class="align-middle font-weight-bold" style="background-color: #48c9b0; color:rgb(0, 0, 0);">
        <td colspan="4"><b>Promedio Tienda <?php echo $tie; ?></b></td>
        <?php
        // Mostrar los promedios por semana
        foreach ($semanas as $sem) {
            $semana = (int)$sem;
            if (isset($promediosPorTienda[$tie][$semana])) {
                $ventas = $promediosPorTienda[$tie][$semana]['ventas'];
                $metas = $promediosPorTienda[$tie][$semana]['metas'];
                
                // Calculamos el promedio como ventas / metas * 100
                if ($metas > 0) {
                    $promedio = round(($ventas / $metas) * 100, 0);
                } else {
                    $promedio = 0;
                }
            } else {
                $promedio = 0;
            }

            // Aplicamos el estatus y color al promedio
            $statusClass = status($promedio);
            $colorStyle = color($promedio);
        ?>
        <td>
            <span class="<?php echo $statusClass; ?>" style="<?php echo $colorStyle; ?>"></span>
            <?php echo $promedio . " %"; ?>
        </td>
        <?php
        }
        ?>
      </tr>


      </tbody>
    </table>

   

    <hr>
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
