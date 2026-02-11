<template>
    <div v-if="links && links.length > 3" class="flex flex-wrap justify-center gap-1 mt-4">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="px-3 py-1 text-sm text-gray-500 border rounded opacity-50 cursor-not-allowed"
                v-html="link.label"
            ></div>
            <Link
                v-else
                :href="link.url"
                class="px-3 py-1 text-sm border rounded hover:bg-white focus:border-blue-500 focus:text-blue-500"
                :class="{ 'bg-blue-600 text-white border-blue-600': link.active, 'bg-white text-gray-700 border-gray-300': !link.active }"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>
