# 🔐 Keamanan QR Code PAMSIMAS

## ❓ Apakah QR Code Statis Aman Dipakai Bertahun-tahun?

**Jawaban Singkat:** 
- ✅ **Relatif aman** untuk use case internal (petugas scan untuk input meteran)
- ⚠️ **Perlu proteksi tambahan** untuk mencegah abuse
- ❌ **Tidak aman** jika QR code disebarkan ke publik atau bisa diakses unauthorized person

---

## 🎯 Analisa Risk QR Code Statis

### Use Case PAMSIMAS:
- QR code berisi: **ID Pelanggan** (contoh: `DWH0001`)
- Fungsi: Petugas scan → Lihat data pelanggan → Input meteran
- QR code tidak berubah selamanya (printed di kartu)

### ⚠️ Potential Security Risks:

#### 1. **Data Exposure** 
**Risk:** Siapa saja yang scan QR bisa lihat data pelanggan (nama, no WA, RT/RW, tagihan)

**Impact:** 
- Privacy breach
- Data pelanggan bocor ke orang tidak berhak

**Mitigasi:** ✅ **Sudah diterapkan**
- Scan hanya bisa dilakukan user yang sudah **authenticated** (login)
- Tidak bisa scan dari public

#### 2. **QR Code Theft/Copy**
**Risk:** Orang lain foto QR code → Bisa scan kapan saja

**Impact:**
- Akses unauthorized ke data pelanggan
- Bisa input meteran palsu

**Mitigasi:** ✅ **Sudah diterapkan**
- **Rate limiting**: Max 5 scan per pelanggan dalam 2 menit
- **Audit log**: Semua scan tercatat (IP, timestamp, user)
- **Authentication required**: Hanya user login yang bisa scan

#### 3. **QR Forgery (Pemalsuan)**
**Risk:** Buat QR code palsu dengan ID sembarang (DWH9999)

**Impact:**
- Akses ke data pelanggan lain
- Manipulasi data

**Mitigasi:** ⚠️ **Partial**
- ✅ Validasi: ID harus exist di database
- ✅ Audit log: Tercatat siapa scan apa
- ❌ Tidak ada signature/encryption di QR

#### 4. **Brute Force / Scanning Spree**
**Risk:** Scan banyak QR code secara cepat untuk collect data

**Impact:**
- Mass data harvesting
- Privacy breach skala besar

**Mitigasi:** ✅ **Sudah diterapkan**
- **Rate limiting**: Max 30 scan per menit per IP
- **Rate limiting per pelanggan**: Max 5 scan dalam 2 menit
- **Audit log**: Detect suspicious patterns

#### 5. **Long-term Exposure**
**Risk:** QR code sama bertahun-tahun = window of vulnerability panjang

**Impact:**
- Semakin lama, semakin banyak orang yang bisa punya copy QR
- Printed QR card hilang/dicuri

**Mitigasi:** ⚠️ **Belum ada solusi otomatis**
- Edukasi pelanggan: Jangan share/foto QR code
- Report jika kartu hilang → Block akses (manual)

---

## ✅ Security Layer yang Sudah Diterapkan

### 1. **Authentication Required**
```php
Route::middleware(['auth'])->group(function () {
    Route::post('/api/qr-scanner/scan', ...)
});
```
- Hanya user yang sudah login bisa scan
- Public tidak bisa akses

### 2. **Rate Limiting** ([QRScanRateLimiter.php](app/Http/Middleware/QRScanRateLimiter.php))

**Per IP:**
- Max **30 scan per menit**
- Mencegah brute force dari satu device

**Per Pelanggan:**
- Max **5 scan dalam 2 menit** untuk ID pelanggan yang sama
- Mencegah spam scan QR code yang sama

**Error Response:**
```json
{
  "success": false,
  "message": "Terlalu banyak permintaan. Silakan tunggu beberapa saat."
}
```

### 3. **Audit Logging** ([QRScannerController.php](app/Http/Controllers/QRScannerController.php))

