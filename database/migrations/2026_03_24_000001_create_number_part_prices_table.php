<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('number_part_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_np')->constrained('number_parts')->onDelete('cascade');
            $table->decimal('price', 10, 6);
            $table->date('price_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('number_part_prices');
    }
};
