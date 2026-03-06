<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\WilayahHelper;
use App\Models\LaporanBulanan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GajiPenarikController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));

        // Daftar semua wilayah yang dikenali
        $wilayahList = ['dawuhan', 'kubangsari_kulon', 'kubangsari_wetan', 'sokarame', 'tiparjaya'];

        // Semua user penarik (bisa lebih dari satu per wilayah)
        $penarikUsers = User::where('role', 'penarik')->whereNotNull('wilayah')->get()
            ->groupBy('wilayah');

        $data = [];

        foreach ($wilayahList as $wilayah) {
            $wilayahNormalized = WilayahHelper::normalize($wilayah);
            $sqlExpr           = WilayahHelper::getSqlExpression();

            // Total pembayaran dari pelanggan aktif di wilayah ini pada bulan tsb
            $pelangganIds = Pelanggan::where('status_aktif', true)
                ->byWilayah($wilayah)
                ->pluck('id');

            $totalPemasukan = Pembayaran::where('bulan_bayar', $bulan)
                ->whereIn('pelanggan_id', $pelangganIds)
                ->sum('jumlah_bayar');

            // === Biaya dari LaporanBulanan ===
            $laporan = LaporanBulanan::where('bulan', $bulan)
                ->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized])
                ->first();

            $biayaOpsPenarik  = $laporan ? (float) $laporan->biaya_operasional_penarik : 0;
            $biayaOpsLapangan = $laporan ? (float) $laporan->biaya_operasional_lapangan : 0;

            // === 4 Kalkulasi ===
            $jasa20Persen = $totalPemasukan * 0.20;
            $honorPenarik = $jasa20Persen + $biayaOpsPenarik;

            // Penarik yang bertugas di wilayah ini
            $petugas = ($penarikUsers[$wilayah] ?? collect())->map(fn($u) => [
                'id'     => $u->id,
                'name'   => $u->name,
                'email'  => $u->email,
            ])->values();

            $data[] = [
                'wilayah'           => $wilayah,
                'wilayah_label'     => ucwords(str_replace('_', ' ', $wilayah)),
                'petugas'           => $petugas,
                'total_pemasukan'   => (float) $totalPemasukan,
                'jasa_20_persen'    => round($jasa20Persen),
                'biaya_ops_penarik' => round($biayaOpsPenarik),
                'honor_penarik'     => round($honorPenarik),
                'biaya_ops_lapangan'=> round($biayaOpsLapangan),
            ];
        }

        // Summary totals
        $totals = [
            'total_pemasukan'    => array_sum(array_column($data, 'total_pemasukan')),
            'jasa_20_persen'     => array_sum(array_column($data, 'jasa_20_persen')),
            'biaya_ops_penarik'  => array_sum(array_column($data, 'biaya_ops_penarik')),
            'honor_penarik'      => array_sum(array_column($data, 'honor_penarik')),
            'biaya_ops_lapangan' => array_sum(array_column($data, 'biaya_ops_lapangan')),
        ];

        return Inertia::render('Admin/GajiPenarik', [
            'data'  => $data,
            'bulan' => $bulan,
            'totals'=> $totals,
        ]);
    }
}
