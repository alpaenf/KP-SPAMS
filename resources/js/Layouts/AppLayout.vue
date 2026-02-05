<template>
    <div class="min-h-screen bg-white">
        <nav :class="{
            'bg-white sticky top-0 transition-all duration-300': true,
            'shadow-lg border-b-2 border-blue-600': isScrolled,
            'shadow-sm border-b border-gray-200': !isScrolled,
            'z-[100]': !mobileMenuOpen,
            'z-[60]': mobileMenuOpen
        }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div :class="{
                    'flex justify-between transition-all duration-300': true,
                    'h-14': isScrolled,
                    'h-16': !isScrolled
                }">
                    <div class="flex items-center">
                        <Link :href="$page.props.auth?.user ? '/dashboard' : '/'" class="flex items-center gap-3">
                            <img src="/images/logo.png" alt="Logo KP-SPAMS" class="h-10 w-10 object-contain" />
                            <div class="flex flex-col">
                                <span class="text-xl font-semibold text-blue-800 leading-tight">KP-SPAMS</span>
                                <span class="text-xs text-blue-600 font-medium">DAMAR WULAN</span>
                            </div>
                        </Link>
                        <div v-if="$page.props.auth?.user" class="hidden md:ml-10 md:flex md:space-x-8">
                            <Link 
                                href="/dashboard" 
                                :class="{
                                    'inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors': true,
                                    'border-blue-600 text-blue-700 font-semibold': isActive('/dashboard'),
                                    'border-transparent text-gray-700 hover:text-blue-800 hover:border-gray-300': !isActive('/dashboard')
                                }"
                            >
                                Dashboard
                            </Link>
                            <Link 
                                href="/cek-pelanggan" 
                                :class="{
                                    'inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors': true,
                                    'border-blue-600 text-blue-700 font-semibold': isActive('/cek-pelanggan'),
                                    'border-transparent text-gray-700 hover:text-blue-800 hover:border-gray-300': !isActive('/cek-pelanggan')
                                }"
                            >
                                Cek Pelanggan
                            </Link>
                            <Link 
                                href="/peta" 
                                :class="{
                                    'inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors': true,
                                    'border-blue-600 text-blue-700 font-semibold': isActive('/peta'),
                                    'border-transparent text-gray-700 hover:text-blue-800 hover:border-gray-300': !isActive('/peta')
                                }"
                            >
                                Peta
                            </Link>
                            
                            <Link 
                                href="/tagihan-bulanan" 
                                :class="{
                                    'inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors': true,
                                    'border-blue-600 text-blue-700 font-semibold': isActive('/tagihan-bulanan'),
                                    'border-transparent text-gray-700 hover:text-blue-800 hover:border-gray-300': !isActive('/tagihan-bulanan')
                                }"
                            >
                                Tagihan & Pembayaran
                            </Link>
                            
                            <Link 
                                href="/laporan" 
                                :class="{
                                    'inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors': true,
                                    'border-blue-600 text-blue-700 font-semibold': isActive('/laporan'),
                                    'border-transparent text-gray-700 hover:text-blue-800 hover:border-gray-300': !isActive('/laporan')
                                }"
                            >
                                Laporan
                            </Link>
                            <!-- Admin Menu Dropdown -->
                            <div v-if="isAdmin || isPengelola" class="relative group">
                                <button 
                                    :class="{
                                        'inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors gap-1': true,
                                        'border-blue-600 text-blue-700 font-semibold': isActive('/admin'),
                                        'border-transparent text-gray-700 hover:text-blue-800 hover:border-gray-300': !isActive('/admin')
                                    }"
                                >
                                    Admin
                                    <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <!-- Dropdown Menu -->
                                <div class="absolute left-0 mt-2 w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 py-1">
                                        <Link href="/admin/landing-page" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800 transition">
                                            Kelola Landing Page
                                        </Link>
                                        <Link href="/admin/payment-settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800 transition">
                                            Pengaturan Pembayaran
                                        </Link>
                                        <Link href="/admin/map-settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800 transition">
                                            Pengaturan Peta
                                        </Link>
                                        <Link href="/admin/faqs" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800 transition">
                                            Kelola FAQ
                                        </Link>
                                        <Link href="/admin/informasi-tarif" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-800 transition">
                                            Kelola Informasi Tarif
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <!-- Desktop Menu -->
                        <div v-if="$page.props.auth?.user" class="hidden md:flex items-center gap-4">
                            <!-- Dashboard Admin untuk role admin -->
                            <Link v-if="$page.props.auth.user.role === 'admin'" href="/admin" class="inline-flex items-center px-6 py-2 bg-blue-800 text-white rounded-lg font-semibold hover:bg-blue-900 transition">
                                Admin Panel
                            </Link>
                            
                            <!-- Logout button -->
                            <form @submit.prevent="logout" class="inline">
                                <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                        
                        <!-- Mobile: Hamburger atau Login -->
                        <div class="flex md:hidden">
                            <button 
                                v-if="$page.props.auth?.user" 
                                @click="mobileMenuOpen = !mobileMenuOpen" 
                                type="button"
                                class="p-3 rounded-md text-gray-700 hover:text-blue-800 hover:bg-gray-100 active:bg-gray-200 touch-manipulation"
                                aria-label="Toggle menu"
                            >
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <Link v-else href="/login" class="inline-flex items-center px-4 py-2 bg-blue-800 text-white rounded-lg font-semibold hover:bg-blue-900 transition text-sm">
                                Login
                            </Link>
                        </div>
                        
                        <!-- Desktop: Login button untuk guest -->
                        <Link v-if="!$page.props.auth?.user" href="/login" class="hidden md:inline-flex items-center px-6 py-2 bg-blue-800 text-white rounded-lg font-semibold hover:bg-blue-900 transition">
                            Login
                        </Link>
                    </div>
                </div>
                
            </div>
            
            <!-- Mobile Menu Backdrop -->
            <Transition
                enter-active-class="transition-opacity ease-linear duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity ease-linear duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="$page.props.auth?.user && mobileMenuOpen" @click="mobileMenuOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[70] md:hidden"></div>
            </Transition>
            
            <!-- Mobile Menu Sidebar -->
            <Transition
                enter-active-class="transition ease-in-out duration-300 transform"
                enter-from-class="translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition ease-in-out duration-300 transform"
                leave-from-class="translate-x-0"
                leave-to-class="translate-x-full"
            >
                <div v-if="$page.props.auth?.user && mobileMenuOpen" class="fixed top-0 right-0 bottom-0 w-80 max-w-full bg-white shadow-xl z-[80] md:hidden overflow-y-auto">
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <div class="flex items-center gap-2">
                            <img src="/images/logo.png" alt="Logo" class="h-8 w-8" />
                            <div>
                                <div class="text-sm font-semibold text-blue-800">KP-SPAMS</div>
                                <div class="text-xs text-blue-600">DAMAR WULAN</div>
                            </div>
                        </div>
                        <button 
                            @click="mobileMenuOpen = false" 
                            type="button"
                            class="p-3 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 active:bg-gray-200 touch-manipulation"
                            aria-label="Close menu"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="p-4 pb-8">
                        <div class="flex flex-col space-y-2">
                            <Link href="/dashboard" @click="mobileMenuOpen = false" :class="{
                                'flex items-center px-4 py-3 text-base font-medium rounded-lg transition': true,
                                'bg-blue-50 text-blue-800': isActive('/dashboard'),
                                'text-gray-700 hover:bg-gray-50': !isActive('/dashboard')
                            }">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </Link>
                            <Link href="/cek-pelanggan" @click="mobileMenuOpen = false" :class="{
                                'flex items-center px-4 py-3 text-base font-medium rounded-lg transition': true,
                                'bg-blue-50 text-blue-800': isActive('/cek-pelanggan'),
                                'text-gray-700 hover:bg-gray-50': !isActive('/cek-pelanggan')
                            }">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Cek Pelanggan
                            </Link>
                            <Link href="/peta" @click="mobileMenuOpen = false" :class="{
                                'flex items-center px-4 py-3 text-base font-medium rounded-lg transition': true,
                                'bg-blue-50 text-blue-800': isActive('/peta'),
                                'text-gray-700 hover:bg-gray-50': !isActive('/peta')
                            }">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Peta
                            </Link>
                            <Link href="/tagihan-bulanan" @click="mobileMenuOpen = false" :class="{
                                'flex items-center px-4 py-3 text-base font-medium rounded-lg transition': true,
                                'bg-blue-50 text-blue-800': isActive('/tagihan-bulanan'),
                                'text-gray-700 hover:bg-gray-50': !isActive('/tagihan-bulanan')
                            }">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Tagihan & Pembayaran
                            </Link>
                            <Link href="/laporan" @click="mobileMenuOpen = false" :class="{
                                'flex items-center px-4 py-3 text-base font-medium rounded-lg transition': true,
                                'bg-blue-50 text-blue-800': isActive('/laporan'),
                                'text-gray-700 hover:bg-gray-50': !isActive('/laporan')
                            }">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Laporan
                            </Link>
                            
                            <!-- Admin Menu -->
                            <div v-if="isAdmin || isPengelola" class="border-t border-gray-200 my-4 pt-4">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Admin
                                </div>
                                <Link href="/admin/landing-page" @click="mobileMenuOpen = false" class="flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Landing Page
                                </Link>
                                <Link href="/admin/payment-settings" @click="mobileMenuOpen = false" class="flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pengaturan Pembayaran
                                </Link>
                                <Link href="/admin/map-settings" @click="mobileMenuOpen = false" class="flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    Pengaturan Peta
                                </Link>
                                <Link href="/admin/faqs" @click="mobileMenuOpen = false" class="flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Kelola FAQ
                                </Link>
                                <Link href="/admin/informasi-tarif" @click="mobileMenuOpen = false" class="flex items-center px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-lg transition">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Kelola Informasi Tarif
                                </Link>
                            </div>
                            
                            <Link v-if="$page.props.auth.user.role === 'admin'" href="/admin" @click="mobileMenuOpen = false" class="flex items-center px-4 py-3 text-base font-medium bg-blue-800 text-white rounded-lg hover:bg-blue-900 transition mt-4">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Admin Panel
                            </Link>
                            <button 
                                @click.prevent="handleLogout" 
                                type="button"
                                class="flex items-center w-full px-4 py-4 text-base font-medium bg-red-600 text-white rounded-lg hover:bg-red-700 active:bg-red-800 transition mt-2 touch-manipulation"
                            >
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </nav>

        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';

const page = usePage();
const currentYear = computed(() => new Date().getFullYear());
const mobileMenuOpen = ref(false);
const isScrolled = ref(false);

const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');
const isPengelola = computed(() => page.props.auth?.user?.role === 'pengelola');

const isActive = (path) => {
    return page.url.startsWith(path);
};

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const logout = () => {
    router.post('/logout');
};

const handleLogout = () => {
    mobileMenuOpen.value = false;
    // Small delay to allow menu animation to complete
    setTimeout(() => {
        logout();
    }, 100);
};

// Close mobile menu when route changes
router.on('navigate', () => {
    mobileMenuOpen.value = false;
});

// Prevent body scroll when mobile menu is open
const updateBodyScroll = () => {
    if (mobileMenuOpen.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
};

// Watch for mobile menu changes
import { watch } from 'vue';
watch(mobileMenuOpen, updateBodyScroll);

onUnmounted(() => {
    document.body.style.overflow = '';
});
</script>
