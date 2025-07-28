<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { reactive, ref, computed, watch } from 'vue'
import { 
    Droplets, 
    User, 
    UserCircle, 
    CheckCircle, 
    AlertTriangle, 
    ChevronDown, 
    Search, 
    X, 
    Save, 
    FileText, 
    TestTube,
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
        title: 'Hematology',
        href: '/hematology',
    },
];

// Define props to receive data from the controller
const props = defineProps({
    patients: {
        type: Array,
        default: () => []
    },
    hematologyTests: {
        type: Array,
        default: () => []
    }
})

// Reactive state for selected module and test
const selectedHematologyTest = ref('cbc')
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

// Computed property to get hematology tests with patient information
const hematologyTestsWithPatients = computed(() => {
    return props.hematologyTests.map(test => {
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
    hematologyTestsWithPatients.value.forEach(test => {
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

const hematologyForm = useForm({
    patient_id: null,
    // CBC Fields
    cbc_wbc: '',
    cbc_neu: '',
    cbc_lym: '',
    cbc_mon: '',
    cbc_eos: '',
    cbc_bas: '',
    cbc_rbc: '',
    cbc_hgb: '',
    cbc_hct: '',
    cbc_mcv: '',
    cbc_mch: '',
    cbc_mchc: '',
    cbc_plt: '',
    cbc_remarks: '',
    // Blood Typing Fields
    bt_abo_group: '',
    bt_rh: '',
    medical_technologist: '',
});

const searchPatients = () => {
    // This function now filters the `patientList` which is populated by the backend.
}

const selectPatient = (patient) => {
    selectedPatient.value = patient
    patientSearchQuery.value = patient.name
    hematologyForm.patient_id = patient.id
}

const clearSelectedPatient = () => {
    selectedPatient.value = null
    patientSearchQuery.value = ''
    hematologyForm.patient_id = null
    editingTestId.value = null
}

// Function to load test data for editing
const editTest = (test) => {
    editingTestId.value = test.id
    selectedPatient.value = test.patient
    patientSearchQuery.value = test.patient.name
    
    // Load all form data from the test
    hematologyForm.patient_id = test.patient_id
    hematologyForm.cbc_wbc = test.cbc_wbc || ''
    hematologyForm.cbc_neu = test.cbc_neu || ''
    hematologyForm.cbc_lym = test.cbc_lym || ''
    hematologyForm.cbc_mon = test.cbc_mon || ''
    hematologyForm.cbc_eos = test.cbc_eos || ''
    hematologyForm.cbc_bas = test.cbc_bas || ''
    hematologyForm.cbc_rbc = test.cbc_rbc || ''
    hematologyForm.cbc_hgb = test.cbc_hgb || ''
    hematologyForm.cbc_hct = test.cbc_hct || ''
    hematologyForm.cbc_mcv = test.cbc_mcv || ''
    hematologyForm.cbc_mch = test.cbc_mch || ''
    hematologyForm.cbc_mchc = test.cbc_mchc || ''
    hematologyForm.cbc_plt = test.cbc_plt || ''
    hematologyForm.cbc_remarks = test.cbc_remarks || ''
    hematologyForm.bt_abo_group = test.bt_abo_group || ''
    hematologyForm.bt_rh = test.bt_rh || ''
    hematologyForm.medical_technologist = test.medical_technologist || ''
    
    // Scroll to form
    document.querySelector('.main-form')?.scrollIntoView({ behavior: 'smooth' })
}

// Function to determine test type based on available data
const getTestType = (test) => {
    if (test.cbc_wbc || test.cbc_rbc || test.cbc_hgb || test.cbc_hct || test.cbc_plt) {
        return 'CBC'
    }
    if (test.bt_abo_group || test.bt_rh) {
        return 'Blood Typing'
    }
    return 'Hematology Test'
}

// Function to format test results for display
const formatTestResults = (test) => {
    const results = []
    
    if (test.cbc_wbc) results.push(`WBC: ${test.cbc_wbc}`)
    if (test.cbc_rbc) results.push(`RBC: ${test.cbc_rbc}`)
    if (test.cbc_hgb) results.push(`HGB: ${test.cbc_hgb}`)
    if (test.cbc_hct) results.push(`HCT: ${test.cbc_hct}`)
    if (test.cbc_plt) results.push(`PLT: ${test.cbc_plt}`)
    if (test.bt_abo_group) results.push(`ABO: ${test.bt_abo_group}`)
    if (test.bt_rh) results.push(`Rh: ${test.bt_rh}`)
    
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
const submitHematologyTests = () => {
    if (!selectedPatient.value) {
        alert('Please select a patient first.');
        return;
    }
        
    // Ensure patient_id is set before submission
    hematologyForm.patient_id = selectedPatient.value.id;
        
    // Determine if we're updating or creating
    const isUpdating = editingTestId.value !== null;
    const submitRoute = isUpdating 
        ? route('hematology.update', editingTestId.value)
        : route('hematology.store');
    
    const submitMethod = isUpdating ? 'put' : 'post';
    
    hematologyForm[submitMethod](submitRoute, {
        onSuccess: (page) => {
            // Clear the form after successful submission
            hematologyForm.reset();
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

// Dropdown options
const typeOptions = ['A', 'B', 'AB', 'O'];
const signsOptions = ['Positive', 'Negative'];

const medicalTechnologists = [
    'KRISTINA CASSANDRA F. SANTOS, RMT',
    'KATE ANGELINE M. SALAS, RMT',
    'JULLIUS D. ANUSENCION, RMT',
    'MARY GRACE L. BERNARDO, RMT',
    'JANIELLE M. PASAMONTE, RMT',
];



const showDeleteModal = ref(false)
const hematologyDelete = ref(null)

const deleteForm = useForm({})

const hematologyDeleteTest = (test) => {
    hematologyDelete.value = test
    showDeleteModal.value = true
}

const confirmDelete = () => {
    deleteForm.delete(`/hematology/${hematologyDelete.value}`, {
        onSuccess: () => {
            showDeleteModal.value = false
            hematologyDelete.value = null
        }
    })
}

// print or pdf
const print = (test) => {
    const type = getTestType(test)

    let url = ''
    if (type === 'CBC') {
        url = `/hematology/${test.id}/cbc-pdf`
    } else if (type === 'Blood Typing') {
        url = `/hematology/${test.id}/blood-type-pdf`
    } else {
        alert('Unsupported test type.')
        return
    }

    window.open(url, '_blank')
}

</script>

<template>
    <Head title="Hematology Tests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-red-50 via-pink-50 to-rose-50">
            <div class="w-full py-8 px-6">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-gradient-to-r from-red-600 to-pink-600 p-4 rounded-2xl shadow-lg">
                            <Droplets class="h-10 w-10 text-white" />
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Hematology Tests</h1>
                    <p class="text-lg text-gray-600">Complete Blood Count (CBC) and Blood Typing (BT) testing platform</p>
                </div>

                <!-- Toggle Results/Form View -->
                <div class="flex justify-center mb-8">
                    <div class="flex space-x-2 p-2 bg-white rounded-2xl shadow-lg border border-gray-200">
                        <button 
                            @click="showResultsSection = true"
                            :class="{
                                'bg-red-600 text-white shadow-lg': showResultsSection,
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
                                'bg-red-600 text-white shadow-lg': !showResultsSection,
                                'text-gray-600 hover:text-gray-800': showResultsSection
                            }"
                            class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center"
                        >
                            <Droplets class="h-5 w-5 mr-2" />
                            Add/Edit Test
                        </button>
                    </div>
                </div>

                <!-- Results Section -->
                <div v-if="showResultsSection" class="space-y-8">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100">
                        <div class="p-8">
                            <div class="flex items-center mb-8">
                                <div class="bg-gradient-to-r from-red-500 to-pink-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <Activity class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Laboratory Test Results</h2>
                                    <p class="text-gray-600">View and manage all hematology test results</p>
                                </div>
                            </div>

                            <!-- Results Display -->
                            <div v-if="Object.keys(testsByPatient).length > 0" class="space-y-6">
                                <div v-for="(patientData, patientId) in testsByPatient" :key="patientId" 
                                     class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                    <!-- Patient Header -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center">
                                            <div class="bg-red-100 p-3 rounded-full mr-4">
                                                <User class="h-6 w-6 text-red-600" />
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-900">{{ patientData.patient.name }}</h3>
                                                <p class="text-gray-600">
                                                    ID: {{ patientData.patient.id }} | 
                                                    Age: {{ calculateAge(patientData.patient.date_of_birth) }} | 
                                                    {{ patientData.patient.gender }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Total Tests</p>
                                            <p class="text-2xl font-bold text-red-600">{{ patientData.tests.length }}</p>
                                        </div>
                                    </div>

                                    <!-- Tests Grid -->
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

                                                    <button @click="print(test); showResultsSection = false"
                                                        class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                                        title="Print Test">
                                                        <Printer class="h-4 w-4" />
                                                    </button>

                                                    <button
                                                        @click="hematologyDeleteTest(test.id); showResultsSection = false"
                                                        class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition-colors duration-200"
                                                        title="Delete Test">
                                                        <Trash class="h-4 w-4" />
                                                    </button>

                                                    <button @click="editTest(test); showResultsSection = false"
                                                        class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                                        title="Edit Test">
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
                            <div v-else class="text-center py-12">
                                <div class="bg-gray-100 p-6 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                                    <Droplets class="h-12 w-12 text-gray-400" />
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Test Results Found</h3>
                                <p class="text-gray-600 mb-6">No hematology tests have been conducted yet.</p>
                                <button 
                                    @click="showResultsSection = false"
                                    class="bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition-colors duration-200 flex items-center mx-auto"
                                >
                                    <Droplets class="h-5 w-5 mr-2" />
                                    Add First Test
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div v-else class="space-y-8">
                    <!-- Patient Selection Card -->
                    <div class="bg-white rounded-3xl shadow-xl p-8 mb-8 border border-gray-100">
                        <div class="flex items-center mb-6">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 p-4 rounded-2xl mr-4 shadow-lg">
                                <UserCircle class="h-8 w-8 text-white" />
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">Patient Information</h2>
                                <p class="text-gray-600">
                                    {{ editingTestId ? 'Editing test for selected patient' : 'Search and select a patient to begin testing' }}
                                </p>
                            </div>
                        </div>
                                        
                        <div class="relative mb-6">
                            <div class="relative">
                                <Search class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                                <input 
                                    v-model="patientSearchQuery"
                                    @input="searchPatients"
                                    type="text"
                                    placeholder="Search patient by name or ID..."
                                    class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 text-lg transition-all duration-200"
                                />
                            </div>
                                            
                            <!-- Search Results Dropdown -->
                            <div v-if="patientSearchQuery && filteredPatients.length > 0"
                                class="absolute z-20 w-full bg-white border-2 border-gray-200 rounded-2xl mt-2 max-h-80 overflow-y-auto shadow-2xl">
                                <div v-for="patient in filteredPatients" :key="patient.id"
                                     @click="selectPatient(patient)"
                                    class="px-6 py-4 cursor-pointer hover:bg-red-50 transition-colors duration-200 border-b border-gray-100 last:border-b-0">
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
                                class="absolute z-20 w-full bg-white border-2 border-gray-200 rounded-2xl mt-2 p-6 text-gray-500 shadow-2xl">
                                <div class="text-center">
                                    <User class="h-12 w-12 text-gray-300 mx-auto mb-2" />
                                    <p>No patients found matching your search.</p>
                                </div>
                            </div>
                        </div>
                                        
                        <!-- Selected Patient Display -->
                        <div v-if="selectedPatient" class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6 animate-fade-in">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-xl font-bold text-green-900 mb-3 flex items-center">
                                        <CheckCircle class="h-6 w-6 text-green-600 mr-2" />
                                        {{ editingTestId ? 'Editing Test for Patient' : 'Selected Patient' }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <p class="text-gray-700"><span class="font-semibold">Name:</span> {{ selectedPatient.name }}</p>
                                            <p class="text-gray-700"><span class="font-semibold">Age:</span> {{ calculateAge(selectedPatient.date_of_birth) }} years old</p>
                                            <p class="text-gray-700"><span class="font-semibold">Gender:</span> {{ selectedPatient.gender }}</p>
                                        </div>
                                        <div class="space-y-2">
                                            <p class="text-gray-700"><span class="font-semibold">DOB:</span> {{ formatDate(selectedPatient.date_of_birth) }}</p>
                                            <p class="text-gray-700"><span class="font-semibold">Company:</span> {{ selectedPatient.company }}</p>
                                            <p class="text-gray-700"><span class="font-semibold">Address:</span> {{ selectedPatient.address }}</p>
                                        </div>
                                    </div>
                                </div>
                                <button @click="clearSelectedPatient"
                                         class="bg-white text-gray-600 hover:text-red-600 p-3 rounded-full hover:bg-red-50 transition-all duration-200 shadow-md hover:shadow-lg">
                                    <X class="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    <div v-if="$page.props.flash?.success"
                        class="bg-green-50 border-2 border-green-200 rounded-2xl p-4 mb-6 animate-fade-in">
                        <div class="flex items-center">
                            <CheckCircle class="h-6 w-6 text-green-500 mr-3" />
                            <div class="text-green-800 font-medium">{{ $page.props.flash.success }}</div>
                        </div>
                    </div>
                                    
                    <div v-if="$page.props.flash?.error"
                        class="bg-red-50 border-2 border-red-200 rounded-2xl p-4 mb-6 animate-fade-in">
                        <div class="flex items-center">
                            <AlertTriangle class="h-6 w-6 text-red-500 mr-3" />
                            <div class="text-red-800 font-medium">{{ $page.props.flash.error }}</div>
                        </div>
                    </div>

                    <!-- Main Test Form -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 main-form">
                        <div class="p-8">
                            <div class="flex items-center mb-8">
                                <div class="bg-gradient-to-r from-red-500 to-pink-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <Droplets class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ editingTestId ? 'Edit Hematology Test' : 'Hematology Tests' }}
                                    </h2>
                                    <p class="text-gray-600">
                                        {{ editingTestId ? 'Update test results and information' : 'Select test type and enter results' }}
                                    </p>
                                </div>
                            </div>
                                            
                            <!-- Test Type Selector -->
                            <div class="flex space-x-2 mb-8 p-2 bg-gray-100 rounded-2xl">
                                <button @click="selectedHematologyTest = 'cbc'"
                                         :class="{
                                            'bg-white text-red-600 shadow-lg': selectedHematologyTest === 'cbc',
                                            'text-gray-600 hover:text-gray-800': selectedHematologyTest !== 'cbc'
                                        }"
                                         class="flex-1 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center">
                                    <Activity class="h-5 w-5 mr-2" />
                                    Complete Blood Count (CBC)
                                </button>
                                <button @click="selectedHematologyTest = 'bt'"
                                         :class="{
                                            'bg-white text-red-600 shadow-lg': selectedHematologyTest === 'bt',
                                            'text-gray-600 hover:text-gray-800': selectedHematologyTest !== 'bt'
                                        }"
                                         class="flex-1 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center">
                                    <Droplets class="h-5 w-5 mr-2" />
                                    Blood Typing (BT)
                                </button>
                            </div>
                                            
                            <form @submit.prevent="submitHematologyTests" class="space-y-8">
                                <!-- CBC Section -->
                                <div v-if="selectedHematologyTest === 'cbc'" class="space-y-8">
                                    <!-- White Blood Cell Count -->
                                    <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-2xl p-6 border border-red-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-red-500 rounded-full mr-3"></div>
                                            White Blood Cell Count
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 2xl:grid-cols-6 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">WBC</label>
                                                <input v-model="hematologyForm.cbc_wbc" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter WBC count" />
                                                <div v-if="hematologyForm.errors.cbc_wbc" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_wbc }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Neutrophils (%)</label>
                                                <input v-model="hematologyForm.cbc_neu" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter NEU %" />
                                                <div v-if="hematologyForm.errors.cbc_neu" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_neu }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Lymphocytes (%)</label>
                                                <input v-model="hematologyForm.cbc_lym" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter LYM %" />
                                                <div v-if="hematologyForm.errors.cbc_lym" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_lym }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Monocytes (%)</label>
                                                <input v-model="hematologyForm.cbc_mon" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter MON %" />
                                                <div v-if="hematologyForm.errors.cbc_mon" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_mon }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Eosinophils (%)</label>
                                                <input v-model="hematologyForm.cbc_eos" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter EOS %" />
                                                <div v-if="hematologyForm.errors.cbc_eos" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_eos }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Basophils (%)</label>
                                                <input v-model="hematologyForm.cbc_bas" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter BAS %" />
                                                <div v-if="hematologyForm.errors.cbc_bas" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_bas }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Red Blood Cell Count -->
                                    <div class="bg-gradient-to-r from-pink-50 to-red-50 rounded-2xl p-6 border border-pink-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-pink-500 rounded-full mr-3"></div>
                                            Red Blood Cell Count & Indices
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 2xl:grid-cols-6 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">RBC</label>
                                                <input v-model="hematologyForm.cbc_rbc" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-pink-100 focus:border-pink-500 transition-all duration-200"
                                                    placeholder="Enter RBC count" />
                                                <div v-if="hematologyForm.errors.cbc_rbc" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_rbc }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Hemoglobin (HGB)</label>
                                                <input v-model="hematologyForm.cbc_hgb" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-pink-100 focus:border-pink-500 transition-all duration-200"
                                                    placeholder="Enter HGB level" />
                                                <div v-if="hematologyForm.errors.cbc_hgb" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_hgb }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Hematocrit (HCT)</label>
                                                <input v-model="hematologyForm.cbc_hct" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-pink-100 focus:border-pink-500 transition-all duration-200"
                                                    placeholder="Enter HCT level" />
                                                <div v-if="hematologyForm.errors.cbc_hct" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_hct }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">MCV</label>
                                                <input v-model="hematologyForm.cbc_mcv" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-pink-100 focus:border-pink-500 transition-all duration-200"
                                                    placeholder="Enter MCV value" />
                                                <div v-if="hematologyForm.errors.cbc_mcv" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_mcv }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">MCH</label>
                                                <input v-model="hematologyForm.cbc_mch" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-pink-100 focus:border-pink-500 transition-all duration-200"
                                                    placeholder="Enter MCH value" />
                                                <div v-if="hematologyForm.errors.cbc_mch" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_mch }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">MCHC</label>
                                                <input v-model="hematologyForm.cbc_mchc" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-pink-100 focus:border-pink-500 transition-all duration-200"
                                                    placeholder="Enter MCHC value" />
                                                <div v-if="hematologyForm.errors.cbc_mchc" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_mchc }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Platelet Count -->
                                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-purple-500 rounded-full mr-3"></div>
                                            Platelet Count
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Platelet Count (PLT)</label>
                                                <input v-model="hematologyForm.cbc_plt" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200"
                                                    placeholder="Enter platelet count" />
                                                <div v-if="hematologyForm.errors.cbc_plt" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.cbc_plt }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CBC Remarks -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">CBC Remarks</label>
                                        <textarea v-model="hematologyForm.cbc_remarks" rows="4"
                                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                            placeholder="Enter any additional remarks or observations..."></textarea>
                                        <div v-if="hematologyForm.errors.cbc_remarks" class="text-red-500 text-sm mt-2 font-medium">
                                            {{ hematologyForm.errors.cbc_remarks }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Blood Typing Section -->
                                <div v-if="selectedHematologyTest === 'bt'" class="space-y-8">
                                    <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-2xl p-6 border border-red-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-red-500 rounded-full mr-3"></div>
                                            Blood Typing Results
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">ABO Blood Group</label>
                                                <div class="relative">
                                                    <select v-model="hematologyForm.bt_abo_group"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select ABO Blood Group</option>
                                                        <option v-for="typesabo in typeOptions" :key="typesabo" :value="typesabo">
                                                            {{ typesabo }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="hematologyForm.errors.bt_abo_group" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.bt_abo_group }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Rh Factor</label>
                                                <div class="relative">
                                                    <select v-model="hematologyForm.bt_rh"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select RH Factor</option>
                                                        <option v-for="signsrh in signsOptions" :key="signsrh" :value="signsrh">
                                                            {{ signsrh }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="hematologyForm.errors.bt_rh" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ hematologyForm.errors.bt_rh }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Technologist -->
                                <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Medical Technologist</label>
                                    <div class="relative">
                                        <select v-model="hematologyForm.medical_technologist"
                                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                            <option value="">Select Medical Technologist</option>
                                            <option v-for="tech in medicalTechnologists" :key="tech" :value="tech">
                                                {{ tech }}
                                            </option>
                                        </select>
                                        <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                    </div>
                                    <div v-if="hematologyForm.errors.medical_technologist" class="text-red-500 text-sm mt-2 font-medium">
                                        {{ hematologyForm.errors.medical_technologist }}
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex flex-col items-center space-y-4">
                                    <button type="submit"
                                         :disabled="hematologyForm.processing || !selectedPatient"
                                        class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-2xl hover:from-red-700 hover:to-pink-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                        <span v-if="hematologyForm.processing" class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ editingTestId ? 'Updating Test...' : 'Submitting Test...' }}
                                        </span>
                                        <span v-else class="flex items-center">
                                            <Save class="h-5 w-5 mr-2" />
                                            {{ editingTestId ? 'Update Hematology Test' : 'Submit Hematology Test' }}
                                        </span>
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
    </AppLayout>

     <!-- Delete Modal -->
     <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
            <div class="flex items-center mb-4">
                <AlertTriangle class="h-6 w-6 text-red-500 mr-2" />
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
            </div>
            <p class="text-gray-600 mb-4">Are you sure you want to delete this test? This action cannot be undone.</p>
            <div class="flex space-x-3">
                <button @click="confirmDelete" :disabled="deleteForm.processing"
                    class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 disabled:opacity-50">
                    <span v-if="deleteForm.processing">Deleting...</span>
                    <span v-else>Delete</span>
                </button>
                <button @click="showDeleteModal = false"
                    class="flex-1 border border-gray-300 py-2 px-4 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
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

/* Remove default select styling to prevent double arrows */
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
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

/* Focus states for better accessibility */
input:focus,
select:focus,
textarea:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

/* Smooth transitions for all interactive elements */
button,
input,
select,
textarea {
    transition: all 0.2s ease-in-out;
}

/* Hover effects for checkboxes */
input[type="checkbox"]:hover {
    transform: scale(1.05);
}

/* Enhanced button hover effects */
button:hover:not(:disabled) {
    transform: translateY(-1px);
}

button:active:not(:disabled) {
    transform: translateY(0);
}
</style>