<?php
require_once "../../Funsiones/global.php";
?>

<div class="container shadow rounded py-3 px-4">
  <center>
    <h1>Datos de corte</h1>
  </center>
  <form id="frmDeposito">
    <div class="form-row">
      <div class="col">
        <label for="tiendaDeposito">Corte</label>
        <select class="form-control" name="corte" id="corte" required>
          <option selected></option>
          <option value="0">Corte Sistema</option>
          <option value="1">Corte Visa</option>
          <option value="2">Corte Credomatic</option>
          <option value="3">Corte Vana</option>
        </select>
      </div>
      <div class="col">
        <label for="fechaDeposito">Fecha corte</label>
        <input type="date" class="form-control" name="fechaDeposito" id="fechaDeposito" value="<?php echo date('Y-m-d',strtotime(date('Y-m-d')."- 1 days"))?>" required>
      </div>

<div class="col">
  <label for="tiendaDeposito">Tienda</label>
  <select class="form-control" name="tiendaDeposito" id="tiendaDeposito" disabled>
    <option selected><?php echo $_SESSION['user'][6]; ?></option>
  </select>
  <!-- Agrega un campo hidden para enviar el valor al backend porque el campo disabled no se envía -->
  <input type="hidden" name="tiendaDeposito" value="<?php echo $_SESSION['user'][6]; ?>">
</div>


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
      <button type="submit" class="btn btn-primary" id="btnOkModalDeposito">Guardar</button>
    </div>
  </form>
  <table id="tblDeposito" class="table table-sm table-hover">
    <thead>
      <th>No</th>
      <th>Fecha Corte</th>
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
      <th>Fecha Corte</th>
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