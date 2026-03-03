<?php

namespace App\Controllers;

use App\Models\WebsiteModel;

class WebsiteController extends BaseController
{
    protected WebsiteModel $websiteModel;

    public function __construct()
    {
        $this->websiteModel = new WebsiteModel();
    }

    /**
     * Daftar semua website
     */
    public function index()
    {
        $data = [
            'title'      => 'Master Website',
            'page_title' => 'Master Website',
            'websites'   => $this->websiteModel->orderBy('website_name', 'ASC')->findAll(),
            'categories' => WebsiteModel::getCategories(),
        ];

        return $this->renderView('websites/index', $data);
    }

    /**
     * Form tambah website
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Website',
            'page_title' => 'Tambah Website',
            'categories' => WebsiteModel::getCategories(),
        ];

        return $this->renderView('websites/create', $data);
    }

    /**
     * Simpan website baru
     */
    public function store()
    {
        $rules = [
            'website_name'  => 'required|max_length[255]',
            'category'      => 'required|max_length[50]',
            'url'           => 'required|max_length[500]|valid_url_strict',
            'description'   => 'permit_empty',
            'admin_name'    => 'permit_empty|max_length[255]',
            'admin_contact' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->websiteModel->insert([
            'website_name'  => $this->request->getPost('website_name'),
            'category'      => $this->request->getPost('category'),
            'url'           => $this->request->getPost('url'),
            'description'   => $this->request->getPost('description') ?: null,
            'admin_name'    => $this->request->getPost('admin_name') ?: null,
            'admin_contact' => $this->request->getPost('admin_contact') ?: null,
            'status'        => 'active',
        ]);

        return redirect()->to('/admin/websites')->with('success', 'Website berhasil ditambahkan.');
    }

    /**
     * Form edit website
     */
    public function edit(int $id)
    {
        $website = $this->websiteModel->find($id);

        if (!$website) {
            return redirect()->to('/admin/websites')->with('error', 'Website tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Website',
            'page_title' => 'Edit Website',
            'website'    => $website,
            'categories' => WebsiteModel::getCategories(),
        ];

        return $this->renderView('websites/edit', $data);
    }

    /**
     * Update website
     */
    public function update(int $id)
    {
        $website = $this->websiteModel->find($id);

        if (!$website) {
            return redirect()->to('/admin/websites')->with('error', 'Website tidak ditemukan.');
        }

        $rules = [
            'website_name'  => 'required|max_length[255]',
            'category'      => 'required|max_length[50]',
            'url'           => 'required|max_length[500]|valid_url_strict',
            'description'   => 'permit_empty',
            'admin_name'    => 'permit_empty|max_length[255]',
            'admin_contact' => 'permit_empty|max_length[255]',
            'status'        => 'required|in_list[active,inactive]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->websiteModel->update($id, [
            'website_name'  => $this->request->getPost('website_name'),
            'category'      => $this->request->getPost('category'),
            'url'           => $this->request->getPost('url'),
            'description'   => $this->request->getPost('description') ?: null,
            'admin_name'    => $this->request->getPost('admin_name') ?: null,
            'admin_contact' => $this->request->getPost('admin_contact') ?: null,
            'status'        => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/websites')->with('success', 'Website berhasil diperbarui.');
    }

    /**
     * Hapus website
     */
    public function delete(int $id)
    {
        $website = $this->websiteModel->find($id);

        if (!$website) {
            return redirect()->to('/admin/websites')->with('error', 'Website tidak ditemukan.');
        }

        $this->websiteModel->delete($id);

        return redirect()->to('/admin/websites')->with('success', 'Website berhasil dihapus.');
    }
}
