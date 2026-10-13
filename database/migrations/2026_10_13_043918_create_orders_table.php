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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot of address (so history is preserved even if user deletes it)
            $table->json('shipping_address')->nullable();

            // Amounts (in paise — Razorpay uses paise)
            $table->unsignedBigInteger('subtotal');       // paise
            $table->unsignedBigInteger('shipping')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('total');

            $table->string('currency', 3)->default('INR');

            // Status
            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('pending');

            // Payment info
            $table->string('payment_method')->nullable();       // razorpay, cod, etc.
            $table->string('payment_status')->default('pending'); // pending, success, failed
            $table->string('razorpay_order_id')->nullable()->index();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->json('payment_meta')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
