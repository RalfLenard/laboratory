<?php

namespace App\Http\Controllers;

use App\Models\Chemistry;
use App\Models\Clinical;
use App\Models\Hematology;
use App\Models\Serology;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use App\Models\Patient;

class PrintController extends Controller
{

    // Urinalysis
    public function generateUrinalysisPdf($id)
    {
        $clinical = Clinical::findOrFail($id);
        $patient = $clinical->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('UrinePDF', compact('clinical', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="urinalysis_report.pdf"',
            ]
        );
    }

    // Fecalysis
    public function generateFecalysisPdf($id)
    {
        $clinical = Clinical::findOrFail($id);
        $patient = $clinical->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('FacalysisPDF', compact('clinical', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="fecalysis_report.pdf"',
            ]
        );
    }

    // cbc
    public function generateCbcPdf($id)
    {
        $hematology = Hematology::findOrFail($id);
        $patient = $hematology->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('CbcPDF', compact('hematology', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="CBC_report.pdf"',
            ]
        );
    }

    // blood type
    public function generateBloodTypePdf($id)
    {
        $hematology = Hematology::findOrFail($id);
        $patient = $hematology->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('BloodTypePDF', compact('hematology', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="blood-type_report.pdf"',
            ]
        );
    }


    // syphilis
    public function generateSyphilisPdf($id)
    {
        $serology = Serology::findOrFail($id);
        $patient = $serology->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('SyphilisPDF', compact('serology', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="syphilis_report.pdf"',
            ]
        );
    }

    // Hbsag
    public function generateHbsagPdf($id)
    {
        $serology = Serology::findOrFail($id);
        $patient = $serology->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('HbsagPDF', compact('serology', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="hbsag_report.pdf"',
            ]
        );
    }


    // dengue
    public function generateDenguePdf($id)
    {
        $serology = Serology::findOrFail($id);
        $patient = $serology->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('DenguePDF', compact('serology', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="dengue_report.pdf"',
            ]
        );
    }

    // hiv
    public function generateHivPdf($id)
    {
        $serology = Serology::findOrFail($id);
        $patient = $serology->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('HivPDF', compact('serology', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="hiv_report.pdf"',
            ]
        );
    }

    // rbs
    public function generateRbsPdf($id)
    {
        $chemistry = Chemistry::findOrFail($id);
        $patient = $chemistry->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('RbsPDF', compact('chemistry', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="rbs_report.pdf"',
            ]
        );
    }


    public function generateFbsPdf($id)
    {
        $chemistry = Chemistry::findOrFail($id);
        $patient = $chemistry->patient;

        // Compute age
        $patient->age = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth)->age : null;

        // Format date of birth
        $patient->formatted_birth_date = $patient->date_of_birth
            ? Carbon::parse($patient->date_of_birth)->translatedFormat('F j, Y')
            : 'N/A';

        $html = View::make('FbsPDF', compact('chemistry', 'patient'))->render();

        return response(
            Browsershot::html($html)
                ->margins(0, 10, 10, 10)
                ->showBackground()
                ->format('A4')
                ->pdf(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="fbs_report.pdf"',
            ]
        );
    }



}
