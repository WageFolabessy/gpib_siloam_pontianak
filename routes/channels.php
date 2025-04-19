<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\AdminUser;

Broadcast::channel('private-chat.user.{userId}', function ($loggedInUser, $userId) {
    Log::info('--- Channel Auth Check ---');
    Log::info('Attempting to authorize channel: private-chat.user.' . $userId);

    if ($loggedInUser) {
        Log::info('User Class: ' . get_class($loggedInUser));
        Log::info('User ID: ' . $loggedInUser->id);

        if ($loggedInUser instanceof User) {
            Log::info('Detected as instance of User (Jemaat).');
            if ($loggedInUser->id == $userId) {
                Log::info('Authorization SUCCESS: User ID matches channel user ID.');
                return true;
            } else {
                Log::info('Authorization FAILED: User ID does not match channel user ID.');
            }
        } elseif ($loggedInUser instanceof AdminUser) {
            Log::info('Detected as instance of AdminUser.');
            Log::info('Authorization SUCCESS: User is Admin.');
            return true;
        } else {
            Log::warning('Authorization FAILED: User is neither User nor AdminUser instance.');
        }
    } else {
        Log::error('Authorization FAILED: No authenticated user provided to callback.');
    }

    Log::warning('Authorization FAILED: Denying access to channel private-chat.user.' . $userId);
    return false;
}, ['guards' => ['web', 'admin_users']]);

Broadcast::channel('admin-notifications', function ($loggedInUser) {
    return $loggedInUser instanceof AdminUser;
}, ['guards' => ['admin_users']]);


