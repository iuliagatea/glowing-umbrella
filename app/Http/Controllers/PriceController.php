<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;

class PriceController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::with(['services' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('prices', compact('categories'));
    }
}
