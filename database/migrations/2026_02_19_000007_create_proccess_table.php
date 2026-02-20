<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proccess', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tarima_np');
            $table->unsignedBigInteger('who_made');
            $table->unsignedBigInteger('id_line');
            $table->integer('pieces_alreadyproccess')->nullable();
            $table->text('operator_name')->nullable();
            $table->text('document_url')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('finished_date')->nullable();
            $table->text('status')->nullable();
            $table->timestamps();

            $table->foreign('id_tarima_np')
                ->references('id')
                ->on('tarima_nps')
                ->onDelete('cascade');

            $table->foreign('who_made')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('id_line')
                ->references('id')
                ->on('work_lines')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('proccess', function (Blueprint $table) {
            $table->dropForeign(['id_tarima_np']);
            $table->dropForeign(['who_made']);
            $table->dropForeign(['id_line']);
        });
        Schema::dropIfExists('proccess');
    }
};
