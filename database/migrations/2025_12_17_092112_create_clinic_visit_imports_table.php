<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clinic_visit_imports', function (Blueprint $table) {
            $table->id();

            // Masukkan semua kolom yang ada di Excel/Database Utama
            $table->unsignedBigInteger('uploaded_by')->nullable(); // PENTING

            $table->string('no_rm')->nullable();
            $table->date('tanggal_kunjungan')->nullable();
            $table->string('nama_pasien')->nullable();
            $table->string('klinik')->nullable();
            $table->string('dpjp')->nullable();
            $table->string('new_patient')->nullable(); // Bisa string/boolean
            $table->text('catatan')->nullable();
            $table->string('updating_nurse')->nullable();
            $table->string('program')->nullable();
            $table->string('surat_rujukan')->nullable();
            $table->string('sumber_informasi')->nullable();
            $table->string('ttl')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota_area')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('telepon')->nullable();
            $table->string('diagnosis')->nullable();
            $table->string('cancer_category')->nullable();
            $table->string('stadium')->nullable();
            $table->string('dosis_fraksi')->nullable();
            $table->string('teknik_rt')->nullable();
            $table->string('surgery_type')->nullable();
            $table->string('hospital')->nullable();
            $table->string('chemo_status')->nullable();
            $table->string('hospital2')->nullable();
            $table->string('checker')->nullable();
            $table->string('usia')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinic_visit_imports');
    }
};