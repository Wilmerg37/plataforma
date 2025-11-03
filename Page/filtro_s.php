<?php require_once "../Funsiones/global.php"; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Date Range Picker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
   <link rel="stylesheet" href="../css/estilofiltros.css">
</head>
<body>

<!-- Filtro de Supervisión -->
<nav class="navbar navbar-light bg-light justify-content-center py-3">
  <form class="form-inline row g-2 align-items-center w-100" id="frmfiltro">
    
    <!-- Subsidiaria -->
<div class="col-md-3">
  <label for="sbs" class="form-label">
    <i class="fas fa-building me-1"></i> Subsidiaria
  </label>
  <div class="input-group">
    <span class="input-group-text">
      <i class="fas fa-building"></i>
    </span>
    <select name="sbs" id="sbs" class="form-select validate[required]">
      <option value="">Seleccione...</option>
      <?php echo Subsidiaria(); ?>
    </select>
  </div>
</div>

    <!-- Código de Supervisor -->
    <div class="col-md-2">
      <label for="tienda" class="form-label">
        <i class="fas fa-user-tie me-1"></i> Código Supervisor
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
        <input type="text" name="tienda" id="tienda" class="form-control" placeholder="Ej. 1234" autocomplete="off">
      </div>
    </div>

    <!-- Fecha -->
    <div class="col-md-3">
      <label for="fecha" class="form-label">
        <i class="far fa-calendar-alt me-1"></i> Rango de Fecha
      </label>
      <div class="input-group">
        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
        <input type="text" name="fecha" id="fecha" class="form-control fecha" autocomplete="off">
      </div>
    </div>

    <!-- Checkboxes -->
    <div class="col-md-2 d-flex align-items-center flex-column">
      <label class="form-label align-self-start">
        <i class="fas fa-cogs me-1"></i> Opciones
      </label>
      <div class="w-100">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="iva" id="Check1" value="1">
          <label class="form-check-label" for="Check1">
            <i class="fas fa-percentage me-1"></i> IVA
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="vacacionista" id="Check2" value="1">
          <label class="form-check-label" for="Check2">
            <i class="fas fa-umbrella-beach me-1"></i> Vacacionistas
          </label>
        </div>
      </div>
    </div>

    <!-- Botón Generar -->
    <div class="col-md-2 text-end">
      <label class="form-label">&nbsp;</label>
      <button class="btn btn-outline-primary w-100" type="submit">
        <i class="fas fa-search me-1"></i> Generar
      </button>
    </div>

  </form>
</nav>

<!-- Tabla de resultados -->
<div class="container-fluid mt-4" id="Tablas"></div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
 // Versión simplificada - Solo reemplaza tu script actual con este
function initDateRangePicker() {
    if (typeof $ !== 'undefined' && 
        typeof moment !== 'undefined' && 
        $.fn.daterangepicker && 
        $('.fecha').length > 0) {
        
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
        
        return true;
    }
    return false;
}

// Intentar inicializar múltiples veces si es necesario
$(document).ready(function() {
    let attempts = 0;
    const maxAttempts = 50;
    
    const tryInit = setInterval(function() {
        attempts++;
        
        if (initDateRangePicker()) {
            clearInterval(tryInit);
            console.log('DateRangePicker inicializado en intento:', attempts);
        } else if (attempts >= maxAttempts) {
            clearInterval(tryInit);
            console.error('No se pudo inicializar DateRangePicker después de', maxAttempts, 'intentos');
        }
    }, 100);
});

// Cargar filtro.js después de que daterangepicker esté listo
setTimeout(function() {
    var url = "../Js/supervision/filtro.js";
    $.getScript(url);
}, 1000);
</script>

</body>
</html>