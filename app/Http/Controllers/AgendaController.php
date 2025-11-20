<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendaIndexRequest;
use App\Services\AgendaService;
use Illuminate\Contracts\View\View;

class AgendaController extends Controller
{
    public function __construct(private readonly AgendaService $agendaService)
    {
    }

    public function index(AgendaIndexRequest $request): View
    {
        $filters = $this->agendaService->resolveFilters($request->validated());
        $agendaData = $this->agendaService->getAgendaIndexData($filters);

        return view('agenda.index', [
            'filters' => $filters,
            'procedures' => $agendaData['procedures'],
            'availableStates' => $agendaData['availableStates'],
            'availableDoctors' => $agendaData['availableDoctors'],
            'availableLocations' => $agendaData['availableLocations'],
        ]);
    }

    public function show(int $visitId): View
    {
        $visitData = $this->agendaService->getVisitViewData($visitId);

        if (! $visitData) {
            return view('agenda.visit-not-found');
        }

        return view('agenda.visit', [
            'visit' => $visitData['visit'],
            'identityVerification' => $visitData['identityVerification'],
        ]);
    }
}
