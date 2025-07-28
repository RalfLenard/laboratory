<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { UserCircle, CheckCircle, AlertTriangle, ChevronDown, Search, X, Droplets } from 'lucide-vue-next';

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Chemistry', href: '/chemistry' },
];

// Props from controller
const props = defineProps({
    patients: {
        type: Array,
        default: () => [],
    },
    chemistryTests: {
        type: Array,
        default: () => [],
    },
});

// Reactive state
const selectedPatient = ref(null);
const patientSearchQuery = ref('');
const patientList = ref(props.patients);

// Chemistry form
const chemistryForm = useForm({
    patient_id: null,
    rbs: '',
    fasting: '',
    remarks: '',
    medical_technologist: '',
});

// Filter patient list based on search query
const filteredPatients = computed(() => {
    const query = patientSearchQuery.value.toLowerCase();
    return patientList.value.filter(patient =>
        patient.name.toLowerCase().includes(query) ||
        patient.id.toString().includes(query)
    );
});

// Select a patient from the list
const selectPatient = (patient) => {
    selectedPatient.value = patient;
    patientSearchQuery.value = patient.name;
    chemistryForm.patient_id = patient.id;
};

// Clear patient selection
const clearSelectedPatient = () => {
    selectedPatient.value = null;
    patientSearchQuery.value = '';
    chemistryForm.patient_id = null;
};

// Calculate age from birthdate
const calculateAge = (dateOfBirth) => {
    if (!dateOfBirth) return '';
    const today = new Date();
    const birthDate = new Date(dateOfBirth);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
};

// Format date nicely
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

// Submit the form
const submitChemistryTest = () => {
    if (!selectedPatient.value) {
        alert('Please select a patient first.');
        return;
    }

    chemistryForm.patient_id = selectedPatient.value.id;

    chemistryForm.post('/chemistry-test', {
        onSuccess: () => {
            chemistryForm.reset();
            selectedPatient.value = null;
            patientSearchQuery.value = '';
        },
        onError: (errors) => {
            console.error('Submission errors:', errors);
            if (errors.patient_id) {
                alert('Patient selection error: ' + errors.patient_id);
            } else {
                alert('There was an error submitting the data. Please check the form for details.');
            }
        }
    });
};

// Available RMTs
const medicalTechnologists = [
    'KRISTINA CASSANDRA F. SANTOS, RMT',
    'KATE ANGELINE M. SALAS, RMT',
    'JULLIUS D. ANUSENCION, RMT',
    'MARY GRACE L. BERNARDO, RMT',
    'JANIELLE M. PASAMONTE, RMT',
];
</script>

