<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { ref, computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import { useAppearance } from '@/composables/useAppearance'

const props = defineProps<{
    stats: any;
    recentOrders: any[];
    topProducts: any[];
    mostViewedProducts: any[];
    recentReviews: any[];
    chart: any;
}>()

const { resolvedAppearance } = useAppearance()

const formatRupiah = (value: number) => {
    return 'Rp ' + Math.round(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const translateStatus = (status: string) => {
    const statusMap: Record<string, string> = {
        'pending_payment': 'Menunggu Pembayaran',
        'processing': 'Diproses',
        'shipped': 'Dikirim',
        'delivered': 'Diterima',
        'cancelled': 'Dibatalkan',
        'refunded': 'Dikembalikan',
    };
    return statusMap[status] || status;
};

const chartOptions = computed(() => {
  const isDark = resolvedAppearance.value === 'dark';
  return {
    chart: {
      type: 'area',
      toolbar: { show: false },
      fontFamily: 'Inter, sans-serif',
      background: 'transparent'
    },
    theme: {
      mode: isDark ? 'dark' : 'light'
    },
    colors: [isDark ? '#ffb2bc' : '#8a4853'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: {
      categories: props.chart?.categories || [],
      axisBorder: { show: false },
      axisTicks: { show: false },
      labels: { style: { colors: isDark ? '#9ca3af' : '#64748B' } }
    },
    yaxis: {
      labels: { 
        formatter: (value: number) => 'Rp ' + (value / 1000) + 'k', 
        style: { colors: isDark ? '#9ca3af' : '#64748B' } 
      }
    },
    grid: { 
      borderColor: isDark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9', 
      strokeDashArray: 4 
    },
    fill: {
      type: 'gradient',
      gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
    },
    tooltip: {
      theme: isDark ? 'dark' : 'light'
    }
  };
});

const chartSeries = computed(() => [{
  name: 'Pendapatan',
  data: props.chart?.data || []
}]);
</script>

<template>
  <AdminLayout>
    <Head title="Admin Dashboard | Jelita Saung" />
    
    <template #header-title>
      <div class="flex items-center gap-xl hidden md:flex">
        <h2 class="font-headline-md text-headline-md text-primary font-bold">Ringkasan</h2>
        <nav class="flex gap-lg">
          <a class="font-label-md text-label-md text-primary border-b-2 border-primary pb-xs transition-all" href="#">Ringkasan</a>
          <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all" href="#">Aktivitas Terbaru</a>
        </nav>
      </div>
    </template>

    <!-- Dashboard Canvas -->
    <div class="pt-8 px-lg pb-xl max-w-[1440px] mx-auto">
      <!-- Top Summary Widgets -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-lg">
        <!-- Today's Orders -->
        <Card class="bg-surface-container-lowest border-0 soft-elevation flex flex-col justify-between min-h-[140px] gap-sm rounded-xl p-md overflow-hidden">
          <div class="flex justify-between items-start">
            <span class="text-on-surface-variant font-label-md text-label-md tracking-wider">PESANAN HARI INI</span>
            <div class="bg-secondary-container p-2 rounded-lg">
              <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">shopping_basket</span>
            </div>
          </div>
          <div class="flex items-end justify-between mt-auto">
            <h3 class="text-[32px] font-bold text-on-surface">{{ stats?.todayOrders || 0 }}</h3>
            <span class="text-[#4CAF50] dark:text-emerald-400 text-label-sm font-bold bg-[#E8F5E9] dark:bg-emerald-950/40 px-2 py-1 rounded-full">Pesanan</span>
          </div>
        </Card>

        <!-- Revenue -->
        <Card class="bg-surface-container-lowest border-0 soft-elevation flex flex-col justify-between min-h-[140px] gap-sm rounded-xl p-md overflow-hidden">
          <div class="flex justify-between items-start">
            <span class="text-on-surface-variant font-label-md text-label-md tracking-wider">PENDAPATAN BERSIH</span>
            <div class="bg-tertiary-fixed p-2 rounded-lg">
              <span class="material-symbols-outlined text-tertiary" style="font-variation-settings: 'FILL' 1;">payments</span>
            </div>
          </div>
          <div class="flex items-end justify-between mt-auto">
            <h3 class="text-[32px] font-bold text-on-surface">{{ formatRupiah(stats?.revenueLast30Days || 0) }}</h3>
            <span class="text-[#4CAF50] dark:text-emerald-400 text-label-sm font-bold bg-[#E8F5E9] dark:bg-emerald-950/40 px-2 py-1 rounded-full">30 Hari Terakhir</span>
          </div>
        </Card>

        <!-- Inventory Status -->
        <Card class="bg-surface-container-lowest border-0 soft-elevation flex flex-col justify-between min-h-[140px] gap-sm rounded-xl p-md overflow-hidden">
          <div class="flex justify-between items-start">
            <span class="text-on-surface-variant font-label-md text-label-md tracking-wider">INVENTARIS</span>
            <div class="bg-primary-fixed p-2 rounded-lg">
              <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">inventory</span>
            </div>
          </div>
          <div class="flex items-end justify-between mt-auto">
            <h3 class="text-[32px] font-bold text-on-surface">Stok Rendah: {{ stats?.lowStockCount || 0 }}</h3>
            <Button variant="outline" size="sm" class="text-primary border-primary/20 hover:bg-primary/5 rounded-full h-7 text-xs">
              Lihat Item
            </Button>
          </div>
        </Card>
      </div>

      <!-- Bento Grid Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
        <!-- Left Column for Chart and Table -->
        <div class="lg:col-span-8 flex flex-col gap-gutter">
          <!-- Weekly Performance Chart -->
          <Card class="bg-surface-container-lowest border-0 soft-elevation rounded-xl p-md h-[400px] flex flex-col">
            <div class="flex justify-between items-center mb-lg">
            <div>
              <h4 class="font-headline-md text-headline-md text-on-surface font-bold">Kinerja Pendapatan</h4>
              <p class="text-on-surface-variant text-label-md">Tren pendapatan 30 hari terakhir</p>
            </div>
            <div class="flex gap-sm">
              <span class="flex items-center gap-xs text-label-sm text-on-surface-variant"><span class="w-2 h-2 rounded-full bg-primary"></span> Minggu Ini</span>
              <span class="flex items-center gap-xs text-label-sm text-on-surface-variant"><span class="w-2 h-2 rounded-full bg-outline-variant"></span> Minggu Lalu</span>
            </div>
          </div>
          <div class="flex-1 w-full relative mt-4">
            <VueApexCharts type="area" width="100%" height="280" :options="chartOptions" :series="chartSeries"></VueApexCharts>
          </div>
          </Card>
  
          <!-- Latest Orders Table -->
          <Card class="bg-surface-container-lowest border-0 soft-elevation rounded-xl overflow-hidden flex flex-col">
          <div class="p-md border-b border-surface-container flex justify-between items-center">
            <h4 class="font-headline-md text-headline-md text-on-surface font-bold">Pesanan Terbaru</h4>
            <Button variant="link" class="text-primary font-bold text-label-md p-0 h-auto">Lihat Semua</Button>
          </div>
          <div class="overflow-x-auto flex-1">
            <Table>
              <TableHeader class="bg-surface-container-low/50">
                <TableRow class="hover:bg-transparent border-surface-container">
                  <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">ID</TableHead>
                  <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Pelanggan</TableHead>
                  <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Jumlah</TableHead>
                  <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Status</TableHead>
                  <TableHead class="text-on-surface-variant font-label-md uppercase tracking-widest font-bold">Tanggal</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="order in recentOrders" :key="order.id" class="hover:bg-surface-container-low/30 border-surface-container transition-colors">
                  <TableCell class="font-bold text-primary">#{{ order.order_number }}</TableCell>
                  <TableCell>
                    <div class="flex items-center gap-sm">
                      <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center font-bold text-on-secondary-container text-xs">
                        {{ order.user?.name?.substring(0, 2).toUpperCase() || 'AN' }}
                      </div>
                      <span class="font-medium text-on-surface">{{ order.user?.name || 'Anonim' }}</span>
                    </div>
                  </TableCell>
                  <TableCell class="font-bold text-on-surface">{{ formatRupiah(order.total) }}</TableCell>
                  <TableCell>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide"
                          :class="{
                            'bg-[#FFF8E1] text-[#F57C00] dark:bg-amber-950/50 dark:text-amber-400': order.status === 'pending_payment',
                            'bg-[#E3F2FD] text-[#1976D2] dark:bg-sky-950/50 dark:text-sky-400': order.status === 'processing',
                            'bg-[#E8F5E9] text-[#2E7D32] dark:bg-emerald-950/50 dark:text-emerald-400': order.status === 'shipped' || order.status === 'delivered',
                            'bg-[#FFEBEE] text-[#D32F2F] dark:bg-rose-950/50 dark:text-rose-400': order.status === 'cancelled'
                          }">
                      {{ translateStatus(order.status) }}
                    </span>
                  </TableCell>
                  <TableCell class="text-on-surface-variant text-label-md">{{ new Date(order.created_at).toLocaleDateString() }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
            </div>
          </Card>
        </div>

        <!-- Right Sidebar Widgets Column -->
        <div class="lg:col-span-4 space-y-gutter flex flex-col">
          <!-- Top Products -->
          <Card class="bg-surface-container-lowest border-0 soft-elevation rounded-xl p-md">
            <h4 class="font-headline-md text-headline-md text-on-surface font-bold mb-md">Produk Terlaris</h4>
            <div class="space-y-md">
              <div v-for="item in topProducts" :key="item.product_id" class="flex items-center gap-md group cursor-pointer">
                <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-surface-container-high flex items-center justify-center">
                  <span class="material-symbols-outlined text-3xl text-on-surface-variant">inventory_2</span>
                </div>
                <div class="flex-1">
                  <h5 class="text-label-md font-bold text-on-surface group-hover:text-primary transition-colors">{{ item.product?.name || 'Produk Dihapus' }}</h5>
                  <p class="text-on-surface-variant text-label-sm">Terjual: {{ item.total_sold }} unit</p>
                  <div class="flex justify-between items-center mt-xs">
                    <span class="text-primary font-bold">{{ formatRupiah(item.product?.price || 0) }}</span>
                    <span class="text-tertiary font-bold text-label-sm flex items-center gap-1">
                       Stok: {{ item.product?.stock || 0 }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </Card>

          <!-- Most Viewed Products -->
          <Card class="bg-surface-container-lowest border-0 soft-elevation rounded-xl p-md">
            <h4 class="font-headline-md text-headline-md text-on-surface font-bold mb-md flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">visibility</span>
              Paling Banyak Dilihat
            </h4>
            <div class="space-y-md">
              <div v-for="item in mostViewedProducts" :key="item.id" class="flex items-center gap-md group cursor-pointer">
                <div class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 bg-surface-container-high flex items-center justify-center">
                  <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover" />
                  <span v-else class="material-symbols-outlined text-3xl text-on-surface-variant">inventory_2</span>
                </div>
                <div class="flex-1">
                  <h5 class="text-label-md font-bold text-on-surface group-hover:text-primary transition-colors">{{ item.name }}</h5>
                  <div class="flex justify-between items-center mt-xs">
                    <span class="text-on-surface-variant text-label-sm">{{ item.view_count }}x dilihat</span>
                    <span class="text-tertiary font-bold text-label-sm">{{ formatRupiah(item.price) }}</span>
                  </div>
                </div>
              </div>
              <div v-if="!mostViewedProducts?.length" class="text-center text-on-surface-variant py-4 text-sm">
                Belum ada data view produk.
              </div>
            </div>
          </Card>

          <!-- Recent Reviews Feed -->
          <Card class="bg-surface-container-lowest border-0 soft-elevation rounded-xl p-md overflow-hidden flex flex-col flex-1 h-[400px]">
            <h4 class="font-headline-md text-headline-md text-on-surface font-bold mb-md">Ulasan Terbaru</h4>
            <div class="flex-1 overflow-y-auto pr-xs scrollbar-hide space-y-md">
              <div v-if="!recentReviews?.length" class="text-center text-on-surface-variant py-4 text-sm">
                Belum ada ulasan.
              </div>
              <div v-for="review in recentReviews" :key="review.id" class="p-sm bg-surface-container-low rounded-xl border-l-4 border-primary">
                <div class="flex justify-between items-start mb-xs">
                  <div class="flex items-center gap-sm">
                    <div class="w-6 h-6 rounded-full bg-secondary-container flex items-center justify-center text-[10px] font-bold text-primary">
                      {{ review.user?.name?.substring(0, 2).toUpperCase() }}
                    </div>
                    <span class="font-bold text-label-sm">{{ review.user?.name || 'Anonim' }}</span>
                  </div>
                  <div class="flex text-tertiary text-[12px]">
                    <span v-for="i in 5" :key="i" class="material-symbols-outlined text-[16px]" :style="i <= review.rating ? 'font-variation-settings: \'FILL\' 1;' : ''">star</span>
                  </div>
                </div>
                <p class="text-label-sm text-on-surface-variant mb-1">{{ review.product?.name }}</p>
                <p v-if="review.comment" class="text-label-sm text-on-surface leading-snug">"{{ review.comment }}"</p>
              </div>
            </div>
          </Card>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.soft-elevation {
    box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.04);
}
.soft-elevation:hover {
    transform: translateY(-4px);
    box-shadow: 0px 12px 40px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
