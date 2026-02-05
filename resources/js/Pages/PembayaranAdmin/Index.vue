<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Daftar Pembayaran</h1>
                            <p class="text-gray-600 mt-1">Kelola dan lihat riwayat pembayaran pelanggan</p>
                        </div>
                        <div class="flex gap-3">
                            <input
                                type="month"
                                v-model="selectedBulan"
                                @change="reloadPage"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Pembayaran</p>
                                <p class="text-2xl font-bold text-gray-900">{{ pembayaranList.length }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Pemasukan</p>
                                <p class="text-2xl font-bold text-green-600">{{ formatRupiah(totalPemasukan) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Rata-rata</p>
                                <p class="text-2xl font-bold text-purple-600">{{ formatRupiah(rataRata) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Pembayaran -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Wilayah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tanggal Bayar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(item, index) in pembayaranList" :key="item.id" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.pelanggan.id_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.pelanggan.nama_pelanggan }}</div>
                                        <div class="text-sm text-gray-500">RT {{ item.pelanggan.rt }} / RW {{ item.pelanggan.rw }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ item.pelanggan.wilayah || '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatTanggal(item.tanggal_bayar) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-green-600">{{ formatRupiah(item.jumlah_bayar) }}</div>
                                        <div v-if="item.jumlah_kubik" class="text-xs text-gray-500">{{ item.jumlah_kubik }} m³</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ item.keterangan || '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button
                                            @click="viewDetail(item)"
                                            class="text-blue-600 hover:text-blue-900 font-medium"
                                        >
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="pembayaranList.length === 0">
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-2">Belum ada pembayaran untuk bulan ini</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div v-if="showDetailModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeDetailModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Detail Pembayaran</h3>
                    
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">ID Pelanggan</p>
                                <p class="text-base font-medium">{{ selectedPembayaran?.pelanggan.id_pelanggan }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Nama</p>
                                <p class="text-base font-medium">{{ selectedPembayaran?.pelanggan.nama_pelanggan }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Wilayah</p>
                                <p class="text-base font-medium">{{ selectedPembayaran?.pelanggan.wilayah || '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">RT/RW</p>
                                <p class="text-base font-medium">RT {{ selectedPembayaran?.pelanggan.rt }} / RW {{ selectedPembayaran?.pelanggan.rw }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Bayar</p>
                                <p class="text-base font-medium">{{ formatTanggal(selectedPembayaran?.tanggal_bayar) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Bulan Tagihan</p>
                                <p class="text-base font-medium">{{ formatBulan(selectedPembayaran?.bulan_bayar) }}</p>
                            </div>
                            <div v-if="selectedPembayaran?.meteran_sebelum !== null">
                                <p class="text-sm text-gray-500">Meteran Sebelum</p>
                                <p class="text-base font-medium">{{ selectedPembayaran?.meteran_sebelum }} m³</p>
                            </div>
                            <div v-if="selectedPembayaran?.meteran_sesudah !== null">
                                <p class="text-sm text-gray-500">Meteran Sesudah</p>
                                <p class="text-base font-medium">{{ selectedPembayaran?.meteran_sesudah }} m³</p>
                            </div>
                            <div v-if="selectedPembayaran?.jumlah_kubik">
                                <p class="text-sm text-gray-500">Pemakaian</p>
                                <p class="text-base font-medium">{{ selectedPembayaran?.jumlah_kubik }} m³</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Jumlah Bayar</p>
                                <p class="text-2xl font-bold text-green-600">{{ formatRupiah(selectedPembayaran?.jumlah_bayar) }}</p>
                            </div>
                            <div v-if="selectedPembayaran?.keterangan" class="col-span-2">
                                <p class="text-sm text-gray-500">Keterangan</p>
                                <p class="text-base font-medium">{{ selectedPembayaran?.keterangan }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button
                            @click="closeDetailModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    pembayaranList: Array,
    bulan: String,
});

const selectedBulan = ref(props.bulan);
const showDetailModal = ref(false);
const selectedPembayaran = ref(null);

const totalPemasukan = computed(() => {
    return props.pembayaranList.reduce((sum, p) => sum + p.jumlah_bayar, 0);
});

const rataRata = computed(() => {
    if (props.pembayaranList.length === 0) return 0;
    return totalPemasukan.value / props.pembayaranList.length;
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const formatTanggal = (tanggal) => {
    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
};

const formatBulan = (bulan) => {
    const [tahun, bulanNum] = bulan.split('-');
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${namaBulan[parseInt(bulanNum) - 1]} ${tahun}`;
};

const reloadPage = () => {
    router.visit('/pembayaran-admin', {
        method: 'get',
        data: { bulan: selectedBulan.value },
        preserveState: false,
    });
};

const viewDetail = (item) => {
    selectedPembayaran.value = item;
    showDetailModal.value = true;
};

const closeDetailModal = () => {
    showDetailModal.value = false;
    selectedPembayaran.value = null;
};
</script>
