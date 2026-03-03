<?php

namespace App\Models;

use CodeIgniter\Model;

class AwardingWeightModel extends Model
{
    protected $table         = 'ms_awarding_weights';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'awarding_period_id', 'criteria_code', 'criteria_name',
        'weight_value', 'criteria_type',
    ];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Daftar kriteria default (3 kriteria SAW)
     */
    public static function getDefaultCriteria(): array
    {
        return [
            'analytics' => [
                'name' => 'Analytics (Jumlah Klik)',
                'type' => 'benefit',
            ],
            'content' => [
                'name' => 'Konten (Jumlah Postingan)',
                'type' => 'benefit',
            ],
            'web_standardization' => [
                'name' => 'Standarisasi Web (Kelengkapan Elemen)',
                'type' => 'benefit',
            ],
        ];
    }

    /**
     * Ambil bobot berdasarkan periode awarding
     */
    public function getByAwardingPeriod(int $awardingPeriodId): array
    {
        return $this->where('awarding_period_id', $awardingPeriodId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Ambil bobot yang sudah di-index berdasarkan criteria_code
     */
    public function getWeightsIndexed(int $awardingPeriodId): array
    {
        $weights = $this->getByAwardingPeriod($awardingPeriodId);
        $indexed = [];
        foreach ($weights as $w) {
            $indexed[$w['criteria_code']] = $w;
        }
        return $indexed;
    }

    /**
     * Simpan/update bobot untuk periode awarding (upsert)
     */
    public function saveWeights(int $awardingPeriodId, array $weightsData): bool
    {
        $criteria = self::getDefaultCriteria();

        foreach ($criteria as $code => $meta) {
            if (!isset($weightsData[$code])) {
                continue;
            }

            $existing = $this->where('awarding_period_id', $awardingPeriodId)
                ->where('criteria_code', $code)
                ->first();

            $data = [
                'awarding_period_id' => $awardingPeriodId,
                'criteria_code'      => $code,
                'criteria_name'      => $meta['name'],
                'weight_value'       => (float) $weightsData[$code],
                'criteria_type'      => $meta['type'],
            ];

            if ($existing) {
                $this->update($existing['id'], $data);
            } else {
                $this->insert($data);
            }
        }

        return true;
    }

    /**
     * Cek apakah total bobot = 1
     */
    public function isWeightComplete(int $awardingPeriodId): bool
    {
        $weights = $this->getByAwardingPeriod($awardingPeriodId);
        if (count($weights) < 3) {
            return false;
        }
        $total = array_sum(array_column($weights, 'weight_value'));
        return abs($total - 1.0) < 0.001;
    }
}
