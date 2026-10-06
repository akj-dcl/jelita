<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('kelurahan')->nullable()->after('city');
            $table->string('destination_id')->nullable()->after('kelurahan');
            $table->string('destination_label')->nullable()->after('destination_id');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['kelurahan', 'destination_id', 'destination_label']);
        });
    }
};
