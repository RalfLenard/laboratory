<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { reactive, ref, computed, watch } from 'vue'
import { 
  Microscope, 
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
  Activity
} from 'lucide-vue-next'
import { route } from 'ziggy-js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Clinical',
        href: '/clinical',
    },
];

// Define props to receive data from the controller
const props = defineProps({
    patients: {
        type: Array,
        default: () => []
    },
    clinicalTests: {
        type: Array,
        default: () => []
    }
})

// Reactive state for selected module and test
const selectedClinicalMicroscopyTest = ref('urinalysis')
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

// Computed property to get clinical tests with patient information
const clinicalTestsWithPatients = computed(() => {
    return props.clinicalTests.map(test => {
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
    clinicalTestsWithPatients.value.forEach(test => {
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

const clinicalForm = useForm({
    patient_id: null,
    // Fecalysis
    fecalysis_color: '',
    fecalysis_consistency: '',
    fecalysis_wbc: '',
    fecalysis_rbc: '',
    fecalysis_remarks: '',
    fecalysis_results: '',
        
    // Urinalysis - Physical
    urinalysis_color: '',
    urinalysis_transparency: '',
        
    // Urinalysis - Chemical
    urinalysis_glucose: '',
    urinalysis_protein: '',
    urinalysis_spgravity: '',
    ph: '',
        
    // Urinalysis - Microscopic
    urinalysis_wbc: '',
    urinalysis_rbc: '',
    urinalysis_bacteria: '',
    urinalysis_epithelial_cells: '',
    urinalysis_amorphous: '',
    urinalysis_phospates: '',
    urinalysis_mucus_threads: '',
    urinalysis_casts: [],
    urinalysis_crystals: [],
    urinalysis_fungal_elements: [],
    urinalysis_parasite: [],
    urinalysis_remarks: '',
        
    medical_technologist: '',
});

const searchPatients = () => {
    // This function now filters the `patientList` which is populated by the backend.
}

const selectPatient = (patient) => {
    selectedPatient.value = patient
    patientSearchQuery.value = patient.name
    clinicalForm.patient_id = patient.id
}

const clearSelectedPatient = () => {
    selectedPatient.value = null
    patientSearchQuery.value = ''
    clinicalForm.patient_id = null
    editingTestId.value = null
}

// Function to load test data for editing
const editTest = (test) => {
    editingTestId.value = test.id
    selectedPatient.value = test.patient
    patientSearchQuery.value = test.patient.name
    
    // Load all form data from the test
    clinicalForm.patient_id = test.patient_id
    clinicalForm.fecalysis_color = test.fecalysis_color || ''
    clinicalForm.fecalysis_consistency = test.fecalysis_consistency || ''
    clinicalForm.fecalysis_wbc = test.fecalysis_wbc || ''
    clinicalForm.fecalysis_rbc = test.fecalysis_rbc || ''
    clinicalForm.fecalysis_remarks = test.fecalysis_remarks || ''
    clinicalForm.fecalysis_results = test.fecalysis_results || ''
    
    clinicalForm.urinalysis_color = test.urinalysis_color || ''
    clinicalForm.urinalysis_transparency = test.urinalysis_transparency || ''
    clinicalForm.urinalysis_glucose = test.urinalysis_glucose || ''
    clinicalForm.urinalysis_protein = test.urinalysis_protein || ''
    clinicalForm.urinalysis_spgravity = test.urinalysis_spgravity || ''
    clinicalForm.ph = test.ph || ''
    clinicalForm.urinalysis_wbc = test.urinalysis_wbc || ''
    clinicalForm.urinalysis_rbc = test.urinalysis_rbc || ''
    clinicalForm.urinalysis_bacteria = test.urinalysis_bacteria || ''
    clinicalForm.urinalysis_epithelial_cells = test.urinalysis_epithelial_cells || ''
    clinicalForm.urinalysis_amorphous = test.urinalysis_amorphous || ''
    clinicalForm.urinalysis_phospates = test.urinalysis_phospates || ''
    clinicalForm.urinalysis_mucus_threads = test.urinalysis_mucus_threads || ''
    clinicalForm.urinalysis_remarks = test.urinalysis_remarks || ''
    clinicalForm.medical_technologist = test.medical_technologist || ''
    
    // Clear existing selections and detail maps first
    selectedCastTypes.value = []
    selectedCrystalTypes.value = []
    selectedFungalTypes.value = []
    selectedParasiteTypes.value = []
    
    // Clear detail maps
    Object.keys(castDetailsMap).forEach(key => delete castDetailsMap[key])
    Object.keys(crystalDetailsMap).forEach(key => delete crystalDetailsMap[key])
    Object.keys(fungalDetailsMap).forEach(key => delete fungalDetailsMap[key])
    Object.keys(parasiteDetailsMap).forEach(key => delete parasiteDetailsMap[key])
    
    // Handle complex fields (arrays) - Parse JSON strings from database
    try {
        // Handle Casts
        if (test.urinalysis_casts) {
            const castsData = typeof test.urinalysis_casts === 'string' 
                ? JSON.parse(test.urinalysis_casts) 
                : test.urinalysis_casts
            
            if (Array.isArray(castsData)) {
                clinicalForm.urinalysis_casts = castsData
                castsData.forEach(cast => {
                    if (cast.type) {
                        selectedCastTypes.value.push(cast.type)
                        castDetailsMap[cast.type] = cast.details || []
                    }
                })
            }
        }
        
        // Handle Crystals
        if (test.urinalysis_crystals) {
            const crystalsData = typeof test.urinalysis_crystals === 'string' 
                ? JSON.parse(test.urinalysis_crystals) 
                : test.urinalysis_crystals
            
            if (Array.isArray(crystalsData)) {
                clinicalForm.urinalysis_crystals = crystalsData
                crystalsData.forEach(crystal => {
                    if (crystal.type) {
                        selectedCrystalTypes.value.push(crystal.type)
                        crystalDetailsMap[crystal.type] = crystal.details || []
                    }
                })
            }
        }
        
        // Handle Fungal Elements
        if (test.urinalysis_fungal_elements) {
            const fungalData = typeof test.urinalysis_fungal_elements === 'string' 
                ? JSON.parse(test.urinalysis_fungal_elements) 
                : test.urinalysis_fungal_elements
            
            if (Array.isArray(fungalData)) {
                clinicalForm.urinalysis_fungal_elements = fungalData
                fungalData.forEach(fungal => {
                    if (fungal.type) {
                        selectedFungalTypes.value.push(fungal.type)
                        fungalDetailsMap[fungal.type] = fungal.details || []
                    }
                })
            }
        }
        
        // Handle Parasites
        if (test.urinalysis_parasite) {
            const parasiteData = typeof test.urinalysis_parasite === 'string' 
                ? JSON.parse(test.urinalysis_parasite) 
                : test.urinalysis_parasite
            
            if (Array.isArray(parasiteData)) {
                clinicalForm.urinalysis_parasite = parasiteData
                parasiteData.forEach(parasite => {
                    if (parasite.type) {
                        selectedParasiteTypes.value.push(parasite.type)
                        // For parasites, details is an array but we store as string in the map
                        parasiteDetailsMap[parasite.type] = Array.isArray(parasite.details) 
                            ? parasite.details[0] || '' 
                            : parasite.details || ''
                    }
                })
            }
        }
        
    } catch (error) {
        console.error('Error parsing test data:', error)
        // If parsing fails, initialize with empty arrays
        clinicalForm.urinalysis_casts = []
        clinicalForm.urinalysis_crystals = []
        clinicalForm.urinalysis_fungal_elements = []
        clinicalForm.urinalysis_parasite = []
    }
    
    // Scroll to form
    document.querySelector('.main-form')?.scrollIntoView({ behavior: 'smooth' })
}

// Debug function to log current state (you can remove this later)
const logCurrentState = () => {
    console.log('Current State:', {
        selectedCastTypes: selectedCastTypes.value,
        castDetailsMap: { ...castDetailsMap },
        selectedCrystalTypes: selectedCrystalTypes.value,
        crystalDetailsMap: { ...crystalDetailsMap },
        selectedFungalTypes: selectedFungalTypes.value,
        fungalDetailsMap: { ...fungalDetailsMap },
        selectedParasiteTypes: selectedParasiteTypes.value,
        parasiteDetailsMap: { ...parasiteDetailsMap },
        formCasts: clinicalForm.urinalysis_casts,
        formCrystals: clinicalForm.urinalysis_crystals,
        formFungal: clinicalForm.urinalysis_fungal_elements,
        formParasite: clinicalForm.urinalysis_parasite
    })
}

// Function to determine test type based on available data
const getTestType = (test) => {
    if (test.urinalysis_color || test.urinalysis_transparency || test.urinalysis_glucose) {
        return 'Urinalysis'
    }
    if (test.fecalysis_color || test.fecalysis_consistency) {
        return 'Fecalysis'
    }
    return 'Clinical Test'
}

// Function to format test results for display
const formatTestResults = (test) => {
    const results = []
    
    if (test.urinalysis_color) results.push(`Color: ${test.urinalysis_color}`)
    if (test.urinalysis_transparency) results.push(`Transparency: ${test.urinalysis_transparency}`)
    if (test.urinalysis_glucose) results.push(`Glucose: ${test.urinalysis_glucose}`)
    if (test.urinalysis_protein) results.push(`Protein: ${test.urinalysis_protein}`)
    if (test.ph) results.push(`pH: ${test.ph}`)
    if (test.fecalysis_color) results.push(`Color: ${test.fecalysis_color}`)
    if (test.fecalysis_consistency) results.push(`Consistency: ${test.fecalysis_consistency}`)
    
    return results.slice(0, 3) // Show first 3 results
}

// Watch pH changes to clear the opposite field
watch(() => clinicalForm.ph, (newPh, oldPh) => {
    if (oldPh !== undefined && newPh !== oldPh) {
        if (newPh !== null && newPh !== '') {
            const phValue = parseFloat(newPh);
            if (!isNaN(phValue)) {
                if (phValue >= 7.0) {
                    clinicalForm.urinalysis_amorphous = '';
                } else {
                    clinicalForm.urinalysis_phospates = '';
                }
            } else {
                clinicalForm.urinalysis_amorphous = '';
                clinicalForm.urinalysis_phospates = '';
            }
        } else {
            clinicalForm.urinalysis_amorphous = '';
            clinicalForm.urinalysis_phospates = '';
        }
    }
});

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

// Casts handling - Fixed to match controller expectations
const selectedCastTypes = ref([]);
const castDetailsMap = reactive({});

const castsOptions = [
    { value: 'hyaline', label: 'Hyaline Casts', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'rbc', label: 'RBC Casts', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'wbc', label: 'WBC Casts', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'fine_granular', label: 'Fine Granular Casts', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'waxy', label: 'Waxy Casts', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
];

const handleCastSelection = (castType) => {
    if (selectedCastTypes.value.includes(castType)) {
        if (!castDetailsMap[castType]) {
            castDetailsMap[castType] = [];
        }
    } else {
        delete castDetailsMap[castType];
    }
};

// Watch for changes in casts and update the form data
watch([selectedCastTypes, castDetailsMap], () => {
    const castsArray = selectedCastTypes.value.map(castType => ({
        type: castType,
        details: castDetailsMap[castType] || []
    }));
    clinicalForm.urinalysis_casts = castsArray;
}, { deep: true });

const crystalsOptions = [
    { value: 'ammonium_biurate', label: 'Ammonium biurate', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'bilirubin', label: 'Bilirubin', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'calcium_oxalate', label: 'Calcium oxalate', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'cystine', label: 'Cystine', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'hippuric_acid', label: 'Hippuric acid', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'leucine', label: 'Leucine', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'struvite', label: 'Struvite', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'tyrosine', label: 'Tyrosine', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'uric_acid', label: 'Uric acid', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'xanthine', label: 'Xanthine', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
];

const fungalOptions = [
    { value: 'yeast_cells', label: 'Yeast Cells', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'pseudohyphae', label: 'Pseudohyphae', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
    { value: 'hyphae', label: 'Hyphae', subOptions: ['Rare', 'Few', 'Moderate', 'Many'] },
];

const parasiteOptions = [
  { value: 'trichomonas_vaginalis', label: 'Trichomonas Vaginalis' },
  { value: 'schistosoma_haematobium', label: 'Schistosoma Haematobium' },
];

const selectedCrystalTypes = ref([]);
const selectedFungalTypes = ref([]);
const selectedParasiteTypes = ref([]);
const crystalDetailsMap = reactive({});
const fungalDetailsMap = reactive({});
const parasiteDetailsMap = reactive({});

const handleCrystalSelection = (crystalType) => {
    if (selectedCrystalTypes.value.includes(crystalType)) {
        if (!crystalDetailsMap[crystalType]) {
            crystalDetailsMap[crystalType] = [];
        }
    } else {
        delete crystalDetailsMap[crystalType];
    }
};

const handleFungalSelection = (fungalType) => {
    if (selectedFungalTypes.value.includes(fungalType)) {
        if (!fungalDetailsMap[fungalType]) {
            fungalDetailsMap[fungalType] = [];
        }
    } else {
        delete fungalDetailsMap[fungalType];
    }
};

const handleParasiteSelection = (parasiteType) => {
    if (selectedParasiteTypes.value.includes(parasiteType)) {
        if (!parasiteDetailsMap[parasiteType]) {
            parasiteDetailsMap[parasiteType] = '';  // Initialize as empty string
        }
    } else {
        delete parasiteDetailsMap[parasiteType];
    }
};

// Watch for changes in crystals, fungal elements, and parasites
watch([selectedCrystalTypes, crystalDetailsMap], () => {
    const crystalsArray = selectedCrystalTypes.value.map(crystalType => ({
        type: crystalType,
        details: crystalDetailsMap[crystalType] || []
    }));
    clinicalForm.urinalysis_crystals = crystalsArray;
}, { deep: true });

watch([selectedFungalTypes, fungalDetailsMap], () => {
    const fungalArray = selectedFungalTypes.value.map(fungalType => ({
        type: fungalType,
        details: fungalDetailsMap[fungalType] || []
    }));
    clinicalForm.urinalysis_fungal_elements = fungalArray;
}, { deep: true });

watch([selectedParasiteTypes, parasiteDetailsMap], () => {
    const parasiteArray = selectedParasiteTypes.value.map(parasiteType => ({
        type: parasiteType,
        details: parasiteDetailsMap[parasiteType] ? [parasiteDetailsMap[parasiteType]] : []  // Convert string to array
    }));
    clinicalForm.urinalysis_parasite = parasiteArray;
}, { deep: true });

// Form submission method
const submitClinicalTests = () => {
    if (!selectedPatient.value) {
        alert('Please select a patient first.');
        return;
    }
        
    // Ensure patient_id is set before submission
    clinicalForm.patient_id = selectedPatient.value.id;
        
    // Determine if we're updating or creating
    const isUpdating = editingTestId.value !== null;
    const submitRoute = isUpdating 
        ? route('clinical.update', editingTestId.value)
        : route('clinical.store');
    
    const submitMethod = isUpdating ? 'put' : 'post';
    
    clinicalForm[submitMethod](submitRoute, {
        onSuccess: (page) => {
            // Clear the form after successful submission
            clinicalForm.reset();
            selectedPatient.value = null;
            patientSearchQuery.value = '';
            editingTestId.value = null;
            // Clear selection arrays
            selectedCastTypes.value = [];
            selectedCrystalTypes.value = [];
            selectedFungalTypes.value = [];
            selectedParasiteTypes.value = [];
            // Clear detail maps
            Object.keys(castDetailsMap).forEach(key => delete castDetailsMap[key]);
            Object.keys(crystalDetailsMap).forEach(key => delete crystalDetailsMap[key]);
            Object.keys(fungalDetailsMap).forEach(key => delete fungalDetailsMap[key]);
            Object.keys(parasiteDetailsMap).forEach(key => delete parasiteDetailsMap[key]);
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
const glucoseOptions = ['Negative', 'Trace', '1+', '2+', '3+', '4+'];
const proteinOptions = ['Negative', 'Trace', '1+', '2+', '3+', '4+'];
const spgravityOptions = ['1.000', '1.005', '1.010', '1.015', '1.020', '1.025', '1.030'];
const bacteriaOptions = ['None', 'Rare', 'Few', 'Moderate', 'Many'];
const epithelialCellsOptions = ['None', 'Rare', 'Few', 'Moderate', 'Many'];
const urinalysisColorOptions = ['Straw', 'Yellow', 'Light Yellow', 'Amber', 'Pink', 'Red', 'Brown', 'Green'];
const urinalysisTransparencyOptions = ['Clear', 'Hazy', 'Slightly Turbid', 'Turbid'];
const amorphousUratesOptions = ['None', 'Rare', 'Few', 'Moderate', 'Many'];
const amorphousPhospatesOptions = ['None', 'Rare', 'Few', 'Moderate', 'Many'];
const MucusOptions = ['None', 'Rare', 'Few', 'Moderate', 'Many'];
const fecalysisColorOptions = ['Brown', 'Light Brown', 'Dark Brown', 'Black', 'Green', 'Yellow', 'Red'];
const fecalysisConsistencyOptions = ['Soft', 'Formed', 'Hard', 'Watery', 'Semi-formed'];

const medicalTechnologists = [
    'KRISTINA CASSANDRA F. SANTOS, RMT',
    'KATE ANGELINE M. SALAS, RMT',
    'JULLIUS D. ANUSENCION, RMT',
    'MARY GRACE L. BERNARDO, RMT',
    'JANIELLE M. PASAMONTE, RMT',
];

// Computed properties to determine which field to show
const showPhospatesField = computed(() => {
    return clinicalForm.ph !== null && clinicalForm.ph !== '' && parseFloat(clinicalForm.ph) >= 7.0;
});

const showAmorphousField = computed(() => {
    return clinicalForm.ph !== null && clinicalForm.ph !== '' && parseFloat(clinicalForm.ph) < 7.0;
});

const showPlaceholderField = computed(() => {
    return !clinicalForm.ph || clinicalForm.ph === '';
});
</script>

<template>
    <Head title="Clinical Microscopy Tests" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
            <div class="w-full py-8 px-6">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-4 rounded-2xl shadow-lg">
                            <TestTube class="h-10 w-10 text-white" />
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Clinical Microscopy Tests</h1>
                    <p class="text-lg text-gray-600">Comprehensive urinalysis and fecalysis testing platform</p>
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
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 rounded-2xl mr-4 shadow-lg">
                                    <Activity class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Laboratory Test Results</h2>
                                    <p class="text-gray-600">View and manage all clinical microscopy test results</p>
                                </div>
                            </div>

                            <!-- Results Display -->
                            <div v-if="Object.keys(testsByPatient).length > 0" class="space-y-6">
                                <div v-for="(patientData, patientId) in testsByPatient" :key="patientId" 
                                     class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                    <!-- Patient Header -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center">
                                            <div class="bg-blue-100 p-3 rounded-full mr-4">
                                                <User class="h-6 w-6 text-blue-600" />
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
                                            <p class="text-2xl font-bold text-blue-600">{{ patientData.tests.length }}</p>
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
                                                <button 
                                                    @click="editTest(test); showResultsSection = false"
                                                    class="bg-blue-100 text-blue-600 p-2 rounded-lg hover:bg-blue-200 transition-colors duration-200"
                                                    title="Edit Test"
                                                >
                                                    <Edit class="h-4 w-4" />
                                                </button>
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
                                <p class="text-gray-600 mb-6">No clinical microscopy tests have been conducted yet.</p>
                                <button 
                                    @click="showResultsSection = false"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 flex items-center mx-auto"
                                >
                                    <TestTube class="h-5 w-5 mr-2" />
                                    Add First Test
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section (existing form code) -->
                <div v-else class="space-y-8">
                    <!-- Patient Selection Card -->
                    <div class="bg-white rounded-3xl shadow-xl p-8 mb-8 border border-gray-100">
                        <div class="flex items-center mb-6">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 rounded-2xl mr-4 shadow-lg">
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
                                    class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 text-lg transition-all duration-200"
                                />
                            </div>
                                                
                            <!-- Search Results Dropdown -->
                            <div v-if="patientSearchQuery && filteredPatients.length > 0"
                                class="absolute z-20 w-full bg-white border-2 border-gray-200 rounded-2xl mt-2 max-h-80 overflow-y-auto shadow-2xl">
                                <div v-for="patient in filteredPatients" :key="patient.id"
                                     @click="selectPatient(patient)"
                                    class="px-6 py-4 cursor-pointer hover:bg-blue-50 transition-colors duration-200 border-b border-gray-100 last:border-b-0">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-semibold text-gray-900 text-lg">{{ patient.name }}</div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                ID: {{ patient.id }} | DOB: {{ formatDate(patient.date_of_birth) }} | {{ patient.gender }}
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
                                    <Microscope class="h-8 w-8 text-white" />
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">
                                        {{ editingTestId ? 'Edit Clinical Microscopy Test' : 'Clinical Microscopy Tests' }}
                                    </h2>
                                    <p class="text-gray-600">
                                        {{ editingTestId ? 'Update test results and information' : 'Select test type and enter results' }}
                                    </p>
                                </div>
                            </div>
                                                
                            <!-- Test Type Selector -->
                            <div class="flex space-x-2 mb-8 p-2 bg-gray-100 rounded-2xl">
                                <button @click="selectedClinicalMicroscopyTest = 'urinalysis'"
                                         :class="{
                                            'bg-white text-blue-600 shadow-lg': selectedClinicalMicroscopyTest === 'urinalysis',
                                            'text-gray-600 hover:text-gray-800': selectedClinicalMicroscopyTest !== 'urinalysis'
                                        }"
                                         class="flex-1 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center">
                                    <FileText class="h-5 w-5 mr-2" />
                                    Urinalysis
                                </button>
                                <button @click="selectedClinicalMicroscopyTest = 'fecalysis'"
                                         :class="{
                                            'bg-white text-blue-600 shadow-lg': selectedClinicalMicroscopyTest === 'fecalysis',
                                            'text-gray-600 hover:text-gray-800': selectedClinicalMicroscopyTest !== 'fecalysis'
                                        }"
                                         class="flex-1 px-6 py-4 rounded-xl font-semibold transition-all duration-200 flex items-center justify-center">
                                    <TestTube class="h-5 w-5 mr-2" />
                                    Fecalysis
                                </button>
                            </div>
                                                
                            <form @submit.prevent="submitClinicalTests" class="space-y-8">
                                <!-- Urinalysis Section -->
                                <div v-if="selectedClinicalMicroscopyTest === 'urinalysis'" class="space-y-8">
                                    <!-- Physical Analysis -->
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-blue-500 rounded-full mr-3"></div>
                                            Physical Analysis
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Color</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_color"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select color</option>
                                                        <option v-for="option in urinalysisColorOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_color" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_color }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Transparency</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_transparency"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select transparency</option>
                                                        <option v-for="option in urinalysisTransparencyOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_transparency" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_transparency }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chemical Analysis -->
                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-green-500 rounded-full mr-3"></div>
                                            Chemical Analysis
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Glucose</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_glucose"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select Glucose</option>
                                                        <option v-for="glucose in glucoseOptions" :key="glucose" :value="glucose">
                                                            {{ glucose }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_glucose" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_glucose }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Protein</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_protein"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select Protein</option>
                                                        <option v-for="protein in proteinOptions" :key="protein" :value="protein">
                                                            {{ protein }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_protein" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_protein }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">pH</label>
                                                <input v-model="clinicalForm.ph" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all duration-200"
                                                     placeholder="e.g., 7.0" />
                                                <div v-if="clinicalForm.errors.ph" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.ph }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Specific Gravity</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_spgravity"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select Specific Gravity</option>
                                                        <option v-for="sp in spgravityOptions" :key="sp" :value="sp">
                                                            {{ sp }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_spgravity" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_spgravity }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Microscopic Analysis -->
                                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-purple-500 rounded-full mr-3"></div>
                                            Microscopic Analysis
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-6 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">WBC</label>
                                                <input v-model="clinicalForm.urinalysis_wbc" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200" />
                                                <div v-if="clinicalForm.errors.urinalysis_wbc" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_wbc }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">RBC</label>
                                                <input v-model="clinicalForm.urinalysis_rbc" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200" />
                                                <div v-if="clinicalForm.errors.urinalysis_rbc" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_rbc }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Bacteria</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_bacteria"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select bacteria level</option>
                                                        <option v-for="option in bacteriaOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_bacteria" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_bacteria }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Epithelial Cells</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_epithelial_cells"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select cell count</option>
                                                        <option v-for="option in epithelialCellsOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_epithelial_cells" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_epithelial_cells }}
                                                </div>
                                            </div>
                                                                                    
                                            <!-- Conditional Amorphous/Phospates Field -->
                                            <div v-if="showPhospatesField">
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Phospates Urates</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_phospates"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select level</option>
                                                        <option v-for="option in amorphousPhospatesOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_phospates" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_phospates }}
                                                </div>
                                            </div>
                                            <div v-else-if="showAmorphousField">
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Amorphous Urates</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_amorphous"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select level</option>
                                                        <option v-for="option in amorphousUratesOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_amorphous" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_amorphous }}
                                                </div>
                                            </div>
                                            <div v-else-if="showPlaceholderField">
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Amorphous Urates/Phospates</label>
                                                <input type="text" placeholder="Enter pH to see options"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl bg-gray-100 cursor-not-allowed transition-all duration-200" readonly />
                                            </div>
                                                                                    
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Mucus Threads</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.urinalysis_mucus_threads"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select level</option>
                                                        <option v-for="option in MucusOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.urinalysis_mucus_threads" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.urinalysis_mucus_threads }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Crystals Section -->
                                        <div class="mt-8">
                                            <label class="block text-lg font-semibold text-gray-800 mb-4">Crystals</label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                                <div v-for="crystal in crystalsOptions" :key="crystal.value"
                                                     class="border-2 border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-all duration-200 bg-white">
                                                    <label class="flex items-center space-x-3 mb-3">
                                                        <input type="checkbox" :value="crystal.value" v-model="selectedCrystalTypes"
                                                            @change="handleCrystalSelection(crystal.value)"
                                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-5 h-5" />
                                                        <span class="font-medium text-gray-900">{{ crystal.label }}</span>
                                                    </label>
                                                    <div v-if="selectedCrystalTypes.includes(crystal.value)" class="ml-8 space-y-2">
                                                        <label class="block text-sm text-gray-600 mb-2">Details:</label>
                                                        <div class="flex flex-wrap gap-2">
                                                            <label v-for="option in crystal.subOptions" :key="option"
                                                                class="inline-flex items-center space-x-2 bg-purple-50 px-3 py-2 rounded-full border border-purple-200">
                                                                <input type="checkbox" :value="option" v-model="crystalDetailsMap[crystal.value]"
                                                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" />
                                                                <span class="text-sm">{{ option }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Fungal Elements Section -->
                                        <div class="mt-8">
                                            <label class="block text-lg font-semibold text-gray-800 mb-4">Fungal Elements</label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-3 gap-4">
                                                <div v-for="fungal in fungalOptions" :key="fungal.value"
                                                     class="border-2 border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-all duration-200 bg-white">
                                                    <label class="flex items-center space-x-3 mb-3">
                                                        <input type="checkbox" :value="fungal.value" v-model="selectedFungalTypes"
                                                            @change="handleFungalSelection(fungal.value)"
                                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-5 h-5" />
                                                        <span class="font-medium text-gray-900">{{ fungal.label }}</span>
                                                    </label>
                                                    <div v-if="selectedFungalTypes.includes(fungal.value)" class="ml-8 space-y-2">
                                                        <label class="block text-sm text-gray-600 mb-2">Details:</label>
                                                        <div class="flex flex-wrap gap-2">
                                                            <label v-for="option in fungal.subOptions" :key="option"
                                                                class="inline-flex items-center space-x-2 bg-purple-50 px-3 py-2 rounded-full border border-purple-200">
                                                                <input type="checkbox" :value="option" v-model="fungalDetailsMap[fungal.value]"
                                                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" />
                                                                <span class="text-sm">{{ option }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Parasite Section -->
                                        <div class="mt-8">
                                            <label class="block text-lg font-semibold text-gray-800 mb-4">Parasite</label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-2 gap-4">
                                                <div v-for="parasite in parasiteOptions" :key="parasite.value"
                                                     class="border-2 border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-all duration-200 bg-white">
                                                    <label class="flex items-center space-x-3 mb-3">
                                                        <input type="checkbox" :value="parasite.value" v-model="selectedParasiteTypes"
                                                            @change="handleParasiteSelection(parasite.value)"
                                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-5 h-5" />
                                                        <span class="font-medium text-gray-900">{{ parasite.label }}</span>
                                                    </label>
                                                    <div v-if="selectedParasiteTypes.includes(parasite.value)" class="ml-8">
                                                        <label class="block text-sm text-gray-600 mb-2">Details:</label>
                                                        <input type="text" v-model="parasiteDetailsMap[parasite.value]"
                                                            placeholder="Enter details..."
                                                            class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-200" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Casts Section -->
                                        <div class="mt-8">
                                            <label class="block text-lg font-semibold text-gray-800 mb-4">Casts</label>
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                                                <div v-for="cast in castsOptions" :key="cast.value"
                                                     class="border-2 border-gray-200 rounded-xl p-4 hover:border-purple-300 transition-all duration-200 bg-white">
                                                    <label class="flex items-center space-x-3 mb-3">
                                                        <input type="checkbox" :value="cast.value" v-model="selectedCastTypes"
                                                            @change="handleCastSelection(cast.value)"
                                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 w-5 h-5" />
                                                        <span class="font-medium text-gray-900">{{ cast.label }}</span>
                                                    </label>
                                                    <div v-if="selectedCastTypes.includes(cast.value)" class="ml-8 space-y-2">
                                                        <label class="block text-sm text-gray-600 mb-2">Details:</label>
                                                        <div class="flex flex-wrap gap-2">
                                                            <label v-for="option in cast.subOptions" :key="option"
                                                                class="inline-flex items-center space-x-2 bg-purple-50 px-3 py-2 rounded-full border border-purple-200">
                                                                <input type="checkbox" :value="option" v-model="castDetailsMap[cast.value]"
                                                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" />
                                                                <span class="text-sm">{{ option }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remarks -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Urinalysis Remarks</label>
                                        <textarea v-model="clinicalForm.urinalysis_remarks" rows="4"
                                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200"
                                            placeholder="Enter any additional remarks or observations..."></textarea>
                                        <div v-if="clinicalForm.errors.urinalysis_remarks" class="text-red-500 text-sm mt-2 font-medium">
                                            {{ clinicalForm.errors.urinalysis_remarks }}
                                        </div>
                                    </div>

                                    <!-- pH Information -->
                                    <div v-if="clinicalForm.ph" class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                                        <p class="text-sm text-blue-800">
                                            <strong>pH Level:</strong> {{ clinicalForm.ph }} -
                                            <span v-if="parseFloat(clinicalForm.ph) >= 7.0" class="text-green-700 font-medium">
                                                Alkaline (Phospates Urates)
                                            </span>
                                            <span v-else class="text-orange-700 font-medium">
                                                Acidic (Amorphous Urates)
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Fecalysis Section -->
                                <div v-if="selectedClinicalMicroscopyTest === 'fecalysis'" class="space-y-8">
                                    <!-- Physical Analysis -->
                                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-orange-500 rounded-full mr-3"></div>
                                            Physical Analysis
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Color</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.fecalysis_color"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select color</option>
                                                        <option v-for="option in fecalysisColorOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.fecalysis_color" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.fecalysis_color }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">Consistency</label>
                                                <div class="relative">
                                                    <select v-model="clinicalForm.fecalysis_consistency"
                                                        class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-100 focus:border-orange-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                                        <option value="">Select consistency</option>
                                                        <option v-for="option in fecalysisConsistencyOptions" :key="option" :value="option">
                                                            {{ option }}
                                                        </option>
                                                    </select>
                                                    <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                                </div>
                                                <div v-if="clinicalForm.errors.fecalysis_consistency" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.fecalysis_consistency }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Microscopic Analysis -->
                                    <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-2xl p-6 border border-red-200">
                                        <h4 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                                            <div class="w-3 h-8 bg-red-500 rounded-full mr-3"></div>
                                            Microscopic Analysis
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">WBC</label>
                                                <input v-model="clinicalForm.fecalysis_wbc" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200" />
                                                <div v-if="clinicalForm.errors.fecalysis_wbc" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.fecalysis_wbc }}
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-3">RBC</label>
                                                <input v-model="clinicalForm.fecalysis_rbc" type="text"
                                                    class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-red-100 focus:border-red-500 transition-all duration-200" />
                                                <div v-if="clinicalForm.errors.fecalysis_rbc" class="text-red-500 text-sm mt-2 font-medium">
                                                    {{ clinicalForm.errors.fecalysis_rbc }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remarks and Results -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Remarks</label>
                                            <textarea v-model="clinicalForm.fecalysis_remarks" rows="4"
                                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200"
                                                placeholder="Enter any remarks..."></textarea>
                                            <div v-if="clinicalForm.errors.fecalysis_remarks" class="text-red-500 text-sm mt-2 font-medium">
                                                {{ clinicalForm.errors.fecalysis_remarks }}
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Results</label>
                                            <textarea v-model="clinicalForm.fecalysis_results" rows="4"
                                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-200"
                                                placeholder="Enter test results..."></textarea>
                                            <div v-if="clinicalForm.errors.fecalysis_results" class="text-red-500 text-sm mt-2 font-medium">
                                                {{ clinicalForm.errors.fecalysis_results }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Technologist -->
                                <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Medical Technologist</label>
                                    <div class="relative">
                                        <select v-model="clinicalForm.medical_technologist"
                                            class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 bg-white appearance-none cursor-pointer transition-all duration-200">
                                            <option value="">Select Medical Technologist</option>
                                            <option v-for="tech in medicalTechnologists" :key="tech" :value="tech">
                                                {{ tech }}
                                            </option>
                                        </select>
                                        <ChevronDown class="absolute right-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none" />
                                    </div>
                                    <div v-if="clinicalForm.errors.medical_technologist" class="text-red-500 text-sm mt-2 font-medium">
                                        {{ clinicalForm.errors.medical_technologist }}
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex flex-col items-center space-y-4">
                                    <button type="submit"
                                         :disabled="clinicalForm.processing || !selectedPatient"
                                        class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-2xl hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center">
                                        <span v-if="clinicalForm.processing" class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ editingTestId ? 'Updating Test...' : 'Submitting Test...' }}
                                        </span>
                                        <span v-else class="flex items-center">
                                            <Save class="h-5 w-5 mr-2" />
                                            {{ editingTestId ? 'Update Clinical Test' : 'Submit Clinical Test' }}
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
    <!-- Add this temporarily after the submit button for debugging -->
    <button type="button" @click="logCurrentState" 
            class="mt-4 px-4 py-2 bg-gray-500 text-white rounded-lg">
        Debug Current State
    </button>
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
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
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