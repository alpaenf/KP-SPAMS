<template>
    <AppLayout>
        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Scan QR Code Pelanggan</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Scan QR code pada kartu pelanggan untuk input meteran
                    </p>
                </div>

            <!-- Camera Scanner -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="space-y-4">
                    <!-- Pilihan Metode Scan -->
                    <div v-if="!scannedData && !cameraStarted" class="text-center space-y-4">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Pilih Cara Scan QR Code</h2>
                        
                        <!-- Loading overlay during image processing -->
                        <div v-if="loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                            <div class="bg-white rounded-lg p-6 text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <p class="text-gray-700 font-medium">Memproses gambar...</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Option 1: Camera -->
                            <button
                                @click="requestCameraPermission"
                                class="p-6 border-2 border-blue-500 rounded-lg hover:bg-blue-50 transition-colors group"
                            >
                                <svg class="w-16 h-16 mx-auto mb-3 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div class="font-semibold text-gray-800">Scan Live</div>
                                <div class="text-xs text-gray-500 mt-1">Real-time scanning</div>
                            </button>
                            
                            <!-- Option 2: Upload Image -->
                            <button
                                @click="triggerFileInput"
                                class="p-6 border-2 border-green-500 rounded-lg hover:bg-green-50 transition-colors group"
                            >
                                <svg class="w-16 h-16 mx-auto mb-3 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="font-semibold text-gray-800">Upload Foto</div>
                                <div class="text-xs text-gray-500 mt-1">Tanpa izin kamera</div>
                            </button>
                            
                            <!-- Option 3: Manual Input -->
                            <button
                                @click="showManualInput = true"
                                class="p-6 border-2 border-yellow-500 rounded-lg hover:bg-yellow-50 transition-colors group"
                            >
                                <svg class="w-16 h-16 mx-auto mb-3 text-yellow-600 group-hover:text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <div class="font-semibold text-gray-800">Input Manual</div>
                                <div class="text-xs text-gray-500 mt-1">Ketik ID pelanggan</div>
                            </button>
                        </div>
                        
                        <!-- Hidden File Input -->
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            @change="handleImageUpload"
                            class="hidden"
                        />
                        
                        <!-- Error Display -->
                        <div v-if="cameraError" class="p-4 bg-red-50 border-2 border-red-300 rounded-lg">
                            <div class="flex items-start gap-2 mb-3">
                                <svg class="w-6 h-6 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-red-900 mb-1">{{ cameraError }}</p>
                                    <p class="text-xs text-red-700">
                                        Gunakan <strong>"Upload Foto"</strong> atau <strong>"Input Manual"</strong> sebagai alternatif yang lebih mudah.
                                    </p>
                                </div>
                            </div>
                            <button 
                                @click="cameraError = ''"
                                class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs font-medium"
                            >
                                Tutup Pesan
                            </button>
                        </div>
                        
                        <!-- Info Note -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-left">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm text-blue-800 mb-2">
                                        <strong>Tips:</strong> Jika popup izin kamera tidak muncul atau sudah ditolak, gunakan opsi <strong>"Upload Foto"</strong> atau <strong>"Input Manual"</strong> sebagai alternatif.
                                    </p>
                                    <details class="text-xs text-blue-700 mt-2">
                                        <summary class="cursor-pointer font-semibold hover:text-blue-900">Cara Reset Izin Kamera (klik untuk lihat)</summary>
                                        <ul class="mt-2 space-y-1 ml-4 list-disc">
                                            <li><strong>Chrome:</strong> Klik ikon 🔒 atau 🎥 di sebelah kiri URL → Kamera → Izinkan → Refresh (F5)</li>
                                            <li><strong>Mobile Chrome:</strong> Menu ⋮ → Info Situs → Izin → Kamera → Izinkan → Refresh</li>
                                            <li><strong>Firefox:</strong> Klik ikon 🛡️ di address bar → Hapus izin → Refresh</li>
                                            <li><strong>Safari:</strong> Settings → Safari → Camera → Allow</li>
                                        </ul>
                                    </details>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camera View -->
                    <div v-if="!scannedData && cameraStarted" class="relative">
                        <div class="relative aspect-[3/4] bg-black rounded-lg overflow-hidden shadow-inner">
                            <video
                                ref="videoElement"
                                class="w-full h-full object-cover"
                                autoplay
                                playsinline
                            ></video>
                            
                            <!-- Scanning Overlay - Fixed positioning -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="relative">
                                    <!-- Kotak scanner dengan garis sudut - DIPERBESAR -->
                                    <div class="w-64 h-64 sm:w-72 sm:h-72 md:w-80 md:h-80 lg:w-96 lg:h-96 relative">
                                        <!-- Border sudut kiri atas -->
                                        <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-blue-500"></div>
                                        <!-- Border sudut kanan atas -->
                                        <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-blue-500"></div>
                                        <!-- Border sudut kiri bawah -->
                                        <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-blue-500"></div>
                                        <!-- Border sudut kanan bawah -->
                                        <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-blue-500"></div>
                                        
                                        <!-- Garis animasi scan -->
                                        <div class="absolute inset-0 overflow-hidden">
                                            <div class="h-0.5 w-full bg-gradient-to-r from-transparent via-blue-400 to-transparent animate-scan"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Label -->
                                    <div class="mt-4 text-center">
                                        <p class="text-white text-sm font-medium bg-black/50 px-3 py-1 rounded-full">Arahkan QR Code ke sini</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Back to Options Button -->
                        <div class="mt-4 text-center">
                            <button
                                @click="backToOptions"
                                class="text-gray-600 hover:text-gray-800 underline text-sm"
                            >
                                ← Kembali ke Pilihan Scan
                            </button>
                        </div>
                        
                        <!-- Camera Status -->
                        <div class="mt-4 text-center">
                            <p v-if="cameraError" class="text-red-600 text-sm font-medium">
                                {{ cameraError }}
                            </p>
                            <div v-else-if="cameraLoading" class="flex items-center justify-center gap-2 text-blue-600 text-sm">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memuat kamera...</span>
                            </div>
                            <p v-else class="text-gray-600">
                                <span class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    Scanning QR code secara real-time...
                                </span>
                            </p>
                            
                            <!-- Help Instructions -->
                            <div v-if="cameraError" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                                <div class="flex items-start gap-2 mb-3">
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-sm text-yellow-800 mb-2">Cara Mengaktifkan Kamera:</p>
                                        <div class="text-xs text-yellow-700 space-y-2">
                                            <p class="font-semibold text-red-600">Jika popup izin tidak muncul:</p>
                                            <ul class="space-y-2 ml-4 list-disc">
                                                <li><strong>Chrome Desktop:</strong> Klik ikon kunci/kamera di address bar → Pilih "Kamera" → "Izinkan" → Refresh (F5)</li>
                                                <li><strong>Chrome Mobile:</strong> Menu (3 titik) → Setelan Situs → Kamera → Izinkan → Refresh</li>
                                                <li><strong>Safari iOS:</strong> Settings → Safari → Camera → Allow</li>
                                                <li><strong>Firefox:</strong> Klik ikon kamera di address bar → Hapus blokir → Refresh</li>
                                                <li class="pt-2 border-t border-yellow-300"><strong class="text-red-600">Setelah ubah setting, REFRESH HALAMAN</strong></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <button 
                                        @click="retryCamera"
                                        class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm font-medium inline-flex items-center justify-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Coba Lagi
                                    </button>
                                    <button 
                                        @click="reloadPage"
                                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium inline-flex items-center justify-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Refresh Halaman
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <!-- Manual Input -->
                    <div v-if="showManualInput && !scannedData" class="space-y-4">
                        <div class="text-center">
                            <button
                                @click="backToOptions"
                                class="text-gray-600 hover:text-gray-800 underline text-sm mb-4"
                            >
                                ← Kembali ke Pilihan Scan
                            </button>
                        </div>
                        <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ID Pelanggan
                        </label>
                        <div class="flex gap-2">
                            <input
                                v-model="manualIdPelanggan"
                                type="text"
                                placeholder="Masukkan ID Pelanggan"
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                @keyup.enter="scanManual"
                            />
                            <button
                                @click="scanManual"
                                :disabled="!manualIdPelanggan || loading"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
                            >
                                {{ loading ? 'Memproses...' : 'Cari' }}
                            </button>
                        </div>
                        </div>
                    </div>

                    <!-- Scanned Data Display -->
                    <div v-if="scannedData" class="border-t pt-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-green-800 font-medium">QR Code berhasil di-scan!</span>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="bg-blue-50 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pelanggan</h3>
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ID Pelanggan</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ scannedData.pelanggan.id_pelanggan }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ scannedData.pelanggan.nama_pelanggan }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">RT/RW</dt>
                                    <dd class="mt-1 text-sm text-gray-900">RT {{ scannedData.pelanggan.rt }} / RW {{ scannedData.pelanggan.rw }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Wilayah</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ scannedData.pelanggan.wilayah || '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ scannedData.pelanggan.kategori || '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span :class="scannedData.pelanggan.status_aktif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                              class="px-2 py-1 text-xs font-semibold rounded-full">
                                            {{ scannedData.pelanggan.status_aktif ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Last Meter Reading -->
                        <div v-if="scannedData.tagihan_terbaru" class="bg-yellow-50 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Meteran Terakhir</h3>
                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Bulan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatBulan(scannedData.tagihan_terbaru.bulan) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Meteran Terakhir</dt>
                                    <dd class="mt-1 text-lg text-gray-900 font-bold">{{ scannedData.tagihan_terbaru.meteran_sesudah }} m³</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pemakaian</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ scannedData.tagihan_terbaru.pemakaian_kubik }} m³</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-4">
                            <button
                                @click="goToInputMeteran"
                                class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-semibold"
                            >
                                Input Meteran
                            </button>
                            <button
                                @click="resetScanner"
                                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                                Scan Ulang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Cara Penggunaan</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Arahkan kamera ke QR code pada kartu pelanggan</li>
                                <li>Tunggu hingga QR code terdeteksi otomatis</li>
                                <li>Periksa data pelanggan yang muncul</li>
                                <li>Klik "Input Meteran" untuk melanjutkan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import jsQR from 'jsqr';

const videoElement = ref(null);
const cameraError = ref('');
const cameraLoading = ref(false);
const cameraStarted = ref(false);
const scannedData = ref(null);
const loading = ref(false);
const showManualInput = ref(false);
const manualIdPelanggan = ref('');
const fileInput = ref(null);

let stream = null;
let scanInterval = null;

onMounted(() => {
    checkEnvironment();
    // Don't auto-start camera to avoid permission issues
    // Let user choose their preferred method
});

onUnmounted(() => {
    stopCamera();
});

const checkEnvironment = () => {
    // Check HTTPS
    const isHttps = window.location.protocol === 'https:';
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    if (!isHttps && !isLocalhost) {
        cameraError.value = 'Kamera hanya dapat diakses melalui HTTPS di production. Gunakan input manual.';
        showManualInput.value = true;
        return false;
    }
    
    // Check if getUserMedia is available
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        cameraError.value = 'Browser tidak mendukung akses kamera. Gunakan input manual.';
        showManualInput.value = true;
        return false;
    }
    
    return true;
};

