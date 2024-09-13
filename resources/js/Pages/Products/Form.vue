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

// Props to receive the product when editing
const props = defineProps({
    product: Object, // This will be null when creating a product
});

// Initialize the form with useForm, setting initial values
const form = useForm({
    product_name: props.product?.product_name || '',
    brand_id: props.product?.brand_id,
    tag: props.product?.tag,
    product_description: props.product?.product_description || '',
    price: props.product?.price || 0.00,
});

const title = (!!props.product ? 'Edit' : 'Tambah') + ' Product';
const breadcrumbs = [
    { name: 'Home', href: route('dashboard') },
    { name: 'Product', href: route('products.index') },
    { name: !!props.product ? 'Edit' : 'Tambah', href: '#' },
];

const saveAction = () => {
    if (!!props.product) {
        form.put(route("products.update", props.product.id));
    } else {
        form.post(route("products.store"));
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

        <Card class="p-4">
            <form @submit.prevent="saveAction">

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <InputLabel for="product_name" value="Name" />
                        <TextInput id="product_name" v-model="form.product_name" />
                        <InputError :message="form.errors.product_name" />
                    </div>

                    <div>
                        <InputLabel for="brand_id" value="Brand" />
                        <TextInput id="brand_id" v-model="form.brand_id" />
                        <InputError :message="form.errors.brand_id" />
                    </div>

                    <div>
                        <InputLabel for="tag" value="Tag" />
                        <TextInput id="tag" v-model="form.tag" />
                        <InputError :message="form.errors.tag" />
                    </div>

                    <div>
                        <label for="product_description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Deskripsi</label>
                        <textarea id="product_description" v-model="form.product_description"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600"
                            :class="{ 'border-red-500': form.errors.product_description }"></textarea>
                        <span v-if="form.errors.product_description" class="text-red-500 text-sm">
                            {{ form.errors.product_description }}
                        </span>
                    </div>

                    <div>
                        <InputLabel for="price" value="Harga" />
                        <TextInput id="price" v-model="form.price" />
                        <InputError :message="form.errors.price" />
                    </div>
                </div>
                <div class="flex justify-end mt-2">
                    <PrimaryButton type="submit">
                        Simpan
                    </PrimaryButton>
                </div>
            </form>

        </Card>
    </AppLayout>
</template>
