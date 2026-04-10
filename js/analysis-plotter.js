'use strict';

/**
 * Plot result from the beam analysis calculation into a graph
 */
class AnalysisPlotter {
    constructor(container) {
        this.container = container;
    }

    /**
     * Plot equation.
     *
     * @param {Object{beam : Beam, load : float, equation: Function}}  The equation data
     */
    plot(data) {
        var canvas = document.getElementById(this.container);
        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        if (!AnalysisPlotter.instances) {
            AnalysisPlotter.instances = {};
        }

        if (AnalysisPlotter.instances[this.container]) {
            AnalysisPlotter.instances[this.container].destroy();
        }

        var spanPrimary = Number(data.beam && data.beam.primarySpan);
        var spanSecondary = Number(data.beam && data.beam.secondarySpan);
        var totalSpan = Number.isFinite(spanSecondary) && spanSecondary > 0
            ? spanPrimary + spanSecondary
            : spanPrimary;

        if (!Number.isFinite(totalSpan) || totalSpan <= 0) {
            return;
        }

        var points = [];
        var steps = 200;
        var condition = Number.isFinite(spanSecondary) && spanSecondary > 0
            ? 'two-span-unequal'
            : 'simply-supported';

        function pushPoint(x) {
            var p = data.equation(x);
            if (p && Number.isFinite(p.y)) {
                points.push({ x: x, y: p.y });
            }
        }

        for (var i = 0; i <= steps; i++) {
            pushPoint((totalSpan * i) / steps);
        }

        // Insert duplicated x values around middle support to visualize shear jump.
        if (condition === 'two-span-unequal' && this.container === 'shear_force_plot') {
            var l1 = spanPrimary;
            points.push({ x: l1, y: data.equation(Math.max(l1 - 1e-6, 0)).y });
            points.push({ x: l1, y: data.equation(l1).y });
            points.sort(function (a, b) {
                return a.x - b.x;
            });
        }

        var config = AnalysisPlotter.getChartConfig(this.container, points, totalSpan);
        AnalysisPlotter.instances[this.container] = new Chart(canvas, config);
    }
}

AnalysisPlotter.getChartConfig = function (container, points, totalSpan) {
    var yLabel = '';
    var chartLabel = '';
    var tension = 0.25;

    if (container === 'bending_moment_plot') {
        chartLabel = 'Bending Moment';
        yLabel = 'Bending Moment (kNm)';
    } else if (container === 'shear_force_plot') {
        chartLabel = 'Shear Force';
        yLabel = 'Shear Force (kN)';
        tension = 0;
    } else {
        chartLabel = 'Deflection';
        yLabel = 'Deflection (mm)';
    }

    return {
        type: 'line',
        data: {
            datasets: [{
                label: chartLabel,
                data: points,
                borderColor: 'red',
                borderWidth: 3,
                pointRadius: 0,
                fill: false,
                tension: tension
            }]
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    type: 'linear',
                    min: 0,
                    max: totalSpan,
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
    };
};
