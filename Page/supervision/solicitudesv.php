<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Supervisores - Solicitudes de Personal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ENLACES DE CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- ENLACES DE JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
/* =====================================================
   ESTILOS CORPORATIVOS MODERNOS - SISTEMA COMPLETO
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

.btn-create {
  background: linear-gradient(135deg, #047857 0%, #10b981 100%);
  color: white;
}

.btn-create:hover {
  box-shadow: 0 10px 30px rgba(4, 120, 87, 0.3);
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
   TABLA
   ======================================== */
.table-container {
  background: white;
  border-radius: 18px;
  overflow: visible !important;
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
  border: 1px solid #f1f5f9;
}

.table-responsive {
  max-height: 600px !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  position: relative;
}

/* Columnas específicas */
.table-modern td:nth-child(1),
.table-modern th:nth-child(1) {
  width: 50px;
  text-align: center;
}

.table-modern td:nth-child(2),
.table-modern th:nth-child(2) {
  width: 80px;
  text-align: center;
}

.table-modern td:nth-child(12),
.table-modern th:nth-child(12) {
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* ========================================
   TABLA HEADER
   ======================================== */
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
.status-badge.estado-candidatos-seleccion {
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

.btn-edit {
  background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
  color: white;
}

.btn-review {
  background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
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

.swal-wide-files {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.swal-wide-files .swal2-html-container {
  padding: 0 !important;
  margin: 0 !important;
}

.swal-wide-files .swal2-content {
  text-align: left !important;
}

/* Scrollbar personalizado moderno */
.swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar,
.table-responsive::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

.swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar-track,
.table-responsive::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 6px;
}

.swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb,
.table-responsive::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 6px;
  transition: background 0.3s ease;
}

.swal-wide-files div[style*="overflow-y: auto"]::-webkit-scrollbar-thumb:hover,
.table-responsive::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* ========================================
   SELECT ESTADO
   ======================================== */
.select-estado {
  border: none;
  border-radius: 24px;
  padding: 8px 20px;
  font-weight: 700;
  color: white !important;
  text-align: center;
  text-transform: uppercase;
  width: auto;
  max-width: 300px;
  min-width: 220px;
  appearance: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  background-size: 100% 100%;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.select-estado:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

/* ========================================
   COMENTARIOS
   ======================================== */
.comentario-cell {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.btnVerComentarioSuper {
  padding: 6px 12px;
  font-size: 0.8rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  display: inline-block;
  border-radius: 8px;
  background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
  color: white;
  border: none;
  transition: all 0.3s ease;
}

.btnVerComentarioSuper:hover {
  background: linear-gradient(135deg, #475569 0%, #64748b 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* ========================================
   CHAT EMERGENTE
   ======================================== */
.chat-burbuja {
  max-width: 75%;
  padding: 12px 16px;
  margin-bottom: 10px;
  border-radius: 16px;
  line-height: 1.5;
  word-wrap: break-word;
  font-size: 0.92rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.chat-burbuja.izquierda {
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  color: #1e293b;
  border-top-left-radius: 4px;
  margin-right: auto;
}

.chat-burbuja.derecha {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
  border-top-right-radius: 4px;
  margin-left: auto;
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

  .btn-custom {
    padding: 11px 20px;
    font-size: 0.85rem;
  }
}




/* ========================================
   PESTAÑAS ESTILO CARDS PREMIUM PARA LOS FILTROS DE ESTADO
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

/* Iconos grandes y llamativos */
.tab-item i {
  font-size: 2.8rem;
  margin-bottom: 8px;
  transition: all 0.3s ease;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.tab-item:hover i {
  transform: scale(1.15);
}

/* Texto de la pestaña */
.tab-item span:not(.tab-counter) {
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: 0.3px;
  color: #2c3e50;
  text-transform: uppercase;
}

/* Contador grande y destacado */
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

/* ========================================
   COLORES ESPECÍFICOS POR PESTAÑA
   ======================================== */

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

/* ========================================
   ANIMACIONES ADICIONALES
   ======================================== */

/* Animación de pulso en el contador */
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

/* Efecto ripple al hacer click */
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

/* ========================================
   RESPONSIVE
   ======================================== */
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
   LOADING STATE
   ======================================== */
.tab-item.loading .tab-counter {
  opacity: 0.5;
}

.tab-item.loading i {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
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
              <i class="fas fa-users-cog mr-3"></i>
              Panel de Supervisores
            </h1>
            <p class="header-subtitle">Gestión de Solicitudes de Personal</p>
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

      <!-- Controls Section -->
      <div class="controls-section">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="d-flex gap-3">
              <button class="btn btn-custom btn-create btnCrearsolicitud">
                <i class="fas fa-plus-circle mr-2"></i>
                Nueva Solicitud
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <div class="search-container">
              <div class="row">
                <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar en solicitudes...">
                </div>
                <div class="input-group">
                <span class="input-group-text"><i class="fas fa-store"></i></span>
                <input type="text" id="searchTienda" class="form-control" placeholder="Buscar por tienda...">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table Section -->
<!-- Table Section -->
<div class="table-container">
  <div id="loading-indicator" class="loading-state">
    <i class="fas fa-spinner fa-spin"></i>
    <p class="mt-3">Cargando solicitudes...</p>
  </div>
  
  <div class="table-responsive">
    <table id="tblSolicitudes" class="table table-modern" style="display: none;">
      <thead>
        <tr>
          <th width="25"><i class="fas fa-expand-alt"></i></th>
          <th width="50">Tienda</th>
          <th width="160">Puesto</th>
          <th width="155">Supervisor</th>
          <th width="155">Gerente</th>
          <th width="155">Asesora RH asignada</th>
          <th width="120">Fecha Solicitud</th>
          <th width="155">Última Edición</th>
          <th width="170">Estado</th>
          <th width="130">Estado de Aprobacion</th>
          <th width="150">Razón</th>
          <th width="20">Comentario</th>
          <th width="300">Acciones</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
  
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

  <!-- Modal de Historial de Modificaciones -->
  <div class="modal fade" id="modalHistorialIndividual" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-history mr-2"></i>
            Historial de Modificaciones
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
          <!--<a id="btnPdfIndividual" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Generar PDF
          </a>-->
          <button class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

<!--MODAL DE SELECCION DE CVS-->
<div class="modal fade" id="modalResumenCVs" tabindex="-1" role="dialog" aria-labelledby="resumenCVsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content text-dark">
      <div class="modal-header">
        <h5 class="modal-title">Resumen de CVs Seleccionados</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="resumenCVsContenido">
          <p class="text-muted">Cargando selección...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Pruebas -->
<div class="modal fade" id="modalVerPruebas" tabindex="-1" role="dialog" aria-labelledby="modalPruebasTitulo" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title" id="modalPruebasTitulo">Pruebas Adjuntas</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalPruebasContenido">
        <p>Cargando pruebas adjuntas...</p>
      </div>
    </div>
  </div>
</div>

<!--MODAL DE REACTIVACION DEL CANDIDATO-->
<!-- Modal Reactivar Solicitud SUPERVISORES -->
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
                        </div>
                        <div class="col-md-6">
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
    //=================================================================================
    // FUNCIONES PRINCIPALES PARA EL SISTEMA Y AVALES 
    //=================================================================================

// 🎯 FUNCIÓN PARA CARGAR Y MOSTRAR RESULTADO DEL AVAL
function cargarResultadoAvalSupervisor(idSolicitud, tienda, puesto, supervisor, razon) {
  // Mostrar loading
  Swal.fire({
    title: '<i class="fas fa-spinner fa-spin"></i> Cargando resultado...',
    text: 'Obteniendo información de la decisión gerencial',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  // Obtener datos del backend
  $.ajax({
    url: './supervision/crudsolicitudes.php',
    method: 'GET',
    data: {
      action: 'obtener_resultado_aval_supervisor',
      id_solicitud: idSolicitud
    },
    dataType: 'json',
    success: function(response) {
      Swal.close(); // Cerrar loading
      
      if (response.success) {
        mostrarModalResultadoAvalSupervisor(response.data, idSolicitud, tienda, puesto, supervisor, razon);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: response.error || 'No se pudo cargar el resultado del aval'
        });
      }
    },
    error: function(xhr, status, error) {
      Swal.close(); // Cerrar loading
      
      console.error('Error AJAX:', xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar al servidor para obtener el resultado'
      });
    }
  });
}

// 🎯 FUNCIÓN PARA MOSTRAR EL MODAL DEL RESULTADO
function mostrarModalResultadoAvalSupervisor(data, idSolicitud, tienda, puesto, supervisor, razon) {
  const solicitud = data.solicitud || {};
  const aval = data.aval || {};
  
  // Determinar si fue aprobado o rechazado
  const esAprobado = aval.decision === 'APROBADO';
  const decision = esAprobado ? 'APROBADO' : 'RECHAZADO';
  
  // Configurar colores y textos según la decisión
  const config = esAprobado ? {
    color: '#2ecc71',
    bgColor: '#d4edda',
    borderColor: '#c3e6cb',
    textColor: '#155724',
    icon: 'fas fa-check-circle',
    titulo: 'Solicitud Aprobada',
    subtitulo: 'Su solicitud ha sido revisada por el gerente y ha sido aprobada',
    estadoBadge: 'APROBADA',
    badgeClass: 'badge-success'
  } : {
    color: '#e74c3c',
    bgColor: '#f8d7da',
    borderColor: '#f1b0b7',
    textColor: '#721c24',
    icon: 'fas fa-times-circle',
    titulo: 'Solicitud Rechazada',
    subtitulo: 'Su solicitud ha sido revisada por el gerente y no ha sido aprobada',
    estadoBadge: 'RECHAZADA',
    badgeClass: 'badge-danger'
  };

  // Próximos pasos según la decisión
  const proximosPasos = esAprobado ? [
    '<i class="fas fa-check-circle"></i> La solicitud continuará con el proceso normal de contratación',
    '<i class="fas fa-arrow-right"></i> RH procederá con los siguientes pasos del proceso',
    '<i class="fas fa-bell"></i> Se notificará cuando haya actualizaciones del estado'
  ] : [
    '<i class="fas fa-clipboard-list"></i> Puede revisar el motivo del rechazo para entender las razones de la decisión',
    '<i class="fas fa-redo"></i> Si considera necesario, puede crear una nueva solicitud corrigiendo los aspectos mencionados',
    '<i class="fas fa-comments"></i> Para dudas adicionales, puede contactar directamente con el gerente para aclaraciones'
  ];

  Swal.fire({
    title: `
      <div class="resultado-header" style="background: ${config.bgColor}; border: 2px solid ${config.borderColor}; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
          <div style="width: 60px; height: 60px; background: ${config.color}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
            <i class="${config.icon}"></i>
          </div>
          <div style="text-align: left;">
            <h3 style="margin: 0; color: ${config.textColor}; font-weight: 600;">${config.titulo}</h3>
            <p style="margin: 5px 0 0 0; color: ${config.textColor}; font-size: 14px;">${config.subtitulo}</p>
          </div>
        </div>
      </div>
    `,
    html: `
      <style>
        .info-section {
          background: #f8f9fa;
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 20px;
          border-left: 4px solid #007bff;
        }
        .info-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
          gap: 15px;
          margin-bottom: 15px;
        }
        .info-item {
          display: flex;
          flex-direction: column;
          gap: 5px;
        }
        .info-label {
          font-weight: 600;
          color: #495057;
          font-size: 12px;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }
        .info-value {
          color: #212529;
          font-size: 14px;
          font-weight: 500;
        }
        .decision-section {
          background: ${config.bgColor};
          border: 2px solid ${config.borderColor};
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 20px;
        }
        .decision-badge {
          display: inline-block;
          padding: 8px 20px;
          border-radius: 25px;
          font-weight: 600;
          font-size: 14px;
          text-transform: uppercase;
          letter-spacing: 1px;
        }
        .badge-success {
          background: #2ecc71;
          color: white;
        }
        .badge-danger {
          background: #e74c3c;
          color: white;
        }
        .motivo-section {
          background: #fff3cd;
          border: 2px solid #ffc107;
          border-radius: 10px;
          padding: 20px;
          margin-bottom: 20px;
        }
        .motivo-content {
          background: white;
          padding: 15px;
          border-radius: 8px;
          border-left: 4px solid #ffc107;
          font-style: italic;
          line-height: 1.6;
          color: #856404;
        }
        .pasos-section {
          background: #e8f4f8;
          border: 2px solid #17a2b8;
          border-radius: 10px;
          padding: 20px;
        }
        .paso-item {
          display: flex;
          align-items: flex-start;
          gap: 10px;
          margin-bottom: 10px;
          padding: 8px;
          background: white;
          border-radius: 6px;
        }
        .paso-item:last-child {
          margin-bottom: 0;
        }
        .section-title {
          font-weight: 600;
          color: #2c3e50;
          margin-bottom: 15px;
          display: flex;
          align-items: center;
          gap: 8px;
        }
        .fecha-decision {
          text-align: center;
          color: #6c757d;
          font-size: 12px;
          margin-top: 15px;
          padding-top: 15px;
          border-top: 1px solid #dee2e6;
        }
      </style>
      
      <div style="text-align: left; max-height: 600px; overflow-y: auto; padding: 0 10px;">
        
        <!-- INFORMACIÓN DE LA SOLICITUD -->
        <div class="info-section">
          <h6 class="section-title">
            <i class="fas fa-info-circle"></i> Información de la Solicitud
          </h6>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label"><i class="fas fa-hashtag"></i> ID Solicitud</span>
              <span class="info-value">#${solicitud.id}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-store"></i> Tienda</span>
              <span class="info-value">Tienda ${solicitud.tienda}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-briefcase"></i> Puesto Solicitado</span>
              <span class="info-value">${solicitud.puesto}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-calendar-alt"></i> Fecha de Solicitud</span>
              <span class="info-value">${solicitud.fecha_solicitud}</span>
            </div>
          </div>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label"><i class="fas fa-user-tie"></i> Supervisor</span>
              <span class="info-value">${solicitud.supervisor}</span>
            </div>
            <div class="info-item">
              <span class="info-label"><i class="fas fa-edit"></i> Razón de la Vacante</span>
              <span class="info-value">${solicitud.razon}</span>
            </div>
          </div>
        </div>

        <!-- ESTADO DE APROBACIÓN -->
        <div class="decision-section">
          <h6 class="section-title" style="color: ${config.textColor};">
            <i class="fas fa-gavel"></i> Estado de Aprobación
          </h6>
          <div style="text-align: center; margin-bottom: 15px;">
            <span class="decision-badge ${config.badgeClass}">${config.estadoBadge}</span>
          </div>
          <div style="text-align: center; color: ${config.textColor};">
            <strong>Revisado por:</strong> ${aval.gerente}<br>
            <small>Fecha de decisión: ${aval.fecha_decision}</small>
          </div>
        </div>

        <!-- MOTIVO DE LA DECISIÓN -->
        <div class="motivo-section">
          <h6 class="section-title" style="color: #856404;">
            <i class="fas fa-comment-alt"></i> ${esAprobado ? 'Comentarios del Gerente' : 'Motivo del Rechazo'}
          </h6>
          <div class="motivo-content">
            ${aval.comentario || 'Sin comentarios adicionales'}
          </div>
        </div>

        <!-- PRÓXIMOS PASOS -->
        <div class="pasos-section">
          <h6 class="section-title" style="color: #17a2b8;">
            <i class="fas fa-route"></i> Próximos Pasos
          </h6>
          ${proximosPasos.map(paso => `
            <div class="paso-item">
              <span style="flex: 1; color: #495057;">${paso}</span>
            </div>
          `).join('')}
        </div>

        <div class="fecha-decision">
          <i class="fas fa-clock"></i> Última actualización: ${aval.fecha_decision}
        </div>

      </div>
    `,
    width: '800px',
    showCancelButton: false,
    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
    confirmButtonColor: config.color,
    customClass: {
      popup: 'resultado-aval-popup'
    }
  });
}

// 🎯 FUNCIÓN PARA AGREGAR EL BOTÓN EN LAS TABLAS
function agregarBotonResultadoAval(idSolicitud, tienda, puesto, supervisor, razon) {
  return `
    <button class="btn btn-info btn-sm btnVerResultadoAval" 
            data-id="${idSolicitud}"
            data-tienda="${tienda}"
            data-puesto="${puesto}"
            data-supervisor="${supervisor}"
            data-razon="${razon}"
            title="Ver resultado del aval gerencial">
      <i class="fas fa-clipboard-check"></i> Ver Resultado
    </button>
  `;
}


    //=================================================================================
    // INICIALIZACION DE TODO EL PROGRAMA
    //=================================================================================
$(document).ready(function() {
    window.ROL_USUARIO = 'SUPERVISOR';
    window.CANDIDATOS_INDEX = {};
    
    // Event delegation para candidatos - SUPERVISIÓN
    $(document).off('click', '.candidate-card .card').on('click', '.candidate-card .card', function(e) {
        e.preventDefault();
        if (window.ROL_USUARIO === 'SUPERVISOR') {
            const idCandidato = $(this).closest('.candidate-card').data('candidato-id');
            seleccionarCandidatoSupervisor(idCandidato);
        }
    });
    
    console.log('Vista SUPERVISOR inicializada');

      let archivosOriginales =[];
      let archivosSeleccionados = new Set();
      let solicitudActual =null;
      let solicitudes = [];
      let idSolicitudActual = null;
      let rowsPerPage = 12;
      let currentPage = 1;
      let modalAbierto = false;

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
  
  // Actualizar la variable global solicitudes
  solicitudes = solicitudesFiltradas;
  
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




// ✅ FUNCIÓN CORREGIDA - MANEJA TANTO ARRAYS COMO OBJETOS
     // FUNCIÓN PARA CARGAR SOLICITUDES
function cargarSolicitudes() {
  $('#loading-indicator').show();
  $('#tblSolicitudes').hide();
  $('#empty-state').hide();

  $.ajax({
    url: './supervision/crudsolicitudes.php?action=get_solicitudes',
    type: 'GET',
    dataType: 'json',
    success: function (data) {
      console.log("🔍 Respuesta del servidor:", data);
      
      if (data.success === false) {
        console.error('❌ Error:', data.error);
        Swal.fire('Error', data.error, 'error');
        return;
      }
      
      if (data.debug) {
        console.log('🐛 Debug info:', data.debug);
        return;
      }
      
      if (!Array.isArray(data)) {
        console.error('❌ Datos no son array:', data);
        Swal.fire('Error', 'Formato de datos incorrecto', 'error');
        return;
      }
      
      // Guardar solicitudes originales
      solicitudesOriginales = data.success ? data.data : data;
      solicitudes = solicitudesOriginales;
      
      // Actualizar contadores
      actualizarContadores();
      
      // Aplicar filtro actual
      filtrarSolicitudes(filtroActual);
      
      $('#loading-indicator').hide();
    },
    error: function (xhr, status, error) {
      console.error('Error cargando solicitudes:', error);
      console.error('Respuesta del servidor:', xhr.responseText);
      $('#loading-indicator').hide();
      Swal.fire({
        icon: 'error',
        title: 'Error de Conexión',
        text: 'No se pudieron cargar las solicitudes. Verifica tu conexión.',
        confirmButtonText: 'Reintentar'
      }).then(() => {
        cargarSolicitudes();
      });
    }
  });
}


//RENDERIZAR TABLA
function renderTable(data) {
  const tbody = $('#tblSolicitudes tbody');
  tbody.empty();

  const start = (currentPage - 1) * rowsPerPage;
  const end = start + rowsPerPage;
  const pageData = data.slice(start, end);

  pageData.forEach((item, index) => {
    const globalIndex = start + index;

    let statusClass = '';
    const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();

    if (estado.includes('pendiente')) statusClass = 'estado-pendiente';
    else if (estado.includes('activa')) statusClass = 'estado-activa';
    else if (estado.includes('cvs')) statusClass = 'estado-cvs';
    else if (estado.includes('candidatos en seleccion')) statusClass = 'estado-candidatos-seleccion';
    else if (estado.includes('plaza cubierta')) statusClass = 'estado-plaza-cubierta';
    else if (estado.includes('psico')) statusClass = 'estado-psico';
    else if (estado.includes('entrevista rh')) statusClass = 'estado-rh';
    else if (estado.includes('tecnica')) statusClass = 'estado-tecnica';
    else if (estado.includes('prueba')) statusClass = 'estado-prueba';
    else if (estado.includes('poligrafo')) statusClass = 'estado-poligrafo';
    else if (estado.includes('expediente')) statusClass = 'estado-expediente';
    else if (estado.includes('confirmacion')) statusClass = 'estado-confirmacion';
    else if (estado.includes('contratada')) statusClass = 'estado-contratada';
    else statusClass = 'estado-pendiente'; // por defecto

    // Estados del badge de aprobación
    let aprobacionClass = '';
    const aprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase();
    if (aprobacion.includes('por aprobar')) aprobacionClass = 'estado-pendiente';
    else if (aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no'))) aprobacionClass = 'estado-contratada';
    else if (aprobacion.includes('no aprobado')) aprobacionClass = 'estado-prueba';
    else aprobacionClass = 'estado-pendiente';

    const fechaModificacion = item.FECHA_MODIFICACION || '—';
    const comentario = item.COMENTARIO_SOLICITUD || '-';
    const idHistorico = item.ID_HISTORICO;
    const estadoAprobacionMostrar = item.ESTADO_APROBACION || 'Por Aprobar';
    const noLeidos = parseInt(item.NO_LEIDOS) || 0;

    // NUEVO: Lógica para mostrar asesora de RRHH solo si está aprobada
    const dirigidoRH = item.DIRIGIDO_RH || null;
    const mostrarDirigidoRH = (aprobacion === 'aprobado' && dirigidoRH) 
      ? `<span class="text-success"><i class="fas fa-user-check mr-1"></i><strong>${dirigidoRH}</strong></span>`
      : '<span class="text-muted"><i class="fas fa-user-times mr-1"></i>Sin asignación</span>';
    
    console.log('ID:', idHistorico, 'Comentario:', comentario, 'NO_LEIDOS:', item.NO_LEIDOS);
    console.log('Estado Aprobación:', item.ID_SOLICITUD, item.ESTADO_APROBACION);
    console.log('Dirigido RH:', item.ID_SOLICITUD, dirigidoRH, 'Mostrar:', mostrarDirigidoRH); // NUEVO DEBUG
    
// SOLUCIÓN FORZADA - ELIMINAR COMPLETAMENTE EL BOTÓN DE COMENTARIO
    const comentarioMostrar = (() => {
    const estadoAprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase().trim();
    
    console.log('🔍 VERIFICANDO COMENTARIO PARA SOLICITUD:', item.ID_SOLICITUD);
    console.log('📊 Estado de aprobación:', estadoAprobacion);
    console.log('💬 Comentario:', comentario);
    
    // REGLA ÚNICA: Solo mostrar comentarios si está "APROBADO"
    if (estadoAprobacion !== 'aprobado') {
        console.log('🚫 NO ESTÁ APROBADO - NO MOSTRAR COMENTARIO');
        console.log('📋 Estados que no muestran comentario: Por Aprobar, No Aprobado');
        return '<span class="text-muted">—</span>';
    }
    
    console.log('✅ ESTADO APROBADO - VERIFICANDO COMENTARIO');
    
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
    
    // ❌ Filtrar comentarios automáticos del sistema/gerente
    const esComentarioAutomatico = 
        // Comentarios típicos del gerente
        comentario.includes('Cambio de aprobación a:') ||
        comentario.includes('Asignado a:') ||
        comentario.includes('- Asignado a:') ||
        
        // Comentarios del sistema
        comentario === 'Cambio de estado de aprobación' ||
        comentario === 'Estado de aprobación actualizado' ||
        comentario === 'Aprobación procesada por gerente' ||
        comentario.includes('Estado actualizado por gerente') ||
        comentario.includes('Procesado por gerencia') ||
        comentario.includes('Decisión del gerente:') ||
        
        // Patrones automáticos
        /^Cambio de aprobación a: .+/.test(comentario) ||
        /^.+ - Asignado a: .+/.test(comentario) ||
        
        // Comentarios cortos con palabras del sistema
        (comentario.length < 50 && 
         (comentario.toLowerCase().includes('aprobación') ||
          comentario.toLowerCase().includes('asignado') ||
          comentario.toLowerCase().includes('procesado'))) ||
        
        // Estados puros
        comentario === 'Aprobado' ||
        comentario === 'No Aprobado' ||
        comentario === 'Por Aprobar';
    
    if (esComentarioAutomatico) {
        console.log('❌ COMENTARIO AUTOMÁTICO DEL SISTEMA FILTRADO');
        return '<span class="text-muted">—</span>';
    }
    
    // ✅ Todo OK: Estado APROBADO + Comentario real de RRHH
    console.log('✅ COMENTARIO REAL DE RRHH EN SOLICITUD APROBADA - MOSTRANDO BOTÓN');
    return `<div class="badge-container">
        <button class="btn btn-sm btn-info btnVerComentarioSuper"
                data-id="${idHistorico}"
                title="Ver comentario de RRHH">
            <i class="fas fa-comment"></i> Ver
        </button>
        ${noLeidos > 0 ? `<span class="notification-badge ${noLeidos > 9 ? 'wide' : ''}">${noLeidos}</span>` : ''}
    </div>`;
})();
    
             // Declarar variable acciones por cada fila
                let acciones = '';
                // ✅ NUEVO: AGREGAR BOTÓN CONDICIONAL PARA VER RESULTADO DE APROBACIÓN
                if (aprobacion === 'no aprobado') {
                acciones += `
                    <button class="btn btn-warning btn-sm btnVerResultadoAprobacion" 
                            data-id="${item.ID_SOLICITUD}"
                            data-aprobacion="${item.ESTADO_APROBACION}"
                            title="Ver motivo del rechazo">
                    <i class="fas fa-exclamation-circle"></i> Ver Resultado
                    </button>`;
                }

                if ((aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no')))) {
                acciones += `
                    <button class="btn btn-success btn-sm btnVerResumenAprobadoGerenncial" 
                        data-id="${item.ID_SOLICITUD}"
                        title="Ver resumen de aprobación gerencial">
                        <i class="fas fa-clipboard-check"></i> Ver Resumen
                    </button>`;
                }

                // Mostrar botón si está en "Candidatos en Selección" o "Plaza Cubierta"
                const estadoSolicitud = item.ESTADO_SOLICITUD || '';
                if (estadoSolicitud === 'Candidatos en Seleccion' || estadoSolicitud === 'Plaza Cubierta') {
                    const cantidadCandidatos = item.TOTAL_CANDIDATOS || 0;
                    const esPlazaCubierta = estadoSolicitud === 'Plaza Cubierta';
                    
                    acciones += `
                        <button class="btn btn-sm ${esPlazaCubierta ? 'btn-info' : 'btn-success'} ml-1" 
                                onclick="mostrarCandidatosEnviados('${item.ID_SOLICITUD}')" 
                                title="${esPlazaCubierta ? 'Ver candidato contratado' : 'Ver candidatos enviados'}">
                            <i class="fas ${esPlazaCubierta ? 'fa-user-check' : 'fa-users'}"></i> 
                            ${esPlazaCubierta ? 'Ver Contratado' : 'Candidatos (' + cantidadCandidatos + ')'}
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
                                title="Reactivar solicitud">
                            <i class="fas fa-redo"></i> Reactivar
                        </button>
                    `;
                }

                // VERIFICAR SI NECESITA BOTÓN DE OBSERVACIONES DEL DÍA DE PRUEBA
                    const esDiaDePrueba = estado.toLowerCase().includes('día de prueba') || estado.toLowerCase().includes('dia de prueba');
                    const esObservacionesEnviadas = estado.toLowerCase().includes('enviado') || estado.toLowerCase().includes('enviadas');

                        //  NUEVA VERIFICACIÓN ESPECÍFICA PARA PENDIENTE AVAL GERENCIA
                        const esPendienteAvalGerencia = estado.toLowerCase().includes('pendiente aval gerencia');

                        console.log(`🔍 EVALUANDO OBSERVACIONES - Solicitud ${item.ID_SOLICITUD}:`);
                        console.log(`📊 Estado actual: "${estado}"`);
                        console.log(`🔍 esDiaDePrueba: ${esDiaDePrueba}`);
                        console.log(`📨 esObservacionesEnviadas: ${esObservacionesEnviadas}`);
                        console.log(`⏳ esPendienteAvalGerencia: ${esPendienteAvalGerencia}`);

                        // PRIMERO VERIFICAR SI ES PENDIENTE AVAL GERENCIA
                        if (estado.toLowerCase().includes('aval enviado')) {
                            console.log('✅ ESTADO AVAL ENVIADO → Mostrar botón resultado');
                            acciones += `
                                <button class="btn btn-success btn-sm btnVerResultadoAval" 
                                        data-id="${item.ID_SOLICITUD}"
                                        data-tienda="${item.NUM_TIENDA}"
                                        data-puesto="${item.PUESTO_SOLICITADO}"
                                        data-supervisor="${item.SOLICITADO_POR}"
                                        data-razon="${item.RAZON || ''}"
                                        title="Ver resultado del aval gerencial">
                                    <i class="fas fa-clipboard-check"></i> Ver Resultado Aval
                                </button>`;
                        }
                        // ✅ SEGUNDO: VERIFICAR SI ES PENDIENTE AVAL GERENCIA
                        else if (esPendienteAvalGerencia) {
                            console.log('🚫 ESTADO PENDIENTE AVAL GERENCIA → Mostrar mensaje de espera');
                            acciones += `
                                <span style="background: #ff6b6b; color: white; padding: 6px 12px; border-radius: 15px; font-size: 11px; font-weight: 600; display: inline-block;">
                                    <i class="fas fa-clock"></i> Esperando confirmacion Aval
                                </span>`;
                        }
                        // ✅ TERCERO: SI NO ES PENDIENTE AVAL, CONTINUAR CON LA LÓGICA NORMAL
                        else if (esDiaDePrueba) {
                            if (esObservacionesEnviadas) {
                                console.log('OBSERVACIONES ENVIADAS → Mostrar solo VER');
                                acciones += `
                                    <button class="btn btn-info btn-sm btnVerObservacionesDiaPrueba" 
                                            data-id="${item.ID_SOLICITUD}"
                                            title="Ver resumen de observaciones enviadas">
                                        <i class="fas fa-eye"></i> Ver Observaciones
                                    </button>`;
                            } else {
                                console.log('📝 SIN OBSERVACIONES → Mostrar SUBIR');
                                acciones += `
                                    <button class="btn btn-warning btn-sm btnSubirObservacionesDiaPrueba" 
                                            data-id="${item.ID_SOLICITUD}"
                                            data-puesto="${item.PUESTO_SOLICITADO}"
                                            data-tienda="${item.NUM_TIENDA}"
                                            data-supervisor="${item.SOLICITADO_POR}"
                                            title="Subir observaciones del día de prueba">
                                        <i class="fas fa-upload"></i> Subir Observaciones
                                    </button>`;
                            }
                        }
                        // ✅ CUARTO: VERIFICAR OTROS ESTADOS DE OBSERVACIONES
                        else if (estado.toLowerCase().includes('observaciones') && 
                                (estado.toLowerCase().includes('enviadas') || estado.toLowerCase().includes('enviado'))) {
                            
                            console.log(`📨 OTRO ESTADO DE OBSERVACIONES ENVIADAS`);
                            acciones += `
                                <button class="btn btn-info btn-sm btnVerObservacionesDiaPrueba" 
                                        data-id="${item.ID_SOLICITUD}"
                                        title="Ver observaciones enviadas">
                                    <i class="fas fa-eye"></i> Ver Observaciones
                                </button>`;
                        }
    const row = `
      <tr data-id="${item.ID_SOLICITUD}">
        <td>
          <button class="btn btn-expand btn-ver-historial-modificaciones" data-id="${item.ID_SOLICITUD}" title="Ver historial">
            <i class="fas fa-plus"></i>
          </button>
        </td>
        <td><span class="badge badge-primary">${item.NUM_TIENDA}</span></td>
        <td><strong>${item.PUESTO_SOLICITADO}</strong></td>
        <td><small class="text-muted">${item.SOLICITADO_POR}</small></td>
        <td><small>${item.DIRIGIDO_A || '—'}</small></td>
        <td><small class="text-info">${mostrarDirigidoRH}</small></td>
        <td><small>${item.FECHA_SOLICITUD}</small></td>
        <td><small class="text-muted">${fechaModificacion}</small></td>
        <td>
          <span class="status-badge ${statusClass}" title="${item.ULTIMO_COMENTARIO || 'Sin comentario'}">
            ${item.ESTADO_SOLICITUD}
          </span>
        </td>
        <td>
          <span class="status-badge ${aprobacionClass}" title="Estado de Aprobación por Gerencia">
            ${estadoAprobacionMostrar}
          </span>
        </td>
        <td><small>${item.RAZON || '—'}</small></td>
        <td class="comentario-cell">${comentarioMostrar}</td>
        <td>
          <div class="actions-container">
            <button class="btn btn-action btn-edit btnEditarSolicitud"
                    data-index="${globalIndex}" title="Editar solicitud">
              <i class="fas fa-edit"></i> Editar
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

        // Event listeners para paginación
        $('.pagination .page-link').click(function (e) {
          e.preventDefault();
          const page = parseInt($(this).data('page'));
          if (page && page !== currentPage && page >= 1 && page <= totalPages) {
            currentPage = page;
            renderTable(solicitudes);
            setupPagination(solicitudes);
          }
        });
      }

      // FILTROS DE BÚSQUEDA
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

//FUNCION PARA VER LOS ARCHIVOS PSICO Y POLIGRAFO
$(document).off('click', '.btnVerPruebas').on('click', '.btnVerPruebas', function () {
    const idSolicitud = $(this).data('id');
    const tipoArchivo = $(this).data('tipo'); // debe ser 'PSICOMETRICA' o 'POLIGRAFO'

    $('#modalPruebasContenido').html('<p>Cargando archivos...</p>');
    $('#modalVerPruebas').modal('show');

    $.ajax({
        url: './supervision/crudsolicitudes.php?action=ver_pruebas_adjuntas',
        method: 'POST',
        data: {
            id_solicitud: idSolicitud,
            tipo: tipoArchivo
        },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.archivos.length > 0) {
                const archivo = response.archivos[0]; // Solo el más reciente
                const nombreCompleto = archivo.NOMBRE_ARCHIVO;
                const nombreLimpio = nombreCompleto.split('/').pop();
                const ext = nombreLimpio.toLowerCase().split('.').pop();
                const icon = ext === 'pdf' ? 'fa-file-pdf' :
                             ext === 'doc' || ext === 'docx' ? 'fa-file-word' : 'fa-file';

                let contenido = `
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${nombreLimpio}</span>
                            <div>
                                <a href="${nombreCompleto}" target="_blank" class="btn btn-sm btn-outline-primary mr-2">
                                    <i class="fas ${icon}"></i> Ver
                                </a>
                                <a href="${nombreCompleto}" download class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-download"></i> Descargar
                                </a>
                            </div>
                        </li>
                    </ul>`;
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


//FUNCION PARA VER RESUMEN DE SELECCIONES #
$(document).off('click', '.btnVerCVResumen').on('click', '.btnVerCVResumen', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation(); // Evitar múltiples ejecuciones
    const idSolicitud = $(this).data('id');
    console.log("Iniciando solicitud para ID:", idSolicitud);
    
    // Opción 1: Envío estándar
    const requestData = {
      action: 'ver_resumen_cvs',
        id_solicitud: idSolicitud
    };
    
    // Opción 2: Envío como JSON
    const jsonData = JSON.stringify(requestData);
    
    // Mostrar loading con estilo mejorado
    const swalInstance = Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando...',
        html: 'Obteniendo información de documentos seleccionados',
        showConfirmButton: false,
        allowOutsideClick: false,
        customClass: {
            popup: 'animated fadeInDown faster'
        },
        didOpen: () => Swal.showLoading()
    });
    
    // Intento 1: Envío tradicional (manteniendo la lógica original)
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=ver_resumen_cvs',
        type: 'POST',
        data: requestData,
        dataType: 'json',
        success: function(response) {
            swalInstance.close();
            handleResponse(response);
        },
        error: function(xhr) {
            // Si falla, intentar con envío como JSON (manteniendo la lógica original)
            console.warn("Primer intento falló, probando con JSON...");
            sendAsJson();
        }
    });
    
    function sendAsJson() {
        $.ajax({
            url: './supervision/crudsolicitudes.php?action=ver_resumen_cvs',
            type: 'POST',
            data: jsonData,
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                swalInstance.close();
                handleResponse(response);
            },
            error: function(xhr) {
                swalInstance.close();
                console.error("Error completo:", {
                    status: xhr.status,
                    response: xhr.responseText,
                    headers: xhr.getAllResponseHeaders()
                });
                Swal.fire({
                    title: '<i class="fas fa-exclamation-circle text-danger"></i> Error',
                    html: `<div class="text-left">
                             <p>No se pudo conectar al servidor.</p>
                             <div class="mt-2 p-2 bg-light rounded">
                               <small>Estado: ${xhr.status}</small>
                               <pre class="mt-2" style="max-height: 150px; overflow-y: auto;">${xhr.responseText || 'Sin respuesta'}</pre>
                             </div>
                           </div>`,
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
            }
        });
    }
    
    function handleResponse(response) {
        console.log("Respuesta completa:", response);
        if (response.success) {
            // Mostrar resultados en el modal con diseño mejorado
            if (response.archivos && response.archivos.length > 0) {
                let html = '<div class="file-list p-2">';
                response.archivos.forEach(file => {
                    // Determinar icono según tipo de archivo
                    let fileIcon = 'file';
                    let fileColor = 'secondary';
                    
                    if (file.TIPO === 'PDF' || file.EXTENSION === 'pdf') {
                        fileIcon = 'file-pdf';
                        fileColor = 'danger';
                    } else if (['DOC', 'DOCX'].includes(file.TIPO) || ['doc', 'docx'].includes(file.EXTENSION)) {
                        fileIcon = 'file-word';
                        fileColor = 'primary';
                    } else if (['XLS', 'XLSX'].includes(file.TIPO) || ['xls', 'xlsx'].includes(file.EXTENSION)) {
                        fileIcon = 'file-excel';
                        fileColor = 'success';
                    } else if (['JPG', 'JPEG', 'PNG'].includes(file.TIPO) || ['jpg', 'jpeg', 'png'].includes(file.EXTENSION)) {
                        fileIcon = 'file-image';
                        fileColor = 'info';
                    }
                    
                    html += `
                        <div class="file-item d-flex align-items-center p-2 mb-2 border rounded">
                            <div class="file-icon mr-3">
                                <i class="fas fa-${fileIcon} fa-2x text-${fileColor}"></i>
                            </div>
                            <div class="file-info flex-grow-1">
                                <div class="font-weight-bold">${file.NOMBRE_ARCHIVO}</div>
                                <div class="small text-muted">
                                    <span class="mr-2"><i class="fas fa-calendar-alt mr-1"></i>${file.FECHA || 'N/A'}</span>
                                    <span class="badge badge-${fileColor}">${file.TIPO}</span>
                                </div>
                            </div>
                            <div class="file-actions">
                                <button class="btn btn-sm btn-primary btn-ver-documento" data-ruta="${file.RUTA || ''}">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </button>
                                <button class="btn btn-sm btn-success ml-1 btn-descargar-documento" data-ruta="${file.RUTA || ''}" data-nombre="${file.NOMBRE_ARCHIVO}">
                                    <i class="fas fa-download mr-1"></i> Descargar
                                </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $('#resumenCVsContenido').html(html);
                
                // Agregar estilos si no existen
                if (!document.getElementById('file-list-styles')) {
                    const style = document.createElement('style');
                    style.id = 'file-list-styles';
                    style.innerHTML = `
                        .file-list {
                            max-height: 400px;
                            overflow-y: auto;
                        }
                        .file-item {
                            transition: all 0.2s;
                        }
                        .file-item:hover {
                            background-color: #f8f9fa;
                            transform: translateY(-2px);
                            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                        }
                        .file-icon {
                            width: 40px;
                            text-align: center;
                        }
                    `;
                    document.head.appendChild(style);
                }
                
                // Configurar eventos para los botones
                $('.btn-ver-documento').on('click', function() {
                    const ruta = $(this).data('ruta');
                    if (ruta) {
                        window.open(ruta, '_blank');
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se encontró la ruta del documento',
                            icon: 'error',
                            timer: 2000
                        });
                    }
                });
                
                $('.btn-descargar-documento').on('click', function() {
                    const ruta = $(this).data('ruta');
                    const nombre = $(this).data('nombre');
                    if (ruta) {
                        const link = document.createElement('a');
                        link.href = ruta;
                        link.download = nombre;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        // Notificación de descarga
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        
                        Toast.fire({
                            icon: 'success',
                            title: `Descargando: ${nombre}`
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se encontró la ruta del documento',
                            icon: 'error',
                            timer: 2000
                        });
                    }
                });
            } else {
                $('#resumenCVsContenido').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-4x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">No se encontraron documentos</h5>
                        <p class="text-muted">No hay documentos seleccionados para esta solicitud</p>
                    </div>
                `);
            }
            $('#modalResumenCVs').modal('show');
        } else {
            Swal.fire({
                title: '<i class="fas fa-exclamation-triangle text-warning"></i> Error',
                text: response.error || 'Error desconocido',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#3085d6'
            });
        }
    }
});

//chat emergente
//FUNCIÓN PARA MOSTRAR COMENTARIO DE RRHH
    $(document).off('click', '.btnVerComentarioSuper').on('click', '.btnVerComentarioSuper', function (e) {
        e.preventDefault();
        e.stopPropagation();
        modalAbierto = true;
        const idHistorico = $(this).data('id');
        console.log("ID Histórico para chat:", idHistorico);
        
        if (!idHistorico) {
            console.error("No se encontró ID histórico");
            Swal.fire('Error', 'No se encontró el ID del histórico', 'error');
            return;
        }

        function mostrarChat(mensajes) {
            console.log("Mostrando chat con", mensajes.length, "mensajes");
            let chatHtml = `
                <div id="chat-contenedor" style="
                    max-height: 400px;
                    overflow-y: auto;
                    padding: 20px;
                    background: #ffffff;
                    margin-bottom: 20px;
                ">
            `;

            if (mensajes && mensajes.length > 0) {
                mensajes.forEach(msg => {
                    const rol = msg.rol ? msg.rol.toLowerCase() : '';
                    const esSupervisor = rol.includes('supervisor');
                    const remitente = esSupervisor ? 'SUPERVISOR' : 'RRHH';

                    if (esSupervisor) {
                        // Mensaje del supervisor (derecha, morado)
                        chatHtml += `
                            <div style="
                                display: flex;
                                justify-content: flex-end;
                                margin-bottom: 15px;
                            ">
                                <div style="
                                    max-width: 70%;
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    color: white;
                                    padding: 12px 16px;
                                    border-radius: 18px 18px 4px 18px;
                                    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
                                ">
                                    <div style="
                                        font-weight: 600;
                                        font-size: 11px;
                                        text-transform: uppercase;
                                        letter-spacing: 0.5px;
                                        margin-bottom: 4px;
                                        opacity: 0.9;
                                    ">${remitente}</div>
                                    <div style="
                                        font-size: 14px;
                                        line-height: 1.4;
                                        margin-bottom: 6px;
                                    ">${msg.mensaje}</div>
                                    <div style="
                                        font-size: 11px;
                                        opacity: 0.8;
                                        text-align: right;
                                    ">${msg.fecha}</div>
                                </div>
                            </div>
                        `;
                    } else {
                        // Mensaje de RRHH (izquierda, gris)
                        chatHtml += `
                                        <div style="
                                            display: flex;
                                            justify-content: flex-start;
                                            margin-bottom: 15px;
                                        ">
                                            <div style="
                                                max-width: 70%;
                                                background: #d7d7f3ff;
                                                color: #0e0d0dff;
                                                padding: 12px 16px;
                                                border-radius: 18px 18px 18px 4px;
                                                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                                            ">
                                                <div style="
                                                    font-weight: 600;
                                                    font-size: 11px;
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                    margin-bottom: 4px;
                                                    color: #050505ff;
                                                ">${remitente}</div>
                                                <div style="
                                                    font-size: 14px;
                                                    line-height: 1.4;
                                                    margin-bottom: 6px;
                                                ">${msg.mensaje}</div>
                                                <div style="
                                                    font-size: 11px;
                                                    color: #0e0d0dff;
                                                    text-align: left;
                                                ">${msg.fecha}</div>
                                            </div>
                                        </div>
                                    `;
                    }
                });
            } else {
                chatHtml += `
                    <div style="
                        text-align: center;
                        padding: 20px;
                        color: #999;
                    ">
                        <i class="fas fa-comment-slash" style="font-size: 48px; margin-bottom: 16px;"></i>
                        <p style="font-size: 16px; margin: 0;">No hay mensajes en este chat</p>
                    </div>
                `;
            }

            chatHtml += `</div>`;
            chatHtml += `
                <div style="
                    border-top: 1px solid #e0e0e0;
                    padding-top: 20px;
                ">
                    <textarea id="nuevoMensaje" 
                        placeholder="Escribe tu respuesta..." 
                        style="
                            width: 100%;
                            min-height: 80px;
                            padding: 12px 16px;
                            border: 1px solid #ddd;
                            border-radius: 12px;
                            font-size: 14px;
                            font-family: inherit;
                            resize: vertical;
                            outline: none;
                            transition: border-color 0.2s;
                        "
                        onfocus="this.style.borderColor='#667eea'"
                        onblur="this.style.borderColor='#ddd'"
                    ></textarea>
                </div>
            `;

            // Obtener nombre del asesor de rh desde la fila de la tabla
            const filaActual = $(`button[data-id="${idHistorico}"]`).closest('tr');
            const nombreRRHH = filaActual.find('td:nth-child(6)').text().trim() || 'RRHH';

            
            Swal.fire({
                title: `<i class="fas fa-comments"></i> ${nombreRRHH}`,
                html: chatHtml,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-paper-plane"></i> Enviar',
                cancelButtonText: 'Cerrar',
                focusConfirm: false,
                allowOutsideClick: false,
                customClass: {
                    popup: 'chat-modal-popup',
                    title: 'chat-modal-title',
                    confirmButton: 'chat-send-button',
                    cancelButton: 'chat-cancel-button'
                },
                preConfirm: () => {
                    const mensaje = $('#nuevoMensaje').val().trim();
                    if (!mensaje) {
                        Swal.showValidationMessage('Debes escribir un mensaje');
                        return false;
                    }
                    return mensaje;
                },
                didOpen: () => {
                    const container = document.getElementById('chat-contenedor');
                    if (container) container.scrollTop = container.scrollHeight;
                    
                    // Agregar estilos CSS dinámicamente
                    if (!document.getElementById('chat-styles')) {
                        const styles = document.createElement('style');
                        styles.id = 'chat-styles';
                        styles.textContent = `
                            .chat-modal-popup {
                                border-radius: 16px !important;
                                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
                            }
                            .chat-modal-title {
                                font-size: 18px !important;
                                font-weight: 600 !important;
                                color: #333 !important;
                            }
                            .chat-send-button {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                                border: none !important;
                                border-radius: 8px !important;
                                padding: 10px 20px !important;
                                font-weight: 600 !important;
                                transition: transform 0.2s !important;
                            }
                            .chat-send-button:hover {
                                transform: translateY(-1px) !important;
                                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
                            }
                            .chat-cancel-button {
                                background: #f5f5f5 !important;
                                color: #666 !important;
                                border: none !important;
                                border-radius: 8px !important;
                                padding: 10px 20px !important;
                                font-weight: 600 !important;
                            }
                        `;
                        document.head.appendChild(styles);
                    }
                }
            }).then((result) => {
                
                if (result.isConfirmed) {
                    const nuevoMensaje = result.value;

                    Swal.fire({
                        title: 'Enviando mensaje...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    // CORREGIDO - Usar nombres fijos según el contexto
                    const nombreSupervisor = filaActual.find('td:nth-child(4)').text().trim() || 'Supervisor'; 
                    const nombreRRHH = 'RRHH';
                    const esSupervisor = true; // Siempre verdadero porque estás en solicitudesv.php
                    const remitente = nombreSupervisor;


                    $.ajax({
                        url: './supervision/crudsolicitudes.php?action=guardar_respuesta_chat',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                        id_historico: idHistorico,
                        mensaje: nuevoMensaje,
                        rol: 'SUPERVISOR',
                        remitente: remitente //NUEVO
                        },
                        success: function (response) {
                            console.log("Respuesta del servidor:", response);
                            if (response && response.success) {
                                $.ajax({
                                    url: './supervision/crudsolicitudes.php?action=marcar_mensajes_leidos_supervisor',
                                    type: 'POST',
                                    data: { id_historico: idHistorico }
                                });
                                cargarMensajesChat(idHistorico);
                                // En lugar de cargarSolicitudes(); en la línea 508, pon:
                                actualizarBadgesSilenciosamente();
                            } else {
                                Swal.fire('Error', response?.error || 'Error al enviar el mensaje', 'error');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error AJAX:', xhr.responseText);
                            Swal.fire('Error', 'Error de conexión: ' + error, 'error');
                        }
                    });
                }
            });
        }

        function cargarMensajesChat(idHistorico) {
            console.log("Cargando mensajes para ID:", idHistorico);

            Swal.fire({
                title: 'Cargando comentario...',
                text: 'Por favor espera un momento.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: './supervision/crudsolicitudes.php?action=get_comentarios_chat',
                type: 'POST',
                dataType: 'json',
                data: { id_historico: idHistorico },
                success: function (response) {
                    Swal.close(); // Cierra el mensaje de carga
                    console.log('Respuesta del servidor:', response);
                    if (response && response.success) {
                        mostrarChat(response.mensajes);
                        // En lugar de cargarSolicitudes(); en la línea 508, pon:
                       //actualizarBadgesSilenciosamente();
                    } else {
                        console.error("Error en respuesta:", response?.error);
                        Swal.fire('Error', response?.error || 'Error al cargar mensajes', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    Swal.close(); // También cerrar si falla
                    console.error('Error al cargar chat:', xhr.responseText);
                    Swal.fire('Error', 'Error al cargar el chat: ' + error, 'error');
                }
            });
        }

        cargarMensajesChat(idHistorico);
        actualizarBadgesSilenciosamente();
        modalAbierto = false;
        });

    // FUNCIÓN PARA ACTUALIZAR SOLO LOS BADGES SIN RUIDO VISUAL
function actualizarBadgesSilenciosamente() {
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=get_solicitudes',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            const nuevasSolicitudes = data.success ? data.data : data;
            
            // Actualizar solo los badges sin recargar la tabla
            nuevasSolicitudes.forEach(function(solicitud) {
                const fila = $(`tr[data-id="${solicitud.ID_SOLICITUD}"]`);
                if (fila.length > 0) {
                    const noLeidos = parseInt(solicitud.NO_LEIDOS) || 0;
                    const badge = fila.find('.notification-badge');
                    
                    if (noLeidos > 0) {
                        // Actualizar badge existente o crear uno nuevo
                        if (badge.length > 0) {
                            badge.text(noLeidos);
                        } else {
                            const btnComentario = fila.find('.btnVerComentarioSuper').parent();
                            btnComentario.append(`<span class="notification-badge">${noLeidos}</span>`);
                        }
                    } else {
                        // Remover badge si no hay mensajes no leídos
                        badge.fadeOut(300, function() { $(this).remove(); });
                    }
                }
            });
        }
    });
}

      // FUNCIÓN PARA CREAR SOLICITUD
// FUNCIÓN PARA CREAR SOLICITUD
      $('.btnCrearsolicitud').click(function () {
        Swal.fire({
            title: 'Crear Nueva Solicitud de Personal',
            html: `
                <div style="text-align: left; max-width: 600px;">
                    <!-- Información de Seguridad -->
                    <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <i class="fas fa-shield-alt" style="font-size: 18px; margin-right: 8px; color: #856404;"></i>
                            <strong style="color: #856404;">Control de Acceso</strong>
                        </div>
                        <p style="margin: 0; font-size: 13px; color: #856404;">
                            Solo supervisores autorizados pueden crear solicitudes de personal.
                            <br><a href="#" id="ver_supervisores" style="color: #007bff;">
                                <i class="fas fa-users"></i> Ver lista de supervisores válidos
                            </a>
                        </p>
                    </div>

                    <!-- Paso 1: Búsqueda de Empleado -->
                    <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <h4 style="margin: 0 0 10px 0; color: #1976d2;">
                            <i class="fas fa-user"></i> 1. Información del Solicitante
                        </h4>
                        <div style="display: flex; gap: 10px; align-items: end;">
                            <div style="flex: 1;">
                                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Código de Supervisor:</label>
                                <input type="text" id="empleado_codigo" class="swal2-input" placeholder="Ej: 5226, 5287, 5333..." style="margin: 0;">
                                <small style="color: #666; font-size: 12px;">Solo códigos de supervisores autorizados</small>
                            </div>
                            <button id="buscar_empleado" style="padding: 10px 15px; background: #1976d2; color: white; border: none; border-radius: 4px; cursor: pointer;">
                                <i class="fas fa-search"></i> Verificar
                            </button>
                        </div>
                        <div id="empleado_info" style="margin-top: 10px; display: none;"></div>
                        <div id="error_info" style="margin-top: 10px; display: none;"></div>
                    </div>

                    <!-- Paso 2: Campo Gerente -->
                    <div class="form-step" id="paso-2" style="background: #e8f5e8; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #2e7d32;">
                            <i class="fas fa-paper-plane"></i> 2. Gerente
                        </h4>
                        <div class="form-group">
                            <label style="display: block; font-weight: bold; margin-bottom: 5px;">Gerente:</label>
                            <select class="swal2-input" id="dirigido_a" name="dirigido_a" required style="margin: 0;">
                                <option value="">Seleccione destinatario</option>
                                <option value="Christian Quan">Christian Quan</option>
                                <option value="Giovanni Cardoza">Giovanni Cardoza</option>
                            </select>
                        </div>
                    </div>

                    <!-- Paso 3: Selección de Tienda -->
                    <div id="tienda_section" style="background: #f3e5f5; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #7b1fa2;">
                            <i class="fas fa-store"></i> 3. Tienda que Necesita Personal
                        </h4>
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Selecciona la tienda:</label>
                        <select id="tienda_select" class="swal2-input" style="margin: 0;">
                            <option value="">Cargando tiendas...</option>
                        </select>
                    </div>

                    <!-- Paso 4: Tipo de Vacante -->
                    <div id="puesto_section" style="background: #fff3e0; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #f57c00;">
                            <i class="fas fa-briefcase"></i> 4. Tipo de Vacante
                        </h4>
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">Puesto a solicitar:</label>
                        <select id="puesto_select" class="swal2-input" style="margin: 0;">
                            <option value="">Selecciona el puesto...</option>
                            <option value="JEFE DE TIENDA">Jefe de Tienda</option>
                            <option value="SUB JEFE DE TIENDA">Sub Jefe de Tienda</option>
                            <option value="ASESOR DE VENTAS">Asesor de Ventas</option>
                            <option value="VACACIONISTA">Vacacionista</option>
                            <option value="CAJERO">Cajero</option>
                            <option value="TEMPORAL POR VACACIONES">Temporal por vacaciones</option>
                            <option value="TEMPORAL POR MATERNIDAD">Temporal por maternidad</option>
                        </select>
                    </div>

                    <!-- Paso 5: Razón de la Vacante -->
                    <div id="razon_section" style="background: #ffebee; padding: 15px; border-radius: 8px; margin-bottom: 15px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #c62828;">
                            <i class="fas fa-edit"></i> 5. Razón de la Vacante
                        </h4>
                        <label style="display: block; font-weight: bold; margin-bottom: 5px;">¿Por qué se necesita esta vacante?</label>
                        <select id="razon_select" class="swal2-input" style="margin: 0;">
                            <option value="">Selecciona la razón...</option>
                            <option value="Renuncia Voluntaria">Renuncia Voluntaria</option>
                            <option value="Despido por Causa Justa">Despido por Causa Justa</option>
                            <option value="Cubre Vacaciones">Cubre Vacaciones</option>
                            <option value="Personal Interino por Maternidad">Personal Interino por Maternidad</option>
                            <option value="Abandono de Trabajo">Abandono de Trabajo</option>
                            <option value="Vencimiento de Contrato">Vencimiento de Contrato</option>
                            <option value="Promoción Interna">Promoción Interna</option>
                            <option value="Traslado a Otra Tienda">Traslado a Otra Tienda</option>
                            <option value="Incapacidad Permanente">Incapacidad Permanente</option>
                            <option value="Jubilación">Jubilación</option>
                            <option value="Nueva Posición">Nueva Posición</option>
                            <option value="Aumento de Personal">Aumento de Personal</option>
                            <option value="Temporada Alta">Temporada Alta</option>
                        </select>
                    </div>

                    <!-- Resumen -->
                    <div id="resumen_section" style="background: #f5f5f5; padding: 15px; border-radius: 8px; display: none;">
                        <h4 style="margin: 0 0 10px 0; color: #424242;">
                            <i class="fas fa-clipboard-list"></i> Resumen de la Solicitud
                        </h4>
                        <div id="resumen_content"></div>
                    </div>
                </div>
            `,
            width: '700px',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-plus-circle"></i> Crear Solicitud',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            showConfirmButton: false,
            didOpen: () => {
                // Store empleadoData in window scope
                window.empleadoData = null;
                // Store reference to main modal
                window.mainModal = Swal.getPopup();

                // Ver supervisores válidos
                $('#ver_supervisores').click(function(e) {
                    e.preventDefault();
                    
                    // CRITICAL FIX: Use mixin to create independent modal
                    const SupervisorModal = Swal.mixin({
                        customClass: {
                            container: 'supervisor-modal-container'
                        },
                        backdrop: 'rgba(0,0,0,0.4)'
                    });
                    
                    SupervisorModal.fire({
                        title: '<i class="fas fa-spinner fa-spin"></i> Cargando supervisores...',
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });
                    
                    $.ajax({
                        url: './supervision/crudsolicitudes.php?action=get_valid_supervisors',
                        method: 'GET',
                        dataType: 'json',
                        timeout: 30000,
                        success: function(supervisors) {
                            if (!supervisors || supervisors.length === 0) {
                                SupervisorModal.fire({
                                    icon: 'info',
                                    title: 'Sin Datos',
                                    text: 'No se encontraron supervisores en el sistema',
                                    confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                                    confirmButtonColor: '#6c757d'
                                });
                                return;
                            }
                            
                            let lista = '<div style="max-height: 300px; overflow-y: auto;">';
                            lista += '<table style="width: 100%; border-collapse: collapse; font-size: 13px;">';
                            lista += '<thead><tr style="background: #f8f9fa;">';
                            lista += '<th style="padding: 12px; border: 1px solid #dee2e6; text-align: center;"><i class="fas fa-hashtag"></i> Código</th>';
                            lista += '<th style="padding: 12px; border: 1px solid #dee2e6;"><i class="fas fa-user"></i> Nombre</th>';
                            lista += '</tr></thead><tbody>';
                            
                            supervisors.forEach(sup => {
                                lista += `<tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 10px; border: 1px solid #dee2e6; text-align: center; font-weight: bold; color: #007bff;">${sup.codigo}</td>
                                    <td style="padding: 10px; border: 1px solid #dee2e6;">${sup.nombre}</td>
                                </tr>`;
                            });
                            
                            lista += '</tbody></table></div>';
                            
                            SupervisorModal.fire({
                                title: '<i class="fas fa-users"></i> Supervisores Autorizados',
                                html: lista,
                                width: '600px',
                                confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                                confirmButtonColor: '#6c757d',
                                // CRITICAL: This ensures only the supervisor modal closes
                                willClose: () => {
                                    // Return focus to main modal without closing it
                                    if (window.mainModal) {
                                        window.mainModal.focus();
                                    }
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error cargando supervisores:', error);
                            SupervisorModal.fire({
                                icon: 'error',
                                title: '<i class="fas fa-exclamation-triangle"></i> Error',
                                text: 'No se pudo cargar la lista de supervisores.',
                                confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                });

                // Buscar empleado
                $('#buscar_empleado').click(function() {
                    const codigo = $('#empleado_codigo').val().trim();
                    if (!codigo) {
                        mostrarError('<i class="fas fa-exclamation-circle"></i> Ingresa un código de empleado', 'warning');
                        return;
                    }

                    $(this).html('<i class="fas fa-spinner fa-spin"></i> Verificando...').prop('disabled', true);
                    $('#empleado_info').hide();
                    $('#error_info').hide();

                    $.ajax({
                        url: './supervision/crudsolicitudes.php?action=search_employee&codigo=' + codigo,
                        method: 'GET',
                        dataType: 'json',
                        timeout: 10000,
                        success: function(data) {
                            if (data.error) {
                                if (data.error === 'ACCESO DENEGADO') {
                                    mostrarError(`
                                        <div style="text-align: center;">
                                            <div style="font-size: 48px; margin-bottom: 15px; color: #dc3545;">
                                                <i class="fas fa-ban"></i>
                                            </div>
                                            <strong style="color: #dc3545; font-size: 18px;">ACCESO DENEGADO</strong>
                                            <p style="margin: 15px 0; font-size: 14px;">
                                                El código <strong>${data.codigo_ingresado}</strong> corresponde a:
                                                <br><strong>${data.nombre_empleado}</strong>
                                            </p>
                                            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #ffc107;">
                                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                                    <i class="fas fa-exclamation-triangle" style="color: #856404; margin-right: 8px;"></i>
                                                    <strong style="color: #856404;">Este empleado NO es supervisor</strong>
                                                </div>
                                                <span style="font-size: 12px; color: #856404;">
                                                    Solo supervisores con tiendas a cargo pueden crear solicitudes
                                                </span>
                                            </div>
                                        </div>
                                    `, 'error');
                                } else if (data.error === 'EMPLEADO NO ENCONTRADO') {
                                    mostrarError(`
                                        <div style="text-align: center;">
                                            <div style="font-size: 48px; margin-bottom: 15px; color: #6c757d;">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <strong style="color: #dc3545; font-size: 18px;">EMPLEADO NO ENCONTRADO</strong>
                                            <p style="margin: 15px 0; font-size: 14px;">
                                                El código <strong>${data.codigo_ingresado}</strong> no existe en el sistema.
                                            </p>
                                        </div>
                                    `, 'error');
                                } else {
                                    mostrarError('<i class="fas fa-times-circle"></i> ' + data.error, 'error');
                                }
                            } else {
                                window.empleadoData = data;
                                mostrarInfoEmpleado(data);
                                cargarTiendas(data.tiendas);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error buscando empleado:', error);
                            mostrarError('<i class="fas fa-wifi"></i> Error de conexión con el servidor', 'error');
                        },
                        complete: function() {
                            $('#buscar_empleado').html('<i class="fas fa-search"></i> Verificar').prop('disabled', false);
                        }
                    });
                });

                function mostrarError(mensaje, tipo) {
                    const colors = {
                        'error': '#dc3545',
                        'warning': '#ffc107', 
                        'info': '#17a2b8'
                    };
                    const color = colors[tipo] || '#6c757d';
                    
                    $('#error_info').html(`
                        <div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid ${color}; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            ${mensaje}
                        </div>
                    `).show();
                    $('#paso-2').hide();
                    $('#tienda_section').hide();
                    $('#puesto_section').hide();
                    $('#razon_section').hide();
                    $('#resumen_section').hide();
                }

                function mostrarInfoEmpleado(data) {
                    $('#empleado_info').html(`
                        <div style="background: white; padding: 20px; border-radius: 8px; border: 2px solid #28a745; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-check-circle" style="font-size: 24px; color: #28a745; margin-right: 10px;"></i>
                                <strong style="color: #155724; font-size: 16px;">SUPERVISOR AUTORIZADO</strong>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-hashtag" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span><strong>Código:</strong> ${data.codigo}</span>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-user" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span><strong>Nombre:</strong> ${data.nombre}</span>
                                </div>
                            </div>
                        </div>
                    `).show();
                    $('#paso-2').show();
                    $('#error_info').hide();
                }

                function cargarTiendas(tiendas) {
                    let options = '<option value="">Selecciona una tienda...</option>';
                    tiendas.forEach(tienda => {
                        options += `<option value="${tienda}">Tienda ${tienda}</option>`;
                    });
                    $('#tienda_select').html(options);
                }

                // Eventos de cambio
                $('#dirigido_a').change(function() {
                    if ($(this).val()) {
                        $('#tienda_section').show();
                    }
                });

                $('#tienda_select').change(function() {
                    if ($(this).val()) {
                        $('#puesto_section').show();
                    }
                });

                $('#puesto_select').change(function() {
                    if ($(this).val()) {
                        $('#razon_section').show();
                    }
                });

                $('#razon_select').change(function() {
                    if ($(this).val()) {
                        mostrarResumen();
                        $('.swal2-confirm').show();
                    }
                });

                function mostrarResumen() {
                    const tienda = $('#tienda_select').val();
                    const puesto = $('#puesto_select').val();
                    const razon = $('#razon_select').val();
                    const dirigidoA = $('#dirigido_a').val();

                    if (!window.empleadoData) {
                        console.error('Error: empleadoData no está definido');
                        mostrarError('<i class="fas fa-exclamation-triangle"></i> Error interno: datos del empleado no disponibles', 'error');
                        return;
                    }

                    $('#resumen_content').html(`
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 14px;">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                                <h5 style="margin: 0 0 10px 0; color: #495057; display: flex; align-items: center;">
                                    <i class="fas fa-user-tie" style="margin-right: 8px;"></i>
                                    Supervisor
                                </h5>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-hashtag" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Código:</strong> ${window.empleadoData.codigo}
                                </div>
                                <div>
                                    <i class="fas fa-user" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Nombre:</strong> ${window.empleadoData.nombre}
                                </div>
                            </div>
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                                <h5 style="margin: 0 0 10px 0; color: #495057; display: flex; align-items: center;">
                                    <i class="fas fa-clipboard-list" style="margin-right: 8px;"></i>
                                    Solicitud
                                </h5>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-paper-plane" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Gerente:</strong> ${dirigidoA}
                                </div>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-store" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Tienda:</strong> ${tienda}
                                </div>
                                <div style="margin-bottom: 8px;">
                                    <i class="fas fa-briefcase" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Vacante:</strong> ${puesto}
                                </div>
                                <div>
                                    <i class="fas fa-edit" style="color: #6c757d; margin-right: 6px; width: 14px;"></i>
                                    <strong>Razón:</strong> ${razon}
                                </div>
                            </div>
                        </div>
                    `);
                    $('#resumen_section').show();
                }

            },
           preConfirm: () => {
                const tienda = $('#tienda_select').val();
                const puesto = $('#puesto_select').val();
                const razon = $('#razon_select').val();
                const dirigidoA = $('#dirigido_a').val();

                if (!window.empleadoData) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Error: datos del empleado no disponibles');
                    return false;
                }

                if (!tienda || !puesto || !razon || !dirigidoA) {
                    Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Completa todos los campos');
                    return false;
                }

                if (!window.empleadoData.es_supervisor) {
                    Swal.showValidationMessage('<i class="fas fa-ban"></i> Solo supervisores autorizados pueden crear solicitudes');
                    return false;
                }

                return {
                    empleado_codigo: window.empleadoData.codigo,
                    empleado_nombre: window.empleadoData.nombre,
                    tienda_no: tienda,
                    puesto_solicitado: puesto,
                    razon_vacante: razon,
                    dirigido_a: dirigidoA
                };
            }

        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                
                Swal.fire({
                    title: '<i class="fas fa-spinner fa-spin"></i> Creando solicitud...',
                    text: 'Por favor espera mientras se procesa la solicitud',
                    allowOutsideClick: false,
                    showConfirmButton: false
                });
                
                $.ajax({
                    url: './supervision/crudsolicitudes.php?action=create_advanced_solicitud',
                    type: 'POST',
                    data: data,
                    timeout: 10000,
                    success: function (response) {
                        console.log('Raw response:', response);
                        
                        let res;
                        try {
                            if (typeof response === 'string') {
                                res = JSON.parse(response);
                            } else {
                                res = response;
                            }
                        } catch (e) {
                            console.log('JSON parse failed, checking for success indicators');
                            const responseStr = String(response);
                            
                            if (responseStr.includes('success') || responseStr.includes('Solicitud creada exitosamente')) {
                                res = { success: true, message: 'Solicitud creada exitosamente' };
                            } else {
                                res = { success: false, error: 'Respuesta inválida del servidor' };
                            }
                        }
                        
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '<i class="fas fa-check-circle"></i> ¡Éxito!',
                                text: res.message || 'Solicitud creada correctamente',
                                confirmButtonText: '<i class="fas fa-check"></i> Entendido'
                            });
                            if (typeof cargarSolicitudes === 'function') {
                                cargarSolicitudes();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<i class="fas fa-times-circle"></i> Error',
                                text: res.error || 'Error al crear solicitud',
                                confirmButtonText: '<i class="fas fa-check"></i> Entendido'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error creando solicitud:', error);
                        Swal.fire({
                            icon: 'error',
                            title: '<i class="fas fa-wifi"></i> Error de Conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonText: '<i class="fas fa-check"></i> Entendido'
                        });
                    }
                });
            }
            
            // Clear the global variables when modal closes
            window.empleadoData = null;
            window.mainModal = null;
        });
      });





// FUNCIÓN PARA EDITAR SOLICITUD
$(document).off('click', '.btnEditarSolicitud').on('click', '.btnEditarSolicitud', function (e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    const index = $(this).data('index');
    const solicitud = solicitudes[index];

    // First, get the supervisor's stores to populate the dropdown
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando información...',
        text: 'Obteniendo tiendas del supervisor',
        allowOutsideClick: false,
        showConfirmButton: false
    });

    // Get supervisor's stores based on who created the original request
    $.ajax({
        url: './supervision/crudSolicitudes.php?action=get_supervisor_stores',
        method: 'GET',
        data: { solicitado_por: solicitud.SOLICITADO_POR },
        dataType: 'json',
        timeout: 10000,
        success: function(supervisorData) {
            // Now show the edit modal with the supervisor's stores
            Swal.fire({
                title: '<i class="fas fa-edit"></i> Editar Solicitud de Personal',
                html: `
                    <div style="text-align: left; max-width: 650px;">
                        <!-- Header Information -->
                        <div style="background: #e3f2fd; border: 1px solid #bbdefb; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-info-circle" style="font-size: 20px; margin-right: 10px; color: #1976d2;"></i>
                                <strong style="color: #1976d2; font-size: 16px;">Información de la Solicitud</strong>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-hashtag" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span style="color: #333;"><strong>ID:</strong> ${solicitud.ID_SOLICITUD}</span>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-calendar" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span style="color: #333;"><strong>Fecha:</strong> ${solicitud.FECHA_SOLICITUD}</span>
                                </div>
                                <div style="display: flex; align-items: center; grid-column: 1 / -1;">
                                    <i class="fas fa-user-tie" style="color: #6c757d; margin-right: 8px; width: 16px;"></i>
                                    <span style="color: #333;"><strong>Solicitado por:</strong> ${solicitud.SOLICITADO_POR}</span>
                                    <span style="margin-left: 10px; padding: 2px 8px; background: #f8f9fa; border-radius: 12px; font-size: 12px; color: #6c757d;">
                                        <i class="fas fa-lock" style="margin-right: 4px;"></i>No editable
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Supervisor Info -->
                        <div style="background: #e8f5e8; border: 1px solid #c8e6c9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-user-shield" style="font-size: 16px; margin-right: 8px; color: #2e7d32;"></i>
                                <strong style="color: #2e7d32; font-size: 14px;">Supervisor: ${supervisorData.nombre}</strong>
                            </div>
                            <div style="font-size: 12px; color: #4caf50;">
                                <i class="fas fa-store" style="margin-right: 5px;"></i>
                                Tiendas a cargo: ${supervisorData.tiendas.length} tienda(s)
                            </div>
                        </div>

                        <!-- Editable Fields Section -->
                        <div style="background: #fff3e0; border: 1px solid #ffcc02; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 20px;">
                                <i class="fas fa-edit" style="font-size: 18px; margin-right: 10px; color: #f57c00;"></i>
                                <strong style="color: #f57c00; font-size: 16px;">Campos Editables</strong>
                            </div>

                            <!-- Store Field -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-store" style="color: #2e7d32; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #2e7d32;">Tienda:</label>
                                </div>
                                <select 
                                    id="tienda_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona una tienda...</option>
                                    ${supervisorData.tiendas.map(tienda => 
                                        `<option value="${tienda}" style="color: #333;" ${tienda === solicitud.NUM_TIENDA ? 'selected' : ''}>Tienda ${tienda}</option>`
                                    ).join('')}
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Selecciona una de las tiendas asignadas al supervisor
                                </small>
                            </div>

                            <!-- Position Field -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-briefcase" style="color: #7b1fa2; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #7b1fa2;">Puesto Solicitado:</label>
                                </div>
                                <select 
                                    id="puesto_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona el puesto...</option>
                                    <option value="JEFE DE TIENDA" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'JEFE DE TIENDA' ? 'selected' : ''}>Jefe de Tienda</option>
                                    <option value="SUB JEFE DE TIENDA" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'SUB JEFE DE TIENDA' ? 'selected' : ''}>Sub Jefe de Tienda</option>
                                    <option value="ASESOR DE VENTAS" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'ASESOR DE VENTAS' ? 'selected' : ''}>Asesor de Ventas</option>
                                    <option value="VACACIONISTA" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'VACACIONISTA' ? 'selected' : ''}>Vacacionista</option>
                                    <option value="CAJERO" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'CAJERO' ? 'selected' : ''}>Cajero</option>
                                    <option value="TEMPORAL POR VACACIONES" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'TEMPORAL POR VACACIONES' ? 'selected' : ''}>Temporal por vaciones</option>
                                    <option value="TEMPORAL POR MATERNIDAD" style="color: #333;" ${solicitud.PUESTO_SOLICITADO === 'TEMPORAL POR MATERNIDAD' ? 'selected' : ''}>Temporal por maternidad</option>
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Selecciona el tipo de puesto que se necesita cubrir
                                </small>
                            </div>

                            <!-- Reason Field -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-clipboard-list" style="color: #d32f2f; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #d32f2f;">Razón de la Vacante:</label>
                                </div>
                                <select 
                                    id="razon_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona la razón...</option>
                                    <option value="Renuncia Voluntaria" style="color: #333;" ${solicitud.RAZON === 'Renuncia Voluntaria' ? 'selected' : ''}>Renuncia Voluntaria</option>
                                    <option value="Despido por Causa Justa" style="color: #333;" ${solicitud.RAZON === 'Despido por Causa Justa' ? 'selected' : ''}>Despido por Causa Justa</option>
                                    <option value="Despido sin Causa Justa" style="color: #333;" ${solicitud.RAZON === 'Despido sin Causa Justa' ? 'selected' : ''}>Despido sin Causa Justa</option>
                                    <option value="Abandono de Trabajo" style="color: #333;" ${solicitud.RAZON === 'Abandono de Trabajo' ? 'selected' : ''}>Abandono de Trabajo</option>
                                    <option value="Vencimiento de Contrato" style="color: #333;" ${solicitud.RAZON === 'Vencimiento de Contrato' ? 'selected' : ''}>Vencimiento de Contrato</option>
                                    <option value="Promoción Interna" style="color: #333;" ${solicitud.RAZON === 'Promoción Interna' ? 'selected' : ''}>Promoción Interna</option>
                                    <option value="Traslado a Otra Tienda" style="color: #333;" ${solicitud.RAZON === 'Traslado a Otra Tienda' ? 'selected' : ''}>Traslado a Otra Tienda</option>
                                    <option value="Incapacidad Permanente" style="color: #333;" ${solicitud.RAZON === 'Incapacidad Permanente' ? 'selected' : ''}>Incapacidad Permanente</option>
                                    <option value="Jubilación" style="color: #333;" ${solicitud.RAZON === 'Jubilación' ? 'selected' : ''}>Jubilación</option>
                                    <option value="Nueva Posición" style="color: #333;" ${solicitud.RAZON === 'Nueva Posición' ? 'selected' : ''}>Nueva Posición</option>
                                    <option value="Aumento de Personal" style="color: #333;" ${solicitud.RAZON === 'Aumento de Personal' ? 'selected' : ''}>Aumento de Personal</option>
                                    <option value="Temporada Alta" style="color: #333;" ${solicitud.RAZON === 'Temporada Alta' ? 'selected' : ''}>Temporada Alta</option>
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Especifica el motivo por el cual se necesita cubrir esta vacante
                                </small>
                            </div>

                            <!-- Gerente Field -->
                            <div style="margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-user-check" style="color: #1976d2; margin-right: 8px;"></i>
                                    <label style="font-weight: bold; color: #1976d2;">Gerente:</label>
                                </div>
                                <select 
                                    id="dirigido_a_edit" 
                                    class="swal2-input"
                                    style="margin: 0; border: 2px solid #e0e0e0; border-radius: 6px; padding: 12px; font-size: 16px; background: white; color: #333; width: 100%; box-sizing: border-box; height: 50px;"
                                >
                                    <option value="" style="color: #666;">Selecciona personal de RRHH...</option>
                                    <option value="Christian Quan" style="color: #333;" ${solicitud.DIRIGIDO_A === 'Christian Quan' ? 'selected' : ''}>Christian Quan</option>
                                    <option value="Giovanni Cardoza" style="color: #333;" ${solicitud.DIRIGIDO_A === 'Giovanni Cardoza' ? 'selected' : ''}>Giovanni Cardoza</option>
                                </select>
                                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">
                                    <i class="fas fa-info-circle"></i> Selecciona la persona de RRHH que procesará esta solicitud
                                </small>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div id="edit_summary" style="background: #f5f5f5; border: 1px solid #e0e0e0; padding: 20px; border-radius: 8px; display: none;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-clipboard-check" style="font-size: 18px; margin-right: 10px; color: #424242;"></i>
                                <strong style="color: #424242; font-size: 16px;">Resumen de Cambios</strong>
                            </div>
                            <div id="changes_content"></div>
                        </div>
                    </div>
                `,
                width: '750px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save"></i> Guardar Cambios',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                didOpen: () => {
                    // Add CSS to ensure proper styling of select elements with larger font
                    const style = document.createElement('style');
                    style.textContent = `
                        .swal2-container select {
                            background: white !important;
                            color: #333 !important;
                            border: 2px solid #e0e0e0 !important;
                            border-radius: 6px !important;
                            padding: 12px !important;
                            font-size: 16px !important;
                            width: 100% !important;
                            box-sizing: border-box !important;
                            height: 50px !important;
                            appearance: menulist !important;
                            -webkit-appearance: menulist !important;
                            -moz-appearance: menulist !important;
                        }
                        
                        .swal2-container select option {
                            background: white !important;
                            color: #333 !important;
                            padding: 10px !important;
                            font-size: 16px !important;
                            line-height: 1.4 !important;
                        }
                        
                        .swal2-container select option:hover {
                            background: #f0f0f0 !important;
                            color: #333 !important;
                        }
                        
                        .swal2-container select:focus {
                            border-color: #007bff !important;
                            outline: none !important;
                            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25) !important;
                        }
                    `;
                    document.head.appendChild(style);

                    // Add real-time validation and summary update
                    function updateSummary() {
                        const tienda = $('#tienda_edit').val();
                        const puesto = $('#puesto_edit').val();
                        const razon = $('#razon_edit').val();
                        const dirigidoA = $('#dirigido_a_edit').val();

                        if (tienda && puesto && razon && dirigidoA) {
                            const changes = [];
                            
                            if (tienda !== solicitud.NUM_TIENDA) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-store" style="color: #2e7d32; margin-right: 6px;"></i><strong>Tienda:</strong> ${solicitud.NUM_TIENDA} → ${tienda}</div>`);
                            }
                            
                            if (puesto !== solicitud.PUESTO_SOLICITADO) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-briefcase" style="color: #7b1fa2; margin-right: 6px;"></i><strong>Puesto:</strong> ${solicitud.PUESTO_SOLICITADO} → ${puesto}</div>`);
                            }
                            
                            if (razon !== solicitud.RAZON) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-clipboard-list" style="color: #d32f2f; margin-right: 6px;"></i><strong>Razón:</strong> ${solicitud.RAZON} → ${razon}</div>`);
                            }

                            if (dirigidoA !== solicitud.DIRIGIDO_A) {
                                changes.push(`<div style="margin-bottom: 8px; color: #333;"><i class="fas fa-user-check" style="color: #1976d2; margin-right: 6px;"></i><strong>Gerente:</strong> ${solicitud.DIRIGIDO_A || 'Sin asignar'} → ${dirigidoA}</div>`);
                            }

                            if (changes.length > 0) {
                                $('#changes_content').html(`
                                    <div style="background: #fff3cd; padding: 15px; border-radius: 6px; border-left: 4px solid #ffc107;">
                                        <div style="font-weight: bold; margin-bottom: 10px; color: #856404;">
                                            <i class="fas fa-exclamation-triangle" style="margin-right: 6px;"></i>
                                            Se detectaron ${changes.length} cambio(s):
                                        </div>
                                        ${changes.join('')}
                                    </div>
                                `);
                                $('#edit_summary').show();
                            } else {
                                $('#changes_content').html(`
                                    <div style="background: #d1ecf1; padding: 15px; border-radius: 6px; border-left: 4px solid #bee5eb; text-align: center;">
                                        <i class="fas fa-info-circle" style="color: #0c5460; margin-right: 6px;"></i>
                                        <span style="color: #0c5460;">No se han realizado cambios</span>
                                    </div>
                                `);
                                $('#edit_summary').show();
                            }
                        } else {
                            $('#edit_summary').hide();
                        }
                    }

                    // Add event listeners for real-time updates
                    $('#tienda_edit, #puesto_edit, #razon_edit, #dirigido_a_edit').on('input change', updateSummary);
                    
                    // Initial summary update
                    updateSummary();

                    // Add visual feedback for field changes
                    $('#tienda_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.NUM_TIENDA;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });

                    $('#puesto_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.PUESTO_SOLICITADO;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });

                    $('#razon_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.RAZON;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });

                    $('#dirigido_a_edit').on('change', function() {
                        const isChanged = $(this).val() !== solicitud.DIRIGIDO_A;
                        $(this).css('border-color', isChanged ? '#ffc107' : '#e0e0e0');
                    });
                },
                preConfirm: () => {
                    const tienda = $('#tienda_edit').val();
                    const puesto = $('#puesto_edit').val();
                    const razon = $('#razon_edit').val();
                    const dirigidoA = $('#dirigido_a_edit').val();

                    // Validation
                    if (!tienda) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar una tienda');
                        return false;
                    }

                    if (!puesto) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar un puesto');
                        return false;
                    }

                    if (!razon) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar una razón para la vacante');
                        return false;
                    }

                    if (!dirigidoA) {
                        Swal.showValidationMessage('<i class="fas fa-exclamation-triangle"></i> Debes seleccionar a quién dirigir la solicitud');
                        return false;
                    }

                    // Check if any changes were made
                    const hasChanges = tienda !== solicitud.NUM_TIENDA || 
                                     puesto !== solicitud.PUESTO_SOLICITADO || 
                                     razon !== solicitud.RAZON ||
                                     dirigidoA !== solicitud.DIRIGIDO_A;

                    if (!hasChanges) {
                        Swal.showValidationMessage('<i class="fas fa-info-circle"></i> No se han realizado cambios para guardar');
                        return false;
                    }

                    return {
                        tienda_no: tienda,
                        puesto: puesto,
                        razon: razon,
                        dirigido_a: dirigidoA
                    };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    const updatedData = result.value;

                    // Show loading state
                    Swal.fire({
                        title: '<i class="fas fa-spinner fa-spin"></i> Actualizando solicitud...',
                        text: 'Por favor espera mientras se guardan los cambios',
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    $.ajax({
                        url: './supervision/crudSolicitudes.php?action=update_solicitud',
                        type: 'POST',
                        data: {
                            id_solicitud: solicitud.ID_SOLICITUD,
                            tienda_no: updatedData.tienda_no,
                            puesto: updatedData.puesto,
                            razon: updatedData.razon,
                            dirigido_a: updatedData.dirigido_a
                        },
                        timeout: 10000,
                        success: function (response) {
                            console.log('Raw response:', response);
                            
                            let res;
                            try {
                                if (typeof response === 'string') {
                                    res = JSON.parse(response);
                                } else {
                                    res = response;
                                }
                            } catch (e) {
                                console.log('JSON parse failed, checking for success indicators');
                                const responseStr = String(response);
                                
                                if (responseStr.includes('success') || responseStr.includes('actualizada') || responseStr.includes('modificada')) {
                                    res = { success: true, message: 'Solicitud actualizada correctamente' };
                                } else {
                                    res = { success: false, error: 'Respuesta inválida del servidor' };
                                }
                            }
                            
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '<i class="fas fa-check-circle"></i> ¡Cambios Guardados!',
                                    html: `
                                        <div style="text-align: center;">
                                            <p style="margin-bottom: 15px; color: #333;">${res.message || 'La solicitud ha sido actualizada correctamente.'}</p>
                                            <div style="background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;">
                                                <div style="font-weight: bold; margin-bottom: 8px; color: #155724;">
                                                    <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                                                    Cambios aplicados:
                                                </div>
                                                <div style="font-size: 14px; color: #155724;">
                                                    <div><strong>Tienda:</strong> ${updatedData.tienda_no}</div>
                                                    <div><strong>Puesto:</strong> ${updatedData.puesto}</div>
                                                    <div><strong>Razón:</strong> ${updatedData.razon}</div>
                                                    <div><strong>Gerente:</strong> ${updatedData.dirigido_a}</div>
                                                </div>
                                            </div>
                                        </div>
                                    `,
                                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                                    confirmButtonColor: '#28a745'
                                });
                                
                                // Reload the requests table
                                if (typeof cargarSolicitudes === 'function') {
                                    cargarSolicitudes();
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '<i class="fas fa-times-circle"></i> Error al Actualizar',
                                    text: res.error || 'No se pudo actualizar la solicitud. Intenta nuevamente.',
                                    confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error updating request:', error);
                            Swal.fire({
                                icon: 'error',
                                title: '<i class="fas fa-wifi"></i> Error de Conexión',
                                text: 'No se pudo conectar con el servidor. Verifica tu conexión e intenta nuevamente.',
                                confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading supervisor stores:', error);
            Swal.fire({
                icon: 'error',
                title: '<i class="fas fa-exclamation-triangle"></i> Error',
                text: 'No se pudieron cargar las tiendas del supervisor.',
                confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                confirmButtonColor: '#dc3545'
            });
        }
    });
});

//HISTORIAL DE MODIFICACIONES
//HISTORIAL DE MODIFICACIONES
$(document).off('click', '.btn-ver-historial-modificaciones').on('click', '.btn-ver-historial-modificaciones', function () {
  const idSolicitud = $(this).data('id');

  if (!Number.isInteger(Number(idSolicitud))) {
    Swal.fire('Error', 'ID de solicitud inválido.', 'error');
    return;
  }

  $('#btnPdfIndividual').attr('href', './supervision/reporte_historial_pdf.php?id=' + idSolicitud);
  $('#modalHistorialIndividual').modal('show');
  $('#contenidoHistorial').html('<div class="text-center">Cargando historial de modificaciones...</div>');

  $.ajax({
    url: './supervision/crudsolicitudes.php?action=get_historial_edicion&id=' + idSolicitud,
    method: 'GET',
    success: function (datos) {
      if (datos.length === 0) {
        $('#contenidoHistorial').html('<div class="text-center text-muted">No hay historial de modificaciones para esta solicitud.</div>');
        return;
      }

      let html = `
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="thead-dark">
              <tr>
                <th>#</th>
                <th>Campo Modificado</th>
                <th>Valor Anterior</th>
                <th>Valor Nuevo</th>
                <th>Fecha de Cambio</th>
              </tr>
            </thead>
            <tbody>`;

      datos.forEach((h, index) => {
        // Formatear el nombre del campo para que sea más legible
        let campoFormateado = h.CAMPO_MODIFICADO;
        switch(h.CAMPO_MODIFICADO) {
          case 'NUM_TIENDA':
            campoFormateado = 'Número de Tienda';
            break;
          case 'PUESTO_SOLICITADO':
            campoFormateado = 'Puesto Solicitado';
            break;
          case 'RAZON':
            campoFormateado = 'Razón de la Vacante';
            break;
          case 'DIRIGIDO_A':
            campoFormateado = 'Gerente';
            break;
          default:
            campoFormateado = h.CAMPO_MODIFICADO;
        }

        // Formatear valores para mejor visualización
        const valorAnterior = h.VALOR_ANTERIOR || '<em style="color: #666;">Sin valor</em>';
        const valorNuevo = h.VALOR_NUEVO || '<em style="color: #666;">Sin valor</em>';

        html += `<tr>
          <td>${index + 1}</td>
          <td><strong>${campoFormateado}</strong></td>
          <td>${valorAnterior}</td>
          <td><span style="color: #28a745; font-weight: bold;">${valorNuevo}</span></td>
          <td>${h.FECHA_CAMBIO}</td>
        </tr>`;
      });

      html += '</tbody></table></div>';
      
      // Agregar información adicional
      html += `
        <div class="mt-3 p-3 bg-light rounded">
          <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Total de modificaciones: <strong>${datos.length}</strong> | 
            Última modificación: <strong>${datos[0].FECHA_CAMBIO}</strong>
          </small>
        </div>
      `;
      
      $('#contenidoHistorial').html(html);
    },
    error: function () {
      $('#contenidoHistorial').html('<div class="alert alert-danger">Error al cargar historial de modificaciones.</div>');
    }
  });
});


// 🆕 FUNCIÓN PARA VER RESULTADO DE APROBACIÓN (SOLO LECTURA)
$(document).off('click', '.btnVerResultadoAprobacion');

// ✅ USAR CAPTURE PHASE PARA MÁXIMA PRIORIDAD
document.addEventListener('click', function(e) {
  if (e.target.closest('.btnVerResultadoAprobacion')) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    
    const btn = e.target.closest('.btnVerResultadoAprobacion');
    const idSolicitud = btn.dataset.id;
    const estadoAprobacion = btn.dataset.aprobacion;
    
    console.log("📋 Ver resultado de aprobación para solicitud:", idSolicitud);
    console.log("🔍 Evento capturado correctamente");
    
    // Mostrar loading
    Swal.fire({
      title: '<i class="fas fa-spinner fa-spin"></i> Cargando información...',
      text: 'Obteniendo detalles del resultado de aprobación',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => Swal.showLoading()
    });
    
    // ✅ OBTENER INFORMACIÓN DETALLADA
    $.ajax({
      url: './supervision/crudsolicitudes.php?action=get_resultado_aprobacion',
      type: 'POST',
      dataType: 'json',
      data: {
        id_solicitud: idSolicitud
      },
      success: function(response) {
        console.log("✅ Respuesta del servidor:", response);
        
        if (response.success) {
          const datos = response.data;
          
          // ✅ CONSTRUIR MODAL DE SOLO LECTURA
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
    $(document).on('click', '.btnVerResumenAprobadoGerenncial', function() {
    const id = $(this).data('id');
    const solicitudId = $(this).data('solicitud-id') || id;
    
    // 🆕 OBTENER NOMBRE DEL GERENTE DESDE LA INTERFAZ (RRHH)
    const filaActual = $(this).closest('tr');
    const nombreGerente = filaActual.find('td:nth-child(5)').text().trim() || 'Gerente'; // Ajustar columna según tu tabla
    
    console.log("📋 RRHH cargando resumen de aprobacion para solicitud:", solicitudId);
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
        url: './gestionhumana/crudsolicitudesrh.php?action=obtener_resumen_rrhh',
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


// 🔄 FUNCIÓN MEJORADA PARA DETECTAR SI SE PUEDE ENVIAR OBSERVACIONES
function puedeEnviarObservaciones(solicitud) {
    // 1. Verificar que el estado actual sea "Día de Prueba"
    const esDiaDePrueba = solicitud.ESTADO_SOLICITUD && 
                         solicitud.ESTADO_SOLICITUD.toLowerCase().includes('día de prueba');
    
    if (!esDiaDePrueba) {
        return {
            puede: false,
            razon: 'El estado actual no es "Día de Prueba"'
        };
    }
    
    // 2. Verificar si ya tiene observaciones para el ciclo actual
    const tieneObservacionesCicloActual = solicitud.TIENE_OBSERVACIONES_DIA_PRUEBA === 1;
    
    if (tieneObservacionesCicloActual) {
        return {
            puede: false,
            razon: 'Ya se enviaron observaciones para este ciclo de "Día de Prueba"'
        };
    }
    
    return {
        puede: true,
        razon: 'Puede enviar observaciones para este nuevo ciclo'
    };
}

// 🆕 FUNCIÓN PARA SUBIR OBSERVACIONES DEL DÍA DE PRUEBA
$(document).off('click', '.btnSubirObservacionesDiaPrueba').on('click', '.btnSubirObservacionesDiaPrueba', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    
    const idSolicitud = $(this).data('id');
    const puesto = $(this).data('puesto');
    const tienda = $(this).data('tienda');
    const supervisor = $(this).data('supervisor');
    
    console.log("📋 Subir observaciones día de prueba - Solicitud:", idSolicitud);
    
    Swal.fire({
        title: '<i class="fas fa-clipboard-list"></i> Observaciones del Día de Prueba',
        html: `
            <div style="text-align: left; max-width: 100%;">
                <!-- Información de la solicitud con indicador de ciclo -->
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                    <h5 style="margin: 0 0 15px 0; font-weight: 600;">
                        <i class="fas fa-info-circle mr-2"></i>Información de la Solicitud
                    </h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px;">
                        <div>
                            <strong><i class="fas fa-hashtag mr-1"></i>ID Solicitud:</strong><br>
                            <span style="opacity: 0.9;">#${idSolicitud}</span>
                        </div>
                        <div>
                            <strong><i class="fas fa-store mr-1"></i>Tienda:</strong><br>
                            <span style="opacity: 0.9;">Tienda ${tienda}</span>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <strong><i class="fas fa-briefcase mr-1"></i>Puesto:</strong><br>
                            <span style="opacity: 0.9;">${puesto}</span>
                        </div>
                    </div>
                    <!-- 🆕 Indicador de nuevo ciclo -->
                    <div style="background: rgba(255,255,255,0.2); border-radius: 8px; padding: 12px; margin-top: 15px; border-left: 4px solid #ffc107;">
                        <div style="display: flex; align-items: center; font-size: 13px;">
                            <i class="fas fa-recycle mr-2" style="color: #ffc107;"></i>
                            <span style="opacity: 0.9;"><strong>Nuevo Ciclo de Evaluación:</strong> Puede enviar observaciones para este período de "Día de Prueba"</span>
                        </div>
                    </div>
                </div>

                <!-- Datos del candidato -->
                <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 15px 0; color: #495057; font-weight: 600;">
                        <i class="fas fa-user mr-2"></i>Datos del Candidato
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-user-circle mr-1"></i>Nombre del Candidato:
                            </label>
                            <input type="text" id="candidato_nombre" class="form-control" 
                                   placeholder="Ingrese el nombre completo del candidato"
                                   style="margin-bottom: 15px;">
                        </div>
                        <div class="col-md-6">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-id-card mr-1"></i>Documento (Opcional):
                            </label>
                            <input type="text" id="candidato_documento" class="form-control" 
                                   placeholder="DPI, Cédula, etc."
                                   style="margin-bottom: 15px;">
                        </div>
                    </div>
                </div>

                <!-- Datos del día de prueba -->
                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 15px 0; color: #856404; font-weight: 600;">
                        <i class="fas fa-calendar-day mr-2"></i>Datos del Día de Prueba
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-calendar mr-1"></i>Fecha:
                            </label>
                            <input type="date" id="fecha_dia_prueba" class="form-control" 
                                   value="${new Date().toISOString().split('T')[0]}"
                                   style="margin-bottom: 15px;">
                        </div>
                        <div class="col-md-4">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-clock mr-1"></i>Hora Inicio:
                            </label>
                            <input type="time" id="hora_inicio" class="form-control" 
                                   style="margin-bottom: 15px;">
                        </div>
                        <div class="col-md-4">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-clock mr-1"></i>Hora Fin:
                            </label>
                            <input type="time" id="hora_fin" class="form-control" 
                                   style="margin-bottom: 15px;">
                        </div>
                    </div>
                </div>

                <!-- Evaluación del desempeño -->
                <div style="background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 15px 0; color: #1976d2; font-weight: 600;">
                        <i class="fas fa-chart-line mr-2"></i>Evaluación del Desempeño
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-clock mr-1"></i>Puntualidad:
                            </label>
                            <select id="puntualidad" class="form-control" style="margin-bottom: 15px;">
                                <option value="">Seleccionar...</option>
                                <option value="EXCELENTE">Excelente</option>
                                <option value="BUENO">Bueno</option>
                                <option value="REGULAR">Regular</option>
                                <option value="MALO">Malo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-smile mr-1"></i>Actitud:
                            </label>
                            <select id="actitud" class="form-control" style="margin-bottom: 15px;">
                                <option value="">Seleccionar...</option>
                                <option value="EXCELENTE">Excelente</option>
                                <option value="BUENA">Buena</option>
                                <option value="REGULAR">Regular</option>
                                <option value="MALA">Mala</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-brain mr-1"></i>Conocimientos:
                            </label>
                            <select id="conocimientos" class="form-control" style="margin-bottom: 15px;">
                                <option value="">Seleccionar...</option>
                                <option value="EXCELENTE">Excelente</option>
                                <option value="BUENO">Bueno</option>
                                <option value="REGULAR">Regular</option>
                                <option value="DEFICIENTE">Deficiente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label style="font-weight: 600; margin-bottom: 5px;">
                                <i class="fas fa-star mr-1"></i>Desempeño General:
                            </label>
                            <select id="desempeno_general" class="form-control" style="margin-bottom: 15px;">
                                <option value="">Seleccionar...</option>
                                <option value="EXCELENTE">Excelente</option>
                                <option value="BUENO">Bueno</option>
                                <option value="REGULAR">Regular</option>
                                <option value="DEFICIENTE">Deficiente</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Observaciones detalladas -->
                <div style="background: #f3e5f5; border: 1px solid #e1bee7; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 15px 0; color: #7b1fa2; font-weight: 600;">
                        <i class="fas fa-edit mr-2"></i>Observaciones Detalladas
                    </h5>
                    <textarea id="observaciones_detalladas" class="form-control" rows="6" 
                              placeholder="Describa detalladamente el desempeño del candidato durante el día de prueba...&#10;&#10;Incluya:&#10;- Tareas específicas realizadas&#10;- Adaptación al equipo&#10;- Resolución de problemas&#10;- Interacción con clientes&#10;- Manejo de herramientas/sistemas&#10;- Observaciones adicionales"
                              style="resize: vertical; font-size: 14px; line-height: 1.4;"></textarea>
                </div>

                <!-- Recomendación final -->
                <div style="background: #e8f5e8; border: 1px solid #c8e6c9; border-radius: 12px; padding: 20px;">
                    <h5 style="margin: 0 0 15px 0; color: #2e7d32; font-weight: 600;">
                        <i class="fas fa-thumbs-up mr-2"></i>Recomendación Final
                    </h5>
                    <div style="display: flex; gap: 20px; align-items: center;">
                        <label style="display: flex; align-items: center; font-weight: 600; cursor: pointer;">
                            <input type="radio" name="recomendacion" value="RECOMENDADO" style="margin-right: 8px; transform: scale(1.2);">
                            <i class="fas fa-check-circle mr-1" style="color: #28a745;"></i>
                            <span style="color: #28a745;">RECOMENDADO para contratación</span>
                        </label>
                        <label style="display: flex; align-items: center; font-weight: 600; cursor: pointer;">
                            <input type="radio" name="recomendacion" value="NO_RECOMENDADO" style="margin-right: 8px; transform: scale(1.2);">
                            <i class="fas fa-times-circle mr-1" style="color: #dc3545;"></i>
                            <span style="color: #dc3545;">NO RECOMENDADO</span>
                        </label>
                    </div>
                </div>
            </div>
        `,
        width: '1000px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Enviar Observaciones',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        buttonsStyling: false,
        customClass: {
            popup: 'observaciones-modal-popup',
            confirmButton: 'btn btn-success btn-lg px-4',
            cancelButton: 'btn btn-secondary btn-lg px-4 mr-2'
        },
        preConfirm: () => {
            // Validaciones
            const candidatoNombre = $('#candidato_nombre').val().trim();
            const fechaDiaPrueba = $('#fecha_dia_prueba').val();
            const horaInicio = $('#hora_inicio').val();
            const horaFin = $('#hora_fin').val();
            const puntualidad = $('#puntualidad').val();
            const actitud = $('#actitud').val();
            const conocimientos = $('#conocimientos').val();
            const desempenoGeneral = $('#desempeno_general').val();
            const observacionesDetalladas = $('#observaciones_detalladas').val().trim();
            const recomendacion = $('input[name="recomendacion"]:checked').val();

            // Validación de campos obligatorios
            if (!candidatoNombre) {
                Swal.showValidationMessage('El nombre del candidato es obligatorio');
                return false;
            }
            
            if (!fechaDiaPrueba) {
                Swal.showValidationMessage('La fecha del día de prueba es obligatoria');
                return false;
            }
            
            if (!horaInicio || !horaFin) {
                Swal.showValidationMessage('Las horas de inicio y fin son obligatorias');
                return false;
            }
            
            if (horaInicio >= horaFin) {
                Swal.showValidationMessage('La hora de fin debe ser posterior a la hora de inicio');
                return false;
            }
            
            if (!puntualidad || !actitud || !conocimientos || !desempenoGeneral) {
                Swal.showValidationMessage('Todos los campos de evaluación son obligatorios');
                return false;
            }
            
            if (!observacionesDetalladas || observacionesDetalladas.length < 50) {
                Swal.showValidationMessage('Las observaciones detalladas deben tener al menos 50 caracteres');
                return false;
            }
            
            if (!recomendacion) {
                Swal.showValidationMessage('Debe seleccionar una recomendación final');
                return false;
            }

            return {
                id_solicitud: idSolicitud,
                candidato_nombre: candidatoNombre,
                candidato_documento: $('#candidato_documento').val().trim(),
                fecha_dia_prueba: fechaDiaPrueba,
                hora_inicio: horaInicio,
                hora_fin: horaFin,
                puesto_evaluado: puesto,
                puntualidad: puntualidad,
                actitud: actitud,
                conocimientos: conocimientos,
                desempeno_general: desempenoGeneral,
                observaciones_detalladas: observacionesDetalladas,
                recomendacion_supervisor: recomendacion,
                supervisor_codigo: supervisor,
                supervisor_nombre: supervisor
            };
        },
        didOpen: () => {
            // Agregar estilos personalizados
            if (!document.getElementById('observaciones-styles')) {
                const styles = document.createElement('style');
                styles.id = 'observaciones-styles';
                styles.textContent = `
                    .observaciones-modal-popup {
                        border-radius: 16px !important;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
                    }
                    .observaciones-modal-popup .form-control:focus {
                        border-color: #667eea !important;
                        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
                    }
                    .observaciones-modal-popup input[type="radio"]:checked {
                        accent-color: #28a745 !important;
                    }
                `;
                document.head.appendChild(styles);
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const datos = result.value;
            
            // Mostrar loading
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Guardando observaciones...',
                text: 'Por favor espera mientras se procesan los datos',
                allowOutsideClick: false,
                showConfirmButton: false
            });
            
            // Enviar datos al servidor
            $.ajax({
                url: './supervision/crudsolicitudes.php?action=guardar_observaciones_dia_prueba',
                type: 'POST',
                dataType: 'json',
                data: datos,
                success: function(response) {
                    console.log("✅ Respuesta del servidor:", response);
                    
                    if (response.success) {
                        // 🔄 MENSAJE MEJORADO CON INFORMACIÓN DEL CICLO
                        Swal.fire({
                            icon: 'success',
                            title: '<i class="fas fa-check-circle"></i> ¡Observaciones Guardadas!',
                            html: `
                                <div style="text-align: center; padding: 15px;">
                                    <p style="margin-bottom: 15px; color: #333;">
                                        Las observaciones del día de prueba han sido registradas correctamente para este ciclo.
                                    </p>
                                    <div style="background: #d4edda; padding: 15px; border-radius: 8px; border-left: 4px solid #28a745;">
                                        <strong style="color: #155724;">
                                            <i class="fas fa-user mr-1"></i> Candidato: ${datos.candidato_nombre}
                                        </strong><br>
                                        <span style="color: #155724;">
                                            <i class="fas fa-${datos.recomendacion_supervisor === 'RECOMENDADO' ? 'thumbs-up' : 'thumbs-down'} mr-1"></i> 
                                            ${datos.recomendacion_supervisor === 'RECOMENDADO' ? 'Recomendado para contratación' : 'No recomendado'}
                                        </span><br>
                                        <small style="color: #155724;">
                                            <i class="fas fa-recycle mr-1"></i> 
                                            Ciclo ID: ${response.data?.id_ciclo || 'N/A'}
                                        </small>
                                    </div>
                                    <div style="background: #e3f2fd; padding: 12px; border-radius: 8px; margin-top: 15px;">
                                        <small style="color: #1976d2;">
                                            <i class="fas fa-lightbulb mr-1"></i>
                                            <strong>Nota:</strong> Si RRHH vuelve a cambiar el estado a "Día de Prueba", podrás enviar nuevas observaciones para el nuevo ciclo.
                                        </small>
                                    </div>
                                </div>
                            `,
                            confirmButtonText: '<i class="fas fa-check"></i> Entendido',
                            confirmButtonColor: '#28a745',
                            width: '600px'
                        });
                        
                        // 🔄 RECARGAR LA TABLA PARA MOSTRAR EL CAMBIO DE BOTÓN
                        cargarSolicitudes();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '<i class="fas fa-exclamation-circle"></i> Error',
                            text: response.error || 'No se pudieron guardar las observaciones',
                            confirmButtonText: 'Entendido',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error guardando observaciones:', {
                        status: xhr.status,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: '<i class="fas fa-wifi"></i> Error de Conexión',
                        text: 'No se pudo conectar al servidor para guardar las observaciones',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
});



$(document).off('click', '.btnVerObservacionesDiaPrueba').on('click', '.btnVerObservacionesDiaPrueba', function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    
    const idSolicitud = $(this).data('id');
    
    console.log("👁️ Ver observaciones día de prueba - Solicitud:", idSolicitud);
    
    // Mostrar loading
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando observaciones...',
        text: 'Obteniendo observaciones del día de prueba',
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    // Obtener observaciones del servidor
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=get_observaciones_dia_prueba',
        type: 'POST',
        dataType: 'json',
        data: {
            id_solicitud: idSolicitud
        },
        success: function(response) {
            console.log("✅ Observaciones recibidas:", response);
            
            if (response.success && response.observaciones) {
                const obs = response.observaciones;
                
                // Construir modal de visualización
                const modalHtml = `
                    <div style="text-align: left; max-width: 100%;">
                        <!-- Encabezado -->
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                            <h5 style="margin: 0 0 10px 0; font-weight: 600;">
                                <i class="fas fa-clipboard-check mr-2"></i>Observaciones del Día de Prueba
                            </h5>
                            <div style="font-size: 14px; opacity: 0.9;">
                                Solicitud #${idSolicitud} - Ciclo ID: ${obs.ID_HIST_ASOCIADO || 'N/A'}
                            </div>
                        </div>

                        <!-- Información del candidato -->
                        <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <h6 style="margin: 0 0 15px 0; color: #495057; font-weight: 600;">
                                <i class="fas fa-user mr-2"></i>Información del Candidato
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-user-circle mr-1"></i>Nombre:</strong><br>
                                    <span style="font-size: 16px; color: #333;">${obs.CANDIDATO_NOMBRE}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-id-card mr-1"></i>Documento:</strong><br>
                                    <span style="font-size: 16px; color: #333;">${obs.CANDIDATO_DOCUMENTO || 'No especificado'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Datos del día de prueba -->
                        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <h6 style="margin: 0 0 15px 0; color: #856404; font-weight: 600;">
                                <i class="fas fa-calendar-day mr-2"></i>Datos del Día de Prueba
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-calendar mr-1"></i>Fecha:</strong><br>
                                    <span style="font-size: 16px; color: #333;">${obs.FECHA_DIA_PRUEBA}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="fas fa-clock mr-1"></i>Horario:</strong><br>
                                    <span style="font-size: 16px; color: #333;">${obs.HORA_INICIO} - ${obs.HORA_FIN}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="fas fa-briefcase mr-1"></i>Puesto:</strong><br>
                                    <span style="font-size: 16px; color: #333;">${obs.PUESTO_EVALUADO}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluación del desempeño -->
                        <div style="background: #e3f2fd; border: 1px solid #bbdefb; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <h6 style="margin: 0 0 15px 0; color: #1976d2; font-weight: 600;">
                                <i class="fas fa-chart-line mr-2"></i>Evaluación del Desempeño
                            </h6>
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                        <i class="fas fa-clock" style="font-size: 24px; color: #1976d2; margin-bottom: 8px;"></i>
                                        <div style="font-weight: 600; color: #333;">Puntualidad</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #1976d2;">${obs.PUNTUALIDAD}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                        <i class="fas fa-smile" style="font-size: 24px; color: #28a745; margin-bottom: 8px;"></i>
                                        <div style="font-weight: 600; color: #333;">Actitud</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #28a745;">${obs.ACTITUD}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                        <i class="fas fa-brain" style="font-size: 24px; color: #7b1fa2; margin-bottom: 8px;"></i>
                                        <div style="font-weight: 600; color: #333;">Conocimientos</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #7b1fa2;">${obs.CONOCIMIENTOS}</div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                        <i class="fas fa-star" style="font-size: 24px; color: #f57c00; margin-bottom: 8px;"></i>
                                        <div style="font-weight: 600; color: #333;">Desempeño</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #f57c00;">${obs.DESEMPENO_GENERAL}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones detalladas -->
                        <div style="background: #f3e5f5; border: 1px solid #e1bee7; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <h6 style="margin: 0 0 15px 0; color: #7b1fa2; font-weight: 600;">
                                <i class="fas fa-edit mr-2"></i>Observaciones Detalladas
                            </h6>
                            <div style="background: white; border: 1px solid #e1bee7; border-radius: 8px; padding: 15px; max-height: 200px; overflow-y: auto;">
                                <div style="font-size: 14px; color: #333; line-height: 1.6; white-space: pre-wrap;">${obs.OBSERVACIONES_DET || 'No se proporcionaron observaciones detalladas.'}</div>
                            </div>
                        </div>

                        <!-- Recomendación final -->
                        <div style="background: ${obs.RECOMENDACION_SUP === 'RECOMENDADO' ? '#e8f5e8' : '#ffebee'}; border: 1px solid ${obs.RECOMENDACION_SUP === 'RECOMENDADO' ? '#c8e6c9' : '#ffcdd2'}; border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                            <h6 style="margin: 0 0 15px 0; color: ${obs.RECOMENDACION_SUP === 'RECOMENDADO' ? '#2e7d32' : '#d32f2f'}; font-weight: 600;">
                                <i class="fas fa-${obs.RECOMENDACION_SUP === 'RECOMENDADO' ? 'thumbs-up' : 'thumbs-down'} mr-2"></i>Recomendación del Supervisor
                            </h6>
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <div style="background: ${obs.RECOMENDACION_SUP === 'RECOMENDADO' ? '#28a745' : '#dc3545'}; color: white; padding: 15px 30px; border-radius: 25px; font-size: 18px; font-weight: 700; text-align: center;">
                                    <i class="fas fa-${obs.RECOMENDACION_SUP === 'RECOMENDADO' ? 'check-circle' : 'times-circle'} mr-2"></i>
                                    ${obs.RECOMENDACION_SUP === 'RECOMENDADO' ? 'RECOMENDADO' : 'NO RECOMENDADO'}
                                </div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 12px; padding: 15px;">
                            <div style="font-size: 12px; color: #6c757d; text-align: center;">
                                <div><strong>Supervisor:</strong> ${obs.SUPERVISOR_NOMBRE} | <strong>Fecha de registro:</strong> ${obs.FECHA_CREACION}</div>
                                <div style="margin-top: 5px;"><strong>Estado:</strong> ${obs.ESTADO} | <strong>Ciclo ID:</strong> ${obs.ID_HIST_ASOCIADO || 'N/A'}</div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Mostrar modal de visualización
                Swal.fire({
                    title: false,
                    html: modalHtml,
                    width: '900px',
                    showCloseButton: true,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                    confirmButtonColor: '#6c757d',
                    customClass: {
                        popup: 'observaciones-view-modal'
                    },
                    didOpen: () => {
                        // Agregar estilos para el modal de visualización
                        if (!document.getElementById('observaciones-view-styles')) {
                            const styles = document.createElement('style');
                            styles.id = 'observaciones-view-styles';
                            styles.textContent = `
                                .observaciones-view-modal {
                                    border-radius: 16px !important;
                                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
                                }
                                .observaciones-view-modal .swal2-html-container {
                                    max-height: 70vh !important;
                                    overflow-y: auto !important;
                                }
                            `;
                            document.head.appendChild(styles);
                        }
                    }
                });
                
            } else {
                Swal.fire({
                    icon: 'info',
                    title: '<i class="fas fa-info-circle"></i> Sin Observaciones',
                    text: 'No se encontraron observaciones para este ciclo del día de prueba',
                    confirmButtonText: 'Entendido'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error obteniendo observaciones:', {
                status: xhr.status,
                responseText: xhr.responseText,
                error: error
            });
            
            Swal.fire({
                icon: 'error',
                title: '<i class="fas fa-exclamation-triangle"></i> Error',
                text: 'No se pudieron cargar las observaciones del día de prueba',
                confirmButtonText: 'Entendido'
            });
        }
    });
});


// BOTON PARA VER EL RESULTADO DEL AVAL
$(document).on('click', '.btnVerResultadoAval', function() {
  const idSolicitud = $(this).data('id');
  const tienda = $(this).data('tienda');
  const puesto = $(this).data('puesto');
  const supervisor = $(this).data('supervisor');
  const razon = $(this).data('razon');
  
  cargarResultadoAvalSupervisor(idSolicitud, tienda, puesto, supervisor, razon);
});

//===========================================================================
// MOSTRAR CANDIDATOS EN LA TABLA DE SOLICITUDES
//===========================================================================
// FUNCIÓN PARA MOSTRAR CANDIDATOS ENVIADOS - SOLO LECTURA SUPERVISORES
window.mostrarCandidatosEnviados = function(idSolicitud) {
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
    
    // ✅ CARGAR CANDIDATOS CON NUEVA LÓGICA
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=get_candidatos_por_solicitud_supervisor',
        type: 'GET',
        data: { 
            id_solicitud: idSolicitud,
            incluir_descartados: true,
            _timestamp: Date.now() // Prevenir caché
        },
        dataType: 'json',
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                const solicitud = response.solicitud || {};
                const esperandoRH = response.esperando_rh || false;
                
                console.log('📊 Respuesta del servidor:', response);
                console.log('⏳ ¿Esperando RH?:', esperandoRH);
                
                // ✅ CREAR ÍNDICE GLOBAL DE CANDIDATOS
                window.CANDIDATOS_INDEX = {};
                response.candidatos.forEach(candidato => {
                    candidato.ID_SOLICITUD = idSolicitud;
                    window.CANDIDATOS_INDEX[candidato.ID_CANDIDATO] = candidato;
                });
                
                // ============================================================================
                // CASO 1: SOLICITUD REACTIVADA - MOSTRAR MENSAJE DE ESPERA
                // ============================================================================
                if (esperandoRH) {
                    mostrarMensajeEsperaReactivacionSupervisor(idSolicitud, solicitud);
                } 
                // ============================================================================
                // CASO 2: SOLICITUD NORMAL O REACTIVACIÓN YA CONFIRMADA
                // ============================================================================
                else {
                    mostrarModalExpedientes(idSolicitud, response.candidatos);
                }
            } else {
                Swal.fire('Info', 'No se encontraron candidatos para esta solicitud', 'info');
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error cargando candidatos:', error);
            Swal.fire('Error', 'Error al cargar candidatos: ' + error, 'error');
        }
    });
};

// ================================================================================================
// NUEVA FUNCIÓN: MOSTRAR MENSAJE DE ESPERA CUANDO LA SOLICITUD ESTÁ EN PROCESO DE REACTIVACIÓN
// ================================================================================================

function mostrarMensajeEsperaReactivacionSupervisor(idSolicitud, solicitud) {
    console.log('⏳ Mostrando mensaje de espera - RH aún no ha seleccionado candidatos');
    
    const tienda = solicitud.num_tienda || 'No especificada';
    const puesto = solicitud.puesto_solicitado || 'No especificado';
    const supervisor = solicitud.supervisor || 'No asignado';
    const motivoReactivacion = solicitud.motivo_reactivacion || 'Sin motivo especificado';
    
    // Crear modal con mensaje de espera
    const htmlModal = `
        <div class="modal fade" id="modalReactivacionSupervisor" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-hourglass-half mr-3"></i>
                            Solicitud en Proceso de Reactivación
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <!-- Información de la solicitud -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-info-circle text-info mr-2"></i>
                                    Información de la Solicitud
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Tienda:</strong><br>${tienda}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Puesto:</strong><br>${puesto}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Supervisor:</strong><br>${supervisor}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alerta principal -->
                        <div class="alert alert-warning text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5 class="mb-3">Esta solicitud fue reactivada recientemente</h5>
                            <p class="mb-0">
                                <strong>Los candidatos estarán disponibles una vez que Recursos Humanos confirme su selección.</strong>
                            </p>
                        </div>
                        
                        <!-- Motivo de reactivación -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-comment-dots mr-2"></i>
                                    Motivo de Reactivación
                                </h6>
                                <p class="mb-0 text-muted">${motivoReactivacion}</p>
                            </div>
                        </div>
                        
                        <!-- Instrucciones -->
                        <div class="mt-4 text-center text-muted">
                            <p class="mb-1">
                                <i class="fas fa-info-circle mr-2"></i>
                                Por favor, espere a que RH confirme la selección de candidatos.
                            </p>
                            <p class="mb-0">
                                <small>Una vez confirmado, los candidatos aparecerán en esta sección.</small>
                            </p>
                        </div>
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
    
    // Remover modal anterior si existe
    $('#modalReactivacionSupervisor').remove();
    $('body').append(htmlModal);
    
    // Abrir modal
    $('#modalReactivacionSupervisor').modal('show');
}

// ================================================================================================
// AGREGAR AL FINAL DEL ARCHIVO (antes de cargarSolicitudes();)
// ================================================================================================

console.log('✅ Módulo de reactivación para Supervisores cargado correctamente');

// FUNCIÓN PARA MOSTRAR MODAL DE EXPEDIENTES
window.mostrarModalExpedientes = function(idSolicitud, candidatos) {
    // Obtener información de la solicitud desde la tabla
    const filaSolicitud = $(`tr[data-id="${idSolicitud}"]`);
    const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(2)').text().trim() : 'No disponible';
    const puestoInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(3)').text().trim() : 'No disponible';
    const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(4)').text().trim() : 'No disponible';
    
    // ✅ CORREGIR: CONTAR CANDIDATOS ACTIVOS VS DESCARTADOS CORRECTAMENTE
    const candidatosActivos = candidatos.filter(c => {
        // Un candidato está activo si:
        // 1. No tiene campo ACTIVO o ACTIVO es 'Y'
        // 2. Y no está marcado como 'Descartado' en ESTADO_CANDIDATO
        const activo = c.ACTIVO === undefined || c.ACTIVO === 'Y' || c.ACTIVO === null;
        const noDescartado = c.ESTADO_CANDIDATO !== 'Descartado' && 
                            c.ESTADO_CANDIDATO !== 'DESCARTADO' &&
                            !c.ESTADO_CANDIDATO?.toLowerCase().includes('descartado');
        return activo && noDescartado;
    });
    
    const candidatosDescartados = candidatos.filter(c => {
        // Un candidato está descartado si:
        // 1. ACTIVO es 'N' 
        // 2. O ESTADO_CANDIDATO contiene 'Descartado'
        // 3. O tiene MOTIVO_DESCARTE
        const activoDescartado = c.ACTIVO === 'N';
        const estadoDescartado = c.ESTADO_CANDIDATO === 'Descartado' || 
                               c.ESTADO_CANDIDATO === 'DESCARTADO' ||
                               (c.ESTADO_CANDIDATO && c.ESTADO_CANDIDATO.toLowerCase().includes('descartado'));
        const tieneMotivoDescarte = c.MOTIVO_DESCARTE && c.MOTIVO_DESCARTE.trim() !== '';
        
        return activoDescartado || estadoDescartado || tieneMotivoDescarte;
    });

    const candidatosAvales = candidatos.filter(c => {
    const estadoActual = (c.ESTADO_CANDIDATO || '').toLowerCase();
    // Incluye candidatos en proceso de aval (pendientes) o ya procesados (enviado)
    return estadoActual.includes('aprobacion') && estadoActual.includes('aval');
    });

    console.log('📊 Conteo de candidatos:', {
        total: candidatos.length,
        activos: candidatosActivos.length,
        descartados: candidatosDescartados.length,
        activosLista: candidatosActivos.map(c => ({id: c.ID_CANDIDATO, nombre: c.NOMBRE_CANDIDATO})),
        descartadosLista: candidatosDescartados.map(c => ({id: c.ID_CANDIDATO, nombre: c.NOMBRE_CANDIDATO, motivo: c.MOTIVO_DESCARTE}))
    });

    const modalHtml = `
        <div class="modal fade" id="modalExpedientesSupervisor" tabindex="-1">
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
                                        <button type="button" class="btn btn-outline-warning" data-filter="avales">
                                            Avales (${candidatosAvales.length})
                                        </button>
                                    </div>
                                    
                                    <div id="listaCandidatosSupervisor" style="max-height: calc(100vh - 300px); overflow-y: auto;">
                                        ${candidatos.map(c => {
const nombreCompleto = `${c.NOMBRE_CANDIDATO || ''} ${c.APELLIDOS_CANDIDATO || ''}`.trim();
const totalArchivos = c.TOTAL_ARCHIVOS || 0;

// ✅ CLASIFICAR CANDIDATOS
const esDescartado = c.ACTIVO === 'N' || c.ESTADO_CANDIDATO === 'Descartado';
const estadoActual = (c.ESTADO_CANDIDATO || '').toLowerCase();
const esAval = estadoActual.includes('aprobacion') && estadoActual.includes('aval');

// ✅ DETERMINAR SI ES APROBADO, RECHAZADO O PENDIENTE
const esAprobado = c.APROBACION === 'Y';
const esRechazado = c.APROBACION === 'N';
const esPendiente = estadoActual === 'aprobacion de aval' && !c.APROBACION;
const esReactivado = c.REACTIVADO_POST_CONTRATACION === 'Y';

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
    claseCSS = 'candidato-aprobado candidato-aval';
    colorBorde = '#28a745';
    colorTexto = '#155724';
    colorFondo = '#d4edda';
} else if (esRechazado) {
    claseCSS = 'candidato-rechazado candidato-aval';
    colorBorde = '#dc3545';
    colorTexto = '#721c24';
    colorFondo = '#f8d7da';
} else if (esPendiente) {
    claseCSS = 'candidato-pendiente-aval candidato-aval';
    colorBorde = '#ffc107';
    colorTexto = '#856404';
    colorFondo = '#fff3cd';
} else if (esAval) {
    claseCSS = 'candidato-aval';
    colorBorde = '#17a2b8';
    colorTexto = '#0c5460';
    colorFondo = '#d1ecf1';
} else {
    claseCSS = 'candidato-activo';
    colorBorde = '#007bff';
    colorTexto = '#004085';
    colorFondo = '#ffffff';
}

return `
    <div class="candidate-card mb-2 ${claseCSS}" 
         data-candidato-id="${c.ID_CANDIDATO}" 
         data-estado="${esDescartado ? 'descartado' : (esAprobado ? 'aprobado' : (esRechazado ? 'rechazado' : (esPendiente ? 'pendiente' : 'activo')))}">
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
                                        
                                        ${candidatos.length === 0 ? `
                                            <div class="text-center py-4">
                                                <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                                                <p class="text-muted mt-2">No hay candidatos registrados</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- PANEL DERECHO - EXPEDIENTE -->
                            <div class="col-md-8" style="background: #f8f9fa;">
                                <div class="p-4">
                                    <div id="expedienteCandidatoSupervisor">
                                        <div class="text-center py-5" style="margin-top: 100px;">
                                            <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 20px;">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <h5 class="text-muted">Selecciona un candidato</h5>
                                            <p class="text-muted">Haz clic en un candidato de la lista para ver su expediente completo</p>
                                            ${candidatos.length === 0 ? `
                                                <div class="alert alert-info mt-3">
                                                    <i class="fas fa-info-circle"></i> No hay candidatos para mostrar
                                                </div>
                                            ` : ''}
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
        background: linear-gradient(135deg, #fff5f5, #ffe6e6) !important;
    }
    .candidato-descartado .card:hover {
        background: linear-gradient(135deg, #ffe6e6, #ffd6d6) !important;
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
    
    /* Candidatos aval genérico */
    .candidato-aval .card {
        border-left: 4px solid #ffc107 !important;
        background: linear-gradient(135deg, #fffbf0, #fff3cd) !important;
    }
    .candidato-aval .card:hover {
        background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
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
    
    /* Ocultar candidatos filtrados */
    .candidato-aprobado.hidden,
    .candidato-rechazado.hidden,
    .candidato-pendiente-aval.hidden,
    .candidato-aval.hidden {
        display: none !important;
    }
</style>
    `;
    

    // Limpiar modales anteriores y agregar nuevo
    $('#modalExpedientesSupervisor').remove();
    $('body').append(modalHtml);
    
    // Mostrar modal manualmente
    const modalElement = document.getElementById('modalExpedientesSupervisor');
    modalElement.style.display = 'block';
    modalElement.classList.add('show');
    document.body.classList.add('modal-open');

    // Agregar backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'modal-backdrop-supervisor';
    document.body.appendChild(backdrop);
    
    // Configurar eventos de filtro
    $('[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        
        // Actualizar botones activos
        $('[data-filter]').removeClass('btn-filter-active').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary btn-outline-success btn-outline-danger');
        
        // Aplicar clases según el filtro seleccionado
        if (filter === 'activos') {
            $(this).addClass('btn-outline-success');
        } else if (filter === 'descartados') {
            $(this).addClass('btn-outline-danger');
        } else {
            $(this).addClass('btn-filter-active');
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
            $('.candidato-aval').show();
        }
    });
    
    // Configurar evento de clic en candidatos
    $('.candidate-card').on('click', function(e) {
        e.preventDefault();
        const idCandidato = $(this).data('candidato-id');
        const candidato = window.CANDIDATOS_INDEX[idCandidato];
        
        if (!candidato) {
            console.error('Candidato no encontrado en el índice:', idCandidato);
            return;
        }
        
        const nombreCompleto = `${candidato.NOMBRE_CANDIDATO || ''} ${candidato.APELLIDOS_CANDIDATO || ''}`.trim();
        
        // Marcar como seleccionado
        $('.candidate-card .card').removeClass('border-primary bg-light border-danger');
        $(this).find('.card').addClass('bg-light');
        
        // Cargar expediente
        seleccionarCandidatoSupervisor(idCandidato, nombreCompleto);
    });

    // Manejar cierre manual del modal
    $('#modalExpedientesSupervisor .close').on('click', function() {
        document.getElementById('modalExpedientesSupervisor').style.display = 'none';
        document.getElementById('modalExpedientesSupervisor').classList.remove('show');
        document.body.classList.remove('modal-open');
        $('#modal-backdrop-supervisor').remove();
        $('#modalExpedientesSupervisor').remove();
    });
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
                es_activo: c.ACTIVO === 'S' || c.ACTIVO === undefined || c.ACTIVO === null,
                es_descartado: c.ACTIVO === 'N' || 
                             c.ESTADO_CANDIDATO === 'Descartado' || 
                             c.ESTADO_CANDIDATO === 'DESCARTADO' ||
                             (c.ESTADO_CANDIDATO && c.ESTADO_CANDIDATO.toLowerCase().includes('descartado'))
            });
        });
    }
};

// FUNCIÓN PARA SELECCIONAR CANDIDATO
window.seleccionarCandidatoSupervisor = function(idCandidato, nombreCandidato) {
    console.log('🎯 Candidato seleccionado:', idCandidato, nombreCandidato);
    
    // Obtener información del candidato para determinar si está descartado
    const candidato = window.CANDIDATOS_INDEX[idCandidato];
    const esDescartado = candidato && (candidato.ACTIVO === 'N' || candidato.ESTADO_CANDIDATO === 'Descartado');
    
    // Limpiar selecciones previas
    $('.candidate-card .card').removeClass('border-primary bg-light border-danger');
    
    // Marcar como seleccionado con el color apropiado
    const cardSeleccionado = $(`.candidate-card[data-candidato-id="${idCandidato}"] .card`);
    if (esDescartado) {
        cardSeleccionado.addClass('border-danger bg-light');
    } else {
        cardSeleccionado.addClass('border-primary bg-light');
    }
    
    // Cargar expediente
    verExpedienteCandidato(idCandidato, nombreCandidato);
};

// FUNCIÓN PARA VER EXPEDIENTE DE CANDIDATO - SOLO LECTURA
window.verExpedienteCandidato = function(idCandidato, nombreCandidato) {
    console.log('Viendo expediente de candidato:', idCandidato);
    
    // OBTENER INFORMACIÓN COMPLETA DEL CANDIDATO DESDE EL ÍNDICE
    const candidato = window.CANDIDATOS_INDEX[idCandidato];
    if (!candidato) {
        $('#expedienteCandidatoSupervisor').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> 
                Error: No se encontró información del candidato
            </div>
        `);
        return;
    }
    
    const nombreReal = `${candidato.NOMBRE_CANDIDATO || ''} ${candidato.APELLIDOS_CANDIDATO || ''}`.trim();
    const esDescartado = candidato.ACTIVO === 'N' || candidato.ESTADO_CANDIDATO === 'Descartado';
    const estadoActual = (candidato.ESTADO_CANDIDATO || '').toLowerCase();

    // SI EL CANDIDATO TIENE AVAL PROCESADO, MOSTRAR RESULTADO
    if (estadoActual === 'aprobacion de aval enviado') {
        console.log('🎯 Candidato con aval procesado, mostrando resultado');
        
        // Mostrar loading mientras se carga
        $('#expedienteCandidatoSupervisor').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-success" style="width: 3rem; height: 3rem;"></div>
                <h5 class="mt-3 text-success">Cargando expediente del candidato...</h5>
                <p class="text-muted">Obteniendo resultado del aval gerencial</p>
            </div>
        `);
        
        // Mostrar resultado después de 300ms
        setTimeout(() => {
            mostrarResultadoAvalProcesadoSupervisor(candidato);
        }, 300);
        
        return;
    }
    
    // Mostrar loading en el panel del expediente
    $('#expedienteCandidatoSupervisor').html(`
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
        url: './supervision/crudsolicitudes.php?action=get_permisos_subida_candidato_supervisor',
        type: 'GET',
        data: {
            id_candidato: idCandidato,
            rol_usuario: 'SUPERVISOR',
            incluir_motivo_descarte: true // ✅ NUEVO PARÁMETRO
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Incluir información del candidato en la respuesta
                response.candidato = candidato;
                mostrarExpedienteCompletoSupervisor(idCandidato, nombreReal, response);
            } else {
                $('#expedienteCandidatoSupervisor').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Error: ${response.error}
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error obteniendo permisos:', error);
            $('#expedienteCandidatoSupervisor').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error al cargar expediente
                </div>
            `);
        }
    });
};

window.mostrarExpedienteCompletoSupervisor = function(idCandidato, nombreCandidato, datosCompletos) {
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
    
    const filaSolicitud = $(`tr[data-id]`).first();
    const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(2)').text().trim() : 'No disponible';
    const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(4)').text().trim() : 'No disponible';
    const fechaRegistro = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(7)').text().trim() : 'No disponible';

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
                                onclick="verArchivosCarpeta('${idCandidato}', '${carpeta.nombre_estado}')">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                    `;
                } else {
                    accionesHtml = `
                        <button class="btn btn-outline-primary btn-sm" 
                                onclick="verArchivosCarpeta('${idCandidato}', '${carpeta.nombre_estado}')">
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
                            onclick="subirArchivoSupervisor('${idCandidato}', '${carpeta.nombre_estado}')">
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
    
    const expedienteHtml = `
        <div class="container-fluid">
            <div class="card ${esDescartado ? 'border-danger' : esContratado ? 'border-success' : ''}">
                <div class="card-header ${esDescartado ? 'bg-danger text-white' : esContratado ? 'bg-success text-white' : 'bg-info text-white'}">
                    <h5 class="mb-0">
                        <i class="fas ${esDescartado ? 'fa-user-times' : esContratado ? 'fa-user-check' : 'fa-user'} mr-2"></i>
                        ${nombreCandidato}
                        <span class="badge ${esDescartado ? 'badge-light text-dark' : 'badge-light'} ml-2">${estadoActual}</span>
                        <span class="badge badge-info ml-2">Supervisión</span>
                        ${esDescartado ? '<span class="badge badge-light ml-2">DESCARTADO</span>' : ''}
                        ${esContratado ? '<span class="badge badge-light ml-2">CONTRATADO</span>' : ''}
                    </h5>
                </div>
                <div class="card-body">
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
                            <button class="btn btn-danger btn-lg" onclick="descartarCandidatoSupervisor(${idCandidato}, '${nombreCandidato}')">
                                <i class="fas fa-user-times mr-2"></i>Descartar Candidato
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    $('#expedienteCandidatoSupervisor').html(expedienteHtml);
};
//=============================================================================
// ARCHIVOS PARA SOLO LECTURA DEL ESTADO DE LOS CANDIDATOS 
//=============================================================================
// FUNCIÓN PARA MOSTRAR EXPEDIENTE EN MODO SOLO LECTURA
window.mostrarExpedienteSoloLectura = function(idCandidato, nombreCandidato, datosPermisos) {
    const carpetas = datosPermisos.carpetas || [];
    const estadoActual = datosPermisos.estado_candidato || 'No definido';
    const puestoSolicitado = datosPermisos.puesto_solicitado || 'No definido';
    
    // Obtener información de la solicitud desde la tabla si está disponible
    const filaSolicitud = $(`tr[data-id]`).first();
    const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(2)').text().trim() : 'No disponible';
    const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(4)').text().trim() : 'No disponible';

    // OBTENER SI EL CANDIDATO ESTA ACTIVO O DESCARTADO
    const candidatoActivo = estadoActual.toLowerCase() !== 'descartado';
    
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
                <button class="btn btn-outline-primary btn-sm" 
                        onclick="verArchivosCarpeta('${idCandidato}', '${carpeta.nombre_estado}')">
                    <i class="fas fa-eye"></i> Ver Archivos
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
}else {
            // No puede subir - gris
            colorCard = 'secondary';
            iconoCarpeta = 'fas fa-folder';
            estadoCarpeta = 'Sin archivos';
            accionesHtml = `
                <small class="text-muted">${carpeta.motivo_bloqueo || 'Sin archivos'}</small>
            `;
        }
        
        // 🎯 DISEÑO UNIFORME - todas las cards iguales
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
                        <span class="badge badge-warning ml-2">Supervisión</span>
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
                    
                    <!-- BOTÓN DE DESCARTAR -->
                    ${candidatoActivo ? `
                        <div class="mt-4 pt-3 border-top text-center">
                            <button class="btn btn-danger btn-lg" onclick="descartarCandidatoSupervisor(${idCandidato}, '${nombreCandidato}')">
                                <i class="fas fa-user-times mr-2"></i>Descartar Candidato
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    $('#expedienteCandidatoSupervisor').html(expedienteHtml);
}

// ================================================================
// 🆕 FUNCIÓN PARA SUBIR ARCHIVO COMO SUPERVISOR
// ================================================================

function subirArchivoSupervisor(idCandidato, nombreEstado) {
    console.log('📤 Subir archivo supervisor:', idCandidato, nombreEstado);
    
    // Crear input file
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
    
    input.onchange = function(e) {
        const archivo = e.target.files[0];
        if (!archivo) return;
        
        // Validar tamaño
        if (archivo.size > 10 * 1024 * 1024) {
            Swal.fire('Error', 'Archivo muy grande (máx. 10MB)', 'error');
            return;
        }
        
        // Confirmar subida
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
                procesarSubidaSupervisor(idCandidato, nombreEstado, archivo);
            }
        });
    };
    
    input.click();
}

// ================================================================
// 🆕 FUNCIÓN PARA PROCESAR LA SUBIDA
// ================================================================

function procesarSubidaSupervisor(idCandidato, nombreEstado, archivo) {
    // ✅ PRIMERO: Obtener el ID_SOLICITUD del candidato
    console.log('🔍 Obteniendo ID_SOLICITUD para candidato:', idCandidato);
    
    $.ajax({
        url: './supervision/crudsolicitudes.php',
        type: 'GET',
        data: {
            action: 'get_solicitud_by_candidato',
            id_candidato: idCandidato
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.id_solicitud) {
                // ✅ Ahora que tenemos el ID_SOLICITUD, procedemos con la subida
                realizarSubidaSupervisor(idCandidato, response.id_solicitud, nombreEstado, archivo);
            } else {
                Swal.fire('Error', 'No se pudo obtener información de la solicitud', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error obteniendo ID_SOLICITUD:', error);
            Swal.fire('Error', 'Error al obtener información de la solicitud', 'error');
        }
    });
}

function realizarSubidaSupervisor(idCandidato, idSolicitud, nombreEstado, archivo) {
    console.log('📤 Iniciando subida con:', {
        idCandidato,
        idSolicitud,
        nombreEstado,
        archivo: archivo.name
    });
    
    const formData = new FormData();
    formData.append('action', 'subir_archivo_candidato_supervisor');
    formData.append('id_candidato', idCandidato);
    formData.append('id_solicitud', idSolicitud); // ✅ AHORA SÍ INCLUIMOS EL ID_SOLICITUD
    formData.append('estado_relacionado', nombreEstado);
    formData.append('archivo', archivo);
    
    // Mostrar loading
    Swal.fire({
        title: 'Subiendo archivo...',
        html: `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Subiendo...</span>
                </div>
                <p><strong>Archivo:</strong> ${archivo.name}</p>
                <p><strong>Estado:</strong> ${nombreEstado}</p>
                <p><strong>Candidato ID:</strong> ${idCandidato}</p>
                <p><strong>Solicitud ID:</strong> ${idSolicitud}</p>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    // Realizar subida
    $.ajax({
        url: './supervision/crudsolicitudes.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('✅ Respuesta subida:', response);
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Archivo subido exitosamente!',
                    text: 'El archivo se guardó correctamente.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Recargar el expediente para mostrar el nuevo archivo
                    verExpedienteCandidato(idCandidato, 'Candidato');
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al subir archivo',
                    text: response.error || 'Error desconocido'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error en subida:', {xhr, status, error});
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo subir el archivo. Intente nuevamente.'
            });
        }
    });
}

// FUNCIÓN PARA VER ARCHIVOS DE UNA CARPETA - SOLO LECTURA
window.verArchivosCarpeta = function(idCandidato, nombreEstado) {
    console.log('Viendo archivos de:', nombreEstado, 'para candidato:', idCandidato);
    
    $.ajax({
        url: './supervision/crudsolicitudes.php?action=get_archivos_candidato',
        type: 'GET',
        data: {
            id_candidato: idCandidato,
            estado_relacionado: nombreEstado
        },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.archivos && response.archivos.length > 0) {
                mostrarArchivosModal(response.archivos, nombreEstado);
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

// FUNCIÓN PARA MOSTRAR ARCHIVOS EN MODAL
window.mostrarArchivosModal = function(archivos, nombreEstado) {
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
                                    onclick="verArchivo('${archivo.ID_ARCHIVO}', '${archivo.NOMBRE_ARCHIVO}')">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                            <button class="btn btn-sm btn-primary" 
                                    onclick="descargarArchivo('${archivo.ID_ARCHIVO}', '${archivo.NOMBRE_ARCHIVO}')">
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

//============================================================================
// DESCARGAR ARCHIVOS DEL CANDIDATO -
//============================================================================
// FUNCIÓN PARA DESCARGAR ARCHIVO
window.descargarArchivo = function(idArchivo, nombreArchivo) {
    console.log('Descargando archivo:', nombreArchivo);
    
    // Crear enlace temporal para descarga usando el nombre del archivo
    const enlaceDescarga = document.createElement('a');
    enlaceDescarga.href = `./supervision/crudsolicitudes.php?action=descargar_archivo&archivo=${nombreArchivo}`;
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

// FUNCIÓN PARA VER ARCHIVO EN NUEVA VENTANA
window.verArchivo = function(idArchivo, nombreArchivo) {
   // Abrir en nueva pestaña del mismo navegador (no ventana emergente)
    const url = `./supervision/crudsolicitudes.php?action=ver_archivo&archivo=${nombreArchivo}`;
    
    // Usar window.open con parámetros específicos para que se abra en pestaña
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

//==============================================================================
// FUNCIONES DE DESCARTAR CANDIDATO SUPERVISION 
//==============================================================================

// FUNCIÓN PARA DESCARTAR CANDIDATO EN SUPERVISIÓN
window.descartarCandidatoSupervisor = function(idCandidato, nombreCandidato) {
  console.log('Descartando candidato supervisor:', idCandidato, nombreCandidato);
  
  // Mostrar loading mientras carga la información
  Swal.fire({
    title: 'Cargando información...',
    text: 'Obteniendo datos del candidato',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });
  
  // Obtener información completa del candidato
  $.ajax({
    url: './supervision/crudsolicitudes.php?action=get_permisos_subida_candidato_supervisor',
    type: 'GET',
    data: {
      id_candidato: idCandidato,
      rol_usuario: 'SUPERVISOR'
    },
    dataType: 'json',
    success: function(response) {
      Swal.close();
      
      if (response.success) {
        mostrarModalDescarteCompletoSupervisor(idCandidato, nombreCandidato, response);
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
window.mostrarModalDescarteCompletoSupervisor = function(idCandidato, nombreCandidato, datosCompletos) {
  const carpetas = datosCompletos.carpetas || [];
  const estadoActual = datosCompletos.estado_candidato || 'No definido';
  const puestoSolicitado = datosCompletos.puesto_solicitado || 'No definido';
  
  // Obtener información de la solicitud desde la tabla
  const filaSolicitud = $(`tr[data-id]`).first();
  const tiendaInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(2)').text().trim() : 'No disponible';
  const supervisorInfo = filaSolicitud.length > 0 ? filaSolicitud.find('td:nth-child(4)').text().trim() : 'No disponible';
  
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
    <div class="modal fade" id="modalDescartarSupervisor${idCandidato}" tabindex="-1" data-backdrop="static">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">
              <i class="fas fa-user-times mr-2"></i>Descartar Candidato - Vista Supervisión
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
              <label for="motivoDescarteSupervisor${idCandidato}" class="font-weight-bold text-danger">
                Motivo del descarte <span class="text-danger">*</span>:
              </label>
                <textarea 
                    id="motivoDescarteSupervisor${idCandidato}" 
                    class="form-control" 
                    rows="4" 
                    placeholder="Ingrese el motivo por el cual está descartando este candidato..."
                    maxlength="500"
                    oninput="updateCharCountSupervisor${idCandidato}()"
                ></textarea>
                <div class="d-flex justify-content-between">
                    <small class="form-text text-muted">
                        Máximo 500 caracteres. Este campo es obligatorio.
                    </small>
                    <small class="text-muted">
                        <span id="charCountSupervisor${idCandidato}">0</span>/500 caracteres
                    </small>
                </div>
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fas fa-times mr-2"></i>Cancelar
            </button>
            <button type="button" class="btn btn-danger" id="btnConfirmarDescarteSupervisor${idCandidato}">
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
  $(`#modalDescartarSupervisor${idCandidato}`).modal('show');

  // Función para contar caracteres
    window[`updateCharCountSupervisor${idCandidato}`] = function() {
        const count = $(`#motivoDescarteSupervisor${idCandidato}`).val().length;
        $(`#charCountSupervisor${idCandidato}`).text(count);
        
        // Cambiar color si se acerca al límite - CORREGIR LA LÓGICA:
        if (count > 480) {
            $(`#charCountSupervisor${idCandidato}`).parent().removeClass('text-muted text-warning').addClass('text-danger');
        } else if (count > 450) {
            $(`#charCountSupervisor${idCandidato}`).parent().removeClass('text-muted text-danger').addClass('text-warning');
        } else {
            $(`#charCountSupervisor${idCandidato}`).parent().removeClass('text-warning text-danger').addClass('text-muted');
        }
    };
    $(`#motivoDescarteSupervisor${idCandidato}`).on('input', window[`updateCharCountSupervisor${idCandidato}`]);
  
  // Configurar eventos
  $(`#btnConfirmarDescarteSupervisor${idCandidato}`).on('click', function() {
    const motivo = $(`#motivoDescarteSupervisor${idCandidato}`).val().trim();
    
    if (motivo.length < 10) {
      Swal.fire('Error', 'El motivo debe tener al menos 10 caracteres', 'warning');
      $(`#motivoDescarteSupervisor${idCandidato}`).focus();
      return;
    }
    
    confirmarDescarteSupervisor(idCandidato, motivo);
  });
  
  // Auto-focus y limpieza
  $(`#modalDescartarSupervisor${idCandidato}`).on('shown.bs.modal', function() {
    setTimeout(() => {
      $(`#motivoDescarteSupervisor${idCandidato}`).focus();
    }, 300);
  });
  
  $(`#modalDescartarSupervisor${idCandidato}`).on('hidden.bs.modal', function() {
    $(this).remove();
  });
}

// FUNCIÓN PARA CARGAR ESTADOS ALCANZADOS
window.cargarEstadosAlcanzadosSupervisor = function(idCandidato) {
  $.ajax({
    url: './supervision/crudsolicitudes.php?action=get_permisos_subida_candidato_supervisor',
    type: 'GET',
    data: {
      id_candidato: idCandidato,
      rol_usuario: 'SUPERVISOR'
    },
    dataType: 'json',
    success: function(response) {
      if (response.success && response.carpetas) {
        mostrarEstadosAlcanzadosSupervisor(idCandidato, response.carpetas);
      } else {
        $(`#estadosAlcanzadosSupervisor${idCandidato}`).html(`
          <div class="text-muted">No se pudieron cargar los estados</div>
        `);
      }
    },
    error: function() {
      $(`#estadosAlcanzadosSupervisor${idCandidato}`).html(`
        <div class="text-danger">Error cargando estados</div>
      `);
    }
  });
}

// FUNCIÓN PARA MOSTRAR ESTADOS ALCANZADOS
window.mostrarEstadosAlcanzadosSupervisor = function (idCandidato, carpetas) {
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
  
  $(`#estadosAlcanzadosSupervisor${idCandidato}`).html(estadosHtml);
}

// FUNCIÓN PARA CONFIRMAR DESCARTE
window.confirmarDescarteSupervisor = function (idCandidato, motivo) {
  // Cerrar modal
  $(`#modalDescartarSupervisor${idCandidato}`).modal('hide');
  
  // Mostrar loading
  Swal.fire({
    title: 'Descartando candidato...',
    text: 'Procesando solicitud de supervisión...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });
  
  // Procesar descarte
  $.ajax({
    url: './supervision/crudsolicitudes.php?action=descartar_candidato_supervisor',
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
          text: 'El candidato fue descartado correctamente por supervisión',
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

//=========================================================================================
// FUNCION PARA MOSTRAR RESULTADO DE AVAL 
//=========================================================================================


//===================================================================================
// FUNCIÓN PARA MOSTRAR RESULTADO DE AVAL PROCESADO - SUPERVISIÓN
//===================================================================================
function mostrarResultadoAvalProcesadoSupervisor(candidato) {
    console.log('🎯 SUPERVISIÓN - Mostrando resultado aval procesado:', candidato);
    
    function obtenerInformacionAvalCompleta(idCandidato) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: './supervision/crudsolicitudes.php?action=get_info_aval_completa_supervisor',
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

    obtenerInformacionAvalCompleta(candidato.ID_CANDIDATO)
        .then(candidatoCompleto => {
            const candidatoInfo = { ...candidato, ...candidatoCompleto };
            
            const esAprobado = candidatoInfo.APROBACION === 'Y';
            const decision = esAprobado ? 'APROBADO PARA CONTRATACION' : 'RECHAZADO';
            const colorFondo = esAprobado ? 'bg-success' : 'bg-danger';
            const colorBorde = esAprobado ? 'border-success' : 'border-danger';
            const icono = esAprobado ? 'fa-check-circle' : 'fa-times-circle';
            const nombreGerente = candidatoInfo.NOMBRE_GERENTE || 'Gerente de Operaciones';
            
            const expedienteHtml = `
                <div class="card border-0 shadow-lg">
                    <div class="card-header ${colorFondo} text-white">
                        <h4 class="mb-0">
                            <i class="fas ${icono} mr-2"></i>${decision}
                        </h4>
                        <p class="mb-0 mt-2">
                            <i class="fas fa-user-tie mr-2"></i>
                            Procesado por: <strong>${nombreGerente}</strong>
                        </p>
                    </div>
                    
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card ${colorBorde}">
                                    <div class="card-header ${colorFondo} text-white">
                                        <h6 class="mb-0"><i class="fas fa-user mr-2"></i>Información del Candidato</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Nombre:</strong> ${candidatoInfo.NOMBRE_CANDIDATO} ${candidatoInfo.APELLIDOS_CANDIDATO}</p>
                                        <p><strong>Documento:</strong> ${candidatoInfo.DOCUMENTO_CANDIDATO || 'No registrado'}</p>
                                        <p><strong>Estado:</strong> <span class="badge ${colorFondo}">${candidatoInfo.ESTADO_CANDIDATO}</span></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-building mr-2"></i>Información de la Solicitud</h6>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Tienda:</strong> ${candidatoInfo.NUM_TIENDA}</p>
                                        <p><strong>Puesto:</strong> ${candidatoInfo.PUESTO_SOLICITADO}</p>
                                        <p><strong>Supervisor:</strong> ${candidatoInfo.SUPERVISOR}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-${esAprobado ? 'success' : 'warning'} border-${esAprobado ? 'success' : 'warning'}">
                            <h6 class="font-weight-bold">
                                <i class="fas fa-comment-dots mr-2"></i>
                                ${esAprobado ? 'Comentarios de Aprobación' : 'Motivo del Rechazo'}:
                            </h6>
                            <p class="mb-2" style="white-space: pre-wrap;">${candidatoInfo.MOTIVO_DECISION || 'Sin comentarios'}</p>
                            <hr>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-2"></i>
                                Fecha de decisión: <strong>${candidatoInfo.FECHA_DECISION_FORMATEADA || 'No disponible'}</strong>
                            </small>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-clipboard-list mr-2"></i>Estados Completados antes del Aval</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- CV Enviado -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_CV > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-file-alt fa-3x mb-2 ${candidatoInfo.ARCHIVOS_CV > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">CV Enviado</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_CV > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_CV > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_CV})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_CV > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'CV Enviado')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Psicométrica -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-brain fa-3x mb-2 ${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Psicométrica</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_PSICOMETRICA})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_PSICOMETRICA > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Psicometrica')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Entrevista RH -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-comments fa-3x mb-2 ${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Entrevista RH</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_ENTREVISTA_RH})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_ENTREVISTA_RH > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Entrevista RH')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Entrevista Técnica -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-laptop-code fa-3x mb-2 ${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Entrevista Técnica</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_ENTREVISTA_TECNICA > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Entrevista Tecnica')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Día de Prueba -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-clipboard-check fa-3x mb-2 ${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Día de Prueba</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_DIA_PRUEBA})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_DIA_PRUEBA > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Dia de Prueba')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Polígrafo -->
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'success' : 'secondary'}">
                                            <div class="card-body text-center py-3">
                                                <i class="fas fa-shield-alt fa-3x mb-2 ${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'text-success' : 'text-muted'}"></i>
                                                <p class="mb-1 font-weight-bold">Polígrafo</p>
                                                <span class="badge badge-${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'success' : 'secondary'}">
                                                    ${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? 'Completado' : 'Pendiente'} (${candidatoInfo.ARCHIVOS_POLIGRAFO})
                                                </span>
                                                ${candidatoInfo.ARCHIVOS_POLIGRAFO > 0 ? `
                                                    <button class="btn btn-sm btn-info mt-2 w-100" 
                                                            onclick="verArchivosCarpeta(${candidatoInfo.ID_CANDIDATO}, 'Poligrafo')">
                                                        <i class="fas fa-eye mr-1"></i>Ver Archivos
                                                    </button>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Vista de Solo Lectura:</strong> Este candidato ya fue procesado por el gerente. Los supervisores pueden revisar el resultado pero no realizar cambios.
                        </div>
                    </div>
                </div>
            `;
            
            $('#expedienteCandidatoSupervisor').html(expedienteHtml);
        })
        .catch(error => {
            console.error('Error:', error);
            $('#expedienteCandidatoSupervisor').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error:</strong> ${error}
                </div>
            `);
        });
}

window.mostrarResultadoAvalProcesadoSupervisor = mostrarResultadoAvalProcesadoSupervisor;


//========================FIN FUNCION DE RESULTADO DE AVAL=================================

// ================================================================================================
// NUEVA FUNCIÓN: MOSTRAR MENSAJE DE ESPERA CUANDO LA SOLICITUD ESTÁ EN REACTIVACIÓN
// ================================================================================================

function mostrarMensajeEsperaReactivacionSupervisor(idSolicitud, solicitud) {
    console.log('⏳ Mostrando mensaje de espera - RH aún no ha seleccionado candidatos');
    
    const tienda = solicitud.num_tienda || 'No especificada';
    const puesto = solicitud.puesto_solicitado || 'No especificado';
    const supervisor = solicitud.supervisor || 'No asignado';
    const motivoReactivacion = solicitud.motivo_reactivacion || 'Sin motivo especificado';
    
    const htmlModal = `
        <div class="modal fade" id="modalReactivacionSupervisor" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-hourglass-half mr-3"></i>
                            Solicitud en Proceso de Reactivación
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <!-- Información de la solicitud -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="fas fa-info-circle text-info mr-2"></i>
                                    Información de la Solicitud
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Tienda:</strong><br>${tienda}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Puesto:</strong><br>${puesto}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Supervisor:</strong><br>${supervisor}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alerta principal -->
                        <div class="alert alert-warning text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5 class="mb-3">Esta solicitud fue reactivada recientemente</h5>
                            <p class="mb-0">
                                <strong>Los candidatos estarán disponibles una vez que Recursos Humanos confirme su selección.</strong>
                            </p>
                        </div>
                        
                        <!-- Motivo de reactivación -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-comment-dots mr-2"></i>
                                    Motivo de Reactivación
                                </h6>
                                <p class="mb-0 text-muted">${motivoReactivacion}</p>
                            </div>
                        </div>
                        
                        <!-- Instrucciones -->
                        <div class="mt-4 text-center text-muted">
                            <p class="mb-1">
                                <i class="fas fa-info-circle mr-2"></i>
                                Por favor, espere a que RH confirme la selección de candidatos.
                            </p>
                            <p class="mb-0">
                                <small>Una vez confirmado, los candidatos aparecerán en esta sección.</small>
                            </p>
                        </div>
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
    
    // Remover modal anterior si existe
    $('#modalReactivacionSupervisor').remove();
    $('body').append(htmlModal);
    
    // Abrir modal
    $('#modalReactivacionSupervisor').modal('show');
}

console.log('✅ Módulo de reactivación para Supervisores cargado correctamente');

//===============================================================================================
// FUNCION DE REACTIVACION DE LA SOLICITUD 
//===============================================================================================

//=========================================================================================
// REACTIVAR SOLICITUD - SUPERVISORES
//=========================================================================================

// Event listener para botón Reactivar
$(document).on('click', '.btnReactivarSolicitud', function() {
    const idSolicitud = $(this).data('id');
    const tienda = $(this).data('tienda');
    const puesto = $(this).data('puesto');
    
    // Llenar datos en el modal
    $('#reactivarTienda').text(tienda);
    $('#reactivarPuesto').text(puesto);
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
                url: './supervision/crudsolicitudes.php',
                type: 'POST',
                data: {
                    action: 'reactivar_solicitud_supervisor',
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

//=========================FIN FUNCION DE REACTIVACION DE LA SOLICITUD =============================


//========================FIN FUNCIONES DE DESCARTE CANDIDATOS=============================
   
// FORZAR FUNCIONES AL CONTEXTO GLOBAL
setTimeout(function() {
    if (typeof window.parent !== 'undefined') {
        window.parent.seleccionarCandidatoSupervisor = window.seleccionarCandidatoSupervisor;
        window.parent.verExpedienteCandidato = window.verExpedienteCandidato;
        window.parent.mostrarExpedienteSoloLectura = window.mostrarExpedienteSoloLectura;
        window.parent.mostrarCandidatosEnviados = window.mostrarCandidatosEnviados;
        window.subirArchivoSupervisor = subirArchivoSupervisor;
        window.procesarSubidaSupervisor = procesarSubidaSupervisor;
        window.descartarCandidatoSupervisor = descartarCandidatoSupervisor;
        window.cargarEstadosAlcanzadosSupervisor = cargarEstadosAlcanzadosSupervisor;
        window.mostrarEstadosAlcanzadosSupervisor = mostrarEstadosAlcanzadosSupervisor;
        window.confirmarDescarteSupervisor = confirmarDescarteSupervisor;
    }
    
    // También en el contexto actual
    window.seleccionarCandidatoSupervisor = function(idCandidato, nombreCandidato) {
        $('.candidate-card .card').removeClass('border-primary bg-light').addClass('border-left-primary');
        $(`.candidate-card[data-candidato-id="${idCandidato}"] .card`).removeClass('border-left-primary').addClass('border-primary bg-light');
        verExpedienteCandidato(idCandidato, nombreCandidato);
    };
}, 100);

// CARGAR SOLICITUDES AL INICIO
      cargarSolicitudes();
    });
  </script>
</body>
</html>