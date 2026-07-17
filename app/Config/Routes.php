<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ================================================================
// ROOT — redirect ke dashboard
// ================================================================
$routes->get('/', function () {
    return redirect()->to(base_url('dashboard'));
});

// ================================================================
// AUTH — publik, tidak butuh login
// ================================================================
$routes->get('login',  'Web\AuthController::loginForm');
$routes->post('login', 'Web\AuthController::loginProcess');
$routes->get('logout', 'Web\AuthController::logout');

// ================================================================
// DASHBOARD — semua role yang sudah login boleh akses
// ================================================================
$routes->get('dashboard', 'Web\DashboardController::index', ['filter' => 'auth']);

// ================================================================
// PRODUCTS
// owner   : hanya bisa lihat index
// admin   : akses penuh (create, edit, delete, import, export)
// cashier : tidak bisa akses sama sekali
// ================================================================
$routes->get('products',              'Web\ProductController::index',         ['filter' => 'auth:admin,owner']);
$routes->get('products/export-csv',   'Web\ProductController::exportCsv',    ['filter' => 'auth:admin']);
$routes->get('products/import',       'Web\ProductController::importForm',    ['filter' => 'auth:admin']);
$routes->post('products/import',      'Web\ProductController::importProcess', ['filter' => 'auth:admin']);
$routes->get('products/create',       'Web\ProductController::create',        ['filter' => 'auth:admin']);
$routes->post('products/store',       'Web\ProductController::store',         ['filter' => 'auth:admin']);
$routes->get('products/(:num)/edit',  'Web\ProductController::edit/$1',       ['filter' => 'auth:admin']);
$routes->post('products/(:num)/update', 'Web\ProductController::update/$1',   ['filter' => 'auth:admin']);
$routes->post('products/(:num)/delete', 'Web\ProductController::delete/$1',   ['filter' => 'auth:admin']);

// ================================================================
// INVENTORI
// owner   : hanya lihat
// admin   : akses penuh (stock in/out)
// cashier : tidak bisa akses
// ================================================================
$routes->get('inventori',            'Web\InventoryController::index',          ['filter' => 'auth:admin,owner']);
$routes->get('inventori/rop',        'Web\InventoryController::rop',            ['filter' => 'auth:admin,owner']);
$routes->get('inventori/stock-in',   'Web\InventoryController::stockInForm',    ['filter' => 'auth:admin']);
$routes->post('inventori/stock-in',  'Web\InventoryController::stockInProcess', ['filter' => 'auth:admin']);
$routes->get('inventori/stock-out',  'Web\InventoryController::stockOutForm',   ['filter' => 'auth:admin']);
$routes->post('inventori/stock-out', 'Web\InventoryController::stockOutProcess',['filter' => 'auth:admin']);

// ================================================================
// POS / KASIR
// cashier : akses penuh
// admin   : boleh akses (untuk testing/supervisi)
// owner   : tidak bisa akses
// ================================================================
$routes->get('pos',                'Web\PosController::index',    ['filter' => 'auth:admin,cashier']);
$routes->get('pos/search',         'Web\PosController::search',   ['filter' => 'auth:admin,cashier']);
$routes->post('pos/checkout',      'Web\PosController::checkout', ['filter' => 'auth:admin,cashier']);
$routes->get('pos/invoice/(:num)', 'Web\PosController::invoice/$1', ['filter' => 'auth:admin,cashier']);
$routes->get('pos/products',       'Web\PosController::products', ['filter' => 'auth:admin,cashier']);

// ================================================================
// TRANSACTIONS
// owner   : bisa lihat
// admin   : bisa lihat
// cashier : tidak bisa akses riwayat transaksi
// ================================================================
$routes->get('transactions',        'Web\TransactionController::index',   ['filter' => 'auth:admin,owner']);
$routes->get('transactions/(:num)', 'Web\TransactionController::show/$1', ['filter' => 'auth:admin,owner']);

// ================================================================
// REPORTS / LAPORAN
// owner   : bisa lihat dan export
// admin   : bisa lihat dan export
// cashier : tidak bisa akses
// ================================================================
$routes->get('reports',            'Web\ReportController::index',     ['filter' => 'auth:admin,owner']);
$routes->get('reports/export-csv', 'Web\ReportController::exportCsv', ['filter' => 'auth:admin,owner']);
$routes->get('reports/export-pdf', 'Web\ReportController::exportPdf', ['filter' => 'auth:admin,owner']);

// ================================================================
// SETTINGS / PENGATURAN
// admin  : bisa akses pengaturan toko & transaksi
// owner  : bisa akses semua termasuk manajemen user
// ================================================================
$routes->get('settings',                       'Web\SettingController::index',      ['filter' => 'auth:admin,owner']);
$routes->post('settings',                      'Web\SettingController::update',     ['filter' => 'auth:admin,owner']);
$routes->post('settings/store',                'Web\SettingController::store',      ['filter' => 'auth:admin,owner']);
$routes->get('settings/users',                 'Web\SettingController::users',      ['filter' => 'auth:owner']);
$routes->post('settings/users/store',          'Web\SettingController::storeUser',  ['filter' => 'auth:owner']);
$routes->post('settings/user/store',           'Web\SettingController::storeUser',  ['filter' => 'auth:owner']);
$routes->post('settings/user/delete',          'Web\SettingController::deleteUser', ['filter' => 'auth:owner']);
$routes->post('settings/users/(:num)/toggle',  'Web\SettingController::toggleUser/$1', ['filter' => 'auth:owner']);
$routes->post('settings/backup',               'Web\SettingController::backup',     ['filter' => 'auth:owner']);

// ================================================================
// API ROUTES (JSON)
// ================================================================
$routes->get('api/products',               'Api\ProductApiController::index');
$routes->get('api/products/(:num)',        'Api\ProductApiController::show/$1');
$routes->post('api/products',              'Api\ProductApiController::create');
$routes->put('api/products/(:num)',        'Api\ProductApiController::update/$1');
$routes->delete('api/products/(:num)',     'Api\ProductApiController::delete/$1');

$routes->get('api/transactions',           'Api\TransactionApiController::index');
$routes->get('api/transactions/(:num)',    'Api\TransactionApiController::show/$1');
$routes->post('api/transactions/checkout', 'Api\TransactionApiController::checkout');

$routes->get('api/inventory/movements',    'Api\InventoryApiController::movements');
$routes->post('api/inventory/stock-in',    'Api\InventoryApiController::stockIn');
$routes->post('api/inventory/stock-out',   'Api\InventoryApiController::stockOut');

$routes->get('api/dashboard/stats',        'Api\DashboardApiController::stats');
$routes->get('api/dashboard/sales-trend',  'Api\DashboardApiController::salesTrend');
$routes->get('api/dashboard/top-products', 'Api\DashboardApiController::topProducts');
