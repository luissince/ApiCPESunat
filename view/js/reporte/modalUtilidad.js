function ModalUtilidad() {

    this.init = function () {

        $("#btnUtilidad").click(function () {
            openModaUtilidad();
        });

        tools.keyEnter($("#btnUtilidad"), function (event) {
            openModaUtilidad();
        });

        $("#rbSelectProductoUtilidad").change(function () {
            $("#cbProductoUtilidad").prop("disabled", $("#rbSelectProductoUtilidad").is(":checked"));
        });

        $("#rbSelectCategoriaUtilidad").change(function () {
            $("#cbCategoriaUtilidad").prop("disabled", $("#rbSelectCategoriaUtilidad").is(":checked"));
        });

        $("#rbSelectMarcaUtilidad").change(function () {
            $("#cbMarcaUtilidad").prop("disabled", $("#rbSelectMarcaUtilidad").is(":checked"));
        });

        $("#rbSelectPresentacionUtilidad").change(function () {
            $("#cbPresentacionUtilidad").prop("disabled", $("#rbSelectPresentacionUtilidad").is(":checked"));
        });

        loadInitUtilidad();
    }

    function loadInitUtilidad() {
        $("#modalUtilidad").on("shown.bs.modal", async function () {
            try {
                let promiseFetchMarca = await tools.promiseFetchGet("../app/controller/DetalleController.php", {
                    "type": "detailid",
                    "value": "0007"
                });
                $("#cbMarcaUtilidad").append('<option value="">- Seleccione -</option>');
                for (let value of promiseFetchMarca) {
                    $("#cbMarcaUtilidad").append('<option value="' + value.IdDetalle + '">' + value.Nombre + '</option>');
                }

                let promiseFetchCategoria = await tools.promiseFetchGet("../app/controller/DetalleController.php", {
                    "type": "detailid",
                    "value": "0006"
                });
                $("#cbCategoriaUtilidad").append('<option value="">- Seleccione -</option>');
                for (let value of promiseFetchCategoria) {
                    $("#cbCategoriaUtilidad").append('<option value="' + value.IdDetalle + '">' + value.Nombre + '</option>');
                }

                let promiseFetchPresentacion = await tools.promiseFetchGet("../app/controller/DetalleController.php", {
                    "type": "detailid",
                    "value": "0008"
                });
                $("#cbPresentacionUtilidad").append('<option value="">- Seleccione -</option>');
                for (let value of promiseFetchPresentacion) {
                    $("#cbPresentacionUtilidad").append('<option value="' + value.IdDetalle + '">' + value.Nombre + '</option>');
                }

                $("#divOverlayUtilidad").addClass("d-none");
            } catch (error) {
                $("#lblTextOverlayUtilidad").html(tools.messageError(error));
            }

            $("#txtFechaInicioUtilidad").val(tools.getCurrentDate());
            $("#txtFechaFinalUtilidad").val(tools.getCurrentDate());

            $("#txtFechaInicioUtilidad").focus();
            selectSuministro();

            $("#btnPdfUtilidad").bind("click", function () {
                if ($("#divOverlayUtilidad").hasClass("d-none")) {
                    if (!$("#rbSelectProductoUtilidad").is(":checked") && $('#cbProductoUtilidad').val() == null) {
                        tools.AlertWarning("", "Seleccione un producto.");
                        $('#cbProductoUtilidad').focus();
                    } else if (!$("#rbSelectCategoriaUtilidad").is(":checked") && tools.validateComboBox($('#cbCategoriaUtilidad'))) {
                        tools.AlertWarning("", "Seleccione un producto.");
                        $('#cbCategoriaUtilidad').focus();
                    } else if (!$("#rbSelectMarcaUtilidad").is(":checked") && tools.validateComboBox($('#cbMarcaUtilidad'))) {
                        tools.AlertWarning("", "Seleccione un producto.");
                        $('#cbMarcaUtilidad').focus();
                    } else if (!$("#rbSelectPresentacionUtilidad").is(":checked") && tools.validateComboBox($('#cbPresentacionUtilidad'))) {
                        tools.AlertWarning("", "Seleccione un producto.");
                        $('#cbPresentacionUtilidad').focus();
                    }
                    else {
                        let params = new URLSearchParams({
                            "fechaInicial": $("#txtFechaInicioUtilidad").val(),
                            "fechaFinal": $("#txtFechaFinalUtilidad").val(),

                            "idSuministro": $("#rbSelectProductoUtilidad").is(":checked") ? "" : $('#cbProductoUtilidad').val(),
                            "nameProducto": $("#rbSelectProductoUtilidad").is(":checked") ? "TODOS" : $('#cbProductoUtilidad option:selected').html(),

                            "idCategoria": $("#rbSelectCategoriaUtilidad").is(":checked") ? 0 : $("#cbCategoriaUtilidad").val(),
                            "nameCategoria": $("#rbSelectCategoriaUtilidad").is(":checked") ? "TODOS" : $('#cbCategoriaUtilidad option:selected').html(),

                            "idMarca": $("#rbSelectMarcaUtilidad").is(":checked") ? 0 : $("#cbMarcaUtilidad").val(),
                            "nameMarca": $("#rbSelectMarcaUtilidad").is(":checked") ? "TODOS" : $('#cbMarcaUtilidad option:selected').html(),

                            "idPresentacion": $("#rbSelectPresentacionUtilidad").is(":checked") ? 0 : $("#cbPresentacionUtilidad").val(),
                            "namePresentacion": $("#rbSelectPresentacionUtilidad").is(":checked") ? "TODOS" : $('#cbPresentacionUtilidad option:selected').html(),

                            "mostrarTodo": $("#rbSelectMostrarTodoUtilidad").is(":checked") ? 1 : 0
                        });
                        window.open("../app/sunat/pdfutilidadA4.php?" + params, "_blank");
                    }
                }
            });

            $("#btnExcelUtilidad").bind("click", function () {
                if ($("#divOverlayUtilidad").hasClass("d-none")) {

                }
            });
        });

        $("#modalUtilidad").on("hide.bs.modal", async function () {
            $("#divOverlayUtilidad").removeClass("d-none");
            $("#lblTextOverlayUtilidad").html("Cargando información...");

            $("#btnPdfUtilidad").unbind();
            $("#btnExcelUtilidad").unbind();

            $("#rbSelectProductoUtilidad").prop("checked", true);
            $("#rbSelectCategoriaUtilidad").prop("checked", true);
            $("#rbSelectMarcaUtilidad").prop("checked", true);
            $("#rbSelectPresentacionUtilidad").prop("checked", true);
            $("#rbSelectMostrarTodoUtilidad").prop("checked", true);

            $("#cbProductoUtilidad").prop("disabled", true);
            $("#cbCategoriaUtilidad").prop("disabled", true);
            $("#cbMarcaUtilidad").prop("disabled", true);
            $("#cbPresentacionUtilidad").prop("disabled", true);

            $("#cbProductoUtilidad").empty();
            $("#cbCategoriaUtilidad").empty();
            $("#cbMarcaUtilidad").empty();
            $("#cbPresentacionUtilidad").empty();
        });
    }

    function openModaUtilidad() {
        $("#modalUtilidad").modal("show");
    }

    function selectSuministro() {
        $('#cbProductoUtilidad').empty();
        $('#cbProductoUtilidad').select2({
            width: '100%',
            placeholder: "Buscar Producto",
            ajax: {
                url: "../app/controller/SuministroController.php",
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        type: "fillsuministrosearch",
                        search: params.term == null ? "" : params.term,
                    };
                },
                processResults: function (response) {
                    let datafill = response.map((item, index) => {
                        return {
                            id: item.IdSuministro,
                            text: item.Clave + ' - ' + item.NombreMarca
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