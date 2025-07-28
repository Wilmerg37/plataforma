<?php
    require_once "../consulta.php";
    require_once "../global.php";

    $opcion = isset($_POST['opcion']) ? $_POST['opcion'] : '';
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    $fechaDeposito = isset($_POST['fechaDeposito']) ? $_POST['fechaDeposito'] : '';
    $tiendaDeposito = isset($_POST['tiendaDeposito']) ? $_POST['tiendaDeposito'] : '';
    $tipoDeposito = isset($_POST['tipoDeposito']) ? $_POST['tipoDeposito'] : '';
    $noDeposito = isset($_POST['noDeposito']) ? $_POST['noDeposito'] : '';
    $montoDeposito = isset($_POST['montoDeposito']) ? str_replace([',','Q'],'',$_POST['montoDeposito']) : '';
    $bancoDeposito = isset($_POST['bancoDeposito']) ? $_POST['bancoDeposito'] : '';
    $comentario = isset($_POST['comentario']) ? $_POST['comentario'] : '';
    $explicacion = isset($_POST['explicacionDeposito']) ? $_POST['explicacionDeposito'] : '';

    $fi = isset($_POST['fechas']) ? date('Y-m-d',strtotime(substr($_POST['fechas'],0,-13))) : '';
    $ff = isset($_POST['fechas']) ? date('Y-m-d',strtotime(substr($_POST['fechas'],-10))) : '';



    switch ($opcion) {
        case 1:
            
            $query = "SELECT 
	          C.Id_Corte, FORMAT(C.Fecha_Corte, 'dd/MM/yyyy') AS Fecha_Corte,    'T' + RIGHT('000' + CAST(C.No_Tienda AS VARCHAR), 3) AS No_Tienda, P.Descripcion, C.No_Transaccion, C.Monto, B.Nombre 
	          FROM Tb_Corte C 
	          JOIN Tb_Bancos_corte B 
	          ON B.Id_Banco = C.Tb_Bancos_corte_Id_Banco 
	          JOIN Tb_Formapago P 
	          ON P.Id_Pago = C.Tb_Formapago_Id_Pago    
                      WHERE C.FECHA_CORTE BETWEEN '$fi' AND '$ff'                      
	          ORDER BY C.Fecha_Creacion DESC
                        ";

            $resultado = consultaSQLServer(3,$query);
            break;
        case 2:
            $query = "INSERT INTO Tb_Corte (NO_TRANSACCION, MONTO, FECHA_CORTE, OBSERVACION, TB_BANCOS_CORTE_ID_BANCO, TB_FORMAPAGO_ID_PAGO, NO_TIENDA)
                        VALUES('$noDeposito',$montoDeposito,'$fechaDeposito','$comentario',$bancoDeposito,$tipoDeposito,$tiendaDeposito)
                     ";
            $resultado = consultaSQLServer(2,$query);
            break;
        case 3:

            $query = "UPDATE Tb_Corte
                        SET NO_TRANSACCION = '$noDeposito',
                            MONTO = $montoDeposito,
                            FECHA_CORTE = CONVERT(datetime, '$fechaDeposito', 120),
                            TB_BANCOS_CORTE_ID_BANCO = $bancoDeposito ,
                            TB_FORMAPAGO_ID_PAGO = $tipoDeposito,
                             NO_TIENDA = $tiendaDeposito
                        WHERE Id_Corte = $id
                     ";
            if($consulta = consultaSQLServer(2,$query)){
                $resultado = registroLog($query,$explicacion);
            }
            else{
                $resultado = $consulta;
            }
            break;
        case 4:

            $query = "DELETE
                        FROM Tb_Corte
                        WHERE Id_Corte = $id
                     ";
            if($consulta = consultaSQLServer(2,$query)){
                $resultado = registroLog($query,$explicacion);
            }
            else{
                $resultado = $consulta;
            }
            break;
        case 5:
          $query = "SELECT NO_TRANSACCION, MONTO, CONVERT(varchar(10), Fecha_Corte, 23) AS Fecha_Corte, OBSERVACION,TB_BANCOS_CORTE_ID_BANCO, TB_FORMAPAGO_ID_PAGO, NO_TIENDA
                    FROM Tb_Corte
                    WHERE Id_Corte = $id
                    ";
          $resultado = consultaSQLServer(3, $query);
       

         if (is_array($resultado) && count($resultado) > 0) {
      $resultado = array_values($resultado[0]);
  }
            break;
    }

    print json_encode($resultado,JSON_UNESCAPED_UNICODE);