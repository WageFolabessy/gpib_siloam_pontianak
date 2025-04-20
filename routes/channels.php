<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\AdminUser;

Broadcast::channel('private-chat.user.{userId}', function ($loggedInUser, $userId) {
    Log::debug("[Channel Auth] Attempting to authorize channel: private-chat.user.{$userId}");

    if ($loggedInUser) {
        $userClass = get_class($loggedInUser);
        $loggedInUserId = $loggedInUser->id;
        Log::debug("[Channel Auth] Authenticated User Class: {$userClass}, ID: {$loggedInUserId}");

        if ($loggedInUser instanceof User) {
            Log::debug("[Channel Auth] User is instance of User.");
            if ($loggedInUserId == $userId) {
                Log::debug("[Channel Auth] SUCCESS: User ID matches channel ID.");
                return true;
            } else {
                Log::debug("[Channel Auth] FAILED: User ID does not match channel ID.");
                return false;
            }
        } elseif ($loggedInUser instanceof AdminUser) {
            Log::debug("[Channel Auth] User is instance of AdminUser.");
            Log::debug("[Channel Auth] SUCCESS: User is Admin.");
            return true;
        } else {
            Log::warning("[Channel Auth] FAILED: User is neither User nor AdminUser instance.");
            return false;
        }
    } else {
        Log::error("[Channel Auth] FAILED: No authenticated user provided.");
        return false;
    }
}, ['guards' => ['admin_users', 'web']]);


Broadcast::channel('admin-notifications', function ($loggedInUser) {
    Log::debug("[Channel Auth] Attempting to authorize channel: admin-notifications");
    if ($loggedInUser && $loggedInUser instanceof AdminUser) {
        Log::debug("[Channel Auth] SUCCESS: User is AdminUser instance.");
        return true;
    } else {
        $userClass = $loggedInUser ? get_class($loggedInUser) : 'null';
        Log::warning("[Channel Auth] FAILED: User is not AdminUser instance or not logged in. Class: {$userClass}");
        return false;
    }
}, ['guards' => ['admin_users']]);
