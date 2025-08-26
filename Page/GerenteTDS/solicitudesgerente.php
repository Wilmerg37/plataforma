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
    /* ✅ Asegurar mismo tamaño que otros botones */
    height: 40px; /* O el valor exacto del height de los otros botones */
    line-height: 1.5;
    padding: 0.400rem 0.75rem; /* Mismo padding que btn-primary/btn-secondary */
    font-size: 1rem;
    font-weight: 400;
    border: 0.1px solid transparent;
    border-radius: 0.30rem;
    display: flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
  }

  /* ✅ Asegurar que el botón ocupe todo el ancho disponible */
  .btn-history.btn-block {
    width: 100% !important;
    display: block;
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
          <!--<div class="col-md-3">
            <label for="filtroGerente" class="font-weight-bold">
              <i class="fas fa-user-tie mr-1"></i> Dirigido a (Gerente)
            </label>
            <select id="filtroGerente" class="form-control">
              <option value="">Todos los Gerentes</option>
              <option value="Christian Quan">Christian Quan</option>
              <option value="Giovanni Cardoza">Giovanni Cardoza</option>
            </select>
          </div>-->

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


          <!--Historial de las solicitudes con RRHH y Gerente-->
          <div class = "col-md-3 d-flex align-items-end">
            <div class = "w-100">
              <button class="btn btn-custom btn-history btnVerHistorial btn-block">
                <i class="far fa-file-alt mr-2"></i>
                PROCESO DE SOLICITUDES
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
              <!--<th width="20">Comentario</th>-->
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

    // FUNCIÓN PARA MOSTRAR MODAL DE PROCESAR AVAL
function mostrarModalProcesarAval(payload, id, tienda, puesto, supervisor, razon) {
  const aval = payload.aval || {};
  const cand = payload.candidato || {};
  const documentos = payload.documentos || [];

  const comentarioRH = (aval.comentario_rh && aval.comentario_rh.trim()) ? aval.comentario_rh : 'Sin comentarios de RH';
  const enviadoPor = aval.enviado_por ? aval.enviado_por : '—';
  const fechaEnvio = aval.fecha_envio ? aval.fecha_envio : '—';

  // 🎯 SECCIÓN DE EVALUACIÓN DEL CANDIDATO (PROFESIONAL)
  const evaluacionHTML = `
    <div class="row mt-3">
      <div class="col-md-6">
        <div class="evaluation-card">
          <div class="eval-item">
            <span class="eval-label">Puntualidad:</span>
            <span class="eval-badge ${getBadgeClass(cand.PUNTUALIDAD)}">${cand.PUNTUALIDAD ?? 'N/A'}</span>
          </div>
          <div class="eval-item">
            <span class="eval-label">Actitud:</span>
            <span class="eval-badge ${getBadgeClass(cand.ACTITUD)}">${cand.ACTITUD ?? 'N/A'}</span>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="evaluation-card">
          <div class="eval-item">
            <span class="eval-label">Conocimientos:</span>
            <span class="eval-badge ${getBadgeClass(cand.CONOCIMIENTOS)}">${cand.CONOCIMIENTOS ?? 'N/A'}</span>
          </div>
          <div class="eval-item">
            <span class="eval-label">Desempeño:</span>
            <span class="eval-badge ${getBadgeClass(cand.DESEMPENO_GENERAL)}">${cand.DESEMPENO_GENERAL ?? 'N/A'}</span>
          </div>
        </div>
      </div>
    </div>
    ${cand.RECOMENDACION_SUP ? `
    <div class="recommendation-section mt-3">
      <div class="recommendation-card ${cand.RECOMENDACION_SUP === 'RECOMENDADO' ? 'recommended' : 'not-recommended'}">
        <i class="fas fa-${cand.RECOMENDACION_SUP === 'RECOMENDADO' ? 'thumbs-up' : 'thumbs-down'}"></i>
        <strong>RECOMENDACIÓN DEL SUPERVISOR: ${cand.RECOMENDACION_SUP}</strong>
      </div>
    </div>` : ''}
    ${cand.OBSERVACIONES_DET ? `
    <div class="observations-section mt-3">
      <h6 class="section-title"><i class="fas fa-clipboard-list"></i> Observaciones Detalladas</h6>
      <div class="observations-content">${cand.OBSERVACIONES_DET}</div>
    </div>` : ''}
  `;

  // 🎯 DOCUMENTOS CON PREVIEW E ICONOS PROFESIONALES
  let documentosHTML = '';
  if (documentos.length) {
    documentosHTML = `
      <h6 class="section-title"><i class="fas fa-folder-open"></i> Documentos de Evaluación</h6>
      <div class="documents-grid">`;
    
    documentos.forEach(doc => {
      const iconClass = getDocumentIcon(doc.tipo);
      const colorClass = getDocumentColor(doc.tipo);
      
      documentosHTML += `
        <div class="document-card ${colorClass}">
          <div class="document-header">
            <i class="${iconClass}"></i>
            <div class="document-info">
              <div class="document-name">${doc.nombre}</div>
              <div class="document-type">${doc.tipo}</div>
            </div>
          </div>
          <div class="document-actions">
            <a href="${doc.ruta}" target="_blank" class="btn-action btn-view" title="Ver documento">
              <i class="fas fa-eye"></i>
            </a>
            <a href="${doc.ruta}" download class="btn-action btn-download" title="Descargar">
              <i class="fas fa-download"></i>
            </a>
          </div>
        </div>`;
    });
    documentosHTML += '</div>';
  } else {
    documentosHTML = `
      <div class="no-documents">
        <i class="fas fa-folder-open"></i>
        <p>No hay documentos adjuntos</p>
      </div>`;
  }

  Swal.fire({
    title: `
      <div class="modal-title-professional">
        <div class="title-icon">
          <i class="fas fa-clipboard-check"></i>
        </div>
        <div class="title-text">
          <h3>Procesar Aval de Gerencia</h3>
          <span class="title-subtitle">Solicitud #${id} - Tienda ${tienda}</span>
        </div>
      </div>
    `,
    html: `
      <style>
        .modal-title-professional {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 15px;
          margin-bottom: 20px;
        }
        .title-icon {
          width: 60px;
          height: 60px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 24px;
          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .title-text h3 {
          margin: 0;
          color: #2c3e50;
          font-weight: 600;
        }
        .title-subtitle {
          color: #7f8c8d;
          font-size: 14px;
        }
        
        .section-card {
          background: white;
          border-radius: 12px;
          padding: 20px;
          margin-bottom: 20px;
          box-shadow: 0 2px 10px rgba(0,0,0,0.08);
          border-left: 4px solid;
        }
        
        .section-card.solicitud { border-left-color: #3498db; }
        .section-card.candidato { border-left-color: #2ecc71; }
        .section-card.comentario { border-left-color: #9b59b6; }
        .section-card.documentos { border-left-color: #f39c12; }
        .section-card.decision { border-left-color: #e74c3c; }
        
        .section-title {
          color: #2c3e50;
          font-weight: 600;
          margin-bottom: 15px;
          display: flex;
          align-items: center;
          gap: 8px;
        }
        
        .section-title i {
          width: 20px;
          text-align: center;
        }
        
        .info-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
          gap: 15px;
        }
        
        .info-item {
          display: flex;
          align-items: center;
          padding: 12px 15px;
          background: #f8f9fa;
          border-radius: 8px;
        }
        
        .info-label {
          font-weight: 600;
          color: #495057;
          min-width: 100px;
          margin-right: 10px;
        }
        
        .info-value {
          color: #212529;
          flex: 1;
        }
        
        .evaluation-card {
          background: #f8f9fa;
          border-radius: 8px;
          padding: 15px;
        }
        
        .eval-item {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 10px;
        }
        
        .eval-label {
          font-weight: 600;
          color: #495057;
        }
        
        .eval-badge {
          padding: 4px 12px;
          border-radius: 20px;
          font-size: 11px;
          font-weight: 600;
          text-transform: uppercase;
        }
        
        .eval-badge.excelente { background: #d4edda; color: #155724; }
        .eval-badge.buena { background: #d1ecf1; color: #0c5460; }
        .eval-badge.regular { background: #fff3cd; color: #856404; }
        .eval-badge.mala { background: #f8d7da; color: #721c24; }
        .eval-badge.default { background: #e2e3e5; color: #6c757d; }
        
        .recommendation-card {
          text-align: center;
          padding: 15px;
          border-radius: 8px;
          font-weight: 600;
        }
        
        .recommendation-card.recommended {
          background: linear-gradient(135deg, #2ecc71, #27ae60);
          color: white;
        }
        
        .recommendation-card.not-recommended {
          background: linear-gradient(135deg, #e74c3c, #c0392b);
          color: white;
        }
        
        .observations-section {
          background: #f8f9fa;
          border-radius: 8px;
          padding: 15px;
        }
        
        .observations-content {
          background: white;
          padding: 15px;
          border-radius: 6px;
          border-left: 4px solid #17a2b8;
          font-style: italic;
          line-height: 1.5;
        }
        
        .documents-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
          gap: 20px;
        }
        
        .document-card {
          background: white;
          border-radius: 10px;
          padding: 15px;
          border: 2px solid;
          transition: all 0.3s ease;
        }
        
        .document-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .document-card.reporte { border-color: #e74c3c; }
        .document-card.cv { border-color: #3498db; }
        
        .document-header {
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 12px;
        }
        
        .document-header i {
          font-size: 24px;
          width: 40px;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 8px;
        }
        
        .document-card.reporte i { background: #ffebee; color: #e74c3c; }
        .document-card.cv i { background: #e3f2fd; color: #3498db; }
        
        .document-name {
          font-weight: 600;
          color: #2c3e50;
          font-size: 14px;
        }
        
        .document-type {
          color: #7f8c8d;
          font-size: 12px;
        }
        
        .document-actions {
          display: flex;
          gap: 8px;
        }
        
        .btn-action {
          padding: 8px 12px;
          border-radius: 6px;
          text-decoration: none;
          font-size: 12px;
          font-weight: 600;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
          gap: 5px;
        }
        
        .btn-view {
          background: #3498db;
          color: white;
        }
        
        .btn-download {
          background: #2ecc71;
          color: white;
        }
        
        .btn-action:hover {
          transform: translateY(-1px);
          box-shadow: 0 2px 8px rgba(0,0,0,0.2);
          color: white;
          text-decoration: none;
        }
        
        .no-documents {
          text-align: center;
          padding: 40px;
          color: #7f8c8d;
        }
        
        .no-documents i {
          font-size: 48px;
          margin-bottom: 10px;
          opacity: 0.5;
        }
        
        .form-group-modern {
          margin-bottom: 20px;
        }
        
        .form-label-modern {
          font-weight: 600;
          color: #2c3e50;
          margin-bottom: 8px;
          display: block;
        }
        
        .form-control-modern {
          width: 100%;
          padding: 12px 15px;
          border: 2px solid #e9ecef;
          border-radius: 8px;
          font-size: 14px;
          transition: all 0.3s ease;
        }
        
        .form-control-modern:focus {
          outline: none;
          border-color: #667eea;
          box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .comment-rh-card {
          background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
          color: white;
          border-radius: 12px;
          padding: 20px;
        }
        
        .comment-rh-content {
          background: rgba(255,255,255,0.15);
          padding: 15px;
          border-radius: 8px;
          margin: 10px 0;
          backdrop-filter: blur(5px);
        }
        
        .comment-rh-meta {
          display: flex;
          justify-content: space-between;
          align-items: center;
          font-size: 12px;
          opacity: 0.9;
        }
      </style>
      
      <div style="text-align:left;max-height:580px;overflow-y:auto;padding:0 10px;">

        <!-- RESUMEN DE LA SOLICITUD -->
        <div class="section-card solicitud">
          <h6 class="section-title">
            <i class="fas fa-info-circle"></i> Resumen de la Solicitud
          </h6>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Puesto:</span>
              <span class="info-value">${puesto}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Supervisor:</span>
              <span class="info-value">${supervisor}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Razón:</span>
              <span class="info-value">${razon}</span>
            </div>
          </div>
        </div>

        <!-- INFORMACIÓN DEL CANDIDATO -->
        <div class="section-card candidato">
          <h6 class="section-title">
            <i class="fas fa-user-graduate"></i> Información del Candidato
          </h6>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Nombre:</span>
              <span class="info-value">${cand.CANDIDATO_NOMBRE ?? '—'}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Documento:</span>
              <span class="info-value">${cand.CANDIDATO_DOCUMENTO ?? '—'}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Fecha Evaluación:</span>
              <span class="info-value">${cand.FECHA_DIA_PRUEBA ?? '—'}</span>
            </div>
            <div class="info-item">
              <span class="info-label">Horario:</span>
              <span class="info-value">${cand.HORARIO ?? '—'}</span>
            </div>
          </div>
          ${evaluacionHTML}
        </div>

        <!-- COMENTARIO DE RH -->
        <div class="section-card comentario">
          <div class="comment-rh-card">
            <h6 class="section-title" style="color: white; margin-bottom: 15px;">
              <i class="fas fa-comments"></i> Comentario de Recursos Humanos
            </h6>
            <div class="comment-rh-content">
              ${comentarioRH}
            </div>
            <div class="comment-rh-meta">
              <span><i class="fas fa-user"></i> ${enviadoPor}</span>
              <span><i class="fas fa-clock"></i> ${fechaEnvio}</span>
            </div>
          </div>
        </div>

        <!-- DOCUMENTOS -->
        <div class="section-card documentos">
          ${documentosHTML}
        </div>

        <!-- DECISIÓN -->
        <div class="section-card decision">
          <h6 class="section-title">
            <i class="fas fa-gavel"></i> Su Decisión como Gerente
          </h6>
          <div class="form-group-modern">
            <label class="form-label-modern">Decisión Final:</label>
            <select id="decision-aval" class="form-control-modern">
              <option value="">Seleccione su decisión...</option>
              <option value="APROBADO">✅ APROBAR - Autorizar contratación</option>
              <option value="RECHAZADO">❌ RECHAZAR - No autorizar contratación</option>
            </select>
          </div>
          <div class="form-group-modern">
            <label class="form-label-modern">Comentarios del Gerente:</label>
            <textarea id="comentario-gerente" class="form-control-modern" rows="4" 
                      placeholder="Escriba sus comentarios sobre la decisión tomada..."></textarea>
          </div>
        </div>

      </div>
    `,
    width: '1200px',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-check-circle"></i> Confirmar Decisión',
    cancelButtonText: '<i class="fas fa-times-circle"></i> Cancelar',
    confirmButtonColor: '#2ecc71',
    cancelButtonColor: '#95a5a6',
    customClass: {
      popup: 'swal-wide',
      confirmButton: 'btn-confirm-modern',
      cancelButton: 'btn-cancel-modern'
    },
    preConfirm: () => {
      const decision = $('#decision-aval').val();
      const comentario = $('#comentario-gerente').val().trim();
      if (!decision) { 
        Swal.showValidationMessage('Debe seleccionar una decisión'); 
        return false; 
      }
      if (!comentario || comentario.length < 10) { 
        Swal.showValidationMessage('El comentario debe tener al menos 10 caracteres'); 
        return false; 
      }
      return { decision, comentario };
    }
  }).then(res => {
    if (res.isConfirmed) {
      procesarDecisionAval(id, res.value.decision, res.value.comentario);
    }
  });
}

// 🎯 FUNCIONES AUXILIARES PARA EL DISEÑO
function getBadgeClass(valor) {
  if (!valor) return 'default';
  const val = valor.toLowerCase();
  if (val.includes('excelente')) return 'excelente';
  if (val.includes('buena') || val.includes('bueno')) return 'buena';
  if (val.includes('regular')) return 'regular';
  if (val.includes('mala') || val.includes('malo')) return 'mala';
  return 'default';
}

function getDocumentIcon(tipo) {
  if (tipo.toLowerCase().includes('reporte')) return 'fas fa-file-pdf';
  if (tipo.toLowerCase().includes('cv') || tipo.toLowerCase().includes('curriculum')) return 'fas fa-file-user';
  return 'fas fa-file-alt';
}

function getDocumentColor(tipo) {
  if (tipo.toLowerCase().includes('reporte')) return 'reporte';
  if (tipo.toLowerCase().includes('cv') || tipo.toLowerCase().includes('curriculum')) return 'cv';
  return 'default';
}

// 🆕 FUNCIÓN PARA PROCESAR LA DECISIÓN DEL AVAL
function procesarDecisionAval(idSolicitud, decision, comentario) {
  // Mostrar loading
  Swal.fire({
    title: '<i class="fas fa-spinner fa-spin"></i> Procesando decisión...',
    text: 'Guardando su decisión sobre el aval',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  // Enviar decisión al backend
  $.ajax({
    url: './GerenteTDS/crudaprobaciones.php?action=procesar_decision_aval',
    type: 'POST',
    dataType: 'json',
    data: {
      id_solicitud: idSolicitud,
      decision: decision,
      comentario_gerente: comentario
    },
    success: function(response) {
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: '<i class="fas fa-check-circle"></i> Decisión Registrada',
          html: `
            <div style="text-align: center; padding: 15px;">
              <div style="font-size: 16px; margin-bottom: 15px;">
                Su decisión ha sido registrada exitosamente
              </div>
              <div style="background: ${decision === 'APROBADO' ? '#d4edda' : '#f8d7da'}; 
                          border: 1px solid ${decision === 'APROBADO' ? '#c3e6cb' : '#f1b0b7'}; 
                          border-radius: 8px; padding: 12px; color: ${decision === 'APROBADO' ? '#155724' : '#721c24'};">
                <strong><i class="fas fa-${decision === 'APROBADO' ? 'check' : 'times'}"></i> 
                ${decision === 'APROBADO' ? 'AVAL APROBADO' : 'AVAL RECHAZADO'}</strong>
              </div>
              <div style="margin-top: 15px; font-size: 14px; color: #666;">
                RH y Supervisión podrán ver su decisión y comentario
              </div>
            </div>
          `,
          timer: 4000,
          showConfirmButton: true,
          confirmButtonText: 'Entendido'
        });
        
        // Recargar la tabla para mostrar el nuevo estado
        cargarSolicitudes();
        
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error al procesar',
          text: response.error || 'No se pudo registrar la decisión'
        });
      }
    },
    error: function(xhr, status, error) {
      console.error('Error AJAX:', xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar al servidor'
      });
    }
  });
}

