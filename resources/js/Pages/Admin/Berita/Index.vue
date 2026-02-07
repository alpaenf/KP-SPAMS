<template>
  <AppLayout>
    <div class="max-w-6xl mx-auto px-4 py-8">
      <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">Kelola Berita</h1>
          <p class="text-gray-600 mt-2">Kelola berita dan artikel KP-SPAMS</p>
        </div>
        <Link href="/admin/berita/create" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
          + Tambah Berita
        </Link>
      </div>

      <!-- Success Message -->
      <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
        {{ $page.props.flash.success }}
      </div>

      <!-- Berita List -->
      <div class="grid gap-6">
        <div v-for="berita in beritas.data" :key="berita.id" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <div class="flex-1">
                <h3 class="text-2xl font-bold text-gray-900">{{ berita.judul }}</h3>
                <p class="text-gray-600 text-sm mt-1">
                  {{ new Date(berita.tanggal_posting).toLocaleDateString('id-ID') }}
                </p>
              </div>
              <span v-if="!berita.is_published" class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full">
                Belum Dipublikasi
              </span>
            </div>
            
            <p class="text-gray-700 mb-4 line-clamp-2">{{ berita.konten }}</p>
            
            <div class="flex gap-2">
              <Link :href="`/admin/berita/${berita.id}/edit`" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                Edit
              </Link>
              <button @click="deleteBerita(berita.id)" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="beritas.links" class="mt-8">
        <div class="flex justify-center gap-2">
          <Link
            v-for="link in beritas.links"
            :key="link.label"
            :href="link.url"
            :class="{
              'px-4 py-2 rounded-lg': true,
              'bg-blue-500 text-white': link.active,
              'bg-gray-200 text-gray-800 hover:bg-gray-300': !link.active,
              'opacity-50 cursor-not-allowed': !link.url,
            }"
          >
            <span v-if="link.label.includes('Previous')">← Previous</span>
            <span v-else-if="link.label.includes('Next')">Next →</span>
            <span v-else-if="link.label.includes('laquo')">«</span>
            <span v-else-if="link.label.includes('raquo')">»</span>
            <span v-else>{{ link.label }}</span>
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
  beritas: Object,
});

const deleteBerita = (id) => {
  if (confirm('Yakin ingin menghapus berita ini?')) {
    router.delete(`/admin/berita/${id}`);
  }
};
</script>
