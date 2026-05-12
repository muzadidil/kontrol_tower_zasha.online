<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraRatingController extends Controller
{
    /**
     * Daftar rating & ulasan yang diterima mitra dari pelanggan.
     * Filter optional: ?filter=belum-dibalas|sudah-dibalas|all
     *                 ?bintang=1..5
     */
    public function index(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $filter   = $request->query('filter', 'all');
        $bintang  = $request->query('bintang');

        $query = Rating::query()
            ->where('dinilai_id', $mitra->id_mitra)
            ->where('tipe_dinilai', 'mitra')
            ->where('tipe_penilai', 'pelanggan')
            ->orderByDesc('created_at');

        if ($filter === 'belum-dibalas') {
            $query->whereNull('balasan_mitra');
        } elseif ($filter === 'sudah-dibalas') {
            $query->whereNotNull('balasan_mitra');
        }

        if ($bintang !== null && $bintang !== '' && in_array((int) $bintang, [1, 2, 3, 4, 5], true)) {
            $query->where('bintang', (int) $bintang);
        }

        $ratings = $query->get();

        // Enrich dengan data pelanggan (kalau tidak anonim)
        $pelangganIds = $ratings->pluck('penilai_id')->unique()->values();
        $pelanggans = DB::table('pelanggans')
            ->whereIn('id_pelanggan', $pelangganIds)
            ->select('id_pelanggan', 'nama_pelanggan', 'nama_panggilan')
            ->get()
            ->keyBy('id_pelanggan');

        // Stats keseluruhan (TIDAK ikut filter agar gambaran umum tetap)
        $allRatings = Rating::query()
            ->where('dinilai_id', $mitra->id_mitra)
            ->where('tipe_dinilai', 'mitra')
            ->where('tipe_penilai', 'pelanggan')
            ->get(['bintang', 'balasan_mitra']);

        $stats = [
            'total'         => $allRatings->count(),
            'rata_rata'     => $allRatings->isNotEmpty() ? round($allRatings->avg('bintang'), 2) : 0,
            'belum_dibalas' => $allRatings->whereNull('balasan_mitra')->count(),
            'distribusi'    => collect([1, 2, 3, 4, 5])->mapWithKeys(function ($b) use ($allRatings) {
                return [$b => $allRatings->where('bintang', $b)->count()];
            })->toArray(),
        ];

        return view('mitra.rating.index', compact(
            'mitra', 'ratings', 'pelanggans', 'stats', 'filter', 'bintang'
        ));
    }

    /**
     * Submit balasan mitra ke salah satu ulasan.
     */
    public function balas(Request $request, Rating $rating)
    {
        $mitra = Auth::guard('mitra')->user();

        // Authorize: rating harus untuk mitra yang login
        if ((int) $rating->dinilai_id !== (int) $mitra->id_mitra
            || $rating->tipe_dinilai !== 'mitra') {
            abort(403, 'Anda tidak punya akses ke ulasan ini.');
        }

        $data = $request->validate([
            'balasan' => 'required|string|max:500',
        ]);

        $rating->update([
            'balasan_mitra' => $data['balasan'],
            'balasan_at'    => Carbon::now(),
        ]);

        return redirect()->route('mitra.rating.index', $request->only('filter', 'bintang'))
            ->with('success', 'Balasan terkirim.');
    }

    /**
     * Hapus balasan (untuk edit ulang).
     */
    public function hapusBalasan(Rating $rating)
    {
        $mitra = Auth::guard('mitra')->user();

        if ((int) $rating->dinilai_id !== (int) $mitra->id_mitra
            || $rating->tipe_dinilai !== 'mitra') {
            abort(403);
        }

        $rating->update([
            'balasan_mitra' => null,
            'balasan_at'    => null,
        ]);

        return back()->with('success', 'Balasan dihapus.');
    }
}
