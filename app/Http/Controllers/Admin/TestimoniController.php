<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    public function getAll()
    {
        return Testimoni::orderBy('created_at', 'desc')->get();
    }

    public function approve($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        $testimoni->update(['status' => 'approved']);
        
        return response()->json(['message' => 'Testimoni berhasil disetujui']);
    }

    public function reject($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        $testimoni->update(['status' => 'rejected']);
        
        return response()->json(['message' => 'Testimoni ditolak']);
    }

    public function destroy($id)
    {
        $testimoni = Testimoni::findOrFail($id);
        $testimoni->delete();
        
        return response()->json(['message' => 'Testimoni berhasil dihapus']);
    }
}
