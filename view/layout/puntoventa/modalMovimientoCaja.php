<div class="row">
    <div class="modal fade" id="modalMovimientoCaja" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fa fa-window-maximize"></i> Movimiento de caja
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="fa fa-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="cbxMovimiento">Movimiento: </label>
                                <select id="cbxMovimiento" class="form-control">
                                    <option value="">- Seleccione -</option>
                                    <option value="1">ENTRADA</option>
                                    <option value="2">SALIDAD</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="txtMonto">Monto: </label>
                                <input id="txtMonto" type="text" class="form-control" placeholder="00.00">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <label for="txtComentarioMovimiento">Comentario: </label>
                                <input id="txtComentarioMovimiento" type="text" class="form-control" placeholder="Ingrese un comentario">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <div class="text-center">
                                    <button id="btnEjecutarMovimiento" class="btn btn-info" type="button" title="Aceptar"><i class="fa fa-check"></i> Aceptar</button>
                                    <button class="btn btn-danger" type="button" title="Cancelar" data-dismiss="modal"><i class="fa fa-close"></i> Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>