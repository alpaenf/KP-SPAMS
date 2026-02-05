<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformasiTarif;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InformasiTarifController extends Controller
{
    public function index()
    {
        $tarifs = InformasiTarif::orderBy('urutan')->orderBy('created_at', 'desc')->get();
        
        return Inertia::render('Admin/InformasiTarif/Index', [
            'tarifs' => $tarifs
        ]);
    }

    public function getActive()
    {
        return InformasiTarif::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['satuan'] = $validated['satuan'] ?? 'm³';
        $validated['kategori'] = $validated['kategori'] ?? 'tarif';
        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        InformasiTarif::create($validated);

        return redirect()->back()->with('success', 'Informasi tarif berhasil ditambahkan');
    }

    public function update(Request $request, InformasiTarif $informasiTarif)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'nullable|string|max:50',
            'kategori' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $informasiTarif->update($validated);

        return redirect()->back()->with('success', 'Informasi tarif berhasil diperbarui');
    }

    public function destroy(InformasiTarif $informasiTarif)
    {
        $informasiTarif->delete();

        return redirect()->back()->with('success', 'Informasi tarif berhasil dihapus');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'tarifs' => 'required|array',
            'tarifs.*.id' => 'required|exists:informasi_tarifs,id',
            'tarifs.*.urutan' => 'required|integer|min:0'
        ]);

        foreach ($request->tarifs as $item) {
            InformasiTarif::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
        }

        return redirect()->back()->with('success', 'Urutan tarif berhasil diperbarui');
    }
}
