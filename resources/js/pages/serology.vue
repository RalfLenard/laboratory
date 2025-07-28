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
    Activity,
    Edit,
    Eye,
    Calendar,
    Clock,
    Trash,
    Printer
} from 'lucide-vue-next'
import { route } from 'ziggy-js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Serology',
        href: '/serology',
    },
];

// Define props to receive data from the controller
const props = defineProps({
    patients: {
        type: Array,
        default: () => []
    },
    serologyTests: {
        type: Array,
        default: () => []
    }
})

// Reactive state for selected module and test
const selectedSerologyTest = ref('syphilis')
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

// Computed property to get serology tests with patient information
const serologyTestsWithPatients = computed(() => {
    return props.serologyTests.map(test => {
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
    serologyTestsWithPatients.value.forEach(test => {
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

const serologyForm = useForm({
    patient_id: null,
    // Syphilis
    ss_kit: '',
    ss_lot_no: '',
    ss_expiration_date: '',
    ss_result: [],
    ss_remarks: '',
    // Dengue
    dd_kit: '',
    dd_lot_no: '',
    dd_expiration_date: '',
    dd_result: [],
    dd_remarks: '',
    // HBSAG
    hbsag_kit: '',
    hbsag_lot_no: '',
    hbsag_expiration_date: '',
    hbsag_result: [],
    hbsag_remarks: '',
    // HIV
    hiv_kit: '',
    hiv_lot_no: '',
    hiv_expiration_date: '',
    hiv_result: [],
    hiv_remarks: '',
    // Other
    medical_technologist: '',
});

const searchPatients = () => {
    // This function now filters the `patientList` which is populated by the backend.
}

const selectPatient = (patient) => {
    selectedPatient.value = patient
    patientSearchQuery.value = patient.name
    serologyForm.patient_id = patient.id
}

const clearSelectedPatient = () => {
    selectedPatient.value = null
    patientSearchQuery.value = ''
    serologyForm.patient_id = null
    editingTestId.value = null
}

// Function to load test data for editing
const editTest = (test) => {
    editingTestId.value = test.id
    selectedPatient.value = test.patient
    patientSearchQuery.value = test.patient.name

    // Load basic serology fields
    serologyForm.patient_id = test.patient_id
    serologyForm.ss_kit = test.ss_kit || ''
    serologyForm.ss_lot_no = test.ss_lot_no || ''
    serologyForm.ss_expiration_date = test.ss_expiration_date || ''
    serologyForm.ss_remarks = test.ss_remarks || ''
    serologyForm.dd_kit = test.dd_kit || ''
    serologyForm.dd_lot_no = test.dd_lot_no || ''
    serologyForm.dd_expiration_date = test.dd_expiration_date || ''
    serologyForm.dd_remarks = test.dd_remarks || ''
    serologyForm.hbsag_kit = test.hbsag_kit || ''
    serologyForm.hbsag_lot_no = test.hbsag_lot_no || ''
    serologyForm.hbsag_expiration_date = test.hbsag_expiration_date || ''
    serologyForm.hbsag_remarks = test.hbsag_remarks || ''
    serologyForm.hiv_kit = test.hiv_kit || ''
    serologyForm.hiv_lot_no = test.hiv_lot_no || ''
    serologyForm.hiv_expiration_date = test.hiv_expiration_date || ''
    serologyForm.hiv_remarks = test.hiv_remarks || ''
    serologyForm.medical_technologist = test.medical_technologist || ''

    // Reset all selections and maps
    syphilisResults.value = []
    hbsagResults.value = []
    selecteddengueResults.value = []
    selectedHivResults.value = []

    Object.keys(dengueDetailsMap).forEach(key => delete dengueDetailsMap[key])
    Object.keys(hivDetailsMap).forEach(key => delete hivDetailsMap[key])

    try {
        // Syphilis Results
        if (test.ss_result) {
            const ssData = typeof test.ss_result === 'string'
                ? JSON.parse(test.ss_result)
                : test.ss_result
            syphilisResults.value = Array.isArray(ssData) ? ssData : []
        }

        // HBsAg Results
        if (test.hbsag_result) {
            const hbsagData = typeof test.hbsag_result === 'string'
                ? JSON.parse(test.hbsag_result)
                : test.hbsag_result
            hbsagResults.value = Array.isArray(hbsagData) ? hbsagData : []
        }

       

    

        // Dengue Results
if (test.dd_result) {
    const dengueData = typeof test.dd_result === 'string'
        ? JSON.parse(test.dd_result)
        : test.dd_result;

    if (Array.isArray(dengueData)) {
        serologyForm.dd_result = dengueData;
        dengueData.forEach(d => {
            if (d.type) {
                selecteddengueResults.value.push(d.type);
                dengueDetailsMap[d.type] = Array.isArray(d.details) ? d.details[0] || '' : '';
            }
        });
    }
}

// HIV Results
if (test.hiv_result) {
    const hivData = typeof test.hiv_result === 'string'
        ? JSON.parse(test.hiv_result)
        : test.hiv_result;

    if (Array.isArray(hivData)) {
        serologyForm.hiv_result = hivData;
        hivData.forEach(h => {
            if (h.type) {
                selectedHivResults.value.push(h.type);
                hivDetailsMap[h.type] = Array.isArray(h.details) ? h.details[0] || '' : '';
            }
        });
    }
}


    } catch (error) {
        console.error('Error parsing serology test data:', error)
        syphilisResults.value = []
        hbsagResults.value = []
        selecteddengueResults.value = []
        selectedHivResults.value = []
        Object.keys(dengueDetailsMap).forEach(key => delete dengueDetailsMap[key])
        Object.keys(hivDetailsMap).forEach(key => delete hivDetailsMap[key])
    }

    // Set selected test tab
    if (serologyForm.ss_kit || syphilisResults.value.length) {
        selectedSerologyTest.value = 'syphilis'
    } else if (serologyForm.dd_kit || selecteddengueResults.value.length) {
        selectedSerologyTest.value = 'dengue'
    } else if (serologyForm.hbsag_kit || hbsagResults.value.length) {
        selectedSerologyTest.value = 'hbsag'
    } else if (serologyForm.hiv_kit || selectedHivResults.value.length) {
        selectedSerologyTest.value = 'hiv'
    }

    // Scroll to form
    document.querySelector('.main-form')?.scrollIntoView({ behavior: 'smooth' })
}


// Function to determine test type based on available data
const getTestType = (test) => {
    // Check Syphilis
    if (test.ss_kit || (test.ss_result && test.ss_result.length > 0)) {
        return 'Syphilis'
    }
    
    // Check Dengue
    if (test.dd_kit || (test.dd_result && test.dd_result.length > 0)) {
        return 'Dengue'
    }
    
    // Check HBSAG
    if (test.hbsag_kit || (test.hbsag_result && test.hbsag_result.length > 0)) {
        return 'HBSAG'
    }
    
    // Check HIV
    if (test.hiv_kit || (test.hiv_result && test.hiv_result.length > 0)) {
        return 'HIV'
    }
    
    return 'Serology Test'
}

// Function to format test results for display
const formatTestResults = (test) => {
    const results = []

    // Syphilis results
    if (test.ss_result) {
        try {
            const syphilisData = typeof test.ss_result === 'string' 
                ? JSON.parse(test.ss_result) 
                : (Array.isArray(test.ss_result) ? test.ss_result : [])
            if (syphilisData.length > 0) {
                results.push(`Syphilis: ${syphilisData.join(', ')}`)
            }
        } catch (e) {
            // Handle parsing error
        }
    }

    

    // Dengue results
    if (test.dd_result) {
        try {
            const dengueData = typeof test.dd_result === 'string' 
                ? JSON.parse(test.dd_result) 
                : (Array.isArray(test.dd_result) ? test.dd_result : [])
            if (dengueData.length > 0) {
                const dengueResults = dengueData.map(item => `${item.type}: ${item.result || 'N/A'}`).join(', ')
                results.push(`Dengue: ${dengueResults}`)
            }
        } catch (e) {
            // Handle parsing error
        }
    }

    // HBSAG results
    if (test.hbsag_result) {
        try {
            const hbsagData = typeof test.hbsag_result === 'string' 
                ? JSON.parse(test.hbsag_result) 
                : (Array.isArray(test.hbsag_result) ? test.hbsag_result : [])
            if (hbsagData.length > 0) {
                results.push(`HBSAG: ${hbsagData.join(', ')}`)
            }
        } catch (e) {
            // Handle parsing error
        }
    }

    // HIV results
    if (test.hiv_result) {
        try {
            const hivData = typeof test.hiv_result === 'string' 
                ? JSON.parse(test.hiv_result) 
                : (Array.isArray(test.hiv_result) ? test.hiv_result : [])
            if (hivData.length > 0) {
                const hivResults = hivData.map(item => `${item.type}: ${item.result || 'N/A'}`).join(', ')
                results.push(`HIV: ${hivResults}`)
            }
        } catch (e) {
            // Handle parsing error
        }
    }

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

// Test result options
const syphilisResults = ref([]);
const hbsagResults = ref([]);

// Syphilis & HBSAG options
const syphilisResultOptions = ['Reactive', 'Non-Reactive', 'Invalid'];
const hbsagResultOptions = ['Reactive', 'Non-Reactive', 'Invalid'];

// Dengue test logic
const selecteddengueResults = ref<string[]>([]);
const dengueDetailsMap = reactive<Record<string, string>>({});
const dengueOptions = [
    { value: 'ns1', label: 'NS1', subOptions: ['Negative', 'Positive'] },
    { value: 'igm', label: 'IgM', subOptions: ['Negative', 'Positive'] },
    { value: 'igg', label: 'IgG', subOptions: ['Negative', 'Positive'] },
];

// Handle checkbox toggle for dengue
const handleCastSelection = (type: string) => {
    if (selecteddengueResults.value.includes(type)) {
        if (!dengueDetailsMap[type]) {
            dengueDetailsMap[type] = '';
        }
    } else {
        delete dengueDetailsMap[type];
    }
};

// Watch dengue selection & sync to form
watch([selecteddengueResults, dengueDetailsMap], () => {
    const dengueArray = selecteddengueResults.value.map(type => ({
        type,
        details: dengueDetailsMap[type] ? [dengueDetailsMap[type]] : []
    }))
    serologyForm.dd_result = dengueArray
}, { deep: true })



// === HIV test logic similar to dengue ===
const selectedHivResults = ref<string[]>([]);
const hivDetailsMap = reactive<Record<string, string>>({});
const hivOptions = [
    { value: 'hiv1', label: 'HIV 1', subOptions: ['Non-Reactive', 'Reactive'] },
    { value: 'hiv2', label: 'HIV 2', subOptions: ['Non-Reactive', 'Reactive'] },
];

// Handle checkbox toggle for HIV
const handleHivSelection = (type: string) => {
    if (selectedHivResults.value.includes(type)) {
        if (!hivDetailsMap[type]) {
            hivDetailsMap[type] = '';
        }
    } else {
        delete hivDetailsMap[type];
    }
};

// Watch HIV selection & sync to form
watch([selectedHivResults, hivDetailsMap], () => {
    const hivArray = selectedHivResults.value.map(type => ({
        type,
        details: hivDetailsMap[type] ? [hivDetailsMap[type]] : [] // Ensure 'details' is always an array
    }));
    serologyForm.hiv_result = hivArray;
}, { deep: true });


// Watch and update form on individual test result change
watch([syphilisResults, hbsagResults], () => {
    serologyForm.ss_result = syphilisResults.value;
    serologyForm.hbsag_result = hbsagResults.value;
}, { deep: true });



// Form submission method
const submitSerologyTest = () => {
    if (!selectedPatient.value) {
        alert('Please select a patient first.');
        return;
    }

    // Set the patient_id before submission
    serologyForm.patient_id = selectedPatient.value.id;

    // Determine if this is an update or a create operation
    const isUpdating = editingTestId.value !== null;
    const submitRoute = isUpdating
        ? route('serology.update', editingTestId.value)
        : route('serology.store');

    const submitMethod = isUpdating ? 'put' : 'post';

    serologyForm[submitMethod](submitRoute, {
        onSuccess: () => {
            // Reset form state after successful submission
            serologyForm.reset();
            selectedPatient.value = null;
            patientSearchQuery.value = '';
            editingTestId.value = null;

            // Reset all result arrays
            syphilisResults.value = [];
            selecteddengueResults.value = [];
            hbsagResults.value = [];
            selectedHivResults.value = [];

            // Clear detail maps
            Object.keys(dengueDetailsMap).forEach(key => delete dengueDetailsMap[key]);
            Object.keys(hivDetailsMap).forEach(key => delete hivDetailsMap[key]);
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


// Medical technologist dropdown options
const medicalTechnologists = [
    'KRISTINA CASSANDRA F. SANTOS, RMT',
    'KATE ANGELINE M. SALAS, RMT',
    'JULLIUS D. ANUSENCION, RMT',
    'MARY GRACE L. BERNARDO, RMT',
    'JANIELLE M. PASAMONTE, RMT',
];


const showDeleteModal = ref(false)
const serologyDelete = ref(null)

const deleteForm = useForm({})

const serologyDeleteTest = (test) => {
    serologyDelete.value = test
    showDeleteModal.value = true
}

const confirmDelete = () => {
    deleteForm.delete(`/serology/${serologyDelete.value}`, {
        onSuccess: () => {
            showDeleteModal.value = false
            serologyDelete.value = null
        }
    })
}

// print or pdf
const print = (test) => {
    const type = getTestType(test)

    let url = ''
    if (type === 'Syphilis') {
        url = `/serology/${test.id}/syphilis-pdf`
    } else if (type === 'Dengue') {
        url = `/serology/${test.id}/dengue-pdf`
    }  else if (type === 'HBSAG') {
        url = `/serology/${test.id}/hbsag-pdf`
    } else if (type === 'HIV') {
        url = `/serology/${test.id}/hiv-pdf`
    } else {
        alert('Unsupported test type.')
        return
    }

    window.open(url, '_blank')
}
</script>

<template>
    <Head title="Serology Tests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50">
            <div class="w-full py-8 px-6">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-4 rounded-2xl shadow-lg">
                            <TestTube class="h-10 w-10 text-white" />
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Serology Tests</h1>
                    <p class="text-lg text-gray-600">Comprehensive serological testing platform</p>
                </div>

                <!-- Toggle Results/Form View -->
                <div class="flex justify-center mb-8">
                    <div class="flex space-x-2 p-2 bg-white rounded-2xl shadow-lg border border-gray-200">
                        <button
                            @click="showResultsSection = true"
                            :class="{
                                'bg-purple-600 text-white shadow-lg': showResultsSection,
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
                                'bg-purple-600 text-white shadow-lg': !showResultsSection,
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
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <Activity class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Serology Test Results</h2>
                                    <p class="text-gray-600">View and manage all serology test results</p>
                                </div>
                            </div>

                            <!-- Results Display -->
                            <div v-if="Object.keys(testsByPatient).length > 0" class="space-y-6">
                                <div v-for="(patientData, patientId) in testsByPatient" :key="patientId"
                                     class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                    <!-- Patient Header -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center">
                                            <div class="bg-purple-100 p-3 rounded-full mr-4">
                                                <User class="h-6 w-6 text-purple-600" />
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
                                            <p class="text-2xl font-bold text-purple-600">{{ patientData.tests.length }}</p>
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
                                                        @click="serologyDeleteTest(test.id); showResultsSection = false"
                                                        class="bg-red-100 text-red-600 p-2 rounded-lg hover:bg-red-200 transition-colors duration-200"
                                                        title="Delete Test">
                                                        <Trash class="h-4 w-4" />
                                                    </button>

                                                    <button
                                                    @click="editTest(test); showResultsSection = false"
                                                    class="bg-purple-100 text-purple-600 p-2 rounded-lg hover:bg-purple-200 transition-colors duration-200"
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
                            <div v-else class="text-center py-12">
                                <div class="bg-gray-100 p-6 rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                                    <TestTube class="h-12 w-12 text-gray-400" />
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Test Results Found</h3>
                                <p class="text-gray-600 mb-6">No serology tests have been conducted yet.</p>
                                <button
                                    @click="showResultsSection = false"
                                    class="bg-purple-600 text-white px-6 py-3 rounded-xl hover:bg-purple-700 transition-colors duration-200 flex items-center mx-auto"
                                >
                                    <TestTube class="h-5 w-5 mr-2" />
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
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-4 rounded-2xl mr-4 shadow-lg">
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
                                    class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 text-lg transition-all duration-200"
                                />
                            </div>

                            <!-- Search Results Dropdown -->
                            <div v-if="patientSearchQuery && filteredPatients.length > 0"
                                class="absolute z-20 w-full bg-white border-2 border-gray-200 rounded-2xl mt-2 max-h-80 overflow-y-auto shadow-2xl">
                                <div v-for="patient in filteredPatients" :key="patient.id"
                                     @click="selectPatient(patient)"
                                    class="px-6 py-4 cursor-pointer hover:bg-purple-50 transition-colors duration-200 border-b border-gray-100 last:border-b-0">
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
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <Activity class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ editingTestId ? 'Edit Serology Test' : 'Serology Tests' }}
                                    </h2>
                                    <p class="text-gray-600">
                                        {{ editingTestId ? 'Update test results and information' : 'Select test type and enter results' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Test Type Selector -->
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 mb-8 p-2 bg-gray-100 rounded-2xl">
                                <button @click="selectedSerologyTest = 'syphilis'"
                                        :class="{
                                            'bg-white text-purple-600 shadow-lg': selectedSerologyTest === 'syphilis',
                                            'text-gray-600 hover:text-gray-800': selectedSerologyTest !== 'syphilis'
                                        }"
                                        class="px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-sm lg:text-base">
                                    Syphilis
                                </button>
                                <button @click="selectedSerologyTest = 'dengue'"
                                        :class="{
                                            'bg-white text-purple-600 shadow-lg': selectedSerologyTest === 'dengue',
                                            'text-gray-600 hover:text-gray-800': selectedSerologyTest !== 'dengue'
                                        }"
                                        class="px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-sm lg:text-base">
                                    Dengue
                                </button>
                                <button @click="selectedSerologyTest = 'hbsag'"
                                        :class="{
                                            'bg-white text-purple-600 shadow-lg': selectedSerologyTest === 'hbsag',
                                            'text-gray-600 hover:text-gray-800': selectedSerologyTest !== 'hbsag'
                                        }"
                                        class="px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-sm lg:text-base">
                                    HBSAG
                                </button>
                                <button @click="selectedSerologyTest = 'hiv'"
                                        :class="{
                                            'bg-white text-purple-600 shadow-lg': selectedSerologyTest === 'hiv',
                                            'text-gray-600 hover:text-gray-800': selectedSerologyTest !== 'hiv'
                                        }"
                                        class="px-4 py-3 rounded-xl font-semibold transition-all duration-200 text-sm lg:text-base">
                                    HIV
                                </button>
                            </div>

                            <form @submit.prevent="submitSerologyTest" class="space-y-8">
                                <!-- Syphilis Section -->
                                <div v-if="selectedSerologyTest === 'syphilis'" class="space-y-8">
                                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-purple-500 rounded-full mr-3"></div>
                                            Syphilis Screening
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                            <!-- Kit Used -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Kit Used</label>
                                                <input v-model="serologyForm.ss_kit" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200"
                                                    placeholder="Enter kit name" />
                                                <div v-if="serologyForm.errors.ss_kit" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.ss_kit }}
                                                </div>
                                            </div>
                                            <!-- Lot Number -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Lot Number</label>
                                                <input v-model="serologyForm.ss_lot_no" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200"
                                                    placeholder="Enter lot number" />
                                                <div v-if="serologyForm.errors.ss_lot_no" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.ss_lot_no }}
                                                </div>
                                            </div>
                                            <!-- Expiration Date -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Expiration Date</label>
                                                <input v-model="serologyForm.ss_expiration_date" type="date"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200" />
                                                <div v-if="serologyForm.errors.ss_expiration_date" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.ss_expiration_date }}
                                                </div>
                                            </div>
                                            <!-- Results -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Results</label>
                                                <div class="space-y-2">
                                                    <label v-for="option in syphilisResultOptions" :key="option"
                                                        class="flex items-center space-x-2">
                                                        <input type="checkbox" :value="option" v-model="syphilisResults"
                                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" />
                                                        <span class="text-sm">{{ option }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Remarks -->
                                        <div class="mt-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Remarks</label>
                                            <textarea v-model="serologyForm.ss_remarks" rows="4"
                                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200"
                                                placeholder="Enter any additional remarks or observations..."></textarea>
                                            <div v-if="serologyForm.errors.ss_remarks" class="text-red-500 text-sm mt-2 font-medium">
                                                {{ serologyForm.errors.ss_remarks }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dengue Section -->
                                <div v-if="selectedSerologyTest === 'dengue'" class="space-y-8">
                                    <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-6 border border-orange-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-orange-500 rounded-full mr-3"></div>
                                            Dengue Detection
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Kit Used -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Kit Used</label>
                                                <input v-model="serologyForm.dd_kit" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all duration-200"
                                                    placeholder="Enter kit name" />
                                                <div v-if="serologyForm.errors.dd_kit" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.dd_kit }}
                                                </div>
                                            </div>
                                            <!-- Lot Number -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Lot Number</label>
                                                <input v-model="serologyForm.dd_lot_no" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all duration-200"
                                                    placeholder="Enter lot number" />
                                                <div v-if="serologyForm.errors.dd_lot_no" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.dd_lot_no }}
                                                </div>
                                            </div>
                                            <!-- Expiration Date -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Expiration Date</label>
                                                <input v-model="serologyForm.dd_expiration_date" type="date"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all duration-200" />
                                                <div v-if="serologyForm.errors.dd_expiration_date" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.dd_expiration_date }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dengue Duo Result Section -->
                                        <div class="mt-10">
                                            <label class="block text-lg font-semibold text-gray-800 mb-4">Dengue Duo Results</label>
                                            <div class="flex flex-wrap gap-6">
                                                <div v-for="dengue in dengueOptions" :key="dengue.value"
                                                    class="w-full sm:w-1/2 lg:w-1/4 xl:w-1/4 border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition">
                                                    <!-- Checkbox -->
                                                    <div class="flex items-center space-x-3 mb-3">
                                                        <input type="checkbox" :value="dengue.value"
                                                            v-model="selecteddengueResults"
                                                            @change="handleCastSelection(dengue.value)"
                                                            class="rounded border-gray-300 text-green-600 focus:ring-green-500 w-5 h-5" />
                                                        <span class="text-sm font-medium text-gray-900">{{ dengue.label }}</span>
                                                    </div>
                                                    <!-- Result Options -->
                                                    <div v-if="selecteddengueResults.includes(dengue.value)">
                                                        <label class="block text-xs text-gray-600 mb-1 ml-1">Result:</label>
                                                        <div class="flex flex-wrap gap-2">
                                                            <label v-for="option in dengue.subOptions" :key="option"
                                                                class="flex items-center space-x-2 px-3 py-1 border border-green-200 rounded-full bg-green-50">
                                                                <input type="radio" :name="dengue.value" :value="option"
                                                                    v-model="dengueDetailsMap[dengue.value]"
                                                                    class="text-green-600 focus:ring-green-500" />
                                                                <span class="text-xs">{{ option }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remarks -->
                                        <div class="mt-10">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Remarks</label>
                                            <textarea v-model="serologyForm.dd_remarks" rows="4"
                                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 transition-all duration-200"
                                                placeholder="Enter any additional remarks or observations..."></textarea>
                                            <div v-if="serologyForm.errors.dd_remarks" class="text-red-500 text-sm mt-2 font-medium">
                                                {{ serologyForm.errors.dd_remarks }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- HBSAG Section -->
                                <div v-if="selectedSerologyTest === 'hbsag'" class="space-y-8">
                                    <div class="bg-gradient-to-r from-green-50 to-teal-50 rounded-2xl p-6 border border-green-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-green-500 rounded-full mr-3"></div>
                                            Hepatitis B Surface Antigen
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                                            <!-- Kit Used -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Kit Used</label>
                                                <input v-model="serologyForm.hbsag_kit" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all duration-200"
                                                    placeholder="Enter kit name" />
                                                <div v-if="serologyForm.errors.hbsag_kit" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.hbsag_kit }}
                                                </div>
                                            </div>
                                            <!-- Lot Number -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Lot Number</label>
                                                <input v-model="serologyForm.hbsag_lot_no" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all duration-200"
                                                    placeholder="Enter lot number" />
                                                <div v-if="serologyForm.errors.hbsag_lot_no" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.hbsag_lot_no }}
                                                </div>
                                            </div>
                                            <!-- Expiration Date -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Expiration Date</label>
                                                <input v-model="serologyForm.hbsag_expiration_date" type="date"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all duration-200" />
                                                <div v-if="serologyForm.errors.hbsag_expiration_date" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.hbsag_expiration_date }}
                                                </div>
                                            </div>
                                            <!-- Results -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Results</label>
                                                <div class="space-y-2">
                                                    <label v-for="option in hbsagResultOptions" :key="option"
                                                        class="flex items-center space-x-2">
                                                        <input type="checkbox" :value="option" v-model="hbsagResults"
                                                            class="rounded border-gray-300 text-green-600 focus:ring-green-500" />
                                                        <span class="text-sm">{{ option }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Remarks -->
                                        <div class="mt-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Remarks</label>
                                            <textarea v-model="serologyForm.hbsag_remarks" rows="4"
                                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all duration-200"
                                                placeholder="Enter any additional remarks or observations..."></textarea>
                                            <div v-if="serologyForm.errors.hbsag_remarks" class="text-red-500 text-sm mt-2 font-medium">
                                                {{ serologyForm.errors.hbsag_remarks }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- HIV Section -->
                                <div v-if="selectedSerologyTest === 'hiv'" class="space-y-8">
                                    <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-2xl p-6 border border-red-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-red-500 rounded-full mr-3"></div>
                                            HIV Screening
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Kit Used -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Kit Used</label>
                                                <input v-model="serologyForm.hiv_kit" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter kit name" />
                                                <div v-if="serologyForm.errors.hiv_kit" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.hiv_kit }}
                                                </div>
                                            </div>
                                            <!-- Lot Number -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Lot Number</label>
                                                <input v-model="serologyForm.hiv_lot_no" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                    placeholder="Enter lot number" />
                                                <div v-if="serologyForm.errors.hiv_lot_no" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.hiv_lot_no }}
                                                </div>
                                            </div>
                                            <!-- Expiration Date -->
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Expiration Date</label>
                                                <input v-model="serologyForm.hiv_expiration_date" type="date"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200" />
                                                <div v-if="serologyForm.errors.hiv_expiration_date" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ serologyForm.errors.hiv_expiration_date }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- HIV Result Section -->
                                        <div class="mt-10">
                                            <label class="block text-lg font-semibold text-gray-800 mb-4">HIV Results</label>
                                            <div class="flex flex-wrap gap-6">
                                                <div v-for="hiv in hivOptions" :key="hiv.value"
                                                    class="w-full sm:w-1/2 lg:w-1/4 xl:w-1/4 border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition">
                                                    <!-- Checkbox -->
                                                    <div class="flex items-center space-x-3 mb-3">
                                                        <input type="checkbox" :value="hiv.value"
                                                            v-model="selectedHivResults"
                                                            @change="handleHivSelection(hiv.value)"
                                                            class="rounded border-gray-300 text-red-600 focus:ring-red-500 w-5 h-5" />
                                                        <span class="text-sm font-medium text-gray-900">{{ hiv.label }}</span>
                                                    </div>
                                                    <!-- Result Options -->
                                                    <div v-if="selectedHivResults.includes(hiv.value)">
                                                        <label class="block text-xs text-gray-600 mb-1 ml-1">Result:</label>
                                                        <div class="flex flex-wrap gap-2">
                                                            <label v-for="option in hiv.subOptions" :key="option"
                                                                class="flex items-center space-x-2 px-3 py-1 border border-red-200 rounded-full bg-red-50">
                                                                <input type="radio" :name="hiv.value" :value="option"
                                                                    v-model="hivDetailsMap[hiv.value]"
                                                                    class="text-red-600 focus:ring-red-500" />
                                                                <span class="text-xs">{{ option }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remarks -->
                                        <div class="mt-10">
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Remarks</label>
                                            <textarea v-model="serologyForm.hiv_remarks" rows="4"
                                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200"
                                                placeholder="Enter any additional remarks or observations..."></textarea>
                                            <div v-if="serologyForm.errors.hiv_remarks" class="text-red-500 text-sm mt-2 font-medium">
                                                {{ serologyForm.errors.hiv_remarks }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Technologist -->
                                <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Medical Technologist</label>
                                    <div class="relative">
                                        <select v-model="serologyForm.medical_technologist"
                                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                            <option value="">Select Medical Technologist</option>
                                            <option v-for="tech in medicalTechnologists" :key="tech" :value="tech">
                                                {{ tech }}
                                            </option>
                                        </select>
                                        <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                    </div>
                                    <div v-if="serologyForm.errors.medical_technologist" class="text-red-500 text-sm mt-2 font-medium">
                                        {{ serologyForm.errors.medical_technologist }}
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex flex-col items-center space-y-4">
                                    <button type="submit"
                                            :disabled="serologyForm.processing || !selectedPatient"
                                        class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-2xl hover:from-purple-700 hover:to-pink-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                        <span v-if="serologyForm.processing" class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ editingTestId ? 'Updating Test...' : 'Submitting Test...' }}
                                        </span>
                                        <span v-else class="flex items-center">
                                            <Save class="h-5 w-5 mr-2" />
                                            {{ editingTestId ? 'Update Serology Test' : 'Submit Serology Test' }}
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
    box-shadow: 0 0 0 4px rgba(147, 51, 234, 0.1);
}

/* Smooth transitions for all interactive elements */
button,
input,
select,
textarea {
    transition: all 0.2s ease-in-out;
}

/* Enhanced button hover effects */
button:hover:not(:disabled) {
    transform: translateY(-1px);
}

button:active:not(:disabled) {
    transform: translateY(0);
}
</style>