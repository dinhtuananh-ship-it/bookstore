<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use OrderModel;

class ReportController extends Controller
{
    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');

        $from = trim((string) ($_GET['from'] ?? ''));
        $to = trim((string) ($_GET['to'] ?? ''));

        if ($from === '') {
            $from = date('Y-m-01');
        }
        if ($to === '') {
            $to = date('Y-m-d');
        }

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $model = new OrderModel();
        $summary = $model->reportSummary($from, $to);
        $byDay = $model->revenueByDay($from, $to);
        $byStatus = $model->statusBreakdown($from, $to);
        $topBooks = $model->topBooks($from, $to, 10);
        $topCustomers = $model->topCustomers($from, $to, 10);

        $maxDayRevenue = 1.0;
        foreach ($byDay as $row) {
            if ((float) $row['t'] > $maxDayRevenue) {
                $maxDayRevenue = (float) $row['t'];
            }
        }

        $this->view('admin/reports', [
            'pageTitle'     => 'Báo cáo doanh thu',
            'from'          => $from,
            'to'            => $to,
            'summary'       => $summary,
            'byDay'         => $byDay,
            'byStatus'      => $byStatus,
            'topBooks'      => $topBooks,
            'topCustomers'  => $topCustomers,
            'maxDayRevenue' => $maxDayRevenue,
        ]);
    }
}