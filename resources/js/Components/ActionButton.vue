<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
    type: {
        type: String,
        default: 'button',
    },
    color: {
        type: String,
        default: 'blue',
    },
    href: String,
});

const goto = (url) => router.visit(url);
</script>

<template>
    <button @click="href ? goto(href) : null" :type="type" :class="[
        `px-1.5 py-1 text-xs font-medium border hover:bg-${color}-300 focus:z-10 focus:ring-2`,
        `text-${color}-700 bg-${color}-200 border-${color}-300 focus:ring-${color}-700`,
        `dark:bg-${color}-800 dark:border-${color}-700 dark:text-${color}-100 dark:hover:bg-${color}-700 dark:focus:ring-${color}-600`
    ]">
        <slot />
    </button>
</template>
