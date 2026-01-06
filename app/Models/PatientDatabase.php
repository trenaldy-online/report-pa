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

    // --- RELASI KE TABEL BARU ---
    public function radioConverted()
    {
        // Hubungkan No RM pasien ini dengan No RM di tabel Radioterapi
        return $this->hasOne(RadioterapiConverted::class, 'no_rm', 'no_rm');
    }

    public function kemoConverted()
    {
        // Hubungkan No RM pasien ini dengan No RM di tabel Kemoterapi
        return $this->hasOne(KemoterapiConverted::class, 'no_rm', 'no_rm');
    }

    // --- ATRIBUT OTOMATIS (MAGIC) ---
    // Cara pakai di view: $pasien->is_converted
    public function getIsConvertedAttribute()
    {
        // LOGIC: 
        // Pasien dianggap CONVERTED jika No RM-nya ADA di tabel Radio ATAU tabel Kemo.
        
        if ($this->radioConverted()->exists()) {
            return true;
        }

        if ($this->kemoConverted()->exists()) {
            return true;
        }

        // Opsional: Cek juga data lama manual (jika ada kolom reason='Converted')
        // if ($this->reason == 'Converted') return true;

        return false; // Jika tidak ada dimana-mana, berarti Non-Converted
    }
}