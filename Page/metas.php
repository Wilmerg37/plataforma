<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignación de horarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                    <style>
                    .bg-orange {
                        background-color: orange !important;
                        color: white; /* Para que el texto sea legible */
                    }


                  .legend-container {
    display: flex;
    flex-wrap: nowrap; /* ✅ Evita que pasen a otra fila */
    gap: 10px;
    justify-content: center; /* O 'center' si quieres centrarlos */
    margin-top: 20px;
    overflow-x: auto; /* ✅ Para permitir scroll horizontal si no caben */
}


  .legend-box {
      padding: 10px 15px;
      border-radius: 5px;
      color: #000;
      font-weight: bold;
      text-align: center;
      min-width: 50px;
  }


                      @media (max-width: 600px) {
                        .legend-box {
                        flex: 1 1 100%; /* En móviles: cada div ocupa toda la fila */
                        }
                    }

                    .bonos-header {
    display: flex;
    justify-content: center;
    align-items: end;
    position: absolute;
    left: 0;
    width: 100%;
    text-align: center;
    font-weight: bold;
    background-color: #6c757d; /* un gris oscuro, puedes cambiar */
    color: white;
    padding-top: 2px;
    padding-bottom: 2px;
    z-index: 1;
  }

  .bonos-container {
    position: relative;
  }

  .bonos-label {
    padding-top: 20px; /* espacio para el encabezado visual */
  }

                </style>

    
</head>
<body>
<div class="container-fluid mt-7">
    <h3 class="text-center font-weight-bold text-primary" ><i class="fas fa-user-friends"></i> Asignación de horarios</h3>
    <form id="form-horarios" method="POST">
        <!-- Selección de supervisor -->
        <div class="row">
    <!-- Columna izquierda con los formularios -->
    <div class="col-md-9">
        <!-- Selección de supervisor -->
       <div style="display: flex; gap: 20px; flex-wrap: wrap;">

    <!-- Supervisor -->
    <div class="form-group" style="max-width: 400px;">
        <label class="text-center font-weight-bold text-primary" for="employee_code">
            <i class="fas fa-user"></i> Seleccione Supervisor:
        </label>
        <select id="employee_code" name="employee_code" class="form-control" required>
            <option value="" disabled selected>Seleccione un supervisor</option>
        </select>
    </div>

    <!-- Tienda -->
    <div class="form-group" style="max-width: 400px;">
        <label class="text-center font-weight-bold text-primary" for="store_no">
            <i class="fas fa-store"></i> Seleccione Tienda:
        </label>
        <select id="store_no" name="store_no" class="form-control" required>
            <option value="" disabled selected>Seleccione una tienda</option>
        </select>
    </div>

    <!-- Fecha -->
    <div class="form-group" style="max-width: 400px;">
        <label class="text-center font-weight-bold text-primary" for="start-date">
            <i class="fas fa-calendar-alt"></i> Selecciona la fecha de inicio (domingo):
        </label>
        <input type="date" id="start-date" name="fecha" class="form-control" required>
    </div>

</div>
       
      </div>

      
