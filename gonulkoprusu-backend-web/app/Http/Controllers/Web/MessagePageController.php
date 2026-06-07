<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessagePageController extends Controller
{
    public function __construct(private MessageConversationService $conversations) {}

    public function index(Request $request): View
    {
        $viewer = $request->user();

        return view('web.messages.index', [
            'viewer' => $viewer,
            'conversations' => $this->conversations->buildConversations($viewer),
        ]);
    }

    public function show(Request $request, string $username): View|RedirectResponse
    {
        $viewer = $request->user();
        $partner = $this->resolvePartner($viewer, $username);

        if (!$partner) {
            abort(404);
        }

        $messages = $this->conversations->messagesBetween($viewer, $partner);
        $this->conversations->markAsRead($viewer, $partner);

        return view('web.messages.show', [
            'viewer' => $viewer,
            'partner' => $partner,
            'messages' => $messages,
            'canSendMessages' => $this->conversations->canSendTo($viewer, $partner),
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
        $partner = $this->resolvePartner($viewer, $username);

        if (!$partner) {
            abort(404);
        }

        if (!$this->conversations->canSendTo($viewer, $partner)) {
            return back()->withErrors([
                'message_text' => 'Mesaj göndermek için premium üyelik, aktif deneme süresi veya geçerli bir sohbet hakkı gereklidir.',
            ]);
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

    private function resolvePartner(User $viewer, string $username): ?User
    {
        $partner = User::where('username', $username)->where('role', 'user')->first();

        if (!$partner || !$this->conversations->canOpenChat($viewer, $partner)) {
            return null;
        }

        return $partner;
    }
}
