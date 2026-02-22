<template>
    <AppLayout>
        <!-- Debug Info - Hapus setelah fix -->
        <div v-if="false" class="bg-yellow-100 border-l-4 border-yellow-500 p-4 m-4">
            <p class="font-bold">Debug Info:</p>
            <p>Total Pelanggan: {{ pelangganList?.length || 0 }}</p>
            <p>Filtered: {{ filteredPelanggan?.length || 0 }}</p>
        </div>
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Data Pelanggan KP-SPAMS</h1>
                            <p class="text-sm sm:text-base text-gray-600">Daftar lengkap pelanggan air bersih desa</p>
                        </div>
                        
                        <!-- Desktop Actions -->
                        <div class="hidden lg:flex gap-2 xl:gap-3">
                            <Link
                                href="/qr-code/bulk-preview"
                                class="inline-flex items-center px-3 xl:px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition text-sm"
                            >
                                <svg class="w-4 h-4 xl:w-5 xl:h-5 xl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span class="hidden xl:inline">Print QR</span>
                            </Link>
                            <Link
                                href="/pembayaran"
                                class="inline-flex items-center px-3 xl:px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition text-sm"
                            >
                                <svg class="w-4 h-4 xl:w-5 xl:h-5 xl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <span class="hidden xl:inline">Riwayat</span>
                            </Link>
                            <button
                                @click="exportExcel"
                                class="inline-flex items-center px-3 xl:px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition text-sm"
                            >
                                <svg class="w-4 h-4 xl:w-5 xl:h-5 xl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="hidden xl:inline">Excel</span>
                            </button>
                            <button
                                @click="exportPdf"
                                class="inline-flex items-center px-3 xl:px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition text-sm"
                            >
                                <svg class="w-4 h-4 xl:w-5 xl:h-5 xl:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="hidden xl:inline">PDF</span>
                            </button>
                            <Link
                                v-if="$page.props.auth?.user"
                                href="/pelanggan/create"
                                class="inline-flex items-center px-4 xl:px-6 py-2 bg-blue-800 text-white rounded-lg font-semibold hover:bg-blue-900 transition text-sm"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="hidden xl:inline">Tambah</span>
                                <span class="xl:hidden">+</span>
                            </Link>
                        </div>
                        
                        <!-- Mobile Actions Dropdown -->
                        <div class="lg:hidden relative">
                            <button
                                @click="showMobileMenu = !showMobileMenu"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-800 text-white rounded-lg font-semibold hover:bg-blue-900 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Menu Aksi
                            </button>
                            
                            <div v-if="showMobileMenu" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 border border-gray-200">
                                <div class="py-2">
                                    <Link
                                        href="/qr-code/bulk-preview"
                                        class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition"
                                        @click="showMobileMenu = false"
                                    >
                                        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                        Print QR Code
                                    </Link>
                                    <Link
                                        href="/pembayaran"
                                        class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition"
                                        @click="showMobileMenu = false"
                                    >
                                        <svg class="w-5 h-5 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        Riwayat Pembayaran
                                    </Link>
                                    <button
                                        @click="exportExcel(); showMobileMenu = false"
                                        class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition"
                                    >
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Export Excel
                                    </button>
                                    <button
                                        @click="exportPdf(); showMobileMenu = false"
                                        class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition"
                                    >
                                        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        Export PDF
                                    </button>
                                    <Link
                                        v-if="$page.props.auth?.user"
                                        href="/pelanggan/create"
                                        class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition border-t border-gray-100"
                                        @click="showMobileMenu = false"
                                    >
                                        <svg class="w-5 h-5 mr-3 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Pelanggan
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.flash?.success" class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Data Pelanggan -->
                <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6">
                    <div class="space-y-3">
                        <!-- Search -->
                        <div class="w-full">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Cari berdasarkan ID, nama, RT, atau RW..."
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                        </div>
                        
                        <!-- Filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <select
                                v-model="statusFilter"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            >
                                <option value="all">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            <select
                                v-model="bulanFilter"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            >
                                <option value="all">Semua Bulan</option>
                                <option v-for="bulan in bulanOptions" :key="bulan.value" :value="bulan.value">
                                    {{ bulan.label }}
                                </option>
                            </select>
                            <select
                                v-if="$page.props.auth.user.role !== 'penarik'"
                                v-model="wilayahFilter"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            >
                                <option value="all">Semua Wilayah</option>
                                <option v-for="wilayah in wilayahOptions" :key="wilayah" :value="wilayah">
                                    {{ wilayah }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <div class="flex items-center">
                            <div class="p-2.5 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Pelanggan</p>
                                <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ allPelanggan.length }}</p>
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
                                <p class="text-sm font-medium text-gray-500">Pelanggan Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ pelangganAktif }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Hasil Pencarian</p>
                                <p class="text-2xl font-bold text-gray-900">{{ filteredPelanggan.length }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-blue-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">NIK</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">No. WhatsApp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">RT / RW</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Wilayah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status Bayar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Foto Rumah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Google Maps</th>
                                    <th v-if="$page.props.auth?.user" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="filteredPelanggan.length === 0">
                                    <td :colspan="$page.props.auth?.user ? 11 : 10" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2">Tidak ada data pelanggan yang ditemukan</p>
                                    </td>
                                </tr>
                                <tr v-for="item in filteredPelanggan" :key="item.id_pelanggan" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.id_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ item.nama_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ item.nik || '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a v-if="item.no_whatsapp" :href="`https://wa.me/${item.no_whatsapp.replace(/^0/, '62')}`" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                            {{ item.no_whatsapp }}
                                        </a>
                                        <span v-else class="text-sm text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ item.rt ? `RT ${item.rt}` : '-' }}
                                            {{ item.rt && item.rw ? '/' : '' }}
                                            {{ item.rw ? `RW ${item.rw}` : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                            {{ item.wilayah || 'Belum diset' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="item.kategori === 'sosial'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                            </svg>
                                            Sosial (Gratis)
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                            </svg>
                                            Umum
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="item.status_aktif" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Aktif
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            Nonaktif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- Status untuk pelanggan kategori sosial: GRATIS -->
                                        <div v-if="item.kategori === 'sosial'" class="flex flex-col">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mb-1">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Gratis
                                            </span>
                                            <span class="text-xs text-purple-600 font-medium">{{ getStatusBayar(item).bulan }}</span>
                                        </div>

                                        <!-- Status untuk pelanggan umum -->
                                        <div v-else class="flex flex-col">
                                            <!-- Sudah Bayar -->
                                            <template v-if="getStatusBayar(item).status === 'SUDAH_BAYAR'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-1">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                    Sudah Bayar
                                                </span>
                                            </template>
                                            
                                            <!-- Menunggak -->
                                            <template v-else-if="getStatusBayar(item).status === 'MENUNGGAK' || getStatusBayar(item).status === 'TUNGGAKAN'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mb-1">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                    Menunggak
                                                </span>
                                            </template>

                                            <!-- Cicilan -->
                                            <template v-else-if="getStatusBayar(item).status === 'CICILAN'">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mb-1">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                                    </svg>
                                                    Cicilan
                                                </span>
                                            </template>

                                            <!-- Belum Bayar -->
                                            <template v-else>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mb-1">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    Belum Bayar
                                                </span>
                                            </template>
                                            
                                            <span class="text-xs text-gray-500">{{ getStatusBayar(item).bulan }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button 
                                            v-if="item.foto_rumah_url"
                                            @click="showFotoModal(item)"
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 transition"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Lihat Foto
                                        </button>
                                        <span v-else class="text-gray-400 text-xs">Tidak ada foto</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <Link 
                                            v-if="item.has_coordinates" 
                                            :href="`/peta?pelanggan=${item.id_pelanggan}`"
                                            class="inline-flex items-center text-blue-800 hover:text-blue-900 font-medium"
                                        >
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Lihat Peta
                                        </Link>
                                        <span v-else class="text-gray-400 text-xs">Tidak ada koordinat</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <template v-if="item.google_maps_url">
                                            <a 
                                                :href="item.google_maps_url" 
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-900 font-medium"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                                </svg>
                                                Buka Maps
                                            </a>
                                        </template>
                                        <template v-else-if="item.has_coordinates">
                                            <a 
                                                :href="item.google_maps_link" 
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center text-blue-600 hover:text-blue-900 font-medium"
                                            >
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                                </svg>
                                                Buka Maps
                                            </a>
                                        </template>
                                        <template v-else>
                                            <span class="text-gray-400 text-xs">-</span>
                                        </template>
                                    </td>
                                    <td v-if="$page.props.auth?.user" class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2 items-center">
                                            <!-- Tombol Download QR Code -->
                                            <a
                                                :href="`/pelanggan/${item.id}/download-qr`"
                                                target="_blank"
                                                class="text-green-600 hover:text-green-800 transition"
                                                title="Download QR Code"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                                </svg>
                                            </a>
                                            <!-- Tombol Riwayat Pembayaran -->
                                            <button
                                                @click="showPembayaranModal(item)"
                                                class="text-purple-600 hover:text-purple-800 transition"
                                                title="Riwayat Pembayaran"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </button>
                                            <Link
                                                :href="`/pelanggan/${item.id}/edit`"
                                                class="text-blue-600 hover:text-blue-800 transition"
                                                title="Edit"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button
                                                @click="confirmDelete(item)"
                                                class="text-red-600 hover:text-red-800 transition"
                                                title="Hapus"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

        <!-- Modal Pembayaran -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeModal">
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col">
                    <div class="bg-blue-800 text-white px-4 sm:px-6 py-3 sm:py-4 rounded-t-lg flex justify-between items-center flex-shrink-0">
                        <h3 class="text-lg sm:text-xl font-bold">Riwayat Pembayaran - {{ selectedPelanggan?.nama_pelanggan }}</h3>
                        <button @click="closeModal" class="text-white hover:text-gray-200 p-1">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-4 sm:p-6 overflow-y-auto flex-1">
                        <!-- Form Tambah Pembayaran -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-3 sm:p-4">
                            <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Tambah Pembayaran Baru</h4>
                            
                            <!-- Info Pelanggan Belum Bayar Bulan Ini -->
                            <div v-if="selectedPelanggan?.kategori !== 'sosial' && !getStatusBayar(selectedPelanggan).sudah_bayar" class="mb-4 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-bold text-yellow-800 mb-1">Pelanggan Belum Bayar Bulan Ini</p>
                                        <p class="text-sm text-yellow-700">
                                            Bulan: <strong>{{ getStatusBayar(selectedPelanggan).bulan }}</strong>. 
                                            Silakan input pembayaran untuk bulan ini atau bulan lainnya.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Info Kategori Sosial -->
                            <div v-if="selectedPelanggan?.kategori === 'sosial'" class="mb-4 bg-purple-50 border border-purple-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm font-medium text-purple-800">Pelanggan Kategori Sosial - Tidak Dikenakan Biaya (Gratis)</span>
                                </div>
                            </div>
                            
                            <!-- Info Cicilan (jika ada) -->
                            <InfoCicilan
                                v-if="currentTagihan"
                                :total-tagihan="currentTagihan.total_tagihan || 0"
                                :jumlah-terbayar="currentTagihan.jumlah_terbayar || 0"
                                :status-bayar="currentTagihan.status_bayar || 'BELUM_BAYAR'"
                                class="mb-4"
                            />
                            
                            <!-- Info Tunggakan (jika ada) - Bersifat informatif, tidak wajib dibayar -->
                            <div v-if="listTunggakan.length > 0" class="mb-4 bg-amber-50 border border-amber-300 rounded-lg p-4">
                                <h5 class="font-bold text-amber-900 mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Info Tunggakan
                                    <span class="ml-auto text-xs font-normal bg-amber-100 text-amber-700 border border-amber-300 px-2 py-0.5 rounded-full">Opsional</span>
                                </h5>
                                <p class="text-xs text-amber-700 mb-3">Pelanggan ini memiliki tunggakan dari bulan sebelumnya. Pembayaran tunggakan <strong>tidak wajib</strong> dilakukan sekarang — bisa dibayar kapan saja.</p>
                                <div class="space-y-1 text-sm mb-3 bg-white rounded-lg border border-amber-200 p-3">
                                    <div v-for="item in listTunggakan" :key="item.id" class="flex justify-between py-1 border-b border-amber-100 last:border-0">
                                        <span class="text-amber-800">{{ formatBulan(item.bulan) }}</span>
                                        <span class="font-semibold text-amber-900">Rp {{ Number(item.sisa_tagihan).toLocaleString('id-ID') }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 font-bold border-t-2 border-amber-200 mt-1">
                                        <span class="text-amber-800">Total Tunggakan:</span>
                                        <span class="text-amber-900">Rp {{ Number(listTunggakan.reduce((sum, t) => sum + t.sisa_tagihan, 0)).toLocaleString('id-ID') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Checkbox opsional untuk bayar tunggakan -->
                                <label class="flex items-start gap-2 cursor-pointer p-2 rounded-lg hover:bg-amber-100 transition">
                                    <input
                                        type="checkbox"
                                        v-model="pembayaranForm.bayar_tunggakan"
                                        @change="toggleBayarTunggakan"
                                        class="mt-0.5 w-4 h-4 text-amber-600 border-gray-300 rounded focus:ring-2 focus:ring-amber-500"
                                    />
                                    <div>
                                        <span class="text-sm font-medium text-amber-800">Bayar tunggakan sekaligus? <span class="font-normal text-amber-600">(opsional)</span></span>
                                        <p class="text-xs text-amber-600 mt-0.5">Centang jika pelanggan ingin melunasi sebagian atau semua tunggakan bersamaan dengan pembayaran bulan ini.</p>
                                    </div>
                                </label>
                                    
                                <div v-if="pembayaranForm.bayar_tunggakan" class="ml-6 mt-2">
                                    <label class="block text-sm font-medium text-amber-800 mb-1">
                                        Jumlah Tunggakan yang Dibayar (Rp)
                                        <span class="text-amber-600 text-xs font-normal">— Bisa dicicil</span>
                                    </label>
                                    <input
                                        type="number"
                                        v-model="pembayaranForm.jumlah_bayar_tunggakan"
                                        @input="hitungTagihan"
                                        step="1000"
                                        min="0"
                                        :max="listTunggakan.reduce((sum, t) => sum + t.sisa_tagihan, 0)"
                                        class="w-full px-3 py-2 border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 bg-white text-sm"
                                        :placeholder="'Maks: Rp ' + Number(listTunggakan.reduce((sum, t) => sum + t.sisa_tagihan, 0)).toLocaleString('id-ID')"
                                    />
                                    <p class="text-xs text-amber-600 mt-1">💡 Bisa diisi sebagian untuk cicil (misal: Rp 5.000 dari total Rp {{ Number(listTunggakan.reduce((sum, t) => sum + t.sisa_tagihan, 0)).toLocaleString('id-ID') }})</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan Bayar</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <input
                                            type="month"
                                            v-model="pembayaranForm.bulan_bayar"
                                            class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 bg-white"
                                        />
                                    </div>
                                    <p v-if="pembayaranErrors.bulan_bayar" class="mt-1 text-xs text-red-600">{{ pembayaranErrors.bulan_bayar }}</p>
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <input
                                            type="date"
                                            v-model="pembayaranForm.tanggal_bayar"
                                            class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 bg-white"
                                        />
                                    </div>
                                    <p v-if="pembayaranErrors.tanggal_bayar" class="mt-1 text-xs text-red-600">{{ pembayaranErrors.tanggal_bayar }}</p>
                                </div>
                                
                                <!-- Info Meteran Belum Ada -->
                                <div v-if="!pembayaranForm.meteran_sebelum && !pembayaranForm.meteran_sesudah" class="col-span-1 sm:col-span-2 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                                    <div class="flex items-start gap-3">
                                        <svg class="h-5 w-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="text-sm">
                                            <p class="font-semibold text-yellow-800 mb-1">Data Meteran Belum Tersedia</p>
                                            <p class="text-yellow-700">Meteran untuk bulan ini belum diinput. Silakan input data meteran air terlebih dahulu menggunakan <strong>QR Scanner</strong> atau isi manual di form ini.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-span-1 space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Meteran Sebelum</label>
                                    <input
                                        type="number"
                                        v-model="pembayaranForm.meteran_sebelum"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 bg-white"
                                        @input="hitungTagihan"
                                    />
                                    <p v-if="pembayaranErrors.meteran_sebelum" class="mt-1 text-xs text-red-600">{{ pembayaranErrors.meteran_sebelum }}</p>
                                    <p class="text-xs text-gray-500">Angka meteran bulan lalu</p>
                                </div>
                                <div class="col-span-1 space-y-2">
                                    <label class="block text-sm font-medium text-gray-700">Meteran Sesudah</label>
                                    <input
                                        type="number"
                                        v-model="pembayaranForm.meteran_sesudah"
                                        step="0.01"
                                        placeholder="0.00"
                                        class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 bg-white"
                                        @input="hitungTagihan"
                                    />
                                    <p v-if="pembayaranErrors.meteran_sesudah" class="mt-1 text-xs text-red-600">{{ pembayaranErrors.meteran_sesudah }}</p>
                                    <p class="text-xs text-gray-500">Angka meteran bulan ini</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pemakaian (m³)</label>
                                    <input
                                        type="number"
                                        v-model="pembayaranForm.jumlah_kubik"
                                        step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent bg-gray-50"
                                        readonly
                                    />
                                    <p v-if="pembayaranErrors.jumlah_kubik" class="text-red-600 text-sm mt-1">{{ pembayaranErrors.jumlah_kubik }}</p>
                                    <p v-if="selectedPelanggan?.kategori === 'sosial'" class="text-xs text-purple-600 mt-1 font-medium">Tarif: Gratis (Kategori Sosial)</p>
                                    <p v-else class="text-xs text-gray-500 mt-1">Tarif: Rp 2.000 / m³</p>
                                </div>
                                <div>
                                    <label class="flex items-center space-x-3 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            v-model="pembayaranForm.abunemen"
                                            @change="hitungTagihan"
                                            class="w-5 h-5 text-blue-800 border-gray-300 rounded focus:ring-2 focus:ring-blue-800"
                                        />
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Abunemen</span>
                                            <p class="text-xs text-gray-500">Biaya tambahan: Rp 3.000</p>
                                        </div>
                                    </label>
                                </div>
                                <!-- Field tunggakan dihapus dari sini karena sudah ada di section Info Tunggakan di atas -->

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ pembayaranForm.status_bayar === 'CICILAN' ? 'Jumlah Cicilan' : 'Total Tagihan' }} (Rp)
                                        <span v-if="pembayaranForm.status_bayar === 'CICILAN'" class="text-blue-600 text-xs">- Bisa diedit</span>
                                    </label>
                                    <input
                                        type="number"
                                        v-model="pembayaranForm.jumlah_bayar"
                                        step="1000"
                                        min="0"
                                        :class="[
                                            'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent',
                                            pembayaranForm.status_bayar === 'CICILAN' ? 'bg-white' : 'bg-gray-50'
                                        ]"
                                        :readonly="pembayaranForm.status_bayar !== 'CICILAN'"
                                    />
                                    <p v-if="pembayaranErrors.jumlah_bayar" class="text-red-600 text-sm mt-1">{{ pembayaranErrors.jumlah_bayar }}</p>
                                    <p v-if="pembayaranForm.status_bayar === 'CICILAN'" class="text-xs text-blue-600 mt-1">💡 Untuk cicilan, isi jumlah berapa saja (misal: Rp 1.000, Rp 5.000, dll)</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Bayar <span class="text-red-500">*</span></label>
                                    <select
                                        v-model="pembayaranForm.status_bayar"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                        required
                                    >
                                        <option value="BELUM_BAYAR">Belum Bayar - Bulan ini belum dibayar</option>
                                        <option value="CICILAN">Cicilan - Bayar sebagian dulu</option>
                                        <option value="SUDAH_BAYAR">Sudah Bayar - Lunas penuh</option>
                                        <option value="TUNGGAKAN">Tunggakan - Input bulan lalu yang nunggak</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Pilih sesuai kondisi pembayaran
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                                    <input
                                        type="text"
                                        v-model="pembayaranForm.keterangan"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                        placeholder="Misal: Bayar di warung, Cicilan ke-2, dll (opsional)"
                                        maxlength="255"
                                    />
                                    <p class="text-xs text-gray-500 mt-1">
                                        Catatan tambahan (tidak wajib)
                                    </p>
                                </div>
                            </div>
                            <button
                                @click="submitPembayaran"
                                :disabled="isSubmitting"
                                class="mt-3 sm:mt-4 w-full sm:w-auto px-4 py-2 text-sm sm:text-base bg-blue-800 text-white rounded-lg hover:bg-blue-900 active:bg-blue-950 disabled:bg-gray-400 transition touch-manipulation"
                            >
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan Pembayaran' }}
                            </button>
                        </div>

                        <!-- List Riwayat Pembayaran -->
                        <div>
                            <h4 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Daftar Pembayaran</h4>
                            <div v-if="loadingPembayaran" class="text-center py-8">
                                <p class="text-gray-500 text-sm">Memuat data...</p>
                            </div>
                            <div v-else-if="pembayaranList.length === 0" class="text-center py-8">
                                <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-gray-500 text-sm">Belum ada riwayat pembayaran</p>
                            </div>
                            <div v-else>
                                <!-- Statistik Ringkasan -->
                                <div class="grid grid-cols-3 gap-3 mb-4 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100">
                                    <div class="text-center">
                                        <div class="text-xl font-bold text-blue-700">{{ pembayaranList.length }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Total Transaksi</div>
                                    </div>
                                    <div class="text-center border-x border-blue-200">
                                        <div class="text-sm font-bold text-green-700">Rp {{ Number(pembayaranList.reduce((s, p) => s + Number(p.jumlah_bayar || 0), 0)).toLocaleString('id-ID') }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">Total Dibayar</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xl font-bold text-purple-700">{{ pembayaranList.reduce((s, p) => s + Number(p.jumlah_kubik || 0), 0).toFixed(1) }} <span class="text-sm font-normal">m³</span></div>
                                        <div class="text-xs text-gray-500 mt-0.5">Total Pemakaian</div>
                                    </div>
                                </div>

                                <!-- Card per Transaksi -->
                                <div class="space-y-3">
                                    <div
                                        v-for="item in pembayaranList"
                                        :key="item.id"
                                        class="border border-gray-200 rounded-xl overflow-hidden hover:border-blue-300 hover:shadow-md transition-all duration-200 bg-white"
                                    >
                                        <!-- Header Card -->
                                        <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-base">{{ formatBulan(item.bulan_bayar) }}</div>
                                                    <div class="text-xs text-blue-200">Tgl Bayar: {{ formatTanggal(item.tanggal_bayar) }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <!-- Badge Keterangan -->
                                                <span v-if="item.keterangan"
                                                    :class="[
                                                        'px-2.5 py-1 rounded-full text-xs font-bold',
                                                        item.keterangan === 'LUNAS' ? 'bg-green-400/30 text-green-100 border border-green-300/40' :
                                                        item.keterangan === 'CICILAN' ? 'bg-yellow-400/30 text-yellow-100 border border-yellow-300/40' :
                                                        item.keterangan === 'TUNGGAKAN' ? 'bg-red-400/30 text-red-100 border border-red-300/40' :
                                                        'bg-white/20 text-white'
                                                    ]"
                                                >
                                                    {{ item.keterangan === 'LUNAS' ? '✓ LUNAS' : item.keterangan === 'CICILAN' ? '⟳ CICILAN' : item.keterangan === 'TUNGGAKAN' ? '⚠ TUNGGAKAN' : item.keterangan }}
                                                </span>
                                                <div class="text-xs text-blue-200 bg-white/10 rounded px-2 py-1">#{{ item.id }}</div>
                                            </div>
                                        </div>

                                        <!-- Body Card -->
                                        <div class="p-4">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                                <!-- Kiri: Data Meteran -->
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                                        Data Meteran Air
                                                    </div>
                                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                                                        <!-- Visualisasi Stand Meter -->
                                                        <div class="flex items-center gap-3 mb-3">
                                                            <div class="flex-1 text-center bg-white rounded-lg py-2 px-3 border border-slate-200 shadow-sm">
                                                                <div class="text-xs text-gray-400 mb-0.5">Stand Awal</div>
                                                                <div class="text-xl font-bold text-gray-700 font-mono tracking-wider">
                                                                    {{ item.meteran_sebelum !== null ? Number(item.meteran_sebelum).toFixed(1) : '—' }}
                                                                </div>
                                                            </div>
                                                            <div class="flex flex-col items-center gap-1">
                                                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                                                <div class="text-xs font-bold text-blue-600 bg-blue-50 rounded-full px-2 py-0.5 border border-blue-200">
                                                                    {{ item.jumlah_kubik ? '+' + Number(item.jumlah_kubik).toFixed(1) : '—' }}
                                                                </div>
                                                            </div>
                                                            <div class="flex-1 text-center bg-white rounded-lg py-2 px-3 border border-slate-200 shadow-sm">
                                                                <div class="text-xs text-gray-400 mb-0.5">Stand Akhir</div>
                                                                <div class="text-xl font-bold text-gray-700 font-mono tracking-wider">
                                                                    {{ item.meteran_sesudah !== null ? Number(item.meteran_sesudah).toFixed(1) : '—' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Volume pemakaian -->
                                                        <div class="text-center py-1.5 bg-blue-50 rounded-lg border border-blue-100">
                                                            <span class="text-xs text-blue-600 font-medium">Pemakaian bulan ini: </span>
                                                            <span class="text-sm font-bold text-blue-800">{{ item.jumlah_kubik ? Number(item.jumlah_kubik).toFixed(1) + ' m³' : '—' }}</span>
                                                        </div>
                                                    </div>
                                                    <!-- Waktu input sistem -->
                                                    <div v-if="item.created_at" class="mt-2 flex items-center gap-1.5 text-xs text-gray-400 pl-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Diinput sistem: {{ item.created_at }}
                                                    </div>
                                                </div>

                                                <!-- Kanan: Breakdown Biaya -->
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                        Rincian Tagihan
                                                    </div>
                                                    <div class="bg-slate-50 rounded-xl border border-slate-100 overflow-hidden">
                                                        <!-- Baris biaya air -->
                                                        <div class="flex items-center justify-between px-3 py-2.5 border-b border-slate-100">
                                                            <div>
                                                                <div class="text-sm text-gray-700 font-medium flex items-center gap-1.5">
                                                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                                                                    Biaya Pemakaian Air
                                                                </div>
                                                                <div v-if="item.jumlah_kubik && item.tarif_per_kubik" class="text-xs text-gray-400 mt-0.5 pl-4">
                                                                    {{ Number(item.jumlah_kubik).toFixed(1) }} m³ × Rp {{ Number(item.tarif_per_kubik).toLocaleString('id-ID') }}/m³
                                                                </div>
                                                            </div>
                                                            <div class="text-sm font-semibold text-gray-800">Rp {{ Number(item.biaya_air || 0).toLocaleString('id-ID') }}</div>
                                                        </div>
                                                        <!-- Baris abunemen -->
                                                        <div class="flex items-center justify-between px-3 py-2.5 border-b border-slate-100">
                                                            <div>
                                                                <div class="text-sm text-gray-700 font-medium flex items-center gap-1.5">
                                                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span>
                                                                    Abunemen
                                                                </div>
                                                                <div class="text-xs text-gray-400 mt-0.5 pl-4">Iuran tetap bulanan</div>
                                                            </div>
                                                            <div class="text-sm font-semibold" :class="item.abunemen ? 'text-gray-800' : 'text-gray-400'">
                                                                {{ item.abunemen ? 'Rp ' + Number(item.abunemen_nominal || 2000).toLocaleString('id-ID') : '— (Tidak)' }}
                                                            </div>
                                                        </div>
                                                        <!-- Baris tunggakan (jika ada) -->
                                                        <div v-if="item.tunggakan > 0" class="flex items-center justify-between px-3 py-2.5 border-b border-red-100 bg-red-50">
                                                            <div>
                                                                <div class="text-sm text-red-700 font-medium flex items-center gap-1.5">
                                                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>
                                                                    Pelunasan Tunggakan
                                                                </div>
                                                                <div class="text-xs text-red-400 mt-0.5 pl-4">Tagihan dari bulan sebelumnya</div>
                                                            </div>
                                                            <div class="text-sm font-semibold text-red-700">Rp {{ Number(item.tunggakan).toLocaleString('id-ID') }}</div>
                                                        </div>
                                                        <!-- Total -->
                                                        <div class="flex items-center justify-between px-3 py-3 bg-gradient-to-r from-blue-600 to-indigo-600">
                                                            <div class="text-sm font-bold text-white">TOTAL DIBAYAR</div>
                                                            <div class="text-base font-bold text-white">Rp {{ Number(item.jumlah_bayar).toLocaleString('id-ID') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Footer Card: Aksi -->
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 py-2.5 bg-gray-50 border-t border-gray-200">
                                            <div class="text-xs text-gray-400 order-2 sm:order-1 font-medium">ID Transaksi: #{{ item.id }}</div>
                                            <div class="flex flex-wrap gap-2 order-1 sm:order-2">
                                                <button
                                                    @click="sendReceipt(item.id)"
                                                    class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition border border-blue-100 whitespace-nowrap"
                                                    title="Kirim Struk via WhatsApp"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                    WA
                                                </button>
                                                <button
                                                    @click="downloadPdf(item.id)"
                                                    class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition border border-green-100 whitespace-nowrap"
                                                    title="Download PDF"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    PDF
                                                </button>
                                                <button
                                                    @click="printReceipt(item.id)"
                                                    class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg transition border border-purple-100 whitespace-nowrap"
                                                    title="Cetak Struk"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                    Cetak
                                                </button>
                                                <button
                                                    @click="deletePembayaran(item.id)"
                                                    class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-bold bg-red-50 text-red-700 hover:bg-red-100 rounded-lg transition border border-red-100 whitespace-nowrap"
                                                    title="Hapus"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                         </div> 
                </div> <!-- Penutup modal body (p-4 overflow-y-auto) -->
            </div> <!-- Penutup modal content container (relative bg-white) -->
        </div> <!-- Penutup modal flex container -->
    </div> <!-- Penutup modal (v-if="showModal") -->
            </div> <!-- Penutup max-w-7xl mx-auto -->
        </div> <!-- Penutup py-12 -->
    </AppLayout>
    
    <!-- Modal Foto Rumah -->
    <div v-if="showPhotoModal" class="fixed inset-0 z-50 overflow-y-auto" @click="showPhotoModal = false">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" @click.stop>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            Foto Rumah - {{ selectedPhoto?.nama_pelanggan }}
                        </h3>
                        <button @click="showPhotoModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Photo Display -->
                    <div v-if="selectedPhoto?.foto_rumah_url && !photoError" class="mt-2 relative">
                        <!-- Loading Overlay -->
                        <div v-if="photoLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-10 rounded-lg">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-2"></div>
                                <p class="text-sm text-gray-600">Memuat foto...</p>
                            </div>
                        </div>
                        
                        <!-- Photo -->
                        <img 
                            :src="selectedPhoto.foto_rumah_url" 
                            :alt="`Foto Rumah ${selectedPhoto.nama_pelanggan}`" 
                            class="w-full h-auto rounded-lg border border-gray-200"
                            @load="onPhotoLoad"
                            @error="handlePhotoError"
                        />
                    </div>
                    
                    <!-- Error State -->
                    <div v-else-if="photoError" class="mt-2 p-8 text-center bg-red-50 rounded-lg">
                        <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h3 class="text-lg font-medium text-red-900 mb-2">Foto Tidak Dapat Dimuat</h3>
                        <p class="text-sm text-red-700 mb-4">{{ photoErrorMessage }}</p>
                        <p class="text-xs text-gray-600">File mungkin tidak ada atau storage link belum dibuat.</p>
                    </div>
                    
                    <!-- No Photo State -->
                    <div v-else class="mt-2 p-8 text-center bg-gray-50 rounded-lg">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-600">Tidak ada foto rumah</p>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <p><span class="font-semibold">ID Pelanggan:</span> {{ selectedPhoto?.id_pelanggan }}</p>
                            <p><span class="font-semibold">NIK:</span> {{ selectedPhoto?.nik || '-' }}</p>
                            <p><span class="font-semibold">Wilayah:</span> {{ selectedPhoto?.wilayah }}</p>
                        </div>
                        <a 
                            v-if="selectedPhoto?.foto_rumah_url && !photoError"
                            :href="selectedPhoto.foto_rumah_url" 
                            download 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition inline-flex items-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';
import InfoCicilan from '@/Components/InfoCicilan.vue';

const props = defineProps({
    pelangganList: {
        type: Array,
        default: () => []
    },
    pembayaranList: {
        type: Array,
        default: () => []
    },
    bulanIni: {
        type: String,
        default: ''
    },
    bulanFilter: {
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

const allPelanggan = ref(props.pelangganList || []);
const searchQuery = ref('');
const statusFilter = ref('all');
const bulanFilter = ref('all');
const wilayahFilter = ref('all');
const showMobileMenu = ref(false);
const showPhotoModal = ref(false);
const selectedPhoto = ref(null);
const photoLoading = ref(false);
const photoError = ref(false);
const photoErrorMessage = ref('');

// Tab state
const activeTab = ref('pelanggan');

// Pembayaran tab filters
const searchPembayaran = ref('');
const wilayahFilterPembayaran = ref('all');

// Modal state
const showModal = ref(false);
const selectedPelanggan = ref(null);
const pembayaranList = ref([]);
const loadingPembayaran = ref(false);
const isSubmitting = ref(false);
const currentTagihan = ref(null); // Data tagihan bulan ini untuk info cicilan
const listTunggakan = ref([]); // List tunggakan yang belum lunas

// Form pembayaran
const pembayaranForm = ref({
    bulan_bayar: props.bulanIni || '',
    tanggal_bayar: new Date().toISOString().split('T')[0],
    meteran_sebelum: null,
    meteran_sesudah: null,
    tarif_per_kubik: 2000, // Sync with backend
    biaya_abunemen_nominal: 3000, // Sync with backend
    abunemen: false,
    tunggakan: 0,
    jumlah_kubik: 0,
    jumlah_bayar: 0,
    keterangan: '',
    status_bayar: 'SUDAH_BAYAR',
    bayar_tunggakan: false, // Apakah mau bayar tunggakan juga
    jumlah_bayar_tunggakan: 0, // Jumlah yang dibayar untuk tunggakan (bisa cicilan)
    id_tunggakan: [] // Array ID tagihan yang mau dibayar
});

const hitungTagihan = () => {
    // Jika status CICILAN, jangan auto-calculate jumlah_bayar
    // Biarkan user input manual berapa yang mau dibayar
    if (pembayaranForm.value.status_bayar === 'CICILAN') {
        // Tetap hitung pemakaian kubik untuk referensi
        const meteranSebelum = parseFloat(pembayaranForm.value.meteran_sebelum) || 0;
        const meteranSesudah = parseFloat(pembayaranForm.value.meteran_sesudah) || 0;
        const pemakaian = meteranSesudah - meteranSebelum;
        pembayaranForm.value.jumlah_kubik = pemakaian >= 0 ? pemakaian : 0;
        // Jangan ubah jumlah_bayar - biarkan user input manual
        return;
    }
    
    // Hitung kubik dari selisih meteran
    const meteranSebelum = parseFloat(pembayaranForm.value.meteran_sebelum) || 0;
    const meteranSesudah = parseFloat(pembayaranForm.value.meteran_sesudah) || 0;
    
    // Pemakaian = Meteran Sesudah - Meteran Sebelum
    const pemakaian = meteranSesudah - meteranSebelum;
    pembayaranForm.value.jumlah_kubik = pemakaian >= 0 ? pemakaian : 0;
    
    // Hitung tagihan
    if (pembayaranForm.value.jumlah_kubik > 0 || pembayaranForm.value.abunemen || pembayaranForm.value.tunggakan > 0) {
        // Cek kategori pelanggan: gratis untuk sosial, normal untuk umum
        if (selectedPelanggan.value?.kategori === 'sosial') {
            pembayaranForm.value.jumlah_bayar = 0;
        } else {
            const tarif = parseFloat(pembayaranForm.value.tarif_per_kubik) || 2000;
            const biaya_abunemen_nominal = parseFloat(pembayaranForm.value.biaya_abunemen_nominal) || 3000;

            // Hitung biaya pemakaian bulan ini
            const biayaPemakaian = Math.round(pembayaranForm.value.jumlah_kubik * tarif);
            
            // Biaya abunemen bulan ini (Rp 3.000 jika dicentang)
            const biayaAbunemen = pembayaranForm.value.abunemen ? biaya_abunemen_nominal : 0;
            
            // Tunggakan dari bulan sebelumnya - Hanya tambahkan jika checkbox "Bayar Tunggakan" AKTIF
            const nominalTunggakan = pembayaranForm.value.bayar_tunggakan ? parseFloat(pembayaranForm.value.jumlah_bayar_tunggakan || 0) : 0;
            
            // Total = Pemakaian bulan ini + Abunemen bulan ini + Tunggakan
            pembayaranForm.value.jumlah_bayar = biayaPemakaian + biayaAbunemen + nominalTunggakan;
        }
    } else {
        pembayaranForm.value.jumlah_bayar = 0;
    }
};

const toggleBayarTunggakan = () => {
    if (pembayaranForm.value.bayar_tunggakan) {
        // Default: bayar semua tunggakan yang ada
        const totalTunggakan = listTunggakan.value.reduce((sum, t) => sum + (t.sisa_tagihan || 0), 0);
        pembayaranForm.value.jumlah_bayar_tunggakan = totalTunggakan;
    } else {
        pembayaranForm.value.jumlah_bayar_tunggakan = 0;
    }
    hitungTagihan();
};

const pembayaranErrors = ref({});

const pelangganAktif = computed(() => {
    return allPelanggan.value.filter(p => p.status_aktif).length;
});

// Generate 12 bulan terakhir untuk dropdown
const bulanOptions = computed(() => {
    const options = [];
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const today = new Date();
    
    for (let i = 0; i < 12; i++) {
        const date = new Date(today.getFullYear(), today.getMonth() - i, 1);
        const year = date.getFullYear();
        const month = date.getMonth();
        const value = `${year}-${String(month + 1).padStart(2, '0')}`;
        const label = `${namaBulan[month]} ${year}`;
        
        options.push({ value, label });
    }
    
    return options;
});

const filteredPelanggan = computed(() => {
    let result = allPelanggan.value;
    
    if (statusFilter.value === 'aktif') {
        result = result.filter(p => p.status_aktif);
    } else if (statusFilter.value === 'nonaktif') {
        result = result.filter(p => !p.status_aktif);
    }
    
    // Filter berdasarkan wilayah (case-insensitive, trim, dan normalisasi underscore/spasi)
    if (wilayahFilter.value !== 'all') {
        const filterValue = wilayahFilter.value.toLowerCase().trim().replace(/_/g, ' ').replace(/\s+/g, ' ');
        result = result.filter(p => {
            const pelangganWilayah = (p.wilayah || '').toLowerCase().trim().replace(/_/g, ' ').replace(/\s+/g, ' ');
            return pelangganWilayah === filterValue;
        });
    }
    
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase().trim();
        result = result.filter(p => 
            (p.id_pelanggan || '').toLowerCase().includes(query) ||
            (p.nama_pelanggan || '').toLowerCase().includes(query) ||
            (p.wilayah || '').toLowerCase().includes(query) ||
            (p.rt && p.rt.toString().includes(query)) ||
            (p.rw && p.rw.toString().includes(query))
        );
    }
    
    return result;
});

// Fungsi untuk mendapatkan status bayar berdasarkan filter bulan
const getStatusBayar = (pelanggan) => {
    const bulanCek = bulanFilter.value !== 'all' ? bulanFilter.value : props.bulanIni;
    
    // Format nama bulan
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const [year, month] = bulanCek.split('-');
    const bulanText = `${namaBulan[parseInt(month) - 1]} ${year}`;

    // Priority 1: Jika filter "all" (bulan ini), gunakan status_bayar (MENUNGGAK, CICILAN, dll)
    if (bulanFilter.value === 'all' && pelanggan.status_bayar) {
        return {
            sudah_bayar: pelanggan.status_bayar === 'SUDAH_BAYAR',
            status: pelanggan.status_bayar,
            bulan: bulanText
        };
    }

    // Priority 2: Periksa apakah ada di list lunas
    const sudahBayar = pelanggan.bulan_dibayar && pelanggan.bulan_dibayar.includes(bulanCek);
    
    return {
        sudah_bayar: sudahBayar,
        status: sudahBayar ? 'SUDAH_BAYAR' : 'BELUM_BAYAR',
        bulan: bulanText
    };
};

// Computed untuk wilayah options (dinamis dari data, normalisasi underscore)
const wilayahOptions = computed(() => {
    const wilayahSet = new Set();
    allPelanggan.value.forEach(p => {
        if (p.wilayah) {
            // Normalisasi: underscore jadi spasi, trim, lowercase untuk set
            const normalized = p.wilayah.trim().replace(/_/g, ' ');
            wilayahSet.add(normalized);
        }
    });
    return Array.from(wilayahSet).sort();
});

// Computed untuk filter pembayaran
const filteredPembayaranList = computed(() => {
    let result = props.pembayaranList || [];
    
    // Filter by search query
    if (searchPembayaran.value) {
        const query = searchPembayaran.value.toLowerCase().trim();
        result = result.filter(p =>
            p.id_pelanggan.toLowerCase().includes(query) ||
            p.nama_pelanggan.toLowerCase().includes(query)
        );
    }
    
    // Filter by wilayah (case-insensitive, trim, dan normalisasi underscore/spasi)
    if (wilayahFilterPembayaran.value !== 'all') {
        const filterValue = wilayahFilterPembayaran.value.toLowerCase().trim().replace(/_/g, ' ').replace(/\s+/g, ' ');
        result = result.filter(p => {
            const pelangganWilayah = (p.wilayah || '').toLowerCase().trim().replace(/_/g, ' ').replace(/\s+/g, ' ');
            return pelangganWilayah === filterValue;
        });
    }
    
    return result;
});

// Helper function untuk format tanggal pembayaran
const formatTanggalPembayaran = (tanggal) => {
    const date = new Date(tanggal);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};


const showPembayaranModal = async (pelanggan) => {
    selectedPelanggan.value = pelanggan;
    showModal.value = true;
    loadingPembayaran.value = true;
    pembayaranErrors.value = {};
    
    // Default values
    const today = new Date();
    const currentMonth = props.bulanIni || today.toISOString().slice(0, 7);
    const currentDate = today.toISOString().split('T')[0];
    
    // Load payment history first untuk cek bulan yang belum dibayar
    try {
        const response = await axios.get(`/pelanggan/${pelanggan.id}/pembayaran`);
        pembayaranList.value = response.data.pembayarans;
        
        // Cari bulan pertama yang belum dibayar (dari Januari tahun ini sampai bulan sekarang)
        const currentYear = today.getFullYear();
        const currentMonthNum = today.getMonth() + 1; // 1-12
        let bulanTujuan = currentMonth; // Default ke bulan sekarang
        
        // Cek dari Januari sampai bulan sekarang
        for (let month = 1; month <= currentMonthNum; month++) {
            const monthStr = `${currentYear}-${String(month).padStart(2, '0')}`;
            const sudahBayar = pembayaranList.value.some(p => p.bulan_bayar === monthStr);
            
            if (!sudahBayar) {
                bulanTujuan = monthStr;
                break; // Gunakan bulan pertama yang belum dibayar
            }
        }
        
        // Reset form dengan bulan yang tepat
        pembayaranForm.value = {
            bulan_bayar: bulanTujuan,
            tanggal_bayar: currentDate,
            meteran_sebelum: 0,
            meteran_sesudah: 0,
            abunemen: false,
            tunggakan: 0,
            jumlah_kubik: 0,
            jumlah_bayar: 0,
            keterangan: '',
            status_bayar: 'BELUM_BAYAR'
        };
        
        // PENTING: Ambil data meteran setelah history pembayaran dimuat
        // Ini memastikan fitur "auto-fill meteran dari bulan lalu" bekerja dengan benar
        // karena fetchMeteranData perlu mengakses pembayaranList.value
        await fetchMeteranData(bulanTujuan);
        
        // Load tunggakan yang belum lunas dari bulan sebelumnya
        try {
            const responseTunggakan = await axios.get(`/api/tagihan-bulanan/${pelanggan.id}/tunggakan`);
            if (responseTunggakan.data && responseTunggakan.data.tunggakan) {
                // Filter tunggakan: hanya bulan-bulan SEBELUM bulan yang sedang diinput
                // Ini mencegah bulan yang sama muncul di form utama DAN di list tunggakan
                listTunggakan.value = responseTunggakan.data.tunggakan.filter(t => t.bulan < bulanTujuan);
                
                // Hitung total tunggakan dari list yang sudah difilter
                const totalTunggakan = listTunggakan.value.reduce((sum, t) => sum + (t.sisa_tagihan || 0), 0);
                if (totalTunggakan > 0) {
                    pembayaranForm.value.tunggakan = totalTunggakan;
                }
            }
        } catch (error) {
            console.error('Error loading tunggakan:', error);
            listTunggakan.value = [];
        }
        
        // Hitung tagihan jika ada tunggakan
        if (pembayaranForm.value.tunggakan > 0) {
            hitungTagihan();
        }
    } catch (error) {
        console.error('Error loading data:', error);
    } finally {
        loadingPembayaran.value = false;
    }
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

const fetchMeteranData = async (bulan) => {
    if (!selectedPelanggan.value) return;
    
    // Reset currentTagihan
    currentTagihan.value = null;
    
    try {
        // 1. Try to get data for the SELECTED month
        // This checks if a bill/meter reading already exists for this specific month
        let currentTagihanRes;
        try {
             currentTagihanRes = await axios.get(`/api/tagihan-bulanan/${selectedPelanggan.value.id}/${bulan}`);
        } catch (err) {
            // Ignore 404, it just means no tagihan yet
            if (err.response && err.response.status === 404) {
                currentTagihanRes = { data: { tagihan: null } };
            } else {
                throw err;
            }
        }
        
        if (currentTagihanRes.data && currentTagihanRes.data.tagihan) {
            const tagihan = currentTagihanRes.data.tagihan;
            
            // Simpan data tagihan lengkap untuk InfoCicilan
            currentTagihan.value = tagihan;
            
            pembayaranForm.value.meteran_sebelum = tagihan.meteran_sebelum;
            pembayaranForm.value.meteran_sesudah = tagihan.meteran_sesudah;
            pembayaranForm.value.jumlah_kubik = tagihan.pemakaian_kubik;
            
            // Only overwrite abunemen if it's explicitly set in tagihan
            if (tagihan.ada_abunemen) pembayaranForm.value.abunemen = true;
            
            // Recalculate bill
            hitungTagihan();
            return; 
        }

        // 2. If NO data for current month (new entry), try to get PREVIOUS month's final reading
        // logic: meteran_sebelum (this month) = meteran_sesudah (previous month)
        const prevMonth = getPreviousMonth(bulan);
        if (prevMonth) {
            let foundPreviousReading = false;
            let previousReading = 0;

            // Check previous month bill/tagihan first
            try {
                const prevTagihanRes = await axios.get(`/api/tagihan-bulanan/${selectedPelanggan.value.id}/${prevMonth}`);
                if (prevTagihanRes.data && prevTagihanRes.data.tagihan && prevTagihanRes.data.tagihan.meteran_sesudah > 0) {
                    previousReading = prevTagihanRes.data.tagihan.meteran_sesudah;
                    foundPreviousReading = true;
                }
            } catch (err) {
                 // Ignore 404
                 if (err.response && err.response.status !== 404) {
                    console.error("Error fetching prev tagihan:", err);
                 }
            }

            // If tagihan didn't have valid reading, check payment history
            if (!foundPreviousReading) {
                const prevPayment = pembayaranList.value.find(p => p.bulan_bayar === prevMonth);
                if (prevPayment && prevPayment.meteran_sesudah > 0) {
                    previousReading = prevPayment.meteran_sesudah;
                    foundPreviousReading = true;
                }
            }

            if (foundPreviousReading) {
                pembayaranForm.value.meteran_sebelum = previousReading;
                pembayaranForm.value.meteran_sesudah = 0; // Reset sesudah because it's new
                pembayaranForm.value.jumlah_kubik = 0;
                hitungTagihan();
                return;
            }
        }

        // 3. Last Fallback: If no previous month data, reset to 0
        pembayaranForm.value.meteran_sebelum = 0;
        pembayaranForm.value.meteran_sesudah = 0;
        pembayaranForm.value.jumlah_kubik = 0;
        hitungTagihan();

    } catch (error) {
        console.error('Error fetching meteran data:', error);
    }
};

watch(() => pembayaranForm.value.bulan_bayar, (newBulan) => {
    if (newBulan && selectedPelanggan.value) {
        fetchMeteranData(newBulan);
    }
});

const closeModal = () => {
    showModal.value = false;
    selectedPelanggan.value = null;
    pembayaranList.value = [];
    pembayaranErrors.value = {};
};

const submitPembayaran = async () => {
    if (!selectedPelanggan.value) return;
    
    pembayaranErrors.value = {};
    isSubmitting.value = true;
    
    try {
        // Prepare payload dengan id_tunggakan jika bayar tunggakan
        const payload = {
            ...pembayaranForm.value
        };
        
        // Jika bayar tunggakan, kirim ID semua tunggakan untuk FIFO distribution
        if (pembayaranForm.value.bayar_tunggakan && listTunggakan.value.length > 0) {
            payload.id_tunggakan = listTunggakan.value.map(t => t.id);
        }
        
        const response = await axios.post(
            `/pelanggan/${selectedPelanggan.value.id}/pembayaran`,
            payload
        );
        
        // Add to list
        pembayaranList.value.unshift(response.data.pembayaran);
        
        // Update pelanggan di list
        const pelangganIndex = allPelanggan.value.findIndex(p => p.id === selectedPelanggan.value.id);
        if (pelangganIndex !== -1) {
            // Update array bulan_dibayar
            if (!allPelanggan.value[pelangganIndex].bulan_dibayar) {
                allPelanggan.value[pelangganIndex].bulan_dibayar = [];
            }
            if (!allPelanggan.value[pelangganIndex].bulan_dibayar.includes(response.data.pembayaran.bulan_bayar)) {
                allPelanggan.value[pelangganIndex].bulan_dibayar.push(response.data.pembayaran.bulan_bayar);
            }
            
            // Update status bayar jika bayar bulan ini
            if (response.data.pembayaran.bulan_bayar === props.bulanIni) {
                allPelanggan.value[pelangganIndex].sudah_bayar = true;
                // Format tanggal untuk display
                const date = new Date(response.data.pembayaran.tanggal_bayar);
                allPelanggan.value[pelangganIndex].tanggal_bayar = date.toLocaleDateString('id-ID', { 
                    day: '2-digit', 
                    month: '2-digit', 
                    year: 'numeric' 
                });
                allPelanggan.value[pelangganIndex].jumlah_bayar = response.data.pembayaran.jumlah_bayar;
            }
        }
        
        // Reset form
        pembayaranForm.value = {
            bulan_bayar: props.bulanIni || '',
            tanggal_bayar: new Date().toISOString().split('T')[0],
            meteran_sebelum: null,
            meteran_sesudah: null,
            abunemen: false,
            tunggakan: 0,
            jumlah_kubik: null,
            jumlah_bayar: null,
            keterangan: '', // Reset catatan kosong
            status_bayar: 'BELUM_BAYAR', // Reset ke default BELUM_BAYAR
            bayar_tunggakan: false,
            jumlah_bayar_tunggakan: 0,
            id_tunggakan: []
        };
        
        // Reset list tunggakan
        listTunggakan.value = [];
        currentTagihan.value = null;
        
        alert('Pembayaran berhasil ditambahkan!');
    } catch (error) {
        if (error.response?.status === 422) {
            if (error.response.data.errors) {
                pembayaranErrors.value = error.response.data.errors;
            } else if (error.response.data.message) {
                alert(error.response.data.message);
            }
        } else {
            alert('Terjadi kesalahan saat menyimpan pembayaran');
        }
    } finally {
        isSubmitting.value = false;
    }
};

const deletePembayaran = async (pembayaranId) => {
    if (!confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')) return;
    
    try {
        // Cari pembayaran yang akan dihapus
        const deletedPembayaran = pembayaranList.value.find(p => p.id === pembayaranId);
        
        await axios.delete(`/pembayaran/${pembayaranId}`);
        
        // Remove from list
        pembayaranList.value = pembayaranList.value.filter(p => p.id !== pembayaranId);
        
        // Update pelanggan di list
        if (deletedPembayaran) {
            const pelangganIndex = allPelanggan.value.findIndex(p => p.id === selectedPelanggan.value.id);
            if (pelangganIndex !== -1) {
                // Remove bulan dari array bulan_dibayar
                if (allPelanggan.value[pelangganIndex].bulan_dibayar) {
                    allPelanggan.value[pelangganIndex].bulan_dibayar = 
                        allPelanggan.value[pelangganIndex].bulan_dibayar.filter(b => b !== deletedPembayaran.bulan_bayar);
                }
                
                // Update status bayar jika hapus pembayaran bulan ini
                if (deletedPembayaran.bulan_bayar === props.bulanIni) {
                    allPelanggan.value[pelangganIndex].sudah_bayar = false;
                    allPelanggan.value[pelangganIndex].tanggal_bayar = null;
                    allPelanggan.value[pelangganIndex].jumlah_bayar = null;
                }
            }
        }
        
        alert('Pembayaran berhasil dihapus');
    } catch (error) {
        alert('Terjadi kesalahan saat menghapus pembayaran');
    }
};

const sendReceipt = async (pembayaranId) => {
    try {
        const response = await axios.post(`/pembayaran/${pembayaranId}/send-receipt`);
        
        if (response.data.wa_link) {
            // Open WhatsApp with the message
            window.open(response.data.wa_link, '_blank');
            
            alert(`Struk berhasil digenerate!\n\nPelanggan: ${response.data.pelanggan_nama}\nNo. WhatsApp: ${response.data.no_whatsapp}\n\nJendela WhatsApp akan terbuka. Silakan kirim pesan dengan struk pembayaran.`);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            alert(error.response.data.message || 'Nomor WhatsApp pelanggan tidak tersedia');
        } else if (error.response?.status === 404) {
            alert(error.response.data.message || 'Data pelanggan tidak ditemukan');
        } else {
            console.error('Error sending receipt:', error);
            alert('Terjadi kesalahan saat mengirim struk. Silakan coba lagi.');
        }
    }
};

const downloadPdf = (pembayaranId) => {
    // Open download URL in new window
    window.open(`/pembayaran/${pembayaranId}/download-pdf`, '_blank');
};

const printReceipt = (pembayaranId) => {
    // Open print view in new window
    window.open(`/pembayaran/${pembayaranId}/print`, '_blank');
};

const showFotoModal = (pelanggan) => {
    console.log('Opening modal for:', pelanggan?.nama_pelanggan);
    console.log('Foto URL:', pelanggan?.foto_rumah_url);
    
    selectedPhoto.value = pelanggan;
    photoError.value = false;
    photoErrorMessage.value = '';
    showPhotoModal.value = true;
    
    // Set loading dan timeout fallback
    if (pelanggan?.foto_rumah_url) {
        photoLoading.value = true;
        console.log('Photo loading set to true');
        
        // Fallback: auto-hide loading after 3 seconds
        setTimeout(() => {
            if (photoLoading.value) {
                console.log('Timeout: forcing loading to false');
                photoLoading.value = false;
            }
        }, 3000);
    } else {
        photoLoading.value = false;
    }
};

const onPhotoLoad = () => {
    console.log('Photo loaded successfully!');
    photoLoading.value = false;
};

const handlePhotoError = (event) => {
    console.error('Error loading photo:', event);
    photoLoading.value = false;
    photoError.value = true;
    photoErrorMessage.value = 'Gagal memuat foto. File mungkin tidak ditemukan atau storage link belum dibuat.';
};

const exportExcel = () => {
    const params = new URLSearchParams({
        search: searchQuery.value,
        status: statusFilter.value,
        bulan: bulanFilter.value,
        wilayah: wilayahFilter.value
    });
    window.location.href = `/cek-pelanggan/export-excel?${params.toString()}`;
};

const exportPdf = () => {
    const params = new URLSearchParams({
        search: searchQuery.value,
        status: statusFilter.value,
        bulan: bulanFilter.value,
        wilayah: wilayahFilter.value
    });
    window.location.href = `/cek-pelanggan/export-pdf?${params.toString()}`;
};

const formatBulan = (bulanBayar) => {
    if (!bulanBayar) return '-';
    const [year, month] = bulanBayar.split('-');
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${months[parseInt(month) - 1]} ${year}`;
};

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-';
    const date = new Date(tanggal);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const confirmDelete = (pelanggan) => {
    if (confirm(`Apakah Anda yakin ingin menghapus pelanggan ${pelanggan.nama_pelanggan}?`)) {
        router.delete(`/pelanggan/${pelanggan.id}`);
    }
};
</script>
