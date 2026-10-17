<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import {
  SidebarProvider,
  Sidebar,
  SidebarContent,
  SidebarHeader,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarMenu,
  SidebarMenuItem,
  SidebarMenuButton,
  SidebarInset,
  SidebarTrigger,
} from '@/components/ui/sidebar'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import EditProfile from '@/components/profile/EditProfile.vue'

const page = usePage()
const user = computed(() => (page.props.auth as any)?.user)

const navigation = [
  { name: 'Dasbor', href: '/admin/dashboard', icon: 'dashboard' },
  { name: 'Produk', href: '/admin/products', icon: 'inventory_2' },
  { name: 'Kategori', href: '/admin/categories', icon: 'category' },
  { name: 'Pesanan', href: '/admin/orders', icon: 'shopping_cart' },
  { name: 'Pelanggan', href: '/admin/customers', icon: 'group' },
]

const isActive = (href: string) => page.url.startsWith(href)
</script>

<template>
  <SidebarProvider>
    <Sidebar variant="inset" class="bg-surface-container-low dark:bg-surface-container-low border-r-0 shadow-sm z-50">
      <SidebarHeader class="px-md py-md mb-xs">
        <h1 class="font-headline-md text-headline-md text-primary font-bold">Jelita Saung</h1>
        <p class="text-on-surface-variant font-label-md text-label-md opacity-70">Manajemen Admin</p>
      </SidebarHeader>

      <SidebarContent>
        <SidebarGroup>
          <SidebarGroupContent>
            <SidebarMenu class="space-y-xs px-sm">
              <SidebarMenuItem v-for="item in navigation" :key="item.name">
                <SidebarMenuButton 
                  asChild 
                  :isActive="isActive(item.href)" 
                  :class="isActive(item.href) ? 'text-primary font-bold bg-secondary-container/30 border-r-4 border-primary rounded-xl' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-xl'"
                  class="h-12 px-md transition-colors duration-200 ease-in-out"
                >
                  <Link :href="item.href" class="flex items-center gap-md w-full">
                    <span class="material-symbols-outlined">{{ item.icon }}</span>
                    <span class="font-label-md text-label-md">{{ item.name }}</span>
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>

      <SidebarFooter class="px-md pb-md mt-auto">
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <button class="flex items-center gap-sm w-full text-left p-2 rounded-xl hover:bg-surface-container-low transition-colors">
              <div class="w-10 h-10 rounded-full bg-primary-fixed overflow-hidden shrink-0 flex items-center justify-center text-on-primary-fixed font-bold text-lg">
                {{ user?.name?.substring(0, 2).toUpperCase() || 'AD' }}
              </div>
              <div class="overflow-hidden flex-1">
                <p class="font-label-md text-label-md font-bold text-on-surface truncate">{{ user?.name || 'Admin' }}</p>
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wider">Pemilik</p>
              </div>
              <span class="material-symbols-outlined text-on-surface-variant text-[20px]">more_vert</span>
            </button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" class="w-56 bg-surface-container-lowest border-outline-variant shadow-lg rounded-xl">
            <DropdownMenuLabel class="text-on-surface font-bold">Akun Saya</DropdownMenuLabel>
            <DropdownMenuSeparator class="bg-surface-container" />
            <Dialog>
              <DialogTrigger as-child>
                <DropdownMenuItem @select.prevent class="cursor-pointer text-on-surface-variant hover:text-primary hover:bg-surface-container-low focus:bg-surface-container-low focus:text-primary transition-colors">
                  <span class="material-symbols-outlined mr-2 text-[20px]">person</span>
                  Edit Profil
                </DropdownMenuItem>
              </DialogTrigger>
              <DialogContent class="sm:max-w-[700px] max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                  <DialogTitle>Edit Profil Admin</DialogTitle>
                </DialogHeader>
                <div class="mt-4">
                  <EditProfile :authUser="user" fallbackAvatar="/images/avatar-fallback.png" />
                </div>
              </DialogContent>
            </Dialog>
            <DropdownMenuItem @click="router.post('/logout')" class="cursor-pointer text-error hover:bg-error-container focus:bg-error-container hover:text-on-error-container focus:text-on-error-container transition-colors">
              <span class="material-symbols-outlined mr-2 text-[20px]">logout</span>
              Keluar
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarFooter>
    </Sidebar>

    <SidebarInset class="bg-background min-h-screen">
      <!-- TopNavBar Integration -->
      <header class="sticky top-0 z-40 bg-surface/80 backdrop-blur-md shadow-sm h-16 px-lg flex justify-between items-center w-full">
        <div class="flex items-center gap-4">
          <SidebarTrigger class="text-on-surface-variant" />
          <slot name="header-title">
            <!-- Default title if slot is not provided -->
            <div class="flex items-center gap-xl hidden md:flex">
              <h2 class="font-headline-md text-headline-md text-primary font-bold">Admin</h2>
            </div>
          </slot>
        </div>
        
        <div class="flex items-center gap-md">
          <div class="relative hidden sm:block">
            <Input 
              class="bg-surface border-outline-variant rounded-full py-xs px-lg pl-10 text-label-md focus-visible:ring-1 focus-visible:ring-primary transition-colors w-64" 
              placeholder="Cari pesanan atau produk..." 
              type="text" 
            />
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-tertiary-container text-[20px]" style="font-variation-settings: 'wght' 300;">search</span>
          </div>
          <Button variant="ghost" size="icon" class="text-on-surface-variant hover:text-primary rounded-full">
            <span class="material-symbols-outlined">notifications</span>
          </Button>
          <Button variant="ghost" size="icon" class="text-on-surface-variant hover:text-primary rounded-full hidden sm:inline-flex">
            <span class="material-symbols-outlined">help_outline</span>
          </Button>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 relative">
        <slot />
      </main>
    </SidebarInset>
  </SidebarProvider>
</template>
