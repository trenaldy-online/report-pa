<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadioterapiConverted extends Model
{
    protected $table = 'radioterapi_converted';
    protected $guarded = []; // Izinkan semua kolom diisi
    
    // Pastikan date_converted dibaca sebagai tanggal
    protected $casts = [
        'date_converted' => 'date',
    ];
}