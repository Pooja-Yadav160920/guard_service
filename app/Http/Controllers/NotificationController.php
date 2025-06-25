<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Guard;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        if (!checkPermission('show-notification')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notifications = Notification::latest()->get();
        return response()->json(['notifications' => $notifications]);
    }

    public function store(Request $request)
    {
        if (!checkPermission('add-notification')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'guard_id' => 'required|exists:guards,id',
            'type'     => 'required|string',
            'message'  => 'required|string',
        ]);

        $guard = Guard::findOrFail($request->guard_id);

        $notification = Notification::create([
            'guard_id'   => $guard->id,
            'guard_name' => $guard->name,
            'type'       => $request->type,
            'message'    => $request->message,
            'sent'       => true,
            'sent_at'    => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Notification sent',
            'data'    => $notification,
        ]);
    }

    public function respond($id)
    {
        if (!checkPermission('respond-notification')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification = Notification::findOrFail($id);
        $notification->responded = true;
        $notification->responded_at = Carbon::now();
        $notification->save();

        return response()->json([
            'message' => 'Notification marked as responded',
            'data'    => $notification,
        ]);
    }

    public function guardNotifications($guard_id)
    {
        if (!checkPermission('show-notification')) { // Or 'guard-notification' if more granular
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notifications = Notification::where('guard_id', $guard_id)->latest()->get();
        return response()->json(['notifications' => $notifications]);
    }
}
