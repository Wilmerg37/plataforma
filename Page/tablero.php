<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Profesional</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="../Css/estilodashboard.css">
</head>
<body>
    <div class="container-fluid">
        <div class="dashboard-container">
            <!-- Header Stats -->
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="stat-box stat-box-info">
                        <div class="stat-inner">
                            <div class="stat-number Trafico">---</div>
                            <div class="stat-label">Tráfico Diario Cadena</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-traffic-light"></i>
                        </div>
                        <a href="#" id="trafico" class="stat-footer">
                            Más información <i class="fas fa-arrow-circle-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="stat-box stat-box-success">
                        <div class="stat-inner">
                            <div class="stat-number">53<sup style="font-size: 1.2rem">%</sup></div>
                            <div class="stat-label">Conversión Cadena</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="stat-footer">
                            Más información <i class="fas fa-arrow-circle-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 fade-in-up" style="animation-delay: 0.3s;">
                    <div class="stat-box stat-box-warning">
                        <div class="stat-inner">
                            <div class="stat-number">44</div>
                            <div class="stat-label">Meta Cadena</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <a href="#" class="stat-footer">
                            Más información <i class="fas fa-arrow-circle-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 fade-in-up" style="animation-delay: 0.4s;">
                    <div class="stat-box stat-box-danger">
                        <div class="stat-inner">
                            <div class="stat-number">65</div>
                            <div class="stat-label">Venta Cadena</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <a href="#" class="stat-footer">
                            Más información <i class="fas fa-arrow-circle-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-6">
                    <!-- Quarterly Comparison Chart -->
                    <div class="dashboard-card fade-in-up" style="animation-delay: 0.5s;">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-chart-pie"></i>
                                Comparativo Trimestral Cadena <span id="current-year">2024</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-bs-toggle="collapse" data-bs-target="#chart1">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body-custom collapse show" id="chart1">
                            <div class="chart-container">
                                <canvas id="popChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Regional RDS Chart -->
                    <div class="dashboard-card fade-in-up" style="animation-delay: 0.6s;">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-map-marked-alt"></i>
                                RDS Región Semana: <span id="current-week">35</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-bs-toggle="collapse" data-bs-target="#chart2">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body-custom collapse show" id="chart2">
                            <div class="chart-container">
                                <canvas id="popChart1"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Todo List -->
                    <div class="dashboard-card fade-in-up" style="animation-delay: 0.7s;">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-tasks"></i>
                                Lista de Tareas
                            </h3>
                            <div class="card-tools">
                                <ul class="pagination pagination-custom pagination-sm">
                                    <li class="page-item"><a href="#" class="page-link">&laquo;</a></li>
                                    <li class="page-item"><a href="#" class="page-link">1</a></li>
                                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                                    <li class="page-item"><a href="#" class="page-link">3</a></li>
                                    <li class="page-item"><a href="#" class="page-link">&raquo;</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body-custom">
                            <ul class="todo-list">
                                <li class="todo-item">
                                    <div class="todo-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="todo-checkbox">
                                        <input class="form-check-input" type="checkbox" id="todoCheck1">
                                    </div>
                                    <span class="todo-text">Diseñar un tema elegante</span>
                                    <span class="badge bg-danger todo-badge">
                                        <i class="far fa-clock me-1"></i>2 mins
                                    </span>
                                    <div class="todo-tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash"></i>
                                    </div>
                                </li>
                                <li class="todo-item">
                                    <div class="todo-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="todo-checkbox">
                                        <input class="form-check-input" type="checkbox" id="todoCheck2" checked>
                                    </div>
                                    <span class="todo-text">Hacer el tema responsive</span>
                                    <span class="badge bg-info todo-badge">
                                        <i class="far fa-clock me-1"></i>4 horas
                                    </span>
                                    <div class="todo-tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash"></i>
                                    </div>
                                </li>
                                <li class="todo-item">
                                    <div class="todo-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="todo-checkbox">
                                        <input class="form-check-input" type="checkbox" id="todoCheck3">
                                    </div>
                                    <span class="todo-text">Hacer brillar el tema como una estrella</span>
                                    <span class="badge bg-warning todo-badge">
                                        <i class="far fa-clock me-1"></i>1 día
                                    </span>
                                    <div class="todo-tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash"></i>
                                    </div>
                                </li>
                                <li class="todo-item">
                                    <div class="todo-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="todo-checkbox">
                                        <input class="form-check-input" type="checkbox" id="todoCheck4">
                                    </div>
                                    <span class="todo-text">Optimizar rendimiento</span>
                                    <span class="badge bg-success todo-badge">
                                        <i class="far fa-clock me-1"></i>3 días
                                    </span>
                                    <div class="todo-tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash"></i>
                                    </div>
                                </li>
                                <li class="todo-item">
                                    <div class="todo-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="todo-checkbox">
                                        <input class="form-check-input" type="checkbox" id="todoCheck5">
                                    </div>
                                    <span class="todo-text">Revisar mensajes y notificaciones</span>
                                    <span class="badge bg-primary todo-badge">
                                        <i class="far fa-clock me-1"></i>1 semana
                                    </span>
                                    <div class="todo-tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash"></i>
                                    </div>
                                </li>
                                <li class="todo-item">
                                    <div class="todo-handle">
                                        <i class="fas fa-grip-vertical"></i>
                                    </div>
                                    <div class="todo-checkbox">
                                        <input class="form-check-input" type="checkbox" id="todoCheck6">
                                    </div>
                                    <span class="todo-text">Planificar próximas funcionalidades</span>
                                    <span class="badge bg-secondary todo-badge">
                                        <i class="far fa-clock me-1"></i>1 mes
                                    </span>
                                    <div class="todo-tools">
                                        <i class="fas fa-edit"></i>
                                        <i class="fas fa-trash"></i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent text-end">
                            <button type="button" class="btn btn-add-item">
                                <i class="fas fa-plus me-2"></i>Agregar Tarea
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-6">
                    <!-- Regions Chart -->
                    <div class="dashboard-card card-success fade-in-up" style="animation-delay: 0.8s;">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-globe-americas"></i>
                                Regiones <span id="current-year2">2024</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-bs-toggle="collapse" data-bs-target="#chart3">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body-custom collapse show" id="chart3">
                            <div class="chart-container">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top 5 Sales Chart -->
                    <div class="dashboard-card card-danger fade-in-up" style="animation-delay: 0.9s;">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">
                                <i class="fas fa-trophy"></i>
                                Top 5 Ventas Semana <span id="current-week2"> 35 </span> por Proveedor <span id="current-year3"> 2025</span>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-bs-toggle="collapse" data-bs-target="#chart4">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body-custom collapse show" id="chart4">
                            <div class="chart-container">
                                <canvas id="donutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalTablero" tabindex="-1" aria-labelledby="modalTableroLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTableroLabel">
                        <i class="fas fa-info-circle me-2"></i>Información Detallada
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

 <script>
  var url = "../Js/tablero/dashboard.js";
  $.getScript(url);