</div>
         
             <div class="legend-container">
                <div class="legend-box" style="background-color:rgb(158, 35, 240);">GTO Presencial</div>
                <div class="legend-box" style="background-color:rgb(87, 244, 250);">GTO Virtual</div>
                <div class="legend-box" style="background-color:rgb(55, 118, 255);">TV Presencial</div>
                <div class="legend-box" style="background-color:rgb(82, 247, 90);">TV Virtual</div>
                <div class="legend-box" style="background-color:rgb(252, 239, 62);">Reunión GTS</div>
                <div class="legend-box" style="background-color:rgb(255, 124, 36);">Reunión ASS</div>
                <div class="legend-box" style="background-color:rgb(141, 69, 1);">Induccion ROY</div>
                <div class="legend-box" style="background-color:rgb(255, 104, 235);">Cumpleaños</div>
                <div class="legend-box" style="background-color:rgb(148, 148, 148);">Vacaciones</div>
                <div class="legend-box" style="background-color:rgb(117, 71, 97);">Cobertura</div>
                <div class="legend-box" style="background-color:rgb(68, 119, 66);">Suspensión LABORAL</div>
                <div class="legend-box" style="background-color:rgb(64, 68, 151);">Suspensión IGSS</div>
                <div class="legend-box" style="background-color:rgb(209, 133, 203);">Lactancia</div>
             </div>

       <div class="mt-4">
            <h4 class="text-center font-weight-bold text-primary" id="semana_display">Semana del año: <span id="numero_semana">0</span></h4>
            <input type="hidden" id="semana" name="semana">
        </div>
         <div class="mt-2 text-center">
            <h3 class="text-success font-weight-bold">Meta semanal: <span id="meta-semanal-total">Q. 0.00</span></h3>
                    
        </div>
      

        <!-- Fechas ocultas por día -->
                <input type="hidden" name="fechas[dia_domingo]" id="fecha-dia-domingo">
                <input type="hidden" name="fechas[dia_lunes]" id="fecha-dia-lunes">
                <input type="hidden" name="fechas[dia_martes]" id="fecha-dia-martes">
                <input type="hidden" name="fechas[dia_miercoles]" id="fecha-dia-miercoles">
                <input type="hidden" name="fechas[dia_jueves]" id="fecha-dia-jueves">
                <input type="hidden" name="fechas[dia_viernes]" id="fecha-dia-viernes">
                <input type="hidden" name="fechas[dia_sabado]" id="fecha-dia-sabado">

        <!-- Tabla de empleados -->
        <div class="container-fluid shadow rounded py-3 px-4">
            <i class="fas fa-users"></i> Empleados Asignados:
            <table id="empleadosTable" class="table table-hover table-sm tbrdst">

               <thead class="thead-dark">

                    <!-- Fila de metas -->
                        <tr class="bg-success text-white text-center font-weight-bold">
                            <td colspan="3">Meta por día</td>
                            <td id="meta-domingo">-</td>
                            <td id="meta-lunes">-</td>
                            <td id="meta-martes">-</td>
                            <td id="meta-miercoles">-</td>
                            <td id="meta-jueves">-</td>
                            <td id="meta-viernes">-</td>
                            <td id="meta-sabado">-</td>
                           <td colspan="4" style="background-color:rgb(247, 139, 120); color: #000; text-align: center;">
                            <strong>Horas extra para esta tienda:</strong> <span id="valor-horas-e">--</span></td>
                           <td colspan="2" style="background-color: #d2b48c; color: #000; text-align: center;">Meta</td>

                        </tr>

                    <tr>
                        <th class="bg-primary text-white">CÓDIGO</th>
                        <th class="bg-primary text-white">ASESORA</th>
                         <th class="bg-primary text-white">PUESTO</th>
                        <th style="width: 120px;" class="text-center align-middle bg-orange">DOMINGO <br><span id="fecha-domingo"></span></th>
                        <th style="width: 120px;" class="text-center align-middle bg-orange">LUNES <br> <span id="fecha-lunes"></span></th>
                        <th style="width: 120px;" class="text-center align-middle bg-orange">MARTES <br> <span id="fecha-martes"></span></th>
                        <th style="width: 120px;" class="text-center align-middle bg-orange">MIÉRCOLES <br> <span id="fecha-miercoles"></span></th>
                        <th style="width: 120px;" class="text-center align-middle bg-orange">JUEVES <br> <span id="fecha-jueves"></span></th>
                        <th style="width: 120px;" class="text-center align-middle bg-orange">VIERNES <br> <span id="fecha-viernes"></span></th>
                        <th style="width: 120px;" class="text-center align-middle bg-orange">SÁBADO <br> <span id="fecha-sabado"></span></th>
                        <th class="bg-primary text-white text-center"><br><span>SEM.</span></th>
                        <th class="bg-primary text-white text-center">TOTAL<br><span>LEY</span></th>
                        <th class="bg-primary text-white text-center">HORAS<br><span>ALM.</span></th>
                        <th class="bg-primary text-white text-center"><br><span>EXTR.</span></th>
                       <th style="background-color: #d2b48c; color: #000; text-align: center;">%</th>
                        <th style="background-color: #d2b48c; color: #000; text-align: center;">Q.</th>
                                                
                    </tr>
                </thead>


                <tbody>
                    <!-- Contenido de empleados se carga dinámicamente -->
                </tbody>
                <tfoot>
                    <!-- Fila de totales por día -->
                    <tr class="bg-light">
                        <td colspan="3"><strong>Totales por día:</strong></td>
                        <td><strong id="total-domingo">0</strong></td>
                        <td><strong id="total-lunes">0</strong></td>
                        <td><strong id="total-martes">0</strong></td>
                        <td><strong id="total-miercoles">0</strong></td>
                        <td><strong id="total-jueves">0</strong></td>
                        <td><strong id="total-viernes">0</strong></td>
                        <td><strong id="total-sabado">0</strong></td>
                        <td colspan="6"></td> <!-- Celdas vacías para horas de ley, almuerzo, extras y total -->
                    </tr>
                    <tr>
                        <td colspan="10"><strong>Total General:</strong></td>
                         <td><strong id="total-horas-trabajadas">0</strong></td>
                        <td><strong id="total-ley"></strong></td>
                        <td><strong id="total-horas-almuerzo">0</strong></td>
                        <td><strong id="total-horas-extras">0</strong></td>
                         <td><strong>100%</strong></td>
                        <td><strong id="total-meta-individual">0</strong></td>
                       
                    </tr>
                </tfoot>
            </table>
                        
                            <!-- agregado para calcular meta por vendedor -->
               <!--  <div id="meta-por-asesor" class="mt-4">
                    <h5 class="text-black font-weight-bold">Meta individual:</h5>
                    <table class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                            <tr>
                                
                                <th class="bg-primary text-white font-weight-bold">Porcentaje X Vendedor</th>
                                <th class="bg-primary text-white font-weight-bold">Meta individual</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-meta-individual">
                           
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td>100%</td>
                                <td id="total-meta-individual">Q. 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div> -->

        </div>

        <div class="text-center">
            <button id="btn-guardar" type="submit" class="btn btn-primary mt-4"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    
