$(document).ready(function () {
    var opcion, fila, fechas;

    semana = $('#rangoFecha').val();
    opcion = 1;

    tblBonoEstrella = $('#tblBonoEstrella').DataTable({
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
            "url": "../Funsiones/operacionesTienda/bonoEstrella.php",
            "method": 'POST',
            "data": { opcion: opcion, fechas: fechas },
            "dataSrc": ""
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5, "render": $.fn.dataTable.render.number(',', '.', 2, 'Q') },
            { "data": 6 },
            { "data": 7 },
            { "defaultContent": "<div><div class='btn-group'><button class='btn btn-secondary btn-sm btnVerDeposito'><i class='fas fa-eye'></i></button><button class='btn btn-primary btn-sm btnEditarDeposito'><i class='fas fa-edit'></i></button><button class='btn btn-danger btn-sm btnBorrarDeposito'><i class='fas fa-trash'></i></button></div></div>" }
        ],
        "columnDefs": [
            {
                "targets": [7],
                "visible": false,
                "searchable": false
            }
        ],
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];

                $('row c[r^="C"]', sheet).attr('s', '2');
            }
        }]
    });

});