const requestCameraPermission = async () => {
    console.log('[QR Scanner] requestCameraPermission called');
    
    // Pre-check permission state to give user better guidance
    if (navigator.permissions) {
        try {
            const permissionStatus = await navigator.permissions.query({ name: 'camera' });
            console.log('[QR Scanner] Current permission state:', permissionStatus.state);
            
            if (permissionStatus.state === 'denied') {
                // Permission is permanently blocked
                cameraError.value = 'Izin kamera DIBLOKIR oleh browser. Klik detail di bawah untuk cara membuka blokir, atau gunakan Upload Foto.';
                alert('⚠️ Izin Kamera Diblokir!\\n\\nAnda pernah memblokir akses kamera untuk situs ini.\\n\\nUntuk mengaktifkan:\\n1. Klik ikon kunci (🔒) di sebelah kiri URL\\n2. Cari setting \"Kamera\"\\n3. Ubah ke \"Izinkan\"\\n4. Refresh halaman (F5)\\n\\nATAU gunakan opsi \"Upload Foto\" untuk scan tanpa kamera.');
                return;
            } else if (permissionStatus.state === 'granted') {
                console.log('[QR Scanner] Permission already granted, starting camera...');
            } else {
                console.log('[QR Scanner] Permission state is prompt, will request...');
            }
        } catch (permErr) {
            console.log('[QR Scanner] Cannot query permission:', permErr);
            // Continue to request anyway
        }
    }
    
    await startCamera();
};

