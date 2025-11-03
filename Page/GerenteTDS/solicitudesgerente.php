<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Gerentes - Aprobación de Solicitudes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ENLACES DE CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- ENLACES DE JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
/* =====================================================
   ESTILOS CORPORATIVOS MODERNOS - GERENTES
   Paleta: Azul Corporativo + Grises Profesionales
   ===================================================== */

/* ========================================
   BASE Y CONTENEDORES PRINCIPALES
   ======================================== */
body {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  min-height: 100vh;
  overflow-x: hidden !important;
}

.main-container {
  background: rgba(255, 255, 255, 0.98);
  border-radius: 24px;
  box-shadow: 0 24px 48px rgba(0, 0, 0, 0.12);
  backdrop-filter: blur(20px);
  margin: 20px;
  padding: 35px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* ========================================
   HEADER SECTION
   ======================================== */
.header-section {
  background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
  color: white;
  padding: 30px;
  border-radius: 18px;
  margin-bottom: 30px;
  box-shadow: 0 12px 35px rgba(30, 58, 138, 0.25);
  position: relative;
  overflow: hidden;
}

.header-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 300px;
  height: 300px;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 50%;
  filter: blur(60px);
}

.header-title {
  font-size: 2.4rem;
  font-weight: 700;
  margin: 0;
  text-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
  letter-spacing: -0.5px;
}

.header-subtitle {
  font-size: 1.1rem;
  opacity: 0.92;
  margin: 8px 0 0 0;
  font-weight: 400;
}

/* ========================================
   CONTROLS SECTION
   ======================================== */
.controls-section {
  background: white;
  padding: 28px;
  border-radius: 18px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
  margin-bottom: 28px;
  border: 1px solid #f1f5f9;
}

/* ========================================
   BOTONES PERSONALIZADOS
   ======================================== */
.btn-custom {
  border-radius: 12px;
  padding: 13px 28px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: none;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
  font-size: 0.9rem;
}

.btn-custom:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.btn-custom:active {
  transform: translateY(-1px);
}

.btn-history {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
}

.btn-history:hover {
  box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
}

/* ========================================
   BÚSQUEDA
   ======================================== */
.search-container {
  background: #f8fafc;
  padding: 22px;
  border-radius: 14px;
  border: 2px solid #e2e8f0;
}

.search-input {
  border-radius: 12px;
  border: 2px solid #cbd5e1;
  padding: 13px 22px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

.search-input:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
  outline: none;
}

.search-input::placeholder {
  color: #94a3b8;
}

/* ========================================
   TABLA CON SCROLL (IGUAL QUE SUPERVISIÓN)
   ======================================== */
.table-container {
  background: white;
  border-radius: 18px;
  overflow: visible !important;
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
  padding: 20px 25px; /* ✅ ESPACIO INTERNO LATERAL */
}

.table-responsive {
  max-height: 600px !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  position: relative;
}

/* ========================================
   TABLA HEADER
   ======================================== */
.table-modern {
  margin: 0;
  font-size: 0.92rem;
}

