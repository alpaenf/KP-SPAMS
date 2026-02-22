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
                // Logika tarif dan abunemen (konsisten dengan PembayaranController@store)
                $isSosial = ($p->pelanggan?->kategori === 'sosial');
                $abunemenNominal = $p->abunemen ? ($isSosial ? 0 : 3000) : 0;
                
                $tunggakanNominal = (float) ($p->tunggakan ?? 0);
                $jumlahBayar = (float) $p->jumlah_bayar;
                
                // Biaya air adalah sisa setelah dikurangi abunemen dan tunggakan
                $biayaAir = max(0, $jumlahBayar - $abunemenNominal - $tunggakanNominal);
                
                // Hitung tarif per kubik secara dinamis dari data yang tersimpan
                $tarifPerKubik = $p->jumlah_kubik > 0 ? round($biayaAir / $p->jumlah_kubik) : ($isSosial ? 0 : 2000);

                // Cek status tagihan bulan ini dari tabel TagihanBulanan
                $tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $p->pelanggan_id)
                    ->where('bulan', $p->bulan_bayar)
                    ->first();

                // Tentukan status_tagihan secara dinamis agar UI akurat (anti-mismatch)
                $statusTagihan = $tagihan?->status_bayar ?? 'SUDAH_BAYAR';
                $sisa = $tagihan ? ($tagihan->total_tagihan - $tagihan->jumlah_terbayar) : 0;
                
                if ($tagihan) {
                    if ($sisa <= 0 && $tagihan->total_tagihan > 0) {
                        $statusTagihan = 'SUDAH_BAYAR';
                    } elseif ($sisa > 0 && $tagihan->jumlah_terbayar > 0) {
                        $statusTagihan = 'CICILAN';
                    } elseif ($sisa > 0 && $tagihan->jumlah_terbayar <= 0) {
                        $statusTagihan = 'TUNGGAKAN';
                    }
                }

                return [
                    'id'               => $p->id,
                    'bulan_bayar'      => $p->bulan_bayar,
                    'tanggal_bayar'    => $p->tanggal_bayar->format('Y-m-d'),
                    'created_at'       => $p->created_at?->format('Y-m-d H:i:s'),
                    'meteran_sebelum'  => $p->meteran_sebelum,
                    'meteran_sesudah'  => $p->meteran_sesudah,
                    'abunemen'         => $p->abunemen,
                    'abunemen_nominal' => $abunemenNominal,
                    'tunggakan'        => $tunggakanNominal,
                    'jumlah_kubik'     => $p->jumlah_kubik,
                    'biaya_air'        => $biayaAir,
                    'tarif_per_kubik'  => $tarifPerKubik,
                    'jumlah_bayar'     => $jumlahBayar,
                    'status_tagihan'   => $statusTagihan,
                    'keterangan'       => $p->keterangan,
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
        // Normalize keterangan jadi uppercase sebelum validation
        if ($request->has('keterangan')) {
            $request->merge([
                'keterangan' => strtoupper(trim($request->input('keterangan')))
            ]);
        }
        
        // Normalize status_bayar jadi uppercase sebelum validation
        if ($request->has('status_bayar')) {
            $request->merge([
                'status_bayar' => strtoupper(trim($request->input('status_bayar')))
            ]);
        }
        
        $validated = $request->validate([
            'bulan_bayar' => 'required|string|max:7',
            'tanggal_bayar' => 'required|date',
            'meteran_sebelum' => 'nullable|numeric|min:0',
            'meteran_sesudah' => 'nullable|numeric|min:0',
            'abunemen' => 'nullable|boolean',
            'tunggakan' => 'nullable|numeric|min:0',
            'jumlah_kubik' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
            'status_bayar' => 'required|string|in:SUDAH_BAYAR,BELUM_BAYAR,CICILAN,TUNGGAKAN',
            'bayar_tunggakan' => 'nullable|boolean',
            'jumlah_bayar_tunggakan' => 'nullable|numeric|min:0',
            'id_tunggakan' => 'nullable|array',
            'id_tunggakan.*' => 'integer|exists:tagihan_bulanan,id',
        ]);

        $pelanggan = Pelanggan::findOrFail($pelangganId);
        
        // Cek apakah sudah ada pembayaran untuk bulan ini
        $existing = Pembayaran::where('pelanggan_id', $pelangganId)
            ->where('bulan_bayar', $validated['bulan_bayar'])
            ->first();
        
        // Allow duplicate untuk:
        // 1. CICILAN (bayar bertahap)
        // 2. Bayar tunggakan (update pembayaran bulan ini + update tagihan tunggakan)
        // Reject duplicate untuk case lainnya
        
        if ($existing && $validated['status_bayar'] === 'CICILAN') {
            // CICILAN: UPDATE accumulate
            $existing->jumlah_bayar = $existing->jumlah_bayar + $validated['jumlah_bayar'];
            $existing->tanggal_bayar = $validated['tanggal_bayar'];
            $existing->keterangan = $validated['keterangan'] ?? $existing->keterangan;
            $existing->save();
            
            $pembayaran = $existing;
        } elseif ($existing && isset($validated['bayar_tunggakan']) && $validated['bayar_tunggakan']) {
            // Bayar tunggakan: UPDATE pembayaran bulan ini dengan total (tagihan bulan ini + tunggakan)
            $existing->jumlah_bayar = $validated['jumlah_bayar'];
            $existing->tanggal_bayar = $validated['tanggal_bayar'];
            $existing->keterangan = $validated['keterangan'] ?? $existing->keterangan;
            $existing->tunggakan = $validated['jumlah_bayar_tunggakan'] ?? 0;
            $existing->save();
            
            $pembayaran = $existing;
        } elseif ($existing) {
            // Reject duplicate untuk case normal
            return response()->json([
                'message' => 'Pembayaran untuk bulan ini sudah ada. Silakan edit pembayaran yang sudah ada.'
            ], 422);
        } else {
            // Create pembayaran baru (cicilan pertama kali atau pembayaran umum)
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
        }        
        
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
            
            // Perbaikan: Pastikan tarif dan abunemen konsisten
            if ($pelanggan->kategori === 'sosial') {
                $tagihan->tarif_per_kubik = 0;
                $tagihan->ada_abunemen = false;
                $tagihan->biaya_abunemen = 0;
            } else {
                $tagihan->tarif_per_kubik = 2000;
                $tagihan->biaya_abunemen = 3000;
            }

            // Hitung berapa bagian yang untuk bulan ini (Exclude tunggakan yang dibayar)
            $jumlahBayarBulanIni = (float) $validated['jumlah_bayar'];
            if (isset($validated['bayar_tunggakan']) && $validated['bayar_tunggakan']) {
                $jumlahBayarBulanIni -= floatval($validated['jumlah_bayar_tunggakan'] ?? 0);
            }

            // Hitung total tagihan dulu (agar pembanding akurat)
            $tagihan->hitungTagihan();
            
            // Logika Pembayaran: Selalu tambahkan (accumulate)
            if ($jumlahBayarBulanIni > 0) {
                if ($existing) {
                    $tagihan->jumlah_terbayar = ($tagihan->jumlah_terbayar ?? 0) + $jumlahBayarBulanIni;
                } else {
                    $tagihan->jumlah_terbayar = $jumlahBayarBulanIni;
                }
            }

            // Tentukan status bayar BERDASARKAN HASIL AKHIR
            if ($tagihan->jumlah_terbayar >= $tagihan->total_tagihan && $tagihan->total_tagihan > 0) {
                $tagihan->status_bayar = 'SUDAH_BAYAR';
            } elseif ($tagihan->jumlah_terbayar > 0) {
                $tagihan->status_bayar = 'CICILAN';
            } else {
                $tagihan->status_bayar = $validated['status_bayar'] ?? 'BELUM_BAYAR';
            }
            
            $tagihan->save();
        } else {
            // Jika belum ada tagihan, buat baru
            // Jika belum ada tagihan, buat baru
            $jumlahTerbayar = 0;
            if ($validated['jumlah_bayar'] > 0) {
                // Jika pembayaran termasuk bayar tunggakan, kita hanya set jumlah_terbayar senilai porsi bulan ini
                $jumlahBayarBulanIni = (float) $validated['jumlah_bayar'];
                if (isset($validated['bayar_tunggakan']) && $validated['bayar_tunggakan']) {
                    $jumlahBayarBulanIni -= floatval($validated['jumlah_bayar_tunggakan'] ?? 0);
                }
                if ($jumlahBayarBulanIni > 0) {
                    $jumlahTerbayar = $jumlahBayarBulanIni;
                }
            }
            
            $tagihan = \App\Models\TagihanBulanan::create([
                'pelanggan_id' => $pelangganId,
                'bulan' => $validated['bulan_bayar'],
                'meteran_sebelum' => $validated['meteran_sebelum'] ?? 0,
                'meteran_sesudah' => $validated['meteran_sesudah'] ?? 0,
                'pemakaian_kubik' => $validated['jumlah_kubik'] ?? 0,
                'tarif_per_kubik' => $pelanggan->kategori === 'sosial' ? 0 : 2000,
                'ada_abunemen' => $validated['abunemen'] ?? false,
                'biaya_abunemen' => ($pelanggan->kategori === 'sosial' || !($validated['abunemen'] ?? false)) ? 0 : 3000,
                'total_tagihan' => 0, // Akan dihitung oleh hitungTagihan()
                'jumlah_terbayar' => $jumlahTerbayar,
                'status_bayar' => $validated['status_bayar'] ?? 'BELUM_BAYAR',
                'tanggal_bayar' => $validated['tanggal_bayar'],
            ]);

            // PENTING: Hitung total tagihan dari meteran yang diinput
            $tagihan->hitungTagihan();
            
            // Re-determine status berdasarkan hasil hitung
            if ($tagihan->jumlah_terbayar >= $tagihan->total_tagihan && $tagihan->total_tagihan > 0) {
                $tagihan->status_bayar = 'SUDAH_BAYAR';
            } elseif ($tagihan->jumlah_terbayar > 0) {
                $tagihan->status_bayar = 'CICILAN';
            } else {
                $tagihan->status_bayar = $validated['status_bayar'] ?? 'TUNGGAKAN';
            }
            
            $tagihan->save();
        }
        
        // Jika bayar tunggakan, distribusikan pembayaran ke tunggakan terlama dulu (FIFO)
        if (isset($validated['bayar_tunggakan']) && $validated['bayar_tunggakan'] && isset($validated['id_tunggakan'])) {
            $jumlahBayarTunggakan = floatval($validated['jumlah_bayar_tunggakan'] ?? 0);
            $sisaPembayaran = $jumlahBayarTunggakan;
            
            // Urutkan tunggakan dari yang terlama (berdasarkan bulan)
            $listTunggakan = \App\Models\TagihanBulanan::whereIn('id', $validated['id_tunggakan'])
                ->orderBy('bulan', 'asc')
                ->get();
            
            foreach ($listTunggakan as $tunggakanTagihan) {
                if ($sisaPembayaran <= 0) break;
                
                $sisaTagihan = $tunggakanTagihan->total_tagihan - $tunggakanTagihan->jumlah_terbayar;
                
                if ($sisaTagihan <= 0) {
                    // Sudah lunas, skip
                    continue;
                }
                
                if ($sisaPembayaran >= $sisaTagihan) {
                    // Bayar lunas tagihan ini
                    $tunggakanTagihan->jumlah_terbayar = $tunggakanTagihan->total_tagihan;
                    $tunggakanTagihan->status_bayar = 'SUDAH_BAYAR';
                    $sisaPembayaran -= $sisaTagihan;
                } else {
                    // Bayar sebagian (cicilan)
                    $tunggakanTagihan->jumlah_terbayar += $sisaPembayaran;
                    $tunggakanTagihan->status_bayar = 'CICILAN'; // Status jadi CICILAN untuk pembayaran bertahap
                    $sisaPembayaran = 0;
                }
                
                $tunggakanTagihan->save();
            }
        }
        
        return response()->json([
            'message' => 'Pembayaran berhasil ditambahkan',
            'pembayaran' => [
                'id' => $pembayaran->id,
                'bulan_bayar' => $pembayaran->bulan_bayar,
                'tanggal_bayar' => $pembayaran->tanggal_bayar->format('Y-m-d'),
                'created_at' => $pembayaran->created_at?->format('Y-m-d H:i:s'),
                'meteran_sebelum' => $pembayaran->meteran_sebelum,
                'meteran_sesudah' => $pembayaran->meteran_sesudah,
                'abunemen' => $pembayaran->abunemen,
                'abunemen_nominal' => $tagihan->biaya_abunemen ?? 0,
                'tunggakan' => $pembayaran->tunggakan,
                'jumlah_kubik' => $pembayaran->jumlah_kubik,
                'biaya_air' => max(0, $pembayaran->jumlah_bayar - ($tagihan->biaya_abunemen ?? 0) - ($pembayaran->tunggakan ?? 0)),
                'tarif_per_kubik' => $tagihan->tarif_per_kubik ?? 2000,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'status_tagihan' => $tagihan->status_bayar ?? 'BELUM_BAYAR',
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
