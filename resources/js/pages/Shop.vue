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
const getInitialCategory = () => {
    const raw = props.filters?.category;
    if (!raw || raw === 'All') return 'All';
    for (const cat of props.categories || []) {
        if (cat.id == raw || cat.slug === raw) return cat.slug;
        if (cat.children) {
            for (const child of cat.children) {
                if (child.id == raw || child.slug === raw) return child.slug;
            }
        }
    }
    return raw;
};
const selectedCategory = ref(getInitialCategory());
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

// Mobile Filter state
const showMobileFilter = ref(false);

const activeFilterCount = computed(() => {
    let count = 0;
    if (selectedCategory.value !== 'All') count++;
    if (maxPrice.value < 5000000) count++;
    if (minRating.value > 0) count++;
    return count;
});

const hasActiveFilters = computed(() => activeFilterCount.value > 0);
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
        <main class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop py-6 md:py-10">
            <!-- Header & Search -->
            <header class="mb-6 md:mb-16 flex flex-col md:flex-row md:items-end justify-between gap-4 md:gap-6">
                <div>
                    <h1 class="font-display-lg text-2xl sm:text-3xl md:text-[48px] text-on-surface mb-1">Karya Pilihan</h1>
                    <p class="text-on-surface-variant text-sm md:text-lg">Jelajahi koleksi kerajinan tangan kami.</p>
                </div>
                <div class="relative w-full md:w-80">
                    <input 
                        v-model="searchQuery"
                        class="w-full bg-surface-container-lowest border border-outline-variant rounded-xl px-4 sm:px-6 py-2.5 sm:py-3 focus:outline-none focus:border-primary transition-all pr-10 text-sm sm:text-base shadow-xs" 
                        placeholder="Cari produk..." 
                        type="text"
                    />
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-tertiary-container pointer-events-none text-xl">
                        search
                    </span>
                </div>
            </header>

            <!-- Mobile Quick Category Bar & Filter Trigger (< md) -->
            <div class="md:hidden mb-6 flex items-center gap-2">
                <!-- Mobile Filter Trigger Button -->
                <button 
                    @click="showMobileFilter = true"
                    class="shrink-0 flex items-center gap-1.5 px-3.5 py-2 rounded-xl border text-xs font-semibold transition-all cursor-pointer shadow-xs active:scale-95"
                    :class="hasActiveFilters ? 'bg-primary text-white border-primary' : 'bg-surface-container-lowest text-on-surface border-outline-variant/60'"
                >
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Filter</span>
                    <span v-if="activeFilterCount > 0" class="w-4 h-4 rounded-full bg-white text-primary text-[10px] flex items-center justify-center font-bold">
                        {{ activeFilterCount }}
                    </span>
                </button>
                
                <!-- Quick Category Horizontal Pills -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                    <button 
                        @click="selectedCategory = 'All'"
                        class="shrink-0 px-3.5 py-1.5 rounded-full text-xs transition-all cursor-pointer whitespace-nowrap active:scale-95"
                        :class="selectedCategory === 'All' ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-variant'"
                    >
                        Semua
                    </button>
                    <button 
                        v-for="category in props.categories" 
                        :key="category.id"
                        @click="selectedCategory = category.slug"
                        class="shrink-0 px-3.5 py-1.5 rounded-full text-xs transition-all cursor-pointer whitespace-nowrap active:scale-95"
                        :class="selectedCategory === category.slug ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface-container-low text-on-surface-variant hover:bg-surface-variant'"
                    >
                        {{ category.name }}
                    </button>
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6 md:gap-16">
                <!-- Desktop Filter Sidebar (hidden on mobile) -->
                <aside class="hidden md:block w-64 space-y-10 shrink-0 sticky top-24 self-start">
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
                                        :value="category.slug"
                                        v-model="selectedCategory"
                                        class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary cursor-pointer"
                                    />
                                    <span class="text-on-surface-variant group-hover:text-primary transition-colors font-bold">{{ category.name }}</span>
                                </label>
                                <label v-for="child in category.children" :key="child.id" class="flex items-center gap-3 cursor-pointer group ml-6">
                                    <input 
                                        type="radio" 
                                        :value="child.slug"
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
                                class="flex items-center gap-1 group w-full text-left cursor-pointer"
                                :class="minRating === 0 ? 'text-primary font-semibold' : 'text-on-surface-variant'"
                            >
                                <span class="text-sm hover:text-primary transition-colors">Semua Penilaian</span>
                            </button>
                            <button 
                                @click="minRating = 4.8"
                                class="flex items-center gap-1 group w-full text-left cursor-pointer"
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
                                class="flex items-center gap-1 group w-full text-left cursor-pointer"
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
                        class="w-full py-3 border border-primary text-primary rounded-xl text-sm font-semibold hover:bg-primary-fixed hover:text-primary transition-all active:scale-95 cursor-pointer shadow-xs"
                    >
                        Reset Filter
                    </button>
                </aside>

                <!-- Product Grid -->
                <div class="flex-grow">
                    <!-- Products count and filter summary -->
                    <div class="mb-4 sm:mb-6 flex justify-between items-center text-xs sm:text-sm text-on-surface-variant">
                        <span>Menampilkan {{ products.from || 0 }} - {{ products.to || 0 }} dari {{ products.total || 0 }} produk</span>
                        <button 
                            v-if="hasActiveFilters" 
                            @click="resetFilters" 
                            class="md:hidden text-xs text-primary font-semibold hover:underline"
                        >
                            Reset Filter
                        </button>
                    </div>

                    <div v-if="filteredProducts.length === 0" class="text-center py-16 sm:py-20 bg-surface-container-lowest rounded-2xl p-6 sm:p-10 shadow-sm border border-outline-variant/30">
                        <span class="material-symbols-outlined text-4xl sm:text-5xl text-outline mb-3 sm:mb-4">search_off</span>
                        <h3 class="text-base sm:text-lg font-bold mb-2">Produk tidak ditemukan</h3>
                        <p class="text-on-surface-variant text-xs sm:text-sm max-w-sm mx-auto">Coba sesuaikan filter atau kata kunci pencarian Anda untuk menemukan produk lainnya.</p>
                        <button @click="resetFilters" class="mt-4 px-4 py-2 bg-primary text-white text-xs font-semibold rounded-lg hover:opacity-90">
                            Hapus Filter
                        </button>
                    </div>

                    <!-- Responsive 2-column mobile / 3-column desktop grid -->
                    <div v-else class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
                        <!-- Product Card -->
                        <Link 
                            v-for="product in filteredProducts" 
                            :key="product.id"
                            :href="'/product/' + product.id"
                            class="group bg-surface-container-lowest rounded-xl sm:rounded-2xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0px_12px_40px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col cursor-pointer block border border-outline-variant/15"
                        >
                            <div class="relative aspect-square sm:aspect-[4/5] overflow-hidden bg-surface-container-low p-1.5 sm:p-2">
                                <img 
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105 rounded-lg" 
                                    :src="product.image ? `/storage/${product.image}` : 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image'" 
                                    :alt="product.name"
                                />
                                <div v-if="product.badge" class="absolute top-2.5 left-2.5 sm:top-4 sm:left-4">
                                    <span 
                                        class="font-semibold text-[10px] sm:text-xs px-2 py-0.5 sm:px-3 sm:py-1 rounded-full uppercase tracking-tight shadow-xs"
                                        :class="product.badge === 'New' ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 
                                                product.badge === 'Handmade' ? 'bg-primary text-white' : 
                                                'bg-secondary-fixed text-on-secondary-fixed'"
                                    >
                                        {{ product.badge }}
                                    </span>
                                </div>
                                <button class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-surface-container-lowest/90 backdrop-blur-xs p-1.5 sm:p-2.5 rounded-full opacity-90 sm:opacity-0 sm:group-hover:opacity-100 transition-all duration-300 hover:bg-primary hover:text-white cursor-pointer active:scale-90 shadow-sm">
                                    <span class="material-symbols-outlined text-[18px] sm:text-xl">add_shopping_cart</span>
                                </button>
                            </div>
                            <div class="p-3 sm:p-5 flex flex-col gap-1 flex-grow justify-between">
                                <div>
                                    <div class="flex justify-between items-start gap-1">
                                        <h3 class="text-xs sm:text-base font-bold text-on-surface leading-snug line-clamp-2">{{ product.name }}</h3>
                                        <div class="flex items-center gap-0.5 shrink-0">
                                            <span class="material-symbols-outlined text-[14px] sm:text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span class="text-[11px] sm:text-xs text-on-surface-variant font-medium">{{ product.rating || '5.0' }}</span>
                                        </div>
                                    </div>
                                    <p class="text-on-surface-variant text-[11px] sm:text-sm truncate mt-0.5 hidden sm:block">{{ product.description || 'Produk kerajinan' }}</p>
                                </div>
                                <div class="mt-2 pt-1.5 border-t border-outline-variant/15 flex items-center justify-between">
                                    <span class="text-primary font-bold text-xs sm:text-base">{{ formatRupiah(product.price) }}</span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Elegant Pagination -->
                    <nav v-if="products.links && products.links.length > 3" class="mt-10 sm:mt-16 flex items-center justify-center gap-1.5 sm:gap-2 flex-wrap">
                        <template v-for="(link, index) in products.links" :key="index">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg text-xs sm:text-sm font-semibold transition-colors"
                                :class="link.active ? 'bg-primary text-white shadow-xs' : 'text-on-surface-variant hover:bg-surface-variant'"
                                v-html="link.label.replace(/Previous|Next/g, '').trim()"
                                preserve-scroll
                            />
                            <span 
                                v-else 
                                class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-lg text-on-surface-variant/40 text-xs sm:text-sm font-semibold"
                                v-html="link.label.replace(/Previous|Next/g, '').trim()"
                            ></span>
                        </template>
                    </nav>
                </div>
            </div>
        </main>

        <!-- Mobile Filter Drawer Modal -->
        <Teleport to="body">
            <div v-if="showMobileFilter" class="fixed inset-0 z-[100] flex md:hidden">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-xs" @click="showMobileFilter = false"></div>
                <div class="relative w-full max-w-xs ml-auto h-full bg-background p-5 shadow-2xl flex flex-col justify-between overflow-y-auto animate-in slide-in-from-right duration-300">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-outline-variant/30 mb-5">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">tune</span>
                                <h3 class="font-bold text-base text-on-surface">Filter Produk</h3>
                            </div>
                            <button @click="showMobileFilter = false" class="p-1 rounded-full text-on-surface-variant hover:bg-surface-container-low cursor-pointer">
                                <span class="material-symbols-outlined text-xl">close</span>
                            </button>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-6">
                            <h4 class="text-xs font-bold text-on-surface uppercase tracking-widest mb-3">Kategori</h4>
                            <div class="space-y-2.5">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" value="All" v-model="selectedCategory" class="w-4 h-4 text-primary focus:ring-primary cursor-pointer" />
                                    <span class="text-sm text-on-surface-variant">Semua Produk</span>
                                </label>
                                <template v-for="category in props.categories" :key="category.id">
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" :value="category.slug" v-model="selectedCategory" class="w-4 h-4 text-primary focus:ring-primary cursor-pointer" />
                                        <span class="text-sm font-semibold text-on-surface">{{ category.name }}</span>
                                    </label>
                                    <label v-for="child in category.children" :key="child.id" class="flex items-center gap-3 cursor-pointer ml-6">
                                        <input type="radio" :value="child.slug" v-model="selectedCategory" class="w-4 h-4 text-primary focus:ring-primary cursor-pointer" />
                                        <span class="text-xs text-on-surface-variant">{{ child.name }}</span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="mb-6">
                            <h4 class="text-xs font-bold text-on-surface uppercase tracking-widest mb-3">Rentang Harga</h4>
                            <input v-model.number="maxPrice" class="w-full h-1 bg-surface-variant rounded-lg appearance-none cursor-pointer accent-primary" max="5000000" min="0" step="100000" type="range" />
                            <div class="flex justify-between mt-2 text-xs text-on-surface-variant font-semibold">
                                <span>Max: {{ formatRupiah(maxPrice) }}</span>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-6">
                            <h4 class="text-xs font-bold text-on-surface uppercase tracking-widest mb-3">Penilaian</h4>
                            <div class="space-y-2">
                                <button @click="minRating = 0" class="text-sm block w-full text-left py-1 cursor-pointer" :class="minRating === 0 ? 'text-primary font-bold' : 'text-on-surface-variant'">Semua Penilaian</button>
                                <button @click="minRating = 4.8" class="text-sm flex items-center gap-1 w-full text-left py-1 cursor-pointer" :class="minRating === 4.8 ? 'text-primary font-bold' : 'text-on-surface-variant'">
                                    <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span>4.8 &amp; Up</span>
                                </button>
                                <button @click="minRating = 4.5" class="text-sm flex items-center gap-1 w-full text-left py-1 cursor-pointer" :class="minRating === 4.5 ? 'text-primary font-bold' : 'text-on-surface-variant'">
                                    <span class="material-symbols-outlined text-[16px] text-gold" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span>4.5 &amp; Up</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="pt-4 border-t border-outline-variant/30 flex gap-2">
                        <button @click="resetFilters" class="flex-1 py-2.5 border border-primary text-primary rounded-xl text-xs font-semibold cursor-pointer active:scale-95">
                            Reset
                        </button>
                        <button @click="showMobileFilter = false" class="flex-1 py-2.5 bg-primary text-white rounded-xl text-xs font-semibold shadow-xs cursor-pointer active:scale-95">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Footer -->
        <ShopFooter />
    </div>
</template>

