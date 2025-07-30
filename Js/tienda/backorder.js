$(document).ready(function () {

  // Inicializar DataTable
  const tblBackorder = $('#tblBackorder').DataTable({
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
      url: "../Funsiones/tienda/backorder.php", // Nuevo archivo para consultar backorders
      method: 'POST',
      data: {},
      dataSrc: ""
    },
    columns: [
      {
      data: null,
      render: function (data, type, row, meta) {
        return meta.row + 1;
      }
    },
      { data: "fecha" },
      { data: "tienda" },
      { data: "estilo" },
      { data: "grupo" },
      { data: "color" },
      { data: "talla" },
      { data: "desc2" },
      { data: "razon" },
      { data: "comentario" }
    ]
  });

  // Concatenar desc2 automáticamente al escribir en estilo, grupo, color o talla
  function actualizarDescripcion() {
    const estilo = $('#estilo').val().trim();
    const grupo = $('#grupo').val().trim();
    const color = $('#color').val().trim();
    const talla = $('#talla').val().trim();
    $('#desc2').val(`${estilo}-${grupo}-${color}-${talla}`);
  }

  $('#estilo, #grupo, #color, #talla').on('input', actualizarDescripcion);

  // Manejar envío del formulario
  $('#frmBackorder').on('submit', function (e) {
    e.preventDefault();

    let datos = $(this).serialize();

    $.ajax({
  url: '../Funsiones/tienda/insertar_backorder.php',
  method: 'POST',
  data: datos,
  dataType: 'json',
  success: function (response) {
    console.log("Respuesta del servidor:", response);
    if (response.status === 'success') {
      alert(response.message);
      $('#frmBackorder')[0].reset();
      $('#desc2').val('');
      tblBackorder.ajax.reload();
    } else {
      alert('Error: ' + response.message);
    }
  },
  error: function (xhr, status, error) {
    console.error("Error AJAX:", error);
    console.log("Respuesta completa:", xhr.responseText);
    alert("Error de conexión con el servidor.");
  }
});

  });

});
