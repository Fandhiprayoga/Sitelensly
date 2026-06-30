# Route List

> Generated from `app/Config/Routes.php`  
> Equivalent to: `php spark routes`

---

## Auth Routes (Shield)

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/login` | `\CodeIgniter\Shield\Controllers\LoginController::loginView` | — |
| POST | `/login` | `\CodeIgniter\Shield\Controllers\LoginController::loginAction` | — |
| GET | `/logout` | `\CodeIgniter\Shield\Controllers\LoginController::logoutAction` | — |
| GET | `/register` | `\CodeIgniter\Shield\Controllers\RegisterController::registerView` | — |
| POST | `/register` | `\CodeIgniter\Shield\Controllers\RegisterController::registerAction` | — |
| GET | `/auth/a/show` | Magic Link (show form) | — |
| POST | `/auth/a/handle` | Magic Link (send) | — |
| GET | `/auth/a/verify` | Magic Link (verify) | — |

---

## Public Routes

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/` | `AuthController::login` | — |
| GET | `/maintenance` | *(closure → view errors/maintenance)* | — |

---

## Protected Routes (filter: session)

### General

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/dashboard` | `DashboardController::index` | session |
| GET | `/performance-dashboard` | `PerformanceDashboardController::index` | session |
| GET | `/awarding-results` | `AwardingResultController::index` | session |
| POST | `/switch-group` | `GroupSwitchController::switch` | session |
| GET | `/profile` | `ProfileController::index` | session |
| POST | `/profile/update` | `ProfileController::update` | session |

---

### Admin (filter: session + permission:admin.access)

#### User Management

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/users` | `UserController::index` | permission:users.list |
| GET | `/admin/users/create` | `UserController::create` | permission:users.create |
| POST | `/admin/users/store` | `UserController::store` | permission:users.create |
| GET | `/admin/users/edit/{id}` | `UserController::edit` | permission:users.edit |
| POST | `/admin/users/update/{id}` | `UserController::update` | permission:users.edit |
| POST | `/admin/users/delete/{id}` | `UserController::delete` | permission:users.delete |
| POST | `/admin/users/assign-role/{id}` | `UserController::assignRole` | permission:users.manage-roles |

#### Role Management (filter: role:superadmin)

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/roles` | `RoleController::index` | role:superadmin |
| GET | `/admin/roles/permissions` | `RoleController::permissions` | role:superadmin |

#### Settings (filter: permission:admin.settings)

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/settings` | `SettingController::index` | permission:admin.settings |
| POST | `/admin/settings/update/general` | `SettingController::updateGeneral` | permission:admin.settings |
| POST | `/admin/settings/update/auth` | `SettingController::updateAuth` | permission:admin.settings |
| POST | `/admin/settings/update/mail` | `SettingController::updateMail` | permission:admin.settings |
| POST | `/admin/settings/update/branding` | `SettingController::updateBranding` | permission:admin.settings |
| POST | `/admin/settings/delete/branding` | `SettingController::deleteBranding` | permission:admin.settings |

#### Periode

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/periods` | `PeriodController::index` | permission:periods.list |
| GET | `/admin/periods/create` | `PeriodController::create` | permission:periods.create |
| POST | `/admin/periods/store` | `PeriodController::store` | permission:periods.create |
| GET | `/admin/periods/edit/{id}` | `PeriodController::edit` | permission:periods.edit |
| POST | `/admin/periods/update/{id}` | `PeriodController::update` | permission:periods.edit |
| POST | `/admin/periods/delete/{id}` | `PeriodController::delete` | permission:periods.delete |
| POST | `/admin/periods/toggle-status/{id}` | `PeriodController::toggleStatus` | permission:periods.edit |

#### Master Website

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/websites` | `WebsiteController::index` | permission:websites.list |
| GET | `/admin/websites/create` | `WebsiteController::create` | permission:websites.create |
| POST | `/admin/websites/store` | `WebsiteController::store` | permission:websites.create |
| GET | `/admin/websites/edit/{id}` | `WebsiteController::edit` | permission:websites.edit |
| POST | `/admin/websites/update/{id}` | `WebsiteController::update` | permission:websites.edit |
| POST | `/admin/websites/delete/{id}` | `WebsiteController::delete` | permission:websites.delete |

#### Input Data Performansi

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/performance` | `PerformanceController::index` | permission:performance.list |
| GET | `/admin/performance/create` | `PerformanceController::create` | permission:performance.input |
| POST | `/admin/performance/store` | `PerformanceController::store` | permission:performance.input |
| GET | `/admin/performance/edit/{id}` | `PerformanceController::edit` | permission:performance.edit |
| POST | `/admin/performance/update/{id}` | `PerformanceController::update` | permission:performance.edit |
| POST | `/admin/performance/delete/{id}` | `PerformanceController::delete` | permission:performance.delete |

#### Laporan

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/reports` | `ReportController::index` | permission:reports.view |
| GET | `/admin/reports/export-csv` | `ReportController::exportCsv` | permission:reports.export |

---

### Awarding / SAW (filter: session + permission:admin.access)

#### Periode Awarding

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/awarding/periods` | `AwardingPeriodController::index` | permission:awarding.periods.list |
| GET | `/admin/awarding/periods/create` | `AwardingPeriodController::create` | permission:awarding.periods.create |
| POST | `/admin/awarding/periods/store` | `AwardingPeriodController::store` | permission:awarding.periods.create |
| GET | `/admin/awarding/periods/edit/{id}` | `AwardingPeriodController::edit` | permission:awarding.periods.edit |
| POST | `/admin/awarding/periods/update/{id}` | `AwardingPeriodController::update` | permission:awarding.periods.edit |
| POST | `/admin/awarding/periods/set-status/{id}` | `AwardingPeriodController::setStatus` | permission:awarding.periods.edit |
| POST | `/admin/awarding/periods/delete/{id}` | `AwardingPeriodController::delete` | permission:awarding.periods.delete |

#### Bobot Penilaian

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/awarding/weights` | `AwardingWeightController::index` | permission:awarding.weights.manage |
| POST | `/admin/awarding/weights/store` | `AwardingWeightController::store` | permission:awarding.weights.manage |

#### Input Penilaian (Scores)

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/awarding/scores` | `AwardingScoreController::index` | permission:awarding.scores.list |
| GET | `/admin/awarding/scores/create` | `AwardingScoreController::create` | permission:awarding.scores.input |
| POST | `/admin/awarding/scores/store` | `AwardingScoreController::store` | permission:awarding.scores.input |
| GET | `/admin/awarding/scores/edit/{id}` | `AwardingScoreController::edit` | permission:awarding.scores.edit |
| POST | `/admin/awarding/scores/update/{id}` | `AwardingScoreController::update` | permission:awarding.scores.edit |
| POST | `/admin/awarding/scores/delete/{id}` | `AwardingScoreController::delete` | permission:awarding.scores.delete |
| GET | `/admin/awarding/scores/get-performance-data` | `AwardingScoreController::getPerformanceData` | permission:awarding.scores.input |

#### Hasil & Peringkat (Results)

| Method | Route | Handler | Filters |
|--------|-------|---------|---------|
| GET | `/admin/awarding/results` | `AwardingResultController::index` | permission:awarding.results.view |
| GET | `/admin/awarding/results/export-csv` | `AwardingResultController::exportCsv` | permission:awarding.results.export |

---

**Total routes:** ~60 (excluding Shield internal routes)
