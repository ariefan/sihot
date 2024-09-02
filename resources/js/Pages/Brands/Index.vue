<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import Tooltip from '@/Components/Tooltip.vue';
import Swal from 'sweetalert2';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    brands: Array,
    alert: Object,
});

const form = useForm({});

const perPageOptions = [10, 25, 50, 100];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const sortKey = ref('brand_name');
const sortOrder = ref('asc');
const uniqueKey = ref(0);

// Filtered and ordered brands based on search query and sort parameters
const filteredBrands = computed(() => {
    return searchQuery.value
        ? props.brands.filter((brand) =>
            brand.brand_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            brand.brand_description.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        : props.brands;
});

const orderedBrands = computed(() => {
    return [...filteredBrands.value].sort((a, b) => {
        let modifier = sortOrder.value === 'asc' ? 1 : -1;
        if (a[sortKey.value] < b[sortKey.value]) return -1 * modifier;
        if (a[sortKey.value] > b[sortKey.value]) return 1 * modifier;
        return 0;
    });
});

// Computed properties for pagination
const totalItems = computed(() => orderedBrands.value.length);
const totalPages = computed(() => Math.ceil(totalItems.value / perPage.value));

const paginatedBrands = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return orderedBrands.value.slice(start, end);
});

// Item range display
const startItemIndex = computed(() => (currentPage.value - 1) * perPage.value + 1);
const endItemIndex = computed(() => Math.min(currentPage.value * perPage.value, totalItems.value));

// Change page handlers
const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        uniqueKey.value++; // Change the unique key to force `tbody` re-render
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value += 1;
        uniqueKey.value++; // Change the unique key to force `tbody` re-render
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value -= 1;
        uniqueKey.value++; // Change the unique key to force `tbody` re-render
    }
};

// Sort key setter
const setSortKey = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
    uniqueKey.value++; // Change the unique key to force `tbody` re-render
};

// Watchers to reset the page and force re-render when filters change
watch([perPage, searchQuery], () => {
    currentPage.value = 1;
    uniqueKey.value++; // Change the unique key to force `tbody` re-render
});

