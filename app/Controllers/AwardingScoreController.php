<?php

namespace App\Controllers;

use App\Models\AwardingPeriodModel;
use App\Models\AwardingScoreModel;
use App\Models\AwardingWeightModel;
use App\Models\WebsiteModel;
use App\Models\PerformanceModel;
use App\Models\PeriodModel;

class AwardingScoreController extends BaseController
{
    protected AwardingPeriodModel $awardingPeriodModel;
    protected AwardingScoreModel $scoreModel;
    protected AwardingWeightModel $weightModel;
    protected WebsiteModel $websiteModel;
    protected PerformanceModel $performanceModel;
    protected PeriodModel $periodModel;

    public function __construct()
    {
        $this->awardingPeriodModel = new AwardingPeriodModel();
        $this->scoreModel          = new AwardingScoreModel();
        $this->weightModel         = new AwardingWeightModel();
        $this->websiteModel        = new WebsiteModel();
        $this->performanceModel    = new PerformanceModel();
        $this->periodModel         = new PeriodModel();
    }

    /**
     * Daftar penilaian per periode awarding
     */
    public function index()
    {
        $awardingPeriodId = $this->request->getGet('period_id');
        $periods = $this->awardingPeriodModel->orderBy('id', 'DESC')->findAll();

        // Default ke periode aktif atau terbaru
        if (!$awardingPeriodId) {
            $activePeriod = $this->awardingPeriodModel->getActivePeriod();
            $awardingPeriodId = $activePeriod ? $activePeriod['id'] : (!empty($periods) ? $periods[0]['id'] : null);
        }

        $selectedPeriod = $awardingPeriodId ? $this->awardingPeriodModel->find($awardingPeriodId) : null;
        $scores         = $awardingPeriodId ? $this->scoreModel->getScoresWithWebsite($awardingPeriodId) : [];
        $elements       = AwardingScoreModel::getStandardizationElements();

        // Hitung skor kalkulasi per baris
        foreach ($scores as &$score) {
            $score['calc_analytics'] = $this->scoreModel->calcAnalyticsScore($score);
            $score['calc_content']   = $this->scoreModel->calcContentScore($score);
            $score['calc_standard']  = $this->scoreModel->calcStandardizationScore($score);
        }

        $data = [
            'title'            => 'Input Penilaian',
            'page_title'       => 'Input Penilaian Awarding',
            'periods'          => $periods,
            'selectedPeriodId' => $awardingPeriodId,
            'selectedPeriod'   => $selectedPeriod,
            'scores'           => $scores,
            'elements'         => $elements,
        ];

        return $this->renderView('awarding/scores/index', $data);
    }

    /**
     * Form input penilaian untuk website tertentu
     */
    public function create()
    {
        $awardingPeriodId = $this->request->getGet('period_id');
        if (!$awardingPeriodId) {
            return redirect()->to('/admin/awarding/scores')->with('error', 'Pilih periode awarding terlebih dahulu.');
        }

        $selectedPeriod = $this->awardingPeriodModel->find($awardingPeriodId);
        if (!$selectedPeriod || $selectedPeriod['status'] !== 'active') {
            return redirect()->to('/admin/awarding/scores')->with('error', 'Periode awarding harus berstatus Aktif untuk input penilaian.');
        }

        // Ambil website yang belum dinilai di periode ini
        $allWebsites = $this->websiteModel->getActiveWebsites();
        $scoredIds   = array_column($this->scoreModel->where('awarding_period_id', $awardingPeriodId)->findAll(), 'website_id');
        $availableWebsites = array_filter($allWebsites, fn($w) => !in_array($w['id'], $scoredIds));

        // Ambil periode performansi untuk opsi import
        $perfPeriods = $this->periodModel->orderBy('id', 'DESC')->findAll();

        $data = [
            'title'            => 'Input Penilaian',
            'page_title'       => 'Input Penilaian Website',
            'selectedPeriod'   => $selectedPeriod,
            'websites'         => array_values($availableWebsites),
            'perfPeriods'      => $perfPeriods,
            'elements'         => AwardingScoreModel::getStandardizationElements(),
        ];

        return $this->renderView('awarding/scores/create', $data);
    }

