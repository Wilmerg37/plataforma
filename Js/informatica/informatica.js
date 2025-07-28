
$(document).ready(function () {
    var opcion, id;

        opcion = 1;

        tblUsuarios = $('#tblUsuario').DataTable({
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
                "url": "../Funsiones/informatica/crudUsuario.php",
                "method": 'POST',
                "data": { opcion: opcion},
                "dataSrc": ""
            },
            "columns": [
                { "data": 0 },
                { "data": 1 },
                { "data": 2 },
                { "data": 3 },
                { "data": 4 },
                { "data": 5 },
                { "data": 6 },
                { "data": 7 },
                { "data": 8 },
                { "data": 9 },
                { "data": 10},
                { "data": 11},
                { "data": 12 },
                { "data": 13 },
                { "data": 14 },
                { "data": 15 },
                { "data": 16 },
                { "data": 17,
                    "className" : "text-center",
                    "render": function(data){
                        var status = '';
                        if(data == 1){
                            status = '<span><i class="fas fa-user-check text-success fa-lg"></i></span>';
                        }
                        else{
                            status = '<span><i class="fas fa-user-times text-danger fa-lg"></i></span>';
                        }
                        return status;
                    }
                },
                { "defaultContent": "<div><div class='btn-group'><button class='btn btn-primary btn-sm btnEditarUsuario'><i class='fas fa-edit'></i></button><button class='btn btn-danger btn-sm btnBorrarUsuario'><i class='fas fa-trash'></i></button></div></div>" }
            ],
            "columnDefs": [
                {
                    "targets": [3,4,5,6,7,9,11,13,15],
                    "visible": false,
                    "searchable": false
                }
            ]
        });



        $('#frmModalUsuario').submit(function(e){
          e.preventDefault();

          var datos = new FormData($('#frmModalUsuario')[0]);
          datos.append('opcion',opcion);
          datos.append('id', id);

          pass = $('#pass');
          confPass = $('#confirmarPass');


              $.ajax({
                url: '../Funsiones/informatica/crudUsuario.php',
                type: 'POST',
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                beforesend: function () { },
                success: function (x) {
                  console.log(x);
                  if (x === 'true') {
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
                      tblUsuarios.ajax.reload(null, false);
                    });
                    $('#modalUsuario').modal('hide');
                  }
                  else if(x == '"existe"'){
                    $(function () {
                      const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                      });
                      Toast.fire({
                        icon: 'error',
                        title: ' El codigo de empleado ya existe.'
                      });
                    });
                    $('#codigo').addClass('is-invalid');
                  }
                  else if (x == '"PassError"') {
                    $(function () {
                      const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                      });
                      Toast.fire({
                        icon: 'error',
                        title: 'No coinciden las contraseñas ingresadas.'
                      });
                    });
                    $('#pass').addClass('is-invalid');
                    $('#confirmarPass').addClass('is-invalid');
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
                        title: ' Error al tratar de realizar la accion.'
                      });
                    });
                    $('#modalUsuario').modal('hide');
                  }
                }
              });

        });


        $(document).on('click', '.btnCrearUsuario', function () {

          opcion = 2;
          id = null;

          $("#frmModalUsuario").trigger("reset");
          $("#frmModalUsuario input").removeClass('is-invalid');
          $('#modalUsuario .modal-header').addClass('bg-success');
          $('#modalUsuario .modal-header').removeClass('bg-primary');
          $('#modalUsuario .modal-title').html(' <i class="fas fa-user-plus"></i> Crear Usuarios');
          $('#btnOkModalUsuario').html(' <i class="fas fa-user-plus"></i> Crear Usuario');
          $('#previewFoto').attr('src', '/Image/user/default.svg');
          $('.contrasena, .fotos').show();
          $('#pass').attr('required', '');
          $('#confirmarPass').attr('required', '');
          $('#pass').removeClass('is-invalid');
          $('#confirmarPass').removeClass('is-invalid');
          $('#modalUsuario').modal('show');

        });


        $(document).on('click','.btnEditarUsuario',function(){

          $('#modalUsuario .modal-header').addClass('bg-primary');
          $('#modalUsuario .modal-header').removeClass('bg-success');
          $('#modalUsuario .modal-header').removeClass('bg-secondary');
          $('#modalUsuario .modal-title').html('<i class="fas fa-user-edit"></i> Editar Usuarios');
          $('#btnOkModalUsuario').html('<i class="fas fa-user-edit"></i> Actualizar Usuario');
          $('.contrasena').hide();
          $('#pass').removeAttr('required', '');
          $('#confirmarPass').removeAttr('required', '');
          $('#btnOkModalUsuario').show();
          $('#modalUsuario').modal('show');

          fila = $(this).closest('tr');
          id = parseInt(fila.find('td:eq(0)').text());

          $.ajax({
            url:'../Funsiones/informatica/crudUsuario.php',
            type:'POST',
            datatype:'json',
            data: {opcion : 5, id:id },
            success: function(x){

              datos = $.parseJSON(x);

              pnombre = datos[0];
              snombre = datos[1];
              papellido = datos[2];
              sapellido = datos[3];
              correo = datos[4];
              codigo = datos[5];  
                    
              pais = datos[9];
              perfil = datos[8];
              departamento = datos[7];
            
              imagen = datos[6];

              $('#pnombre').val(pnombre);
              $('#snombre').val(snombre);
              $('#papellido').val(papellido);
              $('#sapellido').val(sapellido);
              $('#correo').val(correo);
              $('#codigo').val(codigo);  
                       
              $('#pais').val(pais);
              $('#perfil').val(perfil);
              $('#departamento').val(departamento);
              
              ViewImage(pais, imagen);

            }


          });

          opcion = 3;
        });


        $(document).on("click", ".btnBorrarUsuario", function () {

          opcion = 4;

          fila = $(this).closest('tr');
          row = fila.index();

          id = parseInt(fila.find('td:eq(0)').text());
          noUsuario = parseInt(fila.find('td:eq(3)').text());

          Swal.fire({
            title: 'Estas seguro?',
            text: 'Desactivar / Activar el usuario con codigo: ' + noUsuario,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, realizar cambio !'
          }).then((result) => {
            if (result.value) {
              $.ajax({
                url: '../Funsiones/informatica/crudUsuario.php',
                type: 'POST',
                datatype: 'json',
                data: { opcion: opcion, id: id},
                success: function (x) {
                  if (x === 'true') {
                    tblUsuarios.ajax.reload(null, false);
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
                      tblUsuarios.ajax.reload(null, false);
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
          });
        });

      function filePreview(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();
          reader.readAsDataURL(input.files[0]);
          reader.onload = function (e) {
            $('#previewFoto').attr('src',e.target.result);
            $('#labelFoto').text()
          }
        }
        else{
          $('#previewFoto').attr('src','/Image/user/default.svg');
        }
      }

      function ViewImage(pais,imagen){
        if(imagen!=""){
          $('#previewFoto').attr('src', '/Image/user/' + pais + '/' + imagen);
        }
        else{
          $('#previewFoto').attr('src', '/Image/user/default.svg');
        }
      }

      $("#foto").change(function () {
        filePreview(this);
      });

    });








