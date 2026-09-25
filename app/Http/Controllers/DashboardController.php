<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected readonly DashboardService $dashboard,
    ) {}

    public function index(): View
    {
        $data = $this->dashboard->overview(auth()->user());

        return view('dashboard.index', $data);
    }
}
