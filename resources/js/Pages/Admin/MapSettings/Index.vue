<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    mapSettings: Array,
});

const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('success');

const showModal = ref(false);
const editingId = ref(null);

const form = useForm({
    location_type: 'kp_spams',
    name: '',
    latitude: '',
    longitude: '',
    description: '',
    is_active: true,
});

const kpSpamsLocations = computed(() => 
    props.mapSettings.filter(m => m.location_type === 'kp_spams')
);

const sumberAirLocations = computed(() => 
    props.mapSettings.filter(m => m.location_type === 'sumber_air')
);

const bronscapLocations = computed(() => 
    props.mapSettings.filter(m => m.location_type === 'bronscap')
);

const reservoirLocations = computed(() => 
    props.mapSettings.filter(m => m.location_type === 'reservoir')
);

const displayNotification = (message, type = 'success') => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotification.value = true;
    setTimeout(() => {
        showNotification.value = false;
    }, 3000);
};

const openAddModal = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (mapSetting) => {
    editingId.value = mapSetting.id;
    form.location_type = mapSetting.location_type;
    form.name = mapSetting.name;
    form.latitude = mapSetting.latitude;
    form.longitude = mapSetting.longitude;
    form.description = mapSetting.description || '';
    form.is_active = mapSetting.is_active;
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingId.value) {
        form.put(route('map-settings.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                displayNotification('Lokasi peta berhasil diperbarui!');
            },
            onError: () => {
                displayNotification('Gagal memperbarui lokasi. Silakan coba lagi.', 'error');
            },
        });
    } else {
        form.post(route('map-settings.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                displayNotification('Lokasi peta berhasil ditambahkan!');
            },
            onError: () => {
                displayNotification('Gagal menambahkan lokasi. Silakan coba lagi.', 'error');
            },
        });
    }
};

const deleteLocation = (id) => {
    if (confirm('Yakin ingin menghapus lokasi ini?')) {
        useForm({}).delete(route('map-settings.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                displayNotification('Lokasi berhasil dihapus!');
            },
            onError: () => {
                displayNotification('Gagal menghapus lokasi.', 'error');
            },
        });
    }
};
</script>

