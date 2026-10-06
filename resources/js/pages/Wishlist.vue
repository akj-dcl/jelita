<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

interface WishlistItem {
    id: number;
    name: string;
    price: number;
    description: string;
    image: string;
}

const props = defineProps<{
    wishlistItems: any[];
    recommendedProducts?: Array<{
        id: number;
        name: string;
        price: number;
        image: string;
    }>;
}>();

const formatPrice = (price: number): string => {
    return 'Rp ' + Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const removeItem = (id: number) => {
    router.post('/wishlist/toggle', { product_id: id }, {
        preserveScroll: true
    });
};

const handleAddToBag = (item: any) => {
    router.post('/cart/add', {
        product_id: item.id,
        quantity: 1
    });
};

const handleAddAllToBag = () => {
    if (props.wishlistItems.length === 0) return;
    router.visit('/cart');
};

const handleShareList = () => {
    if (typeof window !== 'undefined' && navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href);
        alert("Tautan Wishlist Jelita Saung berhasil disalin ke clipboard!");
    } else {
        alert("Tautan Wishlist: " + (typeof window !== 'undefined' ? window.location.href : '/wishlist'));
    }
};

import { onMounted } from 'vue';

onMounted(() => {
    // Micro-interaction for following mouse movement slightly on the hero icon
    const iconContainer = document.querySelector('.group.relative.z-10') as HTMLElement;
    if (iconContainer) {
        document.addEventListener('mousemove', (e) => {
            const xAxis = (window.innerWidth / 2 - e.pageX) / 50;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 50;
            iconContainer.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
        });
        
        iconContainer.addEventListener('mouseleave', () => {
            iconContainer.style.transform = `rotateY(0deg) rotateX(0deg)`;
            iconContainer.style.transition = 'all 0.5s ease';
        });

        iconContainer.addEventListener('mouseenter', () => {
            iconContainer.style.transition = 'none';
        });
    }
});
</script>

