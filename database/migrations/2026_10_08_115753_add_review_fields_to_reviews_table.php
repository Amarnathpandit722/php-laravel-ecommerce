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
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('product_id')->after('id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_id')->after('product_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->after('user_id');
            $table->string('title', 120)->nullable()->after('rating');
            $table->text('body')->after('title');
            $table->json('images')->nullable()->after('body');
            $table->string('status')->default('pending')->after('images');
            $table->boolean('is_verified_purchase')->default(false)->after('status');
            $table->unsignedInteger('helpful_count')->default(0)->after('is_verified_purchase');
            $table->index('status');
            $table->index(['product_id', 'user_id']);
        });
    }


    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['product_id', 'user_id', 'rating', 'title', 'body', 'images', 'status', 'is_verified_purchase', 'helpful_count',]);
        });
    }
};
