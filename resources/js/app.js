import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { router } from '@inertiajs/vue3';

// ===== CSRF Token Auto-Refresh untuk PWA =====
// Refresh CSRF token setiap 50 menit (Laravel session default 120 menit)
let csrfRefreshInterval = null;

const refreshCsrfToken = async () => {
    try {
        const response = await fetch('/api/csrf-token', {
            method: 'GET',
            credentials: 'same-origin',
        });
        
        if (response.ok) {
            const data = await response.json();
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag && data.token) {
                metaTag.setAttribute('content', data.token);
                console.log('CSRF token refreshed successfully');
            }
        }
    } catch (error) {
        console.error('Error refreshing CSRF token:', error);
    }
};

// Start auto-refresh CSRF token
const startCsrfRefresh = () => {
    // Refresh immediately on load
    refreshCsrfToken();
    
    // Then refresh every 50 minutes
    csrfRefreshInterval = setInterval(refreshCsrfToken, 50 * 60 * 1000);
};

// Refresh token when page becomes visible (user kembali ke app)
if (typeof document.hidden !== 'undefined') {
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            console.log('Page visible, refreshing CSRF token...');
            refreshCsrfToken();
        }
    });
}

// Helper function untuk fetch dengan auto-retry jika CSRF error
window.fetchWithCsrfRetry = async (url, options = {}, retries = 1) => {
    try {
        const response = await fetch(url, {
            ...options,
            credentials: 'same-origin',
            headers: {
                ...options.headers,
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
        });
        
        // Jika 419 (CSRF error) dan masih ada retry, coba lagi setelah refresh token
        if (response.status === 419 && retries > 0) {
            console.log('CSRF token mismatch, refreshing token and retrying...');
            await refreshCsrfToken();
            // Wait 500ms untuk token baru tersimpan
            await new Promise(resolve => setTimeout(resolve, 500));
            return window.fetchWithCsrfRetry(url, options, retries - 1);
        }
        
        return response;
    } catch (error) {
        throw error;
    }
};

createInertiaApp({
    title: (title) => title ? `${title} - PAMSIMAS` : 'PAMSIMAS',
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#1e40af',
    },
});

// Global error handler untuk Inertia
router.on('error', (event) => {
    // Handle 419 CSRF token mismatch
    if (event.detail.response?.status === 419) {
        console.log('CSRF error detected, refreshing token...');
        refreshCsrfToken().then(() => {
            // Reload halaman setelah 1 detik
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
    }
});

// Start CSRF refresh setelah app ready
startCsrfRefresh();

// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('Service Worker registered successfully:', registration.scope);
            })
            .catch((error) => {
                console.log('Service Worker registration failed:', error);
            });
    });
}
