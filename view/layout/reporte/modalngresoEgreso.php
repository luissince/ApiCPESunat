<div class="modal fade" id="modalIngresoEgreso" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-th-large">
                    </i> Ingresos/Egresos
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="overlay p-5" id="divOverlayIngresoEgreso">
                        <div class="m-loader mr-4">
                            <svg class="m-circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle>
                            </svg>
                        </div>
                        <h4 class="l-text text-center text-white p-10" id="lblTextOverlayIngresoEgreso">Cargando información...</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Inicio:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFIIngresoEgreso" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Fin:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFFIngresoEgreso" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Todos los vendedores</label>
                            <div class="form-group">
                                <input id="cbSelectVendedorIngresoEgreso" type="checkbox" checked>
                                <label for="cbSelectVendedorIngresoEgreso">Vendedores</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Vendedor</label>
                            <div class="form-group">
                                <select id="cbVendedorVIngresoEgreso" class="select2-selection__rendered  form-control" disabled>
                                    <option value="">- Seleccione -</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectTransaccionesIngresoEgreso" type="checkbox" checked="">
                                <label for="rbSelectTransaccionesIngresoEgreso">Todos las transacciones</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectMovimientoIngresoEgreso" type="checkbox" checked="">
                                <label for="rbSelectMovimientoIngresoEgreso">Todos los movimientos de caja</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnPdfIngresoEgreso">
                    <i class="text-danger fa fa-file-pdf-o"></i> Pdf</button>
                <button type="button" class="btn btn-secondary" id="btnExcelIngresoEgreso">
                    <i class="text-success fa fa-file-excel-o"></i> Excel</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-close"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>