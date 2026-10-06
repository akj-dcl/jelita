<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

const page = usePage();

const props = defineProps<{
    product: any;
    relatedProducts: any[];
    isWishlisted: boolean;
    wishlistCount: number;
}>();

// Gallery images
const images = ref(props.product.images?.length > 0 
    ? props.product.images.map((img: any) => '/storage/' + img.image_path)
    : (props.product.image ? ['/storage/' + props.product.image] : ['https://placehold.co/400x500/e2e8f0/64748b?text=No+Image'])
);

const currentImageIndex = ref(0);
const isTransitioning = ref(false);

const changeMainImage = (index: number) => {
    isTransitioning.value = true;
    setTimeout(() => {
        currentImageIndex.value = index;
        isTransitioning.value = false;
    }, 150);
};

// Quantity state
const quantity = ref(1);

const adjustQty = (amount: number) => {
    quantity.value = Math.max(1, quantity.value + amount);
};

// Format IDR Helper
const formatRupiah = (value: number) => {
    return 'Rp ' + Math.round(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const selectedVariant = ref(props.product.variants?.[0] || null);

const displayPrice = computed(() => {
    return selectedVariant.value ? selectedVariant.value.price : props.product.price;
});

const displayStock = computed(() => {
    return selectedVariant.value ? selectedVariant.value.stock : props.product.stock;
});

const addToBag = () => {
    if (quantity.value > displayStock.value) {
        alert('Jumlah melebihi stok yang tersedia.');
        return;
    }
    router.post('/cart/add', {
        product_id: props.product.id,
        variant_id: selectedVariant.value?.id || null,
        quantity: quantity.value
    });
};

const showLoginModal = ref(false);
const isWishlisted = ref(props.isWishlisted);

watch(() => props.isWishlisted, (newVal) => {
    isWishlisted.value = newVal;
});

const toggleWishlist = () => {
    if (!page.props.auth.user) {
        showLoginModal.value = true;
        return;
    }
    
    // Optimistic UI update
    isWishlisted.value = !isWishlisted.value;
    
    router.post('/wishlist/toggle', {
        product_id: props.product.id
    }, {
        preserveScroll: true,
        onError: () => {
            isWishlisted.value = !isWishlisted.value; // Revert on error
        }
    });
};
</script>

<template>
    <Head :title="props.product.name + ' | Jelita Saung'">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md overflow-x-clip antialiased">
        <!-- TopNavBar -->
        <ShopNavbar />

        <!-- Main Content -->
        <main class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop py-10 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-gutter">
                <!-- Gallery Section -->
                <section class="lg:col-span-7 flex flex-col-reverse md:flex-row gap-2">
                    <!-- Thumbnails -->
                    <div class="flex md:flex-col gap-2 md:w-24 shrink-0 overflow-x-auto md:overflow-y-auto no-scrollbar">
                        <button 
                            v-for="(img, idx) in images" 
                            :key="idx"
                            @click="changeMainImage(idx)"
                            class="w-20 md:w-full aspect-square border-2 rounded-lg overflow-hidden shrink-0 transition-all hover:opacity-80 cursor-pointer"
                            :class="currentImageIndex === idx ? 'border-primary' : 'border-outline-variant/30'"
                        >
                            <img class="w-full h-full object-cover" :src="img" :alt="'Thumbnail ' + (idx + 1)" />
                        </button>
                    </div>
                    <!-- Main Display -->
                    <div class="flex-grow aspect-[4/5] rounded-xl overflow-hidden bg-white shadow-[0px_4px_20px_rgba(0,0,0,0.04)]">
                        <img 
                            class="w-full h-full object-cover transition-opacity duration-150" 
                            :src="images[currentImageIndex]" 
                            alt="Main Product Image"
                            :class="{ 'opacity-0': isTransitioning, 'opacity-100': !isTransitioning }"
                        />
                    </div>
                </section>

                <!-- Product Info Section -->
                <section class="lg:col-span-5 flex flex-col justify-start">
                    <div class="mb-2">
                        <span class="text-xs font-bold text-tertiary uppercase tracking-widest bg-soft-gold/15 px-3 py-1 rounded-full">
                            {{ props.product.category?.name || 'Kategori' }}
                        </span>
                    </div>
                    <h1 class="text-[32px] md:text-[40px] font-bold text-primary mb-1 leading-tight">{{ props.product.name }}</h1>
                    
                    <div class="flex items-center gap-1 mb-6">
                        <div class="flex text-soft-gold">
                            <span v-for="i in 5" :key="i" class="material-symbols-outlined text-[18px]" :style="i <= Math.round(props.product.average_rating || 0) ? 'font-variation-settings: \'FILL\' 1;' : ''">star</span>
                        </div>
                        <span class="text-xs text-secondary-fixed-variant font-medium ml-2">({{ props.product.average_rating ? Number(props.product.average_rating).toFixed(1) : '0' }} / 5 dari {{ props.product.reviews_count || 0 }} Ulasan)</span>
                    </div>

                    <p class="text-2xl font-bold text-primary mb-10">{{ formatRupiah(displayPrice) }}</p>

                    <div class="space-y-6 mb-16">
                        <h3 class="text-sm font-bold text-on-surface uppercase tracking-wide">Deskripsi</h3>
                        <p class="text-on-surface leading-relaxed text-base whitespace-pre-wrap">
                            {{ props.product.description }}
                        </p>
                    </div>

                    <!-- Video Demo -->
                    <div v-if="props.product.video_path" class="space-y-6 mb-16">
                        <h3 class="text-sm font-bold text-on-surface uppercase tracking-wide">Video 3D / Demo</h3>
                        <div class="rounded-xl overflow-hidden shadow-sm">
                            <video :src="`/storage/${props.product.video_path}`" controls class="w-full object-cover"></video>
                        </div>
                    </div>

                    <!-- Interactions -->
                    <div class="space-y-10 mb-16">
                        <!-- Variant Selector (jika ada varian) -->
                        <div v-if="props.product.variants?.length > 0" class="space-y-3">
                            <h3 class="text-sm font-bold text-on-surface uppercase tracking-wider">Pilih Varian</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <button v-for="variant in props.product.variants" :key="variant.id"
                                        type="button"
                                        @click="selectedVariant = variant"
                                        class="px-4 py-3 rounded-xl border-2 text-sm font-medium transition-all text-left"
                                        :class="selectedVariant?.id === variant.id 
                                            ? 'border-primary bg-primary/5 text-primary' 
                                            : 'border-outline-variant hover:border-primary/50 text-on-surface-variant'">
                                    <span class="block font-semibold">{{ variant.name }}</span>
                                    <span class="block text-xs mt-0.5">{{ formatRupiah(variant.price) }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <div class="flex justify-between items-center w-48">
                                <label class="text-sm font-semibold text-on-surface-variant">Jumlah</label>
                                <span class="text-xs text-primary font-medium bg-primary/10 px-2 py-1 rounded-full">Stok: {{ displayStock }}</span>
                            </div>
                            <div class="flex items-center w-fit border border-outline-variant/30 rounded-[16px] overflow-hidden bg-white">
                                <button @click="adjustQty(-1)" class="p-3 hover:bg-surface-container-low transition-colors text-primary cursor-pointer active:scale-90">
                                    <span class="material-symbols-outlined">remove</span>
                                </button>
                                <span class="px-6 font-bold text-on-surface">{{ quantity }}</span>
                                <button @click="adjustQty(1)" class="p-3 hover:bg-surface-container-low transition-colors text-primary cursor-pointer active:scale-90">
                                    <span class="material-symbols-outlined">add</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button @click="addToBag" class="flex-grow py-4 px-8 bg-dusty-rose text-white rounded-full text-sm font-semibold shadow-md hover:opacity-90 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined">shopping_bag</span>
                                Masukkan ke Keranjang
                            </button>
                            <button @click="toggleWishlist" class="py-4 px-8 bg-pale-pink text-dusty-rose rounded-full text-sm font-semibold hover:bg-pale-pink/80 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <span class="material-symbols-outlined" :style="isWishlisted ? 'font-variation-settings: \'FILL\' 1;' : ''">favorite</span>
                                Favorit
                            </button>
                        </div>
                    </div>
                    
                    <!-- Ulasan Pembeli -->
                    <div class="mt-8 border-t border-surface-variant/30 pt-8">
                        <h3 class="text-lg font-bold text-on-surface mb-6">Ulasan Pembeli</h3>
                        <div v-if="!props.product.reviews || props.product.reviews.length === 0" class="text-on-surface-variant text-sm italic">
                            Belum ada ulasan untuk produk ini.
                        </div>
                        <div v-else class="space-y-6">
                            <div v-for="review in props.product.reviews" :key="review.id" class="border-b border-surface-variant/30 pb-6 last:border-0 last:pb-0">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-on-surface text-sm">{{ review.user?.name || 'Anonim' }}</span>
                                    <span class="text-xs text-on-surface-variant">{{ new Date(review.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) }}</span>
                                </div>
                                <div class="flex text-soft-gold mb-3">
                                    <span v-for="i in 5" :key="i" class="material-symbols-outlined text-[14px]" :style="i <= review.rating ? 'font-variation-settings: \'FILL\' 1;' : ''">star</span>
                                </div>
                                <p class="text-on-surface text-sm leading-relaxed">{{ review.comment }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Related Products Section -->
            <section class="mt-16 pt-16 border-t border-surface-variant/30">
                <div class="flex justify-between items-end mb-10">
                    <div>
                        <h2 class="text-2xl font-bold text-primary mb-1">Anda mungkin juga suka</h2>
                        <p class="text-secondary text-sm">Temukan lebih banyak karya kerajinan tangan</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="w-10 h-10 rounded-full border border-outline-variant/30 flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all cursor-pointer">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <button class="w-10 h-10 rounded-full border border-outline-variant/30 flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all cursor-pointer">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
                    <Link v-for="rel in props.relatedProducts" :key="rel.id" :href="'/product/' + rel.id" class="group cursor-pointer block">
                        <div class="aspect-[3/4] rounded-xl overflow-hidden bg-white shadow-[0px_4px_20px_rgba(0,0,0,0.04)] mb-2 transition-transform duration-500 group-hover:-translate-y-2">
                            <img class="w-full h-full object-cover" :src="rel.image ? '/storage/' + rel.image : 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image'" :alt="rel.name" />
                        </div>
                        <div class="px-1">
                            <h3 class="text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">{{ rel.name }}</h3>
                            <p class="text-dusty-rose font-bold mt-1">{{ formatRupiah(rel.price) }}</p>
                        </div>
                    </Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <!-- Login Modal for Wishlist -->
        <div v-if="showLoginModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-surface rounded-2xl p-8 max-w-sm w-full shadow-lg relative transform transition-all">
                <button @click="showLoginModal = false" class="absolute top-4 right-4 text-on-surface-variant hover:text-on-surface transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>
                <div class="text-center">
                    <div class="w-16 h-16 bg-pale-pink rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-dusty-rose text-3xl">lock</span>
                    </div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface mb-2">Silakan Masuk</h3>
                    <p class="text-body-md text-on-surface-variant mb-8">Anda perlu masuk untuk menambahkan produk ke daftar favorit.</p>
                    <div class="flex flex-col gap-3">
                        <Link href="/login" class="w-full py-3 bg-primary text-white rounded-xl font-label-lg hover:bg-primary/90 transition-colors shadow-sm block text-center">
                            Masuk Sekarang
                        </Link>
                        <button @click="showLoginModal = false" class="w-full py-3 text-primary font-label-lg hover:bg-primary/5 rounded-xl transition-colors">
                            Nanti Saja
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <ShopFooter />
    </div>
</template>

<style scoped>
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
    /* Dark mode adjustments if the system theme is dark */
    --background: #141213;
    --foreground: #f3f0f0;
    --primary: #ffb2bc;
    --primary-foreground: #5a1b24;
    --secondary: #ccc4c7;
    --secondary-foreground: #332f31;
    --border: #524345;
    --outline: #9c8a8c;
}

/* Custom scrollbars for thumbnail tray */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
