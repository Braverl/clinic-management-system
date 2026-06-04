<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('specialization');
            $table->string('qualification');
            $table->integer('experience_years')->default(0);
            $table->decimal('consultation_fee', 10, 2);
            $table->string('available_days')->nullable();
            $table->time('available_from')->nullable();
            $table->time('available_to')->nullable();
            $table->text('bio')->nullable();
            $table->string('license_number')->unique();
            $table->timestamps();
        });
    }

    

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