<template>
    <Head title="Chemistry Tests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
            <div class="py-8 px-6 w-full">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Chemistry Tests</h1>
                    <p class="text-gray-600">Comprehensive chemistry testing platform</p>
                </div>

                <!-- Patient Selection Card -->
                <div class="bg-white rounded-2xl shadow-md p-8 mb-8 border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <User Circle class="h-8 w-8 text-blue-600" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Patient Information</h2>
                            <p class="text-gray-600">Search and select a patient to begin testing</p>
                        </div>
                    </div>

                    <div class="relative mb-6">
                        <div class="relative">
                            <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                            <input v-model="patientSearchQuery" type="text"
                                placeholder="Search patient by name or ID..."
                                class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg" />
                        </div>

                        <!-- Search Results Dropdown -->
                        <div v-if="patientSearchQuery && filteredPatients.length > 0"
                            class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl mt-2 max-h-80 overflow-y-auto shadow-xl">
                            <div v-for="patient in filteredPatients" :key="patient.id" @click="selectPatient(patient)"
                                class="px-6 py-4 cursor-pointer hover:bg-blue-50 transition-colors duration-200 border-b border-gray-100 last:border-b-0">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-semibold text-gray-900 text-lg">{{ patient.name }}</div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            ID: {{ patient.id }} | DOB: {{ formatDate(patient.date_of_birth) }} | {{
                                                patient.gender }}
                                        </div>
                                    </div>
                                    <CheckCircle v-if="selectedPatient && selectedPatient.id === patient.id"
                                        class="h-6 w-6 text-green-500" />
                                </div>
                            </div>
                        </div>

                        <div v-if="patientSearchQuery && filteredPatients.length === 0"
                            class="absolute z-20 w-full bg-white border border-gray-200 rounded-xl mt-2 p-6 text-gray-500 shadow-xl">
                            <div class="text-center">
                                <User  class="h-12 w-12 text-gray-300 mx-auto mb-2" />
                                <p>No patients found matching your search.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Patient Display -->
                    <div v-if="selectedPatient"
                        class="bg-gradient-to-r from-blue-50 to-green-50 border border-blue-200 rounded-xl p-6 animate-fade-in">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-blue-900 mb-3">Selected Patient</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-700"><span class="font-semibold">Name:</span> {{
                                            selectedPatient.name }}</p>
                                        <p class="text-gray-700"><span class="font-semibold">Age:</span> {{
                                            calculateAge(selectedPatient.date_of_birth) }} years old</p>
                                        <p class="text-gray-700"><span class="font-semibold">Gender:</span> {{
                                            selectedPatient.gender }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-700"><span class="font-semibold">DOB:</span> {{
                                            formatDate(selectedPatient.date_of_birth) }}</p>
                                        <p class="text-gray-700"><span class="font-semibold">Company:</span> {{
                                            selectedPatient.company }}</p>
                                        <p class="text-gray-700"><span class="font-semibold">Address:</span> {{
                                            selectedPatient.address }}</p>
                                    </div>
                                </div>
                            </div>
                            <button @click="clearSelectedPatient"
                                class="bg-white text-gray-600 hover:text-red-600 p-2 rounded-full hover:bg-red-50 transition-colors duration-200">
                                <X class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success"
                    class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 animate-fade-in">
                    <div class="flex items-center">
                        <CheckCircle class="h-6 w-6 text-green-500 mr-3" />
                        <div class="text-green-800 font-medium">{{ $page.props.flash.success }}</div>
                    </div>
                </div>

                <div v-if="$page.props.flash?.error"
                    class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 animate-fade-in">
                    <div class="flex items-center">
                        <AlertTriangle class="h-6 w-6 text-red-500 mr-3" />
                        <div class="text-red-800 font-medium">{{ $page.props.flash.error }}</div>
                    </div>
                </div>

                <!-- Main Test Form -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-100">
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Chemistry Test Form</h2>
                        <form @submit.prevent="submitChemistryTest" class="space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- RBS -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">RBS</label>
                                    <input v-model="chemistryForm.rbs" type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Enter RBS value" />
                                    <div v-if="chemistryForm.errors.rbs" class="text-red-500 text-sm mt-1">
                                        {{ chemistryForm.errors.rbs }}
                                    </div>
                                </div>

                                <!-- Fasting -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fasting</label>
                                    <input v-model="chemistryForm.fasting" type="text"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Enter fasting value" />
                                    <div v-if="chemistryForm.errors.fasting" class="text-red-500 text-sm mt-1">
                                        {{ chemistryForm.errors.fasting }}
                                    </div>
                                </div>

                                <!-- Medical Technologist -->
                                <!-- <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Medical Technologist</label>
                                    <select v-model="chemistryForm.medical_technologist"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Medical Technologist</option>
                                        <option v-for="tech in props.medicalTechnologists" :key="tech" :value="tech">
                                            {{ tech }}
                                        </option>
                                    </select>
                                    <div v-if="chemistryForm.errors.medical_technologist" class="text-red-500 text-sm mt-1">
                                        {{ chemistryForm.errors.medical_technologist }}
                                    </div>
                                </div> -->
                            </div>

                            <!-- Remarks -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks</label>
                                <textarea v-model="chemistryForm.remarks" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter remarks here..."></textarea>
                                <div v-if="chemistryForm.errors.remarks" class="text-red-500 text-sm mt-1">
                                    {{ chemistryForm.errors.remarks }}
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Medical Technologist</label>
                                <div class="relative">
                                    <select v-model="chemistryForm.medical_technologist"
                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                        <option value="">Select Medical Technologist</option>
                                        <option v-for="tech in medicalTechnologists" :key="tech" :value="tech">
                                            {{ tech }}
                                        </option>
                                    </select>
                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                </div>
                                <div v-if="chemistryForm.errors.medical_technologist" class="text-red-500 text-sm mt-2 font-medium">
                                    {{ chemistryForm.errors.medical_technologist }}
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex flex-col items-center space-y-4">
                                <button type="submit" :disabled="chemistryForm.processing || !selectedPatient"
                                    class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-green-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <span v-if="chemistryForm.processing" class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Submitting Test...
                                    </span>
                                    <span v-else>Submit Chemistry Test</span>
                                </button>

                                <div v-if="!selectedPatient"
                                    class="text-red-500 text-sm text-center bg-red-50 px-4 py-2 rounded-lg">
                                    ⚠️ Please select a patient to submit tests.
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Custom scrollbar for dropdown */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>