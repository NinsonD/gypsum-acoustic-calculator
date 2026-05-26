<?php

declare(strict_types=1);

final class DownloadRepository
{
    public function __construct(private array $config)
    {
    }

    public function publicDownloads(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT * FROM downloads
             WHERE is_public = 1
             ORDER BY created_at DESC, title ASC'
        );

        return array_map([$this, 'mapDownload'], $stmt->fetchAll());
    }

    public function adminDownloads(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT * FROM downloads
             ORDER BY created_at DESC, title ASC'
        );

        return array_map([$this, 'mapDownload'], $stmt->fetchAll());
    }

    public function downloadById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM downloads WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $download = $stmt->fetch();

        return is_array($download) ? $this->mapDownload($download) : null;
    }

    public function downloadBySlug(string $slug): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM downloads WHERE slug = :slug AND is_public = 1 LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $download = $stmt->fetch();

        return is_array($download) ? $this->mapDownload($download) : null;
    }

    public function saveDownload(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $title = trim((string) ($data['title'] ?? ''));
        $slug = slugify((string) ($data['slug'] ?? $title));
        $filePath = trim((string) ($data['file_path'] ?? ''));
        $fileType = strtoupper(trim((string) ($data['file_type'] ?? '')));
        $category = trim((string) ($data['category'] ?? ''));
        $isPublic = isset($data['is_public']) ? 1 : 0;

        if ($title === '') {
            throw new InvalidArgumentException('Download title is required.');
        }

        if ($filePath === '') {
            throw new InvalidArgumentException('Download file is required.');
        }

        if ($fileType === '') {
            $fileType = strtoupper(pathinfo($filePath, PATHINFO_EXTENSION));
        }

        $payload = [
            'title' => $title,
            'slug' => $slug,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'category' => $category,
            'is_public' => $isPublic,
        ];

        if ($id > 0) {
            $payload['id'] = $id;
            $stmt = $this->pdo()->prepare(
                'UPDATE downloads
                 SET title = :title, slug = :slug, file_path = :file_path, file_type = :file_type,
                     category = :category, is_public = :is_public
                 WHERE id = :id'
            );
            $stmt->execute($payload);
            return;
        }

        $stmt = $this->pdo()->prepare(
            'INSERT INTO downloads (title, slug, file_path, file_type, category, is_public)
             VALUES (:title, :slug, :file_path, :file_type, :category, :is_public)'
        );
        $stmt->execute($payload);
    }

    public function deleteDownload(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM downloads WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function pdo(): PDO
    {
        return Database::connection($this->config);
    }

    private function mapDownload(array $download): array
    {
        $filePath = (string) ($download['file_path'] ?? '');

        return [
            'id' => (int) $download['id'],
            'title' => $download['title'],
            'slug' => $download['slug'],
            'file_path' => $filePath,
            'file_type' => $download['file_type'] ?? strtoupper(pathinfo($filePath, PATHINFO_EXTENSION)),
            'category' => $download['category'] ?? '',
            'is_public' => (int) ($download['is_public'] ?? 0),
            'created_at' => $download['created_at'] ?? '',
            'download_url' => url('/downloads/' . $download['slug']),
            'summary' => trim(((string) ($download['category'] ?? '')) . ' ' . ((string) ($download['file_type'] ?? 'file'))),
        ];
    }
}
