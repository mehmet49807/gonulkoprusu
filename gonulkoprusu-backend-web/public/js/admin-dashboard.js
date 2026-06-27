(function () {
    const config = window.adminDashboardConfig;
    if (!config || typeof Chart === 'undefined') return;

    const palette = {
        teal: '#4A7C77',
        tealLight: '#6EAD9F',
        blue: '#4A8FC7',
        rose: '#C77B8E',
        gold: '#C9A227',
        muted: '#9BB8AE',
    };

    let charts = {};

    function buildCharts(data) {
        const chartsData = data.charts;
        const labels = chartsData.labels;

        if (charts.signups) {
            charts.signups.data.labels = labels;
            charts.signups.data.datasets[0].data = chartsData.user_signups;
            charts.signups.update('none');
        } else {
            charts.signups = new Chart(document.getElementById('chartUserSignups'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Yeni üye',
                        data: chartsData.user_signups,
                        borderColor: palette.teal,
                        backgroundColor: 'rgba(74, 124, 119, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    }],
                },
                options: chartLineOptions(),
            });
        }

        if (charts.gender) {
            charts.gender.data.datasets[0].data = [
                chartsData.gender.male,
                chartsData.gender.female,
                chartsData.gender.banned,
            ];
            charts.gender.update('none');
        } else {
            charts.gender = new Chart(document.getElementById('chartGender'), {
                type: 'doughnut',
                data: {
                    labels: ['Aktif Erkek', 'Aktif Kadın', 'Banlı'],
                    datasets: [{
                        data: [
                            chartsData.gender.male,
                            chartsData.gender.female,
                            chartsData.gender.banned,
                        ],
                        backgroundColor: [palette.blue, palette.rose, palette.muted],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    animation: { duration: 600 },
                },
            });
        }

        if (charts.genderBar) {
            charts.genderBar.data.datasets[0].data = [
                chartsData.gender.male,
                chartsData.gender.female,
            ];
            charts.genderBar.update('none');
        } else {
            charts.genderBar = new Chart(document.getElementById('chartGenderBar'), {
                type: 'bar',
                data: {
                    labels: ['Erkek', 'Kadın'],
                    datasets: [{
                        label: 'Aktif kullanıcı',
                        data: [chartsData.gender.male, chartsData.gender.female],
                        backgroundColor: [palette.blue, palette.rose],
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    animation: { duration: 600 },
                },
            });
        }

        if (charts.messages) {
            charts.messages.data.labels = labels;
            charts.messages.data.datasets[0].data = chartsData.messages;
            charts.messages.update('none');
        } else {
            charts.messages = new Chart(document.getElementById('chartMessages'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Mesaj',
                        data: chartsData.messages,
                        borderColor: palette.blue,
                        backgroundColor: 'rgba(74, 143, 199, 0.1)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    }],
                },
                options: chartLineOptions(),
            });
        }

        if (charts.premium) {
            charts.premium.data.labels = labels;
            charts.premium.data.datasets[0].data = chartsData.premium_sales;
            charts.premium.update('none');
        } else {
            charts.premium = new Chart(document.getElementById('chartPremium'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Premium satış',
                        data: chartsData.premium_sales,
                        backgroundColor: palette.gold,
                        borderRadius: 6,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                    },
                    animation: { duration: 600 },
                },
            });
        }
    }

    function chartLineOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
            },
            animation: { duration: 600 },
        };
    }

    function updateStatCards(stats) {
        const map = {
            statTotalUsers: stats.total_users,
            statActiveMale: stats.active_male,
            statActiveFemale: stats.active_female,
            statOnlineUsers: stats.online_users,
            statOfflineUsers: stats.offline_users,
            statPendingReports: stats.pending_reports,
            statActivePremium: stats.active_premium,
        };

        Object.entries(map).forEach(function ([id, value]) {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });

        const revenue = document.getElementById('statRevenue');
        if (revenue) {
            revenue.innerHTML = Number(stats.revenue_tl).toLocaleString('tr-TR') + ' <small>TL</small>';
        }

        const legendMale = document.getElementById('legendMale');
        const legendFemale = document.getElementById('legendFemale');
        if (legendMale) legendMale.textContent = stats.active_male;
        if (legendFemale) legendFemale.textContent = stats.active_female;
    }

    const REFRESH_MS = 15000;
    const liveIndicator = document.querySelector('.admin-live-indicator');
    let isRefreshing = false;

    function setUpdatedAt(value) {
        const updated = document.getElementById('adminDashboardUpdatedAt');
        if (updated) updated.textContent = value;
    }

    function applyPayload(payload) {
        updateStatCards(payload.stats);
        buildCharts(payload);
        setUpdatedAt(payload.updated_at || new Date().toLocaleString('tr-TR'));
    }

    async function refresh() {
        if (isRefreshing) return;
        isRefreshing = true;
        liveIndicator?.classList.add('admin-live-indicator--refreshing');

        try {
            const res = await fetch(config.statsUrl, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!res.ok) return;
            const json = await res.json();
            if (json.success && json.data) applyPayload(json.data);
        } catch (e) {
            /* silent */
        } finally {
            isRefreshing = false;
            liveIndicator?.classList.remove('admin-live-indicator--refreshing');
        }
    }

    applyPayload({
        stats: config.initial.stats,
        charts: config.initial.charts,
        updated_at: new Date().toLocaleString('tr-TR'),
    });

    setTimeout(refresh, 1500);
    setInterval(refresh, REFRESH_MS);
})();
