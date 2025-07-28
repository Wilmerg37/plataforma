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

  /* ESTILO DE SELECCION DE ESTADO */
.select-estado {
  border: none;
  border-radius: 25px;
  padding: 5px 18px;
  font-weight: bold;
  color: black !important;
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

.btn-Ver-Comentario-Rh {
    padding: 3px 8px;
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
    display: inline-block;
}

/*CHAT EMERGENTE*/
#chat-contenedor {
  max-height: 300px;
  overflow-y: auto;
  background: #1e1e1e;
  padding: 10px;
  border-radius: 8px;
}

.chat-burbuja {
  max-width: 80%;
  padding: 8px 12px;
  border-radius: 15px;
  margin: 6px 0;
  word-wrap: break-word;
  font-size: 14px;
}

.chat-burbuja.izquierda {
  background: #3a3a3a;
  color: white;
  align-self: flex-start;
  border-bottom-left-radius: 0;
}

.chat-burbuja.derecha {
  background: #007bff;
  color: white;
  align-self: flex-end;
  text-align: right;
  margin-left: auto;
  border-bottom-right-radius: 0;
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
              Panel de Reclutadores
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
              <button class="btn btn-custom btn-history btnVerHistorial">
                <i class="fas fa-history mr-2"></i>
                Historial General
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
          <select id="filtroDirigidoA" class="form-control" style="width: 200px; display: inline-block; margin-right: 10px;">
            <option value="">Todos</option>
            <option value="Keisha Davila">Keisha Davila</option>
            <option value="Cristy Garcia">Cristy Garcia</option>
            <option value="Emma de Cea">Emma de Cea</option>
          </select>
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
              <th width="120">Fecha Solicitud</th>
              <th width="140">Modificación registrada</th>
              <th width="160">Estado</th>
              <th width="150">Razón</th>
              <th width="30">Comentario</th>
              <th width="350">Acciones</th>
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
          <a id="btnPdfIndividual" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Generar PDF
          </a>
          <button class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

  <!--MODAL DE CAMBIAR ESTADO DE LA SOLICITUD-->
  <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-labelledby="tituloEstadoModal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content text-white bg-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="tituloEstadoModal"><i class="fas fa-exchange-alt"></i> Cambiar Estado de Solicitud</h5>
        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        
        <!-- Información de la solicitud (no editable) -->
        <div id="infoSolicitudCambio" class="mb-3">
          <!-- Aquí se carga la info por JS -->
        </div>

        <!-- Selección de nuevo estado -->
        <div class="form-group">
          <label for="nuevoEstado"><strong>Nuevo Estado:</strong></label>
          <select id="nuevoEstado" class="form-control select-estado">
            <option value="">Seleccione estado...</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Vacante Activa">Vacante Activa</option>
            <option value="Cvs Enviados">Cvs Enviados</option>
            <option value="P. Psicometrica">P. Psicometrica</option>
            <option value="Entrevista RH">Entrevista RH</option>
            <option value="Entrevista Tecnica">Entrevista Tecnica</option>
            <option value="Dia de Prueba">Dia de prueba</option>
            <option value="Prueba de Poligrafo">Prueba de Poligrafo</option>
            <option value="Expediente RH">Expediente RH</option>
            <option value="Confirmacion Candidato">Confirmacion Candidato</option>
            <option value="Contratada">Contratada</option>
          </select>
        </div>

        <!-- Comentario o razón -->
        <div class="mb-3">
          <label for="razonCambio" class="form-label">Comentario:</label>
          <textarea id="comentarioCambio" class="form-control" rows="3" placeholder="Escribe un comentario del cambio de estado..."></textarea>
        </div>

        <!-- Subida de archivos (solo si estado == CVS Enviados) -->
        <div class="mb-3" id="seccionArchivos" style="display: none;">
          <label for="archivosCVS" class="form-label">Archivos de CVS:</label>
          <input type="file" id="archivosCVS" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
          <small class="text-muted">Máximo 50MB por archivo.</small>
          <div id="vistaPreviaArchivos" class="mt-2"></div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="btnCancelarCambioEstado" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnGuardarCambioEstado">Guardar Cambios</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Comentario -->
<div class="modal fade" id="modalComentario" tabindex="-1" aria-labelledby="comentarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="comentarioLabel"><i class="fas fa-comment-dots"></i> Comentario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p id="textoComentario"></p>
      </div>
    </div>
  </div>
</div>
  <!-- JavaScript Principal -->
  <script>
    $(document).ready(function () {
      let solicitudes = [];
      let rowsPerPage = 10;
      let currentPage = 1;
      let chatAbierto = false;
      let modalArchivosAbierto = false;
      let modalResumenAbierto = false;
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
          url: './gestionhumana/crudsolicitudesrh.php?action=get_solicitudes',
          type: 'GET',
          dataType: 'json',
          success: function (data) {
            allSolicitudes = data;

            // Cargar opciones únicas del campo DIRIGIDO_A
            const nombresUnicos = [...new Set(allSolicitudes.map(item => item.DIRIGIDO_A).filter(Boolean))];

            const select = $('#filtroDirigidoA');
            select.empty(); // Limpiar opciones anteriores
            select.append('<option value="">Todos</option>');

            nombresUnicos.forEach(nombre => {
              select.append(`<option value="${nombre}">${nombre}</option>`);
            });

            // Aplicar filtro si ya se había seleccionado uno
            const filtroGuardado = localStorage.getItem('filtroDirigidoA') || '';
            $('#filtroDirigidoA').val(filtroGuardado);

            const datosFiltrados = filtroGuardado
              ? allSolicitudes.filter(item => item.DIRIGIDO_A === filtroGuardado)
              : allSolicitudes;

            if (datosFiltrados.length === 0) {
              $('#loading-indicator').hide();
              $('#empty-state').show();
            } else {
              renderTable(datosFiltrados);
              setupPagination(datosFiltrados);
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


  // FUNCIÓN PARA RENDERIZAR LA TABLA
function renderTable(data) {
  const tbody = $('#tblSolicitudes tbody');
  const thead = $('#tblSolicitudes thead');
  console.log("Datos recibidos:", data);
  tbody.empty();

  // Debugging
  if (data.length > 0) {
    console.log("Primer item:", data[0]);
    console.log("Comentario del primer item:", data[0].COMENTARIO_SOLICITUD);
  }
  console.log("DEBUG FULL JSON", JSON.stringify(data, null, 2));

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const pageData = data.slice(start, end);

  pageData.forEach((item, index) => {
    const globalIndex = start + index;
let statusClass = '';
const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();

if (estado.includes('pendiente')) {
  statusClass = 'estado-pendiente';
} else if (estado.includes('activa')) {
  statusClass = 'estado-activa';
} else if (estado.includes('cvs')) {
  statusClass = 'estado-cvs';
} else if (estado.includes('psico') || estado.includes('psicometrica')) {
  statusClass = 'estado-psico';
} else if (estado.includes('entrevista rh')) {
  statusClass = 'estado-rh';
} else if (estado.includes('expediente')) {
  statusClass = 'estado-expediente';
} else if (estado.includes('tecnica')) {
  statusClass = 'estado-tecnica';
} else if (estado.includes('prueba')) {
  statusClass = 'estado-prueba';
} else if (estado.includes('poligrafo')) {
  statusClass = 'estado-poligrafo';
} else if (estado.includes('confirmacion')) {
  statusClass = 'estado-confirmacion';
} else if (estado.includes('contratada')) {
  statusClass = 'estado-contratada';
} else {
  statusClass = 'estado-pendiente'; // default
}


    const fechaModificacion = item.FECHA_MODIFICACION || '—';
    const comentario = item.COMENTARIO_SOLICITUD || '-';
    const idHistorico = item.ID_HISTORICO;
  
    // Formatea el comentario para mostrar
    const noLeidos = parseInt(item.NO_LEIDOS) || 0;
    //const noLeidos = 5; // prueba directa
    console.log('ID:', idHistorico, 'Comentario:', comentario, 'NO_LEIDOS:', item.NO_LEIDOS);
    const comentarioMostrar = comentario !== '-' && idHistorico
  ? `<div class="badge-container">
        <button class="btn btn-sm btn-info btn-Ver-Comentario-Rh"
                data-id="${idHistorico}"
                title="Ver comentario">
            <i class="fas fa-comment"></i> Ver
        </button>
        ${noLeidos > 0 ? `<span class="notification-badge ${noLeidos > 9 ? 'wide' : ''}">${noLeidos}</span>` : ''}
    </div>`
  : '<span class="text-muted">—</span>';

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
        <td><small>${item.FECHA_SOLICITUD}</small></td>
        <td><small class="text-muted">${fechaModificacion}</small></td>
        <td><span class="status-badge ${statusClass}">${item.ESTADO_SOLICITUD}</span></td>
        <td><small>${item.RAZON || '—'}</small></td>
        <td class="comentario-cell">${comentarioMostrar}</td>
        <td>
  <div class="actions-container">
    <button class="btn btn-action btn-edit btnCambiarEstado"
      data-id="${item.ID_SOLICITUD}"
      data-tienda="${item.NUM_TIENDA || ''}"
      data-puesto="${item.PUESTO_SOLICITADO || ''}"
      data-razon="${item.RAZON || ''}"
      data-solicitado="${item.SOLICITADO_POR || ''}"
      title="Cambiar Estado">
      <i class="fas fa-exchange-alt"></i> Cambiar Estado
    </button>

${parseInt(item.TIENE_ARCHIVOS) === 1 && parseInt(item.TIENE_SELECCION) === 0 ? (() => {
  const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();
  if (estado.includes('cvs')) {
    return `
      <button class="btn btn-sm btn-info btnVerArchivosRRHH" data-id="${item.ID_SOLICITUD}">
        <i class="fas fa-folder-open"></i> Archivos
      </button>
    `;
  } else if (estado.includes('psico')) {
    return `
      <button class="btn btn-sm btn-warning btnVerArchivosTipo" data-id="${item.ID_SOLICITUD}" data-tipo="PSICOMETRICA">
        <i class="fas fa-brain"></i> Psicométricas
      </button>
    `;
  } else if (estado.includes('poligrafo')) {
    return `
      <button class="btn btn-sm btn-secondary btnVerArchivosTipo" data-id="${item.ID_SOLICITUD}" data-tipo="POLIGRAFO">
        <i class="fas fa-shield-alt"></i> Polígrafo
      </button>
    `;
  } else {
    return '';
  }
})() : ''}

${parseInt(item.TIENE_SELECCION) === 1 ? `
  <button class="btn btn-sm btn-success btnVerResumen" data-id="${item.ID_SOLICITUD}">
    <i class="fas fa-eye"></i> Ver Resumen
  </button>
` : ''}
  </div>
</td>
      </tr>
    `;
    
    console.log("Comentario de solicitud:", item.ID_SOLICITUD, item.COMENTARIO_SOLICITUD);
    tbody.append(row);
  });

  setTimeout(() => {
    $('.estado-selector').each(function () {
      aplicarColorEstado(this);
    });
  }, 0);
}


      //FUNCION PARA FILTRO POR ASESORA DE RRHH
$('#filtroDirigidoA').on('change', function () {
  const filtro = $(this).val();
  localStorage.setItem('filtroDirigidoA', filtro);

  const datosFiltrados = filtro
    ? allSolicitudes.filter(item => item.DIRIGIDO_A === filtro)
    : allSolicitudes;

  currentPage = 1;
  renderTable(datosFiltrados);
});

// FUNCIÓN PARA CONFIGURAR PAGINACIÓN
function setupPagination(data) {
  const totalPages = Math.ceil(data.length / rowsPerPage);
  const pagination = $('.pagination');
  
  console.log("📚 Total de páginas:", totalPages); // ← AGREGAR DEBUG
  console.log("📊 Total de datos:", data.length); // ← AGREGAR DEBUG
  
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

  // Event listeners para paginación - CORREGIDO
  $('.pagination .page-link').off('click').on('click', function (e) {
    e.preventDefault();
    const page = parseInt($(this).data('page'));
    
    console.log("👆 Click en página:", page); // ← AGREGAR DEBUG
    console.log("📄 Página actual:", currentPage); // ← AGREGAR DEBUG
    console.log("📊 Datos disponibles:", data.length); // ← AGREGAR DEBUG
    
    if (page && page !== currentPage && page >= 1 && page <= totalPages) {
      currentPage = page;
      renderTable(data); // ← CAMBIAR: usar 'data' en lugar de 'solicitudes'
      setupPagination(data); // ← CAMBIAR: usar 'data' en lugar de 'solicitudes'
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

//FUNCION CAMBIAR ESTADO DE LA SOLICITUD
// FUNCIÓN PARA CAMBIAR ESTADO - VERSIÓN COMPLETA CORREGIDA
$(document).on('click', '.btnCambiarEstado', function() {
  const $btn = $(this);
  const id = $btn.data('id');
  const tienda = $btn.data('tienda') || 'Sin tienda';
  const puesto = $btn.data('puesto') || 'Sin puesto';
  const razon = $btn.data('razon') || 'Sin razón';
  const solicitado = $btn.data('solicitado') || 'Desconocido';

  if (!id || isNaN(id)) {
    Swal.fire('Error', 'ID de solicitud no válido.', 'error');
    return;
  }

  $('#infoSolicitudCambio').html(`
    <div class="border p-3 rounded bg-light">
      <p class="mb-1"><strong>ID Solicitud:</strong> ${id}</p>
      <p class="mb-1"><strong>Tienda:</strong> ${tienda}</p>
      <p class="mb-1"><strong>Puesto:</strong> ${puesto}</p>
      <p class="mb-1"><strong>Razón:</strong> ${razon}</p>
      <p class="mb-0"><strong>Solicitado Por:</strong> ${solicitado}</p> 
    </div>
  `);

  $('#nuevoEstado').val('').trigger('change');
  $('#comentarioCambio').val('');
  $('#archivosCVS').val('');
  $('#seccionArchivos').hide();
  $('#vistaPreviaArchivos').html('');

  $('#modalCambiarEstado').data('id', id).modal('show');
});

// Mostrar/ocultar sección de archivos según estado seleccionado
$('#nuevoEstado').on('change', function () {
  const estado = $(this).val().toLowerCase();
  const mostrarArchivos =
    estado.includes('cvs') ||
    estado.includes('psico') ||
    estado.includes('poligrafo');

  $('#seccionArchivos').toggle(mostrarArchivos);

  if (mostrarArchivos) {
    if (estado.includes('psicometrica')) {
      $('#label_archivos').text('Archivos de Pruebas Psicométricas:');
    } else if (estado.includes('poligrafo')) {
      $('#label_archivos').text('Archivos de Prueba de Polígrafo:');
    } else {
      $('#label_archivos').text('Archivos de CVS:');
    }
  }

  if (!mostrarArchivos) {
    $('#vistaPreviaArchivos').html('');
    $('#archivosCVS').val('');
  }
});

// Vista previa de archivos con validación
$('#archivosCVS').on('change', function() {
  const files = this.files;
  let preview = '';
  let archivosValidos = true;

  const tiposPermitidos = [
    'application/pdf', 
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png'
  ];
  
  const maxSizeMB = 50;

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
    let status = '';
    
    if (!tiposPermitidos.includes(file.type)) {
      status = `Tipo no permitido (${file.type || 'desconocido'})`;
      archivosValidos = false;
    } else if (file.size > maxSizeMB * 1024 * 1024) {
      status = `Excede ${maxSizeMB}MB (${sizeMB}MB)`;
      archivosValidos = false;
    } else {
      status = `OK (${sizeMB}MB)`;
    }
    
    preview += `<div class="${!archivosValidos ? 'text-danger' : ''}">${status} - ${file.name}</div>`;
  }

  $('#vistaPreviaArchivos').html(preview);
  $('#btnGuardarCambioEstado').prop('disabled', !archivosValidos && files.length > 0);
});

// Guardar cambios en el estado
$('#btnGuardarCambioEstado').on('click', function() {
  const $btn = $(this);
  const id = $('#modalCambiarEstado').data('id');
  const estado = $('#nuevoEstado').val();
  const comentario = $('#comentarioCambio').val().trim();
  const archivos = $('#archivosCVS')[0].files;

  if (!id || isNaN(id)) {
    Swal.fire('Error', 'ID de solicitud inválido.', 'error');
    return;
  }

  if (!estado) {
    Swal.fire('Error', 'Debes seleccionar un estado.', 'warning');
    return $('#nuevoEstado').focus();
  }

  if (!comentario) {
    Swal.fire('Error', 'Debes ingresar un comentario.', 'warning');
    return $('#comentarioCambio').focus();
  }

  const formData = new FormData();
  formData.append('id_solicitud', id);
  formData.append('nuevo_estado', estado);
  formData.append('comentario', comentario);

  const requiereArchivos = estado.toLowerCase().includes('cvs') || estado.toLowerCase().includes('psico') || estado.toLowerCase().includes('poligrafo');
  if (requiereArchivos && archivos.length === 0) {
    Swal.fire('Advertencia', 'Este estado requiere adjuntar archivos.', 'warning');
    return;
  }

  for (let i = 0; i < archivos.length; i++) {
    formData.append('archivos[]', archivos[i]);
  }

  // NUEVO: detectar tipo de archivo y enviarlo
  let tipoArchivo = '';
  if (estado.toLowerCase().includes('psico')) {
    tipoArchivo = 'PSICOMETRICA';
  } else if (estado.toLowerCase().includes('poligrafo')) {
    tipoArchivo = 'POLIGRAFO';
  } else if (estado.toLowerCase().includes('cvs')) {
    tipoArchivo = 'CVS';
  }
  formData.append('tipo_archivo', tipoArchivo);

  $btn.prop('disabled', true);
  const swalLoading = Swal.fire({
    title: 'Procesando...',
    html: 'Por favor espera mientras se guardan los cambios',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=toggle_solicitud_status',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
      let res;
      try {
        res = typeof response === 'string' ? JSON.parse(response) : response;
      } catch (e) {
        console.error('Error parsing response:', response);
        Swal.fire('Error', 'Respuesta del servidor no válida', 'error');
        return;
      }

      if (res.success) {
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: res.mensaje || 'Estado actualizado correctamente',
          timer: 2000,
          showConfirmButton: false
        });
        $('#modalCambiarEstado').modal('hide');

        if (typeof cargarSolicitudes === 'function') {
          cargarSolicitudes();
        }
      } else {
        Swal.fire('Error', res.error || 'Error al actualizar el estado', 'error');
      }
    },
    error: function(xhr, status, error) {
      let errorMsg = 'Error en la solicitud: ' + error;
      if (status === 'timeout') {
        errorMsg = 'La solicitud tardó demasiado tiempo. Intenta nuevamente.';
      }
      Swal.fire('Error', errorMsg, 'error');
      console.error('Error:', xhr.responseText);
    },
    complete: function() {
      $btn.prop('disabled', false);
      swalLoading.close();
    }
  });
});

$(document).on('click', '#btnCancelarCambioEstado', function () {
  $('#modalCambiarEstado').modal('hide');
});




//FUNCION PARA VER LOS CVS SELECCIONADOR POR EL SUPERVISOR
$(document).off('click', '.btnVerResumen').on('click', '.btnVerResumen', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    
    if (modalResumenAbierto) {
        console.warn("🔁 El modal de resumen ya está abierto. Acción cancelada para evitar rebote.");
        return;
    }
    
    if (Swal.isVisible()) {
        Swal.close();
        setTimeout(() => {
            procesarResumen.call(this);
        }, 200);
        return;
    }
    
    procesarResumen.call(this);
    
    function procesarResumen() {
        modalResumenAbierto = true;
        
        const idSolicitud = $(this).data('id');
        console.log("Iniciando solicitud para ID:", idSolicitud);
        
        if (!idSolicitud) {
            console.error("No se encontró ID de solicitud");
            modalResumenAbierto = false;
            Swal.fire('Error', 'No se encontró el ID de la solicitud', 'error');
            return;
        }
        
        const requestData = {
            action: 'ver_resumen_cvs',
            id_solicitud: idSolicitud
        };
        
        // Mostrar loading
        Swal.fire({
            title: '<i class="fas fa-spinner fa-spin"></i> Cargando...',
            html: 'Obteniendo información de documentos seleccionados',
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'animated fadeInDown faster'
            },
            didOpen: () => Swal.showLoading()
        });
        
        // Intento 1: Envío tradicional
        $.ajax({
            url: './gestionhumana/crudsolicitudesrh.php?action=ver_resumen_cvs',
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                handleResponse(response, idSolicitud);
            },
            error: function(xhr) {
                console.warn("Primer intento falló, probando con JSON...");
                sendAsJson();
            }
        });
        
        function sendAsJson() {
            const jsonData = JSON.stringify(requestData);
            
            $.ajax({
                url: './gestionhumana/crudsolicitudesrh.php?action=ver_resumen_cvs',
                type: 'POST',
                data: jsonData,
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    handleResponse(response, idSolicitud);
                },
                error: function(xhr) {
                    modalResumenAbierto = false;
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
    }
    
    function handleResponse(response, idSolicitud) {
        console.log("Respuesta completa:", response);
        
        if (response.success) {
            // Crear modal personalizado con SweetAlert2
            let modalContent = `
                <div class="rrhh-modal-content">
                    <!-- Header del modal -->
                    <div class="modal-header-custom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-star text-warning mr-2" style="font-size: 1.5rem;"></i>
                            <div>
                                <h4 class="mb-0 text-white">CVs Seleccionados - Solicitud #${idSolicitud}</h4>
                                <small class="text-light opacity-75">Revisión de selección realizada por supervisor</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Información del supervisor -->
                    <div class="supervisor-info-card">
                        <div class="d-flex align-items-center">
                            <div class="supervisor-icon mr-3">
                                <i class="fas fa-user-tie" style="font-size: 1.5rem; color: #6f42c1;"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold" style="color: #495057;">
                                    Selección realizada por: ${response.supervisor || 'SUPERVISOR NO ESPECIFICADO'}
                                </div>
                                <div class="d-flex align-items-center text-muted mt-1">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <span>Fecha: ${response.fecha || 'Fecha no disponible'}</span>
                                    <span class="ml-3">
                                        <i class="fas fa-file-alt mr-1"></i>
                                        ${response.archivos ? response.archivos.length : 0} CVs seleccionados
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de CVs -->
                    <div class="cvs-lista">
            `;
            
            if (response.archivos && response.archivos.length > 0) {
                response.archivos.forEach((file, index) => {
                    modalContent += `
                        <div class="cv-item">
                            <div class="d-flex align-items-center">
                                <!-- Indicador de selección -->
                                <div class="selection-indicator mr-3">
                                    <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                </div>
                                
                                <!-- Icono del archivo -->
                                <div class="file-icon-container mr-3">
                                    <i class="fas fa-file-pdf text-danger" style="font-size: 2rem;"></i>
                                </div>
                                
                                <!-- Información del archivo -->
                                <div class="file-info flex-grow-1">
                                    <div class="file-name font-weight-bold">${file.NOMBRE_ARCHIVO}</div>
                                    <div class="file-details d-flex align-items-center text-muted">
                                        <span class="mr-3">
                                            <i class="fas fa-calendar mr-1"></i>
                                            ${file.FECHA || 'N/A'}
                                        </span>
                                        <span class="mr-3">
                                            <i class="fas fa-weight-hanging mr-1"></i>
                                            ${file.TAMAÑO || 'N/A'}
                                        </span>
                                        <span class="badge badge-success">
                                            <i class="fas fa-star mr-1"></i>
                                            Seleccionado por Supervisor
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Botones de acción -->
                                <div class="file-actions">
                                    <button class="btn btn-primary btn-sm btn-ver-cv-rrhh" data-ruta="${file.RUTA || ''}">
                                        <i class="fas fa-eye mr-1"></i> Ver CV
                                    </button>
                                    <button class="btn btn-success btn-sm ml-2 btn-descargar-cv-rrhh" data-ruta="${file.RUTA || ''}" data-nombre="${file.NOMBRE_ARCHIVO}">
                                        <i class="fas fa-download mr-1"></i> Descargar
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                modalContent += `
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No se encontraron documentos</h5>
                        <p class="text-muted">No hay documentos seleccionados para esta solicitud</p>
                    </div>
                `;
            }
            
            modalContent += `
                    </div>
                </div>
            `;
            
            // Cerrar el loading antes de mostrar el nuevo modal
            if (Swal.isVisible()) {
                Swal.close();
            }
            
            // Pequeño delay para evitar conflictos
            setTimeout(() => {
                // Mostrar modal con SweetAlert2
                Swal.fire({
                    html: modalContent,
                    showConfirmButton: false,
                    showCloseButton: true,
                    width: '900px',
                    customClass: {
                        container: 'rrhh-swal-container',
                        popup: 'rrhh-swal-popup',
                        content: 'p-0'
                    },
                    didOpen: () => {
                        // Agregar estilos personalizados
                        if (!document.getElementById('rrhh-modal-styles')) {
                            const style = document.createElement('style');
                            style.id = 'rrhh-modal-styles';
                            style.innerHTML = `
                                .rrhh-swal-container {
                                    z-index: 9999;
                                }
                                .rrhh-swal-popup {
                                    border-radius: 12px !important;
                                    overflow: hidden;
                                }
                                .rrhh-modal-content {
                                    padding: 0;
                                }
                                .modal-header-custom {
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    padding: 20px;
                                    color: white;
                                }
                                .supervisor-info-card {
                                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                                    padding: 20px;
                                    border-bottom: 1px solid #dee2e6;
                                }
                                .cvs-lista {
                                    padding: 20px;
                                    max-height: 400px;
                                    overflow-y: auto;
                                }
                                .cv-item {
                                    background: white;
                                    border: 2px solid #28a745;
                                    border-radius: 12px;
                                    padding: 15px;
                                    margin-bottom: 12px;
                                    transition: all 0.3s ease;
                                    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
                                }
                                .cv-item:hover {
                                    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
                                    transform: translateY(-2px);
                                }
                                .file-icon-container {
                                    width: 50px;
                                    text-align: center;
                                }
                                .file-name {
                                    color: #333;
                                    font-size: 16px;
                                    margin-bottom: 6px;
                                }
                                .file-details {
                                    font-size: 13px;
                                }
                                .file-actions .btn {
                                    font-size: 14px;
                                    font-weight: 600;
                                    padding: 8px 16px;
                                    border-radius: 8px;
                                    transition: all 0.2s;
                                }
                                .file-actions .btn:hover {
                                    transform: translateY(-1px);
                                }
                                .btn-ver-cv-rrhh {
                                    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                                    border: none;
                                    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
                                }
                                .btn-ver-cv-rrhh:hover {
                                    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
                                }
                                .btn-descargar-cv-rrhh {
                                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                                    border: none;
                                    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
                                }
                                .btn-descargar-cv-rrhh:hover {
                                    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
                                }
                                .selection-indicator {
                                    width: 30px;
                                    text-align: center;
                                }
                            `;
                            document.head.appendChild(style);
                        }
                        
                        // Configurar eventos para los botones
                        $(document).off('click', '.btn-ver-cv-rrhh').on('click', '.btn-ver-cv-rrhh', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
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
                        
                        $(document).off('click', '.btn-descargar-cv-rrhh').on('click', '.btn-descargar-cv-rrhh', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
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
                    },
                    willClose: () => {
                        modalResumenAbierto = false;
                    }
                });
            }, 100);
            
        } else {
            modalResumenAbierto = false;
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


     //FUNCION VER ARCHIVOS
// FUNCIÓN GENERAL PARA MOSTRAR ARCHIVOS
function mostrarArchivosSolicitud(id, tipo = 'CVS') {
  // ← AGREGAR VALIDACIÓN DE FLAG
  if (modalArchivosAbierto) {
    console.warn("⚠️ Modal de archivos ya está abierto");
    return;
  }

  if (!id) {
    Swal.fire('Error', 'ID de solicitud no válido', 'error');
    return;
  }

  // ← MARCAR COMO ABIERTO
  modalArchivosAbierto = true;

  // ← CERRAR CUALQUIER MODAL PREVIO
  if (Swal.isVisible()) {
    Swal.close();
    // Pequeño delay para evitar conflictos
    setTimeout(() => {
      abrirModalArchivos(id, tipo);
    }, 200);
    return;
  }

  abrirModalArchivos(id, tipo);
}

// ← SEPARAR LA LÓGICA DEL MODAL EN UNA FUNCIÓN APARTE
function abrirModalArchivos(id, tipo) {
  Swal.fire({
    title: 'Cargando archivos...',
    text: `Obteniendo los archivos más recientes de tipo ${tipo}`,
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    method: 'GET',
    data: {
      action: 'get_archivos',
      id: id,
      tipo: tipo
    },
    dataType: 'json',
    success: function (response) {
      if (!response || !response.archivos || response.archivos.length === 0) {
        Swal.fire({
          icon: 'info',
          title: `Sin archivos ${tipo} recientes`,
          text: `No se encontraron archivos correspondientes al último estado tipo ${tipo}.`,
          confirmButtonText: 'Entendido',
          willClose: () => {
            modalArchivosAbierto = false; // ← RESETEAR FLAG
          }
        });
        return;
      }

      const data = response.archivos;

      let html = `
        <div style="text-align: left;">
          <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="margin: 0 0 10px 0; color: #495057;">
              <i class="fas fa-paperclip mr-2"></i>
              Últimos archivos subidos con estado ${tipo} (#${id})
            </h4>
            <p style="margin: 0; color: #6c757d; font-size: 14px;">
              Total de archivos: <strong>${data.length}</strong>
            </p>
          </div>
          
          <div style="max-height: 400px; overflow-y: auto;">
      `;

      data.forEach((archivo) => {
        const ruta = `./${archivo.NOMBRE_ARCHIVO}`;
        const nombreArchivo = archivo.NOMBRE_ARCHIVO.split('/').pop();
        const fechaSubida = archivo.FECHA_SUBIDA || 'Fecha no disponible';

        const extension = nombreArchivo.split('.').pop().toLowerCase();
        let icono = 'fa-file';
        let tipoArchivo = 'Documento';
        let colorFondo = '#e3f2fd';

        switch (extension) {
          case 'pdf':
            icono = 'fa-file-pdf';
            tipoArchivo = 'PDF';
            colorFondo = '#ffebee';
            break;
          case 'doc':
          case 'docx':
            icono = 'fa-file-word';
            tipoArchivo = 'Word';
            colorFondo = '#e8f5e8';
            break;
          case 'jpg':
          case 'jpeg':
          case 'png':
            icono = 'fa-file-image';
            tipoArchivo = 'Imagen';
            colorFondo = '#fff3e0';
            break;
        }

        html += `
          <div style="background: ${colorFondo}; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
              <div style="flex: 1;">
                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                  <i class="fas ${icono}" style="font-size: 24px; margin-right: 10px;"></i>
                  <div>
                    <h5 style="margin: 0; color: #212529; font-size: 16px; font-weight: 600;">
                      ${nombreArchivo}
                    </h5>
                    <small style="color: #6c757d; font-size: 12px;">
                      ${tipoArchivo} • Subido: ${fechaSubida}
                    </small>
                  </div>
                </div>
              </div>
              <div style="display: flex; gap: 8px; align-items: center;">
                <a href="${ruta}" target="_blank"
                   style="background: #007bff; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                  <i class="fas fa-eye mr-1"></i> Ver
                </a>
                <a href="${ruta}" download="${nombreArchivo}"
                   style="background: #28a745; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                  <i class="fas fa-download mr-1"></i> Descargar
                </a>
              </div>
            </div>
          </div>
        `;
      });

      html += `
          </div>
          <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px; text-align: center;">
            <small style="color: #6c757d;">
              <i class="fas fa-info-circle mr-1"></i>
              Mostrando solo los archivos subidos con el último cambio de estado ${tipo}.
            </small>
          </div>
        </div>
      `;

      Swal.fire({
        title: false,
        html: html,
        width: '700px',
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
        confirmButtonColor: '#6c757d',
        customClass: {
          popup: 'swal-wide-files'
        },
        willClose: () => {
          modalArchivosAbierto = false; // ← RESETEAR FLAG AL CERRAR
        }
      });
    },
    error: function (xhr, status, error) {
      console.error('Error cargando archivos:', error);
      
      Swal.fire({
        icon: 'error',
        title: 'Error al Cargar Archivos',
        text: 'No se pudieron cargar los archivos.',
        footer: 'Intenta nuevamente o contacta al administrador.',
        willClose: () => {
          modalArchivosAbierto = false; // ← RESETEAR FLAG EN ERROR
        }
      });
    }
  });
}

// ESCUCHADORES CON VALIDACIÓN ADICIONAL
$(document).off('click', '.btnVerArchivosRRHH').on('click', '.btnVerArchivosRRHH', function (e) {
  e.preventDefault();
  e.stopImmediatePropagation(); // ← AGREGAR PARA EVITAR PROPAGACIÓN
  
  if (modalArchivosAbierto) return; // ← VALIDACIÓN EXTRA
  
  const id = $(this).data('id');
  mostrarArchivosSolicitud(id, 'CVS');
});

$(document).off('click', '.btnVerArchivosTipo').on('click', '.btnVerArchivosTipo', function (e) {
  e.preventDefault();
  e.stopImmediatePropagation(); // ← AGREGAR PARA EVITAR PROPAGACIÓN
  
  if (modalArchivosAbierto) return; // ← VALIDACIÓN EXTRA
  
  const id = $(this).data('id');
  const tipo = $(this).data('tipo') || 'CVS';
  mostrarArchivosSolicitud(id, tipo);
});



      // FUNCIÓN VER HISTORIAL GENERAL
$(document).on('click', '.btnVerHistorial', function () {
  Swal.fire({
    title: '<i class="fas fa-spinner fa-spin"></i> Cargando historial...',
    text: 'Obteniendo historial general de solicitudes',
    allowOutsideClick: false,
    showConfirmButton: false
  });

  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_historial',
    type: 'GET',
    dataType: 'json',
    success: function (datos) {
      if (!datos || datos.length === 0) {
        Swal.fire({
          icon: 'info',
          title: 'Historial Vacío',
          text: 'No hay cambios registrados en las solicitudes.',
          confirmButtonText: 'Entendido'
        });
        return;
      }

      let tabla = `
        <div style="max-height: 500px; overflow-y: auto;">
          <table class="table table-bordered table-hover table-sm">
            <thead class="thead-dark" style="position: sticky; top: 0; z-index: 10;">
              <tr>
                <th>#</th>
                <th>Solicitud</th>
                <th>Tienda</th>
                <th>Estado Anterior</th>
                <th>Estado Nuevo</th>
                <th>Comentario Anterior</th>
                <th>Comentario Nuevo</th>
                <th>Fecha</th>
                <th>Archivos</th>
              </tr>
            </thead>
            <tbody>`;

      datos.forEach((row, index) => {
        let archivos = 'Sin archivos';
        if (row.ARCHIVOS && Array.isArray(row.ARCHIVOS) && row.ARCHIVOS.length > 0) {
          archivos = `<div class="btn-group" role="group">`;
          row.ARCHIVOS.forEach(a => {
            const nombre = a.NOMBRE_ARCHIVO.split('/').pop();
            archivos += `
              <a href="./${a.NOMBRE_ARCHIVO}" target="_blank" class="btn btn-sm btn-primary mb-1">
                <i class="fas fa-eye"></i> Ver
              </a>
              <a href="./${a.NOMBRE_ARCHIVO}" download="${nombre}" class="btn btn-sm btn-success mb-1">
                <i class="fas fa-download"></i> Descargar
              </a>`;
          });
          archivos += `</div>`;
        }

        tabla += `<tr>
                    <td>${index + 1}</td>
                    <td><span class="badge badge-info">${row.ID_SOLICITUD || '—'}</span></td>
                    <td><span class="badge badge-primary">${row.NUM_TIENDA || '—'}</span></td>
                    <td>${row.ESTADO_ANTERIOR || '—'}</td>
                    <td><strong>${row.ESTADO_NUEVO || '—'}</strong></td>
                    <td><small>${row.COMENTARIO_ANTERIOR || '—'}</small></td>
                    <td><small>${row.COMENTARIO_NUEVO || '—'}</small></td>
                    <td><small>${row.FECHA_CAMBIO || '—'}</small></td>
                    <td>${archivos}</td>
                  </tr>`;
      });

      tabla += '</tbody></table></div>';

      Swal.fire({
        title: '<i class="fas fa-history"></i> Historial General de Solicitudes',
        html: tabla,
        width: '95%',
        customClass: { popup: 'swal-wide' },
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
        footer: `
          <a href="../Page/gestionhumana/reporte_historial_pdf.php" target="_blank" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Generar PDF
          </a>
        `
      });
    },
    error: function () {
      Swal.fire({
        icon: 'error',
        title: 'Error de Conexión',
        text: 'No se pudo cargar el historial general.',
        confirmButtonText: 'Reintentar'
      });
    }
  });
});


      // FUNCIÓN HISTORIAL INDIVIDUAL
$(document).on('click', '.btn-ver-historial', function () {
  const idSolicitud = $(this).data('id');

  if (!Number.isInteger(Number(idSolicitud))) {
    Swal.fire('Error', 'ID de solicitud inválido.', 'error');
    return;
  }

  $('#btnPdfIndividual').attr('href', './gestionhumana/reporte_historial_pdf.php?id=' + idSolicitud);
  $('#modalHistorialIndividual').modal('show');
  $('#contenidoHistorial').html('<div class="text-center">Cargando historial...</div>');

  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_historial_individual&id=' + idSolicitud,
    method: 'GET',
    success: function (datos) {
      if (datos.length === 0) {
        $('#contenidoHistorial').html('<div class="text-center text-muted">No hay historial para esta solicitud.</div>');
        return;
      }

      let html = `
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="thead-dark">
              <tr>
                <th>#</th>
                <th>Tienda</th>
                <th>Estado Anterior</th>
                <th>Estado Nuevo</th>
                <th>Comentario Anterior</th>
                <th>Comentario Nuevo</th>
                <th>Fecha</th>
                <th>Archivos</th>
              </tr>
            </thead>
            <tbody>`;

      datos.forEach((h, index) => {
        let archivos = 'Sin archivos';
        if (h.ARCHIVOS && Array.isArray(h.ARCHIVOS) && h.ARCHIVOS.length > 0) {
          archivos = `
            <div class="btn-group" role="group">`;
          h.ARCHIVOS.forEach(a => {
            const nombre = a.NOMBRE_ARCHIVO.split('/').pop();
            archivos += `
              <a href="./${a.NOMBRE_ARCHIVO}" target="_blank" class="btn btn-sm btn-primary mb-1">
                <i class="fas fa-eye"></i> Ver
              </a>
              <a href="./${a.NOMBRE_ARCHIVO}" download="${nombre}" class="btn btn-sm btn-success mb-1">
                <i class="fas fa-download"></i> Descargar
              </a>`;
          });
          archivos += `</div>`;
        }

        html += `<tr>
          <td>${index + 1}</td>
          <td>${h.NUM_TIENDA || '-'}</td>
          <td>${h.ESTADO_ANTERIOR || '-'}</td>
          <td>${h.ESTADO_NUEVO || '-'}</td>
          <td>${h.COMENTARIO_ANTERIOR || '-'}</td>
          <td>${h.COMENTARIO_NUEVO || '-'}</td>
          <td>${h.FECHA_CAMBIO || '-'}</td>
          <td>${archivos}</td>
        </tr>`;
      });

      html += '</tbody></table></div>';
      $('#contenidoHistorial').html(html);
    },
    error: function () {
      $('#contenidoHistorial').html('<div class="alert alert-danger">Error al cargar historial.</div>');
    }
  });
});


 // FUNCION VER COMENTARIO
$(document).off('click', '.btn-Ver-Comentario-Rh').on('click', '.btn-Ver-Comentario-Rh', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (chatAbierto) return;
        chatAbierto = true;
        const idHistorico = $(this).data('id');
        console.log("🔍 ID Histórico para chat:", idHistorico);
        
        if (!idHistorico) {
            console.error("No se encontró ID histórico");
            Swal.fire('Error', 'No se encontró el ID del histórico', 'error');
            return;
        }

        function mostrarChat(mensajes) {
            console.log("📝 Mostrando chat con", mensajes.length, "mensajes");
            
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
                    const esRRHH = rol.includes('rrhh');
                    const remitente = esRRHH ? 'RRHH' : 'SUPERVISOR';

                    if (esRRHH) {
                        // Mensaje de RRHH (derecha, azul)
                        chatHtml += `
                            <div style="
                                display: flex;
                                justify-content: flex-end;
                                margin-bottom: 15px;
                            ">
                                <div style="
                                    max-width: 70%;
                                    background: linear-gradient(135deg, #4285f4 0%, #1976d2 100%);
                                    color: white;
                                    padding: 12px 16px;
                                    border-radius: 18px 18px 4px 18px;
                                    box-shadow: 0 2px 8px rgba(66, 133, 244, 0.3);
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
                        // Mensaje del supervisor (izquierda, gris)
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
                        onfocus="this.style.borderColor='#4285f4'"
                        onblur="this.style.borderColor='#ddd'"
                    ></textarea>
                </div>
            `;

            // Obtener nombre del supervisor desde la fila de la tabla
            const filaActual = $(`button[data-id="${idHistorico}"]`).closest('tr');
            const nombreSupervisor = filaActual.find('td:nth-child(4)').text().trim() || 'Supervisor'; 
            
            Swal.fire({
                title: `<i class="fas fa-comments"></i> ${nombreSupervisor}`,
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
                                background: linear-gradient(135deg, #4285f4 0%, #1976d2 100%) !important;
                                border: none !important;
                                border-radius: 8px !important;
                                padding: 10px 20px !important;
                                font-weight: 600 !important;
                                transition: transform 0.2s !important;
                            }
                            .chat-send-button:hover {
                                transform: translateY(-1px) !important;
                                box-shadow: 0 4px 12px rgba(66, 133, 244, 0.4) !important;
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

                    // CORREGIDO - Usar nombres fijos según el contexto de RRHH
                      const nombreRRHH = filaActual.find('td:nth-child(5)').text().trim() || 'RRHH'; 
                      const nombreSupervisor = 'SUPERVISOR';
                      const esRRHH = true; // Cambiar nombre de variable y valor porque estás en el lado de RRHH
                      const remitente = nombreRRHH; // El remitente es RRHH

                    $.ajax({
                        url: './gestionhumana/crudsolicitudesrh.php?action=guardar_respuesta_chat_rh',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id_historico: idHistorico,
                            mensaje: nuevoMensaje,
                            rol: 'RRHH',
                            remitente: remitente
                        },
                        success: function (response) {
                            console.log("Respuesta del servidor:", response);
                            if (response && response.success) {
                                cargarMensajesChat(idHistorico);
                                actualizarBadgesSilenciosamenteRH(); 
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
                url: './gestionhumana/crudsolicitudesrh.php?action=get_comentarios_chat_rh',
                type: 'POST',
                dataType: 'json',
                data: { id_historico: idHistorico },
                success: function (response) {
                    console.log('Respuesta del servidor:', response);
                    if (response && response.success) {
                      $.ajax({
                          url: './gestionhumana/crudsolicitudesrh.php?action=marcar_mensajes_leidos_rh',
                          type: 'POST',
                          data: { id_historico: idHistorico }
                      });
                        mostrarChat(response.mensajes);
                    } else {
                        console.error("Error en respuesta:", response?.error);
                        Swal.fire('Error', response?.error || 'Error al cargar mensajes', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al cargar chat:', xhr.responseText);
                    Swal.fire('Error', 'Error al cargar el chat: ' + error, 'error');
                }
            });
        }

        cargarMensajesChat(idHistorico);
        actualizarBadgesSilenciosamenteRH();
        chatAbierto = false;
        });

    // FUNCIÓN PARA ACTUALIZAR SOLO LOS BADGES SIN RUIDO VISUAL
    function actualizarBadgesSilenciosamenteRH() {
        $.ajax({
            url: './gestionhumana/crudsolicitudesrh.php?action=get_solicitudes',
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
                                const btnComentario = fila.find('.btn-Ver-Comentario-Rh').parent();
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

      // CARGAR SOLICITUDES AL INICIO
      cargarSolicitudes();
    });

  </script>
</body>
</html>
