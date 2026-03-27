<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 0;

    protected array $extraBodyAttributes = [
        'class' => 'rb-admin-dashboard',
    ];

    public int $totalServices = 0;
    public int $monthlyBookings = 0;
    public int $totalTherapists = 0;
    public int $totalClients = 0;

    public array $bookingSegmentation = [
        'pending' => 0,
        'confirmed' => 0,
        'completed' => 0,
        'cancelled' => 0,
    ];

    public int $bookingSegmentationTotal = 0;
    public int $completedPercent = 0;

    public array $trendLabels = [];
    public array $trendValues = [];

    public string $topServiceName = 'All Services';
    public array $serviceTrendLabels = [];
    public array $serviceTrendValues = [];

    public array $serviceDistribution = [];

    public $todaysBookings;

    public function mount()
    {
        $adminId = Auth::guard('admin')->id();

        $now = Carbon::now();
        $today = Carbon::today();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $this->totalServices = Service::query()
            ->where('admin_id', $adminId)
            ->count();

        $this->monthlyBookings = Booking::query()
            ->where('admin_id', $adminId)
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->count();

        $this->totalTherapists = Therapist::query()
            ->where('admin_id', $adminId)
            ->count();

        $this->totalClients = User::query()
            ->whereHas('bookings', fn ($query) => $query->where('admin_id', $adminId))
            ->distinct('users.id')
            ->count('users.id');

        $this->todaysBookings = Booking::query()
            ->where('admin_id', $adminId)
            ->whereDate('booking_date', $today)
            ->with([
                'user:id,name',
                'therapist:id,name',
                'service:id,name',
            ])
            ->orderByDesc('booking_time')
            ->get(['id', 'user_id', 'therapist_id', 'service_id', 'booking_date', 'booking_time', 'status']);

        $segmentationRaw = Booking::query()
            ->where('admin_id', $adminId)
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->selectRaw("COALESCE(status, 'pending') as status, COUNT(*) as aggregate")
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->toArray();

        $this->bookingSegmentation = [
            'pending' => (int) ($segmentationRaw['pending'] ?? 0),
            'confirmed' => (int) ($segmentationRaw['confirmed'] ?? 0),
            'completed' => (int) ($segmentationRaw['completed'] ?? 0),
            'cancelled' => (int) ($segmentationRaw['cancelled'] ?? 0),
        ];

        $this->bookingSegmentationTotal = array_sum($this->bookingSegmentation);
        $this->completedPercent = $this->bookingSegmentationTotal > 0
            ? (int) round(($this->bookingSegmentation['completed'] / $this->bookingSegmentationTotal) * 100)
            : 0;

        $trendRangeStart = $now->copy()->startOfMonth()->subMonths(5);
        $this->trendLabels = [];
        $this->trendValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $this->trendLabels[] = $month->format('M');
            $this->trendValues[] = Booking::query()
                ->where('admin_id', $adminId)
                ->whereBetween('booking_date', [$start, $end])
                ->count();
        }

        $topServiceRow = Booking::query()
            ->where('admin_id', $adminId)
            ->whereBetween('booking_date', [$trendRangeStart, $monthEnd])
            ->whereNotNull('service_id')
            ->select('service_id', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('service_id')
            ->orderByDesc('aggregate')
            ->first();

        $topServiceId = $topServiceRow?->service_id;

        if ($topServiceId) {
            $this->topServiceName = (string) (Service::query()->whereKey($topServiceId)->value('name') ?? 'Top Service');
        }

        $this->serviceTrendLabels = $this->trendLabels;
        $this->serviceTrendValues = [];

        foreach ($this->trendLabels as $index => $label) {
            $month = $now->copy()->subMonths(5 - $index);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $query = Booking::query()
                ->where('admin_id', $adminId)
                ->whereBetween('booking_date', [$start, $end]);

            if ($topServiceId) {
                $query->where('service_id', $topServiceId);
            }

            $this->serviceTrendValues[] = $query->count();
        }

        $serviceDistributionRows = Booking::query()
            ->where('admin_id', $adminId)
            ->whereBetween('booking_date', [$monthStart, $monthEnd])
            ->whereNotNull('service_id')
            ->select('service_id', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('service_id')
            ->orderByDesc('aggregate')
            ->limit(4)
            ->get();

        $serviceIds = $serviceDistributionRows->pluck('service_id')->all();
        $serviceNames = Service::query()
            ->whereIn('id', $serviceIds)
            ->pluck('name', 'id')
            ->toArray();

        $this->serviceDistribution = $serviceDistributionRows
            ->map(function ($row) use ($serviceNames) {
                return [
                    'service_id' => (int) $row->service_id,
                    'name' => (string) ($serviceNames[$row->service_id] ?? 'Service'),
                    'count' => (int) $row->aggregate,
                ];
            })
            ->values()
            ->all();
    }
}
