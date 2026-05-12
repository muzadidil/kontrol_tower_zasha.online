<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\MitraJadwalHarian;
use App\Models\MitraJadwalLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MitraJadwalController extends Controller
{
    /**
     * Halaman utama jadwal: jam kerja per hari + daftar tanggal libur.
     */
    public function index()
    {
        $mitra = Auth::guard('mitra')->user();

        // Pastikan ada 7 entri (0-6), bila belum buat default
        $existing = MitraJadwalHarian::where('mitra_id', $mitra->id_mitra)
            ->get()
            ->keyBy('hari');

        $jadwalHarian = collect(MitraJadwalHarian::HARI)->map(function ($nama, $hari) use ($existing, $mitra) {
            if ($existing->has($hari)) {
                return $existing->get($hari);
            }
            // Default: senin-sabtu 08:00-17:00 buka, minggu libur
            return new MitraJadwalHarian([
                'mitra_id'  => $mitra->id_mitra,
                'hari'      => $hari,
                'is_libur'  => $hari === 0,
                'jam_buka'  => $hari === 0 ? null : '08:00:00',
                'jam_tutup' => $hari === 0 ? null : '17:00:00',
            ]);
        });

        $tanggalLibur = MitraJadwalLibur::where('mitra_id', $mitra->id_mitra)
            ->where('tanggal', '>=', Carbon::today()->subDays(7)) // tampilkan dari 1 minggu lalu
            ->orderBy('tanggal')
            ->get();

        return view('mitra.jadwal.index', compact('mitra', 'jadwalHarian', 'tanggalLibur'));
    }

    /**
     * Update bulk jam kerja semua hari sekaligus.
     */
    public function updateHarian(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $data = $request->validate([
            'hari'              => 'required|array|size:7',
            'hari.*.is_libur'   => 'nullable|boolean',
            'hari.*.jam_buka'   => 'nullable|date_format:H:i',
            'hari.*.jam_tutup'  => 'nullable|date_format:H:i|after:hari.*.jam_buka',
        ]);

        foreach ($data['hari'] as $hariIdx => $row) {
            $hariIdx = (int) $hariIdx;
            if (!isset(MitraJadwalHarian::HARI[$hariIdx])) {
                continue;
            }

            $isLibur = !empty($row['is_libur']);
            MitraJadwalHarian::updateOrCreate(
                ['mitra_id' => $mitra->id_mitra, 'hari' => $hariIdx],
                [
                    'is_libur'  => $isLibur,
                    'jam_buka'  => $isLibur ? null : ($row['jam_buka'] ?? '08:00'),
                    'jam_tutup' => $isLibur ? null : ($row['jam_tutup'] ?? '17:00'),
                ]
            );
        }

        return redirect()->route('mitra.jadwal.index')
            ->with('success', 'Jam kerja mingguan tersimpan.');
    }

    /**
     * Tambah tanggal libur spesifik (cuti).
     */
    public function storeLibur(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $data = $request->validate([
            'tanggal'    => 'required|date|after_or_equal:today',
            'keterangan' => 'nullable|string|max:200',
        ]);

        MitraJadwalLibur::updateOrCreate(
            ['mitra_id' => $mitra->id_mitra, 'tanggal' => $data['tanggal']],
            ['keterangan' => $data['keterangan'] ?? null]
        );

        return back()->with('success', 'Tanggal libur ditambahkan.');
    }

    /**
     * Hapus tanggal libur.
     */
    public function destroyLibur(MitraJadwalLibur $libur)
    {
        $mitra = Auth::guard('mitra')->user();
        if ((int) $libur->mitra_id !== (int) $mitra->id_mitra) {
            abort(403);
        }
        $libur->delete();
        return back()->with('success', 'Tanggal libur dihapus.');
    }
}
