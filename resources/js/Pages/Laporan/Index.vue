<template>
    <AppLayout>
        <div class="py-8 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-6 sm:mb-8">
                    <div class="mb-4">
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Laporan Keuangan & Operasional</h2>
                        <p class="text-sm sm:text-base text-gray-500 mt-1">Rekapitulasi pembayaran dan aktivitas pelanggan.</p>
                    </div>
                    
                    <!-- Export Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button 
                            @click="exportExcel"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-blue-600 border border-blue-700 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2 shadow-sm font-medium transition text-sm"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="hidden sm:inline">Export </span>Excel
                        </button>
                        <button 
                            @click="exportPdf"
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-red-600 border border-red-700 text-white rounded-lg hover:bg-red-700 flex items-center justify-center gap-2 shadow-sm font-medium transition text-sm"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="hidden sm:inline">Export </span>PDF
                        </button>
                        <button 
                            onclick="window.print()" 
                            class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2 shadow-sm font-medium transition text-sm"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Cetak
                        </button>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6 sm:mb-8">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Filter Laporan
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6">
                        <!-- Tahun Filter -->
                        <div>
                            <label for="tahun" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select 
                                id="tahun" 
                                v-model="form.tahun"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            >
                                <option v-for="year in options.tahun" :key="year" :value="year">
                                    {{ year }}
                                </option>
                            </select>
                        </div>

                        <!-- Bulan Filter -->
                        <div>
                            <label for="bulan" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select 
                                id="bulan" 
                                v-model="form.bulan"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            >
                                <option value="semua">Semua Bulan</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <!-- Wilayah Filter (Hidden for penarik) -->
                        <div v-if="$page.props.auth.user.role !== 'penarik'">
                            <label for="wilayah" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Wilayah / RW</label>
                            <select 
                                id="wilayah" 
                                v-model="form.wilayah"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                            >
                                <option value="semua">Semua Wilayah</option>
                                <option v-for="area in options.wilayah" :key="area" :value="area">
                                    {{ area }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 sm:p-6 text-white shadow-lg relative overflow-hidden group">
                        <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4">
                            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> 
                        </div>
                        <h4 class="text-blue-50 font-medium text-xs sm:text-sm uppercase tracking-wider mb-2">Total Pemasukan</h4>
                        <p class="text-2xl sm:text-3xl font-bold">{{ formatRupiah(summary.pemasukan) }}</p>
                        <p class="text-xs text-blue-100 mt-2 opacity-80">
                            {{ form.bulan !== 'semua' ? 'Bulan ini' : 'Akumulasi Tahun ' + form.tahun }}
                        </p>
                        <!-- Breakdown Kategori -->
                        <div class="mt-3 pt-3 border-t border-blue-400 border-opacity-30">
                            <div class="flex justify-between text-xs text-blue-50 mb-1">
                                <span>Umum:</span>
                                <span class="font-semibold">{{ formatRupiah(summary.pemasukanUmum || 0) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-blue-50">
                                <span>Sosial:</span>
                                <span class="font-semibold">{{ formatRupiah(summary.pemasukanSosial || 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm hover:shadow-md transition relative overflow-hidden group">
                        <div class="absolute right-4 top-4 bg-blue-50 text-blue-600 p-2 sm:p-3 rounded-full">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <h4 class="text-gray-500 font-medium text-xs sm:text-sm uppercase tracking-wider mb-2">Total Pemakaian Air</h4>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ summary.kubikasi }} <span class="text-base sm:text-lg text-gray-500 font-normal">m³</span></p>
                    </div>

                    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm hover:shadow-md transition relative overflow-hidden group">
                        <div class="absolute right-4 top-4 bg-purple-50 text-purple-600 p-2 sm:p-3 rounded-full">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="text-gray-500 font-medium text-xs sm:text-sm uppercase tracking-wider mb-2">Total Transaksi</h4>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ summary.transaksi }}</p>
                        <!-- Breakdown Kategori -->
                        <div class="mt-2 text-xs text-gray-600">
                            <div class="flex justify-between mb-1">
                                <span>Umum:</span>
                                <span class="font-semibold">{{ summary.transaksiUmum || 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sosial:</span>
                                <span class="font-semibold">{{ summary.transaksiSosial || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Financial Report (New) -->
                <div v-if="detail" class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">Rincian Keuangan & Operasional</h3>
                         <button 
                            @click="openModalOperasional"
                            class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-sm text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Update Biaya Ops
                        </button>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <!-- Total Tarikan -->
                        <div class="bg-blue-50 rounded-xl p-4 sm:p-5 border border-blue-100">
                             <h4 class="text-xs sm:text-sm font-semibold text-blue-900 mb-2">Total Tarikan (Kotor)</h4>
                             <p class="text-xl sm:text-2xl font-bold text-blue-800">{{ formatRupiah(detail.totalTarikan) }}</p>
                             <p class="text-xs text-blue-600 mt-1">Dari {{ detail.srSudahBayar }} pelanggan bayar</p>
                        </div>

                         <!-- 20% Jasa Penarik -->
                        <div class="bg-purple-50 rounded-xl p-4 sm:p-5 border border-purple-100">
                             <h4 class="text-xs sm:text-sm font-semibold text-purple-900 mb-2">20% Jasa Penarik</h4>
                             <p class="text-xl sm:text-2xl font-bold text-purple-800">{{ formatRupiah(detail.tarik20Persen) }}</p>
                             <p class="text-xs text-purple-600 mt-1">Hak Petugas</p>
                        </div>

                        <!-- Biaya Operasional Penarik -->
                        <div class="bg-orange-50 rounded-xl p-4 sm:p-5 border border-orange-100 relative group">
                             <h4 class="text-xs sm:text-sm font-semibold text-orange-900 mb-2">Biaya Ops. Penarik</h4>
                             <p class="text-xl sm:text-2xl font-bold text-orange-800">{{ formatRupiah(detail.biayaOperasional) }}</p>
                             <p class="text-xs text-orange-600 mt-1">BBM & Maintenance (Klik untuk edit)</p>
                             
                             <!-- Edit Button for Penarik -->
                             <button 
                                v-if="$page.props.auth.user.role === 'penarik'"
                                @click="openModalOperasional"
                                class="absolute top-3 right-3 p-2 bg-orange-200 hover:bg-orange-300 text-orange-800 rounded-lg transition opacity-0 group-hover:opacity-100"
                                title="Edit biaya operasional"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Total Honor Penarik -->
                        <div class="bg-indigo-50 rounded-xl p-4 sm:p-5 border border-indigo-100">
                             <h4 class="text-xs sm:text-sm font-semibold text-indigo-900 mb-2">Total Honor Penarik</h4>
                             <p class="text-xl sm:text-2xl font-bold text-indigo-800">{{ formatRupiah(detail.honorPenarik) }}</p>
                             <p class="text-xs text-indigo-600 mt-1">20% + Ops Penarik</p>
                        </div>

                        <!-- Biaya Ops. Lapangan -->
                        <div class="bg-teal-50 rounded-xl p-4 sm:p-5 border border-teal-100 cursor-pointer hover:bg-teal-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-teal-900 mb-2">Biaya Ops. Lapangan</h4>
                             <p class="text-xl sm:text-2xl font-bold text-teal-800">{{ formatRupiah(detail.biayaOpsLapangan) }}</p>
                             <p class="text-xs text-teal-600 mt-1">Operasional lapangan (Klik untuk edit)</p>
                        </div>

                        <!-- Biaya PAD Desa (Hidden for Penarik) -->
                        <div v-if="$page.props.auth.user.role !== 'penarik'" class="bg-yellow-50 rounded-xl p-4 sm:p-5 border border-yellow-100 cursor-pointer hover:bg-yellow-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-yellow-900 mb-2">Biaya PAD Desa</h4>
                             <p class="text-xl sm:text-2xl font-bold text-yellow-800">{{ formatRupiah(detail.biayaPadDesa) }}</p>
                             <p class="text-xs text-yellow-600 mt-1">Pendapatan Asli Desa (Klik untuk edit)</p>
                        </div>

                        <!-- Biaya Lain-lain (Hidden for Penarik) -->
                        <div v-if="$page.props.auth.user.role !== 'penarik'" class="bg-pink-50 rounded-xl p-4 sm:p-5 border border-pink-100 cursor-pointer hover:bg-pink-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-pink-900 mb-2">Biaya Lain-lain</h4>
                             <p class="text-xl sm:text-2xl font-bold text-pink-800">{{ formatRupiah(detail.biayaLainLain) }}</p>
                             <p class="text-xs text-pink-600 mt-1">Biaya lain-lain (Klik untuk edit)</p>
                        </div>

                        <!-- Biaya CSR (Hidden for Penarik) -->
                        <div v-if="$page.props.auth.user.role !== 'penarik'" class="bg-purple-50 rounded-xl p-4 sm:p-5 border border-purple-100 cursor-pointer hover:bg-purple-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-purple-900 mb-2">Biaya CSR</h4>
                             <p class="text-xl sm:text-2xl font-bold text-purple-800">{{ formatRupiah(detail.biayaCSR || 0) }}</p>
                             <p class="text-xs text-purple-600 mt-1">Corporate Social  Responsibility (Klik untuk edit)</p>
                        </div>

                        <!-- Total Semua Biaya (Hidden for Penarik) -->
                        <div v-if="$page.props.auth.user.role !== 'penarik'" class="bg-red-50 rounded-xl p-4 sm:p-5 border border-red-100">
                             <h4 class="text-xs sm:text-sm font-semibold text-red-900 mb-2">Total Semua Biaya</h4>
                             <p class="text-xl sm:text-2xl font-bold text-red-800">{{ formatRupiah((Number(detail?.honorPenarik) || 0) + (Number(detail?.biayaOpsLapangan) || 0) + (Number(detail?.biayaPadDesa) || 0) + (Number(detail?.biayaLainLain) || 0) + (Number(detail?.biayaCSR) || 0)) }}</p>
                             <p class="text-xs text-red-600 mt-1">Honor + Ops + PAD + Lain-lain + CSR</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                        <!-- Total Tarikan Bersih -->
                        <div class="bg-blue-50 rounded-xl p-4 sm:p-6 border border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm sm:text-base font-bold text-blue-900">Total Tarikan Bersih (Kas KP-SPAMS)</h4>
                                <button 
                                    v-if="form.bulan !== 'semua'"
                                    @click="toggleAkumulasi"
                                    :class="[
                                        'px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200',
                                        showAkumulasi 
                                            ? 'bg-blue-600 text-white hover:bg-blue-700' 
                                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                    ]"
                                    title="Toggle akumulasi bulan sebelumnya"
                                >
                                    {{ showAkumulasi ? 'Akumulasi ON' : 'Akumulasi OFF' }}
                                </button>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold text-blue-700">{{ formatRupiah(detail.totalTarikanBersih) }}</p>
                            <p class="text-xs sm:text-sm text-blue-600 mt-2">
                                {{ showAkumulasi && form.bulan !== 'semua' ? 'Dana bersih sampai bulan ini (akumulasi)' : 'Dana bersih untuk kas desa/KP-SPAMS setelah dikurangi honor dan biaya lain.' }}
                            </p>
                        </div>

                        <!-- SR Status -->
                        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-800 mb-4">Status Sambungan Rumah (SR)</h4>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-gray-800">{{ detail.totalSR }}</div>
                                    <div class="text-xs text-gray-500 uppercase font-semibold">Total SR</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-blue-600">{{ detail.srSudahBayar }}</div>
                                    <div class="text-xs text-gray-500 uppercase font-semibold">Sudah Bayar</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-red-500">{{ detail.srBelumBayar }}</div>
                                    <div class="text-xs text-gray-500 uppercase font-semibold">Belum Bayar</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistik Kategori Pelanggan (NEW) -->
                    <div class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm mt-4 sm:mt-6">
                        <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Rincian Pembayaran per Kategori Pelanggan
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pelanggan Umum -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-green-900 flex items-center">
                                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                        Pelanggan UMUM
                                    </span>
                                    <span class="px-2 py-1 bg-green-200 text-green-800 text-xs font-bold rounded-full">
                                        {{ summary.pelangganUmum || 0 }} bangunan
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-green-800 mb-1">
                                    {{ formatRupiah(summary.pemasukanUmum || 0) }}
                                </div>
                                <div class="flex justify-between items-center text-xs text-green-700 mb-2">
                                    <span>{{ ((summary.pemasukanUmum || 0) / (summary.pemasukan || 1) * 100).toFixed(1) }}% dari total</span>
                                    <span class="font-semibold">{{ summary.transaksiUmum || 0 }} transaksi</span>
                                </div>
                                <div class="text-xs text-green-600 bg-green-50 rounded px-2 py-1">
                                    Rata-rata: {{ formatRupiah((summary.pemasukanUmum || 0) / (summary.pelangganUmum || 1)) }}/bangunan
                                </div>
                            </div>
                            
                            <!-- Pelanggan Sosial -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm font-semibold text-blue-900 flex items-center">
                                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                        Pelanggan SOSIAL
                                    </span>
                                    <span class="px-2 py-1 bg-blue-200 text-blue-800 text-xs font-bold rounded-full">
                                        {{ summary.pelangganSosial || 0 }} bangunan
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-blue-800 mb-1">
                                    {{ formatRupiah(summary.pemasukanSosial || 0) }}
                                </div>
                                <div class="flex justify-between items-center text-xs text-blue-700 mb-2">
                                    <span>{{ ((summary.pemasukanSosial || 0) / (summary.pemasukan || 1) * 100).toFixed(1) }}% dari total</span>
                                    <span class="font-semibold">{{ summary.transaksiSosial || 0 }} transaksi</span>
                                </div>
                                <div class="text-xs text-blue-600 bg-blue-50 rounded px-2 py-1">
                                    Rata-rata: {{ formatRupiah((summary.pemasukanSosial || 0) / (summary.pelangganSosial || 1)) }}/bangunan
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="text-left">
                                    <span class="text-gray-600 font-medium">Total Bangunan:</span>
                                    <div class="font-bold text-gray-900 mt-1">
                                        {{ (summary.pelangganUmum || 0) + (summary.pelangganSosial || 0) }} bangunan
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        ({{ summary.pelangganUmum || 0 }} umum + {{ summary.pelangganSosial || 0 }} sosial)
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-gray-600 font-medium">Total Pemasukan:</span>
                                    <div class="font-bold text-gray-900 mt-1">{{ formatRupiah(summary.pemasukan) }}</div>
                                    <div class="text-xs text-gray-500">dari {{ summary.transaksi }} transaksi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Distribusi Tunggakan per Wilayah -->
                <div v-if="distribusiWilayah && distribusiWilayah.length > 0" class="bg-white rounded-xl p-4 sm:p-6 border border-gray-200 shadow-sm mt-6">
                    <h4 class="text-sm font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Tunggakan per Wilayah
                    </h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Wilayah</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total SR</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Sudah Bayar</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Belum Bayar</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Tunggakan</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template v-for="(wilayah, index) in distribusiWilayah" :key="index">
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ wilayah.wilayah }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700 text-right">{{ wilayah.jumlah }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                {{ wilayah.sudah_bayar }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                {{ wilayah.belum_bayar }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold',
                                                wilayah.tunggakan > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600'
                                            ]">
                                                {{ wilayah.tunggakan }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold',
                                                ((wilayah.sudah_bayar / wilayah.jumlah) * 100) >= 80 ? 'bg-green-100 text-green-800' :
                                                ((wilayah.sudah_bayar / wilayah.jumlah) * 100) >= 50 ? 'bg-yellow-100 text-yellow-800' :
                                                'bg-red-100 text-red-800'
                                            ]">
                                                {{ ((wilayah.sudah_bayar / wilayah.jumlah) * 100).toFixed(0) }}%
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button 
                                                v-if="wilayah.tunggakan > 0 && wilayah.detail_tunggakan && wilayah.detail_tunggakan.length > 0"
                                                @click="toggleDetail(wilayah.wilayah)"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium rounded hover:bg-gray-100 transition"
                                                :class="expandedWilayah[wilayah.wilayah] ? 'text-blue-700 bg-blue-50' : 'text-gray-700'"
                                            >
                                                <svg :class="['w-4 h-4 transition-transform', expandedWilayah[wilayah.wilayah] ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                <span class="ml-1">Detail</span>
                                            </button>
                                            <span v-else class="text-xs text-gray-400">-</span>
                                        </td>
                                    </tr>
                                    <!-- Detail Tunggakan Row (Expandable) -->
                                    <tr v-if="expandedWilayah[wilayah.wilayah] && wilayah.detail_tunggakan && wilayah.detail_tunggakan.length > 0" class="bg-red-50">
                                        <td colspan="7" class="px-4 py-4">
                                            <div class="space-y-2">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <h4 class="text-sm font-bold text-red-900">Detail Pelanggan yang Menunggak di {{ wilayah.wilayah }}</h4>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full divide-y divide-red-200">
                                                        <thead class="bg-red-100">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left text-xs font-semibold text-red-900">No</th>
                                                                <th class="px-3 py-2 text-left text-xs font-semibold text-red-900">ID Pelanggan</th>
                                                                <th class="px-3 py-2 text-left text-xs font-semibold text-red-900">Nama Pelanggan</th>
                                                                <th class="px-3 py-2 text-right text-xs font-semibold text-red-900">Jumlah Bulan</th>
                                                                <th class="px-3 py-2 text-right text-xs font-semibold text-red-900">Total Tunggakan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-red-100 bg-white">
                                                            <tr v-for="(detail, idx) in wilayah.detail_tunggakan" :key="idx" class="hover:bg-red-50">
                                                                <td class="px-3 py-2 text-xs text-gray-700">{{ idx + 1 }}</td>
                                                                <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ detail.id_pelanggan }}</td>
                                                                <td class="px-3 py-2 text-xs text-gray-900">{{ detail.nama_pelanggan }}</td>
                                                                <td class="px-3 py-2 text-xs text-right">
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                                        {{ detail.jumlah_bulan }} bulan
                                                                    </span>
                                                                </td>
                                                                <td class="px-3 py-2 text-xs text-right font-semibold text-red-700"> {{ formatRupiah(detail.total_tunggakan) }}</td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot class="bg-red-100">
                                                            <tr>
                                                                <td colspan="4" class="px-3 py-2 text-xs font-bold text-red-900 text-right">TOTAL:</td>
                                                                <td class="px-3 py-2 text-xs font-bold text-red-900 text-right">
                                                                     {{ formatRupiah(wilayah.detail_tunggakan.reduce((sum, d) => sum + Number(d.total_tunggakan || 0), 0)) }}
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr class="bg-gray-100 font-semibold">
                                    <td class="px-4 py-3 text-sm text-gray-900">TOTAL</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ distribusiWilayah.reduce((sum, w) => sum + Number(w.jumlah), 0) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ distribusiWilayah.reduce((sum, w) => sum + Number(w.sudah_bayar), 0) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ distribusiWilayah.reduce((sum, w) => sum + Number(w.belum_bayar), 0) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ distribusiWilayah.reduce((sum, w) => sum + Number(w.tunggakan), 0) }}</td>
                                    <td class="px-4 py-3 text-center">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-xs text-blue-800">
                                <p class="font-semibold mb-1">Keterangan:</p>
                                <ul class="list-disc list-inside space-y-1 pl-2">
                                    <li><span class="font-semibold">Sudah Bayar:</span> Pelanggan yang sudah membayar bulan ini</li>
                                    <li><span class="font-semibold">Belum Bayar:</span> Pelanggan yang belum membayar bulan ini</li>
                                    <li><span class="font-semibold">Tunggakan:</span> Jumlah tagihan bulan sebelumnya yang belum dibayar</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                            <h3 class="font-bold text-gray-700">Rincian Transaksi</h3>
                            <div class="flex flex-wrap items-center gap-2 text-xs">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full font-semibold">
                                    🟢 Umum: {{ summary.pelangganUmum || 0 }} bangunan ({{ summary.transaksiUmum || 0 }}×)
                                </span>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-semibold">
                                    🔵 Sosial: {{ summary.pelangganSosial || 0 }} bangunan ({{ summary.transaksiSosial || 0 }}×)
                                </span>
                                <span class="bg-white border border-gray-200 text-gray-600 px-2 py-1 rounded-full font-medium">
                                    Total: {{ data.length }} data
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian (m³)</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="data.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p>Tidak ada data pembayaran ditemukan untuk periode ini.</p>
                                    </td>
                                </tr>
                                <tr v-for="item in data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ formatDate(item.tanggal_bayar) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ item.pelanggan?.nama_pelanggan || 'Pelanggan Terhapus' }}</div>
                                        <div class="text-xs text-gray-500">{{ item.pelanggan?.id_pelanggan }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span v-if="item.pelanggan?.rt">RT {{ item.pelanggan.rt }}</span>
                                        <span v-if="item.pelanggan?.rw"> / RW {{ item.pelanggan.rw }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="(item.pelanggan?.kategori || 'umum').toLowerCase().trim() === 'sosial'" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Sosial</span>
                                        <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Umum</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ item.bulan_bayar }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        {{ item.jumlah_kubik }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-700 text-right">
                                        {{ formatRupiah(item.jumlah_bayar) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                         <div class="mb-4 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                            <p class="text-xs text-yellow-800">Note: Biaya operasional akan diterapkan untuk bulan yang dipilih.</p>
                        </div>

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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Operasional Penarik (Rp)</label>
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

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Ops. Lapangan (Rp)</label>
                            <input 
                                type="number" 
                                v-model="formOperasional.biaya_operasional_lapangan"
                                step="1000"
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <p class="text-xs text-gray-500 mt-1">Biaya operasional lapangan</p>
                        </div>

                        <div v-if="$page.props.auth.user.role === 'admin'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya PAD Desa (Rp)</label>
                            <input 
                                type="number" 
                                v-model="formOperasional.biaya_pad_desa"
                                step="1000"
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <p class="text-xs text-gray-500 mt-1">Pendapatan Asli Desa</p>
                        </div>

                        <div v-if="$page.props.auth.user.role === 'admin'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Lain-lain (Rp)</label>
                            <input 
                                type="number" 
                                v-model="formOperasional.biaya_lain_lain"
                                step="1000"
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <p class="text-xs text-gray-500 mt-1">Biaya lain-lain</p>
                        </div>

                        <div v-if="$page.props.auth.user.role === 'admin'" class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya CSR (Rp)</label>
                            <input 
                                type="number" 
                                v-model="formOperasional.biaya_csr"
                                step="1000"
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <p class="text-xs text-gray-500 mt-1">Corporate Social Responsibility</p>
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
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, reactive } from 'vue'; // Added reactive
import { router, usePage } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

const { props: $page } = usePage();

const props = defineProps({
    data: Array,
    summary: Object,
    detail: Object,
    filters: Object,
    options: Object,
    distribusiWilayah: Array,
});

const form = ref({
    tahun: props.filters.tahun,
    bulan: props.filters.bulan,
    wilayah: props.filters.wilayah,
});

// State untuk expand/collapse detail tunggakan
const expandedWilayah = ref({});

const toggleDetail = (wilayahName) => {
    expandedWilayah.value[wilayahName] = !expandedWilayah.value[wilayahName];
};

// Toggle Akumulasi (untuk menampilkan data bulan sebelumnya juga)
const showAkumulasi = ref(false);

const toggleAkumulasi = () => {
    showAkumulasi.value = !showAkumulasi.value;
    // Reload data dengan parameter akumulasi
    reloadData();
};

const reloadData = () => {
    const params = {
        ...form.value,
        akumulasi: showAkumulasi.value ? '1' : '0'
    };
    
    router.get('/laporan', params, {
        preserveState: true,
        preserveScroll: true,
        only: ['data', 'summary', 'detail', 'filters'],
    });
};

// Modal Logic
const showModalOperasional = ref(false);
const processing = ref(false);
const formOperasional = reactive({
    bulan: '',
    biaya_operasional_penarik: 0,
    biaya_pad_desa: 0,
    biaya_operasional_lapangan: 0,
    biaya_lain_lain: 0,
    biaya_csr: 0,
    wilayah: ''
});

const openModalOperasional = () => {
    // Determine month string YYYY-MM
    let year = form.value.tahun;
    let month = form.value.bulan;
    
    if (month === 'semua') {
        const now = new Date();
        month = String(now.getMonth() + 1).padStart(2, '0');
        if (year != now.getFullYear()) month = '01'; // Default to Jan if filtering past year
    }
    
    formOperasional.bulan = `${year}-${month}`;
    formOperasional.biaya_operasional_penarik = props.detail ? props.detail.biayaOperasional : 0;
    formOperasional.biaya_pad_desa = props.detail ? props.detail.biayaPadDesa : 0;
    formOperasional.biaya_operasional_lapangan = props.detail ? props.detail.biayaOpsLapangan : 0;
    formOperasional.biaya_lain_lain = props.detail ? props.detail.biayaLainLain : 0;
    formOperasional.biaya_csr = props.detail ? (props.detail.biayaCSR || 0) : 0;
    formOperasional.wilayah = form.value.wilayah !== 'semua' ? form.value.wilayah : null; // Use filter wilayah if exists
    
    showModalOperasional.value = true;
};

const submitOperasional = () => {
    processing.value = true;

    // Bangun payload — penarik hanya boleh kirim field yang diizinkan
    const user = $page.props.auth.user;
    const payload = {
        bulan: formOperasional.bulan,
        biaya_operasional_penarik: formOperasional.biaya_operasional_penarik,
        biaya_operasional_lapangan: formOperasional.biaya_operasional_lapangan,
        wilayah: formOperasional.wilayah,
    };

    // Field sensitif hanya dikirim jika admin
    if (user.role === 'admin') {
        payload.biaya_pad_desa = formOperasional.biaya_pad_desa;
        payload.biaya_lain_lain = formOperasional.biaya_lain_lain;
        payload.biaya_csr = formOperasional.biaya_csr;
    }

    router.post('/laporan/update-operasional', payload, {
        preserveScroll: true,
        onSuccess: () => {
            showModalOperasional.value = false;
        },
        onFinish: () => {
             processing.value = false;
        }
    });
};

const formatRupiah = (value) => {
    // Handle NaN, null, undefined
    const numValue = Number(value);
    if (isNaN(numValue) || !isFinite(numValue)) {
        return 'Rp 0';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(numValue);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const exportExcel = () => {
    const params = new URLSearchParams({
        tahun: form.value.tahun,
        bulan: form.value.bulan,
        wilayah: form.value.wilayah
    });
    window.location.href = `/laporan/export-excel?${params.toString()}`;
};

const exportPdf = () => {
    const params = new URLSearchParams({
        tahun: form.value.tahun,
        bulan: form.value.bulan,
        wilayah: form.value.wilayah
    });
    window.location.href = `/laporan/export-pdf?${params.toString()}`;
};

// Watch for changes and reload data
watch(form, debounce(() => {
    reloadData();
}, 300), { deep: true });

</script>
