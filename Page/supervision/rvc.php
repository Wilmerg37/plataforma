<?php
require_once "../../Funsiones/consulta.php";
require_once "../../Funsiones/kpi.php";
require_once "../../Funsiones/tienda/queryRpro.php";

$tienda = isset($_POST['tienda']) ? $_POST['tienda'] : '';
$fi = date('Y-m-d', strtotime(substr($_POST['fecha'], 0, -13)));
$ff = date('Y-m-d', strtotime(substr($_POST['fecha'], -10)));
$sbs = isset($_POST['sbs']) ? $_POST['sbs'] : '';
$pais = $_SESSION['user'][7];
$sim = impuestoSimbolo($sbs);

$iva = isset($_POST['iva']) ? $_POST['iva'] : '';
$vacacionista = isset($_POST['vacacionista']) ? $_POST['vacacionista'] : '';
$filtro = ($vacacionista == '1') ? '' : " AND EMP.EMPL_NAME < '5000'";

// Calcular el rango de fechas del año anterior
$fi_anio_anterior = date('Y-m-d', strtotime("-1 year", strtotime($fi)));
$ff_anio_anterior = date('Y-m-d', strtotime("-1 year", strtotime($ff)));

$semanas = rangoWY($fi, $ff);
$tiendas = explode(',', $tienda);
sort($tiendas);
?>

<div class="container-fluid shadow rounded py-3 px-4">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Filtro global para todos los gráficos -->
  <div class="form-group">
    <label for="filtroGrafico" class="font-weight-bold">Filtrar gráfico por:</label>
    <select id="filtroGrafico" class="form-control w-25 mb-4">
      <option value="DCS_CODE">DEPARTAMENTO</option>
      <option value="VEND_NAME">PROVEEDOR</option>
      <option value="C_NAME">CATEGORIA</option>
    </select>
  </div>

  <script>
    const charts = {};
    const datosCharts = {};
  </script>

  <?php foreach ($tiendas as $tienda_actual): ?>
  <?php
  // Consulta con datos tanto actuales como del año anterior
  $query = "SELECT * FROM (
    SELECT A.STORE_NO, A.VEND_CODE, A.VEND_NAME, A.DCS_CODE, A.D_NAME, A.C_NAME, A.S_NAME,
           SUM(PARES) AS PARES, SUM(VENTA_CON_IVA) VENTA,
           -- Datos del año anterior
           SUM(PARES_ANIO_ANTERIOR) AS PARES_ANIO_ANTERIOR, SUM(VENTA_CON_IVA_ANIO_ANTERIOR) AS VENTA_ANIO_ANTERIOR
    FROM (
      SELECT 
        t1.store_NO,
        VD.vend_code,
        VD.vend_name,
        DCS.DCS_CODE,
        DCS.D_NAME,
        DCS.C_NAME,
        DCS.S_NAME,
        SUM(CASE 
  WHEN t1.created_datetime BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
       AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS') 
    THEN CASE 
           WHEN t1.receipt_type = 0 THEN t2.qty 
           WHEN t1.receipt_type = 1 THEN t2.qty * -1 
         END 
  ELSE 0
END) AS PARES,

        -- Ventas actuales
        NVL(SUM(CASE 
  WHEN t1.created_datetime BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
       AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS') 
    THEN CASE 
           WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc,0)/100)) * t2.qty)
           WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc,0)/100)) * t2.qty) * -1
         END
  ELSE 0
