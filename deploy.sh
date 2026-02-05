#!/bin/bash

# Script Deploy Pamsimas
# Cara penggunaan: ./deploy.sh
# Pastikan script ini dijalankan dari dalam root folder project (pamsimas)

# Konfigurasi Path (Sesuaikan jika perlu)
# Asumsi: Folder project 'pamsimas' dan 'public_html' berada di level yang sama
PUBLIC_HTML_PATH="../public_html"

echo "========================================"
echo "🚀 Memulai Deployment Pamsimas"
echo "========================================"

# 1. Git Pull
echo "📥 1. Mengambil update terbaru dari Git..."
git pull origin main

if [ $? -ne 0 ]; then
    echo "❌ Gagal melakukan git pull. Cek koneksi atau konflik."
    # Lanjut saja, mungkin user sudah pull manual
fi

# 2. Pindahkan isi folder public ke public_html
echo "📂 2. Menyalin aset dari folder public ke $PUBLIC_HTML_PATH..."
# Menyalin semua isi folder public ke public_html
cp -r public/* "$PUBLIC_HTML_PATH/"

# Salin .htaccess production jika ada
if [ -f ".htaccess-production" ]; then
    echo "📝 Menyalin .htaccess production dengan HTTPS redirect..."
    cp .htaccess-production "$PUBLIC_HTML_PATH/.htaccess"
elif [ -f "public/.htaccess" ]; then
    cp public/.htaccess "$PUBLIC_HTML_PATH/"
fi

# 3. Update index.php
echo "📝 3. Memperbaharui index.php di public_html..."
cat > "$PUBLIC_HTML_PATH/index.php" << 'EOL'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../pamsimas/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../pamsimas/bootstrap/app.php';

$app->handleRequest(Request::capture());
EOL

# 4. Fix Storage Symlink
echo "🔗 4. Memperbaiki Symbolic Link Storage..."
# Hapus link storage lama di public_html untuk memastikan bersih (terkadang cp -r mengcopy folder, bukan link)
rm -rf "$PUBLIC_HTML_PATH/storage"

# Buat symlink baru yang benar
# Dari: public_html/storage
# Ke:   ../pamsimas/storage/app/public
ln -s ../pamsimas/storage/app/public "$PUBLIC_HTML_PATH/storage"

echo "========================================"
echo "✅ Deployment Selesai!"
echo "========================================"
