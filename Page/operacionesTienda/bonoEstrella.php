<?php
    require_once "../../Funsiones/global.php";
?>

<div class="container shadow rounded py-3 px-4">
    <table id="tblBonoEstrella" class="table table-sm table-hover">
		<thead>
                  <th>Id</th>
                  <th>Fecha</th>
                  <th>Tienda</th>
                  <th>Tipo</th>
                  <th>Boleta</th>
                  <th>Monto</th>
                  <th>Banco</th>
                  <th>Observacion</th>
                  <th>Acciones</th>
		</thead>

		<tbody>
		</tbody>

		<tfoot>
                  <th>Id</th>
                  <th>Fecha</th>
                  <th>Tienda</th>
                  <th>Tipo</th>
                  <th>Boleta</th>
                  <th>Monto</th>
                  <th>Banco</th>
                  <th>Observacion</th>
                  <th>Acciones</th>
		</tfoot>
	</table>
</div>
<script>
      var  url ="../Js/operacionesTienda/operacionesTienda.js";
      $.getScript(url);
</script>





