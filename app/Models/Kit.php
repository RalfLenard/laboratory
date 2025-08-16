<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kit extends Model
{
    protected $table = 'kits';
    protected $fillable = [
        'kit_name',
        'kit_types',
        'kit_lot_no',
        'kit_expiration_date',
        
    ];

    public function serology()
    {
        return $this->hasMany(Serology::class);
    }
}
