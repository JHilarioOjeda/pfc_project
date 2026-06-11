<?php

namespace App\Support;
use RuntimeException;
use ZipArchive;
use SimpleXMLElement;

class NpImportFileParser
{
    public function parse(string $path, string $extension): array
    {
        if (in_array($extension, ['csv', 'txt'], true)) {
            return $this->parseCsvFile($path);
        }

        if ($extension === 'xlsx') {
            return $this->parseXlsxFile($path);
        }

        if ($extension === 'xls') {
            throw new RuntimeException('El formato XLS no es compatible. Convierte el archivo a CSV o XLSX.');
        }

        throw new RuntimeException('Formato no soportado.');
    }

    private function parseCsvFile(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('No se pudo abrir el archivo CSV.');
        }

        $isFirstRow = true;
        $delimiter = ',';

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($isFirstRow) {
                $isFirstRow = false;
                $delimiter = $this->detectCsvDelimiter($data);
                continue;
            }

            $normalized = $this->normalizeImportColumns($data);
            if ($normalized !== null) {
                $rows[] = $normalized;
            }
        }

        fclose($handle);

        return $rows;
    }

    private function detectCsvDelimiter(array $headerRow): string
    {
        if (count($headerRow) > 1) {
            return ',';
        }

        $rawHeader = (string) ($headerRow[0] ?? '');
        return substr_count($rawHeader, ';') > substr_count($rawHeader, ',') ? ';' : ',';
    }

    private function parseXlsxFile(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('No se pudo abrir el archivo XLSX.');
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            throw new RuntimeException('No se encontro la hoja principal en el XLSX.');
        }

        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        $zip->close();

        $sharedStrings = [];
        if ($sharedStringsXml !== false) {
            $shared = simplexml_load_string($sharedStringsXml);
            if ($shared !== false && isset($shared->si)) {
                foreach ($shared->si as $si) {
                    $text = '';
                    if (isset($si->t)) {
                        $text = (string) $si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $run) {
                            $text .= (string) $run->t;
                        }
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheet = simplexml_load_string($sheetXml);
        if ($sheet === false || !isset($sheet->sheetData->row)) {
            throw new RuntimeException('No se pudieron leer filas del XLSX.');
        }

        $rows = [];
        $isFirstRow = true;

        foreach ($sheet->sheetData->row as $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            $cellsByIndex = [];
            foreach ($row->c as $cell) {
                $cellRef = (string) ($cell['r'] ?? '');
                $columnLetters = preg_replace('/\d+/', '', $cellRef);
                $columnIndex = $this->excelColumnToIndex($columnLetters);

                $rawValue = '';
                if (isset($cell->v)) {
                    $rawValue = (string) $cell->v;
                }

                $type = (string) ($cell['t'] ?? '');
                if ($type === 's' && $rawValue !== '') {
                    $sharedIndex = (int) $rawValue;
                    $rawValue = $sharedStrings[$sharedIndex] ?? '';
                }

                $cellsByIndex[$columnIndex] = $rawValue;
            }

            ksort($cellsByIndex);
            $normalized = $this->normalizeImportColumns([
                $cellsByIndex[0] ?? null,
                $cellsByIndex[1] ?? null,
                $cellsByIndex[2] ?? null,
                $cellsByIndex[3] ?? null,
                $cellsByIndex[4] ?? null,
            ]);

            if ($normalized !== null) {
                $rows[] = $normalized;
            }
        }

        return $rows;
    }

    private function excelColumnToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }

    private function normalizeImportColumns(array $columns): ?array
    {
        $partnumber = isset($columns[0]) ? trim((string) $columns[0]) : '';
        if ($partnumber === '') {
            return null;
        }

        $process = isset($columns[1]) ? trim((string) $columns[1]) : null;
        $inches = $this->toNullableNumber($columns[2] ?? null);
        $microns = $this->toNullableNumber($columns[3] ?? null);
        $price = $this->toNullableNumber($columns[4] ?? null);

        return [
            'partnumber' => $partnumber,
            'process' => $process,
            'inches' => $inches,
            'microns' => $microns,
            'price' => $price,
        ];
    }

    private function toNullableNumber(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '' || strtoupper($value) === 'NA' || strtoupper($value) === 'N/A') {
            return null;
        }

        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }
}
