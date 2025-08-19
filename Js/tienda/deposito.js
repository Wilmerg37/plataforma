$(document).ready(function () {
  // Inicializar DataTable
  tblDepositos = $('#tblDeposito').DataTable({
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
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior"
      }
    },
    ajax: {
      url: "../Funsiones/tienda/deposito.php",
      method: 'POST',
      data: {},
      dataSrc: ""
    },
    columns: [
      { data: 0 },
      { data: 1 },
      { data: 2 },
      { data: 3 },
      { data: 4 },
      {
        data: 5,
        render: $.fn.dataTable.render.number(',', '.', 2, 'Q ')
      },
      { data: 6 }
    ]
  });

  // Guardamos selects originales
  const originalTipoDeposito = $('#tipoDeposito').html();
  const originalBanco = $('#bancoDeposito').html();

  $('#corte').on('change', function () {
    let corte = $(this).val();

    // Restaurar selects originales
    $('#tipoDeposito').empty().append(originalTipoDeposito).prop('disabled', false);
    $('#bancoDeposito').empty().append(originalBanco).prop('disabled', false).closest('.col').show();

    // Eliminar campo hidden anterior si existe
    $('#hiddenBancoDeposito').remove();

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
    } else if (['1', '2', '3'].includes(corte)) {
      // Filtrar tipoDeposito
      $('#tipoDeposito option').each(function () {
        if (!$(this).text().toUpperCase().includes(filtrosDeposito[corte])) {
          $(this).remove();
        }
      });

      // Seleccionar automáticamente el banco correspondiente
      let bancoSeleccionado = '';
      $('#bancoDeposito option').each(function () {
        if ($(this).text().trim().toUpperCase() === bancosPorCorte[corte].toUpperCase()) {
          $(this).prop('selected', true);
          bancoSeleccionado = $(this).val();
        }
      });

      // Deshabilitar banco y crear campo hidden
      $('#bancoDeposito').prop('disabled', true);

      if ($('#hiddenBancoDeposito').length === 0) {
        $('<input>').attr({
          type: 'hidden',
          id: 'hiddenBancoDeposito',
          name: 'bancoDeposito',
          value: bancoSeleccionado
        }).appendTo('#frmDeposito');
      } else {
        $('#hiddenBancoDeposito').val(bancoSeleccionado);
      }
    }
  });

  // Enviar formulario por AJAX
  $('#frmDeposito').on('submit', function (e) {
    e.preventDefault();

    let datos = $(this).serialize();

    $.ajax({
      url: '../Funsiones/tienda/insertar_deposito.php',
      method: 'POST',
      data: datos,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          alert(response.message);
          $('#frmDeposito')[0].reset();
          $('#hiddenBancoDeposito').remove();

          if (tblDepositos) {
            tblDepositos.ajax.reload();
          }
        } else {
          alert('Error: ' + response.message);
        }
      },
      error: function () {
        alert('Error al conectar con el servidor');
      }
    });
  });
});
