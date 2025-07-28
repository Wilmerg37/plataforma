<?php
    require_once "consulta.php";

    function listadoTienda()
    {
        $query ="SELECT  SID, STORE_NO FROM RPS.STORE
                  WHERE SBS_SID = '680861302000159257'
                  AND STORE_NO <> 0
                  ORDER BY STORE_NO";
        $tienda = consultaOracle(3, $query);
        foreach ($tienda as $key) {
          echo  '<option value="' . $key[1] . '">' . $key[1] . '</option>';
        }
    }

   function listadoBanco()
{
    $query = "SELECT ID_BANCO, NOMBRE FROM Tb_Bancos_corte";
    $banco = consultaSQLServer(3, $query);
    foreach ($banco as $key) {
        echo '<option value="' . $key[0] . '">' . $key[1] . '</option>';
    }
}


    function listadoTipoDeposito()
    {
        $query ="SELECT ID_PAGO, Descripcion FROM Tb_Formapago ";
        $tipopago = consultaSQLServer(3, $query);
        foreach ($tipopago as $key) {
          echo  '<option value="' . $key[0] . '">' . $key[1] . '</option>';
        }
    }

    function listadoPais()
    {
      $query = "SELECT id,pais FROM pais";
      $pais = consulta(1,$query);
      foreach ($pais as $key) {
        echo  "<option value=" . $key[0] . ">" . $key[1] ."</option>";
      }
    }

    function listadoDepartamento()
    {
      $query = "SELECT id,departamento, numero FROM departamento";
      $departamento = consulta(1,$query);
      foreach ($departamento as $key) {
        echo  "<option value=" . $key[0] . ">" . $key[1] . " " . $key[2] . "</option>";
      }
    }

    function listadoPerfil()
    {
      $query = "SELECT id,perfil FROM perfil";
      $perfil = consulta(1, $query);
      foreach ($perfil as $key) {
        echo  "<option value=" . $key[0] . ">" . $key[1] . "</option>";
      }
    }

    function listadoPerfilTds()
    {
      $query = "SELECT id,perfil FROM perfil where id in (9,10,11,12,13)";
      $perfil = consulta(1, $query);
      foreach ($perfil as $key) {
        echo  "<option value=" . $key[0] . ">" . $key[1] . "</option>";
      }
    }

    function Subsidiaria()
    {
      $query = "SELECT subsidiaria,nombre_sbs FROM pais
                 where subsidiaria = '1'
      order by id";
      $sbs = consulta(1, $query);
      foreach ($sbs as $key) {
        echo  "<option value=" . $key[0] . ">" . $key[0] .' - '. $key[1] . "</option>";
      }
    }

  
    function departamento($op,$num = 0){
        switch ($op) {
            case 'Tienda':
                return "Tienda - ".$num;
                break;

            default:
                return $op;
                break;
        }
    }

    function buscarTienda($departamento_id){
      $query = "SELECT numero FROM departamento WHERE id = $departamento_id";
      return consulta(3,$query);
    }

    function imagenPerfil($pais,$imagen){
        if(empty($pais) || empty($imagen)){
          echo "../Image/user/default.svg";
        }
        else{
          echo "../Image/user/$pais/$imagen"."?ver=".time();
        }
    }
    function nombrePerfil($nombrePerfil)
    {
      echo ucwords(strtolower($nombrePerfil));
    }

    function modulos($perfil){
      $query = "SELECT m.id,m.icono,m.modulo
                FROM 	acceso a

                  INNER JOIN pagina p
                    ON a.pagina_id = p.id
                  INNER JOIN modulo m
                    ON p.modulo_id = m.id
                WHERE a.perfil_id = $perfil
                GROUP BY m.id";
      return consulta(1, $query);
    }

  function paginas($modulo)
  {
    $query = "SELECT CONCAT(m.id,'-',p.id), p.pagina, p.icono
              FROM 	acceso a
                INNER JOIN pagina p
                  ON a.pagina_id = p.id
                INNER JOIN modulo m
                  ON p.modulo_id = m.id
              WHERE m.id = $modulo
              GROUP BY CONCAT(m.id,'-',p.id), p.pagina, p.icono
               ORDER BY p.id
              "; //se agrega para agrupar listado de opciones en el menu HWG19062024
    return consulta(1, $query);
  }
