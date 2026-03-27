<?php

namespace App\Livewire;

use App\Models\Booking;
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
