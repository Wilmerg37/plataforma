<?php
  require_once "consulta.php";

  function ppp($venta,$pares,$accesorios = 0){
    if($venta == 0 && $pares == 0){
        return 0;
    }
    else{
        return number_format($venta/($pares + $accesorios),2);
    }
  }

  function upt($factura,$pares,$accesorios = 0){
    if($factura == 0 && $pares == 0)
    {
        return 0;
    }
    else{
        return number_format(($pares+$accesorios)/$factura,2);
    }

  }

  function qpt($venta,$factura){
    if($venta == 0 && $factura == 0)
    {
        return 0;
    }
    else{
        return number_format($venta/$factura,2);
    }
  }
  function vh($venta, $hora)
  {
    if ($venta == 0 || $hora == 0) {
      return 0;
    } else {
      return number_format($venta / $hora, 2);
    }
  }

  function impuestoSimbolo($pais){
    $query = "SELECT simbolo,impuesto FROM pais WHERE id = $pais";
    return consulta(3,$query);
  }

  function iva($op,$valor,$sbs){
    $impuestoSimbolo = impuestoSimbolo($sbs);
    switch ($op) {
      case 1:
        return $impuestoSimbolo[0]." ".number_format($valor * $impuestoSimbolo[1],2);
        break;
      case 0:
        return $impuestoSimbolo[0]." ".number_format($valor,2);
        break;
      default:
        break;
    }
  }

  function DifVentaMeta($venta,$meta = 0){
    return ($venta - $meta);
  }

  function Porcentaje($venta, $meta = 0){
    if($meta != 0){
      return number_format(($venta/$meta)*100,2);
    }
    else{
      return 0;
    }

  }

  function status($valor)
  {
    // Verificamos si el valor es nulo o 0 (sin ventas o datos)
    if ($valor === null || $valor == 0) {
      return "fas fa-circle"; // Retorna un círculo gris para porcentaje 0 o null
    }
    
    // El resto de las condiciones para los porcentajes
    if ($valor < 80) {
      return "fas fa-circle"; // Círculo rojo para porcentajes menores al 80
    } else if ($valor >= 80 && $valor < 100) {
      return "fas fa-circle"; // Círculo negro para porcentajes entre 80 y 100
    } else if ($valor >= 100 && $valor < 125) {
      return "fas fa-star"; // Estrella amarilla para porcentajes entre 100 y 125
    } else if ($valor >= 125) {
      return "fas fa-trophy"; // Trofeo dorado para porcentajes mayores o iguales a 125
    }
  }
  
function color($valor)
{
  // Si el valor es null o 0, asignamos el color gris
  if ($valor === null || $valor == 0) {
    return "color:#B0B0B0; font-size: 2em;"; // Gris para valores null o 0
  }

  // Si el valor es menor a 80, asignamos color rojo
  if ($valor < 80) {
    return "color:#E12626; font-size: 2em;"; // Rojo para valores menores a 80
  } 
  // Si el valor está entre 80 y 99, asignamos color negro
  else if ($valor >= 80 && $valor < 100) {
    return "color:#000000; font-size: 2em;"; // Negro para valores entre 80 y 99
  } 
  // Si el valor está entre 100 y 124, asignamos color amarillo
  else if ($valor >= 100 && $valor < 125) {
    return "color:#E1C708; font-size: 2em;"; // Amarillo para valores entre 100 y 124
  } 
  // Si el valor es 125 o más, asignamos color dorado
  else if ($valor >= 125) {
    return "color:#C6A811; font-size: 2em;"; // Dorado para valores mayores o iguales a 125
  }

  // Valor predeterminado (aunque no debería llegar a este punto)
  return "color:#000000; font-size: 2em;"; // Color negro por defecto
}


function color2($valor, $sem)
{
  if ($valor < 80 && $sem >= 4)
    return "color:#E12626; font-size: 2em;";
  else if ($valor < 80 && $sem <= 3)
    return "color:green; font-size: 2em;";
  else if ($valor >= 80 && $valor < 100)
    return "color:#000000; font-size: 2em;";
  else if ($valor >= 100 && $valor < 125)
    return "color:#E1C708; font-size: 2em;";
  else if ($valor >= 125)
    return "color:#C6A811; font-size: 2em;";
}

Function color3($valor)
{
  if($valor = 0)
    return "color:white; font-size: 2em;";
  else if($valor >= 1 && $valor < 80)
  return "color:#E12626; font-size: 2em;";
  else if($valor >= 80 && $valor < 100)
    return "color:#000000; font-size: 2em;";
  else if($valor >= 100 && $valor < 125)
    return "color:#E1C708; font-size: 2em;";
  else if($valor >= 125)
    return "color:#C6A811; font-size: 2em;";
}


  function IdEmpl($CodEmp){
    $query = "SELECT id_usuario FROM usuario WHERE user = $CodEmp";
    $consulta = consulta(4,$query);
    $IdEmp = $consulta;

    if($IdEmp!=""){
      return $IdEmp[0];
    }
    else {
      $IdEmp[0]="";
      return $IdEmp[0];
    }
  }

  Function v_vrs_m($v)
	{
		if(substr($v,0,1)=="-")
		{
			return "color:red";
		}
		else
		{
			return "color:black";
		}

  }

