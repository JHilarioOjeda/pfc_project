<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timeouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_proccess');
            $table->text('type')->nullable();
            $table->decimal('hours', 10, 0)->nullable();
            $table->timestamps();

            $table->foreign('id_proccess')
                ->references('id')
                ->on('proccess')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('timeouts', function (Blueprint $table) {
            $table->dropForeign(['id_proccess']);
        });
        Schema::dropIfExists('timeouts');
    }
};
