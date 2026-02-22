<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 lg:mb-8">
                    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-4">
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Tagihan & Pembayaran Bulanan</h1>
                            <p class="text-gray-600 mt-1 text-sm lg:text-base">Kelola meteran, tagihan, dan pembayaran pelanggan</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input
                                type="month"
                                v-model="selectedBulan"
                                @change="reloadPage"
                                class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent text-sm"
                            />
                            <button
                                @click="showGenerateModal = true"
                                class="inline-flex items-center justify-center px-4 lg:px-6 py-2 lg:py-3 bg-blue-800 text-white rounded-lg font-semibold hover:bg-blue-900 transition text-sm lg:text-base whitespace-nowrap"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="hidden sm:inline">Generate Tagihan Bulanan</span>
                                <span class="sm:hidden">Generate</span>
                            </button>
                        </div>
                    </div>
                    <!-- Search Box -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Cari nama pelanggan..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-800 focus:border-transparent sm:text-sm"
                        />
                    </div>
                </div>
                
                <!-- Tab Navigation -->
                <div class="mb-6 border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            @click="activeTab = 'tagihan'"
                            :class="[
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'tagihan'
                                    ? 'border-blue-800 text-blue-800'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            Input Tagihan
                        </button>
                        <button
                            @click="activeTab = 'pembayaran'"
                            :class="[
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                                activeTab === 'pembayaran'
                                    ? 'border-blue-800 text-blue-800'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                            ]"
                        >
                            Daftar Pembayaran
                        </button>
                    </nav>
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Pelanggan</p>
                                <p class="text-2xl font-bold text-gray-900">{{ pelangganList.length }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Sudah Input Meteran</p>
                                <p class="text-2xl font-bold text-gray-900">{{ sudahInputCount }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Belum Input Meteran</p>
                                <p class="text-2xl font-bold text-gray-900">{{ belumInputCount }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Sudah Bayar</p>
                                <p class="text-2xl font-bold text-gray-900">{{ sudahBayarCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tagihan Tab -->
                <div v-show="activeTab === 'tagihan'">
                    <!-- Tabel Input Meteran -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Wilayah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Meteran Sebelum (m³)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Meteran Sesudah (m³)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Pemakaian (m³)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tunggakan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Total Tagihan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in filteredPelangganList" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.id_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900">{{ item.nama_pelanggan }}</div>
                                            <span v-if="item.tunggakan && item.tunggakan > 0" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800" title="Memiliki Tunggakan">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                !
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ item.wilayah || '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="item.kategori === 'sosial'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Sosial
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Umum
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ item.tagihan?.meteran_sebelum ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ item.tagihan?.meteran_sesudah ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.tagihan?.pemakaian_kubik ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="item.tunggakan && item.tunggakan > 0" class="text-sm font-bold text-red-600">
                                            {{ formatRupiah(item.tunggakan) }}
                                        </div>
                                        <div v-else class="text-sm text-gray-400">-</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="item.tagihan" class="space-y-1">
                                            <div class="text-sm font-bold text-blue-600">
                                                {{ formatRupiah(item.tagihan.total_tagihan) }}
                                            </div>
                                            <!-- Tampilkan info cicilan jika ada pembayaran sebagian -->
                                            <div v-if="item.tagihan.jumlah_terbayar > 0 && item.tagihan.status_bayar === 'BELUM_BAYAR'" class="text-xs text-green-600">
                                                Terbayar: {{ formatRupiah(item.tagihan.jumlah_terbayar) }}
                                            </div>
                                        </div>
                                        <div v-else class="text-sm text-gray-400">Belum ada</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <span v-if="item.tagihan?.status_konfirmasi === 'pending'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                                Pending Konfirmasi
                                            </span>
                                            <span v-else-if="item.tagihan?.status_bayar === 'SUDAH_BAYAR'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Sudah Bayar
                                            </span>
                                            <span v-else-if="item.tunggakan && item.tunggakan > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd" />
                                                </svg>
                                                Nunggak
                                            </span>
                                            <span v-else-if="item.tagihan && item.tagihan.status_bayar === 'BELUM_BAYAR' && item.tagihan.status_konfirmasi === 'none'" class="inline-flex flex-col items-start gap-1">
                                                <!-- Badge Utama: Belum Bayar atau Cicilan -->
                                                <span v-if="item.tagihan.jumlah_terbayar > 0" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    Cicilan {{ Math.round((item.tagihan.jumlah_terbayar / item.tagihan.total_tagihan) * 100) }}%
                                                </span>
                                                <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    Belum Bayar
                                                </span>
                                            </span>
                                            <span v-else-if="!item.tagihan" class="text-xs text-gray-400">-</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button
                                            @click="openInputModal(item)"
                                            class="text-blue-600 hover:text-blue-800 transition font-medium"
                                            title="Input Meteran"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <!-- Jika pending konfirmasi, tampilkan tombol approve/reject -->
                                        <div v-if="item.tagihan?.status_konfirmasi === 'pending'" class="flex gap-2">
                                            <button
                                                @click="openKonfirmasiModal(item)"
                                                class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-xs font-medium rounded-md hover:bg-orange-700 transition"
                                                title="Lihat Bukti & Approve"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Verifikasi
                                            </button>
                                        </div>
                                        <!-- Jika belum bayar dan tidak ada pending, tampilkan tombol bayar -->
                                        <button
                                            v-else-if="item.tagihan && item.tagihan.status_bayar === 'BELUM_BAYAR' && item.tagihan.status_konfirmasi === 'none'"
                                            @click="openPembayaranModal(item)"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 transition"
                                            title="Input Pembayaran"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Bayar
                                        </button>
                                        <span v-else-if="item.tagihan?.status_bayar === 'SUDAH_BAYAR'" class="text-xs text-green-600 font-medium">
                                            ✓ Lunas
                                        </span>
                                        <span v-else class="text-xs text-gray-400">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Generate Tagihan Bulanan -->
        <div v-if="showGenerateModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showGenerateModal = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Generate Tagihan Bulanan</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <input
                                type="month"
                                v-model="generateForm.bulan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tarif per m³ (Rp)</label>
                            <input
                                type="number"
                                v-model="generateForm.tarif_per_kubik"
                                step="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="generateForm.ada_abunemen"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2 text-sm text-gray-700">Ada Biaya Abunemen</span>
                            </label>
                        </div>
                        
                        <div v-if="generateForm.ada_abunemen">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Abunemen (Rp)</label>
                            <input
                                type="number"
                                v-model="generateForm.biaya_abunemen"
                                step="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <div class="border-t pt-4">
                            <label class="flex items-start">
                                <input
                                    type="checkbox"
                                    v-model="generateForm.masukkan_tunggakan"
                                    class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2">
                                    <span class="text-sm font-medium text-gray-700">Masukkan Tunggakan ke Tagihan Bulan Ini</span>
                                    <span class="block text-xs text-gray-500 mt-1">Tunggakan dari bulan sebelumnya akan otomatis ditambahkan ke total tagihan</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-3">
                        <button
                            @click="generateTagihan"
                            :disabled="isGenerating"
                            class="flex-1 px-4 py-2 bg-blue-800 text-white rounded-lg font-medium hover:bg-blue-900 transition disabled:opacity-50"
                        >
                            {{ isGenerating ? 'Generating...' : 'Generate' }}
                        </button>
                        <button
                            @click="showGenerateModal = false"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
                </div>

                <!-- Pembayaran Tab -->
                <div v-show="activeTab === 'pembayaran'">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Pembayaran</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ pembayaranStats.totalCount }}</p>
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
                                    <p class="text-2xl font-bold text-gray-900">Rp {{ formatRupiah(pembayaranStats.totalAmount) }}</p>
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
                                    <p class="text-sm font-medium text-gray-500">Rata-rata Pembayaran</p>
                                    <p class="text-2xl font-bold text-gray-900">Rp {{ formatRupiah(pembayaranStats.averageAmount) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pembayaran Table -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-blue-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID Pelanggan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama Pelanggan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Wilayah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Bulan Bayar</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">M. Sebelum</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">M. Sesudah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Pemakaian</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Abunemen</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Jumlah Bayar</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Keterangan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Metode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Catatan</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="pembayaran in filteredPembayaranList" :key="pembayaran.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ formatDate(pembayaran.tanggal_bayar) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ pembayaran.pelanggan.id_pelanggan }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ pembayaran.pelanggan.nama_pelanggan }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ pembayaran.pelanggan.wilayah || '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ formatBulan(pembayaran.bulan_bayar) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ pembayaran.meteran_sebelum || '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ pembayaran.meteran_sesudah || '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-600">{{ pembayaran.jumlah_kubik ? pembayaran.jumlah_kubik + ' m³' : '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span v-if="pembayaran.abunemen" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Ya
                                            </span>
                                            <span v-else class="text-gray-400 text-sm">-</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">Rp {{ formatRupiah(pembayaran.jumlah_bayar) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="pembayaran.keterangan === 'Nunggak'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ pembayaran.keterangan }}
                                            </span>
                                            <span v-else-if="pembayaran.keterangan === 'Cicilan'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                {{ pembayaran.keterangan }}
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ pembayaran.keterangan || 'Lunas' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ pembayaran.metode_bayar }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600 max-w-xs truncate">{{ pembayaran.catatan || '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <button
                                                @click="showPembayaranDetail(pembayaran)"
                                                class="text-blue-600 hover:text-blue-900 transition"
                                                title="Lihat Detail"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="!filteredPembayaranList || filteredPembayaranList.length === 0">
                                        <td colspan="14" class="px-6 py-12 text-center text-gray-500">
                                            <div v-if="searchQuery">
                                                Tidak ada pembayaran yang sesuai dengan pencarian "{{ searchQuery }}"
                                            </div>
                                            <div v-else>
                                                Belum ada pembayaran di bulan ini
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

        <!-- Modal Input Meteran -->
        <div v-if="showInputModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeInputModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        Input Meteran - {{ selectedPelanggan?.nama_pelanggan }}
                    </h3>
                    
                    <div v-if="selectedPelanggan?.kategori === 'sosial'" class="mb-4 bg-purple-50 border border-purple-200 rounded-lg p-3">
                        <p class="text-sm text-purple-700">
                            <strong>Pelanggan Kategori Sosial:</strong> Tidak dikenakan biaya (gratis)
                        </p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan Tagihan</label>
                            <input
                                type="month"
                                :value="selectedBulan"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                            />
                            <p class="text-xs text-gray-500 mt-1">Bulan yang sedang dipilih di halaman utama</p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meteran Sebelum (m³)</label>
                                <input
                                    type="number"
                                    v-model="inputForm.meteran_sebelum"
                                    step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    @input="hitungPemakaian"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meteran Sesudah (m³)</label>
                                <input
                                    type="number"
                                    v-model="inputForm.meteran_sesudah"
                                    step="0.01"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    @input="hitungPemakaian"
                                />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pemakaian (m³)</label>
                                <input
                                    type="number"
                                    :value="pemakaianKubik"
                                    readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                />
                            </div>
                            
                            <div v-if="selectedPelanggan?.kategori !== 'sosial'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tarif per m³ (Rp)</label>
                                <input
                                    type="number"
                                    v-model="inputForm.tarif_per_kubik"
                                    step="100"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    @input="hitungPemakaian"
                                />
                            </div>
                        </div>
                        
                        <div v-if="selectedPelanggan?.kategori !== 'sosial'">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="inputForm.ada_abunemen"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    @change="hitungPemakaian"
                                />
                                <span class="ml-2 text-sm text-gray-700">Ada Biaya Abunemen</span>
                            </label>
                        </div>
                        
                        <div v-if="inputForm.ada_abunemen && selectedPelanggan?.kategori !== 'sosial'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Abunemen (Rp)</label>
                            <input
                                type="number"
                                v-model="inputForm.biaya_abunemen"
                                step="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                @input="hitungPemakaian"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Tagihan (Rp)</label>
                            <input
                                type="text"
                                :value="formatRupiah(totalTagihan)"
                                readonly
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-blue-50 font-bold text-blue-600"
                            />
                        </div>
                    </div>
                    
                    <!-- Info Cicilan (jika ada) -->
                    <InfoCicilan
                        v-if="selectedPelanggan?.tagihan"
                        :total-tagihan="selectedPelanggan.tagihan.total_tagihan || 0"
                        :jumlah-terbayar="selectedPelanggan.tagihan.jumlah_terbayar || 0"
                        :status-bayar="selectedPelanggan.tagihan.status_bayar || 'BELUM_BAYAR'"
                        class="mt-4"
                    />
                    
                    <div class="mt-6 flex gap-3">
                        <button
                            @click="submitMeteran"
                            :disabled="isSubmitting"
                            class="flex-1 px-4 py-2 bg-blue-800 text-white rounded-lg font-medium hover:bg-blue-900 transition disabled:opacity-50"
                        >
                            {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                        <button
                            @click="closeInputModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Input Pembayaran -->
        <div v-if="showPembayaranModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closePembayaranModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Input Pembayaran</h3>
                    
                    <div v-if="selectedPelanggan" class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="text-sm">
                            <p><strong>ID:</strong> {{ selectedPelanggan.id_pelanggan }}</p>
                            <p><strong>Nama:</strong> {{ selectedPelanggan.nama_pelanggan }}</p>
                            <p><strong>Bulan:</strong> {{ formatBulan(selectedBulan) }}</p>
                            <p class="mt-2 text-lg font-bold text-blue-600">
                                Total Tagihan: {{ formatRupiah(selectedPelanggan.tagihan?.total_tagihan || 0) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Info Cicilan (jika ada) -->
                    <InfoCicilan
                        v-if="selectedPelanggan?.tagihan"
                        :total-tagihan="selectedPelanggan.tagihan.total_tagihan || 0"
                        :jumlah-terbayar="selectedPelanggan.tagihan.jumlah_terbayar || 0"
                        :status-bayar="selectedPelanggan.tagihan.status_bayar || 'BELUM_BAYAR'"
                        class="mb-4"
                    />
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Bayar</label>
                            <input
                                type="date"
                                v-model="pembayaranForm.tanggal_bayar"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800"
                                required
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meteran Sebelum (m³)</label>
                            <input
                                type="number"
                                v-model="pembayaranForm.meteran_sebelum"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800"
                                @input="hitungPemakaianPembayaran"
                            />
                            <p class="text-xs text-gray-500 mt-1">Angka meteran bulan lalu</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meteran Sesudah (m³)</label>
                            <input
                                type="number"
                                v-model="pembayaranForm.meteran_sesudah"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800"
                                @input="hitungPemakaianPembayaran"
                            />
                            <p class="text-xs text-gray-500 mt-1">Angka meteran bulan ini</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pemakaian (m³)</label>
                            <input
                                type="number"
                                v-model="pembayaranForm.jumlah_kubik"
                                step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 bg-gray-50"
                                readonly
                            />
                            <p v-if="selectedPelanggan?.kategori === 'sosial'" class="text-xs text-purple-600 mt-1 font-medium">Tarif: Gratis (Kategori Sosial)</p>
                            <p v-else class="text-xs text-gray-500 mt-1">Tarif: Rp 2.000 / m³</p>
                        </div>
                        
                        <div>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    v-model="pembayaranForm.abunemen"
                                    @change="hitungPemakaianPembayaran"
                                    class="w-5 h-5 text-blue-800 border-gray-300 rounded focus:ring-2 focus:ring-blue-800"
                                />
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Abunemen</span>
                                    <p class="text-xs text-gray-500">Biaya tambahan: Rp 3.000</p>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Tunggakan Section -->
                        <div v-if="listTunggakan.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <input
                                    type="checkbox"
                                    v-model="pembayaranForm.bayar_tunggakan"
                                    @change="toggleBayarTunggakan"
                                    class="mt-1 mr-3 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                />
                                <div class="flex-1">
                                    <label class="text-sm font-medium text-red-800 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        ⚠️ Bayar Tunggakan ({{ listTunggakan.length }} bulan)
                                    </label>
                                    <p class="text-xs text-red-700 mt-1 font-medium">
                                        Total tunggakan: Rp {{ Number(listTunggakan.reduce((sum, t) => sum + t.sisa_tagihan, 0)).toLocaleString('id-ID') }}
                                    </p>
                                    <div class="text-xs text-red-600 mt-2 space-y-1">
                                        <div v-for="item in listTunggakan" :key="item.id" class="flex justify-between py-1 border-b border-red-200 last:border-0">
                                            <span class="text-red-700">{{ formatBulan(item.bulan) }}</span>
                                            <span class="font-bold text-red-900">Rp {{ Number(item.sisa_tagihan).toLocaleString('id-ID') }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Input Jumlah Bayar Tunggakan -->
                                    <div v-if="pembayaranForm.bayar_tunggakan" class="mt-3">
                                        <label class="block text-sm font-medium text-red-800 mb-1">
                                            Jumlah Bayar Tunggakan (Rp)
                                            <span class="text-xs font-normal text-red-600">- Bisa bayar sebagian/cicil</span>
                                        </label>
                                        <input
                                            type="number"
                                            v-model="pembayaranForm.jumlah_bayar_tunggakan"
                                            @input="hitungTotalBayar"
                                            step="1000"
                                            min="0"
                                            :max="listTunggakan.reduce((sum, t) => sum + t.sisa_tagihan, 0)"
                                            class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-white"
                                            placeholder="Isi jumlah bayar tunggakan (bisa cicil)"
                                        />
                                        <p class="text-xs text-red-600 mt-1">
                                            💡 Isi sesuai kemampuan (misal: Rp 5.000 dari total Rp {{ Number(listTunggakan.reduce((sum, t) => sum + t.sisa_tagihan, 0)).toLocaleString('id-ID') }})
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ pembayaranForm.keterangan === 'CICILAN' ? 'Jumlah Cicilan' : 'Jumlah Bayar' }} (Rp)
                                <span v-if="pembayaranForm.keterangan === 'CICILAN'" class="text-blue-600 text-xs">- Bisa diedit</span>
                            </label>
                            <input
                                type="number"
                                v-model="pembayaranForm.jumlah_bayar"
                                :class="[
                                    'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800',
                                    (pembayaranForm.keterangan === 'CICILAN' || pembayaranForm.bayar_tunggakan) ? 'bg-white' : 'bg-gray-50'
                                ]"
                                step="1000"
                                min="0"
                                required
                                :readonly="pembayaranForm.keterangan !== 'CICILAN' && !pembayaranForm.bayar_tunggakan"
                            />
                            <p v-if="pembayaranForm.keterangan === 'CICILAN'" class="text-xs text-blue-600 mt-1">💡 Untuk cicilan, isi jumlah berapa saja (misal: Rp 1.000, Rp 5.000, dll)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan <span class="text-red-500">*</span></label>
                            <select
                                v-model="pembayaranForm.keterangan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800"
                                required
                            >
                                <option value="LUNAS">Lunas - Bayar penuh tagihan bulan ini</option>
                                <option value="TUNGGAKAN">Tunggakan - Belum bayar, masuk bulan depan</option>
                                <option value="CICILAN">Cicilan - Bayar sebagian</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Lunas = Bayar penuh | Tunggakan = Belum bayar | Cicilan = Bayar sebagian
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                            <textarea
                                v-model="pembayaranForm.catatan"
                                rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800"
                                placeholder="Catatan pembayaran..."
                            ></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-3">
                        <button
                            @click="submitPembayaran"
                            :disabled="isSubmittingPembayaran"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition disabled:opacity-50"
                        >
                            {{ isSubmittingPembayaran ? 'Menyimpan...' : 'Simpan Pembayaran' }}
                        </button>
                        <button
                            @click="closePembayaranModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Verifikasi Konfirmasi Pembayaran -->
        <div v-if="showKonfirmasiModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeKonfirmasiModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Verifikasi Konfirmasi Pembayaran</h3>
                    
                    <div v-if="selectedKonfirmasi" class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-sm"><strong>ID Pelanggan:</strong> {{ selectedKonfirmasi.id_pelanggan }}</p>
                            <p class="text-sm"><strong>Nama:</strong> {{ selectedKonfirmasi.nama_pelanggan }}</p>
                            <p class="text-sm"><strong>Total Tagihan:</strong> {{ formatRupiah(selectedKonfirmasi.tagihan?.total_tagihan || 0) }}</p>
                            <p class="text-sm"><strong>Waktu Konfirmasi:</strong> {{ formatTanggal(selectedKonfirmasi.tagihan?.konfirmasi_at) }}</p>
                        </div>
                        
                        <div v-if="selectedKonfirmasi.tagihan?.bukti_transfer">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Transfer:</label>
                            <img 
                                :src="`/storage/${selectedKonfirmasi.tagihan.bukti_transfer}`" 
                                alt="Bukti Transfer" 
                                class="w-full max-h-96 object-contain border rounded-lg"
                            />
                        </div>
                        <div v-else>
                            <p class="text-sm text-gray-500 italic">Tidak ada bukti transfer yang diupload</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea
                                v-model="catatanAdmin"
                                rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800"
                                placeholder="Tambahkan catatan jika perlu..."
                            ></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-3">
                        <button
                            @click="approveKonfirmasi"
                            :disabled="isProcessing"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition disabled:opacity-50"
                        >
                            {{ isProcessing ? 'Memproses...' : '✓ Approve' }}
                        </button>
                        <button
                            @click="rejectKonfirmasi"
                            :disabled="isProcessing"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition disabled:opacity-50"
                        >
                            {{ isProcessing ? 'Memproses...' : '✗ Tolak' }}
                        </button>
                        <button
                            @click="closeKonfirmasiModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Pembayaran Detail -->
        <div v-if="showPembayaranDetailModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closePembayaranDetail">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Detail Pembayaran</h3>
                    
                    <div v-if="selectedPembayaran" class="space-y-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Informasi Pelanggan</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-medium">ID Pelanggan:</span> {{ selectedPembayaran.pelanggan.id_pelanggan }}</p>
                                <p><span class="font-medium">Nama:</span> {{ selectedPembayaran.pelanggan.nama_pelanggan }}</p>
                                <p><span class="font-medium">Wilayah:</span> {{ selectedPembayaran.pelanggan.wilayah || '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Tanggal Pembayaran</label>
                                <p class="text-base text-gray-900">{{ formatDate(selectedPembayaran.tanggal_bayar) }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Bulan Bayar</label>
                                <p class="text-base text-gray-900">{{ formatBulan(selectedPembayaran.bulan_bayar) }}</p>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 bg-gray-50 p-3 rounded-lg">
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Meteran Sebelum</label>
                                    <p class="text-sm font-medium text-gray-900">{{ selectedPembayaran.meteran_sebelum || '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Meteran Sesudah</label>
                                    <p class="text-sm font-medium text-gray-900">{{ selectedPembayaran.meteran_sesudah || '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500">Pemakaian</label>
                                    <p class="text-sm font-medium text-gray-900">{{ selectedPembayaran.jumlah_kubik ? selectedPembayaran.jumlah_kubik + ' m³' : '-' }}</p>
                                </div>
                            </div>
                            
                            <div v-if="selectedPembayaran.abunemen">
                                <label class="text-sm font-medium text-gray-500">Abunemen</label>
                                <p class="text-base text-gray-900">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Ya (Rp 3.000)
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Jumlah Bayar</label>
                                <p class="text-2xl font-bold text-green-600">Rp {{ formatRupiah(selectedPembayaran.jumlah_bayar) }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Metode Pembayaran</label>
                                <p class="text-base text-gray-900">{{ selectedPembayaran.metode_bayar }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500">Keterangan</label>
                                <p class="text-base text-gray-900">{{ selectedPembayaran.keterangan || 'Lunas' }}</p>
                            </div>
                            
                            <div v-if="selectedPembayaran.catatan">
                                <label class="text-sm font-medium text-gray-500">Catatan</label>
                                <p class="text-base text-gray-900">{{ selectedPembayaran.catatan }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button
                            @click="closePembayaranDetail"
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
import InfoCicilan from '@/Components/InfoCicilan.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    pelangganList: Array,
    pembayaranList: Array,
    bulan: String,
});

const selectedBulan = ref(props.bulan);
const searchQuery = ref('');
const showGenerateModal = ref(false);
const showInputModal = ref(false);
const showPembayaranModal = ref(false);
const showKonfirmasiModal = ref(false);
const showPembayaranDetailModal = ref(false);
const selectedPelanggan = ref(null);
const selectedKonfirmasi = ref(null);
const selectedPembayaran = ref(null);
const catatanAdmin = ref('');
const isGenerating = ref(false);
const isSubmitting = ref(false);
const isSubmittingPembayaran = ref(false);
const isProcessing = ref(false);
const listTunggakan = ref([]);

const generateForm = ref({
    bulan: props.bulan,
    tarif_per_kubik: 2000,
    ada_abunemen: true,
    biaya_abunemen: 3000,
    masukkan_tunggakan: false,
});

const inputForm = ref({
    meteran_sebelum: null,
    meteran_sesudah: null,
    tarif_per_kubik: 2000,
    ada_abunemen: true,
    biaya_abunemen: 3000,
});

const pembayaranForm = ref({
    tanggal_bayar: new Date().toISOString().split('T')[0],
    meteran_sebelum: null,
    meteran_sesudah: null,
    jumlah_kubik: 0,
    abunemen: false,
    jumlah_bayar: 0,
    keterangan: 'LUNAS', // Default LUNAS untuk pembayaran normal
    catatan: '',
    bayar_tunggakan: false,
    jumlah_bayar_tunggakan: 0, // Jumlah bayar tunggakan (bisa cicil)
});

const pemakaianKubik = computed(() => {
    const sebelum = parseFloat(inputForm.value.meteran_sebelum) || 0;
    const sesudah = parseFloat(inputForm.value.meteran_sesudah) || 0;
    return sesudah - sebelum;
});

const totalTagihan = computed(() => {
    if (selectedPelanggan.value?.kategori === 'sosial') return 0;
    
    const pemakaian = pemakaianKubik.value;
    const tarif = parseFloat(inputForm.value.tarif_per_kubik) || 0;
    const biaya_pemakaian = pemakaian * tarif;
    const biaya_abunemen = inputForm.value.ada_abunemen ? (parseFloat(inputForm.value.biaya_abunemen) || 0) : 0;
    
    return biaya_pemakaian + biaya_abunemen;
});

const sudahInputCount = computed(() => {
    return props.pelangganList.filter(p => p.tagihan && p.tagihan.meteran_sesudah !== null).length;
});

const belumInputCount = computed(() => {
    return props.pelangganList.length - sudahInputCount.value;
});

const sudahBayarCount = computed(() => {
    return props.pelangganList.filter(p => p.tagihan && p.tagihan.status_bayar === 'SUDAH_BAYAR').length;
});

const activeTab = ref('tagihan');

const filteredPelangganList = computed(() => {
    if (!searchQuery.value) return props.pelangganList;
    const query = searchQuery.value.toLowerCase();
    return props.pelangganList.filter(p => 
        p.nama_pelanggan.toLowerCase().includes(query) ||
        p.id_pelanggan.toLowerCase().includes(query)
    );
});

const filteredPembayaranList = computed(() => {
    if (!searchQuery.value) return props.pembayaranList || [];
    const query = searchQuery.value.toLowerCase();
    return (props.pembayaranList || []).filter(p => 
        p.pelanggan.nama_pelanggan.toLowerCase().includes(query) ||
        p.pelanggan.id_pelanggan.toLowerCase().includes(query)
    );
});

const pembayaranStats = computed(() => {
    const list = filteredPembayaranList.value;
    const total = list.reduce((sum, p) => sum + parseFloat(p.jumlah_bayar || 0), 0);
    return {
        totalCount: list.length,
        totalAmount: total,
        averageAmount: list.length > 0 ? total / list.length : 0
    };
});

const formatRupiah = (value) => {
    return parseInt(value).toLocaleString('id-ID');
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
};

const formatBulan = (bulan) => {
    const [tahun, bulanNum] = bulan.split('-');
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${namaBulan[parseInt(bulanNum) - 1]} ${tahun}`;
};

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-';
    return new Date(tanggal).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const reloadPage = () => {
    router.visit('/tagihan-bulanan', {
        data: { bulan: selectedBulan.value },
        preserveState: false,
    });
};

const generateTagihan = async () => {
    if (generateForm.value.masukkan_tunggakan) {
        if (!confirm('Yakin ingin memasukkan tunggakan ke tagihan bulan ini? Pelanggan yang punya tunggakan akan mendapat tagihan lebih besar.')) {
            return;
        }
    } else {
        if (!confirm('Generate tagihan untuk semua pelanggan aktif?')) return;
    }
    
    isGenerating.value = true;
    
    try {
        const response = await axios.post('/tagihan-bulanan/generate-bulk', generateForm.value);
        alert(response.data.message);
        showGenerateModal.value = false;
        reloadPage();
    } catch (error) {
        alert('Gagal generate tagihan: ' + (error.response?.data?.message || error.message));
    } finally {
        isGenerating.value = false;
    }
};

const fetchMeteranDataForInput = async (pelanggan) => {
    // 1. Jika sudah ada tagihan bulan ini, gunakan data tersebut
    if (pelanggan.tagihan && pelanggan.tagihan.meteran_sesudah !== null) {
        inputForm.value.meteran_sebelum = pelanggan.tagihan.meteran_sebelum;
        inputForm.value.meteran_sesudah = pelanggan.tagihan.meteran_sesudah;
        inputForm.value.tarif_per_kubik = pelanggan.tagihan.tarif_per_kubik;
        inputForm.value.ada_abunemen = pelanggan.tagihan.ada_abunemen;
        inputForm.value.biaya_abunemen = pelanggan.tagihan.biaya_abunemen;
        return;
    }
    
    // 2. Jika belum ada tagihan bulan ini, otomatis isi meteran_sebelum dari bulan lalu
    const prevMonth = getPreviousMonth(selectedBulan.value);
    if (prevMonth) {
        let foundPreviousReading = false;
        let previousReading = 0;
        
        try {
            // Cek tagihan bulan sebelumnya
            const prevTagihanRes = await axios.get(`/api/tagihan-bulanan/${pelanggan.id}/${prevMonth}`);
            if (prevTagihanRes.data && prevTagihanRes.data.tagihan && prevTagihanRes.data.tagihan.meteran_sesudah > 0) {
                previousReading = prevTagihanRes.data.tagihan.meteran_sesudah;
                foundPreviousReading = true;
            }
        } catch (error) {
            console.log('Tidak ada tagihan bulan sebelumnya');
        }
        
        if (foundPreviousReading) {
            inputForm.value.meteran_sebelum = previousReading;
            inputForm.value.meteran_sesudah = null;
            return;
        }
    }
    
    // 3. Fallback: Set default values
    inputForm.value.meteran_sebelum = null;
    inputForm.value.meteran_sesudah = null;
};

const openInputModal = async (pelanggan) => {
    selectedPelanggan.value = pelanggan;
    
    // Inisialisasi form dengan data default
    inputForm.value = {
        meteran_sebelum: null,
        meteran_sesudah: null,
        tarif_per_kubik: 2000,
        ada_abunemen: true,
        biaya_abunemen: 3000,
    };
    
    showInputModal.value = true;
    
    // Ambil data meteran (akan otomatis mengisi dari bulan lalu jika belum ada data bulan ini)
    await fetchMeteranDataForInput(pelanggan);
};

const closeInputModal = () => {
    showInputModal.value = false;
    selectedPelanggan.value = null;
};

const getPreviousMonth = (monthStr) => {
    if (!monthStr) return null;
    let [year, month] = monthStr.split('-').map(Number);
    
    // Kurangi 1 bulan
    month = month - 1;
    
    // Jika bulan menjadi 0 (sebelum Januari), maka mundur 1 tahun dan set bulan ke Desember (12)
    if (month === 0) {
        year -= 1;
        month = 12;
    }
    
    return `${year}-${String(month).padStart(2, '0')}`;
};

const fetchMeteranDataForPembayaran = async (pelanggan) => {
    // 1. Jika sudah ada tagihan bulan ini dan meteran sudah diisi, gunakan data tersebut
    if (pelanggan.tagihan && pelanggan.tagihan.meteran_sesudah !== null) {
        pembayaranForm.value.meteran_sebelum = pelanggan.tagihan.meteran_sebelum;
        pembayaranForm.value.meteran_sesudah = pelanggan.tagihan.meteran_sesudah;
        pembayaranForm.value.jumlah_kubik = pelanggan.tagihan.pemakaian_kubik;
        pembayaranForm.value.abunemen = pelanggan.tagihan.ada_abunemen;
        return;
    }
    
    // 2. Jika belum ada tagihan meteran bulan ini, cari data bulan sebelumnya
    // meteran_sebelum (bulan ini) = meteran_sesudah (bulan lalu)
    const prevMonth = getPreviousMonth(selectedBulan.value);
    if (prevMonth) {
        try {
            // Cek tagihan bulan sebelumnya
            const prevTagihanRes = await axios.get(`/api/tagihan-bulanan/${pelanggan.id}/${prevMonth}`);
            if (prevTagihanRes.data && prevTagihanRes.data.tagihan && prevTagihanRes.data.tagihan.meteran_sesudah > 0) {
                pembayaranForm.value.meteran_sebelum = prevTagihanRes.data.tagihan.meteran_sesudah;
                pembayaranForm.value.meteran_sesudah = null;
                pembayaranForm.value.jumlah_kubik = 0;
                return;
            }
        } catch (error) {
            console.log('Tidak ada tagihan bulan sebelumnya');
        }
    }
    
    // 3. Fallback: Jika tidak ada data bulan sebelumnya, set ke 0
    pembayaranForm.value.meteran_sebelum = 0;
    pembayaranForm.value.meteran_sesudah = null;
    pembayaranForm.value.jumlah_kubik = 0;
};

const openPembayaranModal = async (pelanggan) => {
    selectedPelanggan.value = pelanggan;
    const tagihanBulanIni = pelanggan.tagihan?.total_tagihan || 0;
    
    // Inisialisasi form dengan data dasar
    pembayaranForm.value = {
        tanggal_bayar: new Date().toISOString().split('T')[0],
        meteran_sebelum: null,
        meteran_sesudah: null,
        jumlah_kubik: 0,
        abunemen: pelanggan.tagihan?.ada_abunemen || false,
        jumlah_bayar: tagihanBulanIni,
        keterangan: 'LUNAS', // Default LUNAS untuk pembayaran normal
        catatan: '',
        bayar_tunggakan: false,
        jumlah_bayar_tunggakan: 0,
    };
    
    showPembayaranModal.value = true;
    
    // Fetch list tunggakan untuk pelanggan ini
    try {
        const tunggakanRes = await axios.get(`/api/tagihan-bulanan/${pelanggan.id}/tunggakan`);
        if (tunggakanRes.data && tunggakanRes.data.tunggakan) {
            listTunggakan.value = tunggakanRes.data.tunggakan;
        } else {
            listTunggakan.value = [];
        }
    } catch (error) {
        console.log('Error fetching tunggakan:', error);
        listTunggakan.value = [];
    }
    
    // Ambil data meteran (akan otomatis mengisi dari bulan lalu jika belum ada data bulan ini)
    await fetchMeteranDataForPembayaran(pelanggan);
};

const hitungPemakaianPembayaran = () => {
    const sebelum = parseFloat(pembayaranForm.value.meteran_sebelum) || 0;
    const sesudah = parseFloat(pembayaranForm.value.meteran_sesudah) || 0;
    const pemakaian = sesudah - sebelum;
    pembayaranForm.value.jumlah_kubik = pemakaian > 0 ? pemakaian : 0;
    
    // Jika keterangan CICILAN, jangan auto-calculate - biarkan user edit manual
    if (pembayaranForm.value.keterangan === 'CICILAN') {
        return;
    }
    
    // Hitung total bayar
    if (selectedPelanggan.value?.kategori === 'sosial') {
        pembayaranForm.value.jumlah_bayar = 0;
    } else {
        const biayaPemakaian = pembayaranForm.value.jumlah_kubik * 2000;
        const biayaAbunemen = pembayaranForm.value.abunemen ? 3000 : 0;
        const tunggakan = pembayaranForm.value.bayar_tunggakan ? parseFloat(pembayaranForm.value.jumlah_bayar_tunggakan || 0) : 0;
        pembayaranForm.value.jumlah_bayar = biayaPemakaian + biayaAbunemen + tunggakan;
    }
};

const toggleBayarTunggakan = () => {
    if (pembayaranForm.value.bayar_tunggakan) {
        // Default set jumlah bayar tunggakan = total tunggakan (bisa diedit)
        const totalTunggakan = listTunggakan.value.reduce((sum, t) => sum + t.sisa_tagihan, 0);
        pembayaranForm.value.jumlah_bayar_tunggakan = totalTunggakan;
    } else {
        pembayaranForm.value.jumlah_bayar_tunggakan = 0;
    }
    hitungTotalBayar();
};

const hitungTotalBayar = () => {
    if (!selectedPelanggan.value) return;
    
    // Jika keterangan CICILAN, jangan auto-calculate - biarkan user edit manual
    if (pembayaranForm.value.keterangan === 'CICILAN') {
        return;
    }
    
    const tagihanBulanIni = selectedPelanggan.value.tagihan?.total_tagihan || 0;
    const tunggakan = pembayaranForm.value.bayar_tunggakan ? parseFloat(pembayaranForm.value.jumlah_bayar_tunggakan || 0) : 0;
    
    pembayaranForm.value.jumlah_bayar = tagihanBulanIni + tunggakan;
};

const closePembayaranModal = () => {
    showPembayaranModal.value = false;
    selectedPelanggan.value = null;
    listTunggakan.value = [];
};

const openKonfirmasiModal = (pelanggan) => {
    selectedKonfirmasi.value = pelanggan;
    catatanAdmin.value = '';
    showKonfirmasiModal.value = true;
};

const closeKonfirmasiModal = () => {
    showKonfirmasiModal.value = false;
    selectedKonfirmasi.value = null;
    catatanAdmin.value = '';
};

const approveKonfirmasi = async () => {
    if (!confirm('Yakin ingin approve pembayaran ini?')) return;
    
    isProcessing.value = true;
    try {
        await axios.post(`/tagihan-bulanan/${selectedKonfirmasi.value.tagihan.id}/approve-konfirmasi`, {
            catatan: catatanAdmin.value
        });
        
        alert('Pembayaran berhasil di-approve!');
        closeKonfirmasiModal();
        reloadPage();
    } catch (error) {
        alert('Gagal approve: ' + (error.response?.data?.message || error.message));
    } finally {
        isProcessing.value = false;
    }
};

const rejectKonfirmasi = async () => {
    if (!catatanAdmin.value) {
        alert('Harap isi catatan alasan penolakan');
        return;
    }
    
    if (!confirm('Yakin ingin tolak konfirmasi pembayaran ini?')) return;
    
    isProcessing.value = true;
    try {
        await axios.post(`/tagihan-bulanan/${selectedKonfirmasi.value.tagihan.id}/reject-konfirmasi`, {
            catatan: catatanAdmin.value
        });
        
        alert('Konfirmasi pembayaran ditolak');
        closeKonfirmasiModal();
        reloadPage();
    } catch (error) {
        alert('Gagal tolak: ' + (error.response?.data?.message || error.message));
    } finally {
        isProcessing.value = false;
    }
};

const submitPembayaran = async () => {
    isSubmittingPembayaran.value = true;
    
    try {
        const payload = {
            bulan_bayar: selectedBulan.value,
            tanggal_bayar: pembayaranForm.value.tanggal_bayar,
            meteran_sebelum: pembayaranForm.value.meteran_sebelum,
            meteran_sesudah: pembayaranForm.value.meteran_sesudah,
            jumlah_kubik: pembayaranForm.value.jumlah_kubik,
            abunemen: pembayaranForm.value.abunemen,
            tunggakan: 0, // Tunggakan di handle via bayar_tunggakan
            jumlah_bayar: pembayaranForm.value.jumlah_bayar,
            keterangan: pembayaranForm.value.keterangan, // Pure enum: LUNAS, CICILAN, TUNGGAKAN
            bayar_tunggakan: pembayaranForm.value.bayar_tunggakan,
            jumlah_bayar_tunggakan: pembayaranForm.value.jumlah_bayar_tunggakan || 0,
        };
        
        // Tambahkan id tunggakan jika bayar tunggakan
        if (pembayaranForm.value.bayar_tunggakan && listTunggakan.value.length > 0) {
            payload.id_tunggakan = listTunggakan.value.map(t => t.id);
        }
        
        await axios.post(`/pelanggan/${selectedPelanggan.value.id}/pembayaran`, payload);
        
        alert('Pembayaran berhasil disimpan!');
        closePembayaranModal();
        reloadPage();
    } catch (error) {
        alert('Gagal menyimpan pembayaran: ' + (error.response?.data?.message || error.message));
    } finally {
        isSubmittingPembayaran.value = false;
    }
};

const hitungPemakaian = () => {
    // Trigger computed property recalculation
};

const submitMeteran = async () => {
    isSubmitting.value = true;
    
    try {
        const response = await axios.post('/tagihan-bulanan', {
            pelanggan_id: selectedPelanggan.value.id,
            bulan: selectedBulan.value,
            ...inputForm.value,
        });
        
        alert('Meteran berhasil disimpan!');
        closeInputModal();
        reloadPage();
    } catch (error) {
        alert('Gagal menyimpan meteran: ' + (error.response?.data?.message || error.message));
    } finally {
        isSubmitting.value = false;
    }
};

const showPembayaranDetail = (pembayaran) => {
    selectedPembayaran.value = pembayaran;
    showPembayaranDetailModal.value = true;
};

const closePembayaranDetail = () => {
    showPembayaranDetailModal.value = false;
    selectedPembayaran.value = null;
};
</script>
