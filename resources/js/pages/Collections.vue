<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

const props = defineProps<{
    categories: Array<{
        id: number;
        name: string;
        slug: string;
        image: string | null;
        description: string | null;
        products_count: number;
    }>;
}>();
</script>

<template>
    <Head title="Koleksi | Jelita Saung" />

    <div class="min-h-screen bg-background text-on-background flex flex-col font-body-md overflow-x-clip">
        <ShopNavbar />

        <main class="flex-grow">
            <!-- Hero Section -->
            <section class="bg-surface-variant/30 py-16 md:py-24">
                <div class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop text-center">
                    <h1 class="font-display-lg text-4xl md:text-5xl text-on-background mb-4">Koleksi Kami</h1>
                    <p class="text-on-surface-variant max-w-2xl mx-auto text-lg leading-relaxed font-medium">
                        Jelajahi berbagai kategori produk unggulan kami. Setiap karya dibuat dengan ketelitian dan dedikasi penuh.
                    </p>
                </div>
            </section>

            <!-- Collections Grid -->
            <section class="py-16 md:py-24">
                <div class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop">
                    <div v-if="categories.length === 0" class="text-center py-12 text-secondary">
                        Belum ada kategori koleksi.
                    </div>
                    
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <Link 
                            v-for="category in categories" 
                            :key="category.id"
                            :href="`/shop?category=${category.slug}`"
                            class="group block relative overflow-hidden rounded-2xl bg-surface shadow-sm border border-outline-variant hover:shadow-md transition-all duration-300"
                        >
                            <div class="aspect-[4/3] w-full overflow-hidden bg-surface-variant">
                                <img 
                                    v-if="category.image" 
                                    :src="`/storage/${category.image}`" 
                                    :alt="category.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-primary/5">
                                    <span class="material-symbols-outlined text-5xl text-primary/30" style="font-variation-settings: 'FILL' 1;">category</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="font-display-md text-2xl font-semibold mb-2 group-hover:text-primary transition-colors">{{ category.name }}</h3>
                                <p v-if="category.description" class="text-secondary text-sm line-clamp-2 mb-4">{{ category.description }}</p>
                                <div class="flex items-center justify-between mt-auto">
                                    <span class="text-sm font-medium bg-surface-variant px-3 py-1 rounded-full">
                                        {{ category.products_count }} Produk
                                    </span>
                                    <span class="text-primary font-medium flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                        Lihat Koleksi <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </span>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <ShopFooter />
    </div>
</template>
