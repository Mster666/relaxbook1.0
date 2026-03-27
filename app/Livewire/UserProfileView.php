<?php

namespace App\Livewire;

use App\Models\Booking;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserProfileView extends Component
{
    public function render()
    {
        $user = Auth::user();
        $tz = config('app.timezone', 'Asia/Manila');
        $since = Carbon::now($tz)->subDays(7);

        $recentBookings = Booking::query()
            ->where('user_id', $user->id)
            ->whereDate('booking_date', '>=', $since->toDateString())
            ->with([
                'room:id,name,code',
                'therapist:id,name',
                'service:id,name,price,duration_minutes',
                'services:id,name,price,duration_minutes',
                'bookingServiceTherapists.service:id,name',
                'bookingServiceTherapists.therapist:id,name',
            ])
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->get()
            ->filter(function (Booking $booking) use ($since, $tz) {
                $date = $booking->booking_date?->format('Y-m-d');
                $time = (string) ($booking->booking_time ?? '00:00');
                if (! $date) {
                    return false;
                }

                $at = Carbon::parse($date . ' ' . $time, $tz);

                return $at->greaterThanOrEqualTo($since);
            })
            ->values();

        return view('livewire.user-profile-view', [
            'user' => $user,
            'recentBookings' => $recentBookings,
            'recentSince' => $since,
        ]);
    }
}
