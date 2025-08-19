<?php
require_once "../../Funsiones/global.php";
?>

<style>
  /* Variables CSS para colores consistentes */
  :root {
    --primary-color: #3498db;
    --primary-dark: #2980b9;
    --secondary-color: #1abc9c;
    --secondary-dark: #16a085;
    --danger-color: #e74c3c;
    --danger-dark: #c0392b;
    --success-color: #27ae60;
    --text-color: #2c3e50;
    --text-light: #34495e;
    --bg-light: #f8f9ff;
    --border-light: #e1e5e9;
    --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 6px 20px rgba(0, 0, 0, 0.15);
  }

  /* Body y contenedor principal */
  body {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    padding: 20px 0;
  }

  /* Contenedor principal con glassmorphism */
  .container {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    margin: 20px auto;
    max-width: 1200px;
  }

  .container:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
  }

  /* Título principal con gradiente */
  h1 {
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 800;
    font-size: 2.5rem;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
  }

  h1::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    border-radius: 2px;
  }

  /* Header con icono */
  .form-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 20px 0;
  }

  .form-header i {
    font-size: 3rem;
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 15px;
    display: block;
  }

  /* Labels mejoradas */
  label {
    color: var(--text-color);
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  label i {
    color: var(--primary-color);
    width: 16px;
  }

  /* Form controls mejorados */
  .form-control {
    border: 2px solid var(--border-light);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15), 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    outline: none;
  }

  .form-control:hover:not(:focus) {
    border-color: var(--primary-color);
    transform: translateY(-1px);
  }

  /* Readonly inputs */
  .form-control[readonly] {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
    border-color: var(--border-light);
    color: var(--text-light);
  }

  /* Select mejorado */
  select.form-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%233498db' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px;
    appearance: none;
  }

  /* Date input mejorado */
  input[type="date"] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233498db' stroke-width='2'%3e%3crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3e%3cline x1='16' y1='2' x2='16' y2='6'/%3e%3cline x1='8' y1='2' x2='8' y2='6'/%3e%3cline x1='3' y1='10' x2='21' y2='10'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 18px;
  }

  /* Textarea mejorado */
  textarea.form-control {
    resize: vertical;
    min-height: 100px;
  }

  /* Estados de validación */
  .is-invalid {
    border-color: var(--danger-color) !important;
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.15) !important;
    animation: shake 0.5s ease-in-out;
  }

  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
  }

  /* Mensajes de error */
  .text-danger {
    font-size: 0.85rem;
    margin-top: 0.5rem;
    padding: 8px 12px;
    background: rgba(231, 76, 60, 0.1);
    border-radius: 6px;
    border-left: 3px solid var(--danger-color);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* Form rows con mejor espaciado */
  .form-row {
    margin-bottom: 1.5rem;
    display: flex;
    gap: 20px;
  }

  .form-row .col {
    flex: 1;
    position: relative;
  }

  .form-row.comentario .col {
    flex: 1;
  }

  /* Botones mejorados */
  .btn {
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    min-width: 140px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-primary {
    background: linear-gradient(45deg, var(--secondary-color), var(--secondary-dark));
    color: white;
    box-shadow: 0 4px 15px rgba(26, 188, 156, 0.3);
  }

  .btn-primary:hover {
    background: linear-gradient(45deg, var(--secondary-dark), #138d75);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(26, 188, 156, 0.4);
    color: white;
  }

  .btn-danger {
    background: linear-gradient(45deg, var(--danger-color), var(--danger-dark));
    color: white;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
  }

  .btn-danger:hover {
    background: linear-gradient(45deg, var(--danger-dark), #992d22);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
    color: white;
  }

  /* Modal footer mejorado */
  .modal-footer {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid var(--border-light);
  }

  /* Tabla moderna */
  .table {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-top: 2rem;
  }

  .table thead th {
    background: linear-gradient(45deg, var(--primary-color), var(--primary-dark));
    color: white;
    padding: 15px 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
    border: none;
    position: relative;
  }

  .table thead th:first-child {
    border-radius: 15px 0 0 0;
  }

  .table thead th:last-child {
    border-radius: 0 15px 0 0;
  }

  .table tbody tr {
    background: white;
    transition: all 0.3s ease;
  }

  .table tbody tr:nth-child(even) {
    background: var(--bg-light);
  }

  .table tbody tr:hover {
    background: rgba(52, 152, 219, 0.08);
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .table tbody td {
    padding: 12px;
    color: var(--text-light);
    border: none;
    border-bottom: 1px solid var(--border-light);
  }

  .table tfoot th {
    background: #ecf0f1;
    color: var(--text-color);
    font-weight: 600;
    padding: 12px;
    border: none;
  }

  .table tfoot th:first-child {
    border-radius: 0 0 0 15px;
  }

  .table tfoot th:last-child {
    border-radius: 0 0 15px 0;
  }

  /* Responsive design */
  @media (max-width: 768px) {
    .container {
      margin: 10px;
      border-radius: 15px;
      padding: 20px 15px;
    }

    .form-row {
      flex-direction: column;
      gap: 15px;
    }

    h1 {
      font-size: 2rem;
    }

    .modal-footer {
      flex-direction: column;
    }

    .btn {
      width: 100%;
    }

    .table {
      font-size: 0.85rem;
    }

    .table thead th,
    .table tbody td,
    .table tfoot th {
      padding: 8px 6px;
    }
  }

  /* Animaciones adicionales */
  .form-control,
  .btn,
  .col {
    animation: fadeInUp 0.6s ease-out;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Loading state para botones */
  .btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
  }

  /* Tooltips mejorados */
  .tooltip-custom {
    position: relative;
    cursor: help;
  }

  .tooltip-custom::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 100;
  }

  .tooltip-custom:hover::after {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(-5px);
  }
</style>

<div class="container shadow rounded py-4 px-4">
  <div class="form-header">
      <h1>Formulario Backorder</h1>
  </div>

  <form id="frmBackorder">
    <div class="form-row">
      <!-- Tienda -->
      <div class="col">
        <label for="tiendaBackorder">
          <i class="fas fa-store"></i>
          Tienda
        </label>
        <?php 
        // Verificar si el usuario es de tienda (asumiendo que si tiene un valor específico en la posición 6, es de tienda)
        $esDeTienda = !empty($_SESSION['user'][6]) && $_SESSION['user'][6] !== 'ADMIN' && $_SESSION['user'][6] !== 'SUPERVISOR';
        ?>
        
        <?php if ($esDeTienda): ?>
          <!-- Usuario de tienda específica - campo deshabilitado -->
          <input type="text" class="form-control" name="tiendaBackorder" id="tiendaBackorder" value="<?php echo $_SESSION['user'][6]; ?>" readonly>
          <!-- Campo oculto para enviar valor de tienda -->
          <input type="hidden" name="tiendaBackorder" value="<?php echo $_SESSION['user'][6]; ?>">
        <?php else: ?>
          <!-- Usuario administrativo - campo habilitado para ingresar tienda -->
          <input type="text" class="form-control" name="tiendaBackorder" id="tiendaBackorder" placeholder="Ingrese numero de tienda" required>
        <?php endif; ?>
      </div>

      <!-- Fecha -->
      <div class="col">
        <label for="fechaBackorder">
          <i class="fas fa-calendar-alt"></i>
          Fecha
        </label>
        <input type="date" class="form-control" name="fechaBackorder" id="fechaBackorder" value="<?php echo date('Y-m-d'); ?>" required>
      </div>
    </div>

        <label for="descripcionTendencia">
          <i class="fas fa-fire"></i>
          Descripción de Producto
        </label>
    <div class="form-row">
      <!-- Estilo -->
      <div class="col">
        <label for="estilo">
          <i class="fas fa-tag"></i>
          Estilo
        </label>
        <input type="text" class="form-control tooltip-custom" name="estilo" id="estilo" required data-tooltip="6 dígitos exactos">
        <small class="text-danger error-msg" id="error-estilo" style="display:none;">
          <i class="fas fa-exclamation-triangle"></i>
          <span></span>
        </small>
      </div>

      <!-- Grupo -->
      <div class="col">
        <label for="grupo">
          <i class="fas fa-layer-group"></i>
          Grupo / Categoria
        </label>
        <input type="text" class="form-control tooltip-custom" name="grupo" id="grupo" required data-tooltip="2 dígitos exactos">
        <small class="text-danger error-msg" id="error-grupo" style="display:none;">
          <i class="fas fa-exclamation-triangle"></i>
          <span></span>
        </small>
      </div>

      <!-- Color -->
      <div class="col">
        <label for="color">
          <i class="fas fa-palette"></i>
          Color
        </label>
        <input type="text" class="form-control tooltip-custom" name="color" id="color" required data-tooltip="2 dígitos exactos">
        <small class="text-danger error-msg" id="error-color" style="display:none;">
          <i class="fas fa-exclamation-triangle"></i>
          <span></span>
        </small>
      </div>

      <!-- Talla -->
      <div class="col">
        <label for="talla">
          <i class="fas fa-ruler"></i>
          Talla
        </label>
        <input type="text" class="form-control tooltip-custom" name="talla" id="talla" required data-tooltip="3 dígitos exactos">
        <small class="text-danger error-msg" id="error-talla" style="display:none;">
          <i class="fas fa-exclamation-triangle"></i>
          <span></span>
        </small>
      </div>
    </div>

    <div class="form-row">
      <!-- Descripción 2 -->
      <div class="col">
        <label for="desc2">
          <i class="fas fa-info-circle"></i>
          Descripción
        </label>
        <input type="text" class="form-control" name="desc2" id="desc2" readonly>
      </div>

      <!-- Razón -->
      <div class="col">
        <label for="razon">
          <i class="fas fa-question-circle"></i>
          Razón
        </label>
        <input type="text" class="form-control" name="razon" id="razon" required>
      </div>
    </div>

    <!-- Comentario -->
    <div class="form-row comentario">
      <div class="col">
        <label for="comentario">
          <i class="fas fa-comment-alt"></i>
          Comentario
        </label>
        <textarea class="form-control" name="comentario" id="comentario" cols="30" rows="3" placeholder="Ingrese comentarios adicionales..."></textarea>
      </div>
    </div>

    <!-- Botones -->
    <div class="modal-footer">
      <button type="button" class="btn btn-danger" data-dismiss="modal">
        <i class="fas fa-times"></i>
        Cancelar
      </button>
      <button type="submit" class="btn btn-primary" id="btnOkModalBackorder">
        <i class="fas fa-save"></i>
        Guardar
      </button>
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
    const errorSpan = errorMsg.querySelector('span');
    
    if (!regex.test(campo.value.trim())) {
      errorSpan.textContent = msgError;
      errorMsg.style.display = 'flex';
      campo.classList.add('is-invalid');
      return false;
    } else {
      errorMsg.style.display = 'none';
      campo.classList.remove('is-invalid');
      return true;
    }
  }

  // Validar en tiempo real
/*  document.getElementById('estilo').addEventListener('input', () => {
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
  });*/

  // Validar al enviar formulario
  document.getElementById('frmBackorder').addEventListener('submit', function(e) {
    const validEstilo = validarCampo('estilo', /^\d{6}$/, 'Estilo debe tener exactamente 6 dígitos.');
    const validGrupo = validarCampo('grupo', /^\d{2}$/, 'Grupo debe tener exactamente 2 dígitos.');
    const validColor = validarCampo('color', /^\d{2}$/, 'Color debe tener exactamente 2 dígitos.');
    const validTalla = validarCampo('talla', /^\d{3}$/, 'Talla debe tener exactamente 3 dígitos.');
    
    // Validar tienda si el campo está habilitado
    let validTienda = true;
    const tiendaSelect = document.getElementById('tiendaBackorder');
    if (!tiendaSelect.disabled && !tiendaSelect.value) {
      tiendaSelect.classList.add('is-invalid');
      validTienda = false;
      
      // Mostrar mensaje de error para tienda
      let errorTienda = document.getElementById('error-tienda');
      if (!errorTienda) {
        errorTienda = document.createElement('small');
        errorTienda.id = 'error-tienda';
        errorTienda.className = 'text-danger error-msg';
        errorTienda.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Debe seleccionar una tienda.</span>';
        errorTienda.style.display = 'flex';
        tiendaSelect.parentElement.appendChild(errorTienda);
      } else {
        errorTienda.style.display = 'flex';
      }
    } else {
      tiendaSelect.classList.remove('is-invalid');
      const errorTienda = document.getElementById('error-tienda');
      if (errorTienda) {
        errorTienda.style.display = 'none';
      }
    }

    if (!validEstilo || !validGrupo || !validColor || !validTalla || !validTienda) {
      e.preventDefault(); // Evita envío si hay error
      
      // Encontrar el primer campo con error y hacer scroll
      const firstError = document.querySelector('.is-invalid');
      if (firstError) {
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstError.focus();
      }
    } else {
      // Mostrar loading en el botón
      const submitBtn = document.getElementById('btnOkModalBackorder');
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
      submitBtn.disabled = true;
      
      // Simular delay para mostrar el loading (remover en producción)
      setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }, 2000);
    }
  });

  // Validación en tiempo real para tienda si está habilitada
  document.getElementById('tiendaBackorder').addEventListener('change', function() {
    if (!this.disabled) {
      if (this.value) {
        this.classList.remove('is-invalid');
        const errorTienda = document.getElementById('error-tienda');
        if (errorTienda) {
          errorTienda.style.display = 'none';
        }
      }
    }
  });

  // Efecto de focus mejorado para inputs
  document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.style.transform = 'scale(1.02)';
    });
    
    input.addEventListener('blur', function() {
      this.parentElement.style.transform = 'scale(1)';
    });
  });

  // Cargar JS externo si necesitas funcionalidades extras
  var url = "../Js/tienda/backorder.js";
  $.getScript(url);
</script>