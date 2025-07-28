<?php

namespace App\Http\Controllers;

use App\Models\Chemistry;
use App\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChemistryController extends Controller
{
    public function index($patientId = null)
    {
        if ($patientId) {
            // Fetch clinical tests for a specific patient
            $chemistryTests = Chemistry::where('patient_id', $patientId)->get();
        } else {
            // Fetch all clinical tests
            $chemistryTests = Chemistry::all();
        }

        // Fetch all patients to populate the search dropdown
        $patients = Patient::all(); // Fetch all patients

        // Return Inertia response with clinical test data and patients
        return Inertia::render('chemistry', [
            'chemistryTests' => $chemistryTests,
            'patients' => $patients, // Pass the patients data
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }

    public function storeChemistry(Request $request)
    {
        $validated = $request->validate([
            'rbs' => 'nullable|string',
            'fasting' => 'nullable|string',
            'remarks' => 'nullable|string',

            'medical_technologist' => 'required|string',
            'patient_id' => 'required|exists:patient,id',

        ]);

        $chemistryTest = Chemistry::create($validated);

        return redirect()
        ->route('chemistry.index', $chemistryTest->patient_id)
        ->with('success', 'Chemistry test added successfully!');
    }

    public function updateChemistry(Request $request, $id)
    {
        $validated = $request->validate([
            'rbs' => 'nullable|string',
            'fasting' => 'nullable|string',
            'remarks' => 'nullable|string',
            'medical_technologist' => 'required|string',
            'patient_id' => 'required|exists:patient,id',
        ]);

        try {
            $chemistryTest = Chemistry::findOrFail($id);
            $chemistryTest->update($validated);

            return redirect()
                ->route('chemistry.index', $chemistryTest->patient_id)
                ->with('success', 'Chemistry test updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update chemistry test: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $chemistry = Chemistry::findOrFail($id);

        $chemistry->delete();

        session()->flash('success', 'Chemistry test deleted successfully!');

        // Redirect to patient list or show page
        return redirect()->back();

    }

}
