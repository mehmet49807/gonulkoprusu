<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremiumSubscription;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalUsers = User::where('role', 'user')->count();
        $onlineUsers = User::where('role', 'user')
            ->where('last_active_at', '>=', now()->subMinutes(User::ONLINE_WINDOW_MINUTES))
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'online_users' => $onlineUsers,
                'offline_users' => max(0, $totalUsers - $onlineUsers),
                'pending_reports' => Report::where('status', 'pending')->count(),
                'active_premium' => PremiumSubscription::active()->count(),
                'revenue_tl' => PremiumSubscription::sum('price_tl'),
            ],
        ]);
    }
}
