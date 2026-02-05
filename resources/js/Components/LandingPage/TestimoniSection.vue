<template>
  <section class="py-20 bg-gradient-to-br from-blue-50 to-blue-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Testimoni Pelanggan</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Apa kata pelanggan kami tentang layanan KP-SPAMS</p>
      </div>

      <!-- Testimoni List -->
      <div v-if="testimonis.length > 0" class="grid md:grid-cols-3 gap-6 mb-12">
        <div
          v-for="testimoni in testimonis"
          :key="testimoni.id"
          class="bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow p-6 animate-on-scroll"
        >
            <!-- Rating Stars -->
            <div class="flex items-center gap-1 mb-3">
              <svg
                v-for="i in 5"
                :key="i"
                :class="{
                  'w-5 h-5': true,
                  'text-yellow-400': i <= testimoni.rating,
                  'text-gray-300': i > testimoni.rating
                }"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            </div>
            
            <!-- Testimoni Text -->
            <p class="text-gray-700 mb-4 italic">"{{ testimoni.isi_testimoni }}"</p>
            
            <!-- Profile -->
            <div class="border-t pt-4">
              <p class="font-bold text-gray-900">{{ testimoni.nama }}</p>
              <p v-if="testimoni.pekerjaan" class="text-sm text-gray-600">{{ testimoni.pekerjaan }}</p>
              <p v-else class="text-sm text-gray-500 italic">Pelanggan KP-SPAMS</p>
            </div>
        </div>
      </div>

      <!-- Form Testimoni -->
      <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl p-8 animate-on-scroll">
        <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">Bagikan Pengalaman Anda</h3>
        <p class="text-gray-600 text-center mb-6">Testimoni Anda sangat berarti bagi kami dan pelanggan lainnya</p>
        
        <form @submit.prevent="submitTestimoni" class="space-y-6">
          <!-- Nama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.nama"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Nama lengkap Anda"
            />
          </div>

          <!-- Pekerjaan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Pekerjaan (Opsional)
            </label>
            <input
              v-model="form.pekerjaan"
              type="text"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Contoh: Ibu Rumah Tangga, Petani, dll"
            />
          </div>

          <!-- Rating -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Rating <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-2">
              <button
                v-for="i in 5"
                :key="i"
                type="button"
                @click="form.rating = i"
                class="focus:outline-none"
              >
                <svg
                  :class="{
                    'w-8 h-8 transition-colors': true,
                    'text-yellow-400 hover:text-yellow-500': i <= form.rating,
                    'text-gray-300 hover:text-gray-400': i > form.rating
                  }"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Klik bintang untuk memberikan rating</p>
          </div>

          <!-- Testimoni -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Testimoni <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="form.isi_testimoni"
              required
              rows="5"
              maxlength="1000"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Ceritakan pengalaman Anda menggunakan layanan KP-SPAMS..."
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">{{ form.isi_testimoni.length }}/1000 karakter</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isSubmitting ? 'Mengirim...' : 'Kirim Testimoni' }}
          </button>
        </form>

        <!-- Success Message -->
        <div v-if="showSuccessMessage" class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
          <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <div>
            <p class="font-semibold text-blue-900">Terima Kasih!</p>
            <p class="text-sm text-blue-800">Testimoni Anda sedang menunggu persetujuan dan akan ditampilkan setelah diverifikasi.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const testimonis = ref([]);
const isSubmitting = ref(false);
const showSuccessMessage = ref(false);

const form = ref({
  nama: '',
  pekerjaan: '',
  rating: 5,
  isi_testimoni: '',
});

const loadTestimoni = async () => {
  try {
    const response = await fetch('/api/testimoni');
    testimonis.value = await response.json();
  } catch (error) {
    console.error('Error loading testimoni:', error);
  }
};

const submitTestimoni = () => {
  isSubmitting.value = true;
  
  router.post('/testimoni', form.value, {
    onSuccess: () => {
      // Reset form
      form.value = {
        nama: '',
        pekerjaan: '',
        rating: 5,
        isi_testimoni: '',
      };
      
      showSuccessMessage.value = true;
      isSubmitting.value = false;
      
      // Hide success message after 5 seconds
      setTimeout(() => {
        showSuccessMessage.value = false;
      }, 5000);
    },
    onError: () => {
      isSubmitting.value = false;
      alert('Gagal mengirim testimoni. Silakan coba lagi.');
    },
  });
};

onMounted(() => {
  loadTestimoni();
});
</script>
