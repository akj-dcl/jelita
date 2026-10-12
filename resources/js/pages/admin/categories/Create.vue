<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const props = defineProps<{
  parentCategories: any[];
}>()

const form = useForm({
  name: '',
  slug: '',
  parent_id: '',
  description: '',
  status: 'active',
  image: null as File | null
})

const generateSlug = () => {
  form.slug = form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '')
}

const submit = () => {
  form.post('/admin/categories')
}
</script>

<template>
  <Head title="Tambah Kategori Baru - Admin" />

  <AdminLayout>
    <template #header-title>
      <div class="flex items-center gap-xl hidden md:flex">
        <h2 class="font-headline-md text-headline-md text-primary font-bold">Manajemen Kategori</h2>
        <nav class="flex gap-lg">
          <Link class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all" href="/admin/dashboard">Ringkasan</Link>
          <a class="font-label-md text-label-md text-primary border-b-2 border-primary pb-xs transition-all" href="/admin/categories">Kategori</a>
        </nav>
      </div>
    </template>
    
    <div class="pt-8 px-lg mb-lg max-w-[1440px] mx-auto w-full">
      <div class="flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
          <nav class="flex items-center gap-xs text-label-sm font-label-sm text-on-surface-variant mb-xs">
            <Link href="/admin/dashboard" class="hover:text-primary transition-colors">Dasbor</Link>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <Link href="/admin/categories" class="hover:text-primary transition-colors">Kategori</Link>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Tambah Baru</span>
          </nav>
          <h1 class="font-display-lg text-display-lg text-on-surface font-bold">Tambah Kategori Baru</h1>
        </div>
      </div>
    </div>

    <!-- Content Area -->
    <div class="px-lg pb-[120px] max-w-[1440px] mx-auto w-full flex-1">
      <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-gutter items-start">
        <!-- Left Column (Main Details) -->
        <div class="lg:col-span-2 space-y-gutter">
          <!-- General Information -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-md">Informasi Umum</h3>
            <div class="space-y-md">
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="name">Nama Kategori</label>
                <input v-model="form.name" @input="generateSlug" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-black dark:text-white font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary" id="name" placeholder="Misalnya: Tas Rajut" type="text" required/>
                <div v-if="form.errors.name" class="text-error text-sm mt-1">{{ form.errors.name }}</div>
              </div>
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs flex items-center" for="slug">
                  Slug
                  <span class="material-symbols-outlined text-[14px] ml-xs text-secondary cursor-help" title="Versi nama yang ramah URL. Biasanya huruf kecil semua dan hanya berisi huruf, angka, dan tanda hubung.">help</span>
                </label>
                <input v-model="form.slug" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-black dark:text-white font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary" id="slug" placeholder="Misalnya: tas-rajut" type="text" required/>
                <div v-if="form.errors.slug" class="text-error text-sm mt-1">{{ form.errors.slug }}</div>
              </div>
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="parent_id">Kategori Induk (Opsional)</label>
                <div class="relative">
                  <select v-model="form.parent_id" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 appearance-none text-black dark:text-white font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" id="parent_id">
                    <option value="">-- Tanpa Induk (Kategori Utama) --</option>
                    <option v-for="cat in props.parentCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                  <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-secondary">expand_more</span>
                </div>
                <div v-if="form.errors.parent_id" class="text-error text-sm mt-1">{{ form.errors.parent_id }}</div>
              </div>
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="description">Deskripsi</label>
                <div class="border border-[#E8E8E8] rounded-2xl overflow-hidden focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-colors">
                  <textarea v-model="form.description" class="w-full bg-surface-container-lowest px-4 py-3 text-black dark:text-white font-body-md text-body-md focus:outline-none placeholder-secondary border-none resize-y" id="description" placeholder="Deskripsikan kategori..." rows="5"></textarea>
                </div>
                <div v-if="form.errors.description" class="text-error text-sm mt-1">{{ form.errors.description }}</div>
              </div>
            </div>
          </section>

          <!-- Media -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Gambar Kategori</h3>
            <div class="border-2 border-dashed border-outline-variant rounded-2xl p-lg text-center flex flex-col items-center justify-center bg-surface-container-low/50 transition-colors relative cursor-pointer hover:bg-surface-container-low">
              <input type="file" accept="image/*" @change="e => form.image = (e.target as HTMLInputElement).files?.[0] || null" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
              <div class="w-16 h-16 rounded-full bg-secondary-container flex items-center justify-center text-primary mb-4">
                <span class="material-symbols-outlined" style="font-size: 32px;">cloud_upload</span>
              </div>
              <p class="font-body-md text-body-md text-on-surface mb-1">{{ form.image ? form.image.name : 'Klik atau seret & lepas gambar di sini' }}</p>
            </div>
            <div v-if="form.errors.image" class="text-error text-sm mt-1">{{ form.errors.image }}</div>
          </section>
        </div>

        <!-- Right Column (Sidebar Settings) -->
        <div class="lg:col-span-1 space-y-md">
          <!-- Status -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Status</h3>
            <div class="relative">
              <select v-model="form.status" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary appearance-none cursor-pointer pr-10">
                <option value="active">Aktif</option>
                <option value="draft">Draf</option>
              </select>
              <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-secondary pointer-events-none">expand_more</span>
            </div>
            <div v-if="form.errors.status" class="text-error text-sm mt-1">{{ form.errors.status }}</div>
            <p class="font-label-sm text-label-sm text-secondary mt-2">Kategori aktif akan terlihat di halaman toko.</p>
          </section>
        </div>
        
        <!-- Footer Action Bar -->
        <div class="fixed bottom-0 md:left-64 left-0 right-0 bg-surface-container-lowest/90 backdrop-blur-md border-t border-[#E8E8E8] py-4 px-margin-desktop z-40 flex justify-end items-center gap-sm shadow-[0_-4px_20px_rgba(0,0,0,0.02)]">
          <Link href="/admin/categories" class="px-6 py-3 rounded-full font-label-md text-label-md text-primary bg-transparent hover:bg-secondary-container/50 transition-colors">
            Batal
          </Link>
          <button type="submit" :disabled="form.processing" class="px-6 py-3 rounded-full font-label-md text-label-md text-on-primary bg-primary hover:bg-primary/90 shadow-sm transition-all hover:shadow-md transform hover:-translate-y-0.5 disabled:opacity-50">
            Simpan Kategori
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
