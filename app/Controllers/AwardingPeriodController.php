<?php

namespace App\Controllers;

use App\Models\AwardingPeriodModel;
use App\Models\AwardingWeightModel;
use App\Models\PeriodModel;

class AwardingPeriodController extends BaseController
{
    protected AwardingPeriodModel $awardingPeriodModel;
    protected AwardingWeightModel $weightModel;
    protected PeriodModel $periodModel;

    public function __construct()
    {
        $this->awardingPeriodModel = new AwardingPeriodModel();
        $this->weightModel         = new AwardingWeightModel();
        $this->periodModel         = new PeriodModel();
    }

    /**
     * Daftar semua periode awarding
     */
    public function index()
    {
        $periods = $this->awardingPeriodModel->getPeriodsWithPerformanceName();

        // Tambahkan status bobot untuk setiap periode
        foreach ($periods as &$period) {
            $period['weight_complete'] = $this->weightModel->isWeightComplete($period['id']);
        }

        $data = [
            'title'      => 'Periode Awarding',
            'page_title' => 'Manajemen Periode Awarding',
            'periods'    => $periods,
        ];

        return $this->renderView('awarding/periods/index', $data);
    }

    /**
     * Form tambah periode awarding
     */
    public function create()
    {
        $data = [
            'title'      => 'Tambah Periode Awarding',
            'page_title' => 'Tambah Periode Awarding',
            'perfPeriods' => $this->periodModel->orderBy('id', 'DESC')->findAll(),
        ];

        return $this->renderView('awarding/periods/create', $data);
    }

    /**
     * Simpan periode awarding baru
     */
    public function store()
    {
        $rules = [
            'period_name' => 'required|max_length[100]',
            'description' => 'permit_empty',
            'performance_period_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->awardingPeriodModel->insert([
            'period_name'            => $this->request->getPost('period_name'),
            'description'            => $this->request->getPost('description') ?: null,
            'performance_period_id'  => $this->request->getPost('performance_period_id') ?: null,
            'status'                 => 'draft',
        ]);

        return redirect()->to('/admin/awarding/periods')->with('success', 'Periode awarding berhasil ditambahkan.');
    }

    /**
     * Form edit periode awarding
     */
    public function edit(int $id)
    {
        $period = $this->awardingPeriodModel->find($id);
        if (!$period) {
            return redirect()->to('/admin/awarding/periods')->with('error', 'Periode tidak ditemukan.');
        }

        $data = [
            'title'       => 'Edit Periode Awarding',
            'page_title'  => 'Edit Periode Awarding',
            'period'      => $period,
            'perfPeriods' => $this->periodModel->orderBy('id', 'DESC')->findAll(),
        ];

        return $this->renderView('awarding/periods/edit', $data);
    }

    /**
     * Update periode awarding
     */
    public function update(int $id)
    {
        $period = $this->awardingPeriodModel->find($id);
        if (!$period) {
            return redirect()->to('/admin/awarding/periods')->with('error', 'Periode tidak ditemukan.');
        }

        $rules = [
            'period_name' => 'required|max_length[100]',
            'description' => 'permit_empty',
            'performance_period_id' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->awardingPeriodModel->update($id, [
            'period_name'            => $this->request->getPost('period_name'),
            'description'            => $this->request->getPost('description') ?: null,
            'performance_period_id'  => $this->request->getPost('performance_period_id') ?: null,
        ]);

        return redirect()->to('/admin/awarding/periods')->with('success', 'Periode awarding berhasil diperbarui.');
    }

    /**
     * Set status periode (draft → active → completed)
     */
    public function setStatus(int $id)
    {
        $period = $this->awardingPeriodModel->find($id);
        if (!$period) {
            return redirect()->to('/admin/awarding/periods')->with('error', 'Periode tidak ditemukan.');
        }

        $newStatus = $this->request->getPost('status');
        if (!in_array($newStatus, ['draft', 'active', 'completed'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        // Jika mau set active, pastikan bobot sudah lengkap
        if ($newStatus === 'active') {
            if (!$this->weightModel->isWeightComplete($id)) {
                return redirect()->back()->with('error', 'Bobot penilaian belum lengkap (total harus = 1). Silakan set bobot terlebih dahulu.');
            }

            // Pastikan tidak ada periode lain yang aktif
            if ($this->awardingPeriodModel->hasOtherActive($id)) {
                return redirect()->back()->with('error', 'Sudah ada periode awarding lain yang aktif. Selesaikan periode tersebut terlebih dahulu.');
            }
        }

        $this->awardingPeriodModel->update($id, ['status' => $newStatus]);

        $statusLabels = ['draft' => 'Draft', 'active' => 'Aktif', 'completed' => 'Selesai'];
        return redirect()->to('/admin/awarding/periods')->with('success', 'Status periode diubah menjadi ' . ($statusLabels[$newStatus] ?? $newStatus) . '.');
    }

    /**
     * Hapus periode awarding
     */
    public function delete(int $id)
    {
        $period = $this->awardingPeriodModel->find($id);
        if (!$period) {
            return redirect()->to('/admin/awarding/periods')->with('error', 'Periode tidak ditemukan.');
        }

        if ($period['status'] === 'active') {
            return redirect()->to('/admin/awarding/periods')->with('error', 'Tidak dapat menghapus periode yang sedang aktif.');
        }

        $this->awardingPeriodModel->delete($id);

        return redirect()->to('/admin/awarding/periods')->with('success', 'Periode awarding berhasil dihapus.');
    }
}
