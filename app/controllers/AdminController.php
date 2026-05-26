<?php

declare(strict_types=1);

final class AdminController extends Controller
{
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
}
