<?php

namespace App\Http\Controllers;

use App\Models\Sejarah;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SejarahController extends Controller
{
    public function index()
    {
        $this->authorize('canAccessFilament');
        $sejarah = Sejarah::first() ?? new Sejarah();

        return Inertia::render('Admin/Sejarah/Index', [
            'sejarah' => $sejarah,
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'konten' => 'required|string',
            'foto' => 'nullable|image|max:5120',
        ]);

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('sejarah', 'public');
        }

        $sejarah = Sejarah::first();
        
        if ($sejarah) {
            $sejarah->update($validated);
        } else {
            Sejarah::create($validated);
        }

        return back()->with('success', 'Sejarah berhasil diperbarui');
    }

    // API endpoint untuk landing page
    public function get()
    {
        return Sejarah::first();
    }
}
