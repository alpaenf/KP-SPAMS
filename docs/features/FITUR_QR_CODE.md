# Fitur QR Code Scanner untuk Input Meteran

## Overview
Fitur ini memungkinkan petugas penarikan untuk scan QR code pelanggan dan langsung input meteran air tanpa perlu mencari data pelanggan secara manual.

## Fitur Utama

### 1. Generate QR Code untuk Pelanggan
- Setiap pelanggan memiliki QR code unik berdasarkan ID Pelanggan
- QR code dapat di-download dan dicetak pada kartu pelanggan
- Format: PNG, ukuran 400x400 pixel

### 2. Scan QR Code
- **Menggunakan Kamera**: 
  - Akses halaman `/qr-scanner`
  - Izinkan akses kamera
  - Arahkan kamera ke QR code pelanggan
  - Sistem akan otomatis mendeteksi dan membaca QR code
  
- **Input Manual**:
  - Jika kamera tidak tersedia atau bermasalah
  - Bisa input ID Pelanggan secara manual
  - Klik toggle "Input Manual ID Pelanggan"

### 3. Input Meteran
Setelah scan berhasil, sistem akan menampilkan:
- Data lengkap pelanggan
- Meteran terakhir yang tercatat
- Form input meteran baru dengan perhitungan otomatis

### 4. Perhitungan Otomatis
Sistem akan otomatis menghitung:
- Pemakaian air (m³)
- Tarif berdasarkan pemakaian
- Biaya abunemen
- Total tagihan
- Minimal pemakaian (jika ada)

## Cara Penggunaan

### A. Untuk Admin - Generate dan Cetak QR Code

1. **Download QR Code Individual**:
   ```
   Buka halaman pelanggan → Klik "Download QR Code"
   URL: /pelanggan/{id}/download-qr
   ```

2. **Cetak QR Code**:
   - Download QR code pelanggan
   - Cetak pada kartu pelanggan atau stiker
   - Tempelkan di lokasi yang mudah diakses petugas

3. **Generate QR Code dalam Program**:
   ```php
   $pelanggan = Pelanggan::find($id);
   $qrCode = $pelanggan->generateQrCode('png', 200);
   // atau sebagai base64
   $qrCodeBase64 = $pelanggan->qr_code_base64;
   ```

### B. Untuk Petugas Penarikan - Scan dan Input

1. **Akses Halaman QR Scanner**:
   - Login ke sistem
   - Klik menu "QR Scanner" atau akses `/qr-scanner`
   - Izinkan akses kamera (jika diminta browser)

2. **Scan QR Code**:
   - Arahkan kamera ke QR code pada kartu pelanggan
   - Tunggu hingga sistem membaca QR code (otomatis)
   - Data pelanggan akan muncul di layar

3. **Periksa Data Pelanggan**:
   - Pastikan nama dan alamat sesuai
   - Lihat meteran terakhir yang tercatat
   - Klik "Input Meteran" untuk melanjutkan

4. **Input Data Meteran**:
   - Pilih bulan tagihan (default: bulan ini)
   - Meteran sebelum sudah terisi otomatis
   - Input meteran sesudah (pembacaan saat ini)
   - Sistem akan menghitung pemakaian dan tagihan secara otomatis
   - Tambahkan keterangan jika perlu
   - Klik "Simpan Data Meteran"

5. **Selesai**:
   - Sistem akan menampilkan konfirmasi berhasil
   - Total tagihan yang dibuat akan ditampilkan
   - Pilih "Scan Lagi" untuk pelanggan berikutnya
   - Atau "Dashboard" untuk kembali ke menu utama

## Technical Details

### Routes
```php
// QR Scanner Routes (Protected with auth middleware)
GET  /qr-scanner                        // Halaman scan QR code
POST /api/qr-scanner/scan               // API untuk proses scan
GET  /qr-scanner/input-meteran/{id}     // Halaman input meteran
POST /api/qr-scanner/store-meteran      // API simpan data meteran
GET  /pelanggan/{id}/download-qr        // Download QR code
```

### Controller: QRScannerController

**Methods**:
- `index()` - Tampilkan halaman scanner
- `scan(Request)` - Proses hasil scan dan ambil data pelanggan
- `inputMeteran($id)` - Tampilkan form input meteran
- `storeMeteran(Request)` - Simpan data meteran baru
- `downloadQR($id)` - Download QR code pelanggan