const startCamera = async () => {
    if (!checkEnvironment()) {
        return;
    }
    
    cameraLoading.value = true;
    cameraError.value = '';
    
    // Log untuk debugging
    console.log('[QR Scanner] Starting camera request...');
    console.log('[QR Scanner] Protocol:', window.location.protocol);
    console.log('[QR Scanner] Hostname:', window.location.hostname);
    
    try {
        // Directly request camera access - this will trigger browser permission prompt
        // Don't check permission state first as it can block the prompt from showing
        const constraints = {
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };
        
        console.log('[QR Scanner] Requesting getUserMedia with constraints:', constraints);
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        console.log('[QR Scanner] Camera access granted!');
        
        
        if (videoElement.value) {
            videoElement.value.srcObject = stream;
            
            // Wait for video to be ready and play it
            videoElement.value.onloadedmetadata = async () => {
                try {
                    // Explicitly play video (required for mobile)
                    await videoElement.value.play();
                    cameraStarted.value = true;
                    cameraLoading.value = false;
                    startScanning();
                } catch (playError) {
                    console.error('Video play error:', playError);
                    cameraError.value = 'Tidak dapat memulai video. Silakan coba lagi.';
                    cameraLoading.value = false;
                }
            };
        } else {
            cameraLoading.value = false;
        }
    } catch (error) {
        cameraLoading.value = false;
        console.error('[QR Scanner] Camera error:', error);
        console.error('[QR Scanner] Error name:', error.name);
        console.error('[QR Scanner] Error message:', error.message);
        
        // Detailed error messages
        let errorMessage = '';
        let isPermissionDenied = false;
        
        if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError' || error.message.includes('permission denied')) {
            isPermissionDenied = true;
            errorMessage = 'Izin akses kamera ditolak atau sudah diblokir sebelumnya.';
            
            // Try to check current permission state
            if (navigator.permissions) {
                try {
                    const permissionStatus = await navigator.permissions.query({ name: 'camera' });
                    console.log('[QR Scanner] Permission state:', permissionStatus.state);
                    
                    if (permissionStatus.state === 'denied') {
                        errorMessage = 'Izin kamera DIBLOKIR oleh browser. Ikuti petunjuk di bawah untuk membuka blokir.';
                    } else if (permissionStatus.state === 'prompt') {
                        errorMessage = 'Popup izin kamera mungkin diblokir oleh browser. Cek popup blocker atau gunakan metode Upload Foto.';
                    }
                } catch (permErr) {
                    console.log('[QR Scanner] Cannot check permission state:', permErr);
                }
            }
        } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
            errorMessage = 'Kamera tidak ditemukan pada perangkat Anda.';
        } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
            errorMessage = 'Kamera sedang digunakan oleh aplikasi lain.';
        } else if (error.name === 'OverconstrainedError') {
            errorMessage = 'Kamera tidak memenuhi persyaratan. Mencoba dengan pengaturan alternatif...';
            
            // Retry with simpler constraints
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                if (videoElement.value) {
                    videoElement.value.srcObject = stream;
                    videoElement.value.onloadedmetadata = async () => {
                        try {
                            await videoElement.value.play();
                            cameraStarted.value = true;
                            startScanning();
                        } catch (playError) {
                            console.error('Video play error:', playError);
                        }
                    };
                    return;
                }
            } catch (retryError) {
                errorMessage = 'Tidak dapat mengakses kamera dengan pengaturan apapun.';
            }
        } else if (error.name === 'TypeError') {
            errorMessage = 'Kamera hanya dapat diakses melalui HTTPS atau localhost.';
        } else {
            errorMessage = `Error: ${error.message}`;
        }
        
        cameraError.value = errorMessage;
        
        // Show manual input if permission denied
        if (isPermissionDenied) {
            console.log('[QR Scanner] Permission denied - showing alternative options');
            // Don't auto-switch to manual, let user see error and instructions
        } else {
            showManualInput.value = false;
        }
    }
};

