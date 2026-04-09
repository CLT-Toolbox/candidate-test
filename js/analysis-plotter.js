'use strict';

class AnalysisPlotter {
    constructor(container) {
        this.container = container;
        this.chart = null;
    }

    plot(data) {
        var beam = data.beam;
        var equation = data.equation;
        var L1 = beam.primarySpan;
        var L2 = (!beam.secondarySpan || isNaN(beam.secondarySpan) || beam.secondarySpan <= 0) ? 0 : beam.secondarySpan;
        var totalLength = L1 + L2;
        var steps = 200;
        var points = [];

        for (var i = 0; i <= steps; i++) {
            var x = (i / steps) * totalLength;
            var result = equation(x);
            points.push({ x: parseFloat(result.x.toFixed(6)), y: parseFloat(result.y.toFixed(6)) });
        }

        var canvas = document.getElementById(this.container);
        var ctx = canvas.getContext('2d');

        var existingChart = Chart.getChart(canvas);
        if (existingChart) {
            existingChart.destroy();
        }

        var canvasId = this.container;
        var yLabel = 'Value';
        var title = 'Analysis Plot';

        if (canvasId === 'deflection_plot') {
            yLabel = 'Deflection (mm)';
            title = 'Deflection Plot';
        } else if (canvasId === 'shear_force_plot') {
            yLabel = 'Shear Force (kN)';
            title = 'Shear Force Plot';
        } else if (canvasId === 'bending_moment_plot') {
            yLabel = 'Bending Moment (kNm)';
            title = 'Bending Moment Plot';
        }

        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    label: title,
                    data: points,
                    borderColor: 'rgba(220, 38, 38, 1)',
                    backgroundColor: 'rgba(209, 213, 219, 0.5)',
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                animation: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: title
                    }
                },
                scales: {
                    x: {
                        type: 'linear',
                        title: {
                            display: true,
                            text: 'Span (m)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: yLabel
                        }
                    }
                }
            }
        });
    }
}