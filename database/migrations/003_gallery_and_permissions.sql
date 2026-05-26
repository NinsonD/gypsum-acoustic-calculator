CREATE TABLE IF NOT EXISTS gallery_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    summary VARCHAR(255) NULL,
    image VARCHAR(255) NOT NULL,
    project_name VARCHAR(180) NULL,
    category VARCHAR(120) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_public TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_gallery_public_sort (is_public, sort_order, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

UPDATE roles
SET permissions = JSON_ARRAY_APPEND(permissions, '$', 'gallery')
WHERE role_name IN ('Super Admin', 'Admin', 'Editor')
  AND JSON_CONTAINS(permissions, JSON_QUOTE('gallery'), '$') = 0;

UPDATE roles
SET permissions = JSON_ARRAY_APPEND(permissions, '$', 'users')
WHERE role_name IN ('Super Admin', 'Admin')
  AND JSON_CONTAINS(permissions, JSON_QUOTE('users'), '$') = 0;
