<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\MitraPortfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MitraPortfolioController extends Controller
{
    public function index()
    {
        $mitra = Auth::guard('mitra')->user();
        $portfolios = MitraPortfolio::where('mitra_id', $mitra->id_mitra)
            ->orderByDesc('is_featured')
            ->orderBy('urutan')
            ->orderByDesc('created_at')
            ->get();

        return view('mitra.portfolio.index', compact('mitra', 'portfolios'));
    }

    public function create()
    {
        $mitra = Auth::guard('mitra')->user();
        return view('mitra.portfolio.create', compact('mitra'));
    }

    public function store(Request $request)
    {
        $mitra = Auth::guard('mitra')->user();

        $data = $request->validate([
            'judul'       => 'required|string|max:200',
            'deskripsi'   => 'nullable|string|max:2000',
            'kategori'    => 'nullable|string|max:100',
            'link_url'    => 'nullable|url|max:500',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,pdf|max:10240',
            'is_featured' => 'nullable|boolean',
        ]);

        if (!$request->hasFile('file') && !$request->filled('link_url')) {
            return back()->withInput()
                ->with('error', 'Wajib upload file atau isi link.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store(
                "portfolio/{$mitra->id_mitra}",
                'public'
            );
        }

        MitraPortfolio::create([
            'mitra_id'    => $mitra->id_mitra,
            'judul'       => $data['judul'],
            'deskripsi'   => $data['deskripsi'] ?? null,
            'kategori'    => $data['kategori'] ?? null,
            'link_url'    => $data['link_url'] ?? null,
            'file_path'   => $filePath,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        return redirect()->route('mitra.portfolio.index')
            ->with('success', 'Portfolio berhasil ditambahkan.');
    }

    public function edit(MitraPortfolio $portfolio)
    {
        $this->authorize_($portfolio);
        $mitra = Auth::guard('mitra')->user();
        return view('mitra.portfolio.edit', compact('mitra', 'portfolio'));
    }

    public function update(Request $request, MitraPortfolio $portfolio)
    {
        $this->authorize_($portfolio);

        $data = $request->validate([
            'judul'       => 'required|string|max:200',
            'deskripsi'   => 'nullable|string|max:2000',
            'kategori'    => 'nullable|string|max:100',
            'link_url'    => 'nullable|url|max:500',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,pdf|max:10240',
            'is_featured' => 'nullable|boolean',
        ]);

        $updateData = [
            'judul'       => $data['judul'],
            'deskripsi'   => $data['deskripsi'] ?? null,
            'kategori'    => $data['kategori'] ?? null,
            'link_url'    => $data['link_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
        ];

        if ($request->hasFile('file')) {
            // hapus file lama bila ada
            if ($portfolio->file_path && Storage::disk('public')->exists($portfolio->file_path)) {
                Storage::disk('public')->delete($portfolio->file_path);
            }
            $mitra = Auth::guard('mitra')->user();
            $updateData['file_path'] = $request->file('file')->store(
                "portfolio/{$mitra->id_mitra}",
                'public'
            );
        }

        $portfolio->update($updateData);

        return redirect()->route('mitra.portfolio.index')
            ->with('success', 'Portfolio berhasil diperbarui.');
    }

    public function toggleFeatured(MitraPortfolio $portfolio)
    {
        $this->authorize_($portfolio);
        $portfolio->update(['is_featured' => !$portfolio->is_featured]);
        return back()->with('success', 'Status featured diperbarui.');
    }

    public function destroy(MitraPortfolio $portfolio)
    {
        $this->authorize_($portfolio);

        if ($portfolio->file_path && Storage::disk('public')->exists($portfolio->file_path)) {
            Storage::disk('public')->delete($portfolio->file_path);
        }

        $portfolio->delete();
        return redirect()->route('mitra.portfolio.index')
            ->with('success', 'Portfolio dihapus.');
    }

    private function authorize_(MitraPortfolio $portfolio): void
    {
        $mitraId = Auth::guard('mitra')->user()->id_mitra;
        if ((int) $portfolio->mitra_id !== (int) $mitraId) {
            abort(403, 'Anda tidak punya akses ke portfolio ini.');
        }
    }
}
