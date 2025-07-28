<?php
require_once "../../Funsiones/global.php";
?>

<div id="filtros">
		<form>
			<table class="fila">
				<tr>
					<td>Ingrese tienda: </td>
					<td>
						<input class="form-control" type="number" name="tienda" id="tienda"  placeholder="Ingrese numero de tienda">
					</td>
					<td>Ingrese fecha: </td>
					<td>
						<input class="form-control controles" type="date" id="fecha" name="fecha">
					</td>
					<td>
						<button type="button" class="btn btn-primary controles" onclick="AsigMetaSemana()"> <i class="fas fa-search"></i> Ejecutar</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div id="contenido1" class="container"></div>

  
    </div>
    <div class="form-row">
      <div class="col">
        <label for="tipoDeposito">Tipo de corte</label>
        <select class="form-control" name="tipoDeposito" id="tipoDeposito" required>
          <option selected></option>
          <?php listadoTipoDeposito() ?>
        </select>
      </div>
      <div class="col">
        <label for="bancoDeposito">Banco</label>
        <select class="form-control" name="bancoDeposito" id="bancoDeposito" required>
          <option selected></option>
          <?php listadoBanco() ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="col">
        <label for="noDeposito">No boleta</label>
        <input type="text" class="form-control" name="noDeposito" id="noDeposito" required>
      </div>
      <div class="col">
        <label for="montoDeposito">Monto</label>
        <input type="text" class="form-control" name="montoDeposito" id="montoDeposito" required>
      </div>
    </div>
    <div class="form-row comentario">
      <div class="col">
        <label for="comentario">Comentario</label>
        <textarea class="form-control" name="comentario" id="comentario" cols="30" rows="3"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-ban"></i> Cancelar</button>
      <button type="submit" class="btn btn-primary" id="btnOkModalDeposito"></button>
    </div>
  </form>
  <table id="tblDeposito" class="table table-sm table-hover">
    <thead>
      <th>No</th>
      <th>Fecha</th>
      <th>Tienda</th>
      <th>Tipo</th>
      <th>Boleta</th>
      <th>Monto</th>
      <th>Banco</th>
    </thead>

    <tbody>
    </tbody>

    <tfoot>
      <th>No</th>
      <th>Fecha</th>
      <th>Tienda</th>
      <th>Tipo</th>
      <th>Boleta</th>
      <th>Monto</th>
      <th>Banco</th>
    </tfoot>
  </table>
</div>
<script>
  var url = "../Js/tienda/deposito.js";
  $.getScript(url);
</script>