@extends('layouts.admin')

@section('title', 'Premium Takip')

@section('content')
<h1 class="admin-page-title">Premium Takip</h1>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $activeCount }}</div>
        <div class="stat-label">Aktif Abonelik</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ number_format($totalRevenue, 0) }} TL</div>
        <div class="stat-label">Toplam Gelir</div>
    </div>
    @foreach($tierDistribution as $tier => $count)
    <div class="stat-card">
        <div class="stat-value">{{ $count }}</div>
        <div class="stat-label">{{ ucfirst($tier) }}</div>
    </div>
    @endforeach
</div>

<div class="admin-table-wrap"><table class="admin-table">
    <thead>
        <tr>
            <th>Kullanıcı</th>
            <th>Paket</th>
            <th>Fiyat</th>
            <th>Bitiş</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subscriptions as $sub)
        <tr>
            <td>{{ $sub->user->username ?? '-' }}</td>
            <td>{{ ucfirst($sub->package_type) }}</td>
            <td>{{ $sub->price_tl }} TL</td>
            <td>{{ $sub->expires_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table></div>
@endsection
