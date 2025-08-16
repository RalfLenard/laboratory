<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { AlertTriangle, Boxes, CheckCircle, Edit, Save, Search, Trash2, UserPlus } from 'lucide-vue-next';
import { ref, watch } from 'vue'; // Import 'watch' for search query

// Define props to receive data from the controller
const props = defineProps({
    kits: {
        type: Object, // Changed to Object for paginated data
        default: () => ({
            data: [],
            links: [], // Add links for pagination
            current_page: 1,
            last_page: 1,
            from: 0,
            to: 0,
            total: 0,
        }),
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
        }),
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kits',
        href: '/kits',
    },
];

// Reactive data
const searchQuery = ref(props.filters.search || ''); // Initialize from props.filters
const showDeleteModal = ref(false);
const showKitsModal = ref(false);
const editingKits = ref(null);
const kitsToDelete = ref(null);

const form = useForm({
    kit_name: '',
    kit_types: '',
    kit_lot_no: '',
    kit_expiration_date: '',
});

const deleteForm = useForm({});

// Watch for changes in searchQuery and trigger a server-side search
watch(searchQuery, (newQuery) => {
    router.get('/kits', { search: newQuery }, { preserveState: true, replace: true });
});

// Format date helper
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const openAddKitsModal = () => {
    editingKits.value = null;
    resetForm();
    showKitsModal.value = true;
};

const closeKitsModal = () => {
    showKitsModal.value = false;
    editingKits.value = null;
    resetForm();
};

const saveKits = () => {
    if (editingKits.value) {
        form.put(`/kits/${editingKits.value.id}`, {
            // Changed to form.put for updates
            onSuccess: () => {
                closeKitsModal();
            },
        });
    } else {
        form.post('/kits', {
            onSuccess: () => {
                closeKitsModal();
            },
        });
    }
};

const editKits = (kit) => {
    editingKits.value = kit;
    form.kit_name = kit.kit_name;

    // Ensure correct format for <input type="date">
    form.kit_expiration_date = kit.kit_expiration_date ? new Date(kit.kit_expiration_date).toISOString().substring(0, 10) : '';

    form.kit_lot_no = kit.kit_lot_no;
    form.kit_types = kit.kit_types;

    showKitsModal.value = true;
};

