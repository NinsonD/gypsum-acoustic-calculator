<?php

declare(strict_types=1);

final class PageController extends Controller
{
    private ContentRepository $content;
    private ProductRepository $products;
    private DownloadRepository $downloads;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->content = new ContentRepository();
        $this->products = new ProductRepository($config);
        $this->downloads = new DownloadRepository($config);
    }

    public function home(): void
    {
        $this->view('pages/home', [
            'title' => 'Gypsum & Acoustic Engineering Systems',
            'description' => 'Gypsum ceiling, drywall partition, acoustic calculator, BOQ, and contractor lead platform.',
            'products' => array_slice($this->catalogProducts(), 0, 3),
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
