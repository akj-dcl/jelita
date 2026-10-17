<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { login, register, myProfile } from '@/routes';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const { resolvedAppearance, updateAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};

// Mobile menu state
const isMobileMenuOpen = ref(false);

// Scroll reveal header behavior
const isHeaderVisible = ref(true);
let lastScrollTop = 0;

const handleScroll = () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (scrollTop > lastScrollTop && scrollTop > 100) {
        isHeaderVisible.value = false;
    } else {
        isHeaderVisible.value = true;
    }
    lastScrollTop = scrollTop;
};

// Prevent background scroll when mobile drawer is open
watch(isMobileMenuOpen, (open) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = open ? 'hidden' : '';
    }
});

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    if (typeof document !== 'undefined') {
        document.body.style.overflow = '';
    }
});
</script>

<template>
    <!-- TopNavBar -->
    <header 
        class="sticky top-0 z-50 bg-surface/90 backdrop-blur-md transition-transform duration-300 shadow-[0px_4px_20px_rgba(0,0,0,0.04)]"
        :class="{ 'translate-y-0': isHeaderVisible, '-translate-y-full': !isHeaderVisible }"
    >
        <nav class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-2 max-w-[1440px] mx-auto h-20">
            <Link :href="'/'" class="font-display-lg font-bold tracking-tight text-primary text-xl md:text-2xl flex items-center gap-1.5">
                <span>Jelita Saung</span>
                <span class="bg-primary text-white px-2.5 py-0.5 rounded-lg text-base md:text-lg font-bold tracking-normal shadow-sm">ONE</span>
            </Link>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-10">
                <Link 
                    :href="'/shop'" 
                    class="font-label-md text-label-md transition-colors duration-300 pb-1"
                    :class="page.component === 'Shop' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary'"
                >
                    Toko
                </Link>
                <Link 
                    href="/collections" 
                    class="font-label-md text-label-md transition-colors duration-300 pb-1"
                    :class="page.component === 'Collections' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary'"
                >
                    Koleksi
                </Link>
                <Link 
                    href="/story" 
                    class="font-label-md text-label-md transition-colors duration-300 pb-1"
                    :class="page.component === 'Story' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary'"
                >
                    Cerita
                </Link>
                <Link 
                    href="/about" 
                    class="font-label-md text-label-md transition-colors duration-300 pb-1"
                    :class="page.component === 'About' ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary'"
                >
                    Tentang
                </Link>
            </div>
            
            <div class="flex items-center gap-1.5 sm:gap-4">
                <!-- Dark Mode Toggle Button -->
                <button 
                    type="button"
                    @click="toggleTheme" 
                    class="w-10 h-10 rounded-full transition-all active:scale-95 cursor-pointer flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container-high" 
                    :title="resolvedAppearance === 'dark' ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'"
                    aria-label="Toggle Dark Mode"
                >
                    <span class="material-symbols-outlined transition-transform duration-300 hover:rotate-12 text-[22px]">
                        {{ resolvedAppearance === 'dark' ? 'light_mode' : 'dark_mode' }}
                    </span>
                </button>

                <Link :href="'/wishlist'" class="w-10 h-10 rounded-full transition-all active:scale-95 cursor-pointer flex items-center justify-center hover:bg-surface-container-high" :class="page.component === 'Wishlist' ? 'text-primary' : 'text-on-surface-variant hover:text-primary'" title="Favorit">
                    <span class="material-symbols-outlined" :style="page.component === 'Wishlist' ? 'font-variation-settings: \'FILL\' 1;' : ''">favorite</span>
                </Link>
                <Link :href="'/cart'" class="w-10 h-10 rounded-full transition-all active:scale-95 cursor-pointer flex items-center justify-center hover:bg-surface-container-high" :class="page.component === 'Cart' ? 'text-primary' : 'text-on-surface-variant hover:text-primary'" title="Keranjang">
                    <span class="material-symbols-outlined" :style="page.component === 'Cart' ? 'font-variation-settings: \'FILL\' 1;' : ''">shopping_bag</span>
                </Link>
                
                <!-- Authenticated State -->
                <template v-if="$page.props.auth.user">
                    <Link :href="myProfile()" class="w-10 h-10 rounded-full text-on-surface-variant hover:text-primary hover:bg-surface-container-high transition-all active:scale-95 flex items-center justify-center" :class="page.component === 'MyProfile' ? 'text-primary' : 'text-on-surface-variant hover:text-primary'" title="Profil Saya">
                        <span class="material-symbols-outlined" :style="page.component === 'MyProfile' ? 'font-variation-settings: \'FILL\' 1;' : ''">person</span>
                    </Link>
                </template>
                <!-- Guest State -->
                <template v-else>
                    <div class="hidden md:flex items-center gap-6">
                        <Link :href="login()" class="text-on-surface-variant hover:text-primary font-label-md text-label-md transition-colors duration-300">
                            Masuk
                        </Link>
                        <Link :href="register()" class="bg-dusty-rose text-white px-10 py-3 rounded-full font-label-md text-label-md hover:opacity-90 transition-all active:scale-95">
                            Daftar
                        </Link>
                    </div>
                    <Link :href="login()" class="md:hidden w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container-high transition-all active:scale-95" title="Masuk">
                        <span class="material-symbols-outlined">person</span>
                    </Link>
                </template>
                
                <button 
                    class="md:hidden w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container-high active:scale-95 cursor-pointer transition-colors" 
                    @click="isMobileMenuOpen = true"
                    aria-label="Buka Menu"
                >
                    <span class="material-symbols-outlined text-[26px]">menu</span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu Drawer with Smooth Transition -->
    <Transition name="drawer">
        <div v-if="isMobileMenuOpen" class="fixed inset-0 z-50 md:hidden" role="dialog" aria-modal="true" aria-label="Menu Navigasi Mobile">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm drawer-backdrop" @click="isMobileMenuOpen = false"></div>
            
            <!-- Drawer Panel -->
            <div class="fixed top-0 right-0 bottom-0 w-80 max-w-[85vw] p-6 bg-background shadow-2xl h-full drawer-panel overflow-y-auto z-10 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-outline-variant/20">
                        <Link :href="'/'" class="font-display-lg font-bold tracking-tight text-primary text-lg flex items-center gap-1.5" @click="isMobileMenuOpen = false">
                            <span>Jelita Saung</span>
                            <span class="bg-primary text-white px-2 py-0.5 rounded text-xs font-bold tracking-normal">ONE</span>
                        </Link>
                        <button 
                            class="w-10 h-10 rounded-full flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-surface-container-high active:scale-95 cursor-pointer transition-colors" 
                            @click="isMobileMenuOpen = false"
                            aria-label="Tutup Menu"
                        >
                            <span class="material-symbols-outlined text-[24px]">close</span>
                        </button>
                    </div>
                    
                    <nav class="flex flex-col gap-1.5 text-base">
                        <Link 
                            :href="'/shop'" 
                            class="px-3.5 py-3 rounded-xl font-label-md transition-all flex items-center justify-between" 
                            :class="page.component === 'Shop' ? 'text-primary bg-primary/10 font-bold' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>Toko</span>
                            <span class="material-symbols-outlined text-sm opacity-60">chevron_right</span>
                        </Link>
                        <Link 
                            href="/collections" 
                            class="px-3.5 py-3 rounded-xl font-label-md transition-all flex items-center justify-between" 
                            :class="page.component === 'Collections' ? 'text-primary bg-primary/10 font-bold' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>Koleksi</span>
                            <span class="material-symbols-outlined text-sm opacity-60">chevron_right</span>
                        </Link>
                        <Link 
                            href="/story" 
                            class="px-3.5 py-3 rounded-xl font-label-md transition-all flex items-center justify-between" 
                            :class="page.component === 'Story' ? 'text-primary bg-primary/10 font-bold' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>Cerita</span>
                            <span class="material-symbols-outlined text-sm opacity-60">chevron_right</span>
                        </Link>
                        <Link 
                            href="/about" 
                            class="px-3.5 py-3 rounded-xl font-label-md transition-all flex items-center justify-between" 
                            :class="page.component === 'About' ? 'text-primary bg-primary/10 font-bold' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'"
                            @click="isMobileMenuOpen = false"
                        >
                            <span>Tentang</span>
                            <span class="material-symbols-outlined text-sm opacity-60">chevron_right</span>
                        </Link>

                        <hr class="border-outline-variant/20 my-2" />

                        <Link 
                            :href="'/wishlist'" 
                            class="px-3.5 py-3 rounded-xl font-label-md transition-all flex items-center justify-between" 
                            :class="page.component === 'Wishlist' ? 'text-primary bg-primary/10 font-bold' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'"
                            @click="isMobileMenuOpen = false"
                        >
                            <span class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[20px]" :style="page.component === 'Wishlist' ? 'font-variation-settings: \'FILL\' 1;' : ''">favorite</span>
                                <span>Favorit</span>
                            </span>
                            <span class="material-symbols-outlined text-sm opacity-60">chevron_right</span>
                        </Link>
                        <Link 
                            :href="'/cart'" 
                            class="px-3.5 py-3 rounded-xl font-label-md transition-all flex items-center justify-between" 
                            :class="page.component === 'Cart' ? 'text-primary bg-primary/10 font-bold' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-low'"
                            @click="isMobileMenuOpen = false"
                        >
                            <span class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-[20px]" :style="page.component === 'Cart' ? 'font-variation-settings: \'FILL\' 1;' : ''">shopping_bag</span>
                                <span>Keranjang</span>
                            </span>
                            <span class="material-symbols-outlined text-sm opacity-60">chevron_right</span>
                        </Link>
                    </nav>
                </div>

                <div class="pt-4 border-t border-outline-variant/20 space-y-3">
                    <!-- Theme Toggle Switch Row -->
                    <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-surface-container-low transition-colors">
                        <span class="flex items-center gap-2.5 text-sm font-medium text-on-surface">
                            <span class="material-symbols-outlined text-[20px] text-primary">
                                {{ resolvedAppearance === 'dark' ? 'dark_mode' : 'light_mode' }}
                            </span>
                            <span>Mode {{ resolvedAppearance === 'dark' ? 'Gelap' : 'Terang' }}</span>
                        </span>
                        <button 
                            type="button" 
                            @click="toggleTheme"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none cursor-pointer"
                            :class="resolvedAppearance === 'dark' ? 'bg-primary' : 'bg-outline-variant/60'"
                            aria-label="Ubah Tema"
                        >
                            <span 
                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform duration-200 ease-in-out shadow-xs"
                                :class="resolvedAppearance === 'dark' ? 'translate-x-6' : 'translate-x-1'"
                            />
                        </button>
                    </div>

                    <template v-if="$page.props.auth.user">
                        <Link 
                            :href="myProfile()" 
                            class="px-3.5 py-3 rounded-xl font-label-md text-primary font-medium flex items-center gap-3 hover:bg-primary/5 transition-all" 
                            :class="page.component === 'MyProfile' ? 'bg-primary/10 font-bold' : ''"
                            @click="isMobileMenuOpen = false"
                        >
                            <span class="material-symbols-outlined text-[20px]" :style="page.component === 'MyProfile' ? 'font-variation-settings: \'FILL\' 1;' : ''">person</span>
                            <span>Profil Saya</span>
                        </Link>
                    </template>
                    <template v-else>
                        <div class="flex flex-col gap-2.5">
                            <Link 
                                :href="login()" 
                                class="w-full text-center py-2.5 rounded-full border border-dusty-rose text-dusty-rose font-label-md hover:bg-dusty-rose/5 transition-all active:scale-95" 
                                @click="isMobileMenuOpen = false"
                            >
                                Masuk
                            </Link>
                            <Link 
                                :href="register()" 
                                class="bg-dusty-rose text-white text-center px-4 py-2.5 rounded-full font-label-md hover:opacity-90 transition-all active:scale-95 shadow-sm" 
                                @click="isMobileMenuOpen = false"
                            >
                                Daftar
                            </Link>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.drawer-enter-active,
.drawer-leave-active {
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.drawer-enter-active .drawer-panel,
.drawer-leave-active .drawer-panel {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
}

.drawer-enter-from .drawer-panel,
.drawer-leave-to .drawer-panel {
    transform: translateX(100%);
}

.drawer-enter-to .drawer-panel,
.drawer-leave-from .drawer-panel {
    transform: translateX(0);
}
</style>

