<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\AdminOperatingHour;
use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class BookingWizard extends Component
{
    public $step = 1;

    protected function selectedCompanyId(): ?int
    {
        return session('selected_company_id');
    }

    public $selectedServiceIds = [];

    public $assignedTherapists = [];

    public ?int $activeServiceId = null;

    // Step 1: Service
    public $selectedServiceId;

    public $selectedService;

    // Step 2: Therapist
    public $selectedTherapistId;

    public $selectedTherapist;

    // Rooms
    public $selectedRoomId;

    public $roomSelectionMade = false;

    public string $roomSearch = '';

    public string $roomCapacity = 'any';

    public array $roomAmenities = [];

    public string $roomAvailability = 'any';

    // Step 3: Date & Time
    public $selectedDate;

    public $selectedTime;

    public $availableSlots = [];

    public ?array $scheduleInfo = null;

    public $currentCalendarMonth;

    // Step 5: Confirm (Notes)
    public $notes;

    // Success Modal State
    public $showSuccessModal = false;

    public $bookedDetails = [];

    public function mount()
    {
        $this->resetAfterCompanySelected();
        $this->currentCalendarMonth = Carbon::now('Asia/Manila');
        $this->step = 1;
    }

    protected function resetAfterCompanySelected(): void
    {
        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->availableSlots = [];
        $this->scheduleInfo = null;
        $this->selectedRoomId = null;
        $this->roomSelectionMade = false;
        $this->roomSearch = '';
        $this->roomCapacity = 'any';
        $this->roomAmenities = [];
        $this->roomAvailability = 'any';
        $this->selectedServiceIds = [];
        $this->assignedTherapists = [];
        $this->selectedTherapistId = null;
        $this->selectedTherapist = null;
        $this->notes = null;
        $this->showSuccessModal = false;
        $this->bookedDetails = [];
    }

    public function incrementMonth()
    {
        $this->currentCalendarMonth = Carbon::parse($this->currentCalendarMonth)->addMonth();
    }

    public function decrementMonth()
    {
        $this->currentCalendarMonth = Carbon::parse($this->currentCalendarMonth)->subMonth();
    }

    public function getHolidaysProperty()
    {
        $start = Carbon::parse($this->currentCalendarMonth)->startOfMonth();
        $end = Carbon::parse($this->currentCalendarMonth)->endOfMonth();

        $companyId = $this->selectedCompanyId();

        return Holiday::whereBetween('date', [$start, $end])
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->pluck('name', 'date')
            ->toArray();
    }

    public function getCompaniesProperty()
    {
        return Admin::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('subscription_expires_at')
                    ->orWhere('subscription_expires_at', '>=', now());
            })
            ->orderBy('name')
            ->get(['id', 'name', 'company_name', 'company_logo', 'company_address', 'email', 'phone_number']);
    }

    public function getSelectedCompanyProperty(): ?Admin
    {
        $companyId = $this->selectedCompanyId();
        if (! $companyId) {
            return null;
        }

        $admin = Admin::find($companyId);
        if (! $admin || ! $admin->is_active || ($admin->subscription_expires_at && now()->greaterThan($admin->subscription_expires_at))) {
            return null;
        }

        return $admin;
    }

    public function getTimezoneProperty()
    {
        $companyId = $this->selectedCompanyId();
        $settings = DB::table('admin_settings')->where('admin_id', $companyId)->first();

        return $settings?->timezone ?? 'Asia/Manila';
    }

    public function getCompanyHoursProperty(): array
    {
        $companyId = $this->selectedCompanyId();
        if (! $companyId) {
            return [
                'timezone' => 'Asia/Manila',
                'open_time' => '09:00:00',
                'close_time' => '22:00:00',
                'break_start' => null,
                'break_end' => null,
            ];
        }

        $settings = DB::table('admin_settings')->where('admin_id', $companyId)->first();

        return [
            'timezone' => $settings?->timezone ?? 'Asia/Manila',
            'open_time' => $settings?->open_time ?? '09:00:00',
            'close_time' => $settings?->close_time ?? '22:00:00',
            'break_start' => $settings?->break_start,
            'break_end' => $settings?->break_end,
        ];
    }

    public function selectCompany($companyId)
    {
        $admin = Admin::find($companyId);
        if (! $admin || ! $admin->is_active || ($admin->subscription_expires_at && now()->greaterThan($admin->subscription_expires_at))) {
            return;
        }
        $previousCompanyId = $this->selectedCompanyId();
        session(['selected_company_id' => $admin->id]);
        if ($previousCompanyId !== $admin->id) {
            $this->resetAfterCompanySelected();
        }
        $this->step = 2;
    }

    public function selectDate($date)
    {
        if (! $this->selectedCompanyId()) {
            return;
        }

        $companyId = $this->selectedCompanyId();
        if (Holiday::where('date', $date)
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->exists()) {
            return;
        }

        $this->selectedDate = $date;
        $this->selectedTime = null;
        $this->availableSlots = [];
        $this->selectedRoomId = null;
        $this->roomSelectionMade = false;
        $this->selectedServiceIds = [];
        $this->assignedTherapists = [];
        $this->selectedTherapistId = null;
        $this->selectedTherapist = null;
        $this->notes = null;
        $this->generateTimeSlots();
    }

    public function getServicesProperty()
    {
        $companyId = $this->selectedCompanyId();

        return Service::where('is_active', true)
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->get();
    }

    public function getTherapistsProperty()
    {
        $companyId = $this->selectedCompanyId();
        $query = Therapist::where('is_active', true)
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId));

        if (! empty($this->selectedServiceIds)) {
            foreach ($this->selectedServiceIds as $svcId) {
                $query->whereHas('services', function ($q) use ($svcId) {
                    $q->where('services.id', $svcId);
                });
            }
        }

        return $query->get();
    }

    protected function getAvailableTherapists()
    {
        $companyId = $this->selectedCompanyId();
        if (! $companyId || ! $this->selectedDate || ! $this->selectedTime || empty($this->selectedServiceIds)) {
            return collect();
        }

        return Therapist::query()
            ->where('is_active', true)
            ->where('admin_id', $companyId)
            ->whereHas('services', function ($q) {
                $q->whereIn('services.id', $this->selectedServiceIds);
            })
            ->with(['services' => fn ($q) => $q->select('services.id', 'services.name')])
            ->withCount('bookings')
            ->get();
    }

    public function getAvailableTherapistsForSelectionProperty()
    {
        return $this->getAvailableTherapists();
    }

    protected function getAvailableTherapistsForService($serviceId)
    {
        $companyId = $this->selectedCompanyId();
        if (! $companyId || ! $this->selectedDate || ! $this->selectedTime) {
            return collect();
        }

        return Therapist::query()
            ->where('is_active', true)
            ->where('admin_id', $companyId)
            ->whereHas('services', function ($q) use ($serviceId) {
                $q->where('services.id', $serviceId);
            })
            ->with(['services' => fn ($q) => $q->select('services.id', 'services.name')])
            ->withCount('bookings')
            ->get();
    }

    public function availableTherapistsForService($serviceId)
    {
        return $this->getAvailableTherapistsForService($serviceId);
    }

    protected function assignTherapistsForSelectedServices()
    {
        $this->assignedTherapists = [];
        foreach ($this->selectedServiceIds as $svcId) {
            $svcId = (int) $svcId;
            $candidates = $this->getAvailableTherapistsForService($svcId);
            $busy = $this->busyIntervalsByTherapist($candidates->pluck('id')->map(fn ($id) => (int) $id)->all());
            $picked = null;

            foreach ($candidates as $candidate) {
                $candidateId = (int) $candidate->id;
                if (! $this->therapistWouldBeInSessionForService($svcId, $candidateId, $busy)) {
                    $picked = $candidate;
                    break;
                }
            }

            $this->assignedTherapists[$svcId] = $picked ? ['id' => $picked->id, 'name' => $picked->name] : null;
        }
        $auto = collect($this->assignedTherapists)->firstWhere('id');
        if ($auto && ! $this->selectedTherapistId) {
            $this->selectedTherapistId = $auto['id'];
            $this->selectedTherapist = Therapist::find($auto['id']);
        }

        if ($this->activeServiceId === null && ! empty($this->selectedServiceIds)) {
            $this->activeServiceId = (int) $this->selectedServiceIds[0];
        }
    }

    public function setAssignedTherapist($serviceId, $therapistId)
    {
        $serviceId = (int) $serviceId;
        $therapistId = (int) $therapistId;

        $candidate = $this->getAvailableTherapistsForService($serviceId)->firstWhere('id', $therapistId);
        if (! $candidate) {
            return;
        }

        if ($this->therapistWouldBeInSessionForService($serviceId, $therapistId)) {
            session()->flash('message', 'This therapist is currently handling another session. Please pick a different therapist or time.');

            return;
        }

        $this->assignedTherapists[$serviceId] = ['id' => $candidate->id, 'name' => $candidate->name];
        $this->activeServiceId = $serviceId;

        $primaryTherapistId = null;
        if (! empty($this->selectedServiceIds)) {
            $firstServiceId = (int) $this->selectedServiceIds[0];
            $primaryTherapistId = (int) ($this->assignedTherapists[$firstServiceId]['id'] ?? $candidate->id);
        }

        $this->selectedTherapistId = $primaryTherapistId ?? $candidate->id;
        $this->selectedTherapist = Therapist::find($this->selectedTherapistId);
    }

    public function setActiveService(int $serviceId): void
    {
        $selected = array_values(array_unique(array_map('intval', $this->selectedServiceIds ?? [])));
        if (! in_array($serviceId, $selected, true)) {
            return;
        }

        $this->activeServiceId = $serviceId;
    }

    public function getTherapistsForActiveServiceProperty()
    {
        if (! $this->activeServiceId) {
            return collect();
        }

        return $this->getAvailableTherapistsForService($this->activeServiceId);
    }

    public function getTherapistsForSelectionProperty()
    {
        $companyId = $this->selectedCompanyId();
        if (! $companyId) {
            return collect();
        }

        return Therapist::query()
            ->where('is_active', true)
            ->where('admin_id', $companyId)
            ->with(['services' => fn ($q) => $q->select('services.id', 'services.name')])
            ->withCount('bookings')
            ->orderBy('name')
            ->get();
    }

    public function getTherapistStatusByIdForActiveServiceProperty(): array
    {
        $serviceId = (int) ($this->activeServiceId ?? 0);
        if (! $serviceId) {
            return [];
        }

        $therapists = $this->therapistsForSelection;
        if ($therapists->isEmpty()) {
            return [];
        }

        $busy = $this->busyIntervalsByTherapist($therapists->pluck('id')->map(fn ($id) => (int) $id)->all());

        $statuses = [];
        foreach ($therapists as $therapist) {
            $therapistId = (int) $therapist->id;
            $isCertified = $therapist->relationLoaded('services') && $therapist->services->contains('id', $serviceId);

            if (! $isCertified) {
                $statuses[$therapistId] = 'not_certified';

                continue;
            }

            $statuses[$therapistId] = $this->therapistWouldBeInSessionForService($serviceId, $therapistId, $busy)
                ? 'in_session'
                : 'available';
        }

        return $statuses;
    }

    public function toggleService($serviceId)
    {
        $serviceId = (int) $serviceId;
        $selected = array_values(array_unique(array_map('intval', $this->selectedServiceIds ?? [])));

        if (in_array($serviceId, $selected, true)) {
            $this->selectedServiceIds = array_values(array_diff($selected, [$serviceId]));
            unset($this->assignedTherapists[$serviceId]);
            if ($this->activeServiceId === $serviceId) {
                $this->activeServiceId = null;
            }
        } else {
            $this->selectedServiceIds = array_values(array_unique(array_merge($selected, [$serviceId])));
            if (! isset($this->assignedTherapists[$serviceId])) {
                $this->assignedTherapists[$serviceId] = null;
            }
        }

        if ($this->selectedDate && $this->selectedTime) {
            $durationMinutes = $this->selectedServicesDurationMinutes();
            if ($durationMinutes <= 0) {
                $durationMinutes = 60;
            }

            $hoursError = $this->validateWithinBusinessHours((string) $this->selectedDate, (string) $this->selectedTime, $durationMinutes);
            if ($hoursError) {
                session()->flash('message', $hoursError);
                $this->selectedTime = null;
                $this->availableSlots = [];
                $this->generateTimeSlots();
                $this->step = 2;
            }
        }
    }

    public function selectTherapist($therapistId)
    {
        $therapist = Therapist::query()
            ->whereKey($therapistId)
            ->where('is_active', true)
            ->first();

        if (! $therapist) {
            return;
        }

        $this->selectedTherapistId = (int) $therapist->id;
        $this->selectedTherapist = $therapist;

        foreach ($this->selectedServiceIds as $svcId) {
            $this->assignedTherapists[$svcId] = ['id' => $therapist->id, 'name' => $therapist->name];
        }
    }

    public function goToStep($step)
    {
        $step = (int) $step;
        if ($step < 1 || $step > 6) {
            return;
        }

        if ($step >= 2 && ! $this->selectedCompanyId()) {
            return;
        }

        if ($step >= 3 && (! $this->selectedDate || ! $this->selectedTime)) {
            return;
        }

        if ($step >= 5 && empty($this->selectedServiceIds)) {
            return;
        }

        if ($step >= 5) {
            $durationMinutes = $this->selectedServicesDurationMinutes();
            if ($durationMinutes <= 0) {
                $durationMinutes = 60;
            }

            if ($this->selectedDate && $this->selectedTime) {
                $hoursError = $this->validateWithinBusinessHours((string) $this->selectedDate, (string) $this->selectedTime, $durationMinutes);
                if ($hoursError) {
                    session()->flash('message', $hoursError);
                    $this->selectedTime = null;
                    $this->availableSlots = [];
                    $this->generateTimeSlots();
                    $this->step = 2;

                    return;
                }
            }

            if ($this->selectedRoomId) {
                if ($this->roomHasTimeConflict($this->selectedRoomId, $this->selectedDate, $this->selectedTime, $durationMinutes)) {
                    session()->flash('message', 'Selected room is not available for the full duration of the selected services. Please choose a different time or room.');
                    $this->selectedRoomId = null;
                    $this->roomSelectionMade = false;
                    $this->step = 3;

                    return;
                }
            } else {
                $roomId = $this->findAvailableRoomIdForRange($this->selectedDate, $this->selectedTime, $durationMinutes);
                if (! $roomId) {
                    session()->flash('message', 'No rooms are available for the selected time and services duration. Please choose a different time.');
                    $this->step = 2;

                    return;
                }
                $this->selectedRoomId = $roomId;
                $this->roomSelectionMade = true;
            }
        }

        if ($step === 5 && empty($this->assignedTherapists)) {
            $this->assignTherapistsForSelectedServices();
        }

        if ($step === 5 && $this->activeServiceId === null && ! empty($this->selectedServiceIds)) {
            $this->activeServiceId = (int) $this->selectedServiceIds[0];
        }

        if ($step >= 6 && ! $this->allSelectedServicesAssigned()) {
            foreach ($this->selectedServiceIds as $svcId) {
                $svcId = (int) $svcId;
                $assignedId = (int) ($this->assignedTherapists[$svcId]['id'] ?? 0);
                if (! $assignedId) {
                    $this->activeServiceId = $svcId;
                    break;
                }
            }

            session()->flash('message', 'Please select a therapist for each service before continuing.');

            return;
        }

        if ($step >= 6 && ! $this->validateTherapistAssignmentsAvailability()) {
            $this->step = 5;

            return;
        }

        $this->step = $step;
    }

    protected function allSelectedServicesAssigned(): bool
    {
        foreach ($this->selectedServiceIds as $svcId) {
            $svcId = (int) $svcId;
            $assignedId = (int) ($this->assignedTherapists[$svcId]['id'] ?? 0);
            if (! $assignedId) {
                return false;
            }
        }

        return true;
    }

    protected function validateTherapistAssignmentsAvailability(): bool
    {
        if (! $this->selectedDate || ! $this->selectedTime || empty($this->selectedServiceIds)) {
            return false;
        }

        $assignments = $this->serviceAssignmentsAsIds();
        $segmentsByTherapist = $this->segmentsByTherapist($assignments);
        $therapistIds = array_keys($segmentsByTherapist);
        if (empty($therapistIds)) {
            return false;
        }

        $busy = $this->busyIntervalsByTherapist($therapistIds);

        foreach ($segmentsByTherapist as $therapistId => $segments) {
            foreach ($segments as $segment) {
                [$startAt, $endAt] = $segment;
                foreach ($busy[$therapistId] ?? [] as $busyInterval) {
                    [$busyStart, $busyEnd] = $busyInterval;
                    if ($this->intervalsOverlap($startAt, $endAt, $busyStart, $busyEnd)) {
                        session()->flash('message', 'One or more selected therapists are already in session for the chosen time. Please adjust your therapist selection or choose a different time.');

                        return false;
                    }
                }
            }
        }

        return true;
    }

    protected function therapistWouldBeInSessionForService(int $serviceId, int $therapistId, ?array $busy = null): bool
    {
        if (! $this->selectedDate || ! $this->selectedTime) {
            return true;
        }

        $assignments = $this->serviceAssignmentsAsIds();
        $assignments[$serviceId] = $therapistId;

        $segmentsByTherapist = $this->segmentsByTherapist($assignments);
        $segments = $segmentsByTherapist[$therapistId] ?? [];
        if (empty($segments)) {
            return true;
        }

        $busy = $busy ?? $this->busyIntervalsByTherapist([$therapistId]);

        foreach ($segments as $segment) {
            [$startAt, $endAt] = $segment;
            foreach ($busy[$therapistId] ?? [] as $busyInterval) {
                [$busyStart, $busyEnd] = $busyInterval;
                if ($this->intervalsOverlap($startAt, $endAt, $busyStart, $busyEnd)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function serviceAssignmentsAsIds(): array
    {
        $assignments = [];
        foreach ($this->selectedServiceIds as $svcId) {
            $svcId = (int) $svcId;
            $assignments[$svcId] = (int) ($this->assignedTherapists[$svcId]['id'] ?? 0) ?: null;
        }

        return $assignments;
    }

    protected function segmentsByTherapist(array $serviceToTherapistId): array
    {
        $startAt = $this->selectedStartAt();
        if (! $startAt) {
            return [];
        }

        $durations = $this->selectedServiceDurationsById();
        $cursor = $startAt->copy();
        $segmentsByTherapist = [];

        foreach ($this->selectedServiceIds as $svcId) {
            $svcId = (int) $svcId;
            $minutes = (int) ($durations[$svcId] ?? 60);
            if ($minutes <= 0) {
                $minutes = 60;
            }

            $endAt = $cursor->copy()->addMinutes($minutes);
            $therapistId = $serviceToTherapistId[$svcId] ?? null;
            $therapistId = $therapistId ? (int) $therapistId : null;
            if ($therapistId) {
                $segmentsByTherapist[$therapistId] ??= [];
                $segmentsByTherapist[$therapistId][] = [$cursor->copy(), $endAt->copy()];
            }

            $cursor = $endAt;
        }

        return $segmentsByTherapist;
    }

    protected function selectedStartAt(): ?Carbon
    {
        if (! $this->selectedDate || ! $this->selectedTime) {
            return null;
        }

        $tz = $this->timezone ?? 'Asia/Manila';

        return Carbon::parse($this->selectedDate.' '.$this->selectedTime, $tz);
    }

    protected function selectedServiceDurationsById(): array
    {
        if (empty($this->selectedServiceIds)) {
            return [];
        }

        return Service::query()
            ->whereIn('id', $this->selectedServiceIds)
            ->pluck('duration_minutes', 'id')
            ->map(fn ($v) => (int) $v)
            ->toArray();
    }

    protected function busyIntervalsByTherapist(array $therapistIds): array
    {
        $therapistIds = array_values(array_unique(array_map('intval', array_filter($therapistIds))));
        if (empty($therapistIds) || ! $this->selectedDate) {
            return [];
        }

        $tz = $this->timezone ?? 'Asia/Manila';
        $companyId = $this->selectedCompanyId();

        $busy = [];
        $hasSegmentsTable = Schema::hasTable('booking_therapist_segments');

        if ($hasSegmentsTable) {
            $segments = DB::table('booking_therapist_segments')
                ->join('bookings', 'bookings.id', '=', 'booking_therapist_segments.booking_id')
                ->whereDate('booking_therapist_segments.starts_at', $this->selectedDate)
                ->where('bookings.status', '!=', 'cancelled')
                ->whereIn('booking_therapist_segments.therapist_id', $therapistIds)
                ->when($companyId, fn ($q) => $q->where('bookings.admin_id', $companyId))
                ->select([
                    'booking_therapist_segments.therapist_id',
                    'booking_therapist_segments.starts_at',
                    'booking_therapist_segments.ends_at',
                ])
                ->get();

            foreach ($segments as $row) {
                $id = (int) $row->therapist_id;
                $busy[$id] ??= [];
                $busy[$id][] = [Carbon::parse($row->starts_at, $tz), Carbon::parse($row->ends_at, $tz)];
            }
        }

        $bookings = Booking::query()
            ->whereDate('booking_date', $this->selectedDate)
            ->where('status', '!=', 'cancelled')
            ->whereIn('therapist_id', $therapistIds)
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->when($hasSegmentsTable, function ($q) {
                $q->whereNotExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('booking_therapist_segments')
                        ->whereColumn('booking_therapist_segments.booking_id', 'bookings.id');
                });
            })
            ->with(['services:id,duration_minutes'])
            ->get(['id', 'therapist_id', 'booking_date', 'booking_time', 'service_id']);

        $durationsByServiceId = Service::query()
            ->whereIn('id', $bookings->pluck('service_id')->filter()->all())
            ->pluck('duration_minutes', 'id')
            ->map(fn ($v) => (int) $v)
            ->toArray();

        foreach ($bookings as $booking) {
            $therapistId = (int) ($booking->therapist_id ?? 0);
            if (! $therapistId) {
                continue;
            }

            $startAt = Carbon::parse($booking->booking_date->format('Y-m-d').' '.(string) $booking->booking_time, $tz);
            $minutes = (int) $booking->services->sum('duration_minutes');
            if ($minutes <= 0) {
                $minutes = (int) ($durationsByServiceId[$booking->service_id] ?? 0);
            }
            if ($minutes <= 0) {
                $minutes = 60;
            }
            $endAt = $startAt->copy()->addMinutes($minutes);

            $busy[$therapistId] ??= [];
            $busy[$therapistId][] = [$startAt, $endAt];
        }

        return $busy;
    }

    protected function intervalsOverlap(Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd): bool
    {
        return $aStart->lt($bEnd) && $aEnd->gt($bStart);
    }

    public function getRoomsProperty()
    {
        $companyId = $this->selectedCompanyId();

        return Room::query()
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->orderBy('name')
            ->get();
    }

    public function getRoomBookingCountsProperty(): array
    {
        if (! $this->selectedDate) {
            return [];
        }

        $roomIds = $this->rooms->pluck('id')->all();
        if (empty($roomIds)) {
            return [];
        }

        $companyId = $this->selectedCompanyId();

        return Booking::query()
            ->whereDate('booking_date', $this->selectedDate)
            ->where('status', '!=', 'cancelled')
            ->whereIn('room_id', $roomIds)
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->selectRaw('room_id, COUNT(*) as aggregate')
            ->groupBy('room_id')
            ->pluck('aggregate', 'room_id')
            ->map(fn ($value) => (int) $value)
            ->toArray();
    }

    public function getFilteredRoomsProperty()
    {
        $rooms = $this->rooms;

        $needle = Str::of($this->roomSearch)->trim()->lower()->toString();
        if ($needle !== '') {
            $rooms = $rooms->filter(function (Room $room) use ($needle) {
                $haystack = Str::of(($room->name ?? '').' '.($room->code ?? ''))->lower()->toString();

                return Str::contains($haystack, $needle);
            });
        }

        if ($this->roomCapacity !== 'any') {
            [$min, $max] = match ($this->roomCapacity) {
                'private' => [1, 1],
                'small' => [2, 2],
                'medium' => [3, 4],
                'group' => [5, 6],
                default => [null, null],
            };

            if ($min !== null && $max !== null) {
                $rooms = $rooms->filter(function (Room $room) use ($min, $max) {
                    $roomMax = (int) ($room->capacity_max ?? 0);

                    if ($min === $max) {
                        return $roomMax === $max;
                    }

                    if ($min === 5) {
                        return $roomMax >= 5;
                    }

                    return $roomMax >= $min && $roomMax <= $max;
                });
            }
        }

        $amenities = array_values(array_filter(array_map('strval', $this->roomAmenities ?? [])));
        if (! empty($amenities)) {
            $rooms = $rooms->filter(function (Room $room) use ($amenities) {
                $roomAmenities = is_array($room->amenities) ? $room->amenities : [];
                foreach ($amenities as $amenity) {
                    if (! in_array($amenity, $roomAmenities, true)) {
                        return false;
                    }
                }

                return true;
            });
        }

        $durationMinutes = $this->selectedServicesDurationMinutes();
        if ($durationMinutes <= 0) {
            $durationMinutes = 60;
        }

        $rooms = $rooms->map(function (Room $room) use ($durationMinutes) {
            $occupiedUntil = null;
            if ($this->selectedDate && $this->selectedTime) {
                $occupiedUntil = $this->roomConflictEndAt((int) $room->id, (string) $this->selectedDate, (string) $this->selectedTime, $durationMinutes);
            }

            $computedStatus = $this->computeRoomStatusWithConflictEnd($room, $durationMinutes, $occupiedUntil);

            return [
                'room' => $room,
                'computed_status' => $computedStatus,
                'is_available' => $computedStatus === 'available',
                'occupied_until' => $occupiedUntil ? $occupiedUntil->format('H:i') : null,
            ];
        });

        if ($this->roomAvailability !== 'any') {
            $rooms = $rooms->filter(fn (array $item) => $item['computed_status'] === $this->roomAvailability);
        }

        $rooms = $rooms->sortBy(function (array $item) {
            $rank = match ($item['computed_status']) {
                'available' => 0,
                'occupied' => 1,
                'maintenance' => 2,
                default => 3,
            };

            return [$rank, (string) $item['room']->name];
        });

        return $rooms->values();
    }

    protected function computeRoomStatusWithConflictEnd(Room $room, int $durationMinutes, ?Carbon $occupiedUntil): string
    {
        $baseStatus = in_array($room->status, ['available', 'occupied', 'maintenance'], true)
            ? $room->status
            : 'available';

        if (! $room->is_active || $baseStatus === 'maintenance') {
            return 'maintenance';
        }

        if (! $this->selectedDate || ! $this->selectedTime) {
            return 'available';
        }

        return $occupiedUntil ? 'occupied' : 'available';
    }

    protected function computeRoomStatus(Room $room, int $durationMinutes): string
    {
        $baseStatus = in_array($room->status, ['available', 'occupied', 'maintenance'], true)
            ? $room->status
            : 'available';

        if (! $room->is_active || $baseStatus === 'maintenance') {
            return 'maintenance';
        }

        if (! $this->selectedDate || ! $this->selectedTime) {
            return 'available';
        }

        if ($this->roomHasTimeConflict((int) $room->id, (string) $this->selectedDate, (string) $this->selectedTime, $durationMinutes)) {
            return 'occupied';
        }

        return 'available';
    }

    protected function roomConflictEndAt(int $roomId, string $date, string $startTime, int $durationMinutes, ?int $ignoreBookingId = null): ?Carbon
    {
        $companyId = $this->selectedCompanyId();
        $tz = $this->timezone ?? 'Asia/Manila';

        $requestedStart = Carbon::parse($date.' '.$startTime, $tz);
        $requestedEnd = $requestedStart->copy()->addMinutes($durationMinutes);

        $bookings = Booking::query()
            ->with([
                'services:id,duration_minutes',
                'service:id,duration_minutes',
            ])
            ->whereDate('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('room_id', $roomId)
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->get(['id', 'booking_date', 'booking_time', 'service_id', 'room_id', 'admin_id', 'status']);

        $latestEnd = null;

        foreach ($bookings as $booking) {
            $existingStart = Carbon::parse($booking->booking_date->format('Y-m-d').' '.$booking->booking_time, $tz);
            $existingDuration = $this->bookingDurationMinutes($booking);
            $existingEnd = $existingStart->copy()->addMinutes($existingDuration);

            if ($requestedStart->lt($existingEnd) && $existingStart->lt($requestedEnd)) {
                $latestEnd = $latestEnd ? ($existingEnd->gt($latestEnd) ? $existingEnd : $latestEnd) : $existingEnd;
            }
        }

        return $latestEnd;
    }

    public function updatedSelectedDate($value)
    {
        $this->generateTimeSlots();
    }

    protected function getCompanyTimezone(): string
    {
        $companyId = $this->selectedCompanyId();
        $settings = $companyId ? DB::table('admin_settings')->where('admin_id', $companyId)->first() : null;

        return $settings?->timezone ?: 'Asia/Manila';
    }

    protected function getOperatingScheduleForDate(string $date): array
    {
        $companyId = $this->selectedCompanyId();
        $tz = $this->getCompanyTimezone();

        $settings = $companyId ? DB::table('admin_settings')->where('admin_id', $companyId)->first() : null;
        $fallbackOpen = $settings?->open_time ?? '09:00:00';
        $fallbackClose = $settings?->close_time ?? '22:00:00';
        $fallbackBreakStart = $settings?->break_start ?? null;
        $fallbackBreakEnd = $settings?->break_end ?? null;

        $dayOfWeekIso = Carbon::parse($date, $tz)->dayOfWeekIso;

        $hours = null;
        if ($companyId && Schema::hasTable('admin_operating_hours')) {
            $hoursQuery = AdminOperatingHour::query()
                ->where('admin_id', $companyId)
                ->where('day_of_week', $dayOfWeekIso);

            if (Schema::hasTable('admin_operating_breaks')) {
                $hoursQuery->with('breaks');
            }

            $hours = $hoursQuery->first();
        }

        $isClosed = (bool) ($hours?->is_closed ?? false);
        $open = $hours?->opens_at ?: $fallbackOpen;
        $close = $hours?->closes_at ?: $fallbackClose;

        $breaks = [];
        if ($hours && $hours->relationLoaded('breaks')) {
            foreach ($hours->breaks as $break) {
                $breaks[] = [
                    'label' => $break->label ?: 'Break',
                    'start' => (string) $break->starts_at,
                    'end' => (string) $break->ends_at,
                ];
            }
        } elseif ($fallbackBreakStart && $fallbackBreakEnd) {
            $breaks[] = [
                'label' => 'Break',
                'start' => (string) $fallbackBreakStart,
                'end' => (string) $fallbackBreakEnd,
            ];
        }

        return [
            'timezone' => $tz,
            'is_closed' => $isClosed,
            'open' => $open,
            'close' => $close,
            'breaks' => $breaks,
        ];
    }

    protected function validateWithinBusinessHours(string $date, string $time, int $durationMinutes): ?string
    {
        $schedule = $this->getOperatingScheduleForDate($date);
        if ($schedule['is_closed']) {
            return 'This company is closed on the selected day.';
        }

        $tz = $schedule['timezone'];
        $openAt = Carbon::parse($date.' '.$schedule['open'], $tz);
        $closeAt = Carbon::parse($date.' '.$schedule['close'], $tz);
        $startAt = Carbon::parse($date.' '.$time, $tz);
        $endAt = $startAt->copy()->addMinutes($durationMinutes);

        if ($startAt->lt($openAt) || $endAt->gt($closeAt)) {
            return 'Selected time is outside business hours.';
        }

        foreach ($schedule['breaks'] as $break) {
            $bStart = Carbon::parse($date.' '.$break['start'], $tz);
            $bEnd = Carbon::parse($date.' '.$break['end'], $tz);
            if ($startAt->lt($bEnd) && $endAt->gt($bStart)) {
                return 'Selected time overlaps with a break time.';
            }
        }

        return null;
    }

    public function generateTimeSlots()
    {
        if (! $this->selectedDate) {
            $this->availableSlots = [];
            $this->scheduleInfo = null;

            return;
        }

        $companyId = $this->selectedCompanyId();
        if ($companyId && Holiday::where('date', $this->selectedDate)->where('admin_id', $companyId)->exists()) {
            $this->availableSlots = [];
            $this->scheduleInfo = null;

            return;
        }

        $durationMinutes = $this->selectedServicesDurationMinutes();
        if ($durationMinutes <= 0) {
            $durationMinutes = 60;
        }

        $this->availableSlots = [];
        $schedule = $this->getOperatingScheduleForDate((string) $this->selectedDate);
        $tz = $schedule['timezone'];

        $this->scheduleInfo = $schedule;

        if ($schedule['is_closed']) {
            return;
        }

        $openAt = Carbon::parse($this->selectedDate.' '.$schedule['open'], $tz);
        $closeAt = Carbon::parse($this->selectedDate.' '.$schedule['close'], $tz);
        $latestStart = $closeAt->copy()->subMinutes($durationMinutes);

        $now = Carbon::now($tz);
        $isToday = Carbon::parse($this->selectedDate, $tz)->isSameDay($now);

        $breakIntervals = [];
        foreach ($schedule['breaks'] as $break) {
            $breakIntervals[] = [
                'label' => $break['label'],
                'start' => Carbon::parse($this->selectedDate.' '.$break['start'], $tz),
                'end' => Carbon::parse($this->selectedDate.' '.$break['end'], $tz),
            ];
        }

        $cursor = $openAt->copy();
        while ($cursor->lte($latestStart)) {
            if ($isToday && $cursor->lt($now)) {
                $cursor->addHour();

                continue;
            }

            $slotEnd = $cursor->copy()->addMinutes($durationMinutes);
            $disabled = false;
            $reason = null;

            foreach ($breakIntervals as $break) {
                if ($cursor->lt($break['end']) && $slotEnd->gt($break['start'])) {
                    $disabled = true;
                    $reason = 'Unavailable (break time)';
                    break;
                }
            }

            $this->availableSlots[] = [
                'time' => $cursor->format('H:i'),
                'booked' => false,
                'disabled' => $disabled,
                'reason' => $reason,
            ];

            $cursor->addHour();
        }
    }

    public function selectTime($time)
    {
        if (! $this->selectedCompanyId() || ! $this->selectedDate) {
            return;
        }

        $this->selectedTime = $time;
        $this->selectedRoomId = null;
        $this->roomSelectionMade = false;
        $this->selectedServiceIds = [];
        $this->assignedTherapists = [];
        $this->selectedTherapistId = null;
        $this->selectedTherapist = null;
        $this->notes = null;
        $this->step = 3;
    }

    public function clearDateTime(): void
    {
        if (! $this->selectedCompanyId()) {
            return;
        }

        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->availableSlots = [];
        $this->scheduleInfo = null;
        $this->selectedRoomId = null;
        $this->roomSelectionMade = false;
        $this->selectedServiceIds = [];
        $this->assignedTherapists = [];
        $this->selectedTherapistId = null;
        $this->selectedTherapist = null;
        $this->notes = null;
        $this->step = 2;
    }

    public function selectRoom($roomId = null)
    {
        if (! $this->selectedCompanyId() || ! $this->selectedDate || ! $this->selectedTime) {
            return;
        }

        $durationMinutes = $this->selectedServicesDurationMinutes();
        if ($durationMinutes <= 0) {
            $durationMinutes = 60;
        }

        if ($roomId) {
            $room = Room::query()->find($roomId);
            if (! $room || ! $room->is_active || $room->status === 'maintenance') {
                session()->flash('message', 'This room is not available.');

                return;
            }

            if ($this->roomHasTimeConflict($roomId, $this->selectedDate, $this->selectedTime, $durationMinutes)) {
                session()->flash('message', 'Not available for now');

                return;
            }
            $this->selectedRoomId = $roomId;
            $this->roomSelectionMade = true;
            $this->step = 4;

            return;
        }

        $autoRoomId = $this->findAvailableRoomIdForRange($this->selectedDate, $this->selectedTime, $durationMinutes);
        if (! $autoRoomId) {
            session()->flash('message', 'No rooms are available for the selected time and services duration.');

            return;
        }
        $this->selectedRoomId = $autoRoomId;
        $this->roomSelectionMade = true;
        $this->step = 4;
    }

    public function isRoomAvailable($roomId): bool
    {
        $room = Room::query()->find($roomId);
        if (! $room || ! $room->is_active || $room->status === 'maintenance') {
            return false;
        }

        $durationMinutes = $this->selectedServicesDurationMinutes();
        if ($durationMinutes <= 0) {
            $durationMinutes = 60;
        }

        return ! $this->roomHasTimeConflict((int) $roomId, (string) $this->selectedDate, (string) $this->selectedTime, (int) $durationMinutes);
    }

    public function toggleRoomMaintenance(int $roomId): void
    {
        $adminId = Auth::guard('admin')->id();
        if (! $adminId) {
            return;
        }

        $room = Room::query()
            ->whereKey($roomId)
            ->where('admin_id', $adminId)
            ->first();

        if (! $room) {
            return;
        }

        $room->status = $room->status === 'maintenance' ? 'available' : 'maintenance';
        $room->is_active = true;
        $room->save();
    }

    protected function selectedServicesDurationMinutes(): int
    {
        if (empty($this->selectedServiceIds)) {
            return 60;
        }

        $durations = Service::whereIn('id', $this->selectedServiceIds)->pluck('duration_minutes')->all();
        $total = 0;
        foreach ($durations as $minutes) {
            $minutes = (int) $minutes;
            $total += $minutes > 0 ? $minutes : 60;
        }

        return $total;
    }

    protected function roomHasTimeConflict(int $roomId, string $date, string $startTime, int $durationMinutes, ?int $ignoreBookingId = null): bool
    {
        $companyId = $this->selectedCompanyId();
        $tz = $this->timezone ?? 'Asia/Manila';

        $requestedStart = Carbon::parse($date.' '.$startTime, $tz);
        $requestedEnd = $requestedStart->copy()->addMinutes($durationMinutes);

        $bookings = Booking::query()
            ->with([
                'services:id,duration_minutes',
                'service:id,duration_minutes',
            ])
            ->whereDate('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('room_id', $roomId)
            ->when($companyId, fn ($q) => $q->where('admin_id', $companyId))
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->get(['id', 'booking_date', 'booking_time', 'service_id', 'room_id', 'admin_id', 'status']);

        foreach ($bookings as $booking) {
            $existingStart = Carbon::parse($booking->booking_date->format('Y-m-d').' '.$booking->booking_time, $tz);
            $existingDuration = $this->bookingDurationMinutes($booking);
            $existingEnd = $existingStart->copy()->addMinutes($existingDuration);

            if ($requestedStart->lt($existingEnd) && $existingStart->lt($requestedEnd)) {
                return true;
            }
        }

        return false;
    }

    protected function bookingDurationMinutes(Booking $booking): int
    {
        if ($booking->relationLoaded('services') && $booking->services->isNotEmpty()) {
            $total = 0;
            foreach ($booking->services as $service) {
                $minutes = (int) $service->duration_minutes;
                $total += $minutes > 0 ? $minutes : 60;
            }

            return max(60, $total);
        }

        if ($booking->relationLoaded('service') && $booking->service) {
            $minutes = (int) $booking->service->duration_minutes;

            return $minutes > 0 ? $minutes : 60;
        }

        return 60;
    }

    protected function findAvailableRoomIdForRange(string $date, string $startTime, int $durationMinutes): ?int
    {
        foreach ($this->rooms as $room) {
            if (! $room->is_active || $room->status === 'maintenance') {
                continue;
            }
            if (! $this->roomHasTimeConflict((int) $room->id, $date, $startTime, $durationMinutes)) {
                return (int) $room->id;
            }
        }

        return null;
    }

    public function book()
    {
        if (! $this->selectedCompanyId()) {
            return;
        }

        if (! $this->allSelectedServicesAssigned()) {
            $this->addError('selectedTherapistId', 'Please select a therapist for each service.');

            return;
        }

        $assignments = $this->serviceAssignmentsAsIds();
        $primaryTherapistId = null;
        if (! empty($this->selectedServiceIds)) {
            $firstServiceId = (int) $this->selectedServiceIds[0];
            $primaryTherapistId = (int) ($assignments[$firstServiceId] ?? 0) ?: null;
        }

        if ($primaryTherapistId) {
            $this->selectedTherapistId = $primaryTherapistId;
            $this->selectedTherapist = Therapist::find($primaryTherapistId);
        }

        $this->validate([
            'selectedServiceIds' => 'required|array|min:1',
            'selectedTherapistId' => 'required',
            'selectedDate' => 'required',
            'selectedTime' => 'required',
        ]);

        $companyId = $this->selectedCompanyId();
        $durationMinutes = $this->selectedServicesDurationMinutes();
        if ($durationMinutes <= 0) {
            $durationMinutes = 60;
        }

        $hoursError = $this->validateWithinBusinessHours((string) $this->selectedDate, (string) $this->selectedTime, $durationMinutes);
        if ($hoursError) {
            $this->addError('selectedTime', $hoursError);
            $this->step = 2;

            return;
        }

        if (! $this->validateTherapistAssignmentsAvailability()) {
            $this->step = 5;

            return;
        }

        if (! $this->selectedRoomId) {
            $roomId = $this->findAvailableRoomIdForRange($this->selectedDate, $this->selectedTime, $durationMinutes);
            if (! $roomId) {
                $this->addError('selectedTime', 'No rooms are available for the selected time and services duration.');

                return;
            }
            $this->selectedRoomId = $roomId;
        }

        if ($this->roomHasTimeConflict($this->selectedRoomId, $this->selectedDate, $this->selectedTime, $durationMinutes)) {
            $this->addError('selectedTime', 'This room is already booked within the selected time range.');

            return;
        }

        $assignments = $assignments ?? $this->serviceAssignmentsAsIds();

        $booking = Booking::create([
            'admin_id' => $companyId,
            'user_id' => Auth::id(),
            'therapist_id' => $this->selectedTherapistId,
            'room_id' => $this->selectedRoomId,
            'booking_date' => $this->selectedDate,
            'booking_time' => $this->selectedTime,
            'status' => 'pending',
            'notes' => $this->notes,
        ]);
        if (! empty($this->selectedServiceIds)) {
            $booking->services()->sync($this->selectedServiceIds);
        }

        if (Schema::hasTable('booking_service_therapist')) {
            foreach ($this->selectedServiceIds as $svcId) {
                $svcId = (int) $svcId;
                $therapistId = (int) ($assignments[$svcId] ?? 0) ?: null;
                DB::table('booking_service_therapist')->updateOrInsert(
                    [
                        'booking_id' => $booking->id,
                        'service_id' => $svcId,
                    ],
                    [
                        'therapist_id' => $therapistId,
                        'admin_id' => $companyId,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        if (Schema::hasTable('booking_therapist_segments')) {
            $segmentsByTherapist = $this->segmentsByTherapist($assignments);
            foreach ($segmentsByTherapist as $therapistId => $segments) {
                foreach ($segments as $segment) {
                    [$startsAt, $endsAt] = $segment;
                    DB::table('booking_therapist_segments')->insert([
                        'booking_id' => $booking->id,
                        'therapist_id' => $therapistId,
                        'admin_id' => $companyId,
                        'starts_at' => $startsAt->toDateTimeString(),
                        'ends_at' => $endsAt->toDateTimeString(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        UserLog::record(Auth::user(), 'booking_created', [
            'booking_id' => $booking->id,
            'admin_id' => $companyId,
            'room_id' => $this->selectedRoomId,
            'date' => (string) $this->selectedDate,
            'time' => (string) $this->selectedTime,
            'duration_minutes' => $durationMinutes,
            'service_ids' => array_values(array_map('intval', $this->selectedServiceIds)),
            'service_therapist_ids' => collect($assignments)
                ->mapWithKeys(fn ($therapistId, $serviceId) => [(int) $serviceId => $therapistId ? (int) $therapistId : null])
                ->toArray(),
        ]);

        $serviceNamesById = Service::whereIn('id', $this->selectedServiceIds)->pluck('name', 'id');
        $assignedPairs = [];
        foreach ($this->selectedServiceIds as $svcId) {
            $assignedName = $this->assignedTherapists[$svcId]['name'] ?? $this->selectedTherapist?->name;
            $assignedPairs[] = [
                'service' => $serviceNamesById[$svcId] ?? 'Service',
                'therapist' => $assignedName ?? 'Unassigned',
            ];
        }

        $this->bookedDetails = [
            'id' => $booking->id,
            'services' => Service::whereIn('id', $this->selectedServiceIds)->pluck('name')->toArray(),
            'service_therapists' => $assignedPairs,
            'room' => $booking->room?->name,
            'date' => Carbon::parse($this->selectedDate)->format('F j, Y'),
            'time' => $this->selectedTime,
            'total' => Service::whereIn('id', $this->selectedServiceIds)->sum('price'),
        ];
        $this->step = 6;

        $this->showSuccessModal = true;
    }

    public function finishBooking()
    {
        session()->flash('message', 'Booking successfully created!');

        return redirect()->route('dashboard');
    }

    public function back()
    {
        if ($this->step === 2) {
            $this->step = 1;

            return;
        }

        if ($this->step === 3) {
            $this->selectedRoomId = null;
            $this->roomSelectionMade = false;
            $this->assignedTherapists = [];
            $this->activeServiceId = null;
            $this->selectedTherapistId = null;
            $this->selectedTherapist = null;
            $this->notes = null;
            $this->step = 2;

            return;
        }

        if ($this->step === 4) {
            $this->assignedTherapists = [];
            $this->activeServiceId = null;
            $this->selectedTherapistId = null;
            $this->selectedTherapist = null;
            $this->notes = null;
            $this->step = 3;

            return;
        }

        if ($this->step === 5) {
            $this->selectedTherapistId = null;
            $this->selectedTherapist = null;
            $this->notes = null;
            $this->step = 4;

            return;
        }

        if ($this->step === 6) {
            $this->notes = null;
            $this->step = 5;

            return;
        }
    }

    public function render()
    {
        return view('livewire.booking-wizard');
    }
}
