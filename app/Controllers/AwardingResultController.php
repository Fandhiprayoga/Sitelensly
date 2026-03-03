<?php

namespace App\Controllers;

use App\Models\AwardingPeriodModel;
use App\Models\AwardingScoreModel;
use App\Models\AwardingWeightModel;

class AwardingResultController extends BaseController
{
    protected AwardingPeriodModel $awardingPeriodModel;
    protected AwardingScoreModel $scoreModel;
    protected AwardingWeightModel $weightModel;

    public function __construct()
    {
        $this->awardingPeriodModel = new AwardingPeriodModel();
        $this->scoreModel          = new AwardingScoreModel();
        $this->weightModel         = new AwardingWeightModel();
    }

    /**
     * Halaman hasil perhitungan SAW & peringkat
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
        $weights        = $awardingPeriodId ? $this->weightModel->getWeightsIndexed($awardingPeriodId) : [];
        $isWeightReady  = $awardingPeriodId ? $this->weightModel->isWeightComplete($awardingPeriodId) : false;
        $results        = [];
        $elements       = AwardingScoreModel::getStandardizationElements();

        if ($awardingPeriodId && $isWeightReady) {
            $results = $this->scoreModel->calculateSAW($awardingPeriodId, $weights);
        }

        // Hitung max values untuk tampilan normalisasi
        $maxValues = [
            'analytics' => 0,
            'content'   => 0,
            'standard'  => 0,
        ];
        foreach ($results as $row) {
            if ($row['raw_analytics'] > $maxValues['analytics']) $maxValues['analytics'] = $row['raw_analytics'];
            if ($row['raw_content']   > $maxValues['content'])   $maxValues['content']   = $row['raw_content'];
            if ($row['raw_standard']  > $maxValues['standard'])  $maxValues['standard']  = $row['raw_standard'];
        }

        $data = [
            'title'            => 'Hasil Awarding',
            'page_title'       => 'Hasil & Peringkat Awarding (SAW)',
            'periods'          => $periods,
            'selectedPeriodId' => $awardingPeriodId,
            'selectedPeriod'   => $selectedPeriod,
            'weights'          => $weights,
            'isWeightReady'    => $isWeightReady,
            'results'          => $results,
            'maxValues'        => $maxValues,
            'elements'         => $elements,
        ];

        return $this->renderView('awarding/results/index', $data);
    }

    /**
     * Export hasil ke CSV
     */
    public function exportCsv()
    {
        $awardingPeriodId = $this->request->getGet('period_id');

        if (!$awardingPeriodId) {
            return redirect()->to('/admin/awarding/results')->with('error', 'Periode tidak dipilih.');
        }

        $selectedPeriod = $this->awardingPeriodModel->find($awardingPeriodId);
        $weights        = $this->weightModel->getWeightsIndexed($awardingPeriodId);
        $results        = $this->scoreModel->calculateSAW($awardingPeriodId, $weights);

        if (empty($results)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor.');
        }

        $filename = 'awarding_result_' . url_title((string)$selectedPeriod['period_name'], '-', true) . '_' . date('Ymd') . '.csv';

        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, [
            'Peringkat', 'Nama Website', 'Kategori',
            'Klik Web', 'Klik Mobile', 'Klik Tablet', 'Total Klik (Analytics)',
            'Jumlah Postingan (Konten)',
            'Elemen Web Terpenuhi (Standarisasi)',
            'Normalisasi Analytics', 'Normalisasi Konten', 'Normalisasi Standarisasi',
            'Bobot Analytics', 'Bobot Konten', 'Bobot Standarisasi',
            'Nilai Preferensi (V)',
        ]);

        $wA = $weights['analytics']['weight_value'] ?? 0;
        $wC = $weights['content']['weight_value'] ?? 0;
        $wS = $weights['web_standardization']['weight_value'] ?? 0;

        foreach ($results as $row) {
            fputcsv($output, [
                $row['rank'],
                $row['website_name'],
                ucfirst($row['website_category'] ?? ''),
                $row['score_data']['clicks_web'],
                $row['score_data']['clicks_mobile'],
                $row['score_data']['clicks_tablet'],
                $row['raw_analytics'],
                $row['raw_content'],
                $row['raw_standard'] . '/17',
                number_format($row['norm_analytics'], 4),
                number_format($row['norm_content'], 4),
                number_format($row['norm_standard'], 4),
                $wA, $wC, $wS,
                number_format($row['preference_value'], 4),
            ]);
        }

        fclose($output);

        return $this->response;
    }
}
