(function (global) {
    'use strict';

    class AnalysisPlotter {
        constructor(containerId) {
            this.containerId = containerId;
            this.chart = null;
        }

        plot(data) {
            const canvas = document.getElementById(this.containerId);
            if (!canvas || !data || typeof data.equation !== 'function') return;

            const ctx = canvas.getContext('2d');
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();

            const primarySpan = (data.beam && data.beam.primarySpan) || 0;
            const secondarySpan = (data.beam && data.beam.secondarySpan) || 0;
            const totalLength = primarySpan + (isNaN(secondarySpan) ? 0 : secondarySpan);
            if (totalLength <= 0) return;

            try {
                // Menggunakan helper dari UI Utils yang sudah digabung
                const { points, peakPoint } = UIUtils.generatePlotPoints(data, totalLength);
                const config = this._generateConfig(this.containerId, points, totalLength, peakPoint);
                this.chart = new Chart(ctx, config);
            } catch (error) {
                console.error('Plotting failed:', error);
            }
        }

        _generateConfig(id, plotData, totalLength, peakPoint) {
            const plotInfo = UIUtils.PLOT_CONFIG[id] || {
                label: 'Value',
                color: '#6c757d',
                title: 'Analysis'
            };

            const peakLabelPlugin = {
                id: 'peakLabel',
                afterDatasetsDraw(chart) {
                    const { ctx, scales: { x, y } } = chart;
                    ctx.save();

                    const xPos = x.getPixelForValue(peakPoint.x);
                    const yPos = y.getPixelForValue(peakPoint.y);
                    const valText = `${peakPoint.y.toFixed(2)}`;

                    ctx.font = 'bold 12px Inter, sans-serif';
                    ctx.fillStyle = plotInfo.color;
                    ctx.textAlign = 'center';

                    const offset = peakPoint.y >= 0 ? -15 : 20;
                    const textWidth = ctx.measureText(valText).width;

                    ctx.fillStyle = '#ffffffcc';
                    ctx.fillRect(xPos - (textWidth / 2 + 5), yPos + offset - 12, textWidth + 10, 16);

                    ctx.fillStyle = plotInfo.color;
                    ctx.fillText(valText, xPos, yPos + offset);

                    ctx.beginPath();
                    ctx.arc(xPos, yPos, 4, 0, 2 * Math.PI);
                    ctx.fill();
                    ctx.restore();
                }
            };

            return {
                type: 'line',
                data: {
                    datasets: [{
                        label: plotInfo.label,
                        data: plotData,
                        borderColor: plotInfo.color,
                        backgroundColor: (context) => {
                            const { ctx, chartArea } = context.chart;
                            return UIUtils.createChartGradient(ctx, chartArea, plotInfo.color);
                        },
                        fill: true,
                        tension: 0,
                        pointRadius: (context) => {
                            const xVal = context.raw ? context.raw.x : null;
                            const isMajor = xVal === 0 || xVal === totalLength || (peakPoint && xVal === peakPoint.x);
                            return isMajor ? 3 : 0;
                        },
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: { display: true, text: plotInfo.title, font: { size: 14 } },
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${context.parsed.y.toFixed(2)}`,
                                title: (items) => `Span Point: ${items[0].parsed.x.toFixed(2)} m`
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 0,
                            max: totalLength,
                            title: { display: true, text: UIUtils.COMMON_X_LABEL },
                            grid: { display: false },
                            ticks: {
                                includeBounds: true,
                                callback: (value) => value.toFixed(2)
                            }
                        },
                        y: {
                            title: { display: true, text: plotInfo.label },
                            reverse: false,
                            grace: '15%'
                        }
                    }
                },
                plugins: [peakLabelPlugin]
            };
        }
    }

    global.AnalysisPlotter = AnalysisPlotter;

})(typeof window !== 'undefined' ? window : global);
