<?php

namespace App\Http\Controllers;

use App\Models\LaporanBulanan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Exports\LaporanExport;
use App\Helpers\WilayahHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private const GO_LIVE_MONTH = '2026-02';

    public function index(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->year);
        $bulan = $request->input('bulan', Carbon::now()->format('m')); // 01-12 or 'semua'
        $wilayah = $request->input('wilayah', 'semua');
        $akumulasi = $request->input('akumulasi', '0'); // 0 = bulan ini saja, 1 = dari Januari s/d bulan ini
        $selectedMonth = ($bulan && $bulan !== 'semua') ? ($tahun . '-' . $bulan) : null;
        $isBeforeGoLive = $selectedMonth ? (strcmp($selectedMonth, self::GO_LIVE_MONTH) < 0) : false;
        $startMonthForYear = strcmp($tahun . '-01', self::GO_LIVE_MONTH) < 0
            ? self::GO_LIVE_MONTH
            : ($tahun . '-01');

        // === 1. Query Data Pembayaran (Untuk Tabel & Total Tarikan) ===
        // Get active pelanggan IDs based on user role and filters
        $pelangganQuery = Pelanggan::forUser()->where('status_aktif', true);
        
        // Filter Wilayah untuk penarik
        if (auth()->user()->isPenarik() && auth()->user()->hasWilayah()) {
            // Penarik hanya bisa lihat wilayahnya sendiri (already handled by forUser)
        } elseif ($wilayah && $wilayah !== 'semua') {
            // Admin bisa filter wilayah manual (menggunakan normalized matching)
            $pelangganQuery->where(function ($q) use ($wilayah) {
                $q->byWilayah($wilayah) // Use normalized scope
                  ->orWhere('rw', $wilayah)
                  ->orWhere('rt', $wilayah);
            });
        }
        
        $pelangganAktifIds = $pelangganQuery->pluck('id');

        $query = Pembayaran::with('pelanggan');

        // Filter Tahun & Bulan via bulan_bayar
        if ($bulan && $bulan !== 'semua') {
            $bulanFormat = $tahun . '-' . $bulan;
            if ($isBeforeGoLive) {
                // Periode sebelum go-live dianggap tidak ada transaksi operasional.
                $query->whereRaw('1 = 0');
            } elseif ($akumulasi == '1') {
                $query->where('bulan_bayar', '>=', $startMonthForYear)
                    ->where('bulan_bayar', '<=', $bulanFormat);
            } else {
                // Hanya bulan yang dipilih
                $query->where('bulan_bayar', $bulanFormat);
            }
        } else {
             // Semua bulan di tahun ini, mulai dari go-live.
            $query->where('bulan_bayar', 'like', $tahun . '-%')
                ->where('bulan_bayar', '>=', $startMonthForYear);
        }

        // Filter by active pelanggan
        $query->whereIn('pelanggan_id', $pelangganAktifIds);

        $pembayarans = $query->latest('tanggal_bayar')->get();
        
        // Calculate total based on billing month PLUS tunggakan (like Dashboard)
        $pembayaranBulanIni = $pembayarans->sum('jumlah_bayar');
        
        // Add tunggakan (pembayaran bulan lalu yang dibayar di periode ini)
        $pembayaranTunggakan = 0;
        if ($bulan && $bulan !== 'semua' && $akumulasi != '1' && !$isBeforeGoLive) {
            // Hanya hitung tunggakan jika bukan mode akumulasi
            $bulanFormat = $tahun . '-' . $bulan;
            $startOfMonth = Carbon::createFromFormat('Y-m', $bulanFormat)->startOfMonth();
            $endOfMonth = Carbon::createFromFormat('Y-m', $bulanFormat)->endOfMonth();
            
            $pembayaranTunggakan = Pembayaran::where('bulan_bayar', '<', $bulanFormat)
            ->where('bulan_bayar', '>=', self::GO_LIVE_MONTH)
                ->whereBetween('tanggal_bayar', [$startOfMonth, $endOfMonth])
                ->whereIn('pelanggan_id', $pelangganAktifIds)
                ->sum('jumlah_bayar');
        }
        
        // Total Pemasukan / Total Tarikan (including tunggakan)
        $totalPemasukan = $pembayaranBulanIni + $pembayaranTunggakan;
        $totalKubik = $pembayarans->sum('jumlah_kubik');
        $totalTransaksi = $pembayarans->count();

        // Detail siapa saja yang bayar tunggakan bulan ini
        $detailPembayaranTunggakan = [];
        if ($pembayaranTunggakan > 0 && $bulan && $bulan !== 'semua' && $akumulasi != '1' && !$isBeforeGoLive) {
            $bulanFormat = $tahun . '-' . $bulan;
            $startOfMonth = Carbon::createFromFormat('Y-m', $bulanFormat)->startOfMonth();
            $endOfMonth   = Carbon::createFromFormat('Y-m', $bulanFormat)->endOfMonth();
            $detailPembayaranTunggakan = Pembayaran::with('pelanggan:id,id_pelanggan,nama_pelanggan')
                ->where('bulan_bayar', '<', $bulanFormat)
            ->where('bulan_bayar', '>=', self::GO_LIVE_MONTH)
                ->whereBetween('tanggal_bayar', [$startOfMonth, $endOfMonth])
                ->whereIn('pelanggan_id', $pelangganAktifIds)
                ->get()
                ->map(fn($p) => [
                    'id_pelanggan'   => $p->pelanggan->id_pelanggan ?? '-',
                    'nama_pelanggan' => $p->pelanggan->nama_pelanggan ?? '-',
                    'bulan_bayar'    => $p->bulan_bayar,
                    'jumlah_bayar'   => $p->jumlah_bayar,
                ])->toArray();
        }
        
        // Calculate by kategori (case-insensitive and trim whitespace)
        $pemasukanUmum = $pembayarans->filter(function($p) { 
            $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
            return $kategori === 'umum'; 
        })->sum('jumlah_bayar');
        
        $pemasukanSosial = $pembayarans->filter(function($p) { 
            $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
            return $kategori === 'sosial'; 
        })->sum('jumlah_bayar');
        
        $transaksiUmum = $pembayarans->filter(function($p) { 
            $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
            return $kategori === 'umum'; 
        })->count();
        
        $transaksiSosial = $pembayarans->filter(function($p) { 
            $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
            return $kategori === 'sosial'; 
        })->count();
        
        // Hitung jumlah pelanggan/bangunan aktif per kategori (dari semua pelanggan aktif, bukan hanya yang bayar)
        $allPelangganAktif = Pelanggan::whereIn('id', $pelangganAktifIds)->get();
        
        $pelangganUmum = $allPelangganAktif->filter(function($p) { 
            $kategori = strtolower(trim($p->kategori ?? 'umum'));
            return $kategori === 'umum'; 
        })->count();
        
        $pelangganSosial = $allPelangganAktif->filter(function($p) { 
            $kategori = strtolower(trim($p->kategori ?? 'umum'));
            return $kategori === 'sosial'; 
        })->count();

        // === 2. Hitung Detail Keuangan (Mirip Dashboard) ===
        
        // A. 20% Tarikan
        $tarik20Persen = $totalPemasukan * 0.20;

        // B. Biaya Operasional
        $biayaOperasional = 0;
        $biayaPadDesa = 0;
        
        $laporanQuery = LaporanBulanan::query();
        if ($bulan && $bulan !== 'semua') {
            // Format di DB LaporanBulanan biasanya Y-m, misal "2024-01"
            $bulanFormat = $tahun . '-' . $bulan;
            
            // Jika akumulasi ON, ambil dari Januari sampai bulan yang dipilih
            if ($akumulasi == '1') {
                $laporanQuery->where('bulan', '>=', $tahun . '-01')
                             ->where('bulan', '<=', $bulanFormat);
            } else {
                $laporanQuery->where('bulan', $bulanFormat);
            }
        } else {
            // Jika semua bulan, ambil semua operasional di tahun tersebut
            $laporanQuery->where('bulan', 'like', $tahun . '-%');
        }
        
        // Jika filter wilayah ada (penarik otomatis, admin manual)
        if (auth()->user()->isPenarik() && auth()->user()->hasWilayah()) {
            $wilayahNormalized = WilayahHelper::normalize(auth()->user()->getWilayah());
            $sqlExpr = WilayahHelper::getSqlExpression();
            $laporanQuery->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized]);
        } elseif ($wilayah && $wilayah !== 'semua') {
            $wilayahNormalized = WilayahHelper::normalize($wilayah);
            $sqlExpr = WilayahHelper::getSqlExpression();
            $laporanQuery->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized]);
        }

        $biayaOperasional = $laporanQuery->sum('biaya_operasional_penarik');
        $biayaPadDesa = $laporanQuery->sum('biaya_pad_desa');
        $biayaOpsLapangan = $laporanQuery->sum('biaya_operasional_lapangan');
        $biayaLainLain = $laporanQuery->sum('biaya_lain_lain');
        $biayaCSR = $laporanQuery->sum('biaya_csr');

        // C. Honor Penarik
        $honorPenarik = $tarik20Persen + $biayaOperasional;

        // D. Total Abunemen (berdasarkan transaksi yang mencentang abunemen)
        $jumlahTransaksiAbunemen = $pembayarans->where('abunemen', true)->count();
        $totalAbunemen = $jumlahTransaksiAbunemen * 3000;

        // E. Total Tarikan Bersih (dikurangi honor penarik, PAD Desa, Ops Lapangan, Lain-lain, dan CSR)
        $totalTarikanBersih = $totalPemasukan - $honorPenarik - $biayaPadDesa - $biayaOpsLapangan - $biayaLainLain - $biayaCSR;

        // === 3. Statistik SR (Sambungan Rumah) ===
        // Use pelanggan IDs already calculated earlier
        $totalSR = $pelangganAktifIds->count();

        // Hitung SR yang sudah bayar (unique pelanggan_id di pembayaran yang difilter)
        $srSudahBayar = $pembayarans->unique('pelanggan_id')->count();
        $srBelumBayar = max(0, $totalSR - $srSudahBayar);

        // === 4. Opsi Filter ===
        $tahunOpsi = Pembayaran::selectRaw('LEFT(bulan_bayar, 4) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();
        if (!in_array(Carbon::now()->year, $tahunOpsi)) {
            array_unshift($tahunOpsi, Carbon::now()->year);
        }

        // Ambil opsi wilayah untuk dropdown (dengan normalisasi)
        $wilayahOpsi = Pelanggan::select('wilayah')
            ->whereNotNull('wilayah')
            ->where('wilayah', '!=', '')
            ->distinct()
            ->orderBy('wilayah')
            ->pluck('wilayah')
            ->map(function($w) {
                return trim($w);
            })
            ->unique()
            ->values()
            ->toArray();

        // === 5. Distribusi Tunggakan per Wilayah ===
        $bulanFilter = ($bulan && $bulan !== 'semua') ? $tahun . '-' . $bulan : now()->format('Y-m');
        $isBeforeGoLiveDistribusi = strcmp($bulanFilter, self::GO_LIVE_MONTH) < 0;
        $sqlExpr = WilayahHelper::getSqlExpression();
        
        $distribusiWilayah = (clone $pelangganQuery)
            ->selectRaw("MAX(wilayah) as wilayah")
            ->selectRaw("count(*) as jumlah")
            ->selectRaw("{$sqlExpr} as wilayah_normalized")
            ->whereNotNull('wilayah')
            ->where('wilayah', '!=', '')
            ->where('status_aktif', true)
            ->groupByRaw($sqlExpr)
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function ($item) use ($bulanFilter, $isBeforeGoLiveDistribusi) {
                $sqlExpr = WilayahHelper::getSqlExpression();
                
                $pelangganIds = Pelanggan::forUser()
                    ->where('status_aktif', true)
                    ->whereRaw("{$sqlExpr} = ?", [$item->wilayah_normalized])
                    ->pluck('id');
                
                // Hitung yang sudah bayar bulan filter
                $sudahBayar = 0;
                if (!$isBeforeGoLiveDistribusi) {
                    $sudahBayar = Pembayaran::where('bulan_bayar', $bulanFilter)
                        ->whereIn('pelanggan_id', $pelangganIds)
                        ->distinct('pelanggan_id')
                        ->count('pelanggan_id');
                }
                
                $belumBayar = $pelangganIds->count() - $sudahBayar;
                
                // Hitung tunggakan (bulan sebelumnya yang belum bayar)
                $tunggakanData = \App\Models\TagihanBulanan::where('bulan', '<', $bulanFilter)
                    ->where('bulan', '>=', self::GO_LIVE_MONTH)
                    ->whereIn('status_bayar', ['BELUM_BAYAR', 'TUNGGAKAN', 'CICILAN'])
                    ->whereIn('pelanggan_id', $pelangganIds)
                    ->with('pelanggan:id,id_pelanggan,nama_pelanggan')
                    ->whereRaw('(total_tagihan - COALESCE(jumlah_terbayar, 0)) > 0')
                    ->select('pelanggan_id', \DB::raw('COUNT(*) as jumlah_bulan'), \DB::raw("GROUP_CONCAT(bulan ORDER BY bulan ASC SEPARATOR ', ') as daftar_bulan"), \DB::raw('SUM(total_tagihan - COALESCE(jumlah_terbayar, 0)) as total_tunggakan'))
                    ->groupBy('pelanggan_id')
                    ->get();
                
                $tunggakanCount = $tunggakanData->count();
                
                // Detail pelanggan yang nunggak
                $detailTunggakan = $tunggakanData->map(function($t) {
                    $bulanList = array_filter(explode(', ', $t->daftar_bulan ?? ''));
                    $formattedBulan = collect($bulanList)->map(function ($b) {
                        $parts = explode('-', $b);
                        if (count($parts) != 2) return $b;
                        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $m = (int)$parts[1];
                        return ($months[$m] ?? '') . ' ' . $parts[0];
                    })->implode(', ');
                    return [
                        'id_pelanggan' => $t->pelanggan->id_pelanggan ?? 'N/A',
                        'nama_pelanggan' => $t->pelanggan->nama_pelanggan ?? 'N/A',
                        'jumlah_bulan' => $t->jumlah_bulan,
                        'total_tunggakan' => $t->total_tunggakan,
                        'daftar_bulan' => $formattedBulan,
                    ];
                })->toArray();
                
                return [
                    'wilayah' => ucwords($item->wilayah_normalized),
                    'jumlah' => $item->jumlah,
                    'sudah_bayar' => $sudahBayar,
                    'belum_bayar' => $belumBayar,
                    'tunggakan' => $tunggakanCount,
                    'detail_tunggakan' => $detailTunggakan,
                ];
            });

        return Inertia::render('Laporan/Index', [
            'data' => $pembayarans,
            'summary' => [
                'pemasukan' => $totalPemasukan,
                'pemasukanBulanan' => $pembayaranBulanIni,
                'pemasukanTunggakan' => $pembayaranTunggakan,
                'detailPembayaranTunggakan' => $detailPembayaranTunggakan,
                'kubikasi' => $totalKubik,
                'transaksi' => $totalTransaksi,
                'pemasukanUmum' => $pemasukanUmum,
                'pemasukanSosial' => $pemasukanSosial,
                'transaksiUmum' => $transaksiUmum,
                'transaksiSosial' => $transaksiSosial,
                'pelangganUmum' => $pelangganUmum,
                'pelangganSosial' => $pelangganSosial,
            ],
            'detail' => [
                'totalTarikan' => $totalPemasukan,
                'tarik20Persen' => $tarik20Persen,
                'biayaOperasional' => $biayaOperasional,
                'biayaPadDesa' => $biayaPadDesa,
                'biayaOpsLapangan' => $biayaOpsLapangan,
                'biayaLainLain' => $biayaLainLain,
                'biayaCSR' => $biayaCSR,
                'honorPenarik' => $honorPenarik, // 20% + Ops
                'honorMurni' => $honorPenarik,   // Sama, penamaan beda konteks
                'totalAbunemen' => $totalAbunemen,
                'jumlahTransaksiAbunemen' => $jumlahTransaksiAbunemen,
                'totalTarikanBersih' => $totalTarikanBersih,
                'totalSR' => $totalSR,
                'srSudahBayar' => $srSudahBayar,
                'srBelumBayar' => $srBelumBayar,
            ],
            'filters' => [
                'tahun' => (int)$tahun,
                'bulan' => $bulan,
                'wilayah' => $wilayah,
            ],
            'options' => [
                'tahun' => array_values(array_unique($tahunOpsi)),
                'wilayah' => array_values(array_unique($wilayahOpsi)),
            ],
            'distribusiWilayah' => $distribusiWilayah,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getFilteredData($request);
        
        $fileName = 'Laporan_Keuangan_' . $data['filters']['tahun'];
        if ($data['filters']['bulan'] !== 'semua') {
            $fileName .= '_' . $this->getBulanName($data['filters']['bulan']);
        }
        $fileName .= '_' . now()->format('YmdHis') . '.xlsx';

        return Excel::download(
            new LaporanExport(
                $data['data'],
                $data['summary'],
                $data['detail'],
                $data['filters'],
                isset($data['distribusiWilayah']) ? $data['distribusiWilayah']->toArray() : []
            ),
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);
        
        // Prepare data for PDF view
        $tahun = $data['filters']['tahun'];
        $bulan = $data['filters']['bulan'];
        $wilayah = $data['filters']['wilayah'];
        
        // Get bulan name
        $bulanName = ($bulan !== 'semua') ? $this->getBulanName($bulan) : 'Semua Bulan';
        
        // Query for laporan bulanan
        $laporanQuery = LaporanBulanan::query();
        if ($bulan && $bulan !== 'semua') {
            $bulanFormat = $tahun . '-' . $bulan;
            $laporanQuery->where('bulan', $bulanFormat);
        } else {
            $laporanQuery->where('bulan', 'like', $tahun . '-%');
        }
        
        if ($wilayah && $wilayah !== 'semua') {
            $wilayahNormalized = WilayahHelper::normalize($wilayah);
            $sqlExpr = WilayahHelper::getSqlExpression();
            $laporanQuery->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized]);
        }
        
        $laporanBulanan = $laporanQuery->get();
        
        // Calculate totals
        $totalPengeluaran = $laporanBulanan->sum(function($laporan) {
            return ($laporan->biaya_operasional_penarik ?? 0) + ($laporan->biaya_operasional_lainnya ?? 0);
        });
        
        $totalPemasukan = $data['summary']['pemasukan'];
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;
        
        // Count active customers
        // Apply filter wilayah berdasarkan user yang login
        $pelangganQuery = Pelanggan::forUser()->where('status_aktif', true);
        if ($wilayah && $wilayah !== 'semua' && auth()->user()->isAdmin()) {
            // Admin bisa filter wilayah manual
            $pelangganQuery->where(function ($q) use ($wilayah) {
                $q->where('wilayah', $wilayah)
                  ->orWhere('rw', $wilayah)
                  ->orWhere('rt', $wilayah);
            });
        }
        $totalPelangganAktif = $pelangganQuery->count();
        
        $pdfData = [
            'filters' => $data['filters'],
            'bulanName' => $bulanName,
            'pembayarans' => $data['data'],
            'laporanBulanan' => $laporanBulanan,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoAkhir' => $saldoAkhir,
            'totalPelangganAktif' => $totalPelangganAktif,
            'distribusiWilayah' => isset($data['distribusiWilayah']) ? $data['distribusiWilayah']->toArray() : [],
        ];
        
        $pdf = Pdf::loadView('laporan-pdf', $pdfData)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif'
            ]);

        $fileName = 'Laporan_Keuangan_' . $data['filters']['tahun'];
        if ($data['filters']['bulan'] !== 'semua') {
            $fileName .= '_' . $this->getBulanName($data['filters']['bulan']);
        }
        $fileName .= '_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($fileName);
    }

    private function getFilteredData(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->year);
        $bulan = $request->input('bulan', Carbon::now()->format('m'));
        $wilayah = $request->input('wilayah', 'semua');
        $selectedMonth = ($bulan && $bulan !== 'semua') ? ($tahun . '-' . $bulan) : null;
        $isBeforeGoLive = $selectedMonth ? (strcmp($selectedMonth, self::GO_LIVE_MONTH) < 0) : false;
        $startMonthForYear = strcmp($tahun . '-01', self::GO_LIVE_MONTH) < 0
            ? self::GO_LIVE_MONTH
            : ($tahun . '-01');

        // Get active pelanggan IDs based on user role and filters
        $pelangganQuery = Pelanggan::forUser()->where('status_aktif', true);
        
        // Filter Wilayah untuk penarik
        if (auth()->user()->isPenarik() && auth()->user()->hasWilayah()) {
            // Penarik hanya bisa lihat wilayahnya sendiri (already handled by forUser)
        } elseif ($wilayah && $wilayah !== 'semua') {
            // Admin bisa filter wilayah manual (case-insensitive)
            $wilayahNormalized = WilayahHelper::normalize($wilayah);
            $sqlExpr = WilayahHelper::getSqlExpression();
            $pelangganQuery->where(function ($q) use ($wilayahNormalized, $sqlExpr, $wilayah) {
                $q->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized])
                  ->orWhere('rw', $wilayah)
                  ->orWhere('rt', $wilayah);
            });
        }
        
        $pelangganAktifIds = $pelangganQuery->pluck('id');

        // Query Data Pembayaran
        $query = Pembayaran::with('pelanggan');

        if ($bulan && $bulan !== 'semua') {
            $bulanFormat = $tahun . '-' . $bulan;
            if ($isBeforeGoLive) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('bulan_bayar', $bulanFormat);
            }
        } else {
            $query->where('bulan_bayar', 'like', $tahun . '-%')
                ->where('bulan_bayar', '>=', $startMonthForYear);
        }

        // Filter by active pelanggan
        $query->whereIn('pelanggan_id', $pelangganAktifIds);

        $pembayarans = $query->latest('tanggal_bayar')->get();
        
        // Calculate total based on billing month PLUS tunggakan (like Dashboard)
        $pembayaranBulanIni = $pembayarans->sum('jumlah_bayar');
        
        // Add tunggakan (pembayaran bulan lalu yang dibayar di periode ini)
        $pembayaranTunggakan = 0;
        if ($bulan && $bulan !== 'semua' && !$isBeforeGoLive) {
            $bulanFormat = $tahun . '-' . $bulan;
            $startOfMonth = Carbon::createFromFormat('Y-m', $bulanFormat)->startOfMonth();
            $endOfMonth = Carbon::createFromFormat('Y-m', $bulanFormat)->endOfMonth();
            
            $pembayaranTunggakan = Pembayaran::where('bulan_bayar', '<', $bulanFormat)
            ->where('bulan_bayar', '>=', self::GO_LIVE_MONTH)
                ->whereBetween('tanggal_bayar', [$startOfMonth, $endOfMonth])
                ->whereIn('pelanggan_id', $pelangganAktifIds)
                ->sum('jumlah_bayar');
        }
        
        $totalPemasukan = $pembayaranBulanIni + $pembayaranTunggakan;
        $totalKubik = $pembayarans->sum('jumlah_kubik');
        $totalTransaksi = $pembayarans->count();

        // Detail siapa saja yang bayar tunggakan bulan ini
        $detailPembayaranTunggakan = [];
        if ($pembayaranTunggakan > 0 && $bulan && $bulan !== 'semua' && !$isBeforeGoLive) {
            $bulanFormat2 = $tahun . '-' . $bulan;
            $startOfMonth2 = Carbon::createFromFormat('Y-m', $bulanFormat2)->startOfMonth();
            $endOfMonth2   = Carbon::createFromFormat('Y-m', $bulanFormat2)->endOfMonth();
            $detailPembayaranTunggakan = Pembayaran::with('pelanggan:id,id_pelanggan,nama_pelanggan')
                ->where('bulan_bayar', '<', $bulanFormat2)
            ->where('bulan_bayar', '>=', self::GO_LIVE_MONTH)
                ->whereBetween('tanggal_bayar', [$startOfMonth2, $endOfMonth2])
                ->whereIn('pelanggan_id', $pelangganAktifIds)
                ->get()
                ->map(fn($p) => [
                    'id_pelanggan'   => $p->pelanggan->id_pelanggan ?? '-',
                    'nama_pelanggan' => $p->pelanggan->nama_pelanggan ?? '-',
                    'bulan_bayar'    => $p->bulan_bayar,
                    'jumlah_bayar'   => $p->jumlah_bayar,
                ])->toArray();
        }
        
        $pemasukanSosial = $pembayarans->filter(function($p) { 
            $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
            return $kategori === 'sosial'; 
        })->sum('jumlah_bayar');
        
        $transaksiUmum = $pembayarans->filter(function($p) { 
            $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
            return $kategori === 'umum'; 
        })->count();
        
        $transaksiSosial = $pembayarans->filter(function($p) { 
            $kategori = strtolower(trim($p->pelanggan->kategori ?? 'umum'));
            return $kategori === 'sosial'; 
        })->count();
        
        // Hitung jumlah pelanggan/bangunan aktif per kategori (dari semua pelanggan aktif, bukan hanya yang bayar)
        $allPelangganAktif = Pelanggan::whereIn('id', $pelangganAktifIds)->get();
        
        $pelangganUmum = $allPelangganAktif->filter(function($p) { 
            $kategori = strtolower(trim($p->kategori ?? 'umum'));
            return $kategori === 'umum'; 
        })->count();
        
        $pelangganSosial = $allPelangganAktif->filter(function($p) { 
            $kategori = strtolower(trim($p->kategori ?? 'umum'));
            return $kategori === 'sosial'; 
        })->count();

        // Hitung Detail Keuangan
        $tarik20Persen = $totalPemasukan * 0.20;

        $laporanQuery = LaporanBulanan::query();
        if ($bulan && $bulan !== 'semua') {
            $bulanFormat = $tahun . '-' . $bulan;
            $laporanQuery->where('bulan', $bulanFormat);
        } else {
            $laporanQuery->where('bulan', 'like', $tahun . '-%');
        }
        
        // Jika filter wilayah ada (penarik otomatis, admin manual)
        if (auth()->user()->isPenarik() && auth()->user()->hasWilayah()) {
            $wilayahNormalized = WilayahHelper::normalize(auth()->user()->getWilayah());
            $sqlExpr = WilayahHelper::getSqlExpression();
            $laporanQuery->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized]);
        } elseif ($wilayah && $wilayah !== 'semua') {
            $wilayahNormalized = WilayahHelper::normalize($wilayah);
            $sqlExpr = WilayahHelper::getSqlExpression();
            $laporanQuery->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized]);
        }

        $biayaOperasional = $laporanQuery->sum('biaya_operasional_penarik');
        $biayaPadDesa = $laporanQuery->sum('biaya_pad_desa');
        $biayaOpsLapangan = $laporanQuery->sum('biaya_operasional_lapangan');
        $biayaLainLain = $laporanQuery->sum('biaya_lain_lain');
        $biayaCSR = $laporanQuery->sum('biaya_csr');
        $honorPenarik = $tarik20Persen + $biayaOperasional;
        $jumlahTransaksiAbunemen = $pembayarans->where('abunemen', true)->count();
        $totalAbunemen = $jumlahTransaksiAbunemen * 3000;
        $totalTarikanBersih = $totalPemasukan - $honorPenarik - $biayaPadDesa - $biayaOpsLapangan - $biayaLainLain - $biayaCSR;

        // Statistik SR - use pelanggan IDs already calculated
        $totalSR = $pelangganAktifIds->count();
        $srSudahBayar = $pembayarans->unique('pelanggan_id')->count();
        $srBelumBayar = max(0, $totalSR - $srSudahBayar);

        // Distribusi Tunggakan per Wilayah
        $bulanFilter = ($bulan && $bulan !== 'semua') ? $tahun . '-' . $bulan : now()->format('Y-m');
        $isBeforeGoLiveDistribusi = strcmp($bulanFilter, self::GO_LIVE_MONTH) < 0;
        $sqlExpr = WilayahHelper::getSqlExpression();
        
        $distribusiWilayah = (clone $pelangganQuery)
            ->selectRaw("MAX(wilayah) as wilayah")
            ->selectRaw("count(*) as jumlah")
            ->selectRaw("{$sqlExpr} as wilayah_normalized")
            ->whereNotNull('wilayah')
            ->where('wilayah', '!=', '')
            ->where('status_aktif', true)
            ->groupByRaw($sqlExpr)
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function ($item) use ($bulanFilter, $isBeforeGoLiveDistribusi) {
                $sqlExpr = WilayahHelper::getSqlExpression();
                
                $pelangganIds = Pelanggan::forUser()
                    ->where('status_aktif', true)
                    ->whereRaw("{$sqlExpr} = ?", [$item->wilayah_normalized])
                    ->pluck('id');
                
                $sudahBayar = 0;
                if (!$isBeforeGoLiveDistribusi) {
                    $sudahBayar = Pembayaran::where('bulan_bayar', $bulanFilter)
                        ->whereIn('pelanggan_id', $pelangganIds)
                        ->distinct('pelanggan_id')
                        ->count('pelanggan_id');
                }
                
                $belumBayar = $pelangganIds->count() - $sudahBayar;
                
                // Hitung tunggakan (bulan sebelumnya yang belum bayar)
                $tunggakanData = \App\Models\TagihanBulanan::where('bulan', '<', $bulanFilter)
                    ->where('bulan', '>=', self::GO_LIVE_MONTH)
                    ->whereIn('status_bayar', ['BELUM_BAYAR', 'TUNGGAKAN', 'CICILAN'])
                    ->whereIn('pelanggan_id', $pelangganIds)
                    ->with('pelanggan:id,id_pelanggan,nama_pelanggan')
                    ->whereRaw('(total_tagihan - COALESCE(jumlah_terbayar, 0)) > 0')
                    ->select('pelanggan_id', \DB::raw('COUNT(*) as jumlah_bulan'), \DB::raw("GROUP_CONCAT(bulan ORDER BY bulan ASC SEPARATOR ', ') as daftar_bulan"), \DB::raw('SUM(total_tagihan - COALESCE(jumlah_terbayar, 0)) as total_tunggakan'))
                    ->groupBy('pelanggan_id')
                    ->get();
                
                $tunggakanCount = $tunggakanData->count();
                
                // Detail pelanggan yang nunggak
                $detailTunggakan = $tunggakanData->map(function($t) {
                    $bulanList = array_filter(explode(', ', $t->daftar_bulan ?? ''));
                    $formattedBulan = collect($bulanList)->map(function ($b) {
                        $parts = explode('-', $b);
                        if (count($parts) != 2) return $b;
                        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $m = (int)$parts[1];
                        return ($months[$m] ?? '') . ' ' . $parts[0];
                    })->implode(', ');
                    return [
                        'id_pelanggan' => $t->pelanggan->id_pelanggan ?? 'N/A',
                        'nama_pelanggan' => $t->pelanggan->nama_pelanggan ?? 'N/A',
                        'jumlah_bulan' => $t->jumlah_bulan,
                        'total_tunggakan' => $t->total_tunggakan,
                        'daftar_bulan' => $formattedBulan,
                    ];
                })->toArray();
                
                return [
                    'wilayah' => ucwords($item->wilayah_normalized),
                    'jumlah' => $item->jumlah,
                    'sudah_bayar' => $sudahBayar,
                    'belum_bayar' => $belumBayar,
                    'tunggakan' => $tunggakanCount,
                    'detail_tunggakan' => $detailTunggakan,
                ];
            });

        return [
            'data' => $pembayarans,
            'summary' => [
                'pemasukan' => $totalPemasukan,
                'pemasukanBulanan' => $pembayaranBulanIni,
                'pemasukanTunggakan' => $pembayaranTunggakan,
                'detailPembayaranTunggakan' => $detailPembayaranTunggakan,
                'kubikasi' => $totalKubik,
                'transaksi' => $totalTransaksi,
                'pemasukanUmum' => $pemasukanUmum,
                'pemasukanSosial' => $pemasukanSosial,
                'transaksiUmum' => $transaksiUmum,
                'transaksiSosial' => $transaksiSosial,
                'pelangganUmum' => $pelangganUmum,
                'pelangganSosial' => $pelangganSosial,
            ],
            'detail' => [
                'totalTarikan' => $totalPemasukan,
                'tarik20Persen' => $tarik20Persen,
                'biayaOperasional' => $biayaOperasional,
                'biayaPadDesa' => $biayaPadDesa,
                'biayaOpsLapangan' => $biayaOpsLapangan,
                'biayaLainLain' => $biayaLainLain,
                'biayaCSR' => $biayaCSR,
                'honorPenarik' => $honorPenarik,
                'honorMurni' => $honorPenarik,
                'totalAbunemen' => $totalAbunemen,
                'jumlahTransaksiAbunemen' => $jumlahTransaksiAbunemen,
                'totalTarikanBersih' => $totalTarikanBersih,
                'totalSR' => $totalSR,
                'srSudahBayar' => $srSudahBayar,
                'srBelumBayar' => $srBelumBayar,
            ],
            'filters' => [
                'tahun' => (int)$tahun,
                'bulan' => $bulan,
                'wilayah' => $wilayah,
            ],
            'distribusiWilayah' => $distribusiWilayah,
        ];
    }

    private function getBulanName($bulan)
    {
        $bulanNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $bulanNames[$bulan] ?? $bulan;
    }
}



