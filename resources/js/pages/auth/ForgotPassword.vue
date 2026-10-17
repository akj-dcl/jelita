<script setup lang="ts">
import { ref, computed, nextTick, onBeforeUnmount } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { login } from '@/routes';

defineOptions({
    layout: null, // Standalone full-screen layout
});

const props = defineProps<{
    status?: string;
}>();

const currentStep = ref<'email' | 'otp'>('email');
const inputEmail = ref('');
const displayEmail = ref('your email');
const processing = ref(false);
const verifying = ref(false);

// OTP functionality & State
const otpValues = ref<string[]>(['', '', '', '', '', '']);
const otpInputs = ref<HTMLInputElement[]>([]);

const isOtpComplete = computed(() => otpValues.value.every(val => val.trim() !== ''));

const handleOtpInput = (index: number, event: Event) => {
    const input = event.target as HTMLInputElement;
    const value = input.value;
    if (value.length === 1 && index < 5) {
        otpInputs.value[index + 1]?.focus();
    }
};

const handleOtpKeydown = (index: number, event: KeyboardEvent) => {
    const input = event.target as HTMLInputElement;
    if (event.key === 'Backspace' && !input.value && index > 0) {
        otpInputs.value[index - 1]?.focus();
    }
};

const handleOtpPaste = (event: ClipboardEvent) => {
    event.preventDefault();
    const pasteData = event.clipboardData?.getData('text') || '';
    const cleanDigits = pasteData.replace(/\D/g, '').slice(0, 6);
    if (cleanDigits.length > 0) {
        for (let i = 0; i < 6; i++) {
            otpValues.value[i] = cleanDigits[i] || '';
        }
        const nextFocusIndex = Math.min(cleanDigits.length, 5);
        otpInputs.value[nextFocusIndex]?.focus();
    }
};

// Resend Code Timer
const timer = ref(59);
let timerInterval: ReturnType<typeof setInterval> | null = null;

const startTimer = () => {
    timer.value = 59;
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        if (timer.value > 0) {
            timer.value--;
        } else {
            if (timerInterval) clearInterval(timerInterval);
        }
    }, 1000);
};

onBeforeUnmount(() => {
    if (timerInterval) clearInterval(timerInterval);
});

const handleEmailSubmit = () => {
    processing.value = true;
    displayEmail.value = inputEmail.value || 'your email';
    
    // Transition to OTP step after a brief smooth simulation
    setTimeout(() => {
        processing.value = false;
        currentStep.value = 'otp';
        startTimer();
        nextTick(() => {
            if (otpInputs.value && otpInputs.value.length > 0) {
                otpInputs.value[0]?.focus();
            }
        });
    }, 400);
};

const handleOtpVerify = () => {
    if (!isOtpComplete.value) return;
    verifying.value = true;
    setTimeout(() => {
        verifying.value = false;
        // After successful identity verification, direct to reset password or login
        window.location.href = login().url ? login().url : '/login';
    }, 700);
};

const handleBack = () => {
    if (currentStep.value === 'otp') {
        currentStep.value = 'email';
    } else {
        window.history.back();
    }
};
</script>

