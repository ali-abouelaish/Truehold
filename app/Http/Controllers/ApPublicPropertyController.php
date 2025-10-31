<?php

namespace App\Http\Controllers;

use App\Models\ApProperty;

class ApPublicPropertyController extends Controller
{
    /**
     * Public listing of AP properties (full flats).
     */
    public function index()
    {
        $properties = ApProperty::orderByDesc('created_at')->paginate(12);
        return view('ap.index', compact('properties'));
    }

    /**
     * Public detail page for a single AP property.
     */
    public function show(ApProperty $ap_property)
    {
        return view('ap.show', ['property' => $ap_property]);
    }
}


