<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue' // Import 'watch' for search query
import { useForm, router } from '@inertiajs/vue3'
import {
  Users,
  User,
  UserPlus,
  Database,
  Search,
  Save,
  Edit,
  Trash2,
  CheckCircle,
  AlertTriangle,
  FileText,
  Calendar,
  Building
} from 'lucide-vue-next'

// Define props to receive data from the controller
const props = defineProps({
  patients: {
    type: Object, // Changed to Object for paginated data
    default: () => ({
      data: [],
      links: [], // Add links for pagination
      current_page: 1,
      last_page: 1,
      from: 0,
      to: 0,
      total: 0,
    })
  },
  filters: {
    type: Object,
    default: () => ({
      search: ''
    })
  }
})


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Patient',
        href: '/patient',
    },
];

// Reactive data
const searchQuery = ref(props.filters.search || '') // Initialize from props.filters
const showDeleteModal = ref(false)
const showPatientModal = ref(false)
const editingPatient = ref(null)
const patientToDelete = ref(null)

// Inertia form for adding/editing patients
const form = useForm({
  name: '',
  date_of_birth: '',
  gender: '',
  company: '',
  address: ''
})

// Inertia form for deleting patients
const deleteForm = useForm({})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})

const calculatedAge = computed(() => {
  if (!form.date_of_birth) return ''
  return calculateAge(form.date_of_birth)
})

// Watch for changes in searchQuery and trigger a server-side search
watch(searchQuery, (newQuery) => {
  router.get('/patient', { search: newQuery }, { preserveState: true, replace: true });
});

// Methods
const calculateAge = (dateOfBirth) => {
  if (!dateOfBirth) return ''
  const today = new Date()
  const birthDate = new Date(dateOfBirth)
  let age = today.getFullYear() - birthDate.getFullYear()
  const monthDiff = today.getMonth() - birthDate.getMonth()
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--
  }
  return age
}
// Format date helper
const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

// Normalize test results to array for safety
const normalizeTestResults = (testResults) => {
  if (!testResults) return []
  if (Array.isArray(testResults)) return testResults
  // Sometimes Laravel returns single related model as object
  if (typeof testResults === 'object') return [testResults]
  return []
}

// Get test status (Not Done / Completed with count)
const getTestStatus = (testResults) => {
  const tests = normalizeTestResults(testResults)
  if (tests.length === 0) {
    return { status: 'Not Done', class: 'text-gray-500 bg-gray-100', count: 0 }
  }
  return { status: 'Completed', class: 'text-green-700 bg-green-100', count: tests.length }
}

// Get latest test date (formatted) or null if none
const getLatestTestDate = (testResults) => {
  const tests = normalizeTestResults(testResults)
  if (tests.length === 0) return null

  const latest = tests.reduce((latest, current) => {
    const latestDate = new Date(latest.created_at || latest.updated_at || latest.date)
    const currentDate = new Date(current.created_at || current.updated_at || current.date)
    return currentDate > latestDate ? current : latest
  })
  return formatDate(latest.created_at || latest.updated_at || latest.date)
}

// Function to open the add patient modal
const openAddPatientModal = () => {
  editingPatient.value = null
  resetForm()
  showPatientModal.value = true
}

// Function to close the patient modal
const closePatientModal = () => {
  showPatientModal.value = false
  editingPatient.value = null
  resetForm()
}

const savePatient = () => {
  if (editingPatient.value) {
    // Update existing patient
    form.put(`/patients/${editingPatient.value.id}`, { // Changed to form.put for updates
      onSuccess: () => {
        closePatientModal()
      }
    })
  } else {
    // Add new patient
    form.post('/patients', {
      onSuccess: () => {
        closePatientModal()
      }
    })
  }
}

const editPatient = (patient) => {
  editingPatient.value = patient
  form.name = patient.name
  form.date_of_birth = patient.date_of_birth
  form.gender = patient.gender
  form.company = patient.company || ''
  form.address = patient.address || ''
  showPatientModal.value = true
}

const deletePatient = (patientId) => {
  patientToDelete.value = patientId
  showDeleteModal.value = true
}

const confirmDelete = () => {
  deleteForm.delete(`/patients/${patientToDelete.value}`, {
    onSuccess: () => {
      showDeleteModal.value = false
      patientToDelete.value = null
    }
  })
}

