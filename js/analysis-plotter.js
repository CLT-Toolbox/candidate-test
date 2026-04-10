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

        AnalysisPlotter.drawSvg(canvas, points, totalSpan, this.container);
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

AnalysisPlotter.drawSvg = function (canvas, points, totalSpan, container) {
    if (!points || points.length === 0) {
        return;
    }

    var width = Math.max(canvas.clientWidth, 500);
    var height = Math.max(canvas.clientHeight, 320);
    var pad = { left: 56, right: 20, top: 16, bottom: 36 };
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
        minY -= 1;
        maxY += 1;
    }

    function mapX(x) {
        return pad.left + (x / totalSpan) * plotW;
    }
    function mapY(y) {
        return pad.top + ((maxY - y) / (maxY - minY)) * plotH;
    }

    var polyline = '';
    for (var j = 0; j < points.length; j++) {
        polyline += mapX(points[j].x) + ',' + mapY(points[j].y) + ' ';
    }

    var yLabel = 'Deflection (mm)';
    if (container === 'bending_moment_plot') {
        yLabel = 'Bending Moment (kNm)';
    } else if (container === 'shear_force_plot') {
        yLabel = 'Shear Force (kN)';
    }

    var zeroY = mapY(0);
    var wrapperId = container + '_svg_wrap';
    var wrapper = document.getElementById(wrapperId);
    if (!wrapper) {
        wrapper = document.createElement('div');
        wrapper.id = wrapperId;
        wrapper.style.width = '100%';
        wrapper.style.height = '320px';
        wrapper.style.border = '1px solid #ddd';
        wrapper.style.margin = '20px 0';
        canvas.parentNode.insertBefore(wrapper, canvas);
    }
    canvas.style.display = 'none';

    wrapper.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" width="' + width + '" height="' + height + '" viewBox="0 0 ' + width + ' ' + height + '">' +
            '<rect x="0" y="0" width="' + width + '" height="' + height + '" fill="#fff"/>' +
            '<line x1="' + pad.left + '" y1="' + zeroY + '" x2="' + (width - pad.right) + '" y2="' + zeroY + '" stroke="#c8c8c8" stroke-width="1"/>' +
            '<line x1="' + pad.left + '" y1="' + pad.top + '" x2="' + pad.left + '" y2="' + (height - pad.bottom) + '" stroke="#c8c8c8" stroke-width="1"/>' +
            '<polyline points="' + polyline.trim() + '" fill="none" stroke="red" stroke-width="2.5"/>' +
            '<text x="' + (width / 2 - 24) + '" y="' + (height - 10) + '" font-size="12" fill="#333">Span (m)</text>' +
            '<text x="18" y="' + (height / 2 + 24) + '" transform="rotate(-90 18 ' + (height / 2 + 24) + ')" font-size="12" fill="#333">' + yLabel + '</text>' +
        '</svg>';
};
