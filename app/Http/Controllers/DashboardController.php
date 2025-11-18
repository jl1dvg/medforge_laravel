<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Simple placeholder dashboard while legacy modules are migrated.
     */
    public function __invoke(): View
    {
        return view('dashboard.index');
    }
}
