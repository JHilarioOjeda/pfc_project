<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('number_parts', function (Blueprint $table) {
            $table->id();
            $table->text('partnumber');
            $table->text('process')->nullable();
            $table->text('details')->nullable();
            $table->decimal('microns', 10, 6)->nullable();
            $table->decimal('inches', 10, 6)->nullable();
            $table->decimal('decimeters', 10, 6)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('number_parts');
    }
};
