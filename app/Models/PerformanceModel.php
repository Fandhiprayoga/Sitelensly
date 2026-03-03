<?php

namespace App\Models;

use CodeIgniter\Model;

class PerformanceModel extends Model
{
    protected $table         = 'tr_performance';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'website_id', 'period_id', 'clicks_web', 'clicks_mobile',
        'clicks_tablet', 'total_new_posts', 'last_post_date', 'input_by',
    ];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /**
     * Ambil data performansi dengan relasi website dan periode
     */
    public function getPerformanceWithRelations($periodId = null)
    {
        $builder = $this->select('tr_performance.*, ms_websites.website_name, ms_websites.category as website_category, ms_websites.url, ms_periods.period_name, ms_periods.status as period_status')
            ->join('ms_websites', 'ms_websites.id = tr_performance.website_id')
            ->join('ms_periods', 'ms_periods.id = tr_performance.period_id');

        if ($periodId) {
            $builder->where('tr_performance.period_id', $periodId);
        }

        return $builder->orderBy('ms_websites.website_name', 'ASC')->findAll();
    }

    /**
     * Ambil data performansi per website
     */
    public function getPerformanceByWebsite($websiteId)
    {
        return $this->select('tr_performance.*, ms_periods.period_name')
            ->join('ms_periods', 'ms_periods.id = tr_performance.period_id')
            ->where('tr_performance.website_id', $websiteId)
            ->orderBy('ms_periods.id', 'ASC')
            ->findAll();
    }

    /**
     * Cek apakah data sudah ada untuk website + periode tertentu
     */
    public function getByWebsitePeriod($websiteId, $periodId)
    {
        return $this->where('website_id', $websiteId)
            ->where('period_id', $periodId)
            ->first();
    }

    /**
     * Ambil total klik semua perangkat per periode
     */
    public function getTotalClicksByPeriod($periodId)
    {
        return $this->selectSum('clicks_web')
            ->selectSum('clicks_mobile')
            ->selectSum('clicks_tablet')
            ->where('period_id', $periodId)
            ->first();
    }

    /**
     * Ambil ringkasan data untuk dashboard
     */
    public function getDashboardSummary($periodId = null, $websiteId = null)
    {
        $builder = $this->select('
                ms_websites.website_name,
                ms_websites.category as website_category,
                tr_performance.clicks_web,
                tr_performance.clicks_mobile,
                tr_performance.clicks_tablet,
                (tr_performance.clicks_web + tr_performance.clicks_mobile + tr_performance.clicks_tablet) as total_clicks,
                tr_performance.total_new_posts,
                tr_performance.last_post_date
            ')
            ->join('ms_websites', 'ms_websites.id = tr_performance.website_id');

        if ($periodId) {
            $builder->where('tr_performance.period_id', $periodId);
        }

        if ($websiteId) {
            $builder->where('tr_performance.website_id', $websiteId);
        }

        return $builder->orderBy('total_clicks', 'DESC')->findAll();
    }

    /**
     * Ambil tren data multi-periode untuk grafik
     */
    public function getTrendData($websiteId = null)
    {
        $builder = $this->select('
                ms_periods.period_name,
                ms_websites.website_name,
                ms_websites.category as website_category,
                tr_performance.website_id,
                tr_performance.period_id,
                tr_performance.clicks_web,
                tr_performance.clicks_mobile,
                tr_performance.clicks_tablet,
                (tr_performance.clicks_web + tr_performance.clicks_mobile + tr_performance.clicks_tablet) as total_clicks,
                tr_performance.total_new_posts,
                tr_performance.last_post_date
            ')
            ->join('ms_websites', 'ms_websites.id = tr_performance.website_id')
            ->join('ms_periods', 'ms_periods.id = tr_performance.period_id');

        if ($websiteId) {
            $builder->where('tr_performance.website_id', $websiteId);
        }

        return $builder->orderBy('ms_periods.id', 'ASC')->findAll();
    }
}
