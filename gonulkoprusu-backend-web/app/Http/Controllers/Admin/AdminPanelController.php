<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminBroadcast;
use App\Models\Message;
use App\Models\PremiumSubscription;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPanelController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'stats' => $this->dashboardStatsData(),
            'chartData' => $this->dashboardChartData(),
        ]);
    }

    public function dashboardStats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $this->dashboardStatsData(),
                'charts' => $this->dashboardChartData(),
                'updated_at' => now()->format('d.m.Y H:i:s'),
            ],
        ]);
    }

    private function dashboardStatsData(): array
    {
        $totalUsers = User::where('role', 'user')->count();
        $onlineUsers = $this->onlineUsersQuery()->count();

        return [
            'total_users' => $totalUsers,
            'online_users' => $onlineUsers,
            'offline_users' => max(0, $totalUsers - $onlineUsers),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'active_premium' => PremiumSubscription::active()->count(),
            'revenue_tl' => (float) PremiumSubscription::sum('price_tl'),
            'active_male' => $this->activeUsersQuery('male')->count(),
            'active_female' => $this->activeUsersQuery('female')->count(),
        ];
    }

    private function dashboardChartData(int $days = 14): array
    {
        return [
            'labels' => $this->chartDayLabels($days),
            'user_signups' => $this->dailyCounts(User::class, $days, ['role' => 'user']),
            'messages' => $this->dailyCounts(Message::class, $days),
            'premium_sales' => $this->dailyCounts(PremiumSubscription::class, $days),
            'gender' => [
                'male' => $this->activeUsersQuery('male')->count(),
                'female' => $this->activeUsersQuery('female')->count(),
                'banned' => User::where('role', 'user')->where('is_banned', true)->count(),
            ],
        ];
    }

    private function activeUsersQuery(string $gender)
    {
        return User::where('role', 'user')
            ->where('is_banned', false)
            ->where('gender', $gender);
    }

    private function onlineUsersQuery()
    {
        return User::where('role', 'user')
            ->where('last_active_at', '>=', now()->subMinutes(User::ONLINE_WINDOW_MINUTES));
    }

    private function chartDayLabels(int $days): array
    {
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('d.m');
        }

        return $labels;
    }

    private function dailyCounts(string $model, int $days, array $where = []): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $query = $model::where('created_at', '>=', $start);

        foreach ($where as $column => $value) {
            $query->where($column, $value);
        }

        $raw = $query
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $counts = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $counts[] = (int) ($raw[$day] ?? 0);
        }

        return $counts;
    }

    public function users(Request $request): View
    {
        $users = User::where('role', 'user')->latest()->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function messages(): View
    {
        $messages = Message::with(['sender', 'receiver'])->latest()->paginate(50);

        return view('admin.messages', compact('messages'));
    }

    public function reports(): View
    {
        $reports = Report::with(['reporter', 'reported'])->latest()->paginate(20);

        return view('admin.reports', compact('reports'));
    }

    public function premium(): View
    {
        $tierDistribution = PremiumSubscription::active()
            ->select('package_type', DB::raw('count(*) as count'))
            ->groupBy('package_type')
            ->pluck('count', 'package_type');

        return view('admin.premium', [
            'activeCount' => PremiumSubscription::active()->count(),
            'tierDistribution' => $tierDistribution,
            'totalRevenue' => PremiumSubscription::sum('price_tl'),
            'subscriptions' => PremiumSubscription::with('user')->latest()->limit(20)->get(),
        ]);
    }

    public function broadcasts(): View
    {
        $broadcasts = AdminBroadcast::with('admin')->latest()->paginate(20);

        return view('admin.broadcasts', compact('broadcasts'));
    }

    public function updateUser(Request $request, User $user)
    {
        if ($user->role !== 'user') {
            abort(404);
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'is_banned' => 'nullable|boolean',
            'banned_reason' => 'nullable|string|max:500',
        ]);

        $isBanned = $request->boolean('is_banned');

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'city' => $request->city,
            'district' => $request->district,
            'is_banned' => $isBanned,
            'banned_at' => $isBanned ? now() : null,
            'banned_reason' => $isBanned ? $request->banned_reason : null,
        ]);

        return redirect()->route('admin.users')->with('success', 'Kullanıcı güncellendi.');
    }

    public function unbanUser(User $user)
    {
        if ($user->role !== 'user') {
            abort(404);
        }

        $user->update([
            'is_banned' => false,
            'banned_at' => null,
            'banned_reason' => null,
        ]);

        return redirect()->route('admin.users')->with('success', 'Kullanıcının banı kaldırıldı.');
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message_text' => 'required|string',
            'target_gender' => 'required|in:all,male,female',
        ]);

        $query = User::where('role', 'user');
        if ($request->target_gender !== 'all') {
            $query->where('gender', $request->target_gender);
        }

        AdminBroadcast::create([
            'admin_id' => $request->user()->id,
            'title' => $request->title,
            'message_text' => $request->message_text,
            'target_gender' => $request->target_gender,
            'sent_count' => $query->count(),
        ]);

        return redirect()->route('admin.broadcasts')->with('success', 'Duyuru gönderildi.');
    }
}
