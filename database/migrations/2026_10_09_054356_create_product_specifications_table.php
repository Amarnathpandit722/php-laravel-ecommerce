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
        Schema::create('product_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // e.g. "Processor", "Display", "Memory"
            $table->string('section');
            // e.g. "Brand", "Model Number"
            $table->string('label');
            // e.g. "Intel Core i7-1355U"
            $table->text('value')->nullable();
            // For sorting rows inside each section
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->index(['product_id', 'section', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_specifications');
    }
};
