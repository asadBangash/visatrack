<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::query()->orderBy('display_name')->paginate(25);

        return view('admin.locations.index', compact('locations'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'is_active' => ['required', 'in:0,1'],
        ]);

        $location->update(['is_active' => (bool) $request->input('is_active')]);

        return back()->with('success', 'Location updated.');
    }
}