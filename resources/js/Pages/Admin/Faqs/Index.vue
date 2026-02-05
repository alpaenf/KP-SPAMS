<template>
  <AppLayout title="Kelola FAQ">
    <Head title="Kelola FAQ" />

    <div class="py-6 sm:py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
          <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Kelola Pertanyaan Umum (FAQ)</h2>
            <p class="text-sm text-gray-600 mt-1">Atur pertanyaan dan jawaban yang sering ditanyakan</p>
          </div>
          <button
            @click="openModal()"
            class="w-full sm:w-auto bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 flex items-center justify-center gap-2 shadow-md transition"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah FAQ</span>
          </button>
        </div>

        <!-- FAQ List -->
        <div v-if="faqs.length > 0" class="space-y-4">
          <div
            v-for="(faq, index) in faqs"
            :key="faq.id"
            class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow"
          >
            <div class="p-4 sm:p-6">
              <div class="flex flex-col sm:flex-row justify-between gap-4">
                <div class="flex-1">
                  <div class="flex items-start gap-3 mb-3">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">
                      {{ index + 1 }}
                    </div>
                    <div class="flex-1">
                      <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ faq.pertanyaan }}</h3>
                      <p class="text-gray-600 leading-relaxed">{{ faq.jawaban }}</p>
                    </div>
                  </div>
                  <div class="flex flex-wrap gap-2 mt-3">
                    <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-600">
                      Urutan: {{ faq.urutan }}
                    </span>
                    <span
                      :class="{
                        'text-xs px-3 py-1 rounded-full': true,
                        'bg-green-100 text-green-600': faq.is_active,
                        'bg-red-100 text-red-600': !faq.is_active
                      }"
                    >
                      {{ faq.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </div>
                </div>
                <div class="flex sm:flex-col gap-2">
                  <button
                    @click="openModal(faq)"
                    class="flex-1 sm:flex-none bg-yellow-500 text-white px-3 py-2 rounded-lg hover:bg-yellow-600 transition text-sm"
                  >
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="confirmDelete(faq)"
                    class="flex-1 sm:flex-none bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                  >
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="bg-white rounded-xl shadow p-12 text-center">
          <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-gray-500 text-lg">Belum ada FAQ. Klik "Tambah FAQ" untuk menambahkan.</p>
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
              {{ editingFaq ? 'Edit FAQ' : 'Tambah FAQ Baru' }}
            </h3>
            <form @submit.prevent="submitForm" class="space-y-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pertanyaan *</label>
                <input
                  v-model="form.pertanyaan"
                  type="text"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Contoh: Bagaimana cara mengajukan pemasangan sambungan baru?"
                />
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Jawaban *</label>
                <textarea
                  v-model="form.jawaban"
                  required
                  rows="5"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                  placeholder="Tulis jawaban lengkap untuk pertanyaan ini..."
                ></textarea>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">Urutan</label>
                  <input
                    v-model.number="form.urutan"
                    type="number"
                    min="0"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  <p class="text-xs text-gray-500 mt-1">Urutan tampilan (semakin kecil, semakin atas)</p>
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
                  {{ editingFaq ? 'Perbarui' : 'Simpan' }}
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
  faqs: Array
});

const showModal = ref(false);
const editingFaq = ref(null);
const notification = reactive({ show: false, message: '' });

const form = reactive({
  pertanyaan: '',
  jawaban: '',
  urutan: 0,
  is_active: true
});

function openModal(faq = null) {
  editingFaq.value = faq;
  if (faq) {
    form.pertanyaan = faq.pertanyaan;
    form.jawaban = faq.jawaban;
    form.urutan = faq.urutan;
    form.is_active = faq.is_active;
  } else {
    resetForm();
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editingFaq.value = null;
  resetForm();
}

function resetForm() {
  form.pertanyaan = '';
  form.jawaban = '';
  form.urutan = 0;
  form.is_active = true;
}

function submitForm() {
  if (editingFaq.value) {
    router.put(`/faqs/${editingFaq.value.id}`, form, {
      onSuccess: () => {
        closeModal();
        showNotification('FAQ berhasil diperbarui');
      }
    });
  } else {
    router.post('/faqs', form, {
      onSuccess: () => {
        closeModal();
        showNotification('FAQ berhasil ditambahkan');
      }
    });
  }
}

function confirmDelete(faq) {
  if (confirm(`Hapus FAQ "${faq.pertanyaan}"?`)) {
    router.delete(`/faqs/${faq.id}`, {
      onSuccess: () => showNotification('FAQ berhasil dihapus')
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
