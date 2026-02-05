<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Kelola Visi & Misi</h1>
        <p class="text-gray-600 mt-2">Atur visi dan misi KP-SPAMS</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-8">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Visi -->
          <div>
            <label for="visi" class="block text-sm font-medium text-gray-700 mb-2">
              Visi KP-SPAMS
            </label>
            <textarea
              id="visi"
              v-model="form.visi"
              rows="4"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
              <div v-for="(misi, index) in form.misi" :key="index" class="flex gap-2">
                <input
                  type="text"
                  v-model="form.misi[index]"
                  class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  :placeholder="`Misi ${index + 1}...`"
                  required
                />
                <button
                  v-if="form.misi.length > 1"
                  type="button"
                  @click="removeMisi(index)"
                  class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                >
                  Hapus
                </button>
              </div>
            </div>
            <button
              type="button"
              @click="addMisi"
              class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
              + Tambah Misi
            </button>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-3 pt-4">
            <button
              type="submit"
              class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium"
            >
              Simpan Perubahan
            </button>
            <Link
              href="/dashboard"
              class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition font-medium"
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
