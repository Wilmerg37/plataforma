/* =====================================================
   ARCHIVO: solicitudes-rh.js 
   DESCRIPCIÓN: Funciones JavaScript centralizadas para el sistema RH
   ===================================================== */

// ===== VARIABLES GLOBALES =====
window.ROL_USUARIO = window.ROL_USUARIO || 'RRHH';
window.CANDIDATOS_INDEX = {};
window.solicitudIdTemporal = null;
let modalArchivosAbierto = false;

// ===== FUNCIONES UTILITARIAS =====
function _isJefe(puesto) { 
  return String(puesto || '').toUpperCase().includes('JEFE'); 
}

function _safeJSON(x) { 
  try { 
    return (typeof x === 'string') ? JSON.parse(x) : x; 
  } catch(e) { 
    return null; 
  } 
}

function _txt(x) { 
  return (x == null ? '' : String(x)); 
}

function _fallbackEstados() {
  return ['CV Enviado','Psicometrica','Entrevista Tecnica','Dia de Prueba','Entrevista RH','Poligrafo','Expediente RH','Aprobado para Aval'];
}

function resolverIdSolicitud(idPreferido) {
  console.log('🔧 Resolviendo ID de solicitud. Preferido:', idPreferido);
  
  // 1) Si lo pasaron directo y es válido
  if (idPreferido && !isNaN(parseInt(idPreferido, 10)) && parseInt(idPreferido, 10) > 0) {
    const id = parseInt(idPreferido, 10);
    console.log('✅ Usando ID preferido válido:', id);
    return id;
  }
  
  // 2) Modal de expedientes (PRINCIPAL)
  const idModalExp = $('#modalExpedientes').data('id-solicitud');
  if (idModalExp && !isNaN(parseInt(idModalExp, 10)) && parseInt(idModalExp, 10) > 0) {
    const id = parseInt(idModalExp, 10);
    console.log('✅ Usando ID del modal expedientes:', id);
    return id;
  }
  
  // 3) Variable global
  if (window.solicitudIdTemporal && !isNaN(parseInt(window.solicitudIdTemporal, 10)) && parseInt(window.solicitudIdTemporal, 10) > 0) {
    const id = parseInt(window.solicitudIdTemporal, 10);
    console.log('✅ Usando ID temporal global:', id);
    return id;
  }
  
  // 4) Buscar en la tabla activa
  const filaSolicitudActiva = $('tr.active, tr.table-active').first();
  if (filaSolicitudActiva.length > 0) {
    const idTabla = filaSolicitudActiva.data('id');
    if (idTabla && !isNaN(parseInt(idTabla, 10)) && parseInt(idTabla, 10) > 0) {
      const id = parseInt(idTabla, 10);
      console.log('✅ Usando ID de fila activa:', id);
      return id;
    }
  }
  
  console.error('❌ No se pudo resolver ID de solicitud válido');
  return null;
}

//mostrar candidatos enviados rh
//mostrar candidatos enviados rh - FUNCIÓN ÚNICA CORREGIDA
//mostrar candidatos enviados rh - FUNCIÓN ÚNICA CORREGIDA
function mostrarCandidatosEnviadosrh(idSolicitud, filtroEstado = 'todos') {
  
  // ✅ PREVENIR DOBLE EJECUCIÓN
  if (window.cargandoCandidatos) {
    console.log('⚠️ Ya se están cargando candidatos, ignorando...');
    return;
  }
  window.cargandoCandidatos = true;
  
  console.log('🚀 Ver candidatos para solicitud:', idSolicitud, 'Filtro:', filtroEstado);
  
  // ✅ VALIDACIÓN ROBUSTA DEL ID
  if (!idSolicitud) {
    window.cargandoCandidatos = false;
    Swal.fire('Error', 'ID de solicitud no proporcionado', 'error');
    return;
  }
  
  const idString = String(idSolicitud).trim();
  
  if (!idString || idString === '' || isNaN(idString)) {
    window.cargandoCandidatos = false;
    Swal.fire('Error', 'ID de solicitud debe ser un número válido', 'error');
    console.error('ID inválido recibido:', idSolicitud, 'tipo:', typeof idSolicitud);
    return;
  }
  
  const idNumerico = parseInt(idString, 10);
  
  if (idNumerico <= 0) {
    window.cargandoCandidatos = false;
    Swal.fire('Error', 'ID de solicitud debe ser mayor a 0', 'error');
    return;
  }
  
  console.log('ID validado correctamente:', idNumerico);
  
  // Mostrar loading
  Swal.fire({
    title: 'Cargando candidatos...',
    text: 'Obteniendo información de los candidatos',
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => Swal.showLoading()
  });
  
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_candidatos_solicitud_rh',
    type: 'GET',
    data: { 
      id_solicitud: idNumerico,
      filtro_estado: filtroEstado,
      _timestamp: Date.now()
    },
    dataType: 'json',
    timeout: 10000,
    success: function(response) {
      Swal.close();
      window.cargandoCandidatos = false;
      
      if (response.success) {
        const candidatos = response.candidatos || [];
        const solicitud = response.solicitud || {};
        
        console.log('📦 Respuesta del servidor:', response);
        console.log('📋 Solicitud info:', solicitud);
        console.log('🔄 ¿Es reactivada?:', solicitud.reactivada);
        console.log('📊 Estado actual de solicitud:', solicitud.estado);
        
        candidatos.forEach(candidato => {
          candidato.ID_SOLICITUD = idNumerico;
        });
        
        window.CANDIDATOS_INDEX = {};
        candidatos.forEach(candidato => {
          window.CANDIDATOS_INDEX[candidato.ID_CANDIDATO] = candidato;
        });
        
        window.SOLICITUD_ACTUAL = solicitud;
        window.solicitudIdTemporal = idNumerico;
        
        if (solicitud.reactivada === 'Y') {
          console.log('🔄 SOLICITUD REACTIVADA - Mostrando panel de selección');
          mostrarPanelReactivacionRH(idNumerico, solicitud, candidatos);
        } 
        else {
          console.log('📄 Solicitud normal - Mostrando modal estándar');
          mostrarModalExpedientesrh(idNumerico, candidatos, filtroEstado);
          
          if (solicitud.tienda && solicitud.puesto) {
            $('#expedientesLabel').html(`
              <i class="fas fa-folder-open mr-3"></i>
              Expedientes - Tienda ${solicitud.tienda} - ${solicitud.puesto}
            `);
          }
        }
      }
    },
    error: function(xhr, status, error) {
      Swal.close();
      window.cargandoCandidatos = false;
      console.error('Error cargando candidatos:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: `No se pudo conectar al servidor (${xhr.status}: ${error})`
      });
    }
  });
}
//======================================================================================
//REACTIVAVION DE SOLICITUD RH - MOSTRAR PANEL DE SELECCION DE CANDIDATOS 
//======================================================================================
///*Panel especial para RH cuando la solicitud está reactivada
 /* Permite seleccionar qué candidatos reactivar con checkboxes*/
function mostrarPanelReactivacionRH(idSolicitud, solicitud, todosCandidatos) {
  console.log('📋 Mostrando panel de reactivación para RH');
  console.log('📦 Total candidatos disponibles:', todosCandidatos.length);
  // ✅ GUARDAR CONTEXTO GLOBAL PARA PODER RECREAR EL MODAL
  window.CONTEXTO_REACTIVACION_RH = {
    idSolicitud: idSolicitud,
    solicitud: solicitud,
    candidatos: todosCandidatos
  };

  const candidatosDisponibles = todosCandidatos.filter(c => {
    return c.ESTADO_CANDIDATO !== 'Contratado' && 
           c.ESTADO_CANDIDATO !== 'contratado';
  });

  console.log('✅ Candidatos disponibles para reactivar:', candidatosDisponibles.length);

  const tienda = solicitud.tienda || solicitud.NUM_TIENDA || 'No especificada';
  const puesto = solicitud.puesto || solicitud.PUESTO_SOLICITADO || 'No especificado';
  const supervisor = solicitud.supervisor || solicitud.SOLICITADO_POR || 'No asignado';
  const motivoReactivacion = solicitud.motivo_reactivacion || solicitud.MOTIVO_REACTIVACION || 'Sin motivo especificado';
  const nombreGerente = solicitud.nombre_gerente || solicitud.NOMBRE_GERENTE || solicitud.usuario_reactivacion || 'Gerente no identificado';

  let htmlCandidatos = '';
  
  if (candidatosDisponibles.length === 0) {
    htmlCandidatos = `
      <div class="alert alert-warning text-center">
        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
        <h5>No hay candidatos disponibles para reactivar</h5>
        <p>Todos los candidatos fueron contratados o eliminados del proceso.</p>
      </div>
    `;
  } else {
    htmlCandidatos = '<div class="list-group" style="margin-top: 10px;">';
    
    candidatosDisponibles.forEach(candidato => {
      const nombreCompleto = `${candidato.NOMBRE_CANDIDATO || ''} ${candidato.APELLIDOS_CANDIDATO || ''}`.trim();
      const estadoActual = candidato.ULTIMO_ESTADO_ALCANZADO || candidato.ESTADO_CANDIDATO || 'Sin estado';
      const esDescartado = candidato.ACTIVO === 'N';
      const dpi = candidato.DOCUMENTO_CANDIDATO || 'Sin DPI';
      const totalArchivos = candidato.TOTAL_ARCHIVOS || 0;
      
      let badgeClass = 'badge-secondary';
      let iconoEstado = 'fa-user';
      let estadoTexto = estadoActual;

      if (esDescartado) {
          badgeClass = 'badge-danger';
          iconoEstado = 'fa-user-times';
          estadoTexto = `${estadoActual} (Descartado)`;
      } else if (estadoActual.toLowerCase().includes('cv enviado')) {
          badgeClass = 'badge-success';
      } else if (estadoActual.toLowerCase().includes('seleccion')) {
          badgeClass = 'badge-warning';
      }

      let infoDescarte = '';
      if (esDescartado && candidato.MOTIVO_DESCARTE) {
          infoDescarte = `
              <div class="mt-2 p-2" style="background: #fff3cd; border-left: 3px solid #ffc107; font-size: 0.85rem;">
                  <strong><i class="fas fa-ban text-danger"></i> Motivo:</strong>
                  <span class="text-muted d-block mt-1">${candidato.MOTIVO_DESCARTE}</span>
              </div>
          `;
      }

htmlCandidatos += `
        <div class="list-group-item" style="padding: 15px;">
          <div class="d-flex align-items-start">
            <div class="mr-3" style="min-width: 30px;">
              <input type="checkbox" 
                    class="checkbox-reactivar-candidato" 
                    id="checkCandidato${candidato.ID_CANDIDATO}"
                    data-candidato-id="${candidato.ID_CANDIDATO}"
                    data-nombre="${nombreCompleto}"
                    data-estado="${estadoActual}"
                    data-descartado="${esDescartado ? 'true' : 'false'}"
                    style="width: 20px; height: 20px; cursor: pointer;">
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="flex-grow-1">
                    <h6 class="mb-1" style="font-size: 1rem; font-weight: 600;">
                      <i class="fas ${iconoEstado} mr-2"></i>${nombreCompleto}
                    </h6>
                    <small class="text-muted" style="font-size: 0.85rem;">
                      <i class="fas fa-id-card mr-1"></i>DPI: ${dpi}
                    </small>
                    <div class="mt-1" style="font-size: 0.8rem; color: #6c757d;">
                      <i class="fas fa-flag mr-1"></i> ${estadoActual}
                      ${totalArchivos > 0 ? ` | <i class="fas fa-folder-open ml-2"></i> ${totalArchivos} archivo${totalArchivos > 1 ? 's' : ''}` : ''}
                    </div>
                  </div>
                  <div class="ml-3">
                    ${totalArchivos > 0 ? `
                      <button type="button" 
                              class="btn btn-sm btn-info btn-ver-archivos-reactivacion" 
                              data-candidato-id="${candidato.ID_CANDIDATO}"
                              data-nombre="${nombreCompleto}"
                              title="Ver archivos del candidato"
                              style="padding: 5px 12px;">
                        <i class="fas fa-folder-open mr-1"></i>Ver Archivos
                      </button>
                    ` : `
                      <button type="button" 
                              class="btn btn-sm btn-secondary" 
                              disabled
                              title="Sin archivos">
                        <i class="fas fa-folder-open mr-1"></i>Sin Archivos
                      </button>
                    `}
                  </div>
                    <div class="text-right">
                        <span class="badge ${badgeClass}" style="font-size: 0.8rem;">
                          ${estadoTexto}
                        </span>
                    </div>
                </div>
              ${infoDescarte}
            </div>
          </div>
        </div>
      `;
    });
    
    htmlCandidatos += '</div>';
  }

  const htmlModal = `
    <div class="modal fade" id="modalReactivacionRH" tabindex="-1" role="dialog" data-backdrop="static">
      <div class="modal-dialog modal-xl" role="document" style="max-width: 1200px;">
        <div class="modal-content">
          <div class="modal-header bg-warning text-dark">
            <h4 class="modal-title">
              <i class="fas fa-redo-alt mr-2"></i>
              Confirmar Reactivación de Candidatos
            </h4>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          
          <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
            <div class="alert alert-info">
              <h5><i class="fas fa-info-circle mr-2"></i>Información de la Solicitud</h5>
              <div class="row">
                <div class="col-md-6">
                  <p class="mb-1"><strong>Tienda:</strong> ${tienda}</p>
                  <p class="mb-1"><strong>Puesto:</strong> ${puesto}</p>
                </div>
                <div class="col-md-6">
                  <p class="mb-1"><strong>Supervisor:</strong> ${supervisor}</p>
                  <p class="mb-1"><strong>Estado:</strong> <span class="badge badge-warning">Reactivada</span></p>
                </div>
              </div>
            </div>

            <div class="alert alert-warning">
              <h5><i class="fas fa-comment-alt mr-2"></i>Motivo de Reactivación</h5>
              <div class="mb-2">
                <strong><i class="fas fa-user-tie mr-1"></i>Reactivado por:</strong> 
                <span class="badge badge-warning">${nombreGerente}</span>
              </div>
              <div class="p-3" style="background: white; border-radius: 5px;">
                <p class="mb-0" style="white-space: pre-wrap;">${motivoReactivacion}</p>
              </div>
            </div>

            <div class="alert alert-primary">
              <h5><i class="fas fa-tasks mr-2"></i>Instrucciones</h5>
              <ul class="mb-0">
                <li>Seleccione los candidatos que desea reactivar</li>
                <li>Los candidatos <strong>descartados</strong> volverán a estado activo</li>
                <li>Los supervisores y gerentes solo verán los que usted reactive</li>
              </ul>
            </div>

            <div class="card">
              <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">
                    <i class="fas fa-users mr-2"></i>
                    Candidatos Disponibles
                    <span class="badge badge-primary ml-2">${candidatosDisponibles.length}</span>
                  </h5>
                  ${candidatosDisponibles.length > 0 ? `
                    <div>
                      <button type="button" class="btn btn-sm btn-outline-primary" id="btnSeleccionarTodos">
                        <i class="fas fa-check-square mr-1"></i>Todos
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="btnDeseleccionarTodos">
                        <i class="fas fa-square mr-1"></i>Ninguno
                      </button>
                    </div>
                  ` : ''}
                </div>
              </div>
              <div class="card-body" style="max-height: 500px; overflow-y: auto; padding: 10px;">
                ${htmlCandidatos}
              </div>
            </div>

            ${candidatosDisponibles.length > 0 ? `
              <div class="mt-3 text-center">
                <span class="badge badge-primary" id="contadorSeleccionados" style="font-size: 1.1rem; padding: 12px 25px;">
                  <i class="fas fa-check-circle mr-2"></i>
                  <span id="numeroSeleccionados">0</span> candidatos seleccionados
                </span>
              </div>
            ` : ''}
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            ${candidatosDisponibles.length > 0 ? `
              <button type="button" class="btn btn-success btn-lg" id="btnConfirmarReactivacionRH" disabled>
                <i class="fas fa-check-circle mr-2"></i>
                Confirmar Reactivación
              </button>
            ` : ''}
          </div>
        </div>
      </div>
    </div>
  `;

  $('#modalReactivacionRH').remove();
  $('body').append(htmlModal);
  
  $('#modalReactivacionRH').modal('show');

  // ============ EVENTOS - DESPUÉS DEL MODAL ============
setTimeout(function() {
    console.log('🔧 CONFIGURANDO...');
    
    const checkboxes = $('.checkbox-reactivar-candidato');
    console.log('Checkboxes:', checkboxes.length);
    
    if (checkboxes.length === 0) {
      console.error('❌ Sin checkboxes');
      return;
    }
    
    const actualizar = function() {
      const t = $('.checkbox-reactivar-candidato:checked').length;
      console.log('📊 Total:', t);
      
      // Recrear badge completo
      $('#contadorSeleccionados').html(`
        <i class="fas fa-check-circle mr-2"></i>
        <span id="numeroSeleccionados">${t}</span> candidatos seleccionados
      `);
      
      $('#btnConfirmarReactivacionRH').prop('disabled', t === 0);
      console.log(t > 0 ? 'Botón ON' : 'Botón OFF');
    };
    
    // Limpiar
    checkboxes.off('change');
    $('#btnSeleccionarTodos').off('click');
    $('#btnDeseleccionarTodos').off('click');
    $('#btnConfirmarReactivacionRH').off('click');
    
    // Registrar
    checkboxes.on('change', function() {
      console.log('✓ Cambio');
      actualizar();
    });
    
    $('#btnSeleccionarTodos').on('click', function(e) {
      e.preventDefault();
      console.log('📋 Todos');
      checkboxes.prop('checked', true);
      actualizar();
    });
    
    $('#btnDeseleccionarTodos').on('click', function(e) {
      e.preventDefault();
      console.log('📋 Ninguno');
      checkboxes.prop('checked', false);
      actualizar();
    });

    // ✅ EVENTO: VER ARCHIVOS DE CANDIDATO
    $('.btn-ver-archivos-reactivacion').off('click').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const idCandidato = $(this).data('candidato-id');
      const nombreCandidato = $(this).data('nombre');
      
      console.log('📁 Ver archivos del candidato:', idCandidato, nombreCandidato);
      
      // Cerrar modal de reactivación temporalmente
      $('#modalReactivacionRH').modal('hide');
      
      // Llamar función para mostrar archivos
      if (typeof window.verArchivosCandidatoRH === 'function') {
        window.verArchivosCandidatoRH(idCandidato, nombreCandidato, idSolicitud);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Función de ver archivos no disponible'
        });
      }
    });
    
    $('#btnConfirmarReactivacionRH').on('click', function(e) {
      e.preventDefault();
      console.log('✅ Confirmar');
      confirmarReactivacionCandidatosRH(idSolicitud);
    });
    
    console.log('✅ OK');
  }, 1000);
  
  $('#modalReactivacionRH').on('hidden.bs.modal', function() {
    console.log('🗑️ Cerrar');
    $(this).remove();
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
  });
}

/**
 * Mostrar archivos detallados de un candidato
 */
function mostrarArchivosDetallados(idCandidato, nombreCandidato, todosCandidatos) {
  const candidato = todosCandidatos.find(c => c.ID_CANDIDATO == idCandidato);
  
  if (!candidato) {
    Swal.fire({
      icon: 'info',
      title: 'Candidato no encontrado',
      text: 'No se pudo obtener la información del candidato',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  // ✅ MOSTRAR LOADING
  Swal.fire({
    title: 'Cargando archivos...',
    text: 'Obteniendo expediente del candidato',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  // ✅ OBTENER ARCHIVOS REALES DEL SERVIDOR
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'GET',
    data: {
      action: 'get_archivos_candidato',
      id_candidato: idCandidato
    },
    dataType: 'json',
    success: function(response) {
      if (response.success && response.archivos && response.archivos.length > 0) {
        const archivos = response.archivos;
        
        // Agrupar archivos por estado
        const archivosPorEstado = {};
        archivos.forEach(arch => {
          if (!archivosPorEstado[arch.ESTADO_RELACIONADO]) {
            archivosPorEstado[arch.ESTADO_RELACIONADO] = [];
          }
          archivosPorEstado[arch.ESTADO_RELACIONADO].push(arch);
        });
        
        // Generar HTML de archivos agrupados
        let htmlArchivos = '';
        Object.keys(archivosPorEstado).forEach(estado => {
          const archivosEstado = archivosPorEstado[estado];
          htmlArchivos += `
            <div class="mb-3">
              <h6 class="text-primary mb-2">
                <i class="fas fa-folder"></i> ${estado}
                <span class="badge badge-primary badge-pill">${archivosEstado.length}</span>
              </h6>
              <ul class="list-group list-group-flush">
          `;
          
          archivosEstado.forEach(arch => {
            const iconoTipo = arch.TIPO_ARCHIVO === 'application/pdf' || arch.NOMBRE_ARCHIVO.toLowerCase().endsWith('.pdf') 
              ? 'fa-file-pdf text-danger' 
              : 'fa-file text-secondary';
            
            const rutaArchivo = `./gestionhumana/archivos_candidatos/${arch.NOMBRE_ARCHIVO}`;
            
            htmlArchivos += `
              <li class="list-group-item py-2 px-3" style="font-size: 13px;">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="flex-grow-1">
                    <i class="fas ${iconoTipo} mr-2"></i>
                    <a href="${rutaArchivo}" target="_blank" class="text-primary" style="text-decoration: none;">
                      ${arch.NOMBRE_ARCHIVO}
                    </a>
                    <small class="text-muted d-block ml-4">
                      <i class="far fa-calendar"></i> ${arch.FECHA_SUBIDA || 'Sin fecha'}
                    </small>
                  </div>
                  <a href="${rutaArchivo}" target="_blank" class="btn btn-sm btn-outline-primary" title="Abrir archivo">
                    <i class="fas fa-external-link-alt"></i>
                  </a>
                </div>
              </li>
            `;
          });
          
          htmlArchivos += `
              </ul>
            </div>
          `;
        });
        
        Swal.fire({
          title: `📁 Archivos de ${nombreCandidato}`,
          html: `
            <div class="text-left" style="max-height: 400px; overflow-y: auto;">
              <div class="alert alert-info mb-3">
                <strong>Total de archivos:</strong> ${archivos.length}
                <small class="d-block mt-1 text-muted">
                  <i class="fas fa-info-circle"></i> Click en el nombre para abrir el archivo
                </small>
              </div>
              ${htmlArchivos}
            </div>
          `,
          width: '700px',
          confirmButtonText: 'Cerrar',
          confirmButtonColor: '#6c757d'
        });
        
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Sin archivos',
          text: 'Este candidato no tiene archivos en su expediente',
          confirmButtonText: 'Entendido'
        });
      }
    },
    error: function() {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudieron cargar los archivos del candidato',
        confirmButtonText: 'Entendido'
      });
    }
  });
}

/**
 * Función auxiliar para cerrar el modal de forma compatible
 */
function cerrarModalReactivacion() {
  try {
    const modal = $('#modalReactivacionRH');
    
    // Cerrar el modal usando diferentes métodos según disponibilidad
    if (modal.length && typeof modal.modal === 'function') {
      modal.modal('hide');
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      const bsModal = bootstrap.Modal.getInstance(modal[0]);
      if (bsModal) bsModal.hide();
    }
    
    // Limpiar todo después de cerrar
    setTimeout(() => {
      modal.remove();
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');
      $('body').css('padding-right', '');
    }, 300);
    
  } catch (error) {
    console.error('Error al cerrar modal:', error);
    // Forzar limpieza manual
    $('#modalReactivacionRH').remove();
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
  }
}

/**
 * Confirmar reactivación de candidatos seleccionados por RH
 */
function confirmarReactivacionCandidatosRH(idSolicitud) {
  // Obtener candidatos seleccionados
  const candidatosSeleccionados = [];
  
  $('.checkbox-reactivar-candidato:checked').each(function() {
    const checkbox = $(this);
    candidatosSeleccionados.push({
      id: checkbox.data('candidato-id'),
      nombre: checkbox.data('nombre'),
      estado: checkbox.data('estado'),
      descartado: checkbox.data('descartado') === 'true'
    });
  });

  if (candidatosSeleccionados.length === 0) {
    Swal.fire('Error', 'Debe seleccionar al menos un candidato para reactivar', 'warning');
    return;
  }

  console.log('📤 Solicitando confirmación para reactivar:', candidatosSeleccionados);

  // ✅ PEDIR CONFIRMACIÓN PRIMERO
  Swal.fire({
    icon: 'question',
    title: '¿Confirmar reactivación?',
    html: `
      <p>Está a punto de reactivar <strong>${candidatosSeleccionados.length}</strong> candidato(s).</p>
      <p class="mt-2">Los supervisores y gerentes podrán continuar el proceso de selección.</p>
      <p class="mt-1 text-muted">Esta acción cambiará el estado a "Candidatos en Selección".</p>
    `,
    showCancelButton: true,
    confirmButtonText: 'Sí, reactivar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d'
  }).then((result) => {
    if (result.isConfirmed) {
      // ✅ SI CONFIRMA, ENTONCES ENVIAR AL BACKEND
      console.log('✅ Usuario confirmó, enviando al backend...');
      enviarReactivacionAlBackend(idSolicitud, candidatosSeleccionados);
    } else {
      console.log('❌ Usuario canceló la reactivación');
    }
  });
}

/**
 * Enviar la confirmación de reactivación al backend - CORREGIDO
 */