### Model: Pelanggan

**QR Code Methods**:
```php
// Generate QR Code
$pelanggan->generateQrCode($format = 'png', $size = 200);

// Get QR Code as base64
$pelanggan->qr_code_base64;
```

### Vue Components

**QRScanner/Index.vue**:
- Camera scanner dengan live preview
- Auto-detect QR code menggunakan jsQR library
- Fallback ke manual input jika kamera tidak tersedia
- Tampilkan data pelanggan setelah scan berhasil

**QRScanner/InputMeteran.vue**:
- Form input meteran dengan validasi
- Perhitungan otomatis pemakaian dan tagihan
- Display meteran terakhir sebagai referensi
- Modal konfirmasi setelah berhasil simpan

## Dependencies

### PHP Packages
- `simplesoftwareio/simple-qrcode` - Generate QR code di backend

### NPM Packages
- `jsqr` - Scan QR code di browser menggunakan kamera

## Database Schema

Data meteran disimpan di tabel `tagihan_bulanan`:
```sql
- pelanggan_id (FK to pelanggan)
- bulan (DATE)
- meteran_sebelum (DECIMAL)
- meteran_sesudah (DECIMAL)
- pemakaian_kubik (DECIMAL)
- tarif_per_kubik (DECIMAL)
- ada_abunemen (BOOLEAN)
- biaya_abunemen (DECIMAL)
- total_tagihan (DECIMAL)
- status_bayar (ENUM)
- keterangan (TEXT)
```

## Validasi

### Scan QR Code
- ID Pelanggan harus ada di database
- Pelanggan harus aktif

### Input Meteran
- Meteran sesudah harus lebih besar atau sama dengan meteran sebelum
- Bulan tagihan wajib diisi
- Tidak boleh duplikat tagihan untuk bulan yang sama

## Error Handling

- **Kamera tidak tersedia**: Fallback ke input manual
- **QR Code tidak terbaca**: Retry atau gunakan input manual
- **Pelanggan tidak ditemukan**: Tampilkan error message
- **Meteran tidak valid**: Validasi di frontend dan backend
- **Duplikat tagihan**: Cegah pembuatan tagihan ganda

## Security

- Semua routes dilindungi dengan middleware `auth`
- CSRF token protection untuk semua POST request
- Validasi input di backend
- Sanitasi data sebelum disimpan

## Tips & Best Practices

1. **Cetak QR Code yang Jelas**:
   - Gunakan ukuran minimal 5x5 cm
   - Cetak dengan printer berkualitas baik
   - Gunakan material tahan air untuk outdoor

2. **Pencahayaan Saat Scan**:
   - Pastikan area cukup terang
   - Hindari pantulan cahaya langsung ke QR code
   - Jarak optimal: 10-30 cm dari kamera

3. **Pengelolaan Data**:
   - Lakukan input meteran rutin setiap bulan
   - Backup data secara berkala
   - Review data yang sudah diinput

4. **Training Petugas**:
   - Berikan pelatihan cara menggunakan scanner
   - Ajarkan cara handle error/masalah
   - Sediakan manual guide di lapangan

## Troubleshooting

### QR Code tidak terbaca
- Pastikan QR code tidak rusak atau kotor
- Cek pencahayaan
- Coba zoom in/out kamera
- Gunakan input manual sebagai alternatif

### Kamera tidak bisa diakses
- Periksa permission browser
- Pastikan HTTPS diaktifkan (kamera hanya bisa diakses di HTTPS)
- Coba browser lain
- Gunakan input manual

### Perhitungan tagihan tidak sesuai
- Cek tarif aktif di pengaturan
- Pastikan meteran diinput dengan benar
- Review konfigurasi minimal pemakaian

## Future Enhancements

1. **Bulk QR Code Generation**: Generate dan print QR code untuk banyak pelanggan sekaligus
2. **Offline Mode**: Support scan dan input saat tidak ada internet, sync nanti
3. **Barcode Support**: Tambahkan support untuk barcode selain QR code
4. **Foto Meteran**: Ambil foto meteran sebagai bukti
5. **GPS Location**: Record lokasi saat input meteran
6. **Signature**: Tanda tangan pelanggan digital
7. **Push Notification**: Notifikasi ke pelanggan setelah meteran diinput

## Support

Jika ada pertanyaan atau masalah, hubungi tim IT PAMSIMAS.
