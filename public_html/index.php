<?php

declare(strict_types=1);

$config = require dirname(__DIR__) . '/app/bootstrap.php';

$router = new Router($config);

$router->get('/', [PageController::class, 'home']);
$router->get('/calculators', [PageController::class, 'calculators']);
$router->get('/products', [PageController::class, 'products']);
$router->get('/products/{slug}', [PageController::class, 'productDetail']);
$router->get('/blog', [PageController::class, 'blogIndex']);
$router->get('/blog/{slug}', [PageController::class, 'blogDetail']);
$router->get('/installation', [PageController::class, 'installation']);
$router->get('/knowledge', [PageController::class, 'knowledge']);
$router->get('/downloads', [PageController::class, 'downloads']);
$router->get('/downloads/{slug}', [PageController::class, 'downloadFile']);
$router->get('/contact', [PageController::class, 'contact']);
$router->get('/admin/login', [AdminController::class, 'login']);
$router->post('/admin/login', [AdminController::class, 'authenticate']);
$router->post('/admin/logout', [AdminController::class, 'logout']);
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/inquiries', [AdminController::class, 'inquiries']);
$router->get('/admin/boqs', [AdminController::class, 'boqs']);
$router->get('/admin/brands', [AdminController::class, 'brands']);
$router->post('/admin/brands/save', [AdminController::class, 'saveBrand']);
$router->post('/admin/brands/{id}/delete', [AdminController::class, 'deleteBrand']);
$router->get('/admin/categories', [AdminController::class, 'categories']);
$router->post('/admin/categories/save', [AdminController::class, 'saveCategory']);
$router->post('/admin/categories/{id}/delete', [AdminController::class, 'deleteCategory']);
$router->get('/admin/products', [AdminController::class, 'products']);
$router->get('/admin/products/create', [AdminController::class, 'createProduct']);
$router->get('/admin/products/{id}/edit', [AdminController::class, 'editProduct']);
$router->post('/admin/products/save', [AdminController::class, 'saveProduct']);
$router->post('/admin/products/{id}/delete', [AdminController::class, 'deleteProduct']);
$router->get('/admin/blogs', [AdminController::class, 'blogs']);
$router->get('/admin/blogs/create', [AdminController::class, 'createBlog']);
$router->get('/admin/blogs/{id}/edit', [AdminController::class, 'editBlog']);
$router->post('/admin/blogs/save', [AdminController::class, 'saveBlog']);
$router->post('/admin/blogs/{id}/delete', [AdminController::class, 'deleteBlog']);
$router->get('/admin/downloads', [AdminController::class, 'downloads']);
$router->get('/admin/downloads/create', [AdminController::class, 'createDownload']);
$router->get('/admin/downloads/{id}/edit', [AdminController::class, 'editDownload']);
$router->post('/admin/downloads/save', [AdminController::class, 'saveDownload']);
$router->post('/admin/downloads/{id}/delete', [AdminController::class, 'deleteDownload']);
$router->get('/admin/boqs/{id}/export/{format}', [AdminController::class, 'exportBoq']);

$router->post('/api/boq', [CalculatorController::class, 'boq']);
$router->post('/api/boq/export', [CalculatorController::class, 'export']);
$router->post('/api/inquiries', [InquiryController::class, 'store']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
