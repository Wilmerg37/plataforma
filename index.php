<?php
  session_start();
  session_destroy();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Roy | Ingreso LABORATORIO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS Plugins - Mantener orden original -->
  <link rel="stylesheet" href="Plugin/sweetalert2/sweetalert2.min.css">
  <link rel="stylesheet" href="Plugin/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="Plugin/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="Css/EstilosGenerales.css">
  
  <!-- Fuentes modernas -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="favicon.ico">
  <link rel="stylesheet" href="Css/estiloindex.css">
  
 
</head>

<body class="hold-transition login-page fondoLogin">
  <div class="login-page fondoLogin">
    <div class="login-container">
      <!-- Panel de Bienvenida (Lado Izquierdo) -->
      <div class="welcome-panel">
        <div class="welcome-logo">
          <img src="Image/favicon.png" alt="Logo Roy" width="60" height="60">
          <span class="welcome-logo-text">LABORATORIO ROY</span>
        </div>
        
        <h1 class="welcome-title">¡Bienvenido de vuelta!</h1>
        
        <p class="welcome-subtitle">
          Accede a tu plataforma de laboratorio para gestionar, 
          resultados y análisis de manera profesional.
        </p>
      </div>

      <!-- Panel del Formulario (Lado Derecho) -->
      <div class="form-panel">
        <!-- Header móvil -->
        <div class="mobile-header">
          <img src="Image/logo.png" alt="Plataforma Roy">
          <h1>LABORATORIO</h1>
        </div>

        <div class="form-header">
          <h2 class="form-title">Iniciar Sesión</h2>
          <p class="form-subtitle">Ingresa tus credenciales para acceder</p>
        </div>

        <form id="frmLogin">
          <div class="form-group">
            <label for="user" class="form-label">Código de Empleado</label>
            <div class="input-wrapper">
              <input type="text" 
                     name="user" 
                     id="user" 
                     class="form-input" 
                     pattern="[0-9]{4}" 
                     placeholder="Ingresa tu código (4 dígitos)" 
                     autocomplete="username" 
                     required
                     aria-label="Código de empleado">
            </div>
          </div>

          <div class="form-group">
            <label for="pass" class="form-label">Contraseña</label>
            <div class="input-wrapper">
              <input type="password" 
                     name="pass" 
                     id="pass" 
                     class="form-input" 
                     placeholder="Ingresa tu contraseña" 
                     autocomplete="current-password" 
                     required
                     aria-label="Contraseña">
            </div>
          </div>

          <div class="form-group">
            <button type="submit" class="btn-login">
              <i class="fas fa-sign-in-alt mr-2"></i>
              Accesar
            </button>
          </div>

          <div class="form-footer">
            Wilmer G. &copy; 2022 - <?php echo date('Y'); ?>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Scripts - Mantener orden original -->
  <script src="Plugin/JQuery/jquery.min.js"></script>
  <script src="Plugin/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="dist/js/adminlte.min.js"></script>
  <script src="Plugin/sweetalert2/sweetalert2.min.js"></script>
  <script src="Js/Login.js"></script>

  <!-- Script adicional para mejorar UX -->
  <script>
    $(document).ready(function() {
      // Mantener toda la funcionalidad original pero con mejoras visuales
      initializeFormEnhancements();
    });

    function initializeFormEnhancements() {
      const form = $('#frmLogin');
      const submitBtn = form.find('button[type="submit"]');
      const inputs = form.find('input');

      // Manejar estados de loading
      form.on('submit', function(e) {
        // Tu lógica de submit original se mantiene en Login.js
        submitBtn.addClass('loading');
        submitBtn.prop('disabled', true);

        // Remover loading después de 3 segundos si no hay respuesta
        setTimeout(() => {
          submitBtn.removeClass('loading');
          submitBtn.prop('disabled', false);
        }, 3000);
      });

      // Validación visual en tiempo real
      inputs.on('input blur', function() {
        const input = $(this);
        const value = input.val().trim();

        if (input.attr('name') === 'user') {
          if (value.length === 4 && /^\d{4}$/.test(value)) {
            input.removeClass('is-invalid').addClass('is-valid');
          } else if (value.length > 0) {
            input.removeClass('is-valid').addClass('is-invalid');
          } else {
            input.removeClass('is-valid is-invalid');
          }
        }

        if (input.attr('name') === 'pass') {
          if (value.length >= 3) {
            input.removeClass('is-invalid').addClass('is-valid');
          } else if (value.length > 0) {
            input.removeClass('is-valid').addClass('is-invalid');
          } else {
            input.removeClass('is-valid is-invalid');
          }
        }
      });

      // Auto-focus en el primer campo
      setTimeout(() => {
        $('#user').focus();
      }, 800);

      // Prevenir múltiples submits
      let isSubmitting = false;
      form.on('submit', function() {
        if (isSubmitting) {
          return false;
        }
        isSubmitting = true;
        setTimeout(() => {
          isSubmitting = false;
        }, 2000);
      });

      // Manejar Enter key para navegar entre campos
      inputs.on('keypress', function(e) {
        if (e.which === 13) {
          const nextInput = inputs.eq(inputs.index(this) + 1);
          if (nextInput.length) {
            nextInput.focus();
          } else {
            form.submit();
          }
        }
      });

      // Efectos adicionales
      inputs.on('focus', function() {
        $(this).closest('.input-wrapper').addClass('focused');
      });

      inputs.on('blur', function() {
        $(this).closest('.input-wrapper').removeClass('focused');
      });
    }

    // Compatibilidad con SweetAlert existente
    function showError(message) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error de acceso',
          text: message,
          confirmButtonColor: '#3b82f6',
          customClass: {
            popup: 'animated fadeInDown'
          }
        });
      }
    }

    function showLoading() {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Verificando credenciales...',
          text: 'Por favor espere un momento',
          allowOutsideClick: false,
          customClass: {
            popup: 'animated fadeInUp'
          },
          didOpen: () => {
            Swal.showLoading();
          }
        });
      }
    }
  </script>
</body>
</html>