<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meditions_report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_proccess');
            $table->text('requirement')->nullable();
            $table->text('method')->nullable();
            $table->timestamp('register_date')->nullable();
            $table->text('document_url')->nullable();
            $table->timestamps();

            $table->foreign('id_proccess')
                ->references('id')
                ->on('proccess')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('meditions_report', function (Blueprint $table) {
            $table->dropForeign(['id_proccess']);
        });
        Schema::dropIfExists('meditions_report');
    }
};