// Delete brand with confirmation
const deleteAction = async (id) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
    });

    if (result.isConfirmed) {
        // Perform the deletion logic here, for example:
        uniqueKey.value++;
        form.delete(route("brands.destroy", id), {
            preserveScroll: true,
        });
        // Swal.fire('Deleted!', 'Your data has been deleted.', 'success');
    }
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <Card>
            <!-- Alerts -->
            <div id="alert" v-if="alert.success || alert.error || alert.info"
                class="flex items-center p-4 mb-4 text-sm rounded-lg border" :class="{
                    'text-green-800 border-green-300 bg-green-50 dark:bg-green-950 dark:text-green-400 dark:border -green-800': alert.success,
                    'text-red-800 border-red-300 bg-red-50 dark:bg-red-950 dark:text-red-400 dark:border-red-800': alert.error,
                    'text-blue-800 border-blue-300 bg-blue-50 dark:bg-blue-950 dark:text-blue-400 dark:border-blue-800': alert.info,
                }" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Alert</span>
                <div>
                    <span v-if="alert.success">{{ alert.success }}</span>
                    <span v-else-if="alert.error">{{ alert.error }}</span>
                    <span v-else-if="alert.info">{{ alert.info }}</span>
                </div>
                <button type="button"
                    class="ms-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex items-center justify-center h-8 w-8"
                    :class="{
                        'bg-green-50 text-green-500 focus:ring-green-400 hover:bg-green-200 dark:bg-green-950 dark:text-green-400 dark:hover:bg-green-700': alert.success,
                        'bg-red-50 text-red-500 focus:ring-red-400 hover:bg-red-200 dark:bg-red-950 dark:text-red-400 dark:hover:bg-red-700': alert.error,
                        'bg-blue-50 text-blue-500 focus:ring-blue-400 hover:bg-blue-200 dark:bg-blue-950 dark:text-blue-400 dark:hover:bg-blue-700': alert.info,
                    }" data-dismiss-target="#alert" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>

            <!-- Table -->
            <div class="relative overflow-x-auto">
                <div class="flex flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
                    <div class="flex items-center space-x-2">
                        <select id="perPage" v-model="perPage"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-auto p-1.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option :value="option" v-for="option in perPageOptions" :key="'perPage-' + option">
                                {{ option }}
                            </option>
                        </select>
                        <label for="perPage" class="block text-sm font-medium text-gray-900 dark:text-white">
                            entries per page
                        </label>
                    </div>
                    <div class="relative">
                        <label for="table-search" class="sr-only">Search</label>
                        <div class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="text" id="table-search" v-model="searchQuery"
                            class="block p-1.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search for items">
                    </div>
                </div>
            </div>

            <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-300">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                            @click="setSortKey('brand_name')">
                            <div class="flex items-center justify-between">
                                Brand Name
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'text-black dark:text-white': sortKey === 'brand_name' && sortOrder === 'asc', 'text-gray-500 dark:text-gray-400': !(sortKey === 'brand_name' && sortOrder === 'asc') }"
                                        d="M12.832 3.445a1 1 0 0 0-1.664 0l-4 6A1 1 0 0 0 8 11h8a1 1 0 0 0 .832-1.555l-4-6Z" />
                                    <path
                                        :class="{ 'text-black dark:text-white': sortKey === 'brand_name' && sortOrder === 'desc', 'text-gray-500 dark:text-gray-400': !(sortKey === 'brand_name' && sortOrder === 'desc') }"
                                        d="M11.168 20.555a1 1 0 0 0 1.664 0l4-6A1 1 0 0 0 16 13H8a1 1 0 0 0-.832 1.555l4 6Z" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                            @click="setSortKey('brand_description')">
                            <div class="flex items-center justify-between">
                                Description
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'text-black dark:text-white': sortKey === 'brand_description' && sortOrder === 'asc', 'text-gray-500 dark:text-gray-400': !(sortKey === 'brand_description' && sortOrder === 'asc') }"
                                        d="M12.832 3.445a1 1 0 0 0-1.664 0l-4 6A1 1 0 0 0 8 11h8a1 1 0 0 0 .832-1.555l-4-6Z" />
                                    <path
                                        :class="{ 'text-black dark:text-white': sortKey === 'brand_description' && sortOrder === 'desc', 'text-gray-500 dark:text-gray-400': !(sortKey === 'brand_description' && sortOrder === 'desc') }"
                                        d="M11.168 20.555a1 1 0 0 0 1.664 0l4-6A1 1 0 0 0 16 13H8a1 1 0 0 0-.832 1.555l4 6Z" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                            @click="setSortKey('created_at')">
                            <div class="flex items-center justify-between">
                                Created At
                                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'text-black dark:text-white': sortKey === 'created_at' && sortOrder === 'asc', 'text-gray-500 dark:text-gray-400': !(sortKey === 'created_at' && sortOrder === 'asc') }"
                                        d="M12.832 3.445a1 1 0 0 0-1.664 0l-4 6A1 1 0 0 0 8 11h8a1 1 0 0 0 .832-1.555l-4-6Z" />
                                    <path
                                        :class="{ 'text-black dark:text-white': sortKey === 'created_at' && sortOrder === 'desc', 'text-gray-500 dark:text-gray-400': !(sortKey === 'created_at' && sortOrder === 'desc') }"
                                        d="M11.168 20.555a1 1 0 0 0 1.664 0l4-6A1 1 0 0 0 16 13H8a1 1 0 0 0-.832 1.555l4 6Z" />
                                </svg>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody :key="uniqueKey">
                    <tr v-for="brand in paginatedBrands" :key="brand.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-4 py-2">
                            {{ brand.brand_name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ brand.brand_description }}
                        </td>
                        <td class="px-4 py-2">
                            {{ new Date(brand.created_at).toLocaleDateString() }}
                        </td>
                        <td class="m-0 p-0">
                            <div class="inline-flex rounded-md mt-1">
                                <!-- Show Button -->
                                <Link :href="route('brands.show', brand.id)"
                                    :data-tooltip-target="`tooltip-show-${brand.id}`"
                                    class="px-1.5 py-1 text-xs font-medium text-cyan-700 bg-cyan-200 border border-cyan-300 rounded-s-lg hover:bg-cyan-300 focus:z-10 focus:ring-2 focus:ring-cyan-700 dark:bg-cyan-800 dark:border-cyan-700 dark:text-cyan-100 dark:hover:bg-cyan-700 dark:focus:ring-cyan-600">
                                <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                        clip-rule="evenodd" />
                                </svg>
                                </Link>
                                <Tooltip :id="`tooltip-show-${brand.id}`">Show</Tooltip>
                                <!-- Edit Button -->
                                <Link :href="route('brands.edit', brand.id)"
                                    :data-tooltip-target="`tooltip-edit-${brand.id}`"
                                    class="px-1.5 py-1 text-xs font-medium text-yellow-700 bg-yellow-200 border-t border-b border-yellow-300 hover:bg-yellow-300 focus:z-10 focus:ring-2 focus:ring-yellow-700 dark:bg-yellow-800 dark:border-yellow-700 dark:text-yellow-100 dark:hover:bg-yellow-700 dark:focus:ring-yellow-600">
                                <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path fill-rule="evenodd"
                                        d="M14 4.182A4.136 4.136 0 0 1 16.9 3c1.087 0 2.13.425 2.899 1.182A4.01 4.01 0 0 1 21 7.037c0 1.068-.43 2.092-1.194 2.849L18.5 11.214l-5.8-5.71 1.287-1.31.012-.012Zm-2.717 2.763L6.186 12.13l2.175 2.141 5.063-5.218-2.141-2.108Zm-6.25 6.886-1.98 5.849a.992.992 0 0 0 .245 1.026 1.03 1.03 0 0 0 1.043.242L10.282 19l-5.25-5.168Zm6.954 4.01 5.096-5.186-2.218-2.183-5.063 5.218 2.185 2.15Z"
                                        clip-rule="evenodd" />
                                </svg>
                                </Link>
                                <Tooltip :id="`tooltip-edit-${brand.id}`">Edit</Tooltip>
                                <!-- Delete Button -->
                                <a @click.prevent="deleteAction(brand.id)"
                                    :data-tooltip-target="`tooltip-delete-${brand.id}`"
                                    class="px-1.5 py-1 text-xs font-medium text-red-700 bg-red-200 border border-red-300 rounded-e-lg hover:bg-red-300 focus:z-10 focus:ring-2 focus:ring-red-700 dark:bg-red-800 dark:border-red-700 dark:text-red-100 dark:hover:bg-red-700 dark:focus:ring-red-600">
                                    <svg class="w-4 h-4 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                    </svg>
                                </a>
                                <Tooltip :id="`tooltip-delete-${brand.id}`">Delete</Tooltip>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <nav class="flex items-center flex-column flex-wrap md:flex-row justify-between pt-4 cursor-pointer"
                aria-label="Table navigation">
                <span
                    class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">
                    Showing
                    <span class="font-semibold text-gray-900 dark:text-white">{{ startItemIndex }}</span>
                    to
                    <span class="font-semibold text-gray-900 dark:text-white">{{ endItemIndex }}</span>
                    of
                    <span class="font-semibold text-gray-900 dark:text-white">{{ totalItems }}</span>
                </span>
                <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
                    <li>
                        <a @click.prevent="changePage(1)"
                            class="flex items-center justify-center px-1 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m17 16-4-4 4-4m-6 8-4-4 4-4" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a @click.prevent="prevPage"
                            class="flex items-center justify-center px-1 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m14 8-4 4 4 4" />
                            </svg>
                        </a>
                    </li>
                    <li v-for="page in totalPages" :key="'page-' + page">
                        <a @click.prevent="changePage(page)" :aria-current="page === currentPage ? 'page' : undefined"
                            :class="['flex items-center justify-center px-3 h-8 leading-tight border', {
                                'text-blue-600 border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 hover:bg-blue-900 hover:text-blue-300 dark:border-gray-700 dark:bg-gray-700 dark:text-white':
                                    page === currentPage,
                                'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
                                    page !== currentPage,
                            }]">
                            {{ page }}
                        </a>
                    </li>
                    <li>
                        <a @click.prevent="nextPage"
                            class="flex items-center justify-center px-1 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m10 16 4-4-4-4" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a @click.prevent="changePage(totalPages)"
                            class="flex items-center justify-center px-1 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m7 16 4-4-4-4m6 8 4-4-4-4" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </nav>
        </Card>
    </AppLayout>
</template>
