<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Serology;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SerologyController extends Controller
{
    public function index($patientId = null)
    {
        if ($patientId) {
            // Fetch clinical tests for a specific patient
            $serologyTests = Serology::where('patient_id', $patientId)->get();
        } else {
            // Fetch all clinical tests
            $serologyTests = Serology::all();
        }

        // Fetch all patients to populate the search dropdown
        $patients = Patient::all(); // Fetch all patients

        // Return Inertia response with clinical test data and patients
        return Inertia::render('serology', [
            'serologyTests' => $serologyTests,
            'patients' => $patients, // Pass the patients data
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }


    public function storeSerology(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient,id',

            // Syphilis
            'ss_kit' => 'nullable|string',
            'ss_lot_no' => 'nullable|string',
            'ss_expiration_date' => 'nullable|date',
            'ss_result' => 'nullable|array',
            'ss_remarks' => 'nullable|string',

            // Dengue
            'dd_kit' => 'nullable|string',
            'dd_lot_no' => 'nullable|string',
            'dd_expiration_date' => 'nullable|date',
            'dd_result' => 'nullable|array',
            'dd_result.*.type' => 'required|string',
            'dd_result.*.details' => 'nullable|array',

            'dd_remarks' => 'nullable|string',

            // HBSAG
            'hbsag_kit' => 'nullable|string',
            'hbsag_lot_no' => 'nullable|string',
            'hbsag_expiration_date' => 'nullable|date',
            'hbsag_result' => 'nullable|array',
            'hbsag_remarks' => 'nullable|string',

            // HIV
            'hiv_kit' => 'nullable|string',
            'hiv_lot_no' => 'nullable|string',
            'hiv_expiration_date' => 'nullable|date',
            'hiv_result' => 'nullable|array',
            'hiv_result.*.type' => 'required|string',
            'hiv_result.*.details' => 'nullable|array',
            'hiv_remarks' => 'nullable|string',

            'medical_technologist' => 'nullable|string',
        ]);

        $serology = Serology::create($validated);

        return redirect()
            ->route('serology.index', $serology->patient_id)
            ->with('success', 'Serology test saved successfully!');
    }


    public function updateSerology(Request $request, $id)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patient,id',

            // Syphilis
            'ss_kit' => 'nullable|string',
            'ss_lot_no' => 'nullable|string',
            'ss_expiration_date' => 'nullable|date',
            'ss_result' => 'nullable|array',
            'ss_remarks' => 'nullable|string',

            // Dengue
            'dd_kit' => 'nullable|string',
            'dd_lot_no' => 'nullable|string',
            'dd_expiration_date' => 'nullable|date',
            'dd_result' => 'nullable|array',
            'dd_result.*.type' => 'required|string',
            'dd_result.*.details' => 'nullable|array',
            'dd_remarks' => 'nullable|string',

            // HBSAG
            'hbsag_kit' => 'nullable|string',
            'hbsag_lot_no' => 'nullable|string',
            'hbsag_expiration_date' => 'nullable|date',
            'hbsag_result' => 'nullable|array',
            'hbsag_remarks' => 'nullable|string',

            // HIV
            'hiv_kit' => 'nullable|string',
            'hiv_lot_no' => 'nullable|string',
            'hiv_expiration_date' => 'nullable|date',
            'hiv_result' => 'nullable|array',
            'hiv_result.*.type' => 'required|string',
            'hiv_result.*.details' => 'nullable|array',
            'hiv_remarks' => 'nullable|string',

            'medical_technologist' => 'nullable|string',
        ]);

        try {
            $serology = Serology::findOrFail($id);
            $serology->update($validated);

            return redirect()
                ->route('serology.index', $serology->patient_id)
                ->with('success', 'Serology test updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update serology test: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $serology = Serology::findOrFail($id);

        $serology->delete();

        session()->flash('success', 'Serology test deleted successfully!');

        // Redirect to patient list or show page
        return redirect()->back();

    }



}
