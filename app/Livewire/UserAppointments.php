<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\CompanyRating;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class UserAppointments extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';
    public string $search = '';
    public string $viewMode = 'list';
    public int $perPage = 5;
    public ?int $viewBookingId = null;

    public bool $rateModalOpen = false;
    public ?int $rateBookingId = null;
    public ?int $rateCompanyId = null;
    public ?string $rateCompanyName = null;
    public int $rateValue = 5;
    public string $rateComment = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
        'viewMode' => ['except' => 'list'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $allowed = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
        if (! in_array($status, $allowed, true)) {
            return;
        }

        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        $allowed = ['list', 'compact'];
        if (! in_array($mode, $allowed, true)) {
            return;
        }

        $this->viewMode = $mode;
    }

    public function viewBooking(int $bookingId): void
    {
        $this->viewBookingId = $bookingId;
    }

    public function closeView(): void
    {
        $this->viewBookingId = null;
    }

    public function openRatingModal(int $bookingId): void
    {
        $booking = Booking::query()
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->with('admin:id,name,company_name')
            ->first();

        if (! $booking || (string) $booking->status !== 'completed') {
            session()->flash('error', 'You can only rate a completed appointment.');

            return;
        }

        $adminId = (int) $booking->admin_id;
        if (! $adminId) {
            session()->flash('error', 'Company not found for this booking.');

            return;
        }

        $this->rateBookingId = (int) $booking->id;
        $this->rateCompanyId = $adminId;
        $this->rateCompanyName = (string) (($booking->admin?->company_name ?: $booking->admin?->name) ?: 'Company');

        $existing = CompanyRating::query()
            ->where('admin_id', $adminId)
            ->where('user_id', Auth::id())
            ->first();

        $this->rateValue = $existing ? max(1, min(5, (int) $existing->rating)) : 5;
        $this->rateComment = $existing ? (string) ($existing->comment ?? '') : '';
        $this->rateModalOpen = true;
    }

    public function closeRatingModal(): void
    {
        $this->rateModalOpen = false;
        $this->rateBookingId = null;
        $this->rateCompanyId = null;
        $this->rateCompanyName = null;
        $this->rateValue = 5;
        $this->rateComment = '';
    }

    public function saveRating(): void
    {
        $this->resetErrorBag();

        $adminId = (int) ($this->rateCompanyId ?? 0);
        $bookingId = (int) ($this->rateBookingId ?? 0);
        $rating = (int) $this->rateValue;
        $comment = trim($this->rateComment);

        if (! $adminId || ! $bookingId) {
            session()->flash('error', 'Rating context is missing.');
            $this->closeRatingModal();

            return;
        }

        if ($rating < 1 || $rating > 5) {
            $this->addError('rateValue', 'Rating must be between 1 and 5.');

            return;
        }

        $userId = (int) Auth::id();
        $hasCompleted = Booking::query()
            ->where('user_id', $userId)
            ->where('admin_id', $adminId)
            ->where('status', 'completed')
            ->exists();

        if (! $hasCompleted) {
            session()->flash('error', 'You can only rate companies you have completed an appointment with.');
            $this->closeRatingModal();

            return;
        }

        $ratingRow = CompanyRating::query()->updateOrCreate(
            [
                'admin_id' => $adminId,
                'user_id' => $userId,
            ],
            [
                'booking_id' => $bookingId,
                'rating' => $rating,
                'comment' => $comment !== '' ? $comment : null,
            ],
        );

        UserLog::record(Auth::user(), 'company_rated', [
            'company_admin_id' => $adminId,
            'booking_id' => $bookingId,
            'rating' => (int) $ratingRow->rating,
        ]);

        session()->flash('message', 'Thank you! Your rating was saved.');
        $this->closeRatingModal();
    }

    public function render()
    {
        $userId = Auth::id();

        $countsByStatus = Booking::query()
            ->where('user_id', $userId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts = [
            'total' => array_sum(array_map('intval', $countsByStatus)),
            'pending' => (int) ($countsByStatus['pending'] ?? 0),
            'confirmed' => (int) ($countsByStatus['confirmed'] ?? 0),
            'completed' => (int) ($countsByStatus['completed'] ?? 0),
            'cancelled' => (int) ($countsByStatus['cancelled'] ?? 0),
        ];

        $query = Booking::query()
            ->where('user_id', $userId)
            ->with([
                'admin:id,name,company_name',
                'room:id,name,code',
                'therapist:id,name',
                'service:id,name,price,icon,duration_minutes',
                'services:id,name,price,icon,duration_minutes',
                'bookingServiceTherapists.service:id,name',
                'bookingServiceTherapists.therapist:id,name',
            ])
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $search = trim($this->search);
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('service', fn ($sq) => $sq->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('services', fn ($sq) => $sq->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('therapist', fn ($tq) => $tq->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('room', fn ($rq) => $rq->where('name', 'like', '%' . $search . '%')->orWhere('code', 'like', '%' . $search . '%'))
                    ->orWhere('id', 'like', '%' . $search . '%');
            });
        }

        $bookings = $query->paginate($this->perPage);

        $viewBooking = null;
        if ($this->viewBookingId) {
            $viewBooking = Booking::query()
                ->where('id', $this->viewBookingId)
                ->where('user_id', $userId)
                ->with([
                    'admin:id,name,company_name',
                    'room:id,name,code',
                    'therapist:id,name',
                    'service:id,name,price,icon,duration_minutes',
                    'services:id,name,price,icon,duration_minutes',
                    'bookingServiceTherapists.service:id,name',
                    'bookingServiceTherapists.therapist:id,name',
                ])
                ->first();
        }

        return view('livewire.user-appointments', [
            'bookings' => $bookings,
            'counts' => $counts,
            'viewBooking' => $viewBooking,
        ]);
    }

    public function cancelBooking($bookingId)
    {
        $bookingId = (int) $bookingId;
        $booking = Booking::query()
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $booking) {
            session()->flash('error', 'Booking not found.');

            return;
        }

        if ($booking->status === 'cancelled') {
            session()->flash('error', 'Booking is already cancelled.');

            return;
        }

        if (! in_array($booking->status, ['pending', 'confirmed'], true)) {
            session()->flash('error', 'This booking cannot be cancelled.');

            return;
        }

        try {
            $bookingDateTime = Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->booking_time, config('app.timezone', 'Asia/Manila'));
            if ($bookingDateTime->isPast()) {
                session()->flash('error', 'Cannot cancel past bookings.');

                return;
            }
        } catch (\Throwable) {
        }

        $statusFrom = (string) $booking->status;
        $booking->update(['status' => 'cancelled']);
        UserLog::record(Auth::user(), 'booking_cancelled', [
            'booking_id' => $booking->id,
            'admin_id' => $booking->admin_id,
            'date' => (string) $booking->booking_date?->format('Y-m-d'),
            'time' => (string) $booking->booking_time,
            'status_from' => $statusFrom,
            'status_to' => 'cancelled',
        ]);
        session()->flash('message', 'Booking cancelled successfully.');
    }

    public function bookAgain(int $bookingId)
    {
        $booking = Booking::query()
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $booking) {
            session()->flash('error', 'Booking not found.');

            return;
        }

        return redirect()->route('dashboard');
    }
}
