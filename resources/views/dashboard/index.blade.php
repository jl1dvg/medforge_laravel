@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @include('dashboard.legacy.users-index', [
        'counts' => $counts,
        'range' => $range,
        'users' => $users,
    ])
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('legacyProfileModal');
            if (!modal) {
                return;
            }

            const closeModal = () => {
                modal.classList.remove('is-visible');
            };

            modal.querySelectorAll('[data-legacy-modal-close]').forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            const formatDate = (value) => {
                if (!value) {
                    return '—';
                }
                const parsed = new Date(value);
                if (Number.isNaN(parsed.getTime())) {
                    return value;
                }
                return parsed.toLocaleString('es-EC', {
                    dateStyle: 'medium',
                    timeStyle: 'short',
                });
            };

            const populateProfile = (payload) => {
                const fill = (field, text) => {
                    const target = modal.querySelector(`[data-profile-field="${field}"]`);
                    if (!target) {
                        return;
                    }

                    if (field === 'firma-src') {
                        if (text) {
                            target.setAttribute('src', text);
                        } else {
                            target.removeAttribute('src');
                        }
                        return;
                    }

                    if (field === 'firma-wrapper') {
                        target.hidden = !text;
                        return;
                    }

                    target.textContent = text ?? '—';
                };

                fill('nombre', payload.nombre ?? payload.username ?? 'Usuario');
                fill('role', payload.role ?? 'Usuario');
                fill('ingreso', formatDate(payload.created_at));
                fill('correo', payload.email ?? '—');
                fill('username', payload.username ?? '—');
                fill('especialidad', payload.especialidad ?? '—');
                fill('subespecialidad', payload.subespecialidad ?? '—');
                fill('biografia', payload.biografia ?? 'Este usuario aún no ha agregado una biografía.');
                fill('firma-wrapper', Boolean(payload.firma));
                fill('firma-src', payload.firma ?? '');
            };

            document.querySelectorAll('.btn-ver-perfil').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    let payload = {};
                    try {
                        payload = JSON.parse(button.getAttribute('data-profile') ?? '{}');
                    } catch (error) {
                        console.warn('No se pudo parsear el perfil legacy', error);
                    }

                    populateProfile(payload);
                    modal.classList.add('is-visible');
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('is-visible')) {
                    closeModal();
                }
            });
        });
    </script>
@endpush
