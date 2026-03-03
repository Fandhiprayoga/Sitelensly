<?php

namespace App\Controllers;

class SettingController extends BaseController
{
    /**
     * Default favicon & logo paths (relative to public/)
     */
    private string $defaultFavicon = 'favicon.ico';
    private string $defaultLogo    = 'assets/img/stisla-fill.svg';
    private string $brandingPath   = 'assets/img/branding/';

    /**
     * Default setting values
     */
    private array $defaults = [
        'App.siteName'        => 'CI4 Shield RBAC',
        'App.siteNameShort'   => 'C4',
        'App.siteDescription' => 'Boilerplate CodeIgniter 4 dengan Shield RBAC',
        'App.siteFooter'      => 'CI4 Shield RBAC Boilerplate',
        'App.siteVersion'     => '1.0.0',
        'App.favicon'         => '',
        'App.logo'            => '',
        'App.maintenanceMode' => '0',
        'App.maintenanceMsg'  => 'Sistem sedang dalam pemeliharaan. Silakan coba beberapa saat lagi.',
        'App.defaultRole'     => 'user',
        'Auth.allowRegistration' => '1',
        'Mail.protocol'       => 'smtp',
        'Mail.hostname'       => '',
        'Mail.port'           => '587',
        'Mail.username'       => '',
        'Mail.password'       => '',
        'Mail.encryption'     => 'tls',
        'Mail.fromEmail'      => 'noreply@example.com',
        'Mail.fromName'       => 'CI4 RBAC',
    ];

    /**
     * Halaman pengaturan — tab-based
     */
    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?? 'general';

        $authGroups = config('AuthGroups');

        $data = [
            'title'      => 'Pengaturan',
            'page_title' => 'Pengaturan Sistem',
            'activeTab'  => $activeTab,
            'groups'     => $authGroups->groups,
            'settings'   => $this->getAllSettings(),
            'faviconUrl' => self::getFaviconUrl(),
            'logoUrl'    => self::getLogoUrl(),
            'hasCustomFavicon' => ! empty(setting('App.favicon')),
            'hasCustomLogo'    => ! empty(setting('App.logo')),
        ];

