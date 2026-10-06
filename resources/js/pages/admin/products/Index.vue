<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card } from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Checkbox } from '@/components/ui/checkbox'

const props = defineProps<{
  products: {
    data: Array<any>;
    links: Array<any>;
    current_page: number;
    from: number;
    to: number;
    total: number;
  },
  stats: {
    total: number;
    active: number;
    low_stock: number;
    draft: number;
  },
  filters?: {
    search?: string;
  }
}>()

const searchQuery = ref(props.filters?.search || '');
let searchTimeout: any = null;

watch(searchQuery, (value) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/products', { search: value }, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});

const getStatusClass = (status: string) => {
  if (status === 'Tersedia') return 'bg-secondary-container text-on-secondary-container'
  if (status === 'Stok Menipis') return 'bg-tertiary-fixed text-on-tertiary-fixed'
  if (status === 'Habis') return 'bg-error-container text-on-error-container'
  return 'bg-surface-container text-on-surface'
}

const getStockBarClass = (status: string) => {
  if (status === 'Stok Menipis') return 'bg-tertiary'
  if (status === 'Habis') return 'bg-error'
  return 'bg-primary'
}

const getStockTextClass = (status: string) => {
  if (status === 'Stok Menipis') return 'text-tertiary'
  if (status === 'Habis') return 'text-error'
  return 'text-on-surface-variant'
}

const showToast = ref(false)

const handleAddProduct = () => {
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
  }, 3000)
}
</script>

