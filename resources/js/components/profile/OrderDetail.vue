<script setup lang="ts">
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    selectedOrder: any;
    statusConfig: Record<string, { label: string; dot: string; badge: string }>;
}>();

const emit = defineEmits(['change-tab', 'confirm-received']);

const getStatusConfig = (status: string, statusConfig: any) => statusConfig[status] || { label: status, dot: 'bg-gray-400', badge: 'bg-gray-50 text-gray-600 border-gray-100' };

const formatCurrency = (amount: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);

const paymentMethodLabel: Record<string, string> = {
    bank_transfer: 'Transfer Bank (BRI)',
    qris: 'QRIS',
    cod: 'COD (Bayar di Tempat)',
};

const paymentStatusLabel: Record<string, string> = {
    pending: '⏳ Menunggu Verifikasi',
    paid:    '✅ Terverifikasi',
    expired: '⚠️ Kadaluarsa',
    failed:  '❌ Ditolak',
};

const confirmReceived = () => {
    if (confirm('Apakah Anda yakin barang sudah diterima dengan baik?')) {
        emit('confirm-received', props.selectedOrder.id);
    }
};

const showReviewModal = ref(false);
const reviewingItem = ref<any>(null);

const reviewForm = useForm({
    product_id: '',
    order_id: '',
    rating: 5,
    comment: '',
});

const openReviewModal = (item: any) => {
    reviewingItem.value = item;
    reviewForm.product_id = item.product_id;
    reviewForm.order_id = props.selectedOrder.id;
    reviewForm.rating = 5;
    reviewForm.comment = '';
    showReviewModal.value = true;
};

const submitReview = () => {
    reviewForm.post('/reviews', {
        preserveScroll: true,
        onSuccess: () => {
            showReviewModal.value = false;
            // The item will be updated from Inertia reload but we could also patch locally if needed.
        }
    });
};
</script>

