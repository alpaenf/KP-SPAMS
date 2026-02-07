<template>
    <AppLayout>
        <!-- Hero Section with Background Image -->
        <div class="relative min-h-[85vh] flex items-center justify-center overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <video 
                    autoplay 
                    loop 
                    muted 
                    playsinline 
                    class="w-full h-full object-cover"
                >
                    <source src="/images/video1.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 via-blue-900/60 to-white"></div>
            </div>

            <!-- Hero Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center pt-20">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 tracking-tight text-shadow-lg animate-fade-in-up" style="line-height: 1.2;">
                    KP-SPAMS<br class="md:hidden" />
                    <span class="text-blue-300">
                        "<span ref="typingText" class="typing-text-wrapper"></span><span class="typing-cursor">|</span>"
                    </span>
                </h1>
                <p class="text-xl md:text-2xl text-blue-50 mb-10 max-w-3xl mx-auto font-light text-shadow animate-fade-in-up" style="animation-delay: 0.2s;">
                    Mewujudkan akses air bersih yang berkelanjutan untuk kesehatan dan kesejahteraan masyarakat desa.
                </p>
                
                <!-- Search Box (Glassmorphism) -->
                <div class="max-w-2xl mx-auto glass-card rounded-2xl p-8 transform transition-all duration-300 hover:scale-[1.01] active:scale-[0.99] touch-manipulation animate-fade-in-up" style="animation-delay: 0.4s;">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Cek Data Pelanggan</h2>
                        <p class="text-gray-600 text-sm">Masukkan ID Pelanggan Anda untuk melihat tagihan dan riwayat</p>
                    </div>

                    <form @submit.prevent="cekPelanggan" class="space-y-4">
                        <div class="relative">
                            <input
                                type="text"
                                id="id_pelanggan"
                                v-model="form.id_pelanggan"
                                placeholder="Contoh: PLG001"
                                class="w-full pl-12 pr-4 py-4 bg-white/80 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-lg shadow-inner placeholder:text-gray-400"
                                required
                            />
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 bg-gradient-to-r from-blue-700 to-blue-600 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-blue-500/30 hover:to-blue-500 transform transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex justify-center items-center gap-2"
                        >
                            <span v-if="!form.processing">Cek Tagihan Saya</span>
                            <span v-else class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sedang Memproses...
                            </span>
                        </button>

                        <p v-if="error" class="text-red-600 text-sm font-medium bg-red-50 p-3 rounded-lg border border-red-100 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ error }}
                        </p>
                    </form>
                </div>
            </div>
            
            <!-- Floating Stats (Decorative) -->
            <div class="absolute bottom-0 w-full bg-white/10 backdrop-blur-sm border-t border-white/10 py-6 hidden md:block">
                 <div class="max-w-7xl mx-auto px-6 grid grid-cols-3 gap-8 text-white text-center">
                    <div class="animate-float" style="animation-delay: 0s;">
                        <div class="text-3xl font-bold">{{ stats.totalPelanggan }}</div>
                        <div class="text-sm opacity-80">Total Pelanggan</div>
                    </div>
                    <div class="animate-float" style="animation-delay: 1s;">
                        <div class="text-3xl font-bold">{{ stats.pelangganAktif }}</div>
                        <div class="text-sm opacity-80">Pelanggan Aktif</div>
                    </div>
                    <div class="animate-float" style="animation-delay: 2s;">
                        <div class="text-3xl font-bold">{{ stats.cakupanArea }}</div>
                        <div class="text-sm opacity-80">Cakupan Area</div>
                    </div>
                 </div>
            </div>
        </div>

        <!-- Result Section (Conditional) -->
        <section v-if="pelanggan" id="result" class="py-12 bg-gray-50 scroll-mt-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-blue-800 to-blue-700 text-white px-8 py-6 flex justify-between items-center">
                        <h3 class="text-2xl font-bold flex items-center gap-3">
                            <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Informasi Pelanggan
                        </h3>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">
                                {{ pelanggan.id_pelanggan }}
                            </span>
                            <a :href="`/download-pdf/${pelanggan.id_pelanggan}`" target="_blank" class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded-full text-sm font-medium backdrop-blur-sm transition flex items-center gap-1" title="Unduh Bukti PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                PDF
                            </a>
                            <a :href="`/pelanggan/${pelanggan.id}/download-qr`" target="_blank" class="px-3 py-1 bg-white/20 hover:bg-white/30 rounded-full text-sm font-medium backdrop-blur-sm transition flex items-center gap-1" title="Unduh QR Code">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h-4v-2h-2v4h6v-2h2v-2zm-6 8h2v2h-2v-2zm-2-2h2v2h-2v-2zm-2 2h2v2H2v-2zm2-2h2v2H4v-2zm2 2h2v2H6v-2zm2 2h2v2H8v-2zm-2-4h2v2H6v-2zm-2-2h2v2H4v-2zm4-2v2H6v-2h2zm-2-2h2v2H6v-2zm4 2v2H8v-2h2zm-2-2h2v2H8v-2zm2-2h2v2h-2V4zm2 2v2h-2v-2h2zm-2-2v2h-2V4h2zm2 2v2h-2V6h2z"></path></svg>
                                QR
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-8 space-y-8">
                        <!-- Profile Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Pelanggan</label>
                                <p class="text-xl font-bold text-gray-800">{{ pelanggan.nama_pelanggan }}</p>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lokasi</label>
                                <p class="text-lg font-medium text-gray-700">
                                    {{ pelanggan.rt ? `RT ${pelanggan.rt}` : '' }}
                                    {{ pelanggan.rt && pelanggan.rw ? '/' : '' }}
                                    {{ pelanggan.rw ? `RW ${pelanggan.rw}` : '' }}
                                </p>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Langganan</label>
                                <div>
                                    <span v-if="pelanggan.status_aktif" class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Aktif
                                    </span>
                                    <span v-else class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-700 border border-red-200">
                                        <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Nonaktif
                                    </span>
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori Tarif</label>
                                <div class="flex items-center gap-2">
                                    <span v-if="pelanggan.kategori === 'sosial'" class="text-purple-700 font-bold bg-purple-100 px-3 py-1 rounded-lg">Sosial (Gratis)</span>
                                    <span v-else class="text-blue-700 font-bold bg-blue-100 px-3 py-1 rounded-lg">Umum</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tagihan Section -->
                        <div v-if="pelanggan.tagihan_bulan_ini" class="bg-gray-50 rounded-xl p-6 border border-gray-100 shadow-inner">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Tagihan {{ pelanggan.tagihan_bulan_ini }}
                            </h4>
                            
                            <!-- Info Kategori Sosial -->
                            <div v-if="pelanggan.kategori === 'sosial'" class="mb-4 bg-purple-50 border border-purple-200 rounded-lg p-4 flex items-start gap-3">
                                <svg class="w-6 h-6 text-purple-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="text-purple-800 font-medium text-sm leading-relaxed">Pelanggan Kategori Sosial dibebaskan dari biaya tagihan bulanan. Tetap gunakan air dengan bijak.</span>
                            </div>
                            
                            <div class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                                <div class="text-center md:text-left">
                                    <p class="text-sm text-gray-500 mb-1">Total Tagihan</p>
                                    <p class="text-3xl font-bold text-gray-900 tracking-tight">{{ formatRupiah(pelanggan.jumlah_bayar) }}</p>
                                </div>
                                
                                <div>
                                    <span v-if="pelanggan.status_bayar === 'Lunas'" class="inline-flex items-center px-6 py-2 rounded-full text-base font-bold bg-blue-100 text-blue-700 border border-blue-200 shadow-sm">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Lunas
                                    </span>
                                    <span v-else class="inline-flex items-center px-6 py-2 rounded-full text-base font-bold bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm animate-pulse">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Belum Dibayar
                                    </span>
                                </div>
                            </div>
                            
                             <div v-if="pelanggan.status_bayar === 'Lunas'" class="mt-4 text-center text-sm text-gray-500">
                                Dibayar pada: <span class="font-semibold text-gray-700">{{ pelanggan.tanggal_bayar }}</span>
                            </div>

                            <!-- Tombol Konfirmasi Pembayaran untuk yang Belum Bayar -->
                            <div v-if="pelanggan.status_bayar !== 'Lunas' && pelanggan.kategori !== 'sosial'" class="mt-6 space-y-3">
                                <!-- Tombol Bayar Sekarang (Primary Action) -->
                                <a 
                                    :href="`/pembayaran/${pelanggan.id_pelanggan}`"
                                    class="flex items-center justify-center gap-3 px-8 py-5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 group text-lg"
                                >
                                    <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Bayar Sekarang</span>
                                </a>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h5 class="font-bold text-blue-900 mb-2 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Cara Pembayaran
                                    </h5>
                                    <p v-if="paymentSettings.payment_instructions" class="text-sm text-blue-800 mb-2">{{ paymentSettings.payment_instructions }}</p>
                                    <ol class="text-sm text-blue-800 space-y-2 ml-7 list-decimal">
                                        <li>Klik tombol <strong>"Bayar Sekarang"</strong> di atas</li>
                                        <li v-if="paymentSettings.qris_enabled">Scan QRIS atau transfer ke rekening</li>
                                        <li v-else-if="paymentSettings.bank_transfer_enabled">Transfer ke rekening KP-SPAMS</li>
                                        <li v-else>Bayar langsung ke petugas</li>
                                        <li>Simpan bukti pembayaran</li>
                                        <li>Konfirmasi via WhatsApp dengan mengirim bukti</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Pembayaran Terakhir (Jika ada pembayaran bulan ini) -->
                        <div v-if="pelanggan.pembayaran_terakhir && pelanggan.pembayaran_terakhir.length > 0" class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Pembayaran Terakhir (Bulan Ini)
                            </h4>
                            <div class="space-y-3">
                                <div v-for="(item, idx) in pelanggan.pembayaran_terakhir" :key="idx" class="flex justify-between items-center bg-white p-3 rounded-lg border border-blue-200">
                                    <div>
                                        <div class="font-bold text-gray-800">{{ item.bulan }}</div>
                                        <div class="text-xs text-gray-500">Dibayar tgl: {{ item.tanggal_bayar }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-blue-700">{{ formatRupiah(item.jumlah_bayar) }}</div>
                                        <span v-if="item.is_tunggakan" class="text-[10px] font-bold uppercase tracking-wider text-white bg-yellow-500 px-2 py-0.5 rounded-full">Tunggakan</span>
                                        <span v-else class="text-[10px] font-bold uppercase tracking-wider text-white bg-blue-500 px-2 py-0.5 rounded-full">Tagihan</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Tunggakan (Jika ada) -->
                        <div v-if="pelanggan.tunggakan && pelanggan.tunggakan.length > 0" class="bg-red-50 rounded-xl p-6 border border-red-100">
                            <h4 class="text-lg font-bold text-red-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Tunggakan Belum Dibayar
                            </h4>
                            <div class="space-y-2">
                                <div v-for="(item, idx) in pelanggan.tunggakan" :key="idx" class="flex justify-between items-center bg-white p-3 rounded-lg border border-red-200">
                                    <div class="font-medium text-gray-700">{{ item.bulan }}</div>
                                    <span class="text-xs font-bold text-red-600 bg-red-100 px-2 py-1 rounded-full">{{ item.status }}</span>
                                </div>
                            </div>
                            <p class="text-xs text-red-600 mt-3 italic">* Mohon segera lakukan pembayaran untuk menghindari pemutusan sementara.</p>
                        </div>

                        <!-- Riwayat Table -->
                        <div v-if="pelanggan.riwayat && pelanggan.riwayat.length > 0" class="pt-4 mt-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4">Riwayat Pemakaian</h4>
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500 sm:pl-6">Bulan</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Pemakaian</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <tr v-for="(item, index) in pelanggan.riwayat" :key="index" class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ item.bulan }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.kubik }} m³</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">{{ formatRupiah(item.rupiah) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section with Image -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center gap-16">
                    <div class="w-full md:w-1/2 relative group animate-on-scroll">
                        <div class="absolute -inset-4 bg-blue-100 rounded-3xl transform rotate-3 group-hover:rotate-2 transition-transform duration-500"></div>
                        <img 
                            src="/images/tentangprogramm.png" 
                            alt="Masyarakat KP-SPAMS" 
                            class="relative rounded-2xl shadow-2xl w-full h-[400px] object-cover transform group-hover:-translate-y-2 transition-transform duration-500"
                        />
                         <!-- Decorative Badge -->
                         <div class="absolute -bottom-6 -right-6 bg-white p-4 rounded-xl shadow-lg border-l-4 border-blue-600 hidden md:block">
                             <div class="text-blue-800 font-bold text-3xl">100%</div>
                             <div class="text-gray-600 text-sm font-medium">Berbasis Masyarakat</div>
                         </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 animate-on-scroll">
                        <h4 class="text-blue-600 font-bold uppercase tracking-widest text-sm mb-2">Tentang Program</h4>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6 leading-tight">
                            Bersama Membangun <br/>
                            <span class="text-blue-600">Sanitasi & Air Bersih</span>
                        </h2>
                        <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                            KP-SPAMS (Kelompok Pengelola Sistem Penyediaan Air Minum dan Sanitasi) adalah program andalan pemerintah untuk meningkatkan akses penduduk perdesaan terhadap fasilitas air minum dan sanitasi yang layak dengan pendekatan berbasis masyarakat.
                        </p>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-bold text-gray-900">Kualitas Terjamin</h5>
                                    <p class="text-gray-500">Air bersih yang telah melalui proses pengecekan kualitas berkala.</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <h5 class="text-lg font-bold text-gray-900">Mudah & Terjangkau</h5>
                                    <p class="text-gray-500">Biaya pemeliharaan yang murah dengan sistem iuran yang transparan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section Grid -->
        <section class="py-20 bg-gray-50 text-center">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-3xl font-bold text-gray-900 mb-12 animate-on-scroll">Layanan Unggulan</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Service 1 -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm md:hover:shadow-xl transition-all duration-300 md:hover:-translate-y-2 active:shadow-lg active:-translate-y-1 touch-manipulation group border border-gray-100 animate-on-scroll">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 md:group-hover:bg-blue-600 group-active:bg-blue-600 transition-colors duration-300">
                            <svg class="w-8 h-8 text-blue-600 md:group-hover:text-white group-active:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Distribusi Air Bersih</h3>
                        <p class="text-gray-600 leading-relaxed">Penyaluran air bersih 24 jam dengan debit yang stabil ke seluruh rumah warga.</p>
                    </div>

                    <!-- Service 2 -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm md:hover:shadow-xl transition-all duration-300 md:hover:-translate-y-2 active:shadow-lg active:-translate-y-1 touch-manipulation group border border-gray-100 animate-on-scroll">
                        <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-6 md:group-hover:bg-blue-600 group-active:bg-blue-600 transition-colors duration-300">
                             <svg class="w-8 h-8 text-blue-600 md:group-hover:text-white group-active:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Pemeliharaan Rutin</h3>
                        <p class="text-gray-600 leading-relaxed">Tim teknis yang siap sedia melakukan perawatan jaringan pipa dan pompa secara berkala.</p>
                    </div>

                    <!-- Service 3 -->
                    <div class="bg-white p-8 rounded-2xl shadow-sm md:hover:shadow-xl transition-all duration-300 md:hover:-translate-y-2 active:shadow-lg active:-translate-y-1 touch-manipulation group border border-gray-100 animate-on-scroll">
                        <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto mb-6 md:group-hover:bg-purple-600 group-active:bg-purple-600 transition-colors duration-300">
                             <svg class="w-8 h-8 text-purple-600 md:group-hover:text-white group-active:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 7m0 13V7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Pemetaan Digital</h3>
                        <p class="text-gray-600 leading-relaxed">Sistem informasi geografis (GIS) untuk memantau persebaran jaringan dan pelanggan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Panduan Layanan Section -->
        <section class="py-16 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Panduan Layanan</h2>
                    <p class="mt-4 text-gray-600 max-w-2xl mx-auto">Ikuti langkah mudah berikut untuk mendaftar sebagai pelanggan baru atau melaporkan kendala layanan.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-12 items-start">
                    <!-- Card Cara Daftar -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-3xl p-8 border border-blue-100 shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-300 animate-on-scroll">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full opacity-20 -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                        
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800">Cara Daftar Pasang Baru</h3>
                        </div>

                        <div class="space-y-6 relative z-10">
                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-blue-600 group-hover/step:text-white transition-colors">1</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Siapkan Berkas</h5>
                                    <p class="text-gray-600 text-sm mt-1">Siapkan fotokopi KTP dan Kartu Keluarga (KK).</p>
                                </div>
                            </div>
                            <!-- Connector Line -->
                            <div class="absolute left-4 top-10 w-0.5 h-6 bg-gray-200 -z-10"></div>
                            
                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-blue-600 group-hover/step:text-white transition-colors">2</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Hubungi Admin</h5>
                                    <p class="text-gray-600 text-sm mt-1">Kirim pesan WhatsApp ke Admin KP-SPAMS untuk formulir pendaftaran.</p>
                                </div>
                            </div>
                            <div class="absolute left-4 top-[5.5rem] w-0.5 h-6 bg-gray-200 -z-10"></div>

                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-blue-600 group-hover/step:text-white transition-colors">3</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Verifikasi Lapangan</h5>
                                    <p class="text-gray-600 text-sm mt-1">Tim teknis akan survei lokasi untuk menentukan jalur pipa.</p>
                                </div>
                            </div>
                            <div class="absolute left-4 top-[10rem] w-0.5 h-6 bg-gray-200 -z-10"></div>

                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-blue-600 group-hover/step:text-white transition-colors">4</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Pemasangan & Aktivasi</h5>
                                    <p class="text-gray-600 text-sm mt-1">Pembayaran administrasi dan pemasangan meteran air siap pakai.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Cara Lapor -->
                    <div class="bg-gradient-to-br from-red-50 to-white rounded-3xl p-8 border border-red-100 shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-red-200 rounded-full opacity-20 -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800">Cara Pelaporan Gangguan</h3>
                        </div>

                        <div class="space-y-6 relative z-10">
                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-red-600 group-hover/step:text-white transition-colors">1</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Siapkan ID Pelanggan</h5>
                                    <p class="text-gray-600 text-sm mt-1">Cek ID Pelanggan Anda pada struk pembayaran terakhir.</p>
                                </div>
                            </div>
                            <!-- Connector Line -->
                            <div class="absolute left-4 top-10 w-0.5 h-6 bg-gray-200 -z-10"></div>

                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-red-600 group-hover/step:text-white transition-colors">2</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Foto Kendala</h5>
                                    <p class="text-gray-600 text-sm mt-1">Ambil foto kerusakan (pipa bocor, meteran macet, dll).</p>
                                </div>
                            </div>
                            <div class="absolute left-4 top-[5.5rem] w-0.5 h-6 bg-gray-200 -z-10"></div>

                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-red-600 group-hover/step:text-white transition-colors">3</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Kirim Laporan</h5>
                                    <p class="text-gray-600 text-sm mt-1">Kirim detail dan foto ke WhatsApp Admin KP-SPAMS.</p>
                                </div>
                            </div>
                            <div class="absolute left-4 top-[10rem] w-0.5 h-6 bg-gray-200 -z-10"></div>

                            <div class="flex gap-4 group/step">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center border-2 border-white shadow-sm group-hover/step:bg-red-600 group-hover/step:text-white transition-colors">4</div>
                                <div>
                                    <h5 class="font-bold text-gray-800">Penanganan Teknisi</h5>
                                    <p class="text-gray-600 text-sm mt-1">Tim kami akan segera datang melakukan perbaikan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Info Tarif & Biaya Section -->
        <section class="py-20 bg-gradient-to-br from-blue-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 animate-on-scroll">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Informasi Tarif & Biaya</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Struktur biaya yang transparan dan terjangkau untuk kemudahan masyarakat</p>
                </div>

                <!-- Tarif Cards -->
                <div class="grid md:grid-cols-3 gap-6 animate-on-scroll">
                    <template v-if="tarifs.length > 0">
                        <div
                            v-for="tarif in tarifs"
                            :key="tarif.id"
                            class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-6 border border-blue-100"
                        >
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-600 font-medium">{{ tarif.kategori }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ tarif.judul }}</h3>
                            <p v-if="tarif.deskripsi" class="text-sm text-gray-600 mb-4">{{ tarif.deskripsi }}</p>
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-500">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-2xl font-bold text-blue-600">{{ formatRupiah(tarif.harga) }}</span>
                                    <span class="text-gray-600 text-sm">/ {{ tarif.satuan }}</span>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Loading placeholder -->
                    <template v-else>
                        <div class="bg-white rounded-xl shadow-md p-6 border border-blue-100 animate-pulse">
                            <div class="h-10 w-10 bg-gray-200 rounded-lg mb-4"></div>
                            <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-gray-200 rounded w-full mb-4"></div>
                            <div class="h-12 bg-gray-200 rounded"></div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md p-6 border border-blue-100 animate-pulse">
                            <div class="h-10 w-10 bg-gray-200 rounded-lg mb-4"></div>
                            <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-gray-200 rounded w-full mb-4"></div>
                            <div class="h-12 bg-gray-200 rounded"></div>
                        </div>
                        <div class="bg-white rounded-xl shadow-md p-6 border border-blue-100 animate-pulse">
                            <div class="h-10 w-10 bg-gray-200 rounded-lg mb-4"></div>
                            <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-gray-200 rounded w-full mb-4"></div>
                            <div class="h-12 bg-gray-200 rounded"></div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Landing Page Sections (Dynamic Content) -->
        <VisiMisiSection />
        <LayananSection />
        <SejarahSection />
        <GaleriSection />
        <BeritaSection />
        <TestimoniSection />

        <!-- FAQ Section -->
        <section class="py-20 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 animate-on-scroll">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Pertanyaan Umum (FAQ)</h2>
                    <p class="text-gray-600">Jawaban cepat untuk pertanyaan yang sering diajukan</p>
                </div>

                <div class="space-y-4">
                    <details class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-blue-300 transition animate-on-scroll">
                        <summary class="flex justify-between items-center cursor-pointer font-bold text-gray-800 text-lg">
                            <span>Bagaimana cara menghitung tarif bulanan?</span>
                            <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <p class="mt-4 text-gray-600 leading-relaxed">Untuk pelanggan kategori umum dikenakan biaya tetap Rp 25.000 per bulan tanpa melihat pemakaian. Sedangkan pelanggan kategori sosial mendapat subsidi penuh (gratis) dari program pemerintah.</p>
                    </details>

                    <details class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-blue-300 transition">
                        <summary class="flex justify-between items-center cursor-pointer font-bold text-gray-800 text-lg">
                            <span>Apa yang harus dilakukan jika air tidak mengalir?</span>
                            <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <p class="mt-4 text-gray-600 leading-relaxed">Segera hubungi admin KP-SPAMS melalui WhatsApp dengan melampirkan ID Pelanggan dan foto meteran. Tim teknis kami akan segera melakukan pengecekan dalam waktu maksimal 24 jam.</p>
                    </details>

                    <details class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-blue-300 transition">
                        <summary class="flex justify-between items-center cursor-pointer font-bold text-gray-800 text-lg">
                            <span>Bagaimana cara melakukan pembayaran tagihan?</span>
                            <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <p class="mt-4 text-gray-600 leading-relaxed">Pembayaran dapat dilakukan langsung ke petugas yang datang ke rumah setiap awal bulan. Anda juga bisa membayar langsung ke kantor KP-SPAMS dengan jam operasional Senin-Jumat pukul 08.00-15.00 WIB.</p>
                    </details>

                    <details class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-blue-300 transition">
                        <summary class="flex justify-between items-center cursor-pointer font-bold text-gray-800 text-lg">
                            <span>Berapa lama proses pemasangan untuk pelanggan baru?</span>
                            <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <p class="mt-4 text-gray-600 leading-relaxed">Setelah berkas lengkap dan pembayaran diterima, proses survei lapangan dilakukan 1-3 hari. Pemasangan instalasi dan meteran biasanya selesai dalam 3-7 hari kerja tergantung jarak lokasi dan kondisi lapangan.</p>
                    </details>

                    <details class="group bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-blue-300 transition">
                        <summary class="flex justify-between items-center cursor-pointer font-bold text-gray-800 text-lg">
                            <span>Bagaimana syarat untuk menjadi pelanggan kategori sosial?</span>
                            <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <p class="mt-4 text-gray-600 leading-relaxed">Untuk kategori sosial diperlukan Surat Keterangan Tidak Mampu (SKTM) dari RT/RW setempat dan diverifikasi oleh pihak desa. Kategori ini diperuntukkan bagi keluarga pra-sejahtera berdasarkan data desa.</p>
                    </details>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-16 relative overflow-hidden">
             <div class="absolute inset-0 opacity-10">
                 <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" /></svg>
             </div>
             <div class="max-w-4xl mx-auto text-center px-4 relative z-10">
                 <h2 class="text-3xl font-bold mb-6">Butuh Bantuan atau Informasi Lebih Lanjut?</h2>
                 <p class="text-blue-100 mb-8 text-lg">Hubungi tim pengelola kami jika Anda mengalami gangguan layanan atau memiliki pertanyaan seputar pendaftaran pelanggan baru.</p>
                 <div class="flex flex-col sm:flex-row justify-center gap-4">
                     <a 
                         href="https://wa.me/6285228357400?text=Halo%20Admin%20KP-SPAMS,%20saya%20ingin%20bertanya%20seputar%20pendaftaran%20dan%20pelaporan%20layanan."
                         target="_blank"
                         rel="noopener noreferrer"
                         class="px-8 py-3 bg-white text-blue-900 rounded-full font-bold hover:bg-gray-100 transition shadow-lg flex items-center justify-center gap-2 group"
                     >
                         <svg class="w-5 h-5 group-hover:scale-110 transition-transform text-blue-600" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                         <span>Hubungi WhatsApp</span>
                     </a>
                     <button class="px-8 py-3 border-2 border-blue-400 text-white rounded-full font-bold hover:bg-blue-800 transition">
                         Lokasi Kantor
                     </button>
                 </div>
             </div>
        </section>

        <!-- Developer Credit Section -->
        <section class="py-12 bg-white border-t border-gray-100">
            <div class="max-w-4xl mx-auto px-4 text-center">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6 animate-on-scroll">Dikembangkan Oleh</p>
                <div class="flex flex-col md:flex-row items-center justify-center gap-8">
                    <!-- Profile Photo -->
                    <div class="relative group mb-4 md:mb-0 animate-on-scroll">
                        <div class="absolute -inset-2 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full opacity-60 group-hover:opacity-100 transition duration-500 blur-lg group-hover:blur-xl animate-pulse"></div>
                        <img 
                            src="/images/saya.png" 
                            alt="Mukhammad Alfaen Fadillah" 
                            class="relative w-48 h-48 rounded-full border-[6px] border-white shadow-2xl object-cover transform transition-all duration-500 group-hover:scale-105 group-hover:rotate-2"
                        />
                        <!-- Coding Badge -->
                        <div class="absolute bottom-1 right-2 bg-blue-600 text-white p-2.5 rounded-full shadow-lg border-4 border-white transform transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-12">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </div>
                    </div>
                    
                    <!-- Text Info -->
                    <div class="text-center md:text-left animate-on-scroll">
                        <h3 class="text-2xl font-bold text-gray-900">Mukhammad Alfaen Fadillah</h3>
                        <p class="text-blue-700 font-medium mb-2">Mahasiswa KKN Universitas Jenderal Soedirman (UNSOED)</p>
                        <p class="text-gray-500 text-sm leading-relaxed max-w-lg">
                            Website ini dikembangkan sebagai bentuk pengabdian masyarakat bekerjasama dengan 
                            <span class="font-bold text-gray-700">Unit KP-SPAMS Desa Ciwuni</span>, 
                            bertujuan untuk mempermudah digitalisasi layanan air bersih desa.
                        </p>
                    </div>
                </div>

                <!-- Team KKN Photo Section -->
                <div class="mt-16 pt-12 border-t border-gray-200 animate-on-scroll">
                    <div class="flex flex-col items-center gap-6">
                        <img 
                            src="/images/team.png" 
                            alt="Tim KKN Desa Ciwuni" 
                            class="w-full max-w-3xl h-auto rounded-xl shadow-lg object-cover transform hover:scale-[1.02] transition-transform duration-500"
                        />
                        
                        <div class="text-center max-w-2xl">
                            <h4 class="text-xl font-bold text-gray-900 mb-3 flex items-center justify-center gap-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Dibantu oleh Rekan-Rekan Pengembang
                            </h4>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                Terima kasih kepada seluruh anggota <span class="font-bold text-gray-800">Tim KKN UNSOED Desa Ciwuni</span> 
                                yang telah memberikan dukungan, masukan, dan semangat dalam pengembangan sistem informasi KP-SPAMS ini. 
                                Kolaborasi tim yang solid menjadikan proyek ini dapat terwujud dengan baik.
                            </p>
                            <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                <span class="text-sm font-bold">Tim KKN UNSOED 2026</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Peta Lokasi Kantor Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Lokasi Kantor KP-SPAMS</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Kunjungi kantor kami untuk informasi lebih lanjut atau pengaduan</p>
                </div>

                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 animate-on-scroll">
                    <!-- Google Maps Embed -->
                    <div class="w-full h-[500px] bg-gray-100">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.846!2d109.08140!3d-7.60043!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMzYnMDEuNSJTIDEwOcKwMDQnNTMuMCJF!5e0!3m2!1sid!2sid!4v1707307890123!5m2!1sid!2sid"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full"
                        ></iframe>
                    </div>

                    <!-- Info Kantor -->
                    <div class="p-6 bg-white border-t border-gray-200">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-lg">Kantor KP-SPAMS DAMAR WULAN</p>
                                    <p class="text-sm text-gray-600 mt-1">Desa Ciwuni, Kec. Kesugihan</p>
                                    <p class="text-sm text-gray-600">Kab. Cilacap, Jawa Tengah</p>
                                </div>
                            </div>
                            <a 
                                href="https://maps.app.goo.gl/dCMnJs3RFM7WJAjh7" 
                                target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition whitespace-nowrap shadow-lg hover:shadow-xl"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Buka di Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Partner/Dukungan Logos -->
                <div class="mt-12">
                    <h3 class="text-center text-2xl font-bold text-gray-800 mb-8 animate-on-scroll">Didukung Oleh</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 items-center">
                        <div class="flex items-center justify-center p-6 bg-white rounded-xl shadow-md md:hover:shadow-xl transition-all duration-300 transform md:hover:-translate-y-2 active:shadow-xl active:-translate-y-1 touch-manipulation animate-on-scroll" style="animation-delay: 0.1s">
                            <img 
                                src="/images/logo_partner1.png" 
                                alt="Partner 1" 
                                class="max-h-20 w-auto object-contain md:grayscale md:hover:grayscale-0 transition-all duration-300"
                            >
                        </div>
                        
                        <div class="flex items-center justify-center p-6 bg-white rounded-xl shadow-md md:hover:shadow-xl transition-all duration-300 transform md:hover:-translate-y-2 active:shadow-xl active:-translate-y-1 touch-manipulation animate-on-scroll" style="animation-delay: 0.2s">
                            <img 
                                src="/images/logo_partner2.png" 
                                alt="Partner 2" 
                                class="max-h-20 w-auto object-contain md:grayscale md:hover:grayscale-0 transition-all duration-300"
                            >
                        </div>
                        
                        <div class="flex items-center justify-center p-6 bg-white rounded-xl shadow-md md:hover:shadow-xl transition-all duration-300 transform md:hover:-translate-y-2 active:shadow-xl active:-translate-y-1 touch-manipulation animate-on-scroll" style="animation-delay: 0.3s">
                            <img 
                                src="/images/logo_partner3.png" 
                                alt="Partner 3" 
                                class="max-h-20 w-auto object-contain md:grayscale md:hover:grayscale-0 transition-all duration-300"
                            >
                        </div>
                        
                        <div class="flex items-center justify-center p-6 bg-white rounded-xl shadow-md md:hover:shadow-xl transition-all duration-300 transform md:hover:-translate-y-2 active:shadow-xl active:-translate-y-1 touch-manipulation animate-on-scroll" style="animation-delay: 0.4s">
                            <img 
                                src="/images/logo_partner4.png" 
                                alt="Partner 4" 
                                class="max-h-20 w-auto object-contain md:grayscale md:hover:grayscale-0 transition-all duration-300"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Floating WhatsApp Button -->
        <a 
            href="https://wa.me/6285228357400?text=Halo%20Admin%20KP-SPAMS,%20saya%20ingin%20bertanya%20seputar%20pendaftaran%20dan%20pelaporan%20layanan."
            target="_blank"
            rel="noopener noreferrer"
            class="fixed bottom-6 right-6 z-50 bg-[#25D366] hover:bg-[#20bd5a] active:bg-[#1ea952] text-white p-4 rounded-full shadow-2xl transition-all duration-300 hover:scale-110 active:scale-105 touch-manipulation flex items-center justify-center group"
            aria-label="Chat WhatsApp"
        >
            <div class="absolute -top-12 right-0 bg-white px-4 py-2 rounded-xl rounded-br-none shadow-xl text-gray-800 text-sm font-bold opacity-0 md:group-hover:opacity-100 group-active:opacity-100 transition-opacity duration-300 w-max pointer-events-none border border-gray-100">
                Pendaftaran & Lapor
            </div>
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.008-.57-.008-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
        </a>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <!-- Tentang -->
                    <div>
                        <h3 class="text-xl font-bold mb-4">KP-SPAMS Damar Wulan</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Menyediakan layanan air bersih berkualitas untuk kesejahteraan masyarakat Desa Ciwuni.
                        </p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <span class="text-sm font-medium">Air Bersih 24 Jam</span>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div>
                        <h4 class="text-lg font-bold mb-4">Hubungi Kami</h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="text-gray-400">Jl. Raya Ciwuni, Desa Ciwuni<br>Kec. Cilongok, Kab. Banyumas<br>Jawa Tengah 53162</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span class="text-gray-400">+62 812-3456-7890</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="text-gray-400">kpspams.ciwuni@gmail.com</span>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Operasional -->
                    <div>
                        <h4 class="text-lg font-bold mb-4">Jam Operasional</h4>
                        <div class="space-y-2 text-sm text-gray-400">
                            <div class="flex justify-between">
                                <span>Senin - Jumat</span>
                                <span class="text-blue-400 font-medium">08.00 - 15.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sabtu</span>
                                <span class="text-blue-400 font-medium">08.00 - 12.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Minggu</span>
                                <span class="text-red-400 font-medium">Tutup</span>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-gray-800 rounded-lg text-xs">
                            <p class="text-gray-400">📞 <span class="text-white font-bold">Layanan Darurat 24 Jam</span></p>
                            <p class="text-gray-500 mt-1">Untuk gangguan mendesak hubungi WhatsApp</p>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-lg font-bold mb-4">Tautan Cepat</h4>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#" class="text-gray-400 hover:text-blue-400 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    Cara Pendaftaran
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-blue-400 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    Cek Tagihan
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-blue-400 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    Lapor Gangguan
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-blue-400 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    Info Tarif
                                </a>
                            </li>
                            <li>
                                <a href="#" class="text-gray-400 hover:text-blue-400 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    Kontak
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-500 text-sm text-center md:text-left">
                        © 2026 <span class="text-white font-bold">KP-SPAMS Damar Wulan</span>. All rights reserved.
                    </p>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <span>Developed with</span>
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>
                        <span>by <span class="text-white font-bold">KKN UNSOED</span></span>
                    </div>
                </div>
            </div>
        </footer>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import VisiMisiSection from '@/Components/LandingPage/VisiMisiSection.vue';
import LayananSection from '@/Components/LandingPage/LayananSection.vue';
import SejarahSection from '@/Components/LandingPage/SejarahSection.vue';
import GaleriSection from '@/Components/LandingPage/GaleriSection.vue';
import BeritaSection from '@/Components/LandingPage/BeritaSection.vue';
import TestimoniSection from '@/Components/LandingPage/TestimoniSection.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, reactive, onMounted } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalPelanggan: 0,
            pelangganAktif: 0,
            cakupanArea: '0 RT/RW',
        }),
    },
    pelanggan: Object,
    error: String,
    paymentSettings: {
        type: Object,
        default: () => ({
            qris_enabled: false,
            bank_transfer_enabled: true,
            qris_image: null,
            bank_name: 'BRI',
            account_number: '1234-5678-9012',
            account_name: 'KP-SPAMS Desa',
            payment_instructions: 'Transfer ke rekening atau scan QRIS',
        }),
    },
});

