<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    authUser: any;
    fallbackAvatar: string;
}>();

const emit = defineEmits(['change-tab', 'handle-image-error']);

// ─────────────────────────────────────────
// Profile form
// ─────────────────────────────────────────
const profileForm = reactive({
    name:  props.authUser?.name  || '',
    phone: props.authUser?.phone || '',
});

const photoFile    = ref<File | null>(null);
const photoPreview = ref<string | null>(props.authUser?.profile_photo || null);
const isSavingProfile = ref(false);
const profileSuccess  = ref('');
const profileErrors   = ref<Record<string, string>>({});

const initials = computed(() =>
    (props.authUser?.name || 'C').charAt(0).toUpperCase()
);

const onPhotoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    photoFile.value = file;
    const reader = new FileReader();
    reader.onload = (ev) => { photoPreview.value = ev.target?.result as string; };
    reader.readAsDataURL(file);
};

const handleProfileSubmit = () => {
    if (isSavingProfile.value) return;
    isSavingProfile.value = true;
    profileSuccess.value  = '';
    profileErrors.value   = {};

    const formData = new FormData();
    formData.append('name',  profileForm.name);
    formData.append('phone', profileForm.phone);
    if (photoFile.value) {
        formData.append('profile_photo', photoFile.value);
    }

    router.post('/my-profile/update-info', formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            profileSuccess.value = 'Profil berhasil diperbarui!';
            photoFile.value = null;
        },
        onError: (errors) => {
            profileErrors.value = errors as Record<string, string>;
        },
        onFinish: () => { isSavingProfile.value = false; },
    });
};

// ─────────────────────────────────────────
// Password form
// ─────────────────────────────────────────
const passwordForm = reactive({
    current_password:          '',
    password:                  '',
    password_confirmation:     '',
});

const showCurrentPw  = ref(false);
const showNewPw      = ref(false);
const showConfirmPw  = ref(false);
const isSavingPw     = ref(false);
const passwordSuccess = ref('');
const passwordErrors  = ref<Record<string, string>>({});

const handlePasswordSubmit = () => {
    if (isSavingPw.value) return;
    isSavingPw.value    = true;
    passwordSuccess.value = '';
    passwordErrors.value  = {};

    router.post('/my-profile/change-password', { ...passwordForm }, {
        preserveScroll: true,
        onSuccess: () => {
            passwordSuccess.value = 'Password berhasil diperbarui!';
            passwordForm.current_password      = '';
            passwordForm.password              = '';
            passwordForm.password_confirmation = '';
        },
        onError: (errors) => {
            passwordErrors.value = errors as Record<string, string>;
        },
        onFinish: () => { isSavingPw.value = false; },
    });
};
</script>

