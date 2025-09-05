<?php
  function VMSE($sbs,$emp,$t,$yk){
    $resultado = [0,0];
    $query = "
	 select 
			
				nvl(round(CASE WHEN SUM(  A.META) = 0 THEN 0 ELSE ROUND(SUM(NVL(sum(case when t1.receipt_type=0 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12 
								 when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12*-1 end ),0)),2) END  /	ROUND( CASE WHEN SUM(  A.META) = 0 THEN 1 ELSE SUM(A.META)END,2)*100,0),0) PORCENTAJE						   
						  
					from rps.document t1 
					inner join rps.document_item t2 on (t1.sid = t2.doc_sid)
					inner JOIN ROY_META_SEM_X_VENDEDOR A ON  TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = A.SEMANA AND TO_CHAR(T1.CREATED_DATETIME,'IYYY') = A.ANIO AND T1.STORE_NO = A.TIENDA AND t1.employee1_login_name = A.CODIGO_EMPLEADO AND T1.SBS_NO = A.SBS
					inner join ROY_VENDEDORES_FRIED E on (E.CODIGO_VENDEDOR = t1.employee1_login_name)
					
					where 1=1
					and t1.status=4 
				 and t1.employee1_full_name not in ('SYSADMIN')
					 and t1.receipt_type<>2
					   AND T1.sbs_no = $sbs
					   AND t1.STORE_NO in($t)
					   and t1.employee1_login_name = '$emp'
					   and EXTRACT(YEAR FROM t1.CREATED_dATETIME)|| TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = '$yk'
				  --   and t1.CREATED_DATETIME between to_date('2024-05-26 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('2024-06-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS')				 
					 group by  A.META";

    $res = consultaOracle(1,$query);
    if($res == ""){
      return $resultado;
    }
    else {
      return $res;
    }
  }

  function VMST($sbs, $t, $yk)
  {
    $query = "   select 
			
				nvl(round(	CASE WHEN SUM(  A.META) = 0 THEN 0 ELSE ROUND(SUM(NVL(sum(case when t1.receipt_type=0 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12 
								 when t1.receipt_type=1 then ((t2.price-( t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))/1.12*-1 end ),0)),2) END  /	ROUND( CASE WHEN SUM(  A.META) = 0 THEN 1 ELSE SUM(A.META)END,2)*100,0),0) PORCENTAJE		 					   
						  
					from rps.document t1 
					inner join rps.document_item t2 on (t1.sid = t2.doc_sid)
					INNER JOIN ROY_META_SEM_TDS A ON  TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = A.SEMANA 
                    AND TO_CHAR(T1.CREATED_DATETIME,'IYYY') = A.ANIO
                    AND T1.STORE_NO = A.TIENDA AND T1.SBS_NO = A.SBS
					INNER join ROY_VENDEDORES_FRIED E on (E.CODIGO_VENDEDOR = t1.employee1_login_name)
					
					where 1=1
					and t1.status=4 
				 and t1.employee1_full_name not in ('SYSADMIN')
					 and t1.receipt_type<>2
					   AND T1.sbs_no = $sbs
					   AND t1.STORE_NO in($t)
					
					   and EXTRACT(YEAR FROM t1.CREATED_dATETIME)|| TO_CHAR(trunc(T1.CREATED_DATETIME,'d'),'IW')+1 = '$yk'
				  --   and t1.CREATED_DATETIME between to_date('2024-05-26 00:00:00', 'YYYY-MM-DD HH24:MI:SS') ANd to_date('2024-06-01 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
					 
					 group by   A.META";


    return consultaOracle(1, $query);
  }

  function MT ($t,$s,$a,$sbs){
    $res = [0];

    $query = " SELECT ROUND(SUM(META),2) META FROM ROY_META_SEM_TDS 
                            WHERE TIENDA= $t
                            AND SEMANA = $s
              AND ANIO =$a
              ";

    $resultado = consultaOracle(1, $query);
    if(!$resultado){
      return $res;
    }
    else{
      return $resultado;
    }
  }

  function MTS ($t,$s,$a,$sbs){
    $res = [0];

    $query = "SELECT ROUND(SUM(META_S_IVA),2) META FROM ROY_META_DIARIA_TDS
              WHERE TIENDA = $t
              AND SEMANA = $s
              AND EXTRACT(YEAR FROM FECHA) =$a 
              ";

    $resultado = consultaOracle(1, $query);
    if(!$resultado){
      return $res;
    }
    else{
      return $resultado;
    }
  }


  //OBTENGO LA META DIARIA DEL SUPERVISOR POR FECHA
  function MTDS ($t,$s,$a,$sbs){
    $res = [0];
   
    $query = "SELECT  SUM(META_S_IVA) META_S_IVA FROM roy_meta_diaria_tds
              WHERE COD_SUPER = $t
              AND FECHA = to_date('$s 00:00:00', 'YYYY-MM-DD HH24:MI:SS')
               AND ANIO =$a
               ";

    $resultado = consultaOracle(1, $query);
    if(!$resultado){
      return $res;
    }
    else{
      return $resultado;
    }
  }

  function MTSS ($t,$s,$a,$sbs){
    $res = [0];

    $query = "SELECT ROUND(SUM(META),2) META FROM ROY_META_SEM_TDS M
                            INNER JOIN RPS.STORE S ON M.TIENDA = S.STORE_NO 
                            INNER JOIN  rps.subsidiary SB on s.sbs_sid=sB.sid AND M.SBS = SB.SBS_NO 
                            WHERE SB.SBS_NO = $sbs
                            AND S.UDF1_STRING = $t
              AND M.SEMANA = $s
              AND M.ANIO =$a
              ";

    $resultado = consultaOracle(1, $query);
    if(!$resultado){
      return $res;
    }
    else{
      return $resultado;
    }
  }

  function MTSC ($t,$s,$a,$sbs){
    $res = [0];

    $query = "SELECT ROUND(SUM(META),2) META FROM ROY_META_SEM_TDS M
                            INNER JOIN RPS.STORE S ON M.TIENDA = S.STORE_NO 
                            INNER JOIN  rps.subsidiary SB on s.sbs_sid=sB.sid AND M.SBS = SB.SBS_NO 
                            WHERE SB.SBS_NO = $sbs
                           -- AND S.UDF1_STRING = $t
              AND M.SEMANA = $s
              AND M.ANIO =$a
              ";

    $resultado = consultaOracle(1, $query);
    if(!$resultado){
      return $res;
    }
    else{
      return $resultado;
    }
  }

  
  // Función para calcular el pago semanal basado en el estatus y las semanas consecutivas
function calcularPagoPorSemana($estatus, $puesto, &$semanasConsecutivas) {
  // Si el estatus es menor a 100%, no se paga bono y reiniciamos semanas consecutivas
  if ($estatus < 100) {
      // Si se baja el estatus, reiniciamos las semanas consecutivas
      $semanasConsecutivas = 0;
      return 0.00;
  }

  // Si el puesto es JEF y el estatus está entre 100% y 119%, se paga progresivamente
  if ($estatus >= 100 && $estatus <= 119) {
      // Si es la primera semana que se cumple el porcentaje (100% a 119%)
      if ($semanasConsecutivas == 0) {
          $semanasConsecutivas = 1; // Primer semana de cumplimiento
          return 25.00;  // Primer pago de 25.00
      }
      // Si es la segunda semana consecutiva de cumplimiento (100% a 119%)
      else if ($semanasConsecutivas == 1) {
          $semanasConsecutivas = 2; // Segunda semana consecutiva
          return 50.00;  // Pago de 50.00
      }
      // Si es la tercera semana consecutiva de cumplimiento (100% a 119%)
      else if ($semanasConsecutivas == 2) {
          $semanasConsecutivas = 3; // Tercera semana consecutiva
          return 100.00;  // Pago de 100.00
      }
      // Si es la cuarta semana consecutiva de cumplimiento (100% a 119%)
      else if ($semanasConsecutivas == 3) {
          $semanasConsecutivas = 4; // Cuarta semana consecutiva
          return 175.00;  // Pago de 175.00
      }
  }

  // Si el puesto es JEF y el estatus es mayor o igual a 120%, se paga según las semanas consecutivas
  if ($estatus >= 120) {
      // Incrementar las semanas consecutivas si sigue cumpliendo
      if ($semanasConsecutivas == 0) {
          $semanasConsecutivas = 1;  // Primer semana de cumplimiento (al menos 120%)
      } else {
          $semanasConsecutivas++;  // Incrementar semana consecutiva
      }

      // Pagos según las semanas consecutivas alcanzadas
      if ($semanasConsecutivas == 1) {
          return 50.00;  // Primera semana de cumplimiento (al menos 120%)
      } elseif ($semanasConsecutivas == 2) {
          return 100.00; // Segunda semana consecutiva
      } elseif ($semanasConsecutivas == 3) {
          return 150.00; // Tercera semana consecutiva
      } elseif ($semanasConsecutivas == 4) {
          return 200.00; // Cuarta semana consecutiva
      }
  }

  return 0.00;  // En caso de que no cumpla con ninguna condición
}


// Función para calcular el pago semanal basado en el estatus y las semanas consecutivas
function calcularPagoPorSemanaVend($estatus, $puesto, &$semanasConsecutivas) {
  // Si el estatus es menor a 100%, no se paga bono y reiniciamos semanas consecutivas
  if ($estatus < 100) {
      // Si se baja el estatus, reiniciamos las semanas consecutivas
      $semanasConsecutivas = 0;
      return 0.00;
  }

  // Si el puesto es JEF y el estatus está entre 100% y 119%, se paga progresivamente
  if ($estatus >= 100 && $estatus <= 119) {
      // Si es la primera semana que se cumple el porcentaje (100% a 119%)
      if ($semanasConsecutivas == 0) {
          $semanasConsecutivas = 1; // Primer semana de cumplimiento
          return 100.00;  // Primer pago de 100.00
      }
      // Si es la segunda semana consecutiva de cumplimiento (100% a 119%)
      else if ($semanasConsecutivas == 1) {
          $semanasConsecutivas = 2; // Segunda semana consecutiva
          return 150.00;  // Pago de 150.00
      }
      // Si es la tercera semana consecutiva de cumplimiento (100% a 119%)
      else if ($semanasConsecutivas == 2) {
          $semanasConsecutivas = 3; // Tercera semana consecutiva
          return 250.00;  // Pago de 250.00
      }
      // Si es la cuarta semana consecutiva de cumplimiento (100% a 119%)
      else if ($semanasConsecutivas == 3) {
          $semanasConsecutivas = 4; // Cuarta semana consecutiva
          return 400.00;  // Pago de 400.00
      }
  }

  // Si el puesto es JEF y el estatus es mayor o igual a 120%, se paga según las semanas consecutivas
  if ($estatus >= 120) {
      // Incrementar las semanas consecutivas si sigue cumpliendo
      if ($semanasConsecutivas == 0) {
          $semanasConsecutivas = 1;  // Primer semana de cumplimiento (al menos 120%)
      } else {
          $semanasConsecutivas++;  // Incrementar semana consecutiva
      }

      // Pagos según las semanas consecutivas alcanzadas
      if ($semanasConsecutivas == 1) {
          return 150.00;  // Primera semana de cumplimiento (al menos 120%)
      } elseif ($semanasConsecutivas == 2) {
          return 250.00; // Segunda semana consecutiva
      } elseif ($semanasConsecutivas == 3) {
          return 350.00; // Tercera semana consecutiva
      } elseif ($semanasConsecutivas == 4) {
          return 550.00; // Cuarta semana consecutiva
      }
  }

  return 0.00;  // En caso de que no cumpla con ninguna condición
}

/*function calcularBonoPorTienda($promedio, &$semanasConsecutivasTienda, $porcentajeJef) {
    // Verificamos que el JEF haya cumplido al menos el 100%
    if ($porcentajeJef < 100) {
        $semanasConsecutivasTienda = 0;
        return 0.00; // No se paga bono si el JEF no cumplió
    }

    if ($promedio < 100) {
        $semanasConsecutivasTienda = 0;
        return 0.00;
    }

    if ($promedio >= 100 && $promedio <= 119) {
        if ($semanasConsecutivasTienda == 0) {
            $semanasConsecutivasTienda = 1;
            return 25.00;
        } elseif ($semanasConsecutivasTienda == 1) {
            $semanasConsecutivasTienda = 2;
            return 50.00;
        } elseif ($semanasConsecutivasTienda == 2) {
            $semanasConsecutivasTienda = 3;
            return 100.00;
        } elseif ($semanasConsecutivasTienda == 3) {
            $semanasConsecutivasTienda = 4;
            return 175.00;
        }
    }

    if ($promedio >= 120) {
        if ($semanasConsecutivasTienda == 0) {
            $semanasConsecutivasTienda = 1;
            return 50.00;
        } elseif ($semanasConsecutivasTienda == 1) {
            $semanasConsecutivasTienda = 2;
            return 100.00;
        } elseif ($semanasConsecutivasTienda == 2) {
            $semanasConsecutivasTienda = 3;
            return 150.00;
        } elseif ($semanasConsecutivasTienda == 3) {
            $semanasConsecutivasTienda = 4;
            return 200.00;
        }
    }

    return 0.00;
}*/

function calcularBonoPorTienda($promedio, &$semanasConsecutivasTienda, $porcentajeJef, $bonoJefe) {
    // Si el jefe no recibió bono, entonces no cumplió con el 100%
    // Se reinicia la consecutividad de la tienda
    if ($bonoJefe == 0.00) {
        $semanasConsecutivasTienda = 0;
        return 0.00;
    }

    // Si la tienda no cumplió el 100%
    if ($promedio < 100) {
        $semanasConsecutivasTienda = 0;
        return 0.00;
    }

    // Ambos cumplieron con al menos 100%, pero menos de 120%
    if ($porcentajeJef >= 100 && $porcentajeJef < 120 && $promedio >= 100 && $promedio < 120) {
        // Reiniciar o incrementar semanas consecutivas de la tienda
        if ($semanasConsecutivasTienda == 0) {
            $semanasConsecutivasTienda = 1;
        } else {
            $semanasConsecutivasTienda++;
        }

        // La tienda gana lo mismo que el jefe esta semana
        return $bonoJefe;
    }

    // Ambos cumplieron 120% o más
    if ($porcentajeJef >= 120 && $promedio >= 120) {
        if ($semanasConsecutivasTienda == 0) {
            $semanasConsecutivasTienda = 1;
        } else {
            $semanasConsecutivasTienda++;
        }

        // La tienda gana el doble del bono del jefe
        return $bonoJefe ;
    }

    // En cualquier otro caso
    return 0.00;
}



function calcularBonoPorTienda2nievo($promedioTienda, &$semanasConsecutivasTienda, $porcentajeJef) {
    // Si el jefe no cumple mínimo 100%, no hay bono
    if ($porcentajeJef < 100 || $promedioTienda < 100) {
        $semanasConsecutivasTienda = 0;
        return 0.00;
    }

    // Definir los montos por semana dependiendo del % del jefe
    $bonos = [];

    if ($porcentajeJef >= 100 && $porcentajeJef <= 119) {
        // Escala de pago entre 100 y 119%
        $bonos = [25.00, 50.00, 100.00, 175.00];
    } elseif ($porcentajeJef >= 120) {
        // Escala de pago para ≥ 120%
        $bonos = [50.00, 100.00, 150.00, 200.00];
    }

    // Obtener el bono según semanas consecutivas (máximo index 3)
    $index = min($semanasConsecutivasTienda, count($bonos) - 1);
    $pago = $bonos[$index];

    // Aumentar contador de semanas consecutivas (hasta 4 máximo)
    if ($semanasConsecutivasTienda < 4) {
        $semanasConsecutivasTienda++;
    }

    return $pago;
}





