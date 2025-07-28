<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/tienda/queryRpro.php";



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


<div class="container-fluid shadow rounded py-3 px-4">
  <?php
  foreach ($tiendas as $tienda) {

    $total = array(
      $existencia = 0,
      $valor_existencia = 0,
      $venta = 0,
      $valor_venta = 0,
      $compras = 0,
      $valor_compras = 0
    );

    $query = "
    SELECT *
    FROM (
        SELECT 
            z.store_no TIENDA,
            z.employee1_login_name AS CODIGO, 
            z.employee1_full_name AS VENDEDOR, e.puesto,
            vd.vend_code,
           nvl( SUM(CASE 
                    WHEN z.receipt_type = 0 THEN b.qty 
                    WHEN z.receipt_type = 1 THEN b.qty * -1 
                END),0) AS cantidad
        FROM rps.document z
        JOIN rps.document_item b ON z.sid = b.doc_sid
        JOIN rps.invn_sbs_item i ON i.sid = b.invn_sbs_item_sid
        
        JOIN rps.vendor vd ON b.vend_code = vd.vend_code AND i.sbs_sid = vd.sbs_sid
        JOIN rps.dcs d ON i.sbs_sid = d.sbs_sid AND i.dcs_sid = d.sid
         INNER join ROY_VENDEDORES_FRIED E on (E.CODIGO_VENDEDOR = z.employee1_login_name)
        WHERE 
            z.sbs_no = 1
            AND z.store_no IN ($tienda)
            AND z.created_datetime BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
                                      AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
        GROUP BY z.store_no, vd.vend_code, z.employee1_login_name, z.employee1_full_name,e.puesto
    )
    PIVOT (
        SUM(cantidad)
        FOR vend_code IN (
            '001' AS \"INTERCALSA\",
            '002' AS \"RESPALDO\",
            '014' AS \"KARINS\",
            '015' AS \"DADYS\",
            '066' AS \"SKECHERS\",
            '132' AS \"PAW PATROL\",
            '127' AS \"TATA\",
            '131' AS \"PENGUIN\"
        )
    )
    ORDER BY DECODE(PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4) ASC";

    $resultado = consultaOracle(5, $query);
  ?>
    <tr><td colspan="8"><h3 align="center"><b><p>Ventas - Pares - Proveedor</p></b></h3></td></tr>
    <tr><td colspan="8"><h3 align="center"><b><p><?php echo 'DEL: '.date('d-m-Y', strtotime($fi)) .' AL: '.date('d-m-Y', strtotime($ff)); ?></p></b></h3></td></tr>                 
    <tr><td colspan='27'><h3 align="center"><b><p>TIENDA: <?php echo utf8_encode($tienda); ?></p></b></h3></td></tr>

<?php if (!empty($resultado)) {
  // Extrae los nombres de columna (asegúrate que sean claves asociativas, no índices numéricos)
  $columnas = array_keys($resultado[0]);
?>
  <?php
// Columnas a excluir del total
$excluir_totales = ['TIENDA', 'CODIGO', 'VENDEDOR', 'PUESTO'];

// Inicializa totales en cero solo para columnas numéricas
$total_general = [];
foreach ($columnas as $col) {
  if (!in_array($col, $excluir_totales)) {
    $total_general[$col] = 0;
  } else {
    $total_general[$col] = ''; // no sumar
  }
}
?>

<table style="font-size:14px;" class="table table-hover table-sm tbevct">
  <thead class="bg-primary">
    <tr>
      <?php foreach ($columnas as $col): ?>
        <th><?php echo htmlspecialchars($col); ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody class="align-middle font-size" style="width:100%">
    <?php foreach ($resultado as $fila): ?>
      <tr>
        <?php foreach ($columnas as $col): ?>
          <td>
  <?php 
    $valor = $fila[$col];
    echo htmlspecialchars($valor === null || $valor === '' ? 0 : $valor); 
  ?>
</td>

          <?php
          // Sumar si no es columna excluida y el valor es numérico
          if (!in_array($col, $excluir_totales) && is_numeric($fila[$col])) {
            $total_general[$col] += $fila[$col];
          }
          ?>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>

    <!-- Fila de Totales -->
    <tr class="table-secondary font-weight-bold">
      <?php foreach ($columnas as $col): ?>
        <td>
          <?php 
          if (!in_array($col, $excluir_totales)) {
            echo number_format($total_general[$col]);
          } elseif ($col == 'VENDEDOR') {
            echo 'TOTAL';
          } else {
            echo '';
          }
          ?>
        </td>
      <?php endforeach; ?>
    </tr>
  </tbody>
</table>


<?php } ?>
<?php
  } // ← Esta llave cierra el foreach de las tiendas
?>

</div>

<script>
  $('.tbevct').DataTable({
    "searching": false,
    "paging": false,
    "ordering": false,
    "info": false,
    "responsive": true,
    "autoWidth": false
  });

  $('.tooltip').tooltip();

  var url = "../Js/tienda/tienda.js";
  $.getScript(url);
</script>
