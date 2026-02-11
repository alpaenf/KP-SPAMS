<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\PaymentSetting;
use App\Exports\PelangganExport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class HomeController extends Controller
{
    public function downloadPdf($id)
    {
        $pelangganData = Pelanggan::where('id_pelanggan', $id)->firstOrFail();
        
        // Cek pembayaran bulan ini
        $bulanIni = now()->format('Y-m');
        $pembayaran = $pelangganData->pembayarans()
            ->where('bulan_bayar', $bulanIni)
            ->first();

        // Cek pembayaran yang dilakukan di bulan ini (bisa termasuk bayar tunggakan)
        $pembayaranBaru = $pelangganData->pembayarans()
            ->whereYear('tanggal_bayar', now()->year)
            ->whereMonth('tanggal_bayar', now()->month)
            ->orderBy('bulan_bayar', 'desc')
            ->get()
            ->map(function ($p) {
                return [
                    'bulan' => \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y'),
                    'jumlah_bayar' => $p->jumlah_bayar,
                    'tanggal_bayar' => $p->tanggal_bayar->format('d/m/Y'),
                    'is_tunggakan' => $p->bulan_bayar !== now()->format('Y-m'),
                ];
            });

        // Cek Daftar Tunggakan (hanya bulan setelah pendaftaran)
        $tunggakanList = [];
        $bulanDaftar = $pelangganData->created_at->format('Y-m');
        
        for ($i = 1; $i <= 12; $i++) {
            $cekBulan = now()->subMonths($i)->format('Y-m');
            
            // Skip jika bulan ini <= bulan pendaftaran (pelanggan belum terdaftar)
            if ($cekBulan <= $bulanDaftar) {
                continue;
            }
            
            $cekPembayaran = $pelangganData->pembayarans()
                ->where('bulan_bayar', $cekBulan)
                ->exists();
            
            if (!$cekPembayaran && $pelangganData->status_aktif) {
                $tunggakanList[] = [
                    'bulan' => \Carbon\Carbon::createFromFormat('Y-m', $cekBulan)->locale('id')->isoFormat('MMMM Y'),
                    'status' => 'Belum Dibayar'
                ];
            }
        }

        // Siapkan data array yang sama seperti di method index
        $pelanggan = [
            'id_pelanggan' => $pelangganData->id_pelanggan,
            'nama_pelanggan' => $pelangganData->nama_pelanggan,
            'rt' => $pelangganData->rt,
            'rw' => $pelangganData->rw,
            'status_aktif' => $pelangganData->status_aktif,
            'kategori' => $pelangganData->kategori,
            'tagihan_bulan_ini' => now()->locale('id')->isoFormat('MMMM Y'),
            'status_bayar' => $pembayaran ? 'Lunas' : 'Belum Bayar',
            'jumlah_bayar' => $pembayaran ? $pembayaran->jumlah_bayar : 0,
            'tanggal_bayar' => $pembayaran ? $pembayaran->tanggal_bayar->format('d/m/Y') : null,
            'pembayaran_terakhir' => $pembayaranBaru,
            'tunggakan' => $tunggakanList,
            'riwayat' => $pelangganData->pembayarans()
                ->orderBy('bulan_bayar', 'desc')
                ->take(5)
                ->get()
                ->map(function ($p) {
                    return [
                        'bulan' => \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y'),
                        'kubik' => $p->jumlah_kubik ?? 0,
                        'rupiah' => $p->jumlah_bayar,
                    ];
                })
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pelanggan', compact('pelanggan'));
        
        return $pdf->download('Laporan-Pelanggan-'.$pelanggan['id_pelanggan'].'.pdf');
    }

    public function pembayaran($id)
    {
        $pelangganData = Pelanggan::where('id_pelanggan', $id)->firstOrFail();
        
        // Cek pembayaran bulan ini
        $bulanIni = now()->format('Y-m');
        $pembayaran = $pelangganData->pembayarans()
            ->where('bulan_bayar', $bulanIni)
            ->first();

        // Cek tagihan bulan ini dari tabel tagihan_bulanan
        $tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pelangganData->id)
            ->where('bulan', $bulanIni)
            ->first();

        // Tentukan tagihan berdasarkan kategori dan tagihan_bulanan
        $isSosial = $pelangganData->kategori === 'sosial';
        
        // Jika ada tagihan dari tabel tagihan_bulanan, gunakan itu
        // TAPI jika meteran belum diinput (masih NULL), gunakan default
        if ($tagihan && $tagihan->meteran_sesudah !== null) {
            $jumlahTagihan = $tagihan->total_tagihan;
            $adaTagihan = true;
            $meteranBelumDiinput = false;
        } else if ($tagihan && $tagihan->meteran_sesudah === null) {
            // Tagihan sudah di-generate tapi meteran belum diinput
            // Gunakan total_tagihan dari database (bisa 0 atau ada tunggakan)
            $jumlahTagihan = $tagihan->total_tagihan;
            $adaTagihan = true;
            $meteranBelumDiinput = true;
        } else {
            // Belum ada tagihan sama sekali (pelanggan baru atau belum di-generate)
            // Jangan tampilkan tagihan apapun
            $jumlahTagihan = 0;
            $adaTagihan = false;
            $meteranBelumDiinput = false;
        }

        $pelanggan = [
            'id_pelanggan' => $pelangganData->id_pelanggan,
            'nama_pelanggan' => $pelangganData->nama_pelanggan,
            'rt' => $pelangganData->rt,
            'rw' => $pelangganData->rw,
            'kategori' => $pelangganData->kategori ?? 'umum',
            'tagihan_bulan_ini' => now()->locale('id')->isoFormat('MMMM Y'),
            'jumlah_bayar' => $pembayaran ? $pembayaran->jumlah_bayar : $jumlahTagihan,
            'sudah_bayar' => !is_null($pembayaran),
            'ada_tagihan' => $adaTagihan, // Apakah sudah ada tagihan yang di-generate pengelola
            'meteran_belum_diinput' => $meteranBelumDiinput, // Meteran belum diisi
        ];

        // Get payment settings
        $paymentSettings = PaymentSetting::getSettings();

        return Inertia::render('Pembayaran', [
            'pelanggan' => $pelanggan,
            'paymentSettings' => $paymentSettings,
            'tagihan' => $tagihan, // Tambahkan data tagihan untuk detail meteran
        ]);
    }

    public function index(Request $request)
    {
        $totalPelanggan = Pelanggan::count();
        $pelangganAktif = Pelanggan::where('status_aktif', true)->count();
        
        // Hitung cakupan area berdasarkan RT/RW unik
        $rtRw = Pelanggan::select('rt', 'rw')
            ->whereNotNull('rt')
            ->whereNotNull('rw')
            ->distinct()
            ->get();
        
        $cakupanArea = $rtRw->count() > 0 ? $rtRw->count() . ' RT/RW' : 'Belum ada data';
        
        $pelanggan = null;
        $error = null;
        
        // Cek pelanggan jika ada input
        if ($request->has('id_pelanggan')) {
            $idPelanggan = $request->input('id_pelanggan');
            
            $pelangganData = Pelanggan::where('id_pelanggan', $idPelanggan)->first();
            
            if (!$pelangganData) {
                $error = 'ID Pelanggan tidak ditemukan. Pastikan ID yang Anda masukkan benar.';
            } else {
                // Cek pembayaran bulan ini
                $bulanIni = now()->format('Y-m');
                $pembayaran = $pelangganData->pembayarans()
                    ->where('bulan_bayar', $bulanIni)
                    ->first();

                // Cek tagihan bulanan
                $tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pelangganData->id)
                    ->where('bulan', $bulanIni)
                    ->first();

                // Tentukan jumlah tagihan
                $isSosial = $pelangganData->kategori === 'sosial';
                if ($tagihan && $tagihan->meteran_sesudah !== null) {
                    // Ada tagihan dengan meteran sudah diinput
                    $jumlahTagihan = $tagihan->total_tagihan;
                } else if ($tagihan && $tagihan->meteran_sesudah === null) {
                    // Ada tagihan tapi meteran belum diinput
                    $jumlahTagihan = $tagihan->total_tagihan;
                } else {
                    // Belum ada tagihan sama sekali
                    $jumlahTagihan = 0;
                }

                // Cek pembayaran yang dilakukan di bulan ini (bisa termasuk bayar tunggakan)
                $pembayaranBaru = $pelangganData->pembayarans()
                    ->whereYear('tanggal_bayar', now()->year)
                    ->whereMonth('tanggal_bayar', now()->month)
                    ->orderBy('bulan_bayar', 'desc')
                    ->get()
                    ->map(function ($p) {
                        return [
                            'bulan' => \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y'),
                            'jumlah_bayar' => $p->jumlah_bayar,
                            'tanggal_bayar' => $p->tanggal_bayar->format('d/m/Y'),
                            'is_tunggakan' => $p->bulan_bayar !== now()->format('Y-m'),
                        ];
                    });

                // Cek Daftar Tunggakan (hanya bulan setelah pendaftaran)
                $tunggakanList = [];
                $bulanDaftar = $pelangganData->created_at->format('Y-m');
                
                for ($i = 1; $i <= 12; $i++) {
                    $cekBulan = now()->subMonths($i)->format('Y-m');
                    
                    // Skip jika bulan ini <= bulan pendaftaran (pelanggan belum terdaftar)
                    if ($cekBulan <= $bulanDaftar) {
                        continue;
                    }
                    
                    $cekPembayaran = $pelangganData->pembayarans()
                        ->where('bulan_bayar', $cekBulan)
                        ->exists();
                    
                    if (!$cekPembayaran && $pelangganData->status_aktif) {
                        $tunggakanList[] = [
                            'bulan' => \Carbon\Carbon::createFromFormat('Y-m', $cekBulan)->locale('id')->isoFormat('MMMM Y'),
                            'status' => 'Belum Dibayar'
                        ];
                    }
                }

                $pelanggan = [
                    'id_pelanggan' => $pelangganData->id_pelanggan,
                    'nama_pelanggan' => $pelangganData->nama_pelanggan,
                    'rt' => $pelangganData->rt,
                    'rw' => $pelangganData->rw,
                    'status_aktif' => $pelangganData->status_aktif,
                    'kategori' => $pelangganData->kategori,
                    'has_coordinates' => $pelangganData->hasCoordinates(),
                    'tagihan_bulan_ini' => now()->locale('id')->isoFormat('MMMM Y'),
                    'status_bayar' => $pembayaran ? 'Lunas' : 'Belum Bayar',
                    'jumlah_bayar' => $pembayaran ? $pembayaran->jumlah_bayar : $jumlahTagihan,
                    'tanggal_bayar' => $pembayaran ? $pembayaran->tanggal_bayar->format('d/m/Y') : null,
                    'pembayaran_terakhir' => $pembayaranBaru,
                    'tunggakan' => $tunggakanList,
                    'riwayat' => $pelangganData->pembayarans()
                        ->orderBy('bulan_bayar', 'desc')
                        ->take(5)
                        ->get()
                        ->map(function ($p) {
                            return [
                                'bulan' => \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y'),
                                'kubik' => $p->jumlah_kubik ?? 0,
                                'rupiah' => $p->jumlah_bayar,
                            ];
                        })
                ];
            }
        }
        
        // Get payment settings
        $paymentSettings = PaymentSetting::getSettings();
        
        return Inertia::render('Home', [
            'stats' => [
                'totalPelanggan' => $totalPelanggan,
                'pelangganAktif' => $pelangganAktif,
                'cakupanArea' => $cakupanArea,
            ],
            'pelanggan' => $pelanggan,
            'error' => $error,
            'paymentSettings' => $paymentSettings,
        ]);
    }
    
    public function konfirmasiPembayaran(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|string|exists:pelanggan,id_pelanggan',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $pelangganData = Pelanggan::where('id_pelanggan', $validated['id_pelanggan'])->firstOrFail();
        $bulanIni = now()->format('Y-m');

        // Cek apakah ada tagihan bulanan
        $tagihan = \App\Models\TagihanBulanan::where('pelanggan_id', $pelangganData->id)
            ->where('bulan', $bulanIni)
            ->first();

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan bulan ini belum tersedia. Silakan hubungi pengelola.'
            ], 404);
        }

        // Upload bukti transfer jika ada
        if ($request->hasFile('bukti_transfer')) {
            $path = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            $tagihan->bukti_transfer = $path;
        }

        $tagihan->konfirmasi_at = now();
        $tagihan->status_konfirmasi = 'pending';
        $tagihan->save();

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi pembayaran berhasil dikirim. Silakan tunggu verifikasi dari admin.'
        ]);
    }
    
    public function cekPelanggan(Request $request)
    {
        $bulanIni = now()->format('Y-m');
        $bulanFilter = $request->input('bulan', $bulanIni);
        
        // Ambil semua pelanggan dengan pembayaran dan tagihan bulanan
        // Apply filter wilayah berdasarkan user yang login
        $pelangganList = Pelanggan::forUser()
            ->with(['pembayarans', 'tagihanBulanan' => function ($query) use ($bulanIni) {
                $query->where('bulan', $bulanIni);
            }])
            ->orderBy('id_pelanggan', 'asc')
            ->get()
            ->map(function ($p) use ($bulanIni) {
            // Cek apakah sudah bayar bulan ini
            $pembayaranBulanIni = $p->pembayarans->firstWhere('bulan_bayar', $bulanIni);
            
            // Cek tagihan bulanan bulan ini
            $tagihan = $p->tagihanBulanan->firstWhere('bulan', $bulanIni);
            
            // Logika untuk kategori sosial: otomatis lunas dengan tarif 0
            $isSosial = $p->kategori === 'sosial';
            
            // Tentukan status bayar dari tagihan_bulanan jika ada, jika tidak dari pembayarans
            if ($tagihan) {
                $sudahBayar = $tagihan->status_bayar === 'SUDAH_BAYAR';
                $jumlahTagihan = $tagihan->total_tagihan;
            } else {
                $sudahBayar = $isSosial ? true : !is_null($pembayaranBulanIni);
                $jumlahTagihan = $isSosial ? 0 : ($pembayaranBulanIni?->jumlah_bayar ?? 10000);
            }
            
            // Ambil semua bulan yang sudah dibayar
            $bulanDibayar = $p->pembayarans->pluck('bulan_bayar')->toArray();
            
            return [
                'id' => $p->id,
                'id_pelanggan' => $p->id_pelanggan,
                'nama_pelanggan' => $p->nama_pelanggan,
                'no_whatsapp' => $p->no_whatsapp,
                'rt' => $p->rt,
                'rw' => $p->rw,
                'kategori' => $p->kategori ?? 'umum',
                'wilayah' => $p->wilayah,
                'status_aktif' => $p->status_aktif,
                'has_coordinates' => $p->hasCoordinates(),
                'google_maps_link' => $p->google_maps_link,
                'google_maps_url' => $p->google_maps_url,
                'sudah_bayar' => $sudahBayar,
                'tanggal_bayar' => $isSosial ? '-' : ($pembayaranBulanIni?->tanggal_bayar?->format('d/m/Y')),
                'jumlah_bayar' => $jumlahTagihan,
                'bulan_dibayar' => $bulanDibayar,
                // Info tambahan dari tagihan bulanan
                'meteran_belum_diinput' => $tagihan && is_null($tagihan->meteran_sesudah),
                'pemakaian_kubik' => $tagihan?->pemakaian_kubik,
            ];
        });
        
        // Ambil data pembayaran untuk tab Riwayat Pembayaran
        $pembayaranQuery = \App\Models\Pembayaran::query()
            ->whereHas('pelanggan', function($q) {
                $q->forUser();
            })
            ->with('pelanggan')
            ->where('bulan_bayar', $bulanFilter)
            ->orderBy('tanggal_bayar', 'desc');
            
        $pembayaranList = $pembayaranQuery->get()->map(function($p) {
            return [
                'id' => $p->id,
                'pelanggan_id' => $p->pelanggan->id,
                'id_pelanggan' => $p->pelanggan->id_pelanggan,
                'nama_pelanggan' => $p->pelanggan->nama_pelanggan,
                'wilayah' => $p->pelanggan->wilayah,
                'rt' => $p->pelanggan->rt,
                'rw' => $p->pelanggan->rw,
                'kategori' => $p->pelanggan->kategori ?? 'umum',
                'bulan_bayar' => $p->bulan_bayar,
                'tanggal_bayar' => $p->tanggal_bayar->format('Y-m-d'),
                'meteran_sebelum' => $p->meteran_sebelum,
                'meteran_sesudah' => $p->meteran_sesudah,
                'jumlah_kubik' => $p->jumlah_kubik,
                'abunemen' => $p->abunemen,
                'tunggakan' => $p->tunggakan,
                'jumlah_bayar' => $p->jumlah_bayar,
                'keterangan' => $p->keterangan,
                'metode_bayar' => $p->metode_bayar ?? 'Tunai',
                'catatan' => $p->catatan,
            ];
        });
        
        // Hitung statistik pembayaran
        $totalPembayaran = $pembayaranList->count();
        $totalPemasukan = $pembayaranList->sum('jumlah_bayar');
        $rataRata = $totalPembayaran > 0 ? $totalPemasukan / $totalPembayaran : 0;
        
        return Inertia::render('CekPelanggan', [
            'pelangganList' => $pelangganList,
            'pembayaranList' => $pembayaranList,
            'bulanIni' => $bulanIni,
            'bulanFilter' => $bulanFilter,
            'stats' => [
                'totalPembayaran' => $totalPembayaran,
                'totalPemasukan' => $totalPemasukan,
                'rataRata' => $rataRata,
            ],
        ]);
    }
    
    public function dashboard(Request $request)
    {
        $wilayahFilter = $request->input('wilayah');
        
        // Apply filter wilayah berdasarkan user yang login
        $pelangganQuery = Pelanggan::forUser();
        
        // Admin bisa filter wilayah manual, penarik otomatis filtered
        if ($wilayahFilter && auth()->user()->isAdmin()) {
            $pelangganQuery->where('wilayah', $wilayahFilter);
        }
        
        $totalPelanggan = (clone $pelangganQuery)->count();
        $pelangganAktif = (clone $pelangganQuery)->where('status_aktif', true)->count();
        $pelangganNonaktif = (clone $pelangganQuery)->where('status_aktif', false)->count();
        
        // Hitung cakupan area berdasarkan RT/RW unik
        $rtRw = (clone $pelangganQuery)
            ->select('rt', 'rw')
            ->whereNotNull('rt')
            ->whereNotNull('rw')
            ->distinct()
            ->get();
        
        $cakupanArea = $rtRw->count() > 0 ? $rtRw->count() . ' RT/RW' : 'Belum ada data';
        
        // Distribusi per RT/RW
        $distribusiRtRw = (clone $pelangganQuery)
            ->select('rt', 'rw')
            ->selectRaw('count(*) as jumlah')
            ->whereNotNull('rt')
            ->whereNotNull('rw')
            ->groupBy('rt', 'rw')
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'rt_rw' => "RT {$item->rt}/RW {$item->rw}",
                    'jumlah' => $item->jumlah,
                ];
            });
        
        // Stats pembayaran bulan ini
        $bulanIni = now()->format('Y-m');
        $pelangganAktifIds = (clone $pelangganQuery)->where('status_aktif', true)->pluck('id');
        
        // Pisahkan pembayaran bulan ini dan tunggakan (Cash Flow Basis)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $paymentsThisMonth = Pembayaran::whereBetween('tanggal_bayar', [$startOfMonth, $endOfMonth])
            ->whereIn('pelanggan_id', $pelangganAktifIds)
            ->get();

        $totalPembayaran = $paymentsThisMonth->sum('jumlah_bayar');
        
        // Pembayaran untuk tagihan bulan ini (tepat waktu)
        $pembayaranBulanIniSaja = $paymentsThisMonth->where('bulan_bayar', $bulanIni)->sum('jumlah_bayar');
        
        // Pembayaran untuk tagihan bulan lalu (tunggakan)
        $pembayaranTunggakan = $paymentsThisMonth->where('bulan_bayar', '<', $bulanIni)->sum('jumlah_bayar');
        
        $sudahBayarCount = $paymentsThisMonth->where('bulan_bayar', $bulanIni)->unique('pelanggan_id')->count();
        
        $belumBayarCount = $pelangganAktifIds->count() - $sudahBayarCount;
        
        // === LAPORAN KEUANGAN BULANAN ===
        // Filter by wilayah if specified
        $laporanQuery = ['bulan' => $bulanIni];
        if ($wilayahFilter) {
            $laporanQuery['wilayah'] = $wilayahFilter;
        }
        
        $laporanBulanan = \App\Models\LaporanBulanan::firstOrCreate(
            $laporanQuery,
            ['biaya_operasional_penarik' => 0]
        );
        
        // 1. Total Tarikan (semua pembayaran RECEIVED bulan ini)
        $totalTarikan = $totalPembayaran;
        
        // 2. 20% Tarikan
        $tarik20Persen = $totalTarikan * 0.20;
        
        // 3. Biaya Operasional Penarik (dar database, bisa diubah)
        $biayaOperasionalPenarik = $laporanBulanan->biaya_operasional_penarik;
        
        // 3b. Biaya PAD Desa (dari database, bisa diubah)
        $biayaPadDesa = $laporanBulanan->biaya_pad_desa ?? 0;

        // 3c. Biaya Tambahan Baru
        $biayaOperasionalLapangan = $laporanBulanan->biaya_operasional_lapangan ?? 0;
        $biayaLainLain = $laporanBulanan->biaya_lain_lain ?? 0;
        
        // 4. Honor Penarik = 20% + Operasional
        $honorPenarik = $tarik20Persen + $biayaOperasionalPenarik;
        
        // 5. Total Tarikan Bersih = Total - Honor Penarik - PAD Desa - Operasional Lapangan - Lain-lain
        $totalTarikanBersih = $totalTarikan - $honorPenarik - $biayaPadDesa - $biayaOperasionalLapangan - $biayaLainLain;
        
        // 6. Total SR (Sambungan Rumah) Sudah Bayar
        $totalSRSudahBayar = $sudahBayarCount;
        
        // 7. Total SR Belum Bayar  
        $totalSRBelumBayar = $belumBayarCount;
        
        // 8. Total SR (Pelanggan Aktif)
        $totalSR = $pelangganAktifIds->count();
        
        // === Hitung Saldo Awal (Akumulasi Bulan Sebelumnya) ===
        $previousLimit = $startOfMonth; 
        
        // A. Pemasukan Lalu (Cash Flow: Received BEFORE start of this month)
        $queryLalu = Pembayaran::query();
        $queryLalu->where('tanggal_bayar', '<', $previousLimit);
        // Admin bisa filter wilayah manual
        if ($wilayahFilter && auth()->user()->isAdmin()) {
            $queryLalu->whereHas('pelanggan', function ($q) use ($wilayahFilter) {
                $q->where('wilayah', $wilayahFilter);
            });
        }
        $pemasukanLalu = $queryLalu->sum('jumlah_bayar');

        // B. Pengeluaran Lalu (Biaya Operasional - Accrual by Month is fine as expense usually booked per month)
        // Filter laporan bulanan sebelum bulan ini
        $laporanLaluQuery = \App\Models\LaporanBulanan::query();
        $laporanLaluQuery->where('bulan', '<', $bulanIni); // $bulanIni is "YYYY-MM"
        if ($wilayahFilter) {
             $laporanLaluQuery->where('wilayah', $wilayahFilter);
        }

        $biayaOpsPenarikLalu = $laporanLaluQuery->sum('biaya_operasional_penarik');
        $biayaPadDesaLalu = $laporanLaluQuery->sum('biaya_pad_desa');
        $biayaOpsLapanganLalu = $laporanLaluQuery->sum('biaya_operasional_lapangan');
        $biayaLainLainLalu = $laporanLaluQuery->sum('biaya_lain_lain');
        
        $totalBiayaLalu = $biayaOpsPenarikLalu + $biayaPadDesaLalu + $biayaOpsLapanganLalu + $biayaLainLainLalu;

        // C. Hitung Saldo Awal Bersih
        // Net Profit = (0.8 * Revenue) - Expenses
        $saldoAwal = ($pemasukanLalu * 0.80) - $totalBiayaLalu;
        
        // Pelanggan yang belum bayar bulan ini (untuk list)
        // Gunakan forUser() untuk filter otomatis berdasarkan role
        $pelangganBelumBayarQuery = Pelanggan::forUser()
            ->where('status_aktif', true)
            ->whereNotIn('id', function($query) use ($bulanIni) {
                // Yang sudah bayar TAGIHAN bulan ini
                $query->select('pelanggan_id')
                    ->from('pembayarans')
                    ->where('bulan_bayar', $bulanIni);
            });
        
        // Admin bisa filter wilayah manual
        if ($wilayahFilter && auth()->user()->isAdmin()) {
            $pelangganBelumBayarQuery->where('wilayah', $wilayahFilter);
        }
        
        $pelangganBelumBayar = $pelangganBelumBayarQuery
            ->orderBy('id_pelanggan')
            ->limit(10)
            ->get(['id', 'id_pelanggan', 'nama_pelanggan', 'no_whatsapp', 'rt', 'rw']);
        
        // List Transaksi Terakhir (untuk melihat siapa yang baru bayar, termasuk bayar tunggakan)
        // Filter berdasarkan wilayah user penarik
        $recentTransactionsQuery = Pembayaran::with('pelanggan')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year);
        
        // Jika penarik, filter berdasarkan wilayah
        if (auth()->user()->isPenarik() && auth()->user()->hasWilayah()) {
            $recentTransactionsQuery->whereHas('pelanggan', function ($q) {
                $q->where('wilayah', auth()->user()->getWilayah());
            });
        } elseif ($wilayahFilter && auth()->user()->isAdmin()) {
            // Admin dengan filter wilayah
            $recentTransactionsQuery->whereHas('pelanggan', function ($q) use ($wilayahFilter) {
                $q->where('wilayah', $wilayahFilter);
            });
        }
        
        $recentTransactions = $recentTransactionsQuery
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'pelanggan_nama' => $p->pelanggan->nama_pelanggan ?? 'Terhapus',
                    'pelanggan_id' => $p->pelanggan->id_pelanggan ?? '-',
                    'wilayah' => ($p->pelanggan->rt ?? '-') . '/' . ($p->pelanggan->rw ?? '-'),
                    'bulan_bayar_text' => \Carbon\Carbon::createFromFormat('Y-m', $p->bulan_bayar)->locale('id')->isoFormat('MMMM Y'),
                    'jumlah_bayar' => $p->jumlah_bayar,
                    'tanggal_bayar' => $p->tanggal_bayar->format('d/m/Y'),
                    'is_tunggakan' => $p->bulan_bayar !== now()->format('Y-m'),
                    'waktu_transaksi' => $p->created_at->diffForHumans()
                ];
            });

        // Hitung Statistik Bulanan untuk Grafik
        $monthlyStats = [];
        $currentYear = now()->year;
        
        for ($m = 1; $m <= 12; $m++) {
            $monthDate = \Carbon\Carbon::createFromDate($currentYear, $m, 1);
            $monthStr = $monthDate->format('Y-m');
            $monthName = $monthDate->locale('id')->isoFormat('MMM');
            
            // Pemasukan (Total Received in Month m)
            $pemasukan = Pembayaran::whereYear('tanggal_bayar', $currentYear)
                ->whereMonth('tanggal_bayar', $m)
                ->sum('jumlah_bayar');
            
            // Pengeluaran (Based on Report Month)
            $laporan = \App\Models\LaporanBulanan::where('bulan', $monthStr)->first();
            
            $biayaOpsPenarik = $laporan ? $laporan->biaya_operasional_penarik : 0;
            $biayaPad = $laporan ? $laporan->biaya_pad_desa : 0;
            $biayaOpsLapangan = $laporan ? $laporan->biaya_operasional_lapangan : 0;
            $biayaLain = $laporan ? $laporan->biaya_lain_lain : 0;
            
            $honorPenarik = ($pemasukan * 0.20) + $biayaOpsPenarik;
            $totalPengeluaran = $honorPenarik + $biayaPad + $biayaOpsLapangan + $biayaLain;
            
            $profit = $pemasukan - $totalPengeluaran;
            
            $monthlyStats[] = [
                'month' => $monthName,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $totalPengeluaran,
                'profit' => $profit
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalPelanggan' => $totalPelanggan,
                'pelangganAktif' => $pelangganAktif,
                'pelangganNonaktif' => $pelangganNonaktif,
                'cakupanArea' => $cakupanArea,
            ],
            'monthlyStats' => $monthlyStats,
            'pembayaran' => [
                'bulanIni' => $bulanIni,
                'pembayaranBulanIni' => $pembayaranBulanIniSaja,
                'pembayaranTunggakan' => $pembayaranTunggakan,
                'terbayar' => $totalPembayaran,
                'sudahBayarCount' => $sudahBayarCount,
                'belumBayarCount' => $belumBayarCount,
            ],
            'laporanKeuangan' => [
                'bulan' => $bulanIni,
                'totalTarikan' => $totalTarikan,
                'tarik20Persen' => $tarik20Persen,
                'biayaOperasionalPenarik' => $biayaOperasionalPenarik,
                'biayaPadDesa' => $biayaPadDesa,
                'biayaOperasionalLapangan' => $biayaOperasionalLapangan,
                'biayaLainLain' => $biayaLainLain,
                'honorPenarik' => $honorPenarik,
                'totalTarikanBersih' => $totalTarikanBersih,
                'totalSRSudahBayar' => $totalSRSudahBayar,
                'totalSRBelumBayar' => $totalSRBelumBayar,
                'totalSR' => $totalSR,
                'laporanId' => $laporanBulanan->id,
                'saldoAwal' => $saldoAwal,
            ],
            'recentTransactions' => $recentTransactions,
            'pelangganBelumBayar' => $pelangganBelumBayar,
            'distribusiRtRw' => $distribusiRtRw,
        ]);
    }
    
    public function createPelanggan()
    {
        return Inertia::render('Pelanggan/Create');
    }
    
    public function storePelanggan(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|string|max:50|unique:pelanggan,id_pelanggan',
            'nama_pelanggan' => 'required|string|max:255',
            'no_whatsapp' => 'nullable|string|max:20',
            'rt' => 'nullable|numeric|max:999',
            'rw' => 'nullable|numeric|max:999',
            'kategori' => 'required|in:umum,sosial',
            'wilayah' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|max:999',
            'longitude' => 'nullable|numeric',
            'google_maps_url' => 'nullable|url|max:500',
            'status_aktif' => 'boolean',
        ]);
        
        // Debug: Log kategori yang diterima
        \Log::info('Kategori received:', ['kategori' => $request->kategori, 'validated' => $validated['kategori']]);
        
        Pelanggan::create($validated);
        
        return redirect()->route('cek-pelanggan')->with('success', 'Pelanggan berhasil ditambahkan!');
    }
    
    public function editPelanggan(Pelanggan $pelanggan)
    {
        return Inertia::render('Pelanggan/Edit', [
            'pelanggan' => [
                'id' => $pelanggan->id,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'no_whatsapp' => $pelanggan->no_whatsapp,
                'rt' => $pelanggan->rt,
                'rw' => $pelanggan->rw,
                'kategori' => $pelanggan->kategori,
                'wilayah' => $pelanggan->wilayah,
                'latitude' => $pelanggan->latitude,
                'longitude' => $pelanggan->longitude,
                'google_maps_url' => $pelanggan->google_maps_url,
                'status_aktif' => $pelanggan->status_aktif,
            ]
        ]);
    }
    
    public function updatePelanggan(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|string|max:50|unique:pelanggan,id_pelanggan,' . $pelanggan->id,
            'nama_pelanggan' => 'required|string|max:255',
            'no_whatsapp' => 'nullable|string|max:20',
            'rt' => 'nullable|numeric|max:999',
            'rw' => 'nullable|numeric|max:999',
            'kategori' => 'required|in:umum,sosial',
            'wilayah' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_maps_url' => 'nullable|url|max:500',
            'status_aktif' => 'boolean',
        ]);
        
        $pelanggan->update($validated);
        
        return redirect()->route('cek-pelanggan')->with('success', 'Pelanggan berhasil diperbarui!');
    }
    
    public function deletePelanggan(Pelanggan $pelanggan)
    {
        // Hapus pembayaran dan tagihan terkait terlebih dahulu untuk menghindari constraint error
        $pelanggan->pembayarans()->delete();
        $pelanggan->tagihanBulanan()->delete();
        
        $pelanggan->delete();
        
        return redirect()->route('cek-pelanggan')->with('success', 'Pelanggan berhasil dihapus!');
    }
    
    public function peta(Request $request)
    {
        // Ambil lokasi dari database
        $mapSettings = \App\Models\MapSetting::where('is_active', true)->get();
        
        // Ambil kantor KP-SPAMS dari database
        $kantorData = $mapSettings->where('location_type', 'kp_spams')->first();
        $kantor = $kantorData ? [
            'name' => $kantorData->name,
            'lat' => (float) $kantorData->latitude,
            'lng' => (float) $kantorData->longitude,
            'description' => $kantorData->description,
        ] : [
            // Default ke koordinat Damar Wulan jika belum ada di database
            'name' => 'Kantor Pusat KP-SPAMS DAMAR WULAN',
            'lat' => -7.60043000,
            'lng' => 109.08140000,
            'description' => 'Sekretariat KP-SPAMS Desa',
        ];
        
        // Ambil sumber air dari database
        $sumberAirData = $mapSettings->where('location_type', 'sumber_air');
        $sumberAir = $sumberAirData->isNotEmpty() 
            ? $sumberAirData->map(function ($item) {
                return [
                    'name' => $item->name,
                    'lat' => (float) $item->latitude,
                    'lng' => (float) $item->longitude,
                    'description' => $item->description,
                ];
            })->values()->toArray() // Tambah values() untuk reset keys
            : [
                // Default ke koordinat Damar Wulan jika belum ada di database
                [
                    'name' => 'Sumber Air Curug Dammar Wulan Tiparjaya',
                    'lat' => -7.58129100,
                    'lng' => 109.08441200,
                    'description' => 'Sumber air utama yang melayani wilayah desa',
                ],
            ];
        
        // Ambil bronscap dari database
        $bronscapData = $mapSettings->where('location_type', 'bronscap');
        $bronscapList = $bronscapData->isNotEmpty() 
            ? $bronscapData->map(function ($item) {
                return [
                    'name' => $item->name,
                    'lat' => (float) $item->latitude,
                    'lng' => (float) $item->longitude,
                    'description' => $item->description,
                ];
            })->values()->toArray()
            : [];
        
        // Ambil reservoir dari database
        $reservoirData = $mapSettings->where('location_type', 'reservoir');
        $reservoirList = $reservoirData->isNotEmpty() 
            ? $reservoirData->map(function ($item) {
                return [
                    'name' => $item->name,
                    'lat' => (float) $item->latitude,
                    'lng' => (float) $item->longitude,
                    'description' => $item->description,
                ];
            })->values()->toArray()
            : [];
        
        // Ambil semua pelanggan yang memiliki koordinat valid (tidak null dan tidak 0)
        $pelangganList = Pelanggan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->get()
            ->map(function ($pelanggan) {
                return [
                    'id' => $pelanggan->id,
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'nama_pelanggan' => $pelanggan->nama_pelanggan,
                    'no_whatsapp' => $pelanggan->no_whatsapp,
                    'rt' => $pelanggan->rt,
                    'rw' => $pelanggan->rw,
                    'wilayah' => $pelanggan->wilayah,
                    'status_aktif' => $pelanggan->status_aktif,
                    'latitude' => (float) $pelanggan->latitude,
                    'longitude' => (float) $pelanggan->longitude,
                    'google_maps_url' => $pelanggan->google_maps_url,
                ];
            })
            ->toArray();
        
        // Highlight pelanggan jika ada parameter
        $highlightPelanggan = $request->query('pelanggan');
        
        return Inertia::render('Peta', [
            'kantor' => $kantor,
            'sumberAir' => $sumberAir,
            'bronscapList' => $bronscapList,
            'reservoirList' => $reservoirList,
            'pelangganList' => $pelangganList,
            'highlightPelanggan' => $highlightPelanggan,
        ]);
    }
    
    public function updateBiayaOperasional(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|string|size:7', // Format: YYYY-MM
            'biaya_operasional_penarik' => 'required|numeric|min:0',
            'biaya_pad_desa' => 'nullable|numeric|min:0',
            'biaya_operasional_lapangan' => 'nullable|numeric|min:0',
            'biaya_lain_lain' => 'nullable|numeric|min:0',
            'wilayah' => 'nullable|string|max:100',
        ]);
        
        $laporanQuery = ['bulan' => $validated['bulan']];
        if (isset($validated['wilayah'])) {
            $laporanQuery['wilayah'] = $validated['wilayah'];
        }
        
        $updateData = [
            'biaya_operasional_penarik' => $validated['biaya_operasional_penarik'],
        ];
        
        if (isset($validated['biaya_pad_desa'])) {
            $updateData['biaya_pad_desa'] = $validated['biaya_pad_desa'];
        }

        if (isset($validated['biaya_operasional_lapangan'])) {
            $updateData['biaya_operasional_lapangan'] = $validated['biaya_operasional_lapangan'];
        }

        if (isset($validated['biaya_lain_lain'])) {
            $updateData['biaya_lain_lain'] = $validated['biaya_lain_lain'];
        }
        
        if (isset($validated['wilayah'])) {
             // Specific update for a region
             \App\Models\LaporanBulanan::updateOrCreate(
                 ['bulan' => $validated['bulan'], 'wilayah' => $validated['wilayah']], 
                 $updateData
             );
        } else {
             // General update (No wilayah filter selected - e.g. "Semua Wilayah")
             // We need to ensure the TOTAL sum matches the user input.
             // Strategy: Find all records for this month. 
             // Update the first one to the value and set others to 0.
             
             $reports = \App\Models\LaporanBulanan::where('bulan', $validated['bulan'])->get();
             
             if ($reports->isEmpty()) {
                 // No record exists, create one with default wilayah
                 \App\Models\LaporanBulanan::create(array_merge($updateData, [
                     'bulan' => $validated['bulan'], 
                     'wilayah' => 'Dawuhan' // Default
                 ]));
             } else {
                 $first = true;
                 foreach($reports as $report) {
                     if ($first) {
                         // Set the first record to the target value
                         $report->update($updateData);
                         $first = false;
                     } else {
                         // Reset others to 0 so the SUM matches the user input
                         $resetData = ['biaya_operasional_penarik' => 0];
                         if (isset($updateData['biaya_pad_desa'])) {
                             $resetData['biaya_pad_desa'] = 0;
                         }
                         if (isset($updateData['biaya_operasional_lapangan'])) {
                             $resetData['biaya_operasional_lapangan'] = 0;
                         }
                         if (isset($updateData['biaya_lain_lain'])) {
                             $resetData['biaya_lain_lain'] = 0;
                         }
                         $report->update($resetData);
                     }
                 }
             }
        }
        
        return redirect()->back()->with('success', 'Biaya operasional dan PAD Desa berhasil diperbarui!');
    }

    public function exportPelangganExcel(Request $request)
    {
        $data = $this->getFilteredPelanggan($request);
        
        $fileName = 'Data_Pelanggan_' . now()->format('YmdHis') . '.xlsx';

        return Excel::download(
            new PelangganExport($data['pelanggan'], $data['filters']),
            $fileName
        );
    }

    public function exportPelangganPdf(Request $request)
    {
        $data = $this->getFilteredPelanggan($request);
        
        $pdf = Pdf::loadView('pelanggan-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Data_Pelanggan_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($fileName);
    }

    private function getFilteredPelanggan(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', 'all');
        $bulan = $request->input('bulan', 'all');
        $wilayah = $request->input('wilayah', 'all');
        $bulanIni = now()->format('Y-m');

        $query = Pelanggan::with(['pembayarans']);

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_pelanggan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('rt', 'like', "%{$search}%")
                  ->orWhere('rw', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status === 'aktif') {
            $query->where('status_aktif', true);
        } elseif ($status === 'nonaktif') {
            $query->where('status_aktif', false);
        }

        // Filter wilayah
        if ($wilayah !== 'all') {
            $query->where('wilayah', $wilayah);
        }

        $pelangganList = $query->orderBy('id_pelanggan', 'asc')->get()
            ->map(function ($p) use ($bulanIni) {
                $pembayaranBulanIni = $p->pembayarans->firstWhere('bulan_bayar', $bulanIni);
                $isSosial = $p->kategori === 'sosial';
                $sudahBayar = $isSosial ? true : !is_null($pembayaranBulanIni);

                return (object) [
                    'id' => $p->id,
                    'id_pelanggan' => $p->id_pelanggan,
                    'nama' => $p->nama_pelanggan,
                    'alamat' => $p->alamat,
                    'rt' => $p->rt,
                    'rw' => $p->rw,
                    'wilayah' => $p->wilayah,
                    'no_whatsapp' => $p->no_whatsapp,
                    'kategori' => $p->kategori ?? 'umum',
                    'status_aktif' => $p->status_aktif,
                    'tanggal_pasang' => $p->tanggal_pasang,
                    'sudah_bayar' => $sudahBayar,
                    'tanggal_bayar' => $isSosial ? null : $pembayaranBulanIni?->tanggal_bayar,
                    'jumlah_bayar' => $isSosial ? 0 : $pembayaranBulanIni?->jumlah_bayar,
                ];
            });

        // Filter bulan (sudah bayar / belum bayar)
        if ($bulan !== 'all') {
            $pelangganList = $pelangganList->filter(function ($p) use ($bulan) {
                return $bulan === 'sudah' ? $p->sudah_bayar : !$p->sudah_bayar;
            })->values();
        }

        $stats = [
            'total' => $pelangganList->count(),
            'aktif' => $pelangganList->where('status_aktif', true)->count(),
            'sudah_bayar' => $pelangganList->where('sudah_bayar', true)->count(),
        ];

        return [
            'pelanggan' => $pelangganList,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'bulan' => $bulan,
                'wilayah' => $wilayah,
            ],
        ];
    }
}
