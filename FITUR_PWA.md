# Panduan PWA (Progressive Web App) - PAMSIMAS

## Fitur yang Sudah Diterapkan ✅

Aplikasi PAMSIMAS sekarang sudah mendukung PWA dan bisa di-install sebagai aplikasi di smartphone. Berikut yang sudah ditambahkan:

### 1. Web App Manifest (`public/manifest.webmanifest`)
- Konfigurasi metadata aplikasi
- Mendefinisikan icon dalam berbagai ukuran
- Mengatur tampilan standalone mode
- Theme color dan background color

### 2. PWA Meta Tags (`resources/views/app.blade.php`)
- Theme color untuk address bar
- Apple Touch Icon untuk iOS
- Mobile web app capable
- Apple-specific PWA meta tags

### 3. Service Worker (`public/sw.js`)
- Caching strategy untuk offline support
- Network-first dengan fallback ke cache
- Auto-update cache version

### 4. Service Worker Registration (`resources/js/app.js`)
- Otomatis register service worker saat page load
- Console logging untuk debugging

### 5. Install Button Component (`resources/js/Components/InstallPWAButton.vue`) ✨
- Tombol install yang muncul otomatis di dashboard
- Smart detection: hanya muncul jika aplikasi belum di-install
- Fallback ke instruksi manual jika browser tidak support
- Auto-hide setelah aplikasi di-install
- Solusi untuk masalah "prompt tidak muncul setelah uninstall"

### 6. CSRF Token Auto-Refresh (`resources/js/app.js`) 🔒
- **Auto-refresh token setiap 50 menit** untuk mencegah token expired
- **Retry mechanism** otomatis jika kena error 419 (CSRF mismatch)
- **Global error handler** untuk semua Inertia request
- **Visibility change detection** - refresh token saat user kembali ke app
- Solusi permanen untuk error "CSRF token mismatch" di PWA
- 📖 Panduan lengkap: [SOLUSI_CSRF_MISMATCH.md](SOLUSI_CSRF_MISMATCH.md)

### 7. Security Layer untuk QR Scanner 🛡️
- **Rate limiting**: Max 30 scan/menit per IP, max 5 scan/2 menit per pelanggan
- **Audit logging**: Semua scan tercatat (IP, user, timestamp)
- **Authentication required**: Hanya user login yang bisa scan
- **Database validation**: QR code palsu tidak akan berfungsi
- 📖 Analisa lengkap: [KEAMANAN_QR_CODE.md](KEAMANAN_QR_CODE.md)

### 6. CSRF Token Auto-Refresh & Retry System ([app.js](resources/js/app.js)) 🔒
- **Auto-refresh CSRF token setiap 50 menit** untuk mencegah token expiry
- **Retry mechanism** otomatis jika request gagal karena CSRF mismatch (error 419)
- **Global error handler** untuk Inertia request
- **Visibility detection** - refresh token saat user kembali ke aplikasi
- **Solusi permanen untuk error "CSRF token mismatch"** di PWA

📖 **Detail lengkap:** [SOLUSI_CSRF_MISMATCH.md](SOLUSI_CSRF_MISMATCH.md)

## Langkah Selanjutnya 🚀

### 1. Buat Icon untuk Aplikasi

Anda perlu membuat icon aplikasi dalam berbagai ukuran dan menyimpannya di folder `public/images/`:

**Icon yang dibutuhkan:**

**Icon Biasa (any purpose):**
- `icon-72x72.png` (72×72 pixels)
- `icon-96x96.png` (96×96 pixels)
- `icon-128x128.png` (128×128 pixels)
- `icon-144x144.png` (144×144 pixels)
- `icon-152x152.png` (152×152 pixels)
- `icon-192x192.png` (192×192 pixels)
- `icon-384x384.png` (384×384 pixels)
- `icon-512x512.png` (512×512 pixels)

**Maskable Icon (untuk splash screen - PENTING!):**
- `icon-maskable-192x192.png` (192×192 pixels dengan padding 20%)
- `icon-maskable-512x512.png` (512×512 pixels dengan padding 20%)

⚠️ **PENTING untuk Splash Screen:**
Maskable icon harus punya **safe zone** (padding 20% di semua sisi) agar logo tidak kena crop oleh berbagai bentuk icon device (circle, rounded square, dll).

