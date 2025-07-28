<?php
require_once "../../Funsiones/global.php";
?>

<div class="container shadow rounded py-3 px-4">
  <center>
    <button class="btn btn-success btn-lg btnCrearUsuario"> <i class="fas fa-user-plus"></i> Crear usuario</button>
  </center>
  <table id="tblUsuario" class="table table-sm table-hover">
    <thead>
      <th>Id</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>PNombre</th>
      <th>SNombre</th>
      <th>PApellido</th>
      <th>SApellido</th>
      <th>Correo</th>
      <th>Código</th>
      <th>Foto</th>
      <th>Fecha Creacion</th>
      <th>IdDepartamento</th>
      <th>Departamento</th>
      <th>IdPerfil</th>
      <th>Perfil</th>
      <th>IdPais</th>
      <th>Pais</th>
      <th>Status</th>
      <th>Acciones</th>
    </thead>

    <tbody>
    </tbody>

    <tfoot>
      <th>Id</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>PNombre</th>
      <th>SNombre</th>
      <th>PApellido</th>
      <th>SApellido</th>
      <th>Correo</th>
      <th>Código</th>
      <th>Foto</th>
      <th>Fecha Creacion</th>
      <th>IdDepartamento</th>
      <th>Departamento</th>
      <th>IdPerfil</th>
      <th>Perfil</th>
      <th>IdPais</th>
      <th>Pais</th>
      <th>Status</th>
      <th>Acciones</th>
    </tfoot>
  </table>
</div>
<script>
  var url = "../Js/informatica/informatica.js";
  $.getScript(url);
</script>


<!-- ModalUsuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="frmModalUsuario" enctype="multipart/form-data" method="POST">
          <div class="form-row">
            <div class="col">
              <label for="pnombre">Primer nombre</label>
              <input type="text" class="form-control" name="pnombre" id="pnombre" autocomplete="off" required>
            </div>
            <div class="col">
              <label for="snombre">Segundo nombre</label>
              <input type="text" class="form-control" name="snombre" id="snombre" autocomplete="off">
            </div>
          </div>
          <div class="form-row">
            <div class="col">
              <label for="papellido">Primer apellido</label>
              <input type="text" class="form-control" name="papellido" id="papellido" autocomplete="off" required>
            </div>
            <div class="col">
              <label for="sapellido">Segundo apellido</label>
              <input type="text" class="form-control" name="sapellido" id="sapellido" autocomplete="off">
            </div>
          </div>
          <div class="form-row">
            <div class="col">
              <label for="correo">Correo</label>
              <input type="email" class="form-control" name="correo" id="correo" autocomplete="off">
            </div>
          </div>
          <div class="form-row">
            <div class="col">
              <label for="codigo">Código empleado</label>
              <input type="text" class="form-control" name="codigo" id="codigo" autocomplete="off" required>
            </div>
            <div class="col">
              <label for="pais">Pais</label>
              <select name="pais" id="pais" class="form-control" required>
                <option selected></option>
                <?php listadoPais() ?>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="col">
              <label for="perfil">Perfil</label>
              <select name="perfil" id="perfil" class="form-control" required>
                <option selected></option>
                <?php listadoPerfil() ?>
              </select>
            </div>
            <div class="col">
              <label for="departamento">Departamento</label>
              <select name="departamento" id="departamento" class="form-control" required>
                <option selected></option>
                <?php listadodepartamento() ?>
              </select>
            </div>
          </div>
          <div class="form-row contrasena">
            <div class="col">
              <label for="pass">Contraseña</label>
              <input type="password" class="form-control" name="pass" id="pass" autocomplete="off" required>
            </div>
            <div class="col">
              <label for="confirmarPass">Confirmar Contraseña</label>
              <input type="password" class="form-control" name="confirmarPass" id="confirmarPass" autocomplete="off" required>
            </div>
          </div>
          <div class="form-row fotos">
            <div class="col my-3">
              <input type="file" name="foto" id="foto" class="form-control-file" accept="image/*">
            </div>
          </div>
          <div class="form-row fotos">
            <div class="col pb-2">
              <img src="/Image/user/default.svg" id="previewFoto" class="border border-dark rounded mx-auto d-block p-2" width="150" height="150" alt="...">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-ban"></i> Cancelar</button>
            <button type="submit" class="btn btn-primary" id="btnOkModalUsuario"></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>