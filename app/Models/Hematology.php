<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hematology extends Model
{
    protected $table = 'hematology';

    protected $fillable = [
        'patient_id',
        'cbc_wbc',
        'cbc_neu',
        'cbc_lym',
        'cbc_mon',
        'cbc_eos',
        'cbc_bas',
        'cbc_rbc',
        'cbc_hgb',
        'cbc_hct',
        'cbc_mcv',
        'cbc_mch',
        'cbc_mchc',
        'cbc_plt',
        'cbc_remarks',
        'bt_abo_group',
        'bt_rh',
        'medical_technologist',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
