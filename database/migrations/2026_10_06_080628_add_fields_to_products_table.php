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
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->unique()->after('name')->nullable();
            $table->string('sku')->unique()->after('slug')->nullable();
            $table->string('image')->nullable()->after('description');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->string('status')->default('active')->after('stock'); // active, draft, archived
            $table->boolean('featured')->default(false)->after('status');
            $table->json('tags')->nullable()->after('featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'sku', 'image', 'discount_price', 'status', 'featured', 'tags']);
        });
    }
};
