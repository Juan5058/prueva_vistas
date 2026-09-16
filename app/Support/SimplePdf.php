<?php

namespace App\Support;

class SimplePdf
{
    /**
     * @param  list<string>  $lines
     */
    public static function fromLines(string $title, array $lines): string
    {
        $pages = [];
        $current = [];

        foreach ($lines as $line) {
            $chunks = str_split(self::toWinAnsi($line), 95);
            if ($chunks === []) {
                $chunks = [''];
            }

            foreach ($chunks as $chunk) {
                $current[] = $chunk;
                if (count($current) >= 42) {
                    $pages[] = $current;
                    $current = [];
                }
            }
        }

        if ($current !== []) {
            $pages[] = $current;
        }

        if ($pages === []) {
            $pages[] = ['Sin datos.'];
        }

        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $pageCount = count($pages);
        $kids = [];

        for ($i = 0; $i < $pageCount; $i++) {
            $kids[] = (4 + ($i * 2)).' 0 R';
        }

        $objects[2] = '<< /Type /Pages /Count '.$pageCount.' /Kids ['.implode(' ', $kids).'] >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';

        foreach ($pages as $i => $pageLines) {
            $pageId = 4 + ($i * 2);
            $contentId = $pageId + 1;
            $stream = "BT\n/F1 12 Tf\n50 760 Td\n(".self::escape($title.' - pagina '.($i + 1)).") Tj\n0 -22 Td\n/F1 9 Tf\n";

            foreach ($pageLines as $line) {
                $stream .= '('.self::escape($line).") Tj\n0 -14 Td\n";
            }

            $stream .= 'ET';
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R >> >> /Contents '.$contentId.' 0 R >>';
            $objects[$contentId] = '<< /Length '.strlen($stream)." >>\nstream\n".$stream."\nendstream";
        }

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        $maxId = 3 + ($pageCount * 2);

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
