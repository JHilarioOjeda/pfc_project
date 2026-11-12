<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tarima', function (Blueprint $table) {
            $table->timestamp('delivered_date')->nullable()->after('status');
            $table->unsignedBigInteger('who_delivered')->nullable()->after('delivered_date');

            $table->foreign('who_delivered')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        // Tarimas confirmadas cuyos procesos ya están todos terminados y con reporte de medición
        $tarimaIds = DB::table('tarima')->where('status', 'finished')->pluck('id');

        foreach ($tarimaIds as $tarimaId) {
            $processes = DB::table('proccess')
                ->join('tarima_nps', 'tarima_nps.id', '=', 'proccess.id_tarima_np')
                ->where('tarima_nps.id_tarima', $tarimaId)
                ->select('proccess.id', 'proccess.status')
                ->get();

            if ($processes->isEmpty()) {
                continue;
            }

            $withReport = DB::table('meditions_report')
                ->whereIn('id_proccess', $processes->pluck('id'))
                ->distinct()
                ->pluck('id_proccess');

            $ready = $processes->every(fn ($p) => $p->status === 'finished' && $withReport->contains($p->id));

            if ($ready) {
                DB::table('tarima')->where('id', $tarimaId)->update(['status' => 'readytodeliver']);
            }
        }
    }

    public function down(): void
    {
        DB::table('tarima')
            ->whereIn('status', ['readytodeliver', 'delivered'])
            ->update(['status' => 'finished']);

        Schema::table('tarima', function (Blueprint $table) {
            $table->dropForeign(['who_delivered']);
            $table->dropColumn(['delivered_date', 'who_delivered']);
        });
    }
};
