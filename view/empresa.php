<!DOCTYPE html>
<html lang="es">

<head>
    <?php include './layout/head.php'; ?>
</head>

<body class="app sidebar-mini">
    <!-- Navbar-->
    <?php include "./layout/header.php"; ?>
    <!-- Sidebar menu-->
    <?php include "./layout/menu.php"; ?>
    <main class="app-content">

        <div class="app-title">
            <div>
                <h1>Registra su Certificado Dígital de la Sunat
                </h1>
            </div>
        </div>

        <div class="tile mb-4">

            <div class="overlay d-none" id="divOverlayEmpresa">
                <div class="m-loader mr-4">
                    <svg class="m-circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle>
                    </svg>
                </div>
                <h4 class="l-text text-white" id="lblTextOverlayEmpresa">Cargando información...</h4>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-text"> R.U.C:</label>
                            <div class="form-group">
                                <input id="txtNumDocumento" disabled class="form-control" type="text" placeholder="R.U.C.">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-text">Razón Social:</label>
                            <div class="form-group">
                                <input id="txtRazonSocial" disabled class="form-control" type="text" placeholder="Razón Social">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-text"> Seleccionar Archivo:</label>
                    <div class="form-group d-flex">
                        <input type="file" class="form-control d-none" id="fileCertificado">
                        <div class="input-group">
                            <label class="form-control" for="fileCertificado" id="lblNameCertificado">Seleccionar archivo</label>
                            <div class="input-group-append">
                                <label for="fileCertificado" class="btn btn-info" type="button" id="btnReloadCliente">Subir</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-text"> Contraseña de tu Certificado:</label>
                    <div class="form-group">
                        <input id="txtClaveCertificado" class="form-control" type="password" placeholder="Contraseña de tu Certificado" />
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-text text-left text-danger">Todos los campos marcados con <i class="fa fa-fw fa-asterisk text-danger"></i> son obligatorios</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group text-right">
                        <button class="btn btn-success" type="button" id="btnGuardar"><i class="fa fa-save"></i>
                            Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <?php include "./layout/footer.php"; ?>
    <script src="./js/notificaciones.js"></script>
    <script>
        let tools = new Tools();
        let idEmpresa = "";
        let txtNumDocumento = $("#txtNumDocumento");
        let txtRazonSocial = $("#txtRazonSocial");
        let lblNameCertificado = $("#lblNameCertificado");
        let fileCertificado = $("#fileCertificado");
        let txtClaveCertificado = $("#txtClaveCertificado");
        $(document).ready(function() {
            $("#fileCertificado").on('change', function(event) {
                if (event.target.files.length > 0) {
                    lblNameCertificado.empty();
                    lblNameCertificado.html(event.target.files[0].name);
                }
            });

            $("#btnGuardar").keypress(function(event) {
                if (event.keyCode == 13) {
                    crudEmpresa();
                }
                event.preventDefault();
            });

            $("#btnGuardar").click(function() {
                crudEmpresa();
            });

            LoadDataEmpresa();
        });

        async function LoadDataEmpresa() {
            try {

                let result = await tools.promiseFetchGet("../app/controller/EmpresaController.php", {
                    "type": "getempresa"
                }, function() {
                    tools.AlertInfo("Mi Empresa", "Cargando información.", "toast-bottom-right");
                    $("#divOverlayEmpresa").removeClass("d-none");
                });

                idEmpresa = result.idEmpresa;
                txtNumDocumento.val(result.documento);
                txtRazonSocial.val(result.razonSocial);

                $("#divOverlayEmpresa").addClass("d-none");
            } catch (error) {
                $("#lblTextOverlayEmpresa").html("Error en :" + error.responseText);
            }
        }

        function crudEmpresa() {
            var formData = new FormData();
            formData.append("idEmpresa", idEmpresa);
            formData.append("txtNumDocumento", txtNumDocumento.val());
            formData.append("certificadoUrl", lblNameCertificado.html());
            formData.append("certificadoType", fileCertificado[0].files.length);
            formData.append("certificado", fileCertificado[0].files[0]);
            formData.append("txtClaveCertificado", txtClaveCertificado.val());

            tools.ModalDialog("Mi Empresa", "¿Está seguro de continuar?", function(value) {
                if (value == true) {
                    $.ajax({
                        url: "../app/controller/EmpresaController.php",
                        method: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            tools.ModalAlertInfo("Mi Empresa", "Procesando petición..");
                        },
                        success: function(result) {
                            tools.ModalAlertSuccess("Mi Empresa", result.message);
                        },
                        error: function(error) {
                            if (error) {
                                tools.ModalAlertWarning("Mi Empresa", error.responseJSON);
                            } else {
                                tools.ModalAlertError("Mi Empresa", "Se produjo un error interno, intente nuevamente.");
                            }
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>