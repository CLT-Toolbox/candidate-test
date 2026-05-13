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
        let L;

        if (data.condition === 'two-span-unequal' && data.beam.secondarySpan > 0) {
            L = data.beam.primarySpan + data.beam.secondarySpan;
        } else {
            L = data.beam.primarySpan;
        }
        const equation = data.equation;

        const labels = [];
        const values = [];

        const step = L / 50;

        for (let x = 0; x <= L; x += step) {
            const point = equation(x);
            labels.push(point.x.toFixed(2));
            values.push(point.y);
        }

        const ctx = document.getElementById(this.container);

        if (this.chart) {
            this.chart.destroy();
        }

        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Analysis Result',
                    data: values,
                    borderwidth: 2,
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Position along the beam (m)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Value'
                        }
                    }
                }
            }
        });

        console.log('Plotting data : ', data);
    }
}