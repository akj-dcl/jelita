<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('profile_photo')->nullable();
            $table->enum('role', ['customer', 'admin'])->default('customer');
            $table->enum('status', ['active', 'disabled'])->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'date_of_birth', 'profile_photo', 'role', 'status']);
        });
    }
};
