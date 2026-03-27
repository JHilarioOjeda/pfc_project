<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_proccess');
            $table->integer('quantity_pieces')->nullable();
            $table->text('status')->nullable();
            $table->unsignedBigInteger('who_made')->nullable();
            $table->timestamp('made_date')->nullable();
            $table->unsignedBigInteger('who_free')->nullable();
            $table->timestamp('free_date')->nullable();
            $table->unsignedBigInteger('who_confirms')->nullable();
            $table->timestamp('confirm_date')->nullable();
            $table->timestamps();

            $table->foreign('id_proccess')
                ->references('id')
                ->on('proccess')
                ->onDelete('cascade');

            $table->foreign('who_made')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('who_free')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('who_confirms')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropForeign(['id_proccess']);
            $table->dropForeign(['who_made']);
            $table->dropForeign(['who_free']);
            $table->dropForeign(['who_confirms']);
        });
        Schema::dropIfExists('charges');
    }
};
