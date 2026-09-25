<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AIHistory::query()->with('user');

        if (filled($request->query('feature'))) {
            $query->ofFeature($request->query('feature'));
        }

        if ($request->query('status') === 'failed') {
            $query->failed();
        }

        $logs = $query->latest()->paginate(25);

        return view('admin.logs.index', [
            'logs' => $logs,
            'features' => AIHistory::query()->distinct()->pluck('feature'),
            'featureFilter' => $request->query('feature', ''),
            'statusFilter' => $request->query('status', ''),
        ]);
    }
}
