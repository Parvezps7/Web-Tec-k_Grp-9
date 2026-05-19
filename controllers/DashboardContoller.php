<?php

declare(strict_types=1);

class DashboardController extends BaseController
{
    public function index(): void
    {
        require_login();
        $user = current_user();
        $this->view('dashboard/index', ['user' => $user]);
    }
}
