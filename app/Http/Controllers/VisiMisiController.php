<?php

namespace App\Http\Controllers;

use App\Models\VisiMisi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisiMisiController extends Controller
{
    public function index()
    {
        $this->authorize('canAccessFilament');
        $visiMisi = VisiMisi::first() ?? new VisiMisi();

        return Inertia::render('Admin/VisiMisi/Index', [
            'visiMisi' => $visiMisi,
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'visi' => 'required|string',
            'misi' => 'required|array',
            'misi.*' => 'required|string',
        ]);

        // Filter out empty misi values
        $validated['misi'] = array_values(array_filter($validated['misi'], function($item) {
            return !empty(trim($item));
        }));

        $validated['updated_by'] = auth()->id();

        $visiMisi = VisiMisi::first();
        
        if ($visiMisi) {
            $visiMisi->update($validated);
        } else {
            VisiMisi::create($validated);
        }

        return back()->with('success', 'Visi Misi berhasil diperbarui');
    }

    // API endpoint untuk landing page
    public function get()
    {
        return VisiMisi::first();
    }
}
