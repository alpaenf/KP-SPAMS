<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-50 py-8 px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Pembayaran Tagihan</h1>
                    <p class="text-gray-600">Pilih metode pembayaran yang tersedia</p>
                </div>

                <!-- Info Pelanggan -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Pelanggan
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">ID Pelanggan</p>
                            <p class="font-bold text-gray-800">{{ pelanggan.id_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="font-bold text-gray-800">{{ pelanggan.nama_pelanggan }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kategori</p>
                            <span v-if="pelanggan.kategori === 'sosial'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
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
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tagihan Bulan</p>
                            <p class="font-bold text-gray-800">{{ pelanggan.tagihan_bulan_ini }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Pembayaran</p>
                            <span v-if="pelanggan.sudah_bayar" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Sudah Dibayar
                            </span>
                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Belum Dibayar
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Tagihan</p>
                            <p class="text-2xl font-bold" :class="pelanggan.kategori === 'sosial' ? 'text-purple-600' : 'text-blue-600'">
                                {{ formatRupiah(pelanggan.jumlah_bayar) }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Peringatan untuk pelanggan kategori sosial -->
                    <div v-if="pelanggan.kategori === 'sosial'" class="mt-4 bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-bold text-purple-800 mb-1">Informasi Pelanggan Kategori Sosial</h3>
                                <p class="text-sm text-purple-700">
                                    Anda terdaftar sebagai pelanggan kategori <strong>Sosial</strong>. 
                                    Tagihan air untuk kategori ini adalah <strong>GRATIS</strong>. 
                                    Anda tidak perlu melakukan pembayaran.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Peringatan tagihan belum di-generate -->
                    <div v-else-if="!pelanggan.ada_tagihan" class="mt-4 bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-bold text-yellow-800 mb-1">Tagihan Belum Tersedia</h3>
                                <p class="text-sm text-yellow-700">
                                    Tagihan untuk bulan <strong>{{ pelanggan.tagihan_bulan_ini }}</strong> belum dibuat oleh pengelola. 
                                    Silakan hubungi pengelola untuk membuat tagihan bulanan Anda.
                                    <br><br>
                                    <em>Sementara ini menggunakan tarif default Rp 10.000/bulan.</em>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Peringatan meteran belum diinput -->
                    <div v-else-if="pelanggan.meteran_belum_diinput" class="mt-4 bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-bold text-yellow-800 mb-1">Meteran Belum Dicatat</h3>
                                <p class="text-sm text-yellow-700">
                                    Pengelola belum mencatat meteran air Anda untuk bulan <strong>{{ pelanggan.tagihan_bulan_ini }}</strong>. 
                                    Tagihan akan muncul setelah meteran dicatat.
                                    <br><br>
                                    <em>Tagihan saat ini: <strong>{{ formatRupiah(pelanggan.jumlah_bayar) }}</strong></em>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Peringatan sudah dibayar -->
                    <div v-else-if="pelanggan.sudah_bayar" class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-bold text-blue-800 mb-1">Pembayaran Sudah Lunas</h3>
                                <p class="text-sm text-blue-700">
                                    Terima kasih! Pembayaran untuk bulan <strong>{{ pelanggan.tagihan_bulan_ini }}</strong> sudah lunas.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Meteran Air (hanya tampil jika ada tagihan dan meteran sudah diinput) -->
                <div v-if="tagihan && tagihan.meteran_sesudah !== null" class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Detail Meteran Air
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                            <p class="text-sm text-blue-600 font-medium mb-1">Meteran Sebelum</p>
                            <p class="text-2xl font-bold text-blue-800">{{ tagihan.meteran_sebelum?.toFixed(2) || '0.00' }} m³</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                            <p class="text-sm text-green-600 font-medium mb-1">Meteran Sesudah</p>
                            <p class="text-2xl font-bold text-green-800">{{ tagihan.meteran_sesudah?.toFixed(2) || '0.00' }} m³</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                            <p class="text-sm text-purple-600 font-medium mb-1">Pemakaian</p>
                            <p class="text-2xl font-bold text-purple-800">{{ tagihan.pemakaian?.toFixed(2) || '0.00' }} m³</p>
                        </div>
                    </div>
                    
                    <!-- Rincian Biaya -->
                    <div class="mt-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                        <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Rincian Biaya
                        </h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Biaya Pemakaian ({{ tagihan.pemakaian_ditagih?.toFixed(2) || '0.00' }} m³ × {{ formatRupiah(tagihan.tarif_per_kubik || 0) }})</span>
                                <span class="font-semibold text-gray-800">{{ formatRupiah(tagihan.biaya_pemakaian || 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Biaya Abonemen</span>
                                <span class="font-semibold text-gray-800">{{ formatRupiah(tagihan.biaya_abunemen || 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tunggakan</span>
                                <span class="font-semibold text-gray-800">{{ formatRupiah(tagihan.tunggakan || 0) }}</span>
                            </div>
                            <div class="border-t-2 border-gray-300 pt-2 flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800">Total Tagihan</span>
                                <span class="text-xl font-bold text-blue-600">{{ formatRupiah(tagihan.total_tagihan || 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div v-if="pelanggan.kategori !== 'sosial' && !pelanggan.sudah_bayar" class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Pilih Metode Pembayaran
                    </h2>

                    <div class="space-y-4">
                        <!-- QRIS Payment -->
                        <div v-if="paymentSettings.qris_enabled && paymentSettings.qris_image" class="border-2 border-blue-200 rounded-xl p-6 hover:border-blue-400 transition">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        QRIS (Quick Response Indonesia Standard)
                                    </h3>
                                    <p class="text-sm text-gray-600 mt-1">Scan kode QR di bawah dengan aplikasi e-wallet Anda</p>
                                </div>
                            </div>
                            <div class="flex justify-center bg-gray-50 rounded-lg p-6">
                                <img 
                                    :src="`/storage/${paymentSettings.qris_image}`" 
                                    alt="QRIS Payment" 
                                    class="max-w-sm w-full border-4 border-white rounded-lg shadow-lg"
                                >
                            </div>
                            <div class="mt-4 bg-blue-50 rounded-lg p-3">
                                <p class="text-sm text-blue-800 font-medium">
                                    ✓ Buka aplikasi: GoPay, OVO, Dana, LinkAja, ShopeePay, dll<br>
                                    ✓ Scan QR Code di atas<br>
                                    ✓ Masukkan nominal: <strong>{{ formatRupiah(pelanggan.jumlah_bayar) }}</strong><br>
                                    ✓ Konfirmasi pembayaran
                                </p>
                            </div>
                        </div>

                        <!-- Bank Transfer -->
                        <div v-if="paymentSettings.bank_transfer_enabled" class="border-2 border-blue-200 rounded-xl p-6 hover:border-blue-400 transition">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                                Transfer Bank
                            </h3>
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm text-gray-600">Bank</p>
                                    <p class="text-xl font-bold text-gray-800">{{ paymentSettings.bank_name }}</p>
                                </div>
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm text-gray-600">No. Rekening</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-2xl font-bold text-blue-700 tracking-wider">{{ paymentSettings.account_number }}</p>
                                        <button 
                                            @click="copyToClipboard(paymentSettings.account_number)"
                                            class="p-2 hover:bg-blue-200 rounded transition"
                                            title="Salin nomor rekening"
                                        >
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600">Atas Nama</p>
                                    <p class="font-bold text-gray-800">{{ paymentSettings.account_name }}</p>
                                </div>
                            </div>
                            <div class="mt-4 bg-blue-50 rounded-lg p-3">
                                <p class="text-sm text-blue-800 font-medium">
                                    ✓ Transfer sejumlah: <strong>{{ formatRupiah(pelanggan.jumlah_bayar) }}</strong><br>
                                    ✓ Simpan bukti transfer<br>
                                    ✓ Konfirmasi ke admin via WhatsApp
                                </p>
                            </div>
                        </div>

                        <!-- Jika tidak ada metode -->
                        <div v-if="!paymentSettings.qris_enabled && !paymentSettings.bank_transfer_enabled" class="border-2 border-gray-200 rounded-xl p-6 text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-600 font-medium">Metode pembayaran online belum tersedia.</p>
                            <p class="text-gray-500 text-sm mt-2">Silakan hubungi petugas untuk pembayaran langsung.</p>
                        </div>
                    </div>
                </div>

                <!-- Instruksi -->
                <div v-if="paymentSettings.payment_instructions && pelanggan.kategori !== 'sosial' && !pelanggan.sudah_bayar" class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-yellow-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Catatan Penting
                    </h3>
                    <p class="text-yellow-800 text-sm">{{ paymentSettings.payment_instructions }}</p>
                </div>

                <!-- Tombol Konfirmasi WhatsApp -->
                <div v-if="pelanggan.kategori !== 'sosial' && !pelanggan.sudah_bayar" class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4">Sudah Melakukan Pembayaran?</h3>
                    <button
                        @click="konfirmasiPembayaran"
                        class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 group"
                    >
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Konfirmasi Pembayaran</span>
                    </button>
                    <p class="text-sm text-gray-500 text-center mt-3">
                        Kirim bukti pembayaran Anda untuk diverifikasi admin
                    </p>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-6">
                    <a href="/" class="text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Pembayaran -->
        <div v-if="showKonfirmasiModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeKonfirmasiModal">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Konfirmasi Pembayaran</h3>
                    
                    <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-sm text-blue-700">
                            <strong>{{ pelanggan.nama_pelanggan }}</strong> ({{ pelanggan.id_pelanggan }})
                        </p>
                        <p class="text-sm text-blue-700">
                            Tagihan: <strong>{{ formatRupiah(pelanggan.jumlah_bayar) }}</strong>
                        </p>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Bukti Transfer (Opsional)
                            </label>
                            <input
                                type="file"
                                @change="handleFileUpload"
                                accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800"
                            />
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG (Max 2MB)</p>
                        </div>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                            <p class="text-xs text-yellow-700">
                                Setelah submit, admin akan memverifikasi pembayaran Anda. Status akan diupdate maksimal 1x24 jam.
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex gap-3">
                        <button
                            @click="submitKonfirmasi"
                            :disabled="isSubmitting"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50"
                        >
                            {{ isSubmitting ? 'Mengirim...' : 'Kirim Konfirmasi' }}
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
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    pelanggan: Object,
    paymentSettings: Object,
    tagihan: Object, // Tambahkan prop tagihan
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening berhasil disalin!');
    });
};

const showKonfirmasiModal = ref(false);
const buktiTransfer = ref(null);
const isSubmitting = ref(false);

const konfirmasiPembayaran = () => {
    showKonfirmasiModal.value = true;
};

const handleFileUpload = (event) => {
    buktiTransfer.value = event.target.files[0];
};

const submitKonfirmasi = async () => {
    isSubmitting.value = true;
    
    const formData = new FormData();
    formData.append('id_pelanggan', props.pelanggan.id_pelanggan);
    if (buktiTransfer.value) {
        formData.append('bukti_transfer', buktiTransfer.value);
    }
    
    try {
        const response = await axios.post('/konfirmasi-pembayaran', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        alert(response.data.message);
        showKonfirmasiModal.value = false;
        window.location.reload();
    } catch (error) {
        alert(error.response?.data?.message || 'Gagal mengirim konfirmasi pembayaran');
    } finally {
        isSubmitting.value = false;
    }
};

const closeKonfirmasiModal = () => {
    showKonfirmasiModal.value = false;
    buktiTransfer.value = null;
};
</script>
