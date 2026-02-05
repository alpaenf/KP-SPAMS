# Fitur Pengiriman Struk Pembayaran via WhatsApp

## Deskripsi
Fitur ini memungkinkan pengelola/admin PAMSIMAS untuk mengirim struk pembayaran digital kepada pelanggan melalui WhatsApp. Struk dibuat dalam format PDF dan dapat langsung dikirim dengan sekali klik.

## Cara Penggunaan

### 1. Buka Halaman Cek Pelanggan
- Login sebagai pengelola/admin
- Navigasi ke menu **Cek Pelanggan**

### 2. Buka Riwayat Pembayaran Pelanggan
- Klik tombol **Riwayat Pembayaran** (ikon ungu) pada pelanggan yang diinginkan
- Modal akan muncul menampilkan daftar pembayaran pelanggan tersebut

### 3. Kirim Struk Pembayaran
- Pada tabel riwayat pembayaran, klik tombol **Kirim Struk** (ikon hijau berbentuk pesawat) pada baris pembayaran yang ingin dikirim
- Sistem akan:
  - Generate PDF struk pembayaran otomatis
  - Membuka WhatsApp Web dengan pesan pre-filled yang berisi:
    - Ucapan terima kasih
    - Detail pembayaran (ID pelanggan, bulan tagihan, tanggal bayar, total bayar)
    - Link download struk PDF
  - Menyimpan PDF struk ke folder `public/storage/struk/`

### 4. Kirim Pesan WhatsApp
- Jendela WhatsApp Web akan terbuka otomatis
- Pesan sudah terisi lengkap dengan detail pembayaran
- Klik tombol **Send** di WhatsApp untuk mengirim struk kepada pelanggan

## Format Struk Pembayaran

Struk pembayaran mencakup informasi berikut:
- **Header**: Logo dan nama KP-SPAMS "DAMMAR WULAN"
- **Info Pelanggan**: 
  - ID Pelanggan
  - Nama
  - Alamat (RT/RW)
  - Nomor WhatsApp
- **Info Transaksi**:
  - Nomor Transaksi
  - Tanggal Bayar
  - Bulan Tagihan
- **Rincian Pembayaran**:
  - Meteran sebelum dan sesudah (jika ada)
  - Pemakaian air dalam m³
  - Tarif per m³
  - Subtotal
  - Abunemen (jika ada)
  - Tunggakan (jika ada)
  - **Total Bayar**
- **Status**: Badge LUNAS
- **Footer**: Informasi waktu cetak dan catatan

## Persyaratan

### Pelanggan harus memiliki:
1. **Nomor WhatsApp** yang terdaftar di sistem
2. **Riwayat pembayaran** yang valid

### Jika nomor WhatsApp tidak tersedia:
- Sistem akan menampilkan pesan error
- Pengelola harus menambahkan nomor WhatsApp pelanggan terlebih dahulu melalui menu **Edit Pelanggan**

## File yang Terlibat

### 1. Backend
- **Controller**: `app/Http/Controllers/PembayaranController.php`
  - Method `sendReceipt()`: Generate PDF dan link WhatsApp
  - Method `getReceiptLink()`: Get link WhatsApp dari struk yang sudah ada
  
- **View Template**: `resources/views/pdf/struk-pembayaran.blade.php`
  - Template Blade untuk generate PDF struk
  
- **Routes**: `routes/web.php`
  - `POST /pembayaran/{pembayaran}/send-receipt`
  - `GET /pembayaran/{pembayaran}/receipt-link`

### 2. Frontend
- **Component**: `resources/js/Pages/CekPelanggan.vue`
  - Function `sendReceipt()`: Handle klik tombol kirim struk
  - Tombol kirim struk di tabel riwayat pembayaran

### 3. Storage
- **Folder**: `public/storage/struk/`
  - Menyimpan file PDF struk pembayaran
  - Format nama file: `struk_{ID_PELANGGAN}_{BULAN_TAHUN}.pdf`
  - Contoh: `struk_SPM001_202602.pdf`

## Format Pesan WhatsApp

```
Halo *[Nama Pelanggan]*,

Terima kasih atas pembayaran Anda untuk tagihan bulan *[Bulan Tahun]*.

📋 *Detail Pembayaran:*
• ID Pelanggan: [ID]
• Bulan Tagihan: [Bulan Tahun]
• Tanggal Bayar: [Tanggal]
• Total Bayar: Rp [Jumlah]

✅ Status: *LUNAS*

Struk pembayaran Anda dapat diunduh melalui link berikut:
[URL Struk PDF]

Simpan struk ini sebagai bukti pembayaran yang sah.

Salam,
*KP-SPAMS DAMMAR WULAN*
```

## Keamanan & Privacy

1. **File PDF disimpan di public storage** - dapat diakses siapa saja yang memiliki link
2. **Link WhatsApp** - hanya dapat dibuka oleh pengelola yang memiliki akses
3. **Nomor WhatsApp** - diformat otomatis ke format internasional (62xxx)
4. **File PDF tidak di-commit ke Git** - folder `/storage/struk` ada di `.gitignore`

## Troubleshooting

### Error: "Nomor WhatsApp pelanggan tidak tersedia"
**Solusi**: Edit data pelanggan dan tambahkan nomor WhatsApp

### Error: "Data pelanggan tidak ditemukan"
**Solusi**: Pastikan data pelanggan masih ada di database

### WhatsApp tidak terbuka
**Solusi**: 
- Pastikan browser mengizinkan popup
- Coba refresh halaman dan ulangi
- Pastikan WhatsApp Web sudah login

### PDF tidak ter-generate
**Solusi**:
- Pastikan folder `public/storage/struk` memiliki permission yang benar (755)
- Cek log Laravel di `storage/logs/laravel.log`
- Pastikan package DomPDF terinstall (`composer require barryvdh/laravel-dompdf`)

## Pengembangan Lebih Lanjut

Fitur yang bisa ditambahkan:
1. **Auto-send via WhatsApp API** - kirim otomatis tanpa membuka WhatsApp Web
2. **Email delivery** - alternatif pengiriman via email
3. **SMS gateway** - alternatif untuk pelanggan tanpa WhatsApp
4. **Bulk send** - kirim struk ke multiple pelanggan sekaligus
5. **Template customization** - pengelola dapat customize template struk
6. **Digital signature** - tambah tanda tangan digital pada struk
7. **QR Code verification** - QR code untuk verifikasi keaslian struk

## Lisensi & Credit
Fitur ini dikembangkan untuk KP-SPAMS "DAMMAR WULAN" Desa Lempeni, Kec. Swakarya, Grobogan.
