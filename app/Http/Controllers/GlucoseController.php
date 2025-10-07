<?php

namespace App\Http\Controllers;

use App\Models\Glucose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GlucoseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $glucoses = Glucose::orderBy('measurement_datetime', 'desc')->get();
        return view('glucose.index', compact('glucoses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric|min:0|max:1000',
            'note' => 'nullable|string|max:255',
            'is_hungry' => 'boolean',
            'measurement_datetime' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Glucose::create($request->all());

        return redirect()->route('glucose.index')
            ->with('success', 'Kan şekeri ölçümü başarıyla kaydedildi.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $glucose = Glucose::findOrFail($id);
        return view('glucose.show', compact('glucose'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $glucose = Glucose::findOrFail($id);
        return view('glucose.edit', compact('glucose'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $glucose = Glucose::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric|min:0|max:1000',
            'note' => 'nullable|string|max:255',
            'is_hungry' => 'boolean',
            'measurement_datetime' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $glucose->update($request->all());

        return redirect()->route('glucose.index')
            ->with('success', 'Kan şekeri ölçümü başarıyla güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $glucose = Glucose::findOrFail($id);
        $glucose->delete();

        return redirect()->route('glucose.index')
            ->with('success', 'Kan şekeri ölçümü başarıyla silindi.');
    }
}
