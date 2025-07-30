<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Gerentes - Aprobación de Solicitudes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ENLACES DE CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- ENLACES DE JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    /* ESTILOS MEJORADOS PARA VISTA PROFESIONAL */
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
    }

    .main-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
      margin: 20px;
      padding: 30px;
    }

    .header-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .header-title {
      font-size: 2.2rem;
      font-weight: 700;
      margin: 0;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .header-subtitle {
      font-size: 1.1rem;
      opacity: 0.9;
      margin: 5px 0 0 0;
    }

    .controls-section {
      background: white;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      margin-bottom: 25px;
      border: 1px solid #e9ecef;
    }

    /* ✅ NUEVOS ESTILOS PARA FILTROS */
    .filters-section {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 25px;
      border: 2px solid #dee2e6;
    }

    .filter-title {
      color: #495057;
      font-weight: 700;
      margin-bottom: 20px;
      font-size: 1.3rem;
    }

    .btn-custom {
      border-radius: 10px;
      padding: 12px 25px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      border: none;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-history {
      background: linear-gradient(135deg, #007bff, #6610f2);
      color: white;
    }

    .search-container {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 12px;
      border: 2px solid #e9ecef;
    }

    .search-input {
      border-radius: 10px;
      border: 2px solid #dee2e6;
      padding: 12px 20px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }

    .search-input:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .table-container {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid #e9ecef;
    }

    .table-modern {
      margin: 0;
      font-size: 0.95rem;
    }

    .table-modern thead {
      background: linear-gradient(135deg, #495057, #6c757d);
      color: white;
    }

    .table-modern thead th {
      border: none;
      padding: 18px 15px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-size: 0.85rem;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .table-modern tbody tr {
      transition: all 0.3s ease;
      border-bottom: 1px solid #f1f3f4;
    }

    .table-modern tbody tr:hover {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      transform: scale(1.01);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .table-modern td {
      padding: 15px;
      vertical-align: middle;
      border: none;
    }

    .status-badge {
      display: inline-block;
      white-space: nowrap;
      padding: 9px 12px;
      border-radius: 20px;
      font-weight: bold;
      color: white;
      font-size: 15px;
      max-width: 100%;
      text-align: center;
    }

    .btn-action {
      border-radius: 8px;
      padding: 8px 16px;
      font-size: 0.85rem;
      font-weight: 600;
      margin: 2px;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-action:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-approval {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
    }

    .btn-expand {
      background: linear-gradient(135deg, #6c757d, #495057);
      color: white;
      border-radius: 50%;
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .actions-container {
      display: flex;
      gap: 8px;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
    }

    .pagination {
      justify-content: center;
      margin-top: 25px;
    }

    .pagination .page-link {
      border-radius: 8px;
      margin: 0 3px;
      border: 2px solid #dee2e6;
      color: #667eea;
      font-weight: 600;
      padding: 10px 15px;
    }

    .pagination .page-item.active .page-link {
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-color: #667eea;
      color: white;
    }

    .pagination .page-link:hover {
      background: #f8f9fa;
      border-color: #667eea;
      color: #495057;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #6c757d;
    }

    .empty-state i {
      font-size: 4rem;
      margin-bottom: 20px;
      opacity: 0.5;
    }

    .loading-state {
      text-align: center;
      padding: 40px;
      color: #667eea;
    }

    .loading-state i {
      font-size: 2rem;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Estados de badges de aprobación */
    .status-badge.estado-pendiente {
      background: linear-gradient(to right, #FFB300, #FFD54F);
      color: #030303ff;
    }

    .status-badge.estado-activa {
      background: linear-gradient(to right, #1976D2, #64B5F6);
      color: white;
    }

    .status-badge.estado-cvs {
      background: linear-gradient(to right, #00897B, #4DB6AC);
      color: white;
    }

    .status-badge.estado-psico {
      background: linear-gradient(to right, #8E24AA, #BA68C8);
      color: white;
    }

    .status-badge.estado-rh {
      background: linear-gradient(to right, #00ACC1, #4DD0E1);
      color: white;
    }

    .status-badge.estado-tecnica {
      background: linear-gradient(to right, #3949AB, #7986CB);
      color: white;
    }

    .status-badge.estado-prueba {
      background: linear-gradient(to right, #E64A19, #FF8A65);
      color: white;
    }

    .status-badge.estado-poligrafo {
      background: linear-gradient(to right, #6D4C41, #A1887F);
      color: white;
    }

    .status-badge.estado-expediente {
      background: linear-gradient(to right, #512DA8, #9575CD);
      color: white;
    }

    .status-badge.estado-confirmacion {
      background: linear-gradient(to right, #546E7A, #90A4AE);
      color: white;
    }

    .status-badge.estado-contratada {
      background: linear-gradient(to right, #388E3C, #81C784);
      color: white;
    }

    /* ✅ ESTILOS PARA INFO DE FILTROS APLICADOS */
    .info-filtros {
      background: linear-gradient(135deg, #17a2b8, #20c997);
      color: white;
      padding: 15px;
      border-radius: 10px;
      margin-top: 15px;
      display: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .main-container {
        margin: 10px;
        padding: 20px;
      }

      .header-title {
        font-size: 1.8rem;
      }

      .table-container {
        overflow-x: auto;
      }

      .actions-container {
        flex-direction: column;
      }

      .btn-custom {
        width: 100%;
        margin-bottom: 10px;
      }
    }

    .swal-wide { 
      max-width: 90vw !important; 
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="main-container">
      <!-- Header Section -->
      <div class="header-section">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="header-title">
              <i class="fas fa-user-check mr-3"></i>
              Panel de Gerentes
            </h1>
            <p class="header-subtitle">Aprobación de Solicitudes de Personal</p>
          </div>
          <div class="text-right">
            <div class="badge badge-light p-3">
              <i class="fas fa-calendar-alt mr-2"></i>
              <span id="current-date"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- ✅ NUEVA SECCIÓN DE FILTROS -->
      <div class="filters-section">
        <h5 class="filter-title">
          <i class="fas fa-filter mr-2"></i>
          Filtros de Solicitudes
        </h5>
        <div class="row">
          <!-- Filtro por Estado de Aprobación -->
          <div class="col-md-3">
            <label for="filtroEstado" class="font-weight-bold">
              <i class="fas fa-check-circle mr-1"></i> Estado de Aprobación
            </label>
            <select id="filtroEstado" class="form-control">
              <option value="">Todos los Estados</option>
              <option value="Por Aprobar">Por Aprobar</option>
              <option value="Aprobado">Aprobado</option>
              <option value="No Aprobado">No Aprobado</option>
            </select>
          </div>

          <!-- Filtro por Gerente -->
          <div class="col-md-3">
            <label for="filtroGerente" class="font-weight-bold">
              <i class="fas fa-user-tie mr-1"></i> Dirigido a (Gerente)
            </label>
            <select id="filtroGerente" class="form-control">
              <option value="">Todos los Gerentes</option>
              <option value="Christian Quan">Christian Quan</option>
              <option value="Giovanni Cardoza">Giovanni Cardoza</option>
            </select>
          </div>

          <!-- Botones de Filtros -->
          <div class="col-md-3 d-flex align-items-end">
            <div class="w-100">
              <button id="btnAplicarFiltros" class="btn btn-primary btn-block">
                <i class="fas fa-search mr-1"></i> Aplicar Filtros
              </button>
            </div>
          </div>

          <!-- Botón Limpiar -->
          <div class="col-md-3 d-flex align-items-end">
            <div class="w-100">
              <button id="btnLimpiarFiltros" class="btn btn-secondary btn-block">
                <i class="fas fa-eraser mr-1"></i> Limpiar Filtros
              </button>
            </div>
          </div>
        </div>

        <!-- Información de Filtros Aplicados -->
        <div id="infoFiltros" class="info-filtros">
          <i class="fas fa-info-circle mr-2"></i>
          <span id="textoFiltros">Filtros aplicados</span>
        </div>
      </div>

      <!-- Controls Section (búsqueda) -->
      <div class="controls-section">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="search-container">
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar en solicitudes...">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-store"></i></span>
                    </div>
                    <input type="text" id="searchTienda" class="form-control" placeholder="Buscar por tienda...">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <div id="loading-indicator" class="loading-state">
          <i class="fas fa-spinner fa-spin"></i>
          <p class="mt-3">Cargando solicitudes...</p>
        </div>
        
        <table id="tblSolicitudes" class="table table-modern" style="display: none;">
          <thead>
            <tr>
              <th width="40"><i class="fas fa-expand-alt"></i></th>
              <th width="50">Tienda</th>
              <th width="140">Puesto</th>
              <th width="150">Supervisor</th>
              <th width="120">Dirigido a</th>
              <th width="120">Dirigido RRHH</th>
              <th width="120">Fecha Solicitud</th>
              <th width="140">Modificación registrada</th>
              <th width="160">Estado</th>
              <th width="160">Estado Aprobación</th>
              <th width="150">Razón</th>
              <th width="200">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

        <div id="empty-state" class="empty-state" style="display: none;">
          <i class="fas fa-inbox"></i>
          <h4>No hay solicitudes</h4>
          <p>No se encontraron solicitudes que coincidan con los criterios de búsqueda.</p>
        </div>
      </div>

      <!-- Pagination -->
      <nav>
        <ul class="pagination"></ul>
      </nav>
    </div>
  </div>

  <!-- Modal de Historial Individual -->
  <div class="modal fade" id="modalHistorialIndividual" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-history mr-2"></i>
            Historial de la Solicitud
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="contenidoHistorial">
          <div class="text-center">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="mt-2">Cargando historial...</p>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript Principal -->
  <script>
    $(document).ready(function () {
      let solicitudes = [];
      let allSolicitudes = [];
      let solicitudesFiltradas = [];
      let rowsPerPage = 10;
      let currentPage = 1;

      // Mostrar fecha actual
      $('#current-date').text(new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }));

      // ✅ FUNCIÓN PARA CARGAR SOLICITUDES (MODIFICADA PARA USAR FILTROS)
      function cargarSolicitudes() {
        $('#loading-indicator').show();
        $('#tblSolicitudes').hide();
        $('#empty-state').hide();

        console.log("🔄 Iniciando carga de solicitudes...");

        // ✅ OBTENER PARÁMETROS DE FILTROS
        const filtroEstado = $('#filtroEstado').val();
        const filtroGerente = $('#filtroGerente').val();

        let url = './GerenteTDS/crudaprobaciones.php?action=get_solicitudes_gerentes';
        
        // ✅ AGREGAR FILTROS A LA URL SI ESTÁN SELECCIONADOS
        if (filtroEstado) {
          url += `&estado_aprobacion=${encodeURIComponent(filtroEstado)}`;
        }
        if (filtroGerente) {
          url += `&dirigido_a=${encodeURIComponent(filtroGerente)}`;
        }

        console.log("📤 URL con filtros:", url);

        $.ajax({
          url: url,
          type: 'GET',
          dataType: 'json',
          success: function (data) {
            console.log("✅ Solicitudes cargadas:", data);
            allSolicitudes = data;
            solicitudesFiltradas = data;
            solicitudes = data;

            // ✅ ACTUALIZAR INFO DE FILTROS
            actualizarInfoFiltros(filtroEstado, filtroGerente, data.length);

            if (data.length === 0) {
              $('#loading-indicator').hide();
              $('#empty-state').show();
            } else {
              renderTable(solicitudes);
              setupPagination(solicitudes);
              $('#loading-indicator').hide();
              $('#tblSolicitudes').show();
            }
          },
          error: function (xhr, status, error) {
            console.error('❌ Error cargando solicitudes:', {
              status: xhr.status,
              statusText: xhr.statusText,
              responseText: xhr.responseText,
              error: error
            });
            $('#loading-indicator').hide();
            Swal.fire({
              icon: 'error',
              title: 'Error de Conexión',
              html: `
                <div style="text-align: left;">
                  <p>No se pudieron cargar las solicitudes.</p>
                  <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">
                    <small><strong>Status:</strong> ${xhr.status}</small><br>
                    <small><strong>Error:</strong> ${error}</small><br>
                    <small><strong>URL:</strong> ${url}</small>
                  </div>
                </div>
              `,
              confirmButtonText: 'Reintentar'
            }).then(() => {
              cargarSolicitudes();
            });
          }
        });
      }

      // ✅ NUEVA FUNCIÓN PARA ACTUALIZAR INFO DE FILTROS
      function actualizarInfoFiltros(estado, gerente, cantidad) {
        const infoDiv = $('#infoFiltros');
        const textoSpan = $('#textoFiltros');
        
        if (estado || gerente) {
          let texto = `Mostrando ${cantidad} solicitudes`;
          if (estado) texto += ` | Estado: ${estado}`;
          if (gerente) texto += ` | Gerente: ${gerente}`;
          
          textoSpan.text(texto);
          infoDiv.show();
        } else {
          infoDiv.hide();
        }
      }

      // ✅ EVENT LISTENERS PARA FILTROS
      $('#btnAplicarFiltros').on('click', function() {
        console.log("🔍 Aplicando filtros...");
        currentPage = 1;
        cargarSolicitudes();
      });

      $('#btnLimpiarFiltros').on('click', function() {
        console.log("🧹 Limpiando filtros...");
        $('#filtroEstado').val('');
        $('#filtroGerente').val('');
        $('#infoFiltros').hide();
        currentPage = 1;
        cargarSolicitudes();
      });

      // ✅ FUNCIÓN PARA RENDERIZAR LA TABLA
      function renderTable(data) {
        const tbody = $('#tblSolicitudes tbody');
        tbody.empty();

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = data.slice(start, end);

        pageData.forEach((item, index) => {
          const globalIndex = start + index;

          // Estados del badge original (Estado Solicitud)
          let statusClass = '';
          const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();
          if (estado.includes('pendiente')) statusClass = 'estado-pendiente';
          else if (estado.includes('activa')) statusClass = 'estado-activa';
          else if (estado.includes('cvs')) statusClass = 'estado-cvs';
          else if (estado.includes('psico') || estado.includes('psicometrica')) statusClass = 'estado-psico';
          else if (estado.includes('entrevista rh')) statusClass = 'estado-rh';
          else if (estado.includes('expediente')) statusClass = 'estado-expediente';
          else if (estado.includes('tecnica')) statusClass = 'estado-tecnica';
          else if (estado.includes('prueba')) statusClass = 'estado-prueba';
          else if (estado.includes('poligrafo')) statusClass = 'estado-poligrafo';
          else if (estado.includes('confirmacion')) statusClass = 'estado-confirmacion';
          else if (estado.includes('contratada')) statusClass = 'estado-contratada';
          else statusClass = 'estado-pendiente';

          // Estados del badge de aprobación
          let aprobacionClass = '';
          const aprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase();
          if (aprobacion.includes('por aprobar')) aprobacionClass = 'estado-pendiente';
          else if (aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no'))) aprobacionClass = 'estado-contratada';
          else if (aprobacion.includes('no aprobado')) aprobacionClass = 'estado-prueba';
          else aprobacionClass = 'estado-pendiente';

          const fechaModificacion = item.FECHA_MODIFICACION || '—';
          const estadoAprobacionMostrar = item.ESTADO_APROBACION || 'Por Aprobar';
          const dirigidoRH = item.DIRIGIDO_RH || '—';

          const row = `
            <tr data-id="${item.ID_SOLICITUD}">
              <td>
                <button class="btn btn-expand btn-ver-historial" data-id="${item.ID_SOLICITUD}" title="Ver historial">
                  <i class="fas fa-plus"></i>
                </button>
              </td>
              <td><span class="badge badge-primary">${item.NUM_TIENDA}</span></td>
              <td><strong>${item.PUESTO_SOLICITADO}</strong></td>
              <td><small class="text-muted">${item.SOLICITADO_POR}</small></td>
              <td><small>${item.DIRIGIDO_A || '—'}</small></td>
              <td><small class="text-info"><strong>${dirigidoRH}</strong></small></td>
              <td><small>${item.FECHA_SOLICITUD}</small></td>
              <td><small class="text-muted">${fechaModificacion}</small></td>
              <td><span class="status-badge ${statusClass}">${item.ESTADO_SOLICITUD}</span></td>
              <td><span class="status-badge ${aprobacionClass}">${estadoAprobacionMostrar}</span></td>
              <td><small>${item.RAZON || '—'}</small></td>
              <td>
                <div class="actions-container">
                  <button class="btn btn-action btn-approval btnProcesarSolicitud"
                          data-id="${item.ID_SOLICITUD}"
                          data-tienda="${item.NUM_TIENDA || ''}"
                          data-puesto="${item.PUESTO_SOLICITADO || ''}"
                          data-supervisor="${item.SOLICITADO_POR || ''}"
                          data-razon="${item.RAZON || ''}"
                          data-aprobacion-actual="${estadoAprobacionMostrar}"
                          title="Procesar Solicitud">
                    <i class="fas fa-gavel mr-1"></i> Procesar
                  </button>
                </div>
              </td>
            </tr>`;

          tbody.append(row);
        });
      }

      // FUNCIÓN PARA CONFIGURAR PAGINACIÓN
      function setupPagination(data) {
        const totalPages = Math.ceil(data.length / rowsPerPage);
        const pagination = $('.pagination');
        pagination.empty();

        if (totalPages <= 1) return;

        // Botón anterior
        const prevDisabled = currentPage === 1 ? 'disabled' : '';
        pagination.append(`
          <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">
              <i class="fas fa-chevron-left"></i>
            </a>
          </li>
        `);

        // Páginas
        for (let i = 1; i <= totalPages; i++) {
          const active = i === currentPage ? 'active' : '';
          pagination.append(`
            <li class="page-item ${active}">
              <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
          `);
        }

        // Botón siguiente
        const nextDisabled = currentPage === totalPages ? 'disabled' : '';
        pagination.append(`
          <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">
              <i class="fas fa-chevron-right"></i>
            </a>
          </li>
        `);
      }

      // Event listener para paginación
      $('.pagination').on('click', 'a', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage) {
          currentPage = page;
          renderTable(solicitudes);
          setupPagination(solicitudes);
        }
      });

      // ✅ FUNCIÓN DE BÚSQUEDA MEJORADA (SIN DUPLICAR FILTROS)
      function performSearch() {
        const searchText = $('#searchInput').val().toLowerCase();
        const searchTienda = $('#searchTienda').val().toLowerCase();

        // ✅ USAR solicitudesFiltradas COMO BASE (ya contiene filtros aplicados)
        let filtered = solicitudesFiltradas.filter(item => {
          const matchesSearch = !searchText || 
            (item.PUESTO_SOLICITADO || '').toLowerCase().includes(searchText) ||
            (item.SOLICITADO_POR || '').toLowerCase().includes(searchText) ||
            (item.DIRIGIDO_A || '').toLowerCase().includes(searchText) ||
            (item.DIRIGIDO_RH || '').toLowerCase().includes(searchText) ||
            (item.ESTADO_SOLICITUD || '').toLowerCase().includes(searchText) ||
            (item.ESTADO_APROBACION || '').toLowerCase().includes(searchText) ||
            (item.RAZON || '').toLowerCase().includes(searchText);

          const matchesTienda = !searchTienda || 
            (item.NUM_TIENDA || '').toString().toLowerCase().includes(searchTienda);

          return matchesSearch && matchesTienda;
        });

        solicitudes = filtered;
        currentPage = 1;
        
        if (filtered.length === 0) {
          $('#tblSolicitudes').hide();
          $('#empty-state').show();
          $('.pagination').empty();
        } else {
          $('#empty-state').hide();
          $('#tblSolicitudes').show();
          renderTable(solicitudes);
          setupPagination(solicitudes);
        }
      }

      // Event listeners para búsqueda
      $('#searchInput, #searchTienda').on('input', performSearch);

      // ✅ EVENT LISTENER PARA VER HISTORIAL
      $(document).on('click', '.btn-ver-historial', function() {
        const idSolicitud = $(this).data('id');
        console.log("📊 Ver historial de solicitud:", idSolicitud);
        
        $('#modalHistorialIndividual').modal('show');
        cargarHistorialIndividual(idSolicitud);
      });

      // ✅ FUNCIÓN PARA CARGAR HISTORIAL INDIVIDUAL
      function cargarHistorialIndividual(idSolicitud) {
        $('#contenidoHistorial').html(`
          <div class="text-center">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="mt-2">Cargando historial...</p>
          </div>
        `);

        $.ajax({
          url: './GerenteTDS/crudaprobaciones.php',
          type: 'GET',
          data: {
            action: 'get_historial_solicitud',
            id_solicitud: idSolicitud
          },
          dataType: 'json',
          success: function(response) {
            console.log("✅ Historial cargado:", response);
            
            if (response.success && response.data && response.data.length > 0) {
              let html = '<div class="timeline">';
              
              response.data.forEach((evento, index) => {
                const fecha = new Date(evento.FECHA_CAMBIO).toLocaleString('es-ES');
                const usuario = evento.USUARIO_CAMBIO || 'Sistema';
                const estadoAnterior = evento.ESTADO_ANTERIOR || 'N/A';
                const estadoNuevo = evento.ESTADO_NUEVO || 'N/A';
                const comentarios = evento.COMENTARIOS || '';

                html += `
                  <div class="timeline-item mb-4">
                    <div class="card">
                      <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                          <i class="fas fa-clock mr-2"></i>
                          ${fecha} - ${usuario}
                        </h6>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-6">
                            <strong>Estado Anterior:</strong><br>
                            <span class="badge badge-secondary">${estadoAnterior}</span>
                          </div>
                          <div class="col-md-6">
                            <strong>Estado Nuevo:</strong><br>
                            <span class="badge badge-success">${estadoNuevo}</span>
                          </div>
                        </div>
                        ${comentarios ? `
                          <div class="mt-3">
                            <strong>Comentarios:</strong><br>
                            <p class="text-muted">${comentarios}</p>
                          </div>
                        ` : ''}
                      </div>
                    </div>
                  </div>
                `;
              });
              
              html += '</div>';
              $('#contenidoHistorial').html(html);
            } else {
              $('#contenidoHistorial').html(`
                <div class="text-center text-muted">
                  <i class="fas fa-info-circle fa-3x mb-3"></i>
                  <h5>Sin historial</h5>
                  <p>No se encontró historial para esta solicitud.</p>
                </div>
              `);
            }
          },
          error: function(xhr, status, error) {
            console.error('❌ Error cargando historial:', error);
            $('#contenidoHistorial').html(`
              <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Error al cargar el historial: ${error}
              </div>
            `);
          }
        });
      }

// FUNCIÓN PARA CAMBIAR APROBACIÓN - CON DEBUG COMPLETO
$(document).on('click', '.btnProcesarSolicitud', function() {
  const id = $(this).data('id');
  const tienda = $(this).data('tienda');
  const puesto = $(this).data('puesto');
  const supervisor = $(this).data('supervisor');
  const aprobacionActual = $(this).data('aprobacion-actual') || 'Por Aprobar';

  Swal.fire({
    title: '<i class="fas fa-user-check"></i> Cambiar Estado de Aprobación',
    html: `
      <div style="text-align: left; margin-bottom: 30px;">
        <div style="background: #cce7ff; border: 1px solid #99d1ff; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
          <h6 style="margin: 0 0 15px 0; font-weight: 600; color: #0066cc;">
            <i class="fas fa-info-circle"></i> Información de la Solicitud
          </h6>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px; color: #333;">
            <div>
              <strong><i class="fas fa-hashtag"></i> ID:</strong> ${id}
            </div>
            <div>
              <strong><i class="fas fa-calendar-alt"></i> Fecha:</strong> ${new Date().toLocaleDateString('es-ES')}
            </div>
            <div style="grid-column: 1 / -1;">
              <strong><i class="fas fa-user"></i> Solicitado por:</strong> ${supervisor}
            </div>
          </div>
        </div>
        
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
          <div style="display: flex; align-items: center; color: #856404;">
            <i class="fas fa-info-circle" style="font-size: 18px; margin-right: 10px;"></i>
            <div>
              <strong>Estado Actual de Aprobación:</strong><br>
              <span style="background: #ffc107; color: #1c1f20ff; padding: 6px 12px; border-radius: 16px; font-size: 14px; font-weight: bold;">
                ${aprobacionActual}
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="form-group">
        <label style="font-weight: 700; margin-bottom: 15px; font-size: 18px; color: #333;">
          <i class="fas fa-check-double"></i> Seleccione el Nuevo Estado de Aprobación:
        </label>
        <select id="nuevaAprobacion" class="form-control" style="
          font-size: 18px; 
          padding: 15px 20px; 
          border: 2px solid #ddd; 
          border-radius: 10px;
          background: #f8f9fa;
          font-weight: 600;
          height: auto;
        ">
          <option value="" style="color: #999;">Seleccione una opción...</option>
          <option value="Aprobado" style="color: #28a745; font-weight: bold;">
            Aprobado
          </option>
          <option value="No Aprobado" style="color: #dc3545; font-weight: bold;">
            No Aprobado
          </option>
          <option value="Por Aprobar" style="color: #ffc107; font-weight: bold;">
            Por Aprobar
          </option>
        </select>
      </div>

      <!-- ✅ CAMPO CONDICIONAL PARA ASIGNAR RRHH -->
      <div id="campo-rrhh" class="form-group" style="display: none;">
        <div class="alert alert-success">
          <i class="fas fa-user-plus mr-2"></i>
          <strong>Solicitud Aprobada - Asignar a RRHH</strong>
        </div>
        <label for="swal-dirigido-rh"><strong>Asignar a:</strong></label>
        <select id="swal-dirigido-rh" class="form-control">
          <option value="">Seleccionar persona de RRHH...</option>
          <option value="Keisha Davila">Keisha Davila</option>
          <option value="Cristy Garcia">Cristy Garcia</option>
          <option value="Emma de Cea">Emma de Cea</option>
        </select>
        <small class="form-text text-muted">
          <i class="fas fa-info-circle mr-1"></i>
          Seleccione la persona de RRHH que se encargará de esta solicitud
        </small>
      </div>
    `,
    width: '700px',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-save"></i> Confirmar Cambio',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    buttonsStyling: false,
    customClass: {
      popup: 'aprobacion-modal-grande',
      confirmButton: 'btn btn-success btn-lg px-4',
      cancelButton: 'btn btn-secondary btn-lg px-4 mr-2'
    },
    preConfirm: () => {
      const nuevaAprobacion = $('#nuevaAprobacion').val();
      const dirigidoRH = $('#swal-dirigido-rh').val(); // ✅ AGREGAR ESTA LÍNEA
      
      if (!nuevaAprobacion) {
        Swal.showValidationMessage(`
          <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
            <span style="font-weight: 600;">Debe seleccionar un estado de aprobación</span>
         </div>
       `);
       return false;
     }

     // ✅ AGREGAR VALIDACIÓN PARA RRHH
     if (nuevaAprobacion === 'Aprobado' && !dirigidoRH) {
       Swal.showValidationMessage(`
         <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
           <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
           <span style="font-weight: 600;">Debe seleccionar una persona de RRHH para la solicitud aprobada</span>
        </div>
      `);
      return false;
    }
     
     return { 
       nuevaAprobacion: nuevaAprobacion, 
       dirigidoRH: dirigidoRH || null  // ✅ AGREGAR ESTA LÍNEA
     };
   },
   didOpen: () => {
     // ✅ AGREGAR LISTENER PARA MOSTRAR/OCULTAR CAMPO DE RRHH
     $('#nuevaAprobacion').on('change', function() {
       const decision = $(this).val();
       const campoRRHH = $('#campo-rrhh');
       
       if (decision === 'Aprobado') {
         campoRRHH.slideDown(300);
         $('#swal-dirigido-rh').attr('required', true);
       } else {
         campoRRHH.slideUp(300);
         $('#swal-dirigido-rh').attr('required', false).val('');
       }
     });

     // Agregar estilos personalizados
     if (!document.getElementById('aprobacion-styles-grande')) {
       const styles = document.createElement('style');
       styles.id = 'aprobacion-styles-grande';
       styles.textContent = `
         .aprobacion-modal-grande {
           border-radius: 16px !important;
           box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2) !important;
           font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
         }
         .aprobacion-modal-grande .swal2-title {
           font-size: 24px !important;
           font-weight: 700 !important;
           color: #333 !important;
           margin-bottom: 20px !important;
         }
         .aprobacion-modal-grande select:focus {
           border-color: #667eea !important;
           box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
           outline: none !important;
         }
         .aprobacion-modal-grande .btn {
           font-weight: 600 !important;
           border-radius: 10px !important;
           padding: 12px 24px !important;
           font-size: 16px !important;
           transition: all 0.3s ease !important;
           margin: 5px !important;
         }
         .aprobacion-modal-grande .btn:hover {
           transform: translateY(-2px) !important;
           box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
         }
         .aprobacion-modal-grande .swal2-actions {
           margin-top: 30px !important;
         }
       `;
       document.head.appendChild(styles);
     }
     
     // Focus en el select
     setTimeout(() => {
       $('#nuevaAprobacion').focus();
     }, 100);
   }
 }).then((result) => {
   if (result.isConfirmed) {
     console.log("📤 Enviando cambio de aprobación:", {
       id_solicitud: id,
       nueva_aprobacion: result.value.nuevaAprobacion,
       dirigido_rh: result.value.dirigidoRH  // ✅ AGREGAR ESTA LÍNEA
     });

     // Mostrar loading
     Swal.fire({
       title: '<i class="fas fa-spinner fa-spin"></i> Procesando cambio...',
       html: `
         <div style="text-align: center; padding: 20px;">
           <div style="font-size: 16px; margin-bottom: 10px;">
             Actualizando estado de aprobación
           </div>
           <div style="color: #666; font-size: 14px;">
             Por favor espera un momento...
           </div>
         </div>
       `,
       allowOutsideClick: false,
       didOpen: () => Swal.showLoading()
     });

     // ✅ MODIFICAR EL AJAX PARA INCLUIR dirigido_rh
     const dataToSend = {
       id_solicitud: id,
       nueva_aprobacion: result.value.nuevaAprobacion,
       comentario: `Cambio de aprobación a: ${result.value.nuevaAprobacion}${result.value.dirigidoRH ? ` - Asignado a: ${result.value.dirigidoRH}` : ''}`
     };

     // ✅ AGREGAR dirigido_rh SI LA SOLICITUD ES APROBADA
     if (result.value.nuevaAprobacion === 'Aprobado' && result.value.dirigidoRH) {
       dataToSend.dirigido_rh = result.value.dirigidoRH;
     }

     $.ajax({
       url: './GerenteTDS/crudaprobaciones.php?action=procesar_aprobacion_gerente',
       type: 'POST',
       dataType: 'json',
       data: dataToSend, // ✅ USAR dataToSend EN LUGAR DE OBJETO INLINE
       success: function(response) {
         console.log("✅ Respuesta exitosa del servidor:", response);
         if (response.success) {
           // ✅ MODIFICAR MENSAJE DE ÉXITO PARA INCLUIR ASIGNACIÓN
           let mensajeExito = `
             <div style="text-align: center; padding: 15px;">
               <div style="font-size: 16px; margin-bottom: 10px;">
                 El estado de aprobación ha sido actualizado correctamente
               </div>
               <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px; color: #155724;">
                 <strong><i class="fas fa-check"></i> Nuevo Estado:</strong> ${result.value.nuevaAprobacion}
               </div>`;

           if (result.value.nuevaAprobacion === 'Aprobado' && result.value.dirigidoRH) {
             mensajeExito += `
               <div style="background: #cce5ff; border: 1px solid #99d1ff; border-radius: 8px; padding: 12px; color: #004085; margin-top: 10px;">
                 <strong><i class="fas fa-user-check"></i> Asignada a:</strong> ${result.value.dirigidoRH}
               </div>`;
           }

           mensajeExito += `</div>`;
           
           Swal.fire({
             icon: 'success',
             title: '<i class="fas fa-check-circle"></i> Cambio Realizado!',
             html: mensajeExito,
             timer: 3000,
             showConfirmButton: false
           });
           cargarSolicitudes();
         } else {
           console.error("❌ Error en respuesta del servidor:", response);
           Swal.fire({
             icon: 'error',
             title: '<i class="fas fa-exclamation-circle"></i> Error',
             text: response.error || 'Error al actualizar el estado de aprobación',
             confirmButtonText: 'Entendido'
           });
         }
       },
       error: function(xhr, status, error) {
         console.error('❌ Error AJAX completo:', {
           status: xhr.status,
           statusText: xhr.statusText,
           responseText: xhr.responseText,
           error: error,
           url: './GerenteTDS/crudaprobaciones.php?action=procesar_aprobacion_gerente'
         });
         
         Swal.fire({
           icon: 'error',
           title: '<i class="fas fa-wifi"></i> Error de Conexión',
           html: `
             <div style="text-align: left;">
               <p>No se pudo conectar al servidor.</p>
               <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">
                 <small><strong>Status:</strong> ${xhr.status}</small><br>
                 <small><strong>Status Text:</strong> ${xhr.statusText}</small><br>
                 <small><strong>Error:</strong> ${error}</small><br>
                 <small><strong>URL:</strong> ./GerenteTDS/crudaprobaciones.php</small><br>
                 <small><strong>Response:</strong> ${xhr.responseText ? xhr.responseText.substring(0, 200) + '...' : 'Sin respuesta'}</small>
               </div>
             </div>
           `,
           confirmButtonText: 'Entendido'
         });
       }
     });
   }
 });
});

      // ✅ CARGAR SOLICITUDES AL INICIAR
      cargarSolicitudes();

      console.log("✅ Panel de Gerentes inicializado correctamente");
    });
  </script>
</body>
</html>