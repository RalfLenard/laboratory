<template>
    <div class="min-h-screen bg-gray-50">
      <!-- Header -->
      <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between items-center py-4">
            <div class="flex items-center">
              <Activity class="h-8 w-8 text-blue-600 mr-3" />
              <h1 class="text-2xl font-bold text-gray-900">Laboratory Information Management System</h1>
            </div>
            <div class="flex items-center space-x-4">
              <span class="text-sm text-gray-500">{{ currentDate }}</span>
              <div class="flex items-center space-x-2">
                <User class="h-5 w-5 text-gray-400" />
                <span class="text-sm font-medium text-gray-700">Lab Technician</span>
              </div>
            </div>
          </div>
        </div>
      </header>
  
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Navigation Tabs -->
        <div class="mb-6">
          <nav class="flex space-x-8">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-sm',
                activeTab === tab.id
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              <component :is="tab.icon" class="h-5 w-5 inline mr-2" />
              {{ tab.name }}
            </button>
          </nav>
        </div>
  
        <!-- Patient Selection -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Patient Selection</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
              <label class="block text-sm font-medium text-gray-700 mb-1">Search Patient *</label>
              <div class="relative">
                <Search class="h-4 w-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  v-model="patientSearchQuery"
                  @input="handlePatientSearch"
                  @focus="showPatientSuggestions = true"
                  @blur="hidePatientSuggestions"
                  type="text"
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Search by name, ID, or company..."
                  required
                />
                
                <!-- Search Suggestions -->
                <div 
                  v-if="showPatientSuggestions && filteredPatients.length > 0" 
                  class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
                >
                  <div
                    v-for="patient in filteredPatients.slice(0, 10)"
                    :key="patient.id"
                    @mousedown="selectPatient(patient)"
                    class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                  >
                    <div class="flex justify-between items-center">
                      <div>
                        <div class="font-medium text-gray-900">{{ patient.name }}</div>
                        <div class="text-sm text-gray-500">
                          ID: {{ patient.id }} • {{ calculateAge(patient.dateOfBirth) }}yrs/{{ capitalizeFirst(patient.gender) }}
                        </div>
                        <div v-if="patient.company" class="text-xs text-gray-400">{{ patient.company }}</div>
                      </div>
                      <User class="h-4 w-4 text-gray-400" />
                    </div>
                  </div>
                  
                  <div v-if="patientSearchQuery && filteredPatients.length === 0" class="px-4 py-3 text-gray-500 text-center">
                    <AlertCircle class="h-4 w-4 inline mr-1" />
                    No patients found matching "{{ patientSearchQuery }}"
                  </div>
                </div>
              </div>
            </div>
            
            <div class="flex items-center">
              <div v-if="!patients.length" class="text-sm text-gray-500">
                <AlertCircle class="h-4 w-4 inline mr-1" />
                No patients found. Please register patients first.
              </div>
              <div v-else-if="!selectedPatient && patientSearchQuery" class="text-sm text-blue-600">
                <Search class="h-4 w-4 inline mr-1" />
                {{ filteredPatients.length }} patient(s) found
              </div>
              <div v-else-if="selectedPatient" class="text-sm text-green-600">
                <CheckCircle class="h-4 w-4 inline mr-1" />
                Patient selected: {{ selectedPatient.name }}
              </div>
            </div>
          </div>
          
          <!-- Selected Patient Info -->
          <div v-if="selectedPatient" class="mt-4 bg-blue-50 rounded-lg p-4">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="text-md font-semibold text-blue-900 mb-2">Selected Patient Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div>
                    <span class="font-medium text-gray-700">Name:</span>
                    <span class="ml-2 text-gray-900">{{ selectedPatient.name }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">Age/Gender:</span>
                    <span class="ml-2 text-gray-900">{{ calculateAge(selectedPatient.dateOfBirth) }} years / {{ capitalizeFirst(selectedPatient.gender) }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">Patient ID:</span>
                    <span class="ml-2 text-gray-900">{{ selectedPatient.id }}</span>
                  </div>
                  <div v-if="selectedPatient.company">
                    <span class="font-medium text-gray-700">Company:</span>
                    <span class="ml-2 text-gray-900">{{ selectedPatient.company }}</span>
                  </div>
                  <div v-if="selectedPatient.phone">
                    <span class="font-medium text-gray-700">Phone:</span>
                    <span class="ml-2 text-gray-900">{{ selectedPatient.phone }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">Date of Birth:</span>
                    <span class="ml-2 text-gray-900">{{ formatDate(selectedPatient.dateOfBirth) }}</span>
                  </div>
                </div>
              </div>
              <button
                @click="clearPatientSelection"
                class="text-gray-400 hover:text-gray-600 focus:outline-none"
                title="Clear selection"
              >
                <X class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
  
        <!-- Clinical Microscopy -->
        <div v-if="activeTab === 'microscopy'" class="space-y-6">
          <!-- Test Selection -->
          <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
              <Microscope class="h-5 w-5 mr-2 text-blue-600" />
              Select Test
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Test Type</label>
                <select
                  v-model="selectedMicroscopyTest"
                  :disabled="!selectedPatient"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                >
                  <option value="">Select Test</option>
                  <option value="fecalysis">Fecalysis</option>
                  <option value="urinalysis">Urinalysis</option>
                </select>
              </div>
            </div>
            <div v-if="!selectedPatient" class="mt-2 text-sm text-amber-600">
              <AlertCircle class="h-4 w-4 inline mr-1" />
              Please select a patient first to proceed with tests.
            </div>
          </div>
  
          <!-- Fecalysis -->
          <div v-if="selectedMicroscopyTest === 'fecalysis'" class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
              <Microscope class="h-5 w-5 mr-2 text-green-600" />
              Fecalysis
            </h3>
            
            <!-- Physical Analysis -->
            <div class="mb-6">
              <h4 class="text-md font-medium text-gray-800 mb-3">Physical Analysis</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                  <select
                    v-model="fecalysis.physical.color"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select Color</option>
                    <option value="brown">Brown</option>
                    <option value="yellow">Yellow</option>
                    <option value="green">Green</option>
                    <option value="black">Black</option>
                    <option value="red">Red</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Consistency</label>
                  <select
                    v-model="fecalysis.physical.consistency"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select Consistency</option>
                    <option value="formed">Formed</option>
                    <option value="soft">Soft</option>
                    <option value="loose">Loose</option>
                    <option value="watery">Watery</option>
                    <option value="mucoid">Mucoid</option>
                  </select>
                </div>
              </div>
            </div>
  
            <!-- Microscopic Analysis -->
            <div class="mb-6">
              <h4 class="text-md font-medium text-gray-800 mb-3">Microscopic Analysis</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">WBC (/hpf)</label>
                  <input
                    v-model="fecalysis.microscopic.wbc"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., 0-2"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">RBC (/hpf)</label>
                  <input
                    v-model="fecalysis.microscopic.rbc"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g., 0-1"
                  />
                </div>
              </div>
            </div>
  
            <!-- Results -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Results/Findings</label>
              <textarea
                v-model="fecalysis.results"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter findings and interpretation"
              ></textarea>
            </div>
          </div>
  
          <!-- Urinalysis -->
          <div v-if="selectedMicroscopyTest === 'urinalysis'" class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
              <Droplets class="h-5 w-5 mr-2 text-blue-600" />
              Urinalysis
            </h3>
  
            <!-- Physical Analysis -->
            <div class="mb-6">
              <h4 class="text-md font-medium text-gray-800 mb-3">Physical Analysis</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                  <select
                    v-model="urinalysis.physical.color"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select Color</option>
                    <option value="pale yellow">Pale Yellow</option>
                    <option value="yellow">Yellow</option>
                    <option value="dark yellow">Dark Yellow</option>
                    <option value="amber">Amber</option>
                    <option value="red">Red</option>
                    <option value="brown">Brown</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Transparency</label>
                  <select
                    v-model="urinalysis.physical.transparency"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select Transparency</option>
                    <option value="clear">Clear</option>
                    <option value="slightly cloudy">Slightly Cloudy</option>
                    <option value="cloudy">Cloudy</option>
                    <option value="turbid">Turbid</option>
                  </select>
                </div>
              </div>
            </div>
  
            <!-- Chemical Analysis -->
            <div class="mb-6">
              <h4 class="text-md font-medium text-gray-800 mb-3">Chemical Analysis</h4>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Glucose</label>
                  <select
                    v-model="urinalysis.chemical.glucose"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select</option>
                    <option value="negative">Negative</option>
                    <option value="trace">Trace</option>
                    <option value="1+">1+</option>
                    <option value="2+">2+</option>
                    <option value="3+">3+</option>
                    <option value="4+">4+</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Protein</label>
                  <select
                    v-model="urinalysis.chemical.protein"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select</option>
                    <option value="negative">Negative</option>
                    <option value="trace">Trace</option>
                    <option value="1+">1+</option>
                    <option value="2+">2+</option>
                    <option value="3+">3+</option>
                    <option value="4+">4+</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">pH</label>
                  <input
                    v-model="urinalysis.chemical.ph"
                    type="number"
                    step="0.1"
                    min="4.5"
                    max="8.5"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="5.0-8.0"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Specific Gravity</label>
                  <input
                    v-model="urinalysis.chemical.specificGravity"
                    type="number"
                    step="0.001"
                    min="1.000"
                    max="1.040"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="1.003-1.030"
                  />
                </div>
              </div>
            </div>
  
            <!-- Microscopic Analysis -->
            <div>
              <h4 class="text-md font-medium text-gray-800 mb-3">Microscopic Analysis</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">WBC (/hpf)</label>
                  <input
                    v-model="urinalysis.microscopic.wbc"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0-5"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">RBC (/hpf)</label>
                  <input
                    v-model="urinalysis.microscopic.rbc"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0-2"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Bacteria</label>
                  <select
                    v-model="urinalysis.microscopic.bacteria"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select</option>
                    <option value="few">Few</option>
                    <option value="moderate">Moderate</option>
                    <option value="many">Many</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Epithelial Cells</label>
                  <input
                    v-model="urinalysis.microscopic.epithelialCells"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0-5"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ urateOrPhosphateLabel }}
                  </label>
                  <select
                    v-model="urinalysis.microscopic.amorphousUrates"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select</option>
                    <option value="few">Few</option>
                    <option value="moderate">Moderate</option>
                    <option value="many">Many</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Mucus Threads</label>
                  <select
                    v-model="urinalysis.microscopic.mucusThreads"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Select</option>
                    <option value="few">Few</option>
                    <option value="moderate">Moderate</option>
                    <option value="many">Many</option>
                  </select>
                </div>
                <div class="md:col-span-3">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Casts</label>
                  <input
                    v-model="urinalysis.microscopic.casts"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Type and quantity of casts"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
  
        <!-- Chemistry -->
        <div v-if="activeTab === 'chemistry'" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <TestTube class="h-5 w-5 mr-2 text-purple-600" />
            Chemistry Results
          </h3>
          <div v-if="!selectedPatient" class="mb-4 text-sm text-amber-600">
            <AlertCircle class="h-4 w-4 inline mr-1" />
            Please select a patient first to proceed with tests.
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4" :class="{ 'opacity-50': !selectedPatient }">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Glucose (mg/dL)</label>
              <input
                v-model="chemistry.glucose"
                type="number"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="70-110"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Cholesterol (mg/dL)</label>
              <input
                v-model="chemistry.cholesterol"
                type="number"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="< 200"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Triglycerides (mg/dL)</label>
              <input
                v-model="chemistry.triglycerides"
                type="number"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="< 150"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Creatinine (mg/dL)</label>
              <input
                v-model="chemistry.creatinine"
                type="number"
                step="0.1"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="0.6-1.2"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">BUN (mg/dL)</label>
              <input
                v-model="chemistry.bun"
                type="number"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="7-20"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ALT (U/L)</label>
              <input
                v-model="chemistry.alt"
                type="number"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="7-56"
              />
            </div>
          </div>
        </div>
  
        <!-- Serology -->
        <div v-if="activeTab === 'serology'" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <Shield class="h-5 w-5 mr-2 text-red-600" />
            Serology Results
          </h3>
          <div v-if="!selectedPatient" class="mb-4 text-sm text-amber-600">
            <AlertCircle class="h-4 w-4 inline mr-1" />
            Please select a patient first to proceed with tests.
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4" :class="{ 'opacity-50': !selectedPatient }">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Hepatitis B Surface Antigen</label>
              <select
                v-model="serology.hbsAg"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
              >
                <option value="">Select Result</option>
                <option value="reactive">Reactive</option>
                <option value="non-reactive">Non-Reactive</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Anti-HCV</label>
              <select
                v-model="serology.antiHCV"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
              >
                <option value="">Select Result</option>
                <option value="reactive">Reactive</option>
                <option value="non-reactive">Non-Reactive</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">HIV 1/2</label>
              <select
                v-model="serology.hiv"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
              >
                <option value="">Select Result</option>
                <option value="reactive">Reactive</option>
                <option value="non-reactive">Non-Reactive</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">VDRL/RPR</label>
              <select
                v-model="serology.vdrl"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
              >
                <option value="">Select Result</option>
                <option value="reactive">Reactive</option>
                <option value="non-reactive">Non-Reactive</option>
              </select>
            </div>
          </div>
        </div>
  
        <!-- Hematology -->
        <div v-if="activeTab === 'hematology'" class="bg-white rounded-lg shadow p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <Heart class="h-5 w-5 mr-2 text-red-500" />
            Hematology Results
          </h3>
          <div v-if="!selectedPatient" class="mb-4 text-sm text-amber-600">
            <AlertCircle class="h-4 w-4 inline mr-1" />
            Please select a patient first to proceed with tests.
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4" :class="{ 'opacity-50': !selectedPatient }">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Hemoglobin (g/dL)</label>
              <input
                v-model="hematology.hemoglobin"
                type="number"
                step="0.1"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="12-16"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Hematocrit (%)</label>
              <input
                v-model="hematology.hematocrit"
                type="number"
                step="0.1"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="36-48"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">RBC Count (x10⁶/μL)</label>
              <input
                v-model="hematology.rbcCount"
                type="number"
                step="0.1"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="4.2-5.4"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">WBC Count (x10³/μL)</label>
              <input
                v-model="hematology.wbcCount"
                type="number"
                step="0.1"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="4.5-11.0"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Platelet Count (x10³/μL)</label>
              <input
                v-model="hematology.plateletCount"
                type="number"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="150-450"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ESR (mm/hr)</label>
              <input
                v-model="hematology.esr"
                type="number"
                :disabled="!selectedPatient"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100"
                placeholder="0-20"
              />
            </div>
          </div>
        </div>
  
        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4 mt-6">
          <button
            @click="clearForm"
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Clear Form
          </button>
          <button
            @click="saveResults"
            :disabled="!selectedPatient"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <Save class="h-4 w-4 inline mr-2" />
            Save Results
          </button>
          <button
            @click="generateReport"
            :disabled="!selectedPatient"
            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <FileText class="h-4 w-4 inline mr-2" />
            Generate Report
          </button>
        </div>
      </div>
  
      <!-- Success Modal -->
      <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
          <div class="flex items-center mb-4">
            <CheckCircle class="h-6 w-6 text-green-500 mr-2" />
            <h3 class="text-lg font-semibold">Success!</h3>
          </div>
          <p class="text-gray-600 mb-4">{{ successMessage }}</p>
          <button
            @click="showSuccessModal = false"
            class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            OK
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue'
  import {
    Activity,
    User,
    Microscope,
    Droplets,
    TestTube,
    Shield,
    Heart,
    Save,
    FileText,
    CheckCircle,
    AlertCircle,
    Search,
    X
  } from 'lucide-vue-next'
  
  // Reactive data
  const activeTab = ref('microscopy')
  const selectedMicroscopyTest = ref('')
  const showSuccessModal = ref(false)
  const successMessage = ref('')
  const patients = ref([])
  const selectedPatientId = ref('')
  const selectedPatient = ref(null)
  
  // Add these new reactive variables after the existing ones
  const patientSearchQuery = ref('')
  const showPatientSuggestions = ref(false)
  
  const currentDate = computed(() => {
    return new Date().toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  })
  
  const tabs = [
    { id: 'microscopy', name: 'Clinical Microscopy', icon: Microscope },
    { id: 'chemistry', name: 'Chemistry', icon: TestTube },
    { id: 'serology', name: 'Serology', icon: Shield },
    { id: 'hematology', name: 'Hematology', icon: Heart }
  ]
  
  // Test Data
  const fecalysis = ref({
    physical: {
      color: '',
      consistency: ''
    },
    microscopic: {
      wbc: '',
      rbc: ''
    },
    results: ''
  })
  
  const urinalysis = ref({
    physical: {
      color: '',
      transparency: ''
    },
    chemical: {
      glucose: '',
      protein: '',
      ph: '',
      specificGravity: ''
    },
    microscopic: {
      wbc: '',
      rbc: '',
      bacteria: '',
      epithelialCells: '',
      amorphousUrates: '',
      mucusThreads: '',
      casts: ''
    }
  })
  
  const chemistry = ref({
    glucose: '',
    cholesterol: '',
    triglycerides: '',
    creatinine: '',
    bun: '',
    alt: ''
  })
  
  const serology = ref({
    hbsAg: '',
    antiHCV: '',
    hiv: '',
    vdrl: ''
  })
  
  const hematology = ref({
    hemoglobin: '',
    hematocrit: '',
    rbcCount: '',
    wbcCount: '',
    plateletCount: '',
    esr: ''
  })
  
  // Add this computed property for filtered patients
  const filteredPatients = computed(() => {
    if (!patientSearchQuery.value) return patients.value
    
    const query = patientSearchQuery.value.toLowerCase()
    return patients.value.filter(patient => 
      patient.name.toLowerCase().includes(query) ||
      patient.id.toLowerCase().includes(query) ||
      patient.company?.toLowerCase().includes(query) ||
      patient.phone?.includes(query)
    )
  })
  
  const urateOrPhosphateLabel = computed(() => {
    const ph = parseFloat(urinalysis.value.chemical.ph)
    return ph > 7.0 ? 'Amorphous Phosphates' : 'Amorphous Urates'
  })
  
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
  
  const capitalizeFirst = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1)
  }
  
  const loadPatientInfo = () => {
    if (selectedPatientId.value) {
      selectedPatient.value = patients.value.find(p => p.id === selectedPatientId.value)
    } else {
      selectedPatient.value = null
    }
  }
  
  // Add these new methods
  const handlePatientSearch = () => {
    if (!patientSearchQuery.value) {
      selectedPatient.value = null
      selectedPatientId.value = ''
    }
  }
  
  const selectPatient = (patient) => {
    selectedPatient.value = patient
    selectedPatientId.value = patient.id
    patientSearchQuery.value = `${patient.name} (${patient.id})`
    showPatientSuggestions.value = false
  }
  
  const hidePatientSuggestions = () => {
    setTimeout(() => {
      showPatientSuggestions.value = false
    }, 200)
  }
  
  const clearPatientSelection = () => {
    selectedPatient.value = null
    selectedPatientId.value = ''
    patientSearchQuery.value = ''
    showPatientSuggestions.value = false
  }
  
  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  }
  
  const clearForm = () => {
    // Reset all test data
    fecalysis.value = {
      physical: { color: '', consistency: '' },
      microscopic: { wbc: '', rbc: '' },
      results: ''
    }
  
    urinalysis.value = {
      physical: { color: '', transparency: '' },
      chemical: { glucose: '', protein: '', ph: '', specificGravity: '' },
      microscopic: {
        wbc: '', rbc: '', bacteria: '', epithelialCells: '',
        amorphousUrates: '', mucusThreads: '', casts: ''
      }
    }
  
    chemistry.value = {
      glucose: '', cholesterol: '', triglycerides: '',
      creatinine: '', bun: '', alt: ''
    }
  
    serology.value = {
      hbsAg: '', antiHCV: '', hiv: '', vdrl: ''
    }
  
    hematology.value = {
      hemoglobin: '', hematocrit: '', rbcCount: '',
      wbcCount: '', plateletCount: '', esr: ''
    }
  
    selectedMicroscopyTest.value = ''
  
    successMessage.value = 'Test forms cleared successfully!'
    showSuccessModal.value = true
  }
  
  const saveResults = () => {
    if (!selectedPatient.value) {
      alert('Please select a patient first!')
      return
    }
  
    // Here you would typically save to a database
    console.log('Saving results for patient:', selectedPatient.value.name, {
      patient: selectedPatient.value,
      fecalysis: fecalysis.value,
      urinalysis: urinalysis.value,
      chemistry: chemistry.value,
      serology: serology.value,
      hematology: hematology.value
    })
  
    successMessage.value = `Results saved successfully for ${selectedPatient.value.name}!`
    showSuccessModal.value = true
  }
  
  const generateReport = () => {
    if (!selectedPatient.value) {
      alert('Please select a patient first!')
      return
    }
  
    // Here you would typically generate a PDF report
    console.log('Generating report for patient:', selectedPatient.value.name)
    
    successMessage.value = `Report generated successfully for ${selectedPatient.value.name}!`
    showSuccessModal.value = true
  }
  
  // Load patients from localStorage on mount
  onMounted(() => {
    const savedPatients = localStorage.getItem('lims_patients')
    if (savedPatients) {
      patients.value = JSON.parse(savedPatients)
    }
  })
  </script>
  
  <style scoped>
  /* Custom styles for better medical interface appearance */
  .prose {
    max-width: none;
  }
  
  /* Focus styles for better accessibility */
  input:focus,
  select:focus,
  textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }
  
  /* Custom scrollbar for better UX */
  ::-webkit-scrollbar {
    width: 6px;
  }
  
  ::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  ::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
  }
  
  ::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
  }
  </style>