<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Mitra, Pelanggan, UserSession};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Login pelanggan / mitra (single endpoint)
    public function login(Request $request)
    {
        $request->validate([
            'no_wa'     => 'required|string',
            'password'  => 'required|string',
            'user_type' => 'required|in:pelanggan,mitra',
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        $user = $request->user_type === 'pelanggan'
            ? Pelanggan::where('no_wa', $request->no_wa)->first()
            : Mitra::where('no_wa', $request->no_wa)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Nomor WA atau password salah.'], 401);
        }

        // Single device check: deactivate other sessions
        $userId = $request->user_type === 'pelanggan' ? $user->id_pelanggan : $user->id_mitra;
        UserSession::where('user_type', $request->user_type)
            ->where('user_id', $userId)
            ->where('device_id', '!=', $request->device_id)
            ->update(['is_active' => false]);

        // Update FCM token
        if ($request->fcm_token) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        // Buat token Sanctum
        $token = $user->createToken('mobile-' . $request->device_id)->plainTextToken;

        // Catat session
        UserSession::updateOrCreate(
            [
                'user_type' => $request->user_type,
                'user_id'   => $userId,
                'device_id' => $request->device_id,
            ],
            [
                'device_name'    => $request->device_name,
                'token'          => substr($token, 0, 64),
                'is_active'      => true,
                'last_active_at' => now(),
            ]
        );

        return response()->json([
            'token'     => $token,
            'user_type' => $request->user_type,
            'user'      => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        UserSession::where('user_type', $request->user_type ?? 'pelanggan')
            ->where('user_id', $request->user()->id_pelanggan ?? $request->user()->id_mitra)
            ->where('device_id', $request->device_id)
            ->update(['is_active' => false]);

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    // Register pelanggan
    public function registerPelanggan(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_wa'    => 'required|string|unique:pelanggans,no_wa',
            'password' => 'required|min:6',
        ]);

        $pelanggan = Pelanggan::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'no_wa'    => $request->no_wa,
            'password' => Hash::make($request->password),
            'kode_zasha' => 'PLG-' . strtoupper(Str::random(6)),
            'status_verifikasi' => 0,
            'is_profile_complete' => false,
        ]);

        $token = $pelanggan->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $pelanggan,
        ], 201);
    }
}