function enviarReactivacionAlBackend(idSolicitud, candidatosSeleccionados) {
  // Mostrar loading
  Swal.fire({
    title: 'Reactivando candidatos...',
    html: 'Procesando solicitud de reactivación...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  // Preparar IDs para enviar
  const idsCandidatos = candidatosSeleccionados.map(c => c.id);

  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'POST',
    dataType: 'json',
    data: {
      action: 'confirmar_reactivacion_rh',
      id_solicitud: idSolicitud,
      candidatos_ids: JSON.stringify(idsCandidatos)
    },
    success: function(response) {
      if (response.success) {
        // ✅ MARCAR QUE HUBO REACTIVACIÓN RECIENTE
        window.ultimaReactivacion = idSolicitud;
        console.log('✅ Marcando solicitud como recién reactivada:', idSolicitud);
        
        Swal.fire({
          icon: 'success',
          title: '¡Candidatos reactivados!',
          html: `
            <p>Se reactivaron exitosamente <strong>${response.total_reactivados || candidatosSeleccionados.length}</strong> candidatos.</p>
            <p class="mt-2">Los supervisores y gerentes ya pueden continuar el proceso.</p>
            <p class="mt-1"><strong>Estado actualizado: Candidatos en Selección</strong></p>
          `,
          confirmButtonText: 'Ver Expedientes'
        }).then(() => {
          // ✅ Cerrar modal de reactivación
          cerrarModalReactivacion();
          
          // ✅ ABRIR MODAL CON EXPEDIENTES DE CANDIDATOS REACTIVADOS
          setTimeout(() => {
            mostrarCandidatosEnviadosrh(idSolicitud, 'reactivados');
          }, 500);
          
          // ✅ RECARGAR LA TABLA DE SOLICITUDES
          if (typeof cargarSolicitudesRH === 'function') {
            setTimeout(() => {
              cargarSolicitudesRH();
            }, 1000);
          }
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error al reactivar',
          text: response.error || 'No se pudo completar la reactivación',
          confirmButtonText: 'Entendido'
        });
      }
    },
    error: function(xhr, status, error) {
      console.error('Error AJAX:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar con el servidor: ' + error,
        confirmButtonText: 'Entendido'
      });
    }
  });
}

// ====================================================================
// EXPORTS Y CONFIGURACIÓN GLOBAL
// ====================================================================

// Asegurar que las funciones estén disponibles globalmente
window.mostrarCandidatosEnviadosrh = mostrarCandidatosEnviadosrh;
window.mostrarPanelReactivacionRH = mostrarPanelReactivacionRH;
window.confirmarReactivacionCandidatosRH = confirmarReactivacionCandidatosRH;

console.log('✅ Módulo de reactivación RH cargado correctamente');

//========================FIN DE PANEL DE REACTIVACION RH=================================


// ================================
// NUEVA FUNCIÓN: mostrarListaCandidatosrh CON FILTROS
// ================================

function mostrarListaCandidatosrh(candidatos, idSolicitud, filtroActual = 'todos') {
  console.log('RH - Iniciando con', candidatos.length, 'candidatos (ya filtrados por backend)');
  
  // CREAR ÍNDICE GLOBAL
  window.CANDIDATOS_INDEX = {};
  candidatos.forEach(candidato => {
    candidato.ID_SOLICITUD = idSolicitud;
    window.CANDIDATOS_INDEX[candidato.ID_CANDIDATO] = candidato;
  });

  if (candidatos.length === 0) {
    $('#listaCandidatos').html(`
      <div class="text-center py-5">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">No hay candidatos</h6>
      </div>
    `);
    return;
  }
  
  // ============================================================================
  // DETECTAR CONTEXTO (el backend ya filtró, solo detectamos para mensajes)
  // ============================================================================
  
  const hayReactivados = candidatos.some(c => c.REACTIVADO_POST_CONTRATACION === 'Y');
  const hayContratado = candidatos.some(c => 
    c.ESTADO_CANDIDATO === 'Contratado' || c.ESTADO_CANDIDATO === 'contratado'
  );
  
  // Todos los candidatos que vienen ya son los que debemos mostrar
  const candidatosAMostrar = candidatos;
  
  console.log('📊 Contexto:', {
    total: candidatos.length,
    hayReactivados,
    hayContratado
  });

  // Ocultar botón de agregar candidatos si hay contratados
  if (hayContratado) {
    $('#btnCargarCandidatos').hide();
  } else {
    $('#btnCargarCandidatos').show();
  }

  // CONTAR CANDIDATOS POR CATEGORÍA
  let candidatosActivos = 0;
  let candidatosDescartados = 0;
  let candidatosAvales = 0;
  let candidatosContratadosCount = 0;
  
  candidatosAMostrar.forEach(c => {
    const esDescartado = c.ES_DESCARTADO === true || c.ACTIVO === 'N';
    const estadoActual = (c.ESTADO_CANDIDATO || '').toLowerCase();
    const esAval = estadoActual.includes('aprobacion') && estadoActual.includes('aval');
    const esContratado = estadoActual === 'contratado';
    
    if (esContratado) {
      candidatosContratadosCount++;
    } else if (esDescartado) {
      candidatosDescartados++;
    } else if (esAval) {
      candidatosAvales++;
    } else {
      candidatosActivos++;
    }
  });

  // ============================================================================
  // MENSAJES INFORMATIVOS
  // ============================================================================
  
  let mensajeEstado = '';
  let mostrarFiltros = true;
  
  if (hayReactivados) {
    // Hay candidatos reactivados
    mensajeEstado = `
      <div class="alert alert-info mb-3" style="background: #e3f2fd; border-left: 4px solid #2196f3;">
        <div class="d-flex align-items-center">
          <i class="fas fa-redo-alt fa-2x text-primary mr-3"></i>
          <div>
            <h6 class="mb-1"><strong>Candidatos Reactivados</strong></h6>
            <p class="mb-0">Mostrando ${candidatos.length} candidato${candidatos.length > 1 ? 's' : ''} reactivado${candidatos.length > 1 ? 's' : ''} después de la contratación.</p>
            <small class="text-muted">Los candidatos contratados están ocultos.</small>
          </div>
        </div>
      </div>
    `;
    mostrarFiltros = false;
  } else if (hayContratado) {
    // Solo hay contratados (plaza cubierta)
    mensajeEstado = `
      <div class="alert alert-success mb-3" style="background: #e8f5e9; border-left: 4px solid #4caf50;">
        <div class="d-flex align-items-center">
          <i class="fas fa-check-circle fa-2x text-success mr-3"></i>
          <div>
            <h6 class="mb-1"><strong>PLAZA CUBIERTA</strong></h6>
            <p class="mb-0">Solo se muestran los candidatos contratados.</p>
            <small class="text-muted">Los demás candidatos están ocultos porque la plaza ya fue asignada.</small>
          </div>
        </div>
      </div>
    `;
    mostrarFiltros = false;
  }

  // ============================================================================
  // CONSTRUIR HTML
  // ============================================================================
  
  let htmlCompleto = mensajeEstado;
  
  // Agregar filtros solo si corresponde
  if (mostrarFiltros && candidatos.length > 1) {
    htmlCompleto += `
      <div class="btn-group btn-group-sm w-100 mb-3">
        <button type="button" class="btn btn-primary filtro-rh-btn" data-filter="todos">
          Todos (${candidatosAMostrar.length})
        </button>
        <button type="button" class="btn btn-outline-success filtro-rh-btn" data-filter="activos">
          Activos (${candidatosActivos})
        </button>
        <button type="button" class="btn btn-outline-danger filtro-rh-btn" data-filter="descartados">
          Descartados (${candidatosDescartados})
        </button>
        <button type="button" class="btn btn-outline-warning filtro-rh-btn" data-filter="avales">
          Avales (${candidatosAvales})
        </button>
      </div>
    `;
  } else if (hayContratado) {
    htmlCompleto += `
      <div class="text-center mb-3">
        <span class="badge badge-success p-2" style="font-size: 1rem;">
          <i class="fas fa-user-check mr-2"></i>Candidatos Contratados (${candidatosContratadosCount})
        </span>
        <small class="d-block text-muted mt-1">Los filtros están deshabilitados cuando hay candidatos contratados</small>
      </div>
    `;
  }
  
  // Generar cards de candidatos
  htmlCompleto += '<div id="listaCandidatosContainer" style="max-height: calc(80vh - 200px); overflow-y: auto;">';
  
  candidatosAMostrar.forEach((candidato, index) => {
    const nombreCompleto = `${candidato.NOMBRE_CANDIDATO || ''} ${candidato.APELLIDOS_CANDIDATO || ''}`.trim();
    const esDescartado = candidato.ES_DESCARTADO === true || candidato.ACTIVO === 'N';
    const estadoActual = (candidato.ESTADO_CANDIDATO || '').toLowerCase();
    const esAval = estadoActual.includes('aprobacion') && estadoActual.includes('aval');
    const esContratado = estadoActual === 'contratado';
    const esReactivado = candidato.REACTIVADO_POST_CONTRATACION === 'Y';
    
    // DETERMINAR ESTADO DEL AVAL
    const esAprobado = candidato.APROBACION === 'Y';
    const esRechazado = candidato.APROBACION === 'N';
    const esPendiente = estadoActual === 'aprobacion de aval' && !candidato.APROBACION;
    
    // ASIGNAR CLASE CSS Y COLORES
    let claseEstado = '';
    let colorBorde = '';
    let colorFondo = '';
    let badgeClass = '';
    let iconoEstado = '';
    
    if (esContratado) {
      claseEstado = 'candidato-contratado';
      colorBorde = '#28a745';
      colorFondo = '#d4edda';
      badgeClass = 'badge-success';
      iconoEstado = 'fa-user-check';
    } else if (esDescartado) {
      claseEstado = 'candidato-descartado';
      colorBorde = '#dc3545';
      colorFondo = '#f8d7da';
      badgeClass = 'badge-danger';
      iconoEstado = 'fa-times-circle';
    } else if (esAprobado) {
      claseEstado = 'candidato-aval candidato-aprobado';
      colorBorde = '#28a745';
      colorFondo = '#d4edda';
      badgeClass = 'badge-success';
      iconoEstado = 'fa-check-circle';
    } else if (esRechazado) {
      claseEstado = 'candidato-aval candidato-rechazado';
      colorBorde = '#dc3545';
      colorFondo = '#f8d7da';
      badgeClass = 'badge-danger';
      iconoEstado = 'fa-times-circle';
    } else if (esPendiente) {
      claseEstado = 'candidato-aval candidato-pendiente';
      colorBorde = '#ffc107';
      colorFondo = '#fff3cd';
      badgeClass = 'badge-warning';
      iconoEstado = 'fa-clock';
    } else if (esAval) {
      claseEstado = 'candidato-aval';
      colorBorde = '#17a2b8';
      colorFondo = '#d1ecf1';
      badgeClass = 'badge-info';
      iconoEstado = 'fa-gavel';
    } else {
      claseEstado = 'candidato-activo';
      colorBorde = '#28a745';
      colorFondo = '#ffffff';
      badgeClass = 'badge-primary';
      iconoEstado = 'fa-user-check';
    }
    
    const textoEstado = esContratado ? 'CONTRATADO' : 
      (esAprobado ? 'APROBADO PARA CONTRATACION' : 
      esRechazado ? 'RECHAZADO' : 
      (candidato.ESTADO_CANDIDATO || 'Activo'));
    
    // Badge de reactivado
    let badgeReactivado = '';
    if (esReactivado) {
      badgeReactivado = '<span class="badge badge-warning ml-2" style="font-size: 0.75rem;"><i class="fas fa-redo mr-1"></i>Reactivado</span>';
    }
    
    htmlCompleto += `
      <div class="candidate-card mb-2 ${claseEstado}" 
           data-candidato-id="${candidato.ID_CANDIDATO}"
           data-es-contratado="${esContratado}">
        <div class="card" style="cursor: pointer; border-left: 4px solid ${colorBorde}; background-color: ${colorFondo};">
          <div class="card-body p-3">
            <h6 class="mb-1">
              <i class="fas ${iconoEstado} mr-1"></i>
              ${nombreCompleto}
              ${esContratado ? '<span class="badge badge-success ml-2">CONTRATADO</span>' : ''}
              ${badgeReactivado}
            </h6>
            <small class="text-muted d-block">
              <i class="fas fa-user mr-1"></i>Candidato ${index + 1}
            </small>
            <small class="text-muted d-block">
              <i class="fas fa-paperclip mr-1"></i>${candidato.TOTAL_ARCHIVOS || 0} archivos
            </small>
            <div class="mt-2">
              <span class="badge ${badgeClass}">
                ${textoEstado}
              </span>
            </div>
            ${esContratado ? `
              <div class="mt-2">
                <small class="text-success">
                  <i class="fas fa-info-circle mr-1"></i>
                  Este candidato ha sido contratado para cubrir la plaza
                </small>
              </div>
            ` : ''}
          </div>
        </div>
      </div>
    `;
  });
  
  htmlCompleto += '</div>';

  $('#listaCandidatos').html(htmlCompleto);

  // ============================================================================
  // EVENTOS
  // ============================================================================
  
  // EVENTOS DE FILTRO - SOLO SI SE MUESTRAN FILTROS
  if (mostrarFiltros) {
    $('.filtro-rh-btn').off('click').on('click', function() {
      const filter = $(this).data('filter');
      
      $('.filtro-rh-btn').removeClass('btn-primary btn-outline-success active btn-outline-danger btn-outline-warning')
                         .addClass('btn-outline-primary');
      
      if (filter === 'todos') {
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        $('.candidate-card').show();
      } else if (filter === 'activos') {
        $(this).removeClass('btn-outline-primary').addClass('btn-outline-success active');
        $('.candidate-card').hide();
        $('.candidato-activo').show();
      } else if (filter === 'descartados') {
        $(this).removeClass('btn-outline-primary').addClass('btn-outline-danger active');
        $('.candidate-card').hide();
        $('.candidato-descartado').show();
      } else if (filter === 'avales') {
        $(this).removeClass('btn-outline-primary').addClass('btn-outline-warning active');
        $('.candidate-card').hide();
        $('.candidato-aval').show();
      }
    });
  }

  // EVENTOS DE CLICK EN CANDIDATOS
  $('.candidate-card').off('click').on('click', function(e) {
    e.preventDefault();
    const idCandidato = $(this).data('candidato-id');
    
    if (idCandidato && window.CANDIDATOS_INDEX[idCandidato]) {
      const candidato = window.CANDIDATOS_INDEX[idCandidato];
      const nombreCompleto = `${candidato.NOMBRE_CANDIDATO || ''} ${candidato.APELLIDOS_CANDIDATO || ''}`.trim();
      
      $('.candidate-card .card').removeClass('bg-light border-primary');
      $(this).find('.card').addClass('bg-light border-primary');
      
      if (typeof obtenerInfoSolicitudYMostrarIntegrado === 'function') {
        obtenerInfoSolicitudYMostrarIntegrado(idSolicitud, idCandidato, nombreCompleto, []);
      }
    }
  });

  console.log('✅ RH - Lista configurada con', candidatos.length, 'candidatos');
}

// ✅ FUNCIÓN DE DEBUG MEJORADA
window.debugRHCompleto = function() {
    console.log('=== DEBUG RH COMPLETO ===');
    
    const $allCards = $('.candidate-card');
    const $activeCards = $('.candidato-activo');  
    const $discardedCards = $('.candidato-descartado');
    const $visibleCards = $('.candidate-card:visible');
    
    console.log('Elementos encontrados:', {
        totalCards: $allCards.length,
        activeCards: $activeCards.length,
        discardedCards: $discardedCards.length,
        visibleCards: $visibleCards.length
    });
    
    console.log('Botones de filtro:', $('#filtrosRH .filtro-rh-btn').length);
    
    // Verificar clases específicas
    $allCards.each(function(index) {
        const $card = $(this);
        const isActive = $card.hasClass('candidato-activo');
        const isDiscarded = $card.hasClass('candidato-descartado');
        const isVisible = $card.is(':visible');
        
        console.log(`Card ${index + 1}:`, {
            id: $card.data('candidato-id'),
            isActive,
            isDiscarded, 
            isVisible,
            displayCSS: $card.css('display'),
            visibilityCSS: $card.css('visibility')
        });
    });
};

// ===== FUNCIONES DE SUBIDA DE ARCHIVOS =====
function subirArchivoCandidato(idSolicitud, idCandidato, tipoArchivo = 'CV Enviado') {
  console.log('=== INICIANDO SUBIDA ARCHIVO ===');
  console.log('ID Solicitud:', idSolicitud);
  console.log('ID Candidato:', idCandidato);
  console.log('Tipo Archivo:', tipoArchivo);
  
  // ✅ CORREGIR: Resolver el ID de solicitud si viene como 0
  const idSolicitudCorregido = resolverIdSolicitud(idSolicitud);
  
  console.log('ID Solicitud Corregido:', idSolicitudCorregido);
  
  // Validar parámetros CORREGIDOS
  if (!idSolicitudCorregido || !idCandidato) {
    Swal.fire('Error', 'Faltan datos obligatorios para la subida', 'error');
    return;
  }

  // 1. Crear input file dinámico
  const inputFile = document.createElement('input');
  inputFile.type = 'file';
  inputFile.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
  inputFile.style.display = 'none';

  // 2. Manejar selección de archivo
  inputFile.onchange = function(event) {
    const archivo = event.target.files[0];
    
    if (!archivo) {
      Swal.fire('Error', 'No se seleccionó ningún archivo', 'error');
      return;
    }
    
    // Validaciones del lado cliente
    if (archivo.size > 10 * 1024 * 1024) {
      Swal.fire('Error', `Archivo muy grande (${(archivo.size / 1024 / 1024).toFixed(2)}MB). Máximo permitido: 10MB`, 'error');
      return;
    }
    
    // Validar extensión
    const extension = archivo.name.split('.').pop().toLowerCase();
    const extensionesPermitidas = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    
    if (!extensionesPermitidas.includes(extension)) {
      Swal.fire('Error', `Tipo de archivo no permitido (.${extension}). Solo se permiten: ${extensionesPermitidas.join(', ')}`, 'error');
      return;
    }

    // 3. Preparar FormData CON ID CORREGIDO
    const formData = new FormData();
    formData.append('action', 'subir_archivo_candidato');
    formData.append('id_solicitud', idSolicitudCorregido); // ✅ Usar el ID corregido
    formData.append('id_candidato', idCandidato);
    formData.append('archivo', archivo);
    formData.append('tipo_archivo', tipoArchivo);

    console.log('FormData preparado:', {
      action: 'subir_archivo_candidato',
      id_solicitud: idSolicitudCorregido,
      id_candidato: idCandidato,
      archivo: archivo.name,
      tipo_archivo: tipoArchivo
    });

    // 4. Mostrar loading
    Swal.fire({ 
      title: 'Subiendo archivo...', 
      html: `Procesando: <strong>${archivo.name}</strong><br>Tamaño: ${(archivo.size / 1024).toFixed(2)} KB`,
      allowOutsideClick: false, 
      didOpen: () => Swal.showLoading() 
    });

    // 5. Realizar petición AJAX
    $.ajax({
      url: './gestionhumana/crudsolicitudesrh.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      timeout: 30000,
      // En la función subirArchivoCandidato, dentro del success del AJAX:
      success: function(resp) {
        if (resp.success) {
          Swal.fire({
            icon: 'success',
            title: '¡Archivo subido correctamente!',
            text: resp.archivo || 'Archivo procesado exitosamente',
            timer: 1500,
            showConfirmButton: false
            }).then(() => {
              // ✅ FORZAR ACTUALIZACIÓN COMPLETA SIEMPRE
              const candidatoSeleccionado = $('.candidate-card .card.bg-light, .candidate-card .card.border-primary').closest('.candidate-card').data('candidato-id');
              
              if (candidatoSeleccionado) {
                // Limpiar cache y forzar recarga
                console.log('🔄 Forzando actualización tras subir archivo');
                
                // Esperar un poco para que la BD se actualice
                setTimeout(() => {
                  recargarExpedienteActualizado(candidatoSeleccionado, idSolicitudCorregido);
                  
                  // Actualizar también la lista de candidatos
                  if (typeof actualizarListaCandidatos === 'function') {
                    actualizarListaCandidatos(idSolicitudCorregido);
                  }
                }, 300);
              }
            });
        } else {
          Swal.fire('Error', resp.error, 'error');
        }
      },
      error: function(xhr, status, error) {
        console.error('❌ Error AJAX:', xhr.responseText);
        Swal.fire({
          icon: 'error',
          title: 'Error de conexión',
          html: `
            <p>No se pudo conectar con el servidor</p>
            <small>Error técnico: ${error}</small>
          `,
          confirmButtonColor: '#dc3545'
        });
      }
    });
  };

  // 3. Simular click para que el usuario seleccione el archivo
  document.body.appendChild(inputFile);
  inputFile.click();
  document.body.removeChild(inputFile);
}



// 2. FUNCIÓN CRÍTICA: ACTUALIZAR ARCHIVOS Y EXPEDIENTE
// ============================================================================
function actualizarArchivosYExpediente(idCandidato, idSolicitud) {
  console.log('🔄 Actualizando archivos para candidato:', idCandidato);
  
  // Mostrar indicador de carga en el expediente
  $('#expedienteCandidato').prepend(`
    <div id="loading-update" class="alert alert-info text-center mb-3">
      <i class="fas fa-sync fa-spin"></i> Actualizando expediente...
    </div>
  `);
  
  // 1. Recargar archivos del candidato
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_archivos_candidato',
    type: 'GET',
    data: { 
      id_candidato: idCandidato,
      _timestamp: Date.now() // Evitar cache
    },
    dataType: 'json',
    success: function(response) {
      console.log('📄 Archivos actualizados:', response);
      
      if (response.success) {
        // 2. Obtener info del candidato actualizada
        const candidatoCard = $(`.candidate-card[data-candidato-id="${idCandidato}"]`);
        const nombreCompleto = candidatoCard.find('.card-title').text().replace(/^\s*✓\s*/, '').trim();
        
        // 3. Obtener info de la solicitud
        let infoSolicitud = {
          id: idSolicitud,
          tienda: 'Cargando...',
          puesto: 'Cargando...',
          supervisor: 'Cargando...',
          estado: 'Cargando...',
          nombre_gerente: 'Cargando...'
        };
        
        const filaSolicitud = $(`tr[data-id="${idSolicitud}"]`);
        if (filaSolicitud.length > 0) {
          infoSolicitud = {
            id: idSolicitud,
            tienda: filaSolicitud.find('td:nth-child(2)').text().trim(),
            puesto: filaSolicitud.find('td:nth-child(3)').text().trim(),
            supervisor: filaSolicitud.find('td:nth-child(4)').text().trim(),
            estado: filaSolicitud.find('td:nth-child(9)').text().trim(),
            nombre_gerente: filaSolicitud.find('td:nth-child(5)').text().trim()
          };
        }
        
        // 4. ✅ REGENERAR COMPLETAMENTE EL EXPEDIENTE
        regenerarExpedienteCompleto(idCandidato, nombreCompleto, response.archivos, infoSolicitud);
        
        // 5. Remover indicador de carga
        setTimeout(() => {
          $('#loading-update').fadeOut(() => $('#loading-update').remove());
        }, 500);
        
      } else {
        console.error('❌ Error al actualizar archivos:', response.error);
        $('#loading-update').removeClass('alert-info').addClass('alert-warning').html(`
          <i class="fas fa-exclamation-triangle"></i> Error al actualizar: ${response.error}
        `);
      }
    },
    error: function(xhr, status, error) {
      console.error('❌ Error AJAX al actualizar archivos:', error);
      $('#loading-update').removeClass('alert-info').addClass('alert-danger').html(`
        <i class="fas fa-times"></i> Error de conexión al actualizar expediente
      `);
    }
  });
}

// 3. FUNCIÓN PARA REGENERAR EXPEDIENTE COMPLETO
// ============================================================================
function regenerarExpedienteCompleto(idCandidato, nombreCompleto, archivos, infoSolicitud) {
  console.log('🔄 Regenerando expediente completo para:', idCandidato);
  
  // Obtener datos del candidato del índice global
  const cand = (window.CANDIDATOS_INDEX && window.CANDIDATOS_INDEX[idCandidato]) || null;
  
  if (!cand) {
    console.warn('❌ Candidato no encontrado en índice global');
    $('#expedienteCandidato').html(`
      <div class="alert alert-warning text-center">
        <i class="fas fa-exclamation-triangle"></i> 
        No se pudo cargar la información del candidato
      </div>
    `);
    return;
  }
  
  // Construir HTML del expediente actualizado
  const estadoActual = cand.ESTADO_CANDIDATO || 'CV Enviado';
  const dpi = cand.DOCUMENTO_CANDIDATO || '';
  const esJefe = infoSolicitud.puesto && infoSolicitud.puesto.toUpperCase().includes('JEFE');
  
  // ✅ GENERAR CARPETAS CON ARCHIVOS ACTUALIZADOS
  //const carpetasHTML = generarCarpetasConArchivos(archivos, esJefe, idCandidato, infoSolicitud.id);
  const estadoCandidato = cand.ESTADO_CANDIDATO || 'Sin estado';
const carpetasHTML = generarCarpetasConArchivos(archivos, esJefe, idCandidato, infoSolicitud.id, estadoCandidato);
  
  const expedienteHTML = `
    <div class="expediente-header mb-4">
      <div class="row">
        <div class="col-md-8">
          <h4 class="mb-1">
            <i class="fas fa-user-tie text-primary"></i> 
            ${nombreCompleto}
          </h4>
          <div class="info-badges">
            <span class="badge badge-info">DPI: ${dpi}</span>
            <span class="badge badge-${getEstadoBadgeClass(estadoActual)}" style="font-size: 1.3em; font-weight: bold; padding: 6px 12px;">${estadoActual}</span>
            ${esJefe ? '<span class="badge badge-warning">Requiere Polígrafo</span>' : ''}
          </div>
        </div>
        <div class="col-md-4 text-right">
          <div class="solicitud-info">
            <small class="text-muted">Solicitud #${infoSolicitud.id}</small><br>
            <small class="text-muted">${infoSolicitud.puesto}</small>
          </div>
        </div>
      </div>
    </div>
    
    <div class="carpetas-expediente">
      <h5 class="mb-3">
        <i class="fas fa-folder-open text-warning"></i> Documentos del Expediente
      </h5>
      <div class="row">
        ${carpetasHTML}
      </div>
    </div>
    
    <div class="estadisticas-expediente mt-4">
      <div class="row">
        <div class="col-md-6">
          <div class="alert alert-light">
            <strong>Total de archivos:</strong> ${archivos.length}
          </div>
        </div>
        <div class="col-md-6">
          <div class="alert alert-light">
            <strong>Última actualización:</strong> ${new Date().toLocaleString()}
          </div>
        </div>
      </div>
    </div>
  `;
  
  // ✅ ACTUALIZAR EL DOM
  $('#expedienteCandidato').html(expedienteHTML);
  
  console.log('✅ Expediente regenerado exitosamente');
}

