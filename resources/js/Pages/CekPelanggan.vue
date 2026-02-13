<template>
    <AppLayout>
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
                                        <div v-else-if="getStatusBayar(item).sudah_bayar" class="flex flex-col">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-1">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Sudah Bayar
                                            </span>
                                            <span class="text-xs text-gray-500">{{ getStatusBayar(item).bulan }}</span>
                                        </div>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Belum Bayar
                                        </span>
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
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tunggakan (Rp)</label>
                                    <input
                                        type="number"
                                        v-model="pembayaranForm.tunggakan"
                                        step="1000"
                                        placeholder="0"
                                        @input="hitungTagihan"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    />
                                    <p v-if="pembayaranErrors.tunggakan" class="text-red-600 text-sm mt-1">{{ pembayaranErrors.tunggakan }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Nominal tagihan bulan sebelumnya yang belum dibayar (maksimal 3 bulan)</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Tagihan (Rp)</label>
                                    <input
                                        type="number"
                                        v-model="pembayaranForm.jumlah_bayar"
                                        step="1000"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent bg-gray-50"
                                        readonly
                                    />
                                    <p v-if="pembayaranErrors.jumlah_bayar" class="text-red-600 text-sm mt-1">{{ pembayaranErrors.jumlah_bayar }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                                    <select
                                        v-model="pembayaranForm.keterangan"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    >
                                        <option value="">Pilih Keterangan</option>
                                        <option value="LUNAS">Lunas - Bayar penuh tagihan bulan ini</option>
                                        <option value="TUNGGAKAN">Tunggakan - Belum bayar, masuk bulan depan</option>
                                        <option value="CICILAN">Cicilan - Bayar sebagian</option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Pilih sesuai kondisi pembayaran pelanggan
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
                            <div v-else class="overflow-x-auto -mx-3 sm:mx-0">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-700 uppercase">Bulan</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-700 uppercase">Tgl Bayar</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-700 uppercase hidden sm:table-cell">Meteran Sebelum</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-700 uppercase hidden sm:table-cell">Meteran Sesudah</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-700 uppercase">Pakai</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center text-xs font-medium text-gray-700 uppercase hidden lg:table-cell">Abunemen</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-700 uppercase">Jumlah</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-medium text-gray-700 uppercase hidden md:table-cell">Ket</th>
                                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center text-xs font-medium text-gray-700 uppercase">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="item in pembayaranList" :key="item.id" class="hover:bg-gray-50">
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">{{ formatBulan(item.bulan_bayar) }}</td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">{{ formatTanggal(item.tanggal_bayar) }}</td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-600 hidden sm:table-cell">{{ item.meteran_sebelum || '-' }}</td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-600 hidden sm:table-cell">{{ item.meteran_sesudah || '-' }}</td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-600">{{ item.jumlah_kubik ? item.jumlah_kubik + ' m³' : '-' }}</td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center hidden lg:table-cell">
                                                <span v-if="item.abunemen" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Ya
                                                </span>
                                                <span v-else class="text-gray-400 text-xs sm:text-sm">-</span>
                                            </td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm font-medium">Rp {{ Number(item.jumlah_bayar).toLocaleString('id-ID') }}</td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm text-gray-600 hidden md:table-cell">{{ item.keterangan || '-' }}</td>
                                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm">
                                                <div class="flex gap-1 sm:gap-2 justify-center">
                                                    <button
                                                        @click="sendReceipt(item.id)"
                                                        class="p-1.5 sm:p-2 text-blue-600 hover:text-blue-800 active:text-blue-900 hover:bg-blue-50 active:bg-blue-100 rounded transition touch-manipulation"
                                                        title="Kirim Struk via WhatsApp"
                                                    >
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        @click="downloadPdf(item.id)"
                                                        class="p-1.5 sm:p-2 text-green-600 hover:text-green-800 active:text-green-900 hover:bg-green-50 active:bg-green-100 rounded transition touch-manipulation"
                                                        title="Download PDF"
                                                    >
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        @click="printReceipt(item.id)"
                                                        class="p-1.5 sm:p-2 text-purple-600 hover:text-purple-800 active:text-purple-900 hover:bg-purple-50 active:bg-purple-100 rounded transition touch-manipulation"
                                                        title="Cetak Struk"
                                                    >
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                        </svg>
                                                    </button>
                                                    <button
                                                        @click="deletePembayaran(item.id)"
                                                        class="p-1.5 sm:p-2 text-red-600 hover:text-red-800 active:text-red-900 hover:bg-red-50 active:bg-red-100 rounded transition touch-manipulation"
                                                        title="Hapus"
                                                    >
                                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    
                    <!-- Loading State -->
                    <div v-if="photoLoading" class="mt-2 flex justify-center items-center py-20">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                    </div>
                    
                    <!-- Photo Display -->
                    <div v-else-if="selectedPhoto?.foto_rumah_url" class="mt-2">
                        <img 
                            :src="selectedPhoto.foto_rumah_url" 
                            :alt="`Foto Rumah ${selectedPhoto.nama_pelanggan}`" 
                            class="w-full h-auto rounded-lg border border-gray-200"
                            @load="photoLoading = false"
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

// Form pembayaran
const pembayaranForm = ref({
    bulan_bayar: props.bulanIni || '',
    tanggal_bayar: new Date().toISOString().split('T')[0],
    meteran_sebelum: null,
    meteran_sesudah: null,
    abunemen: false,
    tunggakan: 0,
    jumlah_kubik: null,
    jumlah_bayar: null,
    keterangan: ''
});

const hitungTagihan = () => {
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
            // Hitung biaya pemakaian bulan ini
            const biayaPemakaian = Math.round(pembayaranForm.value.jumlah_kubik * 2000);
            
            // Biaya abunemen bulan ini (Rp 3.000 jika dicentang)
            const biayaAbunemen = pembayaranForm.value.abunemen ? 3000 : 0;
            
            // Tunggakan dari bulan sebelumnya (sudah termasuk abunemen bulan sebelumnya)
            const nominalTunggakan = parseFloat(pembayaranForm.value.tunggakan) || 0;
            
            // Total = Pemakaian bulan ini + Abunemen bulan ini + Tunggakan
            pembayaranForm.value.jumlah_bayar = biayaPemakaian + biayaAbunemen + nominalTunggakan;
        }
    } else {
        pembayaranForm.value.jumlah_bayar = 0;
    }
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
    const sudahBayar = pelanggan.bulan_dibayar && pelanggan.bulan_dibayar.includes(bulanCek);
    
    // Format nama bulan
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const [year, month] = bulanCek.split('-');
    const bulanText = `${namaBulan[parseInt(month) - 1]} ${year}`;
    
    return {
        sudah_bayar: sudahBayar,
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
            keterangan: ''
        };
        
        // PENTING: Ambil data meteran setelah history pembayaran dimuat
        // Ini memastikan fitur "auto-fill meteran dari bulan lalu" bekerja dengan benar
        // karena fetchMeteranData perlu mengakses pembayaranList.value
        await fetchMeteranData(bulanTujuan);
        
        // Hitung tunggakan otomatis dari bulan sebelumnya yang belum bayar
        // Logic ini tetap di sini karena spesifik untuk inisialisasi form
        const tunggakanBulanSebelumnya = pembayaranList.value
            .filter(p => {
                const keterangan = p.keterangan ? p.keterangan.toUpperCase() : '';
                return p.bulan_bayar < bulanTujuan && 
                       (keterangan === 'TUNGGAKAN' || keterangan === 'CICILAN');
            })
            .reduce((total, p) => total + parseFloat(p.jumlah_bayar || 0), 0);
        
        if (tunggakanBulanSebelumnya > 0) {
            pembayaranForm.value.tunggakan = tunggakanBulanSebelumnya;
            // Jika ada tunggakan, hitung ulang total
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
        const response = await axios.post(
            `/pelanggan/${selectedPelanggan.value.id}/pembayaran`,
            pembayaranForm.value
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
            keterangan: ''
        };
        
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
    selectedPhoto.value = pelanggan;
    photoLoading.value = true;
    photoError.value = false;
    photoErrorMessage.value = '';
    showPhotoModal.value = true;
    
    // Debug: Log URL foto
    console.log('Foto URL:', pelanggan?.foto_rumah_url);
};

const handlePhotoError = (event) => {
    photoLoading.value = false;
    photoError.value = true;
    photoErrorMessage.value = 'Gagal memuat foto. File mungkin tidak ditemukan atau storage link belum dibuat.';
    console.error('Error loading photo:', event);
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
