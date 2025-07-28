<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


  <style>
    .pagination { justify-content: center; }
    .active { background-color: #007bff; color: white; }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="form-group">
    <button class="btn btn-success btn-lg btnCrearsolicitud"> <i class="fas fa-user-plus"></i> Crear Solicitud</button>
    </div>
    <div class="form-group">
      <input type="text" id="searchInput" class="form-control" placeholder="Buscar Empleados...">
    </div>
    <input type="text" id="searchTienda" placeholder="Buscar por número de tienda" class="form-control mb-3">


    <table id="tblVendedores" class="table table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>Tienda No.</th>
          <th>Puesto</th>
          <th>Solicito</th>
          <th>Fecha Solicitud</th>
          <th>Fecha Modificacion</th>
          <th>Estado</th>
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
  let solicitudes = [];
  let rowsPerPage = 10;
  let currentPage = 1;

  $(document).ready(function () {
    cargarSolicitudes();

    $('#searchInput, #searchTienda').on('input', function () {
      filtrarYRenderizar();
    });

    function filtrarYRenderizar() {
      const searchValueGeneral = $('#searchInput').val().toLowerCase();
      const searchValueTienda = $('#searchTienda').val().toLowerCase();

      const filteredData = solicitudes.filter(item =>
        item.NUM_TIENDA.toString().toLowerCase().includes(searchValueTienda) &&
        Object.values(item).some(value =>
          value.toString().toLowerCase().includes(searchValueGeneral)
        )
      );

      renderTable(filteredData);
      setupPagination(filteredData);
    }

    function cargarSolicitudes() {
      $.ajax({
        url: './gestionhumana/crudSolicitudes.php?action=get_solicitudes',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
          solicitudes = data;
          renderTable(solicitudes);
          setupPagination(solicitudes);
        },
        error: function (xhr) {
          Swal.fire('Error', 'No se pudieron cargar las solicitudes.', 'error');
        }
      });
    }

    function renderTable(data) {
      const tbody = $('#tblVendedores tbody');
      tbody.empty();

      const start = (currentPage - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      const pageData = data.slice(start, end);

      pageData.forEach(item => {
        const row = `
          <tr>
            <td>${item.NUM_TIENDA}</td>
            <td>${item.PUESTO_SOLICITADO}</td>
            <td>${item.SOLICITADO_POR}</td>
            <td>${item.FECHA_SOLICITUD}</td>
            <td>-</td>
            <td>${item.ESTADO_SOLICITUD}</td>
            <td>
                <button class="btn btn-info btn-sm btnEditarSolicitud" data-index="${solicitudes.indexOf(item)}">Editar</button>
                <button class="btn btn-warning btn-sm btnCambiarEstado" data-tienda="${item.NUM_TIENDA}" data-fecha="${item.FECHA_SOLICITUD}">Cambiar Estado</button>
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
        renderTable(solicitudes);
      });
    }

    $('.btnCrearsolicitud').click(function () {
      Swal.fire({
        title: 'Crear Nueva Solicitud',
        html: `
          <input type="text" id="tienda" class="swal2-input" placeholder="Número de tienda">
          <select id="puesto" class="swal2-input">
            <option value="JEFE DE TIENDA">Jefe de Tienda</option>
            <option value="SUB JEFE DE TIENDA">Sub Jefe de Tienda</option>
            <option value="ASESOR DE VENTAS">Asesor de Ventas</option>
            <option value="VACACIONISTA">Vacacionista</option>
            <option value="SUPERVISOR">Supervisor</option>
            <option value="TEMPORAL">Temporal</option>
          </select>
          <select id="solicitado_por" class="swal2-input">
            <option value="" disabled selected>Selecciona quien solicita</option>
            <option value="OTTO VALENCIA">OTTO VALENCIA</option>
            <option value="CRISTY BOLAÑOS">CRISTY BOLAÑOS</option>
            <option value="BENITO ACOSTA">BENITO ACOSTA</option>
            <option value="MIRIAM PERUFFO">MIRIAM PERUFFO</option>
            <option value="ROSA MARQUEZ">ROSA MARQUEZ</option>
            <option value="GIOVANNI CARDOZA">GIOVANNI CARDOZA</option>
            <option value="CHRISTIAN QUAN">CHRISTIAN QUAN</option>
    </select>
             `,
        confirmButtonText: 'Crear Solicitud',
        
        showCancelButton: true,
        preConfirm: () => {
          return {
            tienda: $('#tienda').val(),
            puesto: $('#puesto').val(),
            solicitado_por: $('#solicitado_por').val()
          };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          const { tienda, puesto, solicitado_por } = result.value;
          $.ajax({
            url: './gestionhumana/crudSolicitudes.php?action=add_solicitud',
            type: 'POST',
            data: {
              tienda_no: tienda,
              puesto: puesto,
              solicitado_por: solicitado_por
            },
            success: function (response) {
              const res = JSON.parse(response);
              if (res.success) {
                Swal.fire('Éxito', 'Solicitud creada correctamente.', 'success');
                cargarSolicitudes();
              } else {
                Swal.fire('Error', res.error || 'Error al crear solicitud.', 'error');
              }
            },
            error: function () {
              Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error');
            }
          });
        }
      });
    });

    $(document).on('click', '.btnCambiarEstado', function () {
      const tienda = $(this).data('tienda');
      const fecha = $(this).data('fecha');

      Swal.fire({
        title: 'Cambiar Estado de Solicitud',
        input: 'select',
        inputOptions: {
          'Pendiente': 'Pendiente',
          'Aprobada': 'Aprobada',
          'Cancelada': 'Cancelada'
        },
        inputPlaceholder: 'Selecciona nuevo estado',
        showCancelButton: true
      }).then(result => {
        if (result.isConfirmed && result.value) {
          $.ajax({
            url: './gestionhumana/crudSolicitudes.php?action=toggle_solicitud_status',
            type: 'POST',
            data: {
              tienda_no: tienda,
              fecha_solicitud: fecha,
              nuevo_estado: result.value
            },
            success: function (response) {
              const res = JSON.parse(response);
              if (res.success) {
                Swal.fire('Actualizado', 'El estado ha sido actualizado.', 'success');
                cargarSolicitudes();
              } else {
                Swal.fire('Error', res.error || 'No se pudo actualizar.', 'error');
              }
            }
          });
        }
      });
      
      $(document).on('click', '.btnEditarSolicitud', function () {
  const index = $(this).data('index');
  const solicitud = solicitudes[index];

  Swal.fire({
    title: 'Editar Solicitud',
    html: `
      <input type="text" id="tienda_edit" class="swal2-input" value="${solicitud.NUM_TIENDA}" placeholder="Número de tienda">
      <select id="puesto_edit" class="swal2-input">
        <option value="JEFE DE TIENDA" ${solicitud.PUESTO_SOLICITADO === 'JEFE DE TIENDA' ? 'selected' : ''}>Jefe de Tienda</option>
        <option value="SUB JEFE DE TIENDA" ${solicitud.PUESTO_SOLICITADO === 'SUB JEFE DE TIENDA' ? 'selected' : ''}>Sub Jefe de Tienda</option>
        <option value="ASESOR DE VENTAS" ${solicitud.PUESTO_SOLICITADO === 'ASESOR DE VENTAS' ? 'selected' : ''}>Asesor de Ventas</option>
        <option value="VACACIONISTA" ${solicitud.PUESTO_SOLICITADO === 'VACACIONISTA' ? 'selected' : ''}>Vacacionista</option>
        <option value="SUPERVISOR" ${solicitud.PUESTO_SOLICITADO === 'SUPERVISOR' ? 'selected' : ''}>Supervisor</option>
        <option value="TEMPORAL" ${solicitud.PUESTO_SOLICITADO === 'TEMPORAL' ? 'selected' : ''}>Temporal</option>
      </select>
      <input type="text" id="solicitado_por_edit" class="swal2-input" value="${solicitud.SOLICITADO_POR}" placeholder="Solicitado por">
    `,
    confirmButtonText: 'Guardar Cambios',
    showCancelButton: true,
    preConfirm: () => {
      return {
        tienda_no: $('#tienda_edit').val(),
        puesto: $('#puesto_edit').val(),
        solicitado_por: $('#solicitado_por_edit').val()
      };
    }
  }).then(result => {
    if (result.isConfirmed) {
      const updatedData = result.value;

      $.ajax({
        url: './gestionhumana/crudSolicitudes.php?action=update_solicitud',
        type: 'POST',
        data: {
          tienda_no: updatedData.tienda_no,
          puesto: updatedData.puesto,
          solicitado_por: updatedData.solicitado_por,
          fecha_original: solicitud.FECHA_SOLICITUD
        },
        success: function (response) {
          const res = JSON.parse(response);
          if (res.success) {
            Swal.fire('Éxito', 'Solicitud actualizada correctamente.', 'success');
            cargarSolicitudes();
          } else {
            Swal.fire('Error', res.error || 'No se pudo actualizar la solicitud.', 'error');
          }
        },
        error: function () {
          Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
        }
      });
    }
  });
});



    });

  });
})();
</script>

</body>
</html>
