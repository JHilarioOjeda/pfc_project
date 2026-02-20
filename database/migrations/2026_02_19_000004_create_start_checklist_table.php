<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('start_checklist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->json('questions')->nullable();
            $table->timestamp('register_date')->nullable();
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('start_checklist', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        Schema::dropIfExists('start_checklist');
    }
};
