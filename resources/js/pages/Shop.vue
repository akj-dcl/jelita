<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

const props = defineProps<{
    categories: Array<any>;
    products: any;
    filters?: any;
}>();

// Filtering States
const searchQuery = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || 'All');
const maxPrice = ref(props.filters?.maxPrice ? parseInt(props.filters.maxPrice) : 5000000);
const minRating = ref(props.filters?.minRating ? parseFloat(props.filters.minRating) : 0);

// Reset all filters
const resetFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'All';
    maxPrice.value = 5000000;
    minRating.value = 0;
};

let filterTimeout: any = null;

watch([searchQuery, selectedCategory, maxPrice, minRating], () => {
    if (filterTimeout) clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        const query: any = {};
        if (searchQuery.value) query.search = searchQuery.value;
        if (selectedCategory.value !== 'All') query.category = selectedCategory.value;
        if (maxPrice.value < 5000000) query.maxPrice = maxPrice.value;
        if (minRating.value > 0) query.minRating = minRating.value;

        router.get('/shop', query, { preserveState: true, preserveScroll: true, replace: true });
    }, 500);
}, { deep: true });

// Computed for display - apply rating locally since rating might not be a DB column
const filteredProducts = computed(() => {
    let data = props.products.data || [];
    if (minRating.value > 0) {
        data = data.filter((p: any) => (p.rating || 5.0) >= minRating.value);
    }
    return data;
});

