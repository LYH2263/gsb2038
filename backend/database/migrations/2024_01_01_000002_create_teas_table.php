<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('tea_type_id')->nullable()->constrained('tea_types')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('origin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teas');
    }
};
