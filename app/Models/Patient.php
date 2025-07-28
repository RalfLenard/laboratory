<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patient';
    protected $fillable = [
        'name',
        'gender',
        'address',
        'date_of_birth',
        'company',
        
    ];

    // app/Models/Patient.php

    public function clinical()
    {
        return $this->hasMany(Clinical::class);
    }

    public function hematology()
    {
        return $this->hasMany(Hematology::class);
    }

    public function serology()
    {
        return $this->hasMany(Serology::class);
    }

    public function chemistry()
    {
        return $this->hasMany(Chemistry::class);
    }
}
