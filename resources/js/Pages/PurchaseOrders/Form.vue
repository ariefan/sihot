<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { AutoComplete, Breadcrumb, Button, Card, DataTable, Icon, InputError, InputLabel, TextInput } from '@/Components';

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

const newItem = ref({
    product_id: '',
    product_label: '',
    quantity: 0,
    unit: 0,
});

const product_label = ref('test');
const items = ref([]);

const addRow = () => {
    // Add the new item to the items array
    items.value.push({ ...newItem.value })
    // Reset the newItem values after adding
    newItem.value = {
        product_id: '',
        product_label: '',
        quantity: 0,
        unit: 0,
    }
}

const title = (!!props.purchaseOrder ? 'Edit' : 'Tambah') + ' Purchase Order';
const breadcrumbs = [
    { name: 'Home', href: route('dashboard') },
    { name: 'Purchase Order', href: route('purchase-orders.index') },
    { name: !!props.purchaseOrder ? 'Edit' : 'Tambah', href: '#' },
];

const saveAction = () => {
    if (!!props.purchaseOrder) {
        form.put(route("purchase-orders.update", props.purchaseOrder.id));
    } else {
        form.post(route("purchase-orders.store"));
    }
};
</script>

<template>
    <AppLayout :title="title">
        <Card>
            <template #header class="flex">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ title }}
                </h2>
            </template>

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
                </div>

                <div class="card-header px-4 py-2 border-b border-gray-200 dark:border-gray-700"></div>
                <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight my-4">
                    Daftar Barang
                </h2>
                <div class="grid grid-cols-4 gap-2">
                    <div>
                        <InputLabel for="product_id" value="Barang" />
                        <AutoComplete v-model="newItem.product_id" @update:label="newItem.product_label = $event"
                            api-url="/api/products/suggestions" placeholder="Cari Barang..." :debounce-time="300" />
                        <p>Selected Product ID: {{ newItem.product_id }}</p>
                        <p>Selected Product Label: {{ newItem.product_label }}</p>
                    </div>
                    <div>
                        <InputLabel for="quantity" value="Jumlah" />
                        <TextInput type="number" id="quantity" v-model="newItem.quantity" />
                        <InputError :message="form.errors.quantity" />
                    </div>
                    <div>
                        <InputLabel for="unit" value="Satuan" />
                        <TextInput type="number" id="unit" v-model="newItem.unit" />
                        <InputError :message="form.errors.unit" />
                    </div>
                    <div>
                        <InputLabel for="unit" value="&nbsp;" />
                        <Button type="button" color="green" @click="addRow">
                            Tambah
                        </Button>
                    </div>
                </div>

                <div class="relative overflow-x-auto overflow-y-hidden shadow-md sm:rounded-lg mt-2">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-700 dark:text-gray-300">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Barang
                                </th>
                                <th class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Jumlah
                                </th>
                                <th class="px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Satuan
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in items" :key="index"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-4 py-1">
                                    <TextInput class="py-1" v-model="item.product_label" />
                                    <TextInput class="py-1" v-model="item.product_id" />
                                </td>
                                <td class="px-4 py-1">
                                    <TextInput class="py-1" type="number" v-model="item.quantity" />
                                </td>
                                <td class="px-4 py-1">
                                    <TextInput class="py-1" type="number" v-model="item.unit" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
