<script setup>
import { ref, computed, watch } from 'vue';

// Props that will be passed from the parent component
const props = defineProps({
    currentPage: Number,
    totalPages: Number,
    startItemIndex: Number,
    endItemIndex: Number,
    totalItems: Number,
});

// Emit events for pagination control
const emit = defineEmits(['changePage', 'nextPage', 'prevPage']);

const goToPage = (page) => {
    emit('changePage', page);
};

const goToNextPage = () => {
    emit('nextPage');
};

const goToPrevPage = () => {
    emit('prevPage');
};
</script>

<template>
    <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4" aria-label="Table navigation">
        <!-- Display of item range -->
        <span
            class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">
            Showing
            <span class="font-semibold text-gray-900 dark:text-white">{{ startItemIndex }}</span>
            to
            <span class="font-semibold text-gray-900 dark:text-white">{{ endItemIndex }}</span>
            of
            <span class="font-semibold text-gray-900 dark:text-white">{{ totalItems }}</span>
        </span>

        <!-- Page navigation controls -->
        <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8 cursor-pointer" id="pagination">
            <!-- First and Previous page buttons -->
            <li v-if="currentPage !== 1">
                <a @click.prevent="goToPage(1)"
                    class="flex items-center justify-center px-1 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m17 16-4-4 4-4m-6 8-4-4 4-4" />
                    </svg>
                </a>
            </li>
            <li v-if="currentPage !== 1">
                <a @click.prevent="goToPrevPage"
                    class="flex items-center justify-center px-1 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m14 8-4 4 4 4" />
                    </svg>
                </a>
            </li>

            <!-- Page Numbers -->
            <li v-for="page in totalPages" :key="'page-' + page">
                <component :is="page === currentPage ? 'a' : 'div'" @click.prevent="goToPage(page)"
                    :aria-current="page === currentPage ? 'page' : undefined"
                    :class="['flex items-center justify-center px-3 h-8 leading-tight border', {
                        'text-blue-600 border-gray-300 bg-blue-50 dark:border-gray-700 dark:bg-gray-700 dark:text-white cursor-default':
                            page === currentPage,
                        'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
                            page !== currentPage,
                    }, { 'rounded-s-lg': currentPage === 1 && page === 1 }, { 'rounded-e-lg': currentPage === totalPages && page === totalPages }]">
                    {{ page }}
                </component>
            </li>

            <!-- Next and Last page buttons -->
            <li v-if="currentPage !== totalPages">
                <a @click.prevent="goToNextPage"
                    class="flex items-center justify-center px-1 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m10 16 4-4-4-4" />
                    </svg>
                </a>
            </li>
            <li v-if="currentPage !== totalPages">
                <a @click.prevent="goToPage(totalPages)"
                    class="flex items-center justify-center px-1 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m7 16 4-4-4-4m6 8 4-4-4-4" />
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</template>
