@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Approval Penarikan</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Mitra</th>
                <th>Bank</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($withdrawals as $withdrawal)
            <tr>
                <td>{{ $withdrawal->mitra->nama_panggilan }}</td>
                <td>{{ $withdrawal->bank_name }} - {{ $withdrawal->account_number }} ({{ $withdrawal->account_name }})</td>
                <td>Rp{{ number_format($withdrawal->nominal) }}</td>
                <td>{{ $withdrawal->status }}</td>
                <td>
                    <form action="{{ route('admin.finance.withdrawal.approve', $withdrawal->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form action="{{ route('admin.finance.withdrawal.reject', $withdrawal->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
