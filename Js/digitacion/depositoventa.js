$(document).ready(function () {
  var fechas = $('#rangoFecha').val();

  $('#tblDepositoVenta').DataTable({
    responsive: true,
    autoWidth: false,
    language: {
      sProcessing: "Procesando...",
      sLengthMenu: "Mostrar _MENU_ registros",
      sZeroRecords: "No se encontraron resultados",
      sEmptyTable: "Ningún dato disponible en esta tabla =(",
      sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
      sSearch: "Buscar:",
      sLoadingRecords: "Cargando...",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior"
      }
    },
    ajax: {
      url: "../Funsiones/digitacion/depositoventa.php",
      method: 'POST',
      data: { fechas: fechas },
      dataSrc: "data"
    },
    columns: [
      { data: "SUBSIDIARIA" },
      { data: "FECHA" },
      { data: "TIENDA" },
      { data: "VENTA", render: $.fn.dataTable.render.number(',', '.', 2, 'Q ') },
      { data: "DEPOSITO", render: $.fn.dataTable.render.number(',', '.', 2, 'Q ') },
      { data: "DIFERENCIA", render: $.fn.dataTable.render.number(',', '.', 2, 'Q ') },
      {
        data: "DIFERENCIA",
        className: "text-center",
        render: function (data) {
          return data == 0
            ? '<span><i class="fas fa-check text-success fa-lg"></i></span>'
            : '<span><i class="fas fa-times text-danger fa-lg"></i></span>';
        }
      }
    ]
  });
});
