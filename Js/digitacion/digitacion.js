
$(document).ready(function () {
var opcion, fila, fechas;

    fechas = $('#rangoFecha').val();
    opcion = 1;

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
            "url": "../Funsiones/digitacion/crudDeposito.php",
            "method": 'POST',
            "data": { opcion: opcion, fechas:fechas },
            "dataSrc": ""
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5, "render" : $.fn.dataTable.render.number(',', '.', 2, 'Q ') },
            { "data": 6 },
            { "defaultContent": "<div><div class='btn-group'><button class='btn btn-secondary btn-sm btnVerDeposito'><i class='fas fa-eye'></i></button><button class='btn btn-primary btn-sm btnEditarDeposito'><i class='fas fa-edit'></i></button><button class='btn btn-danger btn-sm btnBorrarDeposito'><i class='fas fa-trash'></i></button></div></div>" }
        ]
    });



    $('#frmModalDeposito').submit(function(e){
        e.preventDefault();
        datos = $('#frmModalDeposito').serialize();

        $.ajax({
            url:'../Funsiones/digitacion/crudDeposito.php',
            type:'POST',
            datatype:'json',
            data:datos+'&opcion='+opcion+'&id='+id,
            success:function(x){
                if(x === 'true'){
                    $(function () {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: 'success',
                            title: ' Accion realizada exitosamente.'
                        });
                        tblDepositos.ajax.reload(null, false);
                    });

                }
                else{
                    $(function () {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: 'error',
                            title: ' Error al tratar de realizar la accion.'
                        });
                    });
                }
            }
        });
        $('#modalDeposito').modal('hide');
    });


    $(document).on('click', '.btnCrearDeposito', function () {

        opcion = 2;
        id = null;
        $('#frmModalDeposito .form-control').removeAttr('readonly', '');
        $("#frmModalDeposito").trigger("reset");
        $('#modalDeposito .modal-header').addClass('bg-success');
        $('#modalDeposito .modal-header').removeClass('bg-primary');
        $('#modalDeposito .modal-title').text('Crear depositos');
        $('#btnOkModalDeposito').html('<i class="fas fa-plus"></i> Crear deposito');
        $('.comentario').removeClass('d-none');
        $('#explicacion').addClass('d-none');
        $('#explicacionDeposito').removeAttr('required');
        $('#modalDeposito').modal('show');
    });



    $(document).on('click', '.btnVerDeposito', function () {

      $('#frmModalDeposito .form-control').attr('readonly','');

      fila = $(this).closest("tr");
      id = parseInt(fila.find('td:eq(0)').text());

      fila = $(this).closest('tr');
      id = parseInt(fila.find('td:eq(0)').text());

      $.ajax({
        url: '../Funsiones/digitacion/crudDeposito.php',
        type: 'POST',
        datatype: 'json',
        data: { opcion: 5, id: id },
        success: function (x) {

          datos = $.parseJSON(x);

          noDeposito = datos[0];
          montoDeposito = datos[1];
          fechaDeposito = datos[2];
          comentario = datos[3];
          bancoDeposito = datos[4];
          tipoDeposito = datos[5];
          tienda = datos[6];

          $('#noDeposito').val(noDeposito);
          $('#montoDeposito').val(montoDeposito);
          $('#fechaDeposito').val(fechaDeposito);
          $('#comentario').val(comentario);
          $('#bancoDeposito').val(bancoDeposito);
          $('#tipoDeposito').val(tipoDeposito);
          $('#tiendaDeposito').val(tienda);

        }
      });

        $('#modalDeposito .modal-header').addClass('bg-secondary');
        $('#modalDeposito .modal-header').removeClass('bg-success');
        $('#modalDeposito .modal-title').text('Visualizar depositos');
        $('#explicacion').removeClass('d-none');
        $('.comentario').removeClass('d-none');
        $('#explicacion').addClass('d-none');
        $('#explicacionDeposito').removeAttr('required');
        $('#btnOkModalDeposito').hide();
        $('#modalDeposito').modal('show');
    });


    $(document).on('click','.btnEditarDeposito',function(){

      opcion = 3;

      $('#frmModalDeposito .form-control').removeAttr('readonly', '');

      fila = $(this).closest("tr");
      id = parseInt(fila.find('td:eq(0)').text());

      $.ajax({
        url: '../Funsiones/digitacion/crudDeposito.php',
        type: 'POST',
        datatype: 'json',
        data: { opcion: 5, id: id },
        success: function (x) {

          datos = $.parseJSON(x);

          noDeposito = datos[0];
          montoDeposito = datos[1];
          fechaDeposito = datos[2];
          comentario = datos[3];
          bancoDeposito = datos[4];
          tipoDeposito = datos[5];
          tienda = datos[6];

          $('#noDeposito').val(noDeposito);
          $('#montoDeposito').val(montoDeposito);
          $('#fechaDeposito').val(fechaDeposito);
          $('#comentario').val(comentario);
          $('#bancoDeposito').val(bancoDeposito);
          $('#tipoDeposito').val(tipoDeposito);
          $('#tiendaDeposito').val(tienda);
          $('#modalDeposito .modal-title').text('Editar depositos ID: '+ id);

        }
      });

      $('#modalDeposito .modal-header').addClass('bg-primary');
      $('#modalDeposito .modal-header').removeClass('bg-success');
      $('#modalDeposito .modal-header').removeClass('bg-secondary');

      $('#btnOkModalDeposito').html('<i class="fas fa-sync-alt"></i> Actualizar depósito');
      $('#explicacion').removeClass('d-none');
      $('.comentario').addClass('d-none');
      $('#explicacionDeposito').attr('required','');
      $("#explicacionDeposito").val('');
      $('#btnOkModalDeposito').show();
      $('#modalDeposito').modal('show');

    });


    $(document).on("click", ".btnBorrarDeposito", function () {
        opcion = 4;
        id = parseInt($(this).closest('tr').find('td:eq(0)').text());
        noDeposito = parseInt($(this).closest('tr').find('td:eq(4)').text());

        Swal.fire({
            icon:'warning',
            title: 'Estas seguro?',
            text:'Eliminaras el deposito no: '+noDeposito,
            input: 'text',
            inputPlaceholder:'Escribir razon de eliminacion',
            confirmButtonText: 'Si, Eliminar!',
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value) {
                    return 'Necesitas escribir la razon de eliminacion!'
                }
                else{
                    $.ajax({
                        url: '../Funsiones/digitacion/crudDeposito.php',
                        type: 'POST',
                        datatype: 'json',
                        data: { opcion: opcion, id: id, explicacionDeposito: value },
                        success: function (x) {
                            if (x === 'true') {
                                tblDepositos.ajax.reload(null, false);
                                $(function () {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                    Toast.fire({
                                        icon: 'success',
                                        title: ' Accion realizada exitosamente.'
                                    });
                                    tblDepositos.ajax.reload(null, false);
                                });
                            }
                            else {
                                $(function () {
                                    const Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                    Toast.fire({
                                        icon: 'error',
                                        title: 'No se pudo realizar la accion.'
                                    });
                                });
                            }
                        }
                    });
                }
            }
        })
    });



});








