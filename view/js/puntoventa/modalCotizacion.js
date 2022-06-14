function ModalCotizacion() {

    let stateCotizacion = false;
    let paginacionCotizacion = 0;
    let opcionCotizacion = 0;
    let totalPaginacionCotizacion = 0;
    let filasPorPaginaCotizacion = 10;
    let tbListCotizacion = $("#tbListCotizacion");
    let ulPaginationCotizacion = $("#ulPaginationCotizacion");

    this.init = function () {
        $("#txtFechaInicialCotizacion").val(tools.getCurrentDate());
        $("#txtFechaFinalCotizacion").val(tools.getCurrentDate());

        $("#btnCotizacion").click(function () {
            $("#modalCotizacion").modal("show");
        });

        $("#btnCotizacion").keypress(function (event) {
            if (event.keyCode == 13) {
                $("#modalCotizacion").modal("show");
                event.preventDefault();
            }
        });

        $("#modalCotizacion").on('shown.bs.modal', function () {
            $("#txtSearchCotizacion").focus();
            loadInitCotizacion();
        });

        $("#modalCotizacion").on("hide.bs.modal", function () {
            tbListCotizacion.empty();
            tbListCotizacion.append('<tr><td class="text-center" colspan="7"><p>Iniciar la busqueda para cargar los datos.</p></td></tr>');
            $("#txtSearchCotizacion").val('');
            $("#txtFechaInicialCotizacion").val(tools.getCurrentDate());
            $("#txtFechaFinalCotizacion").val(tools.getCurrentDate());
        });

        $("#txtSearchCotizacion").on("keyup", function (event) {
            let value = $("#txtSearchCotizacion").val();
            if (event.keyCode !== 9 && event.keyCode !== 18) {
                if (value.trim().length != 0) {
                    if (!stateCotizacion) {
                        paginacionCotizacion = 1;
                        fillTableCotizacion(1, value.trim(), "", "");
                        opcionCotizacion = 1;
                    }
                }
            }
        });

        $("#btnRecargarCotizacion").click(function (event) {
            if (tools.validateDate($("#txtFechaInicialCotizacion").val()) && tools.validateDate($("#txtFechaFinalCotizacion").val())) {
                if (!stateCotizacion) {
                    paginacionCotizacion = 1;
                    fillTableCotizacion(0, "", $("#txtFechaInicialCotizacion").val(), $("#txtFechaFinalCotizacion").val());
                    opcionCotizacion = 0;
                }
            }
        });

        $("#btnRecargarCotizacion").keypress(function (event) {
            if (event.keyCode == 13) {
                if (tools.validateDate($("#txtFechaInicialCotizacion").val()) && tools.validateDate($("#txtFechaFinalCotizacion").val())) {
                    if (!stateCotizacion) {
                        paginacionCotizacion = 1;
                        fillTableCotizacion(0, "", $("#txtFechaInicialCotizacion").val(), $("#txtFechaFinalCotizacion").val());
                        opcionCotizacion = 0;
                    }
                }
                event.preventDefault();
            }
        });

        $("#txtFechaInicialCotizacion").change(function (event) {
            if (tools.validateDate($("#txtFechaInicialCotizacion").val()) && tools.validateDate($("#txtFechaFinalCotizacion").val())) {
                if (!stateCotizacion) {
                    paginacionCotizacion = 1;
                    fillTableCotizacion(0, "", $("#txtFechaInicialCotizacion").val(), $("#txtFechaFinalCotizacion").val());
                    opcionCotizacion = 0;
                }
            }
        });

        $("#txtFechaFinalCotizacion").change(function (event) {
            if (tools.validateDate($("#txtFechaInicialCotizacion").val()) && tools.validateDate($("#txtFechaFinalCotizacion").val())) {
                if (!stateCotizacion) {
                    paginacionCotizacion = 1;
                    fillTableCotizacion(0, "", $("#txtFechaInicialCotizacion").val(), $("#txtFechaFinalCotizacion").val());
                    opcionCotizacion = 0;
                }
            }
        });

    }

    this.openModalInit = function () {
        $("#modalCotizacion").modal("show");
    }

    function onEventPaginacion() {
        switch (opcionCotizacion) {
            case 0:
                fillTableCotizacion(0, "", $("#txtFechaInicialCotizacion").val(), $("#txtFechaFinalCotizacion").val());
                break;
            case 1:
                fillTableCotizacion(1, $("#txtSearchCotizacion").val(), "", "");
                break;
        }
    }

    function loadInitCotizacion() {
        if (tools.validateDate($("#txtFechaInicialCotizacion").val()) && tools.validateDate($("#txtFechaFinalCotizacion").val())) {
            if (!stateCotizacion) {
                paginacionCotizacion = 1;
                fillTableCotizacion(0, "", $("#txtFechaInicialCotizacion").val(), $("#txtFechaFinalCotizacion").val());
                opcionCotizacion = 0;
            }
        }
    }

    async function fillTableCotizacion(opcion, buscar, fechaInicial, fechaFinal) {
        try {
            let result = await tools.promiseFetchGet("../app/controller/CotizacionController.php", {
                "type": "all",
                "opcion": opcion,
                "buscar": buscar,
                "fechaInicial": fechaInicial,
                "fechaFinal": fechaFinal,
                "posicionPagina": ((paginacionCotizacion - 1) * filasPorPaginaCotizacion),
                "filasPorPagina": filasPorPaginaCotizacion
            }, function () {
                tools.loadTable(tbListCotizacion, 7);
                stateCotizacion = true;
                totalPaginacionCotizacion = 0;
            });

            tbListCotizacion.empty();
            if (result.data.length == 0) {
                tools.loadTableMessage(tbListCotizacion, "No hay datos para mostrar.", 7);
                tools.paginationEmpty(ulPaginationCotizacion);
                stateCotizacion = false;
            } else {
                for (let value of result.data) {
                    tbListCotizacion.append(`<tr>
                    <td>${value.Id}</td>
                    <td>${value.Apellidos + '<br>' + value.Nombres}</td>
                    <td>${'COTIZACIÓN <br>N° - ' + tools.formatNumber(value.IdCotizacion)}</td>
                    <td>${tools.getDateForma(value.FechaCotizacion) + '<br>' + tools.getTimeForma24(value.HoraCotizacion)}</td>
                    <td>${value.NumeroDocumento + '<br>' + value.Informacion}</td>
                    <td>${value.SimboloMoneda + ' ' + tools.formatMoney(value.Total)}</td>
                    <td class="text-center"><button class="btn btn-danger" onclick="loadAddCotizacion('${value.IdCotizacion}')"><image src="./images/accept.png" width="22" height="22" /></button></td>
                    </tr>
                    `);
                }

                totalPaginacionCotizacion = parseInt(Math.ceil((parseFloat(result.total) / filasPorPaginaCotizacion)));

                let i = 1;
                let range = [];
                while (i <= totalPaginacionCotizacion) {
                    range.push(i);
                    i++;
                }

                let min = Math.min.apply(null, range);
                let max = Math.max.apply(null, range);

                let paginacionHtml = `
                    <button class="btn btn-outline-secondary" onclick="onEventPaginacionInicioCt(${min})">
                        <i class="fa fa-angle-double-left"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="onEventAnteriorPaginacionCt()">
                        <i class="fa fa-angle-left"></i>
                    </button>
                    <span class="btn btn-outline-secondary disabled">${paginacionCotizacion} - ${totalPaginacionCotizacion}</span>
                    <button class="btn btn-outline-secondary" onclick="onEventSiguientePaginacionCt()">
                        <i class="fa fa-angle-right"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="onEventPaginacionFinalCt(${max})">
                        <i class="fa fa-angle-double-right"></i>
                    </button>`;

                ulPaginationCotizacion.html(paginacionHtml);
                stateCotizacion = false;
            }
        } catch (error) {
            tools.loadTableMessage(tbListCotizacion, tools.messageError(error), 7, true);
            tools.paginationEmpty(ulPaginationCotizacion);
            stateCotizacion = false;
        }
    }

    loadAddCotizacion = async function (idCotizacion) {
        try {
            let result = await tools.promiseFetchGet("../app/controller/CotizacionController.php", {
                "type": "cotizacionventa",
                "idCotizacion": idCotizacion
            }, function () {
                listaProductos = [];
                $("#modalCotizacion").modal("hide");
            });

            let venta = result[0];
            // $("#cbComprobante").val(venta.IdComprobante);
            $("#cbTipoDocumento").val(venta.TipoDocumento);
            $("#cbMoneda").val(venta.IdMoneda);
            $("#txtNumero").val(venta.NumeroDocumento);
            $("#txtCliente").val(venta.Informacion);
            $("#txtCelular").val(venta.Celular);
            $("#txtEmail").val(venta.Email);
            $("#txtDireccion").val(venta.Direccion);

            let detalle = result[1];
            for (let value of detalle) {
                let cantidad = parseFloat(value.Cantidad);
                let precio = parseFloat(value.Precio);

                listaProductos.push({
                    "idSuministro": value.IdSuministro,
                    "clave": value.Clave,
                    "nombreMarca": value.NombreMarca,
                    "cantidad": cantidad,
                    "costoCompra": parseFloat(value.PrecioCompra),
                    "bonificacion": 0,
                    "unidadCompra": value.IdMedida,
                    "unidadCompraName": value.UnidadCompraName,

                    "descuento": 0,
                    "descuentoCalculado": 0,
                    "descuentoSumado": 0,

                    "precioVentaGeneral": precio,
                    "precioVentaGeneralUnico": precio,
                    "precioVentaGeneralReal": precio,

                    "impuestoOperacion": value.Operacion,
                    "idImpuesto": value.Impuesto,
                    "impuestoNombre": value.ImpuestoNombre,
                    "impuestoValor": parseFloat(value.Valor),

                    "inventario": value.Inventario,
                    "unidadVenta": value.UnidadVenta,
                    "valorInventario": value.ValorInventario
                });

                renderTableProductos();
            }
        } catch (error) {

        }
    }

    onEventPaginacionInicioCt = function (value) {
        if (!stateCotizacion) {
            if (value !== paginacionCotizacion) {
                paginacionCotizacion = value;
                onEventPaginacion();
            }
        }
    }

    onEventPaginacionFinalCt = function (value) {
        if (!stateCotizacion) {
            if (value !== paginacionCotizacion) {
                paginacionCotizacion = value;
                onEventPaginacion();
            }
        }
    }

    onEventAnteriorPaginacionCt = function () {
        if (!stateCotizacion) {
            if (paginacionCotizacion > 1) {
                paginacionCotizacion--;
                onEventPaginacion();
            }
        }
    }

    onEventSiguientePaginacionCt = function () {
        if (!stateCotizacion) {
            if (paginacionCotizacion < totalPaginacionCotizacion) {
                paginacionCotizacion++;
                onEventPaginacion();
            }
        }
    }

}