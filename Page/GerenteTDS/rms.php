<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/GerenteTDS/queryRpro.php";


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

      $query = "
      SELECT CODIGO_EMPLEADO, NOMBRE, TIENDA, FECHA, DIA, NVL(ENTRADA, 'No Marco') AS ENTRADA, 
       NVL(SALIDA, 'No Marco') AS SALIDA
FROM (
    SELECT RG.CODIGO_EMPLEADO,
    E.NOMBRE,
      RG.TIENDA,
        TO_CHAR(RG.FECHA, 'DD/MM/YYYY') AS FECHA,
        TO_CHAR(RG.FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') DIA,
        TO_CHAR(RG.FECHA, 'HH:MI AM') AS HORA,
        RG.ACCION
    FROM REPORTUSER.ROY_HLL_REGISTRO_HUELLA RG
    INNER JOIN ROY_VENDEDORES_FRIED E ON RG.CODIGO_EMPLEADO = E.CODIGO_VENDEDOR
    WHERE FECHA BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
      AND   RG.CODIGO_EMPLEADO IN ($tienda)
      and RG.SBS = $sbs
   -- AND ((ACCION = 'Entrada' AND TO_CHAR(FECHA, 'AM') = 'AM') OR (ACCION = 'Salida' AND TO_CHAR(FECHA, 'AM') = 'PM')  )
) PIVOT (
    MAX(HORA)
    FOR ACCION IN ('Entrada' AS ENTRADA, 'Salida' AS SALIDA)
)
ORDER BY CODIGO_EMPLEADO, FECHA";
      $resultado = consultaOracle(3, $query);      
      $cnt=1;
   
  ?>
      <h3 class="text-center font-weight-bold text-primary">supervisor: <?php echo $tienda?>
         
     

      <table style="font-size:14px;" class="table table-hover table-sm tbrdst">
    <thead class="bg-primary">
        <td>No</td>
        <td>codigo</td>
        <td>Nombre</td>          
        <td>Tienda</td>
        <td>Fecha</td>
        <td>Dia</td>
        <td>Hora Entrada</td>
        <td>Hora Salida</td>
    </thead>
    
    <tbody class="align-middle font-size" style="width:100%; color: black; font-weight: normal;">
        <?php
        foreach ($resultado as $rdst) {
        ?>
            <tr class="align-middle font-size">
                <td><?php echo $cnt++ ?></td>            
                <td><?php echo $rdst[0] ?></td> <!-- Sin la etiqueta <b> -->
                <td><?php echo $rdst[1] ?></td>
                <td><?php echo $rdst[2] ?></td>
                <td><?php echo $rdst[3] ?></td>
                <td><?php echo $rdst[4] ?></td>
                <td><?php echo $rdst[5] ?></td>
                <td><b></b><?php echo $rdst[6] ?><b></b></td>
                
            </tr>
        <?php
            if ($rdst[3] === 'VACACIONISTA') {
                $rdst[6] = 0;
            }
        }
        ?>
        <tr>
        </tr>
    </tbody>
    
    <tfoot>
    </tfoot>
</table>

         
      <hr>
  <?php
   
  }
  ?>
</div>

<script>
  $('.tbrdst').DataTable({
    "searching": false,
    "paging": false,
    "ordering": false,
    "info": false,
    "responsive": true,
    "autoWidth": false
  });

  $('.tooltip').tooltip();

  var url = "../Js/GerenteTDS/GerenteTDS.js";
  $.getScript(url);

</script>