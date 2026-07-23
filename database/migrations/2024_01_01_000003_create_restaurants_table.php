<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->string('pix_key')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_api_token')->nullable();
            $table->string('whatsapp_phone_id')->nullable();
            $table->string('whatsapp_business_account_id')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('minimum_order', 10, 2)->default(0);
            $table->json('opening_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
