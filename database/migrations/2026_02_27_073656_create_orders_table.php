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
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->string('paypal_order_id', 64)->unique();
            $table->string('capture_id', 64)->nullable();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('address', 255);
            $table->json('cart')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->enum('status', ['created', 'completed', 'canceled', 'failed'])->default('created');
            $table->enum('webhook', ['success', 'failure'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
