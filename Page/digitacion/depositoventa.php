<?php
require_once "../../Funsiones/global.php";
?>

<div class="container shadow rounded py-3 px-4">
  <center>
    <input type="text" id="rangoFecha" value="<?php echo  $_POST['fechas'] ?>" readonly hidden>
    <h5><?php echo "Rango que consultas: " . str_replace('-', '/', $_POST['fechas']) ?></h5>
  </center>
  <table id="tblDepositoVenta" class="table table-sm table-hover">
    <thead>
      <th>Subsidiaria</th>
      <th>Fecha</th>
      <th>Tienda</th>
      <th>Venta</th>
      <th>Depósito</th>
      <th>Diferencia</th>
      <th>Estado</th>
    </thead>

    <tbody>
    </tbody>

    <tfoot>
      <th>Subsidiaria</th>
      <th>Fecha</th>
      <th>Tienda</th>
      <th>Venta</th>
      <th>Depósito</th>
      <th>Diferencia</th>
      <th>Estado</th>
    </tfoot>
  </table>
</div>
<script>
  var url = "../Js/digitacion/depositoventa.js";
  $.getScript(url);
</script>