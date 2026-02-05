<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('urutan')->orderBy('created_at', 'desc')->get();
        
        return Inertia::render('Admin/Faqs/Index', [
            'faqs' => $faqs
        ]);
    }

    public function getActive()
    {
        return Faq::where('is_active', true)
            ->orderBy('urutan')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        Faq::create($validated);

        return redirect()->back()->with('success', 'FAQ berhasil ditambahkan');
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $faq->update($validated);

        return redirect()->back()->with('success', 'FAQ berhasil diperbarui');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ berhasil dihapus');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'faqs' => 'required|array',
            'faqs.*.id' => 'required|exists:faqs,id',
            'faqs.*.urutan' => 'required|integer|min:0'
        ]);

        foreach ($request->faqs as $item) {
            Faq::where('id', $item['id'])->update(['urutan' => $item['urutan']]);
        }

        return redirect()->back()->with('success', 'Urutan FAQ berhasil diperbarui');
    }
}
