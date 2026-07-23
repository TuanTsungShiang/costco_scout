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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('retailer_id');
            $table->string('branch_name');
            $table->string('country_code', 2)->default('TW');
            $table->string('currency_code', 3)->default('TWD');
            $table->string('timezone')->default('Asia/Taipei');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('retailer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
