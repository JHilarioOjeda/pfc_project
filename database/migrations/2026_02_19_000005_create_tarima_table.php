<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarima', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_customer');
            $table->unsignedBigInteger('who_register');
            $table->text('serial_number')->nullable();
            $table->timestamp('register_date')->nullable();
            $table->text('document_url')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('id_customer')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('who_register')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tarima', function (Blueprint $table) {
            $table->dropForeign(['id_customer']);
            $table->dropForeign(['who_register']);
        });
        Schema::dropIfExists('tarima');
    }
};
