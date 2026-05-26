<?php

declare(strict_types=1);

final class PageController extends Controller
{
    private ContentRepository $content;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->content = new ContentRepository();
    }

    public function home(): void
    {
        $this->view('pages/home', [
            'title' => 'Gypsum & Acoustic Engineering Systems',
            'description' => 'Gypsum ceiling, drywall partition, acoustic calculator, BOQ, and contractor lead platform.',
            'products' => array_slice($this->content->products(), 0, 3),
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
        $products = array_map(function (array $product): array {
            $product['brand'] = $this->content->brandById($product['brand_id']);
            return $product;
        }, $this->content->products());

        $this->view('pages/products', [
            'title' => 'Product Catalog',
            'description' => 'Gypsum, drywall, acoustic, insulation, and soundproofing system catalog.',
            'products' => $products,
        ]);
    }

    public function productDetail(string $slug): void
    {
        $product = $this->content->productBySlug($slug);

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
}
