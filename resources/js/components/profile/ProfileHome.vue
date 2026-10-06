<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    authUser: any;
    latestOrder: any;
    statusConfig: Record<string, { label: string; dot: string; badge: string }>;
    timelineSteps: { key: string; label: string }[];
    fallbackAvatar: string;
    fallbackBanner: string;
}>();

const emit = defineEmits(['change-tab', 'handle-image-error']);

const getStatusConfig = (status: string, statusConfig: any) => statusConfig[status] || { label: status, dot: 'bg-gray-400', badge: 'bg-gray-50 text-gray-600 border-gray-100' };

const statusOrder = ['pending_payment', 'processing', 'shipped', 'delivered'];
const getStepActive = (order: any, stepKey: string) => {
    const currentIdx = statusOrder.indexOf(order.status);
    const stepIdx = statusOrder.indexOf(stepKey);
    return stepIdx <= currentIdx;
};
const getStepCurrent = (order: any, stepKey: string) => order.status === stepKey;

</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-[24px]">
        <!-- User Hero Card -->
        <div class="md:col-span-3 bg-surface-container-lowest rounded-2xl p-[24px] sm:p-[32px] flex flex-col md:flex-row items-center gap-[24px] sm:gap-[32px] shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none border border-outline-variant/20 relative overflow-hidden">
        <div class="relative flex-shrink-0">
                <div class="w-[120px] h-[120px] rounded-full overflow-hidden border-[4px] border-primary-fixed shadow-md bg-primary/10 flex items-center justify-center">
                    <img
                        v-if="authUser?.profile_photo"
                        class="w-full h-full object-cover"
                        :src="authUser.profile_photo"
                        @error="$emit('handle-image-error', $event, fallbackAvatar)"
                        alt="User Portrait"
                    />
                    <span v-else class="text-[40px] font-bold text-primary select-none">
                        {{ (authUser?.name || 'C').charAt(0).toUpperCase() }}
                    </span>
                </div>
                <button @click="$emit('change-tab', 'edit_profile')" class="absolute bottom-1 right-1 bg-primary text-white w-9 h-9 rounded-full shadow-lg flex items-center justify-center hover:scale-110 active:scale-95 transition-all duration-200 border-2 border-white dark:border-[#1a1617] cursor-pointer" title="Ubah Avatar">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                </button>
            </div>

            <div class="text-center md:text-left flex-grow min-w-0">
                <h2 class="text-[24px] sm:text-[28px] font-bold text-on-surface tracking-tight truncate">{{ authUser?.name || 'Pelanggan' }}</h2>
                <p class="text-on-surface-variant text-[15px] sm:text-[16px] mt-1 truncate">{{ authUser?.email || 'pelanggan@example.com' }}</p>
                
                <div class="mt-[16px] flex flex-wrap justify-center md:justify-start gap-[10px]">
                    <span class="bg-tertiary-fixed text-on-tertiary-fixed px-[14px] py-[6px] rounded-full text-[12px] font-semibold flex items-center gap-[6px] shadow-sm">
                        <span class="material-symbols-outlined text-[16px] text-[#715822] dark:text-[#261a00]" :style="{ fontVariationSettings: '&quot;FILL&quot; 1' }">stars</span>
                        <span>Anggota Premium</span>
                    </span>
                    <span class="bg-secondary-container text-on-secondary-container px-[14px] py-[6px] rounded-full text-[12px] font-medium">
                        Bergabung Sep 2023
                    </span>
                </div>
            </div>

            <div class="bg-primary-fixed/30 dark:bg-primary/15 rounded-2xl p-[20px] text-center min-w-[180px] w-full md:w-auto border border-primary/20 shadow-inner">
                <p class="text-[11px] font-extrabold text-primary uppercase tracking-widest">Poin Loyalitas</p>
                <p class="text-[32px] sm:text-[36px] font-bold text-primary my-[4px] tracking-tight">2,450</p>
                <button class="text-primary font-bold text-[13px] hover:underline transition-all cursor-pointer mt-1 inline-block">Tukar Hadiah</button>
            </div>
        </div>

        <!-- Recent Order Status -->
        <div class="md:col-span-2 bento-card bg-surface-container-lowest rounded-2xl p-[24px] shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none border border-outline-variant/20 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-[20px]">
                    <h3 class="text-[18px] sm:text-[20px] font-bold text-on-surface">Pesanan Terbaru</h3>
                    <button @click="$emit('change-tab', 'orders')" class="text-primary font-bold text-[14px] hover:underline cursor-pointer">Lihat Semua</button>
                </div>

                <div class="flex items-center gap-[16px] p-[16px] bg-surface-container-low rounded-xl border border-outline-variant/20" v-if="latestOrder">
                    <div class="flex-grow min-w-0">
                        <p class="font-bold text-[15px] sm:text-[16px] text-on-surface truncate">#{{ latestOrder.order_number }}</p>
                        <p class="text-[13px] text-on-surface-variant mt-0.5">
                            {{ latestOrder.items?.length || 0 }} produk • {{ latestOrder.items?.[0]?.product_name }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span :class="['inline-block px-[10px] py-[3px] rounded-full text-[11px] font-bold tracking-wide border', getStatusConfig(latestOrder.status, statusConfig).badge]">
                            {{ getStatusConfig(latestOrder.status, statusConfig).label }}
                        </span>
                        <p class="text-[12px] text-on-surface-variant font-medium mt-1">{{ latestOrder.created_at }}</p>
                    </div>
                </div>
                <div v-else class="text-center p-[24px] text-on-surface-variant text-[14px]">
                    <span class="material-symbols-outlined text-3xl mb-2 block">inventory_2</span>
                    Belum ada pesanan
                </div>
            </div>

            <!-- Progress Timeline (from real order status) -->
            <div class="mt-[28px] pt-[8px] flex gap-[12px] justify-between overflow-x-auto pb-2 px-2" v-if="latestOrder">
                <div v-for="step in timelineSteps" :key="step.key"
                    class="flex flex-col items-center gap-1.5 min-w-[64px]"
                    :class="!getStepActive(latestOrder, step.key) ? 'opacity-40' : ''">
                    <div :class="[
                        'w-3 h-3 rounded-full shadow-sm',
                        getStepActive(latestOrder, step.key) ? 'bg-primary' : 'bg-outline-variant',
                        getStepCurrent(latestOrder, step.key) ? 'w-3.5 h-3.5 ring-4 ring-primary/20 animate-pulse' : ''
                    ]"></div>
                    <div class="w-px h-5" :class="getStepActive(latestOrder, step.key) ? 'bg-primary/40' : 'bg-outline-variant'"></div>
                    <span :class="[
                        'text-[11px] uppercase tracking-wider font-medium text-center',
                        getStepCurrent(latestOrder, step.key) ? 'text-primary font-extrabold' : 'text-on-surface'
                    ]">{{ step.label }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bento-card bg-surface-container-lowest rounded-2xl p-[24px] shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none border border-outline-variant/20 flex flex-col justify-between">
            <div>
                <h3 class="text-[18px] sm:text-[20px] font-bold text-on-surface mb-[20px]">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-[12px]">
                    <button @click="$emit('change-tab', 'orders')" class="flex flex-col items-center justify-center p-[16px] rounded-xl border border-outline-variant/40 hover:border-primary hover:bg-primary/10 transition-all duration-200 gap-[8px] cursor-pointer group text-center active:scale-95">
                        <span class="material-symbols-outlined text-primary text-[26px] group-hover:scale-110 transition-transform">local_shipping</span>
                        <span class="text-[12px] font-semibold text-on-surface-variant group-hover:text-primary transition-colors">Lacak Pesanan</span>
                    </button>
                    <button class="flex flex-col items-center justify-center p-[16px] rounded-xl border border-outline-variant/40 hover:border-primary hover:bg-primary/10 transition-all duration-200 gap-[8px] cursor-pointer group text-center active:scale-95">
                        <span class="material-symbols-outlined text-primary text-[26px] group-hover:scale-110 transition-transform">support_agent</span>
                        <span class="text-[12px] font-semibold text-on-surface-variant group-hover:text-primary transition-colors">Bantuan</span>
                    </button>
                    <button @click="$emit('change-tab', 'orders')" class="flex flex-col items-center justify-center p-[16px] rounded-xl border border-outline-variant/40 hover:border-primary hover:bg-primary/10 transition-all duration-200 gap-[8px] cursor-pointer group text-center active:scale-95">
                        <span class="material-symbols-outlined text-primary text-[26px] group-hover:scale-110 transition-transform">credit_card</span>
                        <span class="text-[12px] font-semibold text-on-surface-variant group-hover:text-primary transition-colors">Pembayaran</span>
                    </button>
                    <button @click="$emit('change-tab', 'addresses')" class="flex flex-col items-center justify-center p-[16px] rounded-xl border border-outline-variant/40 hover:border-primary hover:bg-primary/10 transition-all duration-200 gap-[8px] cursor-pointer group text-center active:scale-95">
                        <span class="material-symbols-outlined text-primary text-[26px] group-hover:scale-110 transition-transform">settings</span>
                        <span class="text-[12px] font-semibold text-on-surface-variant group-hover:text-primary transition-colors">Pengaturan</span>
                    </button>
                </div>
            </div>
            <div class="mt-[20px] pt-[16px] border-t border-outline-variant/20 text-center">
                <span class="text-[12px] text-on-surface-variant opacity-80">Butuh bantuan segera? <a href="#" class="text-primary font-bold hover:underline">Chat langsung</a></span>
            </div>
        </div>

        <!-- Promotional / Featured Banner -->
        <div class="md:col-span-3 bg-secondary-container rounded-2xl overflow-hidden relative min-h-[140px] sm:min-h-[160px] flex items-center p-[24px] sm:p-[36px] shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none border border-outline-variant/20">
            <div class="relative z-10 max-w-[440px]">
                <span class="text-[11px] font-bold uppercase tracking-widest text-primary mb-1 block">Rekomendasi Pribadi</span>
                <h4 class="text-[22px] sm:text-[26px] font-extrabold text-on-secondary-container tracking-tight">Disesuaikan dengan Selera Anda</h4>
                <p class="text-on-secondary-fixed-variant text-[14px] sm:text-[15px] mt-1.5 leading-relaxed opacity-90">Berdasarkan minat terbaru Anda pada seni tekstil dan tenun klasik Lombok.</p>
                <Link :href="`/shop`" class="mt-[20px] bg-primary text-white px-[26px] py-[12px] rounded-full font-bold text-[14px] hover:opacity-95 hover:scale-[1.03] active:scale-95 transition-all duration-300 inline-flex items-center gap-2 shadow-lg">
                    <span>Jelajahi Koleksi</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </Link>
            </div>
            <div class="absolute right-0 top-0 h-full w-3/5 md:w-1/2 opacity-50 md:opacity-75 pointer-events-none">
                <img 
                    class="w-full h-full object-cover object-center [mask-image:linear-gradient(to_right,transparent,black_40%)]" 
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBajFpeMkaHbwInVCRsDzMDa6Nxz2qgBffefv83j2A29lu6upmL6T5lt0p2iWHLosKtDZB3JA1Cerv6JbuJppc4XK5Eb6kEJfAhQQ3auhfHi8lVm5lZgJ9Cr-3NwOv5UXGblNzBHJ3CDqwsObKPKeHbXJ5bRn78A9vx2fhM4i1406xdKevwj5oWIJojb_l1CzEEG0NgkqfnGlTxYuvMnvYjXW2pRx6XYOHLPvRTqDpyc5dWj5-zRAME0OA2jSIWHRY8JUEItPZjCv0C" 
                    @error="$emit('handle-image-error', $event, fallbackBanner)"
                    alt="Kurasi Kain Tenun"
                />
            </div>
        </div>
    </div>
</template>
