(function ($) {
    'use strict';

    function initDashboardCharts() {
        if (typeof Chart === 'undefined') {
            return;
        }

        var cfg = window.eshterakyar_dashboard_charts;
        if (!cfg || !cfg.renderCharts) {
            return;
        }

        var subscriptionsCtx = document.getElementById('subscriptionsChart');
        if (subscriptionsCtx) {
            new Chart(subscriptionsCtx, {
                type: 'line',
                data: {
                    labels: cfg.labels || [],
                    datasets: [{
                        label: cfg.i18n.subscriptionsLabel,
                        data: cfg.counts || [],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e5e7eb', drawBorder: false },
                            ticks: {
                                color: '#6b7280',
                                font: { size: 12 },
                                stepSize: 1
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#6b7280', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        var statusCtx = document.getElementById('statusChart');
        if (statusCtx && cfg.status) {
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: [cfg.i18n.active, cfg.i18n.expired, cfg.i18n.cancelled],
                    datasets: [{
                        data: [
                            cfg.status.active,
                            cfg.status.expired,
                            cfg.status.cancelled
                        ],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: { size: 13, weight: '500' },
                                color: '#374151'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    }

    $(initDashboardCharts);
})(jQuery);
