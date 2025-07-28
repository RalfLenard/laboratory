<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clinical', function (Blueprint $table) {
            $table->id();  // Primary Key
            $table->foreignId('patient_id')->constrained('patient')->onDelete('cascade');
            $table->string('fecalysis_color')->nullable();
            $table->string('fecalysis_consistency')->nullable();
            $table->string('fecalysis_wbc')->nullable();  // Fecalysis WBC
            $table->string('fecalysis_rbc')->nullable();  // Fecalysis RBC
            $table->string('fecalysis_remarks')->nullable();  // Fecalysis RBC
            $table->string('fecalysis_results')->nullable();  // Fecalysis RBC

            $table->string('urinalysis_color')->nullable();
            $table->string('urinalysis_transparency')->nullable();
            $table->string('ph')->nullable();
            $table->string('urinalysis_glucose')->nullable();
            $table->string('urinalysis_protein')->nullable();
            $table->string('urinalysis_spgravity')->nullable();
            $table->string('urinalysis_wbc')->nullable(); // Urinalysis WBC
            $table->string('urinalysis_rbc')->nullable(); // Urinalysis RBC
            $table->string('urinalysis_bacteria')->nullable(); // Urinalysis Bacteria
            $table->string('urinalysis_epithelial_cells')->nullable(); // Urinalysis Epithelial Cells
            $table->string('urinalysis_amorphous')->nullable(); 
            $table->string('urinalysis_phospates')->nullable();
            $table->string('urinalysis_mucus_threads')->nullable(); // Mucus Threads
            $table->json('urinalysis_casts')->nullable(); // Store cast type + details
            $table->string('urinalysis_remarks')->nullable(); 
            $table->json('urinalysis_crystals')->nullable();  
            $table->json('urinalysis_fungal_elements')->nullable();  
            $table->json('urinalysis_parasite')->nullable();

            $table->string('medical_technologist')->nullable();


            $table->timestamps();  // Created at and Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical');
    }
};
