<?php

declare(strict_types=1);

final class NotificationService
{
    public function __construct(private array $config)
    {
    }

    public function inquiry(array $inquiry): array
    {
        $adminEmail = trim((string) ($this->config['admin_email'] ?? ''));
        $subject = '[Lead] ' . ($inquiry['reference'] ?? 'New inquiry');
        $body = implode("\n", [
            'New inquiry received on the Gypsum & Acoustic platform.',
            '',
            'Reference: ' . ($inquiry['reference'] ?? '-'),
            'Name: ' . ($inquiry['name'] ?? '-'),
            'Email: ' . ($inquiry['email'] ?? '-'),
            'Phone: ' . ($inquiry['phone'] ?? '-'),
            'Type: ' . ($inquiry['inquiry_type'] ?? '-'),
            'Message: ' . ($inquiry['message'] ?? '-'),
            'Created At: ' . ($inquiry['created_at'] ?? '-'),
        ]);

        $delivery = 'log';
        $errors = [];

        if ($adminEmail !== '' && filter_var($adminEmail, FILTER_VALIDATE_EMAIL) && function_exists('mail')) {
            $headers = [
                'MIME-Version: 1.0',
                'Content-Type: text/plain; charset=UTF-8',
                'From: ' . ($this->mailFrom() ?: 'no-reply@localhost'),
            ];
            $sent = @mail($adminEmail, $subject, $body, implode("\r\n", $headers));
            if ($sent) {
                $delivery = 'mail';
            } else {
                $errors[] = 'mail_failed';
            }
        } else {
            $errors[] = 'mail_unavailable';
        }

        $payload = [
            'channel' => 'inquiry',
            'reference' => $inquiry['reference'] ?? '',
            'delivery' => $delivery,
            'errors' => $errors,
            'created_at' => date('c'),
        ];

        $this->log($payload);

        return [
            'delivery' => $delivery,
            'whatsapp_url' => $this->whatsappUrl($inquiry),
        ];
    }

    private function whatsappUrl(array $inquiry): string
    {
        $number = preg_replace('/\D+/', '', (string) ($this->config['whatsapp_number'] ?? '')) ?? '';
        if ($number === '') {
            return '';
        }

        $message = sprintf(
            "New inquiry: %s | %s | %s",
            (string) ($inquiry['name'] ?? ''),
            (string) ($inquiry['phone'] ?? ''),
            (string) ($inquiry['reference'] ?? '')
        );

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }

    private function mailFrom(): string
    {
        $from = trim((string) env('MAIL_FROM', ''));
        return filter_var($from, FILTER_VALIDATE_EMAIL) ? $from : '';
    }

    private function log(array $payload): void
    {
        $path = STORAGE_PATH . '/logs/notifications.log';
        if (!is_dir(dirname($path))) {
            @mkdir(dirname($path), 0775, true);
        }

        file_put_contents($path, json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
    }
}
