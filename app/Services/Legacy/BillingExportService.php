<?php

namespace App\Services\Legacy;

use Illuminate\Database\ConnectionInterface;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class BillingExportService
{
    public function __construct(private readonly ConnectionInterface $connection)
    {
    }

    public function exportIndividual(string $formId, ?string $grupoAfiliacion = null): never
    {
        throw new ServiceUnavailableHttpException(null, 'La exportación legacy de facturación aún no está disponible.');
    }

    public function exportByMonth(string $month, ?string $grupoAfiliacion = null): never
    {
        throw new ServiceUnavailableHttpException(null, 'La exportación consolidada por mes aún no está disponible.');
    }
}
