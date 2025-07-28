<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/tienda/queryRpro.php";

$tienda = (isset($_POST['tienda'])) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];
$sim = impuestoSimbolo($sbs);
$iva = (isset($_POST['iva'])) ? $_POST['iva'] : '';
$vacacionista = (isset($_POST['vacacionista'])) ? $_POST['vacacionista'] : '';
$filtro = ($vacacionista == '1') ? '' : " AND EMP.EMPL_NAME < '5000'";

$semanas = rangoWY($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);
$resumen_extras_global = [];
?>

<div class="container-fluid shadow rounded py-3 px-4">

<style>
  thead th {
    vertical-align: middle !important;
    text-align: center;
  }
  .descanso {
    background-color: #e0e0e0 !important;
    color: #777;
    font-style: italic;
  }

   .legend-container {
    display: flex;
    flex-wrap: nowrap; /* ✅ Evita que pasen a otra fila */
    gap: 10px;
    justify-content: center; /* O 'center' si quieres centrarlos */
    margin-top: 20px;
    overflow-x: auto; /* ✅ Para permitir scroll horizontal si no caben */
}


  .legend-box {
      padding: 10px 15px;
      border-radius: 5px;
      color: #000;
      font-weight: bold;
      text-align: center;
      min-width: 50px;
  }


                      @media (max-width: 600px) {
                        .legend-box {
                        flex: 1 1 100%; /* En móviles: cada div ocupa toda la fila */
                        }
                    }
</style>

            <div class="legend-container">
                <div class="legend-box" style="background-color:rgb(158, 35, 240);">GTO Presencial</div>
                <div class="legend-box" style="background-color:rgb(87, 244, 250);">GTO Virtual</div>
                <div class="legend-box" style="background-color:rgb(55, 118, 255);">TV Presencial</div>
                <div class="legend-box" style="background-color:rgb(82, 247, 90);">TV Virtual</div>
                <div class="legend-box" style="background-color:rgb(252, 239, 62);">Reunión GTS</div>
                <div class="legend-box" style="background-color:rgb(255, 124, 36);">Reunión ASS</div>
                <div class="legend-box" style="background-color:rgb(141, 69, 1);">Induccion ROY</div>
                <div class="legend-box" style="background-color:rgb(255, 104, 235);">Cumpleaños</div>
                <div class="legend-box" style="background-color:rgb(148, 148, 148);">Vacaciones</div>
                <div class="legend-box" style="background-color:rgb(117, 71, 97);">Cobertura</div>
                <div class="legend-box" style="background-color:rgb(68, 119, 66);">Suspensión LABORAL</div>
                <div class="legend-box" style="background-color:rgb(64, 68, 151);">Suspensión IGSS</div>
                <div class="legend-box" style="background-color:rgb(209, 133, 203);">Lactancia</div>
             </div>


