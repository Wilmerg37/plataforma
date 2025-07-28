<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>


<div class="container mt-5">
    <h3 class="text-center font-weight-bold text-primary"><i class="fas fa-chart-area"></i> Asignacion de Metas Semanal</h3>
    <form id="form-horarios" method="POST">
    <div class="form-group">
            <label class="text-center font-weight-bold text-primary" for="week_number"><i class="fas fa-calendar-week"></i> Ingrese el número de semana:</label>
            <input type="number" id="week_number" name="week_number" class="form-control" min="1" max="52" placeholder="Número de semana" required>
        </div>
        <div class="form-group">
            <label class="text-center font-weight-bold text-primary" for="employee_code"><i class="fas fa-user"></i> Seleccione Supervisor:</label>
            <select id="employee_code" name="employee_code" class="form-control" required>
                <option value="" disabled selected>Seleccione un supervisor</option>
            </select>
        </div>
        <div class="form-group">
            <label class="text-center font-weight-bold text-primary" for="store_no"><i class="fas fa-store"></i> Seleccione Tienda:</label>
            <select id="store_no" name="store_no" class="form-control" required>
                <option value="" disabled selected>Seleccione una tienda</option>
            </select>
        </div>
        <div class="form-group">
            <label class="text-center font-weight-bold text-primary" for="year"><i class="fas fa-calendar-alt"></i> Ingrese el año:</label>
            <input type="number" id="year" name="year" class="form-control" min="2000" max="3000" value="2025" required>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="showVacationistas">
            <label class="form-check-label" for="showVacationistas">Mostrar Vacacionistas</label>
        </div>
    </form>
</div>
<div class="container mt-5">
    <h3 id="title-meta" class="text-center font-weight-bold text-primary">Metas</h3>
</div>
<div class="mt-4">
<table id="empleadosTable" class="table table-hover table-sm tbavxv">
    <thead class="thead-dark">
        <tr>
            <th>CÓDIGO</th>
            <th>ASESORA</th>
            <th>PUESTO</th>
            <th>HORAS SEMANA</th>
            <th>PORCENTAJE</th>
            <th>MONTO SEMANAL</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
    <tr>
    <th colspan="4">Total Meta Semana:</th>
    <th id="percentageTotal"></th>
    <th id="totalMetas"></th>
    <td><button id="saveAllMetas" class="btn btn-success">Guardar Meta</button></td>
    </tr>
    </tfoot>
