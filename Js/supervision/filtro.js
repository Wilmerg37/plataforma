var loading = "<div class='text-center'><div class='spinner-border text-primary' role ='status'><span class='sr-only'>Loading...</span></div ></div >";

$('#sbs').val(sbs);

$('#frmfiltro').submit(function (e) {
    e.preventDefault();

    if ($('#tienda').val() == '' || $('#sbs').val()==""){

        $(function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                background: '#E9ECEF',
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'error',
                title: ' ! Debe llenar todos los campos !'
            })
        });

        $('#tienda').select();

    }
    else{
        $('#Tablas').html(loading);
        data = $('#frmfiltro').serialize();
        $.ajax({
            url: 'supervision/'+pagina+'.php',
            type: 'POST',
            datatype: 'json',
            data: data,
            success: function (x) {
                $('#Tablas').html(x);
            }
        });
    }
});


