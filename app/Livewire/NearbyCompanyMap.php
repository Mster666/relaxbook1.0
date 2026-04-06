<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\AdminOperatingHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class NearbyCompanyMap extends Component
{
    public array $companies = [];

    public function mount(): void
    {
        $admins = Admin::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('subscription_expires_at')
                    ->orWhere('subscription_expires_at', '>=', now());
            })
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->orderBy('company_name')
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'company_name',
                'company_address',
                'company_latitude',
                'company_longitude',
            ]);

        $adminIds = $admins->pluck('id')->map(fn ($v) => (int) $v)->all();

        $settingsByAdminId = collect();
        if (Schema::hasTable('admin_settings') && $adminIds !== []) {
            $settingsByAdminId = DB::table('admin_settings')
                ->whereIn('admin_id', $adminIds)
                ->get(['admin_id', 'timezone', 'open_time', 'close_time', 'break_start', 'break_end'])
                ->keyBy('admin_id');
        }

        $dayIso = Carbon::now('Asia/Manila')->dayOfWeekIso;

        $hoursByAdminId = collect();
        if (Schema::hasTable('admin_operating_hours') && $adminIds !== []) {
            $hoursByAdminId = AdminOperatingHour::query()
                ->whereIn('admin_id', $adminIds)
                ->where('day_of_week', $dayIso)
                ->get()
                ->keyBy('admin_id');
        }

        $roomsCountByAdminId = collect();
        if (Schema::hasTable('rooms') && $adminIds !== []) {
            $roomsCountByAdminId = DB::table('rooms')
                ->whereIn('admin_id', $adminIds)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('status')->orWhere('status', '!=', 'maintenance');
                })
                ->selectRaw('admin_id, COUNT(*) as cnt')
                ->groupBy('admin_id')
                ->pluck('cnt', 'admin_id');
        }

        $therapistsCountByAdminId = collect();
        if (Schema::hasTable('therapists') && $adminIds !== []) {
            $therapistsCountByAdminId = DB::table('therapists')
                ->whereIn('admin_id', $adminIds)
                ->where('is_active', true)
                ->selectRaw('admin_id, COUNT(*) as cnt')
                ->groupBy('admin_id')
                ->pluck('cnt', 'admin_id');
        }

        $this->companies = $admins
            ->map(function (Admin $admin) use ($settingsByAdminId, $hoursByAdminId, $roomsCountByAdminId, $therapistsCountByAdminId): array {
                $id = (int) $admin->id;

                $settings = $settingsByAdminId->get($id);
                $tz = $settings?->timezone ?: 'Asia/Manila';
                $fallbackOpen = $settings?->open_time ?: '09:00:00';
                $fallbackClose = $settings?->close_time ?: '22:00:00';
                $fallbackBreakStart = $settings?->break_start ?: null;
                $fallbackBreakEnd = $settings?->break_end ?: null;

                $hours = $hoursByAdminId->get($id);
                $isClosed = (bool) ($hours?->is_closed ?? false);
                $open = $hours?->opens_at ?: $fallbackOpen;
                $close = $hours?->closes_at ?: $fallbackClose;

                $now = Carbon::now($tz);
                $today = $now->toDateString();
                $openAt = Carbon::parse($today . ' ' . $open, $tz);
                $closeAt = Carbon::parse($today . ' ' . $close, $tz);
                $isOpenNow = (! $isClosed) && $now->betweenIncluded($openAt, $closeAt);

                $slotsRemaining = 0;
                if (! $isClosed) {
                    $breakIntervals = [];
                    if ($fallbackBreakStart && $fallbackBreakEnd) {
                        $breakIntervals[] = [
                            Carbon::parse($today . ' ' . $fallbackBreakStart, $tz),
                            Carbon::parse($today . ' ' . $fallbackBreakEnd, $tz),
                        ];
                    }

                    $cursor = $openAt->copy();
                    while ($cursor->lte($closeAt->copy()->subHour())) {
                        if ($cursor->lt($now)) {
                            $cursor->addHour();
                            continue;
                        }

                        $slotEnd = $cursor->copy()->addHour();
                        $overlapsBreak = false;
                        foreach ($breakIntervals as [$bStart, $bEnd]) {
                            if ($cursor->lt($bEnd) && $slotEnd->gt($bStart)) {
                                $overlapsBreak = true;
                                break;
                            }
                        }

                        if (! $overlapsBreak) {
                            $slotsRemaining++;
                        }

                        $cursor->addHour();
                    }
                }

                $roomsCount = (int) ($roomsCountByAdminId->get($id) ?? 0);
                $therapistsCount = (int) ($therapistsCountByAdminId->get($id) ?? 0);

                $availability = 'closed';
                if ($isOpenNow && $roomsCount > 0 && $therapistsCount > 0 && $slotsRemaining > 0) {
                    $availability = $slotsRemaining <= 2 ? 'limited' : 'available';
                } elseif (! $isOpenNow || $isClosed || $roomsCount === 0 || $therapistsCount === 0) {
                    $availability = 'closed';
                }

                return [
                    'id' => $id,
                    'name' => (string) ($admin->company_name ?: $admin->name),
                    'address' => $admin->company_address ? (string) $admin->company_address : null,
                    'lat' => $admin->company_latitude !== null ? (float) $admin->company_latitude : null,
                    'lng' => $admin->company_longitude !== null ? (float) $admin->company_longitude : null,
                    'rating_avg' => $admin->ratings_avg_rating !== null ? round((float) $admin->ratings_avg_rating, 2) : null,
                    'rating_count' => (int) ($admin->ratings_count ?? 0),
                    'is_open_now' => $isOpenNow,
                    'open_time' => (string) $open,
                    'close_time' => (string) $close,
                    'timezone' => (string) $tz,
                    'slots_remaining_today' => $slotsRemaining,
                    'availability' => $availability,
                ];
            })
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.nearby-company-map');
    }
}
