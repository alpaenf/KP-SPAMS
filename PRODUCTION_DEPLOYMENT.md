# 🚀 PANDUAN DEPLOYMENT PRODUCTION - KEAMANAN

Quick reference untuk deploy aplikasi PAMSIMAS dengan aman ke production server.

---

## 1️⃣ PERSIAPAN SERVER

### Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### Install Dependencies
```bash
# PHP 8.2+ dengan extensions
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-gd php8.2-zip

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js & NPM
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 2️⃣ SETUP APLIKASI

### Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/your-repo/pamsimas.git
cd pamsimas
```

### Install Dependencies
```bash
# PHP dependencies (production only)
composer install --no-dev --optimize-autoloader

# Node dependencies
npm install

# Build production assets
npm run build
```

### File Permissions
```bash
sudo chown -R www-data:www-data /var/www/pamsimas
sudo chmod -R 755 /var/www/pamsimas
sudo chmod -R 775 storage bootstrap/cache
sudo chmod 644 .env
```

---

## 3️⃣ KONFIGURASI ENVIRONMENT

### Copy dan Edit .env
```bash
cp .env.example .env
nano .env
```

### Environment Variables (CRITICAL)
```env
# Application
APP_NAME="PAMSIMAS"
APP_ENV=production
APP_KEY=                      # Generate dengan: php artisan key:generate
APP_DEBUG=false               # ❗ PENTING: false untuk production
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pamsimas
DB_USERNAME=pamsimas_user     # ❗ JANGAN gunakan root
DB_PASSWORD=YourStrong@Pass123 # ❗ Password kuat

# Session Security
SESSION_DRIVER=database
SESSION_LIFETIME=240          # 4 hours (untuk PWA)
SESSION_ENCRYPT=true          # ❗ Enable encryption
SESSION_SECURE_COOKIE=true    # ❗ HTTPS only
SESSION_SAME_SITE=lax

# Password Hashing
BCRYPT_ROUNDS=12

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (untuk notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Generate Application Key
```bash
php artisan key:generate
```

---

## 4️⃣ DATABASE SETUP

### Create Database & User
```bash
mysql -u root -p
```

```sql
-- Create database
CREATE DATABASE pamsimas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create dedicated user
CREATE USER 'pamsimas_user'@'localhost' IDENTIFIED BY 'YourStrong@Pass123';

-- Grant permissions (minimal required)
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, INDEX, ALTER, LOCK TABLES 
ON pamsimas.* TO 'pamsimas_user'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

### Run Migrations
```bash
php artisan migrate --force
```

### Seed Data (jika perlu)
```bash
php artisan db:seed --force
```

---

## 5️⃣ OPTIMIZATION

### Cache Configuration
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Storage Link
```bash
php artisan storage:link
```

---

## 6️⃣ WEB SERVER SETUP

### Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/pamsimas
```

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;  # Force HTTPS
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/pamsimas/public;

    # SSL Certificate (dari Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

### Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/pamsimas /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 7️⃣ SSL CERTIFICATE (Let's Encrypt)

### Install Certbot
```bash
sudo apt install certbot python3-certbot-nginx
```

### Generate Certificate
```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Auto-Renewal (Crontab)
```bash
sudo crontab -e
```

Add:
```
0 0 * * * certbot renew --quiet
```

---

## 8️⃣ FIREWALL

### UFW Setup
```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
sudo ufw status
```

---

## 9️⃣ CRON JOBS

### Laravel Scheduler
```bash
sudo crontab -e -u www-data
```

Add:
```
* * * * * cd /var/www/pamsimas && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔟 BACKUP AUTOMATION

### Database Backup Script
```bash
sudo nano /usr/local/bin/backup-pamsimas.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pamsimas"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="pamsimas"
DB_USER="pamsimas_user"
DB_PASS="YourStrong@Pass123"

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Storage backup
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/pamsimas/storage/app/public

# Keep only last 7 days
find $BACKUP_DIR -type f -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-pamsimas.sh
```

### Schedule Daily Backup
```bash
sudo crontab -e
```

Add:
```
0 2 * * * /usr/local/bin/backup-pamsimas.sh >> /var/log/pamsimas-backup.log 2>&1
```

---

## 1️⃣1️⃣ MONITORING

### Log Rotation
```bash
sudo nano /etc/logrotate.d/pamsimas
```

```
/var/www/pamsimas/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    missingok
    notifempty
    create 0644 www-data www-data
    sharedscripts
    postrotate
        php /var/www/pamsimas/artisan cache:clear > /dev/null 2>&1
    endscript
}
```

### Monitor Failed Logins
```bash
# Check Laravel logs
tail -f /var/www/pamsimas/storage/logs/laravel.log | grep "authentication failed"

# Check rate limit violations
tail -f /var/www/pamsimas/storage/logs/laravel.log | grep "Too Many Attempts"
```

---

## ✅ POST-DEPLOYMENT CHECKLIST

Setelah deployment, verify:

- [ ] Website accessible via HTTPS
- [ ] HTTP redirect to HTTPS otomatis
- [ ] SSL certificate valid & trusted
- [ ] Login functionality works
- [ ] QR scanner works
- [ ] File upload works
- [ ] PWA install works di mobile
- [ ] Map functionality works
- [ ] Rate limiting active (test dengan spam requests)
- [ ] Database backups running
- [ ] Logs rotating properly
- [ ] Email notifications working (jika ada)
- [ ] `APP_DEBUG=false` verified
- [ ] Security headers present (check dengan curl atau browser dev tools)

### Test Security Headers
```bash
curl -I https://yourdomain.com
```

Should see:
```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
Content-Security-Policy: ...
```

---

## 🆘 TROUBLESHOOTING

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/pamsimas
sudo chmod -R 755 /var/www/pamsimas
sudo chmod -R 775 storage bootstrap/cache
```

### Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Session Issues
```bash
# Clear sessions table
php artisan session:clear

# Or truncate sessions table
mysql -u pamsimas_user -p pamsimas -e "TRUNCATE TABLE sessions;"
```

### Storage Link Missing
```bash
php artisan storage:link
```

---

## 📞 SUPPORT

Jika ada masalah deployment atau security concerns:

1. Check `/var/www/pamsimas/storage/logs/laravel.log`
2. Check Nginx error log: `/var/log/nginx/error.log`
3. Check system log: `sudo journalctl -xe`
4. Review [SECURITY_AUDIT_REPORT.md](./SECURITY_AUDIT_REPORT.md)

---

**Dibuat**: 7 Februari 2026  
**Update Terakhir**: 7 Februari 2026
