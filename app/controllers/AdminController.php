<?php

declare(strict_types=1);

final class AdminController extends Controller
{
    private ProductRepository $products;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->products = new ProductRepository($config);
    }

    public function login(): void
    {
        if (Auth::check($this->config)) {
            redirect_to('/admin');
        }

        $this->view('pages/admin-login', [
            'title' => 'Admin Login',
            'description' => 'Secure admin login for the gypsum and acoustic calculator platform.',
        ]);
    }

    public function authenticate(): void
    {
        $data = $this->requestData();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            $this->view('pages/admin-login', [
                'title' => 'Admin Login',
                'description' => 'Secure admin login for the gypsum and acoustic calculator platform.',
                'error' => 'Security token expired. Please try again.',
            ]);
            return;
        }

        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if (Auth::attempt($this->config, $email, $password)) {
            redirect_to('/admin');
        }

        $this->view('pages/admin-login', [
            'title' => 'Admin Login',
            'description' => 'Secure admin login for the gypsum and acoustic calculator platform.',
            'error' => 'Invalid admin email or password.',
            'email' => $email,
        ]);
    }

    public function logout(): void
    {
        $data = $this->requestData();

        if (verify_csrf($data['_csrf'] ?? null)) {
            Auth::logout();
        }

        redirect_to('/admin/login');
    }

    public function dashboard(): void
    {
        Auth::requireAdmin($this->config);

        $this->view('pages/admin-dashboard', [
            'title' => 'Admin Dashboard',
            'description' => 'Admin dashboard for inquiries, BOQ estimates, and content modules.',
            'user' => Auth::user($this->config),
            'stats' => $this->stats(),
        ]);
    }

    public function brands(): void
    {
        Auth::requireAdmin($this->config);

        $edit = null;
        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $edit = $this->products->brandById((int) $_GET['edit']);
        }

        $this->view('pages/admin-brands', [
            'title' => 'Brands',
            'description' => 'Manage product manufacturers and system brands.',
            'user' => Auth::user($this->config),
            'brands' => $this->products->brands(),
            'edit' => $edit,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function saveBrand(): void
    {
        Auth::requireAdmin($this->config);
        $data = $this->requestData();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/brands');
        }

        try {
            $this->products->saveBrand($data);
            flash('success', 'Brand saved.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/brands');
    }

    public function deleteBrand(string $id): void
    {
        Auth::requireAdmin($this->config);
        $data = $this->requestData();

        if (verify_csrf($data['_csrf'] ?? null)) {
            $this->products->deleteBrand((int) $id);
            flash('success', 'Brand deleted.');
        } else {
            flash('error', 'Security token expired. Please try again.');
        }

        redirect_to('/admin/brands');
    }

    public function categories(): void
    {
        Auth::requireAdmin($this->config);

        $edit = null;
        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $edit = $this->products->categoryById((int) $_GET['edit']);
        }

        $this->view('pages/admin-categories', [
            'title' => 'Categories',
            'description' => 'Manage product categories for gypsum, drywall, acoustic, and insulation systems.',
            'user' => Auth::user($this->config),
            'categories' => $this->products->categories(),
            'edit' => $edit,
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function saveCategory(): void
    {
        Auth::requireAdmin($this->config);
        $data = $this->requestData();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/categories');
        }

        try {
            $this->products->saveCategory($data);
            flash('success', 'Category saved.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/categories');
    }

    public function deleteCategory(string $id): void
    {
        Auth::requireAdmin($this->config);
        $data = $this->requestData();

        if (verify_csrf($data['_csrf'] ?? null)) {
            $this->products->deleteCategory((int) $id);
            flash('success', 'Category deleted.');
        } else {
            flash('error', 'Security token expired. Please try again.');
        }

        redirect_to('/admin/categories');
    }

    public function products(): void
    {
        Auth::requireAdmin($this->config);

        $this->view('pages/admin-products', [
            'title' => 'Products',
            'description' => 'Manage public catalog product systems.',
            'user' => Auth::user($this->config),
            'products' => $this->products->adminProducts(),
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function createProduct(): void
    {
        Auth::requireAdmin($this->config);

        $this->productForm(null);
    }

    public function editProduct(string $id): void
    {
        Auth::requireAdmin($this->config);

        $product = $this->products->productById((int) $id);

        if ($product === null) {
            flash('error', 'Product not found.');
            redirect_to('/admin/products');
        }

        $this->productForm($product);
    }

    public function saveProduct(): void
    {
        Auth::requireAdmin($this->config);
        $data = $this->requestData();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/products');
        }

        try {
            $this->products->saveProduct($data);
            flash('success', 'Product saved.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/products');
    }

    public function deleteProduct(string $id): void
    {
        Auth::requireAdmin($this->config);
        $data = $this->requestData();

        if (verify_csrf($data['_csrf'] ?? null)) {
            $this->products->deleteProduct((int) $id);
            flash('success', 'Product deleted.');
        } else {
            flash('error', 'Security token expired. Please try again.');
        }

        redirect_to('/admin/products');
    }

    public function inquiries(): void
    {
        Auth::requireAdmin($this->config);

        $stmt = $this->db()->query(
            'SELECT id, reference, customer_name, email, phone, inquiry_type, message, status, created_at
             FROM inquiries
             ORDER BY created_at DESC
             LIMIT 100'
        );

        $this->view('pages/admin-inquiries', [
            'title' => 'Inquiries',
            'description' => 'Latest contractor and customer inquiries.',
            'user' => Auth::user($this->config),
            'inquiries' => $stmt->fetchAll(),
        ]);
    }

    public function boqs(): void
    {
        Auth::requireAdmin($this->config);

        $stmt = $this->db()->query(
            'SELECT id, reference, project_name, calculator_type, area, calculations_json, generated_pdf, created_at
             FROM boq_estimations
             ORDER BY created_at DESC
             LIMIT 100'
        );

        $this->view('pages/admin-boqs', [
            'title' => 'BOQ Estimates',
            'description' => 'Latest saved calculator BOQ estimates.',
            'user' => Auth::user($this->config),
            'boqs' => $stmt->fetchAll(),
        ]);
    }

    public function exportBoq(string $id, string $format): void
    {
        Auth::requireAdmin($this->config);

        $stmt = $this->db()->prepare(
            'SELECT id, reference, project_name, calculator_type, area, calculations_json, created_at
             FROM boq_estimations
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => (int) $id]);
        $boq = $stmt->fetch();

        if (!$boq) {
            http_response_code(404);
            echo 'BOQ not found.';
            return;
        }

        $payload = json_decode((string) $boq['calculations_json'], true);
        $payload = is_array($payload) ? $payload : [];
        $payload['reference'] = $boq['reference'] ?? ('BOQ-' . $boq['id']);
        $payload['project_name'] = $boq['project_name'];
        $payload['calculator_type'] = $boq['calculator_type'];
        $payload['area'] = $boq['area'];
        $payload['created_at'] = $boq['created_at'];

        $exporter = new BoqExportService();
        $file = strtolower($format) === 'pdf' ? $exporter->pdf($payload) : $exporter->csv($payload);

        header('Content-Type: ' . $file['content_type']);
        header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
        header('Content-Length: ' . strlen($file['content']));
        echo $file['content'];
    }

    private function stats(): array
    {
        $pdo = $this->db();

        return [
            'inquiries' => (int) $pdo->query('SELECT COUNT(*) FROM inquiries')->fetchColumn(),
            'boqs' => (int) $pdo->query('SELECT COUNT(*) FROM boq_estimations')->fetchColumn(),
            'products' => (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'downloads' => (int) $pdo->query('SELECT COUNT(*) FROM downloads')->fetchColumn(),
        ];
    }

    private function productForm(?array $product): void
    {
        $this->view('pages/admin-product-form', [
            'title' => $product === null ? 'Create Product' : 'Edit Product',
            'description' => 'Create or update catalog product data.',
            'user' => Auth::user($this->config),
            'product' => $product,
            'brands' => $this->products->brands(),
            'categories' => $this->products->categories(),
            'error' => flash('error'),
        ]);
    }
}
