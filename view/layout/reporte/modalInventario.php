<div class="modal fade" id="modalInventario" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-home">
                    </i> Inventario
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="overlay p-5" id="divOverlayInventario">
                        <div class="m-loader mr-4">
                            <svg class="m-circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle>
                            </svg>
                        </div>
                        <h4 class="l-text text-center text-white p-10" id="lblTextOverlayInventario">Cargando información...</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <label>Inventario:</label>
                            <div class="form-group">
                                <select class="form-control" id="cbAlmacenInventario">
                                    
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectUnidadInventario" type="checkbox" checked>
                                <label for="rbSelectUnidadInventario">Todas las Unidades de Medida</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                        
                            <div class="form-group">
                                <select id="cbUnidadInventario" class="form-control" disabled>
                                    
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectCategoriaInventario" type="checkbox" checked>
                                <label for="rbSelectCategoriaInventario">Todas las Categorías</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                        
                            <div class="form-group">
                                <select id="cbCategoriaInventario" class="form-control" disabled>
                                   
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectMarcaInventario" type="checkbox" checked>
                                <label for="rbSelectMarcaInventario">Todas las Marcas</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                        
                            <div class="form-group">
                                <select id="cbMarcaInventario" class="form-control" disabled>
                                    
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectPresentacionInventario" type="checkbox" checked>
                                <label for="rbSelectPresentacionInventario">Todas las Presentaciones</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                        
                            <div class="form-group">
                                <select id="cbPresentacionInventario" class="form-control" disabled>
                                   
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectExistenciaInventario" type="checkbox" checked>
                                <label for="rbSelectExistenciaInventario">Todas las Existencia</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                        
                            <div class="form-group">
                                <select id="cbExistenciaInventario" class="form-control" disabled>
                                   
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnPdfInventario">
                    <i class="text-danger fa fa-file-pdf-o"></i> Pdf</button>
                <button type="button" class="btn btn-secondary" id="btnExcelInventario">
                    <i class="text-success fa fa-file-excel-o"></i> Excel</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-close"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>