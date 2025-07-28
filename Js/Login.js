$(document).ready(function () {

    $('#user').select();

    $('#frmLogin').submit(function (e) {
        e.preventDefault();
        datos = $('#frmLogin').serialize();
        $.ajax({
            url: "Funsiones/Login.php",
            type: "POST",
            datatype: "json",
            data: datos,
            success: function (x) {
                if(x == 0){
                    $(function () {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        Toast.fire({
                            icon: 'error',
                            title: '¡ Contraseña invalida ! por favor intente nuevamente'
                        })
                    });

                    $('.form-control').removeClass('is-invalid');
                    $('.form-control').removeClass('is-valid');

                    $('#pass').addClass('is-invalid');
                    $('#user').addClass('is-valid');


                    $('#pass').select();
                }
                else if(x == 1){
                    window.open('Page/inicio.php', '_self');
                }
                else if(x == 2){
                    $(function () {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        Toast.fire({
                            icon: 'error',
                            title: '¡ Usuario invalido ! por favor intente nuevamente'
                        })
                    });

                    $('.form-control').removeClass('is-invalid');
                    $('.form-control').removeClass('is-valid');

                    $('#user').addClass('is-invalid');

                    $('#user').select();
                }
                else{
                    $(function () {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        Toast.fire({
                            icon: 'error',
                            title: '¡ Error inesperado, error:' + x
                        })
                    });
                }
            }
        });
    });
});