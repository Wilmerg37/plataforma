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
  //let tipoDeposito = $('#tipoDeposito option').html();
// Guardamos los selects originales
let originalTipoDeposito = $('#tipoDeposito').html();
let originalBanco = $('#bancoDeposito').html();

 $('#corte').on('change', function () {
  let corte = $(this).val();

  // Restaurar selects a su estado original
  $('#tipoDeposito').empty().append(originalTipoDeposito).prop('disabled', false);
  $('#bancoDeposito').empty().append(originalBanco).prop('disabled', false).closest('.col').show();

  // Mapeos de filtro y bancos
  const filtrosDeposito = {
    '1': 'VISA',
    '2': 'CREDI',
    '3': 'VANA'
  };

  const bancosPorCorte = {
    '1': 'Banco Promerica Tarjeta',
    '2': 'Banco de America Central BAC Tarjeta',
    '3': 'Vana Pay Tarjeta'
  };

  if (corte === '0') {
    // Corte Sistema → solo efectivo
    $('#tipoDeposito').val('1').prop('disabled', true);
    $('#bancoDeposito').prop('disabled', false); // Si quieres permitir cambiar banco
  } else if (['1', '2', '3'].includes(corte)) {
    // Filtrar tipoDeposito
    $('#tipoDeposito option').each(function () {
      if (!$(this).text().toUpperCase().includes(filtrosDeposito[corte])) {
        $(this).remove();
      }
    });

    // Seleccionar automáticamente el banco correspondiente
    $('#bancoDeposito option').each(function () {
      if ($(this).text().trim().toUpperCase() === bancosPorCorte[corte].toUpperCase()) {
        $(this).prop('selected', true);
      }
    });

    // Deshabilitar el select para evitar cambios manuales
    $('#bancoDeposito').prop('disabled', true);
  }
});




$('#frmDeposito').on('submit', function(e) {
  e.preventDefault(); // evitar recarga de página

  let datos = $(this).serialize();

  $.ajax({
    url: '../Funsiones/digitacion/insertar_deposito.php', // ruta correcta a tu archivo PHP
    method: 'POST',
    data: datos,
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        alert(response.message);
        // Limpiar formulario o cerrar modal si tienes
        $('#frmDeposito')[0].reset();

        // Actualizar tabla si usas DataTables
        if (tblDepositos) {
          tblDepositos.ajax.reload();
        }
      } else {
        alert('Error: ' + response.message);
      }
    },
    error: function() {
      alert('Error al conectar con el servidor');
    }
  });
});





});