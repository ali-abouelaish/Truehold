<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProperties = Property::count();
        $availableProperties = Property::where('status', 'available')->count();
        $rentedProperties = Property::where('status', 'rented')->count();
        $unavailableProperties = Property::where('status', 'unavailable')->count();
        $onHoldProperties = Property::where('status', 'on_hold')->count();
        
        $recentProperties = Property::latest()->take(10)->get();
        
        return view('dashboard', compact(
            'totalProperties',
            'availableProperties', 
            'rentedProperties',
            'unavailableProperties',
            'onHoldProperties',
            'recentProperties'
        ));
    }
}
