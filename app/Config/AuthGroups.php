<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Kontrol penuh terhadap seluruh sistem.',
        ],
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Administrator harian sistem.',
        ],
        'manager' => [
            'title'       => 'Manager',
            'description' => 'Manajer yang dapat melihat laporan dan mengelola data.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'Pengguna umum dengan akses terbatas.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     */
    public array $permissions = [
        // Admin area
        'admin.access'        => 'Dapat mengakses area admin',
        'admin.settings'      => 'Dapat mengakses pengaturan sistem',

        // User management
        'users.list'          => 'Dapat melihat daftar pengguna',
        'users.create'        => 'Dapat membuat pengguna baru',
        'users.edit'          => 'Dapat mengedit pengguna',
        'users.delete'        => 'Dapat menghapus pengguna',
        'users.manage-roles'  => 'Dapat mengatur role pengguna',

        // Role management
        'roles.list'          => 'Dapat melihat daftar role',
        'roles.create'        => 'Dapat membuat role baru',
        'roles.edit'          => 'Dapat mengedit role',
        'roles.delete'        => 'Dapat menghapus role',

        // Dashboard
        'dashboard.access'    => 'Dapat mengakses dashboard',
        'dashboard.stats'     => 'Dapat melihat statistik',

        // Reports
        'reports.view'        => 'Dapat melihat laporan',
        'reports.export'      => 'Dapat mengekspor laporan',

        // Periode Management
        'periods.list'        => 'Dapat melihat daftar periode',
        'periods.create'      => 'Dapat membuat periode baru',
        'periods.edit'        => 'Dapat mengedit periode',
        'periods.delete'      => 'Dapat menghapus periode',

        // Website Management
        'websites.list'       => 'Dapat melihat daftar website prodi',
        'websites.create'     => 'Dapat menambah website prodi',
        'websites.edit'       => 'Dapat mengedit website prodi',
        'websites.delete'     => 'Dapat menghapus website prodi',

        // Performance Data
        'performance.list'    => 'Dapat melihat data performansi',
        'performance.input'   => 'Dapat menginput data performansi',
        'performance.edit'    => 'Dapat mengedit data performansi',
        'performance.delete'  => 'Dapat menghapus data performansi',

        // Performance Dashboard
        'performance.dashboard' => 'Dapat melihat dashboard performansi',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'roles.*',
            'dashboard.*',
            'reports.*',
            'periods.*',
            'websites.*',
            'performance.*',
        ],
        'admin' => [
            'admin.access',
            'users.list',
            'users.create',
            'users.edit',
            'users.delete',
            'dashboard.*',
            'reports.*',
            'periods.*',
            'websites.*',
            'performance.*',
        ],
        'manager' => [
            'admin.access',
            'users.list',
            'dashboard.*',
            'reports.*',
            'periods.list',
            'websites.list',
            'performance.list',
            'performance.dashboard',
        ],
        'user' => [
            'dashboard.access',
            'performance.dashboard',
        ],
    ];
}
