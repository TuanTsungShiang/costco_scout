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
        // append-only: no updated_at
        Schema::create('price_observations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_offer_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('amount_minor');          // integer minor units, no float
            $table->string('currency_code', 3)->default('TWD');
            $table->boolean('tax_included')->default(true);
            $table->unsignedInteger('tax_rate_basis_points')->default(0);
            $table->decimal('fx_rate_to_base', 20, 10)->nullable(); // DECIMAL not float
            $table->string('fx_rate_source')->nullable();
            $table->timestamp('fx_captured_at')->nullable();
            $table->date('observed_at');
            $table->string('source_type')->default('MANUAL'); // PRICE_TAG_OCR|MANUAL|SCRAPE|API
            $table->unsignedBigInteger('raw_capture_id')->nullable(); // FK to price_tag_captures
            $table->string('status')->default('VALID');           // VALID|INVALIDATED|SUPERSEDED
            $table->timestamp('invalidated_at')->nullable();
            $table->string('invalidated_reason')->nullable();
            $table->unsignedBigInteger('superseded_by_id')->nullable();
            $table->timestamp('created_at')->useCurrent();        // no updated_at (append-only)

            $table->index('product_offer_id');
            $table->index('status');
            $table->index('observed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_observations');
    }
};