**Splash screen akan pakai:**
- `background_color: #FFFFFF` (putih) dari manifest
- `icon-maskable-512x512.png` sebagai logo

**Tips membuat icon:**
- Logo harus di dalam safe zone (80% tengah canvas)
- Background icon harus **PUTIH** (`#FFFFFF`) sesuai background_color
- Format: PNG
- **Jangan ada border hitam di file icon!**

📖 **Panduan lengkap:** [CARA_BIKIN_ICON_PWA.md](CARA_BIKIN_ICON_PWA.md)

### Background/Border Hitam di Splash Screen

**Masalah:** Logo muncul dengan border/background hitam saat splash screen loading.
**Cara Tercepat - Auto Generate:**
1. Buka: https://www.pwabuilder.com/imageGenerator
2. Upload logo DASAR WULAN
3. Set padding: 20%, background: putih `#FFFFFF`
4. Download semua icon (sudah include maskable!)
5. Upload ke folder `public/images/`

📖 **Panduan lengkap:** [CARA_BIKIN_ICON_PWA.md](CARA_BIKIN_ICON_PWA.md)

### Background/Border Hitam di Splash Screen

**Masalah:** Logo muncul dengan border/background hitam saat splash screen loading.

Setelah icon siap, jalankan build untuk compile assets:

```bash
npm run build
```

### 3. Testing di HP

#### Android:
1. Buka aplikasi di Chrome browser
2. Tap menu (3 titik) di kanan atas
3. Pilih "Install app" atau "Add to Home screen"
4. Icon aplikasi akan muncul di home screen

#### iOS (iPhone/iPad):
1. Buka aplikasi di Safari browser
2. Tap tombol Share (kotak dengan panah ke atas)
3. Scroll dan pilih "Add to Home Screen"
4. Tap "Add"
5. Icon aplikasi akan muncul di home screen

### 4. Persyaratan untuk Production

Untuk PWA berfungsi optimal di production:

- ✅ **HTTPS**: Aplikasi harus diakses via HTTPS (Service Worker requirement)
- ✅ **Valid manifest.webmanifest**: Sudah dibuat
- ✅ **Service Worker**: Sudah registered
- ⚠️ **Icons**: Perlu dibuat dan diupload
- ⚠️ **SSL Certificate**: Pastikan server production menggunakan HTTPS

## Troubleshooting

### ❗ Install Prompt Tidak Muncul Setelah Uninstall (PENTING!)

**Masalah:** Setelah uninstall aplikasi, ketika mau install lagi prompt/notifikasi tidak muncul.

**Penyebab:** Browser mengingat bahwa user pernah uninstall dan tidak akan menampilkan prompt otomatis lagi untuk menghindari spam.

**Solusi:**

#### 1. **Clear Site Settings (Tercepat)**
**Android Chrome:**
1. Buka aplikasi di Chrome
2. Tap ikon 🔒 atau ⓘ di address bar
3. Tap "Site settings"
4. Scroll ke bawah, tap "Clear & reset"
5. Refresh halaman, prompt install akan muncul lagi

**iOS Safari:**
- Hapus website data di Settings > Safari > Advanced > Website Data

#### 2. **Install Manual via Menu**
Prompt tidak muncul BUKAN berarti tidak bisa install!

**Android Chrome:**
- Tap menu (⋮) kanan atas → "Install app" (selalu ada di menu)

**iOS Safari:**
- Tap Share (📤) → "Add to Home Screen"

#### 3. **Tombol Install di Dashboard (TERBAIK!)**
✅ **Sudah diimplementasikan!** Sekarang ada tombol "Install Aplikasi" di dashboard yang akan:
- Muncul secara otomatis jika aplikasi belum di-install
- Menampilkan instruksi manual jika browser tidak support install prompt
- Hilang otomatis setelah aplikasi di-install

Dengan tombol ini, user tidak perlu bingung lagi!

### Background/Border Hitam di Splash Screen

**Masalah:** Logo muncul dengan border/background hitam saat splash screen loading.

**Penyebab:** 
- File icon asli memang punya background hitam
- Icon tidak punya padding yang cukup (safe zone)
- Background color di manifest tidak sesuai dengan background icon

**Solusi:**
1. **Buat maskable icon dengan benar:**
   - Background icon harus putih `#FFFFFF` (sesuai `background_color` di manifest)
   - Logo harus di dalam safe zone (20% padding dari semua sisi)
   - Ukuran: `icon-maskable-192x192.png` dan `icon-maskable-512x512.png`

