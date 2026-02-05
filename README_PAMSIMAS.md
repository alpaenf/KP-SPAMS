# PAMSIMAS - Sistem Informasi Penyediaan Air Minum dan Sanitasi Berbasis Masyarakat

Aplikasi web manajemen pelanggan PAMSIMAS Desa dengan fitur peta interaktif berbasis Laravel 12, Vue.js 3, Inertia.js, dan Filament Admin.

## 🚀 Fitur Utama

### Halaman Publik
- **Landing Page / Company Profile** - Informasi tentang PAMSIMAS dan statistik pelanggan
- **Cek Data Pelanggan** - Form pencarian pelanggan berdasarkan ID dengan tampilan data terbatas (read-only)
- **Peta Interaktif** - Peta berbasis Leaflet dan OpenStreetMap menampilkan:
  - Marker kantor PAMSIMAS
  - Marker sumber air
  - Marker lokasi pelanggan (jika ada koordinat)
  - Popup informasi dengan link ke Google Maps
  - Legenda peta

### Admin Panel (Filament)
- **Dashboard** - Overview sistem
- **CRUD Pelanggan** - Manajemen data pelanggan lengkap dengan:
  - Form input ID Pelanggan, nama, RT/RW, status aktif/nonaktif
  - Input koordinat latitude & longitude (opsional)
  - Pencarian berdasarkan nama dan ID pelanggan
  - Filter status aktif/nonaktif
  - Filter pelanggan dengan/tanpa koordinat
  - Export data

### Autentikasi & Role
- **2 Role**: Admin dan Pengelola
- Hanya role admin dan pengelola yang dapat mengakses Filament Admin
- Middleware pembatasan akses otomatis

## 📋 Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/PostgreSQL
- Laragon/XAMPP/Server lokal lainnya

## 🛠️ Instalasi & Setup

### 1. Clone/Setup Project

```bash
cd c:\laragon\www\PAMSIMAS
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Konfigurasi Environment

Buat file `.env` atau copy dari `.env.example`:

```bash
cp .env.example .env
```

Edit `.env` dan sesuaikan database:

```env
APP_NAME="PAMSIMAS"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pamsimas
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Setup Database

Buat database baru:

```sql
CREATE DATABASE pamsimas;
```

Jalankan migration dan seeder:

```bash
php artisan migrate:fresh --seed
```

Seeder akan membuat:
- 2 user (admin dan pengelola)
- 8 data pelanggan sample

### 5. Build Assets

Untuk development:

```bash
npm run dev
```

Atau untuk production:

```bash
npm run build
```

### 6. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 🔐 Default Login Credentials

### Admin
- Email: `admin@pamsimas.id`
- Password: `password`

### Pengelola
- Email: `pengelola@pamsimas.id`
- Password: `password`

## 📱 Akses Aplikasi

### Halaman Publik
- Home/Landing Page: `http://localhost:8000/`
- Cek Pelanggan: `http://localhost:8000/cek-pelanggan`
- Peta: `http://localhost:8000/peta`

### Admin Panel
- Filament Admin: `http://localhost:8000/admin`
- Login dengan credentials admin atau pengelola

## 🗂️ Struktur Proyek

```
PAMSIMAS/
├── app/
│   ├── Filament/
│   │   ├── Resources/
│   │   │   └── Pelanggans/
│   │   │       ├── PelangganResource.php (CRUD Pelanggan)
│   │   │       └── Pages/
│   │   │           └── ManagePelanggans.php
│   │   └── Providers/
│   │       └── AdminPanelProvider.php (Konfigurasi Filament)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── HomeController.php (Controller halaman publik)
│   │   └── Middleware/
│   │       ├── HandleInertiaRequests.php (Inertia middleware)
│   │       └── FilamentAccessMiddleware.php (Role middleware)
│   └── Models/
│       ├── Pelanggan.php (Model pelanggan dengan koordinat)
│       └── User.php (Model user dengan role)
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php (+ role field)
│   │   └── 2026_01_17_081051_create_pelanggan_table.php
│   └── seeders/
│       └── DatabaseSeeder.php (Seeder user & pelanggan)
│
├── resources/
│   ├── css/
│   │   └── app.css (Tailwind dengan font Poppins)
│   ├── js/
│   │   ├── app.js (Inertia setup)
│   │   ├── Layouts/
│   │   │   └── AppLayout.vue (Layout utama)
│   │   └── Pages/
│   │       ├── Home.vue (Landing page)
│   │       ├── CekPelanggan.vue (Halaman cek pelanggan)
│   │       └── Peta.vue (Halaman peta Leaflet)
│   └── views/
│       └── app.blade.php (Root template Inertia)
│
├── routes/
│   └── web.php (Routes halaman publik)
│
└── config/
    ├── app.php
    ├── database.php
    └── ... (konfigurasi Laravel)
```

