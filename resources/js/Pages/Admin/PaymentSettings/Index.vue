<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    settings: Object,
});

const page = usePage();
const showNotification = ref(false);
const notificationMessage = ref('');
const notificationType = ref('success'); // 'success' or 'error'

const form = useForm({
    bank_name: props.settings.bank_name || '',
    account_number: props.settings.account_number || '',
    account_name: props.settings.account_name || '',
    payment_instructions: props.settings.payment_instructions || '',
    qris_enabled: props.settings.qris_enabled || false,
    bank_transfer_enabled: props.settings.bank_transfer_enabled || false,
    qris_image: null,
});

// Watch for changes in props.settings and update form
watch(() => props.settings, (newSettings) => {
    form.bank_name = newSettings.bank_name || '';
    form.account_number = newSettings.account_number || '';
    form.account_name = newSettings.account_name || '';
    form.payment_instructions = newSettings.payment_instructions || '';
    form.qris_enabled = newSettings.qris_enabled || false;
    form.bank_transfer_enabled = newSettings.bank_transfer_enabled || false;
}, { deep: true });

const previewImage = ref(null);
const currentQrisImage = computed(() => {
    if (props.settings.qris_image) {
        return `/storage/${props.settings.qris_image}`;
    }
    return null;
});

const handleImageChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.qris_image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.qris_image = null;
    previewImage.value = null;
};

const displayNotification = (message, type = 'success') => {
    notificationMessage.value = message;
    notificationType.value = type;
    showNotification.value = true;
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        showNotification.value = false;
    }, 3000);
};

const submit = () => {
    form.post(route('admin.payment-settings.update'), {
        preserveScroll: true,
        onSuccess: (page) => {
            previewImage.value = null;
            // Reset form dengan data terbaru dari server
            form.clearErrors();
            form.qris_image = null; // Reset file input
            displayNotification('Pengaturan pembayaran berhasil disimpan!', 'success');
        },
        onError: () => {
            displayNotification('Gagal menyimpan pengaturan. Silakan coba lagi.', 'error');
        },
    });
};
</script>

<template>
    <AppLayout title="Pengaturan Pembayaran">
        <div class="max-w-4xl mx-auto py-8 px-4">
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
                        <!-- Success Icon -->
                        <svg v-if="notificationType === 'success'" class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <!-- Error Icon -->
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

            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Pengaturan Pembayaran</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- QRIS Settings -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Pembayaran QRIS</h2>
                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    v-model="form.qris_enabled"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="ml-2 text-gray-700">Aktifkan pembayaran QRIS</span>
                            </label>
                        </div>

                        <div v-if="form.qris_enabled" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload QRIS
                                </label>
                                
                                <!-- Current Image -->
                                <div v-if="currentQrisImage && !previewImage" class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">QRIS Saat Ini:</p>
                                    <img 
                                        :src="currentQrisImage" 
                                        alt="Current QRIS" 
                                        class="max-w-xs border rounded"
                                    >
                                </div>

                                <!-- Preview New Image -->
                                <div v-if="previewImage" class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Preview QRIS Baru:</p>
                                    <img 
                                        :src="previewImage" 
                                        alt="Preview" 
                                        class="max-w-xs border rounded"
                                    >
                                    <button 
                                        type="button"
                                        @click="removeImage"
                                        class="mt-2 text-sm text-red-600 hover:text-red-800"
                                    >
                                        Hapus gambar
                                    </button>
                                </div>

                                <input 
                                    type="file" 
                                    @change="handleImageChange"
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100"
                                >
                                <p class="mt-1 text-sm text-gray-500">
                                    Format: JPG, PNG (Max: 2MB)
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer Settings -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-semibold text-gray-700 mb-4">Pembayaran Transfer Bank</h2>
                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    v-model="form.bank_transfer_enabled"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span class="ml-2 text-gray-700">Aktifkan pembayaran transfer bank</span>
                            </label>
                        </div>

                        <div v-if="form.bank_transfer_enabled" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Bank
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.bank_name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Contoh: BRI"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Rekening
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.account_number"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Contoh: 1234-5678-9012"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Atas Nama
                                </label>
                                <input 
                                    type="text" 
                                    v-model="form.account_name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Contoh: KP-SPAMS Desa"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Payment Instructions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Instruksi Pembayaran
                        </label>
                        <textarea 
                            v-model="form.payment_instructions"
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan instruksi pembayaran untuk pelanggan"
                        ></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
