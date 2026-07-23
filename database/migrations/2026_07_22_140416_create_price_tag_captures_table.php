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
        Schema::create('price_tag_captures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('image_path')->nullable();
            $table->text('ocr_raw_text')->nullable();
            $table->json('ocr_parsed_json')->nullable();
            $table->string('parsed_item_number')->nullable();
            $table->string('parsed_name')->nullable();
            $table->unsignedBigInteger('parsed_amount_minor')->nullable(); // integer minor units
            $table->string('parsed_currency_code', 3)->default('TWD');
            $table->timestamp('parsed_at')->nullable();
            $table->string('status')->default('PENDING'); // PENDING|PARSED|FAILED|LINKED
            $table->timestamps();

            $table->index('store_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_tag_captures');
    }
};
