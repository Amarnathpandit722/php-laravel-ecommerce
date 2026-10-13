<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    // demo — replace with real DB queries on a notifications table
    public function index()
    {
        return response()->json([
            'notifications' => [
                ['id' => 1, 'icon' => '🛒', 'message' => 'New order #1042 placed', 'created_at' => now()->subMinutes(3), 'read_at' => null],
                ['id' => 2, 'icon' => '👤', 'message' => 'New customer registered', 'created_at' => now()->subMinutes(12), 'read_at' => null],
                ['id' => 3, 'icon' => '⚠️', 'message' => 'Low stock: Bluetooth Headphones', 'created_at' => now()->subHour(), 'read_at' => now()],
            ],
        ]);
    }

    public function markRead($id)
    {
        return response()->json(['ok' => true]);
    }
    public function markAllRead()
    {
        return response()->json(['ok' => true]);
    }
}