<template>
    <Head title="Forgot Password - Jelita Saung">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" />
    </Head>

    <div class="welcome-theme bg-surface text-on-surface font-sans min-h-screen flex flex-col overflow-x-clip antialiased selection:bg-primary/20 selection:text-primary">
        <!-- Header -->
        <header class="relative z-10 w-full px-6 md:px-16 lg:px-20 py-5 max-w-[1440px] mx-auto flex flex-row justify-between items-center h-20">
            <button @click="handleBack" class="flex flex-row items-center gap-2 text-secondary hover:text-primary transition-colors cursor-pointer py-2 px-4 rounded-full hover:bg-surface-container/50 whitespace-nowrap">
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
        <main class="relative z-10 flex-grow w-full flex items-center justify-center px-4 sm:px-6 py-8 md:py-14">
            <div class="w-full max-w-[1024px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                
                <!-- Left Side: Visual Editorial / Sanctuary Context -->
                <div class="hidden lg:block relative rounded-3xl overflow-hidden aspect-[4/5] shadow-[0px_20px_50px_rgba(138,72,83,0.15)] border border-outline-variant/30">
                    <div class="absolute inset-0 z-10 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <img 
                        class="w-full h-full object-cover scale-105 transition-transform duration-1000 hover:scale-100" 
                        alt="High-fashion editorial photograph of architectural sanctuary and handcrafted works of modern artistry" 
                        src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=1200" 
                    />
                    <div class="absolute bottom-10 left-10 right-10 z-20 text-white max-w-[340px]">
                        <span class="bg-white/20 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block border border-white/30">
                            Keamanan Berlapis
                        </span>
                        <h1 class="text-3xl font-black tracking-tight mb-3.5 leading-tight">Keamanan & Keanggunan.</h1>
                        <p class="text-white/85 text-sm leading-relaxed font-medium">
                            Melindungi akun Anda adalah prioritas kami. Ikuti langkah-langkah berikut untuk mendapatkan kembali akses ke koleksi kerajinan Anda.
                        </p>
                    </div>
                </div>

                <!-- Right Side: Flow Container -->
                <div class="relative overflow-hidden min-h-[480px] flex flex-col justify-center px-2 sm:px-4 md:px-6">
                    
                    <div v-if="props.status" class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-center text-sm font-semibold text-green-700 dark:text-green-400 rounded-xl">
                        {{ props.status }}
                    </div>

                    <!-- State 1: Email Request -->
                    <div v-if="currentStep === 'email'" class="w-full transition-all duration-300">
                        <div class="mb-8 sm:mb-10">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-5">
                                <span class="material-symbols-outlined text-[26px]">lock_reset</span>
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-black text-on-surface tracking-tight mb-3">Lupa Kata Sandi</h2>
                            <p class="text-secondary text-base sm:text-lg font-medium leading-relaxed">
                                Masukkan alamat email yang terdaftar untuk menerima 6 digit kode keamanan.
                            </p>
                        </div>

                        <form @submit.prevent="handleEmailSubmit" class="space-y-6 sm:space-y-8">
                            <div class="space-y-2.5">
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest" for="email-input">
                                    Alamat Email
                                </label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-4 text-secondary/60 text-[22px] pointer-events-none">mail</span>
                                    <input 
                                        id="email-input" 
                                        v-model="inputEmail"
                                        type="email"
                                        required 
                                        autofocus
                                        autocomplete="email"
                                        placeholder="name@example.com" 
                                        class="w-full pl-12 pr-5 py-4 bg-surface-container-lowest border border-outline-variant rounded-2xl text-sm sm:text-base font-medium text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all duration-300 placeholder:text-secondary/50 outline-none shadow-2xs" 
                                    />
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="processing"
                                class="w-full h-14 bg-primary text-white rounded-2xl font-bold text-base sm:text-lg hover:opacity-95 shadow-lg shadow-primary/25 transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-50 disabled:pointer-events-none group"
                            >
                                <span v-if="processing" class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                <span>{{ processing ? 'Mengirim Kode Reset...' : 'Kirim Kode Reset' }}</span>
                                <span v-if="!processing" class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </button>

                            <div class="text-center pt-2">
                                <p class="text-on-surface-variant text-sm font-medium">
                                    Ingat kata sandi Anda? 
                                    <Link :href="login()" class="text-primary font-bold hover:underline ml-1">Masuk</Link>
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- State 2: OTP Verification -->
                    <div v-if="currentStep === 'otp'" class="w-full transition-all duration-300">
                        <div class="mb-8 sm:mb-10">
                            <div class="flex items-center gap-2 text-primary mb-4 bg-primary/10 w-fit px-3.5 py-1.5 rounded-full border border-primary/20 shadow-2xs">
                                <span class="material-symbols-outlined text-[18px]">mark_email_read</span>
                                <span class="font-bold text-xs uppercase tracking-widest">Kode Terkirim</span>
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-black text-on-surface tracking-tight mb-3">Verifikasi Identitas</h2>
                            <p class="text-secondary text-base sm:text-lg leading-relaxed">
                                Kami telah mengirimkan 6 digit kode keamanan ke <span class="text-on-surface font-extrabold underline decoration-primary/50 underline-offset-4">{{ displayEmail }}</span>.
                            </p>
                        </div>

                        <form @submit.prevent="handleOtpVerify" class="space-y-8">
                            <div>
                                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-3.5">
                                    Kode Verifikasi
                                </label>
                                <div class="flex flex-row justify-between items-center gap-2 sm:gap-3 w-full max-w-[440px]">
                                    <input 
                                        v-for="(val, index) in otpValues" 
                                        :key="index"
                                        ref="otpInputs"
                                        v-model="otpValues[index]"
                                        @input="handleOtpInput(index, $event)"
                                        @keydown="handleOtpKeydown(index, $event)"
                                        @paste="handleOtpPaste($event)"
                                        class="otp-input w-11 sm:w-13 md:w-14 h-12 sm:h-14 md:h-16 text-center text-xl sm:text-2xl font-black bg-surface-container border-2 border-outline-variant/60 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-on-surface select-all shadow-2xs" 
                                        maxlength="1" 
                                        type="text"
                                        inputmode="numeric"
                                        :autofocus="index === 0"
                                    />
                                </div>
                            </div>

                            <button 
                                type="submit" 
                                :disabled="verifying || !isOtpComplete"
                                class="w-full h-14 bg-primary text-white rounded-2xl font-bold text-base sm:text-lg hover:opacity-95 shadow-lg shadow-primary/25 transition-all duration-300 active:scale-[0.98] flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-40 disabled:pointer-events-none group"
                            >
                                <span v-if="verifying" class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
                                <span v-if="!verifying" class="material-symbols-outlined text-[20px]">verified</span>
                                <span>{{ verifying ? 'Memverifikasi Kode...' : 'Verifikasi & Lanjutkan' }}</span>
                            </button>

                            <div class="flex flex-col items-center gap-3 pt-2">
                                <button 
                                    type="button" 
                                    @click="startTimer" 
                                    :disabled="timer > 0"
                                    class="text-primary font-bold hover:underline text-sm disabled:text-secondary/80 disabled:no-underline disabled:cursor-not-allowed transition-colors cursor-pointer"
                                >
                                    {{ timer > 0 ? `Kirim Ulang Kode (dalam 0:${timer < 10 ? '0' : ''}${timer})` : 'Kirim Ulang Kode Sekarang' }}
                                </button>
                                
                                <button 
                                    type="button" 
                                    @click="currentStep = 'email'" 
                                    class="text-secondary hover:text-primary font-semibold text-sm flex items-center gap-1.5 transition-colors py-2 px-4 rounded-full hover:bg-surface-container/60 cursor-pointer mt-1"
                                >
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                    <span>Ubah Alamat Email</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full px-6 md:px-16 lg:px-20 py-8 max-w-[1440px] mx-auto border-t border-outline-variant/40 text-center">
            <p class="text-secondary-fixed-variant text-xs font-extrabold uppercase tracking-[0.18em]">
                © 2026 Jelita Saung. Jelita Saung Rutan Perempuan Kelas II A Bandung — Keamanan Digital Khusus
            </p>
        </footer>
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
.otp-input:focus {
    border-color: var(--primary, #8a4853);
    box-shadow: 0 0 0 2px rgba(138, 72, 83, 0.2);
}

</style>
