<?php

namespace App\Http\Controllers;

use App\Models\Clinical;
use App\Models\Patient; // Import the Patient model
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClinicalController extends Controller
{
    public function index($patientId = null)
    {
        if ($patientId) {
            // Fetch clinical tests for a specific patient
            $clinicalTests = Clinical::where('patient_id', $patientId)->get();
        } else {
            // Fetch all clinical tests
            $clinicalTests = Clinical::all();
        }

        // Fetch all patients to populate the search dropdown
        $patients = Patient::all(); // Fetch all patients

        // Return Inertia response with clinical test data and patients
        return Inertia::render('clinical', [
            'clinicalTests' => $clinicalTests,
            'patients' => $patients, // Pass the patients data
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    // Add function to store a new clinical microscopic test
    public function addClinicalMicroscopicTest(Request $request)
    {
        $validated = $request->validate([
            // Fecalysis
            'fecalysis_color' => 'nullable|string',
            'fecalysis_consistency' => 'nullable|string',
            'fecalysis_wbc' => 'nullable|string',
            'fecalysis_rbc' => 'nullable|string',
            'fecalysis_remarks' => 'nullable|string',
            'fecalysis_results' => 'nullable|string',

            // Urinalysis - Physical and Chemical
            'ph' => 'nullable|string',
            'urinalysis_glucose' => 'nullable|string',
            'urinalysis_protein' => 'nullable|string',
            'urinalysis_spgravity' => 'nullable|string',
            'urinalysis_color' => 'nullable|string',
            'urinalysis_transparency' => 'nullable|string',

            // Urinalysis - Microscopic
            'urinalysis_wbc' => 'nullable|string',
            'urinalysis_rbc' => 'nullable|string',
            'urinalysis_bacteria' => 'nullable|string',
            'urinalysis_epithelial_cells' => 'nullable|string',
            'urinalysis_amorphous' => 'nullable|string',
            'urinalysis_phospates' => 'nullable|string',
            'urinalysis_mucus_threads' => 'nullable|string',
            'urinalysis_casts' => 'nullable|array',
            'urinalysis_casts.*.type' => 'required|string',
            'urinalysis_casts.*.details' => 'nullable|array',

            'urinalysis_remarks' => 'nullable|string',

            'urinalysis_crystals' => 'nullable|array',
            'urinalysis_crystals.*.type' => 'required|string',
            'urinalysis_crystals.*.details' => 'nullable|array',

            'urinalysis_fungal_elements' => 'nullable|array',
            'urinalysis_fungal_elements.*.type' => 'required|string',
            'urinalysis_fungal_elements.*.details' => 'nullable|array',

            'urinalysis_parasite' => 'nullable|array',
            'urinalysis_parasite.*.type' => 'required|string',
            'urinalysis_parasite.*.details' => 'nullable|array',

            // Other required fields
            'medical_technologist' => 'required|string',
            'patient_id' => 'required|exists:patient,id',
        ]);

        // No need to modify $validated['urinalysis_casts'] here
        // It should already be an array of casts from your frontend

        $clinicalTest = Clinical::create($validated);

        return redirect()
            ->route('clinical.index', $clinicalTest->patient_id)
            ->with('success', 'Clinical test added successfully!');
    }

    public function updateClinicalMicroscopicTest(Request $request, $id)
    {
        $validated = $request->validate([
            // Fecalysis
            'fecalysis_color' => 'nullable|string',
            'fecalysis_consistency' => 'nullable|string',
            'fecalysis_wbc' => 'nullable|string',
            'fecalysis_rbc' => 'nullable|string',
            'fecalysis_remarks' => 'nullable|string',
            'fecalysis_results' => 'nullable|string',

            // Urinalysis - Physical and Chemical
            'ph' => 'nullable|string',
            'urinalysis_glucose' => 'nullable|string',
            'urinalysis_protein' => 'nullable|string',
            'urinalysis_spgravity' => 'nullable|string',
            'urinalysis_color' => 'nullable|string',
            'urinalysis_transparency' => 'nullable|string',

            // Urinalysis - Microscopic
            'urinalysis_wbc' => 'nullable|string',
            'urinalysis_rbc' => 'nullable|string',
            'urinalysis_bacteria' => 'nullable|string',
            'urinalysis_epithelial_cells' => 'nullable|string',
            'urinalysis_amorphous' => 'nullable|string',
            'urinalysis_phospates' => 'nullable|string',
            'urinalysis_mucus_threads' => 'nullable|string',

            'urinalysis_casts' => 'nullable|array',
            'urinalysis_casts.*.type' => 'required|string',
            'urinalysis_casts.*.details' => 'nullable|array',

            'urinalysis_remarks' => 'nullable|string',

            'urinalysis_crystals' => 'nullable|array',
            'urinalysis_crystals.*.type' => 'required|string',
            'urinalysis_crystals.*.details' => 'nullable|array',

            'urinalysis_fungal_elements' => 'nullable|array',
            'urinalysis_fungal_elements.*.type' => 'required|string',
            'urinalysis_fungal_elements.*.details' => 'nullable|array',

            'urinalysis_parasite' => 'nullable|array',
            'urinalysis_parasite.*.type' => 'required|string',
            'urinalysis_parasite.*.details' => 'nullable|array',

            // Other required fields
            'medical_technologist' => 'required|string',
            'patient_id' => 'required|exists:patient,id',
        ]);

        try {
            $clinicalTest = Clinical::findOrFail($id);
            $clinicalTest->update($validated);

            return redirect()
                ->route('clinical.index', $clinicalTest->patient_id)
                ->with('success', 'Clinical test updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update clinical test: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $clinical = Clinical::findOrFail($id);

        $clinical->delete();

        session()->flash('success', 'Clinical test deleted successfully!');

        // Redirect to patient list or show page
        return redirect()->back();

    }



}