<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_menu_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_option_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['daily_menu_id', 'menu_option_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_menu_option');
    }
};
