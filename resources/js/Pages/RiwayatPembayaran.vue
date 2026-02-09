<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Riwayat Pembayaran</h1>
                    <p class="text-gray-600 mt-1">Kelola dan pantau semua transaksi pembayaran pelanggan</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.totalPembayaran }}</p>
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
                                <p class="text-2xl font-bold text-gray-900">Rp {{ Number(stats.totalPemasukan).toLocaleString('id-ID') }}</p>
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
                                <p class="text-sm font-medium text-gray-500">Rata-rata Bayar</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ Number(stats.rataRata).toLocaleString('id-ID') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pelanggan</label>
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Cari berdasarkan ID atau nama pelanggan..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <!-- Filter Bulan -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <input
                                type="month"
                                v-model="filterBulan"
                                @change="changeBulan"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <!-- Filter Wilayah -->
                        <div v-if="$page.props.auth.user.role !== 'penarik'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Wilayah</label>
                            <select
                                v-model="wilayahFilter"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            >
                                <option value="all">Semua Wilayah</option>
                                <option value="Dawuhan">Dawuhan</option>
                                <option value="Kubangsari Kulon">Kubangsari Kulon</option>
                                <option value="Kubangsari Wetan">Kubangsari Wetan</option>
                                <option value="Sokarame">Sokarame</option>
                                <option value="Tiparjaya">Tiparjaya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Wilayah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">RT/RW</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Bulan Bayar</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Pemakaian</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Jumlah Bayar</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="filteredPembayaran.length === 0">
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2">Tidak ada data pembayaran ditemukan</p>
                                    </td>
                                </tr>
                                <tr v-for="item in paginatedData" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatTanggal(item.tanggal_bayar) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.id_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ item.nama_pelanggan }}</div>
                                        <div v-if="item.kategori === 'sosial'" class="text-xs text-purple-600 font-medium">Kategori Sosial</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ item.wilayah || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ item.rt ? `RT ${item.rt}` : '-' }}
                                            {{ item.rt && item.rw ? '/' : '' }}
                                            {{ item.rw ? `RW ${item.rw}` : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatBulan(item.bulan_bayar) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">{{ item.jumlah_kubik ? item.jumlah_kubik + ' m³' : '-' }}</div>
                                        <div v-if="item.abunemen" class="text-xs text-blue-600">+ Abunemen</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-semibold text-gray-900">Rp {{ Number(item.jumlah_bayar).toLocaleString('id-ID') }}</div>
                                        <div v-if="item.tunggakan > 0" class="text-xs text-orange-600">Tunggakan: Rp {{ Number(item.tunggakan).toLocaleString('id-ID') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button
                                            @click="showDetail(item)"
                                            class="text-blue-600 hover:text-blue-800 transition"
                                            title="Lihat Detail"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div v-if="filteredPembayaran.length > perPage" class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <button
                                @click="currentPage--"
                                :disabled="currentPage === 1"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                            >
                                Previous
                            </button>
                            <button
                                @click="currentPage++"
                                :disabled="currentPage === totalPages"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                            >
                                Next
                            </button>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Menampilkan
                                    <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
                                    sampai
                                    <span class="font-medium">{{ Math.min(currentPage * perPage, filteredPembayaran.length) }}</span>
                                    dari
                                    <span class="font-medium">{{ filteredPembayaran.length }}</span>
                                    hasil
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <button
                                        @click="currentPage--"
                                        :disabled="currentPage === 1"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                    >
                                        <span class="sr-only">Previous</span>
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <button
                                        v-for="page in visiblePages"
                                        :key="page"
                                        @click="currentPage = page"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            currentPage === page
                                                ? 'z-10 bg-blue-800 border-blue-800 text-white'
                                                : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
                                        ]"
                                    >
                                        {{ page }}
                                    </button>
                                    <button
                                        @click="currentPage++"
                                        :disabled="currentPage === totalPages"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                    >
                                        <span class="sr-only">Next</span>
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
                    <div class="bg-blue-800 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
                        <h3 class="text-xl font-bold">Detail Pembayaran</h3>
                        <button @click="closeModal" class="text-white hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6" v-if="selectedItem">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Informasi Pelanggan -->
                            <div class="col-span-2 border-b pb-4 mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Informasi Pelanggan</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-sm text-gray-600">ID Pelanggan</p>
                                        <p class="font-medium">{{ selectedItem.id_pelanggan }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Nama</p>
                                        <p class="font-medium">{{ selectedItem.nama_pelanggan }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Wilayah</p>
                                        <p class="font-medium">{{ selectedItem.wilayah }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">RT/RW</p>
                                        <p class="font-medium">RT {{ selectedItem.rt }} / RW {{ selectedItem.rw }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Pembayaran -->
                            <div class="col-span-2 border-b pb-4 mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Detail Transaksi</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-sm text-gray-600">Bulan Bayar</p>
                                        <p class="font-medium">{{ formatBulan(selectedItem.bulan_bayar) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tanggal Bayar</p>
                                        <p class="font-medium">{{ formatTanggal(selectedItem.tanggal_bayar) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Meteran Sebelum</p>
                                        <p class="font-medium">{{ selectedItem.meteran_sebelum || '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Meteran Sesudah</p>
                                        <p class="font-medium">{{ selectedItem.meteran_sesudah || '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Pemakaian</p>
                                        <p class="font-medium">{{ selectedItem.jumlah_kubik ? selectedItem.jumlah_kubik + ' m³' : '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Abunemen</p>
                                        <p class="font-medium">{{ selectedItem.abunemen ? 'Ya (Rp 3.000)' : 'Tidak' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Tunggakan</p>
                                        <p class="font-medium">{{ selectedItem.tunggakan > 0 ? 'Rp ' + Number(selectedItem.tunggakan).toLocaleString('id-ID') : 'Tidak ada' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Bayar</p>
                                        <p class="text-xl font-bold text-blue-800">Rp {{ Number(selectedItem.jumlah_bayar).toLocaleString('id-ID') }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Tambahan -->
                            <div class="col-span-2">
                                <h4 class="font-semibold text-gray-900 mb-2">Informasi Tambahan</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-sm text-gray-600">Metode Bayar</p>
                                        <p class="font-medium">{{ selectedItem.metode_bayar || 'Tunai' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Keterangan</p>
                                        <p class="font-medium">{{ selectedItem.keterangan || '-' }}</p>
                                    </div>
                                    <div v-if="selectedItem.catatan" class="col-span-2">
                                        <p class="text-sm text-gray-600">Catatan</p>
                                        <p class="font-medium">{{ selectedItem.catatan }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex gap-3 justify-end">
                            <a
                                :href="`/pembayaran/${selectedItem.id}/download-pdf`"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download PDF
                            </a>
                            <button
                                @click="closeModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    pembayaranList: {
        type: Array,
        default: () => []
    },
    bulan: {
        type: String,
        default: ''
    },
    stats: {
        type: Object,
        default: () => ({
            totalPembayaran: 0,
            totalPemasukan: 0,
            rataRata: 0
        })
    }
});

const searchQuery = ref('');
const filterBulan = ref(props.bulan);
const wilayahFilter = ref('all');
const showModal = ref(false);
const selectedItem = ref(null);
const currentPage = ref(1);
const perPage = 20;

const filteredPembayaran = computed(() => {
    let result = props.pembayaranList;
    
    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(p =>
            p.id_pelanggan.toLowerCase().includes(query) ||
            p.nama_pelanggan.toLowerCase().includes(query)
        );
    }
    
    // Filter by wilayah
    if (wilayahFilter.value !== 'all') {
        result = result.filter(p => p.wilayah === wilayahFilter.value);
    }
    
    return result;
});

const totalPages = computed(() => Math.ceil(filteredPembayaran.value.length / perPage));

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    const end = start + perPage;
    return filteredPembayaran.value.slice(start, end);
});

const visiblePages = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages.value, start + maxVisible - 1);
    
    if (end - start < maxVisible - 1) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    
    return pages;
});

const changeBulan = () => {
    router.get('/riwayat-pembayaran', { bulan: filterBulan.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const showDetail = (item) => {
    selectedItem.value = item;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedItem.value = null;
};

const formatBulan = (bulan) => {
    const [year, month] = bulan.split('-');
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${namaBulan[parseInt(month) - 1]} ${year}`;
};

const formatTanggal = (tanggal) => {
    const date = new Date(tanggal);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>