2. **Generate otomatis:** https://www.pwabuilder.com/imageGenerator
   - Upload logo
   - Set padding: 20%
   - Background: putih
   - Download & upload ke `public/images/`

3. **Setelah upload icon baru:**
   - Uninstall aplikasi dari HP
   - Clear browser cache
   - Install ulang
   - Splash screen sekarang putih bersih!

📖 **Panduan lengkap:** [CARA_BIKIN_ICON_PWA.md](CARA_BIKIN_ICON_PWA.md)

### Icon tidak muncul
- Pastikan semua icon sudah diupload ke `public/images/`
- Clear browser cache
- Re-install aplikasi

### Install button tidak muncul
- Pastikan akses via HTTPS (di production)
- Di development, localhost tetap bisa dipakai
- Check console browser untuk error Service Worker

### Service Worker error
1. Buka browser DevTools (F12)
2. Tab Application > Service Workers
3. Check status dan error messages
4. Bisa unregister dan refresh untuk test ulang

### Error "CSRF token mismatch" ⚠️

**Masalah:** Error ini muncul saat scan QR code atau submit form setelah aplikasi lama tidak digunakan.

**Penyebab:** Token CSRF expired karena aplikasi di-minimize berjam-jam atau session timeout.

**Solusi:** ✅ **Sudah diimplementasikan!**
- Token otomatis refresh setiap 50 menit di background
- Auto-retry jika tetap kena error 419
- Reload page otomatis dengan token baru

**Jika masih terjadi:**
1. Build aplikasi: `npm run build`
2. Clear browser cache (Ctrl+Shift+R)
3. Uninstall & install ulang PWA
4. Lihat console untuk error log

📖 **Detail lengkap:** [SOLUSI_CSRF_MISMATCH.md](SOLUSI_CSRF_MISMATCH.md)

## File Changes Summary

### File Baru:
- `public/manifest.webmanifest` - PWA manifest configuration
- `public/sw.js` - Service Worker untuk offline support
- `resources/js/Components/InstallPWAButton.vue` - Tombol install PWA dengan smart detection
- `app/Http/Middleware/QRScanRateLimiter.php` - Rate limiting untuk QR scanner
- `SOLUSI_CSRF_MISMATCH.md` - Dokumentasi solusi CSRF token mismatch
- `KEAMANAN_QR_CODE.md` - Analisa keamanan QR code & best practices

### File Dimodifikasi:
- `resources/views/app.blade.php` - Tambah PWA meta tags
- `resources/js/app.js` - Register Service Worker + Auto-refresh CSRF token
- `resources/js/Pages/Dashboard.vue` - Tambah tombol install di user dashboard
- `resources/js/Pages/Admin/Dashboard.vue` - Tambah tombol install di admin dashboard
- `resources/js/Pages/QRScanner/Index.vue` - Pakai fetchWithCsrfRetry + Handle rate limit error
- `app/Http/Controllers/QRScannerController.php` - Tambah audit logging
- `bootstrap/app.php` - Register middleware QR rate limiter
- `routes/web.php` - Tambah API endpoint /api/csrf-token + Apply rate limit middleware

## Keuntungan PWA

✅ **Install seperti native app** - User bisa install dari browser tanpa app store  
✅ **Offline support** - Aplikasi tetap bisa diakses tanpa internet (cached pages)  
✅ **Fast loading** - Cache membuat loading lebih cepat  
✅ **No app store submission** - Tidak perlu publish ke Play Store / App Store  
✅ **Auto-update** - Update otomatis tanpa perlu user download manual  
✅ **Native-like experience** - Full screen, no browser UI  
✅ **Push notifications ready** - Siap untuk implementasi push notification (optional)

## Catatan

- PWA tidak sama dengan native app, masih ada keterbatasan akses hardware tertentu
- Untuk fitur lanjutan (push notification, background sync), perlu konfigurasi tambahan
- Test di berbagai device untuk memastikan compatibility
- Monitor Service Worker performance di production

## Support Browser

- ✅ Chrome Android (full support)
- ✅ Edge (full support)
- ✅ Safari iOS 11.3+ (limited support)
- ✅ Firefox Android (full support)
- ✅ Samsung Internet (full support)

---

**Selamat! Aplikasi PAMSIMAS sekarang sudah PWA-ready! 🎉**
