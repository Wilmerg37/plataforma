<?php
    session_start();
    require_once "conexion.php";


    function consulta($op, $query){
        switch ($op) {
            case 1:
                $consulta = mysqli_query(MariaDB(),$query);
                $resultado  = mysqli_fetch_all($consulta,MYSQLI_BOTH);
                break;

            case 2:
                $consulta = mysqli_query(MariaDB(),$query);
                $resultado = $consulta;
                break;
            case 3:
                $consulta = mysqli_query(MariaDB(),$query);
                $resultado  = mysqli_fetch_row($consulta);
                break;
            case 4:
              $consulta = mysqli_query(Mysql(), $query);
              $resultado  = mysqli_fetch_row($consulta);
              break;
            default:
                break;
        }
        return $resultado;
        mysqli_free_result($consulta);
    }

   function consultaOracle($opcion, $query, $params = []) {
      $conn = Oracle(); // Tu función de conexión
      $consulta = oci_parse($conn, $query);
  
      // Si se reciben parámetros, hacer bind
      foreach ($params as $key => $val) {
          oci_bind_by_name($consulta, $key, $params[$key]);
      }
  
      switch ($opcion) {
          case 1: // VIEJO UPDATE sin commit (no recomendado ya)
              oci_execute($consulta); // ¡NO hace commit!
              $resultado = oci_fetch_row($consulta); // innecesario, pero se conserva para compatibilidad
              break;
  
          case 2: // Solo ejecuta
              $resultado = oci_execute($consulta);
              break;
  
          case 3: // SELECT con retorno de filas
              oci_execute($consulta);
              $resul = oci_fetch_all($consulta, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_NUM);
              $resultado = $res;
              break;
  
          case 4: // NUEVO: UPDATE/INSERT/DELETE con commit y soporte de parámetros
              $ok = oci_execute($consulta, OCI_COMMIT_ON_SUCCESS);
              oci_free_statement($consulta);
              oci_close($conn);
              return $ok;

         case 5: // SELECT con retorno de filas
              oci_execute($consulta);
            $resul = oci_fetch_all($consulta, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
              $resultado = $res;
              break;

          default:
              $resultado = false;
              break;
      }
  
      oci_free_statement($consulta);
      oci_close($conn);
      return $resultado;
  }

function consultaSQLServer($opcion, $query, $params = []) {
    $conn = SqlServerConnection(); // Tu función de conexión a SQL Server

    // Preparar la consulta
    $stmt = sqlsrv_prepare($conn, $query, $params);

    if (!$stmt) {
        echo "Error al preparar la consulta:" . PHP_EOL;
        print_r(sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    switch ($opcion) {
        case 1: // UPDATE sin commit manual (no aplica igual en SQL Server con sqlsrv)
            $resultado = sqlsrv_execute($stmt);
            break;

        case 2: // Solo ejecutar (INSERT, UPDATE, DELETE sin necesidad de fetch)
            $resultado = sqlsrv_execute($stmt);
            break;

        case 3: // SELECT que retorna todas las filas
            if (sqlsrv_execute($stmt)) {
                $resultado = [];
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC)) {
                    $resultado[] = $row;
                }
            } else {
                $resultado = false;
            }
            break;

        case 4: // INSERT/UPDATE/DELETE con commit automático (sqlsrv hace commit implícito)
            $ok = sqlsrv_execute($stmt);
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);
            return $ok;

        default:
            $resultado = false;
            break;
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $resultado;
}



    function registroLog($consulta,$explicacion="Sin comentarios"){
      $id_session = isset($_SESSION['user']) ? $_SESSION['user'][0] : '';
      $tipo = substr($consulta,0,6);
      $tabla = "";

      switch ($tipo) {
          case 'INSERT':
              $tabla = trim(substr($consulta,strpos($consulta,'INTO')+5,(strpos($consulta,'(') - (strpos($consulta,'INTO')+5) )));
              break;
          case 'UPDATE':
              $tabla = trim(substr($consulta,strpos($consulta,'UPDATE')+6,(strpos($consulta,'SET') - (strpos($consulta,'UPDATE')+6))));
              break;
          case 'DELETE':
              $tabla = trim(substr($consulta,strpos($consulta,'FROM')+4,(strpos($consulta,'WHERE') - (strpos($consulta,'FROM')+4))));
              break;
          default:
              break;
      }
      $consulta = str_replace("'","\'",$consulta);
      $query = "INSERT INTO log(tipo, tabla, explicacion, query, user_id)
                  VALUES('$tipo','$tabla','$explicacion','$consulta',$id_session);
              ";
      $resultado = mysqli_query(MariaDB(),$query);
      return $resultado;
      mysqli_free_result($resultado);
    }
