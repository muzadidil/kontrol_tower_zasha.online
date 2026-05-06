<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Pelanggan;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMitra = Mitra::count();
        $totalPelanggan = Pelanggan::count();
        $totalOrderAktif = Order::whereNotIn('status', ['Selesai', 'Batal'])->count();
        $latestOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalMitra', 'totalPelanggan', 'totalOrderAktif', 'latestOrders'));
    }
}