// FUNCIÓN PARA CARGAR Y MOSTRAR RESULTADO DEL AVAL
function cargarResultadoAvalGerente(idSolicitud, tienda, puesto, supervisor, razon) {
  // Mostrar loading
  Swal.fire({
    title: '<i class="fas fa-spinner fa-spin"></i> Cargando resultado...',
    text: 'Obteniendo información de la decisión gerencial',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  // Obtener datos del backend
  $.ajax({
    url: './GerenteTDS/crudaprobaciones.php',
    method: 'GET',
    data: {
      action: 'obtener_resultado_aval',
      id_solicitud: idSolicitud
    },
    dataType: 'json',
    success: function(response) {
      Swal.close(); // Cerrar loading
      
      if (response.success) {
        mostrarModalResultadoAvalGerente(response.data, idSolicitud, tienda, puesto, supervisor, razon);
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
function mostrarModalResultadoAvalGerente(data, idSolicitud, tienda, puesto, supervisor, razon) {
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


// 🆕 FUNCIÓN PARA EXTRAER COMENTARIO LIMPIO - SOLUCIÓN DIRECTA
function extraerComentarioLimpio(comentarioCompleto) {
    if (!comentarioCompleto) return 'Sin comentario adicional';
    
    // 1. Buscar patrones específicos conocidos
    if (comentarioCompleto.includes('plaza que cubrira a alexis')) {
        return 'plaza que cubrira a alexis t 46';
    }
    
    if (comentarioCompleto.includes('no aceptado')) {
        return 'no aceptado';
    }
    
    // 2. Dividir por líneas y buscar el comentario real
    const lineas = comentarioCompleto.split('\n');
    
    // Buscar después de "Comentario de aprobacion:" o "Motivo del rechazo:"
    for (let i = 0; i < lineas.length; i++) {
        const linea = lineas[i].trim();
        if (linea.includes('Comentario de aprobacion:')) {
            const comentario = linea.split('Comentario de aprobacion:')[1];
            if (comentario && comentario.trim().length > 0) {
                return comentario.trim();
            }
        }
        if (linea.includes('Motivo del rechazo:')) {
            const motivo = linea.split('Motivo del rechazo:')[1];
            if (motivo && motivo.trim().length > 0) {
                return motivo.trim();
            }
        }
    }
    
    // 3. Si no encuentra, buscar líneas que NO sean metadata
    const lineasLimpias = lineas.filter(linea => {
        const l = linea.trim().toLowerCase();
        return l && 
               !l.includes('gerencial') &&
               !l.includes('procesado por') &&
               !l.includes('asignado a rrhh') &&
               !l.includes('fecha de procesamiento') &&
               !l.includes('cambio de aprobacion') &&
               !l.match(/^\d{4}-\d{2}-\d{2}/) &&
               l.length > 3;
    });
    
    // Devolver la primera línea limpia que encuentre
    if (lineasLimpias.length > 0) {
        return lineasLimpias[0].trim();
    }
    
    return 'Sin comentario adicional';
}

  //=================================================================================
  // INICIALIZACION DE TODO EL PROGRAMA
  //=================================================================================

    $(document).ready(function () {
      let solicitudes = [];
      let allSolicitudes = [];
      let solicitudesFiltradas = [];
      let rowsPerPage = 10;
      let currentPage = 1;
      let archivosOriginales =[];
      let archivosSeleccionados = new Set();
      let solicitudActual =null;
      let idSolicitudActual = null;
      let modalAbierto = false;
      let modalArchivosAbierto = false;

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
          const comentario = item.COMENTARIO_NUEVO || '-';
          const idHistorico = item.ID_HISTORICO;
          const dirigidoRH = item.DIRIGIDO_RH || '—';
          const noLeidos = parseInt(item.NO_LEIDOS) || 0;
    // NUEVO: Lógica para mostrar asesora de RRHH solo si está aprobada
    const mostrarDirigidoRH = (aprobacion === 'aprobado' && dirigidoRH) 
      ? `<span class="text-success"><i class="fas fa-user-check mr-1"></i><strong>${dirigidoRH}</strong></span>`
      : '<span class="text-muted"><i class="fas fa-user-times mr-1"></i>Sin asignación</span>';      

    console.log('ID:', idHistorico, 'Comentario:', comentario, 'NO_LEIDOS:', item.NO_LEIDOS);
    console.log('Estado Aprobación:', item.ID_SOLICITUD, item.ESTADO_APROBACION);
    console.log('Dirigido RH:', item.ID_SOLICITUD, dirigidoRH, 'Mostrar:', mostrarDirigidoRH); // NUEVO DEBUG
    
// ✅ SOLUCIÓN DRÁSTICA - NO MOSTRAR COMENTARIOS SI HAY DECISIÓN DE GERENTE
// ✅ SOLUCIÓN FORZADA - ELIMINAR COMPLETAMENTE EL BOTÓN DE COMENTARIO
// ✅ SOLO MOSTRAR SI ES REALMENTE UN COMENTARIO DE RRHH Y ESTÁ PENDIENTE

const comentarioMostrar = (() => {
    // 🚫 REGLA ABSOLUTA: NUNCA mostrar botón si hay decisión del gerente
    const estadoAprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase().trim();
    
    console.log('🔍 VERIFICANDO COMENTARIO PARA SOLICITUD:', item.ID_SOLICITUD);
    console.log('📊 Estado de aprobación:', estadoAprobacion);
    console.log('💬 Comentario:', comentario);
    console.log('🆔 ID Histórico:', idHistorico);
    
    // ❌ Si el gerente ya decidió (NO es "por aprobar"), NUNCA mostrar botón
    if (estadoAprobacion !== 'por aprobar') {
        console.log('🚫 GERENTE YA DECIDIÓ - OCULTANDO BOTÓN DE COMENTARIO');
        return '<span class="text-muted">—</span>';
    }
    
    // ❌ Si no hay comentario real, no mostrar
    if (!comentario || 
        comentario === '-' || 
        comentario === '' || 
        comentario.trim() === '' ||
        comentario === 'null' ||
        comentario === 'undefined') {
        console.log('❌ SIN COMENTARIO VÁLIDO');
        return '<span class="text-muted">—</span>';
    }
    
    // ❌ Si es comentario automático del sistema, no mostrar
    const esComentarioAutomatico = 
        comentario.includes('Cambio de aprobación') ||
        comentario.includes('Asignado a:') ||
        comentario.includes('Estado actualizado') ||
        comentario.includes('Procesado por') ||
        comentario.includes('Decisión del gerente') ||
        comentario.length < 10; // Comentarios muy cortos probablemente son automáticos
    
    if (esComentarioAutomatico) {
        console.log('❌ COMENTARIO AUTOMÁTICO DEL SISTEMA');
        return '<span class="text-muted">—</span>';
    }
    
    // ✅ Solo mostrar si pasa TODAS las validaciones
    console.log('✅ COMENTARIO VÁLIDO DE RRHH - MOSTRANDO BOTÓN');
    return `<div class="badge-container">
        <button class="btn btn-sm btn-info btnVerComentarioSuper"
                data-id="${idHistorico}"
                title="Ver comentario de RRHH">
            <i class="fas fa-comment"></i> Ver
        </button>
        ${noLeidos > 0 ? `<span class="notification-badge ${noLeidos > 9 ? 'wide' : ''}">${noLeidos}</span>` : ''}
    </div>`;
})();
//variable de acciones 
let acciones = '';

                // 🆕 AGREGAR ESTAS 2 LÍNEAS DESPUÉS DE LA DECLARACIÓN DE acciones
                // Botón para ver resumen de aprobación (solo si está aprobado)
                if (aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no'))) {
                    acciones += `
                        <button class="btn btn-success btn-sm btnVerResumenAprobacion" 
                                data-id="${item.ID_SOLICITUD}"
                                title="Ver resumen de su aprobación">
                            <i class="fas fa-clipboard-check"></i> Ver Resumen
                        </button>`;
                  }
                if (aprobacion === 'no aprobado') {
                acciones += `
                    <button class="btn btn-warning btn-sm btnVerResultadoAprobacion" 
                            data-id="${item.ID_SOLICITUD}"
                            data-aprobacion="${item.ESTADO_APROBACION}"
                            title="Ver motivo del rechazo">
                    <i class="fas fa-exclamation-circle"></i> Ver Resultado
                    </button>`;
                }


      // Mostrar solo "Ver resumen" si hay selección
          if (estado.toLowerCase().includes('cvs')) {
            if (parseInt(item.TIENE_SELECCION) === 1) {
              acciones += `
                <button class="btn btn-info btn-sm btnVerResumen" data-id="${item.ID_SOLICITUD}">
                    <i class="fas fa-eye"></i> Ver resumen
                </button>`;
            } else {
              acciones += `
                <button class="btn btn-primary btn-sm btnVisualizarArchivos" data-id="${item.ID_SOLICITUD}">
                    <i class="fas fa-folder-open"></i> Archivos
                </button>`;
            }
          }

          if (estado.includes('psico')) {
            acciones += `
              <button class="btn btn-secondary btn-sm btnVerPruebasTipo"
                      data-id="${item.ID_SOLICITUD}"
                      data-tipo="PSICOMETRICA">
                <i class="fas fa-brain"></i> Ver Psicométrica
              </button>`;
          } else if (estado.includes('poligrafo')) {
            acciones += `
              <button class="btn btn-dark btn-sm btnVerPruebasTipo"
                      data-id="${item.ID_SOLICITUD}"
                      data-tipo="POLIGRAFO">
                <i class="fas fa-fingerprint"></i> Ver Polígrafo
              </button>`;
          }

          // 🆕 BOTÓN PROCESAR AVAL - Solo para estados "Pendiente Aval Gerencia"
            if (estado.toLowerCase().includes('pendiente aval gerencia')) {
                acciones += `
                    <button class="btn btn-success btn-sm btnProcesarAval" 
                            data-id="${item.ID_SOLICITUD}"
                            data-tienda="${item.NUM_TIENDA || ''}"
                            data-puesto="${item.PUESTO_SOLICITADO || ''}"
                            data-supervisor="${item.SOLICITADO_POR || ''}"
                            data-razon="${item.RAZON || ''}"
                            title="Procesar Aval de Gerencia">
                        <i class="fas fa-gavel mr-1"></i> Procesar Aval
                    </button>`;
            }

            //BOTON RESULTADO AVAL 
            // En la función renderTable de supervisores, después de los botones existentes:
            // ✅ VERIFICAR SI TIENE RESULTADO DE AVAL
            const tieneResultadoAval = estado.toLowerCase().includes('aval enviado');

            if (tieneResultadoAval) {
                acciones += `
                    <button class="btn btn-success btn-sm btnVerResultadoAval" 
                            data-id="${item.ID_SOLICITUD}"
                            data-tienda="${item.NUM_TIENDA}"
                            data-puesto="${item.PUESTO_SOLICITADO}"
                            data-supervisor="${item.SOLICITADO_POR}"
                            data-razon="${item.RAZON || ''}"
                            title="Ver resultado del aval gerencial">
                        <i class="fas fa-clipboard-check"></i> Ver Resultado Aval
                    </button>`;
            }
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
                    <td>
                      <span class="status-badge ${statusClass}" title="${item.ULTIMO_COMENTARIO || 'Sin comentario'}">
                        ${item.ESTADO_SOLICITUD}
                      </span>
                    </td>
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
                          <i class="fas fa-vote-yea mr-2"></i> Procesar
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

            //===================================================================================================
            // FUNCIONES Y BOTONES AGREGADOS PARA LA VISUALIZACION DE ARCHIVOS, COMO CVS Y PRUEBAS QUE TENIA EL 
            // SUPERVISOR Y AHORA SE AGREGAN A LOS GERENTES PARA QUE TENGAN EL CONTROL DEL PROCESO DE SOLICITUDES
            //====================================================================================================   
//FUNCION PARA VER HISTORIAL DE SOLICITUDES POR TIENDA
// FUNCIÓN MEJORADA PARA VER HISTORIAL GENERAL CON FILTROS AVANZADOS
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
        url: './GerenteTDS/crudaprobaciones.php?action=get_historial_filtrado',
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
    url: './GerenteTDS/crudaprobaciones.php?action=get_tiendas_filtro',
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
    url: './GerenteTDS/crudaprobaciones.php?action=get_supervisores_filtro',
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
    url: './GerenteTDS/crudaprobaciones.php?action=get_puestos_filtro',
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
        const urlExcel = `./GerenteTDS/crudaprobaciones.php?action=generar_reporte_historial&formato=excel${parametrosExportacion}`;
        console.log('🟢 URL Excel:', urlExcel);
        window.open(urlExcel, '_blank');
      };
      
      document.getElementById('btnGenerarPDF').onclick = () => {
        const urlPDF = `./GerenteTDS/crudaprobaciones.php?action=generar_reporte_historial&formato=pdf${parametrosExportacion}`;
        console.log('🔴 URL PDF:', urlPDF);
        window.open(urlPDF, '_blank');
      };
    }
  });
}


//ver archivos de solicitud
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
$(document).off('click', '.btnVisualizarArchivos').on('click', '.btnVisualizarArchivos', function (e) {
  e.preventDefault();
  e.stopImmediatePropagation(); // ← AGREGAR PARA EVITAR PROPAGACIÓN
  
  if (modalArchivosAbierto) return; // ← VALIDACIÓN EXTRA
  
  const id = $(this).data('id');
  mostrarArchivosSolicitud(id, 'CVS');
});

//FUNCION PARA VER LOS ARCHIVOS PSICO Y POLIGRAFO
$(document).off('click', '.btnVerPruebasTipo').on('click', '.btnVerPruebasTipo', function () {
    const idSolicitud = $(this).data('id');
    const tipoArchivo = $(this).data('tipo'); // debe ser 'PSICOMETRICA' o 'POLIGRAFO'

    $('#modalPruebasContenido').html('<p>Cargando archivos...</p>');
    $('#modalVerPruebas').modal('show');

    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=ver_pruebas_adjuntas',
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
        url: './GerenteTDS/crudaprobaciones.php?action=ver_resumen_cvs',
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
            url: './GerenteTDS/crudaprobaciones.php?action=ver_resumen_cvs',
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
            //===================================================================================================
            // FIN FUNCIONES Y BOTONES AGREGADOS PARA LA VISUALIZACION DE ARCHIVOS, COMO CVS Y PRUEBAS QUE 
            // TENIA EL SUPERVISOR
            //====================================================================================================   

              // FUNCIÓN PARA CAMBIAR APROBACIÓN - CON DEBUG COMPLETO
              // 🔄 BUSCAR Y REEMPLAZAR ESTA FUNCIÓN COMPLETA EN solicitudesgerente.php
$(document).off('click', '.btnProcesarSolicitud').on('click', '.btnProcesarSolicitud', function() {
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
            ✅ Aprobado
          </option>
          <option value="No Aprobado" style="color: #dc3545; font-weight: bold;">
            ❌ No Aprobado
          </option>
          <option value="Por Aprobar" style="color: #ffc107; font-weight: bold;">
            ⏳ Por Aprobar
          </option>
        </select>
      </div>

      <!-- ✅ CAMPO CONDICIONAL PARA ASIGNAR RRHH (SOLO CUANDO ES APROBADO) -->
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

      <!-- 🆕 CAMPO OBLIGATORIO PARA COMENTARIO DE APROBACIÓN -->
      <div id="campo-comentario-aprobacion" class="form-group" style="display: none;">
        <div class="alert alert-success">
          <i class="fas fa-comment-check mr-2"></i>
          <strong>Comentario de Aprobación - Obligatorio</strong>
        </div>
        <label for="swal-comentario-aprobacion"><strong>Comentario de aprobacion:</strong></label>
        <textarea 
          id="swal-comentario-aprobacion" 
          class="form-control" 
          rows="3" 
          placeholder="Escriba un comentario explicando los detalles de la aprobación..."
          style="border: 2px solid #28a745; border-radius: 8px; font-size: 14px;">
        </textarea>
        <small class="form-text text-muted">
          <i class="fas fa-info-circle mr-1"></i>
          Este comentario será visible para RRHH y el supervisor como detalle de la aprobación
        </small>
      </div>

      <!-- 🆕 CAMPO OBLIGATORIO PARA COMENTARIO DE RECHAZO -->
      <div id="campo-comentario-rechazo" class="form-group" style="display: none;">
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          <strong>Solicitud Rechazada - Comentario Obligatorio</strong>
        </div>
        <label for="swal-comentario-rechazo"><strong>Motivo del rechazo:</strong></label>
        <textarea 
          id="swal-comentario-rechazo" 
          class="form-control" 
          rows="3" 
          placeholder="Explique el motivo por el cual se rechaza esta solicitud..."
          style="border: 2px solid #dc3545; border-radius: 8px; font-size: 14px;">
        </textarea>
        <small class="form-text text-muted">
          <i class="fas fa-info-circle mr-1"></i>
          Este comentario será visible para el supervisor para que pueda entender el motivo del rechazo
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
      const dirigidoRH = $('#swal-dirigido-rh').val();
      const comentarioAprobacion = $('#swal-comentario-aprobacion').val().trim();
      const comentarioRechazo = $('#swal-comentario-rechazo').val().trim();
      
      if (!nuevaAprobacion) {
        Swal.showValidationMessage(`
          <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
            <span style="font-weight: 600;">Debe seleccionar un estado de aprobación</span>
          </div>
        `);
        return false;
      }

      // ✅ VALIDACIÓN PARA SOLICITUDES APROBADAS
      if (nuevaAprobacion === 'Aprobado') {
        // Validar asignación a RRHH
        if (!dirigidoRH) {
          Swal.showValidationMessage(`
            <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
              <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
              <span style="font-weight: 600;">Debe seleccionar una persona de RRHH para la solicitud aprobada</span>
            </div>
          `);
          return false;
        }

        // 🆕 Validar comentario obligatorio para aprobaciones
        if (!comentarioAprobacion) {
          Swal.showValidationMessage(`
            <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
              <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
              <span style="font-weight: 600;">Debe proporcionar un comentario explicando la aprobación</span>
            </div>
          `);
          return false;
        }

        // 🆕 Validar longitud mínima del comentario de aprobación
        if (comentarioAprobacion.length < 10) {
          Swal.showValidationMessage(`
            <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
              <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
              <span style="font-weight: 600;">El comentario de aprobación debe tener al menos 10 caracteres</span>
            </div>
          `);
          return false;
        }
      }

      // 🆕 VALIDACIÓN PARA SOLICITUDES RECHAZADAS
      if (nuevaAprobacion === 'No Aprobado') {
        if (!comentarioRechazo) {
          Swal.showValidationMessage(`
            <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
              <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
              <span style="font-weight: 600;">Debe proporcionar un motivo para el rechazo de la solicitud</span>
            </div>
          `);
          return false;
        }

        // Validar longitud mínima del comentario de rechazo
        if (comentarioRechazo.length < 10) {
          Swal.showValidationMessage(`
            <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
              <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
              <span style="font-weight: 600;">El motivo del rechazo debe tener al menos 10 caracteres</span>
            </div>
          `);
          return false;
        }
      }
        
      return { 
        nuevaAprobacion: nuevaAprobacion, 
        dirigidoRH: dirigidoRH || null,
        comentarioAprobacion: comentarioAprobacion || null,
        comentarioRechazo: comentarioRechazo || null
      };
    },
    didOpen: () => {
      // ✅ LISTENER PARA MOSTRAR/OCULTAR CAMPOS SEGÚN LA DECISIÓN
      $('#nuevaAprobacion').on('change', function() {
        const decision = $(this).val();
        const campoRRHH = $('#campo-rrhh');
        const campoComentarioAprobacion = $('#campo-comentario-aprobacion');
        const campoComentarioRechazo = $('#campo-comentario-rechazo');
        
        // Ocultar todos los campos primero
        campoRRHH.slideUp(200);
        campoComentarioAprobacion.slideUp(200);
        campoComentarioRechazo.slideUp(200);
        
        // Limpiar campos y remover required
        $('#swal-dirigido-rh').attr('required', false).val('');
        $('#swal-comentario-aprobacion').attr('required', false).val('');
        $('#swal-comentario-rechazo').attr('required', false).val('');
        
        if (decision === 'Aprobado') {
          // Mostrar tanto el campo de RRHH como el de comentario de aprobación
          campoRRHH.slideDown(300);
          campoComentarioAprobacion.slideDown(300);
          $('#swal-dirigido-rh').attr('required', true);
          $('#swal-comentario-aprobacion').attr('required', true);
        } else if (decision === 'No Aprobado') {
          // Mostrar solo el campo de comentario de rechazo
          campoComentarioRechazo.slideDown(300);
          $('#swal-comentario-rechazo').attr('required', true);
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
          .aprobacion-modal-grande select:focus,
          .aprobacion-modal-grande textarea:focus {
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
        dirigido_rh: result.value.dirigidoRH,
        comentario_aprobacion: result.value.comentarioAprobacion,
        comentario_rechazo: result.value.comentarioRechazo
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

      // 🆕 CONSTRUIR LA DATA PARA ENVIAR AL SERVIDOR
      const dataToSend = {
        id_solicitud: id,
        nueva_aprobacion: result.value.nuevaAprobacion
      };

      // ✅ MANEJAR COMENTARIOS SEGÚN EL TIPO DE DECISIÓN
      if (result.value.nuevaAprobacion === 'Aprobado') {
        dataToSend.dirigido_rh = result.value.dirigidoRH;
        dataToSend.comentario = result.value.comentarioAprobacion;
        dataToSend.tipo_comentario = 'aprobacion'; // Para identificar en el backend
      } else if (result.value.nuevaAprobacion === 'No Aprobado') {
        dataToSend.comentario = result.value.comentarioRechazo;
        dataToSend.tipo_comentario = 'rechazo'; // Para identificar en el backend
      } else {
        dataToSend.comentario = `Cambio de aprobación a: ${result.value.nuevaAprobacion}`;
        dataToSend.tipo_comentario = 'general';
      }

      $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=procesar_aprobacion_gerente',
        type: 'POST',
        dataType: 'json',
        data: dataToSend,
        success: function(response) {
          console.log("✅ Respuesta exitosa del servidor:", response);
          if (response.success) {
            // 🆕 MENSAJE DE ÉXITO MEJORADO
            let mensajeExito = `
              <div style="text-align: center; padding: 15px;">
                <div style="font-size: 16px; margin-bottom: 10px;">
                  El estado de aprobación ha sido actualizado correctamente
                </div>
                <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px; color: #155724;">
                  <strong><i class="fas fa-check"></i> Nuevo Estado:</strong> ${result.value.nuevaAprobacion}
                </div>`;

            if (result.value.nuevaAprobacion === 'Aprobado') {
              mensajeExito += `
                <div style="background: #cce5ff; border: 1px solid #99d1ff; border-radius: 8px; padding: 12px; color: #004085; margin-top: 10px;">
                  <strong><i class="fas fa-user-check"></i> Asignada a:</strong> ${result.value.dirigidoRH}
                </div>
                <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px; color: #155724; margin-top: 10px;">
                  <strong><i class="fas fa-comment-check"></i> Comentario de aprobación guardado correctamente</strong>
                </div>`;
            } else if (result.value.nuevaAprobacion === 'No Aprobado') {
              mensajeExito += `
                <div style="background: #f8d7da; border: 1px solid #f1b0b7; border-radius: 8px; padding: 12px; color: #721c24; margin-top: 10px;">
                  <strong><i class="fas fa-comment"></i> Motivo del rechazo enviado al supervisor</strong>
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
                  <small><strong>Error:</strong> ${error}</small>
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
        // 🆕 EVENT LISTENER PARA PROCESAR AVAL DE GERENCIA
        // 🆕 EVENT LISTENER PARA PROCESAR AVAL DE GERENCIA
$(document).off('click', '.btnProcesarAval').on('click', '.btnProcesarAval', function() {
  const id = $(this).data('id');
  const tienda = $(this).data('tienda');
  const puesto = $(this).data('puesto');
  const supervisor = $(this).data('supervisor');
  const razon = $(this).data('razon');

  // 🔍 PRIMERO: Obtener información completa de la solicitud y documentos
  Swal.fire({
    title: '<i class="fas fa-spinner fa-spin"></i> Cargando información...',
    text: 'Obteniendo datos de la solicitud y documentos adjuntos',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  // 📤 AJAX para obtener información completa del aval
  $.ajax({
    url: './GerenteTDS/crudaprobaciones.php?action=obtener_info_aval',
    type: 'GET',
    data: { id_solicitud: id },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        mostrarModalProcesarAval(response.data, id, tienda, puesto, supervisor, razon);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: response.error || 'No se pudo cargar la información del aval'
        });
      }
    },
    error: function(xhr, status, error) {
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar al servidor para obtener la información'
      });
    }
  });
});

                    //BOTON PARA VER RESULTADO DEL AVAL 
                    $(document).on('click', '.btnVerResultadoAval', function() {
                    const idSolicitud = $(this).data('id');
                    const tienda = $(this).data('tienda');
                    const puesto = $(this).data('puesto');
                    const supervisor = $(this).data('supervisor');
                    const razon = $(this).data('razon');
                    
                    cargarResultadoAvalGerente(idSolicitud, tienda, puesto, supervisor, razon);
                  });

// 🆕 FUNCIÓN PARA VER RESUMEN DE APROBACIÓN (GERENTES)
$(document).on('click', '.btnVerResumenAprobacion', function() {
    const id = $(this).data('id');
    const solicitudId = $(this).data('solicitud-id') || id;
    
    // 🆕 OBTENER NOMBRE DEL GERENTE DESDE LA INTERFAZ
    const filaActual = $(this).closest('tr');
    const nombreGerente = filaActual.find('td:nth-child(5)').text().trim() || 'Gerente';
    
    console.log("📋 Gerente viendo su propia aprobación para solicitud:", solicitudId);
    console.log("👤 Nombre del gerente obtenido:", nombreGerente);
    
    // Mostrar loading
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando informacion...',
        html: 'Obteniendo detalles de su aprobacion...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    // Obtener el resumen desde la perspectiva del gerente
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=obtener_resumen_aprobacion_gerente',
        type: 'GET',
        dataType: 'json',
        data: { id_solicitud: solicitudId },
        success: function(response) {
            console.log("✅ Resumen de gerente obtenido:", response);
            
            if (response.success) {
                const solicitud = response.solicitud;
                const resumen = response.resumen_aprobacion;
                
                // 🆕 USAR DIRECTAMENTE LOS DATOS FORMATEADOS DEL SERVIDOR
                const fechaProceso = resumen.fecha_procesamiento || 'No disponible';
                const fechaSolicitud = solicitud.fecha_solicitud || 'N/A';
                const comentarioLimpio = resumen.comentario_aprobacion || 'Sin comentario adicional';
                
                // 🆕 USAR NOMBRE DEL GERENTE OBTENIDO DE LA INTERFAZ
                const nombreGerenteCompleto = nombreGerente !== 'Gerente' ? nombreGerente : (resumen.procesado_por || 'No disponible');
                
                // Determinar el tipo de decision y colores
                const esAprobacion = solicitud.estado_aprobacion === 'Aprobado';
                const colorPrincipal = esAprobacion ? '#28a745' : '#dc3545';
                const iconoPrincipal = esAprobacion ? 'fas fa-check-circle' : 'fas fa-times-circle';
                const textoPrincipal = esAprobacion ? 'APROBADA' : 'RECHAZADA';
                
                Swal.fire({
                    title: `<i class="${iconoPrincipal}"></i> Su Decision: ${textoPrincipal}`,
                    html: `
                        <div style="text-align: left; max-width: 100%;">
                            <!-- INFORMACION BASICA DE LA SOLICITUD -->
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                                <h5 style="margin: 0 0 15px 0; font-weight: 700; display: flex; align-items: center;">
                                    <i class="fas fa-file-alt" style="margin-right: 10px; font-size: 20px;"></i>
                                    Informacion de la Solicitud
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                                    <div><strong>ID:</strong> #${solicitud.id}</div>
                                    <div><strong>Tienda:</strong> ${solicitud.tienda || 'N/A'}</div>
                                    <div><strong>Puesto:</strong> ${solicitud.puesto_solicitado || 'N/A'}</div>
                                    <div><strong>Supervisor:</strong> ${solicitud.supervisor || 'N/A'}</div>
                                    <div style="grid-column: 1 / -1;"><strong>Fecha de Solicitud:</strong> ${fechaSolicitud}</div>
                                </div>
                            </div>

                            <!-- RESUMEN DE SU DECISION -->
                            <div style="background: ${esAprobacion ? '#d4edda' : '#f8d7da'}; border: 2px solid ${colorPrincipal}; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: ${esAprobacion ? '#155724' : '#721c24'}; display: flex; align-items: center;">
                                    <i class="${iconoPrincipal}" style="margin-right: 10px; font-size: 18px; color: ${colorPrincipal};"></i>
                                    Su Decision: ${textoPrincipal}
                                </h6>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <div>
                                        <strong style="color: ${esAprobacion ? '#155724' : '#721c24'};">
                                            <i class="fas fa-user-check"></i> Procesado por Usted:
                                        </strong><br>
                                        <span style="background: ${esAprobacion ? '#c3e6cb' : '#f1b0b7'}; padding: 4px 8px; border-radius: 6px; font-size: 13px;">
                                            ${nombreGerenteCompleto}
                                        </span>
                                    </div>
                                    <div>
                                        <strong style="color: ${esAprobacion ? '#155724' : '#721c24'};">
                                            <i class="fas fa-calendar-check"></i> Fecha de Su Decision:
                                        </strong><br>
                                        <span style="background: ${esAprobacion ? '#c3e6cb' : '#f1b0b7'}; padding: 4px 8px; border-radius: 6px; font-size: 13px;">
                                            ${fechaProceso}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            ${esAprobacion ? `
                                <!-- ASIGNACION A RRHH (Solo para aprobaciones) -->
                                <div style="background: #cce5ff; border: 2px solid #007bff; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                    <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #004085; display: flex; align-items: center;">
                                        <i class="fas fa-user-plus" style="margin-right: 10px; font-size: 18px; color: #007bff;"></i>
                                        Asignacion que Realizo
                                    </h6>
                                    <div style="text-align: center;">
                                        <div style="background: #b3d9ff; border-radius: 8px; padding: 15px; display: inline-block;">
                                            <i class="fas fa-user-tie" style="font-size: 24px; color: #0056b3; margin-bottom: 8px;"></i><br>
                                            <strong style="font-size: 16px; color: #004085;">
                                                ${resumen.asignado_a || solicitud.dirigido_rh || 'No asignado'}
                                            </strong><br>
                                            <small style="color: #6c757d;">Asignado a RRHH</small>
                                        </div>
                                    </div>
                                </div>
                            ` : ''}

                            <!-- COMENTARIO DE SU DECISION -->
                            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 12px; padding: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #856404; display: flex; align-items: center;">
                                    <i class="fas fa-comment-alt" style="margin-right: 10px; font-size: 18px; color: #ffc107;"></i>
                                    ${esAprobacion ? 'Su Comentario de Aprobacion' : 'Su Motivo de Rechazo'}
                                </h6>
                                <div style="background: white; border-radius: 8px; padding: 15px; border: 1px solid #ffeaa7;">
                                    <p style="margin: 0; line-height: 1.6; color: #333;">
                                        ${comentarioLimpio}
                                    </p>
                                </div>
                                <small style="color: #856404; margin-top: 10px; display: block;">
                                    <i class="fas fa-info-circle"></i> 
                                    Fecha del ${esAprobacion ? 'comentario' : 'rechazo'}: ${fechaProceso}
                                </small>
                            </div>
                        </div>
                    `,
                    width: '800px',
                    showCancelButton: false,
                    confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
                    confirmButtonColor: '#6c757d',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'resumen-gerente-modal',
                        confirmButton: 'btn btn-secondary btn-lg px-4'
                    }
                });
                
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'No se pudo cargar la informacion de su decision',
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar resumen del gerente:', {
                status: xhr.status,
                error: error,
                responseText: xhr.responseText
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexion',
                text: 'No se pudo cargar la informacion de su decision',
                confirmButtonText: 'Entendido'
            });
        }
    });
});

// 🆕 FUNCIÓN PARA VER RESULTADO DE RECHAZO (GERENTES)
$(document).on('click', '.btnVerResultadoAprobacion', function() {
    const id = $(this).data('id');
    const aprobacion = $(this).data('aprobacion');
    
    // 🆕 OBTENER NOMBRE DEL GERENTE DESDE LA INTERFAZ
    const filaActual = $(this).closest('tr');
    const nombreGerente = filaActual.find('td:nth-child(5)').text().trim() || 'Gerente';
    
    console.log("📋 Viendo resultado de rechazo para solicitud:", id);
    console.log("👤 Nombre del gerente obtenido:", nombreGerente);
    
    // Mostrar loading
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando informacion...',
        html: 'Obteniendo motivo del rechazo...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    // Obtener el resultado del rechazo
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=obtener_resumen_aprobacion_gerente',
        type: 'GET',
        dataType: 'json',
        data: { id_solicitud: id },
        success: function(response) {
            console.log("✅ Resultado de rechazo obtenido:", response);
            
            if (response.success) {
                const solicitud = response.solicitud;
                const resumen = response.resumen_aprobacion;
                
                // 🆕 USAR DIRECTAMENTE LOS DATOS FORMATEADOS DEL SERVIDOR PARA RECHAZO
                const fechaRechazo = resumen.fecha_procesamiento || 'No disponible';
                const fechaSolicitudRechazo = solicitud.fecha_solicitud || 'N/A';
                const motivoRechazo = resumen.comentario_aprobacion || 'Sin motivo especificado';
                
                // 🆕 USAR NOMBRE DEL GERENTE OBTENIDO DE LA INTERFAZ
                const nombreGerenteCompleto = nombreGerente !== 'Gerente' ? nombreGerente : (resumen.procesado_por || 'No disponible');
                
                Swal.fire({
                    title: '<i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i> Solicitud Rechazada',
                    html: `
                        <div style="text-align: left; max-width: 100%;">
                            <!-- ALERTA DE RECHAZO -->
                            <div style="background: #dc3545; color: white; border-radius: 12px; padding: 20px; margin-bottom: 25px; text-align: center;">
                                <h5 style="margin: 0; font-weight: 700;">
                                    <i class="fas fa-times-circle" style="margin-right: 10px;"></i>
                                    Su solicitud ha sido revisada por el gerente y no ha sido aprobada
                                </h5>
                            </div>

                            <!-- INFORMACION DE LA SOLICITUD -->
                            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #495057; display: flex; align-items: center;">
                                    <i class="fas fa-info-circle" style="margin-right: 10px; font-size: 16px;"></i>
                                    Informacion de la Solicitud
                                </h6>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                                    <div><strong>ID Solicitud:</strong> #${solicitud.id}</div>
                                    <div><strong>Tienda:</strong> ${solicitud.tienda}</div>
                                    <div><strong>Puesto Solicitado:</strong> ${solicitud.puesto_solicitado}</div>
                                    <div><strong>Fecha de Solicitud:</strong> ${fechaSolicitudRechazo}</div>
                                </div>
                            </div>

                            <!-- ESTADO DE APROBACION -->
                            <div style="background: #f8d7da; border: 2px solid #dc3545; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #721c24; display: flex; align-items: center;">
                                    <i class="fas fa-times-circle" style="margin-right: 10px; font-size: 18px; color: #dc3545;"></i>
                                    Estado de Aprobacion
                                </h6>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong style="color: #721c24;">No Aprobado</strong><br>
                                        <small style="color: #6c757d;">Revisado por: ${nombreGerenteCompleto}</small>
                                    </div>
                                    <div style="background: #dc3545; color: white; padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                                        <i class="fas fa-times"></i> RECHAZADA
                                    </div>
                                </div>
                            </div>

                            <!-- MOTIVO DEL RECHAZO -->
                            <div style="background: #fff3cd; border: 2px solid #856404; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #856404; display: flex; align-items: center;">
                                    <i class="fas fa-clipboard-list" style="margin-right: 10px; font-size: 18px;"></i>
                                    Motivo del Rechazo
                                </h6>
                                <div style="background: white; border-radius: 8px; padding: 15px; border: 1px solid #ffeaa7;">
                                    <p style="margin: 0; line-height: 1.6; color: #333;">
                                        ${motivoRechazo}
                                    </p>
                                </div>
                                <small style="color: #856404; margin-top: 10px; display: block;">
                                    <i class="fas fa-clock"></i> 
                                    Fecha del rechazo: ${fechaRechazo}
                                </small>
                            </div>

                            <!-- PROXIMOS PASOS -->
                            <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 12px; padding: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #0c5460; display: flex; align-items: center;">
                                    <i class="fas fa-route" style="margin-right: 10px; font-size: 16px;"></i>
                                    Proximos Pasos
                                </h6>
                                <div style="color: #0c5460; line-height: 1.6;">
                                    <p style="margin: 5px 0; display: flex; align-items: flex-start;">
                                        <i class="fas fa-arrow-right" style="margin-right: 8px; margin-top: 4px; color: #17a2b8;"></i>
                                        <span><strong>Puede revisar el motivo del rechazo</strong> para entender las razones de la decision</span>
                                    </p>
                                    <p style="margin: 5px 0; display: flex; align-items: flex-start;">
                                        <i class="fas fa-arrow-right" style="margin-right: 8px; margin-top: 4px; color: #17a2b8;"></i>
                                        <span><strong>Si considera necesario,</strong> puede crear una nueva solicitud corrigiendo los aspectos mencionados</span>
                                    </p>
                                    <p style="margin: 5px 0; display: flex; align-items: flex-start;">
                                        <i class="fas fa-arrow-right" style="margin-right: 8px; margin-top: 4px; color: #17a2b8;"></i>
                                        <span><strong>Para dudas adicionales,</strong> puede contactar directamente con el gerente para aclaraciones</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    `,
                    width: '700px',
                    showCancelButton: false,
                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                    confirmButtonColor: '#007bff',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'resultado-rechazo-modal',
                        confirmButton: 'btn btn-primary btn-lg px-4'
                    }
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'No se pudo cargar la informacion del rechazo',
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar resultado de rechazo:', {
                status: xhr.status,
                error: error,
                responseText: xhr.responseText
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexion',
                text: 'No se pudo cargar la informacion del rechazo',
                confirmButtonText: 'Entendido'
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