<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";

$tienda = (isset($_POST['tienda'])) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];
$sim = impuestoSimbolo($sbs);

$iva = (isset($_POST['iva'])) ? $_POST['iva'] : '';
$vacacionista = (isset($_POST['vacacionista'])) ? $_POST['vacacionista'] : '';
$filtro = '';

if ($vacacionista == '1') {
  $filtro = '';
} else {
  $filtro = " AND EMP.EMPL_NAME < '5000'";
}
$semanas = rangoWe($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);

// Function to calculate time difference
function calcularDiferenciaTiempo($entrada, $salida) {
    if ($entrada == '00:00' || $salida == '00:00' || $entrada == 'DESCANSO' || $salida == 'DESCANSO') {
        return '--';
    }
    
    $timeEntrada = strtotime($entrada);
    $timeSalida = strtotime($salida);
    
    if ($timeSalida < $timeEntrada) {
        // Si la salida es al día siguiente
        $timeSalida += 24 * 3600;
    }
    
    $diferencia = $timeSalida - $timeEntrada;
    $horas = floor($diferencia / 3600);
    $minutos = floor(($diferencia % 3600) / 60);
    
    return sprintf('%02d:%02d', $horas, $minutos);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Horarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
     <link rel="stylesheet" href="../css/estilosmarcaje.css">
</head>

<body>
<div class="main-container">
    <div class="page-header">
        <h1><i class="fas fa-clock"></i> Control de Horarios y Asistencia</h1>
        <p class="mb-0">Período: <?php echo date('d/m/Y', strtotime($fi)) . ' - ' . date('d/m/Y', strtotime($ff)); ?></p>
    </div>

    <div class="legend-container">
        <div class="legend-box etiqueta-1"><i class="fas fa-users"></i> GTO Presencial</div>
        <div class="legend-box etiqueta-2"><i class="fas fa-video"></i> GTO Virtual</div>
        <div class="legend-box etiqueta-3"><i class="fas fa-tv"></i> TV Presencial</div>
        <div class="legend-box etiqueta-4"><i class="fas fa-desktop"></i> TV Virtual</div>
        <div class="legend-box etiqueta-5"><i class="fas fa-handshake"></i> Reunión GTS</div>
        <div class="legend-box etiqueta-6"><i class="fas fa-comments"></i> Reunión ASS</div>
        <div class="legend-box etiqueta-7"><i class="fas fa-graduation-cap"></i> Inducción ROY</div>
        <div class="legend-box etiqueta-8"><i class="fas fa-birthday-cake"></i> Cumpleaños</div>
        <div class="legend-box etiqueta-9"><i class="fas fa-umbrella-beach"></i> Vacaciones</div>
        <div class="legend-box etiqueta-10"><i class="fas fa-user-shield"></i> Cobertura</div>
        <div class="legend-box etiqueta-11"><i class="fas fa-ban"></i> Suspensión LABORAL</div>
        <div class="legend-box etiqueta-12"><i class="fas fa-medkit"></i> Suspensión IGSS</div>
        <div class="legend-box etiqueta-13"><i class="fas fa-baby"></i> Lactancia</div>
    </div>

    <?php
    foreach ($tiendas as $tienda) {
        $total = array(
            $factura = 0,
            $pare_roy = 0,
            $pares_otro = 0,
            $tota_pares = 0,
            $accesorios = 0,
            $venta = 0,
            $meta = 0,
            $hora = 0,
            $mt_prs = 0,
            $vta_prs = 0,
            $dif_prs = 0
        );

        $query = "  SELECT 
                        HR.TIENDA, 
                        HR.CODIGO_EMPL, 
                        HR.NOMBRE_EMPL, 
                        V.PUESTO,
                        UPPER(HR.DIA) AS DIA,
                        TO_CHAR(TO_DATE(HR.FECHA, 'YYYY-MM-DD'), 'DD/MM/YYYY') AS FECHA,
                        
                        CASE 
                            WHEN HR.HORA_IN = '00:00' THEN 'DESCANSO' 
                            ELSE HR.HORA_IN 
                        END AS HORA_IN,
                      NVL( TO_CHAR(RG.ENTRADA, 'HH24:MI'),'00:00') AS ENTRADA,
                      CASE 
                            WHEN HR.HORA_OUT = '00:00' THEN 'DESCANSO' 
                            ELSE HR.HORA_OUT 
                        END AS HORA_OUT,
                      NVL( TO_CHAR(RG.SALIDA, 'HH24:MI'),'00:00') AS SALIDA,
                          ST.UDF1_STRING COD_SUP, ST.UDF2_STRING NOM_SUP , HR.ETIQUETA , HR.JUSTIFICACION, hr.id_registro ,HR.FECHA_INICIO, HR.FECHA_FIN, 
                          TO_CHAR(FECHA_JUSTIFICACION, 'DD/MM/YYYY HH24:MI:SS'), HR.HORA_JUS_IN, HR.HORA_JUS_OUT

                    FROM ROY_HORARIO_TDS HR
                    INNER JOIN ROY_VENDEDORES_FRIED V 
                        ON HR.TIENDA = V.TIENDA AND HR.CODIGO_EMPL = V.CODIGO_VENDEDOR

                    INNER JOIN RPS.STORE ST 
                        ON V.TIENDA = ST.STORE_NO

                    INNER JOIN RPS.SUBSIDIARY SB 
                        ON V.SBS = SB.SBS_NO AND ST.SBS_SID = SB.SID

                     LEFT JOIN (
                        SELECT 
                            TIENDA, 
                            CODIGO_EMPLEADO, 
                            TRUNC(FECHA) AS FECHA,
                            MIN(FECHA) AS ENTRADA,
                            MAX(FECHA) AS SALIDA
                        FROM ROY_HLL_REGISTRO_HUELLA
                        GROUP BY TIENDA, CODIGO_EMPLEADO, TRUNC(FECHA)
                    ) RG 
                        ON HR.TIENDA = RG.TIENDA 
                        AND HR.CODIGO_EMPL = RG.CODIGO_EMPLEADO 
                        AND TRUNC(TO_DATE(HR.FECHA, 'YYYY-MM-DD')) = RG.FECHA

                    -- Filtros
                    WHERE TO_DATE(HR.FECHA, 'YYYY-MM-DD') 
      BETWEEN TO_DATE('$fi', 'YYYY-MM-DD') 
          AND TO_DATE('$ff', 'YYYY-MM-DD')

                      AND HR.TIENDA = $tienda
                      AND V.SBS = $sbs

                    ORDER BY 
                        HR.CODIGO_EMPL, 
                        TO_DATE(HR.FECHA, 'YYYY-MM-DD')";
        $resultado = consultaOracle(3, $query);
        $cnt = 1;
    ?>

    <div class="store-section">
        <div class="store-header">
            <i class="fas fa-store"></i>
            <h3>Tienda: <?php echo $tienda?></h3>
        </div>

        <div class="export-section">
            <button class="btn btn-export btn-modern" onclick="exportarExcel('tabla_<?php echo $tienda; ?>')">
                <i class="fas fa-file-excel"></i> Exportar a Excel
            </button>
        </div>

        <table id="tabla_<?php echo $tienda; ?>" class="professional-table table-responsive">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-id-card"></i> ID Registro</th>
                    <th><i class="fas fa-store"></i> Tienda</th>
                    <th><i class="fas fa-user-tag"></i> Código</th>          
                    <th><i class="fas fa-user"></i> Nombre</th>
                    <th><i class="fas fa-briefcase"></i> Puesto</th>
                    <th><i class="fas fa-calendar-day"></i> Día</th>
                    <th><i class="fas fa-calendar"></i> Fecha</th>
                    <th><i class="fas fa-clock"></i> Hora Ingreso</th>
                    <th><i class="fas fa-sign-in-alt"></i> Marco Entrada</th>
                    <th><i class="fas fa-clock"></i> Hora Salida</th>
                    <th><i class="fas fa-sign-out-alt"></i> Marco Salida</th>
                    <th><i class="fas fa-stopwatch"></i> Tiempo Trabajado</th>
                    <th><i class="fas fa-comment-alt"></i> Justificación</th>
                    <th><i class="fas fa-calendar-plus"></i> Fecha Inicio</th>
                    <th><i class="fas fa-calendar-minus"></i> Fecha Final</th>
                    <th><i class="fas fa-clock"></i> Hora Inicio</th>
                    <th><i class="fas fa-clock"></i> Hora Final</th>
                    <th><i class="fas fa-calendar-check"></i> Fecha Just.</th>
                    <th><i class="fas fa-cogs"></i> Acción</th>
                </tr>
            </thead>
            
            <tbody>
            <?php
            foreach ($resultado as $rdst) {
                $cnt++;

                // Validación de HORA INGRESO y MARCO ENTRADA
                $hora_ingreso = ($rdst[6] != 'DESCANSO') ? strtotime($rdst[6]) : false;
                $marco_entrada = ($rdst[7] != '00:00') ? strtotime($rdst[7]) : false;

                $claseEntrada = '';
                if (!$marco_entrada || !$hora_ingreso) {
                    $claseEntrada = 'alerta-hora';
                } elseif ($marco_entrada > $hora_ingreso) {
                    $claseEntrada = 'alerta-hora';
                }

                // Validación de HORA SALIDA y MARCO SALIDA
                $hora_salida = ($rdst[8] != 'DESCANSO') ? strtotime($rdst[8]) : false;
                $marco_salida = ($rdst[9] != '00:00') ? strtotime($rdst[9]) : false;

                $claseSalida = '';
                if (!$marco_salida || !$hora_salida) {
                    $claseSalida = 'alerta-hora';
                } elseif ($marco_salida < $hora_salida) {
                    $claseSalida = 'alerta-hora';
                }

                // Calcular diferencia de tiempo
                $tiempoTrabajado = calcularDiferenciaTiempo($rdst[7], $rdst[9]);
                ?>
                <tr>
                    <td><?php echo $cnt ?></td>
                    <td><?php echo $rdst[14] ?></td>
                    <td><?php echo $rdst[0] ?></td>
                    <td><?php echo $rdst[1] ?></td>
                    <td style="text-align: left;"><?php echo $rdst[2] ?></td>
                    <td><?php echo $rdst[3] ?></td>
                    <td><?php echo $rdst[4] ?></td>
                    <td><?php echo $rdst[5] ?></td>
                             
                    <?php
                    $etiquetaClase = !empty($rdst[12]) ? 'etiqueta-' . intval($rdst[12]) : '';
                    ?>
                    <td class="<?php echo $etiquetaClase; ?>"><?php echo $rdst[6] ?></td>
                    <td class="<?php echo $claseEntrada . ' ' . $etiquetaClase; ?>"><?php echo $rdst[7] ?></td>
                    <td class="<?php echo $etiquetaClase; ?>"><?php echo $rdst[8] ?></td>
                    <td class="<?php echo $claseSalida . ' ' . $etiquetaClase; ?>"><?php echo $rdst[9] ?></td>
                    <td class="tiempo-diferencia"><?php echo $tiempoTrabajado ?></td>

                    <td style="text-align: left;"><?php echo $rdst[13] ?></td>
                    <td><?php echo $rdst[15] ?></td>
                    <td><?php echo $rdst[16] ?></td>
                    <td><?php echo $rdst[18] ?></td>
                    <td><?php echo $rdst[19] ?></td>
                    <td><?php echo $rdst[17] ?></td>
                    
                    <td>
                        <button class="btn btn-justify btn-modern justificar-btn" 
                                data-id="<?php echo $rdst[14]; ?>" 
                                data-nombre="<?php echo $rdst[2]; ?>" 
                                data-codigo="<?php echo $rdst[1]; ?>"
                                data-fecha="<?php echo htmlspecialchars($rdst[5]); ?>"
                                data-dia="<?php echo $rdst[4]; ?>"
                                data-hora-in="<?php echo $rdst[6]; ?>"
                                data-hora-out="<?php echo $rdst[8]; ?>"
                                data-justificacion="<?php echo htmlspecialchars($rdst[13]); ?>">
                            <i class="fas fa-edit"></i> Justificar
                        </button>
                    </td>

                </tr>
                <?php
                if ($rdst[3] === 'VACACIONISTA') {
                    $rdst[6] = 0;
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    
    <?php } ?>
</div>

<!-- Modal Justificación -->
<div class="modal fade" id="justificarModal" tabindex="-1" role="dialog" aria-labelledby="justificarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="formJustificacion">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit"></i> Justificación de Horario</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">           
            <input type="hidden" name="id_registro" id="id_registro">
            <input type="hidden" name="etiqueta" id="etiqueta">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Empleado</label>
                        <input type="text" class="form-control" id="nombre_empleado" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> Código</label>
                        <input type="text" class="form-control" id="codigo_empleado" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Fecha</label>
                        <input type="text" class="form-control" id="fecha" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-calendar-day"></i> Día</label>
                        <input type="text" class="form-control" id="dia" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Hora Ingreso</label>
                        <input type="text" class="form-control" id="hora_in" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Hora Salida</label>
                        <input type="text" class="form-control" id="hora_out" disabled>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-list"></i> Seleccionar motivo</label>
                <select class="form-control" id="motivo_select">
                    <option value="">-- Seleccione un motivo --</option>
                    <option value="HOME OFFICE">HOME OFFICE</option>
                    <option value="GTO PRESENCIAL">GTO PRESENCIAL</option>
                    <option value="GTO VIRTUAL">GTO VIRTUAL</option>
                    <option value="TV PRESENCIAL">TV PRESENCIAL</option>
                    <option value="TV VIRTUAL">TV VIRTUAL</option>
                    <option value="REUNION GTS">REUNION GTS</option>
                    <option value="REUNION ASS">REUNION ASS</option>
                    <option value="INDUCCION ROY">INDUCCION ROY</option>
                    <option value="CUMPLEANOS">CUMPLEAÑOS</option>
                    <option value="VACACIONES">VACACIONES</option>
                    <option value="COBERTURA">COBERTURA</option>
                    <option value="SUSPENSION LABORAL">SUSPENSION LABORAL</option>
                    <option value="SUSPENSION IGGSS">SUSPENSION IGSS</option>
                    <option value="LACTANCIA">LACTANCIA</option>
                    <option value="OTROS">OTROS</option>
                </select>
            </div>

            <!-- Fechas de SUSPENSION -->
            <div id="fechasSuspension" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-plus"></i> Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-calendar-minus"></i> Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horas para capacitaciones -->
            <div id="horasGTO" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Hora Ingreso</label>
                            <input type="time" class="form-control" name="gto_hora_ingreso" id="gto_hora_ingreso">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Hora Salida</label>
                            <input type="time" class="form-control" name="gto_hora_salida" id="gto_hora_salida">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-comment-alt"></i> Justificación</label>
                <textarea class="form-control" name="justificacion" id="justificacion" rows="3"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success btn-modern">
                <i class="fas fa-save"></i> Guardar
            </button>
                              </div>
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-comment-alt"></i> Justificación</label>
                <textarea class="form-control" name="justificacion" id="justificacion" rows="3"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-success btn-modern">
                <i class="fas fa-save"></i> Guardar
            </button>
            <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTables con configuración profesional
    $('.professional-table').each(function() {
        $(this).DataTable({
            "searching": true,
            "paging": true,
            "pageLength": 25,
            "ordering": true,
            "info": true,
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "columnDefs": [
                { "orderable": false, "targets": [-1] } // Deshabilitar ordenamiento en la columna de acciones
            ]
        });
    });

    // Función para exportar a Excel
    window.exportarExcel = function(tableId) {
        const table = document.getElementById(tableId);
        const wb = XLSX.utils.table_to_book(table, {sheet: "Horarios"});
        
        // Agregar fecha al nombre del archivo
        const fecha = new Date().toLocaleDateString('es-ES').replace(/\//g, '-');
        const filename = `Horarios_${tableId}_${fecha}.xlsx`;
        
        XLSX.writeFile(wb, filename);
        
        // Mostrar notificación de éxito
        showNotification('Archivo Excel generado correctamente', 'success');
    };

    // Función para mostrar notificaciones
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icon} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);
        
        $('body').append(notification);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            notification.alert('close');
        }, 3000);
    }

    // Manejar click en botón justificar
    $(document).on('click', '.justificar-btn', function () {
        // Rellenar el modal con los datos de la fila
        $('#id_registro').val($(this).data('id'));
        $('#nombre_empleado').val($(this).data('nombre'));
        $('#codigo_empleado').val($(this).data('codigo'));
        $('#fecha').val($(this).data('fecha'));
        $('#dia').val($(this).data('dia'));
        $('#hora_in').val($(this).data('hora-in'));
        $('#hora_out').val($(this).data('hora-out'));
        $('#justificacion').val($(this).data('justificacion'));

        $('#justificarModal').modal('show');
    });

    // Guardar justificación
    $('#formJustificacion').submit(function (e) {
        e.preventDefault();
        
        // Mostrar loader en el botón
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Guardando...')
                 .prop('disabled', true);
        
        $.ajax({
            url: '/roy/Page/supervision/guardar_justificacion.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                showNotification('Justificación guardada correctamente', 'success');
                $('#justificarModal').modal('hide');
                
                // Opcional: recargar la página o actualizar la tabla
                setTimeout(() => {
                    location.reload();
                }, 1500);
            },
            error: function (xhr, status, error) {
                showNotification('Error al guardar la justificación: ' + error, 'error');
            },
            complete: function() {
                // Restaurar el botón
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Manejar cambio en el select de motivo
    $('#motivo_select').on('change', function () {
        var selected = $(this).val();

        // Mostrar fechas si aplica
        if (['SUSPENSION IGSS', 'SUSPENSION LABORAL', 'VACACIONES','CUMPLEANOS'].includes(selected)) {
            $('#fechasSuspension').slideDown();
        } else {
            $('#fechasSuspension').slideUp();
            $('#fecha_inicio').val('');
            $('#fecha_fin').val('');
        }

        // Mostrar horas si seleccionan capacitaciones o actividades especiales
        if (['GTO PRESENCIAL', 'GTO VIRTUAL', 'TV PRESENCIAL','TV VIRTUAL','REUNION GTS' , 'REUNION ASS','INDUCCION ROY', 'LACTANCIA','COBERTURA'].includes(selected)) {
            $('#horasGTO').slideDown();
        } else {
            $('#horasGTO').slideUp();
            $('#gto_hora_ingreso').val('');
            $('#gto_hora_salida').val('');
        }

        // Justificación automática o editable
        if (selected === 'OTROS' || selected === '') {
            $('#justificacion').val('').prop('readonly', false);
        } else {
            $('#justificacion').val(selected).prop('readonly', true);
        }

        // Almacena el valor de la etiqueta
        var etiquetas = {
            "GTO PRESENCIAL": 1,
            "GTO VIRTUAL": 2,
            "TV PRESENCIAL": 3,
            "TV VIRTUAL": 4,
            "REUNION GTS": 5,
            "REUNION ASS": 6,
            "INDUCCION ROY": 7,
            "CUMPLEANOS": 8,
            "VACACIONES": 9,
            "COBERTURA": 10,
            "SUSPENSION LABORAL": 11,
            "SUSPENSION IGSS": 12,
            "LACTANCIA": 13
        };

        $('#etiqueta').val(etiquetas[selected] || '');
    });

    // Mejorar la experiencia del usuario con tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Agregar tooltips a los botones
    $('.justificar-btn').attr('data-toggle', 'tooltip')
                        .attr('data-placement', 'top')
                        .attr('title', 'Agregar o editar justificación');
    
    $('.btn-export').attr('data-toggle', 'tooltip')
                    .attr('data-placement', 'top')
                    .attr('title', 'Descargar tabla en formato Excel');
});

// Función adicional para imprimir tabla específica
function imprimirTabla(tableId) {
    const tabla = document.getElementById(tableId);
    const ventana = window.open('', '_blank');
    
    ventana.document.write(`
        <html>
        <head>
            <title>Reporte de Horarios</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                th { background-color: #2c3e50; color: white; }
                .page-header { text-align: center; margin-bottom: 30px; }
                @media print { 
                    .btn { display: none; } 
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="page-header">
                <h2>Reporte de Control de Horarios</h2>
                <p>Generado el: ${new Date().toLocaleDateString('es-ES')}</p>
            </div>
            ${tabla.outerHTML}
            <script>
                window.onload = function() {
                    // Remover botones de la tabla para impresión
                    const botones = document.querySelectorAll('.btn, .justificar-btn');
                    botones.forEach(btn => btn.style.display = 'none');
                    
                    window.print();
                    window.onafterprint = function() {
                        window.close();
                    }
                }
            </script>
        </body>
        </html>
    `);
    
    ventana.document.close();
}

// Cargar script adicional si existe
var url = "../Js/supervision/supervisor.js";
$.getScript(url).fail(function() {
    console.log('Script supervisor.js no encontrado, continuando sin él...');
});
</script>

</body>
</html>