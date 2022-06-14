

// $(document).ready(function () {
//     loadNotificaciones();
// });

// function loadNotificaciones() {
//     $.ajax({
//         url: "../app/controller/VentaController.php",
//         method: "GET",
//         data: {
//             "type": "listarNotificaciones",
//         },
//         beforeSend: function () {
//             $("#divNotificaciones").empty();
//             $("#lblNotificaciones").html("Cargando Notificaciones...");
//         },
//         success: function (result) {
//             if (result.estado == 1) {
//                 let notificaciones = result.data;
//                 if (notificaciones.length == 0) {
//                     $("#lblNumeroNotificaciones").html(0)
//                     $("#lblNotificaciones").html("No hay notificaciones para mostrar.");
//                 } else {
//                     $("#lblNotificaciones").html("");
//                     $("#lblNumeroNotificaciones").html(notificaciones.length);
//                     for (let noti of notificaciones) {
//                         $("#divNotificaciones").append('' +
//                             '<li>' +
//                             '   <a class="app-notification__item" href="mostrarnotificaciones.php"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-warning fa-stack-1x fa-inverse"></i></span></span>' +
//                             '       <div>' +
//                             '           <p class="app-notification__message">' + noti.Cantidad + ' ' + noti.Nombre + '</p>' +
//                             '           <p class="app-notification__meta">' + noti.Estado + '</p>' +
//                             '       </div>' +
//                             '   </a>' +
//                             '</li>');
//                     }
//                 }
//             } else {
//                 $("#lblNumeroNotificaciones").html(0)
//                 $("#lblNotificaciones").html("No hay notificaciones para mostrar.");
//             }
//         },
//         error: function (error) {
//             $("#lblNotificaciones").html("Error al cargar las notificaciones.");
//         }
//     });

// }