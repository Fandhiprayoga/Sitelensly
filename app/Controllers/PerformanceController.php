<?php

namespace App\Controllers;

use App\Models\PerformanceModel;
use App\Models\TopArticleModel;
use App\Models\WebsiteModel;
use App\Models\PeriodModel;

class PerformanceController extends BaseController
{
    protected PerformanceModel $performanceModel;
    protected TopArticleModel $topArticleModel;
    protected WebsiteModel $websiteModel;
    protected PeriodModel $periodModel;

    public function __construct()
    {
        $this->performanceModel = new PerformanceModel();
        $this->topArticleModel  = new TopArticleModel();
        $this->websiteModel     = new WebsiteModel();
        $this->periodModel      = new PeriodModel();
    }

    /**
     * Daftar data performansi
     */
    public function index()
    {
        $periodId = $this->request->getGet('period_id');
        $periods  = $this->periodModel->orderBy('id', 'DESC')->findAll();

        // Default ke periode terbaru
        if (!$periodId && !empty($periods)) {
            $periodId = $periods[0]['id'];
        }

        $performances = $this->performanceModel->getPerformanceWithRelations($periodId);

        // Ambil artikel untuk setiap performance
        foreach ($performances as &$perf) {
            $perf['articles'] = $this->topArticleModel->getByPerformanceId($perf['id']);
            $perf['total_clicks'] = $perf['clicks_web'] + $perf['clicks_mobile'] + $perf['clicks_tablet'];
        }

        $data = [
            'title'            => 'Data Performansi',
            'page_title'       => 'Data Performansi Website',
            'performances'     => $performances,
            'periods'          => $periods,
            'selectedPeriodId' => $periodId,
        ];

        return $this->renderView('performance/index', $data);
    }

    /**
     * Form input data performansi
     */
    public function create()
    {
        $data = [
            'title'      => 'Input Data Performansi',
            'page_title' => 'Input Data Performansi',
            'websites'   => $this->websiteModel->getActiveWebsites(),
            'periods'    => $this->periodModel->getOpenPeriods(),
        ];

        return $this->renderView('performance/create', $data);
    }

    /**
     * Simpan data performansi
     */
    public function store()
    {
        $rules = [
            'website_id'      => 'required|integer',
            'period_id'       => 'required|integer',
            'clicks_web'      => 'required|integer|is_natural',
            'clicks_mobile'   => 'required|integer|is_natural',
            'clicks_tablet'   => 'required|integer|is_natural',
            'total_new_posts' => 'required|integer|is_natural',
            'last_post_date'  => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $websiteId = $this->request->getPost('website_id');
        $periodId  = $this->request->getPost('period_id');

        // Cek periode masih open
        $period = $this->periodModel->find($periodId);
        if (!$period || $period['status'] !== 'open') {
            return redirect()->back()->withInput()->with('error', 'Periode sudah ditutup, tidak bisa input data.');
        }

        // Cek duplikasi
        $existing = $this->performanceModel->getByWebsitePeriod($websiteId, $periodId);
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Data untuk website dan periode ini sudah ada. Silakan edit data yang sudah ada.');
        }

        // Simpan performance
        $performanceId = $this->performanceModel->insert([
            'website_id'      => $websiteId,
            'period_id'       => $periodId,
            'clicks_web'      => $this->request->getPost('clicks_web'),
            'clicks_mobile'   => $this->request->getPost('clicks_mobile'),
            'clicks_tablet'   => $this->request->getPost('clicks_tablet'),
            'total_new_posts' => $this->request->getPost('total_new_posts'),
            'last_post_date'  => $this->request->getPost('last_post_date') ?: null,
            'input_by'        => auth()->id(),
        ]);

        // Simpan top articles
        $articles = $this->request->getPost('articles') ?? [];
        $this->topArticleModel->saveBatch($performanceId, $articles);

        return redirect()->to('/admin/performance')->with('success', 'Data performansi berhasil disimpan.');
    }

    /**
     * Form edit data performansi
     */
    public function edit(int $id)
    {
        $performance = $this->performanceModel->find($id);

        if (!$performance) {
            return redirect()->to('/admin/performance')->with('error', 'Data tidak ditemukan.');
        }

        // Cek periode masih open
        $period = $this->periodModel->find($performance['period_id']);
        if (!$period || $period['status'] !== 'open') {
            return redirect()->to('/admin/performance')->with('error', 'Periode sudah ditutup, tidak bisa edit data.');
        }

        $data = [
            'title'       => 'Edit Data Performansi',
            'page_title'  => 'Edit Data Performansi',
            'performance' => $performance,
            'articles'    => $this->topArticleModel->getByPerformanceId($id),
            'websites'    => $this->websiteModel->getActiveWebsites(),
            'periods'     => $this->periodModel->findAll(),
        ];

        return $this->renderView('performance/edit', $data);
    }

    /**
     * Update data performansi
     */
    public function update(int $id)
    {
        $performance = $this->performanceModel->find($id);

        if (!$performance) {
            return redirect()->to('/admin/performance')->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'clicks_web'      => 'required|integer|is_natural',
            'clicks_mobile'   => 'required|integer|is_natural',
            'clicks_tablet'   => 'required|integer|is_natural',
            'total_new_posts' => 'required|integer|is_natural',
            'last_post_date'  => 'permit_empty|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->performanceModel->update($id, [
            'clicks_web'      => $this->request->getPost('clicks_web'),
            'clicks_mobile'   => $this->request->getPost('clicks_mobile'),
            'clicks_tablet'   => $this->request->getPost('clicks_tablet'),
            'total_new_posts' => $this->request->getPost('total_new_posts'),
            'last_post_date'  => $this->request->getPost('last_post_date') ?: null,
        ]);

        // Update top articles
        $articles = $this->request->getPost('articles') ?? [];
        $this->topArticleModel->saveBatch($id, $articles);

        return redirect()->to('/admin/performance')->with('success', 'Data performansi berhasil diperbarui.');
    }

    /**
     * Hapus data performansi
     */
    public function delete(int $id)
    {
        $performance = $this->performanceModel->find($id);

        if (!$performance) {
            return redirect()->to('/admin/performance')->with('error', 'Data tidak ditemukan.');
        }

        // Hapus artikel terkait dulu
        $this->topArticleModel->where('performance_id', $id)->delete();
        $this->performanceModel->delete($id);

        return redirect()->to('/admin/performance')->with('success', 'Data performansi berhasil dihapus.');
    }
}