function generarCarpetasConArchivos(archivos, esJefe, idCandidato, idSolicitud, estadoCandidato) {
  console.log('📋 Estado recibido:', estadoCandidato);
  console.log('✅ Es contratado:', estadoCandidato === 'Contratado');
  
  const esContratado = estadoCandidato === 'Contratado';
  
  // Estados base en orden correcto
  const estadosBase = ['CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 'Dia de Prueba'];
  
  if (esJefe) {
    estadosBase.push('Poligrafo');
  }
  
  let carpetasHTML = '';
  
  estadosBase.forEach((estado) => {
    const archivosEstado = archivos.filter(arch => arch.ESTADO_RELACIONADO === estado);
    const tieneArchivos = archivosEstado.length > 0;
    
    let cardClass, iconClass, btnClass, btnText, btnAction;
    
    if (tieneArchivos) {
      cardClass = 'border-success';
      iconClass = 'fas fa-folder-open text-success';
      
      console.log(`🔍 Carpeta ${estado}: esContratado = ${esContratado}`);
      
      if (esContratado) {
        btnClass = 'btn-info';
        btnText = 'Ver';
        btnAction = `verArchivosCarpeta('${idCandidato}', '${estado}')`;
      } else {
        btnClass = 'btn-success';
        btnText = `Gestionar (${archivosEstado.length})`;
        btnAction = `abrirGestorArchivos('${idCandidato}', '${estado}')`;
      }
    } else {
      cardClass = 'border-primary';
      iconClass = 'fas fa-folder text-primary';
      btnClass = 'btn-primary';
      btnText = 'Subir archivo';
      btnAction = `subirArchivoCandidato('${idSolicitud}', '${idCandidato}', '${estado}')`;
    }
    
    carpetasHTML += `
      <div class="col-md-4 mb-3">
        <div class="card ${cardClass} h-100">
          <div class="card-body text-center p-3">
            <div class="mb-2">
              <i class="${iconClass} fa-2x"></i>
            </div>
            <h6 class="card-title">${estado}</h6>
            <div class="mb-2">
              ${tieneArchivos ? 
                `<span class="badge badge-success">✓ Completo</span>` : 
                `<span class="badge badge-secondary">Pendiente</span>`
              }
            </div>
            <button class="btn btn-sm ${btnClass}" onclick="${btnAction}">
              <i class="fas fa-${tieneArchivos ? 'eye' : 'upload'}"></i>
              ${btnText}
            </button>
            ${tieneArchivos && archivosEstado.length > 0 ? `
              <div class="mt-2">
                ${archivosEstado.map(arch => `
                  <small class="d-block text-success">
                    <i class="fas fa-file"></i> ${arch.NOMBRE_ARCHIVO.split('_').pop()}
                  </small>
                `).join('')}
              </div>
            ` : ''}
          </div>
        </div>
      </div>
    `;
  });
  
  return carpetasHTML;
}

// 5. FUNCIONES AUXILIARES
// ============================================================================
function getEstadoBadgeClass(estado) {
  const clases = {
    'CV Enviado': 'primary',
    'Psicometrica': 'warning',
    'Entrevista RH': 'info',
    'Entrevista Tecnica': 'secondary',
    'Dia de Prueba': 'dark',
    'Poligrafo': 'danger'
  };
  return clases[estado] || 'secondary';
}

function marcarCarpetaComoCompletada(tipoArchivo) {
  // Marcar visualmente la carpeta como completada
  $(`.card:contains("${tipoArchivo}")`).removeClass('border-primary').addClass('border-success');
  console.log(`✅ Carpeta ${tipoArchivo} marcada como completada`);
}

// ============================================================================
// 6. FUNCIÓN PARA VER ARCHIVOS DE UN ESTADO
// ============================================================================
function verArchivosEstado(idCandidato, estado) {
  console.log(`👁️ Viendo archivos de ${estado} para candidato:`, idCandidato);
  
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_archivos_candidato',
    type: 'GET',
    data: { 
      id_candidato: idCandidato,
      estado_filtro: estado
    },
    dataType: 'json',
    success: function(response) {
      if (response.success && response.archivos.length > 0) {
        let archivosHTML = '';
        response.archivos.forEach(archivo => {
          archivosHTML += `
            <div class="archivo-item mb-2 p-2 border rounded">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <i class="fas fa-file text-primary"></i>
                  <strong>${archivo.NOMBRE_ARCHIVO.split('_').pop()}</strong>
                  <br>
                  <small class="text-muted">
                    Subido: ${archivo.FECHA_SUBIDA} | 
                    Tamaño: ${(archivo.TAMAÑO_BYTES / 1024).toFixed(2)} KB
                  </small>
                </div>
                <div>
                  <a href="./gestionhumana/archivos_candidatos/${archivo.NOMBRE_ARCHIVO}" 
                     class="btn btn-sm btn-primary" 
                     target="_blank" 
                     title="Descargar archivo">
                    <i class="fas fa-download"></i>
                  </a>
                </div>
              </div>
            </div>
          `;
        });
        
        Swal.fire({
          title: `Archivos de ${estado}`,
          html: archivosHTML,
          width: '600px',
          confirmButtonText: 'Cerrar'
        });
      } else {
        Swal.fire('Información', 'No hay archivos para este estado', 'info');
      }
    },
    error: function() {
      Swal.fire('Error', 'No se pudieron cargar los archivos', 'error');
    }
  });
}

// ===== FUNCIONES DE GESTIÓN DE CANDIDATOS =====
function cargarArchivosCandidato(idCandidato, estadoFiltro = null) {
  console.log('Cargando archivos del candidato:', idCandidato);
  
  const params = {
    action: 'get_archivos_candidato',
    id_candidato: idCandidato
  };
  
  if (estadoFiltro) {
    params.estado_filtro = estadoFiltro;
  }
  
  return $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    method: 'GET',
    data: params,
    dataType: 'json'
  });
}

function actualizarListaCandidatos(idSolicitud) {
  console.log('Actualizando lista de candidatos para solicitud:', idSolicitud);
  
  // 🔥 GUARDAR EL CANDIDATO ACTUALMENTE SELECCIONADO
  const candidatoSeleccionado = $('.candidate-card .card.border-primary').closest('.candidate-card').data('candidato-id');
  console.log('Candidato actualmente seleccionado:', candidatoSeleccionado);
  
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_candidatos_solicitud_rh',
    type: 'GET',
    data: { 
      id_solicitud: idSolicitud,
      _timestamp: Date.now() // Evitar caché
    },
    dataType: 'text',
    success: function(responseText) {
      console.log('Respuesta del servidor (raw):', responseText);
      
      if (!responseText || responseText.trim() === '') {
        console.error('Respuesta vacía del servidor');
        return;
      }
      
      try {
        const response = JSON.parse(responseText);
        console.log('Respuesta parseada:', response);
        
        if (response.success) {
          // ✅ ACTUALIZAR EL ÍNDICE GLOBAL CON DATOS FRESCOS
          window.CANDIDATOS_INDEX = {};
          response.candidatos.forEach(candidato => {
            window.CANDIDATOS_INDEX[candidato.ID_CANDIDATO] = candidato;
          });
          console.log('✅ Índice global actualizado:', window.CANDIDATOS_INDEX);
          
          // ✅ RECREAR LA LISTA DE CANDIDATOS CON DATOS ACTUALIZADOS
          mostrarListaCandidatosrh(response.candidatos);
          
          // ✅ RESELECCIONAR EL CANDIDATO QUE ESTABA ACTIVO
          if (candidatoSeleccionado) {
            console.log('🔄 Reseleccionando candidato:', candidatoSeleccionado);
            
            setTimeout(() => {
              // Marcar visualmente como seleccionado
              $(`.candidate-card[data-candidato-id="${candidatoSeleccionado}"] .card`)
                .removeClass('border-left-primary')
                .addClass('border-primary bg-light');
              
              // ✅ RECARGAR EL EXPEDIENTE CON DATOS ACTUALIZADOS
              recargarExpedienteActualizado(candidatoSeleccionado, idSolicitud);
              
            }, 300);
          }
          
          console.log('✅ Lista de candidatos actualizada exitosamente');
          
        } else {
          console.error('Error en respuesta:', response.error);
          Swal.fire('Error', 'No se pudo actualizar la lista: ' + response.error, 'error');
        }
        
      } catch (parseError) {
        console.error('Error al parsear JSON:', parseError);
        console.error('Respuesta problemática:', responseText);
        Swal.fire('Error', 'Error procesando respuesta del servidor', 'error');
      }
    },
    error: function(xhr, status, error) {
      console.error('Error AJAX:', error);
      Swal.fire('Error', 'Error de conexión al actualizar candidatos', 'error');
    }
  });
}

function recargarExpedienteActualizado(idCandidato, idSolicitud) {
  console.log('🔄 Recargando expediente actualizado para candidato:', idCandidato);
  
  // Mostrar indicador de actualización
  $('#expedienteCandidato').prepend(`
    <div id="actualizando-expediente" class="alert alert-info text-center mb-3" style="animation: pulse 1.5s infinite;">
      <i class="fas fa-sync fa-spin"></i> Actualizando expediente...
    </div>
  `);
  
  // 1. CARGAR ARCHIVOS ACTUALIZADOS
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_archivos_candidato',
    type: 'GET',
    data: { 
      id_candidato: idCandidato,
      _timestamp: Date.now() // Evitar caché
    },
    dataType: 'json',
    success: function(response) {
      console.log('📄 Archivos del candidato actualizados:', response);
      
      if (response.success) {
        // 2. OBTENER INFO DEL CANDIDATO DEL ÍNDICE ACTUALIZADO
        const candidatoData = window.CANDIDATOS_INDEX[idCandidato];
        
        if (candidatoData) {
          const nombreCompleto = `${candidatoData.NOMBRE_CANDIDATO || ''} ${candidatoData.APELLIDOS_CANDIDATO || ''}`.trim();
          
          // 3. OBTENER INFO DE LA SOLICITUD
          let infoSolicitud = {
            id: idSolicitud,
            tienda: 'No disponible',
            puesto: 'No disponible',
            supervisor: 'No disponible',
            estado: 'No disponible'
          };
          
          const filaSolicitud = $(`tr[data-id="${idSolicitud}"]`);
          if (filaSolicitud.length > 0) {
            infoSolicitud = {
              id: idSolicitud,
              tienda: filaSolicitud.find('td:nth-child(2)').text().trim() || 'No disponible',
              puesto: filaSolicitud.find('td:nth-child(3)').text().trim() || 'No disponible',
              supervisor: filaSolicitud.find('td:nth-child(4)').text().trim() || 'No disponible',
              estado: filaSolicitud.find('td:nth-child(9)').text().trim() || 'No disponible'
            };
          }
          
          // 4. ✅ USAR TU FUNCIÓN EXISTENTE PARA MOSTRAR EL EXPEDIENTE
          if (typeof obtenerInfoSolicitudYMostrarIntegrado === 'function') {
            obtenerInfoSolicitudYMostrarIntegrado(idSolicitud, idCandidato, nombreCompleto, response.archivos);
          } else if (typeof mostrarExpedienteCandidato === 'function') {
            mostrarExpedienteCandidato(idCandidato, nombreCompleto, response.archivos, infoSolicitud);
          } else {
            console.warn('❌ Funciones de mostrar expediente no encontradas');
            // Fallback básico
            mostrarExpedienteBasico(idCandidato, nombreCompleto, response.archivos, infoSolicitud);
          }
          
          // 5. REMOVER INDICADOR DE CARGA
          setTimeout(() => {
            $('#actualizando-expediente').fadeOut(300, function() {
              $(this).remove();
            });
          }, 1000);
          
        } else {
          console.error('❌ No se encontró el candidato en el índice actualizado');
          $('#actualizando-expediente').removeClass('alert-info').addClass('alert-warning')
            .html('<i class="fas fa-exclamation-triangle"></i> No se pudo cargar información del candidato');
        }
        
      } else {
        console.error('❌ Error cargando archivos:', response.error);
        $('#actualizando-expediente').removeClass('alert-info').addClass('alert-danger')
          .html('<i class="fas fa-times"></i> Error cargando archivos: ' + response.error);
      }
    },
    error: function(xhr, status, error) {
      console.error('❌ Error AJAX cargando archivos:', error);
      $('#actualizando-expediente').removeClass('alert-info').addClass('alert-danger')
        .html('<i class="fas fa-times"></i> Error de conexión al cargar archivos');
    }
  });
}

// 2. AGREGAR ESTA NUEVA FUNCIÓN PARA CONFIGURAR EVENT LISTENERS
function configurarEventListenersCandidatosRH() {
  console.log('=== RH - Configurando event listeners mejorados');
  
  // Limpiar eventos anteriores
  $(document).off('click', '.candidato-clickable-rh');
  
  // Configurar event delegation con efectos mejorados
  $(document).on('click', '.candidato-clickable-rh', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const $card = $(this);
    const idCandidato = $card.data('candidato-id');
    const nombreCandidato = $card.data('nombre');
    
    console.log('=== RH - Click en candidato:', idCandidato, nombreCandidato);
    
    if (idCandidato && window.ROL_USUARIO === 'RRHH') {
      // Efecto visual de click
      $card.addClass('loading');
      
      // Llamar a la función de selección
      setTimeout(() => {
        window.seleccionarCandidatorh(idCandidato);
        $card.removeClass('loading');
      }, 100);
    }
  });
  
  // Configurar efectos hover mejorados
  $(document).on('mouseenter', '.candidato-clickable-rh', function() {
    $(this).css('transform', 'translateY(-2px)');
  });
  
  $(document).on('mouseleave', '.candidato-clickable-rh', function() {
    if (!$(this).hasClass('border-primary')) {
      $(this).css('transform', 'translateY(0)');
    }
  });
  
  console.log('=== RH - Event listeners configurados correctamente');
}

window.seleccionarCandidatorh = function (idCandidato) {
  console.log('=== RH - Seleccionando candidato:', idCandidato);
  
  if (!idCandidato) {
    console.error('ID de candidato no válido');
    return;
  }
  
  // Limpiar selección anterior
  $('.candidato-clickable-rh').removeClass('border-primary bg-light')
    .css('transform', 'translateY(0)');
  
  // Resaltar candidato seleccionado
  const $selectedCard = $(`.candidato-clickable-rh[data-candidato-id="${idCandidato}"]`);
  $selectedCard.addClass('border-primary bg-light')
    .css('transform', 'translateY(-2px)');
  
  // Scroll suave al candidato seleccionado
  const $listContainer = $('#listaCandidatos');
  const cardOffset = $selectedCard.position().top;
  const containerHeight = $listContainer.height();
  const cardHeight = $selectedCard.outerHeight();
  
  if (cardOffset > containerHeight - cardHeight) {
    $listContainer.animate({
      scrollTop: $listContainer.scrollTop() + cardOffset - containerHeight + cardHeight + 20
    }, 300);
  } else if (cardOffset < 0) {
    $listContainer.animate({
      scrollTop: $listContainer.scrollTop() + cardOffset - 20
    }, 300);
  }
  
  console.log('=== RH - Candidato resaltado, cargando expediente...');
  
  // ✅ VERIFICAR ESTADO DEL CANDIDATO ANTES DE CARGAR
  const candidato = window.CANDIDATOS_INDEX[idCandidato];
  
  if (!candidato) {
    console.error('❌ Candidato no encontrado en índice');
    cargarExpedienteCandidato(idCandidato);
    return;
  }
  
  const estadoActual = candidato.ESTADO_CANDIDATO;
  console.log('📊 Estado del candidato:', estadoActual);
  
  // Mostrar loading
  $('#expedienteCandidato').html(`
    <div class="text-center py-5" style="margin-top: 80px;">
      <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
        <span class="sr-only">Cargando...</span>
      </div>
      <h4 class="text-primary mt-4">
        <i class="fas fa-sync fa-spin mr-2"></i>Cargando expediente...
      </h4>
      <p class="text-muted mt-3">
        Obteniendo información del candidato
      </p>
      <div class="progress mx-auto mt-4" style="width: 60%; height: 8px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" 
             style="width: 100%">
        </div>
      </div>
    </div>
  `);
  
  // ✅ DECISIÓN: Si es "Aprobacion de Aval Enviado" → Mostrar resultado
  setTimeout(() => {
    if (estadoActual === 'Aprobacion de Aval Enviado') {
      console.log('✅ RH - Candidato procesado → Mostrando resultado del aval');
      mostrarResultadoAvalProcesadoRH(candidato);
    } else {
      console.log('📄 RH - Mostrando expediente normal');
      cargarExpedienteCandidato(idCandidato);
    }
  }, 300);
}

function cargarExpedienteCandidato(idCandidato) {
  if (!idCandidato) {
    console.error('ID de candidato no válido');
    return;
  }
  
  console.log('Recargando expediente para candidato:', idCandidato);
  
  // Mostrar loading en el área del expediente
  $('#expedienteCandidato').html(`
    <div class="text-center py-4">
      <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
      <p class="mt-2">Actualizando expediente...</p>
    </div>
  `);
  
  // Recargar archivos del candidato
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=get_archivos_candidato',
    type: 'GET',
    data: { id_candidato: idCandidato },
    dataType: 'json',
    success: function(response) {
      console.log('Archivos actualizados:', response);
      
      if (response.success) {
        // Obtener información del candidato desde el modal
        const candidatoCard = $(`.candidate-card[data-candidato-id="${idCandidato}"]`);
        const nombreCompleto = candidatoCard.find('.card-title').text()
          .replace(/^\s*✓\s*/, '').trim();
        const idSolicitud = $('#modalExpedientes').data('id-solicitud');
        
        // Actualizar vista del expediente
        if (typeof obtenerInfoSolicitudYMostrarIntegrado === 'function') {
          obtenerInfoSolicitudYMostrarIntegrado(
            idSolicitud, 
            idCandidato, 
            nombreCompleto, 
            response.archivos
          );
        } else if (typeof mostrarExpedienteCandidato === 'function') {
          mostrarExpedienteCandidato(
            idCandidato, 
            nombreCompleto, 
            response.archivos
          );
        } else {
          console.warn('Funciones de mostrar expediente no encontradas');
          // Fallback básico
          $('#expedienteCandidato').html(`
            <div class="alert alert-success">
              <i class="fas fa-check"></i> Archivos actualizados correctamente
              <p class="mt-2">Total de archivos: ${response.archivos?.length || 0}</p>
            </div>
          `);
        }
      } else {
        $('#expedienteCandidato').html(`
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            Error cargando archivos: ${response.error}
          </div>
        `);
      }
    },
    error: function(xhr, status, error) {
      console.error('Error recargando expediente:', error);
      $('#expedienteCandidato').html(`
        <div class="alert alert-danger">
          <i class="fas fa-times"></i> Error de conexión al actualizar expediente
        </div>
      `);
    }
  });
}

// ===== FUNCIONES DE MODALES =====
function mostrarModalExpedientesrh(idSolicitud, candidatos, filtroActual = 'todos') {
  console.log('🔧 Abriendo modal con filtro:', filtroActual);
  
  // Validar ID al inicio
  const idValidado = parseInt(idSolicitud, 10);
  
  if (!idValidado || isNaN(idValidado)) {
    Swal.fire('Error', 'ID de solicitud inválido para el modal', 'error');
    return;
  }
  
  console.log('Abriendo modal para solicitud:', idValidado);
  
  // Almacenar el ID validado en el modal
  $('#modalExpedientes').data('id-solicitud', idValidado);
  
  // Limpiar contenido previo
  $('#listaCandidatos').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
  $('#expedienteCandidato').html(`
    <div class="text-center py-5" style="margin-top: 100px;">
      <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;">
        <i class="fas fa-user-circle"></i>
      </div>
      <h5 class="text-muted">Selecciona un candidato</h5>
      <p class="text-muted">Haz clic en un candidato de la lista para ver su expediente completo</p>
    </div>
  `);
  
  // Mostrar modal
  try {
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      const modal = new bootstrap.Modal(document.getElementById('modalExpedientes'));
      modal.show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
      $('#modalExpedientes').modal('show');
    } else {
      console.error('Bootstrap Modal no está disponible');
      Swal.fire('Error', 'No se pudo abrir el modal.', 'error');
      return;
    }
  } catch (error) {
    console.error('Error abriendo modal:', error);
    Swal.fire('Error', 'Error abriendo el modal: ' + error.message, 'error');
    return;
  }
  
  // ✅ PROCESAR CANDIDATOS CON FILTROS
  if (candidatos && candidatos.length > 0) {
    // ✅ USAR LA NUEVA FUNCIÓN CON TODOS LOS PARÁMETROS
    if (typeof mostrarListaCandidatosrh === 'function') {
      mostrarListaCandidatosrh(candidatos, idValidado, filtroActual);
    } else {
      // ✅ FALLBACK SI LA FUNCIÓN NO EXISTE AÚN
      console.warn('mostrarListaCandidatosrh con filtros no encontrada, usando función básica');
      mostrarListaCandidatosrh(candidatos);
    }
  } else {
    $('#listaCandidatos').html(`
      <div class="text-center py-4">
        <i class="fas fa-users" style="font-size: 3rem; color: #dee2e6;"></i>
        <h6 class="mt-3 text-muted">No hay candidatos</h6>
        <p class="text-muted">No se encontraron candidatos para esta solicitud</p>
      </div>
    `);
  }
}

console.log('✅ Modificaciones RH aplicadas correctamente');

// ===== FUNCIÓN PARA MOSTRAR EXPEDIENTE =====
function obtenerInfoSolicitudYMostrarIntegrado(idSolicitud, idCandidato, nombreCompleto, archivos) {
  console.log('🔍 RH - Obteniendo info integrada:', {idSolicitud, idCandidato, nombreCompleto});
  
  // ✅ PRIMERO: Verificar si el candidato tiene aval procesado
  const candidato = window.CANDIDATOS_INDEX[idCandidato];
  
  if (candidato && candidato.ESTADO_CANDIDATO === 'Aprobacion de Aval Enviado') {
    console.log('✅ RH - Candidato con aval procesado detectado');
    
    // Mostrar loading
    $('#expedienteCandidato').html(`
      <div class="text-center py-5">
        <div class="spinner-border text-success" style="width: 3rem; height: 3rem;"></div>
        <h5 class="mt-3 text-success">Cargando resultado del aval...</h5>
        <p class="text-muted">Este candidato ya fue procesado por el gerente</p>
      </div>
    `);
    
    // Mostrar resultado del aval
    setTimeout(() => {
      mostrarResultadoAvalProcesadoRH(candidato);
    }, 300);
    
    return; // ✅ SALIR AQUÍ - No continuar con expediente normal
  }
  
  // Si NO tiene aval procesado, continuar normal
  console.log('📄 RH - Candidato sin aval procesado, mostrando expediente normal');
  
  // Obtener información de la solicitud desde la tabla principal
  let infoSolicitud = {
    id: idSolicitud,
    tienda: 'Cargando...',
    puesto: 'Cargando...',
    supervisor: 'Cargando...',
    estado: 'Cargando...'
  };
  
  // Buscar en la tabla principal si está disponible
  const filaSolicitud = $(`tr[data-id="${idSolicitud}"]`);
  if (filaSolicitud.length > 0) {
    infoSolicitud = {
      id: idSolicitud,
      tienda: filaSolicitud.find('td:nth-child(2)').text().trim() || 'No disponible',
      puesto: filaSolicitud.find('td:nth-child(3)').text().trim() || 'No disponible',
      supervisor: filaSolicitud.find('td:nth-child(4)').text().trim() || 'No disponible',
      estado: filaSolicitud.find('td:nth-child(9)').text().trim() || 'No disponible'
    };
  }
  
  // Asegurar que ROL_USUARIO esté definido
  const rolUsuario = window.ROL_USUARIO || 'RRHH';
  console.log('Rol de usuario detectado:', rolUsuario);
  
  // Mostrar expediente usando función existente
  if (typeof mostrarExpedienteCandidato === 'function') {
    mostrarExpedienteCandidato(idCandidato, nombreCompleto, archivos, infoSolicitud);
  } else {
    console.warn('Función mostrarExpedienteCandidato no encontrada, usando vista básica');
    mostrarExpedienteBasico(idCandidato, nombreCompleto, archivos, infoSolicitud);
  }
}


function mostrarExpedienteBasico(idCandidato, nombreCompleto, archivos, infoSolicitud) {
  const candidato = window.CANDIDATOS_INDEX[idCandidato] || {};
  const nombreReal = `${(candidato.NOMBRE_CANDIDATO||'').trim()} ${(candidato.APELLIDOS_CANDIDATO||'').trim()}`.trim() || nombreCompleto;
  
  $('#expedienteCandidato').html(`
    <div class="container-fluid">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="fas fa-user mr-2"></i>Expediente de ${nombreReal}
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h6>Información del Candidato</h6>
              <p><strong>Nombre:</strong> ${nombreReal}</p>
              <p><strong>DPI:</strong> ${candidato.DOCUMENTO_CANDIDATO || 'No disponible'}</p>
              <p><strong>Estado:</strong> <span class="badge badge-${candidato.ACTIVO === 'Y' ? 'success' : 'danger'}">${candidato.ESTADO_CANDIDATO || 'Sin estado'}</span></p>
            </div>
            <div class="col-md-6">
              <h6>Información de la Solicitud</h6>
              <p><strong>Tienda:</strong> ${infoSolicitud.tienda}</p>
              <p><strong>Puesto:</strong> ${infoSolicitud.puesto}</p>
              <p><strong>Supervisor:</strong> ${infoSolicitud.supervisor}</p>
            </div>
          </div>
          
          <hr>
          
          <div class="row">
            <div class="col-12">
              <h6>Acciones Disponibles</h6>
              <button class="btn btn-primary mr-2" onclick="subirArchivoCandidato(${infoSolicitud.id}, ${idCandidato}, 'CV Enviado')">
                <i class="fas fa-upload mr-1"></i>Subir CV
              </button>
              <button class="btn btn-info mr-2" onclick="cargarArchivosCandidato(${idCandidato})">
                <i class="fas fa-folder mr-1"></i>Ver Archivos
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  `);
}

// ===== FUNCIÓN MEJORADA PARA MOSTRAR EXPEDIENTE COMPLETO =====

