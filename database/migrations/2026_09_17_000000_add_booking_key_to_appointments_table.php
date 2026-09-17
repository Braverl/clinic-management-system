<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('booking_key', 64)
                ->nullable()
                ->storedAs("CASE WHEN status = 'cancelled' THEN NULL ELSE CONCAT(doctor_id, '|', appointment_date, '|', TIME_FORMAT(appointment_time, '%H:%i')) END")
                ->after('status');

            $table->unique('booking_key');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique('appointments_booking_key_unique');
            $table->dropColumn('booking_key');
        });
    }
};