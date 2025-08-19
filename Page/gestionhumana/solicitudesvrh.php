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

.status-badge.estado-contratada {
  background: linear-gradient(to right, #388E3C, #81C784);
  color: white;
}

.status-badge.estado-prueba {
  background: linear-gradient(to right, #E64A19, #FF8A65);
  color: white;
}

.status-badge.estado-pendiente {
  background: linear-gradient(to right, #FFB300, #FFD54F);
  color: #1c1c1c;
}

/* =======================
   RESPONSIVE TABLE → CARDS
   ======================= */
@media (max-width: 991.98px) { /* < LG */
  /* layout general */
  .header-section .d-flex { flex-wrap: wrap; }
  #filtroDirigidoA { width:100% !important; display:block !important; margin:12px 0 0 !important; }
  .btn-expand { width:32px; height:32px; }

  /* convierte tabla en bloques */
  .table-container { overflow: visible; } /* dejamos que crezca vertical */
  table.table-modern thead { display: none; }
  table.table-modern { border: 0 !important; }
  table.table-modern tbody, 
  table.table-modern tr, 
  table.table-modern td { display: block; width: 100%; }

  table.table-modern tbody tr {
    margin: 10px 0 16px;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0,0,0,.06);
    background: #fff;
    padding: 10px 10px 6px;
  }

  table.table-modern td {
    /* cada "celda" es una línea con etiqueta a la izquierda y valor a la derecha */
    position: relative;
    padding: 10px 12px 10px 46%;
    text-align: right;         /* valor a la derecha */
    border: 0;
    border-bottom: 1px dashed #f0f2f4;
    font-size: 0.95rem;
  }
  table.table-modern td:last-child {
    border-bottom: 0;
    padding-bottom: 4px;
  }

  /* etiqueta (nombre de la columna) tomada de data-label */
  table.table-modern td::before {
    content: attr(data-label);
    position: absolute;
    left: 12px;
    top: 10px;
    width: 41%;
    text-align: left;
    font-weight: 700;
    color: #6c757d;
    text-transform: uppercase;
    font-size: .72rem;
    letter-spacing: .3px;
    white-space: normal;
  }

  /* acomodos */
  .actions-container { justify-content: flex-start; gap: 6px; }
  .status-badge { width: 100%; max-width: none; margin-top: 6px; }
  .comentario-cell { max-width: none; white-space: normal; }
}

/* gap para Bootstrap 4 (no soporta gap en flex) */
.d-flex.gap-3 > * { margin-right: 12px; }
.d-flex.gap-3 > *:last-child { margin-right: 0; }

@media (max-width: 576px) {
  .main-container { margin: 8px; padding: 16px; }
  .header-title { font-size: 1.6rem; }
  .search-container .input-group + .input-group { margin-top: 8px; }
  .btn-custom { width:100%; margin-bottom:10px; }
  .badge.badge-light.p-3 { display:block; margin-top:10px; }
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
              <th width="160">Puesto</th>
              <th width="155">Supervisor</th>
              <th width="155">Aprobado por</th>
              <th width="155">Asignado a</th>
              <th width="120">Fecha Solicitud</th>
              <th width="150">Modificación registrada</th>
              <th width="160">Estado</th>
              <th width="130">Estado de Aprobacion</th>
              <th width="150">Razón</th>
              <th width="20">Comentario</th>
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
            // ✅ FUNCIÓN PARA GENERAR PDF
                        function generarReportePDF(idSolicitud) {
                            console.log('Generando PDF para solicitud:', idSolicitud);
                            
                            Swal.fire({
                                title: '<i class="fas fa-spinner fa-spin"></i> Generando PDF...',
                                text: 'Creando reporte de observaciones',
                                allowOutsideClick: false,
                                showConfirmButton: false
                            });
                            
                            // Crear URL para generar PDF
                            const urlPDF = `./gestionhumana/reporte_observaciones_pdf.php?id_solicitud=${idSolicitud}`;
                            
                            // Abrir en nueva ventana
                            setTimeout(() => {
                                window.open(urlPDF, '_blank');
                                Swal.fire({
                                    icon: 'success',
                                    title: '<i class="fas fa-file-pdf"></i> PDF Generado',
                                    text: 'El reporte se ha abierto en una nueva ventana',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }, 1000);
                        }

                        // FUNCIÓN PARA ENVIAR AVAL A GERENCIA

function enviarAvalGerencia(idSolicitud) {
    console.log('🚀 Iniciando envío de aval para solicitud:', idSolicitud);
    
    // ✅ EXTRAER NOMBRE DE RRHH USANDO MÉTODO DIRECTO (como en supervisión)
    let nombreRRHH = '';
    
    try {
        // Método 1: Buscar el botón que llamó esta función y extraer de su fila
        const filaActual = $(`button[onclick*="enviarAvalGerencia(${idSolicitud})"]`).closest('tr');
        
        if (filaActual.length > 0) {
            console.log('✅ Fila encontrada con método 1');
            nombreRRHH = filaActual.find('td:nth-child(6)').text().trim() || 'RRHH';
            console.log('👤 Nombre RRHH extraído:', nombreRRHH);
        } else {
            console.log('⚠️ Método 1 falló, intentando método 2...');
            
            // Método 2: Buscar en toda la tabla por ID de solicitud
            $('tbody tr').each(function() {
                const filaTexto = $(this).text();
                if (filaTexto.includes(idSolicitud.toString())) {
                    nombreRRHH = $(this).find('td:nth-child(6)').text().trim() || 'RRHH';
                    console.log('👤 Nombre RRHH extraído (método 2):', nombreRRHH);
                    return false; // break del each
                }
            });
        }
        
        // Método 3: Si aún no encuentra, buscar usando event.target
        if (!nombreRRHH || nombreRRHH === '' || nombreRRHH === 'RRHH') {
            console.log('⚠️ Intentando método 3 con event...');
            
            // Buscar cualquier botón relacionado con esta solicitud
            $(`button[onclick*="${idSolicitud}"]`).each(function() {
                const fila = $(this).closest('tr');
                if (fila.length > 0) {
                    const nombre = fila.find('td:nth-child(6)').text().trim();
                    if (nombre && nombre !== '' && nombre !== 'RRHH') {
                        nombreRRHH = nombre;
                        console.log('👤 Nombre RRHH extraído (método 3):', nombreRRHH);
                        return false; // break
                    }
                }
            });
        }
        
    } catch (error) {
        console.error('❌ Error extrayendo nombre RRHH:', error);
    }
    
    // Fallback si no encuentra el nombre
    if (!nombreRRHH || nombreRRHH === '' || nombreRRHH === 'RRHH') {
        nombreRRHH = 'Emma de Cea'; // Nombre por defecto o el que tú uses
        console.log('⚠️ Usando nombre fallback:', nombreRRHH);
    }
    
    console.log('👤 Nombre RRHH final:', nombreRRHH);
    
    // ✅ CERRAR CUALQUIER MODAL EXISTENTE PRIMERO
    if (Swal.isVisible()) {
        Swal.close();
    }
    
    setTimeout(() => {
        Swal.fire({
            title: '<i class="fas fa-user-tie"></i> Enviar a Aval Gerencia',
            html: `
                <div style="text-align: left; font-family: 'Segoe UI', sans-serif;">
                    <!-- Header informativo -->
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                        <h4 style="margin: 0 0 10px 0; font-weight: 600;">
                            <i class="fas fa-info-circle mr-2"></i>Completar información para aprobación gerencial
                        </h4>
                        <div style="opacity: 0.9; font-size: 14px;">
                            Esta solicitud será enviada al gerente para su aval final
                        </div>
                        <div style="opacity: 0.8; font-size: 13px; margin-top: 8px;">
                            <i class="fas fa-user mr-1"></i> Enviado por: <strong>${nombreRRHH}</strong>
                        </div>
                    </div>

                    <!-- Información de la solicitud -->
                    <div style="background: #f8f9fa; border-left: 4px solid #28a745; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #495057;">Solicitud:</strong> 
                            <span style="color: #28a745; font-weight: 600;">#${idSolicitud}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong style="color: #495057;">Acción:</strong> 
                            <span style="color: #6c757d;">Se notificará al gerente para aprobación final</span>
                        </div>
                        <div>
                            <strong style="color: #495057;">Estado tras envío:</strong> 
                            <span style="background: #fff3cd; color: #856404; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                PENDIENTE AVAL GERENCIA
                            </span>
                        </div>
                    </div>

                    <!-- Documentos requeridos -->
                    <div style="margin-bottom: 25px;">
                        <h5 style="margin: 0 0 15px 0; color: #495057; font-weight: 600;">
                            <i class="fas fa-file-upload mr-2" style="color: #007bff;"></i>
                            Documentos Requeridos
                        </h5>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <!-- PDF Reporte -->
                            <div>
                                <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fas fa-file-pdf mr-1" style="color: #dc3545;"></i>
                                    Reporte del Día de Prueba (PDF)
                                </label>
                                <div style="border: 2px dashed #dee2e6; border-radius: 8px; padding: 15px; text-align: center; background: #f8f9fa; cursor: pointer;" onclick="document.getElementById('pdfFileAval').click()">
                                    <input type="file" id="pdfFileAval" accept=".pdf" style="display: none;" onchange="previewFile('pdf')">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #6c757d; margin-bottom: 8px;"></i>
                                    <div style="color: #6c757d; font-size: 14px;">Subir PDF</div>
                                    <div style="color: #adb5bd; font-size: 12px;">Clic para seleccionar</div>
                                </div>
                                <div id="pdfPreviewAval" style="margin-top: 8px; padding: 8px; background: #d1ecf1; border-radius: 5px; display: none;">
                                    <small style="color: #0c5460;"><i class="fas fa-file-pdf mr-1"></i><span id="pdfNameAval"></span></small>
                                </div>
                            </div>
                            
                            <!-- CV -->
                            <div>
                                <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 14px;">
                                    <i class="fas fa-file-alt mr-1" style="color: #007bff;"></i>
                                    Curriculum Vitae (PDF)
                                </label>
                                <div style="border: 2px dashed #dee2e6; border-radius: 8px; padding: 15px; text-align: center; background: #f8f9fa; cursor: pointer;" onclick="document.getElementById('cvFileAval').click()">
                                    <input type="file" id="cvFileAval" accept=".pdf" style="display: none;" onchange="previewFile('cv')">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #6c757d; margin-bottom: 8px;"></i>
                                    <div style="color: #6c757d; font-size: 14px;">Subir CV</div>
                                    <div style="color: #adb5bd; font-size: 12px;">Formato PDF únicamente</div>
                                </div>
                                <div id="cvPreviewAval" style="margin-top: 8px; padding: 8px; background: #d1ecf1; border-radius: 5px; display: none;">
                                    <small style="color: #0c5460;"><i class="fas fa-file-alt mr-1"></i><span id="cvNameAval"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 600; color: #495057; margin-bottom: 8px;">
                            <i class="fas fa-comment-alt mr-2" style="color: #ffc107;"></i>
                            Comentarios para Gerencia (Obligatorio)
                        </label>
                        <textarea 
                            id="comentarioAval" 
                            placeholder="Agregar información adicional que considere relevante para la evaluación gerencial..."
                            style="
                                width: 100%; 
                                min-height: 100px; 
                                padding: 12px 16px; 
                                border: 2px solid #dee2e6; 
                                border-radius: 8px; 
                                font-family: inherit;
                                font-size: 14px;
                                resize: vertical;
                                transition: border-color 0.3s ease;
                            "
                            onfocus="this.style.borderColor='#007bff'"
                            onblur="this.style.borderColor='#dee2e6'"
                            maxlength="500"
                            oninput="updateCharCount()"
                        ></textarea>
                        <div style="text-align: right; margin-top: 5px;">
                            <small style="color: #6c757d;">
                                <span id="charCountAval">0</span>/500 caracteres
                            </small>
                        </div>
                    </div>

                    <!-- Advertencia -->
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-top: 20px;">
                        <div style="display: flex; align-items: flex-start;">
                            <i class="fas fa-exclamation-triangle" style="color: #856404; margin-right: 10px; margin-top: 2px;"></i>
                            <div style="color: #856404; font-size: 13px; line-height: 1.4;">
                                <strong>Importante:</strong> Una vez enviada, la solicitud quedará pendiente de aval gerencial y no podrá ser modificada hasta recibir respuesta.
                            </div>
                        </div>
                    </div>
                </div>
            `,
            width: '800px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane"></i> Enviar a Gerencia',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            allowOutsideClick: false,
            allowEscapeKey: true,
            preConfirm: () => {
                const comentario = document.getElementById('comentarioAval').value.trim();
                const pdfFile = document.getElementById('pdfFileAval').files[0];
                const cvFile = document.getElementById('cvFileAval').files[0];
                
                // ✅ VALIDACIONES
                if (!pdfFile) {
                    Swal.showValidationMessage('Debe adjuntar el reporte PDF del día de prueba');
                    return false;
                }
                
                if (!cvFile) {
                    Swal.showValidationMessage('Debe adjuntar el CV del candidato');
                    return false;
                }
                
                if (pdfFile.type !== 'application/pdf') {
                    Swal.showValidationMessage('El reporte debe ser un archivo PDF');
                    return false;
                }
                
                if (cvFile.type !== 'application/pdf') {
                    Swal.showValidationMessage('El CV debe ser un archivo PDF');
                    return false;
                }
                
                if (pdfFile.size > 10 * 1024 * 1024) {
                    Swal.showValidationMessage('El archivo PDF del reporte es demasiado grande (máx 10MB)');
                    return false;
                }
                
                if (cvFile.size > 10 * 1024 * 1024) {
                    Swal.showValidationMessage('El archivo CV es demasiado grande (máx 10MB)');
                    return false;
                }
                
                return { 
                    comentario, 
                    pdfFile, 
                    cvFile,
                    nombreRRHH: nombreRRHH
                };
            },
            didOpen: () => {
                // Funciones auxiliares
                window.previewFile = function(type) {
                    const fileInput = document.getElementById(type + 'FileAval');
                    const preview = document.getElementById(type + 'PreviewAval');
                    const nameSpan = document.getElementById(type + 'NameAval');
                    
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        nameSpan.textContent = file.name;
                        preview.style.display = 'block';
                    } else {
                        preview.style.display = 'none';
                    }
                };
                
                window.updateCharCount = function() {
                    const textarea = document.getElementById('comentarioAval');
                    const counter = document.getElementById('charCountAval');
                    const count = textarea.value.length;
                    counter.textContent = count;
                    
                    if (count > 450) {
                        counter.style.color = '#dc3545';
                    } else if (count > 400) {
                        counter.style.color = '#ffc107';
                    } else {
                        counter.style.color = '#6c757d';
                    }
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const { comentario, pdfFile, cvFile, nombreRRHH } = result.value;
                
                console.log('🚀 Iniciando envío AJAX...');
                console.log('👤 Nombre RRHH final a enviar:', nombreRRHH);
                
                // ✅ CREAR FORMDATA PARA ENVÍO
                const formData = new FormData();
                formData.append('id_solicitud', idSolicitud);
                formData.append('comentario', comentario);
                formData.append('pdf_reporte', pdfFile);
                formData.append('cv_candidato', cvFile);
                formData.append('enviado_por', nombreRRHH); // ← AQUÍ VA EL NOMBRE REAL
                
                // ✅ MOSTRAR LOADING
                Swal.fire({
                    title: '<i class="fas fa-spinner fa-spin"></i> Enviando a Gerencia...',
                    html: `
                        <div style="text-align: center; padding: 20px;">
                            <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <strong>Solicitud:</strong> #${idSolicitud}<br>
                                <strong>Documentos:</strong> ${pdfFile.name}, ${cvFile.name}<br>
                                <strong>Enviado por:</strong> ${nombreRRHH}
                            </div>
                            <div style="color: #6c757d; font-size: 14px;">
                                Procesando documentos y notificando al gerente...
                            </div>
                        </div>
                    `,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // AJAX
                $.ajax({
                    url: './gestionhumana/crudsolicitudesrh.php?action=enviar_aval_gerencia',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log('✅ Response:', response);
                        
                        let parsedResponse;
                        try {
                            parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (parsedResponse.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Enviado a Gerencia!',
                                    html: `
                                        <div style="background: #d4edda; padding: 15px; border-radius: 8px;">
                                            <strong>Solicitud #${idSolicitud} enviada exitosamente</strong><br>
                                            <strong>Enviado por:</strong> ${nombreRRHH}<br>
                                            <strong>Candidato:</strong> ${parsedResponse.data?.candidato || 'No especificado'}
                                        </div>
                                    `,
                                    timer: 4000,
                                    confirmButtonText: 'Entendido'
                                }).then(() => {
                                    if (typeof cargarSolicitudes === 'function') {
                                        cargarSolicitudes();
                                    } else {
                                        location.reload();
                                    }
                                });
                            } else {
                                Swal.fire('Error', parsedResponse.error, 'error');
                            }
                        } catch (parseError) {
                            console.error('Error parseando JSON:', parseError);
                            Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error AJAX:', error);
                        Swal.fire('Error', `Error de conexión: ${error}`, 'error');
                    }
                });
            }
        });
    }, 300);
}

//VER RESULTADO DE AVAL GERENCIA OPERACIONAL 

// 🎯 FUNCIÓN PARA CARGAR Y MOSTRAR RESULTADO DEL AVAL
function cargarResultadoAvalRH(idSolicitud, tienda, puesto, supervisor, razon) {
  // Mostrar loading
  Swal.fire({
    title: '<i class="fas fa-spinner fa-spin"></i> Cargando resultado...',
    text: 'Obteniendo información de la decisión gerencial',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  // Obtener datos del backend
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    method: 'GET',
    data: {
      action: 'obtener_resultado_aval_recursos',
      id_solicitud: idSolicitud
    },
    dataType: 'json',
    success: function(response) {
      Swal.close(); // Cerrar loading
      
      if (response.success) {
        mostrarModalResultadoAval(response.data, idSolicitud, tienda, puesto, supervisor, razon);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: response.error || 'No se pudo cargar el resultado del aval'
        });
      }
    },
    error: function(xhr, status, error) {
      Swal.close(); // Cerrar loading
      
      console.error('Error AJAX:', xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar al servidor para obtener el resultado'
      });
    }
  });
}

// 🎯 FUNCIÓN PARA MOSTRAR EL MODAL DEL RESULTADO
function mostrarModalResultadoAvalRH(data, idSolicitud, tienda, puesto, supervisor, razon) {
  const solicitud = data.solicitud || {};
  const aval = data.aval || {};
  
  // Determinar si fue aprobado o rechazado
  const esAprobado = aval.decision === 'APROBADO';
  const decision = esAprobado ? 'APROBADO' : 'RECHAZADO';
  
  // Configurar colores y textos según la decisión
  const config = esAprobado ? {
    color: '#2ecc71',
    bgColor: '#d4edda',
    borderColor: '#c3e6cb',
    textColor: '#155724',
    icon: 'fas fa-check-circle',
    titulo: 'Solicitud Aprobada',
    subtitulo: 'Su solicitud ha sido revisada por el gerente y ha sido aprobada',
    estadoBadge: 'APROBADA',
    badgeClass: 'badge-success'
  } : {
    color: '#e74c3c',
    bgColor: '#f8d7da',
    borderColor: '#f1b0b7',
    textColor: '#721c24',
    icon: 'fas fa-times-circle',
    titulo: 'Solicitud Rechazada',
    subtitulo: 'Su solicitud ha sido revisada por el gerente y no ha sido aprobada',
    estadoBadge: 'RECHAZADA',
    badgeClass: 'badge-danger'
  };

  // Próximos pasos según la decisión
  const proximosPasos = esAprobado ? [
    '<i class="fas fa-check-circle"></i> La solicitud continuará con el proceso normal de contratación',
    '<i class="fas fa-arrow-right"></i> RH procederá con los siguientes pasos del proceso',
    '<i class="fas fa-bell"></i> Se notificará cuando haya actualizaciones del estado'
  ] : [
    '<i class="fas fa-clipboard-list"></i> Puede revisar el motivo del rechazo para entender las razones de la decisión',
    '<i class="fas fa-redo"></i> Si considera necesario, puede crear una nueva solicitud corrigiendo los aspectos mencionados',
    '<i class="fas fa-comments"></i> Para dudas adicionales, puede contactar directamente con el gerente para aclaraciones'
  ];

  Swal.fire({
    title: `
      <div class="resultado-header" style="background: ${config.bgColor}; border: 2px solid ${config.borderColor}; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
          <div style="width: 60px; height: 60px; background: ${config.color}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
            <i class="${config.icon}"></i>
          </div>
          <div style="text-align: left;">
            <h3 style="margin: 0; color: ${config.textColor}; font-weight: 600;">${config.titulo}</h3>
            <p style="margin: 5px 0 0 0; color: ${config.textColor}; font-size: 14px;">${config.subtitulo}</p>
          </div>
        </div>
      </div>
    `,
    html: `
      <style>
        .info-section {
          background: #f8f9fa;
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 20px;
          border-left: 4px solid #007bff;
        }
        .info-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 15px;
          margin-bottom: 15px;
        }
        .info-item {
          display: flex;
          flex-direction: column;
          gap: 5px;
        }
        .info-label {
          font-weight: 600;
          color: #495057;
          font-size: 12px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }
        .info-value {
          color: #212529;
          font-size: 14px;
          font-weight: 500;
        }
        .decision-section {
          background: ${config.bgColor};
          border: 2px solid ${config.borderColor};
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 20px;
        }
        .decision-badge {
          display: inline-block;
          padding: 8px 20px;
          border-radius: 25px;
          font-weight: 600;
          font-size: 14px;
          text-transform: uppercase;
          letter-spacing: 1px;
        }
        .badge-success {
          background: #2ecc71;
          color: white;
        }
        .badge-danger {
          background: #e74c3c;
          color: white;
        }
        .motivo-section {
          background: #fff3cd;
          border: 2px solid #ffc107;
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 20px;
        }
        .motivo-content {
          background: white;
          padding: 15px;
          border-radius: 8px;
          border-left: 4px solid #ffc107;
          font-style: italic;
          line-height: 1.6;
          color: #856404;
        }
        .pasos-section {
          background: #e8f4f8;
          border: 2px solid #17a2b8;
          border-radius: 10px;
          padding: 20px;
        }
        .paso-item {
          display: flex;
          align-items: flex-start;
          gap: 10px;
          margin-bottom: 10px;
          padding: 8px;
          background: white;
          border-radius: 6px;
        }
        .paso-item:last-child {
          margin-bottom: 0;
        }
        .section-title {
          font-weight: 600;
          color: #2c3e50;
          margin-bottom: 15px;
          display: flex;
          align-items: center;
          gap: 8px;
        }
        .fecha-decision {
          text-align: center;
          color: #6c757d;
          font-size: 12px;
          margin-top: 15px;
          padding-top: 15px;
          border-top: 1px solid #dee2e6;
        }
      </style>
      
      <div style="text-align: left; max-height: 600px; overflow-y: auto; padding: 0 10px;">
        
        <!-- INFORMACIÓN DE LA SOLICITUD -->
        <div class="info-section">
          <h6 class="section-title">
            <i class="fas fa-info-circle"></i> Información de la Solicitud
          </h6>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label"><i class="fas fa-hashtag"></i> ID Solicitud</span>
              <span class="info-value">#${solicitud.id}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-store"></i> Tienda</span>
              <span class="info-value">Tienda ${solicitud.tienda}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-briefcase"></i> Puesto Solicitado</span>
              <span class="info-value">${solicitud.puesto}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-calendar-alt"></i> Fecha de Solicitud</span>
              <span class="info-value">${solicitud.fecha_solicitud}</span>
            </div>
          </div>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label"><i class="fas fa-user-tie"></i> Supervisor</span>
              <span class="info-value">${solicitud.supervisor}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-edit"></i> Razón de la Vacante</span>
              <span class="info-value">${solicitud.razon}</span>
            </div>
          </div>
        </div>

        <!-- ESTADO DE APROBACIÓN -->
        <div class="decision-section">
          <h6 class="section-title" style="color: ${config.textColor};">
            <i class="fas fa-gavel"></i> Estado de Aprobación
          </h6>
          <div style="text-align: center; margin-bottom: 15px;">
            <span class="decision-badge ${config.badgeClass}">${config.estadoBadge}</span>
          </div>
          <div style="text-align: center; color: ${config.textColor};">
            <strong>Revisado por:</strong> ${aval.gerente}<br>
            <small>Fecha de decisión: ${aval.fecha_decision}</small>
          </div>
        </div>

        <!-- MOTIVO DE LA DECISIÓN -->
        <div class="motivo-section">
          <h6 class="section-title" style="color: #856404;">
            <i class="fas fa-comment-alt"></i> ${esAprobado ? 'Comentarios del Gerente' : 'Motivo del Rechazo'}
          </h6>
          <div class="motivo-content">
            ${aval.comentario || 'Sin comentarios adicionales'}
          </div>
        </div>

        <!-- PRÓXIMOS PASOS -->
        <div class="pasos-section">
          <h6 class="section-title" style="color: #17a2b8;">
            <i class="fas fa-route"></i> Próximos Pasos
          </h6>
          ${proximosPasos.map(paso => `
            <div class="paso-item">
              <span style="flex: 1; color: #495057;">${paso}</span>
            </div>
          `).join('')}
        </div>

        <div class="fecha-decision">
          <i class="fas fa-clock"></i> Última actualización: ${aval.fecha_decision}
        </div>

      </div>
    `,
    width: '800px',
    showCancelButton: false,
    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
    confirmButtonColor: config.color,
    customClass: {
      popup: 'resultado-aval-popup'
    }
  });
}

// 🎯 FUNCIÓN PARA AGREGAR EL BOTÓN EN LAS TABLAS
function agregarBotonResultadoAval(idSolicitud, tienda, puesto, supervisor, razon) {
  return `
    <button class="btn btn-info btn-sm btnVerResultadoAval" 
            data-id="${idSolicitud}"
            data-tienda="${tienda}"
            data-puesto="${puesto}"
            data-supervisor="${supervisor}"
            data-razon="${razon}"
            title="Ver resultado del aval gerencial">
      <i class="fas fa-clipboard-check"></i> Ver Resultado
    </button>
  `;
}





//=================================================================================
// INICIALIZACION DE TODO EL PROGRAMA
//=================================================================================
    $(document).ready(function () {
      let solicitudes = [];
      let rowsPerPage = 10;
      let currentPage = 1;
      let chatAbierto = false;
      let modalArchivosAbierto = false;
      let modalResumenAbierto = false;
      let allSolicitudes = [];
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
                  allSolicitudes = data; // ✅ ASIGNAR A VARIABLE GLOBAL
                  solicitudes = data; // ✅ MANTENER PARA COMPATIBILIDAD

                  // Cargar opciones únicas del campo DIRIGIDO_A
                  const nombresUnicos = [...new Set(allSolicitudes.map(item => item.DIRIGIDO_RH).filter(Boolean))];

                  const select = $('#filtroDirigidoA');
                  select.empty();
                  select.append('<option value="">Todos</option>');

                  nombresUnicos.forEach(nombre => {
                    select.append(`<option value="${nombre}">${nombre}</option>`);
                  });

                  // Aplicar filtro si ya se había seleccionado uno
                  const filtroGuardado = localStorage.getItem('filtroDirigidoA') || '';
                  $('#filtroDirigidoA').val(filtroGuardado);

                  const datosFiltrados = filtroGuardado
                    ? allSolicitudes.filter(item => item.DIRIGIDO_RH === filtroGuardado)
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
                console.log("Estado Aprobación del primer item:", data[0].ESTADO_APROBACION);
                console.log("Dirigido RH del primer item:", data[0].DIRIGIDO_RH); // ✅ NUEVO DEBUG
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

                // Estados del badge de aprobación
                let aprobacionClass = '';
                const aprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase();
                if (aprobacion.includes('por aprobar')) aprobacionClass = 'estado-pendiente';
                else if (aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no'))) aprobacionClass = 'estado-contratada';
                else if (aprobacion.includes('no aprobado')) aprobacionClass = 'estado-prueba';
                else aprobacionClass = 'estado-contratada'; // Por defecto verde porque RRHH solo ve aprobadas

                const fechaModificacion = item.FECHA_MODIFICACION || '—';
                const comentario = item.COMENTARIO_SOLICITUD || '-';
                const idHistorico = item.ID_HISTORICO;
                const estadoAprobacionMostrar = item.ESTADO_APROBACION || 'Aprobado';

                // ✅ AGREGAR LÓGICA PARA DIRIGIDO_RH
                const dirigidoRH = item.DIRIGIDO_RH || null;
                const mostrarDirigidoRH = dirigidoRH 
                  ? `<span class="text-success"><i class="fas fa-user-check mr-1"></i><strong>${dirigidoRH}</strong></span>`
                  : '<span class="text-muted"><i class="fas fa-user-times mr-1"></i>Sin asignación</span>';
              
                // Formatea el comentario para mostrar
                const noLeidos = parseInt(item.NO_LEIDOS) || 0;
                console.log('ID:', idHistorico, 'Comentario:', comentario, 'NO_LEIDOS:', item.NO_LEIDOS);
                console.log('Dirigido RH:', item.ID_SOLICITUD, dirigidoRH); // ✅ NUEVO DEBUG
                
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
                      <td data-label="Abrir">
                        <button class="btn btn-expand btn-ver-historial" data-id="${item.ID_SOLICITUD}" title="Ver historial">
                          <i class="fas fa-plus"></i>
                        </button>
                      </td>

                      <td data-label="Tienda"><span class="badge badge-primary">${item.NUM_TIENDA}</span></td>
                      <td data-label="Puesto"><strong>${item.PUESTO_SOLICITADO}</strong></td>
                      <td data-label="Supervisor"><small class="text-muted">${item.SOLICITADO_POR}</small></td>
                      <td data-label="Aprobado por"><small>${item.DIRIGIDO_A || '—'}</small></td>
                      <td data-label="Asignado a"><small class="text-info">${mostrarDirigidoRH}</small></td>
                      <td data-label="Fecha Solicitud"><small>${item.FECHA_SOLICITUD}</small></td>
                      <td data-label="Modificación registrada"><small class="text-muted">${fechaModificacion}</small></td>

                      <td data-label="Estado">
                        <span class="status-badge ${statusClass}">${item.ESTADO_SOLICITUD}</span>
                      </td>

                      <td data-label="Estado de Aprobación">
                        <span class="status-badge ${aprobacionClass}" title="Estado de Aprobación por Gerencia">
                          <i class="fas fa-check-circle"></i> ${estadoAprobacionMostrar}
                        </span>
                      </td>

                      <td data-label="Razón"><small>${item.RAZON || '—'}</small></td>
                      <td data-label="Comentario" class="comentario-cell">${comentarioMostrar}</td>

                      <td data-label="Acciones">
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
                            ${(() => {
                              const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();
                              const tieneObs  = Number(item.TIENE_OBSERVACIONES_DIA_PRUEBA) === 1;
                              const tieneSel  = Number(item.TIENE_SELECCION) === 1;
                              const tieneArch = Number(item.TIENE_ARCHIVOS) === 1;

                              // ✅ NUEVA CONDICIÓN: Verificar si tiene resultado de aval
                              if (estado.includes('aval enviado')) {
                                return `
                                  <button class="btn btn-sm btn-success btnVerResultadoAval"
                                          data-id="${item.ID_SOLICITUD}"
                                          data-tienda="${item.NUM_TIENDA}"
                                          data-puesto="${item.PUESTO_SOLICITADO}"
                                          data-supervisor="${item.SOLICITADO_POR}"
                                          data-razon="${item.RAZON || ''}"
                                          title="Ver resultado del aval gerencial">
                                    <i class="fas fa-clipboard-check"></i> Ver Resultado Aval
                                  </button>
                                `;
                              }

                              // 1) Observaciones del Día de Prueba (tiene prioridad absoluta)
                              if (estado.includes('aval enviado')) {
                                return `
                                  <button class="btn btn-sm btn-success btnVerResultadoAval"
                                          data-id="${item.ID_SOLICITUD}"
                                          data-tienda="${item.NUM_TIENDA}"
                                          data-puesto="${item.PUESTO_SOLICITADO}"
                                          data-supervisor="${item.SOLICITADO_POR}"
                                          data-razon="${item.RAZON || ''}"
                                          title="Ver resultado del aval gerencial">
                                    <i class="fas fa-clipboard-check"></i> Ver Resultado Aval
                                  </button>
                                `;
                              }

                              // 1) Observaciones del Día de Prueba (SOLO para estados específicos)
                              if (tieneObs && (estado.includes('día de prueba') || estado.includes('dia de prueba') || estado.includes('observaciones'))) {
                                // VERIFICAR EL ESTADO DE LA SOLICITUD (no el de aprobación)
                                const estadoSolicitud = (item.ESTADO_SOLICITUD || '').toLowerCase();
                                const esPendienteAval = estadoSolicitud.includes('pendiente aval gerencia');
                                
                                if (!esPendienteAval) {
                                  const idObsReciente = item.ID_OBSERVACION_RECIENTE || '';
                                  return `
                                    <button class="btn btn-sm btn-primary btnVerObservacionesCompletasRRHH"
                                            data-id="${item.ID_SOLICITUD}"
                                            data-id-obs="${idObsReciente}"
                                            data-tienda="${item.NUM_TIENDA}"
                                            data-puesto="${item.PUESTO_SOLICITADO}"
                                            data-supervisor="${item.SOLICITADO_POR}"
                                            title="Ver observaciones (últimas primero)">
                                      <i class="fas fa-clipboard-list"></i> Ver Resultados
                                    </button>
                                  `;
                                } else {
                                  // Si el ESTADO es "Pendiente Aval Gerencia", mostrar mensaje de espera
                                  return `
                                    <span style="background: #ff6b6b; color: white; padding: 6px 12px; border-radius: 15px; font-size: 11px; font-weight: 600; display: inline-block;">
                                      <i class="fas fa-clock"></i> Esperando confirmacion Aval
                                    </span>
                                  `;
                                }
                              }

                              // 2) Selección de CVs (solo si NO hay observaciones)
                                if (tieneSel && (estado.includes('cvs') || estado.includes('cv'))) {
                                  return `
                                    <button class="btn btn-sm btn-success btnVerResumen" data-id="${item.ID_SOLICITUD}">
                                      <i class="fas fa-eye"></i> Ver Resumen
                                    </button>
                                  `;
                                }

                              // 3) Archivos (solo si NO hay observaciones ni selección)
                              if (tieneArch) {
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
                                }
                              }

                              // Nada que mostrar
                              return '';
                            })()}

                      </div>
                    </td>
                  </tr>
                `;
                
                console.log("Comentario de solicitud:", item.ID_SOLICITUD, item.COMENTARIO_SOLICITUD);
                console.log("Estado Aprobación:", item.ID_SOLICITUD, item.ESTADO_APROBACION);
                console.log("Dirigido RH:", item.ID_SOLICITUD, item.DIRIGIDO_RH); // ✅ NUEVO DEBUG
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
                ? allSolicitudes.filter(item => item.DIRIGIDO_RH === filtro)
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

                // ✅ USAR allSolicitudes EN LUGAR DE solicitudes
                const filteredData = allSolicitudes.filter(item => {
                  const matchGeneral = !searchValueGeneral || Object.values(item).some(value =>
                    value && value.toString().toLowerCase().includes(searchValueGeneral)
                  );

                  const matchTienda = !searchValueTienda || 
                    (item.NUM_TIENDA && item.NUM_TIENDA.toString().toLowerCase().includes(searchValueTienda));

                  return matchGeneral && matchTienda;
                });

                currentPage = 1; // ✅ RESETEAR PÁGINA AL FILTRAR
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
                
                console.log("🎯 INICIANDO btnVerResumen - Estructura basada en tu código");
                
                if (modalResumenAbierto) {
                    console.warn("🔁 Modal ya abierto, cancelando");
                    return;
                }
                
                if (Swal.isVisible()) {
                    Swal.close();
                }
                
                modalResumenAbierto = true;
                
                const idSolicitud = $(this).data('id');
                console.log("📋 ID Solicitud:", idSolicitud);
                
                if (!idSolicitud) {
                    console.error("❌ No se encontró ID de solicitud");
                    modalResumenAbierto = false;
                    Swal.fire('Error', 'No se encontró el ID de la solicitud', 'error');
                    return;
                }
                
                // ✅ LOADING IGUAL QUE EN TU CÓDIGO
                Swal.fire({
                    title: '<div style="color: #667eea;"><i class="fas fa-spinner fa-spin"></i></div>',
                    html: `
                        <div style="text-align: center; padding: 20px;">
                            <h5 style="color: #333; margin-bottom: 15px;">Cargando Resumen de CVs</h5>
                            <p style="color: #666;">Solicitud #${idSolicitud}</p>
                            <div style="margin-top: 15px; font-size: 12px; color: #999;">
                                Conectando con servidor...
                            </div>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading()
                });
                
                // ✅ AJAX EXACTAMENTE IGUAL QUE TU get_historial_filtrado QUE SÍ FUNCIONA
                console.log("🌐 Iniciando petición AJAX...");
                
                $.ajax({
                    url: './gestionhumana/crudsolicitudesrh.php?action=ver_resumen_cvs',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id_solicitud: idSolicitud
                    },
                    timeout: 30000,
                    success: function(response) {
                        console.log("✅ Respuesta exitosa:", response);
                        
                        // ✅ CERRAR LOADING
                        if (Swal.isVisible()) {
                            Swal.close();
                        }
                        
                        // ✅ VALIDAR RESPUESTA
                        if (!response) {
                            modalResumenAbierto = false;
                            Swal.fire({
                                icon: 'error',
                                title: '❌ Sin Respuesta',
                                text: 'El servidor no devolvió datos',
                                confirmButtonText: 'Entendido'
                            });
                            return;
                        }
                        
                        if (response.success === false) {
                            modalResumenAbierto = false;
                            Swal.fire({
                                icon: 'error',
                                title: '⚠️ Error del Servidor',
                                text: response.error || 'Error no especificado',
                                confirmButtonText: 'Entendido'
                            });
                            return;
                        }
                        
                        // ✅ PROCESAR RESPUESTA EXITOSA
                        setTimeout(() => {
                            procesarRespuestaResumen(response, idSolicitud);
                        }, 200);
                    },
                    error: function(xhr, status, error) {
                        console.error("❌ Error AJAX:", {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });
                        
                        modalResumenAbierto = false;
                        
                        if (Swal.isVisible()) {
                            Swal.close();
                        }
                        
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'error',
                                title: '❌ Error de Conexión',
                                html: `
                                    <div style="text-align: left;">
                                        <p><strong>No se pudo conectar con el servidor</strong></p>
                                        <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin: 15px 0;">
                                            <strong>Status HTTP:</strong> ${xhr.status}<br>
                                            <strong>Error:</strong> ${error}<br>
                                            <strong>Estado:</strong> ${status}<br>
                                            <strong>Solicitud:</strong> #${idSolicitud}
                                        </div>
                                        <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 15px;">
                                            <strong>Respuesta del servidor:</strong><br>
                                            <pre style="max-height: 150px; overflow-y: auto; font-size: 11px;">${xhr.responseText || 'Sin contenido'}</pre>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: 'Entendido',
                                width: '600px'
                            });
                        }, 200);
                    }
                });
            });


            // ✅ FUNCIÓN SEPARADA PARA PROCESAR LA RESPUESTA
            function procesarRespuestaResumen(response, idSolicitud) {
                console.log("🎨 Construyendo modal con respuesta:", response);
                
                // ✅ EXTRAER DATOS DE LA RESPUESTA
                const archivos = response.archivos || [];
                const supervisor = response.supervisor || 'No especificado';
                const fecha = response.fecha || 'No disponible';
                const total = response.total || archivos.length;
                
                console.log("📁 Archivos a mostrar:", archivos.length);
                
                // ✅ CONSTRUIR HTML DEL MODAL
                let modalContent = `
                    <div style="text-align: left; padding: 0; font-family: 'Segoe UI', sans-serif;">
                        
                        <!-- 🎨 HEADER PRINCIPAL -->
                        <div style="
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 25px;
                            margin: -20px -20px 0 -20px;
                            border-radius: 12px 12px 0 0;
                        ">
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <h3 style="margin: 0 0 8px 0; font-weight: 700; font-size: 24px;">
                                        <i class="fas fa-star text-warning mr-2"></i>
                                        CVs Seleccionados por el Supervisor
                                    </h3>
                                    <p style="margin: 0; opacity: 0.9; font-size: 16px;">
                                        Solicitud #${idSolicitud} - Vista para RRHH
                                    </p>
                                </div>
                                <div style="
                                    background: rgba(255,255,255,0.2);
                                    padding: 15px 20px;
                                    border-radius: 12px;
                                    text-align: center;
                                    backdrop-filter: blur(10px);
                                    min-width: 80px;
                                ">
                                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">Total CVs</div>
                                    <div style="font-size: 28px; font-weight: 700;">${total}</div>
                                </div>
                            </div>
                        </div>

                        <!-- 📋 INFORMACIÓN DEL SUPERVISOR -->
                        <div style="
                            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                            border: 1px solid #dee2e6;
                            border-radius: 0;
                            padding: 20px;
                            margin: 0 -20px;
                            border-left: 5px solid #28a745;
                        ">
                            <h5 style="margin: 0 0 15px 0; color: #495057; font-weight: 600;">
                                <i class="fas fa-user-tie mr-2 text-primary"></i>
                                Información de la Selección
                            </h5>
                            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                                <div>
                                    <strong style="color: #666;">Supervisor que seleccionó:</strong>
                                    <div style="color: #333; font-size: 16px; font-weight: 600;">${supervisor}</div>
                                </div>
                                <div>
                                    <strong style="color: #666;">Fecha de la solicitud:</strong>
                                    <div style="color: #333; font-size: 16px;">${fecha}</div>
                                </div>
                                <div>
                                    <strong style="color: #666;">Estado:</strong>
                                    <span style="
                                        background: #28a745;
                                        color: white;
                                        padding: 4px 12px;
                                        border-radius: 15px;
                                        font-size: 12px;
                                        font-weight: 600;
                                    ">
                                        <i class="fas fa-check mr-1"></i>SELECCIONADOS PARA REVISIÓN
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 📁 LISTA DE ARCHIVOS -->
                        <div style="
                            padding: 25px 20px;
                            margin: 0 -20px;
                            max-height: 450px;
                            overflow-y: auto;
                            background: white;
                        ">
                `;
                
                // ✅ GENERAR LISTA DE ARCHIVOS
                if (archivos.length > 0) {
                    archivos.forEach((archivo, index) => {
                        const nombreArchivo = archivo.NOMBRE_ARCHIVO || `CV ${index + 1}`;
                        const rutaArchivo = archivo.RUTA || '';
                        const tipoArchivo = archivo.TIPO || 'PDF';
                        
                        // ✅ ESCAPAR CARACTERES ESPECIALES
                        const rutaEscapada = rutaArchivo.replace(/'/g, "\\'").replace(/"/g, '\\"');
                        const nombreEscapado = nombreArchivo.replace(/'/g, "\\'").replace(/"/g, '\\"');
                        
                        modalContent += `
                            <div style="
                                background: white;
                                border: 2px solid #28a745;
                                border-radius: 12px;
                                padding: 20px;
                                margin-bottom: 15px;
                                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.15);
                                transition: all 0.3s ease;
                                position: relative;
                            " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(40, 167, 69, 0.25)'" 
                              onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(40, 167, 69, 0.15)'">
                                
                                <!-- Badge de número -->
                                <div style="
                                    position: absolute;
                                    top: -8px;
                                    left: -8px;
                                    background: #28a745;
                                    color: white;
                                    width: 30px;
                                    height: 30px;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-weight: 700;
                                    font-size: 14px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                                ">${index + 1}</div>
                                
                                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                    
                                    <!-- Icono del archivo -->
                                    <div style="
                                        background: linear-gradient(135deg, #dc3545, #e74c3c);
                                        color: white;
                                        width: 60px;
                                        height: 60px;
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        font-size: 24px;
                                        flex-shrink: 0;
                                        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
                                    ">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                    
                                    <!-- Información del archivo -->
                                    <div style="flex: 1; min-width: 200px;">
                                        <h6 style="
                                            margin: 0 0 8px 0;
                                            color: #333;
                                            font-weight: 600;
                                            font-size: 16px;
                                            word-break: break-word;
                                            line-height: 1.3;
                                        ">${nombreArchivo}</h6>
                                        
                                        <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 13px; color: #666;">
                                            <span style="display: flex; align-items: center;">
                                                <i class="fas fa-file mr-1" style="color: #007bff;"></i>
                                                Tipo: ${tipoArchivo}
                                            </span>
                                            <span style="
                                                background: #28a745;
                                                color: white;
                                                padding: 3px 10px;
                                                border-radius: 12px;
                                                font-size: 11px;
                                                font-weight: 600;
                                                display: flex;
                                                align-items: center;
                                            ">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                SELECCIONADO POR SUPERVISOR
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Botones de acción -->
                                    <div style="display: flex; gap: 10px; flex-direction: column; min-width: 120px;">
                                        <button 
                                            onclick="abrirArchivoRRHH('${rutaEscapada}')"
                                            style="
                                                background: linear-gradient(135deg, #007bff, #0056b3);
                                                color: white;
                                                border: none;
                                                padding: 10px 16px;
                                                border-radius: 8px;
                                                font-size: 14px;
                                                font-weight: 600;
                                                cursor: pointer;
                                                transition: all 0.2s;
                                                white-space: nowrap;
                                                box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
                                            "
                                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0, 123, 255, 0.4)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0, 123, 255, 0.3)'"
                                        >
                                            <i class="fas fa-eye mr-1"></i> Ver CV
                                        </button>
                                        
                                        <button 
                                            onclick="descargarArchivoRRHH('${rutaEscapada}', '${nombreEscapado}')"
                                            style="
                                                background: linear-gradient(135deg, #28a745, #20c997);
                                                color: white;
                                                border: none;
                                                padding: 10px 16px;
                                                border-radius: 8px;
                                                font-size: 14px;
                                                font-weight: 600;
                                                cursor: pointer;
                                                transition: all 0.2s;
                                                white-space: nowrap;
                                                box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
                                            "
                                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(40, 167, 69, 0.4)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(40, 167, 69, 0.3)'"
                                        >
                                            <i class="fas fa-download mr-1"></i> Descargar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    modalContent += `
                        <div style="
                            text-align: center;
                            padding: 60px 20px;
                            color: #6c757d;
                            background: #f8f9fa;
                            border-radius: 12px;
                            border: 2px dashed #dee2e6;
                        ">
                            <i class="fas fa-folder-open" style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;"></i>
                            <h5 style="color: #6c757d; margin-bottom: 10px;">No hay documentos seleccionados</h5>
                            <p style="color: #adb5bd; margin: 0; font-size: 14px;">
                                El supervisor aún no ha seleccionado ningún CV para esta solicitud
                            </p>
                            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 8px;">
                                <small style="color: #856404;">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Solicitud #${idSolicitud} - Esperando selección del supervisor
                                </small>
                            </div>
                        </div>
                    `;
                }
                
                modalContent += `
                        </div>
                    </div>
                `;
                
                // ✅ MOSTRAR MODAL CON SWAL
                Swal.fire({
                    html: modalContent,
                    width: '1100px',
                    showConfirmButton: true,
                    showCloseButton: true,
                    confirmButtonText: '<i class="fas fa-times"></i> Cerrar Vista',
                    confirmButtonColor: '#6c757d',
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    customClass: {
                        popup: 'resumen-cvs-modal-rrhh',
                        container: 'swal2-container-max-z'
                    },
                    didOpen: () => {
                        console.log("✅ Modal mostrado exitosamente");
                        
                        // ✅ AGREGAR ESTILOS CSS
                        if (!document.getElementById('resumen-cvs-styles')) {
                            const styles = document.createElement('style');
                            styles.id = 'resumen-cvs-styles';
                            styles.textContent = `
                                .swal2-container-max-z {
                                    z-index: 99999 !important;
                                }
                                .resumen-cvs-modal-rrhh {
                                    border-radius: 16px !important;
                                    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2) !important;
                                    overflow: hidden !important;
                                }
                                .resumen-cvs-modal-rrhh .swal2-html-container {
                                    padding: 20px !important;
                                    max-height: 75vh !important;
                                    overflow-y: auto !important;
                                }
                                @media (max-width: 768px) {
                                    .resumen-cvs-modal-rrhh {
                                        width: 95% !important;
                                        margin: 10px !important;
                                    }
                                }
                            `;
                            document.head.appendChild(styles);
                        }
                        
                        // ✅ DEFINIR FUNCIONES GLOBALES
                        window.abrirArchivoRRHH = function(ruta) {
                            try {
                                if (ruta && ruta.trim() !== '') {
                                    console.log("🔗 Abriendo archivo:", ruta);
                                    window.open(ruta, '_blank');
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'No se encontró la ruta del documento',
                                        icon: 'error',
                                        timer: 2000
                                    });
                                }
                            } catch (error) {
                                console.error("❌ Error abriendo archivo:", error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'No se pudo abrir el documento',
                                    icon: 'error',
                                    timer: 2000
                                });
                            }
                        };
                        
                        window.descargarArchivoRRHH = function(ruta, nombre) {
                            try {
                                if (ruta && ruta.trim() !== '') {
                                    console.log("💾 Descargando archivo:", nombre);
                                    
                                    const link = document.createElement('a');
                                    link.href = ruta;
                                    link.download = nombre || 'documento';
                                    link.style.display = 'none';
                                    
                                    document.body.appendChild(link);
                                    link.click();
                                    document.body.removeChild(link);
                                    
                                    // ✅ NOTIFICACIÓN
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'bottom-end',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true
                                    });
                                    
                                    Toast.fire({
                                        icon: 'success',
                                        title: `📁 Descargando: ${nombre}`
                                    });
                                    
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'No se encontró la ruta del documento',
                                        icon: 'error',
                                        timer: 2000
                                    });
                                }
                            } catch (error) {
                                console.error("❌ Error descargando archivo:", error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'No se pudo descargar el documento',
                                    icon: 'error',
                                    timer: 2000
                                });
                            }
                        };
                    },
                    willClose: () => {
                        modalResumenAbierto = false;
                        console.log("🔒 Modal cerrado, flag reseteado");
                    }
                });
            }

            // ✅ FUNCIÓN MEJORADA PARA MOSTRAR ARCHIVOS (COMPATIBLE)
            function mostrarArchivosSolicitud(id, tipo = 'CVS') {
                if (modalArchivosAbierto) {
                    console.warn("⚠️ Modal de archivos ya está abierto");
                    return;
                }

                if (!id) {
                    Swal.fire('Error', 'ID de solicitud no válido', 'error');
                    return;
                }

                modalArchivosAbierto = true;

                if (Swal.isVisible()) {
                    Swal.close();
                    setTimeout(() => {
                        abrirModalArchivos(id, tipo);
                    }, 200);
                    return;
                }

                abrirModalArchivos(id, tipo);
            }

            // ✅ FUNCIÓN AUXILIAR PARA ABRIR MODAL DE ARCHIVOS
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
                                    modalArchivosAbierto = false;
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
                                modalArchivosAbierto = false;
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
                                modalArchivosAbierto = false;
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



            // FUNCIÓN MEJORADA PARA VER HISTORIAL GENERAL CON FILTROS DE FECHA
            $(document).off('click', '.btnVerHistorial').on('click', '.btnVerHistorial', function () {
              // Modal para seleccionar rango de fechas y filtros adicionales
              Swal.fire({
                title: '<i class="fas fa-calendar-alt"></i> Generar Reporte de Solicitudes',
                html: `
                  <div style="text-align: left; margin-bottom: 20px;">
                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                      <h5 style="margin: 0 0 10px 0; font-weight: 600;">
                        <i class="fas fa-chart-line"></i> Configuración del Reporte
                      </h5>
                      <p style="margin: 0; font-size: 14px; opacity: 0.9;">
                        Seleccione el rango de fechas y filtros adicionales para generar el historial
                      </p>
                    </div>
                    
                    <!-- ✅ FILTROS DE FECHA -->
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <label style="font-weight: 600; margin-bottom: 8px; color: #333;">
                          <i class="fas fa-calendar-day"></i> Fecha Inicial:
                        </label>
                        <input type="date" id="fechaInicial" class="form-control" style="
                          padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;">
                      </div>
                      <div class="col-md-6">
                        <label style="font-weight: 600; margin-bottom: 8px; color: #333;">
                          <i class="fas fa-calendar-day"></i> Fecha Final:
                        </label>
                        <input type="date" id="fechaFinal" class="form-control" style="
                          padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;">
                      </div>
                    </div>
                    
                    <!-- ✅ FILTROS RÁPIDOS DE FECHA -->
                    <div style="margin-bottom: 20px;">
                      <label style="font-weight: 600; margin-bottom: 10px; color: #333;">
                        <i class="fas fa-filter"></i> Filtros Rápidos:
                      </label>
                      <div class="btn-group d-flex" role="group" style="margin-bottom: 15px;">
                        <button type="button" class="btn btn-outline-primary btn-filtro-rapido" data-dias="7">
                          <i class="fas fa-calendar-week"></i> Últimos 7 días
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-filtro-rapido" data-dias="30">
                          <i class="fas fa-calendar-alt"></i> Último mes
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-filtro-rapido" data-dias="90">
                          <i class="fas fa-calendar"></i> Últimos 3 meses
                        </button>
                      </div>
                    </div>
                    
                    <!-- ✅ NUEVOS FILTROS ADICIONALES -->
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #007bff;">
                      <h6 style="margin: 0 0 15px 0; color: #007bff; font-weight: 600;">
                        <i class="fas fa-sliders-h"></i> Filtros Adicionales
                      </h6>
                      
                      <div class="row">
                        <!-- FILTRO POR TIENDA -->
                        <div class="col-md-4 mb-3">
                          <label style="font-weight: 500; margin-bottom: 8px; color: #333;">
                            <i class="fas fa-store"></i> Tienda:
                          </label>
                          <select id="filtroTienda" class="form-control" style="border-radius: 8px;">
                            <option value="">Todas las Tiendas</option>
                          </select>
                        </div>
                        
                        <!-- FILTRO POR SUPERVISOR -->
                        <div class="col-md-4 mb-3">
                          <label style="font-weight: 500; margin-bottom: 8px; color: #333;">
                            <i class="fas fa-user-tie"></i> Supervisor:
                          </label>
                          <select id="filtroSupervisor" class="form-control" style="border-radius: 8px;">
                            <option value="">Todos los Supervisores</option>
                          </select>
                        </div>
                        
                        <!-- FILTRO POR PUESTO -->
                        <div class="col-md-4 mb-3">
                          <label style="font-weight: 500; margin-bottom: 8px; color: #333;">
                            <i class="fas fa-briefcase"></i> Puesto:
                          </label>
                          <select id="filtroPuesto" class="form-control" style="border-radius: 8px;">
                            <option value="">Todos los Puestos</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    
                    <!-- ✅ TIPOS DE CAMBIOS -->
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #17a2b8;">
                      <h6 style="margin: 0 0 10px 0; color: #17a2b8; font-weight: 600;">
                        <i class="fas fa-list-check"></i> Tipos de Cambios a Incluir
                      </h6>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="incluirAprobaciones" checked>
                        <label class="form-check-label" for="incluirAprobaciones" style="font-weight: 500;">
                          <i class="fas fa-user-check"></i> Incluir cambios de aprobación
                        </label>
                      </div>
                      <div class="form-check" style="margin-top: 8px;">
                        <input class="form-check-input" type="checkbox" id="incluirEstados" checked>
                        <label class="form-check-label" for="incluirEstados" style="font-weight: 500;">
                          <i class="fas fa-exchange-alt"></i> Incluir cambios de estado
                        </label>
                      </div>
                    </div>
                  </div>
                `,
                width: '900px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-search"></i> Generar Reporte',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                buttonsStyling: false,
                customClass: {
                  popup: 'historial-modal-grande',
                  confirmButton: 'btn btn-success btn-lg px-4',
                  cancelButton: 'btn btn-secondary btn-lg px-4 mr-2'
                },
                preConfirm: () => {
                  const fechaInicial = $('#fechaInicial').val();
                  const fechaFinal = $('#fechaFinal').val();
                  const incluirAprobaciones = $('#incluirAprobaciones').is(':checked');
                  const incluirEstados = $('#incluirEstados').is(':checked');
                  
                  // ✅ OBTENER VALORES DE FILTROS ADICIONALES
                  const filtroTienda = $('#filtroTienda').val();
                  const filtroSupervisor = $('#filtroSupervisor').val();
                  const filtroPuesto = $('#filtroPuesto').val();
                  
                  if (!fechaInicial || !fechaFinal) {
                    Swal.showValidationMessage(`
                      <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                        <span style="font-weight: 600;">Debe seleccionar ambas fechas</span>
                      </div>
                    `);
                    return false;
                  }
                  
                  if (new Date(fechaInicial) > new Date(fechaFinal)) {
                    Swal.showValidationMessage(`
                      <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                        <span style="font-weight: 600;">La fecha inicial debe ser menor que la fecha final</span>
                      </div>
                    `);
                    return false;
                  }
                  
                  if (!incluirAprobaciones && !incluirEstados) {
                    Swal.showValidationMessage(`
                      <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                        <span style="font-weight: 600;">Debe incluir al menos un tipo de cambio</span>
                      </div>
                    `);
                    return false;
                  }
                  
                  return { 
                    fechaInicial, 
                    fechaFinal, 
                    incluirAprobaciones, 
                    incluirEstados,
                    filtroTienda,
                    filtroSupervisor,
                    filtroPuesto
                  };
                },
                didOpen: () => {
                  // ✅ CARGAR DATOS PARA LOS FILTROS
                  cargarDatosFiltros();
                  
                  // Establecer fechas por defecto (último mes)
                  const hoy = new Date();
                  const hace30dias = new Date();
                  hace30dias.setDate(hoy.getDate() - 30);
                  
                  $('#fechaFinal').val(hoy.toISOString().split('T')[0]);
                  $('#fechaInicial').val(hace30dias.toISOString().split('T')[0]);
                  
                  // Event listeners para filtros rápidos
                  $('.btn-filtro-rapido').on('click', function() {
                    const dias = parseInt($(this).data('dias'));
                    const hoy = new Date();
                    const fechaInicio = new Date();
                    fechaInicio.setDate(hoy.getDate() - dias);
                    
                    $('#fechaFinal').val(hoy.toISOString().split('T')[0]);
                    $('#fechaInicial').val(fechaInicio.toISOString().split('T')[0]);
                    
                    // Resaltar botón seleccionado
                    $('.btn-filtro-rapido').removeClass('active');
                    $(this).addClass('active');
                  });
                  
                  // Estilos adicionales
                  if (!document.getElementById('historial-styles')) {
                    const styles = document.createElement('style');
                    styles.id = 'historial-styles';
                    styles.textContent = `
                      .historial-modal-grande {
                        border-radius: 16px !important;
                        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2) !important;
                      }
                      .btn-filtro-rapido.active {
                        background-color: #007bff !important;
                        color: white !important;
                        border-color: #007bff !important;
                      }
                      .historial-modal-grande .form-control:focus {
                        border-color: #667eea !important;
                        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
                      }
                    `;
                    document.head.appendChild(styles);
                  }
                }
              }).then((result) => {
                if (result.isConfirmed) {
                  // ✅ MENSAJE DE LOADING CON FILTROS APLICADOS
                  let filtrosTexto = [];
                  if (result.value.filtroTienda) filtrosTexto.push(`Tienda: ${$('#filtroTienda option:selected').text()}`);
                  if (result.value.filtroSupervisor) filtrosTexto.push(`Supervisor: ${$('#filtroSupervisor option:selected').text()}`);
                  if (result.value.filtroPuesto) filtrosTexto.push(`Puesto: ${$('#filtroPuesto option:selected').text()}`);
                  
                  const filtrosAplicados = filtrosTexto.length > 0 ? filtrosTexto.join(' | ') : 'Sin filtros adicionales';
                  
                  Swal.fire({
                    title: '<i class="fas fa-spinner fa-spin"></i> Generando reporte...',
                    html: `
                      <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 16px; margin-bottom: 10px;">
                          Consultando historial del ${result.value.fechaInicial} al ${result.value.fechaFinal}
                        </div>
                        <div style="color: #666; font-size: 14px; margin-bottom: 10px;">
                          ${filtrosAplicados}
                        </div>
                        <div style="color: #999; font-size: 12px;">
                          Por favor espera un momento...
                        </div>
                      </div>
                    `,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                  });

                  // ✅ LLAMADA AJAX CON FILTROS ADICIONALES
                  $.ajax({
                    url: './gestionhumana/crudsolicitudesrh.php?action=get_historial_filtrado',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                      fecha_inicial: result.value.fechaInicial,
                      fecha_final: result.value.fechaFinal,
                      incluir_aprobaciones: result.value.incluirAprobaciones ? 1 : 0,
                      incluir_estados: result.value.incluirEstados ? 1 : 0,
                      filtro_tienda: result.value.filtroTienda,
                      filtro_supervisor: result.value.filtroSupervisor,
                      filtro_puesto: result.value.filtroPuesto
                    },
                    success: function (datos) {
                      if (!datos || datos.length === 0) {
                        Swal.fire({
                          icon: 'info',
                          title: '<i class="fas fa-info-circle"></i> Sin Resultados',
                          html: `
                            <div style="text-align: center; padding: 20px;">
                              <p>No se encontraron cambios con los filtros seleccionados.</p>
                              <div style="background: #e9ecef; padding: 12px; border-radius: 8px; margin-top: 15px;">
                                <small><strong>Período:</strong> ${result.value.fechaInicial} - ${result.value.fechaFinal}<br>
                                <strong>Filtros:</strong> ${filtrosAplicados}</small>
                              </div>
                            </div>
                          `,
                          confirmButtonText: 'Entendido'
                        });
                        return;
                      }

                      mostrarHistorialFiltrado(datos, result.value);
                    },
                    error: function (xhr, status, error) {
                      console.error('❌ Error cargando historial filtrado:', {
                        status: xhr.status,
                        responseText: xhr.responseText,
                        error: error
                      });
                      Swal.fire({
                        icon: 'error',
                        title: '<i class="fas fa-exclamation-circle"></i> Error de Conexión',
                        text: 'No se pudo cargar el historial filtrado.',
                        confirmButtonText: 'Reintentar'
                      });
                    }
                  });
                }
              });
            });

            // FUNCIÓN PARA CARGAR DATOS DE LOS FILTROS
            function cargarDatosFiltros() {
              console.log('🔍 Cargando datos para filtros...');
              
              // Cargar tiendas
              $.ajax({
                url: './gestionhumana/crudsolicitudesrh.php?action=get_tiendas_filtro',
                type: 'GET',
                dataType: 'json',
                success: function(tiendas) {
                  console.log('✅ Tiendas cargadas:', tiendas);
                  const selectTienda = $('#filtroTienda');
                  selectTienda.empty().append('<option value="">Todas las Tiendas</option>');
                  
                  if (tiendas && tiendas.length > 0) {
                    tiendas.forEach(tienda => {
                      selectTienda.append(`<option value="${tienda.numero}">${tienda.numero} - ${tienda.nombre}</option>`);
                    });
                  }
                },
                error: function(xhr, status, error) {
                  console.error('❌ Error cargando tiendas:', xhr.responseText);
                  console.error('Status:', status, 'Error:', error);
                }
              });
              
              // Cargar supervisores
              $.ajax({
                url: './gestionhumana/crudsolicitudesrh.php?action=get_supervisores_filtro',
                type: 'GET',
                dataType: 'json',
                success: function(supervisores) {
                  console.log('✅ Supervisores cargados:', supervisores);
                  const selectSupervisor = $('#filtroSupervisor');
                  selectSupervisor.empty().append('<option value="">Todos los Supervisores</option>');
                  
                  if (supervisores && supervisores.length > 0) {
                    supervisores.forEach(supervisor => {
                      selectSupervisor.append(`<option value="${supervisor.codigo}">${supervisor.nombre}</option>`);
                    });
                  }
                },
                error: function(xhr, status, error) {
                  console.error('❌ Error cargando supervisores:', xhr.responseText);
                  console.error('Status:', status, 'Error:', error);
                }
              });
              
              // Cargar puestos
              $.ajax({
                url: './gestionhumana/crudsolicitudesrh.php?action=get_puestos_filtro',
                type: 'GET',
                dataType: 'json',
                success: function(puestos) {
                  console.log('✅ Puestos cargados:', puestos);
                  const selectPuesto = $('#filtroPuesto');
                  selectPuesto.empty().append('<option value="">Todos los Puestos</option>');
                  
                  if (puestos && puestos.length > 0) {
                    puestos.forEach(puesto => {
                      selectPuesto.append(`<option value="${puesto}">${puesto}</option>`);
                    });
                  }
                },
                error: function(xhr, status, error) {
                  console.error('❌ Error cargando puestos:', xhr.responseText);
                  console.error('Status:', status, 'Error:', error);
                }
              });
            }

            // FUNCIÓN PARA MOSTRAR EL HISTORIAL FILTRADO
            // ✅ FUNCIÓN PARA LIMPIAR CARACTERES PROBLEMÁTICOS
            function limpiarCaracteres(texto) {
              if (!texto) return '—';
              
              let textoLimpio = texto.toString();
              
              // ✅ REEMPLAZOS DIRECTOS SIN REGEX PROBLEMÁTICA
              textoLimpio = textoLimpio.replace(/\?\?n/g, 'ón');
              textoLimpio = textoLimpio.replace(/\?\?\?/g, 'ción');
              textoLimpio = textoLimpio.replace(/\?\?/g, 'ñ');
              
              // Otros caracteres problemáticos UTF-8
              textoLimpio = textoLimpio.replace(/Ã±/g, 'ñ');
              textoLimpio = textoLimpio.replace(/Ã³/g, 'ó');
              textoLimpio = textoLimpio.replace(/Ã¡/g, 'á');
              textoLimpio = textoLimpio.replace(/Ã©/g, 'é');
              textoLimpio = textoLimpio.replace(/Ã­/g, 'í');
              textoLimpio = textoLimpio.replace(/Ãº/g, 'ú');
              textoLimpio = textoLimpio.replace(/Ã'/g, 'Ñ');
              
              return textoLimpio;
            }

            // ✅ FUNCIÓN MEJORADA PARA MOSTRAR HISTORIAL CON FILTROS ADICIONALES
            function mostrarHistorialFiltrado(datos, filtros) {
              console.log('⏰ Timeline con filtros avanzados:', filtros);
              
              // ✅ LIMPIAR DATOS AL RECIBIRLOS
              const datosLimpios = datos.map(registro => ({
                ...registro,
                ESTADO_ANTERIOR: limpiarCaracteres(registro.ESTADO_ANTERIOR) || '—',
                ESTADO_NUEVO: limpiarCaracteres(registro.ESTADO_NUEVO) || '—',
                APROBACION_ANTERIOR: limpiarCaracteres(registro.APROBACION_ANTERIOR) || 'Por Aprobar',
                APROBACION_NUEVA: limpiarCaracteres(registro.APROBACION_NUEVA) || 'Por Aprobar',
                COMENTARIO_NUEVO: limpiarCaracteres(registro.COMENTARIO_NUEVO) || '—',
                PUESTO_SOLICITADO: limpiarCaracteres(registro.PUESTO_SOLICITADO) || '—',
                SOLICITADO_POR: limpiarCaracteres(registro.SOLICITADO_POR) || '—'
              }));
              
              // ✅ ELIMINAR DUPLICADOS CON DATOS LIMPIOS
              const historialUnico = [];
              const registrosVistos = new Set();
              
              datosLimpios.forEach(registro => {
                const clave = `${registro.ID_SOLICITUD}_${registro.FECHA_CAMBIO}_${registro.ID_HISTORICO}`;
                if (!registrosVistos.has(clave)) {
                  registrosVistos.add(clave);
                  historialUnico.push(registro);
                }
              });
              
              // ✅ APLICAR FILTROS DE TIPOS DE CAMBIOS
              let datosFiltrados = [];
              
              if (filtros.incluirEstados && filtros.incluirAprobaciones) {
                datosFiltrados = historialUnico;
              } else if (filtros.incluirEstados && !filtros.incluirAprobaciones) {
                datosFiltrados = historialUnico.filter(registro => {
                  const tieneEstadoAnterior = registro.ESTADO_ANTERIOR && registro.ESTADO_ANTERIOR !== '—';
                  const tieneEstadoNuevo = registro.ESTADO_NUEVO && registro.ESTADO_NUEVO !== '—';
                  return tieneEstadoAnterior || tieneEstadoNuevo;
                });
              } else if (filtros.incluirAprobaciones && !filtros.incluirEstados) {
                datosFiltrados = historialUnico.filter(registro => {
                  const tieneAprobacionAnterior = registro.APROBACION_ANTERIOR && registro.APROBACION_ANTERIOR !== 'Por Aprobar';
                  const tieneAprobacionNueva = registro.APROBACION_NUEVA && registro.APROBACION_NUEVA !== 'Por Aprobar';
                  return tieneAprobacionAnterior || tieneAprobacionNueva;
                });
              } else {
                datosFiltrados = [];
              }
              
              // Ordenar por fecha
              datosFiltrados.sort((a, b) => new Date(b.FECHA_CAMBIO) - new Date(a.FECHA_CAMBIO));
              
              // ✅ GENERAR TÍTULO SEGÚN FILTROS
              let tituloFiltros = '';
              if (filtros.incluirEstados && filtros.incluirAprobaciones) {
                tituloFiltros = 'Estados y Aprobaciones';
              } else if (filtros.incluirEstados) {
                tituloFiltros = 'Solo Cambios de Estado';
              } else if (filtros.incluirAprobaciones) {
                tituloFiltros = 'Solo Cambios de Aprobación';
              } else {
                tituloFiltros = 'Sin Filtros Seleccionados';
              }
              
              // ✅ CONSTRUIR INFORMACIÓN DE FILTROS ADICIONALES
              let filtrosAdicionales = [];
              if (filtros.filtroTienda) {
                const tiendaTexto = $('#filtroTienda option:selected').text();
                filtrosAdicionales.push(`<span class="badge badge-info">Tienda: ${tiendaTexto}</span>`);
              }
              if (filtros.filtroSupervisor) {
                const supervisorTexto = $('#filtroSupervisor option:selected').text();
                filtrosAdicionales.push(`<span class="badge badge-warning">Supervisor: ${supervisorTexto}</span>`);
              }
              if (filtros.filtroPuesto) {
                const puestoTexto = $('#filtroPuesto option:selected').text();
                filtrosAdicionales.push(`<span class="badge badge-secondary">Puesto: ${puestoTexto}</span>`);
              }
              
              const filtrosAdicionalesHTML = filtrosAdicionales.length > 0 ? 
                `<div style="margin-top: 8px;">${filtrosAdicionales.join(' ')}</div>` : '';
              
              // ✅ GENERAR TIMELINE CON INFORMACIÓN MEJORADA
              let timeline = `
                <div style="margin-bottom: 20px; text-align: center;">
                  <h5><i class="fas fa-clock"></i> ${tituloFiltros}</h5>
                  <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;">
                    <div style="margin-bottom: 8px;">
                      <strong>Período:</strong> ${filtros.fechaInicial} - ${filtros.fechaFinal} | 
                      <strong>Registros:</strong> ${datosFiltrados.length}
                    </div>
                    <div>
                      <strong>Tipos:</strong> 
                      ${filtros.incluirEstados ? '<span class="badge badge-info">Estados</span> ' : ''}
                      ${filtros.incluirAprobaciones ? '<span class="badge badge-success">Aprobaciones</span>' : ''}
                    </div>
                    ${filtrosAdicionalesHTML}
                  </div>
                </div>
                
                <div style="max-height: 500px; overflow-y: auto; padding: 20px;">
              `;
              
              if (datosFiltrados.length === 0) {
                timeline += `
                  <div style="text-align: center; padding: 50px; color: #666;">
                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 20px;"></i>
                    <h5>No se encontraron registros</h5>
                    <p>Ajusta los filtros o el rango de fechas para ver resultados.</p>
                  </div>
                `;
              } else {
                datosFiltrados.forEach((evento, index) => {
                  // Determinar tipo de evento
                  let tipoEvento = '';
                  let iconoEvento = '';
                  let colorEvento = '';
                  let descripcionEvento = '';
                  
                  const cambioEstado = evento.ESTADO_ANTERIOR !== evento.ESTADO_NUEVO && 
                                      (evento.ESTADO_ANTERIOR !== '—' || evento.ESTADO_NUEVO !== '—');
                  const cambioAprobacion = evento.APROBACION_ANTERIOR !== evento.APROBACION_NUEVA;
                  
                  if (filtros.incluirEstados && filtros.incluirAprobaciones) {
                    if (cambioEstado && cambioAprobacion) {
                      tipoEvento = 'Cambio Múltiple';
                      iconoEvento = 'fas fa-exchange-alt';
                      colorEvento = '#ff6b6b';
                      descripcionEvento = `<div><strong>Estado:</strong> ${evento.ESTADO_ANTERIOR} → ${evento.ESTADO_NUEVO}</div>
                                          <div><strong>Aprobación:</strong> ${evento.APROBACION_ANTERIOR} → ${evento.APROBACION_NUEVA}</div>`;
                    } else if (cambioEstado) {
                      tipoEvento = 'Cambio de Estado';
                      iconoEvento = 'fas fa-arrow-right';
                      colorEvento = '#4ecdc4';
                      descripcionEvento = `<strong>Estado:</strong> ${evento.ESTADO_ANTERIOR} → ${evento.ESTADO_NUEVO}`;
                    } else if (cambioAprobacion) {
                      tipoEvento = 'Cambio de Aprobación';
                      iconoEvento = 'fas fa-check-circle';
                      colorEvento = '#45b7d1';
                      descripcionEvento = `<strong>Aprobación:</strong> ${evento.APROBACION_ANTERIOR} → ${evento.APROBACION_NUEVA}`;
                    } else {
                      tipoEvento = 'Actualización';
                      iconoEvento = 'fas fa-edit';
                      colorEvento = '#96ceb4';
                      descripcionEvento = 'Modificación en el registro';
                    }
                  } else if (filtros.incluirEstados) {
                    tipoEvento = 'Cambio de Estado';
                    iconoEvento = 'fas fa-arrow-right';
                    colorEvento = '#4ecdc4';
                    descripcionEvento = `<strong>Estado:</strong> ${evento.ESTADO_ANTERIOR} → ${evento.ESTADO_NUEVO}`;
                  } else if (filtros.incluirAprobaciones) {
                    tipoEvento = 'Cambio de Aprobación';
                    iconoEvento = 'fas fa-check-circle';
                    colorEvento = '#45b7d1';
                    descripcionEvento = `<strong>Aprobación:</strong> ${evento.APROBACION_ANTERIOR} → ${evento.APROBACION_NUEVA}`;
                  }
                  
                  timeline += `
                    <div style="display: flex; margin-bottom: 20px; position: relative;">
                      ${index < datosFiltrados.length - 1 ? 
                        '<div style="position: absolute; left: 24px; top: 50px; width: 2px; height: 30px; background: #ddd;"></div>' : ''}
                      
                      <div style="
                        width: 50px; height: 50px; border-radius: 50%; background: ${colorEvento}; 
                        display: flex; align-items: center; justify-content: center; color: white;
                        flex-shrink: 0; margin-right: 15px;">
                        <i class="${iconoEvento}"></i>
                      </div>
                      
                      <div style="
                        flex: 1; background: white; border: 1px solid #e0e0e0; border-radius: 10px; 
                        padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                          <h6 style="margin: 0; color: ${colorEvento};">
                            <strong>${tipoEvento}</strong>
                          </h6>
                          <small style="color: #666;">
                            <i class="fas fa-clock"></i> ${evento.FECHA_CAMBIO}
                          </small>
                        </div>
                        
                        <div style="margin-bottom: 10px;">
                          <span class="badge badge-primary">Solicitud ${evento.ID_SOLICITUD}</span>
                          <span class="badge badge-secondary">Tienda ${evento.NUM_TIENDA}</span>
                          <span class="badge badge-info">${evento.PUESTO_SOLICITADO}</span>
                          <span class="badge badge-dark">${evento.SOLICITADO_POR}</span>
                        </div>
                        
                        <div style="margin-bottom: 10px;">
                          ${descripcionEvento}
                        </div>
                        
                        ${evento.COMENTARIO_NUEVO && evento.COMENTARIO_NUEVO !== '—' ? 
                          `<div style="background: #f8f9fa; padding: 8px; border-radius: 5px; border-left: 3px solid ${colorEvento};">
                            <small><strong>Comentario:</strong> ${evento.COMENTARIO_NUEVO}</small>
                          </div>` : ''}
                      </div>
                    </div>
                  `;
                });
              }
              
              timeline += '</div>';
              
            // ✅ ACTUALIZAR PARÁMETROS PARA EXPORTACIÓN (CORREGIDO)
              const parametrosExportacion = `&fecha_inicial=${filtros.fechaInicial}&fecha_final=${filtros.fechaFinal}&incluir_aprobaciones=${filtros.incluirAprobaciones ? 1 : 0}&incluir_estados=${filtros.incluirEstados ? 1 : 0}&filtro_tienda=${encodeURIComponent(filtros.filtroTienda || '')}&filtro_supervisor=${encodeURIComponent(filtros.filtroSupervisor || '')}&filtro_puesto=${encodeURIComponent(filtros.filtroPuesto || '')}`;
              
              console.log('🔗 Parámetros de exportación:', parametrosExportacion);
              
              // Mostrar modal
              Swal.fire({
                title: '<i class="fas fa-history"></i> Timeline de Cambios',
                html: timeline,
                width: '90%',
                showCloseButton: true,
                confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
                footer: `
                  <div class="d-flex justify-content-between w-100">
                    <button id="btnExportarExcel" class="btn btn-success btn-sm">
                      <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button id="btnGenerarPDF" class="btn btn-danger btn-sm">
                      <i class="fas fa-file-pdf"></i> PDF
                    </button>
                  </div>
                `,
                didOpen: () => {
                  document.getElementById('btnExportarExcel').onclick = () => {
                    const urlExcel = `./gestionhumana/crudsolicitudesrh.php?action=generar_reporte_historial&formato=excel${parametrosExportacion}`;
                    console.log('🟢 URL Excel:', urlExcel);
                    window.open(urlExcel, '_blank');
                  };
                  
                  document.getElementById('btnGenerarPDF').onclick = () => {
                    const urlPDF = `./gestionhumana/crudsolicitudesrh.php?action=generar_reporte_historial&formato=pdf${parametrosExportacion}`;
                    console.log('🔴 URL PDF:', urlPDF);
                    window.open(urlPDF, '_blank');
                  };
                }
              });
            }


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
                        const nombreGerente = filaActual.find('td:nth-child(5)').text().trim() || 'Gerente'; 
                        
                        Swal.fire({
                            title: `<i class="fas fa-comments"></i> ${nombreGerente}`,
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
                                  const nombreRRHH = filaActual.find('td:nth-child(6)').text().trim() || 'RRHH'; 
                                  const nombreGerente = 'GERENTE'; //Nombre fijo para el gerente
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

                        // 🎯 FUNCIÓN PRINCIPAL PARA VISTA COMPLETA RRHH
                    $(document).off('click', '.btnVerObservacionesCompletasRRHH').on('click', '.btnVerObservacionesCompletasRRHH', function(e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        
                        const idSolicitud = $(this).data('id');
                        const tienda = $(this).data('tienda');
                        const puesto = $(this).data('puesto');
                        const supervisor = $(this).data('supervisor');
                        
                        console.log("🏢 Vista RRHH - Observaciones completas para solicitud:", idSolicitud);
                        
                        // Mostrar loading
                        Swal.fire({
                            title: '<i class="fas fa-spinner fa-spin"></i> Cargando información completa...',
                            text: 'Obteniendo todas las observaciones y estadísticas',
                            allowOutsideClick: false,
                            showConfirmButton: false
                        });
                        
                        // Obtener información completa
                        $.ajax({
                            url: './gestionhumana/crudsolicitudesrh.php?action=get_observaciones_completas_rrhh',
                            type: 'GET',
                            dataType: 'json',
                            data: {
                                id_solicitud: idSolicitud
                            },
                            success: function(response) {
                                console.log("✅ Información completa recibida:", response);
                                
                                if (response.success) {
                                    mostrarVistaCompletaRRHH(response);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '<i class="fas fa-exclamation-triangle"></i> Error',
                                        text: response.error || 'No se pudo obtener la información completa',
                                        confirmButtonText: 'Entendido'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('❌ Error obteniendo información completa:', {
                                    status: xhr.status,
                                    responseText: xhr.responseText,
                                    error: error
                                });
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: '<i class="fas fa-wifi"></i> Error de Conexión',
                                    text: 'No se pudo conectar al servidor para obtener la información',
                                    confirmButtonText: 'Entendido'
                                });
                            }
                        });
                    });

                    // 🎨 FUNCIÓN PARA MOSTRAR VISTA COMPLETA RRHH
                    function mostrarVistaCompletaRRHH(data) {
                        const { solicitud, observaciones, estadisticas, ciclos } = data;
                        
                        // 🎨 CONSTRUIR MODAL AVANZADO
                        const modalHtml = `
                            <div style="text-align: left; max-width: 100%;">
                                <!-- Header RRHH -->
                                <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 25px; border-radius: 15px; margin-bottom: 25px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <h4 style="margin: 0 0 10px 0; font-weight: 700;">
                                                <i class="fas fa-building mr-2"></i>Vista RRHH - Observaciones Completas
                                            </h4>
                                            <div style="font-size: 16px; opacity: 0.9;">
                                                Solicitud #${solicitud.ID_SOLICITUD} - Tienda ${solicitud.NUM_TIENDA} - ${solicitud.PUESTO_SOLICITADO}
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 10px;">
                                                <div style="font-size: 14px; opacity: 0.9;">Supervisor</div>
                                                <div style="font-size: 16px; font-weight: 600;">${solicitud.SOLICITADO_POR}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estadísticas Ejecutivas -->
                                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 15px; padding: 25px; margin-bottom: 25px;">
                                    <h5 style="margin: 0 0 20px 0; color: #495057; font-weight: 600;">
                                        <i class="fas fa-chart-bar mr-2"></i>Resumen Ejecutivo
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid #007bff;">
                                                <div style="font-size: 32px; font-weight: 700; color: #007bff;">${estadisticas.total_observaciones}</div>
                                                <div style="font-size: 14px; color: #6c757d; font-weight: 600;">Total Evaluaciones</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid #6610f2;">
                                                <div style="font-size: 32px; font-weight: 700; color: #6610f2;">${estadisticas.total_ciclos}</div>
                                                <div style="font-size: 14px; color: #6c757d; font-weight: 600;">Ciclos de Prueba</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid #28a745;">
                                                <div style="font-size: 32px; font-weight: 700; color: #28a745;">${estadisticas.recomendados}</div>
                                                <div style="font-size: 14px; color: #6c757d; font-weight: 600;">Recomendados</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid #dc3545;">
                                                <div style="font-size: 32px; font-weight: 700; color: #dc3545;">${estadisticas.no_recomendados}</div>
                                                <div style="font-size: 14px; color: #6c757d; font-weight: 600;">No Recomendados</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-top: 20px; text-align: center;">
                                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 10px; display: inline-block;">
                                            <i class="fas fa-percentage mr-2"></i>
                                            <strong>Tasa de Recomendación: ${estadisticas.porcentaje_recomendado}%</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de Observaciones por Ciclo -->
                                <div style="background: white; border: 1px solid #dee2e6; border-radius: 15px; padding: 25px; margin-bottom: 25px;">
                                    <h5 style="margin: 0 0 20px 0; color: #495057; font-weight: 600;">
                                        <i class="fas fa-list-alt mr-2"></i>Observaciones Detalladas
                                    </h5>
                                    <div id="observaciones-tabs">
                                        ${generarTabsObservaciones(observaciones)}
                                    </div>
                                </div>

                                <!-- Acciones RRHH -->
                                <div style="background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 15px; padding: 25px;">
                                    <h5 style="margin: 0 0 20px 0; color: #1976d2; font-weight: 600;">
                                        <i class="fas fa-cogs mr-2"></i>Acciones de RRHH
                                    </h5>
                                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                                        <button class="btn btn-danger btn-lg" onclick="generarReportePDF(${solicitud.ID_SOLICITUD})">
                                            <i class="fas fa-file-pdf mr-2"></i>Generar Reporte PDF
                                        </button>
                                        <button class="btn btn-warning btn-lg" onclick="enviarAvalGerencia(${solicitud.ID_SOLICITUD})">
                                            <i class="fas fa-paper-plane mr-2"></i>Enviar a Aval Gerencia
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        /*<button class="btn btn-info btn-lg" onclick="exportarExcel(${solicitud.ID_SOLICITUD})">
                                            <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                                        </button>*/
                        
                        // Mostrar modal
                        Swal.fire({
                            title: false,
                            html: modalHtml,
                            width: '1200px',
                            showCloseButton: true,
                            showConfirmButton: true,
                            confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                            confirmButtonColor: '#6c757d',
                            customClass: {
                                popup: 'rrhh-view-modal'
                            },
                            didOpen: () => {
                                // Agregar estilos para el modal RRHH
                                if (!document.getElementById('rrhh-view-styles')) {
                                    const styles = document.createElement('style');
                                    styles.id = 'rrhh-view-styles';
                                    styles.textContent = `
                                        .rrhh-view-modal {
                                            border-radius: 20px !important;
                                            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2) !important;
                                        }
                                        .rrhh-view-modal .swal2-html-container {
                                            max-height: 75vh !important;
                                            overflow-y: auto !important;
                                        }
                                        .obs-tab {
                                            margin-bottom: 20px;
                                            border: 1px solid #dee2e6;
                                            border-radius: 10px;
                                            overflow: hidden;
                                        }
                                        .obs-tab-header {
                                            background: #f8f9fa;
                                            padding: 15px;
                                            cursor: pointer;
                                            transition: all 0.3s;
                                        }
                                        .obs-tab-header:hover {
                                            background: #e9ecef;
                                        }
                                        .obs-tab-content {
                                            padding: 20px;
                                            display: none;
                                        }
                                        .obs-tab.active .obs-tab-content {
                                            display: block;
                                        }
                                    `;
                                    document.head.appendChild(styles);
                                }
                                
                                // Activar tabs
                                $('.obs-tab-header').click(function() {
                                    $('.obs-tab').removeClass('active');
                                    $(this).parent().addClass('active');
                                });
                                
                                // Activar primer tab
                                $('.obs-tab').first().addClass('active');
                            }
                        });
                    }

                    // 🎨 FUNCIÓN PARA GENERAR TABS DE OBSERVACIONES
                    function generarTabsObservaciones(observaciones) {
                        if (!observaciones || observaciones.length === 0) {
                            return '<div class="text-center py-4"><i class="fas fa-info-circle"></i> No hay observaciones disponibles</div>';
                        }
                        
                        let tabsHtml = '';
                        
                        observaciones.forEach((obs, index) => {
                            const cicloNum = index + 1;
                            const recomendacionColor = obs.RECOMENDACION_SUP === 'RECOMENDADO' ? '#28a745' : '#dc3545';
                            const recomendacionIcon = obs.RECOMENDACION_SUP === 'RECOMENDADO' ? 'thumbs-up' : 'thumbs-down';
                            
                            tabsHtml += `
                                <div class="obs-tab">
                                    <div class="obs-tab-header">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <strong><i class="fas fa-calendar mr-2"></i>Ciclo ${cicloNum} - ${obs.FECHA_DIA_PRUEBA}</strong>
                                                <span style="margin-left: 15px; color: #6c757d;">${obs.CANDIDATO_NOMBRE}</span>
                                            </div>
                                            <div style="background: ${recomendacionColor}; color: white; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;">
                                                <i class="fas fa-${recomendacionIcon} mr-1"></i> ${obs.RECOMENDACION_SUP}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="obs-tab-content">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-user mr-2"></i>Información del Candidato</h6>
                                                <table class="table table-sm">
                                                    <tr><td><strong>Nombre:</strong></td><td>${obs.CANDIDATO_NOMBRE}</td></tr>
                                                    <tr><td><strong>Documento:</strong></td><td>${obs.CANDIDATO_DOCUMENTO || 'No especificado'}</td></tr>
                                                    <tr><td><strong>Puesto:</strong></td><td>${obs.PUESTO_EVALUADO}</td></tr>
                                                    <tr><td><strong>Horario:</strong></td><td>${obs.HORA_INICIO} - ${obs.HORA_FIN}</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-chart-line mr-2"></i>Evaluación</h6>
                                                <table class="table table-sm">
                                                    <tr><td><strong>Puntualidad:</strong></td><td><span class="badge badge-info">${obs.PUNTUALIDAD}</span></td></tr>
                                                    <tr><td><strong>Actitud:</strong></td><td><span class="badge badge-success">${obs.ACTITUD}</span></td></tr>
                                                    <tr><td><strong>Conocimientos:</strong></td><td><span class="badge badge-warning">${obs.CONOCIMIENTOS}</span></td></tr>
                                                    <tr><td><strong>Desempeño:</strong></td><td><span class="badge badge-primary">${obs.DESEMPENO_GENERAL || '-'}</span></td></tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <h6><i class="fas fa-edit mr-2"></i>Observaciones Detalladas</h6>
                                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; max-height: 150px; overflow-y: auto;">
                                                    <div style="white-space: pre-wrap; font-size: 14px;">${obs.OBSERVACIONES_DET || 'Sin observaciones detalladas'}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div style="background: ${recomendacionColor}; color: white; padding: 15px; border-radius: 8px; text-align: center;">
                                                    <i class="fas fa-${recomendacionIcon} mr-2"></i>
                                                    <strong>RECOMENDACIÓN: ${obs.RECOMENDACION_SUP}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-user mr-1"></i>${obs.SUPERVISOR_NOMBRE} | 
                                                <i class="fas fa-clock mr-1"></i>${obs.FECHA_CREACION}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        return tabsHtml;
                    }

                    //BOTON PARA VER RESULTADO DEL AVAL 
                    $(document).on('click', '.btnVerResultadoAval', function() {
                    const idSolicitud = $(this).data('id');
                    const tienda = $(this).data('tienda');
                    const puesto = $(this).data('puesto');
                    const supervisor = $(this).data('supervisor');
                    const razon = $(this).data('razon');
                    
                    cargarResultadoAvalRH(idSolicitud, tienda, puesto, supervisor, razon);
                  });

              // CARGAR SOLICITUDES AL INICIO
              cargarSolicitudes();
    });

  </script>
</body>
</html>
