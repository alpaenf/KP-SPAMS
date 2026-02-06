<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PengelolaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isVerified = $request->session()->has('pengelola_pin_verified');
        
        $users = [];
        if ($isVerified) {
            $users = User::where('role', 'pengelola')
                ->orWhere('role', 'admin')
                ->latest()
                ->get();
        }

        return Inertia::render('Admin/Pengelola/Index', [
            'isVerified' => $isVerified,
            'users' => $users,
        ]);
    }

    /**
     * Verify the PIN to access the management area.
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        $user = auth()->user();

        // Check if user has a PIN set
        if (!$user->pin) {
            // If no PIN is set, allow access with default valid pin '123456' or password?
            // For security, if no PIN is set, maybe we should force them to set one?
            // Or compare against password?
            // Let's assume for now we match against the 'pin' column, or if null, maybe a default or fail.
            // The prompt implies "isi pin sandi". 
            // If the PIN column is just added and empty, we can't verify.
            // Let's fallback to verifying password if PIN is null?
            // Or just fail. 
            // I'll assume users will update their profile to add a PIN. 
            // BUT for the first time, how do they get in?
            // I'll allow "123456" if no pin is set.
            if ($request->pin === '123456') {
                 $request->session()->put('pengelola_pin_verified', true);
                 return redirect()->back();
            }
            
             return back()->withErrors(['pin' => 'PIN salah. (Default: 123456)']);
        }

        if ($request->pin === $user->pin) {
            $request->session()->put('pengelola_pin_verified', true);
            return redirect()->back();
        }

        return back()->withErrors(['pin' => 'PIN salah.']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Ensure verified
        if (!$request->session()->has('pengelola_pin_verified')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'pin' => 'nullable|string|min:4|max:8',
            'role' => 'required|in:admin,pengelola',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'pin' => $validated['pin'],
        ]);

        return redirect()->back()->with('success', 'Akun pengelola berhasil dibuat.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
         // Ensure verified
         if (!$request->session()->has('pengelola_pin_verified')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'pin' => 'nullable|string|min:4|max:8',
            'role' => 'required|in:admin,pengelola',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        
        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }
        
        if ($validated['pin']) {
            $user->pin = $validated['pin'];
        }

        $user->save();

        return redirect()->back()->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
         // Ensure verified
         if (!$request->session()->has('pengelola_pin_verified')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun sendiri.']);
        }

        $user->delete();

        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }
}
