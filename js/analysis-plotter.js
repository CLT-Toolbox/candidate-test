'use strict';

class AnalysisPlotter {
    constructor(container) {
        this.container = container;
        this._chart = null;
    }

    _generatePoints(equation, totalLength, n) {
        var points = [];
        for (var i = 0; i <= n; i++) {
            var x = totalLength * i / n;
            var pt = equation(x);
            points.push({ x: pt.x, y: pt.y });
        }
        return points;
    }

    _generatePointsTwoSpan(equation, L1, L2, n) {
        var points = [];
        var n1 = Math.round(n * L1 / (L1 + L2));
        var n2 = n - n1;
        var eps = 1e-9;

        for (var i = 0; i <= n1; i++) {
            var x1 = L1 * i / n1;
            var pt = equation(x1);
            points.push({ x: pt.x, y: pt.y });
        }

        var ptR = equation(L1 + eps);
        points.push({ x: L1, y: ptR.y });

        for (var j = 1; j <= n2; j++) {
            var x2 = L1 + L2 * j / n2;
            var pt2 = equation(x2);
            points.push({ x: pt2.x, y: pt2.y });
        }

        return points;
    }

    _getTheme(containerId) {
        // Neon dark mode themes
        const themes = {
            'deflection_plot': {
                label: 'Deflection (mm)',
                color: '#ec4899', // Pink
                gradientStart: 'rgba(236, 72, 153, 0.5)',
                gradientEnd: 'rgba(236, 72, 153, 0.0)'
            },
            'shear_force_plot': {
                label: 'Shear Force (kN)',
                color: '#8b5cf6', // Purple
                gradientStart: 'rgba(139, 92, 246, 0.5)',
                gradientEnd: 'rgba(139, 92, 246, 0.0)'
            },
            'bending_moment_plot': {
                label: 'Bending Moment (kNm)',
                color: '#3b82f6', // Blue
                gradientStart: 'rgba(59, 130, 246, 0.5)',
                gradientEnd: 'rgba(59, 130, 246, 0.0)'
            }
        };
        return themes[containerId] || themes['deflection_plot'];
    }

    plot(data) {
        var beam = data.beam;
        var equation = data.equation;
        var L1 = beam.primarySpan;
        var L2 = beam.secondarySpan;
        var isTwoSpan = !isNaN(L2) && L2 > 0;
        var totalLength = isTwoSpan ? L1 + L2 : L1;
        var N = 300;

        var points = isTwoSpan
            ? this._generatePointsTwoSpan(equation, L1, L2, N)
            : this._generatePoints(equation, totalLength, N);

        var canvas = document.getElementById(this.container);
        if (!canvas) return;

        // Check for existing chart on this canvas using Chart.js API
        var existingChart = Chart.getChart(canvas);
        if (existingChart) {
            existingChart.destroy();
        }

        var theme = this._getTheme(this.container);
        var ctx = canvas.getContext('2d');

        // Create gradient fill
        var gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, theme.gradientStart);
        gradient.addColorStop(1, theme.gradientEnd);

        // Add a glow effect to the line
        ctx.shadowColor = theme.color;
        ctx.shadowBlur = 10;
        ctx.shadowOffsetX = 0;
        ctx.shadowOffsetY = 0;

        Chart.defaults.font.family = "'Outfit', system-ui, Arial, sans-serif";
        Chart.defaults.color = '#94a3b8';

        new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    label: theme.label,
                    data: points,
                    borderColor: theme.color,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: theme.color,
                    pointHoverBorderWidth: 3,
                    tension: 0.4, // Smooth curves
                    fill: 'origin',
                    backgroundColor: gradient
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (items) {
                                return 'Position: ' + Number(items[0].parsed.x).toFixed(3) + ' m';
                            },
                            label: function (item) {
                                return theme.label.split(' ')[0] + ': ' + Number(item.parsed.y).toFixed(3);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'linear',
                        title: {
                            display: true,
                            text: 'Span Length (m)',
                            font: { size: 14, weight: '500' },
                            color: '#94a3b8',
                            padding: { top: 12 }
                        },
                        ticks: {
                            stepSize: 0.5,
                            font: { size: 12 },
                            color: '#64748b',
                            callback: function (v) { return +v.toFixed(2); }
                        },
                        grid: { 
                            color: 'rgba(255,255,255,0.05)',
                            drawBorder: false
                        },
                        border: { display: false }
                    },
                    y: {
                        title: {
                            display: true,
                            text: theme.label,
                            font: { size: 14, weight: '500' },
                            color: '#94a3b8',
                            padding: { bottom: 12 }
                        },
                        ticks: {
                            font: { size: 12 },
                            color: '#64748b',
                            maxTicksLimit: 8
                        },
                        grid: { 
                            color: 'rgba(255,255,255,0.05)',
                            drawBorder: false,
                            borderDash: [5, 5] // Dashed grid lines
                        },
                        border: { display: false }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 20,
                        top: 20,
                        bottom: 10
                    }
                }
            }
        });
        
        // Reset shadow for other drawings if any
        ctx.shadowBlur = 0;
    }
}