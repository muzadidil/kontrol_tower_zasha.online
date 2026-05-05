<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class MitraController extends Controller
{
    public function dashboard()
    {
        // Contoh, asumsikan mitra sedang login dengan ID 1 untuk testing.
        // Seharusnya menggunakan Auth::id() atau middleware yang sesuai.
        $mitraId = 1; 
        $activeOrder = Order::where('mitra_id', $mitraId)
                            ->whereIn('status', ['Pending', 'Menuju Lokasi'])
                            ->first();

        return view('admin.mitra.dashboard', compact('activeOrder'));
    }

    public function index()
    {
        return view('admin.mitra.index');
    }
}
