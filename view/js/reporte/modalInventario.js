function ModalInventario() {

    this.init = function () {

        $("#btnInventario").click(function () {
            openModalInventario();
        });

        tools.keyEnter($("#btnInventario"), function (event) {
            openModalInventario();
        });

        $("#rbSelectUnidadInventario").change(function () {
            $("#cbUnidadInventario").prop("disabled", $("#rbSelectUnidadInventario").is(":checked"));
        });

        $("#rbSelectCategoriaInventario").change(function () {
            $("#cbCategoriaInventario").prop("disabled", $("#rbSelectCategoriaInventario").is(":checked"));
        });

        $("#rbSelectMarcaInventario").change(function () {
            $("#cbMarcaInventario").prop("disabled", $("#rbSelectMarcaInventario").is(":checked"));
        });

        $("#rbSelectPresentacionInventario").change(function () {
            $("#cbPresentacionInventario").prop("disabled", $("#rbSelectPresentacionInventario").is(":checked"));
        });

        $("#rbSelectExistenciaInventario").change(function () {
            $("#cbExistenciaInventario").prop("disabled", $("#rbSelectExistenciaInventario").is(":checked"));
        });

        $("#btnPdfInventario").click(function () {
            onEventInventarioPdf();
        });

        tools.keyEnter($("#btnPdfInventario"), function () {
            onEventInventarioPdf();
        });


        $("#btnExcelInventario").click(function () {
            onEventInventarioExcel();
        });

        tools.keyEnter($("#btnExcelInventario"), function () {
            onEventInventarioExcel();
        });

        loadInitInventario();
    }

    function loadInitInventario() {
        $("#modalInventario").on("shown.bs.modal", async function () {
            try {
                let result = await tools.promiseFetchGet("../app/controller/AlmacenController.php", {
                    "type": "almacencombobox"
                });

                $("#cbAlmacenInventario").append('<option value=""> - Seleccione -</option>');
                for (let value of result) {
                    $("#cbAlmacenInventario").append('<option value="' + value.IdAlmacen + '">' + value.Nombre + '</option> ');
                }

                let promiseFetcUnidad = await tools.promiseFetchGet("../app/controller/DetalleController.php", {
                    "type": "detailid",
                    "value": "0013"
                });
                $("#cbUnidadInventario").append('<option value="">- Seleccione -</option>');
                for (let value of promiseFetcUnidad) {
                    $("#cbUnidadInventario").append('<option value="' + value.IdDetalle + '">' + value.Nombre + '</option>');
                }

                let promiseFetcCategoria = await tools.promiseFetchGet("../app/controller/DetalleController.php", {
                    "type": "detailid",
                    "value": "0006"
                });
                $("#cbCategoriaInventario").append('<option value="">- Seleccione -</option>');
                for (let value of promiseFetcCategoria) {
                    $("#cbCategoriaInventario").append('<option value="' + value.IdDetalle + '">' + value.Nombre + '</option>');
                }

                let promiseFetchMarca = await tools.promiseFetchGet("../app/controller/DetalleController.php", {
                    "type": "detailid",
                    "value": "0007"
                });
                $("#cbMarcaInventario").append('<option value="">- Seleccione -</option>');
                for (let value of promiseFetchMarca) {
                    $("#cbMarcaInventario").append('<option value="' + value.IdDetalle + '">' + value.Nombre + '</option>');
                }

                let promiseFetchPresentacion = await tools.promiseFetchGet("../app/controller/DetalleController.php", {
                    "type": "detailid",
                    "value": "0008"
                });
                $("#cbPresentacionInventario").append('<option value="">- Seleccione -</option>');
                for (let value of promiseFetchPresentacion) {
                    $("#cbPresentacionInventario").append('<option value="' + value.IdDetalle + '">' + value.Nombre + '</option>');
                }

                $("#cbExistenciaInventario").append('<option value="">- Seleccione -</option>');
                $("#cbExistenciaInventario").append('<option value="1">NEGATIVOS</option>');
                $("#cbExistenciaInventario").append('<option value="2">INTERMEDIAS</option>');
                $("#cbExistenciaInventario").append('<option value="3">NECESARIAS</option>');
                $("#cbExistenciaInventario").append('<option value="4">EXCEDENTES</option>');

                $("#divOverlayInventario").addClass('d-none');
            } catch (error) {
                $("#lblTextOverlayInventario").html(tools.messageError(error));
            }
        });

        $("#modalInventario").on("hide.bs.modal", async function () {
            $("#divOverlayInventario").removeClass('d-none');
            $("#lblTextOverlayInventario").html("Cargando información...");

            $("#rbSelectUnidadInventario").prop('checked', true);
            $("#rbSelectCategoriaInventario").prop('checked', true);
            $("#rbSelectMarcaInventario").prop('checked', true);
            $("#rbSelectPresentacionInventario").prop('checked', true);
            $("#rbSelectExistenciaInventario").prop('checked', true);


            $("#cbUnidadInventario").prop('disabled', true);
            $("#cbCategoriaInventario").prop('disabled', true);
            $("#cbMarcaInventario").prop('disabled', true);
            $("#cbPresentacionInventario").prop('disabled', true);
            $("#cbExistenciaInventario").prop('disabled', true);

            $("#cbAlmacenInventario").empty();
            $("#cbUnidadInventario").empty();
            $("#cbCategoriaInventario").empty();
            $("#cbMarcaInventario").empty();
            $("#cbPresentacionInventario").empty();
            $("#cbExistenciaInventario").empty();
        });
    }

    function openModalInventario() {
        $("#modalInventario").modal("show");
    }

    function onEventInventarioPdf() {

    }

    function onEventInventarioExcel() {

    }
}