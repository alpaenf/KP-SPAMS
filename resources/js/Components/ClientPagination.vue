<template>
    <div v-if="totalPages > 1" class="flex flex-wrap justify-center gap-1 mt-4">
         <button 
            :disabled="currentPage === 1"
            @click="$emit('page-change', currentPage - 1)"
            class="px-3 py-1 text-sm border rounded hover:bg-white disabled:opacity-50 bg-white"
            :class="currentPage === 1 ? 'cursor-not-allowed text-gray-400' : 'text-gray-700'"
         >
            &laquo; Prev
         </button>
         
         <template v-for="(page, index) in pages" :key="index">
             <span
                v-if="page === '...'"
                class="px-3 py-1 text-sm text-gray-500 border rounded cursor-default bg-white"
             >...</span>
             <button
                v-else
                @click="$emit('page-change', page)"
                class="px-3 py-1 text-sm border rounded hover:bg-white focus:border-blue-500 focus:text-blue-500 transition-colors"
                :class="page === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300'"
             >
                {{ page }}
             </button>
         </template>

         <button 
            :disabled="currentPage === totalPages"
            @click="$emit('page-change', currentPage + 1)"
            class="px-3 py-1 text-sm border rounded hover:bg-white disabled:opacity-50 bg-white"
            :class="currentPage === totalPages ? 'cursor-not-allowed text-gray-400' : 'text-gray-700'"
         >
            Next &raquo;
         </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true
    },
    totalPages: {
        type: Number,
        required: true
    },
});

defineEmits(['page-change']);

const pages = computed(() => {
    const total = props.totalPages;
    const current = props.currentPage;
    const delta = 1;
    let range = [];
    let rangeWithDots = [];
    let l;

    range.push(1);
    for (let i = current - delta; i <= current + delta; i++) {
        if (i < total && i > 1) {
            range.push(i);
        }
    }
    range.push(total);
    
    // Remove duplicates and sort
    range = [...new Set(range)].sort((a, b) => a - b);

    for (let i of range) {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(i);
        l = i;
    }

    return rangeWithDots;
});
</script>
