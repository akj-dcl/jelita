<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

const props = defineProps<{
  categories: Array<{ id: number, name: string }>
}>()

const form = useForm({
  name: '',
  slug: '',
  description: '',
  price: '',
  stock: 0,
  sku: '',
  category_id: '',
  status: 'active',
  images: [] as File[],
  video: null as File | null,
  variants: [] as Array<{
    name: string;
    sku: string;
    price: string;
    stock: number;
    weight: string;
    options: Record<string, string>;
  }>
})

const addVariant = () => {
  form.variants.push({ name: '', sku: '', price: '', stock: 0, weight: '', options: {} });
}

const removeVariant = (idx: number) => {
  form.variants.splice(idx, 1);
}

// Auto-generate slug from name
const generateSlug = () => {
  form.slug = form.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '')
}

const submit = () => {
  form.post('/admin/products')
}
</script>

<template>
  <Head title="Tambah Produk Baru - Admin" />

  <AdminLayout>
    <template #header-title>
      <div class="flex items-center gap-xl hidden md:flex">
        <h2 class="font-headline-md text-headline-md text-primary font-bold">Manajemen Produk</h2>
        <nav class="flex gap-lg">
          <Link class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all" href="/admin/dashboard">Ringkasan</Link>
          <a class="font-label-md text-label-md text-primary border-b-2 border-primary pb-xs transition-all" href="/admin/products">Produk</a>
        </nav>
      </div>
    </template>
    
    <div class="pt-8 px-lg mb-lg max-w-[1440px] mx-auto w-full">
      <div class="flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
          <nav class="flex items-center gap-xs text-label-sm font-label-sm text-on-surface-variant mb-xs">
            <Link href="/admin/dashboard" class="hover:text-primary transition-colors">Dasbor</Link>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <Link href="/admin/products" class="hover:text-primary transition-colors">Produk</Link>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Tambah Baru</span>
          </nav>
          <h1 class="font-display-lg text-display-lg text-on-surface font-bold">Tambah Produk Baru</h1>
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
            <div class="mb-sm">
              <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="product-title">Nama Produk</label>
              <input v-model="form.name" @input="generateSlug" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary" id="product-title" placeholder="Misalnya: Anyaman Rotan" type="text" required/>
              <div v-if="form.errors.name" class="text-error text-sm mt-1">{{ form.errors.name }}</div>
            </div>
            <div class="mb-sm">
              <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="product-slug">Slug</label>
              <input v-model="form.slug" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary" id="product-slug" placeholder="Misalnya: anyaman-rotan" type="text" required/>
              <div v-if="form.errors.slug" class="text-error text-sm mt-1">{{ form.errors.slug }}</div>
            </div>
            <div>
              <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="product-description">Deskripsi</label>
              <div class="border border-[#E8E8E8] rounded-2xl overflow-hidden focus-within:border-primary focus-within:ring-1 focus-within:ring-primary transition-colors">
                <textarea v-model="form.description" class="w-full bg-surface-container-lowest px-4 py-3 text-on-surface font-body-md text-body-md focus:outline-none placeholder-secondary border-none resize-y" id="product-description" placeholder="Jelaskan karya, bahan, dan detail..." rows="6"></textarea>
              </div>
              <div v-if="form.errors.description" class="text-error text-sm mt-1">{{ form.errors.description }}</div>
            </div>
          </section>

          <!-- Media -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Media Gambar</h3>
            <div class="border-2 border-dashed border-outline-variant rounded-2xl p-lg text-center flex flex-col items-center justify-center bg-surface-container-low/50 transition-colors relative cursor-pointer hover:bg-surface-container-low">
              <input type="file" multiple accept="image/*" @change="e => form.images = Array.from((e.target as HTMLInputElement).files || [])" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
              <div class="w-16 h-16 rounded-full bg-secondary-container flex items-center justify-center text-primary mb-4">
                <span class="material-symbols-outlined" style="font-size: 32px;">cloud_upload</span>
              </div>
              <p class="font-body-md text-body-md text-on-surface mb-1">
                {{ form.images.length > 0 ? form.images.length + ' gambar dipilih' : 'Klik atau Tarik & lepas gambar di sini' }}
              </p>
            </div>
            <div v-if="form.errors.images" class="text-error text-sm mt-1">{{ form.errors.images }}</div>
            
            <h3 class="font-headline-md text-headline-md text-on-surface mt-6 mb-sm">Media Video (Opsional)</h3>
            <div class="border-2 border-dashed border-outline-variant rounded-2xl p-lg text-center flex flex-col items-center justify-center bg-surface-container-low/50 transition-colors relative cursor-pointer hover:bg-surface-container-low">
              <input type="file" accept="video/mp4,video/quicktime" @change="e => form.video = (e.target as HTMLInputElement).files?.[0] || null" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
              <div class="w-16 h-16 rounded-full bg-secondary-container flex items-center justify-center text-primary mb-4">
                <span class="material-symbols-outlined" style="font-size: 32px;">movie</span>
              </div>
              <p class="font-body-md text-body-md text-on-surface mb-1">
                {{ form.video ? form.video.name : 'Pilih file video (MP4/MOV)' }}
              </p>
            </div>
            <div v-if="form.errors.video" class="text-error text-sm mt-1">{{ form.errors.video }}</div>
          </section>

          <!-- Variants Section -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <div class="flex justify-between items-center mb-sm">
              <h3 class="font-headline-md text-headline-md text-on-surface">Varian Produk</h3>
              <button type="button" @click="addVariant" class="flex items-center gap-1 text-primary font-label-md hover:bg-primary/5 px-3 py-1.5 rounded-xl transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Varian
              </button>
            </div>
            <p class="text-body-md text-on-surface-variant mb-md">Opsional. Gunakan untuk produk dengan variasi berat, warna, aroma, rasa, dll.</p>
            
            <div v-if="form.variants.length === 0" class="text-center py-8 text-on-surface-variant border-2 border-dashed border-outline-variant rounded-2xl">
              <span class="material-symbols-outlined text-3xl mb-2 block opacity-50">tune</span>
              <p class="text-sm">Belum ada varian. Klik "Tambah Varian" untuk menambahkan.</p>
            </div>
            
            <div v-else class="space-y-md">
              <div v-for="(variant, idx) in form.variants" :key="idx" 
                   class="border border-outline-variant/50 rounded-2xl p-4 relative bg-surface-container-low/30">
                <button type="button" @click="removeVariant(idx)" 
                        class="absolute top-3 right-3 text-error hover:bg-error/10 p-1 rounded-lg transition-colors cursor-pointer">
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1">Nama Varian *</label>
                    <input v-model="variant.name" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Contoh: Lavender 100g" required />
                    <p class="text-[10px] text-secondary mt-1">Gunakan gabungan spesifikasi (Contoh: Vanilla - Kecil - Merah Muda).</p>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1">SKU</label>
                    <input v-model="variant.sku" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Contoh: LAT-LAV-100" />
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1">Harga *</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary text-xs">Rp</span>
                      <input v-model="variant.price" type="number" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-xl px-3 py-2 pl-9 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="0" required />
                    </div>
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1">Stok</label>
                    <input v-model="variant.stock" type="number" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="0" />
                  </div>
                  <div>
                    <label class="block text-xs font-semibold text-on-surface-variant mb-1">Berat (gram)</label>
                    <input v-model="variant.weight" type="number" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="0" />
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Right Column (Sidebar Settings) -->
        <div class="lg:col-span-1 space-y-md">
          <!-- Status -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Status</h3>
            <div class="relative">
              <select v-model="form.status" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary appearance-none cursor-pointer pr-10">
                <option value="draft">Draf</option>
                <option value="active">Aktif</option>
              </select>
              <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-secondary pointer-events-none">expand_more</span>
            </div>
            <div v-if="form.errors.status" class="text-error text-sm mt-1">{{ form.errors.status }}</div>
          </section>

          <!-- Pricing -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Harga</h3>
            <div class="space-y-sm">
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="price">Harga</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-secondary font-body-md">Rp</span>
                  <input v-model="form.price" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary pl-12" id="price" placeholder="0" type="number" required/>
                </div>
                <p class="text-xs text-on-surface-variant mt-2 leading-relaxed">Harga awal/dasar. Jika produk memiliki varian ukuran/warna, harga varian di bawah akan menggantikan harga ini.</p>
                <div v-if="form.errors.price" class="text-error text-sm mt-1">{{ form.errors.price }}</div>
              </div>
            </div>
          </section>

          <!-- Inventory -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Inventaris</h3>
            <div class="space-y-sm">
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="sku">SKU</label>
                <input v-model="form.sku" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary" id="sku" placeholder="Misalnya: ART-RAT-001" type="text"/>
                <div v-if="form.errors.sku" class="text-error text-sm mt-1">{{ form.errors.sku }}</div>
              </div>
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="stock">Jumlah Stok</label>
                <input v-model="form.stock" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary" id="stock" placeholder="0" type="number" required/>
                <div v-if="form.errors.stock" class="text-error text-sm mt-1">{{ form.errors.stock }}</div>
              </div>
            </div>
          </section>

          <!-- Organization -->
          <section class="bg-surface-container-lowest rounded-2xl p-md shadow-[0_4px_20px_rgba(0,0,0,0.04)] mb-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Organisasi</h3>
            <div class="space-y-sm">
              <div>
                <label class="block text-on-surface-variant font-label-md text-label-md mb-xs" for="category">Kategori</label>
                <div class="relative">
                  <select v-model="form.category_id" class="w-full bg-surface-container-lowest border border-[#E8E8E8] rounded-2xl px-4 py-3 text-on-surface font-body-md text-body-md transition-colors focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary placeholder-secondary appearance-none cursor-pointer pr-10" id="category" required>
                    <option value="" disabled>Pilih kategori...</option>
                    <template v-for="cat in categories" :key="cat.id">
                      <option v-if="!cat.children || cat.children.length === 0" :value="cat.id">{{ cat.name }}</option>
                      <optgroup v-else :label="cat.name">
                        <option :value="cat.id">{{ cat.name }} (Utama)</option>
                        <option v-for="child in cat.children" :key="child.id" :value="child.id">{{ child.name }}</option>
                      </optgroup>
                    </template>
                  </select>
                  <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-secondary pointer-events-none">expand_more</span>
                </div>
                <div v-if="form.errors.category_id" class="text-error text-sm mt-1">{{ form.errors.category_id }}</div>
              </div>
            </div>
          </section>
        </div>
        
        <!-- Footer Action Bar -->
        <div class="fixed bottom-0 md:left-64 left-0 right-0 bg-surface-container-lowest/90 backdrop-blur-md border-t border-[#E8E8E8] py-4 px-margin-desktop z-40 flex justify-end items-center gap-sm shadow-[0_-4px_20px_rgba(0,0,0,0.02)]">
          <Link href="/admin/products" class="px-6 py-3 rounded-full font-label-md text-label-md text-primary bg-transparent hover:bg-secondary-container/50 transition-colors">
            Batal
          </Link>
          <button type="submit" :disabled="form.processing" class="px-6 py-3 rounded-full font-label-md text-label-md text-on-primary bg-primary hover:bg-primary/90 shadow-sm transition-all hover:shadow-md transform hover:-translate-y-0.5 disabled:opacity-50">
            Simpan Produk
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
