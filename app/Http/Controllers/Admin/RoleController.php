<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('features')->orderBy('id')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $allFeatures = Role::ALL_FEATURES;
        $allVerifikasi = Role::ALL_VERIFIKASI;
        $activeVerifikasi = []; // belum ada
        return view('admin.roles.create', compact('allFeatures', 'allVerifikasi', 'activeVerifikasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:100',
            'icon_color'  => 'nullable|string|max:20',
            'features'    => 'array',
            'features.*'  => 'string',
            'verifikasi'  => 'array',
        ]);

        $role = Role::create([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon ?: Role::DEFAULT_ICON,
            'icon_color'  => $request->icon_color ?: Role::DEFAULT_ICON_COLOR,
            'is_default'  => false,
            'is_active'   => false, // role baru = draft, harus dirilis dulu
        ]);
        $role->syncFeatures($request->features ?? []);
        $role->syncVerifikasi($this->parseVerifikasiInput($request->verifikasi ?? []));

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' berhasil dibuat sebagai DRAFT. Klik 'Rilis & Aktifkan' untuk mengaktifkan.");
    }

    /**
     * Parse input verifikasi[$key][aktif|wajib] dari form ke format:
     *   ['ktp' => true, 'sim' => false]  -> key = verifikasi_key, value = wajib flag
     * Hanya item yang [aktif] di-include.
     */
    private function parseVerifikasiInput(array $verifikasiInput): array
    {
        $result = [];
        foreach ($verifikasiInput as $key => $val) {
            if (!empty($val['aktif'])) {
                $result[$key] = !empty($val['wajib']);
            }
        }
        return $result;
    }

    public function edit(Role $role)
    {
        $allFeatures = Role::ALL_FEATURES;
        $activeFeatures = $role->features->pluck('feature_key')->toArray();

        $allVerifikasi = Role::ALL_VERIFIKASI;
        // Map: ['ktp' => true, 'sim' => false] -> verifikasi_key dengan wajib flag
        $activeVerifikasi = $role->verifikasiKeys->pluck('wajib', 'verifikasi_key')->toArray();

        return view('admin.roles.edit', compact('role', 'allFeatures', 'activeFeatures', 'allVerifikasi', 'activeVerifikasi'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:100',
            'icon_color'  => 'nullable|string|max:20',
            'features'    => 'array',
            'features.*'  => 'string',
            'verifikasi'  => 'array',
        ]);

        $role->update([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon ?: Role::DEFAULT_ICON,
            'icon_color'  => $request->icon_color ?: Role::DEFAULT_ICON_COLOR,
        ]);
        $role->syncFeatures($request->features ?? []);
        $role->syncVerifikasi($this->parseVerifikasiInput($request->verifikasi ?? []));

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' berhasil diperbarui.");
    }

    /** Toggle status aktif/draft role. */
    public function toggleActive(Role $role)
    {
        $role->update(['is_active' => !$role->is_active]);
        $status = $role->is_active ? 'AKTIF dan siap dipakai' : 'di-set sebagai DRAFT';
        return back()->with('success', "Role '{$role->name}' kini {$status}.");
    }

    /** Bulk action: rilis semua / draft semua role sekaligus. */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:rilis-semua,draft-semua',
        ]);

        if ($request->action === 'rilis-semua') {
            $count = Role::where('is_active', false)->update(['is_active' => true]);
            return back()->with('success', "{$count} role berhasil dirilis & diaktifkan.");
        }

        // draft-semua: kecuali role default agar tidak melumpuhkan sistem
        $count = Role::where('is_active', true)
            ->where('is_default', false)
            ->update(['is_active' => false]);
        return back()->with('success', "{$count} role di-set sebagai DRAFT (role default tidak diubah).");
    }

    public function destroy(Role $role)
    {
        if ($role->is_default) {
            return back()->with('error', 'Role default tidak bisa dihapus.');
        }
        $name = $role->name;
        $role->delete();
        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$name}' dihapus.");
    }

    /** Assign role ke mitra (dipanggil dari halaman detail mitra). */
    public function assignToMitra(Request $request, \App\Models\Mitra $mitra)
    {
        $request->validate([
            'role_id' => 'nullable|exists:roles,id',
        ]);
        $mitra->update(['role_id' => $request->role_id]);

        $roleName = $mitra->role?->name ?? 'tanpa role';
        return back()->with('success', "Role mitra {$mitra->nama_panggilan} diubah ke: {$roleName}");
    }
}