$(document).ready(function() {
    // Cargar supervisores al cargar la página
    $.ajax({
        url: 'insert_hours.php?action=get_supervisors',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var supervisorSelect = $('#employee_code');
            supervisorSelect.empty();
            supervisorSelect.append('<option value="" disabled selected>Seleccione un supervisor</option>');
            data.forEach(function(supervisor) {
                supervisorSelect.append('<option value="' + supervisor.SUPERVISOR_ID + '">' + supervisor.SUPERVISOR_ID + ' - ' + supervisor.SUPERVISOR_NAME + '</option>');
            });
        },
        error: function() {
            console.error('Error al cargar supervisores');
        }
    });

    // Cargar tiendas al seleccionar un supervisor
    $('#employee_code').change(function() {
        var supervisorId = $(this).val();
        $.ajax({
            url: 'insert_hours.php?action=get_stores',
            type: 'GET',
            data: { supervisor_id: supervisorId },
            dataType: 'json',
            success: function(data) {
                var storeSelect = $('#store_no');
                storeSelect.empty();
                storeSelect.append('<option value="" disabled selected>Seleccione una tienda</option>');
                data.forEach(function(store) {
                    storeSelect.append('<option value="' + store.STORE_NO + '"> ' + store.STORE_NO + ' </option>');
                });
            },
            error: function() {
                alert('Error al cargar tiendas');
            }
        });
    });

    // Validar cuando se selecciona tienda o fecha
$('#store_no, #start-date').change(function () {
    const tienda = $('#store_no').val();
    const fecha = $('#start-date').val();

    // Activamos el botón mientras validamos (por si antes estaba desactivado)
    $('#btn-guardar').prop('disabled', false);

    if (tienda && fecha) {
        validarHorariosAsignados(tienda, fecha);
    }
});



    // Cargar empleados al seleccionar una tienda
    $('#store_no').change(function() {
        var storeNo = $(this).val();
        $.ajax({
            url: 'insert_hours.php?action=get_employees',
            type: 'GET',
            data: { store_no: storeNo },
            dataType: 'json',
            success: function(data) {
                var employeeTable = $('#empleadosTable tbody');
                employeeTable.empty();

                if (data.length === 0) {
                    employeeTable.append('<tr><td>No se encontraron empleados para esta tienda.</td></tr>');
                    return;
                }

                // Obtener las HORAS_E (mínimo de horas extras) para la tienda seleccionada
                                $.ajax({
                                    url: 'insert_hours.php',
                                    type: 'GET',
                                    data: {
                                        action: 'get_horas_e',
                                        store_no: storeNo
                                    },
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data && data.HORAS_E !== null) {
                                            $('#valor-horas-e').text(data.HORAS_E);
                                            $('#horas-extra-minimas').show();
                                        } else {
                                            $('#valor-horas-e').text('--');
                                            $('#horas-extra-minimas').hide();
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error al obtener HORAS_E:', error);
                                        $('#valor-horas-e').text('--');
                                        $('#horas-extra-minimas').hide();
                                    }
                                });


               data.forEach(function(employee, index) {
                            var dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

                            var employeeRow = `
                                <tr>
                                    <td>${employee.EMPL_NAME}<input type="hidden" name="employees[${index}][codigo_emp]" value="${employee.EMPL_NAME}"></td>
                                    <td style="width: 300px;">${employee.FULL_NAME}<input type="hidden" name="employees[${index}][nombre_emp]" value="${employee.FULL_NAME}"></td>
                                    <td>${employee.PUESTO}<input type="hidden" name="employees[${index}][puesto]" value="${employee.PUESTO}"></td>`;

                            // Generar automáticamente las celdas para cada día
                            dias.forEach(function(dia) {
                                employeeRow += generarCeldaDia(index, dia);
                            });

                            // Agregar el resto de columnas
                            employeeRow += `
                                <td><input type="text" class="form-control total-horas" name="employees[${index}][hora_tot_s]" placeholder="Total horas" readonly></td>
                                <td><input type="text" class="form-control" name="employees[${index}][hora_ley_s]" value="44" placeholder="Horas de ley"></td>
                                <td><input type="text" class="form-control" name="employees[${index}][hora_alm_s]" value="5" placeholder="Horas de almuerzo"></td>
                                <td><input type="text" class="form-control hora-extra" name="employees[${index}][hora_extra_s]" placeholder="Horas extras" readonly></td>
                                <td class="meta-porcentaje text-center"></td>
                                <td class="meta-individual text-center"></td>
                            </tr>`;

                            employeeTable.append(employeeRow);
                        });


                // Recalcular horas trabajadas al cargar la tabla de empleados
                calcularHorasTrabajadas();
                actualizarMetaPorAsesor();
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar empleados:', error);
                console.log(xhr.responseText);  // Mostrar el error en la consola
            }
        });
    });


  // Función para enviar el formulario sin redirigir
