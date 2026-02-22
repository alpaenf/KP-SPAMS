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

                <!-- Cards Container -->
                <div v-if="filteredPembayaranList.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                        <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak Ada Data</h3>
                    <p class="text-gray-500 max-w-xs mx-auto text-sm">Tidak ada data pembayaran ditemukan untuk kriteria pencarian Anda.</p>
                </div>

                <div v-else class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    <div
                        v-for="item in filteredPembayaranList"
                        :key="item.id"
                        class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-blue-200 transition-all duration-300"
                    >
                        <!-- Top Header Component -->
                        <div class="px-5 py-4 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-600">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0 backdrop-blur-sm border border-white/20">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-white tracking-tight text-lg">{{ formatBulan(item.bulan_bayar) }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-blue-100 text-xs font-medium">Bayar: {{ formatTanggalPembayaran(item.tanggal_bayar) }}</span>
                                            <span v-if="item.created_at" class="text-blue-200/60 text-[10px]">• {{ item.created_at.split(' ')[1] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span 
                                        :class="[
                                            'px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase border',
                                            item.keterangan === 'LUNAS' ? 'bg-green-400/20 text-green-100 border-green-300/30' :
                                            item.keterangan === 'CICILAN' ? 'bg-yellow-400/20 text-yellow-100 border-yellow-300/30' :
                                            item.keterangan === 'TUNGGAKAN' ? 'bg-red-400/20 text-red-100 border-red-300/30' :
                                            'bg-white/10 text-white border-white/10'
                                        ]"
                                    >
                                        {{ item.keterangan || '-' }}
                                    </span>
                                    <div class="bg-black/20 text-white/70 px-2 py-0.5 rounded text-[10px] font-mono border border-white/5">#{{ item.id }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-5">
                            <!-- Section: Pelanggan -->
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="w-12 h-12 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold text-lg border-2 border-white shadow-sm">
                                    {{ item.nama_pelanggan ? item.nama_pelanggan.charAt(0) : 'P' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-900 truncate uppercase">{{ item.nama_pelanggan }}</div>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                                        <span class="text-xs text-gray-500 font-medium">🚀 ID: {{ item.id_pelanggan }}</span>
                                        <span class="text-xs text-gray-500">📍 {{ item.wilayah }}</span>
                                        <span v-if="item.kategori" class="text-[10px] px-1.5 py-0.5 rounded bg-purple-50 text-purple-600 font-bold border border-purple-100 uppercase">{{ item.kategori }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- Data Meteran Section -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Meteran Air
                                    </div>
                                    <div class="bg-blue-50/50 rounded-2xl p-4 border border-blue-100/50 relative overflow-hidden group">
                                        <div class="flex items-center justify-between gap-2 relative z-10">
                                            <div class="text-center flex-1 bg-white rounded-xl p-2.5 border border-gray-100 shadow-sm">
                                                <div class="text-[10px] text-gray-400 mb-1 leading-none uppercase tracking-tighter">Awal</div>
                                                <div class="text-lg font-mono font-bold text-gray-700 leading-none tracking-tight">{{ item.meteran_sebelum !== null ? Number(item.meteran_sebelum).toFixed(1) : '-' }}</div>
                                            </div>
                                            
                                            <div class="flex flex-col items-center flex-shrink-0 -mb-1">
                                                <svg class="w-4 h-4 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                                <div class="text-[10px] font-black text-blue-700 bg-blue-100 rounded-full px-1.5 py-0.5 mt-1 border border-blue-200">+{{ item.jumlah_kubik ? Number(item.jumlah_kubik).toFixed(1) : '0' }}</div>
                                            </div>
                                            
                                            <div class="text-center flex-1 bg-white rounded-xl p-2.5 border border-gray-100 shadow-sm">
                                                <div class="text-[10px] text-gray-400 mb-1 leading-none uppercase tracking-tighter">Akhir</div>
                                                <div class="text-lg font-mono font-bold text-gray-700 leading-none tracking-tight">{{ item.meteran_sesudah !== null ? Number(item.meteran_sesudah).toFixed(1) : '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="mt-3 bg-blue-600 rounded-lg py-2 px-3 text-center shadow-md shadow-blue-200">
                                            <span class="text-[10px] text-blue-100 font-semibold uppercase tracking-wider">Pemakaian: </span>
                                            <span class="text-sm font-black text-white px-1 leading-none">{{ item.jumlah_kubik ? Number(item.jumlah_kubik).toFixed(1) + ' m³' : '0 m³' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detail Tagihan Section -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        Rincian Biaya
                                    </div>
                                    <div class="bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                                        <div class="border-b border-gray-100 px-3.5 py-2.5 flex justify-between items-center group">
                                            <div>
                                                <div class="text-[11px] font-bold text-gray-700 flex items-center gap-1.5 leading-none">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div> Air
                                                </div>
                                                <div class="text-[9px] text-gray-400 mt-1 pl-3 leading-none italic" v-if="item.jumlah_kubik && item.tarif_per_kubik">
                                                    {{ Number(item.jumlah_kubik).toFixed(1) }}m³ × Rp {{ Number(item.tarif_per_kubik).toLocaleString('id-ID') }}
                                                </div>
                                            </div>
                                            <div class="text-[11px] font-bold text-gray-900 leading-none">Rp {{ Number(item.biaya_air || 0).toLocaleString('id-ID') }}</div>
                                        </div>
                                        <div class="border-b border-gray-100 px-3.5 py-2.5 flex justify-between items-center bg-white/40">
                                            <div class="text-[11px] font-bold text-gray-700 flex items-center gap-1.5 leading-none">
                                                <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div> Abunemen
                                            </div>
                                            <div class="text-[11px] font-bold" :class="item.abunemen ? 'text-gray-900' : 'text-gray-300 italic font-normal'">
                                                {{ item.abunemen ? 'Rp ' + Number(item.abunemen_nominal || 3000).toLocaleString('id-ID') : 'Tidak Ada' }}
                                            </div>
                                        </div>
                                        <div v-if="item.tunggakan > 0" class="border-b border-red-50 px-3.5 py-2.5 flex justify-between items-center bg-red-50/30">
                                            <div class="text-[11px] font-bold text-red-700 flex items-center gap-1.5 leading-none">
                                                <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div> Tunggakan
                                            </div>
                                            <div class="text-[11px] font-bold text-red-700 leading-none font-mono">Rp {{ Number(item.tunggakan).toLocaleString('id-ID') }}</div>
                                        </div>
                                        <div class="px-3.5 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 flex justify-between items-center">
                                            <span class="text-[10px] font-black text-blue-100 uppercase tracking-widest leading-none">Total</span>
                                            <span class="text-sm font-black text-white leading-none tracking-tight">Rp {{ Number(item.jumlah_bayar).toLocaleString('id-ID') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer: Actions -->
                        <div class="px-5 py-3.5 bg-gray-50 border-t border-gray-200/50 flex flex-wrap gap-2 items-center justify-between">
                            <button
                                @click="openDetailModal(item)"
                                class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-white text-blue-700 text-xs font-bold rounded-xl border border-blue-100 hover:bg-blue-50 hover:border-blue-200 hover:shadow-sm transition-all active:scale-95"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </button>
                            
                            <div class="flex flex-1 sm:flex-none gap-2">
                                <button
                                    @click="downloadPdf(item)"
                                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-green-500 text-white text-xs font-bold rounded-xl hover:bg-green-600 hover:shadow-lg hover:shadow-green-100 transition-all active:scale-95"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    PDF
                                </button>
                                <button
                                    @click="sendReceipt(item)"
                                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 text-white text-xs font-bold rounded-xl hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-100 transition-all active:scale-95"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    WA
                                </button>
                            </div>
                        </div>
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
import axios from 'axios';

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

// Receipt functions
const downloadPdf = (item) => {
    window.open(`/pembayaran/${item.id}/download-pdf`, '_blank');
};

const sendReceipt = async (item) => {
    try {
        const response = await axios.post(`/pembayaran/${item.id}/send-receipt`);
        if (response.data.wa_link) {
            window.open(response.data.wa_link, '_blank');
            alert(`Struk berhasil digenerate!\n\nPelanggan: ${response.data.pelanggan_nama}\nNo. WhatsApp: ${response.data.no_whatsapp}\n\nJendela WhatsApp akan terbuka.`);
        }
    } catch (error) {
        if (error.response?.status === 422) {
            alert(error.response.data.message || 'Nomor WhatsApp pelanggan tidak tersedia');
        } else {
            console.error('Error sending receipt:', error);
            alert('Terjadi kesalahan saat mengirim struk.');
        }
    }
};

</script>
