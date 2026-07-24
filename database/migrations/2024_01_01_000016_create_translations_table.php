<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('language', 10);
            $table->string('alias', 100)->nullable();
            $table->string('original', 500);
            $table->text('translated')->nullable();
            $table->boolean('is_translated')->default(false);
            $table->timestamps();

            $table->unique(['language', 'original'], 'translations_language_original_unique');
            $table->index(['language', 'is_translated'], 'translations_language_translated_index');
        });

        // Case-sensitive collation for MySQL — safely skip for SQLite/PostgreSQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE translations MODIFY original VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
