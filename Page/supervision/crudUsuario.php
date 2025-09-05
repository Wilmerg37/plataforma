<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Vendedores</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --primary-color: #4a90e2;
      --secondary-color: #f8f9fa;
      --success-color: #28a745;
      --danger-color: #dc3545;
      --warning-color: #ffc107;
      --dark-color: #343a40;
      --border-radius: 8px;
      --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main-header {
      background: linear-gradient(135deg, var(--primary-color), #357abd);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      box-shadow: var(--box-shadow);
    }

    .main-header h1 {
      margin: 0;
      font-weight: 600;
      font-size: 2.5rem;
    }

    .main-header p {
      margin: 0.5rem 0 0 0;
      opacity: 0.9;
    }

    .controls-section {
      background: white;
      padding: 1.5rem;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      margin-bottom: 2rem;
    }

    .search-container {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: center;
    }

    .search-input-group {
      flex: 1;
      min-width: 250px;
      position: relative;
    }

    .search-input-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
      z-index: 3;
    }

    .search-input-group input {
      padding-left: 45px;
      border: 2px solid #e9ecef;
      border-radius: var(--border-radius);
      transition: all 0.3s ease;
    }

    .search-input-group input:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    }

    .btn-create {
      background: linear-gradient(45deg, var(--success-color), #20c997);
      border: none;
      border-radius: var(--border-radius);
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
      transition: all 0.3s ease;
    }

    .btn-create:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(40, 167, 69, 0.4);
    }

    .table-container {
      background: white;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      overflow: hidden;
    }

    .table {
      margin: 0;
    }

    .table thead th {
      background: linear-gradient(45deg, var(--dark-color), #495057);
      color: white;
      border: none;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.875rem;
      letter-spacing: 0.5px;
      padding: 1rem 0.75rem;
    }

    .table tbody tr {
      transition: all 0.2s ease;
    }

    .table tbody tr:hover {
      background-color: #f8f9fa;
      transform: scale(1.01);
    }

    .table tbody td {
      padding: 1rem 0.75rem;
      vertical-align: middle;
      border-color: #e9ecef;
    }

    .status-badge {
      padding: 0.375rem 0.75rem;
      border-radius: 20px;
      font-weight: 600;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .status-active {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .status-inactive {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
      justify-content: center;
    }

    .btn-action {
      padding: 0.5rem 0.75rem;
      border-radius: var(--border-radius);
      border: none;
      font-weight: 500;
      font-size: 0.875rem;
      transition: all 0.2s ease;
      min-width: 80px;
    }

    .btn-edit {
      background: linear-gradient(45deg, var(--primary-color), #357abd);
      color: white;
    }

    .btn-edit:hover {
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(74, 144, 226, 0.4);
    }

    .btn-toggle {
      background: linear-gradient(45deg, var(--warning-color), #e0a800);
      color: #212529;
    }

    .btn-toggle:hover {
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(255, 193, 7, 0.4);
    }

    .btn-delete {
      background: linear-gradient(45deg, var(--danger-color), #c82333);
      color: white;
    }

    .btn-delete:hover {
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(220, 53, 69, 0.4);
    }

    .pagination {
      justify-content: center;
      margin-top: 2rem;
    }

    .page-link {
      border-radius: var(--border-radius);
      margin: 0 0.125rem;
      border: 2px solid #e9ecef;
      color: var(--primary-color);
      font-weight: 500;
    }

    .page-item.active .page-link {
      background: linear-gradient(45deg, var(--primary-color), #357abd);
      border-color: var(--primary-color);
    }

    .page-link:hover {
      border-color: var(--primary-color);
      background-color: rgba(74, 144, 226, 0.1);
    }

    /* Estilos para SweetAlert2 */
    .swal2-popup {
      border-radius: var(--border-radius);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .swal2-title {
      color: var(--dark-color);
      font-weight: 600;
    }

    .swal2-input, .swal2-select {
      border: 2px solid #e9ecef;
      border-radius: var(--border-radius);
      padding: 0.75rem 1rem;
      font-size: 0.9rem;
      margin: 0.5rem 0;
      transition: border-color 0.3s ease;
    }

    .swal2-input:focus, .swal2-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
      outline: none;
    }

    .swal2-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: var(--dark-color);
      text-align: left;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 1rem 0;
    }

    .checkbox-container input[type="checkbox"] {
      margin-right: 0.5rem;
      transform: scale(1.2);
    }

    .stats-row {
      background: white;
      padding: 1rem;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      margin-bottom: 1rem;
    }

    .stat-item {
      text-align: center;
      padding: 1rem;
    }

    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary-color);
    }

    .stat-label {
      color: #6c757d;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
      .search-container {
        flex-direction: column;
      }
      
      .search-input-group {
        min-width: 100%;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .btn-action {
        min-width: 100%;
        margin-bottom: 0.25rem;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <div class="main-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col">
          <h1><i class="fas fa-users-cog me-3"></i>Gestión de Vendedores</h1>
          <p>Sistema integral para la administración de personal de ventas</p>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <!-- Estadísticas -->
    <div class="row stats-row">
      <div class="col-md-3 stat-item">
        <div class="stat-number" id="totalVendedores">0</div>
        <div class="stat-label">Total Vendedores</div>
      </div>
      <div class="col-md-3 stat-item">
        <div class="stat-number" id="vendedoresActivos">0</div>
        <div class="stat-label">Activos</div>
      </div>
      <div class="col-md-3 stat-item">
        <div class="stat-number" id="vendedoresInactivos">0</div>
        <div class="stat-label">Inactivos</div>
      </div>
      <div class="col-md-3 stat-item">
        <div class="stat-number" id="totalTiendas">0</div>
        <div class="stat-label">Tiendas</div>
      </div>
    </div>

    <!-- Controles -->
    <div class="controls-section">
      <div class="row align-items-center">
        <div class="col-md-4">
          <button class="btn btn-success btn-create btnCrearVendedor">
            <i class="fas fa-user-plus me-2"></i>Crear Vendedor
          </button>
        </div>
        <div class="col-md-8">
          <div class="search-container">
            <div class="search-input-group">
              <i class="fas fa-search"></i>
              <input type="text" id="searchInput" class="form-control" placeholder="Buscar por código, puesto...">
            </div>
            <div class="search-input-group">
              <i class="fas fa-store"></i>
              <input type="text" id="searchTienda" class="form-control" placeholder="Buscar por número de tienda">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <div class="table-container">
      <div class="table-responsive">
        <table id="tblVendedores" class="table table-hover">
          <thead>
            <tr>
              <th><i class="fas fa-store me-2"></i>Tienda No.</th>
              <th><i class="fas fa-id-card me-2"></i>Código</th>
              <th><i class="fas fa-user me-2"></i>Nombre</th>
              <th><i class="fas fa-briefcase me-2"></i>Puesto</th>
              <th><i class="fas fa-toggle-on me-2"></i>Estado</th>
              <th><i class="fas fa-calendar me-2"></i>Fecha Ingreso</th>
              <th><i class="fas fa-cogs me-2"></i>Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <!-- Paginación -->
      <nav class="p-3">
        <ul class="pagination"></ul>
      </nav>
    </div>
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
          filtrarYRenderizar();
        });

        // Búsqueda por tienda
        $('#searchTienda').on('input', function () {
          filtrarYRenderizar();
        });

        function filtrarYRenderizar() {
          const searchValueGeneral = $('#searchInput').val().toLowerCase();
          const searchValueTienda = $('#searchTienda').val().toLowerCase();

          const filteredData = vendedores.filter(vendedor => {
            const matchesTienda = vendedor.TIENDA_NO.toString().toLowerCase().includes(searchValueTienda);
            const matchesGeneral = Object.values(vendedor).some(value =>
              value.toString().toLowerCase().includes(searchValueGeneral)
            );
            return matchesTienda && matchesGeneral;
          });

          currentPage = 1; // Reset página al filtrar
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
              updateStats(vendedores);
            },
            error: function (xhr, status, error) {
              console.error('Error al cargar los datos:', xhr.responseText, error);
              Swal.fire({
                title: 'Error',
                text: 'No se pudieron cargar los datos.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
            }
          });
        }

        function updateStats(data) {
          const total = data.length;
          const activos = data.filter(v => v.ACTIVO === 'Sí').length;
          const inactivos = total - activos;
          const tiendas = new Set(data.map(v => v.TIENDA_NO)).size;

          $('#totalVendedores').text(total);
          $('#vendedoresActivos').text(activos);
          $('#vendedoresInactivos').text(inactivos);
          $('#totalTiendas').text(tiendas);
        }

        function renderTable(data) {
          const tbody = $('#tblVendedores tbody');
          tbody.empty();
          const start = (currentPage - 1) * rowsPerPage;
          const end = start + rowsPerPage;
          const pageData = data.slice(start, end);

          if (pageData.length === 0) {
            tbody.append(`
              <tr>
                <td colspan="7" class="text-center py-4">
                  <i class="fas fa-search fa-3x text-muted mb-3"></i>
                  <p class="text-muted">No se encontraron vendedores</p>
                </td>
              </tr>
            `);
            return;
          }

          pageData.forEach(vendedor => {
            const isActive = vendedor.ACTIVO === 'Sí';
            const statusBadge = isActive 
              ? '<span class="status-badge status-active"><i class="fas fa-check-circle me-1"></i>Activo</span>'
              : '<span class="status-badge status-inactive"><i class="fas fa-times-circle me-1"></i>Inactivo</span>';
            
            const toggleText = isActive ? 'Desactivar' : 'Activar';
            const toggleIcon = isActive ? 'fas fa-toggle-off' : 'fas fa-toggle-on';

            const row = `
              <tr>
                <td><strong>${vendedor.TIENDA_NO}</strong></td>
                <td><code>${vendedor.CODIGO_VENDEDOR}</code></td>
                <td><i class="fas fa-user-circle me-2 text-primary"></i>${vendedor.NOMBRE}</td>
                <td><span class="badge badge-secondary">${vendedor.PUESTO}</span></td>
                <td>${statusBadge}</td>
                <td><i class="far fa-calendar-alt me-1"></i>${vendedor.FECHA_INGRESO}</td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-action btn-edit btnEditar" data-id="${vendedor.CODIGO_VENDEDOR}" title="Editar">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-action btn-toggle btnToggleStatus" data-id="${vendedor.CODIGO_VENDEDOR}" title="${toggleText}">
                      <i class="${toggleIcon}"></i>
                    </button>
                    <button class="btn btn-action btn-delete btnEliminar" data-id="${vendedor.CODIGO_VENDEDOR}" title="Eliminar">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>`;
            tbody.append(row);
          });
        }

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

          // Números de página
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

          // Event listeners para paginación
          $('.pagination .page-link').click(function (e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page && page !== currentPage && page >= 1 && page <= totalPages) {
              currentPage = page;
              const searchValueGeneral = $('#searchInput').val().toLowerCase();
              const searchValueTienda = $('#searchTienda').val().toLowerCase();
              
              const filteredData = vendedores.filter(vendedor => {
                const matchesTienda = vendedor.TIENDA_NO.toString().toLowerCase().includes(searchValueTienda);
                const matchesGeneral = Object.values(vendedor).some(value =>
                  value.toString().toLowerCase().includes(searchValueGeneral)
                );
                return matchesTienda && matchesGeneral;
              });
              
              renderTable(filteredData);
              setupPagination(filteredData);
            }
          });
        }

        // Crear vendedor
        $('.btnCrearVendedor').click(function() {
          Swal.fire({
            title: '<i class="fas fa-user-plus me-2"></i>Crear Nuevo Vendedor',
            html: `
              <div class="text-left">
                <label class="swal2-label">Número de tienda:</label>
                <input type="number" id="tienda" class="swal2-input" placeholder="Ej: 101">
                
                <label class="swal2-label">Código Vendedor:</label>
                <input type="number" id="codigo_vendedor" class="swal2-input" placeholder="Ej: 12345">
                
                <label class="swal2-label">Nombre completo:</label>
                <input type="text" id="nombre" class="swal2-input" placeholder="Ej: Juan Pérez García">
                
                <label class="swal2-label">Puesto:</label>
                <select id="puesto" class="swal2-input">
                  <option value="">Seleccionar puesto</option>
                  <option value="JEFE DE TIENDA">Jefe de Tienda</option>
                  <option value="SUB JEFE DE TIENDA">Sub Jefe de Tienda</option>
                  <option value="ASESOR DE VENTAS">Asesor de Ventas</option>
                  <option value="VACACIONISTA">Vacacionista</option>
                  <option value="SUPERVISOR">Supervisor</option>
                  <option value="TEMPORAL">Temporal</option>
                </select>
                
                <label class="swal2-label">Fecha de Ingreso (DD-MM-YYYY):</label>
                <input type="text" id="fecha_ingreso" class="swal2-input" placeholder="Ej: 15/01/2024">
                
                <div class="checkbox-container">
                  <input type="checkbox" id="activo" checked>
                  <label for="activo">Vendedor activo</label>
                </div>
              </div>
            `,
            focusConfirm: false,
            width: 600,
            preConfirm: () => {
              const tienda = $('#tienda').val();
              const codigo = $('#codigo_vendedor').val();
              const nombre = $('#nombre').val();
              const puesto = $('#puesto').val();
              const fecha = $('#fecha_ingreso').val();

              if (!tienda || !codigo || !nombre || !puesto || !fecha) {
                Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
                return false;
              }

              return {
                tienda: tienda,
                codigo_vendedor: codigo,
                nombre: nombre,
                puesto: puesto,
                fecha_ingreso: fecha,
                activo: $('#activo').is(':checked') ? 1 : 0
              };
            },
            confirmButtonText: '<i class="fas fa-save me-2"></i>Crear Vendedor',
            showCancelButton: true,
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d'
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
                  try {
                    const result = JSON.parse(response);
                    if (result.success) {
                      Swal.fire({
                        title: '¡Éxito!',
                        text: 'Vendedor creado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                      });
                      cargarVendedores();
                    } else {
                      Swal.fire({
                        title: 'Error',
                        text: result.message || 'No se pudo crear el vendedor.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                      });
                    }
                  } catch (e) {
                    Swal.fire({
                      title: 'Error',
                      text: 'Error al procesar la respuesta del servidor.',
                      icon: 'error',
                      confirmButtonColor: '#dc3545'
                    });
                  }
                },
                error: function(xhr, status, error) {
                  Swal.fire({
                    title: 'Error de conexión',
                    text: 'Error al conectar con el servidor: ' + error,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                  });
                }
              });
            }
          });
        });

        // Editar vendedor
        $(document).on('click', '.btnEditar', function () {
          const id = $(this).data('id');
          const vendedor = vendedores.find(v => v.CODIGO_VENDEDOR == id);

          if (!vendedor) {
            Swal.fire({
              title: 'Error',
              text: 'Vendedor no encontrado.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
            return;
          }

          Swal.fire({
            title: '<i class="fas fa-edit me-2"></i>Editar Vendedor',
            html: `
              <div class="text-left">
                <label class="swal2-label">Número de tienda:</label>
                <input type="number" id="tienda" class="swal2-input" value="${vendedor.TIENDA_NO}">
                
                <label class="swal2-label">Código Vendedor:</label>
                <input type="number" id="codigo_vendedor" class="swal2-input" value="${vendedor.CODIGO_VENDEDOR}" readonly style="background-color: #f8f9fa;">
                
                <label class="swal2-label">Nombre completo:</label>
                <input type="text" id="nombre" class="swal2-input" value="${vendedor.NOMBRE}">
                
                <label class="swal2-label">Puesto:</label>
                <select id="puesto" class="swal2-input">
                  <option value="JEFE DE TIENDA" ${vendedor.PUESTO === 'JEFE DE TIENDA' ? 'selected' : ''}>Jefe de Tienda</option>
                  <option value="SUB JEFE DE TIENDA" ${vendedor.PUESTO === 'SUB JEFE DE TIENDA' ? 'selected' : ''}>Sub Jefe de Tienda</option>
                  <option value="ASESOR DE VENTAS" ${vendedor.PUESTO === 'ASESOR DE VENTAS' ? 'selected' : ''}>Asesor de Ventas</option>
                  <option value="VACACIONISTA" ${vendedor.PUESTO === 'VACACIONISTA' ? 'selected' : ''}>Vacacionista</option>
                  <option value="SUPERVISOR" ${vendedor.PUESTO === 'SUPERVISOR' ? 'selected' : ''}>Supervisor</option>
                  <option value="TEMPORAL" ${vendedor.PUESTO === 'TEMPORAL' ? 'selected' : ''}>Temporal</option>
                </select>
                
                <label class="swal2-label">Fecha de Ingreso (DD-MM-YYYY):</label>
                <input type="text" id="fecha_ingreso" class="swal2-input" value="${vendedor.FECHA_INGRESO}">
              </div>
            `,
            width: 600,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-save me-2"></i>Guardar Cambios',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
            confirmButtonColor: '#4a90e2',
            cancelButtonColor: '#6c757d',
            preConfirm: () => {
              const tienda = $('#tienda').val();
              const nombre = $('#nombre').val();
              const puesto = $('#puesto').val();
              const fecha = $('#fecha_ingreso').val();

              if (!tienda || !nombre || !puesto || !fecha) {
                Swal.showValidationMessage('Por favor, completa todos los campos');
                return false;
              }

              return {
                tienda: tienda,
                codigo_vendedor: $('#codigo_vendedor').val(),
                nombre: nombre,
                puesto: puesto,
                fecha_ingreso: fecha
              };
            }
          }).then((result) => {
            if (result.isConfirmed) {
              const { tienda, codigo_vendedor, nombre, puesto, fecha_ingreso } = result.value;

              $.ajax({
                url: './supervision/crudVendedores.php?action=update_employee',
                type: 'POST',
                data: {
                  tienda_no: tienda,
                  codigo_vendedor: codigo_vendedor,
                  nombre: nombre,
                  puesto: puesto,
                  fecha_ingreso: fecha_ingreso,
                  activo: vendedor.ACTIVO === 'Sí' ? 1 : 0
                },
                success: function(response) {
                  try {
                    const result = JSON.parse(response);
                    if (result.success) {
                      Swal.fire({
                        title: '¡Éxito!',
                        text: 'Vendedor actualizado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                      });
                      cargarVendedores();
                    } else {
                      Swal.fire({
                        title: 'Error',
                        text: 'No se pudo actualizar el vendedor: ' + (result.error || 'Error desconocido'),
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                      });
                    }
                  } catch (e) {
                    Swal.fire({
                      title: 'Error',
                      text: 'La respuesta del servidor no es válida.',
                      icon: 'error',
                      confirmButtonColor: '#dc3545'
                    });
                    console.error('Error parsing JSON:', response);
                  }
                },
                error: function(xhr, status, error) {
                  Swal.fire({
                    title: 'Error de conexión',
                    text: 'Error al conectar con el servidor: ' + error,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                  });
                }
              });
            }
          });
        });

        // Toggle status vendedor
        $(document).on('click', '.btnToggleStatus', function () {
          const id = $(this).data('id');
          const vendedor = vendedores.find(v => v.CODIGO_VENDEDOR == id);
          
          if (!vendedor) {
            Swal.fire({
              title: 'Error',
              text: 'Vendedor no encontrado.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
            return;
          }

          const nuevoEstado = vendedor.ACTIVO === 'Sí' ? 0 : 1;
          const accion = nuevoEstado === 1 ? 'activar' : 'desactivar';
          const icon = nuevoEstado === 1 ? 'fas fa-toggle-on' : 'fas fa-toggle-off';
          const color = nuevoEstado === 1 ? '#28a745' : '#ffc107';

          Swal.fire({
            title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} vendedor?`,
            html: `
              <div class="text-center mb-3">
                <i class="${icon} fa-3x" style="color: ${color}"></i>
              </div>
              <p>¿Estás seguro de ${accion} a <strong>${vendedor.NOMBRE}</strong>?</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#6c757d',
            confirmButtonText: `<i class="fas fa-check me-2"></i>Sí, ${accion}`,
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
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
                    Swal.fire({
                      title: '¡Éxito!',
                      text: `El vendedor ha sido ${accion}do correctamente.`,
                      icon: 'success',
                      confirmButtonColor: '#28a745'
                    });
                    cargarVendedores();
                  } else {
                    Swal.fire({
                      title: 'Error',
                      text: 'No se pudo actualizar el estado.',
                      icon: 'error',
                      confirmButtonColor: '#dc3545'
                    });
                  }
                },
                error: function(xhr, status, error) {
                  Swal.fire({
                    title: 'Error de conexión',
                    text: 'Error al conectar con el servidor: ' + error,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                  });
                }
              });
            }
          });
        });

        // Eliminar vendedor
        $(document).on('click', '.btnEliminar', function () {
          const id = $(this).data('id');
          const vendedor = vendedores.find(v => v.CODIGO_VENDEDOR == id);
          
          if (!vendedor) {
            Swal.fire({
              title: 'Error',
              text: 'Vendedor no encontrado.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
            return;
          }

          Swal.fire({
            title: '¿Eliminar vendedor?',
            html: `
              <div class="text-center mb-3">
                <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
              </div>
              <p>¿Estás seguro de eliminar a <strong>${vendedor.NOMBRE}</strong>?</p>
              <p class="text-muted small">Esta acción no se puede deshacer.</p>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Sí, eliminar',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: './supervision/crudVendedores.php?action=delete_employee',
                type: 'POST',
                data: {
                  codigo_vendedor: id
                },
                success: function (response) {
                  try {
                    const result = JSON.parse(response);
                    if (result.success) {
                      Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El vendedor ha sido eliminado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                      });
                      cargarVendedores();
                    } else {
                      Swal.fire({
                        title: 'Error',
                        text: result.message || 'No se pudo eliminar el vendedor.',
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                      });
                    }
                  } catch (e) {
                    Swal.fire({
                      title: 'Error',
                      text: 'Error al procesar la respuesta del servidor.',
                      icon: 'error',
                      confirmButtonColor: '#dc3545'
                    });
                  }
                },
                error: function(xhr, status, error) {
                  Swal.fire({
                    title: 'Error de conexión',
                    text: 'Error al conectar con el servidor: ' + error,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                  });
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