/*  function rangoWY($fi,$ff){
    $wy = [];
    $wyi = date('YW', strtotime($fi."+ 1 week"));
    $wyf = date('YW', strtotime($ff));
    for ($i = $wyi; $i <= $wyf; $i++) {
      $wy[] = $i;
    }
    return $wy;
  }*/


//se agrego solo para la semana 53 del año 2024
  function rangoWY($fi, $ff) {
    $wy = [];

    // Comprobamos si la fecha inicial es 29/12/2024 y la final es 04/01/2025
    if ($fi == '2024-12-29' && $ff == '2025-01-04') {
        // Si es ese caso específico, agregamos la semana 53
        $wy[] = '202453';
    } else {
        // Si no es el caso específico, calculamos las semanas normalmente
        $wyi = date('YW', strtotime($fi . "+ 1 week"));
        $wyf = date('YW', strtotime($ff));
        for ($i = $wyi; $i <= $wyf; $i++) {
            $wy[] = $i;
        }
    }

    return $wy;
}


  /*function rangoWe($fi, $ff) {
    $wy = [];
    // Calculamos el número de semana para la fecha de inicio y fin
    $wyi = date('YW', strtotime($fi . "+ 1 week")); // Semana inicio
    $wyf = date('YW', strtotime($ff)); // Semana fin

    // Convertimos las semanas al formato "año-semana" y luego extraemos solo el número de semana
    for ($i = $wyi; $i <= $wyf; $i++) {
        // Extraemos solo el número de la semana
        $semana = substr($i, -2);  // Obtener los últimos 2 dígitos (semana)
        $wy[] = $semana;
    }
    return $wy;
}*/

/*function rangoWe($fi, $ff) {
  $wy = [];
  
  // Convertir las fechas a objetos DateTime
  $start = new DateTime($fi ."+ 1 week");
  $end = new DateTime($ff);
  
  // Aseguramos que la fecha de inicio sea un domingo (fin de semana)
  if ($start->format('N') != 7) {
      $start->modify('last Sunday');
  }
  
  // Iteramos por las semanas hasta llegar a la fecha final
  while ($start <= $end) {
      // Obtenemos el año y la semana en formato 'YW' (año-semana)
      $semana = $start->format('YW');
      $year = $start->format('Y');
      $week = $start->format('W');
      
      // Verificamos si estamos dentro del rango de la semana 53
      if ($year == '2024' && ($week == '53' || ($start >= new DateTime('2024-12-29') && $start <= new DateTime('2025-01-04')))) {
          $wy[] = '53'; // Agregar la semana 53
      } else {
          $wy[] = substr($semana, -2); // Si no, agregar la semana normal
      }
      
      // Pasamos a la siguiente semana
      $start->modify('+1 week');
  }
  
  return $wy;
}*/

function rangoWe($fi, $ff) {
  $wy = [];
  
  // Convertir las fechas a objetos DateTime
  $start = new DateTime($fi ."+ 1 week");
  $end = new DateTime($ff);

  // Sumamos un día a la fecha final para incluir toda la semana
  $end->modify('+1 day');
  
  // Aseguramos que la fecha de inicio sea un domingo (fin de semana)
  if ($start->format('N') != 7) {
      $start->modify('last Sunday');
  }

  // Iteramos por las semanas hasta llegar a la fecha final
  while ($start <= $end) {
      // Obtener el número de la semana (sin el año)
      $week = $start->format('W');
      
      // Si la semana es 1 y estamos en el año siguiente, la cambiamos a 53
      if ($week == 1 && $start->format('Y') > (int)date('Y', strtotime($fi))) {
        $week = 53;  // Cambiar semana 1 a 53 solo en el nuevo año
    }
      // Almacenar solo el número de la semana
      $wy[] = str_pad($week, 2, '0', STR_PAD_LEFT);
      
      // Pasamos a la siguiente semana (sumar 7 días)
      $start->modify('+1 week');
  }
  
  return $wy;
}


  function Antiguedad($f)
  {
    if ($f != 0) {
      $hoy = array(
          date('d'),
          date('m'),
          date('Y')
        );

      $fecha = array(
        date('d', strtotime($f)),
        date('m', strtotime($f)),
        date('Y', strtotime($f))
      );

      $ahora = mktime(0, 0, 0, $hoy[1], $hoy[0], $hoy[2]);
      $antes = mktime(0, 0, 0, $fecha[1], $fecha[0], $fecha[2]);

      $dif_segundos = $ahora - $antes;
      $Dias = $dif_segundos / (60 * 60 * 24);
      $Dias = abs($Dias);
      $Dias = floor($Dias);
      $Semanas = floor($Dias / 7);
      $resultado = array($Dias, $Semanas);
      return $resultado;
    } else {
      return 0;
    }
  }

