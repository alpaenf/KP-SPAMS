# 📘 CARA MENGGUNAKAN COMPONENT InfoCicilan

Component `InfoCicilan.vue` sudah dibuat untuk menampilkan informasi cicilan pembayaran di berbagai halaman.

---

## 🎯 CARA PAKAI

### 1. Import Component
```vue
<script setup>
import InfoCicilan from '@/Components/InfoCicilan.vue';
</script>
```

### 2. Gunakan di Template
```vue
<template>
    <InfoCicilan
        :total-tagihan="tagihan.total_tagihan"
        :jumlah-terbayar="tagihan.jumlah_terbayar"
        :status-bayar="tagihan.status_bayar"
    />
</template>
```

---

## 📍 TEMPAT YANG PERLU DITAMBAHKAN

### A. TagihanBulanan/Index.vue
**Lokasi:** Di dalam modal input pembayaran, setelah section tunggakan

**Cari baris yang mirip ini:**
```vue
<!-- Section Tunggakan -->
<div v-if="selectedPelanggan?.tunggakan?.jumlah > 0" class="...">
    ...
</div>

<!-- TAMBAHKAN DI SINI -->
<InfoCicilan
    v-if="selectedPelanggan?.tagihan"
    :total-tagihan="selectedPelanggan.tagihan.total_tagihan || 0"
    :jumlah-terbayar="selectedPelanggan.tagihan.jumlah_terbayar || 0"
    :status-bayar="selectedPelanggan.tagihan.status_bayar || 'BELUM_BAYAR'"
/>
```

**Jangan lupa import:**
```vue
<script setup>
import InfoCicilan from '@/Components/InfoCicilan.vue';
// ... import lainnya
</script>
```

---

### B. Home.vue (Landing Page)
**Lokasi:** Di section hasil cek pelanggan, setelah info tagihan

**Cari baris yang mirip ini:**
```vue
<!-- Tagihan Section -->
<div v-if="pelanggan.tagihan_bulan_ini" class="...">
    ...
</div>

<!-- TAMBAHKAN DI SINI -->
<InfoCicilan
    v-if="pelanggan.tagihan_bulan_ini"
    :total-tagihan="pelanggan.jumlah_bayar || 0"
    :jumlah-terbayar="pelanggan.jumlah_terbayar || 0"
    :status-bayar="pelanggan.status_bayar"
    class="mt-4"
/>
```

**Jangan lupa import:**
```vue
<script setup>
import InfoCicilan from '@/Components/InfoCicilan.vue';
// ... import lainnya
</script>
```

---

### C. CekPelanggan.vue
**Lokasi:** Di bagian detail tagihan pelanggan

**Cari baris yang mirip ini:**
```vue
<!-- Detail Tagihan -->
<div class="bg-gray-50 rounded-xl p-6">
    ...
</div>

<!-- TAMBAHKAN DI SINI -->
<InfoCicilan
    v-if="pelanggan.tagihan"
    :total-tagihan="pelanggan.tagihan.total_tagihan || 0"
    :jumlah-terbayar="pelanggan.tagihan.jumlah_terbayar || 0"
    :status-bayar="pelanggan.tagihan.status_bayar"
    class="mt-4"
/>
```

---

## 🎨 FITUR COMPONENT

✅ **Auto-hide** - Hanya muncul jika ada cicilan (jumlah_terbayar > 0) dan belum lunas
✅ **Progress Bar** - Visual progress pembayaran
✅ **Format Rupiah** - Otomatis format angka ke Rupiah
✅ **Responsive** - Tampil bagus di mobile & desktop

---

## 📊 CONTOH TAMPILAN

```
┌─────────────────────────────────────────┐
│ 💰 Informasi Cicilan                    │
├─────────────────────────────────────────┤
│ Total Tagihan:        Rp 20.000         │
│ Sudah Dibayar:        Rp 15.000         │
│ ─────────────────────────────────────   │
│ Sisa Tagihan:         Rp 5.000          │
│                                         │
│ Progress Pembayaran          75%        │
│ ████████████████░░░░░░░░░░              │
└─────────────────────────────────────────┘
```

---

## ✅ CHECKLIST IMPLEMENTASI

- [x] Component InfoCicilan.vue dibuat
- [ ] Import di TagihanBulanan/Index.vue
- [ ] Import di Home.vue
- [ ] Import di CekPelanggan.vue
- [ ] Test tampilan di browser

---

## 🐛 TROUBLESHOOTING

**Q: Component tidak muncul?**
A: Pastikan:
1. Data `jumlah_terbayar > 0`
2. Status bukan `SUDAH_BAYAR`
3. Import component sudah benar

**Q: Data tidak update?**
A: Pastikan backend sudah return `jumlah_terbayar` dan `sisa_tagihan`
