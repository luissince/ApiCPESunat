function ModalngresoEgreso() {

    this.init = function () {
        $("#btnIngresosEgresos").click(function () {
            openModalIngresosEgresos();
        });

        $("#btnIngresosEgresos").keypress(function (event) {
            if (event.keyCode === 13) {
                openModalIngresosEgresos();
            }
            event.preventDefault();
        });

        $("#cbSelectVendedorIngresoEgreso").change(function () {
            $("#cbVendedorVIngresoEgreso").prop('disabled', $("#cbSelectVendedorIngresoEgreso").is(":checked"));
        });

        loadInitIngresosEgresos();
    }

    function loadInitIngresosEgresos() {
        $("#modalIngresoEgreso").on("shown.bs.modal", async function () {
            $("#txtFIIngresoEgreso").val(tools.getCurrentDate());
            $("#txtFFIngresoEgreso").val(tools.getCurrentDate());

            $("#txtFIIngresoEgreso").focus();
            selectEmpleado();

            $("#btnPdfIngresoEgreso").bind("click", function () {
                if ($("#divOverlayIngresoEgreso").hasClass("d-none")) {
                    if (!$("#cbSelectVendedorIngresoEgreso").is(":checked") && $('#cbVendedorVIngresoEgreso').val() == null) {
                        tools.AlertWarning("", "Seleccione un vendedor.");
                        $('#cbVendedorVIngresoEgreso').focus();
                    } else if (!$("#rbSelectTransaccionesIngresoEgreso").is(":checked") && !$("#rbSelectMovimientoIngresoEgreso").is(":checked")) {
                        tools.AlertWarning("", "Debe seleccionar las transacciones y/o movimientos de caja.");
                        $('#rbSelectTransaccionesIngresoEgreso').focus();
                    }
                    else {
                        let fechaInicial = $("#txtFIIngresoEgreso").val();
                        let fechaFinal = $("#txtFFIngresoEgreso").val();
                        if (tools.validateDate(fechaInicial) && tools.validateDate(fechaFinal)) {
                            let params = new URLSearchParams({
                                "txtFechaInicial": fechaInicial,
                                "txtFechaFinal": fechaFinal,
                                "usuario": $("#cbSelectVendedorIngresoEgreso").is(":checked") ? 0 : 1,
                                "idUsuario": $("#cbSelectVendedorIngresoEgreso").is(":checked") ? '' : $('#cbVendedorVIngresoEgreso').val(),
                                "transaccion": $("#rbSelectTransaccionesIngresoEgreso").is(":checked") ? 1 : 0,
                                "movimientos": $("#rbSelectMovimientoIngresoEgreso").is(":checked") ? 1 : 0,
                            });
                            window.open("../app/sunat/pdfingresosegresos.php?" + params, "_blank");
                        }
                    }
                }
            });

            $("#btnExcelIngresoEgreso").bind("click", function () {
                if ($("#divOverlayIngresoEgreso").hasClass("d-none")) {

                }
            });

            $("#divOverlayIngresoEgreso").addClass("d-none");
        });

        $("#modalIngresoEgreso").on("hide.bs.modal", async function () {
            $("#divOverlayIngresoEgreso").removeClass("d-none");

            $("#btnPdfIngresoEgreso").unbind();
            $("#btnExcelIngresoEgreso").unbind();

            $("#cbSelectVendedorIngresoEgreso").prop("checked", true);
            $("#rbSelectTransaccionesIngresoEgreso").prop("checked", true);
            $("#rbSelectMovimientoIngresoEgreso").prop("checked", true);

            $("#cbVendedorVIngresoEgreso").prop("disabled", true);
        });
    }

    function openModalIngresosEgresos() {
        $("#modalIngresoEgreso").modal("show");
    }

    function selectEmpleado() {
        $('#cbVendedorVIngresoEgreso').empty();
        $('#cbVendedorVIngresoEgreso').select2({
            width: '100%',
            placeholder: "Buscar Empleado",
            ajax: {
                url: "../app/controller/EmpleadoController.php",
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        type: "fillempleado",
                        search: params.term == null ? "" : params.term,
                    };
                },
                processResults: function (response) {
                    let datafill = response.map((item, index) => {
                        return {
                            id: item.IdEmpleado,
                            text: item.NumeroDocumento + ' - ' + item.Informacion
                        };
                    });
                    return {
                        results: datafill
                    };
                },
                cache: true
            }
        });
    }

}