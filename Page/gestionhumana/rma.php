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
  $filtro = " AND EMP.EMPL_NAME < '50000'";
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
      SELECT TIENDA,  CODIGO, NOMBRE,DEPARTAMENTO PUESTO, DIA,FECHA, HE_ASIGNADA,  NVL(ENTRADA, 'No Marco') AS ENTRADA, 
       NVL(SALIDA, 'No Marco') AS SALIDA
FROM (
    SELECT  RG.TIENDA,H.DEPARTAMENTO, RG.CODIGO_EMPLEADO CODIGO,
    E.FULL_NAME NOMBRE,
     
        TO_CHAR(RG.FECHA, 'DD/MM/YYYY') AS FECHA,
        TO_CHAR(RG.FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') DIA,
        h.hora_entrada HE_ASIGNADA,
       -- H.HORA_SALIDA HS_ASIGNDA,
        TO_CHAR(RG.FECHA, 'HH:MI AM') AS HORA,
        RG.ACCION
    FROM REPORTUSER.ROY_HLL_REGISTRO_HUELLA RG
    INNER JOIN RPS.EMPLOYEE E ON TO_CHAR(RG.CODIGO_EMPLEADO) = E.EMPL_NAME
    INNER JOIN ROY_HORARIOS_ADMON H ON RG.CODIGO_EMPLEADO = H.CODIGO_EMPLEADO --AND TO_CHAR(RG.FECHA, 'DAY', 'NLS_DATE_LANGUAGE=SPANISH') = H.DIA
    WHERE RG.FECHA BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS')  AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
      AND   RG.TIENDA IN ($tienda)
      AND   RG.SBS = $sbs
    --AND ((ACCION = 'Entrada' AND TO_CHAR(FECHA, 'AM') = 'AM') OR (ACCION = 'Salida' AND TO_CHAR(FECHA, 'AM') = 'PM') )
) PIVOT (
    MAX(HORA)
    FOR ACCION IN ('Entrada' AS ENTRADA, 'Salida' AS SALIDA)
)
ORDER BY DEPARTAMENTO, CODIGO,FECHA";
      $resultado = consultaOracle(3, $query);      
      $cnt=1;
   
  ?>
      <h3 class="text-center font-weight-bold text-primary">Marcaje Administracion
         
     

      <table style="font-size:14px;" class="table table-hover table-sm tbrdst">
    <thead class="bg-primary">
        <td>No</td>
        <td>Tienda</td>
        <td>Codigo</td>          
        <td>Nombre</td>
        <td>Puesto</td>
        <td>Dia</td>
        <td>Fecha</td>
        <td>Hora Asignada</td>
        <td>Hora de Registro</td>
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
                <td><?php echo $rdst[6] ?></td>
                <td style="background-color: <?php echo ($rdst[7] == 'No Marco') ? '#ff4d4d' : '#eaeded'; ?>; font-weight:bold;"><b></b><?php echo $rdst[7] ?><b></b></td>
                <td style="background-color: <?php echo ($rdst[8] == 'No Marco') ? '#ff4d4d' : '#eaeded'; ?>; font-weight:bold;"><?php echo $rdst[8] ?></td>
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

  var url = "../Js/gestionhumana/gestionhumana.js";
  $.getScript(url);

</script>