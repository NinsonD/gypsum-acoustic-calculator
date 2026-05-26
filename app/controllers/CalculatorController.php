<?php

declare(strict_types=1);

final class CalculatorController extends Controller
{
    public function boq(): void
    {
        $data = $this->requestData();
        $projectName = trim((string) ($data['project_name'] ?? $data['projectName'] ?? ''));
        $items = $data['items'] ?? [];

        if ($projectName === '') {
            $this->json(['ok' => false, 'message' => 'Project name is required.'], 422);
            return;
        }

        if (!is_array($items) || count($items) === 0) {
            $this->json(['ok' => false, 'message' => 'BOQ items are required.'], 422);
            return;
        }

        $reference = 'BOQ-' . date('Ymd-His');
        $calculatorType = trim((string) ($data['calculator_type'] ?? $data['type'] ?? 'calculator'));
        $area = isset($data['area']) && is_numeric($data['area']) ? (float) $data['area'] : null;
        $logPath = STORAGE_PATH . '/logs/boq.log';
        $payload = [
            'reference' => $reference,
            'project_name' => $projectName,
            'calculator_type' => $calculatorType,
            'area' => $area,
            'summary' => $data['summary'] ?? [],
            'items' => $items,
            'created_at' => date('c'),
        ];

        try {
            $stmt = $this->db()->prepare(
                'INSERT INTO boq_estimations (reference, project_name, calculator_type, area, calculations_json)
                 VALUES (:reference, :project_name, :calculator_type, :area, :calculations_json)'
            );
            $stmt->execute([
                'reference' => $reference,
                'project_name' => $projectName,
                'calculator_type' => $calculatorType,
                'area' => $area,
                'calculations_json' => json_encode($payload, JSON_UNESCAPED_SLASHES),
            ]);
            $storage = 'database';
        } catch (Throwable $error) {
            $payload['error'] = $error->getMessage();
            file_put_contents($logPath, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
            $storage = 'log';
        }

        $this->json([
            'ok' => true,
            'message' => 'BOQ received.',
            'reference' => $reference,
            'storage' => $storage,
        ]);
    }
}
