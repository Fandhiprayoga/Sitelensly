<?php

namespace App\Models;

use CodeIgniter\Model;

class WebsiteModel extends Model
{
    protected $table         = 'ms_websites';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['website_name', 'category', 'url', 'description', 'admin_name', 'admin_contact', 'status'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Kategori website yang tersedia
     */
    public static function getCategories(): array
    {
        return [
            'prodi'    => 'Program Studi',
            'fakultas' => 'Fakultas',
            'unit'     => 'Unit / UPT',
            'lembaga'  => 'Lembaga',
            'pusat'    => 'Pusat Studi',
            'lainnya'  => 'Lainnya',
        ];
    }

    /**
     * Ambil website yang aktif
     */
    public function getActiveWebsites()
    {
        return $this->where('status', 'active')->findAll();
    }

    /**
     * Ambil website untuk dropdown
     */
    public function getWebsitesDropdown()
    {
        $websites = $this->where('status', 'active')->findAll();
        $dropdown = [];
        foreach ($websites as $website) {
            $dropdown[$website['id']] = $website['website_name'];
        }
        return $dropdown;
    }
}
