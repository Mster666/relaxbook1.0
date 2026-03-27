<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Admin;
use App\Models\User;
use App\Models\Booking;
use App\Models\SubscriptionLog;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.super-admin.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 0;

    public int $totalAdmins = 0;
    public int $activeAdmins = 0;
    public int $totalUsers = 0;
    public int $totalBookings = 0;

    public array $trendLabels = [];
    public array $trendValues = [];

    public array $barTrendLabels = [];
    public array $barTrendValues = [];

    public $expiringAdmins;

    public int $currentMonthSubscribers = 0;
    public int $currentMonthRevenue = 0;
    public int $totalRevenue = 0;
    public int $expiredSubscriptions = 0;
    public int $upcomingRenewals = 0;
    public array $monthlyRevenueBreakdown = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadTrendData();
        $this->loadExpiringAdmins();
        $this->loadSubscriptionSummary();
    }

    protected function loadStats(): void
    {
        $this->totalAdmins = Admin::count();
        $this->activeAdmins = Admin::where('is_active', true)->count();
        $this->totalUsers = User::count();
        $this->totalBookings = Booking::count();
    }

    protected function loadTrendData(): void
    {
        $now = Carbon::now();
        $this->trendLabels = [];
        $this->trendValues = [];
        
        // Line chart data (User Registrations over last 6 months)
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $this->trendLabels[] = $month->format('M');
            $this->trendValues[] = User::query()
                ->whereBetween('created_at', [$start, $end])
                ->count();
        }

        // Bar chart data (Total Admins over last 6 months)
        $this->barTrendLabels = $this->trendLabels;
        $this->barTrendValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $this->barTrendValues[] = Admin::query()
                ->whereBetween('created_at', [$start, $end])
                ->count();
        }
    }

    protected function loadExpiringAdmins(): void
    {
        $this->expiringAdmins = Admin::query()
            ->where('is_active', true)
            ->whereBetween('subscription_expires_at', [
                now(),
                now()->addDays(7)
            ])
            ->orderBy('subscription_expires_at')
            ->get(['id', 'name', 'email', 'subscription_expires_at']);
    }

    protected function loadSubscriptionSummary(): void
    {
        $monthlyPrice = 24999;

        $monthStart = now()->copy()->startOfMonth();
        $monthEnd = now()->copy()->endOfMonth();

        $this->currentMonthSubscribers = (int) SubscriptionLog::query()
            ->where('payment_status', 'PAID')
            ->whereNotNull('paid_at')
            ->whereDate('starts_at', '<=', $monthEnd->toDateString())
            ->whereDate('ends_at', '>=', $monthStart->toDateString())
            ->distinct('admin_id')
            ->count('admin_id');

        $this->currentMonthRevenue = $this->currentMonthSubscribers * $monthlyPrice;

        $this->totalRevenue = (int) SubscriptionLog::query()
            ->where('payment_status', 'PAID')
            ->whereNotNull('paid_at')
            ->sum('amount');

        $this->expiredSubscriptions = Admin::query()
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<', now())
            ->count();

        $this->upcomingRenewals = Admin::query()
            ->where('is_active', true)
            ->whereBetween('subscription_expires_at', [now(), now()->addDays(7)])
            ->count();

        $breakdown = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i)->startOfMonth();
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $subscribers = (int) SubscriptionLog::query()
                ->where('payment_status', 'PAID')
                ->whereNotNull('paid_at')
                ->whereDate('starts_at', '<=', $end->toDateString())
                ->whereDate('ends_at', '>=', $start->toDateString())
                ->distinct('admin_id')
                ->count('admin_id');

            $breakdown[] = [
                'month_key' => $month->format('Y-m'),
                'month' => $month->format('M Y'),
                'subscribers' => $subscribers,
                'revenue' => $subscribers * $monthlyPrice,
            ];
        }

        $this->monthlyRevenueBreakdown = $breakdown;
    }
}
