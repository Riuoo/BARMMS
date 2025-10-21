<?php

namespace App\Http\Controllers\ResidentControllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class ResidentFaqController
{
    public function index(Request $request)
    {
        $query = Faq::where('is_active', true);

        // Category filtering
        $categories = Faq::where('is_active', true)->distinct()->pluck('category');
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%$search%")
                  ->orWhere('answer', 'like', "%$search%")
                  ->orWhere('category', 'like', "%$search%") ;
            });
        }
        $faqs = $query->orderBy('category')->orderBy('order')->get();
        return view('resident.faqs', compact('faqs','categories'));
    }
}
