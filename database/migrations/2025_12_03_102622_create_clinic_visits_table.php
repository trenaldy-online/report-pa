<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('clinic_visits', function (Blueprint $table) {
        $table->id(); // Ini pengganti BIGINT AUTO_INCREMENT PRIMARY KEY
        
        $table->string('no_rm', 50)->nullable()->index(); // Tambah index biar pencarian cepat
        $table->date('tanggal_kunjungan')->nullable();
        $table->string('nama_pasien', 255)->nullable();
        $table->string('klinik', 100)->nullable();
        $table->string('dpjp', 100)->nullable();
        
        // TINYINT(1) di Laravel biasanya pakai boolean
        $table->boolean('new_patient')->default(false); 
        
        $table->text('catatan')->nullable();
        $table->string('ttl', 255)->nullable();
        
        // ENUM L/P
        $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
        
        $table->text('alamat')->nullable();
        $table->string('kota_area', 100)->nullable();
        $table->text('alamat_domisili')->nullable();
        $table->string('telepon', 30)->nullable();
        
        $table->text('diagnosis')->nullable();
        $table->string('cancer_category', 100)->nullable();
        $table->string('stadium', 50)->nullable();
        $table->string('program', 100)->nullable();
        $table->string('dosis_fraksi', 100)->nullable();
        $table->string('teknik_rt', 100)->nullable();
        $table->string('surgery_type', 200)->nullable();
        $table->string('chemo_status', 200)->nullable();
        $table->string('surat_rujukan', 100)->nullable();
        $table->string('sumber_informasi', 255)->nullable();
        $table->string('hospital', 255)->nullable();
        $table->string('hospital2', 255)->nullable();
        $table->string('updating_nurse', 100)->nullable();
        $table->string('checker', 100)->nullable();
        $table->integer('usia')->nullable();

        $table->timestamps(); // Wajib untuk Laravel (created_at, updated_at)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_visits');
    }
};
