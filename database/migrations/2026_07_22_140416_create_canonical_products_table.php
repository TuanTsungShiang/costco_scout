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
        Schema::create('canonical_products', function (Blueprint $table) {
            $table->id();
            $table->string('brand')->nullable();
            $table->string('name');
            $table->string('gtin')->nullable()->unique();   // barcode
            $table->string('comparison_mode');             // WEIGHT|VOLUME|COUNT|SHEET|BUNDLE
            // Required for all modes except BUNDLE
            $table->unsignedSmallInteger('package_count')->nullable();
            $table->unsignedInteger('content_per_package')->nullable();
            $table->string('content_unit', 10)->nullable(); // G|ML|SHEET|COUNT
            $table->unsignedInteger('comparison_quantity')->nullable();
            $table->string('comparison_unit', 10)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canonical_products');
    }
};
