<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { reactive, ref, computed, watch } from 'vue'
import { 
    TestTube, 
    User, 
    UserCircle, 
    CheckCircle, 
    AlertTriangle, 
    ChevronDown, 
    Search, 
    X, 
    Save, 
    FileText, 
    Beaker,
    Edit,
    Eye,
    Calendar,
    Clock,
    Activity,
    Trash,
    Printer
} from 'lucide-vue-next'
import { route } from 'ziggy-js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Chemistry',
        href: '/chemistry',
    },
];

// Define props to receive data from the controller
const props = defineProps({
    patients: {
        type: Array,
        default: () => []
    },
    chemistryTests: {
        type: Array,
        default: () => []
    }
})

// Reactive state for selected module and test
const selectedChemistryTest = ref('rbs')
const showResultsSection = ref(true)
const editingTestId = ref(null)

// Patient search and selection
const patientSearchQuery = ref('')
const selectedPatient = ref(null)
const patientList = ref(props.patients)

const filteredPatients = computed(() => {
    if (!patientSearchQuery.value) return []
    const query = patientSearchQuery.value.toLowerCase()
    return patientList.value.filter(patient =>
        patient.name.toLowerCase().includes(query) ||
        patient.id.toString().includes(query)
    )
})

// Computed property to get chemistry tests with patient information
const chemistryTestsWithPatients = computed(() => {
    return props.chemistryTests.map(test => {
        const patient = props.patients.find(p => p.id === test.patient_id)
        return {
            ...test,
            patient: patient || null
        }
    })
})

// Group tests by patient
const testsByPatient = computed(() => {
    const grouped = {}
    chemistryTestsWithPatients.value.forEach(test => {
        if (test.patient) {
            if (!grouped[test.patient.id]) {
                grouped[test.patient.id] = {
                    patient: test.patient,
                    tests: []
                }
            }
            grouped[test.patient.id].tests.push(test)
        }
    })
    return grouped
})

const chemistryForm = useForm({
    patient_id: null,
    rbs: '',
    fasting: '',
    remarks: '',
    medical_technologist: '',
});

const searchPatients = () => {
    // This function now filters the `patientList` which is populated by the backend.
}

const selectPatient = (patient) => {
    selectedPatient.value = patient
    patientSearchQuery.value = patient.name
    chemistryForm.patient_id = patient.id
}

const clearSelectedPatient = () => {
    selectedPatient.value = null
    patientSearchQuery.value = ''
    chemistryForm.patient_id = null
    editingTestId.value = null
}

// Function to load test data for editing
const editTest = (test) => {
    editingTestId.value = test.id
    selectedPatient.value = test.patient
    patientSearchQuery.value = test.patient.name
    
    // Load all form data from the test
    chemistryForm.patient_id = test.patient_id
    chemistryForm.rbs = test.rbs || ''
    chemistryForm.fasting = test.fasting || ''
    chemistryForm.remarks = test.remarks || ''
    chemistryForm.medical_technologist = test.medical_technologist || ''
    
    // Scroll to form
    document.querySelector('.main-form')?.scrollIntoView({ behavior: 'smooth' })
}

// Function to determine test type based on available data
const getTestType = (test) => {
    if (test.rbs ) {
        return 'Random Blood Sugar'
    }

    if (test.fasting) {
        return 'Fasting Blood Sugar'
    }

    return 'Chemistry Test'
}

// Function to format test results for display
const formatTestResults = (test) => {
    const results = []
    
    if (test.rbs) results.push(`RBS: ${test.rbs}`)
    if (test.fasting) results.push(`Fasting: ${test.fasting}`)
    
    return results.slice(0, 3) // Show first 3 results
}

