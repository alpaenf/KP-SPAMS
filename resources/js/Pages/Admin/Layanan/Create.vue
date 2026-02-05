<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Tambah Layanan</h1>
        <p class="text-gray-600 mt-2">Buat layanan baru untuk KP-SPAMS</p>
      </div>

      <div class="bg-white rounded-lg shadow-lg p-8">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- Judul -->
          <div>
            <label for="judul" class="block text-sm font-medium text-gray-700 mb-2">
              Nama Layanan
            </label>
            <input
              id="judul"
              type="text"
              v-model="form.judul"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Contoh: Penyediaan Air Bersih..."
              required
            />
            <span v-if="form.errors.judul" class="text-red-500 text-sm mt-1">{{ form.errors.judul }}</span>
          </div>

          <!-- Deskripsi -->
          <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
              Deskripsi Layanan
            </label>
            <textarea
              id="deskripsi"
              v-model="form.deskripsi"
              rows="6"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Deskripsikan layanan ini..."
              required
            ></textarea>
            <span v-if="form.errors.deskripsi" class="text-red-500 text-sm mt-1">{{ form.errors.deskripsi }}</span>
          </div>

          <!-- Icon (optional) -->
          <div>
            <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
              Icon (Opsional)
            </label>
            <input
              id="icon"
              type="text"
              v-model="form.icon"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Contoh: water-drop, shield-check, dll..."
            />
            <p class="text-gray-500 text-sm mt-1">Icon dari Heroicons atau emoji</p>
          </div>

          <!-- Foto -->
          <div>
            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
              Gambar Layanan (Opsional)
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
              {{ form.processing ? 'Menyimpan...' : 'Simpan Layanan' }}
            </button>
            <Link
              href="/admin/layanan"
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
  deskripsi: '',
  icon: '',
  foto: null,
});

const submitForm = () => {
  form.post(route('layanan.store'));
};
</script>
