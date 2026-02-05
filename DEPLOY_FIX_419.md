# 🚀 Setup Production - Fix Error 419 CSRF

Error 419 terjadi karena session/cookie tidak tersimpan dengan benar di HTTPS. Ikuti langkah berikut:

## 📋 Langkah Setup di Server Production

### 1. SSH ke Server
```bash
ssh kpsx9679@kp-spamsdammarwulan.com -p2223
cd ~/pamsimas
```

### 2. Pull Update Terbaru
```bash
git pull origin main
```

### 3. Update File .env Production

Buka file .env di server:
```bash
nano .env
```

Tambahkan/update konfigurasi berikut:

```env
# App Configuration
APP_URL=https://kp-spamsdammarwulan.com
APP_ENV=production
APP_DEBUG=false

# Session Configuration (PENTING!)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=kp-spamsdammarwulan.com
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=kp-spamsdammarwulan.com
```

**Save dengan:** `Ctrl + O` → Enter → `Ctrl + X`

### 4. Clear Cache & Optimize
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
```

### 5. Pastikan Session Table Ada
```bash
php artisan migrate
```

### 6. Set Permission Storage
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 7. Deploy
```bash
./deploy.sh
```

### 8. Test QR Scanner

Buka browser dan test:
- https://kp-spamsdammarwulan.com/qr-scanner
- Scan QR code → Seharusnya tidak ada error 419 lagi!

---

## 🔍 Troubleshooting

### Jika masih error 419:

1. **Check SSL aktif:**
```bash
curl -I https://kp-spamsdammarwulan.com | grep -i "HTTP"
```
Harus return: `HTTP/2 200` atau `HTTP/1.1 200`

2. **Check .env sudah benar:**
```bash
cat .env | grep SESSION
```

3. **Clear browser cache:**
- Buka Developer Tools (F12)
- Application → Clear Storage → Clear site data
- Refresh halaman

4. **Check session table:**
```bash
php artisan tinker
>>> DB::table('sessions')->count();
```

5. **Check middleware:**
```bash
php artisan route:list | grep qr-scanner
```

---

## ✅ Checklist Setup Production

- [ ] SSL/HTTPS sudah aktif
- [ ] .env updated dengan SESSION_SECURE_COOKIE=true
- [ ] SESSION_DOMAIN diset ke domain yang benar
- [ ] APP_URL menggunakan https://
- [ ] Cache sudah di-clear
- [ ] Migration session table sudah jalan
- [ ] Permission storage sudah benar (775)
- [ ] Deploy script sudah dijalankan

---

## 📝 Penjelasan Teknis

**Kenapa error 419?**
- CSRF token Laravel tersimpan di session
- Di HTTPS, cookie harus punya flag `Secure` 
- Shared hosting sering pakai proxy, Laravel harus trust proxy headers
- Session domain harus match dengan domain aktual

**Solusi yang diterapkan:**
1. ✅ Trust proxy headers via TrustProxies middleware
2. ✅ Set SESSION_SECURE_COOKIE=true untuk HTTPS
3. ✅ Set SESSION_DOMAIN ke domain production
4. ✅ Set SESSION_SAME_SITE=lax untuk keamanan
5. ✅ Gunakan database driver untuk session (lebih reliable di shared hosting)
