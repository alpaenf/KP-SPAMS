# PANDUAN PENGELOLAAN LANDING PAGE KP-SPAMS

Dokumentasi ini menjelaskan cara menggunakan fitur pengelolaan landing page sebagai pengelola aplikasi PAMSIMAS.

## 📋 Daftar Isi

1. [Dashboard Admin](#dashboard-admin)
2. [Kelola Visi & Misi](#kelola-visi--misi)
3. [Kelola Layanan](#kelola-layanan)
4. [Kelola Berita](#kelola-berita)
5. [Kelola Galeri Foto](#kelola-galeri-foto)
6. [Kelola Sejarah](#kelola-sejarah)

---

## Dashboard Admin

Setelah login sebagai pengelola, Anda akan mendapatkan akses ke menu admin untuk mengelola konten landing page.

### Menu Akses
- **URL Dashboard**: `http://localhost/dashboard` atau `http://your-domain/dashboard`
- **Submenu Pengelolaan**: 
  - `/admin/visi-misi` - Kelola Visi & Misi
  - `/admin/berita` - Kelola Berita
  - `/admin/galeri` - Kelola Galeri
  - `/admin/sejarah` - Kelola Sejarah
  - `/admin/layanan` - Kelola Layanan

---

## Kelola Visi & Misi

**URL**: `/admin/visi-misi`

### Fitur
- Edit visi dalam satu blok teks
- Menambah/mengurangi misi (multiple items)
- Menyimpan perubahan ke database

### Cara Menggunakan

1. **Buka halaman Visi & Misi**
   - Navigasi ke `/admin/visi-misi`

2. **Edit Visi**
   - Masukkan teks visi KP-SPAMS di field "Visi KP-SPAMS"

3. **Edit Misi**
   - Setiap input field mewakili satu misi
   - Klik "+ Tambah Misi" untuk menambah misi baru
   - Klik "Hapus" untuk menghapus misi yang tidak perlu

4. **Simpan Perubahan**
   - Klik tombol "Simpan Perubahan"
   - Perubahan akan langsung tampil di landing page

### Contoh Konten
```
Visi:
"Mewujudkan akses air bersih yang berkelanjutan untuk kesehatan dan kesejahteraan masyarakat desa"

Misi:
1. Menyediakan air bersih berkualitas tinggi dan berkelanjutan
2. Meningkatkan kesadaran masyarakat tentang pentingnya air bersih
3. Membangun infrastruktur air yang modern dan terawat
4. Memberikan pelayanan terbaik kepada seluruh pelanggan
```

---

## Kelola Layanan

**URL**: `/admin/layanan`

### Fitur
- Menambah layanan baru (Create)
- Mengedit layanan yang ada (Edit)
- Menghapus layanan (Delete)
- Upload gambar untuk setiap layanan
- Pengurutan otomatis layanan
- Field: Judul, Deskripsi, Icon (optional), Foto (optional)

### Cara Menggunakan

#### **Menambah Layanan Baru**

1. Klik tombol "+ Tambah Layanan" di halaman list
2. Isi form:
   - **Nama Layanan**: Judul layanan (contoh: "Penyediaan Air Bersih 24 Jam")
   - **Deskripsi**: Penjelasan detail tentang layanan
   - **Icon** (opsional): Nama icon dari Heroicons atau emoji
   - **Gambar** (opsional): Upload foto/gambar layanan
3. Klik "Simpan Layanan"

#### **Mengedit Layanan**

1. Klik tombol "Edit" pada layanan yang ingin diubah
2. Ubah informasi sesuai kebutuhan
3. Klik "Simpan Perubahan"

#### **Menghapus Layanan**

1. Klik tombol "Hapus" pada layanan
2. Konfirmasi penghapusan
3. Layanan akan dihapus dari database

### Contoh Layanan
```
1. Penyediaan Air Bersih 24 Jam
   Kami menyediakan air bersih berkualitas tinggi selama 24 jam setiap hari...

2. Sistem Pembayaran Fleksibel
   Pembayaran dapat dilakukan melalui berbagai metode dengan sistem yang transparan...

3. Penanganan Gangguan Cepat
   Tim teknis kami siap menangani gangguan 24 jam dengan respons yang cepat...
```

---

## Kelola Berita

**URL**: `/admin/berita`

### Fitur
- Menambah berita baru (Create)
- Mengedit berita (Edit)
- Menghapus berita (Delete)
- Upload thumbnail gambar
- Kategorisasi berita
- Status publikasi (Publish/Draft)
- Pagination untuk berita list

### Cara Menggunakan

#### **Menambah Berita Baru**

1. Klik tombol "+ Tambah Berita"
2. Isi form:
   - **Judul Berita**: Judul yang menarik dan informatif
   - **Kategori** (opsional): Contoh: "Pengumuman", "Berita", "Acara"
   - **Konten Berita**: Isi lengkap berita (dapat panjang)
   - **Thumbnail** (opsional): Gambar sampul berita (max 2MB)
   - **Publikasikan Berita**: Centang untuk membuat berita tampil di halaman
3. Klik "Simpan Berita"

#### **Mengedit Berita**

1. Klik tombol "Edit" pada berita yang akan diubah
2. Update informasi
3. Klik "Simpan Perubahan"

#### **Menghapus Berita**

1. Klik tombol "Hapus" pada berita
2. Konfirmasi penghapusan
3. Berita akan terhapus dari sistem

### Status Publikasi
- **Dipublikasikan** (Publish): Berita tampil di landing page
- **Belum Dipublikasikan** (Draft): Berita disimpan tapi belum tampil

### Tips
- Tulis judul yang menarik dan mencerminkan isi berita
- Gunakan thumbnail berkualitas tinggi
- Update berita secara berkala untuk engagement lebih baik
- Gunakan kategori yang konsisten untuk kemudahan pencarian

---

## Kelola Galeri Foto

**URL**: `/admin/galeri`

### Fitur
- Upload foto dalam batch
- Tambah caption dan kategori untuk setiap foto
- Mengurutkan foto (drag & drop)
- Menghapus foto
- Pagination untuk galeri
- Maksimal ukuran file: 5MB per foto

### Cara Menggunakan

#### **Menambah Foto**

1. Klik tombol "+ Tambah Foto"
2. Modal dialog akan muncul
3. Isi form:
   - **Pilih Foto**: Klik untuk memilih file gambar dari komputer
   - **Caption** (opsional): Deskripsi singkat tentang foto
   - **Kategori** (opsional): Contoh: "Aktivitas", "Acara", "Infrastruktur"
4. Klik tombol "Upload"
5. Foto akan ditambahkan ke galeri

#### **Mengedit Foto**

1. Klik tombol "Edit" pada foto
2. Ubah caption atau kategori
3. Klik "Simpan"

#### **Menghapus Foto**

1. Klik tombol "Hapus" pada foto
2. Konfirmasi penghapusan
3. Foto akan terhapus dari galeri

#### **Mengurutkan Foto**

1. Drag foto ke posisi yang diinginkan (jika fitur drag-drop tersedia)
2. Atau gunakan tombol order untuk menentukan urutan tampilan

### Tips
- Gunakan foto berkualitas tinggi (minimal 1920x1080 px)
- Berikan caption yang deskriptif
- Kategorikan foto dengan konsisten untuk navigasi mudah
- Update galeri secara berkala dengan foto terbaru kegiatan

---

## Kelola Sejarah

**URL**: `/admin/sejarah`

### Fitur
- Edit konten sejarah dalam format teks panjang
- Upload satu gambar sejarah
- Menyimpan perubahan dengan edit history
- Tampilan responsif dengan gambar di sisi kanan

### Cara Menggunakan

1. **Buka halaman Sejarah**
   - Navigasi ke `/admin/sejarah`

2. **Edit Konten Sejarah**
   - Masukkan atau ubah teks sejarah di textarea
   - Gunakan paragraf yang jelas dan mudah dibaca
   - Jelaskan latar belakang terbentuknya aplikasi dan sistem PAMSIMAS

3. **Upload Foto**
   - Klik "Pilih Foto" untuk upload gambar sejarah
   - Foto akan ditampilkan di sisi kanan konten
   - Maksimal ukuran: 5MB

4. **Simpan Perubahan**
   - Klik "Simpan Perubahan"
   - Konten sejarah akan terupdate di landing page

### Contoh Konten Sejarah
```
KP-SPAMS "Dammar Wulan" adalah sistem penyediaan air minum komunal 
yang didirikan untuk memenuhi kebutuhan air bersih bagi masyarakat 
Desa Ciwuni. Dengan memanfaatkan teknologi modern dan manajemen yang baik, 
kami berkomitmen untuk menyediakan air berkualitas tinggi dengan tarif 
yang terjangkau untuk semua kalangan masyarakat...

[Lanjutkan dengan sejarah pembentukan, visi, dan perkembangan organisasi]
```

---

## 🎨 Tips Desain & Konten

### Visi & Misi
- Visi harus singkat, jelas, dan inspiratif (1-2 kalimat)
- Misi harus spesifik dan dapat diukur (3-5 misi)
- Gunakan bahasa yang mudah dipahami oleh masyarakat awam

### Layanan
- Jelaskan manfaat konkret dari setiap layanan
- Gunakan icon/gambar yang relevan dan menarik
- Urutan layanan sesuai dengan prioritas/kepentingan

### Berita
- Update minimal 2-3 kali per bulan
- Gunakan judul yang compelling dan SEO-friendly
- Include tanggal publikasi dan kategori untuk organisiasi

### Galeri
- Dokumentasi visual dari kegiatan dan infrastruktur
- Foto harus berkualitas tinggi dan profesional
- Tambahkan deskripsi yang informatif pada setiap foto

### Sejarah
- Ceritakan perjalanan organisasi dengan cara yang personal
- Include pencapaian dan milestone penting
- Highlight komitmen terhadap masyarakat dan keberlanjutan

---

## 🔒 Otorisasi & Keamanan

- Hanya user dengan role **"pengelola"** yang bisa mengakses menu admin
- Setiap perubahan dicatat dengan user yang melakukan (created_by, updated_by)
- Data foto tersimpan di folder `/storage` dan dapat diakses publik

---

## 📞 Support & Troubleshooting

### Masalah Umum

**Q: Foto tidak tampil setelah upload**
- A: Periksa ukuran file (max 5MB)
- A: Periksa format file (JPG, PNG, GIF)
- A: Jalankan `php artisan storage:link` jika folder storage belum ter-link

**Q: Perubahan tidak tersimpan**
- A: Pastikan koneksi internet stabil
- A: Coba refresh halaman dan ulangi perubahan
- A: Periksa console browser untuk error message (F12)

**Q: Berita tidak tampil di landing page**
- A: Pastikan status "Dipublikasikan" sudah dicentang
- A: Periksa apakah berita sudah disimpan (cek di list)
- A: Clear browser cache (Ctrl+Shift+Delete)

---

## 📝 Catatan Tambahan

- Database akan menyimpan data dengan timestamp (created_at, updated_at)
- Foto tersimpan di `/storage/app/public/` dengan subfolder per kategori
- Untuk menampilkan foto di production, jalankan: `php artisan storage:link`
- Backup database secara berkala untuk keamanan data

---

**Version**: 1.0  
**Last Updated**: Februari 1, 2026  
**Author**: KKN UNSOED Desa Ciwuni