function mostrarExpedienteCandidato(idCandidato, _nombreCandidato, archivos, infoSolicitud) {
  console.log('🎯 Mostrando expediente RH para candidato:', idCandidato);

  const cand = (window.CANDIDATOS_INDEX && window.CANDIDATOS_INDEX[idCandidato]) || null;

  if (!cand) {
    $('#expedienteCandidato').html(`
      <div class="alert alert-warning">No se encontró información del candidato seleccionado.</div>
    `);
    return;
  }

  const nombreCompleto = `${(cand.NOMBRE_CANDIDATO||'').trim()} ${(cand.APELLIDOS_CANDIDATO||'').trim()}`.trim();
  const esDescartado = cand.ESTADO_CANDIDATO?.toLowerCase() === 'descartado' || (cand.ACTIVO === 'N');

  // ✅ MOSTRAR LOADING
  $('#expedienteCandidato').html(`
    <div class="text-center py-5">
      <div class="spinner-border ${esDescartado ? 'text-danger' : 'text-primary'}" role="status" style="width: 3rem; height: 3rem;">
        <span class="sr-only">Cargando...</span>
      </div>
      <h5 class="text-muted mt-3">Cargando expediente...</h5>
      <p class="text-muted">Obteniendo información de ${nombreCompleto}</p>
      ${esDescartado ? '<p class="text-danger"><i class="fas fa-user-times"></i> Candidato descartado</p>' : ''}
    </div>
  `);

  setTimeout(() => {
    const dpi = (cand.DOCUMENTO_CANDIDATO || '').toString().trim();
    const estadoActual = (cand.ESTADO_CANDIDATO || '').toString().trim();
    const motivoDesc = (cand.MOTIVO_DESCARTE || '').toString().trim();

    // ✅ SI ES DESCARTADO - LÓGICA ESPECIAL
    if (esDescartado) {
      console.log('🚫 Candidato descartado - Obteniendo archivos...');
      
      $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php',
        type: 'GET',
        data: {
          action: 'get_archivos_candidato',
          id_candidato: idCandidato
        },
        dataType: 'json',
        success: function(archivosResponse) {
          const archivosDelCandidato = archivosResponse.success ? archivosResponse.archivos : [];
          
          $.ajax({
            url: './gestionhumana/crudsolicitudesrh.php',
            type: 'GET',
            data: {
              action: 'get_permisos_subida_candidato_rh',
              id_candidato: idCandidato,
              incluir_motivo_descarte: true
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                mostrarExpedienteDescartadoRHConArchivos(idCandidato, nombreCompleto, response, archivosDelCandidato, infoSolicitud);
              } else {
                mostrarExpedienteDescartadoBasicoRHConArchivos(idCandidato, nombreCompleto, motivoDesc, archivosDelCandidato, infoSolicitud);
              }
            },
            error: function() {
              mostrarExpedienteDescartadoBasicoRHConArchivos(idCandidato, nombreCompleto, motivoDesc, archivosDelCandidato, infoSolicitud);
            }
          });
        }
      });
      return;
    }

    // ✅ PARA CANDIDATOS ACTIVOS - LÓGICA ORIGINAL CON CORRECCIÓN
    console.log('✅ Candidato activo - Usando lógica original con mensajes supervisión');
    
    // Usar tu función original obtenerCarpetasDinamicas
    obtenerCarpetasDinamicas(idCandidato, infoSolicitud, archivos, (carpetasProgresivas) => {
      console.log('📁 Carpetas recibidas del servidor:', carpetasProgresivas);
      
      const estadoParaMostrar = (carpetasProgresivas && carpetasProgresivas.estadoActual)
        ? String(carpetasProgresivas.estadoActual).trim()
        : estadoActual;

      let carpetasRender = Array.isArray(carpetasProgresivas.carpetas)
        ? carpetasProgresivas.carpetas
        : [];

      // ✅ PROCESAR CARPETAS CON LÓGICA CORRECTA PARA RH
      const carpetasCorregidas = carpetasRender.map(carpeta => {
        // Para Entrevista Técnica y Día de Prueba, RH solo puede VER
        if (carpeta.nombre === 'Entrevista Tecnica' || carpeta.nombre === 'Dia de Prueba') {
          return {
            ...carpeta,
            disponible: false, // RH no puede subir
            puede_subir_rh: false, // Flag específico para RH
            motivo_bloqueo: carpeta.motivo_bloqueo || `El supervisor ${infoSolicitud.supervisor || 'supervisor'} o el gerente ${infoSolicitud.nombre_gerente} aún no han subido los archivos correspondientes`
          };
        }
        
        // Para otros estados, mantener lógica original
        return carpeta;
      });

      // Generar HTML del expediente
      const htmlExpediente = `
        <div class="card">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0">
              <i class="fas fa-user mr-2"></i>Expediente de ${nombreCompleto}
              <span class="badge badge-light ml-2" style="font-size: 1.4em; font-weight: bold; padding: 8px 15px;">${estadoParaMostrar}</span>
            </h5>
          </div>
          <div class="card-body">
            <!-- Información del candidato -->
            <div class="row mb-4">
              <div class="col-md-6">
                <h6>Información Personal</h6>
                <p><strong>Nombre:</strong> ${nombreCompleto}</p>
                <p><strong>DPI:</strong> ${dpi || 'No proporcionado'}</p>
                <p><strong>Estado:</strong> <span class="badge badge-success" style="font-size: 1.3em; font-weight: bold; padding: 6px 12px;">${estadoParaMostrar}</span></p>
              </div>
              <div class="col-md-6">
                <h6>Información de la Solicitud</h6>
                <p><strong>Tienda:</strong> ${infoSolicitud.tienda || 'No especificada'}</p>
                <p><strong>Puesto:</strong> ${infoSolicitud.puesto || 'No especificado'}</p>
                <p><strong>Supervisor:</strong> ${infoSolicitud.supervisor || 'No asignado'}</p>
              </div>
            </div>

            <!-- Descartar Candidatos desde ENTREVISTA TÉCNICA en adelante -->
            ${(() => {
              // ✅ ESTADOS DONDE RH PUEDE DESCARTAR (desde Entrevista Técnica)
              const estadosPermitidosDescartar = [
                'Entrevista RH',
                'Entrevista Rh',
                'Entrevista Tecnica',
                'Entrevista Técnica', 
                'Dia de Prueba',
                'Día de Prueba',
                'Poligrafo',
                'Polígrafo'
              ];
              
              // Verificar si el estado actual permite descartar
              const puedeDescartar = estadosPermitidosDescartar.some(estado => 
                estadoParaMostrar.toLowerCase().includes(estado.toLowerCase())
              );
              
              console.log('🔍 Verificando descarte RH - Estado:', estadoParaMostrar, 'Puede descartar:', puedeDescartar, 'Es descartado:', esDescartado);
              
              if (window.ROL_USUARIO === 'RRHH' && puedeDescartar && !esDescartado) {
                return `
                  <div class="row mb-4">
                    <div class="col-12">
                      <div class="alert alert-warning border-warning">
                        <h6 class="mb-2">
                          <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>
                          <strong>Acciones de Recursos Humanos</strong>
                        </h6>
                        <p class="mb-3">
                          El candidato se encuentra en <strong class="text-primary">${estadoParaMostrar}</strong>. 
                          Como parte de Recursos Humanos, puedes descartarlo si no cumple con los requisitos.
                        </p>
                        <button type="button" 
                                class="btn btn-danger btn-lg" 
                                onclick="descartarCandidatoRH(${idCandidato}, '${nombreCompleto}')">
                          <i class="fas fa-user-times mr-2"></i>Descartar Candidato
                        </button>
                      </div>
                    </div>
                  </div>
                `;
              }
              return '';
            })()}

            <!-- Estados del proceso con lógica corregida -->
            <div class="card">
              <div class="card-header">
                <h6 class="mb-0">
                  <i class="fas fa-folder-open mr-1"></i>Estados del Proceso de Selección
                </h6>
              </div>
              <div class="card-body">
                <div class="row">
                  ${carpetasCorregidas.map(carpeta => {
                    let iconoCarpeta, colorCarpeta, estadoCarpeta, accionBotones;
                    
                    if (carpeta.completado) {
  iconoCarpeta = 'fa-folder-open';
  colorCarpeta = 'success';
  estadoCarpeta = 'Completado';
  
  // Verificar si está contratado
  const esContratado = estadoParaMostrar === 'Contratado';
  
  if (esContratado) {
    accionBotones = `
      <button class="btn btn-info btn-sm" onclick="verArchivosCarpeta(${idCandidato}, '${carpeta.nombre}')">
        <i class="fas fa-eye"></i> Ver
      </button>
    `;
  } else {
    accionBotones = `
      <button class="btn btn-success btn-sm" onclick="abrirGestorArchivos(${idCandidato}, '${carpeta.nombre}')">
        <i class="fas fa-cogs"></i> Gestionar Archivos
      </button>
    `;
  }
                    } else if (carpeta.disponible && carpeta.puede_subir_rh !== false) {
                      // Solo disponible para subir si RH puede subir en este estado
                      iconoCarpeta = 'fa-folder-plus';
                      colorCarpeta = 'primary';
                      estadoCarpeta = 'Disponible';
                      accionBotones = `
                        <button class="btn btn-primary btn-sm" onclick="subirArchivoCandidato(${window.solicitudIdTemporal || 0}, ${idCandidato}, '${carpeta.nombre}')">
                          <i class="fas fa-upload"></i> Subir
                        </button>
                      `;
                    } else {
                      // Bloqueado o manejado por supervisión
                      iconoCarpeta = 'fa-folder';
                      
                      // Color específico para estados de supervisión
                      if (carpeta.nombre === 'Entrevista Tecnica' || carpeta.nombre === 'Dia de Prueba') {
                        colorCarpeta = 'warning'; // Color diferente para indicar que es de supervisión
                        estadoCarpeta = 'Supervisión';
                      } else {
                        colorCarpeta = 'secondary';
                        estadoCarpeta = 'Bloqueado';
                      }
                      
                      accionBotones = `
                        <small class="text-muted">${carpeta.motivo_bloqueo || 'Debe avanzar el estado del candidato primero'}</small>
                      `;
                    }
                    
                    return `
                      <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-${colorCarpeta} h-100">
                          <div class="card-body text-center d-flex flex-column">
                            <i class="fas ${iconoCarpeta} fa-2x text-${colorCarpeta} mb-2"></i>
                            <h6 class="card-title">${carpeta.nombre}</h6>
                            <span class="badge badge-${colorCarpeta}">${estadoCarpeta}</span>
                            <div class="mt-auto">
                              ${accionBotones}
                            </div>
                          </div>
                        </div>
                      </div>
                    `;
                  }).join('')}
                </div>
              </div>
            </div>
            
            <!-- Acciones de RH -->
                ${(() => {
                  const esContratado = estadoParaMostrar === 'Contratado';
                  
                  if (esContratado) {
                    // Si está contratado, solo mostrar el badge, sin botones
                    return `
                      <div class="mt-4 pt-3 border-top">
                        <div class="alert alert-success text-center mb-0">
                          <i class="fas fa-check-circle fa-2x mb-2"></i>
                          <h5 class="mb-0"><strong>CANDIDATO CONTRATADO</strong></h5>
                          <p class="mb-0 mt-2">La plaza ha sido cubierta exitosamente</p>
                        </div>
                      </div>
                    `;
                  } else {
                    // Mostrar botones normalmente si NO está contratado
                    return `
                      <div class="mt-4 pt-3 border-top">
                        <div class="row">
                          <div class="col-md-4">
                            <button class="btn btn-warning btn-block" onclick="cambiarEstadoCandidato(${idCandidato}, '${nombreCompleto}')">
                              <i class="fas fa-exchange-alt mr-2"></i>Cambiar Estado
                            </button>
                          </div>
                          <div class="col-md-4">
                            <button class="btn btn-info btn-block" onclick="verHistorialCandidato(${idCandidato}, '${nombreCompleto}')">
                              <i class="fas fa-history mr-2"></i>Ver Historial
                            </button>
                          </div>
                          <div class="col-md-4">
                            ${(() => {
                              const esAprobado = cand.APROBACION === 'Y';
                              const esRRHH = window.ROL_USUARIO === 'RRHH';
                              
                              if (esAprobado && esRRHH) {
                                return `
                                  <button class="btn btn-success btn-block" onclick="marcarCandidatoContratado(${idCandidato}, '${nombreCompleto}')">
                                    <i class="fas fa-check-circle mr-2"></i>Marcar como Contratado
                                  </button>
                                `;
                              }
                              return '';
                            })()}
                          </div>
                        </div>
                      </div>
                    `;
                  }
                })()}
                </div>
              </div>
            </div>
          </div>
        </div>
        <style>
    /* Candidatos descartados */
    .candidato-descartado .card {
        border-left: 4px solid #dc3545 !important;
        background: linear-gradient(135deg, #fff5f5, #ffe6e6) !important;
    }
    .candidato-descartado .card:hover {
        background: linear-gradient(135deg, #ffe6e6, #ffd6d6) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
    }
    
    /* Candidatos aprobados */
    .candidato-aprobado .card {
        border-left: 4px solid #28a745 !important;
        background: linear-gradient(135deg, #f0fff4, #d4edda) !important;
    }
    .candidato-aprobado .card:hover {
        background: linear-gradient(135deg, #d4edda, #c3e6cb) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    /* Candidatos rechazados */
    .candidato-rechazado .card {
        border-left: 4px solid #dc3545 !important;
        background: linear-gradient(135deg, #fff5f5, #f8d7da) !important;
    }
    .candidato-rechazado .card:hover {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }
    
    /* Candidatos pendientes de aval */
    .candidato-pendiente-aval .card {
        border-left: 4px solid #ffc107 !important;
        background: linear-gradient(135deg, #fffbf0, #fff3cd) !important;
    }
    .candidato-pendiente-aval .card:hover {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }
    
    /* Candidatos aval genérico */
    .candidato-aval .card {
        border-left: 4px solid #ffc107 !important;
        background: linear-gradient(135deg, #fffbf0, #fff3cd) !important;
    }
    .candidato-aval .card:hover {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }
    
    /* Candidatos activos */
    .candidato-activo .card:hover {
        background: linear-gradient(135deg, #f8f9ff, #e8ecff);
        transform: translateY(-2px);
    }
    
    /* Botones de filtro */
    .btn-filter-active {
        background-color: #007bff !important;
        color: white !important;
        border-color: #007bff !important;
    }
    
    /* Ocultar candidatos filtrados */
    .candidato-aprobado.hidden,
    .candidato-rechazado.hidden,
    .candidato-pendiente-aval.hidden,
    .candidato-aval.hidden {
        display: none !important;
    }
</style>
      `;
      
      $('#expedienteCandidato').html(htmlExpediente);
      console.log('✅ Expediente RH mostrado con lógica de supervisión corregida');
    });
  }, 500);
}

