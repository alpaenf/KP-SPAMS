<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\TagihanBulanan;
use App\Models\InformasiTarif;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class QRScannerController extends Controller
{
    /**
     * Tampilkan halaman scan QR code
     */
    public function index()
    {
        return Inertia::render('QRScanner/Index');
    }
    
    /**
     * Proses hasil scan QR code dan ambil data pelanggan
     */
    public function scan(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|string',
        ]);
        
        $pelanggan = Pelanggan::where('id_pelanggan', $request->id_pelanggan)->first();
        
        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan.',
            ], 404);
        }
        
        // Ambil tagihan terbaru
        $tagihanTerbaru = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
            ->orderBy('bulan', 'desc')
            ->first();
        
        // Ambil informasi tarif aktif berdasarkan kategori
        $tarifPemakaian = InformasiTarif::where('is_active', true)
            ->where('kategori', 'tarif')
            ->first();
        $biayaAbunemenData = InformasiTarif::where('is_active', true)
            ->where('kategori', 'biaya')
            ->first();
        
        return response()->json([
            'success' => true,
            'pelanggan' => [
                'id' => $pelanggan->id,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'no_whatsapp' => $pelanggan->no_whatsapp,
                'rt' => $pelanggan->rt,
                'rw' => $pelanggan->rw,
                'kategori' => $pelanggan->kategori,
                'wilayah' => $pelanggan->wilayah,
                'status_aktif' => $pelanggan->status_aktif,
            ],
            'tagihan_terbaru' => $tagihanTerbaru ? [
                'bulan' => $tagihanTerbaru->bulan,
                'meteran_sebelum' => $tagihanTerbaru->meteran_sebelum,
                'meteran_sesudah' => $tagihanTerbaru->meteran_sesudah,
                'pemakaian_kubik' => $tagihanTerbaru->pemakaian_kubik,
                'total_tagihan' => $tagihanTerbaru->total_tagihan,
                'status_bayar' => $tagihanTerbaru->status_bayar,
            ] : null,
            'tarif_aktif' => [
                'tarif_per_kubik' => $tarifPemakaian ? (float)$tarifPemakaian->harga : 2000,
                'biaya_abunemen' => $biayaAbunemenData ? (float)$biayaAbunemenData->harga : 3000,
                'minimal_pemakaian' => 10,
            ],
        ]);
    }
    
    /**
     * Tampilkan form input meteran untuk pelanggan
     */
    public function inputMeteran($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        // Ambil tagihan terbaru
        $tagihanTerbaru = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
            ->orderBy('bulan', 'desc')
            ->first();
        
        // Ambil informasi tarif aktif berdasarkan kategori
        $tarifPemakaian = InformasiTarif::where('is_active', true)
            ->where('kategori', 'tarif')
            ->first();
        $biayaAbunemenData = InformasiTarif::where('is_active', true)
            ->where('kategori', 'biaya')
            ->first();
        
        return Inertia::render('QRScanner/InputMeteran', [
            'pelanggan' => [
                'id' => $pelanggan->id,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'no_whatsapp' => $pelanggan->no_whatsapp,
                'rt' => $pelanggan->rt,
                'rw' => $pelanggan->rw,
                'kategori' => $pelanggan->kategori,
                'wilayah' => $pelanggan->wilayah,
                'status_aktif' => $pelanggan->status_aktif,
            ],
            'tagihan_terbaru' => $tagihanTerbaru ? [
                'id' => $tagihanTerbaru->id,
                'bulan' => $tagihanTerbaru->bulan,
                'meteran_sebelum' => $tagihanTerbaru->meteran_sebelum,
                'meteran_sesudah' => $tagihanTerbaru->meteran_sesudah,
                'pemakaian_kubik' => $tagihanTerbaru->pemakaian_kubik,
                'total_tagihan' => $tagihanTerbaru->total_tagihan,
                'status_bayar' => $tagihanTerbaru->status_bayar,
            ] : null,
            'tarif_aktif' => [
                'tarif_per_kubik' => $tarifPemakaian ? (float)$tarifPemakaian->harga : 2000,
                'biaya_abunemen' => $biayaAbunemenData ? (float)$biayaAbunemenData->harga : 3000,
                'minimal_pemakaian' => 10,
            ],
        ]);
    }
    
    /**
     * Simpan data meteran baru
     */
    public function storeMeteran(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'meteran_sesudah' => 'required|numeric|min:0',
            'bulan' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
        
        try {
            $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);
            
            // Ambil informasi tarif aktif berdasarkan kategori
            $tarifPemakaian = InformasiTarif::where('is_active', true)
                ->where('kategori', 'tarif')
                ->first();
            $biayaAbunemenData = InformasiTarif::where('is_active', true)
                ->where('kategori', 'biaya')
                ->first();
            
            // Fallback ke nilai default jika tidak ada data tarif
            $tarifPerKubik = $tarifPemakaian ? (float)$tarifPemakaian->harga : 2000;
            $biayaAbunemen = $biayaAbunemenData ? (float)$biayaAbunemenData->harga : 3000;
            $minimalPemakaian = 10;
            
            // Validasi tarif tidak boleh null atau 0
            if (!$tarifPerKubik || $tarifPerKubik <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tarif tidak valid. Silakan hubungi administrator untuk mengatur tarif terlebih dahulu.',
                ], 422);
            }

            // Cek apakah tagihan untuk bulan ini sudah ada
            $existingTagihan = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
                ->where('bulan', $request->bulan)
                ->first();

            if ($existingTagihan) {
                // Jika sudah ada dan statusnya Lunas, tidak boleh diupdate
                if ($existingTagihan->status_bayar === 'Lunas') {
                    return response()->json([
                        'success' => false,
                        'message' => "Tagihan untuk bulan {$request->bulan} sudah LUNAS dan tidak dapat diubah.",
                    ], 422);
                }

                // Hitung ulang tagihan
                $pemakaianKubik = $request->meteran_sesudah - $existingTagihan->meteran_sebelum;
                
                // Jika pemakaian kurang dari minimal, gunakan minimal pemakaian
                $pemakaianDitagih = max($pemakaianKubik, $minimalPemakaian);
                
                // Hitung total tagihan
                $totalTagihan = ($pemakaianDitagih * $tarifPerKubik) + $biayaAbunemen;

                // Update tagihan yang ada
                $existingTagihan->update([
                    'meteran_sesudah' => $request->meteran_sesudah,
                    'pemakaian_kubik' => $pemakaianKubik,
                    'tarif_per_kubik' => $tarifPerKubik,
                    'biaya_abunemen' => $biayaAbunemen,
                    'total_tagihan' => $totalTagihan,
                    'keterangan' => $request->keterangan,
                ]);

                $tagihan = $existingTagihan;
                $message = 'Data tagihan berhasil diperbarui.';

            } else {
                // Skeario Baru: Cari meteran sebelum dari bulan sebelumnya
                // Ambil tagihan terakhir SEBELUM bulan yang sedang diinput
                $tagihanTerakhir = TagihanBulanan::where('pelanggan_id', $pelanggan->id)
                    ->where('bulan', '<', $request->bulan) // Hanya cari bulan sebelumnya
                    ->orderBy('bulan', 'desc')
                    ->first();
                
                $meteranSebelum = $tagihanTerakhir ? $tagihanTerakhir->meteran_sesudah : 0;
                
                // Validasi meteran sesudah harus lebih besar dari meteran sebelum
                if ($request->meteran_sesudah < $meteranSebelum) {
                    return response()->json([
                        'success' => false,
                        'message' => "Meteran sesudah ({$request->meteran_sesudah}) tidak boleh lebih kecil dari meteran sebelum ({$meteranSebelum}).",
                    ], 422);
                }

                // Hitung pemakaian
                $pemakaianKubik = $request->meteran_sesudah - $meteranSebelum;
                
                // Jika pemakaian kurang dari minimal, gunakan minimal pemakaian
                $pemakaianDitagih = max($pemakaianKubik, $minimalPemakaian);
                
                // Hitung total tagihan
                $totalTagihan = ($pemakaianDitagih * $tarifPerKubik) + $biayaAbunemen;
                
                // Buat tagihan baru
                $tagihan = TagihanBulanan::create([
                    'pelanggan_id' => $pelanggan->id,
                    'bulan' => $request->bulan,
                    'meteran_sebelum' => $meteranSebelum,
                    'meteran_sesudah' => $request->meteran_sesudah,
                    'pemakaian_kubik' => $pemakaianKubik,
                    'tarif_per_kubik' => $tarifPerKubik,
                    'ada_abunemen' => true,
                    'biaya_abunemen' => $biayaAbunemen,
                    'total_tagihan' => $totalTagihan,
                    'status_bayar' => 'BELUM_BAYAR',
                    'keterangan' => $request->keterangan,
                ]);

                $message = 'Data meteran berhasil disimpan.';
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'tagihan' => [
                    'id' => $tagihan->id,
                    'bulan' => $tagihan->bulan,
                    'meteran_sebelum' => $tagihan->meteran_sebelum,
                    'meteran_sesudah' => $tagihan->meteran_sesudah,
                    'pemakaian_kubik' => $tagihan->pemakaian_kubik,
                    'total_tagihan' => $tagihan->total_tagihan,
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Download QR code untuk pelanggan sebagai PDF
     */
    public function downloadQR($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        // Generate QR code as SVG
        $qrCodeSvg = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($pelanggan->id_pelanggan);
        
        $qrCodeBase64 = base64_encode($qrCodeSvg);
        
        $data = [
            'pelanggan' => $pelanggan,
            'qr_code' => 'data:image/svg+xml;base64,' . $qrCodeBase64,
        ];
        
        $pdf = Pdf::loadView('pdf.qr-code-single', $data)
            ->setPaper('a4', 'portrait');
        
        return $pdf->download('QR-' . $pelanggan->id_pelanggan . '.pdf');
    }
    
    /**
     * Download QR code untuk pelanggan sebagai gambar SVG
     */
    public function downloadQRImage($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        // Generate QR code as SVG
        $qrCode = QrCode::format('svg')
            ->size(400)
            ->errorCorrection('H')
            ->generate($pelanggan->id_pelanggan);
        
        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="QR-' . $pelanggan->id_pelanggan . '.svg"');
    }
}
