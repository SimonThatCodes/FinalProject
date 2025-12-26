<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $chats = Chat::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->with(['user1', 'user2', 'pet', 'messages' => function($query) {
                $query->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json($chats);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user2_id' => 'required|exists:users,id',
            'pet_id' => 'nullable|exists:pets,id',
        ]);

        $user1Id = $request->user()->id;
        $user2Id = $request->user2_id;

        // Check if chat already exists
        $chat = Chat::where(function($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user1Id)->where('user2_id', $user2Id);
        })->orWhere(function($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user2Id)->where('user2_id', $user1Id);
        })->first();

        if ($chat) {
            return response()->json($chat->load(['user1', 'user2', 'pet']));
        }

        $chat = Chat::create([
            'user1_id' => $user1Id,
            'user2_id' => $user2Id,
            'pet_id' => $request->pet_id,
        ]);

        return response()->json($chat->load(['user1', 'user2', 'pet']), 201);
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->id;

        $chat = Chat::where('id', $id)
            ->where(function($query) use ($userId) {
                $query->where('user1_id', $userId)
                      ->orWhere('user2_id', $userId);
            })
            ->with(['user1', 'user2', 'pet', 'messages.sender'])
            ->firstOrFail();

        // Mark messages as read
        Message::where('chat_id', $chat->id)
            ->where('sender_id', '!=', $userId)
            ->update(['read' => true]);

        return response()->json($chat);
    }
}
