<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\GenderFilterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MessagePageController extends Controller
{
    public function __construct(private GenderFilterService $genderFilter) {}

    public function index(Request $request): View
    {
        $viewer = $request->user();
        $conversations = $this->buildConversations($viewer);

        return view('web.messages.index', [
            'viewer' => $viewer,
            'conversations' => $conversations,
        ]);
    }

    public function show(Request $request, string $username): View|RedirectResponse
    {
        $viewer = $request->user();
        $partner = $this->resolveChatPartner($viewer, $username);

        if (!$partner) {
            abort(404);
        }

        $messages = $this->messagesBetween($viewer, $partner);

        Message::where('sender_id', $partner->id)
            ->where('receiver_id', $viewer->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('web.messages.show', [
            'viewer' => $viewer,
            'partner' => $partner,
            'messages' => $messages,
            'canSendMessages' => $viewer->canSendMessages(),
        ]);
    }

    public function store(Request $request, string $username): RedirectResponse
    {
        $request->validate([
            'message_text' => 'required|string|max:2000',
        ], [
            'message_text.required' => 'Mesaj yazın.',
            'message_text.max' => 'Mesaj en fazla 2000 karakter olabilir.',
        ]);

        $viewer = $request->user();

        if (!$viewer->canSendMessages()) {
            return back()->withErrors([
                'message_text' => 'Mesaj göndermek için premium üyelik veya aktif deneme süresi gereklidir.',
            ]);
        }

        $partner = $this->resolveChatPartner($viewer, $username);

        if (!$partner) {
            abort(404);
        }

        Message::create([
            'sender_id' => $viewer->id,
            'receiver_id' => $partner->id,
            'message_text' => $request->message_text,
        ]);

        return redirect()
            ->route('messages.show', $partner->username)
            ->with('success', 'Mesaj gönderildi.');
    }

    private function resolveChatPartner(User $viewer, string $username): ?User
    {
        $partner = User::where('username', $username)->where('role', 'user')->first();

        if (!$partner || $partner->id === $viewer->id) {
            return null;
        }

        if ($partner->gender === $viewer->gender) {
            return null;
        }

        if (!$this->isVisiblePartner($viewer, $partner)) {
            return null;
        }

        return $partner;
    }

    private function isVisiblePartner(User $viewer, User $partner): bool
    {
        return User::where('id', $partner->id)
            ->where(function ($q) use ($viewer) {
                $this->genderFilter->applyDiscoveryFilters($q, $viewer);
            })
            ->exists();
    }

    private function messagesBetween(User $viewer, User $partner): Collection
    {
        return Message::query()
            ->where(function ($q) use ($viewer, $partner) {
                $q->where('sender_id', $viewer->id)->where('receiver_id', $partner->id);
            })
            ->orWhere(function ($q) use ($viewer, $partner) {
                $q->where('sender_id', $partner->id)->where('receiver_id', $viewer->id);
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    private function buildConversations(User $viewer): Collection
    {
        $latestIds = Message::query()
            ->selectRaw('MAX(id) as latest_id')
            ->where(function ($q) use ($viewer) {
                $q->where('sender_id', $viewer->id)
                    ->orWhere('receiver_id', $viewer->id);
            })
            ->groupByRaw(
                'CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END',
                [$viewer->id]
            )
            ->pluck('latest_id');

        if ($latestIds->isEmpty()) {
            return collect();
        }

        $latestMessages = Message::whereIn('id', $latestIds)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $conversations = collect();

        foreach ($latestMessages as $msg) {
            $partnerId = $msg->sender_id === $viewer->id ? $msg->receiver_id : $msg->sender_id;
            $partner = User::find($partnerId);

            if (!$partner || $partner->role !== 'user') {
                continue;
            }

            if (!$this->isVisiblePartner($viewer, $partner)) {
                continue;
            }

            $conversations->push([
                'user' => $partner,
                'last_message' => $msg->message_text,
                'last_message_at' => $msg->created_at,
                'unread_count' => Message::where('sender_id', $partnerId)
                    ->where('receiver_id', $viewer->id)
                    ->where('is_read', false)
                    ->count(),
            ]);
        }

        return $conversations;
    }
}
