<?php

namespace App\Http\Controllers;

use App\Models\TagihanBulanan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TagihanBulananController extends Controller
{
    /**
     * Halaman input meteran bulanan untuk pengelola
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        
        // Ambil semua pelanggan aktif dengan tagihan bulan ini (jika ada)
        // Apply filter wilayah berdasarkan user yang login
        $pelangganList = Pelanggan::forUser()
            ->where('status_aktif', true)
            ->with(['tagihanBulanan' => function ($query) use ($bulan) {
                $query->where('bulan', $bulan);
            }])
            ->with(['pembayarans' => function ($query) use ($bulan) {
                $query->where('bulan_bayar', $bulan);
            }])
            ->orderBy('id_pelanggan')
            ->get()
            ->map(function ($p) use ($bulan) {
                // Cek apakah sudah ada tagihan bulan ini
                $tagihan = $p->tagihanBulanan->firstWhere('bulan', $bulan);
                
                // Cek pembayaran bulan ini
                $pembayaran = $p->pembayarans->firstWhere('bulan_bayar', $bulan);
                
                // Cek tunggakan bulan sebelumnya
                $tunggakan = $this->getTunggakanSebelumnya($p->id, $bulan);
                
                return [
                    'id' => $p->id,
                    'id_pelanggan' => $p->id_pelanggan,
                    'nama_pelanggan' => $p->nama_pelanggan,
                    'kategori' => $p->kategori ?? 'umum',
                    'wilayah' => $p->wilayah,
                    'rt' => $p->rt,
                    'rw' => $p->rw,
                    'tagihan' => $tagihan ? [
                        'id' => $tagihan->id,
                        'meteran_sebelum' => $tagihan->meteran_sebelum,
                        'meteran_sesudah' => $tagihan->meteran_sesudah,
                        'pemakaian_kubik' => $tagihan->pemakaian_kubik,
                        'tarif_per_kubik' => $tagihan->tarif_per_kubik,
                        'ada_abunemen' => $tagihan->ada_abunemen,
                        'biaya_abunemen' => $tagihan->biaya_abunemen,
                        'total_tagihan' => $tagihan->total_tagihan,
                        'jumlah_terbayar' => $tagihan->jumlah_terbayar,
                        'sisa_tagihan' => $tagihan->sisa_tagihan,
                        'status_bayar' => $tagihan->status_bayar,
                        'status_konfirmasi' => $tagihan->status_konfirmasi,
                        'bukti_transfer' => $tagihan->bukti_transfer,
                        'konfirmasi_at' => $tagihan->konfirmasi_at,
                        'catatan_admin' => $tagihan->catatan_admin,
                    ] : null,
                    'tunggakan' => $tunggakan,
                ];
            });
        
        // Ambil pembayaran bulan ini
        // Apply filter wilayah untuk role penarik
        $user = $request->user();
        $query = Pembayaran::with('pelanggan')
            ->where('bulan_bayar', $bulan);
        
        // Filter by penarik wilayah if role is penarik
        if ($user && $user->role === 'penarik' && $user->wilayah) {
            $query->whereHas('pelanggan', function ($q) use ($user) {
                $q->where('wilayah', $user->wilayah);
            });
        }
        
        $pembayaranList = $query->orderBy('tanggal_bayar', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'pelanggan' => [
                        'id' => $p->pelanggan->id,
                        'id_pelanggan' => $p->pelanggan->id_pelanggan,
                        'nama_pelanggan' => $p->pelanggan->nama_pelanggan,
                        'wilayah' => $p->pelanggan->wilayah,
                    ],
                    'bulan_bayar' => $p->bulan_bayar,
                    'tanggal_bayar' => $p->tanggal_bayar,
                    'meteran_sebelum' => $p->meteran_sebelum,
                    'meteran_sesudah' => $p->meteran_sesudah,
                    'jumlah_kubik' => $p->jumlah_kubik,
                    'abunemen' => $p->abunemen,
                    'metode_bayar' => $p->metode_bayar ?? 'Tunai',
                    'jumlah_bayar' => $p->jumlah_bayar,
                    'keterangan' => $p->keterangan,
                    'catatan' => $p->catatan,
                ];
            });
        
        return Inertia::render('TagihanBulanan/Index', [
            'pelangganList' => $pelangganList,
            'pembayaranList' => $pembayaranList,
            'bulan' => $bulan,
        ]);
    }
    
    /**
     * Simpan/Update tagihan untuk satu pelanggan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'bulan' => 'required|string|max:7',
            'meteran_sebelum' => 'required|numeric|min:0',
            'meteran_sesudah' => 'required|numeric|min:0',
            'tarif_per_kubik' => 'nullable|numeric|min:0',
            'ada_abunemen' => 'nullable|boolean',
            'biaya_abunemen' => 'nullable|numeric|min:0',
        ]);
        
        // Cek pelanggan kategori
        $pelanggan = Pelanggan::findOrFail($validated['pelanggan_id']);
        
        // Set default values jika tidak ada di request
        if (!isset($validated['tarif_per_kubik'])) {
            $validated['tarif_per_kubik'] = 2000; // Default tarif per kubik
        }
        if (!isset($validated['ada_abunemen'])) {
            $validated['ada_abunemen'] = true; // Default ada abunemen
        }
        if (!isset($validated['biaya_abunemen'])) {
            $validated['biaya_abunemen'] = 3000; // Default biaya abunemen
        }
        
        // Jika kategori sosial, set total tagihan = 0 dan status SUDAH_BAYAR
        if ($pelanggan->kategori === 'sosial') {
            $validated['total_tagihan'] = 0;
            $validated['ada_abunemen'] = false;
            $validated['tarif_per_kubik'] = 0;
            $validated['status_bayar'] = 'SUDAH_BAYAR';
        }
        
        // Update or Create tagihan
        $tagihan = TagihanBulanan::updateOrCreate(
            [
                'pelanggan_id' => $validated['pelanggan_id'],
                'bulan' => $validated['bulan'],
            ],
            $validated
        );
        
        return response()->json([
            'message' => 'Tagihan berhasil disimpan',
            'tagihan' => [
                'id' => $tagihan->id,
                'meteran_sebelum' => $tagihan->meteran_sebelum,
                'meteran_sesudah' => $tagihan->meteran_sesudah,
                'pemakaian_kubik' => $tagihan->pemakaian_kubik,
                'tarif_per_kubik' => $tagihan->tarif_per_kubik,
                'ada_abunemen' => $tagihan->ada_abunemen,
                'biaya_abunemen' => $tagihan->biaya_abunemen,
                'total_tagihan' => $tagihan->total_tagihan,
                'status_bayar' => $tagihan->status_bayar,
            ],
        ]);
    }
    
    /**
     * Generate tagihan untuk semua pelanggan aktif sekaligus
     */
    public function generateBulk(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|string|max:7',
            'tarif_per_kubik' => 'required|numeric|min:0',
            'ada_abunemen' => 'required|boolean',
            'biaya_abunemen' => 'required|numeric|min:0',
            'masukkan_tunggakan' => 'nullable|boolean',
        ]);
        
        $bulan = $validated['bulan'];
        $masukkanTunggakan = $validated['masukkan_tunggakan'] ?? false;
        
        // Ambil semua pelanggan aktif
        // Apply filter wilayah berdasarkan user yang login
        $pelanggans = Pelanggan::forUser()->where('status_aktif', true)->get();
        
        $created = 0;
        $skipped = 0;
        
        foreach ($pelanggans as $pelanggan) {
            // Cek apakah sudah ada tagihan untuk bulan ini
            $exists = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
                ->where('bulan', $bulan)
                ->exists();
            
            if ($exists) {
                $skipped++;
                continue;
            }
            
            // PERBAIKAN: Cek apakah pelanggan sudah terdaftar sebelum bulan tagihan ini
            // Pelanggan hanya ditagih mulai bulan setelah pendaftaran
            $tanggalDaftar = $pelanggan->created_at;
            $bulanDaftar = $tanggalDaftar->format('Y-m');
            
            // Jika bulan tagihan <= bulan pendaftaran, skip (pelanggan belum aktif di bulan itu)
            if ($bulan <= $bulanDaftar) {
                $skipped++;
                continue;
            }
            
            // Hitung tunggakan jika diminta
            $totalTunggakan = 0;
            if ($masukkanTunggakan) {
                $totalTunggakan = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
                    ->where('bulan', '<', $bulan)
                    ->where('status_bayar', 'BELUM_BAYAR')
                    ->sum('total_tagihan');
            }
            
            // Buat tagihan baru
            TagihanBulanan::create([
                'pelanggan_id' => $pelanggan->id,
                'bulan' => $bulan,
                'meteran_sebelum' => null,
                'meteran_sesudah' => null,
                'tarif_per_kubik' => $pelanggan->kategori === 'sosial' ? 0 : $validated['tarif_per_kubik'],
                'ada_abunemen' => $pelanggan->kategori === 'sosial' ? false : $validated['ada_abunemen'],
                'biaya_abunemen' => $pelanggan->kategori === 'sosial' ? 0 : $validated['biaya_abunemen'],
                'total_tagihan' => $totalTunggakan, // Akan ditambah lagi setelah meteran diisi
                'status_bayar' => $pelanggan->kategori === 'sosial' ? 'SUDAH_BAYAR' : 'BELUM_BAYAR',
            ]);
            
            $created++;
        }
        
        return response()->json([
            'message' => "Berhasil generate {$created} tagihan, {$skipped} sudah ada sebelumnya",
            'created' => $created,
            'skipped' => $skipped,
        ]);
    }

    public function approveKonfirmasi($id)
    {
        $tagihan = TagihanBulanan::findOrFail($id);

        // Buat pembayaran otomatis dengan data meteran dari tagihan
        Pembayaran::create([
            'pelanggan_id' => $tagihan->pelanggan_id,
            'bulan_bayar' => $tagihan->bulan,
            'tanggal_bayar' => now(),
            'meteran_sebelum' => $tagihan->meteran_sebelum,
            'meteran_sesudah' => $tagihan->meteran_sesudah,
            'jumlah_kubik' => $tagihan->pemakaian_kubik,
            'abunemen' => $tagihan->ada_abunemen,
            'tunggakan' => $tagihan->tunggakan ?? 0,
            'jumlah_bayar' => $tagihan->total_tagihan,
            'keterangan' => 'Pembayaran via konfirmasi QRIS/Transfer',
        ]);

        // Update status tagihan
        $tagihan->status_bayar = 'SUDAH_BAYAR';
        $tagihan->status_konfirmasi = 'approved';
        $tagihan->save();

        return response()->json([
            'message' => 'Konfirmasi pembayaran berhasil di-approve'
        ]);
    }

    public function rejectKonfirmasi(Request $request, $id)
    {
        $tagihan = TagihanBulanan::findOrFail($id);

        $tagihan->status_konfirmasi = 'rejected';
        $tagihan->catatan_admin = $request->input('catatan');
        $tagihan->save();

        return response()->json([
            'message' => 'Konfirmasi pembayaran ditolak'
        ]);
    }
    
    /**
     * Get tunggakan dari bulan-bulan sebelumnya yang belum dibayar
     */
    private function getTunggakanSebelumnya($pelangganId, $bulanSekarang)
    {
        // Ambil semua tagihan yang belum lunas sebelum bulan ini
        $tunggakan = TagihanBulanan::where('pelanggan_id', $pelangganId)
            ->where('bulan', '<', $bulanSekarang)
            ->where('status_bayar', 'BELUM_BAYAR')
            ->orderBy('bulan', 'asc')
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'bulan' => $t->bulan,
                    'total_tagihan' => $t->total_tagihan,
                ];
            });
            
        return [
            'jumlah' => $tunggakan->count(),
            'total' => $tunggakan->sum('total_tagihan'),
            'detail' => $tunggakan->toArray(),
        ];
    }
    
    /**
     * Get tagihan by pelanggan ID and bulan for payment modal
     */
    public function getByPelangganBulan($pelangganId, $bulan)
    {
        $tagihan = TagihanBulanan::where('pelanggan_id', $pelangganId)
            ->where('bulan', $bulan)
            ->first();
        
        if ($tagihan) {
            return response()->json([
                'tagihan' => [
                    'id' => $tagihan->id,
                    'meteran_sebelum' => $tagihan->meteran_sebelum ?? 0,
                    'meteran_sesudah' => $tagihan->meteran_sesudah ?? 0,
                    'pemakaian_kubik' => $tagihan->pemakaian_kubik ?? 0,
                    'total_tagihan' => $tagihan->total_tagihan ?? 0,
                    'jumlah_terbayar' => $tagihan->jumlah_terbayar ?? 0,
                    'ada_abunemen' => $tagihan->ada_abunemen ?? false,
                    'biaya_abunemen' => $tagihan->biaya_abunemen ?? 0,
                    'status_bayar' => $tagihan->status_bayar,
                ],
            ]);
        }
        
        // Jika tidak ada tagihan, cek pembayaran bulan tersebut
        // Ini penting untuk data lama yang tidak punya record di tagihan_bulanan
        $pembayaran = Pembayaran::where('pelanggan_id', $pelangganId)
            ->where('bulan_bayar', $bulan)
            ->first();
        
        if ($pembayaran) {
            return response()->json([
                'tagihan' => [
                    'id' => null,
                    'meteran_sebelum' => $pembayaran->meteran_sebelum ?? 0,
                    'meteran_sesudah' => $pembayaran->meteran_sesudah ?? 0,
                    'pemakaian_kubik' => $pembayaran->jumlah_kubik ?? 0,
                    'total_tagihan' => $pembayaran->jumlah_bayar ?? 0,
                    'ada_abunemen' => $pembayaran->abunemen ?? false,
                    'biaya_abunemen' => $pembayaran->abunemen ? 3000 : 0,
                    'status_bayar' => 'SUDAH_BAYAR',
                ],
            ]);
        }
        
        return response()->json([
            'message' => 'Tagihan tidak ditemukan untuk bulan ini',
            'tagihan' => null,
        ], 404);
    }
    
    /**
     * Get list tunggakan yang belum lunas untuk pelanggan
     * API endpoint untuk cicilan tunggakan
     */
    public function getTunggakanByPelanggan($pelangganId)
    {
        try {
            $tunggakan = TagihanBulanan::where('pelanggan_id', $pelangganId)
                ->where('status_bayar', 'BELUM_BAYAR')
                ->orderBy('bulan', 'asc')
                ->get()
                ->map(function($t) {
                    $sisaTagihan = $t->total_tagihan - $t->jumlah_terbayar;
                    return [
                        'id' => $t->id,
                        'bulan' => $t->bulan,
                        'total_tagihan' => $t->total_tagihan,
                        'jumlah_terbayar' => $t->jumlah_terbayar,
                        'sisa_tagihan' => $sisaTagihan,
                    ];
                })
                ->filter(function($t) {
                    // Only show tunggakan with sisa > 0
                    return $t['sisa_tagihan'] > 0;
                });
            
            return response()->json([
                'tunggakan' => $tunggakan->values()->toArray(),
                'total_tunggakan' => $tunggakan->sum('sisa_tagihan'),
                'jumlah_bulan' => $tunggakan->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching tunggakan: ' . $e->getMessage(),
                'tunggakan' => [],
            ], 500);
        }
    }
}
