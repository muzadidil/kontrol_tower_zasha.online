<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'p256dh' => 'required|string',
            'auth_key' => 'required|string',
            'user_type' => 'required|string|in:mitra,pelanggan',
        ]);

        try {
            $userId = null;

            if ($request->user_type === 'mitra') {
                $userId = Auth::guard('mitra')->id();
                if (!$userId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 401);
                }
            } elseif ($request->user_type === 'pelanggan') {
                $userId = Auth::guard('pelanggan')->id();
                if (!$userId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 401);
                }
            }

            // Check if subscription already exists (prevent duplicates)
            $existing = PushSubscription::where('user_type', $request->user_type)
                ->where('user_id', $userId)
                ->where('endpoint', $request->endpoint)
                ->first();

            if (!$existing) {
                PushSubscription::create([
                    'user_type' => $request->user_type,
                    'user_id' => $userId,
                    'endpoint' => $request->endpoint,
                    'p256dh' => $request->p256dh,
                    'auth_key' => $request->auth_key,
                ]);

                Log::info('Push subscription created', [
                    'user_type' => $request->user_type,
                    'user_id' => $userId,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Push subscription saved'
            ]);
        } catch (\Exception $e) {
            Log::error('Push subscription failed', [
                'error' => $e->getMessage(),
                'user_type' => $request->user_type,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal subscribe notifikasi'
            ], 500);
        }
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'user_type' => 'required|string|in:mitra,pelanggan',
        ]);

        try {
            $userId = null;

            if ($request->user_type === 'mitra') {
                $userId = Auth::guard('mitra')->id();
            } elseif ($request->user_type === 'pelanggan') {
                $userId = Auth::guard('pelanggan')->id();
            }

            PushSubscription::where('user_type', $request->user_type)
                ->where('user_id', $userId)
                ->where('endpoint', $request->endpoint)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unsubscribed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal unsubscribe'
            ], 500);
        }
    }

    public function getPublicKey()
    {
        $publicKey = \App\Models\Setting::get('vapid_public_key');

        return response()->json([
            'public_key' => $publicKey ?? null,
        ]);
    }
}