// Helper functions
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

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatDateTime = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Form submission method
const submitChemistryTests = () => {
    if (!selectedPatient.value) {
        alert('Please select a patient first.');
        return;
    }
        
    // Ensure patient_id is set before submission
    chemistryForm.patient_id = selectedPatient.value.id;
        
    // Determine if we're updating or creating
    const isUpdating = editingTestId.value !== null;
    const submitRoute = isUpdating 
        ? route('chemistry.update', editingTestId.value)
        : route('chemistry.store');
    
    const submitMethod = isUpdating ? 'put' : 'post';
    
    chemistryForm[submitMethod](submitRoute, {
        onSuccess: (page) => {
            // Clear the form after successful submission
            chemistryForm.reset();
            selectedPatient.value = null;
            patientSearchQuery.value = '';
            editingTestId.value = null;
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

const medicalTechnologists = [
    'KRISTINA CASSANDRA F. SANTOS, RMT',
    'KATE ANGELINE M. SALAS, RMT',
    'JULLIUS D. ANUSENCION, RMT',
    'MARY GRACE L. BERNARDO, RMT',
    'JANIELLE M. PASAMONTE, RMT',
];

const showDeleteModal = ref(false)
const chemistryDelete = ref(null)

const deleteForm = useForm({})

const chemistryDeleteTest = (test) => {
    chemistryDelete.value = test
    showDeleteModal.value = true
}

const confirmDelete = () => {
    deleteForm.delete(`/chemistry/${chemistryDelete.value}`, {
        onSuccess: () => {
            showDeleteModal.value = false
            chemistryDelete.value = null
        }
    })
}

// print or pdf
const print = (test) => {
    const type = getTestType(test)

    let url = ''
    if (type === 'Random Blood Sugar') {
        url = `/chemistry/${test.id}/rbs-pdf`
    }else if (type === 'Fasting Blood Sugar'){
         url = `/chemistry/${test.id}/fbs-pdf`
    } else {
        url = `/chemistry/${test.id}/chemistry-pdf`
    }

    window.open(url, '_blank')
}

</script>

<template>
    <Head title="Chemistry Tests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-blue-50 via-cyan-50 to-teal-50">
            <div class="w-full py-8 px-6">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-gradient-to-r from-blue-600 to-teal-600 p-4 rounded-2xl shadow-lg">
                            <TestTube class="h-10 w-10 text-white" />
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Chemistry Tests</h1>
                    <p class="text-lg text-gray-600">Comprehensive chemistry testing platform for blood sugar, lipids, and metabolic panels</p>
                </div>

                <!-- Toggle Results/Form View -->
                <div class="flex justify-center mb-8">
                    <div class="flex space-x-2 p-2 bg-white rounded-2xl shadow-lg border border-gray-200">
                        <button 
                            @click="showResultsSection = true"
                            :class="{
                                'bg-blue-600 text-white shadow-lg': showResultsSection,
                                'text-gray-600 hover:text-gray-800': !showResultsSection
                            }"
                            class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center"
                        >
                            <Eye class="h-5 w-5 mr-2" />
                            View Results
                        </button>
                        <button 
                            @click="showResultsSection = false"
                            :class="{
                                'bg-blue-600 text-white shadow-lg': !showResultsSection,
                                'text-gray-600 hover:text-gray-800': showResultsSection
                            }"
                            class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center"
                        >
                            <TestTube class="h-5 w-5 mr-2" />
                            Add/Edit Test
                        </button>
                    </div>
                </div>

                <!-- Results Section -->
                <div v-if="showResultsSection" class="space-y-8">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100">
                        <div class="p-8">
                            <div class="flex items-center mb-8">
                                <div class="bg-gradient-to-r from-blue-500 to-teal-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <Activity class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Laboratory Test Results</h2>
                                    <p class="text-gray-600">View and manage all chemistry test results</p>
                                </div>
                            </div>

                            <!-- Results Display -->
                            <div v-if="Object.keys(testsByPatient).length > 0" class="space-y-6">
                                <div v-for="(patientData, patientId) in testsByPatient" :key="patientId" 
                                     class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                    <!-- Patient Header -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center">
                                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-2xl mr-4 shadow-lg">
                                                <UserCircle class="h-8 w-8 text-white" />
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">{{ patientData.patient.name }}</h3>
                                                <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                                    <span>ID: {{ patientData.patient.id }}</span>
                                                    <span>Age: {{ calculateAge(patientData.patient.date_of_birth) }}</span>
                                                    <span>{{ patientData.patient.gender }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ patientData.tests.length }} Test{{ patientData.tests.length !== 1 ? 's' : '' }}
                                        </div>
                                    </div>

                                    <!-- Tests List -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <div v-for="test in patientData.tests" :key="test.id" 
                                             class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-all duration-200">
                                            <div class="flex items-start justify-between mb-4">
                                                <div>
                                                    <h4 class="font-bold text-gray-900 mb-1">{{ getTestType(test) }}</h4>
                                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                                        <Calendar class="h-4 w-4 mr-1" />
                                                        {{ formatDateTime(test.created_at) }}
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">

                                                    <button 
                                                        @click="print(test); showResultsSection = false"
                                                        class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                                        title="Print Test"
                                                    >
                                                        <Printer class="h-4 w-4" />
                                                    </button>

                                                <button 
                                                    @click="chemistryDeleteTest(test.id); showResultsSection = false"
                                                    class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition-colors duration-200"
                                                    title="Delete Test"
                                                >
                                                    <Trash class="h-4 w-4" />
                                                </button>

                                                <button 
                                                    @click="editTest(test); showResultsSection = false"
                                                    class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                                    title="Edit Test"
                                                >
                                                    <Edit class="h-4 w-4" />
                                                </button>

                                                </div>
                                               
                                            </div>

                                            <!-- Test Results Summary -->
                                            <div class="space-y-2">
                                                <div v-for="result in formatTestResults(test)" :key="result" 
                                                     class="text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded-lg">
                                                    {{ result }}
                                                </div>
                                                <div v-if="formatTestResults(test).length === 0" 
                                                     class="text-sm text-gray-500 italic">
                                                    No results available
                                                </div>
                                            </div>

                                            <!-- Medical Technologist -->
                                            <div v-if="test.medical_technologist" class="mt-4 pt-4 border-t border-gray-200">
                                                <p class="text-xs text-gray-500">Medical Technologist</p>
                                                <p class="text-sm font-medium text-gray-700">{{ test.medical_technologist }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-else class="text-center py-16">
                                <div class="bg-gradient-to-r from-blue-100 to-teal-100 p-6 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                                    <TestTube class="h-12 w-12 text-blue-600" />
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">No Chemistry Tests Found</h3>
                                <p class="text-gray-600 mb-6">Start by adding your first chemistry test using the form below.</p>
                                <button @click="showResultsSection = false" 
                                        class="bg-gradient-to-r from-blue-600 to-teal-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-200">
                                    Add Chemistry Test
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Form Section -->
                <div v-else class="space-y-8">
                    <!-- Patient Selection Card -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100">
                        <div class="p-8">
                            <div class="flex items-center mb-8">
                                <div class="bg-gradient-to-r from-blue-500 to-teal-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <UserCircle class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Patient Information</h2>
                                    <p class="text-gray-600">Search and select a patient to begin testing</p>
                                </div>
                            </div>

                            <div class="relative mb-6">
                                <div class="relative">
                                    <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 h-6 w-6 text-gray-400" />
                                    <input 
                                        v-model="patientSearchQuery" 
                                        type="text"
                                        placeholder="Search patient by name or ID..."
                                        class="w-full pl-14 pr-4 py-4 border border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-lg shadow-sm"
                                    />
                                </div>

                                <!-- Search Results Dropdown -->
                                <div v-if="patientSearchQuery && filteredPatients.length > 0"
                                     class="absolute z-20 w-full bg-white border border-gray-200 rounded-2xl mt-2 max-h-80 overflow-y-auto shadow-2xl">
                                    <div v-for="patient in filteredPatients" :key="patient.id" 
                                         @click="selectPatient(patient)"
                                         class="px-6 py-4 cursor-pointer hover:bg-blue-50 transition-colors duration-200 border-b border-gray-100 last:border-b-0">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="font-semibold text-gray-900 text-lg">{{ patient.name }}</div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    ID: {{ patient.id }} | DOB: {{ formatDate(patient.date_of_birth) }} | {{ patient.gender }} | {{ patient.company }}
                                                </div>
                                            </div>
                                            <CheckCircle v-if="selectedPatient && selectedPatient.id === patient.id" 
                                                       class="h-6 w-6 text-green-500" />
                                        </div>
                                    </div>
                                </div>

                                <div v-if="patientSearchQuery && filteredPatients.length === 0"
                                     class="absolute z-20 w-full bg-white border border-gray-200 rounded-2xl mt-2 p-6 text-gray-500 shadow-2xl">
                                    <div class="text-center">
                                        <UserCircle class="h-12 w-12 text-gray-300 mx-auto mb-2" />
                                        <p>No patients found matching your search.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Patient Display -->
                            <div v-if="selectedPatient"
                                 class="bg-gradient-to-r from-blue-50 to-teal-50 border border-blue-200 rounded-2xl p-6 animate-fade-in">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-xl font-bold text-blue-900 mb-3">Selected Patient</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-gray-700"><span class="font-semibold">Name:</span> {{ selectedPatient.name }}</p>
                                                <p class="text-gray-700"><span class="font-semibold">Age:</span> {{ calculateAge(selectedPatient.date_of_birth) }} years old</p>
                                                <p class="text-gray-700"><span class="font-semibold">Gender:</span> {{ selectedPatient.gender }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-700"><span class="font-semibold">DOB:</span> {{ formatDate(selectedPatient.date_of_birth) }}</p>
                                                <p class="text-gray-700"><span class="font-semibold">Company:</span> {{ selectedPatient.company }}</p>
                                                <p class="text-gray-700"><span class="font-semibold">Address:</span> {{ selectedPatient.address }}</p>
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
                    </div>

                    <!-- Flash Messages -->
                    <div v-if="$page.props.flash?.success"
                         class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 animate-fade-in">
                        <div class="flex items-center">
                            <CheckCircle class="h-6 w-6 text-green-500 mr-3" />
                            <div class="text-green-800 font-medium">{{ $page.props.flash.success }}</div>
                        </div>
                    </div>

                    <div v-if="$page.props.flash?.error"
                         class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6 animate-fade-in">
                        <div class="flex items-center">
                            <AlertTriangle class="h-6 w-6 text-red-500 mr-3" />
                            <div class="text-red-800 font-medium">{{ $page.props.flash.error }}</div>
                        </div>
                    </div>

                    <!-- Main Test Form -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 main-form">
                        <div class="p-8">
                            <div class="flex items-center mb-8">
                                <div class="bg-gradient-to-r from-blue-500 to-teal-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <TestTube class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ editingTestId ? 'Edit Chemistry Test' : 'Chemistry Test Form' }}
                                    </h2>
                                    <p class="text-gray-600">Enter test results and laboratory values</p>
                                </div>
                            </div>

                            <form @submit.prevent="submitChemistryTests" class="space-y-8">
                                <!-- Blood Sugar Panel -->
                                <div class="bg-gradient-to-r from-blue-50 to-teal-50 rounded-2xl p-6 border border-blue-200">
                                    <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                                        <Beaker class="h-6 w-6 mr-2" />
                                        Blood Sugar Panel
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">RBS (mg/dL)</label>
                                            <input 
                                                v-model="chemistryForm.rbs" 
                                                type="text"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                                                placeholder="Random Blood Sugar"
                                            />
                                            <div v-if="chemistryForm.errors.rbs" class="text-red-500 text-sm mt-1">
                                                {{ chemistryForm.errors.rbs }}
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fasting (mg/dL)</label>
                                            <input 
                                                v-model="chemistryForm.fasting" 
                                                type="text"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                                                placeholder="Fasting Blood Sugar"
                                            />
                                            <div v-if="chemistryForm.errors.fasting" class="text-red-500 text-sm mt-1">
                                                {{ chemistryForm.errors.fasting }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remarks and Technologist -->
                                <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
                                    <!-- Remarks -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks</label>
                                        <textarea 
                                            v-model="chemistryForm.remarks" 
                                            rows="4"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 resize-none"
                                            placeholder="Additional notes or observations..."
                                        ></textarea>
                                        <div v-if="chemistryForm.errors.remarks" class="text-red-500 text-sm mt-1">
                                            {{ chemistryForm.errors.remarks }}
                                        </div>
                                    </div>

                                    <!-- Medical Technologist -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Medical Technologist</label>
                                        <div class="relative">
                                            <select 
                                                v-model="chemistryForm.medical_technologist"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 appearance-none bg-white"
                                            >
                                                <option value="">Select Medical Technologist</option>
                                                <option v-for="tech in medicalTechnologists" :key="tech" :value="tech">
                                                    {{ tech }}
                                                </option>
                                            </select>
                                            <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                        </div>
                                        <div v-if="chemistryForm.errors.medical_technologist" class="text-red-500 text-sm mt-1">
                                            {{ chemistryForm.errors.medical_technologist }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex flex-col items-center space-y-4">
                        
                                    <button 
                                        type="submit"
                                        :disabled="chemistryForm.processing || !selectedPatient"
                                        class="bg-gradient-to-r from-blue-600 to-teal-600 text-white px-8 py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                    >
                                        <Save class="h-5 w-5 mr-2" />
                                        {{ editingTestId ? 'Update Test' : 'Save Test Results' }}
                                    </button>
                                    <div v-if="!selectedPatient" class="text-red-500 text-sm text-center bg-red-50 px-4 py-3 rounded-xl border border-red-200">
                                        ⚠️ Please select a patient to submit tests.
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 p-3 rounded-full mr-4">
                        <AlertTriangle class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Confirm Deletion</h3>
                </div>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this chemistry test? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button 
                        @click="showDeleteModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="confirmDelete"
                        :disabled="deleteForm.processing"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors duration-200 disabled:opacity-50"
                    >
                        Delete Test
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
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
</style>
