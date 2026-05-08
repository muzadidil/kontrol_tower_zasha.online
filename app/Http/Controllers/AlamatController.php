<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    public function index()
    {
        $id = Auth::guard('pelanggan')->id();
        $alamats = Alamat::where('id_pelanggan', $id)->orderBy('is_utama', 'desc')->get();
        
        return view('pelanggan.alamat.index', compact('alamats'));
    }
}
