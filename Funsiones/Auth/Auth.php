<?php
    require_once "../Funsiones/consulta.php";

    function AuthLogin($user,$pass){
        $query = "SELECT pass FROM user WHERE user = $user AND estado = 1";
        $resultado = consulta(3, $query);
        if(!empty($resultado)){
            if(password_verify($pass,$resultado[0])){
                return 1;
            }
            else{
                return 0;
            }
        }
        else{
            return 2;
        }


    }

    function Usuario($user){

        $query = "SELECT u.id,
                        CONCAT(u.pnombre,' ',u.papellido) nombre,
                        p.id ,
                        p.perfil,
                        u.fechaCreacion,
                        d.departamento,
                        d.numero,
                        u.pais_id,
                        pa.simbolo,
                        pa.impuesto,
                        pa.subsidiaria,
                        u.imagen, u.user
                  FROM user u
                      INNER JOIN perfil p
                          ON u.perfil_id = p.id
                      INNER JOIN departamento d
                        ON u.departamento_id = d.id
                      INNER JOIN pais pa
                        ON u.pais_id = pa.id
                  WHERE u.user = $user
                  AND estado = 1
                ";
        $usuario = consulta(3,$query);

        if(!empty($user)){
            $_SESSION['user'] = $usuario;
            return $_SESSION['user'];
        }
        else{
            return 0;
        }
    }