<div class="modal fade" id="modalUtilidad" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-cube">
                    </i> Utilidad
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="tile">
                    <div class="overlay p-5" id="divOverlayUtilidad">
                        <div class="m-loader mr-4">
                            <svg class="m-circular" viewBox="25 25 50 50">
                                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10"></circle>
                            </svg>
                        </div>
                        <h4 class="l-text text-center text-white p-10" id="lblTextOverlayUtilidad">Cargando información...</h4>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Inicio:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFechaInicioUtilidad" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Fin:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFechaFinalUtilidad" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectProductoUtilidad" type="checkbox" checked>
                                <label for="rbSelectProductoUtilidad">Todos los productos</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                        
                            <div class="form-group">
                                <select id="cbProductoUtilidad" class="select2-selection__rendered  form-control" disabled>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                        
                            <div class="form-group">
                                <input id="rbSelectCategoriaUtilidad" type="checkbox" checked>
                                <label for="rbSelectCategoriaUtilidad">Todas las categorias</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                            
                            <div class="form-group">
                                <select id="cbCategoriaUtilidad" class="form-control" disabled>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                            
                            <div class="form-group">
                                <input id="rbSelectMarcaUtilidad" type="checkbox" checked>
                                <label for="rbSelectMarcaUtilidad">Todas las marcas</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                           
                            <div class="form-group">
                                <select id="cbMarcaUtilidad" class="form-control" disabled>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                           
                            <div class="form-group">
                                <input id="rbSelectPresentacionUtilidad" type="checkbox" checked>
                                <label for="rbSelectPresentacionUtilidad">Todas las presentaciones</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">                           
                            <div class="form-group">
                                <select id="cbPresentacionUtilidad" class="form-control" disabled>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">                          
                            <div class="form-group">
                                <input id="rbSelectMostrarTodoUtilidad" type="checkbox" checked>
                                <label for="rbSelectMostrarTodoUtilidad">Mostrar detalle de cada producto</label>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnPdfUtilidad">
                    <i class="text-danger fa fa-file-pdf-o"></i> Pdf</button>
                <button type="button" class="btn btn-secondary" id="btnExcelUtilidad">
                    <i class="text-success fa fa-file-excel-o"></i> Excel</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fa fa-close"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>