<div class="modal fade" id="modalVentasEchas" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-window-maximize"></i> Ventas realizadas
                </h4>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <label>Puedes buscar por seríe y/o numeración o cliente.</label>
                        <div class="form-group">
                            <div class="input-group">
                                <input id="txtSearchVentasEchas" type="search" class="form-control" placeholder="Buscar" aria-controls="sampleTable">

                                <div class="input-group-append">
                                    <button id="btnUltimasVentas" class="btn btn-secondary" type="button" title="Recargar"><i class="fa fa-refresh"></i> Ulimas Ventas</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-header-background">
                                    <tr role="row">
                                        <th class="text-center">#</th>
                                        <th class="text-center">Cliente</th>
                                        <th class="text-center">Comprobante</th>
                                        <th class="text-center">Fecha y Hora</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Imprimir</th>
                                        <th class="text-center">Agregar V.</th>
                                        <th class="text-center">Sumar V.</th>
                                    </tr>
                                </thead>
                                <tbody id="tbListVentasEchas">
                                    <tr>
                                        <td colspan="8" align="center">Iniciar la busqueda para cargar los datos.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-12 text-center">
                        <label>Paginación</label>
                        <div class="form-group" id="ulPaginationVentasEchas">
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