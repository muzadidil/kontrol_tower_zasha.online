<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $gmaps_api_key = Setting::get('google_maps_api_key', '');
        return view('admin.settings.index', compact('gmaps_api_key'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'google_maps_api_key' => 'nullable|string|max:200',
        ]);

        Setting::set('google_maps_api_key', $request->google_maps_api_key);

        return redirect()->route('admin.settings')->with('setting_saved', 'API Key berhasil disimpan.');
    }
}
