<?php
require_once "../Funsiones/global.php";
?>

<!-- FORMULARIO DE FILTRO -->
<div class="navbar navbar-light bg-light justify-content-center">
  <form class="form-inline" id="frmfiltro" method="POST" action="">

    <!-- Subsidiaria -->
    <div class="form-group mx-2">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-building"></i></span>
        </div>
        <select name="sbs" class="form-control" required>
          <option value="">Subsidiaria...</option>
          <?php echo Subsidiaria(); ?>
        </select>
      </div>
    </div>

    <!-- Tipo de video -->
    <div class="form-group mx-2">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-video"></i></span>
        </div>
        <select name="tipo" id="tipo" class="form-control" required>
          <option value="">Tipo de Video...</option>
          <option value="clientemisterioso">CLIENTE MISTERIOSO</option>
          <option value="puntosbac">PUNTOS BAC</option>
          <option value="puntoslealtad">PUNTOS LEALTAD</option>
          <option value="vitrinas">OTROS</option>
        </select>
      </div>
    </div>

    <!-- Lista de videos -->
    <div class="form-group mx-2">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text"><i class="fas fa-list"></i></span>
        </div>
        <select name="video" id="video" class="form-control" required>
          <option value="">Seleccione un video</option>
        </select>
      </div>
    </div>

    <!-- Botón -->
    <button class="btn btn-outline-primary" type="submit">
      <i class="fas fa-play"></i> Ver Video
    </button>

  </form>
</div>

<!-- CONTENEDOR DONDE SE CARGARÁ EL VIDEO -->
<div id="contenedor-video" class="text-center mt-4"></div>

<!-- SCRIPTS -->
<script>
  // Cargar lista de videos según tipo
  $('#tipo').on('change', function () {
    const tipo = $('#tipo').val();
    if (tipo) {
      $.ajax({
        url: 'AulaVirtual/videos.php',
        type: 'POST',
        data: { tipo: tipo },
        success: function (data) {
          $('#video').html(data);
        },
        error: function () {
          $('#video').html('<option value="">Error al cargar videos</option>');
        }
      });
    } else {
      $('#video').html('<option value="">Seleccione un video</option>');
    }
  });

  // Reproducir video sin recargar
  $('#frmfiltro').on('submit', function (e) {
    e.preventDefault();

    const tipo = $('#tipo').val();
    const video = $('#video').val();

    if (!tipo || !video) {
      alert('Por favor seleccione el tipo y el video');
      return;
    }

    $.ajax({
      url: 'AulaVirtual/videos.php',
      type: 'POST',
      data: { tipo: tipo, video: video },
      success: function (html) {
        $('#contenedor-video').html(html);
      },
      error: function () {
        $('#contenedor-video').html('<div class="alert alert-danger">Error al cargar el video.</div>');
      }
    });
  });
</script>
