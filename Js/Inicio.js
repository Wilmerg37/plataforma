$(document).ready(function () {

  $('section.content').load('tablero.php');
  $('#titulo').text('Tablero');

  $('.modulo').click(function () {
    $('.modulo').removeClass('active');
    $(this).addClass('active');
  });

  $('.opcion').click(function () {
    $('.opcion').removeClass('active');
    $(this).addClass('active');
  });

    //----------------------------------------- MODULOS DEL SIDEBAR -----------------------------------------

    // TIENDAS

    //prueba git

 
    $('#8-71 a').on('click',function(){
      $('section.content').load('filtro_t.php');
      $('#titulo').text('Resumen de desempeño semanal');
      sbs = $('#subsidiaria').val();
      pagina = 'rds';
    });

    $('#8-72 a').on('click',function(){
      $('section.content').load('filtro_t.php');
      $('#titulo').text('Analisis Ventas X Vendedor');
      sbs = $('#subsidiaria').val();
      pagina = 'avxv';
    });

    $('#8-73 a').on('click', function () {
      $('section.content').load('tienda/depositos.php');
      $('#titulo').text('Ingreso de cortes');
    });

    
 $('#8-74 a').on('click', function () {
      $('section.content').load('filtro_s.php');
      $('#titulo').text('Resumen Trimestral por Tienda');
      sbs = $('#subsidiaria').val();
      pagina = 'rtt';
    });

    $('#8-75 a').on('click', function () {
      $('section.content').load('filtro_t.php');
      $('#titulo').text('Existencias Ventas Compras');
      sbs = $('#subsidiaria').val();
      pagina = 'evct';
    });

     //Reporte de HORARIOS TDS
   $('#8-76 a').on('click',function(){
    $('section.content').load('filtro_st.php');
    $('#titulo').text('Reporte de Horarios Tiendas');
    sbs = $('#subsidiaria').val();
    pagina = 'rhts';
  });

// reporte pares x vendedor y proveedor
    $('#8-77 a').on('click', function () {
      $('section.content').load('filtro_t.php');
      $('#titulo').text('Venta Pares X Vendedor y Proveedor');
      sbs = $('#subsidiaria').val();
      pagina = 'vpxv';
    });


        //Reporte de VENTAS X CATEGORIA
   $('#8-78 a').on('click',function(){
    $('section.content').load('filtro_st.php');
    $('#titulo').text('Reporte de VENTAS X CATEGORIA');
    sbs = $('#subsidiaria').val();
    pagina = 'rvc';
  });

 // reporte los 25 mas Vendidos
    $('#8-79 a').on('click', function () {
      $('section.content').load('filtro_t3.php');
      $('#titulo').text('Top 25 (Los 25 mas Vendidos)');
      sbs = $('#subsidiaria').val();
      pagina = 't25';
    });



      // reporte Back Order
     $('#8-80 a').on('click', function () {
      $('section.content').load('tienda/backorder.php');
      $('#titulo').text('Ingreso de Backorder');
    });

 // reporte corte diario
    $('#8-111 a').on('click', function () {
      $('section.content').load('filtro_t3.php');
      $('#titulo').text('Corte Diario');
      sbs = $('#subsidiaria').val();
      pagina = 'diarioventas';
    });

  
    // SUPERVISORES *******************************************
    //******************************************************* */


//VENTAS 14 Y 17 HRS
    $('#9-81 a').on('click',function(){
      $('section.content').load('filtro_s.php');
      $('#titulo').text('Ventas 14 Y 17 Horas Tiendas');
      sbs = $('#subsidiaria').val();
      pagina = 'vts14';
    });

 //RDS
    $('#9-82 a').on('click',function(){
      $('section.content').load('filtro_st.php');
      $('#titulo').text('Resumen de Desempeño');
      sbs = $('#subsidiaria').val();
      pagina = 'rdst';
    });
    

//RDSR
    $('#9-83 a').on('click',function(){
      $('section.content').load('filtro_s.php');
      $('#titulo').text('Resumen de desempeño semanal Region');
      sbs = $('#subsidiaria').val();
      pagina = 'rdsr';
    });

   //RTT
    $('#9-84 a').on('click', function () {
      $('section.content').load('filtro_st.php');
      $('#titulo').text('Resumen Trimestral por Tienda');
      sbs = $('#subsidiaria').val();
      pagina = 'rtt';
    });

 //RDSC RESUMEN DE DESEMPEÑO SEMANAL CADENA
    $('#9-85 a').on('click',function(){
      $('section.content').load('filtro_s.php');
      $('#titulo').text('Resumen de desempeño semanal Cadena');
      sbs = $('#subsidiaria').val();
      pagina = 'rdsc';
    });
    
      //modulo ingreso horarios
    $('#9-86 a').on('click',function(){
      $('section.content').load('metas.php')
      $('#titulo').text('Asignacion de Horarios');
      sbs = $('#tiendas').val();
      pagina = 'metas';
    });

      //Reporte de marcaje tds
      $('#9-87 a').on('click',function(){
        $('section.content').load('filtro_st.php');
        $('#titulo').text('Reporte de Marcajes por Tienda');
        sbs = $('#subsidiaria').val();
        pagina = 'rmt';
      });

 //CRUD DE USUARIOS Y VENDEDORES
    $('#9-88 a').on('click',function(){
      $('section.content').load('supervision/crudUsuario.php');
      $('#titulo').text('Crud Usuarios');
  });

  //asignacion de metas 16-10-2024
  $('#9-89 a').on('click',function(){
    $('section.content').load('metashorarios.php');
    $('#titulo').text('');
    sbs = $('#tiendas').val();
    pagina = 'metashorarios';
  });

   //REPORTE BONO ESTRELLA
   $('#9-90 a').on('click', function () {
    $('section.content').load('filtro_st.php');
    $('#titulo').text('Bonificacion Estrella');
    sbs = $('#subsidiaria').val();
    pagina = 'rbet';
  });

   //Reporte de marcaje supervisor
   $('#9-181 a').on('click',function(){
    $('section.content').load('filtro_gv.php');
    $('#titulo').text('Reporte de Marcajes Supervisores');
    sbs = $('#subsidiaria').val();
    pagina = 'rms';
  });

    //Reporte de VENTAS X CATEGORIA
   $('#9-182 a').on('click',function(){
    $('section.content').load('filtro_st.php');
    $('#titulo').text('Reporte de VENTAS X CATEGORIA');
    sbs = $('#subsidiaria').val();
    pagina = 'rvc';
  });

    //Reporte de HORARIOS TDS
   $('#9-183 a').on('click',function(){
    $('section.content').load('filtro_st.php');
    $('#titulo').text('Reporte de Horarios Tiendas');
    sbs = $('#subsidiaria').val();
    pagina = 'rhts';
  });

  
  //CRUD DE HORARIOS TDS
      $('#9-184 a').on('click',function(){
        $('section.content').load('filtro_st.php');
        $('#titulo').text('CRUD Horarios Tiendas');
        sbs = $('#subsidiaria').val();
        pagina = 'crudhts';
      });

     
     //CRUD DE RECLUTAMIENTO USUARIOS Y VENDEDORES
    $('#9-185 a').on('click',function(){
      $('section.content').load('supervision/solicitudesv.php');
      $('#titulo').text('Reclutamiento de Personal Tiendas');
  });

      //Reporte de VENTAS X INVENTARIO
   $('#9-186 a').on('click',function(){
    $('section.content').load('filtro_st.php');
    $('#titulo').text('Reporte de VENTAS X INVENTARIO TDS');
    sbs = $('#subsidiaria').val();
    pagina = 'rvit';
  });


  
  /************************************************************ */
  //GERENTE VENTAS Y OPERACIONES g Y c ****************************

   //Reporte de marcaje
   $('#10-91 a').on('click',function(){
    $('section.content').load('filtro_gv.php');
    $('#titulo').text('Reporte de Marcajes Supervisores');
    sbs = $('#subsidiaria').val();
    pagina = 'rms';
  });

  //AUTORIZACION DE VACANTES
    $('#10-92 a').on('click',function(){
      $('section.content').load('GerenteTDS/solicitudesgerente.php');
      $('#titulo').text('Autorizacion de Vacantes');
  });


  //Gestion humana RRHH

   //Reporte de marcaje
   $('#11-101 a').on('click',function(){
    $('section.content').load('filtro_gh.php');
    $('#titulo').text('Reporte de Marcajes Administracion');
    sbs = $('#subsidiaria').val();
    pagina = 'rma';
  });

  //CRUD DE USUARIOS Y VENDEDORES
    $('#11-102 a').on('click',function(){
      $('section.content').load('gestionhumana/solicitudesvrh.php');
      $('#titulo').text('Reclutamiento de Personal Tiendas');
  });


    // DIGITACION********************************
    //**************************************** */

    $('#18-171 a').on('click', function () {
        Swal.fire({
            icon: 'question',
            title: 'Rango fecha a visualizar',
            html: '<input readonly class="form-control fecha1" >',
            confirmButtonText: 'Cargar datos',
            showCancelButton: false,
            onOpen: function () {
                $('.fecha1').daterangepicker({
                    "showDropdowns": true,
                    "showISOWeekNumbers": true,
                    "opens": "center",
                    "autoApply": true,
                    "locale": {
                        "format": "DD-MM-YYYY",
                        "separator": " a ",
                        "weekLabel": "Sm",
                        "daysOfWeek": [
                            "Do",
                            "Lu",
                            "Ma",
                            "Mi",
                            "Ju",
                            "Vi",
                            "Sa"
                        ],
                        "monthNames": [
                            "Enero",
                            "Febrero",
                            "Marzo",
                            "Abril",
                            "Mayo",
                            "Junio",
                            "Julio",
                            "Agosto",
                            "Septiembre",
                            "Octubre",
                            "Noviembre",
                            "Deciembre"
                        ],
                        "firstDay": 0
                    },
                });
            }
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url:'digitacion/crudDeposito.php',
                    type:'POST',
                    datatype:'json',
                    data:{fechas:$('.fecha1').val()},
                    success:function(x){
                        $('section.content').html(x);
                        $('#titulo').text('Crud Depositos');
                    }
                })
            }
        });
    });


  $('#18-172 a').on('click', function () {
    Swal.fire({
      icon: 'question',
      title: 'Rango fecha a visualizar',
      html: '<input readonly class="form-control fecha1" >',
      confirmButtonText: 'Cargar datos',
      showCancelButton: false,
      onOpen: function () {
        $('.fecha1').daterangepicker({
          "showDropdowns": true,
          "showISOWeekNumbers": true,
          "autoApply": true,
          "opens": "center",
          "locale": {
            "format": "DD-MM-YYYY",
            "separator": " a ",
            "weekLabel": "Sm",

            "daysOfWeek": [
              "Do",
              "Lu",
              "Ma",
              "Mi",
              "Ju",
              "Vi",
              "Sa"
            ],
            "monthNames": [
              "Enero",
              "Febrero",
              "Marzo",
              "Abril",
              "Mayo",
              "Junio",
              "Julio",
              "Agosto",
              "Septiembre",
              "Octubre",
              "Noviembre",
              "Deciembre"
            ],
            "firstDay": 0
          },
        });
      }
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: 'digitacion/depositoventa.php',
          type: 'POST',
          datatype: 'json',
          data: { fechas: $('.fecha1').val() },
          success: function (x) {
            $('section.content').html(x);
            $('#titulo').text('Venta vrs depósito');
          }
        })
      }
    });
  });