<?php
foreach ($tiendas as $tienda) {
  foreach ($semanas as $semana) {
    $query = "SELECT 
                HR.TIENDA, 
                HR.CODIGO_EMPL, 
                HR.NOMBRE_EMPL, 
                V.PUESTO,
                
                UPPER(HR.DIA) AS DIA,
                TO_CHAR(TO_DATE(HR.FECHA, 'YYYY-MM-DD'), 'DD/MM/YYYY') AS FECHA,
                
                CASE 
                    WHEN HR.HORA_IN = '00:00' THEN 'DESCANSO' 
                    ELSE HR.HORA_IN 
                END AS HORA_IN,
              NVL( TO_CHAR(RG.ENTRADA, 'HH24:MI'),'00:00') AS ENTRADA,
              CASE 
                    WHEN HR.HORA_OUT = '00:00' THEN 'DESCANSO' 
                    ELSE HR.HORA_OUT 
                END AS HORA_OUT,
              NVL( TO_CHAR(RG.SALIDA, 'HH24:MI'),'00:00') AS SALIDA,
              ST.UDF1_STRING COD_SUP, ST.UDF2_STRING NOM_SUP , MV.META_S_IVA META,
               HR.HORA_TOT_S , HR.HORA_EXTRA_S, HR.HORA_ALM_S, HR.HORA_LEY_S,  HR.ETIQUETA

            FROM ROY_HORARIO_TDS HR
            INNER JOIN ROY_VENDEDORES_FRIED V 
                ON  HR.CODIGO_EMPL = V.CODIGO_VENDEDOR -- HR.TIENDA = V.TIENDA AND SE QUITA PARA MOSTRAR VENDEDORES EN VARIAS TIENDAS

            INNER JOIN RPS.STORE ST 
                ON V.TIENDA = ST.STORE_NO

            INNER JOIN RPS.SUBSIDIARY SB 
                ON V.SBS = SB.SBS_NO AND ST.SBS_SID = SB.SID

            INNER JOIN ROY_META_DIARIA_TDS MV
            ON HR.TIENDA = MV.TIENDA  AND TO_DATE(HR.FECHA, 'YYYY-MM-DD') = MV.FECHA   

             LEFT JOIN (
                SELECT 
                    TIENDA, 
                    CODIGO_EMPLEADO, 
                    TRUNC(FECHA) AS FECHA,
                    MIN(FECHA) AS ENTRADA,
                    MAX(FECHA) AS SALIDA
                FROM ROY_HLL_REGISTRO_HUELLA
                GROUP BY TIENDA, CODIGO_EMPLEADO, TRUNC(FECHA)
            ) RG 
                ON HR.TIENDA = RG.TIENDA 
                AND HR.CODIGO_EMPL = RG.CODIGO_EMPLEADO 
                AND TRUNC(TO_DATE(HR.FECHA, 'YYYY-MM-DD')) = RG.FECHA

            WHERE EXTRACT(YEAR FROM TO_DATE(HR.FECHA, 'YYYY-MM-DD'))|| TO_CHAR(trunc(TO_DATE(HR.FECHA, 'YYYY-MM-DD'),'d'),'IW')+1 ='$semana'
              AND HR.TIENDA = $tienda
              AND V.SBS = $sbs

            ORDER BY 
                TO_DATE(HR.FECHA, 'YYYY-MM-DD'), 
                DECODE(v.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4)";

    $resultado = consultaOracle(3, $query);

    $datos = [];
    $fechas_unicas = [];
    $metas_por_fecha = [];
    $horas_totales = [];
    $horas_extras = [];

    foreach ($resultado as $rdst) {
      $tienda_ = $rdst[0];
      $codigo = $rdst[1];
      $nombre = $rdst[2];
      $puesto = $rdst[3];
      $fecha = $rdst[5]; // DD/MM/YYYY
      $entrada = $rdst[6];
      $salida = $rdst[8];
      $meta = $rdst[12];
      $horas_totales[$codigo] = $rdst[13]; // HORA_TOT_S
      $horas_extras[$codigo] = $rdst[14]; // HORA_EXTRA_S

 // Evita sumar duplicado por empleado+semana
if (!isset($horas_extras_acumuladas)) $horas_extras_acumuladas = [];

$unique_key = $tienda . '|' . $semana . '|' . $codigo;

if (!isset($horas_extras_acumuladas[$unique_key])) {
    $horas_extra = (float)$rdst[14];

    if (!isset($resumen_extras_global[$tienda][$codigo])) {
        $resumen_extras_global[$tienda][$codigo] = [
            'nombre' => $nombre,
            'horas' => 0
        ];
    }

    $resumen_extras_global[$tienda][$codigo]['horas'] += $horas_extra;
    $horas_extras_acumuladas[$unique_key] = true;
}


      $horas_alm[$codigo] = $rdst[15]; // HORA_ALM_S
      $horas_ley[$codigo] = $rdst[16]; // HORA_LEY_S
      $clave = "$tienda_|$codigo|$nombre|$puesto";

      if (!isset($datos[$clave])) {
        $datos[$clave] = [];
      }

      $datos[$clave][$fecha] = [
          'horario' => ($entrada === 'DESCANSO' || $salida === 'DESCANSO') ? 'DESCANSO' : "$entrada - $salida",
          'etiqueta' => $rdst[17] // posición 17 es HR.ETIQUETA
        ];


      if (!in_array($fecha, $fechas_unicas)) {
        $fechas_unicas[] = $fecha;
      }

      $metas_por_fecha[$fecha] = $meta;
    }

    sort($fechas_unicas);

    // Días en español
    $dias = [
      'Sunday' => 'Domingo', 'Monday' => 'Lunes', 'Tuesday' => 'Martes',
      'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves',
      'Friday' => 'Viernes', 'Saturday' => 'Sábado'
    ];
?>

               
    <h3 class="text-center font-weight-bold text-primary">
      Tienda no: <?php echo $tienda; ?><br>
      <small class="h4 text-primary font-weight-bold text-center">
        <?php echo "| Año: " . substr($semana, 0, 4) . " | Semana: " . substr($semana, -2) . " | Meta tienda: Q " . number_format(MTS($tienda, substr($semana, -2), substr($semana, 0, 4), $sbs)[0], 2) . " |" ?>
      </small>
    </h3>


                    <style>
                thead th {
                    vertical-align: middle !important;
                    text-align: center;
                }

                .descanso {
                    background-color:rgb(250, 95, 95) !important;
                    color: #000;
                    font-style: italic;
                }

                .celda-meta {
                    background-color: #28a745 !important; /* Bootstrap's success green */
                    color: white !important;
                    font-weight: bold;
                }

                 .celda-fecha {
                    background-color:rgb(221, 124, 68) !important; /* Bootstrap's success green */
                    color: white !important;
                    font-weight: bold;
                }

                .celda-inout {
                    background-color: white !important;
                    color: black !important;
                    font-weight: bold;
                }
                
                .borde-izquierdo-total {
                     border-left: 1px solid #dee2e6; /* Borde izquierdo igual al de Bootstrap */
                   }

                   .etiqueta-1 {
                   background-color:rgb(158, 35, 240) !important; /* verde claro */
                  }
                  .etiqueta-2 {
                   background-color:rgb(87, 244, 250) !important; /* celeste */
                  }
                  .etiqueta-3 {
                    background-color:rgb(55, 118, 255) !important; /* amarillo claro */
                  }
                  .etiqueta-4 {
                  background-color:rgb(82, 247, 90) !important; /* naranja claro */
                  }
                  .etiqueta-5 {
                    background-color:rgb(252, 239, 62) !important; /* rosa claro */
                  }
                  .etiqueta-6 {
                    background-color:rgb(255, 124, 36) !important; /* VERDE claro */
                  }
                  .etiqueta-7 {
                    background-color:rgb(141, 69, 1) !important; /* VERDE claro */
                  }
                  .etiqueta-8 {
                    background-color:rgb(255, 104, 235) !important; /* VERDE claro */
                  }
                   .etiqueta-9 {
                  background-color:rgb(148, 148, 148) !important; /* naranja claro */
                  }
                  .etiqueta-10 {
                   background-color:rgb(117, 71, 97) !important; /* rosa claro */                    
                  }
                  .etiqueta-11 {
                   background-color:rgb(68, 119, 66) !important; /* VERDE claro */
                  }
                  .etiqueta-12 {
                    background-color:rgb(64, 68, 151) !important; /* VERDE claro */
                  }
                  .etiqueta-13 {
                   background-color:rgb(209, 133, 203) !important; /* VERDE claro */
                  }

                </style>



    <table style="font-size:14px;" class="table table-bordered table-sm tbavxv">
             <thead class="bg-primary text-white text-center">
                <!-- Fila 1: Metas -->

                <tr>
                    <th rowspan="4">Tienda</th>
                    <th rowspan="4">Código</th>
                    <th rowspan="4">Nombre</th>
                    <th rowspan="4">Puesto</th>                    


                    <?php foreach ($fechas_unicas as $fecha): ?>
                    <th class="celda-meta">Q <?php echo isset($metas_por_fecha[$fecha]) ? number_format($metas_por_fecha[$fecha], 2) : '-'; ?></th>
                    <?php endforeach; ?>

                      <th colspan="4" rowspan="3" class="borde-izquierdo-total">Total Horas</th>

                </tr>

                <!-- Fila 2: Fechas -->
                <tr>
                    <?php foreach ($fechas_unicas as $fecha): ?>
                    <th class="celda-fecha"><?php echo DateTime::createFromFormat('d/m/Y', $fecha)->format('d/m'); ?></th>
                    <?php endforeach; ?>
                </tr>

                <!-- Fila 3: Días -->
                <tr>
                    <?php foreach ($fechas_unicas as $fecha): ?>
                    <th class="celda-fecha">
                        <?php
                        $fechaObj = DateTime::createFromFormat('d/m/Y', $fecha);
                        echo $dias[$fechaObj->format('l')];
                        ?>
                    </th>
                    <?php endforeach; ?>
                </tr>

                <!-- Fila 4: IN - OUT -->
                    <tr>
                    <?php foreach ($fechas_unicas as $fecha): ?>
                        <th align="right" class="celda-inout">IN  -  OUT  --  HR</th>
                    <?php endforeach; ?>
                    <th rowspan="1">Sem.</th>
                    <th rowspan="1">Ley</th>                    
                    <th rowspan="1">Alm.</th>
                    <th rowspan="1">Ext.</th>
                    </tr>

                </thead>


              <tbody style="color: black;">
                <?php
                $totales_s = $totales_ext = $totales_alm = $totales_ley = 0;
                foreach ($datos as $empleado => $horarios):
                    list($tienda_e, $codigo, $nombre, $puesto) = explode('|', $empleado);
                    $hs = isset($horas_totales[$codigo]) ? (float)$horas_totales[$codigo] : 0;
                    $he = isset($horas_extras[$codigo]) ? (float)$horas_extras[$codigo] : 0;
                    $ha = isset($horas_alm[$codigo]) ? (float)$horas_alm[$codigo] : 0;
                    $hl = isset($horas_ley[$codigo]) ? (float)$horas_ley[$codigo] : 0;

                    $totales_s += $hs;
                    $totales_ext += $he;
                    $totales_alm += $ha;
                    $totales_ley += $hl;
                ?>
                <tr>
                    <td><?php echo $tienda_e; ?></td>
                    <td><?php echo $codigo; ?></td>
                    <td><?php echo $nombre; ?></td>
                    <td><?php echo $puesto; ?></td>

                     <?php
                      $descripciones_etiqueta = [
                          1 => 'GTO-Presencial',
                          2 => 'GTO-Virtual',
                          3 => 'TV Presencial',
                          4 => 'TV Virtual',
                          5 => 'Reunion GTS',
                          6 => 'Reunion ASS',
                          7 => 'Induccion ROY',
                          8 => 'Cumpleaños',
                          9 => 'Vacaciones',
                          10 => 'Cobertura',                          
                          11 => 'Suspension LABORAL',
                          12 => 'Suspension IGSS',
                          13 => 'Lactancia',
                      ];
                      ?>


                    <?php foreach ($fechas_unicas as $fecha): ?>
                            <?php
                            $celda = $horarios[$fecha] ?? ['horario' => '', 'etiqueta' => null];
                            $horario = $celda['horario'];
                            $etiqueta = (int)($celda['etiqueta'] ?? 0);
                            $es_descanso = ($horario === 'DESCANSO');

                            // Estilo base
                            $clase = $es_descanso ? 'descanso' : '';

                            // Estilos por etiqueta
                            switch ($etiqueta) {
                                case 1: $clase .= ' etiqueta-1'; break;
                                case 2: $clase .= ' etiqueta-2'; break;
                                case 3: $clase .= ' etiqueta-3'; break;
                                case 4: $clase .= ' etiqueta-4'; break;
                                case 5: $clase .= ' etiqueta-5'; break;
                                case 6: $clase .= ' etiqueta-6'; break;
                                case 7: $clase .= ' etiqueta-7'; break;
                                case 8: $clase .= ' etiqueta-8'; break;
                                case 9: $clase .= ' etiqueta-9'; break;
                                case 10: $clase .= ' etiqueta-10'; break;
                                case 11: $clase .= ' etiqueta-11'; break;
                                case 12: $clase .= ' etiqueta-12'; break;
                                case 13: $clase .= ' etiqueta-13'; break;
                            }
                            ?>
                            <td 
                                  class="<?php echo trim($clase); ?>" 
                                  title="<?php echo isset($descripciones_etiqueta[$etiqueta]) ? $descripciones_etiqueta[$etiqueta] : ''; ?>"
                                >

                                <?php
                                if (!$es_descanso && strpos($horario, ' - ') !== false) {
                                    list($hora_in, $hora_out) = explode(' - ', $horario);
                                    $hora_in_ts = strtotime($hora_in);
                                    $hora_out_ts = strtotime($hora_out);

                                    if ($hora_out_ts < $hora_in_ts) {
                                        $hora_out_ts += 24 * 3600;
                                    }

                                    $diferencia = $hora_out_ts - $hora_in_ts;
                                    $horas_diff = floor($diferencia / 3600);
                                    $minutos_diff = floor(($diferencia % 3600) / 60);

                                    echo "$hora_in - $hora_out | {$horas_diff}";
                                } else {
                                    echo 'DESCANSO';
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>

                    
                    <td align="center"><?php echo number_format($hs, 0); ?></td>
                  <td align="center"><?php echo number_format($hl, 0); ?></td>
                    <td align="center"><?php echo number_format($ha, 0); ?></td>                    
                      <td align="center"><?php echo number_format($he, 0); ?></td>
                </tr>
                <?php endforeach; ?>

                <!-- Totales por tienda -->
                <tr class="font-weight-bold bg-light">
                    <td colspan="<?php echo 4 + count($fechas_unicas); ?>" class="text-right"><strong>Total general tienda:</strong></td>
                    <td align="center"><strong><?php echo number_format($totales_s, 0); ?></strong></td>
                     <td align="center"><strong><?php echo number_format($totales_ley, 0); ?></strong></td>
                    <td align="center"><strong><?php echo number_format($totales_alm, 0); ?></strong></td>                   
                    <td align="center"><strong><?php echo number_format($totales_ext, 0); ?></strong></td>
                </tr>
                </tbody>


</table>

    <hr>
<?php
  }
}
?>
</div>

<?php
// Extrae los números de semana para mostrarlos en el título
$semanas_texto = implode(', ', array_map(function($s) {
    return substr($s, -2); // Toma solo los últimos dos dígitos (número de semana)
}, $semanas));
?>

              <!-- REsumen de horas extras -->
                  <?php foreach ($resumen_extras_global as $tienda => $empleados): ?>
  <div class="row justify-content-start mt-4">
    <div class="col-md-6">
      <h4 class="text-primary font-weight-bold mb-3">
  Resumen de Horas Extras – Semanas <?php echo $semanas_texto; ?> – Tienda <?php echo $tienda; ?>
      </h4>


      <table class="table table-bordered table-sm" style="font-size:14px;">
        <thead class="bg-primary text-white text-center">
          <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th class="text-center">Horas Extras</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $total_extras_resumen = 0;
          foreach ($empleados as $codigo => $info):
            if ($info['horas'] <= 0) continue; // omitir si no hay extras
            $total_extras_resumen += $info['horas'];
          ?>
          <tr>
            <td><?php echo $codigo; ?></td>
            <td><?php echo $info['nombre']; ?></td>
            <td class="text-center"><?php echo number_format($info['horas'], 0); ?></td>
          </tr>
          <?php endforeach; ?>
          <tr class="font-weight-bold bg-light">
            <td colspan="2" class="text-right">Total Horas Extras Tienda:</td>
            <td class="text-center"><?php echo number_format($total_extras_resumen, 0); ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
<?php endforeach; ?>



<script>
  $('.tbavxv').DataTable({
    "searching": false,
    "paging": false,
    "ordering": false,
    "info": false,
    "responsive": true,
    "autoWidth": false
  });
</script>