<template>
    <div class="space-y-6 animate-fade-in">

        <!-- ── Profile Info Card ── -->
        <div class="bg-surface-container-lowest rounded-2xl p-[24px] md:p-[36px] border border-outline-variant/20 shadow-sm">
            <header class="mb-[28px] pb-[20px] border-b border-outline-variant/20">
                <h2 class="text-[22px] font-bold text-on-surface">Informasi Profil</h2>
                <p class="text-[14px] text-on-surface-variant mt-1">Perbarui nama, nomor telepon, dan foto profil Anda.</p>
            </header>

            <form @submit.prevent="handleProfileSubmit" enctype="multipart/form-data" class="space-y-6">
                <!-- Photo Upload -->
                <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-outline-variant/20">
                    <div class="relative group cursor-pointer shrink-0" @click="($refs.photoInput as HTMLInputElement).click()">
                        <div class="w-[96px] h-[96px] rounded-full overflow-hidden bg-primary/10 border-[3px] border-primary/20 shadow-sm flex items-center justify-center">
                            <img
                                v-if="photoPreview"
                                class="w-full h-full object-cover"
                                :src="photoPreview"
                                @error="$emit('handle-image-error', $event, fallbackAvatar)"
                                alt="Preview"
                            />
                            <span v-else class="text-[36px] font-bold text-primary select-none">{{ initials }}</span>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/35 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <span class="material-symbols-outlined text-white text-[26px]">photo_camera</span>
                        </div>
                    </div>
                    <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="onPhotoChange" />

                    <div class="text-center sm:text-left">
                        <h3 class="text-[14px] font-bold text-on-surface">Foto Profil</h3>
                        <p class="text-[12px] text-on-surface-variant mt-1">JPG, PNG, GIF, atau WebP. Maks 2MB.</p>
                        <p v-if="profileErrors.profile_photo" class="text-red-500 text-[12px] mt-1">{{ profileErrors.profile_photo }}</p>
                        <div class="flex gap-3 mt-3 justify-center sm:justify-start">
                            <button type="button" @click="($refs.photoInput as HTMLInputElement).click()" class="px-4 py-1.5 bg-primary/10 text-primary rounded-lg text-[13px] font-semibold hover:bg-primary/20 transition-all">
                                Upload Foto
                            </button>
                            <button
                                v-if="photoPreview && authUser?.profile_photo"
                                type="button"
                                @click="photoPreview = null; photoFile = null"
                                class="px-4 py-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg text-[13px] font-semibold transition-all"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Name & Phone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[13px] font-semibold text-on-surface-variant mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input
                            v-model="profileForm.name"
                            type="text"
                            required
                            class="w-full bg-surface border border-outline-variant/40 rounded-xl px-4 py-3 text-[15px] text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all"
                            placeholder="Nama lengkap Anda"
                        />
                        <p v-if="profileErrors.name" class="text-red-500 text-[12px] mt-1">{{ profileErrors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-on-surface-variant mb-1.5">No. Telepon / WhatsApp</label>
                        <input
                            v-model="profileForm.phone"
                            type="tel"
                            class="w-full bg-surface border border-outline-variant/40 rounded-xl px-4 py-3 text-[15px] text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all"
                            placeholder="+62 81234567890"
                        />
                        <p v-if="profileErrors.phone" class="text-red-500 text-[12px] mt-1">{{ profileErrors.phone }}</p>
                    </div>
                </div>

                <!-- Email (read-only) -->
                <div>
                    <label class="block text-[13px] font-semibold text-on-surface-variant mb-1.5">Alamat Email</label>
                    <input
                        :value="authUser?.email"
                        type="email"
                        readonly
                        class="w-full bg-surface-container border border-outline-variant/20 rounded-xl px-4 py-3 text-[15px] text-on-surface-variant cursor-not-allowed"
                    />
                    <p class="text-[12px] text-on-surface-variant mt-1 opacity-70">Email tidak dapat diubah.</p>
                </div>

                <!-- Success -->
                <div v-if="profileSuccess" class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl px-4 py-3 text-[14px] font-semibold">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    {{ profileSuccess }}
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="$emit('change-tab', 'profile')" class="px-6 py-2.5 border border-outline-variant/40 rounded-xl text-[14px] font-semibold text-on-surface-variant hover:bg-surface-container transition-all">
                        Batal
                    </button>
                    <button
                        type="submit"
                        :disabled="isSavingProfile"
                        class="px-8 py-2.5 bg-primary text-white rounded-xl text-[14px] font-bold shadow-sm hover:opacity-90 active:scale-95 transition-all disabled:opacity-60 flex items-center gap-2"
                    >
                        <span v-if="isSavingProfile" class="material-symbols-outlined animate-spin text-[16px]">refresh</span>
                        Simpan Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- ── Change Password Card ── -->
        <div class="bg-surface-container-lowest rounded-2xl p-[24px] md:p-[36px] border border-outline-variant/20 shadow-sm">
            <header class="mb-[28px] pb-[20px] border-b border-outline-variant/20">
                <h2 class="text-[22px] font-bold text-on-surface">Keamanan Akun</h2>
                <p class="text-[14px] text-on-surface-variant mt-1">Ganti password untuk menjaga keamanan akun Anda.</p>
            </header>

            <form @submit.prevent="handlePasswordSubmit" class="space-y-5">
                <!-- Current Password -->
                <div>
                    <label class="block text-[13px] font-semibold text-on-surface-variant mb-1.5">Password Saat Ini <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input
                            v-model="passwordForm.current_password"
                            :type="showCurrentPw ? 'text' : 'password'"
                            required
                            class="w-full bg-surface border border-outline-variant/40 rounded-xl px-4 py-3 pr-12 text-[15px] text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all"
                            placeholder="••••••••"
                        />
                        <button type="button" @click="showCurrentPw = !showCurrentPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-[20px]">{{ showCurrentPw ? 'visibility' : 'visibility_off' }}</span>
                        </button>
                    </div>
                    <p v-if="passwordErrors.current_password" class="text-red-500 text-[12px] mt-1">{{ passwordErrors.current_password }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- New Password -->
                    <div>
                        <label class="block text-[13px] font-semibold text-on-surface-variant mb-1.5">Password Baru <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input
                                v-model="passwordForm.password"
                                :type="showNewPw ? 'text' : 'password'"
                                required
                                class="w-full bg-surface border border-outline-variant/40 rounded-xl px-4 py-3 pr-12 text-[15px] text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all"
                                placeholder="Min. 8 karakter"
                            />
                            <button type="button" @click="showNewPw = !showNewPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">{{ showNewPw ? 'visibility' : 'visibility_off' }}</span>
                            </button>
                        </div>
                        <p v-if="passwordErrors.password" class="text-red-500 text-[12px] mt-1">{{ passwordErrors.password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-[13px] font-semibold text-on-surface-variant mb-1.5">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input
                                v-model="passwordForm.password_confirmation"
                                :type="showConfirmPw ? 'text' : 'password'"
                                required
                                class="w-full bg-surface border border-outline-variant/40 rounded-xl px-4 py-3 pr-12 text-[15px] text-on-surface focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition-all"
                                placeholder="Ulangi password baru"
                            />
                            <button type="button" @click="showConfirmPw = !showConfirmPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[20px]">{{ showConfirmPw ? 'visibility' : 'visibility_off' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Success -->
                <div v-if="passwordSuccess" class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl px-4 py-3 text-[14px] font-semibold">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    {{ passwordSuccess }}
                </div>

                <!-- Actions -->
                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="isSavingPw"
                        class="px-8 py-2.5 bg-primary text-white rounded-xl text-[14px] font-bold shadow-sm hover:opacity-90 active:scale-95 transition-all disabled:opacity-60 flex items-center gap-2"
                    >
                        <span v-if="isSavingPw" class="material-symbols-outlined animate-spin text-[16px]">refresh</span>
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>

    </div>
</template>
