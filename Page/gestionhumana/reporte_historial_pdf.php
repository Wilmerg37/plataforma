<?php
ob_start();
require_once '../../vendor/autoload.php';
require_once '../../Funsiones/global.php'; // ← AGREGAR ESTA LÍNEA
use Dompdf\Dompdf;
use Dompdf\Options;

include_once '../../Funsiones/conexion.php';

$conn = Oracle();
if (!$conn) exit("Error al conectar con Oracle");


// ✅ MAPEO DIRECTO DE CÓDIGOS A NOMBRES
$nombreUsuario = 'Usuario RRHH'; // Valor por defecto

// Obtener el usuario logueado de la sesión
$usuario_logueado = $_SESSION['user'][12] ?? null;

if ($usuario_logueado) {
    // ✅ MAPEO SIMPLE: CÓDIGO => NOMBRE
    $mapeoUsuarios = [
        '5314' => 'Cristy Garcia',
        '5398' => 'Keisha Davila', 
        '5388' => 'Emma de Cea'
    ];
    
    // ✅ BUSCAR EL NOMBRE EN EL MAPEO
    if (isset($mapeoUsuarios[$usuario_logueado])) {
        $nombreUsuario = $mapeoUsuarios[$usuario_logueado];
    } else {
        // ✅ SI NO ESTÁ EN EL MAPEO, USAR EL CÓDIGO
        $nombreUsuario = "Usuario " . $usuario_logueado;
    }
}

// ✅ DEBUG TEMPORAL (quitar después)
error_log("🔍 Usuario código: " . ($usuario_logueado ?? 'NULL'));
error_log("🔍 Nombre asignado: " . $nombreUsuario);

// ✅ DEBUG TEMPORAL - PARA VER QUÉ ESTÁ PASANDO
error_log("🔍 Usuario logueado: " . ($usuario_logueado ?? 'NULL'));
error_log("🔍 Nombre final: " . $nombreUsuario);


// ← NUEVA LÓGICA: Detectar tipo de reporte
$esIndividual = isset($_GET['id']);
$esFiltrado = isset($_GET['fecha_inicial']) && isset($_GET['fecha_final']);

// ✅ MANEJAR FORMATO EXCEL
if (isset($_GET['formato']) && $_GET['formato'] === 'excel') {
    
    $fechaInicial = $_GET['fecha_inicial'];
    $fechaFinal = $_GET['fecha_final'];
    $incluirAprobaciones = isset($_GET['incluir_aprobaciones']) ? (int)$_GET['incluir_aprobaciones'] : 1;
    $incluirEstados = isset($_GET['incluir_estados']) ? (int)$_GET['incluir_estados'] : 1;

    $query = "SELECT 
                h.ID_HISTORICO,
                sp.ID_SOLICITUD,
                sp.NUM_TIENDA,
                sp.PUESTO_SOLICITADO,
                sp.SOLICITADO_POR,
                h.ESTADO_ANTERIOR,
                h.ESTADO_NUEVO,
                h.APROBACION_ANTERIOR,
                h.APROBACION_NUEVA,
                h.COMENTARIO_NUEVO,
                TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO
              FROM ROY_HISTORICO_SOLICITUD h
              JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD
              WHERE sp.ESTADO_APROBACION = 'Aprobado'
                AND TO_DATE(:fecha_inicial, 'YYYY-MM-DD') <= h.FECHA_CAMBIO
                AND h.FECHA_CAMBIO <= TO_DATE(:fecha_final, 'YYYY-MM-DD') + INTERVAL '1' DAY - INTERVAL '1' SECOND";

    if (!$incluirAprobaciones) {
        $query .= " AND (h.APROBACION_ANTERIOR IS NULL AND h.APROBACION_NUEVA IS NULL)";
    }
    
    if (!$incluirEstados) {
        $query .= " AND (h.ESTADO_ANTERIOR IS NULL AND h.ESTADO_NUEVO IS NULL)";
    }

    $query .= " ORDER BY h.FECHA_CAMBIO DESC";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':fecha_inicial', $fechaInicial);
    oci_bind_by_name($stmt, ':fecha_final', $fechaFinal);
    oci_execute($stmt);

    // GENERAR EXCEL
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="historial_filtrado_' . date('Y-m-d_H-i-s') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "\xEF\xBB\xBF";
    echo "<html><head><meta charset='UTF-8'></head><body>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    
    echo "<tr style='background-color: #4CAF50; color: white; font-weight: bold;'>";
    echo "<th>ID Solicitud</th><th>Tienda</th><th>Puesto</th><th>Solicitado Por</th>";
    echo "<th>Estado Anterior</th><th>Estado Nuevo</th><th>Aprobación Anterior</th><th>Aprobación Nueva</th>";
    echo "<th>Comentario</th><th>Fecha Cambio</th></tr>";
    
    while ($row = oci_fetch_assoc($stmt)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ID_SOLICITUD'] ?: '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['NUM_TIENDA'] ?: '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['PUESTO_SOLICITADO'] ?: '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['SOLICITADO_POR'] ?: '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['ESTADO_ANTERIOR'] ?: '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['ESTADO_NUEVO'] ?: '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['APROBACION_ANTERIOR'] ?: 'Por Aprobar') . "</td>";
        echo "<td>" . htmlspecialchars($row['APROBACION_NUEVA'] ?: 'Por Aprobar') . "</td>";
        echo "<td>" . htmlspecialchars($row['COMENTARIO_NUEVO'] ?: '—') . "</td>";
        echo "<td>" . htmlspecialchars($row['FECHA_CAMBIO'] ?: '—') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    echo "<br><small>Reporte generado el " . date('d-m-Y H:i:s') . " por: " . htmlspecialchars($nombreUsuario) . "</small>";
    echo "</body></html>";
    
    oci_free_statement($stmt);
    oci_close($conn);
    exit;
}


