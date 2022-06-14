function ModalVentaEchas() {

    let stateVentasEchas = false;
    let paginacionVentasEchas = 0;
    let opcionVentasEchas = 0;
    let totalPaginacionVentasEchas = 0;
    let filasPorPaginaVentasEchas = 10;
    let tbListVentasEchas = $("#tbListVentasEchas");
    let ulPaginationVentasEchas = $("#ulPaginationVentasEchas");

    this.init = function () {
        $("#btnVentas").click(function () {
            $("#modalVentasEchas").modal("show");
        });

        $("#btnVentas").keypress(function (event) {
            if (event.keyCode == 13) {
                $("#modalVentasEchas").modal("show");
                event.preventDefault();
            }
        });

        $("#modalVentasEchas").on('shown.bs.modal', function () {
            $("#txtSearchVentasEchas").focus();
        });

        $("#modalVentasEchas").on("hide.bs.modal", function () {
            tbListVentasEchas.empty();
            tbListVentasEchas.append('<tr><td class="text-center" colspan="8"><p>Iniciar la busqueda para cargar los datos.</p></td></tr>');
            $("#txtSearchVentasEchas").val('');
        });

        $("#txtSearchVentasEchas").on("keyup", function (event) {
            let value = $("#txtSearchVentasEchas").val();
            if (event.keyCode !== 9 && event.keyCode !== 18) {
                if (value.trim().length != 0) {
                    if (!stateVentasEchas) {
                        paginacionVentasEchas = 1;
                        fillTableVentasEchas(0, value.trim());
                        opcionVentasEchas = 0;
                    }
                }
            }
        });

        $("#btnUltimasVentas").click(function () {
            if (!stateVentasEchas) {
                paginacionVentasEchas = 1;
                fillTableVentasEchas(1, "");
                opcionVentasEchas = 1;
            }
        });

        $("#btnUltimasVentas").keypress(function (event) {
            if (event.keyCode === 13) {
                if (!stateVentasEchas) {
                    paginacionVentasEchas = 1;
                    fillTableVentasEchas(1, "");
                    opcionVentasEchas = 1;
                }
                event.preventDefault();
            }
        });
    }

    this.openModalInit = function () {
        $("#modalVentasEchas").modal("show");
    }

    function onEventPaginacion() {
        switch (opcionVentasEchas) {
            case 0:
                fillTableVentasEchas(0, $("#txtSearchVentasEchas").val());
                break;
            case 1:
                fillTableVentasEchas(1, "");
                break;
        }
    }

    async function fillTableVentasEchas(opcion, buscar) {
        try {
            let result = await tools.promiseFetchGet("../app/controller/VentaController.php", {
                "type": "ventasEchas",
                "opcion": opcion,
                "buscar": buscar,
                "posicionPagina": ((paginacionVentasEchas - 1) * filasPorPaginaVentasEchas),
                "filasPorPagina": filasPorPaginaVentasEchas
            }, function () {
                tools.loadTable(tbListVentasEchas, 8);
                stateVentasEchas = true;
                totalPaginacionVentasEchas = 0;
            });

            tbListVentasEchas.empty();
            if (result.data.length == 0) {
                tools.loadTableMessage(tbListVentasEchas, "No hay datos para mostrar.", 8);
                tools.paginationEmpty(ulPaginationVentasEchas);
                stateVentasEchas = false;
            } else {
                for (let value of result.data) {
                    tbListVentasEchas.append(`<tr>
                    <td>${value.Id}</td>
                    <td>${value.NumeroDocumento + '<br>' + value.Cliente}</td>
                    <td>${value.Comprobante + '<br>' + value.Serie + '-' + value.Numeracion}</td>
                    <td>${tools.getDateForma(value.FechaVenta)}<br>${tools.getTimeForma24(value.HoraVenta)}</td>
                    <td>${value.Simbolo + ' ' + tools.formatMoney(value.Total)}</td>
                    <td class="text-center"><button class="btn btn-danger"><image src="./images/print.png" width="22" height="22" /></button></td>
                    <td class="text-center"><button class="btn btn-danger" onclick="loadAddVenta('${value.IdVenta}')"><image src="./images/accept.png" width="22" height="22" /></button></td>
                    <td class="text-center"><button class="btn btn-danger"><image src="./images/plus.png" width="22" height="22" /></button></td>
                    </tr>
                    `);
                }
                totalPaginacionVentasEchas = parseInt(Math.ceil((parseFloat(result.total) / filasPorPaginaVentasEchas)));

                let i = 1;
                let range = [];
                while (i <= totalPaginacionVentasEchas) {
                    range.push(i);
                    i++;
                }

                let min = Math.min.apply(null, range);
                let max = Math.max.apply(null, range);

                let paginacionHtml = `
                    <button class="btn btn-outline-secondary" onclick="onEventPaginacionInicioVe(${min})">
                        <i class="fa fa-angle-double-left"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="onEventAnteriorPaginacionVe()">
                        <i class="fa fa-angle-left"></i>
                    </button>
                    <span class="btn btn-outline-secondary disabled">${paginacionVentasEchas} - ${totalPaginacionVentasEchas}</span>
                    <button class="btn btn-outline-secondary" onclick="onEventSiguientePaginacionVe()">
                        <i class="fa fa-angle-right"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="onEventPaginacionFinalVe(${max})">
                        <i class="fa fa-angle-double-right"></i>
                    </button>`;

                ulPaginationVentasEchas.html(paginacionHtml);
                stateVentasEchas = false;
            }
        } catch (error) {
            tools.loadTableMessage(tbListVentasEchas, tools.messageError(error), 8, true);
            tools.paginationEmpty(ulPaginationVentasEchas);
            stateVentasEchas = false;
        }
    }

    loadAddVenta = async function (idVenta) {
        try {
            let result = await tools.promiseFetchGet("../app/controller/VentaController.php", {
                "type": "ventaAgregar",
                "idVenta": idVenta
            }, function () {
                listaProductos = [];
                $("#modalVentasEchas").modal("hide");
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
                let precio = parseFloat(value.PrecioVenta);

                listaProductos.push({
                    "idSuministro": value.IdSuministro,
                    "clave": value.Clave,
                    "nombreMarca": value.NombreMarca,
                    "cantidad": cantidad,
                    "costoCompra": parseFloat(value.CostoVenta),
                    "bonificacion": 0,
                    "unidadCompra": value.UnidadCompra,
                    "unidadCompraName": value.UnidadCompraName,

                    "descuento": 0,
                    "descuentoCalculado": 0,
                    "descuentoSumado": 0,

                    "precioVentaGeneral": precio,
                    "precioVentaGeneralUnico": precio,
                    "precioVentaGeneralReal": precio,

                    "impuestoOperacion": value.Operacion,
                    "idImpuesto": value.IdImpuesto,
                    "impuestoNombre": value.NombreImpuesto,
                    "impuestoValor": parseFloat(value.ValorImpuesto),

                    "inventario": value.Inventario,
                    "unidadVenta": value.UnidadVenta,
                    "valorInventario": value.ValorInventario
                });

                renderTableProductos();
            }

        } catch (error) {
            console.log(error)
        }
    }

    onEventPaginacionInicioVe = function (value) {
        if (!stateVentasEchas) {
            if (value !== paginacionVentasEchas) {
                paginacionVentasEchas = value;
                onEventPaginacion();
            }
        }
    }

    onEventPaginacionFinalVe = function (value) {
        if (!stateVentasEchas) {
            if (value !== paginacionVentasEchas) {
                paginacionVentasEchas = value;
                onEventPaginacion();
            }
        }
    }

    onEventAnteriorPaginacionVe = function () {
        if (!stateVentasEchas) {
            if (paginacionVentasEchas > 1) {
                paginacionVentasEchas--;
                onEventPaginacion();
            }
        }
    }

    onEventSiguientePaginacionVe = function () {
        if (!stateVentasEchas) {
            if (paginacionVentasEchas < totalPaginacionVentasEchas) {
                paginacionVentasEchas++;
                onEventPaginacion();
            }
        }
    }

}