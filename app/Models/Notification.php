<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    // Fillable fields
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
        'is_sound'
    ];

    // Cast is_read to boolean automatically
    protected $casts = [
        'is_read' => 'boolean',
        'is_sound' => 'boolean'
    ];

    /**
     * Relationship: Notification belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }

    public function getRouteAttribute()
    {
        switch (strtolower($this->type)) {
            case 'purchase payments':
                return route('admin.payments.index');

            case 'sales payments':
                return route('admin.payments.sales.index');

            case 'wallet':
                return route('admin.wallets.index');

            default:
                return route('admin.notifications.index', $this->id);
        }
    }
}
