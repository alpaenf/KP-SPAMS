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

                        <!-- Biaya Operasional -->
                        <div class="bg-orange-50 rounded-xl p-4 sm:p-5 border border-orange-100 cursor-pointer hover:bg-orange-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-orange-900 mb-2">Biaya Operasional</h4>
                             <p class="text-xl sm:text-2xl font-bold text-orange-800">{{ formatRupiah(detail.biayaOperasional) }}</p>
                             <p class="text-xs text-orange-600 mt-1">BBM & Maintenance (Klik untuk edit)</p>
                        </div>

                        <!-- Biaya PAD Desa -->
                        <div class="bg-yellow-50 rounded-xl p-4 sm:p-5 border border-yellow-100 cursor-pointer hover:bg-yellow-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-yellow-900 mb-2">Biaya PAD Desa</h4>
                             <p class="text-xl sm:text-2xl font-bold text-yellow-800">{{ formatRupiah(detail.biayaPadDesa) }}</p>
                             <p class="text-xs text-yellow-600 mt-1">Pendapatan Asli Desa (Klik untuk edit)</p>
                        </div>

                        <!-- Biaya Operasional Lapangan -->
                        <div class="bg-teal-50 rounded-xl p-4 sm:p-5 border border-teal-100 cursor-pointer hover:bg-teal-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-teal-900 mb-2">Biaya Ops. Lapangan</h4>
                             <p class="text-xl sm:text-2xl font-bold text-teal-800">{{ formatRupiah(detail.biayaOperasionalLapangan) }}</p>
                             <p class="text-xs text-teal-600 mt-1">Perbaikan & Lapangan (Klik untuk edit)</p>
                        </div>

                        <!-- Biaya Lain-lain -->
                        <div class="bg-pink-50 rounded-xl p-4 sm:p-5 border border-pink-100 cursor-pointer hover:bg-pink-100 transition" @click="openModalOperasional" title="Klik untuk update">
                             <h4 class="text-xs sm:text-sm font-semibold text-pink-900 mb-2">Biaya Lain-lain</h4>
                             <p class="text-xl sm:text-2xl font-bold text-pink-800">{{ formatRupiah(detail.biayaLainLain) }}</p>
                             <p class="text-xs text-pink-600 mt-1">Keperluan lainnya (Klik untuk edit)</p>
                        </div>

                         <!-- Total Honor -->
                        <div class="bg-indigo-50 rounded-xl p-4 sm:p-5 border border-indigo-100">
                             <h4 class="text-xs sm:text-sm font-semibold text-indigo-900 mb-2">Total Honor Penarik</h4>
                             <p class="text-xl sm:text-2xl font-bold text-indigo-800">{{ formatRupiah(detail.honorPenarik) }}</p>
                             <p class="text-xs text-indigo-600 mt-1">20% + Operasional</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mt-4 sm:mt-6">
                        <!-- Total Tarikan Bersih -->
                        <!-- Total Tarikan Bersih -->
                        <div class="bg-blue-50 rounded-xl p-4 sm:p-6 border border-blue-200 flex flex-col justify-center">
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="text-sm sm:text-base font-bold text-blue-900">Total Tarikan Bersih (Kas KP-SPAMS)</h4>
                                <button 
                                    @click="showAccumulation = !showAccumulation"
                                    class="text-xs px-2 py-1 rounded border transition flex-shrink-0 ml-2"
                                    :class="showAccumulation ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-blue-600 border-blue-300 hover:bg-blue-50'"
                                    title="Klik untuk melihat akumulasi dari bulan sebelumnya"
                                >
                                    {{ showAccumulation ? 'Akumulasi ON' : 'Akumulasi OFF' }}
                                </button>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold text-blue-700">{{ formatRupiah(totalBersihDisplay) }}</p>
                            <div class="text-xs sm:text-sm text-blue-600 mt-2">
                                <p>Dana bersih untuk kas desa/KP-SPAMS setelah dikurangi honor.</p>
                                <p v-if="showAccumulation" class="mt-1 font-medium bg-blue-100 p-1 rounded inline-block">
                                    Termasuk Saldo Awal: {{ formatRupiah(detail.saldoAwal || 0) }}
                                </p>
                            </div>
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
                </div>
                <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-700">Rincian Transaksi</h3>
                        <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-full px-3 py-1">Menampilkan {{ data.from }}-{{ data.to }} dari {{ data.total }} data</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bayar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wilayah</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian (m³)</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="data.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p>Tidak ada data pembayaran ditemukan untuk periode ini.</p>
                                    </td>
                                </tr>
                                <tr v-for="item in data.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
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
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <Pagination :links="data.links" />
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

                        <div class="mb-4">
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

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Operasional Lapangan (Rp)</label>
                            <input 
                                type="number" 
                                v-model="formOperasional.biaya_operasional_lapangan"
                                step="1000"
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <p class="text-xs text-gray-500 mt-1">Perbaikan, bahan baku, dll.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Biaya Lain-lain (Rp)</label>
                            <input 
                                type="number" 
                                v-model="formOperasional.biaya_lain_lain"
                                step="1000"
                                min="0"
                                placeholder="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <p class="text-xs text-gray-500 mt-1">Keperluan lainnya</p>
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
import Pagination from '@/Components/Pagination.vue';
import { ref, watch, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';

const props = defineProps({
    data: Object,
    summary: Object,
    detail: Object,
    filters: Object,
    options: Object,
});

const showAccumulation = ref(false);

const totalBersihDisplay = computed(() => {
    if (!props.detail) return 0;
    
    let total = props.detail.totalTarikanBersih;
    
    // Convert to number just in case
    total = Number(total) || 0;
    
    if (showAccumulation.value) {
        let saldoAwal = Number(props.detail.saldoAwal) || 0;
        total += saldoAwal;
    }
    
    return total;
});

const form = ref({
    tahun: props.filters.tahun,
    bulan: props.filters.bulan,
    wilayah: props.filters.wilayah,
});

// Modal Logic
const showModalOperasional = ref(false);
const processing = ref(false);
const formOperasional = reactive({
    bulan: '',
    biaya_operasional_penarik: 0,
    biaya_pad_desa: 0,
    biaya_operasional_lapangan: 0,
    biaya_lain_lain: 0,
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
    formOperasional.biaya_operasional_lapangan = props.detail ? props.detail.biayaOperasionalLapangan : 0;
    formOperasional.biaya_lain_lain = props.detail ? props.detail.biayaLainLain : 0;
    formOperasional.wilayah = form.value.wilayah !== 'semua' ? form.value.wilayah : null; // Use filter wilayah if exists
    
    showModalOperasional.value = true;
};

const submitOperasional = () => {
    processing.value = true;
    router.post('/laporan/update-operasional', formOperasional, {
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
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
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
    router.get('/laporan', form.value, {
        preserveState: true,
        preserveScroll: true,
        only: ['data', 'summary', 'detail', 'filters'],
    });
}, 300), { deep: true });

</script>
