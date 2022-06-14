<div class="row">
    <div class="modal fade" id="modaModalFacturado" data-backdrop="static">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <i class="fa fa-th-large">
                        </i> Comprobantes SUNAT
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Inicio:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFIFacturado" />
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
                            <label>Fecha de Fin:</label>
                            <div class="form-group">
                                <input class="form-control" type="date" id="txtFFFacturado" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <span>Se va generar todos los documentos facturados.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnPdfFacturado">
                        <i class="text-danger fa fa-file-pdf-o"></i> PDF</button>
                    <button type="button" class="btn btn-secondary" id="btnExcelFacturado">
                        <i class="text-success fa fa-file-excel-o"></i> Excel</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <i class="fa fa-remove"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>