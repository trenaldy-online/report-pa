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
    Schema::create('patient_databases', function (Blueprint $table) {
        $table->id(); // BIGINT AUTO_INCREMENT PRIMARY KEY

        $table->string('no_rm', 50)->nullable()->index();
        $table->string('name_of_patient', 255)->nullable();
        $table->string('diagnosis', 255)->nullable();
        $table->integer('age')->nullable();
        $table->string('overseas_hospital', 255)->nullable();

        // Radiation Oncology (RO)
        $table->string('source_information_ro', 255)->nullable();
        $table->string('new_ro_clinic', 50)->nullable();
        $table->string('new_rt', 50)->nullable();
        $table->string('reason', 255)->nullable();

        // Medical Oncology (MO)
        $table->string('source_information_mo', 255)->nullable();
        $table->string('new_mo_clinic', 50)->nullable();
        $table->string('new_chemo', 50)->nullable();
        $table->string('reason2', 255)->nullable();

        // Breast Oncology (BO)
        $table->string('source_information_bo', 255)->nullable();
        $table->string('new_bo_clinic', 50)->nullable();

        // Gyne Oncology (GO)
        $table->string('source_information_go', 255)->nullable();
        $table->string('new_go_clinic', 50)->nullable();

        // Pulmo Oncology (PO)
        $table->string('source_information_po', 255)->nullable();
        $table->string('new_po_clinic', 50)->nullable();

        // Pediatric Oncology (AO)
        $table->string('source_information_ao', 255)->nullable();
        $table->string('new_ao_clinic', 50)->nullable();

        // Notes
        $table->text('activities_notes')->nullable();
        $table->text('activities_notes2')->nullable();
        $table->text('activities_notes3')->nullable();
        $table->text('activities_notes4')->nullable();
        $table->text('activities_notes5')->nullable();

        $table->timestamps(); // created_at, updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_databases');
    }
};
