<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn(Blueprint $t) => $t->index('restaurant_id'));

        Schema::table('daily_menu_items', fn(Blueprint $t) => $t->index('dish_id'));

        Schema::table('orders', fn(Blueprint $t) => $t->index('customer_id'));

        Schema::table('order_items', function (Blueprint $t) {
            $t->index('dish_id');
            $t->index('daily_menu_item_id');
        });

        Schema::table('whatsapp_sessions', fn(Blueprint $t) => $t->index('menu_id'));

        Schema::table('customer_customer_tag', fn(Blueprint $t) => $t->index('customer_tag_id'));
    }

    public function down(): void
    {
        Schema::table('users', fn(Blueprint $t) => $t->dropIndex(['restaurant_id']));
        Schema::table('daily_menu_items', fn(Blueprint $t) => $t->dropIndex(['dish_id']));
        Schema::table('orders', fn(Blueprint $t) => $t->dropIndex(['customer_id']));
        Schema::table('order_items', function (Blueprint $t) {
            $t->dropIndex(['dish_id']);
            $t->dropIndex(['daily_menu_item_id']);
        });
        Schema::table('whatsapp_sessions', fn(Blueprint $t) => $t->dropIndex(['menu_id']));
        Schema::table('customer_customer_tag', fn(Blueprint $t) => $t->dropIndex(['customer_tag_id']));
    }
};
