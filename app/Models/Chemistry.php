<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemistry extends Model
{
    use HasFactory;

    protected $table = 'chemistry';

    protected $fillable = [
        'patient_id',
        'rbs',
        'fasting',
        'remarks',
        'medical_technologist',
    ];

    /**
     * Relationship: Chemistry belongs to a patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
