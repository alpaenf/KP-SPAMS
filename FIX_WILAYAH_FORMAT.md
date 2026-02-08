# FIX Wilayah Format Mismatch

## Masalah

**Login penarik kubangsari_kulon menunjukkan 0 pelanggan padahal di peta ada 3 pelanggan.**

**Root cause:** Format wilayah tidak match

- **Tabel users:** `kubangsari_kulon` (lowercase + underscore)
- **Tabel pelanggan:** `Kubangsari Kulon` (Title Case + spasi)

Scope `forUser()` mencari pelanggan dengan wilayah `kubangsari_kulon`, tapi di database pelanggan tersimpan sebagai `Kubangsari Kulon` → **tidak ketemu!**

## Solusi: Normalize Format Wilayah

Migration dibuat untuk standardisasi format wilayah di tabel `pelanggan`:

```
Dawuhan          → dawuhan
Kubangsari Kulon → kubangsari_kulon
Kubangsari Wetan → kubangsari_wetan
Sokarame         → sokarame
Tiparjaya        → tiparjaya
```

## Cara Deploy ke Production

### Step 1: Push migration ke repo
```bash
git add database/migrations/2026_02_08_064818_normalize_wilayah_format_in_pelanggan_table.php
git commit -m "Fix: normalize wilayah format in pelanggan table"
git push origin main
```

### Step 2: SSH ke server
```bash
ssh kpsx9679@kp-spamsdammarwulan.com -p2223
```

### Step 3: Pull changes dan run migration
```bash
cd domains/kp-spamsdammarwulan.com/public_html

# Backup database dulu
php artisan db:dump

# Pull latest code
git pull origin main

# Run migration
php artisan migrate --force

# Verify
php artisan tinker --execute="
echo 'Pelanggan per wilayah:' . PHP_EOL;
\$results = \App\Models\Pelanggan::select('wilayah', DB::raw('count(*) as total'))
    ->groupBy('wilayah')
    ->orderBy('wilayah')
    ->get();
foreach (\$results as \$r) {
    echo '  ' . \$r->wilayah . ' → ' . \$r->total . ' pelanggan' . PHP_EOL;
}
"
```

### Step 4: Test login penarik

1. Login sebagai **kubkul@pamsimas.com** (password: `password`)
2. Check dashboard → harus muncul **3 pelanggan** (DWH0001, KBG0001, KBG0002)
3. Check Cek Pelanggan → harus tampil 3 pelanggan Kubangsari Kulon
4. Check QR Scanner → scan QR Kubangsari Kulon harus berhasil

## Rollback (jika ada masalah)

```bash
php artisan migrate:rollback --step=1
```

Migration akan mengembalikan format ke Title Case + spasi.

## Expected Result

Setelah migration:

```
✅ dawuhan → 20+ pelanggan
✅ kubangsari_kulon → 3 pelanggan
✅ kubangsari_wetan → 1+ pelanggan
✅ sokarame → 1+ pelanggan
✅ tiparjaya → 1+ pelanggan
```

Semua penarik bisa lihat data pelanggan sesuai wilayah mereka! 🎉
