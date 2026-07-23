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
        Schema::create('sales_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('platform_fee_basis_points')->default(0);
            $table->unsignedInteger('payment_fee_basis_points')->default(0);
            $table->unsignedInteger('promotion_fee_basis_points')->default(0);
            $table->unsignedBigInteger('default_shipping_minor')->default(0);
            $table->unsignedBigInteger('default_packaging_minor')->default(0);
            $table->unsignedInteger('expected_return_loss_basis_points')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_channels');
    }
};
