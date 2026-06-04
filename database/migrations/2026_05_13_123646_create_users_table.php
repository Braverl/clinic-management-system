<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->date('dob')->nullable();
                $table->string('gender')->nullable();
                $table->string('role')->default('patient');
                $table->string('profile_image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            // Add any missing columns to existing users table
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'address')) {
                    $table->text('address')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('users', 'dob')) {
                    $table->date('dob')->nullable()->after('address');
                }
                if (!Schema::hasColumn('users', 'gender')) {
                    $table->string('gender')->nullable()->after('dob');
                }
                if (!Schema::hasColumn('users', 'role')) {
                    $table->string('role')->default('patient')->after('gender');
                }
                if (!Schema::hasColumn('users', 'profile_image')) {
                    $table->string('profile_image')->nullable()->after('role');
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('profile_image');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};