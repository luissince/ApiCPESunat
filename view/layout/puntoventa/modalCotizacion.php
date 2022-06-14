<div class="modal fade" id="modalCotizacion" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-window-maximize"></i> Lista de Cotizaciones
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
            </div>

            <div class="modal-body">

                <diw class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Buscar por N° de Cotización o Cliente </label>
                            <div class="input-group">
                                <input id="txtSearchCotizacion" type="text" class="form-control" placeholder="Buscar">
                                <div class="input-group-append">
                                    <button id="btnRecargarCotizacion" class="btn btn-secondary" type="button" title="Recargar"><i class="fa fa-refresh"></i> Recargar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12">
                        <div class="form-group">
                            <label>Fecha Inicio </label>
                            <input id="txtFechaInicialCotizacion" type="date" class="form-control" placeholder="Fecha de Inicio">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div class="form-group">
                            <label>Fecha Fin </label>
                            <input id="txtFechaFinalCotizacion" type="date" class="form-control" placeholder="Fecha de Fin">
                        </div>
                    </div>
                </diw>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-header-background">
                                    <tr role="row">
                                        <th class="text-center">#</th>
                                        <th class="text-center">Vendedor</th>
                                        <th class="text-center">Cotización</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Cliente</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Agregar</th>
                                    </tr>
                                </thead>
                                <tbody id="tbListCotizacion">
                                    <tr>
                                        <td colspan="7" align="center"> !No hay datos para mostrar¡</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-12 text-center">
                        <label>Paginación</label>
                        <div class="form-group" id="ulPaginationCotizacion">
                            <button class="btn btn-outline-secondary">
                                <i class="fa fa-angle-double-left"></i>
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="fa fa-angle-left"></i>
                            </button>
                            <span class="btn btn-outline-secondary disabled" id="lblPaginacion">0 - 0</span>
                            <button class="btn btn-outline-secondary">
                                <i class="fa fa-angle-right"></i>
                            </button>
                            <button class="btn btn-outline-secondary">
                                <i class="fa fa-angle-double-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>