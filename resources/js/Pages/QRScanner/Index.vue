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
                    <!-- Camera View -->
                    <div v-if="!scannedData" class="relative">
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
                        
                        <!-- Camera Status -->
                        <div class="mt-4 text-center">
                            <p v-if="cameraError" class="text-red-600 text-sm">
                                {{ cameraError }}
                            </p>
                            <p v-else-if="cameraLoading" class="text-blue-600 text-sm">
                                ⏳ Memuat kamera...
                            </p>
                            <p v-else class="text-gray-600">
                                Arahkan kamera ke QR code pelanggan
                            </p>
                            
                            <!-- Help Instructions -->
                            <div v-if="cameraError" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                                <p class="font-semibold text-sm text-yellow-800 mb-2">💡 Cara Mengaktifkan Kamera:</p>
                                <ul class="text-xs text-yellow-700 space-y-2">
                                    <li><strong>Chrome Desktop:</strong> Klik ikon kunci/kamera di address bar → Izinkan kamera</li>
                                    <li><strong>Chrome Mobile:</strong> Settings → Site Settings → Camera → Izinkan untuk situs ini</li>
                                    <li><strong>Safari iOS:</strong> Settings → Safari → Camera → Ask atau Allow</li>
                                    <li><strong>Firefox:</strong> Klik ikon kamera dengan garis merah di address bar → Izinkan akses</li>
                                    <li class="pt-2 border-t border-yellow-300"><strong>Penting:</strong> Situs harus menggunakan HTTPS (bukan HTTP)</li>
                                </ul>
                                <button 
                                    @click="retryCamera"
                                    class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm"
                                >
                                    🔄 Coba Lagi
                                </button>
                            </div>
                        </div>
                        
                        <!-- Manual Input Toggle -->
                        <div class="mt-4 text-center">
                            <button
                                @click="showManualInput = !showManualInput"
                                class="text-blue-600 hover:text-blue-800 underline text-sm"
                            >
                                {{ showManualInput ? 'Gunakan Kamera' : 'Input Manual ID Pelanggan' }}
                            </button>
                        </div>
                    </div>

                    <!-- Manual Input -->
                    <div v-if="showManualInput && !scannedData" class="border-t pt-4">
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

let stream = null;
let scanInterval = null;

onMounted(() => {
    checkEnvironment();
    // Auto-start camera for better UX (especially on mobile)
    setTimeout(() => {
        requestCameraPermission();
    }, 300); // Small delay to ensure DOM is ready
});

onUnmounted(() => {
    stopCamera();
});

const checkEnvironment = () => {
    // Check HTTPS
    const isHttps = window.location.protocol === 'https:';
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    
    if (!isHttps && !isLocalhost) {
        cameraError.value = '⚠️ Kamera hanya dapat diakses melalui HTTPS di production. Gunakan input manual.';
        showManualInput.value = true;
        return false;
    }
    
    // Check if getUserMedia is available
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        cameraError.value = '❌ Browser tidak mendukung akses kamera. Gunakan input manual.';
        showManualInput.value = true;
        return false;
    }
    
    return true;
};

const requestCameraPermission = async () => {
    await startCamera();
};

const startCamera = async () => {
    if (!checkEnvironment()) {
        return;
    }
    
    cameraLoading.value = true;
    cameraError.value = '';
    
    try {
        // Check permission first
        if (navigator.permissions) {
            try {
                const permissionStatus = await navigator.permissions.query({ name: 'camera' });
                
                if (permissionStatus.state === 'denied') {
                    throw new Error('Camera permission denied by user');
                }
            } catch (permErr) {
                // Permission query not supported in some browsers
            }
        }
        
        // Request camera access
        const constraints = {
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };
        
        stream = await navigator.mediaDevices.getUserMedia(constraints);
        
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
                    cameraError.value = '⚠️ Tidak dapat memulai video. Silakan coba lagi.';
                    cameraLoading.value = false;
                }
            };
        } else {
            cameraLoading.value = false;
        }
    } catch (error) {
        cameraLoading.value = false;
        console.error('Camera error:', error);
        
        // Detailed error messages
        let errorMessage = '';
        
        if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
            errorMessage = '🚫 Izin akses kamera ditolak. Silakan izinkan akses kamera di pengaturan browser Anda.';
        } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
            errorMessage = '📷 Kamera tidak ditemukan pada perangkat Anda.';
        } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
            errorMessage = '⚠️ Kamera sedang digunakan oleh aplikasi lain.';
        } else if (error.name === 'OverconstrainedError') {
            errorMessage = '⚠️ Kamera tidak memenuhi persyaratan. Mencoba dengan pengaturan alternatif...';
            
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
                errorMessage = '❌ Tidak dapat mengakses kamera dengan pengaturan apapun.';
            }
        } else if (error.name === 'TypeError') {
            errorMessage = '❌ Kamera hanya dapat diakses melalui HTTPS atau localhost.';
        } else {
            errorMessage = `❌ Error: ${error.message}`;
        }
        
        cameraError.value = errorMessage;
        showManualInput.value = true;
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

const retryCamera = () => {
    cameraError.value = '';
    requestCameraPermission();
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
