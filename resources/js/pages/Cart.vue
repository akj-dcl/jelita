<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

interface CartItem {
    id: number;
    name: string;
    category: string;
    material?: string;
    price: number;
    quantity: number;
    image: string;
    color?: string;
    size?: string;
}

const props = defineProps<{
    cartItems: CartItem[];
    recommendedProducts?: any[];
}>();

// Checkout success state
const isCheckoutSuccess = ref(false);
const promoCode = ref('');
const appliedPromo = ref(false);

// Calculations
const subtotal = computed(() => {
    return props.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const shipping = computed(() => {
    return props.cartItems.length > 0 ? 50000 : 0; // Flat shipping rate Rp 50.000
});

const tax = computed(() => {
    return Math.round(subtotal.value * 0.11); // 11% PPN
});

const totalItems = computed(() => {
    return props.cartItems.reduce((sum, item) => sum + item.quantity, 0);
});

const discount = computed(() => {
    return appliedPromo.value ? Math.round(subtotal.value * 0.1) : 0; // 10% promo discount
});

const total = computed(() => {
    return Math.max(0, subtotal.value + shipping.value + tax.value - discount.value);
});

// Quantity controls
const adjustQty = (id: number, amount: number) => {
    const item = props.cartItems.find(i => i.id === id);
    if (item) {
        const newQty = item.quantity + amount;
        if (newQty >= 1) {
            router.put(`/cart/${id}`, { quantity: newQty }, { preserveScroll: true });
        }
    }
};

const removeItem = (id: number) => {
    router.delete(`/cart/${id}`, { preserveScroll: true });
};

const applyPromo = () => {
    if (promoCode.value.trim().toLowerCase() === 'artisan10') {
        appliedPromo.value = true;
    } else {
        alert('Invalid promo code. Try "ARTISAN10"');
    }
};

const proceedToCheckout = () => {
    router.visit('/checkout');
};

// Formatting helpers
const formatRupiah = (val: number) => {
    return 'Rp ' + Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};
</script>

<template>
    <Head title="Shopping Cart | Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md text-body-md antialiased flex flex-col justify-between">
        <!-- TopNavBar -->
        <ShopNavbar />

        <main v-if="props.cartItems.length === 0" class="flex-grow flex flex-col items-center justify-center px-margin-mobile text-center py-xl">
            <div class="max-w-[900px] w-full">
                <!-- Asymmetric/Bento-style Decorative Element -->
                <div class="relative mb-lg mx-auto w-fit">
                    <div class="empty-cart-animation inline-flex items-center justify-center p-xl rounded-full bg-surface-container-low shadow-[0px_4px_20px_rgba(0,0,0,0.04)]">
                        <span class="material-symbols-outlined text-[80px] text-on-surface-variant select-none" data-icon="shopping_basket">shopping_basket</span>
                    </div>
                    <!-- Floating Decorative Accents -->
                    <div class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-pale-pink/50 blur-xl"></div>
                    <div class="absolute -bottom-2 -left-6 w-16 h-16 rounded-full bg-secondary-container/30 blur-2xl"></div>
                </div>
                <h1 class="font-headline-md text-headline-md text-on-surface mb-sm">
                    Keranjang belanja Anda masih kosong
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-[500px] mx-auto mb-lg leading-relaxed">
                    Sepertinya Anda belum menemukan kerajinan favorit Anda. Jelajahi koleksi pilihan kerajinan tangan kami.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-md">
                    <Link class="w-full sm:w-auto px-lg py-sm bg-dusty-rose text-white font-label-md text-label-md rounded-lg shadow-[0px_4px_20px_rgba(183,110,121,0.2)] hover:opacity-90 active:scale-[0.98] transition-all" href="/shop">
                        Jelajahi Koleksi
                    </Link>
                    <Link class="w-full sm:w-auto px-lg py-sm text-primary font-label-md text-label-md border border-outline-variant hover:bg-surface-container-low rounded-lg transition-all" href="/shop">
                        Lihat Kedatangan Baru
                    </Link>
                </div>
                
                <!-- Soft Recommendation Hint -->
                <div class="mt-24 pt-lg border-t border-surface-variant/50" v-if="props.recommendedProducts && props.recommendedProducts.length > 0">
                    <h2 class="text-2xl font-bold text-on-background mb-8 text-center">Populer Saat Ini</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter text-left">
                        <!-- Normal Product Cards -->
                        <Link v-for="prod in props.recommendedProducts" :key="prod.id" :href="'/product/' + prod.id" class="group cursor-pointer block">
                            <div class="relative aspect-[3/4] bg-surface-variant rounded-xl overflow-hidden mb-4 shadow-[0px_4px_20px_rgba(0,0,0,0.04)]">
                                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" :src="prod.image" :alt="prod.name" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <h4 class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors line-clamp-1">{{ prod.name }}</h4>
                            <p class="font-bold text-primary mt-1">{{ formatRupiah(prod.price) }}</p>
                        </Link>
                    </div>
                </div>
            </div>
        </main>

        <main v-else class="flex-grow max-w-[1440px] w-full mx-auto px-margin-mobile md:px-margin-desktop py-10 md:py-16">
            <!-- Breadcrumb / Header -->
            <div class="mb-10">
                <h1 class="font-display-lg text-4xl md:text-5xl font-bold text-on-background mb-2">Keranjang Anda</h1>
                <p class="font-body-md text-on-surface-variant text-base">Tinjau produk kerajinan pilihan Anda sebelum checkout.</p>
            </div>

            <!-- Checkout Success State -->
            <div v-if="isCheckoutSuccess" class="text-center py-20 bg-surface-container-lowest rounded-2xl p-10 shadow-sm border border-outline-variant/30 max-w-xl mx-auto my-10">
                <span class="material-symbols-outlined text-6xl text-primary mb-4" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                <h3 class="text-2xl font-bold mb-2 text-on-surface">Terima kasih atas pesanan Anda!</h3>
                <p class="text-secondary text-sm max-w-sm mx-auto mb-8">Transaksi Anda telah berhasil disimulasikan. Kami sedang menyiapkan pesanan Anda.</p>
                <Link href="/shop" class="bg-primary text-white py-3 px-8 rounded-full font-semibold hover:opacity-90 transition-all cursor-pointer">
                    Lanjut Belanja
                </Link>
            </div>

            <!-- Normal Cart Grid -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
                <!-- Line Items -->
                <section class="lg:col-span-8 space-y-6">
                    <!-- Item -->
                    <div 
                        v-for="item in props.cartItems" 
                        :key="item.id"
                        class="bg-surface-container-lowest p-6 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] flex flex-col sm:flex-row gap-6 items-center group transition-transform duration-300 hover:scale-[1.005] border border-outline-variant/10"
                    >
                        <div class="w-32 h-40 bg-surface-variant rounded-lg overflow-hidden flex-shrink-0">
                            <img class="w-full h-full object-cover" :src="item.image" :alt="item.name" />
                        </div>
                        <div class="flex-grow flex flex-col sm:flex-row justify-between w-full">
                            <div class="space-y-1">
                                <span 
                                    class="text-[10px] font-bold text-primary bg-secondary-container px-3 py-1 rounded-full uppercase tracking-wider"
                                    :class="item.category === 'Limited Edition' ? 'text-tertiary bg-tertiary-fixed' : ''"
                                >
                                    {{ item.category }}
                                </span>
                                <h3 class="text-lg font-bold text-on-surface mt-2">{{ item.name }}</h3>
                                <p class="text-sm text-on-surface-variant">
                                    <template v-if="item.color">Warna: {{ item.color }}</template>
                                    <template v-if="item.size"> | Ukuran: {{ item.size }}</template>
                                    <template v-if="item.material">Bahan: {{ item.material }}</template>
                                </p>
                                <button @click="removeItem(item.id)" class="text-error font-semibold text-sm flex items-center gap-1 mt-4 hover:underline cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    Hapus
                                </button>
                            </div>
                            <div class="mt-4 sm:mt-0 flex flex-col items-end justify-between">
                                <div class="flex items-center border border-outline-variant rounded-full p-1 gap-4 bg-background shadow-inner">
                                    <button @click="adjustQty(item.id, -1)" class="w-8 h-8 flex items-center justify-center hover:bg-surface-variant rounded-full transition-colors font-bold text-primary cursor-pointer">-</button>
                                    <span class="font-bold text-on-surface text-sm w-4 text-center">{{ item.quantity }}</span>
                                    <button @click="adjustQty(item.id, 1)" class="w-8 h-8 flex items-center justify-center hover:bg-surface-variant rounded-full transition-colors font-bold text-primary cursor-pointer">+</button>
                                </div>
                                <span class="text-xl font-bold text-primary mt-6">{{ formatRupiah(item.price * item.quantity) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex justify-start">
                        <Link class="font-semibold text-primary flex items-center gap-1 hover:underline underline-offset-4 transition-all" href="/shop">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Lanjut Belanja
                        </Link>
                    </div>
                </section>

                <!-- Order Summary Sidebar -->
                <aside class="lg:col-span-4 sticky top-24">
                    <div class="bg-surface-container-low p-8 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] border border-surface-variant/50">
                        <h2 class="text-xl font-bold text-on-surface mb-6">Ringkasan Pesanan</h2>
                        <div class="space-y-4 border-b border-outline-variant pb-6 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant">Subtotal ({{ totalItems }} barang)</span>
                                <span class="text-on-surface font-semibold">{{ formatRupiah(subtotal) }}</span>
                            </div>
                            <div v-if="appliedPromo" class="flex justify-between items-center text-primary">
                                <span>Kode Promo (Diskon 10%)</span>
                                <span>-{{ formatRupiah(discount) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant">Estimasi Ongkir</span>
                                <span class="text-on-surface font-semibold">{{ formatRupiah(shipping) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-1">
                                    <span class="text-on-surface-variant">Estimasi Pajak (11%)</span>
                                </div>
                                <span class="text-on-surface font-semibold">{{ formatRupiah(tax) }}</span>
                            </div>
                        </div>
                        <div class="py-6 flex justify-between items-center mb-6">
                            <span class="text-lg font-bold text-on-surface">Total</span>
                            <span class="text-2xl font-bold text-primary">{{ formatRupiah(total) }}</span>
                        </div>
                        <div class="space-y-4">
                            <button @click="proceedToCheckout" class="w-full bg-primary text-white py-4 px-6 rounded-full font-bold text-sm hover:opacity-90 active:scale-[0.98] transition-all shadow-md cursor-pointer">
                                Lanjutkan ke Pembayaran
                            </button>
                        </div>
                        <div class="mt-8 flex flex-col gap-3">
                            <div class="flex items-center gap-2 text-on-secondary-fixed-variant">
                                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                                <span class="text-xs">Checkout aman dengan enkripsi SSL 256-bit</span>
                            </div>
                            <div class="flex items-center gap-2 text-on-secondary-fixed-variant">
                                <span class="material-symbols-outlined text-primary text-[18px]" style="font-variation-settings: 'FILL' 1;">package_2</span>
                                <span class="text-xs">Gratis pengembalian dalam 30 hari</span>
                            </div>
                        </div>

                        <!-- Discount Code -->
                        <div class="mt-8 pt-6 border-t border-outline-variant/30">
                            <label class="text-xs font-semibold text-on-surface-variant mb-2 block" for="promo">Kode Promo</label>
                            <div class="flex gap-2">
                                <input 
                                    v-model="promoCode"
                                    class="flex-grow bg-white border border-outline-variant rounded-lg px-4 py-2 text-sm outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all text-on-surface" 
                                    id="promo" 
                                    placeholder="Masukkan kode (misal: ARTISAN10)" 
                                    type="text"
                                    :disabled="appliedPromo"
                                />
                                <button 
                                    @click="applyPromo"
                                    class="bg-secondary-container text-primary px-6 py-2 rounded-lg text-sm font-semibold hover:bg-primary hover:text-white transition-all cursor-pointer"
                                    :disabled="appliedPromo"
                                >
                                    {{ appliedPromo ? 'Terpasang' : 'Pasang' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Recommendations Section -->
            <section class="mt-16 pt-16 border-t border-surface-variant/50">
                <h2 class="text-2xl font-bold text-on-background mb-8">Anda mungkin juga suka</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter">
                    <!-- Product -->
                    <Link 
                        v-for="prod in props.recommendedProducts"
                        :key="prod.id"
                        href="/product"
                        class="group cursor-pointer block"
                    >
                        <div class="relative aspect-[3/4] bg-surface-variant rounded-xl overflow-hidden mb-4 shadow-[0px_4px_20px_rgba(0,0,0,0.04)]">
                            <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" :src="prod.image" :alt="prod.name" />
                            <button class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-md p-2 rounded-full opacity-0 group-hover:opacity-100 transition-all shadow-lg hover:bg-primary hover:text-white">
                                <span class="material-symbols-outlined text-primary hover:text-inherit text-[20px]">add_shopping_cart</span>
                            </button>
                        </div>
                        <h4 class="font-semibold text-sm text-on-surface group-hover:text-primary transition-colors">{{ prod.name }}</h4>
                        <p class="font-bold text-primary mt-1">{{ formatRupiah(prod.price) }}</p>
                    </Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <ShopFooter />
    </div>
</template>

<style scoped>
.empty-cart-animation {
    animation: float 6s ease-in-out infinite;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.bg-dusty-rose { background-color: #B76E79; }
.bg-pale-pink { background-color: #F7EEF1; }

.welcome-theme {
    /* Scoped theme variables matching the Jelita Saung palette */
    --background: #fbf9f8;
    --foreground: #1b1c1c;
    --primary: #8a4853;
    --primary-foreground: #ffffff;
    --secondary: #625d5f;
    --secondary-foreground: #ffffff;
    --border: #d7c1c3;
    --outline: #857374;
}

:global(.dark) .welcome-theme {
    /* Dark mode adjustments */
    --background: #141213;
    --foreground: #f3f0f0;
    --primary: #ffb2bc;
    --primary-foreground: #5a1b24;
    --secondary: #ccc4c7;
    --secondary-foreground: #332f31;
    --border: #524345;
    --outline: #9c8a8c;
}
</style>
