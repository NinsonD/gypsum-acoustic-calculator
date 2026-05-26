<?php

declare(strict_types=1);

final class GalleryRepository
{
    public function __construct(private array $config)
    {
    }

    public function publicItems(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT * FROM gallery_items
             WHERE is_public = 1
             ORDER BY sort_order ASC, created_at DESC, title ASC'
        );

        return array_map([$this, 'mapItem'], $stmt->fetchAll());
    }

    public function adminItems(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT * FROM gallery_items
             ORDER BY sort_order ASC, created_at DESC, title ASC'
        );

        return array_map([$this, 'mapItem'], $stmt->fetchAll());
    }

    public function itemById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM gallery_items WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $item = $stmt->fetch();

        return is_array($item) ? $this->mapItem($item) : null;
    }

    public function saveItem(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $title = trim((string) ($data['title'] ?? ''));
        $slug = slugify((string) ($data['slug'] ?? $title));
        $summary = trim((string) ($data['summary'] ?? ''));
        $image = trim((string) ($data['image'] ?? ''));
        $projectName = trim((string) ($data['project_name'] ?? ''));
        $category = trim((string) ($data['category'] ?? ''));
        $sortOrder = is_numeric($data['sort_order'] ?? null) ? (int) $data['sort_order'] : 0;
        $isPublic = isset($data['is_public']) ? 1 : 0;

        if ($title === '') {
            throw new InvalidArgumentException('Gallery title is required.');
        }

        if ($image === '') {
            throw new InvalidArgumentException('Gallery image is required.');
        }

        $payload = [
            'title' => $title,
            'slug' => $slug,
            'summary' => $summary,
            'image' => $image,
            'project_name' => $projectName,
            'category' => $category,
            'sort_order' => $sortOrder,
            'is_public' => $isPublic,
        ];

        if ($id > 0) {
            $payload['id'] = $id;
            $stmt = $this->pdo()->prepare(
                'UPDATE gallery_items
                 SET title = :title, slug = :slug, summary = :summary, image = :image,
                     project_name = :project_name, category = :category, sort_order = :sort_order,
                     is_public = :is_public
                 WHERE id = :id'
            );
            $stmt->execute($payload);
            return;
        }

        $stmt = $this->pdo()->prepare(
            'INSERT INTO gallery_items
                (title, slug, summary, image, project_name, category, sort_order, is_public)
             VALUES
                (:title, :slug, :summary, :image, :project_name, :category, :sort_order, :is_public)'
        );
        $stmt->execute($payload);
    }

    public function deleteItem(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM gallery_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function pdo(): PDO
    {
        return Database::connection($this->config);
    }

    private function mapItem(array $item): array
    {
        return [
            'id' => (int) $item['id'],
            'title' => $item['title'],
            'slug' => $item['slug'],
            'summary' => $item['summary'] ?? '',
            'image' => $item['image'],
            'project_name' => $item['project_name'] ?? '',
            'category' => $item['category'] ?? '',
            'sort_order' => (int) ($item['sort_order'] ?? 0),
            'is_public' => (int) ($item['is_public'] ?? 0),
            'created_at' => $item['created_at'] ?? '',
            'image_url' => url('/' . ltrim((string) $item['image'], '/')),
        ];
    }
}
