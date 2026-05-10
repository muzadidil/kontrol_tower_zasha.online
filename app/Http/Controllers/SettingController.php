<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const KEYS_API = [
        'google_maps_api_key',
        'tokopay_merchant_id', 'tokopay_secret', 'tokopay_url',
        'digiflazz_username', 'digiflazz_api_key', 'digiflazz_webhook_secret',
        'fcm_server_key', 'fcm_project_id',
    ];

    private const KEYS_LEGAL = ['tos_mitra', 'tos_pelanggan', 'privacy_policy', 'faq_content'];
    private const KEYS_ABOUT = ['app_name', 'app_tagline', 'app_logo_url', 'app_kontak_wa', 'app_kontak_email', 'app_alamat'];
    private const KEYS_OPERASIONAL = ['komisi_default', 'min_withdraw_mitra', 'min_topup'];

    private const DEFAULTS = [
        'tokopay_url' => 'https://api.tokopay.id/v1/order',
        'app_name'    => 'Zasha Tower',
        'komisi_default' => '5',
        'min_withdraw_mitra' => '50000',
        'min_topup' => '10000',
    ];

    public function index(Request $request)
    {
        $tab      = $request->query('tab', 'api');
        $settings = $this->getAllSettings();
        return view('admin.settings.index', compact('settings', 'tab'));
    }

    public function updateApi(Request $request)
    {
        $request->validate([
            'google_maps_api_key'      => 'nullable|string|max:200',
            'tokopay_merchant_id'      => 'nullable|string|max:100',
            'tokopay_secret'           => 'nullable|string|max:200',
            'tokopay_url'              => 'nullable|url|max:200',
            'digiflazz_username'       => 'nullable|string|max:100',
            'digiflazz_api_key'        => 'nullable|string|max:200',
            'digiflazz_webhook_secret' => 'nullable|string|max:200',
            'fcm_server_key'           => 'nullable|string|max:500',
            'fcm_project_id'           => 'nullable|string|max:100',
        ]);

        foreach (self::KEYS_API as $key) {
            Setting::set($key, $request->input($key) ?? '');
        }

        return redirect()->route('admin.settings', ['tab' => 'api'])->with('success', 'Konfigurasi API berhasil disimpan.');
    }

    public function updateLegal(Request $request)
    {
        foreach (self::KEYS_LEGAL as $key) {
            Setting::set($key, $request->input($key) ?? '');
        }
        return redirect()->route('admin.settings', ['tab' => 'legal'])->with('success', 'Konten legal berhasil disimpan.');
    }

    public function updateAbout(Request $request)
    {
        $request->validate([
            'app_name'         => 'required|string|max:100',
            'app_kontak_email' => 'nullable|email|max:100',
        ]);

        foreach (self::KEYS_ABOUT as $key) {
            Setting::set($key, $request->input($key) ?? '');
        }

        return redirect()->route('admin.settings', ['tab' => 'about'])->with('success', 'Info aplikasi berhasil disimpan.');
    }

    public function updateOperasional(Request $request)
    {
        $request->validate([
            'komisi_default'     => 'required|numeric|min:0|max:100',
            'min_withdraw_mitra' => 'required|numeric|min:0',
            'min_topup'          => 'required|numeric|min:0',
        ]);

        foreach (self::KEYS_OPERASIONAL as $key) {
            Setting::set($key, (string) $request->input($key));
        }

        return redirect()->route('admin.settings', ['tab' => 'operasional'])->with('success', 'Konfigurasi operasional berhasil disimpan.');
    }

    // Backward compat: redirect ke updateApi
    public function update(Request $request)
    {
        return $this->updateApi($request);
    }

    private function getAllSettings(): array
    {
        $allKeys = array_merge(self::KEYS_API, self::KEYS_LEGAL, self::KEYS_ABOUT, self::KEYS_OPERASIONAL);
        $result  = [];
        foreach ($allKeys as $key) {
            $result[$key] = Setting::get($key, self::DEFAULTS[$key] ?? '');
        }
        return $result;
    }
}
