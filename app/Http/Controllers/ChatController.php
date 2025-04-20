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
use App\Models\TemplateTanyaJawab;
use App\Events\MessageSent;
use App\Events\MessagesMarkedAsRead;
use App\Http\Resources\ChatResource;
use Illuminate\Validation\ValidationException;
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
                ->orderBy('created_at', 'desc')
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
                ->orderBy('created_at', 'desc')
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
            $latestChatsSub = DB::table('chats')
                ->select('user_id', DB::raw('MAX(id) as last_chat_id'))
                ->groupBy('user_id');

            $chatsQuery = DB::table('chats')
                ->select(
                    'chats.user_id',
                    'users.name as user_name',
                    'last_chat_details.message as last_message_body',
                    'last_chat_details.created_at as last_timestamp',
                    DB::raw("(SELECT COUNT(*) FROM chats as unread_chats
                              WHERE unread_chats.user_id = chats.user_id
                                AND unread_chats.sender_type = ?
                                AND unread_chats.read_at IS NULL) as unread_user_messages_count")
                )
                ->joinSub($latestChatsSub, 'latest_chats', function ($join) {
                    $join->on('chats.user_id', '=', 'latest_chats.user_id');
                })
                ->join('chats as last_chat_details', 'latest_chats.last_chat_id', '=', 'last_chat_details.id')
                ->join('users', 'chats.user_id', '=', 'users.id')
                ->addBinding('user', 'select')
                ->groupBy(
                    'chats.user_id',
                    'users.name',
                    'last_chat_details.message',
                    'last_chat_details.created_at'
                );

            return DataTables::of($chatsQuery)
                ->addIndexColumn()
                ->addColumn('nama_pengguna', function ($chat) {
                    return $chat->user_name ?? 'User ID: ' . $chat->user_id;
                })
                ->addColumn('unread', function ($chat) {
                    return $chat->unread_user_messages_count > 0 ? '<span class="badge bg-danger">' . $chat->unread_user_messages_count . '</span>' : '';
                })
                ->addColumn('pesan_terakhir', function ($chat) {
                    return $chat->last_message_body ? \Illuminate\Support\Str::limit($chat->last_message_body, 50) : '';
                })
                ->addColumn('waktu', function ($chat) {
                    try {
                        return $chat->last_timestamp ? Carbon::parse($chat->last_timestamp)->locale('id')->diffForHumans() : '-';
                    } catch (\Exception $e) {
                        return '-';
                    }
                })
                ->addColumn('aksi', function ($chat) {
                    $conversation = json_encode(['user_id' => $chat->user_id, 'user_name' => $chat->user_name ?? 'User ID: ' . $chat->user_id,]);
                    return '<a href="#" class="btn btn-primary btn-sm openChatModal" data-bs-toggle="modal" data-bs-target="#chatDetailModal" data-conversation=\'' . e($conversation) . '\'>Lihat Chat</a>';
                })
                ->rawColumns(['aksi', 'unread'])
                ->orderColumn('last_timestamp', function ($query, $order) {
                    $query->orderBy('last_chat_details.created_at', $order);
                })
                ->order(function ($query) {
                    if (empty($query->orders)) {
                        $query->orderBy('last_chat_details.created_at', 'desc');
                    }
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error generating chat users datatable: ' . $e->getMessage(), ['exception' => $e]);
            if (config('app.debug')) {
                Log::error('Query Log:', ['log' => DB::getQueryLog()]);
            }
            return response()->json(['error' => 'Gagal memuat daftar chat pengguna.', 'message' => $e->getMessage()], 500);
        }
    }

    public function markUserMessagesAsRead(Request $request)
    {
        /** @var \App\Models\User|null $reader */
        $reader = Auth::guard('web')->user();

        if (!$reader) {
            Log::warning("[markUserMessagesAsRead] Unauthorized access attempt. Web guard check failed.");
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userIdToUpdate = $reader->id;
        $readerType = 'user';
        $messagesFromType = 'admin';

        Log::debug("------------------------------------------");
        Log::debug("[markUserMessagesAsRead] User (ID: {$reader->id}) marking messages from {$messagesFromType} in thread {$userIdToUpdate}");

        try {
            $now = Carbon::now();
            $updatedCount = 0;
            $lastReadMessageId = null;

            Log::debug("[markUserMessagesAsRead] Finding last unread message for user_id={$userIdToUpdate} from sender_type={$messagesFromType}");

            $lastUnreadMessage = Chat::where('user_id', $userIdToUpdate)
                ->where('sender_type', $messagesFromType)
                ->whereNull('read_at')
                ->orderBy('id', 'desc')
                ->select('id')
                ->first();

            if ($lastUnreadMessage) {
                $lastReadMessageId = $lastUnreadMessage->id;
                Log::debug("[markUserMessagesAsRead] Last unread message ID found: {$lastReadMessageId}");

                Log::debug("[markUserMessagesAsRead] Attempting to update messages up to ID {$lastReadMessageId}");
                $updatedCount = Chat::where('user_id', $userIdToUpdate)
                    ->where('sender_type', $messagesFromType)
                    ->whereNull('read_at')
                    ->where('id', '<=', $lastReadMessageId)
                    ->update(['read_at' => $now]);

                Log::info("[markUserMessagesAsRead] Update executed. Rows affected: {$updatedCount}");
            } else {
                Log::debug("[markUserMessagesAsRead] No unread messages found for this criteria.");
            }

            if ($updatedCount > 0 && $lastReadMessageId !== null) {
                Log::debug("[markUserMessagesAsRead] Broadcasting MessagesMarkedAsRead event.");
                broadcast(new MessagesMarkedAsRead(
                    $userIdToUpdate,
                    $readerType,
                    $now->toIso8601String(),
                    $lastReadMessageId
                ))->toOthers();
                Log::debug("[markUserMessagesAsRead] Event broadcasted.");
            } else {
                Log::debug("[markUserMessagesAsRead] No broadcast needed (updatedCount: {$updatedCount}).");
            }
            Log::debug("------------------------------------------");

            return response()->json([
                'message' => 'Proses tandai dibaca selesai',
                'updated_count' => $updatedCount
            ], 200);
        } catch (\Exception $e) {
            Log::error("[markUserMessagesAsRead] Exception occurred for User ID {$reader->id}: " . $e->getMessage(), ['exception' => $e]);
            Log::debug("------------------------------------------");
            return response()->json(['message' => 'Gagal menandai pesan sebagai dibaca'], 500);
        }
    }

    public function markAdminMessagesAsRead(Request $request)
    {
        /** @var \App\Models\AdminUser|null $reader */
        $reader = Auth::guard('admin_users')->user();

        if (!$reader) {
            Log::warning("[markAdminMessagesAsRead] Unauthorized access attempt. Admin guard check failed.");
            return response()->json(['message' => 'Unauthorized (Admin)'], 401);
        }

        try {
            $validated = $request->validate(['user_id' => 'required|integer|exists:users,id']);
            $userIdToUpdate = $validated['user_id'];
            $readerType = 'admin';
            $messagesFromType = 'user';

            Log::debug("------------------------------------------");
            Log::debug("[markAdminMessagesAsRead] Admin (ID: {$reader->id}) marking messages from {$messagesFromType} in thread {$userIdToUpdate}");
        } catch (ValidationException $e) {
            Log::error("[markAdminMessagesAsRead] Validation failed for ADMIN: " . $e->getMessage(), $e->errors());
            Log::debug("------------------------------------------");
            return response()->json(['message' => 'Data user_id tidak valid atau tidak ditemukan.', 'errors' => $e->errors()], 422);
        }


        try {
            $now = Carbon::now();
            $updatedCount = 0;
            $lastReadMessageId = null;

            Log::debug("[markAdminMessagesAsRead] Finding last unread message for user_id={$userIdToUpdate} from sender_type={$messagesFromType}");

            $lastUnreadMessage = Chat::where('user_id', $userIdToUpdate)
                ->where('sender_type', $messagesFromType)
                ->whereNull('read_at')
                ->orderBy('id', 'desc')
                ->select('id')
                ->first();

            if ($lastUnreadMessage) {
                $lastReadMessageId = $lastUnreadMessage->id;
                Log::debug("[markAdminMessagesAsRead] Last unread message ID found: {$lastReadMessageId}");

                Log::debug("[markAdminMessagesAsRead] Attempting to update messages up to ID {$lastReadMessageId}");
                $updatedCount = Chat::where('user_id', $userIdToUpdate)
                    ->where('sender_type', $messagesFromType)
                    ->whereNull('read_at')
                    ->where('id', '<=', $lastReadMessageId)
                    ->update(['read_at' => $now]);

                Log::info("[markAdminMessagesAsRead] Update executed. Rows affected: {$updatedCount}");
            } else {
                Log::debug("[markAdminMessagesAsRead] No unread messages found for this criteria.");
            }

            if ($updatedCount > 0 && $lastReadMessageId !== null) {
                Log::debug("[markAdminMessagesAsRead] Broadcasting MessagesMarkedAsRead event.");
                broadcast(new MessagesMarkedAsRead(
                    $userIdToUpdate,
                    $readerType,
                    $now->toIso8601String(),
                    $lastReadMessageId
                ))->toOthers();
                Log::debug("[markAdminMessagesAsRead] Event broadcasted.");
            } else {
                Log::debug("[markAdminMessagesAsRead] No broadcast needed (updatedCount: {$updatedCount}).");
            }
            Log::debug("------------------------------------------");

            return response()->json([
                'message' => 'Proses tandai dibaca oleh admin selesai',
                'updated_count' => $updatedCount
            ], 200);
        } catch (\Exception $e) {
            Log::error("[markAdminMessagesAsRead] Exception occurred for Admin ID {$reader->id} on User Thread {$userIdToUpdate}: " . $e->getMessage(), ['exception' => $e]);
            Log::debug("------------------------------------------");
            return response()->json(['message' => 'Gagal menandai pesan sebagai dibaca oleh admin'], 500);
        }
    }
}
