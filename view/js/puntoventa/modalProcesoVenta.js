function ModalProcesoVenta() {


    this.init = function () {

        $("#btnCobrar").click(function () {
            modalVenta();
        });

        $("#btnCobrar").keypress(function (event) {
            if (event.keyCode === 13) {
                modalVenta();
                event.preventDefault();
            }
        });

        $('#modalProcesoVenta').on('shown.bs.modal', function () {
            $('#txtEfectivo').focus();
        });

        $('#modalProcesoVenta').on('hide.bs.modal', function () {
            clearModalProcesoVenta();
        });

        $("#btnContado").click(function () {
            $("#btnContado").removeClass("btn-secondary");
            $("#btnContado").addClass("btn-primary");

            $("#btnCredito").removeClass("btn-primary");
            $("#btnCredito").addClass("btn-secondary");

            $("#btnAdelantado").removeClass("btn-primary")
            $("#btnAdelantado").addClass("btn-secondary")

            $("#boxContado").removeClass("d-none");
            $("#boxCredito").addClass("d-none");
            $("#boxAdelantado").addClass("d-none");

            if ($("#cbDeposito").is(":checked")) {
                $("#txtNumOperacion").focus();
            } else {
                $("#txtEfectivo").focus();
            }

            console.log();
            state_view_pago = 0;
        });

        $("#btnCredito").click(function () {
            $("#btnCredito").removeClass("btn-secondary");
            $("#btnCredito").addClass("btn-primary");

            $("#btnContado").removeClass("btn-primary")
            $("#btnContado").addClass("btn-secondary")

            $("#btnAdelantado").removeClass("btn-primary")
            $("#btnAdelantado").addClass("btn-secondary")

            $("#boxContado").addClass("d-none");
            $("#boxCredito").removeClass("d-none");
            $("#boxAdelantado").addClass("d-none");

            $("#txtFechaVencimiento").focus();
            state_view_pago = 1;
        });

        $("#btnAdelantado").click(function () {
            $("#btnAdelantado").removeClass("btn-secondary");
            $("#btnAdelantado").addClass("btn-primary");

            $("#btnContado").removeClass("btn-primary");
            $("#btnContado").addClass("btn-secondary");

            $("#btnCredito").removeClass("btn-primary");
            $("#btnCredito").addClass("btn-secondary");

            $("#boxContado").addClass("d-none");
            $("#boxCredito").addClass("d-none");
            $("#boxAdelantado").removeClass("d-none");

            if ($("#cbDeposito").is(":checked")) {
                $("#txtNumOperacionAdelantado").focus();
            } else {
                $("#txtEfectivoAdelanto").focus();
            }

            state_view_pago = 2;
        });

        $("#btnCompletarVenta").click(function () {
            crudVenta();
        });

        $("#btnCompletarVenta").keypress(function (event) {
            if (event.keyCode == 13) {
                crudVenta();
                event.preventDefault();
            }
        });

        efectivoEventos();
        depositoEventos();
        adelantadoEventos();
    }

    function efectivoEventos() {
        $("#divEfectivo").css("display", "block");
        $("#divEfectivoDeposito").css("display", "none");

        $("#txtEfectivo").keyup(function (event) {
            if ($("#txtEfectivo").val() == "") {
                vueltoContado = total_venta;
                TotalAPagarContado();
                return;
            }
            if (tools.isNumeric($("#txtEfectivo").val())) {
                TotalAPagarContado();
            }
        });

        $("#txtEfectivo").keydown(function (event) {
            if (event.keyCode == 13) {
                crudVenta();
                event.preventDefault();
            }
        });

        $("#txtEfectivo").keypress(function (event) {
            var key = window.Event ? event.which : event.keyCode;
            var c = String.fromCharCode(key);
            if ((c < '0' || c > '9') && (c != '\b') && (c != '.')) {
                event.preventDefault();
            }
            if (c == '.' && $("#txtEfectivo").val().includes(".")) {
                event.preventDefault();
            }
        });

        $("#txtTarjeta").keyup(function (event) {
            if ($("#txtTarjeta").val() == "") {
                vueltoContado = total_venta;
                TotalAPagarContado();
                return;
            }
            if (tools.isNumeric($("#txtTarjeta").val())) {
                TotalAPagarContado();
            }
        });

        $("#txtTarjeta").keydown(function (event) {
            if (event.keyCode == 13) {
                crudVenta();
                event.preventDefault();
            }
        });

        $("#txtTarjeta").keypress(function (event) {
            var key = window.Event ? event.which : event.keyCode;
            var c = String.fromCharCode(key);
            if ((c < '0' || c > '9') && (c != '\b') && (c != '.')) {
                event.preventDefault();
            }
            if (c == '.' && $("#txtTarjeta").val().includes(".")) {
                event.preventDefault();
            }
        });

        $("#cbDeposito").change(function () {
            if (!$(this).is(":checked")) {
                $("#divEfectivo").css("display", "block");
                $("#divEfectivoDeposito").css("display", "none");
                $("#txtEfectivo").focus();
            } else {
                $("#divEfectivo").css("display", "none");
                $("#divEfectivoDeposito").css("display", "block");
                $("#txtNumOperacion").focus();
            }
        });
    }

    function depositoEventos() {

    }

    function adelantadoEventos() {
        $("#divAdelantado").css("display", "block");
        $("#divAdelantadoDeposito").css("display", "none");

        $("#txtEfectivoAdelanto").keyup(function (event) {
            if ($("#txtEfectivoAdelanto").val() == "") {
                vueltoAdelantado = total_venta;
                TotalAPagarAdelantado();
                return;
            }
            if (tools.isNumeric($("#txtEfectivoAdelanto").val())) {
                TotalAPagarAdelantado();
            }
        });

        $("#txtEfectivoAdelanto").keydown(function (event) {
            if (event.keyCode == 13) {
                crudVenta();
                event.preventDefault();
            }
        });

        $("#txtEfectivoAdelanto").keypress(function (event) {
            var key = window.Event ? event.which : event.keyCode;
            var c = String.fromCharCode(key);
            if ((c < '0' || c > '9') && (c != '\b') && (c != '.')) {
                event.preventDefault();
            }
            if (c == '.' && $("#txtEfectivoAdelanto").val().includes(".")) {
                event.preventDefault();
            }
        });

        $("#txtTarjetaAdelanto").keyup(function (event) {
            if ($("#txtTarjetaAdelanto").val() == "") {
                vueltoAdelantado = total_venta;
                TotalAPagarAdelantado();
                return;
            }
            if (tools.isNumeric($("#txtTarjetaAdelanto").val())) {
                TotalAPagarAdelantado();
            }
        });

        $("#txtTarjetaAdelanto").keydown(function (event) {
            if (event.keyCode == 13) {
                crudVenta();
                event.preventDefault();
            }
        });

        $("#txtTarjetaAdelanto").keypress(function (event) {
            var key = window.Event ? event.which : event.keyCode;
            var c = String.fromCharCode(key);
            if ((c < '0' || c > '9') && (c != '\b') && (c != '.')) {
                event.preventDefault();
            }
            if (c == '.' && $("#txtTarjetaAdelanto").val().includes(".")) {
                event.preventDefault();
            }
        });

        $("#cbDepositoAdelantado").change(function () {
            if (!$(this).is(":checked")) {
                $("#divAdelantado").css("display", "block");
                $("#divAdelantadoDeposito").css("display", "none");
                $("#txtEfectivoAdelanto").focus();
            } else {
                $("#divAdelantado").css("display", "none");
                $("#divAdelantadoDeposito").css("display", "block");
                $("#txtNumOperacionAdelantado").focus();
            }
        });
    }

    this.resetProcesoVenta = function () {
        $("#modalProcesoVenta").modal("hide");
        clearModalProcesoVenta();
    }

    function clearModalProcesoVenta() {
        $("#btnContado").removeClass("btn-secondary");
        $("#btnContado").addClass("btn-primary");

        $("#btnCredito").removeClass("btn-primary");
        $("#btnCredito").addClass("btn-secondary");

        $("#btnAdelantado").removeClass("btn-primary")
        $("#btnAdelantado").addClass("btn-secondary")

        $("#boxContado").removeClass("d-none");
        $("#boxCredito").addClass("d-none");
        $("#boxAdelantado").addClass("d-none");

        $("#txtEfectivo").val('');
        $("#txtTarjeta").val('');
        $("#txtDeposito").val('');
        $("#txtNumOperacion").val('');

        $("#txtEfectivoAdelanto").val('');
        $("#txtTarjetaAdelanto").val('');
        $("#txtDepositoAdelantado").val('');
        $("#txtNumOperacionAdelantado").val('');

        $("#lblVueltoNombre").html('Su cambio:');
        $("#lblVuelto").html(monedaSimbolo + ' 0.00');

        $("#lblVueltoAdelantoNombre").html('Su cambio:');
        $("#lblVueltoAdelanto").html(monedaSimbolo + ' 0.00');

        $("#txtFechaVencimiento").val(null);

        $("#cbDeposito").prop('checked', false);
        $("#divEfectivo").css("display", "block");
        $("#divEfectivoDeposito").css("display", "none");

        $("#cbDepositoAdelantado").prop('checked', false);
        $("#divAdelantado").css("display", "block");
        $("#divAdelantadoDeposito").css("display", "none");

        state_view_pago = 0;

        vueltoContado = 0;
        estadoCobroContado = false;

        vueltoAdelantado = 0;
        estadoCobroAdelantado = false;

        total_venta = 0;
    }

    function TotalAPagarContado() {
        if ($("#txtEfectivo").val() == '' && $("#txtTarjeta").val() == '') {
            $("#lblVuelto").html(monedaSimbolo + " 0.00");
            $("#lblVueltoNombre").html("POR PAGAR: ");
            estadoCobroContado = false;
        } else if ($("#txtEfectivo").val() == '') {
            if (parseFloat($("#txtTarjeta").val()) >= total_venta) {
                vueltoContado = parseFloat($("#txtTarjeta").val()) - total_venta;
                $("#lblVueltoNombre").html("SU CAMBIO ES: ");
                estadoCobroContado = true;
            } else {
                vueltoContado = total_venta - parseFloat($("#txtTarjeta").val());
                $("#lblVueltoNombre").html("POR PAGAR: ");
                estadoCobroContado = false;
            }
        } else if ($("#txtTarjeta").val() == '') {
            if (parseFloat($("#txtEfectivo").val()) >= total_venta) {
                vueltoContado = parseFloat($("#txtEfectivo").val()) - total_venta;
                $("#lblVueltoNombre").html("SU CAMBIO ES: ");
                estadoCobroContado = true;
            } else {
                vueltoContado = total_venta - parseFloat($("#txtEfectivo").val());
                $("#lblVueltoNombre").html("POR PAGAR: ");
                estadoCobroContado = false;
            }
        } else {
            let suma = (parseFloat($("#txtEfectivo").val())) + (parseFloat($("#txtTarjeta").val()));
            if (suma >= total_venta) {
                vueltoContado = suma - total_venta;
                $("#lblVueltoNombre").html("SU CAMBIO ES: ");
                estadoCobroContado = true;
            } else {
                vueltoContado = total_venta - suma;
                $("#lblVueltoNombre").html("POR PAGAR: ");
                estadoCobroContado = false;
            }
        }

        $("#lblVuelto").html(monedaSimbolo + " " + tools.formatMoney(vueltoContado, 2));
    }

    function TotalAPagarAdelantado() {
        if ($("#txtEfectivoAdelanto").val() == '' && $("#txtTarjetaAdelanto").val() == '') {
            $("#lblVueltoAdelanto").html(monedaSimbolo + " 0.00");
            $("#lblVueltoAdelantoNombre").html("POR PAGAR: ");
            estadoCobroAdelantado = false;
        } else if ($("#txtEfectivoAdelanto").val() == '') {
            if (parseFloat($("#txtTarjetaAdelanto").val()) >= total_venta) {
                vueltoAdelantado = parseFloat($("#txtTarjetaAdelanto").val()) - total_venta;
                $("#lblVueltoAdelantoNombre").html("SU CAMBIO ES: ");
                estadoCobroAdelantado = true;
            } else {
                vueltoAdelantado = total_venta - parseFloat($("#txtTarjetaAdelanto").val());
                $("#lblVueltoAdelantoNombre").html("POR PAGAR: ");
                estadoCobroAdelantado = false;
            }
        } else if ($("#txtTarjetaAdelanto").val() == '') {
            if (parseFloat($("#txtEfectivoAdelanto").val()) >= total_venta) {
                vueltoAdelantado = parseFloat($("#txtEfectivoAdelanto").val()) - total_venta;
                $("#lblVueltoAdelantoNombre").html("SU CAMBIO ES: ");
                estadoCobroAdelantado = true;
            } else {
                vueltoAdelantado = total_venta - parseFloat($("#txtEfectivoAdelanto").val());
                $("#lblVueltoAdelantoNombre").html("POR PAGAR: ");
                estadoCobroAdelantado = false;
            }
        } else {
            let suma = (parseFloat($("#txtEfectivoAdelanto").val())) + (parseFloat($("#txtTarjetaAdelanto").val()));
            if (suma >= total_venta) {
                vueltoAdelantado = suma - total_venta;
                $("#lblVueltoAdelantoNombre").html("SU CAMBIO ES: ");
                estadoCobroAdelantado = true;
            } else {
                vueltoAdelantado = total_venta - suma;
                $("#lblVueltoAdelantoNombre").html("POR PAGAR: ");
                estadoCobroAdelantado = false;
            }
        }

        $("#lblVueltoAdelanto").html(monedaSimbolo + " " + tools.formatMoney(vueltoAdelantado, 2));

    }

}