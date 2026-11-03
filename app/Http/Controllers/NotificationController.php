<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{


  


    public function notificationView(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $userRole = $user->role->name ?? null;
        $isAdmin = ($userRole === 'Admin' || (string)($user->role_id ?? '') === '2');
        $notifications = collect();

        if ($isAdmin) {
            $notifications = Notification::orderBy('created_at', 'desc')->get();
        } elseif ($userRole === 'Patient') {
            $patient = \App\Models\Patients::where('login_patient_id', $user->id)->first();

            if ($patient) {
                $notifications = Notification::where('receiver_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                return view('notifications.notifications', [
                    'notifications' => collect(),
                    'notificationCount' => 0,
                    'message' => 'No notifications found for this patient.'
                ]);
            }
        } else {
            $notifications = Notification::where('receiver_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $notificationCount = $notifications->count();

        return view('notifications.notifications', compact('notifications', 'notificationCount'));
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $userRole = $user->role->name ?? null;
        $isAdmin = ($userRole === 'Admin' || (string)($user->role_id ?? '') === '2');

        if ($isAdmin) {
            Notification::where('is_read', false)->update(['is_read' => true]);
        } else {
            Notification::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json([
            'status' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function deleteAll(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $userRole = $user->role->name ?? null;
        $isAdmin = ($userRole === 'Admin' || (string)($user->role_id ?? '') === '2');

        if ($isAdmin) {
            Notification::query()->delete();
        } else {
            Notification::where('receiver_id', $user->id)->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'All notifications deleted successfully',
        ]);
    }

    public function destroy(string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $userRole = $user->role->name ?? null;
        $isAdmin = ($userRole === 'Admin' || (string)($user->role_id ?? '') === '2');

        if ($notification->receiver_id != $user->id && !$isAdmin) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to delete this notification',
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'status' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json([
                'status' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        $userRole = $user->role->name ?? null;
        $isAdmin = ($userRole === 'Admin' || (string)($user->role_id ?? '') === '2');

        if ($notification->receiver_id != $user->id && !$isAdmin) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to mark this notification as read',
            ], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }
}