// ===================================================================================
// FUNCIÓN PARA MARCAR CANDIDATO COMO CONTRATADO
// ===================================================================================
window.marcarCandidatoContratado = function(idCandidato, nombreCandidato) {
  console.log('Marcando candidato como contratado:', idCandidato, nombreCandidato);
  
  Swal.fire({
    title: '<i class="fas fa-user-check"></i> ¿Marcar como Contratado?',
    html: `
      <div class="text-left">
        <p>Estás a punto de marcar a <strong>${nombreCandidato}</strong> como <strong>CONTRATADO</strong>.</p>
        <hr>
        <p><strong>Esto hará lo siguiente:</strong></p>
        <ul style="list-style: none; padding-left: 0;">
          <li><i class="fas fa-check-circle text-success"></i> El candidato pasará al estado "Contratado"</li>
          <li><i class="fas fa-check-circle text-success"></i> La solicitud cambiará a "Plaza Cubierta"</li>
          <li><i class="fas fa-exclamation-triangle text-warning"></i> Esta acción indica que el proceso de selección ha finalizado</li>
        </ul>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fas fa-check-circle mr-1"></i> Sí, marcar como Contratado',
    cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar',
    customClass: {
      popup: 'swal-wide'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar loading
      Swal.fire({
        title: 'Procesando...',
        text: 'Marcando candidato como contratado',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
      });
      
      // Llamar al backend
      $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'marcar_candidato_contratado',
          id_candidato: idCandidato
        },
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: '<i class="fas fa-user-check text-success"></i> ¡Candidato Contratado!',
              html: `
                <p><strong>${response.candidato}</strong> ha sido marcado como <strong>CONTRATADO</strong>.</p>
                <p><i class="fas fa-building text-success"></i> La plaza ha sido cubierta exitosamente.</p>
              `,
              timer: 3000,
              showConfirmButton: false
            }).then(() => {
              // Recargar la página para mostrar los cambios
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: '<i class="fas fa-exclamation-circle text-danger"></i> Error',
              text: response.error || 'No se pudo marcar al candidato como contratado',
              confirmButtonText: 'Entendido'
            });
          }
        },
        error: function(xhr, status, error) {
          Swal.fire({
            icon: 'error',
            title: '<i class="fas fa-exclamation-triangle text-danger"></i> Error de conexión',
            text: 'No se pudo conectar con el servidor: ' + error,
            confirmButtonText: 'Entendido'
          });
        }
      });
    }
  });
};

// funciones para poder descartar al candidato cuando ya este en estado de entrevista rh
// FUNCIÓN PARA DESCARTAR CANDIDATO DESDE RH
window.descartarCandidatoRH = function(idCandidato, nombreCandidato) {
  console.log('Descartando candidato desde RH:', idCandidato, nombreCandidato);
  
  // Mostrar loading mientras carga la información
  Swal.fire({
    title: 'Cargando información...',
    text: 'Obteniendo datos del candidato',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });
  
  // Obtener información completa del candidato desde RH
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'GET',
    data: {
      action: 'get_permisos_subida_candidato_rh',
      id_candidato: idCandidato,
      incluir_motivo_descarte: true
    },
    dataType: 'json',
    success: function(response) {
      Swal.close();
      
      if (response.success) {
        // Validar que esté en Entrevista Técnica
        const estadoActual = response.estado_candidato || '';
        // ESTADOS PERMITIDOS PARA DESCARTAR (desde Entrevista Técnica en adelante)
        const estadosPermitidos = [
          'Entrevista RH',
          'Entrevista Rh',
          'Entrevista Tecnica',
          'Entrevista Técnica', 
          'Dia de Prueba',
          'Día de Prueba',
          'Poligrafo',
          'Polígrafo'
        ];

        if (!estadosPermitidos.includes(estadoActual)) {
          Swal.fire({
            icon: 'warning',
            title: 'Estado no válido',
            text: `Solo se puede descartar candidatos desde "Entrevista RH" en adelante. Estado actual: ${estadoActual}`,
            confirmButtonText: 'Entendido'
          });
          return;
        }
        
        mostrarModalDescarteCompletoRH(idCandidato, nombreCandidato, response);
      } else {
        Swal.fire('Error', 'No se pudo cargar la información del candidato', 'error');
      }
    },
    error: function() {
      Swal.close();
      Swal.fire('Error', 'Error de conexión al cargar información', 'error');
    }
  });
};

// 2. FUNCIÓN PARA MOSTRAR EL MODAL COMPLETO (IGUAL A SUPERVISIÓN PERO PARA RH)
window.mostrarModalDescarteCompletoRH = function(idCandidato, nombreCandidato, datosCompletos) {
  const carpetas = datosCompletos.carpetas || [];
  const estadoActual = datosCompletos.estado_candidato || 'No definido';
  const puestoSolicitado = datosCompletos.puesto_solicitado || 'No definido';
  
  // Obtener información de la solicitud desde el expediente actual
  const filaSolicitud = $(`tr[data-id]`).first();
  const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(2)').text().trim() : 'No disponible';
  const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(4)').text().trim() : 'No disponible';
  
  // Obtener nombre real del candidato
  const candidatoCard = $(`.candidate-card[data-candidato-id="${idCandidato}"]`);
  let nombreReal = nombreCandidato;
  
  if (candidatoCard.length > 0) {
    const nombreEnCard = candidatoCard.find('h6').text().trim();
    if (nombreEnCard && nombreEnCard !== '') {
      nombreReal = nombreEnCard.replace('✓', '').trim();
    }
  }
  
  if (!nombreReal || nombreReal === 'undefined') {
    nombreReal = 'Candidato ID: ' + idCandidato;
  }
  
  // Estados alcanzados (igual que supervisión)
  let estadosHtml = '<div class="row">';
  carpetas.forEach(carpeta => {
    const iconClass = carpeta.ya_tiene_archivos ? 'fas fa-check-circle text-success' : 'far fa-circle text-muted';
    const cardClass = carpeta.ya_tiene_archivos ? 'border-success' : 'border-light';
    
    estadosHtml += `
      <div class="col-md-4 mb-3">
        <div class="card ${cardClass}">
          <div class="card-body text-center py-3">
            <i class="${iconClass} fa-2x mb-2"></i>
            <h6 class="card-title">${carpeta.nombre_estado}</h6>
            <small class="text-muted">
              ${carpeta.ya_tiene_archivos ? 'Completado' : 'Pendiente'}
            </small>
          </div>
        </div>
      </div>
    `;
  });
  estadosHtml += '</div>';

  // Modal HTML (igual a supervisión pero cambiado a RH)
  const modalHtml = `
    <div class="modal fade" id="modalDescartarRH${idCandidato}" tabindex="-1" data-backdrop="static">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">
              <i class="fas fa-user-times mr-2"></i>Descartar Candidato - Recursos Humanos
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Información del candidato y solicitud -->
            <div class="row mb-4">
              <div class="col-md-6">
                <div class="card border-primary">
                  <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                      <i class="fas fa-user mr-2"></i>Información del Candidato
                    </h6>
                  </div>
                  <div class="card-body">
                    <p><strong>Nombre:</strong> ${nombreReal}</p>
                    <p><strong>ID:</strong> ${idCandidato}</p>
                    <p><strong>Estado actual:</strong> 
                      <span class="badge badge-primary">${estadoActual}</span>
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="card border-info">
                  <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                      <i class="fas fa-building mr-2"></i>Información de la Solicitud
                    </h6>
                  </div>
                  <div class="card-body">
                    <p><strong>Tienda:</strong> ${tiendaInfo}</p>
                    <p><strong>Puesto:</strong> ${puestoSolicitado}</p>
                    <p><strong>Supervisor:</strong> ${supervisorInfo}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Estados alcanzados -->
            <div class="card mb-4">
              <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                  <i class="fas fa-clipboard-list mr-2"></i>Estados del Candidato
                </h6>
              </div>
              <div class="card-body">
                ${estadosHtml}
              </div>
            </div>
            
            <!-- Advertencia -->
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle mr-2"></i>
              <strong>¡ATENCIÓN!</strong> Esta acción descartará definitivamente al candidato. No podrá ser revertida.
            </div>
            
            <!-- Campo de motivo -->
            <div class="form-group">
              <label for="motivoDescarteRH${idCandidato}" class="font-weight-bold text-danger">
                Motivo del descarte <span class="text-danger">*</span>:
              </label>
                <textarea 
                    id="motivoDescarteRH${idCandidato}" 
                    class="form-control" 
                    rows="4" 
                    placeholder="Ingrese el motivo por el cual Recursos Humanos está descartando este candidato..."
                    maxlength="500"
                    oninput="updateCharCountRH${idCandidato}()"
                ></textarea>
                <div class="d-flex justify-content-between">
                    <small class="form-text text-muted">
                        Máximo 500 caracteres. Este campo es obligatorio.
                    </small>
                    <small class="text-muted">
                        <span id="charCountRH${idCandidato}">0</span>/500 caracteres
                    </small>
                </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-danger" id="btnConfirmarDescarteRH${idCandidato}">
              <i class="fas fa-user-times mr-2"></i>Confirmar Descarte
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
  
  // Agregar modal al DOM
  $('body').append(modalHtml);
  
  // Mostrar modal
if (typeof $.fn.modal === 'function') {
  $(`#modalDescartarRH${idCandidato}`).modal('show');
} else {
  // Fallback a SweetAlert si Bootstrap no está disponible
  console.error('❌ Bootstrap modal no disponible, usando SweetAlert');
  Swal.fire({
    title: '<i class="fas fa-user-times text-danger"></i> Descartar Candidato',
    html: `
      <div style="text-align: left;">
        <div class="alert alert-info">
          <strong><i class="fas fa-user mr-2"></i>${nombreReal}</strong><br>
          <small>Estado actual: <span class="badge badge-primary">${estadoActual}</span></small>
        </div>
        <div class="form-group">
          <label for="motivoDescarteRH"><strong>Motivo del descarte:</strong></label>
          <textarea id="motivoDescarteRH" class="form-control" rows="4" placeholder="Explique las razones..."></textarea>
          <small class="text-muted">Mínimo 10 caracteres</small>
        </div>
      </div>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fas fa-check mr-2"></i>Descartar',
    cancelButtonText: '<i class="fas fa-times mr-2"></i>Cancelar',
    width: '600px',
    preConfirm: () => {
      const motivo = document.getElementById('motivoDescarteRH').value.trim();
      if (!motivo || motivo.length < 10) {
        Swal.showValidationMessage('El motivo debe tener al menos 10 caracteres');
        return false;
      }
      return { motivo: motivo };
    }
  }).then((result) => {
    if (result.isConfirmed && result.value) {
      enviarDescarteRH(idCandidato, result.value.motivo);
    }
  });
}

  // Función para contar caracteres
  window[`updateCharCountRH${idCandidato}`] = function() {
    const count = $(`#motivoDescarteRH${idCandidato}`).val().length;
    $(`#charCountRH${idCandidato}`).text(count);
    
    if (count > 480) {
      $(`#charCountRH${idCandidato}`).removeClass('text-muted').addClass('text-warning');
    } else if (count === 500) {
      $(`#charCountRH${idCandidato}`).removeClass('text-warning').addClass('text-danger');
    } else {
      $(`#charCountRH${idCandidato}`).removeClass('text-warning text-danger').addClass('text-muted');
    }
  };

  // Evento confirmar descarte
  $(`#btnConfirmarDescarteRH${idCandidato}`).on('click', function() {
    const motivo = $(`#motivoDescarteRH${idCandidato}`).val().trim();
    
    if (!motivo) {
      Swal.fire({
        icon: 'warning',
        title: 'Motivo requerido',
        text: 'Debe ingresar el motivo del descarte',
        confirmButtonText: 'Entendido'
      });
      return;
    }
    
    if (motivo.length < 10) {
      Swal.fire({
        icon: 'warning',
        title: 'Motivo muy corto',
        text: 'El motivo debe tener al menos 10 caracteres',
        confirmButtonText: 'Entendido'
      });
      return;
    }
    
    confirmarDescarteRH(idCandidato, motivo);
  });

  // Limpiar modal al cerrar
  $(`#modalDescartarRH${idCandidato}`).on('hidden.bs.modal', function() {
    $(this).remove();
  });
};

// 3. FUNCIÓN PARA CONFIRMAR DESCARTE RH
window.confirmarDescarteRH = function(idCandidato, motivo) {
  // Cerrar modal
  $(`#modalDescartarRH${idCandidato}`).modal('hide');
  
  // Mostrar loading
  Swal.fire({
    title: 'Descartando candidato...',
    text: 'Procesando solicitud de Recursos Humanos...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });
  
  // Procesar descarte
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'POST',
    dataType: 'json',
    data: {
      action: 'descartar_candidato_rh',
      id_candidato: idCandidato,
      motivo_descarte: motivo
    },
    success: function(response) {
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: 'Candidato descartado',
          text: 'El candidato fue descartado correctamente por Recursos Humanos',
          timer: 3000,
          showConfirmButton: false
        }).then(() => {
          // Recargar el expediente automáticamente
          const idSolicitud = window.SOLICITUD_ID_ACTUAL || window.solicitudIdTemporal;
          if (idSolicitud && typeof mostrarCandidatosEnviadosrh === 'function') {
            mostrarCandidatosEnviadosrh(idSolicitud);
          } else {
            location.reload();
          }
        });
      } else {
        Swal.fire('Error', response.error || 'Error al descartar candidato', 'error');
      }
    },
    error: function(xhr, status, error) {
      Swal.fire('Error', 'Error de conexión: ' + error, 'error');
    }
  });
};


// Esta función debe agregarse al success del cambio de estado
function recargarExpedienteAutomatico(idCandidato, nuevoEstado) {
  console.log('🔄 Recargando expediente automático para:', idCandidato, nuevoEstado);
  
  // Esperar un momento para que se actualice la base de datos
  setTimeout(() => {
    // Obtener información actualizada del candidato
    const candidatoActualizado = window.CANDIDATOS_INDEX[idCandidato];
    if (candidatoActualizado) {
      candidatoActualizado.ESTADO_CANDIDATO = nuevoEstado;
    }
    
    // Recargar el expediente activo si corresponde al candidato cambiado
    const expedienteActivo = $('#expedienteCandidato');
    if (expedienteActivo.is(':visible') && expedienteActivo.length > 0) {
      // Verificar si el expediente mostrado es del candidato que cambió
      const candidatoSeleccionado = $('.candidate-card .card.border-primary').closest('.candidate-card').data('candidato-id');
      
      if (candidatoSeleccionado == idCandidato) {
        console.log('🔄 Recargando expediente del candidato seleccionado');
        
        // Obtener información de la solicitud
        const infoSolicitud = window.SOLICITUD_ACTUAL || {
          id: window.solicitudIdTemporal,
          tienda: 'Cargando...',
          puesto: 'Cargando...',
          supervisor: 'Cargando...'
        };
        
        // Obtener archivos actualizados y recargar expediente
        $.ajax({
          url: './gestionhumana/crudsolicitudesrh.php',
          type: 'GET',
          data: {
            action: 'get_archivos_candidato',
            id_candidato: idCandidato
          },
          dataType: 'json',
          success: function(response) {
            const archivos = response.success ? response.archivos : [];
            const nombreCompleto = candidatoActualizado ? 
              `${candidatoActualizado.NOMBRE_CANDIDATO} ${candidatoActualizado.APELLIDOS_CANDIDATO}`.trim() : 
              'Candidato';
            
            // Recargar usando la función existente
            if (typeof mostrarExpedienteCandidato === 'function') {
              mostrarExpedienteCandidato(idCandidato, nombreCompleto, archivos, infoSolicitud);
            }
          }
        });
      }
    }
  }, 1000);
}

//ENVIAR DESCARTE
function enviarDescarteRH(idCandidato, motivoDescarte) {
  Swal.fire({
    title: 'Descartando candidato...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });
  
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'POST',
    data: {
      action: 'descartar_candidato_rh',
      id_candidato: idCandidato,
      motivo_descarte: motivoDescarte
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: '¡Candidato descartado!',
          text: 'El candidato ha sido marcado como descartado por Recursos Humanos'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire('Error', response.error || 'Error desconocido', 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Error de conexión', 'error');
    }
  });
}

// ✅ FUNCIÓN PARA MOSTRAR EXPEDIENTE DESCARTADO BÁSICO CON ARCHIVOS
function mostrarExpedienteDescartadoRHConArchivos(idCandidato, nombreCompleto, datosCompletos, archivos, infoSolicitud) {
  console.log('🎯 Mostrando expediente descartado RH con archivos:', archivos);
  
  const motivoDescarte = datosCompletos.motivo_descarte || '';
  const infoDescarte = datosCompletos.info_descarte || {};
  const nombreQuienDescarto = infoDescarte.NOMBRE_QUIEN_DESCARTO || 'Usuario no identificado';
  const tipoUsuarioDescarto = infoDescarte.TIPO_USUARIO_DESCARTO || 'DESCONOCIDO';
  const fechaDescarte = infoDescarte.FECHA_CAMBIO || '';
  
  // Determinar icono y color según quién descartó
  let iconoPersona, etiquetaPersona, colorPersona;
  
  if (tipoUsuarioDescarto === 'SUPERVISOR') {
    iconoPersona = 'fa-user-tie';
    etiquetaPersona = 'Supervisor';
    colorPersona = 'info';
  } else if (tipoUsuarioDescarto === 'GERENTE') {
    iconoPersona = 'fa-user-cog';
    etiquetaPersona = 'Gerente';
    colorPersona = 'warning';
  } else if (tipoUsuarioDescarto === 'RRHH') {
    iconoPersona = 'fa-user-graduate';
    etiquetaPersona = 'Recursos Humanos';
    colorPersona = 'success';
  } else {
    iconoPersona = 'fa-user-question';
    etiquetaPersona = 'Usuario';
    colorPersona = 'secondary';
  }

  // ✅ GENERAR CARPETAS BASADO EN ARCHIVOS REALES
  const carpetasHtml = generarCarpetasDescartadoRH(archivos, idCandidato);

  const htmlExpediente = `
    <div class="card">
      <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
          <i class="fas fa-user-times mr-2"></i>Expediente de ${nombreCompleto}
          <span class="badge badge-light ml-2">DESCARTADO</span>
        </h5>
      </div>
      <div class="card-body">
        <!-- Información del candidato -->
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="alert alert-danger">
              <h6 class="mb-1"><i class="fas fa-user mr-1"></i> Información Personal</h6>
              <div><strong>Nombre:</strong> ${nombreCompleto}</div>
              <div><strong>DPI:</strong> ${datosCompletos.candidato?.DOCUMENTO_CANDIDATO || 'No proporcionado'}</div>
              <div><strong>Estado:</strong> <span class="badge badge-danger">DESCARTADO</span></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="alert alert-info">
              <h6 class="mb-1"><i class="fas fa-info-circle mr-1"></i> Información de la Solicitud</h6>
              <div><strong>Tienda:</strong> ${infoSolicitud.tienda || 'No especificada'}</div>
              <div><strong>Puesto:</strong> ${infoSolicitud.puesto || 'No especificado'}</div>
              <div><strong>Supervisor:</strong> ${infoSolicitud.supervisor || 'No asignado'}</div>
            </div>
          </div>
        </div>

        <!-- Información del descarte -->
        <div class="alert alert-danger mb-4">
          <div class="row">
            <div class="col-md-8">
              <h6 class="mb-2">
                <i class="fas fa-exclamation-triangle mr-1"></i> Motivo del Descarte
              </h6>
              <div class="mb-2">${motivoDescarte || 'No se proporcionó un motivo'}</div>
            </div>
            <div class="col-md-4">
              <h6 class="mb-2">
                <i class="fas ${iconoPersona} mr-1"></i> Descartado por
              </h6>
              <div>
                <span class="badge badge-${colorPersona} mr-1">${etiquetaPersona}</span>
                <br><small>${nombreQuienDescarto}</small>
              </div>
              ${fechaDescarte ? `<div><small class="text-muted">Fecha: ${fechaDescarte}</small></div>` : ''}
            </div>
          </div>
        </div>

        <!-- Estados completados antes del descarte -->
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="fas fa-folder-open mr-1"></i>Estados Completados antes del Descarte
            </h6>
          </div>
          <div class="card-body">
            <div class="row">
              ${carpetasHtml}
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
  
  $('#expedienteCandidato').html(htmlExpediente);
  console.log('✅ Expediente descartado RH mostrado con carpetas');
}

// ✅ FUNCIÓN PARA MOSTRAR EXPEDIENTE DESCARTADO BÁSICO CON ARCHIVOS
function mostrarExpedienteDescartadoBasicoRHConArchivos(idCandidato, nombreCompleto, motivoDesc, archivos, infoSolicitud) {
  console.log('🎯 Mostrando expediente descartado básico RH con archivos:', archivos);
  
  // ✅ GENERAR CARPETAS BASADO EN ARCHIVOS REALES
  const carpetasHtml = generarCarpetasDescartadoRH(archivos, idCandidato);

  const htmlExpediente = `
    <div class="card">
      <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
          <i class="fas fa-user-times mr-2"></i>Expediente de ${nombreCompleto}
          <span class="badge badge-light ml-2">DESCARTADO</span>
        </h5>
      </div>
      <div class="card-body">
        <div class="alert alert-danger mb-4">
          <h6 class="mb-2"><i class="fas fa-exclamation-triangle mr-1"></i> Motivo del Descarte</h6>
          <div>${motivoDesc || 'No se proporcionó un motivo'}</div>
        </div>

        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="fas fa-folder-open mr-1"></i>Estados Completados antes del Descarte
            </h6>
          </div>
          <div class="card-body">
            <div class="row">
              ${carpetasHtml}
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
  
  $('#expedienteCandidato').html(htmlExpediente);
  console.log('✅ Expediente descartado básico RH mostrado con carpetas');
}

// ✅ FUNCIÓN PARA GENERAR CARPETAS DE CANDIDATO DESCARTADO BASADO EN ARCHIVOS REALES
function generarCarpetasDescartadoRH(archivos, idCandidato) {
  console.log('📁 Generando carpetas para descartado con archivos:', archivos);
  
  if (!Array.isArray(archivos) || archivos.length === 0) {
    return '<div class="col-12 text-center text-muted">No hay documentos completados antes del descarte.</div>';
  }

  // Estados progresivos en el flujo
  const estadosProgresivos = [
    'CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 'Dia de Prueba', 'Poligrafo', 'Expediente RH'
  ];

  // ✅ MOSTRAR SOLO ESTADOS QUE TIENEN ARCHIVOS
  const carpetasConArchivos = [];
  
  estadosProgresivos.forEach(estado => {
    const archivosEstado = archivos.filter(arch => arch.ESTADO_RELACIONADO === estado);
    if (archivosEstado.length > 0) {
      carpetasConArchivos.push({
        estado: estado,
        cantidadArchivos: archivosEstado.length
      });
    }
  });

  if (carpetasConArchivos.length === 0) {
    return '<div class="col-12 text-center text-muted">No hay documentos completados antes del descarte.</div>';
  }

  return carpetasConArchivos.map(carpeta => `
    <div class="col-md-4 mb-3">
      <div class="card border-warning h-100 bg-light">
        <div class="card-body text-center p-3">
          <div class="mb-2">
            <i class="fas fa-folder-open fa-2x text-warning"></i>
          </div>
          <h6 class="card-title">${carpeta.estado}</h6>
          <div class="mb-2">
            <span class="badge badge-warning">Completado (${carpeta.cantidadArchivos})</span>
          </div>
          <button class="btn btn-sm btn-outline-primary" onclick="verArchivosCarpeta(${idCandidato}, '${carpeta.estado}')">
            <i class="fas fa-eye"></i> Ver Archvos
          </button>
        </div>
      </div>
    </div>
  `).join('');
}

//FUNCIÓN MEJORADA PARA GENERAR HTML DE CARPETAS PARA RH
function generarCarpetasHTMLRH(carpetas, idCandidato, estadoCandidato) {
  if (!Array.isArray(carpetas) || carpetas.length === 0) {
    return '<div class="col-12 text-center text-muted">No hay carpetas disponibles.</div>';
  }

  return carpetas.map(carpeta => {
    const nombreEstado = carpeta.nombre_estado || carpeta.NOMBRE_ESTADO || carpeta.nombre || 'Estado desconocido';
    const yaTieneArchivos = carpeta.ya_tiene_archivos || carpeta.YA_TIENE_ARCHIVOS || carpeta.completado;
    const puedeSubir = carpeta.puede_subir || carpeta.PUEDE_SUBIR || carpeta.disponible;
    const motivoBloqueo = carpeta.motivo_bloqueo || carpeta.MOTIVO_BLOQUEO;
    
    // Usar el estado pasado como parámetro
    const esContratado = estadoCandidato === 'Contratado';
    
    let iconoCarpeta, colorCarpeta, estadoCarpeta, accionBotones;
    
    if (yaTieneArchivos) {
      iconoCarpeta = 'fa-folder-open';
      colorCarpeta = 'success';
      estadoCarpeta = 'Completado';
      
      if (esContratado) {
        accionBotones = `
          <button class="btn btn-info btn-sm" onclick="verArchivosCarpeta(${idCandidato}, '${nombreEstado}')">
            <i class="fas fa-eye"></i> Ver
          </button>
        `;
      } else {
        accionBotones = `
          <button class="btn btn-success btn-sm mb-1" onclick="abrirGestorArchivos(${idCandidato}, '${nombreEstado}')">
            <i class="fas fa-cogs"></i> Gestionar Archivos
          </button>
          ${puedeSubir ? `
          <button class="btn btn-success btn-sm" onclick="subirArchivoCandidato(${window.solicitudIdTemporal || 0}, ${idCandidato}, '${nombreEstado}')">
            <i class="fas fa-plus"></i> Agregar
          </button>
          ` : ''}
        `;
      }
    } else if (puedeSubir) {
      iconoCarpeta = 'fa-folder-plus';
      colorCarpeta = 'primary';
      estadoCarpeta = 'Disponible';
      accionBotones = `
        <button class="btn btn-primary btn-sm" onclick="subirArchivoCandidato(${window.solicitudIdTemporal || 0}, ${idCandidato}, '${nombreEstado}')">
          <i class="fas fa-upload"></i> Subir
        </button>
      `;
    } else {
      iconoCarpeta = 'fa-folder';
      colorCarpeta = 'secondary';
      estadoCarpeta = 'Bloqueado';
      accionBotones = `
        <small class="text-muted">${motivoBloqueo || 'No disponible'}</small>
      `;
    }
    
    return `
      <div class="col-md-6 col-lg-4 mb-3">
        <div class="card border-${colorCarpeta} h-100">
          <div class="card-body text-center d-flex flex-column">
            <i class="fas ${iconoCarpeta} fa-2x text-${colorCarpeta} mb-2"></i>
            <h6 class="card-title">${nombreEstado}</h6>
            <span class="badge badge-${colorCarpeta}">${estadoCarpeta}</span>
            <div class="mt-auto">
              ${accionBotones}
            </div>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

// ===== FUNCIONES AUXILIARES PARA CARPETAS =====
function obtenerCarpetasDinamicas(idCandidato, infoSolicitud, archivos, callback) {
    console.log('🎯 INICIANDO obtenerCarpetasDinamicas');
    console.log('📋 ID Candidato:', idCandidato);
    console.log('📋 Info Solicitud:', infoSolicitud);
    console.log('📋 Archivos:', archivos);
    
    // ✅ USAR EL ENDPOINT CORRECTO QUE SÍ EXISTE
    $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php',
        type: 'GET',
        dataType: 'json',
data: { 
    action: 'get_carpetas_progresivas_rh',
    id_candidato: idCandidato,
    es_jefe: infoSolicitud.puesto && infoSolicitud.puesto.toUpperCase().includes('JEFE') ? 1 : 0
},
        success: function(response) {
            console.log('✅ RESPUESTA DEL SERVIDOR COMPLETA:', response);
            console.log('✅ Success:', response.success);
            console.log('✅ Estado Candidato:', response.estado_candidato);
            console.log('✅ Puesto Solicitado:', response.puesto_solicitado);
            console.log('✅ Carpetas:', response.carpetas);
            console.log('✅ Total Carpetas:', response.carpetas ? response.carpetas.length : 'undefined');
            
            if (response.success && response.carpetas) {
                response.carpetas.forEach((carpeta, index) => {
                    console.log(`📁 Carpeta ${index}: ${carpeta.nombre_estado} - Puede subir: ${carpeta.puede_subir} - Ya tiene archivos: ${carpeta.ya_tiene_archivos} - Motivo: ${carpeta.motivo_bloqueo}`);
                });
                
                // ✅ TRANSFORMAR LA RESPUESTA PARA QUE SEA COMPATIBLE
                const responseTransformada = {
                    success: true,
                    estadoActual: response.estado_candidato,
                    puestoSolicitado: response.puesto_solicitado,
                    carpetas: response.carpetas // Ya vienen con el formato correcto del backend
                };
                
                callback(responseTransformada);
            } else {
                console.warn('⚠️ Respuesta sin carpetas, usando fallback');
                // Fallback básico
                const fallback = {
                    success: true,
                    estadoActual: 'Desconocido',
                    carpetas: [
                        { nombre_estado: 'CV Enviado', puede_subir: true, ya_tiene_archivos: false, motivo_bloqueo: null },
                        { nombre_estado: 'Psicometrica', puede_subir: false, ya_tiene_archivos: false, motivo_bloqueo: 'No disponible' },
                        { nombre_estado: 'Entrevista RH', puede_subir: false, ya_tiene_archivos: false, motivo_bloqueo: 'No disponible' },
                        { nombre_estado: 'Entrevista Tecnica', puede_subir: false, ya_tiene_archivos: false, motivo_bloqueo: 'No disponible' },
                        { nombre_estado: 'Dia de Prueba', puede_subir: false, ya_tiene_archivos: false, motivo_bloqueo: 'No disponible' },
                        { nombre_estado: 'Poligrafo', puede_subir: false, ya_tiene_archivos: false, motivo_bloqueo: 'No disponible' },
                        { nombre_estado: 'Expediente RH', puede_subir: false, ya_tiene_archivos: false, motivo_bloqueo: 'No disponible' }
                    ]
                };
                callback(fallback);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error en la petición:', error);
            console.error('❌ Status:', status);
            console.error('❌ Response Text:', xhr.responseText);
            
            // Fallback en caso de error
            const fallback = {
                success: false,
                estadoActual: 'Error',
                carpetas: []
            };
            callback(fallback);
        }
    });
}

// *** FALLBACK: Carpetas libres (sin restricciones) ***
function generarCarpetasEstaticasLibres(archivos, estadoActual = 'CV Enviado') {
    console.log('🔄 Generando carpetas estáticas PROGRESIVAS');
    console.log('📍 Estado actual para fallback:', estadoActual);
    
    const estadosOrden = {
        'CV Enviado': 1,
        'Psicometrica': 2, 
        'Entrevista RH': 3,
        'Entrevista Tecnica': 4,
        'Dia de Prueba': 5,
        'Poligrafo': 6
    };
    
    const ordenActual = estadosOrden[estadoActual] || 1;
    
    const carpetas = Object.keys(estadosOrden).map((estado) => {
        const orden = estadosOrden[estado];
        const archivosEstado = archivos.filter(arch => arch.ESTADO_RELACIONADO === estado);
        const completado = archivosEstado.length > 0;
        const disponible = orden <= ordenActual; // Solo hasta el estado actual
        
        return {
            nombre: estado,
            completado: completado,
            disponible: disponible,
            motivo_bloqueo: disponible ? null : 'Debe avanzar el estado del candidato primero',
            archivos: archivosEstado.length,
            orden: orden
        };
    });
    
    console.log('🔄 Carpetas fallback generadas (progresivas):', carpetas);
    
    return {
        success: true,
        estadoActual: estadoActual,
        carpetas: carpetas,
        porcentajeProgreso: 0,
        puestoSolicitado: 'FALLBACK',
        esJefeTienda: true,
        notaPoligrafo: 'Fallback mode - carpetas progresivas'
    };
}

/*function generarCarpetasEstaticas(esJefe, archivos) {
    const estadosBase = [
        'CV Enviado', 
        'Psicometrica', 
        'Entrevista RH',
        'Entrevista Tecnica', 
        'Dia de Prueba'
    ];
    
    if (esJefe) {
        estadosBase.push('Poligrafo');
    }
    
    const carpetas = estadosBase.map((estado, index) => {
        const archivosEstado = archivos.filter(arch => arch.ESTADO_RELACIONADO === estado);
        const completado = archivosEstado.length > 0;
        
        return {
            nombre: estado,
            completado: completado,
            disponible: true, // *** SIEMPRE DISPONIBLES ***
            motivo_bloqueo: null,
            archivos: archivosEstado.length
        };
    });
    
    const completados = carpetas.filter(c => c.completado).length;
    
    return {
        success: true,
        estadoActual: 'CV Enviado',
        carpetas: carpetas,
        porcentajeProgreso: Math.round((completados / carpetas.length) * 100),
        notaPoligrafo: esJefe ? 'El Polígrafo aplica para JEFE DE TIENDA' : 'El Polígrafo solo aplica para JEFE DE TIENDA'
    };
}
*/

function verArchivosCarpeta(idCandidato, nombreEstado) {
  console.log('Viendo archivos de carpeta:', nombreEstado, 'para candidato:', idCandidato);
  
  Swal.fire({
    title: `<i class="fas fa-folder-open"></i> ${nombreEstado}`,
    html: '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando archivos...</div>',
    showConfirmButton: false,
    allowOutsideClick: false
  });

  cargarArchivosCandidato(idCandidato, nombreEstado)
    .done(function(response) {
      if (response.success && response.archivos && response.archivos.length > 0) {
        const archivosHtml = response.archivos.map(archivo => `
          <div class="border p-3 mb-2 rounded">
            <div class="row">
              <div class="col-8">
                <div class="d-flex align-items-center">
                  <i class="fas fa-file-alt text-primary mr-2"></i>
                  <div>
                    <strong class="d-block">${archivo.NOMBRE_SOLO || archivo.NOMBRE_ARCHIVO}</strong>
                    <small class="text-muted">
                      Subido: ${archivo.FECHA_SUBIDA} | 
                      Por: ${archivo.SUBIDO_POR_ROL || 'N/A'}
                      ${archivo.TAMAÑO_FORMATTED ? ` | ${archivo.TAMAÑO_FORMATTED}` : ''}
                    </small>
                  </div>
                </div>
              </div>
              <div class="col-4 text-right">
                ${archivo.EXISTE !== false ? `
                  <button class="btn btn-outline-primary btn-sm mr-1" onclick="verArchivo('${archivo.NOMBRE_ARCHIVO}')" title="Ver archivo">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn btn-outline-success btn-sm" onclick="descargarArchivo('${archivo.NOMBRE_ARCHIVO}')" title="Descargar">
                    <i class="fas fa-download"></i>
                  </button>
                ` : `
                  <span class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    No encontrado
                  </span>
                `}
              </div>
            </div>
          </div>
        `).join('');

        Swal.fire({
          title: `<i class="fas fa-folder-open"></i> Archivos - ${nombreEstado}`,
          html: `<div style="max-height: 400px; overflow-y: auto; text-align: left;">${archivosHtml}</div>`,
          confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
          confirmButtonColor: '#6c757d',
          width: '700px'
        });
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Sin archivos',
          text: `No hay archivos en la carpeta "${nombreEstado}"`,
          confirmButtonText: 'Entendido'
        });
      }
    })
    .fail(function() {
      Swal.fire('Error', 'No se pudieron cargar los archivos', 'error');
    });
}

// Nueva función para eliminar archivos de un estado
/*function eliminarArchivosEstado(idCandidato, nombreEstado) {
  Swal.fire({
    title: '¿Eliminar archivos?',
    html: `¿Estás seguro de eliminar todos los archivos de <strong>${nombreEstado}</strong>?<br><small class="text-muted">Esta acción no se puede deshacer</small>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php',
        type: 'POST',
        data: {
          action: 'eliminar_archivos_estado',
          id_candidato: idCandidato,
          estado: nombreEstado
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Archivos eliminados',
              text: `Se eliminaron los archivos de ${nombreEstado}`,
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              // Recargar expediente
              if (typeof cargarExpedienteCandidato === 'function') {
                cargarExpedienteCandidato(idCandidato);
              } else {
                location.reload();
              }
            });
          } else {
            Swal.fire('Error', response.error || 'Error al eliminar archivos', 'error');
          }
        },
        error: function() {
          Swal.fire('Error', 'Error de conexión', 'error');
        }
      });
    }
  });
}
*/

/*function eliminarArchivoIndividual(idArchivo, nombreArchivo, idCandidato) {
  Swal.fire({
    title: 'Eliminar archivo',
    html: `¿Deseas eliminar el archivo:<br><strong>${nombreArchivo}</strong>?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fas fa-trash mr-1"></i>Eliminar',
    cancelButtonText: '<i class="fas fa-times mr-1"></i>Cancelar',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      // Mostrar loading
      Swal.fire({
        title: 'Eliminando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php',
        type: 'POST',
        data: {
          action: 'eliminar_archivo_individual',
          id_archivo: idArchivo,
          nombre_archivo: nombreArchivo
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Archivo eliminado',
              text: 'El archivo se eliminó correctamente',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              // Recargar expediente
              if (typeof cargarExpedienteCandidato === 'function') {
                cargarExpedienteCandidato(idCandidato);
              } else {
                location.reload();
              }
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.error || 'Error al eliminar el archivo'
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al eliminar el archivo'
          });
        }
      });
    }
  });
}*/
 //==============FUNCION PARA CAMBIO DE ESTADO=================
 // *** EVENTO CORREGIDO PARA EL BOTÓN CAMBIAR ESTADO ***