//INGRESO DE DEPOSITOS
  $('#18-173 a').on('click', function () {
      $('section.content').load('tienda/depositos.php');
      $('#titulo').text('Ingreso de cortes');
    });

    // OPERACIONES TIENDAS

    $('#4-1 a').on('click',function(){
        Swal.fire({
            icon:'question',
            title: 'Datos bono estrella',
            input: 'text',
            inputPlaceholder:'Semanas separadas por coma',
            showCancelButton: false,
            preConfirm:(inputValue) =>{
               if(!inputValue){
                   Swal.showValidationMessage(
                       `Necesita escribir las semanas a generar`
                   )
               }
               else{
                   $.ajax({
                       url: 'operacionesTienda/bonoEstrella.php',
                       type: 'POST',
                       datatype: 'json',
                       data: { semanas: inputValue},
                       success: function (x) {
                           $('section.content').html(x);
                           $('#titulo').text('Reporte bono estrella');
                       }
                   })
               }
            }
        })


    });

    // INFORMATICA

    $('#3-21 a').on('click',function(){
        $('section.content').load('informatica/crudUsuario.php');
        $('#titulo').text('Crud Usuarios');
    });


    $('.btnchatroy').on('click',function(){
      $('.chatroy').addClass('collapsed-card');
    });


    // CONTABILIDAD TIENDAS

  $('#1-1 a').on('click', function () {
    Swal.fire({
      icon: 'question',
      title: 'Rango fecha a visualizar',
      html: '<input readonly class="form-control fecha1" >',
      confirmButtonText: 'Cargar datos',
      showCancelButton: false,
      onOpen: function () {
        $('.fecha1').daterangepicker({
          "showDropdowns": true,
          "showWeekNumbers": false,
          "showISOWeekNumbers": true,
          "autoApply": true,
          "locale": {
            "format": "DD-MM-YYYY",
            "separator": " a ",
            "weekLabel": "Sm",
            "daysOfWeek": [
              "Do",
              "Lu",
              "Ma",
              "Mi",
              "Ju",
              "Vi",
              "Sa"
            ],
            "monthNames": [
              "Enero",
              "Febrero",
              "Marzo",
              "Abril",
              "Mayo",
              "Junio",
              "Julio",
              "Agosto",
              "Septiembre",
              "Octubre",
              "Noviembre",
              "Deciembre"
            ],
            "firstDay": 0
          },
        });
      }
    }).then(function (result) {
      if (result.value) {
        $.ajax({
          url: 'contatienda/comicion.php',
          type: 'POST',
          datatype: 'json',
          data: { fechas: $('.fecha1').val() },
          success: function (x) {
            $('section.content').html(x);
            $('#titulo').text('Comiciones, premios y extras');
          }
        })
      }
    });
  });

});