const form = reactive({
    id_pelanggan: '',
    processing: false,
});

const error = ref(props.error);
const pelanggan = ref(props.pelanggan);
const tarifs = ref([]);
const isTarifsLoaded = ref(false);
const typingText = ref(null);

// Typing animation function with loop
const typeWriter = (text, element, typeSpeed = 150, deleteSpeed = 100, pauseTime = 3000) => {
    let i = 0;
    let isDeleting = false;
    
    const animate = () => {
        if (!element) return;
        
        if (!isDeleting && i < text.length) {
            // Typing
            element.textContent = text.substring(0, i + 1);
            i++;
            setTimeout(animate, typeSpeed);
        } else if (!isDeleting && i === text.length) {
            // Pause before deleting
            isDeleting = true;
            setTimeout(animate, pauseTime);
        } else if (isDeleting && i > 0) {
            // Deleting
            element.textContent = text.substring(0, i - 1);
            i--;
            setTimeout(animate, deleteSpeed);
        } else if (isDeleting && i === 0) {
            // Reset and start typing again
            isDeleting = false;
            setTimeout(animate, 500); // Short pause before retyping
        }
    };
    
    animate();
};

// Fetch tarif data from API
const fetchTarifs = async () => {
    if (isTarifsLoaded.value) {
        console.log('⏭️ Tarifs already loaded, skipping fetch');
        return;
    }
    
    try {
        console.log('🔄 Fetching tarifs...');
        const response = await fetch('/api/informasi-tarif');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log('📦 Tarif data received:', data);
        
        // Force reactivity update
        if (Array.isArray(data) && data.length > 0) {
            tarifs.value = [...data];
            isTarifsLoaded.value = true;
            console.log('✅ tarifs.value updated:', tarifs.value);
            console.log('✅ Length:', tarifs.value.length);
        } else {
            console.warn('⚠️ No tarif data received');
        }
    } catch (err) {
        console.error('❌ Error fetching tarif data:', err);
    }
};

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const cekPelanggan = () => {
    error.value = null;
    form.processing = true;
    
    router.get('/', { id_pelanggan: form.id_pelanggan }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            pelanggan.value = page.props.pelanggan;
            error.value = page.props.error;
        },
        onFinish: () => {
            form.processing = false;
        },
    });
};

