<script setup lang="ts">
import { ref } from 'vue';
import { Form, Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules?: string;
}>();

defineOptions({
    layout: null, // Standalone luxury full-screen layout
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);
</script>

<template>
    <Head title="Register - Jelita Saung">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" />
    </Head>

    <div class="welcome-theme bg-surface text-on-surface min-h-screen flex flex-col font-sans antialiased selection:bg-primary/20 selection:text-primary overflow-x-clip">
        <!-- Header -->
        <header class="relative z-10 w-full px-6 md:px-16 lg:px-20 py-5 max-w-[1440px] mx-auto flex flex-row justify-between items-center h-20">
            <button @click="$router ? $router.back() : window.history.back()" class="flex flex-row items-center gap-2 text-secondary hover:text-primary transition-colors cursor-pointer py-2 px-4 rounded-full hover:bg-surface-container/50 whitespace-nowrap">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                <span class="text-xs font-black tracking-widest uppercase">Kembali</span>
            </button>
            
            <Link href="/" class="flex flex-row items-center gap-2.5 hover:opacity-95 transition-opacity">
                <div class="flex flex-row items-center gap-2">
                    <span class="font-extrabold text-xl sm:text-2xl tracking-tight text-on-surface whitespace-nowrap">Jelita Saung</span>
                    <span class="bg-primary text-white text-[11px] sm:text-xs px-2 sm:px-2.5 py-0.5 rounded tracking-widest uppercase font-black shadow-2xs whitespace-nowrap">ONE</span>
                </div>
                <span class="hidden sm:inline-block text-secondary-fixed-variant font-semibold text-sm sm:text-base border-l-2 border-outline-variant/60 pl-3 whitespace-nowrap">
                    Jelita Saung
                </span>
            </Link>
            
            <div class="w-16 sm:w-20"></div> <!-- Spacer for symmetry -->
        </header>

        <!-- Main Content Canvas -->
        <main class="flex-grow w-full flex items-center justify-center py-6 sm:py-10 md:py-14 px-4 sm:px-6">
            <div class="w-full max-w-[1180px] mx-auto grid grid-cols-1 lg:grid-cols-12 bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0px_20px_60px_rgba(138,72,83,0.12)] border border-outline-variant/40 transition-all">
                
                <!-- Visual Branding Panel (5 Columns on Desktop) -->
                <div class="hidden lg:flex lg:col-span-5 flex-col justify-between p-8 xl:p-12 relative overflow-hidden bg-surface-container-low border-r border-outline-variant/30">
                    <div class="z-10 mb-8">
                        <span class="bg-primary/10 text-primary text-[11px] font-black uppercase tracking-widest px-3.5 py-1 rounded-full border border-primary/20 inline-block mb-4 shadow-2xs">
                            Komunitas Perajin
                        </span>
                        <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-primary mb-3 leading-tight">Jelita Saung</h1>
                        <p class="text-base font-medium text-secondary max-w-[380px] leading-relaxed">
                            Bergabunglah dengan komunitas perajin dan kolektor bergengsi kami. Temukan keunggulan penuh jiwa dari kerajinan buatan tangan.
                        </p>
                    </div>
                    
                    <div class="relative z-10 my-auto">
                        <div class="aspect-[4/5] w-full max-w-[360px] mx-auto rounded-2xl overflow-hidden shadow-[0_12px_32px_rgba(0,0,0,0.15)] border border-outline-variant/40 group">
                            <img 
                                class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" 
                                alt="High-end minimalist photograph of artisanal pottery and woven textiles in a bright, sun-drenched studio" 
                                src="/storage/template/daftar.jpg" 
                            />
                        </div>
                    </div>
                    
                    <div class="absolute bottom-0 left-0 w-full h-1/3 bg-gradient-to-t from-surface-container-low via-surface-container-low/60 to-transparent pointer-events-none"></div>
                </div>

                <!-- Registration Form Panel (7 Columns on Desktop) -->
                <div class="lg:col-span-7 p-6 sm:p-10 md:p-12 xl:p-14 flex flex-col justify-center bg-surface-container-lowest">
                    <div class="mb-8 sm:mb-10">
                        <h2 class="text-3xl sm:text-4xl font-black text-on-surface tracking-tight mb-2">Buat Akun</h2>
                        <p class="text-sm sm:text-base font-medium text-secondary">Masukkan detail Anda di bawah untuk memulai perjalanan menuju keanggunan kerajinan.</p>
                    </div>

                    <Form
                        v-bind="store.form()"
                        :reset-on-success="['password', 'password_confirmation']"
                        v-slot="{ errors, processing }"
                        class="space-y-6 sm:space-y-7"
                    >
                        <!-- Full Name -->
                        <div class="space-y-2">
                            <label class="block text-xs font-extrabold uppercase tracking-widest text-on-surface-variant" for="full-name">Nama Lengkap</label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-4 text-secondary/60 text-[20px] pointer-events-none">person</span>
                                <input 
                                    id="full-name" 
                                    name="name" 
                                    type="text" 
                                    required
                                    autofocus
                                    autocomplete="name"
                                    placeholder="E.g. Sophia Alana" 
                                    class="w-full pl-12 pr-4 py-3.5 border border-outline-variant bg-surface-container-lowest rounded-xl font-medium text-sm sm:text-base text-on-surface placeholder:text-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-300 shadow-2xs" 
                                />
                            </div>
                            <InputError :message="errors?.name" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="block text-xs font-extrabold uppercase tracking-widest text-on-surface-variant" for="email">Alamat Email</label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-4 text-secondary/60 text-[20px] pointer-events-none">mail</span>
                                <input 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    required
                                    autocomplete="email"
                                    placeholder="sophia@example.com" 
                                    class="w-full pl-12 pr-4 py-3.5 border border-outline-variant bg-surface-container-lowest rounded-xl font-medium text-sm sm:text-base text-on-surface placeholder:text-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-300 shadow-2xs" 
                                />
                            </div>
                            <InputError :message="errors?.email" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                            <!-- Password -->
                            <div class="space-y-2">
                                <label class="block text-xs font-extrabold uppercase tracking-widest text-on-surface-variant" for="password">Kata Sandi</label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-4 text-secondary/60 text-[20px] pointer-events-none">lock</span>
                                    <input 
                                        id="password" 
                                        name="password" 
                                        :type="showPassword ? 'text' : 'password'" 
                                        required
                                        autocomplete="new-password"
                                        placeholder="••••••••" 
                                        class="w-full pl-12 pr-11 py-3.5 border border-outline-variant bg-surface-container-lowest rounded-xl font-medium text-sm sm:text-base text-on-surface placeholder:text-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-300 shadow-2xs" 
                                    />
                                    <button 
                                        type="button" 
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-on-surface p-1.5 rounded-lg hover:bg-surface-container/60 transition-colors cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[20px] select-none">{{ showPassword ? 'visibility_off' : 'visibility' }}</span>
                                    </button>
                                </div>
                                <InputError :message="errors?.password" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-2">
                                <label class="block text-xs font-extrabold uppercase tracking-widest text-on-surface-variant" for="confirm-password">Konfirmasi Kata Sandi</label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-4 text-secondary/60 text-[20px] pointer-events-none">lock_clock</span>
                                    <input 
                                        id="confirm-password" 
                                        name="password_confirmation" 
                                        :type="showConfirmPassword ? 'text' : 'password'" 
                                        required
                                        autocomplete="new-password"
                                        placeholder="••••••••" 
                                        class="w-full pl-12 pr-11 py-3.5 border border-outline-variant bg-surface-container-lowest rounded-xl font-medium text-sm sm:text-base text-on-surface placeholder:text-secondary/50 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all duration-300 shadow-2xs" 
                                    />
                                    <button 
                                        type="button" 
                                        @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary hover:text-on-surface p-1.5 rounded-lg hover:bg-surface-container/60 transition-colors cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[20px] select-none">{{ showConfirmPassword ? 'visibility_off' : 'visibility' }}</span>
                                    </button>
                                </div>
                                <InputError :message="errors?.password_confirmation" />
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start space-x-3 pt-2">
                            <input 
                                id="terms" 
                                name="terms" 
                                type="checkbox" 
                                required
                                class="custom-checkbox mt-1 size-5 rounded border-outline-variant text-primary focus:ring-primary/30 transition-colors cursor-pointer shadow-2xs" 
                            />
                            <label class="text-xs sm:text-sm font-medium text-secondary leading-relaxed cursor-pointer" for="terms">
                                Saya setuju dengan <a href="#" class="text-primary underline underline-offset-4 font-bold hover:text-primary-container">Syarat &amp; Ketentuan</a> dan <a href="#" class="text-primary underline underline-offset-4 font-bold hover:text-primary-container">Kebijakan Privasi</a>.
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button 
                                type="submit" 
                                :disabled="processing"
                                class="w-full h-14 bg-primary text-white rounded-2xl font-extrabold text-base sm:text-lg transition-all duration-300 hover:opacity-95 active:scale-[0.98] shadow-[0_10px_25px_rgba(138,72,83,0.25)] flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-50 disabled:pointer-events-none group"
                            >
                                <span v-if="processing" class="material-symbols-outlined animate-spin text-[22px]">progress_activity</span>
                                <span>{{ processing ? 'Mendaftar Akun...' : 'Buat Akun' }}</span>
                                <span v-if="!processing" class="material-symbols-outlined text-[22px] group-hover:translate-x-1 transition-transform duration-300">arrow_forward</span>
                            </button>
                        </div>
                    </Form>

                    <!-- Login Link -->
                    <div class="mt-8 sm:mt-10 text-center border-t border-outline-variant/30 pt-6">
                        <p class="text-sm sm:text-base font-medium text-secondary">
                            Sudah punya akun? 
                            <Link :href="login()" class="text-primary font-bold hover:underline underline-offset-4 transition-all ml-1.5 inline-flex items-center gap-0.5">
                                <span>Masuk</span>
                                <span class="material-symbols-outlined text-[15px]">chevron_right</span>
                            </Link>
                        </p>
                    </div>

                    <!-- Footer Trademark -->
                    <div class="mt-8 text-center opacity-65">
                        <p class="text-[11px] sm:text-xs font-bold uppercase tracking-widest text-secondary-fixed-variant">
                            © 2026 Jelita Saung. Jelita Saung Rutan Perempuan Kelas II A Bandung
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Ensure Material Symbols render as crisp icons and never fall back to words */
.material-symbols-outlined {
    font-family: 'Material Symbols Outlined';
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    font-feature-settings: 'liga';
    display: inline-block;
    vertical-align: middle;
}

.custom-checkbox:checked {
    background-color: var(--primary, #8a4853);
    border-color: var(--primary, #8a4853);
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
