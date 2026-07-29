<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('razao_social')->nullable()->after('name');
            $table->string('setup_token')->nullable()->unique()->after('paid_until');
            $table->timestamp('setup_completed_at')->nullable()->after('setup_token');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['razao_social', 'setup_token', 'setup_completed_at']);
        });
    }
};