<template>
  <AppLayout title="Kelola Informasi Tarif">
    <Head title="Kelola Informasi Tarif" />

    <div class="py-6 sm:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Kelola Informasi Tarif</h2>
            <p class="text-sm text-gray-600 mt-1">Atur informasi harga dan biaya layanan</p>
          </div>
          <button
            @click="openModal()"
            class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2 shadow-md transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Tarif</span>
          </button>
        </div>

        <!-- Tarif List -->
        <div v-if="tarifs.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="tarif in tarifs"
            :key="tarif.id"
            class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-md hover:shadow-xl transition-all transform hover:-translate-y-1"
          >
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                  <h3 class="text-xl font-bold text-gray-800 mb-2">{{ tarif.judul }}</h3>
                  <p v-if="tarif.deskripsi" class="text-sm text-gray-600 mb-3">{{ tarif.deskripsi }}</p>
                </div>
                <span
                  :class="{
                    'text-xs px-2 py-1 rounded-full flex-shrink-0': true,
                    'bg-green-100 text-green-600': tarif.is_active,
                    'bg-red-100 text-red-600': !tarif.is_active
                  }"
                >
                  {{ tarif.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </div>

              <div class="bg-white rounded-lg p-4 mb-4 border-l-4 border-blue-500">
                <div class="flex items-baseline gap-2">
                  <span class="text-3xl font-bold text-blue-600">Rp {{ formatRupiah(tarif.harga) }}</span>
                  <span class="text-gray-600">/ {{ tarif.satuan }}</span>
                </div>
              </div>

              <div class="flex flex-wrap gap-2 mb-4">
                <span class="text-xs px-3 py-1 rounded-full bg-purple-100 text-purple-600">
                  {{ tarif.kategori }}
                </span>
                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                  Urutan: {{ tarif.urutan }}
                </span>
              </div>

              <div class="flex gap-2">
                <button
                  @click="openModal(tarif)"
                  class="flex-1 bg-yellow-500 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition text-sm font-medium"
                >
                  Edit
                </button>
                <button
                  @click="confirmDelete(tarif)"
                  class="flex-1 bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition text-sm font-medium"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="bg-white rounded-xl shadow p-12 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-gray-500 text-lg">Belum ada informasi tarif. Klik "Tambah Tarif" untuk menambahkan.</p>
        </div>
      </div>
    </div>
  </AppLayout>

    <!-- Modal Form -->
    <Teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        @click.self="closeModal"
      >
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">
              {{ editingTarif ? 'Edit Informasi Tarif' : 'Tambah Informasi Tarif Baru' }}
            </h3>
            <form @submit.prevent="submitForm" class="space-y-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Judul *</label>
                <input
                  v-model="form.judul"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Contoh: Tarif Pemakaian Normal"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                <textarea
                  v-model="form.deskripsi"
                  rows="3"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                  placeholder="Deskripsi singkat (opsional)"
                ></textarea>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Harga *</label>
                  <input
                    v-model.number="form.harga"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="15000"
                  />
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan</label>
                  <input
                    v-model="form.satuan"
                    type="text"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="m³, bulan, unit"
                  />
                </div>
              </div>

              <div class="grid grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                  <select
                    v-model="form.kategori"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="tarif">Tarif</option>
                    <option value="biaya">Biaya</option>
                    <option value="retribusi">Retribusi</option>
                    <option value="lainnya">Lainnya</option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Urutan</label>
                  <input
                    v-model.number="form.urutan"
                    type="number"
                    min="0"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                  <select
                    v-model="form.is_active"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option :value="true">Aktif</option>
                    <option :value="false">Nonaktif</option>
                  </select>
                </div>
              </div>

              <div class="flex gap-3 pt-4">
                <button
                  type="button"
                  @click="closeModal"
                  class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md"
                >
                  {{ editingTarif ? 'Perbarui' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Notification -->
    <Teleport to="body">
      <div
        v-if="notification.show"
        class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-bounce"
      >
        {{ notification.message }}
      </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  tarifs: Array
});

const showModal = ref(false);
const editingTarif = ref(null);
const notification = reactive({ show: false, message: '' });

const form = reactive({
  judul: '',
  deskripsi: '',
  harga: 0,
  satuan: 'm³',
  kategori: 'tarif',
  urutan: 0,
  is_active: true
});

function formatRupiah(value) {
  return new Intl.NumberFormat('id-ID').format(value);
}

function openModal(tarif = null) {
  editingTarif.value = tarif;
  if (tarif) {
    form.judul = tarif.judul;
    form.deskripsi = tarif.deskripsi || '';
    form.harga = parseFloat(tarif.harga);
    form.satuan = tarif.satuan;
    form.kategori = tarif.kategori;
    form.urutan = tarif.urutan;
    form.is_active = tarif.is_active;
  } else {
    resetForm();
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingTarif.value = null;
  resetForm();
}

function resetForm() {
  form.judul = '';
  form.deskripsi = '';
  form.harga = 0;
  form.satuan = 'm³';
  form.kategori = 'tarif';
  form.urutan = 0;
  form.is_active = true;
}

function submitForm() {
  if (editingTarif.value) {
    router.put(`/informasi-tarif/${editingTarif.value.id}`, form, {
      onSuccess: () => {
        closeModal();
        showNotification('Informasi tarif berhasil diperbarui');
      }
    });
  } else {
    router.post('/informasi-tarif', form, {
      onSuccess: () => {
        closeModal();
        showNotification('Informasi tarif berhasil ditambahkan');
      }
    });
  }
}

function confirmDelete(tarif) {
  if (confirm(`Hapus tarif "${tarif.judul}"?`)) {
    router.delete(`/informasi-tarif/${tarif.id}`, {
      onSuccess: () => showNotification('Informasi tarif berhasil dihapus')
    });
  }
}

function showNotification(message) {
  notification.message = message;
  notification.show = true;
  setTimeout(() => {
    notification.show = false;
  }, 3000);
}
</script>
