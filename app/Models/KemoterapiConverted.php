<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KemoterapiConverted extends Model
{
    protected $table = 'kemoterapi_converted';
    protected $guarded = [];
    
    protected $casts = [
        'date_converted' => 'date',
    ];
}