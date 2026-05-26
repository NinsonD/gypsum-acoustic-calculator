<?php

declare(strict_types=1);

final class BoqExportService
{
    public function filename(array $payload, string $extension): string
    {
        $reference = trim((string) ($payload['reference'] ?? 'BOQ-' . date('Ymd-His')));
        $project = trim((string) ($payload['project_name'] ?? 'boq-estimate'));
        $base = slugify($reference . '-' . $project);

        return $base . '.' . ltrim($extension, '.');
    }

    public function csv(array $payload): array
    {
        $rows = $this->rows($payload);
        $buffer = fopen('php://temp', 'r+');

        if ($buffer === false) {
            throw new RuntimeException('Unable to create CSV buffer.');
        }

        fwrite($buffer, "\xEF\xBB\xBF");
        fputcsv($buffer, ['BOQ Export']);
        fputcsv($buffer, ['Reference', $rows['meta']['reference']]);
        fputcsv($buffer, ['Project', $rows['meta']['project_name']]);
        fputcsv($buffer, ['Calculator', $rows['meta']['calculator_type']]);
        fputcsv($buffer, ['Area (sqm)', $rows['meta']['area']]);
        fputcsv($buffer, ['Created At', $rows['meta']['created_at']]);
        fputcsv($buffer, []);
        fputcsv($buffer, ['Summary']);
        fputcsv($buffer, ['Label', 'Value', 'Unit']);

        foreach ($rows['summary'] as $row) {
            fputcsv($buffer, [$row['label'], $row['value'], $row['unit']]);
        }

        fputcsv($buffer, []);
        fputcsv($buffer, ['Items']);
        fputcsv($buffer, ['Category', 'Item', 'Unit', 'Qty', 'Notes']);

        foreach ($rows['items'] as $row) {
            fputcsv($buffer, [$row['category'], $row['name'], $row['unit'], $row['qty'], $row['notes']]);
        }

        rewind($buffer);
        $content = stream_get_contents($buffer);
        fclose($buffer);

        return [
            'filename' => $this->filename($rows['meta'], 'csv'),
            'content_type' => 'text/csv; charset=UTF-8',
            'content' => $content === false ? '' : $content,
        ];
    }

    public function pdf(array $payload): array
    {
        $rows = $this->rows($payload);
        $lines = $this->pdfLines($rows);

        return [
            'filename' => $this->filename($rows['meta'], 'pdf'),
            'content_type' => 'application/pdf',
            'content' => $this->buildPdf($lines),
        ];
    }

    private function rows(array $payload): array
    {
        $summary = [];
        foreach ($payload['summary'] ?? [] as $row) {
            if (!is_array($row) || count($row) < 3) {
                continue;
            }

            $summary[] = [
                'label' => (string) ($row[0] ?? ''),
                'value' => $this->formatNumber($row[1] ?? ''),
                'unit' => (string) ($row[2] ?? ''),
            ];
        }

        $items = [];
        foreach ($payload['items'] ?? [] as $row) {
            if (!is_array($row)) {
                continue;
            }

            $items[] = [
                'category' => (string) ($row['category'] ?? ''),
                'name' => (string) ($row['name'] ?? ''),
                'unit' => (string) ($row['unit'] ?? ''),
                'qty' => $this->formatNumber($row['qty'] ?? ''),
                'notes' => (string) ($row['notes'] ?? ''),
            ];
        }

        return [
            'meta' => [
                'reference' => trim((string) ($payload['reference'] ?? 'BOQ-' . date('Ymd-His'))),
                'project_name' => trim((string) ($payload['project_name'] ?? 'BOQ Export')),
                'calculator_type' => trim((string) ($payload['calculator_type'] ?? 'calculator')),
                'area' => $this->formatNumber($payload['area'] ?? ''),
                'created_at' => (string) ($payload['created_at'] ?? date('Y-m-d H:i:s')),
            ],
            'summary' => $summary,
            'items' => $items,
        ];
    }

    private function pdfLines(array $rows): array
    {
        $lines = [
            'BOQ EXPORT',
            'Reference: ' . $rows['meta']['reference'],
            'Project: ' . $rows['meta']['project_name'],
            'Calculator: ' . $rows['meta']['calculator_type'],
            'Area (sqm): ' . $rows['meta']['area'],
            'Created At: ' . $rows['meta']['created_at'],
            '',
            'SUMMARY',
        ];

        foreach ($rows['summary'] as $row) {
            $lines[] = $row['label'] . ' | ' . $row['value'] . ' ' . $row['unit'];
        }

        $lines[] = '';
        $lines[] = 'ITEMS';

        foreach ($rows['items'] as $row) {
            $lines[] = $row['category'] . ' | ' . $row['name'] . ' | ' . $row['unit'] . ' | ' . $row['qty'] . ' | ' . $row['notes'];
        }

        $wrapped = [];
        foreach ($lines as $line) {
            $chunk = wordwrap($line, 90, "\n", true);
            foreach (explode("\n", $chunk) as $wrappedLine) {
                $wrapped[] = $wrappedLine;
            }
        }

        return $wrapped;
    }

    private function buildPdf(array $lines): string
    {
        $pageHeight = 842;
        $topMargin = 56;
        $leftMargin = 50;
        $lineHeight = 14;
        $usableLines = 48;
        $pages = array_chunk($lines, $usableLines);

        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';

        $kids = [];
        $pageObjectStart = 4;
        foreach ($pages as $pageIndex => $_pageLines) {
            $pageObjectId = $pageObjectStart + ($pageIndex * 2);
            $kids[] = $pageObjectId . ' 0 R';
        }

        $objects[] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($pages) . ' >>';
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

        foreach ($pages as $pageIndex => $pageLines) {
            $content = "BT\n/F1 10 Tf\n12 TL\n{$leftMargin} " . ($pageHeight - $topMargin) . " Td\n";

            foreach ($pageLines as $lineIndex => $line) {
                if ($lineIndex > 0) {
                    $content .= "T*\n";
                }

                $content .= '(' . $this->escapePdfText($line) . ") Tj\n";
            }

            $content .= "ET";
            $contentObjectId = 5 + ($pageIndex * 2);
            $pageObjectId = 4 + ($pageIndex * 2);
            $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents ' . $contentObjectId . ' 0 R >>';
            $objects[] = '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        }

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf('%010d 00000 n %s', $offsets[$i], "\n");
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xrefPosition}\n%%EOF";

        return $pdf;
    }

    private function escapePdfText(string $text): string
    {
        $text = str_replace(["\\", "(", ")"], ['\\\\', '\\(', '\\)'], $text);
        return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $text) ?? '';
    }

    private function formatNumber(mixed $value): string
    {
        if (!is_numeric($value)) {
            return (string) $value;
        }

        $formatted = number_format((float) $value, 6, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }
}
