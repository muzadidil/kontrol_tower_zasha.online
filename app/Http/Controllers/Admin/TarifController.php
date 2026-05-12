<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Role;
use App\Models\Setting;
use App\Models\TarifMitra;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    /**
     * Daftar tarif cross-mitra dengan filter.
     * Untuk admin bisa banding harga antar mitra di kategori/role yang sama.
     */
    public function index(Request $request)
    {
        $filterRoleId    = $request->query('role_id');
        $filterKeyword   = trim((string) $request->query('q', ''));
        $filterStatus    = $request->query('status'); // aktif | nonaktif | null
        $filterGrouping  = $request->query('group', 'list'); // list | per-layanan

        $query = TarifMitra::query()
            ->with(['mitra' => function ($q) {
                $q->select('id_mitra', 'nama_panggilan', 'nama_asli', 'role_id', 'status_online');
            }, 'mitra.role:id,name,icon,icon_color']);

        if ($filterRoleId === 'none') {
            $query->whereHas('mitra', fn($q) => $q->whereNull('role_id'));
        } elseif ($filterRoleId !== null && $filterRoleId !== '') {
            $query->whereHas('mitra', fn($q) => $q->where('role_id', $filterRoleId));
        }

        if ($filterKeyword !== '') {
            $query->where(function ($q) use ($filterKeyword) {
                $q->where('keterangan', 'like', "%{$filterKeyword}%")
                  ->orWhereHas('mitra', function ($qq) use ($filterKeyword) {
                      $qq->where('nama_panggilan', 'like', "%{$filterKeyword}%")
                         ->orWhere('nama_asli', 'like', "%{$filterKeyword}%");
                  });
            });
        }

        if ($filterStatus === 'aktif') {
            $query->where('is_aktif', true);
        } elseif ($filterStatus === 'nonaktif') {
            $query->where('is_aktif', false);
        }

        $tarifs = $query->orderBy('keterangan')->orderBy('nominal')->get();

        // Jika mode "per-layanan", group by keterangan untuk banding harga
        $grouped = collect();
        $stats = [
            'total_tarif'     => $tarifs->count(),
            'total_mitra'     => $tarifs->pluck('mitra_id')->unique()->count(),
            'total_layanan'   => $tarifs->pluck('keterangan')->unique()->count(),
            'rata_harga'      => $tarifs->isNotEmpty() ? $tarifs->avg('nominal') : 0,
        ];

        if ($filterGrouping === 'per-layanan') {
            $grouped = $tarifs->groupBy('keterangan')->map(function ($items, $keterangan) {
                return (object) [
                    'keterangan'  => $keterangan,
                    'jumlah'      => $items->count(),
                    'min_nominal' => $items->min('nominal'),
                    'max_nominal' => $items->max('nominal'),
                    'avg_nominal' => $items->avg('nominal'),
                    'items'       => $items->sortBy('nominal')->values(),
                ];
            })->sortByDesc('jumlah')->values();
        }

        $rolesForFilter = Role::orderBy('name')->get();
        $komisiPersen   = Setting::komisiPersen();

        return view('admin.tarif.index', compact(
            'tarifs', 'grouped', 'stats',
            'rolesForFilter', 'komisiPersen',
            'filterRoleId', 'filterKeyword', 'filterStatus', 'filterGrouping'
        ));
    }
}
