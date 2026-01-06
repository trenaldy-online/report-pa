<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Radioterapi Converted
        Schema::create('radioterapi_converted', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm')->index(); // Kita kasih index biar pencarian cepat
            $table->date('date_converted')->nullable();
            $table->string('nama_pasien')->nullable();
            $table->string('diagnosis')->nullable();
            $table->string('cancer_type')->nullable();
            $table->string('dpjp')->nullable();
            $table->string('rt_treatment')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Kemoterapi Converted
        Schema::create('kemoterapi_converted', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm')->index(); // Index lagi biar cepat
            $table->date('date_converted')->nullable();
            $table->string('nama_pasien')->nullable();
            $table->string('inpatient')->nullable(); // Rawat Inap/Jalan
            $table->string('new_kemo')->nullable();
            $table->string('status')->nullable();
            $table->string('telephone')->nullable();
            $table->string('diagnosis')->nullable();
            $table->string('cancer_type')->nullable();
            $table->string('dpjp')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('radioterapi_converted');
        Schema::dropIfExists('kemoterapi_converted');
    }
};