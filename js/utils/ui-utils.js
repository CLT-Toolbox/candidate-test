(function (global) {
    'use strict';

    // Helper visual dan Chart.js
    const UIUtils = {

        // Label sumbu X
        COMMON_X_LABEL: 'Span (m)',

        // Config warna dan label
        PLOT_CONFIG: {
            'deflection_plot': {
                label: 'Deflection (mm)',
                color: '#dc3545',
                title: 'Deflection Diagram'
            },
            'shear_force_plot': {
                label: 'Shear Force (kN)',
                color: '#dc3545',
                title: 'Shear Force Diagram'
            },
            'bending_moment_plot': {
                label: 'Bending Moment (kNm)',
                color: '#28a745',
                title: 'Bending Moment Diagram'
            }
        },

        // Efek gradient
        createChartGradient: (ctx, chartArea, color) => {
            if (!chartArea) return null;
            const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
            gradient.addColorStop(0, color + '00');
            gradient.addColorStop(1, color + '33');
            return gradient;
        },

        // Logic Sampling Titik
        generatePlotPoints: (data, totalLength) => {
            const steps = 200;
            const stepSize = totalLength / steps;
            const plotData = [];
            let pointsToSample = [];

            for (let i = 0; i <= steps; i++) pointsToSample.push(i * stepSize);
            if (data.discontinuities) {
                data.discontinuities.forEach(d => {
                    if (d > 0 && d < totalLength) pointsToSample.push(d);
                });
            }
            pointsToSample = [...new Set(pointsToSample)].sort((a, b) => a - b);

            pointsToSample.forEach(x => {
                const isDisc = data.discontinuities && data.discontinuities.some(d => Math.abs(x - d) < 0.0001);
                if (isDisc) {
                    plotData.push({ x: x, y: data.equation(x, 'left').y });
                    plotData.push({ x: x, y: data.equation(x, 'right').y });
                } else {
                    plotData.push({ x: parseFloat(x.toFixed(4)), y: data.equation(x, 'auto').y });
                }
            });

            let peakPoint = { x: 0, y: 0 };
            let maxAbsY = -1;
            plotData.forEach(p => {
                if (Math.abs(p.y) > maxAbsY) {
                    maxAbsY = Math.abs(p.y);
                    peakPoint = { x: p.x, y: p.y };
                }
            });

            return { points: plotData, peakPoint };
        }
    };

    global.UIUtils = UIUtils;

})(typeof window !== 'undefined' ? window : global);
