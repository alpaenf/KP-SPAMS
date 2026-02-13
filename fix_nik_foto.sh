#!/bin/bash

# Script untuk check dan fix masalah NIK dan Foto Rumah
# Jalankan di server production

echo "======================================"
echo "CHECK & FIX NIK DAN FOTO RUMAH"
echo "======================================"
echo ""

# 1. Check struktur database
echo "1. Checking database structure..."
php artisan tinker --execute="
\$columns = DB::select('DESCRIBE pelanggan');
\$hasNik = false;
\$hasFoto = false;
foreach (\$columns as \$col) {
    if (\$col->Field === 'nik') {
        \$hasNik = true;
        echo '   ✓ Kolom nik ditemukan\n';
    }
    if (\$col->Field === 'foto_rumah') {
        \$hasFoto = true;
        echo '   ✓ Kolom foto_rumah ditemukan\n';
    }
}
if (!\$hasNik || !\$hasFoto) {
    echo '   ✗ KOLOM TIDAK LENGKAP! Perlu migration.\n';
    exit(1);
}
"

if [ $? -ne 0 ]; then
    echo ""
    echo "   Migration diperlukan. Menjalankan migration..."
    php artisan migrate --force
    
    if [ $? -eq 0 ]; then
        echo "   ✓ Migration berhasil dijalankan"
    else
        echo "   ✗ Migration gagal! Check error di atas."
        exit 1
    fi
fi

# 2. Check dan buat storage link
echo ""
echo "2. Checking storage link..."
if [ -L "public/storage" ] || [ -d "public/storage" ]; then
    echo "   ✓ Storage link sudah ada"
else
    echo "   Creating storage link..."
    php artisan storage:link
    echo "   ✓ Storage link berhasil dibuat"
fi

# 3. Check permission
echo ""
echo "3. Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || chown -R $USER:$USER storage bootstrap/cache
echo "   ✓ Permissions set"

# 4. Clear cache
echo ""
echo "4. Clearing cache..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo "   ✓ Cache cleared"

# 5. Check data
echo ""
echo "5. Checking existing data..."
php artisan tinker --execute="
\$withNik = App\Models\Pelanggan::whereNotNull('nik')->count();
\$withFoto = App\Models\Pelanggan::whereNotNull('foto_rumah')->count();
echo \"   Pelanggan dengan NIK: \$withNik\n\";
echo \"   Pelanggan dengan Foto: \$withFoto\n\";

if (\$withNik > 0 || \$withFoto > 0) {
    echo \"\n   Sample data (3 pertama):\n\";
    \$samples = App\Models\Pelanggan::where(function(\$q) {
        \$q->whereNotNull('nik')->orWhereNotNull('foto_rumah');
    })->limit(3)->get();
    
    foreach (\$samples as \$p) {
        echo \"   - {\$p->id_pelanggan}: {\$p->nama_pelanggan}\n\";
        echo \"     NIK: \" . (\$p->nik ?: '(kosong)') . \"\n\";
        echo \"     Foto: \" . (\$p->foto_rumah ?: '(kosong)') . \"\n\";
    }
}
"

echo ""
echo "======================================"
echo "✓ CHECK SELESAI"
echo "======================================"
echo ""
echo "Jika masih ada masalah:"
echo "1. Pastikan npm run build sudah dijalankan di local"
echo "2. Push dan pull code terbaru"
echo "3. Restart PHP-FPM: sudo systemctl restart php8.2-fpm"
echo "4. Hard refresh browser: Ctrl + Shift + R"
