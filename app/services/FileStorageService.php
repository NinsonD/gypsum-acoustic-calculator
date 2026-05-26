<?php

declare(strict_types=1);

final class FileStorageService
{
    public function upload(
        array $file,
        string $baseDirectory,
        string $relativeDirectory,
        array $allowedExtensions,
        int $maxBytes = 10485760
    ): array {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['path' => null, 'name' => null, 'extension' => null, 'mime' => null];
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Upload failed. Please try again.');
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file((string) $file['tmp_name'])) {
            throw new InvalidArgumentException('Invalid upload source.');
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > $maxBytes) {
            throw new InvalidArgumentException('File size is not allowed.');
        }

        $originalName = (string) ($file['name'] ?? 'upload');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
            throw new InvalidArgumentException('File type is not allowed.');
        }

        $directory = rtrim($baseDirectory, '\\/') . DIRECTORY_SEPARATOR . trim($relativeDirectory, '\\/');
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create upload directory.');
        }

        $filename = slugify(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
        $absolutePath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file((string) $file['tmp_name'], $absolutePath)) {
            throw new RuntimeException('Unable to store uploaded file.');
        }

        return [
            'path' => trim($relativeDirectory, '\\/') . '/' . $filename,
            'name' => $originalName,
            'extension' => $extension,
            'mime' => (string) ($file['type'] ?? ''),
            'size' => $size,
        ];
    }

    public function delete(string $baseDirectory, ?string $relativePath): void
    {
        $relativePath = trim((string) $relativePath);
        if ($relativePath === '') {
            return;
        }

        $absolutePath = rtrim($baseDirectory, '\\/') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim($relativePath, '/\\'));
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }
}
