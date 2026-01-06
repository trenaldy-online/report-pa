<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicVisitImport extends Model
{
    use HasFactory;
    
    protected $table = 'clinic_visit_imports';
    protected $guarded = []; // Izinkan semua masuk
    
    // Casting agar format tanggal aman
    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'ttl' => 'date',
    ];
}