## 🎨 Desain & Styling

- **Framework CSS**: Tailwind CSS 4
- **Font Global**: Poppins (dari Google Fonts)
- **Warna Utama**: 
  - Putih (#FFFFFF) - Background
  - Hijau Tua (#166534) - Primary color
  - Hijau 900 (#14532d) - Hover states

## 🗺️ Peta Interaktif

### Konfigurasi Marker

Edit `HomeController.php` method `peta()` untuk menyesuaikan koordinat:

```php
// Koordinat kantor PAMSIMAS
$kantor = [
    'name' => 'Kantor PAMSIMAS',
    'lat' => -6.200000,  // Sesuaikan dengan lokasi kantor Anda
    'lng' => 106.816666,
];

// Sumber air
$sumberAir = [
    [
        'name' => 'Sumber Air Utama',
        'lat' => -6.201000,  // Sesuaikan koordinat
        'lng' => 106.817000,
    ],
];
```

### Menambah Data Pelanggan

1. Login ke admin panel: `/admin`
2. Klik menu "Pelanggan"
3. Klik tombol "Create" 
4. Isi form data pelanggan
5. (Opsional) Isi koordinat latitude & longitude untuk menampilkan di peta
6. Klik "Save"

## 📊 Fitur CRUD Pelanggan

### Field Data Pelanggan
- **ID Pelanggan** (required, unique) - ID publik pelanggan
- **Nama Pelanggan** (required) - Nama lengkap
- **RT** (optional) - Nomor RT
- **RW** (optional) - Nomor RW  
- **Status Aktif** (boolean) - Status berlangganan
- **Latitude** (optional) - Koordinat lintang (-90 sampai 90)
- **Longitude** (optional) - Koordinat bujur (-180 sampai 180)

### Fitur Table
- Pencarian real-time nama dan ID
- Filter status aktif/nonaktif
- Filter pelanggan dengan/tanpa koordinat
- Sortir kolom
- Copy ID pelanggan
- Tooltip koordinat
- Export data
- Bulk delete

## 🔧 Development

### Menjalankan Dev Server

```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite dev server untuk hot reload
npm run dev
```

### Clear Cache

```bash
php artisan optimize:clear
```

### Membuat User Baru

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Nama User',
    'email' => 'email@example.com',
    'password' => Hash::make('password'),
    'role' => 'admin', // atau 'pengelola'
]);
```

## 📝 Sample Data Pelanggan

Seeder sudah menyediakan 8 data pelanggan sample:
- PLG001 sampai PLG005 - Pelanggan aktif dengan koordinat
- PLG006, PLG008 - Pelanggan tanpa koordinat
- PLG007 - Pelanggan aktif dengan koordinat

## 🌐 Teknologi Stack

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3
- **Bridge**: Inertia.js
- **Admin Panel**: Filament v5
- **CSS**: Tailwind CSS 4
- **Map**: Leaflet + OpenStreetMap
- **Database**: MySQL/PostgreSQL
- **Build Tool**: Vite

## 📖 Dokumentasi Package

- [Laravel 12](https://laravel.com/docs)
- [Inertia.js](https://inertiajs.com/)
- [Vue.js 3](https://vuejs.org/)
- [Filament v5](https://filamentphp.com/docs)
- [Tailwind CSS](https://tailwindcss.com/)
- [Leaflet](https://leafletjs.com/)

## 🐛 Troubleshooting

### Error: "Target class [HomeController] does not exist"
```bash
composer dump-autoload
```

### Asset tidak muncul
```bash
npm run build
php artisan optimize:clear
```

### Gagal akses Filament Admin
- Pastikan sudah login dengan user role admin atau pengelola
- Check middleware di `AdminPanelProvider.php`

### Peta tidak muncul
- Pastikan NPM package `leaflet` terinstall
- Check console browser untuk error JavaScript
- Pastikan build assets sudah dijalankan

## 📧 Support

Untuk pertanyaan atau issue, silakan buka issue di repository atau hubungi tim development.

## 📄 License

Project ini dibuat untuk keperluan PAMSIMAS Desa.

---

**Selamat menggunakan! 🎉**
