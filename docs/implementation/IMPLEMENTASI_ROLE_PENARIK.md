# Implementasi Role Penarik Per Wilayah

## Yang Sudah Dikerjakan

### 1. ✅ Migration & Database
- **File**: `database/migrations/2026_02_08_053220_add_role_and_wilayah_to_users_table.php`
- Tambah kolom `role` (enum: 'admin', 'penarik')
- Tambah kolom `wilayah` (enum: 5 wilayah - NULL untuk admin)

### 2. ✅ User Model
- **File**: `app/Models/User.php`
- Tambah `wilayah` ke fillable
- Helper methods:
  - `isPenarik()` - cek role penarik
  - `hasWilayah()` - cek punya wilayah
  - `getWilayah()` - ambil wilayah user

### 3. ✅ Pelanggan Model - Scope
- **File**: `app/Models/Pelanggan.php`
- **Scope `forUser()`**: Auto-filter berdasarkan role & wilayah
  - Admin: lihat semua data
  - Penarik: hanya wilayahnya
- **Scope `byWilayah()`**: Filter manual per wilayah

### 4. ✅ Middleware
- **File**: `app/Http/Middleware/ApplyWilayahScope.php`
- Share ke semua view: `currentUserRole`, `currentUserWilayah`, `isAdmin`, `isPenarik`
- Registered di `bootstrap/app.php`

### 5. ✅ HomeController (Partial)
- Method `cekPelanggan()`: sudah apply `forUser()` scope
- Method `dashboard()`: sudah apply `forUser()` scope

## Yang Perlu Dilakukan Selanjutnya

### 1. Update Controllers Lainnya

Tambahkan `->forUser()` di semua query Pelanggan:

#### A. **TagihanBulananController** (PRIORITY HIGH)
```php
// Line 21
$pelangganList = Pelanggan::forUser()
    ->where('status_aktif', true)
    ->orderBy('id_pelanggan')
    ->get();

// Line 163  
$pelanggans = Pelanggan::forUser()
    ->where('status_aktif', true)
    ->get();
```

#### B. **LaporanController** (PRIORITY HIGH)
```php
// Line 87, 207, 296 - tambahkan forUser()
$pelangganQuery = Pelanggan::forUser()
    ->where('status_aktif', true);
```

#### C. **PelangganController** (PRIORITY MEDIUM)
```php
// Line 19, 55 - tambahkan forUser()
$pelanggan = Pelanggan::forUser()
    ->where('id_pelanggan', $idPelanggan)
    ->first();
```

#### D. **HomeController - Methods Lainnya**
Update methods:
- `downloadPdf()` - line 18
- `pembayaran()` - line 99
- `index()` - line 161, 179
- `konfirmasiPembayaran()` - line 298
- `pelangganBelumBayar()` - line 490
- `getPeta()` - line 683

Ganti semua `Pelanggan::where()` menjadi `Pelanggan::forUser()->where()`

### 2. Update UI/Views

#### A. **AppLayout.vue** - Hide/Show Menu
```vue
<!-- Hanya admin yang bisa akses -->
<Link v-if="$page.props.auth.user.role === 'admin'" href="/dashboard">
    Dashboard Admin
</Link>

<!-- Semua bisa akses tapi data ter-filter -->
<Link href="/cek-pelanggan">
    Data Pelanggan
</Link>
```

#### B. **CekPelanggan.vue** - Hide Actions untuk Penarik
```vue
<!-- Hanya admin bisa tambah/edit/delete -->
<div v-if="$page.props.auth.user.role === 'admin'">
    <button>Tambah Pelanggan</button>
    <button>Edit</button>
    <button>Hapus</button>
</div>

<!-- Penarik hanya bisa input meteran & lihat riwayat -->
<div v-if="$page.props.auth.user.role === 'penarik'">
    <button>Input Meteran</button>
    <button>Riwayat Pembayaran</button>
</div>
```

#### C. **Dashboard** - Show Wilayah Info
```vue
<!-- Untuk penarik, tampilkan badge wilayahnya -->
<div v-if="$page.props.auth.user.role === 'penarik'" class="mb-4">
    <div class="bg-blue-100 border border-blue-300 rounded-lg p-3">
        <p class="font-semibold">Wilayah Anda: {{ $page.props.auth.user.wilayah }}</p>
    </div>
</div>
```

### 3. Buat Seeder untuk Testing

**File**: `database/seeders/PenarikSeeder.php`

```php
// Buat 5 akun penarik, 1 untuk setiap wilayah
$wilayahList = [
    'Dawuhan',
    'Kubangsari Kulon',
    'Kubangsari Wetan',
    'Sokarame',
    'Tiparjaya'
];

foreach ($wilayahList as $wilayah) {
    User::create([
        'name' => 'Penarik ' . $wilayah,
        'email' => strtolower(str_replace(' ', '', $wilayah)) . '@pamsimas.com',
        'password' => Hash::make('password'),
        'role' => 'penarik',
        'wilayah' => $wilayah,
    ]);
}
```

### 4. Run Migration & Seeder

```bash
php artisan migrate
php artisan db:seed --class=PenarikSeeder
```

### 5. Test Login Penarik

**Akun Test**:
- Email: `dawuhan@pamsimas.com`
- Password: `password`
- Wilayah: Dawuhan

**Yang Harus Muncul**:
- Hanya pelanggan dengan wilayah = "Dawuhan"
- Menu terbatas (tidak ada tambah/edit/delete pelanggan)
- Dashboard hanya show stats wilayah Dawuhan

## Cara Menggunakan Scope forUser()

Di semua controller, ganti:
```php
// SEBELUM
$pelanggan = Pelanggan::where('status_aktif', true)->get();

// SESUDAH
$pelanggan = Pelanggan::forUser()->where('status_aktif', true)->get();
```

Scope akan otomatis:
- Return semua data untuk admin
- Return hanya data wilayah untuk penarik

## Keamanan

✅ Filter di backend (model scope) - tidak bisa di-bypass dari frontend
✅ Middleware share data ke view - UI bisa adapt
✅ Validation di controller level juga perlu ditambahkan

## Next Steps

1. Update semua controller (list di atas)
2. Update UI components untuk hide/show berdasarkan role
3. Buat seeder dan test
4. Deploy ke production & test dengan real user
5. Training untuk penarik cara login & pakai sistem
