<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarima_nps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_np');
            $table->unsignedBigInteger('id_tarima');
            $table->decimal('quantity', 10, 0)->nullable();
            $table->text('oc')->nullable();
            $table->text('of')->nullable();
            $table->boolean('status_cont')->default(false);
            $table->timestamps();

            $table->foreign('id_np')
                ->references('id')
                ->on('number_parts')
                ->onDelete('cascade');

            $table->foreign('id_tarima')
                ->references('id')
                ->on('tarima')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tarima_nps', function (Blueprint $table) {
            $table->dropForeign(['id_np']);
            $table->dropForeign(['id_tarima']);
        });
        Schema::dropIfExists('tarima_nps');
    }
};
