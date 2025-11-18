@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="content-header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div>
                <h3 class="page-title">Dashboard</h3>
                <p class="text-muted mb-0">Resumen operativo · {{ $range['start'] }} – {{ $range['end'] }}</p>
            </div>
        </div>
    </div>

    @include('dashboard.widgets.summary-cards', compact('counts', 'range'))

    <div class="row mt-3 g-3">
        <div class="col-xl-8">
            @include('dashboard.widgets.procedures-trend', compact('trend'))
        </div>
        <div class="col-xl-4">
            @include('dashboard.widgets.top-procedures', compact('topProcedures'))
        </div>
    </div>

    <div class="row mt-3 g-3">
        <div class="col-12">
            @include('dashboard.widgets.recent-surgeries', compact('recentSurgeries'))
        </div>
    </div>
@endsection
