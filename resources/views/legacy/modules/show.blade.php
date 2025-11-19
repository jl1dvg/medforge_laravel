@extends('layouts.app')

@section('title', $module['name'] ?? 'Módulo')

@section('content')
    <section class="content-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="content-title mb-0">{{ $module['name'] }}</h1>
            <p class="text-muted mb-0">{{ $module['description'] }}</p>
        </div>
        <a class="btn btn-outline-primary" href="{{ route('legacy.modules.index') }}">Volver al listado</a>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="box mb-4">
                    <div class="box-header with-border">
                        <h4 class="box-title">Rutas disponibles</h4>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Canal</th>
                                <th>Método</th>
                                <th>URI</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(['web', 'api'] as $channel)
                                @foreach($module['routes'][$channel] ?? [] as $route)
                                    <tr>
                                        <td><span class="badge badge-secondary text-uppercase">{{ $channel }}</span></td>
                                        <td><code>{{ $route['method'] }}</code></td>
                                        <td class="text-break">{{ $route['uri'] }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="box mb-4">
                    <div class="box-header with-border">
                        <h4 class="box-title mb-0">Ubicaciones clave</h4>
                    </div>
                    <div class="box-body">
                        <dl class="row mb-0">
                            @if($paths['controller'])
                                <dt class="col-sm-3">Controller legacy</dt>
                                <dd class="col-sm-9"><code>{{ $paths['controller'] }}</code></dd>
                            @endif
                            @if($paths['module_root'])
                                <dt class="col-sm-3">Módulo</dt>
                                <dd class="col-sm-9"><code>{{ $paths['module_root'] }}</code></dd>
                            @endif
                            @foreach($paths['views'] as $view)
                                <dt class="col-sm-3">Vista</dt>
                                <dd class="col-sm-9">
                                    <div class="text-break">
                                        <code>{{ $view['absolute'] }}</code>
                                        <span class="text-muted">({{ $view['relative'] }})</span>
                                    </div>
                                </dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-12">
                <div class="box mb-4">
                    <div class="box-header with-border">
                        <h4 class="box-title">Assets</h4>
                    </div>
                    <div class="box-body">
                        <h5 class="text-uppercase text-muted fs-12">Estilos</h5>
                        <ul class="list-unstyled mb-20">
                            @forelse($assets['styles'] as $style)
                                <li class="mb-10">
                                    <code>{{ $style['relative'] }}</code>
                                    <div class="text-muted fs-12 text-break">{{ $style['absolute'] }}</div>
                                </li>
                            @empty
                                <li class="text-muted">Sin hojas de estilo registradas.</li>
                            @endforelse
                        </ul>
                        <h5 class="text-uppercase text-muted fs-12">Scripts</h5>
                        <ul class="list-unstyled mb-0">
                            @forelse($assets['scripts'] as $script)
                                <li class="mb-10">
                                    <code>{{ $script['relative'] }}</code>
                                    <div class="text-muted fs-12 text-break">{{ $script['absolute'] }}</div>
                                </li>
                            @empty
                                <li class="text-muted">Sin scripts registrados.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
