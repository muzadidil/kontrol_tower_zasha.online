@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3>Deposit Manual</h3>
    <form action="{{ route('admin.finance.storeDeposit') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tipe User</label>
            <select name="tipe_user" class="form-control" required>
                <option value="Pelanggan">Pelanggan</option>
                <option value="Mitra">Mitra</option>
            </select>
        </div>
        <div class="mb-3">
            <label>User ID</label>
            <input type="number" name="user_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Jumlah Nominal</label>
            <input type="number" name="jumlah_nominal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Deposit</button>
    </form>
</div>
@endsection
