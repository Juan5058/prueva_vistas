<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

class TabularFile
{
    /**
     * @return list<list<string>>
     */
    public static function rows(string $contents, string $filename): array
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($extension, ['xlsx', 'xlsm'], true)) {
            return self::fromXlsx($contents);
        }

        if (in_array($extension, ['xls'], true)) {
            throw new RuntimeException('El formato .xls no está soportado. Guarde el archivo como .xlsx o CSV.');
        }

        return self::fromCsv($contents);
    }

    /**
     * @return list<list<string>>
     */
    private static function fromCsv(string $contents): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $contents) ?: [];
        $lines = array_values(array_filter($lines, fn ($line) => trim($line) !== ''));

        if ($lines === []) {
            return [];
        }

        $delimiter = str_contains($lines[0], ';') ? ';' : (str_contains($lines[0], "\t") ? "\t" : ',');
        $rows = [];

        foreach ($lines as $line) {
            $cols = array_map(
                fn ($col) => trim(preg_replace('/^["\']|["\']$/', '', $col) ?? ''),
                explode($delimiter, $line),
            );
            $rows[] = $cols;
        }

        return $rows;
    }

    /**
     * @return list<list<string>>
     */
    private static function fromXlsx(string $contents): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'trdxlsx');
        if ($tmp === false) {
            throw new RuntimeException('No fue posible leer el archivo Excel.');
        }

        file_put_contents($tmp, $contents);

        $zip = new ZipArchive;
        $opened = $zip->open($tmp);

        try {
            if ($opened !== true) {
                throw new RuntimeException('El archivo Excel (.xlsx) no es válido.');
            }

            $shared = self::sharedStrings($zip->getFromName('xl/sharedStrings.xml') ?: '');
            $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');

            if ($sheet === false || $sheet === '') {
                throw new RuntimeException('El libro Excel no contiene la hoja 1.');
            }

            return self::sheetRows($sheet, $shared);
        } finally {
            if ($opened === true) {
                $zip->close();
            }
            @unlink($tmp);
        }
    }

    /**
     * @return list<string>
     */
    private static function sharedStrings(string $xml): array
    {
        $document = self::xml($xml);
        if ($document === null) {
            return [];
        }

        $values = [];

        foreach ($document->si as $si) {
            $text = '';
            foreach ($si->t as $node) {
                $text .= (string) $node;
            }
            foreach ($si->r as $run) {
                $text .= (string) $run->t;
            }
            $values[] = trim($text);
        }

        return $values;
    }

    /**
     * @param  list<string>  $shared
     * @return list<list<string>>
     */
    private static function sheetRows(string $xml, array $shared): array
    {
        $document = self::xml($xml);
        if ($document === null || ! isset($document->sheetData)) {
            return [];
        }

        $rows = [];

        foreach ($document->sheetData->row as $row) {
            $cells = [];

            foreach ($row->c as $cell) {
                $index = self::columnIndex((string) $cell['r']);
                $type = (string) $cell['t'];
                $value = '';

                if ($type === 's') {
                    $value = $shared[(int) (string) $cell->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $text = '';
                    if (isset($cell->is->t)) {
                        foreach ($cell->is->t as $node) {
                            $text .= (string) $node;
                        }
                    }
                    $value = trim($text);
                } else {
                    $value = trim((string) $cell->v);
                }

                $cells[$index] = $value;
            }

            if ($cells === []) {
                continue;
            }

            ksort($cells);
            $max = max(array_keys($cells));
            $normalized = [];
            for ($i = 0; $i <= $max; $i++) {
                $normalized[] = $cells[$i] ?? '';
            }

            if (implode('', $normalized) === '') {
                continue;
            }

            $rows[] = $normalized;
        }

        return $rows;
    }

    private static function xml(string $xml): ?\SimpleXMLElement
    {
        if ($xml === '') {
            return null;
        }

        $stripped = preg_replace('/xmlns(:\w+)?="[^"]*"/', '', $xml) ?? $xml;
        $document = simplexml_load_string($stripped);

        return $document === false ? null : $document;
    }

    private static function columnIndex(string $reference): int
    {
        preg_match('/^([A-Z]+)/i', $reference, $matches);
        $letters = strtoupper($matches[1] ?? 'A');
        $number = 0;

        foreach (str_split($letters) as $letter) {
            $number = ($number * 26) + (ord($letter) - 64);
        }

        return max(0, $number - 1);
    }
}
