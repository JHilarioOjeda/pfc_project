<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meditionsreport_observations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medition_report');
            $table->decimal('thickness_in_microns', 10, 0)->nullable();
            $table->text('visual_appearance')->nullable();
            $table->timestamps();

            $table->foreign('id_medition_report')
                ->references('id')
                ->on('meditions_report')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('meditionsreport_observations', function (Blueprint $table) {
            $table->dropForeign(['id_medition_report']);
        });
        Schema::dropIfExists('meditionsreport_observations');
    }
};
