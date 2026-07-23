<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('price_observations', function (Blueprint $table) {
            // 觀測備註（折扣說明、有效期、來源網址等）。model $fillable 已引用但原表缺此欄。
            $table->text('notes')->nullable()->after('invalidated_reason');
        });
    }

    public function down(): void
    {
        Schema::table('price_observations', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
