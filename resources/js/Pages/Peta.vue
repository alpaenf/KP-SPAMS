<template>
    <AppLayout>
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Peta Lokasi KP-SPAMS
                    </h1>
                    <p class="text-gray-600">
                        Peta lokasi kantor, sumber air, dan pelanggan KP-SPAMS Desa
                    </p>
                </div>

                <!-- Controls -->
                <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                    <div class="flex flex-col gap-3">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama atau ID pelanggan..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                        />
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <select
                                v-model="statusFilter"
                                class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            >
                                <option value="all">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            <input
                                v-model="rtFilter"
                                type="text"
                                placeholder="RT"
                                class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <input
                                v-model="rwFilter"
                                type="text"
                                placeholder="RW"
                                class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                            />
                            <button @click="fitToBounds" class="px-4 py-3 bg-blue-800 text-white rounded-lg hover:bg-blue-900 font-medium whitespace-nowrap">Paskan Semua</button>
                        </div>
                    </div>
                </div>

                <!-- Map and Sidebar Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Map Container -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <div 
                                id="map" 
                                ref="mapContainer"
                                class="w-full h-[400px] md:h-[500px] lg:h-[600px]"
                                style="background: #e5e7eb;"
                            ></div>
                        </div>
                    </div>

                    <!-- Details Sidebar -->
                    <div v-if="selectedPelanggan" class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-2xl border border-gray-200 p-4 sticky top-6">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ selectedPelanggan.nama_pelanggan }}</h3>
                                    <p class="text-sm text-gray-600">ID: {{ selectedPelanggan.id_pelanggan }}</p>
                                    <p v-if="selectedPelanggan.rt || selectedPelanggan.rw" class="text-sm text-gray-600">RT {{ selectedPelanggan.rt }} / RW {{ selectedPelanggan.rw }}</p>
                                </div>
                                <button @click="selectedPelanggan = null" class="text-gray-500 hover:text-gray-700 text-xl">✕</button>
                            </div>
                            <div class="mb-3">
                                <span v-if="selectedPelanggan.status_aktif" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Aktif</span>
                                <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                            </div>
                            <div class="space-y-2">
                                <a v-if="selectedPelanggan.no_whatsapp" :href="waLink(selectedPelanggan.no_whatsapp)" target="_blank" class="block px-3 py-2 bg-blue-100 text-blue-800 rounded hover:bg-blue-200 text-sm text-center">Chat WhatsApp</a>
                                <a :href="`/pelanggan/${selectedPelanggan.id}/edit`" class="block px-3 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200 text-sm text-center">Edit Data</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-6 bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Legenda Peta</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-800 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                K
                            </div>
                            <span class="text-sm text-gray-700">Kantor KP-SPAMS</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                S
                            </div>
                            <span class="text-sm text-gray-700">Sumber Air</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                P
                            </div>
                            <span class="text-sm text-gray-700">Pelanggan</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                H
                            </div>
                            <span class="text-sm text-gray-700">Pelanggan Highlight</span>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Klik pada marker untuk melihat informasi detail. 
                                Gunakan zoom dan drag untuk navigasi peta.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, ref, computed, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
    kantor: {
        type: Object,
        default: () => ({
            name: 'Kantor KP-SPAMS',
            lat: -6.200000,
            lng: 106.816666,
        }),
    },
    sumberAir: {
        type: Array,
        default: () => [
            {
                name: 'Sumber Air Utama',
                lat: -6.201000,
                lng: 106.817000,
            },
        ],
    },
    pelangganList: {
        type: Array,
        default: () => [],
    },
    highlightPelanggan: String,
});

const mapContainer = ref(null);
let map = null;
let pelangganLayer = null;
const selectedPelanggan = ref(null);
const searchQuery = ref('');
const statusFilter = ref('all');
const rtFilter = ref('');
const rwFilter = ref('');

onMounted(() => {
    // Tunggu sebentar untuk pastikan DOM ready
    setTimeout(() => {
        initMap();
    }, 100);
});

