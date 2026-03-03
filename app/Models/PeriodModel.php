<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodModel extends Model
{
    protected $table         = 'ms_periods';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['period_name', 'start_date', 'end_date', 'status'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Ambil semua periode yang berstatus open
     */
    public function getOpenPeriods()
    {
        return $this->where('status', 'open')->findAll();
    }

    /**
     * Ambil periode untuk dropdown
     */
    public function getPeriodsDropdown()
    {
        $periods = $this->orderBy('id', 'DESC')->findAll();
        $dropdown = [];
        foreach ($periods as $period) {
            $dropdown[$period['id']] = $period['period_name'] . ($period['status'] === 'closed' ? ' (Closed)' : '');
        }
        return $dropdown;
    }
}
