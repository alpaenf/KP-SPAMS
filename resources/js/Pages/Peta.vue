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
                    <div class="lg:col-span-2 relative group">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 relative h-[500px] md:h-[600px] lg:h-[700px]">
                            <div 
                                id="map" 
                                ref="mapContainer"
                                class="w-full h-full z-0"
                                style="background: #e5e7eb;"
                            ></div>

                            <!-- Floating Stats Card -->
                            <Transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="transform translate-y-4 opacity-0"
                                enter-to-class="transform translate-y-0 opacity-100"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="transform translate-y-0 opacity-100"
                                leave-to-class="transform translate-y-4 opacity-0"
                            >
                                <div v-if="showStats" class="absolute top-4 right-4 z-10 bg-white/90 backdrop-blur-sm p-4 rounded-xl shadow-xl border border-white/50 max-w-xs w-64">
                                    <div class="flex justify-between items-center border-b border-gray-200 pb-2 mb-3">
                                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                            Statistik Wilayah
                                        </h4>
                                        <button @click="showStats = false" class="text-gray-400 hover:text-gray-600 transition p-1 hover:bg-gray-100 rounded-full">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">Total Ditampilkan</span>
                                            <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-full">{{ filteredPelanggan.length }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-green-700 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Aktif</span>
                                            <span class="font-bold text-green-700 bg-green-50 px-2 py-0.5 rounded-full">{{ filteredPelanggan.filter(p => p.status_aktif).length }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-red-700 flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> Non-Aktif</span>
                                            <span class="font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-full">{{ filteredPelanggan.filter(p => !p.status_aktif).length }}</span>
                                        </div>
                                    </div>
                                </div>
                            </Transition>

                            <!-- Restore Buttons Container -->
                            <div class="absolute top-4 right-4 z-10 flex flex-col gap-2">
                                <Transition
                                    enter-active-class="transition duration-300 ease-out"
                                    enter-from-class="transform scale-90 opacity-0"
                                    enter-to-class="transform scale-100 opacity-100"
                                    leave-active-class="transition duration-200 ease-in"
                                    leave-from-class="transform scale-100 opacity-100"
                                    leave-to-class="transform scale-90 opacity-0"
                                >
                                    <button v-if="!showStats" @click="showStats = true" class="bg-white/90 backdrop-blur-sm p-2 rounded-lg shadow-lg border border-white/50 text-blue-600 hover:bg-white transition-all transform hover:scale-110" title="Tampilkan Statistik">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    </button>
                                </Transition>

                                <Transition
                                    enter-active-class="transition duration-300 ease-out"
                                    enter-from-class="transform scale-90 opacity-0"
                                    enter-to-class="transform scale-100 opacity-100"
                                    leave-active-class="transition duration-200 ease-in"
                                    leave-from-class="transform scale-100 opacity-100"
                                    leave-to-class="transform scale-90 opacity-0"
                                >
                                    <button v-if="!showLegend" @click="showLegend = true" class="bg-white/90 backdrop-blur-sm p-2 rounded-lg shadow-lg border border-white/50 text-blue-600 hover:bg-white transition-all transform hover:scale-110" title="Tampilkan Legenda">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                    </button>
                                </Transition>
                            </div>

                            <!-- Floating Legend -->
                            <Transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="transform translate-y-4 opacity-0"
                                enter-to-class="transform translate-y-0 opacity-100"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="transform translate-y-0 opacity-100"
                                leave-to-class="transform translate-y-4 opacity-0"
                            >
                                <div v-if="showLegend" class="absolute bottom-4 left-4 z-10 bg-white/90 backdrop-blur-md p-4 rounded-xl shadow-xl border border-white/50 max-w-xs w-64 transition-opacity duration-300 opacity-90 hover:opacity-100">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-semibold text-gray-800 text-xs uppercase tracking-wider">Legenda</h4>
                                        <button @click="showLegend = false" class="text-gray-400 hover:text-gray-600 transition p-1 hover:bg-gray-100 rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-blue-800 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white">K</div>
                                            <span class="text-xs font-medium text-gray-700">Kantor</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white">S</div>
                                            <span class="text-xs font-medium text-gray-700">Sumber</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white">P</div>
                                            <span class="text-xs font-medium text-gray-700">Aktif</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm ring-2 ring-white">P</div>
                                            <span class="text-xs font-medium text-gray-700">Non-Aktif</span>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                        
                        <!-- Mobile Tip -->
                        <div class="mt-2 flex items-center gap-2 text-xs text-gray-500 md:hidden px-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Gunakan 2 jari untuk menggeser peta pada mobile</span>
                        </div>
                    </div>

                    <!-- Details Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Welcome / Info Card (When nothing selected) -->
                        <div v-if="!selectedPelanggan" class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 h-full min-h-[200px] flex flex-col justify-center items-center text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Jelajahi Peta</h3>
                            <p class="text-gray-500 text-sm max-w-xs mx-auto">
                                Klik pada salah satu marker pelanggan di peta untuk melihat detail informasi, status pembayaran, dan menu aksi lainnya.
                            </p>
                        </div>

                        <!-- Selected Info Card -->
                        <div v-if="selectedPelanggan" class="bg-white rounded-xl shadow-lg border border-blue-100 overflow-hidden sticky top-6 transition-all duration-300 transform animate-in fade-in slide-in-from-right-4">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-4 text-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-lg leading-tight">{{ selectedPelanggan.nama_pelanggan }}</h3>
                                        <p class="text-blue-100 text-sm mt-1 opacity-90">ID: {{ selectedPelanggan.id_pelanggan }}</p>
                                    </div>
                                    <button @click="selectedPelanggan = null" class="text-white/70 hover:text-white hover:bg-white/20 rounded-full p-1 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="p-5 space-y-4">
                                <!-- Status Badge -->
                                <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                                    <span class="text-sm text-gray-500 font-medium">Status Langganan</span>
                                    <span v-if="selectedPelanggan.status_aktif" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200 shadow-sm">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Aktif
                                    </span>
                                    <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 shadow-sm">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                        Nonaktif
                                    </span>
                                </div>

                                <!-- Detail Grid -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Lokasi</p>
                                        <p class="text-sm font-bold text-gray-800">
                                            {{ selectedPelanggan.rt ? `RT ${selectedPelanggan.rt}` : '-' }} / 
                                            {{ selectedPelanggan.rw ? `RW ${selectedPelanggan.rw}` : '-' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Wilayah</p>
                                        <p class="text-sm font-bold text-gray-800 truncate" :title="selectedPelanggan.wilayah">{{ selectedPelanggan.wilayah || '-' }}</p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="space-y-3 pt-2">
                                    <a v-if="selectedPelanggan.no_whatsapp" :href="waLink(selectedPelanggan.no_whatsapp)" target="_blank" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-[#25D366] text-white rounded-lg hover:bg-[#128C7E] font-medium shadow-sm hover:shadow transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        Chat WhatsApp
                                    </a>
                                    
                                    <div class="grid grid-cols-2 gap-3">
                                        <a :href="mapsHref(selectedPelanggan)" target="_blank" class="flex items-center justify-center gap-2 px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition">
                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v2.197A5.973 5.973 0 0110 16v-2a2 2 0 00-2-2 2 2 0 01-2-2 2 2 0 00-1.668-1.973z" clip-rule="evenodd" /></svg>
                                            Google Maps
                                        </a>
                                        <a :href="`/pelanggan/${selectedPelanggan.id}/edit`" target="_blank" class="flex items-center justify-center gap-2 px-3 py-2 bg-blue-50 border border-blue-100 text-blue-700 rounded-lg hover:bg-blue-100 font-medium text-sm transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Edit Data
                                        </a>
                                    </div>
                                </div>
                            </div>
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
const showStats = ref(true);
const showLegend = ref(true);
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
    
    // Define Layers
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
        maxZoom: 19
    });

    // Create Map
    map = L.map('map', {
        center: [centerLat, centerLng],
        zoom: 15,
        layers: [streetLayer], // Default layer
        zoomControl: false // Kita pindah zoom control nanti atau biarkan default
    });

    // Add Layer Control
    const baseMaps = {
        "Peta Jalan (Streets)": streetLayer,
        "Satelit (Imagery)": satelliteLayer
    };

    L.control.layers(baseMaps, null, { position: 'topleft' }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);

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
