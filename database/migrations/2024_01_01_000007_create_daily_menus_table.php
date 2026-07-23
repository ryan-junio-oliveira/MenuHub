<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->date('menu_date');
            $table->string('title')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['restaurant_id', 'menu_date']);
            $table->index(['restaurant_id', 'menu_date', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_menus');
    }
};
