<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Supervisores - Solicitudes de Personal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ENLACES DE CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- ENLACES DE JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- ✅ TU ARCHIVO JAVASCRIPT CORREGIDO -->
  <script src="../Js/gestionhumana/solicitudesrh.js"></script>

  <!-- ENLACES DE CSS LOCALES -->
  <link rel="stylesheet" href="../Css/solicitudes-rh.css">

  <style>
/* =====================================================================
   ESTILOS CORPORATIVOS MODERNOS - RH (RECURSOS HUMANOS)
   Paleta: Azul Corporativo + Grises Profesionales
   Sin morado - Diseño profesional y elegante
   ===================================================================== */

/* ======================================== 
   BASE Y CONTENEDORES PRINCIPALES
   ======================================== */

body {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  min-height: 100vh;
}

.main-container {
  background: rgba(255, 255, 255, 0.98);
  border-radius: 24px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(10px);
  margin: 20px;
  padding: 35px;
}

/* ======================================== 
   HEADER SECTION
   ======================================== */

.header-section {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
  padding: 30px;
  border-radius: 18px;
  margin-bottom: 35px;
  box-shadow: 0 12px 30px rgba(30, 58, 138, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.header-title {
  font-size: 2.4rem;
  font-weight: 700;
  margin: 0;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  letter-spacing: -0.5px;
}

.header-subtitle {
  font-size: 1.15rem;
  opacity: 0.95;
  margin: 8px 0 0 0;
  font-weight: 400;
}

.badge-light {
  background: rgba(255, 255, 255, 0.2) !important;
  color: white !important;
  border: 1px solid rgba(255, 255, 255, 0.3);
  font-size: 1rem;
  border-radius: 12px;
  backdrop-filter: blur(10px);
}

/* ======================================== 
   CONTROLS SECTION
   ======================================== */

.controls-section {
  background: white;
  padding: 28px;
  border-radius: 18px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 28px;
  border: 1px solid #e5e7eb;
}

/* ======================================== 
   BOTONES PERSONALIZADOS
   ======================================== */

.btn-custom {
  border-radius: 12px;
  padding: 14px 28px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: none;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
  font-size: 0.9rem;
}

.btn-custom:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.btn-custom:active {
  transform: translateY(-1px);
}
 /*COLOR DE HISTORIAL*/ */
.btn-history {
  background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  color: white;
}

.btn-history:hover {
  background: linear-gradient(135deg, #1e40af, #2563eb);
}

/* ======================================== 
   SEARCH CONTAINER
   ======================================== */

.search-container {
  background: #f8fafc;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  border: 1px solid #e5e7eb;
}

.input-group-text {
  background: white;
  border: 1px solid #cbd5e1;
  color: #64748b;
  border-right: none;
  border-radius: 10px 0 0 10px;
}

.form-control {
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  transition: all 0.3s ease;
  padding: 12px 16px;
  font-size: 0.95rem;
}

.input-group .form-control {
  border-left: none;
  border-radius: 0 10px 10px 0;
}

.form-control:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  outline: none;
}

/* ======================================== 
   LOADING INDICATOR
   ======================================== */

.loading-indicator {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 9999;
  background: rgba(255, 255, 255, 0.98);
  padding: 50px 60px;
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(59, 130, 246, 0.2);
}

.spinner-border {
  width: 3.5rem;
  height: 3.5rem;
  border-width: 0.35rem;
}

.text-primary .spinner-border {
  color: #3b82f6 !important;
}

/* ======================================== 
   TABLE CONTAINER Y TABLA
   ======================================== */

.table-container {
  background: white;
  border-radius: 18px;
  padding: 25px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  margin-bottom: 28px;
  border: 1px solid #e5e7eb;
}

.table-responsive {
  max-height: 600px !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  border-radius: 12px;
  position: relative;
}

.table {
  margin-bottom: 0;
  font-size: 0.9rem;
}

.table th {
  background: linear-gradient(135deg, #334155 0%, #475569 100%);
  color: white;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 18px 15px;
  border: none;
  font-size: 0.85rem;
  position: sticky;
  top: 0;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table td {
  border-color: #f1f5f9;
  padding: 16px 15px;
  vertical-align: middle;
  color: #334155;
  border-bottom: 1px solid #e5e7eb;
}

.table tbody tr {
  transition: all 0.3s ease;
  border-bottom: 1px solid #e5e7eb;
}

.table tbody tr:hover {
  background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%);
  transform: scale(1.01);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* ======================================== 
   BADGES Y ESTADOS
   ======================================== */

.badge {
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.85rem;
  letter-spacing: 0.3px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.badge-primary {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  border: none;
  color: white;
}

.badge-success {
  background: linear-gradient(135deg, #10b981, #059669);
  border: none;
  color: white;
}

.badge-custom {
  font-size: 0.85rem;
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 600;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
}

.status-badge {
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.85rem;
  display: inline-block;
  letter-spacing: 0.3px;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
  transition: all 0.3s ease;
}

.status-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 12px rgba(0, 0, 0, 0.18);
}

/* Estados específicos */
.estado-pendiente,
.status-badge.estado-pendiente {
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  color: white;
}

.estado-activa,
.status-badge.estado-activa {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
}

/* CANDIDATOS EN SELECCIÓN - ROJO URGENTE */
.estado-candidatos-seleccion,
.status-badge.estado-candidatos-seleccion {
  background: linear-gradient(45deg, #DC143C, #FF4500);
  background-size: 400% 400%;
  animation: gradient-alert 3s ease-in-out infinite;
  color: white !important;
  font-weight: bold;
  box-shadow: 0 0 15px rgba(220, 20, 60, 0.8);
  border: 2px solid #FF0000;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
  text-transform: uppercase;
}

@keyframes gradient-alert {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

.estado-candidatos-enviados,
.status-badge.estado-candidatos-enviados {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
}

.estado-plaza-cubierta,
.status-badge.estado-plaza-cubierta {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.estado-contratada,
.status-badge.estado-contratada {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.estado-cvs {
  background: linear-gradient(135deg, #06b6d4, #0891b2);
  color: white;
}

.estado-psico {
  background: linear-gradient(135deg, #a78bfa, #8b5cf6);
  color: white;
}

.estado-rh {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
}

.estado-expediente {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
}

.estado-tecnica {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: white;
}

.estado-prueba {
  background: linear-gradient(135deg, #ec4899, #db2777);
  color: white;
}

.estado-poligrafo {
  background: linear-gradient(135deg, #64748b, #475569);
  color: white;
}

.estado-confirmacion {
  background: linear-gradient(135deg, #14b8a6, #0d9488);
  color: white;
}

/* ======================================== 
   BOTONES DE ACCIÓN
   ======================================== */

.btn-action {
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
  border: none;
  font-size: 0.85rem;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
  margin: 2px;
}

.btn-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.btn-action:active {
  transform: translateY(0);
}

.btn-edit {
  background: linear-gradient(135deg, #06b6d4, #0891b2);
  color: white;
}

.btn-edit:hover {
  background: linear-gradient(135deg, #0891b2, #0e7490);
}

.btn-comment {
  background: linear-gradient(135deg, #06b6d4, #0891b2);
  color: white;
}

.btn-comment:hover {
  background: linear-gradient(135deg, #0891b2, #0e7490);
}

.btn-history-individual {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: white;
}

.btn-history-individual:hover {
  background: linear-gradient(135deg, #7c3aed, #6d28d9);
}

.btn-delete {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
}

.btn-delete:hover {
  background: linear-gradient(135deg, #dc2626, #b91c1c);
}

.btn-expand {
  background: linear-gradient(135deg, #64748b, #475569);
  color: white;
  border-radius: 8px;
  padding: 8px 12px;
}

.btn-expand:hover {
  background: linear-gradient(135deg, #475569, #334155);
}

.btn-Ver-Comentario-Rh {
  background: linear-gradient(135deg, #64748b, #475569);
  color: white;
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 600;
  border: none;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
}

.btn-Ver-Comentario-Rh:hover {
  background: linear-gradient(135deg, #475569, #334155);
}

/* ======================================== 
   EMPTY STATE
   ======================================== */

.empty-state {
  text-align: center;
  padding: 80px 40px;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-radius: 18px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  margin: 25px 0;
  border: 2px dashed #cbd5e1;
}

.empty-state i {
  font-size: 5rem;
  color: #cbd5e1;
  margin-bottom: 25px;
}

.empty-state h4 {
  color: #475569;
  font-weight: 700;
  margin-bottom: 12px;
  font-size: 1.5rem;
}

.empty-state p {
  color: #64748b;
  font-size: 1.05rem;
  margin: 0;
}

/* ======================================== 
   PAGINACIÓN
   ======================================== */

.pagination {
  justify-content: center;
  margin-top: 30px;
  flex-wrap: wrap;
}

.page-item {
  margin: 0 4px;
}

.page-link {
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  padding: 10px 18px;
  color: #334155;
  font-weight: 600;
  transition: all 0.3s ease;
  background: white;
}

.page-link:hover {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  border-color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.page-item.active .page-link {
  background: linear-gradient(135deg, #1e3a8a, #3b82f6);
  border-color: #1e3a8a;
  color: white;
  box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.page-item.disabled .page-link {
  background: #f8fafc;
  border-color: #e5e7eb;
  color: #94a3b8;
}

/* ======================================== 
   SCROLLBAR PERSONALIZADO
   ======================================== */

.table-responsive::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

.table-responsive::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #64748b, #475569);
  border-radius: 10px;
  border: 2px solid #f1f5f9;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #475569, #334155);
}

/* ======================================== 
   MODALES
   ======================================== */

.modal-content {
  border-radius: 20px;
  border: none;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.modal-header {
  border-radius: 20px 20px 0 0;
  padding: 25px 30px;
  border-bottom: 2px solid #e5e7eb;
}

.modal-body {
  padding: 30px;
}

.modal-footer {
  padding: 20px 30px;
  border-top: 2px solid #e5e7eb;
  border-radius: 0 0 20px 20px;
}

.bg-dark {
  background: linear-gradient(135deg, #1e293b, #334155) !important;
}

.bg-gradient-primary {
  background: linear-gradient(135deg, #1e3a8a, #3b82f6) !important;
}

/* Arreglo para inputs bloqueados en modales */
.modal input[type="text"],
.modal input[type="file"], 
.modal input[type="number"],
.modal textarea,
.modal select,
.swal2-popup input,
.swal2-popup textarea,
.swal2-popup select {
  pointer-events: auto !important;
  user-select: text !important;
  -webkit-user-select: text !important;
  -moz-user-select: text !important;
  background-color: #fff !important;
  border: 1px solid #cbd5e1 !important;
  z-index: 9999 !important;
  position: relative !important;
  border-radius: 8px !important;
}

.modal input:focus,
.modal textarea:focus,
.modal select:focus,
.swal2-popup input:focus,
.swal2-popup textarea:focus,
.swal2-popup select:focus {
  outline: none !important;
  border-color: #3b82f6 !important;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
}

/* ======================================== 
   COMENTARIOS Y NOTIFICACIONES
   ======================================== */

.badge-container {
  position: relative;
  display: inline-block;
}

.notification-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 700;
  box-shadow: 0 3px 8px rgba(239, 68, 68, 0.4);
  animation: pulse-notification 2s ease-in-out infinite;
}

.notification-badge.wide {
  border-radius: 12px;
  width: auto;
  min-width: 22px;
  padding: 0 6px;
}

@keyframes pulse-notification {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.15);
  }
}

/* ======================================== 
   CANDIDATOS - LISTA Y CARDS
   ======================================== */

#listaCandidatos {
  display: flex !important;
  flex-direction: column !important;
  gap: 15px !important;
  padding: 15px !important;
}

#listaCandidatos .candidate-card {
  width: 100% !important;
  margin: 0 !important;
  position: relative !important;
  flex-shrink: 0 !important;
}

#listaCandidatos .candidate-card.rh-hidden {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  overflow: hidden !important;
}

#listaCandidatos .card {
  margin: 0 !important;
  width: 100% !important;
  position: relative !important;
}

.candidate-card {
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 0.5rem;
  border-radius: 12px;
}

.candidate-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}

/* Candidatos activos */
.candidato-activo {
  border-left: 4px solid #10b981 !important;
  background-color: white !important;
  position: relative;
}

.candidato-activo .card {
  background-color: white !important;
  border-color: #d1fae5 !important;
}

.candidato-activo::before {
  content: "ACTIVO";
  position: absolute;
  top: 8px;
  right: 8px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 4px 10px;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 6px;
  z-index: 10;
  box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
  letter-spacing: 0.5px;
}

/* Candidatos descartados */
.candidato-descartado {
  border-left: 4px solid #ef4444 !important;
  background-color: #fee2e2 !important;
  border: 1px solid #fecaca !important;
  position: relative;
}

.candidato-descartado .card {
  background-color: #fee2e2 !important;
  border-color: #fecaca !important;
}

.candidato-descartado .card-body {
  background-color: #fee2e2 !important;
}

.candidato-descartado::before {
  content: "DESCARTADO";
  position: absolute;
  top: 8px;
  right: 8px;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
  padding: 4px 10px;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 6px;
  z-index: 10;
  box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
  letter-spacing: 0.5px;
}

.candidato-descartado h6 {
  color: #991b1b !important;
}

.candidato-descartado .text-danger {
  color: #991b1b !important;
}

.candidato-descartado .badge-danger {
  background: linear-gradient(135deg, #ef4444, #dc2626) !important;
  color: white !important;
}

.candidato-descartado .text-muted {
  color: #6b7280 !important;
}

.candidato-descartado .fa-times-circle {
  color: #ef4444 !important;
}

/* Candidatos contratados */
.candidato-contratado .card {
  border-left: 4px solid #10b981 !important;
  background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.candidato-contratado .card:hover {
  background: linear-gradient(135deg, #a7f3d0, #6ee7b7) !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
}

.candidate-card.rh-hidden {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  overflow: hidden !important;
}

/* ======================================== 
   UTILIDADES ADICIONALES
   ======================================== */

.text-primary {
  color: #3b82f6 !important;
}

.text-success {
  color: #10b981 !important;
}

.text-warning {
  color: #f59e0b !important;
}

.text-danger {
  color: #ef4444 !important;
}

.text-info {
  color: #06b6d4 !important;
}

.text-muted {
  color: #64748b !important;
}

/* ======================================== 
   RESPONSIVE
   ======================================== */

@media (max-width: 992px) {
  .main-container {
    margin: 15px;
    padding: 25px;
  }
  
  .header-title {
    font-size: 2rem;
  }
  
  .table-responsive {
    max-height: 500px !important;
  }
}

@media (max-width: 768px) {
  .main-container {
    margin: 10px;
    padding: 20px;
  }
  
  .header-section {
    padding: 20px;
  }
  
  .header-title {
    font-size: 1.8rem;
  }
  
  .header-subtitle {
    font-size: 1rem;
  }
  
  .controls-section {
    padding: 20px;
  }
  
  .btn-custom {
    padding: 12px 20px;
    font-size: 0.85rem;
  }
  
  .table-container {
    padding: 15px;
  }
  
  .table-responsive {
    max-height: 450px !important;
  }
}

@media (max-width: 576px) {
  .main-container {
    margin: 5px;
    padding: 15px;
  }
  
  .header-title {
    font-size: 1.5rem;
  }
  
  .btn-action {
    padding: 8px 12px;
    font-size: 0.8rem;
  }
}

/* ======================================== 
   FIN DE ESTILOS CORPORATIVOS RH
   ======================================== */

/*estilos del historial*/ 
/* Estilos para el historial de proceso de solicitudes */
.swal2-html-container {
    overflow-x: hidden !important;
}

.swal2-html-container::-webkit-scrollbar {
    width: 8px;
}

.swal2-html-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.swal2-html-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.swal2-html-container::-webkit-scrollbar-thumb:hover {
    background: #555;
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
}

.btn-filtro-rapido:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-filtro-rapido.active {
    background-color: #007bff !important;
    color: white !important;
    border-color: #007bff !important;
}

/* ======================================== 
   PESTAÑAS PREMIUM - RH CARDS
   ======================================== */

.tabs-container {
  margin: 30px 0;
  padding: 0 10px;
}

.tabs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  max-width: 1200px;
  margin: 0 auto;
}

.tab-card {
  background: white;
  border-radius: 18px;
  padding: 28px 20px;
  cursor: pointer;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid #e5e7eb;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  gap: 18px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

/* Barra superior de color */
.tab-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #cbd5e1, #94a3b8);
  transition: all 0.3s ease;
}

/* Efecto hover */
.tab-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
  border-color: #3b82f6;
}

.tab-card:hover::before {
  height: 6px;
}

/* Estado activo */
.tab-card.active {
  background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
  color: white;
  border-color: #1e3a8a;
  box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
  transform: translateY(-4px);
}

.tab-card.active::before {
  background: linear-gradient(90deg, #fbbf24, #f59e0b);
  height: 6px;
}

/* Icono de la pestaña */
.tab-icon {
  font-size: 2.8rem;
  flex-shrink: 0;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.tab-card:not(.active) .tab-icon {
  color: #64748b;
}

.tab-card.active .tab-icon {
  color: white;
  animation: pulse 2s ease-in-out infinite;
}

/* Contenido de la pestaña */
.tab-content {
  flex: 1;
  text-align: left;
}

/* Contador de solicitudes */
.tab-count {
  font-size: 2.5rem;
  font-weight: 800;
  line-height: 1;
  margin-bottom: 8px;
  letter-spacing: -1px;
}

.tab-card:not(.active) .tab-count {
  color: #1e293b;
}

.tab-card.active .tab-count {
  color: white;
  animation: pulse 2s ease-in-out infinite;
}

/* Etiqueta de la pestaña */
.tab-label {
  font-size: 1rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.9;
}

.tab-card:not(.active) .tab-label {
  color: #64748b;
}

.tab-card.active .tab-label {
  color: white;
}

/* Colores específicos para cada pestaña */
.tab-card[data-filter="todas"]:not(.active):hover {
  border-color: #3b82f6;
}

.tab-card[data-filter="todas"]:not(.active):hover::before {
  background: linear-gradient(90deg, #3b82f6, #2563eb);
}

.tab-card[data-filter="pendientes"]:not(.active):hover {
  border-color: #f59e0b;
}

.tab-card[data-filter="pendientes"]:not(.active):hover::before {
  background: linear-gradient(90deg, #fbbf24, #f59e0b);
}

.tab-card[data-filter="en-proceso"]:not(.active):hover {
  border-color: #8b5cf6;
}

.tab-card[data-filter="en-proceso"]:not(.active):hover::before {
  background: linear-gradient(90deg, #a78bfa, #8b5cf6);
}

.tab-card[data-filter="plaza-cubierta"]:not(.active):hover {
  border-color: #10b981;
}

.tab-card[data-filter="plaza-cubierta"]:not(.active):hover::before {
  background: linear-gradient(90deg, #34d399, #10b981);
}

/* Animación de pulso para contador activo */
@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

/* Responsive para tablets */
@media (max-width: 992px) {
  .tabs-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Responsive para móviles */
@media (max-width: 576px) {
  .tabs-container {
    padding: 0 5px;
  }
  
  .tabs-grid {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .tab-card {
    padding: 20px 16px;
  }
  
  .tab-icon {
    font-size: 2.2rem;
  }
  
  .tab-count {
    font-size: 2rem;
  }
  
  .tab-label {
    font-size: 0.9rem;
  }
}

/* ======================================== 
   CORRECCIONES FINALES PARA IGUALAR SUPERVISIÓN
   ======================================== */

/* Botón Cambiar Estado - CYAN/TURQUESA */
.btnCambiarEstado,
button.btnCambiarEstado {
  background: linear-gradient(135deg, #06b6d4, #0891b2) !important;
  color: white !important;
}

.btnCambiarEstado:hover,
button.btnCambiarEstado:hover {
  background: linear-gradient(135deg, #0891b2, #0e7490) !important;
}

/* Botón Ver Comentario - GRIS */
.btn-Ver-Comentario-Rh,
button.btn-Ver-Comentario-Rh {
  background: linear-gradient(135deg, #64748b, #475569) !important;
  color: white !important;
}

.btn-Ver-Comentario-Rh:hover,
button.btn-Ver-Comentario-Rh:hover {
  background: linear-gradient(135deg, #475569, #334155) !important;
}

/* Botón Ver Resumen - VERDE */
.btn-history-individual,
button[class*="Resumen"] {
  background: linear-gradient(135deg, #10b981, #059669) !important;
  color: white !important;
}

.btn-history-individual:hover,
button[class*="Resumen"]:hover {
  background: linear-gradient(135deg, #059669, #047857) !important;
}

/* Botón Ver Candidatos - VERDE */
button[class*="Candidatos"] {
  background: linear-gradient(135deg, #10b981, #059669) !important;
  color: white !important;
}

button[class*="Candidatos"]:hover {
  background: linear-gradient(135deg, #059669, #047857) !important;
}

/* Asegurar que todos los botones de acción tengan el estilo correcto */
.btn-action {
  padding: 10px 18px !important;
  border-radius: 10px !important;
  font-weight: 600 !important;
  font-size: 0.85rem !important;
  margin: 3px !important;
}

/* ======================================== 
   CORRECCIÓN PARA LABELS EN MODALES
   ======================================== */

.modal .form-label,
.modal label {
  font-size: 0.9rem !important;
  font-weight: 600 !important;
  color: white !important;
  margin-bottom: 8px !important;
  display: block !important;
}

.modal .form-group label {
  font-size: 0.9rem !important;
}

/* SELECT EN MODALES */
.modal select.form-control,
#nuevoEstado {
  width: 100% !important;
  padding: 12px 16px !important;
  font-size: 0.95rem !important;
  height: auto !important;
  min-height: 48px !important;
}

/* TEXTAREA EN MODALES */
.modal textarea.form-control {
  width: 100% !important;
  padding: 12px 16px !important;
  font-size: 0.95rem !important;
  min-height: 100px !important;
}
/* ======================================== 
   CORRECCIÓN PARA MODAL DE HISTORIAL (SWAL)
   ======================================== */

/* Títulos y encabezados del SweetAlert */
.swal2-title {
  font-size: 1.5rem !important;
  font-weight: 700 !important;
}

/* Contenedor del contenido */
.swal2-html-container {
  font-size: 0.9rem !important;
  line-height: 1.5 !important;
}

/* Labels y texto dentro del modal */
.swal2-html-container label,
.swal2-html-container .form-label {
  font-size: 0.85rem !important;
  font-weight: 600 !important;
  margin-bottom: 6px !important;
}

/* Inputs y selects dentro del SweetAlert */
.swal2-html-container input,
.swal2-html-container select {
  font-size: 0.9rem !important;
  padding: 10px 14px !important;
  height: auto !important;
  min-height: 42px !important;
}

/* Texto de descripción/ayuda */
.swal2-html-container small,
.swal2-html-container .text-muted {
  font-size: 0.8rem !important;
}

/* Sección de configuración */
.swal2-html-container h4,
.swal2-html-container h5 {
  font-size: 1rem !important;
  font-weight: 600 !important;
}

/* Botones dentro del modal */
.swal2-confirm,
.swal2-cancel {
  font-size: 0.9rem !important;
  padding: 10px 24px !important;
}

/* Filtros adicionales */
.swal2-html-container .alert {
  font-size: 0.85rem !important;
  padding: 12px !important;
}

/* Ajustar ancho del modal si es necesario */
.swal2-popup {
  width: 50em !important;
  max-width: 90% !important;
}

/* Select con opciones */
.swal2-html-container select option {
  font-size: 0.9rem !important;
  padding: 8px !important;
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
              Panel de Reclutadores
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


        <!-- PESTAÑAS DE FILTRADO PREMIUM -->
        <div class="tabs-container">
          <div class="tabs-grid">
            <div class="tab-card active" data-filter="todas">
              <div class="tab-icon">
                <i class="fas fa-clipboard-list"></i>
              </div>
              <div class="tab-content">
                <div class="tab-count" id="count-todas">0</div>
                <div class="tab-label">Todas</div>
              </div>
            </div>

            <div class="tab-card" data-filter="pendientes">
              <div class="tab-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div class="tab-content">
                <div class="tab-count" id="count-pendientes">0</div>
                <div class="tab-label">Pendientes</div>
              </div>
            </div>

            <div class="tab-card" data-filter="en-proceso">
              <div class="tab-icon">
                <i class="fas fa-users-cog"></i>
              </div>
              <div class="tab-content">
                <div class="tab-count" id="count-en-proceso">0</div>
                <div class="tab-label">En Proceso</div>
              </div>
            </div>

            <div class="tab-card" data-filter="plaza-cubierta">
              <div class="tab-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="tab-content">
                <div class="tab-count" id="count-plaza-cubierta">0</div>
                <div class="tab-label">Plaza Cubierta</div>
              </div>
            </div>
          </div>
        </div>

      <!-- Controls Section -->
      <div class="controls-section">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="d-flex gap-3">
              <button class="btn btn-custom btn-history btnVerHistorialRh">
                <i class="fas fa-history mr-2"></i>
                Historial De Solicitudes
              </button>
            </div>
          </div>
          <div class="col-md-6">
            <div class="search-container">
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group mb-2">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar en solicitudes...">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group mb-2">
                    <span class="input-group-text"><i class="fas fa-store"></i></span>
                    <input type="text" id="searchTienda" class="form-control" placeholder="Filtrar por tienda...">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading Indicator -->
      <div id="loading-indicator" class="loading-indicator" style="display: none;">
        <div class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
          </div>
          <p class="mt-3 mb-0">Cargando solicitudes...</p>
        </div>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="tblSolicitudes">
            <thead>
              <tr>
                <th width="20">
                  <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th width="80">Tienda</th>
                <th width="120">Puesto</th>
                <th width="120">Supervisor</th>
                <th width="120">Aprobado Por</th>
                <th width="120">Asignado A</th>
                <th width="100">Fecha Solicitud</th>
                <th width="100">Modificación Registrada</th>
                <th width="100">Estado</th>
                <th width="80">Estado de Aprobación</th>
                <th width="150">Razón</th>
                <th width="20">Comentario</th>
                <th width="350">Acciones</th>
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
          <a id="btnPdfIndividual" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Generar PDF
          </a>
          <button class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times"></i> Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>

<!-- MODAL DE CAMBIAR ESTADO DE LA SOLICITUD - CON SECCIÓN DE CANDIDATOS -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-labelledby="tituloEstadoModal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content text-white bg-dark">
      <div class="modal-header">
        <h5 class="modal-title" id="tituloEstadoModal">
          <i class="fas fa-exchange-alt"></i> Cambiar Estado de Solicitud
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
        <!-- Información de la solicitud -->
        <div id="infoSolicitudCambio" class="mb-4">
          <!-- Se carga por JavaScript -->
        </div>

        <!-- Selección de nuevo estado -->
        <div class="form-group mb-4">
          <label for="nuevoEstado" class="form-label">
            <strong><i class="fas fa-clipboard-list mr-2"></i>Nuevo Estado:</strong>
          </label>
          <select id="nuevoEstado" class="form-control form-control-lg">
            <option value="">Seleccione estado...</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Vacante Activa">Vacante Activa</option>
            <option value="Candidatos en Seleccion">Candidatos en Selección</option>
          </select>
        </div>

        <!-- Comentario obligatorio -->
        <div class="form-group mb-4">
          <label for="comentarioCambio" class="form-label">
            <strong><i class="fas fa-comment mr-2"></i>Comentario:</strong> 
            <span class="text-danger">*</span>
          </label>
          <textarea id="comentarioCambio" 
                    class="form-control" 
                    rows="4" 
                    placeholder="Escriba un comentario detallado sobre el cambio de estado..." 
                    required></textarea>
          <small class="text-warning mt-2 d-block">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            El comentario es obligatorio para continuar
          </small>
        </div>

        <!-- Sección de candidatos (aparece solo cuando selecciona "Candidatos en Selección") -->
        <div id="seccionCandidatos" style="display: none;">
          <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Registro de Candidatos en Selección</strong><br>
            Especifique cuántos candidatos están en proceso de selección.
          </div>
          
          <div class="form-group">
            <label for="cantidadCandidatos" class="form-label">
              <strong>Cantidad de candidatos:</strong>
            </label>
            <div class="input-group">
              <input type="number" id="cantidadCandidatos" class="form-control" 
                     min="1" max="10" placeholder="Ej: 3">
              <div class="input-group-append">
                <button type="button" id="btnConfirmarCandidatos" class="btn btn-primary">
                  <i class="fas fa-arrow-right"></i> Continuar
                </button>
              </div>
            </div>
            <small class="text-muted">Máximo 10 candidatos por solicitud</small>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-2"></i>Cancelar
        </button>
        <button type="button" class="btn btn-primary" id="btnGuardarCambioEstado">
          <i class="fas fa-save mr-2"></i>Guardar Cambios
        </button>
      </div>
    </div>
  </div>
</div>

  <!-- Modal de Expedientes de Candidatos - VERSIÓN MEJORADA -->
  <div class="modal fade" id="modalExpedientes" tabindex="-1" aria-labelledby="expedientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width: 95%; width: 1400px;">
      <div class="modal-content">
        <div class="modal-header bg-gradient-primary text-white">
          <h4 class="modal-title" id="expedientesLabel">
            <i class="fas fa-folder-open mr-3"></i>Sistema de Expedientes de Candidatos
          </h4>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0" style="background: #f8f9fa;">
          <div class="row no-gutters h-100">
            <!-- Panel izquierdo - Lista de candidatos -->
            <div class="col-md-4" style="background: white; border-right: 2px solid #dee2e6;">
              <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <h5 class="mb-0 text-primary">
                    <i class="fas fa-users mr-2"></i>Lista de Candidatos
                  </h5>
                  <span class="badge badge-primary" id="totalCandidatos">0</span>
                </div>
                
                <div id="listaCandidatos" style="max-height: calc(100vh - 300px); overflow-y: auto;">
                  <!-- Se carga dinámicamente -->
                </div>
                
                <div class="mt-4 text-center">
                  <button class="btn btn-success btn-lg" id="btnCargarCandidatos">
                    <i class="fas fa-plus-circle mr-2"></i>Agregar Candidatos
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Panel derecho - Expediente del candidato seleccionado -->
            <div class="col-md-8" style="background: #f8f9fa;">
              <div class="p-4">
                <div id="expedienteCandidato">
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
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary btn-lg" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i>Cerrar Sistema
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Comentario -->
  <div class="modal fade" id="modalComentario" tabindex="-1" aria-labelledby="comentarioLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content bg-dark text-white">
        <div class="modal-header">
          <h5 class="modal-title" id="comentarioLabel">
            <i class="fas fa-comment-dots"></i> Comentario
          </h5>
          <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p id="textoComentario"></p>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript Principal -->
  <script>
//=================================================================================
// INICIALIZACIÓN DE TODO EL PROGRAMA
//=================================================================================
$(document).ready(function() {
    window.ROL_USUARIO = 'RRHH';
    window.CANDIDATOS_INDEX = {};
    
    console.log('=== INICIALIZACIÓN RH ===');
    
    // LIMPIAR COMPLETAMENTE ELEMENTOS DE SUPERVISIÓN
    $('#modalExpedientesSupervisor').remove();
    $('#expedienteCandidatoSupervisor').empty();
    $('#modal-backdrop-supervisor').remove();
    $('.modal-backdrop').remove(); // Limpiar cualquier backdrop residual
    
    // LIMPIAR TODOS LOS EVENT HANDLERS PREVIOS
    $(document).off('click', '.btnVerCandidatos');
    $(document).off('click', '.candidate-item-supervisor');
    $(document).off('click', '.candidate-card .card');
    
    // FORZAR LIMPIEZA DE FUNCIONES GLOBALES DE SUPERVISIÓN
    if (window.mostrarModalExpedientesSupervisor) {
        delete window.mostrarModalExpedientesSupervisor;
    }
    if (window.seleccionarCandidatoSupervisior) {
        delete window.seleccionarCandidatoSupervisior;
    }
    
    // ESTABLECER EVENT HANDLERS ESPECÍFICOS DE RH
    $(document).on('click', '.btnVerCandidatosrh', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const idSolicitud = $(this).data('id');
        console.log('=== CLICK RH - Ver candidatos para solicitud:', idSolicitud);
        
        if (idSolicitud) {
            // LLAMAR DIRECTAMENTE A LA FUNCIÓN DE RH
            mostrarCandidatosEnviadosrh(idSolicitud);
        } else {
            Swal.fire('Error', 'ID de solicitud no encontrado', 'error');
        }
    });
    
    console.log('Vista RH configurada correctamente - ROL:', window.ROL_USUARIO);
    

      let solicitudes = [];
      let rowsPerPage = 10;
      let currentPage = 1;
      let chatAbierto = false;
      let modalArchivosAbierto = false;
      let modalResumenAbierto = false;
      let allSolicitudes = [];
      
      // Mostrar fecha actual
      $('#current-date').text(new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      }));



      // =====================================================================
// SISTEMA DE PESTAÑAS DE FILTRADO PARA RH
// =====================================================================
let filtroActual = 'todas';

// Función para actualizar contadores de pestañas
function actualizarContadoresPestanas() {
  if (!allSolicitudes || allSolicitudes.length === 0) return;
  
  const contadores = {
    todas: 0,
    pendientes: 0,
    'en-proceso': 0,
    'plaza-cubierta': 0
  };
  
  allSolicitudes.forEach(solicitud => {
    const estado = (solicitud.ESTADO_SOLICITUD || '').toLowerCase().trim();
    
    // Contar todas
    contadores.todas++;
    
    // Contar pendientes (Pendiente o Vacante Activa)
    if (estado.includes('pendiente') || estado.includes('vacante activa')) {
      contadores.pendientes++;
    }
    
    // Contar en proceso (Candidatos en Selección)
    if (estado.includes('candidatos en seleccion') || estado.includes('candidatos seleccion')) {
      contadores['en-proceso']++;
    }
    
    // Contar plaza cubierta
    if (estado.includes('plaza cubierta')) {
      contadores['plaza-cubierta']++;
    }
  });
  
  // Actualizar los números en las pestañas
  $('#count-todas').text(contadores.todas);
  $('#count-pendientes').text(contadores.pendientes);
  $('#count-en-proceso').text(contadores['en-proceso']);
  $('#count-plaza-cubierta').text(contadores['plaza-cubierta']);
  
  console.log('📊 Contadores actualizados:', contadores);
}

// Función para filtrar solicitudes según la pestaña activa
function filtrarPorPestana() {
  if (!allSolicitudes || allSolicitudes.length === 0) {
    solicitudes = [];
    return;
  }
  
  let solicitudesFiltradas = [];
  
  switch(filtroActual) {
    case 'todas':
      solicitudesFiltradas = [...allSolicitudes];
      break;
      
    case 'pendientes':
      // Pendiente o Vacante Activa
      solicitudesFiltradas = allSolicitudes.filter(s => {
        const estado = (s.ESTADO_SOLICITUD || '').toLowerCase().trim();
        return estado.includes('pendiente') || estado.includes('vacante activa');
      });
      break;
      
    case 'en-proceso':
      // Candidatos en Selección
      solicitudesFiltradas = allSolicitudes.filter(s => {
        const estado = (s.ESTADO_SOLICITUD || '').toLowerCase().trim();
        return estado.includes('candidatos en seleccion') || estado.includes('candidatos seleccion');
      });
      break;
      
    case 'plaza-cubierta':
      // Plaza Cubierta
      solicitudesFiltradas = allSolicitudes.filter(s => {
        const estado = (s.ESTADO_SOLICITUD || '').toLowerCase().trim();
        return estado.includes('plaza cubierta');
      });
      break;
      
    default:
      solicitudesFiltradas = [...allSolicitudes];
  }
  
  solicitudes = solicitudesFiltradas;
  console.log(`🔍 Filtro "${filtroActual}": ${solicitudesFiltradas.length} solicitudes`);
}

// Event listener para cambio de pestañas
$(document).on('click', '.tab-card', function() {
  const nuevoFiltro = $(this).data('filter');
  
  if (nuevoFiltro === filtroActual) return; // No hacer nada si ya está activo
  
  // Actualizar estado visual
  $('.tab-card').removeClass('active');
  $(this).addClass('active');
  
  // Actualizar filtro actual
  filtroActual = nuevoFiltro;
  
  // Aplicar filtro
  filtrarPorPestana();
  
  // Aplicar también el filtro de búsqueda si existe
  const textoBusqueda = $('#searchInput').val().toLowerCase().trim();
  const tiendaBusqueda = $('#searchTienda').val().toLowerCase().trim();
  
  if (textoBusqueda || tiendaBusqueda) {
    solicitudes = solicitudes.filter(s => {
      const cumpleBusqueda = !textoBusqueda || 
        (s.PUESTO_SOLICITADO || '').toLowerCase().includes(textoBusqueda) ||
        (s.SOLICITADO_POR || '').toLowerCase().includes(textoBusqueda) ||
        (s.ESTADO_SOLICITUD || '').toLowerCase().includes(textoBusqueda);
      
      const cumpleTienda = !tiendaBusqueda || 
        (s.NUM_TIENDA || '').toString().toLowerCase().includes(tiendaBusqueda);
      
      return cumpleBusqueda && cumpleTienda;
    });
  }
  
  // Resetear a página 1
  currentPage = 1;
  
  // Renderizar tabla
  if (solicitudes.length === 0) {
    $('#tblSolicitudes').hide();
    $('#empty-state').show();
  } else {
    renderizarTabla(solicitudes);
    renderizarPaginacion(solicitudes);
    $('#tblSolicitudes').show();
    $('#empty-state').hide();
  }
  
  console.log(`✅ Filtro cambiado a: ${nuevoFiltro}`);
});

      // FUNCIÓN PARA CARGAR SOLICITUDES
        function cargarSolicitudes() {
          console.log('Iniciando carga de solicitudes...');
          
          $('#loading-indicator').show();
          $('#tblSolicitudes').hide();
          $('#empty-state').hide();
          
          $.ajax({
            url: './gestionhumana/crudsolicitudesrh.php?action=listar_solicitudes_rh',
            type: 'GET',
            dataType: 'json',
            timeout: 30000,
            success: function (data) {
              // ❌ ELIMINAR ESTA LÍNEA PROBLEMÁTICA:
              // console.error('Error en la respuesta:', data);
              
              // ✅ REEMPLAZAR POR ESTA LÓGICA CORRECTA:
              console.log('Datos recibidos:', data);
              
              // Verificar si son datos válidos (array)
              if (Array.isArray(data)) {
                console.log('Procesando', data.length, 'solicitudes');
                
                allSolicitudes = data;
                solicitudes = data;

                // Cargar opciones únicas del campo DIRIGIDO_A
                const nombresUnicos = [...new Set(data.map(item => item.DIRIGIDO_RH).filter(Boolean))];

                const select = $('#filtroDirigidoA');
                select.empty();
                select.append('<option value="">Todos</option>');

                nombresUnicos.forEach(nombre => {
                  select.append(`<option value="${nombre}">${nombre}</option>`);
                });

                // Aplicar filtro guardado
                const filtroGuardado = localStorage.getItem('filtroDirigidoA') || '';
                $('#filtroDirigidoA').val(filtroGuardado);

                const datosFiltrados = filtroGuardado
                  ? data.filter(item => item.DIRIGIDO_RH === filtroGuardado)
                  : data;

                if (datosFiltrados.length === 0) {
                  $('#loading-indicator').hide();
                  $('#empty-state').show();
                } else {
                  renderizarTabla(datosFiltrados);
                  renderizarPaginacion(datosFiltrados);
                  actualizarContadoresPestanas();
                  $('#loading-indicator').hide();
                  $('#tblSolicitudes').show();
                }
                
              } else if (data && data.success === false) {
                // Error del servidor
                console.error('Error del servidor:', data.error);
                $('#loading-indicator').hide();
                Swal.fire('Error', data.error, 'error');
                
              } else {
                // Formato inesperado
                console.error('Formato inesperado:', data);
                $('#loading-indicator').hide();
                Swal.fire('Error', 'Formato de respuesta no válido', 'error');
              }
            },
            error: function (xhr, status, error) {
              console.error('Error AJAX:', error);
              $('#loading-indicator').hide();
              Swal.fire('Error', 'No se pudieron cargar las solicitudes', 'error');
            }
          });
        }

      // FUNCIÓN PARA RENDERIZAR LA TABLA
      function renderizarTabla(data) {
        const tbody = $('#tblSolicitudes tbody');
        const thead = $('#tblSolicitudes thead');
        console.log("Datos recibidos:", data);
        tbody.empty();

        // Debugging
        if (data.length > 0) {
          console.log("Primer item:", data[0]);
          console.log("Comentario del primer item:", data[0].COMENTARIO_SOLICITUD);
          console.log("Estado Aprobación del primer item:", data[0].ESTADO_APROBACION);
          console.log("Dirigido RH del primer item:", data[0].DIRIGIDO_RH);
        }
        console.log("DEBUG FULL JSON", JSON.stringify(data, null, 2));

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = data.slice(start, end);

        pageData.forEach((item, index) => {
          const globalIndex = start + index;
          
          let statusClass = '';
          const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();

          if (estado.includes('pendiente')) {
            statusClass = 'estado-pendiente';
          } 
          else if (estado.includes('contratada')) {
            statusClass = 'estado-contratada';
          } else if (estado.includes('plaza cubierta') || estado.includes('plaza cubierta')) {
            statusClass = 'estado-plaza-cubierta';
          }
            else if (estado.includes('activa')) {
            statusClass = 'estado-activa';
          } else if (estado.includes('candidatos en seleccion') || estado.includes('candidatos seleccion')) {
            statusClass = 'estado-candidatos-seleccion';
          } else if (estado.includes('candidatos enviados')) {
          statusClass = 'estado-candidatos-enviados';
          } else if (estado.includes('cvs')) {
            statusClass = 'estado-cvs';
          } else if (estado.includes('psico') || estado.includes('psicometrica')) {
            statusClass = 'estado-psico';
          } else if (estado.includes('entrevista rh')) {
            statusClass = 'estado-rh';
          } else if (estado.includes('expediente')) {
            statusClass = 'estado-expediente';
          } else if (estado.includes('tecnica')) {
            statusClass = 'estado-tecnica';
          } else if (estado.includes('prueba')) {
            statusClass = 'estado-prueba';
          } else if (estado.includes('poligrafo')) {
            statusClass = 'estado-poligrafo';
          } else if (estado.includes('confirmacion')) {
            statusClass = 'estado-confirmacion';
          } else if (estado.includes('contratada')) {
            statusClass = 'estado-contratada';
          } else {
            statusClass = 'estado-pendiente'; // default
          }

          // Estados del badge de aprobación
          let aprobacionClass = '';
          const aprobacion = (item.ESTADO_APROBACION || 'Por Aprobar').toLowerCase();
          if (aprobacion.includes('por aprobar')) aprobacionClass = 'estado-pendiente';
          else if (aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no'))) aprobacionClass = 'estado-contratada';
          else if (aprobacion.includes('no aprobado')) aprobacionClass = 'estado-prueba';
          else aprobacionClass = 'estado-contratada'; // Por defecto verde porque RRHH solo ve aprobadas

          const fechaModificacion = item.FECHA_MODIFICACION || '—';
          const comentario = item.COMENTARIO_SOLICITUD || '-';
          const idHistorico = item.ID_HISTORICO;
          const estadoAprobacionMostrar = item.ESTADO_APROBACION || 'Aprobado';

          // Lógica para DIRIGIDO_RH
          const dirigidoRH = item.DIRIGIDO_RH || null;
          const mostrarDirigidoRH = dirigidoRH 
            ? `<span class="text-success"><i class="fas fa-user-check mr-1"></i><strong>${dirigidoRH}</strong></span>`
            : '<span class="text-muted"><i class="fas fa-user-times mr-1"></i>Sin asignación</span>';
        
          // Formatea el comentario para mostrar
          const noLeidos = parseInt(item.NO_LEIDOS) || 0;
          console.log('ID:', idHistorico, 'Comentario:', comentario, 'NO_LEIDOS:', item.NO_LEIDOS);
          console.log('Dirigido RH:', item.ID_SOLICITUD, dirigidoRH);
          
          const comentarioMostrar = comentario !== '-' && idHistorico
            ? `<div class="badge-container">
                  <button class="btn btn-sm btn-info btn-Ver-Comentario-Rh"
                          data-id="${idHistorico}"
                          title="Ver comentario">
                      <i class="fas fa-comment"></i> Ver
                  </button>
                  ${noLeidos > 0 ? `<span class="notification-badge ${noLeidos > 9 ? 'wide' : ''}">${noLeidos}</span>` : ''}
              </div>`
            : '<span class="text-muted">—</span>';

          const row = `
            <tr data-id="${item.ID_SOLICITUD}">
              <td data-label="Abrir">
                <button class="btn btn-expand btn-ver-historial" data-id="${item.ID_SOLICITUD}" title="Ver historial">
                  <i class="fas fa-plus"></i>
                </button>
              </td>

              <td data-label="Tienda"><span class="badge badge-primary">${item.NUM_TIENDA}</span></td>
              <td data-label="Puesto"><strong>${item.PUESTO_SOLICITADO}</strong></td>
              <td data-label="Supervisor"><small class="text-muted">${item.SOLICITADO_POR}</small></td>
              <td data-label="Aprobado por"><small>${item.DIRIGIDO_A || '—'}</small></td>
              <td data-label="Asignado a"><small class="text-info">${mostrarDirigidoRH}</small></td>
              <td data-label="Fecha Solicitud"><small>${item.FECHA_SOLICITUD}</small></td>
              <td data-label="Modificación registrada"><small class="text-muted">${fechaModificacion}</small></td>

              <td data-label="Estado">
                ${(() => {
                  const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();
                  const cantidadCandidatos = item.TOTAL_CANDIDATOS || 0;
                  if (estado.includes('candidatos en seleccion') || estado.includes('candidatos seleccion')) {
                    return `<span class="status-badge estado-candidatos-seleccion">${item.ESTADO_SOLICITUD}</span>`;
                  } else if (estado.includes('candidatos enviados') && cantidadCandidatos > 0) {
                    return `
                      <div style="text-align: center;">
                        <span class="status-badge estado-candidatos-enviados">${item.ESTADO_SOLICITUD}</span>
                        <div style="margin-top: 5px;">
                          <span class="badge badge-success">
                            <i class="fas fa-users"></i> ${cantidadCandidatos} Candidatos
                          </span>
                        </div>
                      </div>
                    `;
                  } else {
                    return `<span class="status-badge ${statusClass}">${item.ESTADO_SOLICITUD}</span>`;
                  }
                })()}
              </td>

              <td data-label="Estado de Aprobación">
                <span class="status-badge ${aprobacionClass}" title="Estado de Aprobación por Gerencia">
                  <i class="fas fa-check-circle"></i> ${estadoAprobacionMostrar}
                </span>
              </td>

              <td data-label="Razón"><small>${item.RAZON || '—'}</small></td>
              <td data-label="Comentario" class="comentario-cell">${comentarioMostrar}</td>

              <td data-label="Acciones">
                <div class="actions-container">
                  ${(() => {
                    const estado = (item.ESTADO_SOLICITUD || '').toLowerCase();
                    
                    // BOTÓN PRINCIPAL: Cambiar Estado
                    let botonesHTML = `
                      <button class="btn btn-action btn-edit btnCambiarEstado"
                        data-id="${item.ID_SOLICITUD}"
                        data-tienda="${item.NUM_TIENDA || ''}"
                        data-puesto="${item.PUESTO_SOLICITADO || ''}"
                        data-razon="${item.RAZON || ''}"
                        data-solicitado-por="${item.SOLICITADO_POR || ''}"
                        title="Cambiar Estado">
                        <i class="fas fa-exchange-alt"></i> Cambiar Estado
                      </button>
                    `;
                    
                    // MOSTRAR BOTÓN DE CANDIDATOS SOLO SI ESTÁN EN "CANDIDATOS ENVIADOS"
                    if (estado.includes('candidatos enviados') || estado.includes('candidatos en seleccion') || estado.includes('plaza cubierta')) {
                      const cantidadCandidatos = item.TOTAL_CANDIDATOS || 0;
                      
                      if (cantidadCandidatos > 0) {
                        botonesHTML += `
                          <button class="btn btn-sm btn-success btnVerCandidatosrh"
                                  data-id="${item.ID_SOLICITUD}"
                                  title="Ver candidatos enviados y sus expedientes">
                            <i class="fas fa-users"></i> Ver Candidatos (${cantidadCandidatos})
                          </button>
                        `;
                      }
                    }
                    
                    // Botón para ver resumen de aprobación gerencial
                      if ((aprobacion === 'aprobado' || (aprobacion.includes('aprobado') && !aprobacion.includes('no')))) {
                          botonesHTML += `
                              <button class="btn btn-success btn-sm btnVerResumenProcesamiento" 
                                      data-id="${item.ID_SOLICITUD}"
                                      title="Ver resumen de aprobación gerencial">
                                  <i class="fas fa-clipboard-check"></i> Ver Resumen
                              </button>`;
                      }
                    
                    return botonesHTML;
                  })()}
                </div>
              </td>
            </tr>
          `;
          
          console.log("Comentario de solicitud:", item.ID_SOLICITUD, item.COMENTARIO_SOLICITUD);
          console.log("Estado Aprobación:", item.ID_SOLICITUD, item.ESTADO_APROBACION);
          console.log("Dirigido RH:", item.ID_SOLICITUD, item.DIRIGIDO_RH);
          tbody.append(row);
        });

        setTimeout(() => {
          $('.estado-selector').each(function () {
            aplicarColorEstado(this);
          });
        }, 0);
      }

      // FUNCIÓN PARA PAGINACIÓN
      function renderizarPaginacion(data) {
              const totalPages = Math.ceil(data.length / rowsPerPage);
              const pagination = $('.pagination');
              
              console.log("📚 Total de páginas:", totalPages); // ← AGREGAR DEBUG
              console.log("📊 Total de datos:", data.length); // ← AGREGAR DEBUG
              
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

              // Event listeners para paginación - CORREGIDO
              $('.pagination .page-link').off('click').on('click', function (e) {
                e.preventDefault();
                const page = parseInt($(this).data('page'));
                
                console.log("👆 Click en página:", page); // ← AGREGAR DEBUG
                console.log("📄 Página actual:", currentPage); // ← AGREGAR DEBUG
                console.log("📊 Datos disponibles:", data.length); // ← AGREGAR DEBUG
                
                if (page && page !== currentPage && page >= 1 && page <= totalPages) {
                  currentPage = page;
                  renderizarTabla(data); // ← CAMBIAR: usar 'data' en lugar de 'solicitudes'
                  renderizarPaginacion(data); // ← CAMBIAR: usar 'data' en lugar de 'solicitudes'
                }
              });
            }

            // Event listener para ver resumen de aprobación gerencial (RH)
    $(document).on('click', '.btnVerResumenProcesamiento', function() {
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

      // EVENTOS DE PAGINACIÓN
      $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        const newPage = parseInt($(this).data('page'));
        if (newPage && newPage !== currentPage && newPage >= 1 && newPage <= Math.ceil(solicitudes.length / rowsPerPage)) {
          currentPage = newPage;
          renderizarTabla();
          renderizarPaginacion();
        }
      });

      // FUNCIÓN PARA FILTROS
      function aplicarFiltros() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const tiendaFilter = $('#searchTienda').val().toLowerCase();

        solicitudes = allSolicitudes.filter(function(solicitud) {
          const matchesSearch = !searchTerm || 
            (solicitud.PUESTO && solicitud.PUESTO.toLowerCase().includes(searchTerm)) ||
            (solicitud.SUPERVISOR && solicitud.SUPERVISOR.toLowerCase().includes(searchTerm)) ||
            (solicitud.ESTADO && solicitud.ESTADO.toLowerCase().includes(searchTerm));

          const matchesTienda = !tiendaFilter || 
            (solicitud.TIENDA && solicitud.TIENDA.toLowerCase().includes(tiendaFilter));

          return matchesSearch && matchesTienda;
        });

        currentPage = 1;
        renderizarTabla();
        renderizarPaginacion();

        if (solicitudes.length === 0) {
          $('#tblSolicitudes').hide();
          $('#empty-state').show();
        } else {
          $('#tblSolicitudes').show();
          $('#empty-state').hide();
        }
      }

      // EVENTOS DE FILTROS
      $('#searchInput, #searchTienda').on('keyup', function() {
        aplicarFiltros();
        filtrarPorPestana();
      });

      // FUNCIÓN PARA CONTADOR DE FILAS
      function updateRowCounter() {
        const total = solicitudes.length;
        const start = (currentPage - 1) * rowsPerPage + 1;
        const end = Math.min(currentPage * rowsPerPage, total);
        
        // Puedes agregar un contador si lo necesitas
        console.log(`Mostrando ${start}-${end} de ${total} solicitudes`);
      }

      // FUNCIONES GLOBALES PARA LOS BOTONES
      window.abrirModalCambioEstado = function(idSolicitud) {
        console.log('Abriendo modal para cambiar estado de solicitud:', idSolicitud);
        $('#modalCambiarEstado').modal('show');
      };

      window.verHistorialSolicitud = function(idSolicitud) {
        console.log('Viendo historial de solicitud:', idSolicitud);
        $('#modalHistorialIndividual').modal('show');
      };

      // EVENTOS DEL MODAL DE CAMBIO DE ESTADO
      $('#nuevoEstado').on('change', function() {
        const estado = $(this).val();
        if (estado === 'Candidatos Enviados') {
          $('#seccionCandidatos').show();
        } else {
          $('#seccionCandidatos').hide();
        }
      });

      $('#btnGenerarFormularios').on('click', function() {
        const cantidad = parseInt($('#cantidadCandidatos').val());
        if (cantidad) {
          generarFormulariosCandidatos(cantidad);
        } else {
          Swal.fire('Error', 'Seleccione la cantidad de candidatos', 'error');
        }
      });

      // FUNCIÓN PARA GENERAR FORMULARIOS DE CANDIDATOS
      function generarFormulariosCandidatos(cantidad) {
        let formulariosHTML = '';
        
        for (let i = 1; i <= cantidad; i++) {
          formulariosHTML += `
            <div class="card mb-3">
              <div class="card-header bg-success text-white">
                <h6 class="mb-0">Candidato ${i}</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" class="form-control candidato-nombre" required>
                  </div>
                  <div class="col-md-6">
                    <label class="font-weight-bold">Apellidos <span class="text-danger">*</span></label>
                    <input type="text" class="form-control candidato-apellidos" required>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-6">
                    <label>Documento (opcional)</label>
                    <input type="text" class="form-control candidato-documento">
                  </div>
                </div>
              </div>
            </div>
          `;
        }
        
        $('#formulariosCandidatos').html(formulariosHTML);
      }

      //====================================================================
      // CHAT EMERGENTE 
      //====================================================================
           $(document).off('click', '.btn-Ver-Comentario-Rh').on('click', '.btn-Ver-Comentario-Rh', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (chatAbierto) return;
                    chatAbierto = true;
                    const idHistorico = $(this).data('id');
                    console.log("🔍 ID Histórico para chat:", idHistorico);
                    
                    if (!idHistorico) {
                        console.error("No se encontró ID histórico");
                        Swal.fire('Error', 'No se encontró el ID del histórico', 'error');
                        return;
                    }

                    function mostrarChat(mensajes) {
                        console.log("📝 Mostrando chat con", mensajes.length, "mensajes");
                        
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
                                const esRRHH = rol.includes('rrhh');
                                const remitente = esRRHH ? 'RRHH' : 'SUPERVISOR';

                                if (esRRHH) {
                                    // Mensaje de RRHH (derecha, azul)
                                    chatHtml += `
                                        <div style="
                                            display: flex;
                                            justify-content: flex-end;
                                            margin-bottom: 15px;
                                        ">
                                            <div style="
                                                max-width: 70%;
                                                background: linear-gradient(135deg, #4285f4 0%, #1976d2 100%);
                                                color: white;
                                                padding: 12px 16px;
                                                border-radius: 18px 18px 4px 18px;
                                                box-shadow: 0 2px 8px rgba(66, 133, 244, 0.3);
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
                                    // Mensaje del supervisor (izquierda, gris)
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
                                    onfocus="this.style.borderColor='#4285f4'"
                                    onblur="this.style.borderColor='#ddd'"
                                ></textarea>
                            </div>
                        `;

                        // Obtener nombre del supervisor desde la fila de la tabla
                        const filaActual = $(`button[data-id="${idHistorico}"]`).closest('tr');
                        const nombreSupervisor = filaActual.find('td:nth-child(4)').text().trim() || 'Supervisor'; 
                        
                        Swal.fire({
                            title: `<i class="fas fa-comments"></i> ${nombreSupervisor}`,
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
                                            background: linear-gradient(135deg, #4285f4 0%, #1976d2 100%) !important;
                                            border: none !important;
                                            border-radius: 8px !important;
                                            padding: 10px 20px !important;
                                            font-weight: 600 !important;
                                            transition: transform 0.2s !important;
                                        }
                                        .chat-send-button:hover {
                                            transform: translateY(-1px) !important;
                                            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.4) !important;
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

                                // CORREGIDO - Usar nombres fijos según el contexto de RRHH
                                  const nombreRRHH = filaActual.find('td:nth-child(6)').text().trim() || 'RRHH'; 
                                  const nombreGerente = 'GERENTE'; //Nombre fijo para el gerente
                                  const esRRHH = true; // Cambiar nombre de variable y valor porque estás en el lado de RRHH
                                  const remitente = nombreRRHH; // El remitente es RRHH

                                $.ajax({
                                    url: './gestionhumana/crudsolicitudesrh.php?action=guardar_respuesta_chat_rh',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        id_historico: idHistorico,
                                        mensaje: nuevoMensaje,
                                        rol: 'RRHH',
                                        remitente: remitente
                                    },
                                    success: function (response) {
                                        console.log("Respuesta del servidor:", response);
                                        if (response && response.success) {
                                            cargarMensajesChat(idHistorico);
                                            actualizarBadgesSilenciosamenteRH(); 
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
                            url: './gestionhumana/crudsolicitudesrh.php?action=get_comentarios_chat_rh',
                            type: 'POST',
                            dataType: 'json',
                            data: { id_historico: idHistorico },
                            success: function (response) {
                                console.log('Respuesta del servidor:', response);
                                if (response && response.success) {
                                  $.ajax({
                                      url: './gestionhumana/crudsolicitudesrh.php?action=marcar_mensajes_leidos_rh',
                                      type: 'POST',
                                      data: { id_historico: idHistorico }
                                  });
                                    mostrarChat(response.mensajes);
                                } else {
                                    console.error("Error en respuesta:", response?.error);
                                    Swal.fire('Error', response?.error || 'Error al cargar mensajes', 'error');
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Error al cargar chat:', xhr.responseText);
                                Swal.fire('Error', 'Error al cargar el chat: ' + error, 'error');
                            }
                        });
                    }

                    cargarMensajesChat(idHistorico);
                    actualizarBadgesSilenciosamenteRH();
                    chatAbierto = false;
                    });

                // FUNCIÓN PARA ACTUALIZAR SOLO LOS BADGES SIN RUIDO VISUAL
                function actualizarBadgesSilenciosamenteRH() {
                    $.ajax({
                        url: './gestionhumana/crudsolicitudesrh.php?action=get_solicitudes',
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
                                            const btnComentario = fila.find('.btn-Ver-Comentario-Rh').parent();
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
// ===========================================================================================================================================
//  FIN DE COMENTARIO EMERGENTE 
// ===========================================================================================================================================
// AGREGADO - Función para cerrar modal
function cerrarModal() {
  $('#modalCambiarEstado').removeClass('show').css('display', 'none');
  $('body').removeClass('modal-open').css('padding-right', '');
  $('.modal-backdrop').remove();
}

// AGREGADO - Cerrar con X
$('#modalCambiarEstado .close').on('click', function() {
  cerrarModal();
});

// AGREGADO - Cerrar con botón Cancelar
$('#modalCambiarEstado .btn-secondary').on('click', function() {
  cerrarModal();
});      

// EVENTOS DE GUARDAR CAMBIO DE ESTADO
      $('#btnGuardarCambioEstado').on('click', function() {
        const nuevoEstado = $('#nuevoEstado').val();
        const comentario = $('#comentarioCambio').val();

        if (!nuevoEstado) {
          Swal.fire('Error', 'Debe seleccionar un nuevo estado', 'error');
          return;
        }

        // Validar candidatos si es necesario
        if (nuevoEstado === 'Candidatos Enviados') {
          const candidatos = [];
          let errores = [];

          $('.candidato-nombre').each(function(index) {
            const nombre = $(this).val().trim();
            const apellidos = $(`.candidato-apellidos:eq(${index})`).val().trim();
            const documento = $(`.candidato-documento:eq(${index})`).val().trim();

            if (!nombre) errores.push(`Candidato ${index + 1}: Nombre obligatorio`);
            if (!apellidos) errores.push(`Candidato ${index + 1}: Apellidos obligatorios`);

            if (nombre && apellidos) {
              candidatos.push({
                nombre: nombre,
                apellidos: apellidos,
                documento: documento
              });
            }
          });

          if (errores.length > 0) {
            Swal.fire('Errores de validación', errores.join('\n'), 'error');
            return;
          }

          if (candidatos.length === 0) {
            Swal.fire('Error', 'Debe registrar al menos un candidato', 'error');
            return;
          }
        }

        // Aquí procesarías el cambio de estado
        Swal.fire('Éxito', 'Estado actualizado correctamente', 'success').then(() => {
          cerrarModal();
          // Limpiar formulario para próxima vez
          $('#nuevoEstado').val('');
          $('#comentarioCambio').val('');
          $('#alertaCandidatosListos').remove();

          // Restaurar botón a estado normal
          const btnGuardar = $('#btnGuardarCambioEstado');
          btnGuardar.removeClass('btn-success btn-lg').addClass('btn-primary');
          btnGuardar.html('Guardar Cambios');
          cargarSolicitudes(); // Recargar datos de las solicitudes con su actualizacion de estado
        });
      });

      // EVENTO PARA CHECKBOX SELECT ALL
      $('#selectAll').on('change', function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
      });

      // CARGAR DATOS AL INICIAR
      cargarSolicitudes();

      // EVENTO PARA HISTORIAL GENERAL
      // ==================================================================================
      // EVENT LISTENER: BOTÓN GENERAR REPORTE DE SOLICITUDES HISTORIAL GENERAL E INDIVIDUAL
      // ==================================================================================
      $(document).off('click', '.btnVerHistorialRh').on('click', '.btnVerHistorialRh', function() {
          
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
                  cargarOpcionesFiltrosHistorialrh();
                  
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
                      url: './gestionhumana/crudsolicitudesrh.php',
                      type: 'GET',
                      data: {
                          action: 'get_proceso_solicitudes',
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

// HISTORIAL GENERAL E INDIVIDUAL DE SOLICITUDES
      // ==================================================================================
      // FUNCIÓN: CARGAR OPCIONES DE FILTROS - VERSIÓN CORREGIDA PARA TU ESTRUCTURA
      // ==================================================================================
      function cargarOpcionesFiltrosHistorialrh() {
          // Cargar tiendas
          $.ajax({
              url: './gestionhumana/crudsolicitudesrh.php?action=get_tiendas_filtro',
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
              url: './gestionhumana/crudsolicitudesrh.php?action=get_supervisores_filtro',
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
              url: './gestionhumana/crudsolicitudesrh.php?action=get_puestos_filtro',
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
                      url: './gestionhumana/exportar_historial_rh.php',
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
                      url: './gestionhumana/exportar_historial_rh.php',
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

//FIN GENERAR HISTORIAL GENERAL E INDIVIDUAL DE SOLICITUDES

    });
  </script>
</body>
</html>