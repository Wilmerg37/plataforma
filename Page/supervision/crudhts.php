<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/supervision/queryRpro.php";


$tienda = (isset($_POST['tienda'])) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];
$sim = impuestoSimbolo($sbs);

$iva = (isset($_POST['iva'])) ? $_POST['iva'] : '';
$vacacionista = (isset($_POST['vacacionista'])) ? $_POST['vacacionista'] : '';
$filtro = '';

if ($vacacionista == '1') {
  $filtro = '';
} else {
  $filtro = " AND EMP.EMPL_NAME < '5000'";
}
$semanas = rangoWe($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);

?>



<div class="container-fluid shadow rounded py-3 px-4">

<style>
    .alerta-hora {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        font-weight: bold;
        text-align: center;
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
  <?php
  foreach ($tiendas as $tienda) {
  
      $total = array(
        $factura = 0,
        $pare_roy = 0,
        $pares_otro = 0,
        $tota_pares = 0,
        $accesorios = 0,
        $venta = 0,
        $meta = 0,
        $hora = 0,
        $mt_prs = 0 ,
        $vta_prs = 0 ,
        $dif_prs = 0
      );

      $query = "  SELECT 
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
                          ST.UDF1_STRING COD_SUP, ST.UDF2_STRING NOM_SUP , HR.ETIQUETA , HR.JUSTIFICACION, hr.id_registro ,HR.FECHA_INICIO, HR.FECHA_FIN, 
                          TO_CHAR(FECHA_JUSTIFICACION, 'DD/MM/YYYY HH24:MI:SS'), HR.HORA_JUS_IN, HR.HORA_JUS_OUT

                    FROM ROY_HORARIO_TDS HR
                    INNER JOIN ROY_VENDEDORES_FRIED V 
                         ON  HR.CODIGO_EMPL = V.CODIGO_VENDEDOR -- HR.TIENDA = V.TIENDA AND SE QUITA PARA MOSTRAR VENDEDORES EN VARIAS TIENDAS

                    INNER JOIN RPS.STORE ST 
                        ON V.TIENDA = ST.STORE_NO

                    INNER JOIN RPS.SUBSIDIARY SB 
                        ON V.SBS = SB.SBS_NO AND ST.SBS_SID = SB.SID

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

                    -- Filtros
                    WHERE TO_DATE(HR.FECHA, 'YYYY-MM-DD') 
      BETWEEN TO_DATE('$fi', 'YYYY-MM-DD') 
          AND TO_DATE('$ff', 'YYYY-MM-DD')

                      AND HR.TIENDA = $tienda
                      AND V.SBS = $sbs

                    ORDER BY 
                    DECODE(V.PUESTO, 'JEFE DE TIENDA', 1, 'SUB JEFE DE TIENDA', 2, 'ASESOR DE VENTAS', 3, 4),
                    
                        TO_DATE(HR.FECHA, 'YYYY-MM-DD')";
      $resultado = consultaOracle(3, $query);      
      $cnt=1;
   
  ?>

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
                <div class="legend-box" style="background-color:rgb(68, 119, 66);">Suspención LABORAL</div>
                <div class="legend-box" style="background-color:rgb(64, 68, 151);">Suspención IGSS</div>
                <div class="legend-box" style="background-color:rgb(209, 133, 203);">Lactancia</div>
             </div>
      <h3 class="text-center font-weight-bold text-primary">Actualizacion de Horarios</h3>
      <h3 class="text-center font-weight-bold text-primary">Tienda: <?php echo $tienda?>  </h3>
      
         
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
                   background-color:rgb(158, 35, 240)!important; /* verde claro */
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

      <table style="font-size:14px;" class="table table-hover table-sm tbrdst">
    <thead class="bg-primary">
        <td>No</td>
        <td>ID REGISTRO</td>
        <td>Tienda</td>
        <td>Codigo</td>          
        <td>Nombre</td>
        <td>Puesto</td>
        <td>Dia</td>
        <td>Fecha</td>
        <td>HORA INGRESO</td>
     <!--<td>MARCO ENTRADA</td> -->
        <td>HORA SALIDA</td>
     <!--<td>MARCO SALIDA</td> -->
        <td>RAZON</td>
     <!--   <td>FECHA INICIO</td> -->
     <!--   <td>FECHA FINAL</td> -->
     <!--   <td>HORA INICIO</td> -->
     <!--   <td>HORA FINAL</td>  -->
        <td>FECHA ACTUALIZO.</td>
        <td>ACCION</td>
    </thead>
    
    

<tbody class="align-middle font-size" style="width:100%; color: black; font-weight: normal;">
<?php
foreach ($resultado as $rdst) {
    $cnt++;

    // Validación de HORA INGRESO y MARCO ENTRADA
    $hora_ingreso = ($rdst[6] != 'DESCANSO') ? strtotime($rdst[6]) : false;
    $marco_entrada = ($rdst[7] != '00:00') ? strtotime($rdst[7]) : false;

    $claseEntrada = '';
    if (!$marco_entrada || !$hora_ingreso) {
        $claseEntrada = 'alerta-hora';
    } elseif ($marco_entrada > $hora_ingreso) {
        $claseEntrada = 'alerta-hora';
    }

    // Validación de HORA SALIDA y MARCO SALIDA
    $hora_salida = ($rdst[8] != 'DESCANSO') ? strtotime($rdst[8]) : false;
    $marco_salida = ($rdst[9] != '00:00') ? strtotime($rdst[9]) : false;

    $claseSalida = '';
    if (!$marco_salida || !$hora_salida) {
        $claseSalida = 'alerta-hora';
    } elseif ($marco_salida < $hora_salida) {
        $claseSalida = 'alerta-hora';
    }

    ?>
    <tr class="align-middle font-size">
        <td><?php echo $cnt ?></td>
        <td><?php echo $rdst[14] ?></td> <!-- TIENDA -->
        <td><?php echo $rdst[0] ?></td> <!-- TIENDA -->
        <td><?php echo $rdst[1] ?></td> <!-- CODIGO -->
        <td><?php echo $rdst[2] ?></td> <!-- NOMBRE -->
        <td><?php echo SUBSTR($rdst[3],0,3) ?></td> <!-- PUESTO -->
        <td><?php echo $rdst[4] ?></td> <!-- DIA -->
        <td><?php echo $rdst[5] ?></td> <!-- FECHA -->
                 
                 <?php
            $etiquetaClase = !empty($rdst[18]) ? 'etiqueta-' . intval($rdst[12]) : '';
            ?>
            <td class="<?php echo $etiquetaClase; ?>"><?php echo $rdst[6] ?></td> <!-- HORA INGRESO -->
           
        
          
            <td class="<?php echo $etiquetaClase; ?>"><?php echo $rdst[8] ?></td> <!-- HORA SALIDA -->
          

        <td><?php echo $rdst[13] ?></td> <!-- JUSTIFICACION -->
     
        <td><?php echo $rdst[17] ?></td> <!-- JUSTIFICACION -->
        
        <td>
  <button class="btn btn-sm btn-info justificar-btn" 
          data-id="<?php echo $rdst[14]; ?>" 
          data-nombre="<?php echo $rdst[2]; ?>" 
          data-codigo="<?php echo $rdst[1]; ?>"
          data-fecha="<?php echo htmlspecialchars($rdst[5]); ?>"
          data-dia="<?php echo $rdst[4]; ?>"
          data-hora-in="<?php echo $rdst[6]; ?>"
          data-hora-out="<?php echo $rdst[8]; ?>"
          data-justificacion="<?php echo htmlspecialchars($rdst[13]); ?>">
    Actualizar
  </button>

<button class="btn btn-sm btn-danger eliminar-btn" 
          data-id="<?php echo $rdst[14]; ?>">
    Eliminar
  </button>
  
</td>

    </tr>
    <?php
    // Por si quieres manejar casos especiales como VACACIONISTA
    if ($rdst[3] === 'VACACIONISTA') {
        $rdst[6] = 0;
    }
}
?>


</tbody>

    
    <tfoot>
    </tfoot>
</table>

         
      <hr>
  <?php
   
  }
  ?>
</div>

<!-- Modal Justificación -->
<div class="modal fade" id="justificarModal" tabindex="-1" role="dialog" aria-labelledby="justificarModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formJustificacion">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Actualizacion de Horario</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">           
  <input type="hidden" name="id_registro" id="id_registro">
  <input type="hidden" name="etiqueta" id="etiqueta">


  <div class="form-group">
    <label>Empleado</label>
    <input type="text" class="form-control" id="nombre_empleado" disabled>
  </div>

  <div class="form-group">
    <label>Código</label>
    <input type="text" class="form-control" id="codigo_empleado" disabled>
  </div>

  <div class="form-group">
    <label>Fecha</label>
    <input type="text" class="form-control" id="fecha" disabled>
  </div>

  <div class="form-group">
    <label>Día</label>
    <input type="text" class="form-control" id="dia" disabled>
  </div>

  <div class="form-group">
    <label>Hora Ingreso</label>
    <input type="text" class="form-control" id="hora_in" disabled>
  </div>

  <div class="form-group">
    <label>Hora Salida</label>
    <input type="text" class="form-control" id="hora_out" disabled>
  </div>

  <!-- NUEVO SELECT DE MOTIVOS -->
  <div class="form-group">
    <label>Seleccionar motivo</label>
    <select class="form-control" id="motivo_select">
      <option value="">-- Seleccione un motivo --</option>
         <option value="GTO PRESENCIAL">GTO PRESENCIAL</option> <!-- ← NUEVA OPCIÓN -->
         <option value="GTO VIRTUAL">GTO VIRTUAL</option> <!-- ← NUEVA OPCIÓN -->
         <option value="TV PRESENCIAL">TV PRESENCIAL</option> <!-- ← NUEVA OPCIÓN -->
         <option value="TV VIRTUAL">TV VIRTUAL</option> <!-- ← NUEVA OPCIÓN -->
          <option value="REUNION GTS">REUNION GTS</option> <!-- ← NUEVA OPCIÓN -->
          <option value="REUNION ASS">REUNION ASS</option> <!-- ← NUEVA OPCIÓN -->
          <option value="INDUCCION ROY">INDUCCION ROY</option> <!-- ← NUEVA OPCIÓN -->
         <option value="CUMPLEANOS">CUMPLEAÑOS</option> <!-- ← NUEVA OPCIÓN -->
         <option value="VACACIONES">VACACIONES</option> <!-- ← NUEVA OPCIÓN -->
         <option value="COBERTURA">COBERTURA</option> <!-- ← NUEVA OPCIÓN -->
         <option value="SUSPENSION LABORAL">SUSPENSION LABORAL</option> <!-- ← NUEVA OPCIÓN -->
         <option value="SUSPENSION IGGSS">SUSPENSION IGSS</option> <!-- ← NUEVA OPCIÓN -->
           <option value="LACTANCIA">LACTANCIA</option> <!-- ← NUEVA OPCIÓN -->
            <option value="CITA IGSS">CITA IGSS</option> <!-- ← NUEVA OPCIÓN -->
      <option value="OTROS">OTROS</option>
    </select>
  </div>

  <!-- NUEVO: Fechas de SUSPENSION -->
<div id="fechasSuspension" style="display: none;">
  <div class="form-group">
    <label>Fecha Inicio</label>
    <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
  </div>
  <div class="form-group">
    <label>Fecha Fin</label>
    <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
  </div>
</div>

<!-- NUEVO: Horas para GTO PRESENCIAL -->
<div id="horasGTO" style="display: none;">
  <div class="form-group">
    <label>Hora Ingreso</label>
    <input type="time" class="form-control" name="gto_hora_ingreso" id="gto_hora_ingreso">
  </div>
  <div class="form-group">
    <label>Hora Salida</label>
    <input type="time" class="form-control" name="gto_hora_salida" id="gto_hora_salida">
  </div>
</div>


  <div class="form-group">
    <label>Razon</label>
    <textarea class="form-control" name="justificacion" id="justificacion" rows="3"></textarea>
  </div>
</div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Actualizar</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
$(document).ready(function () {
  // Abrir modal con datos
  $(document).off('click', '.justificar-btn').on('click', '.justificar-btn', function () {
    $('#id_registro').val($(this).data('id'));
    $('#nombre_empleado').val($(this).data('nombre'));
    $('#codigo_empleado').val($(this).data('codigo'));
    $('#fecha').val($(this).data('fecha'));
    $('#dia').val($(this).data('dia'));
    $('#hora_in').val($(this).data('hora-in'));
    $('#hora_out').val($(this).data('hora-out'));
    $('#justificacion').val($(this).data('justificacion'));
    $('#justificarModal').modal('show');
  });

  // Guardar justificación
  $('#formJustificacion').submit(function (e) {
    e.preventDefault();
    $.ajax({
      url: '/roy/Page/supervision/update_horarios_tds.php',
      type: 'POST',
      data: $(this).serialize(),
      success: function (response) {
        alert('Actualizacion guardada correctamente');
        $('#justificarModal').modal('hide');
      },
      error: function () {
        alert('Ocurrió un error al guardar la Actualizacion');
      }
    });
  });

  // Cambio de motivo
  $('#motivo_select').on('change', function () {
    var selected = $(this).val();

    if (['SUSPENSION LABORAL', 'VACACIONES'].includes(selected)) {
      $('#fechasSuspension').show();
    } else {
      $('#fechasSuspension').hide();
      $('#fecha_inicio').val('');
      $('#fecha_fin').val('');
    }

    if (['CITA IGSS', 'GTO PRESENCIAL', 'GTO VIRTUAL', 'REUNION GTS', 'REUNION ASS', 'LACTANCIA','OTROS'].includes(selected)) {
      $('#horasGTO').show();
    } else {
      $('#horasGTO').hide();
      $('#gto_hora_ingreso').val('');
      $('#gto_hora_salida').val('');
    }

    if (selected === 'OTROS' || selected === '') {
      $('#justificacion').val('').prop('readonly', false);
    } else {
      $('#justificacion').val(selected).prop('readonly', true);
    }

    var etiquetas = {
     "GTO PRESENCIAL": 1,
      "GTO VIRTUAL": 2,
      "TV PRESENCIAL": 3,
      "TV VIRTUAL": 4,
      "REUNION GTS": 5,
      "REUNION ASS": 6,
      "Induccion ROY": 7,
      "CUMPLEAÑOS": 8,
      "VACACIONES": 9,
      "COBERTURA": 10,
      "SUSPENSION LABORAL": 11,
      "SUSPENSION IGSS": 12,
      "LACTANCIA": 13,
      "CITA IGSS": 14
    };

    $('#etiqueta').val(etiquetas[selected] || '');
  });

  // Eliminar información
  $(document).off('click', '.eliminar-btn').on('click', '.eliminar-btn', function () {
    var id = $(this).data('id');
    var row = $(this).closest('tr');

    if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
      $.ajax({
        url: '/roy/Page/supervision/update_horarios_tds.php',
        type: 'POST',
        data: { 
          id_registro: id, 
          modo: 'eliminar' 
        },
        success: function (response) {
          alert('Registro eliminado correctamente');
          row.remove(); // Elimina visualmente
        },
        error: function () {
          alert('Ocurrió un error al eliminar el registro.');
        }
      });
    }
  });

  // Inicializa DataTable
  $('.tbrdst').DataTable({
    searching: false,
    paging: false,
    ordering: false,
    info: false,
    responsive: true,
    autoWidth: false
  });

  $('.tooltip').tooltip();
});
</script>
