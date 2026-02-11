<template>
    <AppLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Security Check (PIN) -->
                <div v-if="!isVerified" class="max-w-md mx-auto mt-20">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-blue-600 px-8 py-6 text-center">
                            <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-white">Keamanan Diperlukan</h2>
                            <p class="text-blue-100 text-sm mt-1">Masukkan PIN Keamanan untuk mengakses halaman ini</p>
                        </div>
                        
                        <div class="p-8">
                            <form @submit.prevent="verifyPin" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">PIN Keamanan</label>
                                    <input 
                                        type="password" 
                                        v-model="pinForm.pin"
                                        maxlength="8"
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center tracking-[1em] text-xl font-bold"
                                        placeholder="••••••"
                                        autofocus
                                    />
                                    <p v-if="pinForm.errors.pin" class="text-red-500 text-xs mt-1 text-center">{{ pinForm.errors.pin }}</p>
                                </div>
                                
                                <button 
                                    type="submit" 
                                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition transform active:scale-95 disabled:opacity-50"
                                    :disabled="pinForm.processing"
                                >
                                    {{ pinForm.processing ? 'Memverifikasi...' : 'Buka Akses' }}
                                </button>
                            </form>
                            
                            <p class="text-center text-xs text-gray-400 mt-6">
                                * PIN default adalah 123456 jika belum diatur
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Management Content -->
                <div v-else class="space-y-6">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Manajemen Pengelola</h1>
                            <p class="text-sm text-gray-600">Kelola akun admin dan staf pengelola sistem</p>
                        </div>
                        <button 
                            @click="openModal()" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-sm transition gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah Akun
                        </button>
                    </div>

                    <!-- User List -->
                    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama & Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role & Wilayah</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">PIN Akses</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in paginatedUsers" :key="user.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg border border-blue-200">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <span :class="roleClass(user.role)" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">
                                                    {{ user.role }}
                                                </span>
                                                <div v-if="user.wilayah" class="text-xs text-gray-500 mt-1">
                                                    📍 {{ wilayahOptions[user.wilayah] || user.wilayah }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                            {{ user.pin || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openModal(user)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                            <button 
                                                v-if="user.id !== $page.props.auth.user.id"
                                                @click="deleteUser(user)" 
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between sm:px-6 gap-3 rounded-b-xl border-x-0">
                        <div class="text-sm text-gray-700 text-center sm:text-left">
                            Menampilkan 
                            <span class="font-semibold">{{ users.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0 }}</span> 
                            sampai 
                            <span class="font-semibold">{{ Math.min(currentPage * itemsPerPage, users.length) }}</span> 
                            dari 
                            <span class="font-semibold">{{ users.length }}</span> 
                            data
                        </div>
                        <ClientPagination 
                            :current-page="currentPage" 
                            :total-pages="totalPages" 
                            @page-change="onPageChange" 
                        />
                    </div>
                </div>

                <!-- Create/Edit Modal -->
                <div v-if="showModal" class="relative z-[200]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <!-- Background backdrop -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                            <!-- Modal panel -->
                            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                        {{ editingUser ? 'Edit Akun Pengelola' : 'Tambah Akun Pengelola' }}
                                    </h3>
                                    <form @submit.prevent="submitUser" class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                            <input type="text" v-model="form.name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required />
                                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                            <input type="email" v-model="form.email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required />
                                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                            <select v-model="form.role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="pengelola">Pengelola Staf</option>
                                                <option value="admin">Administrator</option>
                                                <option value="penarik">Penarik</option>
                                            </select>
                                        </div>

                                        <!-- Wilayah field (only for penarik) -->
                                        <div v-if="form.role === 'penarik'">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah <span class="text-red-500">*</span></label>
                                            <select v-model="form.wilayah" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                                <option value="">-- Pilih Wilayah --</option>
                                                <option v-for="(label, value) in wilayahOptions" :key="value" :value="value">
                                                    {{ label }}
                                                </option>
                                            </select>
                                            <p v-if="form.errors.wilayah" class="text-red-500 text-xs mt-1">{{ form.errors.wilayah }}</p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    Password {{ editingUser ? '(Opsional)' : '' }}
                                                </label>
                                                <input 
                                                    type="text" 
                                                    v-model="form.password" 
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                                    :required="!editingUser"
                                                    minlength="8"
                                                />
                                                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">PIN Akses (4-8 digit)</label>
                                                <input 
                                                    type="text" 
                                                    v-model="form.pin" 
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                                    maxlength="8"
                                                    placeholder="123456"
                                                />
                                                 <p v-if="form.errors.pin" class="text-red-500 text-xs mt-1">{{ form.errors.pin }}</p>
                                            </div>
                                        </div>
                                        <p v-if="editingUser" class="text-xs text-gray-500 italic">* Kosongkan password jika tidak ingin mengubahnya.</p>
                                    </form>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                                    <button 
                                        @click="submitUser" 
                                        type="button" 
                                        class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto"
                                        :disabled="form.processing"
                                    >
                                        {{ editingUser ? 'Simpan' : 'Buat Akun' }}
                                    </button>
                                    <button 
                                        @click="closeModal" 
                                        type="button" 
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                    >
                                        Batal
                                    </button>
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
import { ref, onMounted, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    isVerified: Boolean,
    users: Array,
    wilayahOptions: Object,
});

// PIN Verification
const pinForm = useForm({
    pin: '',
});

const verifyPin = () => {
    pinForm.post(route('admin.pengelola.verify'), {
        preserveScroll: true,
        onError: () => {
            pinForm.reset();
        }
    });
};

// User Management
const showModal = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'pengelola',
    wilayah: '',
    pin: '',
});

const roleClass = (role) => {
    if (role === 'admin') return 'bg-purple-100 text-purple-800';
    if (role === 'penarik') return 'bg-blue-100 text-blue-800';
    return 'bg-green-100 text-green-800';
};

const openModal = (user = null) => {
    editingUser.value = user;
    if (user) {
        form.name = user.name;
        form.email = user.email;
        form.role = user.role;
        form.wilayah = user.wilayah || '';
        form.pin = user.pin || '';
        form.password = ''; // Don't show password
    } else {
        form.reset();
        form.password = ''; 
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editingUser.value = null;
};

const submitUser = () => {
    if (editingUser.value) {
        form.put(route('admin.pengelola.update', editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.pengelola.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteUser = (user) => {
    if (confirm(`Yakin ingin menghapus akun ${user.name}?`)) {
        router.delete(route('admin.pengelola.destroy', user.id));
    }
};

// --- Pagination Logic ---
import ClientPagination from '@/Components/ClientPagination.vue';

const currentPage = ref(1);
const itemsPerPage = 20;

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return props.users.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(props.users.length / itemsPerPage);
});

const onPageChange = (page) => {
    currentPage.value = page;
};
</script>
