<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { FwbInput, FwbTextarea, FwbButton } from 'flowbite-vue';

// Props to receive the brand when editing
const props = defineProps({
    brand: Object,
});

// Initialize the form with useForm, setting initial values
const form = useForm({
    brand_name: props.brand?.brand_name || '',
    brand_description: props.brand?.brand_description || '',
});

const title = (!!props.brand ? 'Edit' : 'Tambah') + ' Merk';
const breadcrumbs = [
    { name: 'Home', href: route('dashboard') },
    { name: 'Merk', href: route('brands.index') },
    { name: !!props.brand ? 'Edit' : 'Tambah', href: '#' },
];

const saveAction = () => {
    if (!!props.brand) {
        form.put(route("brands.update", props.brand.id));
    } else {
        form.post(route("brands.store"));
    }
};
</script>

<template>
    <AppLayout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <Breadcrumb :title="title" :breadcrumbs="breadcrumbs" />
            </h2>
        </template>

        <!-- <Card class="p-4"> -->
        <form @submit.prevent="saveAction">

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <fwb-input v-model="form.brand_name" label="Brand Name" />
                    <InputError :message="form.errors.brand_name" />
                </div>

                <div>
                    <fwb-textarea v-model="form.brand_description" :rows="4" label="Description" />
                    <InputError :message="form.errors.brand_description" />
                </div>
            </div>
            <div class="flex justify-end mt-2">
                <fwb-button pill type="submit">Simpan</fwb-button>
            </div>
        </form>

        <!-- </Card> -->
    </AppLayout>
</template>
