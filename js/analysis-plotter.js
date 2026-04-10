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
        if (!canvas) {
            return;
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

        if (typeof Chart === 'undefined') {
            AnalysisPlotter.drawFallback(canvas, points, totalSpan, this.container);
            return;
        }

        if (!AnalysisPlotter.instances) {
            AnalysisPlotter.instances = {};
        }

        if (AnalysisPlotter.instances[this.container]) {
            AnalysisPlotter.instances[this.container].destroy();
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

AnalysisPlotter.drawFallback = function (canvas, points, totalSpan, container) {
    if (!points || points.length === 0) {
        return;
    }

    var dpr = window.devicePixelRatio || 1;
    var width = Math.max(canvas.clientWidth, 500);
    var height = Math.max(canvas.clientHeight, 320);
    canvas.width = Math.floor(width * dpr);
    canvas.height = Math.floor(height * dpr);

    var ctx = canvas.getContext('2d');
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    ctx.clearRect(0, 0, width, height);

    var pad = { left: 55, right: 20, top: 15, bottom: 35 };
    var plotW = width - pad.left - pad.right;
    var plotH = height - pad.top - pad.bottom;

    var minY = points[0].y;
    var maxY = points[0].y;
    for (var i = 1; i < points.length; i++) {
        minY = Math.min(minY, points[i].y);
        maxY = Math.max(maxY, points[i].y);
    }
    minY = Math.min(minY, 0);
    maxY = Math.max(maxY, 0);
    if (Math.abs(maxY - minY) < 1e-9) {
        maxY += 1;
        minY -= 1;
    }

    function mapX(x) {
        return pad.left + (x / totalSpan) * plotW;
    }
    function mapY(y) {
        return pad.top + ((maxY - y) / (maxY - minY)) * plotH;
    }

    ctx.strokeStyle = '#d0d0d0';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(pad.left, mapY(0));
    ctx.lineTo(width - pad.right, mapY(0));
    ctx.moveTo(pad.left, pad.top);
    ctx.lineTo(pad.left, height - pad.bottom);
    ctx.stroke();

    ctx.strokeStyle = 'red';
    ctx.lineWidth = 2.5;
    ctx.beginPath();
    ctx.moveTo(mapX(points[0].x), mapY(points[0].y));
    for (var j = 1; j < points.length; j++) {
        ctx.lineTo(mapX(points[j].x), mapY(points[j].y));
    }
    ctx.stroke();

    var yLabel = 'Deflection (mm)';
    if (container === 'bending_moment_plot') {
        yLabel = 'Bending Moment (kNm)';
    } else if (container === 'shear_force_plot') {
        yLabel = 'Shear Force (kN)';
    }

    ctx.fillStyle = '#333';
    ctx.font = '12px sans-serif';
    ctx.fillText('Span (m)', width / 2 - 22, height - 10);
    ctx.save();
    ctx.translate(16, height / 2 + 20);
    ctx.rotate(-Math.PI / 2);
    ctx.fillText(yLabel, 0, 0);
    ctx.restore();
};
