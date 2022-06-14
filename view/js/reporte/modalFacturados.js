function ModalFacturados() {

    this.init = function () {
        $("#btnFacturados").click(function () {
            openModalFacturado();
        });

        $("#btnFacturados").keypress(function (event) {
            if (event.keyCode === 13) {
                openModalFacturado();
            }
            event.preventDefault();
        });

        loadInitFacturado();
    }

    function loadInitFacturado() {
        $("#modaModalFacturado").on("shown.bs.modal", async function () {
            $("#txtFIFacturado").val(tools.getFirstDayOfMoth());
            $("#txtFFFacturado").val(tools.getLastDayOfMonth());

            $("#txtFIFacturado").focus();

            $("#btnPdfFacturado").bind("click", function () {
                let fechaInicial = $("#txtFIFacturado").val();
                let fechaFinal = $("#txtFFFacturado").val();
                if (tools.validateDate(fechaInicial) && tools.validateDate(fechaFinal)) {
                    let params = new URLSearchParams({
                        "txtFechaInicial": fechaInicial,
                        "txtFechaFinal": fechaFinal
                    });
                    window.open("../app/sunat/pdffacturado.php?" + params, "_blank");
                }
            });

            $("#btnExcelFacturado").bind("click", function () {
                let fechaInicial = $("#txtFIFacturado").val();
                let fechaFinal = $("#txtFFFacturado").val();
                if (tools.validateDate(fechaInicial) && tools.validateDate(fechaFinal)) {
                    let params = new URLSearchParams({
                        "txtFechaInicial": fechaInicial,
                        "txtFechaFinal": fechaFinal
                    });
                    window.open("../app/sunat/excelfacturado.php?" + params, "_blank");
                }
            });
        });

        $("#modaModalFacturado").on("hide.bs.modal", async function () {
            $("#btnExcelFacturado").unbind();
            $("#btnPdfFacturado").unbind();
        });
    }

    function openModalFacturado() {
        $("#modaModalFacturado").modal("show");
    }

}