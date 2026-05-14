'use strict';


window.ChartInstances = window.ChartInstances || {};


class AnalysisPlotter {
    constructor(containerId) {
        this.containerId = containerId;
        this.chart = null;
        this.chartLabels = [];
    }

    /**
     * Plot equation.
     *
     * @param {Object{beam : Beam, load : float, equation: Function}}  The equation data
     */
    plot(data) {
        const beam = data.beam;
        const load = data.load;
        const equation = data.equation;

        const totalSpan = beam.secondarySpan ? beam.primarySpan + beam.secondarySpan : beam.primarySpan;
  
        const step = totalSpan / 100;  
        const points = [];
        const labels = [];

        for (let x = 0; x <= totalSpan; x += step) {
            const point = equation(x);
            points.push(point.y);
            labels.push(x.toFixed(2));
        }

        this.chartLabels = [];
        for (let x = 0; x <= totalSpan; x += step) {
            this.chartLabels.push(parseFloat(x.toFixed(2)));
        }


        const canvas = document.getElementById(this.containerId);
        const ctx = canvas.getContext('2d');

        if (window.ChartInstances[this.containerId]) {
            window.ChartInstances[this.containerId].destroy();
        }
        let title = '';
        let yAxisLabel = '';
        let borderColor = 'rgb(220, 20, 60)';
        let backgroundColor = 'rgba(220, 20, 60, 0.1)';

        if (this.containerId.includes('deflection')) {
            title = 'Deflection Plot';
            yAxisLabel = 'Deflection (mm)';
        } else if (this.containerId.includes('shear')) {
            title = 'Shear Force Plot';
            yAxisLabel = 'Shear Force (kN)';
            borderColor = 'rgb(255, 99, 132)';
            backgroundColor = 'rgba(255, 99, 132, 0.1)';
        } else if (this.containerId.includes('bending')) {
            title = 'Bending Moment Plot';
            yAxisLabel = 'Bending Moment (kNm)';
            borderColor = 'rgb(75, 192, 192)';
            backgroundColor = 'rgba(75, 192, 192, 0.1)';
        }

        // Create Chart.js instance
        this.chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: this.chartLabels,
                datasets: [{
                    label: yAxisLabel,
                    data: points,
                    borderColor: borderColor,
                    backgroundColor: backgroundColor,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: title,
                        font: {
                            size: 16
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        title: {
                            display: true,
                            text: 'Span (m)',
                            font: {
                                size: 12
                            }
                        },
                        min: 0,
                        max: totalSpan
                    },
                    y: {
                        title: {
                            display: true,
                            text: yAxisLabel,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
        window.ChartInstances[this.containerId] = this.chart;

        console.log('Plotted data for', this.containerId, ':', {
            equation: data.equation,
            beam: data.beam,
            load: data.load,
            pointsGenerated: points.length
        });
    }
}