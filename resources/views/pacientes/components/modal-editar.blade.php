@php
    $data = $patientData ?? [];
    $afiliaciones = $afiliacionesDisponibles ?? [];
@endphp

<div class="modal fade" id="modalEditarPaciente" tabindex="-1" aria-labelledby="modalEditarPacienteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="#" onsubmit="return false;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarPacienteLabel">Editar Datos del Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Primer Nombre</label>
                        <input type="text" name="fname" class="form-control" value="{{ $data['fname'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Segundo Nombre</label>
                        <input type="text" name="mname" class="form-control" value="{{ $data['mname'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Primer Apellido</label>
                        <input type="text" name="lname" class="form-control" value="{{ $data['lname'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Segundo Apellido</label>
                        <input type="text" name="lname2" class="form-control" value="{{ $data['lname2'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Afiliación</label>
                        <select name="afiliacion" class="form-control">
                            @foreach($afiliaciones as $afiliacion)
                                <option value="{{ $afiliacion }}" @selected(strtolower($afiliacion) === strtolower($data['afiliacion'] ?? ''))>
                                    {{ $afiliacion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ $data['fecha_nacimiento'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sexo</label>
                        <select name="sexo" class="form-control">
                            <option value="Masculino" @selected(strtolower($data['sexo'] ?? '') === 'masculino')>Masculino</option>
                            <option value="Femenino" @selected(strtolower($data['sexo'] ?? '') === 'femenino')>Femenino</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Celular</label>
                        <input type="text" name="celular" class="form-control" value="{{ $data['celular'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Historia Clínica</label>
                        <input type="text" class="form-control" value="{{ $data['hc_number'] ?? '' }}" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
