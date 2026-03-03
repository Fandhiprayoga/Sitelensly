<?php

namespace App\Controllers;

use App\Models\PerformanceModel;
use App\Models\TopArticleModel;
use App\Models\WebsiteModel;
use App\Models\PeriodModel;

class ReportController extends BaseController
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
     * Halaman laporan ringkas
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

        // Tambahkan total klik dan artikel
        foreach ($performances as &$perf) {
            $perf['total_clicks'] = $perf['clicks_web'] + $perf['clicks_mobile'] + $perf['clicks_tablet'];
            $perf['articles'] = $this->topArticleModel->getByPerformanceId($perf['id']);
        }

        // Urutkan berdasarkan total klik (tertinggi)
        usort($performances, function ($a, $b) {
            return $b['total_clicks'] - $a['total_clicks'];
        });

        $data = [
            'title'            => 'Laporan Ringkas',
            'page_title'       => 'Laporan Ringkas Performansi',
            'performances'     => $performances,
            'periods'          => $periods,
            'selectedPeriodId' => $periodId,
        ];

        return $this->renderView('reports/index', $data);
    }

    /**
     * Export laporan ke CSV
     */
    public function exportCsv()
    {
        $periodId = $this->request->getGet('period_id');

        if (!$periodId) {
            return redirect()->to('/admin/reports')->with('error', 'Pilih periode terlebih dahulu.');
        }

        $period = $this->periodModel->find($periodId);
        $performances = $this->performanceModel->getPerformanceWithRelations($periodId);

        foreach ($performances as &$perf) {
            $perf['total_clicks'] = $perf['clicks_web'] + $perf['clicks_mobile'] + $perf['clicks_tablet'];
            $perf['articles'] = $this->topArticleModel->getByPerformanceId($perf['id']);
        }

        // Buat CSV
        $filename = 'laporan-performansi-' . url_title($period['period_name'], '-', true) . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM untuk Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, [
            'No',
            'Nama Website',
            'Kategori',
            'URL Website',
            'Klik Desktop',
            'Klik Mobile',
            'Klik Tablet',
            'Total Klik',
            'Jumlah Post Baru',
            'Update Terakhir',
            'Artikel Top 1',
            'Klik Artikel 1',
            'Artikel Top 2',
            'Klik Artikel 2',
            'Artikel Top 3',
            'Klik Artikel 3',
        ]);

        // Data
        $no = 1;
        foreach ($performances as $perf) {
            $row = [
                $no++,
                $perf['website_name'],
                $perf['website_category'] ?? '-',
                $perf['url'],
                $perf['clicks_web'],
                $perf['clicks_mobile'],
                $perf['clicks_tablet'],
                $perf['total_clicks'],
                $perf['total_new_posts'],
                $perf['last_post_date'] ?? '-',
            ];

            // Tambahkan artikel
            for ($i = 0; $i < 3; $i++) {
                if (isset($perf['articles'][$i])) {
                    $row[] = $perf['articles'][$i]['article_title'];
                    $row[] = $perf['articles'][$i]['article_clicks'];
                } else {
                    $row[] = '-';
                    $row[] = 0;
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
