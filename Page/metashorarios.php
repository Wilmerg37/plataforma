<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Asignación de Metas Semanales</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      background-color: #f8f9fa;
    }

    .form-label i {
      margin-right: 8px;
    }

    .card {
      border-radius: 10px;
    }

    .table thead th {
      vertical-align: middle;
    }

    .meta, .hours, .percentage {
      min-width: 90px;
      text-align: center;
    }

    .table-responsive {
      border-radius: 10px;
      overflow: hidden;
    }

    .table tfoot {
      background-color: #f1f1f1;
      font-weight: bold;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <!-- Título -->
  <div class="text-center mb-4">
    <h2 class="text-primary fw-bold">
      <i class="fas fa-chart-line me-2"></i>Asignación de Metas Semanales
    </h2>
  </div>

  <!-- Formulario -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form id="form-horarios" method="POST">
        <div class="row g-3">
          <div class="col-md-3">
            <label for="week_number" class="form-label text-primary fw-bold">
              <i class="fas fa-calendar-week"></i> Semana
            </label>
            <input type="number" id="week_number" name="week_number" class="form-control" min="1" max="52" required />
          </div>

          <div class="col-md-3">
            <label for="year" class="form-label text-primary fw-bold">
              <i class="fas fa-calendar-alt"></i> Año
            </label>
            <input type="number" id="year" name="year" class="form-control" min="2000" max="3000" value="2025" required />
          </div>

          <div class="col-md-3">
            <label for="employee_code" class="form-label text-primary fw-bold">
              <i class="fas fa-user-tie"></i> Supervisor
            </label>
            <select id="employee_code" name="employee_code" class="form-select" required>
              <option value="" disabled selected>Seleccione un supervisor</option>
            </select>
          </div>

          <div class="col-md-3">
            <label for="store_no" class="form-label text-primary fw-bold">
              <i class="fas fa-store"></i> Tienda
            </label>
            <select id="store_no" name="store_no" class="form-select" required>
              <option value="" disabled selected>Seleccione una tienda</option>
            </select>
          </div>
        </div>

        <div class="form-check mt-3">
          <input class="form-check-input" type="checkbox" id="showVacationistas" />
          <label class="form-check-label" for="showVacationistas">
            Mostrar Vacacionistas
          </label>
        </div>
      </form>
    </div>
  </div>

  <!-- Título metas -->
  <div class="text-center mb-3">
    <h4 id="title-meta" class="fw-bold text-primary">Metas</h4>
  </div>

  <!-- Tabla -->
  <div class="table-responsive mb-5">
    <table id="empleadosTable" class="table table-bordered table-hover align-middle text-center table-sm">
      <thead class="table-dark">
        <tr>
          <th>Código</th>
          <th>Asesora</th>
          <th>Puesto</th>
          <th>Horas</th>
          <th>%</th>
          <th>Monto Semanal</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr>
          <td colspan="4" class="text-end">Total Meta Semana:</td>
          <td id="percentageTotal"></td>
          <td id="totalMetas"></td>
          <td>
            <button id="saveAllMetas" class="btn btn-success btn-sm">
              <i class="fas fa-save me-1"></i>Guardar
            </button>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- Tu script completo debe ir aquí -->
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

<!-- Bootstrap JS (para algunos componentes interactivos si los necesitas) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
