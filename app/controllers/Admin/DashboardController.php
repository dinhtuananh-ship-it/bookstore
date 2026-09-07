<?php
declare(strict_types=1);

namespace Admin;

use Controller;
use BookModel;
use OrderModel;
use UserModel;

class DashboardController extends Controller
{
    public function beforeAction(string $action): void
    {
        requireAdmin();
    }

    public function index(): void
    {
        setLayout('layout/admin');
        $this->view('admin/dashboard', [
            'pageTitle'  => 'Tổng quan',
            'stats'      => (new OrderModel())->stats(),
            'bookCount'  => (new BookModel())->countAll(),
            'memberCount' => (new UserModel())->countMembers(),
        ]);
    }
}