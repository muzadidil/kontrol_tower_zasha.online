<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\MitraSparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MitraSparepartController extends Controller
{
    /**
     * List sparepart milik mitra. Filter ?status=aman|menipis|habis|nonaktif, ?q=keyword
     */
    public function index(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();
        $filterStatus = $request->query('status');
        $filterQuery = trim((string) $request->query('q', ''));

        $query = MitraSparepart::where('mitra_id', $mitra->id_mitra)
            ->orderByDesc('is_aktif')
            ->orderBy('nama');

        if ($filterStatus === 'habis') {
            $query->where('stok', '<=', 0)->where('is_aktif', true);
        } elseif ($filterStatus === 'menipis') {
            $query->whereColumn('stok', '<=', 'stok_min')
                  ->where('stok', '>', 0)
                  ->where('is_aktif', true);
        } elseif ($filterStatus === 'nonaktif') {
            $query->where('is_aktif', false);
        }

        if ($filterQuery !== '') {
            $query->where(function ($q) use ($filterQuery) {
                $q->where('nama', 'like', "%{$filterQuery}%")
                  ->orWhere('kode', 'like', "%{$filterQuery}%")
                  ->orWhere('kategori', 'like', "%{$filterQuery}%");
            });
        }

        $spareparts = $query->get();

        // Stats whole inventory (tanpa filter)
        $allItems = MitraSparepart::where('mitra_id', $mitra->id_mitra)->get();
        $stats = [
            'total_item'    => $allItems->count(),
            'total_nilai'   => $allItems->sum(fn($s) => $s->stok * (float) $s->harga),
            'habis'         => $allItems->filter(fn($s) => $s->is_aktif && $s->stok <= 0)->count(),
            'menipis'       => $allItems->filter(fn($s) => $s->is_aktif && $s->stok > 0 && $s->stok <= $s->stok_min)->count(),
        ];

        return view('mitra.sparepart.index', compact(
            'mitra', 'spareparts', 'stats', 'filterStatus', 'filterQuery'
        ));
    }

    public function create()
    {
        $mitra = Auth::guard('mitra')->user();
        return view('mitra.sparepart.create', compact('mitra'));
    }

    public function store(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $data = $this->validateInput($request);

        if ($request->hasFile('foto')) {
            $data['foto_path'] = $request->file('foto')->store(
                "spareparts/{$mitra->id_mitra}",
                'public'
            );
        }
        $data['mitra_id'] = $mitra->id_mitra;
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        MitraSparepart::create($data);

        return redirect()->route('mitra.sparepart.index')
            ->with('success', 'Sparepart ditambahkan.');
    }

    public function edit(MitraSparepart $sparepart)
    {
        $this->authorize_($sparepart);
        $mitra = Auth::guard('mitra')->user();
        return view('mitra.sparepart.edit', compact('mitra', 'sparepart'));
    }

    public function update(Request $request, MitraSparepart $sparepart)
    {
        $this->authorize_($sparepart);

        $data = $this->validateInput($request);

        if ($request->hasFile('foto')) {
            if ($sparepart->foto_path && Storage::disk('public')->exists($sparepart->foto_path)) {
                Storage::disk('public')->delete($sparepart->foto_path);
            }
            $mitra = Auth::guard('mitra')->user();
            $data['foto_path'] = $request->file('foto')->store(
                "spareparts/{$mitra->id_mitra}",
                'public'
            );
        }
        $data['is_aktif'] = $request->boolean('is_aktif');

        $sparepart->update($data);

        return redirect()->route('mitra.sparepart.index')
            ->with('success', 'Sparepart diperbarui.');
    }

    /** Quick action: tambah/kurang stok dari halaman list. */
    public function ubahStok(Request $request, MitraSparepart $sparepart)
    {
        $this->authorize_($sparepart);

        $data = $request->validate([
            'aksi'   => 'required|in:tambah,kurang',
            'jumlah' => 'required|integer|min:1|max:99999',
        ]);

        if ($data['aksi'] === 'tambah') {
            $sparepart->increment('stok', $data['jumlah']);
            $msg = "+{$data['jumlah']} stok ditambahkan.";
        } else {
            $jumlah = min($data['jumlah'], $sparepart->stok);
            $sparepart->decrement('stok', $jumlah);
            $msg = "-{$jumlah} stok dikurangi.";
        }

        return back()->with('success', "{$sparepart->nama}: {$msg}");
    }

    public function toggleAktif(MitraSparepart $sparepart)
    {
        $this->authorize_($sparepart);
        $sparepart->update(['is_aktif' => !$sparepart->is_aktif]);
        return back()->with('success', 'Status aktif diperbarui.');
    }

    public function destroy(MitraSparepart $sparepart)
    {
        $this->authorize_($sparepart);

        if ($sparepart->foto_path && Storage::disk('public')->exists($sparepart->foto_path)) {
            Storage::disk('public')->delete($sparepart->foto_path);
        }
        $sparepart->delete();
        return redirect()->route('mitra.sparepart.index')->with('success', 'Sparepart dihapus.');
    }

    private function validateInput(Request $request): array
    {
        return $request->validate([
            'nama'         => 'required|string|max:200',
            'kode'         => 'nullable|string|max:100',
            'kategori'     => 'nullable|string|max:100',
            'deskripsi'    => 'nullable|string|max:1000',
            'harga'        => 'required|numeric|min:0|max:99999999',
            'harga_modal'  => 'nullable|numeric|min:0|max:99999999',
            'stok'         => 'required|integer|min:0|max:99999',
            'stok_min'     => 'nullable|integer|min:0|max:99999',
            'satuan'       => 'required|string|max:20',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
    }

    private function authorize_(MitraSparepart $sparepart): void
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $sparepart->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke sparepart ini.');
        }
    }
}
