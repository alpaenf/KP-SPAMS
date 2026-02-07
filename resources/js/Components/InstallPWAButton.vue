<template>
    <div v-if="showInstallButton">
        <button
            @click="installPWA"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-105"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m0 0l-4-4m4 4l4-4M3 20h18"/>
            </svg>
            Install Aplikasi
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const showInstallButton = ref(false);
let deferredPrompt = null;

onMounted(() => {
    // Listen untuk beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent default prompt
        e.preventDefault();
        
        // Store event untuk dipake nanti
        deferredPrompt = e;
        
        // Show install button
        showInstallButton.value = true;
    });

    // Hide button setelah installed
    window.addEventListener('appinstalled', () => {
        showInstallButton.value = false;
        deferredPrompt = null;
    });

    // Check apakah sudah installed (standalone mode)
    if (window.matchMedia('(display-mode: standalone)').matches) {
        showInstallButton.value = false;
    }
});

const installPWA = async () => {
    if (!deferredPrompt) {
        // Fallback: kasih instruksi manual
        alert('Untuk install aplikasi:\n\n' +
              'Android Chrome: Tap menu (⋮) > "Install app"\n' +
              'iPhone Safari: Tap Share (📤) > "Add to Home Screen"');
        return;
    }

    // Show install prompt
    deferredPrompt.prompt();

    // Wait for user response
    const { outcome } = await deferredPrompt.userChoice;
    
    console.log(`User ${outcome === 'accepted' ? 'accepted' : 'dismissed'} the install prompt`);

    // Clear deferredPrompt
    deferredPrompt = null;
    
    if (outcome === 'accepted') {
        showInstallButton.value = false;
    }
};
</script>
