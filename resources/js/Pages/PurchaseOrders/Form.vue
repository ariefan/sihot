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

// Props to receive the purchaseOrder when editing
const props = defineProps({
    purchaseOrder: Object, // This will be null when creating a purchaseOrder
});

// Initialize the form with useForm, setting initial values
const form = useForm({
    supplier_id: props.purchaseOrder?.supplier_id || '',
    user_id: props.purchaseOrder?.user_id || '',
    order_date: props.purchaseOrder?.order_date || '',
    status: props.purchaseOrder?.status || '',
    total_amount: props.purchaseOrder?.total_amount || '',
});

const title = (!!props.purchaseOrder ? 'Edit' : 'Tambah') + ' Purchase Order';
const breadcrumbs = [
    { name: 'Home', href: route('dashboard') },
    { name: 'Purchase Order', href: route('purchaseOrders.index') },
    { name: !!props.purchaseOrder ? 'Edit' : 'Tambah', href: '#' },
];

const saveAction = () => {
    if (!!props.purchaseOrder) {
        form.put(route("purchaseOrders.update", props.purchaseOrder.id));
    } else {
        form.post(route("purchaseOrders.store"));
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

        <Card>
            <form @submit.prevent="saveAction">

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <InputLabel for="supplier_id" value="Supplier ID" />
                        <TextInput id="supplier_id" v-model="form.supplier_id" />
                        <InputError :message="form.errors.supplier_id" />
                    </div>
                    <div>
                        <InputLabel for="user_id" value="User ID" />
                        <TextInput id="user_id" v-model="form.user_id" />
                        <InputError :message="form.errors.user_id" />
                    </div>
                    <div>
                        <InputLabel for="order_date" value="Order Date" />
                        <TextInput id="order_date" v-model="form.order_date" />
                        <InputError :message="form.errors.order_date" />
                    </div>
                    <div>
                        <InputLabel for="status" value="Status" />
                        <TextInput id="status" v-model="form.status" />
                        <InputError :message="form.errors.status" />
                    </div>
                    <div>
                        <InputLabel for="total_amount" value="Total Amount" />
                        <TextInput id="total_amount" v-model="form.total_amount" />
                        <InputError :message="form.errors.total_amount" />
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
