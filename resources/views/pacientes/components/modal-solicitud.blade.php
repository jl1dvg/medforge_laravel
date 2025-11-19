<div class="modal fade" id="modalSolicitud" tabindex="-1" aria-labelledby="modalSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSolicitudLabel">Detalle de la Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 p-3 rounded" id="solicitudContainer" style="background-color:#e9f5ff;">
                    <p class="mb-1">
                        <strong>Fecha:</strong>
                        <span id="modalFecha" class="float-end badge bg-light text-dark"></span>
                    </p>
                    <p class="mb-1"><strong>Procedimiento:</strong> <span id="modalProcedimiento"></span></p>
                    <p class="mb-1"><strong>Ojo:</strong> <span id="modalOjo"></span></p>
                    <p class="mb-1"><strong>Diagnóstico:</strong> <span id="modalDiagnostico"></span></p>
                    <p class="mb-1"><strong>Doctor:</strong> <span id="modalDoctor"></span></p>
                    <p class="mb-1">
                        <strong>Estado:</strong>
                        <span id="modalEstado" class="float-end badge bg-secondary"></span>
                        <span id="modalSemaforo" class="float-end me-2 badge" style="width:16px;height:16px;border-radius:50%;"></span>
                    </p>
                </div>
                <p><strong>Motivo:</strong> <span id="modalMotivo"></span></p>
                <p><strong>Enfermedad Actual:</strong> <span id="modalEnfermedad"></span></p>
                <p><strong>Plan:</strong> <span id="modalDescripcion"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