const initMap = () => {
    if (!mapContainer.value) {
        console.error('Map container not found');
        return;
    }

    // Initialize map
    const centerLat = props.kantor.lat || -6.200000;
    const centerLng = props.kantor.lng || 106.816666;
    
    map = L.map('map').setView([centerLat, centerLng], 15);

    // Add OpenStreetMap tile layer with error handling
    const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    });

    tileLayer.on('tileerror', (error) => {
        console.error('Tile loading error:', error);
    });

    tileLayer.on('tileload', () => {
        console.log('Tiles loading...');
    });

    tileLayer.addTo(map);

    // Force invalidate size after a moment
    setTimeout(() => {
        map.invalidateSize();
        console.log('Map size invalidated');
    }, 200);

    // Custom icon for Kantor
    const kantorIcon = L.divIcon({
        html: '<div style="background-color: #166534; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">K</div>',
        className: 'custom-div-icon',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
    });

    // Add Kantor marker
    const kantorMarker = L.marker([props.kantor.lat, props.kantor.lng], { icon: kantorIcon }).addTo(map);
    kantorMarker.bindPopup(`
        <div style="min-width: 200px;">
            <h3 style="font-weight: bold; margin-bottom: 8px; font-size: 16px;">${props.kantor.name}</h3>
            <p style="margin-bottom: 8px; color: #666; font-size: 14px;">Sekretariat KP-SPAMS Desa</p>
            <a href="https://www.google.com/maps?q=${props.kantor.lat},${props.kantor.lng}" 
               target="_blank" 
               rel="noopener noreferrer"
               style="display: inline-flex; align-items: center; color: #166534; font-weight: 600; text-decoration: none; font-size: 14px;">
                <svg style="width: 16px; height: 16px; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd" />
                </svg>
                Buka di Google Maps
            </a>
        </div>
    `);

    // Custom icon for Sumber Air
    const sumberAirIcon = L.divIcon({
        html: '<div style="background-color: #2563eb; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">S</div>',
        className: 'custom-div-icon',
        iconSize: [32, 32],
        iconAnchor: [16, 16],
    });

    // Add Sumber Air markers
    props.sumberAir.forEach((sumber) => {
        const marker = L.marker([sumber.lat, sumber.lng], { icon: sumberAirIcon }).addTo(map);
        marker.bindPopup(`
            <div style="min-width: 200px;">
                <h3 style="font-weight: bold; margin-bottom: 8px; font-size: 16px;">${sumber.name}</h3>
                <p style="margin-bottom: 8px; color: #666; font-size: 14px;">Sumber air untuk distribusi KP-SPAMS</p>
                <a href="https://www.google.com/maps?q=${sumber.lat},${sumber.lng}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   style="display: inline-flex; align-items: center; color: #2563eb; font-weight: 600; text-decoration: none; font-size: 14px;">
                    <svg style="width: 16px; height: 16px; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd" />
                    </svg>
                    Buka di Google Maps
                </a>
            </div>
        `);
    });

    // Layer for pelanggan markers
    pelangganLayer = L.layerGroup().addTo(map);
    renderPelangganMarkers();

    // Fit bounds if there are multiple markers
    if (props.pelangganList.length > 0 && !props.highlightPelanggan) {
        const bounds = L.latLngBounds([
            [props.kantor.lat, props.kantor.lng],
            ...props.sumberAir.map(s => [s.lat, s.lng]),
            ...props.pelangganList.map(p => [p.latitude, p.longitude]),
        ]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }
};

const filteredPelanggan = computed(() => {
    let data = props.pelangganList;
    if (statusFilter.value === 'aktif') {
        data = data.filter(p => p.status_aktif);
    } else if (statusFilter.value === 'nonaktif') {
        data = data.filter(p => !p.status_aktif);
    }
    if (rtFilter.value) {
        data = data.filter(p => String(p.rt || '').includes(rtFilter.value));
    }
    if (rwFilter.value) {
        data = data.filter(p => String(p.rw || '').includes(rwFilter.value));
    }
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(p => (p.nama_pelanggan || '').toLowerCase().includes(q) || (p.id_pelanggan || '').toLowerCase().includes(q));
    }
    return data;
});

