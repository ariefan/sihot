<script setup>
import { FwbButton } from 'flowbite-vue';
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    class: '',
    color: 'default',
    gradient: null,
    size: 'md',
    shadow: null,
    pill: false,
    square: false,
    outline: false,
    loading: false,
    loadingPosition: 'prefix',
    disabled: false,
    href: '',
    tag: 'button',
});

const goto = (url) => router.visit(url);

const buttonClasses = computed(() => {
    const color = props.color;
    return `
        inline-flex
        items-center
        px-4
        py-2
        bg-${color}-800
        dark:bg-${color}-200
        border
        border-transparent
        rounded-full
        font-semibold
        text-xs
        text-white
        dark:text-${color}-800
        hover:bg-${color}-700
        dark:hover:bg-${color}-100
        focus:bg-${color}-700
        dark:focus:bg-${color}-100
        active:bg-${color}-900
        dark:active:bg-${color}-300
        focus:outline-none
        focus:ring-2
        focus:ring-${color}-500
        focus:ring-offset-2
        dark:focus:ring-offset-${color}-800
        disabled:opacity-50
        transition
        ease-in-out
        duration-150
        `
});
</script>

<template>
    <Link v-if="href" :href="href">
    <fwb-button pill :class="buttonClasses" :color="color" :gradient="gradient" :size="size" :shadow="shadow"
        :pill="pill" :square="square" :outline="outline" :loading="loading" :loading-position="loadingPosition"
        :disabled="disabled" :tag="href ? 'span' : tag">
        <template #prefix>
            <slot v-if="$slots.prefix" name="prefix" />
        </template>
        <slot />
        <template #suffix>
            <slot v-if="$slots.suffix" name="suffix" />
        </template>
    </fwb-button>
    </Link>
</template>
