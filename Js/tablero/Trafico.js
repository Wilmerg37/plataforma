// $(document).ready(function () {

//     $.ajax({
//         url:'../../Funsiones/Trafico.php',
//         type:'POST',
//         datatype:'json',
//         success:function(x){
//             trafico = $.parseJSON(x);
//             //let resultado = trafico['data'].filter(traficos => traficos['@attributes'].storeId == 'ROYT01');

//             // Object.values(resultado[0]).forEach(valor => {
//             //     $('.Trafico').text(Math.trunc(valor.trafficValue));
//             // });
//           //console.log(trafico);
//           //console.log(trafico.data);

//             let total = 0;
//             trafico.data.forEach(element => {
//               total += parseInt(element['@attributes'].trafficValue);
//             });
//             console.log(trafico);
//             //$('.Trafico').text(total);


//             // let resultado = trafico['data'];
//             // $('.Trafico').text(Math.trunc(resultado['@attributes'].trafficValue));
//             // console.log(trafico);
//             // // console.log(resultado['@attributes'].trafficValue);

//         }
//     });


// });