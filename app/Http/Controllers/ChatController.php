<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = auth()->user()->allConversations();

        return view('chat.index', compact('conversations'));
    }

    public function getMessages(int $conversationId): JsonResponse
    {
        $conversation = Conversation::findOrFail($conversationId);
        $userId = Auth::id();

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($conversation->user_one_id === $userId) {
            $conversation->update(['unread_count_user_one' => 0]);
        } else {
            $conversation->update(['unread_count_user_two' => 0]);
        }

        $messages = $conversation->messages()
            ->with('sender:id,name,profile_image')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages,
            'conversation' => $conversation->load('userOne:id,name,profile_image', 'userTwo:id,name,profile_image'),
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'body' => 'required|string|max:5000',
        ]);

        $senderId = Auth::id();

        if ((int) $request->recipient_id === $senderId) {
            return response()->json(['message' => 'Cannot send a message to yourself.'], 422);
        }

        $conversation = Conversation::getOrCreate($senderId, (int) $request->recipient_id);

        DB::beginTransaction();

        try {
            $message = $conversation->messages()->create([
                'sender_id' => $senderId,
                'body' => $request->body,
            ]);

            $preview = \Illuminate\Support\Str::limit($request->body, 50);

            $conversation->update([
                'last_message_at' => now(),
                'last_message_preview' => $preview,
            ]);

            if ($conversation->user_one_id === $senderId) {
                DB::table('conversations')
                    ->where('id', $conversation->id)
                    ->increment('unread_count_user_two');
            } else {
                DB::table('conversations')
                    ->where('id', $conversation->id)
                    ->increment('unread_count_user_one');
            }

            DB::commit();

            return response()->json([
                'message' => $message->load('sender:id,name,profile_image'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to send message.'], 500);
        }
    }

    public function editMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'body' => 'required|string|max:5000',
        ]);

        $message = Message::findOrFail($request->message_id);

        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $message->update([
            'body' => $request->body,
            'is_edited' => true,
        ]);

        return response()->json([
            'message' => $message->fresh()->load('sender:id,name,profile_image'),
        ]);
    }

    public function unsendMessage(int $messageId): JsonResponse
    {
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $message->update(['status' => 'unsent']);

        return response()->json([
            'message' => $message->fresh()->load('sender:id,name,profile_image'),
        ]);
    }

    public function deleteMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message_id' => 'required|exists:messages,id',
            'scope' => 'required|in:sender,recipient',
        ]);

        $message = Message::findOrFail($request->message_id);
        $userId = Auth::id();

        $conversation = $message->conversation;

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($request->scope === 'sender') {
            if ($message->sender_id !== $userId) {
                return response()->json(['message' => 'You can only delete your own messages.'], 403);
            }
            $message->update(['deleted_for_sender' => true]);
        } else {
            if ($message->sender_id === $userId) {
                return response()->json(['message' => 'Use unsend for your own messages.'], 422);
            }
            $message->update(['deleted_for_recipient' => true]);
        }

        return response()->json([
            'message' => 'Message deleted.',
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $userId = Auth::id();

        $count = Conversation::where(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)
                ->where('unread_count_user_one', '>', 0);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_two_id', $userId)
                ->where('unread_count_user_two', '>', 0);
        })->count();

        return response()->json(['count' => $count]);
    }

    public function getConversations(): JsonResponse
    {
        $userId = Auth::id();
        $conversations = auth()->user()->allConversations();

        $data = $conversations->map(function ($conversation) use ($userId) {
            $otherUser = $conversation->getOtherUser($userId);

            return [
                'id' => $conversation->id,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'profile_image' => $otherUser->profile_image,
                ],
                'last_message' => $conversation->last_message_preview,
                'last_message_at' => $conversation->last_message_at?->toISOString(),
                'unread_count' => $conversation->getUnreadCount($userId),
            ];
        });

        return response()->json(['conversations' => $data]);
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $userId = Auth::id();
        $query = $request->input('query');

        $users = User::where('id', '!=', $userId)
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'email', 'profile_image', 'role')
            ->limit(20)
            ->get();

        return response()->json(['users' => $users]);
    }
}
