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

    var parentWidth = canvas.parentNode && canvas.parentNode.clientWidth
        ? canvas.parentNode.clientWidth
        : canvas.clientWidth;
    var width = Math.max(parentWidth - 12, 700);
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
    var xTicks = AnalysisPlotter.buildTicks(0, totalSpan, 9);
    var yTicks = AnalysisPlotter.buildTicks(minY, maxY, 9);
    var plotTop = pad.top;
    var plotBottom = height - pad.bottom;
    var plotLeft = pad.left;
    var plotRight = width - pad.right;

    var shade = '';
    if (zeroY > plotTop && zeroY < plotBottom) {
        shade =
            '<rect x="' + plotLeft + '" y="' + plotTop + '" width="' + plotW + '" height="' + (zeroY - plotTop) + '" fill="#f5f5f5"/>' +
            '<rect x="' + plotLeft + '" y="' + zeroY + '" width="' + plotW + '" height="' + (plotBottom - zeroY) + '" fill="#ebebeb"/>';
    } else {
        shade = '<rect x="' + plotLeft + '" y="' + plotTop + '" width="' + plotW + '" height="' + plotH + '" fill="#f5f5f5"/>';
    }

    var xGrid = '';
    var xLabels = '';
    for (var i = 0; i < xTicks.values.length; i++) {
        var xv = xTicks.values[i];
        var xp = mapX(xv);
        xGrid += '<line x1="' + xp + '" y1="' + plotTop + '" x2="' + xp + '" y2="' + plotBottom + '" stroke="#d3d3d3" stroke-width="1"/>';
        xLabels += '<text x="' + xp + '" y="' + (height - 12) + '" font-size="12" fill="#555" text-anchor="middle">' +
            AnalysisPlotter.formatTick(xv, xTicks.step) +
            '</text>';
    }

    var yGrid = '';
    var yLabels = '';
    for (var k = 0; k < yTicks.values.length; k++) {
        var yv = yTicks.values[k];
        var yp = mapY(yv);
        yGrid += '<line x1="' + plotLeft + '" y1="' + yp + '" x2="' + plotRight + '" y2="' + yp + '" stroke="#d3d3d3" stroke-width="1"/>';
        yLabels += '<text x="' + (plotLeft - 8) + '" y="' + (yp + 4) + '" font-size="12" fill="#555" text-anchor="end">' +
            AnalysisPlotter.formatTick(yv, yTicks.step) +
            '</text>';
    }

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
            shade +
            yGrid +
            xGrid +
            '<line x1="' + plotLeft + '" y1="' + zeroY + '" x2="' + plotRight + '" y2="' + zeroY + '" stroke="#b0b0b0" stroke-width="1.2"/>' +
            '<line x1="' + plotLeft + '" y1="' + plotTop + '" x2="' + plotLeft + '" y2="' + plotBottom + '" stroke="#b0b0b0" stroke-width="1.2"/>' +
            xLabels +
            yLabels +
            '<polyline points="' + polyline.trim() + '" fill="none" stroke="red" stroke-width="2.5"/>' +
            '<text x="' + (width / 2 - 24) + '" y="' + (height - 10) + '" font-size="12" fill="#333">Span (m)</text>' +
            '<text x="18" y="' + (height / 2 + 24) + '" transform="rotate(-90 18 ' + (height / 2 + 24) + ')" font-size="12" fill="#333">' + yLabel + '</text>' +
        '</svg>';
};

AnalysisPlotter.niceNumber = function (range, round) {
    var exponent = Math.floor(Math.log10(range));
    var fraction = range / Math.pow(10, exponent);
    var niceFraction;

    if (round) {
        if (fraction < 1.5) {
            niceFraction = 1;
        } else if (fraction < 3) {
            niceFraction = 2;
        } else if (fraction < 7) {
            niceFraction = 5;
        } else {
            niceFraction = 10;
        }
    } else {
        if (fraction <= 1) {
            niceFraction = 1;
        } else if (fraction <= 2) {
            niceFraction = 2;
        } else if (fraction <= 5) {
            niceFraction = 5;
        } else {
            niceFraction = 10;
        }
    }

    return niceFraction * Math.pow(10, exponent);
};

AnalysisPlotter.buildTicks = function (min, max, targetTickCount) {
    var safeTarget = Math.max(2, targetTickCount || 8);
    var safeMin = min;
    var safeMax = max;

    if (safeMin === safeMax) {
        safeMin -= 1;
        safeMax += 1;
    }

    var range = AnalysisPlotter.niceNumber(safeMax - safeMin, false);
    var step = AnalysisPlotter.niceNumber(range / (safeTarget - 1), true);
    var niceMin = Math.floor(safeMin / step) * step;
    var niceMax = Math.ceil(safeMax / step) * step;

    var values = [];
    for (var v = niceMin; v <= niceMax + 0.5 * step; v += step) {
        values.push(Number(v.toFixed(10)));
    }

    return {
        min: niceMin,
        max: niceMax,
        step: step,
        values: values
    };
};

AnalysisPlotter.formatTick = function (value, step) {
    var v = Math.abs(value) < 1e-9 ? 0 : value;
    var absStep = Math.abs(step);
    var decimals = 0;

    if (absStep < 1) {
        decimals = 2;
    } else if (absStep < 2) {
        decimals = 1;
    }

    var text = v.toFixed(decimals);
    return text === '-0' || text === '-0.0' || text === '-0.00' ? '0' : text;
};
