<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaStatusHistory;

class StatusHistoryController extends Controller
{
    public function index()
    {
        $histories = VisaStatusHistory::query()
            ->with(['application', 'changer'])
            ->latest('created_at')
            ->paginate(20);

        return view('admin.status-histories.index', compact('histories'));
    }
}