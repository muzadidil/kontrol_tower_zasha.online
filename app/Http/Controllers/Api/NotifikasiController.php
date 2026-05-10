<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $notifs = DB::table('notifikasi')
            ->where('id_pelanggan', $request->user()->id_pelanggan ?? 0)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json($notifs);
    }

    public function markAsRead(Request $request, $id)
    {
        DB::table('notifikasi')->where('id', $id)
            ->where('id_pelanggan', $request->user()->id_pelanggan ?? 0)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function updateFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);
        $request->user()->update(['fcm_token' => $request->fcm_token]);
        return response()->json(['ok' => true]);
    }
}
