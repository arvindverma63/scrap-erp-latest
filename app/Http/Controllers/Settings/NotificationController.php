<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\NotificationRepository;

class NotificationController extends Controller
{
    protected $notificationRepo;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Display all notifications for authenticated user
     */
    public function index(Request $request)
    {
        // $notifications = $this->notificationRepo->allForUser(Auth::id());
        $notifications = $this->notificationRepo->allForUser($request);
        return view('layouts.notifications.index', compact('notifications'));
    }

    public function stocks(Request $request)
    {
        $notifications = $this->notificationRepo->stockNotifications($request);
        return view('layouts.notifications.stocks', compact('notifications'));
    }

    public function payments(Request $request)
    {
        $notifications = $this->notificationRepo->paymentNotifications($request);
        return view('layouts.notifications.payment', compact('notifications'));
    }

    /**
     * Store a new notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:100',
        ]);

        $this->notificationRepo->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'is_read' => false,
        ]);

        return response()->json();
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $this->notificationRepo->markAsRead(Auth::id(), $id);
        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $this->notificationRepo->markAllAsRead(auth()->user()->id);
        return back()->with('success', 'All notification seen!');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $this->notificationRepo->delete(Auth::id(), $id);

        return response()->json(['success' => true, 'message' => 'Notification deleted successfully']);
    }

    public function fetch()
    {
        $notifications = $this->notificationRepo->allForUser(auth()->id());

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function checkNotification()
    {

        $notificationCount = Notification::where('is_read', 0)->where('user_id', auth()->user()->id)->count();
        $unreadNotifications = Notification::where('is_read', 0)->where('user_id', auth()->user()->id)->orderBy('id', 'DESC')->limit(10)->get();

        $currentWalletAmount = 0.00;
        $user = auth()->user();
        if (in_array($user->roles->first()->id, [5])) {
            $currentWalletAmount = Wallet::where('date', now()->format('Y-m-d'))->sum('balance');
        } else {
            $currentWalletAmount = Wallet::where('date', now()->format('Y-m-d'))->where('cashier_id', $user->id)->sum('balance');
        }
        return response()->json([
            'count' => $notificationCount,
            'notifications' => $unreadNotifications,
            'current_wallet_balance' => number_format($currentWalletAmount, 2)
        ], 200);
    }

    public function updateStatusNotification(Request $request)
    {
        foreach ($request->notificationIds as $notification) {
            Notification::where('id', $notification)->where('user_id', auth()->user()->id)->update(['is_sound' => 1]);
        }
        return response()->json([
            'status' => true,
            'message' => 'notification updated'
        ], 200);
    }
}