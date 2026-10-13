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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // $table->integer('quantity');
            // $table->decimal('price', 10, 2);

            // Snapshot of product at purchase time
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->unsignedBigInteger('unit_price');   // paise
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');   // paise

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
