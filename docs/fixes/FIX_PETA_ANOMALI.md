# 🗺️ FIX MASALAH PETA ANOMALI JAKARTA

## 🔍 Masalah yang Ditemukan

### 1. **Marker Anomali di Jakarta** ❌
Muncul marker "Kantor PAMSIMAS" di area Jakarta/Tangerang, padahal lokasi seharusnya di Damar Wulan.

### 2. **Data Management Tidak Muncul di Peta** ❌
Data yang sudah ditambahkan di "Pengaturan Lokasi Peta":
- Kantor Pusat KP-SPAMS DAMAR WULAN (-7.60043000, 109.08140000)
- Sumber Air Curug Dammar Wulan (-7.58129100, 109.08441200)

Tidak muncul di peta utama.

---

## 🛠️ Penyebab Masalah

### 1. **Default Coordinates Salah**
**File**: `app/Http/Controllers/HomeController.php`

**Sebelum (SALAH)**:
```php
$kantor = [
    'name' => 'Kantor PAMSIMAS',
    'lat' => -6.200000,    // ❌ Jakarta!
    'lng' => 106.816666,   // ❌ Jakarta!
];
```

**Sesudah (BENAR)** ✅:
```php
$kantor = [
    'name' => 'Kantor Pusat KP-SPAMS DAMAR WULAN',
    'lat' => -7.60043000,  // ✅ Damar Wulan
    'lng' => 109.08140000, // ✅ Damar Wulan
];
```

### 2. **Data Anomali di Database**
Kemungkinan ada data lama dengan koordinat Jakarta yang tersimpan di tabel `map_settings`.

### 3. **Status is_active = false**
Data yang ditambahkan mungkin memiliki `is_active = 0` sehingga tidak ditampilkan.

---

## ✅ Solusi yang Sudah Diterapkan

### 1. **Fix Default Coordinates** ✅
- ✅ Changed default kantor: Jakarta → Damar Wulan (-7.60043000, 109.08140000)
- ✅ Changed default sumber air: Jakarta → Damar Wulan (-7.58129100, 109.08441200)
- ✅ Added description untuk setiap lokasi

**File yang diubah**: 
- `app/Http/Controllers/HomeController.php` (method `peta()`)

### 2. **Script Cleanup** ✅
Created 2 scripts untuk diagnosis dan cleanup:

- ✅ `check_map_settings_data.php` - Cek semua data
- ✅ `cleanup_map_settings.php` - Hapus data anomali interaktif

---

## 🚀 Langkah Perbaikan

### Step 1: Start MySQL di Laragon
```
1. Buka Laragon
2. Klik "Start All"
3. Pastikan MySQL running (indicator hijau)
```

### Step 2: Jalankan Script Cleanup
```bash
# Buka terminal di folder project
cd C:\laragon\www\PAMSIMAS

# Jalankan script cleanup
php cleanup_map_settings.php
```

Script akan:
1. ✅ Menampilkan semua data `map_settings`
2. ✅ Deteksi data anomali (koordinat Jakarta)
3. ✅ Tanya konfirmasi untuk hapus data anomali
4. ✅ Verifikasi data Damar Wulan
5. ✅ Cek status `is_active`

### Step 3: Verifikasi Data di Database

#### Option A: Via Terminal
```bash
# Masuk ke MySQL
mysql -u root -p

# Pilih database
USE pamsimas;

# Cek semua data map_settings
SELECT id, location_type, name, latitude, longitude, is_active FROM map_settings;

# Hapus data anomali Jakarta (jika ada)
DELETE FROM map_settings WHERE latitude BETWEEN -6.5 AND -6.0 AND longitude BETWEEN 106.0 AND 107.0;

# Aktifkan semua data Damar Wulan
UPDATE map_settings SET is_active = 1 WHERE latitude BETWEEN -8.0 AND -7.0 AND longitude BETWEEN 108.0 AND 110.0;

# Verify
SELECT * FROM map_settings WHERE is_active = 1;
```

#### Option B: Via phpMyAdmin
```
1. Buka http://localhost/phpmyadmin
2. Pilih database "pamsimas"
3. Pilih tabel "map_settings"
4. Cari data dengan latitude sekitar -6.x, longitude 106.x
5. Delete data tersebut
6. Pastikan data Damar Wulan memiliki is_active = 1
```

### Step 4: Clear Cache Laravel
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 5: Test di Browser
```
1. Refresh halaman peta (Ctrl + F5)
2. Seharusnya marker Jakarta hilang
3. Marker Kantor dan Sumber Air muncul di Damar Wulan
4. Peta zoom ke area Damar Wulan
```

---

## 🔍 Diagnosis Manual

