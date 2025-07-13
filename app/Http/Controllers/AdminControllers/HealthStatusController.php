<?php

namespace App\Http\Controllers\AdminControllers;

use Illuminate\Support\Facades\Session;

class HealthStatusController
{
    public function healthStatus()
    {
        return view('admin.health.health-status');
    }
}
