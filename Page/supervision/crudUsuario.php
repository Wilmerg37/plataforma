<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .pagination { justify-content: center; }
    .active { background-color: #007bff; color: white; }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="form-group">
    <button class="btn btn-success btn-lg btnCrearVendedor"> <i class="fas fa-user-plus"></i> Crear Vendedor</button>
    </div>
    <div class="form-group">
      <input type="text" id="searchInput" class="form-control" placeholder="Buscar Empleados...">
    </div>
    <input type="text" id="searchTienda" placeholder="Buscar por número de tienda" class="form-control mb-3">


    <table id="tblVendedores" class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Tienda No.</th>
          <th>Código Vendedor</th>
          <th>Nombre</th>
          <th>Puesto</th>
          <th>Activo</th>
          <th>Fecha Ingreso</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <nav>
      <ul class="pagination"></ul>
    </nav>
  </div>

  <script>
    (function () {
      let vendedores = [];
      let rowsPerPage = 10;
      let currentPage = 1;

      $(document).ready(function () {
        cargarVendedores();

        // Búsqueda general
      $('#searchInput').on('input', function () {
        const searchValue = $(this).val().toLowerCase();
        filtrarYRenderizar();
      });

      // Búsqueda por tienda
      $('#searchTienda').on('input', function () {
        filtrarYRenderizar();
      });

      function filtrarYRenderizar() {
        const searchValueGeneral = $('#searchInput').val().toLowerCase();
        const searchValueTienda = $('#searchTienda').val().toLowerCase();

        const filteredData = vendedores.filter(vendedor =>
          vendedor.TIENDA_NO.toString().toLowerCase().includes(searchValueTienda) &&
          Object.values(vendedor).some(value =>
            value.toString().toLowerCase().includes(searchValueGeneral)
          )
        );

        renderTable(filteredData);
        setupPagination(filteredData);
      }

        function cargarVendedores() {
          $.ajax({
            url: './supervision/crudVendedores.php?action=get_employees',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
              vendedores = data;
              renderTable(vendedores);
              setupPagination(vendedores);
            },
            error: function (xhr, status, error) {
              console.error('Error al cargar los datos:', xhr.responseText, error);
              Swal.fire('Error', 'No se pudieron cargar los datos.', 'error');
            }
          });
        }

        function renderTable(data) {
          const tbody = $('#tblVendedores tbody');
          tbody.empty();
          const start = (currentPage - 1) * rowsPerPage;
          const end = start + rowsPerPage;
          const pageData = data.slice(start, end);

          pageData.forEach(vendedor => {
            const estadoTexto = vendedor.ACTIVO === 'Sí' ? 'Desactivar' : 'Activar';
            const botonClase = vendedor.ACTIVO === 'Sí' ? 'btn-danger' : 'btn-success';
            const row = `
              <tr>
                <td>${vendedor.TIENDA_NO}</td>
                <td>${vendedor.CODIGO_VENDEDOR}</td>
                <td>${vendedor.NOMBRE}</td>
                <td>${vendedor.PUESTO}</td>
                <td>${vendedor.ACTIVO}</td>
                <td>${vendedor.FECHA_INGRESO}</td>
              <td>
                <button class="btn btn-primary btn-sm btnEditar" data-id="${vendedor.CODIGO_VENDEDOR}">Editar</button>
                <button class="btn ${botonClase} btn-sm btnToggleStatus" data-id="${vendedor.CODIGO_VENDEDOR}">${estadoTexto}</button>
              </td>
              </tr>`;
            tbody.append(row);
          });
        }

        function setupPagination(data) {
          const totalPages = Math.ceil(data.length / rowsPerPage);
          const pagination = $('.pagination');
          pagination.empty();

          for (let i = 1; i <= totalPages; i++) {
            const pageItem = `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link" href="#">${i}</a>
                              </li>`;
            pagination.append(pageItem);
          }

          $('.pagination li').click(function () {
            currentPage = parseInt($(this).text());
            $('.pagination li').removeClass('active');
            $(this).addClass('active');
            renderTable(data);
          });
        }

        $('.btnCrearVendedor').click(function() {
          Swal.fire({
            title: 'Crear Nuevo Vendedor',
            html: `
              <input type="text" id="tienda" class="swal2-input" placeholder="Número de tienda">
              <input type="text" id="codigo_vendedor" class="swal2-input" placeholder="Código Vendedor">
              <input type="text" id="nombre" class="swal2-input" placeholder="Nombre">
              <select id="puesto" class="swal2-input">
                <option value="JEFE DE TIENDA">Jefe de Tienda</option>
                <option value="SUB JEFE DE TIENDA">Sub Jefe de Tienda</option>
                <option value="ASESOR DE VENTAS">Asesor de Ventas</option>
                <option value="VACACIONISTA">Vacacionista</option>
                <option value="SUPERVISOR">Supervisor</option>
                <option value="TEMPORAL">Temporal</option>
              </select>
              <input type="text" id="fecha_ingreso" class="swal2-input" placeholder="Fecha de Ingreso">
              <input type="checkbox" id="activo" class="swal2-input" checked> Activo
            `,
            focusConfirm: false,
            preConfirm: () => {
              return {
                tienda: $('#tienda').val(),
                codigo_vendedor: $('#codigo_vendedor').val(),
                nombre: $('#nombre').val(),
                puesto: $('#puesto').val(),
                fecha_ingreso: $('#fecha_ingreso').val(),
                activo: $('#activo').is(':checked') ? 1 : 0
              };
            },
            confirmButtonText: 'Crear Vendedor',
            showCancelButton: true,
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
              const { tienda, codigo_vendedor, nombre, puesto, fecha_ingreso, activo } = result.value;
              $.ajax({
                url: './supervision/crudVendedores.php?action=add_employee',
                type: 'POST',
                data: {
                  tienda_no: tienda,
                  codigo_vendedor: codigo_vendedor,
                  nombre: nombre,
                  puesto: puesto,
                  fecha_ingreso: fecha_ingreso,
                  activo: activo
                },
                success: function(response) {
                  response = JSON.parse(response); // Asegúrate de convertir la respuesta a un objeto JSON
                  if (response.success) {
                    Swal.fire('Éxito', 'Vendedor creado correctamente.', 'success');
                    cargarVendedores();
                  } else {
                    Swal.fire('Error', 'No se pudo crear el vendedor. ' + response.error.message, 'error');
                  }
                },
                error: function(xhr, status, error) {
                  Swal.fire('Error', 'Error al conectar con el servidor: ' + error, 'error');
                }
              });
            }
          });
        });

        $(document).on('click', '.btnEditar', function () {
          const id = $(this).data('id');
          const vendedor = vendedores.find(v => v.CODIGO_VENDEDOR == id);

          Swal.fire({
                title: 'Editar Vendedor',
                html: `
            <label for="tienda">Número de tienda:</label>
            <input type="text" id="tienda" class="swal2-input" placeholder="Número de tienda" value="${vendedor.TIENDA_NO}">
            
            <label for="codigo_vendedor">Código Vendedor:</label>
            <input type="number" id="codigo_vendedor" class="swal2-input" placeholder="Código Vendedor" value="${vendedor.CODIGO_VENDEDOR}">
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" class="swal2-input" placeholder="Nombre" value="${vendedor.NOMBRE}">
            
            <label for="puesto">Puesto:</label>
            <select id="puesto" class="swal2-input">
            <option value="JEFE DE TIENDA" ${vendedor.PUESTO === 'JEFE DE TIENDA' ? 'selected' : ''}>Jefe de Tienda</option>
            <option value="SUB JEFE DE TIENDA" ${vendedor.PUESTO === 'SUB JEFE DE TIENDA' ? 'selected' : ''}>Sub Jefe de Tienda</option>
            <option value="ASESOR DE VENTAS" ${vendedor.PUESTO === 'ASESOR DE VENTAS' ? 'selected' : ''}>Asesor de Ventas</option>
            <option value="VACACIONISTA" ${vendedor.PUESTO === 'VACACIONISTA' ? 'selected' : ''}>Vacacionista</option>
            <option value="SUPERVISOR" ${vendedor.PUESTO === 'SUPERVISOR' ? 'selected' : ''}>Supervisor</option>
            <option value="TEMPORAL" ${vendedor.PUESTO === 'TEMPORAL' ? 'selected' : ''}>Temporal</option>
            </select>
            
            <label for="fecha_ingreso">Fecha de Ingreso:</label>
            <input type="text" id="fecha_ingreso" class="swal2-input" value="${vendedor.FECHA_INGRESO}">
          `,



            showCancelButton: true,
            confirmButtonText: 'Guardar',
            preConfirm: () => {
              return {
                tienda: $('#tienda').val(),
                codigo_vendedor: $('#codigo_vendedor').val(),
                nombre: $('#nombre').val(),
                puesto: $('#puesto').val(),
                fecha_ingreso: $('#fecha_ingreso').val()
              };
            }
          }).then((result) => {
            if (result.isConfirmed) {
              const { tienda, codigo_vendedor, nombre, puesto, fecha_ingreso } = result.value;

              $.ajax({
              url: './supervision/crudVendedores.php?action=update_employee',
              type: 'POST',
              data: {
                  tienda_no: $('#tienda').val(),
                  codigo_vendedor: $('#codigo_vendedor').val(),
                  nombre: $('#nombre').val(),  // Asegúrate de enviar este valor
                  puesto: $('#puesto').val(),
                  fecha_ingreso: $('#fecha_ingreso').val(),
                  activo: vendedor.ACTIVO === 'Sí' ? 1 : 0
              },
              success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire('Éxito', 'Vendedor actualizado correctamente.', 'success');
                        cargarVendedores();
                    } else {
                        Swal.fire('Error', 'No se pudo actualizar el vendedor: ' + result.error, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'La respuesta del servidor no es válida.', 'error');
                    console.error('Error parsing JSON:', response);
                }
            }

          });

            }
          });
        });

        function actualizarVendedor(id, { tienda, puesto }) {
          $.ajax({
            url: './supervision/crudVendedores.php?action=update_employee',
            type: 'POST',
            data: {
              codigo_vendedor: id,
              tienda_no: tienda,
              puesto: puesto,
              activo: 1
            },
            success: function (response) {
              if (response === 'true') {
                Swal.fire('Actualizado', 'Vendedor actualizado con éxito.', 'success');
                cargarVendedores();
              } else {
                Swal.fire('Error', 'No se pudo actualizar el vendedor.', 'error');
              }
            }
          });
        }
        $(document).on('click', '.btnToggleStatus', function () {
        const id = $(this).data('id');
        const vendedor = vendedores.find(v => v.CODIGO_VENDEDOR == id);
        const nuevoEstado = vendedor.ACTIVO === 'Sí' ? 0 : 1; // Alternar estado
        const accion = nuevoEstado === 1 ? 'activado' : 'desactivado';

        Swal.fire({
          title: `¿Estás seguro de cambiar el estatus a ${accion} de este vendedor?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, continuar'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: './supervision/crudVendedores.php?action=toggle_employee_status',
              type: 'POST',
              data: {
                codigo_vendedor: id,
                activo: nuevoEstado
              },
              success: function (response) {
                if (response === 'true') {
                  Swal.fire('Éxito', `El vendedor ha sido ${accion}.`, 'success');
                  cargarVendedores();
                } else {
                  Swal.fire('Error', 'No se pudo actualizar el estado.', 'error');
                }
              }
            });
          }
        });
      });
      });
    })();
  </script>
</body>
</html>