        return $this->renderView('settings/index', $data);
    }

    /**
     * Update pengaturan umum
     */
    public function updateGeneral()
    {
        $rules = [
            'site_name'        => 'required|max_length[100]',
            'site_name_short'  => 'permit_empty|max_length[10]',
            'site_description' => 'permit_empty|max_length[255]',
            'site_footer'      => 'permit_empty|max_length[100]',
            'site_version'     => 'permit_empty|max_length[20]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        setting('App.siteName', $this->request->getPost('site_name'));
        setting('App.siteNameShort', $this->request->getPost('site_name_short'));
        setting('App.siteDescription', $this->request->getPost('site_description'));
        setting('App.siteFooter', $this->request->getPost('site_footer'));
        setting('App.siteVersion', $this->request->getPost('site_version'));

        return redirect()->to('/admin/settings?tab=general')->with('success', 'Pengaturan umum berhasil diperbarui.');
    }

    /**
     * Update pengaturan autentikasi
     */
    public function updateAuth()
    {
        $rules = [
            'default_role'       => 'required',
            'allow_registration' => 'permit_empty',
            'maintenance_mode'   => 'permit_empty',
            'maintenance_msg'    => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        setting('App.defaultRole', $this->request->getPost('default_role'));
        setting('Auth.allowRegistration', $this->request->getPost('allow_registration') ? '1' : '0');
        setting('App.maintenanceMode', $this->request->getPost('maintenance_mode') ? '1' : '0');
        setting('App.maintenanceMsg', $this->request->getPost('maintenance_msg') ?? '');

        return redirect()->to('/admin/settings?tab=auth')->with('success', 'Pengaturan autentikasi berhasil diperbarui.');
    }

    /**
     * Update pengaturan email
     */
    public function updateMail()
    {
        $rules = [
            'mail_protocol'   => 'required|in_list[smtp,sendmail,mail]',
            'mail_hostname'   => 'permit_empty|max_length[255]',
            'mail_port'       => 'permit_empty|numeric',
            'mail_username'   => 'permit_empty|max_length[255]',
            'mail_password'   => 'permit_empty|max_length[255]',
            'mail_encryption' => 'required|in_list[tls,ssl,none]',
            'mail_from_email' => 'permit_empty|valid_email',
            'mail_from_name'  => 'permit_empty|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        setting('Mail.protocol', $this->request->getPost('mail_protocol'));
        setting('Mail.hostname', $this->request->getPost('mail_hostname') ?? '');
        setting('Mail.port', $this->request->getPost('mail_port') ?? '587');
        setting('Mail.username', $this->request->getPost('mail_username') ?? '');
        setting('Mail.encryption', $this->request->getPost('mail_encryption'));
        setting('Mail.fromEmail', $this->request->getPost('mail_from_email') ?? '');
        setting('Mail.fromName', $this->request->getPost('mail_from_name') ?? '');

        // Password hanya di-update jika diisi
        $password = $this->request->getPost('mail_password');
        if (! empty($password)) {
            setting('Mail.password', $password);
        }

        return redirect()->to('/admin/settings?tab=mail')->with('success', 'Pengaturan email berhasil diperbarui.');
    }

    /**
     * Upload favicon atau logo
     */
    public function updateBranding()
    {
        $type = $this->request->getPost('type'); // 'favicon' atau 'logo'

        if (! in_array($type, ['favicon', 'logo'], true)) {
            return redirect()->back()->with('error', 'Tipe branding tidak valid.');
        }

        $file = $this->request->getFile('branding_file');

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'File tidak valid atau belum dipilih.');
        }

        // Validasi tipe file
        if ($type === 'favicon') {
            $validTypes = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/svg+xml', 'image/ico'];
            $maxSize    = 512; // KB
            $validExts  = ['ico', 'png', 'svg'];
        } else {
            $validTypes = ['image/png', 'image/jpeg', 'image/svg+xml', 'image/webp'];
            $maxSize    = 2048; // KB
            $validExts  = ['png', 'jpg', 'jpeg', 'svg', 'webp'];
        }

        if ($file->getSizeByUnit('kb') > $maxSize) {
            return redirect()->back()->with('error', ucfirst($type) . ' maksimal ' . $maxSize . ' KB.');
        }

        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, $validExts, true)) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan: ' . implode(', ', $validExts));
        }

        // Hapus file lama jika ada
        $this->removeOldBranding($type);

        // Simpan file baru
        $newName = $type . '_' . time() . '.' . $ext;
        $file->move(FCPATH . $this->brandingPath, $newName);

        // Simpan path ke setting
        $settingKey = $type === 'favicon' ? 'App.favicon' : 'App.logo';
        setting($settingKey, $this->brandingPath . $newName);

        return redirect()->to('/admin/settings?tab=general')
            ->with('success', ucfirst($type) . ' berhasil diperbarui.');
    }

    /**
     * Hapus/reset favicon atau logo ke default
     */
    public function deleteBranding()
    {
        $type = $this->request->getPost('type');

        if (! in_array($type, ['favicon', 'logo'], true)) {
            return redirect()->back()->with('error', 'Tipe branding tidak valid.');
        }

        $this->removeOldBranding($type);

        // Reset setting ke kosong (akan fallback ke default)
        $settingKey = $type === 'favicon' ? 'App.favicon' : 'App.logo';
        setting($settingKey, '');

        return redirect()->to('/admin/settings?tab=general')
            ->with('success', ucfirst($type) . ' berhasil direset ke default.');
    }

    /**
     * Hapus file branding lama dari disk
     */
    private function removeOldBranding(string $type): void
    {
        $settingKey  = $type === 'favicon' ? 'App.favicon' : 'App.logo';
        $currentPath = setting($settingKey);

        if (! empty($currentPath) && is_file(FCPATH . $currentPath)) {
            unlink(FCPATH . $currentPath);
        }
    }

    /**
     * Helper: Ambil URL favicon (custom atau default)
     */
    public static function getFaviconUrl(): string
    {
        $custom = setting('App.favicon');
        return base_url(! empty($custom) ? $custom : 'favicon.ico');
    }

    /**
     * Helper: Ambil URL logo (custom atau default)
     */
    public static function getLogoUrl(): string
    {
        $custom = setting('App.logo');
        return base_url(! empty($custom) ? $custom : 'assets/img/stisla-fill.svg');
    }

    /**
     * Ambil semua settings, gunakan default jika belum ada di DB
     */
    private function getAllSettings(): array
    {
        $result = [];

        foreach ($this->defaults as $key => $default) {
            $value = setting($key);
            $result[$key] = $value ?? $default;
        }

        return $result;
    }
}