<template>
  <Head title="Produk - Admin" />

  <AdminLayout>
    <template #header-title>
      <div class="flex items-center gap-xl hidden md:flex">
        <h2 class="font-headline-md text-headline-md text-primary font-bold">Manajemen Produk</h2>
        <nav class="flex gap-lg">
          <Link class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all" href="/admin/dashboard">Ringkasan</Link>
          <a class="font-label-md text-label-md text-primary border-b-2 border-primary pb-xs transition-all" href="#">Produk</a>
        </nav>
      </div>
    </template>

    <div class="pt-8 px-lg pb-xl max-w-[1440px] mx-auto relative">
      <!-- Dashboard Header Section -->
      <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-lg">
        <div>
          <nav class="flex items-center gap-xs text-label-sm font-label-sm text-on-surface-variant mb-xs">
            <span>Dasbor</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Produk</span>
          </nav>
          <h1 class="font-display-lg text-display-lg text-on-surface font-bold">Inventaris Produk</h1>
          <p class="text-on-surface-variant font-body-md text-body-md mt-xs">Kelola koleksi, tingkat stok, dan visibilitas toko Anda.</p>
        </div>
        <Link href="/admin/products/create" class="bg-primary text-white rounded-lg flex items-center gap-sm font-label-md text-label-md hover:bg-primary/90 transition-all shadow-md hover:shadow-lg h-12 px-6">
          <span class="material-symbols-outlined">add</span>
          Tambah Produk Baru
        </Link>
      </div>

      <!-- Stats Bar -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-md mb-lg">
        <Card class="bg-white p-md rounded-xl border-0 soft-elevation flex flex-col gap-xs min-h-[120px] justify-center">
          <span class="text-on-surface-variant font-label-md text-label-md">Total Produk</span>
          <span class="text-headline-md font-headline-md text-on-surface font-bold">{{ stats?.total || 0 }}</span>
        </Card>
        <Card class="bg-white p-md rounded-xl border-0 soft-elevation flex flex-col gap-xs min-h-[120px] justify-center">
          <span class="text-on-surface-variant font-label-md text-label-md">Daftar Aktif</span>
          <span class="text-headline-md font-headline-md text-on-surface font-bold">{{ stats?.active || 0 }}</span>
        </Card>
        <Card class="bg-white p-md rounded-xl border-0 soft-elevation border-l-4 border-l-error/40 flex flex-col gap-xs min-h-[120px] justify-center">
          <span class="text-on-surface-variant font-label-md text-label-md">Peringatan Stok Rendah</span>
          <span class="text-headline-md font-headline-md text-error font-bold">{{ stats?.low_stock || 0 }}</span>
          <span class="text-error font-label-sm text-label-sm font-bold">Perlu perhatian</span>
        </Card>
        <Card class="bg-white p-md rounded-xl border-0 soft-elevation flex flex-col gap-xs min-h-[120px] justify-center">
          <span class="text-on-surface-variant font-label-md text-label-md">Daftar Draf</span>
          <span class="text-headline-md font-headline-md text-on-surface font-bold">{{ stats?.draft || 0 }}</span>
        </Card>
      </div>

      <!-- Table Filters & Actions -->
      <Card class="bg-white rounded-xl border-0 soft-elevation overflow-hidden">
        <div class="p-md border-b border-surface-container flex flex-wrap items-center justify-between gap-md">
          <div class="flex items-center gap-md w-full md:w-auto">
            <div class="relative w-full md:w-80">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[20px]">search</span>
              <Input v-model="searchQuery" class="pl-10 pr-md py-sm rounded-lg border-outline-variant bg-surface-container-lowest focus-visible:ring-1 focus-visible:ring-primary transition-colors text-label-md w-full h-10" placeholder="Filter berdasarkan nama produk atau SKU..." type="text" />
            </div>
            <Button variant="outline" class="flex items-center gap-sm h-10 border-outline-variant text-on-surface-variant hover:text-on-surface">
              <span class="material-symbols-outlined text-[20px]">filter_list</span>
              Filter
            </Button>
          </div>
          <div class="flex items-center gap-sm w-full md:w-auto">
            <span class="text-label-sm font-label-sm text-on-surface-variant font-bold">Aksi Massal:</span>
            <Select>
              <SelectTrigger class="w-[140px] h-10 border-outline-variant bg-white">
                <SelectValue placeholder="Terpilih (0)" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="export">Ekspor CSV</SelectItem>
                <SelectItem value="duplicate">Duplikat</SelectItem>
                <SelectItem value="delete">Hapus</SelectItem>
                <SelectItem value="archive">Arsipkan</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <!-- Product Table -->
        <div class="overflow-x-auto">
          <Table>
            <TableHeader class="bg-surface-container-low/50">
              <TableRow class="hover:bg-transparent border-surface-container">
                <TableHead class="w-12 text-center">
                  <Checkbox class="border-outline-variant" />
                </TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Produk</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Kategori</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Harga</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold text-center">Tingkat Stok</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Status</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="product in products.data" :key="product.id" class="hover:bg-surface-container-low/30 border-surface-container transition-colors group">
                <TableCell class="text-center">
                  <Checkbox class="border-outline-variant" />
                </TableCell>
                <TableCell>
                  <div class="flex items-center gap-md">
                    <div class="w-12 h-12 rounded bg-surface-container overflow-hidden flex-shrink-0">
                      <img :src="product.image ? `/storage/${product.image}` : 'https://placehold.co/48x48/e2e8f0/64748b?text=No+Img'" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                    </div>
                    <div>
                      <span class="font-bold text-on-surface block">{{ product.name }}</span>
                      <span class="text-label-sm text-on-surface-variant opacity-70 uppercase tracking-wider">{{ product.sku }}</span>
                    </div>
                  </div>
                </TableCell>
                <TableCell class="text-on-surface-variant font-medium">{{ product.category?.name || 'Tanpa Kategori' }}</TableCell>
                <TableCell class="font-bold text-primary">Rp {{ Number(product.price).toLocaleString('id-ID') }}</TableCell>
                <TableCell class="text-center">
                  <div class="w-full bg-surface-container h-1.5 rounded-full overflow-hidden max-w-[100px] mx-auto">
                    <div :class="['h-full transition-all duration-500', getStockBarClass(product.stock > 0 ? (product.stock > 10 ? 'Tersedia' : 'Stok Menipis') : 'Habis')]" :style="`width: ${Math.min(product.stock, 100)}%`"></div>
                  </div>
                  <span :class="['text-label-sm mt-1 block font-bold', getStockTextClass(product.stock > 0 ? (product.stock > 10 ? 'Tersedia' : 'Stok Menipis') : 'Habis')]">{{ product.stock }} unit</span>
                </TableCell>
                <TableCell>
                  <span :class="['px-sm py-xs rounded-full font-label-sm font-bold uppercase tracking-wide text-[11px]', getStatusClass(product.is_published ? (product.stock > 0 ? (product.stock > 10 ? 'Tersedia' : 'Stok Menipis') : 'Habis') : 'Draf')]">
                    {{ product.is_published ? (product.stock > 0 ? (product.stock > 10 ? 'Tersedia' : 'Stok Menipis') : 'Habis') : 'Draf' }}
                  </span>
                </TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-xs">
                    <Link :href="`/admin/products/${product.id}/edit`" class="text-on-surface-variant hover:text-primary hover:bg-surface-container h-8 w-8 rounded-lg flex items-center justify-center transition-colors">
                      <span class="material-symbols-outlined text-[20px]">edit</span>
                    </Link>
                    <Link :href="`/admin/products/${product.id}`" method="delete" as="button" class="text-on-surface-variant hover:text-error hover:bg-error-container/20 h-8 w-8 rounded-lg flex items-center justify-center transition-colors">
                      <span class="material-symbols-outlined text-[20px]">delete</span>
                    </Link>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Pagination -->
        <div class="p-md bg-surface-container-low/30 border-t border-surface-container flex flex-col sm:flex-row items-center justify-between gap-4">
          <span class="text-label-sm text-on-surface-variant font-medium">Menampilkan {{ products.from }} hingga {{ products.to }} dari {{ products.total }} produk</span>
          <div class="flex items-center gap-xs">
            <template v-for="(link, index) in products.links" :key="index">
              <Link 
                v-if="link.url"
                :href="link.url" 
                v-html="link.label.replace(/Previous|Next/g, '').trim()"
                :class="[
                  'w-10 h-10 flex items-center justify-center rounded-md font-bold text-sm transition-colors',
                  link.active ? 'bg-primary text-white hover:bg-primary/90' : 'border border-outline-variant text-on-surface-variant hover:text-on-surface hover:bg-surface-container'
                ]"
              />
              <span 
                v-else 
                v-html="link.label.replace(/Previous|Next/g, '').trim()" 
                class="w-10 h-10 flex items-center justify-center rounded-md border border-outline-variant text-on-surface-variant opacity-50 text-sm"
              ></span>
            </template>
          </div>
        </div>
      </Card>

      <!-- Toast Notification -->
      <div 
        class="fixed top-24 left-1/2 -translate-x-1/2 bg-white px-md py-sm rounded-full shadow-lg border border-primary/20 flex items-center gap-sm transition-all duration-500 z-50 pointer-events-none"
        :class="showToast ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-5'"
      >
        <span class="material-symbols-outlined text-primary" style="font-size: 20px;">check_circle</span>
        <span class="text-label-md font-bold text-on-surface">Inventaris produk berhasil diperbarui.</span>
      </div>

    </div>
  </AdminLayout>
</template>