<template>
    <div class="w-full animate-fade-in" v-if="selectedOrder">
        <!-- Breadcrumbs & Title -->
        <div class="mb-6 sm:mb-lg flex flex-col sm:flex-row justify-between sm:items-end gap-3 sm:gap-4">
            <div>
                <nav class="flex items-center space-x-1 text-on-surface-variant mb-1.5 flex-wrap gap-y-1 text-xs sm:text-sm">
                    <button class="hover:text-primary cursor-pointer" @click="$emit('change-tab', 'profile')">Beranda</button>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <button class="hover:text-primary cursor-pointer" @click="$emit('change-tab', 'orders')">Riwayat Pesanan</button>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-primary font-semibold truncate max-w-[160px] sm:max-w-none">Pesanan #{{ selectedOrder.order_number }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-on-surface">Detail Pesanan</h1>
                    <span :class="['px-2.5 py-0.5 rounded-full text-xs font-semibold border', getStatusConfig(selectedOrder.status, statusConfig).badge]">
                        {{ getStatusConfig(selectedOrder.status, statusConfig).label }}
                    </span>
                </div>
                <p class="text-secondary text-xs sm:text-sm mt-1">Dibuat pada {{ selectedOrder.created_at }}</p>
            </div>
            
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full sm:w-auto">
                <a :href="`/orders/${selectedOrder.id}/invoice`" target="_blank"
                    class="flex-1 sm:flex-initial justify-center bg-surface-variant text-on-surface-variant hover:text-primary hover:bg-primary/10 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    Cetak Invoice
                </a>
                
                <button v-if="selectedOrder.status === 'shipped'" @click="confirmReceived"
                    class="flex-1 sm:flex-initial justify-center bg-emerald-600 text-white px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold hover:opacity-90 transition-all flex items-center gap-1.5 shadow-xs shadow-emerald-500/20 active:scale-95 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Konfirmasi Diterima
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
            <!-- Left Column: Items -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Order Items -->
                <section class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] border border-outline-variant/20">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-primary mb-4">Daftar Produk</h2>
                    <div class="space-y-4">
                        <div v-for="item in selectedOrder.items" :key="item.id"
                            class="flex gap-3 sm:gap-4 py-3 border-b border-surface-variant last:border-0 last:pb-0">
                            <!-- Product Image -->
                            <div class="w-16 h-20 sm:w-20 sm:h-24 bg-surface-variant rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                                <img v-if="item.product_image" :src="item.product_image" :alt="item.product_name"
                                    class="w-full h-full object-cover" />
                                <span v-else class="material-symbols-outlined text-outline text-2xl sm:text-3xl">image</span>
                            </div>
                            <div class="flex-grow min-w-0 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-on-surface line-clamp-2">
                                        {{ item.product_name }}</h3>
                                    <p class="text-xs text-secondary mt-0.5">{{ item.quantity }} x {{ formatCurrency(item.product_price) }}</p>
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-2 mt-2">
                                    <p class="text-xs sm:text-sm font-bold text-primary">{{ formatCurrency(item.subtotal) }}</p>
                                    <div v-if="selectedOrder.status === 'delivered'">
                                        <button v-if="!item.review" @click="openReviewModal(item)" class="text-[11px] sm:text-xs bg-primary text-white px-3 py-1 rounded-full hover:bg-primary/90 transition-colors cursor-pointer">
                                            Beri Ulasan
                                        </button>
                                        <div v-else class="flex text-soft-gold" title="Ulasan Anda">
                                            <span v-for="i in 5" :key="i" class="material-symbols-outlined text-[14px]" :style="i <= item.review.rating ? 'font-variation-settings: \'FILL\' 1;' : ''">star</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Shipping Address -->
                <section class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] border border-outline-variant/20">
                    <div class="flex items-center gap-sm mb-md text-primary">
                        <span class="material-symbols-outlined">location_on</span>
                        <h2 class="font-label-md text-label-md uppercase tracking-widest">Alamat Pengiriman</h2>
                    </div>
                    <div class="text-on-surface-variant">
                        <p class="font-label-md text-label-md text-on-surface mb-xs">{{ selectedOrder.ship_name }}</p>
                        <p class="font-body-md text-body-md leading-relaxed">{{ selectedOrder.ship_address }}, {{ selectedOrder.ship_city }}</p>
                        <p class="font-body-md text-body-md mt-sm">{{ selectedOrder.ship_phone }}</p>
                        <p class="font-body-md text-body-md mt-1 text-xs">Kurir: <strong class="uppercase">{{ selectedOrder.shipping_method }}</strong></p>
                        <div v-if="selectedOrder.tracking_number" class="mt-3 p-3 bg-primary/5 border border-primary/20 rounded-lg">
                            <p class="text-xs text-on-surface-variant">Nomor Resi / Tracking</p>
                            <p class="font-bold text-primary tracking-wider">{{ selectedOrder.tracking_number }}</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Column: Summary & Payment -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Order Summary -->
                <section class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] border border-outline-variant/20">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-primary mb-4">Rincian Pembayaran</h2>
                    <div class="space-y-2.5 text-xs sm:text-sm text-on-surface-variant mb-4 border-b border-surface-variant pb-4">
                        <div class="flex justify-between">
                            <span>Subtotal Produk</span>
                            <span class="font-medium text-on-surface">{{ formatCurrency(selectedOrder.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Ongkos Kirim</span>
                            <span class="font-medium text-on-surface">{{ formatCurrency(selectedOrder.shipping_cost) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Pajak (11%)</span>
                            <span class="font-medium text-on-surface">{{ formatCurrency(selectedOrder.tax) }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-sm sm:text-base text-on-surface">Total Belanja</span>
                        <span class="font-bold text-base sm:text-xl text-primary">{{ formatCurrency(selectedOrder.total) }}</span>
                    </div>
                </section>

                <!-- Payment Status -->
                <section v-if="selectedOrder.payment" class="bg-surface-container-lowest p-4 sm:p-6 rounded-2xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] border-l-4 border-primary border border-outline-variant/20">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-secondary mb-2.5">Status Pembayaran</h2>
                    <div>
                        <p class="font-semibold text-xs sm:text-sm text-on-surface">{{ paymentMethodLabel[selectedOrder.payment.method] || selectedOrder.payment.method }}</p>
                        <p class="text-xs sm:text-sm mt-1 text-primary font-bold">{{ paymentStatusLabel[selectedOrder.payment.status] || selectedOrder.payment.status }}</p>
                        
                        <!-- Bukti bayar thumbnail -->
                        <a v-if="selectedOrder.payment.transfer_proof" :href="selectedOrder.payment.transfer_proof" target="_blank" class="block mt-3">
                            <img :src="selectedOrder.payment.transfer_proof" alt="Bukti Bayar"
                                class="w-full max-h-36 object-contain rounded-lg border border-outline-variant/30 hover:opacity-80 transition-opacity" />
                            <p class="text-xs text-primary text-center mt-1 hover:underline">Lihat bukti pembayaran</p>
                        </a>
                    </div>
                </section>
            </div>
        </div>

        <!-- Review Modal -->
        <Teleport to="body">
            <div v-if="showReviewModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 w-screen h-screen">
                <div class="bg-surface-container-lowest dark:bg-card border border-transparent dark:border-outline-variant/30 text-on-surface rounded-2xl p-6 md:p-8 w-[90vw] sm:w-[500px] max-w-lg min-w-[320px] shadow-2xl relative transform transition-all">
                    <!-- Close button -->
                    <button @click="showReviewModal = false" class="absolute top-4 right-4 text-on-surface-variant hover:text-on-surface transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-2">Beri Ulasan Produk</h3>
                        <p class="text-body-md text-on-surface-variant mb-6">Bagaimana pendapat Anda mengenai <strong>{{ reviewingItem?.product_name }}</strong>?</p>
                        
                        <form @submit.prevent="submitReview" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-on-surface mb-2">Penilaian</label>
                                <div class="flex gap-2 text-soft-gold">
                                    <button type="button" v-for="i in 5" :key="i" @click="reviewForm.rating = i" class="hover:scale-110 transition-transform cursor-pointer">
                                        <span class="material-symbols-outlined text-[32px]" :style="i <= reviewForm.rating ? 'font-variation-settings: \'FILL\' 1;' : ''">star</span>
                                    </button>
                                </div>
                                <div v-if="reviewForm.errors.rating" class="text-error text-xs mt-1">{{ reviewForm.errors.rating }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-on-surface mb-2">Komentar (Opsional)</label>
                                <textarea 
                                    v-model="reviewForm.comment"
                                    rows="4" 
                                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-surface-container-low dark:bg-card focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all resize-none text-on-surface placeholder:text-on-surface-variant/60"
                                    placeholder="Ceritakan pengalaman Anda dengan produk ini..."></textarea>
                                <div v-if="reviewForm.errors.comment" class="text-error text-xs mt-1">{{ reviewForm.errors.comment }}</div>
                            </div>

                            <div class="pt-4 flex justify-end gap-3">
                                <button type="button" @click="showReviewModal = false" class="px-6 py-2 rounded-xl text-primary font-semibold hover:bg-primary/5 dark:hover:bg-primary/20 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" :disabled="reviewForm.processing" class="px-6 py-2 rounded-xl bg-primary text-white font-semibold hover:bg-primary/90 transition-colors disabled:opacity-50">
                                    <span v-if="reviewForm.processing">Menyimpan...</span>
                                    <span v-else>Kirim Ulasan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Teleport>

    </div>
</template>