const resetForm = () => {
  form.reset()
  form.clearErrors()
}
</script>

<template>

  <Head title="Patient" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gray-50">
      <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
        <div v-if="$page.props.flash.success" class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
          <div class="flex">
            <CheckCircle class="h-5 w-5 text-green-400 mr-2" />
            <div class="text-sm text-green-800">{{ $page.props.flash.success }}</div>
          </div>
        </div>

        <div v-if="$page.props.flash.error" class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
          <div class="flex">
            <AlertTriangle class="h-5 w-5 text-red-400 mr-2" />
            <div class="text-sm text-red-800">{{ $page.props.flash.error }}</div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <Database class="h-6 w-6 mr-2 text-blue-600" />
                Registered Patients ({{ patients.total }})
              </h2>
              <div class="flex items-center space-x-3">
                <div class="relative">
                  <Search class="h-4 w-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                  <input v-model="searchQuery" type="text" placeholder="Search patients..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <button @click="openAddPatientModal"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                  <UserPlus class="h-5 w-5 mr-2" />
                  Add New Patient
                </button>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Patient ID
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Patient Info
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Age/Gender
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Company
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Address
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Clinical Microscopy
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Hematology
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Serology
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Chemistry
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="patient in patients.data" :key="patient.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    #{{ patient.id }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                          <User class="h-5 w-5 text-blue-600" />
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ patient.name }}</div>
                        <div class="text-sm text-gray-500 flex items-center">
                          <Calendar class="h-3 w-3 mr-1" />
                          {{ formatDate(patient.date_of_birth) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="text-sm font-medium">{{ calculateAge(patient.date_of_birth) }} years</div>
                    <div class="text-sm text-gray-500">{{ patient.gender }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center text-sm text-gray-900">
                      <Building class="h-4 w-4 mr-2 text-gray-400" />
                      {{ patient.company || '-' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                    <div class="truncate" :title="patient.address">
                      {{ patient.address || '-' }}
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-center">
                      <span
                        :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTestStatus(patient.clinical).class]">
                        {{ getTestStatus(patient.clinical).status }}
                      </span>
                      <div v-if="getTestStatus(patient.clinical).count > 0" class="text-xs text-gray-500 mt-1">
                        {{ getTestStatus(patient.clinical).count }} test(s)
                      </div>
                      <div v-if="getLatestTestDate(patient.clinical)" class="text-xs text-gray-400 mt-1">
                        {{ getLatestTestDate(patient.clinical) }}
                      </div>
                      <div v-if="getTestStatus(patient.clinical).count > 0">
                        <button @click="router.visit(`/clinical/${patient.id}`)"
                          class="text-blue-600 hover:text-blue-900 text-xs mt-1">
                          View
                        </button>
                      </div>
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-center">
                      <span
                        :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTestStatus(patient.hematology).class]">
                        {{ getTestStatus(patient.hematology).status }}
                      </span>
                      <div v-if="getTestStatus(patient.hematology).count > 0" class="text-xs text-gray-500 mt-1">
                        {{ getTestStatus(patient.hematology).count }} test(s)
                      </div>
                      <div v-if="getLatestTestDate(patient.hematology)" class="text-xs text-gray-400 mt-1">
                        {{ getLatestTestDate(patient.hematology) }}
                      </div>
                      <div v-if="getTestStatus(patient.hematology).count > 0">
                        <button @click="router.visit(`/hematology/${patient.id}`)"
                          class="text-blue-600 hover:text-blue-900 text-xs mt-1">
                          View
                        </button>
                      </div>
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-center">
                      <span
                        :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTestStatus(patient.serology).class]">
                        {{ getTestStatus(patient.serology).status }}
                      </span>
                      <div v-if="getTestStatus(patient.serology).count > 0" class="text-xs text-gray-500 mt-1">
                        {{ getTestStatus(patient.serology).count }} test(s)
                      </div>
                      <div v-if="getLatestTestDate(patient.serology)" class="text-xs text-gray-400 mt-1">
                        {{ getLatestTestDate(patient.serology) }}
                      </div>
                      <div v-if="getTestStatus(patient.serology).count > 0">
                        <button @click="router.visit(`/serology/${patient.id}`)"
                          class="text-blue-600 hover:text-blue-900 text-xs mt-1">
                          View
                        </button>
                      </div>
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-center">
                      <span
                        :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTestStatus(patient.chemistry).class]">
                        {{ getTestStatus(patient.chemistry).status }}
                      </span>
                      <div v-if="getTestStatus(patient.chemistry).count > 0" class="text-xs text-gray-500 mt-1">
                        {{ getTestStatus(patient.chemistry).count }} test(s)
                      </div>
                      <div v-if="getLatestTestDate(patient.chemistry)" class="text-xs text-gray-400 mt-1">
                        {{ getLatestTestDate(patient.chemistry) }}
                      </div>
                      <div v-if="getTestStatus(patient.chemistry).count > 0">
                        <button @click="router.visit(`/chemistry/${patient.id}`)"
                          class="text-blue-600 hover:text-blue-900 text-xs mt-1">
                          View
                        </button>
                      </div>
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button @click="editPatient(patient)" class="text-blue-600 hover:text-blue-900 flex items-center">
                        <Edit class="h-4 w-4 mr-1" />
                        Edit
                      </button>
                      <button @click="deletePatient(patient.id)"
                        class="text-red-600 hover:text-red-900 flex items-center">
                        <Trash2 class="h-4 w-4 mr-1" />
                        Delete
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div v-if="patients.data.length === 0" class="text-center py-12">
              <Users class="h-12 w-12 text-gray-400 mx-auto mb-4" />
              <p class="text-gray-500 text-lg">
                {{ searchQuery ? 'No patients found matching your search.' : 'No patients registered yet.' }}
              </p>
              <p class="text-gray-400 mt-2">
                {{ searchQuery ? 'Try adjusting your search terms.' : 'Click "Add New Patient" to register your first patient.' }}
              </p>
            </div>
          </div>

          <div v-if="patients.links.length > 3" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <nav class="flex justify-center items-center space-x-2">
              <template v-for="(link, key) in patients.links">
                <div v-if="link.url === null" :key="key"
                  class="px-4 py-2 text-sm leading-4 text-gray-400 bg-white rounded-md border border-gray-300 cursor-default"
                  v-html="link.label" />
                <a v-else :key="`link-${key}`" :href="link.url"
                  :class="['px-4 py-2 text-sm leading-4 rounded-md border', {
                    'bg-blue-600 text-white border-blue-600': link.active,
                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-100': !link.active
                  }]" v-html="link.label" />
              </template>
            </nav>
          </div>
        </div>
      </div>

      <div v-if="showPatientModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
          <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <UserPlus class="h-6 w-6 mr-2 text-green-600" />
            {{ editingPatient ? 'Edit Patient' : 'Add New Patient' }}
          </h2>

          <form @submit.prevent="savePatient" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
              <input v-model="form.name" type="text" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter full name" />
              <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
              <input v-model="form.date_of_birth" type="date" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
              <p v-if="calculatedAge" class="text-sm text-gray-500 mt-1">Age: {{ calculatedAge }} years old</p>
              <div v-if="form.errors.date_of_birth" class="text-red-500 text-sm mt-1">{{ form.errors.date_of_birth }}
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
              <select v-model="form.gender" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
              <div v-if="form.errors.gender" class="text-red-500 text-sm mt-1">{{ form.errors.gender }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Company/Organization *</label>
              <input v-model="form.company" type="text" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter company or organization" />
              <div v-if="form.errors.company" class="text-red-500 text-sm mt-1">{{ form.errors.company }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
              <textarea v-model="form.address" rows="3" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                placeholder="Enter complete address"></textarea>
              <div v-if="form.errors.address" class="text-red-500 text-sm mt-1">{{ form.errors.address }}</div>
            </div>

            <div class="flex space-x-3 pt-4">
              <button type="submit" :disabled="form.processing"
                class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <Save class="h-4 w-4 inline mr-2" />
                <span v-if="form.processing">Processing...</span>
                <span v-else>{{ editingPatient ? 'Update Patient' : 'Add Patient' }}</span>
              </button>
              <button type="button" @click="closePatientModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
          <div class="flex items-center mb-4">
            <AlertTriangle class="h-6 w-6 text-red-500 mr-2" />
            <h3 class="text-lg font-semibold">Confirm Delete</h3>
          </div>
          <p class="text-gray-600 mb-4">Are you sure you want to delete this patient? This action cannot be undone.</p>
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
    </div>
  </AppLayout>
</template>