</table>
</div>
<script>
    var storeMeta = 0; // Almacena la meta total de la tienda

    function getCurrentWeekNumber() {
        const now = new Date();
        const startOfYear = new Date(now.getFullYear(), 0, 1);
        const pastDaysOfYear = (now - startOfYear) / 86400000;
        return Math.ceil((pastDaysOfYear + startOfYear.getDay() + 1) / 7);
    }

    $(document).ready(function() {
        const currentWeekNumber = getCurrentWeekNumber();
        $('#week_number').val(currentWeekNumber);
        $.ajax({
            url: 'backendmetas.php?action=get_supervisors',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var supervisorSelect = $('#employee_code');
                supervisorSelect.empty();
                supervisorSelect.append('<option value="" disabled selected>Seleccione un supervisor</option>');
                data.forEach(function(supervisor) {
                    supervisorSelect.append(new Option(supervisor.SUPERVISOR_NAME, supervisor.SUPERVISOR_ID));
                });
            },
            error: function() {
                console.error('Error al cargar supervisores');
            }
        });

        $('#employee_code').change(function() {
            var supervisorId = $(this).val();
            $.ajax({
                url: 'backendmetas.php?action=get_stores',
                type: 'GET',
                data: { supervisor_id: supervisorId },
                dataType: 'json',
                success: function(data) {
                    var storeSelect = $('#store_no');
                    storeSelect.empty();
                    storeSelect.append('<option value="" disabled selected>Seleccione una tienda</option>');
                    data.forEach(function(store) {
                        storeSelect.append(new Option(store.STORE_NAME, store.STORE_NO));
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error al cargar tiendas: ' + xhr.responseText);
                }
            });
        });
        function loadEmployeesAndMetas() {
        var storeNo = $('#store_no').val();
        var weekNumber = $('#week_number').val();
        var year = $('#year').val();
        var showVacationistas = $('#showVacationistas').is(':checked');

        if (!storeNo || !year) {
            Swal.fire('Por favor, complete todos los campos necesarios.');
            return;
        }

        $.ajax({
        url: 'backendmetas.php?action=get_employees',
        type: 'GET',
        data: { store_no: storeNo, semana: weekNumber, anio: year },
        dataType: 'json',
        success: function(data) {
            console.log(data);  // Verifica si la respuesta es la esperada
            var employeeTable = $('#empleadosTable tbody');
            employeeTable.empty();
            var totalMetas = 0;

           
            // Filtrar empleados si el checkbox está desmarcado
            var filteredData = showVacationistas ? data : data.filter(function(employee) {
                return employee.TIPO_PUESTO !== 'VACACIONISTA' && employee.TIPO_PUESTO !== 'TEMPORAL';
            });
            // Calcular el porcentaje inicial solo para empleados que no son vacacionistas o temporales
            var totalEmployees = filteredData.length;
            var initialPercentage = totalEmployees > 0 ? (100 / totalEmployees).toFixed(2) : 0;

            filteredData.forEach(function(employee) {
                var metaValue = parseFloat(employee.META || 0);
                totalMetas += metaValue;

                var percentageCell = (employee.TIPO_PUESTO !== 'VACACIONISTA') ? 
                    `<td contenteditable="true" class="percentage" data-original-percentage="${initialPercentage}">${initialPercentage}%</td>` : 
                    `<td class="percentage">0.00%</td>`;

                // Menú desplegable de puesto
                var puestoOptions = `
                    <select class="puesto-select form-control">
                        <option value="JEFE DE TIENDA" ${employee.TIPO_PUESTO === 'JEFE DE TIENDA' ? 'selected' : ''}>Jefe de Tienda</option>
                        <option value="SUB JEFE DE TIENDA" ${employee.TIPO_PUESTO === 'SUB JEFE DE TIENDA' ? 'selected' : ''}>Sub Jefe de Tienda</option>
                        <option value="ASESOR DE VENTAS" ${employee.TIPO_PUESTO === 'ASESOR DE VENTAS' ? 'selected' : ''}>Asesor de Ventas</option>
                        <option value="VACACIONISTA" ${employee.TIPO_PUESTO === 'VACACIONISTA' ? 'selected' : ''}>Vacacionista</option>
                        <option value="TEMPORAL" ${employee.TIPO_PUESTO === 'TEMPORAL' ? 'selected' : ''}>Temporal</option>
                    </select>
                `;

                var row = `<tr>
                    <td>${employee.EMPL_NAME}</td>
                    <td>${employee.FULL_NAME}</td>
                    <td>${puestoOptions}</td>
                    <td contenteditable="true" class="hours">${employee.HORA || 44}</td>
                    ${percentageCell}
                    <td contenteditable="true" class="meta">Q ${metaValue.toFixed(2)}</td>
                </tr>`;
                
                employeeTable.append(row);
            });
            
            $('#totalMetas').text(`Q ${totalMetas.toFixed(2)}`);
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar empleados y metas:', error);
            Swal.fire('Ha ocurrido un error al cargar los datos. Por favor, inténtelo de nuevo.');
        }
    });
}

        // Llamado de función en cambio de selección de tienda, semana, año y checkbox de vacaciones
        $('#store_no, #week_number, #year, #showVacationistas').change(function() {
            if ($('#store_no').val() && $('#week_number').val() && $('#year').val()) {
                loadEmployeesAndMetas();
            }
        });

        $('#week_number').keypress(function(e) {
            if (e.which === 13) { // Detectar Enter
                loadEmployeesAndMetas();
            }
        });

        function updateTitle(storeNo, weekNumber, year) {
            $.ajax({
                url: 'backendmetas.php?action=tile-metas',
                type: 'GET',
                data: {
                    t: storeNo,
                    s: weekNumber,
                    a: year
                },
                dataType: 'json',
                success: function(response) {
                    if (response.meta) {
                        storeMeta = parseFloat(response.meta); // Actualiza la meta global de la tienda
                        $('#title-meta').html(`Tienda no: ${storeNo}<br><small class="h4 text-primary font-weight-bold text-center">| Año: ${year} | Semana: ${weekNumber} | Meta tienda: Q ${storeMeta.toFixed(2)} |</small>`);
                    } else {
                        console.error('Error al cargar metas de la tienda:', response.error);
                        $('#title-meta').html("Error al cargar datos de la tienda");
                    }
                },
                error: function(xhr) {
                    console.error('Error al conectar con el backend para metas de tienda:', xhr.responseText);
                    $('#title-meta').html("Error de conexión");
                }
            });
        }

        $('#store_no, #week_number, #year').change(function() {
            if ($('#store_no').val() && $('#week_number').val() && $('#year').val()) {
                updateTitle($('#store_no').val(), $('#week_number').val(), $('#year').val());
            }
        });

        // Funcion editar metas
        $('#empleadosTable').on('click', '.edit-meta', function() {
            var $row = $(this).closest('tr');
            var $meta = $row.find('.meta');
            $(this).siblings('.save-meta').show(); // Mostrar botón guardar
            $(this).hide(); // Ocultar botón editar
            $(this).closest('tr').find('.meta').attr('contenteditable', true).focus();
        });

        $('#empleadosTable').on('input', '.percentage', function() {
            // Ajustar metas al cambiar el valor en una celda de porcentaje
            var newPercentage = parseFloat($(this).text().replace('%', ''));
            if (isNaN(newPercentage) || newPercentage < 0 || newPercentage > 100) {
                Swal.fire('Ingrese un porcentaje válido.');
                $(this).text($(this).data('original-percentage') + '%'); // Revertir si es inválido
            } else {
                adjustMetasByPercentage($(this).closest('tr'), newPercentage);
            }
        });

       
    // Funcion guardar metas
 /*    $('#empleadosTable').on('click', '.save-meta', function() {
        var $row = $(this).closest('tr');
        var storeNo = $('#store_no').val();
        var employeeCode = $row.find('td:first').text();
        var meta = $row.find('.meta').text();
        var weekNumber = $('#week_number').val();
        var tipo = $row.find('td:eq(2)').text(); 
        var year = $('#year').val();
        var hours = $row.find('.hours').text(); 

 */
    
            // Llamar al backend para actualizar la base de datos
/*             $.ajax({
                url: 'backendmetas.php?action=update_meta',
                type: 'POST',
                data: {
                    store_no: storeNo,
                    employee_name: employeeCode,
                    meta: meta,
                    semana: weekNumber,
                    tipo: tipo,
                    anio: year,
                    hora: hours  
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Meta actualizada correctamente');
                        $row.find('.edit-meta').show(); // Mostrar botón editar
                        $row.find('.save-meta').hide(); // Ocultar botón guardar
                        $row.find('.meta').attr('contenteditable', 'false'); // Deshabilitar edición
                        updateTotalMetas(); // Recalcular y actualizar el total de metas
                    } else {
                        alert('Error al actualizar la meta: ' + response.error);
                    }
                },
                error: function(xhr) {
                    alert('Error al conectar con el backend: ' + xhr.responseText);
                }
            });
        });
 */
            // Permitir solo números en el campo .meta
            $(document).on('keypress', '.meta', function(event) {
                const charCode = event.which;
                const inputValue = $(this).text();
                // Permitir números y un solo punto decimal
                if ((charCode >= 48 && charCode <= 57) || (charCode === 46 && inputValue.indexOf('.') === -1)) {
                    return true;
                }
                return false;
            });

            // Evitar caracteres no numéricos al pegar
            $(document).on('paste', '.meta', function(event) {
                const pastedData = event.originalEvent.clipboardData.getData('text');
                if (!/^\d*\.?\d*$/.test(pastedData)) {
                    event.preventDefault();
                }
            });



        $('#saveAllMetas').click(function() {
            var storeNo = $('#store_no').val();
            var weekNumber = $('#week_number').val();
            var year = $('#year').val();
            var metas = [];

            // Recorrer cada fila para recopilar las metas
            $('#empleadosTable tbody tr').each(function() {
                var employeeCode = $(this).find('td:first').text();
                var meta = $(this).find('.meta').text().replace('Q', '').trim();

                   // Validar que meta sea un número válido antes de agregarlo
                if (!/^\d+(\.\d+)?$/.test(meta)) {
                    alert('Por favor ingrese un valor numérico válido para el campo "Meta" del empleado con código ' + employeeCode);
                    return false;  // Detener el guardado si hay un valor no numérico
                }


                var hours = $(this).find('.hours').text(); // Asegura recolectar las horas
                var tipo = $(this).find('.puesto-select').val(); // Obtiene el valor seleccionado del select
                metas.push({
                    employee_name: employeeCode,
                    meta: meta,
                    tipo: tipo,
                    hours: hours  
                });
            });

            // Enviar los datos al backend
            $.ajax({
                url: 'backendmetas.php?action=save_all_metas',
                type: 'POST',
                contentType: 'application/json', // Asegurando que los datos se envían en formato JSON
                data: JSON.stringify({
                    store_no: storeNo,
                    semana: weekNumber,
                    tipo: $('#tipo').val(), // Si 'tipo' es un valor general para todos, obtenerlo aquí
                    anio: year,
                    metas: metas  // Envía el array completo de metas
                }),
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', 'Todas las metas han sido guardadas correctamente', 'success');

                    } else if (response.error) {
                        alert('Error al guardar las metas: ' + response.error);
                    } else {
                        Swal.fire('Éxito', 'Las metas de la semana de la tienda han sido guardada correctamente', 'success');

                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error al conectar con el backend: ' + error);
                }
            });
        });
        function adjustMetasByPercentage(editedRow, newPercentage) {
                var totalPercentageLeft = 100 - newPercentage;
                
                // Filtrar las filas que no son "VACACIONISTA" y no incluir la fila editada
                var otherRows = $('#empleadosTable tbody tr').not(editedRow).filter(function() {
                    var puesto = $(this).find('.puesto-select').val();
                    return puesto !== 'VACACIONISTA';
                });

                var adjustedPercentageTotal = 0;

                otherRows.each(function() {
                    var currentPercentage = parseFloat($(this).find('.percentage').text().replace('%', ''));
                    var newCurrentPercentage = (currentPercentage / totalPercentageLeft) * (100 - newPercentage);
                    $(this).find('.percentage').text(newCurrentPercentage.toFixed(2) + '%');
                    adjustedPercentageTotal += newCurrentPercentage;
                });

                // Ajuste final por error de redondeo
                if (adjustedPercentageTotal + newPercentage !== 100) {
                    var lastRow = otherRows.last();
                    var lastRowCurrentPercentage = parseFloat(lastRow.find('.percentage').text().replace('%', ''));
                    lastRow.find('.percentage').text((lastRowCurrentPercentage + 100 - adjustedPercentageTotal - newPercentage).toFixed(2) + '%');
                }

                // Ajustar montos de meta basado en nuevos porcentajes
                var totalMeta = parseFloat($('#totalMetas').text().replace('Q', ''));
                $('#empleadosTable tbody tr').each(function() {
                    var percentage = parseFloat($(this).find('.percentage').text().replace('%', ''));
                    var puesto = $(this).find('.puesto-select').val();

                    // Solo ajustar meta si el puesto no es "VACACIONISTA"
                    if (puesto !== 'VACACIONISTA') {
                        var newMeta = (percentage / 100) * totalMeta;
                        $(this).find('.meta').text(newMeta.toFixed(2));
                    } else {
                        // Si es "VACACIONISTA", dejar el valor de meta en 0
                        $(this).find('.meta').text('0.00');
                    }
                });

                updateTotalMetas();
            }


            function adjustPercentageByMeta(editedRow, newMeta) {
                var totalMeta = parseFloat($('#totalMetas').text().replace('Q', ''));
                var newPercentage = (newMeta / totalMeta) * 100;
                $(editedRow).find('.percentage').text(newPercentage.toFixed(2) + '%');

                // Ajustar otros porcentajes
                var otherRows = $('#empleadosTable tbody tr').not(editedRow);
                var totalPercentageLeft = 100 - newPercentage;
                var adjustedPercentageTotal = 0;

                otherRows.each(function() {
                    var currentPercentage = parseFloat($(this).find('.percentage').text().replace('%', ''));
                    var newCurrentPercentage = (currentPercentage / totalPercentageLeft) * (100 - newPercentage);
                    $(this).find('.percentage').text(newCurrentPercentage.toFixed(2) + '%');
                    adjustedPercentageTotal += newCurrentPercentage;
                });

                // Ajuste final por error de redondeo
                if (adjustedPercentageTotal + newPercentage !== 100) {
                    var lastRow = otherRows.last();
                    var lastRowCurrentPercentage = parseFloat(lastRow.find('.percentage').text().replace('%', ''));
                    lastRow.find('.percentage').text((lastRowCurrentPercentage + 100 - adjustedPercentageTotal - newPercentage).toFixed(2) + '%');
                }

                updateTotalMetas();
            }

            function updateTotalMetas() {
                var totalMetas = 0;
                var metaValues = [];

                // Sumar todas las metas
                $('#empleadosTable tbody tr').each(function() {
                    var metaValue = parseFloat($(this).find('.meta').text());
                    if (!isNaN(metaValue)) {
                        totalMetas += metaValue;
                        metaValues.push(metaValue);
                    } else {
                        metaValues.push(0);
                    }
                });

                var totalPercentage = 0;
                // Actualizar cada porcentaje basado en el total
                $('#empleadosTable tbody tr').each(function(index) {
                    var percentage = (metaValues[index] / totalMetas) * 100;
                    $(this).find('.percentage').text(`${percentage.toFixed(2)}%`);
                    totalPercentage += percentage;
                });

                // Mostrar el porcentaje total
                $('#percentageTotal').text(`${totalPercentage.toFixed(2)}%`);
            }
            var isEditing = false;

            $(document).on('input', '.meta', function() {
                if (!isEditing) {
                    isEditing = true; // Evitar procesamiento duplicado
                    var editedRow = $(this).closest('tr');
                    var newMeta = parseFloat($(this).text().replace('Q ', ''));
                    console.log("newMeta input:", newMeta); // Verificar el valor ingresado
                    if (!isNaN(newMeta)) {
                        adjustPercentageByMeta(editedRow, newMeta);
                    }
                    isEditing = false; // Restablecer el estado de edición
                }
            });

        
        });

</script>
</body>
</html>
