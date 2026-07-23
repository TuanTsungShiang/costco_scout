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
        Schema::create('product_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('canonical_product_id');
            $table->unsignedBigInteger('retailer_id');
            $table->string('external_product_id')->nullable();  // Costco item number / platform SKU
            $table->string('external_url')->nullable();
            $table->string('external_title')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('confirmed_by')->default('MANUAL'); // MANUAL | AUTO_GTIN
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['retailer_id', 'external_product_id']);
            $table->index('canonical_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_offers');
    }
};
