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
        Schema::create('resale_experiments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resale_analysis_id');
            $table->unsignedSmallInteger('quantity_purchased')->default(1);
            $table->unsignedSmallInteger('quantity_listed')->default(0);
            $table->unsignedSmallInteger('quantity_sold')->default(0);
            $table->unsignedBigInteger('purchase_total_minor')->nullable();
            $table->unsignedBigInteger('actual_average_sale_amount_minor')->nullable();
            $table->bigInteger('actual_platform_fee_minor')->nullable();
            $table->bigInteger('actual_payment_fee_minor')->nullable();
            $table->bigInteger('actual_shipping_minor')->nullable();
            $table->bigInteger('actual_packaging_minor')->nullable();
            $table->bigInteger('actual_other_cost_minor')->nullable();
            $table->bigInteger('actual_net_profit_minor')->nullable();
            $table->timestamp('listed_at')->nullable();
            $table->timestamp('first_sold_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('PLANNED');
            // PLANNED|LISTED|PARTIALLY_SOLD|SOLD_OUT|CANCELLED|FAILED
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('resale_analysis_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resale_experiments');
    }
};
