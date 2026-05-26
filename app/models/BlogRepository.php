<?php

declare(strict_types=1);

final class BlogRepository
{
    public function __construct(private array $config)
    {
    }

    public function publicPosts(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT blog_posts.*, users.name AS author_name
             FROM blog_posts
             LEFT JOIN users ON users.id = blog_posts.author_id
             WHERE blog_posts.status = "published"
             ORDER BY blog_posts.published_at DESC, blog_posts.created_at DESC'
        );

        return array_map([$this, 'mapPost'], $stmt->fetchAll());
    }

    public function adminPosts(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT blog_posts.*, users.name AS author_name
             FROM blog_posts
             LEFT JOIN users ON users.id = blog_posts.author_id
             ORDER BY blog_posts.updated_at DESC, blog_posts.created_at DESC'
        );

        return array_map([$this, 'mapPost'], $stmt->fetchAll());
    }

    public function postBySlug(string $slug, bool $includeDraft = false): ?array
    {
        $sql = 'SELECT blog_posts.*, users.name AS author_name
                FROM blog_posts
                LEFT JOIN users ON users.id = blog_posts.author_id
                WHERE blog_posts.slug = :slug';
        if (!$includeDraft) {
            $sql .= ' AND blog_posts.status = "published"';
        }
        $sql .= ' LIMIT 1';

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();

        return is_array($post) ? $this->mapPost($post) : null;
    }

    public function postById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare(
            'SELECT blog_posts.*, users.name AS author_name
             FROM blog_posts
             LEFT JOIN users ON users.id = blog_posts.author_id
             WHERE blog_posts.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch();

        return is_array($post) ? $this->mapPost($post) : null;
    }

    public function savePost(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $title = trim((string) ($data['title'] ?? ''));
        $slug = slugify((string) ($data['slug'] ?? $title));
        $metaTitle = trim((string) ($data['meta_title'] ?? ''));
        $metaDescription = trim((string) ($data['meta_description'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $image = trim((string) ($data['image'] ?? ''));
        $status = in_array(($data['status'] ?? 'draft'), ['draft', 'published'], true) ? (string) $data['status'] : 'draft';
        $authorId = $this->nullableInt($data['author_id'] ?? null);
        $publishedAt = null;

        if ($title === '') {
            throw new InvalidArgumentException('Blog title is required.');
        }

        if ($content === '') {
            throw new InvalidArgumentException('Blog content is required.');
        }

        if ($status === 'published') {
            $publishedAt = $data['published_at'] ?? date('Y-m-d H:i:s');
            if (!is_string($publishedAt) || $publishedAt === '') {
                $publishedAt = date('Y-m-d H:i:s');
            }
        }

        $payload = [
            'author_id' => $authorId,
            'title' => $title,
            'slug' => $slug,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'content' => $content,
            'image' => $image,
            'status' => $status,
            'published_at' => $publishedAt,
        ];

        if ($id > 0) {
            $payload['id'] = $id;
            $stmt = $this->pdo()->prepare(
                'UPDATE blog_posts
                 SET author_id = :author_id, title = :title, slug = :slug, meta_title = :meta_title,
                     meta_description = :meta_description, content = :content, image = :image,
                     status = :status, published_at = :published_at
                 WHERE id = :id'
            );
            $stmt->execute($payload);
            return;
        }

        $stmt = $this->pdo()->prepare(
            'INSERT INTO blog_posts
                (author_id, title, slug, meta_title, meta_description, content, image, status, published_at)
             VALUES
                (:author_id, :title, :slug, :meta_title, :meta_description, :content, :image, :status, :published_at)'
        );
        $stmt->execute($payload);
    }

    public function deletePost(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM blog_posts WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function pdo(): PDO
    {
        return Database::connection($this->config);
    }

    private function nullableInt(mixed $value): ?int
    {
        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    private function mapPost(array $post): array
    {
        $content = (string) ($post['content'] ?? '');
        $metaDescription = trim((string) ($post['meta_description'] ?? ''));

        return [
            'id' => (int) $post['id'],
            'author_id' => $post['author_id'],
            'author_name' => $post['author_name'] ?? 'Staff',
            'title' => $post['title'],
            'slug' => $post['slug'],
            'meta_title' => $post['meta_title'] ?? '',
            'meta_description' => $metaDescription,
            'content' => $content,
            'excerpt' => $metaDescription !== '' ? $metaDescription : $this->excerpt($content),
            'image' => $post['image'] ?? '',
            'status' => $post['status'] ?? 'draft',
            'published_at' => $post['published_at'] ?? '',
            'created_at' => $post['created_at'] ?? '',
            'updated_at' => $post['updated_at'] ?? '',
            'download_url' => url('/blog/' . $post['slug']),
        ];
    }

    private function excerpt(string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', strip_tags($value)) ?? '');
        if (strlen($value) <= 160) {
            return $value;
        }

        return rtrim(substr($value, 0, 157)) . '...';
    }
}