$('#form-horarios').submit(function(event) {
    event.preventDefault();  // Prevenir la redirección

    // Serializar los datos del formulario
    var formData = $(this).serialize();

    // Enviar la solicitud AJAX
    $.ajax({
        url: 'insert_hours.php',
        type: 'POST',
        data: formData,
        success: function(response) {
            // Mostrar la alerta de éxito
            alert("Horas de empleados insertadas correctamente.");

            // Opción 1: Recargar la página para limpiar los campos y resetear el formulario
            location.reload(); // Esto recargará la página por completo

             //Opción 2: Limpiar los campos del formulario manualmente
             //$('#form-horarios')[0].reset(); // Descomentar esta línea para limpiar el formulario sin recargar

            // Opción 3: Limpiar solo los campos de texto y time
            // $('#form-horarios input[type="text"], #form-horarios input[type="time"]').val('');

            // Opción 4: Limpiar y recalcular las horas
            // $('#empleadosTable tbody').empty(); // Limpiar la tabla de empleados
            // calcularHorasTrabajadas(); // Recalcular las horas (si tienes una función específica)
        },
        error: function(xhr, status, error) {
            console.error('Error al insertar horas:', error);
            console.log(xhr.responseText);
        }
    });
});


function generarCeldaDia(index, dia) {
    return `
        <td>
            <input type="text" class="form-control hora-input" name="employees[${index}][${dia}][hora_in]" placeholder="HH:MM" value="09:00">
            <input type="text" class="form-control hora-input" name="employees[${index}][${dia}][hora_out]" placeholder="HH:MM" value="20:00">
            <select name="employees[${index}][${dia}][etiqueta]" class="form-control form-control-sm mt-1">
                <option value="0">Sin etiqueta</option>
                <option value="1" style="background-color:rgb(158, 35, 240);">GTO Presencial</option>
                <option value="2" style="background-color:rgb(87, 244, 250);">GTO Virtual</option>
                <option value="3" style="background-color:rgb(55, 118, 255);">TV Presencial</option>
                <option value="4" style="background-color:rgb(82, 247, 90);">TV Virtual</option>
                <option value="5" style="background-color:rgb(252, 239, 62);">Reunión GTS</option>
                <option value="6" style="background-color:rgb(255, 124, 36);">Reunión ASS</option>
                <option value="7" style="background-color:rgb(141, 69, 1);">Incuccion ROY</option>
                <option value="8" style="background-color:rgb(255, 104, 235);">Cumpleaños</option>
                <option value="9" style="background-color:rgb(148, 148, 148);">Vacaciones</option>
                <option value="10" style="background-color:rgb(117, 71, 97);">Cobertura</option>
                <option value="11" style="background-color:rgb(68, 119, 66);">Suspension LABORAL</option>
                <option value="12" style="background-color:rgb(64, 68, 151);">Suspension IGSS</option>
                <option value="13" style="background-color:rgb(209, 133, 203);">Lactancia</option>
            </select>
            <div class="horas-trabajadas" data-dia="${dia}"></div>
        </td>`;
}

    // Función para calcular el número de la semana con la fecha seleccionada y mostrar las fechas correspondientes
    function actualizarFechasYSemana() {
    var fechaInput = $('#start-date').val();
    var partes = fechaInput.split('-');
    var fechaSeleccionada = new Date(partes[0], partes[1] - 1, partes[2]);

    var numeroSemana = getWeekNumber(fechaSeleccionada);
    $('#semana').val(numeroSemana);
    $('#numero_semana').text(numeroSemana);

    var diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

    for (var i = 0; i < diasSemana.length; i++) {
        var fechaDia = new Date(fechaSeleccionada);
        fechaDia.setDate(fechaSeleccionada.getDate() + i);

        var dia = ('0' + fechaDia.getDate()).slice(-2);
        var mes = ('0' + (fechaDia.getMonth() + 1)).slice(-2);
        var anio = fechaDia.getFullYear();

        var fechaFormateada = `${dia}-${mes}-${anio}`;
        var fechaISO = `${anio}-${mes}-${dia}`; // formato para la base de datos

        $('#fecha-' + diasSemana[i]).text(fechaFormateada); // para mostrar
        $('#fecha-dia-' + diasSemana[i]).val(fechaISO);     // para enviar
    }
}

