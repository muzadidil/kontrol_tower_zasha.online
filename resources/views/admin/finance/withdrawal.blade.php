@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Riwayat Penarikan Dana</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Bank</th>
                <th>Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($withdrawals as $withdrawal)
            <tr>
                <td>{{ $withdrawal->created_at }}</td>
                <td>{{ $withdrawal->bank_name }} - {{ $withdrawal->account_number }}</td>
                <td>Rp{{ number_format($withdrawal->nominal) }}</td>
                <td>{{ $withdrawal->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