<template>
    <Head title="Wishlist - Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme bg-surface text-on-surface min-h-screen flex flex-col font-sans antialiased selection:bg-primary/20 selection:text-primary">
        <!-- Top Navigation Bar (Shared Component with Jelita Saung branding & wishlist badge) -->
        <ShopNavbar />

        <!-- Main Content -->
        <main class="flex-grow max-w-[1440px] w-full mx-auto px-4 sm:px-6 lg:px-20 py-12 md:py-16">
            <!-- Header Section -->
            <header class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6 pb-6 border-b border-outline-variant/30">
                <div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-on-surface mb-3">
                        Favorit Anda
                    </h1>
                    <p class="text-base sm:text-lg text-secondary max-w-2xl leading-relaxed">
                        Koleksi pilihan dari hati Rutan Perempuan Kelas II A Bandung dan pengrajin Jelita Saung. Simpan kerajinan favorit Anda untuk dibeli nanti.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 sm:gap-4" v-if="wishlistItems.length > 0">
                    <button 
                        @click="handleShareList" 
                        class="px-6 py-3 rounded-full border-2 border-outline-variant/60 text-sm font-bold text-secondary hover:text-on-surface hover:border-on-surface hover:bg-surface-container/60 active:scale-95 transition-all duration-200 cursor-pointer h-12 flex items-center justify-center whitespace-nowrap"
                    >
                        Bagikan
                    </button>
                    <button 
                        @click="handleAddAllToBag" 
                        class="px-8 py-3 rounded-full bg-primary text-white text-sm font-bold shadow-md hover:opacity-90 hover:shadow-primary/20 active:scale-95 transition-all duration-200 cursor-pointer h-12 flex items-center justify-center whitespace-nowrap"
                    >
                        Masukkan Semua ke Keranjang
                    </button>
                </div>
            </header>

            <!-- Wishlist Grid -->
            <div v-if="wishlistItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 md:gap-8">
                <div 
                    v-for="item in wishlistItems" 
                    :key="item.id"
                    class="product-card group relative bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-xs overflow-hidden transition-all duration-300 hover:shadow-xl hover:border-primary/40 flex flex-col"
                >
                    <!-- Remove Item Button -->
                    <button 
                        @click="removeItem(item.id)" 
                        class="remove-btn absolute top-3 right-3 z-10 p-2 bg-surface-container-lowest/90 backdrop-blur-md rounded-full text-secondary hover:text-error hover:bg-white transition-all duration-200 shadow-sm sm:opacity-0 group-hover:opacity-100 focus:opacity-100 cursor-pointer"
                        :title="'Hapus ' + item.name"
                    >
                        <span class="material-symbols-outlined text-[20px] leading-none">close</span>
                    </button>

                    <!-- Product Image -->
                    <Link :href="'/product/' + item.id" class="aspect-[4/5] overflow-hidden bg-surface-variant block relative">
                        <img 
                            :src="item.images && item.images.length > 0 ? '/storage/' + item.images[0].image_path : 'https://placehold.co/400'" 
                            :alt="item.name"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" 
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </Link>

                    <!-- Product Details -->
                    <div class="p-5 flex-grow flex flex-col justify-between bg-surface-container-lowest">
                        <div>
                            <div class="flex justify-between items-start gap-2 mb-1.5">
                                <Link :href="'/product/' + item.id" class="font-bold text-lg text-on-surface hover:text-primary transition-colors line-clamp-1">
                                    {{ item.name }}
                                </Link>
                                <span class="font-black text-sm text-primary whitespace-nowrap pt-0.5">
                                    {{ formatPrice(item.price) }}
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm text-secondary line-clamp-2 mb-6 leading-relaxed">
                                {{ item.description }}
                            </p>
                        </div>

                        <button 
                            @click="handleAddToBag(item)"
                            class="w-full h-11 bg-surface-container text-on-surface font-bold text-sm rounded-xl hover:bg-primary hover:text-white transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer shadow-2xs"
                        >
                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                            <span>Masukkan Keranjang</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex flex-col items-center justify-center py-xl">
                <div class="max-w-[900px] w-full text-center space-y-lg flex flex-col items-center">
                    <!-- Hero Illustration Area -->
                    <div class="relative w-64 h-64 flex items-center justify-center mb-md">
                        <!-- Decorative Soft Circles -->
                        <div class="absolute inset-0 bg-primary-fixed/20 rounded-full blur-3xl opacity-60"></div>
                        <div class="absolute top-0 right-0 w-32 h-32 bg-tertiary-fixed/20 rounded-full blur-2xl opacity-40 soft-float"></div>
                        <!-- Main Illustration: The Heart -->
                        <div class="relative z-10 bg-surface-container-lowest p-xl rounded-full shadow-[0px_12px_40px_rgba(0,0,0,0.06)] flex items-center justify-center group">
                            <div class="heart-pulse text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined !text-[80px]" data-icon="favorite" style="font-variation-settings: 'FILL' 1;">favorite</span>
                            </div>
                        </div>
                        <!-- Floating Petals/Accents -->
                        <div class="absolute bottom-4 left-4 w-4 h-4 bg-primary-container/30 rounded-full animate-bounce" style="animation-duration: 4s;"></div>
                        <div class="absolute top-10 left-0 w-6 h-6 bg-secondary-container/50 rounded-full soft-float" style="animation-delay: 1s;"></div>
                    </div>
                    <!-- Messaging -->
                    <div class="space-y-sm max-w-[500px] mx-auto px-4">
                        <h1 class="font-headline-md text-headline-md text-on-background">Daftar favorit Anda kosong</h1>
                        <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                            Sepertinya Anda belum menambahkan satupun produk kerajinan ke daftar favorit Anda. Jelajahi karya terbaru kami dan temukan apa yang Anda suka.
                        </p>
                    </div>
                    <!-- CTA Section -->
                    <div class="pt-md flex flex-col sm:flex-row gap-md items-center">
                        <Link class="px-lg py-sm bg-primary text-white font-label-md text-label-md rounded-full shadow-[0px_4px_20px_rgba(0,0,0,0.08)] hover:bg-primary-container hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 inline-flex items-center gap-sm" href="/shop">
                            Mulai Menjelajah
                            <span class="material-symbols-outlined text-sm" data-icon="arrow_forward">arrow_forward</span>
                        </Link>
                        <Link class="px-lg py-sm text-primary font-label-md text-label-md hover:underline underline-offset-8 transition-all" href="/shop">
                            Lihat Kedatangan Baru
                        </Link>
                    </div>
                    <!-- Recommendations Teaser (Subtle) -->
                    <div class="pt-xl w-full" v-if="props.recommendedProducts && props.recommendedProducts.length > 0">
                        <div class="flex items-center justify-between mb-md w-full">
                            <h3 class="font-label-md text-label-md text-on-surface-variant uppercase tracking-widest">Anda mungkin juga suka</h3>
                            <div class="h-px bg-outline-variant flex-grow ml-md"></div>
                        </div>
                        <!-- Small Bento Grid of Previews -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter w-full">
                            <Link v-for="product in props.recommendedProducts" :key="product.id" :href="'/product/' + product.id" class="aspect-square bg-surface-container-low rounded-xl overflow-hidden group cursor-pointer relative shadow-[0px_4px_20px_rgba(0,0,0,0.04)]">
                                <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" :src="product.image" :alt="product.name" />
                                <div class="absolute inset-0 bg-black/5 group-hover:bg-black/0 transition-colors"></div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <ShopFooter />
    </div>
