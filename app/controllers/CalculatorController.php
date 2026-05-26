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
        $logPath = STORAGE_PATH . '/logs/boq.log';
        $payload = ['reference' => $reference, 'project_name' => $projectName, 'items' => $items, 'created_at' => date('c')];
        file_put_contents($logPath, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);

        $this->json([
            'ok' => true,
            'message' => 'BOQ received.',
            'reference' => $reference,
        ]);
    }
}
