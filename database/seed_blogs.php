<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This script must be run from the command line.\n";
    exit(1);
}

$config = require dirname(__DIR__) . '/app/bootstrap.php';
$pdo = Database::connection($config);

$authorId = $pdo->query('SELECT id FROM users ORDER BY id ASC LIMIT 1')->fetchColumn();
$authorId = is_numeric($authorId) ? (int) $authorId : null;

$posts = [
    [
        'title' => 'Gypsum Ceiling UAE: How to Choose the Right System',
        'slug' => 'gypsum-ceiling-uae-how-to-choose-the-right-system',
        'meta_title' => 'Gypsum Ceiling UAE: Choosing the Right System',
        'meta_description' => 'A practical guide to gypsum ceiling systems, board selection, and framing decisions for UAE projects.',
        'content' => "Gypsum ceiling selection should start with the project use case.\n\nFor offices and retail, standard gypsum ceilings work well. For auditoriums, meeting rooms, and schools, perforated acoustic systems or mineral fiber tiles are often a better fit.\n\nKey factors include moisture exposure, fire rating, access requirements, and the amount of acoustic absorption needed.",
        'status' => 'published',
    ],
    [
        'title' => 'Drywall Partition Dubai: What Contractors Need to Check',
        'slug' => 'drywall-partition-dubai-what-contractors-need-to-check',
        'meta_title' => 'Drywall Partition Dubai Guide',
        'meta_description' => 'Checklist for drywall partitions in Dubai projects covering studs, boards, rockwool, sealing, and STC considerations.',
        'content' => "A good drywall partition is more than metal studs and boards.\n\nYou need to check board layers, cavity insulation, perimeter sealing, service penetrations, and the target STC before finalizing the build-up.\n\nIf the wall is for a clinic, cinema, or private office, acoustic sealing becomes just as important as the board specification.",
        'status' => 'published',
    ],
    [
        'title' => 'Acoustic Ceiling Sharjah: NRC and Echo Control Basics',
        'slug' => 'acoustic-ceiling-sharjah-nrc-and-echo-control-basics',
        'meta_title' => 'Acoustic Ceiling Sharjah: NRC Basics',
        'meta_description' => 'Simple explanation of NRC, echo control, and ceiling material choices for acoustic projects in Sharjah.',
        'content' => "Acoustic performance depends on absorption, layout, and room geometry.\n\nHigher NRC materials reduce reflected sound and help control echo in classrooms, offices, mosques, restaurants, and hospitality spaces.\n\nThe right answer is rarely just one product. It is usually a combination of ceiling absorption, wall treatment, and room-specific detailing.",
        'status' => 'published',
    ],
];

$stmt = $pdo->prepare(
    'INSERT INTO blog_posts (author_id, title, slug, meta_title, meta_description, content, status, published_at)
     VALUES (:author_id, :title, :slug, :meta_title, :meta_description, :content, :status, :published_at)
     ON DUPLICATE KEY UPDATE
        author_id = VALUES(author_id),
        title = VALUES(title),
        meta_title = VALUES(meta_title),
        meta_description = VALUES(meta_description),
        content = VALUES(content),
        status = VALUES(status),
        published_at = VALUES(published_at)'
);

foreach ($posts as $post) {
    $stmt->execute([
        'author_id' => $authorId,
        'title' => $post['title'],
        'slug' => $post['slug'],
        'meta_title' => $post['meta_title'],
        'meta_description' => $post['meta_description'],
        'content' => $post['content'],
        'status' => $post['status'],
        'published_at' => date('Y-m-d H:i:s'),
    ]);
}

echo "Blog seed complete: " . count($posts) . " posts.\n";
