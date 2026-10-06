<script setup lang="ts">
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Input } from '@/components/ui/input'

const props = defineProps<{
  customers: any;
  stats: any;
  filters?: {
    search?: string;
  }
}>()

const searchQuery = ref(props.filters?.search || '');
let searchTimeout: any = null;

watch(searchQuery, (value) => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/admin/customers', { search: value }, { preserveState: true, preserveScroll: true, replace: true });
    }, 300);
});
</script>

<template>
  <Head title="Pelanggan - Admin" />

  <AdminLayout>
    <template #header-title>
      <div class="flex items-center gap-xl hidden md:flex">
        <h2 class="font-headline-md text-headline-md text-primary font-bold">Pelanggan</h2>
        <nav class="flex gap-lg">
          <a class="font-label-md text-label-md text-primary border-b-2 border-primary pb-xs transition-all duration-200" href="#">Ringkasan</a>
        </nav>
      </div>
    </template>

    <div class="pt-8 px-lg pb-xl max-w-[1440px] mx-auto relative min-h-screen">
      
      <!-- Dashboard Header Section -->
      <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-lg">
        <div>
          <nav class="flex items-center gap-xs text-label-sm font-label-sm text-on-surface-variant mb-xs">
            <Link href="/admin/dashboard" class="hover:text-primary transition-colors">Dasbor</Link>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Pelanggan</span>
          </nav>
          <p class="font-label-md text-secondary-fixed-dim uppercase tracking-widest mb-xs mt-2">Basis Data CRM</p>
          <h1 class="font-display-lg text-display-lg text-on-surface font-bold">Anggota Komunitas</h1>
        </div>
        
        <div class="flex gap-sm">
          <Link href="/admin/customers/create">
            <Button class="bg-primary text-white font-label-md hover:bg-primary-container transition-all flex items-center gap-2 shadow-sm h-10">
              <span class="material-symbols-outlined text-[18px]">person_add</span>
              Daftar Pelanggan
            </Button>
          </Link>
        </div>
      </div>

      <!-- Dashboard Stats Bento -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter mb-lg">
        <Card class="bg-surface-container-lowest p-md rounded-xl border-0 shadow-[0px_4px_20px_rgba(0,0,0,0.04)] flex flex-row items-center justify-between !gap-0">
          <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-tight">Total Terdaftar</p>
            <h4 class="text-headline-md font-bold text-on-surface mt-1">{{ stats?.total || 0 }}</h4>
          </div>
          <div class="w-12 h-12 rounded-full bg-primary-fixed/30 flex items-center justify-center text-primary shrink-0">
            <span class="material-symbols-outlined text-[24px]">group</span>
          </div>
        </Card>
        
        <Card class="bg-surface-container-lowest p-md rounded-xl border-0 shadow-[0px_4px_20px_rgba(0,0,0,0.04)] flex flex-row items-center justify-between !gap-0">
          <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-tight">Terverifikasi</p>
            <h4 class="text-headline-md font-bold text-on-surface mt-1">{{ stats?.verified || 0 }}</h4>
          </div>
          <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-700 shrink-0">
            <span class="material-symbols-outlined text-[24px]">verified</span>
          </div>
        </Card>
        
        <Card class="bg-surface-container-lowest p-md rounded-xl border-0 shadow-[0px_4px_20px_rgba(0,0,0,0.04)] flex flex-row items-center justify-between !gap-0">
          <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-tight">Belum Verifikasi</p>
            <h4 class="text-headline-md font-bold text-on-surface mt-1">{{ stats?.unverified || 0 }}</h4>
          </div>
          <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
            <span class="material-symbols-outlined text-[24px]">pending</span>
          </div>
        </Card>
        
        <Card class="bg-surface-container-lowest p-md rounded-xl border-0 shadow-[0px_4px_20px_rgba(0,0,0,0.04)] flex flex-row items-center justify-between !gap-0">
          <div>
            <p class="text-label-sm text-on-surface-variant uppercase tracking-tight">Baru Bulan Ini</p>
            <h4 class="text-headline-md font-bold text-on-surface mt-1">{{ stats?.new_this_month || 0 }}</h4>
          </div>
          <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 shrink-0">
            <span class="material-symbols-outlined text-[24px]">person_add</span>
          </div>
        </Card>
      </div>

      <!-- Elegant Customer List Card -->
      <Card class="bg-white rounded-xl border-0 shadow-[0px_4px_20px_rgba(0,0,0,0.04)] overflow-hidden mb-lg">
        <div class="p-md border-b border-surface-container flex flex-wrap items-center justify-between gap-md">
          <div class="flex items-center gap-md w-full md:w-auto">
            <div class="relative w-full md:w-80">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-[20px]">search</span>
              <Input v-model="searchQuery" class="pl-10 pr-md py-sm rounded-lg border-outline-variant bg-surface-container-lowest focus-visible:ring-1 focus-visible:ring-primary transition-colors text-label-md w-full h-10" placeholder="Cari pelanggan berdasarkan nama atau email..." type="text" />
            </div>
          </div>
        </div>
        
        <div class="overflow-x-auto">
          <Table>
            <TableHeader class="bg-surface-container-low/50">
              <TableRow class="hover:bg-transparent border-surface-container">
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Nama Pelanggan</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Alamat Email</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Telepon</TableHead>
                <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold text-right">Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="customer in customers.data" :key="customer.id" class="hover:bg-surface-container-low/30 border-surface-container transition-colors group">
                <TableCell class="py-md">
                  <div class="flex items-center gap-md">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold bg-primary-container/20 text-primary">
                      {{ customer.name.substring(0, 2).toUpperCase() }}
                    </div>
                    <div>
                      <p class="font-bold text-on-surface">{{ customer.name }}</p>
                      <p class="text-label-sm text-on-surface-variant">Anggota sejak {{ new Date(customer.created_at).toLocaleDateString() }}</p>
                    </div>
                  </div>
                </TableCell>
                <TableCell class="text-on-surface-variant font-medium py-md">{{ customer.email }}</TableCell>
                <TableCell class="text-on-surface-variant font-medium py-md">{{ customer.phone || '-' }}</TableCell>
                <TableCell class="text-right py-md">
                  <div class="flex justify-end gap-xs">
                    <Link :href="`/admin/customers/${customer.id}/edit`">
                      <Button variant="ghost" size="icon" class="text-on-surface-variant hover:text-primary hover:bg-surface-container h-8 w-8 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                      </Button>
                    </Link>
                    <Link :href="`/admin/customers/${customer.id}`" method="delete" as="button">
                      <Button variant="ghost" size="icon" class="text-on-surface-variant hover:text-error hover:bg-error-container h-8 w-8 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                      </Button>
                    </Link>
                  </div>
                </TableCell>
              </TableRow>
              
              <TableRow v-if="customers.data.length === 0">
                <TableCell colspan="4" class="text-center py-xl text-on-surface-variant">
                  Pelanggan tidak ditemukan.
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        
        <!-- Pagination -->
        <div class="p-md bg-surface-container-low/30 border-t border-surface-container flex flex-col sm:flex-row items-center justify-between gap-4" v-if="customers.links && customers.links.length > 3">
          <p class="text-sm text-on-surface-variant">
            Menampilkan {{ customers.from }} hingga {{ customers.to }} dari {{ customers.total }}
          </p>
          <div class="flex items-center gap-xs">
            <template v-for="(link, index) in customers.links" :key="index">
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
