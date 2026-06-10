@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="admin-hero">
    <div class="admin-hero-text">
        <h2>Hoş geldiniz{{ auth()->user()->first_name ? ', '.auth()->user()->first_name : '' }}</h2>
        <p>Platform özetini buradan takip edin. Veriler her 15 saniyede otomatik yenilenir.</p>
        <p class="admin-live-indicator"><span class="admin-live-dot" aria-hidden="true"></span> Canlı veri — <span id="adminDashboardUpdatedAt">—</span></p>
    </div>
    <div class="admin-hero-actions">
        <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">Kullanıcılar</a>
        <a href="{{ route('admin.reports') }}" class="btn btn-primary btn-sm">Şikayetler</a>
    </div>
</div>

<div class="stat-grid">
        <div class="stat-card stat-card--users">
            <div class="stat-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-6 8-6s8 2 8 6"/></svg>
            </div>
            <div class="stat-card-body">
                <div class="stat-value" id="statTotalUsers">{{ $stats['total_users'] }}</div>
                <div class="stat-label">Toplam Kullanıcı</div>
            </div>
        </div>
        <div class="stat-card stat-card--male">
            <div class="stat-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="14" r="5"/><path d="M19 5l-5.4 5.4M19 5h-5M19 5v5"/></svg>
            </div>
            <div class="stat-card-body">
                <div class="stat-value" id="statActiveMale">{{ $stats['active_male'] }}</div>
                <div class="stat-label">Aktif Erkek</div>
            </div>
        </div>
        <div class="stat-card stat-card--female">
            <div class="stat-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="9" r="4"/><path d="M12 13v8M9 18h6"/></svg>
            </div>
            <div class="stat-card-body">
                <div class="stat-value" id="statActiveFemale">{{ $stats['active_female'] }}</div>
                <div class="stat-label">Aktif Kadın</div>
            </div>
        </div>
        <div class="stat-card stat-card--reports">
            <div class="stat-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.6L2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0z"/></svg>
            </div>
            <div class="stat-card-body">
                <div class="stat-value" id="statPendingReports">{{ $stats['pending_reports'] }}</div>
                <div class="stat-label">Bekleyen Şikayet</div>
            </div>
        </div>
        <div class="stat-card stat-card--premium">
            <div class="stat-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.1 8.6 22 9.3 17 14.1 18.2 21 12 17.8 5.8 21 7 14.1 2 9.3 8.9 8.6 12 2"/></svg>
            </div>
            <div class="stat-card-body">
                <div class="stat-value" id="statActivePremium">{{ $stats['active_premium'] }}</div>
                <div class="stat-label">Aktif Premium</div>
            </div>
        </div>
        <div class="stat-card stat-card--revenue">
            <div class="stat-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div class="stat-card-body">
                <div class="stat-value" id="statRevenue">{{ number_format($stats['revenue_tl'], 0) }} <small>TL</small></div>
                <div class="stat-label">Toplam Gelir</div>
            </div>
        </div>
</div>

<section class="admin-charts-section" aria-label="Canlı grafikler">
    <h2 class="admin-section-title">Canlı Grafikler</h2>
    <div class="admin-charts-grid">
        <div class="admin-chart-card admin-chart-card--wide">
            <h3>Yeni Üyeler (14 gün)</h3>
            <div class="admin-chart-wrap">
                <canvas id="chartUserSignups" aria-label="Yeni üye grafiği"></canvas>
            </div>
        </div>
        <div class="admin-chart-card">
            <h3>Aktif Kullanıcı — Cinsiyet</h3>
            <div class="admin-chart-wrap admin-chart-wrap--doughnut">
                <canvas id="chartGender" aria-label="Cinsiyet dağılımı"></canvas>
            </div>
            <div class="admin-gender-legend">
                <span><i class="admin-gender-dot admin-gender-dot--male"></i> Erkek: <strong id="legendMale">{{ $chartData['gender']['male'] }}</strong></span>
                <span><i class="admin-gender-dot admin-gender-dot--female"></i> Kadın: <strong id="legendFemale">{{ $chartData['gender']['female'] }}</strong></span>
            </div>
        </div>
        <div class="admin-chart-card admin-chart-card--wide">
            <h3>Günlük Mesajlar</h3>
            <div class="admin-chart-wrap">
                <canvas id="chartMessages" aria-label="Mesaj grafiği"></canvas>
            </div>
        </div>
        <div class="admin-chart-card">
            <h3>Aktif / Banlı Kullanıcılar</h3>
            <div class="admin-chart-wrap admin-chart-wrap--bar">
                <canvas id="chartGenderBar" aria-label="Erkek kadın bar grafiği"></canvas>
            </div>
        </div>
        <div class="admin-chart-card admin-chart-card--wide">
            <h3>Premium Satışları</h3>
            <div class="admin-chart-wrap">
                <canvas id="chartPremium" aria-label="Premium satış grafiği"></canvas>
            </div>
        </div>
    </div>
</section>

<div class="admin-quick-grid">
    <a href="{{ route('admin.messages') }}" class="admin-quick-card">
        <span class="admin-quick-title">Mesaj Denetimi</span>
        <span class="admin-quick-desc">Kullanıcı mesajlarını inceleyin</span>
    </a>
    <a href="{{ route('admin.broadcasts') }}" class="admin-quick-card">
        <span class="admin-quick-title">Duyuru Gönder</span>
        <span class="admin-quick-desc">Toplu bildirim oluşturun</span>
    </a>
    <a href="{{ config('app.url') }}" class="admin-quick-card">
        <span class="admin-quick-title">Ana Site</span>
        <span class="admin-quick-desc">gonulkoprusu.com</span>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
window.adminDashboardConfig = {
    statsUrl: @json(route('admin.dashboard.stats')),
    initial: @json(['stats' => $stats, 'charts' => $chartData]),
};
</script>
<script src="{{ rtrim(config('app.asset_url') ?: config('app.url'), '/') }}/js/admin-dashboard.js?v=2"></script>
@endsection
