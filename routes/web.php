<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\VisiMisiController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\SejarahController;
use App\Http\Controllers\LayananSpamController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\QRScannerController;
use App\Http\Controllers\QRCodeBulkController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('guest.redirect');
Route::get('/download-pdf/{id}', [HomeController::class, 'downloadPdf'])->name('pelanggan.pdf');
Route::get('/pelanggan/{id}/download-qr', [QRScannerController::class, 'downloadQR'])->name('pelanggan.download-qr');
Route::get('/pelanggan/{id}/download-qr-image', [QRScannerController::class, 'downloadQRImage'])->name('pelanggan.download-qr-image');
Route::get('/pembayaran/{id}', [HomeController::class, 'pembayaran'])->name('pembayaran.public');
Route::post('/konfirmasi-pembayaran', [HomeController::class, 'konfirmasiPembayaran'])->name('konfirmasi.pembayaran');

// Public API endpoints (with rate limiting to prevent abuse)
Route::middleware('api.rate.limit')->group(function () {
    // API untuk refresh CSRF token (untuk PWA)
    Route::get('/api/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    });

    // API routes for landing page (public)
    Route::get('/api/berita', [BeritaController::class, 'getPublished']);
    Route::get('/api/visi-misi', [VisiMisiController::class, 'get']);
    Route::get('/api/galeri', [GaleriController::class, 'getAll']);
    Route::get('/api/sejarah', [SejarahController::class, 'get']);
    Route::get('/api/layanan', [LayananSpamController::class, 'getAll']);
    Route::get('/api/testimoni', [TestimoniController::class, 'getApproved']);
    Route::get('/api/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'getActive']);
    Route::get('/api/informasi-tarif', [\App\Http\Controllers\Admin\InformasiTarifController::class, 'getActive']);
});

Route::post('/testimoni', [TestimoniController::class, 'store'])->name('testimoni.store');

// Auth routes untuk pengelola
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes untuk pengelola yang sudah login
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/cek-pelanggan', [HomeController::class, 'cekPelanggan'])->name('cek-pelanggan');
    Route::get('/cek-pelanggan/export-excel', [HomeController::class, 'exportPelangganExcel'])->name('cek-pelanggan.export-excel');
    Route::get('/cek-pelanggan/export-pdf', [HomeController::class, 'exportPelangganPdf'])->name('cek-pelanggan.export-pdf');
    Route::get('/peta', [HomeController::class, 'peta'])->name('peta');
    
    // QR Scanner Routes
    Route::get('/qr-scanner', [QRScannerController::class, 'index'])->name('qr-scanner.index');
    Route::post('/api/qr-scanner/scan', [QRScannerController::class, 'scan'])
        ->middleware('qr.rate.limit')
        ->name('qr-scanner.scan');
    Route::get('/qr-scanner/input-meteran/{id}', [QRScannerController::class, 'inputMeteran'])->name('qr-scanner.input-meteran');
    Route::post('/api/qr-scanner/store-meteran', [QRScannerController::class, 'storeMeteran'])->name('qr-scanner.store-meteran');
    Route::get('/api/qr-scanner/last-meteran/{pelangganId}/{bulan}', [QRScannerController::class, 'getLastMeteran'])->name('qr-scanner.last-meteran');

    Route::get('/qr-code/bulk-preview', [QRCodeBulkController::class, 'preview'])->name('qr-code.bulk-preview');
    Route::get('/qr-code/bulk-download-pdf', [QRCodeBulkController::class, 'downloadPDF'])->name('qr-code.bulk-download-pdf');
    
    // QR Code Bulk Preview & Print
    Route::get('/qr-code/bulk-preview', [QRCodeBulkController::class, 'preview'])->name('qr-code.bulk-preview');
    
    // CRUD Pelanggan
    Route::get('/pelanggan/create', [HomeController::class, 'createPelanggan'])->name('pelanggan.create');
    Route::post('/pelanggan', [HomeController::class, 'storePelanggan'])->name('pelanggan.store');
    Route::get('/pelanggan/{pelanggan}/edit', [HomeController::class, 'editPelanggan'])->name('pelanggan.edit');
    Route::put('/pelanggan/{pelanggan}', [HomeController::class, 'updatePelanggan'])->name('pelanggan.update');
    Route::delete('/pelanggan/{pelanggan}', [HomeController::class, 'deletePelanggan'])->name('pelanggan.delete');
    
    // Riwayat Pembayaran
    Route::get('/pembayaran', [PembayaranController::class, 'riwayat'])->name('pembayaran.riwayat');
    
    // API Pembayaran
    Route::get('/pelanggan/{pelanggan}/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pelanggan/{pelanggan}/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::put('/pembayaran/{pembayaran}', [PembayaranController::class, 'update'])->name('pembayaran.update');
    Route::delete('/pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    Route::post('/pembayaran/{pembayaran}/send-receipt', [PembayaranController::class, 'sendReceipt'])->name('pembayaran.send-receipt');
    Route::get('/pembayaran/{pembayaran}/receipt-link', [PembayaranController::class, 'getReceiptLink'])->name('pembayaran.receipt-link');
    Route::get('/pembayaran/{pembayaran}/download-pdf', [PembayaranController::class, 'downloadPdf'])->name('pembayaran.download-pdf');
    Route::get('/pembayaran/{pembayaran}/print', [PembayaranController::class, 'printReceipt'])->name('pembayaran.print');
    
    // API Tagihan by Pelanggan
    Route::get('/api/tagihan-bulanan/{pelangganId}/{bulan}', [\App\Http\Controllers\TagihanBulananController::class, 'getByPelangganBulan'])->name('tagihan-bulanan.get-by-pelanggan-bulan');
    
    // Tagihan Bulanan & Meteran
    Route::get('/tagihan-bulanan', [\App\Http\Controllers\TagihanBulananController::class, 'index'])->name('tagihan-bulanan.index');
    Route::post('/tagihan-bulanan', [\App\Http\Controllers\TagihanBulananController::class, 'store'])->name('tagihan-bulanan.store');
    Route::post('/tagihan-bulanan/generate-bulk', [\App\Http\Controllers\TagihanBulananController::class, 'generateBulk'])->name('tagihan-bulanan.generate-bulk');
    Route::post('/tagihan-bulanan/{id}/approve-konfirmasi', [\App\Http\Controllers\TagihanBulananController::class, 'approveKonfirmasi'])->name('tagihan-bulanan.approve');
    Route::post('/tagihan-bulanan/{id}/reject-konfirmasi', [\App\Http\Controllers\TagihanBulananController::class, 'rejectKonfirmasi'])->name('tagihan-bulanan.reject');
    
    // Laporan Keuangan & Operasional
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/update-operasional', [HomeController::class, 'updateBiayaOperasional'])->name('laporan.update-operasional');
    Route::get('/laporan/export-excel', [\App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [\App\Http\Controllers\LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    
    // Landing Page Management Dashboard (unified admin interface)
    Route::get('/admin/landing-page', function () {
        return inertia('Admin/Dashboard');
    })->name('admin.dashboard');
    
    // API Routes untuk dashboard (tanpa prefix admin)
    // Berita
    Route::post('/berita', [BeritaController::class, 'store'])->name('berita.store');
    Route::put('/berita/{berita}', [BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{berita}', [BeritaController::class, 'destroy'])->name('berita.destroy');
    
    // Visi Misi
    Route::put('/visi-misi', [VisiMisiController::class, 'update'])->name('visi-misi.update');
    
    // Galeri
    Route::post('/galeri', [GaleriController::class, 'store'])->name('galeri.store');
    Route::put('/galeri/{galeri}', [GaleriController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{galeri}', [GaleriController::class, 'destroy'])->name('galeri.destroy');
    Route::post('/galeri/reorder', [GaleriController::class, 'reorder'])->name('galeri.reorder');
    
    // Sejarah
    Route::post('/sejarah', [SejarahController::class, 'update'])->name('sejarah.update');
    
    // Layanan
    Route::post('/layanan', [LayananSpamController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/{layanan}', [LayananSpamController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{layanan}', [LayananSpamController::class, 'destroy'])->name('layanan.destroy');
    Route::post('/layanan/reorder', [LayananSpamController::class, 'reorder'])->name('layanan.reorder');
    
    // Payment Settings
    Route::get('/admin/payment-settings', [PaymentSettingController::class, 'index'])->name('admin.payment-settings');
    Route::post('/admin/payment-settings', [PaymentSettingController::class, 'update'])->name('admin.payment-settings.update');
    
    // Map Settings
    Route::get('/admin/map-settings', [\App\Http\Controllers\Admin\MapSettingController::class, 'index'])->name('admin.map-settings');
    Route::post('/map-settings', [\App\Http\Controllers\Admin\MapSettingController::class, 'store'])->name('map-settings.store');
    Route::put('/map-settings/{mapSetting}', [\App\Http\Controllers\Admin\MapSettingController::class, 'update'])->name('map-settings.update');
    Route::delete('/map-settings/{mapSetting}', [\App\Http\Controllers\Admin\MapSettingController::class, 'destroy'])->name('map-settings.destroy');
    
    // FAQ Management
    Route::get('/admin/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('admin.faqs');
    Route::post('/faqs', [\App\Http\Controllers\Admin\FaqController::class, 'store'])->name('faqs.store');
    Route::put('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('faqs.destroy');
    Route::post('/faqs/reorder', [\App\Http\Controllers\Admin\FaqController::class, 'reorder'])->name('faqs.reorder');
    
    // Informasi Tarif Management
    Route::get('/admin/informasi-tarif', [\App\Http\Controllers\Admin\InformasiTarifController::class, 'index'])->name('admin.informasi-tarif');
    Route::post('/informasi-tarif', [\App\Http\Controllers\Admin\InformasiTarifController::class, 'store'])->name('informasi-tarif.store');
    Route::put('/informasi-tarif/{informasiTarif}', [\App\Http\Controllers\Admin\InformasiTarifController::class, 'update'])->name('informasi-tarif.update');
    Route::delete('/informasi-tarif/{informasiTarif}', [\App\Http\Controllers\Admin\InformasiTarifController::class, 'destroy'])->name('informasi-tarif.destroy');
    Route::post('/informasi-tarif/reorder', [\App\Http\Controllers\Admin\InformasiTarifController::class, 'reorder'])->name('informasi-tarif.reorder');
    
    // Testimoni Management
    Route::get('/testimoni/all', [\App\Http\Controllers\Admin\TestimoniController::class, 'getAll'])->name('testimoni.all');
    Route::post('/testimoni/{id}/approve', [\App\Http\Controllers\Admin\TestimoniController::class, 'approve'])->name('testimoni.approve');
    Route::post('/testimoni/{id}/reject', [\App\Http\Controllers\Admin\TestimoniController::class, 'reject'])->name('testimoni.reject');
    Route::delete('/testimoni/{id}', [\App\Http\Controllers\Admin\TestimoniController::class, 'destroy'])->name('testimoni.destroy');

    // Manajemen Pengelola (Admin Users)
    Route::get('/admin/pengelola', [\App\Http\Controllers\Admin\PengelolaController::class, 'index'])->name('admin.pengelola.index');
    Route::post('/admin/pengelola/verify', [\App\Http\Controllers\Admin\PengelolaController::class, 'verifyPin'])->name('admin.pengelola.verify');
    Route::post('/admin/pengelola', [\App\Http\Controllers\Admin\PengelolaController::class, 'store'])->name('admin.pengelola.store');
    Route::put('/admin/pengelola/{user}', [\App\Http\Controllers\Admin\PengelolaController::class, 'update'])->name('admin.pengelola.update');
    Route::delete('/admin/pengelola/{user}', [\App\Http\Controllers\Admin\PengelolaController::class, 'destroy'])->name('admin.pengelola.destroy');
});
