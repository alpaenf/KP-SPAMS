<template>
    <AppLayout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-50 py-8 px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <button
                        @click="goBack"
                        class="text-blue-600 hover:text-blue-800 mb-4 flex items-center gap-2 font-medium"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Kembali ke Scanner
                    </button>
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Input Meteran Air</h1>
                        <p class="text-gray-600">Masukkan pembacaan meteran air bulan ini</p>
                    </div>
                </div>

                <!-- Info Pelanggan Card -->
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
                            <p class="text-sm text-gray-500">RT / RW</p>
                            <p class="font-bold text-gray-800">RT {{ pelanggan.rt }} / RW {{ pelanggan.rw }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Wilayah</p>
                            <p class="font-bold text-gray-800">{{ pelanggan.wilayah || '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Meteran Terakhir -->
                <div v-if="tagihan_terbaru" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 mb-6 text-white">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Meteran Terakhir
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                            <p class="text-blue-100 text-sm mb-1">Bulan</p>
                            <p class="text-xl font-bold">{{ formatBulan(tagihan_terbaru.bulan) }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                            <p class="text-blue-100 text-sm mb-1">Meteran Akhir</p>
                            <p class="text-3xl font-bold">{{ tagihan_terbaru.meteran_sesudah }}</p>
                            <p class="text-blue-100 text-xs">m³</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                            <p class="text-blue-100 text-sm mb-1">Pemakaian</p>
                            <p class="text-2xl font-bold">{{ tagihan_terbaru.pemakaian_kubik }}</p>
                            <p class="text-blue-100 text-xs">m³</p>
                        </div>
                    </div>
                </div>

                <!-- Form Input Meteran -->
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Form Input Meteran Baru
                    </h2>
                    
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Bulan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Bulan <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.bulan"
                                type="month"
                                required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            />
                        </div>

                        <!-- Meteran Sebelum (Read Only) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Meteran Sebelum
                            </label>
                            <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span class="text-2xl font-bold text-gray-600">{{ meteranSebelum }}</span>
                                <span class="text-sm text-gray-500 ml-auto">m³</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Meteran terakhir yang tercatat</p>
                        </div>

                        <!-- Meteran Sesudah -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Meteran Sesudah (Pembacaan Baru) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    v-model.number="form.meteran_sesudah"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    placeholder="Masukkan angka meteran saat ini"
                                    class="w-full px-4 py-3 pr-12 border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-lg font-semibold"
                                    :class="{ 'border-red-500': form.meteran_sesudah && form.meteran_sesudah < meteranSebelum }"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">m³</span>
                            </div>
                            <p v-if="form.meteran_sesudah && form.meteran_sesudah < meteranSebelum" class="text-xs text-red-500 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                Meteran sesudah harus lebih besar atau sama dengan meteran sebelum!
                            </p>
                            <p v-else class="text-xs text-gray-500 mt-1">Masukkan angka yang tertera pada meteran air</p>
                        </div>

                        <!-- Perhitungan Otomatis -->
                        <div v-if="form.meteran_sesudah >= meteranSebelum" class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Perhitungan Tagihan
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="bg-white rounded-lg p-4 border border-green-100">
                                    <p class="text-sm text-gray-600 mb-1">Pemakaian Air</p>
                                    <p class="text-3xl font-bold text-green-600">{{ pemakaianKubik.toFixed(2) }}</p>
                                    <p class="text-xs text-gray-500">m³</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-green-100">
                                    <p class="text-sm text-gray-600 mb-1">Tarif per m³</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ formatRupiah(tarif_aktif.tarif_per_kubik) }}</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-4 border-2 border-green-200">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Biaya Pemakaian</span>
                                        <span class="font-semibold">{{ pemakaianDitagih.toFixed(2) }} m³ × {{ formatRupiah(tarif_aktif.tarif_per_kubik) }} = {{ formatRupiah(biayaPemakaian) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Biaya Abunemen</span>
                                        <span class="font-semibold">{{ formatRupiah(tarif_aktif.biaya_abunemen) }}</span>
                                    </div>
                                    <div class="border-t-2 border-gray-200 pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold text-gray-800">Total Tagihan</span>
                                            <span class="text-2xl font-bold text-green-600">{{ formatRupiah(estimasiTagihan) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Keterangan (Opsional)
                            </label>
                            <textarea
                                v-model="form.keterangan"
                                rows="3"
                                placeholder="Catatan tambahan (jika ada)..."
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4">
                            <button
                                type="button"
                                @click="goBack"
                                class="flex-1 px-6 py-4 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="loading || !form.meteran_sesudah || form.meteran_sesudah < meteranSebelum"
                                class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>{{ loading ? 'Menyimpan...' : 'Simpan Meteran' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Berhasil Disimpan!</h3>
                    <p class="text-gray-600 mb-6">Data meteran berhasil dicatat dan tagihan telah dibuat</p>
                    
                    <div v-if="savedTagihan" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 mb-6 border-2 border-green-200">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Pemakaian Air</span>
                                <span class="text-lg font-bold text-gray-800">{{ savedTagihan.pemakaian_kubik }} m³</span>
                            </div>
                            <div class="border-t border-green-200 pt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700 font-semibold">Total Tagihan</span>
                                    <span class="text-2xl font-bold text-green-600">Rp {{ formatRupiah(savedTagihan.total_tagihan) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button
                            @click="scanAnother"
                            class="flex-1 px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition"
                        >
                            Scan Lagi
                        </button>
                        <button
                            @click="goToDashboard"
                            class="flex-1 px-5 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition"
                        >
                            Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    pelanggan: Object,
    tagihan_terbaru: Object,
    tarif_aktif: Object,
});

const form = ref({
    pelanggan_id: props.pelanggan.id,
    bulan: getCurrentMonth(),
    meteran_sesudah: null,
    keterangan: '',
});

const loading = ref(false);
const showSuccessModal = ref(false);
const savedTagihan = ref(null);

const meteranSebelum = computed(() => {
    return props.tagihan_terbaru ? props.tagihan_terbaru.meteran_sesudah : 0;
});

const pemakaianKubik = computed(() => {
    const meteran = parseFloat(form.value.meteran_sesudah);
    if (!meteran || isNaN(meteran) || form.value.meteran_sesudah === '' || form.value.meteran_sesudah === null) {
        return 0;
    }
    const pemakaian = meteran - meteranSebelum.value;
    return Math.max(0, pemakaian);
});

const pemakaianDitagih = computed(() => {
    const pemakaian = pemakaianKubik.value;
    const minimal = props.tarif_aktif.minimal_pemakaian || 10;
    return Math.max(pemakaian, minimal);
});

const biayaPemakaian = computed(() => {
    return pemakaianDitagih.value * props.tarif_aktif.tarif_per_kubik;
});

const estimasiTagihan = computed(() => {
    if (!form.value.meteran_sesudah || form.value.meteran_sesudah === '') return 0;
    
    return biayaPemakaian.value + props.tarif_aktif.biaya_abunemen;
});

const isFormValid = computed(() => {
    const meteran = parseFloat(form.value.meteran_sesudah);
    return form.value.bulan && 
           form.value.meteran_sesudah !== '' && 
           !isNaN(meteran) &&
           meteran >= meteranSebelum.value;
});

function getCurrentMonth() {
    const date = new Date();
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    return `${year}-${month}`;
}

async function submitForm() {
    if (!isFormValid.value || loading.value) return;
    
    loading.value = true;
    
    try {
        const response = await fetch('/api/qr-scanner/store-meteran', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            credentials: 'same-origin', // FIX: Include cookies untuk session/CSRF
            body: JSON.stringify(form.value),
        });
        
        const data = await response.json();
        
        if (data.success) {
            savedTagihan.value = data.tagihan;
            showSuccessModal.value = true;
        } else {
            alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('Terjadi kesalahan saat menyimpan data.');
    } finally {
        loading.value = false;
    }
}

function scanAnother() {
    router.visit('/qr-scanner');
}

function goToDashboard() {
    router.visit('/dashboard');
}

function goBack() {
    router.visit('/qr-scanner');
}

function formatBulan(bulan) {
    const date = new Date(bulan);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long' });
}

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
}
</script>
