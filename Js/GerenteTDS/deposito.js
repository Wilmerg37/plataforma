$(document).ready(function () {

  tblDepositos = $('#tblDeposito').DataTable({
    "responsive": true,
    "autoWidth": false,

    "language": {
      "sProcessing": "Procesando...",
      "sLengthMenu": "Mostrar _MENU_ registros",
      "sZeroRecords": "No se encontraron resultados",
      "sEmptyTable": "Ningún dato disponible en esta tabla =(",
      "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
      "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix": "",
      "sSearch": "Buscar:",
      "sUrl": "",
      "sInfoThousands": ",",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
        "sFirst": "Primero",
        "sLast": "Último",
        "sNext": "Siguiente",
        "sPrevious": "Anterior"
      }
    },
    "ajax": {
      "url": "../Funsiones/tienda/deposito.php",
      "method": 'POST',
      "data": {},
      "dataSrc": ""
    },
    "columns": [
      { "data": 0 },
      { "data": 1 },
      { "data": 2 },
      { "data": 3 },
      { "data": 4 },
      { "data": 5 , "render": $.fn.dataTable.render.number(',', '.', 2, 'Q ') },
      { "data": 6 }
    ]
  });

  let banco;

  $('#bancoDeposito').map(function(){
    banco = this;
  });


  console.log(banco);
  //let tipoDeposito = $('#tipoDeposito option');

  $('#corte').on('change', function () {
    $('#bancoDeposito option').remove();

    switch ($('#corte').val()) {
      case '0':
        tipocorte = $('#tipoDeposito');
        tipocorte.val(1);
        tipocorte.prop('disabled', true);

        $('#bancoDeposito option').map(function () {
          if (this.value == 5 || this.value == 8 || this.value == 12) {
            this.remove();
          }
        });
        break;
      case '1':
        $('#tipoDeposito option').map(function () {
           if ($(this).text().indexOf('VISA') == -1){
             this.remove();
           }
          banco = $('#bancoDeposito');
          banco.val(8);
          banco.prop('disabled', true);
        });
        break;
      case '2':
        $('#tipoDeposito option').map(function () {
          if ($(this).text().indexOf('CREDI') == -1) {
            this.remove();
          }
          banco = $('#bancoDeposito');
          banco.val(5);
          banco.prop('disabled', true);
        });
        break;
      default:
        break;
    }


  });







});