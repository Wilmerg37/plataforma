
// Trafico

$.ajax({
  url: '../Funsiones/Trafico.php',
  type: 'POST',
  datatype: 'json',
  success: function (x) {
    trafico = $.parseJSON(x);
    let total = 0;
    trafico.data.forEach(element => {
      if(element['@attributes'].storeId.indexOf('ROYH') == -1){
      total += parseInt(element['@attributes'].trafficValue);
      }
    });
    console.log(trafico);
    $('.Trafico').text(total);

    $('#modalTablero .modal-body').append('<center><table><thead><th>Tienda</th><th>Trafico</th></thead><tbody></tbody></table></center');
    $('table').addClass('table table-hover text-center');

    trafico.data.forEach(element => {
      if (element['@attributes'].storeId.indexOf('ROYH') == -1){
        $('tbody').append('<tr><td>' + element['@attributes'].storeId + '</td><td>' + parseInt(element['@attributes'].trafficValue) + '</td></tr>');
      }
    });

  }
});


// Regiones

$.ajax({
  url: '../Funsiones/tablero/tablero.php',
  type: 'POST',
  datatype: 'json',
  data: { opcion: 2 },
  success: function (x) {
    ventas = $.parseJSON(x);
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartData = {
      labels: ['Ene', 'Feb', 'Mar'],
      datasets: [
        {
          label: 'GIOVANNI',
          backgroundColor: 'rgba(60,141,188,0.9)',
          borderColor: 'rgba(60,141,188,0.8)',
          pointRadius: false,
          pointColor: '#3b8bba',
          pointStrokeColor: 'rgba(60,141,188,1)',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data: ventas['\'GIOVANNI\'']
        },
        {
          label: 'CHRISTIAN',
          backgroundColor: 'rgba(210, 214, 222, 1)',
          borderColor: 'rgba(210, 214, 222, 1)',
          pointRadius: false,
          pointColor: 'rgba(210, 214, 222, 1)',
          pointStrokeColor: '#c1c7d1',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data: ventas['\'CHRISTIAN\'']
        },
      ]
    }

    var barChartOptions = {
      responsive: true,
      datasetFill: false,
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        display: true
      },
      tooltips: {
        mode: 'index',
        intersect: false,
        callbacks: {
          label: function (tooltipItem, data) {
            return "Q " + tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
        }
      },
      hover: {
        mode: 'nearest',
        intersect: true
      },
      scales: {
        xAxes: [{
          display: true,
          gridLines: {
            display: false,
          }
        }],
        yAxes: [{
          display: true,
          ticks: {
            callback: function (nStr) {
              return "Q " + nStr.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
          },
          gridLines: {
            display: true,
          }
        }]
      }
    }

    var barChart = new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })
  }
});



// Ventas Cadena trimestral

$.ajax({
    url:'../Funsiones/tablero/tablero.php',
    type:'POST',
    datatype:'json',
    data:{opcion:1},
    success:function(x) {
        ventas = $.parseJSON(x);
      var salesChartCanvas = document.getElementById('popChart').getContext('2d');
        var salesChartData = {
            labels: ['Feb', 'Mar', 'Abr'],
            datasets: [
                {
                    label: '2024',
                    backgroundColor: 'rgba(60,141,188,1)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    data: ventas['\'2024\''],
                    pointRadius: 5
                },
                {
                    label: '2023',
                    backgroundColor: 'rgba(210, 214, 222, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    data: ventas['\'2023\''],
                    pointRadius: 5
                },
            ]
        }
      var salesChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
          display: true
        },
        tooltips: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: function (tooltipItem, data) {
              return "Q " + tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
          }
        },
        hover: {
          mode: 'nearest',
          intersect: true
        },
        scales: {
          xAxes: [{
            display: true,
            gridLines: {
              display: false,
            }
          }],
          yAxes: [{
            display: true,
            ticks: {
              callback: function (nStr) {
                return "Q " + nStr.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
              }
            },
            gridLines: {
              display: true,
            }
          }]
        }
      }

      var salesChart = new Chart(salesChartCanvas, {
        type: 'line',
        data: salesChartData,
        options: salesChartOptions
      })
      }
});




// Distrito por semana
$.ajax({

  url: '../Funsiones/tablero/tablero.php',
  type: 'POST',
  datatype: 'json',
  data: { opcion: 3 },
  success: function (x) {
    ventas = $.parseJSON(x);
    var salesChartCanvas = document.getElementById('popChart1').getContext('2d');
    var salesChartData = {
      labels: ['16', '17', '18'],
      datasets: [
        {
          label: '2023',
          backgroundColor: 'rgba(60,141,188,0)',
          borderColor: 'rgba(60,141,188,1)',
          pointRadius: false,
          pointColor: '#3b8bba',
          pointStrokeColor: 'rgba(60,141,188,1)',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data: ventas['\'2023\''],
          pointRadius: 5
        },
        {
          label: '2022',
          backgroundColor: 'rgba(210, 214, 222, 0)',
          borderColor: 'rgba(210, 214, 222, 1)',
          pointRadius: false,
          pointColor: 'rgba(210, 214, 222, 1)',
          pointStrokeColor: '#c1c7d1',
          pointHighlightFill: '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data: ventas['\'2022\''],
          pointRadius: 5
        },
      ]
    }
    var salesChartOptions = {
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        display: true
      },
      tooltips: {
        mode: 'index',
        intersect: false,
        callbacks: {
          label: function (tooltipItem, data) {
            return "Q " + tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
        }
      },
      hover: {
        mode: 'nearest',
        intersect: true
      },
      scales: {
        xAxes: [{
          display: true,
          gridLines: {
            display: true,
          }
        }],
        yAxes: [{
          display: true,
          ticks: {
            callback: function (nStr) {
              return "Q " + nStr.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
          },
          gridLines: {
            display: true,
          }
        }]
      }
    }

    var salesChart = new Chart(salesChartCanvas, {
      type: 'line',
      data: salesChartData,
      options: salesChartOptions
    })
  }
});


// venta por proveedor

$.ajax({
  url: '../Funsiones/tablero/tablero.php',
  type: 'POST',
  datatype: 'json',
  data: { opcion: 4 },
  success: function (x) {
    ventas = $.parseJSON(x);
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData = {
      labels: ventas['PROVEEDOR'],
      datasets: [
        {
          data: ventas['VENTA'] ,
          backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc'],
        }
      ]
    }
    var donutOptions = {
      maintainAspectRatio: false,
      responsive: true,
      tooltips: {
        callbacks: {
          label: function (tooltipItem, data) {
            return "Q " + data.datasets[0].data[tooltipItem.index].replace(/\d(?=(\d{3})+\.)/g, '$&,');
          }
        }
      }
    }
   // Create pie or douhnut chart
   //  You can switch between pie and douhnut using the method below.
    var donutChart = new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
    })
  }
});


$('#trafico').on('click', function () {
  $('#modalTablero').modal('show');
  $('#modalTablero .modal-header').addClass('bg-secondary');
  $('#modalTablero .modal-title').text('Trafico de hoy');
});