Setiap scan QR code tercatat:
```php
\Log::info('QR Scan Success', [
    'id_pelanggan' => 'DWH0001',
    'nama_pelanggan' => 'Budi',
    'ip' => '192.168.1.10',
    'user_id' => 5,
    'user_agent' => 'Mozilla/5.0...',
    'timestamp' => '2026-02-07 13:45:22'
]);
```

**Manfaat:**
- Tracking siapa scan apa kapan
- Detect suspicious activity
- Forensic jika ada incident

**Location log:** `storage/logs/laravel.log`

### 4. **Database Validation**
```php
$pelanggan = Pelanggan::where('id_pelanggan', $request->id_pelanggan)->first();

if (!$pelanggan) {
    return response()->json(['message' => 'Pelanggan tidak ditemukan'], 404);
}
```
- QR code palsu tidak akan berfungsi
- Hanya ID yang exist di database bisa di-scan

---

## 🚀 Rekomendasi Security Best Practices

### Untuk Operator/Admin:

1. **✅ Jangan Share QR Scanner Access**
   - Gunakan akun terpisah untuk setiap petugas
   - Jangan share password

2. **✅ Monitor Log Secara Berkala**
   ```bash
   tail -f storage/logs/laravel.log | grep "QR Scan"
   ```
   - Check ada pattern scan yang aneh?
   - Ada scan di waktu tidak wajar?

3. **✅ Edukasi Petugas**
   - Jangan foto/share screenshot hasil scan
   - Jangan tinggalkan aplikasi terbuka di HP

4. **✅ Secure Device**
   - Pasang PIN/password di HP
   - Auto-lock setelah beberapa menit idle
   - Logout setelah selesai tugas

### Untuk Pelanggan:

1. **✅ Jaga Kartu QR Code**
   - Jangan share foto kartu
   - Simpan kartu dengan aman
   - Lapor jika hilang/dicuri

2. **✅ Verify Petugas**
   - Pastikan yang scan adalah petugas resmi
   - Tanya jika ada scan yang mencurigakan

---

## 🔮 Opsi Enhanced Security (Opsional)

Jika butuh security lebih tinggi, bisa implementasi:

### 1. **Signed QR Code dengan Expiry**

**Konsep:**
- QR code berisi: `{id: "DWH0001", exp: "2026-03-01", sig: "abc123..."}`
- Signature = HMAC dari `id + exp + secret_key`
- Server validate signature sebelum process

**Keuntungan:**
- ✅ QR code tidak bisa di-forge
- ✅ Ada expiry date (regenerate berkala)
- ✅ Lebih aman

**Kerugian:**
- ❌ QR code printed tidak bisa dipakai selamanya
- ❌ Perlu regenerate & reprint kartu berkala
- ❌ Lebih kompleks

### 2. **One-Time QR Code**

**Konsep:**
- QR code hanya valid untuk 1x scan
- Setelah di-scan, QR invalid → Generate QR baru

**Keuntungan:**
- ✅ Sangat aman
- ✅ QR stolen tidak berguna setelah sekali pakai

**Kerugian:**
- ❌ Tidak cocok untuk printed card
- ❌ Perlu digital QR (via app/website)

### 3. **Dynamic QR Code (Time-based)**

**Konsep:**
- QR code berubah setiap 5 menit (seperti Google Authenticator)
- Pelanggan buka app → QR code temporary

**Keuntungan:**
- ✅ Extremely secure
- ✅ QR stolen cepat expired

**Kerugian:**
- ❌ Pelanggan perlu smartphone & app
- ❌ Tidak cocok jika pelanggan tidak tech-savvy
- ❌ Development effort besar

---

## 📊 Comparison: Static vs Dynamic QR

| Aspek | Static QR (Current) | Signed QR | One-Time QR |
|-------|---------------------|-----------|-------------|
| **Security** | Medium | High | Very High |
| **Convenience** | ✅ Very High | Medium | Low |
| **Setup Cost** | Low | Medium | High |
| **Maintenance** | Low | Medium | High |
| **User Friendliness** | ✅ Excellent | Good | Fair |
| **Scalability** | ✅ Excellent | Good | Good |

