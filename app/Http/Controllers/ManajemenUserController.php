<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ManajemenUserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'wilayah', 'pin', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('ManajemenUser/Index', [
            'users' => $users,
            'wilayahOptions' => [
                'dawuhan' => 'Dawuhan',
                'kubangsari_kulon' => 'Kubangsari Kulon',
                'kubangsari_wetan' => 'Kubangsari Wetan',
                'sokarame' => 'Sokarame',
                'tiparjaya' => 'Tiparjaya',
            ],
        ]);
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,penarik',
            'wilayah' => 'nullable|in:dawuhan,kubangsari_kulon,kubangsari_wetan,sokarame,tiparjaya',
            'pin' => 'nullable|string|max:10',
        ]);

        // Validasi: Jika role penarik, wilayah wajib diisi
        if ($validated['role'] === 'penarik' && empty($validated['wilayah'])) {
            return back()->withErrors(['wilayah' => 'Wilayah wajib diisi untuk role penarik']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'wilayah' => $validated['wilayah'],
            'pin' => $validated['pin'] ? Hash::make($validated['pin']) : null,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,penarik',
            'wilayah' => 'nullable|in:dawuhan,kubangsari_kulon,kubangsari_wetan,sokarame,tiparjaya',
            'pin' => 'nullable|string|max:10',
        ]);

        // Validasi: Jika role penarik, wilayah wajib diisi
        if ($validated['role'] === 'penarik' && empty($validated['wilayah'])) {
            return back()->withErrors(['wilayah' => 'Wilayah wajib diisi untuk role penarik']);
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'wilayah' => $validated['wilayah'],
        ];

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Update PIN jika diisi
        if (!empty($validated['pin'])) {
            $updateData['pin'] = Hash::make($validated['pin']);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri']);
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus');
    }
}
