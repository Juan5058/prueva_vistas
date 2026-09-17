<?php

namespace App\Support;

class SimplePdf
{
    /**
     * Genera un PDF nativo 1.4 con plantilla corporativa COTRANSHUILA S.A.
     *
     * @param  list<string>  $lines
     */
    public static function fromLines(string $title, array $lines): string
    {
        $pages = [];
        $current = [];

        foreach ($lines as $line) {
            $chunks = str_split(self::toWinAnsi($line), 88);
            if ($chunks === []) {
                $chunks = [''];
            }

            foreach ($chunks as $chunk) {
                $current[] = $chunk;
                if (count($current) >= 34) {
                    $pages[] = $current;
                    $current = [];
                }
            }
        }

        if ($current !== []) {
            $pages[] = $current;
        }

        if ($pages === []) {
            $pages[] = ['Sin datos registrados.'];
        }

        $pageCount = count($pages);
        $objects = [];

        // 1: Catalog
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        // Kids array
        $kids = [];
        for ($i = 0; $i < $pageCount; $i++) {
            $kids[] = (5 + ($i * 2)).' 0 R';
        }
        $objects[2] = '<< /Type /Pages /Count '.$pageCount.' /Kids ['.implode(' ', $kids).'] >>';

        // Fonts: Helvetica (F1) and Helvetica-Bold (F2)
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        foreach ($pages as $i => $pageLines) {
            $pageId = 5 + ($i * 2);
            $contentId = $pageId + 1;

            $stream = "";

            // 1. COTRANSHUILA Corporate Header Banner (Green: 0.04 0.45 0.24)
            $stream .= "0.04 0.45 0.24 rg 0 726 612 66 re f\n";

            // Gold accent stripe under header (Gold: 0.95 0.72 0.08)
            $stream .= "0.95 0.72 0.08 rg 0 720 612 6 re f\n";

            // Header Text (White)
            $stream .= "BT\n/F2 14 Tf\n1 1 1 rg\n40 762 Td\n(".self::escape("COTRANSHUILA S.A. - TRANSPORTE Y LOGISTICA").") Tj\nET\n";
            $stream .= "BT\n/F1 9 Tf\n0.88 0.96 0.90 rg\n40 742 Td\n(".self::escape("Sistema de Gestio\n Documental TRD y Archivo Oficial").") Tj\nET\n";

            // Page badge top right
            $pageStr = "Pagina ".($i + 1)." de ".$pageCount;
            $stream .= "BT\n/F2 9 Tf\n1 1 1 rg\n480 755 Td\n(".self::escape($pageStr).") Tj\nET\n";

            // 2. COTRANSHUILA Corporate Footer (Green & Gold)
            $stream .= "0.04 0.45 0.24 rg 0 0 612 28 re f\n";
            $stream .= "0.95 0.72 0.08 rg 0 28 612 3 re f\n";
            $stream .= "BT\n/F1 8 Tf\n1 1 1 rg\n40 10 Td\n(".self::escape("COTRANSHUILA S.A. - Documento Oficial de Inventario | Confidencial y de Uso Interno").") Tj\nET\n";

            // Watermark / Subtitle
            $stream .= "BT\n/F2 8 Tf\n0.95 0.72 0.08 rg\n480 10 Td\n(".self::escape("NIT: 891.100.015-7").") Tj\nET\n";

            // 3. Document Body Content
            $y = 690;
            foreach ($pageLines as $line) {
                if (str_starts_with($line, '[SECCION]')) {
                    $secTitle = trim(str_replace('[SECCION]', '', $line));
                    // Draw Section Header Box
                    $stream .= "0.04 0.45 0.24 rg 40 ".($y - 4)." 532 20 re f\n";
                    $stream .= "0.95 0.72 0.08 rg 40 ".($y - 4)." 4 20 re f\n";
                    $stream .= "BT\n/F2 10 Tf\n1 1 1 rg\n50 {$y} Td\n(".self::escape($secTitle).") Tj\nET\n";
                    $y -= 26;
                } elseif (str_starts_with($line, 'EMPRESA:') || str_starts_with($line, 'SISTEMA:') || str_starts_with($line, 'FECHA GENERACIÓN:') || str_starts_with($line, 'PERÍODO') || str_starts_with($line, 'REPORTES')) {
                    // Header metadata info
                    $parts = explode(':', $line, 2);
                    $label = $parts[0].':';
                    $val = trim($parts[1] ?? '');
                    $stream .= "BT\n/F2 9 Tf\n0.04 0.45 0.24 rg\n40 {$y} Td\n(".self::escape($label).") Tj\nET\n";
                    $stream .= "BT\n/F1 9 Tf\n0.15 0.20 0.25 rg\n180 {$y} Td\n(".self::escape($val).") Tj\nET\n";
                    $y -= 15;
                } elseif ($line === '---') {
                    $stream .= "0.85 0.88 0.90 rg 40 {$y} 532 1 re f\n";
                    $y -= 12;
                } elseif (str_starts_with($line, '  •')) {
                    $stream .= "BT\n/F1 9 Tf\n0.20 0.30 0.35 rg\n55 {$y} Td\n(".self::escape($line).") Tj\nET\n";
                    $y -= 14;
                } else {
                    $isBold = str_contains($line, ':') && !str_starts_with($line, ' ');
                    $font = $isBold ? '/F2' : '/F1';
                    $color = $isBold ? '0.10 0.15 0.20 rg' : '0.25 0.30 0.35 rg';

                    $stream .= "BT\n{$font} 9 Tf\n{$color}\n40 {$y} Td\n(".self::escape($line).") Tj\nET\n";
                    $y -= 14;
                }
            }

            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents '.$contentId.' 0 R >>';
            $objects[$contentId] = '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream";
        }

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        $maxId = 4 + ($pageCount * 2);

        for ($id = 1; $id <= $maxId; $id++) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$objects[$id]."\nendobj\n";
        }

        $xref = strlen($pdf);
        $size = $maxId + 1;
        $pdf .= "xref\n0 {$size}\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= $maxId; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        $pdf .= "trailer << /Size {$size} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    private static function toWinAnsi(string $text): string
    {
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $text);

        return $converted === false ? $text : $converted;
    }

    private static function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], self::toWinAnsi($text));
    }
}

