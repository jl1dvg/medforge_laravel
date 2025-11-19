<div class="legacy-modal" id="legacyProfileModal" aria-hidden="true">
    <div class="legacy-modal__dialog">
        <div class="legacy-modal__header d-flex justify-content-between align-items-center">
            <div>
                <p class="legacy-profile__label mb-1">Perfil del usuario</p>
                <h5 class="mb-0" data-profile-field="nombre">—</h5>
            </div>
            <button type="button" class="btn btn-link text-muted" data-legacy-modal-close>
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="legacy-modal__body">
            <div class="d-flex flex-column flex-md-row align-items-start mb-4">
                <div class="legacy-profile__avatar mb-3 mb-md-0" data-profile-field="avatar">👤</div>
                <div>
                    <p class="mb-1"><strong>Rol:</strong> <span data-profile-field="role">Usuario</span></p>
                    <p class="mb-1">
                        <strong>Ingreso:</strong>
                        <span data-profile-field="ingreso">—</span>
                    </p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="legacy-profile__label mb-1">Correo</p>
                    <p class="legacy-profile__value" data-profile-field="correo">—</p>
                </div>
                <div class="col-md-6">
                    <p class="legacy-profile__label mb-1">Usuario</p>
                    <p class="legacy-profile__value" data-profile-field="username">—</p>
                </div>
                <div class="col-md-6">
                    <p class="legacy-profile__label mb-1">Especialidad</p>
                    <p class="legacy-profile__value" data-profile-field="especialidad">—</p>
                </div>
                <div class="col-md-6">
                    <p class="legacy-profile__label mb-1">Subespecialidad</p>
                    <p class="legacy-profile__value" data-profile-field="subespecialidad">—</p>
                </div>
            </div>
            <div class="mt-4" data-profile-field="firma-wrapper" hidden>
                <p class="legacy-profile__label mb-1">Firma</p>
                <img src="" alt="Firma" class="img-fluid rounded" data-profile-field="firma-src">
            </div>
            <div class="mt-4">
                <p class="legacy-profile__label mb-1">Biografía</p>
                <p class="legacy-profile__value" data-profile-field="biografia">Este usuario aún no ha agregado una biografía.</p>
            </div>
        </div>
        <div class="legacy-modal__footer text-end">
            <button type="button" class="btn btn-secondary" data-legacy-modal-close>Cerrar</button>
        </div>
    </div>
</div>
