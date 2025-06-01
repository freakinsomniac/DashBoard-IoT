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
            $table->double('value_temp')->nullable()->after('device_id');
            $table->double('value_ph')->nullable()->after('value_temp');
            $table->double('value_height')->nullable()->after('value_ph');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->dropColumn(['value_temp', 'value_ph', 'value_height']);
        });
    }
};
