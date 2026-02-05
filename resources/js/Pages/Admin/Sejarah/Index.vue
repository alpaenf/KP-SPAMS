<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Kelola Sejarah</h1>
        <p class="text-gray-600 mt-2">Atur sejarah terbentuknya aplikasi KP-SPAMS</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-8">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Konten Sejarah -->
          <div>
            <label for="konten" class="block text-sm font-medium text-gray-700 mb-2">
              Sejarah Aplikasi
            </label>
            <textarea
              id="konten"
              v-model="form.konten"
              rows="10"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-sans"
              placeholder="Masukkan sejarah terbentuknya aplikasi KP-SPAMS..."
              required
            ></textarea>
            <span v-if="form.errors.konten" class="text-red-500 text-sm mt-1">{{ form.errors.konten }}</span>
          </div>

          <!-- Foto Sejarah -->
          <div>
            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
              Foto/Gambar Sejarah
            </label>
            <input
              id="foto"
              type="file"
              @change="form.foto = $event.target.files[0]"
              accept="image/*"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p class="text-gray-500 text-sm mt-1">Maksimal 5MB, format: JPG, PNG, GIF</p>
            <span v-if="form.errors.foto" class="text-red-500 text-sm mt-1">{{ form.errors.foto }}</span>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-3 pt-4">
            <button
              type="submit"
              class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
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

const props = defineProps({
  sejarah: Object,
});

const form = useForm({
  konten: props.sejarah?.konten || '',
  foto: null,
});

const submitForm = () => {
  form.put(route('sejarah.update'));
};
</script>
