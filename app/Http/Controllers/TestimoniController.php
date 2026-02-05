<?php

namespace App\Http\Controllers;

use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'pekerjaan' => 'nullable|string|max:255',
            'isi_testimoni' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Testimoni::create([
            'nama' => $request->nama,
            'pekerjaan' => $request->pekerjaan,
            'isi_testimoni' => $request->isi_testimoni,
            'rating' => $request->rating,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Terima kasih! Testimoni Anda sedang menunggu persetujuan.');
    }

    public function getApproved()
    {
        return Testimoni::approved()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}
