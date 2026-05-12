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
        return view('admin.roles.create', compact('allFeatures'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'features'    => 'array',
            'features.*'  => 'string',
        ]);

        $role = Role::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_default'  => false,
        ]);
        $role->syncFeatures($request->features ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' berhasil dibuat.");
    }

    public function edit(Role $role)
    {
        $allFeatures = Role::ALL_FEATURES;
        $activeFeatures = $role->features->pluck('feature_key')->toArray();
        return view('admin.roles.edit', compact('role', 'allFeatures', 'activeFeatures'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'features'    => 'array',
            'features.*'  => 'string',
        ]);

        $role->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);
        $role->syncFeatures($request->features ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' berhasil diperbarui.");
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