---

## 🎯 Kesimpulan & Rekomendasi

### Untuk PAMSIMAS Use Case:

**✅ Static QR Code (yang sekarang) SUDAH CUKUP AMAN**, dengan alasan:

1. **Audience Internal**  
   - Hanya petugas resmi yang punya akses scan
   - Bukan public-facing system

2. **Auth Required**  
   - Perlu login untuk scan
   - Tidak bisa diakses unauthorized

3. **Rate Limiting Active**  
   - Mencegah abuse & brute force
   - Max 30 scan/menit per IP
   - Max 5 scan/2 menit per pelanggan

4. **Audit Trail**  
   - Semua activity tercatat
   - Bisa track jika ada suspicious activity

5. **Convenience vs Security**  
   - Static QR = printed card yang awet bertahun-tahun
   - Tidak perlu reprint card berkala
   - User-friendly untuk pelanggan non-tech

### ⚠️ Kondisi yang Perlu Enhanced Security:

Pertimbangkan **Signed QR** atau **Dynamic QR** jika:

- ❌ QR code accessible dari public (tanpa auth)
- ❌ Ada history data breach/abuse
- ❌ Compliance requirement ketat (finansial/healthcare)
- ❌ Data super sensitive (KTP, financial info)

### ✅ Action Items:

**Current Implementation (DONE):**
- ✅ Rate limiting middleware
- ✅ Audit logging
- ✅ Authentication required

**Recommended Next Steps:**
1. **Monitor logs** secara berkala (weekly)
2. **Edukasi** petugas & pelanggan tentang keamanan
3. Setup **alert** jika ada suspicious pattern
   - Contoh: >10 scan attempt gagal dari 1 IP
4. **Backup logs** secara berkala (untuk forensic)

**Optional (jika diperlukan nanti):**
- Implement signed QR code
- Add QR regeneration feature (untuk kartu hilang)
- Add pelanggan-level access control

---

## 📁 File yang Dimodifikasi

1. ✅ `app/Http/Middleware/QRScanRateLimiter.php` - Rate limiting
2. ✅ `app/Http/Controllers/QRScannerController.php` - Audit logging
3. ✅ `bootstrap/app.php` - Register middleware
4. ✅ `routes/web.php` - Apply middleware ke scan endpoint
5. ✅ `KEAMANAN_QR_CODE.md` - Dokumentasi (file ini)

---

## 🔍 Cara Check Log Scan Activity

### Via Command Line:
```bash
# Lihat semua QR scan hari ini
tail -100 storage/logs/laravel.log | grep "QR Scan"

# Lihat scan yang gagal
grep "QR Scan Failed" storage/logs/laravel.log

# Monitor real-time
tail -f storage/logs/laravel.log | grep "QR Scan"
```

### Via Laravel Tinker:
```bash
php artisan tinker

# Baca log file
$logs = file_get_contents(storage_path('logs/laravel.log'));
echo $logs;
```

---

## 🛡️ Incident Response Plan

**Jika Menemukan Suspicious Activity:**

1. **Identifikasi Pattern**
   - IP/user mana yang mencurigakan?
   - Berapa banyak scan attempts?
   - ID pelanggan mana yang di-target?

2. **Block Access (Temporary)**
   ```php
   // Tambahkan IP ke blacklist di middleware
   $blacklistedIps = ['192.168.1.100', '10.0.0.50'];
   if (in_array($request->ip(), $blacklistedIps)) {
       abort(403, 'Access Denied');
   }
   ```

3. **Investigate**
   - Check user account yang digunakan
   - Verify apakah legitimate atau compromised

4. **Remediate**
   - Reset password account yang affected
   - Inform pelanggan jika data mereka diakses unauthorized
   - Update security policy

5. **Document**
   - Catat incident details
   - Lessons learned
   - Update procedure

---

**Kesimpulan Akhir:**  
Static QR code **AMAN** untuk use case PAMSIMAS saat ini, dengan security layer yang sudah diterapkan. Monitoring dan edukasi adalah kunci keamanan jangka panjang! 🔐
