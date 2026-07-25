<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->string('customer_phone', 20);
            $table->string('customer_name', 100)->nullable();
            $table->string('step', 50)->default('idle');
            $table->json('data')->nullable();
            $table->foreignId('menu_id')->nullable()->constrained('daily_menus')->onDelete('set null');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();

            $table->unique(['restaurant_id', 'customer_phone']);
            $table->index(['restaurant_id', 'step']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
