<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDatabaseImport extends Model
{
    use HasFactory;
    
    protected $table = 'patient_database_imports';
    protected $guarded = [];
    
    protected $casts = [
        'ttl' => 'date',
    ];
}