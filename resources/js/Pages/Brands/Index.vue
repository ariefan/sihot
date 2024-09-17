<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import Tooltip from '@/Components/Tooltip.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Swal from 'sweetalert2';
import AppLayout from '@/Layouts/AppLayout.vue';
import SuccessButton from '@/Components/SuccessButton.vue';
import { ActionButton, Breadcrumb, Button, Icon, Pagination, SortIcon } from '@/Components';
import { FwbButton } from 'flowbite-vue';


const props = defineProps({
    brands: Array,
});

const form = useForm({});

const title = 'Merk';
const breadcrumbs = [
    { name: 'Home', href: route('dashboard') },
    { name: title, href: '#' },
];
const columns = [
    { name: 'brand_name', label: 'Brand Name' },
    { name: 'brand_description', label: 'Brand Description' },
];
const perPageOptions = [10, 25, 50, 100];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const sortKey = ref('');
const sortOrder = ref('');

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

const paginatedData = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return orderedBrands.value.slice(start, end);
});

// Sort key setter
const setSortKey = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
};

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
        form.delete(route("brands.destroy", id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
        </template>

        <div class="flex flex-wrap space-y-4 sm:space-y-0 items-center justify-between mb-4">
            <Button color="green" :href="route('brands.create')">
                <template #prefix>
                    <Icon name="plus" />
                </template>
                Tambah
            </Button>
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

        <div class="relative overflow-x-auto overflow-y-hidden shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-300">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th v-for="(column, key) in columns" :key="key" scope="col"
                            class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                            :class="{ 'text-right': column.align === 'right' }" @click="setSortKey(column.name)">
                            <div class="flex items-center justify-between">
                                {{ column.label }}
                                <SortIcon :sortKey="sortKey" :sortOrder="sortOrder" :columnName="column.name" />
                            </div>
                        </th>
                        <th scope="col"
                            class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-right">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="(item, index) in paginatedData" :key="index"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <template v-for="(value, key) in item" :key="key">
                            <td v-if="columns.some(column => column.name === key)" class="px-4 py-2">
                                {{ value }}
                            </td>
                        </template>
                        <td class="py-0 px-4 text-right">
                            <div class="inline-flex rounded-md mt-1.5">
                                <!-- Show Button -->
                                <ActionButton color="cyan" :href="route('brands.show', item.id)"
                                    :data-tooltip-target="`tooltip-edit-${item.id}`" class="rounded-s hidden">
                                    <Icon name="info" class="w-4 h-4" />
                                </ActionButton>
                                <Tooltip :id="`tooltip-show-${item.id}`">Show</Tooltip>
                                <!-- Edit Button -->
                                <ActionButton color="yellow" :href="route('brands.edit', item.id)"
                                    :data-tooltip-target="`tooltip-edit-${item.id}`" class="rounded-s">
                                    <Icon name="pencil" class="w-4 h-4" />
                                </ActionButton>
                                <Tooltip :id="`tooltip-edit-${item.id}`">Edit</Tooltip>
                                <!-- Delete Button -->
                                <ActionButton color="red" @click.prevent="deleteAction(item.id)"
                                    :data-tooltip-target="`tooltip-delete-${item.id}`" class="rounded-e">
                                    <Icon name="close" class="w-4 h-4" />
                                </ActionButton>
                                <Tooltip :id="`tooltip-delete-${item.id}`">Delete</Tooltip>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <Pagination v-model="currentPage" :per-page="perPage" :total-items="totalItems" :total-pages="totalPages"
            :show-labels="false" show-icons />

        <!-- Delete Team Confirmation Modal -->
        <!-- <ConfirmationModal :show="confirmingTeamDeletion" @close="confirmingTeamDeletion = false">
            <template #title>
                Hapus Data
            </template>

            <template #content>
                Are you sure you want to delete this team? Once a team is deleted, all of its resources and data will be
                permanently deleted.
            </template>

            <template #footer>
                <SecondaryButton @click="confirmingTeamDeletion = false">
                    Batal
                </SecondaryButton>

                <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="deleteTeam">
                    Hapus Data
                </DangerButton>
            </template>
        </ConfirmationModal> -->
    </AppLayout>
</template>
