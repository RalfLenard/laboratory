<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serology extends Model
{
    protected $table = 'serology';

    protected $fillable = [
        'patient_id',
        'ss_kit', 'ss_lot_no', 'ss_expiration_date', 'ss_result', 'ss_remarks',
        'dd_kit', 'dd_lot_no', 'dd_expiration_date', 'dd_result', 'dd_remarks',
        'hbsag_kit', 'hbsag_lot_no', 'hbsag_expiration_date', 'hbsag_result', 'hbsag_remarks',
        'hiv_kit', 'hiv_lot_no', 'hiv_expiration_date', 'hiv_result', 'hiv_remarks',
        'medical_technologist',
    ];

    protected $casts = [
        'ss_result' => 'array',
        'dd_result' => 'array',
        'hbsag_result' => 'array',
        'hiv_result' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