// Format Rp pricing helper
const formatRupiah = (value: number) => {
    return 'Rp ' + Math.round(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};
</script>

<template>
    <Head title="Shop | Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md overflow-x-clip">
        <!-- TopNavBar -->
        <ShopNavbar />

        <!-- Main Content -->
        <main class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop py-10">
            <!-- Header & Search -->
            <header class="mb-16 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="font-display-lg text-[40px] md:text-[48px] text-on-surface mb-1">Karya Pilihan</h1>
                    <p class="text-on-surface-variant text-lg">Jelajahi koleksi kerajinan tangan kami.</p>
                </div>
                <div class="relative w-full md:w-80">
                    <input 
                        v-model="searchQuery"
                        class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg px-6 py-3 focus:outline-none focus:border-primary transition-all pr-10 text-base" 
                        placeholder="Cari produk..." 
                        type="text"
                    />
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-tertiary-container pointer-events-none">
                        search
                    </span>
                </div>
            </header>

            <div class="flex flex-col md:flex-row gap-16">
                <!-- Minimalist Filter Sidebar -->
                <aside class="w-full md:w-64 space-y-10 shrink-0">
                    <!-- Category Filter -->
                    <div>
                        <h3 class="text-sm font-bold text-on-surface uppercase tracking-widest mb-6">Kategori</h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input 
                                    type="radio" 
                                    value="All"
                                    v-model="selectedCategory"
                                    class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"
                                />
                                <span class="text-on-surface-variant group-hover:text-primary transition-colors">Semua Produk</span>
                            </label>
                            <template v-for="category in props.categories" :key="category.id">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input 
                                        type="radio" 
                                        :value="category.id"
                                        v-model="selectedCategory"
                                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"
                                    />
                                    <span class="text-on-surface-variant group-hover:text-primary transition-colors font-bold">{{ category.name }}</span>
                                </label>
                                <label v-for="child in category.children" :key="child.id" class="flex items-center gap-3 cursor-pointer group ml-6">
                                    <input 
                                        type="radio" 
                                        :value="child.id"
                                        v-model="selectedCategory"
                                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"
                                    />
                                    <span class="text-on-surface-variant group-hover:text-primary transition-colors">{{ child.name }}</span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div>
                        <h3 class="text-sm font-bold text-on-surface uppercase tracking-widest mb-6">Rentang Harga</h3>
                        <div class="px-1">
                            <input 
                                v-model.number="maxPrice"
                                class="w-full h-1 bg-surface-variant rounded-lg appearance-none cursor-pointer accent-primary" 
                                max="5000000" 
                                min="0" 
                                step="100000" 
                                type="range"
                            />
                            <div class="flex justify-between mt-3 text-xs text-on-surface-variant font-semibold">
                                <span>Max: {{ formatRupiah(maxPrice) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <h3 class="text-sm font-bold text-on-surface uppercase tracking-widest mb-6">Penilaian</h3>
                        <div class="space-y-3">
                            <button 
                                @click="minRating = 0"
                                class="flex items-center gap-1 group w-full text-left"
                                :class="minRating === 0 ? 'text-primary font-semibold' : 'text-on-surface-variant'"
                            >
                                <span class="text-sm hover:text-primary transition-colors">Semua Penilaian</span>
                            </button>
                            <button 
                                @click="minRating = 4.8"
                                class="flex items-center gap-1 group w-full text-left"
                                :class="minRating === 4.8 ? 'text-primary font-semibold' : 'text-on-surface-variant'"
                            >
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="text-xs ml-3 group-hover:text-primary transition-colors">4.8 &amp; Up</span>
                            </button>
                            <button 
                                @click="minRating = 4.5"
                                class="flex items-center gap-1 group w-full text-left"
                                :class="minRating === 4.5 ? 'text-primary font-semibold' : 'text-on-surface-variant'"
                            >
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                <span class="material-symbols-outlined text-[16px] text-outline/30">star</span>
                                <span class="text-xs ml-3 group-hover:text-primary transition-colors">4.5 &amp; Up</span>
                            </button>
                        </div>
                    </div>

                    <!-- Reset Filters Button -->
                    <button 
                        @click="resetFilters"
                        class="w-full py-3 border border-primary text-primary rounded-lg text-sm font-semibold hover:bg-primary-fixed hover:text-primary transition-all active:scale-95 cursor-pointer"
                    >
                        Reset Filter
                    </button>
                </aside>

                <!-- Product Grid -->
                <div class="flex-grow">
                    <!-- Products count and empty states -->
                    <div class="mb-6 flex justify-between items-center text-sm text-on-surface-variant">
                        <span>Menampilkan {{ products.from || 0 }} - {{ products.to || 0 }} dari {{ products.total || 0 }} produk</span>
                    </div>

                    <div v-if="filteredProducts.length === 0" class="text-center py-20 bg-surface-container-lowest rounded-2xl p-10 shadow-sm border border-outline-variant/30">
                        <span class="material-symbols-outlined text-5xl text-outline mb-4">search_off</span>
                        <h3 class="text-lg font-bold mb-2">Produk tidak ditemukan</h3>
                        <p class="text-on-surface-variant text-sm max-w-sm mx-auto">Coba sesuaikan filter atau pencarian Anda untuk menemukan produk lainnya.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter">
                        <!-- Product Card -->
                        <Link 
                            v-for="product in filteredProducts" 
                            :key="product.id"
                            :href="'/product/' + product.id"
                            class="group bg-surface-container-lowest rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0px_12px_40px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col cursor-pointer block"
                        >
                            <div class="relative aspect-[4/5] overflow-hidden bg-surface-container-low p-2">
                                <img 
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 rounded-lg" 
                                    :src="product.image ? `/storage/${product.image}` : 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image'" 
                                    :alt="product.name"
                                />
                                <div v-if="product.badge" class="absolute top-6 left-6">
                                    <span 
                                        class="font-semibold text-xs px-3 py-1 rounded-full uppercase tracking-tighter"
                                        :class="product.badge === 'New' ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 
                                                product.badge === 'Handmade' ? 'bg-primary text-white' : 
                                                'bg-secondary-fixed text-on-secondary-fixed'"
                                    >
                                        {{ product.badge }}
                                    </span>
                                </div>
                                <button class="absolute bottom-6 right-6 bg-surface-container-lowest/85 backdrop-blur-md p-3 rounded-full opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 hover:bg-primary hover:text-white cursor-pointer active:scale-90">
                                    <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
                                </button>
                            </div>
                            <div class="p-6 flex flex-col gap-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-on-surface leading-tight">{{ product.name }}</h3>
                                    <div class="flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span class="text-xs text-on-surface-variant font-medium">{{ product.rating || '5.0' }}</span>
                                    </div>
                                </div>
                                <p class="text-on-surface-variant text-sm truncate">{{ product.description || 'Produk kerajinan' }}</p>
                                <div class="mt-3">
                                    <span class="text-primary font-bold text-lg">{{ formatRupiah(product.price) }}</span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Elegant Pagination -->
                    <nav v-if="products.links && products.links.length > 3" class="mt-16 flex items-center justify-center gap-2 flex-wrap">
                        <template v-for="(link, index) in products.links" :key="index">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                class="w-10 h-10 flex items-center justify-center rounded-lg text-sm font-semibold transition-colors"
                                :class="link.active ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-variant'"
                                v-html="link.label.replace(/Previous|Next/g, '').trim()"
                                preserve-scroll
                            />
                            <span 
                                v-else 
                                class="w-10 h-10 flex items-center justify-center rounded-lg text-on-surface-variant/50 text-sm font-semibold"
                                v-html="link.label.replace(/Previous|Next/g, '').trim()"
                            ></span>
                        </template>
                    </nav>
                </div>
            </div>
        </main>

        <!-- Footer -->
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
</style>
