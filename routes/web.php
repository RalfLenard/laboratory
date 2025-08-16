<?php

use App\Http\Controllers\ChemistryController;
use App\Http\Controllers\ClinicalController;
use App\Http\Controllers\HematologyController;
use App\Http\Controllers\KitsController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\SerologyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('/laboratory', function () {
//     return Inertia::render('lab');
// });

// Route::get('/patient', function () {
//     return Inertia::render('patient');
// });

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// patient
Route::get('patient', [PatientController::class, 'index'])->name('patients.index');
Route::post('patients', [PatientController::class, 'addPatient']);
Route::put('patients/{id}', [PatientController::class, 'updatePatient'])->name('update.patient');
Route::delete('patients/{id}', [PatientController::class, 'destroy'])->name('destroy.patient');


// clinical
Route::get('/clinical/{patientId?}', [ClinicalController::class, 'index'])->name('clinical.index');
Route::post('/clinical', [ClinicalController::class, 'addClinicalMicroscopicTest'])->name('clinical.store');
Route::put('/clinical/{id}', [ClinicalController::class, 'updateClinicalMicroscopicTest'])->name('clinical.update');
Route::delete('/clinical/{id}', [ClinicalController::class, 'destroy'])->name('clinical.destroy');

// hematology
Route::get('/hematology/{patientId?}', [HematologyController::class, 'index'])->name('hematology.index');
Route::post('/hematology-test', [HematologyController::class, 'storeHematology'])->name('hematology.store');
Route::put('/hematology/{id}', [HematologyController::class, 'updateHematology'])->name('hematology.update');
Route::delete('/hematology/{id}', [HematologyController::class, 'destroy'])->name('hematology.destroy');

// serology
Route::get('/serology/{patientId?}', [SerologyController::class, 'index'])->name('serology.index');
Route::post('/serology-test', [SerologyController::class, 'storeSerology'])->name('serology.store');
Route::put('/serology/{id}', [SerologyController::class, 'updateSerology'])->name('serology.update');
Route::delete('/serology/{id}', [SerologyController::class, 'destroy'])->name('serology.destroy');

// chemistry
Route::get('/chemistry/{patientId?}', [ChemistryController::class, 'index'])->name('chemistry.index');
Route::post('/chemistry-test', [ChemistryController::class, 'storeChemistry'])->name('chemistry.store');
Route::put('/chemistry/{id}', [ChemistryController::class, 'updateChemistry'])->name('chemistry.update');
Route::delete('/chemistry/{id}', [ChemistryController::class, 'destroy'])->name('chemistry.destroy');

// Kits
Route::get('kits', [KitsController::class, 'index'])->name('kits.index');
Route::post('kits', [KitsController::class, 'store'])->name('kits.store');
Route::put('kits/{id}', [KitsController::class, 'update'])->name('kits.update');
Route::delete('kits/{id}', [KitsController::class, 'destroy'])->name('kits.delete');


// generate pdf
// clinical fecalysis and urinalysis
Route::get('/clinical/{id}/urinalysis-pdf', [PrintController::class, 'generateUrinalysisPdf']);
Route::get('/clinical/{id}/fecalysis-pdf', [PrintController::class, 'generateFecalysisPdf']);

// cbc and blood type
Route::get('/hematology/{id}/cbc-pdf', [PrintController::class, 'generateCbcPdf']);
Route::get('/hematology/{id}/blood-type-pdf', [PrintController::class, 'generateBloodTypePdf']);

// syphilis, dengue, hbsag and hiv
Route::get('/serology/{id}/syphilis-pdf', [PrintController::class, 'generateSyphilisPdf']);
Route::get('/serology/{id}/hbsag-pdf', [PrintController::class, 'generateHbsagPdf']);
Route::get('/serology/{id}/dengue-pdf', [PrintController::class, 'generateDenguePdf']);
Route::get('/serology/{id}/hiv-pdf', [PrintController::class, 'generateHivPdf']);

Route::get('/chemistry/{id}/rbs-pdf', [PrintController::class, 'generateRbsPdf']);
Route::get('/chemistry/{id}/fbs-pdf', [PrintController::class, 'generateFbsPdf']);



// Route::get('/clinical', function () {
//     return Inertia::render('clinical');
// });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
