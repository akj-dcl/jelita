<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: null, // Ensure standalone full-screen layout without default auth card wrappers
});

withDefaults(
    defineProps<{
        status?: string;
        canResetPassword?: boolean;
    }>(),
    {
        canResetPassword: true,
    },
);

const showPassword = ref(false);
</script>

<template>
    <Head title="Login - Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme bg-surface text-on-surface font-sans min-h-screen flex flex-col overflow-x-clip antialiased selection:bg-primary/20 selection:text-primary">
        <!-- Background Layer with Cinematic Imagery -->
        <div class="fixed inset-0 z-0">
            <!-- Stable high-res Unsplash artisanal weaving textile photo -->
            <div 
                class="w-full h-full bg-cover bg-center scale-105 transition-transform duration-1000" 
                style="background-image: url('https://images.unsplash.com/photo-1584992236310-6edddc085ffb?auto=format&fit=crop&q=80&w=2000')"
            ></div>
            <div class="absolute inset-0 bg-surface/50 md:bg-surface/40 backdrop-blur-[3px]"></div>
        </div>

        <!-- Top Navigation -->
        <header class="relative z-10 w-full px-6 md:px-16 lg:px-20 py-6 max-w-[1440px] mx-auto flex flex-row justify-between items-center h-20">
            <Link href="/" class="flex flex-row items-center gap-3 hover:opacity-95 transition-opacity group">
                <div class="flex flex-row items-center gap-2">
                    <span class="font-extrabold text-2xl tracking-tight text-on-surface whitespace-nowrap">Jelita Saung</span>
                    <span class="bg-primary text-white text-xs px-2.5 py-0.5 rounded tracking-widest uppercase font-black shadow-2xs">ONE</span>
                </div>
                <span class="hidden md:inline-block text-secondary-fixed-variant font-semibold text-base border-l-2 border-outline-variant/60 pl-3 whitespace-nowrap">
                    Jelita Saung
                </span>
            </Link>
            
            <Link href="/" class="text-sm font-bold text-secondary hover:text-primary transition-colors flex flex-row items-center gap-1.5 py-2 px-4 rounded-full hover:bg-surface-container/50 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Beranda</span>
            </Link>
        </header>

        <!-- Main Content Canvas -->
        <main class="relative z-10 flex-grow flex flex-col items-center justify-center px-4 sm:px-6 py-10 md:py-16 w-full">
            <div class="w-[92%] sm:w-[460px] max-w-[460px] mx-auto glass-effect rounded-3xl shadow-[0px_16px_48px_rgba(138,72,83,0.12)] p-6 sm:p-10 border border-outline-variant/40 transition-all">
                <div class="text-center mb-8">
                    <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary mx-auto mb-4">
                        <span class="material-symbols-outlined text-[24px]">lock_open</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-on-surface mb-1.5 tracking-tight">Selamat Datang Kembali</h2>
                    <p class="text-sm text-secondary">Masuk untuk melanjutkan perjalanan kerajinan Anda dengan Jelita Saung</p>
                </div>

                <div v-if="status" class="mb-6 p-3.5 bg-green-500/10 border border-green-500/20 text-center text-sm font-semibold text-green-700 dark:text-green-400 rounded-xl">
                    {{ status }}
                </div>

                <!-- Login Form (Integrated with Inertia Form route & validation) -->
                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="space-y-6"
                >
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant block" for="email">
                            Alamat Email
                        </label>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-secondary/60 text-[20px] pointer-events-none">mail</span>
                            <input 
                                id="email" 
                                name="email" 
                                type="email"
                                required 
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="name@example.com" 
                                class="w-full pl-12 pr-4 py-3.5 bg-surface-container-lowest/80 border border-outline-variant rounded-xl text-sm font-medium text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 placeholder:text-secondary/50 outline-none" 
                            />
                        </div>
                        <InputError :message="errors?.email" />
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant block" for="password">
                                Kata Sandi
                            </label>
                            <Link
                                v-if="canResetPassword"
                                :href="request()"
                                class="text-xs font-bold text-primary hover:underline underline-offset-4 transition-colors"
                                :tabindex="5"
                            >
                                Lupa Kata Sandi?
                            </Link>
                        </div>
                        <div class="relative flex items-center">
                            <span class="material-symbols-outlined absolute left-4 text-secondary/60 text-[20px] pointer-events-none">lock</span>
                            <input 
                                id="password" 
                                name="password" 
                                :type="showPassword ? 'text' : 'password'"
                                required 
                                :tabindex="2"
                                autocomplete="current-password"
                                placeholder="••••••••" 
                                class="w-full pl-12 pr-12 py-3.5 bg-surface-container-lowest/80 border border-outline-variant rounded-xl text-sm font-medium text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 placeholder:text-secondary/50 outline-none" 
                            />
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3.5 p-1.5 text-secondary/60 hover:text-primary transition-colors cursor-pointer rounded-lg hover:bg-surface-container/50"
                                :title="showPassword ? 'Hide password' : 'Show password'"
                            >
                                <span class="material-symbols-outlined text-[20px]">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
                            </button>
                        </div>
                        <InputError :message="errors?.password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-3 pt-1">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            :tabindex="3"
                            class="w-4 h-4 rounded text-primary border-outline-variant focus:ring-2 focus:ring-primary/20 cursor-pointer" 
                        />
                        <label class="text-sm font-medium text-secondary select-none cursor-pointer" for="remember">
                            Ingat saya selama 30 hari
                        </label>
                    </div>

                    <!-- Action Button -->
                    <button 
                        type="submit" 
                        :tabindex="4"
                        :disabled="processing"
                        class="w-full bg-primary text-white font-bold text-sm py-4 rounded-xl shadow-md hover:opacity-95 hover:shadow-primary/25 active:scale-[0.98] transition-all duration-300 flex justify-center items-center gap-2 group cursor-pointer disabled:opacity-50 disabled:pointer-events-none mt-2"
                    >
                        <span v-if="processing" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                        <span>{{ processing ? 'Memverifikasi Identitas...' : 'Masuk' }}</span>
                        <span v-if="!processing" class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>
                </Form>

                <!-- Create Account Alternative -->
                <div class="mt-8 pt-6 border-t border-outline-variant/40 text-center">
                    <p class="text-sm text-secondary mb-4">Baru di galeri arsitektur & pengrajin kami?</p>
                    <Link 
                        :href="register()" 
                        class="inline-flex items-center justify-center w-full py-3.5 border-2 border-primary text-primary font-bold text-sm rounded-xl hover:bg-primary hover:text-white transition-all duration-300 shadow-2xs"
                    >
                        Buat Akun Baru
                    </Link>
                </div>
            </div>
        </main>

        <!-- Simplified Footer -->
        <footer class="relative z-10 w-full px-6 md:px-16 lg:px-20 py-6 max-w-[1440px] mx-auto flex flex-row justify-between items-center gap-4">
            <p class="text-xs sm:text-sm font-medium text-secondary-fixed-variant whitespace-nowrap">
                © 2026 Jelita Saung. Jelita Saung Rutan Perempuan Kelas II A Bandung. Memberdayakan Kerajinan.
            </p>
            <div class="flex flex-row items-center gap-6 whitespace-nowrap">
                <a href="#" class="text-xs sm:text-sm font-bold text-secondary hover:text-primary transition-colors">Kebijakan Privasi</a>
                <a href="#" class="text-xs sm:text-sm font-bold text-secondary hover:text-primary transition-colors">Syarat &amp; Ketentuan</a>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Glass effect with frosted crystal aesthetics */
.glass-effect {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}

:global(.dark) .glass-effect {
    background: rgba(26, 22, 23, 0.88);
}

/* Ensure Material Symbols render as crisp icons and never fall back to words */
.material-symbols-outlined {
    font-family: 'Material Symbols Outlined';
    font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
    font-feature-settings: 'liga';
    display: inline-block;
    vertical-align: middle;
}

/* Welcome theme palette encapsulation matching Jelita Saung & preventing grayscale fallback */
.welcome-theme {
    --background: #fbf9f8;
    --foreground: #1b1c1c;
    --primary: #8a4853;
    --primary-foreground: #ffffff;
    --secondary: #625d5f;
    --secondary-foreground: #ffffff;
    --border: #d7c1c3;
    --outline: #857374;

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
    --color-outline: #857374;
    --color-outline-variant: #d7c1c3;
    --color-background: #fbf9f8;
    --color-on-background: #1b1c1c;
}

:global(.dark) .welcome-theme {
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
    --color-outline: #9c8a8c;
    --color-outline-variant: #524345;
    --color-background: #141213;
    --color-on-background: #f3f0f0;
}
</style>