END), 0) - SUM(NVL(t2.lty_piece_of_tbr_disc_amt, 0)) AS VENTA_CON_IVA,

          
        -- Ventas sin IVA
        NVL(SUM(CASE 
  WHEN t1.created_datetime BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
       AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS') 
    THEN CASE 
           WHEN t1.receipt_type = 0 THEN ((t2.price - (t2.price * NVL(t1.disc_perc,0)/100)) * t2.qty)
           WHEN t1.receipt_type = 1 THEN ((t2.price - (t2.price * NVL(t1.disc_perc,0)/100)) * t2.qty) * -1
         END
  ELSE 0
END), 0) - SUM(NVL(t2.lty_piece_of_tbr_disc_amt, 0))/1.12 AS VENTA_SIN_IVA,

          
        -- Datos del año anterior
        SUM(CASE 
          WHEN t1.created_datetime BETWEEN TO_DATE('$fi_anio_anterior 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
               AND TO_DATE('$ff_anio_anterior 23:59:59', 'YYYY-MM-DD HH24:MI:SS') 
          THEN (CASE WHEN t1.receipt_type = 0 THEN t2.qty WHEN t1.receipt_type = 1 THEN t2.qty * -1 END)
          ELSE 0
        END) AS PARES_ANIO_ANTERIOR,
        -- Ventas del año anterior
        SUM(CASE 
          WHEN t1.created_datetime BETWEEN TO_DATE('$fi_anio_anterior 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
               AND TO_DATE('$ff_anio_anterior 23:59:59', 'YYYY-MM-DD HH24:MI:SS') 
          THEN NVL((CASE WHEN t1.receipt_type = 0 THEN ((t2.price-(t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))
                         WHEN t1.receipt_type = 1 THEN ((t2.price-(t2.price*NVL(t1.disc_perc,0)/100))*(t2.qty))*-1 END), 0)
          ELSE 0
        END) AS VENTA_CON_IVA_ANIO_ANTERIOR
      FROM rps.document t1 
      INNER JOIN rps.document_item t2 ON t1.sid = t2.doc_sid
      INNER JOIN RPS.INVN_SBS_ITEM B ON t2.INVN_sBS_ITEM_SID = B.SID
      INNER JOIN RPS.VENDOR VD ON T2.vend_code = VD.VEND_CODE AND B.SBS_SID = VD.SBS_SID
      INNER JOIN RPS.DCS DCS ON B.sbs_sid = DCS.sbs_sid AND B.dcs_sid = DCS.sid
      WHERE t1.status = 4 
        AND t1.receipt_type <> 2
        AND VD.vend_code NOT IN (106)
        AND t1.sbs_no = $sbs
        AND t1.store_no IN ($tienda_actual)
        -- Filtrar por fecha para el año actual y el año anterior
        AND (
            t1.CREATED_DATETIME BETWEEN TO_DATE('$fi 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
            AND TO_DATE('$ff 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
            OR t1.CREATED_DATETIME BETWEEN TO_DATE('$fi_anio_anterior 00:00:00', 'YYYY-MM-DD HH24:MI:SS') 
            AND TO_DATE('$ff_anio_anterior 23:59:59', 'YYYY-MM-DD HH24:MI:SS')
        )
      GROUP BY t1.store_NO, VD.vend_code, VD.vend_name, DCS.DCS_CODE, DCS.D_NAME, DCS.C_NAME, DCS.S_NAME
    ) A
    GROUP BY A.STORE_NO, A.VEND_CODE, A.VEND_NAME, A.DCS_CODE, A.D_NAME, A.C_NAME, A.S_NAME
  )
";
  $resultado = consultaOracle(3, $query);

  // Agrupaciones con datos del año actual y anterior
  $por_dcs = [];
  $por_prov = [];
  $por_cat = [];
  $por_dcs_venta = [];
  $por_prov_venta = [];
  $por_cat_venta = [];
  
  $por_dcs_anio_anterior = [];
  $por_prov_anio_anterior = [];
  $por_cat_anio_anterior = [];
  $por_dcs_venta_anio_anterior = [];
  $por_prov_venta_anio_anterior = [];
  $por_cat_venta_anio_anterior = [];

  foreach ($resultado as $row) {
      $dcs = $row[3];       // DCS_CODE
      $prov = $row[2];      // VEND_NAME
      $cat = $row[5];      // C_NAME CATEGORIA
      $pares = $row[7];     // PARES
      $venta = $row[8];     // VENTA
      $pares_anio_anterior = $row[9]; // PARES_ANIO_ANTERIOR
      $venta_anio_anterior = $row[10]; // VENTA_ANIO_ANTERIOR

      // Año actual
      $por_dcs[$dcs] = ($por_dcs[$dcs] ?? 0) + $pares;
      $por_prov[$prov] = ($por_prov[$prov] ?? 0) + $pares;
      $por_cat[$cat] = ($por_cat[$cat] ?? 0) + $pares;

      $por_dcs_venta[$dcs] = ($por_dcs_venta[$dcs] ?? 0) + $venta;
      $por_prov_venta[$prov] = ($por_prov_venta[$prov] ?? 0) + $venta;
       $por_cat_venta[$cat] = ($por_cat_venta[$cat] ?? 0) + $venta;

      // Año anterior
      $por_dcs_anio_anterior[$dcs] = ($por_dcs_anio_anterior[$dcs] ?? 0) + $pares_anio_anterior;
      $por_prov_anio_anterior[$prov] = ($por_prov_anio_anterior[$prov] ?? 0) + $pares_anio_anterior;
       $por_cat_anio_anterior[$cat] = ($por_cat_anio_anterior[$cat] ?? 0) + $pares_anio_anterior;

      $por_dcs_venta_anio_anterior[$dcs] = ($por_dcs_venta_anio_anterior[$dcs] ?? 0) + $venta_anio_anterior;
      $por_prov_venta_anio_anterior[$prov] = ($por_prov_venta_anio_anterior[$prov] ?? 0) + $venta_anio_anterior;
      $por_cat_venta_anio_anterior[$cat] = ($por_cat_venta_anio_anterior[$cat] ?? 0) + $venta_anio_anterior;
  }

  $chartId = "grafico_" . $tienda_actual;
  ?>

  <h3 class="text-center text-primary">Tienda No: <?php echo $tienda_actual; ?></h3>
  <div class="mb-4">
    <canvas id="<?php echo $chartId; ?>" height="100"></canvas>
  </div>

  <script>
  const ctx_<?php echo $tienda_actual; ?> = document.getElementById('<?php echo $chartId; ?>').getContext('2d');

  // Generamos el mes y año en formato "MM-YYYY"
  const fechaInicioActual = new Date('<?php echo $fi; ?>');
  const fechaFinActual = new Date('<?php echo $ff; ?>');
  const fechaInicioAnioAnterior = new Date('<?php echo $fi_anio_anterior; ?>');
  const fechaFinAnioAnterior = new Date('<?php echo $ff_anio_anterior; ?>');

  const mesAnioActual = fechaInicioActual.getFullYear(); // Ejemplo: 06-2025
  const mesAnioAnterior = fechaInicioAnioAnterior.getFullYear(); // Ejemplo: 06-2024

  datosCharts["<?php echo $chartId; ?>"] = {
    DCS_CODE: {
      labels: <?php echo json_encode(array_keys($por_dcs)); ?>,
      pares: <?php echo json_encode(array_values($por_dcs)); ?>,
      venta: <?php echo json_encode(array_values($por_dcs_venta)); ?>,
      pares_anio_anterior: <?php echo json_encode(array_values($por_dcs_anio_anterior)); ?>,
      venta_anio_anterior: <?php echo json_encode(array_values($por_dcs_venta_anio_anterior)); ?>
    },
    VEND_NAME: {
      labels: <?php echo json_encode(array_keys($por_prov)); ?>,
      pares: <?php echo json_encode(array_values($por_prov)); ?>,
      venta: <?php echo json_encode(array_values($por_prov_venta)); ?>,
      pares_anio_anterior: <?php echo json_encode(array_values($por_prov_anio_anterior)); ?>,
      venta_anio_anterior: <?php echo json_encode(array_values($por_prov_venta_anio_anterior)); ?>
    },
    C_NAME: {
      labels: <?php echo json_encode(array_keys($por_cat)); ?>,
      pares: <?php echo json_encode(array_values($por_cat)); ?>,
      venta: <?php echo json_encode(array_values($por_cat_venta)); ?>,
      pares_anio_anterior: <?php echo json_encode(array_values($por_cat_anio_anterior)); ?>,
      venta_anio_anterior: <?php echo json_encode(array_values($por_cat_venta_anio_anterior)); ?>
    }
  };

  charts["<?php echo $chartId; ?>"] = new Chart(ctx_<?php echo $tienda_actual; ?>, {
    type: 'bar',
    data: {
      labels: datosCharts["<?php echo $chartId; ?>"].DCS_CODE.labels,
      datasets: [
        {
          label: 'Pares vendidos (' + mesAnioActual + ')',
          data: datosCharts["<?php echo $chartId; ?>"].DCS_CODE.pares,
          backgroundColor: 'rgba(24, 9, 233, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        },
        {
          label: 'Monto vendido (GTQ) (' + mesAnioActual + ')',
          data: datosCharts["<?php echo $chartId; ?>"].DCS_CODE.venta,
          backgroundColor: 'rgba(0, 200, 83, 0.6)',
          borderColor: 'rgba(0, 200, 83, 1)',
          borderWidth: 1
        },
        {
          label: 'Pares vendidos (' + mesAnioAnterior + ')',
          data: datosCharts["<?php echo $chartId; ?>"].DCS_CODE.pares_anio_anterior,
          backgroundColor: 'rgba(233, 9, 9, 0.6)',
          borderColor: 'rgba(255, 99, 132, 1)',
          borderWidth: 1
        },
        {
          label: 'Monto vendido (GTQ) (' + mesAnioAnterior + ')',
          data: datosCharts["<?php echo $chartId; ?>"].DCS_CODE.venta_anio_anterior,
          backgroundColor: 'rgba(255, 159, 64, 0.6)',
          borderColor: 'rgba(255, 159, 64, 1)',
          borderWidth: 1
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Cantidad / Monto (GTQ)'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Categoría'
          }
        }
      }
    }
  });
</script>

  <hr>
<?php endforeach; ?>

  <!-- Script que aplica el filtro global a todos los gráficos -->
  <script>
  document.getElementById('filtroGrafico').addEventListener('change', function () {
    const filtro = this.value;
    for (const chartId in charts) {
      const chart = charts[chartId];
      const datos = datosCharts[chartId][filtro];

      chart.data.labels = datos.labels;
      chart.data.datasets[0].data = datos.pares;
      chart.data.datasets[1].data = datos.venta;
      chart.data.datasets[2].data = datos.pares_anio_anterior;
      chart.data.datasets[3].data = datos.venta_anio_anterior;
      chart.update();
    }
  });
</script>

</div>
