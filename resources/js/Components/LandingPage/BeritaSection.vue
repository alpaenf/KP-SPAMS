<template>
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Berita & Pengumuman</h2>
        <p class="text-lg text-gray-600">Update terbaru dari KP-SPAMS</p>
      </div>

      <div v-if="beritas && beritas.length > 0" class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article
          v-for="berita in beritas"
          :key="berita.id"
          class="bg-white rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group"
        >
          <!-- Thumbnail -->
          <div v-if="berita.thumbnail" class="h-48 overflow-hidden bg-gray-200">
            <img
              :src="`/storage/${berita.thumbnail}`"
              :alt="berita.judul"
              class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
            />
          </div>
          <div v-else class="h-48 bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
            <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2v-5.5a2.972 2.972 0 002-2.5V9"></path>
            </svg>
          </div>

          <!-- Content -->
          <div class="p-6">
            <!-- Meta -->
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm text-blue-600 font-semibold">{{ formatDate(berita.tanggal_posting) }}</span>
              <span v-if="berita.kategori" class="text-xs px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                {{ berita.kategori }}
              </span>
            </div>

            <!-- Judul -->
            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition">
              {{ berita.judul }}
            </h3>

            <!-- Konten -->
            <p class="text-gray-600 text-sm line-clamp-3 mb-4">{{ berita.konten }}</p>

            <!-- CTA -->
            <button 
              @click="openModal(berita)"
              class="text-blue-600 font-semibold flex items-center gap-2 group/btn hover:text-blue-700 transition"
            >
              Baca Selengkapnya
              <svg class="w-4 h-4 group-hover/btn:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </article>
      </div>

      <div v-else class="text-center py-12">
        <p class="text-gray-500">Loading berita...</p>
      </div>
    </div>

    <!-- Modal Detail Berita -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header Modal -->
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-start">
          <div class="flex-1 pr-4">
            <div class="flex items-center gap-3 mb-2">
              <span class="text-sm text-blue-600 font-semibold">{{ formatDate(selectedBerita.tanggal_posting) }}</span>
              <span v-if="selectedBerita.kategori" class="text-xs px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                {{ selectedBerita.kategori }}
              </span>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">{{ selectedBerita.judul }}</h2>
          </div>
          <button 
            @click="closeModal"
            class="flex-shrink-0 p-2 hover:bg-gray-100 rounded-full transition"
          >
            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Thumbnail -->
        <div v-if="selectedBerita.thumbnail" class="w-full">
          <img
            :src="`/storage/${selectedBerita.thumbnail}`"
            :alt="selectedBerita.judul"
            class="w-full h-auto max-h-96 object-cover"
          />
        </div>

        <!-- Content -->
        <div class="p-6">
          <div class="prose prose-lg max-w-none">
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ selectedBerita.konten }}</p>
          </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 p-6 bg-gray-50">
          <button 
            @click="closeModal"
            class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const beritas = ref([]);
const showModal = ref(false);
const selectedBerita = ref(null);

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

const openModal = (berita) => {
  selectedBerita.value = berita;
  showModal.value = true;
  document.body.style.overflow = 'hidden';
};

const closeModal = () => {
  showModal.value = false;
  selectedBerita.value = null;
  document.body.style.overflow = 'auto';
};

onMounted(async () => {
  try {
    const response = await fetch(`/api/berita?t=${Date.now()}`);
    beritas.value = await response.json();
  } catch (error) {
    console.error('Error loading berita:', error);
  }
});
</script>
