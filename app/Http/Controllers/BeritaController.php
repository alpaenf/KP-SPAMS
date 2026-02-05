<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BeritaController extends Controller
{
    public function index()
    {
        $this->authorize('canAccessFilament');
        
        $beritas = Berita::with('createdBy', 'updatedBy')
            ->orderBy('tanggal_posting', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Berita/Index', [
            'beritas' => $beritas,
        ]);
    }

    public function create()
    {
        $this->authorize('canAccessFilament');
        return Inertia::render('Admin/Berita/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['judul']);
        $validated['tanggal_posting'] = now();
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('berita', 'public');
        }

        Berita::create($validated);

        return back()->with('success', 'Berita berhasil ditambahkan');
    }

    public function edit(Berita $berita)
    {
        $this->authorize('canAccessFilament');
        return Inertia::render('Admin/Berita/Edit', ['berita' => $berita]);
    }

    public function update(Request $request, Berita $berita)
    {
        $this->authorize('canAccessFilament');
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'kategori' => 'nullable|string|max:100',
            'thumbnail' => 'nullable|image|max:2048',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['judul']);
        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('berita', 'public');
        }

        $berita->update($validated);

        return back()->with('success', 'Berita berhasil diperbarui');
    }

    public function destroy(Berita $berita)
    {
        $this->authorize('canAccessFilament');
        $berita->delete();

        return back()->with('success', 'Berita berhasil dihapus');
    }

    // API endpoint untuk landing page
    public function getPublished()
    {
        return Berita::where('is_published', true)
            ->orderBy('tanggal_posting', 'desc')
            ->limit(6)
            ->get();
    }
}