//Reporte de marcaje admon
$('#1-2 a').on('click',function(){
  $('section.content').load('filtro_gh.php');
  $('#titulo').text('Reporte de Marcajes Administracion');
  sbs = $('#subsidiaria').val();
  pagina = 'rma';
});

//Reporte de marcaje supervisore
$('#1-3 a').on('click',function(){
  $('section.content').load('filtro_gv.php');
  $('#titulo').text('Reporte de Marcajes Supervisores');
  sbs = $('#subsidiaria').val();
  pagina = 'rms';
});

//Reporte de marcaje tds
$('#1-4 a').on('click',function(){
  $('section.content').load('filtro_st.php');
  $('#titulo').text('Reporte de Marcajes por Tienda');
  sbs = $('#subsidiaria').val();
  pagina = 'rmt';
});

 //REPORTE BONO ESTRELLA
   $('#1-5 a').on('click', function () {
    $('section.content').load('filtro_st.php');
    $('#titulo').text('Bonificacion Estrella');
    sbs = $('#subsidiaria').val();
    pagina = 'rbet';
  });


      // AULA VIRTUAL *******************************************
    //******************************************************* */

        $('#19-191 a').on('click',function(){
      $('section.content').load('filtro_t2.php');
      $('#titulo').text('Aula Virtual Capacitaciones');
      sbs = $('#subsidiaria').val();
      pagina = 'videos';
    });

      

    /*VENTAS ANALISIS NUEVOS REPORTES********************************************************** */
    /*********************************************************** */


    //REPORTE RR3 

    $('#21-210 a').on('click', function () {
      $('section.content').load('filtro_va.php');
      $('#titulo').text('RR3 Ventas vrs Stock');
      sbs = $('#subsidiaria').val();
      pagina = 'rr3';
    });

     //REPORTE RR4
        $('#21-211 a').on('click', function () {
      $('section.content').load('filtro_va.php');
      $('#titulo').text('RR4 Ventas Existencias Tienda y Estilo');
      sbs = $('#subsidiaria').val();
      pagina = 'rr4';
    });
