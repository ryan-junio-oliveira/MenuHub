<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number', 20);
            $table->string('status')->default('received');
            $table->string('source')->default('whatsapp');
            $table->text('notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('delivery_type')->default('delivery');
            $table->text('delivery_address')->nullable();
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['restaurant_id', 'order_number']);
            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'ordered_at']);
            $table->index(['restaurant_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
