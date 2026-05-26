<?php

declare(strict_types=1);

final class ProductRepository
{
    public function __construct(private array $config)
    {
    }

    public function publicProducts(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT products.*, brands.name AS brand_name, brands.slug AS brand_slug, categories.name AS category_name
             FROM products
             LEFT JOIN brands ON brands.id = products.brand_id
             LEFT JOIN categories ON categories.id = products.category_id
             WHERE products.is_active = 1
             ORDER BY products.name ASC'
        );

        return array_map([$this, 'mapProduct'], $stmt->fetchAll());
    }

    public function adminProducts(): array
    {
        $stmt = $this->pdo()->query(
            'SELECT products.*, brands.name AS brand_name, categories.name AS category_name
             FROM products
             LEFT JOIN brands ON brands.id = products.brand_id
             LEFT JOIN categories ON categories.id = products.category_id
             ORDER BY products.updated_at DESC, products.created_at DESC, products.name ASC'
        );

        return array_map([$this, 'mapProduct'], $stmt->fetchAll());
    }

    public function productBySlug(string $slug): ?array
    {
        $stmt = $this->pdo()->prepare(
            'SELECT products.*, brands.name AS brand_name, brands.slug AS brand_slug, categories.name AS category_name
             FROM products
             LEFT JOIN brands ON brands.id = products.brand_id
             LEFT JOIN categories ON categories.id = products.category_id
             WHERE products.slug = :slug AND products.is_active = 1
             LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch();

        return is_array($product) ? $this->mapProduct($product) : null;
    }

    public function productById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        if (!is_array($product)) {
            return null;
        }

        $product['specs_text'] = $this->specsToText($this->decodeSpecs($product['specifications'] ?? null));

        return $product;
    }

    public function brands(): array
    {
        $stmt = $this->pdo()->query('SELECT * FROM brands ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function brandById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM brands WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $brand = $stmt->fetch();

        return is_array($brand) ? $brand : null;
    }

    public function categories(): array
    {
        $stmt = $this->pdo()->query('SELECT * FROM categories ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function categoryById(int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch();

        return is_array($category) ? $category : null;
    }

    public function saveBrand(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $slug = slugify((string) ($data['slug'] ?? $name));
        $description = trim((string) ($data['description'] ?? ''));

        if ($name === '') {
            throw new InvalidArgumentException('Brand name is required.');
        }

        if ($id > 0) {
            $stmt = $this->pdo()->prepare('UPDATE brands SET name = :name, slug = :slug, description = :description WHERE id = :id');
            $stmt->execute(['id' => $id, 'name' => $name, 'slug' => $slug, 'description' => $description]);
            return;
        }

        $stmt = $this->pdo()->prepare('INSERT INTO brands (name, slug, description) VALUES (:name, :slug, :description)');
        $stmt->execute(['name' => $name, 'slug' => $slug, 'description' => $description]);
    }

    public function deleteBrand(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM brands WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function saveCategory(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $slug = slugify((string) ($data['slug'] ?? $name));

        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }

        if ($id > 0) {
            $stmt = $this->pdo()->prepare('UPDATE categories SET name = :name, slug = :slug WHERE id = :id');
            $stmt->execute(['id' => $id, 'name' => $name, 'slug' => $slug]);
            return;
        }

        $stmt = $this->pdo()->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug)');
        $stmt->execute(['name' => $name, 'slug' => $slug]);
    }

    public function deleteCategory(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function saveProduct(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $name = trim((string) ($data['name'] ?? ''));
        $slug = slugify((string) ($data['slug'] ?? $name));

        if ($name === '') {
            throw new InvalidArgumentException('Product name is required.');
        }

        $payload = [
            'brand_id' => $this->nullableInt($data['brand_id'] ?? null),
            'category_id' => $this->nullableInt($data['category_id'] ?? null),
            'name' => $name,
            'slug' => $slug,
            'short_description' => trim((string) ($data['short_description'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'specifications' => json_encode($this->parseSpecs((string) ($data['specs_text'] ?? '')), JSON_UNESCAPED_SLASHES),
            'nrc_value' => trim((string) ($data['nrc_value'] ?? '')),
            'stc_value' => trim((string) ($data['stc_value'] ?? '')),
            'fire_rating' => trim((string) ($data['fire_rating'] ?? '')),
            'image' => trim((string) ($data['image'] ?? '')),
            'is_active' => isset($data['is_active']) ? 1 : 0,
        ];

        if ($id > 0) {
            $payload['id'] = $id;
            $stmt = $this->pdo()->prepare(
                'UPDATE products
                 SET brand_id = :brand_id, category_id = :category_id, name = :name, slug = :slug,
                     short_description = :short_description, description = :description, specifications = :specifications,
                     nrc_value = :nrc_value, stc_value = :stc_value, fire_rating = :fire_rating,
                     image = :image, is_active = :is_active
                 WHERE id = :id'
            );
            $stmt->execute($payload);
            return;
        }

        $stmt = $this->pdo()->prepare(
            'INSERT INTO products
                (brand_id, category_id, name, slug, short_description, description, specifications, nrc_value, stc_value, fire_rating, image, is_active)
             VALUES
                (:brand_id, :category_id, :name, :slug, :short_description, :description, :specifications, :nrc_value, :stc_value, :fire_rating, :image, :is_active)'
        );
        $stmt->execute($payload);
    }

    public function deleteProduct(int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM products WHERE id = :id');
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

    private function parseSpecs(string $text): array
    {
        $specs = [];
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $parts = preg_split('/[:=]/', $line, 2);

            if (!is_array($parts) || count($parts) < 2) {
                $specs[$line] = '';
                continue;
            }

            $specs[trim($parts[0])] = trim($parts[1]);
        }

        return $specs;
    }

    private function specsToText(array $specs): string
    {
        $lines = [];

        foreach ($specs as $label => $value) {
            $lines[] = $label . ': ' . $value;
        }

        return implode(PHP_EOL, $lines);
    }

    private function mapProduct(array $product): array
    {
        $specs = $this->decodeSpecs($product['specifications'] ?? null);

        return [
            'id' => (int) $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'brand_id' => $product['brand_id'],
            'brand' => [
                'name' => $product['brand_name'] ?? 'Manufacturer',
                'slug' => $product['brand_slug'] ?? '',
            ],
            'category_id' => $product['category_id'],
            'category' => $product['category_name'] ?? 'Uncategorized',
            'summary' => $product['short_description'] ?: $this->excerpt((string) $product['description']),
            'description' => $product['description'] ?: ($product['short_description'] ?? ''),
            'nrc' => $product['nrc_value'] ?: 'N/A',
            'stc' => $product['stc_value'] ?: 'N/A',
            'fire_rating' => $product['fire_rating'] ?: 'Verify assembly',
            'image' => $product['image'] ?? '',
            'is_active' => (int) ($product['is_active'] ?? 0),
            'specs' => $specs,
            'specs_text' => $this->specsToText($specs),
        ];
    }

    private function decodeSpecs(mixed $value): array
    {
        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function excerpt(string $value): string
    {
        $value = trim($value);

        if (strlen($value) <= 150) {
            return $value;
        }

        return rtrim(substr($value, 0, 147)) . '...';
    }
}
