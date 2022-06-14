function ModalProductos() {

    let arrayProductos = [];
    let stateProductos = false;
    let paginacionProductos = 0;
    let opcionProductos = 0;
    let totalPaginacionProductos = 0;
    let filasPorPaginaProductos = 10;
    let tbListProductos = $("#tbListProductos");
    let ulPaginationProductos = $("#ulPaginationProductos");

    this.init = function () {

        $("#btnProductos").click(function () {
            $("#modalProductos").modal("show");
        });

        $("#btnProductos").keypress(function (event) {
            if (event.keyCode == 13) {
                $("#modalProductos").modal("show");
                event.preventDefault();
            }
        });

        $('#modalProductos').on('shown.bs.modal', function () {
            $('#txtSearchProducto').trigger('focus');
            loadInitVentas();
        });

        $('#modalProductos').on('hide.bs.modal', function () {
            $("#tbListProductos").empty();
        });

        $("#txtSearchProducto").keyup(function () {
            if (!stateProductos) {
                if ($("#txtSearchProducto").val().trim().length != 0) {
                    paginacionProductos = 1;
                    fillProductosTable(1, $("#txtSearchProducto").val().trim());
                    opcionProductos = 1;
                }
            }
        });

        $("#btnAnteriorProducto").click(function () {
            if (!stateProductos) {
                if (paginacionProductos > 1) {
                    paginacionProductos--;
                    onEventPaginacion();
                }
            }
        });

        $("#btnSiguienteProducto").click(function () {
            if (!stateProductos) {
                if (paginacionProductos < totalPaginacionProductos) {
                    paginacionProductos++;
                    onEventPaginacion();
                }
            }
        });

        $("#btnReloadProducto").click(function () {
            loadInitVentas();
        });
    }

    this.openModalInit = function () {
        $("#modalProductos").modal("show");
    }

    function onEventPaginacion() {
        switch (opcionProductos) {
            case 0:
                fillProductosTable(0, "");
                break;
            case 1:
                fillProductosTable(1, $("#txtSearchProducto").val().trim());
                break;
        }
    }

    function loadInitVentas() {
        if (!stateProductos) {
            paginacionProductos = 1;
            fillProductosTable(0, "");
            opcionProductos = 0;
        }
    }

    async function fillProductosTable(tipo, value) {
        try {
            let result = await tools.promiseFetchGet("../app/controller/SuministroController.php", {
                "type": "modalproductos",
                "tipo": tipo,
                "value": value,
                "posicionPagina": ((paginacionProductos - 1) * filasPorPaginaProductos),
                "filasPorPagina": filasPorPaginaProductos
            }, function () {
                tools.loadTable(tbListProductos, 7);
                stateProductos = true;
                totalPaginacionProductos = 0;
                arrayProductos = [];
            });

            let object = result;
            tbListProductos.empty();
            arrayProductos = object.data;
            if (arrayProductos.length === 0) {
                tools.loadTableMessage(tbListProductos, "No hay datos para mostrar", 7);
                tools.paginationEmpty(ulPaginationProductos);
                stateProductos = false;
            } else {
                for (let producto of arrayProductos) {
                    tbListProductos.append(`<tr>
                        <td class="text-center">${producto.Id}</td>
                        <td>${producto.Clave + '</br>' + producto.NombreMarca}</td>
                        <td>${producto.Categoria + '<br>' + producto.Marca}</td>
                        <td class="${(parseFloat(producto.Cantidad) > 0 ? "text-black" : "text-danger")}">${tools.formatMoney(parseFloat(producto.Cantidad)) + '<br>' + producto.UnidadCompraName}</td>
                        <td>${producto.ImpuestoNombre}</td>
                        <td>${tools.formatMoney(parseFloat(producto.PrecioVentaGeneral))}</td>
                        <td><button class="btn btn-danger" onclick="onSelectProducto('${producto.IdSuministro}')"><image src="./images/accept.png" width="22" height="22" /></button></td>' +
                        </tr>`);
                }
                totalPaginacionProductos = parseInt(Math.ceil((parseFloat(object.total) / filasPorPaginaProductos)));

                let i = 1;
                let range = [];
                while (i <= totalPaginacionProductos) {
                    range.push(i);
                    i++;
                }

                let min = Math.min.apply(null, range);
                let max = Math.max.apply(null, range);

                let paginacionHtml = `
                    <button class="btn btn-outline-secondary" onclick="onEventPaginacionInicioPr(${min})">
                        <i class="fa fa-angle-double-left"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="onEventAnteriorPaginacionPr()">
                        <i class="fa fa-angle-left"></i>
                    </button>
                    <span class="btn btn-outline-secondary disabled">${paginacionProductos} - ${totalPaginacionProductos}</span>
                    <button class="btn btn-outline-secondary" onclick="onEventSiguientePaginacionPr()">
                        <i class="fa fa-angle-right"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="onEventPaginacionFinalPr(${max})">
                        <i class="fa fa-angle-double-right"></i>
                    </button>`;

                ulPaginationProductos.html(paginacionHtml);

                stateProductos = false;
            }
        } catch (error) {
            tools.loadTableMessage(tbListProductos, tools.messageError(error), 7, true);
            tools.paginationEmpty(ulPaginationProductos);
            stateProductos = false;
        }
    }

    onSelectProducto = function (idSuministro) {
        for (let i = 0; i < arrayProductos.length; i++) {
            if (arrayProductos[i].IdSuministro == idSuministro) {
                if (!validateDatelleVenta(idSuministro)) {
                    let suministro = arrayProductos[i];
                    let cantidad = 1;
                    let precio = parseFloat(suministro.PrecioVentaGeneral);

                    listaProductos.push({
                        "idSuministro": suministro.IdSuministro,
                        "clave": suministro.Clave,
                        "nombreMarca": suministro.NombreMarca,
                        "cantidad": cantidad,
                        "costoCompra": parseFloat(suministro.PrecioCompra),
                        "bonificacion": 0,
                        "unidadCompra": suministro.UnidadCompra,
                        "unidadCompraName": suministro.UnidadCompraName,

                        "descuento": 0,
                        "descuentoCalculado": 0,
                        "descuentoSumado": 0,

                        "precioVentaGeneral": precio,
                        "precioVentaGeneralUnico": precio,
                        "precioVentaGeneralReal": precio,

                        "impuestoOperacion": suministro.Operacion,
                        "idImpuesto": suministro.Impuesto,
                        "impuestoNombre": suministro.ImpuestoNombre,
                        "impuestoValor": parseFloat(suministro.Valor),

                        "inventario": suministro.Inventario,
                        "unidadVenta": suministro.UnidadVenta,
                        "valorInventario": suministro.ValorInventario
                    });
                    break;
                } else {
                    for (let i = 0; i < listaProductos.length; i++) {
                        if (listaProductos[i].idSuministro == idSuministro) {
                            let currenteObject = listaProductos[i];

                            currenteObject.cantidad = parseFloat(currenteObject.cantidad) + 1;
                            break;
                        }
                    }
                }
            }
        }
        renderTableProductos();
        $("#txtSearchProducto").focus();
    }

    onEventPaginacionInicioPr = function (value) {
        if (!stateProductos) {
            if (value !== paginacionProductos) {
                paginacionProductos = value;
                onEventPaginacion();
            }
        }
    }

    onEventPaginacionFinalPr = function (value) {
        if (!stateProductos) {
            if (value !== paginacionProductos) {
                paginacionProductos = value;
                onEventPaginacion();
            }
        }
    }

    onEventAnteriorPaginacionPr = function () {
        if (!stateProductos) {
            if (paginacionProductos > 1) {
                paginacionProductos--;
                onEventPaginacion();
            }
        }
    }

    onEventSiguientePaginacionPr = function () {
        if (!stateProductos) {
            if (paginacionProductos < totalPaginacionProductos) {
                paginacionProductos++;
                onEventPaginacion();
            }
        }
    }

}