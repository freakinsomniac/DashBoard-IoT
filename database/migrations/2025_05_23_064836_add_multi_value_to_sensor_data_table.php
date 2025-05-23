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
        Schema::table('sensor_data', function (Blueprint $table) {
            // Kolom baru sudah ada, jadi cukup hapus kolom lama
            $table->dropColumn(['sensor_id', 'sensor_type', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->string('sensor_id')->nullable();
            $table->string('sensor_type')->nullable();
            $table->float('value')->nullable();
            $table->json('multi_value')->nullable();
        });
    }
};