const renderPelangganMarkers = () => {
    pelangganLayer.clearLayers();
    console.log('Rendering markers for:', filteredPelanggan.value.length, 'pelanggan');
    filteredPelanggan.value.forEach(pelanggan => {
        console.log('Adding marker:', pelanggan.id_pelanggan, 'at', pelanggan.latitude, pelanggan.longitude);
        const isHighlighted = props.highlightPelanggan && pelanggan.id_pelanggan === props.highlightPelanggan;
        const bgColor = isHighlighted ? '#f97316' : (pelanggan.status_aktif ? '#16a34a' : '#dc2626');
        const pelangganIcon = L.divIcon({
            html: `<div style="background-color: ${bgColor}; width: ${isHighlighted ? 36 : 28}px; height: ${isHighlighted ? 36 : 28}px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: ${isHighlighted ? 16 : 12}px; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">${isHighlighted ? 'H' : 'P'}</div>`,
            className: 'custom-div-icon',
            iconSize: [isHighlighted ? 36 : 28, isHighlighted ? 36 : 28],
            iconAnchor: [isHighlighted ? 18 : 14, isHighlighted ? 18 : 14],
        });
        const marker = L.marker([pelanggan.latitude, pelanggan.longitude], { icon: pelangganIcon, riseOnHover: true, keyboard: false });
        
        // Buat konten popup dulu
        const rtRw = pelanggan.rt && pelanggan.rw ? `RT ${pelanggan.rt} / RW ${pelanggan.rw}` : '';
        const statusBadge = pelanggan.status_aktif 
            ? '<span style="background-color: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">Aktif</span>'
            : '<span style="background-color: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 12px; font-size: 12px; font-weight: 600;">Nonaktif</span>';
        const mapsUrl = mapsHref(pelanggan);
        
        // Bind popup SEBELUM event listener
        marker.bindPopup(`
            <div style="min-width: 220px;">
                <h3 style="font-weight: bold; margin-bottom: 4px; font-size: 16px;">${pelanggan.nama_pelanggan}</h3>
                <p style="margin-bottom: 4px; color: #666; font-size: 13px;">ID: ${pelanggan.id_pelanggan}</p>
                ${rtRw ? `<p style=\"margin-bottom: 8px; color: #666; font-size: 13px;\">${rtRw}</p>` : ''}
                <div style="margin-bottom: 8px;">${statusBadge}</div>
                <a href="${mapsUrl}" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; color: #2563eb; font-weight: 600; text-decoration: none; font-size: 14px;">
                    <svg style="width: 16px; height: 16px; margin-right: 4px;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd" />
                    </svg>
                    Buka di Google Maps
                </a>
            </div>
        `);
        
        // Tambahkan click listener untuk update sidebar
        marker.on('click', () => {
            selectedPelanggan.value = pelanggan;
        });
        marker.addTo(pelangganLayer);
        if (isHighlighted) {
            marker.openPopup();
            map.setView([pelanggan.latitude, pelanggan.longitude], 17);
            selectedPelanggan.value = pelanggan;
        }
    });
};

watch([searchQuery, statusFilter, rtFilter, rwFilter], () => {
    renderPelangganMarkers();
});

const fitToBounds = () => {
    if (!map) return;
    const bounds = L.latLngBounds([
        [props.kantor.lat, props.kantor.lng],
        ...props.sumberAir.map(s => [s.lat, s.lng]),
        ...filteredPelanggan.value.map(p => [p.latitude, p.longitude]),
    ]);
    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }
};

const mapsHref = (pelanggan) => {
    // Prioritas: gunakan google_maps_url kalau ada, kalau kosong baru generate dari lat/lng
    return pelanggan.google_maps_url || `https://www.google.com/maps?q=${pelanggan.latitude},${pelanggan.longitude}`;
};

const waLink = (no) => {
    if (!no) return '#';
    const normalized = String(no).replace(/[^0-9]/g, '').replace(/^0/, '62');
    return `https://wa.me/${normalized}`;
};
</script>

<style scoped>
/* Pastikan Leaflet map terlihat */
#map {
    z-index: 0;
}
</style>

<style>
/* Pastikan popup tidak ter-clipping oleh container */
.leaflet-container {
    background: #e5e7eb;
    z-index: 0;
}
.leaflet-pane.leaflet-popup-pane {
    z-index: 1000;
}
.leaflet-tile-container {
    z-index: 1;
}
</style>
