function ModalVentaLibre() {

    this.init = function () {
        $("#btnVentaLibre").click(function () {
            $("#modalVentaLibre").modal("show");
        });

        $("#btnVentaLibre").keypress(function (event) {
            if (event.keyCode == 13) {
                $("#modalVentaLibre").modal("show");
                event.preventDefault();
            }
        });

        $("#rbSelectComprobanteVl").change(function () {
            $("#cbComprobanteVl").prop('disabled', $("#rbSelectComprobanteVl").is(":checked"));

        });

        $("#rbSelectClienteVl").change(function () {
            $("#cbClienteVl").prop('disabled', $("#rbSelectClienteVl").is(":checked"));

        });

        $("#rbSelectVendedorVl").change(function () {
            $("#cbVendedorVl").prop('disabled', $("#rbSelectVendedorVl").is(":checked"));

        });

        $("#rbSelectTipoCobroVl").change(function () {
            $("#cbTipoCobroVl").prop('disabled', $("#rbSelectTipoCobroVl").is(":checked"));
        });

        $("#rbSelectMetodoCobroVl").change(function () {
            $("#cbMetodoCobroVl").prop('disabled', $("#rbSelectMetodoCobroVl").is(":checked"));
        });

        $("#btnPdfVentaLibre").click(function () {
            onEventPdfVentaLibre();
        });

        $("#btnPdfVentaLibre").keypress(function (event) {
            if (event.keyCode == 13) {
                onEventPdfVentaLibre();
                event.preventDefault();
            }
        });

        $("#btnExcelVentaLibre").click(function () {
            onEventExcelVentaLibre();
        });

        $("#btnExcelVentaLibre").keypress(function (event) {
            if (event.keyCode == 13) {
                onEventExcelVentaLibre();
                event.preventDefault();
            }
        });

        loadInitVentaLibre();
    }

    function loadInitVentaLibre() {
        $("#modalVentaLibre").on("show.bs.modal", async function () {
            $("#txtFechaInicioVl").val(tools.getCurrentDate());
            $("#txtFechaFinalVl").val(tools.getCurrentDate());

            try {
                let promiseFetchComprobante = tools.promiseFetchGet("../app/controller/TipoDocumentoController.php", {
                    "type": "getdocumentocomboboxventas"
                });

                let promiseFetchCliente = tools.promiseFetchGet("../app/controller/ClienteController.php", {
                    "type": "GetListCliente"
                });

                let promiseFetchEmpleado = tools.promiseFetchGet("../app/controller/EmpleadoController.php", {
                    "type": "GetListEmpleados"
                });

                let promise = await Promise.all([
                    promiseFetchComprobante,
                    promiseFetchCliente,
                    promiseFetchEmpleado
                ]);
                let result = await promise;

                let comprobantes = result[0];

                $("#cbComprobanteVl").empty();
                $("#cbComprobanteVl").append('<option value=""> - Seleccione -</option>');
                for (let value of comprobantes) {
                    $("#cbComprobanteVl").append('<option value="' + value.IdTipoDocumento + '">' + value.Nombre + '</option>');
                }

                let clientes = result[1];
                $("#cbClienteVl").empty();
                $("#cbClienteVl").append('<option value=""> - Seleccione -</option>');
                for (let value of clientes) {
                    $("#cbClienteVl").append('<option value="' + value.IdCliente + '">' + value.Informacion + '</option>');
                }

                let vendedores = result[2];
                $("#cbVendedorVl").empty();
                $("#cbVendedorVl").append('<option value=""> - Seleccione -</option>');
                for (let value of vendedores) {
                    $("#cbVendedorVl").append('<option value="' + value.IdEmpleado + '">' + value.Apellidos + ' ' + value.Nombres + '</option>');
                }

                $("#cbTipoCobroVl").empty();
                $("#cbTipoCobroVl").append('<option value=""> - Seleccione -</option>');
                $("#cbTipoCobroVl").append('<option value="1">AL CONTADO</option>');
                $("#cbTipoCobroVl").append('<option value="2">AL CRÉDITO</option>');

                $("#cbMetodoCobroVl").empty();
                $("#cbMetodoCobroVl").append('<option value=""> - Seleccione -</option>');
                $("#cbMetodoCobroVl").append('<option value="1">EFECTIVO</option>');
                $("#cbMetodoCobroVl").append('<option value="2">TARJETA</option>');
                $("#cbMetodoCobroVl").append('<option value="3">MIXTO (EFECTIVO y TARJETA)</option>');
                $("#cbMetodoCobroVl").append('<option value="4">DEPÓSITO</option>');

                $("#divOverlayVentaLibre").addClass("d-none");

            } catch (error) {
                $("#lblTextOverlayVentaLibre").html(tools.messageError(error));
            }
        });

        $("#modalVentaLibre").on("hide.bs.modal", async function () {
            $("#divOverlayVentaLibre").addClass("d-none");
            $("#lblTextOverlayVentaLibre").html("Cargando información...");

            $("#rbSelectComprobanteVl").prop("checked", true);
            $("#rbSelectClienteVl").prop("checked", true);
            $("#rbSelectVendedorVl").prop("checked", true);
            $("#rbSelectTipoCobroVl").prop("checked", true);
            $("#rbSelectMetodoCobroVl").prop("checked", true);

            $("#cbComprobanteVl").prop("disabled", true);
            $("#cbClienteVl").prop("disabled", true);
            $("#cbVendedorVl").prop("disabled", true);
            $("#cbTipoCobroVl").prop("disabled", true);
            $("#cbMetodoCobroVl").prop("disabled", true);
        });
    }

    function onEventPdfVentaLibre() {
        if ($("#divOverlayVentaLibre").hasClass("d-none")) {
            if (!$("#rbSelectComprobanteVl").is(":checked") && $("#cbComprobanteVl").val() == '') {
                tools.AlertWarning("", "Seleccione un comprobante.");
                $("#cbComprobanteVl").focus();
            } else if (!$("#rbSelectClienteVl").is(":checked") && $("#cbClienteVl").val() == '') {
                tools.AlertWarning("", "Seleccione un cliente.");
                $("#cbClienteVl").focus();
            } else if (!$("#rbSelectVendedorVl").is(":checked") && $("#cbVendedorVl").val() == '') {
                tools.AlertWarning("", "Seleccione un vendedor.");
                $("#cbVendedorVl").focus();
            } else if (!$("#rbSelectTipoCobroVl").is(":checked") && $("#cbTipoCobroVl").val() == '') {
                tools.AlertWarning("", "Seleccione un tipo de cobro.");
                $("#cbTipoCobroVl").focus();
            } else if (!$("#rbSelectMetodoCobroVl").is(":checked") && $("#cbMetodoCobroVl").val() == '') {
                tools.AlertWarning("", "Seleccione un meotodo de cobro.");
                $("#cbMetodoCobroVl").focus();
            } else {
                let params = new URLSearchParams({
                    procedencia: 1,
                    fechaInicial: $("#txtFechaInicioVl").val(),
                    fechaFinal: $("#txtFechaFinalVl").val(),
                    tipoComprobante: $("#rbSelectComprobanteVl").is(":checked") ? 0 : $("#cbComprobanteVl").val(),
                    nombreComprobante: $("#rbSelectComprobanteVl").is(":checked") ? "TODOS" : $('#cbComprobanteVl option:selected').html(),
                    idCliente: $("#rbSelectClienteVl").is(":checked") ? "" : $("#cbClienteVl").val(),
                    nombreCliente: $("#rbSelectClienteVl").is(":checked") ? "TODOS" : $("#cbClienteVl option:selected").html(),
                    idVendedor: $("#rbSelectVendedorVl").is(":checked") ? "" : $("#cbVendedorVl").val(),
                    nombreVendedor: $("#rbSelectVendedorVl").is(":checked") ? "TODOS" : $("#cbVendedorVl option:selected").html(),
                    tipoCobro: $("#rbSelectTipoCobroVl").is(":checked") ? 0 : $("#cbTipoCobroVl").val(),
                    nombreCobro: $("#rbSelectTipoCobroVl").is(":checked") ? "TODOS" : $("#cbTipoCobroVl option:selected").html(),
                    metodo: $("#rbSelectMetodoCobroVl").is(":checked"),
                    idMetodo: $("#rbSelectMetodoCobroVl").is(":checked") ? 0 : $("#cbMetodoCobroVl").val(),
                    nombreMetodo: $("#rbSelectMetodoCobroVl").is(":checked") ? "TODOS" : $("#cbMetodoCobroVl option:selected").html(),
                });
                window.open("../app/sunat/pdfventageneral.php?" + params, "_blank");
            }
        }
    }

    function onEventExcelVentaLibre() {
        window.open("../app/sunat/excelventageneral.php", "_blank");
    }

}