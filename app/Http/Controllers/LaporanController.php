<?php

namespace App\Http\Controllers;

use App\Models\LaporanBulanan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Exports\LaporanExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->year);
        $bulan = $request->input('bulan', Carbon::now()->format('m')); // 01-12 or 'semua'
        $wilayah = $request->input('wilayah', 'semua');

        // === 1. Query Data Pembayaran (Untuk Tabel & Total Tarikan) ===
        $query = Pembayaran::with('pelanggan');

        // Filter Tahun & Bulan via bulan_bayar
        if ($bulan && $bulan !== 'semua') {
            $query->where('bulan_bayar', $tahun . '-' . $bulan);
        } else {
             // Semua bulan di tahun ini
            $query->where('bulan_bayar', 'like', $tahun . '-%');
        }

        // Filter Wilayah untuk penarik
        if (auth()->user()->isPenarik() && auth()->user()->hasWilayah()) {
            // Penarik hanya bisa lihat wilayahnya sendiri
            $query->whereHas('pelanggan', function ($q) {
                $q->where('wilayah', auth()->user()->getWilayah());
            });
        } elseif ($wilayah && $wilayah !== 'semua') {
            // Admin bisa filter wilayah manual
            $query->whereHas('pelanggan', function ($q) use ($wilayah) {
                $q->where('wilayah', $wilayah)
                  ->orWhere('rw', $wilayah)
                  ->orWhere('rt', $wilayah);
            });
        }

        $pembayarans = $query->latest('tanggal_bayar')->get();
        
        // Total Pemasukan / Total Tarikan
        $totalPemasukan = $pembayarans->sum('jumlah_bayar');
        $totalKubik = $pembayarans->sum('jumlah_kubik');
        $totalTransaksi = $pembayarans->count();

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
            $laporanQuery->where('bulan', $bulanFormat);
        } else {
            // Jika semua bulan, ambil semua operasional di tahun tersebut
            $laporanQuery->where('bulan', 'like', $tahun . '-%');
        }
        
        // Jika filter wilayah ada
        if ($wilayah && $wilayah !== 'semua') {
             $laporanQuery->where('wilayah', $wilayah);
        }

        $biayaOperasional = $laporanQuery->sum('biaya_operasional_penarik');
        $biayaPadDesa = $laporanQuery->sum('biaya_pad_desa');
        $biayaOperasionalLapangan = $laporanQuery->sum('biaya_operasional_lapangan');
        $biayaLainLain = $laporanQuery->sum('biaya_lain_lain');

        // C. Honor Penarik
        $honorPenarik = $tarik20Persen + $biayaOperasional;

        // D. Total Tarikan Bersih (dikurangi honor penarik DAN PAD Desa DAN Lainnya)
        $totalTarikanBersih = $totalPemasukan - $honorPenarik - $biayaPadDesa - $biayaOperasionalLapangan - $biayaLainLain;

        // === 3. Statistik SR (Sambungan Rumah) ===
        // Hitung total pelanggan aktif (SR) sesuai filter wilayah
        // Apply filter wilayah berdasarkan user yang login
        $pelangganQuery = Pelanggan::forUser()->where('status_aktif', true);
        if ($wilayah && $wilayah !== 'semua' && auth()->user()->isAdmin()) {
            // Admin bisa filter wilayah manual, penarik sudah auto-filtered
            $pelangganQuery->where(function ($q) use ($wilayah) {
                $q->where('wilayah', $wilayah)
                  ->orWhere('rw', $wilayah)
                  ->orWhere('rt', $wilayah);
            });
        }
        $totalSR = $pelangganQuery->count();

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

        // Ambil opsi wilayah untuk dropdown
        $wilayahOpsi = Pelanggan::select('wilayah')
            ->whereNotNull('wilayah')
            ->where('wilayah', '!=', '')
            ->distinct()
            ->orderBy('wilayah')
            ->pluck('wilayah')
            ->toArray();

        return Inertia::render('Laporan/Index', [
            'data' => $pembayarans,
            'summary' => [
                'pemasukan' => $totalPemasukan,
                'kubikasi' => $totalKubik,
                'transaksi' => $totalTransaksi,
            ],
            'detail' => [
                'totalTarikan' => $totalPemasukan,
                'tarik20Persen' => $tarik20Persen,
                'biayaOperasional' => $biayaOperasional,
                'biayaPadDesa' => $biayaPadDesa,
                'biayaOperasionalLapangan' => $biayaOperasionalLapangan,
                'biayaLainLain' => $biayaLainLain,
                'honorPenarik' => $honorPenarik, // 20% + Ops
                'honorMurni' => $honorPenarik,   // Sama, penamaan beda konteks
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
            ]
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
                $data['filters']
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
            $laporanQuery->where('wilayah', $wilayah);
        }
        
        $laporanBulanan = $laporanQuery->get();
        
        // Calculate totals
        $totalPengeluaran = $laporanBulanan->sum(function($laporan) {
            return ($laporan->biaya_operasional_penarik ?? 0) 
                + ($laporan->biaya_pad_desa ?? 0)
                + ($laporan->biaya_operasional_lapangan ?? 0) 
                + ($laporan->biaya_lain_lain ?? 0);
        });
        
        // Note: Logic above might double count because honor penarik includes operasional penarik? 
        // Wait, honorPenarik = 20% + Ops.
        // Usually Laporan Keuangan PDF shows Income vs Expenses.
        // Expenses = Honor Penarik (incl Ops Penarik) + PAD Desa + Ops Lapangan + Lain-lain.
        // But the previous code was: ($laporan->biaya_operasional_penarik ?? 0) + ($laporan->biaya_operasional_lainnya ?? 0);
        // "biaya_operasional_lainnya" didn't exist in my model view, maybe it was a typo or old field.
        // Let's assume Total Pengeluaran = Honor Penarik + PAD Desa + Ops Lapangan + Lain-lain
        // But Honor Penarik is calculated from Income (20%) + Ops Penarik.
        // So Total Pengeluaran = (TotalIncome * 0.2) + Ops Penarik + PAD Desa + Ops Lapangan + Lain-lain.
        
        // Let's recalculate based on logic from Dashboard/Index:
        // Total Bersih = Pemasukan - HonorPenarik - PADDesa - OpsLapangan - LainLain
        // So Pengeluaran = HonorPenarik + PADDesa + OpsLapangan + LainLain.
        
        $tarik20Persen = $data['detail']['tarik20Persen']; // Calculated in getFilteredData
        // The $laporanBulanan->sum() approach only sums DB columns. It misses the 20%.
        
        $totalBiayaOpsPenarik = $laporanBulanan->sum('biaya_operasional_penarik');
        $totalBiayaPadDesa = $laporanBulanan->sum('biaya_pad_desa');
        $totalBiayaOpsLapangan = $laporanBulanan->sum('biaya_operasional_lapangan');
        $totalBiayaLain = $laporanBulanan->sum('biaya_lain_lain');
        
        $totalHonorPenarik = $tarik20Persen + $totalBiayaOpsPenarik;
        
        $totalPengeluaran = $totalHonorPenarik + $totalBiayaPadDesa + $totalBiayaOpsLapangan + $totalBiayaLain;
        
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

        // Query Data Pembayaran
        $query = Pembayaran::with('pelanggan');

        if ($bulan && $bulan !== 'semua') {
            $query->where('bulan_bayar', $tahun . '-' . $bulan);
        } else {
            $query->where('bulan_bayar', 'like', $tahun . '-%');
        }

        // Filter Wilayah untuk penarik
        if (auth()->user()->isPenarik() && auth()->user()->hasWilayah()) {
            // Penarik hanya bisa lihat wilayahnya sendiri
            $query->whereHas('pelanggan', function ($q) {
                $q->where('wilayah', auth()->user()->getWilayah());
            });
        } elseif ($wilayah && $wilayah !== 'semua') {
            // Admin bisa filter wilayah manual
            $query->whereHas('pelanggan', function ($q) use ($wilayah) {
                $q->where('wilayah', $wilayah)
                  ->orWhere('rw', $wilayah)
                  ->orWhere('rt', $wilayah);
            });
        }

        $pembayarans = $query->latest('tanggal_bayar')->get();
        
        $totalPemasukan = $pembayarans->sum('jumlah_bayar');
        $totalKubik = $pembayarans->sum('jumlah_kubik');
        $totalTransaksi = $pembayarans->count();

        // Hitung Detail Keuangan
        $tarik20Persen = $totalPemasukan * 0.20;

        $laporanQuery = LaporanBulanan::query();
        if ($bulan && $bulan !== 'semua') {
            $bulanFormat = $tahun . '-' . $bulan;
            $laporanQuery->where('bulan', $bulanFormat);
        } else {
            $laporanQuery->where('bulan', 'like', $tahun . '-%');
        }
        
        if ($wilayah && $wilayah !== 'semua') {
             $laporanQuery->where('wilayah', $wilayah);
        }

        $biayaOperasional = $laporanQuery->sum('biaya_operasional_penarik');
        $biayaPadDesa = $laporanQuery->sum('biaya_pad_desa');
        $biayaOperasionalLapangan = $laporanQuery->sum('biaya_operasional_lapangan');
        $biayaLainLain = $laporanQuery->sum('biaya_lain_lain');

        $honorPenarik = $tarik20Persen + $biayaOperasional;
        $totalTarikanBersih = $totalPemasukan - $honorPenarik - $biayaPadDesa - $biayaOperasionalLapangan - $biayaLainLain;

        // Statistik SR
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
        $totalSR = $pelangganQuery->count();
        $srSudahBayar = $pembayarans->unique('pelanggan_id')->count();
        $srBelumBayar = max(0, $totalSR - $srSudahBayar);

        return [
            'data' => $pembayarans,
            'summary' => [
                'pemasukan' => $totalPemasukan,
                'kubikasi' => $totalKubik,
                'transaksi' => $totalTransaksi,
            ],
            'detail' => [
                'totalTarikan' => $totalPemasukan,
                'tarik20Persen' => $tarik20Persen,
                'biayaOperasional' => $biayaOperasional,
                'biayaPadDesa' => $biayaPadDesa,
                'biayaOperasionalLapangan' => $biayaOperasionalLapangan,
                'biayaLainLain' => $biayaLainLain,
                'honorPenarik' => $honorPenarik,
                'honorMurni' => $honorPenarik,
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
