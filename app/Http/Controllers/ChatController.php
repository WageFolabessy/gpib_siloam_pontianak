<?php

namespace App\Http\Controllers;

use App\Events\NewUserMessageForAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\TemplateTanyaJawab;
use App\Events\MessageSent;
use App\Events\MessagesMarkedAsRead;
use App\Http\Resources\ChatResource;
use Yajra\DataTables\Facades\DataTables;

class ChatController extends Controller
{
    public function index()
    {
        return view('dashboard.chat.index');
    }

    public function templateTanyaJawab()
    {
        try {
            $templates = TemplateTanyaJawab::all(['pertanyaan', 'jawaban']);
            return response()->json($templates);
        } catch (\Exception $e) {
            Log::error('Error fetching chat templates: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat template'], 500);
        }
    }

    public function storeUserMessage(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:2000',
            'client_message_id' => 'nullable|string|max:36',
        ]);

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $chat = $user->sentChats()->create([
                'user_id' => $user->id,
                'message' => $validated['message'],
            ]);
            $chat->load('sender');

            broadcast(new MessageSent($chat))->toOthers();

            broadcast(new NewUserMessageForAdmin($chat));

            return response()->json([
                'message' => 'Pesan berhasil terkirim',
                'data'    => new ChatResource($chat)
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error storing user message for User ID {$user->id}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan pesan'], 500);
        }
    }

    public function storeAdminMessage(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:2000',
            'target_user_id'    => 'required|integer|exists:users,id',
            'client_message_id' => 'nullable|string|max:36',
        ]);

        /** @var \App\Models\AdminUser|null $admin */
        $admin = Auth::guard('admin_users')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized (Admin)'], 401);
        }

        try {
            $chat = $admin->sentChats()->create([
                'user_id' => $validated['target_user_id'],
                'message' => $validated['message'],
            ]);

            $chat->load('sender');

            broadcast(new MessageSent($chat))->toOthers();

            return response()->json([
                'message' => 'Pesan admin berhasil terkirim',
                'data'    => new ChatResource($chat)
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error storing admin message from Admin ID {$admin->id} to User ID {$validated['target_user_id']}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal menyimpan pesan admin'], 500);
        }
    }

    public function sendAdminTemplateMessage(Request $request)
    {
        $validated = $request->validate([
            'message'           => 'required|string|max:2000',
            'target_user_id'    => 'required|integer|exists:users,id',
            'client_message_id' => 'nullable|string|max:36',
        ]);

        /** @var \App\Models\AdminUser|null $admin */
        $admin = Auth::guard('admin_users')->user();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized (Admin)'], 401);
        }

        try {
            $chat = $admin->sentChats()->create([
                'user_id' => $validated['target_user_id'],
                'message' => $validated['message'],
            ]);

            $chat->load('sender');

            broadcast(new MessageSent($chat))->toOthers();

            return response()->json([
                'message' => 'Template jawaban berhasil terkirim',
                'data'    => new ChatResource($chat)
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error sending admin template message from Admin ID {$admin->id} to User ID {$validated['target_user_id']}: " . $e->getMessage());
            return response()->json(['message' => 'Gagal mengirim template jawaban'], 500);
        }
    }

    public function getChatHistoryForAdmin(User $user)
    {
        try {
            $messages = Chat::where('user_id', $user->id)
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->paginate(30);

            return ChatResource::collection($messages);
        } catch (\Exception $e) {
            Log::error("Error fetching chat history for admin (Target User ID: {$user->id}): " . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat riwayat chat'], 500);
        }
    }

    public function getMyChatHistory(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $messages = Chat::where('user_id', $user->id)
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->paginate(30);

            return ChatResource::collection($messages);
        } catch (\Exception $e) {
            Log::error("Error fetching user chat history (User ID: {$user->id}): " . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat riwayat chat Anda'], 500);
        }
    }

    public function getChatUsersForDataTable(Request $request)
    {
        try {
            $latestChatsSub = Chat::select('user_id', DB::raw('MAX(id) as last_chat_id'))
                ->groupBy('user_id');

            $chats = Chat::select(
                'chats.user_id',
                'users.name as user_name',
                'last_chat.message as last_message_body',
                'chats.created_at as last_timestamp',
                DB::raw("(SELECT COUNT(*) FROM chats as unread_chats
                          WHERE unread_chats.user_id = chats.user_id
                            AND unread_chats.sender_type = ?
                            AND unread_chats.read_at IS NULL) as unread_user_messages_count")
            )
                ->joinSub($latestChatsSub, 'latest_chats', function ($join) {
                    $join->on('chats.id', '=', 'latest_chats.last_chat_id');
                })
                ->join('users', 'chats.user_id', '=', 'users.id')
                ->join('chats as last_chat', 'chats.id', '=', 'last_chat.id')
                ->addBinding(User::class, 'select');

            return DataTables::of($chats)
                ->addIndexColumn()
                ->addColumn('nama_pengguna', function ($chat) {
                    return $chat->user_name ?? 'User ID: ' . $chat->user_id;
                })
                ->addColumn('unread', function ($chat) {
                    return $chat->unread_user_messages_count > 0
                        ? '<span class="badge bg-danger">' . $chat->unread_user_messages_count . '</span>'
                        : '';
                })
                ->addColumn('pesan_terakhir', function ($chat) {
                    return $chat->last_message_body ? \Illuminate\Support\Str::limit($chat->last_message_body, 50) : '';
                })
                ->addColumn('waktu', function ($chat) {
                    try {
                        return Carbon::parse($chat->last_timestamp)->locale('id')->diffForHumans();
                    } catch (\Exception $e) {
                        Log::warning("Could not parse timestamp for chat user_id {$chat->user_id}: {$chat->last_timestamp}");
                        return '-';
                    }
                })
                ->addColumn('aksi', function ($chat) {
                    $conversation = json_encode([
                        'user_id'   => $chat->user_id,
                        'user_name' => $chat->user_name ?? 'User ID: ' . $chat->user_id,
                    ]);
                    return '<a href="#" class="btn btn-primary btn-sm openChatModal" data-bs-toggle="modal" data-bs-target="#chatDetailModal" data-conversation=\'' . e($conversation) . '\'>Lihat Chat</a>';
                })
                ->rawColumns(['aksi', 'unread'])
                ->orderColumn('last_timestamp', function ($query, $order) {
                    $query->orderBy('chats.created_at', $order);
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error generating chat users datatable: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Gagal memuat daftar chat pengguna.'], 500);
        }
    }

    public function markAsRead(Request $request)
    {
        $userIdToUpdate = null;
        $reader = null;
        $readerType = null;
        $messagesFromType = null;

        if (Auth::guard('admin_users')->check()) {
            $validated = $request->validate(['user_id' => 'required|integer|exists:users,id']);
            $userIdToUpdate = $validated['user_id'];
            $reader = Auth::guard('admin_users')->user();
            $readerType = 'admin';
            $messagesFromType = User::class;
        } elseif (Auth::guard('web')->check()) {
            $reader = Auth::guard('web')->user();
            $userIdToUpdate = $reader->id;
            $readerType = 'user';
            $messagesFromType = AdminUser::class;
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $now = Carbon::now();
            $updatedCount = 0;
            $lastReadMessageId = null;

            $lastUnreadMessage = Chat::where('user_id', $userIdToUpdate)
                ->where('sender_type', $messagesFromType) // <-- Perhatikan ini
                ->whereNull('read_at')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastUnreadMessage) {
                $lastReadMessageId = $lastUnreadMessage->id;

                $updatedCount = Chat::where('user_id', $userIdToUpdate)
                    ->where('sender_type', $messagesFromType) // <-- Perhatikan ini juga
                    ->whereNull('read_at')
                    ->where('id', '<=', $lastReadMessageId)
                    ->update(['read_at' => $now]); // <-- Perintah UPDATE

                Log::info("Updated count for markAsRead: " . $updatedCount); // (Tambahkan log ini untuk debug)
            } else {
                Log::info("No unread messages found to mark as read."); // (Tambahkan log ini untuk debug)
            }


            // 3. Broadcast jika ada yg terupdate ($updatedCount > 0)
            if ($updatedCount > 0 && $lastReadMessageId !== null) {
                broadcast(new MessagesMarkedAsRead(
                    $userIdToUpdate,
                    $readerType,
                    $now->toIso8601String(),
                    $lastReadMessageId
                ));
            }

            return response()->json([
                'message' => 'Pesan berhasil ditandai sudah dibaca',
                'updated_count' => $updatedCount
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error marking messages as read (Thread User ID: {$userIdToUpdate}, Reader: {$readerType}): " . $e->getMessage());
            return response()->json(['message' => 'Gagal menandai pesan sebagai dibaca'], 500);
        }
    }
}
