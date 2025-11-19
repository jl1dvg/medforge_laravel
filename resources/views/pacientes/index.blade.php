@extends('layouts.app')

@section('title', 'Pacientes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/datatable/datatables.min.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title">Pacientes</h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pacientes</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    @if($showNotFoundAlert)
                        <div class="box-body">
                            <div class="alert alert-warning mb-0">
                                No encontramos el paciente solicitado. Intenta nuevamente desde la lista.
                            </div>
                        </div>
                    @endif
                    <div class="box-body">
                        <div class="table-responsive rounded card-table">
                            <table class="table table-striped table-hover table-sm invoice-archive" id="pacientes-table">
                                <thead class="bg-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Última consulta</th>
                                    <th>Nombre completo</th>
                                    <th>Afiliación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tableElement = document.getElementById('pacientes-table');
            if (!tableElement || typeof $ === 'undefined') {
                return;
            }

            $(tableElement).DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route('pacientes.datatable') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                },
                columns: [
                    { data: 'hc_number' },
                    { data: 'ultima_fecha' },
                    { data: 'full_name' },
                    { data: 'afiliacion' },
                    { data: 'estado_html', orderable: false, searchable: false },
                    { data: 'acciones_html', orderable: false, searchable: false },
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json',
                },
            });
        });
    </script>
@endpush
