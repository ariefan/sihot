<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

// Props to receive the brand when editing
const props = defineProps({
    brand: Object, // This will be null when creating a brand
});

// Initialize the form with useForm, setting initial values
const form = useForm({
    brand_name: props.brand?.brand_name || '',
    brand_description: props.brand?.brand_description || '',
});

const saveAction = () => {
    if (!!props.brand) {
        form.put(route("brands.update", props.brand.id), {
            onSuccess: () => form.reset('brand_name', 'brand_description'),
        });
    } else {
        form.post(route("brands.store"), {
            onSuccess: () => form.reset('brand_name', 'brand_description'),
        });
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
            <h1 class="text-2xl font-bold mb-4">{{ !!brand ? 'Edit Brand' : 'Create Brand' }}</h1>
            <form @submit.prevent="saveAction">

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <InputLabel for="brand_name" value="Brand Name" />
                        <TextInput id="brand_name" v-model="form.brand_name" />
                        <InputError :message="form.errors.brand_name" />
                    </div>

                    <div>
                        <label for="brand_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
                        <textarea id="brand_description" v-model="form.brand_description"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                            :class="{ 'border-red-500': form.errors.brand_description }"></textarea>
                        <span v-if="form.errors.brand_description" class="text-red-500 text-sm">
                            {{ form.errors.brand_description }}
                        </span>
                    </div>
                </div>
                <div class="flex justify-end">
                    <Link :href="route('brands.index')" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                    Cancel
                    </Link>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Save
                    </button>
                </div>
            </form>

        </Card>
    </AppLayout>
</template>
