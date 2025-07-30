<?php
require_once "../../Funsiones/global.php";
?>

<style>
  /* Contenedor con fondo suave */
  .container {
    background: #f9faff;
    border: 1px solid #dde4f0;
  }

  /* Título centrado y con color */
  h1 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 1rem;
  }

  /* Etiquetas en azul oscuro */
  label {
    color: #34495e;
    font-weight: 600;
  }

  /* Inputs con borde azul suave y sombra ligera */
  .form-control {
    border: 1.5px solid #2980b9;
    box-shadow: 0 1px 3px rgba(41, 128, 185, 0.2);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }

  .form-control:focus {
    border-color: #1abc9c;
    box-shadow: 0 0 8px rgba(26, 188, 156, 0.6);
  }

  /* Estilo para inputs inválidos */
  .is-invalid {
    border-color: #e74c3c !important;
    box-shadow: 0 0 5px rgba(231, 76, 60, 0.7);
  }

  /* Mensajes de error */
  .text-danger {
    font-size: 0.85rem;
    margin-top: 0.25rem;
  }

  /* Botones personalizados */
  .btn-primary {
    background-color: #1abc9c;
    border-color: #16a085;
    font-weight: 600;
  }
  .btn-primary:hover {
    background-color: #16a085;
    border-color: #138d75;
  }

  .btn-danger {
    background-color: #e74c3c;
    border-color: #c0392b;
    font-weight: 600;
  }
  .btn-danger:hover {
    background-color: #c0392b;
    border-color: #992d22;
  }

  /* Tabla con bordes y fondo alternado */
  table.table {
    border-collapse: separate;
    border-spacing: 0 6px;
  }
  table.table thead tr th {
    background-color: #2980b9;
    color: white;
    border-radius: 5px 5px 0 0;
    padding: 10px;
    font-weight: 700;
  }
  table.table tbody tr {
    background-color: #f0f8ff;
    border-radius: 5px;
  }
  table.table tbody tr:hover {
    background-color: #d0e8f2;
  }
  table.table tbody tr td, table.table tfoot tr th {
    padding: 8px 12px;
    color: #34495e;
  }
  table.table tfoot tr th {
    background-color: #ecf0f1;
    font-weight: 600;
    border-radius: 0 0 5px 5px;
  }

  /* Margen en formulario y elementos */
  .form-row {
    margin-bottom: 1rem;
  }

  /* Textarea con mismo estilo que inputs */
  textarea.form-control {
    border: 1.5px solid #2980b9;
    box-shadow: 0 1px 3px rgba(41, 128, 185, 0.2);
    resize: vertical;
  }
</style>

<div class="container shadow rounded py-3 px-4">
  <center>
    <h1>Formulario Backorder</h1>
  </center>

  <form id="frmBackorder">
    <div class="form-row">
      <!-- Tienda -->
      <div class="col">
        <label for="tiendaBackorder">Tienda</label>
        <select class="form-control" name="tiendaBackorder" id="tiendaBackorder" disabled>
          <option selected><?php echo $_SESSION['user'][6]; ?></option>
        </select>
        <!-- Campo oculto para enviar valor de tienda -->
        <input type="hidden" name="tiendaBackorder" value="<?php echo $_SESSION['user'][6]; ?>">
      </div>

      <!-- Fecha -->
      <div class="col">
        <label for="fechaBackorder">Fecha</label>
        <input type="date" class="form-control" name="fechaBackorder" id="fechaBackorder" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
    </div>

    <div class="form-row">
      <!-- Estilo -->
      <div class="col">
        <label for="estilo">Estilo</label>
        <input type="text" class="form-control" name="estilo" id="estilo" required>
        <small class="text-danger error-msg" id="error-estilo" style="display:none;"></small>
      </div>

      <!-- Grupo -->
      <div class="col">
        <label for="grupo">Grupo</label>
        <input type="text" class="form-control" name="grupo" id="grupo" required>
        <small class="text-danger error-msg" id="error-grupo" style="display:none;"></small>
      </div>

      <!-- Color -->
      <div class="col">
        <label for="color">Color</label>
        <input type="text" class="form-control" name="color" id="color" required>
        <small class="text-danger error-msg" id="error-color" style="display:none;"></small>
      </div>

      <!-- Talla -->
      <div class="col">
        <label for="talla">Talla</label>
        <input type="text" class="form-control" name="talla" id="talla" required>
        <small class="text-danger error-msg" id="error-talla" style="display:none;"></small>
      </div>
    </div>

    <div class="form-row">
      <!-- Descripción 2 -->
      <div class="col">
        <label for="desc2">Descripción</label>
        <input type="text" class="form-control" name="desc2" id="desc2" readonly>
      </div>

      <!-- Razón -->
      <div class="col">
        <label for="razon">Razón</label>
        <input type="text" class="form-control" name="razon" id="razon" required>
      </div>
    </div>

    <!-- Comentario -->
    <div class="form-row comentario">
      <div class="col">
        <label for="comentario">Comentario</label>
        <textarea class="form-control" name="comentario" id="comentario" cols="30" rows="3"></textarea>
      </div>
    </div>

    <!-- Botones -->
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal">
        <i class="fas fa-ban"></i> Cancelar
      </button>
      <button type="submit" class="btn btn-primary" id="btnOkModalBackorder">Guardar</button>
    </div>
  </form>

  <!-- Tabla -->
  <table id="tblBackorder" class="table table-sm table-hover mt-4">
    <thead>
      <tr>
        <th>No</th>
        <th>Fecha</th>
        <th>Tienda</th>
        <th>Estilo</th>
        <th>Grupo</th>
        <th>Color</th>
        <th>Talla</th>
        <th>Descripción</th>
        <th>Razón</th>
        <th>Comentario</th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th>No</th>
        <th>Fecha</th>
        <th>Tienda</th>
        <th>Estilo</th>
        <th>Grupo</th>
        <th>Color</th>
        <th>Talla</th>
        <th>Descripción</th>
        <th>Razón</th>
        <th>Comentario</th>
      </tr>
    </tfoot>
  </table>
