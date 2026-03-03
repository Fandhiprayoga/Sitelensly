<?php

namespace App\Models;

use CodeIgniter\Model;

class AwardingPeriodModel extends Model
{
    protected $table         = 'ms_awarding_periods';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'period_name', 'description', 'performance_period_id', 'status',
    ];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Ambil periode awarding yang aktif
     */
    public function getActivePeriod()
    {
        return $this->where('status', 'active')->first();
    }

    /**
     * Ambil periode dengan nama periode performansi (jika ada)
     */
    public function getPeriodsWithPerformanceName()
    {
        return $this->select('ms_awarding_periods.*, ms_periods.period_name as perf_period_name')
            ->join('ms_periods', 'ms_periods.id = ms_awarding_periods.performance_period_id', 'left')
            ->orderBy('ms_awarding_periods.id', 'DESC')
            ->findAll();
    }

    /**
     * Cek apakah ada periode lain yang sudah aktif
     */
    public function hasOtherActive(int $excludeId = 0): bool
    {
        $builder = $this->where('status', 'active');
        if ($excludeId > 0) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
