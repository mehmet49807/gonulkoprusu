<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\GenderFilterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private GenderFilterService $genderFilter) {}

    public function conversations(Request $request): JsonResponse
    {
        $viewer = $request->user();
        $viewerId = $viewer->id;

        $latestIds = Message::query()
            ->selectRaw('MAX(id) as latest_id')
            ->where(function ($q) use ($viewerId) {
                $q->where('sender_id', $viewerId)
                    ->orWhere('receiver_id', $viewerId);
            })
            ->groupByRaw(
                'CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END',
                [$viewerId]
            )
            ->pluck('latest_id');

        $conversations = Message::whereIn('id', $latestIds)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($msg) use ($viewer, $viewerId) {
                $otherId = $msg->sender_id === $viewerId ? $msg->receiver_id : $msg->sender_id;
                $other = User::find($otherId);

                if (!$other || $other->role !== 'user' || $other->gender === $viewer->gender) {
                    return null;
                }

                $visible = User::where('id', $other->id)
                    ->where(function ($q) use ($viewer) {
                        $this->genderFilter->applyDiscoveryFilters($q, $viewer);
                    })
                    ->exists();

                if (!$visible) {
                    return null;
                }

                return [
                    'user' => $other->toPublicArray(),
                    'last_message' => $msg->message_text,
                    'last_message_at' => $msg->created_at?->toIso8601String(),
                    'unread_count' => Message::where('sender_id', $otherId)
                        ->where('receiver_id', $viewerId)
                        ->where('is_read', false)
                        ->count(),
                ];
            })
            ->filter()
            ->values();

        return response()->json(['success' => true, 'data' => ['conversations' => $conversations]]);
    }

    public function messages(Request $request, int $userId): JsonResponse
    {
        $viewer = $request->user();
        $perPage = min((int) $request->get('per_page', 50), 100);

        $partner = User::findOrFail($userId);

        if ($partner->gender === $viewer->gender) {
            return response()->json(['success' => false, 'message' => 'Yalnızca karşı cinsle mesajlaşabilirsiniz.'], 403);
        }

        $messages = Message::query()
            ->where(function ($q) use ($viewer, $userId) {
                $q->where('sender_id', $viewer->id)->where('receiver_id', $userId);
            })
            ->orWhere(function ($q) use ($viewer, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $viewer->id);
            })
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate($perPage);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function send(Request $request, int $userId): JsonResponse
    {
        $request->validate(['message_text' => 'required|string|max:2000']);

        $sender = $request->user();

        if (!$sender->canSendMessages()) {
            return response()->json([
                'success' => false,
                'message' => 'Mesaj göndermek için premium üyelik veya aktif deneme süresi gereklidir.',
            ], 403);
        }

        $receiver = User::findOrFail($userId);

        if ($receiver->gender === $sender->gender) {
            return response()->json(['success' => false, 'message' => 'Yalnızca karşı cinsle mesajlaşabilirsiniz.'], 403);
        }

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $userId,
            'message_text' => $request->message_text,
        ]);

        return response()->json(['success' => true, 'data' => $message], 201);
    }
}