// ✅ CONTINUAR CON EL CÓDIGO PDF EXISTENTE...
if ($esIndividual) {
    // Tu lógica existente para reporte individual
    $id_solicitud = $_GET['id'];
    
    $query = "SELECT 
                sp.NUM_TIENDA,
                sp.PUESTO_SOLICITADO,
                sp.SOLICITADO_POR,
                h.ID_HISTORICO,
                h.ESTADO_ANTERIOR,
                h.ESTADO_NUEVO,
                h.APROBACION_ANTERIOR,
                h.APROBACION_NUEVA,
                h.COMENTARIO_ANTERIOR,
                h.COMENTARIO_NUEVO,
                TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                (
                  SELECT LISTAGG(a.NOMBRE_ARCHIVO, ' | ') WITHIN GROUP (ORDER BY a.NOMBRE_ARCHIVO)
                  FROM ROY_ARCHIVOS_SOLICITUD a
                  WHERE a.ID_HISTORICO = h.ID_HISTORICO
                ) AS ARCHIVOS
              FROM ROY_HISTORICO_SOLICITUD h
              JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD
              WHERE h.ID_SOLICITUD = :id
              ORDER BY h.FECHA_CAMBIO DESC";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':id', $id_solicitud);
    oci_execute($stmt);
    
    $titulo = "Historial de Solicitud No. " . htmlspecialchars($id_solicitud);
    $subtitulo = "";

} elseif ($esFiltrado) {
    // ✅ REPORTE FILTRADO (nueva funcionalidad)
    $fechaInicial = $_GET['fecha_inicial'];
    $fechaFinal = $_GET['fecha_final'];
    $incluirAprobaciones = isset($_GET['incluir_aprobaciones']) ? (int)$_GET['incluir_aprobaciones'] : 1;
    $incluirEstados = isset($_GET['incluir_estados']) ? (int)$_GET['incluir_estados'] : 1;

    $query = "SELECT 
                sp.NUM_TIENDA,
                sp.PUESTO_SOLICITADO,
                sp.SOLICITADO_POR,
                h.ID_HISTORICO,
                sp.ID_SOLICITUD,
                h.ESTADO_ANTERIOR,
                h.ESTADO_NUEVO,
                h.APROBACION_ANTERIOR,
                h.APROBACION_NUEVA,
                h.COMENTARIO_ANTERIOR,
                h.COMENTARIO_NUEVO,
                TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                (
                  SELECT LISTAGG(a.NOMBRE_ARCHIVO, ' | ') WITHIN GROUP (ORDER BY a.NOMBRE_ARCHIVO)
                  FROM ROY_ARCHIVOS_SOLICITUD a
                  WHERE a.ID_HISTORICO = h.ID_HISTORICO
                ) AS ARCHIVOS
              FROM ROY_HISTORICO_SOLICITUD h
              JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD
              WHERE sp.ESTADO_APROBACION = 'Aprobado'
                AND TO_DATE(:fecha_inicial, 'YYYY-MM-DD') <= h.FECHA_CAMBIO
                AND h.FECHA_CAMBIO <= TO_DATE(:fecha_final, 'YYYY-MM-DD') + INTERVAL '1' DAY - INTERVAL '1' SECOND";

    // Agregar filtros opcionales
    if (!$incluirAprobaciones) {
        $query .= " AND (h.APROBACION_ANTERIOR IS NULL AND h.APROBACION_NUEVA IS NULL)";
    }
    
    if (!$incluirEstados) {
        $query .= " AND (h.ESTADO_ANTERIOR IS NULL AND h.ESTADO_NUEVO IS NULL)";
    }

    $query .= " ORDER BY h.FECHA_CAMBIO DESC";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':fecha_inicial', $fechaInicial);
    oci_bind_by_name($stmt, ':fecha_final', $fechaFinal);
    oci_execute($stmt);
    
    $titulo = "Historial Filtrado de Solicitudes";
    $subtitulo = "Período: " . date('d/m/Y', strtotime($fechaInicial)) . " - " . date('d/m/Y', strtotime($fechaFinal));

} else {
    // ✅ REPORTE GENERAL (tu lógica original)
    $query = "SELECT 
                sp.NUM_TIENDA,
                sp.PUESTO_SOLICITADO,
                sp.SOLICITADO_POR,
                h.ID_HISTORICO,
                sp.ID_SOLICITUD,
                h.ESTADO_ANTERIOR,
                h.ESTADO_NUEVO,
                h.APROBACION_ANTERIOR,  -- ← AGREGAR CAMPOS DE APROBACIÓN
                h.APROBACION_NUEVA,     -- ← AGREGAR CAMPOS DE APROBACIÓN
                h.COMENTARIO_ANTERIOR,
                h.COMENTARIO_NUEVO,
                TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
                (
                  SELECT LISTAGG(a.NOMBRE_ARCHIVO, ' | ') WITHIN GROUP (ORDER BY a.NOMBRE_ARCHIVO)
                  FROM ROY_ARCHIVOS_SOLICITUD a
                  WHERE a.ID_HISTORICO = h.ID_HISTORICO
                ) AS ARCHIVOS
              FROM ROY_HISTORICO_SOLICITUD h
              JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD  
              ORDER BY h.FECHA_CAMBIO DESC";

    $stmt = oci_parse($conn, $query);
    oci_execute($stmt);
    
    $titulo = "Historial General de Solicitudes";
    $subtitulo = "";
}

