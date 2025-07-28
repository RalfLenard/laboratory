<?php

namespace App\Http\Controllers;

use App\Models\Hematology;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HematologyController extends Controller
{
    public function index($patientId = null)
    {
        if ($patientId) {
            // Fetch clinical tests for a specific patient
            $hematologyTests = Hematology::where('patient_id', $patientId)->get();
        } else {
            // Fetch all clinical tests
            $hematologyTests = Hematology::all();
        }

        // Fetch all patients to populate the search dropdown
        $patients = Patient::all(); // Fetch all patients

        // Return Inertia response with clinical test data and patients
        return Inertia::render('hematology', [
            'hematologyTests' => $hematologyTests,
            'patients' => $patients, // Pass the patients data
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    public function storeHematology(Request $request)
    {
        $validated = $request->validate([
            // Fecalysis
            'cbc_wbc' => 'nullable|string',
            'cbc_neu' => 'nullable|string',
            'cbc_lym' => 'nullable|string',
            'cbc_mon' => 'nullable|string',
            'cbc_eos' => 'nullable|string',
            'cbc_bas' => 'nullable|string',
            'cbc_rbc' => 'nullable|string',
            'cbc_hgb' => 'nullable|string',
            'cbc_hct' => 'nullable|string',
            'cbc_mcv' => 'nullable|string',
            'cbc_mch' => 'nullable|string',
            'cbc_mchc' => 'nullable|string',
            'cbc_plt' => 'nullable|string',
            'cbc_remarks' => 'nullable|string',

            'bt_abo_group' => 'nullable|string',
            'bt_rh' => 'nullable|string',

            // Other required fields
            'medical_technologist' => 'required|string',
            'patient_id' => 'required|exists:patient,id',
        ]);
    
        $hematologyTest = Hematology::create($validated);
    
        return redirect()
            ->route('hematology.index', $hematologyTest->patient_id)
            ->with('success', 'Hematology test added successfully!');
    }


    public function updateHematology(Request $request, $id)
    {
        $validated = $request->validate([
            'cbc_wbc' => 'nullable|string',
            'cbc_neu' => 'nullable|string',
            'cbc_lym' => 'nullable|string',
            'cbc_mon' => 'nullable|string',
            'cbc_eos' => 'nullable|string',
            'cbc_bas' => 'nullable|string',
            'cbc_rbc' => 'nullable|string',
            'cbc_hgb' => 'nullable|string',
            'cbc_hct' => 'nullable|string',
            'cbc_mcv' => 'nullable|string',
            'cbc_mch' => 'nullable|string',
            'cbc_mchc' => 'nullable|string',
            'cbc_plt' => 'nullable|string',
            'cbc_remarks' => 'nullable|string',

            'bt_abo_group' => 'nullable|string',
            'bt_rh' => 'nullable|string',

            'medical_technologist' => 'required|string',
            'patient_id' => 'required|exists:patient,id',
        ]);

        try {
            $hematologyTest = Hematology::findOrFail($id);
            $hematologyTest->update($validated);

            return redirect()
                ->route('hematology.index', $hematologyTest->patient_id)
                ->with('success', 'Hematology test updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update hematology test: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $hematology = Hematology::findOrFail($id);

        $hematology->delete();

        session()->flash('success', 'Hematology test deleted successfully!');

        // Redirect to patient list or show page
        return redirect()->back();

    }



}
