'use strict';

/**
 * Plot result from the beam analysis calculation into a graph
 */
class AnalysisPlotter {
    constructor(container) {
        this.container = container;
        this.chart = null;
    }

    /**
     * Plot equation.
     *
     * @param {Object{beam : Beam, load : float, equation: Function}}  The equation data
     */
    plot(data) {
        var canvas = document.getElementById(this.container);

        if (!canvas) {
            throw new Error('Canvas not found for container: ' + this.container);
        }

        if (typeof Chart === 'undefined') {
            throw new Error('Chart.js is required but not loaded.');
        }

        var axisMeta = this.getAxisMeta();
        var points = this.buildPoints(data);
        var existingChart = Chart.getChart(canvas);

        if (existingChart) {
            existingChart.destroy();
        }

        if (this.chart) {
            this.chart.destroy();
        }

        this.chart = new Chart(canvas, {
            type: 'line',
            data: {
                datasets: [{
                    data: points,
                    parsing: false,
                    borderColor: '#ff3b1f',
                    borderWidth: 3,
                    pointRadius: 0,
                    tension: 0.25,
                    fill: this.container === 'deflection_plot' ? false : {
                        target: 'origin',
                        above: 'rgba(0, 0, 0, 0.08)',
                        below: 'rgba(0, 0, 0, 0.08)'
                    }
                }]
            },
            options: {
                animation: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        type: 'linear',
                        title: {
                            display: true,
                            text: axisMeta.x
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: axisMeta.y
                        }
                    }
                }
            }
        });
    }

    buildPoints(data) {
        var equation = data.equation;
        var beam = data.beam;
        var totalSpan = beam.primarySpan + (beam.secondarySpan || 0);
        var segments = 120;
        var step = totalSpan / segments;
        var points = [];
        var x = 0;

        while (x <= totalSpan) {
            points.push(equation(x));
            x += step;
        }

        if (points.length === 0 || points[points.length - 1].x < totalSpan) {
            points.push(equation(totalSpan));
        }

        if (Array.isArray(equation.discontinuities)) {
            var epsilon = Math.max(totalSpan * 1e-6, 1e-6);

            equation.discontinuities.forEach(function (d) {
                var leftX = Math.max(0, d - epsilon);
                var rightX = Math.min(totalSpan, d + epsilon);

                points.push(equation(leftX));
                points.push(equation(rightX));
            });

            points.sort(function (a, b) {
                return a.x - b.x;
            });
        }

        return points;
    }

    getAxisMeta() {
        if (this.container === 'deflection_plot') {
            return { x: 'Span (m)', y: 'Deflection (mm)' };
        }

        if (this.container === 'shear_force_plot') {
            return { x: 'Span (m)', y: 'Shear Force (kN)' };
        }

        return { x: 'Span (m)', y: 'Bending Moment (kNm)' };
    }
}