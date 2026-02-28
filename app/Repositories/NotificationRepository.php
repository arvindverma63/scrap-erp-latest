<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    /**
     * Get all notifications for a specific user
     */
    public function allForUser($request)
    {
        $user = auth()->user();
        $notifications = Notification::where('type', 'wallet')
            ->where('user_id', $user->id)->latest()->paginate(30);
        foreach ($notifications as $notification) {
            if ($notification->is_read == false) {
                Notification::where('id', $notification->id)->update(['is_read' => 1, 'is_sound' => 1]);
            }
        }
        return $notifications;
    }

    public function stockNotifications($request)
    {
        $user = auth()->user();
        $notifications = Notification::whereIn('type', ['HIGH_STOCK', 'LOW_STOCK'])
            ->where('user_id', $user->id)->latest()->paginate(30);
        foreach ($notifications as $notification) {
            if ($notification->is_read == false) {
                Notification::where('id', $notification->id)->update(['is_read' => 1, 'is_sound' => 1]);
            }
        }
        return $notifications;
    }

    public function paymentNotifications($request)
    {
        $user = auth()->user();
        $notifications = Notification::whereIn('type', ['purchase payments', 'sales payments'])
            ->where('user_id', $user->id)->latest()->paginate(30);
        foreach ($notifications as $notification) {
            if ($notification->is_read == false) {
                Notification::where('id', $notification->id)->update(['is_read' => 1, 'is_sound' => 1]);
            }
        }
        return $notifications;
    }

    /**
     * Create a new notification
     */
    public function create(array $data)
    {
        return Notification::create($data);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($userId, $id)
    {
        $notification = Notification::where('user_id', $userId)->findOrFail($id);
        $notification->update(['is_read' => true]);
        return $notification;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Delete a notification
     */
    public function delete($userId, $id)
    {
        $notification = Notification::where('user_id', $userId)->findOrFail($id);
        return $notification->delete();
    }
}
