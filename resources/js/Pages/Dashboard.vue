<template>
    <AppLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard Pengelola</h1>
                            <p class="text-sm sm:text-base text-gray-600 mt-1">Selamat datang, {{ $page.props.auth.user.name }}</p>
                        </div>
                        <div class="w-full sm:w-auto">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Filter Wilayah</label>
                            <select
                                v-model="selectedWilayah"
                                @change="reloadDashboard"
                                class="w-full sm:w-auto px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            >
                                <option value="">Semua Wilayah</option>
                                <option value="Dawuhan">Dawuhan</option>
                                <option value="Kubangsari Kulon">Kubangsari Kulon</option>
                                <option value="Kubangsari Wetan">Kubangsari Wetan</option>
                                <option value="Sokarame">Sokarame</option>
                                <option value="Tiparjaya">Tiparjaya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-6 sm:mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
                        <Link
                            href="/qr-scanner"
                            class="group bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 p-4 sm:p-6 touch-manipulation"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold mb-1">Pindai QR Code</h3>
                                    <p class="text-green-100 text-xs sm:text-sm">Scan QR untuk input meteran</p>
                                </div>
                                <div class="bg-white/20 rounded-lg p-2 sm:p-3 group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                            </div>
                        </Link>
                        
                        <Link
                            href="/cek-pelanggan"
                            class="group bg-gradient-to-br from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 p-4 sm:p-6 touch-manipulation"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold mb-1">Data Pelanggan</h3>
                                    <p class="text-blue-100 text-xs sm:text-sm">Kelola data pelanggan</p>
                                </div>
                                <div class="bg-white/20 rounded-lg p-2 sm:p-3 group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                        </Link>
                        
                        <Link
                            href="/laporan"
                            class="group bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 p-4 sm:p-6 touch-manipulation"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold mb-1">Laporan Keuangan</h3>
                                    <p class="text-purple-100 text-xs sm:text-sm">Lihat laporan keuangan</p>
                                </div>
                                <div class="bg-white/20 rounded-lg p-2 sm:p-3 group-hover:bg-white/30 transition-colors">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
                    <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div class="ml-2 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Total Pelanggan</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats.totalPelanggan }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-2 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Pelanggan Aktif</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats.pelangganAktif }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-2 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Pelanggan Nonaktif</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats.pelangganNonaktif }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
                        <div class="flex items-center">
                            <div class="p-2 sm:p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <div class="ml-2 sm:ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Cakupan Area</p>
                                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ stats.cakupanArea }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Pembayaran Bulan Ini -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pembayaran Bulan Ini</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Pembayaran Bulan Ini</span>
                                <span class="text-xl font-bold text-blue-600">Rp {{ formatRupiah(pembayaran.pembayaranBulanIni) }}</span>
                            </div>
                            <div v-if="pembayaran.pembayaranTunggakan > 0" class="flex justify-between items-center pb-3 border-b">
                                <span class="text-gray-600">Tunggakan Bulan Lalu</span>
                                <span class="text-xl font-bold text-blue-600">Rp {{ formatRupiah(pembayaran.pembayaranTunggakan) }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b bg-blue-50 -mx-4 px-4 py-3 rounded-lg">
                                <span class="text-gray-900 font-semibold">Total Terbayar</span>
                                <span class="text-xl font-bold text-blue-700">Rp {{ formatRupiah(pembayaran.terbayar) }}</span>
                            </div>
                            
                            <div class="mt-6">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">Progress Pelanggan Bayar</span>
                                    <span class="font-semibold text-gray-900">{{ persenPelangganBayar }}%</span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-3">
                                    <div 
                                        class="bg-blue-600 h-3 rounded-full transition-all duration-300"
                                        :style="{ width: `${Math.min(persenPelangganBayar, 100)}%` }"
                                    ></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ pembayaran.sudahBayarCount }} dari {{ stats.pelangganAktif }} pelanggan aktif</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 mb-1">Sudah Bayar</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ pembayaran.sudahBayarCount }}</p>
                                    <p class="text-xs text-gray-500 mt-1">pelanggan</p>
                                </div>
                                <div class="bg-red-50 rounded-lg p-4">
                                    <p class="text-sm text-gray-600 mb-1">Belum Bayar</p>
                                    <p class="text-2xl font-bold text-red-600">{{ pembayaran.belumBayarCount }}</p>
                                    <p class="text-xs text-gray-500 mt-1">perlu ditagih</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Pelanggan</h3>
                        <div class="flex items-center justify-center h-64">
                            <div class="relative w-48 h-48">
                                <svg class="transform -rotate-90" viewBox="0 0 100 100">
                                    <circle
                                        cx="50"
                                        cy="50"
                                        r="40"
                                        fill="none"
                                        stroke="#e5e7eb"
                                        stroke-width="10"
                                    />
                                    <circle
                                        cx="50"
                                        cy="50"
                                        r="40"
                                        fill="none"
                                        stroke="#166534"
                                        stroke-width="10"
                                        :stroke-dasharray="`${aktifPercentage * 2.51} 251`"
                                        stroke-linecap="round"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <p class="text-3xl font-bold text-gray-900">{{ aktifPercentage }}%</p>
                                        <p class="text-sm text-gray-600">Aktif</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-around">
                            <div class="text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-3 h-3 bg-blue-800 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Aktif</span>
                                </div>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ stats.pelangganAktif }}</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-3 h-3 bg-gray-300 rounded-full mr-2"></div>
                                    <span class="text-sm text-gray-600">Nonaktif</span>
                                </div>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ stats.pelangganNonaktif }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions (New Section for Detail Payments) -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Transaksi Pembayaran Terakhir</h3>
                        <Link href="/laporan" class="text-sm text-blue-700 font-medium hover:text-blue-800">Lihat Semua Laporan →</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran Untuk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="t in recentTransactions" :key="t.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ t.waktu_transaksi }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ t.pelanggan_nama }}</div>
                                        <div class="text-xs text-gray-500">{{ t.pelanggan_id }} • {{ t.wilayah }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Tagihan {{ t.bulan_bayar_text }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="t.is_tunggakan" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Bayar Tunggakan
                                        </span>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Tagihan Rutin
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-bold">
                                        {{ formatRupiah(t.jumlah_bayar) }}
                                    </td>
                                </tr>
                                <tr v-if="recentTransactions.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada transaksi bulan ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pelanggan Belum Bayar & Distribusi RT/RW -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Pelanggan Belum Bayar -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Pelanggan Belum Bayar</h3>
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">{{ pembayaran.belumBayarCount }}</span>
                        </div>
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            <div v-for="p in pelangganBelumBayar" :key="p.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ p.nama_pelanggan }}</p>
                                    <p class="text-sm text-gray-600">{{ p.id_pelanggan }} • RT {{ p.rt }}/RW {{ p.rw }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a v-if="p.no_whatsapp" :href="waLink(p.no_whatsapp)" target="_blank" class="p-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors" title="Chat WhatsApp">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                    </a>
                                    <Link :href="`/pelanggan/${p.id}/edit`" class="p-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                            <div v-if="pelangganBelumBayar.length === 0" class="text-center text-gray-500 py-8">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="font-semibold">Semua sudah bayar!</p>
                                <p class="text-sm">Tidak ada tunggakan bulan ini</p>
                            </div>
                            <div v-if="pembayaran.belumBayarCount > 10" class="text-center pt-3 border-t">
                                <Link href="/cek-pelanggan" class="text-blue-800 hover:text-blue-900 font-semibold text-sm">
                                    Lihat semua ({{ pembayaran.belumBayarCount }}) →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Distribusi RT/RW -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi per RT/RW</h3>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            <div v-for="item in distribusiRtRw" :key="item.rt_rw" class="flex items-center">
                                <div class="flex-shrink-0 w-20">
                                    <span class="text-sm font-medium text-gray-700">{{ item.rt_rw }}</span>
                                </div>
                                <div class="flex-1 mx-4">
                                    <div class="bg-gray-200 rounded-full h-4">
                                        <div 
                                            class="bg-blue-800 h-4 rounded-full transition-all duration-300"
                                            :style="{ width: `${(item.jumlah / stats.totalPelanggan * 100)}%` }"
                                        ></div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 w-16 text-right">
                                    <span class="text-sm font-semibold text-gray-900">{{ item.jumlah }}</span>
                                </div>
                            </div>
                            <div v-if="distribusiRtRw.length === 0" class="text-center text-gray-500 py-8">
                                Belum ada data distribusi
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Laporan Keuangan Bulanan -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Laporan Keuangan Bulanan</h3>
                            <p class="text-sm text-gray-600 mt-1">Periode: {{ formatBulan(laporanKeuangan.bulan) }}</p>
                        </div>
                        <button 
                            @click="showModalOperasional = true"
                            class="px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 transition flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Update Biaya Operasional
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Total Tarikan -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-5 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-blue-900">Total Tarikan</h4>
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-blue-900">Rp {{ formatRupiah(laporanKeuangan.totalTarikan) }}</p>
                            <p class="text-xs text-blue-700 mt-1">Dari {{ laporanKeuangan.totalSRSudahBayar }} pelanggan</p>
                        </div>

                        <!-- 20% Tarikan -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-5 border border-purple-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-purple-900">20% Tarikan</h4>
                                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-purple-900">Rp {{ formatRupiah(laporanKeuangan.tarik20Persen) }}</p>
                            <p class="text-xs text-purple-700 mt-1">Jasa penarik</p>
                        </div>

                        <!-- Biaya Operasional -->
                        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-5 border border-orange-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-orange-900">Biaya Operasional</h4>
                                <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-orange-900">Rp {{ formatRupiah(laporanKeuangan.biayaOperasionalPenarik) }}</p>
                            <p class="text-xs text-orange-700 mt-1">BBM, dll.</p>
                        </div>

                        <!-- Honor Penarik -->
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-5 border border-indigo-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-indigo-900">Total Honor Penarik</h4>
                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-indigo-900">Rp {{ formatRupiah(laporanKeuangan.honorPenarik) }}</p>
                            <p class="text-xs text-indigo-700 mt-1">20% + Operasional</p>
                        </div>

                        <!-- Total Tarikan Bersih -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-5 border-2 border-blue-300 md:col-span-2">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-base font-semibold text-blue-900">Total Tarikan Bersih</h4>
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <p class="text-3xl font-bold text-blue-900">Rp {{ formatRupiah(laporanKeuangan.totalTarikanBersih) }}</p>
                            <p class="text-sm text-blue-700 mt-1">Sisa untuk kas & operasional KP-SPAMS</p>
                        </div>

                        <!-- SR Stats -->
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-5 border border-gray-200 md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Sambungan Rumah (SR)</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-xs text-gray-600">Sudah Bayar</p>
                                    <p class="text-xl font-bold text-blue-700">{{ laporanKeuangan.totalSRSudahBayar }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Belum Bayar</p>
                                    <p class="text-xl font-bold text-red-700">{{ laporanKeuangan.totalSRBelumBayar }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Total SR Aktif</p>
                                    <p class="text-xl font-bold text-gray-900">{{ laporanKeuangan.totalSR }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Link 
                            href="/cek-pelanggan"
                            class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors"
                        >
                            <div class="p-3 bg-blue-800 rounded-lg text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Data Pelanggan</p>
                                <p class="text-sm text-gray-600">Lihat & kelola data pelanggan</p>
                            </div>
                        </Link>

                        <Link 
                            href="/peta"
                            class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors"
                        >
                            <div class="p-3 bg-blue-600 rounded-lg text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Peta Pelanggan</p>
                                <p class="text-sm text-gray-600">Lihat lokasi pelanggan di peta</p>
                            </div>
                        </Link>

                        <Link 
                            href="/"
                            class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors"
                        >
                            <div class="p-3 bg-purple-600 rounded-lg text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-900">Info KP-SPAMS</p>
                                <p class="text-sm text-gray-600">Informasi & layanan</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Update Biaya Operasional -->
        <div v-if="showModalOperasional" class="fixed inset-0 z-50 overflow-y-auto" @click.self="showModalOperasional = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Update Biaya Operasional Penarik</h3>
                        <button @click="showModalOperasional = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitOperasional">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <input 
                                type="month" 
                                v-model="formOperasional.bulan"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                required
                            />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Operasional (Rp)</label>
                            <input 
                                type="number" 
                                v-model="formOperasional.biaya_operasional_penarik"
                                step="1000"
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                required
                            />
                            <p class="text-xs text-gray-500 mt-1">BBM, maintenance, dll.</p>
                        </div>

                        <div class="flex gap-3">
                            <button 
                                type="submit"
                                :disabled="processing"
                                class="flex-1 px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 transition disabled:opacity-50"
                            >
                                <span v-if="processing">Menyimpan...</span>
                                <span v-else>Simpan</span>
                            </button>
                            <button 
                                type="button"
                                @click="showModalOperasional = false"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    },
    pembayaran: {
        type: Object,
        required: true
    },
    laporanKeuangan: {
        type: Object,
        required: true
    },
    pelangganBelumBayar: {
        type: Array,
        default: () => []
    },
    distribusiRtRw: {
        type: Array,
        default: () => []
    },
    recentTransactions: {
        type: Array,
        default: () => []
    }
});

const showModalOperasional = ref(false);
const processing = ref(false);
const selectedWilayah = ref('');

const formOperasional = ref({
    bulan: props.laporanKeuangan.bulan,
    biaya_operasional_penarik: props.laporanKeuangan.biayaOperasionalPenarik,
    wilayah: selectedWilayah.value || null
});

const reloadDashboard = () => {
    const url = selectedWilayah.value 
        ? `/dashboard?wilayah=${encodeURIComponent(selectedWilayah.value)}`
        : '/dashboard';
    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Update form wilayah after reload
            formOperasional.value.wilayah = selectedWilayah.value || null;
            formOperasional.value.biaya_operasional_penarik = props.laporanKeuangan.biayaOperasionalPenarik;
        }
    });
};

