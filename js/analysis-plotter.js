'use strict';

/**
 * Plot result from the beam analysis calculation into a graph
 */
class AnalysisPlotter {
    constructor(container) {
        this.container = container;
        this.chartInstance = null;
    }

    /**
     * Plot equation.
     *
     * @param {Object{beam : Beam, load : float, equation: Function}}  data The equation data
     */
    plot(data) {
        let canvas = document.getElementById(this.container);
        if (!canvas) return;

        let ctx = canvas.getContext('2d');
        
        let length = parseFloat(data.beam.primarySpan) + (parseFloat(data.beam.secondarySpan) || 0);
        let chartData = [];
        
        let step = 0.05; // high precision
        for (let x = 0; x <= length + 0.0001; x += step) {
            x = Math.round(x * 1000) / 1000;
            if (x > length) x = length;
            let result = data.equation(x);
            chartData.push({ x: result.x, y: result.y });
            if (x === length) break;
        }

        let title = "Plot";
        let yLabel = "";
        let fillArea = true;
        
        if (this.container === 'deflection_plot') {
            title = 'Deflection';
            yLabel = 'Deflection (mm)';
            fillArea = false;
        } else if (this.container === 'shear_force_plot') {
            title = 'Shear Force';
            yLabel = 'Shear Force (kN)';
        } else if (this.container === 'bending_moment_plot') {
            title = 'Bending Moment';
            yLabel = 'Bending Moment (kNm)';
        }

        let config = {
            type: 'line',
            data: {
                datasets: [{
                    label: title,
                    data: chartData,
                    borderColor: 'red',
                    backgroundColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 2,
                    fill: fillArea,
                    pointRadius: 0,
                    pointHitRadius: 10
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
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
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: title
                    }
                }
            }
        };

        if (this.chartInstance) {
            this.chartInstance.destroy();
        }

        // Handle case where chart is already connected to canvas externally 
        // chartjs stores its instance on canvas elements sometimes, but destroying this.chartInstance handles it mostly
        var existingChart = Chart.getChart(canvas);
        if (existingChart) {
            existingChart.destroy();
        }

        this.chartInstance = new Chart(ctx, config);
    }
}