<script setup>
const props = defineProps({
    clickable: {
        type: Boolean,
        default: false,
    },
});

const baseClasses = 'block w-full px-6 py-4 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700';
const hoverClasses = 'hover:bg-gray-100 dark:hover:bg-gray-700';
const classes = props.clickable ? `${baseClasses} ${hoverClasses}` : baseClasses;
</script>

<template>
    <component :is="clickable ? 'a' : 'div'" :class="classes">
        <slot />
    </component>
</template>
