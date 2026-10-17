<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card } from '@/components/ui/card'

const form = useForm({
  name: '',
  email: '',
  phone: '',
  password: ''
})

const submit = () => {
  form.post('/admin/customers')
}
</script>

<template>
  <Head title="Tambah Pelanggan Baru - Admin" />

  <AdminLayout>
    <template #header-title>
      <div class="flex items-center gap-xl hidden md:flex">
        <h2 class="font-headline-md text-headline-md text-primary font-bold">Tambah Pelanggan Baru</h2>
      </div>
    </template>

    <div class="pt-8 px-lg pb-xl max-w-7xl mx-auto relative min-h-[calc(100vh-80px)]">
      
      <!-- Dashboard Header Section -->
      <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-lg">
        <div>
          <nav class="flex items-center gap-xs text-label-sm font-label-sm text-on-surface-variant mb-xs">
            <Link href="/admin/customers" class="hover:text-primary transition-colors">Pelanggan</Link>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Tambah Pelanggan Baru</span>
          </nav>
          <h1 class="font-display-lg text-display-lg text-on-surface font-bold">Buat Profil</h1>
        </div>
      </div>

      <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
        <!-- Left Column (Personal Info & Security) -->
        <div class="lg:col-span-2 space-y-gutter">
          <!-- Photo Upload Card (Coming Soon) -->
          <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none p-md opacity-50 pointer-events-none">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Foto Profil (Segera Hadir)</h3>
            <p class="font-body-md text-body-md text-on-surface-variant mb-md">Unggah foto profil untuk pelanggan ini.</p>
            <div class="flex items-center gap-md">
              <div class="w-24 h-24 rounded-full bg-surface-container-high border-2 border-dashed border-outline-variant flex items-center justify-center overflow-hidden">
                <span class="material-symbols-outlined text-4xl text-on-surface-variant">person</span>
              </div>
              <div>
                <Button variant="outline" class="border-outline-variant text-primary font-label-md relative overflow-hidden">
                  <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">upload</span>
                    Unggah Foto
                  </span>
                </Button>
              </div>
            </div>
          </Card>
          
          <!-- Personal Information Card -->
          <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none p-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Informasi Pribadi</h3>
            <p class="font-body-md text-body-md text-on-surface-variant mb-md">Masukkan detail dasar untuk profil pelanggan baru.</p>
            
            <div class="space-y-md">
              <div>
                <label class="block font-label-md text-label-md text-on-surface mb-xs" for="fullName">Nama Lengkap</label>
                <Input v-model="form.name" class="w-full border-surface-variant bg-surface-container-low/50 text-on-surface" id="fullName" placeholder="Misalnya: Budi Santoso" type="text" required/>
                <div v-if="form.errors.name" class="text-error text-sm mt-1">{{ form.errors.name }}</div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <div>
                  <label class="block font-label-md text-label-md text-on-surface mb-xs" for="email">Alamat Email</label>
                  <Input v-model="form.email" class="w-full border-surface-variant bg-surface-container-low/50 text-on-surface" id="email" placeholder="budi@example.com" type="email" required/>
                  <div v-if="form.errors.email" class="text-error text-sm mt-1">{{ form.errors.email }}</div>
                </div>
                <div>
                  <label class="block font-label-md text-label-md text-on-surface mb-xs" for="phone">Nomor Telepon</label>
                  <Input v-model="form.phone" class="w-full border-surface-variant bg-surface-container-low/50 text-on-surface" id="phone" placeholder="08123456789" type="tel"/>
                  <div v-if="form.errors.phone" class="text-error text-sm mt-1">{{ form.errors.phone }}</div>
                </div>
              </div>
            </div>
          </Card>
          
          <!-- Security Card -->
          <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none p-md">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Keamanan</h3>
            <p class="font-body-md text-body-md text-on-surface-variant mb-md">Tetapkan kata sandi awal untuk akun.</p>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-xs" for="password">Kata Sandi</label>
              <Input v-model="form.password" class="w-full border-surface-variant bg-surface-container-low/50 text-on-surface" id="password" placeholder="••••••••" type="password" required/>
              <p class="font-label-sm text-label-sm text-on-surface-variant mt-2">Kata sandi harus minimal 8 karakter.</p>
              <div v-if="form.errors.password" class="text-error text-sm mt-1">{{ form.errors.password }}</div>
            </div>
          </Card>
        </div>
        
        <!-- Right Column (Status & Loyalty) -->
        <div class="space-y-gutter">
          <!-- Customer Status Card -->
          <Card class="bg-surface-container-lowest rounded-xl border-0 shadow-[0px_4px_20px_rgba(0,0,0,0.04)] p-md opacity-50 pointer-events-none">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-md">Status Pelanggan</h3>
            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-xs" for="status">Status Akun</label>
              <div class="relative">
                <select class="w-full bg-transparent border border-surface-variant rounded-md py-2 pl-3 pr-10 font-body-md text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary appearance-none" id="status">
                  <option value="active">Aktif</option>
                  <option value="disabled">Dinonaktifkan</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-on-surface-variant">
                  <span class="material-symbols-outlined text-sm">expand_more</span>
                </div>
              </div>
            </div>
          </Card>
        </div>
        
        <!-- Action Buttons -->
        <div class="lg:col-span-3 flex justify-end gap-md mt-lg pt-md border-t border-surface-container">
          <Link href="/admin/customers">
            <Button type="button" variant="outline" class="border-outline-variant text-on-surface-variant font-label-md h-10 px-6 hover:bg-surface-container-high transition-colors">
              Batal
            </Button>
          </Link>
          <Button type="submit" :disabled="form.processing" class="bg-primary text-white font-label-md h-10 px-6 hover:bg-primary/90 transition-colors shadow-sm disabled:opacity-50">
            Tambah Pelanggan
          </Button>
        </div>
      </form>

    </div>
  </AdminLayout>
</template>
