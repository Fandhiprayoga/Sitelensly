<?php

namespace App\Controllers;

use App\Models\PerformanceModel;
use App\Models\TopArticleModel;
use App\Models\WebsiteModel;
use App\Models\PeriodModel;

class PerformanceDashboardController extends BaseController
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
     * Dashboard utama performansi
     */
    public function index()
    {
        $periodId  = $this->request->getGet('period_id');
        $websiteId = $this->request->getGet('website_id');
        $periods   = $this->periodModel->orderBy('id', 'DESC')->findAll();
        $websites  = $this->websiteModel->getActiveWebsites();

        // Default ke periode terbaru
        if (!$periodId && !empty($periods)) {
            $periodId = $periods[0]['id'];
        }

        // Data untuk widget ringkasan (filtered by period + website)
        $summaryData    = $this->performanceModel->getDashboardSummary($periodId, $websiteId);
        $totalClicksAll = 0;
        $totalPosts     = 0;
        $totalWeb       = 0;
        $totalMobile    = 0;
        $totalTablet    = 0;

        foreach ($summaryData as $row) {
            $totalClicksAll += $row['total_clicks'];
            $totalPosts     += $row['total_new_posts'];
            $totalWeb       += $row['clicks_web'];
            $totalMobile    += $row['clicks_mobile'];
            $totalTablet    += $row['clicks_tablet'];
        }

        // Data untuk grafik tren (multi-periode)
        $trendData     = $this->performanceModel->getTrendData($websiteId);
        $trendLabels   = [];
        $trendSeries   = [];

        // Kelompokkan tren per website
        foreach ($trendData as $row) {
            if (!in_array($row['period_name'], $trendLabels)) {
                $trendLabels[] = $row['period_name'];
            }
            $trendSeries[$row['website_name']][] = [
                'period'       => $row['period_name'],
                'total_clicks' => (int) $row['total_clicks'],
                'posts'        => (int) $row['total_new_posts'],
            ];
        }

        // Data untuk grafik batang (postingan per website)
        $postsBarLabels = [];
        $postsBarData   = [];
        foreach ($summaryData as $row) {
            $postsBarLabels[] = $row['website_name'];
            $postsBarData[]   = (int) $row['total_new_posts'];
        }

        // Leaderboard artikel (filtered by period + website)
        $leaderboard = $this->topArticleModel->getLeaderboard($periodId, $websiteId);

        // Data timeline update terakhir
        $timelineData = [];
        foreach ($summaryData as $row) {
            $timelineData[] = [
                'website_name'   => $row['website_name'],
                'last_post_date' => $row['last_post_date'],
            ];
        }
        // Urutkan dari yang paling lama update
        usort($timelineData, function ($a, $b) {
            if (empty($a['last_post_date'])) return -1;
            if (empty($b['last_post_date'])) return 1;
            return strtotime($a['last_post_date']) - strtotime($b['last_post_date']);
        });

        $data = [
            'title'           => 'Dashboard Performansi',
            'page_title'      => 'Dashboard Performansi Website',
            'periods'         => $periods,
            'websites'        => $websites,
            'selectedPeriodId'  => $periodId,
            'selectedWebsiteId' => $websiteId,
            'summaryData'     => $summaryData,
            'totalClicksAll'  => $totalClicksAll,
            'totalPosts'      => $totalPosts,
            'totalWeb'        => $totalWeb,
            'totalMobile'     => $totalMobile,
            'totalTablet'     => $totalTablet,
            'trendLabels'     => $trendLabels,
            'trendSeries'     => $trendSeries,
            'postsBarLabels'  => $postsBarLabels,
            'postsBarData'    => $postsBarData,
            'leaderboard'     => $leaderboard,
            'timelineData'    => $timelineData,
            'totalWebsites'   => count($websites),
        ];

        return $this->renderView('performance/dashboard', $data);
    }
}