const deleteKits = (kitId) => {
    kitsToDelete.value = kitId;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    deleteForm.delete(`/kits/${kitsToDelete.value}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            kitsToDelete.value = null;
        },
    });
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <Head title="Kits" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gray-50">
            <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash.success" class="mb-6 rounded-md border border-green-200 bg-green-50 p-4">
                    <div class="flex">
                        <CheckCircle class="mr-2 h-5 w-5 text-green-400" />
                        <div class="text-sm text-green-800">{{ $page.props.flash.success }}</div>
                    </div>
                </div>

                <div v-if="$page.props.flash.error" class="mb-6 rounded-md border border-red-200 bg-red-50 p-4">
                    <div class="flex">
                        <AlertTriangle class="mr-2 h-5 w-5 text-red-400" />
                        <div class="text-sm text-red-800">{{ $page.props.flash.error }}</div>
                    </div>
                </div>

                <div class="rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <input
                                        v-model="searchQuery"
                                        type="text"
                                        placeholder="Search kits..."
                                        class="w-150 rounded-md border border-gray-300 py-2 pr-4 pl-10 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                    />
                                </div>
                            </div>
                            <button
                                @click="openAddKitsModal"
                                class="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none"
                            >
                                <Boxes class="mr-2 h-5 w-5" />
                                Add New Kits
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Kit Types</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Kit Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Kit Lot Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">
                                        Kit Expiration Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="kit in kits.data" :key="kit.id" class="hover:bg-gray-50">
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-900">
                                        <div class="truncate" :title="kit.kit_types">
                                            {{ kit.kit_types || '-' }}
                                        </div>
                                    </td>

                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-900">
                                        <div class="truncate" :title="kit.kit_name">
                                            {{ kit.kit_name || '-' }}
                                        </div>
                                    </td>

                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-900">
                                        <div class="truncate" :title="kit.kit_lot_no">
                                            {{ kit.kit_lot_no || '-' }}
                                        </div>
                                    </td>

                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-900">
                                        <div class="truncate" :title="kit.kit_lot_no">
                                            {{ kit.kit_expiration_date || '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <button @click="editKits(kit)" class="flex items-center text-blue-600 hover:text-blue-900">
                                                <Edit class="mr-1 h-4 w-4" />
                                                Edit
                                            </button>
                                            <button @click="deleteKits(kit.id)" class="flex items-center text-red-600 hover:text-red-900">
                                                <Trash2 class="mr-1 h-4 w-4" />
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="kits.data.length === 0" class="py-12 text-center">
                            <Boxes class="mx-auto mb-4 h-12 w-12 text-gray-400" />
                            <p class="text-lg text-gray-500">
                                {{ searchQuery ? 'No kits found matching your search.' : 'No kits registered yet.' }}
                            </p>
                            <p class="mt-2 text-gray-400">
                                {{ searchQuery ? 'Try adjusting your search terms.' : 'Click "Add New Kits" to register your first kits.' }}
                            </p>
                        </div>
                    </div>

                    <div v-if="kits.links.length > 3" class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <nav class="flex items-center justify-center space-x-2">
                            <template v-for="(link, key) in kits.links">
                                <div
                                    v-if="link.url === null"
                                    :key="key"
                                    class="cursor-default rounded-md border border-gray-300 bg-white px-4 py-2 text-sm leading-4 text-gray-400"
                                    v-html="link.label"
                                />
                                <a
                                    v-else
                                    :key="`link-${key}`"
                                    :href="link.url"
                                    :class="[
                                        'rounded-md border px-4 py-2 text-sm leading-4',
                                        {
                                            'border-blue-600 bg-blue-600 text-white': link.active,
                                            'border-gray-300 bg-white text-gray-700 hover:bg-gray-100': !link.active,
                                        },
                                    ]"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>

            <div v-if="showKitsModal" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black">
                <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                    <h2 class="mb-6 flex items-center text-xl font-bold text-gray-900">
                        <Boxes class="mr-2 h-6 w-6 text-green-600" />
                        {{ editingKits ? 'Edit Kits' : 'Add New Kits' }}
                    </h2>

                    <form @submit.prevent="saveKits" class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Kits Types *</label>
                            <select
                                v-model="form.kit_types"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            >
                                <option value="">Select Kits</option>
                                <option value="Syphilis">Syphilis</option>
                                <option value="Dengue">Dengue</option>
                                <option value="HBSAG">HBSAG</option>
                                <option value="HIV">HIV</option>
                            </select>
                            <div v-if="form.errors.kit_types" class="mt-1 text-sm text-red-500">{{ form.errors.kit_types }}</div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Kit Name *</label>
                            <input
                                v-model="form.kit_name"
                                type="text"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                placeholder="Enter kit name"
                            />
                            <div v-if="form.errors.kit_name" class="mt-1 text-sm text-red-500">{{ form.errors.kit_name }}</div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Kit Lot Number *</label>
                            <input
                                v-model="form.kit_lot_no"
                                type="text"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                placeholder="Enter lot number"
                            />
                            <div v-if="form.errors.kit_lot_no" class="mt-1 text-sm text-red-500">{{ form.errors.kit_lot_no }}</div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Expiration Date *</label>
                            <input
                                v-model="form.kit_expiration_date"
                                type="date"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                            <div v-if="form.errors.kit_expiration_date" class="mt-1 text-sm text-red-500">{{ form.errors.kit_expiration_date }}</div>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Save class="mr-2 inline h-4 w-4" />
                                <span v-if="form.processing">Processing...</span>
                                <span v-else>{{ editingKits ? 'Update Kit' : 'Add Kit' }}</span>
                            </button>
                            <button
                                type="button"
                                @click="closeKitsModal"
                                class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="showDeleteModal" class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black">
                <div class="mx-4 max-w-sm rounded-lg bg-white p-6">
                    <div class="mb-4 flex items-center">
                        <AlertTriangle class="mr-2 h-6 w-6 text-red-500" />
                        <h3 class="text-lg font-semibold">Confirm Delete</h3>
                    </div>
                    <p class="mb-4 text-gray-600">Are you sure you want to delete this kit? This action cannot be undone.</p>
                    <div class="flex space-x-3">
                        <button
                            @click="confirmDelete"
                            :disabled="deleteForm.processing"
                            class="flex-1 rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700 disabled:opacity-50"
                        >
                            <span v-if="deleteForm.processing">Deleting...</span>
                            <span v-else>Delete</span>
                        </button>
                        <button
                            @click="showDeleteModal = false"
                            class="flex-1 rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
