<template>
    <div v-if="shouldShow" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h5 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            💰 Informasi Cicilan
        </h5>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-blue-700">Total Tagihan:</span>
                <span class="font-bold text-blue-900">{{ formatRupiah(totalTagihan) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-blue-700">Sudah Dibayar:</span>
                <span class="font-bold text-green-600">{{ formatRupiah(jumlahTerbayar) }}</span>
            </div>
            <div class="flex justify-between border-t border-blue-200 pt-2 mt-2">
                <span class="text-blue-700 font-bold">Sisa Tagihan:</span>
                <span class="font-bold text-red-600">{{ formatRupiah(sisaTagihan) }}</span>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="flex justify-between text-xs text-blue-700 mb-1">
                <span>Progress Pembayaran</span>
                <span>{{ persentase }}%</span>
            </div>
            <div class="w-full bg-blue-100 rounded-full h-2.5">
                <div 
                    class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                    :style="{ width: persentase + '%' }"
                ></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    totalTagihan: {
        type: Number,
        required: true
    },
    jumlahTerbayar: {
        type: Number,
        default: 0
    },
    statusBayar: {
        type: String,
        default: 'BELUM_BAYAR'
    }
});

const sisaTagihan = computed(() => {
    return Math.max(0, props.totalTagihan - props.jumlahTerbayar);
});

const persentase = computed(() => {
    if (props.totalTagihan === 0) return 0;
    return Math.min(100, Math.round((props.jumlahTerbayar / props.totalTagihan) * 100));
});

const shouldShow = computed(() => {
    // Tampilkan jika:
    // 1. Belum lunas (BELUM_BAYAR)
    // 2. Ada tagihan (total > 0)
    // 3. Sudah ada pembayaran sebagian (jumlah_terbayar > 0) ATAU masih ada sisa tagihan
    if (props.statusBayar === 'SUDAH_BAYAR') return false;
    if (props.totalTagihan === 0) return false;
    
    // Tampilkan jika ada cicilan (sudah bayar sebagian) atau tunggakan dengan info
    return props.jumlahTerbayar > 0 || props.totalTagihan > 0;
});

const formatRupiah = (value) => {
    if (!value) return 'Rp 0';
    return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};
</script>
