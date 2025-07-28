<?php
require_once "../consulta.php";

$tienda = $_SESSION['user'][6];
$sbs = $_SESSION['user'][7];
$filtro;


is_null($_SESSION['user'][6]) ? $filtro = '' : $filtro = 'WHERE c.no_tienda =' . $tienda;


    $query = "  SELECT c.id_corte, FORMAT(C.Fecha_Corte, 'dd/MM/yyyy') AS Fecha_Corte,'T-' + RIGHT('000' + CAST(c.no_tienda AS VARCHAR), 3) AS tienda, t.descripcion tipo, c.no_transaccion no_boleta, c.monto, b.nombre
  FROM Tb_Corte c
      INNER JOIN Tb_Bancos_corte b
          ON B.Id_Banco = C.Tb_Bancos_corte_Id_Banco 
      INNER JOIN Tb_Formapago t
          ON t.Id_Pago = C.Tb_Formapago_Id_Pago                   
  $filtro
  ORDER BY c.id_corte DESC
 OFFSET 0 ROWS FETCH NEXT 20 ROWS ONLY
              ";

  $resultado = consultaSQLServer(3, $query);

  print json_encode($resultado, JSON_UNESCAPED_UNICODE);
