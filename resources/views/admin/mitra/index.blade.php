@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-gray-800">Manajemen Mitra</h1>
            <a href="{{ route('mitra.create') }}" class="btn btn-primary">Tambah Mitra</a>
        </div>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mitra</th>
                            <th>Kategori</th>
                            <th>Alamat</th>
                            <th>Nomor WA</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mitras as $mitra)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $mitra->nama }}</td>
                                <td>{{ $mitra->kategori }}</td>
                                <td>{{ $mitra->alamat }}</td>
                                <td>{{ $mitra->nomor_wa }}</td>
                                <td>{{ $mitra->status ?? 'Aktif' }}</td>
                                <td>
                                    <a href="{{ route('mitra.edit', $mitra->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('mitra.destroy', $mitra->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Data mitra belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
