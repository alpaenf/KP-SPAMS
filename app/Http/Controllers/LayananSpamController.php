<?php

namespace App\Http\Controllers;

use App\Models\LayananSpam;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LayananSpamController extends Controller
{
    public function index()
    {
        $this->authorize('canAccessFilament');
        
        $layanans = LayananSpam::orderBy('urutan', 'asc')->paginate(12);

        return Inertia::render('Admin/Layanan/Index', [
            'layanans' => $layanans,
        ]);
    }

    public function create()
    {
        $this->authorize('canAccessFilament');
        return Inertia::render('Admin/Layanan/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'icon' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['urutan'] = LayananSpam::max('urutan') + 1;

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('layanan', 'public');
        }

        LayananSpam::create($validated);

        return back()->with('success', 'Layanan berhasil ditambahkan');
    }

    public function edit(LayananSpam $layanan)
    {
        $this->authorize('canAccessFilament');
        return Inertia::render('Admin/Layanan/Edit', ['layanan' => $layanan]);
    }

    public function update(Request $request, LayananSpam $layanan)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'icon' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('layanan', 'public');
        }

        $layanan->update($validated);

        return back()->with('success', 'Layanan berhasil diperbarui');
    }

    public function destroy(LayananSpam $layanan)
    {
        $this->authorize('canAccessFilament');
        $layanan->delete();

        return back()->with('success', 'Layanan berhasil dihapus');
    }

    public function reorder(Request $request)
    {
        $this->authorize('canAccessFilament');
        
        $items = $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|integer',
        ])['items'];

        foreach ($items as $index => $id) {
            LayananSpam::where('id', $id)->update(['urutan' => $index]);
        }

        return response()->json(['success' => true]);
    }

    // API endpoint untuk landing page
    public function getAll()
    {
        return LayananSpam::orderBy('urutan', 'asc')->get();
    }
}
