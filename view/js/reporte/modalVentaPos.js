function ModalVentaPos() {

    this.init = function () {
        $("#btnVentaPos").click(function () {
            $("#modalVentaPos").modal("show");
        });

        $("#btnVentaPos").keypress(function (event) {
            if (event.keyCode == 13) {
                $("#modalVentaPos").modal("show");
                event.preventDefault();
            }
        });

        $("#rbSelectComprobanteVp").change(function () {
            $("#cbComprobanteVp").prop('disabled', $("#rbSelectComprobanteVp").is(":checked"));

        });

        $("#rbSelectClienteVp").change(function () {
            $("#cbClienteVp").prop('disabled', $("#rbSelectClienteVp").is(":checked"));

        });

        $("#rbSelectVendedorVp").change(function () {
            $("#cbVendedorVp").prop('disabled', $("#rbSelectVendedorVp").is(":checked"));

        });

        $("#rbSelectTipoCobroVp").change(function () {
            $("#cbTipoCobroVp").prop('disabled', $("#rbSelectTipoCobroVp").is(":checked"));
        });

        $("#rbSelectMetodoCobroVp").change(function () {
            $("#cbMetodoCobroVp").prop('disabled', $("#rbSelectMetodoCobroVp").is(":checked"));
        });

        $("#btnPdfVentaPos").click(function () {
            onEventPdfVentaPos();
        });

        $("#btnPdfVentaPos").keypress(function (event) {
            if (event.keyCode == 13) {
                onEventPdfVentaPos();
                event.preventDefault();
            }
        });

        $("#btnExcelVentaPos").click(function () {
            onEventExcelVentaPos();
        });

        $("#btnExcelVentaPos").keypress(function (event) {
            if (event.keyCode == 13) {
                onEventExcelVentaPos();
                event.preventDefault();
            }
        });

        loadInitVentaPos();
    }

    function loadInitVentaPos() {
        $("#modalVentaPos").on("show.bs.modal", async function () {
            $("#txtFechaInicioVp").val(tools.getCurrentDate());
            $("#txtFechaFinalVp").val(tools.getCurrentDate());

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

                $("#cbComprobanteVp").empty();
                $("#cbComprobanteVp").append('<option value=""> - Seleccione -</option>');
                for (let value of comprobantes) {
                    $("#cbComprobanteVp").append('<option value="' + value.IdTipoDocumento + '">' + value.Nombre + '</option>');
                }

                let clientes = result[1];
                $("#cbClienteVp").empty();
                $("#cbClienteVp").append('<option value=""> - Seleccione -</option>');
                for (let value of clientes) {
                    $("#cbClienteVp").append('<option value="' + value.IdCliente + '">' + value.Informacion + '</option>');
                }

                let vendedores = result[2];
                $("#cbVendedorVp").empty();
                $("#cbVendedorVp").append('<option value=""> - Seleccione -</option>');
                for (let value of vendedores) {
                    $("#cbVendedorVp").append('<option value="' + value.IdEmpleado + '">' + value.Apellidos + ' ' + value.Nombres + '</option>');
                }

                $("#cbTipoCobroVp").empty();
                $("#cbTipoCobroVp").append('<option value=""> - Seleccione -</option>');
                $("#cbTipoCobroVp").append('<option value="1">AL CONTADO</option>');
                $("#cbTipoCobroVp").append('<option value="2">AL CRÉDITO</option>');

                $("#cbMetodoCobroVp").empty();
                $("#cbMetodoCobroVp").append('<option value=""> - Seleccione -</option>');
                $("#cbMetodoCobroVp").append('<option value="1">EFECTIVO</option>');
                $("#cbMetodoCobroVp").append('<option value="2">TARJETA</option>');
                $("#cbMetodoCobroVp").append('<option value="3">MIXTO (EFECTIVO y TARJETA)</option>');
                $("#cbMetodoCobroVp").append('<option value="4">DEPÓSITO</option>');

                $("#divOverlayVentaPos").addClass("d-none");

            } catch (error) {
                $("#lblTextOverlayVentaPos").html(tools.messageError(error));
            }
        });

        $("#modalVentaPos").on("hide.bs.modal", async function () {
            $("#divOverlayVentaPos").removeClass("d-none");
            $("#lblTextOverlayVentaPos").html("Cargando información...");

            $("#rbSelectComprobanteVp").prop("checked", true);
            $("#rbSelectClienteVp").prop("checked", true);
            $("#rbSelectVendedorVp").prop("checked", true);
            $("#rbSelectTipoCobroVp").prop("checked", true);
            $("#rbSelectMetodoCobroVp").prop("checked", true);

            $("#cbComprobanteVp").prop("disabled", true);
            $("#cbClienteVp").prop("disabled", true);
            $("#cbVendedorVp").prop("disabled", true);
            $("#cbTipoCobroVp").prop("disabled", true);
            $("#cbMetodoCobroVp").prop("disabled", true);

        });
    }

    function onEventPdfVentaPos() {
        if ($("#divOverlayVentaPos").hasClass("d-none")) {
            if (!$("#rbSelectComprobanteVp").is(":checked") && $("#cbComprobanteVp").val() == '') {
                tools.AlertWarning("", "Seleccione un comprobante.");
                $("#cbComprobanteVp").focus();
            } else if (!$("#rbSelectClienteVp").is(":checked") && $("#cbClienteVp").val() == '') {
                tools.AlertWarning("", "Seleccione un cliente.");
                $("#cbClienteVp").focus();
            } else if (!$("#rbSelectVendedorVp").is(":checked") && $("#cbVendedorVp").val() == '') {
                tools.AlertWarning("", "Seleccione un vendedor.");
                $("#cbVendedorVp").focus();
            } else if (!$("#rbSelectTipoCobroVp").is(":checked") && $("#cbTipoCobroVp").val() == '') {
                tools.AlertWarning("", "Seleccione un tipo de cobro.");
                $("#cbTipoCobroVp").focus();
            } else if (!$("#rbSelectMetodoCobroVp").is(":checked") && $("#cbMetodoCobroVp").val() == '') {
                tools.AlertWarning("", "Seleccione un meotodo de cobro.");
                $("#cbMetodoCobroVp").focus();
            } else {
                let params = new URLSearchParams({
                    procedencia: 2,
                    fechaInicial: $("#txtFechaInicioVp").val(),
                    fechaFinal: $("#txtFechaFinalVp").val(),
                    tipoComprobante: $("#rbSelectComprobanteVp").is(":checked") ? 0 : $("#cbComprobanteVp").val(),
                    nombreComprobante: $("#rbSelectComprobanteVp").is(":checked") ? "TODOS" : $('#cbComprobanteVp option:selected').html(),
                    idCliente: $("#rbSelectClienteVp").is(":checked") ? "" : $("#cbClienteVp").val(),
                    nombreCliente: $("#rbSelectClienteVp").is(":checked") ? "TODOS" : $("#cbClienteVp option:selected").html(),
                    idVendedor: $("#rbSelectVendedorVp").is(":checked") ? "" : $("#cbVendedorVp").val(),
                    nombreVendedor: $("#rbSelectVendedorVp").is(":checked") ? "TODOS" : $("#cbVendedorVp option:selected").html(),
                    tipoCobro: $("#rbSelectTipoCobroVp").is(":checked") ? 0 : $("#cbTipoCobroVp").val(),
                    nombreCobro: $("#rbSelectTipoCobroVp").is(":checked") ? "TODOS" : $("#cbTipoCobroVp option:selected").html(),
                    metodo: $("#rbSelectMetodoCobroVp").is(":checked"),
                    idMetodo: $("#rbSelectMetodoCobroVp").is(":checked") ? 0 : $("#cbMetodoCobroVp").val(),
                    nombreMetodo: $("#rbSelectMetodoCobroVp").is(":checked") ? "TODOS" : $("#cbMetodoCobroVp option:selected").html(),
                });
                window.open("../app/sunat/pdfventageneral.php?" + params, "_blank");
            }
        }
    }

    function onEventExcelVentaPos() {
        window.open("../app/sunat/excelventageneral.php", "_blank");
    }

}