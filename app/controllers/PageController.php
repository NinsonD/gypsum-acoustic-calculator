<?php

declare(strict_types=1);

final class PageController extends Controller
{
    private ContentRepository $content;
    private ProductRepository $products;
    private DownloadRepository $downloads;
    private BlogRepository $blogs;
    private GalleryRepository $gallery;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->content = new ContentRepository();
        $this->products = new ProductRepository($config);
        $this->downloads = new DownloadRepository($config);
        $this->blogs = new BlogRepository($config);
        $this->gallery = new GalleryRepository($config);
    }

    public function home(): void
    {
        $this->view('pages/home', [
            'title' => 'Gypsum & Acoustic Engineering Systems',
            'description' => 'Gypsum ceiling, drywall partition, acoustic calculator, BOQ, and contractor lead platform.',
            'products' => array_slice($this->catalogProducts(), 0, 3),
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Gypsum & Acoustic Engineering Systems',
                'description' => 'Gypsum ceiling, drywall partition, acoustic calculator, BOQ, and contractor lead platform.',
                'url' => url('/'),
            ],
        ]);
    }

    public function calculators(): void
    {
        $this->view('pages/calculators', [
            'title' => 'Material Calculators',
            'description' => 'Gypsum ceiling, drywall partition, ceiling tile, and acoustic material calculators.',
        ]);
    }

    public function products(): void
    {
        $this->view('pages/products', [
            'title' => 'Product Catalog',
            'description' => 'Gypsum, drywall, acoustic, insulation, and soundproofing system catalog.',
            'products' => $this->catalogProducts(),
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Product Catalog',
                'url' => url('/products'),
            ],
        ]);
    }

    public function productDetail(string $slug): void
    {
        $product = null;

        try {
            $product = $this->products->productBySlug($slug);
        } catch (Throwable) {
            $product = $this->content->productBySlug($slug);
        }

        if ($product === null) {
            http_response_code(404);
            $this->notFound();
            return;
        }

        $this->view('pages/product-detail', [
            'title' => $product['name'],
            'description' => $product['summary'],
            'product' => $product,
            'og_image' => !empty($product['image']) ? url('/' . ltrim((string) $product['image'], '/')) : asset('images/og-default.svg'),
            'og_type' => 'product',
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $product['name'],
                'description' => $product['description'] ?: $product['summary'],
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $product['brand']['name'] ?? 'Manufacturer',
                ],
                'category' => $product['category'],
                'image' => !empty($product['image']) ? [url('/' . ltrim((string) $product['image'], '/'))] : [],
            ],
        ]);
    }

    public function installation(): void
    {
        $this->view('pages/installation', [
            'title' => 'Installation Tutorials',
            'description' => 'Gypsum ceiling, drywall partition, and acoustic installation tutorials.',
            'tutorials' => $this->content->tutorials(),
        ]);
    }

    public function knowledge(): void
    {
        $this->view('pages/knowledge', [
            'title' => 'Acoustic Knowledge Center',
            'description' => 'NRC, STC, reverberation, echo reduction, and soundproofing guidance.',
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Acoustic Knowledge Center',
                'url' => url('/knowledge'),
            ],
        ]);
    }

    public function gallery(): void
    {
        $items = [];
        try {
            $items = $this->gallery->publicItems();
        } catch (Throwable) {
            $items = [];
        }

        $this->view('pages/gallery', [
            'title' => 'Project Gallery',
            'description' => 'Installed gypsum, drywall, acoustic, and technical project reference images.',
            'items' => $items,
            'og_image' => !empty($items[0]['image_url']) ? $items[0]['image_url'] : asset('images/og-default.svg'),
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'ImageGallery',
                'name' => 'Project Gallery',
                'url' => url('/gallery'),
            ],
        ]);
    }

    public function downloads(): void
    {
        $downloads = [];

        try {
            $downloads = $this->downloads->publicDownloads();
        } catch (Throwable) {
            $downloads = [];
        }

        if (count($downloads) === 0) {
            $downloads = array_map(function (array $resource): array {
                return [
                    'title' => $resource['title'],
                    'type' => $resource['type'],
                    'summary' => $resource['summary'],
                    'download_url' => '',
                ];
            }, $this->content->downloads());
        }

        $this->view('pages/downloads', [
            'title' => 'Download Center',
            'description' => 'Technical resources, method statements, BOQ templates, and CAD placeholders.',
            'downloads' => $downloads,
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Download Center',
                'url' => url('/downloads'),
            ],
        ]);
    }

    public function downloadFile(string $slug): void
    {
        $download = $this->downloads->downloadBySlug($slug);

        if ($download === null) {
            http_response_code(404);
            $this->notFound();
            return;
        }

        $path = PUBLIC_PATH . '/' . ltrim((string) $download['file_path'], '/\\');
        if (!is_file($path)) {
            http_response_code(404);
            $this->notFound();
            return;
        }

        $mime = $this->mimeType($path, (string) ($download['file_type'] ?? ''));
        $filename = basename($path);

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }

    public function blogIndex(): void
    {
        $posts = [];
        try {
            $posts = $this->blogs->publicPosts();
        } catch (Throwable) {
            $posts = [];
        }

        $this->view('pages/blog-index', [
            'title' => 'Blog',
            'description' => 'SEO articles for gypsum ceiling UAE, drywall partition Dubai, acoustic ceilings, and soundproofing.',
            'posts' => $posts,
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => 'Blog',
                'url' => url('/blog'),
            ],
        ]);
    }

    public function blogDetail(string $slug): void
    {
        $post = null;

        try {
            $post = $this->blogs->postBySlug($slug);
        } catch (Throwable) {
            $post = null;
        }

        if ($post === null) {
            http_response_code(404);
            $this->notFound();
            return;
        }

        $this->view('pages/blog-detail', [
            'title' => $post['meta_title'] ?: $post['title'],
            'description' => $post['meta_description'] ?: $post['excerpt'],
            'post' => $post,
            'og_image' => !empty($post['image']) ? url('/' . ltrim((string) $post['image'], '/')) : asset('images/og-default.svg'),
            'og_type' => 'article',
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $post['meta_title'] ?: $post['title'],
                'description' => $post['meta_description'] ?: $post['excerpt'],
                'author' => [
                    '@type' => 'Person',
                    'name' => $post['author_name'] ?? 'Staff',
                ],
                'image' => !empty($post['image']) ? [url('/' . ltrim((string) $post['image'], '/'))] : [],
                'mainEntityOfPage' => url('/blog/' . $post['slug']),
            ],
        ]);
    }

    public function contact(): void
    {
        $this->view('pages/contact', [
            'title' => 'Contact',
            'description' => 'Submit a gypsum, drywall, acoustic, or BOQ inquiry.',
        ]);
    }

    public function admin(): void
    {
        $this->view('pages/admin', [
            'title' => 'Admin Modules',
            'description' => 'Future protected admin dashboard structure.',
        ]);
    }

    public function notFound(): void
    {
        $this->view('pages/404', [
            'title' => 'Page Not Found',
            'description' => 'The requested page could not be found.',
        ]);
    }

    private function catalogProducts(): array
    {
        try {
            $products = $this->products->publicProducts();

            if (count($products) > 0) {
                return $products;
            }
        } catch (Throwable) {
        }

        return array_map(function (array $product): array {
            $product['brand'] = $this->content->brandById($product['brand_id']);
            return $product;
        }, $this->content->products());
    }

    private function mimeType(string $path, string $type): string
    {
        $map = [
            'pdf' => 'application/pdf',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'zip' => 'application/zip',
            'dwg' => 'application/octet-stream',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
        ];

        $ext = strtolower(trim($type));
        if ($ext !== '' && isset($map[$ext])) {
            return $map[$ext];
        }

        $detected = @mime_content_type($path);
        return is_string($detected) && $detected !== '' ? $detected : 'application/octet-stream';
    }
}
