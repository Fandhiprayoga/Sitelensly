<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ---------------------------------------------------------------
// Auth Routes (Shield)
// ---------------------------------------------------------------
service('auth')->routes($routes);

// ---------------------------------------------------------------
// Public Routes
// ---------------------------------------------------------------
$routes->get('/', 'AuthController::login');
$routes->get('maintenance', static function () {
    return view('errors/maintenance');
});

// ---------------------------------------------------------------
// Protected Routes (require login)
// ---------------------------------------------------------------
$routes->group('', ['filter' => 'session'], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Dashboard Performansi (accessible by all logged-in users with permission)
    $routes->get('performance-dashboard', 'PerformanceDashboardController::index');

    // Switch Active Group
    $routes->post('switch-group', 'GroupSwitchController::switch');

    // Profile
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');

    // ---------------------------------------------------------------
    // Admin Routes (require admin.access permission)
    // ---------------------------------------------------------------
    $routes->group('admin', ['filter' => 'permission:admin.access'], static function ($routes) {

        // User Management
        $routes->group('users', static function ($routes) {
            $routes->get('/', 'UserController::index', ['filter' => 'permission:users.list']);
            $routes->get('create', 'UserController::create', ['filter' => 'permission:users.create']);
            $routes->post('store', 'UserController::store', ['filter' => 'permission:users.create']);
            $routes->get('edit/(:num)', 'UserController::edit/$1', ['filter' => 'permission:users.edit']);
            $routes->post('update/(:num)', 'UserController::update/$1', ['filter' => 'permission:users.edit']);
            $routes->post('delete/(:num)', 'UserController::delete/$1', ['filter' => 'permission:users.delete']);
            $routes->post('assign-role/(:num)', 'UserController::assignRole/$1', ['filter' => 'permission:users.manage-roles']);
        });

        // Role Management (superadmin only)
        $routes->group('roles', ['filter' => 'role:superadmin'], static function ($routes) {
            $routes->get('/', 'RoleController::index');
            $routes->get('permissions', 'RoleController::permissions');
        });

        // Settings
        $routes->group('settings', ['filter' => 'permission:admin.settings'], static function ($routes) {
            $routes->get('/', 'SettingController::index');
            $routes->post('update/general', 'SettingController::updateGeneral');
            $routes->post('update/auth', 'SettingController::updateAuth');
            $routes->post('update/mail', 'SettingController::updateMail');
        });

        // Manajemen Periode
        $routes->group('periods', static function ($routes) {
            $routes->get('/', 'PeriodController::index', ['filter' => 'permission:periods.list']);
            $routes->get('create', 'PeriodController::create', ['filter' => 'permission:periods.create']);
            $routes->post('store', 'PeriodController::store', ['filter' => 'permission:periods.create']);
            $routes->get('edit/(:num)', 'PeriodController::edit/$1', ['filter' => 'permission:periods.edit']);
            $routes->post('update/(:num)', 'PeriodController::update/$1', ['filter' => 'permission:periods.edit']);
            $routes->post('delete/(:num)', 'PeriodController::delete/$1', ['filter' => 'permission:periods.delete']);
            $routes->post('toggle-status/(:num)', 'PeriodController::toggleStatus/$1', ['filter' => 'permission:periods.edit']);
        });

        // Master Website
        $routes->group('websites', static function ($routes) {
            $routes->get('/', 'WebsiteController::index', ['filter' => 'permission:websites.list']);
            $routes->get('create', 'WebsiteController::create', ['filter' => 'permission:websites.create']);
            $routes->post('store', 'WebsiteController::store', ['filter' => 'permission:websites.create']);
            $routes->get('edit/(:num)', 'WebsiteController::edit/$1', ['filter' => 'permission:websites.edit']);
            $routes->post('update/(:num)', 'WebsiteController::update/$1', ['filter' => 'permission:websites.edit']);
            $routes->post('delete/(:num)', 'WebsiteController::delete/$1', ['filter' => 'permission:websites.delete']);
        });

        // Input Data Performansi
        $routes->group('performance', static function ($routes) {
            $routes->get('/', 'PerformanceController::index', ['filter' => 'permission:performance.list']);
            $routes->get('create', 'PerformanceController::create', ['filter' => 'permission:performance.input']);
            $routes->post('store', 'PerformanceController::store', ['filter' => 'permission:performance.input']);
            $routes->get('edit/(:num)', 'PerformanceController::edit/$1', ['filter' => 'permission:performance.edit']);
            $routes->post('update/(:num)', 'PerformanceController::update/$1', ['filter' => 'permission:performance.edit']);
            $routes->post('delete/(:num)', 'PerformanceController::delete/$1', ['filter' => 'permission:performance.delete']);
        });

        // Laporan Ringkas
        $routes->group('reports', static function ($routes) {
            $routes->get('/', 'ReportController::index', ['filter' => 'permission:reports.view']);
            $routes->get('export-csv', 'ReportController::exportCsv', ['filter' => 'permission:reports.export']);
        });
    });
});
