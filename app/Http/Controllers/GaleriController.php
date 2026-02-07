<?php

namespace App\Http\Controllers;

use App\Models\Galeri;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GaleriController extends Controller
{
    public function index()
    {
        $this->authorize('canAccessFilament');
        
        $galeris = Galeri::orderBy('urutan', 'asc')->paginate(12);

        return Inertia::render('Admin/Galeri/Index', [
            'galeris' => $galeris,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
        ]);

        $validated['foto'] = $request->file('foto')->store('galeri', 'public');
        $validated['created_by'] = auth()->id();
        $validated['urutan'] = Galeri::max('urutan') + 1;

        Galeri::create($validated);

        return back()->with('success', 'Foto berhasil ditambahkan');
    }

    public function update(Request $request, Galeri $galeri)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'caption' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:100',
        ]);

        $galeri->update($validated);

        return back()->with('success', 'Foto berhasil diperbarui');
    }

    public function destroy(Galeri $galeri)
    {
        $this->authorize('canAccessFilament');
        $galeri->delete();

        return back()->with('success', 'Foto berhasil dihapus');
    }

    public function reorder(Request $request)
    {
        $this->authorize('canAccessFilament');
        
        $items = $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer',
        ])['items'];

        foreach ($items as $index => $id) {
            Galeri::where('id', $id)->update(['urutan' => $index]);
        }

        return response()->json(['success' => true]);
    }

    // API endpoint untuk landing page
    public function getAll()
    {
        return Galeri::orderBy('urutan', 'asc')->get();
    }
}
