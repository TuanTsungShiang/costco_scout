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
        Schema::create('resale_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('canonical_product_id');
            $table->unsignedBigInteger('purchase_price_observation_id');
            $table->unsignedBigInteger('sales_channel_id');
            $table->unsignedBigInteger('expected_sale_amount_minor');
            // All monetary values as integer minor units
            $table->bigInteger('estimated_platform_fee_minor')->default(0);
            $table->bigInteger('estimated_payment_fee_minor')->default(0);
            $table->bigInteger('estimated_promotion_fee_minor')->default(0);
            $table->bigInteger('estimated_shipping_minor')->default(0);
            $table->bigInteger('estimated_packaging_minor')->default(0);
            $table->bigInteger('estimated_return_loss_minor')->default(0);
            $table->bigInteger('estimated_other_cost_minor')->default(0);
            $table->bigInteger('estimated_membership_reward_minor')->default(0);
            $table->bigInteger('estimated_net_profit_minor');
            // ROI and margin as basis points (500 = 5%)
            $table->integer('roi_basis_points');
            $table->integer('profit_margin_basis_points');
            $table->unsignedBigInteger('break_even_amount_minor');
            $table->string('market_data_status')->default('UNVERIFIED');
            // UNVERIFIED|LISTING_PRICE_ONLY|MANUAL_MARKET_CHECK|OWN_SALES_HISTORY
            $table->string('decision');
            // PASS|WATCH|TEST_ONE_UNIT|RESTOCK|SCALE
            $table->timestamp('analyzed_at');
            $table->timestamps();

            $table->index('canonical_product_id');
            $table->index('decision');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resale_analyses');
    }
};
