<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Supervisores - Solicitudes de Personal</title>
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

    .btn-create {
      background: linear-gradient(135deg, #28a745, #20c997);
      color: white;
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
      white-space: nowrap; /* evita el salto de línea */
      padding: 9px 12px;
      border-radius: 20px;
      font-weight: bold;
      color: white;
      font-size: 15px;
      max-width: 100%; /* permite crecer dentro del contenedor */
      text-align: center;
      margin: 0 auto;
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

    .btn-edit {
      background: linear-gradient(135deg, #17a2b8, #6610f2);
      color: white;
    }

    .btn-review {
      background: linear-gradient(135deg, #fd7e14, #e83e8c);
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

    /* Mejoras adicionales */
    .swal-wide { 
      max-width: 90vw !important; 
    }

    .swal-wide-files {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .swal-wide-files .swal2-html-container {
      padding: 0 !important;
      margin: 0 !important;
    }

    .swal-wide-files .swal2-content {
      text-align: left !important;
    }

    /* Scrollbar personalizado */
    .swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar {
      width: 8px;
    }

    .swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }

    .swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 4px;
    }

    .swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

.select-estado {
  border: none;
  border-radius: 25px;
  padding: 5px 18px;
  font-weight: bold;
  color: white !important;
  text-align: center;
  text-transform: uppercase;
  width: auto;
  max-width: 300px;
  min-width: 220px;
  appearance: none;
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.4);
  background-size: 100% 100%;
  font-size: 13px;
}

/* Aplicar mismo degradado a los badges dentro de la tabla */
/* Amarillo para Pendiente */
.status-badge.estado-pendiente {
  background: linear-gradient(to right, #FFB300, #FFD54F);
  color: #1c1c1c; /* Texto oscuro para mejor contraste */
}

/* Azul para Activa */
.status-badge.estado-activa {
  background: linear-gradient(to right, #1976D2, #64B5F6);
  color: white;
}

/* Verde azulado (Teal) para CVs */
.status-badge.estado-cvs {
  background: linear-gradient(to right, #00897B, #4DB6AC);
  color: white;
}

/* Morado para Psicométricas */
.status-badge.estado-psico {
  background: linear-gradient(to right, #8E24AA, #BA68C8);
  color: white;
}

/* Cian para RH */
.status-badge.estado-rh {
  background: linear-gradient(to right, #00ACC1, #4DD0E1);
  color: white;
}

/* Índigo para Técnica */
.status-badge.estado-tecnica {
  background: linear-gradient(to right, #3949AB, #7986CB);
  color: white;
}

/* Naranja intenso para Prueba */
.status-badge.estado-prueba {
  background: linear-gradient(to right, #E64A19, #FF8A65);
  color: white;
}

/* Café para Polígrafo */
.status-badge.estado-poligrafo {
  background: linear-gradient(to right, #6D4C41, #A1887F);
  color: white;
}

/* Violeta para Expediente */
.status-badge.estado-expediente {
  background: linear-gradient(to right, #512DA8, #9575CD);
  color: white;
}

/* Gris azulado para Confirmación */
.status-badge.estado-confirmacion {
  background: linear-gradient(to right, #546E7A, #90A4AE);
  color: white;
}

/* Verde para Contratada (éxito) */
.status-badge.estado-contratada {
  background: linear-gradient(to right, #388E3C, #81C784);
  color: white;
}




/*ESTILOS DEL COMENTARIO*/
.comentario-cell {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btnVerComentarioSuper{
    padding: 3px 8px;
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    display: inline-block;
}

/*CHAT EMERGENTE*/
.chat-burbuja {
  max-width: 75%;
  padding: 10px;
  margin-bottom: 8px;
  border-radius: 10px;
  line-height: 1.4;
  word-wrap: break-word;
  font-size: 14px;
}

.chat-burbuja.izquierda {
  background-color: #e0e0e0;
  color: #000;
  border-top-left-radius: 0;
  margin-right: auto;
}

.chat-burbuja.derecha {
  background-color: #007bff;
  color: #fff;
  border-top-right-radius: 0;
  margin-left: auto;
}

/* CSS mejorado para el badge de notificaciones */
.badge-container {
  position: relative;
  display: inline-block;
}

.notification-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background-color: red;
  color: white;
  font-size: 10px;
  padding: 3px 6px;
  border-radius: 50%;
  font-weight: bold;
  z-index: 1000;
  box-shadow: 0 0 2px rgba(0, 0, 0, 0.3);
}

.notification-badge.wide {
  padding: 3px 8px;
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
              <i class="fas fa-users-cog mr-3"></i>
              Panel de Supervisores
            </h1>
            <p class="header-subtitle">Gestión de Solicitudes de Personal</p>
          </div>
          <div class="text-right">
            <div class="badge badge-light p-3">
              <i class="fas fa-calendar-alt mr-2"></i>
              <span id="current-date"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Controls Section -->
      <div class="controls-section">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="d-flex gap-3">
              <button class="btn btn-custom btn-create btnCrearsolicitud">
                <i class="fas fa-plus-circle mr-2"></i>
                Nueva Solicitud
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <div class="search-container">
              <div class="row">
                <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar en solicitudes...">
                </div>
                <div class="input-group">
                <span class="input-group-text"><i class="fas fa-store"></i></span>
                <input type="text" id="searchTienda" class="form-control" placeholder="Buscar por tienda...">
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
              <th width="30"><i class="fas fa-expand-alt"></i></th>
              <th width="50">Tienda</th>
              <th width="140">Puesto</th>
              <th width="150">Supervisor</th>
              <th width="120">Dirigido a</th>
              <th width="120">Fecha Solicitud</th>
              <th width="140">Última Edición</th>
              <th width="180">Estado</th>
              <th width="150">Razón</th>
              <th width="50">Comentario</th>
              <th width="300">Acciones</th>
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

  <!-- Modal de Historial de Modificaciones -->
  <div class="modal fade" id="modalHistorialIndividual" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-history mr-2"></i>
            Historial de Modificaciones
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
          <!--<a id="btnPdfIndividual" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Generar PDF
          </a>-->
          <button class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

<!--MODAL DE SELECCION DE CVS-->
<div class="modal fade" id="modalResumenCVs" tabindex="-1" role="dialog" aria-labelledby="resumenCVsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5 class="modal-title">Resumen de CVs Seleccionados</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="resumenCVsContenido">
          <p class="text-muted">Cargando selección...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Pruebas -->
<div class="modal fade" id="modalVerPruebas" tabindex="-1" role="dialog" aria-labelledby="modalPruebasTitulo" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title" id="modalPruebasTitulo">Pruebas Adjuntas</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalPruebasContenido">
        <p>Cargando pruebas adjuntas...</p>
      </div>
    </div>
  </div>
</div>





  <!-- JavaScript Principal -->
  <script>
    $(document).ready(function () {

      let archivosOriginales =[];
      let archivosSeleccionados = new Set();
      let solicitudActual =null;
      let solicitudes = [];
      let idSolicitudActual = null;
      let rowsPerPage = 10;
      let currentPage = 1;
      let modalAbierto = false;

      // Mostrar fecha actual
      $('#current-date').text(new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }));

      // FUNCIÓN PARA CARGAR SOLICITUDES
      function cargarSolicitudes() {
        $('#loading-indicator').show();
        $('#tblSolicitudes').hide();
        $('#empty-state').hide();

        $.ajax({
          url: './supervision/crudsolicitudes.php?action=get_solicitudes',
          type: 'GET',
          dataType: 'json',
          success: function (data) {
            solicitudes = data.success ? data.data : data;
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
            console.error('Error cargando solicitudes:', error);
            $('#loading-indicator').hide();
            Swal.fire({
              icon: 'error',
              title: 'Error de Conexión',
              text: 'No se pudieron cargar las solicitudes. Verifica tu conexión.',
              confirmButtonText: 'Reintentar'
            }).then(() => {
              cargarSolicitudes();
            });
          }
        });
      }
//RENDERIZAR TABLA
function renderTable(data) {
  const tbody = $('#tblSolicitudes tbody');
  tbody.empty();

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const pageData = data.slice(start, end);

  pageData.forEach((item, index) => {
    const globalIndex = start + index;

    let statusClass = '';
    const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();

    if (estado.includes('pendiente')) statusClass = 'estado-pendiente';
    else if (estado.includes('activa')) statusClass = 'estado-activa';
    else if (estado.includes('cvs')) statusClass = 'estado-cvs';
    else if (estado.includes('psico')) statusClass = 'estado-psico';
    else if (estado.includes('entrevista rh')) statusClass = 'estado-rh';
    else if (estado.includes('tecnica')) statusClass = 'estado-tecnica';
    else if (estado.includes('prueba')) statusClass = 'estado-prueba';
    else if (estado.includes('poligrafo')) statusClass = 'estado-poligrafo';
    else if (estado.includes('expediente')) statusClass = 'estado-expediente';
    else if (estado.includes('confirmacion')) statusClass = 'estado-confirmacion';
    else if (estado.includes('contratada')) statusClass = 'estado-contratada';
    else statusClass = 'estado-pendiente'; // por defecto

    const fechaModificacion = item.FECHA_MODIFICACION || '—';
    const comentario = item.COMENTARIO_NUEVO || '-';
    const idHistorico = item.ID_HISTORICO;
    const noLeidos = parseInt(item.NO_LEIDOS) || 0;
    //const noLeidos = 5; // prueba directa
    console.log('ID:', idHistorico, 'Comentario:', comentario, 'NO_LEIDOS:', item.NO_LEIDOS);
    const comentarioMostrar = comentario !== '-' && idHistorico
  ? `<div class="badge-container">
        <button class="btn btn-sm btn-info btnVerComentarioSuper"
                data-id="${idHistorico}"
                title="Ver comentario">
            <i class="fas fa-comment"></i> Ver
        </button>
        ${noLeidos > 0 ? `<span class="notification-badge ${noLeidos > 9 ? 'wide' : ''}">${noLeidos}</span>` : ''}
    </div>`
  : '<span class="text-muted">—</span>';

    
    // Declarar variable acciones por cada fila
    let acciones = '';

    // Mostrar solo "Ver resumen" si hay selección
if (estado.toLowerCase().includes('cvs')) {
  if (parseInt(item.TIENE_SELECCION) === 1) {
    acciones += `
      <button class="btn btn-info btn-sm btnVerResumen" data-id="${item.ID_SOLICITUD}">
          <i class="fas fa-eye"></i> Ver resumen
      </button>`;
  } else {
    acciones += `
      <button class="btn btn-primary btn-sm btnVerArchivos" data-id="${item.ID_SOLICITUD}">
          <i class="fas fa-folder-open"></i> Archivos
      </button>`;
  }
}

if (estado.includes('psico')) {
  acciones += `
    <button class="btn btn-secondary btn-sm btnVerPruebas"
            data-id="${item.ID_SOLICITUD}"
            data-tipo="PSICOMETRICA">
      <i class="fas fa-brain"></i> Ver Psicométrica
    </button>`;
} else if (estado.includes('poligrafo')) {
  acciones += `
    <button class="btn btn-dark btn-sm btnVerPruebas"
            data-id="${item.ID_SOLICITUD}"
            data-tipo="POLIGRAFO">
      <i class="fas fa-fingerprint"></i> Ver Polígrafo
    </button>`;
}


    const row = `
      <tr data-id="${item.ID_SOLICITUD}">
        <td>
          <button class="btn btn-expand btn-ver-historial-modificaciones" data-id="${item.ID_SOLICITUD}" title="Ver historial">
            <i class="fas fa-plus"></i>
          </button>
        </td>
        <td><span class="badge badge-primary">${item.NUM_TIENDA}</span></td>
        <td><strong>${item.PUESTO_SOLICITADO}</strong></td>
        <td><small class="text-muted">${item.SOLICITADO_POR}</small></td>
        <td><small>${item.DIRIGIDO_A || '—'}</small></td>
        <td><small>${item.FECHA_SOLICITUD}</small></td>
        <td><small class="text-muted">${fechaModificacion}</small></td>
        <td>
          <span class="status-badge ${statusClass}" title="${item.ULTIMO_COMENTARIO || 'Sin comentario'}">
            ${item.ESTADO_SOLICITUD}
          </span>
        </td>
        <td><small>${item.RAZON || '—'}</small></td>
        <td class="comentario-cell">${comentarioMostrar}</td>
        <td>
          <div class="actions-container">
            <button class="btn btn-action btn-edit btnEditarSolicitud"
                    data-index="${globalIndex}" title="Editar solicitud">
              <i class="fas fa-edit"></i> Editar
            </button>
            ${acciones}
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

        // Event listeners para paginación
        $('.pagination .page-link').click(function (e) {
          e.preventDefault();
          const page = parseInt($(this).data('page'));
          if (page && page !== currentPage && page >= 1 && page <= totalPages) {
            currentPage = page;
            renderTable(solicitudes);
            setupPagination(solicitudes);
          }
        });
      }

      // FILTROS DE BÚSQUEDA
$('#searchInput, #searchTienda').on('input', function () {
    const searchValueGeneral = $('#searchInput').val().toLowerCase();
    const searchValueTienda = $('#searchTienda').val().toLowerCase();

    const filteredData = solicitudes.filter(item => {
        const matchGeneral = !searchValueGeneral || Object.values(item).some(value =>
            value && value.toString().toLowerCase().includes(searchValueGeneral)
        );

        const matchTienda = !searchValueTienda || 
            (item.NUM_TIENDA && item.NUM_TIENDA.toString().toLowerCase().includes(searchValueTienda));

        return matchGeneral && matchTienda;
    });

    renderTable(filteredData);
    setupPagination(filteredData);
});

//FUNCION PARA VER LOS ARCHIVOS PSICO Y POLIGRAFO
$(document).off('click', '.btnVerPruebas').on('click', '.btnVerPruebas', function () {
    const idSolicitud = $(this).data('id');
    const tipoArchivo = $(this).data('tipo'); // debe ser 'PSICOMETRICA' o 'POLIGRAFO'

    $('#modalPruebasContenido').html('<p>Cargando archivos...</p>');
    $('#modalVerPruebas').modal('show');

    $.ajax({
        url: './supervision/crudsolicitudes.php?action=ver_pruebas_adjuntas',
        method: 'POST',
        data: {
            id_solicitud: idSolicitud,
            tipo: tipoArchivo
        },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.archivos.length > 0) {
                const archivo = response.archivos[0]; // Solo el más reciente
                const nombreCompleto = archivo.NOMBRE_ARCHIVO;
                const nombreLimpio = nombreCompleto.split('/').pop();
                const ext = nombreLimpio.toLowerCase().split('.').pop();
                const icon = ext === 'pdf' ? 'fa-file-pdf' :
                             ext === 'doc' || ext === 'docx' ? 'fa-file-word' : 'fa-file';

                let contenido = `
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${nombreLimpio}</span>
                            <div>
                                <a href="${nombreCompleto}" target="_blank" class="btn btn-sm btn-outline-primary mr-2">
                                    <i class="fas ${icon}"></i> Ver
                                </a>
                                <a href="${nombreCompleto}" download class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                            </div>
                        </li>
                    </ul>`;
                $('#modalPruebasContenido').html(contenido);
            } else {
                $('#modalPruebasContenido').html('<div class="alert alert-warning">No hay archivos disponibles.</div>');
            }
        },
        error: function () {
            $('#modalPruebasContenido').html('<div class="alert alert-danger">Error al cargar los archivos.</div>');
        }
    });
});


//FUNCION PARA VER RESUMEN DE SELECCIONES #
$(document).off('click', '.btnVerResumen').on('click', '.btnVerResumen', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation(); // Evitar múltiples ejecuciones
    const idSolicitud = $(this).data('id');
    console.log("Iniciando solicitud para ID:", idSolicitud);
    
    // Opción 1: Envío estándar
    const requestData = {
      action: 'ver_resumen_cvs',
        id_solicitud: idSolicitud
    };
    
    // Opción 2: Envío como JSON
    const jsonData = JSON.stringify(requestData);
    
    // Mostrar loading con estilo mejorado
    const swalInstance = Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando...',
        html: 'Obteniendo información de documentos seleccionados',
        showConfirmButton: false,
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeInDown faster'
        },
        didOpen: () => Swal.showLoading()
    });
    
    // Intento 1: Envío tradicional (manteniendo la lógica original)
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=ver_resumen_cvs',
        type: 'POST',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            swalInstance.close();
            handleResponse(response);
        },
        error: function(xhr) {
            // Si falla, intentar con envío como JSON (manteniendo la lógica original)
            console.warn("Primer intento falló, probando con JSON...");
            sendAsJson();
        }
    });
    
    function sendAsJson() {
        $.ajax({
            url: './supervision/crudsolicitudes.php?action=ver_resumen_cvs',
            type: 'POST',
            data: jsonData,
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                swalInstance.close();
                handleResponse(response);
            },
            error: function(xhr) {
                swalInstance.close();
                console.error("Error completo:", {
                    status: xhr.status,
                    response: xhr.responseText,
                    headers: xhr.getAllResponseHeaders()
                });
                Swal.fire({
                    title: '<i class="fas fa-exclamation-circle text-danger"></i> Error',
                    html: `<div class="text-left">
                             <p>No se pudo conectar al servidor.</p>
                             <div class="mt-2 p-2 bg-light rounded">
                               <small>Estado: ${xhr.status}</small>
                               <pre class="mt-2" style="max-height: 150px; overflow-y: auto;">${xhr.responseText || 'Sin respuesta'}</pre>
                             </div>
                           </div>`,
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            }
        });
    }
    
    function handleResponse(response) {
        console.log("Respuesta completa:", response);
        if (response.success) {
            // Mostrar resultados en el modal con diseño mejorado
            if (response.archivos && response.archivos.length > 0) {
                let html = '<div class="file-list p-2">';
                response.archivos.forEach(file => {
                    // Determinar icono según tipo de archivo
                    let fileIcon = 'file';
                    let fileColor = 'secondary';
                    
                    if (file.TIPO === 'PDF' || file.EXTENSION === 'pdf') {
                        fileIcon = 'file-pdf';
                        fileColor = 'danger';
                    } else if (['DOC', 'DOCX'].includes(file.TIPO) || ['doc', 'docx'].includes(file.EXTENSION)) {
                        fileIcon = 'file-word';
                        fileColor = 'primary';
                    } else if (['XLS', 'XLSX'].includes(file.TIPO) || ['xls', 'xlsx'].includes(file.EXTENSION)) {
                        fileIcon = 'file-excel';
                        fileColor = 'success';
                    } else if (['JPG', 'JPEG', 'PNG'].includes(file.TIPO) || ['jpg', 'jpeg', 'png'].includes(file.EXTENSION)) {
                        fileIcon = 'file-image';
                        fileColor = 'info';
                    }
                    
                    html += `
                        <div class="file-item d-flex align-items-center p-2 mb-2 border rounded">
                            <div class="file-icon mr-3">
                                <i class="fas fa-${fileIcon} fa-2x text-${fileColor}"></i>
                            </div>
                            <div class="file-info flex-grow-1">
                                <div class="font-weight-bold">${file.NOMBRE_ARCHIVO}</div>
                                <div class="small text-muted">
                                    <span class="mr-2"><i class="fas fa-calendar-alt mr-1"></i>${file.FECHA || 'N/A'}</span>
                                    <span class="badge badge-${fileColor}">${file.TIPO}</span>
                                </div>
                            </div>
                            <div class="file-actions">
                                <button class="btn btn-sm btn-primary btn-ver-documento" data-ruta="${file.RUTA || ''}">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </button>
                                <button class="btn btn-sm btn-success ml-1 btn-descargar-documento" data-ruta="${file.RUTA || ''}" data-nombre="${file.NOMBRE_ARCHIVO}">
                                    <i class="fas fa-download mr-1"></i> Descargar
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $('#resumenCVsContenido').html(html);
                
                // Agregar estilos si no existen
                if (!document.getElementById('file-list-styles')) {
                    const style = document.createElement('style');
                    style.id = 'file-list-styles';
                    style.innerHTML = `
                        .file-list {
                            max-height: 400px;
                            overflow-y: auto;
                        }
                        .file-item {
                            transition: all 0.2s;
                        }
                        .file-item:hover {
                            background-color: #f8f9fa;
                            transform: translateY(-2px);
                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                        }
                        .file-icon {
                            width: 40px;
                            text-align: center;
                        }
                    `;
                    document.head.appendChild(style);
                }
                
                // Configurar eventos para los botones
                $('.btn-ver-documento').on('click', function() {
                    const ruta = $(this).data('ruta');
                    if (ruta) {
                        window.open(ruta, '_blank');
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se encontró la ruta del documento',
                            icon: 'error',
                            timer: 2000
                        });
                    }
                });
                
                $('.btn-descargar-documento').on('click', function() {
                    const ruta = $(this).data('ruta');
                    const nombre = $(this).data('nombre');
                    if (ruta) {
                        const link = document.createElement('a');
                        link.href = ruta;
                        link.download = nombre;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        // Notificación de descarga
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        Toast.fire({
                            icon: 'success',
                            title: `Descargando: ${nombre}`
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se encontró la ruta del documento',
                            icon: 'error',
                            timer: 2000
                        });
                    }
                });
            } else {
                $('#resumenCVsContenido').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No se encontraron documentos</h5>
                        <p class="text-muted">No hay documentos seleccionados para esta solicitud</p>
                    </div>
                `);
            }
            $('#modalResumenCVs').modal('show');
        } else {
            Swal.fire({
                title: '<i class="fas fa-exclamation-triangle text-warning"></i> Error',
                text: response.error || 'Error desconocido',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3085d6'
            });
        }
    }
});


//FUNCIÓN PARA MOSTRAR COMENTARIO DE RRHH
    $(document).off('click', '.btnVerComentarioSuper').on('click', '.btnVerComentarioSuper', function (e) {
        e.preventDefault();
        e.stopPropagation();
        modalAbierto = true;
        const idHistorico = $(this).data('id');
        console.log("ID Histórico para chat:", idHistorico);
        
        if (!idHistorico) {
            console.error("No se encontró ID histórico");
            Swal.fire('Error', 'No se encontró el ID del histórico', 'error');
            return;
        }

        function mostrarChat(mensajes) {
            console.log("Mostrando chat con", mensajes.length, "mensajes");
            let chatHtml = `
                <div id="chat-contenedor" style="
                    max-height: 400px;
                    overflow-y: auto;
                    padding: 20px;
                    background: #ffffff;
                    margin-bottom: 20px;
                ">
            `;

            if (mensajes && mensajes.length > 0) {
                mensajes.forEach(msg => {
                    const rol = msg.rol ? msg.rol.toLowerCase() : '';
                    const esSupervisor = rol.includes('supervisor');
                    const remitente = esSupervisor ? 'SUPERVISOR' : 'RRHH';

                    if (esSupervisor) {
                        // Mensaje del supervisor (derecha, morado)
                        chatHtml += `
                            <div style="
                                display: flex;
                                justify-content: flex-end;
                                margin-bottom: 15px;
                            ">
                                <div style="
                                    max-width: 70%;
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    color: white;
                                    padding: 12px 16px;
                                    border-radius: 18px 18px 4px 18px;
                                    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                                ">
                                    <div style="
                                        font-weight: 600;
                                        font-size: 11px;
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                        margin-bottom: 4px;
                                        opacity: 0.9;
                                    ">${remitente}</div>
                                    <div style="
                                        font-size: 14px;
                                        line-height: 1.4;
                                        margin-bottom: 6px;
                                    ">${msg.mensaje}</div>
                                    <div style="
                                        font-size: 11px;
                                        opacity: 0.8;
                                        text-align: right;
                                    ">${msg.fecha}</div>
                                </div>
                            </div>
                        `;
                    } else {
                        // Mensaje de RRHH (izquierda, gris)
                        chatHtml += `
                            <div style="
                                display: flex;
                                justify-content: flex-start;
                                margin-bottom: 15px;
                            ">
                                <div style="
                                    max-width: 70%;
                                    background: #f1f3f4;
                                    color: #333;
                                    padding: 12px 16px;
                                    border-radius: 18px 18px 18px 4px;
                                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                                ">
                                    <div style="
                                        font-weight: 600;
                                        font-size: 11px;
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                        margin-bottom: 4px;
                                        color: #666;
                                    ">${remitente}</div>
                                    <div style="
                                        font-size: 14px;
                                        line-height: 1.4;
                                        margin-bottom: 6px;
                                    ">${msg.mensaje}</div>
                                    <div style="
                                        font-size: 11px;
                                        color: #888;
                                        text-align: left;
                                    ">${msg.fecha}</div>
                                </div>
                            </div>
                        `;
                    }
                });
            } else {
                chatHtml += `
                    <div style="
                        text-align: center;
                        padding: 20px;
                        color: #999;
                    ">
                        <i class="fas fa-comment-slash" style="font-size: 48px; margin-bottom: 16px;"></i>
                        <p style="font-size: 16px; margin: 0;">No hay mensajes en este chat</p>
                    </div>
                `;
            }

            chatHtml += `</div>`;
            chatHtml += `
                <div style="
                    border-top: 1px solid #e0e0e0;
                    padding-top: 20px;
                ">
                    <textarea id="nuevoMensaje" 
                        placeholder="Escribe tu respuesta..." 
                        style="
                            width: 100%;
                            min-height: 80px;
                            padding: 12px 16px;
                            border: 1px solid #ddd;
                            border-radius: 12px;
                            font-size: 14px;
                            font-family: inherit;
                            resize: vertical;
                            outline: none;
                            transition: border-color 0.2s;
                        "
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#ddd'"
                    ></textarea>
                </div>
            `;

            // Obtener nombre del asesor de rh desde la fila de la tabla
            const filaActual = $(`button[data-id="${idHistorico}"]`).closest('tr');
            const nombreRRHH = filaActual.find('td:nth-child(5)').text().trim() || 'RRHH';

            
            Swal.fire({
                title: `<i class="fas fa-comments"></i> ${nombreRRHH}`,
                html: chatHtml,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-paper-plane"></i> Enviar',
                cancelButtonText: 'Cerrar',
                focusConfirm: false,
                allowOutsideClick: false,
                customClass: {
                    popup: 'chat-modal-popup',
                    title: 'chat-modal-title',
                    confirmButton: 'chat-send-button',
                    cancelButton: 'chat-cancel-button'
                },
                preConfirm: () => {
                    const mensaje = $('#nuevoMensaje').val().trim();
                    if (!mensaje) {
                        Swal.showValidationMessage('Debes escribir un mensaje');
                        return false;
                    }
                    return mensaje;
                },
                didOpen: () => {
                    const container = document.getElementById('chat-contenedor');
                    if (container) container.scrollTop = container.scrollHeight;
                    
                    // Agregar estilos CSS dinámicamente
                    if (!document.getElementById('chat-styles')) {
                        const styles = document.createElement('style');
                        styles.id = 'chat-styles';
                        styles.textContent = `
                            .chat-modal-popup {
                                border-radius: 16px !important;
                                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
                            }
                            .chat-modal-title {
                                font-size: 18px !important;
                                font-weight: 600 !important;
                                color: #333 !important;
                            }
                            .chat-send-button {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                                border: none !important;
                                border-radius: 8px !important;
                                padding: 10px 20px !important;
                                font-weight: 600 !important;
                                transition: transform 0.2s !important;
                            }
                            .chat-send-button:hover {
                                transform: translateY(-1px) !important;
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
                            }
                            .chat-cancel-button {
                                background: #f5f5f5 !important;
                                color: #666 !important;
                                border: none !important;
                                border-radius: 8px !important;
                                padding: 10px 20px !important;
                                font-weight: 600 !important;
                            }
                        `;
                        document.head.appendChild(styles);
                    }
                }
            }).then((result) => {
                
                if (result.isConfirmed) {
                    const nuevoMensaje = result.value;

                    Swal.fire({
                        title: 'Enviando mensaje...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // CORREGIDO - Usar nombres fijos según el contexto
                    const nombreSupervisor = filaActual.find('td:nth-child(4)').text().trim() || 'Supervisor'; 
                    const nombreRRHH = 'RRHH';
                    const esSupervisor = true; // Siempre verdadero porque estás en solicitudesv.php
                    const remitente = nombreSupervisor;


                    $.ajax({
                        url: './supervision/crudsolicitudes.php?action=guardar_respuesta_chat',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                        id_historico: idHistorico,
                        mensaje: nuevoMensaje,
                        rol: 'SUPERVISOR',
                        remitente: remitente //NUEVO
                        },
                        success: function (response) {
                            console.log("Respuesta del servidor:", response);
                            if (response && response.success) {
                                $.ajax({
                                    url: './supervision/crudsolicitudes.php?action=marcar_mensajes_leidos_supervisor',
                                    type: 'POST',
                                    data: { id_historico: idHistorico }
                                });
                                cargarMensajesChat(idHistorico);
                                // En lugar de cargarSolicitudes(); en la línea 508, pon:
                                actualizarBadgesSilenciosamente();
                            } else {
                                Swal.fire('Error', response?.error || 'Error al enviar el mensaje', 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error AJAX:', xhr.responseText);
                            Swal.fire('Error', 'Error de conexión: ' + error, 'error');
                        }
                    });
                }
            });
        }

        function cargarMensajesChat(idHistorico) {
            console.log("Cargando mensajes para ID:", idHistorico);

            Swal.fire({
                title: 'Cargando comentario...',
                text: 'Por favor espera un momento.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: './supervision/crudsolicitudes.php?action=get_comentarios_chat',
                type: 'POST',
                dataType: 'json',
                data: { id_historico: idHistorico },
                success: function (response) {
                    Swal.close(); // Cierra el mensaje de carga
                    console.log('Respuesta del servidor:', response);
                    if (response && response.success) {
                        mostrarChat(response.mensajes);
                        // En lugar de cargarSolicitudes(); en la línea 508, pon:
                       //actualizarBadgesSilenciosamente();
                    } else {
                        console.error("Error en respuesta:", response?.error);
                        Swal.fire('Error', response?.error || 'Error al cargar mensajes', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close(); // También cerrar si falla
                    console.error('Error al cargar chat:', xhr.responseText);
                    Swal.fire('Error', 'Error al cargar el chat: ' + error, 'error');
                }
            });
        }

        cargarMensajesChat(idHistorico);
        actualizarBadgesSilenciosamente();
        modalAbierto = false;
        });

    // FUNCIÓN PARA ACTUALIZAR SOLO LOS BADGES SIN RUIDO VISUAL
function actualizarBadgesSilenciosamente() {
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=get_solicitudes',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            const nuevasSolicitudes = data.success ? data.data : data;
            
            // Actualizar solo los badges sin recargar la tabla
            nuevasSolicitudes.forEach(function(solicitud) {
                const fila = $(`tr[data-id="${solicitud.ID_SOLICITUD}"]`);
                if (fila.length > 0) {
                    const noLeidos = parseInt(solicitud.NO_LEIDOS) || 0;
                    const badge = fila.find('.notification-badge');
                    
                    if (noLeidos > 0) {
                        // Actualizar badge existente o crear uno nuevo
                        if (badge.length > 0) {
                            badge.text(noLeidos);
                        } else {
                            const btnComentario = fila.find('.btnVerComentarioSuper').parent();
                            btnComentario.append(`<span class="notification-badge">${noLeidos}</span>`);
                        }
                    } else {
                        // Remover badge si no hay mensajes no leídos
                        badge.fadeOut(300, function() { $(this).remove(); });
                    }
                }
            });
        }
    });
}

      // FUNCIÓN PARA REVISAR CVs Y VER ARCHIVOS

let modalCVAbierto = false;

$(document).off('click', '.btnVerArchivos').on('click', '.btnVerArchivos', async function (e) {
    e.preventDefault();
    e.stopImmediatePropagation(); // ← CAMBIAR stopPropagation por stopImmediatePropagation
    
    // Verificación profesional
    if (modalCVAbierto) {
        console.warn("🔁 El modal de selección ya está abierto. Acción cancelada para evitar rebote.");
        return;
    }

    if (Swal.isVisible()) {
        console.warn("⚠️ Otro modal está activo. Cerrando primero para evitar conflicto visual.");
        Swal.close();
        await new Promise(resolve => setTimeout(resolve, 300));
    }

    const idSolicitud = $(this).data('id');
    if (!idSolicitud) {
        Swal.fire('Error', 'ID de solicitud no encontrado', 'error');
        return;
    }

    solicitudActual = idSolicitud;
    idSolicitudActual = idSolicitud;

    archivosOriginales = [];
    archivosSeleccionados.clear();

    // Marcar que el modal está abierto
    modalCVAbierto = true;

    // Iniciar carga
    cargarArchivosConSeleccion(idSolicitud);
});

function cargarArchivosConSeleccion(idSolicitud) {
    Swal.fire({
        title: 'Cargando archivos...',
        text: 'Obteniendo archivos para revisión y selección',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=get_archivos',
        type: 'GET',
        dataType: 'json',
        data: { id: idSolicitud },
        success: function(response) {
            console.log("📄 Archivos recibidos:", response);
            
            if (response.success && response.archivos) {
                archivosOriginales = response.archivos.map(a => ({
                    ...a,
                    ID_ARCHIVO: String(a.ID_ARCHIVO)
                }));

                archivosSeleccionados.clear();
                console.log("🧹 Variables limpiadas después de cargar archivos");

                mostrarModalSeleccionArchivos(archivosOriginales, idSolicitud);
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin archivos disponibles',
                    text: 'No se encontraron archivos para esta solicitud',
                    confirmButtonText: 'Entendido',
                    willClose: () => {
                        modalCVAbierto = false; // ← RESETEAR FLAG
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar archivos:', xhr.responseText);
            Swal.fire({
                title: 'Error',
                text: 'Error al cargar archivos: ' + error,
                icon: 'error',
                willClose: () => {
                    modalCVAbierto = false; // ← RESETEAR FLAG
                }
            });
        }
    });
}

function mostrarModalSeleccionArchivos(archivos, idSolicitud) {
    idSolicitudActual = idSolicitud;
    Swal.close();
        let modalHtml = `
            <div id="archivos-container" style="max-height: 500px; overflow-y: auto;">
                <!-- Instrucciones -->
                <div style="margin-bottom: 20px; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white;">
                    <h6 style="margin: 0 0 8px 0; font-weight: 600; display: flex; align-items: center;">
                        <i class="fas fa-info-circle" style="margin-right: 8px;"></i> 
                        Instrucciones para Supervisores
                    </h6>
                    <p style="margin: 0; font-size: 14px; opacity: 0.9;">
                        Selecciona los CVs que consideres aptos para avanzar a la siguiente fase del proceso de selección. 
                        Los archivos seleccionados serán enviados a RRHH para continuar con el proceso.
                    </p>
                </div>
        `;
        
        if (archivos.length > 0) {
            archivos.forEach((archivo, index) => {
                const esCV = archivo.NOMBRE_SOLO.toLowerCase().includes('cv') || 
                           archivo.NOMBRE_SOLO.toLowerCase().includes('curriculum') ||
                           archivo.EXTENSION === 'pdf';
                
                modalHtml += `
                    <div class="archivo-item" data-archivo-id="${archivo.ID_ARCHIVO}" style="
                        display: flex;
                        align-items: center;
                        padding: 15px;
                        margin-bottom: 12px;
                        background: white;
                        border: 2px solid #e9ecef;
                        border-radius: 12px;
                        transition: all 0.3s ease;
                        cursor: pointer;
                    " onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 4px 12px rgba(102,126,234,0.15)'" 
                       onmouseout="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'">
                        
                        <!-- Checkbox de selección -->
                        <div style="margin-right: 15px;">
                            <input type="checkbox" 
                                   class="cv-checkbox" 
                                   data-archivo-id="${archivo.ID_ARCHIVO}"
                                   style="
                                       width: 20px; 
                                       height: 20px; 
                                       cursor: pointer;
                                       accent-color: #667eea;
                                   ">
                        </div>
                        
                        <!-- Icono del archivo -->
                        <div style="margin-right: 15px;">
                            <i class="fas fa-file-pdf" style="
                                font-size: 28px; 
                                color: #dc3545;
                            "></i>
                        </div>
                        
                        <!-- Información del archivo -->
                        <div style="flex: 1; min-width: 0;">
                            <div style="
                                font-weight: 600; 
                                color: #333; 
                                margin-bottom: 6px;
                                word-break: break-word;
                            ">${archivo.NOMBRE_SOLO}</div>
                            <div style="
                                font-size: 12px; 
                                color: #666;
                                display: flex;
                                gap: 15px;
                                flex-wrap: wrap;
                            ">
                                <span><i class="fas fa-calendar"></i> ${archivo.FECHA_SUBIDA}</span>
                                <span><i class="fas fa-weight-hanging"></i> ${archivo.TAMAÑO_MB} MB</span>
                                ${esCV ? '<span style="color: #28a745; font-weight: 600;"><i class="fas fa-user-tie"></i> CV Detectado</span>' : ''}
                            </div>
                        </div>
                        
                                  <!-- Botones de acción -->
                                  <div style="display: flex; gap: 8px; margin-left: 15px;">
                                      <button type="button" 
                                              class="btn-ver-archivo" 
                                              data-archivo="${archivo.RUTA_RELATIVA}"
                                              style="
                                                  background: #007bff;
                                                  color: white;
                                                  border: none;
                                                  padding: 10px 15px;
                                                  border-radius: 8px;
                                                  font-size: 13px;
                                                  font-weight: 600;
                                                  cursor: pointer;
                                                  transition: all 0.2s;
                                              ">
                                          <i class="fas fa-eye"></i> Ver
                                      </button>
                                      <button type="button" 
                                              class="btn-descargar-archivo" 
                                              data-archivo="${archivo.RUTA_RELATIVA}"
                                              style="
                                                  background: #28a745;
                                                  color: white;
                                                  border: none;
                                                  padding: 10px 15px;
                                                  border-radius: 8px;
                                                  font-size: 13px;
                                                  font-weight: 600;
                                                  cursor: pointer;
                                                  transition: all 0.2s;
                                              ">
                                          <i class="fas fa-download"></i> Descargar
                                      </button>
                                  </div>

                    </div>
                `;
            });
        } else {
            modalHtml += `
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-folder-open" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <p style="font-size: 16px; margin: 0;">No hay archivos disponibles</p>
                </div>
            `;
        }
        
        modalHtml += `</div>`;
        
        // Controles de selección
        modalHtml += `
            <div id="controles-seleccion" style="
                margin-top: 20px; 
                padding-top: 20px; 
                border-top: 2px solid #e9ecef;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 15px;
            ">
                <!-- Información y controles básicos -->
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <span id="contador-seleccionados" style="
                        font-weight: 600; 
                        color: #667eea;
                        font-size: 16px;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    ">
                        <i class="fas fa-check-circle"></i>
                        <span id="numero-seleccionados">0</span> CVs seleccionados
                    </span>
                    
                    <button type="button" 
                            id="btn-seleccionar-todos" 
                            style="
                                background: #6c757d;
                                color: white;
                                border: none;
                                padding: 10px 16px;
                                border-radius: 8px;
                                font-size: 13px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.2s;
                            "
                            onmouseover="this.style.background='#5a6268'; this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.background='#6c757d'; this.style.transform='translateY(0)'">
                        <i class="fas fa-check-double"></i> Seleccionar Todos
                    </button>
                    
                    <button type="button" 
                            id="btn-limpiar-seleccion" 
                            style="
                                background: #dc3545;
                                color: white;
                                border: none;
                                padding: 10px 16px;
                                border-radius: 8px;
                                font-size: 13px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.2s;
                            "
                            onmouseover="this.style.background='#c82333'; this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.background='#dc3545'; this.style.transform='translateY(0)'">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
                
                <!-- Botones de acción principal -->
                <div style="display: flex; gap: 12px;">
                    <button type="button" 
                            id="btn-confirmar-seleccion" 
                            disabled
                            style="
                                background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                                color: white;
                                border: none;
                                padding: 12px 24px;
                                border-radius: 8px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.2s;
                                opacity: 0.5;
                                font-size: 14px;
                            ">
                        <i class="fas fa-filter"></i> Confirmar Selección
                    </button>
                    
                    <button type="button" 
                            id="btn-enviar-rrhh" 
                            disabled
                            style="
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                border: none;
                                padding: 12px 24px;
                                border-radius: 8px;
                                font-weight: 600;
                                cursor: pointer;
                                transition: all 0.2s;
                                opacity: 0.5;
                                display: none;
                                font-size: 14px;
                            ">
                        <i class="fas fa-paper-plane"></i> Enviar a RRHH
                    </button>
                </div>
            </div>
        `;
        
            Swal.fire({
                    title: `<i class="fas fa-folder-open"></i> Selección de CVs - Solicitud #${idSolicitud}`,
                    html: modalHtml,
                    width: '1000px',
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: '<i class="fas fa-times"></i> Cerrar',
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'archivos-modal-popup',
                        cancelButton: 'archivos-cancel-button'
                    },
                    didOpen: () => {
                        configurarEventosSeleccion();
                        setTimeout(() => actualizarContador(), 100);
                        
                        // Agregar estilos CSS
                        if (!document.getElementById('archivos-styles')) {
                            const styles = document.createElement('style');
                            styles.id = 'archivos-styles';
                            styles.textContent = `
                                .archivos-modal-popup {
                                    border-radius: 16px !important;
                                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
                                }
                                .archivos-cancel-button {
                                    background: #6c757d !important;
                                    border: none !important;
                                    border-radius: 8px !important;
                                    padding: 12px 24px !important;
                                    font-weight: 600 !important;
                                    font-size: 14px !important;
                                }
                                .archivo-item.seleccionado {
                                    border-color: #667eea !important;
                                    background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%) !important;
                                    box-shadow: 0 4px 12px rgba(102,126,234,0.15) !important;
                                }
                                .archivo-item.seleccionado .cv-checkbox {
                                    accent-color: #667eea !important;
                                }
                            `;
                            document.head.appendChild(styles);
                        }
                    },
                    // ← MOVER willClose AQUÍ, FUERA DEL didOpen
                    willClose: () => {
                        modalCVAbierto = false;
                        console.log("🧯 Modal de selección cerrado, desbloqueado");
                    }
                });
            }

            function configurarEventosSeleccion() {
                console.log("⚙️ Iniciando configuración de eventos...");
                
                // EVENTO PRINCIPAL PARA CHECKBOXES
                $(document).off('change', '.cv-checkbox').on('change', '.cv-checkbox', function(e) {
                    e.stopPropagation();
                    
                    const archivoId = String($(this).data('archivo-id'));
                    const isChecked = $(this).is(':checked');
                    const archivoItem = $(this).closest('.archivo-item');

                    if (isChecked) {
                        archivosSeleccionados.add(archivoId);
                        archivoItem.addClass('seleccionado');
                    } else {
                        archivosSeleccionados.delete(archivoId);
                        archivoItem.removeClass('seleccionado');
                    }

                    actualizarContador();
                });

                // Resto de eventos con .off() agregado
                $(document).off('click', '#btn-seleccionar-todos').on('click', '#btn-seleccionar-todos', function(e) {
                    e.stopPropagation();
                    console.log('🔄 Seleccionando todos los archivos');
                    
                    archivosSeleccionados.clear();
                    
                    $('.cv-checkbox').each(function() {
                        $(this).prop('checked', true);
                        const archivoId = $(this).data('archivo-id');
                        archivosSeleccionados.add(String(archivoId));
                        $(this).closest('.archivo-item').addClass('seleccionado');
                    });
                    
                    actualizarContador();
                });

                $(document).off('click', '#btn-limpiar-seleccion').on('click', '#btn-limpiar-seleccion', function(e) {
                    e.stopPropagation();
                    console.log('🧹 Limpiando selección');
                    
                    $('.cv-checkbox').each(function() {
                        $(this).prop('checked', false);
                        $(this).closest('.archivo-item').removeClass('seleccionado');
                    });
                    
                    archivosSeleccionados.clear();
                    actualizarContador();
                });

                $(document).off('click', '#btn-confirmar-seleccion').on('click', '#btn-confirmar-seleccion', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (archivosSeleccionados.size === 0) {
                        Swal.fire('Atención', 'Debes seleccionar al menos un CV', 'warning');
                        return;
                    }

                    confirmarSeleccion();
                });

                $(document).off('click', '#btn-enviar-rrhh').on('click', '#btn-enviar-rrhh', function(e) {
                    e.stopPropagation();
                    enviarSeleccionARRHH();
                });

                $(document).off('click', '.btn-ver-archivo').on('click', '.btn-ver-archivo', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const rutaArchivo = $(this).data('archivo');
                    if (rutaArchivo) {
                        window.open(rutaArchivo, '_blank');
                    }
                });

                $(document).off('click', '.btn-descargar-archivo').on('click', '.btn-descargar-archivo', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const rutaArchivo = $(this).data('archivo');
                    if (rutaArchivo) {
                        const link = document.createElement('a');
                        link.href = rutaArchivo;
                        link.download = rutaArchivo.split('/').pop();
                        link.click();
                    }
                });

                $(document).off('click', '.archivo-item').on('click', '.archivo-item', function(e) {
                    if (!$(e.target).closest('button').length && !$(e.target).is('input[type="checkbox"]')) {
                        const checkbox = $(this).find('.cv-checkbox');
                        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                    }
                });
                
                console.log("✅ Eventos configurados correctamente");
            }

    // FUNCIÓN CORREGIDA PARA FORZAR ACTUALIZACIÓN VISUAL
    function actualizarContador() {
        const contador = archivosSeleccionados.size;
        console.log(`🔢 Actualizando contador: ${contador} archivos seleccionados`);
        
        // MÚLTIPLES MÉTODOS PARA FORZAR ACTUALIZACIÓN VISUAL
        const numeroElement = $('#numero-seleccionados');
        const btnConfirmar = $('#btn-confirmar-seleccion');
        
        if (numeroElement.length === 0) {
            console.error("❌ Elemento #numero-seleccionados no encontrado");
            return;
        }
        
        // MÉTODO 1: Actualización directa
        numeroElement.text(contador);
        
        // MÉTODO 2: Forzar repaint del DOM
        numeroElement[0].style.display = 'none';
        numeroElement[0].offsetHeight; // Trigger reflow
        numeroElement[0].style.display = '';
        
        // MÉTODO 3: Actualización con HTML
        numeroElement.html(contador);
        
        // MÉTODO 4: Cambiar color para indicar cambio
        numeroElement.css('color', contador > 0 ? '#28a745' : '#667eea');
        numeroElement.css('font-weight', '700');
        numeroElement.css('font-size', '18px');
        
        // MÉTODO 5: Actualizar atributo data
        numeroElement.attr('data-count', contador);
        
        console.log(`✅ Contador actualizado en UI: ${contador}`);
        
        // Actualizar botón con el número
        if (btnConfirmar.length > 0) {
            if (contador > 0) {
                btnConfirmar.prop('disabled', false).css('opacity', '1');
                btnConfirmar.html(`<i class="fas fa-filter"></i> Confirmar Selección (${contador})`);
                console.log("✅ Botón confirmar habilitado");
            } else {
                btnConfirmar.prop('disabled', true).css('opacity', '0.5');
                btnConfirmar.html('<i class="fas fa-filter"></i> Confirmar Selección');
                console.log("❌ Botón confirmar deshabilitado");
            }
        }
        
        // VERIFICACIÓN FINAL
        setTimeout(() => {
            const valorActual = numeroElement.text();
            console.log(`🔍 Verificación: Valor mostrado="${valorActual}", Esperado="${contador}"`);
            if (valorActual != contador) {
                console.warn("⚠️ Valor no coincide, forzando actualización...");
                numeroElement.text(contador).trigger('change');
            }
        }, 100);
    }


    // RESTO DE FUNCIONES (confirmarSeleccion, enviarSeleccionARRHH, etc.)
        function confirmarSeleccion() {
            const archivosArray = Array.from(archivosSeleccionados);
            const archivosConfirmados = archivosOriginales.filter(archivo =>
                archivosArray.includes(String(archivo.ID_ARCHIVO))
            );

            console.log(`Confirmando ${archivosConfirmados.length} archivos`);

            Swal.fire({
                title: '¿Confirmar selección de CVs?',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <h6 style="margin: 0 0 8px 0; font-weight: 600;">
                                <i class="fas fa-check-circle"></i> Resumen de Selección
                            </h6>
                            <p style="margin: 0; opacity: 0.9;">
                                Has seleccionado <strong>${archivosConfirmados.length} CVs</strong> para avanzar en el proceso
                            </p>
                        </div>
                        <div style="max-height: 200px; overflow-y: auto; margin-bottom: 15px;">
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                ${archivosConfirmados.map(archivo => `
                                    <li style="padding: 10px 15px; margin: 5px 0; 
                                              background: #f8f9fa; border-radius: 8px;
                                              border-left: 4px solid #28a745; display: flex; align-items: center;">
                                        <i class="fas fa-file-pdf" style="color: #dc3545; margin-right: 10px;"></i>
                                        <span style="font-weight: 500;">${archivo.NOMBRE_SOLO}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                        <div style="background: #e3f2fd; border: 1px solid #bbdefb; 
                                    border-radius: 8px; padding: 12px; margin-top: 15px;">
                            <p style="margin: 0; color: #1976d2; font-size: 14px;">
                                <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                                Solo se mostrarán estos archivos seleccionados. Los demás quedarán ocultos.
                            </p>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Sí, confirmar selección',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {

                    // GUARDAR EN BASE DE DATOS
                    $.ajax({
                        url: './supervision/crudsolicitudes.php?action=guardar_seleccion_cvs',
                        type: 'POST',
                        data: {
                              id_solicitud: idSolicitudActual,
                              archivos_seleccionados: JSON.stringify(
                                  archivosConfirmados.map(a => a.NOMBRE_ARCHIVO)
                              ),
                              comentario: $('#comentarioSupervisor').val() || '',
                              total_archivos: archivosConfirmados.length
                        },
                          success: function (response) {
                            console.log("🟡 Archivos guardados correctamente:", response);

                            // OCULTAR ARCHIVOS NO SELECCIONADOS Y APLICAR ESTILO A LOS SELECCIONADOS
                            $('.archivo-item').each(function () {
                                const archivoId = $(this).data('archivo-id');
                                if (!archivosSeleccionados.has(archivoId)) {
                                    $(this).fadeOut(400);
                                } else {
                                    $(this).css({
                                        'border-color': '#28a745',
                                        'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
                                    });
                                }
                            });

                            // CAMBIAR BOTONES
                            $('#btn-confirmar-seleccion').fadeOut(300, function () {
                                $('#btn-enviar-rrhh').fadeIn(300).prop('disabled', false).css('opacity', '1');
                            });

                            // ACTUALIZAR MENSAJE
                            $('#contador-seleccionados').html(`
                                <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                <span style="color: #28a745; font-weight: 600;">
                                    ${archivosConfirmados.length} CVs confirmados para envío
                                </span>
                            `);

                            // DESHABILITAR CONTROLES
                            $('#btn-seleccionar-todos, #btn-limpiar-seleccion').prop('disabled', true).css('opacity', '0.5');
                            $('.cv-checkbox').prop('disabled', true);

                            Swal.fire({
                                title: '¡Selección confirmada!',
                                text: 'Ahora puedes enviar la selección a RRHH',
                                icon: 'success',
                                timer: 2500,
                                showConfirmButton: false
                            });
                            cargarSolicitudes(); 
                        },
                        error: function (xhr, status, error) {
                            console.error("❌ Error al guardar selección:", error);
                            Swal.fire('Error', 'Ocurrió un error al guardar la selección en la base de datos.', 'error');
                        }
                    });
                }
            });
        }

      // FUNCIÓN PARA CREAR SOLICITUD
// FUNCIÓN PARA CREAR SOLICITUD
      $('.btnCrearsolicitud').click(function () {
        Swal.fire({
            title: 'Crear Nueva Solicitud de Personal',
            html: `
                <div style="text-align: left; max-width: 600px;">
                    <!-- Información de Seguridad -->
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <i class="fas fa-shield-alt" style="font-size: 18px; margin-right: 8px; color: #856404;"></i>
                            <strong style="color: #856404;">Control de Acceso</strong>
                        </div>
                        <p style="margin: 0; font-size: 13px; color: #856404;">
                            Solo supervisores autorizados pueden crear solicitudes de personal.
                            <br><a href="#" id="ver_supervisores" style="color: #007bff;">
                                <i class="fas fa-users"></i> Ver lista de supervisores válidos
                            </a>
                        </p>
                    </div>

                    <!-- Paso 1: Búsqueda de Empleado -->
                    <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <h4 style="margin: 0 0 10px 0; color: #1976d2;">
                            <i class="fas fa-user"></i> 1. Información del Solicitante
                        </h4>
                        <div style="display: flex; gap: 10px; align-items: end;">
                            <div style="flex: 1;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Código de Supervisor:</label>
                                <input type="text" id="empleado_codigo" class="swal2-input" placeholder="Ej: 5226, 5287, 5333..." style="margin: 0;">
                                <small style="color: #666; font-size: 12px;">Solo códigos de supervisores autorizados</small>
                            </div>
                            <button id="buscar_empleado" style="padding: 10px 15px; background: #1976d2; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                <i class="fas fa-search"></i> Verificar
                            </button>
                        </div>
                        <div id="empleado_info" style="margin-top: 10px; display: none;"></div>
                        <div id="error_info" style="margin-top: 10px; display: none;"></div>
                    </div>

                    <!-- Paso 2: Campo Dirigido a -->
                    <div class="form-step" id="paso-2" style="background: #e8f5e8; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #2e7d32;">
                            <i class="fas fa-paper-plane"></i> 2. Dirigido a
                        </h4>
                        <div class="form-group">
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Dirigido a:</label>
                            <select class="swal2-input" id="dirigido_a" name="dirigido_a" required style="margin: 0;">
                                <option value="">Seleccione destinatario</option>
                                <option value="Keisha Davila">Keisha Davila</option>
                                <option value="Cristy Garcia">Cristy Garcia</option>
                                <option value="Emma de Cea">Emma de Cea</option>
                            </select>
                        </div>
                    </div>

                    <!-- Paso 3: Selección de Tienda -->
                    <div id="tienda_section" style="background: #f3e5f5; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #7b1fa2;">
                            <i class="fas fa-store"></i> 3. Tienda que Necesita Personal
                        </h4>
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Selecciona la tienda:</label>
                        <select id="tienda_select" class="swal2-input" style="margin: 0;">
                            <option value="">Cargando tiendas...</option>
                        </select>
                    </div>

                    <!-- Paso 4: Tipo de Vacante -->
                    <div id="puesto_section" style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #f57c00;">
                            <i class="fas fa-briefcase"></i> 4. Tipo de Vacante
                        </h4>
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Puesto a solicitar:</label>
                        <select id="puesto_select" class="swal2-input" style="margin: 0;">
                            <option value="">Selecciona el puesto...</option>
                            <option value="JEFE DE TIENDA">Jefe de Tienda</option>
                            <option value="SUB JEFE DE TIENDA">Sub Jefe de Tienda</option>
                            <option value="ASESOR DE VENTAS">Asesor de Ventas</option>
                            <option value="VACACIONISTA">Vacacionista</option>
                            <option value="SUPERVISOR">Supervisor</option>
                            <option value="TEMPORAL">Temporal</option>
                            <option value="CAJERO">Cajero</option>
                            <option value="BODEGUERO">Bodeguero</option>
                            <option value="SEGURIDAD">Seguridad</option>
                        </select>
                    </div>

                    <!-- Paso 5: Razón de la Vacante -->
                    <div id="razon_section" style="background: #ffebee; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #c62828;">
                            <i class="fas fa-edit"></i> 5. Razón de la Vacante
                        </h4>
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">¿Por qué se necesita esta vacante?</label>
                        <select id="razon_select" class="swal2-input" style="margin: 0;">
                            <option value="">Selecciona la razón...</option>
                            <option value="Renuncia Voluntaria">Renuncia Voluntaria</option>
                            <option value="Despido por Causa Justa">Despido por Causa Justa</option>
                            <option value="Despido sin Causa Justa">Despido sin Causa Justa</option>
                            <option value="Abandono de Trabajo">Abandono de Trabajo</option>
                            <option value="Vencimiento de Contrato">Vencimiento de Contrato</option>
                            <option value="Promoción Interna">Promoción Interna</option>
                            <option value="Traslado a Otra Tienda">Traslado a Otra Tienda</option>
                            <option value="Incapacidad Permanente">Incapacidad Permanente</option>
                            <option value="Jubilación">Jubilación</option>
                            <option value="Nueva Posición">Nueva Posición</option>
                            <option value="Aumento de Personal">Aumento de Personal</option>
                            <option value="Temporada Alta">Temporada Alta</option>
                        </select>
                    </div>

                    <!-- Resumen -->
                    <div id="resumen_section" style="background: #f5f5f5; padding: 15px; border-radius: 8px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #424242;">
                            <i class="fas fa-clipboard-list"></i> Resumen de la Solicitud
                        </h4>
                        <div id="resumen_content"></div>
                    </div>
                </div>
            `,
            width: '700px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-plus-circle"></i> Crear Solicitud',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            showConfirmButton: false,
            didOpen: () => {
                // Store empleadoData in window scope
                window.empleadoData = null;
                // Store reference to main modal
                window.mainModal = Swal.getPopup();

                // Ver supervisores válidos
                $('#ver_supervisores').click(function(e) {
                    e.preventDefault();
                    
                    // CRITICAL FIX: Use mixin to create independent modal
                    const SupervisorModal = Swal.mixin({
                        customClass: {
                            container: 'supervisor-modal-container'
                        },
                        backdrop: 'rgba(0,0,0,0.4)'
                    });
                    
                    SupervisorModal.fire({
                        title: '<i class="fas fa-spinner fa-spin"></i> Cargando supervisores...',
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });
                    
                    $.ajax({
                        url: './supervision/crudsolicitudes.php?action=get_valid_supervisors',
                        method: 'GET',
                        dataType: 'json',
                        timeout: 10000,
                        success: function(supervisors) {
                            if (!supervisors || supervisors.length === 0) {
                                SupervisorModal.fire({
                                    icon: 'info',
                                    title: 'Sin Datos',
                                    text: 'No se encontraron supervisores en el sistema',
                                    confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                                    confirmButtonColor: '#6c757d'
                                });
                                return;
                            }
                            
                            let lista = '<div style="max-height: 300px; overflow-y: auto;">';
                            lista += '<table style="width: 100%; border-collapse: collapse; font-size: 13px;">';
                            lista += '<thead><tr style="background: #f8f9fa;">';
                            lista += '<th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;"><i class="fas fa-hashtag"></i> Código</th>';
                            lista += '<th style="padding: 12px; border: 1px solid #dee2e6;"><i class="fas fa-user"></i> Nombre</th>';
                            lista += '</tr></thead><tbody>';
                            
                            supervisors.forEach(sup => {
                                lista += `<tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: bold; color: #007bff;">${sup.codigo}</td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6;">${sup.nombre}</td>
                                </tr>`;
                            });
                            
                            lista += '</tbody></table></div>';
                            
                            SupervisorModal.fire({
                                title: '<i class="fas fa-users"></i> Supervisores Autorizados',
                                html: lista,
                                width: '600px',
                                confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                                confirmButtonColor: '#6c757d',
                                // CRITICAL: This ensures only the supervisor modal closes
                                willClose: () => {
                                    // Return focus to main modal without closing it
                                    if (window.mainModal) {
                                        window.mainModal.focus();
                                    }
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error cargando supervisores:', error);
                            SupervisorModal.fire({
                                icon: 'error',
                                title: '<i class="fas fa-exclamation-triangle"></i> Error',
                                text: 'No se pudo cargar la lista de supervisores.',
                                confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                });

                // Buscar empleado
                $('#buscar_empleado').click(function() {
                    const codigo = $('#empleado_codigo').val().trim();
                    if (!codigo) {
                        mostrarError('<i class="fas fa-exclamation-circle"></i> Ingresa un código de empleado', 'warning');
                        return;
                    }

                    $(this).html('<i class="fas fa-spinner fa-spin"></i> Verificando...').prop('disabled', true);
                    $('#empleado_info').hide();
                    $('#error_info').hide();

                    $.ajax({
                        url: './supervision/crudsolicitudes.php?action=search_employee&codigo=' + codigo,
                        method: 'GET',
                        dataType: 'json',
                        timeout: 10000,
                        success: function(data) {
                            if (data.error) {
                                if (data.error === 'ACCESO DENEGADO') {
                                    mostrarError(`
                                        <div style="text-align: center;">
                                            <div style="font-size: 48px; margin-bottom: 15px; color: #dc3545;">
                                                <i class="fas fa-ban"></i>
                                            </div>
                                            <strong style="color: #dc3545; font-size: 18px;">ACCESO DENEGADO</strong>
                                            <p style="margin: 15px 0; font-size: 14px;">
                                                El código <strong>${data.codigo_ingresado}</strong> corresponde a:
                                                <br><strong>${data.nombre_empleado}</strong>
                                            </p>
                                            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #ffc107;">
                                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                                    <i class="fas fa-exclamation-triangle" style="color: #856404; margin-right: 8px;"></i>
                                                    <strong style="color: #856404;">Este empleado NO es supervisor</strong>
                                                </div>
                                                <span style="font-size: 12px; color: #856404;">
                                                    Solo supervisores con tiendas a cargo pueden crear solicitudes
                                                </span>
                                            </div>
                                        </div>
                                    `, 'error');
                                } else if (data.error === 'EMPLEADO NO ENCONTRADO') {
                                    mostrarError(`
                                        <div style="text-align: center;">
                                            <div style="font-size: 48px; margin-bottom: 15px; color: #6c757d;">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <strong style="color: #dc3545; font-size: 18px;">EMPLEADO NO ENCONTRADO</strong>
                                            <p style="margin: 15px 0; font-size: 14px;">
                                                El código <strong>${data.codigo_ingresado}</strong> no existe en el sistema.
                                            </p>
                                        </div>
                                    `, 'error');
                                } else {
                                    mostrarError('<i class="fas fa-times-circle"></i> ' + data.error, 'error');
                                }
                            } else {
                                window.empleadoData = data;
                                mostrarInfoEmpleado(data);
                                cargarTiendas(data.tiendas);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error buscando empleado:', error);
                            mostrarError('<i class="fas fa-wifi"></i> Error de conexión con el servidor', 'error');
                        },
                        complete: function() {
                            $('#buscar_empleado').html('<i class="fas fa-search"></i> Verificar').prop('disabled', false);
                        }
                    });
                });

                function mostrarError(mensaje, tipo) {
                    const colors = {
                        'error': '#dc3545',
                        'warning': '#ffc107', 
                        'info': '#17a2b8'
                    };
                    const color = colors[tipo] || '#6c757d';
                    
                    $('#error_info').html(`
                        <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid ${color}; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            ${mensaje}
                        </div>
                    `).show();
                    $('#paso-2').hide();
                    $('#tienda_section').hide();
                    $('#puesto_section').hide();
                    $('#razon_section').hide();
                    $('#resumen_section').hide();
                }

                function mostrarInfoEmpleado(data) {
                    $('#empleado_info').html(`
                        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #28a745; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-check-circle" style="font-size: 24px; color: #28a745; margin-right: 10px;"></i>
                                <strong style="color: #155724; font-size: 16px;">SUPERVISOR AUTORIZADO</strong>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-hashtag" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span><strong>Código:</strong> ${data.codigo}</span>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-user" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span><strong>Nombre:</strong> ${data.nombre}</span>
                                </div>
                            </div>
                        </div>
                    `).show();
                    $('#paso-2').show();
                    $('#error_info').hide();
                }

                function cargarTiendas(tiendas) {
                    let options = '<option value="">Selecciona una tienda...</option>';
                    tiendas.forEach(tienda => {
                        options += `<option value="${tienda}">Tienda ${tienda}</option>`;
                    });
                    $('#tienda_select').html(options);
                }

                // Eventos de cambio
                $('#dirigido_a').change(function() {
                    if ($(this).val()) {
                        $('#tienda_section').show();
                    }
                });

                $('#tienda_select').change(function() {
                    if ($(this).val()) {
                        $('#puesto_section').show();
                    }
                });

                $('#puesto_select').change(function() {
                    if ($(this).val()) {
                        $('#razon_section').show();
                    }
                });

                $('#razon_select').change(function() {
                    if ($(this).val()) {
                        mostrarResumen();
                        $('.swal2-confirm').show();
                    }
                });

                function mostrarResumen() {
                    const tienda = $('#tienda_select').val();
                    const puesto = $('#puesto_select').val();
                    const razon = $('#razon_select').val();
                    const dirigidoA = $('#dirigido_a').val();

                    if (!window.empleadoData) {
                        console.error('Error: empleadoData no está definido');
                        mostrarError('<i class="fas fa-exclamation-triangle"></i> Error interno: datos del empleado no disponibles', 'error');
                        return;
                    }

                    $('#resumen_content').html(`
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 14px;">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                                <h5 style="margin: 0 0 10px 0; color: #495057; display: flex; align-items: center;">
                                    <i class="fas fa-user-tie" style="margin-right: 8px;"></i>
                                    Supervisor
                                </h5>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-hashtag" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Código:</strong> ${window.empleadoData.codigo}
                                </div>
                                <div>
                                    <i class="fas fa-user" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Nombre:</strong> ${window.empleadoData.nombre}
                                </div>
                            </div>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                                <h5 style="margin: 0 0 10px 0; color: #495057; display: flex; align-items: center;">
                                    <i class="fas fa-clipboard-list" style="margin-right: 8px;"></i>
                                    Solicitud
                                </h5>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-paper-plane" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Dirigido a:</strong> ${dirigidoA}
                                </div>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-store" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Tienda:</strong> ${tienda}
                                </div>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-briefcase" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Vacante:</strong> ${puesto}
                                </div>
                                <div>
                                    <i class="fas fa-edit" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Razón:</strong> ${razon}
                                </div>
                            </div>
                        </div>
                    `);
                    $('#resumen_section').show();
                }

            },
           preConfirm: () => {
                const tienda = $('#tienda_select').val();
                const puesto = $('#puesto_select').val();
                const razon = $('#razon_select').val();
                const dirigidoA = $('#dirigido_a').val();

                if (!window.empleadoData) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Error: datos del empleado no disponibles');
                    return false;
                }

                if (!tienda || !puesto || !razon || !dirigidoA) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Completa todos los campos');
                    return false;
                }

                if (!window.empleadoData.es_supervisor) {
                    Swal.showValidationMessage('<i class="fas fa-ban"></i> Solo supervisores autorizados pueden crear solicitudes');
                    return false;
                }

                return {
                    empleado_codigo: window.empleadoData.codigo,
                    empleado_nombre: window.empleadoData.nombre,
                    tienda_no: tienda,
                    puesto_solicitado: puesto,
                    razon_vacante: razon,
                    dirigido_a: dirigidoA
                };
            }

        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                
                Swal.fire({
                    title: '<i class="fas fa-spinner fa-spin"></i> Creando solicitud...',
                    text: 'Por favor espera mientras se procesa la solicitud',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });
                
                $.ajax({
                    url: './supervision/crudsolicitudes.php?action=create_advanced_solicitud',
                    type: 'POST',
                    data: data,
                    timeout: 10000,
                    success: function (response) {
                        console.log('Raw response:', response);
                        
                        let res;
                        try {
                            if (typeof response === 'string') {
                                res = JSON.parse(response);
                            } else {
                                res = response;
                            }
                        } catch (e) {
                            console.log('JSON parse failed, checking for success indicators');
                            const responseStr = String(response);
                            
                            if (responseStr.includes('success') || responseStr.includes('Solicitud creada exitosamente')) {
                                res = { success: true, message: 'Solicitud creada exitosamente' };
                            } else {
                                res = { success: false, error: 'Respuesta inválida del servidor' };
                            }
                        }
                        
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '<i class="fas fa-check-circle"></i> ¡Éxito!',
                                text: res.message || 'Solicitud creada correctamente',
                                confirmButtonText: '<i class="fas fa-check"></i> Entendido'
                            });
                            if (typeof cargarSolicitudes === 'function') {
                                cargarSolicitudes();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<i class="fas fa-times-circle"></i> Error',
                                text: res.error || 'Error al crear solicitud',
                                confirmButtonText: '<i class="fas fa-check"></i> Entendido'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error creando solicitud:', error);
                        Swal.fire({
                            icon: 'error',
                            title: '<i class="fas fa-wifi"></i> Error de Conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonText: '<i class="fas fa-check"></i> Entendido'
                        });
                    }
                });
            }
            
            // Clear the global variables when modal closes
            window.empleadoData = null;
            window.mainModal = null;
        });
      });





// FUNCIÓN PARA EDITAR SOLICITUD
$(document).off('click', '.btnEditarSolicitud').on('click', '.btnEditarSolicitud', function () {
    const index = $(this).data('index');
    const solicitud = solicitudes[index];

    // First, get the supervisor's stores to populate the dropdown
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando información...',
        text: 'Obteniendo tiendas del supervisor',
        allowOutsideClick: false,
        showConfirmButton: false
    });

    // Get supervisor's stores based on who created the original request
    $.ajax({
        url: './supervision/crudSolicitudes.php?action=get_supervisor_stores',
        method: 'GET',
        data: { solicitado_por: solicitud.SOLICITADO_POR },
        dataType: 'json',
        timeout: 10000,
        success: function(supervisorData) {
            // Now show the edit modal with the supervisor's stores
            Swal.fire({
                title: '<i class="fas fa-edit"></i> Editar Solicitud de Personal',
                html: `
                    <div style="text-align: left; max-width: 650px;">
                        <!-- Header Information -->
                        <div style="background: #e3f2fd; border: 1px solid #bbdefb; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-info-circle" style="font-size: 20px; margin-right: 10px; color: #1976d2;"></i>
                                <strong style="color: #1976d2; font-size: 16px;">Información de la Solicitud</strong>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-hashtag" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span style="color: #333;"><strong>ID:</strong> ${solicitud.ID_SOLICITUD}</span>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-calendar" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span style="color: #333;"><strong>Fecha:</strong> ${solicitud.FECHA_SOLICITUD}</span>
                                </div>
                                <div style="display: flex; align-items: center; grid-column: 1 / -1;">
                                    <i class="fas fa-user-tie" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span style="color: #333;"><strong>Solicitado por:</strong> ${solicitud.SOLICITADO_POR}</span>
                                    <span style="margin-left: 10px; padding: 2px 8px; background: #f8f9fa; border-radius: 12px; font-size: 12px; color: #6c757d;">
                                        <i class="fas fa-lock" style="margin-right: 4px;"></i>No editable
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Supervisor Info -->
                        <div style="background: #e8f5e8; border: 1px solid #c8e6c9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-user-shield" style="font-size: 16px; margin-right: 8px; color: #2e7d32;"></i>
                                <strong style="color: #2e7d32; font-size: 14px;">Supervisor: ${supervisorData.nombre}</strong>
                            </div>
                            <div style="font-size: 12px; color: #4caf50;">
                                <i class="fas fa-store" style="margin-right: 5px;"></i>
                                Tiendas a cargo: ${supervisorData.tiendas.length} tienda(s)
                            </div>
                        </div>

                        <!-- Editable Fields Section -->
                        <div style="background: #fff3e0; border: 1px solid #ffcc02; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 20px;">
                                <i class="fas fa-edit" style="font-size: 18px; margin-right: 10px; color: #f57c00;"></i>
                                <strong style="color: #f57c00; font-size: 16px;">Campos Editables</strong>
                            </div>

                            <!-- Store Field -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-store" style="color: #2e7d32; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #2e7d32;">Tienda:</label>
                                </div>
                                <select 
                                    id="tienda_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona una tienda...</option>
                                    ${supervisorData.tiendas.map(tienda => 
                                        `<option value="${tienda}" style="color: #333;" ${tienda === solicitud.NUM_TIENDA ? 'selected' : ''}>Tienda ${tienda}</option>`
                                    ).join('')}
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Selecciona una de las tiendas asignadas al supervisor
                                </small>
                            </div>

                            <!-- Position Field -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-briefcase" style="color: #7b1fa2; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #7b1fa2;">Puesto Solicitado:</label>
                                </div>
                                <select 
                                    id="puesto_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona el puesto...</option>
                                    <option value="JEFE DE TIENDA" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'JEFE DE TIENDA' ? 'selected' : ''}>Jefe de Tienda</option>
                                    <option value="SUB JEFE DE TIENDA" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'SUB JEFE DE TIENDA' ? 'selected' : ''}>Sub Jefe de Tienda</option>
                                    <option value="ASESOR DE VENTAS" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'ASESOR DE VENTAS' ? 'selected' : ''}>Asesor de Ventas</option>
                                    <option value="VACACIONISTA" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'VACACIONISTA' ? 'selected' : ''}>Vacacionista</option>
                                    <option value="SUPERVISOR" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'SUPERVISOR' ? 'selected' : ''}>Supervisor</option>
                                    <option value="TEMPORAL" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'TEMPORAL' ? 'selected' : ''}>Temporal</option>
                                    <option value="CAJERO" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'CAJERO' ? 'selected' : ''}>Cajero</option>
                                    <option value="BODEGUERO" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'BODEGUERO' ? 'selected' : ''}>Bodeguero</option>
                                    <option value="SEGURIDAD" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'SEGURIDAD' ? 'selected' : ''}>Seguridad</option>
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Selecciona el tipo de puesto que se necesita cubrir
                                </small>
                            </div>

                            <!-- Reason Field -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-clipboard-list" style="color: #d32f2f; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #d32f2f;">Razón de la Vacante:</label>
                                </div>
                                <select 
                                    id="razon_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona la razón...</option>
                                    <option value="Renuncia Voluntaria" style="color: #333;" ${solicitud.RAZON === 'Renuncia Voluntaria' ? 'selected' : ''}>Renuncia Voluntaria</option>
                                    <option value="Despido por Causa Justa" style="color: #333;" ${solicitud.RAZON === 'Despido por Causa Justa' ? 'selected' : ''}>Despido por Causa Justa</option>
                                    <option value="Despido sin Causa Justa" style="color: #333;" ${solicitud.RAZON === 'Despido sin Causa Justa' ? 'selected' : ''}>Despido sin Causa Justa</option>
                                    <option value="Abandono de Trabajo" style="color: #333;" ${solicitud.RAZON === 'Abandono de Trabajo' ? 'selected' : ''}>Abandono de Trabajo</option>
                                    <option value="Vencimiento de Contrato" style="color: #333;" ${solicitud.RAZON === 'Vencimiento de Contrato' ? 'selected' : ''}>Vencimiento de Contrato</option>
                                    <option value="Promoción Interna" style="color: #333;" ${solicitud.RAZON === 'Promoción Interna' ? 'selected' : ''}>Promoción Interna</option>
                                    <option value="Traslado a Otra Tienda" style="color: #333;" ${solicitud.RAZON === 'Traslado a Otra Tienda' ? 'selected' : ''}>Traslado a Otra Tienda</option>
                                    <option value="Incapacidad Permanente" style="color: #333;" ${solicitud.RAZON === 'Incapacidad Permanente' ? 'selected' : ''}>Incapacidad Permanente</option>
                                    <option value="Jubilación" style="color: #333;" ${solicitud.RAZON === 'Jubilación' ? 'selected' : ''}>Jubilación</option>
                                    <option value="Nueva Posición" style="color: #333;" ${solicitud.RAZON === 'Nueva Posición' ? 'selected' : ''}>Nueva Posición</option>
                                    <option value="Aumento de Personal" style="color: #333;" ${solicitud.RAZON === 'Aumento de Personal' ? 'selected' : ''}>Aumento de Personal</option>
                                    <option value="Temporada Alta" style="color: #333;" ${solicitud.RAZON === 'Temporada Alta' ? 'selected' : ''}>Temporada Alta</option>
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Especifica el motivo por el cual se necesita cubrir esta vacante
                                </small>
                            </div>

                            <!-- Dirigido A Field -->
                            <div style="margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-user-check" style="color: #1976d2; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #1976d2;">Dirigido A (RRHH):</label>
                                </div>
                                <select 
                                    id="dirigido_a_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona personal de RRHH...</option>
                                    <option value="Keisha Davila" style="color: #333;" ${solicitud.DIRIGIDO_A === 'Keisha Davila' ? 'selected' : ''}>Keisha Davila</option>
                                    <option value="Cristy Garcia" style="color: #333;" ${solicitud.DIRIGIDO_A === 'Cristy Garcia' ? 'selected' : ''}>Cristy Garcia</option>
                                    <option value="Emma de Cea" style="color: #333;" ${solicitud.DIRIGIDO_A === 'Emma de Cea' ? 'selected' : ''}>Emma de Cea</option>
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Selecciona la persona de RRHH que procesará esta solicitud
                                </small>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div id="edit_summary" style="background: #f5f5f5; border: 1px solid #e0e0e0; padding: 20px; border-radius: 8px; display: none;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-clipboard-check" style="font-size: 18px; margin-right: 10px; color: #424242;"></i>
                                <strong style="color: #424242; font-size: 16px;">Resumen de Cambios</strong>
                            </div>
                            <div id="changes_content"></div>
                        </div>
                    </div>
                `,
                width: '750px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save"></i> Guardar Cambios',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                didOpen: () => {
                    // Add CSS to ensure proper styling of select elements with larger font
                    const style = document.createElement('style');
                    style.textContent = `
                        .swal2-container select {
                            background: white !important;
                            color: #333 !important;
                            border: 2px solid #e0e0e0 !important;
                            border-radius: 6px !important;
                            padding: 12px !important;
                            font-size: 16px !important;
                            width: 100% !important;
                            box-sizing: border-box !important;
                            height: 50px !important;
                            appearance: menulist !important;
                            -webkit-appearance: menulist !important;
                            -moz-appearance: menulist !important;
                        }
                        
                        .swal2-container select option {
                            background: white !important;
                            color: #333 !important;
                            padding: 10px !important;
                            font-size: 16px !important;
                            line-height: 1.4 !important;
                        }
                        
                        .swal2-container select option:hover {
                            background: #f0f0f0 !important;
                            color: #333 !important;
                        }
                        
                        .swal2-container select:focus {
                            border-color: #007bff !important;
                            outline: none !important;
                            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25) !important;
                        }
                    `;
                    document.head.appendChild(style);

                    // Add real-time validation and summary update
                    function updateSummary() {
                        const tienda = $('#tienda_edit').val();
                        const puesto = $('#puesto_edit').val();
                        const razon = $('#razon_edit').val();
                        const dirigidoA = $('#dirigido_a_edit').val();

                        if (tienda && puesto && razon && dirigidoA) {
                            const changes = [];
                            
                            if (tienda !== solicitud.NUM_TIENDA) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-store" style="color: #2e7d32; margin-right: 6px;"></i><strong>Tienda:</strong> ${solicitud.NUM_TIENDA} → ${tienda}</div>`);
                            }
                            
                            if (puesto !== solicitud.PUESTO_SOLICITADO) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-briefcase" style="color: #7b1fa2; margin-right: 6px;"></i><strong>Puesto:</strong> ${solicitud.PUESTO_SOLICITADO} → ${puesto}</div>`);
                            }
                            
                            if (razon !== solicitud.RAZON) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-clipboard-list" style="color: #d32f2f; margin-right: 6px;"></i><strong>Razón:</strong> ${solicitud.RAZON} → ${razon}</div>`);
                            }

                            if (dirigidoA !== solicitud.DIRIGIDO_A) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-user-check" style="color: #1976d2; margin-right: 6px;"></i><strong>Dirigido A:</strong> ${solicitud.DIRIGIDO_A || 'Sin asignar'} → ${dirigidoA}</div>`);
                            }

                            if (changes.length > 0) {
                                $('#changes_content').html(`
                                    <div style="background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
                                        <div style="font-weight: bold; margin-bottom: 10px; color: #856404;">
                                            <i class="fas fa-exclamation-triangle" style="margin-right: 6px;"></i>
                                            Se detectaron ${changes.length} cambio(s):
                                        </div>
                                        ${changes.join('')}
                                    </div>
                                `);
                                $('#edit_summary').show();
                            } else {
                                $('#changes_content').html(`
                                    <div style="background: #d1ecf1; padding: 15px; border-radius: 6px; border-left: 4px solid #bee5eb; text-align: center;">
                                        <i class="fas fa-info-circle" style="color: #0c5460; margin-right: 6px;"></i>
                                        <span style="color: #0c5460;">No se han realizado cambios</span>
                                    </div>
                                `);
                                $('#edit_summary').show();
                            }
                        } else {
                            $('#edit_summary').hide();
                        }
                    }

                    // Add event listeners for real-time updates
                    $('#tienda_edit, #puesto_edit, #razon_edit, #dirigido_a_edit').on('input change', updateSummary);
                    
                    // Initial summary update
                    updateSummary();

                    // Add visual feedback for field changes
                    $('#tienda_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.NUM_TIENDA;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });

                    $('#puesto_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.PUESTO_SOLICITADO;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });

                    $('#razon_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.RAZON;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });

                    $('#dirigido_a_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.DIRIGIDO_A;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });
                },
                preConfirm: () => {
                    const tienda = $('#tienda_edit').val();
                    const puesto = $('#puesto_edit').val();
                    const razon = $('#razon_edit').val();
                    const dirigidoA = $('#dirigido_a_edit').val();

                    // Validation
                    if (!tienda) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar una tienda');
                        return false;
                    }

                    if (!puesto) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar un puesto');
                        return false;
                    }

                    if (!razon) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar una razón para la vacante');
                        return false;
                    }

                    if (!dirigidoA) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar a quién dirigir la solicitud');
                        return false;
                    }

                    // Check if any changes were made
                    const hasChanges = tienda !== solicitud.NUM_TIENDA || 
                                     puesto !== solicitud.PUESTO_SOLICITADO || 
                                     razon !== solicitud.RAZON ||
                                     dirigidoA !== solicitud.DIRIGIDO_A;

                    if (!hasChanges) {
                        Swal.showValidationMessage('<i class="fas fa-info-circle"></i> No se han realizado cambios para guardar');
                        return false;
                    }

                    return {
                        tienda_no: tienda,
                        puesto: puesto,
                        razon: razon,
                        dirigido_a: dirigidoA
                    };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const updatedData = result.value;

                    // Show loading state
                    Swal.fire({
                        title: '<i class="fas fa-spinner fa-spin"></i> Actualizando solicitud...',
                        text: 'Por favor espera mientras se guardan los cambios',
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    $.ajax({
                        url: './supervision/crudSolicitudes.php?action=update_solicitud',
                        type: 'POST',
                        data: {
                            id_solicitud: solicitud.ID_SOLICITUD,
                            tienda_no: updatedData.tienda_no,
                            puesto: updatedData.puesto,
                            razon: updatedData.razon,
                            dirigido_a: updatedData.dirigido_a
                        },
                        timeout: 10000,
                        success: function (response) {
                            console.log('Raw response:', response);
                            
                            let res;
                            try {
                                if (typeof response === 'string') {
                                    res = JSON.parse(response);
                                } else {
                                    res = response;
                                }
                            } catch (e) {
                                console.log('JSON parse failed, checking for success indicators');
                                const responseStr = String(response);
                                
                                if (responseStr.includes('success') || responseStr.includes('actualizada') || responseStr.includes('modificada')) {
                                    res = { success: true, message: 'Solicitud actualizada correctamente' };
                                } else {
                                    res = { success: false, error: 'Respuesta inválida del servidor' };
                                }
                            }
                            
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '<i class="fas fa-check-circle"></i> ¡Cambios Guardados!',
                                    html: `
                                        <div style="text-align: center;">
                                            <p style="margin-bottom: 15px; color: #333;">${res.message || 'La solicitud ha sido actualizada correctamente.'}</p>
                                            <div style="background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;">
                                                <div style="font-weight: bold; margin-bottom: 8px; color: #155724;">
                                                    <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                                                    Cambios aplicados:
                                                </div>
                                                <div style="font-size: 14px; color: #155724;">
                                                    <div><strong>Tienda:</strong> ${updatedData.tienda_no}</div>
                                                    <div><strong>Puesto:</strong> ${updatedData.puesto}</div>
                                                    <div><strong>Razón:</strong> ${updatedData.razon}</div>
                                                    <div><strong>Dirigido A:</strong> ${updatedData.dirigido_a}</div>
                                                </div>
                                            </div>
                                        </div>
                                    `,
                                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                                    confirmButtonColor: '#28a745'
                                });
                                
                                // Reload the requests table
                                if (typeof cargarSolicitudes === 'function') {
                                    cargarSolicitudes();
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '<i class="fas fa-times-circle"></i> Error al Actualizar',
                                    text: res.error || 'No se pudo actualizar la solicitud. Intenta nuevamente.',
                                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error updating request:', error);
                            Swal.fire({
                                icon: 'error',
                                title: '<i class="fas fa-wifi"></i> Error de Conexión',
                                text: 'No se pudo conectar con el servidor. Verifica tu conexión e intenta nuevamente.',
                                confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading supervisor stores:', error);
            Swal.fire({
                icon: 'error',
                title: '<i class="fas fa-exclamation-triangle"></i> Error',
                text: 'No se pudieron cargar las tiendas del supervisor.',
                confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                confirmButtonColor: '#dc3545'
            });
        }
    });
});

//HISTORIAL DE MODIFICACIONES
//HISTORIAL DE MODIFICACIONES
$(document).off('click', '.btn-ver-historial-modificaciones').on('click', '.btn-ver-historial-modificaciones', function () {
  const idSolicitud = $(this).data('id');

  if (!Number.isInteger(Number(idSolicitud))) {
    Swal.fire('Error', 'ID de solicitud inválido.', 'error');
    return;
  }

  $('#btnPdfIndividual').attr('href', './supervision/reporte_historial_pdf.php?id=' + idSolicitud);
  $('#modalHistorialIndividual').modal('show');
  $('#contenidoHistorial').html('<div class="text-center">Cargando historial de modificaciones...</div>');

  $.ajax({
    url: './supervision/crudsolicitudes.php?action=get_historial_edicion&id=' + idSolicitud,
    method: 'GET',
    success: function (datos) {
      if (datos.length === 0) {
        $('#contenidoHistorial').html('<div class="text-center text-muted">No hay historial de modificaciones para esta solicitud.</div>');
        return;
      }

      let html = `
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="thead-dark">
              <tr>
                <th>#</th>
                <th>Campo Modificado</th>
                <th>Valor Anterior</th>
                <th>Valor Nuevo</th>
                <th>Fecha de Cambio</th>
              </tr>
            </thead>
            <tbody>`;

      datos.forEach((h, index) => {
        // Formatear el nombre del campo para que sea más legible
        let campoFormateado = h.CAMPO_MODIFICADO;
        switch(h.CAMPO_MODIFICADO) {
          case 'NUM_TIENDA':
            campoFormateado = 'Número de Tienda';
            break;
          case 'PUESTO_SOLICITADO':
            campoFormateado = 'Puesto Solicitado';
            break;
          case 'RAZON':
            campoFormateado = 'Razón de la Vacante';
            break;
          case 'DIRIGIDO_A':
            campoFormateado = 'Dirigido A (RRHH)';
            break;
          default:
            campoFormateado = h.CAMPO_MODIFICADO;
        }

        // Formatear valores para mejor visualización
        const valorAnterior = h.VALOR_ANTERIOR || '<em style="color: #666;">Sin valor</em>';
        const valorNuevo = h.VALOR_NUEVO || '<em style="color: #666;">Sin valor</em>';

        html += `<tr>
          <td>${index + 1}</td>
          <td><strong>${campoFormateado}</strong></td>
          <td>${valorAnterior}</td>
          <td><span style="color: #28a745; font-weight: bold;">${valorNuevo}</span></td>
          <td>${h.FECHA_CAMBIO}</td>
        </tr>`;
      });

      html += '</tbody></table></div>';
      
      // Agregar información adicional
      html += `
        <div class="mt-3 p-3 bg-light rounded">
          <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Total de modificaciones: <strong>${datos.length}</strong> | 
            Última modificación: <strong>${datos[0].FECHA_CAMBIO}</strong>
          </small>
        </div>
      `;
      
      $('#contenidoHistorial').html(html);
    },
    error: function () {
      $('#contenidoHistorial').html('<div class="alert alert-danger">Error al cargar historial de modificaciones.</div>');
    }
  });
});



      // CARGAR SOLICITUDES AL INICIO
      cargarSolicitudes();
    });
  </script>
</body>
</html>
