<?php require_once "../Funsiones/global.php"; ?>

<!-- Referencia al archivo CSS de estilos -->
<link rel="stylesheet" href="../css/estilofiltros.css">

<nav class="navbar navbar-light justify-content-center">
  <form class="form-inline" id="frmfiltro">
    
    <!-- Subsidiaria -->
    <div class="form-group mx-2">
      
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-building"></i>
        </span>
        <select name="sbs" onchange="" class="validate[required] form-select">
          <option value="">Subsidiaria...</option>
          <?php echo Subsidiaria() ?>
        </select>
      </div>
    </div>

    <!-- Tienda -->
    <div class="form-group mx-2">
      
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-store"></i>
        </span>
        <input type="text" 
               name="tienda" 
               id="tienda" 
               class="form-control" 
               placeholder="Tiendas" 
               autocomplete="off" 
               value="<?php echo $_SESSION['user'][6]; ?>">
      </div>
    </div>

    <!-- Fecha -->
    <div class="form-group mx-2">
      
      <div class="input-group">
        <span class="input-group-text">
          <i class="far fa-calendar-alt"></i>
        </span>
        <input type="text"  name="fecha"  id="fecha" class="form-control float-right fecha"    autocomplete="off"  placeholder="Seleccionar fechas">
      </div>
    </div>

    <!-- Checkboxes -->
    <div class="form-group mx-2">
     
      
      <div class="form-check">
        <input class="form-check-input" 
               name="iva" 
               type="checkbox" 
               value="1" 
               id="Check1">
        <label class="form-check-label" for="Check1">
          <i class="fas fa-percentage me-1"></i> Iva
        </label>
      </div>

      <div class="form-check">
        <input class="form-check-input" 
               name="vacacionista" 
               type="checkbox" 
               value="1" 
               id="Check2">
        <label class="form-check-label" for="Check2">
          <i class="fas fa-umbrella-beach me-1"></i> Vacacionistas
        </label>
      </div>
    </div>

    <!-- Botón -->
    <div class="form-group mx-2">
      <label class="form-label" style="visibility: hidden;">Acción</label>
      <button class="btn btn-outline-primary" type="submit">
        <i class="fas fa-search me-1"></i>Generar
      </button>
    </div>

  </form>
</nav>



<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(document).ready(function() {
    // Función para inicializar daterangepicker
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

    // Intentar inicializar hasta que funcione
    let attempts = 0;
    const tryInit = setInterval(function() {
        attempts++;
        
        if (initDateRangePicker()) {
            clearInterval(tryInit);
            console.log('DateRangePicker inicializado en intento:', attempts);
        } else if (attempts >= 20) {
            clearInterval(tryInit);
        }
    }, 200);
});
</script>

<!-- Tabla de resultados -->
<div class="container-fluid mt-4" id="Tablas"></div>

<script>
  var url = "../Js/tienda/filtro.js";
  $.getScript(url);
</script>