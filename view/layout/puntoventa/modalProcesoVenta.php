<div class="row">
    <div class="modal fade" id="modalProcesoVenta" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fa fa-shopping-bag"></i> Completar venta
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="fa fa-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="text-center">
                                <h3>TOTAL A PAGAR: <span id="lblTotalModal">M 0.00</span></h3>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <hr>
                        </div>
                        <div class="col-md-4 col-sm-4 d-flex align-items-center justify-content-center">
                            <h6 class="mb-0">Tipos de pagos</h6>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <button id="btnContado" class="btn btn-primary btn-block" type="button" title="Pago al contado">
                                <div class="row">
                                    <div class="col-md-12">
                                        <img src="./images/efectivo.png" width="28">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <label>Contado</label>
                                </div>
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <button id="btnCredito" class="btn btn-secondary btn-block" type="button" title="Pago al credito">
                                <div class="row">
                                    <div class="col-md-12">
                                        <img src="./images/generar.png" width="28">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <label>Crédito</label>
                                </div>
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <button id="btnAdelantado" class="btn btn-secondary btn-block" type="button" title="Pago adelantado">
                                <div class="row">
                                    <div class="col-md-12">
                                        <img src="./images/presupuesto.png" width="28">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <label>Adelanto</label>
                                </div>
                            </button>
                        </div>
                    </div>
                    <hr class="mt-3 mb-2" />

                    <!-- OPCION VENTA CONTADO -->
                    <div id="boxContado">
                        <div class="row d-flex justify-content-center mt-3">
                            <div class="col-md-6 col-sm-12" style="display: contents;">
                                <label><input id="cbDeposito" type="checkbox" /> Deposito</label>
                            </div>
                        </div>

                        <div id="divEfectivo">
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtEfectivo">Efectivo: </label>
                                        <input id="txtEfectivo" type="text" class="form-control" placeholder="0.0">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtTarjeta">Tarjeta: </label>
                                        <input id="txtTarjeta" type="text" class="form-control" placeholder="0.0">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <div class="text-center d-flex justify-content-center">
                                            <h3 class="mr-1" id="lblVueltoNombre">N.V.</h3>
                                            <h3 class="text-primary" id="lblVuelto"> 0.00</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="divEfectivoDeposito">
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtDeposito">Efectivo: </label>
                                        <input id="txtDeposito" type="text" class="form-control" placeholder="0.0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtNumOperacion">Número de Operación: </label>
                                        <input id="txtNumOperacion" type="text" class="form-control" placeholder="0.0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OPCION VENTA CREDITO -->
                    <div id="boxCredito" class="d-none">
                        <div class="row d-flex justify-content-center">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="txtFechaVencimiento">Fecha de venciminto: </label>
                                    <input id="txtFechaVencimiento" type="date" class="form-control" placeholder="0.0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- OPCION VENTA ADELANTADO -->
                    <div id="boxAdelantado" class="d-none">
                        <div class="row d-flex justify-content-center mt-3">
                            <div class="col-md-6 col-sm-12" style="display: contents;">
                                <label><input id="cbDepositoAdelantado" type="checkbox" /> Deposito</label>
                            </div>
                        </div>

                        <div class="row d-flex justify-content-center">
                            <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label class="text-center"><i class="fa fa-info text-info"></i> El proceso de pago adelantado consiste en registrar el monto/dinero sin modificar la cantidad de los productos agregados en el detalle de venta.</label>
                                </div>
                            </div>
                        </div>

                        <div id="divAdelantado">
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtEfectivoAdelanto">Efectivo: </label>
                                        <input id="txtEfectivoAdelanto" type="text" class="form-control" placeholder="0.0">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtTarjetaAdelanto">Tarjeta: </label>
                                        <input id="txtTarjetaAdelanto" type="text" class="form-control" placeholder="0.0">
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <div class="text-center d-flex justify-content-center">
                                            <h3 class="mr-1" id="lblVueltoAdelantoNombre">N.V.</h3>
                                            <h3 class="text-primary" id="lblVueltoAdelanto"> 0.00</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="divAdelantadoDeposito">
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtDepositoAdelantado">Efectivo: </label>
                                        <input id="txtDepositoAdelantado" type="text" class="form-control" placeholder="0.0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label for="txtNumOperacionAdelantado">Número de Operación: </label>
                                        <input id="txtNumOperacionAdelantado" type="text" class="form-control" placeholder="0.0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group">
                                <div class="text-center">
                                    <button id="btnCompletarVenta" class="btn btn-success" type="button" title="Procesar venta"><i class="fa fa-save"></i> Completar venta</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>