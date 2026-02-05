<template>
  <section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-900 mb-4 animate-on-scroll">Galeri</h2>
        <p class="text-lg text-gray-600 animate-on-scroll" style="animation-delay: 0.1s">Dokumentasi kegiatan dan infrastruktur KP-SPAMS</p>
      </div>

      <div v-if="galeris && galeris.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="(galeri, index) in galeris"
          :key="galeri.id"
          class="group relative overflow-hidden rounded-2xl shadow-lg md:hover:shadow-2xl active:shadow-xl transition-all duration-500 cursor-pointer h-72 animate-on-scroll touch-manipulation"
          :style="`animation-delay: ${0.1 * (index % 6)}s`"
        >
          <!-- Gambar -->
          <img
            :src="`/storage/${galeri.foto}`"
            :alt="galeri.caption"
            class="w-full h-full object-cover md:group-hover:scale-110 group-active:scale-105 transition-transform duration-700 ease-out"
          />

          <!-- Overlay -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 md:group-hover:opacity-100 group-active:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6">
            <h3 v-if="galeri.caption" class="text-white font-bold text-lg mb-2 transform translate-y-4 md:group-hover:translate-y-0 group-active:translate-y-0 transition-transform duration-500">{{ galeri.caption }}</h3>
            <p v-if="galeri.kategori" class="text-gray-200 text-sm transform translate-y-4 md:group-hover:translate-y-0 group-active:translate-y-0 transition-transform duration-500 delay-75">{{ galeri.kategori }}</p>
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

  // Intersection Observer for scroll animations
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
        }
      });
    },
    {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px',
    }
  );

  // Observe all elements with animate-on-scroll class
  setTimeout(() => {
    document.querySelectorAll('.animate-on-scroll').forEach((el) => {
      observer.observe(el);
    });
  }, 100);
});
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-on-scroll {
  opacity: 0;
  transform: translateY(30px);
}

.animate-in {
  animation: fadeInUp 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
}
</style>