//cambia el color del select de la etiqueta
function cambiarColorSelect(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const color = selectedOption.style.backgroundColor;
    selectElement.style.backgroundColor = color;
}


//FUNSION PARA OBTENER METAS POR FECHA TIENDA
function obtenerMetasPorTiendaYFecha() {
    var storeNo = $('#store_no').val(); // o como sea que obtengas la tienda
    var fechaInput = $('#start-date').val(); // formato YYYY-MM-DD

    console.log("Fecha enviada al backend:", fechaInput);

    if (!storeNo || !fechaInput) {
        console.warn('Tienda o fecha de inicio no están definidos.');
        return;
    }

    $.ajax({
        url: 'insert_hours.php',
        method: 'GET',
        data: {
            action: 'get_goals',
            tienda: storeNo,
            fecha: fechaInput
        },

        
        success: function(response) {

            console.log("Respuesta del backend (metas):", response);
            // Días en orden
            var dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

            dias.forEach(function(dia) {
                if (response.hasOwnProperty(dia)) {
                        const valor = parseFloat(response[dia]);
                        if (!isNaN(valor)) {
                            $('#meta-' + dia).text('Q. ' + valor.toLocaleString('es-GT', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        } else {
                            $('#meta-' + dia).text('-');
                        }
                    } else {
                        $('#meta-' + dia).text('-');
                    }

            });

            let sumaMetas = 0;

                dias.forEach(function(dia) {
                    if (response.hasOwnProperty(dia)) {
                        const valor = parseFloat(response[dia]);
                        if (!isNaN(valor)) {
                            sumaMetas += valor;
                            $('#meta-' + dia).text('Q. ' + valor.toLocaleString('es-GT', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }));
                        } else {
                            $('#meta-' + dia).text('-');
                        }
                    } else {
                        $('#meta-' + dia).text('-');
                    }
                });

                // Mostrar la suma de metas
                $('#meta-semanal-total').text('Q. ' + sumaMetas.toLocaleString('es-GT', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                actualizarMetaPorAsesor(); 
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener las metas:', error);
        }
    });
}

//actualiza meta x asesor
function actualizarMetaPorAsesor() {
    var totalMetaSemanal = 0;
    var metaTexto = $('#meta-semanal-total').text().replace('Q. ', '').replaceAll(',', '').trim();

    if (metaTexto !== "") {
        totalMetaSemanal = parseFloat(metaTexto);
    }

    var filasAsesores = $('#empleadosTable tbody tr');
    var cantidadAsesores = filasAsesores.length;

    if (cantidadAsesores === 0 || isNaN(totalMetaSemanal)) {
        $('#total-meta-individual').text('Q. 0.00');
        filasAsesores.each(function () {
            $(this).find('.meta-porcentaje').text('-');
            $(this).find('.meta-individual').text('-');
        });
        return;
    }

    var metaIndividual = totalMetaSemanal / cantidadAsesores;
    var porcentaje = (100 / cantidadAsesores).toFixed(2);

    filasAsesores.each(function () {
        // Asignar % y meta individual a la fila del asesor
        $(this).find('.meta-porcentaje').text(`${porcentaje}%`);
        $(this).find('.meta-individual').text(` ${metaIndividual.toLocaleString('es-GT', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`);
    });

    // Actualiza el total semanal abajo
    $('#total-meta-individual').text(' ' + totalMetaSemanal.toLocaleString('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }));
}



$('#store_no').on('change', function () {
    actualizarFechasYSemana();
    obtenerMetasPorTiendaYFecha();
    actualizarMetaPorAsesor();
});

$('#start-date').on('change', function () {
    actualizarFechasYSemana();
    obtenerMetasPorTiendaYFecha();
    actualizarMetaPorAsesor();
});


        // Función para obtener el número de la semana
    function getWeekNumber(date) {
        var firstDayOfYear = new Date(date.getFullYear(), 0, 1);
        var pastDaysOfYear = (date - firstDayOfYear) / 86400000;
        return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
    }

    // Detectar cambios en la fecha de inicio y actualizar semana y fechas de los días
    $('#start-date').on('change', actualizarFechasYSemana);


    // Función para convertir hora en formato HH:MM a minutos
    function convertirHoraAMinutos(hora) {
        if (!hora || hora === "") return 0; // Si hora está vacío o es null, retornar 0
        var partes = hora.split(':');
        return parseInt(partes[0]) * 60 + parseInt(partes[1]);
    }

    // Función para convertir minutos a formato HH:MM
    function convertirMinutosAHoras(minutos) {
        if (isNaN(minutos) || minutos < 0) return "00:00"; // Si los minutos no son válidos, retornar "00:00"
        var horas = Math.floor(minutos / 60);
        var mins = minutos % 60;
        return horas.toString().padStart(2, '0') + ':' + mins.toString().padStart(2, '0');
    }

        // Vincula el evento de cambio a los campos de horas de almuerzo
        $(document).on('change', 'input[name*="[hora_alm_s]"], input[name*="[hora_ley_s]"]', function() {
        calcularHorasTrabajadas();  // Recalcula cuando las horas de almuerzo cambian
    });


    //funsion para validar horarios

    function validarHorariosAsignados(tienda, fecha) {
    fetch(`insert_hours.php?action=check_schedule&store_no=${tienda}&fecha=${fecha}`)
        .then(response => response.json())
        .then(data => {
            if (data.total > 0) {
                // Mostrar alerta con SweetAlert2
                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención!',
                    html: '<span style="color: red; font-weight: bold;">ESTA TIENDA YA TIENE HORARIOS ASIGNADOS PARA ESTA SEMANA.</span>',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#3085d6',
                    backdrop: true
                });

                // Desactivar botón de guardar
                document.getElementById('btn-guardar').disabled = true;
            } else {
                console.log("No hay horarios aún para esta tienda y semana.");

                // Activar botón de guardar si todo está bien
                document.getElementById('btn-guardar').disabled = false;
            }
        })
        .catch(error => {
            console.error("Error al verificar horarios:", error);

            // Por seguridad, habilita el botón si hay un error
            document.getElementById('btn-guardar').disabled = false;
        });
}

    
    // Función para calcular las horas trabajadas por día y empleado
    function calcularHorasTrabajadas() {
        var totalPorDia = {
            domingo: 0,
            lunes: 0,
            martes: 0,
            miercoles: 0,
            jueves: 0,
            viernes: 0,
            sabado: 0
        };

        var totalHorasTrabajadas = 0;
        var totalHorasExtras = 0;
        var totalHorasAlmuerzo = 0;

         // Iterar sobre cada fila de empleado
         $('#empleadosTable tbody tr').each(function() {
            var totalEmpleado = 0;
            var dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];

            dias.forEach(function(dia) {
                var horaIn = $(this).find(`input[name*="[${dia}][hora_in]"]`).val();
                var horaOut = $(this).find(`input[name*="[${dia}][hora_out]"]`).val();
                
                if (horaIn && horaOut) {
                    var minutosTrabajados = convertirHoraAMinutos(horaOut) - convertirHoraAMinutos(horaIn);
                    if (minutosTrabajados < 0) minutosTrabajados += 24 * 60;  // Ajuste para cuando hay horas cruzando medianoche
                    
                    var horasTrabajadas = minutosTrabajados / 60;
                    totalEmpleado += horasTrabajadas;
                    totalPorDia[dia] += horasTrabajadas;

                    // Mostrar las horas trabajadas como texto en la celda correspondiente
                    $(this).find(`div[data-dia="${dia}"]`).text(horasTrabajadas.toFixed(0));
                } else {
                    // Si no hay valores válidos, mostrar 0 en la celda correspondiente
                    $(this).find(`div[data-dia="${dia}"]`).text("0");
                }
            }, this);

            // Obtener las horas de almuerzo y ley
            var horasAlmuerzo = parseFloat($(this).find('input[name*="[hora_alm_s]"]').val()) || 0;
            var horasLey = parseFloat($(this).find('input[name*="[hora_ley_s]"]').val()) || 44;

            // Calcular horas netas trabajadas
            var horasNetasTrabajadas = totalEmpleado - horasAlmuerzo;
            var horasExtras = Math.max(horasNetasTrabajadas - horasLey, 0);

            // Restar horas extras del total de horas netas trabajadas
            //ANULO LA RESTA DE HORAS EXTRAS PARA QUE DE EL TOTAL DE HORAS TRABAJADAS EN LA SEMANA
          //  horasNetasTrabajadas -= horasExtras;  

            // Actualizar los valores en la tabla
            $(this).find('.total-horas').val(horasNetasTrabajadas.toFixed(0));
            $(this).find('.hora-extra').val(horasExtras.toFixed(0));
            totalHorasTrabajadas += horasNetasTrabajadas;
            totalHorasExtras += horasExtras;
            totalHorasAlmuerzo += horasAlmuerzo;
        });


        // Actualizar las sumas totales por día en el pie de la tabla
        for (var dia in totalPorDia) {
            $(`#total-${dia}`).text(totalPorDia[dia].toFixed(0));
        }

        // Actualizar los totales generales en el pie de la tabla
        $('#total-horas-trabajadas').text(totalHorasTrabajadas.toFixed(0));
        $('#total-horas-almuerzo').text(totalHorasAlmuerzo.toFixed(0));
        $('#total-horas-extras').text(totalHorasExtras.toFixed(0));
    }

    // Detectar cambios en las horas de entrada o salida y recalcular
    $(document).on('change', '.hora-input', calcularHorasTrabajadas);

    // Convertir entrada de texto a formato HH:00 automáticamente
    $(document).on('change', '.hora-input', function() {
        let value = $(this).val().trim();

        // Si el valor es un número entre 0 y 23, agregar ":00"
        if (/^\d{1,2}$/.test(value) && parseInt(value) >= 0 && parseInt(value) <= 23) {
            $(this).val(value.padStart(2, '0') + ":00"); // Convertir a formato "HH:00"
        }

        // Recalcular después de formatear la entrada
        calcularHorasTrabajadas();
    });
});
</script>
<script>
    function actualizarReloj() {
        const reloj = document.getElementById("reloj");
        const ahora = new Date();
        const horas = ahora.getHours().toString().padStart(2, '0');
        const minutos = ahora.getMinutes().toString().padStart(2, '0');
        const segundos = ahora.getSeconds().toString().padStart(2, '0');
        reloj.textContent = `${horas}:${minutos}:${segundos}`;
    }

    setInterval(actualizarReloj, 1000);
    actualizarReloj(); // Mostrarlo al cargar
</script>

</body>
</html>