const konfirmasiPembayaran = () => {
    if (!pelanggan.value) return;
    
    const nomorAdmin = '6281234567890'; // Ganti dengan nomor admin yang sebenarnya
    
    const pesan = `Halo Admin KP-SPAMS,

Saya ingin mengkonfirmasi pembayaran tagihan air:

📋 *Detail Pelanggan:*
• ID Pelanggan: ${pelanggan.value.id_pelanggan}
• Nama: ${pelanggan.value.nama_pelanggan}
• Alamat: RT ${pelanggan.value.rt}/RW ${pelanggan.value.rw}

💰 *Detail Tagihan:*
• Bulan: ${pelanggan.value.tagihan_bulan_ini}
• Jumlah: ${formatRupiah(pelanggan.value.jumlah_bayar)}

Saya sudah melakukan pembayaran. Berikut saya lampirkan bukti transfer/pembayaran.

Mohon untuk diverifikasi. Terima kasih.`;

    const waLink = `https://wa.me/${nomorAdmin}?text=${encodeURIComponent(pesan)}`;
    window.open(waLink, '_blank');
};

// Scroll Animation Observer
onMounted(() => {
    fetchTarifs(); // Load tarif data on mount
    
    // Start typing animation after a short delay
    setTimeout(() => {
        if (typingText.value) {
            typeWriter('DAMAR WULAN', typingText.value, 150, 100, 3000);
        }
    }, 800); // Start after fade-in animation
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.animate-on-scroll').forEach((el) => {
        observer.observe(el);
    });
});
</script>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-on-scroll {
    opacity: 0;
    transform: translateY(20px);
}

.animate-in {
    animation: fadeInUp 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out;
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.text-shadow {
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.text-shadow-lg {
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}

.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Typing Animation */
.typing-text-wrapper {
    display: inline;
    white-space: nowrap;
}

.typing-cursor {
    display: inline;
    animation: blink 0.7s steps(2) infinite;
    margin-left: 2px;
    font-weight: lighter;
}

@keyframes blink {
    0%, 50% {
        opacity: 1;
    }
    51%, 100% {
        opacity: 0;
    }
}

/* Stagger animation delays */
.animate-on-scroll:nth-child(1) { animation-delay: 0.1s; }
.animate-on-scroll:nth-child(2) { animation-delay: 0.2s; }
.animate-on-scroll:nth-child(3) { animation-delay: 0.3s; }
.animate-on-scroll:nth-child(4) { animation-delay: 0.4s; }
.animate-on-scroll:nth-child(5) { animation-delay: 0.5s; }
.animate-on-scroll:nth-child(6) { animation-delay: 0.6s; }
</style>
