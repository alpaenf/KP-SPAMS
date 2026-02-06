<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Pelanggan Baru</h1>
                    <p class="text-gray-600 mt-1">Isi form di bawah untuk menambahkan pelanggan baru</p>
                </div>

                <!-- Form -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <form @submit.prevent="submitForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ID Pelanggan -->
                            <div>
                                <label for="id_pelanggan" class="block text-sm font-medium text-gray-700 mb-2">
                                    ID Pelanggan <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="id_pelanggan"
                                    v-model="form.id_pelanggan"
                                    placeholder="PLG001"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.id_pelanggan }"
                                    required
                                />
                                <p v-if="errors.id_pelanggan" class="mt-1 text-sm text-red-600">{{ errors.id_pelanggan }}</p>
                            </div>

                            <!-- Nama Pelanggan -->
                            <div>
                                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Pelanggan <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_pelanggan"
                                    v-model="form.nama_pelanggan"
                                    placeholder="Nama lengkap"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.nama_pelanggan }"
                                    required
                                />
                                <p v-if="errors.nama_pelanggan" class="mt-1 text-sm text-red-600">{{ errors.nama_pelanggan }}</p>
                            </div>

                            <!-- No WhatsApp -->
                            <div>
                                <label for="no_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp</label>
                                <input
                                    type="text"
                                    id="no_whatsapp"
                                    v-model="form.no_whatsapp"
                                    placeholder="08123456789"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.no_whatsapp }"
                                />
                                <p v-if="errors.no_whatsapp" class="mt-1 text-sm text-red-600">{{ errors.no_whatsapp }}</p>
                            </div>

                            <!-- RT -->
                            <div>
                                <label for="rt" class="block text-sm font-medium text-gray-700 mb-2">RT</label>
                                <input
                                    type="number"
                                    id="rt"
                                    v-model="form.rt"
                                    placeholder="001"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.rt }"
                                />
                                <p v-if="errors.rt" class="mt-1 text-sm text-red-600">{{ errors.rt }}</p>
                            </div>

                            <!-- RW -->
                            <div>
                                <label for="rw" class="block text-sm font-medium text-gray-700 mb-2">RW</label>
                                <input
                                    type="number"
                                    id="rw"
                                    v-model="form.rw"
                                    placeholder="001"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.rw }"
                                />
                                <p v-if="errors.rw" class="mt-1 text-sm text-red-600">{{ errors.rw }}</p>
                            </div>

                            <!-- Kategori Pelanggan -->
                            <div>
                                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kategori Pelanggan <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="kategori"
                                    v-model="form.kategori"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.kategori }"
                                    required
                                >
                                    <option value="umum">Umum</option>
                                    <option value="sosial">Sosial (Gratis)</option>
                                </select>
                                <p v-if="errors.kategori" class="mt-1 text-sm text-red-600">{{ errors.kategori }}</p>
                                <p class="mt-1 text-xs text-gray-500">Pelanggan kategori sosial tidak dikenakan biaya</p>
                            </div>

                            <!-- Wilayah -->
                            <div>
                                <label for="wilayah" class="block text-sm font-medium text-gray-700 mb-2">
                                    Wilayah <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="wilayah"
                                    v-model="form.wilayah"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.wilayah }"
                                    required
                                >
                                    <option value="Dawuhan">Dawuhan</option>
                                    <option value="Kubangsari Kulon">Kubangsari Kulon</option>
                                    <option value="Kubangsari Wetan">Kubangsari Wetan</option>
                                    <option value="Sokarame">Sokarame</option>
                                    <option value="Tiparjaya">Tiparjaya</option>
                                </select>
                                <p v-if="errors.wilayah" class="mt-1 text-sm text-red-600">{{ errors.wilayah }}</p>
                            </div>

                            <!-- Latitude -->
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        inputmode="decimal"
                                        id="latitude"
                                        v-model="form.latitude"
                                        placeholder="-6.200000"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent transition"
                                        :class="{ 'border-red-500': errors.latitude }"
                                    />
                                    <button 
                                        type="button"
                                        @click="form.latitude = form.latitude ? (form.latitude.startsWith('-') ? form.latitude.substring(1) : '-' + form.latitude) : '-'"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-bold border border-gray-300 shadow-sm"
                                        title="Ubah Tanda +/-"
                                    >
                                        ±
                                    </button>
                                </div>
                                <p v-if="errors.latitude" class="mt-1 text-sm text-red-600">{{ errors.latitude }}</p>
                                <a 
                                    href="https://www.google.com/maps" 
                                    target="_blank" 
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center mt-1 text-xs text-blue-600 hover:text-blue-800"
                                >
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                        <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                    </svg>
                                    Buka Google Maps untuk mendapatkan koordinat
                                </a>
                            </div>


                            <!-- Longitude -->
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                                <input
                                    type="text"
                                    inputmode="decimal"
                                    id="longitude"
                                    v-model="form.longitude"
                                    placeholder="106.816666"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.longitude }"
                                />
                                <p v-if="errors.longitude" class="mt-1 text-sm text-red-600">{{ errors.longitude }}</p>
                                <p class="mt-1 text-xs text-gray-500">Klik kanan pada lokasi di Google Maps → Koordinat akan muncul di bagian atas</p>
                            </div>
                        </div>

                        <!-- Google Maps URL -->
                        <div class="mt-6">
                            <label for="google_maps_url" class="block text-sm font-medium text-gray-700 mb-2">
                                Link Google Maps
                            </label>
                            <input
                                type="url"
                                id="google_maps_url"
                                v-model="form.google_maps_url"
                                placeholder="https://maps.app.goo.gl/xxxx"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
                                :class="{ 'border-red-500': errors.google_maps_url }"
                            />
                            <p v-if="errors.google_maps_url" class="mt-1 text-sm text-red-600">{{ errors.google_maps_url }}</p>
                            <p class="mt-1 text-xs text-gray-500">Paste shortened Google Maps link (contoh: https://maps.app.goo.gl/8jxgfLkPirsLH9uc7)</p>
                        </div>

                        <!-- Status Aktif -->
                        <div class="mt-6">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    v-model="form.status_aktif"
                                    class="h-4 w-4 text-blue-800 focus:ring-blue-800 border-gray-300 rounded"
                                />
                                <span class="ml-2 text-sm text-gray-700">Status Aktif</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 flex gap-4">
                            <button
                                type="submit"
                                :disabled="processing"
                                class="px-6 py-3 bg-blue-800 text-white rounded-lg font-semibold hover:bg-blue-900 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="processing">Menyimpan...</span>
                                <span v-else>Simpan</span>
                            </button>
                            <Link
                                href="/cek-pelanggan"
                                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({})
    }
});

const form = ref({
    id_pelanggan: '',
    nama_pelanggan: '',
    no_whatsapp: '',
    rt: null,
    rw: null,
    kategori: 'umum',
    wilayah: 'Dawuhan',
    latitude: null,
    longitude: null,
    google_maps_url: '',
    status_aktif: true
});

const processing = ref(false);

const submitForm = () => {
    processing.value = true;
    
    router.post('/pelanggan', form.value, {
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script>