const stopCamera = () => {
    cameraStarted.value = false;
    
    if (scanInterval) {
        clearInterval(scanInterval);
        scanInterval = null;
    }
    
    if (stream) {
        stream.getTracks().forEach(track => {
            track.stop();
        });
        stream = null;
    }
    
    if (videoElement.value) {
        videoElement.value.srcObject = null;
    }
};

const startScanning = () => {
    // Clear any existing interval
    if (scanInterval) {
        clearInterval(scanInterval);
        scanInterval = null;
    }
    
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    let attempts = 0;
    const maxAttempts = 5;
    
    scanInterval = setInterval(() => {
        if (!videoElement.value) {
            return;
        }
        
        // Check if video is ready
        if (videoElement.value.readyState === videoElement.value.HAVE_ENOUGH_DATA) {
            attempts = 0; // Reset attempts on success
            
            canvas.width = videoElement.value.videoWidth;
            canvas.height = videoElement.value.videoHeight;
            
            if (canvas.width === 0 || canvas.height === 0) {
                return;
            }
            
            try {
                context.drawImage(videoElement.value, 0, 0, canvas.width, canvas.height);
                
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert' // Faster scanning
                });
                
                if (code && code.data) {
                    processQRCode(code.data);
                }
            } catch (err) {
                console.error('Scanning error:', err);
            }
        } else {
            // Video not ready yet
            attempts++;
            if (attempts > maxAttempts) {
                console.warn('Video not ready after', maxAttempts, 'attempts. ReadyState:', videoElement.value.readyState);
                attempts = 0; // Reset to keep trying
            }
        }
    }, 250);
};

