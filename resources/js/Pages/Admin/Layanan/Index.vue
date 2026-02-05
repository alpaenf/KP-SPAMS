<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-4 py-8">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">Kelola Layanan</h1>
          <p class="text-gray-600 mt-2">Kelola layanan KP-SPAMS yang ditampilkan di landing page</p>
        </div>
        <Link href="/admin/layanan/create" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
          + Tambah Layanan
        </Link>
      </div>

      <!-- Success Message -->
      <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
        {{ $page.props.flash.success }}
      </div>

      <!-- Layanan Grid -->
      <div class="grid gap-6">
        <div v-for="layanan in layanans.data" :key="layanan.id" class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
          <div class="flex gap-6 items-start">
            <div v-if="layanan.foto" class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
              <img
                :src="`/storage/${layanan.foto}`"
                :alt="layanan.judul"
                class="w-full h-full object-cover"
              />
            </div>
            <div class="flex-1">
              <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ layanan.judul }}</h3>
              <p class="text-gray-700 mb-4">{{ layanan.deskripsi }}</p>
              <div class="flex gap-2">
                <Link :href="`/admin/layanan/${layanan.id}/edit`" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                  Edit
                </Link>
                <button @click="deleteLayanan(layanan.id)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="layanans.links" class="mt-8">
        <div class="flex justify-center gap-2">
          <Link
            v-for="link in layanans.links"
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
import { Link, router } from '@inertiajs/vue3';

defineProps({
  layanans: Object,
});

const deleteLayanan = (id) => {
  if (confirm('Yakin ingin menghapus layanan ini?')) {
    router.delete(`/admin/layanan/${id}`);
  }
};
</script>
