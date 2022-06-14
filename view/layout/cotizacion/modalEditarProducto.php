<div class="row">
    <div class="modal fade" id="modalEditProducto" data-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fa fa-edit"></i> Editar Producto
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="fa fa-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Cantidad <i class="fa fa-fw fa-asterisk text-danger"></i></label>
                                <div class="input-group">
                                    <input id="txtCantidadEdit" class="form-control" type="text" placeholder="Ingrese la cantidad" autocomplete="off" />
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Precio <i class="fa fa-fw fa-asterisk text-danger"></i></label>
                                <div class="input-group">
                                    <input id="txtPrecioEdit" class="form-control" type="text" placeholder="Ingrese el precio" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="form-group">
                                <label>Unidad de Medida <i class="fa fa-fw fa-asterisk text-danger"></i></label>
                                <div class="input-group">
                                    <select id="cbMedidaEdit" class="select2-selection__rendered  form-control"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" modal-footer">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group">
                                <label class="form-text text-left text-danger">Los campos marcados con * son obligatorios</label>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 text-right">
                            <button class="btn btn-success" type="button" id="btnSaveEditProducto"><i class="fa fa-save"></i> Guardar</button>
                            <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-close"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>