</template>

<style scoped>
/* Ensure Material Symbols render as icons and never fall back to words */
.material-symbols-outlined {
    font-family: 'Material Symbols Outlined';
    font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
    font-feature-settings: 'liga';
    display: inline-block;
    vertical-align: middle;
}

/* Responsive button reveal for product cards */
@media (max-width: 639px) {
    .remove-btn {
        opacity: 1 !important;
    }
}

.heart-pulse {
    animation: pulse 3s infinite ease-in-out;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.05); opacity: 1; }
}
.soft-float {
    animation: float 6s infinite ease-in-out;
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Welcome theme palette encapsulation matching Jelita Saung mockups & preventing grayscale fallback */
.welcome-theme {
    /* Scoped root theme variables for Tailwind v4 bindings */
    --background: #fbf9f8;
    --foreground: #1b1c1c;
    --primary: #8a4853;
    --primary-foreground: #ffffff;
    --secondary: #625d5f;
    --secondary-foreground: #ffffff;
    --border: #d7c1c3;
    --outline: #857374;

    /* Custom expanded color tokens */
    --color-surface: #fbf9f8;
    --color-surface-dim: #dcd9d9;
    --color-surface-container-lowest: #ffffff;
    --color-surface-container-low: #f6f3f2;
    --color-surface-container: #f0eded;
    --color-surface-container-highest: #e4e2e1;
    --color-on-surface: #1b1c1c;
    --color-on-surface-variant: #524345;
    --color-primary: #8a4853;
    --color-on-primary: #ffffff;
    --color-primary-fixed: #ffd9dd;
    --color-on-primary-fixed: #3a0915;
    --color-secondary: #625d5f;
    --color-secondary-fixed-variant: #4a4548;
    --color-secondary-container: #e9e0e3;
    --color-tertiary: #715822;
    --color-tertiary-fixed: #ffdf9f;
    --color-tertiary-fixed-dim: #e3c281;
    --color-outline-variant: #d7c1c3;
    --color-background: #fbf9f8;
    --color-on-background: #1b1c1c;
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

    --color-surface: #141213;
    --color-surface-dim: #1e1a1b;
    --color-surface-container-lowest: #1a1617;
    --color-surface-container-low: #221d1e;
    --color-surface-container: #262122;
    --color-surface-container-highest: #332d2e;
    --color-on-surface: #f3f0f0;
    --color-on-surface-variant: #ccc4c7;
    --color-primary: #ffb2bc;
    --color-on-primary: #5a1b24;
    --color-primary-fixed: #5a1b24;
    --color-on-primary-fixed: #ffb2bc;
    --color-secondary: #ccc4c7;
    --color-secondary-fixed-variant: #e4e2e1;
    --color-secondary-container: #332f31;
    --color-tertiary: #e3c281;
    --color-tertiary-fixed: #5a430e;
    --color-outline-variant: #524345;
    --color-background: #141213;
    --color-on-background: #f3f0f0;
}
</style>
