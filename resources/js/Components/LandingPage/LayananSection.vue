<template>
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12 animate-on-scroll">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Layanan Kami</h2>
        <p class="text-lg text-gray-600">Komitmen KP-SPAMS untuk kesejahteraan masyarakat</p>
      </div>

      <div v-if="layanans && layanans.length > 0" class="grid md:grid-cols-3 gap-6 animate-on-scroll">
        <div
          v-for="layanan in layanans"
          :key="layanan.id"
          class="bg-gradient-to-br from-blue-50 to-white p-6 rounded-xl shadow-md hover:shadow-xl hover:scale-[1.02] transition-all duration-300 border border-blue-100"
        >
          <!-- Icon/Foto -->
          <div v-if="layanan.foto" class="mb-4 h-40 rounded-lg overflow-hidden bg-gray-200">
            <img
              :src="`/storage/${layanan.foto}`"
              :alt="layanan.judul"
              class="w-full h-full object-cover hover:scale-110 transition duration-300"
            />
          </div>
          <div v-else class="mb-4 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>

          <!-- Judul -->
          <h3 class="text-xl font-bold text-gray-900 mb-2">{{ layanan.judul }}</h3>

          <!-- Deskripsi -->
          <p class="text-sm text-gray-700 mb-4 leading-relaxed">{{ layanan.deskripsi }}</p>

          <!-- CTA Button -->
          <button class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium text-sm">
            Pelajari Lebih Lanjut
          </button>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-500">Loading layanan...</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const layanans = ref([]);

onMounted(async () => {
  try {
    const response = await fetch(`/api/layanan?t=${Date.now()}`);
    layanans.value = await response.json();
  } catch (error) {
    console.error('Error loading layanan:', error);
  }
});
</script>
