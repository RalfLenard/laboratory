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
        Schema::create('serology', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient')->onDelete('cascade');
        
            // Syphilis Screening
            $table->string('ss_kit')->nullable();
            $table->string('ss_lot_no')->nullable();
            $table->date('ss_expiration_date')->nullable();
            $table->json('ss_result')->nullable();       // Store 1 or more results
            $table->string('ss_remarks')->nullable();    // Remarks
        
            // Dengue Duo
            $table->string('dd_kit')->nullable();
            $table->string('dd_lot_no')->nullable();
            $table->date('dd_expiration_date')->nullable();
            $table->json('dd_result')->nullable();       // Up to 3 results
            $table->string('dd_remarks')->nullable();    // Remarks
        
            // HBSAG
            $table->string('hbsag_kit')->nullable();
            $table->string('hbsag_lot_no')->nullable();
            $table->date('hbsag_expiration_date')->nullable();
            $table->json('hbsag_result')->nullable();    // 1 result only
            $table->string('hbsag_remarks')->nullable(); // Remarks
        
            // HIV
            $table->string('hiv_kit')->nullable();
            $table->string('hiv_lot_no')->nullable();
            $table->date('hiv_expiration_date')->nullable();
            $table->json('hiv_result')->nullable();      // 2 results
            $table->string('hiv_remarks')->nullable();   // Remarks
        
            $table->string('medical_technologist')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serology');
    }
};
