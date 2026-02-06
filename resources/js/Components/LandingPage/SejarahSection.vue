<template>
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12 animate-on-scroll">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Sejarah & Latar Belakang</h2>
        <p class="text-lg text-gray-600">Perjalanan kami dalam menyediakan air bersih</p>
      </div>

      <div v-if="sejarah" class="bg-white rounded-2xl shadow-lg overflow-hidden animate-on-scroll">
        <div class="grid md:grid-cols-2 gap-8 items-center">
          <!-- Konten -->
          <div class="p-8 md:p-12">
            <p class="text-gray-800 text-lg leading-relaxed whitespace-pre-wrap">{{ sejarah.konten }}</p>
          </div>

          <!-- Foto -->
          <div v-if="sejarah.foto" class="relative h-96 md:h-full overflow-hidden">
            <img
              :src="`/storage/${sejarah.foto}`"
              :alt="sejarah.konten"
              class="w-full h-full object-cover"
            />
          </div>
          <div v-else class="h-96 bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
            <svg class="w-24 h-24 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-500">Loading sejarah...</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const sejarah = ref(null);

onMounted(async () => {
  try {
    const response = await fetch(`/api/sejarah?t=${Date.now()}`);
    sejarah.value = await response.json();
  } catch (error) {
    console.error('Error loading sejarah:', error);
  }
});
</script>
