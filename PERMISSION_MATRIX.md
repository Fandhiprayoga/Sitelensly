# Permission Matrix

> Sitelensly - Role-based Permission Management System

---

## Table of Contents

1. [Permission Categories](#permission-categories)
2. [Role Definitions](#role-definitions)
3. [Permission Matrix by Role](#permission-matrix-by-role)
4. [Detailed Permission Mapping](#detailed-permission-mapping)

---

## Permission Categories

### 1. Admin Area
| Permission | Description |
|-----------|-------------|
| `admin.access` | Dapat mengakses area admin |
| `admin.settings` | Dapat mengakses pengaturan sistem |

### 2. User Management
| Permission | Description |
|-----------|-------------|
| `users.list` | Dapat melihat daftar pengguna |
| `users.create` | Dapat membuat pengguna baru |
| `users.edit` | Dapat mengedit pengguna |
| `users.delete` | Dapat menghapus pengguna |
| `users.manage-roles` | Dapat mengatur role pengguna |

### 3. Role Management
| Permission | Description |
|-----------|-------------|
| `roles.list` | Dapat melihat daftar role |
| `roles.create` | Dapat membuat role baru |
| `roles.edit` | Dapat mengedit role |
| `roles.delete` | Dapat menghapus role |

### 4. Dashboard
| Permission | Description |
|-----------|-------------|
| `dashboard.access` | Dapat mengakses dashboard |
| `dashboard.stats` | Dapat melihat statistik |

### 5. Reports
| Permission | Description |
|-----------|-------------|
| `reports.view` | Dapat melihat laporan |
| `reports.export` | Dapat mengekspor laporan |

### 6. Periode Management
| Permission | Description |
|-----------|-------------|
| `periods.list` | Dapat melihat daftar periode |
| `periods.create` | Dapat membuat periode baru |
| `periods.edit` | Dapat mengedit periode |
| `periods.delete` | Dapat menghapus periode |

### 7. Website Management (Prodi)
| Permission | Description |
|-----------|-------------|
| `websites.list` | Dapat melihat daftar website prodi |
| `websites.create` | Dapat menambah website prodi |
| `websites.edit` | Dapat mengedit website prodi |
| `websites.delete` | Dapat menghapus website prodi |

### 8. Performance Data
| Permission | Description |
|-----------|-------------|
| `performance.list` | Dapat melihat data performansi |
| `performance.input` | Dapat menginput data performansi |
| `performance.edit` | Dapat mengedit data performansi |
| `performance.delete` | Dapat menghapus data performansi |
| `performance.dashboard` | Dapat melihat dashboard performansi |

### 9. Awarding (SAW - Simple Additive Weighting)
| Permission | Description |
|-----------|-------------|
| `awarding.periods.list` | Dapat melihat daftar periode awarding |
| `awarding.periods.create` | Dapat membuat periode awarding |
| `awarding.periods.edit` | Dapat mengedit periode awarding |
| `awarding.periods.delete` | Dapat menghapus periode awarding |
| `awarding.weights.manage` | Dapat mengelola bobot penilaian awarding |
| `awarding.scores.list` | Dapat melihat daftar penilaian awarding |
| `awarding.scores.input` | Dapat menginput penilaian awarding |
| `awarding.scores.edit` | Dapat mengedit penilaian awarding |
| `awarding.scores.delete` | Dapat menghapus penilaian awarding |
| `awarding.results.view` | Dapat melihat hasil & peringkat awarding |
| `awarding.results.export` | Dapat mengekspor hasil awarding |

---

## Role Definitions

### 🔴 Super Admin
**Title:** Super Admin  
**Description:** Kontrol penuh terhadap seluruh sistem.  
**Wildcard Permissions:** `admin.*`, `users.*`, `roles.*`, `dashboard.*`, `reports.*`, `periods.*`, `websites.*`, `performance.*`, `awarding.*`

> ✅ Has access to ALL features in the system

---

### 🟠 Admin
**Title:** Admin  
**Description:** Administrator harian sistem.

**Permissions:**
- `admin.access`
- `users.list`, `users.create`, `users.edit`, `users.delete`
- `dashboard.*` (all dashboard permissions)
- `reports.*` (all report permissions)
- `periods.*` (all period management permissions)
- `websites.*` (all website management permissions)
- `performance.*` (all performance data permissions)
- `awarding.*` (all awarding permissions)

> ✅ Can manage users, create/edit periods, manage awarding, and view all reports

---

### 🟡 Manager
**Title:** Manager  
**Description:** Manajer yang dapat melihat laporan dan mengelola data.

**Permissions:**
- `admin.access`
- `users.list`
- `dashboard.*` (all dashboard permissions)
- `reports.*` (all report permissions)
- `periods.list`
- `websites.list`
- `performance.list`
- `performance.dashboard`
- `awarding.periods.list`
- `awarding.scores.list`
- `awarding.results.view`
- `awarding.results.export`

> 👀 Can VIEW most data but cannot CREATE/EDIT/DELETE. Can export reports and awarding results.

---

### 🟢 User
**Title:** User  
**Description:** Pengguna umum dengan akses terbatas.

**Permissions:**
- `dashboard.access`
- `performance.dashboard`
- `awarding.results.view`

> 👤 Limited access - can only view dashboard and awarding results

---

## Permission Matrix by Role

```
Permission                      | Super Admin | Admin | Manager | User
--------------------------------|-----------|-------|---------|-----
admin.access                    |     ✅     |  ✅   |   ✅    |  ❌
admin.settings                  |     ✅     |  ✅   |   ❌    |  ❌
users.list                      |     ✅     |  ✅   |   ✅    |  ❌
users.create                    |     ✅     |  ✅   |   ❌    |  ❌
users.edit                      |     ✅     |  ✅   |   ❌    |  ❌
users.delete                    |     ✅     |  ✅   |   ❌    |  ❌
users.manage-roles              |     ✅     |  ❌   |   ❌    |  ❌
roles.list                      |     ✅     |  ❌   |   ❌    |  ❌
roles.create                    |     ✅     |  ❌   |   ❌    |  ❌
roles.edit                      |     ✅     |  ❌   |   ❌    |  ❌
roles.delete                    |     ✅     |  ❌   |   ❌    |  ❌
dashboard.access                |     ✅     |  ✅   |   ✅    |  ✅
dashboard.stats                 |     ✅     |  ✅   |   ✅    |  ❌
reports.view                    |     ✅     |  ✅   |   ✅    |  ❌
reports.export                  |     ✅     |  ✅   |   ✅    |  ❌
periods.list                    |     ✅     |  ✅   |   ✅    |  ❌
periods.create                  |     ✅     |  ✅   |   ❌    |  ❌
periods.edit                    |     ✅     |  ✅   |   ❌    |  ❌
periods.delete                  |     ✅     |  ✅   |   ❌    |  ❌
websites.list                   |     ✅     |  ✅   |   ✅    |  ❌
websites.create                 |     ✅     |  ✅   |   ❌    |  ❌
websites.edit                   |     ✅     |  ✅   |   ❌    |  ❌
websites.delete                 |     ✅     |  ✅   |   ❌    |  ❌
performance.list                |     ✅     |  ✅   |   ✅    |  ❌
performance.input               |     ✅     |  ✅   |   ❌    |  ❌
performance.edit                |     ✅     |  ✅   |   ❌    |  ❌
performance.delete              |     ✅     |  ✅   |   ❌    |  ❌
performance.dashboard           |     ✅     |  ✅   |   ✅    |  ✅
awarding.periods.list           |     ✅     |  ✅   |   ✅    |  ❌
awarding.periods.create         |     ✅     |  ✅   |   ❌    |  ❌
awarding.periods.edit           |     ✅     |  ✅   |   ❌    |  ❌
awarding.periods.delete         |     ✅     |  ✅   |   ❌    |  ❌
awarding.weights.manage         |     ✅     |  ✅   |   ❌    |  ❌
awarding.scores.list            |     ✅     |  ✅   |   ✅    |  ❌
awarding.scores.input           |     ✅     |  ✅   |   ❌    |  ❌
awarding.scores.edit            |     ✅     |  ✅   |   ❌    |  ❌
awarding.scores.delete          |     ✅     |  ✅   |   ❌    |  ❌
awarding.results.view           |     ✅     |  ✅   |   ✅    |  ✅
awarding.results.export         |     ✅     |  ✅   |   ✅    |  ❌
```

---

## Detailed Permission Mapping

### By Role → Permissions

#### Super Admin
```php
[
    'admin.*',              // All admin permissions
    'users.*',              // All user management permissions
    'roles.*',              // All role management permissions
    'dashboard.*',          // All dashboard permissions
    'reports.*',            // All report permissions
    'periods.*',            // All period management permissions
    'websites.*',           // All website management permissions
    'performance.*',        // All performance data permissions
    'awarding.*',           // All awarding permissions
]
```

#### Admin
```php
[
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
    'awarding.*',
]
```

#### Manager
```php
[
    'admin.access',
    'users.list',
    'dashboard.*',
    'reports.*',
    'periods.list',
    'websites.list',
    'performance.list',
    'performance.dashboard',
    'awarding.periods.list',
    'awarding.scores.list',
    'awarding.results.view',
    'awarding.results.export',
]
```

#### User
```php
[
    'dashboard.access',
    'performance.dashboard',
    'awarding.results.view',
]
```

---

## Access Level Summary

| Feature | Super Admin | Admin | Manager | User |
|---------|-----------|-------|---------|------|
| **User Management** | Full | Full | View Only | ❌ |
| **Role Management** | Full | Limited | ❌ | ❌ |
| **Settings** | Full | Full | ❌ | ❌ |
| **Dashboard** | Full | Full | View | View |
| **Reports** | Full | Full | View & Export | ❌ |
| **Period Management** | Full | Full | View Only | ❌ |
| **Website Management** | Full | Full | View Only | ❌ |
| **Performance Data** | Full | Full | View Only | ❌ |
| **Awarding System** | Full | Full | View & Export | View Results |

---

## Permission Inheritance

### Wildcard Patterns

- `admin.*` → Expands to: `admin.access`, `admin.settings`
- `users.*` → Expands to: `users.list`, `users.create`, `users.edit`, `users.delete`, `users.manage-roles`
- `roles.*` → Expands to: `roles.list`, `roles.create`, `roles.edit`, `roles.delete`
- `dashboard.*` → Expands to: `dashboard.access`, `dashboard.stats`
- `reports.*` → Expands to: `reports.view`, `reports.export`
- `periods.*` → Expands to: `periods.list`, `periods.create`, `periods.edit`, `periods.delete`
- `websites.*` → Expands to: `websites.list`, `websites.create`, `websites.edit`, `websites.delete`
- `performance.*` → Expands to: `performance.list`, `performance.input`, `performance.edit`, `performance.delete`, `performance.dashboard`
- `awarding.*` → Expands to: All `awarding.*` permissions (11 total)

---

## Configuration Location

- **File:** [app/Config/AuthGroups.php](app/Config/AuthGroups.php)
- **Permissions List:** `$permissions` array
- **Matrix Definition:** `$matrix` array
- **Groups Definition:** `$groups` array

---

**Total Permissions:** 39  
**Total Roles:** 4  
**Last Updated:** 2026-06-30
