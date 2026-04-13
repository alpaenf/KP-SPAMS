# Root Maintenance Archive (2026-04-14)

Folder ini berisi skrip maintenance/debug yang sebelumnya ada di root project.
Pemindahan ini dilakukan untuk merapikan root tanpa menghapus file.

## Dampak ke sistem

- Tidak mempengaruhi runtime aplikasi Laravel.
- File di folder ini tidak di-load otomatis oleh `routes`, `app`, `config`, atau `composer`.
- Verifikasi bootstrap: `php artisan --version` berhasil.

## Cara menjalankan skrip arsip

Jalankan dari root project dengan path baru, contoh:

```bash
php scripts/root-maintenance-archive-2026-04-14/check_database.php
```

## Catatan

Jika ada dokumentasi lama yang masih menunjuk ke root (mis. `php check_xxx.php`), ubah menjadi:

```bash
php scripts/root-maintenance-archive-2026-04-14/check_xxx.php
```
