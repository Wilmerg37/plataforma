<?php
require_once "../Funsiones/global.php";
?>

<!-- Referencia al archivo CSS de estilos -->
<link rel="stylesheet" href="../css/estilofiltros.css">

<!-- FORMULARIO DE FILTRO -->
<nav class="navbar navbar-light justify-content-center">
  <form class="form-inline" id="frmfiltro" method="POST" action="">

    <!-- Subsidiaria -->
    <div class="form-group mx-2">
      <label for="sbs" class="form-label">
        <i class="fas fa-building me-1"></i> Subsidiaria
      </label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-building"></i>
        </span>
        <select name="sbs" id="sbs" class="form-select" required>
          <option value="">Subsidiaria...</option>
          <?php echo Subsidiaria(); ?>
        </select>
      </div>
    </div>

    <!-- Tipo de video -->
    <div class="form-group mx-2">
      <label for="tipo" class="form-label">
        <i class="fas fa-video me-1"></i> Tipo de Video
      </label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-video"></i>
        </span>
        <select name="tipo" id="tipo" class="form-select" required>
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
      <label for="video" class="form-label">
        <i class="fas fa-list me-1"></i> Video
      </label>
      <div class="input-group">
        <span class="input-group-text">
          <i class="fas fa-list"></i>
        </span>
        <select name="video" id="video" class="form-select" required>
          <option value="">Seleccione un video</option>
        </select>
      </div>
    </div>

    <!-- Botón -->
    <div class="form-group mx-2">
      <label class="form-label" style="visibility: hidden;">Acción</label>
      <button class="btn btn-outline-primary" type="submit">
        <i class="fas fa-play me-1"></i>Ver Video
      </button>
    </div>

  </form>
</nav>

<!-- CONTENEDOR DONDE SE CARGARÁ EL VIDEO -->
<div class="container-fluid" id="Tablas">
  <div id="contenedor-video" class="text-center mt-4"></div>
</div>

<!-- SCRIPTS -->
<script>
  // Cargar lista de videos según tipo
  $('#tipo').on('change', function () {
    const tipo = $('#tipo').val();
    if (tipo) {
      // Agregar clase de loading al botón
      $('#frmfiltro button[type="submit"]').addClass('btn-loading');
      
      $.ajax({
        url: 'AulaVirtual/videos.php',
        type: 'POST',
        data: { tipo: tipo },
        success: function (data) {
          $('#video').html(data);
          $('#frmfiltro button[type="submit"]').removeClass('btn-loading');
        },
        error: function () {
          $('#video').html('<option value="">Error al cargar videos</option>');
          $('#frmfiltro button[type="submit"]').removeClass('btn-loading');
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
      // Mostrar alerta estilizada
      const alertHtml = `
        <div class="alert alert-warning alert-dismissible fade show" role="alert" style="
          background: linear-gradient(135deg, var(--warning-color), #f59e0b);
          color: white;
          border: none;
          border-radius: 12px;
          box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        ">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Por favor seleccione el tipo y el video
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
      `;
      $('#contenedor-video').html(alertHtml);
      return;
    }

    // Agregar clase de loading al botón
    $(this).find('button[type="submit"]').addClass('btn-loading');

    $.ajax({
      url: 'AulaVirtual/videos.php',
      type: 'POST',
      data: { tipo: tipo, video: video },
      success: function (html) {
        $('#contenedor-video').html(html);
        $('button[type="submit"]').removeClass('btn-loading');
      },
      error: function () {
        const errorHtml = `
          <div class="alert alert-danger" style="
            background: linear-gradient(135deg, var(--danger-color), #ef4444);
            color: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
          ">
            <i class="fas fa-exclamation-circle me-2"></i>
            Error al cargar el video.
          </div>
        `;
        $('#contenedor-video').html(errorHtml);
        $('button[type="submit"]').removeClass('btn-loading');
      }
    });
  });
</script>