<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class PembayaranController extends Controller
{
    /**
     * Halaman riwayat pembayaran semua pelanggan
     */
    public function riwayat(Request $request)
    {
        $user = $request->user();
        
        // Base query
        $query = Pembayaran::with('pelanggan');
        
        // Filter by penarik wilayah if role is penarik
        if ($user && $user->role === 'penarik' && $user->wilayah) {
            $query->whereHas('pelanggan', function ($q) use ($user) {
                $q->where('wilayah', $user->wilayah);
            });
        }
        
        // Get all pembayaran with pelanggan data
        $pembayaranList = $query->orderBy('tanggal_bayar', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'id_pelanggan' => $p->pelanggan->id_pelanggan,
                    'nama_pelanggan' => $p->pelanggan->nama_pelanggan,
                    'wilayah' => $p->pelanggan->wilayah,
                    'kategori' => $p->pelanggan->kategori,
                    'bulan_bayar' => $p->bulan_bayar,
                    'tanggal_bayar' => $p->tanggal_bayar->format('Y-m-d'),
                    'meteran_sebelum' => $p->meteran_sebelum,
                    'meteran_sesudah' => $p->meteran_sesudah,
                    'jumlah_kubik' => $p->jumlah_kubik,
                    'abunemen' => $p->abunemen,
                    'tunggakan' => $p->tunggakan,
                    'jumlah_bayar' => $p->jumlah_bayar,
                    'keterangan' => $p->keterangan,
                ];
            });
        
        return Inertia::render('Pembayaran/Index', [
            'pembayaranList' => $pembayaranList,
        ]);
    }
    
    /**
     * Halaman pembayaran untuk admin
     */
    public function adminIndex(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        
        // Ambil semua pembayaran bulan ini dengan data pelanggan
        $pembayaranList = Pembayaran::with('pelanggan')
            ->where('bulan_bayar', $bulan)
            ->orderBy('tanggal_bayar', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'pelanggan' => [
                        'id' => $p->pelanggan->id,
                        'id_pelanggan' => $p->pelanggan->id_pelanggan,
                        'nama_pelanggan' => $p->pelanggan->nama_pelanggan,
                        'wilayah' => $p->pelanggan->wilayah,
                        'rt' => $p->pelanggan->rt,
                        'rw' => $p->pelanggan->rw,
                    ],
                    'bulan_bayar' => $p->bulan_bayar,
                    'tanggal_bayar' => $p->tanggal_bayar->format('Y-m-d'),
                    'jumlah_bayar' => $p->jumlah_bayar,
                    'keterangan' => $p->keterangan,
                    'meteran_sebelum' => $p->meteran_sebelum,
                    'meteran_sesudah' => $p->meteran_sesudah,
                    'jumlah_kubik' => $p->jumlah_kubik,
                ];
            });
        
        return Inertia::render('PembayaranAdmin/Index', [
            'pembayaranList' => $pembayaranList,
            'bulan' => $bulan,
        ]);
    }
    
    public function index($pelangganId)
    {
        $pelanggan = Pelanggan::findOrFail($pelangganId);
        $pembayarans = Pembayaran::where('pelanggan_id', $pelangganId)
            ->orderBy('bulan_bayar', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'bulan_bayar' => $p->bulan_bayar,
                    'tanggal_bayar' => $p->tanggal_bayar->format('Y-m-d'),
                    'meteran_sebelum' => $p->meteran_sebelum,
                    'meteran_sesudah' => $p->meteran_sesudah,
                    'abunemen' => $p->abunemen,
                    'tunggakan' => $p->tunggakan,
                    'jumlah_kubik' => $p->jumlah_kubik,
                    'jumlah_bayar' => $p->jumlah_bayar,
                    'keterangan' => $p->keterangan,
                ];
            });
        
        return response()->json([
            'pelanggan' => [
                'id' => $pelanggan->id,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
            ],
            'pembayarans' => $pembayarans,
        ]);
    }

    public function store(Request $request, $pelangganId)
    {
        $validated = $request->validate([
            'bulan_bayar' => 'required|string|max:7',
            'tanggal_bayar' => 'required|date',
            'meteran_sebelum' => 'nullable|numeric|min:0',
            'meteran_sesudah' => 'nullable|numeric|min:0',
            'abunemen' => 'nullable|boolean',
            'tunggakan' => 'nullable|numeric|min:0',
            'jumlah_kubik' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'keterangan' => 'required|string|in:LUNAS,TUNGGAKAN,CICILAN',
            'bayar_tunggakan' => 'nullable|boolean',
            'id_tunggakan' => 'nullable|array',
            'id_tunggakan.*' => 'integer|exists:tagihan_bulanan,id',
        ]);

        $pelanggan = Pelanggan::findOrFail($pelangganId);
        
        // Cek apakah sudah ada pembayaran untuk bulan ini
        $existing = Pembayaran::where('pelanggan_id', $pelangganId)
            ->where('bulan_bayar', $validated['bulan_bayar'])
            ->first();
        
        if ($existing) {
            return response()->json([
                'message' => 'Pembayaran untuk bulan ini sudah ada. Silakan edit pembayaran yang sudah ada.'
            ], 422);
        }

        $pembayaran = Pembayaran::create([
            'pelanggan_id' => $pelangganId,
            'bulan_bayar' => $validated['bulan_bayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'meteran_sebelum' => $validated['meteran_sebelum'] ?? null,
            'meteran_sesudah' => $validated['meteran_sesudah'] ?? null,
            'abunemen' => $validated['abunemen'] ?? false,
            'tunggakan' => $validated['tunggakan'] ?? 0,
            'jumlah_kubik' => $validated['jumlah_kubik'] ?? 0,
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);        
        
        // Update atau create tagihan_bulanan
        $tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pelangganId)
            ->where('bulan', $validated['bulan_bayar'])
            ->first();
        
        if ($tagihan) {
            // Update data meteran dan tagihan agar sinkron dengan pembayaran
            $tagihan->meteran_sebelum = $validated['meteran_sebelum'] ?? $tagihan->meteran_sebelum;
            $tagihan->meteran_sesudah = $validated['meteran_sesudah'] ?? $tagihan->meteran_sesudah;
            $tagihan->pemakaian_kubik = $validated['jumlah_kubik'] ?? $tagihan->pemakaian_kubik;
            
            // Jika pembayaran menyertakan abunemen, update juga
            if (isset($validated['abunemen'])) {
                $tagihan->ada_abunemen = $validated['abunemen'];
            }
            
            // Update total tagihan
            $tagihan->total_tagihan = $validated['jumlah_bayar'];
            
            // Trim dan uppercase keterangan untuk konsistensi
            $keterangan = trim(strtoupper($validated['keterangan'] ?? ''));
            
            // Logika status bayar berdasarkan keterangan:
            // - TUNGGAKAN: tetap BELUM_BAYAR (belum dibayar sama sekali)
            // - CICILAN: BELUM_BAYAR jika belum lunas, SUDAH_BAYAR jika sudah lunas
            // - LUNAS: SUDAH_BAYAR
            // - Kosong/lainnya: bergantung pada jumlah bayar vs total tagihan
            
            if ($keterangan === 'TUNGGAKAN') {
                // Jika dibuat sebagai tunggakan, tetap BELUM_BAYAR
                $tagihan->status_bayar = 'BELUM_BAYAR';
                $tagihan->jumlah_terbayar = 0; // Reset jumlah terbayar untuk tunggakan
            } elseif ($keterangan === 'CICILAN') {
                // Untuk cicilan, cek apakah sudah lunas
                $totalTagihanRef = $tagihan->total_tagihan > 0 ? $tagihan->total_tagihan : $validated['jumlah_bayar'];
                
                if ($validated['jumlah_bayar'] >= $totalTagihanRef) {
                    $tagihan->status_bayar = 'SUDAH_BAYAR';
                    $tagihan->jumlah_terbayar = $validated['jumlah_bayar'];
                } else {
                    $tagihan->status_bayar = 'BELUM_BAYAR';
                    $tagihan->jumlah_terbayar = $validated['jumlah_bayar'];
                }
            } elseif ($keterangan === 'LUNAS') {
                // Eksplisit LUNAS
                $tagihan->status_bayar = 'SUDAH_BAYAR';
                $tagihan->jumlah_terbayar = $validated['jumlah_bayar'];
            } else {
                // Default: cek apakah jumlah bayar >= total tagihan
                $totalTagihanRef = $tagihan->total_tagihan > 0 ? $tagihan->total_tagihan : $validated['jumlah_bayar'];
                
                if ($validated['jumlah_bayar'] >= $totalTagihanRef) {
                    $tagihan->status_bayar = 'SUDAH_BAYAR';
                } else {
                    $tagihan->status_bayar = 'BELUM_BAYAR';
                }
                $tagihan->jumlah_terbayar = $validated['jumlah_bayar'];
            }
            
            $tagihan->save();
        } else {
            // Jika belum ada tagihan, buat baru
            $keterangan = trim(strtoupper($validated['keterangan'] ?? ''));
            
            // Logika status bayar:
            // - TUNGGAKAN: BELUM_BAYAR (belum dibayar)
            // - CICILAN: BELUM_BAYAR (cicilan belum lunas)
            // - LUNAS: SUDAH_BAYAR
            // - Kosong/lainnya: bergantung pada jumlah bayar
            
            $statusBayar = 'SUDAH_BAYAR';
            $jumlahTerbayar = $validated['jumlah_bayar'];
            
            if ($keterangan === 'TUNGGAKAN') {
                $statusBayar = 'BELUM_BAYAR';
                $jumlahTerbayar = 0; // Tunggakan = belum bayar sama sekali
            } elseif ($keterangan === 'CICILAN') {
                // Cicilan dianggap belum lunas (bisa ditambah pembayaran di kemudian hari)
                $statusBayar = 'BELUM_BAYAR';
            } elseif ($keterangan !== 'LUNAS') {
                // Jika bukan TUNGGAKAN, CICILAN, atau LUNAS, cek jumlah bayar
                // Anggap tidak lunas jika jumlah bayar 0 atau tidak ada
                if ($validated['jumlah_bayar'] <= 0) {
                    $statusBayar = 'BELUM_BAYAR';
                }
            }
            
            \App\Models\TagihanBulanan::create([
                'pelanggan_id' => $pelangganId,
                'bulan' => $validated['bulan_bayar'],
                'meteran_sebelum' => $validated['meteran_sebelum'] ?? 0,
                'meteran_sesudah' => $validated['meteran_sesudah'] ?? 0,
                'pemakaian_kubik' => $validated['jumlah_kubik'] ?? 0,
                'ada_abunemen' => $validated['abunemen'] ?? false,
                'total_tagihan' => $validated['jumlah_bayar'],
                'jumlah_terbayar' => $jumlahTerbayar,
                'status_bayar' => $statusBayar,
                'tanggal_bayar' => $validated['tanggal_bayar'],
            ]);
        }
        
        // Jika bayar tunggakan juga, update status tunggakan yang dibayar
        if (isset($validated['bayar_tunggakan']) && $validated['bayar_tunggakan'] && isset($validated['id_tunggakan'])) {
            foreach ($validated['id_tunggakan'] as $tunggakanId) {
                $tunggakanTagihan = \App\Models\TagihanBulanan::find($tunggakanId);
                if ($tunggakanTagihan) {
                    $tunggakanTagihan->status_bayar = 'SUDAH_BAYAR';
                    $tunggakanTagihan->save();
                }
            }
        }
        
        return response()->json([
            'message' => 'Pembayaran berhasil ditambahkan',
            'pembayaran' => [
                'id' => $pembayaran->id,
                'bulan_bayar' => $pembayaran->bulan_bayar,
                'tanggal_bayar' => $pembayaran->tanggal_bayar->format('Y-m-d'),
                'meteran_sebelum' => $pembayaran->meteran_sebelum,
                'meteran_sesudah' => $pembayaran->meteran_sesudah,
                'abunemen' => $pembayaran->abunemen,
                'tunggakan' => $pembayaran->tunggakan,
                'jumlah_kubik' => $pembayaran->jumlah_kubik,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'keterangan' => $pembayaran->keterangan,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        
        $pembayaran->update([
            'tanggal_bayar' => $validated['tanggal_bayar'],
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return response()->json([
            'message' => 'Pembayaran berhasil diupdate',
            'pembayaran' => [
                'id' => $pembayaran->id,
                'bulan_bayar' => $pembayaran->bulan_bayar,
                'tanggal_bayar' => $pembayaran->tanggal_bayar->format('Y-m-d'),
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'keterangan' => $pembayaran->keterangan,
            ],
        ]);
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Update status_bayar di tagihan_bulanan kembali ke BELUM_BAYAR
        $tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pembayaran->pelanggan_id)
            ->where('bulan', $pembayaran->bulan_bayar)
            ->first();
        
        if ($tagihan) {
            $tagihan->status_bayar = 'BELUM_BAYAR';
            $tagihan->save();
        }
        
        $pembayaran->delete();

        return response()->json([
            'message' => 'Pembayaran berhasil dihapus'
        ]);
    }

    public function sendReceipt($id)
    {
        $pembayaran = Pembayaran::with('pelanggan')->findOrFail($id);
        
        if (!$pembayaran->pelanggan) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }

        if (!$pembayaran->pelanggan->no_whatsapp) {
            return response()->json([
                'message' => 'Nomor WhatsApp pelanggan tidak tersedia'
            ], 422);
        }

        // Cek data tagihan untuk melengkapi informasi yang kurang
        $tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pembayaran->pelanggan_id)
            ->where('bulan', $pembayaran->bulan_bayar)
            ->first();
            
        $meteranSebelum = $pembayaran->meteran_sebelum;
        $meteranSesudah = $pembayaran->meteran_sesudah;
        $jumlahKubik = $pembayaran->jumlah_kubik;
        $biayaAbunemen = 3000; // Default abonemen wajib
        $tarifPerKubik = 2000; // Default tarif per kubik
        
        // Ambil data dari tagihan jika ada
        if ($tagihan) {
            // Meteran
            if (is_null($meteranSebelum)) $meteranSebelum = $tagihan->meteran_sebelum;
            if (is_null($meteranSesudah)) $meteranSesudah = $tagihan->meteran_sesudah;
            
            // Jumlah kubik - prioritas dari tagihan
            if (!is_null($tagihan->pemakaian_kubik) && $tagihan->pemakaian_kubik > 0) {
                $jumlahKubik = $tagihan->pemakaian_kubik;
            } elseif ($jumlahKubik <= 0 && $meteranSebelum && $meteranSesudah) {
                // Hitung dari meteran
                $jumlahKubik = $meteranSesudah - $meteranSebelum;
            }
            
            // Biaya abonemen dari tagihan (jika ada)
            if ($tagihan->biaya_abunemen > 0) {
                $biayaAbunemen = $tagihan->biaya_abunemen;
            }
            
            // Tarif per kubik dari tagihan
            if ($tagihan->tarif_per_kubik > 0) {
                $tarifPerKubik = $tagihan->tarif_per_kubik;
            }
        } elseif ($jumlahKubik <= 0 && $meteranSebelum && $meteranSesudah) {
            // Jika tidak ada tagihan, hitung dari meteran
            $jumlahKubik = $meteranSesudah - $meteranSebelum;
        }
        
        // Generate PDF
        $data = [
            'pembayaran' => [
                'id' => $pembayaran->id,
                'pelanggan_id' => $pembayaran->pelanggan->id_pelanggan,
                'pelanggan_nama' => $pembayaran->pelanggan->nama_pelanggan,
                'rt' => $pembayaran->pelanggan->rt,
                'rw' => $pembayaran->pelanggan->rw,
                'no_whatsapp' => $pembayaran->pelanggan->no_whatsapp,
                'bulan_bayar' => \Carbon\Carbon::parse($pembayaran->bulan_bayar . '-01')->locale('id')->isoFormat('MMMM YYYY'),
                'tanggal_bayar' => \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->locale('id')->isoFormat('D MMMM YYYY'),
                'meteran_sebelum' => $meteranSebelum,
                'meteran_sesudah' => $meteranSesudah,
                'biaya_abunemen' => $biayaAbunemen,
                'tarif_per_kubik' => $tarifPerKubik,
                'tunggakan' => $pembayaran->tunggakan,
                'jumlah_kubik' => $jumlahKubik,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'keterangan' => $pembayaran->keterangan,
            ]
        ];

        $pdf = Pdf::loadView('pdf.struk-pembayaran', $data);
        $pdf->setPaper([0, 0, 302.36, 566.93], 'portrait'); // 80mm x 200mm struk paper
        
        // Generate unique filename with timestamp to prevent caching
        $timestamp = time();
        $fileName = 'struk_' . $pembayaran->pelanggan->id_pelanggan . '_' . str_replace('-', '', $pembayaran->bulan_bayar) . '_' . $timestamp . '.pdf';
        $filePath = public_path('storage/struk/' . $fileName);
        
        // Create directory if not exists
        if (!file_exists(public_path('storage/struk'))) {
            mkdir(public_path('storage/struk'), 0755, true);
        }
        
        // Delete old struk files for this pelanggan and month to save space
        $oldPattern = public_path('storage/struk/struk_' . $pembayaran->pelanggan->id_pelanggan . '_' . str_replace('-', '', $pembayaran->bulan_bayar) . '_*.pdf');
        foreach (glob($oldPattern) as $oldFile) {
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }
        
        $pdf->save($filePath);

        // Generate WhatsApp link
        $waNumber = preg_replace('/[^0-9]/', '', $pembayaran->pelanggan->no_whatsapp);
        if (substr($waNumber, 0, 1) === '0') {
            $waNumber = '62' . substr($waNumber, 1);
        }

        $message = "Halo *{$pembayaran->pelanggan->nama_pelanggan}*,\n\n";
        $message .= "Terima kasih atas pembayaran Anda untuk tagihan bulan *{$data['pembayaran']['bulan_bayar']}*.\n\n";
        $message .= "📋 *Detail Pembayaran:*\n";
        $message .= "• ID Pelanggan: {$pembayaran->pelanggan->id_pelanggan}\n";
        $message .= "• Bulan Tagihan: {$data['pembayaran']['bulan_bayar']}\n";
        $message .= "• Tanggal Bayar: {$data['pembayaran']['tanggal_bayar']}\n";
        $message .= "• Total Bayar: Rp " . number_format($pembayaran->jumlah_bayar, 0, ',', '.') . "\n\n";
        $message .= "✅ Status: *LUNAS*\n\n";
        $message .= "Struk pembayaran Anda dapat diunduh melalui link berikut:\n";
        $message .= url('storage/struk/' . $fileName) . "\n\n";
        $message .= "Simpan struk ini sebagai bukti pembayaran yang sah.\n\n";
        $message .= "Salam,\n*KP-SPAMS DAMAR WULAN*";

        $waLink = "https://wa.me/{$waNumber}?text=" . urlencode($message);

        return response()->json([
            'message' => 'Struk berhasil digenerate',
            'wa_link' => $waLink,
            'pdf_url' => url('storage/struk/' . $fileName),
            'pelanggan_nama' => $pembayaran->pelanggan->nama_pelanggan,
            'no_whatsapp' => $pembayaran->pelanggan->no_whatsapp,
        ]);
    }

    public function getReceiptLink($id)
    {
        $pembayaran = Pembayaran::with('pelanggan')->findOrFail($id);
        
        if (!$pembayaran->pelanggan) {
            return response()->json([
                'message' => 'Data pelanggan tidak ditemukan'
            ], 404);
        }

        if (!$pembayaran->pelanggan->no_whatsapp) {
            return response()->json([
                'message' => 'Nomor WhatsApp pelanggan tidak tersedia'
            ], 422);
        }

        // Always regenerate PDF to ensure fresh data
        return $this->sendReceipt($id);
    }

    public function downloadPdf($id)
    {
        $pembayaran = Pembayaran::with('pelanggan')->findOrFail($id);
        
        if (!$pembayaran->pelanggan) {
            abort(404, 'Data pelanggan tidak ditemukan');
        }

        // Get data from pembayaran
        $biayaAbunemen = $pembayaran->abunemen ? 3000 : 0;
        $meteranSebelum = $pembayaran->meteran_sebelum ?? 0;
        $meteranSesudah = $pembayaran->meteran_sesudah ?? 0;
        $jumlahKubik = $pembayaran->jumlah_kubik ?? 0;
        $tarifPerKubik = 2000; // Default tarif
        
        // Generate PDF
        $data = [
            'pembayaran' => [
                'id' => $pembayaran->id,
                'pelanggan_id' => $pembayaran->pelanggan->id_pelanggan,
                'pelanggan_nama' => $pembayaran->pelanggan->nama_pelanggan,
                'rt' => $pembayaran->pelanggan->rt,
                'rw' => $pembayaran->pelanggan->rw,
                'no_whatsapp' => $pembayaran->pelanggan->no_whatsapp,
                'bulan_bayar' => \Carbon\Carbon::parse($pembayaran->bulan_bayar . '-01')->locale('id')->isoFormat('MMMM YYYY'),
                'tanggal_bayar' => \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->locale('id')->isoFormat('D MMMM YYYY'),
                'meteran_sebelum' => $meteranSebelum,
                'meteran_sesudah' => $meteranSesudah,
                'biaya_abunemen' => $biayaAbunemen,
                'tarif_per_kubik' => $tarifPerKubik,
                'tunggakan' => $pembayaran->tunggakan,
                'jumlah_kubik' => $jumlahKubik,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'keterangan' => $pembayaran->keterangan,
            ]
        ];

        $pdf = Pdf::loadView('pdf.struk-pembayaran', $data);
        $pdf->setPaper([0, 0, 302.36, 566.93], 'portrait');
        
        $fileName = 'struk_' . $pembayaran->pelanggan->id_pelanggan . '_' . str_replace('-', '', $pembayaran->bulan_bayar) . '.pdf';
        
        return $pdf->download($fileName);
    }

    public function printReceipt($id)
    {
        $pembayaran = Pembayaran::with('pelanggan')->findOrFail($id);
        
        if (!$pembayaran->pelanggan) {
            abort(404, 'Data pelanggan tidak ditemukan');
        }

        // Get data from pembayaran
        $biayaAbunemen = $pembayaran->abunemen ? 3000 : 0;
        $meteranSebelum = $pembayaran->meteran_sebelum ?? 0;
        $meteranSesudah = $pembayaran->meteran_sesudah ?? 0;
        $jumlahKubik = $pembayaran->jumlah_kubik ?? 0;
        $tarifPerKubik = 2000; // Default tarif
        
        $data = [
            'pembayaran' => [
                'id' => $pembayaran->id,
                'pelanggan_id' => $pembayaran->pelanggan->id_pelanggan,
                'pelanggan_nama' => $pembayaran->pelanggan->nama_pelanggan,
                'rt' => $pembayaran->pelanggan->rt,
                'rw' => $pembayaran->pelanggan->rw,
                'no_whatsapp' => $pembayaran->pelanggan->no_whatsapp,
                'bulan_bayar' => \Carbon\Carbon::parse($pembayaran->bulan_bayar . '-01')->locale('id')->isoFormat('MMMM YYYY'),
                'tanggal_bayar' => \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->locale('id')->isoFormat('D MMMM YYYY'),
                'meteran_sebelum' => $meteranSebelum,
                'meteran_sesudah' => $meteranSesudah,
                'biaya_abunemen' => $biayaAbunemen,
                'tarif_per_kubik' => $tarifPerKubik,
                'tunggakan' => $pembayaran->tunggakan,
                'jumlah_kubik' => $jumlahKubik,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'keterangan' => $pembayaran->keterangan,
            ]
        ];
        
        return view('print.struk-pembayaran', $data);
    }
}
