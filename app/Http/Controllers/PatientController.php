<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{

    // public function index()
    // {
    //     // Fetch all patients with their related results if they exist
    //     $patients = Patient::with(['clinical', 'hematology', 'serology', 'chemistry'])
    //     ->orderBy('created_at', 'desc')
    //     ->get();


    // foreach ($patients as $patient) {
    //     logger()->info('Patient ' . $patient->id, [
    //         'clinical_count' => $patient->clinical->count(),
    //         'hematology_count' => $patient->hematology->count(),
    //         'serology_count' => $patient->serology->count(),
    //         'chemistry_count' => $patient->chemistry->count(),
    //     ]);
    // }


    //     return Inertia::render('patient', [
    //         'patients' => $patients->map(function ($patient) {
    //             return [
    //                 'id' => $patient->id,
    //                 'name' => $patient->name,
    //                 'date_of_birth' => $patient->date_of_birth,
    //                 'address' => $patient->address,
    //                 'company' => $patient->company,
                   

    //                 'clinical' => $patient->clinical, // should be a collection/array
    //                 'hematology' => $patient->hematology,
    //                 'serology' => $patient->serology,
    //                 'chemistry' => $patient->chemistry,
    //             ];
    //         }),
    //         'flash' => [
    //             'success' => session('success'),
    //             'error' => session('error')
    //         ]
    //     ]);
        
    // }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $patientsQuery = Patient::with(['clinical', 'hematology', 'serology', 'chemistry'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $patientsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $patients = $patientsQuery->paginate(50)->withQueryString(); // 👈 keeps query string on pagination

        return Inertia::render('patient', [
            'patients' => $patients->through(function ($patient) {
                return [
                    'id' => $patient->id,
                    'name' => $patient->name,
                    'date_of_birth' => $patient->date_of_birth,
                    'address' => $patient->address,
                    'company' => $patient->company,
                    'clinical' => $patient->clinical,
                    'hematology' => $patient->hematology,
                    'serology' => $patient->serology,
                    'chemistry' => $patient->chemistry,
                ];
            }),
            'filters' => [
                'search' => $search,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ]
        ]);
    }

    public function addPatient(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female',
            'company' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);
    
        try {
            // Proceed with creating the patient using validated data
            $patient = Patient::create($validated);
    
            // Flash a success message to the session
            session()->flash('success', 'Patient created successfully!');
            
            // Redirect to the patient list page (or another page)
            return redirect()->route('patients.index'); // Example route
    
        } catch (\Exception $e) {
            // If something goes wrong, flash an error message
            session()->flash('error', 'Failed to create patient: ' . $e->getMessage());
    
            // Redirect back to the form
            return redirect()->back()->withInput(); // Optionally, retain the form inputs
        }
    }

    public function updatePatient(Request $request, $id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:Male,Female',
            'company' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        try {
            // Find the patient by ID
            $patient = Patient::findOrFail($id);

            // Update patient with validated data
            $patient->update($validated);

            // Flash success message
            session()->flash('success', 'Patient updated successfully!');

            // Redirect to patient list or show page
            return redirect()->route('patients.index');

        } catch (\Exception $e) {
            // Flash error message
            session()->flash('error', 'Failed to update patient: ' . $e->getMessage());

            // Redirect back with input
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        $patient->delete();

         // Flash success message
         session()->flash('success', 'Patient deleted successfully!');

         // Redirect to patient list or show page
         return redirect()->route('patients.index');

    }

    
    

}
