<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
      <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-blue-800 mb-2">Login Pengelola</h2>
        <p class="text-gray-600">KP-SPAMS Desa</p>
      </div>

      <form @submit.prevent="submitLogin" class="space-y-6">
        <div v-if="errors.message" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
          {{ errors.message }}
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
            placeholder="email@kpspams.id"
          />
          <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
            placeholder="••••••••"
          />
          <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
        </div>

        <div class="flex items-center">
          <input
            id="remember"
            v-model="form.remember"
            type="checkbox"
            class="h-4 w-4 text-blue-800 focus:ring-blue-800 border-gray-300 rounded"
          />
          <label for="remember" class="ml-2 block text-sm text-gray-700">
            Ingat Saya
          </label>
        </div>

        <button
          type="submit"
          :disabled="processing"
          class="w-full bg-blue-800 text-white py-3 px-4 rounded-lg hover:bg-blue-900 transition-colors duration-200 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="processing">Memproses...</span>
          <span v-else>Masuk</span>
        </button>
      </form>

      <div class="mt-6 text-center">
        <Link href="/" class="text-sm text-blue-800 hover:text-blue-900">
          ← Kembali ke Beranda
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
});

const form = ref({
  email: '',
  password: '',
  remember: false
});

const processing = ref(false);

const submitLogin = () => {
  processing.value = true;
  
  router.post('/login', form.value, {
    onFinish: () => {
      processing.value = false;
    },
    onError: () => {
      form.value.password = '';
    }
  });
};
</script>
