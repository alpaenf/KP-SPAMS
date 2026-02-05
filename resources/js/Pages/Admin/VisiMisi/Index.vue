<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-6 sm:py-8">
      <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-gray-900">Kelola Visi & Misi</h1>
        <p class="text-sm sm:text-base text-gray-600 mt-2">Atur visi dan misi KP-SPAMS</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-4 sm:p-8">
        <form @submit.prevent="submitForm" class="space-y-4 sm:space-y-6">
          <!-- Visi -->
          <div>
            <label for="visi" class="block text-sm font-medium text-gray-700 mb-2">
              Visi KP-SPAMS
            </label>
            <textarea
              id="visi"
              v-model="form.visi"
              rows="4"
              class="w-full px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Masukkan visi KP-SPAMS..."
              required
            ></textarea>
          </div>

          <!-- Misi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Misi KP-SPAMS
            </label>
            <div class="space-y-3">
              <div v-for="(misi, index) in form.misi" :key="index" class="flex flex-col sm:flex-row gap-2">
                <input
                  type="text"
                  v-model="form.misi[index]"
                  class="flex-1 px-3 sm:px-4 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :placeholder="`Misi ${index + 1}...`"
                  required
                />
                <button
                  v-if="form.misi.length > 1"
                  type="button"
                  @click="removeMisi(index)"
                  class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 active:bg-red-700 transition font-medium touch-manipulation flex items-center justify-center gap-1"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Hapus
                </button>
              </div>
            </div>
            <button
              type="button"
              @click="addMisi"
              class="mt-3 w-full sm:w-auto px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 active:bg-blue-700 transition font-medium touch-manipulation"
            >
              + Tambah Misi
            </button>
          </div>

          <!-- Submit Button -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button
              type="submit"
              class="w-full sm:w-auto px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 active:bg-blue-700 transition font-medium touch-manipulation"
            >
              Simpan Perubahan
            </button>
            <Link
              href="/dashboard"
              class="w-full sm:w-auto px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 active:bg-gray-500 transition font-medium text-center touch-manipulation"
            >
              Batal
            </Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  visiMisi: Object,
});

const form = useForm({
  visi: props.visiMisi?.visi || '',
  misi: props.visiMisi?.misi || [''],
});

const addMisi = () => {
  form.misi.push('');
};

const removeMisi = (index) => {
  form.misi.splice(index, 1);
};

const submitForm = () => {
  form.put(route('visi-misi.update'));
};
</script>
