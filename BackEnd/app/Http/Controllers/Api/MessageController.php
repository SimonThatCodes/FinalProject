<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:1000',
        ]);

        $userId = $request->user()->id;

        // Verify user is part of the chat
        $chat = Chat::where('id', $request->chat_id)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        $message = Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => $userId,
            'message' => $request->message,
            'read' => false,
        ]);

        // Update chat's last_message_at
        $chat->update(['last_message_at' => now()]);

        return response()->json($message->load('sender'), 201);
    }

    public function getMessages(Request $request, $chatId)
    {
        $userId = $request->user()->id;

        // Verify user is part of the chat
        $chat = Chat::where('id', $chatId)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->firstOrFail();

        $messages = Message::where('chat_id', $chatId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('chat_id', $chatId)
            ->where('sender_id', '!=', $userId)
            ->update(['read' => true]);

        return response()->json($messages);
    }
}