const processQRCode = async (qrData) => {
    if (loading.value || scannedData.value) return;
    
    loading.value = true;
    stopCamera();
    
    try {
        // Gunakan fetchWithCsrfRetry untuk auto-retry jika CSRF error
        const response = await window.fetchWithCsrfRetry('/api/qr-scanner/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_pelanggan: qrData }),
        });
        
        // Handle specific error status
        if (response.status === 419) {
            alert('Sesi Anda telah berakhir. Silakan refresh halaman dan coba lagi.');
            setTimeout(() => window.location.reload(), 2000);
            return;
        }
        
        // Handle rate limiting (429)
        if (response.status === 429) {
            const errorData = await response.json().catch(() => ({}));
            alert(errorData.message || 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.');
            startCamera();
            loading.value = false;
            return;
        }
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            scannedData.value = data;
        } else {
            alert(data.message || 'Pelanggan tidak ditemukan.');
            startCamera();
        }
    } catch (error) {
        console.error('Error processing QR code:', error);
        
        // User-friendly error message
        const errorMessage = error.message.includes('Failed to fetch') 
            ? 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.'
            : `Terjadi kesalahan saat memproses QR code: ${error.message}`;
        
        alert(errorMessage);
        startCamera();
    } finally {
        loading.value = false;
    }
};

const scanManual = async () => {
    if (!manualIdPelanggan.value) return;
    
    await processQRCode(manualIdPelanggan.value);
};

const goToInputMeteran = () => {
    if (scannedData.value) {
        router.visit(`/qr-scanner/input-meteran/${scannedData.value.pelanggan.id}`);
    }
};

const resetScanner = () => {
    scannedData.value = null;
    manualIdPelanggan.value = '';
    showManualInput.value = false;
    startCamera();
};

const retryCamera = async () => {
    cameraError.value = '';
    // Simply call startCamera, don't pre-check permission state
    // This allows the browser to show the permission prompt again
    requestCameraPermission();
};

const reloadPage = () => {
    window.location.reload();
};

const backToOptions = () => {
    stopCamera();
    cameraStarted.value = false;
    showManualInput.value = false;
    cameraError.value = '';
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleImageUpload = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;
    
    loading.value = true;
    cameraError.value = '';
    
    try {
        // Create image element
        const img = new Image();
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        // Load image
        await new Promise((resolve, reject) => {
            img.onload = resolve;
            img.onerror = reject;
            img.src = URL.createObjectURL(file);
        });
        
        // Set canvas size to image size
        canvas.width = img.width;
        canvas.height = img.height;
        
        // Draw image on canvas
        ctx.drawImage(img, 0, 0);
        
        // Get image data
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        
        // Try to decode QR code
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'attemptBoth',
        });
        
        if (code) {
            await processQRCode(code.data);
        } else {
            cameraError.value = 'Tidak dapat mendeteksi QR code pada gambar. Pastikan foto jelas dan QR code terlihat.';
        }
        
        // Clean up
        URL.revokeObjectURL(img.src);
    } catch (error) {
        console.error('Image upload error:', error);
        cameraError.value = 'Gagal memproses gambar. Silakan coba lagi dengan foto yang lebih jelas.';
    } finally {
        loading.value = false;
        // Reset file input
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
};

const formatBulan = (bulan) => {
    const date = new Date(bulan);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
};
</script>

<style scoped>
@keyframes scan {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(100%);
    }
    100% {
        transform: translateY(0);
    }
}

.animate-scan {
    animation: scan 2s ease-in-out infinite;
}
</style>
