<?php

use App\Models\MeditionsReport;
use App\Models\MeditionsReportFolio;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meditions_report_folio_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_folio')->constrained('meditions_report_folios')->onDelete('cascade');
            $table->foreignId('id_meditions_report')->constrained('meditions_report')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['id_folio', 'id_meditions_report'], 'meditions_report_folio_report_unique');
        });

        $this->backfillFromLegacyFolioColumn();
    }

    public function down(): void
    {
        Schema::dropIfExists('meditions_report_folio_report');
    }

    /**
     * Los reportes que hoy comparten un mismo valor de texto en la columna
     * (ya obsoleta) `meditions_report.folio` se agrupan en un único registro
     * de `meditions_report_folios` con ese mismo texto, y se enlazan a él.
     *
     * Esto es solo una foto de la situación actual: si algún reporte fue
     * impreso antes como parte de otra combinación y luego su folio fue
     * sobrescrito por una impresión posterior (el bug que esta migración
     * corrige hacia adelante), esa agrupación anterior ya no existe en la
     * columna y no se puede reconstruir aquí.
     */
    private function backfillFromLegacyFolioColumn(): void
    {
        MeditionsReport::query()
            ->whereNotNull('folio')
            ->where('folio', '!=', '')
            ->select('id', 'folio')
            ->orderBy('id')
            ->chunkById(500, function ($reports) {
                foreach ($reports as $report) {
                    $value = trim((string) $report->folio);

                    if ($value === '') {
                        continue;
                    }

                    $folio = MeditionsReportFolio::firstOrCreate(['folio' => $value]);
                    $folio->reports()->syncWithoutDetaching([$report->id]);
                }
            });
    }
};
