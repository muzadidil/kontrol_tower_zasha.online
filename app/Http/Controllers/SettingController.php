<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'google_maps_api_key' => 'nullable|string|max:200',
        ]);

        Setting::set('google_maps_api_key', $request->google_maps_api_key);

        return back()->with('setting_saved', 'API Key berhasil disimpan.');
    }
}
