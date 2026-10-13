<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    //
    public function index()
    {
        // Demo conversations — replace with real DB later
        $conversations = collect([
            (object) [
                'id' => 1,
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'last_message' => 'Thanks for the quick response!',
                'unread' => 2,
                'updated_at' => now()->subMinutes(5),
            ],
            (object) [
                'id' => 2,
                'name' => 'Mike Chen',
                'email' => 'mike@example.com',
                'last_message' => 'When will my order ship?',
                'unread' => 0,
                'updated_at' => now()->subHours(2),
            ],
            (object) [
                'id' => 3,
                'name' => 'Emma Wilson',
                'email' => 'emma@example.com',
                'last_message' => 'Received. Thanks!',
                'unread' => 0,
                'updated_at' => now()->subDay(),
            ],
        ]);

        return view('messages.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = (object) [
            'id' => $id,
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
        ];

        $messages = collect([
            (object) ['id' => 1, 'from' => 'them', 'body' => 'Hi, I have a question about my order.', 'created_at' => now()->subHours(3)],
            (object) ['id' => 2, 'from' => 'me',   'body' => 'Sure! What can I help you with?',       'created_at' => now()->subHours(2)->subMinutes(50)],
            (object) ['id' => 3, 'from' => 'them', 'body' => 'When will it be shipped?',              'created_at' => now()->subHours(2)],
            (object) ['id' => 4, 'from' => 'me',   'body' => 'It ships tomorrow morning.',            'created_at' => now()->subHours(1)],
            (object) ['id' => 5, 'from' => 'them', 'body' => 'Thanks for the quick response!',        'created_at' => now()->subMinutes(5)],
        ]);

        return view('messages.show', compact('conversation', 'messages'));
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Message sent.');
    }
}
