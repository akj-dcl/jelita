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
  categories: any;
  filters?: {
    search?: string;
  }
}>()

const searchQuery = ref(props.filters?.search || '');
let searchTimeout: any = null;

watch(searchQuery, (value) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/categories', { search: value }, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});
const getStatusClass = (status: boolean) => {
  if (status) return 'bg-secondary-container text-on-secondary-container'
  return 'bg-surface-container-high text-on-surface-variant'
}
</script>

<template>
  <Head title="Kategori - Admin" />

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

    <div class="pt-8 px-lg pb-xl max-w-[1440px] mx-auto relative">
      <!-- Dashboard Header Section -->
      <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-lg">
        <div>
          <nav class="flex items-center gap-xs text-label-sm font-label-sm text-on-surface-variant mb-xs">
            <Link href="/admin/dashboard" class="hover:text-primary transition-colors">Dasbor</Link>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Kategori</span>
          </nav>
          <h1 class="font-display-lg text-display-lg text-on-surface font-bold">Kategori</h1>
          <p class="text-on-surface-variant font-body-md text-body-md mt-xs">Kelompokkan produk Anda ke dalam koleksi yang bermakna.</p>
        </div>
        <Link href="/admin/categories/create" class="bg-primary text-white rounded-lg flex items-center gap-sm font-label-md text-label-md hover:bg-primary/90 transition-all shadow-md hover:shadow-lg h-12 px-6">
          <span class="material-symbols-outlined">add</span>
          Tambah Kategori Baru
        </Link>
      </div>

      <!-- Stats Bar -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-md mb-lg">
        <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 p-md rounded-xl soft-elevation flex flex-col gap-xs min-h-[120px] justify-center">
          <span class="text-on-surface-variant font-label-md text-label-md">Total Kategori</span>
          <span class="text-headline-md font-headline-md text-on-surface font-bold">{{ categories.total }}</span>
        </Card>
        <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 p-md rounded-xl soft-elevation flex flex-col gap-xs min-h-[120px] justify-center">
          <span class="text-on-surface-variant font-label-md text-label-md">Kategori Aktif</span>
          <span class="text-headline-md font-headline-md text-on-surface font-bold">-</span>
        </Card>
        <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 p-md rounded-xl soft-elevation flex flex-col gap-xs min-h-[120px] justify-center">
          <span class="text-on-surface-variant font-label-md text-label-md">Kategori Draf</span>
          <span class="text-headline-md font-headline-md text-on-surface font-bold">-</span>
        </Card>
      </div>

      <!-- Table Filters & Actions -->
      <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 rounded-xl soft-elevation overflow-hidden">
        <div class="p-md border-b border-surface-container flex flex-wrap items-center justify-between gap-md">
          <div class="flex items-center gap-md w-full md:w-auto">
            <div class="relative w-full md:w-80">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[20px]">search</span>
              <Input v-model="searchQuery" class="pl-10 pr-md py-sm rounded-lg border-outline-variant bg-surface-container-low/50 focus-visible:ring-1 focus-visible:ring-primary transition-colors text-label-md w-full h-10 text-on-surface" placeholder="Cari kategori..." type="text" />
            </div>
          </div>
          <div class="flex items-center gap-sm w-full md:w-auto">
            <span class="text-label-sm font-label-sm text-on-surface-variant font-bold">Aksi Massal:</span>
            <Select>
              <SelectTrigger class="w-[140px] h-10 border-outline-variant bg-surface-container-lowest text-on-surface">
                <SelectValue placeholder="Dipilih (0)" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="delete">Hapus</SelectItem>
                <SelectItem value="archive">Arsipkan</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <!-- Category Table -->
        <div class="overflow-x-auto">
          <Table>
            <TableHeader class="bg-surface-container-low/50">
              <TableRow class="hover:bg-transparent border-surface-container">
                <TableHead class="w-12 text-center">
                  <Checkbox class="border-outline-variant" />
                </TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Kategori</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Produk</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Status</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="category in categories.data" :key="category.id" class="hover:bg-surface-container-low/30 border-surface-container transition-colors group">
                <TableCell class="text-center">
                  <Checkbox class="border-outline-variant" />
                </TableCell>
                <TableCell>
                  <div class="flex items-center gap-md">
                    <div class="w-12 h-12 rounded bg-surface-container overflow-hidden flex-shrink-0 flex items-center justify-center">
                      <img v-if="category.image" :src="`/storage/${category.image}`" :alt="category.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                      <span v-else class="material-symbols-outlined text-on-surface-variant opacity-50">category</span>
                    </div>
                    <div>
                      <span class="font-bold text-on-surface block">
                        <span v-if="category.parent" class="text-secondary font-normal">{{ category.parent.name }} &raquo; </span>
                        {{ category.name }}
                      </span>
                      <span class="text-label-sm text-on-surface-variant opacity-70">{{ category.slug }}</span>
                    </div>
                  </div>
                </TableCell>
                <TableCell class="font-bold text-on-surface">{{ category.products_count || 0 }}</TableCell>
                <TableCell>
                  <span :class="['px-sm py-xs rounded-full font-label-sm font-bold uppercase tracking-wide text-[11px]', getStatusClass(category.is_active)]">
                    {{ category.is_active ? 'Aktif' : 'Draf' }}
                  </span>
                </TableCell>
                <TableCell class="text-right">
                  <div class="flex justify-end gap-xs">
                    <Link :href="`/admin/categories/${category.id}/edit`" class="text-on-surface-variant hover:text-primary hover:bg-surface-container h-8 w-8 rounded-lg flex items-center justify-center transition-colors">
                      <span class="material-symbols-outlined text-[20px]">edit</span>
                    </Link>
                    <Link :href="`/admin/categories/${category.id}`" method="delete" as="button" class="text-on-surface-variant hover:text-error hover:bg-error-container/20 h-8 w-8 rounded-lg flex items-center justify-center transition-colors">
                      <span class="material-symbols-outlined text-[20px]">delete</span>
                    </Link>
                  </div>
                </TableCell>
              </TableRow>
              
              <TableRow v-if="categories.data.length === 0">
                <TableCell colspan="5" class="text-center py-xl text-on-surface-variant">
                  Kategori tidak ditemukan.
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        
        <!-- Pagination -->
        <div class="p-md border-t border-surface-container flex items-center justify-between" v-if="categories.links && categories.links.length > 3">
          <p class="text-label-sm text-on-surface-variant">
            Menampilkan {{ categories.from }} hingga {{ categories.to }} dari {{ categories.total }}
          </p>
          <div class="flex items-center gap-xs">
            <template v-for="(link, index) in categories.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label.replace(/Previous|Next/g, '').trim()"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-label-sm font-bold transition-colors"
                :class="link.active ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container'"
              />
              <span
                v-else
                v-html="link.label.replace(/Previous|Next/g, '').trim()"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-label-sm font-bold text-on-surface-variant opacity-50 cursor-not-allowed"
              ></span>
            </template>
          </div>
        </div>
      </Card>
    </div>
  </AdminLayout>
</template>
