<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';

defineProps<{
    orders: any[];
    statusConfig: Record<string, { label: string; dot: string; badge: string }>;
    timelineSteps: { key: string; label: string }[];
    statusOrder: string[];
}>();

const emit = defineEmits(['change-tab', 'view-order', 'confirm-received']);

const getStatusConfig = (status: string, statusConfig: any) => statusConfig[status] || { label: status, dot: 'bg-gray-400', badge: 'bg-gray-50 text-gray-600 border-gray-100' };

const getStepActive = (order: any, stepKey: string, statusOrder: string[]) => {
    const currentIdx = statusOrder.indexOf(order.status);
    const stepIdx = statusOrder.indexOf(stepKey);
    return stepIdx <= currentIdx;
};
const getStepCurrent = (order: any, stepKey: string) => order.status === stepKey;

const formatCurrency = (amount: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);

const paymentMethodLabel: Record<string, string> = {
    bank_transfer: 'Transfer Bank (BRI))',
    qris: 'QRIS',
};

const paymentStatusLabel: Record<string, string> = {
    pending: '⏳ Menunggu Verifikasi',
    paid:    '✅ Terverifikasi',
    expired: '⚠️ Kadaluarsa',
    failed:  '❌ Ditolak',
};

const confirmReceived = (orderId: number) => {
    if (confirm('Apakah Anda yakin barang sudah diterima dengan baik?')) {
        emit('confirm-received', orderId);
    }
};

</script>

<template>
    <div class="w-full animate-fade-in">
        <!-- Page Header -->
        <div class="mb-xl flex flex-col md:flex-row md:items-end justify-between gap-md">
            <div>
                <nav class="flex items-center gap-xs mb-base text-secondary font-label-sm text-label-sm">
                    <button class="hover:text-primary" @click="$emit('change-tab', 'profile')">Akun</button>
                    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                    <span class="text-on-surface">Riwayat Pesanan</span>
                </nav>
                <h1 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg font-bold text-on-surface">Pesanan Saya</h1>
                <p class="text-secondary mt-base font-body-md text-body-md">Pantau status dan riwayat pesanan kamu.</p>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="orders.length === 0" class="text-center py-20">
            <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4 block">inventory_2</span>
            <h3 class="text-xl font-bold text-on-surface mb-2">Belum ada pesanan</h3>
            <p class="text-on-surface-variant mb-6">Yuk mulai belanja produk handmade kami!</p>
            <Link href="/shop" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-full font-bold hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                Mulai Belanja
            </Link>
        </div>

        <!-- Orders List -->
        <div class="space-y-md" v-else>
            <div v-for="order in orders" :key="order.id"
                class="bg-surface-container-lowest rounded-xl p-md md:p-lg shadow-[0px_4px_20px_rgba(0,0,0,0.04)] border border-outline-variant/30 transition-all duration-300 hover:shadow-md">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-lg">
                    <div class="flex items-start gap-md flex-grow min-w-0">
                        <!-- Icon -->
                        <div class="w-16 h-16 bg-primary/10 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-3xl">shopping_bag</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-sm mb-xs">
                                <span class="font-label-md text-label-md text-on-surface font-semibold">#{{ order.order_number }}</span>
                                <span :class="['px-sm py-xs rounded-full font-label-sm text-label-sm border flex items-center gap-xs', getStatusConfig(order.status, statusConfig).badge]">
                                    <span :class="['w-1.5 h-1.5 rounded-full', getStatusConfig(order.status, statusConfig).dot]"></span>
                                    {{ getStatusConfig(order.status, statusConfig).label }}
                                </span>
                            </div>
                            <p class="text-secondary font-label-md text-label-md mb-xs">{{ order.created_at }}</p>
                            <p class="text-on-surface-variant font-body-md text-body-md">
                                {{ order.items.length }} produk
                                <template v-if="order.items.length > 0"> • {{ order.items[0].product_name }}<span v-if="order.items.length > 1"> + {{ order.items.length - 1 }} lainnya</span></template>
                            </p>
                            <!-- Payment status badge -->
                            <p class="text-xs text-on-surface-variant mt-1" v-if="order.payment">
                                {{ paymentMethodLabel[order.payment.method] || order.payment.method }} •
                                {{ paymentStatusLabel[order.payment.status] || order.payment.status }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-row lg:flex-col items-center lg:items-end justify-between lg:justify-center gap-sm flex-shrink-0">
                        <div class="text-right">
                            <p class="text-secondary font-label-sm text-label-sm mb-xs">Total</p>
                            <p class="font-headline-md text-headline-md text-primary font-bold">{{ formatCurrency(order.total) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button v-if="order.status === 'shipped'"
                                @click="confirmReceived(order.id)"
                                class="bg-emerald-600 text-white px-md py-sm rounded-lg font-label-md text-label-md hover:opacity-90 active:scale-[0.98] transition-all flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                Diterima
                            </button>
                            <button
                                class="bg-primary text-white px-lg py-sm rounded-lg font-label-md text-label-md hover:opacity-90 active:scale-[0.98] transition-all"
                                @click="$emit('view-order', order)">Lihat Detail</button>
                        </div>
                    </div>
                </div>

                <!-- Mini timeline for active orders -->
                <div v-if="order.status !== 'delivered' && order.status !== 'cancelled' && order.status !== 'refunded'"
                    class="mt-4 pt-4 border-t border-outline-variant/20">
                    <div class="flex items-center justify-between">
                        <div v-for="step in timelineSteps" :key="step.key" class="flex flex-col items-center gap-1 flex-1">
                            <div :class="[
                                'w-2.5 h-2.5 rounded-full transition-all',
                                getStepActive(order, step.key, statusOrder) ? 'bg-primary' : 'bg-outline-variant',
                                getStepCurrent(order, step.key) ? 'ring-2 ring-primary/30 ring-offset-1 scale-125' : ''
                            ]"></div>
                            <span :class="['text-[10px] text-center leading-tight', getStepCurrent(order, step.key) ? 'text-primary font-bold' : 'text-on-surface-variant']">{{ step.label }}</span>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div class="mt-2 h-1 bg-outline-variant/30 rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-700"
                            :style="{ width: ((statusOrder.indexOf(order.status) + 1) / statusOrder.length * 100) + '%' }"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
