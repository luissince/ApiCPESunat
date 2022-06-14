<div class="modal fade" id="modalVentaPos" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-th-large">
                    </i> Venta Pos
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="overlay p-5" id="divOverlayVentaPos">
                        <div class="m-loader mr-4">
                            <svg class="m-circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle>
                            </svg>
                        </div>
                        <h4 class="l-text text-center text-white p-10" id="lblTextOverlayVentaPos">Cargando información...</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Inicio:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFechaInicioVp" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Fin:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFechaFinalVp" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Todos los Comprobantes</label>
                            <div class="form-group">
                                <input id="rbSelectComprobanteVp" type="checkbox" checked>
                                <label for="rbSelectComprobanteVp">Comprobantes</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Comprobantes</label>
                            <div class="form-group">
                                <select id="cbComprobanteVp" class="form-control" disabled>
                                    <option value="">- Seleccione -</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Todos los Clientes</label>
                            <div class="form-group">
                                <input id="rbSelectClienteVp" type="checkbox" checked>
                                <label for="rbSelectClienteVp">Clientes</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Clientes</label>
                            <div class="form-group">
                                <select id="cbClienteVp" class="form-control" disabled>
                                    <option value="">- Seleccione -</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Todos los Vendedores</label>
                            <div class="form-group">
                                <input id="rbSelectVendedorVp" type="checkbox" checked>
                                <label for="rbSelectVendedorVp">Vendedores</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Vendedores</label>
                            <div class="form-group">
                                <select id="cbVendedorVp" class="form-control" disabled>
                                    <option value="">- Seleccione -</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Todos las Tipo de Cobro</label>
                            <div class="form-group">
                                <input id="rbSelectTipoCobroVp" type="checkbox" checked>
                                <label for="rbSelectTipoCobroVp">Venta</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Venta</label>
                            <div class="form-group">
                                <select id="cbTipoCobroVp" class="form-control" disabled>
                                    <option value="">- Seleccione -</option>
                                    <option value="1">AL CONTADO</option>
                                    <option value="2">AL CRÉDITO</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Todos los Metodos de Cobro</label>
                            <div class="form-group">
                                <input id="rbSelectMetodoCobroVp" type="checkbox" checked>
                                <label for="rbSelectMetodoCobroVp">Metodo</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Metodo</label>
                            <div class="form-group">
                                <select id="cbMetodoCobroVp" class="form-control" disabled>
                                    <option value="">- Seleccione -</option>
                                    <option value="1">EFECTIVO</option>
                                    <option value="2">TARJETA</option>
                                    <option value="3">MIXTO (EFECTIVO y TARJETA)</option>
                                    <option value="4">DEPÓSITO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnPdfVentaPos">
                    <i class="text-danger fa fa-file-pdf-o"></i> Pdf</button>
                <button type="button" class="btn btn-secondary" id="btnExcelVentaPos">
                    <i class="text-success fa fa-file-excel-o"></i> Excel</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-close"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>