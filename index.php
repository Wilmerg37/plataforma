<?php
  session_start();
  session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Roy | Ingreso LABORATORIO</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

 <link rel="stylesheet" href="Plugin/sweetalert2/sweetalert2.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="Plugin/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="Plugin/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Estilos Generales -->
  <link rel="stylesheet" href="Css/EstilosGenerales.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <link rel="shortcut icon" href="favicon.ico">
</head>
<body class="hold-transition login-page fondoLogin">
<div class="login-box">
  <div class="login-logo">
		<img src="Image/logo.png" alt="Plataforma roy" width ="300" height="72">
    	<b class="text-white">LABORATIORIO</b>
  </div>
  <!-- /.login-logo -->
  <div class="card shadow">
    <div class="card-body login-card-body rounded">
      <p class="login-box-msg">Ingresar</p>

      <form id="frmLogin">
        <div class="input-group mb-3">
          <input type="text" name="user" id="user" class="form-control" pattern="[0-9]{4}" placeholder="Código empleado" autocomplete="off" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="pass" id="pass" class="form-control" placeholder="Contraseña" autocomplete="off" required >
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col mb-4">
            <button type="submit" class="btn btn-primary btn-block"> <i class="fas fa-sign-in-alt"></i> Accesar</button>
          </div>
		</div>
		<p class=" text-center"> Wilmer G. &copy; 2022 - 2024</p>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="Plugin/JQuery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="Plugin/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<script src="Plugin/sweetalert2/sweetalert2.min.js"></script>
<script src="Js/Login.js"></script>


</body>
</html>