$(document).on('click', '.btnCambiarEstado', function() {
    const idSolicitud = $(this).data('id');
    const tienda = $(this).data('tienda');
    const puesto = $(this).data('puesto');
    const razon = $(this).data('razon');
    const solicitadoPor = $(this).data('solicitado-por');
    
    console.log('🔘 Botón Cambiar Estado clickeado:', {idSolicitud, tienda, puesto, razon, solicitadoPor});
    
    // ✅ VALIDAR QUE EL ID EXISTE
    if (!idSolicitud) {
        Swal.fire('Error', 'No se pudo identificar la solicitud. ID no encontrado.', 'error');
        console.error('❌ ID de solicitud no encontrado en el botón');
        return;
    }
    
    // ✅ GUARDAR EL ID EN EL MODAL - CRÍTICO
    $('#modalCambiarEstado').data('id', idSolicitud);
    
    // ✅ TAMBIÉN GUARDARLO COMO ATRIBUTO (backup)
    $('#modalCambiarEstado').attr('data-id', idSolicitud);
    
    // ✅ VERIFICAR QUE SE GUARDÓ
    const idGuardado = $('#modalCambiarEstado').data('id');
    console.log('✅ ID guardado en modal:', idGuardado);
    
    if (!idGuardado) {
        console.error('❌ Error: No se pudo guardar el ID en el modal');
        Swal.fire('Error', 'Error interno al guardar el ID', 'error');
        return;
    }
    
    // Llenar información de la solicitud
    const infoHTML = `
        <div class="alert alert-primary" style="background-color: #007bff; border-color: #007bff;">
            <h6 class="text-white mb-3"><i class="fas fa-info-circle mr-2"></i>Información de la Solicitud</h6>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1 text-white"><strong>ID:</strong> ${idSolicitud}</p>
                    <p class="mb-1 text-white"><strong>Tienda:</strong> ${tienda}</p>
                    <p class="mb-1 text-white"><strong>Puesto:</strong> ${puesto}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-white"><strong>Solicitado por:</strong> ${solicitadoPor}</p>
                    <p class="mb-0 text-white"><strong>Razón:</strong> ${razon}</p>
                </div>
            </div>
        </div>
    `;
    
    $('#infoSolicitudCambio').html(infoHTML);
    
    // Resetear formulario
    $('#nuevoEstado').val('');
    $('#comentarioCambio').val('');
    $('#seccionCandidatos').hide();
    $('#formulariosCandidatos').empty();
    
    // Mostrar modal
    // Mostrar modal sin Bootstrap (método directo)
$('#modalCambiarEstado').show().addClass('show');
$('body').addClass('modal-open');
if ($('.modal-backdrop').length === 0) {
    $('<div class="modal-backdrop fade show"></div>').appendTo('body');
}
    
    console.log('📋 Modal abierto con ID:', idGuardado);
});

// *** EVENTO PARA CAMBIO DE ESTADO (mostrar sección candidatos) ***
$(document).on('change', '#nuevoEstado', function() {
    const estadoSeleccionado = $(this).val();
    console.log('Estado seleccionado:', estadoSeleccionado);
    
    if (estadoSeleccionado === 'Candidatos en Seleccion') {
        $('#seccionCandidatos').slideDown(300);
    } else {
        $('#seccionCandidatos').slideUp(300);
        $('#cantidadCandidatos').val('');
    }
});

// *** EVENTO PARA CONFIRMAR CANTIDAD DE CANDIDATOS ***
// Confirmar cantidad de candidatos y generar formularios
$(document).on('click', '#btnConfirmarCandidatos', function() {
  console.log('Botón Continuar clickeado');
  
  const cantidad = parseInt($('#cantidadCandidatos').val());
  const comentario = $('#comentarioCambio').val().trim();
  
  // VALIDACIÓN OBLIGATORIA DEL COMENTARIO PRIMERO
  if (!comentario) {
    Swal.fire({
      icon: 'warning',
      title: 'Comentario obligatorio',
      text: 'Debe ingresar un comentario antes de continuar con el registro de candidatos',
      confirmButtonText: 'Entendido'
    });
    $('#comentarioCambio').focus();
    return;
  }
  
  if (!cantidad || cantidad < 1 || cantidad > 10) {
    Swal.fire('Error', 'Debe ingresar una cantidad válida entre 1 y 10', 'warning');
    return;
  }
  
  const idSolicitud = $('#modalCambiarEstado').data('id');
  
  // Cerrar modal usando método nativo (sin Bootstrap)
  document.getElementById('modalCambiarEstado').style.display = 'none';
  document.querySelector('.modal-backdrop')?.remove();
  document.body.classList.remove('modal-open');
  
  // Iniciar registro inmediatamente
  registrarCandidatosUnoAUno(idSolicitud, 'Candidatos en Seleccion', comentario, cantidad, 1, []);
});

// *** FUNCIÓN PARA GENERAR FORMULARIOS DE CANDIDATOS ***
function generarFormulariosCandidatos(cantidad) {
    let formulariosHTML = '<div class="row">';
    
    for (let i = 1; i <= cantidad; i++) {
        formulariosHTML += `
            <div class="col-md-6 mb-3">
                <div class="card border-primary" data-candidato="${i}">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Candidato ${i}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="nombre_${i}" class="form-label font-weight-bold text-dark">
                                <i class="fas fa-user mr-1"></i>Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre_${i}"
                                   class="form-control candidato-nombre" 
                                   placeholder="Ingrese el nombre"
                                   required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="apellidos_${i}" class="form-label font-weight-bold text-dark">
                                <i class="fas fa-user-tag mr-1"></i>Apellidos <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   id="apellidos_${i}"
                                   class="form-control candidato-apellidos" 
                                   placeholder="Ingrese los apellidos"
                                   required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="documento_${i}" class="form-label font-weight-bold text-dark">
                                <i class="fas fa-id-card mr-1"></i>DPI (opcional)
                            </label>
                            <input type="text" 
                                   id="documento_${i}"
                                   class="form-control candidato-documento" 
                                   placeholder="Número de DPI (opcional)">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    formulariosHTML += '</div>';
    
    // Mostrar mensaje de éxito
    const mensajeExito = `
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Formularios generados correctamente</strong><br>
            Complete la información de los ${cantidad} candidato(s) y haga clic en "Guardar Cambios".
        </div>
    `;
    
    $('#formulariosCandidatos').html(mensajeExito + formulariosHTML);
    
    // Scroll hacia los formularios
    $('#formulariosCandidatos')[0].scrollIntoView({ behavior: 'smooth' });
}

// *** EVENTO PARA GUARDAR CAMBIO DE ESTADO ***
            // =========================================================
            // ✅ EVENTO ÚNICO PARA GUARDAR CAMBIO DE ESTADO - CORREGIDO
            // =========================================================
$('#btnGuardarCambioEstado').off('click').on('click', function () {
    console.log('🔵 Click en btnGuardarCambioEstado');
    
    // ✅ INTENTAR OBTENER EL ID DE MÚLTIPLES FORMAS
    let id = $('#modalCambiarEstado').data('id');
    
    if (!id) {
        id = $('#modalCambiarEstado').attr('data-id');
    }
    
    const nuevoEstado = $('#nuevoEstado').val();
    const comentario = $('#comentarioCambio').val();

    // Debugging
    console.log('ID Solicitud:', id);
    console.log('Nuevo Estado capturado:', nuevoEstado);
    console.log('Comentario:', comentario);

    // Validaciones
    if (!id) {
        Swal.fire('Error', 'No se encontró el ID de la solicitud. Por favor, cierra el modal y ábrelo de nuevo.', 'error');
        return;
    }

    if (!nuevoEstado || nuevoEstado === '' || nuevoEstado === 'undefined') {
        Swal.fire('Error', 'Debe seleccionar un estado válido.', 'error');
        return;
    }

    if (!comentario || comentario.trim() === '') {
        Swal.fire('Error', 'El comentario es obligatorio.', 'error');
        return;
    }

    // SI ES "Candidatos en Seleccion", validar que se registraron candidatos
    if (nuevoEstado === 'Candidatos en Seleccion') {
        const candidatos = validarYObtenerDatosCandidatos();
        
        if (!candidatos || candidatos.length === 0) {
            Swal.fire('Error', 'Debe registrar al menos un candidato.', 'error');
            return;
        }

        // Procesar candidatos primero
        procesarTodosLosCandidatos(id, nuevoEstado, comentario, candidatos);
        return;
    }

    // PARA OTROS ESTADOS: Cambiar directamente
    enviarCambioEstado(id, nuevoEstado, comentario);
});

// =========================================================
// ✅ FUNCIÓN PARA ENVIAR EL CAMBIO DE ESTADO
// =========================================================
function enviarCambioEstado(id, nuevoEstado, comentario) {
    console.log('📤 Enviando cambio de estado:', { id, nuevoEstado, comentario });

    $('#btnGuardarCambioEstado').prop('disabled', true);
    mostrarLoadingCambioEstado();

    $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php?action=toggle_solicitud_status',
        type: 'POST',
        dataType: 'json',
        data: {
            id_solicitud: id,
            nuevo_estado: nuevoEstado,
            comentario: comentario
        },
        success: function (res) {
            console.log('✅ Respuesta del servidor:', res);
            
            if (typeof res === 'string') {
                try {
                    res = JSON.parse(res);
                } catch (e) {
                    console.error('Error parseando respuesta:', e);
                    Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
                    return;
                }
            }

            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: res.mensaje || 'Estado actualizado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#modalCambiarEstado').modal('hide');
                    
                    // ✅ RECARGAR LA PÁGINA COMPLETA
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', res.error || 'Error al actualizar el estado', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error('❌ Error AJAX:', error);
            console.error('Response Text:', xhr.responseText);
            Swal.fire('Error', 'Error de conexión: ' + error, 'error');
        },
        complete: function () {
            $('#btnGuardarCambioEstado').prop('disabled', false);
        }
    });
}

// =========================================================
// ✅ FUNCIÓN PARA MOSTRAR LOADING
// =========================================================
function mostrarLoadingCambioEstado() {
    Swal.fire({
        title: 'Procesando...',
        html: 'Por favor espera mientras se guardan los cambios',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
}

// *** FUNCIÓN PARA INICIAR REGISTRO DE CANDIDATOS CON SWEET ALERT ***
function iniciarRegistroCandidatos(idSolicitud, nuevoEstado, comentario) {
    // Paso 1: Preguntar cantidad
    Swal.fire({
        title: 'Registro de Candidatos',
        text: '¿Cuántos candidatos desea registrar?',
        input: 'number',
        inputAttributes: {
            min: 1,
            max: 10,
            step: 1
        },
        inputValue: 1,
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            const num = parseInt(value);
            if (!value || num < 1 || num > 10) {
                return 'Ingrese un número entre 1 y 10';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const cantidad = parseInt(result.value);
            registrarCandidatosUnoAUno(idSolicitud, nuevoEstado, comentario, cantidad, 1, []);
        }
    });
}

// *** FUNCIÓN PARA REGISTRAR CANDIDATOS UNO A UNO ***
// FUNCIÓN PARA REGISTRAR CANDIDATOS UNO A UNO CON SWEET ALERT
function registrarCandidatosUnoAUno(idSolicitud, nuevoEstado, comentario, totalCandidatos, candidatoActual, candidatosRegistrados) {
    Swal.fire({
        title: `<i class="fas fa-user-plus"></i> Candidato ${candidatoActual} de ${totalCandidatos}`,
        html: `
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group text-left mb-3">
                            <label class="form-label font-weight-bold text-dark">
                                <i class="fas fa-user mr-1"></i>Nombre <span class="text-danger">*</span>
                            </label>
                            <input id="swal-nombre" class="form-control" placeholder="Ingrese el nombre" style="border: 2px solid #ddd;">
                        </div>
                        <div class="form-group text-left mb-3">
                            <label class="form-label font-weight-bold text-dark">
                                <i class="fas fa-user-tag mr-1"></i>Apellidos <span class="text-danger">*</span>
                            </label>
                            <input id="swal-apellidos" class="form-control" placeholder="Ingrese los apellidos" style="border: 2px solid #ddd;">
                        </div>
                        <div class="form-group text-left mb-0">
                            <label class="form-label font-weight-bold text-dark">
                                <i class="fas fa-id-card mr-1"></i>DPI (opcional)
                            </label>
                            <input id="swal-documento" class="form-control" placeholder="Número de DPI (opcional)" style="border: 2px solid #ddd;">
                        </div>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: `<i class="fas fa-${candidatoActual < totalCandidatos ? 'arrow-right' : 'check'}"></i> ${candidatoActual < totalCandidatos ? 'Siguiente' : 'Finalizar'}`,
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        width: '450px',
        customClass: {
            popup: 'swal2-border-radius'
        },
        preConfirm: () => {
            const nombre = document.getElementById('swal-nombre').value.trim();
            const apellidos = document.getElementById('swal-apellidos').value.trim();
            const documento = document.getElementById('swal-documento').value.trim();
            
            if (!nombre) {
                Swal.showValidationMessage('El nombre es obligatorio');
                return false;
            }
            
            if (!apellidos) {
                Swal.showValidationMessage('Los apellidos son obligatorios');
                return false;
            }
            
            return { nombre, apellidos, documento };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            candidatosRegistrados.push({
                nombre: result.value.nombre,
                apellidos: result.value.apellidos,
                documento: result.value.documento || ''
            });
            
            if (candidatoActual < totalCandidatos) {
                registrarCandidatosUnoAUno(idSolicitud, nuevoEstado, comentario, totalCandidatos, candidatoActual + 1, candidatosRegistrados);
            } else {
                procesarTodosLosCandidatos(idSolicitud, nuevoEstado, comentario, candidatosRegistrados);
            }
        } else {
            Swal.fire('Cancelado', 'Registro de candidatos cancelado', 'info');
        }
    });
}

