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
    Schema::create('patient_database_imports', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('uploaded_by')->nullable();
        
        $table->string('no_rm')->nullable();
        $table->string('name_of_patient')->nullable();
        $table->text('diagnosis')->nullable();
        $table->integer('age')->nullable();
        $table->string('overseas_hospital')->nullable();
        
        // RO
        $table->string('source_information_ro')->nullable();
        $table->string('new_ro_clinic')->nullable();
        $table->string('new_rt')->nullable();
        $table->text('reason')->nullable();

        // MO
        $table->string('source_information_mo')->nullable();
        $table->string('new_mo_clinic')->nullable();
        $table->string('new_chemo')->nullable();
        $table->text('reason2')->nullable();

        // Lainnya
        $table->string('source_information_bo')->nullable();
        $table->string('new_bo_clinic')->nullable();
        $table->string('source_information_go')->nullable();
        $table->string('new_go_clinic')->nullable();
        $table->string('source_information_po')->nullable();
        $table->string('new_po_clinic')->nullable();
        $table->string('source_information_ao')->nullable();
        $table->string('new_ao_clinic')->nullable();

        // Notes
        $table->text('activities_notes')->nullable();
        $table->text('activities_notes2')->nullable();
        $table->text('activities_notes3')->nullable();
        $table->text('activities_notes4')->nullable();
        $table->text('activities_notes5')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_database_imports');
    }
};
