<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
          <Link
            href="/admin/berita"
            class="inline-flex items-center text-gray-600 hover:text-gray-900 transition"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
          </Link>
        </div>
        <h1 class="text-4xl font-bold text-gray-900">Tambah Berita</h1>
        <p class="text-gray-600 mt-2">Buat berita atau artikel baru untuk landing page</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-8">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Judul -->
          <div>
            <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
              Judul Berita
            </label>
            <input
              id="judul"
              type="text"
              v-model="form.judul"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Masukkan judul berita..."
              required
            />
            <span v-if="form.errors.judul" class="text-red-500 text-sm mt-1">{{ form.errors.judul }}</span>
          </div>

          <!-- Kategori -->
          <div>
            <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
              Kategori
            </label>
            <input
              id="kategori"
              type="text"
              v-model="form.kategori"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Contoh: Pengumuman, Berita, Acara..."
            />
          </div>

          <!-- Konten -->
          <div>
            <label for="konten" class="block text-sm font-medium text-gray-700 mb-2">
              Konten Berita
            </label>
            <textarea
              id="konten"
              v-model="form.konten"
              rows="10"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-sans"
              placeholder="Masukkan konten berita..."
              required
            ></textarea>
            <span v-if="form.errors.konten" class="text-red-500 text-sm mt-1">{{ form.errors.konten }}</span>
          </div>

          <!-- Thumbnail -->
          <div>
            <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
              Thumbnail/Gambar Utama
            </label>
            <input
              id="thumbnail"
              type="file"
              @change="form.thumbnail = $event.target.files[0]"
              accept="image/*"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <p class="text-gray-500 text-sm mt-1">Maksimal 2MB, format: JPG, PNG, GIF</p>
            <span v-if="form.errors.thumbnail" class="text-red-500 text-sm mt-1">{{ form.errors.thumbnail }}</span>
          </div>

          <!-- Publish Status -->
          <div class="flex items-center gap-3">
            <input
              id="is_published"
              type="checkbox"
              v-model="form.is_published"
              class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
            />
            <label for="is_published" class="text-sm font-medium text-gray-700">
              Publikasikan berita ini
            </label>
          </div>

          <!-- Submit Button -->
          <div class="flex gap-3 pt-4">
            <button
              type="submit"
              class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Menyimpan...' : 'Simpan Berita' }}
            </button>
            <Link
              href="/admin/berita"
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

const form = useForm({
  judul: '',
  kategori: '',
  konten: '',
  thumbnail: null,
  is_published: true,
});

const submitForm = () => {
  form.post(route('berita.store'));
};
</script>
