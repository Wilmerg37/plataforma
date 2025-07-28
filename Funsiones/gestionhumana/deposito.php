<?php
require_once "../consulta.php";

$tienda = $_SESSION['user'][6];
$sbs = $_SESSION['user'][7];
$filtro;


is_null($_SESSION['user'][6]) ? $filtro = '' : $filtro = 'WHERE d.numero =' . $tienda;


    $query = "SELECT c.id, c.fechaCorte, concat('T-',lpad(d.numero,3,'0'))tienda, t.descripcion tipo, c.transaccion no_boleta, c.monto, b.banco
              FROM corte c
                  INNER JOIN banco b
                      ON c.banco_id = b.id
                  INNER JOIN tipopago t
                      ON c.tipopago_id = t.id
                  INNER JOIN departamento d
                      ON c.departamento_id = d.id
              $filtro
              ORDER BY c.id DESC
              LIMIT 100
              ";

  $resultado = consulta(1, $query);

  print json_encode($resultado, JSON_UNESCAPED_UNICODE);
