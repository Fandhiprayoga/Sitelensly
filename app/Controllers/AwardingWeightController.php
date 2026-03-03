<?php

namespace App\Controllers;

use App\Models\AwardingPeriodModel;
use App\Models\AwardingWeightModel;

class AwardingWeightController extends BaseController
{
    protected AwardingPeriodModel $periodModel;
    protected AwardingWeightModel $weightModel;

    public function __construct()
    {
        $this->periodModel = new AwardingPeriodModel();
        $this->weightModel = new AwardingWeightModel();
    }

    /**
     * Halaman kelola bobot per periode awarding
     */
    public function index()
    {
        $awardingPeriodId = $this->request->getGet('period_id');
        $periods = $this->periodModel->orderBy('id', 'DESC')->findAll();

        // Default ke periode terbaru
        if (!$awardingPeriodId && !empty($periods)) {
            $awardingPeriodId = $periods[0]['id'];
        }

        $selectedPeriod = $awardingPeriodId ? $this->periodModel->find($awardingPeriodId) : null;
        $weights        = $awardingPeriodId ? $this->weightModel->getWeightsIndexed($awardingPeriodId) : [];
        $criteria       = AwardingWeightModel::getDefaultCriteria();

        $data = [
            'title'            => 'Bobot Penilaian',
            'page_title'       => 'Bobot Penilaian Awarding',
            'periods'          => $periods,
            'selectedPeriodId' => $awardingPeriodId,
            'selectedPeriod'   => $selectedPeriod,
            'weights'          => $weights,
            'criteria'         => $criteria,
        ];

        return $this->renderView('awarding/weights/index', $data);
    }

    /**
     * Simpan bobot penilaian
     */
    public function store()
    {
        $awardingPeriodId = $this->request->getPost('awarding_period_id');

        if (!$awardingPeriodId) {
            return redirect()->back()->with('error', 'Periode awarding tidak ditemukan.');
        }

        $period = $this->periodModel->find($awardingPeriodId);
        if (!$period) {
            return redirect()->back()->with('error', 'Periode awarding tidak ditemukan.');
        }

        // Tidak bisa edit bobot jika periode sudah completed
        if ($period['status'] === 'completed') {
            return redirect()->back()->with('error', 'Periode sudah selesai, bobot tidak dapat diubah.');
        }

        $weightsInput = $this->request->getPost('weights');
        if (!is_array($weightsInput)) {
            return redirect()->back()->with('error', 'Data bobot tidak valid.');
        }

        // Validasi total bobot harus = 1
        $total = 0;
        foreach ($weightsInput as $value) {
            $total += (float) $value;
        }

        if (abs($total - 1.0) > 0.001) {
            return redirect()->back()->withInput()->with('error', 'Total bobot harus sama dengan 1.00 (saat ini: ' . number_format($total, 4) . ').');
        }

        // Validasi setiap bobot > 0
        foreach ($weightsInput as $code => $value) {
            if ((float)$value <= 0) {
                return redirect()->back()->withInput()->with('error', 'Setiap bobot harus lebih dari 0.');
            }
        }

        $this->weightModel->saveWeights($awardingPeriodId, $weightsInput);

        return redirect()->to('/admin/awarding/weights?period_id=' . $awardingPeriodId)
            ->with('success', 'Bobot penilaian berhasil disimpan.');
    }
}
