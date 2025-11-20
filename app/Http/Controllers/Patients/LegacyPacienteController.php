<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Pacientes\PacienteController as BasePacienteController;
use App\Services\Pacientes\PacienteService;

class LegacyPacienteController extends BasePacienteController
{
    public function __construct(PacienteService $service)
    {
        parent::__construct($service);
    }
}
