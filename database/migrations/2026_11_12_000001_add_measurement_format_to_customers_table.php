<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Formato de reporte de medición que se imprime para este cliente (1, 2 o 3).
            $table->unsignedTinyInteger('measurement_format')->default(2)->after('line');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('measurement_format');
        });
    }
};
