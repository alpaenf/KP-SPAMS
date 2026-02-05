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
                        <div class="aspect-video bg-black rounded-lg overflow-hidden">
                            <video
                                ref="videoElement"
                                class="w-full h-full object-cover"
                                autoplay
                                playsinline
                            ></video>
                            
                            <!-- Scanning Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="border-4 border-blue-500 w-64 h-64 rounded-lg opacity-50"></div>
                            </div>
                        </div>
                        
                        <!-- Camera Status -->
                        <div class="mt-4 text-center">
                            <p v-if="cameraError" class="text-red-600">
                                {{ cameraError }}
                            </p>
                            <p v-else class="text-gray-600">
                                Arahkan kamera ke QR code pelanggan
                            </p>
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
const scannedData = ref(null);
const loading = ref(false);
const showManualInput = ref(false);
const manualIdPelanggan = ref('');

let stream = null;
let scanInterval = null;

onMounted(() => {
    startCamera();
});

onUnmounted(() => {
    stopCamera();
});

const startCamera = async () => {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { facingMode: 'environment' } 
        });
        
        if (videoElement.value) {
            videoElement.value.srcObject = stream;
            startScanning();
        }
    } catch (error) {
        console.error('Error accessing camera:', error);
        cameraError.value = 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.';
        showManualInput.value = true;
    }
};

const stopCamera = () => {
    if (scanInterval) {
        clearInterval(scanInterval);
    }
    
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
};

const startScanning = () => {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    
    scanInterval = setInterval(() => {
        if (videoElement.value && videoElement.value.readyState === videoElement.value.HAVE_ENOUGH_DATA) {
            canvas.width = videoElement.value.videoWidth;
            canvas.height = videoElement.value.videoHeight;
            context.drawImage(videoElement.value, 0, 0, canvas.width, canvas.height);
            
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code && code.data) {
                processQRCode(code.data);
            }
        }
    }, 500);
};

const processQRCode = async (qrData) => {
    if (loading.value || scannedData.value) return;
    
    loading.value = true;
    stopCamera();
    
    try {
        const response = await fetch('/api/qr-scanner/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ id_pelanggan: qrData }),
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
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
        alert(`Terjadi kesalahan saat memproses QR code: ${error.message}`);
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

const formatBulan = (bulan) => {
    const date = new Date(bulan);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
};
</script>