const submitOperasional = () => {
    processing.value = true;
    router.post('/laporan/update-operasional', formOperasional.value, {
        onFinish: () => {
            processing.value = false;
            showModalOperasional.value = false;
        }
    });
};

const aktifPercentage = computed(() => {
    if (props.stats.totalPelanggan === 0) return 0;
    return Math.round((props.stats.pelangganAktif / props.stats.totalPelanggan) * 100);
});

const persenPelangganBayar = computed(() => {
    if (props.stats.pelangganAktif === 0) return 0;
    return Math.round((props.pembayaran.sudahBayarCount / props.stats.pelangganAktif) * 100);
});

const persenPembayaran = computed(() => {
    if (props.pembayaran.totalTagihan === 0) return 0;
    return Math.round((props.pembayaran.terbayar / props.pembayaran.totalTagihan) * 100);
});

const formatRupiah = (angka) => {
    if (!angka && angka !== 0) return '0';
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(angka);
};

const waLink = (no) => {
    if (!no) return '#';
    const normalized = String(no).replace(/[^0-9]/g, '').replace(/^0/, '62');
    return `https://wa.me/${normalized}`;
};

const formatBulan = (bulan) => {
    if (!bulan) return '';
    const [year, month] = bulan.split('-');
    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return `${namaBulan[parseInt(month) - 1]} ${year}`;
};
</script>
