#!/bin/bash

echo "🚀 Deploying Cicilan & Tunggakan Fix..."

# Build assets
echo "📦 Building assets..."
npm run build

# Copy files ke hosting
echo "📤 Uploading to hosting..."

# Upload perubahan ke hosting
# Sesuaikan dengan metode deploy Anda:
# - jika pakai git: git push
# - jika pakai FTP: upload manual folder public/build/
# - jika pakai rsync: rsync command

echo ""
echo "📝 Files yang perlu diupload ke hosting:"
echo "   - public/build/ (semua file hasil build)"
echo "   - app/Http/Controllers/PembayaranController.php"
echo "   - app/Http/Controllers/TagihanBulananController.php"
echo "   - resources/js/Components/InfoCicilan.vue"
echo "   - resources/js/Pages/CekPelanggan.vue"
echo "   - resources/js/Pages/TagihanBulanan/Index.vue"
echo "   - fix_a01_januari.php (untuk fix data)"
echo ""
echo "⚠️  Setelah upload, jalankan di hosting:"
echo "   1. php fix_a01_januari.php (untuk fix data A01)"
echo "   2. Clear browser cache (Ctrl+Shift+R atau Ctrl+F5)"
echo ""
echo "✅ Done! Deploy files di atas ke hosting."
