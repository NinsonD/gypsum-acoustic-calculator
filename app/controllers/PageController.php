<?php

declare(strict_types=1);

final class PageController extends Controller
{
    private ContentRepository $content;
    private ProductRepository $products;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->content = new ContentRepository();
        $this->products = new ProductRepository($config);
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
        $this->view('pages/downloads', [
            'title' => 'Download Center',
            'description' => 'Technical resources, method statements, BOQ templates, and CAD placeholders.',
            'downloads' => $this->content->downloads(),
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
}
