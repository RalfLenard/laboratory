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
        Schema::create('hematology', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patient')->onDelete('cascade');
            $table->string('cbc_wbc')->nullable();
            $table->string('cbc_neu')->nullable();
            $table->string('cbc_lym')->nullable();
            $table->string('cbc_mon')->nullable();
            $table->string('cbc_eos')->nullable();
            $table->string('cbc_bas')->nullable();

            $table->string('cbc_rbc')->nullable();
            $table->string('cbc_hgb')->nullable();
            $table->string('cbc_hct')->nullable();

            $table->string('cbc_mcv')->nullable();
            $table->string('cbc_mch')->nullable();
            $table->string('cbc_mchc')->nullable();
            $table->string('cbc_plt')->nullable();
            $table->string('cbc_remarks')->nullable();

            $table->string('bt_abo_group')->nullable();
            $table->string('bt_rh')->nullable();

            $table->string('medical_technologist')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hematology');
    }
};
