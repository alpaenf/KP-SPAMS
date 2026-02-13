<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Helpers\WilayahHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

class QRCodeBulkController extends Controller
{
    /**
     * Tampilkan halaman preview QR codes untuk semua pelanggan
     */
    public function preview(Request $request)
    {
        $query = Pelanggan::query();
        
        // Filter berdasarkan wilayah jika ada (case-insensitive dengan underscore normalization)
        if ($request->has('wilayah') && $request->wilayah !== 'all') {
            $wilayahNormalized = WilayahHelper::normalize($request->wilayah);
            $sqlExpr = WilayahHelper::getSqlExpression();
            $query->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized]);
        }
        
        // Filter berdasarkan RT/RW jika ada
        if ($request->has('rt') && $request->rt !== 'all') {
            $query->where('rt', $request->rt);
        }
        
        if ($request->has('rw') && $request->rw !== 'all') {
            $query->where('rw', $request->rw);
        }
        
        // Filter berdasarkan status
        if ($request->has('status_aktif') && $request->status_aktif !== 'all') {
            $query->where('status_aktif', $request->status_aktif === 'aktif');
        }
        
        $pelangganList = $query->orderBy('id_pelanggan')->get()->map(function ($pelanggan) {
            return [
                'id' => $pelanggan->id,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'rt' => $pelanggan->rt,
                'rw' => $pelanggan->rw,
                'wilayah' => $pelanggan->wilayah,
                'status_aktif' => $pelanggan->status_aktif,
                'qr_code_data_url' => $pelanggan->qr_code_data_url,
            ];
        });
        
        return Inertia::render('QRCode/BulkPreview', [
            'pelangganList' => $pelangganList,
            'filters' => [
                'wilayah' => $request->wilayah ?? 'all',
                'rt' => $request->rt ?? 'all',
                'rw' => $request->rw ?? 'all',
                'status_aktif' => $request->status_aktif ?? 'all',
            ],
        ]);
    }
    
    /**
     * Download bulk QR codes sebagai PDF
     */
    public function downloadPDF(Request $request)
    {
        $query = Pelanggan::query();
        
        // Filter berdasarkan wilayah jika ada (case-insensitive dengan underscore normalization)
        if ($request->has('wilayah') && $request->wilayah !== 'all') {
            $wilayahNormalized = WilayahHelper::normalize($request->wilayah);
            $sqlExpr = WilayahHelper::getSqlExpression();
            $query->whereRaw("{$sqlExpr} = ?", [$wilayahNormalized]);
        }
        
        // Filter berdasarkan RT/RW jika ada
        if ($request->has('rt') && $request->rt !== 'all') {
            $query->where('rt', $request->rt);
        }
        
        if ($request->has('rw') && $request->rw !== 'all') {
            $query->where('rw', $request->rw);
        }
        
        // Filter berdasarkan status
        if ($request->has('status_aktif') && $request->status_aktif !== 'all') {
            $query->where('status_aktif', $request->status_aktif === 'aktif');
        }
        
        $pelangganList = $query->orderBy('id_pelanggan')->get()->map(function ($pelanggan) {
            $qrCodeSvg = QrCode::format('svg')
                ->size(200)
                ->errorCorrection('H')
                ->generate($pelanggan->id_pelanggan);
            
            return [
                'id' => $pelanggan->id,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'nama_pelanggan' => $pelanggan->nama_pelanggan,
                'rt' => $pelanggan->rt,
                'rw' => $pelanggan->rw,
                'wilayah' => $pelanggan->wilayah,
                'qr_code' => 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg),
            ];
        });
        
        $data = [
            'pelangganList' => $pelangganList,
        ];
        
        $pdf = Pdf::loadView('pdf.qr-code-bulk', $data)
            ->setPaper('a4', 'portrait');
        
        return $pdf->download('QR-Code-Pelanggan-' . date('Y-m-d') . '.pdf');
    }
}
