<?php
require_once "../Funsiones/global.php";
?>
<nav class="navbar navbar-light bg-light justify-content-center">
  <form class="form-inline" id="frmfiltro">

    <div class="form-group mx-2">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">
            <i class="fas fa-building"></i>
          </span>
        </div>
        <select name="sbs" onchange="" class="validate[required] form-control" >
        <option value="">Subsidiaria...</option>
        <?php echo Subsidiaria() ?>
        </select>
      </div>
      <!-- /.input group -->

    </div>
    <div class="form-group">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">
            <i class="fas fa-store"></i>
          </span>
        </div>
        <input type="text" name="tienda" id="tienda" class="form-control float-right" placeholder="Tiendas" autocomplete="off">
      </div>
      <!-- /.input group -->
    </div>

    <div class="form-group mx-2">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">
            <i class="far fa-calendar-alt"></i>
          </span>
        </div>
        <input type="text" name="fecha" id="fecha" class="form-control float-right fecha" autocomplete="off">
      </div>
      <!-- /.input group -->
    </div>

    <div class="form-check ">
      <input class="form-check-input" name="iva" type="checkbox" value="1" id="Check1">
      <label class="form-check-label" for="Check1"> Iva </label>
    </div>
    <div class="form-check mx-2">
      <input class="form-check-input" name="vacacionista" type="checkbox" value="1" id="Check2">
      <label class="form-check-label" for="Check2">Vacacionistas </label>
    </div>
    <button class="btn btn-outline-primary my-2 my-sm-0" type="submit"> <i class="fas fa-search"></i>Generar</button>
  </form>
</nav>

<script>
  $('.fecha').daterangepicker({
    "showDropdowns": true,
   // "showISOWeekNumbers": true,
    "autoApply": true,
    "locale": {
      "format": "DD-MM-YYYY",
      "separator": " a ",
      "weekLabel": "Sm",
      "daysOfWeek": [
        "Do",
        "Lu",
        "Ma",
        "Mi",
        "Ju",
        "Vi",
        "Sa"
      ],
      "monthNames": [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Deciembre"
      ],
      "firstDay": 0
    },
  });
</script>


<div class="container-fluid" id="Tablas">

</div>

<script>
  var url = "../Js/tienda/filtro.js";
  $.getScript(url);
</script>