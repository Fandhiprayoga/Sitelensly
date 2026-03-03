<?php

namespace App\Controllers;

use App\Models\PeriodModel;

class PeriodController extends BaseController
{
    protected PeriodModel $periodModel;

    public function __construct()
    {
        $this->periodModel = new PeriodModel();
    }

    /**
     * Daftar semua periode
     */
    public function index()
    {
        $data = [
            'title'      => 'Manajemen Periode',
            'page_title' => 'Manajemen Periode',
            'periods'    => $this->periodModel->orderBy('id', 'DESC')->findAll(),
        ];

        return $this->renderView('periods/index', $data);
    }

    /**
     * Form tambah periode baru
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Periode',
            'page_title' => 'Tambah Periode Baru',
        ];

        return $this->renderView('periods/create', $data);
    }

    /**
     * Simpan periode baru
     */
    public function store()
    {
        $rules = [
            'period_name' => 'required|max_length[100]',
            'start_date'  => 'permit_empty|valid_date',
            'end_date'    => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->periodModel->insert([
            'period_name' => $this->request->getPost('period_name'),
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'status'      => 'open',
        ]);

        return redirect()->to('/admin/periods')->with('success', 'Periode berhasil ditambahkan.');
    }

    /**
     * Form edit periode
     */
    public function edit(int $id)
    {
        $period = $this->periodModel->find($id);

        if (!$period) {
            return redirect()->to('/admin/periods')->with('error', 'Periode tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Periode',
            'page_title' => 'Edit Periode',
            'period'     => $period,
        ];

        return $this->renderView('periods/edit', $data);
    }

    /**
     * Update periode
     */
    public function update(int $id)
    {
        $period = $this->periodModel->find($id);

        if (!$period) {
            return redirect()->to('/admin/periods')->with('error', 'Periode tidak ditemukan.');
        }

        $rules = [
            'period_name' => 'required|max_length[100]',
            'start_date'  => 'permit_empty|valid_date',
            'end_date'    => 'permit_empty|valid_date',
            'status'      => 'required|in_list[open,closed]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->periodModel->update($id, [
            'period_name' => $this->request->getPost('period_name'),
            'start_date'  => $this->request->getPost('start_date') ?: null,
            'end_date'    => $this->request->getPost('end_date') ?: null,
            'status'      => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/periods')->with('success', 'Periode berhasil diperbarui.');
    }

    /**
     * Hapus periode
     */
    public function delete(int $id)
    {
        $period = $this->periodModel->find($id);

        if (!$period) {
            return redirect()->to('/admin/periods')->with('error', 'Periode tidak ditemukan.');
        }

        $this->periodModel->delete($id);

        return redirect()->to('/admin/periods')->with('success', 'Periode berhasil dihapus.');
    }

    /**
     * Toggle status periode (open/closed)
     */
    public function toggleStatus(int $id)
    {
        $period = $this->periodModel->find($id);

        if (!$period) {
            return redirect()->to('/admin/periods')->with('error', 'Periode tidak ditemukan.');
        }

        $newStatus = $period['status'] === 'open' ? 'closed' : 'open';
        $this->periodModel->update($id, ['status' => $newStatus]);

        $statusLabel = $newStatus === 'open' ? 'dibuka' : 'ditutup';
        return redirect()->to('/admin/periods')->with('success', "Periode berhasil {$statusLabel}.");
    }
}