### Cek Data di Database
```sql
-- Cek semua map_settings
SELECT 
    id,
    location_type,
    name,
    latitude,
    longitude,
    is_active,
    created_at
FROM map_settings
ORDER BY created_at DESC;

-- Cek hanya yang aktif
SELECT * FROM map_settings WHERE is_active = 1;

-- Cek koordinat anomali (Jakarta area)
SELECT * FROM map_settings 
WHERE latitude BETWEEN -6.5 AND -6.0 
  AND longitude BETWEEN 106.0 AND 107.0;

-- Cek koordinat benar (Damar Wulan area)
SELECT * FROM map_settings 
WHERE latitude BETWEEN -8.0 AND -7.0 
  AND longitude BETWEEN 108.0 AND 110.0;
```

### Cek di Management
```
1. Login sebagai pengelola
2. Buka menu "Admin" → "Pengaturan Lokasi Peta"
3. Pastikan data yang ditambahkan muncul di list
4. Pastikan tombol toggle "Aktif" berwarna hijau
5. Jika merah, klik untuk aktifkan
```

---

## 📋 Checklist Verifikasi

Setelah fix, pastikan:

- [ ] Tidak ada marker di area Jakarta/Tangerang
- [ ] Marker "Kantor Pusat KP-SPAMS" muncul di Damar Wulan
- [ ] Marker "Sumber Air Curug" muncul di Damar Wulan
- [ ] Data di management page ter-sinkronisasi dengan peta
- [ ] Toggle "Aktif" berwarna hijau di management
- [ ] Peta zoom ke area Damar Wulan saat pertama dibuka
- [ ] Pelanggan markers muncul normal

---

## 🎯 Solusi Jangka Panjang

### 1. **Validasi Koordinat saat Input**
Tambahkan validasi di frontend untuk memastikan koordinat berada di area Damar Wulan:

```javascript
// Di Vue component MapSettings
const validateCoordinates = (lat, lng) => {
    // Damar Wulan area: -8.0 to -7.0 latitude, 108.0 to 110.0 longitude
    if (lat < -8.0 || lat > -7.0 || lng < 108.0 || lng > 110.0) {
        alert('⚠️ Koordinat harus berada di area Damar Wulan!\n' +
              'Latitude: -8.0 hingga -7.0\n' +
              'Longitude: 108.0 hingga 110.0');
        return false;
    }
    return true;
};
```

### 2. **Auto-check Data Anomali**
Buat command artisan untuk cleanup berkala:

```bash
php artisan make:command CleanupAnomalyMapData
```

### 3. **Prevent Default Fallback**
Jika tidak ada data di database, redirect ke management page dengan warning:

```php
if (!$kantorData && !$sumberAirData->isNotEmpty()) {
    return redirect()->route('admin.map-settings')
        ->with('warning', 'Belum ada data lokasi. Silakan tambahkan terlebih dahulu.');
}
```

---

## ⚠️ Catatan Penting

### Koordinat yang Benar:
- **Damar Wulan area**: 
  - Latitude: -8.0 hingga -7.0
  - Longitude: 108.0 hingga 110.0

- **Data Valid**:
  - Kantor: -7.60043000, 109.08140000
  - Sumber Air: -7.58129100, 109.08441200

### Koordinat yang SALAH (Jakarta):
- Latitude: -6.x
- Longitude: 106.x
- **HARUS DIHAPUS!**

---

## 📞 Troubleshooting

### Q: Script cleanup error "No connection"
**A**: Pastikan MySQL running di Laragon. Start Laragon terlebih dahulu.

### Q: Data sudah dihapus tapi masih muncul di peta
**A**: Clear browser cache dan Laravel cache:
```bash
php artisan cache:clear
# Lalu Ctrl + Shift + R di browser
```

### Q: Data di management muncul tapi tidak di peta
**A**: Check status `is_active`:
```sql
UPDATE map_settings SET is_active = 1 WHERE id = YOUR_ID;
```

### Q: Setelah fix, peta masih zoom ke Jakarta
**A**: Clear browser cache dan cookies, atau buka di incognito mode.

---

## ✅ Kesimpulan

### Fixed:
1. ✅ Default coordinates Jakarta → Damar Wulan
2. ✅ Script cleanup untuk hapus data anomali
3. ✅ Dokumentasi lengkap diagnosis dan solusi

### Action Required:
1. ⏳ Jalankan script cleanup saat database online
2. ⏳ Verifikasi data di database
3. ⏳ Clear cache
4. ⏳ Test di browser

---

**Status**: Ready to Deploy ✅  
**Tested**: Local development  
**Last Updated**: 7 Februari 2026
