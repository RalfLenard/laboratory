<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinical extends Model
{
    protected $table = 'clinical';

    protected $casts = [
        'urinalysis_casts' => 'array',
        'urinalysis_crystals' => 'array',
        'urinalysis_fungal_elements' => 'array',
        'urinalysis_parasite' => 'array',
    ];
    

    // The attributes that are mass assignable
    protected $fillable = [
        'fecalysis_color',
        'fecalysis_consistency',
        'fecalysis_wbc',
        'fecalysis_rbc',
        'fecalysis_remarks',
        'fecalysis_results',


        'ph',
        'urinalysis_glucose',
        'urinalysis_protein',
        'urinalysis_spgravity',
        'urinalysis_transparency',
        'urinalysis_color',
        'urinalysis_wbc',
        'urinalysis_rbc',
        'urinalysis_bacteria',
        'urinalysis_epithelial_cells',
        'urinalysis_amorphous',
        'urinalysis_phospates',
        'urinalysis_mucus_threads',
        'urinalysis_casts',
        'urinalysis_crystals',
        'urinalysis_fungal_elements',
        'urinalysis_remarks',
        'urinalysis_parasite',
        'medical_technologist',
        'patient_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
