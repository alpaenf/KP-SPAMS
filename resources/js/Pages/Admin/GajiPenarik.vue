<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    data:           { type: Array,  default: () => [] },
    bulan:          { type: String, default: '' },
    totals:         { type: Object, default: () => ({}) },
    availableBulan: { type: Array,  default: () => [] },
});

const selectedBulan = ref(props.bulan);

const filterBulan = () => {
    router.get('/admin/gaji-penarik', { bulan: selectedBulan.value }, { preserveState: true });
};

// Bulan options: combine available DB months + 12 generated months so picker is never empty
const bulanOptions = computed(() => {
    const names = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const fmt = (val) => {
        const [y, m] = val.split('-');
        return `${names[parseInt(m)-1]} ${y}`;
    };
    // Start with DB months
    const opts = props.availableBulan.map(v => ({ value: v, label: fmt(v) + ' (ada data)' }));
    // Add generated months that aren't already in the list
    const existing = new Set(props.availableBulan);
    const now = new Date();
    for (let i = 0; i < 12; i++) {
        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        const val = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}`;
        if (!existing.has(val)) {
            opts.push({ value: val, label: fmt(val) });
        }
    }
    return opts;
});

const bulanLabel = computed(() => {
    if (!selectedBulan.value) return '';
    const names = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const [y, m] = selectedBulan.value.split('-');
    return `${names[parseInt(m)-1]} ${y}`;
});

const fmt = (n) => Number(n || 0).toLocaleString('id-ID');

const wilayahColors = {
    dawuhan:           { bg: 'bg-blue-50',   border: 'border-blue-200',   title: 'text-blue-800',  icon: 'bg-blue-100' },
    kubangsari_kulon:  { bg: 'bg-purple-50',  border: 'border-purple-200', title: 'text-purple-800',icon: 'bg-purple-100' },
    kubangsari_wetan:  { bg: 'bg-indigo-50',  border: 'border-indigo-200', title: 'text-indigo-800',icon: 'bg-indigo-100' },
    sokarame:          { bg: 'bg-teal-50',    border: 'border-teal-200',   title: 'text-teal-800',  icon: 'bg-teal-100' },
    tiparjaya:         { bg: 'bg-orange-50',  border: 'border-orange-200', title: 'text-orange-800',icon: 'bg-orange-100' },
};

const getColor = (wilayah) => wilayahColors[wilayah] || { bg:'bg-gray-50', border:'border-gray-200', title:'text-gray-800', icon:'bg-gray-100' };
</script>

<template>
    <AppLayout title="Gaji Penarik">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gaji Penarik per Wilayah</h1>
                    <p class="text-sm text-gray-500 mt-1">Rekap honor & operasional penarik bulan {{ bulanLabel }}</p>
                </div>
                <!-- Bulan Picker -->
                <div class="flex items-center gap-2">
                    <select
                        v-model="selectedBulan"
                        @change="filterBulan"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 bg-white"
                    >
                        <option v-for="opt in bulanOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                </div>
            </div>

            <!-- Summary Row -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4 text-white shadow">
                    <p class="text-xs font-medium text-blue-200 uppercase tracking-wider">Total Pemasukan</p>
                    <p class="text-xl font-bold mt-1">Rp {{ fmt(totals.total_pemasukan) }}</p>
                    <p class="text-xs text-blue-200 mt-0.5">Semua wilayah</p>
                </div>
                <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4 text-white shadow">
                    <p class="text-xs font-medium text-purple-200 uppercase tracking-wider">20% Jasa Penarik</p>
                    <p class="text-xl font-bold mt-1">Rp {{ fmt(totals.jasa_20_persen) }}</p>
                    <p class="text-xs text-purple-200 mt-0.5">Hak petugas</p>
                </div>
                <div class="bg-gradient-to-br from-pink-600 to-pink-700 rounded-xl p-4 text-white shadow">
                    <p class="text-xs font-medium text-pink-200 uppercase tracking-wider">Total Honor Penarik</p>
                    <p class="text-xl font-bold mt-1">Rp {{ fmt(totals.honor_penarik) }}</p>
                    <p class="text-xs text-pink-200 mt-0.5">20% + Ops Penarik + Ops Lapangan</p>
                </div>
                <div class="bg-gradient-to-br from-teal-600 to-teal-700 rounded-xl p-4 text-white shadow">
                    <p class="text-xs font-medium text-teal-200 uppercase tracking-wider">Total Ops. Lapangan</p>
                    <p class="text-xl font-bold mt-1">Rp {{ fmt(totals.biaya_ops_lapangan) }}</p>
                    <p class="text-xs text-teal-200 mt-0.5">Semua wilayah</p>
                </div>
            </div>

            <!-- Per-Wilayah Cards -->
            <div class="space-y-6">
                <div
                    v-for="item in data"
                    :key="item.wilayah"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                >
                    <!-- Wilayah Header -->
                    <div :class="[getColor(item.wilayah).bg, getColor(item.wilayah).border, 'border-b px-6 py-4 flex items-center justify-between']">
                        <div class="flex items-center gap-3">
                            <div :class="[getColor(item.wilayah).icon, 'w-9 h-9 rounded-lg flex items-center justify-center']">
                                <svg class="w-5 h-5" :class="getColor(item.wilayah).title" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 :class="[getColor(item.wilayah).title, 'text-lg font-bold capitalize']">{{ item.wilayah_label }}</h2>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Total tarikan: <span class="font-semibold text-gray-700">Rp {{ fmt(item.total_pemasukan) }}</span>
                                </p>
                            </div>
                        </div>
                        <!-- Petugas badges -->
                        <div class="hidden sm:flex flex-wrap gap-2 justify-end max-w-xs">
                            <template v-if="item.petugas.length">
                                <span
                                    v-for="p in item.petugas"
                                    :key="p.id"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-white shadow-sm border border-gray-200 text-gray-700"
                                >
                                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                    {{ p.name }}
                                </span>
                            </template>
                            <span v-else class="text-xs text-gray-400 italic">Belum ada penarik</span>
                        </div>
                    </div>

                    <!-- 4 Cards -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5">
                        <!-- Card 1: 20% Jasa Penarik -->
                        <div class="rounded-xl border border-purple-100 bg-purple-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-purple-700">20% Jasa Penarik</p>
                            </div>
                            <p class="text-lg font-bold text-purple-900">Rp {{ fmt(item.jasa_20_persen) }}</p>
                            <p class="text-xs text-purple-500 mt-0.5">Hak petugas</p>
                        </div>

                        <!-- Card 2: Total Honor Penarik -->
                        <div class="rounded-xl border border-pink-100 bg-pink-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-pink-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-pink-700">Total Honor Penarik</p>
                            </div>
                            <p class="text-lg font-bold text-pink-900">Rp {{ fmt(item.honor_penarik) }}</p>
                            <p class="text-xs text-pink-500 mt-0.5">20% + Ops Penarik + Ops Lapangan</p>
                        </div>

                        <!-- Card 3: Biaya Ops Penarik -->
                        <div class="rounded-xl border border-orange-100 bg-orange-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-orange-700">Biaya Ops. Penarik</p>
                            </div>
                            <p class="text-lg font-bold text-orange-900">Rp {{ fmt(item.biaya_ops_penarik) }}</p>
                            <p class="text-xs text-orange-500 mt-0.5">BBM & Maintenance</p>
                        </div>

                        <!-- Card 4: Biaya Ops Lapangan -->
                        <div class="rounded-xl border border-teal-100 bg-teal-50 p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-teal-700">Biaya Ops. Lapangan</p>
                            </div>
                            <p class="text-lg font-bold text-teal-900">Rp {{ fmt(item.biaya_ops_lapangan) }}</p>
                            <p class="text-xs text-teal-500 mt-0.5">Operasional lapangan</p>
                        </div>
                    </div>

                    <!-- Mobile petugas -->
                    <div class="sm:hidden px-5 pb-4 flex flex-wrap gap-2">
                        <template v-if="item.petugas.length">
                            <span v-for="p in item.petugas" :key="p.id" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                {{ p.name }}
                            </span>
                        </template>
                        <span v-else class="text-xs text-gray-400 italic">Belum ada penarik ditugaskan</span>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="!data.length" class="text-center py-16 text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-medium">Tidak ada data</p>
            </div>

            <!-- Notes -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
                <p class="font-semibold mb-1">Keterangan Kalkulasi:</p>
                <ul class="space-y-1 text-xs">
                    <li>• <strong>20% Jasa Penarik</strong> = Total pemasukan wilayah × 20%</li>
                    <li>• <strong>Total Honor Penarik</strong> = 20% Jasa Penarik + Biaya Ops. Penarik + Biaya Ops. Lapangan</li>
                    <li>• <strong>Biaya Ops. Penarik</strong> & <strong>Biaya Ops. Lapangan</strong> diambil dari data Laporan Bulanan per wilayah</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
