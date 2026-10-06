<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { login, register, myProfile } from '@/routes';
import { onMounted, onUnmounted, ref } from 'vue';

const page = usePage();

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

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
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
            
            <div class="flex items-center gap-4 sm:gap-6">
                <Link :href="'/wishlist'" class="p-2 transition-all active:scale-95 cursor-pointer flex items-center justify-center" :class="page.component === 'Wishlist' ? 'text-primary' : 'text-on-surface-variant hover:text-primary'" title="Favorit">
                    <span class="material-symbols-outlined" :style="page.component === 'Wishlist' ? 'font-variation-settings: \'FILL\' 1;' : ''">favorite</span>
                </Link>
                <Link :href="'/cart'" class="p-2 transition-all active:scale-95 cursor-pointer flex items-center justify-center" :class="page.component === 'Cart' ? 'text-primary' : 'text-on-surface-variant hover:text-primary'" title="Keranjang">
                    <span class="material-symbols-outlined" :style="page.component === 'Cart' ? 'font-variation-settings: \'FILL\' 1;' : ''">shopping_bag</span>
                </Link>
                
                <!-- Authenticated State -->
                <template v-if="$page.props.auth.user">
                    <Link :href="myProfile()" class="p-2 text-on-surface-variant hover:text-primary transition-all active:scale-95" :class="page.component === 'MyProfile' ? 'text-primary' : 'text-on-surface-variant hover:text-primary'" title="Profil Saya">
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
                    <Link :href="login()" class="md:hidden p-2 text-on-surface-variant hover:text-primary transition-all active:scale-95" title="Masuk">
                        <span class="material-symbols-outlined">person</span>
                    </Link>
                </template>
                
                <button class="md:hidden p-2 text-on-surface-variant active:scale-95 cursor-pointer" @click="isMobileMenuOpen = true">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu Drawer -->
    <div v-if="isMobileMenuOpen" class="fixed inset-0 z-50 flex md:hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="isMobileMenuOpen = false"></div>
        <div class="relative flex flex-col w-full max-w-xs p-6 bg-background shadow-xl ml-auto h-full animate-in slide-in-from-right duration-300">
            <div class="flex items-center justify-between mb-8">
                <span class="font-display-lg text-lg font-bold text-primary">Menu</span>
                <button class="p-2 text-on-surface-variant active:scale-95 cursor-pointer" @click="isMobileMenuOpen = false">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <nav class="flex flex-col gap-6 text-lg">
                <Link :href="'/shop'" class="text-on-surface-variant hover:text-primary transition-colors font-label-md" @click="isMobileMenuOpen = false">Toko</Link>
                <Link :href="'/wishlist'" class="text-on-surface-variant hover:text-primary transition-colors font-label-md flex items-center gap-2" @click="isMobileMenuOpen = false">
                    <span>Favorit</span>
                </Link>
                <Link href="/collections" class="text-on-surface-variant hover:text-primary transition-colors font-label-md" @click="isMobileMenuOpen = false" :class="{'text-primary': page.component === 'Collections'}">Koleksi</Link>
                <Link href="/story" class="text-on-surface-variant hover:text-primary transition-colors font-label-md" @click="isMobileMenuOpen = false">Cerita</Link>
                <Link href="/about" class="text-on-surface-variant hover:text-primary transition-colors font-label-md" @click="isMobileMenuOpen = false">Tentang</Link>
                <hr class="border-outline-variant/30 my-2" />
                <template v-if="$page.props.auth.user">
                    <Link :href="myProfile()" class="text-primary font-medium flex items-center gap-2" @click="isMobileMenuOpen = false">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Profil Saya
                    </Link>
                </template>
                <template v-else>
                    <Link :href="login()" class="text-on-surface-variant hover:text-primary transition-colors font-label-md" @click="isMobileMenuOpen = false">
                        Masuk
                    </Link>
                    <Link :href="register()" class="bg-dusty-rose text-white text-center px-4 py-2 rounded-full font-label-md hover:opacity-90 transition-all active:scale-95" @click="isMobileMenuOpen = false">
                        Daftar
                    </Link>
                </template>
            </nav>
        </div>
    </div>
</template>
