<?php

namespace App\Http\Controllers\AdminControllers;

use Illuminate\Support\Facades\Session;

class HealthReportController
{
    public function healthReport()
    {
        return view('admin.health-reports');
    }
}
