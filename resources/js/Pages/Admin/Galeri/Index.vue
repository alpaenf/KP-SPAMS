<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-4 py-8">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">Kelola Galeri</h1>
          <p class="text-gray-600 mt-2">Kelola foto-foto KP-SPAMS</p>
        </div>
        <button
          @click="showUploadModal = true"
          class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium"
        >
          + Tambah Foto
        </button>
      </div>

      <!-- Upload Modal -->
      <div v-if="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full mx-4">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Upload Foto</h2>
          <form @submit.prevent="uploadFoto" class="space-y-4">
            <div>
              <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Foto
              </label>
              <input
                id="foto"
                type="file"
                @change="uploadForm.foto = $event.target.files[0]"
                accept="image/*"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                required
              />
            </div>

            <div>
              <label for="caption" class="block text-sm font-medium text-gray-700 mb-2">
                Caption
              </label>
              <input
                id="caption"
                type="text"
                v-model="uploadForm.caption"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                placeholder="Deskripsi singkat foto..."
              />
            </div>

            <div>
              <label for="kategori" class="block text-sm font-medium text-gray-700 mb-2">
                Kategori
              </label>
              <input
                id="kategori"
                type="text"
                v-model="uploadForm.kategori"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                placeholder="Contoh: Aktivitas, Acara, Infrastruktur..."
              />
            </div>

            <div class="flex gap-3">
              <button
                type="submit"
                class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
                :disabled="uploadForm.processing"
              >
                {{ uploadForm.processing ? 'Uploading...' : 'Upload' }}
              </button>
              <button
                type="button"
                @click="showUploadModal = false"
                class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition"
              >
                Batal
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Foto Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="galeri in galeris.data" :key="galeri.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition group">
          <div class="relative aspect-square overflow-hidden bg-gray-200">
            <img
              :src="`/storage/${galeri.foto}`"
              :alt="galeri.caption"
              class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
            />
          </div>
          <div class="p-4">
            <p v-if="galeri.caption" class="text-gray-900 font-medium mb-2">{{ galeri.caption }}</p>
            <p v-if="galeri.kategori" class="text-sm text-gray-600 mb-3">{{ galeri.kategori }}</p>
            <div class="flex gap-2">
              <button
                @click="editGaleri(galeri)"
                class="flex-1 px-3 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition"
              >
                Edit
              </button>
              <button
                @click="deleteGaleri(galeri.id)"
                class="flex-1 px-3 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition"
              >
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="galeris.links" class="mt-8">
        <div class="flex justify-center gap-2">
          <Link
            v-for="link in galeris.links"
            :key="link.label"
            :href="link.url"
            :class="{
              'px-4 py-2 rounded-lg': true,
              'bg-blue-500 text-white': link.active,
              'bg-gray-200 text-gray-800 hover:bg-gray-300': !link.active,
              'opacity-50 cursor-not-allowed': !link.url,
            }"
            v-html="link.label"
          ></Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  galeris: Object,
});

const showUploadModal = ref(false);

const uploadForm = useForm({
  foto: null,
  caption: '',
  kategori: '',
});

const uploadFoto = () => {
  uploadForm.post(route('galeri.store'), {
    onSuccess: () => {
      uploadForm.reset();
      showUploadModal.value = false;
    },
  });
};

const editGaleri = (galeri) => {
  // Implement edit modal if needed
};

const deleteGaleri = (id) => {
  if (confirm('Yakin ingin menghapus foto ini?')) {
    router.delete(route('galeri.destroy', id), {
      onSuccess: () => {
        // Deleted successfully
      },
    });
  }
};
</script>
