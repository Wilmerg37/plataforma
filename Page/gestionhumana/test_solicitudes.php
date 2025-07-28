<?php
$conn = oci_connect("USUARIO", "CONTRASEÑA", "TU_SERVICIO");

$query = "
    SELECT 
        s.ID_SOLICITUD,
        s.NUM_TIENDA,
        s.PUESTO_SOLICITADO,
        s.ESTADO_SOLICITUD,
        TO_CHAR(s.FECHA_SOLICITUD, 'DD-MM-YYYY') AS FECHA_SOLICITUD,
        TO_CHAR(s.FECHA_MODIFICACION, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_MODIFICACION,
        s.SOLICITADO_POR,
        s.RAZON,
        s.TIENE_ARCHIVOS,
        s.CVS_DISPONIBLES,
        s.COMENTARIO_SOLICITUD AS COMENTARIO
    FROM ROY_SOLICITUD_PERSONAL s
    ORDER BY s.ID_SOLICITUD DESC";

$stmt = oci_parse($conn, $query);
oci_execute($stmt);

$solicitudes = [];
while ($row = oci_fetch_assoc($stmt)) {
    $solicitudes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($solicitudes, JSON_PRETTY_PRINT);

oci_free_statement($stmt);
oci_close($conn);
?>
