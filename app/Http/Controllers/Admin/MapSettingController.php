<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MapSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MapSettingController extends Controller
{
    public function index()
    {
        $mapSettings = MapSetting::orderBy('location_type')
            ->orderBy('created_at')
            ->get();
        
        return Inertia::render('Admin/MapSettings/Index', [
            'mapSettings' => $mapSettings,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_type' => 'required|in:kp_spams,sumber_air',
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        MapSetting::create($validated);

        return redirect()->route('admin.map-settings')
            ->with('success', 'Lokasi peta berhasil ditambahkan');
    }

    public function update(Request $request, MapSetting $mapSetting)
    {
        $validated = $request->validate([
            'location_type' => 'required|in:kp_spams,sumber_air',
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $mapSetting->update($validated);

        return redirect()->route('admin.map-settings')
            ->with('success', 'Lokasi peta berhasil diperbarui');
    }

    public function destroy(MapSetting $mapSetting)
    {
        $mapSetting->delete();

        return redirect()->route('admin.map-settings')
            ->with('success', 'Lokasi peta berhasil dihapus');
    }
}
