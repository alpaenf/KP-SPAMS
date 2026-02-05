<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Print QR Code Pelanggan</h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Preview dan cetak QR code untuk pelanggan
                    </p>
                </div>
                <div class="flex gap-3">
                    <a
                        :href="'/qr-code/bulk-download-pdf?' + buildQueryString()"
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Download PDF
                    </a>
                    <button
                        @click="printAllQRCodes"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Semua
                    </button>
                    <Link
                        href="/cek-pelanggan"
                        class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
                    >
                        Kembali
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Filter Pelanggan</h3>
                <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Wilayah</label>
                        <select
                            v-model="filters.wilayah"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">Semua Wilayah</option>
                            <option value="Dawuhan">Dawuhan</option>
                            <option value="Kubangsari Kulon">Kubangsari Kulon</option>
                            <option value="Kubangsari Wetan">Kubangsari Wetan</option>
                            <option value="Sokarame">Sokarame</option>
                            <option value="Tiparjaya">Tiparjaya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                        <input
                            v-model="filters.rt"
                            type="text"
                            placeholder="Contoh: 001"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                        <input
                            v-model="filters.rw"
                            type="text"
                            placeholder="Contoh: 002"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            v-model="filters.status_aktif"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold"
                        >
                            Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-blue-700">
                        Menampilkan <strong>{{ pelangganList.length }}</strong> QR code pelanggan. 
                        Klik "Print Semua" untuk mencetak.
                    </span>
                </div>
            </div>

            <!-- QR Code Grid (Preview) -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 no-print">
                <h3 class="text-lg font-semibold mb-4">Preview QR Code</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <div 
                        v-for="pelanggan in pelangganList" 
                        :key="pelanggan.id"
                        class="border border-gray-200 rounded-lg p-4 text-center"
                    >
                        <img 
                            :src="pelanggan.qr_code_data_url" 
                            :alt="pelanggan.id_pelanggan"
                            class="w-full h-auto mb-2"
                        />
                        <p class="text-xs font-semibold text-gray-900">{{ pelanggan.id_pelanggan }}</p>
                        <p class="text-xs text-gray-600 truncate">{{ pelanggan.nama_pelanggan }}</p>
                        <p class="text-xs text-gray-500">RT {{ pelanggan.rt }} / RW {{ pelanggan.rw }}</p>
                    </div>
                </div>
            </div>

            <!-- Print Layout (Hidden on screen, visible on print) -->
            <div class="print-only">
                <div class="print-page">
                    <div 
                        v-for="(pelanggan, index) in pelangganList" 
                        :key="pelanggan.id"
                        class="qr-card"
                        :class="{ 'page-break': (index + 1) % 12 === 0 }"
                    >
                        <div class="qr-card-inner">
                            <div class="qr-header">
                                <h4>PAMSIMAS</h4>
                                <p>Kartu Pelanggan</p>
                            </div>
                            <div class="qr-code-container">
                                <img 
                                    :src="pelanggan.qr_code_data_url" 
                                    :alt="pelanggan.id_pelanggan"
                                />
                            </div>
                            <div class="qr-info">
                                <p class="qr-id">{{ pelanggan.id_pelanggan }}</p>
                                <p class="qr-name">{{ pelanggan.nama_pelanggan }}</p>
                                <p class="qr-address">RT {{ pelanggan.rt }} / RW {{ pelanggan.rw }}</p>
                                <p class="qr-wilayah">{{ pelanggan.wilayah }}</p>
                            </div>
                            <div class="qr-footer">
                                <p>Scan untuk Input Meteran</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';

const props = defineProps({
    pelangganList: Array,
    filters: Object,
});

const filters = ref({
    wilayah: props.filters.wilayah,
    rt: props.filters.rt,
    rw: props.filters.rw,
    status_aktif: props.filters.status_aktif,
});

const applyFilters = () => {
    router.get('/qr-code/bulk-preview', filters.value, {
        preserveState: true,
    });
};

const printAllQRCodes = () => {
    window.print();
};

const buildQueryString = () => {
    const params = new URLSearchParams();
    if (filters.value.wilayah && filters.value.wilayah !== 'all') {
        params.append('wilayah', filters.value.wilayah);
    }
    if (filters.value.rt && filters.value.rt !== 'all') {
        params.append('rt', filters.value.rt);
    }
    if (filters.value.rw && filters.value.rw !== 'all') {
        params.append('rw', filters.value.rw);
    }
    if (filters.value.status_aktif && filters.value.status_aktif !== 'all') {
        params.append('status_aktif', filters.value.status_aktif);
    }
    return params.toString();
};
</script>

<style scoped>
/* Hide on screen, show on print */
.print-only {
    display: none;
}

@media print {
    /* Hide everything except print content */
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    /* Print page settings */
    @page {
        size: A4;
        margin: 10mm;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
    
    /* QR Card Layout - 3 columns x 4 rows = 12 per page */
    .print-page {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5mm;
        width: 100%;
    }
    
    .qr-card {
        border: 1px solid #333;
        border-radius: 8px;
        padding: 8px;
        background: white;
        page-break-inside: avoid;
        height: 65mm;
        display: flex;
        flex-direction: column;
    }
    
    .page-break {
        page-break-after: always;
    }
    
    .qr-card-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
        text-align: center;
    }
    
    .qr-header {
        margin-bottom: 4px;
        border-bottom: 2px solid #333;
        padding-bottom: 4px;
    }
    
    .qr-header h4 {
        font-size: 14px;
        font-weight: bold;
        margin: 0;
        color: #1a56db;
    }
    
    .qr-header p {
        font-size: 9px;
        margin: 2px 0 0 0;
        color: #666;
    }
    
    .qr-code-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px 0;
    }
    
    .qr-code-container img {
        max-width: 100%;
        max-height: 35mm;
        width: auto;
        height: auto;
    }
    
    .qr-info {
        margin-top: 4px;
        padding-top: 4px;
        border-top: 1px solid #ddd;
    }
    
    .qr-id {
        font-size: 11px;
        font-weight: bold;
        margin: 0;
        color: #000;
    }
    
    .qr-name {
        font-size: 10px;
        font-weight: 600;
        margin: 2px 0;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .qr-address {
        font-size: 9px;
        margin: 1px 0;
        color: #666;
    }
    
    .qr-wilayah {
        font-size: 8px;
        margin: 1px 0;
        color: #999;
    }
    
    .qr-footer {
        margin-top: 4px;
        padding-top: 4px;
        border-top: 1px solid #ddd;
    }
    
    .qr-footer p {
        font-size: 8px;
        margin: 0;
        color: #666;
        font-style: italic;
    }
}
</style>
