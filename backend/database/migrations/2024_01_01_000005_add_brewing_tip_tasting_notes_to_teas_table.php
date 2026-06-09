<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teas', function (Blueprint $table) {
            $table->text('brewing_tip')->nullable()->after('description');
            $table->text('tasting_notes')->nullable()->after('brewing_tip');
        });
    }

    public function down(): void
    {
        Schema::table('teas', function (Blueprint $table) {
            $table->dropColumn(['brewing_tip', 'tasting_notes']);
        });
    }
};
