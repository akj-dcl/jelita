import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        const cleanName = name.toLowerCase();
        if (cleanName === 'welcome' || cleanName === 'shop' || cleanName === 'collections' || cleanName === 'story' || cleanName === 'about' || cleanName === 'productdetail' || cleanName === 'cart' || cleanName === 'wishlist' || cleanName === 'checkout' || cleanName === 'checkoutpayment' || cleanName === 'orderconfirmed' || cleanName === 'myprofile' || cleanName === 'customerprofile' || cleanName === 'dashboard' || (cleanName.includes('profile') && !cleanName.startsWith('settings/')) || cleanName.includes('login') || cleanName.includes('forgot') || cleanName.includes('password') || cleanName.includes('register') || cleanName.startsWith('admin/')) {
            return null;
        }
        if (cleanName.startsWith('auth/')) {
            return AuthLayout;
        }
        if (cleanName.startsWith('settings/')) {
            return [AppLayout, SettingsLayout];
        }
        return AppLayout;
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
