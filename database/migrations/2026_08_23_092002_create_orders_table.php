<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status', ['pending_payment', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded']);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('shipping_method')->nullable();
            $table->text('notes')->nullable();
            $table->string('ship_name');
            $table->string('ship_phone');
            $table->string('ship_address');
            $table->string('ship_city');
            $table->string('ship_province');
            $table->string('ship_postal_code');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
