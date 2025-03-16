<?php

namespace App\Http\Controllers;

use App\Events\UserMessageSent;
use App\Events\AdminMessageSent;
use App\Models\TemplateTanyaJawab;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Hanya mengembalikan view tanpa data chat dari server
    public function chatIndex()
    {
        return view('dashboard.chat.index');
    }

    public function templateTanyaJawab()
    {
        $datas = TemplateTanyaJawab::all();
        return response()->json($datas, 200);
    }

    public function sendUserMessage(Request $request)
    {
        $message = $request->message;
        $user_id = $request->get('user_id'); // ambil dari query string
        // Broadcast pesan user dengan payload array
        UserMessageSent::dispatch([
            'message'   => $message,
            'user_id'   => $user_id,
            'timestamp' => now()->toIso8601String()
        ]);

        return response()->json([
            'message' => 'Message sent successfully'
        ], 201);
    }

    public function sendAdminMessage(Request $request)
    {
        $message = $request->message;
        $target  = $request->get('target'); // target adalah userId dari penerima pesan
        // Broadcast pesan admin dengan payload array
        AdminMessageSent::dispatch([
            'message'   => $message,
            'target'    => $target,
            'timestamp' => now()->toIso8601String()
        ]);

        return response()->json([
            'message' => 'Admin message sent successfully'
        ], 201);
    }
}
