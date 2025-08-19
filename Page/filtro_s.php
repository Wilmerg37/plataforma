<?php require_once "../Funsiones/global.php"; ?>

<!-- Filtro de Supervisión -->
<nav class="navbar navbar-light bg-light justify-content-center py-3">
  <form class="form-inline row g-2 align-items-center w-100" id="frmfiltro">
    
    <!-- Subsidiaria -->
    <div class="col-md-3">
      <label for="sbs" class="form-label">Subsidiaria</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-building"></i></span>
        <select name="sbs" id="sbs" class="form-control validate[required]">
          <option value="">Seleccione...</option>
          <?php echo Subsidiaria(); ?>
        </select>
      </div>
    </div>

    <!-- Código de Supervisor -->
    <div class="col-md-2">
      <label for="tienda" class="form-label">Código Supervisor</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-store"></i></span>
        <input type="text" name="tienda" id="tienda" class="form-control" placeholder="Ej. 1234" autocomplete="off">
      </div>
    </div>

    <!-- Fecha -->
    <div class="col-md-3">
      <label for="fecha" class="form-label">Rango de Fecha</label>
      <div class="input-group">
        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
        <input type="text" name="fecha" id="fecha" class="form-control fecha" autocomplete="off">
      </div>
    </div>

    <!-- Checkboxes -->
    <div class="col-md-2 d-flex align-items-center">
      <div class="form-check me-3">
        <input class="form-check-input" type="checkbox" name="iva" id="Check1" value="1">
        <label class="form-check-label" for="Check1">IVA</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="vacacionista" id="Check2" value="1">
        <label class="form-check-label" for="Check2">Vacacionistas</label>
      </div>
    </div>

    <!-- Botón Generar -->
    <div class="col-md-2 text-end">
      <button class="btn btn-outline-primary w-100" type="submit">
        <i class="fas fa-search"></i> Generar
      </button>
    </div>

  </form>
</nav>

<!-- Tabla de resultados -->
<div class="container-fluid mt-4" id="Tablas"></div>

<!-- Scripts -->
<script>
  $('.fecha').daterangepicker({
    showDropdowns: true,
    autoApply: true,
    locale: {
      format: 'DD-MM-YYYY',
      separator: ' a ',
      weekLabel: 'Sm',
      daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
      monthNames: [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
      ],
      firstDay: 0
    }
  });

  // Cargar script adicional
  var url = "../Js/supervision/filtro.js";
  $.getScript(url);
</script>
