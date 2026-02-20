<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Riwayat Pembayaran</h1>
                            <p class="text-sm sm:text-base text-gray-600">Daftar semua transaksi pembayaran pelanggan</p>
                        </div>
                        
                        <div class="flex gap-2">
                            <Link
                                href="/cek-pelanggan"
                                class="inline-flex items-center px-4 py-2 bg-blue-800 text-white rounded-lg font-medium hover:bg-blue-900 transition text-sm"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali
                            </Link>
                        </div>
                    </div>
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
                                <p class="text-sm font-medium text-gray-500">Rata-rata</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ Number(stats.rataRata).toLocaleString('id-ID') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pelanggan</label>
                            <input
                                type="text"
                                v-model="searchPembayaran"
                                placeholder="Cari berdasarkan ID atau nama pelanggan..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <div v-if="$page.props.auth.user.role !== 'penarik'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Wilayah</label>
                            <select
                                v-model="wilayahFilterPembayaran"
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
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <input
                                type="month"
                                v-model="bulanFilterPembayaran"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                            <select
                                v-model="keteranganFilterPembayaran"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            >
                                <option value="all">Semua</option>
                                <option value="LUNAS">Lunas</option>
                                <option value="TUNGGAKAN">Tunggakan</option>
                                <option value="CICILAN">Cicilan</option>
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
                                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Pemakaian</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Keterangan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Jumlah Bayar</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="filteredPembayaranList.length === 0">
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2">Tidak ada data pembayaran ditemukan</p>
                                    </td>
                                </tr>
                                <tr v-for="item in filteredPembayaranList" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatTanggalPembayaran(item.tanggal_bayar) }}</div>
                                        <div class="text-xs text-gray-500">{{ formatBulan(item.bulan_bayar) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.id_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ item.nama_pelanggan }}</div>
                                        <div v-if="(item.kategori || 'umum').toLowerCase().trim() === 'sosial'" class="text-xs text-purple-600 font-medium">Kategori Sosial</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ item.wilayah || 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">{{ item.jumlah_kubik ? item.jumlah_kubik + ' m³' : '-' }}</div>
                                        <div v-if="item.abunemen" class="text-xs text-blue-600">+ Abunemen</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span v-if="item.keterangan === 'LUNAS'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Lunas
                                        </span>
                                        <span v-else-if="item.keterangan === 'TUNGGAKAN'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd" />
                                            </svg>
                                            Tunggakan
                                        </span>
                                        <span v-else-if="item.keterangan === 'CICILAN'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                            Cicilan
                                        </span>
                                        <span v-else class="text-xs text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm font-semibold text-gray-900">Rp {{ Number(item.jumlah_bayar).toLocaleString('id-ID') }}</div>
                                        <div v-if="item.tunggakan > 0" class="text-xs text-orange-600">Tunggakan: Rp {{ Number(item.tunggakan).toLocaleString('id-ID') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button
                                            @click="openDetailModal(item)"
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition"
                                            title="Lihat Detail"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Detail Pembayaran -->
        <div v-if="showDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <!-- Header -->
                <div class="bg-blue-800 px-6 py-4 rounded-t-lg">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">Detail Pembayaran</h3>
                        <button
                            @click="closeDetailModal"
                            class="text-white hover:text-gray-200 transition"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6 space-y-4" v-if="selectedPembayaran">
                    <!-- Info Pelanggan -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Informasi Pelanggan
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500">ID Pelanggan</p>
                                <p class="font-semibold text-gray-900">{{ selectedPembayaran.id_pelanggan }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Nama</p>
                                <p class="font-semibold text-gray-900">{{ selectedPembayaran.nama_pelanggan }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Wilayah</p>
                                <p class="font-semibold text-gray-900">{{ selectedPembayaran.wilayah }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Kategori</p>
                                <p class="font-semibold text-gray-900 capitalize">{{ selectedPembayaran.kategori }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info Pembayaran -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Informasi Pembayaran
                        </h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500">Tanggal Bayar</p>
                                <p class="font-semibold text-gray-900">{{ formatTanggalPembayaran(selectedPembayaran.tanggal_bayar) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Bulan Bayar</p>
                                <p class="font-semibold text-gray-900">{{ formatBulan(selectedPembayaran.bulan_bayar) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Keterangan</p>
                                <p class="font-semibold" :class="{
                                    'text-green-600': selectedPembayaran.keterangan === 'LUNAS',
                                    'text-red-600': selectedPembayaran.keterangan === 'TUNGGAKAN',
                                    'text-yellow-600': selectedPembayaran.keterangan === 'CICILAN'
                                }">
                                    {{ selectedPembayaran.keterangan || '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info Meteran -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Informasi Meteran
                        </h4>
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500">Meteran Sebelum</p>
                                <p class="font-semibold text-gray-900">{{ selectedPembayaran.meteran_sebelum || '-' }} m³</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Meteran Sesudah</p>
                                <p class="font-semibold text-gray-900">{{ selectedPembayaran.meteran_sesudah || '-' }} m³</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Pemakaian</p>
                                <p class="font-semibold text-blue-600">{{ selectedPembayaran.jumlah_kubik || '0' }} m³</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rincian Biaya -->
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Rincian Biaya
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Abunemen</span>
                                <span class="font-medium text-gray-900">{{ selectedPembayaran.abunemen ? 'Ya' : 'Tidak' }}</span>
                            </div>
                            <div v-if="selectedPembayaran.tunggakan > 0" class="flex justify-between">
                                <span class="text-gray-600">Tunggakan</span>
                                <span class="font-medium text-orange-600">Rp {{ Number(selectedPembayaran.tunggakan).toLocaleString('id-ID') }}</span>
                            </div>
                            <div class="border-t border-blue-200 pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-800">Total Bayar</span>
                                    <span class="font-bold text-lg text-blue-600">Rp {{ Number(selectedPembayaran.jumlah_bayar).toLocaleString('id-ID') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-end">
                    <button
                        @click="closeDetailModal"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition font-medium"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    pembayaranList: {
        type: Array,
        default: () => []
    }
});

const searchPembayaran = ref('');
const wilayahFilterPembayaran = ref('all');
const bulanFilterPembayaran = ref('');
const keteranganFilterPembayaran = ref('all');
const showDetailModal = ref(false);
const selectedPembayaran = ref(null);

// Filter pembayaran
const filteredPembayaranList = computed(() => {
    let filtered = props.pembayaranList;
    
    // Filter by search
    if (searchPembayaran.value) {
        const search = searchPembayaran.value.toLowerCase();
        filtered = filtered.filter(item => 
            item.id_pelanggan.toLowerCase().includes(search) ||
            item.nama_pelanggan.toLowerCase().includes(search)
        );
    }
    
    // Filter by wilayah
    if (wilayahFilterPembayaran.value !== 'all') {
        filtered = filtered.filter(item => item.wilayah === wilayahFilterPembayaran.value);
    }
    
    // Filter by bulan
    if (bulanFilterPembayaran.value) {
        filtered = filtered.filter(item => item.bulan_bayar === bulanFilterPembayaran.value);
    }
    
    // Filter by keterangan
    if (keteranganFilterPembayaran.value !== 'all') {
        filtered = filtered.filter(item => item.keterangan === keteranganFilterPembayaran.value);
    }
    
    return filtered;
});

// Statistics
const stats = computed(() => {
    const list = filteredPembayaranList.value;
    const totalPemasukan = list.reduce((sum, item) => sum + Number(item.jumlah_bayar), 0);
    const totalPembayaran = list.length;
    const rataRata = totalPembayaran > 0 ? totalPemasukan / totalPembayaran : 0;
    
    return {
        totalPembayaran,
        totalPemasukan,
        rataRata
    };
});

// Format tanggal pembayaran
const formatTanggalPembayaran = (tanggal) => {
    if (!tanggal) return '-';
    const date = new Date(tanggal);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
};

// Format bulan bayar
const formatBulan = (bulan) => {
    if (!bulan) return '-';
    const [year, month] = bulan.split('-');
    const date = new Date(year, parseInt(month) - 1);
    const options = { year: 'numeric', month: 'long' };
    return date.toLocaleDateString('id-ID', options);
};

// Modal functions
const openDetailModal = (pembayaran) => {
    selectedPembayaran.value = pembayaran;
    showDetailModal.value = true;
};

const closeDetailModal = () => {
    showDetailModal.value = false;
    selectedPembayaran.value = null;
};
</script>