//PROCESAR TODOS LOS CANDIDATOS
function procesarTodosLosCandidatos(idSolicitud, nuevoEstado, comentario, candidatos) {
    console.log('Procesando candidatos:', candidatos);
    
    Swal.fire({
        title: 'Registrando candidatos...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    const formData = new FormData();
    formData.append('action', 'registrar_candidatos_iniciales');
    formData.append('id_solicitud', String(idSolicitud));
    formData.append('nuevo_estado', nuevoEstado);
    formData.append('comentario', comentario);
    formData.append('candidatos', JSON.stringify(candidatos));
    
    $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            if (response.success) {
                // ✅ VERIFICAR SI DEBE MANTENER MODAL ABIERTO
                if (response.mantener_modal_abierto && response.candidatos_listos) {
                    // ✅ MOSTRAR ÉXITO PERO NO CERRAR TODO
                    Swal.fire({
                        icon: 'success',
                        title: '¡Candidatos registrados!',
                        html: `✅ <b>${response.candidatos_registrados}</b> candidatos registrados<br>
                               📋 Los datos están listos para continuar<br>
                               <small class="text-muted">Ahora puede proceder a guardar el cambio de estado</small>`,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        // ✅ VOLVER AL MODAL PRINCIPAL CON DATOS PRESERVADOS
                        setTimeout(() => {
                            // Preservar datos en el modal principal
                            if ($('#comentarioCambio').length) {
                                $('#comentarioCambio').val(response.comentario_original);
                            }
                            if ($('#nuevoEstado').length) {
                                $('#nuevoEstado').val(response.nuevo_estado);
                            }
                            
                            // ✅ FORZAR APERTURA DEL MODAL DE FORMA MÁS DIRECTA
                          console.log('🔄 Intentando reabrir modal...');

                          // Asegurar que no hay modales interferiendo
                          $('.modal').modal('hide');
                          $('.modal-backdrop').remove();
                          $('body').removeClass('modal-open');

                          // Esperar un momento y abrir
                          setTimeout(() => {
                              console.log('🎯 Abriendo modal principal...');
                              
                              // Método Bootstrap 4 directo
                              $('#modalCambiarEstado').modal({
                                  backdrop: 'static',
                                  keyboard: false,
                                  show: true
                              });
                              
                              // Backup: Si Bootstrap falla, abrir manualmente
                              setTimeout(() => {
                                  if (!$('#modalCambiarEstado').hasClass('show') && !$('#modalCambiarEstado').is(':visible')) {
                                      console.log('⚡ Método manual...');
                                      $('#modalCambiarEstado').show().addClass('show');
                                      $('body').addClass('modal-open');
                                      $('<div class="modal-backdrop fade show"></div>').appendTo('body');
                                  }
                              }, 500);
                              
                          }, 300);
                            
                            // ✅ DESTACAR EL BOTÓN "GUARDAR CAMBIOS"
                            const btnGuardar = $('#btnGuardarCambioEstado');
                            if (btnGuardar.length) {
                                btnGuardar.removeClass('btn-primary').addClass('btn-success');
                                btnGuardar.html('<i class="fas fa-check mr-2"></i>Completar Cambio de Estado');
                                btnGuardar.focus();
                            }
                            
                            // ✅ AGREGAR MENSAJE INFORMATIVO EN EL MODAL
                            if (!$('#mensajeCandidatosListos').length && $('#modalCambiarEstado .modal-body').length) {
                                $('#modalCambiarEstado .modal-body').prepend(`
                                    <div id="mensajeCandidatosListos" class="alert alert-success alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <i class="fas fa-check-circle mr-2"></i>
                                        <strong>Candidatos registrados exitosamente</strong><br>
                                        <small>${response.candidatos_registrados} candidatos listos. Haga clic en "Completar Cambio de Estado" para finalizar.</small>
                                    </div>
                                `);
                            }
                        }, 500);
                    });
                } else {
                    // ✅ FLUJO TRADICIONAL - CERRAR TODO
                    Swal.fire('Éxito', `Candidatos registrados: ${response.candidatos_registrados}`, 'success');
                    
                    // Recargar tabla/página
                    if (typeof cargarSolicitudesRRHH === 'function') {
                        cargarSolicitudesRRHH();
                    } else if (typeof cargarSolicitudes === 'function') {
                        cargarSolicitudes();
                    } else {
                        location.reload();
                    }
                }
            } else {
                Swal.fire('Error', response.error || 'Error al procesar la solicitud', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            console.error('Response:', xhr.responseText);
            
            Swal.fire('Error', 'Error de conexión al servidor', 'error');
        }
    });
}

// PROCESAR CANDIDATOS AGREGAR esta función después de las funciones existentes:
function procesarCandidatos(candidatos) {
    console.log('Procesando candidatos con parámetros:', candidatos);
    
    // OBTENER ID DE SOLICITUD DEL MODAL ACTIVO
    let idSolicitud = null;
    
    // Intentar varios métodos para obtener el ID
    if ($('#modalCambiarEstado').data('id')) {
        idSolicitud = $('#modalCambiarEstado').data('id');
    } else if ($('#modalExpedientes').data('id-solicitud')) {
        idSolicitud = $('#modalExpedientes').data('id-solicitud');
    } else if (window.solicitudIdTemporal) {
        idSolicitud = window.solicitudIdTemporal;
    } else if ($('#solicitud_id_hidden').val()) {
        idSolicitud = $('#solicitud_id_hidden').val();
    }
    
    if (!idSolicitud) {
        Swal.fire('Error', 'No se pudo identificar la solicitud. Cierra y abre nuevamente el modal.', 'error');
        return;
    }
    
    // OBTENER COMENTARIO 
    const comentario = window.comentarioTemporal || $('#comentarioCambio').val().trim();
    
    if (!comentario) {
        Swal.fire({
            icon: 'error',
            title: 'Comentario requerido',
            text: 'El comentario es obligatorio para registrar candidatos',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Llamar a la función principal
    procesarTodosLosCandidatos(idSolicitud, 'Candidatos Enviados', comentario, candidatos);
}

// *** FUNCIÓN PARA VALIDAR CANDIDATOS ***
function validarYObtenerDatosCandidatos() {
    const candidatos = [];
    let errores = [];
    
    $('[data-candidato]').each(function() {
        const numero = $(this).data('candidato');
        const nombre = $(this).find('.candidato-nombre').val().trim();
        const apellidos = $(this).find('.candidato-apellidos').val().trim();
        const documento = $(this).find('.candidato-documento').val().trim();
        
        if (!nombre) errores.push(`Candidato ${numero}: Nombre obligatorio`);
        if (!apellidos) errores.push(`Candidato ${numero}: Apellidos obligatorios`);
        
        if (nombre && apellidos) {
            candidatos.push({
                numero,
                nombre,
                apellidos,
                documento
            });
        }
    });
    
    if (errores.length > 0) {
        Swal.fire('Errores', errores.join('\n'), 'error');
        return null;
    }
    
    return candidatos;
}

// *** FUNCIÓN PARA PROCESAR CAMBIO SIMPLE ***
function procesarCambioEstadoSimple(idSolicitud, nuevoEstado, comentario) {
    const formData = new FormData();
    formData.append('id_solicitud', idSolicitud);
    formData.append('nuevo_estado', nuevoEstado);
    formData.append('comentario', comentario);
    
    $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php?action=toggle_solicitud_status',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Éxito', 'Estado actualizado correctamente', 'success');
                $('#modalCambiarEstado').modal('hide');
                
                // Recargar tabla
                if (typeof cargarSolicitudesRRHH === 'function') {
                    cargarSolicitudesRRHH();
                } else {
                    location.reload();
                }
            } else {
                Swal.fire('Error', response.error || 'Error al actualizar estado', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}

// *** FUNCIÓN PARA PROCESAR CON CANDIDATOS ***
function procesarCambioEstadoConCandidatos(idSolicitud, nuevoEstado, comentario, candidatos) {
    const formData = new FormData();
    formData.append('action', 'registrar_candidatos_iniciales');
    formData.append('id_solicitud', idSolicitud);
    formData.append('nuevo_estado', nuevoEstado);
    formData.append('comentario', comentario);
    formData.append('candidatos', JSON.stringify(candidatos));
    
    $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // ✅ VERIFICAR SI DEBE MANTENER MODAL ABIERTO
                if (response.mantener_modal_abierto && response.candidatos_listos) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Candidatos registrados!',
                        html: `Candidatos registrados: <b>${response.candidatos_agregados || response.candidatos_registrados}</b><br>
                               <small class="text-muted">Proceda a completar el cambio de estado</small>`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                    // NO cerrar modal aquí - mantener flujo abierto
                } else {
                    Swal.fire('Éxito', `Candidatos registrados: ${response.candidatos_agregados || response.candidatos_registrados}`, 'success');
                    $('#modalCambiarEstado').modal('hide');
                    
                    // Recargar tabla
                    if (typeof cargarSolicitudesRRHH === 'function') {
                        cargarSolicitudesRRHH();
                    } else {
                        location.reload();
                    }
                }
            } else {
                Swal.fire('Error', response.error || 'Error al registrar candidatos', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    });
}

// ================================
// MODIFICACIÓN 3: Agregar evento para limpiar mensajes al cerrar modal
// ================================

// AGREGAR este evento (no reemplazar, es nuevo):
$('#modalCambiarEstado').on('hidden.bs.modal', function() {
    // Limpiar mensaje de candidatos listos
    $('#mensajeCandidatosListos').remove();
    
    // Restaurar botón a estado normal
    const btnGuardar = $('#btnGuardarCambioEstado');
    if (btnGuardar.length) {
        btnGuardar.removeClass('btn-success').addClass('btn-primary');
        btnGuardar.html('Guardar Cambios');
    }
    
    // Limpiar variables temporales
    window.candidatosProcesados = false;
    window.comentarioTemporal = null;
});
 
 //===========FIN DE LA FUNCION CAMBIO DE ESTADO===============

// ===== FUNCIONES ADICIONALES =====
function verHistorialCandidato(idCandidato) {
  Swal.fire({
    title: 'Historial del Candidato',
    text: 'Función en desarrollo',
    icon: 'info'
  });
}

function cambiarEstadoCandidato(idCandidato, puestoSolicitado) {
  console.log('🎯 Cambiar estado candidato:', idCandidato, 'Puesto recibido:', puestoSolicitado);

  //MOSTRAR LOADING MIENTRAS CARGA
  Swal.fire({
    title: 'Cargando...',
    text: 'Obteniendo información del estado',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });
  
  // OBTENER EL PUESTO DESDE LA BASE DE DATOS (no confiar en el parámetro)
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'GET',
    data: {
      action: 'get_permisos_subida_candidato_rh',
      id_candidato: idCandidato
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        const puestoReal = response.puesto_solicitado || '';
        const estadoActual = response.estado_candidato || 'CV Enviado';
        const esJefeTienda = puestoReal.toUpperCase().trim() === 'JEFE DE TIENDA';
        const carpetas = response.carpetas || [];
        
        console.log('📋 Puesto real obtenido:', puestoReal);
        console.log('👑 Es Jefe de Tienda:', esJefeTienda);
        console.log('🔵 Estado actual:', estadoActual);
        
        // TODOS los estados disponibles SIEMPRE
        let estadosDisponibles = [
          'CV Enviado', 
          'Psicometrica', 
          'Entrevista RH',
          'Entrevista Tecnica', 
          'Dia de Prueba'
        ];
        
        if (esJefeTienda) {
          estadosDisponibles.push('Poligrafo');
        }
        
        // VALIDAR SI PUEDE ACCEDER A APROBACION DE AVAL
        const estadosRequeridos = ['CV Enviado', 'Psicometrica', 'Entrevista RH', 'Entrevista Tecnica', 'Dia de Prueba'];
        if (esJefeTienda) {
          estadosRequeridos.push('Poligrafo');
        }
        
        const estadosFaltantes = estadosRequeridos.filter(estado => {
          const carpeta = carpetas.find(c => c.nombre_estado === estado);
          return !carpeta || !carpeta.ya_tiene_archivos;
        });
        
        const todosTienenArchivos = estadosFaltantes.length === 0;
        
        // Construir opciones - TODOS los estados siempre habilitados
        let opcionesHTML = '<option value="">Seleccione estado...</option>';
        estadosDisponibles.forEach(estado => {
          const esActual = estado === estadoActual;
          opcionesHTML += `<option value="${estado}" ${esActual ? 'selected' : ''}>${estado}${esActual ? ' (Actual)' : ''}</option>`;
        });
        
        // Aprobacion de Aval solo si cumple requisitos
        if (todosTienenArchivos) {
          opcionesHTML += `<option value="Aprobacion de Aval">Aprobacion de Aval</option>`;
        } else {
          opcionesHTML += `<option value="" disabled style="color: #999;">Aprobacion de Aval (Faltan documentos)</option>`;
        }
        
        Swal.fire({
          title: '<i class="fas fa-exchange-alt"></i> Cambiar Estado',
          html: `
            <div style="text-align: left;">
              <div class="alert alert-info">
                <strong>Puesto:</strong> ${puestoReal}<br>
                <strong>Estado actual:</strong> ${estadoActual}<br>
                <strong>Tipo:</strong> ${esJefeTienda ? 'JEFE DE TIENDA (incluye Polígrafo)' : 'PUESTO REGULAR (sin Polígrafo)'}
              </div>
              
              ${!todosTienenArchivos ? `
                <div class="alert alert-warning">
                  <strong><i class="fas fa-exclamation-triangle"></i> Para "Aprobacion de Aval":</strong><br>
                  Complete primero estos estados:<br>
                  <ul class="text-left mb-0 mt-2 small">
                    ${estadosFaltantes.map(e => `<li>${e}</li>`).join('')}
                  </ul>
                </div>
              ` : ''}
              
              <div class="form-group">
                <label for="nuevoEstadoSelect">Nuevo Estado:</label>
                <select id="nuevoEstadoSelect" class="form-control">
                  ${opcionesHTML}
                </select>
                <small class="form-text text-muted">
                  Puede cambiar a cualquier estado. Solo "Aprobacion de Aval" requiere completar todos los anteriores.
                </small>
              </div>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: '<i class="fas fa-check"></i> Cambiar',
          cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
          confirmButtonColor: '#28a745',
          width: '550px',
          preConfirm: () => {
            const nuevoEstado = document.getElementById('nuevoEstadoSelect').value;
            
            if (!nuevoEstado) {
              Swal.showValidationMessage('Debe seleccionar un estado');
              return false;
            }
            
            if (nuevoEstado === estadoActual) {
              Swal.showValidationMessage('El candidato ya está en ese estado');
              return false;
            }
            
            return { nuevoEstado };
          }
        }).then((result) => {
          if (result.isConfirmed) {
            aplicarCambioEstado(idCandidato, result.value.nuevoEstado, '');
          }
        });
        
      } else {
        Swal.fire('Error', 'No se pudo obtener información del candidato', 'error');
      }
    },
    error: function(xhr, status, error) {
      Swal.fire('Error', 'Error de conexión: ' + error, 'error');
    }
  });
}

function generarReporteCandidato(idCandidato) {
  Swal.fire({
    title: 'Generar Reporte',
    text: 'Función en desarrollo',
    icon: 'info'
  });
}

function descargarArchivo(nombreArchivo) {
    console.log('🎯 Descargando archivo:', nombreArchivo);
    const url = `./gestionhumana/crudsolicitudesrh.php?action=descargar_archivo&archivo=${encodeURIComponent(nombreArchivo)}`;
    
    const link = document.createElement('a');
    link.href = url;
    link.download = nombreArchivo;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// *** NUEVA FUNCIÓN: Ver archivo en nueva ventana ***
function verArchivo(nombreArchivo) {
    console.log('🎯 Viendo archivo:', nombreArchivo);
    const url = `./gestionhumana/crudsolicitudesrh.php?action=ver_archivo&archivo=${encodeURIComponent(nombreArchivo)}`;
    window.open(url, '_blank');
}

// ===== FUNCIONES DE DEPURACIÓN =====
function debugBotonesCandidatos() {
  console.log('=== DEBUG BOTONES CANDIDATOS ===');
  
  $('.btnVerCandidatosrh').each(function(index) {
    const $btn = $(this);
    const id = $btn.data('id');
    const solicitudId = $btn.data('solicitud-id');
    const row = $btn.closest('tr');
    const rowId = row.data('id');
    
    console.log(`Botón ${index + 1}:`, {
      'data-id': id,
      'data-solicitud-id': solicitudId,
      'tr data-id': rowId,
      'elemento': $btn[0]
    });
  });
  
  console.log('=================================');
}

// ===== FUNCIONES AUXILIARES DE REPORTES =====
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

// ===== FUNCIONES PARA CANDIDATOS ADICIONALES =====
function procesarCandidatosAdicionales(idSolicitud, candidatos) {
  // Resolver de forma robusta
  const id = resolverIdSolicitud(idSolicitud);

  if (!id) {
    Swal.fire('Error', 'No se pudo identificar la solicitud. Cierra y vuelve a abrir el modal.', 'error');
    return;
  }
  if (!Array.isArray(candidatos) || candidatos.length === 0) {
    Swal.fire('Error', 'No hay candidatos para enviar.', 'warning');
    return;
  }

  const formData = new FormData();
  formData.append('id_solicitud', String(id));

  candidatos.forEach((candidato, index) => {
    formData.append(`candidatos[${index}][nombre]`, (candidato.nombre || '').trim());
    formData.append(`candidatos[${index}][apellidos]`, (candidato.apellidos || '').trim());
    formData.append(`candidatos[${index}][documento]`, (candidato.documento || '').trim());
  });

  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php?action=cargar_candidatos_adicionales',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: '¡Candidatos registrados!',
          html: `Agregados: <b>${response.candidatos_agregados}</b><br>` +
                (response.errores?.length ? ('<small>Errores:<br>' + response.errores.join('<br>') + '</small>') : '')
        });
        if (typeof actualizarListaCandidatos === 'function') {
          actualizarListaCandidatos(id);
        }
      } else {
        Swal.fire('Error', response.error || 'No se pudo registrar', 'error');
      }
    },
    error: function(xhr, status, error) {
      Swal.fire('Error', 'Error de conexión: ' + error, 'error');
    }
  });
}

//==================================================================================================
// FUNCIONES PARA GESTION DE ARCHIVOS DE LOS CANDIDATOS COMO ELIMINAR, VER, DESCARGAR,ETC
//==================================================================================================

// AGREGAR esta nueva función para gestionar archivos de forma elegante:
function abrirGestorArchivos(idCandidato, nombreEstado) {
  // Mostrar loading elegante
  Swal.fire({
    title: `Gestionando ${nombreEstado}`,
    text: 'Cargando archivos...',
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Cargar archivos de la carpeta
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'GET',
    data: {
      action: 'get_archivos_candidato',
      id_candidato: idCandidato,
      estado_filtro: nombreEstado
    },
    dataType: 'json',
    success: function(response) {
      if (response.success && response.archivos.length > 0) {
        mostrarGestorArchivos(nombreEstado, response.archivos, idCandidato);
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Sin archivos',
          text: `No hay documentos en ${nombreEstado}`,
          confirmButtonText: 'Entendido'
        });
      }
    },
    error: function() {
      Swal.fire('Error', 'No se pudieron cargar los archivos', 'error');
    }
  });
}

function mostrarGestorArchivos(nombreEstado, archivos, idCandidato) {
  const archivosHTML = archivos.map(archivo => `
    <div class="archivo-item-gestor" data-archivo-id="${archivo.ID_ARCHIVO}" data-archivo-nombre="${archivo.NOMBRE_ARCHIVO}">
      <div class="row align-items-center py-3 px-2 border-bottom">
        <div class="col-1">
          <div class="form-check">
            <input class="form-check-input archivo-checkbox" type="checkbox" value="${archivo.ID_ARCHIVO}">
          </div>
        </div>
        <div class="col-1 text-center">
          <i class="fas fa-file-alt text-primary" style="font-size: 1.2rem;"></i>
        </div>
        <div class="col-6">
          <div class="archivo-info">
            <div class="archivo-nombre fw-medium" style="font-size: 0.9rem;">${archivo.NOMBRE_ARCHIVO}</div>
            <small class="text-muted">Subido: ${archivo.FECHA_SUBIDA}</small>
          </div>
        </div>
        <div class="col-4 text-end">
          <div class="btn-group" role="group">
            <button class="btn btn-outline-info btn-sm" onclick="verArchivo('${archivo.NOMBRE_ARCHIVO}')" title="Ver">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="descargarArchivo('${archivo.NOMBRE_ARCHIVO}')" title="Descargar">
              <i class="fas fa-download"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  `).join('');

  Swal.fire({
    title: `<i class="fas fa-folder-open me-2"></i>Gestionar - ${nombreEstado}`,
    html: `
      <div class="gestor-archivos" style="max-height: 400px; overflow-y: auto;">
        <!-- Controles superiores -->
        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
          <!--<div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label fw-medium" for="selectAll">
              Seleccionar todos
            </label>
          </div>-->
          <button class="btn btn-outline-danger btn-sm" id="eliminarSeleccionados" style="display: none;">
            <i class="fas fa-trash me-1"></i>Eliminar seleccionados
          </button>
        </div>
        
        <!-- Lista de archivos -->
        <div class="archivos-lista">
          ${archivosHTML}
        </div>
      </div>
    `,
    width: '700px',
    showConfirmButton: true,
    confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
    confirmButtonColor: '#6c757d',
    didOpen: () => {
      // Manejar selección individual
      $('.archivo-checkbox').on('change', function() {
        const checkedBoxes = $('.archivo-checkbox:checked').length;
        $('#eliminarSeleccionados').toggle(checkedBoxes > 0);
      });

      // Manejar seleccionar todos
      $('#selectAll').on('change', function() {
        $('.archivo-checkbox').prop('checked', this.checked);
        $('#eliminarSeleccionados').toggle(this.checked);
      });

      // Manejar eliminación
      $('#eliminarSeleccionados').on('click', function() {
        const archivosSeleccionados = [];
        $('.archivo-checkbox:checked').each(function() {
          const item = $(this).closest('.archivo-item-gestor');
          archivosSeleccionados.push({
            id: item.data('archivo-id'),
            nombre: item.data('archivo-nombre')
          });
        });

        if (archivosSeleccionados.length > 0) {
          confirmarEliminacionMultiple(archivosSeleccionados, idCandidato);
        }
      });
    }
  });
}

function confirmarEliminacionMultiple(archivosSeleccionados, idCandidato) {
  const nombres = archivosSeleccionados.map(a => a.nombre).join('<br>');
  
  Swal.fire({
    title: '¿Eliminar archivos seleccionados?',
    html: `Se eliminarán los siguientes archivos:<br><br><small>${nombres}</small>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      eliminarArchivosSeleccionados(archivosSeleccionados, idCandidato);
    }
  });
}

function eliminarArchivosSeleccionados(archivos, idCandidato) {
  // Mostrar progreso
  Swal.fire({
    title: 'Eliminando archivos...',
    text: 'Por favor espera',
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  // Eliminar archivos uno por uno
  const promesas = archivos.map(archivo => {
    return $.ajax({
      url: './gestionhumana/crudsolicitudesrh.php',
      type: 'POST',
      data: {
        action: 'eliminar_archivo_individual',
        id_archivo: archivo.id,
        nombre_archivo: archivo.nombre
      },
      dataType: 'json'
    });
  });

  Promise.all(promesas)
    .then(results => {
      const exitosos = results.filter(r => r.success).length;
      Swal.fire({
        icon: 'success',
        title: 'Archivos eliminados',
        text: `Se eliminaron ${exitosos} archivo(s) correctamente`,
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        // Recargar expediente
        if (typeof cargarExpedienteCandidato === 'function') {
          cargarExpedienteCandidato(idCandidato);
        } else {
          location.reload();
        }
      });
    })
    .catch(error => {
      Swal.fire('Error', 'Hubo problemas al eliminar algunos archivos', 'error');
    });
}

//==================================================================================================
// ===== INICIALIZACIÓN =====
//==================================================================================================
$(document).ready(function() {
    // Limpiar variables globales y forzar rol RH
    window.ROL_USUARIO = 'RRHH';
    window.CANDIDATOS_INDEX = {};
    
    // *** QUITAR EL EVENT DELEGATION GENÉRICO ***
    // NO configurar eventos aquí, se configurarán cuando se muestre la lista
    
    console.log('Vista RH inicializada - ROL_USUARIO:', window.ROL_USUARIO);
    
    // Resto de tu código existente...
});

// 6. FUNCIÓN DE DEBUG PARA PROBAR
function debugCandidatosRH() {
  console.log('=== DEBUG RH ===');
  console.log('ROL_USUARIO:', window.ROL_USUARIO);
  console.log('CANDIDATOS_INDEX:', window.CANDIDATOS_INDEX);
  console.log('Función seleccionarCandidatorh:', typeof window.seleccionarCandidatorh);
  console.log('Event listeners configurados:', $('.candidato-clickable-rh').length);
}

// Exportar función de debug
window.debugCandidatosRH = debugCandidatosRH;

console.log('=== CORRECCIONES RH APLICADAS ===');
  
  // Verificar dependencias
  if (typeof $ === 'undefined') {
    console.error('jQuery no está cargado');
  }
  
  if (typeof Swal === 'undefined') {
    console.error('SweetAlert2 no está cargado');
  }
  
  // Configurar eventos globales si es necesario
// Evento para ver candidatos enviados
$(document).on('click', '.btnVerCandidatosrh', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const idSolicitud = $(this).data('id');
    console.log('Ver candidatos para solicitud:', idSolicitud);
    
    if (idSolicitud) {
        if (typeof mostrarCandidatosEnviadosrh === 'function') {
            mostrarCandidatosEnviadosrh(idSolicitud);
        } else if (typeof mostrarModalExpedientesrh === 'function') {
            mostrarModalExpedientesrh(idSolicitud);
        } else {
            console.error('Función para mostrar candidatos no encontrada');
            Swal.fire('Error', 'Función no disponible', 'error');
        }
    } else {
        Swal.fire('Error', 'ID de solicitud no encontrado', 'error');
    }
});


// ===== EXPORTAR FUNCIONES PARA USO GLOBAL =====
window.mostrarCandidatosEnviadosrh = mostrarCandidatosEnviadosrh;
window.subirArchivoCandidato = subirArchivoCandidato;
window.seleccionarCandidato = window.seleccionarCandidatorh;
window.mostrarListaCandidatos = mostrarListaCandidatosrh;
window.configurarEventListenersCandidatosRH = configurarEventListenersCandidatosRH;
window.cargarExpedienteCandidato = cargarExpedienteCandidato;
window.mostrarModalExpedientesrh = mostrarModalExpedientesrh;
window.mostrarExpedienteCandidato = mostrarExpedienteCandidato;
window.verArchivosCarpeta = verArchivosCarpeta;
window.debugBotonesCandidatos = debugBotonesCandidatos;
window.generarReportePDF = generarReportePDF;
window.procesarCandidatosAdicionales = procesarCandidatosAdicionales;

// ===== FUNCIONES ADICIONALES QUE FALTABAN =====

// Función para aplicar cambio de estado existente
function aplicarCambioEstado(idCandidato, nuevoEstado, comentario) {
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'POST',
    data: {
      action: 'cambiar_estado_progresivo',
      id_candidato: idCandidato,
      nuevo_estado: nuevoEstado
    },
    dataType: 'json',
    success: function(response) {
      console.log('Respuesta completa del servidor:', response);
      
      // Verificar si hay éxito (incluso con warnings de Oracle)
      if (response && response.success) {
        Swal.fire({
          icon: 'success',
          title: 'Estado actualizado correctamente',
          text: `El candidato cambió a: ${nuevoEstado}`,
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          // *** RECARGAR EL EXPEDIENTE COMPLETO ***
          if (typeof cargarExpedienteCandidato === 'function') {
            cargarExpedienteCandidato(idCandidato);
          } else if (typeof mostrarExpedienteCandidato === 'function') {
            // Obtener información actualizada y recargar
            $.ajax({
              url: './gestionhumana/crudsolicitudesrh.php',
              type: 'GET',
              data: {
                action: 'get_archivos_candidato',
                id_candidato: idCandidato
              },
              dataType: 'json',
              success: function(archivosResponse) {
                if (archivosResponse.success) {
                  // Recargar expediente con información actualizada
                  const infoSolicitud = window.SOLICITUD_ACTUAL || {};
                  mostrarExpedienteCandidato(idCandidato, '', archivosResponse.archivos, infoSolicitud);
                } else {
                  location.reload();
                }
              },
              error: function() {
                location.reload();
              }
            });
          } else {
            location.reload();
          }
        });
      } else {
        Swal.fire('Error', response.error || 'Error al cambiar estado', 'error');
      }
    },
    error: function(xhr, status, error) {
      console.error('Error AJAX:', xhr.responseText);
      
      // Verificar si la respuesta contiene éxito a pesar del error HTTP
      try {
        const responseText = xhr.responseText;
        if (responseText && responseText.includes('"success":true')) {
          // Extraer el JSON válido de la respuesta
          const jsonMatch = responseText.match(/\{"success":true[^}]+\}/);
          if (jsonMatch) {
            const jsonData = JSON.parse(jsonMatch[0]);
            Swal.fire({
              icon: 'success',
              title: 'Estado actualizado correctamente',
              text: `El candidato cambió exitosamente`,
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              location.reload();
            });
            return;
          }
        }
      } catch(e) {
        console.log('No se pudo parsear respuesta en error');
      }
      
      // Si no hay éxito, mostrar error real
      Swal.fire('Error', 'Error de conexión al cambiar estado', 'error');
    }
  });
}

// Función para cargar resultado de aval RH
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
            Swal.close();
            
            if (response.success) {
                mostrarModalResultadoAvalRH(response.data, idSolicitud, tienda, puesto, supervisor, razon);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'No se pudo cargar el resultado del aval'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            
            console.error('Error AJAX:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar al servidor para obtener el resultado'
            });
        }
    });
}

// Función para mostrar modal de resultado de aval RH
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
            <div style="text-align: left; max-height: 600px; overflow-y: auto; padding: 0 10px;">
                
                <!-- INFORMACIÓN DE LA SOLICITUD -->
                <div style="background: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px; border-left: 4px solid #007bff;">
                    <h6 style="color: #2c3e50; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle"></i> Información de la Solicitud
                    </h6>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
                        <div>
                            <span style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-hashtag"></i> ID Solicitud</span><br>
                            <span style="color: #212529; font-size: 14px; font-weight: 500;">#${solicitud.id}</span>
                        </div>
                        <div>
                            <span style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-store"></i> Tienda</span><br>
                            <span style="color: #212529; font-size: 14px; font-weight: 500;">Tienda ${solicitud.tienda}</span>
                        </div>
                        <div>
                            <span style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-briefcase"></i> Puesto Solicitado</span><br>
                            <span style="color: #212529; font-size: 14px; font-weight: 500;">${solicitud.puesto}</span>
                        </div>
                        <div>
                            <span style="font-weight: 600; color: #495057; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fas fa-calendar-alt"></i> Fecha de Solicitud</span><br>
                            <span style="color: #212529; font-size: 14px; font-weight: 500;">${solicitud.fecha_solicitud}</span>
                        </div>
                    </div>
                </div>

                <!-- ESTADO DE APROBACIÓN -->
                <div style="background: ${config.bgColor}; border: 2px solid ${config.borderColor}; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <h6 style="color: ${config.textColor}; margin-bottom: 15px;">
                        <i class="fas fa-gavel"></i> Estado de Aprobación
                    </h6>
                    <div style="text-align: center; margin-bottom: 15px;">
                        <span style="display: inline-block; padding: 8px 20px; border-radius: 25px; font-weight: 600; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; background: ${config.color}; color: white;">${config.estadoBadge}</span>
                    </div>
                    <div style="text-align: center; color: ${config.textColor};">
                        <strong>Revisado por:</strong> ${aval.gerente}<br>
                        <small>Fecha de decisión: ${aval.fecha_decision}</small>
                    </div>
                </div>

                <!-- MOTIVO DE LA DECISIÓN -->
                <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                    <h6 style="color: #856404; margin-bottom: 15px;">
                        <i class="fas fa-comment-alt"></i> ${esAprobado ? 'Comentarios del Gerente' : 'Motivo del Rechazo'}
                    </h6>
                    <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; font-style: italic; line-height: 1.6; color: #856404;">
                        ${aval.comentario || 'Sin comentarios adicionales'}
                    </div>
                </div>

                <div style="text-align: center; color: #6c757d; font-size: 12px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                    <i class="fas fa-clock"></i> Última actualización: ${aval.fecha_decision}
                </div>

            </div>
        `,
        width: '800px',
        showCancelButton: false,
        confirmButtonText: '<i class="fas fa-check"></i> Entendido',
        confirmButtonColor: config.color
    });
}

// Funciones auxiliares adicionales
function iniciarProcesamiento(solicitudId) {
    Swal.fire({
        title: '¿Iniciar procesamiento?',
        text: 'Esto marcará la solicitud como "En Proceso" por RRHH',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, iniciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log("Iniciando procesamiento para solicitud:", solicitudId);
        }
    });
}

function verHistorialCompleto(solicitudId) {
    console.log("Mostrando historial para solicitud:", solicitudId);
    Swal.fire({
        title: 'Historial Completo',
        text: 'Aquí se mostraría el historial completo de la solicitud',
        icon: 'info'
    });
}

function descargarResumen(solicitudId) {
    console.log("Descargando resumen para solicitud:", solicitudId);
    Swal.fire({
        title: 'Generando documento...',
        text: 'Se está preparando el resumen para descarga',
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    });
}

// Función para agregar botón de resultado de aval
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

// Función para validar documentos para aval
function validarDocumentosParaAval(idCandidato, callback) {
    $.ajax({
        url: './gestionhumana/crudsolicitudesrh.php?action=validar_documentos_para_aval',
        type: 'GET',
        dataType: 'json',
        data: { id_candidato: idCandidato },
        success: function(response) {
            if (response.success) {
                callback(response.puede_pasar_a_aval, response.documentos_faltantes);
            } else {
                callback(false, ['Error de validación']);
            }
        },
        error: function() {
            callback(false, ['Error de conexión']);
        }
    });
}

// Función para obtener clase de estado auxiliar
function getEstadoClassrh(estado) {
  const m = {
    'CV Enviado': 'badge-primary',
    'Psicometrica': 'badge-warning', 
    'Entrevista RH': 'badge-success',
    'Entrevista Tecnica': 'badge-info',
    'Dia de Prueba': 'badge-danger',
    'Poligrafo': 'badge-dark',
    'Descartado': 'badge-secondary'
  };
  return m[estado] || 'badge-secondary';
}

// *** FUNCIÓN: Validar antes de permitir cambio ***
/*function validarAntesDePermitirCambio(idCandidato, esJefe, callback) {
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'GET',
    data: {
      action: 'validar_progresion_candidato',
      id_candidato: idCandidato,
      es_jefe: esJefe ? 1 : 0
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        callback(response.puede_avanzar || true, response.mensaje || '');
      } else {
        callback(true, ''); // Si hay error, permitir cambio
      }
    },
    error: function() {
      callback(true, ''); // Si hay error de conexión, permitir cambio
    }
  });
}*/

// *** FUNCIÓN: Mostrar modal de cambio de estado ***
function mostrarModalCambioEstado(idCandidato, esJefe) {
  // Estados disponibles según tu flujo
  let estadosDisponibles = [
    'CV Enviado', 
    'Psicometrica', 
    'Entrevista RH',
    'Entrevista Tecnica', 
    'Dia de Prueba'
  ];
  
  if (esJefe) {
    estadosDisponibles.push('Poligrafo');
  }
  
  estadosDisponibles.push('Aprobado para Aval', 'Descartado');
  
  let opcionesHTML = '<option value="">Seleccione estado...</option>';
  estadosDisponibles.forEach(estado => {
    opcionesHTML += `<option value="${estado}">${estado}</option>`;
  });
  
  Swal.fire({
    title: '<i class="fas fa-exchange-alt"></i> Cambiar Estado',
    html: `
      <div style="text-align: left;">
        <div class="form-group">
          <label for="nuevoEstadoSelect">Nuevo Estado:</label>
          <select id="nuevoEstadoSelect" class="form-control">
            ${opcionesHTML}
          </select>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-check"></i> Cambiar',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    confirmButtonColor: '#28a745',
    width: '400px',
    preConfirm: () => {
      const nuevoEstado = document.getElementById('nuevoEstadoSelect').value;
      
      if (!nuevoEstado) {
        Swal.showValidationMessage('Debe seleccionar un estado');
        return false;
      }
      
      return { nuevoEstado };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      aplicarCambioEstado(idCandidato, result.value.nuevoEstado, ''); // Sin comentario
    }
  });
}

// *** FUNCIÓN: Aplicar cambio de estado ***
function aplicarCambioEstado(idCandidato, nuevoEstado, comentario) {
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'POST',
    data: {
      action: 'cambiar_estado_progresivo',
      id_candidato: idCandidato,
      nuevo_estado: nuevoEstado
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: 'Estado actualizado',
          text: `El candidato cambió a: ${nuevoEstado}`,
          timer: 2000,
          showConfirmButton: false
        }).then(() => {
          // Recargar expediente
          if (typeof cargarExpedienteCandidato === 'function') {
            cargarExpedienteCandidato(idCandidato);
          } else {
            location.reload();
          }
        });
      } else {
        Swal.fire('Error', response.error || 'Error al cambiar estado', 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Error de conexión', 'error');
    }
  });
}

//====================================================================================
//FUNCION PARA AGREGAR CANDIDATOS ADICIONALES 
//====================================================================================

