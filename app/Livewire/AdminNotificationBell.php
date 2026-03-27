<?php

namespace App\Livewire;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminNotificationBell extends Component
{
    public int $notificationCount = 0;
    public $notificationBookings = [];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $adminId = Auth::guard('admin')->id();

        if (!$adminId) {
            $adminId = Auth::guard('super_admin')->id();
        }

        if (!$adminId) {
            return;
        }

        $this->notificationCount = Booking::query()
            ->where('admin_id', $adminId)
            ->where('status', 'pending')
            ->count();

        $this->notificationBookings = Booking::query()
            ->where('admin_id', $adminId)
            ->whereIn('status', ['pending', 'cancelled'])
            ->with(['user:id,name', 'service:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'user_id', 'service_id', 'status', 'created_at', 'booking_date', 'booking_time']);
    }

    public function render()
    {
        return view('livewire.admin-notification-bell');
    }
}
