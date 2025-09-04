<?php
require_once "../Funsiones/global.php";
require_once "../auth.php";
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Roy | LABORATORIO</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="../Plugin/jquery-ui/jquery-ui.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../Plugin/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../Plugin/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../Plugin/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../Plugin/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../dist/css/adminlte.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../Plugin/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../Plugin/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../Plugin/summernote/summernote-bs4.css">
  <!-- Datatables -->
  <link rel="stylesheet" href="../Plugin/datatables-bs4/css/dataTables.bootstrap4.min.css">

  <link rel="stylesheet" href="../Plugin/datatables-responsive/css/responsive.bootstrap4.min.css">

  <link rel="stylesheet" href="../Plugin/sweetalert2/sweetalert2.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <link rel="stylesheet" href="../Css/EstilosGenerales.css">
  <link rel="shortcut icon" href="../favicon.ico">
    <link rel="stylesheet" href="../Css/estiloinicio.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
  <div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>


      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-comments"></i>
            <span class="badge badge-danger navbar-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="../dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    Brad Diesel
                    <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">Call me whenever you can...</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="../dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    John Pierce
                    <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">I got your message bro</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <!-- Message Start -->
              <div class="media">
                <img src="../dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                <div class="media-body">
                  <h3 class="dropdown-item-title">
                    Nora Silvester
                    <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                  </h3>
                  <p class="text-sm">The subject goes here</p>
                  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                </div>
              </div>
              <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
          </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge">15</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <span class="dropdown-item dropdown-header">15 Notifications</span>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-envelope mr-2"></i> 4 new messages
              <span class="float-right text-muted text-sm">3 mins</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-users mr-2"></i> 8 friend requests
              <span class="float-right text-muted text-sm">12 hours</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
              <i class="fas fa-file mr-2"></i> 3 new reports
              <span class="float-right text-muted text-sm">2 days</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
          </div>
        </li>

        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            <img src="<?php imagenPerfil($_SESSION['user'][7], $_SESSION['user'][11]) ?>" class="user-image img-circle elevation-2" id="userImage" alt="User Image">
            <span class=" d-md-inline"><?php nombrePerfil($_SESSION['user'][1]) ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <!-- User image -->
            <li class="user-header bg-primary">
              <img src="<?php imagenPerfil($_SESSION['user'][7], $_SESSION['user'][11]) ?>" class="img-circle elevation-2" alt="User Image">

              <p>
                <?php echo $_SESSION['user'][3] . "<br> <small>" . departamento($_SESSION['user'][5], $_SESSION['user'][6]) . "</small>" ?>

                <small>Miembro desde <?php echo date('M', strtotime($_SESSION['user'][4])) . "-" . date('Y', strtotime($_SESSION['user'][4])) ?></small>
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <a href="#" class="btn btn-default btn-flat">Perfil</a>
              <a href="../index.php" class="btn btn-default btn-flat float-right">Cerrar sesión</a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="inicio.php" class="brand-link">
        <img src="../Image/favicon.png" alt="Plataforma Roy" class="brand-image" style="opacity: .8">
        <span class="brand-text font-weight-light"><b>LABORATORIO ROY</b></span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <input type="text" id="subsidiaria" value="<?php echo $_SESSION['user'][10] ?>" hidden>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" id="modulos">

            <li class="nav-item">
              <a href="inicio.php" class="nav-link">
                <i class="nav-icon fas fa-home"></i>
                <p>
                  INICIO
                </p>
              </a>
            </li>
            <?php $modulos = modulos($_SESSION['user'][2]);
            foreach ($modulos as $modulo) { ?>
              <li class="nav-item has-treeview modulos" id="<?php echo $modulo[0] ?>">
                <a href="#" class="nav-link modulo">
                  <i class="nav-icon <?php echo trim($modulo[1]) ?>"></i>
                  <p><?php echo trim($modulo[2]) ?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php $paginas = paginas($modulo[0],$modulo[3]);
                  foreach ($paginas as $pagina) { ?>
                    <li class="nav-item opciones" id="<?php echo $pagina[0] ?>">
                      <a href="#" class="nav-link opcion">
                      <i class="nav-icon <?php echo trim($pagina[2]) ?>"></i>
                        <p><?php echo $pagina[1] ?></p>
                        
                      </a>
                    </li>
                  <?php
                  }
                  ?>
                </ul>
              </li>
            <?php
            }
            ?>
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark" id="titulo"></h1>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
      </section>

      <!-- Chat -->
      <div class="collapse slide" id="collapseExample">
        <div class=" card direct-chat direct-chat-primary chatroy position-fixed" style="width: 22rem;">
          <div class="card-header">
            <h3 class="card-title">Roy Chat</h3>

            <div class="card-tools">
              <span data-toggle="tooltip" title="3 New Messages" class="badge badge-primary">3</span>
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
                <i class="fas fa-comments"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <!-- Conversations are loaded here -->
            <div class="direct-chat-messages">
              <!-- Message. Default to the left -->
              <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-left">Alexander Pierce</span>
                  <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                </div>
                <!-- /.direct-chat-infos -->
                <img class="direct-chat-img" src="../dist/img/user1-128x128.jpg" alt="message user image">
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                  Is this template really for free? That's unbelievable!
                </div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->

              <!-- Message to the right -->
              <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-right">Sarah Bullock</span>
                  <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                </div>
                <!-- /.direct-chat-infos -->
                <img class="direct-chat-img" src="../dist/img/user3-128x128.jpg" alt="message user image">
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                  You better believe it!
                </div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->

              <!-- Message. Default to the left -->
              <div class="direct-chat-msg">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-left">Alexander Pierce</span>
                  <span class="direct-chat-timestamp float-right">23 Jan 5:37 pm</span>
                </div>
                <!-- /.direct-chat-infos -->
                <img class="direct-chat-img" src="../dist/img/user1-128x128.jpg" alt="message user image">
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                  Working with AdminLTE on a great new app! Wanna join?
                </div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->

              <!-- Message to the right -->
              <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                  <span class="direct-chat-name float-right">Sarah Bullock</span>
                  <span class="direct-chat-timestamp float-left">23 Jan 6:10 pm</span>
                </div>
                <!-- /.direct-chat-infos -->
                <img class="direct-chat-img" src="../dist/img/user3-128x128.jpg" alt="message user image">
                <!-- /.direct-chat-img -->
                <div class="direct-chat-text">
                  I would love to.
                </div>
                <!-- /.direct-chat-text -->
              </div>
              <!-- /.direct-chat-msg -->

            </div>
            <!--/.direct-chat-messages-->

            <!-- Contacts are loaded here -->
            <div class="direct-chat-contacts">
              <ul class="contacts-list">
                <li>
                  <a href="#">
                    <img class="contacts-list-img" src="../dist/img/user1-128x128.jpg">

                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Count Dracula
                        <small class="contacts-list-date float-right">2/28/2015</small>
                      </span>
                      <span class="contacts-list-msg">How have you been? I was...</span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img class="contacts-list-img" src="../dist/img/user7-128x128.jpg">

                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Sarah Doe
                        <small class="contacts-list-date float-right">2/23/2015</small>
                      </span>
                      <span class="contacts-list-msg">I will be waiting for...</span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img class="contacts-list-img" src="../dist/img/user3-128x128.jpg">

                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Nadia Jolie
                        <small class="contacts-list-date float-right">2/20/2015</small>
                      </span>
                      <span class="contacts-list-msg">I'll call you back at...</span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img class="contacts-list-img" src="../dist/img/user5-128x128.jpg">

                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Nora S. Vans
                        <small class="contacts-list-date float-right">2/10/2015</small>
                      </span>
                      <span class="contacts-list-msg">Where is your new...</span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img class="contacts-list-img" src="../dist/img/user6-128x128.jpg">

                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        John K.
                        <small class="contacts-list-date float-right">1/27/2015</small>
                      </span>
                      <span class="contacts-list-msg">Can I take a look at...</span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
                <li>
                  <a href="#">
                    <img class="contacts-list-img" src="../dist/img/user8-128x128.jpg">

                    <div class="contacts-list-info">
                      <span class="contacts-list-name">
                        Kenneth M.
                        <small class="contacts-list-date float-right">1/4/2015</small>
                      </span>
                      <span class="contacts-list-msg">Never mind I found...</span>
                    </div>
                    <!-- /.contacts-list-info -->
                  </a>
                </li>
                <!-- End Contact Item -->
              </ul>
              <!-- /.contacts-list -->
            </div>
            <!-- /.direct-chat-pane -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <form action="#" method="post">
              <div class="input-group">
                <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                <span class="input-group-append">
                  <button type="button" class="btn btn-primary">Send</button>
                </span>
              </div>
            </form>
          </div>
          <!-- /.card-footer-->
        </div>
      </div>

      <a class="btn btn-primary bchatroy  position-fixed" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
        <i class="fas fa-comment"></i> chat
      </a>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer text-center">
      <strong>Copyright Wilmer G. &copy; 2021-<?php echo date('Y') ?> Calzado Roy</strong>
      All rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- jQuery -->
  <script src="../Plugin/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="../Plugin/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>

  <!-- Bootstrap 4 -->
  <script src="../Plugin/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="../Plugin/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="../Plugin/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="../Plugin/jqvmap/jquery.vmap.min.js"></script>
  <script src="../Plugin/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="../Plugin/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="../Plugin/moment/moment.min.js"></script>

  <script src="../Plugin/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../Plugin/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="../Plugin/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../Plugin/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->

  <script src="../Plugin/sweetalert2/sweetalert2.min.js"></script>
  <script src="../Plugin/datatables/jquery.dataTables.min.js"></script>
  <script src="../Plugin/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="../Plugin/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="../Plugin/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

  <script src="../Js/Inicio.js"></script>
  <script src="../Js/tablero/Trafico.js"></script>
</body>

</html>