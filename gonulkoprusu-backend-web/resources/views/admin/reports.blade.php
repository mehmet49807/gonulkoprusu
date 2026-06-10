@extends('layouts.admin')

@section('title', 'Şikayetler')

@section('content')
<h1 class="admin-page-title">Şikayetler / Raporlar</h1>

<div class="admin-table-wrap"><table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Şikayet Eden</th>
            <th>Şikayet Edilen</th>
            <th>Sebep</th>
            <th>Durum</th>
            <th>Tarih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $report)
        <tr>
            <td>{{ $report->id }}</td>
            <td>{{ $report->reporter->username ?? '-' }}</td>
            <td>{{ $report->reported->username ?? '-' }}</td>
            <td>{{ Str::limit($report->reason, 50) }}</td>
            <td><span class="badge badge-{{ $report->status === 'pending' ? 'pending' : 'resolved' }}">{{ $report->status }}</span></td>
            <td>{{ $report->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table></div>

{{ $reports->links() }}
@endsection
