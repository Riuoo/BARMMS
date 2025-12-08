<?php

namespace App\Http\Controllers\AdminControllers\Settings;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = Faq::query();
        if ($request->filled('category')) $q->where('category', $request->category);
        if ($request->filled('search')) {
            $search = $request->search;
            $q->where(function($sub) use ($search) {
                $sub->where('question','like',"%$search%")
                    ->orWhere('answer','like',"%$search%");
            });
        }
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $q->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $q->where('is_active', false);
            }
        }
        $faqs = $q->orderBy('category')->orderBy('order')->get();
        $categories = Faq::distinct()->pluck('category');
        return view('admin.settings.faqs.index', compact('faqs', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        $categories = Faq::distinct()->pluck('category');
        return view('admin.settings.faqs.form', [ 'faq' => new Faq, 'categories' => $categories, 'mode' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            $data = $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'category' => 'required|string|max:100',
                'order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
            ]);
            
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            
            if(!isset($data['order']) || !$data['order']) {
                $max = Faq::where('category',$data['category'])->max('order');
                $data['order'] = $max ? $max+1 : 1;
            }
            
            Faq::create($data);
            return redirect()->route('admin.faqs.index')->with('success','FAQ created successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create FAQ: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq) {
        $categories = Faq::distinct()->pluck('category');
        return view('admin.settings.faqs.form', compact('faq','categories'))->with('mode','edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq) {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $faq->update($data);
        return redirect()->route('admin.faqs.index')->with('success','FAQ updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq) {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success','FAQ deleted!');
    }

    public function toggle(Faq $faq) {
        $faq->is_active = !$faq->is_active;
        $faq->save();
        return response()->json(['success'=>true, 'is_active' => $faq->is_active]);
    }

    public function reorder(Request $request) {
        $ids = $request->input('ids'); // expects ordered array of FAQ IDs
        foreach($ids as $i=>$id) {
            Faq::where('id',$id)->update(['order'=>$i+1]);
        }
        return response()->json(['success'=>true]);
    }
}