</div>

<script>
  // Concatenar desc2 al cambiar estilo, grupo, color o talla
  function actualizarDescripcion() {
    const estilo = document.getElementById('estilo').value.trim();
    const grupo = document.getElementById('grupo').value.trim();
    const color = document.getElementById('color').value.trim();
    const talla = document.getElementById('talla').value.trim();

    document.getElementById('desc2').value = `${estilo}-${grupo}-${color}-${talla}`;
  }

  document.getElementById('estilo').addEventListener('input', actualizarDescripcion);
  document.getElementById('grupo').addEventListener('input', actualizarDescripcion);
  document.getElementById('color').addEventListener('input', actualizarDescripcion);
  document.getElementById('talla').addEventListener('input', actualizarDescripcion);

  // Función para validar un campo y mostrar error
  function validarCampo(idCampo, regex, msgError) {
    const campo = document.getElementById(idCampo);
    const errorMsg = document.getElementById('error-' + idCampo);
    if (!regex.test(campo.value.trim())) {
      errorMsg.textContent = msgError;
      errorMsg.style.display = 'block';
      campo.classList.add('is-invalid');
      return false;
    } else {
      errorMsg.style.display = 'none';
      campo.classList.remove('is-invalid');
      return true;
    }
  }

  // Validar en tiempo real
  document.getElementById('estilo').addEventListener('input', () => {
    validarCampo('estilo', /^\d{6}$/, 'Estilo debe tener exactamente 6 dígitos.');
  });
  document.getElementById('grupo').addEventListener('input', () => {
    validarCampo('grupo', /^\d{2}$/, 'Grupo debe tener exactamente 2 dígitos.');
  });
  document.getElementById('color').addEventListener('input', () => {
    validarCampo('color', /^\d{2}$/, 'Color debe tener exactamente 2 dígitos.');
  });
  document.getElementById('talla').addEventListener('input', () => {
    validarCampo('talla', /^\d{3}$/, 'Talla debe tener exactamente 3 dígitos.');
  });

  // Validar al enviar formulario
  document.getElementById('frmBackorder').addEventListener('submit', function(e) {
    const validEstilo = validarCampo('estilo', /^\d{6}$/, 'Estilo debe tener exactamente 6 dígitos.');
    const validGrupo = validarCampo('grupo', /^\d{2}$/, 'Grupo debe tener exactamente 2 dígitos.');
    const validColor = validarCampo('color', /^\d{2}$/, 'Color debe tener exactamente 2 dígitos.');
    const validTalla = validarCampo('talla', /^\d{3}$/, 'Talla debe tener exactamente 3 dígitos.');

    if (!validEstilo || !validGrupo || !validColor || !validTalla) {
      e.preventDefault(); // Evita envío si hay error
    }
  });

  // Cargar JS externo si necesitas funcionalidades extras
  var url = "../Js/tienda/backorder.js";
  $.getScript(url);
</script>
