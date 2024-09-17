<script setup>
// Created by: Ariefan Dipokusumo Wibowo, last update 2024-09-20

import { ref, computed, defineProps, onMounted } from 'vue';

const props = defineProps(['data', 'headers', 'sortable', 'pageSize']);

const pageSize = ref(props.pageSize || 10);
const currentPage = ref(1);
const filterValue = ref('');
const columnFilter = ref('');
const sortColumn = ref('');
const ascending = ref(true);

const pageSizeList = computed(() => {
    const sizes = [10, 25, 50, 100];
    if (props.pageSize && !sizes.includes(props.pageSize)) {
        sizes.push(props.pageSize);
    }
    return sizes.sort((a, b) => a - b);
});

const headers = computed(() => {
    const headers = [];
    if (props.data.length > 0) {
        for (const key in props.data[0]) {
            headers.push(key);
        }
    }
    return headers;
});

const sortedData = computed(() => {
    if (props.sortable && sortColumn.value >= 0) {
        const sorted = [...props.data].sort((a, b) => {
            const key = Object.keys({ ...a })[sortColumn.value];
            const valueA = { ...a }[key];
            const valueB = { ...b }[key];
            if (typeof valueA === 'string' && typeof valueB === 'string') {
                const compareValue = valueA.localeCompare(valueB);
                return ascending.value ? compareValue : -compareValue;
            } else {
                return ascending.value ? valueA - valueB : valueB - valueA;
            }
        });
        return sorted || [];
    }
    return props.data || [];
});

const filteredData = computed(() => {
    return sortedData.value.filter(item => {
        return Object.entries(item).some(([key, value]) => {
            return columnFilter.value === '' || key === columnFilter.value;
        }) && Object.values(item).some(value => value?.toString().toLowerCase().includes(filterValue.value.toLowerCase()));
    });
});

const totalPages = computed(() => {
    return Math.ceil(filteredData.value.length / pageSize.value);
});

const displayedData = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filteredData.value.slice(start, end);
});

const toggleSort = (column) => {
    if (props.sortable) {
        if (sortColumn.value === column) {
            ascending.value = !ascending.value;
        } else {
            sortColumn.value = column;
            ascending.value = true;
        }
    }
};

const applyFilter = () => {
    currentPage.value = 1;
};

const goToPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        links.value[currentPage.value - 1].active = false;
        links.value[page - 1].active = true;
        currentPage.value = page;
    }
};

const links = computed(() => {
    const links = [];
    for (let i = 1; i <= totalPages.value; i++) {
        links.push({
            label: i,
            active: i === currentPage.value,
        });
    }
    return links;
});

onMounted(() => { });
// watch(() => columnFilter.value, () => {
//     applyFilter();
// });
</script>

<template>
    <div class="flex justify-between">
        <div class="ml-2 mt-2">
            <span class="join-item text-xs px-1 font-normal">Show</span>
            <select class="select select-bordered select-xs join-item py-0 text-sm" v-model="pageSize"
                @change="applyFilter">
                <option v-for="size in pageSizeList" :value="size" :key="size">{{ size }}</option>
            </select>
            <span class="join-item text-sm px-1 font-normal">entries</span>
        </div>
        <div class="join">
            <select class="select select-bordered select-ghost select-sm join-item py-0 text-sm" v-model="columnFilter"
                @change="applyFilter">
                <option value="">All</option>
                <option v-for="header in headers" :value="header" :key="header">
                    {{ header.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') }}
                </option>
            </select>
            <input class="input input-sm input-bordered join-item" placeholder="Find" v-model="filterValue"
                @input="applyFilter" />
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th v-for="(header, key) in headers" :key="key" class="px-1 py-2 min-w-20">
                    <span class="btn btn-xs btn-ghost" @click="toggleSort(key)">
                        <span>
                            {{ header.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') }}
                            <span v-if="sortable && sortColumn === key" class="ml-1">
                                <font-awesome-icon v-if="ascending" icon="fa-solid fa-caret-up" />
                                <font-awesome-icon v-else icon="fa-solid fa-caret-down" />
                            </span>
                        </span>
                    </span>
                </th>
                <th v-if="$slots.actionButtons">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr class="hover" v-for="(item, index) in displayedData" :key="index">
                <td v-for="(value, key) in item" :key="key">{{ value }}</td>
                <td v-if="$slots.actionButtons">
                    <slot name="actionButtons" :item="item"></slot>
                </td>
            </tr>
        </tbody>
    </table>



    <div class="flex justify-between">
        <div class="text-xs mt-2 ml-2">
            Showing {{ currentPage }} to {{ totalPages }} of {{ filteredData.length }}
        </div>
        <div class="join border border-primary" v-if="totalPages > 1">
            <button class="join-item btn btn-sm p-2 btn-primary btn-outline" @click="goToPage(1)"
                :class="{ 'btn-disabled': currentPage === 1 }">
                <i class="fa-solid fa-angles-left"></i>
            </button>
            <button class="join-item btn btn-sm p-2 btn-primary btn-outline" @click="goToPage(currentPage - 1)"
                :class="{ 'btn-disabled': currentPage === 1 }">
                <i class="fa-solid fa-angle-left"></i>
            </button>
            <button v-for="(link, p) in links " :key="p" class="join-item btn btn-sm btn-primary btn-outline"
                :class="{ 'btn-active': link.active }" @click="goToPage(p + 1)">
                {{ link.label }}
            </button>
            <button class="join-item btn btn-sm p-2 btn-primary btn-outline" @click="goToPage(currentPage + 1)"
                :class="{ 'btn-disabled': currentPage === totalPages }">
                <i class="fa-solid fa-angle-right"></i>
            </button>
            <button class="join-item btn btn-sm p-2 btn-primary btn-outline" @click="goToPage(totalPages)"
                :class="{ 'btn-disabled': currentPage === totalPages }">
                <i class="fa-solid fa-angles-right"></i>
            </button>
        </div>
    </div>
</template>