<template>
    <AppLayout title="Pengaturan Peta">
        <div class="max-w-7xl mx-auto py-8 px-4">
            <!-- Notification Toast -->
            <transition
                enter-active-class="transform transition ease-out duration-300"
                enter-from-class="translate-y-2 opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showNotification" class="fixed top-4 right-4 z-50 max-w-sm">
                    <div 
                        :class="{
                            'bg-blue-50 border-blue-500 text-blue-800': notificationType === 'success',
                            'bg-red-50 border-red-500 text-red-800': notificationType === 'error'
                        }"
                        class="border-l-4 p-4 rounded-lg shadow-lg flex items-start gap-3"
                    >
                        <svg v-if="notificationType === 'success'" class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg v-else class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <p class="font-semibold">{{ notificationType === 'success' ? 'Berhasil!' : 'Error!' }}</p>
                            <p class="text-sm">{{ notificationMessage }}</p>
                        </div>
                        <button @click="showNotification = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </transition>

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Pengaturan Lokasi Peta</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Kelola lokasi KP-SPAMS dan Sumber Air yang ditampilkan di peta</p>
                </div>
                <button 
                    @click="openAddModal"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 font-medium flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="sm:inline">Tambah Lokasi</span>
                </button>
            </div>

            <!-- KP-SPAMS Locations -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-800 rounded-full flex items-center justify-center text-white text-xs font-bold">K</div>
                    Lokasi Kantor KP-SPAMS
                </h2>
                <div v-if="kpSpamsLocations.length > 0" class="space-y-3">
                    <div 
                        v-for="location in kpSpamsLocations" 
                        :key="location.id"
                        class="border rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow"
                    >
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm sm:text-base">{{ location.name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 break-all">
                                    Koordinat: {{ location.latitude }}, {{ location.longitude }}
                                </p>
                                <p v-if="location.description" class="text-xs sm:text-sm text-gray-500 mt-1">
                                    {{ location.description }}
                                </p>
                                <span 
                                    :class="location.is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'"
                                    class="inline-block px-2 py-1 text-xs rounded mt-2"
                                >
                                    {{ location.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="flex sm:flex-col gap-2 justify-end sm:justify-start">
                                <button 
                                    @click="openEditModal(location)"
                                    class="text-blue-600 hover:text-blue-800 p-1.5 sm:p-2 hover:bg-blue-50 rounded"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button 
                                    @click="deleteLocation(location.id)"
                                    class="text-red-600 hover:text-red-800 p-1.5 sm:p-2 hover:bg-red-50 rounded"
                                    title="Hapus"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-8">Belum ada lokasi kantor KP-SPAMS</p>
            </div>

            <!-- Sumber Air Locations -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">S</div>
                    Lokasi Sumber Air
                </h2>
                <div v-if="sumberAirLocations.length > 0" class="space-y-3">
                    <div 
                        v-for="location in sumberAirLocations" 
                        :key="location.id"
                        class="border rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow"
                    >
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm sm:text-base">{{ location.name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 break-all">
                                    Koordinat: {{ location.latitude }}, {{ location.longitude }}
                                </p>
                                <p v-if="location.description" class="text-xs sm:text-sm text-gray-500 mt-1">
                                    {{ location.description }}
                                </p>
                                <span 
                                    :class="location.is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'"
                                    class="inline-block px-2 py-1 text-xs rounded mt-2"
                                >
                                    {{ location.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="flex sm:flex-col gap-2 justify-end sm:justify-start">
                                <button 
                                    @click="openEditModal(location)"
                                    class="text-blue-600 hover:text-blue-800 p-1.5 sm:p-2 hover:bg-blue-50 rounded"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button 
                                    @click="deleteLocation(location.id)"
                                    class="text-red-600 hover:text-red-800 p-1.5 sm:p-2 hover:bg-red-50 rounded"
                                    title="Hapus"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-8">Belum ada lokasi sumber air</p>
            </div>

            <!-- Bronscap Locations -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center text-white text-xs font-bold">B</div>
                    Lokasi Bronscap
                </h2>
                <div v-if="bronscapLocations.length > 0" class="space-y-3">
                    <div 
                        v-for="location in bronscapLocations" 
                        :key="location.id"
                        class="border rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow"
                    >
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm sm:text-base">{{ location.name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 break-all">
                                    Koordinat: {{ location.latitude }}, {{ location.longitude }}
                                </p>
                                <p v-if="location.description" class="text-xs sm:text-sm text-gray-500 mt-1">
                                    {{ location.description }}
                                </p>
                                <span 
                                    :class="location.is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'"
                                    class="inline-block px-2 py-1 text-xs rounded mt-2"
                                >
                                    {{ location.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="flex sm:flex-col gap-2 justify-end sm:justify-start">
                                <button 
                                    @click="openEditModal(location)"
                                    class="text-blue-600 hover:text-blue-800 p-1.5 sm:p-2 hover:bg-blue-50 rounded"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" strokeLine-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button 
                                    @click="deleteLocation(location.id)"
                                    class="text-red-600 hover:text-red-800 p-1.5 sm:p-2 hover:bg-red-50 rounded"
                                    title="Hapus"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-8">Belum ada lokasi bronscap</p>
            </div>

            <!-- Reservoir Locations -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 bg-teal-600 rounded-full flex items-center justify-center text-white text-xs font-bold">R</div>
                    Lokasi Reservoir
                </h2>
                <div v-if="reservoirLocations.length > 0" class="space-y-3">
                    <div 
                        v-for="location in reservoirLocations" 
                        :key="location.id"
                        class="border rounded-lg p-3 sm:p-4 hover:shadow-md transition-shadow"
                    >
                        <div class="flex flex-col sm:flex-row sm:justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 text-sm sm:text-base">{{ location.name }}</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-1 break-all">
                                    Koordinat: {{ location.latitude }}, {{ location.longitude }}
                                </p>
                                <p v-if="location.description" class="text-xs sm:text-sm text-gray-500 mt-1">
                                    {{ location.description }}
                                </p>
                                <span 
                                    :class="location.is_active ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600'"
                                    class="inline-block px-2 py-1 text-xs rounded mt-2"
                                >
                                    {{ location.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="flex sm:flex-col gap-2 justify-end sm:justify-start">
                                <button 
                                    @click="openEditModal(location)"
                                    class="text-blue-600 hover:text-blue-800 p-1.5 sm:p-2 hover:bg-blue-50 rounded"
                                    title="Edit"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button 
                                    @click="deleteLocation(location.id)"
                                    class="text-red-600 hover:text-red-800 p-1.5 sm:p-2 hover:bg-red-50 rounded"
                                    title="Hapus"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-500 text-center py-8">Belum ada lokasi reservoir</p>
            </div>

            <!-- Modal Form -->
            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
                    
                    <!-- Modal container -->
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <!-- Modal panel -->
                        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg transform transition-all">
                            <form @submit.prevent="submit">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        {{ editingId ? 'Edit Lokasi' : 'Tambah Lokasi Baru' }}
                                    </h3>

                                    <div class="space-y-4">
                                        <!-- Tipe Lokasi -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Tipe Lokasi <span class="text-red-500">*</span>
                                            </label>
                                            <select
                                                v-model="form.location_type"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required
                                            >
                                                <option value="kp_spams">Kantor KP-SPAMS</option>
                                                <option value="sumber_air">Sumber Air</option>
                                                <option value="bronscap">Bronscap</option>
                                                <option value="reservoir">Reservoir</option>
                                            </select>
                                            <p v-if="form.errors.location_type" class="mt-1 text-sm text-red-600">{{ form.errors.location_type }}</p>
                                        </div>

                                        <!-- Nama -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Nama Lokasi <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model="form.name"
                                                type="text"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Contoh: Kantor Pusat KP-SPAMS"
                                                required
                                            />
                                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                        </div>

                                        <!-- Latitude & Longitude -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Latitude <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model="form.latitude"
                                                    type="number"
                                                    step="any"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    placeholder="-6.200000"
                                                    required
                                                />
                                                <p v-if="form.errors.latitude" class="mt-1 text-sm text-red-600">{{ form.errors.latitude }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Longitude <span class="text-red-500">*</span>
                                                </label>
                                                <input
                                                    v-model="form.longitude"
                                                    type="number"
                                                    step="any"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                    placeholder="106.816666"
                                                    required
                                                />
                                                <p v-if="form.errors.longitude" class="mt-1 text-sm text-red-600">{{ form.errors.longitude }}</p>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Deskripsi (Opsional)
                                            </label>
                                            <textarea
                                                v-model="form.description"
                                                rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="Tambahkan deskripsi lokasi..."
                                            ></textarea>
                                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                                        </div>

                                        <!-- Status Aktif -->
                                        <div>
                                            <label class="flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    v-model="form.is_active"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="ml-2 text-sm text-gray-700">Tampilkan di peta</span>
                                            </label>
                                        </div>

                                        <!-- Info Bantuan -->
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <p class="text-sm text-blue-800">
                                                <strong>Tips:</strong> Untuk mendapatkan koordinat, buka Google Maps, klik kanan pada lokasi, lalu pilih koordinat untuk menyalinnya.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="w-full sm:w-auto px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                                    >
                                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                    </button>
                                    <button
                                        type="button"
                                        @click="closeModal"
                                        class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium"
                                    >
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </AppLayout>
</template>