</script>


    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <script>
        // Initialize current date/week/year
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();
            const currentWeek = getWeekNumber(new Date());
            
            document.getElementById('current-year').textContent = currentYear;
            document.getElementById('current-year2').textContent = currentYear;
            document.getElementById('current-year3').textContent = currentYear;
            document.getElementById('current-week').textContent = currentWeek;
            document.getElementById('current-week2').textContent = currentWeek;
        });

        // Get week number function
        function getWeekNumber(date) {
            const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
            const pastDaysOfYear = (date - firstDayOfYear) / 86400000;
            return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
        }

        // Load your original dashboard.js
        var url = "../Js/tablero/dashboard.js";
        $.getScript(url);

        // Add smooth scrolling and interactions
        document.querySelectorAll('.stat-footer, .todo-tools i, .btn-tool').forEach(element => {
            element.addEventListener('click', function(e) {
                // Add your click handlers here
                console.log('Element clicked:', e.target);
            });
        });

        // Add sortable functionality to todo list (requires additional library)
        // You can integrate with Sortable.js if needed

        // Chart.js placeholder - you'll need to integrate with your existing charts
        const ctx1 = document.getElementById('popChart');
        const ctx2 = document.getElementById('popChart1');
        const ctx3 = document.getElementById('barChart');
        const ctx4 = document.getElementById('donutChart');

        // Example chart initialization (replace with your actual chart data)
        if (ctx1) {
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['Q1', 'Q2', 'Q3', 'Q4'],
                    datasets: [{
                        data: [12, 19, 3, 5],
                        backgroundColor: ['#3498db', '#27ae60', '#f39c12', '#e74c3c']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    </script>

   

</body>
</html>