<?php
ob_start();
require_once '../../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include_once '../../Funsiones/conexion.php';
$conn = Oracle();
if (!$conn) exit("Error al conectar con Oracle");

$esIndividual = isset($_GET['id']);
$id_solicitud = $esIndividual ? $_GET['id'] : null;

$query = "SELECT 
            sp.NUM_TIENDA,
            sp.PUESTO_SOLICITADO,
            sp.SOLICITADO_POR,
            h.ID_HISTORICO,
            h.ESTADO_ANTERIOR,
            h.ESTADO_NUEVO,
            h.COMENTARIO_ANTERIOR,
            h.COMENTARIO_NUEVO,
            TO_CHAR(h.FECHA_CAMBIO, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CAMBIO,
            (
              SELECT LISTAGG(a.NOMBRE_ARCHIVO, ' | ') WITHIN GROUP (ORDER BY a.NOMBRE_ARCHIVO)
              FROM ROY_ARCHIVOS_SOLICITUD a
              WHERE a.ID_HISTORICO = h.ID_HISTORICO
            ) AS ARCHIVOS
          FROM ROY_HISTORICO_SOLICITUD h
          JOIN ROY_SOLICITUD_PERSONAL sp ON h.ID_SOLICITUD = sp.ID_SOLICITUD";

if ($esIndividual) $query .= " WHERE h.ID_SOLICITUD = :id";
$query .= " ORDER BY h.FECHA_CAMBIO DESC";

$stmt = oci_parse($conn, $query);
if ($esIndividual) oci_bind_by_name($stmt, ':id', $id_solicitud);
oci_execute($stmt);

// Logo base64
$logoPath = realpath("logo3.png");
$logoBase64 = base64_encode(file_get_contents($logoPath));
$logoHtml = '<img src="data:image/png;base64,' . $logoBase64 . '" width="100">';

$titulo = $esIndividual ? "Historial de Solicitud No. " . htmlspecialchars($id_solicitud) : "Historial de Solicitudes";

$html = '
<html>
<head>
  <style>
  @page {
    margin: 100px 30px 50px 30px;
  }

  body {
    font-family: Arial, sans-serif;
    font-size: 12px;
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
    font-size: 24px;
    font-weight: bold;
    margin-top: 20px;
    font-family: "Times New Roman", Times, serif;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
  }

  thead {
    background-color: #f2f2f2;
    display: table-header-group;
  }

  th, td {
    border: 1px solid #666;
    padding: 6px;
    text-align: center;
    vertical-align: top;
  }

  td small {
    display: block;
    text-align: left;
    font-size: 11px;
  }
</style>

</head>
<body>
  <header>
    ' . $logoHtml . '
  </header>

  <div class="title">' . $titulo . '</div>

  <table>
    <thead>
      <tr>
        <th>Tienda</th>
        <th>Puesto</th>
        <th>Solicitado por</th>
        <th>Estado Anterior</th>
        <th>Estado Nuevo</th>
        <th>Comentario Anterior</th>
        <th>Comentario Nuevo</th>
        <th>Archivos Adjuntos</th>
        <th>Fecha del Cambio</th>
      </tr>
    </thead>
    <tbody>';

$hayDatos = false;
while ($row = oci_fetch_assoc($stmt)) {
    $hayDatos = true;
    $archivosStr = '-';
    if (!empty($row['ARCHIVOS'])) {
        $archivosArray = explode(' | ', $row['ARCHIVOS']);
        $links = array_map(function ($nombre) {
            return htmlspecialchars(basename($nombre));
        }, $archivosArray);
        $archivosStr = implode('<br>', $links);
    }

    $html .= '<tr>
                <td>' . htmlspecialchars($row['NUM_TIENDA']) . '</td>
                <td>' . htmlspecialchars($row['PUESTO_SOLICITADO']) . '</td>
                <td>' . htmlspecialchars($row['SOLICITADO_POR']) . '</td>
                <td>' . htmlspecialchars($row['ESTADO_ANTERIOR']) . '</td>
                <td><strong>' . htmlspecialchars($row['ESTADO_NUEVO']) . '</strong></td>
                <td><small>' . nl2br(htmlspecialchars($row['COMENTARIO_ANTERIOR'])) . '</small></td>
                <td><small>' . nl2br(htmlspecialchars($row['COMENTARIO_NUEVO'])) . '</small></td>
                <td>' . $archivosStr . '</td>
                <td>' . htmlspecialchars($row['FECHA_CAMBIO']) . '</td>
              </tr>';
}

if (!$hayDatos) {
    $html .= '<tr><td colspan="9">No se encontraron cambios para esta solicitud.</td></tr>';
}

$html .= '</tbody></table></body></html>';

oci_free_statement($stmt);
oci_close($conn);

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

ob_end_clean();
$nombreArchivo = $esIndividual ? "Historial_Solicitud_{$id_solicitud}.pdf" : "Historial_Solicitudes.pdf";
$dompdf->stream($nombreArchivo, ["Attachment" => false]);
