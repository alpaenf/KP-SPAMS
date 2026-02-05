<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PelangganController extends Controller
{
    public function cekPelanggan(Request $request)
    {
        $pelanggan = null;
        $error = null;

        if ($request->has('id_pelanggan')) {
            $idPelanggan = $request->input('id_pelanggan');
            
            $pelanggan = Pelanggan::where('id_pelanggan', $idPelanggan)->first();
            
            if (!$pelanggan) {
                $error = 'Data pelanggan dengan ID tersebut tidak ditemukan.';
            } else {
                // Add computed properties for frontend
                $pelanggan->has_coordinates = $pelanggan->hasCoordinates();
                $pelanggan->google_maps_link = $pelanggan->google_maps_link;
            }
        }

        return Inertia::render('CekPelanggan', [
            'pelanggan' => $pelanggan,
            'error' => $error,
        ]);
    }

    public function peta(Request $request)
    {
        // Koordinat kantor PAMSIMAS (bisa dikonfigurasi di .env atau settings)
        $kantor = [
            'name' => 'Kantor PAMSIMAS Desa',
            'lat' => -6.200000,
            'lng' => 106.816666,
        ];

        // Sumber air (contoh data, bisa dari database)
        $sumberAir = [
            [
                'name' => 'Sumber Air Utama',
                'lat' => -6.201000,
                'lng' => 106.817000,
            ],
        ];

        // Ambil semua pelanggan yang memiliki koordinat
        $pelangganList = Pelanggan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($pelanggan) {
                return [
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'nama_pelanggan' => $pelanggan->nama_pelanggan,
                    'rt' => $pelanggan->rt,
                    'rw' => $pelanggan->rw,
                    'status_aktif' => $pelanggan->status_aktif,
                    'latitude' => (float) $pelanggan->latitude,
                    'longitude' => (float) $pelanggan->longitude,
                ];
            });

        // Jika ada parameter pelanggan, highlight pelanggan tersebut
        $highlightPelanggan = $request->query('pelanggan');

        return Inertia::render('Peta', [
            'kantor' => $kantor,
            'sumberAir' => $sumberAir,
            'pelangganList' => $pelangganList,
            'highlightPelanggan' => $highlightPelanggan,
        ]);
    }
}
