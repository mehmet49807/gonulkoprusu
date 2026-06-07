<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private MessageConversationService $conversations) {}

    public function conversations(Request $request): JsonResponse
    {
        $viewer = $request->user();

        $conversations = $this->conversations->buildConversations($viewer)
            ->map(fn (array $item) => [
                'user' => $item['user']->toPublicArray(),
                'last_message' => $item['last_message'],
                'last_message_at' => $item['last_message_at']?->toIso8601String(),
                'message_count' => $item['message_count'],
                'unread_count' => $item['unread_count'],
            ])
            ->values();

        return response()->json(['success' => true, 'data' => ['conversations' => $conversations]]);
    }

    public function messages(Request $request, int $userId): JsonResponse
    {
        $viewer = $request->user();
        $partner = User::findOrFail($userId);

        if (!$this->conversations->canOpenChat($viewer, $partner)) {
            return response()->json(['success' => false, 'message' => 'Bu sohbete erişiminiz yok.'], 403);
        }

        $perPage = min((int) $request->get('per_page', 50), 100);

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

        $this->conversations->markAsRead($viewer, $partner);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function send(Request $request, int $userId): JsonResponse
    {
        $request->validate(['message_text' => 'required|string|max:2000']);

        $sender = $request->user();
        $receiver = User::findOrFail($userId);

        if (!$this->conversations->canSendTo($sender, $receiver)) {
            return response()->json([
                'success' => false,
                'message' => 'Mesaj göndermek için premium üyelik, aktif deneme süresi veya geçerli bir sohbet hakkı gereklidir.',
            ], 403);
        }

        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $userId,
            'message_text' => $request->message_text,
        ]);

        return response()->json(['success' => true, 'data' => $message], 201);
    }
}
