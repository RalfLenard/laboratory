<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('serology', function (Blueprint $table) {
            // Add the column first
            $table->unsignedBigInteger('kit_id')->nullable()->after('patient_id');

            // Then add the foreign key constraint
            $table->foreign('kit_id')
                  ->references('id')
                  ->on('kits')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('serology', function (Blueprint $table) {
            $table->dropForeign(['kit_id']);
            $table->dropColumn('kit_id');
        });
    }
};
