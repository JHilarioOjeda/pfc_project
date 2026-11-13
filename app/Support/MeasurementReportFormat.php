<?php

namespace App\Support;

use App\Models\Customer;
use Carbon\Carbon;

/**
 * Utilidades compartidas por los formatos de reporte de medición
 * (resources/views/formats/measurement/formato{1,2,3}.blade.php).
 */
class MeasurementReportFormat
{
    // Datos del encabezado de cada formato, tal como vienen en los .docx originales.
    public const HEADERS = [
        1 => ['company' => 'RECUBRIMIENTOS INDUSTRIALES PFC S.A. DE C.V.', 'date' => '12/Nov/2025'],
        2 => ['company' => 'PLATING FINISHING AND COATING S. DE R.L DE C.V.', 'date' => '12/Nov/2025'],
        3 => ['company' => 'PLATING FINISHING AND COATING S. DE R.L DE C.V.', 'date' => '12/Nov/2026'],
    ];

    public const DEFAULT_NOTE = '*Se recomienda mantener el material en un lugar seco libre de humedad o líquidos (agua, soluciones, aceites, etc.) o solventes que emanen vapores.';

    // Número de formato (1, 2 o 3) configurado para el cliente; 2 por defecto.
    public static function formatFor(?Customer $customer): int
    {
        $format = (int) optional($customer)->measurement_format;

        return array_key_exists($format, Customer::MEASUREMENT_FORMATS)
            ? $format
            : Customer::DEFAULT_MEASUREMENT_FORMAT;
    }

    public static function viewFor(?Customer $customer): string
    {
        return 'formats.measurement.formato' . self::formatFor($customer);
    }

    // Muestra un valor numérico sin ceros sobrantes (p. ej. "5.000000" -> "5").
    // Si no es numérico (p. ej. "Sn 5um") se regresa tal cual.
    public static function number($value, int $decimals = 4): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (!is_numeric($value)) {
            return (string) $value;
        }

        $formatted = number_format((float) $value, $decimals, '.', ',');

        return str_contains($formatted, '.') ? rtrim(rtrim($formatted, '0'), '.') : $formatted;
    }

    // "9,114 piezas"
    public static function pieces($quantity): string
    {
        $number = self::number($quantity, 2);

        return $number === '' ? '' : $number . ' piezas';
    }

    // "Semana 43 (21 de Octubre del 2026)"
    public static function processWeek($date): string
    {
        if (!$date) {
            return '';
        }

        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $month = ucfirst($date->copy()->locale('es')->monthName);

        return "Semana {$date->isoWeek} ({$date->day} de {$month} del {$date->year})";
    }
}
