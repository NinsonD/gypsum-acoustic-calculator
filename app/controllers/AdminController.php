<?php

declare(strict_types=1);

final class AdminController extends Controller
{
    private ProductRepository $products;
    private DownloadRepository $downloads;
    private BlogRepository $blogs;
    private UserRepository $users;
    private FileStorageService $files;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->products = new ProductRepository($config);
        $this->downloads = new DownloadRepository($config);
        $this->blogs = new BlogRepository($config);
        $this->users = new UserRepository($config);
        $this->files = new FileStorageService();
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
        Auth::requirePermission($this->config, 'products');

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
        Auth::requirePermission($this->config, 'products');
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
        Auth::requirePermission($this->config, 'products');
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
        Auth::requirePermission($this->config, 'products');

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
        Auth::requirePermission($this->config, 'products');
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
        Auth::requirePermission($this->config, 'products');
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
        Auth::requirePermission($this->config, 'products');

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
        Auth::requirePermission($this->config, 'products');

        $this->productForm(null);
    }

    public function editProduct(string $id): void
    {
        Auth::requirePermission($this->config, 'products');

        $product = $this->products->productById((int) $id);

        if ($product === null) {
            flash('error', 'Product not found.');
            redirect_to('/admin/products');
        }

        $this->productForm($product);
    }

    public function saveProduct(): void
    {
        Auth::requirePermission($this->config, 'products');
        $data = $this->requestData();
        $files = $this->requestFiles();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/products');
        }

        try {
            $existing = null;
            $id = (int) ($data['id'] ?? 0);
            if ($id > 0) {
                $existing = $this->products->productById($id);
            }

            $upload = $files['image_upload'] ?? null;
            if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $stored = $this->files->upload(
                    $upload,
                    PUBLIC_PATH,
                    'uploads/products',
                    ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                    10 * 1024 * 1024
                );
                $data['image'] = $stored['path'];
                if ($existing && !empty($existing['image'])) {
                    $this->files->delete(PUBLIC_PATH, (string) $existing['image']);
                }
            } elseif ($existing) {
                $data['image'] = (string) ($data['image'] ?? $existing['image'] ?? '');
            }

            $this->products->saveProduct($data);
            flash('success', 'Product saved.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/products');
    }

    public function deleteProduct(string $id): void
    {
        Auth::requirePermission($this->config, 'products');
        $data = $this->requestData();

        if (verify_csrf($data['_csrf'] ?? null)) {
            $product = $this->products->productById((int) $id);
            if ($product !== null && !empty($product['image'])) {
                $this->files->delete(PUBLIC_PATH, (string) $product['image']);
            }
            $this->products->deleteProduct((int) $id);
            flash('success', 'Product deleted.');
        } else {
            flash('error', 'Security token expired. Please try again.');
        }

        redirect_to('/admin/products');
    }

    public function downloads(): void
    {
        Auth::requirePermission($this->config, 'downloads');

        $this->view('pages/admin-downloads', [
            'title' => 'Downloads',
            'description' => 'Manage downloadable technical files and BOQ resources.',
            'user' => Auth::user($this->config),
            'downloads' => $this->downloads->adminDownloads(),
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function blogs(): void
    {
        Auth::requirePermission($this->config, 'blog');

        $this->view('pages/admin-blogs', [
            'title' => 'Blogs',
            'description' => 'Manage SEO articles, guides, and technical blog posts.',
            'user' => Auth::user($this->config),
            'posts' => $this->blogs->adminPosts(),
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function createBlog(): void
    {
        Auth::requirePermission($this->config, 'blog');
        $this->blogForm(null);
    }

    public function editBlog(string $id): void
    {
        Auth::requirePermission($this->config, 'blog');

        $post = $this->blogs->postById((int) $id);
        if ($post === null) {
            flash('error', 'Blog post not found.');
            redirect_to('/admin/blogs');
        }

        $this->blogForm($post);
    }

    public function saveBlog(): void
    {
        Auth::requirePermission($this->config, 'blog');
        $data = $this->requestData();
        $files = $this->requestFiles();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/blogs');
        }

        try {
            $existing = null;
            $id = (int) ($data['id'] ?? 0);
            if ($id > 0) {
                $existing = $this->blogs->postById($id);
            }

            $upload = $files['image_upload'] ?? null;
            if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $stored = $this->files->upload(
                    $upload,
                    PUBLIC_PATH,
                    'uploads/blogs',
                    ['jpg', 'jpeg', 'png', 'webp', 'gif'],
                    10 * 1024 * 1024
                );
                $data['image'] = $stored['path'];
                if ($existing && !empty($existing['image'])) {
                    $this->files->delete(PUBLIC_PATH, (string) $existing['image']);
                }
            } elseif ($existing) {
                $data['image'] = (string) ($data['image'] ?? $existing['image'] ?? '');
            }

            $currentUser = Auth::user($this->config);
            if (!isset($data['author_id']) || trim((string) $data['author_id']) === '') {
                $data['author_id'] = $currentUser['id'] ?? null;
            }

            $this->blogs->savePost($data);
            flash('success', 'Blog post saved.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/blogs');
    }

    public function deleteBlog(string $id): void
    {
        Auth::requirePermission($this->config, 'blog');
        $data = $this->requestData();

        if (verify_csrf($data['_csrf'] ?? null)) {
            $post = $this->blogs->postById((int) $id);
            if ($post !== null && !empty($post['image'])) {
                $this->files->delete(PUBLIC_PATH, (string) $post['image']);
            }
            $this->blogs->deletePost((int) $id);
            flash('success', 'Blog post deleted.');
        } else {
            flash('error', 'Security token expired. Please try again.');
        }

        redirect_to('/admin/blogs');
    }

    public function createDownload(): void
    {
        Auth::requirePermission($this->config, 'downloads');
        $this->downloadForm(null);
    }

    public function editDownload(string $id): void
    {
        Auth::requirePermission($this->config, 'downloads');

        $download = $this->downloads->downloadById((int) $id);
        if ($download === null) {
            flash('error', 'Download not found.');
            redirect_to('/admin/downloads');
        }

        $this->downloadForm($download);
    }

    public function saveDownload(): void
    {
        Auth::requirePermission($this->config, 'downloads');
        $data = $this->requestData();
        $files = $this->requestFiles();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/downloads');
        }

        try {
            $existing = null;
            $id = (int) ($data['id'] ?? 0);
            if ($id > 0) {
                $existing = $this->downloads->downloadById($id);
            }

            $upload = $files['file_upload'] ?? null;
            if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $stored = $this->files->upload(
                    $upload,
                    PUBLIC_PATH,
                    'uploads/downloads',
                    ['pdf', 'xlsx', 'xls', 'docx', 'zip', 'dwg', 'jpg', 'jpeg', 'png'],
                    25 * 1024 * 1024
                );
                $data['file_path'] = $stored['path'];
                $data['file_type'] = strtoupper((string) $stored['extension']);
                if ($existing && !empty($existing['file_path'])) {
                    $this->files->delete(STORAGE_PATH, (string) $existing['file_path']);
                }
            } elseif ($existing) {
                $data['file_path'] = $existing['file_path'];
                $data['file_type'] = $existing['file_type'];
            }

            $this->downloads->saveDownload($data);
            flash('success', 'Download saved.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/downloads');
    }

    public function deleteDownload(string $id): void
    {
        Auth::requirePermission($this->config, 'downloads');
        $data = $this->requestData();

        if (verify_csrf($data['_csrf'] ?? null)) {
            $download = $this->downloads->downloadById((int) $id);
            if ($download !== null && !empty($download['file_path'])) {
                $this->files->delete(PUBLIC_PATH, (string) $download['file_path']);
            }
            $this->downloads->deleteDownload((int) $id);
            flash('success', 'Download deleted.');
        } else {
            flash('error', 'Security token expired. Please try again.');
        }

        redirect_to('/admin/downloads');
    }

    public function users(): void
    {
        Auth::requirePermission($this->config, 'users');

        $this->view('pages/admin-users', [
            'title' => 'Users',
            'description' => 'Manage admin, editor, and contractor accounts.',
            'user' => Auth::user($this->config),
            'users' => $this->users->users(),
            'roles' => $this->users->roles(),
            'success' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function createUser(): void
    {
        Auth::requirePermission($this->config, 'users');
        $this->userForm(null);
    }

    public function editUser(string $id): void
    {
        Auth::requirePermission($this->config, 'users');

        $user = $this->users->userById((int) $id);
        if ($user === null) {
            flash('error', 'User not found.');
            redirect_to('/admin/users');
        }

        $this->userForm($user);
    }

    public function saveUser(): void
    {
        Auth::requirePermission($this->config, 'users');
        $data = $this->requestData();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/users');
        }

        try {
            $this->users->saveUser($data);
            flash('success', 'User saved.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/users');
    }

    public function deleteUser(string $id): void
    {
        Auth::requirePermission($this->config, 'users');
        $data = $this->requestData();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/users');
        }

        $currentUser = Auth::user($this->config);
        if ((int) ($currentUser['id'] ?? 0) === (int) $id) {
            flash('error', 'You cannot delete your own account.');
            redirect_to('/admin/users');
        }

        $this->users->deleteUser((int) $id);
        flash('success', 'User deleted.');

        redirect_to('/admin/users');
    }

    public function inquiries(): void
    {
        Auth::requirePermission($this->config, 'leads');

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

    public function updateInquiryStatus(string $id): void
    {
        Auth::requirePermission($this->config, 'leads');
        $data = $this->requestData();

        if (!verify_csrf($data['_csrf'] ?? null)) {
            flash('error', 'Security token expired. Please try again.');
            redirect_to('/admin/inquiries');
        }

        $status = strtolower(trim((string) ($data['status'] ?? 'new')));
        $allowed = ['new', 'contacted', 'quoted', 'closed'];

        if (!in_array($status, $allowed, true)) {
            flash('error', 'Invalid inquiry status.');
            redirect_to('/admin/inquiries');
        }

        try {
            $stmt = $this->db()->prepare('UPDATE inquiries SET status = :status WHERE id = :id');
            $stmt->execute([
                'status' => $status,
                'id' => (int) $id,
            ]);
            flash('success', 'Inquiry status updated.');
        } catch (Throwable $error) {
            flash('error', $error->getMessage());
        }

        redirect_to('/admin/inquiries');
    }

    public function boqs(): void
    {
        Auth::requirePermission($this->config, 'leads');

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
        Auth::requirePermission($this->config, 'leads');

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
            'blogs' => (int) $pdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn(),
            'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
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

    private function downloadForm(?array $download): void
    {
        $this->view('pages/admin-download-form', [
            'title' => $download === null ? 'Create Download' : 'Edit Download',
            'description' => 'Upload downloadable technical resources.',
            'user' => Auth::user($this->config),
            'download' => $download,
            'error' => flash('error'),
        ]);
    }

    private function blogForm(?array $post): void
    {
        $this->view('pages/admin-blog-form', [
            'title' => $post === null ? 'Create Blog Post' : 'Edit Blog Post',
            'description' => 'Create SEO articles and technical content.',
            'user' => Auth::user($this->config),
            'post' => $post,
            'error' => flash('error'),
        ]);
    }

    private function userForm(?array $user): void
    {
        $this->view('pages/admin-user-form', [
            'title' => $user === null ? 'Create User' : 'Edit User',
            'description' => 'Create admin, editor, or contractor accounts.',
            'user' => Auth::user($this->config),
            'account' => $user,
            'roles' => $this->users->roles(),
            'error' => flash('error'),
        ]);
    }
}
