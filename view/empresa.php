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
                <h1>Configurar mi Empresa
                </h1>
            </div>
        </div>

        <div class="tile mb-4">

            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-text"> R.U.C: <i class="fa fa-fw fa-asterisk text-danger"></i></label>
                            <div class="form-group">
                                <input id="txtNumDocumento" class="form-control" type="text" placeholder="R.U.C.">
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
        let idEmpresa = 'SD0001';
        let txtNumDocumento = $("#txtNumDocumento");
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

            // LoadDataEmpresa();
        });

        async function LoadDataEmpresa() {
            try {

                let result = await tools.promiseFetchGet("../app/controller/EmpresaController.php", {
                    "type": "getempresa"
                }, function() {
                    tools.AlertInfo("Mi Empresa", "Cargando información.", "toast-bottom-right");
                    $("#divOverlayEmpresa").removeClass("d-none");
                });

                let empresa = result;
                idEmpresa = empresa.IdEmpresa;
                txtNumDocumento.val(empresa.NumeroDocumento);
                txtRazonSocial.val(empresa.RazonSocial);
                txtNomComercial.val(empresa.NombreComercial);
                if (empresa.Image == "") {
                    lblImagen.attr("src", "./images/noimage.jpg");
                } else {
                    lblImagen.attr("src", "data:image/png;base64," + empresa.Image);
                }
                txtDireccion.val(empresa.Domicilio);
                txtTelefono.val(empresa.Telefono);
                txtCelular.val(empresa.Celular);
                txtPaginWeb.val(empresa.PaginaWeb);
                txtEmail.val(empresa.Email);
                txtTerminos.val(empresa.Terminos);
                txtCodiciones.val(empresa.Condiciones);
                txtUsuarioSol.val(empresa.UsuarioSol);
                txtClaveSol.val(empresa.ClaveSol);
                lblNameCertificado.html(empresa.CertificadoRuta);
                txtClaveCertificado.val(empresa.CertificadoClave);

                var data = [{
                        id: 0,
                        text: '- Seleccione -'
                    },
                    {
                        id: result.IdUbigeo,
                        text: result.Departamento + ' - ' + result.Provincia + ' - ' + result.Distrito + '(' + result.Ubigeo + ')'
                    }
                ];

                cbUbigeo.select2({
                    width: '100%',
                    placeholder: "Buscar Ubigeo",
                    data: data,
                    ajax: {
                        url: "../app/controller/EmpresaController.php",
                        type: "GET",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                type: "fillubigeo",
                                search: params.term
                            };
                        },
                        processResults: function(response) {
                            let datafill = response.map((item, index) => {
                                return {
                                    id: item.IdUbigeo,
                                    text: item.Departamento + ' - ' + item.Provincia + ' - ' + item.Distrito + '(' + item.Ubigeo + ')'
                                };
                            });
                            return {
                                results: datafill
                            };
                        },
                        cache: true
                    }
                });

                if (result.IdUbigeo != 0) {
                    cbUbigeo.val(result.IdUbigeo).trigger('change.select2');
                }

                $("#divOverlayEmpresa").addClass("d-none");
            } catch (error) {
                $("#lblTextOverlayEmpresa").html("Error en :" + error.responseText);
            }
        }

        function crudEmpresa() {
            var formData = new FormData();
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
                            tools.ModalAlertError("Mi Empresa", "Se produjo un error: " + error.responseText);
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>