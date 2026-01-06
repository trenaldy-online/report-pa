<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicVisit extends Model
{
    use HasFactory;

    // Arahkan ke tabel baru
    protected $table = 'clinic_visits';

    // Izinkan semua kolom diisi
    protected $guarded = [];

    // Ubah kolom new_patient (0/1) jadi boolean (true/false) otomatis agar mudah diolah
    protected $casts = [
        'new_patient' => 'boolean',
        'tanggal_kunjungan' => 'date',
    ];

    // Relasi
    public function radioConverted()
    {
        return $this->hasOne(RadioterapiConverted::class, 'no_rm', 'no_rm');
    }

    public function kemoConverted()
    {
        return $this->hasOne(KemoterapiConverted::class, 'no_rm', 'no_rm');
    }

    // Magic Attribute
    public function getIsConvertedAttribute()
    {
        if ($this->radioConverted()->exists()) return true;
        if ($this->kemoConverted()->exists()) return true;
        return false;
    }
}