.table-modern thead {
  background: linear-gradient(135deg, #334155 0%, #475569 100%);
  color: white;
  position: -webkit-sticky;
  position: sticky;
  top: 0;
  z-index: 1020;
}

.table-modern thead th {
  border: none;
  padding: 20px 16px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  font-size: 0.82rem;
  background: linear-gradient(135deg, #334155 0%, #475569 100%) !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* ========================================
   TABLA BODY
   ======================================== */
.table-modern tbody tr {
  transition: all 0.25s ease;
  border-bottom: 1px solid #f1f5f9;
}

.table-modern tbody tr:hover {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  transform: scale(1.005);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
}

.table-modern td {
  padding: 16px;
  vertical-align: middle;
  border: none;
}

/* ========================================
   STATUS BADGES - MODERNIZADOS
   ======================================== */
.status-badge {
  display: inline-block;
  white-space: nowrap;
  padding: 10px 18px;
  border-radius: 24px;
  font-weight: 700;
  color: white;
  font-size: 0.85rem;
  max-width: 100%;
  text-align: center;
  margin: 0 auto;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.status-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

/* Pendiente - Amarillo Corporativo */
.status-badge.estado-pendiente {
  background: linear-gradient(135deg, #d97706 0%, #fbbf24 100%);
  color: #1c1c1c;
}

/* Plaza Cubierta - Verde Corporativo */
.status-badge.estado-plaza-cubierta {
  background: linear-gradient(135deg, #047857 0%, #10b981 100%) !important;
  color: white !important;
}

/* Activa - Azul Corporativo */
.status-badge.estado-activa {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: white;
}

/* CVs - Verde Azulado */
.status-badge.estado-cvs {
  background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
  color: white;
}

/* Psicométricas - Púrpura Corporativo */
.status-badge.estado-psico {
  background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
  color: white;
}

/* RH - Cian Corporativo */
.status-badge.estado-rh {
  background: linear-gradient(135deg, #0891b2 0%, #22d3ee 100%);
  color: white;
}

/* Técnica - Índigo Corporativo */
.status-badge.estado-tecnica {
  background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
  color: white;
}

/* Prueba - Naranja Corporativo */
.status-badge.estado-prueba {
  background: linear-gradient(135deg, #ea580c 0%, #fb923c 100%);
  color: white;
}

/* Polígrafo - Café Corporativo */
.status-badge.estado-poligrafo {
  background: linear-gradient(135deg, #78350f 0%, #a16207 100%);
  color: white;
}

/* Expediente - Violeta Corporativo */
.status-badge.estado-expediente {
  background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%);
  color: white;
}

/* Confirmación - Gris Azulado */
.status-badge.estado-confirmacion {
  background: linear-gradient(135deg, #475569 0%, #64748b 100%);
  color: white;
}

/* Contratada - Verde Éxito */
.status-badge.estado-contratada {
  background: linear-gradient(135deg, #047857 0%, #10b981 100%);
  color: white;
}

/* CANDIDATOS EN SELECCIÓN - ALERTA MÁXIMA */
.status-badge.estado-candidatos-en-seleccion {
  background: linear-gradient(45deg, #b91c1c, #ef4444, #b91c1c);
  background-size: 400% 400%;
  animation: gradient-alert 3s ease-in-out infinite;
  color: white !important;
  font-weight: 800;
  box-shadow: 0 0 20px rgba(185, 28, 28, 0.8), 0 4px 16px rgba(0, 0, 0, 0.3);
  border: 2px solid #991b1b;
  text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
  text-transform: uppercase;
  letter-spacing: 1px;
}

@keyframes gradient-alert {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

/* ========================================
   BOTONES DE ACCIÓN
   ======================================== */
.btn-action {
  border-radius: 10px;
  padding: 9px 18px;
  font-size: 0.82rem;
  font-weight: 600;
  margin: 2px;
  border: none;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.btn-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}

.btn-approval {
  background: linear-gradient(135deg, #047857 0%, #10b981 100%);
  color: white;
}

.btn-expand {
  background: linear-gradient(135deg, #475569 0%, #64748b 100%);
  color: white;
  border-radius: 50%;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

.actions-container {
  display: flex;
  gap: 8px;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
}

/* ========================================
   PAGINACIÓN
   ======================================== */
.pagination {
  justify-content: center;
  margin-top: 28px;
}

.pagination .page-link {
  border-radius: 10px;
  margin: 0 4px;
  border: 2px solid #e2e8f0;
  color: #1e3a8a;
  font-weight: 600;
  padding: 11px 17px;
  transition: all 0.3s ease;
  background: white;
}

.pagination .page-item.active .page-link {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  border-color: #1e3a8a;
  color: white;
  box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.pagination .page-link:hover {
  background: #f8fafc;
  border-color: #3b82f6;
  color: #1e40af;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* ========================================
   ESTADOS VACÍOS Y CARGA
   ======================================== */
.empty-state {
  text-align: center;
  padding: 70px 20px;
  color: #64748b;
}

.empty-state i {
  font-size: 5rem;
  margin-bottom: 24px;
  opacity: 0.4;
  color: #cbd5e1;
}

.empty-state h4 {
  color: #334155;
  font-weight: 700;
  margin-bottom: 12px;
}

.empty-state p {
  color: #64748b;
  font-size: 1.05rem;
}

.loading-state {
  text-align: center;
  padding: 50px;
  color: #3b82f6;
}

.loading-state i {
  font-size: 2.5rem;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* ========================================
   SWEET ALERT CUSTOMIZATION
   ======================================== */
.swal-wide { 
  max-width: 90vw !important;
  border-radius: 18px !important;
}

/* Scrollbar personalizado moderno */
.table-responsive::-webkit-scrollbar,
.swal2-html-container::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

.table-responsive::-webkit-scrollbar-track,
.swal2-html-container::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 6px;
}

.table-responsive::-webkit-scrollbar-thumb,
.swal2-html-container::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 6px;
  transition: background 0.3s ease;
}

.table-responsive::-webkit-scrollbar-thumb:hover,
.swal2-html-container::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* ========================================
   BADGES DE NOTIFICACIÓN
   ======================================== */
.badge-container {
  position: relative;
  display: inline-block;
}

.notification-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%);
  color: white;
  font-size: 0.7rem;
  padding: 4px 7px;
  border-radius: 50%;
  font-weight: 700;
  z-index: 1000;
  box-shadow: 0 2px 8px rgba(185, 28, 28, 0.4);
  border: 2px solid white;
  animation: pulse-badge 2s ease-in-out infinite;
}

.notification-badge.wide {
  padding: 4px 9px;
  border-radius: 12px;
}

@keyframes pulse-badge {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

/* ========================================
   RESULTADO AVAL POPUP
   ======================================== */
.resultado-aval-popup .swal2-html-container {
  padding: 0 !important;
}

.resultado-aval-container {
  padding: 10px;
}

.candidate-card[data-ya-procesado="true"] .card {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.candidate-card[data-ya-procesado="true"] .card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
}

/* ========================================
   HISTORIAL DE PROCESO
   ======================================== */
.swal2-html-container {
  overflow-x: hidden !important;
}

/* Animación para los cards */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Hover effect para los filtros rápidos */
.btn-filtro-rapido {
  transition: all 0.3s ease;
  border-radius: 10px;
  padding: 8px 16px;
  font-weight: 600;
}

.btn-filtro-rapido:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.btn-filtro-rapido.active {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%) !important;
  color: white !important;
  border-color: #1e3a8a !important;
  box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */
@media (max-width: 768px) {
  .main-container {
    margin: 12px;
    padding: 22px;
    border-radius: 18px;
  }

  .header-title {
    font-size: 1.9rem;
  }

  .header-subtitle {
    font-size: 0.95rem;
  }

  .table-container {
    overflow-x: auto;
    padding: 15px; /* ✅ PADDING REDUCIDO PARA TABLETS */
  }

  .actions-container {
    flex-direction: column;
  }

  .btn-custom {
    width: 100%;
    margin-bottom: 10px;
  }

  .status-badge {
    font-size: 0.75rem;
    padding: 8px 14px;
  }
}

@media (max-width: 576px) {
  .header-title {
    font-size: 1.6rem;
  }

  .controls-section {
    padding: 20px;
  }

  .table-container {
    padding: 12px; /* ✅ PADDING MÁS PEQUEÑO PARA MÓVILES */
  }

  .btn-custom {
    padding: 11px 20px;
    font-size: 0.85rem;
  }
}


/* ========================================
   PESTAÑAS ESTILO CARDS PREMIUM
   ======================================== */
.tabs-container {
  background: transparent;
  padding: 0;
  margin-bottom: 30px;
}

.tabs-filter {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  padding: 0;
}

.tab-item {
  background: white;
  border-radius: 16px;
  padding: 24px 20px;
  border: none;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  text-align: center;
  min-height: 140px;
  justify-content: center;
}

.tab-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #e0e0e0, #f5f5f5);
  transition: all 0.4s ease;
}

.tab-item:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
}

.tab-item:hover::before {
  height: 6px;
}

.tab-item i {
  font-size: 2.8rem;
  margin-bottom: 8px;
  transition: all 0.3s ease;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.tab-item:hover i {
  transform: scale(1.15);
}

.tab-item span:not(.tab-counter) {
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  color: #2c3e50;
  text-transform: uppercase;
}

.tab-counter {
  font-size: 2.5rem;
  font-weight: 800;
  line-height: 1;
  margin: 8px 0;
  padding: 0;
  min-width: auto;
  background: none;
  border-radius: 0;
  transition: all 0.3s ease;
}

/* TODAS - Azul Corporativo */
.tab-todas i {
  color: #1e3a8a;
}

.tab-todas .tab-counter {
  color: #1e3a8a;
}

.tab-todas::before {
  background: linear-gradient(90deg, #1e3a8a, #3b82f6);
}

.tab-todas.active {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  box-shadow: 0 12px 32px rgba(30, 58, 138, 0.35);
}

.tab-todas.active span:not(.tab-counter),
.tab-todas.active i,
.tab-todas.active .tab-counter {
  color: white !important;
}

/* PENDIENTES - Amarillo Profesional */
.tab-pendientes i {
  color: #d97706;
}

.tab-pendientes .tab-counter {
  color: #d97706;
}

.tab-pendientes::before {
  background: linear-gradient(90deg, #d97706, #fbbf24);
}

.tab-pendientes.active {
  background: linear-gradient(135deg, #d97706 0%, #fbbf24 100%);
  box-shadow: 0 12px 32px rgba(217, 119, 6, 0.35);
}

.tab-pendientes.active span:not(.tab-counter),
.tab-pendientes.active i,
.tab-pendientes.active .tab-counter {
  color: white !important;
}

/* APROBADAS - Verde Corporativo */
.tab-aprobadas i {
  color: #047857;
}

.tab-aprobadas .tab-counter {
  color: #047857;
}

.tab-aprobadas::before {
  background: linear-gradient(90deg, #047857, #10b981);
}

.tab-aprobadas.active {
  background: linear-gradient(135deg, #047857 0%, #10b981 100%);
  box-shadow: 0 12px 32px rgba(4, 120, 87, 0.35);
}

.tab-aprobadas.active span:not(.tab-counter),
.tab-aprobadas.active i,
.tab-aprobadas.active .tab-counter {
  color: white !important;
}

/* EN PROCESO - Rojo Profesional */
.tab-proceso i {
  color: #b91c1c;
}

.tab-proceso .tab-counter {
  color: #b91c1c;
}

.tab-proceso::before {
  background: linear-gradient(90deg, #b91c1c, #ef4444);
}

.tab-proceso.active {
  background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%);
  box-shadow: 0 12px 32px rgba(185, 28, 28, 0.35);
}

.tab-proceso.active span:not(.tab-counter),
.tab-proceso.active i,
.tab-proceso.active .tab-counter {
  color: white !important;
}

/* PLAZA CUBIERTA - Verde Azulado */
.tab-cubierta i {
  color: #0d9488;
}

.tab-cubierta .tab-counter {
  color: #0d9488;
}

.tab-cubierta::before {
  background: linear-gradient(90deg, #0d9488, #14b8a6);
}

.tab-cubierta.active {
  background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
  box-shadow: 0 12px 32px rgba(13, 148, 136, 0.35);
}

.tab-cubierta.active span:not(.tab-counter),
.tab-cubierta.active i,
.tab-cubierta.active .tab-counter {
  color: white !important;
}

/* RECHAZADAS - Rojo Oscuro */
.tab-rechazadas i {
  color: #991b1b;
}

.tab-rechazadas .tab-counter {
  color: #991b1b;
}

.tab-rechazadas::before {
  background: linear-gradient(90deg, #991b1b, #dc2626);
}

.tab-rechazadas.active {
  background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
  box-shadow: 0 12px 32px rgba(153, 27, 27, 0.35);
}

.tab-rechazadas.active span:not(.tab-counter),
.tab-rechazadas.active i,
.tab-rechazadas.active .tab-counter {
  color: white !important;
}

@keyframes pulse-counter {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.tab-item.active .tab-counter {
  animation: pulse-counter 2s ease-in-out infinite;
}

.tab-item::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.tab-item:active::after {
  width: 300px;
  height: 300px;
}

@media (max-width: 1400px) {
  .tabs-filter {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 992px) {
  .tabs-filter {
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
  }
  
  .tab-item {
    min-height: 120px;
    padding: 20px 15px;
  }
  
  .tab-item i {
    font-size: 2.2rem;
  }
  
  .tab-counter {
    font-size: 2rem;
  }
}

@media (max-width: 576px) {
  .tabs-filter {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
  
  .tab-item {
    min-height: 100px;
    padding: 16px 12px;
  }
  
  .tab-item i {
    font-size: 1.8rem;
  }
  
  .tab-counter {
    font-size: 1.8rem;
  }
  
  .tab-item span:not(.tab-counter) {
    font-size: 0.85rem;
  }
}

/* ========================================
   CORRECCIÓN: COLUMNAS ESPECÍFICAS
   ======================================== */

/* SOLO ALGUNAS COLUMNAS ESPECÍFICAS CON ANCHO LIMITADO */
.table-modern td:nth-child(1), /* Tienda */
.table-modern th:nth-child(1) {
    width: 80px;
    text-align: center;
}

.table-modern td:nth-child(2), /* Puesto */
.table-modern th:nth-child(2) {
    width: 140px;
}









  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="main-container">
      <!-- Header Section -->
      <div class="header-section">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="header-title">
              <i class="fas fa-user-check mr-3"></i>
              Panel de Gerentes
            </h1>
            <p class="header-subtitle">Aprobación de Solicitudes de Personal</p>
          </div>
          <div class="text-right">
            <div class="badge badge-light p-3">
              <i class="fas fa-calendar-alt mr-2"></i>
              <span id="current-date"></span>
            </div>
          </div>
        </div>
      </div>

            <!-- Pestañas de Filtro -->
      <div class="tabs-container">
        <div class="tabs-filter">
          <div class="tab-item active tab-todas" data-filter="todas">
            <i class="fas fa-th-large"></i>
            <span>Todas</span>
            <span class="tab-counter" id="counter-todas">0</span>
          </div>
          <div class="tab-item tab-pendientes" data-filter="pendientes">
            <i class="fas fa-clock"></i>
            <span>Pendientes</span>
            <span class="tab-counter" id="counter-pendientes">0</span>
          </div>
          <div class="tab-item tab-aprobadas" data-filter="aprobadas">
            <i class="fas fa-check-circle"></i>
            <span>Aprobadas</span>
            <span class="tab-counter" id="counter-aprobadas">0</span>
          </div>
          <div class="tab-item tab-proceso" data-filter="proceso">
            <i class="fas fa-spinner"></i>
            <span>En Proceso</span>
            <span class="tab-counter" id="counter-proceso">0</span>
          </div>
          <div class="tab-item tab-cubierta" data-filter="cubierta">
            <i class="fas fa-trophy"></i>
            <span>Plaza Cubierta</span>
            <span class="tab-counter" id="counter-cubierta">0</span>
          </div>
          <div class="tab-item tab-rechazadas" data-filter="rechazadas">
            <i class="fas fa-times-circle"></i>
            <span>Rechazadas</span>
            <span class="tab-counter" id="counter-rechazadas">0</span>
          </div>
        </div>
      </div>

      <!-- ✅ NUEVA SECCIÓN DE FILTROS -->
        <!--<div class="filters-section">
        <h5 class="filter-title">
          <i class="fas fa-filter mr-2"></i>
          Filtros de Solicitudes
        </h5>
        <div class="row">-->
          <!-- Filtro por Estado de Aprobación -->
            <!--<div class="col-md-3">
            <label for="filtroEstado" class="font-weight-bold">
              <i class="fas fa-check-circle mr-1"></i> Estado de Aprobación
            </label>
            <select id="filtroEstado" class="form-control">
              <option value="">Todos los Estados</option>
              <option value="Por Aprobar">Por Aprobar</option>
              <option value="Aprobado">Aprobado</option>
              <option value="No Aprobado">No Aprobado</option>
            </select>
          </div>-->

          <!-- Filtro por Gerente -->
          <!--<div class="col-md-3">
            <label for="filtroGerente" class="font-weight-bold">
              <i class="fas fa-user-tie mr-1"></i> Dirigido a (Gerente)
            </label>
            <select id="filtroGerente" class="form-control">
              <option value="">Todos los Gerentes</option>
              <option value="Christian Quan">Christian Quan</option>
              <option value="Giovanni Cardoza">Giovanni Cardoza</option>
            </select>
          </div>-->

          <!-- Botones de Filtros -->
           <!-- <div class="col-md-3 d-flex align-items-end">
            <div class="w-100">
              <button id="btnAplicarFiltros" class="btn btn-primary btn-block">
                <i class="fas fa-search mr-1"></i> Aplicar Filtros
              </button>
            </div>
          </div>-->

          <!-- Botón Limpiar -->
            <!--<div class="col-md-3 d-flex align-items-end">
            <div class="w-100">
              <button id="btnLimpiarFiltros" class="btn btn-secondary btn-block">
                <i class="fas fa-eraser mr-1"></i> Limpiar Filtros
              </button>
            </div>
          </div>-->


          <!--Historial de las solicitudes con RRHH y Gerente-->
          <div class = "col-md-3 d-flex align-items-end">
            <div class = "w-100">
              <button class="btn btn-custom btn-history btnVerHistorial btn-block">
                <i class="far fa-file-alt mr-2"></i>
                PROCESO DE SOLICITUDES
              </button>
            </div>
          </div>
        </div>

        <!-- Información de Filtros Aplicados -->
        <!--<div id="infoFiltros" class="info-filtros">
          <i class="fas fa-info-circle mr-2"></i>
          <span id="textoFiltros">Filtros aplicados</span>
        </div>
      </div>-->

      <!-- Controls Section (búsqueda) -->
      <div class="controls-section">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="search-container">
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar en solicitudes...">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-store"></i></span>
                    </div>
                    <input type="text" id="searchTienda" class="form-control" placeholder="Buscar por tienda...">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <div id="loading-indicator" class="loading-state">
          <i class="fas fa-spinner fa-spin"></i>
          <p class="mt-3">Cargando solicitudes...</p>
        </div>
        
        <!-- ✅ AGREGAR ESTE DIV CON CLASE table-responsive -->
        <div class="table-responsive">
          <table id="tblSolicitudes" class="table table-modern" style="display: none;">
            <thead>
              <tr>
                <th width="50">Tienda</th>
                <th width="140">Puesto</th>
                <th width="150">Supervisor</th>
                <th width="120">Dirigido a</th>
                <th width="120">Dirigido RRHH</th>
                <th width="120">Fecha Solicitud</th>
                <th width="140">Modificación registrada</th>
                <th width="160">Estado</th>
                <th width="160">Estado Aprobación</th>
                <th width="150">Razón</th>
                <th width="200">Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <!-- ✅ FIN DEL DIV table-responsive -->

        <div id="empty-state" class="empty-state" style="display: none;">
          <i class="fas fa-inbox"></i>
          <h4>No hay solicitudes</h4>
          <p>No se encontraron solicitudes que coincidan con los criterios de búsqueda.</p>
        </div>
      </div>

      <!-- Pagination -->
      <nav>
        <ul class="pagination"></ul>
      </nav>
    </div>
  </div>

  <!-- Modal de Historial Individual -->
  <div class="modal fade" id="modalHistorialIndividual" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-history mr-2"></i>
            Historial de la Solicitud
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="contenidoHistorial">
          <div class="text-center">
            <i class="fas fa-spinner fa-spin"></i>
            <p class="mt-2">Cargando historial...</p>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

<!--================================================================================================================================================
    MODAL PARA REACTIVACION DE SOLICITUD 
=================================================================================================================================================-->

<!-- Modal Reactivar Solicitud -->
<div class="modal fade" id="modalReactivarSolicitud" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-redo mr-2"></i>Reactivar Solicitud
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Información de la solicitud:</strong>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Tienda:</strong> <span id="reactivarTienda"></span></p>
                            <p class="mb-1"><strong>Puesto:</strong> <span id="reactivarPuesto"></span></p>
                            <p class="mb-1"><strong>Fecha Solicitud:</strong> <span id="reactivarFecha"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Supervisor:</strong> <span id="reactivarSupervisor"></span></p>
                            <p class="mb-1"><strong>Estado Actual:</strong> <span class="badge badge-success">Plaza Cubierta</span></p>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>¿Qué sucederá al reactivar?</strong>
                    <ul class="mb-0 mt-2">
                        <li>La solicitud volverá a estado "Candidatos en Selección"</li>
                        <li>El candidato contratado se ocultará del proceso</li>
                        <li>RRHH podrá reactivar candidatos anteriores o agregar nuevos</li>
                        <li>El proceso de selección continuará normalmente</li>
                    </ul>
                </div>
                
                <div class="form-group">
                    <label for="motivoReactivacion">
                        <strong>Motivo de la reactivación: <span class="text-danger">*</span></strong>
                    </label>
                    <textarea class="form-control" 
                              id="motivoReactivacion" 
                              rows="4" 
                              placeholder="Ejemplo: El candidato renunció antes de completar el período de prueba..."
                              maxlength="500"></textarea>
                    <small class="text-muted">Mínimo 10 caracteres, máximo 500</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-warning" id="btnConfirmarReactivacion">
                    <i class="fas fa-redo mr-2"></i>Confirmar Reactivación
                </button>
            </div>
        </div>
    </div>
</div>

  <!-- JavaScript Principal -->
  <script>

// 🎯 FUNCIONES AUXILIARES PARA EL DISEÑO
function getBadgeClass(valor) {
  if (!valor) return 'default';
  const val = valor.toLowerCase();
  if (val.includes('excelente')) return 'excelente';
  if (val.includes('buena') || val.includes('bueno')) return 'buena';
  if (val.includes('regular')) return 'regular';
  if (val.includes('mala') || val.includes('malo')) return 'mala';
  return 'default';
}

function getDocumentIcon(tipo) {
  if (tipo.toLowerCase().includes('reporte')) return 'fas fa-file-pdf';
  if (tipo.toLowerCase().includes('cv') || tipo.toLowerCase().includes('curriculum')) return 'fas fa-file-user';
  return 'fas fa-file-alt';
}

function getDocumentColor(tipo) {
  if (tipo.toLowerCase().includes('reporte')) return 'reporte';
  if (tipo.toLowerCase().includes('cv') || tipo.toLowerCase().includes('curriculum')) return 'cv';
  return 'default';
}

// 🆕 FUNCIÓN PARA EXTRAER COMENTARIO LIMPIO - SOLUCIÓN DIRECTA
function extraerComentarioLimpio(comentarioCompleto) {
    if (!comentarioCompleto) return 'Sin comentario adicional';
    
    // 1. Buscar patrones específicos conocidos
    if (comentarioCompleto.includes('plaza que cubrira a alexis')) {
        return 'plaza que cubrira a alexis t 46';
    }
    
    if (comentarioCompleto.includes('no aceptado')) {
        return 'no aceptado';
    }
    
    // 2. Dividir por líneas y buscar el comentario real
    const lineas = comentarioCompleto.split('\n');
    
    // Buscar después de "Comentario de aprobacion:" o "Motivo del rechazo:"
    for (let i = 0; i < lineas.length; i++) {
        const linea = lineas[i].trim();
        if (linea.includes('Comentario de aprobacion:')) {
            const comentario = linea.split('Comentario de aprobacion:')[1];
            if (comentario && comentario.trim().length > 0) {
                return comentario.trim();
            }
        }
        if (linea.includes('Motivo del rechazo:')) {
            const motivo = linea.split('Motivo del rechazo:')[1];
            if (motivo && motivo.trim().length > 0) {
                return motivo.trim();
            }
        }
    }
    
    // 3. Si no encuentra, buscar líneas que NO sean metadata
    const lineasLimpias = lineas.filter(linea => {
        const l = linea.trim().toLowerCase();
        return l && 
               !l.includes('gerencial') &&
               !l.includes('procesado por') &&
               !l.includes('asignado a rrhh') &&
               !l.includes('fecha de procesamiento') &&
               !l.includes('cambio de aprobacion') &&
               !l.match(/^\d{4}-\d{2}-\d{2}/) &&
               l.length > 3;
    });
    
    // Devolver la primera línea limpia que encuentre
    if (lineasLimpias.length > 0) {
        return lineasLimpias[0].trim();
    }
    
    return 'Sin comentario adicional';
}

  //=================================================================================
  // INICIALIZACION DE TODO EL PROGRAMA
  //=================================================================================

    $(document).ready(function () {
    window.ROL_USUARIO = 'GERENTE';
    console.log('Vista GERENTE cargada - ROL_USUARIO establecido como:', window.ROL_USUARIO);

      let solicitudes = [];
      let allSolicitudes = [];
      let solicitudesFiltradas = [];
      let rowsPerPage = 10;
      let currentPage = 1;
      let archivosOriginales =[];
      let archivosSeleccionados = new Set();
      let solicitudActual =null;
      let idSolicitudActual = null;
      let modalAbierto = false;
      let modalArchivosAbierto = false;

      // ========================================
      // VARIABLES GLOBALES PARA FILTRADO
      // ========================================
      let filtroActual = 'todas';
      let solicitudesOriginales = [];

      // Mostrar fecha actual
      $('#current-date').text(new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }));

// ========================================
// FUNCIÓN PARA FILTRAR SOLICITUDES
// ========================================
function filtrarSolicitudes(filtro) {
  filtroActual = filtro;
  
  let solicitudesFiltradas = [];
  
  switch(filtro) {
    case 'todas':
      solicitudesFiltradas = solicitudesOriginales;
      break;
      
    case 'pendientes':
      // Estado: Pendiente + Estado Aprobación: Por Aprobar
      solicitudesFiltradas = solicitudesOriginales.filter(sol => {
        const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
        const estadoAprob = (sol.ESTADO_APROBACION || '').toLowerCase();
        return estado.includes('pendiente') && 
               (estadoAprob.includes('por aprobar') || estadoAprob === '');
      });
      break;
      
    case 'aprobadas':
      // Estado: Vacante Activa O Pendiente + Estado Aprobación: Aprobado
      solicitudesFiltradas = solicitudesOriginales.filter(sol => {
        const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
        const estadoAprob = (sol.ESTADO_APROBACION || '').toLowerCase();
        return (estado.includes('activa') || estado.includes('pendiente')) && 
               estadoAprob === 'aprobado';
      });
      break;
      
    case 'proceso':
      // Estado: Candidatos en Selección
      solicitudesFiltradas = solicitudesOriginales.filter(sol => {
        const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
        return estado.includes('candidatos en seleccion');
      });
      break;
      
    case 'cubierta':
      // Estado: Plaza Cubierta
      solicitudesFiltradas = solicitudesOriginales.filter(sol => {
        const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
        return estado.includes('plaza cubierta');
      });
      break;
      
    case 'rechazadas':
      // Estado Aprobación: No Aprobado
      solicitudesFiltradas = solicitudesOriginales.filter(sol => {
        const estadoAprob = (sol.ESTADO_APROBACION || '').toLowerCase();
        return estadoAprob.includes('no aprobado');
      });
      break;
  }
  
  // Actualizar variables globales
  solicitudes = solicitudesFiltradas;
  solicitudesFiltradas = solicitudesFiltradas;
  
  // Reiniciar a la primera página
  currentPage = 1;
  
  // Renderizar tabla
  if (solicitudesFiltradas.length === 0) {
    $('#tblSolicitudes').hide();
    $('#empty-state').show();
  } else {
    renderTable(solicitudesFiltradas);
    setupPagination(solicitudesFiltradas);
    $('#tblSolicitudes').show();
    $('#empty-state').hide();
  }
}

// ========================================
// FUNCIÓN PARA ACTUALIZAR CONTADORES
// ========================================
function actualizarContadores() {
  const todas = solicitudesOriginales.length;
  
  const pendientes = solicitudesOriginales.filter(sol => {
    const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
    const estadoAprob = (sol.ESTADO_APROBACION || '').toLowerCase();
    return estado.includes('pendiente') && 
           (estadoAprob.includes('por aprobar') || estadoAprob === '');
  }).length;
  
  const aprobadas = solicitudesOriginales.filter(sol => {
    const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
    const estadoAprob = (sol.ESTADO_APROBACION || '').toLowerCase();
    return (estado.includes('activa') || estado.includes('pendiente')) && 
           estadoAprob === 'aprobado';
  }).length;
  
  const proceso = solicitudesOriginales.filter(sol => {
    const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
    return estado.includes('candidatos en seleccion');
  }).length;
  
  const cubierta = solicitudesOriginales.filter(sol => {
    const estado = (sol.ESTADO_SOLICITUD || '').toLowerCase();
    return estado.includes('plaza cubierta');
  }).length;
  
  const rechazadas = solicitudesOriginales.filter(sol => {
    const estadoAprob = (sol.ESTADO_APROBACION || '').toLowerCase();
    return estadoAprob.includes('no aprobado');
  }).length;
  
  $('#counter-todas').text(todas);
  $('#counter-pendientes').text(pendientes);
  $('#counter-aprobadas').text(aprobadas);
  $('#counter-proceso').text(proceso);
  $('#counter-cubierta').text(cubierta);
  $('#counter-rechazadas').text(rechazadas);
}

// ========================================
// EVENT LISTENER PARA LAS PESTAÑAS
// ========================================
$(document).on('click', '.tab-item', function() {
  const filtro = $(this).data('filter');
  
  // Actualizar clases activas
  $('.tab-item').removeClass('active');
  $(this).addClass('active');
  
  // Filtrar solicitudes
  filtrarSolicitudes(filtro);
});


      // ✅ FUNCIÓN PARA CARGAR SOLICITUDES (MODIFICADA PARA USAR FILTROS)
function cargarSolicitudes() {
  $('#loading-indicator').show();
  $('#tblSolicitudes').hide();
  $('#empty-state').hide();

  console.log("🔄 Iniciando carga de solicitudes...");

  let url = './GerenteTDS/crudaprobaciones.php?action=get_solicitudes_gerentes';

  console.log("📤 URL:", url);

  $.ajax({
    url: url,
    type: 'GET',
    dataType: 'json',
    success: function (data) {
      console.log("✅ Solicitudes cargadas:", data);
      
      // Guardar solicitudes originales
      solicitudesOriginales = data;
      allSolicitudes = data;
      solicitudes = data;
      solicitudesFiltradas = data;

      // Actualizar contadores
      actualizarContadores();

      // Aplicar filtro actual
      filtrarSolicitudes(filtroActual);

      $('#loading-indicator').hide();
    },
    error: function (xhr, status, error) {
      console.error('❌ Error cargando solicitudes:', {
        status: xhr.status,
        statusText: xhr.statusText,
        responseText: xhr.responseText,
        error: error
      });
      $('#loading-indicator').hide();
      Swal.fire({
        icon: 'error',
        title: 'Error de Conexión',
        html: `
          <div style="text-align: left;">
            <p>No se pudieron cargar las solicitudes.</p>
            <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">
              <small><strong>Status:</strong> ${xhr.status}</small><br>
              <small><strong>Error:</strong> ${error}</small><br>
              <small><strong>URL:</strong> ${url}</small>
            </div>
          </div>
        `,
        confirmButtonText: 'Reintentar'
      }).then(() => {
        cargarSolicitudes();
      });
    }
  });
}

      // ✅ NUEVA FUNCIÓN PARA ACTUALIZAR INFO DE FILTROS
      /*function actualizarInfoFiltros(estado, gerente, cantidad) {
        const infoDiv = $('#infoFiltros');
        const textoSpan = $('#textoFiltros');
        
        if (estado || gerente) {
          let texto = `Mostrando ${cantidad} solicitudes`;
          if (estado) texto += ` | Estado: ${estado}`;
          if (gerente) texto += ` | Gerente: ${gerente}`;
          
          textoSpan.text(texto);
          infoDiv.show();
        } else {
          infoDiv.hide();
        }
      }*/

      // ✅ EVENT LISTENERS PARA FILTROS
      /*$('#btnAplicarFiltros').on('click', function() {
        console.log("🔍 Aplicando filtros...");
        currentPage = 1;
        cargarSolicitudes();
      });

      $('#btnLimpiarFiltros').on('click', function() {
        console.log("🧹 Limpiando filtros...");
        $('#filtroEstado').val('');
        $('#filtroGerente').val('');
        $('#infoFiltros').hide();
        currentPage = 1;
        cargarSolicitudes();
      });*/

      // ✅ FUNCIÓN PARA RENDERIZAR LA TABLA
      function renderTable(data) {
        const tbody = $('#tblSolicitudes tbody');
        tbody.empty();

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = data.slice(start, end);

        pageData.forEach((item, index) => {
          const globalIndex = start + index;

          // Estados del badge original (Estado Solicitud)
          let statusClass = '';
          const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();
          if (estado.includes('pendiente')) statusClass = 'estado-pendiente';
          else if (estado.includes('activa')) statusClass = 'estado-activa';
          else if (estado.includes('candidatos') && estado.includes('seleccion')) statusClass = 'estado-candidatos-en-seleccion';
          else if (estado.includes('cvs')) statusClass = 'estado-cvs';
          else if (estado.includes('psico') || estado.includes('psicometrica')) statusClass = 'estado-psico';
          else if (estado.includes('entrevista rh')) statusClass = 'estado-rh';
          else if (estado.includes('tecnica')) statusClass = 'estado-tecnica';
          else if (estado.includes('prueba')) statusClass = 'estado-prueba';
          else if (estado.includes('poligrafo')) statusClass = 'estado-poligrafo';
          else if (estado.includes('expediente')) statusClass = 'estado-expediente';
          else if (estado.includes('confirmacion')) statusClass = 'estado-confirmacion';
          else if (estado.includes('contratada')) statusClass = 'estado-contratada';
          else if (estado.includes('plaza cubierta')) statusClass = 'estado-plaza-cubierta';
          else statusClass = 'estado-pendiente';
          // Estados del badge de aprobación
          let aprobacionClass = '';
          const aprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase();
          if (aprobacion.includes('por aprobar')) aprobacionClass = 'estado-pendiente';
          else if (aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no'))) aprobacionClass = 'estado-contratada';
          else if (aprobacion.includes('no aprobado')) aprobacionClass = 'estado-prueba';
          else aprobacionClass = 'estado-pendiente';

          const fechaModificacion = item.FECHA_MODIFICACION || '—';
          const estadoAprobacionMostrar = item.ESTADO_APROBACION || 'Por Aprobar';
          const comentario = item.COMENTARIO_NUEVO || '-';
          const idHistorico = item.ID_HISTORICO;
          const dirigidoRH = item.DIRIGIDO_RH || '—';
          const noLeidos = parseInt(item.NO_LEIDOS) || 0;
    // NUEVO: Lógica para mostrar asesora de RRHH solo si está aprobada
    const mostrarDirigidoRH = (aprobacion === 'aprobado' && dirigidoRH) 
      ? `<span class="text-success"><i class="fas fa-user-check mr-1"></i><strong>${dirigidoRH}</strong></span>`
      : '<span class="text-muted"><i class="fas fa-user-times mr-1"></i>Sin asignación</span>';      

    console.log('ID:', idHistorico, 'Comentario:', comentario, 'NO_LEIDOS:', item.NO_LEIDOS);
    console.log('Estado Aprobación:', item.ID_SOLICITUD, item.ESTADO_APROBACION);
    console.log('Dirigido RH:', item.ID_SOLICITUD, dirigidoRH, 'Mostrar:', mostrarDirigidoRH); // NUEVO DEBUG
    
// ✅ SOLUCIÓN DRÁSTICA - NO MOSTRAR COMENTARIOS SI HAY DECISIÓN DE GERENTE
// ✅ SOLUCIÓN FORZADA - ELIMINAR COMPLETAMENTE EL BOTÓN DE COMENTARIO
// ✅ SOLO MOSTRAR SI ES REALMENTE UN COMENTARIO DE RRHH Y ESTÁ PENDIENTE

const comentarioMostrar = (() => {
    // 🚫 REGLA ABSOLUTA: NUNCA mostrar botón si hay decisión del gerente
    const estadoAprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase().trim();
    
    console.log('🔍 VERIFICANDO COMENTARIO PARA SOLICITUD:', item.ID_SOLICITUD);
    console.log('📊 Estado de aprobación:', estadoAprobacion);
    console.log('💬 Comentario:', comentario);
    console.log('🆔 ID Histórico:', idHistorico);
    
    // ❌ Si el gerente ya decidió (NO es "por aprobar"), NUNCA mostrar botón
    if (estadoAprobacion !== 'por aprobar') {
        console.log('🚫 GERENTE YA DECIDIÓ - OCULTANDO BOTÓN DE COMENTARIO');
        return '<span class="text-muted">—</span>';
    }
    
    // ❌ Si no hay comentario real, no mostrar
    if (!comentario || 
        comentario === '-' || 
        comentario === '' || 
        comentario.trim() === '' ||
        comentario === 'null' ||
        comentario === 'undefined') {
        console.log('❌ SIN COMENTARIO VÁLIDO');
        return '<span class="text-muted">—</span>';
    }
    
    // ❌ Si es comentario automático del sistema, no mostrar
    const esComentarioAutomatico = 
        comentario.includes('Cambio de aprobación') ||
        comentario.includes('Asignado a:') ||
        comentario.includes('Estado actualizado') ||
        comentario.includes('Procesado por') ||
        comentario.includes('Decisión del gerente') ||
        comentario.length < 10; // Comentarios muy cortos probablemente son automáticos
    
    if (esComentarioAutomatico) {
        console.log('❌ COMENTARIO AUTOMÁTICO DEL SISTEMA');
        return '<span class="text-muted">—</span>';
    }
    
    // ✅ Solo mostrar si pasa TODAS las validaciones
    console.log('✅ COMENTARIO VÁLIDO DE RRHH - MOSTRANDO BOTÓN');
    return `<div class="badge-container">
        <button class="btn btn-sm btn-info btnVerComentarioSuper"
                data-id="${idHistorico}"
                title="Ver comentario de RRHH">
            <i class="fas fa-comment"></i> Ver
        </button>
        ${noLeidos > 0 ? `<span class="notification-badge ${noLeidos > 9 ? 'wide' : ''}">${noLeidos}</span>` : ''}
    </div>`;
})();
//variable de acciones 
let acciones = '';

                // AGREGAR ESTAS 2 LÍNEAS DESPUÉS DE LA DECLARACIÓN DE acciones

                if (aprobacion === 'no aprobado') {
                acciones += `
                    <button class="btn btn-warning btn-sm btnVerResumenGerencial" 
                            data-id="${item.ID_SOLICITUD}"
                            data-aprobacion="${item.ESTADO_APROBACION}"
                            title="Ver motivo del rechazo">
                    <i class="fas fa-exclamation-circle"></i> Ver Resultado
                    </button>`;
                }

                if ((aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no')))) {
                acciones += `
                    <button class="btn btn-success btn-sm btnVerResultadoAprobGerencial" 
                        data-id="${item.ID_SOLICITUD}"
                        title="Ver resumen de aprobación gerencial">
                        <i class="fas fa-clipboard-check"></i> Ver Resumen
                    </button>`;
                }
          
            // AGREGAR BOTÓN DE CANDIDATOS SI ESTÁ EN "Candidatos en Seleccion" O "Plaza Cubierta"
            const estadoSolicitud = item.ESTADO_SOLICITUD || '';
            if (estadoSolicitud === 'Candidatos en Seleccion' || estadoSolicitud === 'Plaza Cubierta') {
                const cantidadCandidatos = item.TOTAL_CANDIDATOS || 0;
                const esPlazaCubierta = estadoSolicitud === 'Plaza Cubierta';
                const iconoBoton = esPlazaCubierta ? 'fa-user-check' : 'fa-users';
                const colorBoton = esPlazaCubierta ? 'btn-info' : 'btn-success';
                const textoBoton = esPlazaCubierta ? 'Ver Contratado' : `Candidatos (${cantidadCandidatos})`;
                
                acciones += `
                    <button class="${colorBoton} btn-sm ml-1" 
                            onclick="mostrarCandidatosEnviadosGerente('${item.ID_SOLICITUD}')" 
                            title="Ver expediente de candidatos">
                        <i class="fas ${iconoBoton}"></i> ${textoBoton}
                    </button>
                `;
            }
              // Botón Reactivar (solo para Plaza Cubierta)
              if (estado.includes('plaza cubierta')) {
                  acciones += `
                      <button class="btn btn-warning btn-sm btnReactivarSolicitud" 
                              data-id="${item.ID_SOLICITUD}"
                              data-tienda="${item.NUM_TIENDA}"
                              data-puesto="${item.PUESTO_SOLICITADO}"
                              data-supervisor="${item.SOLICITADO_POR}"
                              data-fecha="${item.FECHA_SOLICITUD}"
                              title="Reactivar solicitud">
                          <i class="fas fa-redo"></i> Reactivar
                      </button>
                  `;
              }

                const row = `
                  <tr data-id="${item.ID_SOLICITUD}">
                    <td><span class="badge badge-primary">${item.NUM_TIENDA}</span></td>
                    <td><strong>${item.PUESTO_SOLICITADO}</strong></td>
                    <td><small class="text-muted">${item.SOLICITADO_POR}</small></td>
                    <td><small>${item.DIRIGIDO_A || '—'}</small></td>
                    <td><small class="text-info"><strong>${dirigidoRH}</strong></small></td>
                    <td><small>${item.FECHA_SOLICITUD}</small></td>
                    <td><small class="text-muted">${fechaModificacion}</small></td>
                    <td>
                      <span class="status-badge ${statusClass}" title="${item.ULTIMO_COMENTARIO || 'Sin comentario'}">
                        ${item.ESTADO_SOLICITUD}
                      </span>
                    </td>
                    <td><span class="status-badge ${aprobacionClass}">${estadoAprobacionMostrar}</span></td>
                    <td><small>${item.RAZON || '—'}</small></td>
                  
                    <td>
                      <div class="actions-container">
                        <button class="btn btn-action btn-approval btnProcesarSolicitud"
                                data-id="${item.ID_SOLICITUD}"
                                data-tienda="${item.NUM_TIENDA || ''}"
                                data-puesto="${item.PUESTO_SOLICITADO || ''}"
                                data-supervisor="${item.SOLICITADO_POR || ''}"
                                data-razon="${item.RAZON || ''}"
                                data-aprobacion-actual="${estadoAprobacionMostrar}"
                                title="Procesar Solicitud">
                          <i class="fas fa-vote-yea mr-2"></i> Procesar
                        </button>
                        ${acciones}
                      </div>
                    </td>
                  </tr>`;

                tbody.append(row);
              });
            }

      // FUNCIÓN PARA CONFIGURAR PAGINACIÓN
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

        // Páginas
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
      }

      // Event listener para paginación
      $('.pagination').on('click', 'a', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage) {
          currentPage = page;
          renderTable(solicitudes);
          setupPagination(solicitudes);
        }
      });

      // ✅ FUNCIÓN DE BÚSQUEDA MEJORADA (SIN DUPLICAR FILTROS)
      function performSearch() {
        const searchText = $('#searchInput').val().toLowerCase();
        const searchTienda = $('#searchTienda').val().toLowerCase();

        // ✅ USAR solicitudesFiltradas COMO BASE (ya contiene filtros aplicados)
        let filtered = solicitudesFiltradas.filter(item => {
          const matchesSearch = !searchText || 
            (item.PUESTO_SOLICITADO || '').toLowerCase().includes(searchText) ||
            (item.SOLICITADO_POR || '').toLowerCase().includes(searchText) ||
            (item.DIRIGIDO_A || '').toLowerCase().includes(searchText) ||
            (item.DIRIGIDO_RH || '').toLowerCase().includes(searchText) ||
            (item.ESTADO_SOLICITUD || '').toLowerCase().includes(searchText) ||
            (item.ESTADO_APROBACION || '').toLowerCase().includes(searchText) ||
            (item.RAZON || '').toLowerCase().includes(searchText);

          const matchesTienda = !searchTienda || 
            (item.NUM_TIENDA || '').toString().toLowerCase().includes(searchTienda);

          return matchesSearch && matchesTienda;
        });

        solicitudes = filtered;
        currentPage = 1;
        
        if (filtered.length === 0) {
          $('#tblSolicitudes').hide();
          $('#empty-state').show();
          $('.pagination').empty();
        } else {
          $('#empty-state').hide();
          $('#tblSolicitudes').show();
          renderTable(solicitudes);
          setupPagination(solicitudes);
        }
      }
      // Event listeners para búsqueda
      $('#searchInput, #searchTienda').on('keyup', function() {
  const searchTerm = $('#searchInput').val().toLowerCase();
  const searchTienda = $('#searchTienda').val().toLowerCase();
  
  // Aplicar búsqueda sobre el filtro actual
  let datosFiltrados = solicitudes.filter(function(item) {
    const matchSearch = searchTerm === '' || 
      Object.values(item).some(val => 
        String(val).toLowerCase().includes(searchTerm)
      );
    
    const matchTienda = searchTienda === '' || 
      String(item.NUM_TIENDA).toLowerCase().includes(searchTienda);
    
    return matchSearch && matchTienda;
  });
  
  currentPage = 1;
  renderTable(datosFiltrados);
  setupPagination(datosFiltrados);
});


// ==================================================================================
// EVENT LISTENER: BOTÓN GENERAR REPORTE DE SOLICITUDES HISTORIAL GENERAL E INDIVIDUAL
// ==================================================================================
$(document).off('click', '.btnVerHistorial').on('click', '.btnVerHistorial', function() {
    
    // Abrir modal de configuración
    Swal.fire({
        title: '<i class="fas fa-file-alt"></i> Generar Reporte de Solicitudes',
        html: `
            <div style="text-align: left;">
                <!-- Header con gradiente -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <h6 style="margin: 0; color: white;">
                        <i class="fas fa-cog"></i> Configuración del Reporte
                    </h6>
                    <small>Seleccione el rango de fechas y filtros adicionales para generar el historial</small>
                </div>
                
                <!-- Fechas -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="font-weight-bold"><i class="fas fa-calendar"></i> Fecha Inicial:</label>
                        <input type="date" id="fechaInicial" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold"><i class="fas fa-calendar"></i> Fecha Final:</label>
                        <input type="date" id="fechaFinal" class="form-control">
                    </div>
                </div>

                <!-- Filtros Rápidos -->
                <div class="mb-3">
                    <label class="font-weight-bold"><i class="fas fa-filter"></i> Filtros Rápidos:</label>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <button type="button" class="btn btn-outline-primary btn-filtro-rapido" data-dias="7">Últimos 7 días</button>
                        <button type="button" class="btn btn-outline-primary btn-filtro-rapido active" data-dias="30">Último mes</button>
                        <button type="button" class="btn btn-outline-primary btn-filtro-rapido" data-dias="90">Últimos 3 meses</button>
                    </div>
                </div>

                <hr>

                <!-- Info sobre filtros -->
                <div style="background: #e7f3ff; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff; margin-bottom: 15px;">
                    <h6 style="color: #004085; margin-bottom: 10px;">
                        <i class="fas fa-info-circle"></i> Filtros Adicionales
                    </h6>
                    <small style="color: #004085;">
                        <strong>Sin filtros:</strong> Historial GENERAL (todas las solicitudes)<br>
                        <strong>Con filtros:</strong> Historial INDIVIDUAL (solicitud específica)
                    </small>
                </div>

                <!-- Filtros Adicionales -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="font-weight-bold"><i class="fas fa-store"></i> Tienda:</label>
                        <select id="filtroTienda" class="form-control">
                            <option value="">Todas las Tiendas</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold"><i class="fas fa-user-tie"></i> Supervisor:</label>
                        <select id="filtroSupervisor" class="form-control">
                            <option value="">Todos los Supervisores</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold"><i class="fas fa-briefcase"></i> Puesto:</label>
                        <select id="filtroPuesto" class="form-control">
                            <option value="">Todos los Puestos</option>
                        </select>
                    </div>
                </div>
            </div>
        `,
        width: '900px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-search"></i> Generar Reporte',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const fechaInicial = document.getElementById('fechaInicial').value;
            const fechaFinal = document.getElementById('fechaFinal').value;
            
            if (!fechaInicial || !fechaFinal) {
                Swal.showValidationMessage('Debe seleccionar ambas fechas');
                return false;
            }
            
            return {
                fechaInicial: fechaInicial,
                fechaFinal: fechaFinal,
                filtroTienda: document.getElementById('filtroTienda').value,
                filtroSupervisor: document.getElementById('filtroSupervisor').value,
                filtroPuesto: document.getElementById('filtroPuesto').value
            };
        },
        didOpen: () => {
            // Cargar opciones de filtros
            cargarOpcionesFiltrosHistorial();
            
            // Establecer fechas por defecto (últimos 30 días)
            const hoy = new Date();
            const hace30dias = new Date();
            hace30dias.setDate(hoy.getDate() - 30);
            
            document.getElementById('fechaFinal').value = hoy.toISOString().split('T')[0];
            document.getElementById('fechaInicial').value = hace30dias.toISOString().split('T')[0];
            
            // Event listeners para filtros rápidos
            document.querySelectorAll('.btn-filtro-rapido').forEach(btn => {
                btn.addEventListener('click', function() {
                    const dias = parseInt(this.dataset.dias);
                    const hoy = new Date();
                    const fechaInicio = new Date();
                    fechaInicio.setDate(hoy.getDate() - dias);
                    
                    document.getElementById('fechaFinal').value = hoy.toISOString().split('T')[0];
                    document.getElementById('fechaInicial').value = fechaInicio.toISOString().split('T')[0];
                    
                    document.querySelectorAll('.btn-filtro-rapido').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const filtros = result.value;
            
            // Determinar tipo
            const tieneFiltros = filtros.filtroTienda || filtros.filtroSupervisor || filtros.filtroPuesto;
            const tipoHistorial = tieneFiltros ? 'INDIVIDUAL' : 'GENERAL';
            
            console.log('📊 Generando historial:', tipoHistorial);
            console.log('Filtros:', filtros);
            
            // Loading
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Cargando...',
                html: 'Generando historial ' + tipoHistorial + '...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            // Llamada AJAX
            $.ajax({
                url: './GerenteTDS/crudaprobaciones.php',
                type: 'GET',
                data: {
                    action: 'get_proceso_solicitudes_gerentes',
                    filtro_tienda: filtros.filtroTienda,
                    filtro_supervisor: filtros.filtroSupervisor,
                    filtro_puesto: filtros.filtroPuesto
                },
                dataType: 'json',
                success: function(response) {
                    console.log('✅ Respuesta:', response);
                    
                    if (response.success) {
                        mostrarHistorialProceso(response.datos, response.tipo);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'No se pudo cargar el historial',
                            confirmButtonText: 'Entendido'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error:', error);
                    console.error('Respuesta:', xhr.responseText);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Conexión',
                        text: 'No se pudo conectar con el servidor',
                        confirmButtonText: 'Entendido'
                    });
                }
            });
        }
    });
});

// ==================================================================================
// FUNCIÓN: CARGAR OPCIONES DE FILTROS (TIENDA, SUPERVISOR, PUESTO)
// ==================================================================================
// ==================================================================================
// FUNCIÓN: CARGAR OPCIONES DE FILTROS - VERSIÓN CORREGIDA PARA TU ESTRUCTURA
// ==================================================================================
function cargarOpcionesFiltrosHistorial() {
    // Cargar tiendas
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_tiendas_filtro_gerente',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Tiendas recibidas:', response);
            const select = document.getElementById('filtroTienda');
            
            if (response && Array.isArray(response)) {
                response.forEach(tienda => {
                    const option = document.createElement('option');
                    // Usar 'numero' porque tu case devuelve objetos con {numero, nombre}
                    option.value = tienda.numero;
                    option.textContent = tienda.nombre || ('Tienda ' + tienda.numero);
                    select.appendChild(option);
                });
            }
        },
        error: function(xhr) {
            console.error('❌ Error cargando tiendas:', xhr.responseText);
        }
    });
    
    // Cargar supervisores
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_supervisores_filtro_gerente',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Supervisores recibidos:', response);
            const select = document.getElementById('filtroSupervisor');
            
            if (response && Array.isArray(response)) {
                response.forEach(supervisor => {
                    const option = document.createElement('option');
                    // Usar 'codigo' porque tu case devuelve objetos con {codigo, nombre}
                    option.value = supervisor.codigo;
                    option.textContent = supervisor.nombre;
                    select.appendChild(option);
                });
            }
        },
        error: function(xhr) {
            console.error('❌ Error cargando supervisores:', xhr.responseText);
        }
    });
    
    // Cargar puestos
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_puestos_filtro_gerente',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('✅ Puestos recibidos:', response);
            const select = document.getElementById('filtroPuesto');
            
            if (response && Array.isArray(response)) {
                response.forEach(puesto => {
                    const option = document.createElement('option');
                    // Los puestos vienen como strings directos
                    option.value = puesto;
                    option.textContent = puesto;
                    select.appendChild(option);
                });
            }
        },
        error: function(xhr) {
            console.error('❌ Error cargando puestos:', xhr.responseText);
        }
    });
}

// ==================================================================================
// FUNCIÓN: MOSTRAR HISTORIAL DE PROCESO DE SOLICITUDES
// ==================================================================================
function mostrarHistorialProceso(datos, tipo) {
    console.log('📋 Mostrando historial:', datos);
    
    if (!datos || datos.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Sin registros',
            text: 'No se encontraron solicitudes para mostrar',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    let html = '';
    
    datos.forEach(function(solicitud) {
        html += generarCardSolicitud(solicitud);
    });
    
    // Calcular métricas
    const totalCandidatos = datos.reduce((sum, s) => sum + (parseInt(s.TOTAL_CANDIDATOS) || 0), 0);
    const totalReactivadas = datos.reduce((sum, s) => sum + (parseInt(s.NUM_REACTIVACIONES) || 0), 0);
    
    // Debug para verificar
    console.log('🔍 Total solicitudes:', datos.length);
    console.log('🔍 Total candidatos:', totalCandidatos);
    console.log('🔍 Total reactivadas:', totalReactivadas);
    
    // Mostrar modal
    Swal.fire({
        title: '<i class="fas fa-history"></i> Proceso de Solicitudes - ' + tipo.toUpperCase(),
        html: `
            <div style="text-align: left; max-height: 70vh; overflow-y: auto;">
                <!-- Métricas -->
                <div class="alert alert-primary mb-3">
                    <h6 class="mb-2"><i class="fas fa-chart-bar"></i> MÉTRICAS GENERALES:</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <h4>${datos.length}</h4>
                            <small>Solicitudes</small>
                        </div>
                        <div class="col-4">
                            <h4>${totalCandidatos}</h4>
                            <small>Candidatos</small>
                        </div>
                        <div class="col-4">
                            <h4>${totalReactivadas}</h4>
                            <small>Reactivadas</small>
                        </div>
                    </div>
                </div>
                
                ${html}
            </div>
        `,
        width: '95%',
        showCloseButton: true,
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
        cancelButtonText: '<i class="fas fa-file-pdf"></i> Generar PDF',
        denyButtonText: '<i class="fas fa-file-excel"></i> Generar Excel',
        confirmButtonColor: '#6c757d',
        cancelButtonColor: '#dc3545',
        denyButtonColor: '#28a745',
        reverseButtons: true
    }).then((result) => {
        if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
            // Generar PDF
            exportarHistorialPDF(datos, tipo);
        } else if (result.isDenied) {
            // Generar Excel
            exportarHistorialExcel(datos, tipo);
        }
    });
}

        // ==================================================================================
        // FUNCIÓN: EXPORTAR HISTORIAL A PDF
        // ==================================================================================
        function exportarHistorialPDF(datos, tipo) {
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Generando PDF...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            $.ajax({
                url: './GerenteTDS/exportar_historial.php',
                type: 'POST',
                data: {
                    action: 'generar_pdf',
                    datos: JSON.stringify(datos),
                    tipo: tipo
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob) {
                    Swal.close();
                    
                    // Crear enlace de descarga
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Historial_Solicitudes_' + tipo.toUpperCase() + '_' + new Date().toISOString().split('T')[0] + '.pdf';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡PDF Generado!',
                        text: 'El archivo se ha descargado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo generar el PDF: ' + (xhr.responseText || 'Error desconocido'),
                        confirmButtonText: 'Entendido'
                    });
                }
            });
        }

        // ==================================================================================
        // FUNCIÓN: EXPORTAR HISTORIAL A EXCEL
        // ==================================================================================
        function exportarHistorialExcel(datos, tipo) {
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Generando Excel...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });
            
            $.ajax({
                url: './GerenteTDS/exportar_historial.php',
                type: 'POST',
                data: {
                    action: 'generar_excel',
                    datos: JSON.stringify(datos),
                    tipo: tipo
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob) {
                    Swal.close();
                    
                    // Crear enlace de descarga
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'Historial_Solicitudes_' + tipo.toUpperCase() + '_' + new Date().toISOString().split('T')[0] + '.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Excel Generado!',
                        text: 'El archivo se ha descargado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo generar el Excel: ' + (xhr.responseText || 'Error desconocido'),
                        confirmButtonText: 'Entendido'
                    });
                }
            });
        }

// ==================================================================================
// FUNCIÓN: GENERAR CARD DE SOLICITUD (VISUAL MEJORADO)
// ==================================================================================
function generarCardSolicitud(solicitud) {
    //const esReactivada = solicitud.REACTIVADA === 'Y';
    const esReactivada = (solicitud.NUM_REACTIVACIONES && solicitud.NUM_REACTIVACIONES > 0);
    const esPlazaCubierta = solicitud.ESTADO_ACTUAL === 'Plaza Cubierta';
    
    let html = '<div style="border: 2px solid #dee2e6; border-radius: 10px; margin-bottom: 20px; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">';
    
    // HEADER con fecha y tiempo
    html += '<div style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 15px; border-radius: 8px 8px 0 0;">';
    html += '  <div style="display: flex; justify-content: space-between; align-items: center;">';
    html += '    <div>';
    html += '      <h6 style="margin: 0; color: white;"><i class="fas fa-calendar"></i> ' + solicitud.FECHA_SOLICITUD.split(' ')[0] + '</h6>';
    html += '    </div>';
    html += '    <div style="text-align: right;">';
    html += '      <div style="font-size: 14px;"><i class="fas fa-clock"></i> TIEMPO TOTAL DESDE SOLICITUD: <strong>' + solicitud.TIEMPO_TOTAL + '</strong></div>';
    
    if (esReactivada && solicitud.TIEMPO_REACTIVACION) {
        html += '      <div style="font-size: 13px; background: rgba(255,193,7,0.3); padding: 3px 8px; border-radius: 5px; margin-top: 5px;">';
        html += '        <i class="fas fa-redo"></i> TIEMPO DESDE REACTIVACIÓN: <strong>' + solicitud.TIEMPO_REACTIVACION + '</strong>';
        html += '      </div>';
    }
    
    html += '    </div>';
    html += '  </div>';
    html += '</div>';
    
    // BODY del card
    html += '<div style="padding: 20px;">';
    
    // Info básica de la solicitud
    html += '<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 5px solid #007bff;">';
    html += '  <h6 style="color: #495057; margin-bottom: 10px;"><i class="fas fa-file-alt"></i> SOLICITUD #' + solicitud.ID_SOLICITUD + '</h6>';
    html += '  <div class="row">';
    html += '    <div class="col-6"><strong><i class="fas fa-store"></i> Tienda:</strong> ' + solicitud.NUM_TIENDA + ' - Managua Centro</div>';
    html += '    <div class="col-6"><strong><i class="fas fa-briefcase"></i> Puesto:</strong> ' + solicitud.PUESTO_SOLICITADO + '</div>';
    html += '  </div>';
    html += '  <div class="row mt-2">';
    html += '    <div class="col-6"><strong><i class="fas fa-user-tie"></i> Supervisor:</strong> ' + solicitud.SOLICITADO_POR + '</div>';
    html += '    <div class="col-6"><strong><i class="fas fa-calendar-check"></i> Fecha Solicitud:</strong> ' + solicitud.FECHA_SOLICITUD + '</div>';
    html += '  </div>';
    html += '</div>';
    
    // Cambio de estado
    html += '<div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 5px solid #ffc107;">';
    html += '  <h6 style="color: #856404;"><i class="fas fa-exchange-alt"></i> CAMBIO DE ESTADO:</h6>';
    html += '  <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">';
    html += '    <div><strong>➤ Anterior:</strong> <span style="background: #6c757d; color: white; padding: 3px 10px; border-radius: 15px; font-size: 13px;">' + solicitud.ESTADO_ANTERIOR + '</span></div>';
    html += '    <div><strong>➤ Actual:</strong> <span style="background: ' + (esPlazaCubierta ? '#28a745' : '#007bff') + '; color: white; padding: 3px 10px; border-radius: 15px; font-size: 13px;">' + solicitud.ESTADO_ACTUAL + (esPlazaCubierta ? ' ✅' : '') + '</span></div>';
    html += '  </div>';
    html += '  <div style="margin-top: 8px; color: #856404;"><i class="fas fa-hourglass-half"></i> Tiempo en estado anterior: <strong>' + solicitud.TIEMPO_ESTADO_ANTERIOR + '</strong></div>';
    html += '</div>';
    
    // Reactivación
    html += '<div style="background: ' + (esReactivada ? '#fff3cd' : '#e7f3ff') + '; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 5px solid ' + (esReactivada ? '#ffc107' : '#007bff') + ';">';
    html += '  <h6 style="color: ' + (esReactivada ? '#856404' : '#004085') + ';"><i class="fas fa-' + (esReactivada ? 'redo' : 'info-circle') + '"></i> REACTIVACIÓN: ';
    html += '    <span style="background: ' + (esReactivada ? '#ffc107' : '#28a745') + '; color: black; padding: 3px 12px; border-radius: 15px; font-size: 13px;">' + (esReactivada ? 'SÍ' : 'NO') + '</span>';
    html += '  </h6>';
    
    if (esReactivada) {
        html += '  <div style="margin-top: 10px; background: white; padding: 10px; border-radius: 5px;">';
        html += '    <p style="margin: 0;"><strong><i class="fas fa-comment-dots"></i> Motivo:</strong> ' + (solicitud.MOTIVO_REACTIVACION || 'No especificado') + '</p>';
        html += '    <p style="margin: 5px 0 0 0;"><strong><i class="fas fa-user"></i> Reactivado por:</strong> ' + (solicitud.USUARIO_REACTIVACION || 'No especificado') + '</p>';
        if (solicitud.FECHA_REACTIVACION) {
            html += '    <p style="margin: 5px 0 0 0;"><strong><i class="fas fa-calendar"></i> Fecha reactivación:</strong> ' + solicitud.FECHA_REACTIVACION + '</p>';
        }
        html += '  </div>';
    }
    
    html += '</div>';
    
    // Candidatos
    if (solicitud.TOTAL_CANDIDATOS > 0) {
        html += generarSeccionCandidatos(solicitud);
    } else {
        html += '<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; color: #6c757d;">';
        html += '  <i class="fas fa-info-circle"></i> CANDIDATOS INVOLUCRADOS: Ninguno';
        html += '</div>';
    }
    
    html += '</div>'; // Cierre body
    html += '</div>'; // Cierre card
    
    return html;
}

// ==================================================================================
// FUNCIÓN: GENERAR SECCIÓN DE CANDIDATOS
// ==================================================================================
      function generarSeccionCandidatos(solicitud) {
          const esReactivada = (solicitud.NUM_REACTIVACIONES && solicitud.NUM_REACTIVACIONES > 0);
          const candidatos = solicitud.CANDIDATOS;
          
          let html = '<div style="background: #e8f5e9; padding: 15px; border-radius: 8px; border-left: 5px solid #4caf50;">';
          html += '  <h6 style="color: #2e7d32;"><i class="fas fa-users"></i> CANDIDATOS INVOLUCRADOS:</h6>';
          
          // ✅ MOSTRAR CANDIDATOS DEL PROCESO ORIGINAL
          if (candidatos.proceso_original && candidatos.proceso_original.length > 0) {
              html += '<div style="margin-top: 15px;">';
              html += '  <h6 style="color: #495057; font-size: 14px; margin-bottom: 10px;"><i class="fas fa-list"></i> 👥 PROCESO ORIGINAL</h6>';
              
              candidatos.proceso_original.forEach(function(candidato) {
                  html += generarCardCandidato(candidato, false);
              });
              
              html += '</div>';
          }
          
          // ✅ MOSTRAR CADA REACTIVACIÓN CON SUS CANDIDATOS
          if (candidatos.reactivaciones && candidatos.reactivaciones.length > 0) {
              candidatos.reactivaciones.forEach(function(reactivacion) {
                  const info = reactivacion.info_reactivacion;
                  
                  // SEPARADOR DE REACTIVACIÓN
                  html += '<div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 5px solid #ff9800;">';
                  html += '  <h6 style="color: #ff9800; margin-bottom: 10px;"><i class="fas fa-redo"></i> 🔄 REACTIVACIÓN #' + info.NUM_REACTIVACION + '</h6>';
                  html += '  <div style="background: white; padding: 10px; border-radius: 5px; font-size: 13px;">';
                  html += '    <p style="margin: 5px 0;"><strong><i class="fas fa-calendar"></i> Fecha:</strong> ' + info.FECHA_REACTIVACION + '</p>';
                  html += '    <p style="margin: 5px 0;"><strong><i class="fas fa-comment-dots"></i> Motivo:</strong> ' + (info.MOTIVO_REACT || 'No especificado') + '</p>';
                  html += '    <p style="margin: 5px 0;"><strong><i class="fas fa-user-check"></i> Candidato anterior:</strong> ' + (info.NOMBRE_CAND_ANT || 'No especificado') + '</p>';
                  html += '    <p style="margin: 5px 0 0 0;"><strong><i class="fas fa-user-tie"></i> Reactivado por:</strong> ' + (info.USUARIO_REACTIVO || 'No especificado') + '</p>';
                  html += '  </div>';
                  html += '</div>';
                  
                  // CANDIDATOS DE ESTA REACTIVACIÓN
                  if (reactivacion.candidatos && reactivacion.candidatos.length > 0) {
                      html += '<div style="margin-top: 15px; padding-left: 20px;">';
                      html += '  <h6 style="color: #4caf50; font-size: 14px; margin-bottom: 10px;"><i class="fas fa-users"></i> 👥 CANDIDATOS DE REACTIVACIÓN #' + info.NUM_REACTIVACION + '</h6>';
                      
                      reactivacion.candidatos.forEach(function(candidato) {
                          html += generarCardCandidato(candidato, true);
                      });
                      
                      html += '</div>';
                  } else {
                      html += '<div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; text-align: center; color: #6c757d;">';
                      html += '  <i class="fas fa-info-circle"></i> No hay candidatos registrados en esta reactivación';
                      html += '</div>';
                  }
              });
          }
          
          // Si no hay candidatos en absoluto
          if ((!candidatos.proceso_original || candidatos.proceso_original.length === 0) && 
              (!candidatos.reactivaciones || candidatos.reactivaciones.length === 0)) {
              html += '  <p style="margin: 10px 0 0 0; color: #6c757d;">No hay candidatos registrados</p>';
          }
          
          html += '</div>';
          
          return html;
      }



        // ==================================================================================
        // FUNCIÓN: GENERAR CARD DE UN CANDIDATO INDIVIDUAL
        // ==================================================================================
        function generarCardCandidato(candidato, esReactivado) {
            let html = '';
            
            html += '<div style="background: white; padding: 12px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid ' + (esReactivado ? '#4caf50' : '#007bff') + ';">';
            html += '  <div style="display: flex; justify-content: between; align-items: start;">';
            html += '    <div style="flex: 1;">';
            html += '      <div style="font-weight: 600; color: #212529; margin-bottom: 5px;">';
            html += '        <i class="fas fa-user"></i> ' + candidato.NOMBRE_COMPLETO;
            
            // Etiqueta de reactivado
            if (esReactivado) {
                html += ' <span style="background: #4caf50; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px;"><i class="fas fa-redo"></i> 🏷️ REACTIVADO</span>';
            }
            
            html += '      </div>';
            
            // Estados
            html += '      <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">';
            html += '        <span style="background: #6c757d; color: white; padding: 3px 10px; border-radius: 15px; font-size: 12px;">' + (candidato.ESTADO_ANTERIOR || 'Inicial') + '</span>';
            html += '        <i class="fas fa-arrow-right" style="color: #007bff;"></i>';
            
            // Color según estado actual
            let colorEstado = '#007bff';
            if (candidato.ESTADO_ACTUAL === 'Contratado') {
                colorEstado = '#28a745';
            } else if (candidato.ESTADO_ACTUAL === 'Descartado') {
                colorEstado = '#dc3545';
            }
            
            html += '        <span style="background: ' + colorEstado + '; color: white; padding: 3px 10px; border-radius: 15px; font-size: 12px;">' + candidato.ESTADO_ACTUAL + '</span>';
            html += '      </div>';
            
            // Motivo de descarte (si existe)
            if (candidato.MOTIVO_DESCARTE && candidato.ESTADO_ACTUAL === 'Descartado') {
                html += '      <div style="margin-top: 8px; padding: 8px; background: #fff3cd; border-radius: 5px; font-size: 12px; color: #856404;">';
                html += '        <i class="fas fa-exclamation-triangle"></i> <strong>Motivo:</strong> ' + candidato.MOTIVO_DESCARTE;
                html += '      </div>';
            }
            
            // Tiempo en proceso
            html += '      <div style="margin-top: 8px; font-size: 12px; color: #495057;">';
            html += '        <i class="fas fa-clock"></i> Tiempo en proceso: <strong>' + (candidato.TIEMPO_EN_PROCESO || 'N/A') + '</strong>';
            html += '      </div>';
            
            html += '    </div>';
            html += '  </div>';
            html += '</div>';
            
            return html;
        }
            //===================================================================================================
            // FIN FUNCIONES Y BOTONES AGREGADOS PARA LA VISUALIZACION DE ARCHIVOS, COMO CVS Y PRUEBAS QUE 
            // TENIA EL SUPERVISOR
            //====================================================================================================   
              // FUNCIÓN PARA CAMBIAR APROBACIÓN 
$(document).off('click', '.btnProcesarSolicitud').on('click', '.btnProcesarSolicitud', function(e) {
  // CRÍTICO: Prevenir propagación del evento
  e.preventDefault();
  e.stopPropagation();
  e.stopImmediatePropagation();
  
  const id = $(this).data('id');
  const tienda = $(this).data('tienda');
  const puesto = $(this).data('puesto');
  const supervisor = $(this).data('supervisor');
  const aprobacionActual = $(this).data('aprobacion-actual') || 'Por Aprobar';

  // Agregar pequeño delay antes de abrir el modal
  setTimeout(() => {
    Swal.fire({
      title: '<i class="fas fa-user-check"></i> Cambiar Estado de Aprobación',
      html: `
        <div style="text-align: left; margin-bottom: 30px;">
          <div style="background: #cce7ff; border: 1px solid #99d1ff; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
            <h6 style="margin: 0 0 15px 0; font-weight: 600; color: #0066cc;">
              <i class="fas fa-info-circle"></i> Información de la Solicitud
            </h6>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px; color: #333;">
              <div>
                <strong><i class="fas fa-hashtag"></i> ID:</strong> ${id}
              </div>
              <div>
                <strong><i class="fas fa-store"></i> Tienda:</strong> ${tienda}
              </div>
              <div>
                <strong><i class="fas fa-briefcase"></i> Puesto:</strong> ${puesto}
              </div>
              <div>
                <strong><i class="fas fa-calendar-alt"></i> Fecha:</strong> ${new Date().toLocaleDateString('es-ES')}
              </div>
              <div style="grid-column: 1 / -1;">
                <strong><i class="fas fa-user"></i> Solicitado por:</strong> ${supervisor}
              </div>
            </div>
          </div>
          
          <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
            <div style="display: flex; align-items: center; color: #856404;">
              <i class="fas fa-info-circle" style="font-size: 18px; margin-right: 10px;"></i>
              <div>
                <strong>Estado Actual de Aprobación:</strong><br>
                <span style="background: #ffc107; color: #1c1f20ff; padding: 6px 12px; border-radius: 16px; font-size: 14px; font-weight: bold;">
                  ${aprobacionActual}
                </span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <label style="font-weight: 700; margin-bottom: 15px; font-size: 18px; color: #333;">
            <i class="fas fa-check-double"></i> Seleccione el Nuevo Estado de Aprobación:
          </label>
          <select id="nuevaAprobacion" class="form-control" style="
            font-size: 18px; 
            padding: 15px 20px; 
            border: 2px solid #ddd; 
            border-radius: 10px;
            background: #f8f9fa;
            font-weight: 600;
            height: auto;
          ">
            <option value="" style="color: #999;">Seleccione una opción...</option>
            <option value="Aprobado" style="color: #28a745; font-weight: bold;">
               Aprobado
            </option>
            <option value="No Aprobado" style="color: #dc3545; font-weight: bold;">
               No Aprobado
            </option>
            <option value="Por Aprobar" style="color: #ffc107; font-weight: bold;">
               Por Aprobar
            </option>
          </select>
        </div>

        <!-- CAMPO CONDICIONAL PARA ASIGNAR RRHH (SOLO CUANDO ES APROBADO) -->
        <div id="campo-rrhh" class="form-group" style="display: none;">
          <div class="alert alert-success">
            <i class="fas fa-user-plus mr-2"></i>
            <strong>Solicitud Aprobada - Asignar a RRHH</strong>
          </div>
          <label for="swal-dirigido-rh"><strong>Asignar a:</strong></label>
          <select id="swal-dirigido-rh" class="form-control">
            <option value="">Seleccionar persona de RRHH...</option>
            <option value="Keisha Davila">Keisha Davila</option>
            <option value="Emma de Cea">Emma de Cea</option>
          </select>
          <small class="form-text text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Seleccione la persona de RRHH que se encargará de esta solicitud
          </small>
        </div>

        <!-- CAMPO OBLIGATORIO PARA COMENTARIO DE APROBACIÓN -->
        <div id="campo-comentario-aprobacion" class="form-group" style="display: none;">
          <div class="alert alert-success">
            <i class="fas fa-comment-check mr-2"></i>
            <strong>Comentario de Aprobación - Obligatorio</strong>
          </div>
          <label for="swal-comentario-aprobacion"><strong>Comentario de aprobacion:</strong></label>
          <textarea 
            id="swal-comentario-aprobacion" 
            class="form-control" 
            rows="3" 
            placeholder="Escriba un comentario explicando los detalles de la aprobación..."
            style="border: 2px solid #28a745; border-radius: 8px; font-size: 14px;">
          </textarea>
          <small class="form-text text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Este comentario será visible para RRHH y el supervisor como detalle de la aprobación
          </small>
        </div>

        <!-- CAMPO OBLIGATORIO PARA COMENTARIO DE RECHAZO -->
        <div id="campo-comentario-rechazo" class="form-group" style="display: none;">
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Solicitud Rechazada - Comentario Obligatorio</strong>
          </div>
          <label for="swal-comentario-rechazo"><strong>Motivo del rechazo:</strong></label>
          <textarea 
            id="swal-comentario-rechazo" 
            class="form-control" 
            rows="3" 
            placeholder="Explique el motivo por el cual se rechaza esta solicitud..."
            style="border: 2px solid #dc3545; border-radius: 8px; font-size: 14px;">
          </textarea>
          <small class="form-text text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Este comentario será visible para el supervisor para que pueda entender el motivo del rechazo
          </small>
        </div>
      `,
      width: '700px',
      showCancelButton: true,
      confirmButtonText: '<i class="fas fa-save"></i> Confirmar Cambio',
      cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      buttonsStyling: false,
      customClass: {
        popup: 'aprobacion-modal-grande',
        confirmButton: 'btn btn-success btn-lg px-4',
        cancelButton: 'btn btn-secondary btn-lg px-4 mr-2'
      },
      //CRÍTICO: Evitar que el modal se cierre al hacer clic fuera
      allowOutsideClick: false,
      allowEscapeKey: true,
      allowEnterKey: false,
      
      preConfirm: () => {
        const nuevaAprobacion = $('#nuevaAprobacion').val();
        const dirigidoRH = $('#swal-dirigido-rh').val();
        const comentarioAprobacion = $('#swal-comentario-aprobacion').val().trim();
        const comentarioRechazo = $('#swal-comentario-rechazo').val().trim();
        
        if (!nuevaAprobacion) {
          Swal.showValidationMessage(`
            <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
              <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
              <span style="font-weight: 600;">Debe seleccionar un estado de aprobación</span>
            </div>
          `);
          return false;
        }

        // ✅ VALIDACIÓN PARA SOLICITUDES APROBADAS
        if (nuevaAprobacion === 'Aprobado') {
          // Validar asignación a RRHH
          if (!dirigidoRH) {
            Swal.showValidationMessage(`
              <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
                <span style="font-weight: 600;">Debe seleccionar una persona de RRHH para la solicitud aprobada</span>
              </div>
            `);
            return false;
          }

          // 🆕 Validar comentario obligatorio para aprobaciones
          if (!comentarioAprobacion) {
            Swal.showValidationMessage(`
              <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
                <span style="font-weight: 600;">Debe proporcionar un comentario explicando la aprobación</span>
              </div>
            `);
            return false;
          }

          // 🆕 Validar longitud mínima del comentario de aprobación
          if (comentarioAprobacion.length < 10) {
            Swal.showValidationMessage(`
              <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
                <span style="font-weight: 600;">El comentario de aprobación debe tener al menos 10 caracteres</span>
              </div>
            `);
            return false;
          }
        }

        // 🆕 VALIDACIÓN PARA SOLICITUDES RECHAZADAS
        if (nuevaAprobacion === 'No Aprobado') {
          if (!comentarioRechazo) {
            Swal.showValidationMessage(`
              <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
                <span style="font-weight: 600;">Debe proporcionar un motivo para el rechazo de la solicitud</span>
              </div>
            `);
            return false;
          }

          // Validar longitud mínima del comentario de rechazo
          if (comentarioRechazo.length < 10) {
            Swal.showValidationMessage(`
              <div style="display: flex; align-items: center; justify-content: center; color: #dc3545;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px; font-size: 16px;"></i>
                <span style="font-weight: 600;">El motivo del rechazo debe tener al menos 10 caracteres</span>
              </div>
            `);
            return false;
          }
        }
          
        return { 
          nuevaAprobacion: nuevaAprobacion, 
          dirigidoRH: dirigidoRH || null,
          comentarioAprobacion: comentarioAprobacion || null,
          comentarioRechazo: comentarioRechazo || null
        };
      },
      
      didOpen: () => {
        // ✅ LISTENER PARA MOSTRAR/OCULTAR CAMPOS SEGÚN LA DECISIÓN
        // 🔴 IMPORTANTE: Remover listeners previos para evitar duplicados
        $('#nuevaAprobacion').off('change').on('change', function() {
          const decision = $(this).val();
          const campoRRHH = $('#campo-rrhh');
          const campoComentarioAprobacion = $('#campo-comentario-aprobacion');
          const campoComentarioRechazo = $('#campo-comentario-rechazo');
          
          // Ocultar todos los campos primero
          campoRRHH.slideUp(200);
          campoComentarioAprobacion.slideUp(200);
          campoComentarioRechazo.slideUp(200);
          
          // Limpiar campos y remover required
          $('#swal-dirigido-rh').attr('required', false).val('');
          $('#swal-comentario-aprobacion').attr('required', false).val('');
          $('#swal-comentario-rechazo').attr('required', false).val('');
          
          if (decision === 'Aprobado') {
            // Mostrar tanto el campo de RRHH como el de comentario de aprobación
            campoRRHH.slideDown(300);
            campoComentarioAprobacion.slideDown(300);
            $('#swal-dirigido-rh').attr('required', true);
            $('#swal-comentario-aprobacion').attr('required', true);
          } else if (decision === 'No Aprobado') {
            // Mostrar solo el campo de comentario de rechazo
            campoComentarioRechazo.slideDown(300);
            $('#swal-comentario-rechazo').attr('required', true);
          }
        });

        // Agregar estilos personalizados (solo una vez)
        if (!document.getElementById('aprobacion-styles-grande')) {
          const styles = document.createElement('style');
          styles.id = 'aprobacion-styles-grande';
          styles.textContent = `
            .aprobacion-modal-grande {
              border-radius: 16px !important;
              box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2) !important;
              font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            }
            .aprobacion-modal-grande .swal2-title {
              font-size: 24px !important;
              font-weight: 700 !important;
              color: #333 !important;
              margin-bottom: 20px !important;
            }
            .aprobacion-modal-grande select:focus,
            .aprobacion-modal-grande textarea:focus {
              border-color: #667eea !important;
              box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
              outline: none !important;
            }
            .aprobacion-modal-grande .btn {
              font-weight: 600 !important;
              border-radius: 10px !important;
              padding: 12px 24px !important;
              font-size: 16px !important;
              transition: all 0.3s ease !important;
              margin: 5px !important;
            }
            .aprobacion-modal-grande .btn:hover {
              transform: translateY(-2px) !important;
              box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2) !important;
            }
            .aprobacion-modal-grande .swal2-actions {
              margin-top: 30px !important;
            }
          `;
          document.head.appendChild(styles);
        }
        
        // Focus en el select con delay
        setTimeout(() => {
          $('#nuevaAprobacion').focus();
        }, 150);
      }
    }).then((result) => {
      if (result.isConfirmed) {
        console.log("📤 Enviando cambio de aprobación:", {
          id_solicitud: id,
          nueva_aprobacion: result.value.nuevaAprobacion,
          dirigido_rh: result.value.dirigidoRH,
          comentario_aprobacion: result.value.comentarioAprobacion,
          comentario_rechazo: result.value.comentarioRechazo
        });

        // Mostrar loading
        Swal.fire({
          title: '<i class="fas fa-spinner fa-spin"></i> Procesando cambio...',
          html: `
            <div style="text-align: center; padding: 20px;">
              <div style="font-size: 16px; margin-bottom: 10px;">
                Actualizando estado de aprobación
              </div>
              <div style="color: #666; font-size: 14px;">
                Por favor espera un momento...
              </div>
            </div>
          `,
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        // 🆕 CONSTRUIR LA DATA PARA ENVIAR AL SERVIDOR
        const dataToSend = {
          id_solicitud: id,
          nueva_aprobacion: result.value.nuevaAprobacion
        };

        // ✅ MANEJAR COMENTARIOS SEGÚN EL TIPO DE DECISIÓN
        if (result.value.nuevaAprobacion === 'Aprobado') {
          dataToSend.dirigido_rh = result.value.dirigidoRH;
          dataToSend.comentario = result.value.comentarioAprobacion;
          dataToSend.tipo_comentario = 'aprobacion';
        } else if (result.value.nuevaAprobacion === 'No Aprobado') {
          dataToSend.comentario = result.value.comentarioRechazo;
          dataToSend.tipo_comentario = 'rechazo';
        } else {
          dataToSend.comentario = `Cambio de aprobación a: ${result.value.nuevaAprobacion}`;
          dataToSend.tipo_comentario = 'general';
        }

        $.ajax({
          url: './GerenteTDS/crudaprobaciones.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'procesar_aprobacion_gerente',
            ...dataToSend
          },
          success: function(response) {
            console.log("✅ Respuesta exitosa del servidor:", response);

            if (response.success) {
              // 🟢 MENSAJE DE ÉXITO MEJORADO
              let mensajeExito = `
                <div style="text-align: center; padding: 15px;">
                  <div style="font-size: 16px; margin-bottom: 10px;">
                    El estado de aprobación ha sido actualizado correctamente
                  </div>
                  <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px; color: #155724;">
                    <strong><i class="fas fa-check"></i> Nuevo Estado:</strong> ${result.value.nuevaAprobacion}
                  </div>`;

              if (result.value.nuevaAprobacion === 'Aprobado') {
                mensajeExito += `
                  <div style="background: #cce5ff; border: 1px solid #99d1ff; border-radius: 8px; padding: 12px; color: #004085; margin-top: 10px;">
                    <strong><i class="fas fa-user-check"></i> Asignada a:</strong> ${result.value.dirigidoRH}
                  </div>
                  <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 12px; color: #155724; margin-top: 10px;">
                    <strong><i class="fas fa-comment-check"></i> Comentario de aprobación guardado correctamente</strong>
                  </div>`;
              } else if (result.value.nuevaAprobacion === 'No Aprobado') {
                mensajeExito += `
                  <div style="background: #f8d7da; border: 1px solid #f1b0b7; border-radius: 8px; padding: 12px; color: #721c24; margin-top: 10px;">
                    <strong><i class="fas fa-comment"></i> Motivo del rechazo enviado al supervisor</strong>
                  </div>`;
              }

              mensajeExito += `</div>`;

              Swal.fire({
                icon: 'success',
                title: '<i class="fas fa-check-circle"></i> Cambio Realizado!',
                html: mensajeExito,
                timer: 3000,
                showConfirmButton: false
              });

              // 🔁 Recargar la tabla principal después de un pequeño delay
              setTimeout(() => {
                if (typeof cargarSolicitudes === 'function') {
                  cargarSolicitudes();
                }
              }, 1000);

            } else {
              console.error("❌ Error en respuesta del servidor:", response);
              Swal.fire({
                icon: 'error',
                title: '<i class="fas fa-exclamation-circle"></i> Error',
                text: response.error || 'Error al actualizar el estado de aprobación',
                confirmButtonText: 'Entendido'
              });
            }
          },
          error: function(xhr, status, error) {
            console.error('❌ Error AJAX completo:', {
              status: xhr.status,
              statusText: xhr.statusText,
              responseText: xhr.responseText,
              error: error,
              url: './GerenteTDS/crudaprobaciones.php'
            });

            Swal.fire({
              icon: 'error',
              title: '<i class="fas fa-wifi"></i> Error de Conexión',
              html: `
                <div style="text-align: left;">
                  <p>No se pudo conectar al servidor.</p>
                  <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 10px;">
                    <small><strong>Status:</strong> ${xhr.status}</small><br>
                    <small><strong>Error:</strong> ${error}</small>
                  </div>
                </div>
              `,
              confirmButtonText: 'Entendido'
            });
          }
        });
      }
    });
  }, 100); // 🔴 Delay de 100ms antes de abrir el modal
});


// FUNCIÓN PARA VER RESULTADO DE APROBACIÓN (SOLO LECTURA)
$(document).off('click', '.btnVerResumenGerencial');

// USAR CAPTURE PHASE PARA MÁXIMA PRIORIDAD
document.addEventListener('click', function(e) {
  if (e.target.closest('.btnVerResumenGerencial')) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    
    const btn = e.target.closest('.btnVerResumenGerencial');
    const idSolicitud = btn.dataset.id;
    const estadoAprobacion = btn.dataset.aprobacion;
    
    console.log("Ver resultado de aprobación para solicitud:", idSolicitud);
    console.log("Evento capturado correctamente");
    
    // Mostrar loading
    Swal.fire({
      title: '<i class="fas fa-spinner fa-spin"></i> Cargando información...',
      text: 'Obteniendo detalles del resultado de aprobación',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => Swal.showLoading()
    });
    
    // OBTENER INFORMACIÓN DETALLADA
    $.ajax({
      url: './GerenteTDS/crudaprobaciones.php?action=get_resultado_procesamiento_gerencial',
      type: 'POST',
      dataType: 'json',
      data: {
        id_solicitud: idSolicitud
      },
      success: function(response) {
        console.log("Respuesta del servidor:", response);
        
        if (response.success) {
          const datos = response.data;
          
          // CONSTRUIR MODAL DE SOLO LECTURA
          const modalHtml = `
            <div style="text-align: left; max-width: 100%;">
              <!-- Encabezado con estado actual -->
              <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 25px; border-radius: 15px; margin-bottom: 25px; text-align: center;">
                <div style="font-size: 24px; font-weight: 700; margin-bottom: 10px;">
                  <i class="fas fa-exclamation-triangle" style="margin-right: 12px;"></i>
                  Solicitud Rechazada
                </div>
                <div style="font-size: 16px; opacity: 0.9;">
                  Su solicitud ha sido revisada por el gerente y no ha sido aprobada
                </div>
              </div>
              
              <!-- Información de la solicitud -->
              <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                <h5 style="margin: 0 0 15px 0; color: #495057; font-weight: 600;">
                  <i class="fas fa-info-circle mr-2"></i>Información de la Solicitud
                </h5>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                  <div>
                    <strong><i class="fas fa-hashtag mr-1"></i>ID Solicitud:</strong><br>
                    <span style="color: #007bff; font-weight: 600;">#${datos.ID_SOLICITUD}</span>
                  </div>
                  <div>
                    <strong><i class="fas fa-store mr-1"></i>Tienda:</strong><br>
                    <span style="color: #6f42c1; font-weight: 600;">Tienda ${datos.NUM_TIENDA}</span>
                  </div>
                  <div>
                    <strong><i class="fas fa-briefcase mr-1"></i>Puesto Solicitado:</strong><br>
                    <span style="color: #e83e8c; font-weight: 600;">${datos.PUESTO_SOLICITADO}</span>
                  </div>
                  <div>
                    <strong><i class="fas fa-calendar-alt mr-1"></i>Fecha de Solicitud:</strong><br>
                    <span style="color: #20c997; font-weight: 600;">${datos.FECHA_SOLICITUD}</span>
                  </div>
                  <div style="grid-column: 1 / -1;">
                    <strong><i class="fas fa-clipboard-list mr-1"></i>Razón de la Vacante:</strong><br>
                    <span style="color: #fd7e14; font-weight: 600;">${datos.RAZON}</span>
                  </div>
                </div>
              </div>
              
              <!-- Estado de aprobación actual -->
              <div style="background: #fff5f5; border: 2px solid #fc8181; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                <h5 style="margin: 0 0 15px 0; color: #c53030; font-weight: 600;">
                  <i class="fas fa-times-circle mr-2"></i>Estado de Aprobación
                </h5>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                  <div>
                    <div style="font-size: 18px; font-weight: 700; color: #c53030;">
                      ${datos.ESTADO_APROBACION}
                    </div>
                    <div style="font-size: 14px; color: #718096; margin-top: 5px;">
                      Revisado por: ${datos.GERENTE_DECISION || 'Gerente'}
                    </div>
                  </div>
                  <div style="background: #c53030; color: white; padding: 12px 20px; border-radius: 25px; font-weight: 600;">
                    <i class="fas fa-ban mr-2"></i>RECHAZADA
                  </div>
                </div>
              </div>
              
              <!-- Motivo del rechazo -->
              <div style="background: #fffbf0; border: 2px solid #f6ad55; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                <h5 style="margin: 0 0 15px 0; color: #c05621; font-weight: 600;">
                  <i class="fas fa-comment-alt mr-2"></i>Motivo del Rechazo
                </h5>
                <div style="background: white; border: 1px solid #f6ad55; border-radius: 8px; padding: 15px;">
                  <div style="font-size: 16px; color: #2d3748; line-height: 1.6; min-height: 40px;">
                    ${datos.COMENTARIO_RECHAZO || 'No se proporcionó un motivo específico para el rechazo.'}
                  </div>
                </div>
                <div style="margin-top: 12px; font-size: 12px; color: #718096;">
                  <i class="fas fa-clock mr-1"></i>
                  Fecha del rechazo: ${datos.FECHA_RECHAZO || datos.FECHA_MODIFICACION}
                </div>
              </div>
              
              <!-- Información adicional y próximos pasos -->
              <div style="background: #e6fffa; border: 1px solid #81e6d9; border-radius: 12px; padding: 20px;">
                <h5 style="margin: 0 0 15px 0; color: #234e52; font-weight: 600;">
                  <i class="fas fa-lightbulb mr-2"></i>Próximos Pasos
                </h5>
                <div style="color: #2c7a7b; font-size: 14px; line-height: 1.6;">
                  <div style="margin-bottom: 10px;">
                    <i class="fas fa-arrow-right mr-2"></i>
                    <strong>Puede revisar el motivo del rechazo</strong> para entender las razones de la decisión
                  </div>
                  <div style="margin-bottom: 10px;">
                    <i class="fas fa-arrow-right mr-2"></i>
                    <strong>Si considera necesario,</strong> puede crear una nueva solicitud corrigiendo los aspectos mencionados
                  </div>
                  <div>
                    <i class="fas fa-arrow-right mr-2"></i>
                    <strong>Para dudas adicionales,</strong> puede contactar directamente con el gerente para aclaraciones
                  </div>
                </div>
              </div>
            </div>
          `;
          
          // ✅ MOSTRAR MODAL DE SOLO LECTURA
          Swal.fire({
            title: false,
            html: modalHtml,
            width: '800px',
            showCloseButton: true,
            showConfirmButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Entendido',
            confirmButtonColor: '#6c757d'
          });
          
        } else {
          // ✅ ERROR EN LA RESPUESTA
          Swal.fire({
            icon: 'error',
            title: '<i class="fas fa-exclamation-triangle"></i> Error',
            text: response.error || 'No se pudo obtener la información del resultado de aprobación',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#dc3545'
          });
        }
      },
      error: function(xhr, status, error) {
        console.error('❌ Error obteniendo resultado de aprobación:', {
          status: xhr.status,
          responseText: xhr.responseText,
          error: error
        });
        
        Swal.fire({
          icon: 'error',
          title: '<i class="fas fa-wifi"></i> Error de Conexión',
          text: 'No se pudo conectar al servidor para obtener la información',
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#dc3545'
        });
      }
    });
    
    return false;
  }
}, true); // ← TRUE = CAPTURE PHASE (MÁXIMA PRIORIDAD)


// VER RESUMEN APROBACION ACEPTADA
    $(document).on('click', '.btnVerResultadoAprobGerencial', function() {
    const id = $(this).data('id');
    const solicitudId = $(this).data('solicitud-id') || id;
    
    // 🆕 OBTENER NOMBRE DEL GERENTE DESDE LA INTERFAZ (RRHH)
    const filaActual = $(this).closest('tr');
    const nombreGerente = filaActual.find('td:nth-child(5)').text().trim() || 'Gerente'; // Ajustar columna según tu tabla
    
    console.log("📋 Gerente cargando resumen de aprobacion para solicitud:", solicitudId);
    console.log("👤 Nombre del gerente obtenido desde tabla:", nombreGerente);
    
    // Mostrar loading
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando informacion...',
        html: 'Obteniendo detalles de la aprobacion...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    // 🆕 USAR EL MISMO ENDPOINT QUE FUNCIONA PARA GERENTES
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=obtener_resumen_grnts',
        type: 'GET',
        dataType: 'json',
        data: { id_solicitud: solicitudId },
        success: function(response) {
            console.log("✅ Resumen obtenido:", response);
            
            if (response.success) {
                const solicitud = response.solicitud;
                const resumen = response.resumen_aprobacion;
                
                // 🆕 USAR FECHA YA FORMATEADA DEL SERVIDOR
                const fechaProceso = resumen.fecha_procesamiento || 'No disponible';
                
                // 🆕 USAR NOMBRE DEL GERENTE OBTENIDO DE LA INTERFAZ
                const nombreGerenteCompleto = nombreGerente !== 'Gerente' ? nombreGerente : (resumen.procesado_por || 'No disponible');
                
                Swal.fire({
                    title: '<i class="fas fa-clipboard-check"></i> Resumen de Aprobacion',
                    html: `
                        <div style="text-align: left; max-width: 100%;">
                            <!-- INFORMACION BASICA DE LA SOLICITUD -->
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                                <h5 style="margin: 0 0 15px 0; font-weight: 700; display: flex; align-items: center;">
                                    <i class="fas fa-file-alt" style="margin-right: 10px; font-size: 20px;"></i>
                                    Informacion de la Solicitud
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                                    <div><strong>ID:</strong> #${solicitud.id}</div>
                                    <div><strong>Tienda:</strong> ${solicitud.tienda || 'N/A'}</div>
                                    <div><strong>Puesto:</strong> ${solicitud.puesto_solicitado || 'N/A'}</div>
                                    <div><strong>Supervisor:</strong> ${solicitud.supervisor || 'N/A'}</div>
                                    <div style="grid-column: 1 / -1;"><strong>Fecha de Solicitud:</strong> ${solicitud.fecha_solicitud || 'N/A'}</div>
                                </div>
                            </div>

                            <!-- RESUMEN DE LA APROBACION -->
                            <div style="background: #d4edda; border: 2px solid #28a745; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #155724; display: flex; align-items: center;">
                                    <i class="fas fa-check-circle" style="margin-right: 10px; font-size: 18px; color: #28a745;"></i>
                                    Estado: APROBADA
                                </h6>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <div>
                                        <strong style="color: #155724;">
                                            <i class="fas fa-user-check"></i> Procesado por:
                                        </strong><br>
                                        <span style="background: #c3e6cb; padding: 4px 8px; border-radius: 6px; font-size: 13px;">
                                            ${nombreGerenteCompleto}
                                        </span>
                                    </div>
                                    <div>
                                        <strong style="color: #155724;">
                                            <i class="fas fa-calendar-check"></i> Fecha de Procesamiento:
                                        </strong><br>
                                        <span style="background: #c3e6cb; padding: 4px 8px; border-radius: 6px; font-size: 13px;">
                                            ${fechaProceso}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- ASIGNACION A RRHH -->
                            <div style="background: #cce5ff; border: 2px solid #007bff; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #004085; display: flex; align-items: center;">
                                    <i class="fas fa-user-plus" style="margin-right: 10px; font-size: 18px; color: #007bff;"></i>
                                    Asignacion de RRHH
                                </h6>
                                <div style="text-align: center;">
                                    <div style="background: #b3d9ff; border-radius: 8px; padding: 15px; display: inline-block;">
                                        <i class="fas fa-user-tie" style="font-size: 24px; color: #0056b3; margin-bottom: 8px;"></i><br>
                                        <strong style="font-size: 16px; color: #004085;">
                                            ${solicitud.dirigido_rh || resumen.asignado_a || 'No asignado'}
                                        </strong><br>
                                        <small style="color: #6c757d;">Responsable de RRHH</small>
                                    </div>
                                </div>
                            </div>

                            <!-- COMENTARIO DE APROBACION -->
                            <div style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 12px; padding: 20px;">
                                <h6 style="margin: 0 0 15px 0; font-weight: 700; color: #856404; display: flex; align-items: center;">
                                    <i class="fas fa-comment-alt" style="margin-right: 10px; font-size: 18px; color: #ffc107;"></i>
                                    Comentario de Aprobacion del Gerente
                                </h6>
                                <div style="background: white; border-radius: 8px; padding: 15px; border: 1px solid #ffeaa7;">
                                    <p style="margin: 0; line-height: 1.6; color: #333;">
                                        ${resumen.comentario_aprobacion || 'Sin comentario adicional'}
                                    </p>
                                </div>
                                <small style="color: #856404; margin-top: 10px; display: block;">
                                    <i class="fas fa-info-circle"></i> 
                                    Fecha del comentario: ${fechaProceso}
                                </small>
                            </div>

                            <!-- ACCIONES DISPONIBLES -->
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-top: 25px; text-align: center;">
                                <h6 style="color: #495057; margin-bottom: 15px;">
                                    <i class="fas fa-tools"></i> La solicitud ya fue aprobada, favor seguir con el proceso de reclutamiento
                                </h6>

                    `,
                    width: '800px',
                    showCancelButton: false,
                    confirmButtonText: '<i class="fas fa-times"></i> Cerrar',
                    confirmButtonColor: '#6c757d',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'resumen-aprobacion-modal',
                        confirmButton: 'btn btn-secondary btn-lg px-4'
                    },
                    didOpen: () => {
                        // Agregar estilos especificos para este modal
                        if (!document.getElementById('resumen-aprobacion-styles')) {
                            const styles = document.createElement('style');
                            styles.id = 'resumen-aprobacion-styles';
                            styles.textContent = `
                                .resumen-aprobacion-modal {
                                    border-radius: 16px !important;
                                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
                                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
                                }
                                .resumen-aprobacion-modal .swal2-title {
                                    font-size: 24px !important;
                                    font-weight: 700 !important;
                                    color: #333 !important;
                                    margin-bottom: 20px !important;
                                }
                                .resumen-aprobacion-modal .btn {
                                    font-weight: 600 !important;
                                    border-radius: 8px !important;
                                    transition: all 0.3s ease !important;
                                }
                                .resumen-aprobacion-modal .btn:hover {
                                    transform: translateY(-2px) !important;
                                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
                                }
                            `;
                            document.head.appendChild(styles);
                        }
                    }
                });
                
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'No se pudo cargar la informacion de la solicitud',
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar resumen:', {
                status: xhr.status,
                error: error,
                responseText: xhr.responseText
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexion',
                text: 'No se pudo cargar la informacion de la solicitud',
                confirmButtonText: 'Entendido'
            });
        }
    });
});

// ================================================================================================
// NUEVAS FUNCIONES PARA EL FUNCIONAMIENTO DE SOLICITUDES DE Personal
// ================================================================================================

// CONFIGURACIÓN PARA GERENTES - SOLO LECTURA
window.VISTA_ACTUAL = 'GERENTE';
window.ROL_USUARIO = 'GERENTE';

// FUNCIÓN PARA MOSTRAR CANDIDATOS ENVIADOS - SOLO LECTURA GERENTES
window.mostrarCandidatosEnviadosGerente = function(idSolicitud) {
    console.log('📋 Mostrando candidatos para solicitud:', idSolicitud);
    
    if (!idSolicitud) {
        Swal.fire('Error', 'ID de solicitud no proporcionado', 'error');
        return;
    }
    
    // Mostrar loading mientras carga candidatos
    Swal.fire({
        title: 'Cargando candidatos...',
        html: '<i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><br>Obteniendo lista de candidatos',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Cargar candidatos incluyendo información de descarte
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_candidatos_por_solicitud_gerente',
        type: 'GET',
        data: { id_solicitud: idSolicitud },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta:', response);
            Swal.close();
            
            if (response.success) {
                // ✅ VERIFICAR SI ESTÁ ESPERANDO A RH
                if (response.esperando_rh === true) {
                    console.log('⏳ Esperando a RH');
                    const solicitud = response.solicitud || {};
                    mostrarMensajeReactivacionGerente(idSolicitud, solicitud, []);
                    return;
                }
                
                // ✅ PROCESO NORMAL CON CANDIDATOS
                if (response.candidatos && response.candidatos.length > 0) {
                    console.log('📊 Mostrando candidatos:', response.candidatos.length);
                    
                    window.CANDIDATOS_INDEX = {};
                    response.candidatos.forEach(candidato => {
                        candidato.ID_SOLICITUD = idSolicitud;
                        window.CANDIDATOS_INDEX[candidato.ID_CANDIDATO] = candidato;
                    });
                    
                    mostrarModalExpedientesGerente(idSolicitud, response.candidatos);
                } else {
                    Swal.fire('Info', 'No se encontraron candidatos', 'info');
                }
            } else {
                Swal.fire('Error', response.error || 'Error al cargar candidatos', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error AJAX:', error, xhr.responseText);
            Swal.close();
            
            // ✅ En lugar de mostrar error, mostrar mensaje de RH gestionando
            mostrarMensajeReactivacionGerente(idSolicitud, {
                motivo_reactivacion: 'El sistema está procesando la reactivación. RH confirmará los candidatos pronto.',
                num_tienda: '',
                puesto_solicitado: '',
                supervisor: ''
            }, []);
        },
        timeout: 10000
    });
};


//MOSTRAR MENSAJE ESPECIAL PARA SOLICITUDES REACTIVADAS - GERENTES
//MOSTRAR MENSAJE ESPECIAL PARA SOLICITUDES REACTIVADAS - GERENTES
function mostrarMensajeReactivacionGerente(idSolicitud, solicitud, candidatos) {
    const motivoReactivacion = solicitud.motivo_reactivacion || 'No especificado';
    
    const htmlModal = `
        <div class="modal fade" id="modalReactivacionGerente" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h4 class="modal-title">
                            <i class="fas fa-hourglass-half mr-2"></i>
                            Solicitud en Proceso de Reactivación
                        </h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body text-center" style="padding: 40px;">
                        <i class="fas fa-clock fa-4x text-warning mb-4"></i>
                        
                        <h5 class="mb-3">Esperando Selección de Recursos Humanos</h5>
                        
                        <div class="alert alert-info text-left">
                            <p class="mb-2"><strong><i class="fas fa-info-circle mr-2"></i>Motivo de Reactivación:</strong></p>
                            <p class="mb-0" style="white-space: pre-wrap;">${motivoReactivacion}</p>
                        </div>
                        
                        <p class="text-muted">
                            La asesora de Recursos Humanos está revisando los candidatos disponibles para esta solicitud.
                        </p>
                        
                        <p class="text-muted">
                            <strong>Los candidatos estarán disponibles una vez que RH confirme su selección.</strong>
                        </p>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#modalReactivacionGerente').remove();
    $('body').append(htmlModal);
    
    // ✅ FORZAR APERTURA (igual que la otra función)
    const modalElement = document.getElementById('modalReactivacionGerente');
    if (modalElement) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modalElement);
            modalInstance.show();
        } else if (typeof $.fn.modal !== 'undefined') {
            $(modalElement).modal('show');
        } else {
            $(modalElement).addClass('show').css('display', 'block');
            $('body').addClass('modal-open').append('<div class="modal-backdrop fade show"></div>');
        }
    }
}

// FUNCIÓN PARA MOSTRAR MODAL DE EXPEDIENTES - IGUAL QUE SUPERVISORES
// FUNCIÓN PARA MOSTRAR MODAL DE EXPEDIENTES - IGUAL QUE SUPERVISORES
window.mostrarModalExpedientesGerente = function(idSolicitud, candidatos) {
    // Obtener información de la solicitud desde la tabla
    const filaSolicitud = $(`tr[data-id="${idSolicitud}"]`);
    const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(2)').text().trim() : 'No disponible';
    const puestoInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(3)').text().trim() : 'No disponible';
    const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(4)').text().trim() : 'No disponible';
    
    // ✅ AGREGADO: Detectar si hay candidatos reactivados
    const hayReactivados = candidatos.some(c => c.REACTIVADO_POST_CONTRATACION === 'Y');
    
    // Contar candidatos activos vs descartados
    const candidatosActivos = candidatos.filter(c => c.ACTIVO === 'Y' && c.ESTADO_CANDIDATO !== 'Descartado');
    const candidatosDescartados = candidatos.filter(c => c.ACTIVO === 'N' || c.ESTADO_CANDIDATO === 'Descartado');
    const candidatosAvales = candidatos.filter(c => {
    const estadoActual = (c.ESTADO_CANDIDATO || '').toLowerCase();
    // Los candidatos de avales son los que están en "Aprobación de Aval Enviado" o ya fueron procesados
    return estadoActual.includes('aprobacion') && estadoActual.includes('aval') ||
           estadoActual.includes('aval') ||
           (c.DECISION_AVAL && c.DECISION_AVAL !== 'Pendiente'); // Los que ya tienen decisión tomada
    });
    
    const modalHtml = `
        <div class="modal fade" id="modalExpedientesGerente" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-users mr-2"></i>Expedientes - Solicitud ${idSolicitud} 
                            <span class="badge badge-light ml-2">${candidatos.length} candidatos</span>
                            <span class="badge badge-success ml-1">${candidatosActivos.length} activos</span>
                            <span class="badge badge-danger ml-1">${candidatosDescartados.length} descartados</span>
                            <span class="badge badge-warning ml-2">Solo Lectura</span>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="row no-gutters" style="height: 80vh;">
                            <!-- PANEL IZQUIERDO - LISTA DE CANDIDATOS -->
                            <div class="col-md-4 bg-light border-right">
                                <div class="p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-users mr-2"></i>Lista de Candidatos
                                        </h6>
                                        <div>
                                            <span class="badge badge-success" id="totalActivos">${candidatosActivos.length}</span>
                                            <span class="badge badge-danger" id="totalDescartados">${candidatosDescartados.length}</span>
                                        </div>
                                    </div>
                                    
                                    ${hayReactivados ? `
                                    <div class="alert alert-info mb-3" style="background: #e3f2fd; border-left: 4px solid #2196f3; padding: 10px;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-redo-alt fa-2x text-primary mr-2"></i>
                                            <div style="font-size: 0.85rem;">
                                                <strong>Candidatos Reactivados</strong>
                                                <p class="mb-0" style="font-size: 0.8rem;">Mostrando ${candidatos.length} candidato${candidatos.length > 1 ? 's' : ''} reactivado${candidatos.length > 1 ? 's' : ''}.</p>
                                                <small class="text-muted">Los contratados están ocultos.</small>
                                            </div>
                                        </div>
                                    </div>
                                    ` : ''}
                                    
                                    <!-- FILTROS RÁPIDOS -->
                                    <div class="btn-group btn-group-sm w-100 mb-3">
                                        <button type="button" class="btn btn-outline-primary active" data-filter="todos">
                                            Todos (${candidatos.length})
                                        </button>
                                        <button type="button" class="btn btn-outline-success" data-filter="activos">
                                            Activos (${candidatosActivos.length})
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-filter="descartados">
                                            Descartados (${candidatosDescartados.length})
                                        </button>
                                            <!--NUEVO BOTÓN PARA AVALES -->
                                        <button type="button" class="btn btn-outline-warning" data-filter="avales">
                                            Avales (${candidatosAvales.length})
                                        </button>
                                    </div>
                                    
                                    <div id="listaCandidatosGerente" style="max-height: calc(100vh - 300px); overflow-y: auto;">
                                      ${candidatos.map(c => {
                                          const nombreCompleto = `${c.NOMBRE_CANDIDATO || ''} ${c.APELLIDOS_CANDIDATO || ''}`.trim();
                                          const totalArchivos = c.TOTAL_ARCHIVOS || 0;
                                          
                                          // ✅ AGREGADO: Verificar si es reactivado
                                          const esReactivado = c.REACTIVADO_POST_CONTRATACION === 'Y';
                                          
                                          // ✅ CLASIFICAR CANDIDATOS: DESCARTADOS, AVALES O ACTIVOS
                                          const esDescartado = c.ACTIVO === 'N' || c.ESTADO_CANDIDATO === 'Descartado';
                                          const estadoActual = (c.ESTADO_CANDIDATO || '').toLowerCase();
                                          const esAval = estadoActual.includes('aprobacion') && estadoActual.includes('aval');
                                          
                                          // ✅ DETERMINAR SI ES APROBADO, RECHAZADO O PENDIENTE
                                          const esAprobado = c.APROBACION === 'Y';
                                          const esRechazado = c.APROBACION === 'N';
                                          const esPendiente = estadoActual === 'aprobacion de aval' && !c.APROBACION;
                                          const esAvalProcesado = estadoActual === 'aprobacion de aval enviado';
                                          
                                          // ✅ DETERMINAR CLASE CSS Y COLOR
                                          let claseCSS = '';
                                          let colorBorde = '';
                                          let colorTexto = '';
                                          let colorFondo = '';
                                          
                                          if (esDescartado) {
                                              claseCSS = 'candidato-descartado';
                                              colorBorde = '#dc3545';
                                              colorTexto = '#721c24';
                                              colorFondo = '#f8d7da';
                                          } else if (esAprobado) {
                                              claseCSS = 'candidato-aprobado';
                                              colorBorde = '#28a745';
                                              colorTexto = '#155724';
                                              colorFondo = '#d4edda';
                                          } else if (esRechazado) {
                                              claseCSS = 'candidato-rechazado';
                                              colorBorde = '#dc3545';
                                              colorTexto = '#721c24';
                                              colorFondo = '#f8d7da';
                                          } else if (esPendiente) {
                                              claseCSS = 'candidato-pendiente-aval';
                                              colorBorde = '#ffc107';
                                              colorTexto = '#856404';
                                              colorFondo = '#fff3cd';
                                          } else if (esAval) {
                                              claseCSS = 'candidato-aval';
                                              colorBorde = '#ffc107';
                                              colorTexto = '#856404';
                                              colorFondo = '#fff3cd';
                                          } else {
                                              claseCSS = 'candidato-activo';
                                              colorBorde = '#007bff';
                                              colorTexto = '#004085';
                                              colorFondo = '#ffffff';
                                          }
                                          
                                          const yaProcesado = esAvalProcesado ? 'true' : 'false';
                                          
                                          return `
                                              <div class="candidate-card mb-2 ${claseCSS}" 
                                                  data-candidato-id="${c.ID_CANDIDATO}" 
                                                  data-estado="${esDescartado ? 'descartado' : (esAprobado ? 'aprobado' : (esRechazado ? 'rechazado' : (esPendiente ? 'pendiente' : 'activo')))}"
                                                  data-ya-procesado="${yaProcesado}">
                                                  <div class="card" style="border-left: 4px solid ${colorBorde}; background-color: ${colorFondo};">
                                                      <div class="card-body p-3">
                                                          <div class="d-flex align-items-center">
                                                              <div class="flex-grow-1">
                                                                  <h6 class="mb-1 font-weight-bold" style="color: ${colorTexto};">
                                                                      ${nombreCompleto}
                                                                      ${esDescartado ? '<span class="badge badge-danger ml-2"><i class="fas fa-times-circle"></i> Descartado</span>' : ''}
                                                                      ${esAprobado ? '<span class="badge badge-success ml-2"><i class="fas fa-check-circle"></i> Aprobado</span>' : ''}
                                                                      ${esRechazado ? '<span class="badge badge-danger ml-2"><i class="fas fa-times-circle"></i> Rechazado</span>' : ''}
                                                                      ${esPendiente ? '<span class="badge badge-warning ml-2"><i class="fas fa-clock"></i> Pendiente Aval</span>' : ''}
                                                                      ${esReactivado ? '<span class="badge badge-warning ml-2" style="font-size: 0.75rem;"><i class="fas fa-redo mr-1"></i>Reactivado</span>' : ''}
                                                                  </h6>
                                                                  <p class="mb-1 text-muted small">
                                                                      <i class="fas fa-id-card mr-1"></i>${c.DOCUMENTO_CANDIDATO || 'Sin documento'}
                                                                  </p>
                                                                  <p class="mb-0 text-muted small">
                                                                      <i class="fas fa-info-circle mr-1"></i>${c.ESTADO_CANDIDATO}
                                                                  </p>
                                                              </div>
                                                              <div class="text-right">
                                                                  <span class="badge badge-info">
                                                                      <i class="fas fa-file mr-1"></i>${totalArchivos} archivos
                                                                  </span>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          `;
                                      }).join('')}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PANEL DERECHO - EXPEDIENTE -->
                            <div class="col-md-8" style="background: #f8f9fa;">
                                <div class="p-4">
                                    <div id="expedienteCandidatoGerente">
                                        <div class="text-center py-5" style="margin-top: 100px;">
                                            <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <h5 class="text-muted">Selecciona un candidato</h5>
                                            <p class="text-muted">Haz clic en un candidato de la lista para ver su expediente completo</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<style>
    /* Candidatos descartados */
    .candidato-descartado .card {
        border-left: 4px solid #dc3545 !important;
        background: linear-gradient(135deg, #fff5f5, #ffe6e6);
    }
    .candidato-descartado .card:hover {
        background: linear-gradient(135deg, #ffe6e6, #ffd6d6);
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
    
    /* Candidatos aval (genérico) */
    .candidato-aval .card {
        border-left: 4px solid #17a2b8 !important;
        background: linear-gradient(135deg, #f0f9ff, #d1ecf1) !important;
    }
    .candidato-aval .card:hover {
        background: linear-gradient(135deg, #d1ecf1, #bee5eb) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
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
</style>
    `;
    
    // Limpiar modales anteriores y agregar nuevo
    $('#modalExpedientesGerente').remove();
    $('body').append(modalHtml);
    
    // Mostrar modal manualmente
    const modalElement = document.getElementById('modalExpedientesGerente');
    modalElement.style.display = 'block';
    modalElement.classList.add('show');
    document.body.classList.add('modal-open');

    // Agregar backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'modal-backdrop-gerente';
    document.body.appendChild(backdrop);
    
    // Configurar eventos de filtro
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        
        // Actualizar botones activos
        $('[data-filter]').removeClass('btn-filter-active').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-filter-active');
        // Aplicar clase específica según el filtro
        if (filter === 'avales') {
            $(this).removeClass('btn-outline-primary').addClass('btn-outline-warning');
        }
        
        // Aplicar filtro
        if (filter === 'todos') {
            $('.candidate-card').show();
        } else if (filter === 'activos') {
            $('.candidate-card').hide();
            $('.candidato-activo').show();
        } else if (filter === 'descartados') {
            $('.candidate-card').hide();
            $('.candidato-descartado').show();
        } else if (filter === 'avales') {
            $('.candidate-card').hide();
            $('.candidato-aval, .candidato-aprobado, .candidato-rechazado, .candidato-pendiente-aval').show();
        }
    });
    
    // Configurar evento de clic en candidatos
    $('.candidate-card').on('click', function(e) {
        e.preventDefault();
        const idCandidato = $(this).data('candidato-id');
        const candidato = window.CANDIDATOS_INDEX[idCandidato];
        const nombreCompleto = `${candidato.NOMBRE_CANDIDATO || ''} ${candidato.APELLIDOS_CANDIDATO || ''}`.trim();
        
        // Marcar como seleccionado
        $('.candidate-card .card').removeClass('border-primary bg-light border-danger');
        $(this).find('.card').addClass('bg-light');
        
        // Cargar expediente
        seleccionarCandidatoGerente(idCandidato, nombreCompleto);
    });

    // Manejar cierre manual del modal
    $('#modalExpedientesGerente .close').on('click', function() {
        document.getElementById('modalExpedientesGerente').style.display = 'none';
        document.getElementById('modalExpedientesGerente').classList.remove('show');
        document.body.classList.remove('modal-open');
        $('#modal-backdrop-gerente').remove();
        $('#modalExpedientesGerente').remove();
    });

    // Event listener específico para candidatos de aval procesados
    $(document).off('click', '.candidato-aval[data-ya-procesado="true"] .card').on('click', '.candidato-aval[data-ya-procesado="true"] .card', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const candidatoId = $(this).closest('.candidate-card').data('candidato-id');
        const candidato = window.CANDIDATOS_INDEX[candidatoId];
        
        if (candidato) {
            // Cerrar modal actual
            $('#modalExpedientesGerente').modal('hide');
            
            // Mostrar resultado del aval
            setTimeout(() => {
                mostrarResultadoAvalProcesado(candidato);
            }, 300);
        }
    });

};

// FUNCIÓN PARA SELECCIONAR CANDIDATO
window.seleccionarCandidatoGerente = function(idCandidato, nombreCandidato) {
    console.log('🎯 Candidato seleccionado:', idCandidato, nombreCandidato);
    
    // ✅ MOSTRAR LOADING INMEDIATAMENTE
    $('#expedienteCandidatoGerente').html(`
        <div class="text-center py-5" style="margin-top: 100px;">
            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                <span class="sr-only">Cargando...</span>
            </div>
            <h4 class="text-primary mt-4">
                <i class="fas fa-sync fa-spin mr-2"></i>Cargando expediente...
            </h4>
            <p class="text-muted mt-3">
                Obteniendo información de <strong>${nombreCandidato}</strong>
            </p>
            <div class="progress mx-auto mt-4" style="width: 60%; height: 8px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 100%">
                </div>
            </div>
        </div>
    `);
    
    // Obtener información del candidato desde el índice
    const candidato = window.CANDIDATOS_INDEX[idCandidato];
    
    if (!candidato) {
        console.error('❌ Candidato no encontrado en índice');
        $('#expedienteCandidatoGerente').html(`
            <div class="alert alert-danger mt-5 mx-5">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Error:</strong> No se encontró información del candidato
            </div>
        `);
        return;
    }
    
    const estadoActual = candidato.ESTADO_CANDIDATO;
    
    console.log('📊 Estado del candidato:', estadoActual);
    console.log('📋 Datos del candidato:', candidato);
    
    // ✅ PEQUEÑO DELAY PARA QUE SE VEA EL LOADING (opcional, puedes quitarlo)
    setTimeout(() => {
        // ✅ DECISIÓN: ¿Qué vista mostrar?
        if (estadoActual === 'Aprobacion de Aval Enviado') {
            // 🟢 YA FUE PROCESADO → Mostrar resultado (imagen 2)
            console.log('✅ Candidato procesado → Mostrando resultado del aval');
            mostrarResultadoAvalProcesado(candidato);
        } else if (estadoActual === 'Aprobacion de Aval') {
            // 🟡 PENDIENTE DE PROCESAR → Mostrar expediente básico (imagen 1)
            console.log('⏳ Candidato pendiente → Mostrando expediente básico');
            verExpedienteCandidatoGerente(idCandidato, nombreCandidato);
        } else {
            // 🔵 OTRO ESTADO → Mostrar expediente básico
            console.log('📄 Otro estado → Mostrando expediente básico');
            verExpedienteCandidatoGerente(idCandidato, nombreCandidato);
        }
    }, 300); // 300ms de delay - puedes ajustar o eliminar este valor
};


// FUNCIÓN PARA VER EXPEDIENTE DE CANDIDATO - SOLO LECTURA
window.verExpedienteCandidatoGerente = function(idCandidato, nombreCandidato) {
    console.log('Viendo expediente de candidato:', idCandidato);
    
    // OBTENER INFORMACIÓN COMPLETA DEL CANDIDATO DESDE EL ÍNDICE
    const candidato = window.CANDIDATOS_INDEX[idCandidato];
    if (!candidato) {
        $('#expedienteCandidatoGerente').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> 
                Error: No se encontró información del candidato
            </div>
        `);
        return;
    }
    
    const nombreReal = `${candidato.NOMBRE_CANDIDATO || ''} ${candidato.APELLIDOS_CANDIDATO || ''}`.trim();
    const esDescartado = candidato.ACTIVO === 'N' || candidato.ESTADO_CANDIDATO === 'Descartado';
    
    // Mostrar loading en el panel del expediente
    $('#expedienteCandidatoGerente').html(`
        <div class="text-center py-5">
            <div class="spinner-border ${esDescartado ? 'text-danger' : 'text-primary'}" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Cargando...</span>
            </div>
            <h5 class="text-muted mt-3">Cargando expediente...</h5>
            <p class="text-muted">Obteniendo información de ${nombreReal}</p>
            ${esDescartado ? '<p class="text-danger"><i class="fas fa-user-times"></i> Candidato descartado</p>' : ''}
        </div>
    `);
    
    // Obtener permisos e información detallada
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_permisos_subida_candidato_gerente',
        type: 'GET',
        data: {
            id_candidato: idCandidato,
            rol_usuario: 'GERENTE',
            incluir_motivo_descarte: true // ✅ NUEVO PARÁMETRO
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Incluir información del candidato en la respuesta
                response.candidato = candidato;
                mostrarExpedienteCompletoGerente(idCandidato, nombreReal, response);
            } else {
                $('#expedienteCandidatoGerente').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Error: ${response.error}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error obteniendo permisos:', error);
            $('#expedienteCandidatoGerente').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error al cargar expediente
                </div>
            `);
        }
    });
};

window.mostrarExpedienteCompletoGerente = function(idCandidato, nombreCandidato, datosCompletos) {

    console.log('=== DEBUG EXPEDIENTE GERENTE ===');
    console.log('ID Candidato:', idCandidato);
    console.log('Nombre Candidato:', nombreCandidato);
    console.log('Datos completos:', datosCompletos);
    
    if (typeof window.CANDIDATOS_INDEX !== 'undefined') {
        const candidato = window.CANDIDATOS_INDEX[idCandidato];
        console.log('Candidato del índice:', candidato);
        
        if (candidato) {
            console.log('ID_SOLICITUD del candidato:', candidato.ID_SOLICITUD);
        }
    }
    
    const todasLasFilas = $('tr[data-id]');
    console.log('Total filas en tabla:', todasLasFilas.length);
    
    todasLasFilas.each(function(index) {
        const idSolicitud = $(this).data('id');
        const tienda = $(this).find('td:nth-child(1)').text().trim();
        const fecha = $(this).find('td:nth-child(6)').text().trim();
        const puesto = $(this).find('td:nth-child(2)').text().trim();
        console.log(`Fila ${index}: ID=${idSolicitud}, Tienda=${tienda}, Puesto=${puesto}, Fecha=${fecha}`);
    });
    
    console.log('=== FIN DEBUG ===');

    const carpetas = datosCompletos.carpetas || [];
    const estadoActual = datosCompletos.estado_candidato || 'No definido';
    const puestoSolicitado = datosCompletos.puesto_solicitado || 'No definido';
    const motivoDescarte = datosCompletos.motivo_descarte || datosCompletos.candidato?.MOTIVO_DESCARTE || '';
    
    const infoDescarte = datosCompletos.info_descarte || {};
    const nombreQuienDescarto = infoDescarte.NOMBRE_QUIEN_DESCARTO || 'Usuario no identificado';
    const tipoUsuarioDescarto = infoDescarte.TIPO_USUARIO_DESCARTO || 'DESCONOCIDO';
    const fechaDescarte = infoDescarte.FECHA_CAMBIO || '';
    
    const esDescartado = estadoActual.toLowerCase() === 'descartado' || 
                        datosCompletos.candidato?.ACTIVO === 'N' ||
                        datosCompletos.candidato?.ESTADO_CANDIDATO === 'Descartado';
    
    // ✅ NUEVO: DETECTAR SI ESTÁ CONTRATADO
    const esContratado = estadoActual === 'Contratado';

    // OBTENER INFORMACIÓN DE LA SOLICITUD
    let tiendaInfo = 'No disponible';
    let supervisorInfo = 'No disponible';
    let fechaRegistro = 'No disponible';

    if (datosCompletos.num_tienda) {
        tiendaInfo = datosCompletos.num_tienda;
    }
    if (datosCompletos.supervisor) {
        supervisorInfo = datosCompletos.supervisor;
    }

    const candidato = window.CANDIDATOS_INDEX[idCandidato];
    if (candidato && candidato.ID_SOLICITUD) {
        const filaSolicitudEspecifica = $(`tr[data-id="${candidato.ID_SOLICITUD}"]`);
        if (filaSolicitudEspecifica.length > 0) {
            const tiendaEncontrada = filaSolicitudEspecifica.find('td:nth-child(1)').text().trim();
            const supervisorEncontrado = filaSolicitudEspecifica.find('td:nth-child(3)').text().trim();
            const fechaEncontrada = filaSolicitudEspecifica.find('td:nth-child(6)').text().trim();
            
            if (tiendaEncontrada && tiendaEncontrada !== '—') tiendaInfo = tiendaEncontrada;
            if (supervisorEncontrado && supervisorEncontrado !== '—') supervisorInfo = supervisorEncontrado;
            if (fechaEncontrada && fechaEncontrada !== '—') fechaRegistro = fechaEncontrada;
        }
        
        if (tiendaInfo === 'No disponible' && candidato.NUM_TIENDA) {
            tiendaInfo = candidato.NUM_TIENDA;
        }
        if (supervisorInfo === 'No disponible' && (candidato.SUPERVISOR || candidato.SOLICITADO_POR)) {
            supervisorInfo = candidato.SUPERVISOR || candidato.SOLICITADO_POR;
        }
    }

    if (tiendaInfo === 'No disponible' || supervisorInfo === 'No disponible' || fechaRegistro === 'No disponible') {
        const filaSolicitud = $(`tr[data-id]`).first();
        if (filaSolicitud.length > 0) {
            if (tiendaInfo === 'No disponible') {
                tiendaInfo = filaSolicitud.find('td:nth-child(1)').text().trim() || tiendaInfo;
            }
            if (supervisorInfo === 'No disponible') {
                supervisorInfo = filaSolicitud.find('td:nth-child(3)').text().trim() || supervisorInfo;
            }
            if (fechaRegistro === 'No disponible') {
                fechaRegistro = filaSolicitud.find('td:nth-child(6)').text().trim() || fechaRegistro;
            }
        }
    }
    
    console.log('📋 Información de solicitud obtenida:', {
        tienda: tiendaInfo,
        supervisor: supervisorInfo,
        candidato: nombreCandidato,
        id_solicitud: candidato?.ID_SOLICITUD
    });

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
        iconoPersona = 'fa-user-friends';
        etiquetaPersona = 'RRHH';
        colorPersona = 'success';
    } else {
        iconoPersona = 'fa-user-question';
        etiquetaPersona = 'Usuario';
        colorPersona = 'secondary';
    }

    // GENERAR HTML DE CARPETAS
    let carpetasHtml = '';
    const estadosConArchivos = carpetas.filter(c => c.ya_tiene_archivos);
    
    if (esDescartado) {
        if (estadosConArchivos.length > 0) {
            estadosConArchivos.forEach(carpeta => {
                carpetasHtml += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border-warning h-100">
                            <div class="card-body text-center d-flex flex-column">
                                <i class="fas fa-folder-open fa-2x text-warning mb-2"></i>
                                <h6 class="card-title">${carpeta.nombre_estado}</h6>
                                <span class="badge badge-warning">Completado</span>
                                <div class="mt-auto">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            onclick="verArchivosCarpeta('${idCandidato}', '${carpeta.nombre_estado}')">
                                        <i class="fas fa-eye"></i> Ver Archivos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            carpetasHtml = `
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fas fa-user-times text-danger" style="font-size: 3rem;"></i>
                        <h6 class="mt-3 text-danger">Candidato descartado antes de completar estados del proceso</h6>
                        <p class="text-muted">No se registraron archivos en ningún estado del proceso</p>
                    </div>
                </div>
            `;
        }
    } else {
        carpetas.forEach(carpeta => {
            let colorCard, iconoCarpeta, estadoCarpeta, accionesHtml;
            
            if (carpeta.ya_tiene_archivos) {
                colorCard = 'success';
                iconoCarpeta = 'fas fa-folder-open';
                estadoCarpeta = 'Completado';
                
                // ✅ SI ESTÁ CONTRATADO, SOLO BOTÓN VER
                if (esContratado) {
                    accionesHtml = `
                        <button class="btn btn-info btn-sm" 
                                onclick="verArchivosCarpetaGerente('${idCandidato}', '${carpeta.nombre_estado}')">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    `;
                } else {
                    accionesHtml = `
                        <button class="btn btn-outline-primary btn-sm" 
                                onclick="verArchivosCarpetaGerente('${idCandidato}', '${carpeta.nombre_estado}')">
                            <i class="fas fa-eye"></i> Ver Archivos
                        </button>
                    `;
                }
            } else if (carpeta.puede_subir && !esContratado) {
                colorCard = 'primary';
                iconoCarpeta = 'fas fa-folder-plus';
                estadoCarpeta = 'Disponible';
                accionesHtml = `
                    <button class="btn btn-success btn-sm" 
                            onclick="subirArchivoGerente('${idCandidato}', '${carpeta.nombre_estado}')">
                        <i class="fas fa-upload"></i> Subir archivo
                    </button>
                `;
            } else {
                colorCard = 'secondary';
                iconoCarpeta = 'fas fa-folder';
                estadoCarpeta = esContratado ? 'Solo lectura' : 'Sin archivos';
                accionesHtml = `
                    <small class="text-muted">${carpeta.motivo_bloqueo || 'Sin archivos'}</small>
                `;
            }
            
            carpetasHtml += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-${colorCard} h-100">
                        <div class="card-body text-center d-flex flex-column">
                            <i class="${iconoCarpeta} fa-2x text-${colorCard} mb-2"></i>
                            <h6 class="card-title">${carpeta.nombre_estado}</h6>
                            <span class="badge badge-${colorCard}">${estadoCarpeta}</span>
                            <div class="mt-auto">
                                ${accionesHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    // CONSTRUIR HTML COMPLETO DEL EXPEDIENTE
    const expedienteHtml = `
        <div class="container-fluid">
            <div class="card ${esDescartado ? 'border-danger' : esContratado ? 'border-success' : ''}">
                <div class="card-header ${esDescartado ? 'bg-danger text-white' : esContratado ? 'bg-success text-white' : 'bg-info text-white'}">
                    <h5 class="mb-0">
                        <i class="fas ${esDescartado ? 'fa-user-times' : esContratado ? 'fa-user-check' : 'fa-user'} mr-2"></i>
                        ${nombreCandidato}
                        <span class="badge ${esDescartado ? 'badge-light text-dark' : 'badge-light'} ml-2">${estadoActual}</span>
                        <span class="badge badge-warning ml-2">Gerente</span>
                        ${esDescartado ? '<span class="badge badge-light ml-2">DESCARTADO</span>' : ''}
                        ${esContratado ? '<span class="badge badge-light ml-2">CONTRATADO</span>' : ''}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- INFORMACIÓN PRINCIPAL -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card ${esDescartado ? 'border-danger' : esContratado ? 'border-success' : 'border-primary'}">
                                <div class="card-header ${esDescartado ? 'bg-danger text-white' : esContratado ? 'bg-success text-white' : 'bg-primary text-white'} py-2">
                                    <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Información del Candidato</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nombre:</strong> ${nombreCandidato}</p>
                                    <p><strong>Documento:</strong> ${datosCompletos.candidato?.DOCUMENTO_CANDIDATO || 'No registrado'}</p>
                                    <p><strong>Estado:</strong> 
                                        <span class="badge badge-${esDescartado ? 'danger' : esContratado ? 'success' : 'primary'}">${estadoActual}</span>
                                    </p>
                                    ${esDescartado ? `
                                        <div class="alert alert-danger mt-3">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-exclamation-triangle"></i> Motivo del Descarte
                                            </h6>
                                            <hr>
                                            <p class="mb-0 font-weight-bold">
                                                ${motivoDescarte ? motivoDescarte : 'No se especificó motivo'}
                                            </p>
                                            <div class="mt-3 p-2 bg-light rounded">
                                                <small class="d-block text-dark">
                                                    <i class="fas ${iconoPersona} text-${colorPersona}"></i> 
                                                    <strong>Descartado por ${etiquetaPersona}:</strong> 
                                                    <span class="text-${colorPersona} font-weight-bold">${nombreQuienDescarto}</span>
                                                    ${fechaDescarte ? `<br><i class="fas fa-calendar text-muted"></i> <small class="text-muted">Fecha: ${fechaDescarte}</small>` : ''}
                                                </small>
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="mb-0"><i class="fas fa-building mr-2"></i>Información de la Solicitud</h6>
                                </div>
                                <div class="card-body">
                                  <p><strong>Tienda:</strong> ${tiendaInfo}</p>
                                  <p><strong>Puesto:</strong> ${puestoSolicitado}</p>
                                  <p><strong>Supervisor:</strong> ${supervisorInfo}</p>
                                  <p><strong>Fecha registro:</strong> ${fechaRegistro}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ESTADOS DEL PROCESO -->
                    <div class="card ${esDescartado ? 'border-warning' : 'border-secondary'}">
                        <div class="card-header ${esDescartado ? 'bg-warning text-dark' : 'bg-secondary text-white'}">
                            <h6 class="mb-0">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                ${esDescartado ? 'Estados Completados antes del Descarte' : esContratado ? 'Estados Completados' : 'Estados del Proceso'}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                ${carpetasHtml}
                            </div>
                        </div>
                    </div>
                    
                    <!-- BOTONES DE ACCIÓN O BADGE CONTRATADO -->
                    ${esContratado ? `
                        <div class="mt-4 pt-3 border-top">
                            <div class="alert alert-success text-center mb-0">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h5 class="mb-0"><strong>CANDIDATO CONTRATADO</strong></h5>
                                <p class="mb-0 mt-2">La plaza ha sido cubierta exitosamente</p>
                            </div>
                        </div>
                    ` : !esDescartado ? `
                        <div class="mt-4 pt-3 border-top text-center">
                            <button class="btn btn-danger btn-lg mr-3" onclick="descartarCandidatoGerente(${idCandidato}, '${nombreCandidato}')">
                                <i class="fas fa-user-times mr-2"></i>Descartar Candidato
                            </button>
                            ${(estadoActual === 'APROBACION DE AVAL' || estadoActual === 'Aprobacion de Aval') ? `
                                <button class="btn btn-warning btn-lg" onclick="mostrarModalProcesarAvalIndividual('${idCandidato}', '${nombreCandidato}')">
                                    <i class="fas fa-gavel mr-2"></i>Procesar Aval
                                </button>
                            ` : ''}
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    $('#expedienteCandidatoGerente').html(expedienteHtml);
};

// =============================================================================
// AGREGAR FUNCIÓN DE DEBUG PARA VERIFICAR LOS DATOS
// =============================================================================

window.debugCandidatos = function() {
    console.log('=== DEBUG CANDIDATOS ===');
    console.log('CANDIDATOS_INDEX:', window.CANDIDATOS_INDEX);
    
    if (window.CANDIDATOS_INDEX) {
        const candidatosArray = Object.values(window.CANDIDATOS_INDEX);
        console.log('Total candidatos en índice:', candidatosArray.length);
        
        candidatosArray.forEach((c, index) => {
            console.log(`Candidato ${index + 1}:`, {
                id: c.ID_CANDIDATO,
                nombre: c.NOMBRE_CANDIDATO,
                activo: c.ACTIVO,
                estado: c.ESTADO_CANDIDATO,
                motivo_descarte: c.MOTIVO_DESCARTE,
                es_activo: c.ACTIVO === 'Y' || c.ACTIVO === undefined || c.ACTIVO === null,
                es_descartado: c.ACTIVO === 'N' || 
                             c.ESTADO_CANDIDATO === 'Descartado' || 
                             c.ESTADO_CANDIDATO === 'DESCARTADO' ||
                             (c.ESTADO_CANDIDATO && c.ESTADO_CANDIDATO.toLowerCase().includes('descartado'))
            });
        });
    }
};

// FUNCIÓN PARA MOSTRAR EXPEDIENTE EN MODO SOLO LECTURA - GERENTES
function mostrarExpedienteSoloLecturaGerente(idCandidato, nombreCandidato, datosPermisos, idSolicitud) {
    const carpetas = datosPermisos.carpetas || [];
    const estadoActual = datosPermisos.estado_candidato || 'No definido';
    const puestoSolicitado = datosPermisos.puesto_solicitado || 'No definido';
    
    // Obtener información de la solicitud desde la tabla (IGUAL QUE EN SUPERVISOR)
    const filaSolicitud = $(`tr[data-id]`).first();
    const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(1)').text().trim() : datosPermisos.num_tienda || 'No disponible';
    const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(3)').text().trim() : datosPermisos.supervisor || 'No disponible';
    
    let carpetasHtml = '';
    
    carpetas.forEach(carpeta => {
        // 🎨 Determinar el color y estado según los nuevos permisos
        let colorCard, iconoCarpeta, estadoCarpeta, accionesHtml;
        
        if (carpeta.ya_tiene_archivos) {
            // Ya tiene archivos - verde
            colorCard = 'success';
            iconoCarpeta = 'fas fa-folder-open';
            estadoCarpeta = 'Completado';
            accionesHtml = `
                <button class="btn btn-outline-primary btn-sm mt-2" 
                        onclick="verArchivosCarpetaGerente('${idCandidato}', '${carpeta.nombre_estado}')">
                    <i class="fas fa-eye"></i> Ver archivos
                </button>
            `;
        } else if (carpeta.puede_subir) {
            // Puede subir archivos - azul/primary
            colorCard = 'primary';
            iconoCarpeta = 'fas fa-folder-plus';
            estadoCarpeta = 'Disponible';
            accionesHtml = `
                <div class="mt-2">
                    <button class="btn btn-success btn-sm" 
                            onclick="subirArchivoGerente('${idCandidato}', '${carpeta.nombre_estado}')">
                        <i class="fas fa-upload"></i> Subir archivo
                    </button>
                </div>
            `;
        } else {
            // No puede subir - gris
            colorCard = 'secondary';
            iconoCarpeta = 'fas fa-folder';
            estadoCarpeta = 'Sin archivos';
            accionesHtml = `<small class="text-muted mt-2">${carpeta.motivo_bloqueo || 'Sin archivos'}</small>`;
        }
        
        carpetasHtml += `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-${colorCard} h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <i class="${iconoCarpeta} fa-2x text-${colorCard} mb-2"></i>
                        <h6 class="card-title">${carpeta.nombre_estado}</h6>
                        <span class="badge badge-${colorCard}">${estadoCarpeta}</span>
                        <div class="mt-auto">
                            ${accionesHtml}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    const expedienteHtml = `
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user mr-2"></i>${nombreCandidato}
                        <span class="badge badge-light text-dark ml-2">${estadoActual}</span>
                        <span class="badge badge-warning ml-2">Solo Lectura</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Información Personal</h6>
                            <p><strong>Nombre:</strong> ${nombreCandidato}</p>
                            <p><strong>Estado:</strong> <span class="badge badge-primary">${estadoActual}</span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Información de la Solicitud</h6>
                            <p><strong>Tienda:</strong> ${tiendaInfo}</p>
                            <p><strong>Puesto:</strong> ${puestoSolicitado}</p>
                            <p><strong>Supervisor:</strong> ${supervisorInfo}</p>
                        </div>
                    </div>
                    
                    <h6>Documentos por Estado</h6>
                    <div class="row">
                        ${carpetasHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#expedienteCandidatoGerente').html(expedienteHtml);
}
 //SUBIR ARCHIVO GERENTES 
window.subirArchivoGerente = function(idCandidato, nombreEstado) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
    
    input.onchange = function(e) {
        const archivo = e.target.files[0];
        if (!archivo) return;
        
          Swal.fire({
              title: 'Confirmar subida',
              html: `
                  <p><strong>Archivo:</strong> ${archivo.name}</p>
                  <p><strong>Estado:</strong> ${nombreEstado}</p>
                  <p><strong>Candidato:</strong> ${idCandidato}</p>
              `,
              icon: 'question',
              showCancelButton: true,
              confirmButtonText: '<i class="fas fa-upload"></i> Subir',
              cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
              confirmButtonColor: '#28a745'
          }).then((result) => {
            if (result.isConfirmed) {
                procesarSubidaGerente(idCandidato, nombreEstado, archivo);
            }
        });
    };
    
    input.click();
}

function procesarSubidaGerente(idCandidato, nombreEstado, archivo) {
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php',
        type: 'GET',
        data: { action: 'get_solicitud_by_candidato', id_candidato: idCandidato },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                realizarSubidaGerente(idCandidato, response.id_solicitud, nombreEstado, archivo);
            } else {
                Swal.fire('Error', 'No se pudo obtener información de la solicitud', 'error');
            }
        }
    });
}

function realizarSubidaGerente(idCandidato, idSolicitud, nombreEstado, archivo) {
    const formData = new FormData();
    formData.append('action', 'subir_archivo_candidato_gerente');
    formData.append('id_candidato', idCandidato);
    formData.append('id_solicitud', idSolicitud);
    formData.append('estado_relacionado', nombreEstado);
    formData.append('archivo', archivo);
    
    Swal.fire({ title: 'Subiendo archivo...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
success: function(response) {
    if (response.success) {
        Swal.fire({
            icon: 'success',
            title: '¡Archivo subido exitosamente!',
            text: 'El archivo se guardó correctamente.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            // Recargar el expediente para mostrar el nuevo archivo
            window.verExpedienteCandidatoGerente(idCandidato, 'Candidato');
        });
    } else {
        Swal.fire('Error', response.error, 'error');
    }
}
    });
}

// FUNCIÓN PARA VER ARCHIVOS DE UNA CARPETA - GERENTES
window.verArchivosCarpetaGerente = function (idCandidato, nombreEstado) {
    console.log('Viendo archivos de:', nombreEstado, 'para candidato:', idCandidato);
    
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_archivos_candidato',
        type: 'GET',
        data: {
            id_candidato: idCandidato,
            estado_relacionado: nombreEstado
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.archivos && response.archivos.length > 0) {
                mostrarArchivosModalGerente(response.archivos, nombreEstado);
            } else {
                Swal.fire('Info', 'No hay archivos en esta carpeta', 'info');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error cargando archivos:', error);
            Swal.fire('Error', 'Error al cargar archivos', 'error');
        }
    });
}

// FUNCIÓN PARA MOSTRAR ARCHIVOS EN MODAL - GERENTES
function mostrarArchivosModalGerente(archivos, nombreEstado) {
    let archivosHtml = '';
    
    archivos.forEach(archivo => {
        const fechaSubida = new Date(archivo.FECHA_SUBIDA).toLocaleDateString('es-ES');
        const subidoPor = archivo.SUBIDO_POR_ROL || 'Sistema';
        archivosHtml += `
            <div class="card mb-2">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${archivo.NOMBRE_ARCHIVO}</h6>
                            <small class="text-muted">Subido: ${fechaSubida} por ${subidoPor}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-info mr-2" 
                                    onclick="verArchivoGerente('${archivo.ID_ARCHIVO}', '${archivo.NOMBRE_ARCHIVO}')">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            <button class="btn btn-sm btn-primary" 
                                    onclick="descargarArchivoGerente('${archivo.ID_ARCHIVO}', '${archivo.NOMBRE_ARCHIVO}')">
                                <i class="fas fa-download"></i> Descargar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    Swal.fire({
        title: `Archivos - ${nombreEstado}`,
        html: `
            <div style="max-height: 400px; overflow-y: auto;">
                ${archivosHtml}
            </div>
        `,
        width: '700px',
        showCloseButton: true,
        showConfirmButton: false
    });
}

// FUNCIÓN PARA VER ARCHIVO - GERENTES
function verArchivoGerente(idArchivo, nombreArchivo) {
    // Abrir en nueva pestaña del mismo navegador
    const url = `./GerenteTDS/crudaprobaciones.php?action=ver_archivo&archivo=${nombreArchivo}`;
    
    const nuevaPestana = window.open(url, '_blank');
    
    if (!nuevaPestana) {
        // Si el navegador bloquea popups, mostrar enlace directo
        Swal.fire({
            title: 'Ver Archivo',
            html: `
                <p>Para ver el archivo, haz clic en el enlace:</p>
                <a href="${url}" target="_blank" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Abrir ${nombreArchivo}
                </a>
            `,
            showCloseButton: true,
            showConfirmButton: false
        });
    }
}

// FUNCIÓN PARA DESCARGAR ARCHIVO - GERENTES
function descargarArchivoGerente(idArchivo, nombreArchivo) {
    console.log('Descargando archivo:', nombreArchivo);
    
    // Crear enlace temporal para descarga usando el nombre del archivo
    const enlaceDescarga = document.createElement('a');
    enlaceDescarga.href = `./GerenteTDS/crudaprobaciones.php?action=descargar_archivo&archivo=${nombreArchivo}`;
    enlaceDescarga.download = nombreArchivo;
    enlaceDescarga.style.display = 'none';
    
    document.body.appendChild(enlaceDescarga);
    enlaceDescarga.click();
    document.body.removeChild(enlaceDescarga);
    
    // Mostrar mensaje de descarga
    Swal.fire({
        title: 'Descargando...',
        text: `Archivo: ${nombreArchivo}`,
        icon: 'info',
        timer: 2000,
        showConfirmButton: false
    });
}

//==============================================================================
// FUNCIONES DE DESCARTAR CANDIDATO SUPERVISION 
//==============================================================================

// FUNCIÓN PARA DESCARTAR CANDIDATO EN SUPERVISIÓN
window.descartarCandidatoGerente = function(idCandidato, nombreCandidato) {
  console.log('Descartando candidato Gerente:', idCandidato, nombreCandidato);
  
  // Mostrar loading mientras carga la información
  Swal.fire({
    title: 'Cargando información...',
    text: 'Obteniendo datos del candidato',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });
  
  // Obtener información completa del candidato
  $.ajax({
    url: './GerenteTDS/crudaprobaciones.php?action=get_permisos_subida_candidato_gerente',
    type: 'GET',
    data: {
      id_candidato: idCandidato,
      rol_usuario: 'GERENTE'
    },
    dataType: 'json',
    success: function(response) {
      Swal.close();
      
      if (response.success) {
        mostrarModalDescarteCompletoGerente(idCandidato, nombreCandidato, response);
      } else {
        Swal.fire('Error', 'No se pudo cargar la información del candidato', 'error');
      }
    },
    error: function() {
      Swal.close();
      Swal.fire('Error', 'Error de conexión al cargar información', 'error');
    }
  });
}

// Nueva función para mostrar el modal completo
window.mostrarModalDescarteCompletoGerente = function(idCandidato, nombreCandidato, datosCompletos) {
    const carpetas = datosCompletos.carpetas || [];
    const estadoActual = datosCompletos.estado_candidato || 'No definido';
    const puestoSolicitado = datosCompletos.puesto_solicitado || 'No definido';
    
    // ✅ OBTENER INFORMACIÓN DE LA SOLICITUD ESPECÍFICA DEL CANDIDATO (igual que arriba)
    let tiendaInfo = 'No disponible';
    let supervisorInfo = 'No disponible';
    
    // Desde los datos completos del backend
    if (datosCompletos.num_tienda) {
        tiendaInfo = datosCompletos.num_tienda;
    }
    if (datosCompletos.supervisor) {
        supervisorInfo = datosCompletos.supervisor;
    }
    
    // Desde el candidato en el índice global
    const candidato = window.CANDIDATOS_INDEX[idCandidato];
    if (candidato) {
        if (candidato.NUM_TIENDA && tiendaInfo === 'No disponible') {
            tiendaInfo = candidato.NUM_TIENDA;
        }
        if ((candidato.SUPERVISOR || candidato.SOLICITADO_POR) && supervisorInfo === 'No disponible') {
            supervisorInfo = candidato.SUPERVISOR || candidato.SOLICITADO_POR;
        }
        
        // Buscar en la tabla por ID_SOLICITUD específico
        if (candidato.ID_SOLICITUD) {
            const filaSolicitudEspecifica = $(`tr[data-id="${candidato.ID_SOLICITUD}"]`);
            if (filaSolicitudEspecifica.length > 0) {
                tiendaInfo = filaSolicitudEspecifica.find('td:nth-child(1)').text().trim() || tiendaInfo;
                supervisorInfo = filaSolicitudEspecifica.find('td:nth-child(3)').text().trim() || supervisorInfo;
            }
        }
    }
    
    // Como último recurso, usar la primera fila
    if (tiendaInfo === 'No disponible' || supervisorInfo === 'No disponible') {
        const filaSolicitud = $(`tr[data-id]`).first();
        if (filaSolicitud.length > 0) {
            tiendaInfo = filaSolicitud.find('td:nth-child(1)').text().trim() || tiendaInfo;
            supervisorInfo = filaSolicitud.find('td:nth-child(3)').text().trim() || supervisorInfo;
        }
    }
    
    console.log('📋 Información para descarte:', {
        tienda: tiendaInfo,
        supervisor: supervisorInfo,
        candidato: nombreCandidato,
        id_solicitud: candidato?.ID_SOLICITUD
    });
    
  
  // OBTENER EL NOMBRE REAL DEL CANDIDATO DESDE LA LISTA
  const candidatoCard = $(`.candidate-card[data-candidato-id="${idCandidato}"]`);
  let nombreReal = nombreCandidato;
  
  if (candidatoCard.length > 0) {
    const nombreEnCard = candidatoCard.find('h6').text().trim();
    if (nombreEnCard && nombreEnCard !== '') {
      nombreReal = nombreEnCard;
    }
  }
  
  // Si aún es undefined, usar un nombre por defecto
  if (!nombreReal || nombreReal === 'undefined') {
    nombreReal = 'Candidato ID: ' + idCandidato;
  }
  
  console.log('Nombre real del candidato:', nombreReal);
  
  // Estados alcanzados (solo los que tienen archivos o el estado actual)
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
  
const modalHtml = `
    <div class="modal fade" id="modalDescartarGerente${idCandidato}" tabindex="-1" data-backdrop="static">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">
              <i class="fas fa-user-times mr-2"></i>Descartar Candidato - Vista Gerente
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
              <strong>¡ATENCIÓN!</strong> Esta acción descartará definitivamente al candidato. 
              No podrá ser revertida.
            </div>
            
            <!-- Campo de motivo -->
            <div class="form-group">
              <label for="motivoDescarteGerente${idCandidato}" class="font-weight-bold text-danger">
                Motivo del descarte <span class="text-danger">*</span>:
              </label>
                <textarea 
                    id="motivoDescarteGerente${idCandidato}" 
                    class="form-control" 
                    rows="4" 
                    placeholder="Ingrese el motivo por el cual está descartando este candidato..."
                    maxlength="500"
                    oninput="updateCharCountGerente${idCandidato}()"
                ></textarea>
                <div class="d-flex justify-content-between">
                    <small class="form-text text-muted">
                        Máximo 500 caracteres. Este campo es obligatorio.
                    </small>
                    <small class="text-muted">
                        <span id="charCountGerente${idCandidato}">0</span>/500 caracteres
                    </small>
                </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-danger" id="btnConfirmarDescarteGerente${idCandidato}">
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
  $(`#modalDescartarGerente${idCandidato}`).modal('show');

  // Función para contar caracteres
    window[`updateCharCountGerente${idCandidato}`] = function() {
        const count = $(`#motivoDescarteGerente${idCandidato}`).val().length;
        $(`#charCountGerente${idCandidato}`).text(count);
        
        // Cambiar color si se acerca al límite - CORREGIR LA LÓGICA:
        if (count > 480) {
            $(`#charCountGerente${idCandidato}`).parent().removeClass('text-muted text-warning').addClass('text-danger');
        } else if (count > 450) {
            $(`#charCountGerente${idCandidato}`).parent().removeClass('text-muted text-danger').addClass('text-warning');
        } else {
            $(`#charCountGerente${idCandidato}`).parent().removeClass('text-warning text-danger').addClass('text-muted');
        }
    };
    $(`#motivoDescarteGerente${idCandidato}`).on('input', window[`updateCharCountGerente${idCandidato}`]);
  
  // Configurar eventos
  $(`#btnConfirmarDescarteGerente${idCandidato}`).on('click', function() {
    const motivo = $(`#motivoDescarteGerente${idCandidato}`).val().trim();
    
    if (motivo.length < 10) {
      Swal.fire('Error', 'El motivo debe tener al menos 10 caracteres', 'warning');
      $(`#motivoDescarteGerente${idCandidato}`).focus();
      return;
    }
    
    confirmarDescarteGerente(idCandidato, motivo);
  });
  
  // Auto-focus y limpieza
  $(`#modalDescartarGerente${idCandidato}`).on('shown.bs.modal', function() {
    setTimeout(() => {
      $(`#motivoDescarteGerente${idCandidato}`).focus();
    }, 300);
  });
  
  $(`#modalDescartarGerente${idCandidato}`).on('hidden.bs.modal', function() {
    $(this).remove();
  });
}

// FUNCIÓN PARA CARGAR ESTADOS ALCANZADOS
window.cargarEstadosAlcanzadosGerente = function(idCandidato) {
  $.ajax({
    url: './GerenteTDS/crudaprobaciones.php?action=get_permisos_subida_candidato_gerente',
    type: 'GET',
    data: {
      id_candidato: idCandidato,
      rol_usuario: 'GERENTE'
    },
    dataType: 'json',
    success: function(response) {
      if (response.success && response.carpetas) {
        mostrarEstadosAlcanzadosGerente(idCandidato, response.carpetas);
      } else {
        $(`#estadosAlcanzadosGerente${idCandidato}`).html(`
          <div class="text-muted">No se pudieron cargar los estados</div>
        `);
      }
    },
    error: function() {
      $(`#estadosAlcanzadosGerente${idCandidato}`).html(`
        <div class="text-danger">Error cargando estados</div>
      `);
    }
  });
}

// FUNCIÓN PARA MOSTRAR ESTADOS ALCANZADOS
window.mostrarEstadosAlcanzadosGerente = function (idCandidato, carpetas) {
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
  
  $(`#estadosAlcanzadosGerente${idCandidato}`).html(estadosHtml);
}

// FUNCIÓN PARA CONFIRMAR DESCARTE
window.confirmarDescarteGerente = function (idCandidato, motivo) {
  // Cerrar modal
  $(`#modalDescartarGerente${idCandidato}`).modal('hide');
  
  // Mostrar loading
  Swal.fire({
    title: 'Descartando candidato...',
    text: 'Procesando solicitud de supervisión...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });
  
  // Procesar descarte
  $.ajax({
    url: './GerenteTDS/crudaprobaciones.php?action=descartar_candidato_gerente',
    type: 'POST',
    dataType: 'json',
    data: {
      id_candidato: idCandidato,
      motivo_descarte: motivo
    },
    success: function(response) {
      if (response.success) {
        Swal.fire({
          icon: 'success',
          title: 'Candidato descartado',
          text: 'El candidato fue descartado correctamente por Gerente',
          timer: 3000,
          showConfirmButton: false
        }).then(() => {
          // Recargar la lista de candidatos
          location.reload();
        });
      } else {
        Swal.fire('Error', response.error || 'Error al descartar candidato', 'error');
      }
    },
    error: function(xhr, status, error) {
      Swal.fire('Error', 'Error de conexión: ' + error, 'error');
    }
  });
}

// ===================================================================================
// FUNCIÓN PARA MOSTRAR CANDIDATOS PARA PROCESAR AVAL
// ===================================================================================
window.mostrarCandidatosAvalGerente = function(idSolicitud, numeroTienda, puestoSolicitado, supervisor, razon) {
    console.log('🎯 Mostrando candidatos para aval gerente:', idSolicitud);
    
    Swal.fire({
        title: 'Cargando candidatos...',
        text: 'Obteniendo candidatos pendientes de aval',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_candidatos_aval_gerente',
        type: 'GET',
        data: { id_solicitud: idSolicitud },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success && response.candidatos && response.candidatos.length > 0) {
                mostrarModalCandidatosAval(response.candidatos, idSolicitud, numeroTienda, puestoSolicitado, supervisor, razon);
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin candidatos para aval',
                    text: 'No hay candidatos en estado "APROBACION DE AVAL" para esta solicitud',
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error cargando candidatos aval:', error);
            Swal.fire('Error', 'Error al cargar candidatos: ' + error, 'error');
        }
    });
}

// ===================================================================================
// FUNCIÓN PARA MOSTRAR MODAL CON CANDIDATOS PARA PROCESAR AVAL - ACTUALIZADA
// ===================================================================================
function mostrarModalCandidatosAval(candidatos, idSolicitud, numeroTienda, puestoSolicitado, supervisor, razon) {
    let candidatosHtml = '';
    
    candidatos.forEach(candidato => {
        const nombreCompleto = `${candidato.NOMBRE_CANDIDATO} ${candidato.APELLIDOS_CANDIDATO}`;
        
        // ✅ NUEVA LÓGICA PARA CANDIDATOS REPROCESABLES
        let estadoClass, estadoBadge, fechaDecision, botonProcesar;
        
        if (candidato.tiene_decisiones_previas) {
            // Candidato que ya tuvo decisiones pero volvió al estado "Aprobacion de Aval"
            estadoClass = 'info';
            estadoBadge = '<span class="badge badge-info">REPROCESABLE</span>';
            fechaDecision = candidato.FECHA_DECISION ? 
                `<small class="text-muted">Última decisión: ${new Date(candidato.FECHA_DECISION).toLocaleDateString('es-ES')}</small>` : '';
            botonProcesar = `<button class="btn btn-sm btn-warning" 
                                    onclick="mostrarModalProcesarAval('${candidato.ID_CANDIDATO}', '${nombreCompleto}')">
                                <i class="fas fa-redo"></i> Reprocesar Aval
                            </button>`;
        } else if (candidato.ya_procesado || candidato.ESTADO_CANDIDATO === 'Aprobacion de Aval Enviado') {
            // Candidato con decisión final (no reprocesable)
            estadoClass = candidato.APROBACION === 'Y' ? 'success' : 'danger';
            estadoBadge = `<span class="badge badge-${estadoClass}">${candidato.decision_texto}</span>`;
            fechaDecision = candidato.FECHA_DECISION ? 
                `<small class="text-muted">Procesado: ${new Date(candidato.FECHA_DECISION).toLocaleDateString('es-ES')}</small>` : '';
            botonProcesar = '<button class="btn btn-sm btn-secondary" disabled>Ya Procesado</button>';
        } else {
            // Candidato nuevo sin decisiones previas
            estadoClass = 'warning';
            estadoBadge = '<span class="badge badge-warning">PENDIENTE</span>';
            fechaDecision = '';
            botonProcesar = `<button class="btn btn-sm btn-primary" 
                                    onclick="mostrarModalProcesarAval('${candidato.ID_CANDIDATO}', '${nombreCompleto}')">
                                <i class="fas fa-gavel"></i> Procesar Aval
                            </button>`;
        }
        
        candidatosHtml += `
        <div class="candidate-card mb-3" 
            data-candidato-id="${candidato.ID_CANDIDATO}"
            data-ya-procesado="${(candidato.ya_procesado && !candidato.tiene_decisiones_previas) ? 'true' : 'false'}">
                <div class="card shadow-sm border-left-${estadoClass} ${candidato.ya_procesado && !candidato.tiene_decisiones_previas ? 'cursor-pointer' : ''}">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <i class="fas fa-user mr-1"></i>${nombreCompleto}
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-id-card mr-1"></i>DPI: ${candidato.DOCUMENTO_CANDIDATO || 'No registrado'}
                            </small>
                            <div class="mt-2">
                                ${estadoBadge}
                                ${fechaDecision ? `<br>${fechaDecision}` : ''}
                            </div>
                            ${candidato.tiene_decisiones_previas ? 
                                '<div class="mt-2"><small class="text-info"><i class="fas fa-info-circle"></i> Este candidato volvió a estado de aval</small></div>' : ''}
                            ${candidato.MOTIVO_DECISION ? 
                                `<div class="mt-2">
                                    <small class="text-muted"><strong>Último motivo:</strong> ${candidato.MOTIVO_DECISION.substring(0, 100)}${candidato.MOTIVO_DECISION.length > 100 ? '...' : ''}</small>
                                </div>` : ''}
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="btn-group-vertical">
                                <button class="btn btn-sm btn-outline-info mb-1" 
                                        onclick="verExpedienteCandidatoGerente('${candidato.ID_CANDIDATO}', 'Candidato')">
                                    <i class="fas fa-folder-open"></i> Ver Expediente
                                </button>
                                ${botonProcesar}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    // ✅ INFORMACIÓN MEJORADA DE LA SOLICITUD
    const candidatosReprocesables = candidatos.filter(c => c.tiene_decisiones_previas).length;
    const candidatosPendientes = candidatos.filter(c => !c.ya_procesado && !c.tiene_decisiones_previas).length;
    const candidatosProcesados = candidatos.filter(c => c.ya_procesado && !c.tiene_decisiones_previas).length;
    
    const infoSolicitudHtml = `
        <div class="alert alert-info mb-4">
            <h6 class="mb-2"><i class="fas fa-info-circle mr-1"></i>Información de la Solicitud</h6>
            <div class="row">
                <div class="col-md-6">
                    <small><strong>Tienda:</strong> ${numeroTienda}</small><br>
                    <small><strong>Puesto:</strong> ${puestoSolicitado}</small><br>
                    <small><strong>Supervisor:</strong> ${supervisor}</small>
                </div>
                <div class="col-md-6">
                    <small><strong>Total candidatos:</strong> ${candidatos.length}</small><br>
                    ${candidatosPendientes > 0 ? `<small><strong>Pendientes:</strong> ${candidatosPendientes}</small><br>` : ''}
                    ${candidatosReprocesables > 0 ? `<small><strong>Reprocesables:</strong> ${candidatosReprocesables}</small><br>` : ''}
                    ${candidatosProcesados > 0 ? `<small><strong>Finalizados:</strong> ${candidatosProcesados}</small>` : ''}
                </div>
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'Candidatos para Procesamiento de Aval',
        html: `
            <div style="text-align: left; max-height: 600px; overflow-y: auto;">
                ${infoSolicitudHtml}
                ${candidatosHtml}
            </div>
        `,
        width: '80%',
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            container: 'swal2-container-aval'
        }
    });

    // Event listener para candidatos procesados vs pendientes (MANTENER EL ORIGINAL)
    $(document).off('click', '.candidate-card[data-ya-procesado="true"] .card').on('click', '.candidate-card[data-ya-procesado="true"] .card', function(e) {
        e.preventDefault();
        console.log('🔍 CLICK en candidato procesado detectado');
        
        const candidatoId = $(this).closest('.candidate-card').data('candidato-id');
        const yaProcesado = $(this).closest('.candidate-card').data('ya-procesado');
        
        console.log('📊 Datos del candidato:', {
            candidatoId: candidatoId,
            yaProcesado: yaProcesado,
            candidatoCompleto: window.CANDIDATOS_INDEX[candidatoId]
        });
        
        const candidato = window.CANDIDATOS_INDEX[candidatoId];
        
        if (!candidato) {
            console.error('❌ Candidato no encontrado en índice');
            return;
        }
        
        // Cerrar modal actual
        $('#modalSolicitudDetalle').modal('hide');
        
        // Mostrar resultado del aval
        mostrarResultadoAvalProcesado(candidato);
    });
}

//===================================================================================
// FUNCIÓN PARA MOSTRAR RESULTADO DE AVAL YA PROCESADO (SOLO LECTURA)
//===================================================================================
//===================================================================================
// FUNCIÓN PARA MOSTRAR RESULTADO DE AVAL YA PROCESADO (SOLO LECTURA)
//===================================================================================
function mostrarResultadoAvalProcesado(candidato) {
    console.log('🎯 Mostrando resultado aval procesado:', candidato);
    
    // Función auxiliar para obtener información completa
    function obtenerInformacionAvalCompleta(idCandidato) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: './GerenteTDS/crudaprobaciones.php?action=get_info_aval_completa',
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

    // Llamar a la función auxiliar
    obtenerInformacionAvalCompleta(candidato.ID_CANDIDATO)
        .then(candidatoCompleto => {
            // Combinar datos
            const candidatoInfo = { ...candidato, ...candidatoCompleto };
            
            console.log('🔍 DEBUG - Datos completos:', candidatoInfo);
            console.log('🔍 ARCHIVOS:', {
                CV: candidatoInfo.ARCHIVOS_CV,
                Psicometrica: candidatoInfo.ARCHIVOS_PSICOMETRICA,
                EntrevistaRH: candidatoInfo.ARCHIVOS_ENTREVISTA_RH,
                EntrevistaTecnica: candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA,
                DiaPrueba: candidatoInfo.ARCHIVOS_DIA_PRUEBA,
                Poligrafo: candidatoInfo.ARCHIVOS_POLIGRAFO
            });

            // Determinar si fue aprobado o rechazado
            const esAprobado = candidatoInfo.APROBACION === 'Y';
            const decision = esAprobado ? 'APROBADO' : 'RECHAZADO';
            
            // Información del candidato
            const nombreCompleto = `${candidatoInfo.NOMBRE_CANDIDATO} ${candidatoInfo.APELLIDOS_CANDIDATO}`;
            const documento = candidatoInfo.DOCUMENTO_CANDIDATO || 'No registrado';
            const fechaDecision = candidatoInfo.FECHA_DECISION ? 
                new Date(candidatoInfo.FECHA_DECISION).toLocaleDateString('es-ES') : 'No disponible';
            const comentario = candidatoInfo.MOTIVO_DECISION || 'Sin comentarios adicionales'
            
            // Información de la solicitud
            const tiendaInfo = candidatoInfo.NUM_TIENDA || 'No disponible';
            const puestoInfo = candidatoInfo.PUESTO_SOLICITADO || 'No disponible';
            const supervisorInfo = candidatoInfo.SUPERVISOR || 'No disponible';
            const nombregerente = candidatoInfo.NOMBRE_GERENTE || 'Gerente no encontrado';
            
            
            // Función para generar botones de archivos dinámicamente
            const generarBotonArchivos = (estado, cantidad, icono, texto) => {
                const tieneArchivos = cantidad > 0;
                const badgeClass = tieneArchivos ? 'badge-success' : 'badge-secondary';
                const badgeText = tieneArchivos ? `Completado (${cantidad})` : 'Sin archivos';
                const buttonDisabled = !tieneArchivos ? 'disabled' : '';
                
                return `
                    <div class="text-center p-3" style="background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px;">
                        <i class="${icono} fa-2x text-warning mb-2"></i>
                        <h6 class="mb-1">${texto}</h6>
                        <span class="badge ${badgeClass}">${badgeText}</span><br>
                        <button class="btn btn-sm btn-outline-info mt-2" 
                                onclick="verArchivosCarpetaGerente('${candidatoInfo.ID_CANDIDATO}', '${estado}')"
                                ${buttonDisabled}>
                            <i class="fas fa-eye mr-1"></i>Ver Archivos
                        </button>
                    </div>
                `;
            };
            
            // HTML de la vista completa
            const expedienteHTML = `
                <div class="card mb-0">
                    <div class="card-header ${esAprobado ? 'bg-success' : 'bg-danger'} text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user mr-2"></i>Expediente de ${nombreCompleto}
                            <span class="badge badge-light ml-2">${decision}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- INFORMACIÓN PERSONAL Y DE SOLICITUD -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="alert ${esAprobado ? 'alert-success' : 'alert-danger'}">
                                    <h6 class="mb-1">
                                        <i class="fas fa-user mr-1"></i>Información Personal
                                    </h6>
                                    <div><strong>Nombre:</strong> ${nombreCompleto}</div>
                                    <div><strong>DPI:</strong> ${documento}</div>
                                    <div><strong>Estado:</strong> 
                                        <span class="badge ${esAprobado ? 'badge-success' : 'badge-danger'}">${decision}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="mb-1">
                                        <i class="fas fa-info-circle mr-1"></i>Información de la Solicitud
                                    </h6>
                                    <div><strong>Tienda:</strong> ${tiendaInfo}</div>
                                    <div><strong>Puesto:</strong> ${puestoInfo}</div>
                                    <div><strong>Supervisor:</strong> ${supervisorInfo}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- DECISIÓN DEL AVAL Y PROCESADO POR -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="alert ${esAprobado ? 'alert-success' : 'alert-danger'}">
                                    <h6 class="mb-1">
                                        <i class="fas fa-gavel mr-1"></i>${esAprobado ? 'Comentarios del Aval' : 'Motivo del Rechazo'}
                                    </h6>
                                    <div>${comentario}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="mb-1">
                                        <i class="fas fa-user-check mr-1"></i>Procesado por
                                    </h6>
                                    <span class="badge badge-warning mb-2">Gerente</span><br>
                                    <div><strong>${nombregerente}</strong></div>
                                    <div><strong>Fecha:</strong> ${fechaDecision}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ESTADOS COMPLETADOS ANTES DEL AVAL -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-list-check mr-2"></i>Estados Completados antes del Aval
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        ${generarBotonArchivos('CV Enviado', candidatoInfo.ARCHIVOS_CV, 'fas fa-file-alt', 'CV Enviado')}
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        ${generarBotonArchivos('Psicometrica', candidatoInfo.ARCHIVOS_PSICOMETRICA, 'fas fa-brain', 'Psicometrica')}
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        ${generarBotonArchivos('Entrevista Rh', candidatoInfo.ARCHIVOS_ENTREVISTA_RH, 'fas fa-users', 'Entrevista RH')}
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        ${generarBotonArchivos('Entrevista Tecnica', candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA, 'fas fa-clipboard-check', 'Entrevista Tecnica')}
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        ${generarBotonArchivos('Dia de Prueba', candidatoInfo.ARCHIVOS_DIA_PRUEBA, 'fas fa-calendar-check', 'Dia de Prueba')}
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        ${generarBotonArchivos('Poligrafo', candidatoInfo.ARCHIVOS_POLIGRAFO, 'fas fa-shield-alt', 'Poligrafo')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Mostrar en el panel del expediente
            $('#expedienteCandidatoGerente').html(expedienteHTML);
        })
        .catch(error => {
            console.error('❌ Error al mostrar resultado aval:', error);
            Swal.fire('Error', 'No se pudo cargar la información completa del aval: ' + error, 'error');
        });
}

// Event listener para candidatos procesados vs pendientes
$(document).off('click', '.candidate-card[data-ya-procesado="true"] .card').on('click', '.candidate-card[data-ya-procesado="true"] .card', function(e) {
    e.preventDefault();
    console.log('🔍 CLICK en candidato procesado detectado');
    
    const candidatoId = $(this).closest('.candidate-card').data('candidato-id');
    const yaProcesado = $(this).closest('.candidate-card').data('ya-procesado');
    
    console.log('📊 Datos del candidato:', {
        candidatoId: candidatoId,
        yaProcesado: yaProcesado,
        candidatoCompleto: window.CANDIDATOS_INDEX[candidatoId]
    });
    
    const candidato = window.CANDIDATOS_INDEX[candidatoId];
    
    if (!candidato) {
        console.error('❌ Candidato no encontrado en índice');
        
        // Si no está en el índice, obtener desde el backend
        function obtenerInfoBackend(idCandidato) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: './GerenteTDS/crudaprobaciones.php?action=get_info_aval_completa',
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
        
        obtenerInfoBackend(candidatoId)
            .then(candidatoCompleto => {
                $('#modalSolicitudDetalle').modal('hide');
                mostrarResultadoAvalProcesado(candidatoCompleto);
            })
            .catch(error => {
                Swal.fire('Error', 'No se pudo cargar la información del candidato: ' + error, 'error');
            });
        return;
    }
    
    // Cerrar modal actual
    $('#modalSolicitudDetalle').modal('hide');
    
    // Mostrar resultado del aval
    mostrarResultadoAvalProcesado(candidato);
});
// ===================================================================================
// FUNCIÓN PARA MOSTRAR MODAL PROCESAR AVAL INDIVIDUAL
// ===================================================================================
window.mostrarModalProcesarAval = function(idCandidato, nombreCompleto) {
    console.log('🎯 Mostrando modal procesar aval para:', idCandidato, nombreCompleto);
    
    Swal.fire({
        title: 'Procesar Aval de Candidato',
        html: `
            <div style="text-align: left;">
                <div class="alert alert-warning">
                    <h6 class="mb-2">
                        <i class="fas fa-user mr-1"></i>Candidato: <strong>${nombreCompleto}</strong>
                    </h6>
                    <p class="mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        Seleccione la decisión para este candidato y proporcione el motivo correspondiente.
                    </p>
                </div>
                
                <div class="form-group mt-3">
                    <label class="font-weight-bold">Decisión del Aval:</label>
                    <div class="mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="decision_aval" 
                                   id="aprobado" value="APROBADO">
                            <label class="form-check-label text-success" for="aprobado">
                                <i class="fas fa-check-circle mr-1"></i><strong>APROBADO PARA CONTRATACION</strong>
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="radio" name="decision_aval" 
                                   id="rechazado" value="RECHAZADO">
                            <label class="form-check-label text-danger" for="rechazado">
                                <i class="fas fa-times-circle mr-1"></i><strong>RECHAZADO PARA CONTRATACION</strong>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-3">
                    <label for="motivo_decision_aval" class="font-weight-bold">Motivo de la Decisión:</label>
                    <textarea id="motivo_decision_aval" class="form-control" rows="4" 
                              placeholder="Describa el motivo de su decisión..." required></textarea>
                </div>
            </div>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Procesar Decisión',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const decision = document.querySelector('input[name="decision_aval"]:checked');
            const motivo = document.getElementById('motivo_decision_aval').value.trim();
            
            if (!decision) {
                Swal.showValidationMessage('Debe seleccionar una decisión');
                return false;
            }
            
            if (!motivo) {
                Swal.showValidationMessage('Debe proporcionar un motivo para la decisión');
                return false;
            }
            
            if (motivo.length < 10) {
                Swal.showValidationMessage('El motivo debe tener al menos 10 caracteres');
                return false;
            }
            
            return {
                decision: decision.value,
                motivo: motivo
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            procesarDecisionAval(idCandidato, result.value.decision, result.value.motivo);
        }
    });
}

// ===================================================================================
// FUNCIÓN PARA PROCESAR LA DECISIÓN DE AVAL
// ===================================================================================
function procesarDecisionAval(idCandidato, decision, motivoDecision) {
    console.log('🎯 Procesando decisión aval:', idCandidato, decision, motivoDecision);
    
    Swal.fire({
        title: 'Procesando decisión...',
        text: 'Guardando la decisión del aval',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'procesar_aval_gerente',
            id_candidato: idCandidato,
            decision: decision,
            motivo_decision: motivoDecision
        },
        success: function(response) {
            if (response.success) {
                const iconType = decision === 'APROBADO' ? 'success' : 'warning';
                const titleText = decision === 'APROBADO' ? 'Candidato Aprobado' : 'Candidato Rechazado';
                const messageText = `La decisión "${decision} PARA CONTRATACION" ha sido procesada correctamente`;
                
                Swal.fire({
                    icon: iconType,
                    title: titleText,
                    text: messageText,
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    // Recargar la página para actualizar las solicitudes
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.error || 'Error al procesar la decisión', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Error de conexión: ' + error, 'error');
        }
    });
}

//========================FIN FUNCIONES DE DESCARTE CANDIDATOS=============================

//=========================================================================================
//FUNCIONES PARA APROBACION DE AVAL - GERENTES
//=========================================================================================


// NUEVA FUNCIÓN: MOSTRAR MODAL PROCESAR AVAL INDIVIDUAL
window.mostrarModalProcesarAvalIndividual = function(idCandidato, nombreCompleto) {
    console.log('🎯 Mostrando modal procesar aval individual:', idCandidato, nombreCompleto);
    
    // Mostrar loading inmediatamente
    Swal.fire({
        title: 'Cargando información...',
        text: 'Por favor espere mientras se carga la información del candidato',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Obtener información del candidato desde el índice global
    const candidato = window.CANDIDATOS_INDEX[idCandidato];
    if (!candidato) {
        Swal.close();
        Swal.fire('Error', 'No se encontró información del candidato', 'error');
        return;
    }
    
    const estadoActual = candidato.ESTADO_CANDIDATO || 'No definido';
    const documento = candidato.DOCUMENTO_CANDIDATO || 'No registrado';
    
    // ✅ SOLUCIÓN: Obtener datos de solicitud del candidato desde el índice
    const idSolicitud = candidato.ID_SOLICITUD;
    
    // Buscar la fila de la solicitud en la tabla para obtener los datos
    const filaSolicitud = $(`tr[data-id="${idSolicitud}"]`);
    const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(1)').text().trim() : 'No disponible';
    const puestoSolicitado = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(2)').text().trim() : 'No disponible';
    const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(3)').text().trim() : 'No disponible';
    
    console.log('📋 Información de solicitud obtenida:', {
        tienda: tiendaInfo,
        puesto: puestoSolicitado,
        supervisor: supervisorInfo,
        candidato: nombreCompleto
    });
    
    // Ahora cargar información de estados alcanzados
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php?action=get_permisos_subida_candidato_gerente',
        type: 'GET',
        data: {
            id_candidato: idCandidato,
            rol_usuario: 'GERENTE'
        },
        dataType: 'json',
        beforeSend: function() {
            // El loading ya está mostrado desde el inicio
        },
        success: function(response) {
            Swal.close(); // Cerrar loading al recibir respuesta
            
            if (response.success) {
                mostrarModalAvalCompleto(idCandidato, nombreCompleto, response, 
                    tiendaInfo, puestoSolicitado, supervisorInfo, estadoActual, documento);
            } else {
                Swal.fire('Error', 'No se pudo cargar la información del candidato', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.close(); // Cerrar loading en caso de error
            console.error('❌ Error al obtener información:', error);
            Swal.fire('Error', 'Error de conexión al cargar información', 'error');
        }
    });
}

// FUNCIÓN PARA MOSTRAR EL MODAL COMPLETO DE AVAL
function mostrarModalAvalCompleto(idCandidato, nombreCompleto, datosCompletos, tiendaInfo, puestoSolicitado, supervisorInfo, estadoActual, documento) {
    const carpetas = datosCompletos.carpetas || [];
    
    // Estados alcanzados (solo los que tienen archivos o el estado actual)
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
    
    const modalHtml = `
        <div class="modal fade" id="modalProcesarAval${idCandidato}" tabindex="-1" data-backdrop="static">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-gavel mr-2"></i>Procesar Aval de Candidato - Vista Gerente
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
                                        <p><strong>Nombre:</strong> ${nombreCompleto}</p>
                                        <p><strong>Documento:</strong> ${documento}</p>
                                        <p><strong>Estado actual:</strong> 
                                            <span class="badge badge-warning">${estadoActual}</span>
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
                        
                        <!-- Decisión del aval -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>PROCESAMIENTO DE AVAL</strong> - Seleccione la decisión final para este candidato.
                        </div>
                        
                        <!-- Opciones de decisión -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Decisión del Aval:</label>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card border-success h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                            <h5 class="text-success">APROBAR CONTRATACIÓN</h5>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="decision_aval" id="aprobado_aval" value="APROBADO">
                                                <label class="form-check-label font-weight-bold text-success" for="aprobado_aval">
                                                    Candidato APROBADO para contratación
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-danger h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                            <h5 class="text-danger">RECHAZAR CONTRATACIÓN</h5>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="decision_aval" id="rechazado_aval" value="RECHAZADO">
                                                <label class="form-check-label font-weight-bold text-danger" for="rechazado_aval">
                                                    Candidato RECHAZADO para contratación
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campo de motivo -->
                        <div class="form-group">
                            <label for="motivoDecisionAval${idCandidato}" class="font-weight-bold text-dark">
                                Motivo de la Decisión <span class="text-danger">*</span>:
                            </label>
                            <textarea 
                                id="motivoDecisionAval${idCandidato}" 
                                class="form-control" 
                                rows="4" 
                                placeholder="Ingrese el motivo detallado de su decisión..."
                                maxlength="500"
                                oninput="updateCharCountAval${idCandidato}()"
                            ></textarea>
                            <div class="d-flex justify-content-between">
                                <small class="form-text text-muted">
                                    Máximo 500 caracteres. Este campo es obligatorio.
                                </small>
                                <small class="text-muted">
                                    <span id="charCountAval${idCandidato}">0</span>/500 caracteres
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="btnConfirmarAval${idCandidato}">
                            <i class="fas fa-gavel mr-2"></i>Procesar Decisión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Agregar modal al DOM
    $('body').append(modalHtml);
    
    // Mostrar modal
    $(`#modalProcesarAval${idCandidato}`).modal('show');

    // Función para contar caracteres
    window[`updateCharCountAval${idCandidato}`] = function() {
        const count = $(`#motivoDecisionAval${idCandidato}`).val().length;
        $(`#charCountAval${idCandidato}`).text(count);
        
        // Cambiar color si se acerca al límite
        if (count > 480) {
            $(`#charCountAval${idCandidato}`).parent().removeClass('text-muted text-warning').addClass('text-danger');
        } else if (count > 450) {
            $(`#charCountAval${idCandidato}`).parent().removeClass('text-muted text-danger').addClass('text-warning');
        } else {
            $(`#charCountAval${idCandidato}`).parent().removeClass('text-warning text-danger').addClass('text-muted');
        }
    };
    
    $(`#motivoDecisionAval${idCandidato}`).on('input', window[`updateCharCountAval${idCandidato}`]);
  
    // Configurar eventos
    $(`#btnConfirmarAval${idCandidato}`).on('click', function() {
        const decision = $('input[name="decision_aval"]:checked').val();
        const motivo = $(`#motivoDecisionAval${idCandidato}`).val().trim();
        
        if (!decision) {
            Swal.fire('Advertencia', 'Debe seleccionar una decisión (Aprobar o Rechazar)', 'warning');
            return;
        }
        
        if (motivo.length < 10) {
            Swal.fire('Error', 'El motivo debe tener al menos 10 caracteres', 'warning');
            $(`#motivoDecisionAval${idCandidato}`).focus();
            return;
        }
        
        // Mostrar loading al procesar la decisión
        Swal.fire({
            title: 'Procesando decisión...',
            text: 'Por favor espere mientras se guarda su decisión',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Cerrar modal y procesar decisión
        $(`#modalProcesarAval${idCandidato}`).modal('hide');
        procesarDecisionAvalIndividual(idCandidato, decision, motivo, nombreCompleto);
    });
    
    // Auto-focus y limpieza
    $(`#modalProcesarAval${idCandidato}`).on('shown.bs.modal', function() {
        setTimeout(() => {
            $(`#motivoDecisionAval${idCandidato}`).focus();
        }, 300);
    });
    
    $(`#modalProcesarAval${idCandidato}`).on('hidden.bs.modal', function() {
        $(this).remove();
    });
}

// FUNCIÓN PARA PROCESAR LA DECISIÓN DE AVAL INDIVIDUAL
function procesarDecisionAvalIndividual(idCandidato, decision, motivoDecision, nombreCompleto) {
    console.log('🎯 Procesando decisión aval individual:', idCandidato, decision, motivoDecision);
    
    Swal.fire({
        title: 'Procesando decisión...',
        text: 'Guardando la decisión del aval',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.ajax({
        url: './GerenteTDS/crudaprobaciones.php',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'procesar_aval_gerente',
            id_candidato: idCandidato,
            decision: decision,
            motivo_decision: motivoDecision
        },
        success: function(response) {
            if (response.success) {
                const iconType = decision === 'APROBADO' ? 'success' : 'warning';
                const titleText = decision === 'APROBADO' ? 'Candidato Aprobado' : 'Candidato Rechazado';
                const messageText = `La decisión "${decision} PARA CONTRATACION" ha sido procesada correctamente para ${nombreCompleto}`;
                
                Swal.fire({
                    icon: iconType,
                    title: titleText,
                    text: messageText,
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    // Recargar el expediente para mostrar el cambio de estado
                    verExpedienteCandidatoGerente(idCandidato, 'Candidato');
                });
            } else {
                Swal.fire('Error', response.error || 'Error al procesar la decisión', 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Error de conexión: ' + error, 'error');
        }
    });
}

//========================FIN FUNCIONES DE APROBACION DE AVAL==============================

//=========================================================================================
// BOTON PARA REACTIVAR LA SOLICITUD 
//=========================================================================================

// Event listener para botón Reactivar
$(document).on('click', '.btnReactivarSolicitud', function() {
    const idSolicitud = $(this).data('id');
    const tienda = $(this).data('tienda');
    const puesto = $(this).data('puesto');
    const supervisor = $(this).data('supervisor');
    const fecha = $(this).data('fecha');
    
    // Llenar datos en el modal
    $('#reactivarTienda').text(tienda);
    $('#reactivarPuesto').text(puesto);
    $('#reactivarSupervisor').text(supervisor || 'No especificado');
    $('#reactivarFecha').text(fecha || 'No disponible');
    $('#motivoReactivacion').val('');
    
    // Guardar ID en el modal
    $('#modalReactivarSolicitud').data('id-solicitud', idSolicitud);
    
    // Mostrar modal
    $('#modalReactivarSolicitud').modal('show');
});

// Confirmar reactivación
$('#btnConfirmarReactivacion').on('click', function() {
    const idSolicitud = $('#modalReactivarSolicitud').data('id-solicitud');
    const motivo = $('#motivoReactivacion').val().trim();
    
    // Validar motivo
    if (!motivo) {
        Swal.fire('Error', 'Debe ingresar un motivo de reactivación', 'error');
        return;
    }
    
    if (motivo.length < 10) {
        Swal.fire('Error', 'El motivo debe tener al menos 10 caracteres', 'error');
        return;
    }
    
    // Confirmar acción
    Swal.fire({
        title: '¿Confirmar reactivación?',
        html: `
            <p>Está a punto de reactivar esta solicitud.</p>
            <p><strong>Esta acción:</strong></p>
            <ul style="text-align: left;">
                <li>Cambiará el estado a "Candidatos en Selección"</li>
                <li>Ocultará al candidato contratado</li>
                <li>Permitirá a RRHH continuar con el proceso</li>
            </ul>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, reactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Reactivando solicitud...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            
            // Llamar al backend
            $.ajax({
                url: './GerenteTDS/crudaprobaciones.php',
                type: 'POST',
                data: {
                    action: 'reactivar_solicitud',
                    id_solicitud: idSolicitud,
                    motivo_reactivacion: motivo
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Solicitud reactivada',
                            text: 'La solicitud ha sido reactivada exitosamente',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            $('#modalReactivarSolicitud').modal('hide');
                            // Recargar tabla
                            if (typeof cargarSolicitudes === 'function') {
                                cargarSolicitudes();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('Error', response.error || 'No se pudo reactivar la solicitud', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Error de conexión con el servidor', 'error');
                }
            });
        }
    });
});

//===========================FIN BOTON DE REACTIVACION=====================================
// GLOBALIZAR FUNCIONES PARA EL FUNCIONAMIENTO DE LAS MISMAS
$(document).ready(function() {
      window.ROL_USUARIO = 'GERENTE';
    console.log('Vista GERENTE cargada - ROL_USUARIO establecido como:', window.ROL_USUARIO);
    console.log("✅ Panel de Gerentes iniciando...");
    
    // 🌟 HACER FUNCIONES GLOBALES INMEDIATAMENTE
    window.verArchivoGerente = verArchivoGerente;
    window.descargarArchivoGerente = descargarArchivoGerente;
    window.mostrarArchivosModalGerente = mostrarArchivosModalGerente;
    window.verArchivosCarpetaGerente = verArchivosCarpetaGerente;
    window.subirArchivoGerente = subirArchivoGerente;
    window.descartarCandidatoGerente = descartarCandidatoGerente;
    window.mostrarModalDescarteCompletoGerente = mostrarModalDescarteCompletoGerente;
    window.cargarEstadosAlcanzadosGerente = cargarEstadosAlcanzadosGerente;
    window.mostrarEstadosAlcanzadosGerente = mostrarEstadosAlcanzadosGerente;
    window.confirmarDescarteGerente = confirmarDescarteGerente;
});
      // CARGAR SOLICITUDES AL INICIAR
      cargarSolicitudes();

      console.log("✅ Panel de Gerentes inicializado correctamente");
    });
  </script>
</body>
</html>