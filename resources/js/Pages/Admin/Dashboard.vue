<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-700 to-blue-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="bg-white/20 p-2 rounded-lg">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-white">Kelola Landing Page</h1>
              <p class="text-blue-100 mt-1">Manage semua konten landing page dari satu dashboard terpadu</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4">
          <div class="flex overflow-x-auto">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="{
                'flex items-center gap-2 py-4 px-6 border-b-2 font-medium text-sm transition-all whitespace-nowrap': true,
                'border-blue-600 text-blue-700 bg-blue-50': activeTab === tab.id,
                'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50': activeTab !== tab.id,
              }"
            >
              <!-- Document Text Icon -->
              <svg v-if="tab.icon === 'document-text'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <!-- Cog Icon -->
              <svg v-else-if="tab.icon === 'cog'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <!-- Newspaper Icon -->
              <svg v-else-if="tab.icon === 'newspaper'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
              <!-- Photograph Icon -->
              <svg v-else-if="tab.icon === 'photograph'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <!-- Book Open Icon -->
              <svg v-else-if="tab.icon === 'book-open'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              <!-- Chat Alt 2 Icon -->
              <svg v-else-if="tab.icon === 'chat-alt-2'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
              </svg>
              <span>{{ tab.label }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Content Area -->
      <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Visi & Misi Tab -->
        <div v-if="activeTab === 'visi-misi'" class="bg-white rounded-lg shadow p-4 sm:p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Visi & Misi KP-SPAMS</h2>
          <form @submit.prevent="submitVisiMisi" class="space-y-6">
            <!-- Visi -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Visi</label>
              <textarea
                v-model="visiMisiForm.visi"
                rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Masukkan visi KP-SPAMS..."
              ></textarea>
            </div>

            <!-- Misi -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-3">Misi</label>
              <div class="space-y-3">
                <div v-for="(misi, idx) in visiMisiForm.misi" :key="idx" class="flex flex-col sm:flex-row gap-2">
                  <input
                    type="text"
                    v-model="visiMisiForm.misi[idx]"
                    class="w-full sm:flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :placeholder="`Misi ${idx + 1}...`"
                  />
                  <button
                    v-if="visiMisiForm.misi.length > 1"
                    type="button"
                    @click="visiMisiForm.misi.splice(idx, 1)"
                    class="w-full sm:w-auto px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 shrink-0"
                  >
                    Hapus
                  </button>
                </div>
              </div>
              <button
                type="button"
                @click="visiMisiForm.misi.push('')"
                class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
              >
                + Tambah Misi
              </button>
            </div>

            <button
              type="submit"
              class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
              Simpan Visi & Misi
            </button>
          </form>
        </div>

        <!-- Layanan Tab -->
        <div v-if="activeTab === 'layanan'" class="space-y-6">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Kelola Layanan</h2>
            <button
              @click="showLayananForm = !showLayananForm"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
              {{ showLayananForm ? 'Batal' : '+ Tambah Layanan' }}
            </button>
          </div>

          <!-- Form Tambah/Edit Layanan -->
          <div v-if="showLayananForm" class="bg-white rounded-lg shadow p-4 sm:p-6">
            <h3 class="text-xl font-bold mb-4">{{ editingLayanan ? 'Edit' : 'Tambah' }} Layanan</h3>
            <form @submit.prevent="submitLayanan" class="space-y-4">
              <input
                type="text"
                v-model="layananForm.judul"
                placeholder="Nama Layanan"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
              <textarea
                v-model="layananForm.deskripsi"
                rows="3"
                placeholder="Deskripsi Layanan"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              ></textarea>
              <input
                type="text"
                v-model="layananForm.icon"
                placeholder="Icon (optional)"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Layanan</label>
                <input
                  type="file"
                  @change="e => layananForm.foto = e.target.files[0]"
                  accept="image/*"
                  id="layanan-foto"
                  class="hidden"
                />
                <label
                  for="layanan-foto"
                  class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 cursor-pointer"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Pilih Foto
                </label>
                <span v-if="layananForm.foto" class="ml-3 text-sm text-gray-600">{{ layananForm.foto.name }}</span>
              </div>
              <button
                type="submit"
                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
              >
                {{ editingLayanan ? 'Update' : 'Simpan' }} Layanan
              </button>
            </form>
          </div>

          <!-- List Layanan -->
          <div class="grid gap-4">
            <div
              v-for="layanan in layanans"
              :key="layanan.id"
              class="bg-white rounded-lg shadow p-4 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4"
            >
              <div class="flex-1 w-full">
                <h3 class="font-bold text-gray-900">{{ layanan.judul }}</h3>
                <p class="text-gray-600 text-sm">{{ layanan.deskripsi }}</p>
              </div>
              <div class="flex gap-2 w-full sm:w-auto">
                <button
                  @click="editLayanan(layanan)"
                  class="flex-1 sm:flex-none px-4 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600"
                >
                  Edit
                </button>
                <button
                  @click="deleteLayanan(layanan.id)"
                  class="flex-1 sm:flex-none px-4 py-2 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Berita Tab -->
        <div v-if="activeTab === 'berita'" class="space-y-6">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Kelola Berita</h2>
            <button
              @click="showBeritaForm = !showBeritaForm"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
              {{ showBeritaForm ? 'Batal' : '+ Tambah Berita' }}
            </button>
          </div>

          <!-- Form Tambah/Edit Berita -->
          <div v-if="showBeritaForm" class="bg-white rounded-lg shadow p-4 sm:p-6">
            <h3 class="text-xl font-bold mb-4">{{ editingBerita ? 'Edit' : 'Tambah' }} Berita</h3>
            <form @submit.prevent="submitBerita" class="space-y-4">
              <input
                type="text"
                v-model="beritaForm.judul"
                placeholder="Judul Berita"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              />
              <input
                type="text"
                v-model="beritaForm.kategori"
                placeholder="Kategori (optional)"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <textarea
                v-model="beritaForm.konten"
                rows="5"
                placeholder="Konten Berita"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              ></textarea>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Berita</label>
                <input
                  type="file"
                  @change="e => beritaForm.thumbnail = e.target.files[0]"
                  accept="image/*"
                  id="berita-thumbnail"
                  class="hidden"
                />
                <label
                  for="berita-thumbnail"
                  class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 cursor-pointer"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Pilih Gambar
                </label>
                <span v-if="beritaForm.thumbnail" class="ml-3 text-sm text-gray-600">{{ beritaForm.thumbnail.name }}</span>
              </div>
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="beritaForm.is_published" class="w-4 h-4" />
                <span class="text-sm font-medium text-gray-700">Publikasikan Berita</span>
              </label>
              <button
                type="submit"
                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
              >
                {{ editingBerita ? 'Update' : 'Simpan' }} Berita
              </button>
            </form>
          </div>

          <!-- List Berita -->
          <div class="grid gap-4">
            <div
              v-for="berita in beritas"
              :key="berita.id"
              class="bg-white rounded-lg shadow p-4 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4"
            >
              <div class="flex-1 w-full">
                <h3 class="font-bold text-gray-900">{{ berita.judul }}</h3>
                <p class="text-gray-600 text-sm">{{ berita.konten.substring(0, 100) }}...</p>
                <p class="text-xs text-gray-500 mt-2">
                  {{ new Date(berita.tanggal_posting).toLocaleDateString('id-ID') }}
                  <span v-if="!berita.is_published" class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                    Draft
                  </span>
                </p>
              </div>
              <div class="flex gap-2 w-full sm:w-auto">
                <button
                  @click="editBerita(berita)"
                  class="flex-1 sm:flex-none px-4 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600"
                >
                  Edit
                </button>
                <button
                  @click="deleteBerita(berita.id)"
                  class="flex-1 sm:flex-none px-4 py-2 bg-red-500 text-white rounded text-sm hover:bg-red-600"
                >
                  Hapus
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Galeri Tab -->
        <div v-if="activeTab === 'galeri'" class="space-y-6">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">Kelola Galeri</h2>
            <button
              @click="showGaleriForm = !showGaleriForm"
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
            >
              {{ showGaleriForm ? 'Batal' : '+ Upload Foto' }}
            </button>
          </div>

          <!-- Form Upload -->
          <div v-if="showGaleriForm" class="bg-white rounded-lg shadow p-4 sm:p-6">
            <h3 class="text-xl font-bold mb-4">Upload Foto Galeri</h3>
            <form @submit.prevent="submitGaleri" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Galeri</label>
                <input
                  type="file"
                  @change="e => galeriForm.foto = e.target.files[0]"
                  accept="image/*"
                  id="galeri-foto"
                  class="hidden"
                  required
                />
                <label
                  for="galeri-foto"
                  class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 cursor-pointer"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Pilih Foto
                </label>
                <span v-if="galeriForm.foto" class="ml-3 text-sm text-gray-600">{{ galeriForm.foto.name }}</span>
              </div>
              <input
                type="text"
                v-model="galeriForm.caption"
                placeholder="Caption (optional)"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <input
                type="text"
                v-model="galeriForm.kategori"
                placeholder="Kategori (optional)"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <button
                type="submit"
                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
              >
                Upload Foto
              </button>
            </form>
          </div>

          <!-- Grid Galeri -->
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="galeri in galeris"
              :key="galeri.id"
              class="bg-white rounded-lg shadow overflow-hidden group hover:shadow-lg transition"
            >
              <div class="aspect-square bg-gray-200 overflow-hidden relative">
                <img
                  :src="`/storage/${galeri.foto}`"
                  :alt="galeri.caption"
                  class="w-full h-full object-cover group-hover:scale-110 transition"
                />
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-end">
                  <button
                    @click="deleteGaleri(galeri.id)"
                    class="w-full px-2 py-2 bg-red-500 text-white text-sm hover:bg-red-600"
                  >
                    Hapus
                  </button>
                </div>
              </div>
              <div class="p-2">
                <p class="text-xs font-medium text-gray-700 truncate">{{ galeri.caption }}</p>
                <p class="text-xs text-gray-500">{{ galeri.kategori }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimoni Tab -->
        <div v-if="activeTab === 'testimoni'" class="bg-white rounded-lg shadow p-4 sm:p-6">
          <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Kelola Testimoni</h2>
          
          <div class="mb-4 sm:mb-6 flex gap-2 sm:gap-4 overflow-x-auto pb-2">
            <button
              @click="testimoniFilter = 'all'"
              :class="{
                'px-3 sm:px-4 py-2 rounded-lg font-medium transition whitespace-nowrap text-sm sm:text-base': true,
                'bg-blue-500 text-white': testimoniFilter === 'all',
                'bg-gray-200 text-gray-700 hover:bg-gray-300': testimoniFilter !== 'all'
              }"
            >
              Semua ({{ filteredTestimoni.length }})
            </button>
            <button
              @click="testimoniFilter = 'pending'"
              :class="{
                'px-3 sm:px-4 py-2 rounded-lg font-medium transition whitespace-nowrap text-sm sm:text-base': true,
                'bg-yellow-500 text-white': testimoniFilter === 'pending',
                'bg-gray-200 text-gray-700 hover:bg-gray-300': testimoniFilter !== 'pending'
              }"
            >
              Menunggu ({{ testimonis.filter(t => t.status === 'pending').length }})
            </button>
            <button
              @click="testimoniFilter = 'approved'"
              :class="{
                'px-3 sm:px-4 py-2 rounded-lg font-medium transition whitespace-nowrap text-sm sm:text-base': true,
                'bg-blue-500 text-white': testimoniFilter === 'approved',
                'bg-gray-200 text-gray-700 hover:bg-gray-300': testimoniFilter !== 'approved'
              }"
            >
              Disetujui ({{ testimonis.filter(t => t.status === 'approved').length }})
            </button>
            <button
              @click="testimoniFilter = 'rejected'"
              :class="{
                'px-3 sm:px-4 py-2 rounded-lg font-medium transition whitespace-nowrap text-sm sm:text-base': true,
                'bg-red-500 text-white': testimoniFilter === 'rejected',
                'bg-gray-200 text-gray-700 hover:bg-gray-300': testimoniFilter !== 'rejected'
              }"
            >
              Ditolak ({{ testimonis.filter(t => t.status === 'rejected').length }})
            </button>
          </div>

          <div class="space-y-4">
            <div
              v-for="testimoni in filteredTestimoni"
              :key="testimoni.id"
              class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition"
            >
              <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                <div class="flex-1">
                  <div class="flex items-center gap-2 sm:gap-3 mb-2 flex-wrap">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base">{{ testimoni.nama }}</h3>
                    <span
                      :class="{
                        'px-2 sm:px-3 py-1 rounded-full text-xs font-semibold': true,
                        'bg-yellow-100 text-yellow-800': testimoni.status === 'pending',
                        'bg-blue-100 text-blue-800': testimoni.status === 'approved',
                        'bg-red-100 text-red-800': testimoni.status === 'rejected'
                      }"
                    >
                      {{ testimoni.status === 'pending' ? 'Menunggu' : testimoni.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                    </span>
                  </div>
                  <p v-if="testimoni.pekerjaan" class="text-xs sm:text-sm text-gray-600 mb-2">{{ testimoni.pekerjaan }}</p>
                  <div class="flex items-center gap-1 mb-2">
                    <svg
                      v-for="i in 5"
                      :key="i"
                      :class="{
                        'w-4 h-4': true,
                        'text-yellow-400': i <= testimoni.rating,
                        'text-gray-300': i > testimoni.rating
                      }"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <p class="text-sm sm:text-base text-gray-700">{{ testimoni.isi_testimoni }}</p>
                  <p class="text-xs text-gray-500 mt-2">{{ new Date(testimoni.created_at).toLocaleDateString('id-ID') }}</p>
                </div>
                <div class="flex gap-2 sm:ml-4">
                  <button
                    v-if="testimoni.status !== 'approved'"
                    @click="approveTestimoni(testimoni.id)"
                    class="flex-1 sm:flex-none px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 active:bg-blue-700 transition touch-manipulation"
                    title="Setujui"
                  >
                    ✓
                  </button>
                  <button
                    v-if="testimoni.status !== 'rejected'"
                    @click="rejectTestimoni(testimoni.id)"
                    class="flex-1 sm:flex-none px-3 py-2 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 active:bg-yellow-700 transition touch-manipulation"
                    title="Tolak"
                  >
                    ✗
                  </button>
                  <button
                    @click="deleteTestimoni(testimoni.id)"
                    class="flex-1 sm:flex-none px-3 py-2 bg-red-500 text-white text-sm rounded hover:bg-red-600 active:bg-red-700 transition touch-manipulation"
                    title="Hapus"
                  >
                    🗑
                  </button>
                </div>
              </div>
            </div>

            <div v-if="filteredTestimoni.length === 0" class="text-center py-12 text-gray-500">
              <p class="text-lg">Tidak ada testimoni {{ testimoniFilter === 'all' ? '' : testimoniFilter === 'pending' ? 'yang menunggu' : testimoniFilter === 'approved' ? 'yang disetujui' : 'yang ditolak' }}</p>
            </div>
          </div>
        </div>

        <!-- Sejarah Tab -->
        <div v-if="activeTab === 'sejarah'" class="bg-white rounded-lg shadow p-4 sm:p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">Sejarah KP-SPAMS</h2>
          <form @submit.prevent="submitSejarah" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Konten Sejarah</label>
              <textarea
                v-model="sejarahForm.konten"
                rows="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Ceritakan sejarah KP-SPAMS..."
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Foto Sejarah</label>
              <input
                type="file"
                @change="e => sejarahForm.foto = e.target.files[0]"
                accept="image/*"
                id="sejarah-foto"
                class="hidden"
              />
              <label
                for="sejarah-foto"
                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 cursor-pointer"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Pilih Foto
              </label>
              <span v-if="sejarahForm.foto" class="ml-3 text-sm text-gray-600">{{ sejarahForm.foto.name }}</span>
              <div v-if="sejarahData?.foto" class="mt-4">
                <img
                  :src="`/storage/${sejarahData.foto}`"
                  :alt="sejarahData.konten"
                  class="max-w-xs rounded-lg"
                />
              </div>
            </div>

            <button
              type="submit"
              class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition"
            >
              Simpan Sejarah
            </button>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const activeTab = ref('visi-misi');

const tabs = [
  { id: 'visi-misi', label: 'Visi & Misi', icon: 'document-text' },
  { id: 'layanan', label: 'Layanan', icon: 'cog' },
  { id: 'berita', label: 'Berita', icon: 'newspaper' },
  { id: 'galeri', label: 'Galeri', icon: 'photograph' },
  { id: 'sejarah', label: 'Sejarah', icon: 'book-open' },
  { id: 'testimoni', label: 'Testimoni', icon: 'chat-alt-2' },
];

// Visi Misi
const visiMisiForm = ref({ visi: '', misi: [''] });

// Layanan
const showLayananForm = ref(false);
const editingLayanan = ref(null);
const layanans = ref([]);
const layananForm = ref({ judul: '', deskripsi: '', icon: '', foto: null });

// Berita
const showBeritaForm = ref(false);
const editingBerita = ref(null);
const beritas = ref([]);
const beritaForm = ref({
  judul: '',
  kategori: '',
  konten: '',
  thumbnail: null,
  is_published: true,
});

// Galeri
const showGaleriForm = ref(false);
const galeris = ref([]);
const galeriForm = ref({ foto: null, caption: '', kategori: '' });

// Sejarah
const sejarahData = ref(null);
const sejarahForm = ref({ konten: '', foto: null });

// Testimoni
const testimonis = ref([]);
const testimoniFilter = ref('all');
const filteredTestimoni = computed(() => {
  if (testimoniFilter.value === 'all') return testimonis.value;
  return testimonis.value.filter(t => t.status === testimoniFilter.value);
});

onMounted(() => {
  loadAllData();
});

const loadAllData = async () => {
  try {
    const timestamp = Date.now();
    const [visiMisiRes, layananRes, beritaRes, galeriRes, sejarahRes] = await Promise.all([
      fetch(`/api/visi-misi?t=${timestamp}`).then(r => r.json()),
      fetch(`/api/layanan?t=${timestamp}`).then(r => r.json()),
      fetch(`/api/berita?t=${timestamp}`).then(r => r.json()),
      fetch(`/api/galeri?t=${timestamp}`).then(r => r.json()),
      fetch(`/api/sejarah?t=${timestamp}`).then(r => r.json()),
    ]);

    if (visiMisiRes) {
      // Filter out empty misi
      const filteredMisi = visiMisiRes.misi ? visiMisiRes.misi.filter(m => m && m.trim() !== '') : [''];
      visiMisiForm.value = {
        visi: visiMisiRes.visi || '',
        misi: filteredMisi.length > 0 ? filteredMisi : [''],
      };
    }

    layanans.value = layananRes || [];
    beritas.value = beritaRes || [];
    galeris.value = galeriRes || [];
    
    // Load testimoni
    await loadTestimoni();
    sejarahData.value = sejarahRes;
    if (sejarahRes) {
      sejarahForm.value = { konten: sejarahRes.konten, foto: null };
    }
  } catch (error) {
    console.error('Error loading data:', error);
  }
};

const submitVisiMisi = () => {
  // Filter out empty misi
  const filteredMisi = visiMisiForm.value.misi.filter(m => m.trim() !== '');
  
  router.put(route('visi-misi.update'), {
    visi: visiMisiForm.value.visi,
    misi: filteredMisi
  }, {
    onSuccess: () => {
      alert('Visi & Misi berhasil disimpan!');
      loadAllData();
    },
  });
};

const submitLayanan = () => {
  const formData = new FormData();
  formData.append('judul', layananForm.value.judul);
  formData.append('deskripsi', layananForm.value.deskripsi);
  formData.append('icon', layananForm.value.icon);
  if (layananForm.value.foto) {
    formData.append('foto', layananForm.value.foto);
  }

  if (editingLayanan.value) {
    router.post(
      route('layanan.update', editingLayanan.value.id),
      formData,
      {
        onSuccess: () => {
          alert('Layanan berhasil diupdate!');
          showLayananForm.value = false;
          editingLayanan.value = null;
          loadAllData();
        },
      }
    );
  } else {
    router.post(route('layanan.store'), formData, {
      onSuccess: () => {
        alert('Layanan berhasil ditambahkan!');
        showLayananForm.value = false;
        layananForm.value = { judul: '', deskripsi: '', icon: '', foto: null };
        loadAllData();
      },
    });
  }
};

const editLayanan = (layanan) => {
  editingLayanan.value = layanan;
  layananForm.value = { ...layanan, foto: null };
  showLayananForm.value = true;
};

const deleteLayanan = (id) => {
  if (confirm('Yakin menghapus layanan ini?')) {
    router.delete(route('layanan.destroy', id), {
      onSuccess: () => {
        alert('Layanan berhasil dihapus!');
        loadAllData();
      },
    });
  }
};

const submitBerita = () => {
  const formData = new FormData();
  formData.append('judul', beritaForm.value.judul);
  formData.append('kategori', beritaForm.value.kategori);
  formData.append('konten', beritaForm.value.konten);
  formData.append('is_published', beritaForm.value.is_published ? 1 : 0);
  if (beritaForm.value.thumbnail) {
    formData.append('thumbnail', beritaForm.value.thumbnail);
  }

  if (editingBerita.value) {
    router.post(
      route('berita.update', editingBerita.value.id),
      formData,
      {
        onSuccess: () => {
          alert('Berita berhasil diupdate!');
          showBeritaForm.value = false;
          editingBerita.value = null;
          loadAllData();
        },
      }
    );
  } else {
    router.post(route('berita.store'), formData, {
      onSuccess: () => {
        alert('Berita berhasil ditambahkan!');
        showBeritaForm.value = false;
        beritaForm.value = {
          judul: '',
          kategori: '',
          konten: '',
          thumbnail: null,
          is_published: true,
        };
        loadAllData();
      },
    });
  }
};

const editBerita = (berita) => {
  editingBerita.value = berita;
  beritaForm.value = { ...berita, thumbnail: null };
  showBeritaForm.value = true;
};

const deleteBerita = (id) => {
  if (confirm('Yakin menghapus berita ini?')) {
    router.delete(route('berita.destroy', id), {
      onSuccess: () => {
        alert('Berita berhasil dihapus!');
        loadAllData();
      },
    });
  }
};

const submitGaleri = () => {
  if (!galeriForm.value.foto) {
    alert('Pilih foto terlebih dahulu!');
    return;
  }

  const formData = new FormData();
  formData.append('foto', galeriForm.value.foto);
  formData.append('caption', galeriForm.value.caption);
  formData.append('kategori', galeriForm.value.kategori);

  router.post(route('galeri.store'), formData, {
    onSuccess: () => {
      alert('Foto berhasil diupload!');
      showGaleriForm.value = false;
      galeriForm.value = { foto: null, caption: '', kategori: '' };
      loadAllData();
    },
    onError: (errors) => {
      alert('Gagal upload foto: ' + Object.values(errors).join(', '));
    }
  });
};

const deleteGaleri = (id) => {
  if (confirm('Yakin menghapus foto ini?')) {
    router.delete(route('galeri.destroy', id), {
      onSuccess: () => {
        alert('Foto berhasil dihapus!');
        loadAllData();
      },
    });
  }
};

const submitSejarah = () => {
  const formData = new FormData();
  formData.append('konten', sejarahForm.value.konten);
  if (sejarahForm.value.foto) {
    formData.append('foto', sejarahForm.value.foto);
  }

  router.post(route('sejarah.update'), formData, {
    onSuccess: () => {
      loadAllData();
      alert('Sejarah berhasil disimpan!');
    },
  });
};

// Testimoni Functions
const loadTestimoni = async () => {
  try {
    const response = await fetch(route('testimoni.all'));
    testimonis.value = await response.json();
  } catch (error) {
    console.error('Error loading testimoni:', error);
  }
};

const approveTestimoni = async (id) => {
  if (!confirm('Setujui testimoni ini?')) return;
  
  try {
    await router.post(route('testimoni.approve', id), {}, {
      onSuccess: () => {
        loadTestimoni();
        alert('Testimoni berhasil disetujui!');
      },
    });
  } catch (error) {
    console.error('Error approving testimoni:', error);
  }
};

const rejectTestimoni = async (id) => {
  if (!confirm('Tolak testimoni ini?')) return;
  
  try {
    await router.post(route('testimoni.reject', id), {}, {
      onSuccess: () => {
        loadTestimoni();
        alert('Testimoni ditolak.');
      },
    });
  } catch (error) {
    console.error('Error rejecting testimoni:', error);
  }
};

const deleteTestimoni = async (id) => {
  if (!confirm('Hapus testimoni ini? Tindakan ini tidak dapat dibatalkan.')) return;
  
  try {
    await router.delete(route('testimoni.destroy', id), {
      onSuccess: () => {
        loadTestimoni();
        alert('Testimoni berhasil dihapus!');
      },
    });
  } catch (error) {
    console.error('Error deleting testimoni:', error);
  }
};
</script>