// Logo base64 (tu código original)
$logoPath = realpath("logo3.png");
$logoBase64 = base64_encode(file_get_contents($logoPath));
$logoHtml = '<img src="data:image/png;base64,' . $logoBase64 . '" width="100">';

$html = '
<html>
<head>
  <style>
  @page {
    margin: 100px 30px 50px 30px;
  }

  body {
    font-family: Arial, sans-serif;
    font-size: 11px;
  }

  header {
    position: fixed;
    top: -80px;
    left: 0;
    right: 0;
    height: 80px;
    text-align: left;
  }

  header img {
    height: 80px;
    margin-left: 40px;
  }

  .title {
    text-align: center;
    font-size: 22px;
    font-weight: bold;
    margin-top: 20px;
    font-family: "Times New Roman", Times, serif;
    color: #333;
  }

  .subtitle {
    text-align: center;
    font-size: 14px;
    color: #666;
    margin-top: 5px;
    margin-bottom: 20px;
  }

  .info-box {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px;
    margin-bottom: 20px;
    font-size: 10px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  thead {
    background-color: #343a40;
    color: white;
    display: table-header-group;
  }

  th, td {
    border: 1px solid #666;
    padding: 5px;
    text-align: center;
    vertical-align: top;
    font-size: 10px;
  }

  th {
    font-weight: bold;
    text-transform: uppercase;
  }

  td small {
    display: block;
    text-align: left;
    font-size: 9px;
  }

  .badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: bold;
  }

  .badge-success { background-color: #28a745; color: white; }
  .badge-danger { background-color: #dc3545; color: white; }
  .badge-warning { background-color: #ffc107; color: #212529; }
  .badge-info { background-color: #17a2b8; color: white; }

  .footer {
    position: fixed;
    bottom: -30px;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 9px;
    color: #666;
  }
</style>
</head>
<body>
  <header>
    ' . $logoHtml . '
  </header>

  <div class="title">' . $titulo . '</div>';

if ($subtitulo) {
    $html .= '<div class="subtitle">' . $subtitulo . '</div>';
}

$html .= '
  <div class="info-box">
    <strong>Fecha de generación:</strong> ' . date('d/m/Y H:i:s') . ' | 
     <strong>Usuario:</strong> ' . htmlspecialchars($nombreUsuario) . ' |  <!-- ← AQUÍ CAMBIÓ --> 
    <strong>Tipo:</strong> ' . ($esIndividual ? 'Individual' : ($esFiltrado ? 'Filtrado' : 'General')) . '
  </div>

  <table>
    <thead>
      <tr>' . 
        (!$esIndividual ? '<th>ID</th>' : '') . '
        <th>Tienda</th>
        <th>Puesto</th>
        <th>Solicitado por</th>
        <th>Estado Anterior</th>
        <th>Estado Nuevo</th>' . 
        ($esFiltrado || !$esIndividual ? '<th>Aprob. Anterior</th><th>Aprob. Nueva</th>' : '') . '
        <th>Comentario</th>
        <th>Archivos</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>';

$hayDatos = false;
$contador = 0;
while ($row = oci_fetch_assoc($stmt)) {
    $hayDatos = true;
    $contador++;
    
    // Procesar archivos
    $archivosStr = '—';
    if (!empty($row['ARCHIVOS'])) {
        $archivosArray = explode(' | ', $row['ARCHIVOS']);
        $links = array_map(function ($nombre) {
            return htmlspecialchars(basename($nombre));
        }, $archivosArray);
        $archivosStr = implode('<br>', $links);
    }

    // Determinar badges de aprobación
    $aprobAnterior = $row['APROBACION_ANTERIOR'] ?: 'Por Aprobar';
    $aprobNueva = $row['APROBACION_NUEVA'] ?: 'Por Aprobar';
    
    $badgeAnterior = '';
    $badgeNueva = '';
    
    if ($esFiltrado || !$esIndividual) {
        if ($aprobAnterior === 'Aprobado') $badgeAnterior = '<span class="badge badge-success">Aprobado</span>';
        elseif ($aprobAnterior === 'No Aprobado') $badgeAnterior = '<span class="badge badge-danger">No Aprobado</span>';
        else $badgeAnterior = '<span class="badge badge-warning">Por Aprobar</span>';
        
        if ($aprobNueva === 'Aprobado') $badgeNueva = '<span class="badge badge-success">Aprobado</span>';
        elseif ($aprobNueva === 'No Aprobado') $badgeNueva = '<span class="badge badge-danger">No Aprobado</span>';
        else $badgeNueva = '<span class="badge badge-warning">Por Aprobar</span>';
    }

    $html .= '<tr>' . 
                (!$esIndividual ? '<td><strong>' . htmlspecialchars($row['ID_SOLICITUD']) . '</strong></td>' : '') .
                '<td>' . htmlspecialchars($row['NUM_TIENDA']) . '</td>
                <td>' . htmlspecialchars($row['PUESTO_SOLICITADO']) . '</td>
                <td>' . htmlspecialchars($row['SOLICITADO_POR']) . '</td>
                <td>' . htmlspecialchars($row['ESTADO_ANTERIOR'] ?: '—') . '</td>
                <td><strong>' . htmlspecialchars($row['ESTADO_NUEVO'] ?: '—') . '</strong></td>' .
                ($esFiltrado || !$esIndividual ? '<td>' . $badgeAnterior . '</td><td>' . $badgeNueva . '</td>' : '') .
                '<td><small>' . nl2br(htmlspecialchars($row['COMENTARIO_NUEVO'] ?: $row['COMENTARIO_ANTERIOR'] ?: '—')) . '</small></td>
                <td>' . $archivosStr . '</td>
                <td>' . htmlspecialchars($row['FECHA_CAMBIO']) . '</td>
              </tr>';
}

if (!$hayDatos) {
    $colspan = $esIndividual ? '9' : ($esFiltrado ? '11' : '10');
    $html .= '<tr><td colspan="' . $colspan . '">No se encontraron datos para los criterios especificados.</td></tr>';
}

$html .= '</tbody></table>

<div class="footer">
  Página {PAGE_NUM} de {PAGE_COUNT} | Registros encontrados: ' . $contador . ' | Generado por Sistema de Gestión de Personal
</div>

</body></html>';

oci_free_statement($stmt);
oci_close($conn);

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

ob_end_clean();

// Nombre del archivo dinámico
if ($esIndividual) {
    $nombreArchivo = "Historial_Solicitud_{$id_solicitud}.pdf";
} elseif ($esFiltrado) {
    $nombreArchivo = "Historial_Filtrado_" . str_replace('-', '', $fechaInicial) . "_" . str_replace('-', '', $fechaFinal) . ".pdf";
} else {
    $nombreArchivo = "Historial_General_" . date('Y-m-d_H-i-s') . ".pdf";
}

$dompdf->stream($nombreArchivo, ["Attachment" => false]);
?>