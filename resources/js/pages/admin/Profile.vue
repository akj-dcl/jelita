<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import { Input } from '@/components/ui/input'

const props = defineProps<{
    user: any;
    status?: string;
}>()

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
})

const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
})

const updateProfile = () => {
    profileForm.put('/admin/profile', {
        preserveScroll: true,
        onSuccess: () => {
            // success
        }
    })
}

const updatePassword = () => {
    passwordForm.put('/admin/profile', {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset()
        }
    })
}
</script>

<template>
    <Head title="Profil Admin" />

    <AdminLayout>
        <template #header-title>
            <div class="flex items-center gap-xl hidden md:flex">
                <h2 class="font-headline-md text-headline-md text-primary font-bold">Pengaturan Profil</h2>
            </div>
        </template>

        <div class="pt-8 px-lg pb-xl max-w-3xl mx-auto relative min-h-[calc(100vh-64px)]">
            <div class="mb-lg">
                <h1 class="font-display-sm text-display-sm text-on-surface font-bold">Edit Profil</h1>
                <p class="text-on-surface-variant mt-2">Kelola detail akun admin dan password Anda.</p>
            </div>

            <!-- Profile Info Form -->
            <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none mb-lg p-lg">
                <h3 class="text-lg font-bold text-on-surface mb-6">Informasi Profil</h3>
                
                <form @submit.prevent="updateProfile" class="flex flex-col gap-md">
                    <div>
                        <label class="block text-label-md font-bold text-on-surface mb-xs">Nama</label>
                        <Input v-model="profileForm.name" type="text" class="h-12 border-outline-variant bg-surface-container-low/50 text-on-surface focus-visible:ring-primary" required />
                        <span v-if="profileForm.errors.name" class="text-error text-sm mt-1">{{ profileForm.errors.name }}</span>
                    </div>

                    <div>
                        <label class="block text-label-md font-bold text-on-surface mb-xs">Alamat Email</label>
                        <Input v-model="profileForm.email" type="email" class="h-12 border-outline-variant bg-surface-container-low/50 text-on-surface focus-visible:ring-primary" required />
                        <span v-if="profileForm.errors.email" class="text-error text-sm mt-1">{{ profileForm.errors.email }}</span>
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <Button type="submit" :disabled="profileForm.processing" class="bg-primary text-white hover:bg-primary-container h-12 px-8 rounded-lg shadow-sm">
                            Simpan Perubahan
                        </Button>
                        <transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0" leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0">
                            <span v-if="profileForm.recentlySuccessful" class="text-sm text-on-surface-variant font-medium">Tersimpan.</span>
                        </transition>
                    </div>
                </form>
            </Card>

            <!-- Password Form -->
            <Card class="bg-surface-container-lowest border border-transparent dark:border-outline-variant/30 rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none p-lg">
                <h3 class="text-lg font-bold text-on-surface mb-6">Ganti Password</h3>
                
                <form @submit.prevent="updatePassword" class="flex flex-col gap-md">
                    <div>
                        <label class="block text-label-md font-bold text-on-surface mb-xs">Password Saat Ini</label>
                        <Input v-model="passwordForm.current_password" type="password" class="h-12 border-outline-variant bg-surface-container-low/50 text-on-surface focus-visible:ring-primary" required />
                        <span v-if="passwordForm.errors.current_password" class="text-error text-sm mt-1">{{ passwordForm.errors.current_password }}</span>
                    </div>

                    <div>
                        <label class="block text-label-md font-bold text-on-surface mb-xs">Password Baru</label>
                        <Input v-model="passwordForm.new_password" type="password" class="h-12 border-outline-variant bg-surface-container-low/50 text-on-surface focus-visible:ring-primary" required />
                        <span v-if="passwordForm.errors.new_password" class="text-error text-sm mt-1">{{ passwordForm.errors.new_password }}</span>
                    </div>

                    <div>
                        <label class="block text-label-md font-bold text-on-surface mb-xs">Konfirmasi Password Baru</label>
                        <Input v-model="passwordForm.new_password_confirmation" type="password" class="h-12 border-outline-variant bg-surface-container-low/50 text-on-surface focus-visible:ring-primary" required />
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <Button type="submit" :disabled="passwordForm.processing" class="bg-primary text-white hover:bg-primary-container h-12 px-8 rounded-lg shadow-sm">
                            Perbarui Password
                        </Button>
                        <transition enter-active-class="transition ease-in-out duration-300" enter-from-class="opacity-0" leave-active-class="transition ease-in-out duration-300" leave-to-class="opacity-0">
                            <span v-if="passwordForm.recentlySuccessful" class="text-sm text-on-surface-variant font-medium">Tersimpan.</span>
                        </transition>
                    </div>
                </form>
            </Card>
        </div>
    </AdminLayout>
</template>
