<?php

namespace App\Http\Controllers;

use App\Events\UserMessageSent;
use App\Events\AdminMessageSent;
use App\Models\Chat;
use App\Models\TemplateTanyaJawab;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        return view('dashboard.chat.index');
    }

    public function templateTanyaJawab()
    {
        return response()->json(TemplateTanyaJawab::all());
    }

    public function sendAdminTemplate(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:1000',
            'target'            => 'required|integer',
            'client_message_id' => 'nullable|string',
        ]);

        Chat::create([
            'user_id'     => $validated['target'],
            'sender_id'   => 0,
            'sender_type' => 'admin',
            'message'     => $validated['message'],
        ]);

        AdminMessageSent::dispatch([
            'message'           => $validated['message'],
            'target'            => $validated['target'],
            'timestamp'         => now()->toIso8601String(),
            'client_message_id' => $validated['client_message_id'] ?? null,
        ]);

        return response()->json(['message' => 'Template jawaban terkirim'], 201);
    }

    public function sendUserMessage(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:1000',
            'client_message_id' => 'nullable|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Chat::create([
            'user_id'     => $user->id,
            'sender_id'   => $user->id,
            'sender_type' => 'user',
            'message'     => $validated['message'],
        ]);

        UserMessageSent::dispatch([
            'message'           => $validated['message'],
            'user_id'           => $user->id,
            'user_name'         => $user->name,
            'timestamp'         => now()->toIso8601String(),
            'client_message_id' => $validated['client_message_id'] ?? null,
        ]);

        return response()->json(['message' => 'Message sent successfully'], 201);
    }

    public function sendAdminMessage(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:1000',
            'target'            => 'required|integer',
            'client_message_id' => 'nullable|string',
        ]);

        $admin = Auth::guard('admin_users')->user();
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Chat::create([
            'user_id'     => $validated['target'],
            'sender_id'   => $admin->id,
            'sender_type' => 'admin',
            'message'     => $validated['message'],
        ]);

        AdminMessageSent::dispatch([
            'message'           => $validated['message'],
            'target'            => $validated['target'],
            'timestamp'         => now()->toIso8601String(),
            'client_message_id' => $validated['client_message_id'] ?? null,
        ]);

        return response()->json(['message' => 'Admin message sent successfully'], 201);
    }

    public function getUserMessages($userId)
    {
        $admin = Auth::guard('admin_users')->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $messages = Chat::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->each(function ($msg) {
                $msg->timestamp  = $msg->created_at->toIso8601String();
                $msg->sender     = $msg->sender_type;
                $msg->user_name  = ($msg->sender_type === 'user' && $msg->user) ? $msg->user->name : 'Admin';
            });

        return response()->json($messages);
    }

    public function getChatUsers(Request $request)
    {
        $chats = Chat::select(
            'user_id',
            DB::raw('MAX(id) as last_chat_id'),
            DB::raw('MAX(created_at) as last_timestamp')
        )
            ->groupBy('user_id')
            ->orderBy(DB::raw('MAX(created_at)'), 'desc');

        return DataTables::of($chats)
            ->addIndexColumn()
            ->addColumn('nama_pengguna', function ($chat) {
                $user = User::find($chat->user_id);
                return $user ? $user->name : $chat->user_id;
            })
            ->addColumn('pesan_terakhir', function ($chat) {
                $lastMessage = Chat::find($chat->last_chat_id);
                return $lastMessage ? substr($lastMessage->message, 0, 50) : '';
            })
            ->addColumn('waktu', function ($chat) {
                return Carbon::parse($chat->last_timestamp)->diffForHumans();
            })
            ->addColumn('aksi', function ($chat) {
                $user = User::find($chat->user_id);
                $userName = $user ? $user->name : $chat->user_id;
                $conversation = json_encode([
                    'user_id'   => $chat->user_id,
                    'user_name' => $userName,
                ]);
                return '<a href="#" class="btn btn-primary btn-sm openChatModal" data-bs-toggle="modal" data-bs-target="#chatDetailModal" data-conversation=\'' . $conversation . '\'>Lihat Chat</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function getMessagesForUser(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        $messages = Chat::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