    /**
     * Simpan penilaian
     */
    public function store()
    {
        $awardingPeriodId = $this->request->getPost('awarding_period_id');
        $selectedPeriod   = $this->awardingPeriodModel->find($awardingPeriodId);

        if (!$selectedPeriod || $selectedPeriod['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Periode awarding harus berstatus Aktif.');
        }

        $rules = [
            'website_id'    => 'required|integer',
            'clicks_web'    => 'required|integer|is_natural',
            'clicks_mobile' => 'required|integer|is_natural',
            'clicks_tablet' => 'required|integer|is_natural',
            'total_posts'   => 'required|integer|is_natural',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $websiteId = $this->request->getPost('website_id');

        // Cek duplikasi
        $existing = $this->scoreModel->getByWebsiteAndPeriod($websiteId, $awardingPeriodId);
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Website ini sudah memiliki penilaian di periode ini.');
        }

        // Siapkan data standarisasi
        $elements = AwardingScoreModel::getStandardizationElements();
        $stdData  = [];
        foreach (array_keys($elements) as $field) {
            $stdData[$field] = $this->request->getPost($field) ? 1 : 0;
        }

        $insertData = array_merge([
            'awarding_period_id' => $awardingPeriodId,
            'website_id'         => $websiteId,
            'clicks_web'         => $this->request->getPost('clicks_web'),
            'clicks_mobile'      => $this->request->getPost('clicks_mobile'),
            'clicks_tablet'      => $this->request->getPost('clicks_tablet'),
            'total_posts'        => $this->request->getPost('total_posts'),
            'is_imported'        => $this->request->getPost('is_imported') ? 1 : 0,
            'input_by'           => auth()->id(),
            'notes'              => $this->request->getPost('notes') ?: null,
        ], $stdData);

        $this->scoreModel->insert($insertData);

        return redirect()->to('/admin/awarding/scores?period_id=' . $awardingPeriodId)
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    /**
     * Form edit penilaian
     */
    public function edit(int $id)
    {
        $score = $this->scoreModel->find($id);
        if (!$score) {
            return redirect()->to('/admin/awarding/scores')->with('error', 'Data penilaian tidak ditemukan.');
        }

        $selectedPeriod = $this->awardingPeriodModel->find($score['awarding_period_id']);
        if (!$selectedPeriod || $selectedPeriod['status'] !== 'active') {
            return redirect()->to('/admin/awarding/scores')->with('error', 'Periode awarding harus berstatus Aktif untuk edit penilaian.');
        }

        $website = $this->websiteModel->find($score['website_id']);
        $perfPeriods = $this->periodModel->orderBy('id', 'DESC')->findAll();

        $data = [
            'title'          => 'Edit Penilaian',
            'page_title'     => 'Edit Penilaian Website',
            'score'          => $score,
            'website'        => $website,
            'selectedPeriod' => $selectedPeriod,
            'perfPeriods'    => $perfPeriods,
            'elements'       => AwardingScoreModel::getStandardizationElements(),
        ];

        return $this->renderView('awarding/scores/edit', $data);
    }

    /**
     * Update penilaian
     */
    public function update(int $id)
    {
        $score = $this->scoreModel->find($id);
        if (!$score) {
            return redirect()->to('/admin/awarding/scores')->with('error', 'Data tidak ditemukan.');
        }

        $selectedPeriod = $this->awardingPeriodModel->find($score['awarding_period_id']);
        if (!$selectedPeriod || $selectedPeriod['status'] !== 'active') {
            return redirect()->back()->with('error', 'Periode awarding harus berstatus Aktif.');
        }

        $rules = [
            'clicks_web'    => 'required|integer|is_natural',
            'clicks_mobile' => 'required|integer|is_natural',
            'clicks_tablet' => 'required|integer|is_natural',
            'total_posts'   => 'required|integer|is_natural',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $elements = AwardingScoreModel::getStandardizationElements();
        $stdData  = [];
        foreach (array_keys($elements) as $field) {
            $stdData[$field] = $this->request->getPost($field) ? 1 : 0;
        }

        $updateData = array_merge([
            'clicks_web'    => $this->request->getPost('clicks_web'),
            'clicks_mobile' => $this->request->getPost('clicks_mobile'),
            'clicks_tablet' => $this->request->getPost('clicks_tablet'),
            'total_posts'   => $this->request->getPost('total_posts'),
            'is_imported'   => $this->request->getPost('is_imported') ? 1 : 0,
            'notes'         => $this->request->getPost('notes') ?: null,
        ], $stdData);

        $this->scoreModel->update($id, $updateData);

        return redirect()->to('/admin/awarding/scores?period_id=' . $score['awarding_period_id'])
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    /**
     * Hapus penilaian
     */
    public function delete(int $id)
    {
        $score = $this->scoreModel->find($id);
        if (!$score) {
            return redirect()->to('/admin/awarding/scores')->with('error', 'Data tidak ditemukan.');
        }

        $selectedPeriod = $this->awardingPeriodModel->find($score['awarding_period_id']);
        if (!$selectedPeriod || $selectedPeriod['status'] !== 'active') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus penilaian pada periode yang tidak aktif.');
        }

        $periodId = $score['awarding_period_id'];
        $this->scoreModel->delete($id);

        return redirect()->to('/admin/awarding/scores?period_id=' . $periodId)
            ->with('success', 'Penilaian berhasil dihapus.');
    }

    /**
     * AJAX: Ambil data performansi untuk import
     */
    public function getPerformanceData()
    {
        $websiteId      = $this->request->getGet('website_id');
        $perfPeriodId   = $this->request->getGet('perf_period_id');

        if (!$websiteId || !$perfPeriodId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter tidak lengkap.']);
        }

        $perfData = $this->performanceModel->getByWebsitePeriod($websiteId, $perfPeriodId);

        if (!$perfData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data performansi tidak ditemukan untuk website dan periode ini.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => [
                'clicks_web'    => (int) $perfData['clicks_web'],
                'clicks_mobile' => (int) $perfData['clicks_mobile'],
                'clicks_tablet' => (int) $perfData['clicks_tablet'],
                'total_posts'   => (int) $perfData['total_new_posts'],
            ],
        ]);
    }
}
