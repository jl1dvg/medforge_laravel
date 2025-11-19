<?php
require_once __DIR__ . '/../../bootstrap.php';

use medforge\modules\Billing\Controllers\BillingController;

$controller = new BillingController($pdo);
$controller->index();
