'use strict';

/**
 * Plot beam analysis results using Chart.js.
 */
class AnalysisPlotter {
    constructor(container) {
        this.container = container;
        this._chart = null;
    }

    _axisLabels(kind) {
        if (kind === 'deflection') {
            return { x: 'Span (m)', y: 'Deflection (mm)' };
        }
        if (kind === 'shear') {
            return { x: 'Span (m)', y: 'Shear Force (kN)' };
        }
        return { x: 'Span (m)', y: 'Bending Moment (kNm)' };
    }

    _detectKind() {
        if (this.container === 'deflection_plot') {
            return 'deflection';
        }
        if (this.container === 'shear_force_plot') {
            return 'shear';
        }
        if (this.container === 'bending_moment_plot') {
            return 'moment';
        }
        return 'moment';
    }

    _spanLength(beam) {
        var p = beam.primarySpan;
        var s = beam.secondarySpan;
        if (typeof s === 'number' && !isNaN(s) && s > 0) {
            return p + s;
        }
        return p;
    }

    _sampleXs(beam, kind, totalLen, primarySpan) {
        var n = 200;
        var xs = [];
        var i;
        var twoSpan =
            typeof beam.secondarySpan === 'number' &&
            !isNaN(beam.secondarySpan) &&
            beam.secondarySpan > 0;
        var L1 = primarySpan;
        var eps = Math.max(totalLen * 1e-7, 1e-9);
        var prev;
        var x;

        if (kind === 'shear' && twoSpan && L1 > 0 && L1 < totalLen) {
            prev = -1;
            for (i = 0; i <= n; i++) {
                x = (totalLen * i) / n;
                if (i > 0 && prev < L1 && x >= L1) {
                    xs.push(L1 - eps);
                    xs.push(L1 + eps);
                }
                xs.push(x);
                prev = x;
            }
            return xs;
        }

        for (i = 0; i <= n; i++) {
            xs.push((totalLen * i) / n);
        }
        return xs;
    }

    /**
     * @param {Object{beam : Beam, load : float, equation: Function}}  data
     */
    plot(data) {
        var canvas = document.getElementById(this.container);
        if (!canvas || !canvas.getContext) {
            console.warn('Canvas not found:', this.container);
            return;
        }

        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }

        var kind = this._detectKind();
        var labels = this._axisLabels(kind);
        var beam = data.beam;
        var eq = data.equation;
        var L = this._spanLength(beam);
        var primarySpan = beam.primarySpan;
        var xs = this._sampleXs(beam, kind, L, primarySpan);

        var chartData = xs.map(function (xv) {
            var p = eq(xv);
            return { x: p.x, y: p.y };
        });

        if (this._chart) {
            this._chart.destroy();
        }

        var ctx = canvas.getContext('2d');
        var tension = kind === 'shear' ? 0 : 0.15;
        var useFill = kind !== 'deflection';

        this._chart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [
                    {
                        label: labels.y,
                        data: chartData,
                        parsing: false,
                        borderColor: 'rgb(211, 47, 47)',
                        backgroundColor: 'rgba(190, 190, 190, 0.35)',
                        borderWidth: 2,
                        pointRadius: 0,
                        tension: tension,
                        fill: useFill ? 'origin' : false,
                        spanGaps: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        type: 'linear',
                        title: {
                            display: true,
                            text: labels.x
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.08)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: labels.y
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.08)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}
