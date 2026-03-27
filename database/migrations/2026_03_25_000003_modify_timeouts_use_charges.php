<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old FK if it still exists (may survive as orphan after column drop)
        $existingFks = collect(Schema::getForeignKeys('timeouts'))->pluck('name')->all();
        if (in_array('timeouts_id_proccess_foreign', $existingFks)) {
            Schema::table('timeouts', function (Blueprint $table) {
                $table->dropForeign('timeouts_id_proccess_foreign');
            });
        }

        // Drop old column if it still exists
        if (Schema::hasColumn('timeouts', 'id_proccess')) {
            Schema::table('timeouts', function (Blueprint $table) {
                $table->dropColumn('id_proccess');
            });
        }

        // Add new column if not yet present
        if (! Schema::hasColumn('timeouts', 'id_charge')) {
            Schema::table('timeouts', function (Blueprint $table) {
                $table->unsignedBigInteger('id_charge')->nullable()->after('id');
            });
        }

        // Remove all rows without a valid charge reference before adding FK
        \Illuminate\Support\Facades\DB::statement(
            'DELETE t FROM timeouts t LEFT JOIN charges c ON t.id_charge = c.id WHERE c.id IS NULL'
        );

        // Add FK only if it doesn't already exist
        $existingFks = collect(Schema::getForeignKeys('timeouts'))->pluck('name')->all();
        if (! in_array('timeouts_id_charge_foreign', $existingFks)) {
            Schema::table('timeouts', function (Blueprint $table) {
                $table->foreign('id_charge')
                    ->references('id')
                    ->on('charges')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('timeouts', function (Blueprint $table) {
            $table->dropForeign(['id_charge']);
            $table->dropColumn('id_charge');

            $table->unsignedBigInteger('id_proccess')->after('id');

            $table->foreign('id_proccess')
                ->references('id')
                ->on('proccess')
                ->onDelete('cascade');
        });
    }
};
