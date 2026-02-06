<template>
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12 animate-on-scroll">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Visi & Misi</h2>
        <p class="text-lg text-gray-600">Komitmen kami untuk kesejahteraan masyarakat</p>
      </div>

      <div v-if="visiMisi" class="grid md:grid-cols-2 gap-12 items-center animate-on-scroll">
        <!-- Visi -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
          <h3 class="text-2xl font-bold text-blue-900 mb-4 flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Visi
          </h3>
          <p class="text-gray-800 text-lg leading-relaxed">{{ visiMisi.visi }}</p>
        </div>

        <!-- Misi -->
        <div class="space-y-4">
          <h3 class="text-2xl font-bold text-blue-900 mb-6 flex items-center gap-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Misi
          </h3>
          <div class="space-y-3">
            <div
              v-for="(misi, index) in visiMisi.misi"
              :key="index"
              class="bg-white p-4 rounded-lg border-l-4 border-blue-500 shadow-sm hover:shadow-md transition"
            >
              <p class="text-gray-800 font-medium">{{ misi }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-500">Loading visi misi...</p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const visiMisi = ref(null);

const loadVisiMisi = async () => {
  try {
    // Add cache busting timestamp to prevent browser caching
    const response = await fetch(`/api/visi-misi?t=${Date.now()}`);
    visiMisi.value = await response.json();
    
    // Filter out any empty misi items
    if (visiMisi.value?.misi) {
      visiMisi.value.misi = visiMisi.value.misi.filter(m => m && m.trim() !== '');
    }
  } catch (error) {
    console.error('Error loading visi misi:', error);
  }
};

onMounted(loadVisiMisi);
</script>
