<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisaApplication;
use App\Models\VisaStatusHistory;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => VisaApplication::query()->count(),
            'received' => VisaApplication::query()->where('status', VisaApplication::STATUS_APPLICATION_RECEIVED)->count(),
            'approved' => VisaApplication::query()->where('status', VisaApplication::STATUS_APPROVED)->count(),
            'issued' => VisaApplication::query()->where('status', VisaApplication::STATUS_ISSUED)->count(),
        ];

        $recentApplications = VisaApplication::query()
            ->with('location')
            ->latest()
            ->limit(8)
            ->get();

        $recentStatusChanges = VisaStatusHistory::query()
            ->with(['application', 'changer'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'recentStatusChanges'));
    }
}