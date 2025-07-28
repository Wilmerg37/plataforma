<?php
    require_once "../consulta.php";

    $opcion = isset($_POST['opcion']) ? $_POST['opcion'] : '';
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';

    $pnombre = isset($_POST['pnombre']) ? ucwords(strtolower($_POST['pnombre'])) : '';
    $snombre = isset($_POST['snombre']) ? ucwords(strtolower($_POST['snombre'])) : '';
    $papellido = isset($_POST['papellido']) ? ucwords(strtolower($_POST['papellido'])) : '';
    $sapellido = isset($_POST['sapellido']) ? ucwords(strtolower($_POST['sapellido'])) : '';
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : '';
    $pais = isset($_POST['pais']) ? $_POST['pais'] : '';
    $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';
    $departamento = isset($_POST['departamento']) ? $_POST['departamento'] : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
    $confirPass = isset($_POST['confirmarPass']) ? $_POST['confirmarPass'] : '';
    $foto = isset($_FILES['foto']) ? $_FILES['foto'] : ''; ;

    $path = "../../Image/user/$pais/";


    switch ($opcion) {
        case 1:
            $query = " SELECT u.id,
                              CONCAT(u.pnombre,' ',u.snombre) nombre,
                              CONCAT(u.papellido,' ',u.sapellido) apellido,
                              u.pnombre,
                              u.snombre,
                              u.papellido,
                              u.sapellido,
                              u.email,
                              u.user,
                              u.imagen,
                              DATE(u.fechaCreacion),
                              d.id iddepto,
                              CONCAT(d.departamento,' ',IFNULL(d.numero,'')) departamento,
                              per.id idperfil,
                              per.perfil,
                              p.id idpais,
                              p.pais,
                              u.estado
                        FROM user u
                            INNER JOIN pais p
                                ON 	u.pais_id = p.id
                            INNER JOIN perfil per
                                ON u.perfil_id = per.id
                            INNER JOIN departamento d
                                ON u.departamento_id = d.id
                        ORDER BY u.id
                        ";

            $resultado = consulta(1,$query);
            break;
        case 2:
            if(!VerificacionExistenciaCodigo($codigo)){
              if(ValidarPass($pass, $confirPass)){
                $pass = password_hash($pass, PASSWORD_DEFAULT);

                if(empty($_FILES['foto']['name'])){
                  $name = null;
                }
                else{
                  $name = $codigo . '.jpg';
                }

                $query = "INSERT INTO user (pnombre, snombre, papellido, sapellido, email, user, pass, estado, imagen, pais_id,perfil_id, departamento_id)
                                      VALUES('$pnombre','$snombre' ,'$papellido','$sapellido','$correo',$codigo,'$pass',1,'$name',$pais,$perfil,$departamento)
                                    ";
                $resultado = consulta(2, $query);
                SubirFoto($foto, $path, $name);
              }
              else{
                $resultado = 'PassError';
              }
            }
            else{
              $resultado = 'existe';
            }
            break;
        case 3:
            if (empty($_FILES['foto']['name'])) {
              $name = null;
              $query = "UPDATE user
                        SET pnombre = '$pnombre',
                            snombre = '$snombre',
                            papellido = '$papellido',
                            sapellido = '$sapellido',
                            email = '$correo',
                            user = $codigo,
                            pass = $pass,
                            pais_id = $pais,
                            perfil_id = $perfil,
                            departamento_id = $departamento
                        WHERE id = $id
                    ";
            } else {
              $name = $codigo . '.jpg';
              $query = "UPDATE user
                        SET pnombre = '$pnombre',
                            snombre = '$snombre',
                            papellido = '$papellido',
                            sapellido = '$sapellido',
                            email = '$correo',
                            user = $codigo,
                            pass = $pass,
                            pais_id = $pais,
                            perfil_id = $perfil,
                            departamento_id = $departamento,
                            imagen = '$name'
                        WHERE id = $id
                     ";
              SubirFoto($foto, $path, $name);
            }

            if($consulta = consulta(2,$query)){
                $resultado = registroLog($query);
            }
            else{
                $resultado = $consulta;
            }
            break;
        case 4:

            $query = "SELECT estado FROM user WHERE id = $id";
            $consulta = consulta(3,$query);

            if ($consulta[0]) {
              $estado = 0;
            } else {
              $estado = 1;
            }
            $query = "UPDATE user
                        SET estado = $estado
                        WHERE id = $id
                    ";
            if($consulta = consulta(2,$query)){
                $resultado = registroLog($query);
            }
            else{
                $resultado = $consulta;
            }
            break;
        case 5:
          $query = " SELECT
                    pnombre,
                    snombre,
                    papellido,
                    sapellido,
                    email,
                    user,                    
                    imagen,
                    departamento_id,
                    perfil_id,
                    pais_id
              FROM user u
              WHERE id = $id
              ";

            $resultado = consulta(3, $query);
            break;
        default:

            break;
    }

    print json_encode($resultado,JSON_UNESCAPED_UNICODE);



    function SubirFoto($foto,$path,$name){
      if (file_exists($path)) {
        if ($foto["error"] == UPLOAD_ERR_OK) {
          $tmp_name = $foto["tmp_name"];
          move_uploaded_file($tmp_name, $path.$name);
        }
      }
      else{
        echo "La carpeta no existe";
      }
    }

    function VerificacionExistenciaCodigo($codigo){
      $query = "SELECT user FROM user WHERE user = $codigo";
      $resultado = consulta(3,$query);
      return !empty($resultado) ? true : false;
    }

    function ValidarPass($pass, $confirPass){
      if($pass === $confirPass){
        return true;
      }
      else{
        return false;
      }

    }