// ✅ FUNCIÓN PRINCIPAL PARA AGREGAR CANDIDATOS
window.agregarCandidatosRH = function(idSolicitud) {
  console.log('🚀 Agregando candidatos a solicitud:', idSolicitud);

  // AGREGAR AL INICIO DE agregarCandidatosRH
  $('.modal').modal('hide');
  $('.modal-backdrop').remove();
  $('body').removeClass('modal-open');
  
  // Obtener información de la solicitud desde la tabla
  const filaSolicitud = $(`tr[data-id="${idSolicitud}"]`);
  let tiendaInfo = 'No disponible';
  let puestoInfo = 'No disponible'; 
  let supervisorInfo = 'No disponible';
  
  if (filaSolicitud.length > 0) {
    // ✅ CORRECCIÓN: Asignar valores a las variables correctas
    tiendaInfo = filaSolicitud.find('td:nth-child(2)').text().trim() || 'No disponible';
    puestoInfo = filaSolicitud.find('td:nth-child(3)').text().trim() || 'No disponible';
    supervisorInfo = filaSolicitud.find('td:nth-child(4)').text().trim() || 'No disponible';
    
    // También guardar en el objeto global si lo necesitas
    infoSolicitud = {
      id: idSolicitud,
      tienda: tiendaInfo,
      puesto: puestoInfo,
      supervisor: supervisorInfo,
      estado: filaSolicitud.find('td:nth-child(9)').text().trim() || 'No disponible',
      nombre_gerente: filaSolicitud.find('td:nth-child(5)').text().trim() || window.SOLICITUD_ACTUAL?.nombre_gerente || 'Gerente de Operaciones'
    };
    
    console.log('Información cargada:', {tiendaInfo, puestoInfo, supervisorInfo});
  } else {
    console.error(' No se encontró la fila con ID:', idSolicitud);
  }

  // PRIMER MODAL: Cantidad de candidatos
  Swal.fire({
    title: '<i class="fas fa-user-plus"></i> Agregar Candidatos',
    html: `
      <div style="text-align: left;">
        <!-- Información de la solicitud -->
        <div class="alert alert-info mb-4">
          <h6><i class="fas fa-info-circle mr-2"></i>Información de la Solicitud</h6>
          <div class="row">
            <div class="col-md-4"><strong>Tienda:</strong> ${tiendaInfo}</div>
            <div class="col-md-4"><strong>Puesto:</strong> ${puestoInfo}</div>
            <div class="col-md-4"><strong>Supervisor:</strong> ${supervisorInfo}</div>
          </div>
        </div>
        
        <!-- Cantidad de candidatos -->
        <div class="form-group">
          <label for="cantidadCandidatos" class="font-weight-bold">
            <i class="fas fa-users mr-1"></i>¿Cuántos candidatos desea agregar?
          </label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
            </div>
            <input type="number" 
                   id="cantidadCandidatos" 
                   class="form-control" 
                   min="1" 
                   max="50" 
                   value="1"
                   placeholder="Ingrese la cantidad"
                   style="font-size: 16px; text-align: center;">
            <div class="input-group-append">
              <span class="input-group-text">candidatos</span>
            </div>
          </div>
          <small class="text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Ingrese cualquier cantidad entre 1 y 10 candidatos
          </small>
        </div>
      </div>
    `,
    width: 600,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-arrow-right mr-1"></i>Continuar',
    cancelButtonText: '<i class="fas fa-times mr-1"></i>Cancelar',
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    preConfirm: () => {
      // ✅ VALIDACIÓN CORREGIDA
      const input = Swal.getPopup().querySelector('#cantidadCandidatos');
      const valor = input ? input.value.trim() : '';
      const cantidad = parseInt(valor);
      
      console.log('📋 Input encontrado:', !!input);
      console.log('📋 Valor del input:', valor);
      console.log('📋 Cantidad parseada:', cantidad);
      
      // Validaciones
      if (!valor || valor === '') {
        Swal.showValidationMessage('Debe ingresar una cantidad');
        return false;
      }
      
      if (isNaN(cantidad) || cantidad < 1) {
        Swal.showValidationMessage('Debe ingresar un número válido mayor a 0');
        return false;
      }
      
      if (cantidad > 50) {
        Swal.showValidationMessage('La cantidad máxima es 50 candidatos');
        return false;
      }
      
      return cantidad; // ✅ Retornar la cantidad validada
    }
  }).then((result) => {
    console.log('🎯 Resultado del primer modal:', result);
    
    if (result.isConfirmed && result.value) {
      const cantidad = result.value;
      console.log('✅ Cantidad confirmada:', cantidad);
      
      // ✅ Mostrar segundo modal después de una breve pausa
      setTimeout(() => {
        mostrarModalDatosCandidatos(idSolicitud, cantidad);
      }, 300);
    }
  });
};


// 🔧 FUNCIÓN PARA EL SEGUNDO MODAL - DATOS DE CANDIDATOS
function mostrarModalDatosCandidatos(idSolicitud, cantidad) {
  console.log('📝 Mostrando modal de datos para', cantidad, 'candidatos');
  
  // Generar formularios HTML
  let formulariosHTML = '';
  for (let i = 1; i <= cantidad; i++) {
    formulariosHTML += `
      <div class="card mb-3" style="border: 2px solid #28a745; border-radius: 10px;">
        <div class="card-header bg-gradient-success text-white">
          <h6 class="mb-0">
            <i class="fas fa-user mr-2"></i>Candidato ${i} de ${cantidad}
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label font-weight-bold">
                  <i class="fas fa-user mr-1"></i>Nombre *
                </label>
                <input type="text" 
                       id="nombre_${i}" 
                       class="form-control candidato-input" 
                       placeholder="Ingrese el nombre"
                       data-candidato="${i}"
                       style="border: 2px solid #ddd; padding: 10px;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label font-weight-bold">
                  <i class="fas fa-user-tag mr-1"></i>Apellidos *
                </label>
                <input type="text" 
                       id="apellidos_${i}" 
                       class="form-control candidato-input" 
                       placeholder="Ingrese los apellidos"
                       data-candidato="${i}"
                       style="border: 2px solid #ddd; padding: 10px;">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label font-weight-bold">
              <i class="fas fa-id-card mr-1"></i>DPI (opcional)
            </label>
            <input type="text" 
                   id="documento_${i}" 
                   class="form-control candidato-input" 
                   placeholder="Número de DPI (opcional)"
                   data-candidato="${i}"
                   style="border: 2px solid #ddd; padding: 10px;">
          </div>
        </div>
      </div>
    `;
  }

  // ✅ SEGUNDO MODAL CON DATOS CORREGIDOS
    Swal.fire({
      title: `<i class="fas fa-users text-success"></i> Registro de ${cantidad} Candidato${cantidad > 1 ? 's' : ''}`,
      html: `
        <div style="text-align: left; max-height: 70vh; overflow-y: auto; padding: 10px;">
          <div class="alert alert-success mb-4" style="border-radius: 10px; border-left: 5px solid #28a745;">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Complete la información de cada candidato</strong>
            <br><small class="text-muted">Los campos marcados con (*) son obligatorios</small>
          </div>
          ${formulariosHTML}
        </div>
      `,
      width: cantidad > 2 ? '95%' : '900px',
      showCancelButton: true,
      confirmButtonText: '<i class="fas fa-save mr-2"></i>Guardar Candidatos',
      cancelButtonText: '<i class="fas fa-arrow-left mr-2"></i>Volver',
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      buttonsStyling: true,
      backdrop: true,
      allowOutsideClick: false,
      focusConfirm: false,
      preConfirm: () => {
        const candidatos = [];
        let hayErrores = false;
        
        for (let i = 1; i <= cantidad; i++) {
          const popup = Swal.getPopup();
          const nombre = popup.querySelector(`#nombre_${i}`).value.trim();
          const apellidos = popup.querySelector(`#apellidos_${i}`).value.trim();
          const documento = popup.querySelector(`#documento_${i}`).value.trim();
          
          if (!nombre || !apellidos) {
            Swal.showValidationMessage(`Complete nombre y apellidos del candidato ${i}`);
            hayErrores = true;
            break;
          }
          
          candidatos.push({
            nombre: nombre,
            apellidos: apellidos,
            documento: documento || 'No proporcionado'
          });
        }
        
        if (hayErrores) {
          return false;
        }
        
        return candidatos;
      },
      didOpen: () => {
        // Remover eventos problemáticos de Bootstrap
        $(document).off('focusin.modal');
        
        setTimeout(() => {
          const popup = Swal.getPopup();
          const inputs = popup.querySelectorAll('input');
          
          inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.removeAttribute('disabled');
          });
          
          // Focus simple
          const primerInput = popup.querySelector('#nombre_1');
          if (primerInput) {
            primerInput.focus();
          }
        }, 500);
      }
    }).then((result) => {
      if (result.isConfirmed) {
        guardarCandidatos(idSolicitud, result.value);
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // Volver al primer modal
        agregarCandidatosRH(idSolicitud);
      }
    }).then((result) => {
    console.log('🎯 Resultado del segundo modal:', result);
    
    if (result.isConfirmed && result.value) {
      console.log('✅ Guardando candidatos:', result.value);
      guardarCandidatosModal(idSolicitud, result.value);
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      console.log('🔄 Volviendo al primer modal');
      // Volver al primer modal
      setTimeout(() => {
        agregarCandidatosRH(idSolicitud);
      }, 100);
    }
  });
}
//GUARDAR CANDIDATO ADICIONAL 
function guardarCandidatos(idSolicitud, candidatos) {
  console.log('💾 Guardando candidatos:', candidatos);
  
  // Remover modal anterior si existe
  $('#modalLoadingCandidatos').remove();
  
  // Mostrar loading
  const loadingHTML = `
    <div class="modal fade" id="modalLoadingCandidatos" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
              <span class="sr-only">Guardando...</span>
            </div>
            <h5 class="mt-3">Guardando candidatos...</h5>
            <p class="text-muted">Por favor espere</p>
          </div>
        </div>
      </div>
    </div>
  `;
  
  $('body').append(loadingHTML);
  $('#modalLoadingCandidatos').modal('show');
  
  // Enviar al servidor
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'POST',
    data: {
      action: 'agregar_candidatos_solicitud',
      id_solicitud: idSolicitud,
      candidatos: JSON.stringify(candidatos)
    },
    dataType: 'json',
    success: function(response) {
      $('#modalLoadingCandidatos').modal('hide');
      $('#modalLoadingCandidatos').remove(); // ← NUEVA LÍNEA
      
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: '¡Candidatos agregados!',
          text: `Se agregaron ${candidatos.length} candidato${candidatos.length > 1 ? 's' : ''} exitosamente`,
          confirmButtonText: 'Entendido'
        }).then(() => {
          // Recargar la lista de candidatos si existe la función
          if (typeof mostrarCandidatosEnviadosrh === 'function') {
            setTimeout(() => {
              mostrarCandidatosEnviadosrh(idSolicitud);
            }, 300);
          }
        });
      } else {
        Swal.fire('Error', response.error || 'Error desconocido al guardar candidatos', 'error');
      }
    },
    error: function() {
      $('#modalLoadingCandidatos').modal('hide');
      $('#modalLoadingCandidatos').remove(); // ← NUEVA LÍNEA
      Swal.fire('Error', 'Error de conexión al guardar candidatos', 'error');
    }
  });
}

// ✅ CONFIGURAR EVENTO DEL BOTÓN AL CARGAR LA PÁGINA
$(document).ready(function() {
  // Event listener para el botón agregar candidatos
  $(document).on('click', '#btnCargarCandidatos, .btn-agregar-candidatos', function(e) {
    e.preventDefault();
    
    // Obtener ID de solicitud desde el modal o elemento padre
    let idSolicitud = $('#modalExpedientes').data('id-solicitud');
    
    if (!idSolicitud) {
      // Intentar obtener desde window si está disponible
      idSolicitud = window.solicitudIdTemporal;
    }
    
    if (!idSolicitud) {
      Swal.fire('Error', 'No se pudo identificar la solicitud', 'error');
      return;
    }
    
    agregarCandidatosRH(idSolicitud);
  });
  
  console.log('✅ Event listener para agregar candidatos configurado');
});

//====================FIN DE AGREGAR CANDIDATOS ADICIONALES===========================

//====================================================================================
//  RESULTADO DE AVAL RH
//====================================================================================

//===================================================================================
// FUNCIÓN PARA MOSTRAR RESULTADO DE AVAL PROCESADO - RH
//===================================================================================
function mostrarResultadoAvalProcesadoRH(candidato) {
    console.log('🎯 RH - Mostrando resultado aval procesado:', candidato);
    
    function obtenerInformacionAvalCompleta(idCandidato) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: './gestionhumana/crudsolicitudesrh.php?action=get_info_aval_completa_rh',
                type: 'GET',
                data: { id_candidato: idCandidato },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        resolve(response.candidato);
                    } else {
                        reject(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    obtenerInformacionAvalCompleta(candidato.ID_CANDIDATO)
        .then(candidatoCompleto => {
            const candidatoInfo = { ...candidato, ...candidatoCompleto };
            
            const esAprobado = candidatoInfo.APROBACION === 'Y';
            const decision = esAprobado ? 'APROBADO PARA CONTRATACION' : 'RECHAZADO';
            const colorFondo = esAprobado ? 'bg-success' : 'bg-danger';
            const colorBorde = esAprobado ? 'border-success' : 'border-danger';
            const icono = esAprobado ? 'fa-check-circle' : 'fa-times-circle';
            const nombreGerente = candidatoInfo.NOMBRE_GERENTE || 'Gerente de Operaciones';
            
            const expedienteHtml = `
                <div class="card border-0 shadow-lg">
                    <div class="card-header ${colorFondo} text-white">
                        <h4 class="mb-0">
                            <i class="fas ${icono} mr-2"></i>${decision}
                        </h4>
                        <p class="mb-0 mt-2">
                            <i class="fas fa-user-tie mr-2"></i>
                            Procesado por: <strong>${nombreGerente}</strong>
                        </p>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card ${colorBorde}">
                                    <div class="card-header ${colorFondo} text-white">
                                        <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Información del Candidato</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Nombre:</strong> ${candidatoInfo.NOMBRE_CANDIDATO} ${candidatoInfo.APELLIDOS_CANDIDATO}</p>
                                        <p><strong>Documento:</strong> ${candidatoInfo.DOCUMENTO_CANDIDATO || 'No registrado'}</p>
                                        <p><strong>Estado:</strong> <span class="badge ${colorFondo}">${candidatoInfo.ESTADO_CANDIDATO}</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-building mr-2"></i>Información de la Solicitud</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Tienda:</strong> ${candidatoInfo.NUM_TIENDA}</p>
                                        <p><strong>Puesto:</strong> ${candidatoInfo.PUESTO_SOLICITADO}</p>
                                        <p><strong>Supervisor:</strong> ${candidatoInfo.SUPERVISOR}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-${esAprobado ? 'success' : 'warning'} border-${esAprobado ? 'success' : 'warning'}">
                            <h6 class="font-weight-bold">
                                <i class="fas fa-comment-dots mr-2"></i>
                                ${esAprobado ? 'Comentarios de Aprobación' : 'Motivo del Rechazo'}:
                            </h6>
                            <p class="mb-2" style="white-space: pre-wrap;">${candidatoInfo.MOTIVO_DECISION || 'Sin comentarios'}</p>
                            <hr>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-2"></i>
                                Fecha de decisión: <strong>${candidatoInfo.FECHA_DECISION_FORMATEADA || 'No disponible'}</strong>
                            </small>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-clipboard-list mr-2"></i>Estados Completados antes del Aval</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- CV Enviado -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_CV > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-file-alt fa-3x mb-2 ${candidatoInfo.ARCHIVOS_CV > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">CV Enviado</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_CV > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_CV > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_CV})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_CV > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'CV Enviado')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Psicométrica -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-brain fa-3x mb-2 ${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Psicométrica</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_PSICOMETRICA})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Psicometrica')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Entrevista RH -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-comments fa-3x mb-2 ${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Entrevista RH</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_ENTREVISTA_RH})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Entrevista RH')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Entrevista Técnica -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-laptop-code fa-3x mb-2 ${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Entrevista Técnica</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Entrevista Tecnica')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Día de Prueba -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-clipboard-check fa-3x mb-2 ${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Día de Prueba</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_DIA_PRUEBA})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Dia de Prueba')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Polígrafo -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-shield-alt fa-3x mb-2 ${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Polígrafo</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_POLIGRAFO})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Poligrafo')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                        <!-- ✅ NUEVA SECCIÓN: ACCIONES DE RH -->
                        ${(() => {
                            // Mostrar botón SOLO si el candidato fue APROBADO
                            if (esAprobado && candidatoInfo.ESTADO_CANDIDATO !== 'Contratado') {
                                return `
                                    <div class="mt-4 pt-3 border-top">
                                        <h6 class="mb-3"><i class="fas fa-cog mr-2"></i>Acciones de Recursos Humanos</h6>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button class="btn btn-success btn-lg btn-block" onclick="marcarCandidatoContratado(${candidatoInfo.ID_CANDIDATO}, '${candidatoInfo.NOMBRE_CANDIDATO} ${candidatoInfo.APELLIDOS_CANDIDATO}')">
                                                    <i class="fas fa-check-circle mr-2"></i>Marcar como Contratado
                                                </button>
                                                <small class="text-muted d-block mt-2 text-center">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Al marcar como contratado, la plaza se considerará cubierta
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            // Si ya está contratado, mostrar mensaje
                            if (candidatoInfo.ESTADO_CANDIDATO === 'Contratado') {
                                return `
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="alert alert-success text-center mb-0">
                                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                                            <h5 class="mb-0">CANDIDATO CONTRATADO</h5>
                                            <p class="mb-0 mt-2">La plaza ha sido cubierta exitosamente</p>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            // Si fue rechazado, no mostrar nada
                            return '';
                        })()}
                        
                    </div>
                </div>

            `;
            
            $('#expedienteCandidato').html(expedienteHtml);
        })
        .catch(error => {
            console.error('Error:', error);
            $('#expedienteCandidato').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error:</strong> ${error}
                </div>
            `);
        });
}


    // ================================================================================================
    // FUNCIÓN: VER ARCHIVOS DE CANDIDATO EN REACTIVACIÓN
    // ================================================================================================
window.verArchivosCandidatoRH = function(idCandidato, nombreCandidato, idSolicitud) {
  console.log('📂 Cargando archivos del candidato:', idCandidato);
  
  // ✅ GUARDAR REFERENCIA DEL ID DE SOLICITUD GLOBALMENTE
  window.ID_SOLICITUD_REACTIVACION = idSolicitud;
  
  // Mostrar loading
  Swal.fire({
    title: 'Cargando archivos...',
    html: '<i class="fas fa-spinner fa-spin fa-2x"></i>',
    allowOutsideClick: false,
    showConfirmButton: false
  });
  
  // Obtener archivos del candidato
  $.ajax({
    url: './gestionhumana/crudsolicitudesrh.php',
    type: 'GET',
    data: {
      action: 'get_archivos_candidato',
      id_candidato: idCandidato
    },
    dataType: 'json',
    success: function(response) {
      Swal.close();
      
      console.log('📦 Respuesta archivos:', response);
      
      if (response.success && response.archivos && response.archivos.length > 0) {
        mostrarModalArchivosReactivacion(idCandidato, nombreCandidato, response.archivos, idSolicitud);
      } else {
        Swal.fire({
          icon: 'info',
          title: 'Sin archivos',
          text: 'No se encontraron archivos para este candidato',
          confirmButtonText: 'Volver'
        }).then(() => {
          // Reabrir modal de reactivación
          setTimeout(() => {
            $('#modalReactivacionRH').modal('show');
          }, 300);
        });
      }
    },
    error: function(xhr, status, error) {
      Swal.close();
      console.error('❌ Error al cargar archivos:', error);
      console.error('Response:', xhr.responseText);
      
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al cargar los archivos del candidato',
        confirmButtonText: 'Volver'
      }).then(() => {
        setTimeout(() => {
          $('#modalReactivacionRH').modal('show');
        }, 300);
      });
    }
  });
};
    // ================================================================================================
    // FUNCIÓN: MOSTRAR MODAL CON ARCHIVOS DEL CANDIDATO
    // ================================================================================================
function mostrarModalArchivosReactivacion(idCandidato, nombreCandidato, archivos, idSolicitud) {
  console.log('🗂️ Mostrando modal de archivos para:', nombreCandidato);
  console.log('📁 Total archivos:', archivos.length);
  
  let htmlArchivos = '';
  
  if (archivos.length === 0) {
    htmlArchivos = `
      <div class="alert alert-warning text-center">
        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
        <p class="mb-0">No hay archivos disponibles para este candidato</p>
      </div>
    `;
  } else {
    htmlArchivos = '<div class="list-group">';
    
    archivos.forEach(archivo => {
      const icono = archivo.TIPO_ARCHIVO === 'DPI' ? 'fa-id-card' : 
                   archivo.TIPO_ARCHIVO === 'CV' ? 'fa-file-alt' : 
                   archivo.TIPO_ARCHIVO === 'ANTECEDENTES' ? 'fa-file-pdf' : 
                   'fa-file';
      
      const urlVer = archivo.URL_VER || '#';
      const tieneUrl = archivo.URL_VER && archivo.URL_VER !== '#';
      
      htmlArchivos += `
        <div class="list-group-item">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-1">
                <i class="fas ${icono} text-primary mr-2"></i>
                ${archivo.TIPO_ARCHIVO}
              </h6>
              <small class="text-muted">
                <i class="fas fa-calendar mr-1"></i>${archivo.FECHA_SUBIDA || 'Sin fecha'}
              </small>
            </div>
            ${tieneUrl ? `
              <a href="${urlVer}" 
                 target="_blank" 
                 class="btn btn-sm btn-primary">
                <i class="fas fa-eye mr-1"></i>Ver Archivo
              </a>
            ` : `
              <button class="btn btn-sm btn-secondary" disabled>
                <i class="fas fa-eye-slash mr-1"></i>No disponible
              </button>
            `}
          </div>
        </div>
      `;
    });
    
    htmlArchivos += '</div>';
  }
  
  const htmlModal = `
    <div class="modal fade" id="modalArchivosReactivacion" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title">
              <i class="fas fa-folder-open mr-2"></i>
              Archivos de ${nombreCandidato}
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
            ${htmlArchivos}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-arrow-left mr-2"></i>Volver a Reactivación
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
  
  // Remover modal anterior si existe
  $('#modalArchivosReactivacion').remove();
  
  // Agregar nuevo modal al DOM
  $('body').append(htmlModal);
  
  // ✅ CERRAR EL MODAL DE REACTIVACIÓN PRIMERO
  $('#modalReactivacionRH').modal('hide');
  
  // ✅ ESPERAR Y MOSTRAR MODAL DE ARCHIVOS
  setTimeout(() => {
    $('#modalArchivosReactivacion').modal('show');
    console.log('✅ Modal de archivos mostrado');
  }, 400);
  
  // ✅ AL CERRAR MODAL DE ARCHIVOS, RECREAR EL MODAL DE REACTIVACIÓN
  $('#modalArchivosReactivacion').on('hidden.bs.modal', function() {
    console.log('🔙 Cerrando modal de archivos...');
    
    // Remover el modal de archivos
    $('#modalArchivosReactivacion').remove();
    
    // ✅ RECREAR COMPLETAMENTE EL MODAL DE REACTIVACIÓN
    setTimeout(() => {
      // Verificar si tenemos el contexto guardado
      if (window.CONTEXTO_REACTIVACION_RH) {
        console.log('✅ Recreando modal de reactivación con contexto guardado...');
        
        const ctx = window.CONTEXTO_REACTIVACION_RH;
        
        // ✅ FORZAR CIERRE COMPLETO DEL MODAL ANTERIOR
        $('#modalReactivacionRH').modal('hide');
        $('#modalReactivacionRH').remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        
        // Esperar un poco y recrear
        setTimeout(() => {
          mostrarPanelReactivacionRH(ctx.idSolicitud, ctx.solicitud, ctx.candidatos);
          console.log('✅ Modal de reactivación recreado y abierto');
        }, 200);
        
      } else {
        console.error('❌ No hay contexto guardado para recrear el modal');
        
        // Si no hay contexto, intentar abrir el modal existente
        if ($('#modalReactivacionRH').length > 0) {
          const modalElement = document.getElementById('modalReactivacionRH');
          if (modalElement) {
              // Intenta Bootstrap 5 primero
              if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                  const modalInstance = new bootstrap.Modal(modalElement);
                  modalInstance.show();
              } 
              // Si no, intenta Bootstrap 4 con jQuery
              else if (typeof $.fn.modal !== 'undefined') {
                  $(modalElement).modal('show');
              } 
              // Último recurso: mostrar manualmente
              else {
                  $(modalElement).addClass('show').css('display', 'block');
                  $('body').addClass('modal-open').append('<div class="modal-backdrop fade show"></div>');
              }
          }
        } else {
          Swal.fire({
            icon: 'info',
            title: 'Recargar vista',
            text: 'Por favor, vuelva a abrir el panel de reactivación',
            confirmButtonText: 'OK'
          });
        }
      }
    }, 300);
  });
}

//====================================================================================
//  FIN DE FUNCION DE RESULTADO DE AVAL RH
//====================================================================================

// 7. AGREGAR AL FINAL
window.mostrarResultadoAvalProcesadoRH = mostrarResultadoAvalProcesadoRH;
window.cargarOpcionesFiltrosHistorialrh = cargarOpcionesFiltrosHistorialrh;
window.mostrarHistorialProceso=mostrarHistorialProceso;
window.exportarHistorialPDF=exportarHistorialPDF;
window.exportarHistorialExcel=exportarHistorialExcel;
window.generarCardSolicitud=generarCardSolicitud
window.generarSeccionCandidatos=generarSeccionCandidatos;

window.verArchivo = verArchivo;
window.descargarArchivo = descargarArchivo;
//window.eliminarArchivosEstado = eliminarArchivosEstado;
window.eliminarArchivoIndividual = eliminarArchivoIndividual;
window.validarAntesDePermitirCambio = validarAntesDePermitirCambio;
window.mostrarModalCambioEstado = mostrarModalCambioEstado;
window.aplicarCambioEstado = aplicarCambioEstado;
window.actualizarBadgesSilenciosamenteRH = actualizarBadgesSilenciosamenteRH;
// AGREGAR a las exportaciones:
window.abrirGestorArchivos = abrirGestorArchivos;
window.mostrarGestorArchivos = mostrarGestorArchivos;
window.confirmarEliminacionMultiple = confirmarEliminacionMultiple;
window.eliminarArchivosSeleccionados = eliminarArchivosSeleccionados;

// Exportar funciones adicionales
window.aplicarCambioEstadoExistente = aplicarCambioEstadoExistente;
window.cargarResultadoAvalRH = cargarResultadoAvalRH;
window.mostrarModalResultadoAvalRH = mostrarModalResultadoAvalRH;
window.iniciarProcesamiento = iniciarProcesamiento;
window.verHistorialCompleto = verHistorialCompleto;
window.descargarResumen = descargarResumen;
window.agregarBotonResultadoAval = agregarBotonResultadoAval;
window.validarDocumentosParaAval = validarDocumentosParaAval;
window.getEstadoClassrh = getEstadoClassrh;