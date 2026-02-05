<template>
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Galeri</h2>
        <p class="text-lg text-gray-600">Dokumentasi kegiatan dan infrastruktur KP-SPAMS</p>
      </div>

      <div v-if="galeris && galeris.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="galeri in galeris"
          :key="galeri.id"
          class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 cursor-pointer h-72"
        >
          <!-- Gambar -->
          <img
            :src="`/storage/${galeri.foto}`"
            :alt="galeri.caption"
            class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
          />

          <!-- Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-6">
            <h3 v-if="galeri.caption" class="text-white font-bold text-lg mb-2">{{ galeri.caption }}</h3>
            <p v-if="galeri.kategori" class="text-gray-200 text-sm">{{ galeri.kategori }}</p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-500">Loading galeri...</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const galeris = ref([]);

onMounted(async () => {
  try {
    const response = await fetch(`/api/galeri?t=${Date.now()}`);
    galeris.value = await response.json();
  } catch (error) {
    console.error('Error loading galeri:', error);
  }
});
</script>
