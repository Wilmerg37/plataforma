        // Evento para abrir modal de archivos con selección
    $(document).off('click', '.btnVerArchivos').on('click', '.btnVerArchivos', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const idSolicitud = $(this).data('id');
        solicitudActual = idSolicitud;
        console.log("📁 Abriendo archivos para solicitud:", idSolicitud);
        
        if (!idSolicitud) {
            Swal.fire('Error', 'ID de solicitud no encontrado', 'error');
            return;
        }
        
        cargarArchivosConSeleccion(idSolicitud);
    });
    
    // Evento para revisar CVs disponibles
    $(document).off('click', '.btnRevisarCVs').on('click', '.btnRevisarCVs', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const id = $(this).data('id');
        
        Swal.fire({
            icon: 'info',
            title: 'CVs Disponibles para Revisión',
            html: `
                <div style="text-align: center; padding: 20px;">
                    <i class="fas fa-file-alt" style="font-size: 3rem; color: #17a2b8; margin-bottom: 15px;"></i>
                    <h4>Recursos Humanos ha subido CVs</h4>
                    <p>Los CVs están disponibles para tu revisión. Puedes acceder a ellos desde la sección de archivos.</p>
                    <div style="background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 15px 0;">
                        <strong>Solicitud #${id}</strong><br>
                        <small>Estado: Con CVs Disponibles</small>
                    </div>
                </div>
            `,
            confirmButtonText: '<i class="fas fa-eye"></i> Ver Archivos',
            showCancelButton: true,
            cancelButtonText: '<i class="fas fa-times"></i> Cerrar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Trigger the view files function
                $('.btnVerArchivos[data-id="' + id + '"]').click();
            }
        });
    });
    
    function cargarArchivosConSeleccion(idSolicitud) {
        Swal.fire({
            title: 'Cargando archivos...',
            text: 'Obteniendo archivos para revisión y selección',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        $.ajax({
            url: './supervision/crudsolicitudes_fixed.php?action=get_archivos',
            type: 'GET',
            dataType: 'json',
            data: { id: idSolicitud },
            success: function(response) {
                console.log("📄 Archivos recibidos:", response);
                
                if (response.success && response.archivos) {
                    archivosOriginales = response.archivos;
                    archivosSeleccionados = [];
                    mostrarModalSeleccionArchivos(response.archivos, idSolicitud);
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Sin archivos disponibles',
                        text: 'No se encontraron archivos para esta solicitud',
                        confirmButtonText: 'Entendido'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al cargar archivos:', xhr.responseText);
                Swal.fire('Error', 'Error al cargar archivos: ' + error, 'error');
            }
        });
    }
    
    function mostrarModalSeleccionArchivos(archivos, idSolicitud) {
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
                    <div class="archivo-item" data-id="${archivo.ID_ARCHIVO}" style="
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
                       onmouseout="this.style.borderColor='#e9ecef'; this.style.boxShadow='none'"
                       onclick="toggleCheckbox('${archivo.ID_ARCHIVO}')">
                        
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
                                   "
                                   onclick="event.stopPropagation();">
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
                                    "
                                    onclick="event.stopPropagation()"
                                    onmouseover="this.style.background='#0056b3'; this.style.transform='translateY(-1px)'"
                                    onmouseout="this.style.background='#007bff'; this.style.transform='translateY(0)'">
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
                                    "
                                    onclick="event.stopPropagation()"
                                    onmouseover="this.style.background='#1e7e34'; this.style.transform='translateY(-1px)'"
                                    onmouseout="this.style.background='#28a745'; this.style.transform='translateY(0)'">
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
                
                // Agregar función global para toggle
                window.toggleCheckbox = function(archivoId) {
                    const checkbox = $(`.cv-checkbox[data-archivo-id="${archivoId}"]`);
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                };
            }
        });
    }
    
    function configurarEventosSeleccion() {
        // Evento para checkboxes individuales
        $(document).off('change', '.cv-checkbox').on('change', '.cv-checkbox', function(e) {
            e.stopPropagation();
            const archivoId = $(this).data('archivo-id');
            const isChecked = $(this).is(':checked');
            const archivoItem = $(this).closest('.archivo-item');
            
            if (isChecked) {
                archivoItem.addClass('seleccionado');
                if (!archivosSeleccionados.includes(archivoId)) {
                    archivosSeleccionados.push(archivoId);
                }
            } else {
                archivoItem.removeClass('seleccionado');
                archivosSeleccionados = archivosSeleccionados.filter(id => id !== archivoId);
            }
            
            actualizarContadorYBotones();
        });
        
        // Evento para seleccionar todos
        $(document).off('click', '#btn-seleccionar-todos').on('click', '#btn-seleccionar-todos', function(e) {
            e.stopPropagation();
            $('.cv-checkbox').prop('checked', true).trigger('change');
        });
        
        // Evento para limpiar selección
        $(document).off('click', '#btn-limpiar-seleccion').on('click', '#btn-limpiar-seleccion', function(e) {
            e.stopPropagation();
            $('.cv-checkbox').prop('checked', false).trigger('change');
        });
        
        // Evento para confirmar selección
        $(document).off('click', '#btn-confirmar-seleccion').on('click', '#btn-confirmar-seleccion', function(e) {
            e.stopPropagation();
            if (archivosSeleccionados.length === 0) {
                Swal.fire('Atención', 'Debes seleccionar al menos un CV', 'warning');
                return;
            }
            
            confirmarSeleccion();
        });
        
        // Evento para enviar a RRHH
        $(document).off('click', '#btn-enviar-rrhh').on('click', '#btn-enviar-rrhh', function(e) {
            e.stopPropagation();
            enviarSeleccionARRHH();
        });
        
        // Eventos para ver y descargar archivos
        $(document).off('click', '.btn-ver-archivo').on('click', '.btn-ver-archivo', function(e) {
            e.stopPropagation();
            const rutaArchivo = $(this).data('archivo');
            window.open(rutaArchivo, '_blank');
        });
        
        $(document).off('click', '.btn-descargar-archivo').on('click', '.btn-descargar-archivo', function(e) {
            e.stopPropagation();
            const rutaArchivo = $(this).data('archivo');
            const link = document.createElement('a');
            link.href = rutaArchivo;
            link.download = rutaArchivo.split('/').pop();
            link.click();
        });
    }
    
    function actualizarContadorYBotones() {
        const contador = archivosSeleccionados.length;
        $('#numero-seleccionados').text(contador);
        
        const btnConfirmar = $('#btn-confirmar-seleccion');
        if (contador > 0) {
            btnConfirmar.prop('disabled', false).css('opacity', '1');
            btnConfirmar.off('mouseover mouseout').hover(
                function() { 
                    $(this).css({
                        'transform': 'translateY(-2px)',
                        'box-shadow': '0 6px 20px rgba(40, 167, 69, 0.4)'
                    });
                },
                function() { 
                    $(this).css({
                        'transform': 'translateY(0)',
                        'box-shadow': 'none'
                    });
                }
            );
        } else {
            btnConfirmar.prop('disabled', true).css('opacity', '0.5').off('mouseover mouseout');
        }
    }
    
    function confirmarSeleccion() {
        // Filtrar solo archivos seleccionados
        const archivosConfirmados = archivosOriginales.filter(archivo => 
            archivosSeleccionados.includes(archivo.ID_ARCHIVO)
        );
        
        Swal.fire({
            title: '¿Confirmar selección de CVs?',
            html: `
                <div style="text-align: left; margin: 20px 0;">
                    <div style="
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        padding: 15px;
                        border-radius: 8px;
                        margin-bottom: 20px;
                    ">
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
                                <li style="
                                    padding: 10px 15px; 
                                    margin: 5px 0; 
                                    background: #f8f9fa; 
                                    border-radius: 8px;
                                    border-left: 4px solid #28a745;
                                    display: flex;
                                    align-items: center;
                                ">
                                    <i class="fas fa-file-pdf" style="color: #dc3545; margin-right: 10px; font-size: 16px;"></i>
                                    <span style="font-weight: 500;">${archivo.NOMBRE_SOLO}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                    
                    <div style="
                        background: #e3f2fd;
                        border: 1px solid #bbdefb;
                        border-radius: 8px;
                        padding: 12px;
                        margin-top: 15px;
                    ">
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
                // Ocultar archivos no seleccionados con animación
                $('.archivo-item').each(function() {
                    const archivoId = $(this).data('id');
                    if (!archivosSeleccionados.includes(archivoId)) {
                        $(this).fadeOut(400);
                    } else {
                        // Resaltar archivos seleccionados
                        $(this).css({
                            'border-color': '#28a745',
                            'background': 'linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%)'
                        });
                    }
                });
                
                // Actualizar controles
                $('#btn-confirmar-seleccion').fadeOut(300, function() {
                    $('#btn-enviar-rrhh').fadeIn(300).prop('disabled', false).css('opacity', '1');
                    $('#btn-enviar-rrhh').off('mouseover mouseout').hover(
                        function() { 
                            $(this).css({
                                'transform': 'translateY(-2px)',
                                'box-shadow': '0 6px 20px rgba(102, 126, 234, 0.4)'
                            });
                        },
                        function() { 
                            $(this).css({
                                'transform': 'translateY(0)',
                                'box-shadow': 'none'
                            });
                        }
                    );
                });
                
                // Actualizar mensaje
                $('#contador-seleccionados').html(`
                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                    <span style="color: #28a745; font-weight: 600;">
                        ${archivosConfirmados.length} CVs confirmados para envío
                    </span>
                `);
                
                // Deshabilitar controles de selección
                $('#btn-seleccionar-todos, #btn-limpiar-seleccion').prop('disabled', true).css('opacity', '0.5');
                $('.cv-checkbox').prop('disabled', true);
                
                Swal.fire({
                    title: '¡Selección confirmada!',
                    html: `
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745; margin-bottom: 15px;"></i>
                            <p style="margin: 0; color: #666; font-size: 16px;">
                                Ahora puedes enviar la selección a RRHH para continuar con el proceso
                            </p>
                        </div>
                    `,
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        });
    }
    
    function enviarSeleccionARRHH() {
        const archivosParaEnvio = archivosOriginales.filter(archivo => 
            archivosSeleccionados.includes(archivo.ID_ARCHIVO)
        );
        
        Swal.fire({
            title: '¿Enviar selección a RRHH?',
            html: `
                <div style="text-align: left; margin: 20px 0;">
                    <div style="
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        padding: 15px;
                        border-radius: 8px;
                        margin-bottom: 20px;
                    ">
                        <h6 style="margin: 0 0 8px 0; font-weight: 600;">
                            <i class="fas fa-paper-plane"></i> Envío a Recursos Humanos
                        </h6>
                        <p style="margin: 0; opacity: 0.9;">
                            Se enviará la selección de <strong>${archivosParaEnvio.length} CVs</strong> a RRHH
                        </p>
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                            <i class="fas fa-comment"></i> Comentario para RRHH (opcional):
                        </label>
                        <textarea id="comentario-envio" 
                                  placeholder="Ej: Estos candidatos cumplen con el perfil requerido para la posición. Recomiendo priorizar los primeros dos CVs por su experiencia..."
                                  style="
                                      width: 100%;
                                      height: 100px;
                                      padding: 12px;
                                      border: 1px solid #ddd;
                                      border-radius: 8px;
                                      font-family: inherit;
                                      resize: vertical;
                                      font-size: 14px;
                                  "></textarea>
                    </div>
                    
                    <div style="
                        background: #fff3cd;
                        border: 1px solid #ffeaa7;
                        border-radius: 8px;
                        padding: 12px;
                        margin-top: 15px;
                    ">
                        <p style="margin: 0; color: #856404; font-size: 14px;">
                            <i class="fas fa-exclamation-triangle" style="margin-right: 6px;"></i>
                            <strong>Importante:</strong> Esta acción no se puede deshacer. Los CVs seleccionados serán enviados a RRHH para continuar con el proceso.
                        </p>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-paper-plane"></i> Enviar a RRHH',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#6c757d',
            width: '650px',
            preConfirm: () => {
                return $('#comentario-envio').val().trim();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const comentario = result.value || '';
                
                Swal.fire({
                    title: 'Enviando selección...',
                    html: `
                        <div style="text-align: center; padding: 20px;">
                            <div style="margin-bottom: 20px;">
                                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                            <p style="margin: 0; color: #666;">
                                Por favor espera mientras se procesa la información...
                            </p>
                        </div>
                    `,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        // Agregar spinner CSS si no existe
                        if (!document.getElementById('spinner-styles')) {
                            const spinnerStyles = document.createElement('style');
                            spinnerStyles.id = 'spinner-styles';
                            spinnerStyles.textContent = `
                                .spinner-border {
                                    display: inline-block;
                                    width: 2rem;
                                    height: 2rem;
                                    vertical-align: text-bottom;
                                    border: 0.25em solid currentColor;
                                    border-right-color: transparent;
                                    border-radius: 50%;
                                    animation: spinner-border 0.75s linear infinite;
                                }
                                @keyframes spinner-border {
                                    to { transform: rotate(360deg); }
                                }
                                .text-primary { color: #667eea !important; }
                            `;
                            document.head.appendChild(spinnerStyles);
                        }
                    }
                });
                
                // Enviar datos al servidor
                $.ajax({
                    url: './supervision/crudsolicitudes_fixed.php?action=enviar_seleccion_cvs',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_solicitud: solicitudActual,
                        archivos_seleccionados: JSON.stringify(archivosSeleccionados),
                        comentario: comentario,
                        total_archivos: archivosParaEnvio.length
                    },
                    success: function(response) {
                        console.log("✅ Respuesta del servidor:", response);
                        
                        if (response && response.success) {
                            Swal.fire({
                                title: '¡Selección enviada exitosamente!',
                                html: `
                                    <div style="text-align: center; padding: 20px;">
                                        <div style="margin-bottom: 20px;">
                                            <i class="fas fa-check-circle" style="font-size: 64px; color: #28a745;"></i>
                                        </div>
                                        <h4 style="color: #28a745; margin-bottom: 15px;">Envío Completado</h4>
                                        <p style="margin-bottom: 15px; color: #666; font-size: 16px;">
                                            La selección de <strong>${archivosParaEnvio.length} CVs</strong> ha sido enviada exitosamente a RRHH.
                                        </p>
                                        <div style="
                                            background: #d4edda;
                                            border: 1px solid #c3e6cb;
                                            border-radius: 8px;
                                            padding: 15px;
                                            margin: 15px 0;
                                        ">
                                            <p style="margin: 0; color: #155724; font-size: 14px;">
                                                <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                                                El equipo de Recursos Humanos podrá revisar tu selección y dar seguimiento al proceso de contratación.
                                            </p>
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                                confirmButtonColor: '#28a745',
                                width: '600px'
                            }).then(() => {
                                // Cerrar modal y recargar tabla si es necesario
                                Swal.close();
                                // Opcional: recargar la página o actualizar la tabla
                                // location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error al enviar',
                                text: response?.error || 'Error al enviar la selección',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error AJAX:', xhr.responseText);
                        Swal.fire({
                            title: 'Error de conexión',
                            html: `
                                <div style="text-align: center; padding: 20px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
                                    <p style="margin: 0; color: #666;">
                                        No se pudo conectar con el servidor. Por favor, intenta nuevamente.
                                    </p>
                                    <div style="margin-top: 15px; font-size: 12px; color: #999;">
                                        Error: ${error}
                                    </div>
                                </div>
                            `,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }


    $('.btnVerPruebas').on('click', function () {
    const idSolicitud = $(this).data('id');
    const estadoActual = $(this).data('estado');

    $('#modalPruebasContenido').html('<p>Cargando archivos...</p>');
    $('#modalVerPruebas').modal('show');

    $.ajax({
        url: './supervision/crudsolicitudes.php?action=ver_pruebas_adjuntas',
        method: 'POST',
        data: {
            id_solicitud: idSolicitud,
            estado: estadoActual
        },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.archivos.length > 0) {
                let contenido = '<ul class="list-group">';
                response.archivos.forEach(archivo => {
                    const ext = archivo.NOMBRE.toLowerCase().split('.').pop();
                    const icon = ext === 'pdf' ? 'fa-file-pdf' :
                                 ext === 'doc' || ext === 'docx' ? 'fa-file-word' : 'fa-file';

                    contenido += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${archivo.NOMBRE}
                            <a href="${archivo.URL}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas ${icon}"></i> Ver
                            </a>
                        </li>`;
                });
                contenido += '</ul>';
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

