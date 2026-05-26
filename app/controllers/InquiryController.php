<?php

declare(strict_types=1);

final class InquiryController extends Controller
{
    public function store(): void
    {
        $data = $this->requestData();
        $name = trim((string) ($data['name'] ?? $data['customer_name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));
        $message = trim((string) ($data['message'] ?? ''));
        $type = trim((string) ($data['inquiry_type'] ?? 'General inquiry'));

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($phone) < 7 || strlen($message) < 10) {
            $this->json(['ok' => false, 'message' => 'Please complete name, valid email, phone, and project message.'], 422);
            return;
        }

        $reference = 'LEAD-' . date('Ymd-His');
        $payload = [
            'reference' => $reference,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'inquiry_type' => $type,
            'message' => $message,
            'created_at' => date('c'),
        ];

        file_put_contents(STORAGE_PATH . '/logs/inquiries.log', json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);

        $this->json([
            'ok' => true,
            'message' => 'Inquiry received. Reference: ' . $reference,
            'reference' => $reference,
        ]);
    }
}
