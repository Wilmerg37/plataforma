<?php
    require_once "../../Funsiones/global.php";
?>

<div class="container shadow rounded py-3 px-4">
      <center>
            <input type="text" id="rangoFecha" value="<?php echo  $_POST['fechas'] ?>" readonly hidden>
            <h5><?php echo "Rango que consultas: ".str_replace('-','/',$_POST['fechas'])?></h5>
            <button class="btn btn-success btn-lg btnCrearDeposito"> <i class="fas fa-plus"></i> Crear depósito</button>
      </center>
    <table id="tblDeposito" class="table table-sm table-hover">
		<thead>
      <th>Id</th>
      <th>Fecha</th>
      <th>Tienda</th>
      <th>Tipo</th>
      <th>Boleta</th>
      <th>Monto</th>
      <th>Banco</th>
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
      <th>Acciones</th>
		</tfoot>
	</table>
</div>
<script>
      var  url ="../Js/digitacion/digitacion.js";
      $.getScript(url);
</script>


<!-- ModalDeposito -->
<div class="modal fade" id="modalDeposito" tabindex="-1" aria-labelledby="modalDepositoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <form id="frmModalDeposito">
                  <div class="form-row">
                        <div class="col">
                              <label for="fechaDeposito">Fecha deposito</label>
                              <input type="date" class="form-control" name="fechaDeposito" id="fechaDeposito" required>
                        </div>
                        <div class="col">
                              <label for="tiendaDeposito">Tienda</label>
                              <select class="form-control" name="tiendaDeposito" id="tiendaDeposito" required>
                                    <option selected></option>
                                    <?php listadoTienda() ?>
                              </select>
                        </div>
                  </div>
                  <div class="form-row">
                        <div class="col">
                              <label for="tipoDeposito">Tipo de deposito</label>
                              <select class="form-control" name="tipoDeposito" id="tipoDeposito" required>
                                    <option selected></option>
                                    <?php  listadoTipoDeposito() ?>
                              </select>
                        </div>
                        <div class="col">
                              <label for="noDeposito">No boleta</label>
                              <input type="text" class="form-control" name="noDeposito" id="noDeposito" required>
                        </div>

                  </div>
                  <div class="form-row">
                        <div class="col">
                              <label for="montoDeposito">Monto</label>
                              <input type="text" class="form-control" name="montoDeposito" id="montoDeposito" required>
                        </div>
                        <div class="col">
                              <label for="bancoDeposito">Banco</label>
                              <select class="form-control" name="bancoDeposito" id="bancoDeposito" required>
                                    <option selected></option>
                                    <?php listadoBanco() ?>
                              </select>
                        </div>
                  </div>
                  <div class="form-row comentario">
                        <div class="col">
                              <label for="comentario">Comentario</label>
                              <textarea class="form-control" name="comentario" id="comentario" cols="30" rows="3"></textarea>
                        </div>
                  </div>
                  <div class="form-row" id="explicacion">
                        <div class="col">
                              <label for="explicacionDeposito">Explicacion</label>
                              <textarea class="form-control" name="explicacionDeposito" id="explicacionDeposito"  pattern="[A-Za-z]{10}" title="Minimo 10 letras" minlength = "10" required></textarea>
                        </div>
                  </div>
                  <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-ban"></i> Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="btnOkModalDeposito"></button>
                  </div>
            </form>
      </div>
    </div>
  </div>
</div>



