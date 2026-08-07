<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tarima_nps', function (Blueprint $table) {
            $table->decimal('quantity', 14, 6)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarima_nps', function (Blueprint $table) {
            $table->decimal('quantity', 10, 6)->nullable()->change();
        });
    }
};
