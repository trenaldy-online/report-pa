<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDatabase extends Model
{
    use HasFactory;

    protected $table = 'patient_databases';
    protected $guarded = [];

    // --- TAMBAHKAN RELASI INI ---
    public function visits()
    {
        // Menghubungkan ke tabel clinic_visits berdasarkan no_rm
        return $this->hasMany(ClinicVisit::class, 'no_rm', 'no_rm');
    }
}